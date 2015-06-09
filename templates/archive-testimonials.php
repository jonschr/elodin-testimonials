<?php

function custom_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

function new_excerpt_more( $more ) {
	return '...';
}
add_filter('excerpt_more', 'new_excerpt_more');

/** Code for custom loop */
function testimonials_archive_loop() {

    if ( have_posts() ) {
    	while ( have_posts() ) {
    		the_post(); 
    		?>

			<div <?php post_class(); ?>>
				<div class="testimonial-entry">
					<div class="testimonial-content">
						<?php the_content(); ?>
						<?php edit_post_link( 'Edit this testimonial', '<p><small>', '</small></p>' ); ?>
					</div>
					<h2 class="testimonial-title">- <?php the_title(); ?></h2>
				</div>
			</div>

			<?php
    	} // end while
    } // end if
}
 
/** Replace the standard loop with our custom loop */
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'testimonials_archive_loop' );

genesis();