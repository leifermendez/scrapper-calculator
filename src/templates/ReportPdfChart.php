<?php 
	
	$file_name = (isset($file_name)) ? $file_name : NULL;
	$price = (isset($price)) ? $price : 0;
	$second_hand = (isset($second_hand)) ? $second_hand : 0;
	$cocina = (isset($cocina)) ? $cocina : 0;
	$a_c = (isset($a_c)) ? $a_c : 0;
	$elevator = (isset($elevator)) ? $elevator : 0;
	$balcon = (isset($balcon)) ? $balcon : 0;
	$exterior = (isset($exterior)) ? $exterior : 0;
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
	$pdf -> SetFont('Courier', '', 12);

	function headerPdf($pdf)
	{
		$pdf -> Image(__DIR__ .'/../image/urbanData.jpg', 7, 5, 40, 15);
		$pdf->Text(75,15,'Informe de Valoracion');
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
	$pdf->Text(80,40,utf8_decode('INFORME DE VALORACIÓN'));
	$pdf->SetFontSize(12);
	$pdf->Text(150,330,utf8_decode('Fecha de emisión'));
	$pdf->Text(145,339,$date['mday']." de ".$date['month']." de ".$date['year']);

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
	$pdf -> Cell(30,15,'Alquiler','L',0,'J',true);
	$pdf -> Image(__DIR__ .'/../image/urbanData.jpg', 70, 290, 60, 30);

	//Pagina datos 
	$pdf -> AddPage();
	$pdf -> SetFont('Courier', '', 12);	
	$pdf->SetTextColor('55','55','55');
	define('EURO', chr(128));
	headerPdf($pdf);	

	// foto / descripcion
	$pdf -> Image(__DIR__ .'/../image/prueba.jpg', 10, 40, 60, 60);
	$pdf->SetFontSize(20);
	$pdf->Text(75,45,utf8_decode($apart['calle']));
	$pdf->SetFontSize(12);
	$pdf->Text(75,55,utf8_decode($apart['barrio']));
	$pdf->Text(75,65,utf8_decode($apart['distrito']));
	$pdf->Text(77,75,utf8_decode($apart['ciudad']));
	$pdf -> Ln(95);
	$pdf->MultiCell(0,10,' Titulo: '.utf8_decode($apart['titulo']).". "." Precio: ".$apart['precio'].EURO, 0, 'J',false);

	// Detalles
	$pdf -> Ln(20);
	$pdf -> Cell(30,10,' Tipologia','L',0,'J');
	$pdf -> Cell(30,10,' Planta','L',0,'J');
	$pdf -> Cell(40,10,' Habitaciones','L',0,'J');
	$pdf -> Cell(30,10,utf8_decode(' Baños'),'L',0,'J');
	$pdf -> Cell(30,10,' Superficie Construida','L',1,'J');
	$pdf -> SetFont('Courier', 'B', 16);
	//datos
	$pdf -> Cell(30,10,$apart['tipo'],'L',0,'C');
	$pdf -> Cell(30,10,$apart['planta'],'L',0,'C');
	$pdf -> Cell(40,10,$apart['habitaciones'],'L',0,'C');
	$pdf -> Cell(30,10,$apart['bano'],'L',0,'C');
	$pdf -> Cell(50,10,$apart['metrosCuadrados'].utf8_decode('²'),'L',0,'C');

	$pdf -> Line(8,165,200,165);

	// Beneficios del apartamento
	$pdf -> Ln(28);
	$pdf -> SetFont('Courier', '', 12);
	foreach ($beneficios as $key => $value) {
		if ($apart[$key]) {
			$pdf -> Cell(40,10,utf8_decode($value),0,0,'C',true);
			$pdf -> Cell(5);
		}
	}

	//Reporte Global-1
	$pdf -> Line(8,190,200,190);
	$pdf->SetFontSize(16);
	$pdf->Text(60,200,'Detalle global de la zona');
	$pdf->SetFontSize(12);

	$pdf -> Line(8,210,8,250);
	$pdf->Text(22,215,'Precio');
	$pdf -> Image(__DIR__ .'/../image/redondo.png', 15, 218, 30, 35);
	$pdf->Text(20,238,round($prom,2).EURO);

	$pdf -> Line(53,210,53,250);	
	$pdf->Text(60,215,'Apartamentos');
	$pdf -> Image(__DIR__ .'/../image/redondo.png', 60, 218, 30, 35);
	$pdf->Text(71,238,$rows);

	$pdf -> Line(100,210,100,250);
	$pdf->Text(110,215,'Segunda mano');
	$pdf -> Image(__DIR__ .'/../image/redondo.png', 110, 218, 30, 35);
	$pdf->Text(121,238,$second_hand);

	$pdf -> Line(150,210,150,250);
	$pdf->Text(155,215,'Cocina Equipada');
	$pdf -> Image(__DIR__ .'/../image/redondo.png', 160, 218, 30, 35);
	$pdf->Text(172,238,$cocina);
	$pdf -> Line(200,210,200,250);

	//Reporte Global-2
	$pdf -> Line(8,260,200,260);

	$pdf -> Line(8,270,8,310);
	$pdf->Text(26,275,'A/C');
	$pdf -> Image(__DIR__ .'/../image/roundedL.png', 15, 278, 30, 35);
	$pdf->Text(25,297,$a_c);

	$pdf -> Line(53,270,53,310);	
	$pdf->Text(67,275,'Ascensor');
	$pdf -> Image(__DIR__ .'/../image/rounded.png', 60, 278, 30, 35);
	$pdf->Text(71,297,'120');

	$pdf -> Line(100,270,100,310);
	$pdf->Text(118,275,utf8_decode('Balcón'));
	$pdf -> Image(__DIR__ .'/../image/roundedL.png', 110, 278, 30, 35);
	$pdf->Text(121,297,$balcon);

	$pdf -> Line(150,270,150,310);
	$pdf->Text(165,275,'Exterior');
	$pdf -> Image(__DIR__ .'/../image/rounded.png', 160, 278, 30, 35);
	$pdf->Text(171,297,$exterior);
	$pdf -> Line(200,270,200,310);

	footer($pdf);
	$pdf ->Output('F',$file_name);