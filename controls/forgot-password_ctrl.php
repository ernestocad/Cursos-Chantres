<?php
   header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 1 Jul 2000 05:00:00 GMT"); // Fecha en el pasado

	session_start();
	setlocale(LC_TIME, "spanish");
	date_default_timezone_set('America/Mexico_City');
   include('database.php');

   function console_log( $data ){
      echo '<script>';
      echo 'console.log('. json_encode( $data ) .')';
      echo '</script>';
   }

   function envia_mail($para,$to2,$textomail) {
      //Recipiente
      $to = $para;
      //remitente del correo
      $from = 'no-respoder@laura.com';
      $fromName = 'Laura Arcangel';
      //Asunto del email
      $subject = 'Código de seguridad'; 
      //Contenido del Email
      $htmlContent = $textomail;
      //Encabezado para información del remitente
      $headers = "De: $fromName"." <".$from.">" ."\r\n" . "CO: " .$to2;
      //Limite Email
      $semi_rand = md5(time()); 
      $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
      //Encabezados para archivo adjunto 
      $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
      //límite multiparte
      $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
      "Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n"; 
      $message .= "--{$mime_boundary}--"; 
      $returnpath = "-f" . $from; 
      //Enviar EMail
      $mail = @mail($to, $subject, $message, $headers, $returnpath);
   }

   if(isset($_POST['datos'])) {
      $datos = $_POST['datos'];
      $datos = explode(",", $datos);
      $accion = $datos[0];
   }

   if ($accion == 1) {      //1 Valida el correo y envía el codigo
      $email = $_POST['email'];
      $sql = "SELECT IdCliente FROM clientes where Email = ?;";
      $stmt = mysqli_stmt_init($connection);
      if (mysqli_stmt_prepare($stmt,$sql)) {
         mysqli_stmt_bind_param($stmt, "s", $email);
         if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            $n = mysqli_stmt_num_rows($stmt);
            if ($n >= 1) {
               mysqli_stmt_bind_result($stmt, $id);
               mysqli_stmt_fetch($stmt);
               $idcliente = $id;
               $_SESSION['IdCtex'] = $id;
               $fecha = date("Y-m-d H:i:s");
               $codigo = strtoupper(hash("crc32b", $fecha));
               $sql = "INSERT INTO reset_pass (IdCliente, Fecha, Codigo, Usado) VALUES 
                  ('$idcliente', '$fecha', '$codigo', 0);";
               $result = mysqli_query($connection, $sql);
               if(!$result)
                  die('Query Failed ' .$sql .' ' . mysqli_error($connection));
               else {
                  $txt='<h5>Tu código de seguridad es.</h5>
                  <p><br><br></p><p><b>' .$codigo .'</b>';
                  envia_mail($email,'noresponder@laura.com',$txt);
                  echo "OK";
               }
            }
            else 
               echo "FALSE";
         }
      }
   }

   if ($accion == 2) {      //2 Valida el código
      $codigo = $_POST['codigo'];
      $sql = "SELECT * FROM reset_pass where Codigo = ? and IdCliente = ? and Usado = 0;";
      $stmt = mysqli_stmt_init($connection);
      if (mysqli_stmt_prepare($stmt,$sql)) {
         mysqli_stmt_bind_param($stmt, "si", $codigo, $_SESSION['IdCtex']);
         if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            $n = mysqli_stmt_num_rows($stmt);
            if ($n >= 1) 
               echo 'OK';
            else 
               echo "FALSE";
         }
      }
   }

   if ($accion == 3) {      //3 Actualiza la contraseña
      $pass = md5($_POST['pass']);
      $id = $_SESSION['IdCtex'];
      $sql = "UPDATE clientes SET Pass = ? WHERE IdCliente = ?;";
      $stmt = mysqli_stmt_init($connection);
      if (mysqli_stmt_prepare($stmt,$sql)) {
         mysqli_stmt_bind_param($stmt, "si", $pass, $id);
         mysqli_stmt_execute($stmt);
         $contador=mysqli_stmt_affected_rows($stmt);
         if ($contador >= 0) {   //-1 Es un error
            $sql = "UPDATE reset_pass SET Usado = 1 WHERE IdCliente = ?;";
            $stmt = mysqli_stmt_init($connection);
            if (mysqli_stmt_prepare($stmt,$sql)) {
               mysqli_stmt_bind_param($stmt, "i", $id);
               mysqli_stmt_execute($stmt);
            }
            session_destroy();
            session_unset();
            echo "OK";
         }
         else {
            throw new Exception('Error Update. '.$sql .' ' . mysqli_stmt_error($stmt));
            echo "FALSE";
         }
      }
      else
         throw new Exception('Error Prepare. '.$sql .' ' . mysqli_connect_error($stmt));
   }


   
