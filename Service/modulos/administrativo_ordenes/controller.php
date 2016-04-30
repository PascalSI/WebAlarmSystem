<?php
include(dirname(dirname(dirname(dirname(__FILE__))))."/include/scriptdb/config.php");
include(dirname(dirname(dirname(dirname(__FILE__))))."/include/scriptdb/querys.php");
include(dirname(dirname(dirname(dirname(__FILE__))))."/include/phpscript/generales.php");
include(dirname(dirname(dirname(dirname(__FILE__))))."/include/phpscript/sessionAjax.php");
include(dirname(dirname(dirname(dirname(__FILE__))))."/include/plugins/phpMailer/PHPMailerAutoload.php");

$query= new Querys();
$conex = $query->getConection($CONFIG);

include(dirname(dirname(dirname(__FILE__)))."/include/phpscript/i_ManagerOrdServicio.php");
include(dirname(dirname(dirname(__FILE__)))."/include/phpscript/i_ManagerNotificaciones.php");

$acc= $_REQUEST['acc'];

if(isset($acc) && $acc=='load_client_ord'){ //load clientes que se le aya creado una orden de servicio
	$paramCO = "";
	$vFilasCo = array();

	//agregar al query una busqueda
	if($_REQUEST['q']!=""){
		$paramCO.=" and (c.nombre_cliente  like '%".$_REQUEST['q']."%' or concat(c.prefijo,'-',c.cuenta) like '".$_REQUEST['q']."%')";
	}

	$MyrecordData = $query->SelDB($conex,"site_sel_OrdSearchCliente",array($_SESSION["user"]["idEmpresa"],$paramCO));


	while($r=$query->getdata_object($MyrecordData)){
		unset($rAux);

		$rAux->idT = $r->tipoc;
		$rAux->id = $r->id_cliente;
		$rAux->text = $r->prefijo."-".$r->cuenta." - ".$r->nombre_cliente;
		$vFilasCo[] = $rAux;
	}


	$response->aaData=$vFilasCo;
	echo json_encode($response);
}else if(isset($acc) && $acc=='load_ad_ord_XCobrar'){ //carga ordenes de servicio en estatus ejecutadas
	$vFilas = array();
	$num_rows = 0;

	$iTotalRecords = 0;
	$paramOdV1 = "";
	$paramOdV2 = "";
	$partQuery = 0; //determina si se va ejecutar como UNION ALL o querys por separados al elegir un cliente

	//filtros

	//status de la orden
	if($_REQUEST["sts"]!=""){
		$paramOdV1 = $paramOdV1." AND (o.id_status='".$_REQUEST["sts"]."')";
	}

	//tipo de ordern
	$paramOdV1 = $paramOdV1." AND (o.id_tipo_orden='1')";

	//problema presentado
	if($_REQUEST["problm"]!=""){
		$paramOdV1 = $paramOdV1." AND (o.problema like '%".$_REQUEST["problm"]."%') ";
	}

	//fechas
	if($_REQUEST["f1"]!=""){
		if($_REQUEST["f2"] == ""){
			$paramOdV1 = $paramOdV1." AND (DAY(o.fechaCreada) = ".datePreparePart($_REQUEST["f1"],"day").") AND (MONTH(o.fechaCreada) = ".datePreparePart($_REQUEST["f1"],"month").")  AND (YEAR(o.fechaCreada) = ".datePreparePart($_REQUEST["f1"],"year").") ";
		}else{
			$paramOdV1 = $paramOdV1."AND (o.fechaCreada >= CONVERT(datetime,'".$_REQUEST["f1"] ."', 103)) AND  (o.fechaCreada <= DATEADD(dd, 1, CONVERT(datetime,'".$_REQUEST["f2"]."', 103)))";
		}
	}

	$paramOdV2 = $paramOdV1;

	//cliente
	if($_REQUEST["client"]!=""){
		if($_REQUEST["tipoc"]==1){
			$paramOdV1 = $paramOdV1." AND (o.id_cliente='".$_REQUEST["client"]."')";
			$partQuery=1;
		}else{
			$paramOdV2 = $paramOdV2." AND (o.id_cliente='".$_REQUEST["client"]."')";
			$partQuery=2;
		}
	}

	$MyRecordOdVC = $query->SelDB($conex,"site_sel_OrdenModAdminFinalizadasCount",array($_SESSION["user"]["idEmpresa"],$paramOdV1,$paramOdV2,$order,$partQuery,$_REQUEST["sts"]));

	$num_rows = $query->count_row($MyRecordOdVC);

	$per_page =  $_POST["iDisplayLength"];
	$row_start  = 0;

	if(isset($_POST["iDisplayStart"])){
		$row_start = $_POST["iDisplayStart"];
	}

	$row_end = intval($row_start)+intval($per_page);

	$paramPag=" RowID > ".$row_start." AND  RowID <= ".$row_end." ";

	//agregar el campo por el cual se ordena
	$Names = array(
		"1"=>"o.id_orden",
		"2"=>"c.nombre_cliente",
		"7"=>"diff"
	);
	$order = " ORDER BY ".$Names[$_REQUEST["iSortCol_0"]]." ".$_REQUEST["sSortDir_0"];

	$NamesAux = array(
		"1"=>"id_orden",
		"2"=>"nombre_cliente",
		"7"=>"diff"
	);
	$orderAux = " ORDER BY ".$NamesAux[$_REQUEST["iSortCol_0"]]." ".$_REQUEST["sSortDir_0"];

	$MyrecordData = $query->SelDB($conex,"site_sel_OrdenModAdminFinalizadas",array($_SESSION["user"]["idEmpresa"],$paramOdV1,$paramOdV2,$order,$partQuery,$_REQUEST["sts"],$paramPag,$orderAux));

	while($r=$query->getdata_object($MyrecordData)){
		unset($rAux);

		$colorbg = $r->colorbg;
		$color = $r->color;

		$obserFinal = "";

		//busca la ultima observacion al ser ejecutada la orden
		$MyRecordObFinal = $query->SelDB($conex,"site_sel_OrdenServicioVisitaObFInal",array($r->id_orden));

		if($query->count_row($MyRecordObFinal)>0){
			$rC = $query->getdata_object($MyRecordObFinal);
			$obserFinal = $rC->descripcion;
		}

		$rAux->prioridad = $r->prioridad;
		$rAux->id_orden = $r->id_orden;
		$rAux->idcliente = $r->id_cliente;
		$rAux->nombre = trim($r->prefijo."-".$r->cuenta." - ".$r->nombre_cliente);
		$rAux->name = trim($r->prefijo."-".$r->cuenta." - ".$r->nombre_cliente);
		$rAux->problema = trim($r->problema);
		$rAux->pre_observacion = trim($r->pre_observacion);
		$rAux->fechaCreada = date_format($r->fechaCreada,"d/m/Y");
		$rAux->estatus = trim($r->descripcion);
		$rAux->colorbg = $colorbg;
		$rAux->color = $color;
		$rAux->acci = "";
		$rAux->obsFinal = trim($obserFinal);
		$rAux->tipoc = $r->tipo_cliente;
		$rAux->id_crip = encode64_asp($r->id_cliente);
		$rAux->id_status = $r->id_status;
		$rAux->st = $r->st;
		$rAux->id_tipo_orden = $r->id_tipo_orden;
		$rAux->correlativo = $r->correlativo;
		$rAux->tipo_orden = $r->tipo_orden;

		$vFilas[] = $rAux;
	}

	$response->aaData = $vFilas;
	$response->sEcho = intval($_POST["sEcho"]);
	$response->iTotalRecords = $num_rows;
	$response->iTotalDisplayRecords = $num_rows;

	echo json_encode($response);
}else if(isset($acc) && $acc=='load_ad_ord_facturadas'){ //carga ordenes de servicio en estatus facturadas
	$vFilas = array();
	$num_rows = 0;

	$iTotalRecords=0;
	$paramOdV1 ="";
	$paramOdV2="";
	$partQuery=0; // determina si se va ejecutar como UNION ALL o querys por separados al elegir un cliente

	//filtros

	//status de la orden
	if($_REQUEST["sts"]!=""){
		$paramOdV1 = $paramOdV1." AND (o.id_status_admin='".$_REQUEST["sts"]."')";
	}

	//tipo de ordern
	$paramOdV1 = $paramOdV1." AND (o.id_tipo_orden='1')";

	//problema presentado
	if($_REQUEST["problm"]!=""){
		$paramOdV1 = $paramOdV1." AND (o.problema like '%".$_REQUEST["problm"]."%') ";
	}

	//fechas
	if($_REQUEST["f1"]!=""){
		if($_REQUEST["f2"] == ""){
			$paramOdV1 = $paramOdV1." AND (DAY(o.fecha_factura) = ".datePreparePart($_REQUEST["f1"],"day").") AND (MONTH(o.fecha_factura) = ".datePreparePart($_REQUEST["f1"],"month").")  AND (YEAR(o.fecha_factura) = ".datePreparePart($_REQUEST["f1"],"year").") ";
		}else{
			$paramOdV1 = $paramOdV1."AND (o.fecha_factura >= CONVERT(datetime,'".$_REQUEST["f1"] ."', 103)) AND  (o.fecha_factura <= DATEADD(dd, 1, CONVERT(datetime,'".$_REQUEST["f2"]."', 103)))";
		}
	}

	$paramOdV2 = $paramOdV1;

	//cliente
	if($_REQUEST["client"]!=""){
		if($_REQUEST["tipoc"]==1){
			$paramOdV1 = $paramOdV1." AND (o.id_cliente='".$_REQUEST["client"]."')";
			$partQuery=1;
		}else{
			$paramOdV2 = $paramOdV2." AND (o.id_cliente='".$_REQUEST["client"]."')";
			$partQuery=2;
		}
	}

	$MyRecordOdVC = $query->SelDB($conex,"site_sel_OrdenModAdminFacturadasCount",array($_SESSION["user"]["idEmpresa"],$paramOdV1,$paramOdV2,$order,$partQuery,$_REQUEST["sts"]));

	$num_rows = $query->count_row($MyRecordOdVC);

	$per_page =  $_POST["iDisplayLength"];
	$row_start  = 0;

	if(isset($_POST["iDisplayStart"])){
		$row_start = $_POST["iDisplayStart"];
	}

	$row_end = intval($row_start)+intval($per_page);

	$paramPag=" RowID > ".$row_start." AND  RowID <= ".$row_end." ";

	//agregar el campo por el cual se ordena
	$Names = array(
		"1"=>"o.id_orden",
		"2"=>"c.nombre_cliente",
		"7"=>"diff"
	);
	$order = " ORDER BY ".$Names[$_REQUEST["iSortCol_0"]]." ".$_REQUEST["sSortDir_0"];

	$NamesAux = array(
		"1"=>"id_orden",
		"2"=>"nombre_cliente",
		"7"=>"diff"
	);
	$orderAux = " ORDER BY ".$NamesAux[$_REQUEST["iSortCol_0"]]." ".$_REQUEST["sSortDir_0"];

	$MyrecordData = $query->SelDB($conex,"site_sel_OrdenModAdminFacturadas",array($_SESSION["user"]["idEmpresa"],$paramOdV1,$paramOdV2,$order,$partQuery,$_REQUEST["sts"],$paramPag,$orderAux));

	while($r=$query->getdata_object($MyrecordData)){
		unset($rAux);

		$colorbg = $r->colorbg;
		$color = $r->color;

		$obserFinal = "";

		$rAux->prioridad = $r->prioridad;
		$rAux->id_orden = $r->id_orden;
		$rAux->idcliente = $r->id_cliente;
		$rAux->nombre = trim($r->prefijo."-".$r->cuenta." - ".$r->nombre_cliente);
		$rAux->name = trim($r->prefijo."-".$r->cuenta." - ".$r->nombre_cliente);
		$rAux->problema = trim($r->problema);
		$rAux->estatus = trim($r->descripcion);
		$rAux->colorbg = trim($r->colorbg);
		$rAux->color = trim($r->color);
		$rAux->acci = "";
		$rAux->tipoc = trim($r->tipo_cliente);
		$rAux->id_crip = encode64_asp($r->id_cliente);
		$rAux->id_status = $r->id_status_admin;
		$rAux->st = $r->st;
		$rAux->id_tipo_orden = $r->id_tipo_orden;
		$rAux->correlativo = $r->correlativo;
		$rAux->tipo_orden = $r->tipo_orden;
		$rAux->fechaFact =  date_format($r->fecha_factura,"d/m/Y");
		$rAux->codeFact = $r->codigo_factura;
		$rAux->montoFact = $r->monto_factura;

		$vFilas[] = $rAux;
	}

	$response->aaData = $vFilas;
	$response->sEcho = intval($_POST["sEcho"]);
	$response->iTotalRecords = $num_rows;
	$response->iTotalDisplayRecords = $num_rows;

	echo json_encode($response);
}else if(isset($acc) && $acc=='load_ad_ord_pagadas'){ //carga ordenes de servicio en estatus pagadas
	$vFilas = array();
	$num_rows = 0;

	$iTotalRecords=0;
	$paramOdV1 ="";
	$paramOdV2="";
	$partQuery=0; // determina si se va ejecutar como UNION ALL o querys por separados al elegir un cliente

	//filtros

	//status de la orden
	if($_REQUEST["sts"]!=""){
		$paramOdV1 = $paramOdV1." AND (o.id_status_admin='".$_REQUEST["sts"]."')";
	}

	//tipo de ordern
	$paramOdV1 = $paramOdV1." AND (o.id_tipo_orden='1')";

	//problema presentado
	if($_REQUEST["problm"]!=""){
		$paramOdV1 = $paramOdV1." AND (o.problema like '%".$_REQUEST["problm"]."%') ";
	}

	//fechas
	if($_REQUEST["f1"]!=""){
		if($_REQUEST["f2"] == ""){
			$paramOdV1 = $paramOdV1." AND (DAY(o.fecha_pago) = ".datePreparePart($_REQUEST["f1"],"day").") AND (MONTH(o.fecha_pago) = ".datePreparePart($_REQUEST["f1"],"month").")  AND (YEAR(o.fecha_pago) = ".datePreparePart($_REQUEST["f1"],"year").") ";
		}else{
			$paramOdV1 = $paramOdV1."AND (o.fecha_pago >= CONVERT(datetime,'".$_REQUEST["f1"] ."', 103)) AND  (o.fecha_pago <= DATEADD(dd, 1, CONVERT(datetime,'".$_REQUEST["f2"]."', 103)))";
		}
	}

	$paramOdV2 = $paramOdV1;

	//cliente
	if($_REQUEST["client"]!=""){
		if($_REQUEST["tipoc"]==1){
			$paramOdV1 = $paramOdV1." AND (o.id_cliente='".$_REQUEST["client"]."')";
			$partQuery=1;
		}else{
			$paramOdV2 = $paramOdV2." AND (o.id_cliente='".$_REQUEST["client"]."')";
			$partQuery=2;
		}
	}

	$MyRecordOdVC = $query->SelDB($conex,"site_sel_OrdenModAdminPagadasCount",array($_SESSION["user"]["idEmpresa"],$paramOdV1,$paramOdV2,$order,$partQuery,$_REQUEST["sts"]));

	$num_rows = $query->count_row($MyRecordOdVC);

	$per_page =  $_POST["iDisplayLength"];
	$row_start  = 0;

	if(isset($_POST["iDisplayStart"])){
		$row_start = $_POST["iDisplayStart"];
	}

	$row_end = intval($row_start)+intval($per_page);

	$paramPag=" RowID > ".$row_start." AND  RowID <= ".$row_end." ";

	//agregar el campo por el cual se ordena
	$Names = array(
		"1"=>"o.id_orden",
		"2"=>"c.nombre_cliente",
		"7"=>"diff"
	);
	$order = " ORDER BY ".$Names[$_REQUEST["iSortCol_0"]]." ".$_REQUEST["sSortDir_0"];

	$NamesAux = array(
		"1"=>"id_orden",
		"2"=>"nombre_cliente",
		"7"=>"diff"
	);
	$orderAux = " ORDER BY ".$NamesAux[$_REQUEST["iSortCol_0"]]." ".$_REQUEST["sSortDir_0"];

	$MyrecordData = $query->SelDB($conex,"site_sel_OrdenModAdminPagadas",array($_SESSION["user"]["idEmpresa"],$paramOdV1,$paramOdV2,$order,$partQuery,$_REQUEST["sts"],$paramPag,$orderAux));

	while($r=$query->getdata_object($MyrecordData)){
		unset($rAux);

		$colorbg = $r->colorbg;
		$color = $r->color;

		$rAux->prioridad = $r->prioridad;
		$rAux->id_orden = $r->id_orden;
		$rAux->idcliente = $r->id_cliente;
		$rAux->nombre = trim($r->prefijo."-".$r->cuenta." - ".$r->nombre_cliente);
		$rAux->name = trim($r->prefijo."-".$r->cuenta." - ".$r->nombre_cliente);
		$rAux->problema = trim($r->problema);
		$rAux->estatus = trim($r->descripcion);
		$rAux->colorbg = trim($r->colorbg);
		$rAux->color = trim($r->color);
		$rAux->acci = "";
		$rAux->tipoc = trim($r->tipo_cliente);
		$rAux->id_crip = encode64_asp($r->id_cliente);
		$rAux->id_status = $r->id_status_admin;
		$rAux->st = $r->st;
		$rAux->id_tipo_orden = $r->id_tipo_orden;
		$rAux->correlativo = $r->correlativo;
		$rAux->tipo_orden = $r->tipo_orden;
		$rAux->fechaPago =  date_format($r->fecha_pago,"d/m/Y");
		$rAux->identiPago = $r->identity_pago;
		$rAux->formaPago = $r->formaPago;

		$vFilas[] = $rAux;
	}



	$response->aaData = $vFilas;
	$response->sEcho = intval($_POST["sEcho"]);
	$response->iTotalRecords = $num_rows;
	$response->iTotalDisplayRecords = $num_rows;

	echo json_encode($response);
}else if(isset($acc) && $acc=='chageStatus'){ //cambia el estatus de la orden
	$idx =  $_REQUEST["id"];

	switch ($_REQUEST["st"]) {
		case "7":
			$montoFact = $_REQUEST["monto"];

			//pasa orden a facturada y agrega comentario
			$arrayData = array($_REQUEST["fecha"],$_REQUEST["codigo"],$montoFact,trim($_REQUEST["coment"]));
			$textLog = getDetailLog("msgAddFact",$arrayData);

			$LastIdC = $query->InsDB($conex,"site_ins_OrdenesServicioLogs",array(
				'id_orden'=>$idx,
				'id_status'=>$_REQUEST["st"],
				'id_accion'=>4,
				'id_usuario'=>$_SESSION["user"]["idOperador"],
				'descripcion'=>$textLog,
				'privado'=>0,
			));

			$param = " id_status_admin = ".$_REQUEST["st"]." , fecha_factura=  CAST('".$_REQUEST["fecha"]." 12:00:00' AS smalldatetime) , codigo_factura = '".$_REQUEST["codigo"]."' , monto_factura = Convert(float,".$montoFact.")";
		break;

		case "8":
			//pasa orden a pagada y agrega comentario
			$arrayData = array($_REQUEST["fecha"],$_REQUEST["tipoPText"],$_REQUEST["idtP"],trim($_REQUEST["coment"]));
			$textLog = getDetailLog("msgAddPago",$arrayData);

			$LastIdC = $query->InsDB($conex,"site_ins_OrdenesServicioLogs",array(
				'id_orden'=>$idx,
				'id_status'=>$_REQUEST["st"],
				'id_accion'=>4,
				'id_usuario'=>$_SESSION["user"]["idOperador"],
				'descripcion'=>$textLog,
				'privado'=>0,
			));

			$param = "   id_status_admin=".$_REQUEST["st"]."  , fecha_pago = CAST('".$_REQUEST["fecha"]." 12:00:00' AS smalldatetime) , id_forma_pago  = '" . trim($_REQUEST["tipoP"]) . "' , identity_pago  = '" . trim($_REQUEST["idtP"]) . "'  ";
		break;

	}

	$query->UpdDB($conex,"site_upd_ordenes_st",array($param,$idx));
	echo "ok";
}

?>