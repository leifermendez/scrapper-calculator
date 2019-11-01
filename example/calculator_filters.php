<?php

include __DIR__ . "/../vendor/autoload.php";

use leifermendez\scrapper_calculator\Calculator;

$calculator = new Calculator();
$calculator->calculator(
    'filters',
    '40.4238647',
    '-3.700173',
    10,
    [
        'bano' => [
            'symbol' => '=',
            'value' => 1
        ]
    ]
);
