<?php
include("../../include/scriptdb/config.php");
include("../../include/scriptdb/querys.php");
include("../../include/phpscript/generales.php");
include("../../include/phpscript/sessionAjaxCliente.php");

$acc= $_REQUEST['acc'];

$query= new Querys();
$conex = $query->getConection($CONFIG);

if(isset($acc) && $acc=='load_user'){
	$vFilas = array();
	$num_rows = 0;

	//agregar al query una busqueda
	if($_POST["sSearch"]!=""){
		$param = " and ((u.id_user like '%".$_POST["sSearch"]."%') or (u.nombre like '%".$_POST["sSearch"]."%')  or (u.email like '%".$_POST["sSearch"]."%') or (u.movil like '%".$_POST["sSearch"]."%') )";
	}

	//agregar el campo por el cual se ordena
	$Names = array("CASE WHEN ISNUMERIC(id_user) = 1 THEN 0 ELSE 1 END, CASE WHEN ISNUMERIC(id_user) = 1 THEN CAST(id_user AS INT) ELSE 0 END","nombre","email","movil");
	$order = " order by ".$Names[$_POST["iSortCol_0"]]." ".$_POST["sSortDir_0"];

	//contador
	$MyrecordDataCount = $query->SelDB($conex,"site_sel_AllUsuariosTotal",array($_GET["id"],$param));
	$num_rows = $query->count_row($MyrecordDataCount);

	$per_page =  $_POST["iDisplayLength"];
	$row_start  = 0;

	if(isset($_POST["iDisplayStart"])){
		$row_start = $_POST["iDisplayStart"];
	}

	$row_end = intval($row_start)+intval($per_page);

	$paramPag=" RowID > ".$row_start." AND  RowID <= ".$row_end." ";


	$MyrecordData = $query->SelDB($conex,"site_sel_AllUsuariosParam",array($_GET["id"],$param,$order,$paramPag));

	while($r=$query->getdata_object($MyrecordData)){
		unset($rAux);

		$color = "";

		//verifica su tiene eventos
		$countEvent = 0;

		$MyrecordContEv = $query->SelDB($conex,"site_sel_ClientesEventosDetalle",array($r->id_user,$_GET["id"]));
		if($query->count_row($MyrecordContEv)>0){
			$countEvent = 1;
		}

		if($r->status ==0 || empty($r->movil) || $countEvent == 0){
			$color = "#FF7979";
		}


		$rAux->id = $r->id_user;
		$rAux->nom = $r->nombre;
		$rAux->name = "";
		$rAux->ape = $r->apellido;
		$rAux->bbpin = $r->bbpin;
		$rAux->mail = $r->email;
		$rAux->movil = $r->movil;

		$dateAux = date_format($r->FechaAniversario,"d/m/Y");
		if($dateAux=="01/01/1900"){
			$dateAux ="";
		}

		$rAux->fech = $dateAux;
		$rAux->id_type = $r->id_type_user;
		$rAux->clavevoz = $r->clavevoz;
		$rAux->status = $r->status;
		$rAux->active_email = $r->active_email;
		$rAux->frec_email = $r->send_mail."-".$r->frecuencia_mail;
		$rAux->img = $r->imagen;
		$rAux->bg = $color;
		$rAux->parentesco = $r->descrip;

		$vFilas[] = $rAux;
	}
	$response->aaData=$vFilas;
	$response->sEcho = intval($_POST["sEcho"]);
	$response->iTotalRecords = $num_rows;
	$response->iTotalDisplayRecords = $num_rows;

	echo json_encode($response);

}

?>