<?php

/*
 * Humus
 * Ads
 */

class Humus_Ads {

	function __construct() {

		if(function_exists('drawAdsPlace')) {
			add_action('humus_after_archive_posts', array($this, 'ad'));
			add_action('wp_ajax_nopriv_humus_ads', array($this, 'ad'));
			add_action('wp_ajax_humus_ads', array($this, 'ad'));
		}

	}

	function ad($options = false) {
		$ad = false;

		if($options) {

			$settings = $options;

		} else {

			if(is_tax() || is_category() || is_tag()) {

				$obj = get_queried_object();
				$settings = array('name' => $obj->name);

			}

		}

		$ad = $this->get_ad($settings);

		if($ad) {
			if($obj->taxonomy)
				$taxonomy = get_taxonomy($obj->taxonomy);
			?>
			<div class="humus-ad">
				<div class="container row">
					<div class="twelve columns">
						<?php if($obj->taxonomy) : ?>
							<div class="ad-title">
								<h4><?php printf(__('%1$s %2$s is sponsored by %3$s', 'humus'), $taxonomy->labels->singular_name, $obj->name, $this->get_ad_name($this->get_current_ad_id())); ?></h4>
							</div>
						<?php endif; ?>
						<div class="ad-content">
							<?php echo $ad; ?>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	}

	function get_ad($options) {

		global $samObject;
		return $samObject->buildAd($options);

	}

	function get_ad_name($ad_id) {

		$ad = $this->get_ad_row($ad_id);

		if($ad)
			return $ad->name;

		return false;

	}

	function get_current_ad_id() {
		return $GLOBALS['sam_ad_id'];
	}

	function get_ad_row($ad_id) {

		if(!$ad_id)
			return false;

		global $wpdb;
		return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}sam_ads WHERE id = {$ad_id}");

	}

}

$GLOBALS['humus_ads'] = new Humus_Ads();

function humus_get_ad($options) {
	return $GLOBALS['humus_ads']->get_ad($options);
}

function humus_ad($options) {
	return $GLOBALS['humus_ads']->ad($options);
}