<?php
include("../include/scriptdb/config.php");
include("../include/scriptdb/querys.php");
include("../include/phpscript/session.php");
include("../include/phpscript/init.php");
include("../include/phpscript/generales.php");
include("../include/phpscript/sessionPopup.php");


$id_cam =  decode64_asp($_GET["q"]);
$id_client =  decode64_asp($_GET["cl"]);
$id_channel =  decode64_asp($_GET["c"]);


$query= new Querys();
$conex = $query->getConection($CONFIG);


$id_channel =  decode64_asp($_GET["c"]);
$MyrecordData = $query->SelDB($conex,"site_sel_DatosViewCamClienteChannel",array($id_cam,$id_client,$id_channel));
$r=$query->getdata_object($MyrecordData);

$type="rstp";


$url = $r->string_acceso;

$url = str_replace("#ip#",$r->ip,$url);
$url = str_replace("#puerto#",$r->puerto,$url);
$url = str_replace("#channel#",$id_channel,$url);
$url = str_replace("#user#",$r->usuario,$url);
$url = str_replace("#clave#",$r->clave,$url);

$parsed = get_string_between($url, "[encode]", "[/encode]");
$fn = get_string_between($parsed, "[fn]", "[/fn]");
$val = get_string_between($parsed, "[val]", "[/val]");

$decodeAux = call_user_func_array($fn,array($val));

$start = '\[encode]';
$end = '\[/encode]';
$url = preg_replace('#'.$start.'.*?'.$end.'#s', '$1'.$decodeAux.'$3',$url);

$typeAction = decode64_asp($_GET["t"]);
$idTrama = decode64_asp($_GET["tr"]);

if($typeAction=="1"){
	$query->InsDB($conex,"site_ins_TramaObservacion",array(
	"trama"=>$idTrama,
	"observacion"=>"Se ingreso a la camara: ".decode64_asp($_GET["namex"]),
	"idoperador"=>$_SESSION["user"]["idOperador"]));
}
?>

<body style="margin:0px !important">
	<object classid="clsid:9BE31822-FDAD-461B-AD51-BE1D1C159921" codebase="http://downloads.videolan.org/pub/videolan/vlc/latest/win32/axvlc.cab" events="True"  width="100%" height="100%">
   <param name="Src" value="<?php echo $url;?>" />
   <param name="ShowDisplay" value="True" />
   <param name="AutoLoop" value="False" />
   <param name="AutoPlay" value="True" />
   <embed id="vlcEmb"  type="application/x-google-vlc-plugin" version="VideoLAN.VLCPlugin.2" autoplay="true" loop="no"
     target="<?php echo $url;?>" width="100%"  height="100%"></embed>
	</OBJECT>
</body>

