<?php

add_shortcode( 'testimonials', 'testimonials_shortcode_loop' );

function testimonials_shortcode_loop( $atts ) {

    $atts = shortcode_atts(
        array(
            'post_type' => 'testimonials',
            'posts_per_page' => -1,
        ), $atts, 'testimonials'
    );

   //  extract( shortcode_atts( array(
   //      'post_type' => 'testimonials',
   //      'posts_per_page' => -1,
   // ), $args ) );


   $cpt_query = new WP_Query( $atts );

    global $paged;

    ob_start();

    if ( $cpt_query->have_posts() ) {

        wp_enqueue_style( 'testimonials-style' );

        echo '<div class="testimonials-container">';

    	while ( $cpt_query->have_posts() ) {

    		$cpt_query->the_post();
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

        echo '</div>'; // .testimonials-container

    } // end if

    $output = ob_get_clean();

    genesis_posts_nav();
    wp_reset_query();

    return $output;
}
