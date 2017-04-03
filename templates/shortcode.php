<?php

//* For use with the Genesis Simple Query Shortcodes plugin, attach this
add_action( 'add_loop_layout_testimonials', 'testimonials_do_single' );

//* Just for the Genesis Simple Query Shotrcodes plugin, we'll need those styles included
add_action( 'before_loop_layout_testimonials', 'testimonials_do_before' );
function testimonials_do_before() {
	wp_enqueue_style( 'testimonials-style' );
}

//* A function which takes the post ID and outputs the markup for a single testimonial
function testimonials_do_single( $post_id ) {
	$post_class = implode( get_post_class(), ' ' );
	printf( '<div %s>', $post_class );
	?>
		<div class="testimonials-entry">
			<div class="testimonials-content">
				<?php

				the_content();
				edit_post_link( 'Edit this testimonial', '<p><small>', '</small></p>' );

				$title = get_post_meta( $post_id, '_rbt_testimonials_title', true );

				$url = get_post_meta( $post_id, '_rbt_testimonials_url', true );
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
}

//* A function which does a default testimonials loop (also used on the archive page)
add_shortcode( 'testimonials', 'testimonials_shortcode_loop' );
function testimonials_shortcode_loop( $atts ) {

    $atts = shortcode_atts(
        array(
            'post_type' => 'testimonials',
            'posts_per_page' => -1,
        ), $atts, 'testimonials'
    );

   $cpt_query = new WP_Query( $atts );

    global $paged;

    ob_start();

    if ( $cpt_query->have_posts() ) {

        wp_enqueue_style( 'testimonials-style' );

        echo '<div class="testimonials-container">';

	    	while ( $cpt_query->have_posts() ) {

	    		$cpt_query->the_post();
	    		

	    		testimonials_do_single( get_the_ID() );

	    	} // end while

        echo '</div>'; // .testimonials-container

    } // end if

    $output = ob_get_clean();

    genesis_posts_nav();
    wp_reset_query();

    return $output;
}
