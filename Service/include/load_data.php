<?php
include(dirname(dirname(dirname(__FILE__)))."/include/scriptdb/config.php");
include(dirname(dirname(dirname(__FILE__)))."/include/scriptdb/querys.php");
include(dirname(dirname(dirname(__FILE__)))."/include/phpscript/generales.php");
include(dirname(dirname(dirname(__FILE__)))."/include/phpscript/sessionAjax.php");
include(dirname(dirname(dirname(__FILE__)))."/include/plugins/phpMailer/PHPMailerAutoload.php");

$query= new Querys();
$conex = $query->getConection($CONFIG);

include(dirname(dirname(__FILE__))."/include/phpscript/i_ManagerOrdServicio.php");
include(dirname(dirname(__FILE__))."/include/phpscript/i_ManagerNotificaciones.php");

$acc= $_REQUEST['dat'];

if(isset($acc) && $acc=='load_notifi'){

	include(dirname(dirname(__FILE__))."/include/diseno/i_header_count.php");

}else if(isset($acc) && $acc=='load_all_user'){
	$vFilas = array();

	$param3 = " AND ( nombre like '%".$_REQUEST["q"]."%' ) ";

	if($_REQUEST["per"] != ""){
		$param3 = $param3." AND  (id_perfil in(".$_REQUEST["per"].")) ";
	}

	//excluye un tecnico responsable
	if($_REQUEST["r"] != ""){
		$param3 = $param3." AND ( idPersonal<>'".$_REQUEST["r"]."' ) ";
	}

	$MyrecordData = $query->SelDB($conex,"site_sel_SearchUser",array($_SESSION["user"]["idEmpresa"],$param3));

	while($r=$query->getdata_object($MyrecordData)){
		unset($rAux);

		$rAux->id = $r->idPersonal;
		$rAux->text = $r->nombre;
		$vFilas[] = $rAux;
	}

	$response->aaData=$vFilas;

	echo json_encode($response);
}else if(isset($acc) && $acc=='load_client_ord'){
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
}
?>