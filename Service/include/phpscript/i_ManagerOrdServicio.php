<?php
function getDetailLog($acc,$args){
	$text = "";
	switch($acc){
		case "addOrd": // texto al crear orden de servicio
			$text = "Problema: ".$args[0];

			if($args[1] != ""){
				$text.="<br/>Pre-Observacion: ".$args[1];
			}
		break;

		case "2": //cuando se asigna un tecnico
			$text = " ".$args;
		break;

		case "3": //texto
			$text = " ".$args;
		break;

		case "msgAddTecnico":
			$text = "Se asigno el tecnico ".$args[1];
			if($args[0] != "1900/01/01"){
				$text = $text . ", para la fecha ".$args[0];
			}
			if($args[3] == 0 && $args[2]!=""){
				$text = $text . ", con la siguiente pre observacion : ".$args[2];
			}
		break;

		case "msgAddcoment":
			$text = " ". $args;
		break;

		case "msgAddFact":
			$text = " Se genero la Factura ".$args[1].", el dia ".$args[0]." con un monto Total de: ".$args[2];
			if(trim($args[3]) != ""){
				$text = $text . ", y la siguiente observacion : ".$args[3];
			}
		break;

		case "msgAddPago":
			$text = " Orden fue pagada el dia  ".$args[0].", con  ".$args[1]." ";
			if(trim($args[2]) != ""){
				$text = $text . "(Identificador: ".$args[2].")";
			}
			if(trim($args[3]) != ""){
				$text = $text . ", y la siguiente observacion : ".$args[3];
			}
		break;

		case "msgTecReasignado":
			$text = "Orden reasignada al tecnico ".$args[0];
		break;

		case "addRecordatorio":
			$text = "Para el dia: ".$args[0]." ";
			$text = $text . "<br/>Titulo : ".$args[1];
			$text = $text . "<br/>Descripcion : ".$args[2];
		break;

		case "changeRecordatorio":
			$text = "Se modifico la fecha de recordatorio: ".$args[0].", para el dia ".$args[1];
			$text = $text . "<br/> Por el siguiente motivo: ".$args[2];
		break;

		case "realizadoRecordatorio":
			$text = "Se realizo el recordatorio: ".$args[0];
			$text = $text & "<br/> Con la Observacion: ".$args[2];
		break;

		case "msgAddcomentDate":
			$text = " (".$args[0].") ".$args[1];
		break;

		case "msgAsignarOrdenCalendarTecnico":
			$text = "Se asigo la Fecha de Atencion: ".$args["fecha"];
		break;

		default:
			$text = "Accion no reconocida";
		break;

	}
	return $text;
}
?>