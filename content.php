<?php

/**
 * @package Cardume
 * @subpackage Humus
 */

$section_icon = humus_get_term_icon_url($post->ID, 'section');

if(is_single()) :

	$header_image = humus_get_header_image_url();
	$media = get_post_meta($post->ID, 'media_oembed', true);

	$content_columns = 'nine columns';
	$media_columns = 'twelve columns';
	if(get_post_type() == 'partner') {
		$content_columns = 'ten columns';
		$media_columns = 'ten columns';
	}

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
				<?php
				$show_media = false
				if($media && $show_media) :
					?>
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
				<?php if(get_post_type() !== 'partner') : ?>
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
										<div class="post-author">
											<p>
												<span><?php _e('by', 'humus'); ?></span>
												<span class="meta-content"><?php the_author(); ?></span>
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
									<?php else : ?>
										<div class="post-date">
											<p>
												<span><?php _e('published', 'humus'); ?></span>
												<span class="meta-content"><?php the_date(); ?></span>
											</p>
										</div>
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
					<div class="three columns">
						<div class="row">
							<aside id="post-terms">
								<?php do_action('humus_before_single_post_meta'); ?>
								<?php echo humus_get_post_tax_label(); ?>
								<?php the_tags('<p class="tags"><span class="label">' . __('Tags', 'humus') . '</span>', ', ', '</p>'); ?>
								<?php do_action('humus_after_single_post_meta'); ?>
							</aside>
						</div>
					</div>
				<?php else : ?>
					<div class="one column">&nbsp;</div>
				<?php endif; ?>
				<div class="<?php echo $content_columns; ?>">
					<div class="row">
						<section id="post-content" class="post-content">
							<?php the_content(); ?>
						</section>
					</div>
				</div>
			</div>
		</section>
	</article>
	<?php

elseif(humus_is_template('minimal')) :

	?>

	<div class="four columns">
		<article id="post-<?php the_ID(); ?>" <?php post_class('minimal'); ?>>
			<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="cover-link"></a>
			<?php if(has_post_thumbnail()) : ?>
				<div class="post-thumbnail">
					<?php the_post_thumbnail('humus-wide-thumbnail', array('class' => 'scale-with-grid')); ?>
				</div>
			<?php endif; ?>
			<header class="post-header">
				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><img src="<?php echo $section_icon; ?>" class="icon" /></a>
				<h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
			</header>
			<section class="post-content">
				<?php the_excerpt(); ?>
			</section>
		</article>
	</div>

	<?php

else :

	if(is_tax('section', 'albuns')) :

		?>

		<div class="three columns">
			<article id="post-<?php the_ID(); ?>" <?php post_class('list'); ?>>
				<?php if(has_post_thumbnail()) : ?>
					<div class="post-thumbnail">
						<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_post_thumbnail('humus-thumbnail', array('class' => 'scale-with-grid')); ?></a>
					</div>
				<?php endif; ?>
				<h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<p class="image-count"><?php
				$image_count = humus_get_image_count();
				printf(_n('%d image', '%d images', $image_count, 'humus'), $image_count);
				?></p>
			</article>
		</div>

	<?php else : ?>

		<div class="six columns">
			<article id="post-<?php the_ID(); ?>" <?php post_class('list'); ?>>
				<?php if(has_post_thumbnail()) : ?>
					<div class="three columns alpha">
						<div class="post-thumbnail">
							<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_post_thumbnail('humus-thumbnail', array('class' => 'scale-with-grid')); ?></a>
						</div>
					</div>
				<?php endif; ?>
				<div class="three columns omega">
					<header class="post-header">
						<?php
						$category = get_the_terms($post->ID, 'category');
						if($category) : 
							$category = array_shift($category);
							?>
							<h3 class="category"><a href="<?php echo get_term_link($category); ?>"><?php echo $category->name; ?></a></h3>
						<?php endif; ?>
						<?php do_action('humus_list_article_before_title'); ?>
						<h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
						<p class="author"><?php _e('by', 'humus'); ?> <span><?php the_author(); ?></span></p>
					</header>
					<section class="post-content">
						<?php the_excerpt(); ?>
					</section>
					<footer class="post-meta">
						<?php
						$footer = apply_filters('humus_list_article_footer', '');
						if($footer === '') {
							the_tags('<p class="tags">', ', ', '</p>');
							echo '<p class="date">' . get_the_date() . '</p>';
						} else {
							echo $footer;
						}
						?>
					</footer>
				</div>
			</article>
		</div>

	<?php
	endif;

endif;
?>