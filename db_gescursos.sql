-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-09-2025 a las 22:43:03
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `db_gescursos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignacion_curso`
--

CREATE TABLE `asignacion_curso` (
  `id_asignacion` int(11) NOT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `id_profesor` int(11) DEFAULT NULL,
  `id_estudiante` int(11) DEFAULT NULL,
  `fecha_asignacion` date DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asignacion_curso`
--

INSERT INTO `asignacion_curso` (`id_asignacion`, `id_curso`, `id_profesor`, `id_estudiante`, `fecha_asignacion`, `comentarios`, `estado`) VALUES
(22, 2, 9, NULL, '2024-10-23', NULL, 'activo'),
(23, 10, 9, NULL, '2024-10-29', NULL, 'activo'),
(24, 11, 10, NULL, '2025-07-15', NULL, 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asig_modulo`
--

CREATE TABLE `asig_modulo` (
  `id_asig_modulo` int(11) NOT NULL,
  `id_modulo` int(11) DEFAULT NULL,
  `id_tipo_usuario` int(11) DEFAULT NULL,
  `fecha_asig` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asig_modulo`
--

INSERT INTO `asig_modulo` (`id_asig_modulo`, `id_modulo`, `id_tipo_usuario`, `fecha_asig`) VALUES
(11, 2, 37, NULL),
(13, 2, 1, NULL),
(14, 3, 1, NULL),
(17, 5, 1, NULL),
(18, 6, 1, NULL),
(19, 7, 1, NULL),
(20, 8, 1, NULL),
(22, 9, 1, NULL),
(23, 10, 1, NULL),
(24, 10, 3, NULL),
(25, 11, 1, NULL),
(26, 11, 3, NULL),
(27, 12, 1, NULL),
(28, 13, 3, NULL),
(29, 13, 2, NULL),
(30, 14, 2, NULL),
(31, 15, 3, NULL),
(32, 10, 2, NULL),
(33, 11, 2, NULL),
(37, 11, 4, NULL),
(38, 17, 4, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia`
--

CREATE TABLE `asistencia` (
  `id_asistencia` int(11) NOT NULL,
  `id_estudiante` int(11) DEFAULT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `presente` enum('si','no','','') DEFAULT NULL,
  `justificacion` text DEFAULT NULL,
  `estado` enum('presente','ausente','retardo') NOT NULL DEFAULT 'ausente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asistencia`
--

INSERT INTO `asistencia` (`id_asistencia`, `id_estudiante`, `id_curso`, `fecha`, `presente`, `justificacion`, `estado`) VALUES
(4, 1, 1, '2024-10-13', 'si', NULL, 'ausente'),
(5, 1, 1, '2024-10-13', 'no', NULL, 'ausente'),
(6, 1, 1, '2024-10-13', NULL, NULL, 'presente'),
(7, 1, 1, '2024-12-04', NULL, NULL, 'ausente'),
(8, 1, 10, '2024-10-30', NULL, NULL, 'ausente'),
(9, 29, 11, '2025-07-15', NULL, NULL, 'presente'),
(10, 30, 2, '2025-09-14', NULL, NULL, 'presente'),
(11, 25, 2, '2025-09-14', NULL, NULL, 'ausente'),
(12, 27, 2, '2025-09-14', NULL, NULL, 'ausente'),
(13, 5, 2, '2025-09-14', NULL, NULL, 'presente'),
(14, 26, 2, '2025-09-14', NULL, NULL, 'ausente'),
(15, 16, 2, '2025-09-14', NULL, NULL, 'ausente'),
(16, 28, 2, '2025-09-14', NULL, NULL, 'presente'),
(17, 13, 2, '2025-09-14', NULL, NULL, 'presente'),
(18, 11, 2, '2025-09-14', NULL, NULL, 'presente'),
(19, 29, 2, '2025-09-14', NULL, NULL, 'ausente'),
(20, 32, 2, '2025-09-14', NULL, NULL, 'presente'),
(21, 31, 2, '2025-09-14', NULL, NULL, 'ausente'),
(22, 30, 2, '2025-09-14', NULL, NULL, 'presente'),
(23, 25, 2, '2025-09-14', NULL, NULL, 'ausente'),
(24, 27, 2, '2025-09-14', NULL, NULL, 'ausente'),
(25, 5, 2, '2025-09-14', NULL, NULL, 'presente'),
(26, 26, 2, '2025-09-14', NULL, NULL, 'ausente'),
(27, 16, 2, '2025-09-14', NULL, NULL, 'ausente'),
(28, 28, 2, '2025-09-14', NULL, NULL, 'presente'),
(29, 13, 2, '2025-09-14', NULL, NULL, 'presente'),
(30, 11, 2, '2025-09-14', NULL, NULL, 'presente'),
(31, 29, 2, '2025-09-14', NULL, NULL, 'ausente'),
(32, 32, 2, '2025-09-14', NULL, NULL, 'presente'),
(33, 31, 2, '2025-09-14', NULL, NULL, 'ausente'),
(34, 30, 2, '2025-09-14', NULL, NULL, 'presente'),
(35, 25, 2, '2025-09-14', NULL, NULL, 'ausente'),
(36, 27, 2, '2025-09-14', NULL, NULL, 'ausente'),
(37, 5, 2, '2025-09-14', NULL, NULL, 'presente'),
(38, 26, 2, '2025-09-14', NULL, NULL, 'ausente'),
(39, 16, 2, '2025-09-14', NULL, NULL, 'ausente'),
(40, 33, 2, '2025-09-14', NULL, NULL, 'retardo'),
(41, 28, 2, '2025-09-14', NULL, NULL, 'presente'),
(42, 13, 2, '2025-09-14', NULL, NULL, 'presente'),
(43, 11, 2, '2025-09-14', NULL, NULL, 'presente'),
(44, 29, 2, '2025-09-14', NULL, NULL, 'presente'),
(45, 32, 2, '2025-09-14', NULL, NULL, 'presente'),
(46, 31, 2, '2025-09-14', NULL, NULL, 'ausente'),
(47, 30, 2, '2025-09-14', NULL, NULL, 'presente'),
(48, 25, 2, '2025-09-14', NULL, NULL, 'ausente'),
(49, 27, 2, '2025-09-14', NULL, NULL, 'ausente'),
(50, 5, 2, '2025-09-14', NULL, NULL, 'presente'),
(51, 26, 2, '2025-09-14', NULL, NULL, 'ausente'),
(52, 16, 2, '2025-09-14', NULL, NULL, 'ausente'),
(53, 33, 2, '2025-09-14', NULL, NULL, 'retardo'),
(54, 28, 2, '2025-09-14', NULL, NULL, 'presente'),
(55, 13, 2, '2025-09-14', NULL, NULL, 'presente'),
(56, 11, 2, '2025-09-14', NULL, NULL, 'presente'),
(57, 29, 2, '2025-09-14', NULL, NULL, 'presente'),
(58, 32, 2, '2025-09-14', NULL, NULL, 'presente'),
(59, 31, 2, '2025-09-14', NULL, NULL, 'ausente'),
(60, 30, 2, '2025-09-14', NULL, NULL, 'presente'),
(61, 25, 2, '2025-09-14', NULL, NULL, 'presente'),
(62, 27, 2, '2025-09-14', NULL, NULL, 'presente'),
(63, 5, 2, '2025-09-14', NULL, NULL, 'presente'),
(64, 26, 2, '2025-09-14', NULL, NULL, 'ausente'),
(65, 16, 2, '2025-09-14', NULL, NULL, 'ausente'),
(66, 33, 2, '2025-09-14', NULL, NULL, 'retardo'),
(67, 28, 2, '2025-09-14', NULL, NULL, 'presente'),
(68, 13, 2, '2025-09-14', NULL, NULL, 'presente'),
(69, 11, 2, '2025-09-14', NULL, NULL, 'presente'),
(70, 29, 2, '2025-09-14', NULL, NULL, 'presente'),
(71, 32, 2, '2025-09-14', NULL, NULL, 'presente'),
(72, 31, 2, '2025-09-14', NULL, NULL, 'presente'),
(73, 30, 2, '2025-09-15', NULL, NULL, 'presente'),
(74, 28, 2, '2025-09-15', NULL, NULL, 'presente'),
(75, 5, 2, '2025-09-15', NULL, NULL, 'ausente'),
(76, 13, 2, '2025-09-15', NULL, NULL, 'ausente'),
(77, 11, 2, '2025-09-15', NULL, NULL, 'presente'),
(78, 33, 2, '2025-09-15', NULL, NULL, 'presente'),
(79, 32, 2, '2025-09-15', NULL, NULL, 'presente'),
(80, 30, 2, '2025-09-20', NULL, NULL, 'presente'),
(81, 28, 2, '2025-09-20', NULL, NULL, 'presente'),
(82, 5, 2, '2025-09-20', NULL, NULL, 'presente'),
(83, 13, 2, '2025-09-20', NULL, NULL, 'presente'),
(84, 11, 2, '2025-09-20', NULL, NULL, 'presente'),
(85, 33, 2, '2025-09-20', NULL, NULL, 'presente'),
(86, 32, 2, '2025-09-20', NULL, NULL, 'presente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carousel`
--

CREATE TABLE `carousel` (
  `id_carrousel` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `order_index` int(11) NOT NULL,
  `fecha_curso_inicio` date NOT NULL,
  `fecha_curso_fin` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carousel`
--

INSERT INTO `carousel` (`id_carrousel`, `title`, `description`, `image`, `order_index`, `fecha_curso_inicio`, `fecha_curso_fin`) VALUES
(50, 'Curso De Banda', 'Anímate a inscribirte a nuestro curso de banda, marcha con el corsaje', '452083467_901651245322247_7937504907709867816_n.jpg', 0, '2025-08-22', '2026-08-22'),
(51, 'Curso de Pastoral', 'Anímate a incribirte a nuestro curso de pastoral y descubre tu potencial.', '448172350_874612134692825_6660471295016287333_n.jpg', 0, '2025-02-25', '2025-02-15'),
(52, 'Sinfonica', 'Animate a inscribirte a nuestro curos de sinfónica, descubre tu nota musical', '441954466_859522112868494_5875778135578260400_n.jpg', 0, '2025-08-15', '2025-08-15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria_curso`
--

CREATE TABLE `categoria_curso` (
  `id_categoria` int(11) NOT NULL,
  `nombre_categoria` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria_curso`
--

INSERT INTO `categoria_curso` (`id_categoria`, `nombre_categoria`) VALUES
(1, 'Danza'),
(2, 'Ajedrez'),
(3, 'Pastoral'),
(6, 'Sin categoria');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id_curso` int(11) NOT NULL,
  `nombre_curso` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `nivel_educativo` enum('primaria','secundaria','terciaria') NOT NULL,
  `duracion` int(3) NOT NULL COMMENT 'Duración en semanas',
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `icono` varchar(255) DEFAULT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `id_profesor` int(11) DEFAULT NULL,
  `horarios` text DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id_curso`, `nombre_curso`, `descripcion`, `nivel_educativo`, `duracion`, `estado`, `icono`, `id_categoria`, `id_profesor`, `horarios`, `id_usuario`) VALUES
(1, 'Danzas', 'Curso de danzas', 'primaria', 3, 'activo', 'icon_66df8b8983f491.65048104.jpg', 1, NULL, NULL, NULL),
(2, 'Ajedrez', 'Curso de ajedrez', 'terciaria', 3, 'activo', 'icon_66df8ca659c689.92859720.jpg', 2, NULL, NULL, NULL),
(10, 'Baloncesto', 'Participa y juega', 'secundaria', 12, 'activo', 'icon_671a4b78f2a112.67630683.jpg', 1, NULL, NULL, NULL),
(11, 'Natación', 'nananansns', 'secundaria', 12, 'activo', 'icon_6876be379f8f80.46852633.jpg', 6, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `db_gescursoslecturas_mensajes`
--

CREATE TABLE `db_gescursoslecturas_mensajes` (
  `id` int(11) NOT NULL,
  `id_mensaje` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_lectura` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `db_gescursoslecturas_mensajes`
--

INSERT INTO `db_gescursoslecturas_mensajes` (`id`, `id_mensaje`, `id_usuario`, `fecha_lectura`) VALUES
(1, 1, 56, '2024-10-06 23:44:10'),
(2, 4, 56, '2024-10-09 19:16:08'),
(3, 5, 56, '2024-10-09 19:16:08'),
(4, 6, 56, '2024-10-09 19:16:07'),
(5, 7, 56, '2024-10-09 19:16:06'),
(6, 9, 56, '2024-10-06 18:46:24'),
(14, 10, 56, '2024-10-07 00:06:58'),
(15, 12, 56, '2024-10-07 00:07:01'),
(23, 13, 56, '2024-10-06 18:34:53'),
(28, 14, 56, '2024-10-06 18:22:01'),
(30, 15, 56, '2024-10-06 18:36:18'),
(31, 16, 56, '2024-10-06 18:22:03'),
(32, 17, 56, '2024-10-06 18:22:03'),
(45, 11, 56, '2024-10-06 18:37:59'),
(101, 18, 56, '2024-10-06 18:34:24'),
(162, 19, 56, '2024-10-07 00:07:00'),
(165, 20, 56, '2024-10-06 23:51:51'),
(227, 22, 56, '2024-10-07 00:07:00'),
(418, 26, 56, '2024-10-07 00:05:24'),
(419, 26, 53, '2024-10-06 23:28:43'),
(420, 24, 53, '2024-10-06 23:28:43'),
(421, 22, 53, '2024-10-06 23:28:25'),
(426, 20, 53, '2024-10-06 23:27:43'),
(427, 6, 53, '2024-10-06 23:27:47'),
(428, 4, 53, '2024-10-06 23:27:48'),
(429, 5, 53, '2024-10-06 23:27:39'),
(434, 19, 53, '2024-10-06 23:27:43'),
(435, 7, 53, '2024-10-06 23:27:47'),
(436, 12, 53, '2024-10-06 23:27:44'),
(437, 10, 53, '2024-10-06 23:28:26'),
(441, 1, 53, '2024-10-06 23:27:48'),
(463, 37, 56, '2024-10-09 19:16:05'),
(466, 38, 56, '2024-10-09 19:16:04'),
(467, 38, 53, '2024-10-08 04:36:41'),
(469, 39, 53, '2024-10-08 19:56:46'),
(471, 40, 56, '2024-10-06 23:37:19'),
(474, 39, 56, '2024-10-09 19:16:04'),
(481, 40, 53, '2024-10-08 19:56:47'),
(484, 41, 56, '2024-10-09 19:16:03'),
(495, 42, 53, '2024-10-06 23:37:41'),
(496, 42, 56, '2024-10-09 19:16:09'),
(501, 24, 56, '2024-10-07 00:05:25'),
(514, 8, 56, '2024-10-09 19:16:07'),
(679, 41, 53, '2024-10-08 19:56:47'),
(685, 37, 53, '2024-10-07 19:57:02'),
(687, 41, 36, '2024-10-07 19:57:48'),
(688, 42, 36, '2024-10-07 19:57:49'),
(732, 49, 56, '2024-10-17 20:33:27'),
(742, 50, 56, '2024-10-17 20:33:27'),
(754, 59, 36, '2024-10-24 15:02:32'),
(755, 61, 36, '2024-10-24 15:23:38'),
(756, 61, 56, '2025-07-13 20:42:01'),
(757, 60, 56, '2025-07-13 18:17:44'),
(758, 58, 56, '2024-10-30 03:25:51'),
(760, 57, 56, '2024-10-30 03:25:52'),
(763, 59, 56, '2025-07-13 18:17:45'),
(770, 58, 71, '2025-07-13 18:31:40'),
(771, 63, 90, '2025-07-13 18:39:41'),
(772, 65, 91, '2025-07-13 19:35:43'),
(773, 69, 91, '2025-07-13 19:35:42'),
(774, 68, 91, '2025-07-13 19:35:42'),
(775, 66, 91, '2025-07-13 19:35:42'),
(777, 91, 56, '2025-07-13 20:43:15'),
(778, 75, 56, '2025-07-13 20:43:15'),
(779, 74, 56, '2025-07-13 20:41:54'),
(784, 73, 56, '2025-07-13 20:40:30'),
(785, 72, 56, '2025-07-13 20:41:55'),
(786, 71, 56, '2025-07-13 20:39:41'),
(787, 91, 93, '2025-07-13 20:54:39'),
(788, 58, 93, '2025-07-13 20:58:00'),
(789, 57, 93, '2025-07-13 20:29:31'),
(790, 56, 93, '2025-07-13 20:28:15'),
(791, 55, 93, '2025-07-13 20:27:14'),
(792, 92, 93, '2025-07-13 20:57:55'),
(797, 54, 93, '2025-07-13 20:23:28'),
(800, 93, 93, '2025-07-13 20:57:55'),
(824, 93, 56, '2025-07-13 20:48:29'),
(825, 92, 56, '2025-07-13 20:43:27'),
(859, 70, 56, '2025-07-13 20:41:56'),
(860, 68, 56, '2025-07-13 20:41:57'),
(861, 69, 56, '2025-07-13 20:41:58'),
(862, 67, 56, '2025-07-13 20:41:59'),
(863, 64, 56, '2025-07-13 20:42:00'),
(875, 94, 93, '2025-07-13 20:54:41'),
(885, 95, 93, '2025-07-13 20:57:20'),
(886, 96, 93, '2025-07-13 20:57:54'),
(892, 118, 56, '2025-07-15 19:32:30'),
(893, 128, 96, '2025-07-15 20:44:07'),
(894, 127, 96, '2025-07-15 20:44:08'),
(897, 93, 98, '2025-09-14 02:17:45'),
(898, 58, 98, '2025-09-14 02:17:46'),
(899, 130, 98, '2025-09-14 03:10:06'),
(900, 130, 56, '2025-09-14 02:18:03'),
(901, 131, 98, '2025-09-14 03:10:06'),
(903, 96, 98, '2025-09-14 03:10:08'),
(904, 124, 98, '2025-09-14 03:10:08'),
(905, 132, 98, '2025-09-14 03:10:31'),
(906, 95, 99, '2025-09-14 04:46:58'),
(907, 94, 99, '2025-09-14 04:47:00'),
(908, 58, 99, '2025-09-14 04:47:02'),
(909, 57, 99, '2025-09-14 04:47:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiante`
--

CREATE TABLE `estudiante` (
  `id_estudiante` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `genero` varchar(10) DEFAULT NULL,
  `fecha_registro` date DEFAULT curdate(),
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `nivel_educativo` varchar(50) DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estudiante`
--

INSERT INTO `estudiante` (`id_estudiante`, `id_usuario`, `genero`, `fecha_registro`, `estado`, `nivel_educativo`, `observaciones`) VALUES
(1, 53, 'M', '5234-05-31', 'activo', 'secundaria', 'Perro marica'),
(3, 69, NULL, '2024-10-24', 'activo', NULL, NULL),
(4, 70, NULL, '2024-10-24', 'activo', NULL, NULL),
(5, 72, NULL, '2025-07-13', 'activo', NULL, NULL),
(6, 73, NULL, '2025-07-13', 'activo', NULL, NULL),
(7, 74, NULL, '2025-07-13', 'activo', NULL, NULL),
(8, 75, NULL, '2025-07-13', 'activo', NULL, NULL),
(9, 76, NULL, '2025-07-13', 'activo', NULL, NULL),
(10, 67, NULL, '2025-07-13', 'activo', NULL, NULL),
(11, 77, NULL, '2025-07-13', 'activo', NULL, NULL),
(12, 78, NULL, '2025-07-13', 'activo', NULL, NULL),
(13, 79, NULL, '2025-07-13', 'activo', NULL, NULL),
(14, 80, NULL, '2025-07-13', 'activo', NULL, NULL),
(15, 81, NULL, '2025-07-13', 'activo', NULL, NULL),
(16, 71, NULL, '2025-07-13', 'activo', NULL, NULL),
(17, 82, NULL, '2025-07-13', 'activo', NULL, NULL),
(18, 83, NULL, '2025-07-13', 'activo', NULL, NULL),
(19, 84, NULL, '2025-07-13', 'activo', NULL, NULL),
(20, 85, NULL, '2025-07-13', 'activo', NULL, NULL),
(21, 86, NULL, '2025-07-13', 'activo', NULL, NULL),
(22, 87, NULL, '2025-07-13', 'activo', NULL, NULL),
(23, 88, NULL, '2025-07-13', 'activo', NULL, NULL),
(24, 89, NULL, '2025-07-13', 'activo', NULL, NULL),
(25, 90, NULL, '2025-07-13', 'activo', NULL, NULL),
(26, 91, NULL, '2025-07-13', 'activo', NULL, NULL),
(27, 92, NULL, '2025-07-13', 'activo', NULL, NULL),
(28, 93, NULL, '2025-07-13', 'activo', NULL, NULL),
(29, 94, NULL, '2025-07-14', 'activo', NULL, NULL),
(30, 95, NULL, '2025-07-14', 'activo', NULL, NULL),
(31, 96, NULL, '2025-07-15', 'activo', NULL, NULL),
(32, 98, NULL, '2025-09-13', 'activo', NULL, NULL),
(33, 99, NULL, '2025-09-13', 'activo', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_inscripciones`
--

CREATE TABLE `historial_inscripciones` (
  `id_historial` int(11) NOT NULL,
  `id_inscripcion` int(11) DEFAULT NULL,
  `estado_anterior` enum('pendiente','aprobada','rechazada','cancelada') DEFAULT NULL,
  `estado_nuevo` enum('pendiente','aprobada','rechazada','cancelada') DEFAULT NULL,
  `fecha_cambio` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_usuario_cambio` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_inscripciones`
--

INSERT INTO `historial_inscripciones` (`id_historial`, `id_inscripcion`, `estado_anterior`, `estado_nuevo`, `fecha_cambio`, `id_usuario_cambio`) VALUES
(92, 77, 'pendiente', 'aprobada', '2024-10-24 02:41:14', 36),
(93, 77, 'aprobada', 'pendiente', '2024-10-24 15:00:26', 36),
(94, 77, 'pendiente', 'aprobada', '2024-10-24 15:13:03', 36),
(95, 77, 'aprobada', 'rechazada', '2024-10-24 15:18:58', 36),
(96, 78, 'pendiente', 'aprobada', '2024-10-30 03:27:17', 56),
(97, 98, 'aprobada', 'rechazada', '2025-07-13 17:59:13', 56),
(98, 97, 'aprobada', 'cancelada', '2025-07-13 17:59:19', 56),
(99, 96, 'aprobada', 'rechazada', '2025-07-13 17:59:40', 56),
(100, 95, 'aprobada', 'rechazada', '2025-07-13 17:59:45', 56),
(101, 86, 'aprobada', 'cancelada', '2025-07-13 18:00:23', 56),
(102, 97, 'cancelada', 'aprobada', '2025-07-13 18:00:35', 56),
(103, 98, 'rechazada', 'aprobada', '2025-07-13 18:01:26', 56),
(104, 96, 'rechazada', 'aprobada', '2025-07-13 18:01:37', 56),
(105, 98, 'aprobada', 'rechazada', '2025-07-13 18:02:06', 56),
(106, 97, 'aprobada', 'rechazada', '2025-07-13 18:03:00', 56),
(107, 98, 'rechazada', 'aprobada', '2025-07-13 18:03:12', 56),
(108, 97, 'rechazada', 'aprobada', '2025-07-13 18:03:18', 56),
(109, 100, 'aprobada', 'rechazada', '2025-07-13 18:09:03', 56),
(110, 82, 'aprobada', 'cancelada', '2025-07-13 18:16:50', 56),
(111, 102, 'aprobada', 'rechazada', '2025-07-13 18:30:23', 56),
(112, 91, 'aprobada', 'rechazada', '2025-07-13 18:35:01', 56),
(113, 94, 'aprobada', 'rechazada', '2025-07-13 18:35:50', 56),
(114, 99, 'aprobada', 'rechazada', '2025-07-13 18:36:20', 56),
(115, 90, 'aprobada', 'rechazada', '2025-07-13 18:36:59', 56),
(116, 92, 'aprobada', 'cancelada', '2025-07-13 18:38:24', 56),
(117, 103, 'aprobada', 'rechazada', '2025-07-13 18:39:31', 56),
(118, 104, 'aprobada', 'rechazada', '2025-07-13 18:44:31', 56),
(119, 105, 'aprobada', 'rechazada', '2025-07-13 18:48:18', 56),
(120, 106, 'aprobada', 'rechazada', '2025-07-13 19:31:33', 56),
(121, 107, 'aprobada', 'rechazada', '2025-07-13 19:33:43', 56),
(122, 108, 'aprobada', 'rechazada', '2025-07-13 19:44:42', 56),
(123, 109, 'aprobada', 'rechazada', '2025-07-13 19:45:30', 56),
(138, 110, 'aprobada', 'rechazada', '2025-07-13 20:14:44', 56),
(139, 111, 'aprobada', 'rechazada', '2025-07-13 20:21:57', 56),
(140, 113, 'aprobada', 'rechazada', '2025-07-15 00:58:43', 56),
(141, 114, 'pendiente', 'aprobada', '2025-07-15 01:01:49', 56),
(142, 114, 'aprobada', 'rechazada', '2025-07-15 01:09:00', 56),
(143, 115, 'pendiente', 'rechazada', '2025-07-15 01:24:53', 56),
(144, 116, 'pendiente', 'aprobada', '2025-07-15 01:25:55', 56),
(145, 116, 'aprobada', 'cancelada', '2025-07-15 01:30:18', 56),
(146, 117, 'pendiente', 'aprobada', '2025-07-15 01:30:39', 56),
(147, 117, 'aprobada', 'rechazada', '2025-07-15 01:31:44', 56),
(148, 118, 'aprobada', 'rechazada', '2025-07-15 01:32:33', 56),
(149, 119, 'pendiente', 'aprobada', '2025-07-15 01:34:48', 56),
(150, 119, 'aprobada', 'rechazada', '2025-07-15 01:36:58', 56),
(151, 120, 'pendiente', 'aprobada', '2025-07-15 01:37:18', 56),
(152, 120, 'aprobada', 'rechazada', '2025-07-15 01:41:44', 56),
(153, 121, 'pendiente', 'aprobada', '2025-07-15 01:41:59', 56),
(154, 121, 'aprobada', 'rechazada', '2025-07-15 01:44:19', 56),
(155, 122, 'pendiente', 'aprobada', '2025-07-15 01:44:35', 56),
(156, 122, 'aprobada', 'rechazada', '2025-07-15 01:53:27', 56),
(157, 123, 'aprobada', 'rechazada', '2025-07-15 01:54:15', 56),
(158, 124, 'pendiente', 'aprobada', '2025-07-15 01:55:06', 56),
(159, 124, 'aprobada', 'rechazada', '2025-07-15 01:58:12', 56),
(160, 125, 'pendiente', 'aprobada', '2025-07-15 01:58:27', 56),
(161, 125, 'aprobada', 'rechazada', '2025-07-15 02:00:03', 56),
(162, 126, 'pendiente', 'aprobada', '2025-07-15 02:00:28', 56),
(163, 126, 'aprobada', 'rechazada', '2025-07-15 02:01:58', 56),
(164, 127, 'pendiente', 'aprobada', '2025-07-15 02:02:23', 56),
(165, 127, 'aprobada', 'rechazada', '2025-07-15 02:02:50', 56),
(166, 128, 'pendiente', 'aprobada', '2025-07-15 02:03:25', 56),
(167, 128, 'aprobada', 'rechazada', '2025-07-15 02:05:16', 56),
(168, 129, 'pendiente', 'aprobada', '2025-07-15 02:05:34', 56),
(169, 129, 'aprobada', 'rechazada', '2025-07-15 02:08:24', 56),
(170, 130, 'pendiente', 'aprobada', '2025-07-15 02:09:01', 56),
(171, 130, 'aprobada', 'rechazada', '2025-07-15 02:14:34', 56),
(172, 131, 'pendiente', 'aprobada', '2025-07-15 02:14:52', 56),
(173, 131, 'aprobada', 'rechazada', '2025-07-15 02:20:16', 56),
(174, 132, 'pendiente', 'aprobada', '2025-07-15 02:20:33', 56),
(175, 132, 'aprobada', 'rechazada', '2025-07-15 02:21:06', 56),
(176, 133, 'aprobada', 'rechazada', '2025-07-15 02:21:42', 56),
(177, 134, 'aprobada', 'rechazada', '2025-07-15 02:23:55', 56),
(178, 135, 'pendiente', 'aprobada', '2025-07-15 02:24:21', 56),
(179, 135, 'aprobada', 'rechazada', '2025-07-15 02:24:33', 56),
(180, 136, 'aprobada', 'rechazada', '2025-07-15 02:36:58', 56),
(181, 137, 'aprobada', 'rechazada', '2025-07-15 02:48:22', 56),
(182, 138, 'aprobada', 'rechazada', '2025-07-15 02:50:12', 56),
(183, 139, 'pendiente', 'aprobada', '2025-07-15 02:50:28', 56),
(184, 140, 'aprobada', 'rechazada', '2025-07-15 19:35:04', 56),
(185, 141, 'aprobada', 'rechazada', '2025-07-15 19:36:43', 56),
(186, 142, 'aprobada', 'rechazada', '2025-07-15 20:44:39', 56),
(187, 145, 'pendiente', 'aprobada', '2025-09-14 03:07:56', 56),
(188, 147, 'aprobada', 'cancelada', '2025-09-14 03:10:21', 56),
(189, 148, 'aprobada', 'cancelada', '2025-09-14 03:16:02', 56);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `id_horario` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_profesor` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `dia_semana` enum('lunes','martes','miércoles','jueves','viernes','sábado') NOT NULL,
  `lunes` varchar(20) DEFAULT NULL,
  `martes` varchar(20) DEFAULT NULL,
  `miercoles` varchar(20) DEFAULT NULL,
  `jueves` varchar(20) DEFAULT NULL,
  `viernes` varchar(20) DEFAULT NULL,
  `sabado` varchar(20) DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `horarios`
--

INSERT INTO `horarios` (`id_horario`, `id_curso`, `id_profesor`, `fecha_creacion`, `dia_semana`, `lunes`, `martes`, `miercoles`, `jueves`, `viernes`, `sabado`, `hora_inicio`, `hora_fin`) VALUES
(37, 2, 9, '2025-09-14 02:25:46', 'lunes', NULL, '10:00 - 12:00', '08:00 - 10:00', NULL, '08:00 - 10:00', '10:00 - 12:00', NULL, NULL),
(38, 10, 9, '2025-09-14 02:49:34', 'lunes', '06:00 - 08:00', '12:00 - 14:00', NULL, NULL, '10:00 - 12:00', NULL, NULL, NULL),
(39, 11, 10, '2025-09-14 13:02:11', 'lunes', '12:00 - 14:00', '12:00 - 14:00', '06:00 - 08:00', NULL, '06:00 - 08:00', NULL, NULL, NULL),
(40, 1, 9, '2025-09-14 13:05:20', 'lunes', NULL, '06:00 - 08:00', NULL, NULL, NULL, '06:00 - 08:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones`
--

CREATE TABLE `inscripciones` (
  `id_inscripcion` int(11) NOT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `id_estudiante` int(11) DEFAULT NULL,
  `fecha_inscripcion` date DEFAULT NULL,
  `estado` enum('pendiente','aprobada','rechazada','cancelada') NOT NULL DEFAULT 'pendiente',
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `comprobante_pago` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inscripciones`
--

INSERT INTO `inscripciones` (`id_inscripcion`, `id_curso`, `id_estudiante`, `fecha_inscripcion`, `estado`, `fecha_actualizacion`, `comprobante_pago`) VALUES
(77, 1, 1, '2024-10-23', 'rechazada', '2024-10-24 15:18:58', '../../uploads/comprobantes/1729737647_coomadenort.jpg'),
(78, 10, 1, '2024-10-29', 'aprobada', '2024-10-30 03:27:17', '../uploads/comprobantes/1730258810_BASKET11.jpg'),
(79, 2, 5, '2025-07-13', 'aprobada', '2025-07-13 15:55:23', NULL),
(80, 1, 6, '2025-07-13', 'aprobada', '2025-07-13 16:13:03', NULL),
(81, 1, 7, '2025-07-13', 'aprobada', '2025-07-13 16:17:45', NULL),
(82, 1, 8, '2025-07-13', 'cancelada', '2025-07-13 18:16:50', NULL),
(83, 10, 9, '2025-07-13', 'aprobada', '2025-07-13 16:26:44', NULL),
(84, 1, 10, '2025-07-13', 'aprobada', '2025-07-13 16:31:57', NULL),
(85, 2, 11, '2025-07-13', 'aprobada', '2025-07-13 16:32:07', NULL),
(86, 1, 12, '2025-07-13', 'cancelada', '2025-07-13 18:00:23', NULL),
(87, 2, 13, '2025-07-13', 'aprobada', '2025-07-13 16:45:25', NULL),
(88, 10, 14, '2025-07-13', 'aprobada', '2025-07-13 16:49:12', NULL),
(89, 1, 15, '2025-07-13', 'aprobada', '2025-07-13 16:52:06', NULL),
(90, 2, 16, '2025-07-13', 'rechazada', '2025-07-13 18:36:59', NULL),
(91, 1, 17, '2025-07-13', 'rechazada', '2025-07-13 18:35:01', NULL),
(92, 1, 18, '2025-07-13', 'cancelada', '2025-07-13 18:38:24', NULL),
(93, 10, 19, '2025-07-13', 'aprobada', '2025-07-13 17:09:48', NULL),
(94, 1, 20, '2025-07-13', 'rechazada', '2025-07-13 18:35:50', NULL),
(95, 1, 21, '2025-07-13', 'rechazada', '2025-07-13 17:59:45', NULL),
(96, 1, 22, '2025-07-13', 'aprobada', '2025-07-13 18:01:37', NULL),
(97, 1, 23, '2025-07-13', 'aprobada', '2025-07-13 18:03:18', NULL),
(98, 1, 24, '2025-07-13', 'aprobada', '2025-07-13 18:03:12', NULL),
(99, 1, 5, '2025-07-13', 'rechazada', '2025-07-13 18:36:20', NULL),
(100, 10, 5, '2025-07-13', 'rechazada', '2025-07-13 18:09:03', NULL),
(101, 1, 16, '2025-07-13', 'aprobada', '2025-07-13 18:11:06', NULL),
(102, 10, 16, '2025-07-13', 'rechazada', '2025-07-13 18:30:23', NULL),
(103, 2, 25, '2025-07-13', 'rechazada', '2025-07-13 18:39:31', NULL),
(104, 1, 25, '2025-07-13', 'rechazada', '2025-07-13 18:44:31', NULL),
(105, 2, 26, '2025-07-13', 'rechazada', '2025-07-13 18:48:18', NULL),
(106, 1, 26, '2025-07-13', 'rechazada', '2025-07-13 19:31:33', NULL),
(107, 10, 26, '2025-07-13', 'rechazada', '2025-07-13 19:33:43', NULL),
(108, 2, 27, '2025-07-13', 'rechazada', '2025-07-13 19:44:42', NULL),
(109, 10, 27, '2025-07-13', 'rechazada', '2025-07-13 19:45:30', NULL),
(110, 2, 28, '2025-07-13', 'rechazada', '2025-07-13 20:14:44', NULL),
(111, 2, 28, '2025-07-13', 'rechazada', '2025-07-13 20:21:57', NULL),
(112, 2, 28, '2025-07-13', 'aprobada', '2025-07-13 20:22:21', NULL),
(113, 2, 29, '2025-07-14', 'rechazada', '2025-07-15 00:58:43', NULL),
(114, 10, 29, '2025-07-14', 'rechazada', '2025-07-15 01:09:00', '../uploads/comprobantes/1752541302_WhatsApp Image 2025-07-14 at 09.59.39.jpeg'),
(115, 2, 29, '2025-07-14', 'rechazada', '2025-07-15 01:24:53', '../uploads/comprobantes/1752542550_WhatsApp Image 2025-07-14 at 09.59.39.jpeg'),
(116, 2, 29, '2025-07-14', 'cancelada', '2025-07-15 01:30:18', '../uploads/comprobantes/1752542735_WhatsApp Image 2025-07-14 at 09.59.39.jpeg'),
(117, 2, 29, '2025-07-14', 'rechazada', '2025-07-15 01:31:44', '../uploads/comprobantes/1752543030_WhatsApp Image 2025-07-14 at 09.59.39.jpeg'),
(118, 2, 29, '2025-07-14', 'rechazada', '2025-07-15 01:32:33', NULL),
(119, 2, 29, '2025-07-14', 'rechazada', '2025-07-15 01:36:58', '../uploads/comprobantes/1752543282_WhatsApp Image 2025-07-14 at 09.59.39.jpeg'),
(120, 2, 29, '2025-07-14', 'rechazada', '2025-07-15 01:41:44', '../uploads/comprobantes/1752543428_WhatsApp Image 2025-07-14 at 09.59.39.jpeg'),
(121, 2, 29, '2025-07-14', 'rechazada', '2025-07-15 01:44:19', '../uploads/comprobantes/1752543713_WhatsApp Image 2025-07-14 at 09.59.39.jpeg'),
(122, 2, 29, '2025-07-14', 'rechazada', '2025-07-15 01:53:27', '../uploads/comprobantes/1752543869_WhatsApp Image 2025-07-14 at 09.59.39.jpeg'),
(123, 2, 29, '2025-07-14', 'rechazada', '2025-07-15 01:54:15', NULL),
(124, 2, 29, '2025-07-14', 'rechazada', '2025-07-15 01:58:12', '../uploads/comprobantes/1752544499_WhatsApp Image 2025-07-14 at 09.59.39.jpeg'),
(125, 2, 29, '2025-07-14', 'rechazada', '2025-07-15 02:00:03', '../uploads/comprobantes/1752544702_WhatsApp Image 2025-07-14 at 09.59.39.jpeg'),
(126, 2, 29, '2025-07-14', 'rechazada', '2025-07-15 02:01:58', '../uploads/comprobantes/1752544823_WhatsApp Image 2025-07-14 at 09.59.39.jpeg'),
(127, 2, 29, '2025-07-14', 'rechazada', '2025-07-15 02:02:50', '../uploads/comprobantes/1752544929_WhatsApp Image 2025-07-14 at 09.59.39.jpeg'),
(128, 2, 29, '2025-07-14', 'rechazada', '2025-07-15 02:05:16', '../uploads/comprobantes/1752544998_WhatsApp Image 2025-07-14 at 09.59.39.jpeg'),
(129, 2, 29, '2025-07-14', 'rechazada', '2025-07-15 02:08:24', '../uploads/comprobantes/1752545125_WhatsApp Image 2025-07-14 at 09.59.39.jpeg'),
(130, 2, 29, '2025-07-14', 'rechazada', '2025-07-15 02:14:34', '../uploads/comprobantes/1752545331_WhatsApp Image 2025-07-14 at 09.59.39.jpeg'),
(131, 2, 29, '2025-07-14', 'rechazada', '2025-07-15 02:20:16', '../uploads/comprobantes/1752545687_WhatsApp Image 2025-07-14 at 09.59.39.jpeg'),
(132, 2, 29, '2025-07-14', 'rechazada', '2025-07-15 02:21:06', '../uploads/comprobantes/1752546028_WhatsApp Image 2025-07-14 at 09.59.39.jpeg'),
(133, 2, 29, '2025-07-14', 'rechazada', '2025-07-15 02:21:42', NULL),
(134, 2, 29, '2025-07-14', 'rechazada', '2025-07-15 02:23:55', NULL),
(135, 2, 29, '2025-07-14', 'rechazada', '2025-07-15 02:24:33', '../uploads/comprobantes/1752546254_WhatsApp Image 2025-07-14 at 09.59.39.jpeg'),
(136, 2, 29, '2025-07-14', 'rechazada', '2025-07-15 02:36:58', NULL),
(137, 2, 30, '2025-07-14', 'rechazada', '2025-07-15 02:48:22', NULL),
(138, 2, 30, '2025-07-14', 'rechazada', '2025-07-15 02:50:12', NULL),
(139, 2, 30, '2025-07-14', 'aprobada', '2025-07-15 02:50:28', '../uploads/comprobantes/1752547822_WhatsApp Image 2025-07-14 at 09.59.39.jpeg'),
(140, 10, 29, '2025-07-15', 'rechazada', '2025-07-15 19:35:04', NULL),
(141, 2, 29, '2025-07-15', 'rechazada', '2025-07-15 19:36:43', NULL),
(142, 2, 31, '2025-07-15', 'rechazada', '2025-07-15 20:44:39', NULL),
(143, 11, 29, '2025-07-15', 'aprobada', '2025-07-15 20:54:27', NULL),
(144, 2, 32, '2025-09-13', 'aprobada', '2025-09-14 02:16:55', NULL),
(145, 1, 32, '2025-09-13', 'aprobada', '2025-09-14 03:07:56', '../uploads/comprobantes/1757819254_mapa navegacion proyecto.png'),
(146, 10, 32, '2025-09-13', 'aprobada', '2025-09-14 03:09:32', NULL),
(147, 11, 32, '2025-09-13', 'cancelada', '2025-09-14 03:10:21', NULL),
(148, 2, 33, '2025-09-13', 'cancelada', '2025-09-14 03:16:02', NULL),
(149, 2, 33, '2025-09-13', 'aprobada', '2025-09-14 03:16:31', NULL),
(150, 11, 33, '2025-09-13', 'aprobada', '2025-09-14 04:38:39', NULL),
(151, 1, 33, '2025-09-13', 'aprobada', '2025-09-14 04:38:42', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `ip`, `timestamp`) VALUES
(13, '127.0.0.1', '2024-10-14 12:16:07'),
(14, '127.0.0.1', '2024-10-24 08:47:47'),
(15, '127.0.0.1', '2024-10-24 08:47:59'),
(16, '127.0.0.1', '2024-10-24 09:46:01'),
(17, '::1', '2025-07-13 11:25:19'),
(18, '::1', '2025-07-13 13:43:45'),
(19, '::1', '2025-07-13 14:54:52'),
(20, '::1', '2025-07-13 14:54:58'),
(21, '::1', '2025-07-13 15:19:40'),
(22, '::1', '2025-07-13 15:58:26'),
(23, '::1', '2025-07-14 19:58:51'),
(24, '::1', '2025-07-14 20:18:38'),
(25, '::1', '2025-07-14 20:18:45'),
(26, '::1', '2025-07-14 20:25:00'),
(27, '::1', '2025-07-14 20:35:04'),
(28, '::1', '2025-07-14 20:37:28'),
(29, '::1', '2025-07-14 21:00:44'),
(30, '::1', '2025-07-14 21:14:14'),
(31, '::1', '2025-07-14 21:15:16'),
(32, '::1', '2025-07-14 21:39:32'),
(33, '::1', '2025-07-14 21:39:38'),
(34, '::1', '2025-07-14 21:47:18'),
(35, '::1', '2025-07-15 15:54:01'),
(36, '::1', '2025-09-13 21:35:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id_mensaje` int(11) NOT NULL,
  `id_remitente` int(11) DEFAULT NULL,
  `tipo_remitente` int(11) DEFAULT NULL,
  `tipo_destinatario` varchar(20) NOT NULL,
  `id_destinatario` int(11) DEFAULT NULL,
  `asunto` varchar(255) DEFAULT NULL,
  `contenido` text DEFAULT NULL,
  `fecha_envio` datetime DEFAULT current_timestamp(),
  `id_tipo_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`id_mensaje`, `id_remitente`, `tipo_remitente`, `tipo_destinatario`, `id_destinatario`, `asunto`, `contenido`, `fecha_envio`, `id_tipo_usuario`) VALUES
(49, 56, NULL, 'individual', 56, 'asdasdasdasdasda', 'asdasd', '2024-10-17 21:50:05', NULL),
(50, 56, NULL, 'todos', NULL, 'jhjh', 'ljkjklj', '2024-10-17 22:19:30', NULL),
(51, 56, NULL, 'individual', 36, 'Sharif', 'adfadjfk', '2024-10-17 23:08:39', NULL),
(52, 56, NULL, 'todos', NULL, 'gjjj', 'vvjvj', '2024-10-17 23:09:49', NULL),
(53, 56, NULL, 'todos', NULL, 'ihhi', 'jhkj', '2024-10-17 23:10:21', NULL),
(54, 56, NULL, 'todos', NULL, 'njlk', 'lnkmmn', '2024-10-17 23:10:45', NULL),
(55, 56, NULL, 'todos', NULL, 'njnn', 'nklnkl', '2024-10-17 23:10:58', NULL),
(56, 56, NULL, 'todos', NULL, 'nknln', 'lknnkllnk', '2024-10-17 23:11:13', NULL),
(57, 56, NULL, 'todos', NULL, 'nknkl', 'llknlnk', '2024-10-17 23:11:36', NULL),
(58, 56, NULL, 'todos', NULL, 'nnmnm', 'mbjkkbj', '2024-10-17 23:11:59', NULL),
(59, 36, NULL, 'individual', 56, 'perro hp', 'su papa', '2024-10-24 10:02:30', NULL),
(60, 53, NULL, 'individual', 56, 'perro hp', 'parea baiar la bamba', '2024-10-24 10:18:20', NULL),
(61, 36, NULL, 'individual', 56, 'TE COMPRO TU NOVIA', 'TE COMPRO TU NOVIA\r\n', '2024-10-24 10:23:35', NULL),
(62, 56, 1, 'individual', 83, 'Tu inscripción ha sido cancelada', 'Motivo: asdasdas', '2025-07-13 13:38:24', NULL),
(63, 56, 1, 'individual', 90, 'Tu inscripción ha sido rechazada', 'Motivo: vimos que faltan algunas cosas', '2025-07-13 13:39:31', NULL),
(64, 56, 1, 'individual', 90, 'Tu inscripción ha sido rechazada', 'Motivo: por lk', '2025-07-13 13:44:31', NULL),
(65, 56, 1, 'individual', 91, 'Tu inscripción ha sido rechazada', 'Motivo: porque no quisimos que este aca', '2025-07-13 13:48:18', NULL),
(66, 56, 1, 'individual', 91, 'Tu preinscripción ha sido rechazada', 'Motivo: asdasd', '2025-07-13 13:52:16', NULL),
(67, 56, 1, 'individual', 90, 'Tu preinscripción ha sido rechazada', 'Motivo: sdasdas', '2025-07-13 13:52:18', NULL),
(68, 56, 1, 'individual', 91, 'Tu inscripción ha sido rechazada', 'Motivo: porque si', '2025-07-13 14:31:33', NULL),
(69, 56, 1, 'individual', 91, 'Tu inscripción ha sido rechazada', 'Motivo: no haz sido elegido', '2025-07-13 14:33:43', NULL),
(70, 56, 1, 'individual', 91, 'Tu preinscripción ha sido rechazada', 'Motivo: asdasd', '2025-07-13 14:35:46', NULL),
(71, 56, 1, 'individual', 91, 'Tu preinscripción ha sido rechazada', 'Motivo: asdasdasd', '2025-07-13 14:37:39', NULL),
(72, 56, 1, 'individual', 92, 'Tu inscripción ha sido rechazada', 'Motivo: asdasdasd', '2025-07-13 14:44:42', NULL),
(73, 56, 1, 'individual', 92, 'Tu preinscripción ha sido rechazada', 'Motivo: asdasd', '2025-07-13 14:45:14', NULL),
(74, 56, 1, 'individual', 92, 'Tu inscripción ha sido rechazada', 'Motivo: asdasd', '2025-07-13 14:45:30', NULL),
(75, 56, 1, 'individual', 92, 'Tu preinscripción ha sido rechazada', 'Motivo: asdasd', '2025-07-13 14:48:16', NULL),
(91, 56, 1, 'individual', 93, 'Tu inscripción ha sido rechazada', 'Motivo: dhdfgfdgf', '2025-07-13 15:14:44', NULL),
(92, 56, 1, 'individual', 93, 'Tu inscripción ha sido rechazada', 'Motivo: ya no te queremos aca', '2025-07-13 15:21:57', NULL),
(93, 93, NULL, 'todos', NULL, 'sdfsdf', 'sdfsdfsd', '2025-07-13 15:26:51', NULL),
(94, 93, NULL, 'estudiantes', NULL, 'asdasd', 'asdasdasd', '2025-07-13 15:54:24', 3),
(95, 93, NULL, 'todos', NULL, 'asdas', 'dasdsad', '2025-07-13 15:57:03', NULL),
(96, 93, NULL, 'estudiantes', NULL, 'asdasdsa', 'asdasd', '2025-07-13 15:57:13', 3),
(97, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: porque si', '2025-07-14 19:58:43', NULL),
(98, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: porque si', '2025-07-14 20:09:00', NULL),
(99, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: locota', '2025-07-14 20:24:53', NULL),
(100, 56, 1, 'individual', 94, 'Tu inscripción ha sido cancelada', 'Motivo: asdfsa', '2025-07-14 20:30:18', NULL),
(101, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: sdfds', '2025-07-14 20:31:44', NULL),
(102, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: dadsad', '2025-07-14 20:32:33', NULL),
(103, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: apo', '2025-07-14 20:36:58', NULL),
(104, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: df', '2025-07-14 20:41:44', NULL),
(105, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: popo', '2025-07-14 20:44:19', NULL),
(106, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: por sapo', '2025-07-14 20:53:27', NULL),
(107, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: por popo', '2025-07-14 20:54:15', NULL),
(108, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: ljljk', '2025-07-14 20:58:12', NULL),
(109, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: fsd', '2025-07-14 21:00:03', NULL),
(110, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: jejej', '2025-07-14 21:01:58', NULL),
(111, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: paila', '2025-07-14 21:02:50', NULL),
(112, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: sfs', '2025-07-14 21:05:16', NULL),
(113, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: sdf', '2025-07-14 21:08:24', NULL),
(114, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: ya no aguanto mas', '2025-07-14 21:14:34', NULL),
(115, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: polo', '2025-07-14 21:20:16', NULL),
(116, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: no eres apto', '2025-07-14 21:21:06', NULL),
(117, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: ya no te queremos xd', '2025-07-14 21:21:42', NULL),
(118, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: sdfd', '2025-07-14 21:23:55', NULL),
(119, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: sdfd', '2025-07-14 21:24:33', NULL),
(120, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: dfd', '2025-07-14 21:36:58', NULL),
(121, 56, 1, 'individual', 95, 'Tu preinscripción ha sido rechazada', 'Motivo: jljklñ', '2025-07-14 21:46:18', NULL),
(122, 56, 1, 'individual', 95, 'Tu inscripción ha sido rechazada', 'Motivo: kjlklj', '2025-07-14 21:48:22', NULL),
(123, 56, 1, 'individual', 95, 'Tu inscripción ha sido rechazada', 'Motivo: aldkkjljkl', '2025-07-14 21:50:12', NULL),
(124, 56, NULL, 'estudiantes', NULL, 'popapola', 'popapola con pan', '2025-07-15 14:33:29', 3),
(125, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: kbhihij', '2025-07-15 14:35:04', NULL),
(126, 56, 1, 'individual', 94, 'Tu inscripción ha sido rechazada', 'Motivo: por lk', '2025-07-15 14:36:43', NULL),
(127, 56, 1, 'individual', 96, 'Tu preinscripción ha sido rechazada', 'Motivo: qd', '2025-07-15 15:42:40', NULL),
(128, 56, 1, 'individual', 96, 'Tu preinscripción ha sido rechazada', 'Motivo: wef', '2025-07-15 15:42:50', NULL),
(129, 56, 1, 'individual', 96, 'Tu inscripción ha sido rechazada', 'Motivo: porque si', '2025-07-15 15:44:39', NULL),
(130, 98, NULL, 'todos', NULL, 'nose ', 'asdasd', '2025-09-13 21:17:55', NULL),
(131, 98, NULL, 'profesores', NULL, 'asdas', 'asdasd', '2025-09-13 21:18:55', 2),
(132, 56, 1, 'individual', 98, 'Tu inscripción ha sido cancelada', 'Motivo: porque si', '2025-09-13 22:10:21', NULL),
(133, 56, 1, 'individual', 99, 'Tu inscripción ha sido cancelada', 'Motivo: mejora y redacta', '2025-09-13 22:16:02', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes_eliminados`
--

CREATE TABLE `mensajes_eliminados` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_mensaje` int(11) NOT NULL,
  `fecha_eliminacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensajes_eliminados`
--

INSERT INTO `mensajes_eliminados` (`id`, `id_usuario`, `id_mensaje`, `fecha_eliminacion`) VALUES
(4, 56, 120, '2025-07-15 19:32:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id_modulo` int(11) NOT NULL,
  `nom_modulo` varchar(30) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `icono` varchar(255) NOT NULL,
  `orden` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`id_modulo`, `nom_modulo`, `url`, `icono`, `orden`) VALUES
(2, 'Profesor', '../models/profesor/profesor.php', 'school', 6),
(3, 'Estudiante', '../models/estudiante/estudiante.php', 'face\r\n', 7),
(5, 'Usuarios', '../models/usuarios/users.php', 'person', 1),
(6, 'Cursos', 'models/cursos/cursos.php', 'assignment', 4),
(7, 'Modulos', 'models/modulos/modulos.php', 'event', 8),
(8, 'Inscripciones', 'models/inscripciones/inscripciones.php', 'card_travel', 5),
(9, 'Index', 'models/admin_index/admin_index.php', 'home', 9),
(10, 'Mensajes', 'models/mensajeria/mensajeria.php', 'question_answer', 3),
(11, 'Perfil', 'models/perfil/perfil.php', 'person', 2),
(12, 'Asig_Horario', 'models/horario/horarios_asignados.php', 'event', NULL),
(13, 'Horario', 'models/horario/cursos_listado.php', 'event', NULL),
(14, 'Asistencia', 'models/asistencia/asistencia.php', 'assignment_turned_in', NULL),
(15, 'Mi Asistencia', 'models/asistencia/asistencia_estudiante.php', 'assignment_turned_in', NULL),
(17, 'Notificaciones', 'models/notificaciones/notificaciones_user.php', 'notifications', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones_user`
--

CREATE TABLE `notificaciones_user` (
  `id_notificacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `leido` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones_user`
--

INSERT INTO `notificaciones_user` (`id_notificacion`, `id_usuario`, `titulo`, `mensaje`, `fecha`, `leido`) VALUES
(1, 93, 'Tu inscripción ha sido rechazada', 'dhdfgfdgf', '2025-07-13 15:14:44', 1),
(2, 93, 'Tu inscripción ha sido rechazada', 'ya no te queremos aca', '2025-07-13 15:21:57', 0),
(3, 94, 'Tu inscripción ha sido rechazada', 'porque si', '2025-07-14 19:58:43', 1),
(4, 94, 'Tu inscripción ha sido rechazada', 'porque si', '2025-07-14 20:09:00', 0),
(5, 94, 'Tu inscripción ha sido rechazada', 'locota', '2025-07-14 20:24:53', 0),
(6, 94, 'Tu inscripción ha sido cancelada', 'asdfsa', '2025-07-14 20:30:18', 0),
(7, 94, 'Tu inscripción ha sido rechazada', 'sdfds', '2025-07-14 20:31:44', 0),
(8, 94, 'Tu inscripción ha sido rechazada', 'dadsad', '2025-07-14 20:32:33', 0),
(9, 94, 'Tu inscripción ha sido rechazada', 'apo', '2025-07-14 20:36:58', 0),
(10, 94, 'Tu inscripción ha sido rechazada', 'df', '2025-07-14 20:41:44', 0),
(11, 94, 'Tu inscripción ha sido rechazada', 'popo', '2025-07-14 20:44:19', 0),
(12, 94, 'Tu inscripción ha sido rechazada', 'por sapo', '2025-07-14 20:53:27', 0),
(13, 94, 'Tu inscripción ha sido rechazada', 'por popo', '2025-07-14 20:54:15', 0),
(14, 94, 'Tu inscripción ha sido rechazada', 'ljljk', '2025-07-14 20:58:12', 0),
(15, 94, 'Tu inscripción ha sido rechazada', 'fsd', '2025-07-14 21:00:03', 0),
(16, 94, 'Tu inscripción ha sido rechazada', 'jejej', '2025-07-14 21:01:58', 0),
(17, 94, 'Tu inscripción ha sido rechazada', 'paila', '2025-07-14 21:02:50', 0),
(18, 94, 'Tu inscripción ha sido rechazada', 'sfs', '2025-07-14 21:05:16', 0),
(19, 94, 'Tu inscripción ha sido rechazada', 'sdf', '2025-07-14 21:08:24', 0),
(20, 94, 'Tu inscripción ha sido rechazada', 'ya no aguanto mas', '2025-07-14 21:14:34', 0),
(21, 94, 'Tu inscripción ha sido rechazada', 'polo', '2025-07-14 21:20:16', 0),
(22, 94, 'Tu inscripción ha sido rechazada', 'no eres apto', '2025-07-14 21:21:06', 0),
(23, 94, 'Tu inscripción ha sido rechazada', 'ya no te queremos xd', '2025-07-14 21:21:42', 0),
(24, 94, 'Tu inscripción ha sido rechazada', 'sdfd', '2025-07-14 21:23:55', 0),
(25, 94, 'Tu inscripción ha sido rechazada', 'sdfd', '2025-07-14 21:24:33', 0),
(26, 94, 'Tu inscripción ha sido rechazada', 'dfd', '2025-07-14 21:36:58', 0),
(27, 95, 'Tu preinscripción ha sido rechazada', 'jljklñ', '2025-07-14 21:46:18', 0),
(28, 95, 'Tu inscripción ha sido rechazada', 'kjlklj', '2025-07-14 21:48:22', 0),
(29, 95, 'Tu inscripción ha sido rechazada', 'aldkkjljkl', '2025-07-14 21:50:12', 0),
(30, 94, 'Tu inscripción ha sido rechazada', 'kbhihij', '2025-07-15 14:35:04', 0),
(31, 94, 'Tu inscripción ha sido rechazada', 'por lk', '2025-07-15 14:36:43', 0),
(32, 96, 'Tu preinscripción ha sido rechazada', 'qd', '2025-07-15 15:42:40', 0),
(33, 96, 'Tu preinscripción ha sido rechazada', 'wef', '2025-07-15 15:42:50', 0),
(34, 96, 'Tu inscripción ha sido rechazada', 'porque si', '2025-07-15 15:44:39', 0),
(35, 99, 'Tu inscripción ha sido cancelada', 'mejora y redacta', '2025-09-13 22:16:02', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `id_estudiante` int(11) DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `fecha_pago` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id_pago`, `id_estudiante`, `monto`, `fecha_pago`) VALUES
(2, 1, 12000.00, '2024-08-30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `created_at`, `id_usuario`) VALUES
(46, 'santiagocaponf@gmail.com', 'ab62cf32778dfe063a7afd8c87081bebcccd15900ac0ffbb98f56b4fee6c8aca', '2024-10-02 19:32:34', 36),
(47, 'santiagocaponf@gmail.com', '3a43fab6750869f508850169d66ff43bdfb0ca059381c17a99ec2f5c3a24f0e8', '2024-10-02 19:40:44', 36),
(48, 'santiagocaponf@gmail.com', 'bcad622b7b016e3a263de308641be51c29abc94d02709f462882eb2b0e44bb10', '2024-10-02 19:40:47', 36),
(49, 'santiagocaponf@gmail.com', '451b28b392d2a9ae0bd37a79e04b4814194c8d6e556087a61c9c70f0e350763b', '2024-10-23 21:06:24', 36);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preinscripciones`
--

CREATE TABLE `preinscripciones` (
  `id_preinscripcion` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `fecha_preinscripcion` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','completada','cancelada') NOT NULL DEFAULT 'pendiente',
  `token` varchar(255) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `preinscripciones`
--

INSERT INTO `preinscripciones` (`id_preinscripcion`, `id_curso`, `nombre`, `email`, `telefono`, `fecha_preinscripcion`, `estado`, `token`, `id_usuario`) VALUES
(96, 2, 'dasdasd', 'afdafd@gmail.com', 'adasd', '2025-07-13 18:44:51', '', '8a47fc835bc93b93ab2533d90cc137ff', 90),
(101, 2, 'adasdasd', 'asdasd@gmail.com', 'asdasdas', '2025-07-13 19:35:22', '', '5cf75789b46e6016f82311315f096a6b', 91),
(102, 1, 'asdasdas', 'asdasd@gmail.com', 'dasdas', '2025-07-13 19:37:25', '', 'e4cf7459e20b44bf42b27bc6e94cc11a', 91),
(125, 2, 'camilo', 'jajs@gmail.com', '5555', '2025-09-14 02:14:11', 'pendiente', 'e3d4d127c6c4c904c9a1dd5db0c934df', 97),
(133, 2, 'jhjj', 'adasdad@gmail.com', '654465465', '2025-09-14 13:13:15', 'pendiente', '06ae17560e439c27bcda368c4af60206', 100);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesor`
--

CREATE TABLE `profesor` (
  `id_profesor` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `especialidad` varchar(255) DEFAULT NULL,
  `experiencia` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `profesor`
--

INSERT INTO `profesor` (`id_profesor`, `id_usuario`, `especialidad`, `experiencia`, `descripcion`) VALUES
(9, 60, 'Deportista', 12, 'Buen profesor que baila la bamba'),
(10, 64, '', 0, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resume_cursos`
--

CREATE TABLE `resume_cursos` (
  `id` int(11) NOT NULL,
  `dia` varchar(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `lugar` varchar(255) NOT NULL,
  `descripcion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `resume_cursos`
--

INSERT INTO `resume_cursos` (`id`, `dia`, `nombre`, `lugar`, `descripcion`) VALUES
(7, '30', 'Sinfonica', 'Colegio Sagrado Corazon De Jesus', '\"Este curso de Sinfónica te ofrece la oportunidad de conocer y profundizar en el mundo de la música orquestal. A través de la teoría y práctica, aprenderás sobre los instrumentos, la interpretación en conjunto y el repertorio sinfónico, desarrollando habilidades tanto técnicas como musicales para participar en una orquesta sinfónica.\"'),
(8, '48', 'Ajedrez', 'Colegio Sagrado Corazon De Jesus', '\"En este curso de Ajedrez aprenderás estrategias, tácticas y técnicas para mejorar tu juego, desde los movimientos básicos hasta jugadas avanzadas. Desarrollarás habilidades de pensamiento crítico, resolución de problemas y toma de decisiones, mientras exploras el fascinante mundo de este milenario deporte mental.\"'),
(9, '96', 'Baloncesto', 'Colegio Sagrado Corazon De Jesus', '\"Este curso de Baloncesto está diseñado para mejorar tus habilidades en el deporte, desde fundamentos como el manejo del balón y los tiros, hasta tácticas de equipo y estrategias de juego. A través de sesiones prácticas, aprenderás a desarrollar tu resistencia, coordinación y trabajo en equipo, perfeccionando tu desempeño en la cancha.\"'),
(10, '125', 'Natación', 'Colegio Sagrado Corazon De Jesus', '\"En este curso de Natación aprenderás las técnicas fundamentales de los diferentes estilos de nado, mejorando tu resistencia, coordinación y técnica en el agua. Con entrenamientos prácticos y progresivos, desarrollarás confianza y habilidades para nadar de manera eficiente y segura, ya sea a nivel recreativo o competitivo.\"');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_usuario`
--

CREATE TABLE `tipo_usuario` (
  `id_tipo_usuario` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_usuario`
--

INSERT INTO `tipo_usuario` (`id_tipo_usuario`, `nombre`) VALUES
(1, 'admin'),
(2, 'profesor'),
(3, 'estudiante'),
(4, 'user');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_module_order`
--

CREATE TABLE `user_module_order` (
  `id_usuario` int(11) NOT NULL,
  `module_order` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`module_order`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `user_module_order`
--

INSERT INTO `user_module_order` (`id_usuario`, `module_order`) VALUES
(36, '[\"12\",\"8\",\"7\",\"2\",\"3\",\"5\",\"6\",\"9\",\"10\",\"11\"]'),
(53, '[\"13\",\"15\",\"10\",\"11\"]'),
(56, '[\"12\",\"5\",\"11\",\"10\",\"7\",\"6\",\"8\",\"2\",\"3\",\"9\"]'),
(64, '[\"13\",\"14\",\"10\",\"2\",\"11\",\"3\"]');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `tipo_doc` varchar(10) DEFAULT NULL,
  `documento` varchar(200) DEFAULT NULL,
  `fecha_nac` date DEFAULT NULL,
  `foto` varchar(50) DEFAULT NULL,
  `mail` varchar(50) DEFAULT NULL,
  `telefono` varchar(25) DEFAULT NULL,
  `direccion` varchar(100) NOT NULL,
  `id_tipo_usuario` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `clave` varchar(255) DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `ultimo_acceso` datetime DEFAULT NULL,
  `is_locked` tinyint(1) DEFAULT 0,
  `lock_timestamp` datetime DEFAULT NULL,
  `perfil_incompleto` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `apellido`, `tipo_doc`, `documento`, `fecha_nac`, `foto`, `mail`, `telefono`, `direccion`, `id_tipo_usuario`, `username`, `clave`, `fecha_registro`, `estado`, `ultimo_acceso`, `is_locked`, `lock_timestamp`, `perfil_incompleto`) VALUES
(36, 'Santiago', 'Capone', 'ID', '12341235234', '2345-03-12', 'WhatsApp Image 2024-07-23 at 4.49.07 PM.jpeg', 'santiagocaponf@gmail.com', '32452345', 'CL 18 A NORTE 2 72', 1, 'alez', '$2y$10$64T2Qk8yptB8y8Rk6Kq26uhnbT3Ias.JH.EXcin2d1BPQCzAvHiM6', '2024-08-25 17:43:54', 'activo', '2024-10-24 10:22:21', 0, NULL, 0),
(42, 'chad', 'sexteto', 'ID', '523456346', '3654-04-23', '66d273c33d474_Recurso 9europe.jpg', 'luisillo@gmail.com', '4563475674', 'CL 18 A NORTE 2 72', 2, 'alez23', '$2y$10$FrpZXvgI3WrL22y9MxNtfuQsyQSgCJ7Jm4VPUv3Aa4qEn2HCKdxsK', '2024-08-29 16:26:44', 'activo', NULL, 0, NULL, 0),
(51, 'antonela', 'sepulveda', 'ID', '342352345', '0005-04-23', '66d23c1021bab_f7c0528d915ec3b38dd89bf7beb2a194.jpg', 'scflorez@corsaje.edu.co', '42352345', 'CL 18 A NORTE 2 72', 1, 'mientras', '$2y$10$KJU2liHj854T1T9M.6/EK.xDYy4sfLf2XEwCldj230rdreZmC.3KC', '2024-08-30 16:39:28', 'activo', NULL, 0, NULL, 0),
(53, 'Juanitos', 'Alimaña', 'ID', '43523634', '0634-06-02', '66d2441faa705_pngwing.com.png', 'juanit@gmail.com', '5233456345123', 'CL 18 A NORTE 2 72', 3, 'alez123123', '$2y$10$p.bJhCL9d2VM1IjUCnC63.Edj5Pg87KZgKGTFyedUHPusUd.QSDAK', '2024-08-30 17:13:51', 'activo', '2025-07-13 11:02:53', 0, NULL, 0),
(55, 'Santiago', 'Capon', 'Passport', '4234523456', '5234-04-23', 'pngwing.com.png', 'scflorez@corsaje.edu.co3', '53643563456', 'CL 18 A NORTE 2 72', 1, 'alez1234', '$2y$10$pcvzMHIh1F53bR25oEpRfu5MbZB5FO6Kn3ceIKwNBtp9KWahjApMe', '2024-09-03 12:35:04', 'activo', NULL, 0, NULL, 0),
(56, 'camilo ', 'prato', 'ID', '1091357317', '2024-09-17', '67032f8d169dd_images.png', 'albertocamiloprato@gmail.com', '3043282464', 'Sapo Marica', 1, 'camilo', '$2y$10$pkH8Zi8gEArSclW4KlpcjOm0Tbx5fSF2o8f7Ukw8qUNWj8Bl7i2I.', '2024-09-07 18:45:23', 'activo', '2025-09-14 08:04:37', 0, NULL, 0),
(58, 'santiago', NULL, NULL, NULL, NULL, NULL, 'edison_alberto@hotmail.com', '52343456', '', 1, 'edison_alberto', '$2y$10$DLJSPUZnsBduhl5PFRtg6uP5aXma0xTP9FOSkKUN/g2l9MrcP7d3S', '2024-09-19 12:02:34', 'activo', NULL, 0, NULL, 0),
(59, 'Santiago', NULL, NULL, NULL, NULL, NULL, 'scapon@misena.edu.co', '3034235435', '', 1, 'scapon', '$2y$10$yrrLCg7Fr85s6u9jOmiVMO14UhXLMOrHN6krm2bL4fCNqnnCqc4Oy', '2024-09-20 20:09:22', 'activo', NULL, 0, NULL, 0),
(60, 'Alirio', 'Moncada', 'ID', '3453453346346', '5234-04-23', '671a4e6e8c19d_jkjhh.png', 'albertocamiloprato@gmail.com', '563456346', 'klerklefjkjdvjkldfj', 2, 'alberto', '$2y$10$XOU.TKla75fnFb.nLgWgb.JA1iNDoMVpCAAoXNGbHZDlAcVKfvKEy', '2024-09-20 20:50:39', 'activo', '2025-09-14 08:07:01', 0, NULL, 0),
(63, NULL, NULL, NULL, NULL, NULL, NULL, 'santigao@gmail.com', NULL, '', 1, 'alez1233', '$2y$10$DSX8990wWKG04J/82ENXo.xZAJyQn/flaX2ULl1gFLB3TuZKQpMZ6', '2024-09-28 20:04:00', 'activo', NULL, 0, NULL, 0),
(64, 'camilo', 'prato profe', 'ID', '13450735', '2000-08-14', '670c345acc2a8_fondos-de-pantalla-3d-paisaje.jpg', 'camiloprato234@gmail.com', '3043282464', 'Brr Atalaya', 2, 'camilop', '$2y$10$1Djh88ty26viA.IG41s4oOFrO5NU.mrAiw.6b3VnEVTPDj0qKeg2q', '2024-10-13 09:06:40', 'activo', '2025-07-15 15:54:42', 0, NULL, 0),
(65, NULL, NULL, NULL, NULL, NULL, NULL, 'alezio@gmail.com', NULL, '', 4, 'alezio', '$2y$10$Aoi7mRPqFZgLA2/7VM8f0eelkVsEaH.HxAKI3IoZ/PcdARIb5koze', '2024-10-23 21:43:42', 'activo', '2024-10-23 21:44:17', 0, NULL, 0),
(66, NULL, NULL, NULL, NULL, NULL, NULL, 'abortopacamodda@gmail.com', NULL, '', 3, 'camilo peñaranda', '$2y$10$PprcAWUiflXNx7PWvt5jKe8b1efpTANoZS3uiClCBOrCnbSDapCaC', '2024-10-24 08:46:37', 'activo', '2024-10-24 08:48:29', 0, NULL, 0),
(67, 'Sinfonica', NULL, NULL, NULL, NULL, NULL, 'michel.camilo.566@co.co', '3222', '', 3, 'michel.camilo.566', '$argon2id$v=19$m=65536,t=4,p=1$RThQc3BZRUpxWGNUMkFCcA$irxitnqgibgqolUHW8tbl4Sizq+HMMHgdqyn/TRhhJo', '2024-10-24 09:32:38', 'activo', NULL, 0, NULL, 0),
(68, NULL, NULL, NULL, NULL, NULL, NULL, 'lklkalaf@gmail.com', NULL, '', 3, 'camilokis', '$2y$10$Q5FEcR8qnPvWGuP2Izc0y.si/Fs8bQ6VPbAJgOEY2d2qGuirOxGhq', '2024-10-24 09:34:45', 'activo', '2024-10-24 09:35:39', 0, NULL, 0),
(69, NULL, NULL, NULL, NULL, NULL, NULL, 'ldasdfklkalaf@gmail.com', NULL, '', 3, 'camilokisa', '$2y$10$3eU8k37qwB5pYAWFwAm/xusZUeRQ/9KNQSrcc64wNkU5.mQxv9Ic2', '2024-10-24 09:43:40', 'activo', NULL, 0, NULL, 0),
(70, NULL, NULL, NULL, NULL, NULL, NULL, 'santiagoca@gmail.comd', NULL, '', 3, 'camilokisalo', '$2y$10$91rsTlSGxc3LUeGwuj6xu.HEb0bcoJzkKP3L3kBBYKT4OCO3n/iX.', '2024-10-24 09:51:43', 'activo', '2024-10-24 09:51:52', 0, NULL, 0),
(71, NULL, NULL, NULL, NULL, NULL, NULL, 'asdsa@gmail.com', NULL, '', 3, 'sofan', '$2y$10$nByBE0mhvUwtlW2VNsseKexwwB4Z9EHO4DGRy28kGDtUmMAIuRI8.', '2025-07-13 10:52:14', 'activo', '2025-07-13 13:10:44', 0, NULL, 0),
(72, NULL, NULL, NULL, NULL, NULL, NULL, 'asda@gmail.com', NULL, '', 3, 'soflan', '$2y$10$Pfy7.7w3QdcgC4g4P7FcMuPCiYA5NoqYBQVMIp8BDq/LZAAahLqFW', '2025-07-13 10:53:45', 'activo', '2025-07-13 13:10:38', 0, NULL, 0),
(73, ' CAmilko', '', NULL, NULL, NULL, NULL, 'asda@gmail.com', '123123', '', 3, 'camilko508', '$2y$10$6vBnRvASwuXCiaOhXZxMquBzsA6YXtcazWcbu1ibivGEEDT2jM.bu', '2025-07-13 11:13:03', 'activo', NULL, 0, NULL, 0),
(74, ' CAmilka', '', NULL, NULL, NULL, NULL, 'asda@gmail.com', '212412341', '', 3, 'camilka153', '$2y$10$Kn37snuVS.4dIWfjU60YOu40nYXaT85Oyirevy6iccnVgqxgkkutu', '2025-07-13 11:17:45', 'activo', NULL, 0, NULL, 0),
(75, ' asda', '', NULL, NULL, NULL, NULL, 'asda@gmail.com', 'asdasd', '', 3, 'asda261', '$2y$10$tjiaoFyB8wKY7MgUonni/eh0b2i3oOc8.M0DZF8fbuikvKsA4ssWW', '2025-07-13 11:26:23', 'activo', NULL, 0, NULL, 0),
(76, ' asdasd', '', NULL, NULL, NULL, NULL, 'asda@gmail.com', 'asdas', '', 3, 'asdasd267', '$2y$10$k9c5duchNu0sKGcDaS7eb.rk2F1vndwGP48vvPlcV85u.O3bMVrX6', '2025-07-13 11:26:44', 'activo', NULL, 0, NULL, 0),
(77, 'Juanito Alimaña', '', NULL, NULL, NULL, NULL, 'juanit@gmail.com', '5233456345123', '', 3, 'juanitoalimaña434', '$2y$10$M5y7y0brGX5KAiIVD6ORiuf09.imExWhlukhOlxl2iAs13Rh3zxnO', '2025-07-13 11:32:07', 'activo', NULL, 0, NULL, 0),
(78, 'camilo  prato', '', NULL, NULL, NULL, NULL, 'albertocamiloprato@gmail.com', '3043282464', '', 3, 'camiloprato239', '$2y$10$hAsOi.ujhEn3qWhUSr504OQtONI1fbaS76XOhhR8VlK0v3TRFRN8q', '2025-07-13 11:43:58', 'activo', NULL, 0, NULL, 0),
(79, 'camilo  prato', '', NULL, NULL, NULL, NULL, 'albertocamiloprato@gmail.com', '3043282464', '', 3, 'camiloprato964', '$2y$10$fFchyjJQROSZNfw9l08EU.rnGVoF01jOJZfhgwzcSLD2/2WpgnvB.', '2025-07-13 11:45:25', 'activo', NULL, 0, NULL, 0),
(80, ' asdas', '', NULL, NULL, NULL, NULL, 'asda@gmail.com', 'dadasdas', '', 3, 'asdas758', '$2y$10$fWMewQD.lDxo/dCPzMNoGebeSsGwJMCUUOVXDiLmM89.RpENYAOEK', '2025-07-13 11:49:12', 'activo', NULL, 0, NULL, 0),
(81, ' asdasd', '', NULL, NULL, NULL, NULL, 'asda@gmail.com', 'asdasdas', '', 3, 'asdasd988', '$2y$10$i/ykqNksOE5TCetkCnbqiujXJUQTadR1urhu0fh.uLF6rhBFoFAgi', '2025-07-13 11:52:06', 'activo', NULL, 0, NULL, 0),
(82, ' asdasda', '', NULL, NULL, NULL, NULL, 'asdsa@gmail.com', 'dasdasd', '', 3, 'asdasda140', '$2y$10$YNu/qxN2QQRJN/w.NFRTB.AcW.DeBUxQRfFa8WY0BYZY52bYiPnra', '2025-07-13 12:05:36', 'activo', NULL, 0, NULL, 0),
(83, ' adasdas', '', NULL, NULL, NULL, NULL, 'asdsa@gmail.com', 'asdasd', '', 3, 'adasdas767', '$2y$10$p8Dc2ULT265OVmzT.9PKLOyni0oYG2Jemm6Y/Te9T4n3Im0EW0l4G', '2025-07-13 12:07:52', 'activo', NULL, 0, NULL, 0),
(84, ' adasdas', '', NULL, NULL, NULL, NULL, 'asdsa@gmail.com', 'dasdasdasdas', '', 3, 'adasdas446', '$2y$10$BUIRH4KMnqlZ.QrE4kJqzuc8VMQbo5RhYj32AS60lRJ3Aj0OPCQdG', '2025-07-13 12:09:48', 'activo', NULL, 0, NULL, 0),
(85, ' asasasasas', '', NULL, NULL, NULL, NULL, 'asdsa@gmail.com', 'adasdas', '', 3, 'asasasasas118', '$2y$10$8kZVL2bI5.BnY2.yCFMQ8O6oiPaIY35PlvjAWBxhpLE/b4xXfv9mu', '2025-07-13 12:12:56', 'activo', NULL, 0, NULL, 0),
(86, ' asdasd', '', NULL, NULL, NULL, NULL, 'asdsa@gmail.com', 'asdasdas', '', 3, 'asdasd765', '$2y$10$.ow01TYc4Ts4gzXY6reYhutH/lvNJueIpbJd0CwdlCQ3BXXhlMBL2', '2025-07-13 12:16:42', 'activo', NULL, 0, NULL, 0),
(87, ' asdasd', '', NULL, NULL, NULL, NULL, 'asdsa@gmail.com', 'asdasd', '', 3, 'asdasd208', '$2y$10$3sqTMOFgmvrICjtW9ssaKOqkGoxXcGU6oet7uePtkqdYt1hOlN.2K', '2025-07-13 12:18:51', 'activo', NULL, 0, NULL, 0),
(88, ' asdasd', '', NULL, NULL, NULL, NULL, 'asdsa@gmail.com', 'asdasd', '', 3, 'asdasd242', '$2y$10$yuWqtVMLLR1jLFZ982Bl0ei4pz1RD.EWV9eE1BrFsTIAr1rgFF3Ke', '2025-07-13 12:22:05', 'activo', NULL, 0, NULL, 0),
(89, ' popopop', '', NULL, NULL, NULL, NULL, 'asda@gmail.com', 'opopo', '', 3, 'popopop277', '$2y$10$gsClqgIH9UIZyTgvKRa3TuCRVfIaiCZRrE0oupmqCCMGkyiywH0PW', '2025-07-13 12:39:32', 'activo', NULL, 0, NULL, 0),
(90, NULL, NULL, NULL, NULL, NULL, NULL, 'afdafd@gmail.com', NULL, '', 4, 'lacucalia', '$2y$10$LKSk.GUg01vmIdjCRSC9aeDfkLTaM72xAYgIooLdvv9soZr5G.SWe', '2025-07-13 13:38:48', 'activo', '2025-07-13 13:44:43', 0, NULL, 0),
(91, NULL, NULL, NULL, NULL, NULL, NULL, 'asdasd@gmail.com', NULL, '', 4, 'serpiente', '$2y$10$2gOwkX3tcolYnhcN1d2pTeNLPopwL88AyOzo3b2rtPymuu6BAxJZu', '2025-07-13 13:47:31', 'activo', '2025-07-13 15:58:33', 0, NULL, 0),
(92, NULL, NULL, NULL, NULL, NULL, NULL, 'asdas@gmail.com', NULL, '', 4, 'agua', '$2y$10$5C/rC7pZLVqQAXlUvGa7LOLSOdHT6BuWBCD3DQrihzbqN1N7M/1Vi', '2025-07-13 14:44:16', 'activo', '2025-07-13 14:44:55', 0, NULL, 0),
(93, NULL, NULL, NULL, NULL, NULL, NULL, 'asdasdaas@gmail.com', NULL, '', 3, 'agua2', '$2y$10$o53WBhXaRDE2yk.uuZ7Iq.7CAjInH7MX.blaS2DupFLcm6B7fif16', '2025-07-13 14:54:24', 'activo', '2025-07-13 15:22:31', 0, NULL, 0),
(94, 'camilo', 'jljkkjlljk', NULL, NULL, '5555-05-05', NULL, 'adssad@gmail.com', '908908900', 'jlhhlhl', 3, 'agua4', '$2y$10$7aO.0FbXKPOCjVOivVJsQe8EtetT0DnFTJPgvlo1dF4MhOFz1aBZ2', '2025-07-14 19:57:09', 'activo', '2025-07-15 15:55:13', 0, NULL, 0),
(95, NULL, NULL, NULL, NULL, NULL, NULL, 'aosdasdad@gmail.com', NULL, '', 4, 'elprocam44', '$2y$10$KkZinpT9sGtd2HF5Mw4wV.VjcbHewGZqYgZV8hJ89SBChZpMgKm4q', '2025-07-14 21:44:31', 'activo', '2025-07-14 21:49:41', 0, NULL, 0),
(96, 'gersson giovany ', 'rubio gonzalez', NULL, NULL, '2025-07-15', '6876bbfc0aaac_1.png', 'ggrubio@corsaje.edu.co', '3175639110', 'ghghj', 4, 'gerssiton', '$2y$10$I3LZm5/Bv.HaM7F/NEyZaucFFGteFxRWdkCwys.7lQ6OxCkcfXEVK', '2025-07-15 15:33:37', 'activo', '2025-07-15 15:41:53', 0, NULL, 0),
(97, 'camilo', NULL, NULL, NULL, NULL, NULL, 'jajs@gmail.com', '5555', '', 4, 'jajs', '$argon2id$v=19$m=65536,t=4,p=1$dlZHZDVxWXVnQlZ6SWppMA$06w8k8o46ar158eNScii96CKjPXr+ficmNyUcNicHdI', '2025-09-13 21:14:11', 'activo', NULL, 0, NULL, 1),
(98, 'camilo', 'prato', NULL, NULL, '2025-09-25', '68c625b2c6c6c_Peak-2-dragged.jpg', 'adaddsda@gmail.com', '64465546645', 'oiuiood', 3, 'elsato', '$2y$10$aD2flA5qD5N43MrCHIQUHep9uEpKPbz0EXgfkY9qcY0StjnKDUhTG', '2025-09-13 21:15:06', 'activo', '2025-09-13 22:01:33', 0, NULL, 0),
(99, 'saleo', 'peñosa', NULL, NULL, '2025-09-25', '68c64628494f7_Peak-2-dragged.jpg', 'adsasdad@gmail.com', '65454634535', 'Calle 9n Número 2-69 urbanización El Bosque', 3, 'saleo', '$2y$10$XCoKOO/SsRuU0mYUawoIvOdcFT7cQYsZH.BZvsuJD7X/6Lwz5nFKK', '2025-09-13 22:11:25', 'activo', '2025-09-14 08:05:43', 0, NULL, 0),
(100, NULL, NULL, NULL, NULL, NULL, NULL, 'adasdad@gmail.com', NULL, '', 4, 'salio', '$2y$10$rgZ85n/hlZ57cAuFkGxk4udBpPlCbYVdhxrpj0F9aYK5PXSXmagxu', '2025-09-14 08:12:53', 'activo', '2025-09-14 08:13:00', 0, NULL, 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asignacion_curso`
--
ALTER TABLE `asignacion_curso`
  ADD PRIMARY KEY (`id_asignacion`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `id_profesor` (`id_profesor`),
  ADD KEY `id_estudiante` (`id_estudiante`);

--
-- Indices de la tabla `asig_modulo`
--
ALTER TABLE `asig_modulo`
  ADD PRIMARY KEY (`id_asig_modulo`),
  ADD KEY `id_modulo` (`id_modulo`),
  ADD KEY `id_usuario` (`id_tipo_usuario`);

--
-- Indices de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD PRIMARY KEY (`id_asistencia`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_curso` (`id_curso`);

--
-- Indices de la tabla `carousel`
--
ALTER TABLE `carousel`
  ADD PRIMARY KEY (`id_carrousel`);

--
-- Indices de la tabla `categoria_curso`
--
ALTER TABLE `categoria_curso`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id_curso`),
  ADD KEY `fk_categoria` (`id_categoria`),
  ADD KEY `id_profesor` (`id_profesor`);

--
-- Indices de la tabla `db_gescursoslecturas_mensajes`
--
ALTER TABLE `db_gescursoslecturas_mensajes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_mensaje` (`id_mensaje`,`id_usuario`);

--
-- Indices de la tabla `estudiante`
--
ALTER TABLE `estudiante`
  ADD PRIMARY KEY (`id_estudiante`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `historial_inscripciones`
--
ALTER TABLE `historial_inscripciones`
  ADD PRIMARY KEY (`id_historial`),
  ADD KEY `id_inscripcion` (`id_inscripcion`),
  ADD KEY `historial_inscripciones_ibfk_2` (`id_usuario_cambio`);

--
-- Indices de la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `id_profesor` (`id_profesor`);

--
-- Indices de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD PRIMARY KEY (`id_inscripcion`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `idx_inscripciones_curso_estudiante` (`id_curso`,`id_estudiante`);

--
-- Indices de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id_mensaje`),
  ADD KEY `id_remitente` (`id_remitente`),
  ADD KEY `id_destinatario` (`id_destinatario`);

--
-- Indices de la tabla `mensajes_eliminados`
--
ALTER TABLE `mensajes_eliminados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usuario_mensaje` (`id_usuario`,`id_mensaje`),
  ADD KEY `id_mensaje` (`id_mensaje`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id_modulo`);

--
-- Indices de la tabla `notificaciones_user`
--
ALTER TABLE `notificaciones_user`
  ADD PRIMARY KEY (`id_notificacion`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_estudiante` (`id_estudiante`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_password_resets_user_id` (`id_usuario`),
  ADD KEY `idx_password_resets_token` (`token`);

--
-- Indices de la tabla `preinscripciones`
--
ALTER TABLE `preinscripciones`
  ADD PRIMARY KEY (`id_preinscripcion`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `fk_preinscripciones_usuario` (`id_usuario`);

--
-- Indices de la tabla `profesor`
--
ALTER TABLE `profesor`
  ADD PRIMARY KEY (`id_profesor`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_token` (`id_usuario`,`token`);

--
-- Indices de la tabla `resume_cursos`
--
ALTER TABLE `resume_cursos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipo_usuario`
--
ALTER TABLE `tipo_usuario`
  ADD PRIMARY KEY (`id_tipo_usuario`);

--
-- Indices de la tabla `user_module_order`
--
ALTER TABLE `user_module_order`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_tipo_usuario` (`id_tipo_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asignacion_curso`
--
ALTER TABLE `asignacion_curso`
  MODIFY `id_asignacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `asig_modulo`
--
ALTER TABLE `asig_modulo`
  MODIFY `id_asig_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  MODIFY `id_asistencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT de la tabla `carousel`
--
ALTER TABLE `carousel`
  MODIFY `id_carrousel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `categoria_curso`
--
ALTER TABLE `categoria_curso`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `db_gescursoslecturas_mensajes`
--
ALTER TABLE `db_gescursoslecturas_mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=910;

--
-- AUTO_INCREMENT de la tabla `estudiante`
--
ALTER TABLE `estudiante`
  MODIFY `id_estudiante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `historial_inscripciones`
--
ALTER TABLE `historial_inscripciones`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=190;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  MODIFY `id_inscripcion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- AUTO_INCREMENT de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id_mensaje` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT de la tabla `mensajes_eliminados`
--
ALTER TABLE `mensajes_eliminados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `notificaciones_user`
--
ALTER TABLE `notificaciones_user`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de la tabla `preinscripciones`
--
ALTER TABLE `preinscripciones`
  MODIFY `id_preinscripcion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT de la tabla `profesor`
--
ALTER TABLE `profesor`
  MODIFY `id_profesor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `resume_cursos`
--
ALTER TABLE `resume_cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tipo_usuario`
--
ALTER TABLE `tipo_usuario`
  MODIFY `id_tipo_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asignacion_curso`
--
ALTER TABLE `asignacion_curso`
  ADD CONSTRAINT `asignacion_curso_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`) ON DELETE CASCADE,
  ADD CONSTRAINT `asignacion_curso_ibfk_2` FOREIGN KEY (`id_profesor`) REFERENCES `profesor` (`id_profesor`),
  ADD CONSTRAINT `asignacion_curso_ibfk_3` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`);

--
-- Filtros para la tabla `asig_modulo`
--
ALTER TABLE `asig_modulo`
  ADD CONSTRAINT `asig_modulo_ibfk_1` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD CONSTRAINT `asistencia_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`),
  ADD CONSTRAINT `asistencia_ibfk_2` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`);

--
-- Filtros para la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD CONSTRAINT `cursos_ibfk_1` FOREIGN KEY (`id_profesor`) REFERENCES `profesor` (`id_profesor`),
  ADD CONSTRAINT `fk_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categoria_curso` (`id_categoria`);

--
-- Filtros para la tabla `estudiante`
--
ALTER TABLE `estudiante`
  ADD CONSTRAINT `fk_estudiante_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `historial_inscripciones`
--
ALTER TABLE `historial_inscripciones`
  ADD CONSTRAINT `historial_inscripciones_ibfk_1` FOREIGN KEY (`id_inscripcion`) REFERENCES `inscripciones` (`id_inscripcion`),
  ADD CONSTRAINT `historial_inscripciones_ibfk_2` FOREIGN KEY (`id_usuario_cambio`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`) ON DELETE CASCADE,
  ADD CONSTRAINT `horarios_ibfk_2` FOREIGN KEY (`id_profesor`) REFERENCES `profesor` (`id_profesor`) ON DELETE CASCADE;

--
-- Filtros para la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD CONSTRAINT `inscripciones_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`),
  ADD CONSTRAINT `inscripciones_ibfk_2` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`);

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`id_remitente`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `mensajes_ibfk_2` FOREIGN KEY (`id_destinatario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `mensajes_eliminados`
--
ALTER TABLE `mensajes_eliminados`
  ADD CONSTRAINT `mensajes_eliminados_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `mensajes_eliminados_ibfk_2` FOREIGN KEY (`id_mensaje`) REFERENCES `mensajes` (`id_mensaje`);

--
-- Filtros para la tabla `notificaciones_user`
--
ALTER TABLE `notificaciones_user`
  ADD CONSTRAINT `notificaciones_user_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`);

--
-- Filtros para la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_password_resets_user` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `preinscripciones`
--
ALTER TABLE `preinscripciones`
  ADD CONSTRAINT `fk_preinscripciones_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `preinscripciones_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`) ON DELETE CASCADE;

--
-- Filtros para la tabla `profesor`
--
ALTER TABLE `profesor`
  ADD CONSTRAINT `profesor_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `user_module_order`
--
ALTER TABLE `user_module_order`
  ADD CONSTRAINT `user_module_order_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_tipo_usuario`) REFERENCES `tipo_usuario` (`id_tipo_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
