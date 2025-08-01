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
      $email = $_POST['email'];
      $pass = $_POST['pass'];

      $query = "SELECT * FROM clientes where Email = '$email' and Pass = md5('$pass') ";
      $result = mysqli_query($connection, $query);
      if(!$result)
         die('Query Failed ' .$query .' ' . mysqli_error($connection));
      else {
         $n = mysqli_num_rows($result);
         $row = mysqli_fetch_array($result);
         if ($n >= 1) {
            $_SESSION['idcliente'] = $row['IdCliente'];
            $_SESSION['Nombre'] = $row['Nombre'];
            echo "OK";
         }
         else 
            echo "FALSE";
      }
   }



   
