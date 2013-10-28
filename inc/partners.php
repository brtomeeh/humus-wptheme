<?php

/*
 * Humus
 * Partners
 */

class Humus_Partners {

	function __construct() {
		add_action('init', array($this, 'init'));
	}

	function init() {
		$this->register_post_type();
		$this->register_field_group();
		add_filter('the_content', array($this, 'the_content'));
	}


	function register_post_type() {

		$labels = array( 
			'name' => __('Partners', 'humus'),
			'singular_name' => __('Partner', 'humus'),
			'add_new' => __('Add partner', 'humus'),
			'add_new_item' => __('Add new partner', 'humus'),
			'edit_item' => __('Edit partner', 'humus'),
			'new_item' => __('New partner', 'humus'),
			'view_item' => __('View partner', 'humus'),
			'search_items' => __('Search partner', 'humus'),
			'not_found' => __('No partner found', 'humus'),
			'not_found_in_trash' => __('No partner found in the trash', 'humus'),
			'menu_name' => __('Partners', 'humus')
		);

		$args = array( 
			'labels' => $labels,
			'hierarchical' => false,
			'description' => __('Humus partners', 'humus'),
			'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'comments'),
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'has_archive' => true,
			'menu_position' => 4,
			'rewrite' => array('slug' => 'parceiros', 'with_front' => false)
		);

		register_post_type('partner', $args);

	}

	function register_field_group() {

		$config = array(
			'id' => 'acf_partner_url',
			'title' => __('Partner website', 'humus'),
			'fields' => array(
				array(
					'key' => 'field_partner_url',
					'label' => __('Partner website', 'humus'),
					'name' => 'partner_url',
					'type' => 'text',
					'required' => 1,
					'instructions' => __('Enter the partner\'s website url', 'humus'),
					'default_value' => '',
					'placeholder' => 'http://www.partner.com/',
					'prepend' => '',
					'append' => '',
					'formatting' => 'html',
					'maxlength' => '',
				),
				array(
					'key' => 'field_partner_logo',
					'label' => __('Logo', 'humus'),
					'name' => 'partner_logo',
					'type' => 'image',
					'required' => 1,
					'instructions' => __('Upload the partner\'s logo in white, with transparent background', 'humus'),
					'column_width' => '',
					'save_format' => 'object',
					'preview_size' => 'thumbnail',
					'library' => 'all',
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
						'value' => 'partner',
						'order_no' => 0,
						'group_no' => 0,
					)
				),
			),
			'menu_order' => 0,
		);

		register_field_group($config);

	}

	function the_content($content) {
		global $post;
		if(get_post_type($post->ID) === 'partner' && get_field('partner_url', $post->ID)) {
			$content .= '<p><a class="arrow-link" href="' . get_field('partner_url', $post->ID) . '" target="_blank" title="' . get_the_title() . '">' . __('Visit official website', 'humus') . '</a></p>';
			$content .= do_shortcode('[gallery]');
		}
		return $content;
	}

}

$humus_partners = new Humus_Partners();