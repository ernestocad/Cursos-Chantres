<?php

  $db_server = 'localhost';
  $db_usr = 'root';
  $db_pass = 'Team-BD2010';
  $db_esquema = 'cursos';
  try {
    $connection = mysqli_connect($db_server, $db_usr, $db_pass, $db_esquema);
    if (mysqli_connect_errno()) {
      throw new Exception('Error en conexión. '.mysqli_connect_error());
      exit();
    }
  }
  catch (Exception $ex) {
    echo 'Excepción capturada en CONEXIÓN: '.$ex->getMessage();
  }

  

?>
