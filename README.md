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

El sistema es capaz de realizar 3 funciones, importar un archivo CSV, generar un reporte PDF básico ya sea global basado en km o combinado con filtros y un reporte PDF con estadístcas global basado en km o combinado con filtros

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


##### Reporte Básico

```php
<?php

include __DIR__ . "/../vendor/autoload.php";

use leifermendez\scrapper_calculator\Calculator;

$calculator = new Calculator();
$calculator->calculator(      
    '40.4238647', //latitud
    '-3.700173', //longitud
    1, // Rango Km -- Hasta aca los argumentos si solo desea busqueda global
    [   //array para filtros
       'ciudad' => [ // filtro de busqueda por ciudad 
            'symbol' => '>',
            'value' => 'madrid' //ciudad a buscar
        ],
        'bano' => [  // Nombre del campo en la bd por el cual filtrar 
            'symbol' => '=', // Condicion del WHERE
            'value' => 3 // Valor a ser buscado
        ],
        'precio' => [  //filtro por precio 
            'symbol' => '=', // Condicion del WHERE
            'value'=>[600,1000]//min - max
        ]
    ]
);

var_dump($calculator);
```


 ##### Reporte Estadístico

 ```php
<?php

include __DIR__ . "/../vendor/autoload.php";

use leifermendez\scrapper_calculator\Calculator;

$calculator = new Calculator();
$calculator->calculatorChart(    
    '40.4238647', //latitud
    '-3.700173', //longitud
    1, // Rango Km -- Hasta aca los argumentos si solo desea busqueda global
    [   //array para filtros
       'ciudad' => [ // filtro de busqueda por ciudad 
            'symbol' => '>',
            'value' => 'madrid' //ciudad a buscar
        ],
        'bano' => [ // Nombre del campo en la bd por el cual filtrar  
            'symbol' => '=', // Condicion del WHERE
            'value' => 3 // Valor a ser buscado
        ],
        'precio' => [  //filtro por precio 
            'symbol' => '=', // Condicion del WHERE
            'value'=>[600,1000]//min - max
        ]
    ]
);

var_dump($calculator);
```
 
 
 [Leifer M](https://leifermendez.github.io) - 
 [Arturo L](https://github.com/arturoluona)