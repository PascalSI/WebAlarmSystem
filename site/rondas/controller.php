<?php
include("../../include/scriptdb/config.php");
include("../../include/scriptdb/querys.php");
include("../../include/phpscript/generales.php");
include("../../include/phpscript/sessionAjaxCliente.php");

$acc= $_REQUEST['acc'];

$query= new Querys();
$conex = $query->getConection($CONFIG);

if(isset($acc) && $acc=='load_rondas'){
	$vFilas = array();
	$num_rows = 0;

	//agregar al query una busqueda
	if($_POST["sSearch"]!=""){
		$param = " and ((Nombre like '%".$_POST["sSearch"]."%')  )  ";
	}

	//contador
	$MyrecordDataCount = $query->SelDB($conex,"site_sel_AllRondasTotal",array($_GET["id"],$param));
	$num_rows = $query->count_row($MyrecordDataCount);


	$per_page =  $_POST["iDisplayLength"];
	$row_start  = 0;

	if(isset($_POST["iDisplayStart"])){
		$row_start = $_POST["iDisplayStart"];
	}

	$row_end = intval($row_start)+intval($per_page);

	$paramPag=" RowID > ".$row_start." AND  RowID <= ".$row_end." ";


	//agregar el campo por el cual se ordena
	$Names = array("Nombre","t.Descripcion","Hora_Inicio","Hora_Fin","Tolerancia","rc.Descripcion");
	$order = " order by ".$Names[$_POST["iSortCol_0"]]." ".$_POST["sSortDir_0"];

	$MyrecordData = $query->SelDB($conex,"site_sel_AllRondasParam",array($_GET["id"],$order,$param));

	while($r=$query->getdata_object($MyrecordData)){
		unset($rAux);

		$rAux->rAux = "";
		$rAux->id = $r->id_ronda;
		$rAux->nombre = $r->Nombre;
		$rAux->tipo = $r->Tipo;
		$rAux->hora_inicio =date_format($r->Hora_Inicio,"H:i:s");
		$rAux->hora_fin = date_format($r->Hora_Fin,"H:i:s");
		$rAux->tolerancia = $r->Tolerancia." Minutos";
		$rAux->calencario = $r->Calendario;


		$vFilas[] = $rAux;
	}
	$response->aaData=$vFilas;
	$response->sEcho = intval($_POST["sEcho"]);
	$response->iTotalRecords = $num_rows;
	$response->iTotalDisplayRecords = $num_rows;

	echo json_encode($response);
}

?>