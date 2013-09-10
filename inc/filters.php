<?php
/**
 * Archive filtering system
 *
 * @package Cardume
 * @subpackage Humus
 */

class Humus_Filters {

	var $filter_prefix = 'humus_filter_';

	function __construct() {
		add_action('init', array($this, 'init'));
	}

	function init() {
		$this->register_related_field_group();
		add_action('humus_before_archive_posts', array($this, 'form'));
		add_action('posts_clauses', array($this, 'posts_clauses'), 10, 2);
	}

	function get_taxonomies() {
		return apply_filters('humus_filterable_taxonomies', array('axis', 'section'));
	}

	function get_relatable_taxonomies() {
		return apply_filters('humus_relatable_taxonomies', array('category'));
	}

	function get_order_options() {
		$query_arg = $this->filter_prefix . 'order';
		$active = $this->get_active_filter($query_arg);

		$options = array(
			'default' => array(
				'name' => __('Most recent', 'humus'),
				'active' => $active ? false : true,
				'order' => 0
			),
			'oldest' => array(
				'name' => __('Oldest', 'humus'),
				'active' => ($active == 'oldest') ? true : false,
				'order' => 5
			)
		);

		return apply_filters('humus_filter_order_options', $options);
	}

	function get_related_selector_taxonomies() {

		if(!is_tax($this->get_taxonomies()))
			return false;

		return $this->get_relatable_taxonomies();

	}

	function get_related_term_url($term_id, $active, $query_arg) {
		if(!is_array($active))
			return add_query_arg(array($query_arg => array($term_id)));

		if(in_array($term_id, $active)) {
			$key = array_search($term_id, $active);
			unset($active[$key]);
		} else {
			$active[] = $term_id;
		}
		return add_query_arg(array($query_arg => $active));
	}

	function get_active_filter($query_arg) {
		if(!isset($_REQUEST[$query_arg]) || !$_REQUEST[$query_arg])
			return false;

		return $_REQUEST[$query_arg];
	}

	function register_related_field_group() {

		$taxonomies = $this->get_taxonomies();
		$locations = $this->get_relatable_taxonomies();

		if(function_exists("register_field_group") && $taxonomies && !empty($taxonomies) && $locations && !empty($locations)) {

			$args = array (
				'id' => 'acf_related-taxonomies',
				'title' => __('Related taxonomies', 'humus'),
				'fields' => array(),
				'location' => array(),
				'options' => array(
					'position' => 'normal',
					'layout' => 'no_box',
					'hide_on_screen' => array(),
				),
				'menu_order' => 0,
			);

			foreach($taxonomies as $taxonomy) {

				$tax_object = get_taxonomy($taxonomy);
				$lower_name = strtolower($tax_object->labels->name);

				$args['fields'][] = array(
					'key' => 'field_related_' . $taxonomy,
					'label' => sprintf(__('Related %s', 'humus'), $lower_name),
					'name' => 'related_' . $taxonomy,
					'type' => 'taxonomy',
					'instructions' => sprintf(__('Select related %s to show up as it\'s filter.', 'humus'), $lower_name),
					'taxonomy' => $taxonomy,
					'field_type' => 'checkbox',
					'allow_null' => 0,
					'load_save_terms' => 0,
					'return_format' => 'id',
					'multiple' => 0,
				);
			}

			foreach($locations as $location) {
				$args['location'][] = array(
					array(
						'param' => 'ef_taxonomy',
						'operator' => '==',
						'value' => $location,
						'order_no' => 0,
						'group_no' => 0,
					),
				);				
			}

			register_field_group($args);
		}

	}

	function form() {
		$taxonomies = $this->get_taxonomies();
		$relatable_taxonomies = $this->get_related_selector_taxonomies();
		?>
		<section id="filters">
			<div class="container">
				<div class="row">

					<div class="three columns">
						<h3 class="filter-title"><?php _e('Showing:', 'humus'); ?></h3>
					</div>

					<?php

					if($taxonomies && !empty($taxonomies)) {
						foreach($taxonomies as $tax) {
							$this->taxonomy_selector($tax);
						}
					}

					$this->ordering_selector();

					if($relatable_taxonomies) {
						?>
						<div class="three columns">
							<a href="#" class="toggle-more-filters"><?php _e('Filter results', 'humus'); ?></a>
						</div>
						<?php
					}
					?>

				</div>

				<?php $this->related_selector($relatable_taxonomies); ?>

			</div>
		</section>
		<?php
	}

	function taxonomy_selector($taxonomy = false) {
		if(!$taxonomy)
			return false;

		$terms = get_terms($taxonomy, array('hide_empty' => 0));
		$query_arg = $this->filter_prefix . $taxonomy;
		$active = $this->get_active_filter($query_arg);

		if(!is_tax($taxonomy) && $terms) {
			$tax_object = get_taxonomy($taxonomy);
			?>
			<div class="three columns">
				<div class="humus-dropdown">
					<ul>
						<?php foreach($terms as $term) { ?>
							<li <?php if($active == $term->term_id) echo 'class="active"'; ?>><a href="<?php echo add_query_arg(array($query_arg => $term->term_id)); ?>"><?php echo $term->name; ?></a></li>
						<?php } ?>
						<li <?php if(!$active) echo 'class="active"'; ?>><a href="<?php echo remove_query_arg($query_arg); ?>"><?php echo $tax_object->labels->all_items; ?></a></li>
					</ul>
				</div>
			</div>
			<?php
		}
	}

	function ordering_selector() {
		$query_arg = $this->filter_prefix . 'order';
		$options = $this->get_order_options();
		if($options) :
			?>
			<div class="three columns">
				<div class="humus-dropdown">
					<ul>
						<?php foreach($options as $key => $option) :
							$url = add_query_arg(array($query_arg => $key));
							if($key == 'default')
								$url = remove_query_arg($query_arg);
							?>
							<li <?php if($option['active']) echo 'class="active"'; ?>><a href="<?php echo $url; ?>"><?php echo $option['name']; ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<?php
		endif;
	}

	function related_selector($relatable_taxonomies) {

		if(!is_tax($this->get_taxonomies()) || !$relatable_taxonomies)
			return false;

		$term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));

		foreach($relatable_taxonomies as $taxonomy) {

			$related_terms = array();
			$taxonomy_terms = get_terms($taxonomy);

			foreach($taxonomy_terms as $taxonomy_term) {
				$taxonomy_term_relateds = get_field('related_' . get_query_var('taxonomy'), $taxonomy . '_' . $taxonomy_term->term_id);
				if(is_array($taxonomy_term_relateds) && in_array($term->term_id, $taxonomy_term_relateds)) {
					$related_terms[] = $taxonomy_term;
				}
			}

			if(!$related_terms)
				continue;

			$query_arg = $this->filter_prefix . $taxonomy;
			$active = $this->get_active_filter($query_arg);
			$tax_object = get_taxonomy($taxonomy);
			?>
			<div id="<?php echo $taxonomy; ?>-filter" class="related-selector row">

				<div class="three columns">
					<h4 class="related-title"><?php echo $tax_object->labels->name; ?>:</h4>
				</div>

				<div class="nine columns">
					<ul class="related-terms">
						<?php
						foreach($related_terms as $term) {

							$class = '';
							if(is_array($active) && in_array($term->term_id, $active)) {
								$class = 'active';
							}

							?>
							<li class="<?php echo $class; ?>"><a href="<?php echo $this->get_related_term_url($term->term_id, $active, $query_arg); ?>"><?php echo $term->name; ?></a></li>
						<?php } ?>
					</ul>
				</div>

			</div>
			<?php

		}

	}

	/* 
	 * Perform filtering
	 */
	
	function posts_clauses($clauses, $query) {

		global $wp_the_query, $wpdb;

		if($wp_the_query !== $query)
			return $clauses;

		$request = $_REQUEST;

		if(is_array($request) && !empty($request)) {

			$request_keys = array();
			foreach($request as $key => $val) {
				$request_keys[] = $key;
			}

			/*
			 * TAXONOMIES
			 */

			$taxonomies = $this->get_taxonomies();
			$relatable_taxonomies = $this->get_relatable_taxonomies();

			$filter_taxonomies = array_merge($taxonomies, $relatable_taxonomies);

			foreach($filter_taxonomies as $taxonomy) {
				$key = $this->filter_prefix . $taxonomy;
				if(in_array($key, $request_keys)) {
					$term_id = $request[$key];

					$clauses['join'] .= "
						INNER JOIN {$wpdb->term_relationships} AS {$key} ON ({$wpdb->posts}.ID = {$key}.object_id)
						";

					$clauses['where'] .= " AND ";
					$where = array();
					if(is_array($term_id)) {
						foreach($term_id as $t_id) {
							$where[] = "{$key}.term_taxonomy_id IN ({$t_id})";
						}
					} else {
						$where[] = "{$key}.term_taxonomy_id IN ({$term_id})";
					}

					$clauses['where'] .= ' ( ' . implode(' OR ', $where) . ' ) ';

				}
			}

			/*
			 * ORDERING
			 */

			if(in_array($this->filter_prefix . 'order', $request_keys)) {

				$order = $request[$this->filter_prefix . 'order'];

				if($order == 'oldest') {

					$clauses['orderby'] = "{$wpdb->posts}.post_date ASC";

				}

				$clauses = apply_filters('humus_filter_order_clauses', $clauses);

			}

		}

		return $clauses;
	}

}

new Humus_Filters();