<?php
/*
	Plugin Name: Red Blue Testimonials
	Plugin URI: http://redblue.us
	Description: Just another testimonials plugin
	Version: 1.1
    Author: Jon Schroeder
    Author URI: http://redblue.us

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.
*/

// Plugin directory 
define( 'RBT_DIR', dirname( __FILE__ ) );

//* Register the post type
include_once( 'lib/post_type.php' );

//* Customize the admin panel
include_once( 'lib/admin.php' );

//* Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'testimonials_add_scripts' );
function testimonials_add_scripts() {
    
    wp_register_style( 'testimonials-style', plugins_url( '/css/testimonials-style.css', __FILE__) );
    wp_enqueue_style( 'testimonials-style' );

}

//* Testimonials archive template
function testimonials_archive_template( $archive_template ) {
     global $post;

     if ( is_post_type_archive ( 'testimonials' ) ) {
          $archive_template = dirname( __FILE__ ) . '/templates/archive-testimonials.php';
     }
     return $archive_template;
}

add_filter( 'archive_template', 'testimonials_archive_template' ) ;

//* Set the number of testimonials on the archive
function rb_testimonials_query( $query ) {
    if ( is_admin() || ! $query->is_main_query() )
        return;

    if ( is_post_type_archive( 'testimonials' ) ) {
        $query->set( 'posts_per_page', -1 );
        return;
    }
}
add_action( 'pre_get_posts', 'rb_testimonials_query', 1 );

/**
 * Add a redirect from the single template to the archive
 */
function rb_redirect_testimonials_single_to_archive()
{
    if ( ! is_singular( 'testimonials' ) )
        return;

    wp_redirect( get_post_type_archive_link( 'testimonials' ), 301 );
    exit;
}
add_action( 'template_redirect', 'rb_redirect_testimonials_single_to_archive' );