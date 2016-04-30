<?php
	include("../include/diseno/i_topExternalMonitoreo.php");

	//incluye los css principales del framework
	include("../include/diseno/i_header_css_monitoreo.php");
?>
<link rel="stylesheet" type="text/css" href="<?php echo $CONFIG['HOST'];?>plugins/thickbox/thickbox.css"/>
<title>
	Monitoreo <?php echo $CONFIG['META_AUTOR'];?>
</title>

<?php if($_SESSION["user"]["tipoUser"]==2){ ?>
<script>
var WinMaps = null;

if(WinMaps){
	window.WinMaps.close();
}

WinMaps = window.open("MapsMonitoreo.php","Monitoreo Maps","height="+window.screen.height+",width="+window.screen.width);
</script>
<?php } ?>
<style>
    #contextMenu {
        position: absolute;
        display:none;
    }
    table > tbody > tr {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
</style>

<?php
include("../js/session.js.php");
include("../include/diseno/i_headerMonitoreo.php");

//'function para crear tablas iniciales de señales
function StrTable($idTable,$strLoad){
    global $CONFIG;

    $StrSalida = "<table class='table table-hover  table-condensed '>";
    $StrSalida.= "<thead><tr > ";
    $StrSalida.= "<th align='center'><b>#</b></th>";
    $StrSalida.= "<th><b>Cliente</b></th>";
    $StrSalida.= " <th><b>Evento</b></th>";
    $StrSalida.= "<th><b>Usuario / Zona</b></th>";
    $StrSalida.= "<th><b>Fecha</b></th>";
    $StrSalida.= " </tr> </thead> <tbody id='".$idTable."'><tr> <td colspan='5' ><br/><div class='splash-loader  text-center text-1x'><img class='preload-medium' src='".$CONFIG['HOST']."img/loading-spinner-grey.gif' alt='' /></div><p class='lead text-center text-1x'>Cargando ".$strLoad."...</p>";
    $StrSalida.= "</td> </tr> </tbody> </table>";

    return $StrSalida;
}

//function para crear tablas iniciales de señales con fecha de procesada
function StrTable2($idTable,$strLoad){
    global $CONFIG;

    $StrSalida = "<table class='table table-hover  table-condensed '>";
    $StrSalida.= "<thead><tr > ";
    $StrSalida.= "<th align='center'><b>#</b></th>";
    $StrSalida.= "<th><b>Cliente</b></th>";
    $StrSalida.= " <th><b>Evento</b></th>";
    $StrSalida.= "<th><b>Usuario / Zona</b></th>";
    $StrSalida.= "<th><b>Fecha Recepcion</b></th>";
    $StrSalida.= "<th><b>Fecha Procesada</b></th>";
    $StrSalida.= " </tr> </thead> <tbody id='".$idTable."'> <tr> <td colspan='6' ><br/><div class='splash-loader  text-center text-1x'><img class='preload-medium' src='".$CONFIG['HOST']."img/loading-spinner-grey.gif' alt='' /></div><p class='lead text-center text-1x'>Cargando ".$strLoad."...</p>";
    $StrSalida.= "</td> </tr> </tbody> </table>";

    return $StrSalida;
}

//function para crear tablas iniciales de señales con acciones
function StrTableAcc($idTable,$strLoad){
    global $CONFIG;

    $StrSalida = "<table class='table table-hover  table-condensed '>";
    $StrSalida.= "<thead><tr > ";
    $StrSalida.= "<th  align='center'><b>#</b></th>";
    $StrSalida.= "<th ><b>Cliente</b></th>";
    $StrSalida.= "<th ><b>Evento</b></th>";
    $StrSalida.= "<th ><b>Usuario / Zona</b></th>";
    $StrSalida.= "<th ><b>Fecha</b></th>";
    $StrSalida.= "<th align='center'><b>Acciones</b></th>";
    $StrSalida.= " </tr></thead>  <tbody id='".$idTable."'> <tr> <td colspan='6' ><br/><div class='splash-loader  text-center text-1x'><img class='preload-medium' src='".$CONFIG['HOST']."img/loading-spinner-grey.gif' alt='' /></div><p class='lead text-center text-1x'>Cargando ".$strLoad."...</p>";
    $StrSalida.= "</td> </tr> </tbody> </table>";

    return $StrSalida;
}

//verifica los permisos del usuario
$tabsSigxProcesar=0;
$tabsSigPendientes=0;
$tabsSigTodas=0;
$tabsSigProcesadas=0;

$contPen = 0;

if((PermModAccUser(1,47) && $_SESSION["user"]["tipoUser"]==2) || $_SESSION["cliente"]["tipoUser"] == 3){
    $tabsSigxProcesar=1;
}

if((PermModAccUser(1,48)  && $_SESSION["user"]["tipoUser"]==2) || $_SESSION["cliente"]["tipoUser"] == 3){
    $tabsSigPendientes=1;
    //busca cantidad de señales en pendiente
    if($_SESSION["user"]["master"]==0){
        $paramCP = " AND (id_empresa = '".$_SESSION["user"]["idEmpresa"]."') ";
    }

    $query= new Querys();
    $conex = $query->getConection($CONFIG);

    $MyRecordPenAux = $query->SelDB($conex,"site_sel_MonitoreoSenalesPendientes",array($paramCP));
    $contPen = $query->count_row($MyRecordPenAux);
}

if((PermModAccUser(1,49) && $_SESSION["user"]["tipoUser"]==2) ||  $_SESSION["cliente"]["tipoUser"] == 3){
    $tabsSigTodas=1;
}

if((PermModAccUser(1,50) && $_SESSION["user"]["tipoUser"]==2) || $_SESSION["cliente"]["tipoUser"] == 3){
    $tabsSigProcesadas=1;
}


?>
<div class="page-container">
	<div class="container-fluid" style="padding-right: 0px; padding-left: 0px;">
		<div class="page-content">
            <!--adventencia pause-->
            <div class="row" id="div-estatus-monitoreo" style="margin-right: 0px; margin-left: 0px; display:none">
                <div class="col-md-12" align="center">
                    <h2 class=" ">
                        <i class="glyphicon glyphicon-info-sign font-red" style="font-size:1em"></i>
                        Monitoreo se encuentra en modo PAUSE, <a href="javascript:;" class="btn-status-monitoreo">presiona aqui</a> para continuar
                    </h2>
                </div>
            </div>

            <!--tabs de monitoreo-->
			<div class="row" style="margin-right: 0px; margin-left: 0px;" id="div-tabs-monitoreo">
                <div class="col-md-12" style="padding-right: 0px; padding-left: 0px;">
					<div class="tabbable-custom ">
                        <ul class="nav nav-tabs <?php echo $CONFIG['WEB_THEME'];?>">
                            <?php if($tabsSigxProcesar==1){?>
                        	<li class="active bordered-right">
                                <a href="#tabs-1" id="ShowProSignal" data-toggle="tab">Se&ntilde;ales por Procesar <span class="badge badge-danger" id="count-proc">0</span></a>
                            </li>
                            <?php } ?>
                            <?php if($tabsSigPendientes==1){?>
                            <li class=" bordered-right">
                                <a href="#tabs-2" id="ShowPenSignal" data-toggle="tab">Se&ntilde;ales Pendientes &nbsp;<span class="badge badge-danger" id="count-pen"><?php echo $contPen;?></span></a>
                            </li>
                            <?php } ?>
                            <?php if($tabsSigTodas==1){?>
                             <li class=" bordered-right">
                                <a href="#tabs-4" id="ShowSignalLog" data-toggle="tab">Todas las Se&ntilde;ales</a>
                            </li>
                            <?php } ?>
                            <?php if($tabsSigProcesadas==1){?>
                             <li class=" bordered-right">
                                <a href="#tabs-3" id="ShowProcSignal" data-toggle="tab">Se&ntilde;ales Procesadas</a>
                            </li>
                            <?php } ?>
                        </ul>
                        <div class="tab-content bbtom0" id="cont-tabs" >
                                <?php if($tabsSigxProcesar==1){?>
                            	<div class="tab-pane active" id="tabs-1">
                            		<?php echo StrTableAcc("tbodySigProc","Se&ntilde;ales");?>
                            	</div>
                                <?php } ?>
                                <?php if($tabsSigPendientes==1){?>
                            	<div class="tab-pane" id="tabs-2">
                            		<?php echo StrTable("tbodySigPendientes","Se&ntilde;ales");?>
                            	</div>
                                <?php } ?>
                                <?php if($tabsSigProcesadas==1){?>
                            	<div class="tab-pane" id="tabs-3">
                            		<?php echo StrTable2("tbodySigProcesadas","Se&ntilde;ales");?>
                            	</div>
                                <?php } ?>
                                <?php if($tabsSigTodas==1){?>
                            	<div class="tab-pane" id="tabs-4">
                                    <?php echo StrTable("tbodySigLog","Todas las Se&ntilde;ales");?>
                            	</div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>
    <?php
    if($_SESSION["user"]["tipoUser"]==2){
        include("../include/diseno/sidebar/quick-sidebar.php");
    }
    ?>
</div>

<div style="display:none">
    <audio id="audio_emerg" loop="true" volume="0.0">
        <source type="audio/wav" src="<?php echo $CONFIG['HOST'];?>include/sonidos/Alarm.wav"></source>,
        <source type="audio/ogg" src="<?php echo $CONFIG['HOST'];?>include/sonidos/Alarm.ogg"></source>,
        <source type="audio/mpeg" src="<?php echo $CONFIG['HOST'];?>include/sonidos/Alarm.mp3"></source>
    </audio>
    <audio id="audio_pendientes" >
        <source type="audio/wav" src="<?php echo $CONFIG['HOST'];?>include/sonidos/pendiente.wav"></source>,
        <source type="audio/ogg" src="<?php echo $CONFIG['HOST'];?>include/sonidos/pendiente.ogg"></source>,
        <source type="audio/mpeg" src="<?php echo $CONFIG['HOST'];?>include/sonidos/pendiente.mp3"></source>
    </audio>
</div>

<div id="contextMenu" class="dropdown clearfix menu-btn-right">
    <ul class="dropdown-menu <?php echo $CONFIG['WEB_THEME'];?>" role="menu" aria-labelledby="dropdownMenu" style="display:block;position:static;">
        <?php if($_SESSION["cliente"]["tipoUser"] == 2){?>
        <li id="li-del"><a tabindex="-1" href="javascript:void(0)" class="deletex">Pasar a Pendiente</a></li>
        <?php } ?>
        <li><a tabindex="-1" href="javascript:void(0)" class="mapax">Mapa</a></li>
        <?php /*?><li class="divider"></li>
        <li class="dropdown-submenu">
        <a href="javascript:void(0)" class="li-camaras" tabindex="-1" rel-old="0">Camaras</a>
            <ul class="dropdown-menu <%=theme%>" id="ul-cam">
                <li><a href="javascript:void(0)" tabindex="-1">Sin Camaras</a></li>
            </ul>
        </li>*/?>
    </ul>
</div><!-- set up the modal to start hidden and fade in and out -->

<?php
include("../include/diseno/i_bottom.php");
include("../include/diseno/i_bottom_script_monitoreo_external.php");
?>
<script>
    var globales_salir='Salir';
    tb_pathToImage = '<?php echo $CONFIG['HOST'];?>img/preload-8-white.gif';
    var fnClose = "CloseTick";
</script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/jquery-migrate.js"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/thickbox/thickbox.js"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/jquery.timer.js"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/jquery.idle-timer.js"></script>

<script>
$(document).ready(function() {
    <?php if($_SESSION["user"]["tipoUser"]==2){ ?>
    if(WinMaps==null){
        bootbox.alert("Por favor active ventanas emergentes de su navegador para que pueda disfrutar del Monitoreo Maps");
    }
    <?php } ?>

    //function de sub menu sobre tabla
    $(function () {
        var $contextMenu = $("#contextMenu");
        var $rowClicked;

        $("body").on("contextmenu", ".contextMenu", function (e) {
            $rowClicked = $(this);

            //verifica el tabss para mostara opciones
            if($(this).attr("rel-menu")=="xp"){
                $("#li-del").show();
            }else{
                $("#li-del").hide();
            }

            $contextMenu.css({
                display: "block",
                left: e.pageX,
                top: e.pageY
            });
            return false;
        });

        //opcion de pasar a pendiente
        $contextMenu.on("click", "a.deletex", function () {
            var ix = $($rowClicked).attr("id");
            ConfirmSendPendiente(ix)
            $contextMenu.hide();
        });

        //opcion de ver mapa
        $contextMenu.on("click", "a.mapax", function () {
            var rows = eval('('+$($rowClicked).attr("rel-info")+')');
            window.open("mapsCliente.php?q="+rows.codi,"","height=550,width=600")
            $contextMenu.hide();
        });

        //opcion ver camaras
        $contextMenu.on("mouseover","a.li-camaras",function(){
            var that = this;
            var rows = eval('('+$($rowClicked).attr("rel-info")+')');
            if($(that).attr("rel-old")!=rows.idCliente){
                $(that).attr("rel-old",rows.idCliente)
                $("#ul-cam").html('<li><a href="javascript:void(0)" tabindex="-1">Buscando...</a></li>');
                var param = "x="+Math.random()+"&q="+rows.idCliente+"&acc=load_cam_cliente";
                $("#ul-cam").load('controller.php',param,function(response,status,xhr){});
            }
        });

        $(document).click(function () {
            $contextMenu.hide();
        });

    });

    //datos de la configuracion del monitoreo
    system={
        MaxIdTramaLog:0,
        MaxDateTramaProc:"",
        loadProSignal:false,
        loadLog:false,
        loadProPending:false,
        loadProcSignal:false,
        tabProSignal:false,
        tabSignalLog:false,
        tabPenSignal:false,
        tabProcSignal:false,
        autoLoad:10000,
        tempObjCierre:null,
        HrAjaxXProcesar:null,
        HrAjaxXProcesadas:null,
        HrAjaxDatosClient:null,
        HrAjaxheartbeat:null,
        tabDatosClie:false,
        Pplay:true,
        BamloadSinProc:false,
        BamloadLog:false,
        BamloadProcesado:false,
        BamloadHeartbeat:false,
        stsMonitoreo:0,//estatus para validar el pausar o ausente
        windowMoniCamaras:null,
        windowMoniImgZona:null,
        windowMoniImgUser:null,
        loadXProcesarPlay:function(){
            this.Pplay=true;
            if(this.HrAjaxXProcesar)
                this.HrAjaxXProcesar.abort();
            this.loadProSignal = false;
            processingSignals();
        },
        loadXProcesarStop:function(){
            this.Pplay=false;
            if(this.HrAjaxXProcesar)
                this.HrAjaxXProcesar.abort();
            this.loadProSignal = true;
        },
        loadProcesadasPlay:function(){
            if(this.HrAjaxXProcesadas)
                system.HrAjaxXProcesadas.abort();
            SignalsProcesadas();
        },
        loadheartbeat:function(){
            if(this.HrAjaxheartbeat)
                system.HrAjaxheartbeat.abort();
            heartbeatOpeMonitoreo();
        }
    }

    //function para buscar automaticamnete
    setTimeout(function(){

        heartbeatOpeMonitoreo();//primer hearbeat


        <?php
            //verifica cual es el primer tabs para cargarlo al inicia la pagina'
            if($tabsSigxProcesar == 1){?>

                processingSignals();

                //actualiza las señales por procesar
                setInterval(function(){
                    processingSignals();
                },system.autoLoad);
            <?php
            }else{
                if($tabsSigPendientes == 1){
                    echo "$('#ShowPenSignal').click();";
                }else{
                    if($tabsSigTodas == 1){
                        echo "$('#ShowSignalLog').click();";
                    }else{
                        if($tabsSigProcesadas == 1){
                            echo "$('#ShowProcSignal').click();";
                        }
                    }
                }
            }
            ?>

        setInterval(function(){
            heartbeatOpeMonitoreo();
        },system.autoLoad+5000);

    },100);

    //activa fucion de cargar señales pendientes
    $("#ShowPenSignal").click(function() {
        if(!system.tabPenSignal){
            system.tabPenSignal = true;
            SignalsPendientes();

            //actualza el log cada x segundos
            setInterval(function(){
                SignalsPendientes();
            },system.autoLoad);
        }
    });

    //activa fucion de cargar log
    $("#ShowSignalLog").click(function() {
        if(!system.tabSignalLog){
            system.tabSignalLog = true;
            logSignals();
            //actualza el log cada x segundos
            setInterval(function(){
                logSignals();
            },system.autoLoad);
        }
    });

    //activa fucion de cargar señales procesadas
    $("#ShowProcSignal").click(function() {
        if(!system.tabProcSignal){
            system.tabProcSignal = true;
            SignalsProcesadas();

            //actualza el log cada x segundos
            setInterval(function(){
                SignalsProcesadas();
            },system.autoLoad);
        }
    });

    $("#link-iconsound").click(soundMute);

    $(document).on("click", "a.delete", function () {
        var ix = $(this).parent().parent().attr("id");
        ConfirmSendPendiente(ix)
    });

    $(document).on("click", "a.link_url", function () {
        var url = $(this).attr("rel-url");
        if(url)
            window.open("http://"+url,"","height=550,width=600")
    });

    $("#btn-cieere-rapido").click(showCierreRapido);

    $(".btn-status-monitoreo").click(setEstatusMonitoreo);

});


//funcion para cargar las señales por procesar
function processingSignals(){
    if(!system.loadProSignal){
        system.HrAjaxXProcesar = $.ajax({
            url:'controller.php?x='+Math.random(),
            type: "POST",
            timeout:60000,
            error: function(x, t, m) {
                system.loadProSignal=false;
            },
            data: {
                acc:'load_proccessin_signal'
            },
            cache: false,
            beforeSend:function(){
                system.loadProSignal=true;
            },
            success:function(data) {
                validateSession(data);

                if(!system.BamloadSinProc){
                    system.BamloadSinProc=true;
                    $("#tbodySigProc").html("");
                }

                system.loadProSignal=false;

                $("#tbodySigProc").html(data);
                setTimeout(function(){AlertEmergencias();},100);
                pasarWindos();//pasa al mapa los cleintes q tiene coordenas
            }

        });
    }
}

//function para cargar señales pendientes
function SignalsPendientes(){
    if(!system.loadProPending){
        $.ajax({
            url:'controller.php?x='+Math.random(),
            type: "POST",
            timeout:60000,
            error: function(x, t, m) {
                system.loadProPending=false;
            },
            data: {
                acc:'load_signal_pendientes'
            },
            cache: false,
            beforeSend:function(){
                system.loadProPending=true;
            },
            success:function(data) {
                validateSession(data);
                system.loadProPending=false;
                $("#tbodySigPendientes").html(data);
                setTimeout(function(){$("#count-pen").text($("#count-penx").val())},100);
                AlertEmergencias();
            }

        });
    }
}

function soundMute(event){
    event.preventDefault();

    <?php if(PermModAccUser(1,190)){ ?>

    var elemt=$("#iconsound");
    if(elemt.hasClass('fa-volume-up')){
        elemt.removeClass("fa-volume-up");
        elemt.addClass("fa-volume-off");
        document.getElementById('audio_emerg').muted = true;
    }else{
        elemt.removeClass("fa-volume-off");
        elemt.addClass("fa-volume-up");
        document.getElementById('audio_emerg').muted = false;
    }
    <?php } ?>
}

//function para cargar el log de señales
function logSignals(){
    if(!system.loadLog){
        $.ajax({
            url:'controller.php?x='+Math.random(),
            type: "POST",
            timeout:60000,
            error: function(x, t, m) {
                system.loadLog=false;
            },
            data: {
                acc:'load_log_signal',
                MaxIdTramaLog:system.MaxIdTramaLog
            },
            cache: false,
            beforeSend:function(){
                system.loadLog=true;
            },
            success:function(data) {
                validateSession(data);
                system.loadLog=false;

                if(!system.BamloadLog){
                    system.BamloadLog=true;
                    $("#tbodySigLog").html("");
                }

                $("#tbodySigLog").prepend(data);

                $('#tbodySigLog tr').each(function(index,value) {
                   if (index>99){
                        $(this).remove();
                   }
                });

                setCountTable('.count-sl');
                Metronic.initAjax();
            }

        });
    }
}

//function para cargar señales procesadas
function SignalsProcesadas(){
    if(!system.loadProcSignal){
        system.HrAjaxXProcesadas = $.ajax({
            url:'controller.php?x='+Math.random(),
            type: "POST",
            timeout:60000,
            error: function(x, t, m) {
                system.loadProcSignal=false;
            },
            data: {
                acc:'load_signal_procesadas',
                MaxDateTrama:system.MaxDateTramaProc
            },
            cache: false,
            beforeSend:function(){
                system.loadProcSignal=true;
            },
            success:function(data) {
                validateSession(data);
                system.loadProcSignal=false;

                if(!system.BamloadProcesado){
                    system.BamloadProcesado=true;
                    $("#tbodySigProcesadas").html("");
                }

                $("#tbodySigProcesadas").prepend(data);

                $('#tbodySigProcesadas tr').each(function(index,value) {
                   if (index>99){
                       $(this).remove();
                   }
                });

                setCountTable('.count-pro');

                Metronic.initAjax();
            }

        });
    }
}

function AlertEmergencias(){
    var c =$("#tbodySigProc tr.pointer").length;
    if(c>0){
        $("#lbl-count-emrg").html(c);
        $("#count-proc").text(c);
        $("#lbl-count-emrg").show();
        if(system.Pplay)
            $("#audio_emerg")[0].play();
    }else{
        $("#lbl-count-emrg").html("0");
        $("#count-proc").text("0");
        $("#lbl-count-emrg").hide();
        $("#audio_emerg")[0].pause();
    }
}

function setCountTable(identi){
    var _total = $(identi).length;
    $.each($(identi),function(index,value) {
        $(value).html(_total);
        _total--;
    });
}

function removeTrTable(idx){
    $(idx).fadeTo(400, 0, function () {
        $(this).remove();
    });

    setTimeout(function(){
        AlertEmergencias();
    },500);

    setTimeout(function(){
        pasarWindos();
    },200);
}

//funcion para validar pasar a pendiente
function ConfirmSendPendiente(x){
    bootbox.confirm("Esta seguro que desea pasar esta señal a pendiente??",  function(result) {
        if (result) {
            sendPendiente(x,'');
        }
    });
}

function sendPendiente(x,coment){
    var id = getIDtr(x);
    setTramaPendiente(id,coment);
    var co_aux = parseInt($("#count-proc").text());
    $("#count-proc").text(co_aux-1);

    $("#PS-"+id).fadeTo(400, 0, function () {
        $(this).remove();
    });
}

//function para cambiar a pendientes
function setTramaPendiente(id,coment){

    var tAux = eval('('+$("#PS-"+id).attr("rel-info")+')');

    $.ajax({
        url:'controller.php?x='+Math.random(),
        type: "POST",
        timeout:60000,
        data: {
            acc:'update_pendiente',
            idC:tAux.idCliente,
            det:tAux.evento+" "+tAux.eventD,
            id:id,
            coment:coment
        },
        cache: false,
        beforeSend:function(){

        },
        success:function(data) {
            if(data=='ok'){
                alertSuccess({title:"Notificaci&oacute;n",text:"Se&ntilde;al en Observaci&oacute;n."})
                SignalsPendientes();
            }
        }

    });
}

//obtener un partido
function getIDtr(id){
    var aux = id.split("-");
    return aux[1];
}

<?php
if($tabsSigPendientes==1){
?>
//function para verificar si hay señales en pendientes
function verifyTablePendientes(){
    var rowss = $('#tbodySigPendientes tr.pointer').length;
    if (rowss > 0){
        $("#audio_pendientes")[0].play();
        alertNotice({title:"Notificaci&oacute;n",text:"Recuerde que Ud. tiene "+rowss+" señales en espera",delay:"10000"});
    }else{
        $("#audio_pendientes")[0].pause();
    }
}

setTimeout(function(){
    SignalsPendientes();
    setTimeout(function(){
        $.timer('<?php echo TIME_PENDIENTES_SOUND;?>', function(){
            verifyTablePendientes();
        })
    },1000);
},5000) ;
<?php
}
?>
function CloseTick(){
    system.loadXProcesarPlay();
    AlertEmergencias();

    if(system.windowMoniCamaras){
        system.windowMoniCamaras.close();
        system.windowMoniCamaras = null;
    }

    if(system.windowMoniImgZona){
        system.windowMoniImgZona.close();
        system.windowMoniImgZona = null;
    }

    if(system.windowMoniImgUser){
        system.windowMoniImgUser.close();
        system.windowMoniImgUser = null;
    }

    if(verfiWindow()){
        WinMaps.LimpiarInfoClient();
    }
}

function closeTrama(obj){
    system.loadXProcesarStop();
    $("#audio_emerg")[0].pause();

    system.tempObjCierre = eval('('+$("#"+obj.id).attr("rel-info")+')');
    var idCliente = system.tempObjCierre.idCliente;
    var idTrama = system.tempObjCierre.idTrama;
    var tipoevent = system.tempObjCierre.tipoevent;
    var idDisp = system.tempObjCierre.idDisp;


    height = Math.round($(window).height() *0.9);
    var tabx = setTabModal(obj.tipo);

    var url = 'cierre_signal.php?idDisp='+idDisp+'&tyev='+tipoevent+'&alto='+height+'&tipo='+obj.tipo+'&q='+idCliente+'&trama='+idTrama+tabx+'&fast=0&keepThis=true&TB_iframe=true&height='+height+'&width='+Math.round($(window).width() *0.8)+'&modal=true';

    tb_show('Cierre de Señal',url,'');
}

function showCierreRapido(){
     system.tempObjCierre = {pre:"PS-"};
    var idCliente = 0;
    var idTrama = 0;

    height = Math.round($(window).height() *0.9);
    var tabx = setTabModal(5);

    var url = 'cierre_signal.php?alto='+height+'&tipo=1&q='+idCliente+'&trama='+idTrama+tabx+'&fast=1&keepThis=true&TB_iframe=true&height='+height+'&width='+Math.round($(window).width() *0.8)+'&modal=true';

    tb_show('Cierre de Señal',url,'');
}

function setTabModal(t){
    var tabCie=0,tabDat=1,tabCieR=0,tabMap=1,tabFoto=1,tabNota=0,tabHist=0,tabCama=0,tabOrde=0;;
    switch(t){
        case 1:
            tabCie=1,tabCieR=1,tabNota=1,tabHist=1,tabCama=1,tabOrde=1;
        break;

        case 2:
            tabCie=1,tabCieR=1,tabNota=1,tabHist=1,tabCama=1,tabOrde=1;
        break;

        case 5:
            tabDat=0,tabCieR=1,tabMap=0,tabFoto=0;
        break;
    }

    return "&tabCie="+tabCie+"&tabDat="+tabDat+"&tabCieR="+tabCieR+"&tabMap="+tabMap+"&tabFoto="+tabFoto+"&tabNota="+tabNota+'&tabCama='+tabCama+'&tabHist='+tabHist+'&tabOrde='+tabOrde;
}

function getInfoTabsTramas(o){
    if(o.handler=="SignalsPendientes"){
        SignalsPendientes();
    }
    system.loadXProcesarPlay();

    if(o.proc)
        system.loadProcesadasPlay();
}

function InfoClick(id){
    if(verfiWindow()){
        WinMaps.clickInfo(id)
    }
}


function verfiWindow(){
    if(window.WinMaps!=null){
        if(!window.WinMaps.closed){
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
}

function validateSessionPopup(a){
    if(a=='session'){
        location.href='<?php echo $CONFIG['HOST'];?>';
        window.parent.tb_remove(true);
        return false;
    }else{
        return true;
    }
}

function pasarWindos(){
    var tempClient = [];
    var arrayTempClient = [];

    if(verfiWindow()){
        if (typeof window.WinMaps.ControllerMarker == "function") {

            $("#tbodySigProc tr.pointer").each(function (index) {
                 t=eval('('+$(this).attr("rel-info")+')');
                 if(!arraySearch(tempClient,t.idCliente)){
                    var objs = {
                        iT:t.idTrama,
                        iC:t.idCliente,
                        cuenta:t.Cuenta,
                        nom:t.Nombre,
                        dir:t.dir,
                        ref:t.ref,
                        lat:t.lat,
                        lon:t.lon,
                        img:t.img,
                        telf:t.telf,
                        pic:t.pic,
                        back:t.back,
                        color:t.color,
                        event:t.evento,
                        usrZone:t.eventD
                    }
                    arrayTempClient.push(objs);
                    tempClient.push(t.idCliente);
                 }
            });
            WinMaps.ControllerMarker({aaData:arrayTempClient});
        }
    }
}

function abrirWindosMaps(){
    <?php if($_SESSION["user"]["tipoUser"]==2){ ?>
    if(!verfiWindow()){
        WinMaps = window.open("MapsMonitoreo.php","Monitoreo Maps","height="+window.screen.height+",width="+window.screen.width);
    }else{
        alertNotice({title:"Notificaci&oacute;n",text:"Mapa ya ha sido Cargado"});
        WinMaps.focus();
    }

    <?php } ?>
}

function setEstatusMonitoreo(event){
    event.preventDefault();

    var elemtThat = $("#btn-status-monitoreo");
    var elemt=$("#btn-status-monitoreo>i");

    if(elemt.hasClass('fa-pause')){
        elemt.removeClass("fa-pause");
        elemt.addClass("fa-play");
        elemtThat.attr("title","Activar Monitoreo");

        system.stsMonitoreo = 3;

        //$("#div-tabs-monitoreo").hide();
        $("#div-estatus-monitoreo").show();
    }else{
        elemt.removeClass("fa-play");
        elemt.addClass("fa-pause");
        elemtThat.attr("title","Pausar Monitoreo");

        system.stsMonitoreo = 0;

        $("#div-estatus-monitoreo").hide();
        //$("#div-tabs-monitoreo").show();

    }
}


function heartbeatOpeMonitoreo(){
    <?php if($_SESSION["user"]["idEmpresa"]==$_SESSION["user"]["emp_monitorea"] || $_SESSION["user"]["emp_monitoreaCount"]>0){ ?>
    if(!system.BamloadHeartbeat){

        var _estatusOpe = system.stsMonitoreo;

        if(system.stsMonitoreo!=3){
            if($("#tbodySigProc tr.pointer").length > 0 || $("#tbodySigPendientes tr.pointer").length > 0){
                _estatusOpe = 2; //operador activo con señal
            }else{
                _estatusOpe = 1; //operador desacupado
            }
        }

        system.HrAjaxheartbeat = $.ajax({
            url:'<?php echo $CONFIG['HOST'];?>include/heartbeat_session.php?x='+Math.random(),
            type: "POST",
            timeout:10000,
            error: function (x, t, m) {
                system.BamloadHeartbeat = false;
                system.loadheartbeat();//relanza el heart
            },
            data: {
                acc:'heartbeat',
                statusOpe:_estatusOpe
            },
            cache: false,
            beforeSend:function(){
                system.BamloadHeartbeat = true;
            },
            success:function(data) {
                system.BamloadHeartbeat = false;
            }

        });

    }else{
        system.loadheartbeat();//relanza el heart
    }
    <?php }else{ ?>
        system.BamloadHeartbeat = true;
    <?php } ?>
}


function viewOrdenService(){
    var height = Math.round($(window).height() *0.9);

    var url = 'viewOrdenesServicio.php?height='+height+'&width='+Math.round($(window).width() *0.8)+'&keepThis=true&TB_iframe=true&modal=true';
    tb_show('Crear Orden de Servicio',url,'');
}
</script>

<?php
if($_SESSION["user"]["tipoUser"]==2){
    include("hombre_muerto.php");
}

if($tabsSigProcesadas==1 || $tabsSigTodas==1){
    include("../modulos/generales/comentarios_signal/view.php");
}

?>