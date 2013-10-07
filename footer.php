<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package Cardume
 * @subpackage Humus
 */
?>

		</div><!-- #main -->

		<footer id="colophon" class="site-footer" role="contentinfo">
			<div class="container">
				<div class="four columns">
					<?php
					$partners = get_posts(array(
						'post_type' => 'partner',
						'posts_per_page' => -1,
						'orderby' => 'rand'
					));
					if($partners) :
						?>
						<div class="partners row">
							<h3><?php _e('Partners', 'humus'); ?></h3>
							<div class="partner-list">
								<ul>
									<?php
									foreach($partners as $partner) :
										$image = get_field('partner_logo', $partner->ID);
										if(!$image)
											continue;
										?>
										<li><a href="<?php echo get_permalink($partner->ID); ?>" title="<?php echo get_the_title($partner->ID); ?>"><img src="<?php echo $image['sizes']['thumbnail']; ?>" alt="<?php echo get_the_title($partner->ID); ?>" /></a></li>
									<?php
								endforeach;
								?>
								</ul>
							</div>
							<div class="navigate">
								<a href="#" class="prev-partner"><?php _e('Previous partner', 'humus'); ?></a>
								<a href="#" class="next-partner"><?php _e('Next partner', 'humus'); ?></a>
							</div>
						</div>
						<script type="text/javascript">
							(function($) {

								var sly;

								$(document).ready(function() {

									sly = new Sly('.partner-list', {
										horizontal: 1,
										itemNav: 'basic',
										smart: 1,
										startAt: 0,
										scrollBy: 1,
										speed: 200,
										ease: 'easeOutExpo',
										next: $('.partners .next-partner'),
										prev: $('.partners .prev-partner')
									});

									sly.init();

								});

							})(jQuery);
						</script>
						<?php
					endif;
					?>
					<div class="social row">
						<h3><?php _e('Around the web', 'humus'); ?></h3>
						<div class="social-content">
							<div class="social-icons">
								<?php
								$facebook = get_field('facebook_url', 'option');
								if($facebook)
									echo '<a href="' . $facebook . '" title="Facebook" target="_blank">facebook</a>';
								$twitter = get_field('twitter_url', 'option');
								if($twitter)
									echo '<a href="' . $twitter . '" title="Twitter" target="_blank">twitter</a>';
								$youtube = get_field('youtube_url', 'option');
								if($youtube)
									echo '<a href="' . $youtube . '" title="YouTube" target="_blank">youtube</a>';
								$instagram = get_field('instagram_url', 'option');
								if($instagram)
									echo '<a href="' . $instagram . '" title="Instagram" target="_blank">instagram</a>';
								$gplus = get_field('gplus_url', 'option');
								if($gplus)
									echo '<a href="' . $gplus . '" title="Google Plus" target="_blank">google</a>';
								$pinterest = get_field('pinterest_url', 'option');
								if($pinterest)
									echo '<a href="' . $pinterest . '" title="Pinterest" target="_blank">pinterest</a>';
								?>
							</div>
						</div>
					</div>
				</div>
				<div class="four columns">
					<div class="newsletter row">
						<h3><?php _e('Newsletter', 'humus'); ?></h3>
						<div class="newsletter-container">
							<p><?php _e('Sign up to our newsletter and be the first to know about our latest contents', 'humus'); ?></p>
							<?php humus_newsletter_form(); ?>
						</div>
					</div>
				</div>
				<div class="four columns">
					<?php
					$contact = get_field('footer_contact', 'option');
					if($contact) :
						?>
						<div class="contacts row">
							<h3><?php _e('Contact', 'humus'); ?></h3>
							<p><?php echo $contact; ?></p>
						</div>
						<?php
					endif;
					?>
					<div class="colophon row">
						<h3><?php _e('Colophon', 'humus'); ?></h3>
						<table class="colophon-table">
							<tr>
								<td><?php _e('Typography', 'humus'); ?></td>
								<td><a href="http://www.latofonts.com/" target="_blank">Lato</a>, <a href="http://www.google.com/fonts/specimen/Open+Sans" target="_blank">Open Sans</a></td>
							</tr>
							<tr>
								<td><?php _e('Some icons by', 'humus'); ?></td>
								<td><a href="http://www.entypo.com/" target="_blank">Entypo</a>, <a href="http://kudakurage.com/ligature_symbols/" target="_blank">Ligature Symbols</a></td>
							</tr>
							<tr>
								<td><?php _e('License', 'humus'); ?></td>
								<td><a href="http://creativecommons.org/licenses/by-sa/3.0/deed.pt" target="_blank">CC BY - SA 3.0</a></td>
							</tr>
							<tr>
								<td><?php _e('Development', 'humus'); ?></td>
								<td class="logos"><a href="http://espacohumus.com/" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/img/humus.png" /></a> - <a href="http://cardume.art.br/" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/img/cardume.png" /></a></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div class="site-info">
				<?php do_action( 'humus_credits' ); ?>
			</div><!-- .site-info -->
		</footer><!-- #colophon -->
	</div><!-- #page -->

	<?php wp_footer(); ?>
</body>
</html>