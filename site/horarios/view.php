<div class="row">
	<div class="col-md-12">
		<div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
			<div class="portlet-title">
				<div class="caption">
					<i class="glyphicon marginTop1  glyphicon-time "></i>
					<span>Administrador de Horarios</span>
				</div>
			</div>
			<div class="portlet-body">
				<div class="table-container">

					<table class="table table-hover table-striped table-condensed"  id="datatablesHorarios">
                        <thead>
                           <tr role="row" class="heading">
                                <th>Dia</th>
                                <th>H / Apertura</th>
                                <th>H / Cierre</th>
                            </tr>
                        </thead>
                        <tbody></tbody> <!-- source with ajax -->
                    </table>

				</div>
			</div>
		</div>
	</div>
</div>

<script>
var oTableHorarios= null;
var tableWrapperHorarios  = null;
var validator_hor = null;
var view_dia_hor = "Dia:&nbsp;<select  style='margin-bottom:8px;' id='selec_dia_hor' class='form-control input-small input-inline' onchange='UpdateTableHorarios()'>";
view_dia_hor+="<option  value=''>Todos</option>";
view_dia_hor+="<option  value='1'>Lunes</option>";
view_dia_hor+="<option  value='2'>Martes</option>";
view_dia_hor+="<option  value='3'>Miercoles</option>";
view_dia_hor+="<option  value='4'>Jueves</option>";
view_dia_hor+="<option  value='5'>Viernes</option>";
view_dia_hor+="<option  value='6'>Sabado</option>";
view_dia_hor+="<option  value='7'>Domingo</option>";
view_dia_hor+="<option  value='8'>Feriado</option>";
view_dia_hor+="</select>";

function initialize_table_horarios(){

		oTableHorarios = $("#datatablesHorarios").dataTable({
		    "aaSorting": [[ 0, "asc" ]],
		     "dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'<'div_dias_hor'>>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
		    "sAjaxSource": '<?php echo $CONFIG['HOST'];?>site/horarios/controller.php?acc=load_horarios&id=<?php echo $idCliente;?>',
		    "aoColumns": [
		        { "mData": "dia" },
		        { "mData": "ha" },
		        { "mData": "hc" },
		    ]
		});

		$("div.div_dias_hor").attr("align","right");
		$("div.div_dias_hor").html(view_dia_hor);

}


function UpdateTableHorarios(){

	var urlConsul ='<?php echo $CONFIG['HOST'];?>site/horarios/controller.php?acc=load_horarios&test=1&id=<?php echo $idCliente;?>&d='+$("#selec_dia_hor").val();
	oTableHorarios.fnReloadAjax(urlConsul);

}


</script>