<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Cardume
 * @subpackage Humus
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<div class="full-height-content">

				<section id="axes-area" class="full-height-section" style="background-image: url(http://lorempixum.com/1000/600/);">
				</section>

				<section id="axes-area" class="full-height-section" style="background-image: url(http://lorempixum.com/900/600/);">
				</section>

				<section id="axes-area" class="full-height-section" style="background-image: url(http://lorempixum.com/1200/600/);">
				</section>

				<section id="axes-area" class="full-height-section" style="background-image: url(http://lorempixum.com/1100/600/);">
				</section>

			</div>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php
get_footer();