<?php

/**
 * Class Calculadora
 * Author: leifer33@gmail.com
 * Collaborator: arturoluna879@gmail.com
 */

namespace leifermendez\scrapper_calculator;

use Exception;
use mysqli;
use Fpdf\Fpdf;
use leifermendez\scrapper_calculator\Tools;
use leifermendez\scrapper_calculator\Errores;

class Calculator extends Settings
{
    public static $ERROR;

    public $connection;
    public $db_name;
    public $table_name;
    public $TOOLS;

    public function __construct()
    {
        $this->connection = parent::__construct();

        try {
            $this->db_name = parent::$DB_NAME;
            $this->table_name = parent::$DB_TABLE;
            $this->TOOLS = new Tools();

        } catch (\Exception $e) {
            return self::$ERROR->ERROR_CONNECTION_DB;
        }

    }

    public function importCSV($file)
    {
        try {
            if (!file_exists($file)) {
                throw new Exception(self::$ERROR->ERROR_NOT_FOUND_FILE_CSV);
            }
            $data_file = $this->TOOLS->ReadCsv($file);
            $sql_row = array();
            $values = '';
            $values_array = array();


            foreach (parent::$FIELDS as $key => $value) {
                parent::$FIELDS[$key]['position'] = array_search($key, $data_file['data']);

            }

            $len = count(parent::$FIELDS) - 1;
            $fields_db = implode(',', parent::$FIELDS_DB);
            while (($data = fgetcsv($data_file['data_file'], 10000)) == true) {
                $data = array_map("utf8_encode", $data);
                for ($index = 0; $index < $len; $index++) {
                    $this->TOOLS->SetValueField($index, $data[$index]);
                    $res_values = $this->TOOLS->GetAllValues();
                    $res_values = implode(',',$res_values);
                    $sql_row[] = "INSERT INTO {$this->table_name} ($fields_db) values ({$res_values});";
                }

                /*for ($i = 0; $i < $len; $i++) {

                    if ((strlen($data[parent::$FIELDS[$i]])) <= 0 || parent::$FIELDS[$i] === false) {
                        $values .= "0,";
                        $values_array[] = 0;
                    } else {

                        if ($data[parent::$FIELDS[$i]] == "TRUE" || $data[parent::$FIELDS[$i]] == "FALSE") {
                            $values_array[] = $data[parent::$FIELDS[$i]];
                            $values .= $data[parent::$FIELDS[$i]] . ",";
                        } else {
                            $values_array[] = utf8_decode(addslashes($data[parent::$FIELDS[$i]]));
                            $values .= "'" . utf8_decode(addslashes($data[parent::$FIELDS[$i]])) . "',";
                        }


                    }
                }
                if ((strlen($data[parent::$FIELDS[$len]])) <= 0 || parent::$FIELDS[$i] === false) {
                    $values .= "0";
                    $values_array[] = 0;
                } else {
                    $values .= $data[parent::$FIELDS[$len]];
                    $values_array[] = $data[parent::$FIELDS[$len]];
                }*/

                //mysql_real_escape_string

                //$sql = "INSERT INTO {$this->table_name} (latitud, longitud, id, titulo,anunciante,  descripcion, reformado, telefonos, fecha, tipo,  precio,precioMetro, direccion, provincia, ciudad, calle, barrio,distrito, metrosCuadrados, bano, segundaMano, armarioEmpotrado,  construidoEn,  cocinaEquipada,amueblado, cocinaEquipad, certificacionEnergetica, planta,exterior,  interior,  ascensor, aireAcondicionado, habitaciones, balcon,trastero, metrosCuadradosUtiles, piscina, jardin,parking, terraza, calefaccionIndividual, movilidadReducida, mascotas) values ({$values});";
                //$ok = $this->connection->query($sql);

                //$registers[] = $sql;
                //error_log($sql . " \n", 3, 'LOG-ERROR.txt');
                // var_dump($this->connection->error);
                $values = '';
                // var_dump($values_array);
                //echo "<br>" . $this->connection->error . "<br>";

            }

            fclose($data_file['data_file']);

            var_dump($sql_row);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    function calculator($opc, $lat, $lon, $km = 1, $array = null, $max = null, $min = null)
    {
        $distancia = $km * 0.62137;
        switch ($opc) {
            case 'global':

                $sql = "SELECT *, 3956 * 2 * ASIN(SQRT(
                POWER(SIN((" . $lat . " - abs(dest.latitud)) * pi()/180 / 2),
                2) + COS(" . $lat . " * pi()/180 ) * COS(abs(dest.latitud) *
                pi()/180) * POWER(SIN((" . $lon . " - dest.longitud) *
                pi()/180 / 2), 2) )) as distance
                FROM {$this->table_name} dest
                having distance < " . $distancia . " ORDER BY distance ASC;";
                $ok = $this->connection->query($sql);
                // echo $this->$conexion->error."<br><br>";
                $row = $this->connection->affected_rows;
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

                    $ok = $this->connection->query($sql);
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
                    $ok = $this->connection->query($sql);
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
                break;

            case 'filters':

                if (count($array) <= 0) {
                    echo "Filtro invalido!";
                } else {
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
                            having distance < " . $distancia . " ORDER BY distance ASC;";
                    $ok = $this->connection->query($sql);

                    $rows = $this->connection->affected_rows;

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

                        $ok = $this->connection->query($sql);
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
                        $ok = $this->connection->query($sql);
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
                break;

            case 'precio':

                if (!$min && !$max) {
                    echo "No se ha especificado el precio minimo y precio maximo";
                } else {

                    $sql = "SELECT *, 3956 * 2 * ASIN(SQRT(
                    POWER(SIN((" . $lat . " - abs(dest.latitud)) * pi()/180 / 2),
                    2) + COS(" . $lat . " * pi()/180 ) * COS(abs(dest.latitud) *
                    pi()/180) * POWER(SIN((" . $lon . " - dest.longitud) *
                    pi()/180 / 2), 2) )) as distance
                    FROM apartaments dest
                    having distance < " . $distancia . " AND precio >" . $min . " AND precio <" . $max . " ORDER BY distance ASC;";
                    $ok = $this->connection->query($sql);
                    // echo $this->$conexion->error."<br><br>";
                    $row = $this->connection->affected_rows;
                    if ($row <= 0) {
                        echo "No existen apartamentos en las coordenadas indicadas Con el precio especificado";
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


                        $pdf->Cell(270, 8, 'PROMEDIO DE PRECIO DE LA ZONA POR PRECIO', 'B', 1, 'C');
                        $pdf->Cell(270, 8, 'Precio Maximo: ' . $max . ' || Precio Minimo: ' . $min, 0, 1, 'C');
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

                        $ok = $this->connection->query($sql);
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
                        $ok = $this->connection->query($sql);
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
                break;

            default:
                echo utf8_decode("Opción invalida!!");
                break;
        }
    }
}