<?php

/**
 * Class Calculadora
 * Author: leifer33@gmail.com
 * Collaborator: arturoluna879@gmail.com
 */

namespace leifermendez\scrapper_calculator;

use Exception;
use mysqli;

class Calculadora
{
    /**
     * Declaramos las variables
     */

    private $version = 1.0;
    private $conexion;
    private $database_name = 'idealista_csv';
    private $table_name = 'apartaments';

    /**
     * calculadora constructor.
     * Conexion con el BD
     */
    public function __construct($db_opciones = array())
    {

        try {

            $db_default = array(
                'server' => 'localhost',
                'user' => 'root',
                'pwd' => '',
                'db' => $this->database_name
            );

            $db_default = array_merge($db_default, $db_opciones);

            $con = new mysqli (
                $db_default['server'],
                $db_default['user'],
                $db_default['pwd'],
                $db_default['db']
            );
            // Check if table exists
            $sql = "SELECT * FROM {$db_default['db']}.apartaments";
            $result = $con->query($sql);

            if (!$result) {
                $con->query('USE ' . $this->database_name . ';');
                $con->query($sql);
                $sql = file_get_contents(__DIR__ . '/../src/sql/table_db.sql');
                $result = $con->query($sql);
            }


            $this->conexion = $con;

        } catch (\Exception $e) {
            return "Lo sentimos, este sitio web está experimentando problemas";
        }

    }

    public function importCSV($fichero)
    {
        try {
            if (!file_exists($fichero)) {
                throw new Exception('El archivo CSV no existe');
            }

            $registers = array();
            $data = array();
            $archivo = fopen($fichero, "r");
            $values = '';
            $values_array = array();

            $data=fgetcsv($archivo,10000);

            $lt = array_search('﻿Latitud', $data);
            $lg = array_search('Longitud', $data);
            $hab = array_search('Habitaciones', $data);         
            $ban = array_search('Baños', $data);
            $tit = array_search('Titulo', $data);
            $pre = array_search('Precio', $data);
            $amu = array_search('Amueblado', $data);
            $Id = array_search('ID', $data);
                //latitud, longitud, id, titulo, precio, bano, habitaciones, amueblado
            $num=array($lt,$lg,$Id,$tit,$pre,$ban,$hab,$amu);

            $n=count($num);
            $nn= $n-1;

            while (($data = fgetcsv($archivo, 10000)) == true) {

                for ($i = 0; $i < $nn; $i++) {

                    if ((strlen($data[$num[$i]]))<=0) {
                        $values .= "0,";
                        $values_array[] = 0;
                    } else {

                        if ($data[$num[$i]]=="TRUE" || $data[$num[$i]]=="FALSE") 
                        {
                            $values_array[] = $data[$num[$i]];
                            $values.=$data[$num[$i]].",";
                        }
                        else
                        {
                            $values_array[] =utf8_decode(addslashes($data[$num[$i]]));
                            $values .= "'".utf8_decode(addslashes($data[$num[$i]]))."',";
                        }

                        
                    }
                }
                if ((strlen($data[$num[$nn]]))<=0) {
                    $values .= "0";
                    $values_array[] = 0;
                } else {
                    $values .= $data[$num[$nn]];
                    $values_array[] = $data[$num[$nn]];
                }

                //mysql_real_escape_string

                $sql = "INSERT INTO {$this->table_name}(latitud, longitud, id, titulo, precio, bano, habitaciones, amueblado) values ({$values});";
                $ok = $this->conexion->query($sql);
                
                $registers[] = $sql;
                echo "<br><br><br>".$sql."<br>";
                $values='';
                // var_dump($values_array);
                echo "<br>" . $this->conexion->error . "<br>";$values = '';
            }
            fclose($archivo);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function calculatorGlobal($lat, $lon)
    {
        $sql = "SELECT *, 3956 * 2 * ASIN(SQRT(
				POWER(SIN((" . $lat . " - abs(dest.latitud)) * pi()/180 / 2),
				2) + COS(" . $lat . " * pi()/180 ) * COS(abs(dest.latitud) *
				pi()/180) * POWER(SIN((" . $lon . " - dest.longitud) *
				pi()/180 / 2), 2) )) as distance
				FROM calculadora dest
				having distance < 0.621371;";
        $ok = $this->conexion->query($sql);

        $row = $this->conexion->affected_rows;
        if ($row <= 0) {
            echo "No existen apartamentos en las calculadora indicadas";
            die();
        } else {
            $precio = 0;
            $prom = 0.0;
            while (($dato = $ok->fetch_assoc()) > 0) {
                $precio += $dato['precio'];
            }

            //ARCHIVO PDF
            define('EURO', chr(128));
            $pdf = new FPDF('L', 'mm', 'A4');

            $pdf->AddPage();
            $pdf->SetFont('Arial', '', 10);

            if ($row <= 0)
                $prom = 0;
            else
                $prom = $precio / $row;


            $pdf->Cell(270, 8, 'PROMEDIO DE PRECIO DE LA ZONA', 0, 1, 'C');
            $pdf->Cell(270, 8, '', 0, 1, 'C');

            $pdf->Cell(135, 8, "Precio de la zona", "B", 0, 'C');
            $pdf->Cell(135, 8, "Apartamentos en la zona", "B", 1, 'C');
            $pdf->Cell(135, 8, round($prom, 2) . " " . EURO, 0, 0, 'C');
            $pdf->Cell(135, 8, $row, 0, 1, 'C');
            $pdf->Cell(270, 8, '', 0, 1, 'C');


            $pdf->Cell(85, 8, "Titulo", "B", 0, 'C');
            $pdf->Cell(30, 8, "Precio", "B", 0, 'C');
            $pdf->Cell(15, 8, "Habitaciones", "B", 0, 'C');
            $pdf->Cell(15, 8, utf8_decode("m²"), "B", 0, 'C');
            $pdf->Cell(15, 8, utf8_decode("Baños"), "B", 0, 'C');
            $pdf->Cell(20, 8, "Amueblado", "B", 0, 'C');
            $pdf->Cell(30, 8, utf8_decode("Latitud"), "B", 0, 'C');
            $pdf->Cell(30, 8, utf8_decode("Longitud"), "B", 0, 'C');
            $pdf->Cell(30, 8, utf8_decode("Distancia"), "B", 1, 'C');

            $ok = $this->conexion->query($sql);
            while (($d = $ok->fetch_assoc()) > 0) {
                $pdf->Cell(85, 8, utf8_decode($d['titulo']), "B", 0, 'J');
                $pdf->Cell(30, 8, $d['precio'] . " " . EURO, "B", 0, 'C');
                $pdf->Cell(20, 8, $d['habitaciones'], "B", 0, 'C');
                $pdf->Cell(15, 8, $d['metrosCuadrados'] . utf8_decode("²"), "B", 0, 'C');
                $pdf->Cell(15, 8, $d['bano'], "B", 0, 'C');

                if ($d['amueblado'] == TRUE)
                    $pdf->Cell(15, 8, "Si", "B", 0, 'C');
                else
                    $pdf->Cell(15, 8, "No", "B", 0, 'C');

                $dist = $d['distance'] / 0.62137;

                $pdf->Cell(30, 8, $d['latitud'], "B", 0, 'C');
                $pdf->Cell(30, 8, $d['longitud'], "B", 0, 'C');
                $pdf->Cell(30, 8, round($dist, 4) . " Km", "B", 1, 'C');
            }

            $pdf->Output();
            $contenido = array();
            $ok = $this->conexion->query($sql);
            $k = 0;
            $j = 0;
            while (($var = $ok->fetch_assoc()) > 0) {
                foreach ($var as $key => $value) {
                    $contenido [$k][$j] = $key . " => " . $value;
                    $j++;
                }
                $k++;
            }

            return $contenido;
        }
    }

    private function calculatorFilters($lat, $lon, $array)
    {
        $row = sizeof($array);
        $where = '';
        $i = 0;
        foreach ($array as $key => $value) {
            if ($i < $row - 1)
                $where .= $key . "=" . $value . " AND ";
            else
                $where .= $key . "=" . $value;
            $i++;
        }


        $sql = "SELECT *, 3956 * 2 * ASIN(SQRT(
				POWER(SIN((" . $lat . " - abs(dest.latitud)) * pi()/180 / 2),
				2) + COS(" . $lat . " * pi()/180 ) * COS(abs(dest.latitud) *
				pi()/180) * POWER(SIN((" . $lon . " - dest.longitud) *
				pi()/180 / 2), 2) )) as distance
				FROM calculadora dest WHERE " . $where . " 				
				having distance < 0.621371 ORDER BY distance ASC;";
        $ok = $this->conexion->query($sql);

        $rows = $this->conexion->affected_rows;

        //verificacion de que exista en la base de datos la consulta
        if ($rows <= 0) {
            echo "No existen apartamentos en esta zona con el filtro añadido";
        } else {

            $precio = 0;
            $prom = 0.0;
            while (($dato = $ok->fetch_assoc()) > 0) {
                $precio += $dato['precio'];
            }

            //ARCHIVO PDF
            $pdf = new FPDF('L', 'mm', 'A4');

            $pdf->AddPage();
            $pdf->SetFont('Arial', '', 10);

            //calcula el promedio del precio de la zona
            if ($rows <= 0)
                $prom = 0;
            else
                $prom = $precio / $rows;

            //crea un string para mostrar en el pdf cuales fueron los filtros asignados
            $pdfarray = '';
            foreach ($array as $key => $value) {
                $pdfarray .= $key . " = " . $value . " | ";
            }

            define('EURO', chr(128));

            $pdf->Cell(270, 8, 'PROMEDIO DE PRECIO DE LA ZONA FILTRADO', "B", 1, 'C');
            $pdf->Cell(270, 8, '( ' . $pdfarray . ' )', 0, 1, 'C');
            $pdf->Cell(270, 8, '', 0, 1, 'C');

            $pdf->Cell(135, 8, "Precio de la zona", "B", 0, 'C');
            $pdf->Cell(135, 8, "Apartamentos en la zona", "B", 1, 'C');
            $pdf->Cell(135, 8, round($prom, 2) . " " . EURO, 0, 0, 'C');
            $pdf->Cell(135, 8, $rows, 0, 1, 'C');
            $pdf->Cell(270, 8, '', 0, 1, 'C');

            $pdf->Cell(85, 8, "Titulo", "B", 0, 'C');
            $pdf->Cell(30, 8, "Precio", "B", 0, 'C');
            $pdf->Cell(15, 8, "Habitaciones", "B", 0, 'C');
            $pdf->Cell(15, 8, utf8_decode("m²"), "B", 0, 'C');
            $pdf->Cell(15, 8, utf8_decode("Baños"), "B", 0, 'C');
            $pdf->Cell(20, 8, "Amueblado", "B", 0, 'C');
            $pdf->Cell(30, 8, utf8_decode("Latitud"), "B", 0, 'C');
            $pdf->Cell(30, 8, utf8_decode("Longitud"), "B", 0, 'C');
            $pdf->Cell(30, 8, utf8_decode("Distancia"), "B", 1, 'C');

            $ok = $this->conexion->query($sql);
            while (($d = $ok->fetch_assoc()) > 0) {
                $pdf->Cell(85, 8, utf8_decode($d['titulo']), "B", 0, 'J');
                $pdf->Cell(30, 8, $d['precio'] . " " . EURO, "B", 0, 'C');
                $pdf->Cell(20, 8, $d['habitaciones'], "B", 0, 'C');
                $pdf->Cell(15, 8, $d['metrosCuadrados'] . utf8_decode("²"), "B", 0, 'C');
                $pdf->Cell(15, 8, $d['bano'], "B", 0, 'C');

                if ($d['amueblado'] == TRUE)
                    $pdf->Cell(15, 8, "Si", "B", 0, 'C');
                else
                    $pdf->Cell(15, 8, "No", "B", 0, 'C');

                $dist = $d['distance'] / 0.62137;

                $pdf->Cell(30, 8, $d['latitud'], "B", 0, 'C');
                $pdf->Cell(30, 8, $d['longitud'], "B", 0, 'C');
                $pdf->Cell(30, 8, round($dist, 4) . " Km", "B", 1, 'C');
            }
            $pdf->Output();

            $contenido = array();
            $ok = $this->conexion->query($sql);
            $k = 0;
            $j = 0;
            while (($var = $ok->fetch_assoc()) > 0) {
                foreach ($var as $key => $value) {
                    $contenido [$k][$j] = $key . " => " . $value;
                    $j++;
                }
                $k++;
            }
            return $contenido;
        }
    }

    public function calculator($lat = null, $lng = null, $filters = array())
    {
        try {

            $response = array();

            if (count($filters)) {
                $response = $this->calculatorFilters($lat, $lng, $filters);
            } else {
                $response = $this->calculatorGlobal($lat, $lng);
            }

            return $response;

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}