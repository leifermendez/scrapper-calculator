# scrapper-calculator

Requisitos del sistema

Estoy usando PHP en la version "7.3.8 " y mysqli version "10.4.6-MariaDB"

Para importar un archivo CSV:

Se instancia la clase, se llama al metodo conexion() para obtener la conexion con la base de datos.
se hace llamado del metodo archivoCSV($conexion,direccion en que se encuentra el archivo CSV);
De esta manera ya estaria realizada la importacion del archivo CSV en la base de datos


Para hacer una busqueda de una zona mediante coordenadas:

Se instancia la clase, se llama al metodo conexion() para obtener la conexion con la base de datos.
se hace llamado del metodo promedioZona($conexion,Latitud,Longitud); se pasan los parametos indicados,
al hacer este llamado con los parametros indicados se ejecuta el metodo, creando un PDF donde se indican los apartamentos en la zona y el promedio de precio de la zona, de ser el caso en que no hayan apartamentos en la zona, se pintara: "No existen apartamentos en las coordenadas indicadas". este metodo retorna un array bidimencional con todos los datos de cada apartamento


Para hacer una busqueda de una zona mediante coordenadas y filtro:

Se instancia la clase, se llama al metodo conexion() para obtener la conexion con la base de datos.
se hace llamado del metodo promedioZonaFiltro($conexion,Latitud,Longitud,$array); se pasan los parametos indicados,al hacer este llamado con los parametros indicados se ejecuta el metodo, creando un PDF donde se indican los apartamentos en la zona y el promedio de precio de la zona, de ser el caso en que no hayan apartamentos en la zona, se pintara: "No existen apartamentos en esta zona con el filtro añadido". este metodo retorna un array bidimencional con todos los datos de cada apartamento

el $array que se pasa como parametro en el metodo lleva lo siguiente, array("atributo a buscar correspondiente a la base de datos" => valor a ser buscado, asi sucesivamente con todos los filtros a ser requeridos a busqueda)
