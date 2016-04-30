<?php
include("../include/diseno/i_topExternalMonitoreo.php");
include("../include/diseno/i_header_css_monitoreo.php");

$idCliente = decode64_asp($_GET["cl"]);

$query= new Querys();
$conex = $query->getConection($CONFIG);

$MyrecordDataN = $query->SelDB($conex,"site_sel_DatosCliente",array($idCliente));
$rN=$query->getdata_object($MyrecordDataN);

$typeAction = decode64_asp($_GET["t"]);
$idTrama = decode64_asp($_GET["tr"]);

?>
<!-- END THEME STYLES -->
</head>
	<?php include("include_all/i_header_camaras.php"); ?>
</div>
<!-- END HEADER -->
<div class="clearfix">
</div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
	<!-- BEGIN SIDEBAR -->
	<div class="page-sidebar-wrapper">
		<?php include("include_all/i_menu_camaras.php");?>
	</div>
	<!-- END SIDEBAR -->
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<div class="row" id="panels-chanels">
        		<div class="col-sm-4 col-md-3 chanel-portal pointer " onclick="setTempBox(1)">
					<div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
						<div class="portlet-title " >
							<div class="caption inlinex" id="title-descrip-cam-1">
								<i class="icon-eye "></i> Canal 1
							</div>
						</div>
						<div class="portlet-body padding0"   align="center" style="height: 200px;" id="box-1">
							<i class="glyphicon glyphicon-facetime-video" style="font-size: 100px;margin-top: 74px;color: rgba(153, 153, 153, 0.18);"></i>
						</div>
					</div>
				</div>

				<div class="col-sm-4 col-md-3 chanel-portal pointer " onclick="setTempBox(2)">
					<div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
						<div class="portlet-title " >
							<div class="caption inlinex" id="title-descrip-cam-2">
								<i class="icon-eye "></i> Canal 2
							</div>
						</div>
						<div class="portlet-body padding0"  align="center"  style="height: 200px;"  id="box-2">
							<i class="glyphicon glyphicon-facetime-video" style="font-size: 100px;margin-top: 74px;color: rgba(153, 153, 153, 0.18);"></i>
						</div>
					</div>
				</div>

				<div class="col-sm-4 col-md-3 chanel-portal pointer " onclick="setTempBox(3)">
					<div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
						<div class="portlet-title " >
							<div class="caption inlinex"  id="title-descrip-cam-3">
								<i class="icon-eye "></i> Canal 3
							</div>
						</div>
						<div class="portlet-body padding0"  align="center"   style="height: 200px;"  id="box-3">
							<i class="glyphicon glyphicon-facetime-video" style="font-size: 100px;margin-top: 74px;color: rgba(153, 153, 153, 0.18);"></i>

						</div>
					</div>
				</div>

				<div class="col-sm-4 col-md-3 chanel-portal pointer "  onclick="setTempBox(4)">
					<div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
						<div class="portlet-title ">
							<div class="caption inlinex" id="title-descrip-cam-4">
								<i class="icon-eye "></i> Canal 4
							</div>
						</div>
						<div class="portlet-body padding0"  align="center"  style="height: 200px;"  id="box-4">
							<i class="glyphicon glyphicon-facetime-video" style="font-size: 100px;margin-top: 74px;color: rgba(153, 153, 153, 0.18);"></i>
						</div>
					</div>
				</div>

				<div class="col-sm-4 col-md-3 chanel-portal pointer "  onclick="setTempBox(5)">
					<div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
						<div class="portlet-title ">
							<div class="caption inlinex" id="title-descrip-cam-5">
								<i class="icon-eye "></i> Canal 5
							</div>
						</div>
						<div class="portlet-body padding0"  align="center"  style="height: 200px;"  id="box-5">
							<i class="glyphicon glyphicon-facetime-video" style="font-size: 100px;margin-top: 74px;color: rgba(153, 153, 153, 0.18);"></i>
						</div>
					</div>
				</div>

				<div class="col-sm-4 col-md-3 chanel-portal pointer "  onclick="setTempBox(6)">
					<div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
						<div class="portlet-title ">
							<div class="caption inlinex" id="title-descrip-cam-6">
								<i class="icon-eye "></i> Canal 6
							</div>
						</div>
						<div class="portlet-body padding0"  align="center"  style="height: 200px;"  id="box-6">
							<i class="glyphicon glyphicon-facetime-video" style="font-size: 100px;margin-top: 74px;color: rgba(153, 153, 153, 0.18);"></i>
						</div>
					</div>
				</div>

				<div class="col-sm-4 col-md-3 chanel-portal pointer "  onclick="setTempBox(7)">
					<div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
						<div class="portlet-title ">
							<div class="caption inlinex" id="title-descrip-cam-7">
								<i class="icon-eye "></i> Canal 7
							</div>
						</div>
						<div class="portlet-body padding0"  align="center"  style="height: 200px;"  id="box-7">
							<i class="glyphicon glyphicon-facetime-video" style="font-size: 100px;margin-top: 74px;color: rgba(153, 153, 153, 0.18);"></i>
						</div>
					</div>
				</div>

				<div class="col-sm-4 col-md-3 chanel-portal pointer "  onclick="setTempBox(8)">
					<div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
						<div class="portlet-title "  >
							<div class="caption inlinex" id="title-descrip-cam-8">
								<i class="icon-eye "></i> Canal 8
							</div>
						</div>
						<div class="portlet-body padding0"  align="center"  style="height: 200px;"  id="box-8">
							<i class="glyphicon glyphicon-facetime-video" style="font-size: 100px;margin-top: 74px;color: rgba(153, 153, 153, 0.18);"></i>
						</div>
					</div>
				</div>

			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>
</div>
<!-- END CONTENT -->
<!-- BEGIN FOOTER -->
<?php include("../include/diseno/i_bottom.php"); ?>
<?php //incluye los javascript principales del framework
include("../include/diseno/i_bottom_script_default.php");
?>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>
<script>
var tempNumber = 0;

function addChanels(value){
	removeChanels(value);
	if(value!=0){ //elimina todo mayor a 6
		var htmlxAdd = "";
		var contPint = $(".chanel-portal").length;

		for (var i = contPint; i < value; i++) {
			var numbreBox = i+1;
			htmlxAdd+='<div class="col-sm-4 col-md-3 chanel- pointerportal chanel-portal chanel-portal-ad"  onclick="setTempBox('+numbreBox+')">'+
					'<div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">'+
						'<div class="portlet-title ">'+
							'<div class="caption inlinex" id="title-descrip-cam-'+numbreBox+'">'+
								'<i class="icon-eye "></i> Canal '+numbreBox+
							'</div>'+
						'</div>'+
						'<div class="portlet-body padding0"  align="center"  style="height: 200px;" id="box-'+numbreBox+'">'+
						'<i class="glyphicon glyphicon-facetime-video" style="font-size: 100px;margin-top: 74px;color: rgba(153, 153, 153, 0.18);"></i>'+
						'</div>'+

					'</div>'+
				'</div>';
		};
		$("#panels-chanels").append(htmlxAdd);
	}
}

function removeChanels(cant){
	var total_ =  parseInt(cant)-6;
	$(".chanel-portal-ad").each(function(index, el) {
		if(parseInt(index)>=total_){
			$(el).remove();
		}
	});
}

function setCamBox(obj){
	if(tempNumber==0){
		alertError({title:"Error",text:"Debe Seleccionar Canal donde desea ver la camara"});
		return false;
	}

	var title_cam = $(obj._this).attr("rel-name-cam");
	$("#title-descrip-cam-"+tempNumber).html(title_cam);
	var _name_encode = Base64.encode(title_cam);

	var  url_x = "<?php echo $CONFIG['HOST']?>camaras/camara_show.php?q="+obj.q+'&c='+obj.c+'&cl=<?php echo $_GET["cl"];?>&t=<?php echo $_GET["t"];?>&tr=<?php echo $_GET["tr"];?>&namex='+_name_encode;

	$("#box-"+tempNumber).load(url_x);

	<?php if($typeAction=="1"){ ?>
	if(window.opener){
		window.opener.setComentTableSignal("Se ingreso a la camara: "+title_cam);
	}
	<?php } ?>

	tempNumber=0;
}

function setTempBox(numb){
	tempNumber = numb;
}

$(document).ready(function() {
	setCamarasActivas();
});


function setCamarasActivas(){
	var cantActive = $(".active_relation").length;
	var canalAux = 1;

	if(cantActive>6 && cantActive<=9){
		addChanels(9);
	}else if(cantActive>9 && cantActive<=12){
		addChanels(12);
	}if(cantActive>12){
		addChanels(18);
	}

	setTimeout(function(){
		$(".active_relation").each(function(index, el) {
			tempNumber = canalAux;
			$(el.click());
			canalAux++;
		});
	},800);
}

function viewAllCam(){
	var camCont = $(".link-cam").length;

	if(camCont<=8){
		addChanels(8);
	}else if(camCont>8 && camCont<=12){
		addChanels(12);
	}else if(camCont>12 && camCont<=16){
		addChanels(16);
	}else if(camCont>16){
		addChanels(20);
	}

	$(".link-cam").each(function(index, el) {
		setTempBox(index+1);
		$(el).click();

	});

}
</script>