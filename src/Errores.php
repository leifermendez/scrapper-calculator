<?php

namespace leifermendez\scrapper_calculator;
class Errores
{
    public $ERROR_NOT_FOUND_FILE_CSV = 'El archivo CSV no existe';
    public $ERROR_NOT_FOUND = 'Sin resultados';
    public $ERROR_NOT_FILTERS = 'Debes agregar filtros';
    public $ERROR_CONNECTION_DB = 'Lo sentimos, este sitio web está experimentando problemas';

    public $ERROR_NOT_MIN_MAX = 'No se ha especificado el precio minimo y precio maximo';
    public $ERROR_NOT_EXIST = 'No existen apartamentos en las coordenadas indicadas Con el precio especificado';

    public $MSG_SUCCESS = 'Reporte generado, puedes encontrarlo en la carpeta OUTPUT';
    public $MSG_IMPORT_SUCCESS = 'Exito';

    public function Log($data = 'll')
    {
        error_log($data . "  \n", 3, __DIR__ . '/LOG.txt');
    }
}