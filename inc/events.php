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
		$this->register_post_type();
		$this->register_field_group();
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
			'rewrite' => array('slug' => 'event', 'with_front' => false)
		);

		register_post_type('event', $args);

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