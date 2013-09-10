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

		<footer id="colophon" class="site-footer" role="contentinfo" style="height:900px;">
			<div class="site-info">
				<?php do_action( 'humus_credits' ); ?>
			</div><!-- .site-info -->
		</footer><!-- #colophon -->
	</div><!-- #page -->

	<?php wp_footer(); ?>
</body>
</html>