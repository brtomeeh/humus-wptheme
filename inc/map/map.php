<?php

/* Humus
 * Map features
 */

class Humus_Map {

	function __construct() {

		require_once(TEMPLATEPATH . '/inc/acf/add-ons/acf-location-field/acf-location.php');
		include_once(TEMPLATEPATH . '/inc/map/tiles.php');
		add_action('init', array($this, 'init'), 100);

	}

	function init() {

		if(!function_exists('register_field_group'))
			return false;

		$this->register_location_field();
		$this->register_location_taxonomy();

		add_action('wp_head', array($this, 'register_scripts'), 100);
		add_filter('post_class', array($this, 'post_class'));
		add_action('humus_before_header_content', array($this, 'map'));
		add_action('humus_list_article_after_title', array($this, 'list_article_after_title'));

		add_filter('query_vars', array($this, 'location_query_var'));
		add_filter('posts_clauses', array($this, 'location_clauses'), 10, 2);

		$this->setup_template();

		add_action('humus_header_content', array($this, 'location_dropdown'));

	}

	function get_post_types() {

		return apply_filters('humus_map_post_types', array('post'));

	}

	function get_taxonomies() {
		return apply_filters('humus_map_taxonomies', array('location'));
	}

	function get_map_view_terms() {
		// list of term objects
		return apply_filters('humus_map_view_terms', array());
	}

	function is_map_view() {

		$terms = $this->get_map_view_terms();

		if(in_array(get_queried_object(), $terms))
			return true;

		return false;

	}

	function register_location_field() {

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
					)
				);
			}

		}

		$taxonomies = $this->get_taxonomies();

		if(is_array($taxonomies) && !empty($taxonomies)) {

			foreach($taxonomies as $taxonomy) {

				$locations[] = array(
					array(
						'param' => 'ef_taxonomy',
						'operator' => '==',
						'value' => $taxonomy,
						'order_no' => 0,
						'group_no' => 0
					)
				);
			}

		}

		if(!empty($locations)) {

			$field_group['location'] = $locations;

			$field_group = apply_filters('humus_location_field_group', $field_group);
			
			register_field_group($field_group);
	
		}

	}

	function register_location_taxonomy() {

		$labels = array(
			'name' => _x('Locations', 'Location general name', 'humus'),
			'singular_name' => _x('Location', 'Location singular name', 'humus'),
			'all_items' => __('All locations', 'humus'),
			'edit_item' => __('Edit location', 'humus'),
			'view_item' => __('View location', 'humus'),
			'update_item' => __('Update location', 'humus'),
			'add_new_item' => __('Add new location', 'humus'),
			'new_item_name' => __('New location name', 'humus'),
			'parent_item' => __('Parent location', 'humus'),
			'parent_item_colon' => __('Parent location:', 'humus'),
			'search_items' => __('Search locations', 'humus'),
			'popular_items' => __('Popular locations', 'humus'),
			'separate_items_with_commas' => __('Separate locations with commas', 'humus'),
			'add_or_remove_items' => __('Add or remove locations', 'humus'),
			'choose_from_most_used' => __('Choose from most used locations', 'humus'),
			'not_found' => __('No locations found', 'humus')
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_admin_column' => true,
			'hierarchical' => true,
			'query_var' => 'location',
			'rewrite' => array('slug' => 'locations', 'with_front' => false)
		);

		register_taxonomy('location', $this->get_post_types(), $args);

	}

	function location_query_var($vars) {
		$vars[] = 'humus_location';
		return $vars;
	}

	function get_current_location() {

		global $wp_query;

		$location = false;
		if($wp_query->get('humus_location'))
			$location = $wp_query->get('humus_location');

		return $location;

	}

	function location_dropdown() {

		if(!$this->is_map_view())
			return false;

		$locations = get_terms('location');
		if(!$locations)
			return false;

		wp_enqueue_style('humus-map');

		$current = $this->get_current_location();

		?>
		<div class="two columns">
			<div class="location-dropdown">
				<div class="humus-dropdown">
					<ul>
						<li class="all <?php if(!$current) echo 'active'; ?>"><a href="<?php echo remove_query_arg('humus_location'); ?>"><?php _e('Locations', 'humus'); ?></a></li>
						<?php foreach($locations as $location) : ?>
							<li data-location="<?php echo $location->slug; ?>" <?php if($current == $location->term_id) echo 'class="active"'; ?>><a href="<?php echo add_query_arg(array('humus_location' => $location->term_id)); ?>"><?php echo $location->name; ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
		<?php
	}

	function location_clauses($clauses, $query) {

		global $wp_the_query, $wpdb;

		if($query->get('humus_location') && $wp_the_query === $query && $query->get('list')) {

			$key = 'humus_location';
			$term_id = $query->get('humus_location');

			$clauses['join'] .= "
				INNER JOIN {$wpdb->term_relationships} AS {$key} ON ({$wpdb->posts}.ID = {$key}.object_id)
				";

			$clauses['where'] .= " AND ( {$key}.term_taxonomy_id IN ({$term_id}) ) ";

		}

		return $clauses;

	}

	function get_map_tile_default_color() {
		return 'f0f0f0';
	}

	function get_map_tile() {

		$color = $GLOBALS['humus_page_color'];

		$color = $color ? str_replace('#', '', $color) : $this->get_map_tile_default_color();

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

		return apply_filters('humus_map_tiles', $tile, $color);
	}

	function get_map_zoom() {
		$this->zoom = false;
		if(is_single())
			$this->zoom = 14;

		return apply_filters('humus_map_zoom', $this->zoom);
	}

	function register_scripts() {

		wp_register_script('leaflet', get_template_directory_uri() . '/inc/map/js/leaflet.js', array(), '0.6.4');
		wp_register_style('leaflet', get_template_directory_uri() . '/inc/map/css/leaflet.css');
		wp_register_style('leaflet-ie', get_template_directory_uri() . '/inc/map/css/leaflet.ie.css');
		$GLOBALS['wp_styles']->add_data('leaflet-ie', 'conditional', 'lte IE 8');

		wp_register_script('jquery-hashchange', get_template_directory_uri() . '/inc/map/js/jquery.ba-hashchange.min.js', array('jquery'), '1.3.1');

		wp_register_script('humus-map', get_template_directory_uri() . '/inc/map/js/map.js', array('jquery', 'underscore', 'leaflet', 'fitvids', 'jquery-hashchange'), '0.1.3');

		wp_localize_script('humus-map', 'humus_map', array(
			'tiles' => $this->get_map_tile(),
			'geojson' => $this->get_geojson(),
			'canvas' => 'map',
			'zoom' => $this->get_map_zoom(),
			'pin' => array(
				'iconUrl' => get_template_directory_uri() . '/inc/map/img/pin_small.png',
				'iconSize' => array(30, 48),
				'iconAnchor' => array(15, 48),
				'popupAnchor' => array(0, -52)
			),
			'pin_active' => array(
				'iconUrl' => get_template_directory_uri() . '/inc/map/img/pin_big.png',
				'iconSize' => array(50, 80),
				'iconAnchor' => array(25, 80),
				'popupAnchor' => array(0, -84)
			)
		));

		wp_register_style('humus-map', get_template_directory_uri() . '/inc/map/css/map.css', array('leaflet', 'leaflet-ie'), '0.1.0');

	}

	function enqueue_scripts() {
		wp_enqueue_script('humus-map');
		wp_enqueue_style('humus-map');
	}

	function object_has_map() {

		$obj = get_queried_object();

		$taxonomies = $this->get_taxonomies();

		if(is_tax($taxonomies)) {

			return $this->get_location($obj->taxonomy . '_' . $obj->term_id) ? true : false;

		} elseif(is_single()) {

			if($this->get_location($obj->ID)) {
				return true;
			}

			foreach($taxonomies as $taxonomy) {
				$terms = get_the_terms($obj->ID, $taxonomy);
				if($terms) {
					foreach($terms as $term) {
						if($this->get_location($taxonomy . '_' . $term->term_id))
							return true;
					}
				}
			}

		}

		return false;
	}

	function get_location($id = false) {

		global $post;
		$id = $id ? $id : $post->ID;

		return get_field('location', $id);

	}

	function get_coordinates($id = false) {

		$location = $this->get_location($id);

		if($location)
			return split(',', $location['coordinates']);

		return false;

	}

	function get_address($id = false) {

		$location = $this->get_location($id);

		if($location)
			return $location['address'];

		return false;

	}

	function get_geojson($query = false) {
		global $wp_query;

		$query = $query ? $query : $wp_query;

		$geojson = array(
			'type' => 'FeatureCollection',
		);
		$features = array();

		if(is_tax($this->get_taxonomies())) {

			$term = get_queried_object();

			$coordinates = $this->get_coordinates($term->taxonomy . '_' . $term->term_id);

			if($coordinates) {
					
				$feature = array(
					'type' => 'Feature',
					'geometry' => array(
						'type' => 'Point',
						'coordinates' => array(
							floatval($coordinates[1]),
							floatval($coordinates[0])
						)
					),
					'properties' => array(
						'termid' => $term->term_id,
						'term_name' => $term->name,
						'term_description' => $term->description
					)
				);

				$features[] = $feature;

			}

		} elseif(is_single() && !get_field('location')) {

			$taxonomies = $this->get_taxonomies();

			foreach($taxonomies as $tax) {

				$terms = get_the_terms($post->ID, $tax);
				if($terms) {

					foreach($terms as $term) {

						$coordinates = $this->get_coordinates($term->taxonomy . '_' . $term->term_id);

						if($coordinates) {
								
							$feature = array(
								'type' => 'Feature',
								'geometry' => array(
									'type' => 'Point',
									'coordinates' => array(
										floatval($coordinates[1]),
										floatval($coordinates[0])
									)
								),
								'properties' => array(
									'termid' => $term->term_id,
									'term_name' => $term->name,
									'term_description' => $term->description
								)
							);

							$features[] = $feature;

						}

					}

				}

			}

		} elseif($query->have_posts()) {

			while($query->have_posts()) {

				the_post();

				$coordinates = $this->get_coordinates();

				if($coordinates) {

					global $post;

					$location = get_the_terms($post->ID, 'location');
					if($location)
						$location = array_shift($location);

					$feature = array(
						'type' => 'Feature',
						'geometry' => array(
							'type' => 'Point',
							'coordinates' => array(
								floatval($coordinates[1]),
								floatval($coordinates[0])
							)
						),
						'properties' => array(
							'postid' => get_the_ID(),
							'date' => get_the_date(),
							'post_title' => get_the_title(),
							'excerpt' => get_the_excerpt(),
							'location' => $location->slug
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
		if($this->object_has_map() || $force) {
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
		if($this->object_has_map()) {
			$class[] = 'map';
		}
		return $class;
	}

	function list_article_after_title($content) {
		global $post;
		$location = get_the_terms($post->ID, 'location');
		if($location) {
			$location = array_shift($location);
			?>
			<p class="location"><?php echo $location->name; ?></p>
			<?php
		}
	}

	/*
	 * Map view template
	 */

	function setup_template() {
		add_filter('query_vars', array($this, 'query_vars'));
		add_action('humus_header_content', array($this, 'map_view_dropdown'));
		add_action('template_redirect', array($this, 'template_redirect'));
		add_filter('body_class', array($this, 'body_class'));
		add_filter('embed_oembed_html', array($this, 'embed_oembed_html'));
	}

	function map_view_dropdown() {

		if(!$this->is_map_view())
			return false;
	
		global $wp_query;
		wp_enqueue_style('humus-map');
		$is_map_view = (!$wp_query->get('list'));
		?>
		<div class="three columns">
			<div class="map-view-dropdown">
				<p class="label"><?php _e('View as', 'humus'); ?></p>
				<div class="humus-dropdown">
					<ul>
						<li <?php if($is_map_view) echo 'class="active"'; ?>><a href="<?php echo remove_query_arg('list', remove_query_arg('humus_location')); ?>"><?php _e('Map', 'humus'); ?></a></li>
						<li <?php if(!$is_map_view) echo 'class="active"'; ?>><a href="<?php echo add_query_arg(array('list' => 1)); ?>"><?php _e('List', 'humus'); ?></a></li>
					</ul>
				</div>
			</div>
		</div>
		<?php
	}

	function query_vars($vars) {
		$vars[] = 'map';
		$vars[] = 'list';
		return $vars;
	}

	function template_redirect() {

		global $wp_query;
		if(!$wp_query->get('list') && $this->is_map_view()) {
			include_once(TEMPLATEPATH . '/inc/map/template.php');
			exit;
		}
	}

	function body_class($class) {
		global $wp_query;
		$obj = get_queried_object();
		if(!$wp_query->get('list') && $this->is_map_view()) {
			$class[] = 'map-view';
		} elseif(is_tax($this->get_taxonomies()) && get_field('location', $obj->taxonomy . '_' . $obj->term_id)) {
			$class[] = 'map';
		}
		return $class;
	}

	function get_map_view_url($post_id = false, $tax = false) {

		global $post;
		$post_id = $post_id ? $post_id : $post->ID;

		if(!$tax)
			return false;

		$term = get_the_terms($post_id, $tax);

		if($term)
			$term = array_shift($term);
		else 
			return false;

		$url = get_term_link($term);

		$url .= '#!/';

		$location = get_the_terms($post_id, 'location');
		if($location){
			$location = array_shift($location);
			$url .= 'location=' . $location->slug . '&';
		}

		$url .= 'post=' . $post_id;

		return $url;

	}

	function embed_oembed_html($html) {
		global $wp_query;
		if(!$wp_query->get('list') && $this->is_map_view() && !is_admin()) {
			return '';
		}
		return $html;
	}

}

$GLOBALS['humus_map'] = new Humus_Map();

function humus_map() {
	$GLOBALS['humus_map']->map(true);
}

function humus_get_location($id = false) {
	return $GLOBALS['humus_map']->get_location($id);
}

function humus_get_address($id = false) {
	return $GLOBALS['humus_map']->get_address($id);
}

function humus_get_map_view_url($post_id = false, $tax = 'section') {
	return $GLOBALS['humus_map']->get_map_view_url($post_id, $tax);
}