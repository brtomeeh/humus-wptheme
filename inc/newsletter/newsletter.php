<?php

/*
 * Humus
 * Newsletter
 */

class Humus_Newsletter {

    var $post_url = 'http://espacohumus.us7.list-manage1.com/subscribe/post?u=1c9ce8f8fec09ec47be15945c&amp;id=442f4139fc';

	function __construct() {

        add_action('wp_ajax_nopriv_humus_newsletter_registration', array($this, 'register'));
        add_action('wp_ajax_humus_newsletter_registration', array($this, 'register'));

	}

	function scripts() {
		wp_enqueue_script('humus-newsletter', get_template_directory_uri() . '/inc/newsletter/newsletter.js', array('jquery'));
        wp_localize_script('humus-newsletter', 'humus_newsletter', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'success' => __('Great! You\'ve received an email to confirm your subscription', 'humus'),
            'error' => __('Something went wrong. Make sure you typed your email right.', 'humus')
        ));
	}

	function form() {
		?>
		<div class="newsletter-form">
			<form class="newsletter-signup" action="<?php echo $this->post_url; ?>" method="POST" target="_blank">
                <input type="hidden" name="action" value="humus_newsletter_registration" />
				<input type="text" name="EMAIL" placeholder="<?php _e('Enter your email address', 'humus'); ?>" />
				<input type="submit" value="<?php _e('Send', 'humus'); ?>" />
			</form>
		</div>
		<?php
	}

    function register() {

        $result = '';

        if(isset($_REQUEST['EMAIL'])) {

            $post_string = 'EMAIL=' . $_REQUEST['EMAIL'];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $this->post_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);

            $result = curl_exec($ch);

            curl_close($ch);


        }

		echo $result;
		exit;

    }

}

$GLOBALS['humus_newsletter'] = new Humus_Newsletter();

function humus_newsletter_form() {
	return $GLOBALS['humus_newsletter']->form();
}
