<?php

include __DIR__ . "/../vendor/autoload.php";

use leifermendez\scrapper_calculator\Calculadora;

$base_datos = array(
    'server' => 'localhost',
    'user' => 'root',
    'pwd' => '',
    'db' => 'idealista_csv'
);
$calculadora = new Calculadora($base_datos);
$calculadora->calculator('global', '40.4238647', '-3.700173', 1, array('bano'=>7), 1500, 500);

//function calculator(opciones: global || filters || precio, 'latitud', 'longitud', radio de busqueda en Km, array('filtro'=>valor), 'Precio Max', 'Precio Min')

/**
	Columnas en base de datos para especificar filtro de busqueda

	 latitud, longitud, id,	titulo, anunciante,	descripcion, reformado,	telefonos, fecha,	tipo, precio, precioMetro, direccion, provincia, ciudad, calle, barrio,	distrito,	metrosCuadrados, bano, segundaMano,	armarioEmpotrado, construidoEn,	cocinaEquipada,	amueblado, cocinaEquipad, certificacionEnergetica, planta, exterior, interior, ascensor, aireAcondicionado,	habitaciones, balcon, trastero,	metrosCuadradosUtiles, piscina,	jardin,	parking, terraza, calefaccionIndividual, movilidadReducida,	mascotas
**/

