<?php

/**
 * Class Ajustes
 */

namespace leifermendez\rbs_accommodations;

use Exception;
use mysqli;

class Settings
{
    public static $VERSION = '1.0.1';

    public static $DB_HOST = '127.0.0.1';
    public static $DB_NAME = 'idealista_csv';
    public static $DB_USER = 'root';
    public static $DB_PASSWORD = '';
    public static $DB_PORT = '3306';
    public static $DB_TABLE = 'apartaments';

    public static $KM_DEFAULT = 1;
    public static $KM_DEFAULT_INIT = 0.62137;

    public static $REPORT_FILENAME = 'reporte_';
    public static $REPORT_GLOBAL_FILENAME = 'reporte_global_';
    public static $REPORT_FILTERS_FILENAME = 'reporte_filters_';
    public static $REPORT_PRECIO_FILENAME = 'reporte_precio_';


    /**
     * File CSV fields.
     */


    public static $FIELDS = array(
        'Latitud' => ['value' => NULL, 'position' => NULL],
        'Longitud' => ['value' => NULL, 'position' => NULL],
        'ID' => ['value' => NULL, 'position' => NULL],
        'Titulo' => ['value' => NULL, 'position' => NULL],
        'Anunciante' => ['value' => NULL, 'position' => NULL],
        'Descripcion' => ['value' => NULL, 'position' => NULL],
        'Reformado' => ['value' => NULL, 'position' => NULL],
        'Telefonos' => ['value' => NULL, 'position' => NULL],
        'Fecha' => ['value' => NULL, 'position' => NULL],
        'Tipo' => ['value' => NULL, 'position' => NULL],
        'Precio' => ['value' => NULL, 'position' => NULL],
        'Precio por metro' => ['value' => NULL, 'position' => NULL],
        'Direccion' => ['value' => NULL, 'position' => NULL],
        'Provincia' => ['value' => NULL, 'position' => NULL],
        'Ciudad' => ['value' => NULL, 'position' => NULL],
        'Calle' => ['value' => NULL, 'position' => NULL],
        'Barrio' => ['value' => NULL, 'position' => NULL],
        'Distrito' => ['value' => NULL, 'position' => NULL],
        'Metros cuadrados' => ['value' => NULL, 'position' => NULL],
        'Baños' => ['value' => NULL, 'position' => NULL],
        'Segunda mano' => ['value' => NULL, 'position' => NULL],
        'Armarios Empotrados' => ['value' => NULL, 'position' => NULL],
        'Construido en' => ['value' => NULL, 'position' => NULL],
        'Cocina Equipada' => ['value' => NULL, 'position' => NULL],
        'Amueblado' => ['value' => NULL, 'position' => NULL],
        'Cocina equipada' => ['value' => NULL, 'position' => NULL],
        'Certificación energética' => ['value' => NULL, 'position' => NULL],
        'Planta' => ['value' => NULL, 'position' => NULL],
        'Exterior' => ['value' => NULL, 'position' => NULL],
        'Interior' => ['value' => NULL, 'position' => NULL],
        'Ascensor' => ['value' => NULL, 'position' => NULL],
        'Aire acondicionado' => ['value' => NULL, 'position' => NULL],
        'Habitaciones' => ['value' => NULL, 'position' => NULL],
        'Balcón' => ['value' => NULL, 'position' => NULL],
        'Trastero' => ['value' => NULL, 'position' => NULL],
        'Metros cuadrados útiles' => ['value' => NULL, 'position' => NULL],
        'Piscina' => ['value' => NULL, 'position' => NULL],
        'Jardín' => ['value' => NULL, 'position' => NULL],
        'Parking' => ['value' => NULL, 'position' => NULL],
        'Terraza' => ['value' => NULL, 'position' => NULL],
        'Calefacción individual' => ['value' => NULL, 'position' => NULL],
        'Apto para personas con movilidad reducida' => ['value' => NULL, 'position' => NULL],
        'Se admiten mascotas' => ['value' => NULL, 'position' => NULL],
    );

    public static $FIELDS_DB = array(
        'latitud',
        'longitud',
        'id',
        'titulo',
        'anunciante',
        'descripcion',
        'reformado',
        'telefonos',
        'fecha',
        'tipo',
        'precio',
        'precioMetro',
        'direccion',
        'provincia',
        'ciudad',
        'calle',
        'barrio',
        'distrito',
        'metrosCuadrados',
        'bano',
        'segundaMano',
        'armarioEmpotrado',
        'construidoEn',
        'cocinaEquipada',
        'amueblado',
        'cocinaEquipad',
        'certificacionEnergetica',
        'planta',
        'exterior',
        'interior',
        'ascensor',
        'aireAcondicionado',
        'habitaciones',
        'balcon',
        'trastero',
        'metrosCuadradosUtiles',
        'piscina',
        'jardin',
        'parking',
        'terraza',
        'calefaccionIndividual',
        'movilidadReducida',
        'mascotas'
    );


    public function __construct($settings = array())
    {
        try {

            self::$DB_HOST = (isset($settings['DB_HOST'])) ? (isset($settings['DB_HOST'])) : self::$DB_HOST;
            self::$DB_USER = (isset($settings['DB_USERNAME'])) ? (isset($settings['DB_USERNAME'])) : self::$DB_USER;
            self::$DB_PASSWORD = (isset($settings['DB_PASSWORD'])) ? (isset($settings['DB_PASSWORD'])) : self::$DB_PASSWORD;
            self::$DB_NAME = (isset($settings['DB_DATABASE'])) ? (isset($settings['DB_DATABASE'])) : self::$DB_NAME;
            self::$DB_PORT = (isset($settings['DB_PORT'])) ? (isset($settings['DB_PORT'])) : self::$DB_HOST;
            self::$DB_TABLE = (isset($settings['DB_TABLE_SCRAPPER'])) ? (isset($settings['DB_TABLE_SCRAPPER'])) : self::$DB_TABLE;

            $con = new mysqli (
                self::$DB_HOST,
                self::$DB_USER,
                self::$DB_PASSWORD,
                self::$DB_NAME,
                self::$DB_PORT
            );
            if ($con->connect_error) {
                throw new Exception($con->connect_error);
            }
            $con->set_charset("utf8mb4");

            $sql = "SELECT * FROM " . self::$DB_NAME . self::$DB_TABLE;
            $result = $con->query($sql);

            if (!$result) {
                $con->query('USE ' . self::$DB_NAME . ';');
                $con->query($sql);
                $sql = file_get_contents(__DIR__ . '/../src/sql/table_db.sql');
                $con->query($sql);
            }

            return $con;
        } catch (Exception $e) {
            return false;
        }
    }

}
