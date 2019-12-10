<?php

include __DIR__ . "/../vendor/autoload.php";

use leifermendez\rbs_accommodations\Calculator;


$calculator = new Calculator();

$file = __DIR__ . '/../csv/2019-10-02T071802.756Z_alquiler-viviendas_madrid_villa-de-vallecas_ensanche-de-vallecas-la-gavia.csv';
$res = $calculator->importCSV($file);

var_dump($res);