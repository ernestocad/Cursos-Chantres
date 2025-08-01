<?php
   //Para evitar cargar la cache
   header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
   header("Expires: Sat, 1 Jul 2000 05:00:00 GMT"); // Fecha en el pasado

   //session_start();
   setlocale(LC_TIME, "spanish");
   date_default_timezone_set('America/Mexico_City');
   //Obtener el Id del usuario por session
   // $idusuario=$_SESSION['idusuario'];   
   include('database.php');
   
   if(isset($_POST['datos'])) {
      $datos = $_POST['datos'];
      $datos = explode(",", $datos);
      $accion = $datos[0];
   }

   if ($accion == 1) {   //1 Carga los datos del curso en el form
      $id = $_POST['id'];
      $query = "SELECT * from cursos WHERE Clave = '$id'";
      $result = mysqli_query($connection, $query);
      if(!$result) 
         die('Query Failed ' .$query .' ' . mysqli_error($connection));
      else {
         $json = array();
         $row = mysqli_fetch_array($result);
         //obtengo los temas del curso
         $idcurso = $row['IdCurso'];
         $query = "SELECT * from cursos_det WHERE IdCurso = '$idcurso'";
         $result = mysqli_query($connection, $query);
         $temas = "";
         while($row2 = mysqli_fetch_array($result)) 
            $temas = $temas ."<li><b>" .mb_strtoupper($row2['Nombre']) ."</b></li>";

         //$ruta = $row['Imagen'];
         $json[] = array(
            'a' => $row['Nombre'],
            'b' => $row['Imagen'],
            'c' => str_replace("\n", "<br/>", $row['Descripcion']),
            'd' => $row['Precio'],
            'e' => $row['DemoPDF'],
            'f' => $row['DemoAudio'],
            'g' => $row['RutaDemoPDF'],
            'h' => $row['RutaDemoAudio'],
            'i' => $row['Clave'],
            'j' => $row['Activo'],
            'k' => $row['PrecioOld'],
            'l' => $row['Moneda'],
            'z' => $temas
         );
         // if( file_exists("../../assets/images/mbr-1920x1285.jpg") )
         //    unlink("../../assets/images/mbr-1920x1285.jpg");
         // copy($ruta, '../../assets/images/mbr-1920x1285.jpg');
         $jsonstring = json_encode($json[0]);
         echo $jsonstring;
      }
   }






   if ($accion == 2222) {   //2 Obtengo el Id del curso e inicio el registro
      $clv = $_POST['clvcurso'];
      $query = "SELECT * FROM cursos where Clave = '$clv';";
      $result = mysqli_query($connection, $query);
      if(!$result) {
         die('Query Failed ' .$query .' ' . mysqli_error($connection));
      }
      else {
        $row = mysqli_fetch_array($result);
        $_SESSION['IdCurso'] = $row['IdCurso'];
        $_SESSION['ClvCurso'] = $row['Clave'];
      }
      echo 'OK';
    }