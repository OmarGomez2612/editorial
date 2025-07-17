<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
}

include 'components/wishlist_cart.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>TIENDA</title>
   
   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Estilos personalizados -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>


<section class="products">

   <h1 class="heading">AUDIOLIBROS EN TIENDA</h1>

   <div class="box-container">

   <?php
     // Modificar la consulta para obtener solo los productos con archivo de audio
     $select_products = $conn->prepare("SELECT `id`, `name`, `details`, `price`, `image_01`, `image_02`, `audio`, `book_folder`, `category_id` FROM `products` WHERE `audio` IS NOT NULL AND `audio` != ''"); 
     $select_products->execute();
     
     if($select_products->rowCount() > 0){
      while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
         // Verificar si el usuario ya ha comprado este audiolibro
         $check_purchase = $conn->prepare("SELECT * FROM `payment_items` JOIN `payments` ON payment_items.payment_id = payments.payment_id WHERE payments.user_id = ? AND payment_items.product_id = ?");
         $check_purchase->execute([$user_id, $fetch_product['id']]);
         $already_purchased = $check_purchase->rowCount() > 0; // Verificar si el usuario compró este audiolibro

         // Verificar si el producto pertenece a la categoría "Gratis" (category_id = 11)
         $is_free_category = $fetch_product['category_id'] == 11;
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
      <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
      <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
      <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
      <input type="hidden" name="audio" value="<?= $fetch_product['audio']; ?>">

      <!-- Mostrar imagen y nombre del audiolibro -->
      <img src="audiolibros/<?= $fetch_product['book_folder'] . '/' . $fetch_product['image_01']; ?>" alt="Imagen del Audiolibro">
      <div class="name"><?= $fetch_product['name']; ?></div>
      <div class="flex">
         <?php if (!$is_free_category): ?>
            <div class="price"><span>$</span><?= $fetch_product['price']; ?><span> MXN</span></div>
         <?php else: ?>
            <div class="price">
               <span style="color: green; font-weight: bold;">GRATIS</span>
            </div>
         <?php endif; ?>
      </div>      

      <!-- Si el producto pertenece a la categoría "Gratis" -->
      <?php if ($is_free_category): ?>
         <!-- Botón de escuchar ahora redirige a listen_book.php -->
         <div class="flex-btn">
            <a href="listen_book.php?id=<?= $fetch_product['id']; ?>" class="btn">ESCUCHAR AHORA</a>
         </div>
      <?php else: ?>
         <!-- Si el producto tiene precio, mostrar las opciones de carrito y lista de deseos -->
         <?php if ($fetch_product['price'] > 0): ?>
            <!-- Mostrar botones solo si no ha comprado el producto -->
            <?php if(!$already_purchased): ?>
               <div class="flex-btn">
                  <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
                  <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>            
               </div>
            <?php endif; ?>
            <!-- Mostrar botón de añadir al carrito si el producto tiene precio y no ha sido comprado -->
            <?php if(!$already_purchased): ?>
               <div class="flex-btn">
                  <input type="submit" value="AÑADIR AL CARRITO" class="btn" name="add_to_cart">
               </div>
            <?php endif; ?>
         <?php endif; ?>
      <?php endif; ?>

      <!-- Si ya ha comprado el audiolibro, cambiar el texto y redirigir -->
      <?php if($already_purchased): ?>
         <a href="listen_book.php?id=<?= $fetch_product['id']; ?>" class="btn">ESCUCHAR AHORA</a>
      <?php endif; ?>

   </form>
   <?php
      }
   }else{
      echo '<p class="empty">¡NO HAY AUDIOLIBROS DISPONIBLES!</p>';
   }
   ?>

   </div>

</section>


<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
