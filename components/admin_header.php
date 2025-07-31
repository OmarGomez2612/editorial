<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Panel de Administración</title>
   <!-- Puedes agregar otros estilos si los usas -->
</head>
<style>
.profile-pic-wrapper {
   margin: 0 auto;
   width: 100px;
   height: 100px;
   border-radius: 25%;
   overflow: hidden;
   display: flex;
   justify-content: center;
   align-items: center;
   background-color: #f0f0f0;
}

.profile-pic {
   width: 100%;
   height: 100%;
   object-fit: cover;
   display: block;
}
</style>

<body>

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

            $admin_photo = !empty($fetch_profile['profile_picture']) ? $fetch_profile['profile_picture'] : '../uploaded_profiles/default.png';
         ?>
         
         <div class="profile-pic-wrapper">
            <img src="<?= $admin_photo; ?>" alt="Foto de perfil" class="profile-pic">
         </div>
         
         <p><?= htmlspecialchars($fetch_profile['name']); ?></p>
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
<script src="../js/admin_script.js"></script>

</body>
</html>
