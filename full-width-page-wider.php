<?php
/**
 * Template Name: Full Width Page with wider content
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
							<div class="twelve columns">
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