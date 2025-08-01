<?php
  include('database.php');

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $pass = $_POST['pass'] ?? '';

    $email = mysqli_real_escape_string($connection, $email);
    $pass = mysqli_real_escape_string($connection, $pass);

    $query = "SELECT * FROM clientes WHERE Email = '$email' AND Pass = md5('$pass')";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_num_rows($result) === 1) {
      $row = mysqli_fetch_assoc($result);
      $idcliente = $row['IdCliente'];
      $nombre = $row['Nombre'];

      $token = bin2hex(random_bytes(32));
      $expiraEn = date('Y-m-d H:i:s', strtotime('+30 minutes'));

      mysqli_query($connection, "INSERT INTO sesiones (id_cliente, token, expira_en) VALUES ('$idcliente', '$token', '$expiraEn')");

      echo json_encode([
        "ok" => true,
        "token" => $token,
        "nombre" => $nombre,
        "idcliente" => $idcliente
      ]);
    } else {
      echo json_encode(["ok" => false, "msg" => "Correo o contraseña incorrectos."]);
    }
  }
