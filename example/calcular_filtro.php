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
//idealista_csv
$calculadora->calculator();