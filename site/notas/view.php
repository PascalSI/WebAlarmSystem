<div class="row">
	<div class="col-md-12">
		<div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa marginTop1 fa-file-o"></i>
					<span>Administrador de Notas</span>
				</div>
			</div>
			<div class="portlet-body">
				<div class="row">
					<div class="col-md-6 col-sm-6">
						<div class="portlet box  divDialog <?php echo $CONFIG['WEB_THEME'];?>">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-bookmark marginTop1  "></i>
									Notas Fijas
								</div>
							</div>
							<div class="portlet-body ">
								<textarea class="form-control" rows="9" id="notfija" placeholder="Ingrese Nota Fija"></textarea>
								<div align="center" class="top-5">
									 <button class="btn btn-primary" id="btn-nota" onclick="guardarFija()">
                                        <i class="<?php echo getImgIcon("guardar");?>"></i>
                                        <span id="text-nota">Guardar</span>
                                     </button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6 col-sm-6">
						<div class="portlet box  divDialog <?php echo $CONFIG['WEB_THEME'];?>">
							<div class="portlet-title">
								<div class="caption">
									<i class="glyphicon glyphicon-list-alt marginTop1  "></i>
									Notas Temporales
								</div>
							</div>
							<div class="portlet-body ">
								<div class="row">
									<div class="col-md-6 col-sm-6">
										Desde:
										 <div class="input-group date date-picker date-picker-one-not" data-date-format="dd/mm/yyyy" >
				                            <input type="text" class="form-control" id="desde" name="desde" readonly placeholder="Desde">
				                            <span class="input-group-btn">
				                                <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
				                            </span>
				                        </div>
									</div>
									<div class="col-md-6 col-sm-6">
										Hasta:
										 <div class="input-group   date date-picker date-picker-two-not" data-date-format="dd/mm/yyyy" >
				                            <input type="text" class="form-control" id="hasta" name="hasta" readonly placeholder="Desde">
				                            <span class="input-group-btn">
				                                <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
				                            </span>
				                        </div>
									</div>
								</div>
								<div style="padding:3px"></div>
								<div class="row">
									<div class="col-md-12 col-sm-12">
										<textarea class="form-control" rows="6"  id="notTemp" placeholder="Ingrese Nota Temporal"></textarea>
										<div align="center" class="top-5">
											 <button class="btn btn-primary" onclick="guardarTemp()" id="btn-nota">
                                                <i class="<?php echo getImgIcon("guardar");?>"></i>
                                                <span id="text-nota">Guardar</span>
                                             </button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
var load_notas=null;

$(document).ready(function() {
	 $('.date-picker-one-not').datepicker({
		language: 'es',
		autoclose: true,
		format:'dd/mm/yyyy',
		todayHighlight: true
	});

	$('.date-picker-two-not').datepicker({
		language: 'es',
		autoclose: true,
		format:'dd/mm/yyyy',
		todayHighlight: true
	});


	 setTimeout(function(){
		$('.divDialog').equalHeight();
	},500);
});

function load_Notas(){
	$.ajax({
		url:'../modulos/clientes/notas/controller.php?x='+Math.random(),
		type: "POST",
		timeout:40000,
		dataType:"json",
		error: function(x, t, m) {
			$("#btn-nota").attr('disabled',false);
		},
		data: {
			acc:"load_notas",
			idcliente:'<?php echo $idCliente;?>'
		},
		cache: false,
		beforeSend:function(){
			$("#btn-nota").attr('disabled', 'disabled');
		},
		success:function(data) {

			validateSession(data);

			$("#notfija").html(data.fija);
			$("#notTemp").html(data.temp);


			$('.date-picker-one-not').datepicker('update',data.ini);
			$('.date-picker-two-not').datepicker('update',data.fin);

			$("#btn-nota").attr('disabled',false);

		}

	});
}

function guardarFija(){
	$.ajax({
		url:'../modulos/clientes/notas/controller.php?x='+Math.random(),
		type: "POST",
		timeout:40000,
		error: function(x, t, m) {
			if(t==="timeout") {
			}
			$("#btn-nota").attr('disabled',false);
		},
		data: {
			acc:"save_fija",
			text:$("#notfija").val(),
			idcliente:'<?php echo $idCliente;?>'
		},
		cache: false,
		beforeSend:function(){
			$("#btn-nota").attr('disabled', 'disabled');
		},
		success:function(data) {

			validateSession(data);
			alertSuccess({title:"Nota Fija",text:"Datos Actulizados Exitosamente"});
			$("#btn-nota").attr('disabled',false);

		}

	});
}

function guardarTemp(){
	$.ajax({
		url:'../modulos/clientes/notas/controller.php?x='+Math.random(),
		type: "POST",
		timeout:40000,
		error: function(x, t, m) {
			if(t==="timeout") {
			}
			$("#btn-nota").attr('disabled',false);
		},
		data: {
			acc:"save_temp",
			text:$("#notTemp").val(),
			d:$("#desde").val(),
			h:$("#hasta").val(),
			idcliente:'<?php echo $idCliente;?>'
		},
		cache: false,
		beforeSend:function(){
			$("#btn-nota").attr('disabled', 'disabled');
		},
		success:function(data) {

			validateSession(data);
			alertSuccess({title:"Nota Fija",text:"Datos Actulizados Exitosamente"});
			$("#btn-nota").attr('disabled',false);

		}

	});
}
</script>