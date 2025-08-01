<?php
   //Para evitar cargar la cache
   header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
   header("Expires: Sat, 1 Jul 2000 05:00:00 GMT"); // Fecha en el pasado

   session_start();
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
    $clv = $_POST['clave'];
    $query = "SELECT cursos.Nombre as NmbCurso, Imagen, Descripcion, cursos_det.Nombre as NmbContenido, 
      Ruta, IdCurso_Det FROM cursos inner join cursos_det on cursos.IdCurso = cursos_det.IdCurso 
      where Clave = '$clv' order by cursos_det.Nombre asc;";
    $result = mysqli_query($connection, $query);
    if(!$result) {
       die('Query Failed ' .$query .' ' . mysqli_error($connection));
    }
    else {
      $json = array();
      while($row = mysqli_fetch_array($result)) {
          $json[] = array(
          'a' => $row['NmbCurso'],
          'b' => $row['Imagen'],
          'c' => $row['Descripcion'],
          'd' => $row['NmbContenido'],
          'e' => $row['Ruta'],
          'z' => $row['IdCurso_Det']
        );
      }
      $jsonstring = json_encode($json);
      echo $jsonstring;
    }
  }

  if ($accion == 2) {   //2 Descarga el archivo del curso
    $id = $_POST['id'];
    $query = "SELECT * FROM cursos_det where IdCurso_Det = $id;";
    $result = mysqli_query($connection, $query);
    if(!$result) {
       die('Query Failed ' .$query .' ' . mysqli_error($connection));
    }
    else {
      $row = mysqli_fetch_array($result);
      $archivo = $row['Nombre'] .',' .$row['Ruta'];    
      echo $archivo;
    }
  }
