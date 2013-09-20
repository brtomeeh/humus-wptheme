<?php get_header(); ?>

<section id="primary" class="content-area">
	<div id="content" class="site-content" role="main">
		<?php humus_map(); ?>
		<?php humus_archive_header(true); ?>
		<div class="map-content">
			<div class="container">
				<div class="four columns">
					<div class="post-list">
						<div id="page-description" class="navigation-item">
							<div class="description"><?php echo term_description(); ?></div>
							<div class="scroll-tip">
								<p><?php _e('Scroll to explore our content', 'humus'); ?></p>
							</div>
						</div>
						<?php
						$locations = get_terms('location');
						if($locations) :
							?>
							<div class="locations">
								<?php foreach($locations as $location) : ?>
									<article id="location-<?php echo $location->term_id; ?>" class="navigation-item location" data-location="<?php echo $location->slug; ?>">
										<h2 class="page-color"><?php echo $location->name; ?></h2>
										<?php if($location->description) : ?>
											<p><?php echo $location->description; ?></p>
										<?php endif; ?>
										<a class="button this page-background" href="#"><?php _e('View from this city', 'humus'); ?></a>
										<a class="button location-list page-background" href="#"><?php _e('Back to city list', 'humus'); ?></a>
									</article>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						<div class="posts">
							<?php while(have_posts()) :
								the_post();
								global $post;
								$location = get_the_terms($post->ID, 'location');
								if($location) {
									$location = array_shift($location);
								} elseif($locations) {
									continue;
								}
								?>
								<article id="post-<?php the_ID(); ?>" <?php post_class('navigation-item post page-scrollbar-thumb'); ?> data-postid="<?php the_ID(); ?>" data-location="<?php echo $location->slug; ?>">
									<a class="close-post" href="#">x</a>
									<p class="date page-color"><?php echo get_the_date(); ?></p>
									<h2 class="page-color"><?php the_title(); ?></h2>
									<div class="post-excerpt">
										<?php the_excerpt(); ?>
									</div>
									<div class="post-content">
										<?php the_content(); ?>
									</div>
									<?php
									$video = get_post_meta($post->ID, 'media_oembed', true);
									if($video) :
										?>
										<div class="video">
											<?php echo $video; ?>
										</div>
									<?php endif; ?>
									<a class="button page-background this" href="#"><?php _e('View', 'humus'); ?></a>
								</article>
							<?php endwhile; ?>
						</div>
					</div>
					<?php if($locations) : ?>
						<a class="button location-list page-color fixed-button" href="#"><?php _e('Back to city list', 'humus'); ?></a>
					<?php endif; ?>
				</div>
				<div class="eight columns">
					<div id="media"></div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php if($GLOBALS['humus_page_color']) : ?>
	<style>
		#content {
			background: <?php // echo $GLOBALS['humus_page_color']; ?>;
		}
	</style>
<?php endif; ?>

<?php get_footer(); ?>