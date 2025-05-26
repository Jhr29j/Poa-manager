-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-05-2025 a las 17:58:05
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
-- Base de datos: `poa_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades`
--

CREATE TABLE `actividades` (
  `id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `responsable_id` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `presupuesto_asignado` decimal(12,2) NOT NULL,
  `estado` enum('pendiente','en_progreso','completada') DEFAULT 'pendiente',
  `gasto_real` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planes`
--

CREATE TABLE `planes` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `objetivo_general` text DEFAULT NULL,
  `año` year(4) DEFAULT NULL,
  `estado` enum('borrador','en_revision','aprobado') DEFAULT 'borrador',
  `presupuesto` decimal(12,2) DEFAULT NULL,
  `responsable_id` int(11) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `presupuesto_total` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `planes`
--

INSERT INTO `planes` (`id`, `titulo`, `descripcion`, `objetivo_general`, `año`, `estado`, `presupuesto`, `responsable_id`, `creado_en`, `presupuesto_total`) VALUES
(13, 'PLAN TI', 'Capacitación técnica', 'Capacitar a todos lo profesores de materias técnicas, mediante cursos', '2025', 'borrador', 24000.00, 3, '2025-05-25 13:05:50', 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `primer_nombre` varchar(50) NOT NULL,
  `segundo_nombre` varchar(50) DEFAULT NULL,
  `primer_apellido` varchar(50) NOT NULL,
  `segundo_apellido` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `rol` enum('administrador','editor','lector') DEFAULT 'lector',
  `telefono` varchar(20) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `genero` enum('masculino','femenino','otro') DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `protegido` tinyint(1) DEFAULT 0,
  `es_super_admin` tinyint(1) DEFAULT 0,
  `ultimo_acceso` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `email`, `contraseña`, `rol`, `telefono`, `fecha_nacimiento`, `genero`, `activo`, `creado_en`, `actualizado_en`, `protegido`, `es_super_admin`, `ultimo_acceso`) VALUES
(3, 'Joelvis', '', 'Henriquez', 'Henríquez', 'Joelvishenriquez@gmail.com', '$2y$10$zGNns3mJVGLgG5SL8PO31Ovn46cCF0s0SqCKf0MJlxOyR.Nr2nBXe', 'administrador', '8094526104', '2009-09-29', 'masculino', 1, '2025-05-18 17:34:51', '2025-05-25 15:49:07', 1, 1, '2025-05-25 11:49:07'),
(31, 'Jaziel', '', 'Henriquez', 'Ramirez', 'Jazielhenriquezramirez31@gmail.com', '$2y$10$RNy41.R7cZuEZPC3QvQ3..yOLXIgRKSP3WGDW4QM4PVhF0ejAwwSK', 'administrador', '', '2016-12-31', 'masculino', 1, '2025-05-25 14:38:52', '2025-05-25 15:39:02', 0, 0, '2025-05-25 11:39:02'),
(32, 'Joel', '', 'Henriquez', 'Minaya', 'Joelhenriquezminaya@gmail.com', '$2y$10$4UedtF7k3SMyqFH8iFTG.eZ2hhjZTsQlTtorNJC4szB8q4PsYFsZW', 'administrador', NULL, NULL, NULL, 1, '2025-05-25 14:45:17', '2025-05-25 15:07:22', 0, 0, '2025-05-25 11:07:22');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plan_id` (`plan_id`),
  ADD KEY `responsable_id` (`responsable_id`);

--
-- Indices de la tabla `planes`
--
ALTER TABLE `planes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `responsable_id` (`responsable_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividades`
--
ALTER TABLE `actividades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `planes`
--
ALTER TABLE `planes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD CONSTRAINT `actividades_ibfk_1` FOREIGN KEY (`plan_id`) REFERENCES `planes` (`id`),
  ADD CONSTRAINT `actividades_ibfk_2` FOREIGN KEY (`responsable_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `planes`
--
ALTER TABLE `planes`
  ADD CONSTRAINT `planes_ibfk_1` FOREIGN KEY (`responsable_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
