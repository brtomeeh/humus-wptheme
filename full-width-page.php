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

				do_action('humus_before_page_content');	$header_image = humus_get_header_image_url();

				$media = get_post_meta($post->ID, 'media_oembed', true);

				$content_columns = 'ten columns';
				$media_columns = 'ten columns';

				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class('full row'); ?>>
					<header class="page-header post-header <?php if($header_image) echo 'header-image'; ?>" <?php if($header_image) echo 'style="background-image:url(' . $header_image . ')"'; ?>>
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
							<?php if($media) : ?>
								<div class="row">
									<?php if(get_post_type() == 'partner') : ?>
										<div class="one column">&nbsp;</div>
									<?php endif; ?>
									<div class="<?php echo $media_columns; ?>">
										<section id="post-media">
											<?php
											do_action('humus_before_post_media');
											echo $media;
											do_action('humus_after_post_media');
											?>
										</section>
									</div>
								</div>
							<?php endif; ?>
							<div class="one column">&nbsp;</div>
							<div class="<?php echo $content_columns; ?>">
								<section id="post-content" class="post-content">
									<?php the_content(); ?>
									<?php the_tags('<p class="tags"><span class="label">' . __('Tags', 'humus') . '</span>', ', ', '</p>'); ?>
								</section>
							</div>
						</div>
					</section>
				</article>
				<?php

				do_action('humus_after_page_content');

				// If comments are open or we have at least one comment, load up the comment template.
				if (comments_open() || get_comments_number()) {

					do_action('humus_before_single_post_comments');

					comments_template();

					do_action('humus_after_single_post_comments');
					
				}

			endwhile;
		?>
	</div><!-- #content -->
</div><!-- #primary -->

<?php
get_footer();