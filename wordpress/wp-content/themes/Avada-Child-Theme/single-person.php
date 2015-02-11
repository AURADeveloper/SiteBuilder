<?php get_header(); ?>

<div id="content" role="main" class="full-width">
    <div class="fusion-one-fifth one_fifth fusion-layout-column fusion-column spacing-no aura-people-list">
        <?php
        $roles = get_terms( 'roles', 'orderby=count&hide_empty=0&order=DESC' );
        foreach( $roles as $role) : ?>
            <h3><?php echo $role->name ?></h3>
            <?php
            //
            $args = array(
                'post_type' => 'person',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'roles',
                        'field' => 'slug',
                        'terms' => $role->slug
                    )
                ),
            );

            //
            $query = new WP_Query( $args ); ?>
            <ul>
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <li><a href="<?php the_permalink(); ?>"><?php echo the_title(); ?></a></li>
            <?php endwhile; ?>
            </ul>
        <?php endforeach; ?>
    </div>
    <div class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last spacing-no aura-person-singular">
        <?php while ( have_posts() ) : the_post(); ?>
        <div class="aura-person">
            <h1><?php the_field('persons_name'); ?><span><?php the_field('persons_job_role_title'); ?></span></h1>
            <div class="aura-person-profile">
                <div>
                    <?php the_post_thumbnail( '', array( 'class' => 'aura-person-photo' ) ); ?>
                    <div class="aura-person-links">
                        <a href="/contact?recipient=<?php the_ID(); ?>"><i class="fa fa-envelope"></i> Email</a>
                    </div>
                </div>
                <div>
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
        <?php endwhile; // end of the loop. ?>
    </div>
</div><!-- #content -->

<?php get_footer(); ?>