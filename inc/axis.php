<?php

/*
 * Humus
 * Axes
 */

class Humus_Axes {

	function __construct() {
		add_action('init', array($this, 'init'));
	}

	function init() {

		add_filter('humus_header_image_locations', array($this, 'register_header_image'));
		$this->register_taxonomy();

	}

	function register_taxonomy() {

		$labels = array(
			'name' => _x('Axes', 'Axis general name', 'humus'),
			'singular_name' => _x('Axis', 'Axis singular name', 'humus'),
			'all_items' => __('All axes', 'humus'),
			'edit_item' => __('Edit axis', 'humus'),
			'view_item' => __('View axis', 'humus'),
			'update_item' => __('Update axis', 'humus'),
			'add_new_item' => __('Add new axis', 'humus'),
			'new_item_name' => __('New axis name', 'humus'),
			'parent_item' => __('Parent axis', 'humus'),
			'parent_item_colon' => __('Parent axis:', 'humus'),
			'search_items' => __('Search axes', 'humus'),
			'popular_items' => __('Popular axes', 'humus'),
			'separate_items_with_commas' => __('Separate axes with commas', 'humus'),
			'add_or_remove_items' => __('Add or remove axes', 'humus'),
			'choose_from_most_used' => __('Choose from most used axes', 'humus'),
			'not_found' => __('No axes found', 'humus')
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_admin_column' => true,
			'hierarchical' => true,
			'query_var' => 'axis',
			'rewrite' => array('slug' => 'axes', 'with_front' => false)
		);

		register_taxonomy('axis', $this->axis_post_types(), $args);

	}

	function register_header_image($locations) {
		if(!is_array($locations['taxonomy']))
			$locations['taxonomy'] = array();

		$locations['taxonomy'][] = 'axis';

		return $locations;
	}

	function axis_post_types() {
		return apply_filters('humus_axis_post_types', array('post'));
	}

}

$humus_axes = new Humus_Axes();