<?php
// Template Name: Sitemap
get_header(); ?>
    <div id="content" class="full-width">
        <div class="post-content">
            <div class="fusion-fullwidth fullwidth-box iqo-box">
                <div class="avada-row">
                    <?php the_widget( 'WP_Widget_Pages', '', 'before_title=<div style="display:none">&after_title=</div>' ); ?>
                </div>
            </div>
        </div>
    </div>
<?php get_footer(); ?>