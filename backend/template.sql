-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-07-2021 a las 21:24:36
-- Versión del servidor: 10.4.19-MariaDB
-- Versión de PHP: 7.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `template`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL COMMENT 'ID del usuario que realizó la petición',
  `tabla` varchar(50) NOT NULL COMMENT 'Tabla en cuestión',
  `id_registro` int(11) NOT NULL COMMENT 'ID del registro en la tabla',
  `accion` varchar(50) NOT NULL COMMENT 'Acción que realizó al registro',
  `datos` text NOT NULL COMMENT 'Datos con que creó / sustituyó el registro',
  `fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `definiciones`
--

CREATE TABLE `definiciones` (
  `id` int(11) NOT NULL,
  `codigo` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `categoria` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `abreviatura` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `descripcion` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `estatus` int(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '0: Inactivo, 1: Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `definiciones`
--

INSERT INTO `definiciones` (`id`, `codigo`, `categoria`, `abreviatura`, `descripcion`, `estatus`) VALUES
(1, '1', 'Metodo-Pago', NULL, 'Efectivo', 1),
(2, '2', 'Metodo-Pago', NULL, 'Cheque', 1),
(3, '3', 'Metodo-Pago', NULL, 'Depósito', 1),
(4, '4', 'Metodo-Pago', NULL, 'Crédito', 1),
(5, '5', 'Metodo-Pago', NULL, 'Transferencia', 1),
(6, '6', 'Metodo-Pago', NULL, 'Mixto', 1),
(7, '1', 'Rol', NULL, 'Administrador', 1),
(8, '2', 'Rol', NULL, 'Vendedor', 1),
(9, '3', 'Rol', NULL, 'Cliente', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `foto` varchar(150) DEFAULT NULL,
  `rol` int(1) UNSIGNED NOT NULL COMMENT '1: Administrador, 2: Gerente, 3: Cajero, 4: Mesero, 5: Cocinero. Referencia en definiciones.codigo con categoria Rol',
  `correo` varchar(30) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `usuario` varchar(20) NOT NULL,
  `contrasena` varchar(100) NOT NULL,
  `access_token` varchar(200) DEFAULT NULL COMMENT 'Token de sesión',
  `access_origin` varchar(100) DEFAULT NULL COMMENT 'IP Origen de la sesión',
  `estatus` int(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '0: Eliminado, 1: Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `foto`, `rol`, `correo`, `telefono`, `usuario`, `contrasena`, `access_token`, `access_origin`, `estatus`) VALUES
(1, 'Pedro', 'Pérez', NULL, 1, 'admin@admin.com', NULL, 'admin', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', '1d6248e4e0eaa5781e9ececc65d15956627d3188ed80bf473c3b917e31ee5329', '127.0.0.1', 1),
(2, 'Alejandra', 'Bastidas', '', 3, 'alebas@restaurant.com', '123654987', 'alebas', 'd7f7a79f99fed1dbafcb3fc602a99e085f08603ba8ed3f550af272806f2a6c72', '584dc91cdda4dd05c14b7e2b3a5eb6a03bf99de88ed23ecc3ddffd469857694b', '127.0.0.1', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `definiciones`
--
ALTER TABLE `definiciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `definiciones`
--
ALTER TABLE `definiciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
