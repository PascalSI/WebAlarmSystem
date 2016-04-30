<?php
include("../../include/scriptdb/config.php");
include("../../include/scriptdb/querys.php");
include("../../include/phpscript/generales.php");
include("../../include/phpscript/sessionAjaxCliente.php");

$acc= $_REQUEST['acc'];

$query= new Querys();
$conex = $query->getConection($CONFIG);

if(isset($acc) && $acc=='load_asist'){
	$vFilas = array();
	$num_rows = 0;

	//agregar al query una busqueda
	if($_POST["sSearch"]!=""){
		$param = " and ((descript like '%".$_POST["sSearch"]."%') or (numero like '%".$_POST["sSearch"]."%'))  ";
	}

	//agregar el campo por el cual se ordena
	$Names = array("prioridad","numero","descript");
	$order = " order by ".$Names[$_POST["iSortCol_0"]]." ".$_POST["sSortDir_0"];

	//contador
	$MyrecordDataCount = $query->SelDB($conex,"site_sel_AllClienteNumEmergenciaTotal",array($_GET["id"],$param));
	$num_rows = $query->count_row($MyrecordDataCount);

	$per_page =  $_POST["iDisplayLength"];
	$row_start  = 0;

	if(isset($_POST["iDisplayStart"])){
		$row_start = $_POST["iDisplayStart"];
	}

	$row_end = intval($row_start)+intval($per_page);

	$paramPag=" RowID > ".$row_start." AND  RowID <= ".$row_end." ";


	$MyrecordData = $query->SelDB($conex,"site_sel_AllClienteNumEmergenciaParam",array($_GET["id"],$param,$order,$paramPag));

	while($r=$query->getdata_object($MyrecordData)){
		unset($rAux);

		$rAux->id = $r->id_numero;
		$rAux->num = $r->numero;
		$rAux->desc = $r->descript;
		$rAux->ob = $r->observacion;
		$rAux->priori = $r->prioridad;
		$rAux->acci = "";

		$vFilas[] = $rAux;
	}
	$response->aaData=$vFilas;
	$response->sEcho = intval($_POST["sEcho"]);
	$response->iTotalRecords = $num_rows;
	$response->iTotalDisplayRecords = $num_rows;
	$response->idDisplay = $num_rows;


	echo json_encode($response);
}
?>