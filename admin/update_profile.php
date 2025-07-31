<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

// Obtener datos actuales del perfil
$fetch_profile = $conn->prepare("SELECT * FROM `admins` WHERE id = ?");
$fetch_profile->execute([$admin_id]);
$existing_profile = $fetch_profile->fetch(PDO::FETCH_ASSOC);

if(isset($_POST['submit'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);

   $update_profile_name = $conn->prepare("UPDATE `admins` SET name = ? WHERE id = ?");
   $update_profile_name->execute([$name, $admin_id]);

   // Actualizar contraseña solo si se ha llenado el campo anterior
   if(!empty($_POST['old_pass'])){
      $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
      $prev_pass = $_POST['prev_pass'];
      $old_pass = sha1($_POST['old_pass']);
      $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);
      $new_pass = sha1($_POST['new_pass']);
      $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
      $confirm_pass = sha1($_POST['confirm_pass']);
      $confirm_pass = filter_var($confirm_pass, FILTER_SANITIZE_STRING);

      if($old_pass == $empty_pass){
         $message[] = '¡Por favor, ingresa tu contraseña anterior!';
      }elseif($old_pass != $prev_pass){
         $message[] = '¡La contraseña anterior no coincide!';
      }elseif($new_pass != $confirm_pass){
         $message[] = '¡La contraseña de confirmación no coincide!';
      }else{
         if($new_pass != $empty_pass){
            $update_admin_pass = $conn->prepare("UPDATE `admins` SET password = ? WHERE id = ?");
            $update_admin_pass->execute([$confirm_pass, $admin_id]);
            $message[] = '¡Contraseña actualizada con éxito!';
         }else{
            $message[] = '¡Por favor, ingresa una nueva contraseña!';
         }
      }
   }

   // Actualizar foto de perfil si se selecciona una nueva
   if (isset($_FILES['profile']) && $_FILES['profile']['error'] == 0) {
      $profile_name = $_FILES['profile']['name'];
      $profile_tmp = $_FILES['profile']['tmp_name'];
      $profile_folder = '../uploaded_profiles/' . $profile_name;

      if (!empty($existing_profile['profile_picture']) && file_exists($existing_profile['profile_picture'])) {
         unlink($existing_profile['profile_picture']);
      }

      move_uploaded_file($profile_tmp, $profile_folder);

      $update_photo = $conn->prepare("UPDATE `admins` SET profile_picture = ? WHERE id = ?");
      $update_photo->execute([$profile_folder, $admin_id]);
   }

}

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>ACTUALIZAR PERFIL</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="form-container">

   <form action="" method="post" enctype="multipart/form-data">
      <h3>ACTUALIZAR PERFIL</h3>
      <input type="hidden" name="prev_pass" value="<?= $existing_profile['password']; ?>">
      <input type="text" name="name" value="<?= $existing_profile['name']; ?>" required placeholder="Ingresa tu nombre de usuario" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="old_pass" placeholder="Ingresa la contraseña anterior (opcional)" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="new_pass" placeholder="Ingresa la nueva contraseña" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="confirm_pass" placeholder="Confirma la nueva contraseña" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">

      <h3 for="profile">Actualizar foto de perfil (opcional):</h3>
      <input type="file" name="profile" id="profile" accept="image/*" class="box">
      <img id="preview" src="" alt="Vista previa" style="display:none; max-width:100px; margin-top:10px; border-radius:50%;">

      <input type="submit" value="ACTUALIZAR AHORA" class="btn" name="submit">
   </form>

</section>

<script src="../js/admin_script.js"></script>

<script>
document.getElementById("profile").addEventListener("change", function () {
   const file = this.files[0];
   const preview = document.getElementById("preview");
   if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
         preview.src = e.target.result;
         preview.style.display = "block";
      };
      reader.readAsDataURL(file);
   } else {
      preview.src = "";
      preview.style.display = "none";
   }
});
</script>

</body>
</html>
