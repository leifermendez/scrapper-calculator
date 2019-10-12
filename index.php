<?php

include_once 'Calculator.php'; //Incluimos la clase que esta en el archivo Calculator.php

$clase = new Calculator(); //Esto se le llama instanciar una clase

$resultado_suma = $clase->suma(10,15); //Usamos el metodo suma de la clase

$resultado_resta = $clase->suma(30,14); //Usamos el metodo resta de la clase


/*Salida */
var_dump($resultado_suma); //Resultado de la suma

var_dump($resultado_resta); //Resultado de la resta

