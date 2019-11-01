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
        self::$ERROR = new Errores();

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
                    $res_values = implode(',', $res_values);
                    $sql = "INSERT INTO {$this->table_name} ($fields_db) values ({$res_values});";
                    $this->connection->query($sql);
                    if ($this->connection->error) {
                        self::$ERROR->Log('ee');
                    }
                }
            }

            fclose($data_file['data_file']);
            echo self::$ERROR->MSG_IMPORT_SUCCESS;
            return self::$ERROR->MSG_IMPORT_SUCCESS;

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function Calculator($option, $lat, $lng, $km = 1, $filters = [], $max_price = null, $min_price = null)
    {
        $measure = floatval($km * parent::$KM_DEFAULT_INIT);
        $file_name = __DIR__ . '/../output';
        $price = 0;
        $average = 0.0;
        $list_data = array();
        switch ($option) {
            case 'global':
                $file_name .= "/" . parent::$REPORT_GLOBAL_FILENAME . time() . ".pdf";
                $sql = $this->TOOLS->SQLRange($lat, $lng, $measure);
                $ok = $this->connection->query($sql);
                $row = $this->connection->affected_rows;
                if (!$row) {
                    echo self::$ERROR->ERROR_NOT_FOUND;
                } else {
                    while (($datum = $ok->fetch_assoc())) {
                        $price += $datum['precio'];
                        $list_data[] = $datum;
                    }
                    include_once(__DIR__ . '/templates/ReportGlobal.php');
                    echo self::$ERROR->MSG_SUCCESS . ": \n" . $file_name;
                    return $file_name;
                }
                break;
            case 'filters':
                $file_name .= "/" . parent::$REPORT_FILTERS_FILENAME . time() . ".pdf";
                if (!count($filters)) {
                    echo self::$ERROR->ERROR_NOT_FILTERS;
                } else {

                    $sql = $this->TOOLS->SQLRange($lat, $lng, $measure, $filters);
                    $ok = $this->connection->query($sql);
                    $rows = $this->connection->affected_rows;
                    if (!$rows) {
                        echo self::$ERROR->ERROR_NOT_FOUND;
                    } else {
                        while (($datum = $ok->fetch_assoc())) {
                            $price += $datum['precio'];
                            $list_data[] = $datum;
                        }

                        include_once(__DIR__ . '/templates/ReportFilters.php');
                        echo self::$ERROR->MSG_SUCCESS . ": \n" . $file_name;
                        return $file_name;
                    }
                }
                break;

            case 'precio':

                /**
                 * ESTA FACTORIZALA BASADA EN LAS DOS DE ARRIBA
                 */
                if (!$min && !$max) {
                    echo "No se ha especificado el precio minimo y precio maximo";
                } else {

                    $sql = "SELECT *, 3956 * 2 * ASIN(SQRT(
                    POWER(SIN((" . $lat . " - abs(dest.latitud)) * pi()/180 / 2),
                    2) + COS(" . $lat . " * pi()/180 ) * COS(abs(dest.latitud) *
                    pi()/180) * POWER(SIN((" . $lon . " - dest.longitud) *
                    pi()/180 / 2), 2) )) as distance
                    FROM apartaments dest
                    having distance < " . $measure . " AND precio >" . $min . " AND precio <" . $max . " ORDER BY distance ASC;";
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