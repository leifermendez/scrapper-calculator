# scrapper-calculator

Requisitos del sistema

PHP version "7.3.8 " 
mysqli version "10.4.6-MariaDB"

Para importar un archivo CSV:

se hace llamado del metodo importCSV(direccion en que se encuentra el archivo CSV);
De esta manera ya estaria realizada la importacion del archivo CSV en la base de datos

Busqueda Global, Filters y Precio

Se hace llamado del metodo calculator ($opc, $lat, $lon, $km, $array, $max, $min)

$opc = 'opcion a ejecutar en el metodo ya sean ('global', 'filters', 'precio')'

	'global' : Ejecuta un archivo PDF con valores correspondientes a los apartamentos que estan dentro del radio y las coordenadas especificadas

	'filters' : Ejecuta un archivo PDF con valores correspondientes a los apartamentos que estan dentro del radio, coordenadas y el filtro especificado

	'precio' : Ejecuta un archivo PDF con valores correspondientes a los apartamentos que estan dentro del radio, coordenadas y el precio maximo y minimo especificado

$lat = 'latitud'

$lon = 'longitud'

$km = 'kilometros del radio de busqueda'

$array = lleva un array el cual es: array('filtro a buscar'=>'valor')

		Filtros de busqueda correspondiente a la base de datos:
	 	latitud, longitud, id,	titulo, anunciante,	descripcion, reformado,	telefonos, fecha,	tipo, precio, precioMetro, direccion, provincia, ciudad, calle, barrio,	distrito,	metrosCuadrados, bano, segundaMano,	armarioEmpotrado, construidoEn,	cocinaEquipada,	amueblado, cocinaEquipad, certificacionEnergetica, planta, exterior, interior, ascensor, aireAcondicionado,	habitaciones, balcon, trastero,	metrosCuadradosUtiles, piscina,	jardin,	parking, terraza, calefaccionIndividual, movilidadReducida,	mascotas

$max = 'precio maximo de apartamentos a buscar'

$min = 'precio minimo de apartamentos a buscar'