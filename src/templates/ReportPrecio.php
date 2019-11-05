<?php

use Fpdf\Fpdf;

$file_name = (isset($file_name)) ? $file_name : NULL;
$price = (isset($price)) ? $price : 0;
$list_data = (isset($list_data)) ? $list_data : [];
//$filters = (isset($filters)) ? $filters : [];
$max_price = (isset($max_price)) ? $max_price : NULL;
$min_price = (isset($min_price)) ? $min_price : NULL;

$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

if ($rows <= 0)
    $prom = 0;
else
    $prom = $price / $rows;

define('EURO', chr(128));

$pdf->Cell(270, 8, 'PROMEDIO DE PRECIO DE LA ZONA POR PRECIO', 'B', 1, 'C');
$pdf->Cell(270, 8, 'Precio Maximo: ' . $max_price . ' || Precio Minimo: ' . $min_price, 0, 1, 'C');
$pdf->Cell(270, 8, '', 0, 1, 'C');

$pdf->Cell(135, 8, "Precio de la zona", "B", 0, 'C');
$pdf->Cell(135, 8, "Apartamentos en la zona", "B", 1, 'C');
$pdf->Cell(135, 8, round($prom, 2) . " " . EURO, 0, 0, 'C');
$pdf->Cell(135, 8, $rows, 0, 1, 'C');
$pdf->Cell(270, 8, '', 0, 1, 'C');

$pdf->Cell(85, 8, "Titulo", "B", 0, 'C');
$pdf->Cell(30, 8, "Precio", "B", 0, 'C');
$pdf->Cell(15, 8, "Habitaciones", "B", 0, 'C');
$pdf->Cell(15, 8, utf8_decode("m²"), "B", 0, 'C');
$pdf->Cell(15, 8, utf8_decode("Baños"), "B", 0, 'C');
$pdf->Cell(20, 8, "Amueblado", "B", 0, 'C');
$pdf->Cell(30, 8, utf8_decode("Latitud"), "B", 0, 'C');
$pdf->Cell(30, 8, utf8_decode("Longitud"), "B", 0, 'C');
$pdf->Cell(30, 8, utf8_decode("Distancia"), "B", 1, 'C');

foreach ($list_data as $v) {
    $pdf->Cell(85, 8, $v['titulo'], "B", 0, 'J');
    $pdf->Cell(30, 8, $v['precio'] . " " . EURO, "B", 0, 'C');
    $pdf->Cell(20, 8, $v['habitaciones'], "B", 0, 'C');
    $pdf->Cell(15, 8, $v['metrosCuadrados'] . utf8_decode("²"), "B", 0, 'C');
    $pdf->Cell(15, 8, $v['bano'], "B", 0, 'C');

    if ($v['amueblado'])
        $pdf->Cell(15, 8, "Si", "B", 0, 'C');
    else
        $pdf->Cell(15, 8, "No", "B", 0, 'C');
    
    $dist = $v['distance'] / 0.62137;

    $pdf->Cell(30, 8, $v['latitud'], "B", 0, 'C');
    $pdf->Cell(30, 8, $v['longitud'], "B", 0, 'C');
    $pdf->Cell(30, 8, round($dist, 4) . " Km", "B", 1, 'C');
}
    
$pdf->Output('F', $file_name);
