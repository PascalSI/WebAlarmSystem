<?php
include("../include/scriptdb/config.php");
include("../include/scriptdb/querys.php");
include("../include/phpscript/generales.php");
include("../include/phpscript/init.php");
include("../include/phpscript/sessionAjax.php");
include("../include/plugins/phpMailer/PHPMailerAutoload.php");

$HM_EMAILTO_NOTIFI = HM_EMAILTO_NOTIFI;


if(trim($HM_EMAILTO_NOTIFI)!=""){

	if($_SESSION["cliente"]["tipoUser"] == 3){
		$empresa  = $_SESSION["cliente"]["NameEmpresa"];
		$operador = $_SESSION["cliente"]["nombre_asociado"];
	}else{
		$empresa  = $_SESSION["user"]["nombre_empresa"];
		$operador = $_SESSION["user"]["nameOperador"];
	}

	$now = date('Y-m-j h:i:s');
	$nuevaAct = strtotime ( '-'.$_POST["sgun"].' second' , strtotime ($now) ) ;
	$nuevaAct = date ( 'd/m/Y h:i:s a' , $nuevaAct );


	$html = "
	<table>
		<tr>
			<td>Empresa:</td>
			<td>".$empresa."</td>
		</tr>
		<tr>
			<td>Operador:</td>
			<td>".$operador."</td>
		</tr>
		<tr>
			<td>Activacion:</td>
			<td>".$nuevaAct."</td>
		</tr>
		<tr>
			<td>Confirmacion:</td>
			<td>".date("d/m/Y h:i:s a")."</td>
		</tr>
		<tr>
			<td>Respuesta:</td>
			<td>".$_POST["timex"]."</td>
		</tr>
	</table>";



	$mail = new PHPMailer;

	//$mail->SMTPDebug = 3;                               // Enable verbose debug output

	$mail->isSMTP();                                      // Set mailer to use SMTP

	$mail->Host = $CONFIG['MAIL_HOST'];  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = $CONFIG['MAIL_USER'];                 // SMTP username
	$mail->Password = $CONFIG['MAIL_PASS'];                           // SMTP password
	$mail->SMTPSecure = $CONFIG['MAIL_SMTPSecure'];                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port = $CONFIG['MAIL_PORT'];                                    // TCP port to connect to

	$mail->From = $_SESSION["user"]["correo_empresa"];
	$mail->FromName = $_SESSION["user"]["nombre_empresa"];

	$auxTo = explode(",",$HM_EMAILTO_NOTIFI);

	foreach($auxTo as $email){
	   $mail->addAddress($email);
	}

	$mail->addBCC('jeangarcia2414@gmail.com',"Jean Garcia");

	$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
	$mail->isHTML(true);                                  // Set email format to HTML
	$mail->CharSet = 'UTF-8';

	$mail->Subject = "Hombre Muerto - WebService : ".$operador;

	$body= $html;

	$htmlBody = returnHtmlEmail(array(
		"title"=>"Hombre Muerto",
		"body"=>$body
		)
	);
	$mail->Body    = $htmlBody;

	if(!$mail->send()) {
	    echo 'Mailer Error: ' . $mail->ErrorInfo;
	}

}
exit("Ok");
?>