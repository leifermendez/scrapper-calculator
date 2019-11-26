<?php 
	
	$file_name = (isset($file_name)) ? $file_name : NULL;
	$price = (isset($price)) ? $price : 0;
	$amueblado = (isset($amueblado)) ? $amueblado : 0;
	$a_c = (isset($a_c)) ? $a_c : 0;
	$elevator = (isset($elevator)) ? $elevator : 0;
	$balcon = (isset($balcon)) ? $balcon : 0;
	$list_data = (isset($list_data)) ? $list_data : [];
	$filters = (isset($filters)) ? $filters : [];
	

	//calcula el promedio del precio de la zona
	if ($rows <= 0)
	    $prom = 0;
	else
	    $prom = $price / $rows;

	use Fpdf\Fpdf;
	

	$pdf = new FPDF('P','mm','Legal');

	$pdf ->SetFillColor('200','200','200');
	$pdf->SetDrawColor('194','194','194');
	$pdf->SetTextColor('55','55','55');
	$pdf->SetLineWidth(0.5);
	$pdf -> SetFont('Arial', '', 12);

	function headerPdf($pdf)
	{
		$pdf -> Image(__DIR__ .'/../image/AH_LogoHost_Color.png', 6, 6, 60, 14);
		$pdf->Text(93,15,utf8_decode('Informe de Valoración'));
		//$pdf -> Image(__DIR__ .'/../image/maps.png', 185, 3, 20, 20, 'PNG');
		$pdf -> Line(8,25,207,25);
	}

	function footer($pdf)
	{
		$pdf->SetTextColor('134','134','134');
		$pdf -> Line(8,333,207,333);
		$pdf->SetFontSize(9);
		$pdf->Text(8,346,'Alterhome');
		$pdf->Text(60,346,utf8_decode('Calle de Alfonso XII, 8, entreplanta, izquierda, 28014 Madrid'));		
		$pdf->Text(177,346,'+34 910 57 30 27');		
	}

	function apartamento ($pdf, $apart, $des)
	{
		$beneficios = array(
			'aireAcondicionado' => 'A/C',
			'ascensor' => 'Ascensor',
			'exterior' => 'Exterior',
			'balcon' => 'Balcón'
		);
		$pdf->SetTextColor('55','55','55');

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

		
		$pdf->Text(13,116.5,'Precio.');
		$pdf -> Image(__DIR__ .'/../image/price2.png', 38, 109, 35, 12);
		$pdf->Text(43,116.5,$apart['precio']." ". EURO);

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
		$pdf->Text(52,146,$apart['planta']);
		$pdf->Text(88,146,$apart['habitaciones']);
		$pdf->Text(124,146,$apart['bano']);
		$pdf->Text(153,146,$apart['metrosCuadrados'].utf8_decode(' m²'));
		$pdf -> SetFont('Arial', '', 12);

		$pdf -> Line(8,165,207,165);
		$pdf -> SetFont('Arial', '', 12);

		//Beneficios apartamentos
		$coun = 0;
		$ejeX = 9;
		foreach ($beneficios as $key => $value) {
			if ($apart[$key]) {
				$pdf -> Rect($ejeX, 172, 40, 10, 'F');
				$pdf->Text($ejeX+13,178.4,utf8_decode($value));
				$ejeX+=45;
				$coun++;
			}
		}
		if($coun == 0)
		{
			$pdf -> Rect(23, 172, 160, 10, 'F');
			$pdf->Text(35,178.4,utf8_decode('Apartamento no posee: Aire Acondicionado, Exterior, Ascensor, Balcón.'));
		}

		$pdf -> Line(8,190,207,190);


		$pdf -> SetFont('Arial', 'B', 14);
		switch ($des) {
			case '7'://primer apartamento
					$pdf->Text(60,34,utf8_decode("Apartamento más cercano a la ubicación"));
				break;
			case '8': // economicio
					$desc = substr($apart['descripcion'], 0, 1200);
					$pdf->Text(74,34,utf8_decode("Apartamento más económico"));
					$pdf->SetFontSize(16);
					$pdf->Text(11,200,utf8_decode("Descripción: "));
					$pdf -> SetFont('Arial', '', 11);
					$pdf -> SetY(205);
					$pdf->MultiCell(0,10,utf8_decode($desc)."...", 0, 'J',false);
				break;
			case '9': //costoso
					$desc = substr($apart['descripcion'], 0, 1200);
					$pdf->Text(74,34,utf8_decode("Apartamento más costoso"));			
					$pdf->SetFontSize(16);
					$pdf->Text(11,200,utf8_decode("Descripción: "));
					$pdf -> SetFont('Arial', '', 11);
					$pdf -> SetY(205);
					$pdf->MultiCell(0,10,utf8_decode($desc)."...", 0, 'J',false);
				break;
		}
	}

	//Portada
	$pdf -> AddPage();	
	$pdf->SetTextColor('225','225','225');
	$pdf->SetFontSize(20);
	$date = getdate();
	$pdf->image(__DIR__.'/../image/portada.jpg',0,0,250,356,'JPG');
	$pdf->image(__DIR__.'/../image/AH_LogoHost_Blanco.png',50,10,120,29,'PNG');
	$pdf->Text(70,60,utf8_decode('INFORME DE VALORACIÓN'));
	$pdf->SetFontSize(12);
	 //cambio de ingles a español
	 $meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
  	 $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
  	$mes = str_replace($meses_EN,$meses_ES,$date['month']);
	$pdf->Text(150,330,utf8_decode('Fecha de emisión'));
	$pdf->Text(145,339,$date['mday']." de ".$mes." de ".$date['year']);

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
	$pdf -> Image(__DIR__ .'/../image/AH_LogoHost_Color.png', 50, 270, 120,27);

	//Pagina datos 
	$pdf -> AddPage();
	$pdf -> SetFont('Arial', '', 12);	
	$pdf->SetTextColor('55','55','55');
	define('EURO', chr(128));
	headerPdf($pdf);	

	

	apartamento($pdf, $apart, 7);

	//Reporte Global-1
	$pdf->SetFontSize(16);
	$pdf->Text(73,200,'Detalle global de la zona');
	$pdf->SetFontSize(12);

	//tamaños title
	$pdf->Text(23,215,'Precio');
	$pdf->Text(63,215,'Apartamentos');	
	$pdf->Text(101.5,215,utf8_decode('Edad media edificación'));
	$pdf->Text(155,215,utf8_decode('Radio de búsqueda'));
	$pdf->Text(26,275,'A/C');
	$pdf->Text(67,275,'Ascensor');
	$pdf->Text(118,275,utf8_decode('Balcón'));	
	$pdf->Text(164,275,'Amueblado');

	$pdf->SetTextColor('100','100','100');
	$pdf->Text(90,320,'Apartamentos');
	$pdf->SetTextColor('55','55','55');

	//data
	$pdf -> SetFont('Arial', 'B', 13.5);
	//precio	
	$pdf -> Line(8,210,8,250);
	$pdf -> Image(__DIR__ .'/../image/rounded_blue.png', 15, 218, 30, 35);
	$pdf->Text(20,238,round($prom,2)." ".EURO);

	//apartamentos
	$pdf -> Line(53,210,53,250);	
	$pdf -> Image(__DIR__ .'/../image/rounded_blue.png', 60, 218, 30, 35);
	$pdf->Text(71,238,$rows);

	//edad media
	$pdf -> Line(100,210,100,250);
	$pdf -> Image(__DIR__ .'/../image/rounded_blue.png', 110, 218, 30, 35);

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
	$pdf->Text(122,238,round($edad));

	// Radio de busqueda
	$pdf -> Line(150,210,150,250);
	$pdf -> Image(__DIR__ .'/../image/rounded_blue.png', 160, 218, 30, 35);
	$pdf->Text(172.5,238,$km);
	$pdf -> Line(200,210,200,250);			
	$pdf -> SetFont('Arial', '', 11);
	$pdf->Text(138,223,utf8_decode('Años'));
	$pdf->Text(189,223,utf8_decode('Km³'));	
	$pdf -> SetFont('Arial', 'B', 13.5);

	//Reporte Global-2
	$pdf -> Line(8,260,200,260);

	// A/C
	$pdf -> Line(8,270,8,320);
	$pdf -> Image(__DIR__ .'/../image/rounded_blue.png', 15, 278, 30, 35);
	$pdf->Text(26,297,$a_c);

	//ascensor
	$pdf -> Line(53,270,53,310);	
	$pdf -> Image(__DIR__ .'/../image/rounded_blue.png', 60, 278, 30, 35);
	$pdf->Text(71,297,$elevator);

	// balcón
	$pdf -> Line(100,270,100,310);
	$pdf -> Image(__DIR__ .'/../image/rounded_blue.png', 110, 278, 30, 35);
	$pdf->Text(122.5,297,$balcon);

	// Amueblado
	$pdf -> Line(150,270,150,310);
	$pdf -> Image(__DIR__ .'/../image/rounded_blue.png', 160, 278, 30, 35);
	$pdf->Text(171,297,$amueblado);
	$pdf -> Line(200,270,200,320);

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
	    $pdf -> Image($imgHab, 8, 45, 95, 95);

		
	    $pdf->Text(148,40,utf8_decode('Baños'));
	    $pdf -> Image($imgBano, 110, 45, 95, 95);

		
	    $pdf -> Image($imgPrice, 18, 168, 180, 160);
	    $pdf->Text(54,185,utf8_decode('Cantidad de apartamentos según su precio'));

	    $pdf -> Line(10,165,207,165);
	    $pdf -> Line(106,35,106,150);

	    $pdf->SetTextColor('110','110','110');
	    $pdf->SetFontSize(12);

	    $pdf->Text(160,318,'Precio ( '.EURO." )");
	    $pdf->Text(8,220,'Apartamentos');
	    $pdf->Text(17,148,utf8_decode('Porcentaje según cantidad de habitaciones'));
	    $pdf->Text(126,148,utf8_decode('Porcentaje según cantidad de baños'));
	    footer($pdf);

	}else{
	    //en caso de haber añadido algun filtro
	}

	//Pagina apartamento economico
	$pdf->AddPage();

	headerPdf($pdf);

	apartamento($pdf, $apartmin, 8);

	footer($pdf);

	//pagina aartamento costoso
	$pdf->AddPage();
	headerPdf($pdf);

	apartamento($pdf, $apartmax, 9);

	footer($pdf);

	$pdf ->Output('F',$file_name);//