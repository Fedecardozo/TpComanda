-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-11-2023 a las 00:44:02
-- Versión del servidor: 10.4.25-MariaDB
-- Versión de PHP: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `comanda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles`
--

CREATE TABLE `detalles` (
  `id` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `duracion` int(11) DEFAULT NULL,
  `id_sector` int(11) NOT NULL,
  `estado` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuestas`
--

CREATE TABLE `encuestas` (
  `id` int(11) NOT NULL,
  `mesa` int(11) NOT NULL,
  `restaurante` int(11) NOT NULL,
  `mozo` int(11) NOT NULL,
  `cocinero` int(11) NOT NULL,
  `estrellas` int(11) NOT NULL,
  `tipo` varchar(20) NOT NULL,
  `texto` varchar(68) NOT NULL,
  `codigo_pedido` varchar(7) NOT NULL,
  `codigo_mesa` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `id` int(11) UNSIGNED NOT NULL,
  `codigo` varchar(10) DEFAULT NULL,
  `codigo_pedido` varchar(10) DEFAULT NULL,
  `estado` varchar(50) NOT NULL,
  `nombreCliente` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`id`, `codigo`, `codigo_pedido`, `estado`, `nombreCliente`) VALUES
(1000, 'y4svd', NULL, 'Cerrada', NULL),
(1001, 'CcgRR', NULL, 'Cerrada', NULL),
(1002, 'YcIGU', NULL, 'Cerrada', NULL),
(1003, 'V9c44', NULL, 'Cerrada', NULL),
(1004, 'lWjTU', NULL, 'Cerrada', NULL),
(1005, '8v9Wx', NULL, 'Cerrada', NULL),
(1006, 'Dsw8d', NULL, 'Cerrada', NULL),
(1007, '5k2d5', NULL, 'Cerrada', NULL),
(1008, 'e8V05', NULL, 'Cerrada', NULL),
(1009, 'bksT2', NULL, 'Cerrada', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_mesa` int(11) UNSIGNED NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `fechaInicio` datetime NOT NULL,
  `fechaEntrega` datetime DEFAULT NULL,
  `destino` varchar(50) DEFAULT NULL,
  `imagen` blob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `tipo` varchar(20) NOT NULL,
  `precio` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `tipo`, `precio`) VALUES
(1000, 'Ruttini', 'Trago-Vino', 25000),
(1001, 'Daikiri', 'Trago-Vino', 2500),
(1002, 'Fernet', 'Trago-Vino', 2500),
(1003, 'Martini', 'Trago-Vino', 2500),
(1004, 'Gin-Tonic', 'Trago-Vino', 2500),
(1005, 'Negroni', 'Trago-Vino', 2500),
(1006, 'Mojito', 'Trago-Vino', 2500),
(1007, 'Margarita', 'Trago-Vino', 2500),
(1008, 'Agua', 'Trago-Vino', 500),
(1009, 'Coca cola', 'Trago-Vino', 1500),
(1010, 'Sprite', 'Trago-Vino', 1500),
(1011, 'Honey', 'Cerveza', 1500),
(1012, 'Ipa', 'Cerveza', 1500),
(1013, 'Blonde', 'Cerveza', 1500),
(1014, 'Belgia', 'Cerveza', 1500),
(1015, 'Empanadas', 'Comida', 1500),
(1016, 'Pizza', 'Comida', 1500),
(1017, 'Asado', 'Comida', 1500),
(1018, 'Bife de chorizo', 'Comida', 3500),
(1019, 'Lasagna', 'Comida', 3500),
(1020, 'Milanesa a caballo', 'Comida', 3500),
(1021, 'Cangrebuguer', 'Comida', 3500),
(1022, 'Hamburguesa', 'Comida', 3500),
(1023, 'Big Mac', 'Comida', 3500),
(1024, 'Lemon Pie', 'Postre', 3500),
(1025, 'Helado', 'Postre', 3500),
(1026, 'Chocotorta', 'Postre', 3500),
(1027, 'Tarta de manzana', 'Postre', 3500),
(1028, 'Tiramisu', 'Postre', 3500),
(1029, 'Hamburguesa Garbanzo', 'Comida', 3500),
(1030, 'Corona', 'Cerveza', 3500);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sector`
--

CREATE TABLE `sector` (
  `id` int(11) NOT NULL,
  `nombre_sector` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `sector`
--

INSERT INTO `sector` (`id`, `nombre_sector`) VALUES
(1, 'Cocina'),
(2, 'Barra de tragos y vinos'),
(3, 'Barra de choperas'),
(4, 'Candy Bar');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `dni` varchar(50) NOT NULL,
  `puesto` varchar(50) NOT NULL,
  `fechaAlta` datetime NOT NULL,
  `fechaBaja` datetime DEFAULT NULL,
  `estado` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `dni`, `puesto`, `fechaAlta`, `fechaBaja`, `estado`) VALUES
(1000, 'Federico', '40812362', 'Mozo', '2023-11-06 14:56:59', NULL, 'Activo'),
(1001, 'Leandro', '38853892', 'Mozo', '2023-11-10 19:16:03', NULL, 'Activo'),
(1002, 'Marcos', '38877792', 'Mozo', '2023-11-10 19:17:03', NULL, 'Activo'),
(1003, 'Nahuel', '41234792', 'Bartender', '2023-11-10 19:18:20', NULL, 'Activo'),
(1004, 'Franco', '40142592', 'Bartender', '2023-11-10 19:18:58', NULL, 'Activo'),
(1005, 'Claribel', '41623988', 'Bartender', '2023-11-10 19:20:14', NULL, 'Activo'),
(1006, 'Alan', '35853126', 'Cervecero', '2023-11-10 19:20:53', NULL, 'Activo'),
(1007, 'Julian', '39851266', 'Cervecero', '2023-11-10 19:21:16', NULL, 'Activo'),
(1008, 'Miguel', '14970895', 'Cocinero', '2023-11-10 19:22:06', NULL, 'Activo'),
(1009, 'Melanie', '15478852', 'Cocinero', '2023-11-10 19:22:20', NULL, 'Activo'),
(1010, 'Valeria', '37895025', 'Cocinero', '2023-11-10 19:22:35', NULL, 'Activo'),
(1011, 'Pablo', '27852369', 'Socio', '2023-11-10 19:23:55', NULL, 'Activo'),
(1012, 'Ayelen', '32598861', 'Socio', '2023-11-10 19:24:13', NULL, 'Activo'),
(1013, 'Juan', '36985741', 'Admin', '2023-11-10 19:24:36', NULL, 'Activo'),
(1014, 'Osvaldo', '111455573', 'Mozo', '2023-11-10 19:26:14', NULL, 'Activo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `detalles`
--
ALTER TABLE `detalles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_pedido` (`codigo_pedido`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sector`
--
ALTER TABLE `sector`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `detalles`
--
ALTER TABLE `detalles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1009;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1031;

--
-- AUTO_INCREMENT de la tabla `sector`
--
ALTER TABLE `sector`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1015;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
