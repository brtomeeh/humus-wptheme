/*
 * Humus
 * Map
 */

(function($) {

	var map,
		tileLayer,
		markerLayer;

	$(document).ready(function() {

		var options = {
			scrollWheelZoom: false,
			attributionControl: false
		};

		map = L.map(humus_map.canvas, options).setView([0, 0], 3);
		tileLayer = L.tileLayer(humus_map.tiles);

		tileLayer.addTo(map);

		markerLayer = L.geoJson(humus_map.geojson);
		markerLayer.addTo(map);

		console.log(markerLayer.getBounds());

		//map.fitBounds(markerLayer.getBounds());

		if(humus_map.zoom && !isNaN(humus_map.zoom)) {
			map.setZoom(humus_map.zoom);
		}

		/*
		 * Map view
		 */

		 if($('.map-view').length) {

		 	var items = $('.post-list .navigation-item');

		 	$(window).scroll(function() {

		 		items.each(function() {

		 			var relTop = $(this).offset().top - $(window).scrollTop();

		 			var relBottom = relTop + $(this).innerHeight();

		 			if(relTop <= 300 && relBottom >= 300) {
		 				console.log($(this).attr('id'));
		 			}

		 		})

		 	});

		 }

	});

})(jQuery);