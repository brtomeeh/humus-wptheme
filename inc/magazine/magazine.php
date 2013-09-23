<?php

/*
 * Humus
 * Magazine
 */

class Humus_Magazine {

	var $remix;
	
	function __construct() {

		if(!function_exists('register_field_group'))
			return false;

		require_once(TEMPLATEPATH . '/inc/magazine/remix.php');

		$this->setup_remix();

	}

	function setup_remix() {

		$this->remix = new Humus_Magazine_Remix();

	}

}

$humus_magazine = new Humus_Magazine();