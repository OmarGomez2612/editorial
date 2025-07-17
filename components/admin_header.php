<?php
include 'connect.php';
   if(isset($message)){
      foreach($message as $message){
         echo '
         <div class="message">
            <span>'.$message.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
      }
   }
   $admin_id = $_SESSION['admin_id'] ?? null;
?>

<header class="header">

   <section class="flex">

      <a href="../admin/dashboard.php" class="logo">ADMIN<span>PANEL</span></a>

      <nav class="navbar">
         <a href="../admin/dashboard.php" class="nav-link">INICIO</a>
         <a href="../admin/add_categories.php" class="nav-link">CATEGORÍAS</a>
         <a href="../admin/products.php" class="nav-link">AGREGAR</a>
         <a href="../admin/add_prods.php" class="nav-link">LIBROS</a>
         <a href="../admin/productsaudio.php" class="nav-link">AUDIOLIBROS</a>
         <a href="../admin/placed_orders.php" class="nav-link">COMPRAS</a>
         <a href="../admin/admin_accounts.php" class="nav-link">ADMINISTRADORES</a>
         <a href="../admin/users_accounts.php" class="nav-link">USUARIOS</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM admins WHERE id = ?");
            $select_profile->execute([$admin_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <p><?=($fetch_profile['name']); ?></p>
         <a href="../admin/update_profile.php" class="btn">ACTUALIZAR PERFIL</a>
         <div class="flex-btn">
            <a href="../admin/register_admin.php" class="option-btn">REGISTRAR</a>
           
         </div>
         <a href="../components/admin_logout.php" class="delete-btn" onclick="return confirm('¿Cerrar sesión del sitio web?');">CERRAR SESIÓN</a> 
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