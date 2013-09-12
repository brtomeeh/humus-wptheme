<?php

/**
 * Taxonomy custom fields for styles
 * Fields:
 * - Icon
 * - Menu icon
 * - Color
 *
 * Requires ACF
 */

class Humus_Taxonomy_Styles {

	function __construct() {

		if(function_exists('register_field_group'))
			$this->init();

	}

	function get_taxonomies() {
		return apply_filters('humus_styled_taxonomies', array());
	}

	function init() {

		$field_group = array(
			'id' => 'acf_taxonomy_styles',
			'title' => __('Taxonomy Styles', 'humus'),
			'fields' => array(
				array(
					'key' => 'field_tax_icon',
					'label' => __('Icon', 'humus'),
					'name' => 'term_icon',
					'type' => 'image',
					'instructions' => __('60x60 image to represent the section on the section\'s page and posts association', 'humus'),
					'save_format' => 'url',
					'preview_size' => 'thumbnail',
					'library' => 'all',
				),
				array(
					'key' => 'field_tax_menu_icon',
					'label' => __('Menu icon', 'humus'),
					'name' => 'term_menu_icon',
					'type' => 'image',
					'instructions' => __('21x21 image to represent the section on the navigation menu', 'humus'),
					'save_format' => 'url',
					'preview_size' => 'full',
					'library' => 'all',
				),
				array(
					'key' => 'field_tax_color',
					'label' => __('Color', 'humus'),
					'name' => 'term_color',
					'type' => 'color_picker',
					'default_value' => '',
				),
			),
			'options' => array(
				'position' => 'normal',
				'layout' => 'no_box',
				'hide_on_screen' => array(),
			),
			'menu_order' => 0,
		);

		$locations = array();

		$taxonomies = $this->get_taxonomies();

		if(is_array($taxonomies) && !empty($taxonomies)) {

			foreach($taxonomies as $taxonomy) {

				$locations[] = array(
					array(
						'param' => 'ef_taxonomy',
						'operator' => '==',
						'value' => $taxonomy,
						'order_no' => 0,
						'group_no' => 0,
					)
				);

			}

		}

		if(!empty($locations)) {

			$field_group['location'] = $locations;
			register_field_group($field_group);

		}

	}

}

new Humus_Taxonomy_Styles();

function humus_get_term_icon_url($id = false, $tax = false) {

	if($id)
		$field = get_field('section_icon', $id);

	if($field)
		return $field;

	if(is_tax()) {
		$term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
		$id = get_query_var('taxonomy') . '_' . $term->term_id;
	} elseif(is_category()) {
		$term = get_category_by_slug(get_query_var('category_name'));
		$id = 'category_' . $term->term_id;
	} elseif(is_single() || get_post($id)) {
		global $post;
		if($tax) {
			$terms = get_the_terms($id, $tax);
			if($terms) {
				$term = array_shift($terms);
				$id = $tax . '_' . $term->term_id;
			}
		} else {
			$id = $post->ID;
		}
	}

	if($id)
		return get_field('section_icon', $id);

	return false;
}