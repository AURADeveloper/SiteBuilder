<?php get_header(); ?>

<div id="content" role="main" class="full-width">
    <div class="fusion-one-fourth one_fourth fusion-layout-column fusion-column spacing-no aura-people-list">
        <?php
        $args = array( 'post_type' => 'person' );
        $the_query = new WP_Query( $args );
        if ( $the_query->have_posts() ): ?>
            <ul>
            <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
                <li>
                    <a href="<?php the_permalink(); ?>"><?php the_field('persons_name'); ?></a>
                </li>
            <?php endwhile; ?>
            </ul>
        <?php endif; ?>
    </div>
    <div class="fusion-three-fourth three_fourth fusion-layout-column fusion-column last spacing-no aura-person">
        <?php while ( have_posts() ) : the_post(); ?>
            <div class="aura-person-group">
                <div>
                    <?php the_post_thumbnail( '', array( 'class' => 'aura-person-photo' ) ); ?>
                </div>
                <div>
                    <h1><?php the_field('persons_name'); ?></h1>
                    <h2><?php the_field('persons_job_role_title'); ?></h2>
                    <?php the_content(); ?>
                </div>
            </div>
        <?php endwhile; // end of the loop. ?>
    </div>
</div><!-- #content -->

<?php get_footer(); ?>