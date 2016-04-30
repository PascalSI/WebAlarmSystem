<?php
include("../../include/scriptdb/config.php");
include("../../include/scriptdb/querys.php");
include("../../include/phpscript/generales.php");
include("../../include/phpscript/sessionAjaxCliente.php");
include("../../include/plugins/phpMailer/PHPMailerAutoload.php");
include("../../include/phpscript/init.php");

$acc= $_REQUEST['acc'];

$query= new Querys();
$conex = $query->getConection($CONFIG);

if(isset($acc) && $acc=='soporte'){


	$logo = '../../../img/logo_empresas/'.$CONFIG['LOGO_EXPORT'];

	$nameClient = $_POST["nombre_abonado"]=="" ? $_SESSION["cliente"]["nombre_cliente"] : $_POST["nombre_abonado"];

	$mail = new PHPMailer;

	//$mail->SMTPDebug = 3;                               // Enable verbose debug output

	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = $CONFIG['MAIL_HOST'];  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = $CONFIG['MAIL_USER'];                 // SMTP username
	$mail->Password = $CONFIG['MAIL_PASS'];                           // SMTP password
	$mail->SMTPSecure = $CONFIG['MAIL_SMTPSecure'];                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port = $CONFIG['MAIL_PORT'];                                    // TCP port to connect to

	$mail->From = $_SESSION["cliente"]["email_empresa"];
	$mail->FromName = $_SESSION["cliente"]["nombre_empresa"];
	$mail->addAddress($_POST["to"]);     // Add a recipient
	$mail->addBCC('jeangarcia2414@gmail.com',"Jean Garcia");

	$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
	$mail->isHTML(true);                                  // Set email format to HTML
	$mail->CharSet = 'UTF-8';

	$mail->Subject = "Soporte Cliente ".$nameClient;


	$body= "
	<!DOCTYPE html>
	<html>
	<head>
	<meta	http-equiv='Content-Type'	content='charset=utf-8' />

	<style>
		html,body{
			font-family:'Lucida Sans Unicode', 'Lucida Grande', sans-serif;
			font-size:14px;
			margin:20px;
		}

		table, th, td {
		   border: 1px solid black;
		   padding:2px;
		}
		tr {
			page-break-inside: avoid;
		}


	</style>
	</head>
	<body>
		<table border='0'>
			<tr>
				<td align='center'>Soporte en Linea</td>
			</tr>
			<tr>
				<table  border='0'>
					<tr>
						<td>Nombre:</td>
						<td>".$nameClient."</td>
					</tr>
					<tr>
						<td>Tel&eacute;fono:</td>
						<td>".$_POST["tel"]."</td>
					</tr>
					<tr>
						<td>Correo:</td>
						<td>".$_POST["email"]."</td>
					</tr>
					<tr>
						<td>Motivo:</td>
						<td>".$_POST["motivo"]."</td>
					</tr>
					<tr>
						<td>Comentario:</td>
						<td>".$_POST["coment"]."</td>
					</tr>
				</table>
			</tr>
		</table>";

	$htmlBody = returnHtmlEmail(array(
		"title"=>"Soporte Cliente ".$nameClient,
		"body"=>$body
		)
	);
	$mail->Body    = $htmlBody;

	if(!$mail->send()) {
	    echo 'Mailer Error: ' . $mail->ErrorInfo;
	} else {
	    echo 'send';
	}

}
?>