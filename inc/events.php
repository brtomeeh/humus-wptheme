<?php

/*
 * Humus
 * Events
 */

class Humus_Events {

	function __construct() {

		require_once(TEMPLATEPATH . '/inc/acf/add-ons/acf-field-date-time-picker/acf-date_time_picker.php');
		add_action('init', array($this, 'init'));

	}

	function init() {
		$this->register_location_taxonomy();
		$this->register_post_type();
		$this->register_field_group();

		add_filter('humus_map_taxonomies', array($this, 'register_location_map'));
	}


	function register_post_type() {

		$labels = array( 
			'name' => __('Events', 'humus'),
			'singular_name' => __('Event', 'humus'),
			'add_new' => __('Add event', 'humus'),
			'add_new_item' => __('Add new event', 'humus'),
			'edit_item' => __('Edit event', 'humus'),
			'new_item' => __('New event', 'humus'),
			'view_item' => __('View event', 'humus'),
			'search_items' => __('Search event', 'humus'),
			'not_found' => __('No event found', 'humus'),
			'not_found_in_trash' => __('No event found in the trash', 'humus'),
			'menu_name' => __('Events', 'humus')
		);

		$args = array( 
			'labels' => $labels,
			'hierarchical' => false,
			'description' => __('Humus events', 'humus'),
			'supports' => array('title', 'editor', 'excerpt', 'thumbnail'),
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'has_archive' => false,
			'menu_position' => 4,
			'rewrite' => array('slug' => 'events', 'with_front' => false)
		);

		register_post_type('event', $args);

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
			'query_var' => 'event-location',
			'rewrite' => array('slug' => 'events/locations', 'with_front' => false)
		);

		register_taxonomy('event-location', 'event', $args);

	}

	function register_location_map($taxonomies) {
		$taxonomies[] = 'event-location';
		return $taxonomies;
	}

	function register_field_group() {

		$config = array(
			'id' => 'acf_partner_url',
			'title' => __('Event settings', 'humus'),
			'fields' => array(
				array(
					'key' => 'field_event_time',
					'label' => __('Event time', 'humus'),
					'name' => 'event_time',
					'type' => 'date_time_picker',
					'required' => 1,
					'show_date' => 'true',
					'date_format' => _x('m/d/y', 'Event date', 'humus'),
					'time_format' => _x('h:mm tt', 'Event time', 'humus'),
					'show_week_number' => 'false',
					'picker' => 'slider',
					'save_as_timestamp' => 'true',
					'get_as_timestamp' => 'false',
				),
			),
			'options' => array(
				'position' => 'normal',
				'layout' => 'no_box',
				'hide_on_screen' => array(),
			),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'event',
						'order_no' => 0,
						'group_no' => 0,
					)
				),
			),
			'menu_order' => 0,
		);

		register_field_group($config);

	}

}

$humus_events = new Humus_Events();