
<div class="row">
	<div class="col-md-12">
		<div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa marginTop1 fa-camera"></i>
					<span>Administrador de CCTV </span>
				</div>
				<div class="actions">

				</div>
			</div>
			<div class="portlet-body">
				<div class="table-container">

					<table class="table table-hover table-striped table-condensed"  id="datatablesCCTV">
                        <thead>
                           <tr role="row" class="heading">
                           		<th>&nbsp;</th>
                                <th width="2%"></th>
                                <th>Descripci&oacute;n</th>
                                <th>Tipo</th>
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
<?php if($ModCamElim){
	include("delete_camaras.php");
}?>
<script>
var oTableCamaras = null;
var tableWrapperCamaras = null;

/* Formatting function for row details */
function fnFormatDetailsChannel (nTr){
    var aData = oTableCamaras.fnGetData( nTr );

    var sOut = '<table width="100%" class="table table-striped  table-hover table-bordered">';
    sOut += '<thead><tr>';
    sOut += '<th>Channel</th>';
    sOut += '<th>Descripción</th>';
    sOut += '<th>Acciones</th>';
    sOut += '<tbody>';

    if(aData.channels.length>0){
    	var color = "";
    	var cont = 1;
        $.each( aData.channels, function( key, value ) {
        	if (cont % 2 == 0)
			{
				color='';
			}else{
				color = "style='background-color:#f9f9f9'";
			}

       		var btnVer="<a class='btn <?php echo getClassIcon("ver_cam");?>' title='Ver Camara' onclick='showCamra({id:\""+StrTrim(aData.idCry)+"\",channel:\""+value.channelCry+"\",desc:\""+value.desc+"\"})'>";
			btnVer+="<i class='<?php echo getImgIcon("camaras");?>'></i></a>";

          	sOut += '<tr style="border: 1px solid #ddd;"><td '+color+'>'+value.channel+'</td><td '+color+'>'+value.desc+'</td><td '+color+'>'+btnVer+'</td></tr>';

          	cont++;
        });
    }else{
        sOut += '<tr><td colspan="3" align="center"><div class="text-center"><b>';
        sOut += 'Camara no posee chanel registrados</b></div></td></tr>';
    }
    sOut += '</tbody>';
    sOut += '</table>';

    return sOut;
}

function initialize_table_camaras(){

	oTableCamaras = $("#datatablesCCTV").dataTable({
	    "aaSorting": [[ 1, "asc" ]],
	    "sAjaxSource": '<?php echo $CONFIG['HOST'];?>modulos/clientes/camaras/controller.php?acc=load_cctv&id=<?php echo $idCliente;?>',
	    "fnDrawCallback" : function(a) {
	        Metronic.initAjax();
	    },
	    "aoColumns": [
	   		{ "mData": "acci",
              "sWidth": "10px",
              "bSortable": false,
              "mRender":  function(data, type, obj) {

              	if(obj.channels.length>0){
              		return '<span class="row-details row-details-close" title="Ver Channels"></span>';
              	}else{
              		return '';
              	}

              }
            },
	        {
	        	"mData": "modo",
	        	"bSortable": false,
	        	"mRender":  function(data, type, obj) {
	        		return "<i class='"+obj.icon+" tooltips' data-container='body' data-placement='top' data-original-title='"+obj.modo+"'></i>";
	        	}
	        },
			{ "mData": "desc" },
			{ "mData": "tipo","bSortable": false },
	        {
	           "mData": "acci",
	           "bSortable": false ,
	           "sClass": " col-xs-3 col-sm-2 col-md-2 col-lg-2",
	           "mRender":  function(data, type, obj) {
	           		var btnVer="";

					if(obj.channels.length==0){

						btnVer="<a class='btn <?php echo getClassIcon("ver_cam");?>' title='Ver Camara' onclick='showLinkCam({puerto:\""+StrTrim(obj.puerto)+"\",ip:\""+StrTrim(obj.ip)+"\"})'>";
						btnVer+="<i class='<?php echo getImgIcon("camaras");?>'></i></a>";
					}else{
						btnVer="<a class='btn <?php echo getClassIcon("ver_cam");?>' title='Ver Camara' onclick='showCamraAll({id:\""+StrTrim(obj.idCry)+"\"})'>";
						btnVer+="<i class='<?php echo getImgIcon("camaras");?>'></i></a>";
					}

	           		return btnVer;
	           }
	        }
	    ]
	});

	tableWrapperCamaras = oTableCamaras.parents('.dataTables_wrapper');


	$('#datatablesAsist_filter input', tableWrapperCamaras).addClass("form-control input-medium input-inline");


	$('#datatablesCCTV').on('click', ' tbody td .row-details', function () {
        var nTr = $(this).parents('tr')[0];
        if ( oTableCamaras.fnIsOpen(nTr) ) {
            /* This row is already open - close it */
            $(this).addClass("row-details-close").removeClass("row-details-open");
            oTableCamaras.fnClose( nTr );
        } else {
            /* Open this row */
            $(this).addClass("row-details-open").removeClass("row-details-close");
            oTableCamaras.fnOpen( nTr, fnFormatDetailsChannel(nTr), 'details' );
        }
    });


}


function UpdateTableCam(){

		oTableCamaras.fnReloadAjax();

}


function showCamra(obj){
	window.open("<?php echo $CONFIG['HOST'];?>camaras/index.php?q="+obj.id+"&c="+obj.channel+"&cl=<?php echo encode64_asp($idCliente);?>","_blank","width=700,height=500,menubar=no,location=no,resizable=no,scrollbars=no,status=no",false);
}


function showLinkCam(obj){
	var urlClick = obj.ip;

	if(obj.puerto!="0" && obj.puerto!=""){
		urlClick+=":"+obj.puerto;

	}

	if(urlClick.indexOf("http://")=="-1" && urlClick.indexOf("https://")=="-1"){
		urlClick = "http://"+urlClick;
		alert("agrego")
	}
	window.open(urlClick,"_blank","width="+Math.round($(window).width() *0.9)+",height="+Math.round($(window).height() *0.9)+",menubar=no,location=no,resizable=no,scrollbars=no,status=no",false)
}



function showCamraAll(obj){
	var params = [
	    'height='+screen.height,
	    'width='+screen.width,
	    "scrollbars=1" // only works in IE, but here for completeness
	].join(',');

	window.open("<?php echo $CONFIG['HOST'];?>camaras/all.php?xq="+obj.id+"&cl=<?php echo encode64_asp($idCliente);?>","_blank",params,false);
}
</script>