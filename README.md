# scrapper-calculator

Requisitos del sistema

Estoy usando PHP en la version "7.3.8 " y mysqli version "10.4.6-MariaDB"

Para importar un archivo CSV:

se hace llamado del metodo importCSV(direccion en que se encuentra el archivo CSV);
De esta manera ya estaria realizada la importacion del archivo CSV en la base de datos


Para hacer una busqueda de una zona mediante coordenadas:
.
se hace llamado del metodo calculatorGlobal(Latitud,Longitud,Kilometros del radio a buscar los apartamentos); se pasan los parametos indicados,
al hacer este llamado con los parametros indicados se ejecuta el metodo, creando un PDF donde se indican los apartamentos en la zona y el promedio de precio de la zona, de ser el caso en que no hayan apartamentos en la zona, se pintara: "No existen apartamentos en las coordenadas indicadas". este metodo retorna un array bidimencional con todos los datos de cada apartamento


Para hacer una busqueda de una zona mediante coordenadas y filtro:

se hace llamado del metodo calculatorGlobalFilters(Latitud,Longitud,Kilometros del radio a buscar los apartamentos,$array); se pasan los parametos indicados,al hacer este llamado con los parametros indicados se ejecuta el metodo, creando un PDF donde se indican los apartamentos en la zona y el promedio de precio de la zona, de ser el caso en que no hayan apartamentos en la zona, se pintara: "No existen apartamentos en esta zona con el filtro añadido". este metodo retorna un array bidimencional con todos los datos de cada apartamento

el $array que se pasa como parametro en el metodo lleva lo siguiente, array("atributo a buscar correspondiente a la base de datos" => valor a ser buscado, asi sucesivamente con todos los filtros a ser requeridos a busqueda)
