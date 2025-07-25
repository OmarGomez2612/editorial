<?php
// Conexión a la base de datos
include 'components/connect.php';
session_start();

// Verificación de sesión activa
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

if (isset($_POST['submit'])) {

   $identifier = filter_var($_POST['identifier'], FILTER_SANITIZE_STRING);
   $password = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);
   $hashed_password = sha1($password);

   // Verificar si es administrador
   $select_admin = $conn->prepare("SELECT * FROM `admins` WHERE name = ?");
   $select_admin->execute([$identifier]);
   $admin = $select_admin->fetch(PDO::FETCH_ASSOC);

   if ($select_admin->rowCount() > 0 && $admin['password'] === $hashed_password) {
      $_SESSION['admin_id'] = $admin['id'];
      header('Location: admin/dashboard.php');
      exit;
   }

   // Verificar si es usuario normal
   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $select_user->execute([$identifier]);
   $user = $select_user->fetch(PDO::FETCH_ASSOC);

   if ($select_user->rowCount() == 0) {
      $message[] = 'EL CORREO ELECTRÓNICO NO ESTÁ REGISTRADO.';
   } else {
      if ($user['password'] === $hashed_password) {
         $_SESSION['user_id'] = $user['id'];

         if ($user['role_id'] == 1) {
            header('Location: admin/dashboard.php');
         } else if ($user['role_id'] == 2) {
            header('Location: home.php');
         }
         exit;
      } else {
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
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
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

   <?php include 'components/user_header.php'; ?>

   <section class="form-container">
      <form action="" method="post">
         <h3>INICIAR SESIÓN</h3>
         <a href="home.php" class="logo">
            <img src="images/logo.png" alt="Logo de la Editorial" style="max-width: 120px; height: auto;">
         </a>

         <input type="text" name="identifier" required placeholder="Ingrese su correo" maxlength="50" class="box" oninput="this.value = this.value.replace(/\s/g, '')">

         <div class="password-container">
            <input type="password" name="pass" id="password" required placeholder="Ingrese su contraseña" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
            <i class="fas fa-eye" id="togglePassword" style="cursor: pointer;"></i>
         </div>

         <input type="submit" value="INICIAR SESIÓN" class="btn" name="submit">

         <p>¿No tiene una cuenta?</p>
         <a href="user_register.php" class="option-btn">REGISTRAR AHORA</a>
      </form>
   </section>

   <?php
   if (isset($message)) {
      foreach ($message as $msg) {
         echo "<p class='error-msg'>$msg</p>";
      }
   }
   ?>

   <?php include 'components/footer.php'; ?>

   <script src="js/script.js"></script>
   <script>
      const togglePassword = document.querySelector("#togglePassword");
      const password = document.querySelector("#password");

      togglePassword.addEventListener("click", function () {
         const type = password.getAttribute("type") === "password" ? "text" : "password";
         password.setAttribute("type", type);
         this.classList.toggle("fa-eye-slash");
      });
   </script>

</body>
</html>
