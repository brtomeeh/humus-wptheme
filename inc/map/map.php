<?php

/* Humus
 * Map features
 */

class Humus_Map {

	function __construct() {

		require_once(TEMPLATEPATH . '/inc/acf/add-ons/acf-location-field/acf-location.php');
		add_action('init', array($this, 'init'), 100);

	}

	function init() {

		if(function_exists('register_field_group')) {
			$this->register_field();
		}

		add_action('wp_head', array($this, 'register_scripts'), 100);
		add_filter('post_class', array($this, 'post_class'));
		add_action('humus_before_header_content', array($this, 'map'));

		$this->setup_template();

	}

	function get_post_types() {

		return apply_filters('humus_map_post_types', array('post'));

	}

	function register_field() {

		$field_group = array(
			'id' => 'acf_location',
			'title' => __('Location', 'humus'),
			'fields' => array(
				array(
					'key' => 'field_map_location',
					'label' => __('Location', 'humus'),
					'name' => 'location',
					'type' => 'location-field',
					'val' => 'address',
					'mapheight' => 300,
					'center' => '0,0',
					'zoom' => 2,
					'scrollwheel' => 1,
					'mapTypeControl' => 1,
					'streetViewControl' => 1,
					'PointOfInterest' => 1,
				),
			),
			'options' => array(
				'position' => 'normal',
				'layout' => 'default',
				'hide_on_screen' => array(),
			),
			'menu_order' => 0,
		);

		$locations = array();

		$post_types = $this->get_post_types();

		if(is_array($post_types) && !empty($post_types)) {

			foreach($post_types as $post_type) {

				$locations[] = array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => $post_type,
						'order_no' => 0,
						'group_no' => 0,
					),
				);
			}

		}

		if(!empty($locations)) {

			$field_group['location'] = $locations;

			$field_group = apply_filters('humus_location_field_group', $field_group);
			
			register_field_group($field_group);
	
		}

	}

	function get_map_tile() {

		$color = $GLOBALS['humus_page_color'];

		$color = $color ? str_replace('#', '', $color) : 'f0f0f0';

		/*
		 * Gray background
		 * MapBox lines & labels
		 */
		//$style = '($f0f0f0[@p],(mapbox-water,$fff[@34],$ffc72d[hsl-color]),(streets-and-labels,$000[@22],$ffc72d[hsl-color]))';
	
		/*
		 * Black background
		 * Toner lines & labels
		 */
		$style = '(toner,$fff[difference],$000[@22],$' . $color . '[hsl-color])';

		$tile = 'http://{s}.sm.mapstack.stamen.com/' . $style . '/{z}/{x}/{y}.png';

		//$tile = 'http://tile.stamen.com/toner/{z}/{x}/{y}.png';

		return apply_filters('humus_map_tiles', $tile);
	}

	function get_map_zoom() {
		$zoom = false;
		if(is_single())
			$zoom = 14;

		return apply_filters('humus_map_zoom', $zoom);
	}

	function register_scripts() {

		wp_register_script('leaflet', get_template_directory_uri() . '/inc/map/js/leaflet.js', array(), '0.6.4');
		wp_register_style('leaflet', get_template_directory_uri() . '/inc/map/css/leaflet.css');
		wp_register_style('leaflet-ie', get_template_directory_uri() . '/inc/map/css/leaflet.ie.css');
		$GLOBALS['wp_styles']->add_data('leaflet-ie', 'conditional', 'lte IE 8');

		wp_register_script('humus-map', get_template_directory_uri() . '/inc/map/js/map.js', array('jquery', 'leaflet'), '0.1.0');

		wp_localize_script('humus-map', 'humus_map', array(
			'tiles' => $this->get_map_tile(),
			'geojson' => $this->get_geojson(),
			'canvas' => 'map',
			'zoom' => $this->get_map_zoom()
		));

		wp_register_style('humus-map', get_template_directory_uri() . '/inc/map/css/map.css', array('leaflet', 'leaflet-ie'), '0.1.0');

	}

	function enqueue_scripts() {
		wp_enqueue_script('humus-map');
		wp_enqueue_style('humus-map');
	}

	function get_geojson($query = false) {
		global $wp_query;

		$query = $query ? $query : $wp_query;

		$geojson = array(
			'type' => 'FeatureCollection',
		);
		$features = array();

		if($query->have_posts()) {
			while($query->have_posts()) {

				the_post();

				$location = get_field('location');
				if($location) {

					$latlng = split(',', $location['coordinates']);

					$feature = array(
						'type' => 'Feature',
						'geometry' => array(
							'type' => 'Point',
							'coordinates' => array(
								floatval($latlng[1]),
								floatval($latlng[0])
							)
						),
						'properties' => array(
							'id' => get_the_ID(),
							'date' => get_the_date(),
							'title' => get_the_title(),
							'excerpt' => get_the_excerpt()
						)
					);

					$features[] = $feature;

				}

			}
		}

		$geojson['features'] = $features;

		return $geojson;
	}

	function map($force = false) {
		global $post;
		if(is_single() && get_field('location') || $force) {
			$this->enqueue_scripts();
			?>
			<div class="map-container">
				<div id="map"></div>
				<div class="map-gradient"></div>
			</div>
			<?php
		}
	}

	function post_class($class) {
		global $post;
		if(is_single() && get_field('location')) {
			$class[] = 'map';
		}
		return $class;
	}

	/*
	 * Map post template
	 */

	function setup_template() {
		add_filter('query_vars', array($this, 'query_vars'));
		add_action('template_redirect', array($this, 'template_redirect'));
		add_filter('body_class', array($this, 'body_class'));
	}

	function query_vars($vars) {
		$vars[] = 'map';
		return $vars;
	}

	function template_redirect() {
		global $wp_query;
		if($wp_query->get('map')) {
			include_once(TEMPLATEPATH . '/inc/map/template.php');
			exit;
		}
	}

	function body_class($class) {
		global $wp_query;
		if($wp_query->get('map')) {
			$class[] = 'map-view';
		}
		return $class;
	}

}

$GLOBALS['humus_map'] = new Humus_Map();

function humus_map() {
	$GLOBALS['humus_map']->map(true);
}