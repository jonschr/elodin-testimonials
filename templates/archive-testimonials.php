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
    	while ( have_posts() ) {
    		the_post(); 
    		?>

			<div <?php post_class(); ?>>
				<div class="testimonials-entry">
					<div class="testimonials-content">
						<?php

						the_content();
						edit_post_link( 'Edit this testimonial', '<p><small>', '</small></p>' );
						
						$title = get_post_meta( get_the_ID(), '_rbt_testimonials_title', true );
						
						$url = get_post_meta( get_the_ID(), '_rbt_testimonials_url', true );
						$urlwithoutwww = str_replace( 'www.', '', $url );
						$urlwithouthttp = str_replace( 'http://', '', $urlwithoutwww );
						$urlwithouthttps = str_replace( 'https://', '', $urlwithouthttp );

						
						
						?>
					</div>
					<?php the_post_thumbnail( 'rbt_testimonials_image_square' ); ?>
					<cite class="testimonials-author">
						<span class="testimonials-name"><?php the_title(); ?></span>
						<span class="testimonials-title"><?php echo $title; ?></span>

						<?php if ( !empty( $url ) ) {
							?>
						<a target="_blank" href="<?php echo $url; ?>" class="testimonials-url"><?php echo $urlwithouthttps; ?></a>
							<?php
						}
						?>
					</cite>
					
				</div>
			</div>

			<?php
    	} // end while
    } // end if

    genesis_posts_nav();
    wp_reset_query();
}
 
/** Replace the standard loop with our custom loop */
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'testimonials_archive_loop' );

genesis();