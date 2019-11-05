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
                $file_name .= "/" . parent::$REPORT_PRECIO_FILENAME . time() . ".pdf";
                if (!$min_price && !$max_price) {
                    echo self::$ERROR->ERROR_NOT_MIN_MAX;
                } else {

                    $filters['precio']= [
                        'symbol' => '>',
                        'value' => $min_price
                    ];
                  

                    //var_dump(json_encode($filters));

                    //exit();
                    $sql = $this->TOOLS->SQLRange($lat, $lng, $measure, $filters);
                   /* $sql = "SELECT *, 3956 * 2 * ASIN(SQRT(
                    POWER(SIN((" . $lat . " - abs(dest.latitud)) * pi()/180 / 2),
                    2) + COS(" . $lat . " * pi()/180 ) * COS(abs(dest.latitud) *
                    pi()/180) * POWER(SIN((" . $lng . " - dest.longitud) *
                    pi()/180 / 2), 2) )) as distance
                    FROM apartaments dest
                    having distance < " . $measure . " AND precio >" . $min_price . " AND precio <" . $max_price . " ORDER BY distance ASC;";*/
                  
                    $ok = $this->connection->query($sql);
                    // echo $this->$conexion->error."<br><br>";
                    $rows = $this->connection->affected_rows;
                    if (!$rows){
                        echo self::$ERROR->ERROR_NOT_EXIST;
                    } else {
                        while (($datum = $ok->fetch_assoc())) {
                            $price += $datum['precio'];
                            $list_data[] = $datum;
                    }
                    /*
                    if ($row <= 0) {
                        echo "No existen apartamentos en las coordenadas indicadas Con el precio especificado";
                        die();
                    } else {
                        $precio = 0;
                        $prom = 0.0;
                        while (($dato = $ok->fetch_assoc()) > 0) {
                            $precio += $dato['precio'];
                        }
                    */
                        //ARCHIVO PDF
                        include_once(__DIR__ . '/templates/ReportPrecio.php');
                        echo self::$ERROR->MSG_SUCCESS . ": \n" . $file_name;
                        return $file_name;
                        
                    }
                }
                break;

            default:
                echo utf8_decode("Opción invalida!!");
                break;
        }
    }
}