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

    public function Calculator($lat, $lng, $km = 1, $filters = [])
    {
        $measure = floatval($km * parent::$KM_DEFAULT_INIT);
        $file_name = __DIR__ . '/../output';
        $price = 0;
        $average = 0.0;
        $list_data = array();
        
        $file_name .= "/" . parent::$REPORT_FILENAME . time() . ".pdf";
        if (!count($filters)) {                    
            $sql = $this->TOOLS->SQLRange($lat, $lng, $measure);
        } else {
            $sql = $this->TOOLS->SQLRange($lat, $lng, $measure, $filters);
        }
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
}