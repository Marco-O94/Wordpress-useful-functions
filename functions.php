<?php

/*============================== WELCOME ==============================
* Here you will find every useful application of wordpress code, enjoy!
* =====================================================================*/

/*============================== KEYWORDS ==============================
* You can use those keywords in your code editor to easily find what you are looking for.
* - WORDPRESS BASIC THINGS
* - REGISTER STYLE FILES
* - REGISTER SCRIPT FILES
* - CREATE A SHORTCODE
* - SHORTCODE WITH ATTRIBUTES
* - SHORTCODE + ACF
* - WP QUERY
* - WP QUERY + ACF
* - WP QUERY + ACF (Import only products chosen in backend artisan or owner page)
* - REGISTER A POST TYPE
* - CHANGE WORDPRESS LOGIN PANEL LOGO
* - SEND EMAIL WHEN POST IS PUBLISHED
* - GET TEMPLATE PARTS
* - MEDIA HANDLE UPDATE
*/


/*===================== WORDPRESS BASIC THINGS (Work in progress) =====================*/ 
$post = get_post(); // Return the Post Object (not the array, watch out!)
/* From now on we will call pre-composed functions with get_post in, we may call them shortcuts. You can call variables as you wish. */
$post_id = get_the_ID(); // Return the ID(int) of a post.
$post_type = get_post_type(); // Return the post-type(string) of the post. You can use either post_object or post_id.
$post_meta = get_post_meta(); // Return post meta keys of the post.
$post_title = get_the_title(); // Return post title.
$post_content = get_the_content(); // Return post content.
$post_parent = get_post_parent(); // Return post parent
$post_permalink = get_permalink(); // Return post permalink with post slug in, like get_the_permalink();
$post_permalink2 = get_post_permalink(); // Return post permalink with post type and id on url, like this: mywebsite.it/wordpresstest/?post_type=page&p=2"
$post_status = get_post_status(); // Return post status like publish, draft or private.
$post_excerpt = get_the_excerpt(); // Return post excerpt.
$post_category = get_the_category(); // Return categories as objects in an array, watch out! Try var_dump(); for more info.
$post_thumbnail = get_the_post_thumbnail(); //Return post thumbnail with wordpress layout. the_post_thumbnail(); is the same. Source function below:

/*function the_post_thumbnail( $size = 'post-thumbnail', $attr = '' ) {
    echo get_the_post_thumbnail( null, $size, $attr );
}*/


/* Functions that requires post ID */
$post_terms = get_the_terms($post->ID, 'term'); // Return post Terms.
/*
WP_Term Object (Below there are terms that you can use in string above.)
(
    [term_id] => 
    [name] => 
    [slug] => 
    [term_group] => 
    [term_taxonomy_id] => 
    [taxonomy] => 
    [description] => 
    [parent] => 
    [count] => 
    [filter] => 
)
*/


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

/*===================== CREATE A SHORTCODE =====================*/ 
function my_function() {
	// Your Code here
}
add_shortcode('tag_name', 'my_function');

/* ======== SHORTCODE WITH ATTRIBUTES ======== */
function my_shortcode($atts) {
	
	$values = $atts["tag_name"]; // Tag name is for example [test_short tag_name="myvalue"]. It will return myvalue in the $values variable.
	echo $values;
}

add_shortcode('test_short', 'my_shortcode');

/* ======== SHORTCODE + ACF (An Example) ======== */
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

/*===================== WP QUERY =====================
* First of all you need arguments for your query, so declare an array with what you need.
* You can find array parameters list with relative links in the README file. */

/* A complete example of WP Query below */

// WP_Query arguments
$args = array(
    'post_type'              => array('post'), // use any for any kind of post type, custom post type slug for custom post type
    'post_status'            => array('publish'), // Also support: pending, draft, auto-draft, future, private, inherit, trash, any
    'posts_per_page'         => '5', // use -1 for all post
    'order'                  => 'DESC', // Also support: ASC
    'orderby'                => 'date', // Also support: none, rand, id, title, slug, modified, parent, menu_order, comment_count
    'tax_query'              => array(
        'relation' => 'OR', // Use AND for taking result on both condition true
        array(
            'taxonomy'         => 'category', // taxonomy slug
            'terms'            => array(1, 2), // term ids
            'field'            => 'term_id', // Also support: slug, name, term_taxonomy_id
            'operator'         => 'IN', // Also support: AND, NOT IN, EXISTS, NOT EXISTS
            'include_children' => true,
        ),
        array(
            'taxonomy'         => 'custom-category', // taxonomy slug
            'terms'            => array(1, 2), // term ids
            'field'            => 'term_id', // Also support: slug, name, term_taxonomy_id
            'operator'         => 'IN', // Also support: slug, name, term_taxonomy_id
            'include_children' => true,
        ),
    ),
    'meta_query'             => array(
        'relation' => 'OR', // Use AND for taking result on both condition true
        array(
            'key'     => 'meta-1', // any meta key
            'value'   => 'value-1', // value under that meta
            'compare' => 'LIKE', // Also support: =, !=, >, >=, <, <=, NOT LIKE, IN, NOT IN, BETWEEN, NOT BETWEEN, EXISTS, NOT EXISTS, REGEXP, NOT REGEXP, RLIKE
            'type'    => 'CHAR', // Also support: NUMERIC, BINARY, DATE, DATETIME, DECIMAL, SIGNED, UNSIGNED, TIME
        ),
        array(
            'key'     => 'meta-2', // any meta key
            'value'   => 'value-2', // value under that meta
            'compare' => 'LIKE', // Also support: =, !=, >, >=, <, <=, NOT LIKE, IN, NOT IN, BETWEEN, NOT BETWEEN, EXISTS, NOT EXISTS, REGEXP, NOT REGEXP, RLIKE
            'type'    => 'CHAR', // Also support: NUMERIC, BINARY, DATE, DATETIME, DECIMAL, SIGNED, UNSIGNED, TIME
        ),
    ),
);
 
// The Query
$query = new WP_Query($args);
 
// The Loop
if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        // do something
    }
} else {
    // no posts found
}
 
// Restore original Post Data
wp_reset_postdata();


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
	echo '<ul class="products elementor-grid columns-4">'; //Some elementor classes;
    while($the_query->have_posts() ) {
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


/* WP QUERY + ACF (Import only products chosen in backend artisan or owner page) */
function artisan_products_block() {
	$artisan_id = get_field("artisan_tag");
	$artisan_slug= $artisan_id->slug;
	$artisan_products = get_field("artisan_products"); // Use ACF post Object field type, set it to ID, choose multi-selection.
	// Scelgo gli argomenti per la query
	$args = array(
    'post_type'              => array('product'), // use any for any kind of post type, custom post type slug for custom post type
    'post__in'				 => $artisan_products,
	'posts_per_page'         => '4',
	'orderby'					 => 'post__in' //This orders products by position chosen in backend.
);

$the_query = new WP_Query($args); //Starting the query with my args above.

if ($artisan_products) {
	echo '<ul class="products elementor-grid columns-4">';
    while ( $the_query->have_posts() ) {
		$the_query->the_post();
		wc_get_template_part( 'content', 'product' ); //Get the woocommerce or your theme product layout.
    }
	echo '</ul>';
} else {
  echo 'No products found';
}

wp_reset_postdata();
}
add_shortcode('products_grid', 'artisan_products_block');


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


/*===================== CHANGE WORDPRESS LOGIN PANEL LOGO =====================*/

function my_login_logo() { 
    $uploads = wp_upload_dir(); 
    ?>
        <style type="text/css">
            #login h1 a, .login h1 a {
                background-image: url('<?php echo $uploads['baseurl'] . '/2021/10/myLogo.svg?>'; ?>');
                padding-right: 3px;
            height:100px;
            width:200px;
            background-size: 320px auto;
            background-repeat: no-repeat;
                background-size:contain;
                background-position: center;
                padding-bottom: 0;
                margin-bottom: 0;
            }
        </style>
    <?php
    }
    add_action( 'login_enqueue_scripts', 'my_login_logo' );

/*===================== SEND EMAIL WHEN POST IS PUBLISHED =====================*/
// You can use it with woocommerce products too, for example you want to send news about a product
    function wpdocs_email( $post ) {
        $post_type = get_post_type($post);
        if($post_type == 'post') {
        $contacts = 'mail@mail.it, mail2@mail.it'; // you can convert array to string with -> echo implode(" ",$arr);
        wp_mail( $friends, "Mail Title Goes Here", 'Mail Body Goes here' ); // 	wp_mail( $to, $subject, $message, $headers, $attachments );
        }
        return $post;
    }
    add_action( 'publish_post', 'wpdocs_email' );


/*===================== GET TEMPLATE PARTS (Work in progress) =====================*/
get_template_part( 'template-parts/file', 'name', $args ); // template-parts/file-name.php

//example below
$data = array(
    'location'  => 'slider',
    'number'    =>   3
);
// Passing variable to get_template_part
get_template_part( 'templates/slider', 'full', $data ); //templates/slider-full.php
// get data in part of template
global $data; //dont forget use globally
print_r($data); // output: array('location' => 'slider', 'number' => 3)

/*===================== MEDIA HANDLE UPDATE =====================*/
//Saves a file submitted from a POST request and create an attachment post for it.
//Example below.
function media_handle_upload( $file_id, $post_id, $post_data = array(), $overrides = array( 'test_form' => false ) ) {
    $time = current_time( 'mysql' );
    $post = get_post( $post_id );
 
    if ( $post ) {
        // The post date doesn't usually matter for pages, so don't backdate this upload.
        if ( 'page' !== $post->post_type && substr( $post->post_date, 0, 4 ) > 0 ) {
            $time = $post->post_date;
        }
    }
 
    $file = wp_handle_upload( $_FILES[ $file_id ], $overrides, $time );
 
    if ( isset( $file['error'] ) ) {
        return new WP_Error( 'upload_error', $file['error'] );
    }
 
    $name = $_FILES[ $file_id ]['name'];
    $ext  = pathinfo( $name, PATHINFO_EXTENSION );
    $name = wp_basename( $name, ".$ext" );
 
    $url     = $file['url'];
    $type    = $file['type'];
    $file    = $file['file'];
    $title   = sanitize_text_field( $name );
    $content = '';
    $excerpt = '';
 
    if ( preg_match( '#^audio#', $type ) ) {
        $meta = wp_read_audio_metadata( $file );
 
        if ( ! empty( $meta['title'] ) ) {
            $title = $meta['title'];
        }
 
        if ( ! empty( $title ) ) {
 
            if ( ! empty( $meta['album'] ) && ! empty( $meta['artist'] ) ) {
                /* translators: 1: Audio track title, 2: Album title, 3: Artist name. */
                $content .= sprintf( __( '"%1$s" from %2$s by %3$s.' ), $title, $meta['album'], $meta['artist'] );
            } elseif ( ! empty( $meta['album'] ) ) {
                /* translators: 1: Audio track title, 2: Album title. */
                $content .= sprintf( __( '"%1$s" from %2$s.' ), $title, $meta['album'] );
            } elseif ( ! empty( $meta['artist'] ) ) {
                /* translators: 1: Audio track title, 2: Artist name. */
                $content .= sprintf( __( '"%1$s" by %2$s.' ), $title, $meta['artist'] );
            } else {
                /* translators: %s: Audio track title. */
                $content .= sprintf( __( '"%s".' ), $title );
            }
        } elseif ( ! empty( $meta['album'] ) ) {
 
            if ( ! empty( $meta['artist'] ) ) {
                /* translators: 1: Audio album title, 2: Artist name. */
                $content .= sprintf( __( '%1$s by %2$s.' ), $meta['album'], $meta['artist'] );
            } else {
                $content .= $meta['album'] . '.';
            }
        } elseif ( ! empty( $meta['artist'] ) ) {
 
            $content .= $meta['artist'] . '.';
 
        }
 
        if ( ! empty( $meta['year'] ) ) {
            /* translators: Audio file track information. %d: Year of audio track release. */
            $content .= ' ' . sprintf( __( 'Released: %d.' ), $meta['year'] );
        }
 
        if ( ! empty( $meta['track_number'] ) ) {
            $track_number = explode( '/', $meta['track_number'] );
 
            if ( isset( $track_number[1] ) ) {
                /* translators: Audio file track information. 1: Audio track number, 2: Total audio tracks. */
                $content .= ' ' . sprintf( __( 'Track %1$s of %2$s.' ), number_format_i18n( $track_number[0] ), number_format_i18n( $track_number[1] ) );
            } else {
                /* translators: Audio file track information. %s: Audio track number. */
                $content .= ' ' . sprintf( __( 'Track %s.' ), number_format_i18n( $track_number[0] ) );
            }
        }
 
        if ( ! empty( $meta['genre'] ) ) {
            /* translators: Audio file genre information. %s: Audio genre name. */
            $content .= ' ' . sprintf( __( 'Genre: %s.' ), $meta['genre'] );
        }
 
        // Use image exif/iptc data for title and caption defaults if possible.
    } elseif ( 0 === strpos( $type, 'image/' ) ) {
        $image_meta = wp_read_image_metadata( $file );
 
        if ( $image_meta ) {
            if ( trim( $image_meta['title'] ) && ! is_numeric( sanitize_title( $image_meta['title'] ) ) ) {
                $title = $image_meta['title'];
            }
 
            if ( trim( $image_meta['caption'] ) ) {
                $excerpt = $image_meta['caption'];
            }
        }
    }
 
    // Construct the attachment array.
    $attachment = array_merge(
        array(
            'post_mime_type' => $type,
            'guid'           => $url,
            'post_parent'    => $post_id,
            'post_title'     => $title,
            'post_content'   => $content,
            'post_excerpt'   => $excerpt,
        ),
        $post_data
    );
 
    // This should never be set as it would then overwrite an existing attachment.
    unset( $attachment['ID'] );
 
    // Save the data.
    $attachment_id = wp_insert_attachment( $attachment, $file, $post_id, true );
 
    if ( ! is_wp_error( $attachment_id ) ) {
        // Set a custom header with the attachment_id.
        // Used by the browser/client to resume creating image sub-sizes after a PHP fatal error.
        if ( ! headers_sent() ) {
            header( 'X-WP-Upload-Attachment-ID: ' . $attachment_id );
        }
 
        // The image sub-sizes are created during wp_generate_attachment_metadata().
        // This is generally slow and may cause timeouts or out of memory errors.
        wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $file ) );
    }
 
    return $attachment_id;
}

/*===================== CODING IN PROGRESS =====================*/ 
/* Next Steps:
* - Need to end Basic Things
* - more wp functions incoming
* - Actions
* - Filters
*/
