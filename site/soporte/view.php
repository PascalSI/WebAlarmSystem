<?php

$querys= new Querys();
$conex = $querys->getConection($CONFIG);

?><div class="row">
	<div class="col-md-12">
		<div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
			<div class="portlet-title">
				<div class="caption">
					<i class="glyphicon glyphicon-headphones fa-globe"></i>
					<span class="font16">Soporte en Linea</span>
				</div>
			</div>
			<div class="portlet-body">
				<form class="form-horizontal" onsubmit="return false;" id="form-send-soport">
					<div class="form-body">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label col-md-3" for="nombre_soport">NOMBRE:</label>
									<div class="col-md-9">
										<input type="text" placeholder="NOMBRE" class="form-control" id="nombre_soport" value="<?php echo $_SESSION["cliente"]["nombre_cliente"];?>" />
									</div>
								</div>
							</div>
							<!--/span-->
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label col-md-3" for="select_soport">CATEGORIA:</label>
									<div class="col-md-9">
										<select class="form-control" id="select_soport" name="select_soport">
											<option value="">Seleccione...</option>
											<?php
	                                        $MyRecorC = $querys->SelDB($conex,"site_sel_SoporteMotivos",array($_SESSION["cliente"]["idEmpresa"]));

	                                        while($rC=$querys->getdata_object($MyRecorC)){
	                                        ?>
	                                        <option value="<?php echo $rC->descripcion;?>" rel-correo="<?php echo $rC->correo;?>">
	                                            <?php echo $rC->descripcion;?>
	                                        </option>
	                                        <?php
	                                        }
	                                        ?>
	                                        <option value="General" rel-correo="<?php echo $_SESSION["cliente"]["email_empresa"];?>" >General</option>
										</select>
									</div>
								</div>
							</div>
							<!--/span-->
						</div>
						<!--/row-->
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label col-md-3" for="telefono_soport">TEL&Eacute;FONO:</label>
									<div class="col-md-9">
										<input type="text" placeholder="TEL&Eacute;FONO" class="form-control" id="telefono_soport" />
									</div>
								</div>
							</div>
							<!--/span-->


							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label col-md-3" for="descrip_soport">DESCRIPCI&Oacute;N:</label>
									<div class="col-md-9">
										<textarea class="form-control" placeholder="DESCRIPCI&Oacute;N" id="descrip_soport" name="descrip_soport"></textarea>
									</div>
								</div>
							</div>
							<!--/span-->

						</div>
						<!--/row-->
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label col-md-3" for="correo_soport">CORREO:</label>
									<div class="col-md-9">
										<input type="text" placeholder="CORREO" class="form-control" id="correo_soport" value="<?php echo $_SESSION["cliente"]["email"];?>"/>
									</div>
								</div>
							</div>
							<!--/span-->

							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label col-md-3" for="telefono_soport">PRIORIDAD:</label>
									<div class="col-md-9">
										<div class="radio-list">
											<label class="radio-inline">
											<input type="radio"  onclick="setValuePriori('Baja')" name="optionsRadios" id="optionsRadios4" value="option1" checked="checked"  > BAJA </label>
											<label class="radio-inline">
											<input type="radio"  onclick="setValuePriori('Media')" name="optionsRadios" id="optionsRadios5" value="option2"> MEDIA </label>
											<label class="radio-inline">
											<input type="radio" onclick="setValuePriori('Alta')"  name="optionsRadios" id="optionsRadios6" value="option3" > ALTA </label>
										</div>
									</div>

								</div>

								<div align="center"  style="margin-bottom: 5px;">
									<button class="btn btn-primary "  id="btn_enviar_soport" onclick="enviar_soport()">
										Enviar
									</button>
								</div>

							</div>
							<!--/span-->

						</div>
						<!--/row-->

						<div class="row">
							<div class="col-md-12">
								<table width="100%" border="0">
	                                <tr>
	                                    <td width="100%;">
	                                        <div  id="map-soport" >Cargando Mapa...</div>
	                                    </td>
	                                </tr>
	                            </table>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
var prioridad="Baja";
var validator_soport = null;

var rulesSopport ={
	nombre_soport:{
		required: true
	},
	correo_soport:{
		required: true
	},
	telefono_soport:{
		required: true
	},
	select_soport:{
		required: true
	},
	descrip_soport:{
		required: true
	}
};

var messagesSopport ={
	nombre_soport:{
		required: "Ingrese Nombre"
	},
	correo_soport:{
		required: "Ingrese Correo"
	},
	telefono_soport:{
		required: "Ingrese Telefono"
	},
	select_soport:{
		required: "Ingrese Categoria"
	},
	descrip_soport:{
		required: "Ingrese Descripcion"
	}
};

$(document).ready(function() {
	validator_soport = handleValidationForm("#form-send-soport",rulesSopport,messagesSopport);
});

function enviar_soport(){
	var btn = $("#btn_enviar_soport");
	var coment = $("#descrip_soport").val();
	var motivo =  $("#select_soport").val();
	var email =  $("#correo_soport").val();
	var tel =  $("#telefono_soport").val();
	var nombre = $("#nombre_soport").val();
	var to_ =  $('#select_soport option:selected').attr('rel-correo');


	if(validator_soport.form()){
		$.ajax({
			url:'soporte/controller.php',
			type: "POST",
			error: function(x, t, m) {
				$(btn).attr("disabled",false)
				$(btn).attr("value","Enviar")
			},
			data: {
				acc:'soporte',
				priori:prioridad,
				nombre_abonado:nombre,
				tel:tel,
				email:email,
				motivo:motivo,
				coment:coment,
				to:to_
			},
			cache: false,
			beforeSend:function(){
				$(btn).attr("disabled",true)
				$(btn).attr("value","Enviando...")
			},
			success:function(data) {
				$(btn).attr("disabled",false)
				$(btn).attr("value","Enviar")
				$("#msj-exito,#msj-error").hide();

				if(data=="send"){
					$("#descrip_soport").val("");
					$("#select_soport").val("");
					$("#correo_soport").val("");
					$("#telefono_soport").val("");
					$("#nombre_soport").val("");

					alertSuccess({title:"Notificaci&oacute;n",text:"Se ha enviado soporte Exitosamente"});
				}else{
					alertError({title:"Notificaci&oacute;n",text:":( Ha ocurrido un Eror al enviar."});
				}
			}
		});
	}
}

function setValuePriori(v){
	prioridad=v;
}

	/*MAPA*/
var map_soport= null;

//ruta
var Emplat ='<?php echo $EmpresaLat;?>';
var Emplogi = '<?php echo $EmpresaLog;?>' ;


function load_map_soport(){
	if(map_soport==null){


		var lat =Emplat;
		var logi = Emplogi ;


		var latlng = new google.maps.LatLng(lat.replace(",","."),logi.replace(",","."));

		var icon = 'residencial.png' ;
		var contentEmpresa = '<div align="center" class="infowin" style="height:100px"><?php echo $_SESSION["cliente"]["nombre_empresa"];?><br><?php echo $_SESSION["cliente"]["direccion"];?><br/><a href="http://<?php echo $_SESSION["cliente"]["web"];?>" target="_new"><?php echo $_SESSION["cliente"]["web"];?></a><br/><?php echo $_SESSION["cliente"]["movil"];?></div>',

		map_soport = new google.maps.Map(document.getElementById('map-soport'), {
			zoom: 15,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}),

		markerempresa = new google.maps.Marker({
			icon:'<?php echo $CONFIG['HOST'];?>/img/iconmap/agency.png',
			position: new google.maps.LatLng(Emplat.replace(",","."),Emplogi.replace(",",".")),
			map: map_soport
		});

		infoEmpresa = new google.maps.InfoWindow({
			content: contentEmpresa
		})
		infoEmpresa.open(map_soport, markerempresa);
		google.maps.event.addListener(markerempresa, 'click', function() {
			infoEmpresa.open(map_soport, markerempresa);
		});
	}
}
</script>