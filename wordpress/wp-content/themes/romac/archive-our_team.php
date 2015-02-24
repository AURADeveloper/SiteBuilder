<?php get_header(); ?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main aura-people" role="main">
            <div class="aura-people-list">
                <?php
                $roles = get_terms( 'ou', 'orderby=count&hide_empty=0&order=DESC' );
                foreach( $roles as $role) : ?>
                    <h3><?php echo $role->name ?></h3>
                    <?php
                    //
                    $args = array(
                        'post_type' => 'our_team',
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'ou',
                                'field' => 'slug',
                                'terms' => $role->slug
                            )
                        )
                    );

                    //
                    $query = new WP_Query( $args );

                    ?>
                    <ul>
                    <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                        <li><a href="<?php the_permalink(); ?>"><?php echo the_title(); ?></a></li>
                    <?php endwhile; ?>
                    </ul>
                <?php endforeach; ?>
            </div>
            <div class="aura-people-snaps">
            <?php
            $args= array(
                'order' => 'ASC',
                'orderby' => 'menu_order'
            );
            global $query_string;
            query_posts( $query_string . '&order=ASC&orderby=menu_order&posts_per_page=25' );
            while ( have_posts() ) : the_post(); ?>
                <div class="aura-person">
                    <h1><?php echo get_post_meta( get_the_ID(), '_rot_name' )[0]; ?></h1>
                    <h2><?php echo get_post_meta( get_the_ID(), '_rot_role' )[0]; ?></h2>
                    <div class="aura-person-group">
                        <?php the_post_thumbnail( 'thumbnail', array( 'class' => 'aura-person-photo' ) ); ?>
                        <div class="aura-person-links">
                            <a href="/contact/?recipient=<?php the_ID(); ?>"><i class="fa fa-envelope"></i> Email</a>
                            <a href="<?php the_permalink(); ?>"><i class="fa fa-user"></i> More</a>
                        </div>
                    </div>
                    <div class="aura-person-blurb">
                        <?php the_content( ); ?>
                    </div>
                </div>
            <?php endwhile; // end of the loop. ?>
            </div>
        </main><!-- #content -->
    </div>

<?php get_footer(); ?>