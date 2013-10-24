<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Cardume
 * @subpackage Humus
 */

get_header(); ?>

<div id="primary" class="content-area">
	<div id="content" class="site-content" role="main">
		<?php
			while ( have_posts() ) :

				the_post();

				do_action('humus_before_single_post');

				get_template_part('content');

				do_action('humus_after_single_post');

				// If comments are open or we have at least one comment, load up the comment template.
				if (comments_open() || get_comments_number()) {

					do_action('humus_before_single_post_comments');

					?>
					<div class="container">
						<div class="twelve columns">
							<?php comments_template(); ?>
						</div>
					</div>
					<?php

					do_action('humus_after_single_post_comments');
					
				}

			endwhile;
		?>
	</div><!-- #content -->
</div><!-- #primary -->

<?php
get_footer();