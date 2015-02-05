<?php get_header(); ?>

<div id="content" role="main" class="full-width">
    <div class="fusion-one-fifth one_fifth fusion-layout-column fusion-column spacing-no aura-people-list">
        <?php
        $roles = get_terms( 'roles' );
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
                )
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
    <div class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last spacing-no aura-people-snaps">
        <?php
            // the number of profiles per row
            $cols_per_row = 3;
            // the number of people to render
            $num_people = wp_count_posts();

            // track current person
            $i = 0;
        ?>
        <?php
        $args= array(
            'order' => 'ASC',
            'orderby' => 'menu_order'
        );
        global $query_string;
        query_posts( $query_string . '&order=ASC&orderby=menu_order&posts_per_page=25' );
        while ( have_posts() ) : the_post(); ?>
            <?php
            $first_in_row = ($i % $cols_per_row == 0);
            $last_in_row = ($i % $cols_per_row == ($cols_per_row-1));
            $last_person = $i == $num_people-1;
            $empty_cols = $cols_per_row - ($i+1 % $cols_per_row);

            // debug
            $last_person = false;

            // define the column width
            if ( $last_person ) {
                switch( $empty_cols ) {
                    case 1:
                        $css = "fusion-one-third one_third";
                        break;
                    case 2:
                        $css = "fusion-two-third two_third";
                        break;
                }

                // last person means this is the last person in the row
                $last_in_row = true;
            } else {
                $css = "fusion-one-third one_third";
            }

            // assign standard column classes
            $css .= " fusion-layout-column fusion-column spacing-yes aura-person";

            // if last in row, assign the special last class
            if ( $last_in_row ) {
                $css .= " last";
            }

            if ( $first_in_row ) {
                $css .= " fusion-clearfix";
            }
            ?>
            <div class="<?php echo $css; ?>">
                <h1><?php the_field( 'persons_name' ); ?></h1>
                <h2><?php the_field( 'persons_job_role_title' ); ?></h2>
                <div class="aura-person-group">
                    <?php the_post_thumbnail( 'thumbnail', array( 'class' => 'aura-person-photo' ) ); ?>
                    <div class="aura-person-links">
                        <a href="#"><i class="fa fa-envelope"></i> Email</a>
                        <a href="<?php the_permalink(); ?>"><i class="fa fa-user"></i> More</a>
                    </div>
                </div>
                <div class="aura-person-blurb">
                    <?php the_content(); ?>
                </div>
            </div>
            <?php $i++; ?>
        <?php endwhile; // end of the loop. ?>
    </div>
</div><!-- #content -->

<?php get_footer(); ?>