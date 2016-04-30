<?php
include("../../include/scriptdb/config.php");
include("../../include/scriptdb/querys.php");
include("../../include/phpscript/generales.php");
include("../../include/phpscript/sessionAjaxCliente.php");

$acc= $_REQUEST['acc'];

$query= new Querys();
$conex = $query->getConection($CONFIG);

if(isset($acc) && $acc=='load_ordenes'){
	$vFilas = array();
	$param = " AND (o.id_cliente = '".$_REQUEST["c"]."') AND (o.tipo_cliente = '1')";

	//contador
	$MyrecordDataCount = $query->SelDB($conex,"site_sel_ReportOrdListClienteFinalCount",array($param));
	$num_rows = $query->count_row($MyrecordDataCount);

	$per_page =  $_POST["iDisplayLength"];
	$row_start  = 0;

	if(isset($_POST["iDisplayStart"])){
		$row_start = $_POST["iDisplayStart"];
	}

	$row_end = intval($row_start)+intval($per_page);

	$paramPag=" RowID > ".$row_start." AND  RowID <= ".$row_end." ";


	$MyrecordData = $query->SelDB($conex,"site_sel_ReportOrdListClienteFinal",array($param,$paramPag));

	while($r=$query->getdata_object($MyrecordData)){
		unset($rAux);

		$finalizada = date_format($r->fecha_fin,"d/m/Y");
		if(!$finalizada){
			$finalizada = 'Sin Finalizar';
		}

		$rAux->id_status = $r->id_status;
		$rAux->correlativo = $r->correlativo;
		$rAux->problema = $r->problema;
		$rAux->fechaCreada = date_format($r->fechaCreada,"d/m/Y");
		$rAux->estatus = $r->estatus;
		$rAux->colorbg = $r->colorbg;
		$rAux->color = $r->color;
		$rAux->finalizada = $finalizada;
		$rAux->comnt_fin = trim($r->comnt_fin);
		$rAux->id_orden = $r->id_orden;
		$rAux->sts = $r->id_status;
		$rAux->tipo_orden = $r->tipo_orden;
		$rAux->tipoc = $r->tipo_cliente;
		$rAux->acci = "";

		$vFilas[] = $rAux;
	}

	$response->aaData = $vFilas;
	$response->sEcho = intval($_POST["sEcho"]);
	$response->iTotalRecords = $num_rows;
	$response->iTotalDisplayRecords = $num_rows;

	echo json_encode($response);
}

?>