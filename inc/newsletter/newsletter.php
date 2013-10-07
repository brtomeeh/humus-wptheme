<?php

/*
 * Humus
 * Newsletter
 */

class Humus_Newsletter {

	function __construct() {

		

	}

	function scripts() {
		wp_enqueue_script('humus-newsletter', get_template_directory_uri() . '/inc/newsletter/newsletter.js', array('jquery'));
		wp_localize_script('humus-newsletter', 'humus_newsletter', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'action' => ''
		));
	}

	function form() {
		$this->scripts();
		?>
		<div class="newsletter-form">
			<form class="newsletter-signup">
				<input type="text" name="email" placeholder="<?php _e('Enter your email address', 'humus'); ?>" />
			</form>
		</div>
		<?php
	}

}

$GLOBALS['humus_newsletter'] = new Humus_Newsletter();

function humus_newsletter_form() {
	return $GLOBALS['humus_newsletter']->form();
}