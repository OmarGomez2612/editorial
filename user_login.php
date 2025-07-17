<?php
// Incluir archivo de conexión a la base de datos
include 'components/connect.php';

// Iniciar sesión
session_start();

// Verificar si el usuario ya está logueado
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

// Procesar el formulario de inicio de sesión
if (isset($_POST['submit'])) {

   // Sanitizar y filtrar los datos ingresados
   $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
   $password = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);
   $hashed_password = sha1($password);

   // Verificar si el correo electrónico existe en la base de datos
   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $select_user->execute([$email]);
   $user = $select_user->fetch(PDO::FETCH_ASSOC);

   // Si el correo electrónico no existe
   if ($select_user->rowCount() == 0) {
      $message[] = 'EL CORREO ELECTRÓNICO NO ESTÁ REGISTRADO.';
   } else {
      // Si el correo electrónico existe, verificar la contraseña
      if ($user['password'] === $hashed_password) {
         // Si las credenciales son correctas, verificar el role_id
         $_SESSION['user_id'] = $user['id'];

         // Verificar el role_id y redirigir según el valor
         if ($user['role_id'] == 1) {
            // Redirigir a la página para administradores
            header('Location: admin/dashboard.php');
         } else if ($user['role_id'] == 2) {
            // Redirigir a la página principal para usuarios normales
            header('Location: home.php');
         }
         exit;
      } else {
         // Si la contraseña es incorrecta
         $message[] = 'LA CONTRASEÑA ES INCORRECTA, INTENTE NUEVAMENTE.';
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
   <title>INICIAR SESIÓN</title>

   <!-- Enlace a Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Enlace al archivo CSS personalizado -->
   <link rel="stylesheet" href="css/style.css">

   <!-- Estilos adicionales para la alineación del ícono dentro del campo de contraseña -->
   <style>
      .password-container {
         position: relative;
      }

      .password-container .box {
         padding-right: 30px;
      }

      .password-container i {
         position: absolute;
         right: 10px;
         top: 50%;
         transform: translateY(-50%);
      }
   </style>

</head>

<body>

   <!-- Incluir el encabezado del usuario -->
   <?php include 'components/user_header.php'; ?>

   <section class="form-container">

      <form action="" method="post">
         <h3>INICIAR SESIÓN</h3>

         <a href="home.php" class="logo">
            <img src="images/logo.png" alt="Logo de la Editorial" style="max-width: 120px; height: auto;">
         </a>

         <!-- Campo para correo electrónico -->
         <input type="email" name="email" required placeholder="Ingrese su correo electrónico" maxlength="50" class="box" oninput="this.value = this.value.replace(/\s/g, '')">

         <!-- Campo para contraseña -->
         <div class="password-container">
            <input type="password" name="pass" id="password" required placeholder="Ingrese su contraseña" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
            <i class="fas fa-eye" id="togglePassword" style="cursor: pointer;"></i>
         </div>

         <!-- Botón de envío -->
         <input type="submit" value="INICIAR SESIÓN" class="btn" name="submit">

         <!-- Enlace para registro de usuario -->
         <p>¿No tiene una cuenta?</p>
         <a href="user_register.php" class="option-btn">REGISTRAR AHORA</a>
      </form>

   </section>

   <!-- Mostrar mensaje de error si existe -->
   <?php
   if (isset($message)) {
      foreach ($message as $msg) {
         echo "<p class='error-msg'>$msg</p>";
      }
   }
   ?>

   <!-- Incluir el pie de página -->
   <?php include 'components/footer.php'; ?>

   <!-- Enlace al archivo JavaScript -->
   <script src="js/script.js"></script>
   <script>
      const togglePassword = document.querySelector("#togglePassword");
      const password = document.querySelector("#password");

      togglePassword.addEventListener("click", function (e) {
         // Alternar el tipo de entrada
         const type = password.getAttribute("type") === "password" ? "text" : "password";
         password.setAttribute("type", type);

         // Alternar el ícono
         this.classList.toggle("fa-eye-slash");
      });
   </script>

</body>

</html>
