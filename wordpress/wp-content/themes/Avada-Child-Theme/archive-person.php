<?php get_header(); ?>

<div id="content" role="main" class="full-width">
    <div class="fusion-one-fourth one_fourth fusion-layout-column fusion-column spacing-no aura-people-list">
        <ul>
        <?php while ( have_posts() ) : the_post(); ?>
            <li><a href="<?php the_permalink(); ?>"><?php the_field('persons_name'); ?></a></li>
        <?php endwhile; ?>
        </ul>
    </div>
    <div class="fusion-three-fourth three_fourth fusion-layout-column fusion-column last spacing-no aura-people-snaps">
        <?php
            // the number of profiles per row
            $cols_per_row = 3;
            // the number of people to render
            $num_people = wp_count_posts();

            // track current person
            $i = 0;
        ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <?php
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
            ?>
            <div class="<?php echo $css; ?>">
                <h1><?php the_field( 'persons_name' ); ?></h1>
                <h2><?php the_field( 'persons_job_role_title' ); ?></h2>
                <div class="aura-person-group">
                    <?php the_post_thumbnail( 'thumbnail', array( 'class' => 'aura-person-photo' ) ); ?>
                    <div class="aura-person-links">
                        <a>Email</a>
                        <a href="<?php the_permalink(); ?>">More</a>
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