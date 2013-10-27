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

		wp_enqueue_script('humus-about', get_template_directory_uri() . '/inc/about/about.js', array('jquery'), '0.1.0');

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
				<div class="three columns alpha">&nbsp;</div>
				<div class="nine columns omega">
					<?php echo $content; ?>
				</div>
			</div>
			<?php if($team) : ?>
				<div class="humus-about-heads about-area" data-area="team">
					<?php $i = 0; foreach($team as $member) : $i++; ?>
						<div class="member-item three columns <?php if($i%4 == 1) echo 'alpha'; if(($i+1)%4 == 1) echo 'omega'; ?>">
								<div class="member-thumbnail">
									<img class="scale-with-grid" src="<?php echo $member['member_photo']['sizes']['humus-big-thumbnail']; ?>" alt="<?php echo $member['member_name']; ?>" />
								</div>
								<div class="member-profile-content">
									<h2><a href="#"><?php echo $member['member_name']; ?></a></h2>
									<p class="role"><?php echo $member['member_role']; ?></p>
									<div class="member-profile clearfix">
										<?php echo $member['member_profile']; ?>
										<div class="social-icons">
											<?php
											$facebook = $member['facebook_url'];
											if($facebook)
												echo '<a href="' . $facebook . '" title="Facebook" target="_blank">facebook</a>';
											$twitter = $member['twitter_url'];
											if($twitter)
												echo '<a href="' . $twitter . '" title="Twitter" target="_blank">twitter</a>';
											$youtube = $member['youtube_url'];
											if($youtube)
												echo '<a href="' . $youtube . '" title="YouTube" target="_blank">youtube</a>';
											$instagram = $member['instagram_url'];
											if($instagram)
												echo '<a href="' . $instagram . '" title="Instagram" target="_blank">instagram</a>';
											$gplus = $member['gplus_url'];
											if($gplus)
												echo '<a href="' . $gplus . '" title="Google Plus" target="_blank">google</a>';
											$pinterest = $member['pinterest_url'];
											if($pinterest)
												echo '<a href="' . $pinterest . '" title="Pinterest" target="_blank">pinterest</a>';
											?>
										</div>
									</div>
								</div>
							</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<div class="member-content-container">
				<div class="container">
					<div class="twelve columns">
						<a href="#" class="close-member">x</a>
						<div class="navigation">
							<a class="previous" href="#" title="<?php _e('Previous', 'humus'); ?>"><?php _e('Previous', 'humus'); ?></a>
							<a class="next" href="#" title="<?php _e('Next', 'humus'); ?>"><?php _e('Next', 'humus'); ?></a>
						</div>
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