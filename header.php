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
<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
<?php humus_favicon(); ?>
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
				<div class="header-navigation">
					<nav id="mastnav">
						<?php
						wp_nav_menu(array(
							'theme_location' => 'primary',
							'container' => false,
							'walker' => new Humus_Header_Menu_Walker()
						));
						?>
					</nav>
					<div class="right-side">
						<div class="search maintain-hover">
							<div class="searchform">
								<?php get_search_form(); ?>
							</div>
						</div>
                        <div class="newsletter hide-if-mobile maintain-hover">
                            <div class="newsletter-content">
                                <div class="newsletter-container">
                                    <p><?php _e('Sign up to our newsletter and be the first to know about our latest contents', 'humus'); ?></p>
                                    <?php humus_newsletter_form(); ?>
                                </div>
                            </div>
                        </div>
						<div class="social hide-if-mobile maintain-hover">
							<div class="social-content">
								<div class="social-icons">
									<?php
									$facebook = get_field('facebook_url', 'option');
									if($facebook)
										echo '<a href="' . $facebook . '" title="Facebook" target="_blank">facebook</a>';
									$twitter = get_field('twitter_url', 'option');
									if($twitter)
										echo '<a href="' . $twitter . '" title="Twitter" target="_blank">twitter</a>';
									$youtube = get_field('youtube_url', 'option');
									if($youtube)
										echo '<a href="' . $youtube . '" title="YouTube" target="_blank">youtube</a>';
									$instagram = get_field('instagram_url', 'option');
									if($instagram)
										echo '<a href="' . $instagram . '" title="Instagram" target="_blank">instagram</a>';
									$gplus = get_field('gplus_url', 'option');
									if($gplus)
										echo '<a href="' . $gplus . '" title="Google Plus" target="_blank">google</a>';
									$pinterest = get_field('pinterest_url', 'option');
									if($pinterest)
										echo '<a href="' . $pinterest . '" title="Pinterest" target="_blank">pinterest</a>';
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</header><!-- #masthead -->

	<div id="main" class="site-main">
