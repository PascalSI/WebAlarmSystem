<?php
include(dirname(dirname(dirname(dirname(__FILE__))))."/include/scriptdb/config.php");
include(dirname(dirname(dirname(dirname(__FILE__))))."/include/scriptdb/querys.php");
include(dirname(dirname(dirname(dirname(__FILE__))))."/include/phpscript/generales.php");
include(dirname(dirname(dirname(dirname(__FILE__))))."/include/phpscript/sessionAjax.php");

$query= new Querys();
$conex = $query->getConection($CONFIG);

include(dirname(dirname(dirname(__FILE__)))."/include/phpscript/i_ManagerOrdServicio.php");
include(dirname(dirname(dirname(__FILE__)))."/include/phpscript/i_ManagerNotificaciones.php");


$acc= $_REQUEST['acc'];

if(isset($acc) && $acc=='addRecord'){
	$idLast = 0;
	$idOrden = $_REQUEST["idO"];
	$fechaRecord = $_REQUEST["fec"]." ".$_REQUEST["time"];
	$fechaHum = $_REQUEST["fec"]." ".$_REQUEST["timeDesc"];

	$idLast = $query->InsDB($conex,"site_ins_OrdenesServicioRecordatorios",array($_SESSION["user"]["idOperador"],$idOrden,$fechaRecord,trim($_REQUEST["tit"]),trim($_REQUEST["desc"])));

	$query->InsDB($conex,"site_ins_OrdenesServicioRecordatoriosUsrRel",array($idLast,$_REQUEST["users"]));

	$textLog = getDetailLog("addRecordatorio",array($fechaHum,trim($_REQUEST["tit"]),trim($_REQUEST["desc"])));

	$LastIdCom = $query->InsDB($conex,"site_ins_OrdenesServicioLogs",array(
		'id_orden'=>$idOrden,
		'id_status'=>$_REQUEST["st"],
		'id_accion'=>7,
		'id_usuario'=>$_SESSION["user"]["idOperador"],
		'descripcion'=>$textLog,
		'privado'=>1,
	));

	$query->InsDB($conex,"site_ins_OrdenesServicioLogUSerRel",array($LastIdCom ,trim($_REQUEST["users"])));

	echo "ok";
}else if(isset($acc) && $acc=='load_recordatorios'){
	$paramRecord ="";
	$vFilasCo = array();

	if($_SESSION["user"]["id_perfil"]=="1" || $_SESSION["user"]["id_perfil"]=="2"){
		$paramRecord = $paramRecord . " AND (r.id_personal in (".$_REQUEST["user"]."))";
	}else{
		$paramRecord = $paramRecord." AND (r.id_personal = '".$_SESSION["user"]["idOperador"]."')";
	}

	$paramRecord = $paramRecord."AND (re.fecha_recordar >= CONVERT(datetime,'".datePrepareInput($_REQUEST["start"],"-")."', 103)) AND   (re.fecha_recordar <= DATEADD(dd, 1, CONVERT(datetime,'".datePrepareInput($_REQUEST["end"],"-")."', 103)))";

	$MyrecordData = $query->SelDB($conex,"site_sel_GetRecordatoriosUser",array($paramRecord));


	while($r=$query->getdata_object($MyrecordData)){
		unset($rAux);

		if($r->status ==  1){
			$colorBG = "#4C4C4C";
			$editable  = "false";
			$className = "tachar";
		}else{
			if(intval($r->diff) <= 4250){
				$colorBG = "#35aa47";
				$editable  = "true";
				$className = "";
			}else{
				$colorBG = "#d84a38";
				$editable  = "false";
				$className = "";
			}
		}

		$rAux->id = $r->id_recordatorio;
		$rAux->idRec = $r->id_recordatorio;
		$rAux->titleAux = $r->titulo;
		$rAux->description = $r->descripcion;
		$rAux->color = $colorBG;;
		$rAux->start = date_format($r->fecha_recordar,"Y-m-d h:i:s");;
		$rAux->fecha = date_format($r->fecha_recordar,"d-m-Y h:i:s a");;
		$rAux->objetivo = $r->id_objetivo;
		$rAux->id_status = $r->id_status;
		$rAux->tipo_cliente = $r->tipo_cliente;
		$rAux->tipo_orden = $r->tipo_orden;
		$rAux->title = $r->nombre_cliente;
		$rAux->local = $r->telf_local;
		$rAux->movil = $r->telf_movil;
		$rAux->editable = $editable;
		$rAux->startEditable = $editable;
		$rAux->diff = $r->diff;
		$rAux->className = $className;
		$rAux->durationEditable = false;
		$rAux->stattus_re = $r->status;

		$vFilasCo[] = $rAux;
	}


	echo json_encode($vFilasCo);
}else if(isset($acc) && $acc=='change_record'){
	$fechaRecord =  $_REQUEST["fec"]." ".$_REQUEST["time"];
	$fechaHum = $_REQUEST["fec"]." ".$_REQUEST["timeDesc"];
	$idOrden = $_REQUEST["idOrd"];

	$paramUdp = $paramUdp." fecha_recordar = CAST('".$fechaRecord."' AS smalldatetime) ";

	if($_REQUEST["tipo"] == 1){
		$accLog = 8;
		$caseText = "changeRecordatorio";
	}else{
		$accLog = 9;
		$caseText = "realizadoRecordatorio";
		$paramUdp = $paramUdp." , status=1";
	}

	$query->UpdDB($conex,"site_upd_changeFechaRecordatorio",array($paramUdp,$_REQUEST["id"]));

	//busca los usuarios asignados al recordatorio
	$group_user_rel = "";

	$MyRecordUserRel = $query->SelDB($conex,"site_sel_GetRecordatoriosUserRel",array($_REQUEST["id"]));
	$group_user = "";

	while($rU=$query->getdata_object($MyRecordUserRel)){
		if($rU->id_personal!="0" && $rU->id_personal!=""){
			$group_user = $group_user.",".$rU->id_personal;
		}

	}

	$group_user = ltrim($group_user,",");

	$textLog = getDetailLog($caseText,array(trim($_REQUEST["title"]),$fechaHum,trim($_REQUEST["motivo"])));

	$LastIdCom = $query->InsDB($conex,"site_ins_OrdenesServicioLogs",array(
		'id_orden'=>$idOrden,
		'id_status'=>$_REQUEST["st"],
		'id_accion'=>$accLog,
		'id_usuario'=>$_SESSION["user"]["idOperador"],
		'descripcion'=>$textLog,
		'privado'=>1,
	));

	if($group_user!=""){
		$query->InsDB($conex,"site_ins_OrdenesServicioLogUSerRel",array($LastIdCom ,trim($group_user)));
	}

	echo "ok";
}
?>