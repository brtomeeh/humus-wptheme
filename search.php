<?php
/**
 * The template for displaying search results.
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

			if(have_posts() && !isset($GLOBALS['humus_custom_archived'])) :

				?>
				<div class="container">
					<?php

					humus_pagination();

					?>
					<div class="row">
						<?php

						while ( have_posts() ) :

							the_post();

							get_template_part('content');

						endwhile;
						?>
					</div>
					<?php

					humus_pagination();

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