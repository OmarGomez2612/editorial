<?php
   include '../components/connect.php';
   session_start();
   $admin_id = $_SESSION['admin_id'];

   if (!isset($admin_id)) {
      header('location:admin_login.php');
   };
?>

<!DOCTYPE html>
<html lang="es">
   <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>PRODUCTO</title>

      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
      <link rel="stylesheet" href="../css/admin_style.css">
   </head>

   <body>
      <?php
         if (isset($_SESSION['message'])) {
            echo '<div class="message">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
         }
      ?>

      <?php include '../components/admin_header.php'; ?>

      <section class="show-products">

         <h1 class="heading">LIBROS AGREGADOS</h1> <br>
         <a href="dashboard.php" class="btn">VOLVER</a> <br>
         <div class="box-container">            
         <?php
            // Ejecutamos la consulta con JOIN para obtener la categoría
            $select_products = $conn->prepare("
               SELECT p.*, c.categorias 
               FROM products p
               LEFT JOIN categories c ON p.category_id = c.id
               WHERE p.type_id = 1
            ");
            $select_products->execute();

            // Verificamos si hay productos
            if($select_products->rowCount() > 0){
               // Si hay productos, mostramos cada uno
               while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
         ?>
            <div class="box">
               <img src="../libros/<?= htmlspecialchars($fetch_products['book_folder'] . '/' . $fetch_products['image_01']); ?>" alt="Imagen del libro">
               <div class="name"><?= htmlspecialchars($fetch_products['name']); ?></div>
               <div class="author">Autor: <?= htmlspecialchars($fetch_products['author']); ?></div>
               <div class="publisher">Editorial: <?= htmlspecialchars($fetch_products['publisher']); ?></div>
               <div class="category">Categoría: <?= htmlspecialchars($fetch_products['categorias']); ?></div>
               <div class="price">$<span><?= htmlspecialchars($fetch_products['price']); ?></span> MXN</div>
               <div class="details"><span><?= htmlspecialchars($fetch_products['details']); ?></span></div>

               <?php if (is_dir('../libros/' . htmlspecialchars($fetch_products['book_folder']))) { ?>
                  <div class="book-link">
                     <a href="../libros/<?= htmlspecialchars($fetch_products['book_folder']); ?>/" target="_blank">VER ARCHIVOS DEL LIBRO</a>
                  </div>
               <?php } ?>
               <div class="flex-btn">
                  <!--<a href="update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">ACTUALIZAR</a>-->
                  <a href="delete_product.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('¿Eliminar este producto?');">ELIMINAR</a>
               </div>
            </div>
               <?php
               }
            } else {
               echo '<p class="empty">¡No hay libros registrados!</p>';
            }
         ?>
         </div>
      </section>
      <script src="../js/admin_script.js"></script>
   </body>
</html>