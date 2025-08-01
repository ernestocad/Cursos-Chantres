<?php
  include('database.php');
  include('auth_helper.php');

  //$headers = getallheaders();
  $headers = apache_request_headers();
  //$token = $headers['Authorization'] ?? '';
  $token = $_POST['token'] ?? '';

  $usuario = obtener_usuario_por_token($connection, $token);

  if ($usuario) {
    renovar_expiracion($connection, $token);  // Renueva automáticamente
    echo json_encode(["ok" => true, "usuario" => $usuario]);
  } else {
    echo json_encode(["ok" => false, "msg" => "Token inválido o expirado"]);
  }
