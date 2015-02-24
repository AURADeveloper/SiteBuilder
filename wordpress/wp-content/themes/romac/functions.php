<?php
/**
 * romac functions and definitions
 *
 * @package romac
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

if ( ! function_exists( 'romac_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function romac_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on romac, use a find and replace
	 * to change 'romac' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'romac', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'romac' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'romac_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // romac_setup
add_action( 'after_setup_theme', 'romac_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function romac_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'romac' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'romac_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function romac_scripts() {
	wp_enqueue_script( 'romac-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

	wp_enqueue_script( 'romac-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

    // for localhost / testing environment, include the less css pre-processor
    if (isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'],'Google App Engine') !== true) {
        wp_enqueue_style(  'romac-style-less',        get_bloginfo( 'template_directory' ) . '/style.less' );
        wp_enqueue_style(  'romac-layout-style-less', get_bloginfo( 'template_directory' ) . '/layouts/layout.less' );
        wp_enqueue_script( 'less-processor',        get_template_directory_uri() . '/js/less.min.js', array(), '2.4.0', true );
    } else {
        wp_enqueue_style( 'romac-style',        get_stylesheet_uri() );
        wp_enqueue_style( 'romac-layout-style', get_template_directory_uri() . '/layouts/layout.css' );
    }
}
add_action( 'wp_enqueue_scripts', 'romac_scripts' );

/**
 * Enqueue admin specific scripts and styles.
 */
function romac_admin_scripts() {
    wp_enqueue_style( 'romac-admin-style', get_template_directory_uri() . '/admin.css');
}
add_action( 'admin_enqueue_scripts', 'romac_admin_scripts' );

/**
 * Implement the Custom Header feature.
 */
//require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**.
 * Register a widget area in the footer
 *
 * @link http://codex.wordpress.org/Widgetizing_Themes
 */
function footer_widgets_init() {
    register_sidebar( array(
        'name'          => 'Footer Area',
        'id'            => 'footer-widgets',
        'before_widget' => '<div>',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="rounded">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'footer_widgets_init' );

/**
 * Register the team member post type.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 */
function our_team_init() {
    $labels = array(
        'name'               => _x( 'Our Team', 'post type general name', 'rot-textdomain' ),
        'singular_name'      => _x( 'Team Member', 'post type singular name', 'rot-textdomain' ),
        'menu_name'          => _x( 'Team Members', 'admin menu', 'rot-textdomain' ),
        'name_admin_bar'     => _x( 'Team Member', 'add new on admin bar', 'rot-textdomain' ),
        'add_new'            => _x( 'Add New', 'book', 'rot-textdomain' ),
        'add_new_item'       => __( 'Add New Team Member', 'rot-textdomain' ),
        'new_item'           => __( 'New Team Member', 'rot-textdomain' ),
        'edit_item'          => __( 'Edit Team Member', 'rot-textdomain' ),
        'view_item'          => __( 'View Team Member', 'rot-textdomain' ),
        'all_items'          => __( 'All Team Members', 'rot-textdomain' ),
        'search_items'       => __( 'Search Team Members', 'rot-textdomain' ),
        'parent_item_colon'  => __( 'Parent Team Members:', 'rot-textdomain' ),
        'not_found'          => __( 'No team members found.', 'rot-textdomain' ),
        'not_found_in_trash' => __( 'No team members found in Trash.', 'rot-textdomain' )
    );

    $args = array(
        'labels'               => $labels,
        'public'               => true,
        'publicly_queryable'   => true,
        'show_ui'              => true,
        'show_in_menu'         => true,
        'query_var'            => true,
        'rewrite'              => array( 'slug' => 'about-romac/our-team' ),
        'capability_type'      => 'post',
        'has_archive'          => true,
        'hierarchical'         => false,
        'menu_position'        => null,
        'menu_icon'            => 'dashicons-id-alt',
        'supports'             => array( 'title', 'editor', 'thumbnail' ),
        'register_meta_box_cb' => 'add_our_team_meta_boxes'
    );

    register_post_type( 'our_team', $args );
}
add_action( 'init', 'our_team_init' );

/**
 * Adds custom meta fields to the our team post type
 */
function add_our_team_meta_boxes() {
    add_meta_box(
        'rot_meta_box',
        __( 'Team Member Meta', 'rot-textdomain' ),
        'rot_meta_box_callback',
        'our_team',
        'side',
        'default'
    );
}

/**
 * Prints the box content.
 *
 * @param WP_Post $post The object for the current post/page.
 */
function rot_meta_box_callback( $post ) {
    // Add an nonce field so we can check for it later.
    wp_nonce_field( 'rot_meta_box', 'rot_meta_box_nonce' );

    /*
     * Use get_post_meta() to retrieve an existing value
     * from the database and use the value for the form.
     */
    $name  = get_post_meta( $post->ID, '_rot_name',  true );
    $role  = get_post_meta( $post->ID, '_rot_role',  true );
    $email = get_post_meta( $post->ID, '_rot_email', true );

    // Echo the template fields

    // Team members name:
    echo '<label for="team-member-name">';
    _e( 'Full Name', 'romac-our-team-textdomain' );
    echo '</label> ';
    echo '<input type="text" id="team-member-name" name="team-member-name" value="' . esc_attr( $name ) . '" size="25" />';

    // Team members job role:
    echo '<label for="team-member-role">';
    _e( 'Job Role Title', 'romac-our-team-textdomain' );
    echo '</label> ';
    echo '<input type="text" id="team-member-role" name="team-member-role" value="' . esc_attr( $role ) . '" size="25" />';

    // Team members email address:
    echo '<label for="team-member-email">';
    _e( 'E-Mail Address', 'romac-our-team-textdomain' );
    echo '</label> ';
    echo '<input type="email" id="team-member-email" name="team-member-email" value="' . esc_attr( $email ) . '" />';
}

/**
 * Creates the organisational units taxonomy that is applied to the our team post type.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_taxonomy
 */
function create_organisational_units_taxonomy() {
    $labels = array(
        'name'                       => _x( 'Organisational Units', 'taxonomy general name' ),
        'singular_name'              => _x( 'Organisational Unit', 'taxonomy singular name' ),
        'search_items'               => __( 'Search Organisational Units' ),
        'popular_items'              => __( 'Popular Organisational Units' ),
        'all_items'                  => __( 'All Organisational Units' ),
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => __( 'Edit Organisational Unit' ),
        'update_item'                => __( 'Update Organisational Unit' ),
        'add_new_item'               => __( 'Add New Organisational Unit' ),
        'new_item_name'              => __( 'New Organisational Unit' ),
        'separate_items_with_commas' => __( 'Separate units with commas' ),
        'add_or_remove_items'        => __( 'Add or remove units' ),
        'choose_from_most_used'      => __( 'Choose from the most used units' ),
        'not_found'                  => __( 'No units found.' ),
        'menu_name'                  => __( 'Organisational Units' ),
    );

    $args = array(
        'hierarchical'          => true,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'organisational-units' ),
    );

    register_taxonomy( 'ou', 'our_team', $args );
}
add_action( 'init', 'create_organisational_units_taxonomy' );

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function rot_save_meta_box_data( $post_id ) {

    /*
     * We need to verify this came from our screen and with proper authorization,
     * because the save_post action can be triggered at other times.
     */

    // Check if our nonce is set.
    if ( ! isset( $_POST['rot_meta_box_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['rot_meta_box_nonce'], 'rot_meta_box' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }

    } else {

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    /* OK, it's safe for us to save the data now. */

    // Make sure that it is set.
    if ( ! isset( $_POST['team-member-name'] ) )  return;
    if ( ! isset( $_POST['team-member-role'] ) )  return;
    if ( ! isset( $_POST['team-member-email'] ) ) return;

    // Sanitize user input.
    $name =  sanitize_text_field( $_POST['team-member-name'] );
    $role =  sanitize_text_field( $_POST['team-member-role'] );
    $email = sanitize_text_field( $_POST['team-member-email'] );

    // Update the meta field in the database.
    update_post_meta( $post_id, '_rot_name', $name );
    update_post_meta( $post_id, '_rot_role', $role );
    update_post_meta( $post_id, '_rot_email', $email );
}
add_action( 'save_post', 'rot_save_meta_box_data' );

/**
 * Register the patrons and ambassadors post type.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 */
function patrons_init() {
    $labels = array(
        'name'               => _x( 'Our Patrons and Ambassadors', 'post type general name', 'rpab-textdomain' ),
        'singular_name'      => _x( 'Patron', 'post type singular name', 'rpab-textdomain' ),
        'menu_name'          => _x( 'Patrons', 'admin menu', 'rpab-textdomain' ),
        'name_admin_bar'     => _x( 'Patron', 'add new on admin bar', 'rpab-textdomain' ),
        'add_new'            => _x( 'Add New', 'book', 'rpab-textdomain' ),
        'add_new_item'       => __( 'Add New Patron', 'rpab-textdomain' ),
        'new_item'           => __( 'New Patron', 'rpab-textdomain' ),
        'edit_item'          => __( 'Edit Patron', 'rpab-textdomain' ),
        'view_item'          => __( 'View Patron', 'rpab-textdomain' ),
        'all_items'          => __( 'All Patrons', 'rpab-textdomain' ),
        'search_items'       => __( 'Search Patrons', 'rpab-textdomain' ),
        'parent_item_colon'  => __( 'Parent Patrons:', 'rpab-textdomain' ),
        'not_found'          => __( 'No patrons found.', 'rpab-textdomain' ),
        'not_found_in_trash' => __( 'No patrons found in Trash.', 'rpab-textdomain' )
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'about-romac/patrons-and-ambassadors' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-universal-access',
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
    );

    register_post_type( 'patrons', $args );
}
add_action( 'init', 'patrons_init' );

/**
 * Creates the designation taxonomy that is applied to the patrons post type.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_taxonomy
 */
function create_designation_taxonomy() {
    $labels = array(
        'name'                       => _x( 'Designation', 'taxonomy general name' ),
        'singular_name'              => _x( 'Designation', 'taxonomy singular name' ),
        'search_items'               => __( 'Search Designations' ),
        'popular_items'              => __( 'Popular Designations' ),
        'all_items'                  => __( 'All Designations' ),
        'parent_item'                => __( 'Parent Designation' ),
        'parent_item_colon'          => __( 'Parent Designations:' ),
        'edit_item'                  => __( 'Edit Designation' ),
        'update_item'                => __( 'Update Designation' ),
        'add_new_item'               => __( 'Add New Designation' ),
        'new_item_name'              => __( 'New Designation' ),
        'separate_items_with_commas' => __( 'Separate designations with commas' ),
        'add_or_remove_items'        => __( 'Add or remove designations' ),
        'choose_from_most_used'      => __( 'Choose from the most used designations' ),
        'not_found'                  => __( 'No designations found.' ),
        'menu_name'                  => __( 'Designations' ),
    );

    $args = array(
        'hierarchical'          => true,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'designations' ),
    );

    register_taxonomy( 'designation', 'patrons', $args );
}
add_action( 'init', 'create_designation_taxonomy' );

/**
 * A custom style enqueue function used to loading less files. NB: Development only
 *
 * @param $tag
 * @param $handle
 * @return string The composited link tag containing the specified link
 */
function enqueue_less_styles($tag, $handle) {
    global $wp_styles;
    $match_pattern = '/\.less$/U';
    if ( preg_match( $match_pattern, $wp_styles->registered[$handle]->src ) ) {
        $handle = $wp_styles->registered[$handle]->handle;
        $media = $wp_styles->registered[$handle]->args;
        $href = $wp_styles->registered[$handle]->src . '?ver=' . $wp_styles->registered[$handle]->ver;
        $title = isset($wp_styles->registered[$handle]->extra['title']) ? "title='" . esc_attr( $wp_styles->registered[$handle]->extra['title'] ) . "'" : '';

        $tag = "<link rel='stylesheet' id='$handle' $title href='$href' type='text/less' media='$media' />\r\n";
    }
    return $tag;
}
add_filter( 'style_loader_tag', 'enqueue_less_styles', 5, 2);
