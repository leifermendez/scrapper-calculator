<?php

include __DIR__ . "/../vendor/autoload.php";

use leifermendez\scrapper_calculator\Calculadora;

$base_datos = array(
    'server' => '127.0.0.1',
    'user' => 'root',
    'pwd' => 'Token.01',
    'db' => 'idealista_csv'
);
$calculadora = new Calculadora($base_datos);

$fichero = __DIR__ . '/../src/csv/test.csv';
$res = $calculadora->calculator('40.4229807','-3.6992253');

var_dump($res);

