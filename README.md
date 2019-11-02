# Reporte Idealista

> __Progreso del proyecto:__ Puedes ver el progreso del proyecto, sus próximas carácteristicas y en lo que se está trabajando actualmente. [Aquí](https://github.com/leifermendez/scrapper-calculator/projects/1)

#### Requisitos del sistema:

``` json
    "php": "^7.1.3",
    "ext-mysqli": "*",
    "fpdf/fpdf": "^1.81",
    "amenadiel/jpgraph": "^3.6"
```
 > Ver más en [composer.json](https://github.com/leifermendez/scrapper-calculator/blob/master/composer.json)
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