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
		<div class="row">
			<div class="twelve columns">
				<h3 class="section-title"><?php _e('See also', 'humus'); ?></h3>
			</div>
			<?php
			while(have_posts()) :

				the_post();
				get_template_part('content');

			endwhile;
			?>
		</div>
	</div>
<?php endif; ?>
<?php // wp_reset_query(); ?>