<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
};

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODUCTO</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>

    <?php include '../components/admin_header.php'; ?>

    <section class="show-products">

        <h1 class="heading">AUDIOLIBROS AGREGADOS</h1>

        <div class="box-container">

            <?php
            // Ejecutamos la consulta con JOIN para obtener la categoría
            $select_products = $conn->prepare("
               SELECT p.*, c.categorias 
               FROM products p
               LEFT JOIN categories c ON p.category_id = c.id
               WHERE p.type_id = 2
            ");
            $select_products->execute();
            if ($select_products->rowCount() > 0) {
                while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
            ?>
                    <div class="box">
                        <img src="../audiolibros/<?= htmlspecialchars($fetch_products['book_folder'] . '/' . $fetch_products['image_01']); ?>" alt="Imagen del libro">
                        <div class="name"><?= $fetch_products['name']; ?></div>
                        <div class="author"><?= $fetch_products['author']; ?></div>
                        <div class="price">
                            <?php if ($fetch_products['price'] == 0) : ?>
                                <span style="color: green; font-weight: bold;">GRATIS</span>
                            <?php else : ?>
                                $<span><?= $fetch_products['price']; ?></span> MXN
                            <?php endif; ?>
                        </div>
                        <div class="details"><span><?= $fetch_products['details']; ?></span></div>
                        <div class="audio-link">
                        <?php
                        // Verificar si el campo 'audio' existe y no está vacío
                        if (isset($fetch_products['AUDIO']) && !empty($fetch_products['AUDIO'])) {
                            // Obtener la ruta completa al archivo de audio dentro de la carpeta 'audiolibros'
                            $audio_path = '../audiolibros/' . $fetch_products['book_folder'] . '/' . $fetch_products['AUDIO'];
                            
                            // Verificar si el archivo de audio existe
                            if (file_exists($audio_path)) {
                                echo '<a href="' . $audio_path . '" target="_blank">ESCUCHAR AUDIO</a>';
                            } else {
                                echo '<span>Archivo de audio no disponible</span>';
                            }
                        } else {
                            echo '<span>Sin archivo de audio</span>';
                        }
                        ?>
                        </div>



                        <div class="flex-btn">
                            <!--<a href="update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">ACTUALIZAR</a>-->
                            <a href="products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('¿Eliminar este producto?');">ELIMINAR</a>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">¡NO SE HAN AGREGADO AUDIOLIBROS!</p>';
            }
            ?>

        </div>

    </section>

    <script src="../js/admin_script.js"></script>

</body>

</html>
