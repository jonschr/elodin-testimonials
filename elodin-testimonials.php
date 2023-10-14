<?php
/*
	Plugin Name: Elodin Testimonials
	Plugin URI: https://github.com/jonschr/elodin-testimonials
	Description: Just another testimonials plugin
	Version: 1.10.1
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
define( 'ELODIN_TESTIMONIALS_DIR', plugin_dir_path( __FILE__ ) );
define( 'ELODIN_TESTIMONIALS_PATH', plugin_dir_url( __FILE__ ) );

// Define the version of the plugin
define ( 'ELODIN_TESTIMONIALS_VERSION', '1.10.1' );

//* Register the post type
include_once( 'lib/post-type.php' );

//* Register the taxonomies
include_once( 'lib/taxonomy.php' );

//* Redirect the single template to /testimonials
include_once( 'lib/single-redirect.php' );

//* Admin Columns Pro settings
include_once( 'lib/admin-columns.php' );

//* Add a link to documentation in menu
include_once( 'lib/documentation-sidebar-link.php' );

//* Custom meta (using the CMB library)
include_once( 'vendor/metabox/metabox.php' );

//* Layouts
require_once( 'layout/testimonial-grid.php' );
require_once( 'layout/testimonial-slider.php' );

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
		plugin_dir_url( __FILE__ ) . 'vendor/slick/slick.min.js',
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
		ELODIN_TESTIMONIALS_VERSION
	);

}

/////////
// ACP //
/////////

use AC\ListScreenRepository\Storage\ListScreenRepositoryFactory;
use AC\ListScreenRepository\Rules;
use AC\ListScreenRepository\Rule;
add_filter( 'acp/storage/repositories', function( array $repositories, ListScreenRepositoryFactory $factory ) {
    
    //! Change $writable to true to allow changes to columns for the content types below
    $writable = false;
    
    // 2. Add rules to target individual list tables.
    // Defaults to Rules::MATCH_ANY added here for clarity, other option is Rules::MATCH_ALL
    $rules = new Rules( Rules::MATCH_ANY );
    $rules->add_rule( new Rule\EqualType( 'testimonials' ) );
    
    // 3. Register your repository to the stack
    $repositories['rent-fetch'] = $factory->create(
        ELODIN_TESTIMONIALS_DIR . '/acp-settings',
        $writable,
        $rules
    );
    
    return $repositories;
    
}, 10, 2 );

// Updater
require 'vendor/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/jonschr/elodin-testimonials',
	__FILE__,
	'elodin-testimonials'
);

// Optional: Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');