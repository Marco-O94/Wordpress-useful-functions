<?php

/*============================== WELCOME ==============================
* Here you will find every useful application of wordpress code, enjoy!
* =====================================================================*/


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





/*===================== CODING IN PROGRESS =====================*/ 