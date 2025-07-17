<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
}

if(isset($_POST['submit'])){

   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $apellido_paterno = filter_var($_POST['apellido_paterno'], FILTER_SANITIZE_STRING);
   $apellido_materno = filter_var($_POST['apellido_materno'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
   $telefono = filter_var($_POST['telefono'], FILTER_SANITIZE_STRING);
   $edad = filter_var($_POST['edad'], FILTER_SANITIZE_NUMBER_INT);

   // Actualizar los datos del usuario
   $update_profile = $conn->prepare("UPDATE `users` SET name = ?, apellido_paterno = ?, apellido_materno = ?, email = ?, telefono = ?, edad = ? WHERE id = ?");
   $update_profile->execute([$name, $apellido_paterno, $apellido_materno, $email, $telefono, $edad, $user_id]);

   // Manejo de contraseña
   $prev_pass = $_POST['prev_pass'];
   $old_pass = sha1($_POST['old_pass']);
   $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);
   $new_pass = sha1($_POST['new_pass']);
   $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   if(!empty($_POST['old_pass']) || !empty($_POST['new_pass']) || !empty($_POST['cpass'])){
      if($old_pass != $prev_pass){
         $message[] = '¡LA CONTRASEÑA ACTUAL NO ES CORRECTA!';
      } elseif($new_pass != $cpass){
         $message[] = '¡LAS CONTRASEÑAS NUEVAS NO COINCIDEN!';
      } else {
         $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
         $update_pass->execute([$new_pass, $user_id]);
         $message[] = '¡CONTRASEÑA ACTUALIZADA CORRECTAMENTE!';
      }
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
   
   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <!-- Archivo CSS personalizado -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="form-container">

   <form action="" method="post">
      <h3>ACTUALIZAR PERFIL</h3>
      <input type="hidden" name="prev_pass" value="<?= $fetch_profile["password"]; ?>">
      
      <input type="text" name="name" required placeholder="Nombre(s)" maxlength="20" class="box" value="<?= $fetch_profile["name"]; ?>">
      <input type="text" name="apellido_paterno" required placeholder="Apellido Paterno" maxlength="20" class="box" value="<?= $fetch_profile["apellido_paterno"]; ?>">
      <input type="text" name="apellido_materno" required placeholder="Apellido Materno" maxlength="20" class="box" value="<?= $fetch_profile["apellido_materno"]; ?>">
      <input type="email" name="email" required placeholder="Correo Electrónico" maxlength="50" class="box" value="<?= $fetch_profile["email"]; ?>" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="text" name="telefono" required placeholder="Número de Teléfono" maxlength="15" class="box" value="<?= $fetch_profile["telefono"]; ?>" pattern="^\+?\d{1,4}?[\d\s\-\(\)]{7,15}$">
      <input type="number" name="edad" required placeholder="Edad" class="box" min="18" max="120" value="<?= $fetch_profile["edad"]; ?>">

      <input type="password" name="old_pass" placeholder="Contraseña Actual" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="new_pass" placeholder="Nueva Contraseña" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" placeholder="Confirmar Nueva Contraseña" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">

      <input type="submit" value="ACTUALIZAR" class="btn" name="submit">
   </form>

</section>

<script src="js/script.js"></script>

</body>
</html>












<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>