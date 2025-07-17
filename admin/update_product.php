<?php
include '../components/connect.php';

if (isset($_GET['update'])) {
    $product_id = $_GET['update'];

    // Consultar el producto actual desde la base de datos
    $select_product = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $select_product->execute([$product_id]);
    $product = $select_product->fetch(PDO::FETCH_ASSOC);
    
    // Si no se encuentra el producto, redirigir
    if (!$product) {
        echo "Producto no encontrado";
        exit;
    }
}


if (isset($_POST['update_product'])) {
    // Obtener los datos del formulario
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $author = $_POST['author'];
    $price = $_POST['price'];
    $publisher = $_POST['publisher'];
    $details = $_POST['details'];
    $category_id = $_POST['category_id'];
    $type_id = $_POST['type_id'];

    // Obtener el producto actual de la base de datos
    $select_product = $conn->prepare("SELECT * FROM products WHERE id = :id");
    $select_product->bindParam(':id', $product_id);
    $select_product->execute();
    $product = $select_product->fetch(PDO::FETCH_ASSOC);

    // Revisar si el nombre del libro cambió
    $new_folder_name = $name . '_' . time();  // Nuevo nombre para la carpeta con timestamp

    // Verificar si el nombre del libro cambió
    if ($name !== $product['name']) {
        // Usar los nombres actuales para construir las rutas de las carpetas
        $old_folder = "../libros/" . $product['book_folder']; // Carpeta principal
        $old_subfolder = $old_folder . '/' . $product['name']; // Subcarpeta con el nombre antiguo
        $new_folder = "../libros/" . $new_folder_name;
        $new_subfolder = $new_folder . '/' . $name; // Nueva subcarpeta con el nuevo nombre

        // Mostrar las rutas para depuración
        echo "Ruta antigua de la subcarpeta: $old_subfolder<br>";
        echo "Ruta antigua de la carpeta: $old_folder<br>";
        echo "Ruta nueva de la carpeta: $new_folder<br>";
        echo "Ruta nueva de la subcarpeta: $new_subfolder<br>";

        // Comprobamos si la subcarpeta realmente existe antes de renombrarla
        if (!file_exists($old_subfolder)) {
            echo "La subcarpeta no existe. No se puede renombrar.<br>";
            exit();
        }

        // Intentamos renombrar la subcarpeta (escapando los espacios y caracteres especiales)
        echo "Intentando renombrar la subcarpeta...<br>";
        if (!rename($old_subfolder, $new_subfolder)) {
            echo "Error al renombrar la subcarpeta.<br>";
            exit();
        } else {
            echo "Subcarpeta renombrada correctamente.<br>";
        }

        // Luego renombramos la carpeta principal (que tiene el timestamp)
        echo "Intentando renombrar la carpeta principal...<br>";
        if (!rename($old_folder, $new_folder)) {
            echo "Error al renombrar la carpeta principal.<br>";
            exit();
        } else {
            echo "Carpeta principal renombrada correctamente.<br>";
        }

        // Actualizar el nombre de la carpeta en la base de datos con el nuevo nombre
        $update_folder_query = $conn->prepare("UPDATE products SET book_folder = :book_folder WHERE id = :id");
        $update_folder_query->bindParam(':book_folder', $new_folder_name);
        $update_folder_query->bindParam(':id', $product_id);
        $update_folder_query->execute();
    } else {
        // Si el nombre del libro no cambia, mantenemos la misma carpeta
        $new_folder_name = $product['book_folder'];
    }

    // Procesar las imágenes
    $image_01 = $product['image_01']; // Mantener la imagen si no se cambia
    $image_02 = $product['image_02'];

    if (isset($_FILES['image_01']) && $_FILES['image_01']['error'] == 0) {
        $image_01 = time() . '_' . $_FILES['image_01']['name'];
        move_uploaded_file($_FILES['image_01']['tmp_name'], $new_folder . '/portada/' . $image_01);
    }

    if (isset($_FILES['image_02']) && $_FILES['image_02']['error'] == 0) {
        $image_02 = time() . '_' . $_FILES['image_02']['name'];
        move_uploaded_file($_FILES['image_02']['tmp_name'], $new_folder . '/contraportada/' . $image_02);
    }

    // Procesar el audio (si es un audiolibro)
    $audio = $product['audio']; // Mantener el audio si no se cambia
    if ($type_id == 2 && isset($_FILES['audio']) && $_FILES['audio']['error'] == 0) {
        $audio = time() . '_' . $_FILES['audio']['name'];
        move_uploaded_file($_FILES['audio']['tmp_name'], $new_folder . '/audio/' . $audio);
    }

    // Actualizar los demás campos del producto
    $update_product_query = $conn->prepare("
        UPDATE products 
        SET name = :name, author = :author, price = :price, publisher = :publisher, 
        details = :details, category_id = :category_id, type_id = :type_id, 
        image_01 = :image_01, image_02 = :image_02, audio = :audio
        WHERE id = :id
    ");
    $update_product_query->bindParam(':name', $name);
    $update_product_query->bindParam(':author', $author);
    $update_product_query->bindParam(':price', $price);
    $update_product_query->bindParam(':publisher', $publisher);
    $update_product_query->bindParam(':details', $details);
    $update_product_query->bindParam(':category_id', $category_id);
    $update_product_query->bindParam(':type_id', $type_id);
    $update_product_query->bindParam(':image_01', $image_01);
    $update_product_query->bindParam(':image_02', $image_02);
    $update_product_query->bindParam(':audio', $audio);
    $update_product_query->bindParam(':id', $product_id);
    $update_product_query->execute();

    $_SESSION['message'] = "Producto actualizado correctamente.";
    header('Location: update_product.php?update=' . $product_id);
}





?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Producto</title>
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
    <?php
    if (isset($_SESSION['message'])) {
        echo '<div class="message">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']);
    }
    ?>

    <?php include '../components/admin_header.php'; ?>

    <section class="update-product">
        <h1 class="heading">Actualizar Libro</h1>

        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">

            <div class="flex">
                <div class="inputBox">
                    <span>Título del Libro</span>
                    <input type="text" class="box" required maxlength="100" value="<?= $product['name']; ?>" name="name">
                </div>
                <div class="inputBox">
                    <span>Autor</span>
                    <input type="text" class="box" required maxlength="100" value="<?= $product['author']; ?>" name="author">
                </div>
                <div class="inputBox">
                    <span>Precio</span>
                    <input type="text" class="box" required maxlength="100" value="<?= $product['price']; ?>" name="price">
                </div>
                <div class="inputBox">
                    <span>Editorial</span>
                    <input type="text" class="box" required maxlength="100" value="<?= $product['publisher']; ?>" name="publisher">
                </div>
                <div class="inputBox">
                    <span>Imagen 1 (Portada)</span>
                    <input type="file" name="image_01" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
                    <?php if ($product['image_01']) { ?>
                        <img src="../libros/<?= $product['book_folder'] . '/' . $product['image_01']; ?>" alt="Portada" style="width: 100px;">
                    <?php } ?>
                </div>
                <div class="inputBox">
                    <span>Imagen 2 (Contraportada)</span>
                    <input type="file" name="image_02" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
                    <?php if ($product['image_02']) { ?>
                        <img src="../libros/<?= $product['book_folder'] . '/' . $product['image_02']; ?>" alt="Contraportada" style="width: 100px;">
                    <?php } ?>
                </div>
                <div class="inputBox">
                    <span>Detalles</span>
                    <textarea name="details" placeholder="Ingresa los detalles del libro" class="box" required maxlength="500" cols="30" rows="10"><?= $product['details']; ?></textarea>
                </div>
                <div class="inputBox">
                    <span>Categoría</span>
                    <select name="category_id" class="box" required>
                        <option value="">Selecciona una categoría</option>
                        <?php
                        $select_categories = $conn->prepare("SELECT * FROM categories");
                        $select_categories->execute();
                        while ($fetch_categories = $select_categories->fetch(PDO::FETCH_ASSOC)) {
                            $selected = ($fetch_categories['id'] == $product['category_id']) ? 'selected' : '';
                            echo '<option value="' . $fetch_categories['id'] . '" ' . $selected . '>' . $fetch_categories['categorias'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="inputBox">
                    <span>Tipo de Producto</span>
                    <select name="type_id" class="box" required>
                        <option value="">Selecciona un tipo de producto</option>
                        <?php
                        $select_types = $conn->prepare("SELECT * FROM type_products");
                        $select_types->execute();
                        while ($fetch_types = $select_types->fetch(PDO::FETCH_ASSOC)) {
                            $selected = ($fetch_types['id'] == $product['type_id']) ? 'selected' : '';
                            echo '<option value="' . $fetch_types['id'] . '" ' . $selected . '>' . $fetch_types['types'] . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <!-- Mostrar el campo para actualizar audio solo si es un audiolibro -->
                <?php if ($product['type_id'] == 2) { // Suponiendo que '2' es el tipo de audiolibro ?>
                    <div class="inputBox">
                        <span>Actualizar Audio (Solo Audiolibro)</span>
                        <input type="file" name="audio" accept="audio/mpeg, audio/wav" class="box">
                        <?php if ($product['audio']) { ?>
                            <audio controls>
                                <source src="../libros/<?= $product['book_folder'] . '/' . $product['audio']; ?>" type="audio/mp3">
                                Tu navegador no soporta el elemento de audio.
                            </audio>
                        <?php } ?>
                    </div>
                <?php } ?>

                <!-- Campo para actualizar la carpeta (solo si cambia el nombre del libro) -->
                <div class="inputBox">
                    <span>Actualizar Carpeta</span>
                    <input type="text" class="box" value="<?= $product['book_folder']; ?>" name="book_folder" disabled>
                    <small>La carpeta solo se actualizará si el nombre del libro cambia.</small>
                </div>
            </div>

            <input type="submit" value="Actualizar Producto" class="btn" name="update_product">
        </form>
    </section>

    <script src="../js/admin_script.js"></script>
</body>
</html>

