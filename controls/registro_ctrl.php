<?php
   //session_start();
   include('database.php');
   setlocale(LC_TIME, "spanish");
	date_default_timezone_set('America/Mexico_City');

   // ***** CONFIGURACIÓN MERCADO PAGO ***** //
   // Desactiva la notificación de errores deprecados en PHP
   error_reporting(~E_DEPRECATED);

   // Carga el autoload de Composer para gestionar dependencias
   require_once 'vendor/autoload.php';

   // Importa las clases necesarias del SDK de MercadoPago
   use MercadoPago\Client\Preference\PreferenceClient;
   use MercadoPago\Client\User\CustomerClient;
   use MercadoPago\MercadoPagoConfig;
   //Obtengo el access token
   $query = "SELECT Pay_Access FROM configuracion;";
   $result = mysqli_query($connection, $query);
   if(!$result) 
      die('Query Failed ' .mysqli_error($connection));
   $row = mysqli_fetch_array($result);
   $access_tkn = $row['Pay_Access'];
   // Agrega credenciales ACCESS_TOKEN
   //"APP_USR-297566681490189-050916-7b154a737259eef90df0d2e202e9bfec-2416355932"
   MercadoPagoConfig::setAccessToken($access_tkn);

   function console_log( $data ){
      echo '<script>';
      echo 'console.log('. json_encode( $data ) .')';
      echo '</script>';
   }

   function envia_mail($para, $cc, $contenido_html) {
      $to = $para;
      $from = 'no-responder@lauraarcangel.com';
      $fromName = 'Laura Arcangel';
      $subject = 'Confirmación de orden de compra';

      // Encabezados
      $headers = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
      $headers .= "From: $fromName <$from>" . "\r\n";
      if (!empty($cc)) {
         $headers .= "Bcc: $cc\r\n";
      }

      // Enviar email
      return mail($to, $subject, $contenido_html, $headers);
   }

   function generatePassword($length) {
      $key = "";
      //$pattern = "1234567890abcdefghijklmnopqrstuvwxyz";
      //$pattern = "1234567890abcdefghijklmñnopqrstuvwxyzABCDEFGHIJKLMNÑOPQRSTUVWXYZ.-_*/=[]{}#@|~¬&()?¿";
      $pattern = "1234567890abcdefghijklmñnopqrstuvwxyzABCDEFGHIJKLMNÑOPQRSTUVWXYZ-_*/=#@&()?¿";
      $max = strlen($pattern)-1;
      for($i = 0; $i < $length; $i++){
         $key .= substr($pattern, mt_rand(0,$max), 1);
      }
      return $key;
   }

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

   if ($accion == 1) {   //1 Llena CmbPais
      $query = "SELECT * FROM paises order by Pais";
      $result = mysqli_query($connection, $query);
      if(!$result) {
         die('Query Failed ' .$query .' ' . mysqli_error($connection));
      }

      $json = array();
      while($row = mysqli_fetch_array($result)) {
         $json[] = array(
            'a' => $row['Pais'],       //utf8_encode(
            'z' => $row['IdPais']
         );
      }
      mysqli_close($connection);
      $jsonstring = json_encode($json);
      echo $jsonstring;
   }

   if ($accion == 2) {   //2 Llena CmbEstado
      $query = "SELECT * FROM estados where IdPais = $datos[1] order by Estado";
      $result = mysqli_query($connection, $query);
      if(!$result) {
         die('Query Failed ' .$query .' ' . mysqli_error($connection));
      }

      $json = array();
      while($row = mysqli_fetch_array($result)) {
         $json[] = array(
            'a' => $row['Estado'],
            'z' => $row['IdEstado']
         );
      }
      $jsonstring = json_encode($json);
      echo $jsonstring;
   }

   if ($accion == 3) {    //3 Valido el correo que no exista
      $query = "SELECT * FROM clientes where Email = '$datos[1]'";
      $result = mysqli_query($connection, $query);
      if(!$result) {
         die('Query Failed ' .$query .' ' . mysqli_error($connection));
      }
      else {
         $n = mysqli_num_rows($result);
         if ($n >= 1)
            echo 1;
      }
   }

   if ($accion == 4) {     //4 Guardo al nuevo cliente y genero órden de compra
      $nombre    = $_POST['nombre'] ?? '';
      $apellidos = $_POST['apellidos'] ?? '';
      $telefono  = $_POST['telefono'] ?? '';
      $email     = $_POST['email'] ?? '';
      $pais      = $_POST['pais_texto'] ?? '';
      $estado    = $_POST['estado_texto'] ?? '';
      $carrito   = isset($_POST['carrito']) ? json_decode($_POST['carrito'], true) : [];
      $pass = generatePassword(8);
      $clvventa = strtoupper(hash("crc32b", date("Y-m-d G:i:s")));
      //$clvcurso = $_SESSION['ClvCurso'];
      $clvcurso = $_POST['clvcurso'] ?? '';
      //Obtengo los datos del curso
      $query = "SELECT Nombre, IdCurso, Clave, Precio, Moneda FROM cursos where Clave = '$clvcurso';";
      $result = mysqli_query($connection, $query);
      if(!$result) 
        die('Query Failed ' .mysqli_error($connection));
      else {
         $row = mysqli_fetch_array($result);
         $nmbcurso = $row['Nombre'];
         $precio = $row['Precio'];
         $idcurso = $row['IdCurso'];
         $moneda = $row['Moneda'];
      }

      // 1. Guardar al CLIENTE en la base de datos
      $sql = "INSERT INTO clientes (Nombre, Apellidos, Telefono, Email, Pais, Estado, Pass) VALUES (?, ?, ?, ?, ?, ?, md5(?))";
      $stmt = mysqli_stmt_init($connection);

      if (!mysqli_stmt_prepare($stmt, $sql)) {
         echo json_encode(['success' => false, 'mensaje' => 'Error Prepare: ' . mysqli_error($connection)]);
         exit;
      }

      mysqli_stmt_bind_param($stmt, "sssssss", $nombre, $apellidos, $telefono, $email, $pais, $estado, $pass);
      mysqli_stmt_execute($stmt);

      if (mysqli_stmt_affected_rows($stmt) < 0) {
         echo json_encode(['success' => false, 'mensaje' => 'Error al guardar cliente']);
         exit;
      }
      $cliente_id = mysqli_stmt_insert_id($stmt);

      // 2. Guardo los datos de la VENTA
      $fch = date("Y-m-d");
      $hr = date("G:i:s");
      $query = "INSERT INTO ventas (IdCliente, IdCurso, FchVenta, HrVenta, Total, Moneda, MetodoPago,
               Pagado, ClvVenta) VALUES ('$cliente_id', $idcurso, '$fch','$hr','$precio', '$moneda'  , -1, 0, '$clvventa')";
      $result = mysqli_query($connection, $query);
      if(!$result) {

         echo json_encode(['success' => false, 'mensaje' => mysqli_error($connection)]);
         exit;
      }


      $txt = '
         <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #e0e0e0; border-radius: 10px; padding: 30px; background-color: #ffffff;">
         <div style="text-align: center; margin-bottom: 20px;">
            <img src="https://lauraarcangel.com/logo.png" alt="Laura Arcangel" style="max-height: 60px;">
         </div>

         <h2 style="color: #4a4a4a; text-align: center;">Gracias por tu compra</h2>

         <p style="font-size: 16px; color: #333;">
            Hola, muchas gracias por adquirir uno de nuestros cursos. Una vez que confirmemos tu pago, tendrás acceso completo al contenido.
         </p>

         <p style="font-size: 16px; color: #333;">
            Para ingresar a la plataforma, visita el siguiente enlace:
         </p>

         <div style="text-align: center; margin: 20px 0;">
            <a href="https://lauraarcangel.com/mis-cursos/" target="_blank" style="background-color: #007bff; color: #fff; text-decoration: none; padding: 12px 20px; border-radius: 6px; display: inline-block;">
               Acceder a Mis Cursos
            </a>
         </div>

         <p style="font-size: 16px; color: #333;">
            <strong>Datos de acceso:</strong><br>
            Usuario: <strong>' . $email . '</strong><br>
            Contraseña: <strong>' . $pass . '</strong>
         </p>

         <p style="font-size: 16px; color: #333;">
            Si tienes algún problema para acceder al curso, no dudes en contactarnos. Estamos aquí para ayudarte.
         </p>

         <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">

         <p style="font-size: 13px; color: #888; text-align: center;">
            Este correo fue enviado automáticamente por <strong>lauraarcangel.com</strong>. Por favor, no respondas a este mensaje.
         </p>
         </div>';
      envia_mail($email,"",$txt);     // envia_mail($email,'ernesto.sivh@gmail.com',$txt);


      // 2. Preparar los items del carrito para Mercado Pago
      $items = [];
      // foreach ($carrito as $clave => $item) {
         $items[] = [
            "id" => $idcurso,
            "title" => $nmbcurso,
            "quantity" => intval(1),
            "currency_id" => $moneda ?? "MXN",
            "unit_price" => floatval($precio)
         ];
      //  }

      // 3. Crear preferencia con SDK de Mercado Pago
      $client = new PreferenceClient();

      $backUrls = [
         "success" => "https://lauraarcangel.com/success.php",
         "failure" => "https://lauraarcangel.com/failure.php",
         "pending" => "https://lauraarcangel.com/pending.php"
      ];

      try {
         $preference = $client->create([
            "items" => $items,
            "payer" => [
            "name" => $nombre,
            "surname" => $apellidos,
            "email" => $email,
            "phone" => [
               "area_code" => "52",
               "number" => $telefono
            ],
            "address" => [
               "zip_code" => "00000",
               "street_name" => $estado,
               "street_number" => "0"
            ]
            ],
            "back_urls" => $backUrls,
            "auto_return" => "approved",
            "binary_mode" => true,
            "external_reference" => "CLV-" . $clvventa,
            "statement_descriptor" => "Cursos Laura Arcangel",
            "payment_methods" => [
            "excluded_payment_methods" => [
               ["id" => "amex"]
            ],
            "excluded_payment_types" => [
               ["id" => "bank_transfer"]
            ],
            "installments" => 12
            ],
            "notification_url" => "https://lauraarcangel.com/mis-cursos/controls/pay_webhook.php"
         ]);
      } catch (Exception $e) {
         echo json_encode(['success' => false, 'mensaje' => 'Error al crear preferencia: ' . $e->getMessage()]);
         exit;
      }

      // 4. Devolver datos al frontend
      echo json_encode([
         'success' => true,
         'cliente_id' => $cliente_id,
         'preference_id' => $preference->id,
         'init_point' => $preference->init_point
      ]);
      exit;
   }





   // if ($accion == 44) {     //4 Guardo al nuevo cliente y genero órden de compra
   //    $nombre = $_POST['nombre'];
   //    $apellido = $_POST['apellido'];
   //    $telefono = $_POST['telefono'];
   //    $email = $_POST['email'];
   //    $pais = $_POST['pais'];
   //    $estado = $_POST['estado'];
   //    $clvcurso = $_SESSION['ClvCurso'];
      
   //    //Obtengo los datos del curso
   //    $query = "SELECT Nombre, IdCurso, Clave, Precio FROM cursos where Clave = '$clvcurso';";
   //    $result = mysqli_query($connection, $query);
   //    if(!$result) 
   //      die('Query Failed ' .mysqli_error($connection));
   //    else {
   //       $row = mysqli_fetch_array($result);
   //       $nmbcurso = $row['Nombre'];
   //       $precio = $row['Precio'];
   //       $_SESSION['idcurso'] = $row['IdCurso'];
   //       $concepto='Curso: ' .$nmbcurso;
   //       $descripcion = $concepto;
   //       $pass = generatePassword(8);
   //       //$pass = strtoupper(hash("crc32b", date("G:i:s")));

   //       //Guardo los valores en SESSION
   //       $_SESSION['pass'] = $pass;
   //       $_SESSION['Nmbcurso'] = $nmbcurso;
   //       $_SESSION['precio'] = $precio;
   //       $_SESSION['concepto'] = $concepto;
                  
   //       //Obtengo la Key de conecta
   //       $query = "SELECT Conekta_key_privada FROM configuracion;";
   //       $result = mysqli_query($connection, $query);
   //       if(!$result) 
   //          die('Query Failed ' .mysqli_error($connection));
   //       else {
   //          $row = mysqli_fetch_array($result);
   //          $key_privada = $row['Conekta_key_privada'];
            
   //          //cargo las librerias del conecta
   //          require_once("lib/Conekta.php");
   //          \Conekta\Conekta::setApiKey("$key_privada");//key privada
   //          \Conekta\Conekta::setApiVersion("2.0.0");
   //          //creo al cliente (customer)
   //          $validCustomer = [
   //             'name' => $nombre .' ' .$apellido,
   //             'email' => $email
   //          ];
   //          $customer = \Conekta\Customer::create($validCustomer);
   //          $customer = $customer->id;
   //          $_SESSION['customer'] = $customer;
            

   //          //Guardo el nuevo cliente
   //          $query = "INSERT INTO clientes (Nombre, Apellidos, Telefono, Email, Pais, Estado, Pass,
   //             Id_Conekta) VALUES ('$nombre', '$apellido','$telefono','$email','$pais','$estado',md5('$pass'),
   //             '$customer');";
   //          $result = mysqli_query($connection, $query);
   //          if(!$result)
   //             die('Query Failed ' . mysqli_error($connection));
   //          $idclt = mysqli_insert_id($connection);
   //          $_SESSION['idclt'] = $idclt;

   //          echo 'OK';

   //       }
   //    }

   // }

   // if ($accion == 5) {     //5 Genero órden de compra
   //    //Obtengo la Key de conecta
   //    $query = "SELECT Conekta_key_privada FROM configuracion;";
   //    $result = mysqli_query($connection, $query);
   //    if(!$result) 
   //       die('Query Failed ' .mysqli_error($connection));
   //    else {
   //       $row = mysqli_fetch_array($result);
   //       $key_privada = $row['Conekta_key_privada'];
         
   //       //cargo las librerias del conecta
   //       require_once("lib/Conekta.php");
   //       \Conekta\Conekta::setApiKey("$key_privada");//key privada
   //       \Conekta\Conekta::setApiVersion("2.0.0");

   //       //Obtengo los valores en SESSION
   //       $precio = $_SESSION['precio'];
   //       $concepto = $_SESSION['concepto'];
   //       $customer = $_SESSION['customer'];
   //       $_SESSION['usr_new'] = 1;
   //       $descripcion = $concepto;
   //       $sku='CURSO';

   //       //Se crea la orden de pago
   //       $validOrderWithCheckout = array(
   //          'line_items'=> array(
   //          array(
   //             'name'=> $concepto,
   //             'description'=> $descripcion,
   //             'unit_price'=> $precio *100,
   //             'quantity'=> 1,
   //             'sku'=> $sku
   //          )
   //          ),
   //          'checkout' => array(
   //          'allowed_payment_methods' => array("cash", "card"),
   //          'type' => 'HostedPayment',
   //          'success_url' => 'http://127.0.0.1/laura_arcangel/mis-cursos/cnkt_confirmation.php',
   //          'failure_url' => 'http://127.0.0.1/laura_arcangel/mis-cursos/cnkt_failure.php',
   //          'monthly_installments_enabled' => false,
   //          'monthly_installments_options' => array(3, 6, 9, 12)
   //          ),
   //          'customer_info' => array(
   //          'customer_id'   =>  $customer
   //          ),
   //          'currency'    => 'mxn'
   //       );
   //       $order = \Conekta\Order::create($validOrderWithCheckout);
   //       header("Location: {$order->checkout->url}");
   //    }
   // } 



   
