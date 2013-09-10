<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package Cardume
 * @subpackage Humus
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<?php do_action( 'before' ); ?>
	<header id="masthead" class="site-header" role="banner">

		<div class="container">
			<div class="twelve columns">
				<?php
				$logo = humus_get_logo_url();
				?>
				<h1 class="site-title <?php if($logo) echo 'with-logo'; ?>">
					<a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
						<?php bloginfo('name'); ?>
						<?php humus_logo(); ?>
					</a>
				</h1>
				<nav id="mastnav">
					<?php
					wp_nav_menu(array(
						'theme_location' => 'primary',
						'container' => false,
						'walker' => new Humus_Header_Menu_Walker()
					));
					?>
				</nav>
				<div class="search">
					<?php // get_search_form(); ?>
				</div>
			</div>
		</div>

	</header><!-- #masthead -->

	<div id="main" class="site-main">