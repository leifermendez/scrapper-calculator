<?php


/**
 * Class Calculato
 * Author: Leifer Mendez
 *
 * Esto es una clase
 */
class Calculator // Esto es el nombre de la clase
{
    public function suma($primer = null, $segundo = null) //Esto es un metodo publico
    {
        try {

            $total = $primer + $segundo;

            return $total;

        } catch (\Exception $e) {


            return $e->getMessage();
        }
    }

    public function resta($primer = null, $segundo = null) //Esto es un metodo publico
    {
        try {

            $total = $primer - $segundo;

            return $total;

        } catch (\Exception $e) {


            return $e->getMessage();
        }
    }
}