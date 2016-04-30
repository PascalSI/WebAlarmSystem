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

$NameFile = $idx.'-'.$rAux->nombre_cliente ;

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


		if($_GET["d1"]){
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
			<img src="<?php echo $logo;?>" width="100px" height="40px" />
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
		<td align="left" colspan="3"># <?php echo $idx.' - '.$rAux->nombre_cliente;?></td>
	</tr>
	<tr>
		<td width="100px"  valign="top" align="left">Direccion:</td>
		<td align="left"  colspan="3"><?php echo $rAux->direccion;?></td>
	</tr>
	<tr>
		<td colspan="4"   valign="middle" align="center"><b>Reporte <?php echo $returnFecha;?></b></td>
	</tr>
</table>
<table width="100%"  cellpadding="1" cellspacing="0" style="border:1px solid #020202" border="1">
	<tr  style="background:<?php echo $CONFIG['COLOR_HEAD'];?>; color:white ;">
		<td align="center">
			N°
		</td>
		<td align="center">
			Evento
		</td>
		<td align="center">
			Usuario/Zona
		</td>
		<td align="center">
			Fecha
		</td>
	</tr>
	<?php
	$MyrecordData = $query->SelDB($conex,$query_result,$paramResult);

	$i=1;
	while($r=$query->getdata_object($MyrecordData)){

		$auxEvento =DecoEventoWS($r->Variante,$r->descript,$r->protocolo);
		$textEvento = $r->evento." ".$auxEvento;

		$html.='<tr>
			<td align="center" valign="middle">
				'.$i.'
			</td>
			<td align="center" valign="middle">
				'.$textEvento.'
			</td>
			<td align="center" valign="middle">
				'.$r->UserZona.'
			</td>
			<td align="center" valign="middle">
				'.date_format($r->fecha,"d-m-Y H:i:s").'
			</td>
		</tr>';

		$i++;

	}
	echo $html;
	?>
</table>
