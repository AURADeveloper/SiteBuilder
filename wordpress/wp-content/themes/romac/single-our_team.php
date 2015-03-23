<?php get_header(); ?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main our-team" role="main">
            <div class="team-list">
                <?php $roles = get_terms( 'ou', 'orderby=count&hide_empty=0&order=DESC' );
                foreach( $roles as $role) : ?>
                    <div class="panel blue alternate">
                        <div class="panel-heading text-center">
                            <h3><?php echo $role->name ?></h3>
                        </div>
                        <?php $args = array(
                            'post_type' => 'our_team',
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'ou',
                                    'field' => 'slug',
                                    'terms' => $role->slug
                                )
                            )
                        );
                        $query = new WP_Query( $args ); ?>
                        <ul class="panel-body text-small">
                            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                                <li><a href="<?php the_permalink(); ?>"><?php echo the_title(); ?></a></li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="aura-person-singular">
                <?php while ( have_posts() ) : the_post(); ?>
                <div class="aura-person">
                    <h1><?php echo get_post_meta( get_the_ID(), '_rot_name' )[0] ; ?><span><?php echo get_post_meta( get_the_ID(), '_rot_role')[0]; ?></span></h1>
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
        </main><!-- #content -->
    </div>
<?php get_footer(); ?>