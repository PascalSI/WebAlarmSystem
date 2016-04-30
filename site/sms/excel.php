<?php
include("../../include/scriptdb/config.php");
include("../../include/scriptdb/querys.php");
include("../../include/phpscript/generales.php");
include("../../include/phpscript/sessionPopupCliente.php");


$idx = decode(5,$_GET['id']);

session_start();

$fecha = explode("/",$_GET["fech"]);

//query obtener
$query= new Querys();
$conex = $query->getConection($CONFIG);
$MyrecordDatos = $query->SelDB($conex,"site_sel_DatosCliente",array($idx));
$rAux = $query->getdata_object($MyrecordDatos);

$logo = $CONFIG['HOST'].'img/logo_empresas/'.getLogoEmpresaCliente($query,$conex,$idx,$CONFIG);

$NameFile = $idx.'-'.$rAux->nombre_cliente."-SMS-".$_GET["fech"];

$fecha = explode("/",$_GET["fech"]);


$MyrecordData = $query->SelDB($conex,"site_sel_BuscarPorSMSExport",array($idx,$fecha[0],$fecha[1],$fecha[2]));


header('Content-Type: text/html; charset=utf-8');
header("Content-Type: application/vnd.ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=".NameDowlon($NameFile).".xls");
?>
<meta	http-equiv="Content-Type"	content="charset=utf-8" />
<table width="100%" border="0">
	<tr>
		<td width="200px">
			<img src="<?php echo $logo;?>" style="width:10px; height:50px" />
		</td>
		<td align="center" colspan="2" valign="bottom">
			<h4><?php echo $_SESSION["cliente"]["nombre_empresa"];?></h4>
		</td>
		<td  width="200px">
		</td>
	</tr>
</table>
<br/>
<table width="100%" border="0">
	<tr>
		<td width="100px"  valign="top" align="left">Cliente:</td>
		<td align="left" colspan="2"># <?php echo $idx.' - '.$rAux->nombre_cliente;?></td>
	</tr>
	<tr>
		<td width="100px"  valign="top" align="left">Direccion:</td>
		<td align="left"  colspan="2"><?php echo $rAux->direccion;?></td>
	</tr>
	<tr>
		<td colspan="4"   valign="middle" align="center"><b>Reporte <?php echo $returnFecha;?></b></td>
	</tr>
</table>
<table width="100%"  cellpadding="1" cellspacing="0" style="border:1px solid #020202" border="1">
		<tr  style="background:<?php echo $CONFIG['COLOR_HEAD'];?>; color:white ;">
			<td align="center">
				Movil
			</td>
			<td align="center">
				Hora
			</td>
			<td align="center">
				SMS
			</td>
		</tr>
		';
	<?php
	while($r=$query->getdata_object($MyrecordData)){

		$html.='<tr>
			<td align="center" valign="top">
				'.$r->movil.'
			</td>
			<td align="center" valign="top">
				'.date_format($r->fecha,"H:i:s").'
			</td>
			<td valign="top">
				'.$r->sms.'
			</td>
		</tr>';
	}
	echo $html;
	?>
</table>