<?php

include '../components/connect.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
   header('location:admin_login.php');
   exit();
}

if (isset($_POST['submit'])) {

   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $pass = filter_var(sha1($_POST['pass']), FILTER_SANITIZE_STRING);
   $cpass = filter_var(sha1($_POST['cpass']), FILTER_SANITIZE_STRING);

   $profile = $_FILES['profile']['name'];
   $profile_tmp = $_FILES['profile']['tmp_name'];
   $profile_folder = '../uploaded_profiles/' . $profile;

   if (!empty($profile)) {
      move_uploaded_file($profile_tmp, $profile_folder);
   } else {
      $profile_folder = '../uploaded_profiles/default.png';
   }

   if (empty($name) || empty($pass) || empty($cpass)) {
      $message[] = 'Todos los campos son obligatorios.';
   } else {
      $select_admin = $conn->prepare("SELECT * FROM `admins` WHERE name = ?");
      $select_admin->execute([$name]);

      if ($select_admin->rowCount() > 0) {
         $message[] = 'El nombre de usuario ya existe.';
      } else {
         if ($pass != $cpass) {
            $message[] = 'Las contraseñas no coinciden.';
         } else {
            $insert_admin = $conn->prepare("INSERT INTO `admins` (name, password, profile_picture) VALUES (?, ?, ?)");
            $insert_admin->execute([$name, $cpass, $profile_folder]);
            $message[] = 'Nuevo administrador registrado con éxito.';
         }
      }
   }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <title>Nuevo Administrador</title>
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="../css/admin_style.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="form-container">
   <form action="" method="post" enctype="multipart/form-data">
      <h3>Registrar nuevo administrador</h3>

      <input type="text" name="name" required placeholder="Nombre de usuario" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" required placeholder="Contraseña" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" required placeholder="Confirmar contraseña" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">

      <!-- Subir o tomar foto -->
      <label for="profile">Foto de perfil:</label>
      <input type="file" name="profile" id="profile" accept="image/*" capture="environment" class="box">
      <img id="preview" src="" alt="Vista previa" style="max-width: 150px; margin-top: 10px; display: none; border-radius: 10px;">

      <input type="submit" value="REGISTRAR AHORA" class="btn" name="submit">
   </form>
</section>

<?php
if (isset($message)) {
   foreach ($message as $msg) {
      echo '<p class="message">' . htmlspecialchars($msg) . '</p>';
   }
}
?>

<script src="../js/admin_script.js"></script>
<script>
   // Mostrar vista previa de la imagen seleccionada
   const profileInput = document.getElementById("profile");
   const previewImg = document.getElementById("preview");

   profileInput.addEventListener("change", function () {
      const file = this.files[0];
      if (file) {
         const reader = new FileReader();
         reader.onload = function (e) {
            previewImg.setAttribute("src", e.target.result);
            previewImg.style.display = "block";
         };
         reader.readAsDataURL(file);
      } else {
         previewImg.setAttribute("src", "");
         previewImg.style.display = "none";
      }
   });
</script>

</body>
</html>
