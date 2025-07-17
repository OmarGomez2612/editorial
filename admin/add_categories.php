<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
   exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>DASHBOARD</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
   <link rel="stylesheet" href="../css/style.css">

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   
   
</head>
<body>
    <?php include '../components/admin_header.php'; ?>
    
    <section class="add-products">

        <h1 class="heading">AGREGAR CATEGORÍA</h1>
        <br><a href="dashboard.php" class="btn">VOLVER</a> <br>

        <form action="" method="post" enctype="multipart/form-data">
            <div class="inputBox">
                <span>Nombre de la Categoría</span>
                <input type="text" name="category_name" class="box" required placeholder="Nombre de la categoría">
            </div>

            <div class="inputBox">
                <span>Imagen de la Categoría</span>
                <input type="file" name="category_image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp" required>
            </div>

            <input type="submit" value="Agregar Categoría" class="btn" name="add_category">
        </form>

        <?php
        // Si el formulario de agregar categoría fue enviado
        if (isset($_POST['add_category'])) {
            // Recoger el nombre de la categoría
            $category_name = $_POST['category_name'];

            // Verificar si la categoría ya existe
            $check_category = $conn->prepare("SELECT * FROM `categories` WHERE `categorias` = ?");
            $check_category->execute([$category_name]);

            if ($check_category->rowCount() > 0) {
                echo '<p class="message">¡La categoría ya existe!</p>';
            } else {
                // Manejar la imagen subida
                $category_image = $_FILES['category_image']['name'];
                $category_image_tmp = $_FILES['category_image']['tmp_name'];
                $category_image_size = $_FILES['category_image']['size'];
                $category_image_ext = pathinfo($category_image, PATHINFO_EXTENSION);
                $category_image_folder = '../images/' . $category_name . '.' . $category_image_ext;

                // Verificar si el archivo es una imagen válida
                $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
                if (in_array(strtolower($category_image_ext), $allowed_ext)) {
                    // Verificar el tamaño de la imagen (limitar a 2 MB)
                    if ($category_image_size > 2000000) {
                        echo '<p class="message">¡La imagen es demasiado grande! Debe ser menor a 2 MB.</p>';
                    } else {
                        // Mover la imagen a la carpeta ../images/
                        if (move_uploaded_file($category_image_tmp, $category_image_folder)) {
                            // Insertar la nueva categoría en la base de datos
                            $insert_category = $conn->prepare("INSERT INTO `categories` (`categorias`, `image`) VALUES (?, ?)");
                            $insert_category->execute([$category_name, $category_name . '.' . $category_image_ext]);

                            if ($insert_category) {
                                echo '<p class="message">¡Categoría agregada correctamente!</p>';
                            } else {
                                echo '<p class="message">¡Error al agregar la categoría!</p>';
                            }
                        } else {
                            echo '<p class="message">¡Error al cargar la imagen!</p>';
                        }
                    }
                } else {
                    echo '<p class="message">¡Solo se permiten imágenes en formato JPG, JPEG, PNG o WEBP!</p>';
                }
            }
        }
        ?>

    </section>

    <section class="show-products">

   <h1 class="heading">CATEGORÍAS</h1>
   <div class="box-container">
      <?php
         // Ejecutamos la consulta para obtener las categorías
         $select_categories = $conn->prepare("SELECT * FROM `categories`");
         $select_categories->execute();

         // Verificamos si hay categorías
         if($select_categories->rowCount() > 0){
            // Si hay categorías, las mostramos
            while($fetch_category = $select_categories->fetch(PDO::FETCH_ASSOC)){ 
      ?>
      <div class="box">
         <img src="../images/<?= htmlspecialchars($fetch_category['image']); ?>" alt="Imagen de la categoría">
         <div class="name"><?= htmlspecialchars($fetch_category['categorias']); ?></div>
         <div class="category-id">ID: <?= htmlspecialchars($fetch_category['id']); ?></div>
      </div>
      <?php
            }
         } else {
            // Si no hay categorías, mostramos un mensaje
            echo '<p class="empty">¡No hay categorías disponibles!</p>';
         }
      ?>
   </div>

</section>
<script src="../js/admin_script.js"></script>
</body>
</html>


