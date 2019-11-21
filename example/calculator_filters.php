<?php

include __DIR__ . "/../vendor/autoload.php";

use leifermendez\scrapper_calculator\Calculator;

$calculator = new Calculator();
$calculator->calculator(
      
    '40.4238647',
    '-3.700173',
    1,
    [
       'ciudad' => [
            'symbol' => '>',
            'value' => 'madrid'
        ],
        'precio' => [
            'symbol' => '=',
            'value'=>[600,1000]//min - max
        ]
    ]
);
