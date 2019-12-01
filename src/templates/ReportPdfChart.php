<?php 
	use Fpdf\Fpdf;
	
	$file_name = (isset($file_name)) ? $file_name : NULL;
	$price = (isset($price)) ? $price : 0;
	$pricemetro = (isset($pricemetro)) ? $pricemetro : 0;
	$planta = (isset($planta)) ? $planta : 0;
	$hab = (isset($hab)) ? $hab : 0;
	$bno = (isset($bno)) ? $bno : 0;
	$constr = (isset($constr)) ? $constr : 0;
	$amueblado = (isset($amueblado)) ? $amueblado : 0;
	$a_c = (isset($a_c)) ? $a_c : 0;
	$elevator = (isset($elevator)) ? $elevator : 0;
	$balcon = (isset($balcon)) ? $balcon : 0;
	$list_data = (isset($list_data)) ? $list_data : [];
	$filters = (isset($filters)) ? $filters : [];
	

	//calcula el promedio del precio de la zona
	if ($rows <= 0)
	{
	    $prom = 0;
		$promMetro = 0;
		$planta = 0;
		$hab = 0;
		$bno = 0;
		$constr = 0;
	}
	else
	{
	    $prom = $price / $rows;
		$promMetro = $pricemetro / $rows;
		$plan = $planta / $rows;
		$hb = $hab / $rows;
		$bn = $bno / $rows;
		$co = $constr / $rows;
	}	

	$pdf = new FPDF('P','mm','A4');

	$pdf ->SetFillColor('200','200','200');
	$pdf->SetDrawColor('194','194','194');
	$pdf->SetTextColor('55','55','55');
	$pdf->SetLineWidth(0.5);
	$pdf -> SetFont('Arial', '', 12);

	function headerPdf($pdf)
	{
		$pdf -> SetFont('Arial', '', 12);	
		$pdf->SetTextColor('55','55','55');
		$pdf -> Image(__DIR__ .'/../image/AH_LogoHost_Color.png', 6, 6, 60, 14);
		$pdf->Text(93,15,utf8_decode('Informe de Valoración'));
		//$pdf -> Image(__DIR__ .'/../image/maps.png', 185, 3, 20, 20, 'PNG');
		$pdf -> Line(8,23,200,23);
	}

	function footer($pdf)
	{
		$pdf->SetTextColor('134','134','134');
		$pdf -> Line(8,280,200,280);
		$pdf -> SetFont('Arial', '', 9);	
		$pdf->Text(8,290,'Alterhome');
		$pdf->Text(60,290,utf8_decode('Calle de Alfonso XII, 8, entreplanta, izquierda, 28014 Madrid'));		
		$pdf->Text(177,290,'+34 910 57 30 27');		
	}

	function apartamento ($pdf, $apart, $prom)
	{
		$beneficios = array(
			'aireAcondicionado' => 'A/C',
			'ascensor' => 'Ascensor',
			'exterior' => 'Exterior',
			'balcon' => 'Balcón'
		);
		$pdf->SetTextColor('55','55','55');
		$pdf -> SetFont('Arial', 'B', 17);

		$pdf->Text(75,33,utf8_decode("Promedio de la zona"));
		// foto / descripcion
		$pdf -> Image(__DIR__ .'/../image/prueba.jpg', 10, 40, 60, 60);

		$pdf -> SetFont('Arial', '', 14);
		$pdf -> SetY(40);
		$pdf -> Cell(65);
		$pdf->MultiCell(0,10,utf8_decode($apart['calle']), 0, 'J',false);
		$pdf->SetFontSize(12);
		$y=$pdf->GetY();
		$pdf->SetFontSize(12);
		$pdf->Text(75,$y+10,utf8_decode($apart['barrio']));
		$pdf->Text(75,$y+20,utf8_decode($apart['distrito']));
		$pdf->Text(76,$y+30,utf8_decode($apart['ciudad']));

		
		$pdf->Text(16,116.5,'Precio.');
		$pdf -> Image(__DIR__ .'/../image/price2.png', 38, 109, 40, 12);
		$pdf->Text(41,116.5,round($prom['prom'],2)." ". EURO." /mes");

		$pdf->Text(117,116.5,utf8_decode('Precio m².'));
		$pdf -> Image(__DIR__ .'/../image/price2.png', 143, 109, 40, 12);
		$pdf->Text(146,116.5,round($prom['promM'],2)." ". EURO." /mes");


		// Detalles
		$pdf -> Line(10,130,10,154);
		$pdf -> Line(40,130,40,154);
		$pdf -> Line(70,130,70,154);
		$pdf -> Line(110,130,110,154);
		$pdf -> Line(140,130,140,154);

		$pdf->Text(13,134,utf8_decode('Tipología'));
		$pdf->Text(43,134,'Planta');
		$pdf->Text(73,134,'Habitaciones');
		$pdf->Text(113,134,utf8_decode('Baños'));
		$pdf->Text(143,134,'Superficie Construida');

		$pdf -> SetFont('Arial', 'B', 16);
		$pdf->Text(18,146,$apart['tipo']);
		$pdf->Text(52,146,round($prom['planta'],0));
		$pdf->Text(88,146,round($prom['habitaciones'],0));
		$pdf->Text(124,146,round($prom['bano'],0));
		$pdf->Text(153,146,round($prom['construccion'],2).utf8_decode(' m²'));
		$pdf -> SetFont('Arial', '', 12);

		$pdf -> Line(8,160,207,160);
		$pdf -> SetFont('Arial', '', 12);
	}

	//Portada
	$pdf -> AddPage();	
	$pdf->SetTextColor('225','225','225');
	$pdf->SetFontSize(20);
	$date = getdate();
	$pdf->image(__DIR__.'/../image/portada.jpg',0,0,250,300,'JPG');
	$pdf->image(__DIR__.'/../image/AH_LogoHost_Blanco.png',50,10,120,29,'PNG');
	$pdf->Text(70,60,utf8_decode('INFORME DE VALORACIÓN'));
	$pdf->SetFontSize(12);
	 //cambio de ingles a español
	 $meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
  	 $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
  	$mes = str_replace($meses_EN,$meses_ES,$date['month']);
	$pdf->Text(150,280,utf8_decode('Fecha de emisión'));
	$pdf->Text(145,286,$date['mday']." de ".$mes." de ".$date['year']);

	//contra-portada
	$pdf -> AddPage();	
	$pdf->SetTextColor('55','55','55');
	$pdf->SetFontSize(40);
	$pdf->Text(10,40,'Inmueble');
	$pdf->SetTextColor('80','80','80');
	$pdf->SetFontSize(12);
	$pdf->Ln(38);
	$pdf->MultiCell(0,10,utf8_decode($apart['direccion']),0,'J',false);
	$pdf->Ln(50);
	$pdf -> Cell(30,15,'Alquiler','L',0,'C',true);
	$pdf -> Image(__DIR__ .'/../image/AH_LogoHost_Color.png', 50, 220, 120,27);

	//Pagina datos 
	$pdf -> AddPage();
	$pdf -> SetFont('Arial', '', 12);	
	$pdf->SetTextColor('55','55','55');
	define('EURO', chr(128));
	headerPdf($pdf);	

	

	apartamento($pdf, $apart,[ 'prom' => $prom, 'promM' => $promMetro, 'planta' => $plan, 'habitaciones' => $hb, 'bano' => $bn, 'construccion' => $co]);

	//Reporte Global-1
	$pdf->SetFontSize(16);
	$pdf->Text(73,170,'Detalle global de la zona');
	$pdf->SetFontSize(12);

	//tamaños title
	$pdf->Text(23,180,'Precio');
	$pdf->Text(63,180,'Apartamentos');	
	$pdf->Text(101.5,180,utf8_decode('Edad media edificación'));
	$pdf->Text(155,180,utf8_decode('Radio de búsqueda'));
	$pdf->Text(26,228,'A/C');
	$pdf->Text(67,228,'Ascensor');
	$pdf->Text(118,228,utf8_decode('Balcón'));	
	$pdf->Text(164,228,'Amueblado');

	$pdf->SetTextColor('100','100','100');
	$pdf->Text(43,267,'Estos valores son la cantidad de apartamentos en base a la zona');
	$pdf->SetTextColor('55','55','55');
	$pdf -> SetFont('Arial', 'I', 9);
	$pdf->Text(58,272,'( Ejemplo: hay '.$elevator.' apartamentos de '.$rows.' que cuentan con ascensor )');

	//data
	$pdf -> SetFont('Arial', 'B', 12.5);
	//precio	
	$pdf -> Line(8,175,8,210);
	$pdf -> Image(__DIR__ .'/../image/rounded_blue.png', 18, 185, 25, 27);
	$pdf->Text(20,200,round($prom,2)." ".EURO);

	//apartamentos
	$pdf -> Line(53,175,53,210);	
	$pdf -> Image(__DIR__ .'/../image/rounded_blue.png', 64, 185, 25, 27);
	$pdf->Text(73,200,$rows);

	//edad media
	$pdf -> Line(100,175,100,210);
	$pdf -> Image(__DIR__ .'/../image/rounded_blue.png', 112, 185, 25, 27);

		//calculo edad
		$sumedad=0;
		$rowsedad=0;
		foreach ($edad as $value) {
			if (intval($value) > 1000 && intval($value) < intval($date['year'])) {
				$sumedad += intval($date['year'])-intval($value);
				$rowsedad++;
			}			
		}
		$edad = $sumedad/$rowsedad;
	$pdf->Text(121,200,round($edad));

	// Radio de busqueda
	$pdf -> Line(150,175,150,210);
	$pdf -> Image(__DIR__ .'/../image/rounded_blue.png', 162, 185, 25, 27);
	$pdf->Text(172.5,200,$km);
	$pdf -> Line(200,175,200,210);			
	$pdf -> SetFont('Arial', '', 11);
	$pdf->Text(138,189,utf8_decode('Años'));
	$pdf->Text(189,189,utf8_decode('Km³'));	
	$pdf -> SetFont('Arial', 'B', 13.5);

	//Reporte Global-2
	$pdf -> Line(8,218,200,218);

	// A/C
	$pdf -> Line(8,223,8,270);
	$pdf -> Image(__DIR__ .'/../image/rounded_blue.png', 18, 232, 25, 27);
	$pdf->Text(27,247,$a_c);

	//ascensor
	$pdf -> Line(53,223,53,258);	
	$pdf -> Image(__DIR__ .'/../image/rounded_blue.png', 64, 232, 25, 27);
	$pdf->Text(73,247,$elevator);

	// balcón
	$pdf -> Line(100,223,100,258);
	$pdf -> Image(__DIR__ .'/../image/rounded_blue.png', 112, 232, 25, 27);
	$pdf->Text(121.5,247,$balcon);

	// Amueblado
	$pdf -> Line(150,223,150,258);
	$pdf -> Image(__DIR__ .'/../image/rounded_blue.png', 162, 232, 25, 27);
	$pdf->Text(172,247,$amueblado);
	$pdf -> Line(200,223,200,270);

	footer($pdf);

	//GRAFICAS
	if (!count($filters)){
	    include_once (__DIR__.'/Chart.php');
	    
	    $imgHab = habitacion($lat, $lng, $measure, $this->TOOLS, $this->connection);
	    $imgBano = bano($lat, $lng, $measure, $this->TOOLS, $this->connection);
	    $imgPrice = price($lat, $lng, $measure, $this->TOOLS, $this->connection);

	    $pdf->AddPage();
		headerPdf($pdf);
		$pdf -> SetFont('Arial', '', 15);
		$pdf->SetTextColor('50','50','50');

		
	    $pdf->Text(40,40,'Habitaciones');
	    $pdf -> Image($imgHab, 15, 45, 80, 75);

		
	    $pdf->Text(150,40,utf8_decode('Baños'));
	    $pdf -> Image($imgBano, 120, 45, 80, 75);

		
	    $pdf -> Image($imgPrice, 28, 135, 170, 150);
	    $pdf->Text(54,150,utf8_decode('Cantidad de apartamentos según su precio'));

	    $pdf -> Line(10,140,207,140);
	    $pdf -> Line(106,35,106,135);

	    $pdf->SetTextColor('110','110','110');
	    $pdf->SetFontSize(12);

	    $pdf->Text(160,272,'Precio ( '.EURO." )");
	    $pdf->Text(3,200,'Apartamentos');
	    $pdf->Text(17,130,utf8_decode('Porcentaje según cantidad de habitaciones'));
	    $pdf->Text(126,130,utf8_decode('Porcentaje según cantidad de baños'));
	    footer($pdf);

	}else{
	    //en caso de haber añadido algun filtro
	}

	$pdf ->Output('F',$file_name);//