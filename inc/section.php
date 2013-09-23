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
		$this->home_featured_fields();

		add_filter('humus_header_image_locations', array($this, 'register_header_image'));
		add_filter('humus_styled_taxonomies', array($this, 'register_taxonomy_styles'));

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

		register_taxonomy('section', $this->get_post_types(), $args);

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

	function get_post_types() {
		return apply_filters('humus_section_post_types', array('post'));
	}

	function home_featured_fields() {

		$field_group = array (
			'id' => 'acf_section-featured-options',
			'title' => __('Section featured options', 'humus'),
			'fields' => array (
				array (
					'key' => 'field_section_featured',
					'label' => __('Section featured', 'humus'),
					'name' => 'section_featured',
					'type' => 'true_false',
					'message' => __('Featured on home section slider', 'humus'),
					'default_value' => 0,
				),
				array (
					'key' => 'field_section_featured_image',
					'label' => __('Full screen image', 'humus'),
					'name' => 'section_featured_image',
					'type' => 'image',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_section_featured',
								'operator' => '==',
								'value' => '1',
							),
						),
						'allorany' => 'all',
					),
					'save_format' => 'url',
					'preview_size' => 'thumbnail',
					'library' => 'all',
				),
				array (
					'key' => 'field_section_featured_image_only',
					'label' => __('Image only', 'humus'),
					'name' => 'section_featured_image_only',
					'type' => 'true_false',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_section_featured',
								'operator' => '==',
								'value' => '1',
							),
						),
						'allorany' => 'all',
					),
					'message' => 'Show only featured image (no title)',
					'default_value' => 0,
				),
				array (
					'key' => 'field_section_featured_title',
					'label' => __('Title', 'humus'),
					'name' => 'section_featured_title',
					'type' => 'text',
					'instructions' => __('Title to show on home slider (default is post title)', 'humus'),
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_section_featured',
								'operator' => '==',
								'value' => '1',
							),
							array (
								'field' => 'field_section_featured_image_only',
								'operator' => '!=',
								'value' => '1',
							),
						),
						'allorany' => 'all',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'formatting' => 'html',
					'maxlength' => '',
				),
				array (
					'key' => 'field_section_featured_color',
					'label' => __('Color scheme', 'humus'),
					'name' => 'section_featured_color',
					'type' => 'radio',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_section_featured',
								'operator' => '==',
								'value' => '1',
							),
							array (
								'field' => 'field_section_featured_image_only',
								'operator' => '!=',
								'value' => '1',
							),
						),
						'allorany' => 'all',
					),
					'choices' => array (
						'dark' => 'Dark',
						'light' => 'Light',
					),
					'other_choice' => 0,
					'save_other_choice' => 0,
					'default_value' => '',
					'layout' => 'vertical',
				),
			),
			'options' => array (
				'position' => 'side',
				'layout' => 'default',
				'hide_on_screen' => array (
				),
			),
			'menu_order' => 0,
		);

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

			$field_group = apply_filters('humus_section_home_featured_field_group', $field_group);
			
			register_field_group($field_group);
	
		}

	}

}

$humus_sections = new Humus_Sections();