<?php
	include("../include/scriptdb/config.php");
    include("../include/phpscript/init.php");
    include("../include/phpscript/generales.php");
    include("../include/scriptdb/querys.php");

	if($_SESSION["cliente"]["idEmpresa"]=="" || $_SESSION["cliente"]["tipoUser"]==""){
		header("LOCATION: ../");
	}

    if($_SESSION["cliente"]["tipoUser"]==4){
        $idCliente =  $_SESSION["cliente"]["idAbonado"];
    }else if($_SESSION["cliente"]["tipoUser"]==3){
        $idCliente =  decode64_asp($_GET["id"]);
    }else{
       header("LOCATION: ../");
    }

	//incluye los metadatas
	include("../include/diseno/i_header_metada.php");

	//incluye los css principales del framework
	include("../include/diseno/i_header_css_default.php");
?>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places"></script>

<title><?php echo $_SESSION["user"]["nombre_empresa"];?> Control Panel</title>

<?php

$query= new Querys();
$conex = $query->getConection($CONFIG);

$MyrecordDatos = $query->SelDB($conex,"site_sel_ClienteByCode",array($idCliente));
$rowCliente=$query->getdata_object($MyrecordDatos);

if($rowCliente->tipocuenta==5){ //alarma
    $typoCuenta="ALA";
    $modPlural = "Zonas";
    $modSingular = "Zona";
}else if($rowCliente->tipocuenta==7){
    $typoCuenta="BAS";
    $modPlural = "Puntos";
    $modSingular = "Punto";

}

$iconType =getIconTipoCuenta($rowCliente->tipocuenta);

if($query->count_row($MyrecordDatos)==0){
    echo  "Cliente N°: ".$idCliente." No encontrado";
    exit;
}


$EmpresaLat = $_SESSION["cliente"]["latitud"];
$EmpresaLog = $_SESSION["cliente"]["longuitud"];
$BamLatLogCliente=0; //bandera para mostar mensaje de no coordenadas


//se obtiene la posicion del cliente
if($rowCliente->latitud!=0 && $rowCliente->longitud!=0){

    $clienteLat =  $rowCliente->latitud;
    $clienteLog =  $rowCliente->longitud;
    $ZoomMap = 16;
    $BamLatLogCliente=1;

}else{
    if(strlen($_SESSION["user"]["latitud"])>0 && strlen($_SESSION["user"]["longuitud"])>0){
        $clienteLat =  $_SESSION["user"]["latitud"];
        $clienteLog =  $_SESSION["user"]["longuitud"];
        $ZoomMap = 9;
    }else{
        $clienteLat =  $CONFIG['MAP_LAT'];
        $clienteLog =  $CONFIG['MAP_LOG'];
        $ZoomMap = 9;
    }
}

$clienNom = $rowCliente->nombre_cliente;
$icon = $rowCliente->icon;
$pic = $rowCliente->pic;

$PanNewClie = PermModAccUser(2,1);
$PanEditClie = PermModAccUser(8,46);

//incluye el header principal
include("../include/diseno/i_header_PanelClienteFinal.php");

//'incluye el menu principal
include("../include/diseno/i_menu_PanelClienteFinal.php");


?>

<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content page-content-small">
        <div class="page-content-body">
            <!-- HERE WILL BE LOADED AN AJAX CONTENT -->
            <div class="row">
                <div class="col-md-12">
                    <!-- Begin: life time stats -->
                    <div class="portlet box  <?php echo $CONFIG['WEB_THEME'];?>">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="<?php echo $iconType;?>"></i>
                                <span class="font16">
                                    Cliente: <?php echo $clienNom;?> (<?php echo  $rowCliente->TipoAlarma;?>)

                                    <?php if($rowCliente->manu_help){?>
                                    <a href="../file.php?type=<?php echo encode(5,"manual_cliente");?>&file=<?php echo encode(5,$rowCliente->manu_help);?>" target="_new" class="textRed" title="Ver Manual de Ayuda" onclick="setAccionesClienteLog(44,'<?php echo $idCliente;?>','Alarma: <?php echo  $rowCliente->TipoAlarma;?>')">
                                        &nbsp;<i  class="fa fa-file-pdf-o "></i>
                                      </a>
                                    <?php } ?>

                                </span>
                            </div>

                        </div>
                        <div class="portlet-body">
							<form class="form-horizontal" role="form">
 								<div class="form-body">
                                	<div class="row">
                                        <div class="col-md-6 ">
                                            <div class="portlet">
                                            	<div class="col-md-12">
                                                	<div class="row ">
                                                    	<div class="bordered header-top">
                                                            <div class=" form-group marginAll0">
                                                                <label class="control-label col-md-4 text-leftForze  "><b>EMPRESA</b>:</label>
                                                                <div class="col-md-8  ">
                                                                    <p class="form-control-static paddingTop word">
                                                                    	<?php echo $rowCliente->Empresa."&nbsp;(".setTextStatus($rowCliente->id_status).")";?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class=" form-group marginAll0">
                                                                <label class="control-label col-md-4 text-leftForze  "><b>COD. CLIENTE</b>:</label>
                                                                <div class="col-md-8  ">
                                                                    <p class="form-control-static paddingTop word">
                                                                       <span id="name-cliente-panel"> <?php echo $rowCliente->prefijo."-".$rowCliente->cuenta." - ".$clienNom."&nbsp"; ?></span>
                                                                        <?php if(trim($pic)!=""){
                                                                            ?>
                                                                               &nbsp;&nbsp;&nbsp;
                                                                               <a href="<?php echo $CONFIG['HOST'];?>img/img_c/<?php echo $pic;?>?a=<?php echo md5(microtime());?>" class="fancybox-button" title="Cliente : <?php echo $rowCliente->id_cliente." - ".$clienNom;?>" data-rel="fancybox-button">
                                                                                <i class='<?php echo getImgIcon("camaras");?>'></i>
                                                                               </a>

                                                                               <a data-toggle="lightbox" href="#demoLightbox">

                                                                               </a>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class=" form-group marginAll0">
                                                                <label class="control-label col-md-4 text-leftForze "><b>C.I. / RIF</b>:</label>
                                                                <div class="col-md-8  ">
                                                                    <p class="form-control-static paddingTop word">
                                                                        <?php echo $rowCliente->rif;?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class=" form-group marginAll0">
                                                                <label class="control-label col-md-4 text-leftForze "><b>CLAVE MASTER</b>:</label>
                                                                <div class="col-md-8 ">
                                                                    <p class="form-control-static paddingTop word">
                                                                        <?php echo $rowCliente->clavemaster;?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--/span-->
                                        <div class="col-md-6  ">
                                            <div class="portlet">
                                            	<div class="col-md-12">
                                                	<div class="row ">
                                                    	<div class="bordered header-top">
                                                            <div class=" form-group marginAll0">
                                                                <label class="control-label col-md-4 text-leftForze  "><b>CIUDAD</b>:</label>
                                                                <div class="col-md-8  ">
                                                                    <p class="form-control-static paddingTop word">
                                                                    <?php
                                                                        $referencia = "Sin Referencia";

                                                                        if(!empty($rowCliente->estado)){
                                                                            $referencia = $rowCliente->ciudad;
                                                                        }

                                                                        if(!empty($rowCliente->estado)){
                                                                            $referencia.=" , Edo. ".$rowCliente->estado;
                                                                        }

                                                                        if(!empty($rowCliente->pais)){
                                                                            $referencia.=" - ".$rowCliente->pais;
                                                                        }

                                                                        echo substr($referencia,0,50);
                                                                    ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class=" form-group marginAll0">
                                                                <label class="control-label col-md-4 text-leftForze  "><b>DIRECCI&Oacute;N</b>:</label>
                                                                <div class="col-md-8  ">
                                                                    <p class="form-control-static paddingTop word">
                                                                    <?php
                                                                     if(!empty($rowCliente->direccion)){
                                                                        $direcionPDF=substr($rowCliente->direccion,0,100);
                                                                        $direcionHtml=substr($rowCliente->direccion,0,50);
                                                                     }else{
                                                                        $direcionPDF = "Sin Direccion";
                                                                        $direcionHtml=$direcionPDF;
                                                                     }
                                                                    ?>
                                                                    <span class="popovers" data-original-title="Direcci&oacute;n" data-content="<?php echo $rowCliente->direccion;?>" data-placement="left" data-trigger="hover" data-container="body" ><?php echo $direcionHtml;?></span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                             <div class=" form-group marginAll0">
                                                                <label class="control-label col-md-4 text-leftForze  "><b>REFERENCIA</b>:</label>
                                                                <div class="col-md-8  ">
                                                                    <p class="form-control-static paddingTop word">
                                                                        <span class="popovers" data-original-title="Referencia" data-content="<?php echo $rowCliente->referencia;?>" data-placement="left" data-trigger="hover" data-container="body" ><?php echo substr($rowCliente->referencia,0,50);?></span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class=" form-group marginAll0"  style="padding-bottom: 3px;">
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <label class="control-label col-md-12 text-leftForze  "><b>TEL.&nbsp;LOCAL</b>:&nbsp;<?php echo $rowCliente->telf_local;?></label>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <label class="control-label col-md-12 text-leftForze  "><b>MOVIL</b>:&nbsp;<?php echo $rowCliente->telf_movil;?> </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--/span-->
                                    </div>
                                    <!--/row-->
                                    <div style="padding:1px"></div>
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <div class="portlet">
                                                <div class="col-md-12">
                                                    <div class="row ">
                                                        <div class="bordered header-bottom">
                                                            <div class=" form-group marginAll0">
                                                                <label class="control-label col-md-4 text-leftForze  "><b>E-MAIL</b>:</label>
                                                                <div class="col-md-8  ">
                                                                    <p class="form-control-static paddingTop word">
                                                                        <?php echo $rowCliente->email;?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class=" form-group marginAll0">
                                                                <label class="control-label col-md-4 text-leftForze  "><b>TIPO</b>:</label>
                                                                <div class="col-md-8  ">
                                                                    <p class="form-control-static paddingTop word">
                                                                        <?php echo $rowCliente->TipoC;?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class=" form-group marginAll0">
                                                                <label class="control-label col-md-4 text-leftForze "><b>PROTOCOLO</b>:</label>
                                                                <div class="col-md-8  ">
                                                                    <p class="form-control-static paddingTop word">
                                                                        <?php echo $rowCliente->Protocolo;?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--/span-->
                                        <div class="col-md-6  ">
                                            <div class="portlet">
                                                <div class="col-md-12">
                                                    <div class="row ">
                                                        <div class="bordered header-bottom">
                                                            <div class=" form-group marginAll0">
                                                                <label class="control-label col-md-4 text-leftForze  "><b>FECHA INICIO</b>:</label>
                                                                <div class="col-md-8  ">
                                                                    <p class="form-control-static paddingTop word">
                                                                        <?php
                                                                           if(!empty($rowCliente->fechinicio)){
                                                                                echo date_format($rowCliente->fechinicio,"d/m/Y h:i:s a");
                                                                           }else{
                                                                                echo "Sin Fecha";
                                                                           }
                                                                        ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class=" form-group marginAll0">
                                                                <label class="control-label col-md-4 text-leftForze  "><b>ÚLTIMO EVENTO</b>:</label>
                                                                <div class="col-md-8  ">
                                                                    <p class="form-control-static paddingTop word">
                                                                        <?php

                                                                        $MyRecodI = $query->SelDB($conex,"site_sel_UltimoStatusPanelByCliente",array($idCliente));
                                                                        $rowEvent=$query->getdata_object($MyRecodI);

                                                                        if($query->count_row($MyRecodI)){
                                                                            $Msg = $rowEvent->descript." ".$rowEvent->UserZona;
                                                                            $FechaEven = getFullDateShort($rowEvent->fecha);
                                                                            $styleEv ="color:".$rowEvent->web_color.";background:".$rowEvent->web_colorBg;
                                                                        }else{
                                                                            $Msg = "Informaci&oacute;n no registrada";
                                                                            $FechaEven = "Sin Fecha";
                                                                            $styleEv ="";
                                                                        }
                                                                        ?>
                                                                        <label style="<?php echo $styleEv;?>; margin-bottom: 0px;"><?php echo $Msg;?></label>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                             <div class=" form-group marginAll0">
                                                                <label class="control-label col-md-4 text-leftForze  "><b>ÚLTIMO REPORTE</b>:</label>
                                                                <div class="col-md-8  ">
                                                                    <p class="form-control-static paddingTop word">
                                                                        <?php echo  $FechaEven;?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--/span-->
                                    </div>
                                    <!--/row-->
                                </div>
                            </form>
                            <div style="padding:1px"></div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="tabbable-custom ">
                                        <ul class="nav nav-tabs <?php echo $CONFIG['WEB_THEME'];?>">
                                            <li class="active ">
                                                <a href="#tab_5_1" id="showZonas" menBar="men-zona" data-toggle="tab">
                                                     <?php echo $modPlural;?><?php if($TotalZonas>0){ ?>
                                                     (<?php echo $TotalZonas;?>)
                                                    <?php } ?>
                                                </a>
                                            </li>
                                            <?php if($typoCuenta=="BAS"){?>
                                            <li class=" bordered-left">
                                                <a href="#tab_5_13" id="showRondas" menBar="men-rondas" data-toggle="tab">
                                                    Rondas
                                                </a>
                                            </li>
                                            <?php } ?>
                                            <li class=" bordered-left">
                                                <a href="#tab_5_2" id="showUser" menBar="men-user" data-toggle="tab">
                                                     Usuarios<?php if($TotalUsuarios>0){ ?>
                                                    (<?php echo $TotalUsuarios;?>)
                                                    <?php } ?>
                                                </a>
                                            </li>
                                            <li class=" bordered-left">
                                                <a href="#tab_5_4"  id="showAsist" menBar="men-asist"  data-toggle="tab">
                                                   Contactos
                                                </a>
                                            </li>
                                            <?php if($typoCuenta=="ALA"){?>
                                            <li class=" bordered-left">
                                                <a href="#tab_5_5" id="showHorarios" menBar="men-hora"  data-toggle="tab">
                                                    Horarios
                                                </a>
                                            </li>
                                            <?php } ?>
                                            <li class=" bordered-left">
                                                <a href="#tab_5_6"  id="showSms" menBar="men-sms" data-toggle="tab">
                                                    SMS
                                                </a>
                                            </li>
                                            <li class=" bordered-left">
                                                <a href="#tab_5_7" id="showSenal" menBar="men-sig" data-toggle="tab">
                                                    Historial
                                                </a>
                                            </li>
                                            <li class=" bordered-left">
                                                <a href="#tab_5_9" id="showMap" menBar="men-map" data-toggle="tab">
                                                    Mapa
                                                </a>
                                            </li>
                                            <li class=" bordered-left">
                                                <a href="#tab_5_10"  id="showCam"  menBar="men-cam"  data-toggle="tab">
                                                    Camaras
                                                </a>
                                            </li>
                                            <li class=" bordered-left bordered-right">
                                                <a href="#tab_5_11"  id="showOrdenes"  menBar="men-ord"  data-toggle="tab">
                                                    Ordenes
                                                </a>
                                            </li>
                                            <li class=" bordered-left bordered-right">
                                                <a href="#tab_5_12"  id="showSoport"  menBar="men-soport"  data-toggle="tab">
                                                    Soporte
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="tab_5_1">
                                                <p>
                                                    <?php include("zonas/view.php");?>
                                                </p>
                                            </div>
                                            <div class="tab-pane" id="tab_5_2">
                                                <p>
                                                    <?php include("usuarios/view.php");?>
                                                </p>
                                            </div>
                                            <div class="tab-pane" id="tab_5_4">
                                                <p>
                                                    <?php include("contactos/view.php");?>
                                                </p>
                                            </div>
                                            <?php if($typoCuenta=="ALA"){?>
                                            <div class="tab-pane" id="tab_5_5">
                                                <p>
                                                    <?php include("horarios/view.php");?>
                                                </p>
                                            </div>
                                            <?php } ?>
                                            <div class="tab-pane" id="tab_5_6">
                                                <p>
                                                    <?php include("sms/view.php");?>
                                                </p>
                                            </div>
                                            <div class="tab-pane" id="tab_5_7">
                                                <p>
                                                    <?php include("signales/view.php");?>
                                                </p>
                                            </div>
                                            <div class="tab-pane" id="tab_5_9">
                                                <p>
                                                    <?php include("maps/view.php");?>
                                                </p>
                                            </div>
                                            <div class="tab-pane" id="tab_5_10">
                                                <p>
                                                    <?php include("camaras/view.php");?>
                                                </p>
                                            </div>
                                            <div class="tab-pane" id="tab_5_11">
                                                <p>
                                                    <?php include("ordenes/view.php");?>
                                                </p>
                                            </div>
                                            <div class="tab-pane" id="tab_5_12">
                                                <p>
                                                    <?php include("soporte/view.php");?>
                                                </p>
                                            </div>
                                            <div class="tab-pane" id="tab_5_13">
                                                <p>
                                                   <?php include("rondas/view.php");?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End: life time stats -->
                </div>
            </div>
        </div>
    </div>
    <!-- BEGIN CONTENT -->
</div>
<!-- END CONTAINER -->

<?php
//incluye el bottom de la pagina
include("../include/diseno/i_bottom.php");

//incluye los javascript principales del framework
include("../include/diseno/i_bottom_script_default.php");

?>

<script>
var ControllerTab = {
    tabZonas:false,
    tabUser:false,
    tabEvent:false,
    tabAsist:false,
    tabSms:false,
    tabSen:false,
    tabCam:false,
    tabOrd:false,
    tabHora:false,
    tabNota:false,
    tabSoprt:false,
    tabRondas:false,
};
$(document).ready(function() {
    $('.header-top').equalHeight();
    $('.header-bottom').equalHeight();

    Metronic.init();

    setTimeout(function(){
        ControllerTab.tabZonas = true;
        initialize_table_zonas();
    },300);

    $("#showZonas").click(function() {
        if(!ControllerTab.tabZonas){
            ControllerTab.tabZonas = true;
            initialize_table_zonas();
        }
    });

    $("#showRondas").click(function() {
        if(!ControllerTab.tabRondas){
            ControllerTab.tabRondas = true;
            setTimeout(function(){initialize_table_rondas();},300);
        }
    });

    $("#showUser").click(function() {
        if(!ControllerTab.tabUser){
            ControllerTab.tabUser = true;
            setTimeout(function(){initialize_table_user();},300);
        }
    });

    $("#showAsist").click(function() {
        if(!ControllerTab.tabAsist){
            ControllerTab.tabAsist = true;
            setTimeout(function(){initialize_table_contactos();},300);
        }
    });

    $("#showHorarios").click(function() {
      if(!ControllerTab.tabHora){
        ControllerTab.tabHora = true;
        setTimeout(function(){initialize_table_horarios();},300);
      }
    });

    $("#showSms").click(function() {
        if(!ControllerTab.tabSms){
            ControllerTab.tabSms = true;
            setTimeout(function(){initialize_table_sms();},300);
        }
    });

    $("#showSenal").click(function() {
        if(!ControllerTab.tabSen){
            ControllerTab.tabSen = true;
            setTimeout(function(){initialize_table_signal();},300);
        }
    });

    $("#showMap").click(function() {
        $("#map").css({'width':'100%', 'height':'400px'});
        setTimeout(function(){load_map();},1000);
    });

    $("#showCam").click(function() {
        if(!ControllerTab.tabCam){
            ControllerTab.tabCam = true;
            setTimeout(function(){initialize_table_camaras();},500);
        }
    });

    $("#showSoport").click(function() {
        if(!ControllerTab.tabSoprt){
            ControllerTab.tabSoprt=true;
            $("#map-soport").css({'width':'100%', 'height':'400px'});
            setTimeout(function(){load_map_soport();},1000);
        }
    });

     $("#showOrdenes").click(function() {
      if(!ControllerTab.tabOrd){
        ControllerTab.tabOrd = true;
        setTimeout(function(){initialize_table_ordenes();},500);
      }
    });

});

function activeMenubarPanelk(t,a){
    var menuContainer = jQuery('.page-sidebar ul');
    $('.selected', menuContainer.children('li.active')).remove();
    menuContainer.children('li.active').removeClass('active');

    $(t).parents('li').addClass('active');
    $(t).parents('li').find("a:first").append('<span class="selected"></span>');

    $(a).click();
}

$('.page-sidebar-menu').on('click', 'li > a', function (e) {
    e.preventDefault();
    var t = "#"+$(e.currentTarget).attr("tab-meniu");

    var menuContainer = jQuery('.page-sidebar ul');
    $('.selected', menuContainer.children('li.active')).remove();
    menuContainer.children('li.active').removeClass('active');

    $(e).parents('li').addClass('active');
    $(e).parents('li').find("a:first").append('<span class="selected"></span>');

    $(t).click();

});

$('.nav-tabs').on('click', 'li > a[data-toggle="tab"]', function (e) {
    e.preventDefault();
    var t = "#"+$(e.currentTarget).attr("menBar");

    var menuContainer = jQuery('.page-sidebar ul');
    $('.selected', menuContainer.children('li.active')).remove();
    menuContainer.children('li.active').removeClass('active');

    $(t).parents('li').addClass('active');
    $(t).parents('li').find("a:first").append('<span class="selected"></span>');

});

function showNewClienteEquipos(){
  $("#myModalViewClienteEq").modal("show");
}
</script>