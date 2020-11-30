-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-11-2020 a las 02:29:54
-- Versión del servidor: 10.4.11-MariaDB
-- Versión de PHP: 7.1.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `udem`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `id` int(10) UNSIGNED NOT NULL,
  `carnet` int(6) NOT NULL,
  `nombre_completo` varchar(40) NOT NULL,
  `facultad` varchar(40) NOT NULL,
  `turno` varchar(40) NOT NULL,
  `fecha_matri` date NOT NULL,
  `numero` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`id`, `carnet`, `nombre_completo`, `facultad`, `turno`, `fecha_matri`, `numero`) VALUES
(1, 130188, 'Alex Maltez', 'Ingeniería', 'Nocturno', '2013-01-18', 87654321),
(3, 130899, 'juan perez', 'Periodismo', 'Nocturno', '2016-11-03', 77665544),
(4, 150890, 'Prueba 2', 'Administración', 'Dominical', '2014-11-16', 22334455),
(5, 190124, 'Pedro pablo', 'Derecho', 'OnLine', '2019-11-04', 11223344),
(7, 111000, 'Carlos Perez', 'Ingeniería', 'Dominical', '2011-01-01', 81279868),
(8, 180104, 'Denis Morras', 'Ingeniería', 'Dominical', '1998-12-10', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `membership_grouppermissions`
--

CREATE TABLE `membership_grouppermissions` (
  `permissionID` int(10) UNSIGNED NOT NULL,
  `groupID` int(10) UNSIGNED DEFAULT NULL,
  `tableName` varchar(100) DEFAULT NULL,
  `allowInsert` tinyint(4) NOT NULL DEFAULT 0,
  `allowView` tinyint(4) NOT NULL DEFAULT 0,
  `allowEdit` tinyint(4) NOT NULL DEFAULT 0,
  `allowDelete` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `membership_grouppermissions`
--

INSERT INTO `membership_grouppermissions` (`permissionID`, `groupID`, `tableName`, `allowInsert`, `allowView`, `allowEdit`, `allowDelete`) VALUES
(7, 3, 'alumnos', 0, 0, 0, 0),
(8, 3, 'movimientos', 0, 0, 0, 0),
(9, 2, 'alumnos', 1, 3, 3, 3),
(10, 2, 'movimientos', 1, 3, 3, 3),
(11, 1, 'alumnos', 0, 0, 0, 0),
(12, 1, 'movimientos', 0, 0, 0, 0),
(15, 9, 'alumnos', 1, 3, 3, 3),
(16, 9, 'movimientos', 1, 3, 3, 3),
(17, 10, 'alumnos', 1, 3, 1, 0),
(18, 10, 'movimientos', 1, 3, 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `membership_groups`
--

CREATE TABLE `membership_groups` (
  `groupID` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `allowSignup` tinyint(4) DEFAULT NULL,
  `needsApproval` tinyint(4) DEFAULT NULL,
  `allowCSVImport` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `membership_groups`
--

INSERT INTO `membership_groups` (`groupID`, `name`, `description`, `allowSignup`, `needsApproval`, `allowCSVImport`) VALUES
(1, 'temporal', 'grupo para efectos de prueba', 0, 0, 0),
(2, 'Admins', 'Administradores de la aplicación', 0, 1, 1),
(9, 'cajeroadmin', 'cajero con privilegios elevados', 0, 1, 0),
(10, 'cajero', 'grupos de cajero solo para ingresar info', 0, 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `membership_userpermissions`
--

CREATE TABLE `membership_userpermissions` (
  `permissionID` int(10) UNSIGNED NOT NULL,
  `memberID` varchar(100) NOT NULL,
  `tableName` varchar(100) DEFAULT NULL,
  `allowInsert` tinyint(4) NOT NULL DEFAULT 0,
  `allowView` tinyint(4) NOT NULL DEFAULT 0,
  `allowEdit` tinyint(4) NOT NULL DEFAULT 0,
  `allowDelete` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `membership_userrecords`
--

CREATE TABLE `membership_userrecords` (
  `recID` bigint(20) UNSIGNED NOT NULL,
  `tableName` varchar(100) DEFAULT NULL,
  `pkValue` varchar(255) DEFAULT NULL,
  `memberID` varchar(100) DEFAULT NULL,
  `dateAdded` bigint(20) UNSIGNED DEFAULT NULL,
  `dateUpdated` bigint(20) UNSIGNED DEFAULT NULL,
  `groupID` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `membership_userrecords`
--

INSERT INTO `membership_userrecords` (`recID`, `tableName`, `pkValue`, `memberID`, `dateAdded`, `dateUpdated`, `groupID`) VALUES
(1, 'alumnos', '1', 'admin', 1606365802, 1606365802, 2),
(2, 'alumnos', '3', 'usr1', 1606366424, 1606366424, 9),
(3, 'alumnos', '4', 'usr1', 1606366526, 1606366526, 9),
(4, 'movimientos', '1', 'usr1', 1606366553, 1606366553, 9),
(5, 'movimientos', '2', 'usr1', 1606366557, 1606366578, 9),
(6, 'movimientos', '3', 'usr1', 1606366580, 1606366608, 9),
(7, 'alumnos', '5', 'cajero', 1606366799, 1606366799, 10),
(8, 'movimientos', '4', 'cajero', 1606366865, 1606366865, 10),
(9, 'alumnos', '7', 'cajero', 1606368365, 1606368365, 10),
(10, 'movimientos', '5', 'cajero', 1606368429, 1606368429, 10),
(11, 'alumnos', '8', 'usr1', 1606372867, 1606372880, 9),
(12, 'movimientos', '6', 'usr1', 1606373037, 1606373044, 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `membership_users`
--

CREATE TABLE `membership_users` (
  `memberID` varchar(100) NOT NULL,
  `passMD5` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `signupDate` date DEFAULT NULL,
  `groupID` int(10) UNSIGNED DEFAULT NULL,
  `isBanned` tinyint(4) DEFAULT NULL,
  `isApproved` tinyint(4) DEFAULT NULL,
  `custom1` text DEFAULT NULL,
  `custom2` text DEFAULT NULL,
  `custom3` text DEFAULT NULL,
  `custom4` text DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `pass_reset_key` varchar(100) DEFAULT NULL,
  `pass_reset_expiry` int(10) UNSIGNED DEFAULT NULL,
  `allowCSVImport` tinyint(4) NOT NULL DEFAULT 0,
  `flags` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `membership_users`
--

INSERT INTO `membership_users` (`memberID`, `passMD5`, `email`, `signupDate`, `groupID`, `isBanned`, `isApproved`, `custom1`, `custom2`, `custom3`, `custom4`, `comments`, `pass_reset_key`, `pass_reset_expiry`, `allowCSVImport`, `flags`) VALUES
('admin', '$2y$10$HnEtsnbwzRbN8C6Oh9xpQ.E/5Bc7CKnwglr/o1ZMWw0pn4DjCt2Aa', 'alxteam.ni@gmail.com', '2020-11-25', 2, 0, 1, NULL, NULL, NULL, NULL, 'Admin member created automatically on 2020-11-25\nRegistro actualizado automáticamente en fecha 2020-11-26\nRegistro actualizado automáticamente en fecha 2020-11-29', NULL, NULL, 0, NULL),
('cajero', '$2y$10$OcP5KVbWYxui85YFr3PxKOroHg4AEAtKt2BplLgGX2wcIfP.e6urK', 'demo@cajero.com', '2020-11-26', 10, 0, 1, 'cajero prueba', 'residencial 2', 'Nicaragua', 'Masaya', 'creado', NULL, NULL, 0, NULL),
('invitado', NULL, NULL, '2020-11-25', 1, 0, 1, NULL, NULL, NULL, NULL, 'Anonymous member created automatically on 2020-11-25', NULL, NULL, 0, NULL),
('usr1', '$2y$10$YXafv2ay9.X3lrtgEL.bK.9CeoV577P1ov5pcdUrLOck8jhS/7ywO', 'cajero@admin.com', '2020-11-26', 9, 0, 1, 'cajero admin', 'Barrio x', 'Nicaragua', 'Managua', 'creado!', NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `membership_usersessions`
--

CREATE TABLE `membership_usersessions` (
  `memberID` varchar(100) NOT NULL,
  `token` varchar(100) NOT NULL,
  `agent` varchar(100) NOT NULL,
  `expiry_ts` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

CREATE TABLE `movimientos` (
  `id` int(10) UNSIGNED NOT NULL,
  `carnet` int(10) UNSIGNED NOT NULL,
  `descripcion` varchar(40) NOT NULL,
  `comentarios` varchar(40) DEFAULT NULL,
  `fecha_pago` date NOT NULL,
  `importe_nio` double(10,2) NOT NULL,
  `tipo_pago` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `movimientos`
--

INSERT INTO `movimientos` (`id`, `carnet`, `descripcion`, `comentarios`, `fecha_pago`, `importe_nio`, `tipo_pago`) VALUES
(1, 3, 'Mensualidad', 'Mes enero', '2020-11-25', 500.00, 'Transferencia'),
(2, 3, 'Clase Adicional', 'matematicas 2', '2020-11-25', 100.00, 'Tarjeta'),
(3, 4, 'Examen Rescate', 'rescate programacion 20', '2020-11-25', 150.00, 'Efectivo'),
(4, 5, 'Abono por:', 'arreglo de pago', '2020-11-25', 2500.00, 'Efectivo'),
(5, 7, 'Mensualidad', NULL, '2020-11-25', 1100.00, 'Efectivo'),
(6, 8, 'Matricula', 'pago de matricula año 2021', '1998-12-10', 750.00, 'Efectivo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `carnet_unique` (`carnet`);

--
-- Indices de la tabla `membership_grouppermissions`
--
ALTER TABLE `membership_grouppermissions`
  ADD PRIMARY KEY (`permissionID`),
  ADD UNIQUE KEY `groupID_tableName` (`groupID`,`tableName`);

--
-- Indices de la tabla `membership_groups`
--
ALTER TABLE `membership_groups`
  ADD PRIMARY KEY (`groupID`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `membership_userpermissions`
--
ALTER TABLE `membership_userpermissions`
  ADD PRIMARY KEY (`permissionID`),
  ADD UNIQUE KEY `memberID_tableName` (`memberID`,`tableName`);

--
-- Indices de la tabla `membership_userrecords`
--
ALTER TABLE `membership_userrecords`
  ADD PRIMARY KEY (`recID`),
  ADD UNIQUE KEY `tableName_pkValue` (`tableName`,`pkValue`(150)),
  ADD KEY `pkValue` (`pkValue`),
  ADD KEY `tableName` (`tableName`),
  ADD KEY `memberID` (`memberID`),
  ADD KEY `groupID` (`groupID`);

--
-- Indices de la tabla `membership_users`
--
ALTER TABLE `membership_users`
  ADD PRIMARY KEY (`memberID`),
  ADD KEY `groupID` (`groupID`);

--
-- Indices de la tabla `membership_usersessions`
--
ALTER TABLE `membership_usersessions`
  ADD UNIQUE KEY `memberID_token_agent` (`memberID`,`token`,`agent`),
  ADD KEY `memberID` (`memberID`),
  ADD KEY `expiry_ts` (`expiry_ts`);

--
-- Indices de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carnet` (`carnet`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `membership_grouppermissions`
--
ALTER TABLE `membership_grouppermissions`
  MODIFY `permissionID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT de la tabla `membership_groups`
--
ALTER TABLE `membership_groups`
  MODIFY `groupID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT de la tabla `membership_userpermissions`
--
ALTER TABLE `membership_userpermissions`
  MODIFY `permissionID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `membership_userrecords`
--
ALTER TABLE `membership_userrecords`
  MODIFY `recID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
