<?php

/* Humus
 * Header image
 */

class Humus_Header_Image {

	function __construct() {

		add_action('init', array($this, 'init'), 100);

	}

	function init() {

		if(function_exists('register_field_group'))
			$this->register_field_group();

	}

	function get_locations() {

		return apply_filters('humus_header_image_locations', array('post_type' => array('post', 'page'), 'taxonomy' => array('category', 'post_tag')));

	}

	function register_field_group() {

		$field_group = array(
			'id' => 'acf_header-image',
			'title' => 'Header image',
			'fields' => array(
				array(
					'key' => 'field_header_image',
					'label' => __('Header image file', 'humus'),
					'name' => 'header_image',
					'type' => 'image',
					'instructions' => __('Upload a large image for header placement.', 'humus'),
					'save_format' => 'url',
					'preview_size' => 'full',
					'library' => 'all',
				),
			),
			'location' => array(),
			'options' => array(
				'position' => 'side',
				'layout' => 'no_box',
				'hide_on_screen' => array(
				),
			),
			'menu_order' => 0,
		);

		$locations = $this->get_locations();

		if(is_array($locations) && !empty($locations)) {

			$i = 0;

			$formatted_locations = array();

			foreach($locations as $type => $type_locations) {

				if(is_array($type_locations) && !empty($type_locations)) {

					if($type == 'taxonomy')
						$type = 'ef_taxonomy';

					foreach($type_locations as $type_location) {

						$formatted_locations[] = array(
							array(
								'param' => $type,
								'operator' => '==',
								'value' => $type_location,
								'order_no' => 0,
								'group_no' => $i
							)
						);

						$i++;

					}

				}

			}

			$locations = $formatted_locations;

		}

		if(!empty($locations)) {

			$field_group['location'] = $locations;

			$field_group = apply_filters('humus_header_image_field_group', $field_group);
			
			register_field_group($field_group);
	
		}

	}

}

$humus_header_image = new Humus_Header_Image();

function humus_get_header_image_url() {

	$id = false;

	if(is_tax()) {
		$term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
		$id = get_query_var('taxonomy') . '_' . $term->term_id;
	} elseif(is_category()) {
		$term = get_category_by_slug(get_query_var('category_name'));
		$id = 'category_' . $term->term_id;
	} elseif(is_single() || is_page()) {
		global $post;
		$id = $post->ID;
	}

	if($id)
		return get_field('header_image', $id);

	return false;
}