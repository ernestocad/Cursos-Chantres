<?php
   session_start();
   include('database.php');

   function console_log( $data ){
      echo '<script>';
      echo 'console.log('. json_encode( $data ) .')';
      echo '</script>';
   }

   if(isset($_POST['datos'])) {
      $datos = $_POST['datos'];
      $datos = explode(",", $datos);
      $accion = $datos[0];
   }

   if ($accion == 1)       //1 Valida los datos de sesión
   {
      $pass = md5($_POST['pass']);
      $id = $_SESSION['idcliente'];
      $sql = "UPDATE clientes SET Pass = ? WHERE IdCliente = ?;";
      $stmt = mysqli_stmt_init($connection);
      if (mysqli_stmt_prepare($stmt,$sql)) {
         mysqli_stmt_bind_param($stmt, "si", $pass, $id);
         mysqli_stmt_execute($stmt);
         $contador=mysqli_stmt_affected_rows($stmt);
         if ($contador >= 0)   //-1 Es un error
            echo "OK";
         else {
            throw new Exception('Error Update. '.$sql .' ' . mysqli_stmt_error($stmt));
            echo "FALSE";
         }
      }
      else
         throw new Exception('Error Prepare. '.$sql .' ' . mysqli_connect_error($stmt));
   }



   
