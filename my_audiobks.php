<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>MIS PEDIDOS</title>
   
   <!-- Enlace de Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Enlace al archivo CSS personalizado -->
   <link rel="stylesheet" href="css/admin_style.css">
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   <?php include 'components/user_header.php'; ?>

   <section class="show-products">

   <h1 class="heading">MIS LIBROS</h1>

   <div class="box-container">
   <?php
      // Consulta para obtener los productos comprados por el usuario
      $select_products = $conn->prepare("
         SELECT p.*, c.categorias 
         FROM products p 
         JOIN payment_items pi ON p.id = pi.product_id
         JOIN payments pay ON pi.payment_id = pay.payment_id
         LEFT JOIN categories c ON p.category_id = c.id
         WHERE pay.user_id = ? AND p.type_id = 2

      ");
      $select_products->execute([$user_id]);

      // Verificamos si hay productos comprados
      if($select_products->rowCount() > 0){
         // Si hay productos, mostramos cada uno
         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
      <div class="box">
         <?php
         // Verificamos si el tipo de producto es un libro o un audiolibro
         if ($fetch_products['type_id'] == 1) {
            // Si es un libro
            echo '<img src="libros/' . htmlspecialchars($fetch_products['book_folder'] . '/' . $fetch_products['image_01']) . '" alt="Imagen del libro">';
         } elseif ($fetch_products['type_id'] == 2) {
            // Si es un audiolibro
            echo '<img src="audiolibros/' . htmlspecialchars($fetch_products['book_folder'] . '/' . $fetch_products['image_01']) . '" alt="Imagen del audiolibro">';
         }
         ?>
         <div class="name"><?= htmlspecialchars($fetch_products['name']); ?></div>
         <div class="author">Autor: <?= htmlspecialchars($fetch_products['author']); ?></div>
         <div class="publisher">Editorial: <?= htmlspecialchars($fetch_products['publisher']); ?></div>
         <div class="category">Categoría: <?= htmlspecialchars($fetch_products['categorias']); ?></div>
         <div class="details"><span><?= htmlspecialchars($fetch_products['details']); ?></span></div>

         <?php if (is_dir('../libros/' . htmlspecialchars($fetch_products['book_folder']))) { ?>
            <div class="book-link">
               <a href="../libros/<?= htmlspecialchars($fetch_products['book_folder']); ?>/" target="_blank">VER ARCHIVOS DEL LIBRO</a>
            </div>
         <?php } ?>

         <div class="flex-btn">
            <a href="listen_book.php?id=<?= $fetch_products['id']; ?>" class="option-btn">ESCUCHAR AHORA</a>

         </div>

      </div>
   <?php
         }
      } else {
         echo '<p class="empty"><a href="user_login.php" style="text-decoration: none; color: inherit;">¡INICIA SESIÓN PARA CONOCER TUS AUDIOLIBROS!</a></p>';
      }
   ?>
   </div>

   </section>

   <?php include 'components/footer.php'; ?>
   <script src="js/script.js"></script>
</body>
</html>
