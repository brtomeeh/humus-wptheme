<?php
/**
 * The Template for displaying related content from YARPP (http://wordpress.org/plugins/yet-another-related-posts-plugin/).
 *
 * @package Cardume
 * @subpackage Humus
 */
?>

<?php // query_posts() ; ?>
<?php if(have_posts()) : ?>
	<div class="container">
		<div class="related-content row">
			<div class="twelve columns">
				<h3 class="section-title"><?php _e('You might also like', 'humus'); ?></h3>
			</div>
			<div class="related-posts">
				<div class="post-list">
					<?php
					while(have_posts()) :

						the_post();
						get_template_part('content');

					endwhile;
					?>
				</div>
			</div>
			<a href="#" class="next"></a>
			<a href="#" class="prev"></a>
		</div>
	</div>
<?php endif; ?>
<?php // wp_reset_query(); ?>