<?php
// 1. Configuración inicial
$paypal_url = "https://ipnpb.sandbox.paypal.com/cgi-bin/webscr"; // Para pruebas sandbox
$receiver_email = "sb-4pkkh34488120@business.example.com"; // El correo de tu cuenta PayPal

// 2. Recibir datos de PayPal
$raw_post_data = file_get_contents('php://input'); // Lee los datos crudos enviados por PayPal
$myPost = array();
foreach (explode('&', $raw_post_data) as $keyval) {
    $keyval = explode('=', $keyval);
    if (count($keyval) == 2) {
        $myPost[$keyval[0]] = urldecode($keyval[1]);
    }
}

// 3. Construir la consulta para PayPal
$req = 'cmd=_notify-validate';
foreach ($myPost as $key => $value) {
    $req .= "&$key=" . urlencode($value);
}

// 4. Enviar los datos de vuelta a PayPal para validarlos
$ch = curl_init($paypal_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
$res = curl_exec($ch);
curl_close($ch);

// 5. Verificar que la respuesta de PayPal sea "VERIFIED"
if (strcmp($res, "VERIFIED") == 0) {
    // 6. Validación de pago
    $payment_status = $_POST['payment_status'];
    $payment_amount = $_POST['mc_gross'];
    $payment_currency = $_POST['mc_currency'];
    $transaction_id = $_POST['txn_id'];
    $payer_email = $_POST['payer_email'];
    $user_id = $_POST['custom'];  // El custom que podemos usar para almacenar el user_id (opcional)
    $order_id = $_POST['invoice']; // El invoice que podemos usar para vincular el pago con el pedido

    // 7. Verificar que el pago sea exitoso
    if ($payment_status == 'Completed' && $payment_currency == 'MXN') {
        // 8. Insertar el pago en la base de datos
        include 'components/connect.php'; // Asegúrate de incluir la conexión a la base de datos

        $stmt = $conn->prepare("INSERT INTO payments (user_id, order_id, payment_status, payment_amount, currency, transaction_id, payer_email, payment_method, order_details) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $order_id, $payment_status, $payment_amount, $payment_currency, $transaction_id, $payer_email, 'PayPal', 'Detalles del pedido']);

        // Aquí también puedes actualizar el estado del pedido en la tabla `orders`
        $update_order = $conn->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
        $update_order->execute(['Completed', $order_id]);

        // 9. Notificar a la tienda que el pago se ha procesado
        echo "Pago exitoso y registrado en la base de datos.";
    } else {
        echo "El pago no es válido o no está completo.";
    }
}
?>
