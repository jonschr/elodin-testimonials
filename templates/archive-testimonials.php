<?php

function custom_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

function new_excerpt_more( $more ) {
	return '...';
}
// add_filter('excerpt_more', 'new_excerpt_more');

/** Code for custom loop */
function testimonials_archive_loop() {
	global $paged;
    if ( have_posts() ) {

		$atts = apply_filters( 'testimonials_args', $atts );

		echo do_shortcode( '[testimonials]' );

    } // end if

    genesis_posts_nav();
    wp_reset_query();
}

/** Replace the standard loop with our custom loop */
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'testimonials_archive_loop' );

genesis();
