<?php
// Incluir archivo de conexión a la base de datos
include 'components/connect.php';

// Iniciar sesión
session_start();

// Verificar si el usuario está logueado
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

// Incluir archivo para el manejo de la lista de deseos y el carrito
include 'components/wishlist_cart.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>PÁGINA DE BÚSQUEDA</title>

   <!-- Enlace a Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Enlace al archivo CSS personalizado -->
   <link rel="stylesheet" href="css/style.css">
</head>

<body>

   <!-- Incluir el encabezado del usuario -->
   <?php include 'components/user_header.php'; ?>

   <!-- Sección de formulario de búsqueda -->
   <section class="search-form">
      <form action="" method="post">
         <input type="text" name="search_box" placeholder="Buscar aquí..." maxlength="100" class="box" required>
         <button type="submit" class="fas fa-search" name="search_btn"></button>
      </form>
   </section>

   <!-- Sección de resultados de productos -->
   <section class="products" style="padding-top: 0; min-height: 100vh;">

      <div class="box-container">
         <?php
         // Verificar si se ha realizado una búsqueda
         if (isset($_POST['search_box']) || isset($_POST['search_btn'])) {
            // Sanitizar la entrada de búsqueda
            $search_box = filter_var($_POST['search_box'], FILTER_SANITIZE_STRING);

            // Consultar los productos que coinciden con la búsqueda
            $select_products = $conn->prepare("SELECT * FROM `products` WHERE name LIKE ?");
            $select_products->execute(["%{$search_box}%"]);

            // Mostrar los productos si se encontraron
            if ($select_products->rowCount() > 0) {
               while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) {
                  // Verificar si el usuario ha comprado este producto
                  $check_payment = $conn->prepare("SELECT * FROM `payments` WHERE `user_id` = ?");
                  $check_payment->execute([$user_id]);

                  // Inicializar la variable que nos dirá si el producto ha sido comprado
                  $has_purchased = false;

                  // Verificar si el usuario tiene pagos registrados
                  if ($check_payment->rowCount() > 0) {
                     // Consultar en la tabla payment_items si el producto ha sido comprado por el usuario
                     $check_payment_item = $conn->prepare("SELECT * FROM `payment_items` WHERE `product_id` = ? AND `payment_id` IN (SELECT payment_id FROM `payments` WHERE `user_id` = ?)");
                     $check_payment_item->execute([$fetch_product['id'], $user_id]);

                     // Si el producto ha sido comprado por el usuario, marcamos la variable
                     if ($check_payment_item->rowCount() > 0) {
                        $has_purchased = true;
                     }
                  }

                  // Si el precio es 0 (gratis), se muestran los productos con la lógica especial
                  if ($fetch_product['price'] == 0) {
            ?>
                     <form action="" method="post" class="box">
                        <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
                        <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
                        <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
                        <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">

                        

                        <?php
                        // Mostrar la imagen dependiendo del tipo de producto
                        if ($fetch_product['type_id'] == 1) {
                           // Si es un libro
                           echo '<img src="libros/' . htmlspecialchars($fetch_product['book_folder'] . '/' . $fetch_product['image_01']) . '" alt="Imagen del libro">';
                           echo '<div class="type">Libro</div>';  // Leyenda de libro
                        } elseif ($fetch_product['type_id'] == 2) {
                           // Si es un audiolibro
                           echo '<img src="audiolibros/' . htmlspecialchars($fetch_product['book_folder'] . '/' . $fetch_product['image_01']) . '" alt="Imagen del audiolibro">';
                           echo '<div class="type">Audiolibro</div>';  // Leyenda de audiolibro
                        }
                        ?>

                        <div class="name"><?= htmlspecialchars($fetch_product['name']); ?></div>
                        <div class="flex">
                           <!-- Cambiar precio a 'GRATIS' -->
                           <div class="price">
                              <span style="color: green; font-weight: bold;">GRATIS</span>
                           </div>
                        </div>
                        <!-- Eliminar los botones de Wishlist y Quick View -->
                        <div class="no-buttons">
                           <!-- Botón de "Escuchar ahora" -->
                           <a href="listen_book.php?id=<?= $fetch_product['id']; ?>" class="btn">ESCUCHAR AHORA</a>
                        </div>
                     </form>
            <?php
                  } else {
                     // Mostrar el producto solo si no ha sido comprado por el usuario
                     if (!$has_purchased) {
            ?>
                        <form action="" method="post" class="box">
                           <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
                           <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
                           <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
                           <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">

                           <!-- Botón de Wishlist -->
                           <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
                           <!-- Botón de Quick View -->
                           <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>

                           <?php
                           // Mostrar la imagen dependiendo del tipo de producto
                           if ($fetch_product['type_id'] == 1) {
                              // Si es un libro
                              echo '<img src="libros/' . htmlspecialchars($fetch_product['book_folder'] . '/' . $fetch_product['image_01']) . '" alt="Imagen del libro">';
                              echo '<div class="type">Libro</div>';  // Leyenda de libro
                           } elseif ($fetch_product['type_id'] == 2) {
                              // Si es un audiolibro
                              echo '<img src="audiolibros/' . htmlspecialchars($fetch_product['book_folder'] . '/' . $fetch_product['image_01']) . '" alt="Imagen del audiolibro">';
                              echo '<div class="type">Audiolibro</div>';  // Leyenda de audiolibro
                           }
                           ?>

                           <div class="name"><?= htmlspecialchars($fetch_product['name']); ?></div>
                           <div class="flex">
                              <div class="price"><span>$</span><?= $fetch_product['price']; ?></div>
                           </div>
                           <input type="submit" value="AGREGAR AL CARRITO" class="btn" name="add_to_cart">
                        </form>
            <?php
                     } else {
                        // Si el producto ha sido comprado, solo mostramos la información sin botones y cambiamos el texto del botón
                        echo '<div class="box purchased">';
                        if ($fetch_product['type_id'] == 1) {
                           echo '<img src="libros/' . htmlspecialchars($fetch_product['book_folder'] . '/' . $fetch_product['image_01']) . '" alt="Imagen del libro">';
                           echo '<div class="type">Libro</div>';
                        } elseif ($fetch_product['type_id'] == 2) {
                           echo '<img src="audiolibros/' . htmlspecialchars($fetch_product['book_folder'] . '/' . $fetch_product['image_01']) . '" alt="Imagen del audiolibro">';
                           echo '<div class="type">Audiolibro</div>';
                        }
                        echo '<div class="name">' . htmlspecialchars($fetch_product['name']) . '</div>';
                        echo '<div class="flex">';
                        
                        echo '</div>';
                        
                        // Cambiar el texto del botón según el tipo de producto y redirigir
                        if ($fetch_product['type_id'] == 1) {
                           echo '<a href="read_book.php?pid=' . $fetch_product['id'] . '" class="btn">Leer ahora</a>';
                        } elseif ($fetch_product['type_id'] == 2) {
                           echo '<a href="listen_book.php?pid=' . $fetch_product['id'] . '" class="btn">Escuchar ahora</a>';
                        }
                        echo '</div>';
                     }
                  }
               }
            } else {
               echo '<p class="empty">¡NO SE ENCONTRARON PRODUCTOS!</p>';
            }
         }
         ?>
      </div>

   </section>
   <?php include 'components/footer.php'; ?>
   <script src="js/script.js"></script>

</body>

</html>
