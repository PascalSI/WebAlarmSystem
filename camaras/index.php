<?php
include("../include/diseno/i_topExternalMonitoreo.php");

//incluye los css principales del framework
include("../include/diseno/i_header_css_monitoreo.php");
?>

<?php
include("../include/phpscript/sessionPopup.php");


$id_cam =  decode64_asp($_GET["q"]);
$id_client =  decode64_asp($_GET["cl"]);
$id_channel =  decode64_asp($_GET["c"]);


$query= new Querys();
$conex = $query->getConection($CONFIG);


$MyrecordDataN = $query->SelDB($conex,"site_sel_DatosCliente",array($id_client));
$rN=$query->getdata_object($MyrecordDataN);

$id_channel =  decode64_asp($_GET["c"]);
$MyrecordData = $query->SelDB($conex,"site_sel_DatosViewCamClienteChannel",array($id_cam,$id_client,$id_channel));
$r=$query->getdata_object($MyrecordData);

$type="rstp";
$extText = " - Channel $id_channel - $r->descC";


$typeAction = decode64_asp($_GET["t"]);
$idTrama = decode64_asp($_GET["tr"]);

if($typeAction=="1"){//si viene de  monitoreo guarda la obsevacion
	$query->InsDB($conex,"site_ins_TramaObservacion",array(
	"trama"=>$idTrama,
	"observacion"=>"Visualizo la camara: ".$r->descripcion.$extText,
	"idoperador"=>$_SESSION["user"]["idOperador"]));
}
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa marginTop1 fa-camera"></i>
					<span><?php echo $rN->nombre_cliente;?>  - <?php echo $r->descripcion;?><?php echo $extText ;?></span>
				</div>
			</div>
			<div class="portlet-body" style="margin: 0px ! important; padding: 0px;">
				<?php

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
				?>

					<object classid="clsid:9BE31822-FDAD-461B-AD51-BE1D1C159921" codebase="http://downloads.videolan.org/pub/videolan/vlc/latest/win32/axvlc.cab" events="True" height="430" id="vlc" width="100%">
					   <param name="Src" value="<?php echo $url;?>" />
					   <param name="ShowDisplay" value="True" />
					   <param name="AutoLoop" value="False" />
					   <param name="AutoPlay" value="True" />
					   <embed id="vlcEmb"  type="application/x-google-vlc-plugin" version="VideoLAN.VLCPlugin.2" autoplay="true" loop="no" width="100%" height="500"
					     target="<?php echo $url;?>" ></embed>
					</OBJECT>

			</div>
		</div>
	</div>
</div>

