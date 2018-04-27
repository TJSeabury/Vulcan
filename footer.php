<?php namespace Vulcan;
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package _vulcan
 */

global $vulcan;

?>

	</main>
	<footer id="colophon" class="site-footer">
		<?php if ( is_active_sidebar( 'footer' ) ) : ?>
		<div id="primary-widget-area" class="widget-area" role="complementary">
			<?php dynamic_sidebar( 'footer' ); ?>
		</div>
		<?php endif; ?>
		<div class="sub-footer">
			<div class="copyright">
				<small>© Copyright <?php echo $vulcan->get_the_copyright_years(); ?>, <?php bloginfo( 'name' ); ?>, All rights reserved.</small>
			</div>
			<div class="social-media">
				<?php /* get_social_icons() */ ?>
			</div>
			<div class="author">
				<small>Designed and developed by <a href="https://difdesign.com/" target="_blank">DIF Design</a>.</small>
			</div>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>