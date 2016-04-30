<?php
include("../../include/scriptdb/config.php");
include("../../include/scriptdb/querys.php");
include("../../include/phpscript/generales.php");
include("../../include/phpscript/sessionAjaxCliente.php");

$acc= $_REQUEST['acc'];

$query= new Querys();
$conex = $query->getConection($CONFIG);

if(isset($acc) && $acc=='load_sms'){
	$vFilas = array();

	$fecha = explode("/",$_GET["fech"]);

	//contador
	$MyrecordDataCount = $query->SelDB($conex,"site_sel_BuscarPorSMSTotal",array($_GET["id"],$fecha[0],$fecha[1],$fecha[2]));
	$num_rows = $query->count_row($MyrecordDataCount);

	$per_page =  $_POST["iDisplayLength"];
	$row_start  = 0;

	if(isset($_POST["iDisplayStart"])){
		$row_start = $_POST["iDisplayStart"];
	}

	$row_end = intval($row_start)+intval($per_page);

	$paramPag=" RowID > ".$row_start." AND  RowID <= ".$row_end." ";

	//agregar el campo por el cual se ordena
	$Names = array("movil","fecha","sms");
	$order = " order by ".$Names[$_POST["iSortCol_0"]]." ".$_POST["sSortDir_0"];


	$MyrecordData = $query->SelDB($conex,"site_sel_BuscarPorSMSParam",array($_GET["id"],$fecha[0],$fecha[1],$fecha[2],$order,$paramPag));

	while($r=$query->getdata_object($MyrecordData)){
		unset($rAux);

		$rAux->m = $r->movil;
		$rAux->fech = date_format($r->fecha,"h:i:s a");
		$rAux->sms = $r->sms;

		$rAux->acci = "";

		$vFilas[] = $rAux;
	}




	$response->aaData=$vFilas;
	$response->sEcho = intval($_POST["sEcho"]);
	$response->iTotalRecords = $num_rows;
	$response->iTotalDisplayRecords = $num_rows;
	$response->date =  getFullDate($_GET["fech"]);

	echo json_encode($response);

}
?>