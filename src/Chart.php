<?php

/**
 * Class Calculadora
 * Author: leifer33@gmail.com
 * Collaborator: arturoluna879@gmail.com
 */

namespace leifermendez\rbs_accommodations;

use Exception;
use mysqli;
use Fpdf\Fpdf;
use Amenadiel\JpGraph\Graph;
use Amenadiel\JpGraph\Plot;
use Amenadiel\JpGraph\Themes\AquaTheme;

class Chart extends Settings
{
    public $connection;
    public $db_name;
    public $table_name;

    public function __construct()
    {
        try {
            $this->db_name = parent::$DB_NAME;
            $this->table_name = parent::$DB_TABLE;
            $this->connection = parent::Conexion();

            $sql = "SELECT * FROM " . parent::$DB_NAME . parent::$DB_TABLE;
            $result = $this->connection->query($sql);

            if (!$result) {
                $this->connection->query('USE ' . parent::$DB_NAME . ';');
                $this->connection->query($sql);
                $sql = file_get_contents(__DIR__ . '/../src/sql/table_db.sql');
                $result = $this->connection->query($sql);
            }

        } catch (\Exception $e) {
            return "Lo sentimos, este sitio web está experimentando problemas";
        }

    }

    function calculatorGrafica($opc, $lat, $lon, $km = 1, $array = null, $max = null, $min = null)
    {
        $habitaciones = array(1, 2, 3, 4, 5, 6, 7);
        $banos = array(1, 2, 3, 4, 5, 6, 7);
        $precio_graf = array(0, 1000, 2000, 3000, 4000, 5000);
        $amueblado = array('TRUE', 'FALSE');
        $distancia = $km * 0.62137;
        switch ($opc) {
            case 'global':

                $sql = "SELECT *, 3956 * 2 * ASIN(SQRT(
                POWER(SIN((" . $lat . " - abs(dest.latitud)) * pi()/180 / 2),
                2) + COS(" . $lat . " * pi()/180 ) * COS(abs(dest.latitud) *
                pi()/180) * POWER(SIN((" . $lon . " - dest.longitud) *
                pi()/180 / 2), 2) )) as distance
                FROM {$this->table_name} dest
                having distance < " . $distancia . " ORDER BY distance ASC;";

                $ok = $this->connection->query($sql);
                // echo $this->$conexion->error."<br><br>";
                $row = $this->connection->affected_rows;
                if ($row <= 0) {
                    echo "No existen apartamentos en las coordenadas indicadas";
                    die();
                } else {
                    $precio = 0;
                    $prom = 0.0;
                    while (($dato = $ok->fetch_assoc()) > 0) {
                        $precio += $dato['precio'];
                    }

                    //GRAFICAS
                    //sql separados para concatenar busquedas
                    $sql1 = "SELECT *, 3956 * 2 * ASIN(SQRT(
                POWER(SIN((" . $lat . " - abs(dest.latitud)) * pi()/180 / 2),
                2) + COS(" . $lat . " * pi()/180 ) * COS(abs(dest.latitud) *
                pi()/180) * POWER(SIN((" . $lon . " - dest.longitud) *
                pi()/180 / 2), 2) )) as distance
                FROM {$this->table_name} dest having distance < " . $distancia;

                    // Create the Pie Graph HABITACIONES.
                    $j = count($habitaciones);
                    $x = $j - 1;
                    $i = 0;
                    while ($i <= $x) {
                        if ($i < $x) {
                            $sql2 = $sql1 . " AND habitaciones >= " . $habitaciones[$i] . " AND habitaciones <= " . $habitaciones[$i + 1] . ";";
                            $ok = $this->connection->query($sql2);
                            $filas = $this->connection->affected_rows;
                            $name[] = "" . $habitaciones[$i] . "-" . $habitaciones[$i + 1] . "";
                            $habit[] = $filas;
                        } else {
                            $sql2 = $sql1 . " AND habitaciones >= " . $habitaciones[$i] . ";";
                            $ok = $this->connection->query($sql2);
                            $filas = $this->connection->affected_rows;
                            $name[] = '' . $habitaciones[$i] . '';
                            $habit[] = $filas;
                        }
                        $i += 2;
                    }

                    $graph_hab = new Graph\PieGraph(700, 500);
                    $theme_class = new AquaTheme;
                    $graph_hab->SetTheme($theme_class);
                    // Set A title for the plot
                    $graph_hab->title->Set("Habitaciones");
                    $graph_hab->title->SetFont(FF_ARIAL, FS_NORMAL, 20);
                    // Create
                    $p1 = new Plot\PiePlot3D($habit);
                    $p1->value->SetFont(FF_ARIAL, FS_NORMAL, 20);
                    $p1->SetLegends($name);
                    $graph_hab->Add($p1);
                    $p1->ShowBorder();
                    $p1->SetColor('black');
                    $p1->ExplodeSlice(1);
                    @unlink("habitaciones.png");
                    $graph_hab->Stroke('habitaciones.png');

                    // Create the Pie Graph BAÑOS.
                    $j = count($banos);
                    $x = $j - 1;
                    $i = 0;
                    while ($i <= $x) {
                        if ($i < $x) {
                            $sql2 = $sql1 . " AND bano >= " . $banos[$i] . " AND bano <= " . $banos[$i + 1] . ";";
                            $ok = $this->connection->query($sql2);
                            $filas = $this->connection->affected_rows;
                            $name[] = "" . $banos[$i] . "-" . $banos[$i + 1] . "";
                            $ban[] = $filas;
                        } else {
                            $sql2 = $sql1 . " AND bano >= " . $banos[$i] . ";";
                            $ok = $this->connection->query($sql2);
                            $filas = $this->connection->affected_rows;
                            $name[] = '' . $banos[$i] . '';
                            $ban[] = $filas;
                        }
                        $i += 2;
                    }

                    $graph_ba = new Graph\PieGraph(700, 500);
                    $theme_class = new AquaTheme;
                    $graph_ba->SetTheme($theme_class);
                    // Set A title for the plot
                    $graph_ba->title->Set("Baños");
                    $graph_ba->title->SetFont(FF_ARIAL, FS_NORMAL, 20);
                    // Create
                    $p2 = new Plot\PiePlot3D($ban);
                    $p2->value->SetFont(FF_ARIAL, FS_NORMAL, 20);
                    $p2->SetLegends($name);
                    $graph_ba->Add($p2);
                    $p2->ShowBorder();
                    $p2->SetColor('black');
                    $p2->ExplodeSlice(1);
                    @unlink("banos.png");
                    $graph_ba->Stroke('banos.png');

                    // Create the Pie Graph AMUEBLADO.
                    $sql3 = "SELECT *, 3956 * 2 * ASIN(SQRT(
                POWER(SIN((" . $lat . " - abs(dest.latitud)) * pi()/180 / 2),
                2) + COS(" . $lat . " * pi()/180 ) * COS(abs(dest.latitud) *
                pi()/180) * POWER(SIN((" . $lon . " - dest.longitud) *
                pi()/180 / 2), 2) )) as distance
                FROM {$this->table_name} dest WHERE amueblado=TRUE
                having distance < " . $distancia . ";";
                    $ok = $this->connection->query($sql3);
                    $filas = $this->connection->affected_rows;
                    $amue[] = $filas;


                    $sql4 = "SELECT *, 3956 * 2 * ASIN(SQRT(
                POWER(SIN((" . $lat . " - abs(dest.latitud)) * pi()/180 / 2),
                2) + COS(" . $lat . " * pi()/180 ) * COS(abs(dest.latitud) *
                pi()/180) * POWER(SIN((" . $lon . " - dest.longitud) *
                pi()/180 / 2), 2) )) as distance
                FROM {$this->table_name} dest WHERE amueblado=FALSE
                having distance < " . $distancia . ";";
                    $ok = $this->connection->query($sql4);
                    $filas = $this->connection->affected_rows;
                    $amue[] = $filas;

                    $graph_amu = new Graph\PieGraph(700, 500);
                    $theme_class = new AquaTheme;
                    $graph_amu->SetTheme($theme_class);
                    // Set A title for the plot
                    $graph_amu->title->Set("Apartamentos Amueblados");
                    $graph_amu->title->SetFont(FF_ARIAL, FS_NORMAL, 20);
                    // Create
                    $p3 = new Plot\PiePlot3D($amue);
                    $p3->value->SetFont(FF_ARIAL, FS_NORMAL, 20);
                    $p3->SetLegends(array('Amueblado', 'No Amueblado'));
                    $graph_amu->Add($p3);
                    $p3->ShowBorder();
                    $p3->SetColor('black');
                    $p3->ExplodeSlice(1);
                    @unlink("amueblado.png");
                    $graph_amu->Stroke('amueblado.png');

                    //create graph PRECIOS
                    $j = count($precio_graf);
                    $x = $j - 1;
                    $i = 0;
                    while ($i <= $x) {
                        if ($i < $x) {
                            $sql2 = $sql1 . " AND precio >= " . $precio_graf[$i] . " AND precio <= " . $precio_graf[$i + 1] . ";";
                            $ok = $this->connection->query($sql2);
                            $filas = $this->connection->affected_rows;
                            $name_pre[] = "" . $precio_graf[$i] . "-" . $precio_graf[$i + 1] . "";
                            $fil[] = $filas;
                        } else {
                            $sql2 = $sql1 . " AND precio >= " . $precio_graf[$i] . ";";
                            $ok = $this->connection->query($sql2);
                            $filas = $this->connection->affected_rows;
                            $name_pre[] = '' . $precio_graf[$i] . '';
                            $fil[] = $filas;
                        }
                        $i++;
                    }

                    $graph_pre = new Graph\Graph(500, 600, 'auto');
                    $graph_pre->setScale("textint");
                    $graph_pre->title->Set("Precio por apartamentos");
                    $graph_pre->title->SetFont(FF_ARIAL, FS_NORMAL, 20);
                    $graph_pre->xaxis->title->set("Precio (€)");
                    $graph_pre->xaxis->setTickLabels($name_pre);
                    $graph_pre->SetMargin(100, auto, auto, auto); //izq, der, sup, inf
                    $graph_pre->yaxis->title->set("Apartamentos");
                    $graph_pre->yaxis->title->SetMargin(20);
                    $p4 = new  Plot\BarPlot($fil);

                    $p4->setFillGradient('#C7FAC4', '#67FC95', GRAD_HOR);

                    $p4->setwidth(30);

                    $graph_pre->Add($p4);
                    @unlink("precios.png");
                    $graph_pre->Stroke('precios.png');

                    //ARCHIVO PDF
                    define('EURO', chr(128));
                    $pdf = new FPDF('L', 'mm', 'A4');

                    $pdf->AddPage();
                    $pdf->SetFont('Arial', '', 10);

                    if ($row <= 0)
                        $prom = 0;
                    else
                        $prom = $precio / $row;


                    $pdf->Cell(270, 8, 'PROMEDIO DE PRECIO DE LA ZONA', 0, 1, 'C');

                    $pdf->Cell(135, 8, "Precio de la zona", "B", 0, 'C');
                    $pdf->Cell(135, 8, "Apartamentos en la zona", "B", 1, 'C');
                    $pdf->Cell(135, 8, round($prom, 2) . " " . EURO, 0, 0, 'C');
                    $pdf->Cell(135, 8, $row, 0, 1, 'C');
                    $pdf->Cell(270, 8, '', 0, 1, 'C');

                    //graficas
                    $pdf->Cell(135, 8, $pdf->image("habitaciones.png", 10, 50, 130, 130), 0, 0, 'C');
                    $pdf->Cell(270, 8, $pdf->image("banos.png", 150, 50, 130, 130), 0, 1, 'C');

                    //Salto de pagina
                    $pdf->AddPage();
                    $pdf->Cell(135, 8, $pdf->image("amueblado.png", 20, 30, 130, 130), 0, 0, 'C');
                    $pdf->Cell(270, 8, $pdf->image("precios.png", 135, 20, 145, 150), 0, 1, 'C');

                    //Salto de pagina
                    $pdf->AddPage();

                    $pdf->Cell(85, 8, "Titulo", "B", 0, 'C');
                    $pdf->Cell(30, 8, "Precio", "B", 0, 'C');
                    $pdf->Cell(15, 8, "Habitaciones", "B", 0, 'C');
                    $pdf->Cell(15, 8, utf8_decode("m²"), "B", 0, 'C');
                    $pdf->Cell(15, 8, utf8_decode("Baños"), "B", 0, 'C');
                    $pdf->Cell(20, 8, "Amueblado", "B", 0, 'C');
                    $pdf->Cell(30, 8, utf8_decode("Latitud"), "B", 0, 'C');
                    $pdf->Cell(30, 8, utf8_decode("Longitud"), "B", 0, 'C');
                    $pdf->Cell(30, 8, utf8_decode("Distancia"), "B", 1, 'C');

                    $ok = $this->connection->query($sql);
                    while (($d = $ok->fetch_assoc()) > 0) {
                        $pdf->Cell(85, 8, $d['titulo'], "B", 0, 'J');
                        $pdf->Cell(30, 8, $d['precio'] . " " . EURO, "B", 0, 'C');
                        $pdf->Cell(20, 8, $d['habitaciones'], "B", 0, 'C');
                        $pdf->Cell(15, 8, $d['metrosCuadrados'] . utf8_decode("²"), "B", 0, 'C');
                        $pdf->Cell(15, 8, $d['bano'], "B", 0, 'C');

                        if ($d['amueblado'] == TRUE)
                            $pdf->Cell(15, 8, "Si", "B", 0, 'C');
                        else
                            $pdf->Cell(15, 8, "No", "B", 0, 'C');

                        $dist = $d['distance'] / 0.62137;

                        $pdf->Cell(30, 8, $d['latitud'], "B", 0, 'C');
                        $pdf->Cell(30, 8, $d['longitud'], "B", 0, 'C');
                        $pdf->Cell(30, 8, round($dist, 4) . " Km", "B", 1, 'C');
                    }

                    $pdf->Output();
                    $contenido = array();
                    $ok = $this->connection->query($sql);
                    $k = 0;
                    $j = 0;
                    while (($var = $ok->fetch_assoc()) > 0) {
                        foreach ($var as $key => $value) {
                            $contenido [$k][$j] = $key . " => " . $value;
                            $j++;
                        }
                        $k++;
                    }

                    return $contenido;
                }
                break;

            case 'filters':

                if (count($array) <= 0) {
                    echo "Filtro invalido!";
                } else {
                    $row = sizeof($array);
                    $where = '';
                    $i = 0;
                    foreach ($array as $key => $value) {
                        if ($i < $row - 1)
                            $where .= $key . "=" . $value . " AND ";
                        else
                            $where .= $key . "=" . $value;
                        $i++;
                    }

                    $sql = "SELECT *, 3956 * 2 * ASIN(SQRT(
                            POWER(SIN((" . $lat . " - abs(dest.latitud)) * pi()/180 / 2),
                            2) + COS(" . $lat . " * pi()/180 ) * COS(abs(dest.latitud) *
                            pi()/180) * POWER(SIN((" . $lon . " - dest.longitud) *
                            pi()/180 / 2), 2) )) as distance
                            FROM {$this->table_name} dest WHERE " . $where . "            
                            having distance < " . $distancia . " ORDER BY distance ASC;";
                    $ok = $this->connection->query($sql);

                    $rows = $this->connection->affected_rows;

                    //verificacion de que exista en la base de datos la consulta
                    if ($rows <= 0) {
                        echo "No existen apartamentos en esta zona con el filtro añadido";
                    } else {

                        $precio = 0;
                        $prom = 0.0;
                        while (($dato = $ok->fetch_assoc()) > 0) {
                            $precio += $dato['precio'];
                        }

                        //GRAFICAS
                        //sql separados para concatenar busquedas
                        $sql1 = "SELECT *, 3956 * 2 * ASIN(SQRT(
                            POWER(SIN((" . $lat . " - abs(dest.latitud)) * pi()/180 / 2),
                            2) + COS(" . $lat . " * pi()/180 ) * COS(abs(dest.latitud) *
                            pi()/180) * POWER(SIN((" . $lon . " - dest.longitud) *
                            pi()/180 / 2), 2) )) as distance
                            FROM {$this->table_name} dest WHERE " . $where . "            
                            having distance < " . $distancia;
                        //create graph PRECIOS
                        $j = count($precio_graf);
                        $x = $j - 1;
                        $i = 0;
                        while ($i <= $x) {
                            if ($i < $x) {
                                $sql2 = $sql1 . " AND precio >= " . $precio_graf[$i] . " AND precio <= " . $precio_graf[$i + 1] . ";";
                                $ok = $this->connection->query($sql2);
                                $filas = $this->connection->affected_rows;
                                $name_pre[] = "" . $precio_graf[$i] . "-" . $precio_graf[$i + 1] . "";
                                $fil[] = $filas;
                            } else {
                                $sql2 = $sql1 . " AND precio >= " . $precio_graf[$i] . ";";
                                $ok = $this->connection->query($sql2);
                                $filas = $this->connection->affected_rows;
                                $name_pre[] = '' . $precio_graf[$i] . '';
                                $fil[] = $filas;
                            }
                            $i++;
                        }

                        $graph_pre = new Graph\Graph(600, 700, 'auto');
                        $graph_pre->setScale("textint");
                        $graph_pre->title->Set("Precio por apartamentos");
                        $graph_pre->title->SetFont(FF_ARIAL, FS_NORMAL, 20);
                        $graph_pre->xaxis->title->set("Precio (€)");
                        $graph_pre->xaxis->setTickLabels($name_pre);
                        $graph_pre->SetMargin(100, auto, auto, auto); //izq, der, sup, inf
                        $graph_pre->yaxis->title->set("Apartamentos");
                        $graph_pre->yaxis->title->SetMargin(20);
                        $p4 = new  Plot\BarPlot($fil);

                        $p4->setFillGradient('#C7FAC4', '#67FC95', GRAD_HOR);

                        $p4->setwidth(30);

                        $graph_pre->Add($p4);
                        @unlink("precio_filtro.png");
                        $graph_pre->Stroke('precio_filtro.png');

                        //ARCHIVO PDF
                        $pdf = new FPDF('L', 'mm', 'A4');

                        $pdf->AddPage();
                        $pdf->SetFont('Arial', '', 10);

                        //calcula el promedio del precio de la zona
                        if ($rows <= 0)
                            $prom = 0;
                        else
                            $prom = $precio / $rows;

                        //crea un string para mostrar en el pdf cuales fueron los filtros asignados
                        $pdfarray = '';
                        foreach ($array as $key => $value) {
                            $pdfarray .= $key . " = " . $value . " | ";
                        }

                        define('EURO', chr(128));

                        $pdf->Cell(270, 8, 'PROMEDIO DE PRECIO DE LA ZONA FILTRADO', "B", 1, 'C');
                        $pdf->Cell(270, 8, '( ' . $pdfarray . ' )', 0, 1, 'C');
                        $pdf->Cell(270, 8, '', 0, 1, 'C');

                        $pdf->Cell(135, 8, "Precio de la zona", "B", 0, 'C');
                        $pdf->Cell(135, 8, "Apartamentos en la zona", "B", 1, 'C');
                        $pdf->Cell(135, 8, round($prom, 2) . " " . EURO, 0, 0, 'C');
                        $pdf->Cell(135, 8, $rows, 0, 1, 'C');
                        $pdf->Cell(270, 8, '', 0, 1, 'C');


                        $pdf->Cell(270, 8, $pdf->image("precio_filtro.png", 70, 60, 135, 140), 0, 1, 'C');
                        $pdf->AddPage();
                        $pdf->Cell(85, 8, "Titulo", "B", 0, 'C');
                        $pdf->Cell(30, 8, "Precio", "B", 0, 'C');
                        $pdf->Cell(15, 8, "Habitaciones", "B", 0, 'C');
                        $pdf->Cell(15, 8, utf8_decode("m²"), "B", 0, 'C');
                        $pdf->Cell(15, 8, utf8_decode("Baños"), "B", 0, 'C');
                        $pdf->Cell(20, 8, "Amueblado", "B", 0, 'C');
                        $pdf->Cell(30, 8, utf8_decode("Latitud"), "B", 0, 'C');
                        $pdf->Cell(30, 8, utf8_decode("Longitud"), "B", 0, 'C');
                        $pdf->Cell(30, 8, utf8_decode("Distancia"), "B", 1, 'C');

                        $ok = $this->connection->query($sql);
                        while (($d = $ok->fetch_assoc()) > 0) {
                            $pdf->Cell(85, 8, $d['titulo'], "B", 0, 'J');
                            $pdf->Cell(30, 8, $d['precio'] . " " . EURO, "B", 0, 'C');
                            $pdf->Cell(20, 8, $d['habitaciones'], "B", 0, 'C');
                            $pdf->Cell(15, 8, $d['metrosCuadrados'] . utf8_decode("²"), "B", 0, 'C');
                            $pdf->Cell(15, 8, $d['bano'], "B", 0, 'C');

                            if ($d['amueblado'] == TRUE)
                                $pdf->Cell(15, 8, "Si", "B", 0, 'C');
                            else
                                $pdf->Cell(15, 8, "No", "B", 0, 'C');

                            $dist = $d['distance'] / 0.62137;

                            $pdf->Cell(30, 8, $d['latitud'], "B", 0, 'C');
                            $pdf->Cell(30, 8, $d['longitud'], "B", 0, 'C');
                            $pdf->Cell(30, 8, round($dist, 4) . " Km", "B", 1, 'C');
                        }
                        $pdf->Output();

                        $contenido = array();
                        $ok = $this->connection->query($sql);
                        $k = 0;
                        $j = 0;
                        while (($var = $ok->fetch_assoc()) > 0) {
                            foreach ($var as $key => $value) {
                                $contenido [$k][$j] = $key . " => " . $value;
                                $j++;
                            }
                            $k++;
                        }
                        return $contenido;
                    }
                }
                break;

            case 'precio':

                if (!$min && !$max) {
                    echo "No se ha especificado el precio minimo y precio maximo";
                } else {

                    $sql = "SELECT *, 3956 * 2 * ASIN(SQRT(
                    POWER(SIN((" . $lat . " - abs(dest.latitud)) * pi()/180 / 2),
                    2) + COS(" . $lat . " * pi()/180 ) * COS(abs(dest.latitud) *
                    pi()/180) * POWER(SIN((" . $lon . " - dest.longitud) *
                    pi()/180 / 2), 2) )) as distance
                    FROM apartaments dest
                    having distance < " . $distancia . " AND precio >" . $min . " AND precio <" . $max . " ORDER BY distance ASC;";
                    $ok = $this->connection->query($sql);
                    // echo $this->$conexion->error."<br><br>";
                    $row = $this->connection->affected_rows;
                    if ($row <= 0) {
                        echo "No existen apartamentos en las coordenadas indicadas Con el precio especificado";
                        die();
                    } else {
                        $precio = 0;
                        $prom = 0.0;
                        while (($dato = $ok->fetch_assoc()) > 0) {
                            $precio += $dato['precio'];
                        }

                        //GRAFICAS
                        //sql separados para concatenar busquedas
                        $sql1 = "SELECT *, 3956 * 2 * ASIN(SQRT(
                    POWER(SIN((" . $lat . " - abs(dest.latitud)) * pi()/180 / 2),
                    2) + COS(" . $lat . " * pi()/180 ) * COS(abs(dest.latitud) *
                    pi()/180) * POWER(SIN((" . $lon . " - dest.longitud) *
                    pi()/180 / 2), 2) )) as distance FROM apartaments dest
                    having distance < " . $distancia . " AND precio >" . $min . " AND precio <" . $max;

                        // Create the Pie Graph HABITACIONES.
                        $j = count($habitaciones);
                        $x = $j - 1;
                        $i = 0;
                        while ($i <= $x) {
                            if ($i < $x) {
                                $sql2 = $sql1 . " AND habitaciones >= " . $habitaciones[$i] . " AND habitaciones <= " . $habitaciones[$i + 1] . ";";
                                $ok = $this->connection->query($sql2);
                                $filas = $this->connection->affected_rows;
                                $name[] = "" . $habitaciones[$i] . "-" . $habitaciones[$i + 1] . "";
                                $habit[] = $filas;
                            } else {
                                $sql2 = $sql1 . " AND habitaciones >= " . $habitaciones[$i] . ";";
                                $ok = $this->connection->query($sql2);
                                $filas = $this->connection->affected_rows;
                                $name[] = '' . $habitaciones[$i] . '';
                                $habit[] = $filas;
                            }
                            $i += 2;
                        }

                        $graph_hab = new Graph\PieGraph(700, 500);
                        $theme_class = new AquaTheme;
                        $graph_hab->SetTheme($theme_class);
                        // Set A title for the plot
                        $graph_hab->title->Set("Habitaciones");
                        $graph_hab->title->SetFont(FF_ARIAL, FS_NORMAL, 20);
                        // Create
                        $p1 = new Plot\PiePlot3D($habit);
                        $p1->value->SetFont(FF_ARIAL, FS_NORMAL, 20);
                        $p1->SetLegends($name);
                        $graph_hab->Add($p1);
                        $p1->ShowBorder();
                        $p1->SetColor('black');
                        $p1->ExplodeSlice(1);
                        @unlink("habitaciones_pre.png");
                        $graph_hab->Stroke('habitaciones_pre.png');

                        // Create the Pie Graph BAÑOS.
                        $j = count($banos);
                        $x = $j - 1;
                        $i = 0;
                        while ($i <= $x) {
                            if ($i < $x) {
                                $sql2 = $sql1 . " AND bano >= " . $banos[$i] . " AND bano <= " . $banos[$i + 1] . ";";
                                $ok = $this->connection->query($sql2);
                                $filas = $this->connection->affected_rows;
                                $name[] = "" . $banos[$i] . "-" . $banos[$i + 1] . "";
                                $ban[] = $filas;
                            } else {
                                $sql2 = $sql1 . " AND bano >= " . $banos[$i] . ";";
                                $ok = $this->connection->query($sql2);
                                $filas = $this->connection->affected_rows;
                                $name[] = '' . $banos[$i] . '';
                                $ban[] = $filas;
                            }
                            $i += 2;
                        }

                        $graph_ba = new Graph\PieGraph(700, 500);
                        $theme_class = new AquaTheme;
                        $graph_ba->SetTheme($theme_class);
                        // Set A title for the plot
                        $graph_ba->title->Set("Baños");
                        $graph_ba->title->SetFont(FF_ARIAL, FS_NORMAL, 20);
                        // Create
                        $p2 = new Plot\PiePlot3D($ban);
                        $p2->value->SetFont(FF_ARIAL, FS_NORMAL, 20);
                        $p2->SetLegends($name);
                        $graph_ba->Add($p2);
                        $p2->ShowBorder();
                        $p2->SetColor('black');
                        $p2->ExplodeSlice(1);
                        @unlink("banos_pre.png");
                        $graph_ba->Stroke('banos_pre.png');

                        // Create the Pie Graph AMUEBLADO.
                        $sql3 = "SELECT *, 3956 * 2 * ASIN(SQRT(
                    POWER(SIN((" . $lat . " - abs(dest.latitud)) * pi()/180 / 2),
                    2) + COS(" . $lat . " * pi()/180 ) * COS(abs(dest.latitud) *
                    pi()/180) * POWER(SIN((" . $lon . " - dest.longitud) *
                    pi()/180 / 2), 2) )) as distance FROM apartaments dest WHERE amueblado=TRUE
                    having distance < " . $distancia . " AND precio >" . $min . " AND precio <" . $max . ";";

                        $ok = $this->connection->query($sql3);
                        $filas = $this->connection->affected_rows;
                        $amue[] = $filas;


                        $sql4 = "SELECT *, 3956 * 2 * ASIN(SQRT(
                    POWER(SIN((" . $lat . " - abs(dest.latitud)) * pi()/180 / 2),
                    2) + COS(" . $lat . " * pi()/180 ) * COS(abs(dest.latitud) *
                    pi()/180) * POWER(SIN((" . $lon . " - dest.longitud) *
                    pi()/180 / 2), 2) )) as distance FROM apartaments dest WHERE amueblado=FALSE
                    having distance < " . $distancia . " AND precio >" . $min . " AND precio <" . $max . ";";
                        $ok = $this->connection->query($sql4);
                        $filas = $this->connection->affected_rows;
                        $amue[] = $filas;

                        $graph_amu = new Graph\PieGraph(700, 500);
                        $theme_class = new AquaTheme;
                        $graph_amu->SetTheme($theme_class);
                        // Set A title for the plot
                        $graph_amu->title->Set("Apartamentos Amueblados");
                        $graph_amu->title->SetFont(FF_ARIAL, FS_NORMAL, 20);
                        // Create
                        $p3 = new Plot\PiePlot3D($amue);
                        $p3->value->SetFont(FF_ARIAL, FS_NORMAL, 20);
                        $p3->SetLegends(array('Amueblado', 'No Amueblado'));
                        $graph_amu->Add($p3);
                        $p3->ShowBorder();
                        $p3->SetColor('black');
                        $p3->ExplodeSlice(1);
                        @unlink("amueblado_pre.png");
                        $graph_amu->Stroke('amueblado_pre.png');

                        //ARCHIVO PDF
                        define('EURO', chr(128));
                        $pdf = new FPDF('L', 'mm', 'A4');

                        $pdf->AddPage();
                        $pdf->SetFont('Arial', '', 10);

                        if ($row <= 0)
                            $prom = 0;
                        else
                            $prom = $precio / $row;


                        $pdf->Cell(270, 8, 'PROMEDIO DE PRECIO DE LA ZONA POR PRECIO', 'B', 1, 'C');
                        $pdf->Cell(270, 8, 'Precio Maximo: ' . $max . ' || Precio Minimo: ' . $min, 0, 1, 'C');
                        $pdf->Cell(270, 8, '', 0, 1, 'C');

                        $pdf->Cell(135, 8, "Precio de la zona", "B", 0, 'C');
                        $pdf->Cell(135, 8, "Apartamentos en la zona", "B", 1, 'C');
                        $pdf->Cell(135, 8, round($prom, 2) . " " . EURO, 0, 0, 'C');
                        $pdf->Cell(135, 8, $row, 0, 1, 'C');
                        $pdf->Cell(270, 8, '', 0, 1, 'C');

                        //graficas
                        $pdf->Cell(135, 8, $pdf->image("habitaciones_pre.png", 10, 70, 130, 130), 0, 0, 'C');
                        $pdf->Cell(270, 8, $pdf->image("banos_pre.png", 150, 70, 130, 130), 0, 1, 'C');

                        //Salto de pagina
                        $pdf->AddPage();
                        $pdf->Cell(135, 8, $pdf->image("amueblado_pre.png", 80, 30, 130, 130), 0, 0, 'C');
                        $pdf->AddPage();
                        $pdf->Cell(85, 8, "Titulo", "B", 0, 'C');
                        $pdf->Cell(30, 8, "Precio", "B", 0, 'C');
                        $pdf->Cell(15, 8, "Habitaciones", "B", 0, 'C');
                        $pdf->Cell(15, 8, utf8_decode("m²"), "B", 0, 'C');
                        $pdf->Cell(15, 8, utf8_decode("Baños"), "B", 0, 'C');
                        $pdf->Cell(20, 8, "Amueblado", "B", 0, 'C');
                        $pdf->Cell(30, 8, utf8_decode("Latitud"), "B", 0, 'C');
                        $pdf->Cell(30, 8, utf8_decode("Longitud"), "B", 0, 'C');
                        $pdf->Cell(30, 8, utf8_decode("Distancia"), "B", 1, 'C');

                        $ok = $this->connection->query($sql);
                        while (($d = $ok->fetch_assoc()) > 0) {
                            $pdf->Cell(85, 8, $d['titulo'], "B", 0, 'J');
                            $pdf->Cell(30, 8, $d['precio'] . " " . EURO, "B", 0, 'C');
                            $pdf->Cell(20, 8, $d['habitaciones'], "B", 0, 'C');
                            $pdf->Cell(15, 8, $d['metrosCuadrados'] . utf8_decode("²"), "B", 0, 'C');
                            $pdf->Cell(15, 8, $d['bano'], "B", 0, 'C');

                            if ($d['amueblado'] == TRUE)
                                $pdf->Cell(15, 8, "Si", "B", 0, 'C');
                            else
                                $pdf->Cell(15, 8, "No", "B", 0, 'C');

                            $dist = $d['distance'] / 0.62137;

                            $pdf->Cell(30, 8, $d['latitud'], "B", 0, 'C');
                            $pdf->Cell(30, 8, $d['longitud'], "B", 0, 'C');
                            $pdf->Cell(30, 8, round($dist, 4) . " Km", "B", 1, 'C');
                        }

                        $pdf->Output();
                        $contenido = array();
                        $ok = $this->connection->query($sql);
                        $k = 0;
                        $j = 0;
                        while (($var = $ok->fetch_assoc()) > 0) {
                            foreach ($var as $key => $value) {
                                $contenido [$k][$j] = $key . " => " . $value;
                                $j++;
                            }
                            $k++;
                        }

                        return $contenido;
                    }
                }
                break;

            default:
                echo "Opción invalida!!";
                break;
        }
    }

}