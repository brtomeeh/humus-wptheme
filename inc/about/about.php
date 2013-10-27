<?php

/*
 * Humus
 * About page
 */

class Humus_About_Page {

	function __construct() {

		require_once(TEMPLATEPATH . '/inc/acf/add-ons/acf-repeater/acf-repeater.php');
		add_action('init', array($this, 'init'));

	}

	function init() {

		//add_filter('query_vars', array($this, 'query_vars'));
		//add_action('generate_rewrite_rules', array($this, 'generate_rewrite_rules'));
		//add_action('template_redirect', array($this, 'template_redirect'));
		add_filter('humus_theme_options_fields', array($this, 'theme_options_fields'));
		add_shortcode('humus-about', array($this, 'content'	));

	}

	function query_vars($vars) {
		$vars[] = 'humus_about';
		return $vars;
	}

	function generate_rewrite_rules($wp_rewrite) {
		$rules = array(
			'about$' => 'index.php?humus_about=1'
		);
		$wp_rewrite->rules = $rules + $wp_rewrite->rules;
	}

	function template_redirect() {
		global $wp_query;
		if($wp_query->get('humus_about')) {

			exit;
		}
	}

	function content() {

		wp_enqueue_script('humus-about', get_template_directory_uri() . '/inc/about/about.js', array('jquery'));

		$content = get_field('about_content', 'option');
		$team = get_field('team', 'option');

		ob_start();
		?>
		<div class="humus-about">
			<?php
			if($team) :
				?>
				<div class="humus-about-filter row">
					<div class="three columns alpha">
						<h3 class="filter-title"><?php _e('Showing', 'humus'); ?></h3>
					</div>
					<div class="three columns">
						<a href="#" class="about-filter" data-area="content"><?php _e('Our ideas', 'humus'); ?></a>
					</div>
					<div class="three columns">
						<a href="#" class="about-filter" data-area="team"><?php _e('Our heads', 'humus'); ?></a>
					</div>
				</div>
				<?php
			endif;
			?>
			<div class="humus-about-content about-area row" data-area="content">
				<?php echo $content; ?>
			</div>
			<?php if($team) : ?>
				<div class="humus-about-heads about-area" data-area="team">
					<?php $i = 0; foreach($team as $member) : $i++; ?>
						<div class="member-item three columns <?php if($i%4 == 1) echo 'alpha'; if(($i+1)%4 == 1) echo 'omega'; ?>">
							<div class="row">
								<div class="member-thumbnail">
									<img class="scale-with-grid" src="<?php echo $member['member_photo']['sizes']['humus-thumbnail']; ?>" alt="<?php echo $member['member_name']; ?>" />
								</div>
								<h2><a href="#"><?php echo $member['member_name']; ?></a></h2>
								<p class="role"><?php echo $member['member_role']; ?></p>
							</div>
							<div class="member-profile"><?php echo $member['member_profile']; ?></div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<div class="member-content-container">
				<div class="container">
					<div class="twelve columns">
						<a href="#" class="close-member">x</a>
						<div class="member-content">
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		$output = ob_get_clean();
		return $output;

	}

	function theme_options_fields($fields) {

		$about_fields = array(
			array(
				'key' => 'field_about_page_tab',
				'label' => __('About page', 'humus'),
				'name' => '',
				'type' => 'tab',
			),
			array(
				'key' => 'field_about_content',
				'label' => __('About content', 'humus'),
				'name' => 'about_content',
				'type' => 'wysiwyg',
				'default_value' => '',
				'toolbar' => 'full',
				'media_upload' => 'yes',
			),
			array(
				'key' => 'field_team',
				'label' => __('Team', 'humus'),
				'name' => 'team',
				'type' => 'repeater',
				'sub_fields' => array(
					array(
						'key' => 'field_member_name',
						'label' => __('Member name', 'humus'),
						'name' => 'member_name',
						'type' => 'text',
						'column_width' => '',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
					array(
						'key' => 'field_member_photo',
						'label' => __('Photo', 'humus'),
						'name' => 'member_photo',
						'type' => 'image',
						'column_width' => '',
						'save_format' => 'object',
						'preview_size' => 'thumbnail',
						'library' => 'all',
					),
					array(
						'key' => 'field_member_role',
						'label' => __('Role', 'humus'),
						'name' => 'member_role',
						'type' => 'text',
						'column_width' => '',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
					array(
						'key' => 'field_member_profile',
						'label' => __('Profile', 'humus'),
						'name' => 'member_profile',
						'type' => 'wysiwyg',
						'column_width' => 20,
						'default_value' => '',
						'toolbar' => 'basic',
						'media_upload' => 'no',
					),
				),
				'row_min' => 0,
				'row_limit' => '',
				'layout' => 'row',
				'button_label' => __('Add member', 'humus'),
			)
		);

		$fields = array_merge($fields, $about_fields);

		return $fields;

	}

}

new Humus_About_Page();