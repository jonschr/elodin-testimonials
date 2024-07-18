<?php

//* Output each testimonial_slider
function elodin_testimonial_slider_each() {
	
	// Base testimonials slider
	wp_enqueue_style( 'testimonials-style' );

	// Slick stuff
	wp_enqueue_style( 'slick-theme-style' );
	wp_enqueue_style( 'slick-main-style' );
	wp_enqueue_script( 'slick-main-script' );
	wp_enqueue_script( 'elodin-testimonials-slick-init' );

	//* Global vars
	global $post;
	$id = get_the_ID();

	//* Vars
	$title = esc_html( get_the_title() );
	$permalink = esc_url( get_the_permalink() );
	$content = apply_filters( 'the_content', apply_filters( 'the_content', get_the_content() ) );
	$jobtitle = esc_html( get_post_meta( $id, '_rbt_testimonials_title', true ) );
	$url = esc_url( get_post_meta( $id, '_rbt_testimonials_url', true ) );
	$background = esc_url( get_the_post_thumbnail_url( get_the_ID(), 'large' ) );
	
	//* Markup
	if ( $content )
		printf( '<div class="testimonial-content">%s</div>', $content );

	if ( $title )
		printf( '<h3>%s</h3>', $title );

	if ( $jobtitle )
		printf( '<span class="jobtitle">%s</span>', $jobtitle );

	if ( $url )
		printf( '<a class="testimonial-url" href="%s" target="_blank">%s</a>', $url, $url );
		
	if ( $background ) 
		printf( '<div class="featured-image" style="background-image:url( %s )"></div>', $background );
	
	// edit_post_link( 'Edit this testimonial', '<small>', '</small>' );
}
add_action( 'add_loop_layout_testimonial_slider', 'elodin_testimonial_slider_each' );