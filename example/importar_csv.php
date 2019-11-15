<?php

include __DIR__ . "/../vendor/autoload.php";

use leifermendez\scrapper_calculator\Calculator;


$calculator = new Calculator();

$file = __DIR__ . '/../csv/2019-09-30T230344.101Z_alquiler-viviendas_madrid_salamanca_lista.csv';
$res = $calculator->importCSV($file);

var_dump($res);