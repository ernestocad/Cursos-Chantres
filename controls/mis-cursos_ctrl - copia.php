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
    $idcliente = $_SESSION['idcliente']; //Cambiara segun la sesion del cliente
    $query = "SELECT Nombre, Imagen, CONCAT(SUBSTRING(Descripcion,1,175),'...') as Descripcion, 
      Clave FROM cursos inner join compras on cursos.IdCurso = compras.IdCurso 
      where IdCliente = $idcliente and Pagado = 1 order by FchCompra desc;";
    $result = mysqli_query($connection, $query);
    if(!$result) {
        die('Query Failed ' .$query .' ' . mysqli_error($connection));
    }
    else {
      $json = array();
      while($row = mysqli_fetch_array($result)) {
          $json[] = array(
          'a' => $row['Nombre'],
          'b' => $row['Imagen'],
          'c' => $row['Descripcion'],
          'z' => $row['Clave']
        );
      }
      $jsonstring = json_encode($json);
      echo $jsonstring;
    }
  }




   