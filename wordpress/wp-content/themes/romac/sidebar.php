<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package romac
 */
$sidebar = get_post_meta( get_the_ID(), "show_sidebar", true );
if ( ! $sidebar ) {
    return;
}
if ( ! is_active_sidebar( $sidebar ) ) {
    return;
}
?>

<div id="secondary" class="widget-area" role="complementary">
	<?php dynamic_sidebar( $sidebar ); ?>
</div><!-- #secondary -->
