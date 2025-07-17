<?php
// Incluir el archivo de conexión a la base de datos
include 'components/connect.php';

// Iniciar la sesión
session_start();

// Verificar si el usuario está logueado
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

// Incluir el archivo para el manejo de la lista de deseos y el carrito
include 'components/wishlist_cart.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>VISTA RÁPIDA</title>

   <!-- Enlace a Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Enlace al archivo CSS personalizado -->
   <link rel="stylesheet" href="css/style.css">
   
</head>

<body>

   <!-- Incluir el encabezado del usuario -->
   <?php include 'components/user_header.php'; ?>

   <!-- Sección de rápida del producto -->
   <section class="quick-view">
      <h1 class="heading">VISTA RÁPIDA</h1>

      <?php
      // Obtener el ID del producto desde la URL
      $pid = isset($_GET['pid']) ? $_GET['pid'] : null;

      if ($pid) {
         // Preparar y ejecutar la consulta para obtener los detalles del producto
         $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
         $select_products->execute([$pid]);

         if ($select_products->rowCount() > 0) {
            while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) {
      ?>

               <!-- Formulario para agregar al carrito y lista de deseos -->
               <form action="" method="post" class="box">
                  <input type="hidden" name="pid" value="<?= htmlspecialchars($fetch_product['id']); ?>">
                  <input type="hidden" name="name" value="<?= htmlspecialchars($fetch_product['name']); ?>">
                  <input type="hidden" name="price" value="<?= htmlspecialchars($fetch_product['price']); ?>">
                  <input type="hidden" name="image" value="<?= htmlspecialchars($fetch_product['image_01']); ?>">

                  <div class="row">
                     <!-- Contenedor de imagenes del producto -->
                     <div class="image-container">
                        
                        <?php
                        // Verificamos si el tipo de producto es un libro o un audiolibro
                        if ($fetch_product['type_id'] == 1) {
                           // Si es un libro
                           echo '<div class="main-image">';
                           echo '<img src="libros/' . htmlspecialchars($fetch_product['book_folder'] . '/' . $fetch_product['image_01']) . '" alt="Imagen del libro">';
                           echo '</div>';
                           echo '<div class="sub-image">';
                           echo '<img src="libros/' . htmlspecialchars($fetch_product['book_folder'] . '/' . $fetch_product['image_01']) . '" alt="Imagen del audiolibro">';
                           echo '<img src="libros/' . htmlspecialchars($fetch_product['book_folder'] . '/' . $fetch_product['image_02']) . '" alt="Imagen del audiolibro">';
                           echo '</div>';
                        } elseif ($fetch_product['type_id'] == 2) {
                           // Si es un audiolibro
                           echo '<div class="main-image">';
                           echo '<img src="audiolibros/' . htmlspecialchars($fetch_product['book_folder'] . '/' . $fetch_product['image_01']) . '" alt="Imagen del libro">';
                           echo '</div>';
                           echo '<div class="sub-image">';
                           echo '<img src="audiolibros/' . htmlspecialchars($fetch_product['book_folder'] . '/' . $fetch_product['image_01']) . '" alt="Imagen del audiolibro">';
                           echo '<img src="audiolibros/' . htmlspecialchars($fetch_product['book_folder'] . '/' . $fetch_product['image_02']) . '" alt="Imagen del audiolibro">';
                           echo '</div>';
                        }
                        ?>
                     </div>

                     <!-- Contenido y detalles del producto -->
                     <div class="content">
                        <div class="name"><?= htmlspecialchars($fetch_product['name']); ?></div>

                        <!-- Autor debajo del nombre del libro -->
                        <div class="author">Autor: <?= htmlspecialchars($fetch_product['author']); ?></div>

                        <div class="flex">
                           <div class="price">
                              <span>$</span><?= $fetch_product['price']; ?><span> MXN</span>
                           </div>
                        </div>

                        <!-- Si el precio es 0 (producto gratis) -->
                        <?php if ($fetch_product['price'] == 0): ?>
                          
                        <?php endif; ?>

                        <div class="details"><?= htmlspecialchars($fetch_product['details']); ?></div>

                        <!-- Botón para ver el archivo PDF si está disponible -->
<?php if (!empty($fetch_product['pdf_file'])): ?>
   <?php if (isset($_SESSION['user_id'])): ?>
      <!-- Si el usuario está logueado -->
      <?php if ($fetch_product['price'] > 0): ?>
         <!-- Si el libro tiene un costo, mostrar el mensaje solo cuando se haga clic -->
         <a href="javascript:void(0);" onclick="showPdfAlert();" class="btn">VER LIBRO</a>
         <script>
            function showPdfAlert() {
               alert('Este libro tiene un costo. Necesitas comprarlo para acceder al contenido.');
            }
         </script>
      <?php else: ?>
         <!-- Si el libro es gratuito, mostrar el enlace para ver el libro -->
         <a href="libros/<?= htmlspecialchars($fetch_product['book_folder']); ?>" target="_blank" class="btn">VER LIBRO</a>
      <?php endif; ?>
   <?php else: ?>
      <!-- Si el usuario no está logueado, mostrar el enlace para iniciar sesión -->
      <a href="user_login.php" class="btn">VER EL LIBRO</a>
   <?php endif; ?>
<?php endif; ?>


                        <!-- Botón para escuchar el audiolibro si está disponible -->
<?php if (!empty($fetch_product['AUDIO'])): ?>
   <?php if (isset($_SESSION['user_id'])): ?>
      <!-- Si el usuario está logueado -->
      <?php if ($fetch_product['price'] > 0): ?>
         <!-- Si el libro tiene un costo, mostrar el mensaje solo cuando se haga clic -->
         <a href="javascript:void(0);" onclick="showAudioAlert();" class="btn">ESCUCHAR AUDIOLIBRO</a>
         <script>
            function showAudioAlert() {
               alert('Este audiolibro tiene un costo. Necesitas comprarlo para acceder al contenido.');
            }
         </script>
      <?php else: ?>
         <!-- Si el libro es gratuito, mostrar el enlace para escuchar el audiolibro -->
         <a href="listen_book.php?id=<?= $fetch_product['id']; ?>" class="btn">ESCUCHAR AHORA</a>
      <?php endif; ?>
   <?php else: ?>
      <!-- Si el usuario no está logueado, mostrar el enlace para iniciar sesión -->
      <a href="listen_book.php?id=<?= $fetch_product['id']; ?>" class="btn">ESCUCHAR AHORA</a>
      <a href="user_login.php" class="btn">INICIAR SESIÓN o REGISTRARSE</a>
   <?php endif; ?>
<?php endif; ?>


                        <!-- Si el producto tiene precio, mostrar las opciones de carrito y lista de deseos -->
                        <?php if ($fetch_product['price'] > 0): ?>
                           <div class="flex-btn">
                              <input type="submit" value="AÑADIR AL CARRITO" class="btn" name="add_to_cart">
                              <input class="option-btn" type="submit" name="add_to_wishlist" value="AÑADIR A LA LISTA DE DESEOS">
                           </div>
                        <?php endif; ?>
                     </div>
                  </div>
               </form>
      <?php
            }
         } else {
            // Mensaje si no se encuentran productos con el ID proporcionado
            echo '<p class="empty">¡NO HAY LIBROS DISPONIBLES EN ESTE MOMENTO!.</p>';
         }
      } else {
         // Mensaje si no se ha proporcionado un ID de producto
         echo '<p class="empty">¡PRODUCTO NO ENCONTRADO!.</p>';
      }
      ?>

   </section>

   <!-- Enlace al archivo JavaScript -->
   <script src="js/script.js"></script>

</body>

</html>
