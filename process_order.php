include 'components/connect.php';
session_start();

// Verificar si el usuario está autenticado
if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
} else {
   header('location:user_login.php');
   exit();
}

// Verificar si los detalles del pedido existen en la sesión
if(isset($_POST['payment_id']) && isset($_POST['payer_id']) && isset($_POST['order_details'])){
   // Recibir los detalles del pago y el pedido
   $payment_id = htmlspecialchars($_POST['payment_id']);
   $payer_id = htmlspecialchars($_POST['payer_id']);
   $order_details = json_decode($_POST['order_details'], true); // Decodificar los detalles del pedido

   // Verificar si los detalles del pedido están completos
   if($order_details && isset($order_details['user_name'], $order_details['user_lastname'], $order_details['total_products'], $order_details['total_price'])){
      $name = htmlspecialchars($order_details['user_name']);
      $lastname = htmlspecialchars($order_details['user_lastname']);
      $total_products = htmlspecialchars($order_details['total_products']);
      $total_price = htmlspecialchars($order_details['total_price']);

      // Insertar los detalles del pedido en la base de datos
      $insert_order = $conn->prepare("INSERT INTO orders(user_id, name, lastname, total_products, total_price, payment_id, payer_id) VALUES(?,?,?,?,?,?,?)");
      $insert_order->execute([$user_id, $name, $lastname, $total_products, $total_price, $payment_id, $payer_id]);

      // Obtener el ID del pedido insertado
      $order_id = $conn->lastInsertId();

      // Insertar los detalles del pago en la base de datos
      $insert_payment = $conn->prepare("INSERT INTO payments(user_id, order_id, payment_status, payment_amount, transaction_id, payer_id, payer_email) VALUES(?,?,?,?,?,?,?)");
      $insert_payment->execute([$user_id, $order_id, 'Completed', $total_price, $payment_id, $payer_id, 'user@example.com']); // Asegúrate de obtener el correo electrónico real del usuario

      // Vaciar el carrito de compras después del pago
      $delete_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
      $delete_cart->execute([$user_id]);

      // Mensaje de éxito
      $message[] = '¡Tu pedido ha sido realizado con éxito!';
   } else {
      $message[] = 'Detalles del pedido no válidos.';
   }
} else {
   $message[] = 'No se recibieron los detalles del pago.';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Confirmación de Pedido</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>
<h3>Confirmación de Pedido</h3>
<section class="checkout-orders">
   
   <div class="display-orders">
      <?php
         if(isset($message)){
            foreach($message as $msg){
               echo '<p class="message">'.$msg.'</p>';
            }
         }
      ?>
      <p>Gracias por tu compra. El pago ha sido procesado exitosamente.</p>
      <p>Detalles del pedido:</p>
      <div class="order-details">
         <p><strong>Nombre:</strong> <?= htmlspecialchars($name); ?></p>
         <p><strong>Apellido:</strong> <?= htmlspecialchars($lastname); ?></p>
         <p><strong>Total de Productos:</strong> <?= htmlspecialchars($total_products); ?></p>
         <p><strong>Total a Pagar:</strong> $<?= htmlspecialchars($total_price); ?> MXN</p>
      </div>
      <a href="order_success.php" class="btn">Ver mi Pedido</a>
   </div>
</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
