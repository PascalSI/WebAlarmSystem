<div class="row">
	<div class="col-md-12">
		<div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa marginTop1 fa-globe"></i>
					<span class="font16">Ubicación del Cliente</span>
				</div>
			</div>
			<div class="portlet-body">
				<div class="row">
					<div class="col-md-12">
						<input  type="text" class="form-control" id="buscar" placeholder="Ingrese una Direcci&oacute;n">
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<table width="100%" border="0">
		                    <tr>
		                        <td width="100%;" align="center">
		                            <div  id="map" >Cargando Mapa...</div>
		                        </td>
		                    </tr>
		                </table>
		            </div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>

	var map= null;
	var markerCenter = null
	var cargando_maps=false;

	//ruta
	var directionsDisplay;
	var directionsService = new google.maps.DirectionsService();
	var Emplat ='<?php echo $EmpresaLat;?>';
	var Emplogi = '<?php echo $EmpresaLog;?>' ;


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
	//Fin ruta

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
		if(map==null){


			var lat ='<?php echo $clienteLat;?>';
			var logi = '<?php echo $clienteLog;?>' ;
			var icon = '<?php echo $icon;?>' ;
			var bamlatlog = '<?php echo $BamLatLogCliente;?>';

			if(bamlatlog=='0'){
				alertError({title:"Notificaci&oacute;n Mapa",text:"Cliente sin Coordenadas",delay:"8000"});
			}

			directionsDisplay = new google.maps.DirectionsRenderer({draggable: true,suppressMarkers : true});

			var latlng = new google.maps.LatLng(lat.replace(",","."),logi.replace(",","."));

			var contentEmpresa = '<span class="infowin"><?php echo $_SESSION["cliente"]["nombre_empresa"];?></span>',
			contentCenter = '<span class="infowin"><?php echo $clienNom;?></span>'

			map = new google.maps.Map(document.getElementById('map'), {
				zoom: <?php echo $ZoomMap;?>,
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
				radius: 20,
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

			google.maps.event.addListener(map, 'click', function(e) {
				markerCenter.setPosition(e.latLng)
				latLngCenter = new google.maps.LatLng(markerCenter.position.lat(), markerCenter.position.lng());
				bounds = circle.getBounds();
			});

			google.maps.event.addListener(markerCenter, 'click', function() {
				infoCenter.open(map, markerCenter);
			});

			google.maps.event.addListener(markerempresa, 'click', function() {
				infoEmpresa.open(map, markerempresa);
			});

			google.maps.event.addListener(markerCenter, 'drag', function() {
				infoCenter.close();
			});


			var rutaDiv = document.createElement('map');
			rutaDiv.index = 1;
			map.controls[google.maps.ControlPosition.TOP_RIGHT].push(rutaDiv);
			var rutaDiv_ = new btn_ruta(rutaDiv, map);


			var input = (document.getElementById('buscar'));
			var searchBox = new google.maps.places.SearchBox(input);
			var markers = [];

			google.maps.event.addListener(searchBox, 'places_changed', function() {
				var places = searchBox.getPlaces();

				for (var i = 0, marker; marker = markers[i]; i++) {
					marker.setMap(null);
				}

				markers = [];
				var bounds = new google.maps.LatLngBounds();
				for (var i = 0, place; place = places[i]; i++) {
				var image = {
				  url: place.icon,
				  size: new google.maps.Size(71, 71),
				  origin: new google.maps.Point(0, 0),
				  anchor: new google.maps.Point(17, 34),
				  scaledSize: new google.maps.Size(25, 25)
				};

				var marker = new google.maps.Marker({
				  map: map,
				  icon: image,
				  title: place.name,
				  position: place.geometry.location
				});

				markers.push(marker);

				bounds.extend(place.geometry.location);
				}

				map.fitBounds(bounds);
			});

			google.maps.event.addListener(map, 'bounds_changed', function() {
				var bounds = map.getBounds();
				searchBox.setBounds(bounds);
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



		}
	}
</script>