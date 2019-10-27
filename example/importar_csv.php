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

$fichero = __DIR__ . '/../src/csv/test.csv';
$res = $calculadora->importCSV($fichero);

