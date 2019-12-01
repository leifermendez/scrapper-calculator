<?php 

	
	use Amenadiel\JpGraph\Graph;
	use Amenadiel\JpGraph\Plot;

	function habitacion($lat, $lng, $km, $TOOLS, $connection)
	{
		for ($i=1; $i <= 7 ; $i++) { 
			if ($i<7) {
				$sql = $TOOLS->SQLRange($lat, $lng, $km, ['habitaciones' => ['value' => [$i,$i+1]]]);	
				$s = $i+1;		
           		$name[] = $i ." - ". $s;
			}
			else {
				$sql = $TOOLS->SQLRange($lat, $lng, $km, ['habitaciones' => ['symbol' => ' > ', 'value' => $i]]);
            	$name[] = $i;
			}

			$ok = $connection->query($sql);
            $data[] = $connection->affected_rows;
			$i++;
		}

		$graph    = new Graph\PieGraph(450, 400);
		$graph->SetShadow();

		// Setup the pie plot
		$p1 = new Plot\PiePlot($data);

		// Adjust size and position of plot
		$p1->SetSize(0.45);
		$p1->SetCenter(0.5, 0.52);

		// Setup slice labels and move them into the plot
		$p1->value->SetFont(FF_ARIAL);
		$p1->value->SetColor('#000');
		$p1->SetLabelPos(0.5);
		$p1->SetLegends($name);
		$p1->SetSliceColors(array("#F27F7F","#85EEF7","#D3D2D2","#FBEB9E"));
		// Explode all slices
		$p1->ExplodeAll(8);
		$p1->value->SetFont(FF_ARIAL, FS_NORMAL, 15);
		// Finally add the plot
		$graph->Add($p1);

		// ... and stroke it		
        @unlink(__DIR__."/../image/habitacion.png");
		$graph->Stroke(__DIR__.'/../image/habitacion.png');

		return __DIR__.'/../image/habitacion.png';
	}

	function bano($lat, $lng, $km, $TOOLS, $connection)
	{
		for ($i=1; $i <= 7 ; $i++) { 
			if ($i<7) {
				$sql = $TOOLS->SQLRange($lat, $lng, $km, ['bano' => ['value' => [$i,$i+1]]]);	
				$s = $i+1;		
           		$name[] = $i ." - ". $s;
			}
			else {
				$sql = $TOOLS->SQLRange($lat, $lng, $km, ['bano' => ['symbol' => ' > ', 'value' => $i]]);
            	$name[] = $i;
			}

			$ok = $connection->query($sql);
            $data[] = $connection->affected_rows;
			$i++;
		}

		$graph    = new Graph\PieGraph(450, 400);
		$graph->SetShadow();

		// Setup the pie plot
		$p1 = new Plot\PiePlot($data);

		// Adjust size and position of plot
		$p1->SetSize(0.45);
		$p1->SetCenter(0.5, 0.52);

		// Setup slice labels and move them into the plot
		$p1->value->SetFont(FF_ARIAL);
		$p1->value->SetColor('#000');
		$p1->SetLabelPos(0.5);
		$p1->SetLegends($name);
		$p1->SetSliceColors(array("#97E895","#D79167","#D3D2D2","#FBEB9E"));
		// Explode all slices
		$p1->ExplodeAll(8);
		$p1->value->SetFont(FF_ARIAL, FS_NORMAL, 15);
		// Finally add the plot
		$graph->Add($p1);

		// ... and stroke it		
        @unlink(__DIR__."/../image/baño.png");
		$graph->Stroke(__DIR__.'/../image/baño.png');
		return __DIR__.'/../image/baño.png';
	}
	
	function price($lat, $lng, $km, $TOOLS, $connection)
	{
		$prices = array(0,1000,2000,3000,4000,5000);
		for ($i=0; $i <= 5 ; $i++) { 
			if ($i<5) {
				$sql = $TOOLS->SQLRange($lat, $lng, $km, ['precio' => ['value' => [$prices[$i],$prices[$i+1]]]]);		
           		$name[] = $prices[$i]." - ".$prices[$i+1];
			}
			else {
				$sql = $TOOLS->SQLRange($lat, $lng, $km, ['precio' => ['symbol' => ' > ', 'value' => $prices[$i]]]);
            	$name[] = "> ".$prices[$i];
			}

			$ok = $connection->query($sql);
            $data[] = $connection->affected_rows;
		}

		$i=0;
		$y=0;
		foreach ($data as $value) {
			if($value>$i)
			{
				$i=$value;
				$y = $value+5;
			}
		}

		$graph = new Graph\Graph(800, 600, 'auto');
        $graph->setScale("textlin",0,$y);

        $graph->xaxis->SetFont(FF_ARIAL, FS_NORMAL, 12);
        $graph->yaxis->SetFont(FF_ARIAL, FS_NORMAL, 12);
        $graph->xaxis->setTickLabels($name);

        //$graph->yaxis->SetTickPositions([0,10,20,30,40,50,60,70,80,90,100,150,200,300,400,500]);
        $graph->yaxis->HideTicks(false,false);
        $p1 = new  Plot\BarPlot($data);

        $p1->setFillGradient('#C7FAC4', '#67FC95', GRAD_HOR);

        $p1->setwidth(40);

        $graph->Add($p1);
        @unlink(__DIR__."/../image/precios.png");
        $graph->Stroke(__DIR__."/../image/precios.png");

        return __DIR__."/../image/precios.png";
	}