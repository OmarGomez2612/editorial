<?php

include 'components/connect.php';
session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
};

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>MIS PEDIDOS</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .message-wrapper {
         width: 100%;
         display: flex;
         justify-content: center;
         align-items: center;
         padding: 40px 0;
      }
      .message-wrapper .empty {
         max-width: 600px;
         text-align: center;
         margin: 0 auto;
      }
   </style>
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="orders">
   <h1 class="heading">PEDIDOS REALIZADOS</h1>

   <div class="box-container">

   <?php
      if($user_id == '') {
         echo '<div class="message-wrapper"><p class="empty"><a href="user_login.php" style="text-decoration: none; color: inherit;">INICIA SESIÓN PARA VER TUS PEDIDOS</a></p></div>';
      } else {
         $select_orders = $conn->prepare("SELECT p.*, u.name AS user_name FROM `payments` p 
                                          LEFT JOIN `users` u ON p.user_id = u.id 
                                          WHERE p.user_id = ?");
         $select_orders->execute([$user_id]);

         if($select_orders->rowCount() > 0){
            while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p>Realizado el : <span><?= $fetch_orders['payment_date']; ?></span></p>
      <p>Nombre : <span><?= htmlspecialchars($fetch_orders['user_name']); ?></span></p>
      <p>Correo Electrónico : <span><?= $fetch_orders['payer_email']; ?></span></p>
      <p>Método de Pago : <span><?= $fetch_orders['payment_method']; ?></span></p>
      <p>Total a Pagar : <span>$<?= $fetch_orders['payment_amount']; ?> MXN</span></p>
      <p>Estado del Pago : <span style="color:<?php if($fetch_orders['payment_status'] == 'pending'){ echo 'red'; }else{ echo 'green'; }; ?>"><?= $fetch_orders['payment_status']; ?></span></p>
      
      <?php
         if ($fetch_orders['payment_status'] == 'COMPLETED') {
            $order_id = $fetch_orders['payment_id'];
            $select_items = $conn->prepare("SELECT * FROM `order_items` WHERE order_id = ?");
            $select_items->execute([$order_id]);

            if($select_items->rowCount() > 0){
               while($fetch_item = $select_items->fetch(PDO::FETCH_ASSOC)){
      ?>
      <div class="box">
         <img src="../uploaded_img/<?= htmlspecialchars($fetch_item['image']); ?>" alt="Imagen del libro">
         <div class="name"><?= htmlspecialchars($fetch_item['product_name']); ?></div>
         <div class="price">$<span><?= htmlspecialchars($fetch_item['price']); ?></span> MXN</div>
         <div class="flex-btn">
            <a href="read_book.php?book_id=<?= $fetch_item['product_id']; ?>" class="option-btn">LEER</a>
         </div>
      </div>
      <?php
               }
            } else {
               echo '<div class="message-wrapper"><p class="empty">No has comprado libros en este pedido.</p></div>';
            }
         }
      ?>
   </div>
   <?php
         }
      } else {
         echo '<div class="message-wrapper"><p class="empty"><a href="shop.php" style="text-decoration: none; color: inherit;">¡CONOCE NUESTROS LIBROS Y REALIZA TU PEDIDO!</a></p></div>';
      }
   }
   ?>
   </div>
</section>

<?php include 'components/footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>
