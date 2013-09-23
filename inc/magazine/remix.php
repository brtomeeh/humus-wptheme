<?php

/*
 * Humus
 * Magazine Remix
 */

class Humus_Magazine_Remix extends Humus_Magazine {

	function __construct() {

		add_action('init', array($this, 'init'));

	}

	function init() {

		$this->register_post_type();
		$this->register_field_group();

		add_filter('post_type_link', array($this, 'post_link'), 10, 2);

	}

	function register_post_type() {

		$labels = array( 
			'name' => __('Remix', 'humus'),
			'singular_name' => __('Remix', 'humus'),
			'add_new' => __('Add remix', 'humus'),
			'add_new_item' => __('Add new remix', 'humus'),
			'edit_item' => __('Edit remix', 'humus'),
			'new_item' => __('New remix', 'humus'),
			'view_item' => __('View remix', 'humus'),
			'search_items' => __('Search Remix', 'humus'),
			'not_found' => __('No remix found', 'humus'),
			'not_found_in_trash' => __('No remix found in the trash', 'humus'),
			'menu_name' => __('Remix', 'humus')
		);

		$args = array( 
			'labels' => $labels,
			'hierarchical' => false,
			'description' => __('Humus Remix', 'humus'),
			'supports' => array('title', 'editor', 'author', 'excerpt', 'thumbnail'),
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'has_archive' => false,
			'menu_position' => 4,
			'taxonomies' => array('category', 'post_tag'),
			'rewrite' => array('slug' => 'remix', 'with_front' => false)
		);

		register_post_type('remix', $args);

	}

	function register_field_group() {

		$config = array(
			'id' => 'acf_remix_url',
			'title' => __('Remix URL', 'humus'),
			'fields' => array(
				array(
					'key' => 'field_remix_url',
					'label' => __('Remix URL', 'humus'),
					'name' => 'remix_url',
					'type' => 'text',
					'required' => 1,
					'instructions' => __('Paste the original remix URL', 'humus'),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'formatting' => 'html',
					'maxlength' => '',
				),
			),
			'options' => array(
				'position' => 'normal',
				'layout' => 'default',
				'hide_on_screen' => array(),
			),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'remix',
						'order_no' => 0,
						'group_no' => 0,
					)
				),
			),
			'menu_order' => 0,
		);

		register_field_group($config);

	}

	function post_link($permalink, $post) {
		if(get_post_type($post->ID) == 'remix') {
			$permalink = get_field('remix_url', $post->ID);
		}
		return $permalink;
	}

}