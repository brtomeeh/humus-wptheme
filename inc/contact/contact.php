<?php

/* 
 * Humus
 */

class Humus_Contact {

	function __construct() {

		add_shortcode('humus-contact', array($this, 'get_content'));
		$this->ajax();

	}

	function get_email() {

		return get_field('email', 'option');

	}

	function get_phone() {

		return get_field('phone_number', 'option');

	}

	function get_content() {
		ob_start();
		$this->content();
		return ob_get_clean();
	}

	function content() {
		$email = $this->get_email();
		$phone = $this->get_phone();
		?>
		<div class="humus-contact row">
			<div class="three columns alpha">
				<div class="contacts">
					<?php if($email) : ?>
						<div class="email">
							<p><?php echo $email; ?></p>
						</div>
					<?php endif; ?>
					<?php if($phone) : ?>
						<div class="phone">
							<p><?php echo $phone; ?></p>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="six columns offset-by-one omega">
				<?php $this->form(); ?>
			</div>
		</div>
		<?php
	}

	function form() {
		if(!$this->get_email())
			return false;

		wp_enqueue_script('humus-contact', get_template_directory_uri() . '/inc/contact/contact.js', array('jquery'));
		wp_localize_script('humus-contact', 'humus_contact', array(
			'ajaxurl' => admin_url('admin-ajax.php')
		));
		?>
		<form class="humus-contact-form">
			<div class="inputs">
				<input type="hidden" name="_nonce" value="<?php echo wp_create_nonce('humus-contact'); ?>" />
				<input type="hidden" name="action" value="humus_contact" />
				<input type="text" class="name" name="name" placeholder="<?php _e('Name', 'humus'); ?>" />
				<input type="text" class="email" name="email" placeholder="<?php _e('Email', 'humus'); ?>" />
				<input type="text" class="subject" name="subject" placeholder="<?php _e('Subject', 'humus'); ?>" />
				<textarea class="msg" name="message" placeholder="<?php _e('Your message here', 'humus'); ?>"></textarea>
				<input type="submit" class="button" value="<?php _e('Send', 'humus'); ?>" />
			</div>
			<div class="message">
			</div>
		</form>
		<?php
	}

	function ajax() {

		add_action('wp_ajax_nopriv_humus_contact', array($this, 'send'));
		add_action('wp_ajax_humus_contact', array($this, 'send'));

	}

	function send() {

		$email = $this->get_email();

		if(!$email)
			$this->ajax_response(__('Our system is not accepting email forms for now.', 'humus'));

		if(!wp_verify_nonce($_REQUEST['_nonce'], 'humus-contact'))
			$this->ajax_response(__('Security check.', 'humus'));

		if(!$_REQUEST['email'] || !$_REQUEST['name'] || !$_REQUEST['message'])
			$this->ajax_response(__('Please fill out all the form fields.', 'humus'));

		if(!filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL))
			$this->ajax_response(__('Invalid email.', 'humus'));

		$headers = 'From: ' . $_REQUEST['name'] . ' <' . $_REQUEST['email'] . '>' . "\r\n";
		$subject = '[' . get_bloginfo('name') . ' contact] ' . $_REQUEST['subject'];

		wp_mail($email, $subject, $_REQUEST['message'], $headers);

		$this->ajax_response(__('Thank you! Your message has been sent, we\'ll get back to you as soon as possible.', 'humus'), 'success');

	}

	function ajax_response($message = 'Error', $status = 'error') {

		$response = array(
			'status' => $status,
			'message' => $message
		);

		header('Content-Type: application/json;charset=UTF-8');
		echo json_encode($response);
		exit;
	}

}

new Humus_Contact();