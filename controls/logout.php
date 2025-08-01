<?php
   include('database.php');
   include('auth_helper.php');

   $headers = getallheaders();
   $token = $headers['Authorization'] ?? '';

   eliminar_token($connection, $token);

   echo json_encode(["ok" => true, "msg" => "Sesión cerrada"]);
