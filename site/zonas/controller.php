<?php
include("../../include/scriptdb/config.php");
include("../../include/scriptdb/querys.php");
include("../../include/phpscript/generales.php");
include("../../include/phpscript/sessionAjaxCliente.php");

$acc= $_REQUEST['acc'];

$query= new Querys();
$conex = $query->getConection($CONFIG);

if(isset($acc) && $acc=='load_zonas'){
	$vFilas = array();
	$num_rows = 0;

	//agregar al query una busqueda
	if($_POST["sSearch"]!=""){
		$param = " and ((descrip like '%".$_POST["sSearch"]."%') or (id_zona like '%".$_POST["sSearch"]."%'))  ";
	}

	//contador
	$MyrecordDataCount = $query->SelDB($conex,"site_sel_AllZonasTotal",array($_GET["id"],$param));
	$num_rows = $query->count_row($MyrecordDataCount);


	$per_page =  $_POST["iDisplayLength"];
	$row_start  = 0;

	if(isset($_POST["iDisplayStart"])){
		$row_start = $_POST["iDisplayStart"];
	}

	$row_end = intval($row_start)+intval($per_page);

	$paramPag=" RowID > ".$row_start." AND  RowID <= ".$row_end." ";


	//agregar el campo por el cual se ordena
	$Names = array("CASE WHEN ISNUMERIC(id_zona) = 1 THEN 0 ELSE 1 END, CASE WHEN ISNUMERIC(id_zona) = 1 THEN CAST(id_zona AS INT) ELSE 0 END","zona");
	$order = " order by ".$Names[$_POST["iSortCol_0"]];

	$MyrecordData = $query->SelDB($conex,"site_sel_AllZonasParam",array($_GET["id"],$param,$order,$_POST["sSortDir_0"],$paramPag));

	while($r=$query->getdata_object($MyrecordData)){
		$addimg = "";

		$MyrecordIMgZ = $query->SelDB($conex,"site_sel_ClientesZonasImagen",array($r->id,$_GET["id"]));
		while($rI=$query->getdata_object($MyrecordIMgZ)){
			$addimg.= ",".$rI->imagen;
		}

		$r->imgs = ltrim($addimg,",");
		$vFilas[] = $r;
	}
	$response->aaData=$vFilas;
	$response->sEcho = intval($_POST["sEcho"]);
	$response->iTotalRecords = $num_rows;
	$response->iTotalDisplayRecords = $num_rows;

	echo json_encode($response);
}

?>