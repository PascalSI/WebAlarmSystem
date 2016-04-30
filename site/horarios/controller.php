<?php
include("../../include/scriptdb/config.php");
include("../../include/scriptdb/querys.php");
include("../../include/phpscript/generales.php");
include("../../include/phpscript/sessionAjaxCliente.php");

$acc= $_REQUEST['acc'];

$query= new Querys();
$conex = $query->getConection($CONFIG);

if(isset($acc) && $acc=='load_horarios'){
	$vFilas = array();
	$num_rows = 0;

	//agregar al query una busqueda
	if($_REQUEST["d"]!=""){
		$param = " AND (diaapertura = '".$_REQUEST["d"]."') ";
	}

	//agregar el campo por el cual se ordena
	$Names = array("diaapertura","horaapertura","horacierre");
	$order = " order by ".$Names[$_POST["iSortCol_0"]]." ".$_POST["sSortDir_0"];


	//contador
	$MyrecordDataCount = $query->SelDB($conex,"site_sel_AllHorariosClientTotal",array($_GET["id"],$param));
	$num_rows = $query->count_row($MyrecordDataCount);

	$per_page =  $_POST["iDisplayLength"];
	$row_start  = 0;

	if(isset($_POST["iDisplayStart"])){
		$row_start = $_POST["iDisplayStart"];
	}

	$row_end = intval($row_start)+intval($per_page);

	$paramPag=" RowID > ".$row_start." AND  RowID <= ".$row_end." ";

	$MyrecordData = $query->SelDB($conex,"site_sel_AllHorariosClientParam",array($_GET["id"],$param,$order,$paramPag));

	while($r=$query->getdata_object($MyrecordData)){
		unset($rAux);

		$rAux->id = $r->Id;
		$rAux->id_dia = $r->diaapertura;
		$rAux->dia = getDayNameNumber($r->diaapertura);
		$rAux->ha = date_format($r->horaapertura,"h:i:s a");
		$rAux->tolApe = $r->toleranciaapertura;
		$rAux->hc = date_format($r->horacierre,"h:i:s a");
		$rAux->tolCie = $r->toleranciacierre;

		$rAux->acci = "";

		$vFilas[] = $rAux;
	}

	$response->aaData=$vFilas;
	$response->sEcho = intval($_POST["sEcho"]);
	$response->iTotalRecords = $num_rows;
	$response->iTotalDisplayRecords = $num_rows;

	echo json_encode($response);

}
?>