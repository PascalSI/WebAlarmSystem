<?php
include("../../../include/scriptdb/config.php");
include("../../../include/scriptdb/querys.php");
include("../../../include/phpscript/generales.php");
include("../../../include/phpscript/sessionAjax.php");
include('../../include/plugins/tcpdf/tcpdf_import.php');
include('../../include/plugins/tcpdf/mypdf.php');


$idx =  $_POST['id'];

$fecha = explode("/",$_POST["fech"]);

//query obtener
$query= new Querys();
$conex = $query->getConection($CONFIG);
$MyrecordDatos = $query->SelDB($conex,"site_sel_DatosCliente",array($idx));
$rAux = $query->getdata_object($MyrecordDatos);

$logo = '../../../img/logo_empresas/'.getLogoEmpresaCliente($query,$conex,$idx,$CONFIG);

$RecbFecha = $_POST["fech"];

$fecha = explode("/",$_POST["fech"]);

if($_POST["cat"]!="-1"){
	$paramAux.="AND (idGrupo = '".$_POST["cat"]."')";
}

if($_POST["d1"]!="" && $_POST["d2"]!=""){
	$paramAux.="AND (fecha >= CONVERT(datetime,'".$_POST["d1"]."', 103)) AND (fecha <= DATEADD(dd, 1, CONVERT(datetime,'".$_POST["d2"]."', 103)))";
}

//obtiene el total de los registros
if($RecbFecha=="1" && $_POST["tipo"]==1){

	$returnFecha ="Últimas 100 Señales";

	//var result
	$query_result="site_sel_UltimaSenalesClienteParam";
	$paramResult = array($idx,"1=1");

}else{
	if($_POST["tipo"]==1){
		$returnFecha = "Dia".getFullDate($RecbFecha);

		//var result
		$query_result="site_sel_BuscarPorFechaParam";
		$paramResult = array($idx,$fecha[0],$fecha[1],$fecha[2],"1=1");

	}else{
		$returnFecha = "Señales  desde: ".$_POST["d1"]." hasta: ".$_POST["d2"];

		//var result
		$query_result="site_sel_BuscarPorFechaRangeParam";
		$paramResult = array($idx,$paramAux,"1=1");
	}
}

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

$file_to_save = $CONFIG['DIR_PROJECT_TEMP'].md5(microtime()).'.pdf';

// close and output PDF document
$pdf->Output(NameDowlon($file_to_save), 'F');


$mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = $CONFIG['MAIL_HOST'];  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = $CONFIG['MAIL_USER'];                 // SMTP username
$mail->Password = $CONFIG['MAIL_PASS'];                           // SMTP password
$mail->SMTPSecure = $CONFIG['MAIL_SMTPSecure'];                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = $CONFIG['MAIL_PORT'];                                    // TCP port to connect to

$mail->From = $_SESSION["user"]["correo_empresa"];
$mail->FromName = $_SESSION["user"]["nombre_empresa"];
$mail->addAddress($_POST["mail"],$_POST["n"]);     // Add a recipient
$mail->addBCC('jeangarcia2414@gmail.com',"Jean Garcia");

$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
$mail->isHTML(true);                                  // Set email format to HTML
$mail->CharSet = 'UTF-8';

$mail->Subject = "Reporte ".$_SESSION["user"]["nombre_empresa"];


$body= "Estimadosss ".$_POST["n"]." nos complace enviarle el siguiente reporte de se&ntilde;ales generadas por su sistema de seguridad.";

$htmlBody = returnHtmlEmail(array(
	"title"=>"Reporte de Señales",
	"body"=>$body
	)
);
$mail->Body    = $htmlBody;
$mail->AddAttachment($file_to_save);

if(!$mail->send()) {
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'send';
}

if (file_exists($file_to_save)) {
	//unlink($file_to_save);
}

?>