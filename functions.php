<?php
//* Start the engine
include_once( get_template_directory() . '/lib/init.php' );

//* Child theme (do not remove)
define( 'CHILD_THEME_NAME', 'MunichParis' );
define( 'CHILD_THEME_URL', 'http://www.munichparis.com/' );

//* Enqueue Google Fonts
add_action( 'wp_enqueue_scripts', 'mp_google_fonts' );
function mp_google_fonts() {

	wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Lato:300,400,700|Karla|Playfair+Display|EB+Garamond|Montserrat', array(), CHILD_THEME_VERSION );

}

//* Add HTML5 markup structure
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

//* Add support for custom background
add_theme_support( 'custom-background' );

//* Add support for 3-column footer widgets
add_theme_support( 'genesis-footer-widgets', 3 );

//* Add new image sizes
add_image_size('grid-thumbnail', 200, 100, TRUE);
add_image_size( 'featured-thumb', 300, 300 );
add_image_size( 'related', 300, 200, true );


/**
* Add Slider Widget Area Below Header.
*/

//* Add the Page Title section 
add_action( 'genesis_after_header', 'slider_widget' );
function slider_widget() {
 
    if (is_active_sidebar( 'sliderwidget' )) {
        genesis_widget_area( 'sliderwidget', array(
            'before' => '<div class="sliderwidget widget-area"><div class="wrap"',
            'after' => '</div></div>'
            ) );
    }
}

//* Register widget areas
genesis_register_sidebar( array(
    'id' 			=> 'sliderwidget',
    'name' 			=> __( 'Slider Widget Area', 'mp' ),
    'description' 	=> __( 'This is the slider widget section.', 'mp' ),
) );

//* Enqueue sticky menu script
add_action( 'wp_enqueue_scripts', 'sp_enqueue_script' );
function sp_enqueue_script() {
wp_enqueue_script( 'sample-sticky-menu', get_bloginfo( 'stylesheet_directory' ) . '/js/sticky-menu.js', array( 'jquery' ), '1.0.0' );
}


//* Reposition the secondary navigation menu
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_before', 'genesis_do_subnav' );

// Register the widget area
genesis_register_sidebar( array(
	'id'          => 'nav-social-menu',
	'name'        => __( 'Secondary Nav Widget', 'mp' ),
	'description' => __( 'This is the nav social menu section.', 'mp' ),
) );
add_filter( 'wp_nav_menu_items', 'mp_social_icons', 10, 2 );
function mp_social_icons($menu, $args) {
	$args = (array)$args;
	
	if ( 'secondary' !== $args['theme_location'] ) {
		return $menu;
	}
	
	// start to buffer the output
	ob_start(); 
	
	// wrap the menu in a list item, otherwise it throws a validation error
	echo '<li class="menu-item">';
	genesis_widget_area('nav-social-menu');
	echo '</li>';
	
	$social = ob_get_clean(); // 
	return $menu . $social;
}




// Primary nav below header
remove_action( 'genesis_before_header', 'genesis_do_nav' );
add_action( 'genesis_after_header', 'genesis_do_nav' );


// Add header image
add_theme_support( 'genesis-custom-header', array( 'width' => 300, 'height' => 100 ) );


//* Add Post categories above Post title 
add_action ( 'genesis_entry_header', 'mp_show_category_name', 9 );
function mp_show_category_name() {
	echo do_shortcode('[post_categories before=""]');
}



// TRIPLE HEADER
//* Filter the header-right widget-area classes
add_filter( 'genesis_attr_header-widget-area', 'mp_genesis_attr_header_widget' );
function mp_genesis_attr_header_widget ( $attributes ) {
    
    $attributes['class'] = 'widget-area header-widget-area header-right';
    return $attributes;
}

//* Add markup for header-left widget-area
add_action( 'genesis_header', 'mp_header_left', 8 );
function mp_header_left() {

    if ( is_active_sidebar( 'header-left' ) ) {
        
        echo '<aside class="widget-area header-widget-area header-left">';
            
        add_filter( 'wp_nav_menu_args', 'genesis_header_menu_args' );
        add_filter( 'wp_nav_menu', 'genesis_header_menu_wrap' );
        dynamic_sidebar( 'header-left' );
        remove_filter( 'wp_nav_menu_args', 'genesis_header_menu_args' );
        remove_filter( 'wp_nav_menu', 'genesis_header_menu_wrap' );

        echo '</aside>';
    }
}

//* Register header-left widget-area
genesis_register_sidebar( array(
    'id'   => 'header-left',
    'name' => __( 'Header Left', 'mp' ),
) );  



//* New footer area
genesis_register_sidebar( array(
    'id' => 'before-footer',
    'name' => __( 'Before Footer', 'mp' ),
    'description' => __( 'Before Footer Widget.', 'mp' ),
) );


add_action( 'genesis_before_footer', 'before_footer_widget', 4 );
function before_footer_widget() {
if (is_active_sidebar( 'before-footer' ) ) {
		echo '<div class="before-footer">';
		dynamic_sidebar( 'before-footer' );
		echo '</div><!-- end #before-footer -->';
	}
 
}


// Full width post header on post pages
add_action( 'get_header', 'reposition_single_entry_header' );
function reposition_single_entry_header() {
if ( is_singular('post')) :
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
remove_action( 'genesis_entry_header', 'mp_show_category_name', 9 );
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );

add_action( 'genesis_before_content', 'genesis_entry_header_markup_open', 5 );
add_action( 'genesis_before_content', 'mp_show_category_name');
add_action( 'genesis_before_content', 'genesis_do_post_title' );
add_action( 'genesis_before_content', 'genesis_post_info');
add_action( 'genesis_before_content', 'genesis_entry_header_markup_close', 15 );
endif;
}


//Search form edit 
add_filter( 'genesis_search_text', 'mp_search_text' );
function mp_search_text( $text ) {
	return esc_attr( 'Search + hit enter' );
}
add_filter( 'genesis_search_button_text', 'mp_search_button_text' );
function mp_search_button_text( $text ) {
	return esc_attr( 'Go' );
}



/** Remove Genesis footer widgets **/
remove_theme_support( 'genesis-footer-widgets', 3 );
/** Add support for 1 footer widget **/
add_theme_support( 'genesis-footer-widgets', 1 );




//* Register before header widget area
genesis_register_sidebar( array(
	'id'          => 'before-header',
	'name'        => __( 'Before Header', 'mp' ),
	'description' => __( 'This is the before header widget area.', 'mp' ),
) );

//* Hook before header widget area before site header
add_action( 'genesis_before_header', 'mp_before_header_widget_area' );
function mp_before_header_widget_area() {
	genesis_widget_area( 'before-header', array(
		'before' => '<div class="before-header widget-area"><div class="wrap">',
		'after'  => '</div></div>',
	) );
}





// Add Social Widget Area for Primay Nav
genesis_register_sidebar( array(
	'id'          => 'nav-social-menu',
	'name'        => __( 'Nav Social Menu' ),
	'description' => __( 'This is the nav social menu section.' ),
) );
// Add Search to Primary Nav
add_filter( 'wp_nav_menu_items', 'genesis_search_primary_nav_menu', 10, 2 );
function genesis_search_primary_nav_menu( $menu, stdClass $args ){
  if ( 'primary' != $args->theme_location )
    return $menu;
        if( genesis_get_option( 'nav_extras' ) )
          return $menu;
	ob_start();
	echo '<li id="menu-item-4439203" class="custom-social menu-item">';
	genesis_widget_area('nav-social-menu');
	$social = ob_get_clean();
    return $menu . $social;
}



// Add Read More Link to Excerpts
add_filter('excerpt_more', 'get_read_more_link');
add_filter( 'the_content_more_link', 'get_read_more_link' );
function get_read_more_link() {
   return '...&nbsp;<a href="' . get_permalink() . '">[Read&nbsp;More]</a>';
}



// ADD RELATED POSTS WIDGET
add_action( 'genesis_after_entry_content', 'mp_related_posts' );

function mp_related_posts() {

if ( is_single ( ) ) {

global $post;

$count = 0;
$postIDs = array( $post->ID );
$related = '';
$tags = wp_get_post_tags( $post->ID );
$cats = wp_get_post_categories( $post->ID );

if ( $tags ) {

foreach ( $tags as $tag ) {

$tagID[] = $tag->term_id;

}

$args = array(
'tag__in' => $tagID,
'post__not_in' => $postIDs,
'showposts' => 3,
'ignore_sticky_posts' => 1,
'tax_query' => array(
array(
'taxonomy' => 'post_format',
'field' => 'slug',
'terms' => array(
'post-format-link',
'post-format-status',
'post-format-aside',
'post-format-quote'
),
'operator' => 'NOT IN'
)
)
);

$tag_query = new WP_Query( $args );

if ( $tag_query->have_posts() ) {

while ( $tag_query->have_posts() ) {

$tag_query->the_post();

$img = genesis_get_image() ? genesis_get_image( array( 'size' => 'related' ) ) : '<img src="' . get_bloginfo( 'stylesheet_directory' ) . '/images/related.png" alt="' . get_the_title() . '" />';

$related .= '<li><a href="' . get_permalink() . '" rel="bookmark" title="' . get_the_title() . '">' . $img . get_the_title() . '</a></li>';

$postIDs[] = $post->ID;

$count++;
}
}
}

if ( $count <= 2 ) {

$catIDs = array( );

foreach ( $cats as $cat ) {

if ( 3 == $cat )
continue;
$catIDs[] = $cat;

}

$showposts = 3 - $count;

$args = array(
'category__in' => $catIDs,
'post__not_in' => $postIDs,
'showposts' => $showposts,
'ignore_sticky_posts' => 1,
'orderby' => 'rand',
'tax_query' => array(
array(
'taxonomy' => 'post_format',
'field' => 'slug',
'terms' => array(
'post-format-link',
'post-format-status',
'post-format-aside',
'post-format-quote' ),
'operator' => 'NOT IN'
)
)
);

$cat_query = new WP_Query( $args );

if ( $cat_query->have_posts() ) {

while ( $cat_query->have_posts() ) {

$cat_query->the_post();

$img = genesis_get_image() ? genesis_get_image( array( 'size' => 'related' ) ) : '<img src="' . get_bloginfo( 'stylesheet_directory' ) . '/images/related.png" alt="' . get_the_title() . '" />';

$related .= '<li><a href="' . get_permalink() . '" rel="bookmark" title="Permanent Link to' . get_the_title() . '">' . $img . get_the_title() . '</a></li>';
}
}
}

if ( $related ) {

printf( '<div class="related-posts"><h3 class="related-title">Related Posts</h3><ul class="related-list">%s</ul></div>', $related );

}

wp_reset_query();

}
}
