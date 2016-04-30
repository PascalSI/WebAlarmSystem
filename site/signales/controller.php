<?php
include("../../include/scriptdb/config.php");
include("../../include/scriptdb/querys.php");
include("../../include/phpscript/generales.php");
include("../../include/phpscript/sessionAjaxCliente.php");

$acc= $_REQUEST['acc'];

$query= new Querys();
$conex = $query->getConection($CONFIG);

if(isset($acc) && $acc=='load_signal'){
	$vFilas = array();
	$num_rows = 0;
	$paramAux = "";

	$RecbFecha = $_GET["fech"];

	$fecha = explode("/",$_GET["fech"]);

	if($_GET["cat"]!="-1"){
		$paramAux.="AND (idGrupo = '".$_GET["cat"]."')";
	}

	if($_GET["d1"]!="" && $_GET["d2"]!=""){
		$paramAux.="AND (fecha >= CONVERT(datetime,'".$_GET["d1"]."', 103)) AND (fecha <= DATEADD(dd, 1, CONVERT(datetime,'".$_GET["d2"]."', 103)))";
	}

	//paginado
	$per_page =  $_POST["iDisplayLength"];
	$row_start  = 0;

	if(isset($_POST["iDisplayStart"])){
		$row_start = $_POST["iDisplayStart"];
	}

	$row_end = intval($row_start)+intval($per_page);

	$paramPag=" RowID > ".$row_start." AND  RowID <= ".$row_end." ";



	//obtiene el total de los registros
	if($RecbFecha=="1" && $_GET["tipo"]==1){

		$returnFecha ="&Uacute;ltimas 100 Se&ntilde;ales";

		//var contadores
		$query_count="site_sel_UltimaSenalesClienteTotal";
		$paramCount = array($_GET["id"]);

		//var result
		$query_result="site_sel_UltimaSenalesClienteParam";
		$paramResult = array($_GET["id"],$paramPag);

	}else{
		if($_GET["tipo"]==1){
			$returnFecha = getFullDate($RecbFecha);

			//var contadores
			$query_count="site_sel_BuscarPorFechaTotal";
			$paramCount = array($_GET["id"],$fecha[0],$fecha[1],$fecha[2]);

			//var result
			$query_result="site_sel_BuscarPorFechaParam";
			$paramResult = array($_GET["id"],$fecha[0],$fecha[1],$fecha[2],$paramPag);


		}else{
			$returnFecha = "Se&ntilde;ales  desde: ".$_GET["d1"]." hasta: ".$_GET["d2"];

			//var contadores
			$query_count="site_sel_BuscarPorFechaRangeTotal";
			$paramCount = array($_GET["id"],$paramAux);

			//var result
			$query_result="site_sel_BuscarPorFechaRangeParam";
			$paramResult = array($_GET["id"],$paramAux,$paramPag);
		}
	}

	//contador
	$MyrecordDataCount = $query->SelDB($conex,$query_count,$paramCount);
	$num_rows = $query->count_row($MyrecordDataCount);



	$MyrecordData = $query->SelDB($conex,$query_result,$paramResult);

	while($r=$query->getdata_object($MyrecordData)){
		unset($rAux);
		$auxEvento = "";
		$color = "";
		$bg="";

		$auxEvento =DecoEventoWS($r->Variante,$r->descript,$r->protocolo);
		$textEvento = $r->evento." - ".$auxEvento;

		$bg = setColorBg($r->web_colorBg);
		$color = setColor($r->web_color);

		if($RecbFecha=="1" || $_GET["tipo"]!=1){
			$refecha = getFullDateShort($r->fecha);
		}else {
			$refecha = getHoraShort($r->fecha);
		}

		$rAux->id_trama = $r->id_trama;
		$rAux->event = $textEvento;
		$rAux->userzona = $r->UserZona;
		$rAux->bg = $bg;
		$rAux->fecha = $refecha;
		$rAux->fechaP = getFullDateShort($r->Fecha_proc);
		$rAux->color = $color;
		$rAux->operador = $r->operador;
		$rAux->ob = $r->Obser;

		$vFilas[] = $rAux;
	}



	$response->aaData=$vFilas;
	$response->sEcho = intval($_POST["sEcho"]);
	$response->iTotalRecords = $num_rows;
	$response->iTotalDisplayRecords = $num_rows;
	$response->date = $returnFecha;

	echo json_encode($response);

}
?>