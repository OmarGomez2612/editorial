<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
   exit();
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];

   // Primero eliminamos el administrador de la base de datos
   $delete_admins = $conn->prepare("DELETE FROM `admins` WHERE id = ?");
   $delete_admins->execute([$delete_id]);

   // Si el administrador eliminado es el que está en sesión, cerramos la sesión
   if ($delete_id == $admin_id) {
       session_unset();
       session_destroy();
       header('location:admin_login.php');
       exit();
   }

   header('location:admin_accounts.php');
   exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>CUENTAS DE ADMINISTRADOR</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="accounts">

   <h1 class="heading">CUENTAS DE ADMINISTRADOR</h1>

   <div class="box-container">

      <div class="box">
         <p>AGREGAR NUEVO ADMINISTRADOR</p>
         <a href="register_admin.php" class="option-btn">REGISTRAR ADMINISTRADOR</a>
      </div>

      <?php
         $select_accounts = $conn->prepare("SELECT * FROM `admins`");
         $select_accounts->execute();
         if ($select_accounts->rowCount() > 0) {
            while ($fetch_accounts = $select_accounts->fetch(PDO::FETCH_ASSOC)) {   
               $profile_img = !empty($fetch_accounts['profile_picture']) ? $fetch_accounts['profile_picture'] : '../uploaded_profiles/default.png';
      ?>
      <div class="box">
         <!-- Imagen de perfil ajustada al tamaño del contenedor -->
         <img src="<?= htmlspecialchars($profile_img) ?>" 
              alt="Foto del administrador" 
              style="width: 100%; max-width: 150px; height: 150px; object-fit: cover; border-radius: 10px; margin: 0 auto 15px; display: block;">

         <p> admin id : <span><?= $fetch_accounts['id']; ?></span> </p>
         <p> admin: <span><?= $fetch_accounts['name']; ?></span> </p>
         <div class="flex-btn">
            <a href="admin_accounts.php?delete=<?= $fetch_accounts['id']; ?>" 
               onclick="return confirm('¿ELIMINAR ESTA CUENTA?')" 
               class="delete-btn">ELIMINAR</a>
            <?php
               if ($fetch_accounts['id'] == $admin_id) {
                  echo '<a href="update_profile.php" class="option-btn">ACTUALIZAR</a>';
               }
            ?>
         </div>
      </div>
      <?php
            }
         } else {
            echo '<p class="empty">¡ADMINISTRADOR INVÁLIDO!</p>';
         }
      ?>

   </div>

</section>

<script src="../js/admin_script.js"></script>

</body>
</html>

