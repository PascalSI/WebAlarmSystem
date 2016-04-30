<div class="tab-pane tab-cierre fade tab-div container-fluid" id="tabs-cierre-9" style='padding-left: 0px; padding-right: 0px;'>
	<div class="row" style='padding-left: 0px; padding-right: 0px;'>
		<div class="col-md-12">
			<div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa marginTop1 fa-tags"></i>
						<span>Historial de Ordenes</span>
					</div>
					<div class="actions">
						<a class="btn btn-sm bordered-left <?php echo $CONFIG['WEB_THEME'];?>" href="javascript:;" id="btn-show-add-ordenes" onclick="showCreateOrdenes()">
							<i class="fa fa-plus"></i>
							Agregar
						</a>

						<a style="display:none" class="btn btn-sm bordered-left <?php echo $CONFIG['WEB_THEME'];?>" href="javascript:;" id="btn-show-add-regresar" onclick="showListOrdenes()">
							<i class="<?php echo getImgIcon("regresar");?>"></i>
							regresar
						</a>
					</div>
				</div>
				<div class="portlet-body" id="body-ordenes-cliente" style="overflow-y:auto;overflow-x:hidden;">
					<div class="table-container" id="div-table-ordenes">
						<table class="table table-hover table-striped table-condensed"  id="datatablesOrdenes" style="font-size:13px">
                        <thead>
                           <tr role="row" class="heading">
                                <th width="1%"></th>
	                            <th>Orden</th>
	                            <th>Problema</th>
	                            <th>Creacion</th>
	                            <th>Comentario Final</th>
	                            <th>Estatus</th>
                            </tr>
                        </thead>
                        <tbody></tbody> <!-- source with ajax -->
                    	</table>
					</div>
					<div id="div-ordenes-create" style="display:none">
						<form class="form-horizontal" id="form-add-ordenes" onsubmit="return false;">
		                    <div class="form-body">
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
		                                <button class="btn default btn-ordenes" type="button" id="cancelar-orden" onclick=" ">Cancelar</button>
		                            </div>
		                        </div>
		                    </div>
		                </form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
var oTableOrden = null;
var validator_ordenes = null;
var loadOrdenesBam = false;

var rulesOrden ={
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

jQuery(document).ready(function($) {
	$("#vist_priori").select2({
        placeholder: "Seleccione Prioridad",
        minimumResultsForSearch: -1,
        allowClear: true
    });

	$('#vist_tipo').select2({
    	placeholder: "Seleccione Tipo Servicio"
    });

	$("#vist_tipo").select2("val", "");

    validator_ordenes = handleValidationForm("#form-add-ordenes",rulesOrden,messagesOrden);
});

function initialize_table_ordenes(){
	if(!loadOrdenesBam){
		oTableOrden =  $('#datatablesOrdenes').dataTable({
		"dom": "<'row'<'col-md-6 col-sm-12'><'col-md-6 col-sm-12'<'div_fech'>>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
		"bProcessing": true,
		"iDisplayLength": 25,
		"bServerSide": true,
		"aaSorting": [[ 1, "asc" ]],
		"fnDrawCallback" : function(a) {
			Metronic.initAjax();
		},
		"sAjaxSource": '<?php echo $CONFIG['HOST'];?>modulos/clientes/ordenes/controller.php?acc=load_ordenes&c=<?php echo $idCliente;?>',
		"aoColumnDefs": [ {
		  "aTargets": [0,1,2,3,4,5],
		  "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
			$(nTd).css('background',StrTrim(oData.colorbg));
			$(nTd).css('color',StrTrim(oData.color));
		  }
		} ],
		"aoColumns": [
		{
			"mData": "id_status",
			"bSortable": false
			,
		  	"mRender":  function(data, type, obj) {
				return '<span class="'+setIconOrd(obj.id_status)+'padding0 popovers" data-trigger="hover" data-placement="right" data-content="'+obj.estatus+ '"></span>';
		  	}
		},
		{ "mData": "correlativo","bSortable": false },
		{ "mData": "problema",
		  "bSortable": false,
		  	"mRender":  function(data, type, obj) {
				return '<span class="padding0 popovers" data-trigger="hover" data-placement="top" data-content="'+obj.problema+ '">'+setMid(obj.problema,30)+ '</span>';
		  	}
		},
		{
			"mData": "fechaCreada",
			"bSortable": false
		},
		{ "mData": "comnt_fin",
			"bSortable": false,
		  	"mRender":  function(data, type, obj) {
				return '<span class="padding0 popovers" data-trigger="hover" data-placement="top" data-content="'+obj.comnt_fin+ '">'+setMid(obj.comnt_fin,30)+ '</span>';
		  	}
		},
		{
			"mData": "estatus",
			"bSortable": false
		}]
		});
		loadOrdenesBam = true;
	}
}

function showCreateOrdenes(){
	$("#div-table-ordenes,#btn-show-add-ordenes").hide();
	$("#div-ordenes-create,#btn-show-add-regresar").show();
}

function showListOrdenes(){
	clearFormOrdenSer();
	$("#div-ordenes-create,#btn-show-add-regresar").hide();
	$("#div-table-ordenes,#btn-show-add-ordenes").show();
}

function crearOrdenMonitoreo(){
	if(validator_ordenes.form()){
		$.ajax({
	        url:'<?php echo $CONFIG['HOST'];?>Service/modulos/visitas_tecnicas/controller.php?x='+Math.random(),
	        type: "POST",
	        data: {
	            acc:'new_orden_serv_moni',
	            id_cliente:obj.idCliente,
	            id_trama:obj.idTrama,
	            priori:$("#vist_priori").val(),
	            tiposrv:$("#vist_tipo").val().toString(),
	            problema:$("#vist_problema").val(),
	            empresa:obj.id_emp
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
	            	oTableOrden.fnReloadAjax('<?php echo $CONFIG['HOST'];?>modulos/clientes/ordenes/controller.php?acc=load_ordenes&c=<?php echo $idCliente;?>');

	            	setComentTableSignal("Se creo la Orden de Servicio");
	            	$("#btn-show-add-regresar").click();
	            	window.parent.alertSuccess({title:"Notificaci&oacute;n",text:"Orden creada exitosamente."});
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
	$("#vist_priori").select2("val", "");
	$("#vist_tipo").select2("val", "");
	$("#vist_problema").val("");
}


</script>