<?php
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 1 Jul 2000 05:00:00 GMT"); // Fecha en el pasado

	setlocale(LC_TIME, "spanish");
	date_default_timezone_set('America/Mexico_City');
	
	include('database.php');
	// Carga el autoload de Composer para gestionar dependencias
	require __DIR__ . '/vendor/autoload.php'; // Ajusta la ruta según tu estructura

	use MercadoPago\MercadoPagoConfig;
	use MercadoPago\Client\Payment\PaymentClient;

	//Obtengo el access token
	$query = "SELECT Pay_Access FROM configuracion;";
	$result = mysqli_query($connection, $query);
	if(!$result) 
		die('Query Failed ' .mysqli_error($connection));
	$row = mysqli_fetch_array($result);
	$access_tkn = $row['Pay_Access'];
	MercadoPagoConfig::setAccessToken($access_tkn); // tu token real


	//obtengo las compras pendientes de pago
	$query = "SELECT * FROM ventas where Pagado = 0";
	$result = mysqli_query($connection, $query);
	if(!$result)
		die('Query Failed ' .$query .' ' . mysqli_error($connection));
	
	echo "Número de compras pendientes: " .mysqli_num_rows($result);
	$fch = date("Y-m-d");
	$hr = date("G:i:s");
	while($row = mysqli_fetch_array($result)) {
		echo "<br><br>REVISANDO...<br>";
		$orden = $row['IdVenta_MP'];
		
		$paymentClient = new PaymentClient();
		$payment = $paymentClient->get($orden);
		
		// Datos útiles
		$status = $payment->status; // Estado del pago: approved, pending, etc.
		$external_ref = $payment->external_reference; // Tu clave de venta, ejemplo: CLV-AEBB3598
		$external_ref = substr($external_ref, 4,8);
		$amount = $payment->transaction_amount; // Monto pagado
		$fecha_iso = $payment->date_created; // 2025-07-21T21:22:37.000-04:00
		$fecha_obj = new DateTime($fecha_iso); // crea objeto DateTime
		$fecha = $fecha_obj->format('Y-m-d'); // 2025-07-21
		$hora = $fecha_obj->format('H:i:s'); // 21:22:37

		$payment_method = $payment->payment_method_id; // Método de pago: 'visa', 'oxxo', 'master', etc.
		$payment_type = $payment->payment_type_id; // Tipo: 'credit_card', 'debit_card', 'ticket', etc.
		echo '<br>Estatus del pago ' . $status .' con método de pago ' .$payment_method;

		if ($status == 'approved') {
			$pagado = 1;
			$sql = "UPDATE ventas SET MetodoPago = ?, payment_type_id = ?, Pagado = ?, TtRecibido = ?, FchPago = ?, HrPago = ?, " 
					." WHERE ClvVenta = ?;";
			$stmt = mysqli_stmt_init($connection);
			if (mysqli_stmt_prepare($stmt,$sql)) {
					mysqli_stmt_bind_param($stmt, "ssidsss", $payment_method, $payment_type, $pagado, $amount, $fecha, $hora, $external_ref);
					mysqli_stmt_execute($stmt);
					$contador=mysqli_stmt_affected_rows($stmt);
			}
			else
					throw new Exception('Error Prepare. '.$sql .' ' . mysqli_connect_error($stmt));
		}
		
			
	}
	mysqli_close($connection);