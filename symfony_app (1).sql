-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 04-04-2025 a las 12:19:37
-- Versión del servidor: 8.0.41-0ubuntu0.22.04.1
-- Versión de PHP: 8.1.2-1ubuntu2.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `symfony_app`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acceso_partes`
--

CREATE TABLE `acceso_partes` (
  `id` int NOT NULL,
  `trabajador_id` int NOT NULL,
  `parte_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `acceso_partes`
--

INSERT INTO `acceso_partes` (`id`, `trabajador_id`, `parte_id`) VALUES
(1, 1, 1),
(2, 1, 3),
(3, 2, 2),
(5, 2, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partes_proyecto`
--

CREATE TABLE `partes_proyecto` (
  `id` int NOT NULL,
  `proyecto_id` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `partes_proyecto`
--

INSERT INTO `partes_proyecto` (`id`, `proyecto_id`, `nombre`, `descripcion`) VALUES
(1, 1, 'Maquetación Album', NULL),
(2, 1, 'Toma de datos', NULL),
(3, 2, 'Maquetación Album', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos`
--

CREATE TABLE `proyectos` (
  `id` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `proyectos`
--

INSERT INTO `proyectos` (`id`, `nombre`, `descripcion`) VALUES
(1, 'CARPA PACO ALONSO', NULL),
(2, 'LOS MANJARES', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiempo`
--

CREATE TABLE `tiempo` (
  `id` int NOT NULL,
  `trabajador_id` int NOT NULL,
  `proyecto_id` int NOT NULL,
  `parte_id` int NOT NULL,
  `inicio` datetime NOT NULL,
  `fin` datetime DEFAULT NULL,
  `tiempo_total` int DEFAULT NULL,
  `pausado` tinyint(1) DEFAULT '0',
  `tiempo_pausado` int DEFAULT '0',
  `ultima_pausa` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tiempo`
--

INSERT INTO `tiempo` (`id`, `trabajador_id`, `proyecto_id`, `parte_id`, `inicio`, `fin`, `tiempo_total`, `pausado`, `tiempo_pausado`, `ultima_pausa`) VALUES
(7, 1, 1, 1, '2025-04-02 10:45:14', '2025-04-02 10:45:38', 19, 0, 5, NULL),
(8, 1, 1, 1, '2025-04-02 10:49:17', '2025-04-02 10:49:26', 9, 1, 0, '2025-04-02 10:49:23'),
(10, 2, 1, 2, '2025-04-02 11:21:04', '2025-04-02 11:21:19', 13, 0, 2, NULL),
(11, 1, 1, 1, '2025-04-03 13:05:31', NULL, NULL, 0, 0, NULL),
(12, 1, 1, 1, '2025-04-03 14:28:29', '2025-04-03 14:28:41', 10, 0, 2, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajadores`
--

CREATE TABLE `trabajadores` (
  `id` int NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `dni` varchar(9) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `departamento` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `trabajadores`
--

INSERT INTO `trabajadores` (`id`, `nombre`, `apellido`, `dni`, `telefono`, `email`, `departamento`) VALUES
(1, 'Alberto', 'Rodríguez', '31876703z', '644969315', 'aporcuna2003@gmail.com', 'Informartica'),
(2, 'Francisco', 'Pastor', '12345678a', '611111111', 'tecnico@azabacheingenieria.es', 'Deliente'),
(3, 'Rafa', 'Castillero', '20952204d', '674263854', 'casti4mtb2003@gmail.com', 'Informartica'),
(4, 'Moises', 'Requena', '2092204d', '67426854', 'casti4mb2003@gmail.com', 'Informartica');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `trabajador_id` int DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('admin','supervisor','empleado') DEFAULT 'empleado',
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `trabajador_id`, `username`, `password_hash`, `rol`, `activo`, `fecha_creacion`) VALUES
(1, 1, 'Alberto Rodriguez', '$2y$10$bVMcFvsFwzEW5dltTaF2m.B2vtCBPkVk3zwLW4QP4h3Ln8e.r2zTa', 'admin', 1, '2025-04-01 12:56:02'),
(3, 2, 'Francisco Pastor', '$2y$10$oP2dXweRm3tz6hinb75bguCEL55PK8iqZD.yQL5v4j.ROsXJxzp9.', 'empleado', 1, '2025-04-02 08:32:34'),
(4, 3, 'Rafa Castillero', '$2y$10$bmBHdH3xhgdduxHwtOfKb.FvC54nIra5gvKNCkvxe/6T2nJ8SdZbO', 'admin', 1, '2025-04-02 09:20:45'),
(5, 4, 'Moises Requena', '$2y$10$SIRmYTSnpEtjDeEb3KhFeeYlfWqIHp6dsnMzaz79AtIC9bpORid8C', 'admin', 1, '2025-04-03 12:32:02');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `acceso_partes`
--
ALTER TABLE `acceso_partes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trabajador_id` (`trabajador_id`),
  ADD KEY `parte_id` (`parte_id`);

--
-- Indices de la tabla `partes_proyecto`
--
ALTER TABLE `partes_proyecto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proyecto_id` (`proyecto_id`);

--
-- Indices de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tiempo`
--
ALTER TABLE `tiempo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trabajador_id` (`trabajador_id`),
  ADD KEY `proyecto_id` (`proyecto_id`),
  ADD KEY `parte_id` (`parte_id`);

--
-- Indices de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `trabajador_id` (`trabajador_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `acceso_partes`
--
ALTER TABLE `acceso_partes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `partes_proyecto`
--
ALTER TABLE `partes_proyecto`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tiempo`
--
ALTER TABLE `tiempo`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `acceso_partes`
--
ALTER TABLE `acceso_partes`
  ADD CONSTRAINT `acceso_partes_ibfk_1` FOREIGN KEY (`trabajador_id`) REFERENCES `trabajadores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `acceso_partes_ibfk_2` FOREIGN KEY (`parte_id`) REFERENCES `partes_proyecto` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `partes_proyecto`
--
ALTER TABLE `partes_proyecto`
  ADD CONSTRAINT `partes_proyecto_ibfk_1` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tiempo`
--
ALTER TABLE `tiempo`
  ADD CONSTRAINT `tiempo_ibfk_1` FOREIGN KEY (`trabajador_id`) REFERENCES `trabajadores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tiempo_ibfk_2` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tiempo_ibfk_3` FOREIGN KEY (`parte_id`) REFERENCES `partes_proyecto` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`trabajador_id`) REFERENCES `trabajadores` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
