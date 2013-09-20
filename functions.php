<?php

/*
 * Tools
 */
require_once(TEMPLATEPATH . '/inc/map/map.php');
require_once(TEMPLATEPATH . '/inc/media.php');
require_once(TEMPLATEPATH . '/inc/header-image.php');
require_once(TEMPLATEPATH . '/inc/taxonomy-styles.php');

require_once(TEMPLATEPATH . '/inc/theme-options.php');

/*
 * Taxonomies
 */
require_once(TEMPLATEPATH . '/inc/section.php');
require_once(TEMPLATEPATH . '/inc/axis.php');

/*
 * Advanced Custom Fields
 */

function toolkit_acf_path() {
	return get_template_directory_uri() . '/inc/acf/';
}
add_filter('acf/helpers/get_dir', 'toolkit_acf_path');

define('ACF_LITE', false);
require_once(TEMPLATEPATH . '/inc/acf/acf.php');

/*
 * Humus includes
 */
include_once(TEMPLATEPATH . '/inc/filters.php');

/*
 * Styles
 */

function humus_styles() {
	wp_register_style('base', get_template_directory_uri() . '/css/base.css');
	wp_register_style('skeleton', get_template_directory_uri() . '/css/skeleton.css', array('base'));
	wp_register_style('webfonts', 'http://fonts.googleapis.com/css?family=Lato:300,400,300italic,400italic|Open+Sans:300italic,400italic,600italic,400,300,600,700,800');
	wp_register_style('main', get_template_directory_uri() . '/css/main.css', array('skeleton', 'webfonts'));
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
	wp_register_script('motio', get_template_directory_uri() . '/js/motio.min.js', array(), '2.2.1');
	wp_register_script('underscore', get_template_directory_uri() . '/js/underscore-min.js', array(), '1.5.2');

	wp_register_script('frontend', get_template_directory_uri() . '/js/frontend.js', array('jquery',  'imagesloaded', 'fitvids', 'lockfixed', 'sly'), '0.0.1');

	wp_enqueue_script('frontend');
}
add_action('wp_enqueue_scripts', 'humus_scripts');

/*
 * Theme setup
 */

function humus_setup() {

	// Thumbnail support
	add_theme_support('post-thumbnails');

	// Image sizes
	add_image_size('humus-thumbnail', 260, 260, true);
	add_image_size('humus-wide-thumbnail', 360, 205, true);

	// Menus
	register_nav_menu('primary', __('Primary menu', 'humus'));
}
add_action('after_setup_theme', 'humus_setup');

// custom header menu
include_once(TEMPLATEPATH . '/inc/header-menu-walker.php');

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
							printf( __( 'Day: %s', 'twentyfourteen' ), get_the_date() );

						elseif(is_month()) :
							printf( __( 'Month: %s', 'twentyfourteen' ), get_the_date( 'F Y' ) );

						elseif(is_year()) :
							printf( __( 'Year: %s', 'twentyfourteen' ), get_the_date( 'Y' ) );

						elseif(is_tax() || is_tag() || is_category()) :
							single_term_title();

						else :
							_e( 'Archives', 'twentyfourteen' );

						endif;
						?>

					</h1>

					<?php
					if((is_tax() || is_tag() || is_category()) && !$short_version) :

							$description = term_description();
							if($description) :
								echo $description;

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

/*
 * Humus
 * Map view terms
 */

function humus_map_view_terms($terms) {
	$terms[] = get_term_by('slug', 'tape', 'section');
	return $terms;
}
add_filter('humus_map_view_terms', 'humus_map_view_terms');

?>