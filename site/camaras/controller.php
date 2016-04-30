<?php
include("../../include/scriptdb/config.php");
include("../../include/scriptdb/querys.php");
include("../../include/phpscript/generales.php");
include("../../include/phpscript/sessionAjaxCliente.php");

$acc= $_REQUEST['acc'];

$query= new Querys();
$conex = $query->getConection($CONFIG);

if(isset($acc) && $acc=='load_cam'){
	$vFilas = array();
	$num_rows = 0;

	//agregar al query una busqueda
	if($_POST["sSearch"]!=""){
		$param = " and ((c.descripcion like '%".$_POST["sSearch"]."%'))  ";
	}

	//agregar el campo por el cual se ordena

	$order = " order by c.descripcion ".$_POST["sSortDir_0"];
	$order2 = " order by descripcion ".$_POST["sSortDir_0"];

	//contador
	$MyrecordDataCount = $query->SelDB($conex,"site_sel_ALLClienteCamTotal",array($_GET["id"],$param));
	$num_rows = $query->count_row($MyrecordDataCount);

	$per_page =  $_POST["iDisplayLength"];
	$row_start  = 0;

	if(isset($_POST["iDisplayStart"])){
		$row_start = $_POST["iDisplayStart"];
	}

	$row_end = intval($row_start)+intval($per_page);

	$paramPag=" RowID > ".$row_start." AND  RowID <= ".$row_end." ";


	$MyrecordData = $query->SelDB($conex,"site_sel_ALLClienteCamParam",array($_GET["id"],$param,$order,$paramPag,$order2));

	while($r=$query->getdata_object($MyrecordData)){
		unset($rAux);

		$rAux->id = $r->id_cctv;
		$rAux->idCry = encode64_asp($r->id_cctv);
		$rAux->modo = $r->modo;
		$rAux->ip = $r->ip;
		$rAux->puerto = $r->puerto;
		$rAux->desc = $r->descripcion;
		$rAux->tipo = $r->tipoc;
		$rAux->icon = get_IconModoCam($r->id_modo);

		$vFilasC = array();
		$paramChanel = " and id_cctv='$r->id_cctv'";
		$MyrecordUSer = $query->SelDB($conex,"site_sel_DataCCTV_Channel",array($paramChanel));
		while($rU=$query->getdata_object($MyrecordUSer)){
			unset($rAuxC);

			$rAuxC->channel = $rU->channel;
			$rAuxC->channelCry = encode64_asp($rU->channel);
			$rAuxC->desc = $rU->descripcion;
			$vFilasC[] = $rAuxC;
		}

		$rAux->channels = $vFilasC;

		$rAux->plus = "";
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