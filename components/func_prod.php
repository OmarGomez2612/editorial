PROD

<?php
include '../components/func_prod.php'; // Incluir el archivo PHP para manejar la lógica

// Mensaje de confirmación, si existe
if (isset($_SESSION['message'])) {
   echo '<div class="message">' . $_SESSION['message'] . '</div>';
   unset($_SESSION['message']);
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
   <?php include '../components/admin_header.php'; ?>

   <section class="add-products">
      <h1 class="heading">AGREGAR PRODUCTO</h1>

      <form action="" method="post" enctype="multipart/form-data">
         <div class="flex">
            <!-- Campo para el tipo de producto (Libro o Audiolibro) -->
            <div class="inputBox">
               <span>Tipo de Producto</span>
               <select name="type_id" id="type_id" class="box" required>
                  <option value="">Selecciona un tipo de producto</option>
                  <option value="1">Libro</option>
                  <option value="2">Audiolibro</option>
               </select>
            </div>

            <div class="inputBox">
               <span>Título del Producto</span>
               <input type="text" class="box" required maxlength="100" placeholder="Ingresa el título del libro" name="name">
            </div>
            <div class="inputBox">
               <span>Autor</span>
               <input type="text" class="box" required maxlength="100" placeholder="Ingresa el autor" name="author">
            </div>
            <div class="inputBox">
               <span>Precio</span>
               <input type="number" class="box" required maxlength="100" placeholder="Ingresa el precio" name="price">
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
               <textarea name="details" placeholder="Ingresa los detalles del producto" class="box" required maxlength="500" cols="30" rows="10"></textarea>
            </div>
            <div class="inputBox">
               <span>Categoría</span>
               <select name="category_id" class="box" required>
                  <option value="">Selecciona una categoría</option>
                  <?php
                  // Obtener las categorías desde la base de datos
                  $select_categories = $conn->prepare("SELECT * FROM categories");
                  $select_categories->execute();
                  while($fetch_categories = $select_categories->fetch(PDO::FETCH_ASSOC)){
                        echo '<option value="'.$fetch_categories['id'].'">'.$fetch_categories['categorias'].'</option>';
                  }
                  ?>
               </select>
            </div>

            <!-- Input para carpeta de archivos (libros) -->
            <div class="inputBox" id="book_folder_input" style="display: none;">
               <span>Carpeta de Archivos (Solo para libros)</span>
               <input type="file" name="book_folder[]" class="box" webkitdirectory mozdirectory multiple>
            </div>

            <!-- Input para archivo de audio (audiolibros) -->
            <div class="inputBox" id="audio_input" style="display: none;">
               <span>Archivo de Audio (Solo para audiolibros)</span>
               <input type="file" name="audio_file" accept=".mp3, .mp4" class="box">
            </div>
         </div>
         <input type="submit" value="Agregar Producto" class="btn" name="add_product">
      </form>
   </section>

   <section class="show-products">
      <h1 class="heading">PRODUCTOS AGREGADOS</h1>
      <div class="box-container">
      <?php
         // Ejecutamos la consulta con JOIN para obtener la categoría
         $select_products = $conn->prepare("
            SELECT p.*, c.categorias 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
         ");
         $select_products->execute();

         // Verificamos si hay productos
         if($select_products->rowCount() > 0){
            // Si hay productos, mostramos cada uno
            while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
      ?>
         <div class="box">
            <img src="../libros/<?= htmlspecialchars($fetch_products['book_folder'] . '/' . $fetch_products['image_01']); ?>" alt="Imagen del producto">
            <div class="name"><?= htmlspecialchars($fetch_products['name']); ?></div>
            <div class="author">Autor: <?= htmlspecialchars($fetch_products['author']); ?></div>
            <div class="publisher">Editorial: <?= htmlspecialchars($fetch_products['publisher']); ?></div>
            <div class="category">Categoría: <?= htmlspecialchars($fetch_products['categorias']); ?></div>
            <div class="price">$<span><?= htmlspecialchars($fetch_products['price']); ?></span> MXN</div>
            <div class="details"><span><?= htmlspecialchars($fetch_products['details']); ?></span></div>

            <?php if (is_dir('../libros/' . htmlspecialchars($fetch_products['book_folder']))) { ?>
               <div class="book-link">
                  <a href="../libros/<?= htmlspecialchars($fetch_products['book_folder']); ?>/" target="_blank">VER ARCHIVOS DEL LIBRO</a>
               </div>
            <?php } ?>
            <div class="flex-btn">
               <a href="update_product.php?update=<?= htmlspecialchars($fetch_products['id']); ?>" class="btn">Actualizar</a>
               <a href="delete_product.php?delete=<?= htmlspecialchars($fetch_products['id']); ?>" class="btn">Eliminar</a>
            </div>
         </div>
      <?php
            }
         }
      ?>
      </div>
   </section>

   <?php include '../components/footer.php'; ?>

   <script>
      // Script para mostrar u ocultar los campos dependiendo del tipo de producto
      document.getElementById('type_id').addEventListener('change', function() {
         var type = this.value;
         if (type == '1') { // Si es un libro
            document.getElementById('book_folder_input').style.display = 'block';
            document.getElementById('audio_input').style.display = 'none';
         } else if (type == '2') { // Si es un audiolibro
            document.getElementById('book_folder_input').style.display = 'none';
            document.getElementById('audio_input').style.display = 'block';
         } else {
            document.getElementById('book_folder_input').style.display = 'none';
            document.getElementById('audio_input').style.display = 'none';
         }
      });
   </script>
</body>
</html>






FUNC
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
   $type_id = filter_var($_POST['type_id'], FILTER_SANITIZE_NUMBER_INT);

   // Validar y procesar los archivos según el tipo de producto
   if ($type_id == 1) { // Libros
      // Procesamos las imágenes
      $image_01 = $_FILES['image_01']['name'];
      $image_01_tmp_name = $_FILES['image_01']['tmp_name'];
      $image_01_folder = '../uploaded_img/' . $image_01;

      $image_02 = $_FILES['image_02']['name'];
      $image_02_tmp_name = $_FILES['image_02']['tmp_name'];
      $image_02_folder = '../uploaded_img/' . $image_02;

      // Definimos la ruta para los libros
      $book_folder_name = strtolower($name) . '_' . time(); // Renombrar carpeta con nombre único (libro+timestamp)
      $book_folder_path = '../libros/' . $book_folder_name;

      // Crear la carpeta para el libro
      if (!is_dir($book_folder_path)) {
         mkdir($book_folder_path, 0777, true);
      }

      // Mover las imágenes a la carpeta del libro y renombrarlas
      if ($image_01) {
         $image_01_extension = pathinfo($image_01, PATHINFO_EXTENSION);
         $new_image_01 = 'portada.' . $image_01_extension;
         move_uploaded_file($image_01_tmp_name, $book_folder_path . '/' . $new_image_01);
      }
      if ($image_02) {
         $image_02_extension = pathinfo($image_02, PATHINFO_EXTENSION);
         $new_image_02 = 'contraportada.' . $image_02_extension;
         move_uploaded_file($image_02_tmp_name, $book_folder_path . '/' . $new_image_02);
      }

      // Comprobar si se han subido archivos de libro
      if (!empty($_FILES['book_folder']['name'][0])) {
         $files = [];
         foreach ($_FILES['book_folder']['name'] as $key => $file_name) {
            $relative_path = $_FILES['book_folder']['full_path'][$key];
            $files[] = [
               'name' => $file_name,
               'tmp_name' => $_FILES['book_folder']['tmp_name'][$key],
               'relative_path' => $relative_path
            ];
         }

         foreach ($files as $file) {
            $file_destination = $book_folder_path . '/' . $file['relative_path'];
            $file_dir = dirname($file_destination);
            if (!is_dir($file_dir)) {
               mkdir($file_dir, 0777, true);
            }
            if (!move_uploaded_file($file['tmp_name'], $file_destination)) {
               echo "Error al mover el archivo: " . $file['name'];
            }
         }
      }

      // Insertar libro en la base de datos
      $insert_product = $conn->prepare("INSERT INTO products (name, author, publisher, details, price, category_id, type_id, image_01, image_02, book_folder) 
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $insert_product->execute([$name, $author, $publisher, $details, $price, $category_id, $type_id, $new_image_01, $new_image_02, $book_folder_name]);

      if ($insert_product) {
         $_SESSION['message'] = '¡Nuevo libro agregado y archivos subidos!';
      } else {
         $_SESSION['message'] = 'Error al agregar el libro.';
      }
   } else if ($type_id == 2) { // Audiolibros
      // Procesamos el archivo de audio
      $audio_name = $_FILES['audio_file']['name'];
      $audio_tmp_name = $_FILES['audio_file']['tmp_name'];
      $audio_folder_name = strtolower($name) . '_' . time();
      $audio_folder_path = '../audiolibros/' . $audio_folder_name;

      // Crear la carpeta para el audiolibro
      if (!is_dir($audio_folder_path)) {
         mkdir($audio_folder_path, 0777, true);
      }

      // Mover el archivo de audio a la carpeta correspondiente
      if ($audio_name) {
         $audio_extension = pathinfo($audio_name, PATHINFO_EXTENSION);
         $new_audio_name = 'audiolibro.' . $audio_extension;
         move_uploaded_file($audio_tmp_name, $audio_folder_path . '/' . $new_audio_name);
      }

      // Insertar audiolibro en la base de datos
      $insert_product = $conn->prepare("INSERT INTO products (name, author, publisher, details, price, category_id, type_id, audio_file) 
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
      $insert_product->execute([$name, $author, $publisher, $details, $price, $category_id, $type_id, $audio_folder_name]);

      if ($insert_product) {
         $_SESSION['message'] = '¡Nuevo Audiolibro agregado y archivo subido!';
      } else {
         $_SESSION['message'] = 'Error al agregar el Audiolibro.';
      }
   }
}
?>