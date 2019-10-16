<?php 
	require '../fpdf/fpdf.php';
	class coordenadas
	{
		//CONEXION CON EL SERVIDOR
		function conexion()
		{
			$con = new mysqli ("localhost","root","","coordenadas");

			if ($con->connect_errno) 
			{
				echo "Lo sentimos, este sitio web está experimentando problemas";
			}
			else
				return $con;
		}

		function archivoCSV ($con,$fichero)
		{
			$x=0;
			$data=array();
			$archivo=fopen($fichero, "r");
			$values='';

			while (($data=fgetcsv($archivo,10000))==true) 
			{
				$x++;
				if ($x>1) 
				{
					for ($i=0; $i < 42; $i++) 
					{ 
						if ((strlen($data[$i]))<=0) 
						{
							$values.="0,";
						}
						else
						{
							if ($data[$i]=="TRUE" || $data[$i]=="FALSE") 
							{
								$values.=$data[$i].",";
							}
							else
							{
								$values.="'".$data[$i]."',";
							}
						}							
					}
					if ((strlen($data[42]))<=0) 
					{
						$values.="0";
					}
					else
					{
						$values.=$data[42];
					}

					$sql = "INSERT INTO coordenadas values ($values);";
					$ok = $con->query($sql);
					$values='';
					echo "<br>".$sql."<br>";
					echo "<br>".$con->error."<br>";
				}
			}
			fclose($archivo);
		}

		function promedioZona($con,$lat,$lon)
		{
			$sql = "SELECT *, 3956 * 2 * ASIN(SQRT(
				POWER(SIN((".$lat." - abs(dest.latitud)) * pi()/180 / 2),
				2) + COS(".$lat." * pi()/180 ) * COS(abs(dest.latitud) *
				pi()/180) * POWER(SIN((".$lon." - dest.longitud) *
				pi()/180 / 2), 2) )) as distance
				FROM coordenadas dest
				having distance < 0.621371;";
			$ok = $con->query($sql);

			$row=$con->affected_rows;
			if ($row<=0) 
			{
				echo "No existen apartamentos en las coordenadas indicadas";
				die();
			}
			else
			{
				$precio=0;
				$prom=0.0;
				while (($dato = $ok->fetch_assoc())>0) 
				{
					$precio+=$dato['precio'];			
				}
				
				//ARCHIVO PDF
				define('EURO',chr(128));
				$pdf = new FPDF('L','mm','A4');

				$pdf -> AddPage();
				$pdf -> SetFont('Arial', '', 10);

				if ($row<=0) 
					$prom=0;
				else
					$prom= $precio/$row;


				$pdf -> Cell(270,8,'PROMEDIO DE PRECIO DE LA ZONA',0,1,'C');
				$pdf -> Cell(270,8,'',0,1,'C');
				
				$pdf -> Cell(135,8,"Precio de la zona","B",0,'C');
				$pdf -> Cell(135,8,"Apartamentos en la zona","B",1,'C');
				$pdf -> Cell(135,8,round($prom,2)." ".EURO,0,0,'C');
				$pdf -> Cell(135,8,$row,0,1,'C');
				$pdf -> Cell(270,8,'',0,1,'C');


				$pdf -> Cell(85,8,"Titulo","B",0,'C');
				$pdf -> Cell(30,8,"Precio","B",0,'C');
				$pdf -> Cell(15,8,"Habitaciones","B",0,'C');
				$pdf -> Cell(15,8,utf8_decode("m²"),"B",0,'C');
				$pdf -> Cell(15,8,utf8_decode("Baños"),"B",0,'C');
				$pdf -> Cell(20,8,"Amueblado","B",0,'C');
				$pdf -> Cell(30,8,utf8_decode("Latitud"),"B",0,'C');
				$pdf -> Cell(30,8,utf8_decode("Longitud"),"B",0,'C');
				$pdf -> Cell(30,8,utf8_decode("Distancia"),"B",1,'C');

				$ok = $con->query($sql);
				while (($d = $ok->fetch_assoc())>0) 
				{	
					$pdf -> Cell(85,8,utf8_decode($d['titulo']),"B",0,'J');
					$pdf -> Cell(30,8,$d['precio']." ".EURO,"B",0,'C');
					$pdf -> Cell(20,8,$d['habitaciones'],"B",0,'C');
					$pdf -> Cell(15,8,$d['metrosCuadrados'].utf8_decode("²"),"B",0,'C');
					$pdf -> Cell(15,8,$d['bano'],"B",0,'C');

					if ($d['amueblado']==TRUE) 
						$pdf -> Cell(15,8,"Si","B",0,'C');
					else
						$pdf -> Cell(15,8,"No","B",0,'C');

					$dist=$d['distance']/0.62137;

					$pdf -> Cell(30,8,$d['latitud'],"B",0,'C');
					$pdf -> Cell(30,8,$d['longitud'],"B",0,'C');
					$pdf -> Cell(30,8,round($dist,4)." Km","B",1,'C');
				}

				$pdf ->Output();
				$contenido=array();
				$ok = $con->query($sql);
				$k=0; $j=0;
				while (($var=$ok->fetch_assoc())>0) 
				{
					foreach ($var as $key => $value) 
				    {
				    	$contenido [$k][$j] = $key." => ". $value;
				    	$j++;  	 
				    }
				    $k++;
				}

				return $contenido;
			}
		}

		function promedioZonaFiltro($con,$lat,$lon,$array)
		{
		    $row=sizeof($array);
		  	$where='';  
		  	$i=0;
		    foreach ($array as $key => $value) 
		    {
		    	if ($i<$row-1) 
		    		$where.=$key."=".$value." AND ";
		    	else
		    		$where.=$key."=".$value; 
		    	$i++;   	 
		    }


			$sql = "SELECT *, 3956 * 2 * ASIN(SQRT(
				POWER(SIN((".$lat." - abs(dest.latitud)) * pi()/180 / 2),
				2) + COS(".$lat." * pi()/180 ) * COS(abs(dest.latitud) *
				pi()/180) * POWER(SIN((".$lon." - dest.longitud) *
				pi()/180 / 2), 2) )) as distance
				FROM coordenadas dest WHERE ".$where." 				
				having distance < 0.621371 ORDER BY distance ASC;";
			$ok = $con->query($sql);

			$rows=$con->affected_rows;

			//verificacion de que exista en la base de datos la consulta
			if ($rows<=0) 
			{
				echo "No existen apartamentos en esta zona con el filtro añadido";
			}
			else
			{

				$precio=0;
				$prom=0.0;
				while (($dato = $ok->fetch_assoc())>0) 
				{
					$precio+=$dato['precio'];			
				}
				
				//ARCHIVO PDF
				$pdf = new FPDF('L','mm','A4');

				$pdf -> AddPage();
				$pdf -> SetFont('Arial', '', 10);

				//calcula el promedio del precio de la zona
				if ($rows<=0) 
					$prom=0;
				else
					$prom= $precio/$rows;

				//crea un string para mostrar en el pdf cuales fueron los filtros asignados
				$pdfarray='';
				foreach ($array as $key => $value) 
			    {
			    	$pdfarray.=$key." = ". $value." | ";  	 
			    }

				define('EURO',chr(128));			

				$pdf -> Cell(270,8,'PROMEDIO DE PRECIO DE LA ZONA FILTRADO',"B",1,'C');
				$pdf -> Cell(270,8,'( '.$pdfarray.' )',0,1,'C');
				$pdf -> Cell(270,8,'',0,1,'C');

				$pdf -> Cell(135,8,"Precio de la zona","B",0,'C');
				$pdf -> Cell(135,8,"Apartamentos en la zona","B",1,'C');
				$pdf -> Cell(135,8,round($prom,2)." ".EURO,0,0,'C');
				$pdf -> Cell(135,8,$rows,0,1,'C');
				$pdf -> Cell(270,8,'',0,1,'C');

				$pdf -> Cell(85,8,"Titulo","B",0,'C');
				$pdf -> Cell(30,8,"Precio","B",0,'C');
				$pdf -> Cell(15,8,"Habitaciones","B",0,'C');
				$pdf -> Cell(15,8,utf8_decode("m²"),"B",0,'C');
				$pdf -> Cell(15,8,utf8_decode("Baños"),"B",0,'C');
				$pdf -> Cell(20,8,"Amueblado","B",0,'C');
				$pdf -> Cell(30,8,utf8_decode("Latitud"),"B",0,'C');
				$pdf -> Cell(30,8,utf8_decode("Longitud"),"B",0,'C');
				$pdf -> Cell(30,8,utf8_decode("Distancia"),"B",1,'C');

				$ok = $con->query($sql);
				while (($d = $ok->fetch_assoc())>0) 
				{	
					$pdf -> Cell(85,8,utf8_decode($d['titulo']),"B",0,'J');
					$pdf -> Cell(30,8,$d['precio']." ".EURO,"B",0,'C');
					$pdf -> Cell(20,8,$d['habitaciones'],"B",0,'C');
					$pdf -> Cell(15,8,$d['metrosCuadrados'].utf8_decode("²"),"B",0,'C');
					$pdf -> Cell(15,8,$d['bano'],"B",0,'C');

					if ($d['amueblado']==TRUE) 
						$pdf -> Cell(15,8,"Si","B",0,'C');
					else
						$pdf -> Cell(15,8,"No","B",0,'C');

					$dist=$d['distance']/0.62137;

					$pdf -> Cell(30,8,$d['latitud'],"B",0,'C');
					$pdf -> Cell(30,8,$d['longitud'],"B",0,'C');
					$pdf -> Cell(30,8,round($dist,4)." Km","B",1,'C');
				}
				$pdf ->Output();

				$contenido=array();
				$ok = $con->query($sql);
				$k=0; $j=0;
				while (($var=$ok->fetch_assoc())>0) 
				{
					foreach ($var as $key => $value) 
				    {
				    	$contenido [$k][$j] = $key." => ". $value;
				    	$j++;  	 
				    }
				    $k++;
				}
				return $contenido;
			}			
		}
    }

    $obj=new coordenadas;
    $con=$obj->conexion();
    $obj->promedioZona($con,'40.420612','-3.6898051');

    

 ?>

