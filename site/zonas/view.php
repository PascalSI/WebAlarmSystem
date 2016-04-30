<div class="row">
	<div class="col-md-12">
		<div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa marginTop1  fa-compass "></i>
					<span>Administrador de <?php echo $modPlural; ?></span>
				</div>
			</div>
			<div class="portlet-body">
				<div class="table-container">
					<table class="table table-hover table-striped table-condensed"  id="datatablesZonas">
                        <thead>
                           <tr role="row" class="heading">
                                <th>ID</th>
                                <th>Descripci&oacute;n</th>
                                <th>Ubicaci&oacute;n</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody> <!-- source with ajax -->
                    </table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php

include("view_img_zonas.php");

?>
<script>
var oTableZonas = null;
var tableWrapperZonas = null;
var validator_zona=null;
var CaruselZona = null;
var imgElimZona =[];
var imgContZona =0;

function initialize_table_zonas(){
	oTableZonas = $("#datatablesZonas").dataTable({
	    "aaSorting": [[ 0, "asc" ]],
	    "sAjaxSource": '<?php echo $CONFIG['HOST'];?>site/zonas/controller.php?acc=load_zonas&id=<?php echo $idCliente;?>',
	    "aoColumns": [
	        { "mData": "id_zona" },
	        { "mData": "zona" },
	        {
	        	"mData": "ubi",
	        	"sClass": "hidden-xs",
	        	"bSortable": false
	        },
	        {
	           "mData": "acci",
	           "bSortable": false ,
	           "sClass": " col-xs-4 col-sm-3 col-md-3 col-lg-2",
	           "mRender":  function(data, type, obj) {
	           		var  btnImg="";

					if(StrTrim(obj.imgs)!=""){
						btnImg="<a id='btn-zona-img-"+StrTrim(obj.id)+"' class='btn <?php echo getClassIcon("add_imagen");?>'  href='javascript:void(0)' ";
						btnImg+=" onclick='showImgZonas({id:\""+StrTrim(obj.id)+"\",id_zona:\""+StrTrim(obj.id_zona)+"\",path:\""+StrTrim(obj.imgs)+"\",zona:\""+StrTrim(obj.zona)+"\"})' ";
						btnImg+="   title='Ver Imagenes'> <i class='<?php echo getImgIcon("camaras");?>'></i></a>";
					}


	           		return btnImg;
	           }
	        }
	    ]
	});

	tableWrapperZonas = oTableZonas.parents('.dataTables_wrapper');


	$('#datatablesZonas_filter input', tableWrapperZonas).addClass("form-control input-medium input-inline");
}

function UpdateTableZonas(){
	oTableZonas.fnReloadAjax();
}


$(document).ready(function() {
	CaruselZona = $('.carousel').carousel();
});

function showImgZonas(obj){
	var auxObj = obj.path.split(",");
	var active="";
	CaruselZona.carousel("pause").removeData();

	$("#title-img-zona").text(obj.id_zona);
	$("#car-img-zonas-indicators").html("");
	$("#car-img-zonas-content").html("");

	$.each(auxObj, function( index, value ) {
		if(index==0){
			active = "active";
		}else{
			active="";
		}

		var html1='<li data-target="#myCarousel" data-slide-to="'+index+'" class="'+active+'"></li>';
	 	$("#car-img-zonas-indicators").append(html1);

		var caption =obj.zona+" - Imagen "+(index+1);

		var html2= '<div class="item '+active+'">';
        html2+= '       <img src="<?php echo $CONFIG['HOST'];?>img/img_z/'+value+'"  alt="" />';
        html2+= '       <div class="carousel-caption">';
        html2+= '       	<p>'+caption+'.</p>';
        html2+= '       </div>';
        html2+= '   </div>';
		$("#car-img-zonas-content").append(html2);

	});

	$('#myModalViewImgZona').modal('show');
}

function clickImgLink(a){
	$("#btn-zona-img-"+a).click();
}
</script>