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
    <footer id="romac-sponsors" class="site-footer sponsorship-area" role="presentation">
        <div class="marquee">
            <h3>Our Sponsors</h3>
            <hr>
            <span>
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
    </footer>
<?php if ( is_active_sidebar( 'footer-widgets' ) ) : ?>
    <footer id="footer-widgets" class="site-footer widget-area" role="contentinfo">
        <?php dynamic_sidebar( 'footer-widgets' ); ?>
    </footer><!-- #footer-sidebar -->
<?php endif; ?>
	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="site-info">
            <span class="links">
                <a href="<?php echo esc_url( __( '#' ) ); ?>">Contact us</a> |
                <a href="#">Privacy &amp; Legal</a> |
                <a href="#">Site Map</a>
            </span>
            <span class="copyright">
                Copyright 2015 Rotary Oceania Medical Aid for Children Ltd. (ROMAC Ltd.) All rights reserved.
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
