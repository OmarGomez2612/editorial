<?php
// Mostrar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'components/connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
   header('Location: user_login.php');
   exit;
}

$user_id = $_SESSION['user_id'];

// Función para obtener el perfil
function getUserProfile($conn, $user_id) {
   $stmt = $conn->prepare("SELECT id, name, email, password, apellido_paterno, apellido_materno, telefono, edad, role_id, profile_picture FROM users WHERE id = ?");
   $stmt->execute([$user_id]);
   return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Obtener perfil actual
$fetch_profile = getUserProfile($conn, $user_id);

$success_message = '';
$password_message = '';

if (isset($_POST['submit'])) {
   $name     = htmlspecialchars(strip_tags($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
   $email    = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
   $telefono = htmlspecialchars(strip_tags($_POST['telefono'] ?? ''), ENT_QUOTES, 'UTF-8');
   $edad     = filter_var($_POST['edad'] ?? '', FILTER_SANITIZE_NUMBER_INT);

   $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, telefono = ?, edad = ? WHERE id = ?");
   $stmt->execute([$name, $email, $telefono, $edad, $user_id]);

   if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
      $image_name = $_FILES['profile_picture']['name'];
      $image_tmp = $_FILES['profile_picture']['tmp_name'];
      $folder = 'uploaded_profiles/' . $image_name;

      if (!is_dir('uploaded_profiles')) {
         mkdir('uploaded_profiles', 0777, true);
      }

      if (!empty($fetch_profile['profile_picture']) && file_exists($fetch_profile['profile_picture'])) {
         unlink($fetch_profile['profile_picture']);
      }

      move_uploaded_file($image_tmp, $folder);

      $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
      $stmt->execute([$folder, $user_id]);
   }

   $old_pass  = isset($_POST['old_pass']) ? sha1($_POST['old_pass']) : '';
   $new_pass  = isset($_POST['new_pass']) ? sha1($_POST['new_pass']) : '';
   $cpass     = isset($_POST['confirm_pass']) ? sha1($_POST['confirm_pass']) : '';

   if (!empty($_POST['old_pass']) || !empty($_POST['new_pass']) || !empty($_POST['confirm_pass'])) {
      if ($old_pass !== $fetch_profile['password']) {
         $password_message = '⚠️ ¡La contraseña actual no es correcta!';
      } elseif ($new_pass !== $cpass) {
         $password_message = '⚠️ ¡Las nuevas contraseñas no coinciden!';
      } else {
         $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
         $stmt->execute([$new_pass, $user_id]);
         $password_message = '✅ ¡Perfil actualizado correctamente!';
      }
   }


   $fetch_profile = getUserProfile($conn, $user_id);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1" />
   <title>ACTUALIZAR PERFIL</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
   <link rel="stylesheet" href="css/style.css" />
   <style>
   .profile-picture {
      width: 150px;
      height: 150px;
      object-fit: cover;
      border-radius: 50%;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      display: block;
      margin: 0 auto 1.5rem auto;
      border: 3px solid #ddd;
   }
</style>
</head>
<body>
<?php include 'components/user_header.php'; ?>
<?php
include 'components/connect.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
   header('location:login.php');
   exit;
}

$select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$select_profile->execute([$user_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
?>

<section class="form-container">

   <form action="update_user.php" method="POST" enctype="multipart/form-data">
      <h3>Actualizar perfil</h3>

      <!-- Mensajes -->
      <?php if (!empty($password_message)): ?>
         <div id="password-alert" style="background-color: #fff3cd; border-left: 5px solid #ffc107; padding: 1rem; margin-bottom:1rem; color: #856404;">
            <p style="margin: 0;"><?= htmlspecialchars($password_message) ?></p>
         </div>
      <?php endif; ?>

      <?php if (!empty($success_message)): ?>
         <div style="background-color: #d4edda; border-left: 5px solid #28a745; padding: 1rem; margin-bottom:1rem; color: #155724;">
            <p style="margin: 0;"><?= htmlspecialchars($success_message) ?></p>
         </div>
      <?php endif; ?>

      <?php if (!empty($fetch_profile['profile_picture'])): ?>
   <img src="<?= htmlspecialchars($fetch_profile['profile_picture']); ?>" alt="Foto de perfil" class="profile-picture">
<?php endif; ?>


      <input type="text" name="name" required maxlength="50" placeholder="Ingresa tu nombre" class="box" value="<?= htmlspecialchars($fetch_profile['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" readonly onkeydown="return false" onpaste="return false" style="background-color:#f5f5f5; cursor:not-allowed;">
      <input type="text" name="apellido_paterno" required maxlength="50" placeholder="Apellido paterno" class="box" value="<?= htmlspecialchars($fetch_profile['apellido_paterno'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" readonly onkeydown="return false" onpaste="return false" style="background-color:#f5f5f5; cursor:not-allowed;">
      <input type="text" name="apellido_materno" required maxlength="50" placeholder="Apellido materno" class="box" value="<?= htmlspecialchars($fetch_profile['apellido_materno'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" readonly onkeydown="return false" onpaste="return false" style="background-color:#f5f5f5; cursor:not-allowed;">

      <input type="email" name="email" required maxlength="50" placeholder="Ingresa tu correo" class="box" value="<?= htmlspecialchars($fetch_profile['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
      <input type="text" name="telefono" maxlength="10" placeholder="Teléfono" class="box" value="<?= htmlspecialchars($fetch_profile['telefono'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
      <input type="number" name="edad" maxlength="3" placeholder="Edad" class="box" value="<?= htmlspecialchars($fetch_profile['edad'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

      <input type="password" name="old_pass" maxlength="20" placeholder="Contraseña actual" class="box">
      <input type="password" name="new_pass" maxlength="20" placeholder="Nueva contraseña" class="box">
      <input type="password" name="confirm_pass" maxlength="20" placeholder="Confirmar nueva contraseña" class="box">

      <p>Subir nueva imagen (opcional):</p>
      <input type="file" name="profile_picture" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">

      <input type="submit" value="Actualizar" name="submit" class="btn">
   </form>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>
<script>
function previewImage(event) {
   const reader = new FileReader();
   reader.onload = function () {
      const output = document.getElementById('preview');
      output.src = reader.result;
   };
   reader.readAsDataURL(event.target.files[0]);
}

// Ocultar alerta de contraseña tras 5 segundos
setTimeout(() => {
   const alertBox = document.getElementById('password-alert');
   if (alertBox) {
      alertBox.style.transition = "opacity 0.5s ease-out";
      alertBox.style.opacity = 0;
      setTimeout(() => alertBox.remove(), 500);
   }
}, 5000);
</script>

</body>
</html>
