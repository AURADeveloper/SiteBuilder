<?php get_header(); ?>
<div id="content" role="main" class="full-width aura-patron-list">
    <?php while ( have_posts() ) : the_post(); ?>
        <div class="aura-patron">
            <div>
                <?php the_post_thumbnail( 'thumbnail', array( 'class' => 'aura-patron-photo' ) ); ?>
            </div>
            <div>
                <?php $term_list = wp_get_post_terms( $post->ID, 'designation', array( 'fields' => 'names' ) ); ?>
                <h1 class="title"><?php the_title(); ?><span><?php echo count( $term_list ) ? $term_list[0] : '<!-- no term assigned! -->'; ?></span></h1>
                <div class="text-justify"><?php the_content(); ?></div>
            </div>
        </div>
    <?php endwhile; ?>
</div><!-- #content -->
<?php get_footer(); ?>