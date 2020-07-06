<?php

//* Output testimonial_slider before
add_action( 'before_loop_layout_testimonial_slider', 'rb_testimonial_slider_before' );
function rb_testimonial_slider_before( $args ) {

    // Base testimonials slider
    wp_enqueue_style( 'testimonials-style' );

	wp_enqueue_style( 'slick-theme-style' );
	wp_enqueue_style( 'slick-main-style' );
	wp_enqueue_script( 'slick-main-script' );

	?>
	<script>
		jQuery(document).ready(function( $ ) {

			$('.loop-layout-testimonial_slider').slick({
				dots: true,
				arrows: false,
				speed: 300,
				adaptiveHeight: true,
				slidesToShow: 1,
				fade: true,
				cssEase: 'linear',
			});
						
		});
	</script>
	<?php
}

//* Output each testimonial_slider
add_action( 'add_loop_layout_testimonial_slider', 'rb_testimonial_slider_each' );
function rb_testimonial_slider_each() {

	//* Global vars
	global $post;
	$id = get_the_ID();

	//* Vars
	$title = get_the_title();
	$permalink = get_the_permalink();
	$content = apply_filters( 'the_content', apply_filters( 'the_content', get_the_content() ) );
	
	$jobtitle = get_post_meta( $id, '_rbt_testimonials_title', true );
	$url = get_post_meta( $id, '_rbt_testimonials_url', true );
	$urlwithoutwww = str_replace( 'www.', '', $url );
	$urlwithouthttp = str_replace( 'http://', '', $urlwithoutwww );
	$urlwithouthttps = str_replace( 'https://', '', $urlwithouthttp );
	
	//* Markup
	if ( has_post_thumbnail() )
		the_post_thumbnail( 'thumbnail' );

	if ( $content )
		printf( '<div class="testimonial-content">%s</div>', $content );

	if ( $title )
		printf( '<h3>%s</h3>', $title );

	if ( $jobtitle )
		printf( '<span class="jobtitle">%s</span>', $jobtitle );

	if ( $url )
		printf( '<a class="testimonial-url" href="%s" target="_blank">%s</a>', $url, $urlwithouthttps );
	
	// edit_post_link( 'Edit this testimonial', '<small>', '</small>' );
}