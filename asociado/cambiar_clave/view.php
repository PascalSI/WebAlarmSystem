<div class="row">
	<div class="col-md-12">
		<!-- BEGIN PAGE TITLE & BREADCRUMB-->
		<h3 class="page-title">
			Cambiar Clave
		</h3>
		<!-- END PAGE TITLE & BREADCRUMB-->
	</div>
</div>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
    <div class="col-md-12">
        <!-- Begin: life time stats -->
        <div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
            <div class="portlet-title page-breadcrumb breadcrumb marginTop1">
                <div class="caption">
                    <i class="fa fa-unlock-alt marginTop1"></i>Clave del Asociado <?php echo $_SESSION["cliente"]["nombre_asociado"];?>
                </div>
            </div>
            <div class="portlet-body form">
				<form class="form-horizontal" role="form" id="form-change-clave">
					<div class="form-body">
						<div class="form-group">
							<label class="col-md-3 control-label">Clave actual</label>
							<div class="col-md-4">
								<input type="password" id="clave_actual" name="clave_actual" placeholder="Ingrese clave actual" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">Clave Nueva</label>
							<div class="col-md-4">
								<input type="password" id="clave_nueva" name="clave_nueva" placeholder="Ingrese clave nueva" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">Repetir clave Nueva</label>
							<div class="col-md-4">
								<input type="password" id="clave_repetir" name="clave_repetir" placeholder="Ingrese repetir clave" class="form-control">
							</div>
						</div>
					</div>
					<div class="form-actions">
						<div class="row">
							<div class="col-md-offset-3 col-md-9">
								<button class="btn blue" type="button" id="btn_chage_clave" onclick="change_clave_asociado()">
									<i class="<?php echo getImgIcon("guardar");?>"></i>	<span id="text_chage_clave">Guardar</span>
								</button>
							</div>
						</div>
					</div>
				</form>
            </div>
        </div>
        <!-- End: life time stats -->
    </div>
</div>
<!-- END PAGE CONTENT-->
<script>
var validator_change_clave=null;

var rulesCambiarClave ={
	clave_actual:{
		required: true
	},
	clave_nueva:{
		required: true
	},
	clave_repetir:{
		required: true,
		equalTo: "#clave_nueva"
	}
};

var messagesCambiarClave ={
	clave_actual:{
		required: "Ingrese Clave Actual"
	},
	clave_nueva:{
		required: "Ingrese Nueva Clave"
	},
	clave_repetir:{
		required: "Ingrese Repita Clave",
		equalTo: "Claves no Coinciden"
	}
};

$(document).ready(function() {
	validator_change_clave = handleValidationForm("#form-change-clave",rulesCambiarClave,messagesCambiarClave)
});


function change_clave_asociado(){
	if(validator_change_clave.form()){
		var btn = $('#btn_chage_clave');
		var text_btn = $('#text_chage_clave');

		$.ajax({
			url:'cambiar_clave/controller.php?x='+Math.random(),
			type: "POST",
			timeout:40000,
			error: function(x, t, m) {
				if(t==="timeout") {
				}
				btn.removeClass('btn-danger');
				btn.addClass('btn-primary');
				text_btn.text('Guardar');
				btn.attr('disabled',false);
			},
			data: {
				acc:"change_clave",
				a:$("#clave_actual").val(),
				cnew:$("#clave_nueva").val(),
				idAsociado:'<?php echo $_SESSION["cliente"]["idAsociado"];?>'
			},
			cache: false,
			beforeSend:function(){
				btn.removeClass('btn-primary');
				btn.addClass('btn-danger');
				text_btn.text('Guardando...');
				btn.attr('disabled', 'disabled');
			},
			success:function(data) {

				validateSession(data);

				if(data=="ok"){
					alertSuccess({title:"Notificaci&oacute;n",text:"Clave cambiada Exitosamente."})
					resert_change();
				}else{
					alertError({title:"Notificaci&oacute;n",text:"Clave Actual Invalida."})
				}

				btn.removeClass('btn-danger');
				btn.addClass('btn-primary');
				text_btn.text('Guardar');
				btn.attr('disabled',false);


			}

		});
	}
}

function resert_change(){
	resetFormulario(validator_change_clave);
	$("#clave_actual").val("")
	$("#clave_nueva").val("")
	$("#clave_repetir").val("")
}
</script>