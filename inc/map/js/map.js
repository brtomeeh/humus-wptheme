/*
 * Humus
 * Map
 */

(function($) {

	var map,
		tileLayer,
		markers = [],
		locationLayers = {},
		markerLayer,
		mapOptions = {
			scrollWheelZoom: false,
			attributionControl: false,
			center: [0,0],
			zoom: 3
		},
		mapInit = _.once(function() {

			map.fitBounds(markerLayer.getBounds());

			if(humus_map.zoom && !isNaN(humus_map.zoom)) {
				map.setZoom(humus_map.zoom);
			}

			mapView();

		}),
		createLayer = function(geojson) {

			return L.geoJson(geojson, {
				pointToLayer: function(f, latlng) {
					var marker = new L.marker(latlng);
					markers.push(marker);
					return marker;
				}
			});

		};

	map = L.map(humus_map.canvas, mapOptions);

	tileLayer = L.tileLayer(humus_map.tiles);

	tileLayer.on('load', mapInit);

	tileLayer.addTo(map);

	// Separete marker layers per location
	var locationFeatures = [];
	_.each(humus_map.geojson.features, function(feature) {

		var location = feature.properties.location;

		if(typeof locationFeatures[location] !== 'object') {
			locationFeatures[location] = {
				type: 'FeatureCollection',
				features: []
			};
		}

		locationFeatures[location].features.push(feature);

	});

	// Set marker layer as feature group

	markerLayer = L.featureGroup();

	// Add location layers to feature group (markerLayer)

	for(var location in locationFeatures) {

		var layer = createLayer(locationFeatures[location]);

		locationLayers[location] = layer;

		markerLayer.addLayer(layer);

	}

	markerLayer.addTo(map);

	/*
	 * Map view
	 */

	var mapView = function() {

		var container = $('.map-view');

		if(container.length) {

			var items = container.find('.post-list .navigation-item'),
				locations = items.filter('.location'),
				posts = items.filter('.post'),
				scrollLocation = items.filter(':first').attr('id'),
				post;

			var init = function(silent) {

				if(locations.length) {
					locations.show();
					container.find('.button.location-list').hide();
					locations.find('.button.this').show();
					posts.hide();
				}

				$('#' + scrollLocation).addClass('active');
				locate($('#' + scrollLocation).data('postid'));

				fragmentNavigate();

			};

			var home = function(silent) {

				$('.location-dropdown li').removeClass('active');
				$('.location-dropdown li.all').addClass('active');

				closePost();

				if(locations.length) {
					locations.show();
					container.find('.button.location-list').hide();
					locations.find('.button.this').show();
					posts.hide();
				}

				fragment().rm('location');
				fragment().rm('post');

				if(typeof silent === 'undefined' || !silent) {
					$('html,body').animate({
						scrollTop: 0
					}, 400, function() {
						map.fitBounds(markerLayer.getBounds());
					});
				}

			};

			var fragment = function() {

				var f = {};
				var _set = function(query) {
					var hash = [];
					_.each(query, function(v, k) {
						hash.push(k + '=' + v);
					});
					document.location.hash = '!/' + hash.join('&');
				};
				f.set = function(options) {
					_set(_.extend(f.get(), options));
				};
				f.get = function(key, defaultVal) {
					var vars = document.location.hash.substring(3).split('&');
					var hash = {};
					_.each(vars, function(v) {
						var pair = v.split("=");
						if (!pair[0] || !pair[1]) return;
						hash[pair[0]] = unescape(pair[1]);
						if (key && key == pair[0]) {
							defaultVal = hash[pair[0]];
						}
					});
					return key ? defaultVal : hash;
				};
				f.rm = function(key) {
					var hash = f.get();
					hash[key] && delete hash[key];
					_set(hash);
				};
				return f;

			};

			var fromPost = fragment().get('post') ? true : false;

			var fragmentNavigate = _.debounce(function() {
				if(fragment().get('post')) {
					openLocation(fragment().get('location'), fromPost);
					openPost(fragment().get('post'));
				} else if(fragment().get('location')) {
					openLocation(fragment().get('location'), fromPost);
				} else {
					home();
				}
				fromPost = fragment().get('post') ? true : false;
			}, 10);

			var locate = function(id) {

				var marker = _.filter(markers, function(m) { return m.toGeoJSON().properties.id == id; });

				if(marker.length) {

					$('.map-container').removeClass('disabled');

					marker = marker[0];

					map.setView(marker.getLatLng(), 15);

				} else if(typeof id === 'string' && locationLayers[id]) {

					$('.map-container').removeClass('disabled');

					var bounds = locationLayers[id].getBounds();

					map.setView(bounds.getCenter(), 12);

				} else {

					$('.map-container').addClass('disabled');

				}

			};

			var scrollLocate = function() {

				var halfWindow = $(window).height() / 2;

				items.each(function() {

					if($(this).is(':visible')) {

						var relTop = $(this).offset().top - $(window).scrollTop();
						var relBottom = relTop + $(this).innerHeight();

						if(relTop <= halfWindow && relBottom >= halfWindow) {

							if(scrollLocation !== $(this).attr('id')) {

								scrollLocation = $(this).attr('id');

								var id = $(this).data('postid') ? $(this).data('postid') : $(this).data('location');

								locate(id);

							}

							items.removeClass('active');
							$(this).addClass('active');

						}

					}

				})

			};

			var openLocation = function(locationName, silent) {

				var location = locations.filter('[data-location="' + locationName + '"]');

				if(!location.length)
					return false;

				closePost();

				locations.hide();
				posts.hide();

				location.show();
				posts.filter('[data-location="' + locationName + '"]').show();

				container.find('.button.location-list').show();
				locations.find('a.button.this').hide();

				$('.location-dropdown li').removeClass('active');
				$('.location-dropdown li[data-location="' + locationName + '"]').addClass('active');

				locate(locationName);

				fragment().set({'location': locationName});

				var firstPost = posts.filter('[data-location="' + locationName + '"]:first');

				if(!fragment().get('post')) {

					if((typeof silent === 'undefined' || !silent)) {

						var center = firstPost.offset().top - ($(window).height()/2) + (firstPost.innerHeight()/2);

						locate(firstPost.data('postid'));

						$('html,body').stop().animate({
							scrollTop: center
						}, 400);

					} else {

						$('#' + scrollLocation).addClass('active');
						locate($('#' + scrollLocation).data('postid'));

					}

				}

				$(window).trigger('scroll');

			};

			var openedPostHeight = function() {

				post.stop().animate({
					height: $(window).height() - 300
				}, 400);

			}

			var openPost = function(postid) {

				closePost();

				post = posts.filter('[data-postid="' + postid + '"]');

				if(post.length) {

					post.addClass('post-active active');
					fragment().set({'post': postid});

					$(window).bind('resize', openedPostHeight).resize();
					$(window).unbind('scroll', scrollLocate);

					$('body').css({overflow:'hidden'});

					locate(postid);

					$('#content').addClass('dark');
					$('.map-container').addClass('disabled');

					$('html,body').stop().animate({
						scrollTop: post.offset().top - 150
					}, 400, _.once(function() {
						post.find('.post-excerpt').hide();
						post.find('.post-content').show();

						if(post.find('.video').length)
							post.find('.video').clone().appendTo(container.find('#media'));

						container.find('#media').show();
						container.fitVids();

					}));

				}

			};

			var closePost = function() {

				if(typeof post !== 'undefined') {
					$('#content').removeClass('dark');
					$('.map-container').removeClass('disabled');
					post.find('.post-excerpt').show();
					post.find('.post-content').hide();
					post.css({height: 'auto'});
					post.removeClass('post-active');
					container.find('#media').empty().hide();
					$('body').css({overflow:'auto'});
					$(window).bind('scroll', scrollLocate).scroll();
					$(window).unbind('resize', openedPostHeight).resize();
					post = undefined;
				}

			};

			init();

			$(window).bind('scroll', scrollLocate);
			$(window).bind('hashchange', fragmentNavigate);

			locations.click(function() {
				fragment().set({'location': $(this).data('location')});
				return false;
			});

			posts.find('.button.this').click(function() {
				fragment().set({'post': $(this).parents('.post').data('postid')});
				return false;
			});

			posts.find('.close-post').click(function() {
				fragment().rm('post');
				return false;
			})

			container.find('.button.location-list').click(function() {
				fragment().rm('post');
				fragment().rm('location');
				return false;
			});

			$('.location-dropdown li a').click(function() {
				var location = $(this).parent().data('location');

				if(location)
					fragment().set({'location': location});
				else
					home();

				return false;
			});

		}

	};

})(jQuery);