<?php
include 'components/connect.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

// Verificamos si se ha pasado un ID en la URL
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];  // Obtener el ID del libro desde la URL

    // Consulta para obtener la información del producto con el product_id
    $select_product = $conn->prepare("
        SELECT p.book_folder, p.name, p.author, p.publisher, p.details
        FROM products p
        WHERE p.id = ? AND EXISTS (
            SELECT 1 FROM payment_items pi 
            JOIN payments pay ON pi.payment_id = pay.payment_id
            WHERE pi.product_id = p.id AND pay.user_id = ?
        )");  // Aseguramos que el libro haya sido comprado por el usuario

    $select_product->execute([$product_id, $user_id]);

    // Verificamos si el producto fue encontrado
    if ($select_product->rowCount() > 0) {
        $fetch_product = $select_product->fetch(PDO::FETCH_ASSOC);

        // Obtener el 'book_folder' y el 'name' del producto
        $book_folder = $fetch_product['book_folder'];
        $book_name = $fetch_product['name'];

        // Construir la ruta del archivo HTML
        $html_file_path = 'libros/' . htmlspecialchars($book_folder) . '/' . htmlspecialchars($book_name) . '/';
    } else {
        echo '<p class="empty">No se encontró el libro o no has comprado este libro.</p>';
    }
} else {
    echo '<p class="empty">No se ha especificado un libro.</p>';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LEER LIBRO</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>
    <?php include 'components/user_header.php'; ?>

    <?php if (isset($html_file_path)) { ?>
        <!-- Mostrar el archivo HTML en un iframe -->
        <iframe src="<?= $html_file_path; ?>" width="100%" height="1000px"></iframe>
    <?php } ?>

    <?php include 'components/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
