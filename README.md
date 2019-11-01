# Reporte Idealista

#### Requisitos del sistema:

- PHP version "7.3.8 " 
- Mysqli version "10.4.6-MariaDB"

#### Uso:

El sistema es capaz de realizar 3 funciones, importar un archivo CSV, generar un reporte PDF global basado en un rango de km, y reporte PDF basado en km combinado con filtros

 `composer install`
 
##### Importar CSV

```php
<?php

include __DIR__ . "/../vendor/autoload.php";

use leifermendez\scrapper_calculator\Calculator;

$calculator = new Calculator();
$file = __DIR__ . '/../csv/2019-10-02T071802.756Z_alquiler-viviendas_madrid_villa-de-vallecas_ensanche-de-vallecas-la-gavia.csv';
$res = $calculator->importCSV($file);

var_dump($res);

```

##### Reporte Global

```php
<?php

include __DIR__ . "/../vendor/autoload.php";

use leifermendez\scrapper_calculator\Calculator;

$calculator = new Calculator();
$calculator->calculator(
'global', // Tipo de filtro
'40.4238647', //Latitud
'-3.700173', //Longitud
10 //Rango KM
);

var_dump($calculator);
```

##### Reporte Filtro

```php
<?php

include __DIR__ . "/../vendor/autoload.php";

use leifermendez\scrapper_calculator\Calculator;

$calculator = new Calculator();
$calculator->calculator(
'filters', // Tipo de filtro
'40.4238647', //Latitud
'-3.700173', //Longitud
10, //Rango KM
 [
        'bano' => [ // Nombre del campo en la bd por el cual filtrar
            'symbol' => '=', // Condición del WHERE "="
            'value' => 1 //Valor a buscar
        ]
    ]
);

var_dump($calculator);
```

Próximamente:
 - Reporte PDF con graficas
 
 
 [Leifer M](https://leifermendez.github.io) - 
 [Arturo L](https://github.com/arturoluona)