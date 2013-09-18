<?php get_header(); ?>

<section id="primary" class="content-area">
	<div id="content" class="site-content" role="main">
		<?php humus_map(); ?>
		<div class="container">
			<div class="four columns">
				<div class="post-list">
					<div id="term-description" class="navigation-item">
						<p><?php echo term_description(); ?></p>
					</div>
					<?php while(have_posts()) : the_post(); ?>
						<article id="post-<?php the_ID(); ?>" <?php post_class('navigation-item'); ?>>
							<h2><?php the_title(); ?></h2>
							<p><?php the_excerpt(); ?></p>
						</article>
					<?php endwhile; ?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php if($GLOBALS['humus_page_color']) : ?>
	<style>
		#content {
			background: <?php echo $GLOBALS['humus_page_color']; ?>;
		}
	</style>
<?php endif; ?>

<?php get_footer(); ?>