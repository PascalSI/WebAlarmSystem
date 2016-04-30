<?php

include("../../../include/scriptdb/config.php");
include("../../../include/scriptdb/querys.php");
include("../../../include/phpscript/generales.php");
include("../../../include/phpscript/sessionAjax.php");



$acc= $_REQUEST['acc'];

$query= new Querys();
$conex = $query->getConection($CONFIG);


if(isset($acc) && $acc=='load_notas'){

	$rAux->fija="";
	$rAux->temp="";
	$rAux->ini="";
	$rAux->fin="";

	$MyrecordData = $query->SelDB($conex,"site_sel_NotasClientes",array($_POST["idcliente"]));
	if($query->count_row($MyrecordData)){

		$r=$query->getdata_object($MyrecordData);

		$rAux->fija=$r->NotaFija;
		if(trim($r->NotaTemp)!=""){
			$rAux->temp=$r->NotaTemp;
			$rAux->ini=date_format($r->FechaIni,"d/m/Y");
			$rAux->fin=date_format($r->FechaFin,"d/m/Y");
		}

	}

	echo json_encode($rAux);
}else if(isset($acc) && $acc=='save_fija'){

	$MyrecordData = $query->SelDB($conex,"site_sel_NotasClientes",array($_POST["idcliente"]));

	if($query->count_row($MyrecordData)){
		$query->UpdDB($conex,"site_upd_ClienteNotaFija",array($_POST["text"],$_POST["idcliente"]));
	}else{
		$query->InsDB($conex,"site_ins_NotasClientesFija",array($_POST["idcliente"],$_POST["text"]));
	}

	RegLog(array($_SESSION["user"]["idOperador"],$_POST["idcliente"],1,71,mid($_POST["text"],200)));

	echo "ok";

}else if(isset($acc) && $acc=='save_temp'){

	$d = " CAST('".datePrepareBD($_POST["d"])." 12:00:00' AS smalldatetime)";
	$h = " CAST('".datePrepareBD($_POST["h"])." 12:00:00' AS smalldatetime)";

	$MyrecordData = $query->SelDB($conex,"site_sel_NotasClientes",array($_POST["idcliente"]));

	if($query->count_row($MyrecordData)){
		$query->UpdDB($conex,"site_upd_ClienteNotaTemporal",array($_POST["text"],$d,$h,$_POST["idcliente"]));
	}else{
		$query->InsDB($conex,"site_ins_NotasClientesFija",array($_POST["idcliente"],$_POST["text"]));
	}

	//registra log'
	$textLogNot = "Fechas: ".$_POST["d"]." - ".$_POST["h"]." , ".$_POST["text"];
	RegLog(array($_SESSION["user"]["idOperador"],$_POST["idcliente"],1,72,mid($textLogNot,200)));

	echo "ok";

}
?>