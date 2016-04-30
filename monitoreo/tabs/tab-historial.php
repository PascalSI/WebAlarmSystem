<div class="tab-pane tab-cierre fade tab-div container-fluid" id="tabs-cierre-7" style='padding-left: 0px; padding-right: 0px;'>
	<div class="row" style='padding-left: 0px; padding-right: 0px;'>
		<div class="col-md-12" >
			<div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa marginTop1 fa-rss "></i>
						<span id="title-signales">Últimas 20 Señales</span>
					</div>
					<div class="actions">
						<a class="btn btn-sm bordered-left <?php echo $CONFIG['WEB_THEME'];?>" href="javascript:;" id="btn-show-seach" onclick="viewSearch()">
							<i class="fa fa-plus"  id="mini-icon" ></i>
							Busqueda Avanzada
						</a>
					</div>
				</div>
				<div class="portlet-body" id="body-signal-cliente" style="overflow-y:auto;overflow-x:hidden;">
					<div class="table-container">
						<div align="" id="div-search"  style="display: none;">
							<form class="form-horizontal" role="form"  onsubmit="return false;" >
								<div class="form-body paddingAll1" style="margin-bottom:20px">
									<div class="row">
										<div class="col-md-6">
							                <div class="row">
							                	<div class="col-md-2">
								                	<label class="control-label col-md-12">Fecha:</label>
								                </div>
							                    <div class="col-md-5">
							                        <div class="input-group   date date-picker date-picker-one" data-date-format="dd/mm/yyyy" >
							                            <input type="text" class="form-control" id="fecha1" name="fecha1" readonly placeholder="Desde">
							                            <span class="input-group-btn">
							                                <button class="btn default" type="button" style='padding-top: 10px;padding-bottom: 9px;'><i class="fa fa-calendar"></i></button>
							                            </span>
							                        </div>
							                    </div>
							                    <div class="col-md-5">
							                        <div class="input-group   date date-picker date-picker-two" data-date-format="dd/mm/yyyy" >
							                            <input type="text" class="form-control" id="fecha2" name="fecha2" readonly placeholder="Hasta">
							                            <span class="input-group-btn">
							                                <button class="btn default" type="button"  style='padding-top: 10px;padding-bottom: 9px;'><i class="fa fa-calendar"></i></button>
							                            </span>
							                        </div>
							                    </div>
							                </div>
										</div>
										<div class="col-md-4">
											<div class="row">
												<div class="col-md-4">
								                	<label class="control-label col-md-12">Categorias:</label>
								                </div>
								                <div class="col-md-8">
								                	<select class="form-control"  id="select_cat" name="select_cat">
								                		<option value="-1">Todas</option>
								                		<?php
								                		$querys= new Querys();
														$conexs = $query->getConection($CONFIG);
														$MyrecordCatS = $querys->SelDB($conexs,"site_sel_GruposAlarmas");

														while($rS=$querys->getdata_object($MyrecordCatS)){
								                		?>
														<option value="<?php echo $rS->idGrupo;?>"><?php echo $rS->Descript;?></option>
														<?php
														}
														?>
													</select>
								                </div>
							                </div>
										</div>
										<div class="col-md-2">
											<button onclick="changeDateSig(2)" class="btn btn-primary">
				                            	<i class="<?php echo getImgIcon('buscar');?>"></i>
				                            </button>
										</div>
									</div>
								</div>
							</form>
						</div>
						<table class="table table-hover table-striped table-condensed"  id="datatablesSig" style="font-size: 13px;">
	                        <thead>
	                           <tr role="row" class="heading">
	                                <th>Evento</th>
		                            <th>Usuario/Zonas</th>
		                            <th>Fecha Recepcion</th>
		                            <th>Fecha Procesada</th>
	                            </tr>
	                        </thead>
	                        <tbody></tbody> <!-- source with ajax -->
	                    </table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" id="conver-fech-signal" />
	<script>
	var oTableSig= null;
	var loadHistorialBam = false;

	var auxTipo="1";
	var date_signal = 1 ;
	var date1_signal = "";
	var date2_signal="";
	var caty_signal="-1";
	var caty_text_signal="-1";

	var view_fecha_1 = "&nbsp; <select  style='margin-bottom:8px;' id='selec_date1' class='form-control  input-inline' onchange='changeDateSig(1)'>";

	view_fecha_1+="<option  value='1' selected>Ultimas 100</option>";

	<?php

	$query= new Querys();
	$conex = $query->getConection($CONFIG);

	$fechaAuxSig = "";

	$MyrecordSig = $query->SelDB($conex,"site_sel_UltFecReport",array($idCliente));
	if($query->count_row($MyrecordSig)){
		while($rSig=$query->getdata_object($MyrecordSig)){
			$fechaSig =  date_format($rSig->fechaSalida,"d/m/Y");
		?>
			view_fecha_1+="<option  value='<?php echo $fechaSig?>'><?php echo $fechaSig;?></option>";
		<?php
			if($fechaAuxSig==""){
				$fechaAuxSig = $fechaSig;
			}
		}
	}else{
			$fechaAuxSig = date("d/m/Y");
	}

	?>

	view_fecha_1+="</select>&nbsp;&nbsp;";

	function initialize_table_signal(){
		if(!loadHistorialBam){
			loadHistorialBam =  true;
			oTableSig = $("#datatablesSig").dataTable({
			     "dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'<'div_fech'>>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
			    "sAjaxSource": '<?php echo $CONFIG['HOST'];?>modulos/clientes/signales/controller.php?acc=load_signal&fech=1&id=<?php echo $idCliente;?>&d1=&d2=&cat=-1&tipo=1',
			    "fnDrawCallback" : function(a) {
			    	Metronic.initAjax();

					var text = a.jqXHR.responseJSON.date;
					$("#conver-fech-signal").val(text);
					setTimeout(function(){
						$("#title-signales").html($("#conver-fech-signal").val())
					},100);

				},
				"aoColumnDefs": [ {
				  "aTargets": [0,1,2,3],
				  "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
					  if(oData.bg!=""){
					  	$(nTd).css('background',oData.bg)
					  }
					  if(oData.color!=""){
					 	 $(nTd).css('color',oData.color)
					  }
				  }
				} ],
			    "aoColumns": [
			        {
						"mData": "event",
						"bSortable": false ,
						"mRender":  function(data, type, obj) {
						    if (StrTrim(obj.ob)=="SO" || StrTrim(obj.ob)==""){
								var obs ='';
							}else{
								var evento = obj.event;
								if(StrTrim(obj.userzona)!="..."){
									evento=evento+" "+obj.userzona;
								}
							}
							return StrTrim(obj.event);
					   }
					},
					{ "mData": "userzona" ,"bSortable": false },
					{ "mData": "fecha" ,"bSortable": false },
					{
						"mData": "fechaP" ,
						"bSortable": false,
						"sClass": "hidden-xs hidden-sm"
					}
			    ]
			});

			$("div.div_fech").attr("align","right");
			$("div.div_fech").html(view_fecha_1);
		}
	}



	function changeDateSig(tipo){
		date_signal = $("#selec_date1").val();
		date1_signal =$("#fecha1").val();
		date2_signal=$("#fecha2").val();
		caty_signal=$("#select_cat").val();
		caty_text_signal=$("#select_cat option:selected").html();


		auxTipo=tipo;

		if(date1_signal=="" && date2_signal=="" && caty_signal=="-1"){
			auxTipo=1
		}

		oTableSig.fnReloadAjax('<?php echo $CONFIG['HOST'];?>modulos/clientes/signales/controller.php?acc=load_signal&fech='+date_signal+'&id=<?php echo $idCliente;?>&d1='+date1_signal+'&d2='+date2_signal+'&cat='+caty_signal+'&tipo='+auxTipo);
	}

	function viewSearch(){
		var div = $("#div-search");
		var btn = $("#mini-icon");
		if (div.is(':visible')){
			div.hide();
			btn.removeClass("fa-minus").addClass("fa-plus")
		}else{
			div.show();
			btn.removeClass("fa-plus").addClass("fa-minus");
		}
	}

	$(document).ready(function() {
		$('.date-picker-one,.date-picker-two').datepicker({
			language: 'es',
			autoclose: true,
			format: "dd/mm/yyyy",
			endDate: new Date(),
			todayHighlight: true
		});
	});

</script>
</div>