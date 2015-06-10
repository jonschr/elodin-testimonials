<?php
/**
 * Include and setup custom metaboxes and fields. (make sure you copy this file to outside the CMB2 directory)
 * http://nacin.com/2010/05/11/in-wordpress-prefix-everything/
 *
 * @category Red Blue Testimonials
 * @package  rbt_CMB2
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/WebDevStudios/CMB2
 */

/**
 * Get the bootstrap! If using the plugin from wordpress.org, REMOVE THIS!
 */

if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/cmb2/init.php';
} elseif ( file_exists( dirname( __FILE__ ) . '/CMB2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/CMB2/init.php';
}

add_action( 'cmb2_init', 'rbt_register_testimonials_metabox' );
/**
 * Hook in and add a demo metabox. Can only happen on the 'cmb2_init' hook.
 */
function rbt_register_testimonials_metabox() {

	// Start with an underscore to hide fields from custom fields list
	$prefix = '_rbt_';

	/**
	 * Sample metabox to demonstrate each field type included
	 */
	$rbt = new_cmb2_box( array(
		'id'            => $prefix . 'testimonials_metabox',
		'title'         => __( 'Testimonial details', 'cmb2' ),
		'object_types'  => array( 'testimonials', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'cmb_styles' => true, // false to disable the CMB stylesheet
		'closed'     => false, // true to keep the metabox closed by default
	) );

	$rbt->add_field( array(
		'name' => __( 'Title & Company', 'cmb2' ),
		'desc' => __( "<p>The text you would like to appear below the testimonial author's name, e.g. <b>CEO of Apple, Inc.</b></p>", 'cmb2' ),
		'id'   => $prefix . 'testimonials_title',
		'type' => 'text_medium',
		'repeatable' => false,
		// 'after_field'  => "<p>The text you would like to appear below the testimonial author's name, e.g. <b>CEO of Apple, Inc.</b></p>",
	) );

	$rbt->add_field( array(
		'name' => __( 'Website URL', 'cmb2' ),
		'desc' => __( 'The full website address for the testimonial author, e.g. <b>http://apple.com</b>', 'cmb2' ),
		'id'   => $prefix . 'testimonials_url',
		'type' => 'text_url',
		'protocols' => array( 'http', 'https', ), // Array of allowed protocols
		'repeatable' => false,
		// 'after_field'  => '<p>The full website address for the testimonial author, e.g. <b>http://apple.com</b></p>',
	) );
}

add_action( 'cmb2_init', 'yourprefix_register_theme_options_metabox' );
/**
 * Hook in and register a metabox to handle a theme options page
 */
function yourprefix_register_theme_options_metabox() {
	// Start with an underscore to hide fields from custom fields list
	$option_key = '_yourprefix_theme_options';
	/**
	 * Metabox for an options page. Will not be added automatically, but needs to be called with
	 * the `cmb2_metabox_form` helper function. See wiki for more info.
	 */
	$cmb_options = new_cmb2_box( array(
		'id'      => $option_key . 'page',
		'title'   => __( 'Theme Options Metabox', 'cmb2' ),
		'hookup'  => false, // Do not need the normal user/post hookup
		'show_on' => array(
			// These are important, don't remove
			'key'   => 'options-page',
			'value' => array( $option_key )
		),
	) );
	/**
	 * Options fields ids only need
	 * to be unique within this option group.
	 * Prefix is not needed.
	 */
	$cmb_options->add_field( array(
		'name'    => __( 'Site Background Color', 'cmb2' ),
		'desc'    => __( 'field description (optional)', 'cmb2' ),
		'id'      => 'bg_color',
		'type'    => 'colorpicker',
		'default' => '#ffffff',
	) );
}