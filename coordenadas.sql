-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 14-10-2019 a las 12:16:51
-- Versión del servidor: 5.7.24
-- Versión de PHP: 7.2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `coordenadas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `coordenadas`
--

DROP TABLE IF EXISTS `coordenadas`;
CREATE TABLE IF NOT EXISTS `coordenadas` (
  `latitud` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  `longitud` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  `id` int(11) NOT NULL,
  `titulo` varchar(1000) COLLATE utf8_spanish_ci NOT NULL DEFAULT '0',
  `anunciante` varchar(1000) COLLATE utf8_spanish_ci NOT NULL DEFAULT '0',
  `descripcion` text COLLATE utf8_spanish_ci NOT NULL,
  `reformado` tinyint(1) NOT NULL DEFAULT '0',
  `telefonos` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '0',
  `fecha` varchar(12) COLLATE utf8_spanish_ci NOT NULL DEFAULT '0',
  `tipo` varchar(1000) COLLATE utf8_spanish_ci NOT NULL DEFAULT '0',
  `precio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `precioMetro` decimal(10,2) NOT NULL DEFAULT '0.00',
  `direccion` varchar(5000) COLLATE utf8_spanish_ci NOT NULL DEFAULT '0',
  `provincia` varchar(1000) COLLATE utf8_spanish_ci NOT NULL DEFAULT '0',
  `ciudad` varchar(1000) COLLATE utf8_spanish_ci NOT NULL DEFAULT '0',
  `calle` varchar(1000) COLLATE utf8_spanish_ci NOT NULL DEFAULT '0',
  `barrio` varchar(1000) COLLATE utf8_spanish_ci NOT NULL DEFAULT '0',
  `distrito` varchar(1000) COLLATE utf8_spanish_ci NOT NULL DEFAULT '0',
  `metrosCuadrados` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bano` int(11) NOT NULL DEFAULT '0',
  `segundaMano` tinyint(1) NOT NULL DEFAULT '0',
  `armarioEmpotrado` tinyint(1) NOT NULL DEFAULT '0',
  `construidoEn` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '0',
  `cocinaEquipada` tinyint(1) NOT NULL DEFAULT '0',
  `amueblado` tinyint(1) NOT NULL DEFAULT '0',
  `cocinaEquipad` tinyint(1) NOT NULL DEFAULT '0',
  `certificacionEnergetica` varchar(1000) COLLATE utf8_spanish_ci NOT NULL DEFAULT '0',
  `planta` int(11) NOT NULL DEFAULT '0',
  `exterior` tinyint(1) NOT NULL DEFAULT '0',
  `interior` tinyint(1) NOT NULL DEFAULT '0',
  `ascensor` tinyint(1) NOT NULL DEFAULT '0',
  `aireAcondicionado` tinyint(1) NOT NULL DEFAULT '0',
  `habitaciones` int(11) NOT NULL DEFAULT '0',
  `balcon` tinyint(1) NOT NULL DEFAULT '0',
  `trastero` tinyint(1) NOT NULL DEFAULT '0',
  `metrosCuadradosUtiles` decimal(10,2) NOT NULL DEFAULT '0.00',
  `piscina` tinyint(1) NOT NULL DEFAULT '0',
  `jardin` tinyint(1) NOT NULL DEFAULT '0',
  `parking` tinyint(1) NOT NULL DEFAULT '0',
  `terraza` tinyint(1) NOT NULL DEFAULT '0',
  `calefaccionIndividual` varchar(1000) COLLATE utf8_spanish_ci NOT NULL DEFAULT '0',
  `movilidadReducida` tinyint(1) NOT NULL DEFAULT '0',
  `mascotas` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `coordenadas`
--

INSERT INTO `coordenadas` (`latitud`, `longitud`, `id`, `titulo`, `anunciante`, `descripcion`, `reformado`, `telefonos`, `fecha`, `tipo`, `precio`, `precioMetro`, `direccion`, `provincia`, `ciudad`, `calle`, `barrio`, `distrito`, `metrosCuadrados`, `bano`, `segundaMano`, `armarioEmpotrado`, `construidoEn`, `cocinaEquipada`, `amueblado`, `cocinaEquipad`, `certificacionEnergetica`, `planta`, `exterior`, `interior`, `ascensor`, `aireAcondicionado`, `habitaciones`, `balcon`, `trastero`, `metrosCuadradosUtiles`, `piscina`, `jardin`, `parking`, `terraza`, `calefaccionIndividual`, `movilidadReducida`, `mascotas`) VALUES
('40.4232471', '-3.6808792', 37982256, 'Alquiler de Piso en Recoletos', 'Profesional Virginia Sedano', '\"Precioso piso de 170 metros situado junto al Parque de El Retiro, en el corazón de Salamanca Prime.Está en perfecto estado y se alquila completamente amueblado. Tiene un amplio salón comedor, terraza de 10 m², 3 grandes dormitorios, 2 baños completos y un aseo de invitados. Cocina totalmente amueblada y equipada con office. Calefacción central y aire acondicionado.Posibilidad de alquilar plaza de garaje en la propia finca.Ubicado en un magnífico entorno comercial y de servicios y perfectamente comunicado. Metro: Príncipe de Vergara.Precio 3.500 + Garantías + Honorarios agencia.Contacto: Paloma Valverde 628 111 908.\"', 0, '912 176 735', '2019/9/23', 'piso', '3500.00', '20.59', ', Barrio Recoletos, Distrito Salamanca, Madrid, Madrid capital, Madrid', 'Madrid capital, Madrid', 'Madrid', '', ' Barrio Recoletos ', ' Distrito Salamanca ', '170.00', 3, 1, 1, '', 1, 1, 1, 'en trámite', 3, 1, 0, 1, 1, 3, 1, 1, '1.00', 1, 1, 1, 1, '1', 1, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
