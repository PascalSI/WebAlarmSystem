<?php
header('Content-Type: text/html; charset=UTF-8');

/*Archivo de configuracionaaaaaaaaaaaaaaaa*/
ini_set('display_errors', '1');
error_reporting(E_ERROR | E_PARSE);


date_default_timezone_set('America/Caracas');
setlocale (LC_TIME,"spanish");

ini_set('max_execution_time', 300);

session_start();

$CONFIG['DIR_PATH'] = "4.0";//deje en blanco si desea colocar el proyecto en la reaiz
$CONFIG['HOST'] = "http://".$_SERVER['HTTP_HOST']."/";
$CONFIG['DIR_PROJECT'] = $_SERVER["DOCUMENT_ROOT"]."/";

if(trim($CONFIG['DIR_PATH'])!=""){
	$CONFIG['HOST'].=$CONFIG['DIR_PATH']."/";
}

if(trim($CONFIG['DIR_PATH'])!=""){
	$CONFIG['DIR_PROJECT'].=$CONFIG['DIR_PATH']."/";
}

$CONFIG['DIR_PROJECT_TEMP'] = $CONFIG['DIR_PROJECT']."include/temp/";
$CONFIG['PATH_IMG_CLIENT'] = $CONFIG['DIR_PROJECT']."img/img_c/";
$CONFIG['PATH_IMG_USER_CLIENT'] = $CONFIG['DIR_PROJECT']."img/img_uc/";
$CONFIG['PATH_IMG_ZONA_CLIENT'] = $CONFIG['DIR_PROJECT']."img/img_z/";
$CONFIG['PATH_IMG_PERSONAL'] = $CONFIG['DIR_PROJECT']."img/img_p/";
$CONFIG['PATH_IMG_VEHICULOS'] = $CONFIG['DIR_PROJECT']."img/img_v/";
$CONFIG['PATH_FILE_MANUAL'] = $CONFIG['DIR_PROJECT']."document/ma_alarm/";



//metadatos
$CONFIG['META_AUTOR'] = "365 Connect ";
$CONFIG['META_DESC'] = "Software de Monitoreo";

//base de Datos
$CONFIG['DB_HOST'] = "SQL5021.Smarterasp.net";//JEAN
$CONFIG['DB_CHEMA'] = "DB_9EED2C_SMSSecurePHP ";
$CONFIG['DB_USUARIO'] = "DB_9EED2C_SMSSecurePHP_admin";
$CONFIG['DB_PASS'] = "jermsoft1.";

//$CONFIG['DB_HOST'] = "JEAN";//JEAN
//$CONFIG['DB_CHEMA'] = "365DB";
//$CONFIG['DB_USUARIO'] = "";
//$CONFIG['DB_PASS'] = "";

//Datos Correos
$CONFIG['MAIL_HOST'] = "smtp.gmail.com";
$CONFIG['MAIL_USER'] = "365monitoreo@gmail.com";
$CONFIG['MAIL_PASS'] = "20101733";
$CONFIG['MAIL_SMTPSecure'] = 'tls';
$CONFIG['MAIL_PORT'] = '587';

//defualt_theme
$CONFIG['WEB_THEME'] = "grey";

//Default map
$CONFIG['MAP_LAT'] = "7,20";
$CONFIG['MAP_LOG'] = "-66,12";

//DATOS EXPORT
$CONFIG['LOGO_EXPORT'] = "logo-invert.png";
$CONFIG['LOGO_PAGES'] = "logo-invert.png";
$CONFIG['COLOR_HEAD'] = "#2d5f8b";

$CONFIG['WEB_THEME_LOGO'] = "logo.png";

//DEBUG
$CONFIG['DEBUG'] = true;


$CONFIG['msjCobranza']  = "#Empresa# su empresa de seguridad electronica le recueda que tiene una deuda pendiente de #monto#, favor ponerce al dia antes del #Fecha# ";
$CONFIG['NAME_APLICATION'] = "365Connect";
$CONFIG['MONEDA_SYSTEM'] = "Bs";


$CONFIG["array_stattus_icon"]["1"] = " glyphicon glyphicon-thumbs-down ";
$CONFIG["array_stattus_icon"]["2"] = " glyphicon glyphicon-thumbs-up ";
$CONFIG["array_stattus_icon"]["3"] = " glyphicon glyphicon-hand-right ";
$CONFIG["array_stattus_icon"]["4"] = " glyphicon glyphicon-chevron-down ";
$CONFIG["array_stattus_icon"]["5"] = " glyphicon glyphicon-remove-circle ";
$CONFIG["array_stattus_icon"]["6"] = " glyphicon glyphicon-time ";
$CONFIG["array_stattus_icon"]["7"] = " fa fa-file-text ";
$CONFIG["array_stattus_icon"]["8"] = "fa fa-money  ";

$CONFIG["VERSION"]= "2.28";

?>