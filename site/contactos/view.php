<div class="row">
	<div class="col-md-12">
		<div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa marginTop1  fa-bell-o"></i>
					<span>Administrador de Números de Contacto en caso de EMERGENCIA</span>
				</div>
			</div>
			<div class="portlet-body">
				<div class="table-container">

					<table class="table table-hover table-striped table-condensed"  id="datatablesAsist">
                        <thead>
                           <tr role="row" class="heading">
                           		<th>Prioridad</th>
                                <th>N&uacute;mero</th>
                                <th>Descripci&oacute;n</th>
                                <th>Observaci&oacute;n</th>
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
var oTableAsist = null;
var validator_asist=null;
tableWrapperAsist = null;


function initialize_table_contactos(){

	oTableAsist = $("#datatablesAsist").dataTable({
	    "aaSorting": [[ 0, "asc" ]],
	    "sAjaxSource": '<?php echo $CONFIG['HOST'];?>site/contactos/controller.php?acc=load_asist&id=<?php echo $idCliente;?>',
	    "aoColumns": [
	    	{ "mData": "priori" },
	        { "mData": "num" },
			{ "mData": "desc" },
			{
				"mData": "ob",
				"bSortable": false,
				"sClass": "hidden-xs"
			}
	    ]
	});

	tableWrapperAsist = oTableAsist.parents('.dataTables_wrapper');


	$('#datatablesAsist_filter input', tableWrapperAsist).addClass("form-control input-medium input-inline");
}

function UpdateTableAsist(){
	oTableAsist.fnReloadAjax();
}

</script>