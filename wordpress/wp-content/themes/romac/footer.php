<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package romac
 */
?>

	</div><!-- #content -->
	<footer id="colophon" class="site-footer" role="contentinfo">
        <div id="sponsorship-area" class="sponsorship-area">
            <div class="sponsorship-title-container">
                <div class="sponsorship-title">
                    <h3>Our Sponsors</h3>
                </div>
            </div>
            <div class="sponsorship-marquee-container">
                <div class="sponsorship-marquee">
                    <span class="sponsorship-marquee-slider">
                        <a href="http://www.coopers.com.au/" target="_blank">
                            <img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/sponsors/coopers.png" alt="Coopers" />
                        </a>
                        <a href="http://www.perpetual.com.au/" target="_blank">
                            <img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/sponsors/perpetual.png" />
                        </a>
                        <a href="http://foundation.rch.org.au/" target="_blank">
                            <img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/sponsors/royal_children.png" />
                        </a>
                        <a href="http://www.tswf.com.au/" target="_blank">
                            <img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/sponsors/shane_warne.png" />
                        </a>
                        <a href="http://www.eurekafm.com.au/" target="_blank">
                            <img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/sponsors/eureka.png" />
                        </a>
                        <a href="http://www.conocophillips.com.au/" target="_blank">
                            <img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/sponsors/conoco.png" />
                        </a>
                        <a href="http://www.emeraldpress.com.au/" target="_blank">
                            <img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/sponsors/emerald.png" />
                        </a>
                        <a href="http://www.jnjmedical.com.au/" target="_blank">
                            <img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/sponsors/johnson.png" />
                        </a>
                        <a href="http://www.westonprint.com.au/" target="_blank">
                            <img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/sponsors/weston.png" />
                        </a>
                        <a href="http://www.flysolomons.com/" target="_blank">
                            <img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/sponsors/solomons.png" />
                        </a>
                        <a href="http://www.regionalimaging.com.au/" target="_blank">
                            <img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/sponsors/regional.png" />
                        </a>
                    </span>
                </div>
            </div>
        </div>
        <?php if ( is_active_sidebar( 'footer-widgets' ) ) : ?>
            <div id="footer-widgets" class="widget-area" role="contentinfo">
                <?php dynamic_sidebar( 'footer-widgets' ); ?>
            </div><!-- #footer-sidebar -->
        <?php endif; ?>
		<div class="site-info-area">
            <span class="links">
                <a href="<?php echo esc_url( __( '#' ) ); ?>">Contact us</a> |
                <a href="#">Privacy &amp; Legal</a> |
                <a href="#">Site Map</a>
            </span>
            <span class="copyright">
                &copy; 2015 Rotary Oceania Medical Aid for Children Ltd. (ROMAC Ltd.)
            </span>
            <hr>
            <img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/logo-rotary.jpg" alt="Rotary | ROMAC" class="logo">
            <span class="legal">Standard information about ROMAC's ATO charity status and text exempt info.</span>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
