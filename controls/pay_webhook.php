<?php
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

    if (!isset($_GET["id"]) || !isset($_GET["topic"])) {
        http_response_code(400);
        exit("Faltan parámetros");
    }

    if ($_GET["topic"] === "payment") {
        $payment_id = $_GET["id"];

        try {
            $paymentClient = new PaymentClient();
            $payment = $paymentClient->get($payment_id);

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

             if ($status == 'approved')
                $pagado = 1;
            else    
                $pagado = 0;

            $sql = "UPDATE ventas SET MetodoPago = ?, payment_type_id = ?, Pagado = ?, TtRecibido = ?, FchPago = ?, HrPago = ?, " 
                ."IdVenta_MP = ? WHERE ClvVenta = ?;";
            $stmt = mysqli_stmt_init($connection);
            if (mysqli_stmt_prepare($stmt,$sql)) {
                mysqli_stmt_bind_param($stmt, "ssidssss", $payment_method, $payment_type, $pagado, $amount, $fecha, $hora, $payment_id, $external_ref);
                mysqli_stmt_execute($stmt);
                $contador=mysqli_stmt_affected_rows($stmt);
                if ($contador >= 0)   //-1 Es un error
                    echo "OK";
                else
                    throw new Exception('Error Update. '.$sql .' ' . mysqli_stmt_error($stmt));
            }
            else
                throw new Exception('Error Prepare. '.$sql .' ' . mysqli_connect_error($stmt));




            // Guardar toda la respuesta de Mercado Pago en un archivo log
            // $logData = [
            //     'fecha' => date('Y-m-d H:i:s'),
            //     'payment_id' => $payment_id,
            //     'status' => $status,
            //     'external_reference' => $external_ref,
            //     'amount' => $amount,
            //     'date_created' => $fecha_iso,
            //     'raw_data' => $payment // objeto completo
            // ];

            // // Convertir a texto legible
            // $logText = print_r($logData, true);

            // // Guardar en archivo
            // file_put_contents(__DIR__ . '/mp_logs.txt', $logText . "\n-----------------------------\n", FILE_APPEND);

        } catch (Exception $e) {
            error_log("Error al obtener el pago: " . $e->getMessage());
            http_response_code(500);
            exit;
        }

        http_response_code(200);
        echo "OK";
    }
?>