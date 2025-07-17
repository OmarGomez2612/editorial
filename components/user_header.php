<?php
   if(isset($message)){
      foreach($message as $message){
         echo '
         <div class="message">
            <span>' . htmlspecialchars($message) . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
      }
   }
?>

<header class="header">
   <section class="flex">
      <!-- Logo Section -->
      <a href="home.php" class="logo">
         <img src="images/logo.png" alt="Logo de la Editorial" style="max-width: 120px; height: auto;">
      </a>

      <!-- Navigation Bar -->
      <nav class="navbar">
         <a href="home.php" class="nav-link">INICIO</a>
         <a href="orders.php" class="nav-link">MIS PEDIDOS</a>
         <a href="my_books.php" class="nav-link">MIS LIBROS</a>
         <a href="my_audiobks.php" class="nav-link">MIS AUDIOLIBROS</a>
         <a href="shop.php" class="nav-link">VER LIBROS</a><br>
         <a href="shopaudio.php" class="nav-link">VER AUDIOLIBROS</a>
      </nav>

      <!-- Icons Section -->
      <div class="icons">
         <?php
            // Verificamos si el usuario está autenticado y existe el ID de usuario en la sesión
            if(isset($_SESSION['user_id'])) {
               // Usamos directamente $_SESSION['user_id'] en lugar de $user_id
               $user_id = $_SESSION['user_id'];

               // Contar artículos en la lista de deseos
               $count_wishlist_items = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
               $count_wishlist_items->execute([$user_id]);
               $total_wishlist_counts = $count_wishlist_items->rowCount();

               // Contar artículos en el carrito de compras
               $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
               $count_cart_items->execute([$user_id]);
               $total_cart_counts = $count_cart_items->rowCount();
            }
         ?>
         <div id="menu-btn" class="fas fa-bars" aria-label="Menú"></div>
         <a href="search_page.php"><i class="fas fa-search" aria-label="Buscar productos"></i></a>
         <a href="wishlist.php"><i class="fas fa-heart" aria-label="Ver lista de deseos"></i><span>(<?= isset($total_wishlist_counts) ? $total_wishlist_counts : 0; ?>)</span></a>
         <a href="cart.php"><i class="fas fa-shopping-cart" aria-label="Ver carrito de compras"></i><span>(<?= isset($total_cart_counts) ? $total_cart_counts : 0; ?>)</span></a>
         <div id="user-btn" class="fas fa-user" aria-label="Perfil de usuario"></div>
      </div>

      <!-- Profile Section -->
      <div class="profile">
         <?php
            if(isset($_SESSION['user_id'])){
               // Si el usuario está autenticado
               $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
               $select_profile->execute([$_SESSION['user_id']]);
               if($select_profile->rowCount() > 0){
                  $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <p class="profile-name"><?= htmlspecialchars($fetch_profile["name"]); ?></p>
         <a href="update_user.php" class="btn btn-update">ACTUALIZAR PERFIL</a>
         <a href="components/user_logout.php" class="delete-btn" onclick="return confirm('¿Cerrar sesión del sitio web?');">CERRAR SESIÓN</a>
         <?php
               }
            } else {
         ?>
         <p class="profile-msg">"Inicia sesión o regístrate para acceder a tus libros y ofertas exclusivas."</p>
         <div class="flex-btn">
            <a href="user_register.php" class="option-btn">REGISTRAR</a>
            <a href="user_login.php" class="option-btn">INICIAR SESIÓN</a>
         </div>
         <?php
            }
         ?>
      </div>
   </section>
</header>

<script>
document.addEventListener("DOMContentLoaded", function() {
   const navLinks = document.querySelectorAll(".nav-link");
   navLinks.forEach(link => {
      if(link.href === window.location.href) {
         link.classList.add("active");
      }
   });
});
</script>
