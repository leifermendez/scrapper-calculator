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
            $Id = array_search('ID', $data);
            $tit = array_search('Titulo', $data);
            $anun = array_search('Anunciante', $data);
            $desc = array_search('Descripcion', $data);
            $refor = array_search('Reformado', $data);
            $telf = array_search('Telefonos', $data);
            $fec = array_search('Fecha', $data);
            $tipo = array_search('Tipo', $data);
            $pre = array_search('Precio', $data);
            $preM = array_search('Precio por metro', $data);
            $dire = array_search('Direccion', $data);
            $prov = array_search('Provincia', $data);
            $ciud = array_search('Ciudad', $data);
            $calle = array_search('Calle', $data);
            $barrio = array_search('Barrio', $data);
            $dist = array_search('Distrito', $data);
            $metroC = array_search('Metros cuadrados', $data);
            $bano = array_search('Baños', $data);
            $segM = array_search('Segunda mano', $data);
            $armEm = array_search('Armarios Empotrados', $data);
            $constEn = array_search('Construido en', $data);
            $cocEda = array_search('Cocina Equipada', $data);
            $amue = array_search('Amueblado', $data);
            $cocEd = array_search('Cocina equipada', $data);
            $certEng = array_search('Certificación energética', $data);
            $planta = array_search('Planta', $data);
            $ext = array_search('Exterior', $data);
            $inte = array_search('Interior', $data);
            $asce = array_search('Ascensor', $data);
            $aireAc = array_search('Aire acondicionado', $data);
            $hab = array_search('Habitaciones', $data);
            $balc = array_search('Balcón', $data);
            $tras = array_search('Trastero', $data);
            $metroCU = array_search('Metros cuadrados útiles', $data);
            $pisc = array_search('Piscina', $data);
            $jard = array_search('Jardín', $data);
            $park = array_search('Parking', $data);
            $ter = array_search('Terraza', $data);
            $calI = array_search('Calefacción individual', $data);
            $movRed = array_search('Apto para personas con movilidad reducida', $data);
            $masc = array_search('Se admiten mascotas', $data);

            
            $num=array($lt, $lg, $Id, $tit, $anun, $desc, $refor, $telf, $fec, $tipo, $pre, $preM, $dire, $prov, $ciud, $calle, $barrio, $dist, $metroC, $bano, $segM, $armEm, $constEn, $cocEda, $amue, $cocEd, $certEng, $planta, $ext, $inte, $asce, $aireAc, $hab, $balc, $tras, $metroCU, $pisc, $jard, $park, $ter, $calI, $movRed, $masc);

            $n=count($num);
            $nn= $n-1;

            while (($data = fgetcsv($archivo, 10000)) == true) {

                for ($i = 0; $i < $nn; $i++) {

                    if ((strlen($data[$num[$i]]))<=0 || $num[$i]===false) {
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
                if ((strlen($data[$num[$nn]]))<=0 || $num[$i]===false) {
                    $values .= "0";
                    $values_array[] = 0;
                } else {
                    $values .= $data[$num[$nn]];
                    $values_array[] = $data[$num[$nn]];
                }

                //mysql_real_escape_string

                $sql = "INSERT INTO {$this->table_name}(latitud, longitud, id, titulo,anunciante,  descripcion, reformado, telefonos, fecha, tipo,  precio,precioMetro, direccion, provincia, ciudad, calle, barrio,distrito, metrosCuadrados, bano, segundaMano, armarioEmpotrado,  construidoEn,  cocinaEquipada,amueblado, cocinaEquipad, certificacionEnergetica, planta,exterior,  interior,  ascensor, aireAcondicionado, habitaciones, balcon,trastero, metrosCuadradosUtiles, piscina, jardin,parking, terraza, calefaccionIndividual, movilidadReducida, mascotas) values ({$values});";
                $ok = $this->conexion->query($sql);
                
                $registers[] = $sql;
                echo "<br><br>".$sql."<br><br>";
                $values='';
                // var_dump($values_array);
                echo "<br>" . $this->conexion->error . "<br>";$values = '';
            }
            fclose($archivo);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function calculatorGlobal($lat, $lon, $km)
    {
        $distancia= $km * 0.62137;
        $sql = "SELECT *, 3956 * 2 * ASIN(SQRT(
				POWER(SIN((" . $lat . " - abs(dest.latitud)) * pi()/180 / 2),
				2) + COS(" . $lat . " * pi()/180 ) * COS(abs(dest.latitud) *
				pi()/180) * POWER(SIN((" . $lon . " - dest.longitud) *
				pi()/180 / 2), 2) )) as distance
				FROM {$this->table_name} dest
				having distance < ".$distancia." ORDER BY distance ASC;";
        $ok = $this->conexion->query($sql);
        // echo $this->$conexion->error."<br><br>";
        $row = $this->conexion->affected_rows;
        if ($row <= 0) {
            echo "No existen apartamentos en las coordenadas indicadas";
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
                $pdf->Cell(85, 8, $d['titulo'], "B", 0, 'J');
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

    private function calculatorFilters($lat, $lon, $km, $array,$max,$min)
    {
        $distancia= $km * 0.62137;
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
				FROM {$this->table_name} dest WHERE " . $where . " 				
				having distance < ".$distancia." ORDER BY distance ASC;";
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
                $pdf->Cell(85, 8, $d['titulo'], "B", 0, 'J');
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

    private function calculatorPrecio($lat, $lon, $km, $max, $min)
    {
        $distancia= $km * 0.62137;
        $sql = "SELECT *, 3956 * 2 * ASIN(SQRT(
                POWER(SIN((" . $lat . " - abs(dest.latitud)) * pi()/180 / 2),
                2) + COS(" . $lat . " * pi()/180 ) * COS(abs(dest.latitud) *
                pi()/180) * POWER(SIN((" . $lon . " - dest.longitud) *
                pi()/180 / 2), 2) )) as distance
                FROM apartaments dest
                having distance < ".$distancia." AND precio >".$min." AND precio <".$max." ORDER BY distance ASC;";
        $ok = $this->conexion->query($sql);
        // echo $this->$conexion->error."<br><br>";
        $row = $this->conexion->affected_rows;
        if ($row <= 0) {
            echo "No existen apartamentos en las coordenadas indicadas";
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
                $pdf->Cell(85, 8, $d['titulo'], "B", 0, 'J');
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




    public function calculator($lat = null, $lng = null, $km=1, $filters = array(), $max = null, $min = null)
    {
        try {

            $response = array();

            if (count($filters)) {
                $response = $this->calculatorFilters($lat, $lng, $km, $filters);
            } else if ($max && $min){
                $response = $this->calculatorPrecio($lat, $lng, $km, $max, $min);
            }
            else {
                $response = $this->calculatorGlobal($lat, $lng, $km);
            }

            return $response;

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}