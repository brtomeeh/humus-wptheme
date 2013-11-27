<?php
/**
 * Template Name: Full Width Page
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

				if(is_page())
					do_action('humus_before_page_content');

				if(is_single())
					do_action('humus_before_single_post');

				$header_image = humus_get_header_image_url();
				$section_icon = humus_get_term_icon_url($post->ID, 'section');

				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class('full row'); ?>>
					<header class="page-header post-header <?php if($header_image) echo 'header-image'; ?>" <?php if($header_image) echo 'style="background-image:url(' . $header_image . ')"'; ?>>
                        <div class="gradient"></div>
						<?php do_action('humus_before_header_content'); ?>
						<div class="header-content">
							<div class="container">
								<div class="one column">
									<?php if($section_icon) : ?>
										<img src="<?php echo $section_icon; ?>" alt="<?php single_term_title(); ?>" />
									<?php else : ?>
										&nbsp;
									<?php endif; ?>
								</div>

								<div class="ten columns">
									<?php humus_breadcrumb(); ?>
									<h1 class="page-title"><?php the_title(); ?></h1>
								</div>

							</div>
						</div>
						<?php do_action('humus_after_header_content'); ?>
					</header>
					<section class="page-content">
						<div class="container">
							<?php if(is_single()) : ?>
								<div class="twelve columns">
									<div class="row">
										<aside id="post-meta">
											<div class="three columns alpha">
												<?php if(get_post_type() == 'event'	) : ?>
													<div class="event-date">
														<p>
															<span><?php _e('When', 'humus'); ?></span>
															<span class="meta-content"><?php echo humus_get_event_date(); ?></span>
														</p>
													</div>
												<?php else : ?>
													<div class="post-date">
														<p>
															<span><?php _e('published', 'humus'); ?></span>
															<span class="meta-content"><?php the_date(); ?></span>
														</p>
													</div>
												<?php endif; ?>
											</div>
											<div class="five columns">
												<?php if(get_post_type() == 'event') : ?>
													<div class="event-location">
														<p>
															<span><?php _e('Where', 'humus'); ?></span>
															<span class="meta-content"><?php echo humus_get_event_location(); ?></span>
														</p>
													</div>
												<?php elseif($display_author) : ?>
													<div class="post-author">
														<p>
															<span><?php _e('by', 'humus'); ?></span>
															<span class="meta-content"><?php the_author(); ?></span>
														</p>
													</div>
												<?php else : ?>
													&nbsp;
												<?php endif; ?>
											</div>
											<div class="four columns omega">
												<div class="share">
													<ul>
														<li>
															<div class="fb-like" data-href="<?php the_permalink(); ?>" data-layout="box_count" data-show-faces="false" data-send="false"></div>
														</li>
														<li>
															<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php the_permalink(); ?>" data-lang="en" data-count="vertical">Tweet</a>
														</li>
														<li>
															<div class="g-plusone" data-size="tall" data-href="<?php the_permalink(); ?>"></div>
														</li>
													</ul>
												</div>
											</div>
											<div class="clearfix"></div>
										</aside>
									</div>
								</div>
							<?php endif; ?>
							<div class="one column">&nbsp;</div>
							<div class="ten columns">
								<section id="post-content" class="post-content">
									<?php the_content(); ?>
									<?php the_tags('<p class="tags"><span class="label">' . __('Tags', 'humus') . '</span>', ', ', '</p>'); ?>
								</section>
							</div>
						</div>
					</section>
					<?php
					if(is_page())
						do_action('humus_after_page_content');

					if(is_single()) {
						do_action('humus_after_single_post');
					}
					?>
				</article>
				<?php

				// If comments are open or we have at least one comment, load up the comment template.
				if (comments_open() || get_comments_number()) {

					do_action('humus_before_single_post_comments');

					?>
					<div class="container">
						<div class="twelve columns row">
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
