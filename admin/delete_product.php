<?php
    include '../components/connect.php';

    session_start();

    $admin_id = $_SESSION['admin_id'];

    if (!isset($admin_id)) {
    header('location:admin_login.php');
    };

    if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];

    // Obtener el libro y sus archivos
    $select_product = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $select_product->execute([$product_id]);
    $product = $select_product->fetch(PDO::FETCH_ASSOC);

    // Eliminar los archivos del libro
    $book_folder_path = '../libros/' . $product['book_folder'];

    // Verificar si la carpeta del libro existe
    if (is_dir($book_folder_path)) {
        $files = glob($book_folder_path . '/*'); // Obtener todos los archivos dentro de la carpeta

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file); // Eliminar archivo
            }
        }

        rmdir($book_folder_path); // Eliminar la carpeta del libro
    }

    // Eliminar el producto de la base de datos
    $delete_product = $conn->prepare("DELETE FROM products WHERE id = ?");
    $delete_product->execute([$product_id]);

    // Verificar si la eliminación fue exitosa
    if ($delete_product) {
        header('location:products.php');
        exit;
    } else {
        echo 'Error al eliminar el libro.';
    }
    } else {
    header('location:products.php');
    exit;
    }

    // Después de eliminar el producto, redirigir a la página principal de productos
    if ($delete_product) {
        // Mensaje de éxito para la eliminación
        $_SESSION['message'] = '¡Libro eliminado exitosamente!';
        header('location: admin_products.php');  // Ajusta la redirección según sea necesario
        exit();
    } else {
        // Mensaje de error si no se pudo eliminar
        $_SESSION['message'] = 'Error al eliminar el libro.';
        header('location: admin_products.php');
        exit();
    }
?>
