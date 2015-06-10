<?php

function rbt_custom_title_admin( $title ){
     $screen = get_current_screen();
 
     if  ( 'testimonials' == $screen->post_type ) {
          $title = 'Steve Jobs';
     }
 
     return $title;
}
add_filter( 'enter_title_here', 'rbt_custom_title_admin' );

function rbt_content_admin( $content, $post ) {

    switch( $post->post_type ) {
        case 'testimonials':
            $content = 'I absolutely love ' . get_bloginfo( 'name' ) . ', and I heartily recommend them to everyone I know!';
        break;
        default:
            $content = '';
        break;
    }

    return $content;
}
add_filter( 'default_content', 'rbt_content_admin', 10, 2 );

/**
 * Remove the WordPress SEO metabox
 */
function rbt_remove_wp_seo_meta_box() {
    remove_meta_box( 'wpseo_meta', 'testimonials', 'normal' ); // change custom-post-type into the name of your custom post type
}
add_action( 'add_meta_boxes', 'rbt_remove_wp_seo_meta_box', 100000 );