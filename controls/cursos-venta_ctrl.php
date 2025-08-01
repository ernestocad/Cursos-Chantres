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

  if(isset($_GET['datos'])) {
    $datos = $_GET['datos'];
    $datos = explode(",", $datos);
    $accion = $datos[0];
  }

  if ($accion == 1) {   //1 Carga los cursos en venta
    $query = "SELECT Nombre, Imagen, CONCAT(SUBSTRING(Descripcion,1,175),'...') as Descripcion, 
      Clave, Precio, PrecioOld, Moneda FROM cursos where Activo = 1 order by Nombre asc;";
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
          'd' => $row['Precio'],
          'e' => $row['PrecioOld'],
          'f' => $row['Moneda'],
          'z' => $row['Clave']
        );
      }
      $jsonstring = json_encode($json);
      echo $jsonstring;
    }
  }

  if ($accion == 2) {   //2 Genera la orden de venta del curso
    $clvcurso = $datos[1];
    echo $clvcurso;
    $idcliente = $_SESSION['idcliente']; //Cambiara segun la sesion del cliente
    $query = "SELECT * FROM clientes where IdCliente = $idcliente;";
    $result = mysqli_query($connection, $query);
    if(!$result) {
      die('Query Failed ' .mysqli_error($connection));
    }
    else {
      $row = mysqli_fetch_array($result);
      $customer = $row['Id_Conekta'];
      //Obtengo los datos del curso
      $query = "SELECT Nombre, IdCurso, Clave, Precio, Moneda FROM cursos where Clave = '$clvcurso';";
      $result = mysqli_query($connection, $query);
      if(!$result) {
        die('Query Failed ' .mysqli_error($connection));
      }
      else {
        $row = mysqli_fetch_array($result);
        $nomcurso = $row['Nombre'];
        $idcurso = $row['IdCurso'];
        $_SESSION['idcurso'] = $idcurso;
        $precio = $row['Precio'];
        $moneda = $row['Moneda'];
        //Obtengo la Key de conecta
        $query = "SELECT Conekta_key_privada FROM configuracion;";
        $result = mysqli_query($connection, $query);
        if(!$result) {
          die('Query Failed ' .mysqli_error($connection));
        }
        else {
          $row = mysqli_fetch_array($result);
          $key_privada = $row['Conekta_key_privada'];
          $concepto='Curso: ' .$nomcurso;
          $sku='CURSO';
          $descripcion = $concepto;
          //cargo las librerias del conecta
          require_once("lib/Conekta.php");
          \Conekta\Conekta::setApiKey("$key_privada");//key privada
          \Conekta\Conekta::setApiVersion("2.0.0");
          //Se crea la orden de pago
          $validOrderWithCheckout = array(
            'line_items'=> array(
              array(
                  'name'=> $concepto,
                  'description'=> $descripcion,
                  'unit_price'=> $precio *100,
                  'quantity'=> 1,
                  'sku'=> $sku
              )
            ),
            'checkout' => array(
              'allowed_payment_methods' => array("cash", "card"),
              'type' => 'HostedPayment',
              'success_url' => 'http://127.0.0.1/laura_arcangel/mis-cursos/cnkt_confirmation.php',
              'failure_url' => 'http://127.0.0.1/laura_arcangel/mis-cursos/cnkt_failure.php',
              'monthly_installments_enabled' => false,
              'monthly_installments_options' => array(3, 6, 9, 12)
            ),
            'customer_info' => array(
              'customer_id'   =>  $customer
            ),
            'currency'    => $moneda
          );
          $order = \Conekta\Order::create($validOrderWithCheckout);
          header("Location: {$order->checkout->url}");

        }
      }

    }


    
  }

  if ($accion == 3) {   //3 Genera la orden de venta del curso
    $clvcurso = $datos[1];
    echo $clvcurso;
    $idcliente = $_SESSION['idcliente']; //Cambiara segun la sesion del cliente
    $query = "SELECT * FROM clientes where IdCliente = $idcliente;";
    $result = mysqli_query($connection, $query);
    if(!$result) {
      die('Query Failed ' .mysqli_error($connection));
    }
    else {
      $row = mysqli_fetch_array($result);
      $customer = $row['Id_Conekta'];
      //Obtengo los datos del curso
      $query = "SELECT Nombre, IdCurso, Clave, Precio, Moneda FROM cursos where Clave = '$clvcurso';";
      $result = mysqli_query($connection, $query);
      if(!$result) {
        die('Query Failed ' .mysqli_error($connection));
      }
      else {
        $row = mysqli_fetch_array($result);
        $nomcurso = $row['Nombre'];
        $idcurso = $row['IdCurso'];
        $_SESSION['idcurso'] = $idcurso;
        $precio = $row['Precio'];
        $moneda = $row['Moneda'];
        //Obtengo la Key de conecta
        $query = "SELECT Conekta_key_privada FROM configuracion;";
        $result = mysqli_query($connection, $query);
        if(!$result) {
          die('Query Failed ' .mysqli_error($connection));
        }
        else {
          $row = mysqli_fetch_array($result);
          $key_privada = $row['Conekta_key_privada'];
          $concepto='Curso: ' .$nomcurso;
          $sku='CURSO';
          $descripcion = $concepto;
          //cargo las librerias del conecta
          // require_once("lib/Conekta.php");
          // \Conekta\Conekta::setApiKey("$key_privada");//key privada
          // \Conekta\Conekta::setApiVersion("2.0.0");
          // //Se crea la orden de pago
          // $validOrderWithCheckout = array(
          //   'line_items'=> array(
          //     array(
          //         'name'=> $concepto,
          //         'description'=> $descripcion,
          //         'unit_price'=> $precio *100,
          //         'quantity'=> 1,
          //         'sku'=> $sku
          //     )
          //   ),
          //   'checkout' => array(
          //     'allowed_payment_methods' => array("cash", "card"),
          //     'type' => 'HostedPayment',
          //     'success_url' => 'http://127.0.0.1/laura_arcangel/mis-cursos/cnkt_confirmation.php',
          //     'failure_url' => 'http://127.0.0.1/laura_arcangel/mis-cursos/cnkt_failure.php',
          //     'monthly_installments_enabled' => false,
          //     'monthly_installments_options' => array(3, 6, 9, 12)
          //   ),
          //   'customer_info' => array(
          //     'customer_id'   =>  $customer
          //   ),
          //   'currency'    => $moneda
          // );
          // $order = \Conekta\Order::create($validOrderWithCheckout);
          // header("Location: {$order->checkout->url}");

        }
      }

    }


    
  }




   