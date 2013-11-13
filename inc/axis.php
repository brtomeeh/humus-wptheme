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
		
		$this->register_taxonomy();
		$this->home_featured_fields();
		$this->custom_sort_field();

		add_filter('humus_header_image_locations', array($this, 'register_header_image'));

		add_filter('get_terms', array($this, 'custom_sort'), 10, 3);

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
			'rewrite' => array('slug' => 'eixo', 'with_front' => false)
		);

		register_taxonomy('axis', $this->get_post_types(), $args);

	}

	function register_header_image($locations) {
		if(!is_array($locations['taxonomy']))
			$locations['taxonomy'] = array();

		$locations['taxonomy'][] = 'axis';

		return $locations;
	}

	function get_post_types() {
		return apply_filters('humus_axis_post_types', array('post'));
	}

	function home_featured_fields() {

		$field_group = array (
			'id' => 'acf_home-featured-options',
			'title' => __('Home featured options', 'humus'),
			'fields' => array (
				array (
					'key' => 'field_home_featured',
					'label' => __('Home featured', 'humus'),
					'name' => 'home_featured',
					'type' => 'true_false',
					'message' => __('Featured on home axes slider', 'humus'),
					'default_value' => 0,
				),
				array (
					'key' => 'field_home_featured_axis',
					'label' => 'Axis',
					'name' => 'home_featured_axis',
					'type' => 'taxonomy',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_home_featured',
								'operator' => '==',
								'value' => '1',
							),
						),
						'allorany' => 'all',
					),
					'taxonomy' => 'axis',
					'field_type' => 'radio',
					'allow_null' => 0,
					'load_save_terms' => 0,
					'return_format' => 'id',
					'multiple' => 0,
				),
				array (
					'key' => 'field_home_featured_title',
					'label' => __('Title', 'humus'),
					'name' => 'home_featured_title',
					'type' => 'text',
					'instructions' => __('Title to show on home slider (default is post title)', 'humus'),
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_home_featured',
								'operator' => '==',
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
					'key' => 'field_home_featured_description',
					'label' => __('Description', 'humus'),
					'name' => 'home_featured_description',
					'type' => 'textarea',
					'instructions' => __('Description to show on home slider (default is post excerpt)', 'humus'),
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_home_featured',
								'operator' => '==',
								'value' => '1',
							),
						),
						'allorany' => 'all',
					),
					'default_value' => '',
					'placeholder' => '',
					'maxlength' => '',
					'formatting' => 'br',
				),
				array (
					'key' => 'field_home_featured_image',
					'label' => __('Full screen image', 'humus'),
					'name' => 'home_featured_image',
					'type' => 'image',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_home_featured',
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

			$field_group = apply_filters('humus_axes_home_featured_field_group', $field_group);
			
			register_field_group($field_group);
	
		}
	}

	function custom_sort_field() {

		$field_group = array(
			'id' => 'acf_axis_custom_sort',
			'title' => __('Custom sort', 'humus'),
			'fields' => array(
				array(
					'key' => 'field_axis_order',
					'label' => __('Order', 'humus'),
					'name' => 'axis_order',
					'type' => 'text',
					'instructions' => __('Select a number for order of appearance.', 'humus')
				)
			),
			'options' => array(
				'position' => 'normal',
				'layout' => 'no_box',
			),
			'menu_order' => 0,
			'location' => array(
				array(
					array(
						'param' => 'ef_taxonomy',
						'operator' => '==',
						'value' => 'axis',
						'order_no' => 0,
						'group_no' => 0
					)
				)
			)
		);

		register_field_group($field_group);

	}

	function custom_sort($terms, $taxonomies, $args) {

		if(!in_array('axis', $taxonomies))
			return $terms;

		// Controls behavior when get_terms is called at unusual times resulting in a terms array without objects
		$empty = false;

		// Create collector arrays
		$ordered_terms = array();
		$unordered_terms = array();

		// Add taxonomy order to terms
		foreach($terms as $term) {
			// Only set tax_order if value is an object
			if(is_object($term)) {
				if($taxonomy_sort = get_field('axis_order', 'axis_' . $term->term_id)) {
					$term->tax_order = (int) $taxonomy_sort;
					$ordered_terms[] = $term;
				} else {
					$term->tax_order = (int) 0;
					$unordered_terms[] = $term;
				}
			} else $empty = true;
		}

		// Only sort by tax_order if there are items to sort, otherwise return the original array
		if(!$empty && count($ordered_terms) > 0)
			$this->quickSort($ordered_terms);
		else
			return $terms;

		// Combine the newly ordered items with the unordered items and return
		return array_merge($ordered_terms, $unordered_terms);  
	}

	function quickSort(&$array) {
		$cur = 1;
		$stack[1]['l'] = 0;
		$stack[1]['r'] = count($array)-1;
		
		do {
			$l = $stack[$cur]['l'];
			$r = $stack[$cur]['r'];
			$cur--;
		
			do {
				$i = $l;
				$j = $r;
				$tmp = $array[(int)( ($l+$r)/2 )];
			
				// partion the array in two parts.
				// left from $tmp are with smaller values,
				// right from $tmp are with bigger ones
				do {
					while( $array[$i]->tax_order < $tmp->tax_order )
					$i++;
				
					while( $tmp->tax_order < $array[$j]->tax_order )
				 	$j--;
				
					// swap elements from the two sides
					if( $i <= $j) {
						 $w = $array[$i];
						 $array[$i] = $array[$j];
						 $array[$j] = $w;
				
				 		$i++;
				 		$j--;
					}
				
				}while( $i <= $j );
				
				if( $i < $r ) {
					$cur++;
					$stack[$cur]['l'] = $i;
					$stack[$cur]['r'] = $r;
				}
				$r = $j;
				
			}while( $l < $r );
				
		}while( $cur != 0 );
	}

}

$humus_axes = new Humus_Axes();
