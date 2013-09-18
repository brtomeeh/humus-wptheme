<?php

/*
 * Humus
 * Sections
 */

class Humus_Sections {

	function __construct() {
		add_action('init', array($this, 'init'));
	}

	function init() {

		$this->register_taxonomy();
		add_filter('humus_styled_taxonomies', array($this, 'register_taxonomy_styles'));
		add_filter('humus_header_image_locations', array($this, 'register_header_image'));

	}

	function register_taxonomy() {

		$labels = array(
			'name' => _x('Sections', 'Section general name', 'humus'),
			'singular_name' => _x('Section', 'Section singular name', 'humus'),
			'all_items' => __('All sections', 'humus'),
			'edit_item' => __('Edit section', 'humus'),
			'view_item' => __('View section', 'humus'),
			'update_item' => __('Update section', 'humus'),
			'add_new_item' => __('Add new section', 'humus'),
			'new_item_name' => __('New section name', 'humus'),
			'parent_item' => __('Parent section', 'humus'),
			'parent_item_colon' => __('Parent section:', 'humus'),
			'search_items' => __('Search sections', 'humus'),
			'popular_items' => __('Popular sections', 'humus'),
			'separate_items_with_commas' => __('Separate sections with commas', 'humus'),
			'add_or_remove_items' => __('Add or remove sections', 'humus'),
			'choose_from_most_used' => __('Choose from most used sections', 'humus'),
			'not_found' => __('No sections found', 'humus')
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_admin_column' => true,
			'hierarchical' => true,
			'query_var' => 'section',
			'rewrite' => array('slug' => 'sections', 'with_front' => false)
		);

		register_taxonomy('section', $this->section_post_types(), $args);

	}

	function register_taxonomy_styles($taxonomies) {
		$taxonomies[] = 'section';
		return $taxonomies;
	}

	function register_header_image($locations) {
		if(!is_array($locations['taxonomy']))
			$locations['taxonomy'] = array();

		$locations['taxonomy'][] = 'section';

		return $locations;
	}

	function section_post_types() {
		return apply_filters('humus_section_post_types', array('post'));
	}

}

$humus_sections = new Humus_Sections();