<?php
$arrayHTMLEmail = array(
	"newEditOrden"=>'<!doctype html> <html> <head> <meta charset="utf-8"> <title>Documento sin título</title> </head> <body> <p>Estimado Usuario(a): {Aplicacion} le informa que se {5} la orden de Servicio #{0} con lo siguientes datos:</p> <table width="100%" border="0"> <tbody> <tr> <td width="20%" align="right" valign="top"><b>Nombre Cliente:</b></td> <td width="80%" valign="top">{1}</td> </tr> <tr> <td  align="right" valign="top"><b>Problema:</b></td> <td valign="top">{2}</td> </tr> <tr> <td  align="right" valign="top"><b>Fecha de Atencion:</b></td> <td valign="top">{3}</td> </tr> <tr> <td  align="right" valign="top"><b>Tecnico Asignado</b>:</td> <td valign="top">{4}</td> </tr> </tbody> </table> <p>&nbsp;</p> </body> </html>',

	"commitOrden"=>'<!doctype html><html><head><meta charset="utf-8"><title>Documento sin título</title></head><body><p>Estimado Usuario(a): {Aplicacion} le informa que {5} la orden de Servicio #{0} :</p><table width="100%" border="0"> <tbody> <tr> <td width="20%" align="right" valign="top"><b>Nombre Cliente:</b></td> <td width="80%" valign="top">{1}</td> </tr> <tr> <td  align="right" valign="top"><b>Problema:</b></td> <td valign="top">{2}</td> </tr> <tr>
      <td  align="right" valign="top"><b>Estatus:</b></td> <td valign="top">{3}</td> </tr> <tr>  <td  align="right" valign="top"><b>Comentario</b>:</td> <td valign="top">{4}</td> </tr> </tbody> </table> <p>&nbsp;</p> </body></html>'

);

$arrayTextSMSCliente = array(
	"Status-1"=>"{0} Informa Orden de Servicio Numero: {2} de {1} Estatus: {3}",
	"Status-2"=>"{0} Informa Orden de Servicio Numero: {2} de {1}  Estatus: {3} {4} {5}",
	"Status-REST"=>"{0} Informa Orden de Servicio Numero: {2} de {1}  Estatus: {3}"
);

$arrayTextEMAILCliente = array(
	"Status-1"=>"Estimado Cliente {0}, {1} le informa que se ha procesado la Orden de Servicio  que a continuación detallamos <br/><br/> Numero de Orden: {2} <br/><br/> Fecha de la orden: {3} <br/><br/> Fecha Atención: Sin Fecha de Atencion <br/><br/> Descripción de La orden: {4} <br/><br/> Status de La orden: <b>{5}</b>  ",
	"Status-2"=>"Estimado Cliente {0}, {1} le informa que se ha procesado la Orden de Servicio  que a continuación detallamos <br/><br/> Numero de Orden: {2} <br/><br/> Fecha de la orden: {3} <br/><br/> Fecha Atención: {4} <br/><br/>Descripción de La orden: {5} <br/><br/> Status de La orden: <b>{6} {7}</b>",
	"Status-REST"=>"Estimado Cliente {0}, {1} le informa que se ha procesado la Orden de Servicio  que a continuación detallamos <br/><br/> Numero de Orden: {2} <br/><br/> Fecha de la orden: {3} <br/><br/> Fecha Atención: {4} <br/><br/> Fecha {8}: {5} <br/><br/> Descripción de La orden: {6} <br/><br/> Comentarios: {7} <br/><br/> Status de La orden: <b>{8}</b>  "
);


//p = particularidad ; 0 = public , 1 = privado


//genera una notificacion
function generarNoticacion($idOrd,$autor,$tipo,$p,$destinos,$objetivo,$op,$userRel){
	global $conex,$query;

	if($p == 1){ //es privada la notificacion generada
		$destinoAux = $userRel.",".$autor;
	}else{ //es publica
		$destinoAux = $destinos;
	}

	$userDestino = load_UserNotifi($autor,$destinoAux,$p);

	//inserta las notificaiones sin ver para los usuarios'
	if($userDestino["user"]!=""){
		$query->InsDB($conex,"site_ins_OrdenesServicioNotificacion",array($userDestino["user"],clearString($autor),clearString($objetivo),clearString($tipo),0));
	}

	//inserta a los usuarios relacionados explicitamente en el log generado'
	if($userRel !=""){
		$query->InsDB($conex,"site_ins_OrdenesServicioLogUSerRel",array($objetivo,$userRel.",".$autor));
	}

	//se envia notificacion a los usuarios seleccionados'
	if($userDestino["contCorreoAct"]>0){
		getSendNotifi($tipo,array(
			"idOrd"=>$idOrd,
			"destino"=>$userDestino,
			"objtivo"=>$objetivo,
			"op"=>$op));
	}

}


//busca todos los usuarios para generar una noticacion
function load_UserNotifi($autor,$destinos,$part){
	global $conex,$query;

	$group_user = "";
	$group_userNom = "";
	$group_userCorreo ="";
	$group_userCorreoValid ="";
	$group_userCorreoCont = 0;

	$partTipo = "AND (";

	//filtra la empresa
	$paramUserd = " AND (id_empresa='".$_SESSION["user"]["idEmpresa"]."')";

	//si se desea buscar por un usuario especifico
	if($destinos != "0" && trim($destinos) != ""){
		$paramUserd = $paramUserd." AND  (idPersonal IN (".$destinos.") ";
		$partTipo = " or ";
	}

	if($part == 0){
		//busca por tipo de usuario
		$paramUserd = $paramUserd.$partTipo."  notifi_serv_tec = 1";
	}

	$paramUserd= $paramUserd." ) ";

	//extrae al autor
	$paramUserd = $paramUserd." AND  (idPersonal !=".$autor." ) ";

	$MyRecordUSer = $query->SelDB($conex,"site_sel_GetUSerNotificaciones",array($paramUserd));

	while($rU=$query->getdata_object($MyRecordUSer)){
		$group_user = $group_user.",".$rU->idPersonal;
		$group_userNom =  $group_userNom.",".$rU->nombre;
		$group_userCorreo =  $group_userCorreo.",".$rU->correo;
		$group_userCorreoValid =  $group_userCorreoValid.",".$rU->notifi_email_servicio;
		if(trim($rU->notifi_email_servicio)=="1"){
			$group_userCorreoCont++;
		}
	}

	$respuexta = array(
		"user"=>ltrim($group_user,","),
		"userNom"=>ltrim($group_userNom,","),
		"userCorreo"=>ltrim($group_userCorreo,","),
		"userCorreoValid"=>ltrim($group_userCorreoValid,","),
		"contCorreoAct"=>$group_userCorreoCont
	);

	return $respuexta;
}

//obtiene el texto de la nofiticacion
function getSendNotifi($accN,$infoSend){

	global $conex,$query,$arrayHTMLEmail,$CONFIG;

	$txtBody="";
	$dataParam = "";
	$NameArchiLoad = "";

	switch($accN){
		case 1:
			$NameArchiLoad = $arrayHTMLEmail["newEditOrden"];
			$MyRecordDat = $query->SelDB($conex,"site_sel_GetDatOrdBasic",array($infoSend["objtivo"],$infoSend["idOrd"]));
			$rD=$query->getdata_object($MyRecordDat);

			$fechatencionAux = date_format($rD->fechaAtencion,"d/m/Y");
			if($fechatencionAux=="01/01/1900"){
				$fechatencionAux = "Sin Fecha de Atencion";
			}

			$tecnico = "Sin Tecnico Asignado";
			if(trim($rD->tecnico)!=""){
				$tecnico = $rD->tecnico;
			}

			$data = array($rD->correlativo,$rD->cliente,$rD->problema,$fechatencionAux,$tecnico,"creo");

			$titleMail = "Nueva Orden de Servicio #".$rD->correlativo;
		break;

		case 3:
			$NameArchiLoad = $arrayHTMLEmail["newEditOrden"];

			$MyRecordDat = $query->SelDB($conex,"site_sel_GetDatOrdBasic",array($infoSend["objtivo"],$infoSend["idOrd"]));
			$rD=$query->getdata_object($MyRecordDat);

			$fechatencionAux = date_format($rD->fechaAtencion,"d/m/Y");
			if($fechatencionAux=="01/01/1900"){
				$fechatencionAux = "Sin Fecha de Atencion";
			}

			$tecnico = "Sin Tecnico Asignado";
			if(trim($rD->tecnico)!=""){
				$tecnico = $rD->tecnico;
			}

			$data = array($rD->correlativo,$rD->cliente,$rD->problema,$fechatencionAux,$tecnico,"edito");

			$titleMail = "Edicion Orden de Servicio #".$rD->correlativo;
		break;

		case 4:
			$NameArchiLoad = $arrayHTMLEmail["commitOrden"];
			$MyRecordDat = $query->SelDB($conex,"site_sel_GetDatosStatus",array($infoSend["objtivo"],$infoSend["idOrd"]));
			$rD=$query->getdata_object($MyRecordDat);

			$data = array($rD->correlativo,$rD->cliente,$rD->problema,$rD->sts,$rD->coment,"se cambio el estatus de");

			$titleMail = "Cambio de estatus de la  Orden  #".$rD->correlativo;
		break;

		case 5:
			$NameArchiLoad = $arrayHTMLEmail["commitOrden"];
			$MyRecordDat = $query->SelDB($conex,"site_sel_GetDatosStatus",array($infoSend["objtivo"],$infoSend["idOrd"]));
			$rD=$query->getdata_object($MyRecordDat);

			$data = array($rD->correlativo,$rD->cliente,$rD->problema,$rD->sts,$rD->coment,"se agrego comentarios a ");

			$titleMail = "Se agrego comentario la  Orden  #".$rD->correlativo;
		break;
	}

	if(is_array($data)){
		$contData = count($data);

		$txtBody = $NameArchiLoad;

		for($l=0;$l<$contData;$l++){
 			$txtBody = str_replace("{".$l."}",$data[$l],$txtBody);
		}
	}
	$txtBody = str_replace("{Aplicacion}",$CONFIG['NAME_APLICATION'],$txtBody);

	//valida que el personal tenga el correo activo par enviar notificacion
	$correox = explode(",",$infoSend["destino"]["userCorreo"]);
	$correoAct = explode(",",$infoSend["destino"]["userCorreoValid"]);
	$countCAux = count($correox);
	$too = "";
	for ($v=0; $v < $countCAux; $v++) {
		if($correoAct[$v]=="1"){
			$too.= ",".$correox[$v];
		}
	}
	$too =  array('','',ltrim($too,","));

	sendMailNotification($titleMail,$too,$txtBody);

}

function sendMailNotification($titleMail,$too,$txtBody,$op=array()){
	global $CONFIG,$conex,$query;

	$htmlBody = returnHtmlEmail(array(
		"title"=>$titleMail,
		"body"=>$txtBody
		)
	);
	//$nameTo = explode(",", $too[1]);
	$emailTo = explode(",", $too[2]);
	$countMail = count($emailTo);

	for($e=0;$e<$countMail;$e++){
		$query->InsDB($conex,"site_ins_EMAILSalida",array(
			"id_cliente"=>0,
			"email"=>$emailTo[$e],
			"asunto"=>$titleMail,
			"mensaje"=>$htmlBody
		));
	}

	/*$mail = new PHPMailer;

	//$mail->SMTPDebug = 3;                               // Enable verbose debug output

	$mail->isSMTP();                                      // Set mailer to use SMTP

	$mail->Host = $CONFIG['MAIL_HOST'];  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = $CONFIG['MAIL_USER'];                 // SMTP username
	$mail->Password = $CONFIG['MAIL_PASS'];                           // SMTP password
	$mail->SMTPSecure = $CONFIG['MAIL_SMTPSecure'];                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port = $CONFIG['MAIL_PORT'];                                    // TCP port to connect to

	if(isset($op["emailEmpresa"])){
		$emailEmpresa = trim($op["emailEmpresa"]);
	}else{
		$emailEmpresa = $_SESSION["user"]["correo_empresa"];
	}

	if(isset($op["nameEmpresa"])){
		$nameEmpresa = trim($op["nameEmpresa"]);
	}else{
		$nameEmpresa = $_SESSION["user"]["nombre_empresa"];
	}

	$mail->From = $emailEmpresa;
	$mail->FromName = $nameEmpresa;

	$nameTo = explode(",", $too[1]);
	$emailTo = explode(",", $too[2]);
	$countMail = count($emailTo);

	for($e=0;$e<$countMail;$e++){
		$mail->addBCC($emailTo[$e],$nameTo[$e]);     // Add a recipient
	}

	$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
	$mail->isHTML(true);                                  // Set email format to HTML
	$mail->CharSet = 'UTF-8';

	$mail->Subject = $titleMail;

	$htmlBody = returnHtmlEmail(array(
		"title"=>$titleMail,
		"body"=>$txtBody
		)
	);
	$mail->Body    = $htmlBody;

	if(!$mail->send()) {
	    return  'Mailer Error: ' . $mail->ErrorInfo;
	} else {
	    return 'send';
	}*/
}


function userRelacionadosOrd($idx){
	global $CONFIG,$conex,$query;
	$group_user_rel = "";

	$MyRecordUserRel = $query->SelDB($conex,"site_sel_GetUserRelOrd",array($idx));

	while($r=$query->getdata_object($MyRecordUserRel)){
		if($r->id!="0" and trim($r->id)!=""){
			$group_user_rel = $group_user_rel.",".$r->id;
		}
	}

	if($group_user_rel!=""){
		$group_user_rel = ltrim($group_user_rel, ",");
	}

	return $group_user_rel;
}

function namesAtencion($idx,$idAutor){
	global $CONFIG,$conex,$query;
	$group_user_rel_name = "";

	$MyRecordUserRelDat = $query->SelDB($conex,"site_sel_GetUSerRelData",array($idx,$idAutor));

	while($r=$query->getdata_object($MyRecordUserRelDat)){
		if(trim($r->nombre)!=""){
			$group_user_rel_name = $group_user_rel_name.",".$r->nombre;
		}
	}

	if($group_user_rel_name != ""){
		$group_user_rel_name = ltrim($group_user_rel_name,",");
	}

	return $group_user_rel_name;
}


function generarNoticacionCliente($data){
	global $query,$conex,$CONFIG,$arrayTextSMSCliente,$arrayTextEMAILCliente;

	//valida si la empresa tiene activa notificaciones para los clientes
	$validNotifClient = validateNotifiSrvEmpresaClient($data["empresaCl"]);

	if($validNotifClient["sms"]=="0" && $validNotifClient["email"]=="0"){
		return false;
	}

	switch($data["status"]){
		case 1:
			$MyrecordDat = $query->SelDB($conex,"site_sel_GetDataNotifiClientStatus",array($data["idOrd"]));
			$rDat=$query->getdata_object($MyrecordDat);

			//datos para sms
			$dataTXT =  array($rDat->nombre,$rDat->nombre_cliente,"#".$rDat->correlativo,$rDat->descripcion);
			$TextSMS = $arrayTextSMSCliente["Status-1"];

			//datos para correo
			$fechaCreada = date_format($rDat->fechaCreada,"d/m/Y h:i a");
			$dataTXTEMAIL =  array($rDat->nombre_cliente,$rDat->nombre,"#".$rDat->correlativo,$fechaCreada,$rDat->problema,$rDat->descripcion);
			$TextEMAIL = $arrayTextEMAILCliente["Status-1"];

			//Datos Cliente
			$idcliente = $rDat->id_cliente;
			$movil = $rDat->telf_movil;
			$email = trim($rDat->email);
			$emailEmp = $rDat->correoEmp;
			$webEmp = trim($rDat->web);
		break;

		case 2:
			$MyrecordDat = $query->SelDB($conex,"site_sel_GetDataNotifiClientStatusTecnico",array($data["idOrd"]));
			$rDat=$query->getdata_object($MyrecordDat);

			$fechAtenAux = date_format($rDat->fechaAtencion,"d/m/Y");
			if($fechAtenAux=="01/01/1900"){
				$fechAtenAux = date("d/m/Y");
			}

			//datos para sms
			$dataTXT =  array($rDat->nombre,$rDat->nombre_cliente,"#".$rDat->correlativo,$rDat->descripcion,$rDat->tecnico,$fechAtenAux);
			$TextSMS = $arrayTextSMSCliente["Status-2"];

			//datos para correo
			$fechaCreada = date_format($rDat->fechaCreada,"d/m/Y h:i a");

			$fechAtenAuxE = date_format($rDat->fechaAtencion,"d/m/Y");
			if($fechAtenAuxE=="01/01/1900"){
				$fechAtenAuxE = date("d/m/Y h:i a");
			}else{
				$fechAtenAuxE = date_format($rDat->fechaAtencion,"d/m/Y h:i a");
			}

			$dataTXTEMAIL =  array($rDat->nombre_cliente,$rDat->nombre,"#".$rDat->correlativo,$fechaCreada,$fechAtenAuxE,$rDat->problema,$rDat->descripcion,$rDat->tecnico);
			$TextEMAIL = $arrayTextEMAILCliente["Status-2"];

			$idcliente = $rDat->id_cliente;
			$movil = $rDat->telf_movil;
			$email = trim($rDat->email);
			$emailEmp = $rDat->correoEmp;
			$webEmp = trim($rDat->web);
		break;

		default:
			$MyrecordDat = $query->SelDB($conex,"site_sel_GetDataNotifiClientStatusComentario",array($data["idOrd"]));
			$rDat=$query->getdata_object($MyrecordDat);

			//datos sms
			$dataTXT =  array($rDat->nombre,$rDat->nombre_cliente,"#".$rDat->correlativo,$rDat->descripcion);
			$TextSMS = $arrayTextSMSCliente["Status-REST"];

			//datos para correo
			$fechaCreada = date_format($rDat->fechaCreada,"d/m/Y h:i a");

			$fechAtenAuxE = date_format($rDat->fechaAtencion,"d/m/Y");
			if($fechAtenAuxE=="01/01/1900"){
				$fechAtenAuxE = "Sin Fecha de Atencion";
			}else{
				$fechAtenAuxE = date_format($rDat->fechaAtencion,"d/m/Y h:i a");
			}

			$fechaStatus = date_format($rDat->fechaStatus,"d/m/Y h:i a");
			$comentario = trim($rDat->comentario);

			$dataTXTEMAIL =  array($rDat->nombre_cliente,$rDat->nombre,"#".$rDat->correlativo,$fechaCreada,$fechAtenAuxE,$fechaStatus,$rDat->problema,$comentario,$rDat->descripcion);
			$TextEMAIL = $arrayTextEMAILCliente["Status-REST"];

			$idcliente = $rDat->id_cliente;
			$movil = $rDat->telf_movil;
			$email = trim($rDat->email);
			$emailEmp = $rDat->correoEmp;
			$webEmp = trim($rDat->web);
		break;
	}

	//envia sms al cliente
	if($validNotifClient["sms"]=="1"){
		//replaza variables
		if(is_array($dataTXT)){
			$contData = count($dataTXT);

			$txtBody = $TextSMS;
			for($l=0;$l<$contData;$l++){
	 			$txtBody = str_replace("{".$l."}",$dataTXT[$l],$txtBody);
			}
		}


		$query->InsDB($conex,"site_ins_SMSSalida",array(
			"id_cliente"=>$idcliente,
			"movil"=>$movil,
			"sms"=>$txtBody
		));
	}

	//envia correo al cliente
	if($validNotifClient["email"]=="1" && $email!=""){
		//replaza variables
		if(is_array($dataTXTEMAIL)){
			$contData = count($dataTXTEMAIL);

			$txtBody = $TextEMAIL;
			for($l=0;$l<$contData;$l++){
	 			$txtBody = str_replace("{".$l."}",$dataTXTEMAIL[$l],$txtBody);
			}
		}

		if(trim($txtBody)!="" && $webEmp!=""){
			$txtBody.="<br/><br/> Al mismo tiempo lo invitamos a  visitar nuestra página ".$webEmp.", donde  encontrara todos nuestros productos y Servicios.";
		}


		$titleAuxMail =" Orden de Servicio Tecnico ".$dataTXTEMAIL[2];
		$opAuxMail = array("emailEmpresa"=>$emailEmp,"nameEmpresa"=>$dataTXTEMAIL[1]);
		$tooAuxMail = array(
			1=>$dataTXTEMAIL[0],
			2=>$email
		);

		sendMailNotification($titleAuxMail,$tooAuxMail,$txtBody,$opAuxMail);
	}
}


function validateNotifiSrvEmpresaClient($empresa){
	if($empresa==$_SESSION["user"]["idEmpresa"]){
		return array(
			"sms"=>trim($_SESSION["user"]["notif_sms_servicio"]),
			"email"=>trim($_SESSION["user"]["notifi_email_servicio"]),
		);
	}else{
		global $query,$conex,$CONFIG;

		$MyrecordE = $query->SelDB($conex,"site_sel_EmpresasVerifi","and id_empresa='".$empresa."'");
		$rE=$query->getdata_object($MyrecordE);

		return array(
			"sms"=>trim($rE->notif_sms_servicio),
			"email"=>trim($rE->notifi_email_servicio),
		);
	}
}

function sendSMSTecnicoOrdenAsig($data){
	global $query,$conex,$CONFIG;

	$MyrecordDat = $query->SelDB($conex,"site_sel_GetDataNotifiTecnicoOrden",array($data["idOrd"]));
	$rDat=$query->getdata_object($MyrecordDat);

	if($query->count_row($MyrecordDat)==0){ return false;}
	if($rDat->notif_sms_servicio=="0" && $rDat->notifi_email_servicio=="0"){ return false; }

	if($rDat->notif_sms_servicio=="1"){
		$textSMS = "Orden:#".trim($rDat->correlativo).",".trim($rDat->prefijo)."-".trim($rDat->cuenta)." ".trim($rDat->nombre_cliente).": ".trim($rDat->problema).",";

		$latitud = trim($rDat->latitud);
		$longitud = trim($rDat->longitud);

		if($latitud=="0" && $longitud=="0"){
			$textSMS.=trim($rDat->direccion);
		}else{
			$new_url = get_tiny_url('https://maps.google.com/maps?q='.$latitud.',+'.$longitud);
			$textSMS.=$new_url;
		}

		$query->InsDB($conex,"site_ins_SMSSalida",array(
			"id_cliente"=>0,
			"movil"=>trim($rDat->celTecnico),
			"sms"=>$textSMS
		));
	}
}

?>