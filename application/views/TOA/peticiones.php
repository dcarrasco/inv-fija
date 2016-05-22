<div class="content-module-main">
{reporte}
<div id="map_canvas" style="height: 500px"></div>
<hr/>
</div> <!-- fin content-module-main -->

<script>
	<?php echo $arr_makers; ?>
	function initMap() {
		var ubic = new google.maps.LatLng(centro_y, centro_x),
		    map = new google.maps.Map(document.getElementById('map_canvas'), {
				center: ubic,
				zoom: 11
			}),
			bounds = new google.maps.LatLngBounds();
/*
		var rutas = new google.maps.Polyline({
				path: ruta,
				strokeColor: '#FF5555',
				strokeOpacity: 0.5,
				strokeWeight: 3
			});

		rutas.setMap(map);
*/
		for (var i = 0; i < ubicaciones.length; i++) {
			var ubic = ubicaciones[i];
			var marker = new google.maps.Marker({
				position: {lat: ubic[1], lng: ubic[2]},
				map: map,
				title: 'Petición: ' + ubic[0],
				zIndex: ubic[3]
			});

			/*
			marker.addListener('click', function() {
				window.location.href = "<?php echo $link_detalle ?>/"+ubic[0];
			});
			*/

			bounds.extend(marker.position);
		}

		if (ubicaciones.length > 1) {
			map.fitBounds(bounds);
		}

	}
</script>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&callback=initMap" defer async></script>

