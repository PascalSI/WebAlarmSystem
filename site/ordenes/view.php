<div class="row">
	<div class="col-md-12">
		<div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa marginTop1 fa-tags"></i>
					<span>Historial de Ordenes</span>
				</div>
			</div>
			<div class="portlet-body">
				<div class="table-container">
					<table class="table table-hover table-striped table-condensed"  id="datatablesOrdenes">
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
                        <tbody></tbody>
                    </table>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
var oTableOrden= null;

function initialize_table_ordenes(){
	oTableOrden =  $('#datatablesOrdenes').dataTable({
		"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'<'div_fech'>>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
		"bProcessing": true,
		"iDisplayLength": 25,
		"bServerSide": true,
		"aaSorting": [[ 1, "asc" ]],
		"fnDrawCallback" : function(a) {
			Metronic.initAjax();
		},
		"sAjaxSource": '<?php echo $CONFIG['HOST'];?>site/ordenes/controller.php?acc=load_ordenes&c=<?php echo $idCliente;?>',
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
}


</script>