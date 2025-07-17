-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-03-2025 a las 18:46:08
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `shop_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admins`
--

CREATE TABLE `admins` (
  `id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `admins`
--

INSERT INTO `admins` (`id`, `name`, `password`) VALUES
(1, 'admin', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2'),
(14, 'Aaron', 'b0399d2029f64d445bd131ffaa399a42d2f8e7dc'),
(15, 'omar', 'b1b3773a05c0ed0176787a4f1574ff0075f7521e');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cart`
--

CREATE TABLE `cart` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `pid` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(10) NOT NULL,
  `quantity` int(10) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `categorias` varchar(20) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `categorias`, `image`) VALUES
(4, 'Fantasía', 'Fantasía.png'),
(5, 'Pruebas', 'Pruebas.png'),
(7, 'Historieta', 'Historieta.jpeg'),
(8, 'Ficción', 'Ficción.png'),
(9, 'Cuentos', 'Cuentos.png'),
(10, 'Audiolibros', 'Audiolibros.jpg'),
(11, 'Gratis', 'Gratis.png'),
(12, 'Novela', 'Novela.png'),
(13, 'Poesía', 'Poesía.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'MXN',
  `transaction_id` varchar(255) NOT NULL,
  `payer_id` varchar(255) NOT NULL,
  `payer_email` varchar(255) NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'PayPal',
  `order_details` text NOT NULL,
  `order_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `payments`
--

INSERT INTO `payments` (`payment_id`, `user_id`, `payment_status`, `payment_date`, `payment_amount`, `currency`, `transaction_id`, `payer_id`, `payer_email`, `payment_method`, `order_details`, `order_id`) VALUES
(46, 9, 'Completed', '2025-02-28 17:58:24', 210.00, 'MXN', '2T4414267C3434018', '6T2HRRGTPZRTC', 'sb-4903v34492468@personal.example.com', 'PayPal', '{\"id\":\"2T4414267C3434018\",\"intent\":\"CAPTURE\",\"status\":\"COMPLETED\",\"purchase_units\":[{\"reference_id\":\"default\",\"amount\":{\"currency_code\":\"MXN\",\"value\":\"210.00\"},\"payee\":{\"email_address\":\"sb-4pkkh34488120@business.example.com\",\"merchant_id\":\"TWYZWTW9E4DGG\"},\"soft_descriptor\":\"PAYPAL *TEST STORE\",\"shipping\":{\"name\":{\"full_name\":\"John Doe\"},\"address\":{\"address_line_1\":\"Calle Juarez 1\",\"address_line_2\":\"Col. Cuauhtemoc\",\"admin_area_2\":\"Miguel Hidalgo\",\"admin_area_1\":\"Ciudad de Mexico\",\"postal_code\":\"11580\",\"country_code\":\"MX\"}},\"payments\":{\"captures\":[{\"id\":\"81G17290TH743462Y\",\"status\":\"PENDING\",\"status_details\":{\"reason\":\"PENDING_REVIEW\"},\"amount\":{\"currency_code\":\"MXN\",\"value\":\"210.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"NOT_ELIGIBLE\"},\"create_time\":\"2025-02-28T17:58:24Z\",\"update_time\":\"2025-02-28T17:58:24Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"John\",\"surname\":\"Doe\"},\"email_address\":\"sb-4903v34492468@personal.example.com\",\"payer_id\":\"6T2HRRGTPZRTC\",\"address\":{\"country_code\":\"MX\"}},\"create_time\":\"2025-02-28T17:57:42Z\",\"update_time\":\"2025-02-28T17:58:24Z\",\"links\":[{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/checkout\\/orders\\/2T4414267C3434018\",\"rel\":\"self\",\"method\":\"GET\"}]}', NULL),
(47, 12, 'Completed', '2025-02-28 19:02:06', 210.00, 'MXN', '1EH8919195072593Y', '6T2HRRGTPZRTC', 'sb-4903v34492468@personal.example.com', 'PayPal', '{\"id\":\"1EH8919195072593Y\",\"intent\":\"CAPTURE\",\"status\":\"COMPLETED\",\"purchase_units\":[{\"reference_id\":\"default\",\"amount\":{\"currency_code\":\"MXN\",\"value\":\"210.00\"},\"payee\":{\"email_address\":\"sb-4pkkh34488120@business.example.com\",\"merchant_id\":\"TWYZWTW9E4DGG\"},\"soft_descriptor\":\"PAYPAL *TEST STORE\",\"shipping\":{\"name\":{\"full_name\":\"John Doe\"},\"address\":{\"address_line_1\":\"Calle Juarez 1\",\"address_line_2\":\"Col. Cuauhtemoc\",\"admin_area_2\":\"Miguel Hidalgo\",\"admin_area_1\":\"Ciudad de Mexico\",\"postal_code\":\"11580\",\"country_code\":\"MX\"}},\"payments\":{\"captures\":[{\"id\":\"9LS343695B571740G\",\"status\":\"PENDING\",\"status_details\":{\"reason\":\"PENDING_REVIEW\"},\"amount\":{\"currency_code\":\"MXN\",\"value\":\"210.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"NOT_ELIGIBLE\"},\"create_time\":\"2025-02-28T19:02:06Z\",\"update_time\":\"2025-02-28T19:02:06Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"John\",\"surname\":\"Doe\"},\"email_address\":\"sb-4903v34492468@personal.example.com\",\"payer_id\":\"6T2HRRGTPZRTC\",\"address\":{\"country_code\":\"MX\"}},\"create_time\":\"2025-02-28T19:00:49Z\",\"update_time\":\"2025-02-28T19:02:06Z\",\"links\":[{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/checkout\\/orders\\/1EH8919195072593Y\",\"rel\":\"self\",\"method\":\"GET\"}]}', NULL),
(48, 12, 'Completed', '2025-03-01 00:13:51', 220.00, 'MXN', '5AB119769T4264412', '6T2HRRGTPZRTC', 'sb-4903v34492468@personal.example.com', 'PayPal', '{\"id\":\"5AB119769T4264412\",\"intent\":\"CAPTURE\",\"status\":\"COMPLETED\",\"purchase_units\":[{\"reference_id\":\"default\",\"amount\":{\"currency_code\":\"MXN\",\"value\":\"220.00\"},\"payee\":{\"email_address\":\"sb-4pkkh34488120@business.example.com\",\"merchant_id\":\"TWYZWTW9E4DGG\"},\"soft_descriptor\":\"PAYPAL *TEST STORE\",\"shipping\":{\"name\":{\"full_name\":\"John Doe\"},\"address\":{\"address_line_1\":\"Calle Juarez 1\",\"address_line_2\":\"Col. Cuauhtemoc\",\"admin_area_2\":\"Miguel Hidalgo\",\"admin_area_1\":\"Ciudad de Mexico\",\"postal_code\":\"11580\",\"country_code\":\"MX\"}},\"payments\":{\"captures\":[{\"id\":\"5TS11301GL0901514\",\"status\":\"PENDING\",\"status_details\":{\"reason\":\"PENDING_REVIEW\"},\"amount\":{\"currency_code\":\"MXN\",\"value\":\"220.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"NOT_ELIGIBLE\"},\"create_time\":\"2025-03-01T00:13:51Z\",\"update_time\":\"2025-03-01T00:13:51Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"John\",\"surname\":\"Doe\"},\"email_address\":\"sb-4903v34492468@personal.example.com\",\"payer_id\":\"6T2HRRGTPZRTC\",\"address\":{\"country_code\":\"MX\"}},\"create_time\":\"2025-03-01T00:12:32Z\",\"update_time\":\"2025-03-01T00:13:51Z\",\"links\":[{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/checkout\\/orders\\/5AB119769T4264412\",\"rel\":\"self\",\"method\":\"GET\"}]}', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payment_items`
--

CREATE TABLE `payment_items` (
  `payment_item_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `payment_items`
--

INSERT INTO `payment_items` (`payment_item_id`, `payment_id`, `product_id`, `product_name`, `price`) VALUES
(35, 46, 121, 'Al Desnudo', 210.00),
(36, 47, 121, 'Al Desnudo', 210.00),
(37, 48, 125, 'Cuentos de un maestro que es puro cuento', 220.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `details` varchar(500) NOT NULL,
  `price` int(10) NOT NULL,
  `category_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image_01` varchar(100) NOT NULL,
  `image_02` varchar(100) NOT NULL,
  `AUDIO` varchar(255) DEFAULT NULL,
  `author` varchar(255) NOT NULL DEFAULT 'Autor Desconocido',
  `publisher` varchar(255) NOT NULL,
  `book_folder` varchar(255) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `name`, `details`, `price`, `category_id`, `created_at`, `image_01`, `image_02`, `AUDIO`, `author`, `publisher`, `book_folder`, `type_id`) VALUES
(83, 'Habitos Atómicos', 'Es libro de autoayuda', 215, 5, '2025-02-23 04:38:03', '9786075696140.jpg', '61ZJfQWqWQL._AC_UF1000,1000_QL80_.jpg', NULL, 'Anónimo', 'Prueba', 'habitos atómicos_1740285479', 1),
(117, 'El gato con botas', 'Este libro es un audiolibro gratis', 0, 11, '2025-02-26 22:46:41', 'gato2.jpg', 'gato1.jpg', 'el gato con botas.mp3', 'Anónimo', 'Libre', 'el gato con botas_1740610001', 2),
(120, 'Orgullo y Prejuicio', 'Una de las historias de amor más preciadas de todos los tiempos, esta comedia clásica de modales cuenta el cortejo improbable de Elizabeth Bennet y Fitzwilliam Darcy. La historia de cómo estos dos personajes superan su propio orgullo y prejuicio, así como la familia y la sociedad en general, continúa fascinando a los lectores de todas las edades a través de generaciones.', 349, 12, '2025-02-28 17:49:22', 'portada.jpg', 'contraportada.jpg', NULL, 'Jane Austen', 'Clásicos Ilustrados', 'orgullo y prejuicio_1740764960', 1),
(121, 'Al Desnudo', 'Al desnudo es una propuesta de mirar el interior de lo desconocido, de lo oculto, de lo que falta por resolver, y la propuesta de este libro marca un rumbo digno de admirar y por descubrir... ', 210, 12, '2025-02-28 17:56:07', 'portada.png', 'contraportada.png', NULL, 'Miriam Pérez Santamaría', 'Tlacuache', 'al desnudo_1740765365', 1),
(122, 'La cenicienta', 'El cuento de hadas más icónico del mundo de la literatura y la pantalla grande, Cenicienta te tendrá esperando el momento de dar vuelta la página de principio a fin. Presencia la historia de la princesa más bondadosa y agraciada del mundo, que contará con la ayuda de su madrina mágica para sorprender al príncipe y toda la nobleza del país con su delicadeza y belleza. ', 49, 10, '2025-02-28 18:09:11', '91cvOqshQZL._UF894,1000_QL80_.jpg', 'images.jpg', 'la cenicienta.mp3', 'Charles Perraut', 'Cuentos Infantiles', 'la cenicienta_1740766151', 2),
(123, 'Padre rico padre pobre', 'Padre rico, padre pobre es una lectura necesaria si quieres aprender sobre finanzas personales y educación financiera. Este bestseller ha ayudado a la mayoría de la gente a entender cuáles son sus malos hábitos financieros, cómo cambiarlos, saber cómo invertir y tener una mejor comprensión de los mercados.', 79, 10, '2025-02-28 18:14:28', 'page_1_thumb_large.webp', 'Robert-Kiyosaki.webp', 'padre rico padre pobre.mp3', 'Robert Kiyosaki', 'Financieros', 'padre rico padre pobre_1740766468', 2),
(124, 'Cocinando la piel de la luna', 'Es una colección de poesías alusivas a la luna y al amor de pareja.', 215, 13, '2025-02-28 18:24:49', 'portada.jpg', 'contraportada.jpg', NULL, 'Roberto Alonzo', 'Tlacuache', 'cocinando la piel de la luna_1740767086', 1),
(125, 'Cuentos de un maestro que es puro cuento', 'Es difícil para un docente de provincia que su participación se a valorado de la misma forma que a los compañeros de la capital del Estado.\r\n', 220, 9, '2025-02-28 18:31:45', 'portada.jpg', 'contraportada.jpg', NULL, 'Manuel Reyes García', 'Tlacuache', 'cuentos de un maestro que es puro cuento_1740767502', 1),
(126, 'Tres metros de Infancia', 'Es un libro donde el autor expresa vivencias que tuvo en su infancia siendo el de en medio de tres hermanos', 219, 12, '2025-02-28 18:38:32', 'portada.jpg', 'contraportada.jpg', NULL, 'Roberto Alonzo', 'Tlacuache', 'tres metros de infancia_1740767909', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `role`) VALUES
(1, 'admin'),
(2, 'client');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `type_products`
--

CREATE TABLE `type_products` (
  `id` int(11) NOT NULL,
  `types` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `type_products`
--

INSERT INTO `type_products` (`id`, `types`) VALUES
(1, 'Libros'),
(2, 'Audiolibros');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `apellido_paterno` varchar(50) NOT NULL,
  `apellido_materno` varchar(50) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `edad` int(11) NOT NULL CHECK (`edad` between 18 and 120),
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `apellido_paterno`, `apellido_materno`, `telefono`, `edad`, `role_id`) VALUES
(6, 'omar', 'omar@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'gomez', 'rivera', '5656565655', 25, 2),
(7, 'sol', 'sol@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'rios', 'florin', '1111111111', 25, 2),
(8, 'kevin', 'kevin@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'ramirez', 'vite', '5618178150', 27, 2),
(9, 'Aarón', '2302amt@gmail.com', '6e3abbaab0913dc5f5e54f6a66fffc098d7c2d99', 'Mora', 'Torres', '5564937506', 24, 1),
(12, 'Aaron', 'aaron@gmail.com', '6e3abbaab0913dc5f5e54f6a66fffc098d7c2d99', 'Mora', 'Torres', '5531088320', 25, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `pid` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(100) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indices de la tabla `payment_items`
--
ALTER TABLE `payment_items`
  ADD PRIMARY KEY (`payment_item_id`),
  ADD KEY `payment_id` (`payment_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_category` (`category_id`),
  ADD KEY `fk_type_id` (`type_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `type_products`
--
ALTER TABLE `type_products`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_role_id` (`role_id`);

--
-- Indices de la tabla `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=195;

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT de la tabla `payment_items`
--
ALTER TABLE `payment_items`
  MODIFY `payment_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `type_products`
--
ALTER TABLE `type_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `payment_items`
--
ALTER TABLE `payment_items`
  ADD CONSTRAINT `payment_items_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`payment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_type_id` FOREIGN KEY (`type_id`) REFERENCES `type_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
