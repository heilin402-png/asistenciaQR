-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-08-2026 a las 18:39:45
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
-- Base de datos: `asistencia_qr_escolar`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia_clase`
--

CREATE TABLE `asistencia_clase` (
  `id_asistencia` int(11) NOT NULL,
  `id_sesion` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `estado` enum('PRESENTE','AUSENTE','EVASION','EXCUSA','TARDE') NOT NULL,
  `hora_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia_restaurante`
--

CREATE TABLE `asistencia_restaurante` (
  `id_asistencia_restaurante` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id_auditoria` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `accion` varchar(255) NOT NULL,
  `fecha_hora` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id_curso` int(11) NOT NULL,
  `nombre_curso` varchar(20) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docente_curso`
--

CREATE TABLE `docente_curso` (
  `id_docente_curso` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

CREATE TABLE `estudiantes` (
  `id_estudiante` int(11) NOT NULL,
  `documento` varchar(20) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`) VALUES
(1, 'ADMINISTRADOR'),
(2, 'DOCENTE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones_clase`
--

CREATE TABLE `sesiones_clase` (
  `id_sesion` int(11) NOT NULL,
  `id_docente` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` datetime NOT NULL,
  `estado` enum('ABIERTA','CERRADA') DEFAULT 'ABIERTA'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `documento` varchar(20) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `id_rol`, `nombres`, `apellidos`, `documento`, `correo`, `password`, `estado`, `fecha_creacion`) VALUES
(1, 1, 'Administrador', 'Sistema', '1000000000', 'admin@colegio.edu.co', '$2y$10$6RHhXtcXOSt/OGqIxCIrSuv0HQA/dHyZ/waUvw4xlQbxQNWLGB3nK', 'ACTIVO', '2026-07-29 13:15:20');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asistencia_clase`
--
ALTER TABLE `asistencia_clase`
  ADD PRIMARY KEY (`id_asistencia`),
  ADD UNIQUE KEY `id_sesion` (`id_sesion`,`id_estudiante`),
  ADD KEY `fk_ac_estudiante` (`id_estudiante`);

--
-- Indices de la tabla `asistencia_restaurante`
--
ALTER TABLE `asistencia_restaurante`
  ADD PRIMARY KEY (`id_asistencia_restaurante`),
  ADD UNIQUE KEY `id_estudiante` (`id_estudiante`,`fecha`),
  ADD KEY `idx_asistencia_fecha` (`fecha`);

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id_auditoria`),
  ADD KEY `fk_auditoria_usuario` (`id_usuario`);

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id_curso`),
  ADD UNIQUE KEY `nombre_curso` (`nombre_curso`);

--
-- Indices de la tabla `docente_curso`
--
ALTER TABLE `docente_curso`
  ADD PRIMARY KEY (`id_docente_curso`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`,`id_curso`),
  ADD KEY `fk_dc_curso` (`id_curso`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`id_estudiante`),
  ADD UNIQUE KEY `documento` (`documento`),
  ADD KEY `fk_estudiante_curso` (`id_curso`),
  ADD KEY `idx_estudiante_documento` (`documento`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `nombre_rol` (`nombre_rol`);

--
-- Indices de la tabla `sesiones_clase`
--
ALTER TABLE `sesiones_clase`
  ADD PRIMARY KEY (`id_sesion`),
  ADD KEY `fk_sesion_docente` (`id_docente`),
  ADD KEY `fk_sesion_curso` (`id_curso`),
  ADD KEY `idx_sesion_fecha` (`fecha`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `documento` (`documento`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `fk_usuario_rol` (`id_rol`),
  ADD KEY `idx_usuario_documento` (`documento`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asistencia_clase`
--
ALTER TABLE `asistencia_clase`
  MODIFY `id_asistencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `asistencia_restaurante`
--
ALTER TABLE `asistencia_restaurante`
  MODIFY `id_asistencia_restaurante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id_auditoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `docente_curso`
--
ALTER TABLE `docente_curso`
  MODIFY `id_docente_curso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  MODIFY `id_estudiante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sesiones_clase`
--
ALTER TABLE `sesiones_clase`
  MODIFY `id_sesion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asistencia_clase`
--
ALTER TABLE `asistencia_clase`
  ADD CONSTRAINT `fk_ac_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiantes` (`id_estudiante`),
  ADD CONSTRAINT `fk_ac_sesion` FOREIGN KEY (`id_sesion`) REFERENCES `sesiones_clase` (`id_sesion`);

--
-- Filtros para la tabla `asistencia_restaurante`
--
ALTER TABLE `asistencia_restaurante`
  ADD CONSTRAINT `fk_ar_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiantes` (`id_estudiante`);

--
-- Filtros para la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `fk_auditoria_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `docente_curso`
--
ALTER TABLE `docente_curso`
  ADD CONSTRAINT `fk_dc_curso` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`),
  ADD CONSTRAINT `fk_dc_docente` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD CONSTRAINT `fk_estudiante_curso` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`);

--
-- Filtros para la tabla `sesiones_clase`
--
ALTER TABLE `sesiones_clase`
  ADD CONSTRAINT `fk_sesion_curso` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`),
  ADD CONSTRAINT `fk_sesion_docente` FOREIGN KEY (`id_docente`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
