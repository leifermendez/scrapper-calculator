<?php

include __DIR__ . "/../vendor/autoload.php";

use leifermendez\scrapper_calculator\Calculator;

$calculator = new Calculator();
$calculator->calculatorChart(
      
    '40.4315534',
    '-3.6777435',
    10
);