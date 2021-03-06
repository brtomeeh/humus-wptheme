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

			<div id="full-height-content">

				<div class="scroll-tip">
					<div class="container">
						<div class="twelve columns">
							<p><span><?php _e('Click to explore our content', 'humus'); ?></span><span class="button-container"><img src="<?php echo get_template_directory_uri(); ?>/img/scroll-down.png" /></span></p>
						</div>
					</div>
				</div>

				<div class="full-height-sections">

					<div class="items">

						<section id="axes-area" class="full-height-section">

							<div class="axes-bg">
								<div class="odd bg"></div>
								<div class="even bg"></div>
							</div>

							<div class="axes-content">
								<div class="gradient-bg"></div>
								<div class="container">
									<?php
									$axes = get_terms('axis', array('hide_empty' => 0));
									foreach($axes as $axis) : 
										$axis_posts = get_posts(array(
											'posts_per_page' => 4,
											'post_type' => get_post_types(array('public' => true)),
											'axis' => $axis->slug,
											'meta_query' => array(
												array(
													'key' => 'home_featured',
													'value' => 1
												),
												array(
													'key' => 'home_featured_axis',
													'value' => $axis->term_id
												)
											),
											'orderby' => 'rand'
										));
										?>
										<div class="four columns relative">
											<div class="axis-content <?php echo $axis->slug; ?>" data-axis="<?php echo $axis->slug; ?>">
												<h2><a href="<?php echo get_term_link($axis); ?>"><?php echo $axis->name; ?></a></h2>
												<?php
												if($axis_posts) :
													?>
													<ul class="axis-posts">
														<?php
														foreach($axis_posts as $post) :
															global $post;
															setup_postdata($post);
															$title = get_field('home_featured_title') ? get_field('home_featured_title') : get_the_title();
															$description = get_field('home_featured_description') ? get_field('home_featured_description') : get_the_excerpt();
															$image = get_field('home_featured_image');
															?>
															<li data-postid="<?php echo $post->ID; ?>" data-image="<?php echo $image; ?>" data-axis="<?php echo $axis->slug; ?>" class="axis-post">
																<img src="<?php echo humus_get_term_icon_url($post->ID, 'section'); ?>" class="section-icon" />
																<h3><a href="<?php the_permalink(); ?>"><?php echo $title; ?></a></h3>
																<p><a href="<?php the_permalink(); ?>"><?php echo $description; ?></a></p>
																<a class="area-link" href="<?php the_permalink(); ?>" title="<?php echo $title; ?>"></a>
															</li>
															<?php
															wp_reset_postdata();
														endforeach;
														?>
													</ul>
													<?php
												endif;
												?>
											</div>
											<div class="clearfix">&nbsp;</div>
										</div>
										<?php
									endforeach;
									?>
								</div>
							</div>
							<div class="prev axes-nav"></div>
							<div class="next axes-nav"></div>

						</section>

						<?php /* <section id="magazine-area" class="full-height-section" style="background-image: url(http://lorempixum.com/900/600/);"></section> */ ?>

						<section id="sections-area" class="full-height-section">
							<?php
							$sections = get_terms('section', array('hide_empty' => 0));
							shuffle($sections);
							$section_posts = array();
							foreach($sections as $section) {
								$section_post = get_posts(array(
									'posts_per_page' => 1,
									'post_type' => get_post_types(array('public' => true)),
									'section' => $section->slug,
									'meta_query' => array(
										array(
											'key' => 'section_featured',
											'value' => 1
										)
									)
								));
								if($section_post) {
									$section_posts[$section->slug] = array_shift($section_post);
								}
							}
							?>
							<div class="sections-content <?php if(empty($section_posts)) echo 'vertical-center'; ?>">
								<div class="container">
									<div class="six columns">
										<ul class="section-list">
											<?php foreach($sections as $section) : ?>
												<li data-termid="<?php echo $section->term_id; ?>">
													<h2>
														<a href="<?php echo get_term_link($section); ?>">
															<img src="<?php echo humus_get_term_icon_url('section_' . $section->term_id); ?>" />
															<?php echo $section->name; ?>
														</a>
													</h2>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
									<div class="six columns">
										<ul class="section-descriptions">
											<?php foreach($sections as $section) : ?>
												<li data-termid="<?php echo $section->term_id; ?>" class="clearfix">
													<?php if($section->description) : ?>
														<p class="description"><a href="<?php echo get_term_link($section); ?>"><?php echo $section->description; ?></a></p>
													<?php endif; ?>
													<p class="link"><a href="<?php echo get_term_link($section); ?>"><?php _e('View all content from this section', 'humus'); ?></a></p>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
								</div>
								<?php
								if(!empty($section_posts)) :
									global $post;
									?>
									<ul class="section-posts">
										<?php
										foreach($sections as $section) :
											if(!isset($section_posts[$section->slug]))
												continue;
											$post = $section_posts[$section->slug];
											setup_postdata($post);
											$title = get_field('section_featured_title') ? get_field('section_featured_title') : get_the_title();
											$image = get_field('section_featured_image');
											?>
											<li data-termid="<?php echo $section->term_id; ?>" data-image="<?php echo $image; ?>">
												<div class="post-container">
													<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"></a>
													<div class="container">
														<div class="twelve columns">
															<?php if(!get_field('section_featured_image_only')) : ?>
																<div class="post-content <?php echo get_field('section_featured_color'); ?>">
																	<?php
																	/*
																	<?php
																	$axis = get_the_terms($post->ID, 'axis');
																	if($axis) :
																		$axis = array_shift($axis);
																		?>
																		<h3><?php echo $axis->name; ?></h3>
																	<?php endif; ?>
																	<h2><?php echo $title; ?></h2>
																	*/
																	?>
																	<h2><?php echo $title; ?></h2>
																	<?php the_excerpt(); ?>
																</div>
															<?php endif; ?>
														</div>
													</div>
												</div>
											</li>
											<?php
											wp_reset_postdata();
										endforeach;
										?>
									</ul>
								<?php endif; ?>
							</div>

						</section>

						<?php
						$recent = get_posts(array(
							'posts_per_page' => 10
						));
						if($recent) : 
							?>
							<section id="recent-area" class="full-height-section">
								<div class="recent-content vertical-center">
									<div class="container">
										<div class="twelve columns">
											<h2><?php _e('Recent posts', 'humus'); ?></h2>
										</div>
										<div class="nine columns">
											<div class="active clearfix">
												<div class="active-container">
												</div>
												<div class="active-info"></div>
												<div class="active-links">
													<a href="#" class="read-more"><?php _e('Read more on this content', 'humus'); ?></a>
												</div>
											</div>
										</div>
										<div class="three columns">
											<div class="item-list">
												<ul class="items">
													<?php
													foreach($recent as $post) :
														global $post;
														setup_postdata($post);
														if(!has_post_thumbnail())
															continue;
														$section_icon = humus_get_term_icon_url($post->ID, 'section');
														?>
														<li data-postid="<?php the_ID(); ?>">
															<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
																<?php if($section_icon) : ?>
																	<img src="<?php echo $section_icon; ?>" class="section-icon" />
																<?php endif; ?>
																<div class="post-thumbnail">
																	<a href="<?php the_permalink(); ?>">
																		<?php the_post_thumbnail('humus-wide-thumbnail', array('class' => 'scale-with-grid')); ?>
																	</a>
																</div>
																<header class="post-header">
																	<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
																	<?php the_excerpt(); ?>
																</header>
															</article>
															<script>
																<?php
																$media = get_post_meta($post->ID, "media_oembed", true);
																if(!$media)
																	$media = '<a href="' . get_permalink() . '">' . get_the_post_thumbnail($post->ID, 'humus-wide-medium') . '</a>';
																?>
																jQuery(document).ready(function($) {
																	$('#recent-area li[data-postid="<?php the_ID(); ?>"]')
																		.data('embed', '<?php echo $media; ?>');
																});
															</script>
														</li>
														<?php
														wp_reset_postdata();
													endforeach;
													?>
												</ul>
												<script>
													jQuery(document).ready(function($) {
														$('#recent-area').trigger('dataReady');
													});
												</script>
											</div>
											<div class="list-controls">
												<a href="#" class="prev"></a>
												<a href="#" class="next"></a>
											</div>
										</div>
									</div>
								</div>
							</section>
						<?php
						endif;
						?>

						<?php 
						$ad = humus_get_ad(array('name' => 'Home'));
						if($ad) : ?>
							<section class="ad-section full-height-section">
								<div class="vertical-center">
									<?php humus_ad(array('name' => 'Home')); ?>
								</div>
							</section>
							<?php
						endif;
						?>

						<section class="footer-section full-height-section" style="overflow: auto;">
							<?php get_template_part('section', 'colophon'); ?>
						</section>

					</div><!-- .items -->

				</div><!-- .full-height-sections -->

			</div><!-- #full-height-content -->

		</div><!-- #content -->
	</div><!-- #primary -->

	</div><!-- #main -->

	</div><!-- #page -->

	<?php wp_footer(); ?>
</body>
</html>
