<?php
include("../include/scriptdb/config.php");
include("../include/scriptdb/querys.php");
include("../include/phpscript/session.php");
include("../include/phpscript/init.php");
include("../include/phpscript/generales.php");

$query= new Querys();
$conex = $query->getConection($CONFIG);

$alto = $_GET["alto"]-40;
$bamNotas = 0;
$tabsCierre=0;
$tabsCierreRapido=0;
$operador ="";
$operadorName ="";

if($_SESSION["cliente"]["tipoUser"] == 3){
	if($_SESSION["cliente"]["nombre_asociado"]!=""){
	    $operador.= $_SESSION["cliente"]["nombre_asociado"];
	    $operadorName = $_SESSION["cliente"]["nombre_asociado"];
	}

	$operador.=" (".$_SESSION["cliente"]["NameEmpresa"].")";
}else{
	if($_SESSION["user"]["nameOperador"]!=""){
	    $operador.= $_SESSION["user"]["nameOperador"];
	    $operadorName = $_SESSION["user"]["nameOperador"];
	}

	$operador.=" (".$_SESSION["user"]["nombre_empresa"].")";
}



if((PermModAccUser(1,51)  && $_SESSION["user"]["tipoUser"]==2) || $_SESSION["cliente"]["tipoUser"] == 3){
	$tabsCierre=1;
}

if((PermModAccUser(1,array(52,53,54,55,122)) && $_SESSION["user"]["tipoUser"]==2)  || $_SESSION["cliente"]["tipoUser"] == 3){
	$tabsCierreRapido=1;
}


//Variables notas

$NotaFija="";
$NotaTemp="";
$NotaIni="";
$NotaFin="";

$titleModal = "";

if($_GET["fast"]==1){
	$titleModal = "Cierre de Señal Rapido";
}else{
	$titleModal = "Cierre de Señal";
}

//verifica si el cliente tiene notas
if($_GET["q"]!=""){
	//query
	$MyRecordNote = $query->SelDB($conex,"site_sel_NotasClientes",array($_GET["q"]));

	if($query->count_row($MyRecordNote)){
		$rowNote = $query->getdata_object($MyRecordNote);

		$NotaFija=addslashes($rowNote->NotaFija);
		$NotaTemp=addslashes($rowNote->NotaTemp);
		$NotaIni=date_format($rowNote->FechaIni,"d/m/Y");
		$NotaFin=date_format($rowNote->FechaFin,"d/m/Y");

		if($NotaFija!="" || $NotaTemp!=""){
			$bamNotas = 1;
		}
	}
}

//table info
if($_GET["tipo"]==1){
	$tableSeign = "tbodySigProc";

	//se va insertar el primer comentario de señal tomada
	$query->InsDB($conex,"site_ins_TramaObservacion",array(
		"trama"=>trim($_GET["trama"]),
		"observacion"=>trim("Señal tomada por el Operador"),
		"idoperador"=>$_SESSION["user"]["idOperador"]
	));
}else{
	$tableSeign = "tbodySigPendientes";
}

$idCliente = $_GET["q"];

?>
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="<?php echo $CONFIG['HOST'];?>plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $CONFIG['HOST'];?>plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $CONFIG['HOST'];?>plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $CONFIG['HOST'];?>plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>

<link rel="stylesheet" type="text/css" href="<?php echo $CONFIG['HOST'];?>plugins/bootstrap-datepicker/css/datepicker3.css"/>

<link rel="stylesheet" type="text/css" href="<?php echo $CONFIG['HOST'];?>plugins/select2/select2.css"/>

<link href="<?php echo $CONFIG['HOST'];?>css/components.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $CONFIG['HOST'];?>css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $CONFIG['HOST'];?>css/layout.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $CONFIG['HOST'];?>css/themes/<?php echo $CONFIG['WEB_THEME'];?>.css" rel="stylesheet" type="text/css" id="style_color"/>

<link href="<?php echo $CONFIG['HOST'];?>css/custom.css" rel="stylesheet" type="text/css"/>

<script src="<?php echo $CONFIG['HOST'];?>plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script src="<?php echo $CONFIG['HOST'];?>plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="<?php echo $CONFIG['HOST'];?>plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script src="<?php echo $CONFIG['HOST'];?>plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

<script src="<?php echo $CONFIG['HOST'];?>js/jquery.equalHeight.js" type="text/javascript"></script>

<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/select2/select2_locale_es.js"></script>

<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/jquery-validation/js/additional-methods.min.js"></script>

<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/bootstrap-datepicker/js/locales/bootstrap-datepicker.es.js"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>

<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>


<?php include("../js/metronic.js.php");?>
<script src="<?php echo $CONFIG['HOST'];?>js/layout.js" type="text/javascript"></script>
<?php include("../js/apptheme.js.php");?>
<?php include("../js/generales.js.php") ?>
<style>
.datepicker table {
	font-size: 13px !important;
}


</style>
<body>
<div class="modal tickboxs"  tabindex="-1" role="basic"  style="display: block;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onClick="window.parent.tb_remove(true)" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title title-center" id="modal-title" id="title-modal"><?php echo $titleModal;?> <span class="text-center"><?php echo $operador;?> </span></h4>
            </div>
            <div class="modal-body"  id="body-cierre" style="overflow:hidden;padding: 1px;">
            	<?php if($_GET["tabCie"]==1 && $tabsCierre==1){?>
				<div class="row bordered" id="container-datos" style="padding: 3px;">
		            <div class="col-xs-6 content-data">
		            	<div class="row">
		                    <div class="col-xs-2 "><b>EMPRESA:</b></div>
		                    <div class="col-xs-10" id="cierre-empresa"></div>
		                </div>
		                <div class="row">
		                    <div class="col-xs-2 "><b>NOMBRE:</b></div>
		                    <div class="col-xs-10" id="cierre-nombre">
		                        <span class="bold"></span>
		                    </div>
		                </div>
		                <div class="row">
		                    <div class="col-xs-2 "><b>FECHA:</b></div>
		                    <div class="col-xs-10" id="cierre-fecha"></div>
		                </div>
		                <div class="row">
		                    <div class="col-xs-2 "><b>EVENTO:</b></div>
		                    <div class="col-xs-10 text-events" id="cierre-evento" ></div>
		                </div>
		            </div>
		            <div class="col-xs-6 content-data">
		            	<div class="row">
		                    <div class="col-xs-2 "><b>TELEFONO:</b></div>
		                    <div class="col-xs-10 paddleft25" id="cierre-telefono"></div>
		                </div>
		                <div class="row">
		                    <div class="col-xs-2 "><b>DIRECCIÓN:</b></div>
		                    <div class="col-xs-10 paddleft25  "   >
		                    	<div class="inlinex" style="cursor:pointer" id="cierre-direccion"></div>
		                    </div>
		                </div>
		                <div class="row">
		                    <div class="col-xs-2 "><b>REFERENCIA:</b></div>
		                    <div class="col-xs-10 paddleft25" >
		                    	<div class="inlinex"  style="cursor:pointer" id="cierre-referencia"></div>
		                    </div>
		                </div>
		                <div class="row paddbotom5">
		                    <div class="col-xs-2 "><b>STATUS:</b></div>
		                    <div class="col-xs-10 paddleft25" id="cierre-status-panel" >
		                        <span class="bold "></span>
		                        <span class="font-xs"></span>
		                    </div>
		                </div>
		            </div>
		            <div style="margin-left:20px;">
		            	<div data-container="body" class="btn-group  tooltips">
						    <button type="button" id="cie-btn-img-zona-cliente" class="btn btn-sm yellow-gold  dropdown-toggle" data-toggle="dropdown" title="Ver Imagenes"><i class="fa fa-camera "></i></button>
						    <ul class="dropdown-menu" role="menu" id="cie-img-rel-ul">
						    	<li>
						    		<a href="javascript:;"> Cargando... </a>
						    	</li>
						 	</ul>
						</div>
		            	<div data-container="body"  class="btn-group  tooltips">
						    <button type="button"  id="cie-btn-cam-rel" class="btn btn-sm purple  dropdown-toggle" data-toggle="dropdown" title="Ver Camaras"><i class="fa  fa-video-camera "></i></button>
						    <ul class="dropdown-menu" role="menu" id="cie-cam-rel-ul">
						    	<li>
						    		<a href="javascript:;"> Cargando... </a>
						    	</li>
						 	</ul>
						</div>
						<div data-container="body" class="btn-group  tooltips">
						    <button type="button" id="cie-btn-clave-master" class="btn btn-sm blue  dropdown-toggle" data-toggle="dropdown" title="Ver Clave Master"><i class="fa fa-lock "></i></button>
						    <ul class="dropdown-menu" role="menu">
						    	<li>
						    		<a href="javascript:;" id="cie-text-clave-master"> Panel no tiene clave mastee </a>
						    	</li>
						 	</ul>
						</div>
						<div data-container="body" class="btn-group  tooltips">
						    <button type="button" id="cie-btn-llaves-cliente" class="btn btn-sm green  dropdown-toggle" data-toggle="dropdown" title="Ver Llaves"><i class="fa  fa-key "></i></button>
						    <ul class="dropdown-menu" role="menu">
						    	<li>
						    		<a href="javascript:;" id="cie-text-llaves-cliente"> Panel no posee llaves </a>
						    	</li>
						 	</ul>
						</div>

						<div data-container="body" class="btn-group  tooltips form-tag">
						    <button type="button" id="cie-btn-send-sms" class="btn btn-sm red  dropdown-toggle" title="Enviar SMS" data-toggle="dropdown" ><i class="fa  fa-comment "></i></button>
						    <ul class="dropdown-menu" role="menu"  >
						    	<li>
						    		<div style="padding: 10px 15px; *zoom: 1;">
						    			<span>Envia SMS a
						    			<select name="select-tipo-movil" id="select-tipo-movil" onchange="setDataTipoUserMovil(this.value)" class="form-control">
						    				<option value="1">Telefono del Cliente</option>
						    				<option value="2">Usuarios</option>
						    				<option value="3">Contactos</option>
						    			</select>
						    			<select name="select-number-movil" id="select-number-movil" class="form-control">
						    			</select>
					    				<textarea  class="form-control input-medium" placeholder="Escriba el mensaje a enviar" maxlength="160" id="msj-send-text-moni" rows="6"></textarea><br/>
					    				 <button class="btn btn-primary btn-send-sms-moni" type="button" onclick="sendSMSMonitoreo()">
							                <i class="<?php echo getImgIcon("guardar");?>"></i> Enviar
							             </button>
							             <button class="btn btn-send-sms-moni btn-cancel-sms" type="button" onclick="cancelSMSMonitoreo()">
							                <i class="glyphicon glyphicon-remove"></i> Cancelar
							             </button>
					    			</div>
						    	</li>
						 	</ul>
						</div>

						<div data-container="body" class="btn-group  tooltips form-tag">
						    <button type="button" id="cie-btn-send-email" class="btn btn-sm grey-gallery  dropdown-toggle" title="Ver Correo" data-toggle="dropdown" ><i class="fa  fa-envelope"></i></button>
						    <ul class="dropdown-menu" role="menu"  >
						    	<li>
						    		<div style="padding: 10px 15px; *zoom: 1;">
						    			<span>Envia Correo a:
					    				<select name="select-tipo-email" id="select-tipo-email" onchange="setDataTipoUserEmail(this.value)" class="form-control">
						    				<option value="1">Correo del Cliente</option>
						    				<option value="2">Usuarios</option>
						    			</select>
						    			<select name="select-email-send" id="select-email-send" class="form-control">
						    			</select><br/>
						    			<input type="text" class="form-control input-xlarge" placeholder="Asunto" id="asunto-send-mail-moni" maxlength="100"><br/>
					    				<textarea  class="form-control input-xlarge" placeholder="Escriba el correo a enviar" maxlength="160" id="msj-send-mail-moni" rows="6"></textarea><br/>
					    				 <button class="btn btn-primary btn-send-mail-moni" type="button" onclick="sendMailMonitoreo()">
							                <i class="<?php echo getImgIcon("guardar");?>"></i> Enviar
							             </button>
							             <button class="btn btn-send-mail-moni btn-cancel-mail" type="button" onclick="cancelEmailMonitoreo()">
							                <i class="glyphicon glyphicon-remove"></i> Cancelar
							             </button>
					    			</div>
						    	</li>
						 	</ul>
						</div>


		            </div>
		        </div>
		        <?php } ?>
		        <div class="row-fluid">
            		<div class="col-xs-12"  style="padding-left: 0px; padding-right: 0px;">
            			<div class="tabbable-custom ">
            				<ul class="nav nav-tabs <?php echo $CONFIG['WEB_THEME'];?>" id="nav-tab-modal">
            					<?php if($_GET["tabNota"]==1 && $bamNotas==1){ ?>
		                            <li id="li-6" class=" bordered-right link-tab">
                                        <a href="#tabs-cierre-6" id="tabs-notas" data-toggle="tab">Notas</a>
                                    </li>
            					<?php } ?>
            					<?php if($_GET["tabCie"]==1 && $tabsCierre==1){?>
                                     <li id="li-1" class="active bordered-right link-tab">
                                        <a href="#tabs-cierre-1" id="ShowTab1" data-toggle="tab">Cierre</a>
                                    </li>
                                <?php } ?>
                                <?php if($_GET["tabDat"]==1){?>
                               		<li id="li-2" class=" bordered-right link-tab">
                                    	<a href="#tabs-cierre-2" id="tabs-cierre-datos" data-toggle="tab">Datos del Cliente</a>
                                	</li>
                                <?php } ?>
                                <?php if($_GET["tabHist"]==1){?>
                                <li id="li-7" class=" bordered-right link-tab">
                                    <a href="#tabs-cierre-7" id="tabs-cierre-historial" data-toggle="tab">Historial</a>
                                </li>
                                <?php } ?>
                                <?php if($_GET["tabCama"]==1){?>
                                <li id="li-8" class=" bordered-right link-tab">
                                    <a href="#tabs-cierre-8" id="tabs-cierre-camaras" data-toggle="tab">Camaras</a>
                                </li>
                                <?php } ?>
                                <?php if($_GET["tabMap"]==1){ ?>
                                <li id="li-4" class=" bordered-right link-tab">
                                    <a href="#tabs-cierre-4" id="tabs-cierre-maps" data-toggle="tab">Maps</a>
                                </li>
                                <?php } ?>
                                <?php if($_GET["tabFoto"]==1){ ?>
                                <li id="li-5" class=" bordered-right link-tab">
                                    <a href="#tabs-cierre-5" id="tabs-cierre-foto" data-toggle="tab">Foto</a>
                                </li>
                                <?php } ?>
                                <?php if($_GET["tabOrde"]==1 && $tabsCierre==1){?>
                                     <li id="li-1" class="  bordered-right link-tab">
                                        <a href="#tabs-cierre-9" id="tabs-cierre-ordenes" data-toggle="tab">Ordenes</a>
                                    </li>
                                <?php } ?>
                                <?php if($_GET["tabCieR"]==1 && $tabsCierreRapido==1){?>
                                    <li id="li-3" class=" bordered-right link-tab">
                                        <a href="#tabs-cierre-3" id="tabs-cierre-rapido" data-toggle="tab">Cierre Rapido</a>
                                    </li>
                                <?php } ?>
            				</ul>
            				<div class="tab-content bbtom0" id="cont-tabsCierre">
            					<?php if($_GET["tabCie"]==1 && $tabsCierre == 1){
        							include("tabs/tab-cierre.php");
            					}

            					if($_GET["tabDat"]==1){
                                	include("tabs/tab-datos-cliente.php");
                            	}

                                if($tabsCierreRapido==1 && $_GET["tabCieR"]==1){
                                    include("tabs/tab-cierre-rapido.php");
                                }

                                if($_GET["tabMap"]==1){
                                	include("tabs/tab-mapa.php");
                                }

                                if($_GET["tabFoto"]==1){
                                	include("tabs/tab-foto.php");
                                }

                                if($_GET["tabNota"]==1 && $bamNotas==1){
                                    include("tabs/tab-notas.php");
                                }

                                if($_GET["tabHist"]==1){
                                	include("tabs/tab-historial.php");
                                }

                                if($_GET["tabCama"]==1){
                            		include("tabs/tab-camaras.php");
                                }


                                 if($_GET["tabOrde"]==1 && $tabsCierre==1){
                            		include("tabs/tab-ordenes.php");
                                }
                                ?>
            				</div>
            			</div>
            		</div>
            	</div>
            </div>
            <div class="modal-footer"></div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
</body>
<script>
	var obj = window.parent.system.tempObjCierre;

	var systems = {
		tabDatosClie:false,
		tabDatosMaps:false,
		AjaxDatosClient:null,
		bamAjaxComent:false,
		bamAjaxSendSMS:false,
		bamAjaxSendMail:false,
		id_info:[],
        id_infoClass:[],
		id_aux:[],
		tipoCierre:-1,
		Subacc:"",
        id_cliente:0,
        event:""
	}


	$(document).ready(function(){

		$(document).on('click', '.form-tag .dropdown-menu', function(e) {
          e.stopPropagation()
        })

		window.parent.system.loadXProcesarStop();
		window.parent.InfoClick(obj.idCliente);
		window.parent.$("#audio_emerg")[0].pause();

		setInfoTrama();
		setFoto();
		initBottomAction();


		$("#body-cierre").css("height",<?php echo $alto;?>);
		$("#body-cierre").css("max-height",<?php echo $alto;?>);
		$("#tablesignal").css("height",<?php echo $alto;?>-Math.round(<?php echo $alto;?> * 0.72));

		setTimeout(function(){
			$("#nav-tab-modal li:first a").click();
		},50);

        setTimeout(function(){
        	$(".content-data").equalHeight();
        },500);

        setTimeout(function(){
            var altoSignalClient = <?php echo $alto;?>-$("#container-datos").height()-Math.round(<?php echo $alto;?> * 0.5);
            $("#tablesignalClient,#tablesignalHistory").css("height",altoSignalClient);
            $("#tablesignalClient,#tablesignalHistory").css("max-height",altoSignalClient);
        },500);


		<?php
			if($_GET["tabNota"]==1 && $bamNotas==1){ //si tiene notas se muestra al inicio
				?>
					setTimeout(function(){
                        $("#tabs-notas").click();
                        setTimeout(function(){
                            $('.divDialog').equalHeight();
                        },500);
                    },700);
				<?php
			}
		?>

        <?php if($_GET["tabCieR"]==1 && $tabsCierreRapido==1){ //si se puede cerrar multiple?>
            $("#tabs-cierre-rapido").click(function(){
                $(".radio-cierre:first").click();
            });
        <?php } ?>

		$("#tabs-cierre-datos").click(function() {
			DataClick();
			var height_TabData = <?php echo $alto;?>-$("#container-datos").height()-110;
			var height_TabData2 = (height_TabData/2)-35;

			setTimeout(function(){
				$(".lista-data-user").css({
					"height":height_TabData2,
					"max-height":height_TabData2
				});
				$("#div-num-data").css({
					"height":height_TabData,
					"max-height":height_TabData
				});
			},500);
		});

		$("#tabs-cierre-maps").click(function() {
			if(!systems.tabDatosMaps){
				systems.tabDatosMaps = true;
				var iframe='<iframe src="mapsCliente.php?q='+obj.codi+'" width="100%" height="70%"';
				 iframe+=' marginheight="0" marginwidth="0" noresize scrolling="No" frameborder="0"></iframe>'
				$("#tabs-cierre-4").html(iframe)
			}
		});

		//tab se señales
		$("#tabs-cierre-historial").click(function(event) {
			var height_TabData = <?php echo $alto;?>-$("#container-datos").height()-110;
			setTimeout(function(){
				$("#body-signal-cliente").css({
					"height":height_TabData,
					"max-height":height_TabData
				});
			},500);
			initialize_table_signal();
		});

		//tab de camaras
		$("#tabs-cierre-camaras").click(function(event) {
			var height_TabData = <?php echo $alto;?>-$("#container-datos").height()-110;
			setTimeout(function(){
				$("#body-camara-cliente").css({
					"height":height_TabData,
					"max-height":height_TabData
				});
			},500);
			initialize_table_camaras();
		});

		//tab ordenes
		$("#tabs-cierre-ordenes").click(function(event) {
			var height_TabData = <?php echo $alto;?>-$("#container-datos").height()-110;
			setTimeout(function(){
				$("#body-ordenes-cliente").css({
					"height":height_TabData,
					"max-height":height_TabData
				});
			},500);
			initialize_table_ordenes();
		});

		Metronic.initAjax();
	});

	//pasar a pendienete
	function setPendiente(){
		window.parent.sendPendiente(obj.pre+obj.idTrama,$("#cierre-comentario").val());
		window.parent.tb_remove(true);
	}

	//function para setear informacion de cieere de señal
	function setInfoTrama(){
		//seta la info en el tabs de cierre
		var detalle = obj.eventD =="" ? "" : "("+obj.eventD+")"
		$("#text-nom-datos-cliente").html(obj.Cuenta+" - "+obj.Nombre);
		$("#cierre-nombre span:first").html(obj.Cuenta+" - "+obj.Nombre);
		$("#cierre-fecha").html(obj.fecha);
		$("#cierre-telefono").html(obj.telf=="" ? "Sin Telefono" : obj.telf);
		$("#select-number-movil").append("<option value='"+obj.movil+"'>"+obj.movil+"</option>");

		$("#select-email-send").append("<option value='"+obj.email+"'>"+obj.email+"</option>");


		$("#cierre-empresa").html(obj.emp);
		$("#cierre-evento").html(obj.evento+" "+detalle);
		$("#cierre-evento").css({
			"background":obj.back,
			"color":obj.color
		});
		$("#cierre-id").val(obj.idTrama);


		$("#cierre-direccion").html(obj.dir);
		$("#cierre-direccion").attr("title",obj.dir);

		$("#cierre-referencia").html(obj.ref=="" ? "Sin referencia" : obj.ref);
		$("#cierre-referencia").attr("title",obj.ref=="" ? "Sin referencia" : obj.ref);

		$("#cierre-status-panel span:first").html(obj.stsPanel);
		$("#cierre-status-panel span:first").css("color",obj.stsPanelC);
		$("#cierre-status-panel span:last").html("&nbsp;("+obj.stsPanelF+")");

        setTramasCliente();
    }

    function initBottomAction(){
    	var disBtnClaveMaster = true;
    	if(obj.claveMaster!=""){
    		disBtnClaveMaster = false;

    		$("#cie-text-clave-master").html("<b>Clave Master:</b> "+obj.claveMaster);
    	}
    	$("#cie-btn-clave-master").attr("disabled",disBtnClaveMaster);

    	var disBtnLlavesCliente = true;
    	if(obj.llave!=""){
    		disBtnLlavesCliente = false;

    		$("#cie-text-llaves-cliente").html("<b>Llaves:</b> "+obj.llave);
    	}
    	$("#cie-btn-llaves-cliente").attr("disabled",disBtnLlavesCliente);

    	var disBtnListCam = true;
    	var clickBtnListCam = "";
    	if(obj.tipoevent=="1" && obj.idDisp!=""){
    		disBtnListCam = false;
    		clickBtnListCam = "CieloadListCam()"
    	}
    	$("#cie-btn-cam-rel").attr({
    		"disabled":disBtnListCam,
    		"onclick":clickBtnListCam
    	});

    	var disBtnListImgZona = true;
    	var clickBtnListImgZona = "";
    	if(obj.tipoevent=="1" && obj.idDisp!=""){
    		disBtnListImgZona = false;
    		clickBtnListImgZona = "CieLoadImagZona()"
    	}
    	$("#cie-btn-img-zona-cliente").attr({
    		"disabled":disBtnListImgZona,
    		"onclick":clickBtnListImgZona
    	});

    	if(obj.movil==""){
    		$("#cie-btn-send-sms").attr("disabled",true);
    	}

    }

    function CieloadListCam(){
    	if($("#cie-cam-rel-ul li.cie-list-cam-item").length>0){ return false; }
    	var param = "x="+Math.random()+"&q="+obj.idDisp+"&acc=load_cam_cliente_moni&idCliente=<?php echo $_REQUEST['q']?>";
        $("#cie-cam-rel-ul").load('controller.php',param,function(response,status,xhr){});
    }

    function CieLoadImagZona(){
    	if($("#cie-img-rel-ul li.cie-list-img-item").length>0){ return false; }
    	var param = "x="+Math.random()+"&q="+obj.idDisp+"&acc=load_img_cliente_moni&idCliente=<?php echo $_REQUEST['q']?>";
        $("#cie-img-rel-ul").load('controller.php',param,function(response,status,xhr){});
    }

    function viewImgZonaOpen(_name,i){
    	var idxTrama = Base64.encode(obj.idTrama);

    	var params = [
	        'height='+485,
	        'width='+645,
	        "scrollbars=1" // only works in IE, but here for completeness
	    ].join(',');

	    setComentTableSignal('Visualizo la foto: Imagen'+i);

	    window.parent.system.windowMoniImgZona = window.open("<?php echo $CONFIG['HOST'];?>file.php?type=<?php echo encode(5,"view_img_zona");?>&file="+_name+"&t=<?php echo encode64_asp(1);?>&tr="+idxTrama+"&i="+i,"_blank",params,false);
    }

    function viewImgUserOpen(_name){
    	var params = [
	        'height='+205,
	        'width='+205,
	        "scrollbars=1" // only works in IE, but here for completeness
	    ].join(',');

	    window.parent.system.windowMoniImgUser = window.open("<?php echo $CONFIG['HOST'];?>file.php?type=<?php echo encode(5,"view_img_user_cliente");?>&file="+_name,"_blank",params,false);
    }


	function setMenPredefinido(m,i){
		if(m!=''){
			$("#"+i).val(m);
		}
	}

	function cierreSignal(ts){

		var coment = $("#cierre-comentario").val();
		var pref = obj.pre;
		var detalle = "";

        if(coment==""){
        	window.parent.alertError({title:"Error",text:"Debe escribir una observacion al cerrar señal"});
            return false;
        }

        if(ts=="1"){
            var formatClose = "cierre-one";
            var idlog = 99;
        }else{
            var formatClose = "cierre-one-pendiente";
            var idlog = 102;
        }

        if($(".check-id-cierre:checked").length>0){
        	 $.each($(".check-id-cierre:checked"), function(index, elm) {
        	 	var id = $(elm).val();
        	 	systems.id_info.push(id);
				systems.id_infoClass.push("#"+obj.pre+id);
        	 });

        	 detalle = "Cierre multiple";
        }else{
        	var id = $("#cierre-id").val();

        	systems.id_info.push(id);
			systems.id_infoClass.push("#"+obj.pre+id);

			detalle = obj.evento+" "+obj.eventD;
        }

		$.ajax({
			url:'controller.php?x='+Math.random(),
			type: "POST",
			timeout:60000,
			error: function(x, t, m) {
				window.parent.alertError({title:"Notificaci&oacute;n",text:"Error cerrar se&ntilde;al"})
				window.parent.tb_remove(true);
			},
			data: {
				acc:'cierre-signal',
				subAcc:formatClose,
				id:systems.id_info.toString(),
                det:obj.detalle,
                idC:obj.idCliente,
				coment:coment,
                idac:idlog
			},
			cache: false,
			beforeSend:function(){
				$("#btn-cancel-cierre,#btn-save-cierre").attr("disabled",true);
				$("#text-btn-save").text("Enviando...");
			},
			success:function(data) {
				if(window.parent.validateSessionPopup(data)){
					window.parent.getInfoTabsTramas({handler:obj.handler,proc:true});
					window.parent.alertSuccess({title:"Notificaci&oacute;n",text:"Se&ntilde;al cerrada exitosamente."});

                    if(pref=="PS-"){//si es una señal por procesar
                        window.parent.removeTrTable(systems.id_infoClass.toString());
                    }else{
                        window.parent.$(systems.id_infoClass.toString()).fadeTo(400, 0, function () {
                            $(this).remove();
                        });
                    }

                    $("#text-btn-save").text("Procesar");
                    $("#btn-cancel-cierre,#btn-save-cierre").attr("disabled",false);

					window.parent.tb_remove(true);
				}
			}

		});
	}

	function DataClick(){
		if(!systems.tabDatosClie){
			systems.tabDatosClie = true;
			load_data_client();
		}
	}

	function load_data_client(){
		$.ajax({
			url:'controller.php?x='+Math.random(),
			timeout:60000,
			dataType : 'json',
			error: function(x, t, m) {
				window.parent.alertError({title:"Notificaci&oacute;n",text:"Error al mostrar datos"})
				window.parent.tb_remove(true);
			},
			data: {
				acc:'load_data_cliente',
				id:obj.idCliente
			},
			cache: false,
			beforeSend:function(){
				$("#load_datos_cliente").show();
				$("#table-datos-clientes").hide();
			},
			success:function(data) {
				if(window.parent.validateSessionPopup(data)){
					//se imprime los numeros de emergenci
					var html2="";
					if(data.numEmg.length==0){
						$("#tbody-nummerge").html('<tr ><td align="center" colspan="3"><b style="font-size:13px">No se encontraron registros</b></td></tr>');
					}else{
						$.each(data.numEmg, function(index, val) {
							html2+="<tr ><td>"+val.descript+"</td><td><a href='tel:"+val.numero+"'>"+val.numero+"</a></td><td>"+val.ob+"</td></tr>";

						});
						$("#tbody-nummerge").html(html2);
					}
					//FIn numero de emergencia

					//imprime los usuarios del cliente
					var html1 = "";
					if(data.users.length==0){
						$("#tbody-usuarios-cli").html('<tr ><td align="center" colspan="5"><b style="font-size:13px">No se encontraron registros</b></td></tr>');
					}else{
						$.each(data.users, function(index, val) {
							var imgUser = "";

							if(val.img!=""){
								imgUser="<a class='btn <?php echo getClassIcon("add_imagen");?>'  href='javascript:void(0)' ";
								imgUser+=" onclick=\"viewImgUserOpen('"+val.img+"')\"   title='Ver Foto'> <i class='<?php echo getImgIcon("camaras");?>'></i></a>";
							}


							html1+="<tr ><td>"+val.id+"</td><td>"+val.nom+"</td><td>"+val.movil+"</td><td>"+val.clavevoz+"</td><td>"+val.parents+"</td><td>"+imgUser+"</td></tr>";

						});
						$("#tbody-usuarios-cli").html(html1);
					}
					//usuarios del cliente

					//imprime las zonas del cliente
					var html3 = "";
					if(data.zonas.length==0){
						$("#tbody-zonas-cli").html('<tr ><td align="center" colspan="5"><b style="font-size:13px">No se encontraron registros</b></td></tr>');
					}else{
						$.each(data.zonas, function(index, val) {
							html3+="<tr><td>"+val.id_zona+"</td><td>"+val.zona+"</td><td>"+val.ubi+"</td></tr>";

						});
						$("#tbody-zonas-cli").html(html3);
					}
					//zonas del cliente

					Metronic.initAjax();
					//FIN ultimas

					$("#load_datos_cliente").hide();
					$("#table-datos-clientes").show();
				}
			}

		});
	}

	function toogleClaveUser(e){
		$(".clave-voz-"+e).toggle();
	}

    function returnEventD(d){
        if(d!="") return " ("+d+")";

        return "";
    }

	function setTipo(t){

		systems.tipoCierre = t;
		$("#btn-procesar").attr("disabled",true);
        $("#msj-enviado").show();

		systems.id_info=[];
        systems.id_infoClass=[];
		systems.id_aux=[];
        systems.id_cliente = 0;
        systems.event = "";

		switch(t){
			case 1:

				$("#btn-procesar").attr("disabled",false);
				var html ='</th><th   align="left"><b>Cliente</b>';
				html+='</th><th   align="left"><b>Se&ntilde;al</b></th><th   align="left"><b>Fecha</b></th>';

				var tbody = "<tr style='"+obj.style+"'><td>&nbsp;"+obj.Cuenta+" - "+obj.Nombre;
                tbody+="</td><td>"+obj.evento;
                tbody+=returnEventD(obj.eventD);

                tbody+="</td><td>"+obj.fecha+"</td></tr>";
                systems.id_cliente = obj.idCliente;
                systems.event = obj.evento +" "+obj.eventD;
				systems.id_info.push(obj.idTrama);
                systems.id_infoClass.push("#"+obj.pre+obj.idTrama);
			break;
			case 2:

				var html ='<th align="left"  width="25"><input type="checkbox" id-class="check-id" value="1" onclick="check_all(this)" /></th><th   align="left"><b>ID</b></th>';
				html+='<th   align="left"><b>Nombre</b></th><th   align="left"><b>Se&ntilde;al</b></th>';
				var tbody="";
				window.parent.$("#<?php echo $tableSeign;?> tr.pointer").each(function (index) {
					 t=eval('('+$(this).attr("rel-info")+')');
					 if(!arraySearch(systems.id_aux,t.idCliente)){
						 tbody+="<tr style='"+t.style+"'><td align='left'><input type='checkbox' value='"+t.idCliente+"' class='check-id' /></td><td>"+t.Cuenta+" ";
						 tbody+="</td><td>&nbsp;"+t.Nombre+"</td><td>"+t.evento+returnEventD(t.eventD)+"</td></tr> ";
						 systems.id_aux.push(t.idCliente);
					 }
				});

			break;
			case 3:

				var html ='<th align="left"><input type="checkbox" id-class="check-id" value="" class="check-all" onclick="check_all(this)" /></th><th   align="left"><b>ID</b></th>';
				html+='<th    align="left"><b>Evento</b>';

				var tbody="";
				window.parent.$("#<?php echo $tableSeign;?> tr.pointer").each(function (index) {
					 t=eval('('+$(this).attr("rel-info")+')');
					 if(!arraySearch(systems.id_aux,t.idEvento)){
						 tbody+="<tr style='"+t.style+"'><td align='left' width='5%'><input type='checkbox' value='"+t.idEvento+"' class='check-id' />";
						 tbody+="<td align='left'>"+t.idEvento+"</td><td align='left'>&nbsp;"+t.evento+" </tr> ";
						 systems.id_aux.push(t.idEvento);
					 }
				});

			break;
			case 4:

				var html ='<th   align="left"><b>Cliente</b></th><th   align="left"><b>Se&ntilde;al</b></th>';
				html+='<th   align="left"><b>Fecha</b></th>';
				var tbody="";
				window.parent.$("#<?php echo $tableSeign;?> tr.pointer").each(function (index) {
					 t=eval('('+$(this).attr("rel-info")+')');
					 tbody+="<tr style='"+t.style+"'><td>&nbsp;"+t.idCliente+"-";
					 tbody+=""+t.Nombre+"</td><td>"+t.evento+returnEventD(t.eventD)+"</td><td>"+t.fecha+"</td></tr>";
					 systems.id_info.push(t.idTrama);
				});

			break;
			case 5:

				var html ='<th align="left"><input type="checkbox" value=""  id-class="check-id" class="check-all" onclick="check_all(this)" /></th><th   align="left"><b>Codigo</b></th>';
				html+='<th    align="left"><b>Descripci&oacute;n</b>';

				var tbody="";
				window.parent.$("#<?php echo $tableSeign;?> tr.pointer").each(function (index) {
					 t=eval('('+$(this).attr("rel-info")+')');
					 if(t.codA.trim()!=""){
						 if(!arraySearch(systems.id_aux,t.codA)){
							 tbody+="<tr style='"+t.style+"'><td align='left' width='5%'><input type='checkbox' value='"+t.codA+"' class='check-id' />";
							 tbody+="<td align='left'>"+t.codA+"</td><td align='left'>&nbsp;"+t.codDesc+" </tr> ";
							 systems.id_aux.push(t.codA);
						 }
					 }
				});

			break;
		}

		$("#tr-cierre").html(html);
		$("#tbody-cierres").html(tbody);
		$("#btn-procesar").attr("disabled",false);
        $("#msj-enviado").hide("slow");
	}

    function setTramasCliente(){
        var tbody="";
        window.parent.$("#<?php echo $tableSeign;?> tr.pointer").each(function (index) {
            t=eval('('+$(this).attr("rel-info")+')');
            if(obj.idCliente==t.idCliente){
                tbody+="<tr style='"+t.style+"'>";
                tbody+="<td><input type='checkbox' class='check-id-cierre' value='"+t.idTrama+"' /></td>";
                tbody+="<td>"+t.fecha+"</td>";
                tbody+="<td>"+t.evento;

                if(t.eventD!=""){
                    tbody+=" ("+t.eventD+") ";
                }

                tbody+="</td>";
                tbody+="</tr>";
            }
        });

        if(tbody!=""){
            $("#tbody-signal-client").html(tbody);
        }
    }

	function check_all(a){
		var st = a.checked;
		var id_class= $(a).attr("id-class");
		$("."+id_class).prop("checked",st);

		if(!st)
			systems.id_info=[];
	}

	function getId(){
		var casAx = parseInt(systems.tipoCierre);
		systems.id_info = [];

		switch(casAx){
			case 2:
				$(".check-id:checked").each(function(){
                    var ClienteAux = $(this).val();
					window.parent.$("#<?php echo $tableSeign;?> tr.pointer").each(function (index) {
                        t=eval('('+$(this).attr("rel-info")+')');
                        if(ClienteAux==t.idCliente){
                            systems.id_info.push("'"+t.idTrama+"'");
                            systems.id_infoClass.push("#"+t.pre+t.idTrama);
                        }
                    });
				});
			break;
			case 3:
				systems.id_aux=[];
				$(".check-id:checked").each(function(){
					var evns = $(this).val();
					window.parent.$("#<?php echo $tableSeign;?> tr.pointer").each(function (index) {
						t=eval('('+$(this).attr("rel-info")+')');
						if(evns==t.idEvento.trim()){
							systems.id_info.push("'"+t.idTrama+"'");
                            systems.id_infoClass.push("#"+t.pre+t.idTrama);
						}
					});

				});
			break;
            case 4:
                systems.id_aux=[];
                window.parent.$("#<?php echo $tableSeign;?> tr.pointer").each(function (index) {
                    t=eval('('+$(this).attr("rel-info")+')');
                    systems.id_info.push("'"+t.idTrama+"'");
                    systems.id_infoClass.push("#"+t.pre+t.idTrama);

                });
            break;
			case 5:
				systems.id_aux=[];
				$(".check-id:checked").each(function(){
					var evns = $(this).val();
					window.parent.$("#<?php echo $tableSeign;?> tr.pointer").each(function (index) {
						t=eval('('+$(this).attr("rel-info")+')');
						if(evns==t.codA.trim()){
							systems.id_info.push("'"+t.idTrama+"'");
                            systems.id_infoClass.push("#"+t.pre+t.idTrama);
						}
					});

				});
			break;
		}
	}

    function prepareVarCierre(){
        var detail = "";
        var idClienteAux = 0;
        var idacAux = "100";

        switch(systems.tipoCierre){
            case 1:
                var subAcx = "cierre-one";
                var tAux = eval('('+$(this).attr("rel-info")+')');
                idClienteAux = systems.id_cliente;
                detail =  systems.event;
                idacAux = 0;
            break;
            case 2:
                var subAcx = "cierre-cliente";
                detail = "Por Clientes";
            break;
            case 3:
                var subAcx = "cierre-evento";
                detail = "Por Eventos";
            break;
            case 4:
                var subAcx = "cierre-todas";
                detail = "Todas las Señales";
            break;
            case 5:
                var subAcx = "cierre-codigo";
                detail = "Por Codigo de Alarma";
            break;
        }

        var idDat = getId();

        var datox = {
            acc:'cierre-signal',
            subAcc:subAcx,
            id:systems.id_info.toString(),
            coment:$("#cierre-comentario-2").val(),
            idC:idClienteAux,
            det:detail,
            idac:idacAux
        }

        return datox;
    }

	function procesar_cierre(){
		var coment = $("#cierre-comentario-2").val();
		var datos = prepareVarCierre();

		if(coment==""){
			$("#text-error").text("Debe escribir una observacion al cerrar señal");
			$("#div-error").show();
			return false;
		}

		if(systems.tipoCierre==-1 || systems.id_info.toString()==""){
			$("#text-error").text("Debe Seleccionar que Señales desea cerrar?");
			$("#div-error").show();
			return false;
		}

		$.ajax({
			url:'controller.php?x='+Math.random(),
			type: "POST",
			timeout:60000,
			error: function(x, t, m) {
				window.parent.alertError({title:"Notificaci&oacute;n",text:"Tenemos Inconvenientes de comunicacion con el servidor Intente luego"})
				window.parent.getInfoTabsTramas({handler:obj.handler,proc:false});
				window.parent.tb_remove(true);
			},
			data: datos,
			cache: false,
			beforeSend:function(){
				$("#btn-procesar").attr("disabled",true);
                $("#msj-enviado").show();
				$("#text-error").text();
				$("#div-error").hide();
			},
			success:function(data) {
				if(window.parent.validateSessionPopup(data)){
                    window.parent.removeTrTable(systems.id_infoClass.toString());
					window.parent.getInfoTabsTramas({handler:obj.handler,proc:false});
					window.parent.tb_remove(true);
				}
			}
		});
	}

	function setFoto(){
		var pic = obj.pic;
		var urlAux = "<?php echo $CONFIG['HOST'];?>img/not_found.jpg";
		if(pic!=""){
			urlAux = "<?php echo $CONFIG['HOST'];?>img/img_c/"+pic;
		}
		$("#imgF").attr("src",urlAux);
	}

	function addObservacion(){
		var _trama = obj.idTrama;
		var _coment = $("#cierre-comentario").val();

		if(!systems.bamAjaxComent){

			if(_coment==""){
				window.parent.alertError({title:"Notificaci&oacute;n",text:"Debe ingresar una observación."});
				return false;
			}

			$.ajax({
		        url:'controller.php?x='+Math.random(),
		        type: "POST",
		        timeout:60000,
		        data: {
		            acc:'add_comentario',
		            id:_trama,
		            coment:_coment
		        },
		        cache: false,
		        error: function (x, t, m) {
		            systems.bamAjaxComent =  false;
		            btnPendienteStado(1)
		        },
		        beforeSend:function(){
		        	systems.bamAjaxComent =  true;
		        	btnPendienteStado(0)
		        },
		        success:function(data) {
		            systems.bamAjaxComent = false;
		            btnPendienteStado(1);
		            window.parent.tb_remove(true);

		            window.parent.alertSuccess({title:"Notificaci&oacute;n",text:"Observación agregada exitosamente."});
		        }

		    });
		}
	}

	function btnPendienteStado(st){
		var _btns = $("#tb-cierre-btn-pendiente,#tb-cierre-btn-cancelar,#tb-cierre-btn-procesar");
		var _btn = $("#tb-cierre-btn-pendiente");
		if(st){
			_btns.attr("disabled",false);
			_btn.find("i").removeClass("fa-spin fa-spinner").addClass("fa-eye");
		}else{
			_btns.attr("disabled",true);
			_btn.find("i").removeClass("fa-eye").addClass("fa-spin fa-spinner");

		}
	}

	function sendSMSMonitoreo(){
		var _sms = $("#msj-send-text-moni").val();
		var _num = $("#select-number-movil").val();

		if(!systems.bamAjaxSendSMS){
			if(_num==""){
				window.parent.alertError({title:"Error",text:"Seleccione Destinatario del Mensaje"});
				return false;
			}

			if(_sms==""){
				window.parent.alertError({title:"Error",text:"Ingrese el texto del Mensaje"});
				return false;
			}

			$.ajax({
		        url:'controller.php?x='+Math.random(),
		        type: "POST",
		        timeout:60000,
		        data: {
		            acc:'send_sms_monitoreo',
		            sms:_sms,
		            id_cliente:obj.idCliente,
		            numero:_num,
		            id:obj.idTrama
		        },
		        cache: false,
		        error: function (x, t, m) {
		            systems.bamAjaxSendSMS =  false;
		            btnsSendSMSStado(1)
		        },
		        beforeSend:function(){
		        	systems.bamAjaxSendSMS =  true;
		        	btnsSendSMSStado(0)
		        },
		        success:function(data) {
		            systems.bamAjaxSendSMS = false;
		            btnsSendSMSStado(1);
		            setComentTableSignal("Se envio SMS: "+_sms);
		            $(".btn-cancel-sms").click();
		            window.parent.alertSuccess({title:"Notificaci&oacute;n",text:"SMS enviado exitosamente."});
		        }

		    });
		}else{
			window.parent.alertError({text:"Se esta enviando el SMS anterior"});
		}

	}

	function btnsSendSMSStado(st){

		if(st==1){
			$(".btn-send-sms-moni").attr("disabled",false);
		}else{
			$(".btn-send-sms-moni").attr("disabled",true);
		}
	}

	function cancelSMSMonitoreo(){
		$("#msj-send-text-moni").val("")
		$("#cie-btn-send-sms").parent().removeClass('open');
	}

	function sendMailMonitoreo(){
		var _texto = $("#msj-send-mail-moni").val();
		var _asunto = $("#asunto-send-mail-moni").val();
		var _email = $("#select-email-send").val();


		if(!systems.bamAjaxSendMail){
			if(_email==""){
				window.parent.alertError({title:"Error",text:"Seleccione Destinatario del Correo"});
				return false;
			}

			if(_asunto==""){
				window.parent.alertError({title:"Error",text:"Ingrese el Asunto del Correo"});
				return false;
			}

			if(_texto==""){
				window.parent.alertError({title:"Error",text:"Ingrese el texto del Correo"});
				return false;
			}



			$.ajax({
		        url:'controller.php?x='+Math.random(),
		        type: "POST",
		        timeout:60000,
		        data: {
		            acc:'send_email_monitoreo',
		            texto:_texto,
		            id_cliente:obj.idCliente,
		            mail:_email,
		            asunto:_asunto,
		            id:obj.idTrama
		        },
		        cache: false,
		        error: function (x, t, m) {
		            systems.bamAjaxSendMail =  false;
		            btnsSendMailStado(1)
		        },
		        beforeSend:function(){
		        	systems.bamAjaxSendMail =  true;
		        	btnsSendMailStado(0)
		        },
		        success:function(data) {
		            systems.bamAjaxSendMail = false;
		            btnsSendMailStado(1);
		            setComentTableSignal("Se envio Correo: "+_texto);
		            $(".btn-cancel-mail").click();
		            window.parent.alertSuccess({title:"Notificaci&oacute;n",text:"Correo enviado exitosamente."});
		        }

		    });
		}else{
			window.parent.alertError({text:"Se esta enviando el Correo anterior"});
		}
	}

	function btnsSendMailStado(st){

		if(st==1){
			$(".btn-send-mail-moni").attr("disabled",false);
		}else{
			$(".btn-send-mail-moni").attr("disabled",true);
		}
	}

	function cancelEmailMonitoreo(){
		$("#msj-send-mail-moni,#asunto-send-mail-moni").val("")
		$("#cie-btn-send-email").parent().removeClass('open');
	}

	function setComentTableSignal(_comment){
		var _table = $("#tbody-coment-signal");

		var currentdate = new Date();
		var datetime = padL(currentdate.getDate(),2) + "/"
                + padL((currentdate.getMonth()+1),2)  + "/"
                + currentdate.getFullYear() + " "
                + padL(currentdate.getHours(),2) + ":"
                + padL(currentdate.getMinutes(),2) + ":"
                + padL(currentdate.getSeconds(),2);


        var _htmlx = "<tr>"+
        	"<td>"+datetime+"</td>"+
        	"<td>"+_comment+"</td>"+
        	"<td><?php echo $operadorName;?></td>"+
        	"</tr>";

        _table.prepend(_htmlx);
	}

	function setDataTipoUserMovil(t){
		$("#select-number-movil").find('option').remove().end();

		var optionHtml = "";

		if(t!="1"){
			$("#select-number-movil").attr("disabled",true).append("<option value=''>CARGANDO...</option>");

			$.getJSON('controller.php?x='+Math.random()+'&cl='+obj.idCliente+'&t='+t+'&acc=loadNumberUserContact',function(datax){
				$("#select-number-movil").find('option').remove().end();

				if(datax==null){
					$("#select-number-movil").attr("disabled",false).append("<option value=''>Sin registros</option>");
					return false;
				}

			    $.each(datax, function(index, val) {
			        optionHtml+= "<option value='"+val.movil+"'>"+val.nom+' - '+val.movil+"</option>";

			    });

			    $("#select-number-movil").attr("disabled",false).append(optionHtml);
			});

		}else{
			optionHtml+= "<option value='"+obj.movil+"'>"+obj.movil+"</option>";
			$("#select-number-movil").append(optionHtml);
		}
	}

	function setDataTipoUserEmail(t){
		$("#select-email-send").find('option').remove().end();

		var optionHtml = "";

		if(t!="1"){
			$("#select-email-send").attr("disabled",true).append("<option value=''>CARGANDO...</option>");

			$.getJSON('controller.php?x='+Math.random()+'&cl='+obj.idCliente+'&t='+t+'&acc=loadEmailUserContact',function(datax){
				$("#select-email-send").find('option').remove().end();

				if(datax==null){
					$("#select-email-send").attr("disabled",false).append("<option value=''>Sin registros</option>");
					return false;
				}

			    $.each(datax, function(index, val) {
			        optionHtml+= "<option value='"+val.email+"'>"+val.nom+' - '+val.email+"</option>";

			    });

			    $("#select-email-send").attr("disabled",false).append(optionHtml);
			});

		}else{
			optionHtml+= "<option value='"+obj.email+"'>"+obj.email+"</option>";
			$("#select-email-send").append(optionHtml);
		}
	}

	$("#nav-tab-modal li:first a").click();
</script>