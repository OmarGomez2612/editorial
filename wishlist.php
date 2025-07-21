<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:user_login.php');
}

include 'components/wishlist_cart.php';

if(isset($_POST['delete'])){
   $wishlist_id = $_POST['wishlist_id'];
   $delete_wishlist_item = $conn->prepare("DELETE FROM `wishlist` WHERE id = ?");
   $delete_wishlist_item->execute([$wishlist_id]);
}

if(isset($_GET['delete_all'])){
   $delete_wishlist_item = $conn->prepare("DELETE FROM `wishlist` WHERE user_id = ?");
   $delete_wishlist_item->execute([$user_id]);
   header('location:wishlist.php');
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>LISTA DE DESEOS</title>
   
   <!-- Enlace CDN de Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Enlace a archivo CSS personalizado -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="products">

   <h3 class="heading">LISTA DE DESEOS</h3>

   <div class="box-container">

   <?php
      $grand_total = 0;
      $select_wishlist = $conn->prepare("SELECT wishlist.*, products.book_folder, products.image_01, products.type_id FROM `wishlist` JOIN `products` ON wishlist.pid = products.id WHERE wishlist.user_id = ?");
      $select_wishlist->execute([$user_id]);
      if($select_wishlist->rowCount() > 0){
         while($fetch_wishlist = $select_wishlist->fetch(PDO::FETCH_ASSOC)){
            $grand_total += $fetch_wishlist['price'];  
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="pid" value="<?= $fetch_wishlist['pid']; ?>">
      <input type="hidden" name="wishlist_id" value="<?= $fetch_wishlist['id']; ?>">
      <input type="hidden" name="name" value="<?= $fetch_wishlist['name']; ?>">
      <input type="hidden" name="price" value="<?= $fetch_wishlist['price']; ?>">
      <input type="hidden" name="image" value="<?= $fetch_wishlist['image']; ?>">
      <a href="quick_view.php?pid=<?= $fetch_wishlist['pid']; ?>" class="fas fa-eye"></a>
      <?php
      // Verificamos si el tipo de producto es un libro o un audiolibro
      if ($fetch_wishlist['type_id'] == 1) {
         // Si es un libro
         echo '<img src="libros/' . htmlspecialchars($fetch_wishlist['book_folder'] . '/' . $fetch_wishlist['image_01']) . '" alt="Imagen del libro">';
      } elseif ($fetch_wishlist['type_id'] == 2) {
         // Si es un audiolibro
         echo '<img src="audiolibros/' . htmlspecialchars($fetch_wishlist['book_folder'] . '/' . $fetch_wishlist['image_01']) . '" alt="Imagen del audiolibro">';
      }
      ?>
      <div class="name"><?= strtoupper($fetch_wishlist['name']); ?></div>
      <div class="flex">
         <div class="price">$<?= $fetch_wishlist['price']; ?> MXN</div>
      </div>
      <input type="submit" value="AÑADIR A TU CARRITO" class="btn" name="add_to_cart">
      <input type="submit" value="ELIMINAR" onclick="return confirm('¿Eliminar este producto de la lista de deseos?');" class="delete-btn" name="delete">
   </form>
   <?php
         }
      }else{
         echo '<p class="empty"><a href="shop.php" style="text-decoration: none; color: inherit;">TU LISTA DE DESEOS ESTÁ VACIA, CONOCE NUESTRA VARIEDA DE LIBROS</a></p>';
      }
   ?>
   </div>

   <div class="wishlist-total">
      <p>MONTO TOTAL: <span>$<?= $grand_total; ?> MXN</span></p>
      <a href="shop.php" class="option-btn">CONTINUAR COMPRANDO</a>
      <a href="wishlist.php?delete_all" class="delete-btn <?= ($grand_total > 1)?'':'disabled'; ?>" onclick="return confirm('¿Eliminar todos los productos de la lista de deseos?');">ELIMINAR LISTA DE DESEOS</a>
   </div>

</section>


<script src="js/script.js"></script>

</body>
</html>

