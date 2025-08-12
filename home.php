<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};
include 'components/wishlist_cart.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>INICIO</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <!-- CSS to remove image borders -->
   <style>
      /* SOLO para recuadros en secciones home-products (novedades y libros gratis) */
.home-products .swiper-slide.slide {
   display: flex;
   flex-direction: column;
   justify-content: space-between;
   height: 430px; /* Ajusta este valor si es necesario */
   padding: 10px;
   box-sizing: border-box;
   background-color: #fff;
   border-radius: 8px;
   overflow: hidden;
}

.home-products .swiper-slide.slide img {
   max-height: 200px;
   object-fit: contain;
   margin: 0 auto 10px;
}

.home-products .swiper-slide.slide .name {
   font-size: 1.9rem;
   font-weight: bold;
   text-align: center;
   height: 45px;
   line-height: 1.3;
   overflow: hidden;
   text-overflow: ellipsis;
   display: -webkit-box;
   -webkit-line-clamp: 2; 
   -webkit-box-orient: vertical;
   margin-bottom: 8px;
}

.home-products .swiper-slide.slide .product-type {
   font-size: 0.9rem;
   color: #555;
   text-align: center;
   margin-bottom: 10px;
}

.home-products .swiper-slide.slide .flex {
   margin-top: auto;
   display: flex;
   justify-content: center;
   align-items: center;
   gap: 5px;
   margin-bottom: 10px;
}

.home-products .swiper-slide.slide .btn {
   display: block;
   text-align: center;
   margin-top: auto;
}

      img {
         border: none;
         outline: none;
         display: block;
         max-width: 100%;
         height: auto;
      }
      .home-bg {
         background-image: url('images/imagen-4.jpeg');
         background-size: cover;
         background-position: center;
      }

      /* Animación para las imágenes y contenido del banner */
      .category .swiper-slide .image {
         opacity: 0;
         animation: fadeInUp 1s ease-out forwards;
      }

      .category .swiper-slide .content {
         opacity: 0;
         animation: fadeInUp 1s ease-out 0.3s forwards;
      }

      /* Efecto de aparición de las imágenes */
      @keyframes fadeInUp {
         0% {
            opacity: 0;
            transform: translateY(20px);
         }
         100% {
            opacity: 1;
            transform: translateY(0);
         }
      }

      /* Animación para el slider principal */
      .home .swiper-slide {
         opacity: 0;
         animation: slideIn 1s ease-out forwards;
      }

      /* Animación de deslizamiento */
      @keyframes slideIn {
         0% {
            opacity: 0;
            transform: translateX(-100%);
         }
         100% {
            opacity: 1;
            transform: translateX(0);
         }
      }

      /* Aumentar el espacio entre las diapositivas para el movimiento */
      .category .swiper-slide {
         margin-right: 20px;
      }
   </style>
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<div class="home-bg">

<section class="home">

   <div class="swiper home-slider">
   
   <div class="swiper-wrapper">

      <div class="swiper-slide slide">
         <div class="image">
            <img src="images/imagen-1.png" alt="">
         </div>
         <div class="content">
            <span>AMPLÍA TU COLECCIÓN</span>
            <h3>Bienvenido a Tlacuache Editorial Digital, el lugar donde los libros cobran vida.</h3>
            <a href="shop.php" class="btn">VER LIBROS</a>
         </div>
      </div>

      <div class="swiper-slide slide">
         <div class="image">
            <img src="images/imagen-2.png" alt="">
         </div>
         <div class="content">
            <span>AMPLÍA TU COLECCIÓN</span>
            <h3>Sumérgete en nuevas lecturas y expande tu horizonte</h3>
            <a href="shop.php" class="btn">VER LIBROS</a>
         </div>
      </div>

      <div class="swiper-slide slide">
         <div class="image">
            <img src="images/imagen-3.png" alt="">
         </div>
         <div class="content">
            <span>AMPLÍA TU COLECCIÓN</span>
            <h3>Descubre tu próxima aventura literaria</h3>
            <a href="shop.php" class="btn">VER LIBROS</a>
         </div>
      </div>

   </div>

      <div class="swiper-pagination"></div>

   </div>

</section>



</div>


<!-- Sección de Libros Gratis -->
<section class="home-products">
   <h1 class="heading">LIBROS GRATIS</h1>
   <div class="swiper products-slider">
      <div class="swiper-wrapper">
         <?php
            $select_free_books = $conn->prepare("SELECT * FROM `products` WHERE price = 0 LIMIT 6"); 
            $select_free_books->execute();
            if($select_free_books->rowCount() > 0){
               while($fetch_free_book = $select_free_books->fetch(PDO::FETCH_ASSOC)){
         ?>
         <div class="swiper-slide slide">
            <a href="quick_view.php?pid=<?= $fetch_free_book['id']; ?>" class="fas fa-eye"></a>
            <img src="audiolibros/<?= $fetch_free_book['book_folder'] . '/' . $fetch_free_book['image_01']; ?>" alt="Imagen del Libro">
            <div class="name"><?= $fetch_free_book['name']; ?></div>
            <div class="flex">
               <div class="price" style="color: green; font-weight: bold;">GRATIS</div>
            </div>
          
         </div>
         <?php
               }
            }else{
               echo '<p class="empty"><a href="user_login.php" style="text-decoration: none; color: inherit;">¡NO HAY LIBROS GRATIS DISPONIBLES!</a></p>';
            }
         ?>
      </div>
      <div class="swiper-pagination"></div>
   </div>
</section>


<!--SECCIÓN DE CATEGORÍAS-->
<?php

// Obtener el valor de la categoría desde la URL
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Consultar categorías
$select_categories = $conn->prepare("SELECT * FROM categories");
$select_categories->execute();
?>

<section class="category">
   <h1 class="heading">CATEGORÍAS</h1>

   <div class="swiper category-slider">
      <div class="swiper-wrapper">
         <?php
         // Verificar si hay categorías en la base de datos
         if ($select_categories->rowCount() > 0) {
            while ($fetch_category = $select_categories->fetch(PDO::FETCH_ASSOC)) {
         ?>
         <a href="category.php?category=<?= htmlspecialchars($fetch_category['categorias']); ?>" class="swiper-slide slide">
            <div class="image">
               <img src="images/<?= htmlspecialchars($fetch_category['image']); ?>" alt="<?= htmlspecialchars($fetch_category['categorias']); ?>">
            </div>
            <div class="content">
               <h3><?= htmlspecialchars($fetch_category['categorias']); ?></h3>
            </div>
         </a>
         <?php
            }
         } else {
            echo '<p class="empty"><a href="user_login.php" style="text-decoration: none; color: inherit;">Inicia sesión para conocer las categorías disponibles.</a></p>';
         }
         ?>
      </div>
      <div class="swiper-pagination"></div>
   </div>
</section>

<?php
// Mostrar los productos si se ha seleccionado una categoría
if ($category) {
   // Consulta para obtener los productos de la categoría seleccionada
   $select_products = $conn->prepare("SELECT * FROM products WHERE category_id = (SELECT id FROM categories WHERE categorias = ?)");
   $select_products->execute([$category]);
?>

<section class="products">
   <h1 class="heading"><?= htmlspecialchars($category); ?> - Productos</h1>

   <div class="swiper products-slider">
      <div class="swiper-wrapper">
         <?php
         // Verificar si hay productos en la categoría
         if ($select_products->rowCount() > 0) {
            while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) {
         ?>
         <form action="" method="post" class="swiper-slide slide">
            <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
            <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
            <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
            <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
            
            <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
            <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
            
            <?php
            if ($fetch_product['type_id'] == 1) {
               echo '<img src="libros/' . htmlspecialchars($fetch_product['book_folder'] . '/' . $fetch_product['image_01']) . '" alt="Imagen del libro">';
            } elseif ($fetch_product['type_id'] == 2) {
               echo '<img src="audiolibros/' . htmlspecialchars($fetch_product['book_folder'] . '/' . $fetch_product['image_01']) . '" alt="Imagen del audiolibro">';
            }
            ?>
            <div class="name"><?= $fetch_product['name']; ?></div>
            <div class="flex">
               <div class="price"><span>$</span><?= $fetch_product['price']; ?><span> MXN</span></div>
            </div>

            <input type="submit" value="AÑADIR AL CARRITO" class="btn" name="add_to_cart">
         </form>
         <?php
            }
         } else {
            echo '<p class="empty">No hay productos disponibles en esta categoría.</p>';//Creo que se quitará
         }
         ?>
      </div>
      <div class="swiper-pagination"></div>
   </div>
</section>

<?php
} // Fin del bloque para mostrar productos
?>

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script>
   // Inicializar el Swiper para categorías
   var swiper = new Swiper('.category-slider', {
      slidesPerView: 3,
      spaceBetween: 30,
      pagination: {
         el: '.swiper-pagination',
         clickable: true,
      },
   });

   // Inicializar el Swiper para productos
   var swiperProducts = new Swiper('.products-slider', {
      slidesPerView: 4,
      spaceBetween: 30,
      pagination: {
         el: '.swiper-pagination',
         clickable: true,
      },
   });
</script>

<section class="home-products">
   <h1 class="heading">NOVEDADES</h1>
   <div class="swiper products-slider">
      <div class="swiper-wrapper">
         <?php
         // Obtener productos más recientes
         $select_products = $conn->prepare("SELECT * FROM `products` ORDER BY id DESC LIMIT 6");
         $select_products->execute();

         if ($select_products->rowCount() > 0) {
            while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) {

               $is_purchased = false;

               // Verificar si el producto ya fue comprado (si hay sesión)
               if ($user_id) {
                  $select_payment = $conn->prepare("SELECT * 
                     FROM `payment_items` pi
                     INNER JOIN `payments` p ON pi.payment_id = p.payment_id
                     WHERE pi.product_id = ? AND p.user_id = ?");
                  $select_payment->execute([$fetch_product['id'], $user_id]);
                  $is_purchased = $select_payment->rowCount() > 0;
               }

               $product_type = ($fetch_product['type_id'] == 1) ? 'Libro' : 'Audiolibro';
               $button_label = $is_purchased ? (($fetch_product['type_id'] == 1) ? 'LEER AHORA' : 'ESCUCHAR AHORA') : 'AÑADIR AL CARRITO';
               $button_name = $is_purchased ? '' : 'add_to_cart';
         ?>
         <form action="" method="post" class="swiper-slide slide">
            <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
            <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
            <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
            <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">

            <?php if (!$is_purchased): ?>
               <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
               <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
            <?php endif; ?>

            <?php
            if ($fetch_product['type_id'] == 1) {
               echo '<img src="libros/' . htmlspecialchars($fetch_product['book_folder'] . '/' . $fetch_product['image_01']) . '" alt="Imagen del libro">';
            } elseif ($fetch_product['type_id'] == 2) {
               echo '<img src="audiolibros/' . htmlspecialchars($fetch_product['book_folder'] . '/' . $fetch_product['image_01']) . '" alt="Imagen del audiolibro">';
            }
            ?>

            <div class="name"><?= $fetch_product['name']; ?></div>
            <div class="product-type"><?= $product_type; ?></div>

            <div class="flex">
               <div class="price"><span>$</span><?= $fetch_product['price']; ?><span> MXN</span></div>
            </div>

            <?php if ($user_id && $is_purchased): ?>
               <?php if ($fetch_product['type_id'] == 1): ?>
                  <a href="read_book.php?id=<?= $fetch_product['id']; ?>" class="btn"><?= $button_label; ?></a>
               <?php elseif ($fetch_product['type_id'] == 2): ?>
                  <a href="listen_book.php?pid=<?= $fetch_product['id']; ?>" class="btn"><?= $button_label; ?></a>
               <?php endif; ?>
            <?php else: ?>
               <input type="submit" value="<?= $button_label; ?>" class="btn" name="<?= $button_name; ?>">
            <?php endif; ?>
         </form>
         <?php
            }
         } else {
            echo '<p class="empty">¡No hay productos disponibles!</p>';
         }
         ?>
      </div>
      <div class="swiper-pagination"></div>
   </div>
</section>

<?php include 'components/footer.php'; ?>

<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

<script src="js/script.js"></script>

<script>

var swiper = new Swiper(".home-slider", {
   loop:true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
   autoplay: {
      delay: 3000,  // Intervalo de 3 segundos entre cada diapositiva
      disableOnInteraction: false,  // Mantiene el autoplay activo incluso si el usuario interactúa
   },
});

var swiper = new Swiper(".category-slider", {
   loop:true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
   autoplay: {
      delay: 3000,  // Intervalo de 3 segundos entre cada diapositiva
      disableOnInteraction: false,  // Mantiene el autoplay activo incluso si el usuario interactúa
   },
   breakpoints: {
      0: {
         slidesPerView: 2,
       },
      650: {
        slidesPerView: 3,
      },
      768: {
        slidesPerView: 4,
      },
      1024: {
        slidesPerView: 5,
      },
   },
});

var swiperProducts = new Swiper(".products-slider", {
   loop: true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable: true,
   },
   autoplay: {
      delay: 3000,
      disableOnInteraction: false,
   },
   breakpoints: {
      550: {
         slidesPerView: 2,
      },
      768: {
         slidesPerView: 2,
      },
      1024: {
         slidesPerView: 3,
      },
   },
});

// Pausar autoplay al pasar el mouse (hover) y reanudar al salir
document.querySelectorAll('.products-slider').forEach(function(slider) {
   slider.addEventListener('mouseenter', function() {
      swiperProducts.autoplay.stop();
   });
   slider.addEventListener('mouseleave', function() {
      swiperProducts.autoplay.start();
   });
});

</script>

</body>
</html>
