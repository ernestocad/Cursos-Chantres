<?php

  function obtener_usuario_por_token($conexion, $token) {
    $ahora = date('Y-m-d H:i:s');
    $token = mysqli_real_escape_string($conexion, $token);

    $sql = "SELECT clientes.IdCliente, clientes.Nombre, sesiones.id AS id_sesion
            FROM sesiones
            JOIN clientes ON sesiones.id_cliente = clientes.IdCliente
            WHERE sesiones.token = '$token'
            AND sesiones.expira_en > '$ahora'
            LIMIT 1";

    $res = mysqli_query($conexion, $sql);
    return ($res && mysqli_num_rows($res) === 1) ? mysqli_fetch_assoc($res) : false;
  }

  function renovar_expiracion($conexion, $token, $minutos = 30) {
    $nuevaExpiracion = date('Y-m-d H:i:s', strtotime("+$minutos minutes"));
    $token = mysqli_real_escape_string($conexion, $token);

    $update = "UPDATE sesiones SET expira_en = '$nuevaExpiracion' WHERE token = '$token'";
    mysqli_query($conexion, $update);
  }

  function eliminar_token($conexion, $token) {
    $token = mysqli_real_escape_string($conexion, $token);
    mysqli_query($conexion, "DELETE FROM sesiones WHERE token = '$token'");
  }