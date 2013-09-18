<?php

/*
 * Humus
 * Theme options
 */

class Humus_Theme_Options {

	function __construct() {

		require_once(TEMPLATEPATH . '/inc/acf/add-ons/acf-options-page/acf-options-page.php');
		add_action('init', array($this, 'init'), 100);

	}

	function init() {

		if(function_exists('register_field_group')) {
			$this->register_field_group();
		}

	}

	function register_field_group() {

		register_field_group(array(
			'id' => 'acf_options',
			'title' => __('Options', 'humus'),
			'fields' => array(
				array(
					'key' => 'field_style_tab',
					'label' => __('Styles', 'humus'),
					'name' => '',
					'type' => 'tab',
				),
				array(
					'key' => 'field_logo_image',
					'label' => __('Logo image', 'humus'),
					'name' => 'site_logo_image',
					'type' => 'image',
					'save_format' => 'object',
					'preview_size' => 'thumbnail',
					'library' => 'all',
				),
				array(
					'key' => 'field_contact_tab',
					'label' => __('Contact info', 'humus'),
					'name' => '',
					'type' => 'tab',
				),
				array(
					'key' => 'field_email',
					'label' => __('Email', 'humus'),
					'name' => 'email',
					'type' => 'email',
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'field_phone',
					'label' => __('Phone number', 'humus'),
					'name' => 'phone_number',
					'type' => 'text',
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'formatting' => 'html',
					'maxlength' => '',
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'options_page',
						'operator' => '==',
						'value' => 'acf-options',
						'order_no' => 0,
						'group_no' => 0,
					),
				),
			),
			'options' => array(
				'position' => 'normal',
				'layout' => 'no_box',
				'hide_on_screen' => array(
				),
			),
			'menu_order' => 0,
		));

	}

}

new Humus_Theme_Options();