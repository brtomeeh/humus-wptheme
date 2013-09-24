<?php

/* Humus
 * Media features
 */

class Humus_Media {

	function __construct() {

		add_action('init', array($this, 'init'), 100);
		add_action('acf/save_post', array($this, 'save_oembed'), 1);


	}

	function init() {

		if(function_exists('register_field_group')){
			$this->register_field();
		}

	}

	function get_post_types() {

		return apply_filters('humus_media_post_types', array('post'));

	}

	function register_field() {

		$field_group = array(
			'id' => 'acf_media',
			'title' => __('Media', 'humus'),
			'fields' => array(
				array(
					'key' => 'field_media',
					'label' => __('Media URL', 'humus'),
					'name' => 'media_url',
					'type' => 'text',
					'instructions' => sprintf(__('Paste a media url from any of the sources <a href="%s" target="_blank">listed here</a>', 'humus'), 'http://codex.wordpress.org/Embeds#Can_I_Use_Any_URL_With_This.3F'),
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
			'menu_order' => 0,
		);

		$preview = get_post_meta($_GET['post'], 'media_oembed', true);

		if($preview) {
			$field_group['fields'][] = array(
				'key' => 'field_media_preview',
				'label' => __('Preview', 'humus'),
				'name' => '',
				'type' => 'message',
				'message' => '<h4>'. __('Preview', 'humus') . '</h4>' . $preview
			);
		}

		$locations = array();

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

			$field_group = apply_filters('humus_media_field_group', $field_group);
			
			register_field_group($field_group);
	
		}

	}

	function save_oembed($post_id) {
	
		if(wp_is_post_revision($post_id))
			return;

		$oembed = get_post_meta($post_id, 'media_oembed', true);

		if(isset($_POST['fields']) && isset($_POST['fields']['field_media'])) {

			if(get_field('media_url', $post_id) !== $_POST['fields']['field_media'] || !$oembed) {

				$embed = wp_oembed_get($_POST['fields']['field_media']);

				if($embed) {

					update_post_meta($post_id, 'media_oembed', $embed);

				}

			}

		} elseif($oembed) {

			delete_post_meta($post_id, 'media_oembed');

		}
	}

}

$humus_media = new Humus_Media();