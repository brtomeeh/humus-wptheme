<?php

/*
 * Humus
 * Events
 */

class Humus_Events {

	function __construct() {

		require_once(TEMPLATEPATH . '/inc/acf/add-ons/acf-field-date-time-picker/acf-date_time_picker.php');
		add_action('init', array($this, 'init'));

	}

	function init() {
		$this->register_location_taxonomy();
		$this->register_post_type();
		$this->register_field_group();

		add_filter('query_vars', array($this, 'query_vars'));
		add_action('pre_get_posts', array($this, 'pre_get_posts'));
		add_filter('posts_clauses', array($this, 'posts_clauses'), 10, 2);

		add_filter('humus_map_taxonomies', array($this, 'register_location_map'));
		add_action('humus_before_archive_posts', array($this, 'archive'));

		add_action('humus_list_article_before_thumbnail', array($this, 'list_article_before_thumbnail'));
		add_filter('humus_list_article_footer', array($this, 'list_article_footer'));
		add_filter('humus_list_article_before_title', array($this, 'list_article_before_title'));

		add_filter('humus_filter_order_options', array($this, 'disable_order_filter'));
	}


	function register_post_type() {

		$labels = array( 
			'name' => __('Events', 'humus'),
			'singular_name' => __('Event', 'humus'),
			'add_new' => __('Add event', 'humus'),
			'add_new_item' => __('Add new event', 'humus'),
			'edit_item' => __('Edit event', 'humus'),
			'new_item' => __('New event', 'humus'),
			'view_item' => __('View event', 'humus'),
			'search_items' => __('Search event', 'humus'),
			'not_found' => __('No event found', 'humus'),
			'not_found_in_trash' => __('No event found in the trash', 'humus'),
			'menu_name' => __('Events', 'humus')
		);

		$args = array( 
			'labels' => $labels,
			'hierarchical' => false,
			'description' => __('Humus events', 'humus'),
			'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'comments'),
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'has_archive' => false,
			'menu_position' => 4,
			'taxonomies' => array('post_tag', 'category'),
			'rewrite' => array('slug' => 'events', 'with_front' => false)
		);

		register_post_type('event', $args);

	}

	function register_location_taxonomy() {

		$labels = array(
			'name' => _x('Locations', 'Location general name', 'humus'),
			'singular_name' => _x('Location', 'Location singular name', 'humus'),
			'all_items' => __('All locations', 'humus'),
			'edit_item' => __('Edit location', 'humus'),
			'view_item' => __('View location', 'humus'),
			'update_item' => __('Update location', 'humus'),
			'add_new_item' => __('Add new location', 'humus'),
			'new_item_name' => __('New location name', 'humus'),
			'parent_item' => __('Parent location', 'humus'),
			'parent_item_colon' => __('Parent location:', 'humus'),
			'search_items' => __('Search locations', 'humus'),
			'popular_items' => __('Popular locations', 'humus'),
			'separate_items_with_commas' => __('Separate locations with commas', 'humus'),
			'add_or_remove_items' => __('Add or remove locations', 'humus'),
			'choose_from_most_used' => __('Choose from most used locations', 'humus'),
			'not_found' => __('No locations found', 'humus')
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_admin_column' => true,
			'hierarchical' => true,
			'query_var' => 'event-location',
			'rewrite' => array('slug' => 'events/locations', 'with_front' => false)
		);

		register_taxonomy('event-location', 'event', $args);

	}

	function register_location_map($taxonomies) {
		$taxonomies[] = 'event-location';
		return $taxonomies;
	}

	function register_field_group() {

		$config = array(
			'id' => 'acf_event_settings',
			'title' => __('Event settings', 'humus'),
			'fields' => array(
				array(
					'key' => 'field_event_time',
					'label' => __('Event time', 'humus'),
					'name' => 'event_time',
					'type' => 'date_time_picker',
					'required' => 1,
					'show_date' => 'true',
					'date_format' => _x('m/d/y', 'Event date', 'humus'),
					'time_format' => _x('h:mm tt', 'Event time', 'humus'),
					'show_week_number' => 'false',
					'picker' => 'slider',
					'save_as_timestamp' => 'true',
					'get_as_timestamp' => 'true',
				),
				array(
					'key' => 'field_event_featured',
					'label' => __('Week featured', 'humus'),
					'name' => 'event_featured',
					'type' => 'true_false',
					'instructions' => __('Check this event to appear on "week featured" area', 'humus'),
					'message' => __('Mark as "week featured"', 'humus'),
					'default_value' => 0
				)

			),
			'options' => array(
				'position' => 'normal',
				'layout' => 'no_box',
				'hide_on_screen' => array(),
			),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'event',
						'order_no' => 0,
						'group_no' => 0,
					)
				),
			),
			'menu_order' => 0,
		);

		register_field_group($config);

	}

	function get_event_date($post_id = false, $format = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;

		$ts = get_field('event_time', $post_id);

		if($ts) {
			if(!$format) { 
				$date = date_i18n(_x('F jS, Y', 'Event date output', 'humus'), $ts);
				$time = date_i18n(_x('g:i a', 'Event time output', 'humus'), $ts);
			} else {
				return date_i18n($format, $ts);
			}
		}

		$output = '<span class="date">' . $date . '</span><span class="time">' . _x('starting', 'Event time output prefix', 'humus') . ' ' . $time . '</span>';

		return $output;
	}

	function get_event_location($post_id = false, $name_only = false) {
		global $post;
		$post_id = $post_id ? $post_id : $post->ID;

		$locations = get_the_terms($post_id, 'event-location');

		if(!$locations)
			return false;

		$location = array_shift($locations);

		$name = $location->name;
		$address = humus_get_address($location->taxonomy . '_' . $location->term_id);

		if($name_only)
			return $name;

		return '<span class="location-name">' . $name . '</span><span class="location-address">' . $address . '</span>';
	}

	/* 
	 * Queries
	 */

	function query_vars($vars) {
		$vars[] = 'humus_event_query';
		$vars[] = 'humus_event_order';
		return $vars;
	}

	function pre_get_posts($query) {

		$obj = get_queried_object();

		if($query->get('post_type') === 'event' || ($obj->slug === 'agenda' && $obj->taxonomy === 'section')){
			$query->set('humus_event_query', 1);
			$query->set('posts_per_page', -1);
		}

	}

	function posts_clauses($clauses, $query) {

		global $wpdb;

		if($query->get('humus_event_query') && !$query->is_single()) {

			$clauses['join'] .= " INNER JOIN {$wpdb->postmeta} AS event_ts ON ({$wpdb->posts}.ID = event_ts.post_id) ";

			$clauses['where'] .= " AND event_ts.meta_key = 'event_time' ";

			//$order = $query->get('humus_event_order') ? $query->get('humus_event_order') : 'DESC';
			//$clauses['orderby'] = "event_ts.meta_value+0 {$order}";

			$clauses['orderby'] = "
				CASE WHEN event_ts.meta_value > UNIX_TIMESTAMP(NOW()) THEN 0 ELSE 1 END,
				CASE WHEN event_ts.meta_value > UNIX_TIMESTAMP(NOW()) THEN event_ts.meta_value ELSE event_ts.meta_value * -1 END
			";

		}


		return $clauses;

	}

	/*
	 * Templates
	 */

	function archive() {

		global $wp_query;
		$obj = get_queried_object();

		if(is_post_type_archive('event') || (is_tax('section') && $obj->slug == 'agenda')) {

			$GLOBALS['humus_custom_archived'] = 1;

			/*
			 * WEEK FEATURED
			 */

			$featured_query = new WP_Query(array(
				'post_type' => 'event',
				'meta_query' => array(
					array(
						'key' => 'event_featured',
						'value' => 1
					)
				)
			));

			if($featured_query->have_posts()) :

				?>
				<div id="week_featured" class="sub-posts">
					<div class="container">
						<div class="sub-posts-title row">
							<div class="twelve columns">
								<a href="#" class="toggle-sub-posts">+</a>
								<h2><?php _e('Featured this week', 'humus'); ?></h2>
								<p class="results">
									<?php printf(_n('%d result', '%d results', $featured_query->found_posts, 'humus'), $featured_query->found_posts); ?>
								</p>
							</div>
						</div>
						<div class="sub-posts-content row">
							<?php
							while($featured_query->have_posts()) :

								$featured_query->the_post();

								get_template_part('content');

							endwhile;
							?>
						</div>
					</div>
				</div>
				<?php
			endif;

			/*
			 * THIS MONTH
			 */

			$month = date_i18n('F');

			$month_range = array(
				mktime(0, 0, 0, date('n'), 1),
				mktime(23, 59, 0, date('n'), date('t'))
			);

			$month_query = new WP_Query(array(
				'post_type' => 'event',
				'meta_query' => array(
					array(
						'key' => 'event_time',
						'value' => $month_range[0],
						'compare' => '>=',
						'type' => 'NUMERIC'
					),
					array(
						'key' => 'event_time',
						'value' => $month_range[1],
						'compare' => '<=',
						'type' => 'NUMERIC'
					)
				)
			));

			if($month_query->have_posts()) :

				?>
				<div id="month_events" class="sub-posts">
					<div class="container">
						<div class="sub-posts-title row">
							<div class="twelve columns">
								<a href="#" class="toggle-sub-posts">+</a>
								<h2><?php echo $month; ?></h2>
								<p class="results">
									<?php printf(_n('%d result', '%d results', $month_query->found_posts, 'humus'), $month_query->found_posts); ?>
								</p>
							</div>
						</div>
						<div class="sub-posts-content row">
							<?php
							while($month_query->have_posts()) :

								$month_query->the_post();

								get_template_part('content');

							endwhile;
							?>
						</div>
					</div>
				</div>
				<?php
			endif;

			/*
			 * NEXT MONTH
			 */

			$month = date_i18n('F', mktime(0, 0, 0, date('n') + 1, 1));

			$month_range = array(
				mktime(0, 0, 0, date('n') + 1, 1),
				mktime(23, 59, 0, date('n') + 1, date('t', mktime(0, 0, 0, date('n') + 1, 1)))
			);

			$month_query = new WP_Query(array(
				'post_type' => 'event',
				'meta_query' => array(
					array(
						'key' => 'event_time',
						'value' => $month_range[0],
						'compare' => '>=',
						'type' => 'NUMERIC'
					),
					array(
						'key' => 'event_time',
						'value' => $month_range[1],
						'compare' => '<=',
						'type' => 'NUMERIC'
					)
				)
			));

			if($month_query->have_posts()) :

				?>
				<div id="month_events" class="sub-posts">
					<div class="container">
						<div class="sub-posts-title row">
							<div class="twelve columns">
								<a href="#" class="toggle-sub-posts">+</a>
								<h2><?php echo $month; ?></h2>
								<p class="results">
									<?php printf(_n('%d result', '%d results', $month_query->found_posts, 'humus'), $month_query->found_posts); ?>
								</p>
							</div>
						</div>
						<div class="sub-posts-content row">
							<?php
							while($month_query->have_posts()) :

								$month_query->the_post();

								get_template_part('content');

							endwhile;
							?>
						</div>
					</div>
				</div>
				<?php
			endif;

		}

	}

	function list_article_before_thumbnail() {
		if(get_post_type() == 'event') {
			?>
			<p class="event-date"><?php echo $this->get_event_date(false, _x('d M', 'Thumbnail event date format', 'humus')); ?></p>
			<?php
		}
	}

	function list_article_footer($content) {
		if(get_post_type() == 'event') {
			ob_start();
			?>
			<p class="event-time"><?php _e('starting at', 'humus'); ?> <?php echo $this->get_event_date(false, _x('g:i a', 'Minimal event time format', 'humus')); ?></p>

			<?php
			$content = ob_get_clean();
		}
		return $content;
	}

	function list_article_before_title() {
		if(get_post_type() == 'event') {
			echo '<p class="location-name">' . $this->get_event_location(false, true) . '</p>';
		}
	}

	function disable_order_filter($options) {
		if(get_post_type() == 'event') {
			$options = false;
		}
		return $options;
	}

}

$humus_events = new Humus_Events();

function humus_get_event_date($post_id = false) {
	global $humus_events;
	return $humus_events->get_event_date($post_id);
}

function humus_get_event_location($post_id = false) {
	global $humus_events;
	return $humus_events->get_event_location($post_id);
}