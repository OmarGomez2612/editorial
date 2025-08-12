<?php
include 'components/connect.php';

session_start();

// Verificar si el usuario está logueado
if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
}

include 'components/wishlist_cart.php';

// Validar la categoría
$category = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : '';
if (empty($category)) {
    echo '<p class="empty">CATEGORÍA NO VALIDA.</p>';
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>EXPLORA NUESTRAS CATEGORÍAS DE LIBROS</title>

   <!-- Fuente de Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Archivo CSS personalizado -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="products">

   <h1 class="heading">EXPLORA NUESTRA SELECCIÓN DE LIBROS</h1>

   <div class="box-container">

   <?php
     // Consulta para obtener la categoría desde la tabla 'categories' y encontrar el 'id' correspondiente
     $select_category = $conn->prepare("SELECT id FROM categories WHERE categorias = ?");
     $select_category->execute([$category]);

     // Verificar si la categoría existe
     if ($select_category->rowCount() > 0) {
         $category_data = $select_category->fetch(PDO::FETCH_ASSOC);
         $category_id = $category_data['id'];

         // Consulta para obtener los productos que corresponden a la categoría
         $select_products = $conn->prepare("SELECT * FROM products WHERE category_id = ?");
         $select_products->execute([$category_id]);

         // Verificar si hay productos disponibles en la categoría
         if ($select_products->rowCount() > 0) {
            while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) {
                // Variables para los botones y etiquetas según si el producto ha sido comprado
                $is_purchased = false;
                $button_label = 'AGREGAR AL CARRITO';  // Valor por defecto del botón
                $button_name = 'add_to_cart'; // Nombre por defecto para el botón

                // Si el usuario está logueado, verificar si el producto ha sido comprado
                if ($user_id) {
                    // Verificar si el producto ha sido comprado por el usuario
                    $check_purchase = $conn->prepare("SELECT pi.product_id FROM payments p 
                                                      JOIN payment_items pi ON p.payment_id = pi.payment_id 
                                                      WHERE p.user_id = ? AND pi.product_id = ?");
                    $check_purchase->execute([$user_id, $fetch_product['id']]);
                    if ($check_purchase->rowCount() > 0) {
                        $is_purchased = true;  // Producto ya comprado
                        $button_label = '';  // Cambiar texto del botón
                        $button_name = ''; // No se necesita el nombre para este botón
                    }
                }

                // Verificar si el producto es de la categoría "Gratis" (id 11)
                $is_free_category = $fetch_product['category_id'] == 11;

                ?>

                <form action="" method="post" class="box">
                   <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
                   <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
                   <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
                   <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">

                   <!-- Si el producto no ha sido comprado y no es gratis, mostrar los botones de wishlist y quick view -->
                   <?php if (!$is_purchased && !$is_free_category) { ?>
                      <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
                      <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
                   <?php } ?>

                   <!-- Mostrar imagen según el tipo de producto -->
                   <?php
                   if ($fetch_product['type_id'] == 1) {
                      echo '<img src="libros/' . htmlspecialchars($fetch_product['book_folder'] . '/' . $fetch_product['image_01']) . '" alt="Imagen del libro">';
                   } elseif ($fetch_product['type_id'] == 2) {
                      echo '<img src="audiolibros/' . htmlspecialchars($fetch_product['book_folder'] . '/' . $fetch_product['image_01']) . '" alt="Imagen del audiolibro">';
                   }
                   ?>

                   <!-- Nombre y tipo de producto -->
                   <div class="name"><?= $fetch_product['name']; ?></div>
                   
                   <!-- Leyenda según tipo de producto -->
                   <div class="product-type"><?= ($fetch_product['type_id'] == 1) ? 'Libro' : 'Audiolibro'; ?></div>
                   
                   <!-- Si el producto no es gratis, mostrar el precio -->
                   <?php if (!$is_free_category) { ?>
                      <div class="flex">
                        <div class="price"><span>$</span><?= $fetch_product['price']; ?><span> MXN</span></div>
                      </div>
                   <?php } else { ?>
                      <span style="color: green; font-weight: bold;">GRATIS</span>
                   <?php } ?>
                  

                   <!-- Botón de agregar al carrito o leer/escuchar dependiendo de si se compró -->
                   <?php if ($is_purchased): ?>
                      <!-- Si el producto ha sido comprado, el botón redirige a la página correspondiente -->
                      <?php if ($fetch_product['type_id'] == 1): ?>
                         <a href="read_book.php?id=<?= $fetch_product['id']; ?>" class="btn"><?= $button_label; ?>LEER AHORA</a>
                      <?php elseif ($fetch_product['type_id'] == 2): ?>
                         <a href="listen_book.php?id=<?= $fetch_product['id']; ?>" class="btn"><?= $button_label; ?>ESCUCHAR AHORA</a>
                      <?php endif; ?>
                   <?php elseif ($is_free_category): ?>
                      <!-- Si el producto es gratis, mostrar el botón "Escuchar ahora" que redirige a listen_book.php -->
                      <a href="listen_book.php?pid=<?= $fetch_product['id']; ?>" class="btn">ESCUCHAR AHORA</a>
                   <?php else: ?>
                      <!-- Si el producto no ha sido comprado, se muestra el botón de agregar al carrito -->
                      <input type="submit" value="<?= $button_label; ?>" class="btn" name="<?= $button_name; ?>">
                   <?php endif; ?>
                 </form>

                <?php
            }
         } else {
            echo '<p class="empty">¡NO HAY LIBROS DISPONIBLES EN ESTA CATEGORÍA!</p>';
         }
     } else {
        echo '<p class="empty">¡CATEGORÍA NO ENCONTRADA!</p>';
     }
   ?>

   </div>

</section>


<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

<script>
// Función para alertar al usuario que debe iniciar sesión o registrarse
function alertLogin() {
    alert('INICIA SESIÓN O REGÍSTRATE PARA AGREGAR AL CARRITO O A TU LISTA DE DESEOS.');
    window.location.href = 'login_or_register.php'; // Redirige a la página de inicio de sesión o registro
}
</script>

</body>
</html>
