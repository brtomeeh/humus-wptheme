<?php

/*
 * Humus
 * Map tile cache
 */

class Humus_Map_Tiles extends Humus_Map {

	function __construct() {

		add_filter('humus_map_tiles', array($this, 'get_cached_map_tile'), 10, 2);
		add_action('after_setup_theme', array($this, 'init_template'), 1);

		//add_action('generate_rewrite_rules', array($this, 'generate_rewrite_rules'));
		//add_filter('query_vars', array($this, 'query_vars'));
		//add_action('template_redirect', array($this, 'template'), 2);

	}

	function get_timeout() {

		return apply_filters('humus_map_tiles_timeout', 86400);

	}

	function get_dir() {

		return apply_filters('humus_map_tiles_dir', WP_CONTENT_DIR . '/humus-map-tiles');

	}

	function get_cached_map_tile($tile, $color) {

		if(!isset($_GET['humus_map_tile']))
			$tile = home_url() . '/?humus_map_tile=1&humus_tile_z={z}&humus_tile_x={x}&humus_tile_y={y}&color=' . $color;

		return $tile;

		//return home_url() . '/map/tiles/{z}/{x}/{y}.' . $this->get_tile_extension($tile);
	}

	function init_template() {

		if(isset($_GET['humus_map_tile'])) {
			if(strpos($_SERVER['HTTP_REFERER'], home_url()) === false) {
				header('HTTP/1.0 404 Not Found');
 				exit;
 			}
			$tile = str_replace($this->get_map_tile_default_color(), $_GET['color'], $this->get_map_tile());
			$this->get_tile($tile, intval($_GET['humus_tile_z']), intval($_GET['humus_tile_x']), intval($_GET['humus_tile_y']));
			exit;
		}

	}

	function get_tile_extension($server) {
		$path_parts = pathinfo($server);
		return $path_parts['extension'];
	}

	function get_tile($server, $z, $x, $y) {

		$timeout = $this->get_timeout();

		$dir = $this->get_dir() . '/' . sanitize_file_name($server);

		$extension = $this->get_tile_extension($server);

		if(!file_exists($dir)) {
			mkdir($dir, 0777, true);
		}

		$file = $dir . '/' . $z . '_' . $x . '_' . $y . '.' . $extension;

		if (!is_file($file) || filemtime($file) < time()-($timeout*30)) {

			$url = str_replace('{s}', 'a', $server);
			$url = str_replace('{z}', $z, $url);
			$url = str_replace('{x}', $x, $url);
			$url = str_replace('{y}', $y, $url);

			$ch = curl_init($url);
			$fp = fopen($file, "w");
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_exec($ch);
			curl_close($ch);
			fflush($fp);
			fclose($fp);

		}

		$exp_gmt = gmdate("D, d M Y H:i:s", time() + $timeout * 60) ." GMT";
		$mod_gmt = gmdate("D, d M Y H:i:s", filemtime($file)) ." GMT";

		header("Expires: " . $exp_gmt);
		header("Last-Modified: " . $mod_gmt);
		header("Cache-Control: public, max-age=" . $timeout * 60);

		// for MSIE 5
		header("Cache-Control: pre-check=" . $timeout * 60, FALSE);  

		header('Content-Type: image/' . $extension);

		readfile($file);

	}

	/*

	function generate_rewrite_rules($wp_rewrite) {
		$rules = array(
			'map/tiles/([0-9]+)/([0-9]+)/([0-9]+).png?$' => 'index.php?humus_map_tile=1&humus_tile_z=' . $wp_rewrite->preg_index(1) . '&humus_tile_x=' . $wp_rewrite->preg_index(2) . '&humus_tile_y=' . $wp_rewrite->preg_index(3)
		);

		$wp_rewrite->rules = $rules + $wp_rewrite->rules;
	}

	function query_vars($vars) {
		$vars[] = 'humus_map_tile';
		$vars[] = 'humus_tile_z';
		$vars[] = 'humus_tile_x';
		$vars[] = 'humus_tile_y';
		return $vars;
	}

	function template() {

		global $wp_query;

		if($wp_query->get('humus_map_tile')) {
			$this->get_tile($this->get_map_tile(), intval($wp_query->get('humus_tile_z')), intval($wp_query->get('humus_tile_x')), intval($wp_query->get('humus_tile_y')));
			exit;
		}

	}

	*/

}

new Humus_Map_Tiles();