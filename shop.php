<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
}

include 'components/wishlist_cart.php';

// Si se presiona el botón de añadir al carrito
if(isset($_POST['add_to_cart'])){
    $product_id = $_POST['pid']; // ID del producto
    $product_name = $_POST['name']; // Nombre del producto
    $product_price = $_POST['price']; // Precio del producto
    $product_image = $_POST['image']; // Imagen del producto
    
    // Verificar si el usuario ya compró este libro
    $check_purchase = $conn->prepare("SELECT * FROM `payment_items` JOIN `payments` ON payment_items.payment_id = payments.payment_id WHERE payments.user_id = ? AND payment_items.product_id = ?");
    $check_purchase->execute([$user_id, $product_id]);
    
    // Si el usuario ya compró el producto, no permitir agregarlo al carrito
    if($check_purchase->rowCount() > 0) {
        echo '<script>alert("Ya has comprado este libro. ¡Ve a leerlo!")</script>';
    } else {
        // Verificar si el producto ya está en el carrito
        $check_cart = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND pid = ?");
        $check_cart->execute([$user_id, $product_id]);
        
        // Si el producto ya está en el carrito, actualizamos la cantidad
        if($check_cart->rowCount() > 0){
            $update_quantity = $conn->prepare("UPDATE cart SET quantity = 1 WHERE user_id = ? AND pid = ?");
            $update_quantity->execute([$user_id, $product_id]);
        } else {
            // Si no está en el carrito, lo agregamos con cantidad 1
            $insert_cart = $conn->prepare("INSERT INTO cart (user_id, pid, product_name, product_price, product_image, quantity) VALUES (?, ?, ?, ?, ?, 1)");
            $insert_cart->execute([$user_id, $product_id, $product_name, $product_price, $product_image]);
        }
    }
}

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
   <h1 class="heading">LIBROS EN TIENDA</h1>
   <div class="box-container">
   <?php
     $select_products = $conn->prepare("SELECT * FROM `products` WHERE book_folder IS NOT NULL AND book_folder != '' AND type_id = 1"); 
     $select_products->execute();
     if($select_products->rowCount() > 0){
      while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
         // Verificar si el usuario ya ha comprado este libro
         $check_purchase = $conn->prepare("SELECT * FROM `payment_items` JOIN `payments` ON payment_items.payment_id = payments.payment_id WHERE payments.user_id = ? AND payment_items.product_id = ?");
         $check_purchase->execute([$user_id, $fetch_product['id']]);
         $already_purchased = $check_purchase->rowCount() > 0; // Verificar si el usuario compró este libro
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
      <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
      <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
      <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">

      <!-- Botón de lista de deseos deshabilitado si ya ha comprado el libro -->
      <?php if(!$already_purchased): ?>
      <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
      <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
      <?php endif; ?>
      
      
      <?php
      // Verificamos si el tipo de producto es un libro o un audiolibro
      if ($fetch_product['type_id'] == 1) {
         // Si es un libro
         echo '<img src="libros/' . htmlspecialchars($fetch_product['book_folder'] . '/' . $fetch_product['image_01']) . '" alt="Imagen del libro">';
      } elseif ($fetch_product['type_id'] == 2) {
         // Si es un audiolibro
         echo '<img src="audiolibros/' . htmlspecialchars($fetch_product['book_folder'] . '/' . $fetch_product['image_01']) . '" alt="Imagen del audiolibro">';
      }
      ?>
      <div class="name"><?= $fetch_product['name']; ?></div>
      <div class="flex">
         <div class="price"><span>$</span><?= $fetch_product['price']; ?><span> MXN</span></div>
      </div>
      
      
      <?php if($already_purchased): ?>
         <!-- Si ya compró el libro, cambia el texto y redirige -->
         <a href="read_book.php?id=<?= $fetch_product['id']; ?>" class="btn">LEER AHORA</a>
      <?php else: ?>
         <!-- Si no lo ha comprado, permite agregarlo al carrito -->
         <input type="submit" value="AÑADIR AL CARRITO" class="btn" name="add_to_cart">
      <?php endif; ?>
   </form>
   <?php
      }
   } else {
      echo '<p class="empty">¡NO HAY LIBROS DISPONIBLES!</p>';
   }
   ?>

   </div>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
