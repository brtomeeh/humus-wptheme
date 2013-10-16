<?php

/*
 * Advanced Custom Fields
 */

function humus_acf_dir() {
	return get_template_directory_uri() . '/inc/acf/';
}
add_filter('acf/helpers/get_dir', 'humus_acf_dir');

function humus_acf_date_time_picker_dir() {
	return humus_acf_dir() . '/add-ons/acf-field-date-time-picker/';
}
add_filter('acf/add-ons/date-time-picker/get_dir', 'humus_acf_date_time_picker_dir');

function humus_acf_repeater_dir() {
	return humus_acf_dir() . '/add-ons/acf-repeater/';
}
add_filter('acf/add-ons/repeater/get_dir', 'humus_acf_repeater_dir');

define('ACF_LITE', false);
require_once(TEMPLATEPATH . '/inc/acf/acf.php');

/*
 * Tools
 */
require_once(TEMPLATEPATH . '/inc/map/map.php');
require_once(TEMPLATEPATH . '/inc/magazine/magazine.php');
require_once(TEMPLATEPATH . '/inc/media.php');
require_once(TEMPLATEPATH . '/inc/header-image.php');
require_once(TEMPLATEPATH . '/inc/taxonomy-styles.php');
require_once(TEMPLATEPATH . '/inc/filters.php');
require_once(TEMPLATEPATH . '/inc/theme-options.php');

/*
 * Taxonomies
 */
require_once(TEMPLATEPATH . '/inc/section.php');
require_once(TEMPLATEPATH . '/inc/axis.php');

/*
 * Side stuff
 */
require_once(TEMPLATEPATH . '/inc/events/events.php');
require_once(TEMPLATEPATH . '/inc/partners.php');
require_once(TEMPLATEPATH . '/inc/contact/contact.php');
require_once(TEMPLATEPATH . '/inc/about/about.php');
require_once(TEMPLATEPATH . '/inc/newsletter/newsletter.php');

include_once(TEMPLATEPATH . '/inc/ads.php');

/*
 * Styles
 */

function humus_styles() {
	wp_register_style('base', get_template_directory_uri() . '/css/base.css');
	wp_register_style('skeleton', get_template_directory_uri() . '/css/skeleton.css', array('base'));
	wp_register_style('webfonts', 'http://fonts.googleapis.com/css?family=Lato:300,400,300italic,400italic|Open+Sans:300italic,400italic,600italic,400,300,600,700,800');
	wp_register_style('responsive-nav', get_template_directory_uri() . '/css/responsive-nav.css');
	wp_register_style('main', get_template_directory_uri() . '/css/main.css', array('skeleton', 'webfonts', 'responsive-nav'));
	wp_register_style('home', get_template_directory_uri() . '/css/home.css', array('main'));

	wp_enqueue_style('main');

	if(is_front_page())
		wp_enqueue_style('home');

}
add_action('wp_enqueue_scripts', 'humus_styles');

function humus_scripts() {
	wp_register_script('imagesloaded', get_template_directory_uri() . '/js/imagesloaded.js', array('jquery'), '3.0.4');
	wp_register_script('fitvids', get_template_directory_uri() . '/js/jquery.fitvids.js', array('jquery'), '1.0');
	wp_register_script('lockfixed', get_template_directory_uri() . '/js/jquery.lockfixed.min.js', array('jquery'), '0.1');
	wp_register_script('sly', get_template_directory_uri() . '/js/sly.min.js', array('jquery'), '1.1.0');
	wp_register_script('isotope', get_template_directory_uri() . '/js/jquery.isotope.min.js', array('jquery'), '1.5.25');
	wp_register_script('underscore', get_template_directory_uri() . '/js/underscore-min.js', array(), '1.5.2');

	wp_register_script('swipebox', get_template_directory_uri() . '/js/swipebox/jquery.swipebox.min.js', array('jquery'));
	wp_register_style('swipebox', get_template_directory_uri() . '/js/swipebox/swipebox.css');

	wp_register_script('responsive-nav', get_template_directory_uri() . '/js/responsive-nav.min.js', array('jquery'));

	wp_register_script('frontend', get_template_directory_uri() . '/js/frontend.js', array('jquery',  'imagesloaded', 'fitvids', 'lockfixed', 'sly', 'responsive-nav'), '0.1.1');


	wp_enqueue_script('frontend');
	wp_localize_script('frontend', 'humus_frontend', array(
		'ajaxurl' => admin_url('admin-ajax.php')
	));
}
add_action('wp_enqueue_scripts', 'humus_scripts');

/*
 * Theme setup
 */

function humus_setup() {

	// i18n
	load_theme_textdomain('humus', get_template_directory() . '/languages');

	// Thumbnail support
	add_theme_support('post-thumbnails');

	// Image sizes
	add_image_size('humus-thumbnail', 260, 260, true);
	add_image_size('humus-wide-thumbnail', 360, 205, true);

	// Menus
	register_nav_menu('primary', __('Primary menu', 'humus'));

	// Humus Gallery
	remove_shortcode('gallery', 'gallery_shortcode');
	add_shortcode('gallery', 'humus_gallery');
}
add_action('after_setup_theme', 'humus_setup');

// custom header menu
include_once(TEMPLATEPATH . '/inc/header-menu-walker.php');

function humus_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'humus' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'humus_wp_title', 10, 2 );

/*
 * Custom options
 */

// Logo

function humus_get_logo_url() {
	$logo = get_field('site_logo_image', 'option');
	if($logo)
		return $logo['url'];
	return false;
}

function humus_logo() {
	$url = humus_get_logo_url();
	if($url) {
		echo '<img src="' . $url . '" alt="' . get_bloginfo('name') . '" />';
	}
}

function humus_get_header_image_url() {

	$id = false;

	if(is_tax()) {
		$term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
		$id = get_query_var('taxonomy') . '_' . $term->term_id;
	} elseif(is_category()) {
		$term = get_category_by_slug(get_query_var('category_name'));
		$id = 'category_' . $term->term_id;
	} elseif(is_single()) {
		global $post;
		$id = $post->ID;
	}

	if($id)
		return get_field('header_image', $id);

	return false;
}

function humus_breadcrumb($before = '', $sep = '/', $after = '/') {

	/*
	 * Items
	 */
	$items = array(
		array(
			'url' => home_url(),
			'title' => get_bloginfo('name'),
			'class' => 'home-url'
		)
	);

	if(is_single()) {
		global $post;
		// Axis
		$axes = get_the_terms($post->ID, 'axis');
		if($axes) {
			$axis = array_shift($axes);
			$items[] = array(
				'url' => get_term_link($axis),
				'title' => $axis->name,
				'class' => $section->slug
			);
		}
		// Section
		$sections = get_the_terms($post->ID, 'section');
		if($sections) {
			$section = array_shift($sections);
			$items[] = array(
				'url' => get_term_link($section),
				'title' => $section->name,
				'class' => $section->slug
			);
		} else {
			$categories = get_the_terms($post->ID, 'category');
			if($categories) {
				$category = array_shift($categories);
				$items[] = array(
					'url' => get_term_link($category),
					'title' => $category->name,
					'class' => $category->slug
				);
			}
		}

		if(count($items) == 1) {
			$post_type = get_post_type();
			$post_type_obj = get_post_type_object($post_type);
			$items[] = array(
				'url' => get_post_type_archive_link($post_type),
				'title' => $post_type_obj->labels->name,
				'class' => $post_type
			);
		}
	}

	$items = apply_filters('humus_breadcrumb_items', $items);

	/*
	 * Parse items and build links
	 */
	$links = array();

	foreach($items as $item) {
		$links[] = '<a href="' . $item['url'] . '" title="' . $item['title'] . '">' . $item['title'] . '</a>';
	}

	echo '<nav class="humus-breadcrumb">' . $before . implode($sep, $links) . $after . '</nav>';

}

function humus_oembed_result($html, $url, $args) {
	return $html;
}
add_filter('oembed_result', 'humus_oembed_result', 10, 3);

function humus_social_apis() {

	// Facebook
	?>
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/pt_BR/all.js#xfbml=1&appId=174607379284946";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
	<?php

	// Twitter
	?>
	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
	<?php

	// Google Plus
	?>
	<script type="text/javascript">
	  (function() {
	    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
	    po.src = 'https://apis.google.com/js/plusone.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	  })();
	</script>
	<?php
}
add_action('wp_footer', 'humus_social_apis');

function humus_get_post_tax_label($post_id = false) {

	global $post;
	$post_id = $post_id ? $post_id : $post->ID;

	$color_taxonomy = 'section';
	$label_taxonomy = 'category';

	$color_term = get_the_terms($post_id, $color_taxonomy);
	if($color_term)
		$color_term = array_shift($color_term);

	$label_term = get_the_terms($post_id, $label_taxonomy);

	$output = '';

	if($label_term) {

		$label_term = array_shift($label_term);

		$style = '';
		if($color_term && get_field('term_color', $color_taxonomy . '_' . $color_term->term_id)) {
			$style = 'background:' . get_field('term_color', $color_taxonomy . '_' . $color_term->term_id);
		}

		$output = '<p class="post-tax-label"><a href="' . get_term_link($label_term, $label_taxonomy) . '" title="' . $label_term->name .'"  style="' . $style . '">' . $label_term->name . '</a></p>';

	}

	return $output;

}

function humus_related_content() {
	if(function_exists('yarpp_related'))
		yarpp_related(array(
			'template' => 'yarpp-template-humus.php'
		));
}
add_action('humus_after_single_post', 'humus_related_content');

function humus_is_template($template = '') {
	global $wp_query;

	$bool = false;

	switch($template) {
		case 'minimal':
			// Check YARPP
			if(function_exists('yarpp_related')) {
				$request = $wp_query->request;
				if(strrpos($request, 'yarpp') !== false) {
					$bool = true;
				}
			}
			break;
	}

	return apply_filters('humus_is_template', $bool);
}

function humus_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
			// Display trackbacks differently than normal comments.
			?>
			<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
				<p><?php _e( 'Pingback:', 'humus' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'humus' ), '<span class="edit-link">', '</span>' ); ?></p>
			</li>
			<?php
		break;
		default :
		// Proceed with normal comments.
		global $post;
	?>
	<li <?php comment_class('row'); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>">
			<div class="one column alpha <?php if($comment->comment_parent) echo 'offset-by-one'; ?>">
				<header class="clearfix">
					<?php echo get_avatar($comment, 60); ?>
				</header>
			</div>
			<div class="<?php if($comment->comment_parent) echo 'ten'; else echo 'eleven'; ?> columns omega">
				<div class="comment-meta">
					<span class="comment-author">
						<?php
						printf( '<cite class="fn">%1$s</cite>',
							get_comment_author_link()
						);
						?>
					</span> | 
					<span class="comment-date">
						<?php
						printf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
							esc_url( get_comment_link( $comment->comment_ID ) ),
							get_comment_time( 'c' ),
							/* translators: 1: date, 2: time */
							sprintf( __( '%1$s at %2$s', 'humus' ), get_comment_date(), get_comment_time() )
						);
						?>
					</span>
					<?php edit_comment_link( __( 'Edit', 'humus' ), ' | <span class="comment-edit-link">', '</span>'); ?>
				</div>
				<div class="comment-content-area">
					<?php if ( '0' == $comment->comment_approved ) : ?>
						<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'humus' ); ?></p>
					<?php endif; ?>

					<section class="comment-content">
						<?php comment_text(); ?>
					</section><!-- .comment-content -->

					<div class="reply">
						<?php comment_reply_link( array_merge( $args, array('reply_text' => __( 'Reply', 'humus' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
					</div><!-- .reply -->
				</div>
			</div>
		</article><!-- #comment-## -->
	<?php
		break;
	endswitch; // end comment_type check
}

function humus_archive_header($short_version = false) {

	$header_image = $short_version ? false : humus_get_header_image_url();
	$section_icon = humus_get_term_icon_url();

	?>
	<header class="page-header <?php if($header_image) echo 'header-image'; ?>" <?php if($header_image) echo 'style="background-image:url(' . $header_image . ')"'; ?>>

		<div class="header-content">

			<?php do_action('humus_before_header_content'); ?>

			<div class="container">

				<div class="one column">
					<?php if($section_icon) : ?>
						<img src="<?php echo $section_icon; ?>" alt="<?php single_term_title(); ?>" />
					<?php else : ?>
						&nbsp;
					<?php endif; ?>
				</div>

				<div class="six columns">

					<?php humus_breadcrumb(); ?>

					<h1 class="page-title">

						<?php

						if(is_day()) :
							printf( __( 'Day: %s', 'humus' ), get_the_date() );

						elseif(is_month()) :
							printf( __( 'Month: %s', 'humus' ), get_the_date( 'F Y' ) );

						elseif(is_year()) :
							printf( __( 'Year: %s', 'humus' ), get_the_date( 'Y' ) );

						elseif(is_tax() || is_tag() || is_category()) :
							single_term_title();

						elseif(is_post_type_archive()) :
							post_type_archive_title();

						elseif(is_search()) :
							_e('Search', 'humus');

						elseif(is_404()) :
							_e('Nothing found', 'humus');

						else :
							_e( 'Archives', 'humus' );

						endif;
						?>

					</h1>

					<?php
					if((is_tax() || is_tag() || is_category()) && !$short_version) :

							$description = term_description();
							if($description) :
								echo $description;

							endif;

					elseif(is_search() || is_404()) :
					
						global $wp;

						$s_request = str_replace('/', ' ', str_replace('-', ' ', $wp->request));

						$s = isset($_GET['s']) ? $_GET['s'] : $s_request;

						if(is_404()) : 
							?>
							<p class="results"><?php _e('Try using our search:', 'humus'); ?></p>
							<?php
						endif;
						?>
						<form id="searchform" action="<?php echo home_url(); ?>">
							<input name="s"	 type="text" placeholder="<?php _e('Type your search...', 'humus'); ?>" value="<?php if($s) echo $s; ?>" />
						</form>
						<?php
						if(is_search()) :
							global $wp_query;
							?>
							<p class="results">
								<?php printf(_n('We found just %d result for your search', 'We found %d results for your search', $wp_query->found_posts, 'humus'), $wp_query->found_posts); ?>
							</p>
							<?php
						endif;

					endif;
					?>

				</div>

				<?php do_action('humus_header_content'); ?>

			</div>

			<?php do_action('humus_after_header_content'); ?>

		</div>

	</header>
	<?php
}

function humus_gallery($atts, $content = null) {

	extract(shortcode_atts(array(
		'ids' => false,
	), $atts));

	if($ids) {

		$ids = split(',', $ids);
		
		$images = array();

		foreach($ids as $id) {
			$images[] = get_post($id);
		}

	} else {

		global $post;
		$images = get_posts(array(
			'post_type' => 'attachment',
			'post_parent' => $post->ID,
			'posts_per_page' => -1,
			'post_status' => null
		));

	}

	if(!$images || empty($images))
		return '';

	wp_enqueue_script('humus-gallery', get_template_directory_uri() . '/js/gallery.js', array('jquery', 'sly'), '0.1.0');

	ob_start();
	?>

	<div class="humus-gallery-container row">

		<div class="image-container">
			<a href="#" class="next"><?php _e('Next image', 'humus'); ?></a>
			<a href="#" class="prev"><?php _e('Previous image', 'humus'); ?></a>
			<div class="image"></div>
		</div>
		<div class="image-list-container">
			<ul class="image-list">
				<?php foreach($images as $image) :
					$large = wp_get_attachment_image_src($image->ID, 'large');
					$thumb = wp_get_attachment_image_src($image->ID, 'thumbnail');
					?>
					<li class="image-thumb" data-image="<?php echo $large[0]; ?>">
						<img src="<?php echo $thumb[0]; ?>" alt="<?php echo $image->post_title; ?>" />
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="scrollbar">
			<div class="handle"></div>
		</div>

	</div>

	<?php
	$output = ob_get_clean();

	return $output;
}

function humus_album_gallery() {

	extract(shortcode_atts(array(
		'ids' => false,
	), $atts));

	if($ids) {

		$ids = split(',', $ids);
		
		$images = array();

		foreach($ids as $id) {
			$images[] = get_post($id);
		}

	} else {

		global $post;
		$images = get_posts(array(
			'post_type' => 'attachment',
			'post_parent' => $post->ID,
			'posts_per_page' => -1,
			'post_status' => null
		));

	}

	if(!$images || empty($images))
		return '';

	wp_enqueue_style('swipebox');

	wp_enqueue_script('humus-gallery', get_template_directory_uri() . '/js/gallery.js', array('jquery', 'sly', 'isotope', 'imagesloaded', 'swipebox'), '0.1.0');

	ob_start();
	?>
	<div class="humus-masonry-gallery row">
		<ul class="images">
			<?php foreach($images as $image) :
				$large = wp_get_attachment_image_src($image->ID, 'large');
				$medium = wp_get_attachment_image_src($image->ID, 'medium');
				?>
				<li class="image">
					<a rel="gallery-<?php echo $post->ID; ?>" href="<?php echo $large[0]; ?>" class="swipebox" title="<?php echo $image->post_title; ?>"><img src="<?php echo $medium[0]; ?>" alt="<?php echo $image->post_title; ?>" /></a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
	$output = ob_get_clean();

	return $output;
}

function humus_get_image_count($p = false) {
	global $post;
	$p = $p ? $p : $post;

	$content = $p->post_content;

	$photos = array();

	if(strpos($content, '[gallery') !== false) {

		$pattern = get_shortcode_regex();

		preg_match('/'.$pattern.'/s', $p->post_content, $matches);

		if (is_array($matches) && $matches[2] == 'gallery') {

			preg_match('/\[gallery ids=\"(.*?)\"]/',$matches[0],$ids);

			if (is_array($ids) && $ids[1] ) {

				$photos = explode(',',$ids[1]);

			}

		}

	}

	if(empty($photos)) {

		$photos = get_posts(array(
			'post_type' => 'attachment',
			'post_status' => null,
			'post_parent' => $p->ID,
			'posts_per_page' => -1
		));

	}

	return count($photos);

}

function humus_album_page_template() {

	global $post;
	if(has_term('albuns', 'section') && is_single()) {
		get_template_part('full', 'width-page');
		exit;
	}

}
add_action('template_redirect', 'humus_album_page_template');

function humus_album_content($content) {

	if(has_term('albuns', 'section') && strpos($content, '[gallery') === false) {

		remove_shortcode('gallery', 'humus_gallery');
		add_shortcode('gallery', 'humus_album_gallery');

		add_action('humus_after_page_content', 'humus_outside_album_gallery');

	}

	return $content;

}
add_filter('the_content', 'humus_album_content');

$after_gallery = '';

function humus_album_after_gallery_content($atts, $content = '') {
	global $after_gallery;
	$after_gallery = $content;
	return '';
}
add_shortcode('humus-after-gallery', 'humus_album_after_gallery_content');

function humus_outside_album_gallery() {
	global $after_gallery;
	?>
	<div class="container row">
		<div class="twelve columns">
			<?php echo do_shortcode('[gallery]'); ?>
		</div>
		<?php if($after_gallery) : ?>
			<div class="one column">&nbsp;</div>
			<div class="ten columns">
				<section class="post-content">
					<?php echo apply_filters('the_content', $after_gallery); ?>
				</section>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

function humus_pagination() {
	global $wp_query;
	if($wp_query->max_num_pages > 1) {

		echo '<div class="container"><div class="twelve columns"><div class="humus-pagination">';
			if(function_exists('wp_pagenavi'))
				wp_pagenavi();
			else {
				echo '<div class="wp-pagination">';
				posts_nav_link();
				echo '</div>';
			}
		echo '</div></div></div>';

	}
}

/*
 * Humus
 * Map view terms
 */

function humus_map_view_terms($terms) {
	$terms[] = get_term_by('slug', 'tape', 'section');
	return $terms;
}
add_filter('humus_map_view_terms', 'humus_map_view_terms');

function humus_404_template() {
	if(is_404()) {
		include_once(TEMPLATEPATH . '/search.php');
		exit;
	}
}
add_action('template_redirect', 'humus_404_template');

function humus_404_query($query) {
	if($query->is_404()) {
		global $wp;
		$s_request = str_replace('/', ' ', str_replace('-', ' ', $wp->request));
		$query->set('s', $s_request);
	}
}
add_action('pre_get_posts', 'humus_404_query');


function humus_toggle_display_author() {
	if(function_exists('register_field_group')) {

		$post_types = get_post_types(array('public' => true, '_builtin' => false), 'names');
		$post_types[] = 'post';

		$locations = array();
		foreach($post_types as $post_type) {
			$locations[] = array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => $post_type,
					'order_no' => 0,
					'group_no' => 0
				)
			);
		}

		register_field_group(array (
			'id' => 'acf_show-author-info',
			'title' => __('Show author info', 'humus'),
			'fields' => array (
				array (
					'key' => 'field_display_author',
					'label' => __('Author info', 'humus'),
					'name' => 'display_author',
					'type' => 'true_false',
					'instructions' => __('Check to show author information', 'humus'),
					'message' => __('Display author information', 'humus'),
					'default_value' => 0,
				),
			),
			'location' => $locations,
			'options' => array (
				'position' => 'normal',
				'layout' => 'no_box',
				'hide_on_screen' => array (
				),
			),
			'menu_order' => 0,
		));
	}
}
add_action('init', 'humus_toggle_display_author');


/*
 * Connect REMIX
 */

function humus_connect_remix_post_type($post_types) {
	$post_types[] = 'remix';
	return $post_types;
}
add_filter('humus_axis_post_types', 'humus_connect_remix_post_type');
add_filter('humus_section_post_types', 'humus_connect_remix_post_type');

/*
 * Connect event
 */

function humus_connect_event_post_type($post_types) {
	$post_types[] = 'event';
	return $post_types;
}
add_filter('humus_axis_post_types', 'humus_connect_event_post_type');
add_filter('humus_section_post_types', 'humus_connect_event_post_type');

/*
 * Connect PARTNER
 */

function humus_connect_partner_post_type($post_types) {
	$post_types[] = 'partner';
	return $post_types;
}
add_filter('humus_media_post_types', 'humus_connect_partner_post_type');

function humus_enable_filter_partner() {
	if(is_post_type_archive('partner'))
		return false;
	return true;
}
add_filter('humus_enable_filter', 'humus_enable_filter_partner');