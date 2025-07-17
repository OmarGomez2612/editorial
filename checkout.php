<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
   header('location:user_login.php');
   exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'process_order') {
   $payment_id = $_POST['payment_id'];
   $payer_id = $_POST['payer_id'];
   $order_details = json_decode($_POST['order_details'], true);

   $payment_status = 'Completed'; 
   $payment_amount = $order_details['purchase_units'][0]['amount']['value'];
   $currency = $order_details['purchase_units'][0]['amount']['currency_code'];
   $transaction_id = $order_details['id'];
   $payer_email = $order_details['payer']['email_address'];
   $payment_method = 'PayPal';
   
   $order_id = NULL; // Opcional

   try {
      $conn->beginTransaction(); // Iniciar transacción

      // Insertar pago en la tabla payments
      $insert_payment = $conn->prepare("INSERT INTO payments (user_id, payment_status, payment_amount, currency, transaction_id, payer_id, payer_email, payment_method, order_details, order_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $insert_payment->execute([$user_id, $payment_status, $payment_amount, $currency, $transaction_id, $payer_id, $payer_email, $payment_method, json_encode($order_details), $order_id]);

      $payment_id_inserted = $conn->lastInsertId(); // Obtener el último ID insertado en payments

      // Insertar productos en la tabla payment_items
      $select_cart = $conn->prepare("SELECT cart.pid, cart.quantity, (products.price * cart.quantity) AS total_price, products.name 
                                      FROM cart 
                                      JOIN products ON cart.pid = products.id 
                                      WHERE cart.user_id = ?");
      $select_cart->execute([$user_id]);

      while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
         $insert_payment_items = $conn->prepare("INSERT INTO payment_items (payment_id, product_id, product_name, price) VALUES (?, ?, ?, ?)");
         $insert_payment_items->execute([$payment_id_inserted, $fetch_cart['pid'], $fetch_cart['name'], $fetch_cart['total_price']]);
      }

      // Eliminar productos del carrito
      $delete_cart_items = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
      $delete_cart_items->execute([$user_id]);

      $conn->commit(); // Confirmar transacción

      echo json_encode(['success' => true]);
   } catch (PDOException $e) {
      $conn->rollBack(); // Revertir transacción
      error_log("Error en el proceso de pago: " . $e->getMessage());
      echo json_encode(['success' => false, 'message' => 'Hubo un error con el proceso de pago: ' . $e->getMessage()]);
   }
   exit;
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Realiza tu Pedido</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <section class="checkout-orders">
      <form action="" method="POST">
         <h3>Detalles de tu Pedido</h3>
         <div class="display-orders">
            <?php
            $grand_total = 0;
            $cart_items = [];
            $select_cart = $conn->prepare("SELECT c.id, c.quantity, p.name AS product_name, p.price, p.details, p.book_folder, p.image_01, u.name AS user_name, u.apellido_paterno
                              FROM cart c 
                              JOIN products p ON c.pid = p.id
                              JOIN users u ON c.user_id = u.id 
                              WHERE c.user_id = ?");
            $select_cart->execute([$user_id]);

            if ($select_cart->rowCount() > 0) {
               while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                  $cart_items[] = $fetch_cart['product_name'] . $fetch_cart['price'] . ' x ' . $fetch_cart['quantity'];
                  $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
            ?>    
                  <p>Producto: <?= $fetch_cart['product_name']; ?> <span><?= '$' . $fetch_cart['price'].'.00 MXN'; ?></span></p>
                  <p>Detalles: <?= $fetch_cart['details']; ?></p>
                  <hr>                
            <?php
               }
            } else {
               echo '<p class="empty">Tu carrito está vacío.</p>';
            }
            ?>
            <input type="hidden" name="total_products" value="<?= implode(" ", $cart_items); ?>">
            <input type="hidden" name="total_price" value="<?= $grand_total; ?>">
            <p>Total: <span>$<?= $grand_total.'.00 '; ?> MXN</span></p>
         </div>


         <h3>Paga TU Pedido</h3>

         <!--PAY PAL-->
         <div class="paypal-button-container">
            <div id="paypal-button-container"></div>
         </div>

         <script src="https://www.paypal.com/sdk/js?client-id=AUqjDIgdwHGHm7jiU43nvxYcT3Y8hy-hbWiiII9b88TEByg_D_MCwVyThm5Jy48vZ9GwBFVWULhRKxsI&currency=MXN"></script>
         <script>
            console.log('Cargando PayPal SDK...');

            paypal.Buttons({
               createOrder: function (data, actions) {
                  console.log('Creando pedido en PayPal...');
                  return actions.order.create({
                     purchase_units: [{
                        amount: {
                           value: '<?= $grand_total; ?>', // Total del pedido
                        }
                     }]
                  }).then(function (orderID) {
                     console.log('Pedido creado: ' + orderID);
                     return orderID;
                  });
               },
               onApprove: function (data, actions) {
                  console.log('Aprobación recibida de PayPal...');
                  return actions.order.capture().then(function (details) {
                     console.log('Pedido capturado: ', details);

                     // Enviar los detalles al servidor
                     fetch('checkout.php', {
                        method: 'POST',
                        body: new URLSearchParams({
                           'action': 'process_order',
                           'payment_id': details.id,
                           'payer_id': details.payer.payer_id,
                           'order_details': JSON.stringify(details)
                        })
                     })
                     .then(response => response.json())
                     .then(data => {
                        if (data.success) {
                           alert('Pago realizado con éxito. ¡Tu pedido está confirmado!');
                           window.location.href = 'order_success.php'; // Redirigir a la página de éxito
                        } else {
                           alert('Hubo un error con el proceso de pago: ' + data.message);
                        }
                     })
                     .catch(error => {
                        console.error('Error:', error);
                        alert('Hubo un error con el proceso de pago');
                     });
                  });
               },
               onError: function (err) {
                  console.log('Error con PayPal: ', err);
                  alert('Hubo un error con el proceso de pago');
               }
            }).render('#paypal-button-container');
         </script>

      </form>
   </section>

   <?php include 'components/footer.php'; ?>

   <script src="js/script.js"></script>

</body>

</html>