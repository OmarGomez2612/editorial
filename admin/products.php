<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
};

if (isset($_POST['add_product'])) {

    // Obtenemos los valores del formulario
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $author = filter_var($_POST['author'], FILTER_SANITIZE_STRING);
    $publisher = filter_var($_POST['publisher'], FILTER_SANITIZE_STRING);
    $price = filter_var($_POST['price'], FILTER_SANITIZE_STRING);
    $details = filter_var($_POST['details'], FILTER_SANITIZE_STRING);
    $category_id = filter_var($_POST['category_id'], FILTER_SANITIZE_NUMBER_INT);
    $type_id = filter_var($_POST['type_id'], FILTER_SANITIZE_NUMBER_INT); // Tipo de libro (físico o audiolibro)

    // Procesamos las imágenes
    $image_01 = $_FILES['image_01']['name'];
    $image_01_tmp_name = $_FILES['image_01']['tmp_name'];
    $image_01_folder = '../uploaded_img/' . $image_01;

    $image_02 = $_FILES['image_02']['name'];
    $image_02_tmp_name = $_FILES['image_02']['tmp_name'];
    $image_02_folder = '../uploaded_img/' . $image_02;

    // Creamos una carpeta para el libro/audiolibro
    $folder_name = strtolower($name) . '_' . time(); // Renombrar carpeta con nombre único (libro+timestamp)

    // Si es un libro digital
    if ($type_id == 1) {
        $book_folder_path = '../libros/' . $folder_name;
        if (!is_dir($book_folder_path)) {
            mkdir($book_folder_path, 0777, true);
        }

        // Mover las imágenes a la carpeta del libro y renombrarlas
         if ($image_01) {
            $new_image_01 = 'portada.' . pathinfo($image_01, PATHINFO_EXTENSION); // Cambiar el nombre a 'portada'
            move_uploaded_file($image_01_tmp_name, $book_folder_path . '/' . $new_image_01);
         }
         if ($image_02) {
            $new_image_02 = 'contraportada.' . pathinfo($image_02, PATHINFO_EXTENSION); // Cambiar el nombre a 'contraportada'
            move_uploaded_file($image_02_tmp_name, $book_folder_path . '/' . $new_image_02);
         }


        // Comprobar si se han subido archivos
      if (!empty($_FILES['book_folder']['name'][0])) {
         // Crear un array para almacenar los archivos con su índice
         $files = [];
         foreach ($_FILES['book_folder']['name'] as $key => $file_name) {
            // Guardamos también la ruta relativa del archivo para mantener la estructura de carpetas
            $relative_path = $_FILES['book_folder']['full_path'][$key]; // Ruta relativa completa del archivo
            $files[] = [
               'name' => $file_name,
               'tmp_name' => $_FILES['book_folder']['tmp_name'][$key],
               'relative_path' => $relative_path // Ruta relativa completa
            ];
         }

         // Mover cada archivo al directorio adecuado respetando la estructura de carpetas
         foreach ($files as $file) {
            // Obtener la ruta completa donde el archivo debe ser almacenado
            $file_destination = $book_folder_path . '/' . $file['relative_path'];

            // Obtener el directorio donde se almacenará el archivo
            $file_dir = dirname($file_destination); // Obtener el directorio contenedor

            // Crear el directorio si no existe (recursivamente)
            if (!is_dir($file_dir)) {
               mkdir($file_dir, 0777, true); // Crear directorios de forma recursiva
            }

            // Mover el archivo al directorio correspondiente
            if (!move_uploaded_file($file['tmp_name'], $file_destination)) {
               echo "Error al mover el archivo: " . $file['name'] . " a " . $file_destination . ". Verifica los permisos y la estructura de la carpeta.";
            }
         }
      }

      // Obtener la ruta de la carpeta interna (asumimos que siempre hay al menos un archivo subido)
      $uploaded_book_folder = dirname($book_folder_path . '/' . $_FILES['book_folder']['full_path'][0]);
      //$message[] = 'Carpeta interna original: $uploaded_book_folder\n';  Depuración

      // Nuevo nombre para la carpeta interna, usando el nombre del libro tal como el usuario lo digita
      $new_folder_name = $book_folder_path . '/' . $name;
      //$message[] = 'Nuevo nombre de carpeta: $new_folder_name\n'; // Depuración

      // Verificamos si la subcarpeta existe y la renombramos
      if (is_dir($uploaded_book_folder)) {
         if (rename($uploaded_book_folder, $new_folder_name)) {
            $message[] = 'Carpeta renombrada exitosamente.';
         } else {
            $message[] = 'Error al renombrar la carpeta.';
         }
      } else {
         echo "La carpeta interna no existe.";
      }

      // Insertar libro en la base de datos
      $insert_product = $conn->prepare("INSERT INTO products (name, author, publisher, details, price, category_id, image_01, image_02, book_folder, type_id) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $insert_product->execute([$name, $author, $publisher, $details, $price, $category_id, $new_image_01, $new_image_02, $folder_name, $type_id]);

      if ($insert_product) {
         $message[] = '¡Nuevo libro agregado y archivos subidos!';
      } else {
         $message[] = 'Error al agregar el libro.';
      }
    }

    // Si es un audiolibro
    elseif ($type_id == 2) {
        $audio_file_name = $_FILES['audio_file']['name'];
        $audio_file_tmp_name = $_FILES['audio_file']['tmp_name'];
        $audio_file_extension = pathinfo($audio_file_name, PATHINFO_EXTENSION);
        $audio_file_new_name = strtolower($name) . '.' . $audio_file_extension;
        $audio_folder_path = '../audiolibros/' . strtolower($name) . '_' . time(); 

        // Crear carpeta para el audiolibro
        if (!is_dir($audio_folder_path)) {
            mkdir($audio_folder_path, 0777, true);
        }

        // Mover imágenes a la carpeta del audiolibro
        if ($image_01) {
            move_uploaded_file($image_01_tmp_name, $audio_folder_path . '/' . $image_01);
        }
        if ($image_02) {
            move_uploaded_file($image_02_tmp_name, $audio_folder_path . '/' . $image_02);
        }

        // Mover el archivo de audio
        if ($audio_file_name) {
            move_uploaded_file($audio_file_tmp_name, $audio_folder_path . '/' . $audio_file_new_name);
            $audio_file = $audio_file_new_name;

            // Insertar audiolibro en la base de datos
            $insert_product = $conn->prepare("INSERT INTO products (name, author, publisher, details, price, category_id, image_01, image_02, book_folder, audio, type_id) 
                                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert_product->execute([$name, $author, $publisher, $details, $price, $category_id, $image_01, $image_02, $folder_name, $audio_file, $type_id]);

            if ($insert_product) {
                $message[] = '¡Nuevo audiolibro agregado y archivos subidos!';
            } else {
                $message[] = 'Error al agregar el audiolibro.';
            }
        } else {
            $message[] = 'Por favor sube un archivo de audio para el audiolibro.';
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
      <title>PRODUCTO</title>

      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
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

      <section class="add-products">
         <h1 class="heading">AGREGAR LIBRO</h1>

         
         <?php
         // Consulta para obtener tipos de productos CAMBIAR SI ES NECESARIO
         $select_types = $conn->prepare("SELECT * FROM type_products");
         $select_types->execute();
         $types = $select_types->fetchAll(PDO::FETCH_ASSOC);
         ?>
         
         <form action="" method="post" enctype="multipart/form-data">

            <div class="flex">
               <div class="inputBox">
                  <span>Título del Libro</span>
                  <input type="text" class="box" required maxlength="100" placeholder="Ingresa el título del libro" name="name">
               </div>
               <div class="inputBox">
                  <span>Autor</span>
                  <input type="text" class="box" required maxlength="100" placeholder="Ingresa el autor" name="author">
               </div>
               <div class="inputBox">
                  <span>Precio</span>
                  <input type="text" class="box" required maxlength="100" placeholder="Ingresa el precio" name="price">
               </div>
               <div class="inputBox">
                  <span>Editorial</span>
                  <input type="text" class="box" required maxlength="100" placeholder="Ingresa la editorial" name="publisher">
               </div>
               <div class="inputBox">
                  <span>Imagen 1 (Portada)</span>
                  <input type="file" name="image_01" accept="image/jpg, image/jpeg, image/png, image/webp" class="box" required>
               </div>
               <div class="inputBox">
                  <span>Imagen 2 (Contraportada)</span>
                  <input type="file" name="image_02" accept="image/jpg, image/jpeg, image/png, image/webp" class="box" required>
               </div>
               <div class="inputBox">
                  <span>Detalles</span>
                  <textarea name="details" placeholder="Ingresa los detalles del libro" class="box" required maxlength="500" cols="30" rows="10"></textarea>
               </div>
               <div class="inputBox">
                  <span>Categoría</span>
                  <select name="category_id" class="box" required>
                     <option value="">Selecciona una categoría</option>
                     <?php
                     // Obtener las categorías desde la base de datos
                     $select_categories = $conn->prepare("SELECT * FROM categories");
                     $select_categories->execute();
                     while($fetch_categories = $select_categories->fetch(PDO::FETCH_ASSOC)){
                           echo '<option value="'.$fetch_categories['id'].'">'.$fetch_categories['categorias'].'</option>';
                     }
                     ?>
                  </select>
               </div>
              <!-- Validación para saber el tipo de archivo -->
               <div class="inputBox">
                  <span>Tipo de Producto</span>
                  <select name="type_id" class="box" required onchange="toggleFileInput(this)">
                     <option value="">Selecciona un tipo de producto</option>
                     <?php foreach ($types as $type) { ?>
                        <option value="<?= $type['id']; ?>"><?= $type['types']; ?></option>
                     <?php } ?>
                  </select>
               </div>

               <div class="inputBox" id="audioInput" style="display: none;">
                  <span>Archivo de Audio</span>
                  <input type="file" name="audio_file" accept="audio/*" class="box">
               </div>

               <div class="inputBox" id="bookFolderInput" style="display: block;">
                  <span>Carpeta de Archivos</span>
                  <input type="file" name="book_folder[]" class="box" webkitdirectory mozdirectory multiple>
               </div>

               <script>
                  function toggleFileInput(select) {
                     const bookFolderInput = document.getElementById('bookFolderInput');
                     const audioInput = document.getElementById('audioInput');
                     
                     if (select.value == '2') { // Audiolibro
                        bookFolderInput.style.display = 'none';  // Ocultar el campo para carpeta
                        audioInput.style.display = 'block';  // Mostrar el campo para audio
                     } else {  // Libro
                        bookFolderInput.style.display = 'block';  // Mostrar el campo para carpeta
                        audioInput.style.display = 'none';  // Ocultar el campo para audio
                     }
                  }
               </script>
            </div>
            <input type="submit" value="Agregar Libro" class="btn" name="add_product">
         </form>
      </section>
      <script src="../js/admin_script.js"></script>

   </body>
</html>