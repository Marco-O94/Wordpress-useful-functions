<?php

/*============================== WELCOME ==============================
* Here you will find every useful application of wordpress code, enjoy!
* =====================================================================*/

/*===================== REGISTER STYLE FILES =====================*/ 
function add_my_style() {
    wp_register_style( 'my_css', get_stylesheet_directory_uri() . '/css/myStyle.css' ); //Choose a name for your style, use a different name from previous registered styles.
    wp_enqueue_style( 'my_css' );
}
add_action( 'wp_enqueue_scripts', 'add_my_style', 20 ); //Second add_action value must be the function value that register the style. 

/*===================== REGISTER SCRIPT FILES =====================*/ 

function add_my_customScript() {
    wp_register_script('customScript', get_stylesheet_directory_uri() . '/js/customScript.js');
    wp_enqueue_script('customScript');
} 
add_action( 'wp_enqueue_scripts', 'add_my_customScript' ); //Second add_action value must be the function value that register the style.

/*===================== SHORTCODE =====================*/ 
function my_function() {
	// Your Code here
}
add_shortcode('tag_name', 'my_function');


/* ======== SHORTCODE + ACF ======== */
/* It uses lightbox.js and some grid css */

function dynamic_gallery() {
	$gallery_imgs = get_field('your_gallery_custom_field'); //get_field is a Advanced custom field function
	if ($gallery_imgs) {
	echo '<div class="gallery-grid">';
	foreach ($gallery_imgs as $gallery_img) {
  		echo '<div class="grid-item"><a href="' . $gallery_img["url"] . '" data-lightbox="my_image"><img src="' . $gallery_img["url"] . '"></a></div>';
	}
	echo '</div><script>
    lightbox.option({
      \'resizeDuration\': 200,
      \'albumLabel\': \'Image %1 of %2\'
    })
</script>';
	}
	else {
		echo 'No images found';
	}
}
add_shortcode('custom-gallery', 'dynamic_gallery');



/* ======== WP QUERY + ACF ======== */
/* Custom products query from a chosen tag */

function custom_products_block() {
	$post_id = get_field("custom_field_name"); // I used ACF because I want to filter products of a chosen meta tag.
	$post_slug = $post_id->slug;

	// I'm going to choose arguments for my query
	$args = array(
    'post_type'              => array('product'), // use any for any kind of post type, custom post type slug for custom post type
    'post_status'            => array('publish'), // Also support: pending, draft, auto-draft, future, private, inherit, trash, any
    'posts_per_page'         => '4', // use -1 for all post
    'order'                  => 'DESC', // Also support: ASC
    'orderby'                => 'date', // Also support: none, rand, id, title, slug, modified, parent, menu_order, comment_count
    'tax_query'              => array(
        'relation' => 'AND', // Use AND for taking result on both condition true
        array(
            'taxonomy'         => 'product_tag', // taxonomy slug
            'terms'            => $post_slug, // term ids
            'field'            => 'slug', // Also support: slug, name, term_taxonomy_id
        ),
    ),
);
 
// Start the query with selected arguments above.
$the_query = new WP_Query($args);
 
// If posts are in, than I'll loop until the end.
if ( $the_query->have_posts() ) {
	echo '<ul class="products elementor-grid columns-4">' //Some elementor classes;
    while ( $the_query->have_posts() ) {
		$the_query->the_post();
		/* I will use default product template from woocommerce or your theme */
		wc_get_template_part( 'content', 'product' );
    }
	echo '</ul>';
} else {
  echo 'Nessun prodotto trovato';
}

wp_reset_postdata();
}
add_shortcode('custom_metaProducts', 'custom_products_block');


/*===================== REGISTER A POST TYPE =====================*/ 

/**
 * Register a custom post type called "book".
 *
 * @see get_post_type_labels() for label keys.
 */
function wpdocs_codex_book_init() {
    $labels = array(
        'name'                  => _x( 'Books', 'Post type general name', 'textdomain' ),
        'singular_name'         => _x( 'Book', 'Post type singular name', 'textdomain' ),
        'menu_name'             => _x( 'Books', 'Admin Menu text', 'textdomain' ),
        'name_admin_bar'        => _x( 'Book', 'Add New on Toolbar', 'textdomain' ),
        'add_new'               => __( 'Add New', 'textdomain' ),
        'add_new_item'          => __( 'Add New Book', 'textdomain' ),
        'new_item'              => __( 'New Book', 'textdomain' ),
        'edit_item'             => __( 'Edit Book', 'textdomain' ),
        'view_item'             => __( 'View Book', 'textdomain' ),
        'all_items'             => __( 'All Books', 'textdomain' ),
        'search_items'          => __( 'Search Books', 'textdomain' ),
        'parent_item_colon'     => __( 'Parent Books:', 'textdomain' ),
        'not_found'             => __( 'No books found.', 'textdomain' ),
        'not_found_in_trash'    => __( 'No books found in Trash.', 'textdomain' ),
        'featured_image'        => _x( 'Book Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
        'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
        'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
        'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
        'archives'              => _x( 'Book archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
        'insert_into_item'      => _x( 'Insert into book', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
        'uploaded_to_this_item' => _x( 'Uploaded to this book', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
        'filter_items_list'     => _x( 'Filter books list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
        'items_list_navigation' => _x( 'Books list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
        'items_list'            => _x( 'Books list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
    );
 
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'book' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
    );
 
    register_post_type( 'book', $args );
}
 
add_action( 'init', 'wpdocs_codex_book_init' );


/*===================== CODING IN PROGRESS =====================*/ 