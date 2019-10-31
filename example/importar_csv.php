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

$fichero = '../src/csv/2019-09-30T230344.101Z_alquiler-viviendas_madrid_moncloa_arguelles.csv';
$res = $calculadora->importCSV($fichero);

