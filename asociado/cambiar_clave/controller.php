<?php
include("../../include/scriptdb/config.php");
include("../../include/scriptdb/querys.php");
include("../../include/phpscript/generales.php");
include("../../include/phpscript/sessionAjax.php");

$query= new Querys();
$conex = $query->getConection($CONFIG);

if($_POST["acc"]=="change_clave"){
	if($_POST["a"]==decode64_asp($_SESSION["cliente"]["user_clave"])){
		if(strlen($_POST["cnew"])>0){

			$query->UpdDB($conex,"site_upd_CambioClaveAsociado",array($_POST["cnew"],$_POST["idAsociado"],$_SESSION["cliente"]["idEmpresa"]));

			$_SESSION["user"]["user_clave"] = encode64_asp($_POST['cnew']);

			exit("ok");
		}
	}else{
		exit("clave-error");
	}
}else{
	exit("error");
}
?>