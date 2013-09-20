<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each specific one. For example, Twenty Fourteen
 * already has tag.php for Tag archives, category.php for Category archives,
 * and author.php for Author archives.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Cardume
 * @subpackage Humus
 */

get_header(); ?>

<section id="primary" class="content-area">
	<div id="content" class="site-content" role="main">

		<?php humus_archive_header(); ?>

		<section class="page-content">

			<?php

			do_action('humus_before_archive_posts');

			if(have_posts()) :

				?>
				<div class="container">
					<?php

					while ( have_posts() ) :

						the_post();

						get_template_part('content');

					endwhile;
					?>
				</div>
				<?php

			else :



			endif;

			do_action('humus_after_archive_posts');

			?>

		</section>

	</div><!-- #content -->
</section><!-- #primary -->

<?php
get_footer();