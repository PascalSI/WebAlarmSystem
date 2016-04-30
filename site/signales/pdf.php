<?php
include("../../include/scriptdb/config.php");
include("../../include/scriptdb/querys.php");
include("../../include/phpscript/generales.php");
include("../../include/phpscript/sessionPopupCliente.php");
include('../../include/plugins/tcpdf/tcpdf_import.php');
include('../../include/plugins/tcpdf/mypdf.php');

$idx = decode(5,$_GET['id']);

$fecha = explode("/",$_GET["fech"]);

//query obtener
$query= new Querys();
$conex = $query->getConection($CONFIG);
$MyrecordDatos = $query->SelDB($conex,"site_sel_DatosCliente",array($idx));
$rAuxC = $query->getdata_object($MyrecordDatos);


$logo = '../../../img/logo_empresas/'.getLogoEmpresaCliente($query,$conex,$idx,$CONFIG);


$NameFile = $idx.'-'.$rAuxC->nombre_cliente ;

$RecbFecha = $_GET["fech"];

$fecha = explode("/",$_GET["fech"]);

if($_GET["cat"]!="-1"){
	$paramAux.="AND (idGrupo = '".$_GET["cat"]."')";
	$NameFile.="-".$_GET["catText"];
}

if($_GET["d1"]!="" && $_GET["d2"]!=""){
	$paramAux.="AND (fecha >= CONVERT(datetime,'".$_GET["d1"]."', 103)) AND (fecha <= DATEADD(dd, 1, CONVERT(datetime,'".$_GET["d2"]."', 103)))";
}

//obtiene el total de los registros
if($RecbFecha=="1" && $_GET["tipo"]==1){

	$returnFecha ="Últimas 100 Señales";
	$NameFile.="-Ultimas_100_Signales";

	//var result
	$query_result="site_sel_UltimaSenalesClienteParam";
	$paramResult = array($idx,"1=1");

}else{
	if($_GET["tipo"]==1){
		$returnFecha = "Dia".getFullDate($RecbFecha);

		if($RecbFecha=="1"){
			$NameFile.="-Ultimas_100_Signales";
		}else{
			$NameFile.="-Signales-".$_GET["fech"];
		}

		//var result
		$query_result="site_sel_BuscarPorFechaParam";
		$paramResult = array($idx,$fecha[0],$fecha[1],$fecha[2],"1=1");

	}else{
		$returnFecha = "Señales  desde: ".$_GET["d1"]." hasta: ".$_GET["d2"];

		if($_GET["d1"]=="" && $_GET["d2"]==""){
			if($RecbFecha=="1"){
				$NameFile.="-Ultimas_100_Signales";
			}else{
				$NameFile.="-Signales-".$_GET["fech"];
			}
		}

		if($_GET["d1"]!=""){
			$NameFile.="-Signales-".$_GET["d1"];
		}

		if($_GET["d2"]!=""){
			$NameFile.="_Al_".$_GET["d2"];
		}

		//var result
		$query_result="site_sel_BuscarPorFechaRangeParam";
		$paramResult = array($idx,$paramAux,"1=1");
	}
}

$MyrecordData = $query->SelDB($conex,$query_result,$paramResult);

$vFilas =  array();
$i=1;
while($r=$query->getdata_object($MyrecordData)){
	unset($rAux);
	$rAux = array();

	$auxEvento =DecoEventoWS($r->Variante,$r->descript,$r->protocolo);
	$textEvento = $r->evento." ".$auxEvento;

	$rAux[0] = $i;
	$rAux[1] = $textEvento;
	$rAux[2] = $r->UserZona;
	$rAux[3] = date_format($r->fecha,"d-m-Y h:i:s a");

	$i++;

	$vFilas[] = $rAux;
}

$titleName='Reporte '.$returnFecha;

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor("Reporte ".$_SESSION["cliente"]["nombre_empresa"]);
$pdf->SetTitle($titleName);

// set default header data
$pdf->SetHeaderData($logo,30,"Reporte ".$_SESSION["cliente"]["nombre_empresa"],$titleName);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


// set font
$pdf->SetFont('helvetica', '', 10);

// add a page
$pdf->AddPage();


$html = '<table width="100%" border="0">
		<tr>
			<td align="left">Cliente: # '.$idx.' - '.$rAuxC->nombre_cliente.'</td>
		</tr>
		<tr>
			<td align="left">Direccion: '.$rAuxC->direccion.'</td>
		</tr>
	</table><br/>';

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// column titles
$header = array('N°', 'Evento', 'Usuario/Zona' ,'Fecha');


// Colors, line width and bold font
$pdf->SetFillColor(0, 0, 255);
$pdf->SetTextColor(255);
$pdf->SetDrawColor(0,0,0);
$pdf->SetLineWidth(0.3);
$pdf->SetFont('', 'B');
// Header
$w = array(15,55,85,45);
$num_headers = count($header);
for($i = 0; $i < $num_headers; ++$i) {
	$pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
}
$pdf->Ln();
// Color and font restoration
$pdf->SetFillColor(224, 235, 255);
$pdf->SetTextColor(0);
$pdf->SetFont('');
// Data
$fill = 0;
foreach($vFilas as $row) {
	$pdf->Cell($w[0], 6, $row[0], 'LR', 0, 'C', $fill,"",1);
	$pdf->Cell($w[1], 6, $row[1], 'LR', 0, 'L', $fill,"",1);
	$pdf->Cell($w[2], 6, $row[2], 'LR', 0, 'L', $fill,"",1);
	$pdf->Cell($w[3], 6, $row[3], 'LR', 0, 'L', $fill,"",1);
	$pdf->Ln();
	$fill=!$fill;
}
$pdf->Cell(array_sum($w), 0, '', 'T');

// close and output PDF document
$pdf->Output(NameDowlon($NameFile.'.pdf'), 'D');


?>