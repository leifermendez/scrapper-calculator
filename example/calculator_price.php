<?php

include __DIR__ . "/../vendor/autoload.php";

use leifermendez\scrapper_calculator\Calculator;

$calculator = new Calculator();
$calculator->calculator(
    'precio',
    '40.4238647',
    '-3.700173',
    100,
    [
        'bano' => [
            'symbol' => '=',
            'value' => 1
        ]
    ],
    1000,
    800
);
