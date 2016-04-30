<?php
include("../include/scriptdb/config.php");
include("../include/scriptdb/querys.php");
include("../include/phpscript/session.php");
include("../include/phpscript/init.php");
include("../include/phpscript/generales.php");


$query= new Querys();
$conex = $query->getConection($CONFIG);

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

$alto = $_GET["height"]-40;

$titleModal = "Crear Orden de Servicio";



?><link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
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
<div class="modal tickboxs"  tabindex="-1" role="basic"  style="display: block;position: initial;" aria-hidden="true"  data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onClick="window.parent.tb_remove(true)" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title title-center" id="modal-title" id="title-modal"><?php echo $titleModal;?> <span class="text-center"><?php echo $operador;?> </span></h4>
            </div>
            <div class="modal-body"  id="body-view-ordenes">
		        <div class="row" style='padding-left: 0px; padding-right: 0px;'>
					<div class="col-md-12">
						<div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa marginTop1 fa-tags"></i>
									<span>Datos Orden de Servicio</span>
								</div>
							</div>
							<div class="portlet-body" id="body-ordenes-cliente" style="overflow-y:auto;overflow-x:hidden;height:400px">
								 <form class="form-horizontal" id="form-add-ordenes" onsubmit="return false;"  >
				                    <div class="form-body">
										<div class="form-group">
				                            <label class="col-md-3 control-label" for="vist_client">Nombre Cliente:
				                            </label>
				                            <div class="col-md-7">
				                                 <input type="hidden" name="vist_client" id="vist_client" class="form-control"/>
                                                    <input type="hidden" name="vist_tipoc"  id="vist_tipoc" class="form-control"/>
                                                    <input type="hidden" name="vist_id_empresa"  id="vist_id_empresa" class="form-control"/>
				                            </div>
				                        </div><!-- /form-group -->

				                        <div class="form-group">
				                            <label class="col-md-3 control-label" for="vist_priori">Prioridad:
				                            </label>
				                            <div class="col-md-7">
				                                <select class="form-control" id="vist_priori" name="vist_priori">
				                                <option value=""></option>
		                                        <option value="2">Normal</option>
		                                        <option value="1" >Urgente</option>
		                                        </select>
				                            </div>
				                        </div><!-- /form-group -->

				                        <div class="form-group">
				                            <label class="col-md-3 control-label" for="vist_tipo">Tipo Servicio:
				                            </label>
				                            <div class="col-md-7">
				                                <select class="form-control" id="vist_tipo"  name="vist_tipo"  multiple="multiple" >

			                                        <?php
			                                        $MyrecordDataTS = $query->SelDB($conex,"site_sel_TipoOdrServicio",array());

													while($r=$query->getdata_object($MyrecordDataTS)){
														unset($rAux);
													?>
													<option value="<?php echo $r->id_tipo_equipo;?>"><?php echo $r->descripcion;?></option>
													<?php
													}
			                                        ?>
		                                        </select>
				                            </div>
				                        </div><!-- /form-group -->

				                        <div class="form-group">
				                            <label class="col-md-3 control-label" for="vist_tipo">Problema:
				                            </label>
				                            <div class="col-md-7">
												<textarea  id="vist_problema" rows="4" name="vist_problema" class="form-control" placeholder="Problema que presenta el Cliente"></textarea>
				                            </div>
				                        </div><!-- /form-group -->

				                    </div>
				                    <div class="form-actions fluid">
				                        <div class="row">
				                            <div class="col-md-offset-3 col-md-9">
				                                <button class="btn <?php echo getClassIcon('guardar');?> btn-ordenes" id="btn-acc-orden" onclick="crearOrdenMonitoreo()" data-loading-text="Cargando...">
				                                    <i class="<?php echo getImgIcon('guardar');?>"></i>
				                                    <span id="text-save-orden">Guardar</span>
				                                </button>
				                                <button class="btn default btn-ordenes" type="button" id="cancelar-orden" onclick="clearFormOrdenSer()">Cancelar</button>
				                            </div>
				                        </div>
				                    </div>
				                </form>
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
var validator_ordenes = null;
var loadOrdenesBam = false;

var rulesOrden ={
	vist_client:{
		required: true
	},
	vist_priori:{
		required: true
	},
	vist_tipo:{
		required: true
	},
	vist_problema:{
		required: true
	}
};

var messagesOrden ={
	vist_client:{
		required: "Seleccione Cliente"
	},
	vist_priori:{
		required: "Seleccione Prioridad"
	},
	vist_tipo:{
		required: "Seleccione Tipo"
	},
	vist_problema:{
		required: "Ingrese Problema que presenta el Cliente"
	}
};

$(document).ready(function() {
	var height_TabData = <?php echo $alto;?> ;

	$("#body-view-ordenes").css({
		"height":height_TabData,
		"max-height":height_TabData
	});

	$("#body-ordenes-cliente").css({
		"height":height_TabData-83,
		"max-height":height_TabData-83
	});

	$("#vist_priori").select2({
        placeholder: "Seleccione Prioridad",
        minimumResultsForSearch: -1,
        allowClear: true
    });

	$('#vist_tipo').select2({
    	placeholder: "Seleccione Tipo Servicio"
    });

	$("#vist_tipo").select2("val", "");

	$('#vist_client').select2({
        placeholder: "Buscar Cliente",
        minimumInputLength: 1,
        allowClear: true,
        ajax: {
            url:'<?php echo $CONFIG['HOST'];?>Service/modulos/visitas_tecnicas/controller.php?acc=load_client&x='+Math.random(),
            dataType: 'json',
            data: function (term, page) {
                return {
                    q: term
                };
            },
            results: function (dat, page) {
                    return {results: dat.aaData};
            }
        },
        createSearchChoicePosition: 'bottom',
        formatSelection: function(a){
            if(a!=""){
                $("#vist_tipoc").val(a.idT);
                $("#vist_id_empresa").val(a.id_empresa);
                return  a.text;
            }
        },
        escapeMarkup: function (m) { return m; }
    });

    validator_ordenes = handleValidationForm("#form-add-ordenes",rulesOrden,messagesOrden);

});


function crearOrdenMonitoreo(){
	if(validator_ordenes.form()){
		$.ajax({
	        url:'<?php echo $CONFIG['HOST'];?>Service/modulos/visitas_tecnicas/controller.php?x='+Math.random(),
	        type: "POST",
	        data: {
	            acc:'new_orden_serv_moni',
	            id_cliente:$("#vist_client").val(),
	            priori:$("#vist_priori").val(),
	            tiposrv:$("#vist_tipo").val().toString(),
	            problema:$("#vist_problema").val(),
	            empresa:$("#vist_id_empresa").val()
	        },
	        cache: false,
	        error: function (x, t, m) {
	            btnPrimaryOrdenes(1)
	            window.parent.alertError({title:"Error",text:"Error al crear la Orden"});
	        },
	        beforeSend:function(){
	        	btnPrimaryOrdenes(0)
	        },
	        success:function(data) {
	            btnPrimaryOrdenes(1);

	            if(data=="ok"){
	            	window.parent.alertSuccess({title:"Notificaci&oacute;n",text:"Orden creada exitosamente."});
	            	window.parent.tb_remove(true);
	            }else{
	           		window.parent.alertError({title:"Error",text:"Error al crear la Orden, Intente luego"});
	            }

	        }

	    });
	}else{
		window.parent.alertError({title:"Error",text:"Debe ingresar todos los datos para crear la orden"});
	}
}

function btnPrimaryOrdenes(st){
    var btn = $('#btn-acc-orden');
	var txtBtn =$("#text-save-orden");


    if(st==1){
        btn.removeClass('btn-danger');
        btn.addClass('btn-primary');
        txtBtn.text('Guardar');
        btn.attr('disabled',false);
        $("#btn-ordenes").attr('disabled',false);
    }else{
        btn.removeClass('btn-primary');
        btn.addClass('btn-danger');
        txtBtn.text('Guardando...');
        btn.attr('disabled', 'disabled');
        $("#btn-ordenes").attr('disabled', 'disabled');
    }
}

function clearFormOrdenSer(){
	$("#vist_priori,#vist_client,#vist_tipo").select2("val", "");
	$("#vist_problema").val("");
}

</script>