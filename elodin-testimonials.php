<?php
/*
	Plugin Name: Elodin Testimonials
	Plugin URI: https://github.com/jonschr/elodin-testimonials
	Description: Just another testimonials plugin
	Version: 1.3
    Author: Jon Schroeder
    Author URI: https://elod.in

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
define( 'ELODIN_TESTIMONIALS', dirname( __FILE__ ) );

// Define the version of the plugin
define ( 'ELODIN_TESTIMONIALS_VERSION', '1.3' );

//* Register the post type
include_once( 'lib/post_type.php' );

//* Register the taxonomies
include_once( 'lib/taxonomy.php' );

//* Customize the admin panel
include_once( 'lib/admin.php' );

//* Admin Columns Pro settings
include_once( 'lib/admin_columns.php' );

//* Add a shortcode
include_once( 'templates/shortcode.php' );

// Custom meta (using the CMB library)
include_once( 'lib/metabox/metabox.php' );

//* Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'testimonials_add_scripts' );
function testimonials_add_scripts() {

    wp_register_style( 
        'testimonials-style', 
        plugins_url( '/css/testimonials-style.css', __FILE__), 
        array(), 
        ELODIN_TESTIMONIALS_VERSION 
    );

    wp_register_script(
		'muuri-main',
		plugin_dir_url( __FILE__ ) . 'vendor/muuri/muuri.min.js',
		array( 'jquery' ),
		ELODIN_TESTIMONIALS_VERSION,
		true
    );
    
    ///////////
	// SLICK //
	///////////

	wp_register_script(
		'slick-main-script',
		plugin_dir_url( __FILE__ ) . 'vendor/slick/slick.js',
		array( 'jquery' ),
		ELODIN_TESTIMONIALS_VERSION,
		true
	);

	wp_register_style(
		'slick-main-style',
		plugin_dir_url( __FILE__ ) . 'vendor/slick/slick.css',
		array(),
		ELODIN_TESTIMONIALS_VERSION
	);

	wp_register_style(
		'slick-theme-style',
		plugin_dir_url( __FILE__ ) . 'vendor/slick/slick-theme.css',
		array(),
		CHILD_THEME_VERSION
	);

}

//* Layouts
require_once( 'layout/testimonial-grid.php' );
require_once( 'layout/testimonial-slider.php' );

//* Testimonials archive template
function testimonials_archive_template( $archive_template ) {
     global $post;

     if ( is_post_type_archive ( 'testimonials' ) || is_tax( 'testimonialcategories' ) ) {
          $archive_template = dirname( __FILE__ ) . '/templates/archive-testimonials.php';
     }
     return $archive_template;
}

add_filter( 'archive_template', 'testimonials_archive_template' ) ;

//* Set the number of testimonials on the archive
function rb_testimonials_query( $query ) {
    if ( is_admin() || ! $query->is_main_query() )
        return;

    if ( is_post_type_archive( 'testimonials' ) || is_tax( 'testimonialcategories' ) ) {
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

/**
 * Add an image size
 */
add_image_size( 'rbt_testimonials_image_square', 160, 160, true );

/**
 * Return Section (for template selection)
 * @link http://www.billerickson.net/code/helper-function-for-template-include-and-body-class/
 *
 * @param null
 * @return string
 */
function redblue_testimonials_return_section() {
    if ( is_post_type_archive( 'testimonials' ) )
        return 'archive-testimonials'; // we are returning the name of the template file with the .php stripped
    if ( is_singular( 'testimonials' ) )
        return 'single-testimonials';
    return false;
}
/**
 * Template Chooser
 * Use CPT archive templates for taxonomies
 * @link http://www.billerickson.net/code/use-same-template-for-taxonomy-and-cpt-archive/
 *
 * @param string, default template path
 * @return string, modified template path
 *
 */
add_filter( 'template_include', 'redblue_testimonials_template_chooser' );
function redblue_testimonials_template_chooser( $template ) {
    if ( redblue_testimonials_return_section() ) {

        //* Get the filename of the location in the theme where the override template would live
        $template_in_theme = get_query_template( redblue_testimonials_return_section() );
        // echo 'Theme template method 1: ' . $template_in_theme . '</br>';

        //* Get the filename of the location in the plugin where our default template lives
        $template_in_plugin = dirname( __FILE__ ) . '/templates/' . redblue_testimonials_return_section() . '.php';
        // echo 'Plugin template: ' . $template_in_plugin . '</br>';

        //* If this specific template is in the theme, we'll use that as our first choice
        if ( file_exists( $template_in_theme ) )
            return $template_in_theme;

        //* If this specific template is in the plugin, we'll use that next
        if ( file_exists( $template_in_plugin ) )
            return $template_in_plugin;
    }
    //* If we don't have either of those, we'll just return whatever the original $template value was
    return $template;
}
/**
 * Section Body Classes
 * @author Bill Erickson
 *
 * @param array $classes
 * @return array
 */
add_filter( 'body_class', 'redblue_testimonials_section_body_classes' );
function redblue_testimonials_section_body_classes( $classes ) {
    if ( redblue_testimonials_return_section() )
        $classes[] = 'section-' . redblue_testimonials_return_section();
    return $classes;
}
