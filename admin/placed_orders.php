<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

if(isset($_POST['update_payment'])){
   $order_id = $_POST['order_id'];
   $payment_status = $_POST['payment_status'];
   $payment_status = filter_var($payment_status, FILTER_SANITIZE_STRING);
   $update_payment = $conn->prepare("UPDATE `payments` SET payment_status = ? WHERE id = ?");
   $update_payment->execute([$payment_status, $order_id]);
   $message[] = '¡PEDIDO ACTUALIZADO!';
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_order = $conn->prepare("DELETE FROM `payments` WHERE id = ?");
   $delete_order->execute([$delete_id]);
   header('location:placed_orders.php');
}

?>

<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>PEDIDOS</title>

      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

      <link rel="stylesheet" href="../css/admin_style.css">

   </head>
   <body>

   <?php include '../components/admin_header.php'; ?>

   <section class="orders">

   <h1 class="heading">PEDIDOS</h1>

   <div class="box-container">
   <?php
   $select_payments = $conn->prepare("SELECT payments.*, 
   CONCAT(users.name, ' ', users.apellido_paterno, ' ', users.apellido_materno) AS full_name, 
   payment_items.product_name, 
   payment_items.price, 
   payment_items.payment_id
FROM `payments`
JOIN `users` ON payments.user_id = users.id
LEFT JOIN `payment_items` ON payments.payment_id = payment_items.payment_id
ORDER BY payments.payment_id");
$select_payments->execute();




   if($select_payments->rowCount() > 0){
      while($fetch_payments = $select_payments->fetch(PDO::FETCH_ASSOC)){
?>
      <div class="box">
         <p> Fecha: <span><?= $fetch_payments['payment_date']; ?></span> </p>
         <p> Nombre: <span><?= $fetch_payments['full_name']; ?></span> </p> <!-- Nombre del usuario -->
         <p> Estatus: <span><?= $fetch_payments['payment_status']; ?></span> </p>
         <p> Precio total: <span>$<?= $fetch_payments['payment_amount']; ?> <?= $fetch_payments['currency']; ?></span> </p>
         <p> Método de pago: <span><?= $fetch_payments['payment_method']; ?></span> </p>

         <p>Detalles de los productos: <span><ul>
            <?php
               // Si existen productos asociados al pago
               if (!empty($fetch_payments['product_name'])) {
                  echo "<li>" . $fetch_payments['product_name'] . " - $ " . $fetch_payments['price'] . "</li>";
               }
            ?>
         </ul>
         </span></p> 
      </div>
<?php
      }
   }else{
      echo '<p class="empty">NO SE A REALIZADO EL PEDIDO!</p>';
   }
?>

   </div>

   </section>
   <script src="../js/admin_script.js"></script>
      
   </body>
</html>