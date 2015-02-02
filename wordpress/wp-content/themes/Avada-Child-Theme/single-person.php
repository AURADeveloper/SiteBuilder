<?php get_header(); ?>

<div id="content" role="main">

    <?php while ( have_posts() ) : the_post(); ?>

        <h1><?php the_field('persons_name'); ?></h1>
        <h2><?php the_field('persons_job_role_title'); ?></h2>

        <img src="<?php the_post_thumbnail(); ?>" />

        <p><?php the_content(); ?></p>

    <?php endwhile; // end of the loop. ?>

</div><!-- #content -->

<?php get_footer(); ?>