<?php

include '../components/connect.php';
session_start();

// Verificar si la sesión del admin está activa
if(!isset($_SESSION['admin_id'])){
   header('location:../admin/admin_login.php'); // Redirigir a login si no está iniciada la sesión
   exit();
}

$admin_id = $_SESSION['admin_id'];

// Aquí recuperamos los datos que necesitamos
$total_completados = $conn->query("SELECT COUNT(*) FROM `payments` WHERE payment_status = 'Completed'")->fetchColumn();
$total_pendientes = $conn->query("SELECT COUNT(*) FROM `payments` WHERE payment_status = 'Pending'")->fetchColumn();
$total_productos = $conn->query("SELECT COUNT(*) FROM `products`")->fetchColumn();
$total_usuarios = $conn->query("SELECT COUNT(*) FROM `users`")->fetchColumn();
$total_admins = $conn->query("SELECT COUNT(*) FROM `admins`")->fetchColumn();
$total_libros = $conn->query("SELECT COUNT(*) FROM `products` WHERE type_id = '1'")->fetchColumn();
$total_audiolibros = $conn->query("SELECT COUNT(*) FROM `products` WHERE type_id = '2'")->fetchColumn();
// Configurar el lenguaje de MySQL en español
$conn->query("SET lc_time_names = 'es_ES'");
// Obtener los ingresos por mes
$ventas_por_mes = $conn->query("SELECT DATE_FORMAT(payment_date, '%M') AS mes, SUM(payment_amount) AS total_dinero FROM payments WHERE YEAR(payment_date) = YEAR(CURRENT_DATE) GROUP BY MONTH(payment_date) ORDER BY MONTH(payment_date)")->fetchAll(PDO::FETCH_ASSOC);



?>

<?php
// Obtener los 3 productos más vendidos ++++++ no se para que se quiere aqui
$select_top_books = $conn->prepare("SELECT product_id, COUNT(*) AS total_sales
                                    FROM payment_items
                                    INNER JOIN payments ON payments.payment_id = payment_items.payment_id
                                    WHERE payments.payment_status = 'Completed'
                                    GROUP BY product_id
                                    ORDER BY total_sales DESC
                                    LIMIT 3");
$select_top_books->execute();
$top_books = $select_top_books->fetchAll(PDO::FETCH_ASSOC);

// Obtener los nombres de los productos más vendidos
$book_names = [];
$sales_data = [];
foreach ($top_books as $book) {
    $product_id = $book['product_id'];
    
    // Obtener el nombre del producto
    $select_product = $conn->prepare("SELECT name FROM products WHERE id = ?");
    $select_product->execute([$product_id]);
    $product = $select_product->fetch(PDO::FETCH_ASSOC);
    
    if ($product) {
        $book_names[] = $product['name'];
        $sales_data[] = $book['total_sales'];
    }
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
   
   <!-- Añade Chart.js -->
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

   <!-- Librerías de AMCharts -->
   <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
   <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
   <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="dashboard">

   <h1 class="heading">PANEL DE CONTROL</h1>

   <div class="box-container">

      <div class="box" style="height: 200px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
         <h3>¡BIENVENIDO!</h3>
         <p><?= $fetch_profile['name']; ?></p>
         <a href="update_profile.php" class="btn">ACTUALIZAR PERFIL</a>
      </div>

      <div class="box" style="height: 200px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
         <?php
            $total_pendings = 0;
            $select_pendings = $conn->prepare("SELECT * FROM `payments` WHERE payment_status = ?");
            $select_pendings->execute(['Completed']);
            while($fetch_pendings = $select_pendings->fetch(PDO::FETCH_ASSOC)){
               $total_pendings += $fetch_pendings['payment_amount'];
            }
         ?>
         <h3><span>$</span><?= $total_pendings; ?><span> MXM</span></h3>
         <p>COMPLETADOS</p>
         <a href="placed_orders.php" class="btn">VER TUS PEDIDOS</a>
      </div>

      <div class="box" style="height: 200px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
         <?php
            $select_orders = $conn->prepare("SELECT * FROM `payments`");
            $select_orders->execute();
            $number_of_orders = $select_orders->rowCount();
         ?>
         <h3><?= $number_of_orders; ?></h3>
         <p>TOTAL DE PEDIDOS</p>
         <a href="placed_orders.php" class="btn">TODOS LOS PEDIDOS</a>
      </div>

      <div class="box" style="height: 200px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
         <?php
            $select_orders = $conn->prepare("SELECT * FROM `wishlist`");
            $select_orders->execute();
            $number_of_orders = $select_orders->rowCount();
         ?>
         <h3><?= $number_of_orders; ?></h3>
         <p>EN LISTA DE DESEOS</p>
         <a href="wishlist.php" class="btn">VER CARRITO</a>
      </div>

      <div class="box" style="height: 200px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
         <?php
            $select_users = $conn->prepare("SELECT * FROM `products` WHERE type_id = 1");     
            $select_users->execute();
            $number_of_users = $select_users->rowCount();
         ?>
         <h3><?= $number_of_users; ?></h3>
         <p>LIBROS</p>
         <a href="add_prods.php" class="btn">VER LIBROS</a>
      </div>

      <div class="box" style="height: 200px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
         <?php
            $select_users = $conn->prepare("SELECT * FROM `products` WHERE type_id = 2");     
            $select_users->execute();
            $number_of_users = $select_users->rowCount();
         ?>
         <h3><?= $number_of_users; ?></h3>
         <p>AUDIOLIBROS</p>
         <a href="productsaudio.php" class="btn">VER AUDIOLIBROS</a>
      </div>

      <div class="box" style="height: 200px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
         <?php
            $select_users = $conn->prepare("SELECT * FROM `users`");
            $select_users->execute();
            $number_of_users = $select_users->rowCount();
         ?>
         <h3><?= $number_of_users; ?></h3>
         <p>USUARIOS</p>
         <a href="users_accounts.php" class="btn">VER USUARIOS</a>
      </div>

      <div class="box" style="height: 200px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
         <?php
            $select_admins = $conn->prepare("SELECT * FROM `admins`");
            $select_admins->execute();
            $number_of_admins = $select_admins->rowCount();
         ?>
         <h3><?= $number_of_admins; ?></h3>
         <p>ADMINISTRADORES</p>
         <a href="admin_accounts.php" class="btn">VER ADMINISTRADORES</a>
      </div>

   </div>
   <h2 class="heading">ANÁLISIS DE GRÁFICOS</h2>
   <div class="charts-grid">
   <div class="chart-box">
      <h3>TIPOS DE PRODUCTOS</h3>
      <div id="typeProductChart" style="width: 100%; height: 200px;"></div>
   </div>
   <div class="chart-box">
      <h3>TOTAL DE PEDIDOS</h3>
      <div id="ordersChart" style="width: 100%; height: 200px;"></div>
   </div>
   <div class="chart-box">
      <h3>VENTAS POR LIBRO</h3>
      <div id="productsChart" style="width: 100%; height: 200px;"></div>
   </div>
   <div class="chart-box">
      <h3>TOTAL DE USUARIOS</h3>
      <div id="usersChart" style="width: 100%; height: 200px;"></div>
   </div>
   <div class="chart-box">
      <h3>INGRESOS POR MES</h3>
      <div id="monthlySalesChart" style="width: 100%; height: 200px;"></div>
   </div>
</div>
</section>

<script>
   
// Grafico de total de Pedidos
am5.ready(function() {
   let rootOrders = am5.Root.new("ordersChart");
   rootOrders.setThemes([am5themes_Animated.new(rootOrders)]);

   let chartOrders = rootOrders.container.children.push(am5xy.XYChart.new(rootOrders, {}));
   let xAxisOrders = chartOrders.xAxes.push(am5xy.CategoryAxis.new(rootOrders, {
      categoryField: "status",
      renderer: am5xy.AxisRendererX.new(rootOrders, {})
   }));
   let yAxisOrders = chartOrders.yAxes.push(am5xy.ValueAxis.new(rootOrders, {
      renderer: am5xy.AxisRendererY.new(rootOrders, {})
   }));
   let seriesOrders = chartOrders.series.push(am5xy.ColumnSeries.new(rootOrders, {
      name: "Pedidos",
      xAxis: xAxisOrders,
      yAxis: yAxisOrders,
      valueYField: "value",
      categoryXField: "status"
   }));

   let dataOrders = [
      { status: "Completados", value: <?= $total_completados; ?> },
      { status: "Pendientes", value: <?= $total_pendientes; ?> }
   ];
   seriesOrders.data.setAll(dataOrders);
   xAxisOrders.data.setAll(dataOrders);

   // Gráfico de Ventas por Libro
   let rootProducts = am5.Root.new("productsChart");
   rootProducts.setThemes([am5themes_Animated.new(rootProducts)]);

   let chartProducts = rootProducts.container.children.push(am5xy.XYChart.new(rootProducts, {}));
   let xAxisProducts = chartProducts.xAxes.push(am5xy.CategoryAxis.new(rootProducts, {
      categoryField: "product",
      renderer: am5xy.AxisRendererX.new(rootProducts, {})
   }));
   let yAxisProducts = chartProducts.yAxes.push(am5xy.ValueAxis.new(rootProducts, {
      renderer: am5xy.AxisRendererY.new(rootProducts, {})
   }));
   let seriesProducts = chartProducts.series.push(am5xy.ColumnSeries.new(rootProducts, {
      name: "Ventas",
      xAxis: xAxisProducts,
      yAxis: yAxisProducts,
      valueYField: "sales",
      categoryXField: "product"
   }));

   let dataProducts = [
      <?php foreach ($book_names as $index => $name) { ?>
      { product: "<?= $name; ?>", sales: <?= $sales_data[$index]; ?> },
      <?php } ?>
   ];
   seriesProducts.data.setAll(dataProducts);
   xAxisProducts.data.setAll(dataProducts);

   // Gráfico de Usuarios
   let rootUsers = am5.Root.new("usersChart");
   rootUsers.setThemes([am5themes_Animated.new(rootUsers)]);

   let chartUsers = rootUsers.container.children.push(am5xy.XYChart.new(rootUsers, {}));
   let xAxisUsers = chartUsers.xAxes.push(am5xy.CategoryAxis.new(rootUsers, {
      categoryField: "category",
      renderer: am5xy.AxisRendererX.new(rootUsers, {})
   }));
   let yAxisUsers = chartUsers.yAxes.push(am5xy.ValueAxis.new(rootUsers, {
      renderer: am5xy.AxisRendererY.new(rootUsers, {})
   }));
   let seriesUsers = chartUsers.series.push(am5xy.ColumnSeries.new(rootUsers, {
      name: "Usuarios",
      xAxis: xAxisUsers,
      yAxis: yAxisUsers,
      valueYField: "value",
      categoryXField: "category"
   }));

   let dataUsers = [
      { category: "Usuarios", value: <?= $total_usuarios; ?> },
      { category: "Administradores", value: <?= $total_admins; ?> }
   ];
   seriesUsers.data.setAll(dataUsers);
   xAxisUsers.data.setAll(dataUsers);

   // Gráfica de Tipos de libros
   let rootType = am5.Root.new("typeProductChart");
   rootType.setThemes([am5themes_Animated.new(rootType)]);

   let chartType = rootType.container.children.push(am5xy.XYChart.new(rootType, {}));
   let xAxisType = chartType.xAxes.push(am5xy.CategoryAxis.new(rootType, {
      categoryField: "type",
      renderer: am5xy.AxisRendererX.new(rootType, {})
   }));
   let yAxisType = chartType.yAxes.push(am5xy.ValueAxis.new(rootType, {
      renderer: am5xy.AxisRendererY.new(rootType, {})
   }));
   let seriesType = chartType.series.push(am5xy.ColumnSeries.new(rootType, {
      name: "Tipos",
      xAxis: xAxisType,
      yAxis: yAxisType,
      valueYField: "value",
      categoryXField: "type"
   }));

   let dataType = [
      { type: "Libro", value: <?= $total_libros; ?> },
      { type: "Audiolibro", value: <?= $total_audiolibros; ?> },
   ];
   seriesType.data.setAll(dataType);
   xAxisType.data.setAll(dataType);

   // Grafica de ventas por mes
   // Grafica de ventas por mes
let rootSales = am5.Root.new("monthlySalesChart");
rootSales.setThemes([am5themes_Animated.new(rootSales)]);

// Crear el gráfico XY
let chartSales = rootSales.container.children.push(am5xy.XYChart.new(rootSales, {}));

// Configurar los ejes X y Y
let xAxisSales = chartSales.xAxes.push(am5xy.CategoryAxis.new(rootSales, {
   categoryField: "category", // Corregido: Asegura que coincide con los datos
   renderer: am5xy.AxisRendererX.new(rootSales, {})
}));

let yAxisSales = chartSales.yAxes.push(am5xy.ValueAxis.new(rootSales, {
   renderer: am5xy.AxisRendererY.new(rootSales, {})
}));

// Configurar la serie lineal con puntos visibles (bullets)
let seriesSales = chartSales.series.push(am5xy.LineSeries.new(rootSales, {
   name: "Ingresos por mes",
   xAxis: xAxisSales,
   yAxis: yAxisSales,
   valueYField: "value",
   categoryXField: "category",
   stroke: am5.color(0xFF5722),
   tooltip: am5.Tooltip.new(rootSales, {}),
   bullets: [am5.Bullet.new(rootSales, {
      sprite: am5.Circle.new(rootSales, {
         radius: 5, // Tamaño del punto en cada vértice
         fill: am5.color(0xFF5722)
      })
   })]
}));

// Asegurarse de que los datos de PHP sean JSON válido
let dataSales = <?= json_encode(array_map(function($venta) {
   return [
      "category" => $venta["mes"], // Nombre del mes en español
      "value" => floatval($venta["total_dinero"]) // Convertir a número decimal
   ];
}, $ventas_por_mes)); ?>;

// Establecer los datos correctamente en la serie y el eje X
seriesSales.data.setAll(dataSales);
xAxisSales.data.setAll(dataSales);






});


</script>
</body>
</html>