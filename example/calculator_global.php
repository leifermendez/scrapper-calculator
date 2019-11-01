<?php

include __DIR__ . "/../vendor/autoload.php";

use leifermendez\scrapper_calculator\Calculator;

$calculator = new Calculator();
$calculator->calculator('global', '40.4238647', '-3.700173', 10);

var_dump($calculator);