<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:user_login.php');
}

if(isset($_POST['delete'])){
   $cart_id = $_POST['cart_id'];
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
   $delete_cart_item->execute([$cart_id]);
}

if(isset($_GET['delete_all'])){
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
   $delete_cart_item->execute([$user_id]);
   header('location:cart.php');
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>CARRITO DE COMPRAS</title>

   <!-- Fuente de Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Archivo CSS personalizado -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="products shopping-cart">

   <h3 class="heading">TU CARRITO DE COMPRAS</h3>

   <div class="box-container">

   <?php
      $grand_total = 0;
      $select_cart = $conn->prepare("SELECT cart.*, products.book_folder, products.image_01, products.type_id FROM `cart` JOIN `products` ON cart.pid = products.id WHERE cart.user_id = ?");
      $select_cart->execute([$user_id]);
      if($select_cart->rowCount() > 0){
         while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
      <a href="quick_view.php?pid=<?= $fetch_cart['pid']; ?>" class="fas fa-eye" title="Ver detalles del libro"></a>
      
      <?php
      // Verificamos si el tipo de producto es un libro o un audiolibro
      if ($fetch_cart['type_id'] == 1) {
         // Si es un libro
         echo '<img src="libros/' . htmlspecialchars($fetch_cart['book_folder'] . '/' . $fetch_cart['image_01']) . '" alt="Imagen del libro">';
      } elseif ($fetch_cart['type_id'] == 2) {
         // Si es un audiolibro
         echo '<img src="audiolibros/' . htmlspecialchars($fetch_cart['book_folder'] . '/' . $fetch_cart['image_01']) . '" alt="Imagen del audiolibro">';
      }
      ?>
      <div class="name"><?= $fetch_cart['name']; ?></div>
      <div class="flex">
         <div class="price">$<?= $fetch_cart['price']; ?> MXN</div>
      </div>
      <input type="submit" value="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este artículo del carrito?');" class="delete-btn" name="delete">
   </form>
   <?php
   $grand_total += $fetch_cart['price']; // Solo sumamos el precio de cada libro
      }
   }else{
      echo '<p class="empty">¡TU CARRITO DE COMPRAS ESTÁ VACÍO!</p>';
   }
   ?>
   </div>

   <div class="cart-total">
      <p>Total: <span>$<?= $grand_total; ?> MXN</span></p>
      <a href="shop.php" class="option-btn">SEGUIR COMPRANDO</a>
      <a href="cart.php?delete_all" class="delete-btn <?= ($grand_total > 1)?'':'disabled'; ?>" onclick="return confirm('¿Estás seguro de eliminar todos los artículos del carrito?');">ELIMINAR TODO</a>
      <a href="checkout.php" class="btn <?= ($grand_total > 1)?'':'disabled'; ?>">PAGAR</a>
   </div>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
