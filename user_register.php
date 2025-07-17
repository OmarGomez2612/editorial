<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if(isset($_POST['submit'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   
   $apellido_paterno = $_POST['apellido_paterno'];
   $apellido_paterno = filter_var($apellido_paterno, FILTER_SANITIZE_STRING);
   
   $apellido_materno = $_POST['apellido_materno'];
   $apellido_materno = filter_var($apellido_materno, FILTER_SANITIZE_STRING);

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   $edad = $_POST['edad'];
   $edad = filter_var($edad, FILTER_SANITIZE_NUMBER_INT);

   $telefono = $_POST['telefono'];
   $telefono = filter_var($telefono, FILTER_SANITIZE_STRING);

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $select_user->execute([$email]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);

   if($select_user->rowCount() > 0){
      $message[] = 'EL CORREO ELECTRONICO YA ESTÁ REGISTRADO!';
   }else{
      if($pass != $cpass){
         $message[] = 'LAS CONTRASEÑAS NO COINCIDEN!';
      }else{
         $insert_user = $conn->prepare("INSERT INTO `users` (name, apellido_paterno, apellido_materno, email, password, telefono, edad, role_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
         $insert_user->execute([$name, $apellido_paterno, $apellido_materno, $email, $pass, $telefono, $edad, 2]);


         // Almacenar el mensaje en la sesión
         $_SESSION['registration_message'] = 'USUARIO REGISTRADO CORRECTAMENTE, INICIA SESION.';

         // Mostrar el mensaje y redirigir después de 3 segundos
         echo '<script>
            alert("USUARIO REGISTRADO CORRECTAMENTE, PORFAVOR INICIA SESION");
            setTimeout(function(){
               window.location.href = "user_login.php";
            }, 2000);
         </script>';
         exit;
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
   <title>REGISTRO</title>
   
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

   <style>
      /* Estilo adicional para alinear el ícono dentro del input */
      .password-container {
         position: relative;
      }
      .password-container .box {
         padding-right: 30px; /* Espacio para el ícono */
      }
      .password-container i {
         position: absolute;
         right: 10px;
         top: 50%;
         transform: translateY(-50%);
         cursor: pointer;
      }
   </style>

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="form-container">

<form action="" method="post">
<h3>REGÍSTRATE AHORA</h3>
<a href="home.php" class="logo">
         <img src="images/logo.png" alt="Logo de la Editorial" style="max-width: 120px; height: auto;">
      </a>

<input type="text" name="name" required placeholder="Ingrese su nombre(s)" maxlength="20" class="box">
<input type="text" name="apellido_paterno" required placeholder="Ingrese su apellido paterno" maxlength="20" class="box">
<input type="text" name="apellido_materno" required placeholder="Ingrese su apellido materno" maxlength="20" class="box">
<input type="email" name="email" required placeholder="Ingrese su correo electrónico" maxlength="50" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
<div class="password-container">
   <input type="password" name="pass" id="password" required placeholder="Ingrese su contraseña" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
   <i class="fas fa-eye" id="togglePassword"></i>
</div>
<div class="password-container">
   <input type="password" name="cpass" id="confirmPassword" required placeholder="Confirme su contraseña" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
   <i class="fas fa-eye" id="toggleConfirmPassword"></i>
</div>
<input type="tel" name="telefono" required placeholder="Ingrese su número de teléfono" maxlength="10" class="box" pattern="\d{10}" title="Ingrese exactamente 10 dígitos">
<input type="edad" name="edad" required placeholder="Ingrese su edad" class="box" min="18" max="120">

   <input type="submit" value="ÚNETE AHORA" class="btn" name="submit">
   <p>¿Ya tienes cuenta?</p>
   <a href="user_login.php" class="option-btn">INICIAR SESIÓN</a>
</form>

<script src="js/script.js"></script>

<script>
   // Alternar visibilidad de la contraseña
   const togglePassword = document.querySelector("#togglePassword");
   const password = document.querySelector("#password");

   const toggleConfirmPassword = document.querySelector("#toggleConfirmPassword");
   const confirmPassword = document.querySelector("#confirmPassword");

   togglePassword.addEventListener("click", function () {
      // Alternar el tipo de entrada
      const type = password.getAttribute("type") === "password" ? "text" : "password";
      password.setAttribute("type", type);

      // Alternar el ícono
      this.classList.toggle("fa-eye-slash");
   });

   toggleConfirmPassword.addEventListener("click", function () {
      // Alternar el tipo de entrada
      const type = confirmPassword.getAttribute("type") === "password" ? "text" : "password";
      confirmPassword.setAttribute("type", type);

      // Alternar el ícono
      this.classList.toggle("fa-eye-slash");
   });
</script>

</body>
</html>
