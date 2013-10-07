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

		register_field_group($this->get_settings());

	}

	function get_settings() {
		$settings = array(
			'id' => 'acf_options',
			'title' => __('Options', 'humus'),
			'fields' => $this->get_fields(),
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
		);

		return apply_filters('humus_theme_options_settings', $settings);
	}

	function get_fields() {
		$fields = array(
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
			array(
				'key' => 'field_footer_contact',
				'label' => __('Footer contact content', 'humus'),
				'name' => 'footer_contact',
				'type' => 'textarea',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array(
				'key' => 'field_social_tab',
				'label' => __('Social', 'humus'),
				'name' => '',
				'type' => 'tab'
			),
			array(
				'key' => 'field_fb',
				'label' => 'Facebook',
				'name' => 'facebook_url',
				'type' => 'text',
				'placeholder' => 'http://facebook.com/...',
				'formatting' => 'html',
			),
			array(
				'key' => 'field_tw',
				'label' => 'Twitter',
				'name' => 'twitter_url',
				'type' => 'text',
				'placeholder' => 'http://twitter.com/...',
				'formatting' => 'html',
			),
			array(
				'key' => 'field_instagram',
				'label' => 'Instagram',
				'name' => 'instagram_url',
				'type' => 'text',
				'placeholder' => 'http://instagram.com/...',
				'formatting' => 'html',
			),
			array(
				'key' => 'field_gplus',
				'label' => 'Google Plus',
				'name' => 'gplus_url',
				'type' => 'text',
				'placeholder' => 'http://plus.google.com/...',
				'formatting' => 'html',
			),
			array(
				'key' => 'field_yt',
				'label' => 'YouTube',
				'name' => 'youtube_url',
				'type' => 'text',
				'placeholder' => 'http://youtube.com/...',
				'formatting' => 'html',
			),
			array(
				'key' => 'field_pinterest',
				'label' => 'Pinterest',
				'name' => 'pinterest_url',
				'type' => 'text',
				'placeholder' => 'http://pinterest.com/...',
				'formatting' => 'html',
			)
		);

		return apply_filters('humus_theme_options_fields', $fields);
	}

}

new Humus_Theme_Options();