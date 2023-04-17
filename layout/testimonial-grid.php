<?php

//* Output testimonials before
add_action( 'before_loop_layout_testimonial_grid', 'rb_testimonial_grid_before' );
function rb_testimonial_grid_before( $args ) {

    wp_enqueue_style( 'testimonials-style' );
    
    //* Do the muuri main script
    wp_enqueue_script( 'muuri-main' );

    $rand = rand( 1, 100000 );

    printf( '<div id="muuri-%s">', $rand ); // use a random number to allow multiple on a page
    
    ?>
    <script>
        jQuery(document).ready(function( $ ) {
	            
            var grid = new Muuri('#muuri-<?php echo $rand; ?> .loop-layout-testimonial_grid', {
                layout: {
                    fillGaps: true,
                }
            });
        });
    </script>

    
    <?php
    
}

//* Close the markup after
add_action( 'after_loop_layout_testimonial_grid', 'wm_testimonial_grid_afer' );
function wm_testimonial_grid_afer( $args ) {
    echo '</div>';
}

//* Output each testimonials
add_action( 'add_loop_layout_testimonial_grid', 'rb_testimonial_grid_each' );
function rb_testimonial_grid_each() {

	//* Global vars
	global $post;
	$id = get_the_ID();

	//* Vars
	$title = esc_html( get_the_title() );
    $permalink = esc_url( get_the_permalink() );
    $content = apply_filters( 'the_content', apply_filters( 'the_content', get_the_content() ) );
    $reference = esc_html( get_post_meta( $id, '_rbt_testimonials_title', true ) );
    $url = esc_url( get_post_meta( $id, '_rbt_testimonials_url', true ) );
    $background = get_the_post_thumbnail_url( get_the_ID(), 'large' );
    $featuredclass = null;
    
    if ( has_post_thumbnail() )
        $featuredclass = 'has-featured-image';

    //* Markup
    printf( '<div class="content-container %s">', $featuredclass );

        if ( $content ) {

            printf( '<div class="testimonial-content">%s', $content );

                edit_post_link('Edit testimonial', '<small>', '</small>', $id, 'edit-link');
                
                if ( $background ) 
                    printf( '<div class="featured-image" style="background-image:url( %s )"></div>', $background );
            
            echo '</div>';
        }
        
       
            
        if ( $title )
            printf( '<h3 class="title">%s</h3>', $title );

        if ( $reference )
            printf( '<p class="title">%s</p>', $reference );

        if ( $url )
            printf( '<a class="website-link" target="_blank" href="%s"></a>', $url );

    echo '</div>';
    
    // printf( '<a class="link-overlay" href="%s"></a>', $permalink );

    // if ( has_post_thumbnail() ) 
    //     printf( '<div class="featured-image" style="background-image:url( %s )"></div>', get_the_post_thumbnail_url( $post_id, 'large' ) );
}