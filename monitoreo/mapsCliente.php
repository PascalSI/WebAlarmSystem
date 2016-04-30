<?php
	include("../include/diseno/i_topExternalMonitoreo.php");

	//incluye los css principales del framework
	include("../include/diseno/i_header_css_monitoreo.php");
?>
<link rel="stylesheet" type="text/css" href="<?php echo $CONFIG['HOST'];?>plugins/bootstrap-toastr/toastr.min.css"/>
<?php
	$idCliente = decode(7,$_GET["q"]);

	$query= new Querys();
    $conex = $query->getConection($CONFIG);

    $MyRecord = $query->SelDB($conex,"site_sel_DatosMapsCliente",array($idCliente));
    $rData = $query->getdata_object($MyRecord);
    $BamLatLogCliente = 0;

   if($query->count_row($MyRecord)>0){
		$nombre_cliente = $rData->nombre_cliente;
		$latemp  = $rData->latemp;
		$logemp  = $rData->logemp;

		if($rData->latclie!=0 && $rData->logclie!=0){
			$latclie  = $rData->latclie;
			$logclie  = $rData->logclie;
			$ZoomMap = 16;
			$BamLatLogCliente = 1;
		}else{
			if(strlen($rData->latemp)>0 && strlen($rData->logemp)>0){
				$latclie  = $latemp;
				$logclie  = $logemp;
				$ZoomMap = 9;
			}else{
				$latclie =  $CONFIG['MAP_LAT'];
				$logclie =  $CONFIG['MAP_LOG'];
				$ZoomMap = 9;
			}
		}

		$dirclie  = $rData->dirclie;
		$refclie  = $rData->refclie;
		$nomemp  = $rData->nomemp;
		$icon  = $rData->icon;
   }

	$textClie = "<b>Cliente</b>: ".$nombre_cliente;
	if(!empty($dirclie)){
		$textClie = $textClie."<br><b>Direcci&oacute;n</b>: ".$dirclie;
	}

	if(!empty($refclie)){
		$textClie = $textClie."<br><b>Referencia</b>: ".$refclie;
	}

?>
<title>Mapa Monitoreo <?php echo $nombre_cliente;?> </title>
 <!-- javascript
================================================== -->
<!-- required js -->
<script src="<?php echo $CONFIG['HOST'];?>plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="<?php echo $CONFIG['HOST'];?>plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script src="<?php echo $CONFIG['HOST'];?>plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/bootstrap-toastr/toastr.min.js"></script>

<?php include("../js/generales.js.php"); ?>

<script>
	var maps;
	var markerCenter = null
	var cargando_maps=false;
	var wmsMapType;

	//ruta
	var directionsDisplay;
	var directionsService = new google.maps.DirectionsService();
	var Emplat ='<?php echo $latemp;?>';
	var Emplogi = '<?php echo $logemp;?>' ;
	function calcRoute() {
	  var start =  new google.maps.LatLng(Emplat.replace(",","."),Emplogi.replace(",","."));
	  var end = markerCenter.getPosition();

	  var request = {
		  origin:start,
		  destination:end,
		  travelMode: google.maps.DirectionsTravelMode.DRIVING
	  };
	  directionsService.route(request, function(response, status) {
		if (status == google.maps.DirectionsStatus.OK) {
		  directionsDisplay.setDirections(response);
		}
	  });
	}

	function btn_ruta(controlDiv, map){
		controlDiv.style.padding = '5px';
		var controlUI = document.createElement('DIV');
		controlUI.style.backgroundColor = 'white';
		controlUI.style.borderStyle = 'solid';
		controlUI.style.borderWidth = '1px';
		controlUI.style.color = 'black';
		controlUI.style.cursor = 'pointer';
		controlUI.style.textAlign = 'center';
		controlUI.style.padding = '2px';
		controlUI.title = 'Como llegar?';
		controlDiv.appendChild(controlUI);
		var controlText = document.createElement('DIV');
		controlText.id="btn-guardar-map"
		controlText.style.fontFamily = 'Arial,sans-serif';
		controlText.style.fontSize = '12px';
		controlText.style.paddingLeft = '4px';
		controlText.style.paddingRight = '4px';
		controlText.innerHTML = 'Como llegar?';
		controlUI.appendChild(controlText);
		google.maps.event.addDomListener(controlUI, 'click', function(){
			calcRoute()
		});
	}

		function load_map(){

		var lat ='<?php echo $latclie;?>';
		var logi = '<?php echo $logclie;?>' ;
		var icon = '<?php echo $icon;?>' ;
		var zoom = 15;

		var bamlatlog = '<?php echo $BamLatLogCliente;?>';

		if(bamlatlog=='0'){
			alertError({title:"Notificaci&oacute;n Mapa",text:"Cliente sin Coordenadas",delay:"5000"});
		}

		directionsDisplay = new google.maps.DirectionsRenderer({draggable: true,suppressMarkers : true});

		var latlng = new google.maps.LatLng(lat.replace(",","."),logi.replace(",","."));
		var contentEmpresa = '<span class="infowin"><?php echo $nomemp;?></span>',
		contentCenter = '<span class="infowin"><?php echo $textClie;?></span>';


		map = new google.maps.Map(document.getElementById('map'), {
			zoom:zoom,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}),

		directionsDisplay.setMap(map);


		markerCenter = new google.maps.Marker({
			icon:'<?php echo $CONFIG['HOST'];?>img/iconmap/'+icon,
			position: latlng,
			animation: google.maps.Animation.DROP,
			map: map,
			draggable: false
		}),

		markerempresa = new google.maps.Marker({
			icon:'<?php echo $CONFIG['HOST'];?>img/iconmap/agency.png',
			position: new google.maps.LatLng(Emplat.replace(",","."),Emplogi.replace(",",".")),
			map: map
		}),

		infoCenter = new google.maps.InfoWindow({
			content: contentCenter
		}),

		infoEmpresa = new google.maps.InfoWindow({
			content: contentEmpresa
		}),

		circle = new google.maps.Circle({
			map: map,
			clickable: false,
			// metres
			radius:20,
			fillColor: '#EAF7F7',
			fillOpacity: .6,
			strokeColor: '#004080',
			strokeOpacity: .4,
			strokeWeight: .8
		});
		circle.bindTo('center', markerCenter, 'position');


		var bounds = circle.getBounds()
		google.maps.event.addListener(markerCenter, 'dragend', function() {
			latLngCenter = new google.maps.LatLng(markerCenter.position.lat(), markerCenter.position.lng());
			bounds = circle.getBounds();
		});

		google.maps.event.addListener(markerCenter, 'click', function() {
			infoCenter.open(map, markerCenter);
		});

		google.maps.event.addListener(markerempresa, 'click', function() {
			infoEmpresa.open(map, markerempresa);
		});



		var rutaDiv = document.createElement('map');
		rutaDiv.index = 1;
		map.controls[google.maps.ControlPosition.TOP_RIGHT].push(rutaDiv);
		var rutaDiv_ = new btn_ruta(rutaDiv, map);


		var osmMapnik = new google.maps.ImageMapType({
			getTileUrl: function(coord, zoom) {
			return "http://tile.openstreetmap.org/" +
			zoom + "/" + coord.x + "/" + coord.y + ".png";
		},
			tileSize: new google.maps.Size(256, 256),
			isPng: true,
			alt: "Mapnik",
			name: "Mapnik",
			maxZoom: 19
		});
		var osmOsmarender = new google.maps.ImageMapType({
			getTileUrl: function(coord, zoom) {
			return "http://a.tile.cloudmade.com/f5d30721affd4e17a731e11e02959eca/1/256/" +
			zoom + "/" + coord.x + "/" + coord.y + ".png";
		},
			tileSize: new google.maps.Size(256, 256),
			isPng: true,
			alt: "Osmarender",
			name: "Osmarender",
			maxZoom: 19
		});
		var osmCycleMap = new google.maps.ImageMapType({
			getTileUrl: function(coord, zoom) {
			return "http://tile.opencyclemap.org/cycle/" +
			zoom + "/" + coord.x + "/" + coord.y + ".png";
		},
			tileSize: new google.maps.Size(256, 256),
			isPng: true,
			alt: "CycleMap",
			name: "CycleMap",
			maxZoom: 19
		});

		map.mapTypes.set('Mapnik', osmMapnik);
		map.mapTypes.set('Osmarender',osmOsmarender);
		map.mapTypes.set('CycleMap',osmCycleMap);
		map.setOptions({
			mapTypeControlOptions: {
			mapTypeIds: [
			google.maps.MapTypeId.ROADMAP,
			google.maps.MapTypeId.HYBRID,
				'Mapnik',
				'Osmarender',
				'CycleMap'
			],
			style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
			}
		});

	}
	$(window).ready(function() {
		/*Rezise del mapa*/
		$('#map').css('width', $(window).width()-1);
		$('#map').css('height', $(window).height());

		load_map();

		google.maps.event.trigger(map, 'resize');


	});

	$(window).resize(function(){
		$('#map').css('width', $(window).width()-1);
		$('#map').css('height',$(window).height());
		var center = map.getCenter();
		google.maps.event.trigger(map, "resize");
		map.setCenter(center);
	});


</script>
<html>
    <body style=" background:#FFF; height:100%;"  onload="">
        <div id="map" style="width:600px;height:100%; margin:0;">
        	Cargando...
        </div>
        <div   style='display croll;position:fixed;bottom:18px;right:0px;' title='volver arriba'>
        	<img src="<?php echo $CONFIG['HOST'];?>img/logo_empresas/<?php echo $CONFIG['LOGO_PAGES'];?>" />
        </div>
    </body>
</html>