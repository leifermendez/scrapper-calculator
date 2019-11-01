<?php

include __DIR__ . "/../vendor/autoload.php";

use leifermendez\scrapper_calculator\Calculator;


$calculadora = new Calculator();

$fichero = __DIR__ . '/../csv/2019-10-02T071802.756Z_alquiler-viviendas_madrid_villa-de-vallecas_ensanche-de-vallecas-la-gavia.csv';
$res = $calculadora->importCSV($fichero);

