<?php

include '../components/connect.php';
session_start();

// Verificar si ya hay sesión activa para el administrador
if (!isset($_SESSION['admin_id'])) {
   header('location:admin_login.php');
   exit();
}

// Verificar si se envió el formulario
if (isset($_POST['submit'])) {

   // Filtrar y sanitizar datos de entrada
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']); // Encriptación de la contraseña
   $cpass = sha1($_POST['cpass']); // Confirmar la contraseña

   // Verificar que los campos no estén vacíos
   if (empty($name) || empty($pass) || empty($cpass)) {
      $message[] = 'Todos los campos son obligatorios.';
   } else {
      // Verificar que el nombre de usuario no exista ya
      $select_admin = $conn->prepare("SELECT * FROM `admins` WHERE name = ?");
      $select_admin->execute([$name]);

      if ($select_admin->rowCount() > 0) {
         $message[] = 'El nombre de usuario ya existe. Por favor, elija otro.';
      } else {
         // Verificar que las contraseñas coincidan
         if ($pass != $cpass) {
            $message[] = 'Las contraseñas no coinciden.';
         } else {
            // Insertar el nuevo administrador en la base de datos
            $insert_admin = $conn->prepare("INSERT INTO `admins`(name, password) VALUES(?, ?)");
            $insert_admin->execute([$name, $cpass]);
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
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>NUEVO ADMINISTRADOR</title>

   <!-- Enlace a FontAwesome para iconos -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Enlace al archivo CSS personalizado -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="form-container">
   <form action="" method="post">
      <h3>Registrar nuevo administrador</h3>
      
      <!-- Nombre de usuario -->
      <input type="text" name="name" required placeholder="Ingrese su nombre de usuario" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">

      <!-- Contraseña -->
      <input type="password" name="pass" required placeholder="Ingrese su contraseña" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">

      <!-- Confirmar contraseña -->
      <input type="password" name="cpass" required placeholder="Confirme su contraseña" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      
      <!-- Botón para enviar el formulario -->
      <input type="submit" value="REGISTAR AHORA" class="btn" name="submit">
   </form>
</section>

<?php
// Mostrar mensajes de estado si existen
if (isset($message)) {
   foreach ($message as $msg) {
      echo '<p class="message">' . htmlspecialchars($msg) . '</p>';
   }
}
?>

<script src="../js/admin_script.js"></script>

</body>
</html>
