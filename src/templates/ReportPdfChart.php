<?php 
	
	$file_name = (isset($file_name)) ? $file_name : NULL;
	$price = (isset($price)) ? $price : 0;
	$amueblado = (isset($amueblado)) ? $amueblado : 0;
	$a_c = (isset($a_c)) ? $a_c : 0;
	$elevator = (isset($elevator)) ? $elevator : 0;
	$balcon = (isset($balcon)) ? $balcon : 0;
	$list_data = (isset($list_data)) ? $list_data : [];
	$filters = (isset($filters)) ? $filters : [];
	$beneficios = array(
			'aireAcondicionado' => 'A/C',
			'ascensor' => 'Ascensor',
			'exterior' => 'Exterior',
			'balcon' => 'Balcón'
		);

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
		$pdf -> Image(__DIR__ .'/../image/AH_LogoHost_Negro.png', 6, 6, 50, 17);
		$pdf->Text(85,15,'Informe de Valoracion');
		$pdf -> Image(__DIR__ .'/../image/REDpulse.png', 160, 10, 40, 8, 'PNG');
		$pdf -> Line(8,25,200,25);
	}

	function footer($pdf)
	{
		$pdf->SetTextColor('134','134','134');
		$pdf -> Line(8,333,200,333);
		$pdf->SetFontSize(9);
		$pdf->Text(8,346,'urbanData Analytics');
		$pdf->Text(78,346,utf8_decode('Almagro 22,5º | 28010 Madrid'));		
		$pdf->Text(150,343,'uda@urbanDataAnalytics.com');
		$pdf->Text(150,348,'+34 91 532 28 45');		
	}

	//Portada
	$pdf -> AddPage();	
	$pdf->SetTextColor('225','225','225');
	$pdf->SetFontSize(20);
	$date = getdate();
	$pdf->image(__DIR__.'/../image/portada.jpg',0,0,250,356,'JPG');
	$pdf->image(__DIR__.'/../image/AH_LogoHost_Blanco.png',56,10,100,30,'PNG');
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
	$pdf -> Image(__DIR__ .'/../image/AH_LogoHost_Color.png', 70, 270, 80, 30);

	//Pagina datos 
	$pdf -> AddPage();
	$pdf -> SetFont('Arial', '', 12);	
	$pdf->SetTextColor('55','55','55');
	define('EURO', chr(128));
	headerPdf($pdf);	

	// foto / descripcion
	$pdf -> Image(__DIR__ .'/../image/prueba.jpg', 10, 40, 60, 60);

	$pdf->SetFontSize(14);
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

	$pdf->Text(13,134,'Tipologia');
	$pdf->Text(43,134,'Planta');
	$pdf->Text(73,134,'Habitaciones.');
	$pdf->Text(113,134,utf8_decode('Baños.'));
	$pdf->Text(143,134,'Superficie Construida');

	$pdf -> SetFont('Arial', 'B', 16);
	$pdf->Text(18,146,$apart['tipo']);
	$pdf->Text(52,146,$apart['planta']);
	$pdf->Text(88,146,$apart['habitaciones']);
	$pdf->Text(124,146,$apart['bano']);
	$pdf->Text(153,146,$apart['metrosCuadrados'].utf8_decode(' m²'));
	$pdf -> SetFont('Arial', '', 12);

	$pdf -> Line(8,165,200,165);
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
		$pdf->Text(42,178.4,utf8_decode('Apartamento no posee: A/C .. Exterior .. Ascensor .. Balcón'));
	}

	//Reporte Global-1
	$pdf -> Line(8,190,200,190);
	$pdf->SetFontSize(16);
	$pdf->Text(73,200,'Detalle global de la zona');
	$pdf->SetFontSize(12);

	//tamaños title
	$pdf->Text(23,215,'Precio');
	$pdf->Text(63,215,'Apartamentos');	
	$pdf->Text(103,215,'Edad media edificacion');	
	$pdf->Text(121,250,utf8_decode('Años'));
	$pdf->Text(157,215,'Radio de busqueda');		
	$pdf->Text(171,250,utf8_decode('Km³'));	
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
	$pdf -> Image(__DIR__ .'/../image/rounded_green.png', 15, 218, 30, 35);
	$pdf->Text(20,238,round($prom,2)." ".EURO);

	//apartamentos
	$pdf -> Line(53,210,53,250);	
	$pdf -> Image(__DIR__ .'/../image/redondo.png', 60, 218, 30, 35);
	$pdf->Text(71,238,$rows);

	//edad media
	$pdf -> Line(100,210,100,250);
	$pdf -> Image(__DIR__ .'/../image/rounded_blue.png', 115, 220, 20, 23);

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

	$pdf->Text(122,233,round($edad));

	// Radio de busqueda
	$pdf -> Line(150,210,150,250);
	$pdf -> Image(__DIR__ .'/../image/rounded_blue.png', 164.5, 220, 20, 23);
	$pdf->Text(172.5,233,$km);
	$pdf -> Line(200,210,200,250);

	//Reporte Global-2
	$pdf -> Line(8,260,200,260);

	// A/C
	$pdf -> Line(8,270,8,320);
	$pdf -> Image(__DIR__ .'/../image/redondo.png', 15, 278, 30, 35);
	$pdf->Text(26,297,$a_c);

	//ascensor
	$pdf -> Line(53,270,53,310);	
	$pdf -> Image(__DIR__ .'/../image/redondo.png', 60, 278, 30, 35);
	$pdf->Text(71,297,$elevator);

	// balcón
	$pdf -> Line(100,270,100,310);
	$pdf -> Image(__DIR__ .'/../image/redondo.png', 110, 278, 30, 35);
	$pdf->Text(122.5,297,$balcon);

	// Amueblado
	$pdf -> Line(150,270,150,310);
	$pdf -> Image(__DIR__ .'/../image/redondo.png', 160, 278, 30, 35);
	$pdf->Text(171,297,$amueblado);
	$pdf -> Line(200,270,200,320);

	footer($pdf);
	$pdf ->Output();//'F',$file_name