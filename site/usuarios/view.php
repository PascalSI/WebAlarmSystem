<div class="row">
	<div class="col-md-12">
		<div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa marginTop1 fa-user"></i>
					<span>Administrador de Usuarios</span>
				</div>
			</div>
			<div class="portlet-body">
				<div class="table-container">
					<table class="table table-hover table-striped table-condensed"  id="datatablesUser">
                        <thead>
                           <tr role="row" class="heading">
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Movil</th>
                                <th>Parentesco</th>
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
var oTableUser= null;
var tableWrapperUser = null;
var validator_user = null;

function initialize_table_user(){

	oTableUser = $("#datatablesUser").dataTable({
	    "aaSorting": [[ 0, "asc" ]],
	    "fnDrawCallback" : function(a) {
			Metronic.initAjax();
		},
	    "sAjaxSource": '<?php echo $CONFIG['HOST'];?>site/usuarios/controller.php?acc=load_user&id=<?php echo $idCliente;?>',
	    "aoColumnDefs": [ {
		  "aTargets": [0,1,2,3,4],
		  "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
			  if(oData.bg!=""){
			  	$(nTd).css('background',oData.bg)
			  }
		  }
		} ],
	    "aoColumns": [
	        { "mData": "id" },
	        {
	        	"mData": "name",
	        	"mRender":  function(data, type, obj) {
	        		return obj.nom+" "+obj.ape;
	        	}
	        },
	        {
	        	"mData": "mail",
	        	"sClass": "hidden-xs hidden-sm",
	        	"bSortable": false
	        },
	        {
	        	"mData": "movil",
		        "bSortable": false
	        },
	        {
	        	"mData": "parentesco",
	        	"sClass": "hidden-xs hidden-sm",
	        	"bSortable": false
	        }
	    ]
	});

	tableWrapperUser = oTableUser.parents('.dataTables_wrapper');


	$('#datatablesUser_filter input', tableWrapperUser).addClass("form-control input-medium input-inline");

}

function UpdateTableUser(){
	oTableUser.fnReloadAjax();
}

</script>