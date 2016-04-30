<html>
<?php
	include("../include/scriptdb/config.php");
	include("../include/phpscript/sessionPopup.php");
	include("../include/phpscript/generales.php");
	include("../include/phpscript/init.php");


	$EmpresaLat = $_SESSION["user"]["latitud"];
	$EmpresaLog = $_SESSION["user"]["longuitud"];
	if(strlen($EmpresaLog)==0 && strlen($EmpresaLog)==0){
		$EmpresaLat = $CONFIG['MAP_LAT'];
		$EmpresaLog = $CONFIG['MAP_LOG'];
	}

	//incluye los css principales del framework
	include("../include/diseno/i_header_css_monitoreo.php");
?>
<link rel="stylesheet" type="text/css" href="<?php echo $CONFIG['HOST'];?>plugins/select2/select2.css"/>
<style>
/* your custom CSS \*/
@-moz-keyframes pulsate {
	from {
		transform: scale(0.9);
		opacity: 1.0;
	}
	95% {
		-moz-transform: scale(1.4);
		opacity: 0;
	}
	to {
		-moz-transform: scale(0.3);
		opacity: 0;
	}
}
@-webkit-keyframes pulsate {
	from {
		transform: scale(0.9);
		opacity: 1.0;
	}
	95% {
		-webkit-transform: scale(1.4);
		opacity: 0;
	}
	to {
		-webkit-transform: scale(0.3);
		opacity: 0;
	}
}

#map div.gmnoprint[title="Maps Alarma"] {
	-moz-animation: pulsate 1.5s ease-in-out infinite;
	-webkit-animation: pulsate 1.5s ease-in-out infinite;
	border:1pt solid #F5A7A7;
	/* make a circle */
	-webkit-border-radius: 51px !important;
	-moz-border-radius: 51px !important;
	border-radius: 51px !important;
	/* multiply the shadows, inside and outside the circle */
	-moz-box-shadow:inset 0 0 5px #FC0000, inset 0 0 5px #FC0000, inset 0 0 5px #FC0000, 0 0 5px #FC0000, 0 0 5px #FC0000, 0 0 5px #FC0000;
	-webkit-box-shadow:inset 0 0 5px #FC0000, inset 0 0 5px #FC0000, inset 0 0 5px #FC0000, 0 0 5px #FC0000, 0 0 5px #FC0000, 0 0 5px #FC0000;
	box-shadow:inset 0 0 5px #FC0000, inset 0 0 5px #FC0000, inset 0 0 5px #FC0000, 0 0 5px #FC0000, 0 0 5px #FC0000, 0 0 5px #FC0000;
	/* set the ring's new dimension and re-center it */
	height:51px!important;
	margin: -9px 0 0 -11px;
	width:51px!important;
}

#map div.gmnoprint[title="Maps Alarma"] img {
	display:none;
}

@media only screen and (-webkit-min-device-pixel-ratio: 1.5), only screen and (device-width: 768px) {
	#map div.gmnoprint[title="Maps Alarma"] {
		margin:-10px 0 0 -10px;
	}
}

.nav-tabs.mapx > li > a {
    color: #fff;
}

.tabbable-custom > .nav-tabs.mapx > li.active > a {
	color: #555;
}

.btn-top{
	padding-bottom: 13px !important;
}
</style>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places"></script>

<script src="<?php echo $CONFIG['HOST'];?>plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script src="<?php echo $CONFIG['HOST'];?>plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="<?php echo $CONFIG['HOST'];?>plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script src="<?php echo $CONFIG['HOST'];?>plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/bootstrap-toastr/toastr.min.js"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>js/maps/keydragzoom/keydragzoom_packed.js"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/select2/select2_locale_es.js"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/bootbox/bootbox.min.js"></script>

<?php include("../js/generales.js.php"); ?>

<script>
	var map;
	var maps;
	var markerCenter = null
	var cargando_maps=false;
	var wmsMapType;
	var GlobalMarkers= new Array();
	var GlobalMarkersVehic= new Array();
	var MapsAutoZoom = true;

	var markerGlobal = null
	var Emplat ='<?php echo $EmpresaLat;?>';
	var Emplogi  ='<?php echo $EmpresaLog;?>';
	var contEmrg=0;
	var GlobalPosition = null;
	var bounds = new google.maps.LatLngBounds();
	var Loaded = false;
	var clienteAuxs = [];

	var infowGlobalOpen =  null;

	//ruta
	var directionsDisplay;
	var directionsService = new google.maps.DirectionsService();

	//boton de busqueda
	function btn_search(controlDiv, map){
		var html_search =  ' <select onclick="viewInfoVeh(this.value)" class="input-large" data-fx="select2" id="select2-minin" ';
		html_search+=  ' name="select2_minin" placeholder="Buscar Patrulla" ><option value=""></option></select>';

		controlDiv.style.padding = '5px';
		var controlUI = document.createElement('DIV');
		controlUI.style.backgroundColor = 'white';
		controlDiv.appendChild(controlUI);
		var controlText = document.createElement('DIV');
		controlText.id="search-map"
		controlText.style.display = 'none';
		controlText.innerHTML = html_search;
		controlUI.appendChild(controlText);
		google.maps.event.addDomListener(controlUI, 'click', function(){});
	}

	//boton de Limpiar
	function btn_clear(controlDiv, map){

		controlDiv.style.padding = '5px';
		var controlUI = document.createElement('DIV');
		controlUI.style.backgroundColor = 'white';
		controlUI.style.borderStyle = 'solid';
		controlUI.style.borderWidth = '1px';
		controlUI.style.color = 'black';
		controlUI.style.cursor = 'pointer';
		controlUI.style.textAlign = 'center';
		controlUI.style.padding = '2px';
		controlUI.title = 'Limpiar Mapa';
		controlDiv.appendChild(controlUI);
		var controlText = document.createElement('DIV');
		controlText.id="btn-guardar-map"
		controlText.style.fontFamily = 'Arial,sans-serif';
		controlText.style.fontSize = '12px';
		controlText.style.paddingLeft = '4px';
		controlText.style.paddingRight = '4px';
		controlText.innerHTML = 'Limpiar Mapa';
		controlUI.appendChild(controlText);
		google.maps.event.addDomListener(controlUI, 'click', function(){
			//directionsDisplay.setDirections({routes:[],status:"OK",mc:{}});
			 directionsDisplay.setDirections({ routes: [] });
		});
	}

	//caraga mapa
	function load_map(){
		var lat ='<?php echo $EmpresaLat;?>';
		var logi = '<?php echo $EmpresaLog;?>' ;
		var zoom = 15;

		var latlng = new google.maps.LatLng(lat.replace(",","."),logi.replace(",","."));

		bounds.extend(latlng);

		GlobalPosition = latlng;
		var contentEmpresa = '<span class="infowin"><?php echo $_SESSION["user"]["nombre_empresa"];?></span>',

		map = new google.maps.Map(document.getElementById('map'), {
			zoom:zoom,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		});

		directionsDisplay = new google.maps.DirectionsRenderer({map:map,draggable: true,suppressMarkers : true});

		markerempresa = new google.maps.Marker({
			icon:'<?php echo $CONFIG['HOST'];?>img/iconmap/agency.png',
			position: new google.maps.LatLng(lat.replace(",","."),logi.replace(",",".")),
			map: map
		});

		infoEmpresa = new google.maps.InfoWindow({
			content: contentEmpresa
		});

		google.maps.event.addListener(markerempresa, 'click', function() {
			infoEmpresa.open(map, markerempresa);
		});

		var limpiarDiv = document.createElement('map');
		limpiarDiv.index = 1;
		map.controls[google.maps.ControlPosition.TOP_RIGHT].push(limpiarDiv);
		var limpiarDiv_ = new btn_clear(limpiarDiv, map);


		var searchDiv = document.createElement('map');
		searchDiv.index = 1;
		map.controls[google.maps.ControlPosition.TOP_RIGHT].push(searchDiv);
		var searchDiv_ = new btn_search(searchDiv, map);

		google.maps.event.addListener(map, 'idle', function(){  //addListenerOnce
			if(!Loaded){
				Loaded=true;
				loadVehiculos();
				$('[data-fx="select2"]').each(function(){
					var $this = $(this),
					min_in = ($this.attr('data-min-input') == undefined) ? 0 : parseInt($this.attr('data-min-input'));

					$this.select2({
						minimumInputLength : min_in
					})
				});
				$("#search-map").show();
			}
		});

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

		map.enableKeyDragZoom({
			boxStyle: {
				border: "4px dashed #1ba1e2",
				backgroundColor: "transparent",
				opacity: 1.0
			},
			veilStyle: {
				backgroundColor: "#1ba1e2",
				opacity: 0.35,
				cursor: "crosshair"
			},
			visualEnabled: true,
			visualPosition: google.maps.ControlPosition.LEFT,
			visualPositionOffset: new google.maps.Size(35, 0),
			visualPositionIndex: null,
			visualSprite: "http://maps.gstatic.com/mapfiles/ftr/controls/dragzoom_btn.png",
			visualSize: new google.maps.Size(20, 20),
			visualTips: {
				off: "Zoom on",
				on: "Zoom off"
			}
		});

		maps= map
	}

	function calcRoute(obj) {

	  var start = obj.start;
	  var end = obj.end;

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

	$(window).ready(function() {
		/*Rezise del mapa*/
		$('#map').css('width', $(window).width());
		$('#map').css('height',$(window).height()-95);

		load_map();
		google.maps.event.trigger(maps, 'resize');

		//carga posicion de los vehiculos de la empresa
		setTimeout(function(){
			//loadVehiculos();
		},1000);

		$("#despacho").click(despacho);

		$(window).resize(function(){
			$('#map').css('width', $(window).width());
			$('#map').css('height', $(window).height()-95);
			google.maps.event.trigger(maps, "resize");
			var center = maps.getCenter();
			maps.setCenter(center);
		});
	});

	function setClientTemp(mrk){
		var cont =  clienteAuxs.length;
		var esta = false;

		for (var i = 0; i < cont; i++) {
			if (clienteAuxs[i] == mrk.iC) {
				esta = true;
			}
		}

		if(!esta){
			clienteAuxs.push(mrk.iC);
		}
	}

	function ControllerMarker(mar){
		Inicialize();

		$.each(mar.aaData, function( index, valuesCMarker ) {
			if(valuesCMarker.lat!="" && valuesCMarker.lon!="" && valuesCMarker.lat!="0" && valuesCMarker.lon!="0"){
				showMarker(valuesCMarker);
				setClientTemp(valuesCMarker)
			}
		});

		if(MapsAutoZoom){
			maps.fitBounds(bounds);
		}

		$("#lbl-count-emrg-maps").text(contEmrg);

		if(contEmrg==0){
			maps.setCenter(GlobalPosition);
		}
	}

	function showMarker(obj){

		if(!verifiPto(GlobalMarkers,obj.iC)){//por crear
			pintarPuntos(obj);
		}else{
			actualizePuntos(obj);
		}

		bounds.extend(new google.maps.LatLng(obj.lat.replace(",","."),obj.lon.replace(",",".")));
		contEmrg++;
	}

	function pintarPuntos(uni){
		var myLatLng=new google.maps.LatLng(uni.lat.replace(",","."),uni.lon.replace(",","."));

		var image = new google.maps.MarkerImage(
			'<?php echo $CONFIG['HOST'];?>img/iconmap/'+uni.img,
			null, // size
			null, // origin
			new google.maps.Point( 15, 15 ), // anchor (move to center of marker)
			new google.maps.Size( 32, 37 ) // scaled size (required for Retina display icon)
		);

		var markers = new google.maps.Marker({
			position: myLatLng,
			map: maps,
			icon:image,
			optimized: true,
			title:"Maps Alarma"
		});

		var htmlIfo = setHtmlInfoClient(uni);

		var infoCliente = new google.maps.InfoWindow({
			content: htmlIfo
		});

		google.maps.event.addListener(markers, 'click', function(){
			LimpiarInfoClient();
			markerGlobal={
				client:uni.iC,
				mark:this
			};
			infoCliente.open(maps,this);
			maps.panTo(myLatLng);
			maps.panTo(myLatLng);

			this.setOptions({optimized:false});
		});

		var arrayMarker = new Array(uni.iC,infoCliente,markers);
		GlobalMarkers.push(arrayMarker);
	}


	function actualizePuntos(uni){
		var htmlIfo = setHtmlInfoClient(uni);

		var myLatLng=new google.maps.LatLng(uni.lat.replace(",","."),uni.lon.replace(",","."));
		var client = getPto(GlobalMarkers,uni.iC);

		client[1].setContent(htmlIfo);
		client[2].setPosition(myLatLng);
	}

	function clearPuntos(){
		var auxGloabalMark = [];
		 $.each(GlobalMarkers, function(index,valuesCMarker){
			var contAux = clienteAuxs.length;
			var exists = false;

			for (var i = 0; i < contAux; i++) {
				if (clienteAuxs[i] == valuesCMarker[0]) {
					exists = true;
					auxGloabalMark.push()
				}
			}

			if(!exists){
				GlobalMarkers[index][1].close();
				GlobalMarkers[index][1].setMap(null);
				GlobalMarkers[index][2].setMap(null);

				if(GlobalMarkers[index]!=undefined){
					//GlobalMarkers.splice(index,index);
				}
				LiberarPatrulla(GlobalMarkers[index]);
			}
		});

		clienteAuxs = [];
	}

	function setHtmlInfoClient(uni){
		//tabs datos
		var htmlDatos="<b>Nombre:</b> "+uni.nom+"&nbsp;&nbsp;(<b>"+uni.cuenta+"</b>)";
		if(uni.dir!="" && uni.dir!= null){htmlDatos+="<br/><b>Direcci&oacute;n:</b> "+uni.dir;}
		if(uni.ref!="" && uni.ref!= null){htmlDatos+="<br/><b>Referencia:</b> "+uni.ref;}
		if(uni.telf!="" && uni.telf!= null){htmlDatos+="<br/><b>Telefono:</b> "+uni.telf;}
		if(uni.event!="" && uni.event!= null){htmlDatos+="<br/><b>Evento:</b> "+uni.event;}
		if(uni.usrZone!="" && uni.usrZone!= null){htmlDatos+="<br/><b>Usuario / Zona:</b> "+uni.usrZone;}

		if(MapsAutoZoom==false){
			textZoomAncla = " Activar zoom automatico";
			popZoom ="  ";
		}else{
			textZoomAncla = " Desactivar zoom automatico";
			popZoom =" checked ";
		}

		htmlDatos+="<br/><label>&nbsp;<input type='checkbox' class='checkZoomMap' "+popZoom+" ";
		htmlDatos+=" onclick='MapAutoZoom()' /><span class='textZoomMap'>"+textZoomAncla+"</span></label>";

		//tabs imagen
		var htmlImg="";

		if(uni.pic!="" && uni.pic!= null){
			var auxImg= "img_c/"+uni.pic;
		}else{
			var auxImg= "not_found.jpg";
		}

		htmlImg="<div align='center' ><img width='248px' height='194px' alt='Imagen del Cliente "+uni.nom+"'  style='height:194px !important'";
		htmlImg+=" src='<?php echo $CONFIG['HOST'];?>img/"+auxImg+"' onerror='this.src = \"<?php echo $CONFIG['HOST'];?>img/not_found.jpg\";' /></div>";

		var htmlIfo='<div class="" style=" height:250px !important;width:260px !important;  margin-bottom:5px !important">';
		htmlIfo+='	<div class="tabbable-custom" style="padding-bottom:0px; margin-bottom:0px">';
		htmlIfo+='		<ul  class="nav nav-tabs mapx " style="color:white !important; background-color:'+uni.color+' !important;">';
		htmlIfo+='			 <li class="active bordered-right">';
		htmlIfo+='				<a href="#tabs-1" id="showDatos" data-toggle="tab" >Datos</a>';
		htmlIfo+='			 </li>';
		htmlIfo+='			 <li class="bordered-right">';
		htmlIfo+='				<a href="#tabs-2" id="showFoto" data-toggle="tab">Foto</a>';
		htmlIfo+='			 </li>';
		htmlIfo+='		</ul>';
		htmlIfo+='		<div class="tab-content" style="padding:0px !important;">';
		htmlIfo+='			<div class="tab-pane fade in active" id="tabs-1" style=" height:200px;overflow-y:auto;padding:2px !important;">';
		htmlIfo+='				'+htmlDatos;
		htmlIfo+='			</div>';
		htmlIfo+='			<div class="tab-pane fade " id="tabs-2" style="height:200px;padding:2px !important;">';
		htmlIfo+='				'+htmlImg;
		htmlIfo+='			</div>';
		htmlIfo+='		</div>';
		htmlIfo+='	</div>';
		htmlIfo+='</div>';

		return htmlIfo;
	}

	function LimpiarInfoClient(){
		var a;
		for(a in GlobalMarkers){

			GlobalMarkers[a][1].setMap(null);
			GlobalMarkers[a][2].setOptions({optimized:true});
		}
	}

	function Inicialize(){
		contEmrg=0;
		bounds = new google.maps.LatLngBounds();

		clearPuntos();

		var lat ='<?php echo $EmpresaLat;?>';
		var logi = '<?php echo $EmpresaLog;?>' ;
		var latlng = new google.maps.LatLng(lat.replace(",","."),logi.replace(",","."));

		bounds.extend(latlng);

		//bounds con vehiculos
		$.each(GlobalMarkersVehic, function( index, valuesCMarkerVeh) {
			bounds.extend(valuesCMarkerVeh[1].marker.getPosition());
		});
	}

	function verifiPto(arrayPto,element) {
		for (var i = 0; i < arrayPto.length; i++) {
			if (arrayPto[i][0] == element) {
				return true;
			}
		}
		return false;
	}

	function clickInfo(id){
		var ptoAux =getPto(GlobalMarkers,id);
		if(ptoAux!=null){
			google.maps.event.trigger(ptoAux[2], 'click');
		}	else{
			alertError({title:"Notificaci&oacute;n Mapa",text:"Cliente sin Coordenadas",delay:"5000"});
		}
	}

	function getPto(array,element) {
		for (var i = 0; i < array.length; i++) {
			if (array[i][0] == element) {
				return array[i];
			}
		}
		return null;
	}

	function loadVehiculos(){
		$.ajax({
			url:'controller.php?x='+Math.random(),
			type: "POST",
			timeout:40000,
			error: function(x, t, m) {
				if(t==="timeout") {
				}

			},
			dataType:'json',
			data: {
				acc:"load_vehiculos"
			},
			cache: false,
			beforeSend:function(){
				$('#splash-inline').show();
			},
			success:function(data) {
				validateSession(data);

				$.each(data.aaData, function( index, valuesCMarkerLVeh ) {
					if(valuesCMarkerLVeh.Lat!="" && valuesCMarkerLVeh.Logi!="" && valuesCMarkerLVeh.Lat!="0" && valuesCMarkerLVeh.Logi!="0"){
						iniciateVehiculos(valuesCMarkerLVeh);
						var tax = '<option value="'+valuesCMarkerLVeh.codigo_gps+'" >'+valuesCMarkerLVeh.alias+'</option>';
						$('#select2-minin').append(tax);
					}
				});

				if(MapsAutoZoom){
					maps.fitBounds(bounds);
				}

				$('#splash-inline').hide();
				setInterval(function(){
					actualizeVehiculos();
				},30000);
			}

		});
	}

	function setHtmlInfoVeh(car){
		var htmlDato='<b>Alias:</b> '+car.alias+'<br/>';
		htmlDato+='<b>Marca:</b> '+car.marca+' '+car.modelo+'<br/>';
		htmlDato+='<b>Velocidad:</b> '+car.Velocidad+' KM/H<br/>';
		htmlDato+='<b>Fecha GPS:</b> '+car.FechaGPS+'<br/>';

		//tabs imagen
		var htmlImgs="";

		if(car.imagen!="" && car.imagen!= null){
			var auxImg= "img_v/"+car.imagen;
		}else{
			var auxImg= "not_found.jpg";
		}

		htmlImgs="<div align='center' ><img width='248px' height='194px' alt='Imagen del Vehiculo"+car.codigo_gps+"'  style='height:194px !important'";
		htmlImgs+=" src='<?php echo $CONFIG['HOST'];?>img/"+auxImg+"' onError='this.src=\"<?php echo $CONFIG['HOST'];?>img/not_found.jpg\"' /></div>";

		var htmlIfos='<div class="" style="width:260px !important;  margin-bottom:5px !important">';
		htmlIfos+='	<div class="tabbable-custom " style="margin-bottom:0px">';
		htmlIfos+='		<ul  class="nav nav-tabs mapx <?php echo $CONFIG['WEB_THEME'];?>">';
		htmlIfos+='			 <li class="active bordered-right">';
		htmlIfos+='				<a href="#tabs-1" id="showDatos" data-toggle="tab">Datos</a>';
		htmlIfos+='			 </li>';
		htmlIfos+='			 <li class="bordered-right">';
		htmlIfos+='				<a href="#tabs-2" id="showFoto" data-toggle="tab">Foto</a>';
		htmlIfos+='			 </li>';
		htmlIfos+='		</ul>';
		htmlIfos+='		<div class="tab-content" style="padding:0px !important;">';
		htmlIfos+='			<div class="tab-pane fade in active" id="tabs-1" style="padding:2px !important;">';
		htmlIfos+='				'+htmlDato;
		htmlIfos+='			</div>';
		htmlIfos+='			<div class="tab-pane fade " id="tabs-2" style="padding:2px !important;">';
		htmlIfos+='				'+htmlImgs;
		htmlIfos+='			</div>';
		htmlIfos+='		</div>';
		htmlIfos+='	</div>';
		htmlIfos+='</div>';

		return htmlIfos
	}

	function iniciateVehiculos(car){
		var myLatLng=new google.maps.LatLng(car.Lat,car.Logi);

		bounds.extend(new google.maps.LatLng(car.Lat,car.Logi));

		var icon = "";
		if(car.iconMap!=""){
			icon = '<?php echo $CONFIG['HOST'];?>img/iconmap/'+car.iconMap;
		}

		var markers = new google.maps.Marker({
			position: myLatLng,
			map: maps,
			icon:icon,
			title:car.codigo_gps
		});

		var html = setHtmlInfoVeh(car);

		var infoVehiculo = new google.maps.InfoWindow({
			content: html
		});

		google.maps.event.addListener(markers, 'click', function(){
			LimpiarInfoVeh();
			infoVehiculo.open(maps,this);
			maps.panTo(myLatLng);
			maps.panTo(myLatLng);
		});

		var arrayVeh = {
			id:car.id,
			alias:car.alias,
			gps:car.codigo_gps,
			info:infoVehiculo,
			marker:markers,
			desp:{
				st:0, //0 = disponible, 1 = ocupada
				client:0
			},
			setDesp:function(_st,_client){
				this.desp.st=_st;
				this.desp.client=_client;
			}
		}

		GlobalMarkersVehic.push([car.codigo_gps,arrayVeh]);
	}

	function LimpiarInfoVeh(){
		var a;
		for(a in GlobalMarkersVehic){
			GlobalMarkersVehic[a][1].info.close();
		}
	}

	function actualizeVehiculos(){
		$.ajax({
			url:'controller.php?x='+Math.random(),
			type: "POST",
			timeout:40000,
			error: function(x, t, m) {
				if(t==="timeout") {
				}

			},
			dataType:'json',
			data: {
				acc:"load_vehiculos"
			},
			cache: false,
			beforeSend:function(){
				$('#splash-inline').show();
			},
			success:function(data) {
				validateSession(data);

				$.each(data.aaData, function( index, valuesCMarkerACVeh ) {
					if(valuesCMarkerACVeh.Lat!="" && valuesCMarkerACVeh.Logi!="" && valuesCMarkerACVeh.Lat!="0" && valuesCMarkerACVeh.Logi!="0"){
						var car = valuesCMarkerACVeh;
						var myLatLng=new google.maps.LatLng(car.Lat,car.Logi);
						var vehiculo = getPto(GlobalMarkersVehic,car.codigo_gps);

						if(vehiculo!=null){
							vehiculo[1].info.setContent(setHtmlInfoVeh(car));
							vehiculo[1].marker.setPosition(myLatLng);
						}
					}
				});

				$('#splash-inline').hide();
			}

		});
	}

	function MapAutoZoom(){
		var elemt=$("#iconzoom");

		if(elemt.hasClass("fa-eye")){
			var alertTxt = "Seguro desea <b>desactivar</b> el zoom automatico.? <br/>";
			alertTxt+= "Tome en cuenta que si lo desactiva es posible que no vea reflejados puntos en el mapa.";
			bootbox.confirm(alertTxt,function(result) {
				if (result) {
					elemt.removeClass("fa-eye");
					elemt.addClass("fa-eye-slash");
					MapsAutoZoom = false;
					$(".textZoomMap").text(" Activar zoom automatico");
					$(".checkZoomMap").prop("checked",false);
				}else{
					$(".textZoomMap").text(" Desactivar zoom automatico");
					$(".checkZoomMap").prop("checked",true);
				}
			});
		}else{
			elemt.removeClass("fa-eye-slash");
			elemt.addClass("fa-eye");
			maps.fitBounds(bounds);
			MapsAutoZoom = true;
			$(".textZoomMap").text("Desactivar zoom automatico");
			$(".checkZoomMap").prop("checked",true);
		}
	}

	function viewInfoVeh(a){
		var vehiculo = getPto(GlobalMarkersVehic,a);
		if(vehiculo!=null){
			google.maps.event.trigger(vehiculo[1].marker, 'click');
		}

		$("#select2-minin").find('option').removeAttr("selected");
		$("#select2-minin").change();
	}

	function despacho(){
		if(markerGlobal==null){
			alertError({text:"Seleccione un cliente"});
			return false;
		}

		var puntiInicio = markerGlobal.mark.getPosition();
		var cercano = null;
		var MenorDistancia = 0;
		var bamOne = 0; //bandera para poner por default la primera patrulla libre

		$.each(GlobalMarkersVehic, function(index,valuesCMarkerMVeh){
			if(valuesCMarkerMVeh[1].desp.st==0 || valuesCMarkerMVeh[1].desp.client == markerGlobal.client){
				var patrullaX = valuesCMarkerMVeh[1].marker.getPosition();
				var distancia = google.maps.geometry.spherical.computeDistanceBetween(patrullaX,puntiInicio);

				if(bamOne==0){
					MenorDistancia = distancia;
					cercano = valuesCMarkerMVeh;
					bamOne=1;
				}

				if(parseInt(distancia) < MenorDistancia){
					MenorDistancia = distancia;
					cercano = valuesCMarkerMVeh;
				}
			}
		});

		if(cercano!=null){
			cercano[1].setDesp(1,markerGlobal.client);

			calcRoute({start:cercano[1].marker.getPosition(),end:puntiInicio});
			alertSuccess({text:cercano[1].alias+" fue asignado"});
		}else{
			alertNotice({text:"No hay patrulla disponibles"});
		}
	}

	function LiberarPatrulla(objx){
		$.each(GlobalMarkersVehic, function(index,valuesVeha){
			if(valuesVeha[1].desp.client == objx[0]){
				valuesVeha[1].setDesp(0,0);
				directionsDisplay.setDirections({routes:[],status:"OK",mc:{}});
			}
		});
	}
</script>
	<?php include("../include/diseno/i_headerMonitoreoMaps.php");?>
    <body style=" background:#FFF; "  onload="">
        <div id="map" style="width:600px;height:100%; margin:0;">
        	Cargando...
        </div>
        <div   style='display croll;position:fixed;bottom:30px;right:10px;' title='volver arriba'>
        	<img src="<?php echo $CONFIG['HOST'];?>img/logo_empresas/<?php echo $CONFIG['LOGO_PAGES'];?>" />
        </div>
    </body>
</html>