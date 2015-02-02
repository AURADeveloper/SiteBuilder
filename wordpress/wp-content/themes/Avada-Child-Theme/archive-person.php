<?php get_header(); ?>

<div id="content" role="main" class="full-width">
    <div class="fusion-one-fourth one_fourth fusion-layout-column fusion-column spacing-no">
        <ul>
        <?php while ( have_posts() ) : the_post(); ?>
            <li><?php the_field('persons_name'); ?></li>
        <?php endwhile; ?>
        </ul>
    </div>
    <div class="fusion-three-fourth three_fourth fusion-layout-column fusion-column last spacing-no">
        <?php
            // the number of profiles per row
            $cols_per_row = 3;

            // track current person
            $i = 0;
        ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <?php

            ?>
            <h1><?php the_field('persons_name'); ?></h1>
            <h2><?php the_field('persons_job_role_title'); ?></h2>
            <div>
                <img src="<?php the_post_thumbnail(); ?>" />
                <button>Email</button>
                <button>More</button>
            </div>
            <div>
                <?php the_content(); ?>
            </div>
            <?php $i++; ?>
        <?php endwhile; // end of the loop. ?>
    </div>
</div><!-- #content -->

<?php get_footer(); ?>