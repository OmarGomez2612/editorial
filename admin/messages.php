<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
};

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_message = $conn->prepare("DELETE FROM `messages` WHERE id = ?");
   $delete_message->execute([$delete_id]);
   header('location:messages.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Mensajes</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">

   <style>
      body {
         font-family: 'Arial', sans-serif;
         background: #f4f4f4;
         margin: 0;
         padding: 0;
      }

      .contacts {
         padding: 20px;
         background: #fff;
         box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
         border-radius: 10px;
         margin: 20px;
      }

      .contacts h1.heading {
         text-align: center;
         font-size: 2rem;
         color: #333;
         margin-bottom: 20px;
      }

      .contacts .box-container {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
         gap: 20px;
      }

      .contacts .box {
         background: #fff;
         border: 1px solid #ddd;
         padding: 20px;
         border-radius: 10px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
         transition: 0.3s;
      }

      .contacts .box:hover {
         box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      }

      .contacts .box p {
         margin: 10px 0;
         font-size: 1rem;
         color: #555;
         display: flex;
         align-items: center;
      }

      .contacts .box p span {
         font-weight: bold;
         color: #333;
         margin-left: 10px;
      }

      .contacts .box p i {
         color: #007bff;
         margin-right: 10px;
      }

      .contacts .box .delete-btn {
         display: inline-block;
         padding: 10px 15px;
         color: #fff;
         background: #ff4d4d;
         text-decoration: none;
         border-radius: 5px;
         text-align: center;
         transition: 0.3s;
      }

      .contacts .box .delete-btn:hover {
         background: #e60000;
      }

      .empty {
         text-align: center;
         font-size: 1.2rem;
         color: #888;
         margin-top: 20px;
      }

      .search-bar {
         margin-bottom: 20px;
         display: flex;
         justify-content: center;
      }

      .search-bar input {
         width: 100%;
         max-width: 400px;
         padding: 10px;
         border: 1px solid #ddd;
         border-radius: 5px;
         font-size: 1rem;
         outline: none;
      }

      .search-bar input:focus {
         border-color: #007bff;
      }
   </style>

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="contacts">



<div class="box-container">

   <?php
      $select_messages = $conn->prepare("SELECT * FROM `messages`");
      $select_messages->execute();
      if($select_messages->rowCount() > 0){
         while($fetch_message = $select_messages->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p><i class="fas fa-user"></i> ID Usuario: <span><?= $fetch_message['user_id']; ?></span></p>
      <p><i class="fas fa-user-circle"></i> Nombre: <span><?= $fetch_message['name']; ?></span></p>
      <p><i class="fas fa-envelope"></i> Correo: <span><?= $fetch_message['email']; ?></span></p>
      <p><i class="fas fa-phone"></i> Teléfono: <span><?= $fetch_message['number']; ?></span></p>
      <p><i class="fas fa-comment"></i> Mensaje: <span><?= $fetch_message['message']; ?></span></p>
      <a href="messages.php?delete=<?= $fetch_message['id']; ?>" onclick="return confirm('¿Estás seguro de eliminar este mensaje?');" class="delete-btn">Eliminar</a>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">NO HAY MENSAJES DISPONIBLES</p>';
      }
   ?>

</div>

</section>

<script src="../js/admin_script.js"></script>
   
</body>
</html>
