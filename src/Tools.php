<?php

namespace leifermendez\rbs_accommodations;

use Exception;

class Tools extends Settings
{
    public function SQLRange($lat, $lng, $measure, $conditions = array())
    {
        try {
            $where = [];
            $count = 0;
            foreach ($conditions as $key => $value) {
                if (!$count) {
                    if (gettype($value['value'])==='integer'){
                        $where[] = " WHERE ${key}{$value['symbol']}{$value['value']} ";
                    } else{
                        $where[] = " WHERE ${key} BETWEEN {$value['value'][0]} AND {$value['value'][1]}";
                    }
                } else {
                    if (gettype($value['value'])==='integer'){
                        $where[] = " AND ${key}{$value['symbol']}{$value['value']} ";
                    } else{
                        $where[] = " AND ${key} BETWEEN {$value['value'][0]} AND {$value['value'][1]}";
                    }
                }
                $count++;
            }
            $where_sql = implode(' ', $where);
            $table = parent::$DB_TABLE;
            $sql = "SELECT *, 3956 * 2 * ASIN(SQRT(
                POWER(SIN((" . $lat . " - abs(dest.latitud)) * pi()/180 / 2),
                2) + COS(" . $lat . " * pi()/180 ) * COS(abs(dest.latitud) *
                pi()/180) * POWER(SIN((" . $lng . " - dest.longitud) *
                pi()/180 / 2), 2) )) as distance
                FROM {$table} dest {$where_sql} 
                having distance < " . $measure . " ORDER BY distance ASC";

            return $sql;

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    private function TypeDatum($datum = null)
    {
        $tmp = '';
        switch (gettype($datum)) {
            case 'boolean':
                $tmp = ($datum) ? 1 : 0;
                break;
            case 'double':
                $tmp = floatval($datum);
                break;
            default:
                if ($datum === 'TRUE') {
                    $tmp = 1;
                } elseif ($datum === 'FALSE') {
                    $tmp = 0;
                } elseif ($datum == '0') {
                    $tmp = 0;
                } else {
                    $tmp = "'" . $datum . "'";
                }

                break;
        }

        return $tmp;
    }

    public function GetAllValues()
    {
        try {
            $values = [];
            foreach (parent::$FIELDS as $key => $value) {
                $tmp = self::Sanity($value['value']);
                $tmp = (strlen($tmp) < 1) ? 0 : $tmp;
                $tmp = ((float)$tmp) ? (float)$tmp : $tmp;
                $values[] = self::TypeDatum($tmp);
            }

            return $values;

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function GetValueField($position = NULL)
    {
        try {
            $data = array();
            if (!is_null($position)) {
                foreach (parent::$FIELDS as $key => $value) {

                    if ($value['position'] == $position) {
                        $data = array(
                            'position' => $position,
                            'value' => parent::$FIELDS[$key]['value'],
                            'key' => $key

                        );
                    }
                }
            }

            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function SetValueField($position = NULL, $v = NULL)
    {
        try {
            $data = array();
            if (!is_null($position) && !is_null($v)) {
                foreach (parent::$FIELDS as $key => $value) {

                    if ($value['position'] == $position) {
                        parent::$FIELDS[$key]['value'] = ($v === 'FALSE' || (strlen($v) < 1)) ? NULL : $v;
                        $data = array(
                            'position' => $position,
                            'value' => parent::$FIELDS[$key]['value'],
                            'key' => $key

                        );
                    }
                }
            }

            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function ReadCsv($file = NULL)
    {
        try {
            $data_file = fopen($file, "r");
            $data = fgetcsv($data_file, 10000);
            return [
                'data_file' => $data_file,
                'data' => $data
            ];

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function Sanity($s)
    {
        // $s = mb_convert_encoding($s, 'UTF-8', '');
        $s = preg_replace("/á|à|â|ã|ª/", "a", $s);
        $s = preg_replace("/Á|À|Â|Ã/", "A", $s);
        $s = preg_replace("/é|è|ê/", "e", $s);
        $s = preg_replace("/É|È|Ê/", "E", $s);
        $s = preg_replace("/í|ì|î/", "i", $s);
        $s = preg_replace("/Í|Ì|Î/", "I", $s);
        $s = preg_replace("/ó|ò|ô|õ|º/", "o", $s);
        $s = preg_replace("/Ó|Ò|Ô|Õ/", "O", $s);
        $s = preg_replace("/ú|ù|û/", "u", $s);
        $s = preg_replace("/Ú|Ù|Û/", "U", $s);
        $s = str_replace(" ", "_", $s);
        $s = str_replace("ñ", "n", $s);
        $s = str_replace("Ñ", "N", $s);

        $s = preg_replace('/[^a-zA-Z0-9_.-]/', '', $s);
        return $s;
    }
}