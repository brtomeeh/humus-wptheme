<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each specific one. For example, Twenty Fourteen
 * already has tag.php for Tag archives, category.php for Category archives,
 * and author.php for Author archives.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Cardume
 * @subpackage Humus
 */

get_header(); ?>

<section id="primary" class="content-area">
	<div id="content" class="site-content" role="main">

		<?php

		$header_image = humus_get_header_image_url();
		$section_icon = humus_get_term_icon_url();

		?>
		<header class="page-header <?php if($header_image) echo 'header-image'; ?>" <?php if($header_image) echo 'style="background-image:url(' . $header_image . ')"'; ?>>

			<div class="header-content">

				<?php do_action('humus_before_header_content'); ?>

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

						<h1 class="page-title">

							<?php

							if(is_day()) :
								printf( __( 'Day: %s', 'twentyfourteen' ), get_the_date() );

							elseif(is_month()) :
								printf( __( 'Month: %s', 'twentyfourteen' ), get_the_date( 'F Y' ) );

							elseif(is_year()) :
								printf( __( 'Year: %s', 'twentyfourteen' ), get_the_date( 'Y' ) );

							elseif(is_tax() || is_tag() || is_category()) :
								single_term_title();

							else :
								_e( 'Archives', 'twentyfourteen' );

							endif;
							?>

						</h1>

						<?php
						if(is_tax() || is_tax() || is_category()) :

								$description = term_description();
								if($description) :
									echo $description;

								endif;

						endif;
						?>

					</div>

				</div>

				<?php do_action('humus_after_header_content'); ?>

			</div>

		</header><!-- .page-header -->

		<section class="page-content">

			<?php

			do_action('humus_before_archive_posts');

			if(have_posts()) :

				?>
				<div class="container">
					<?php

					while ( have_posts() ) :

						the_post();

						get_template_part('content');

					endwhile;
					?>
				</div>
				<?php

			else :



			endif;

			do_action('humus_after_archive_posts');

			?>

		</section>

	</div><!-- #content -->
</section><!-- #primary -->

<?php
get_footer();