
<?php
include 'components/connect.php';
include 'components/user_header.php';
session_start();

// Verificamos si se ha pasado un ID en la URL
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];  // Obtener el ID del producto desde la URL

    // Consulta para obtener la información del producto con el product_id
    $select_product = $conn->prepare("
        SELECT p.book_folder, p.name, p.author, p.publisher, p.details, p.price, p.AUDIO, p.type_id
        FROM products p
        WHERE p.id = ? AND (
            EXISTS (
                SELECT 1 FROM payment_items pi 
                JOIN payments pay ON pi.payment_id = pay.payment_id
                WHERE pi.product_id = p.id AND pay.user_id = ? 
            ) OR p.price = 0
        )");  // Aseguramos que el libro haya sido comprado por el usuario o que tenga precio 0

    // Verificamos si el usuario está autenticado
    if (isset($_SESSION['user_id'])) {
        // Si el usuario está autenticado, incluimos su ID en la consulta
        $select_product->execute([$product_id, $_SESSION['user_id']]);
    } else {
        // Si el usuario no está autenticado, ejecutamos la consulta solo verificando el precio 0
        $select_product->execute([$product_id, 0]);
    }

    // Verificamos si el producto fue encontrado
    if ($select_product->rowCount() > 0) {
        $fetch_product = $select_product->fetch(PDO::FETCH_ASSOC);
        $book_folder = $fetch_product['book_folder'];
        $book_name = $fetch_product['name'];
        $audio_file = $fetch_product['AUDIO'];
        $type_id = $fetch_product['type_id'];
        $price = $fetch_product['price'];

        // Verificar si es un audiolibro (type_id = 2) y si existe un archivo de audio
        if ($type_id == 2 && $audio_file) {
            $audio_file_path = 'audiolibros/' . htmlspecialchars($book_folder) . '/' . htmlspecialchars($audio_file);

            // Guardar en la sesión que el usuario está reproduciendo este audiolibro
            $_SESSION['audio_playing'] = true;
            $_SESSION['audio_file_path'] = $audio_file_path;
            $_SESSION['audio_book_name'] = $book_name;

        } else {
            echo '<p class="empty">Este producto no es un audiolibro o no tiene archivo de audio disponible.</p>';
        }
    } else {
        echo '<p class="empty">No se encontró el audiolibro o no has comprado este producto.</p>';
    }
} else {
    echo '<p class="empty">No se ha especificado un audiolibro.</p>';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REPRODUCIR AUDIOLIBRO</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        /* Estilos solo para el reproductor de audio */
        audio {
            width: 100%;
            max-width: 400px;
            border: 2px solid #000;
            border-radius: 5px;
            margin-top: 20px;
            background-color: #fff; /* Fondo blanco */
        }

        /* Estilo para el contenedor del reproductor */
        .container {
            width: 100%;
            height: 100vh; /* Utilizamos el 100% de la altura de la pantalla */
            display: flex;
            flex-direction: column;
            align-items: center; /* Centra el contenido horizontalmente */
            text-align: center;
        }

        /* Estilo de mensaje vacío (error) */
        .empty {
            color: #f00;
            font-size: 1.2rem;
            margin-top: 20px;
        }

        footer {
            background-color: #000;
            color: #fff;
            text-align: center;
            padding: 15px;
            width: 100%;
            position: absolute;
            bottom: 0;
        }
    </style>
</head>


<body>
    

    <div class="container">
        <?php if (isset($audio_file_path)) { ?>
            <!-- Reproductor de audio -->
            <h1>Reproduciendo: <?= htmlspecialchars($book_name); ?></h1>
            <audio id="audioPlayer" controls controlsList="nodownload">
                <source src="<?= $audio_file_path; ?>" type="audio/mpeg">
                Tu navegador no soporta el elemento de audio.
            </audio>
        <?php } ?>
    </div>

    <?php include 'components/footer.php'; ?>
</body>
</html>
