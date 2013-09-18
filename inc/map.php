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

}

$humus_map = new Humus_Map();