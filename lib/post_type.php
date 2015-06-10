<?php

function testimonials_register_post_type() {
	$labels = array(
		'name' => 'Testimonials',
		'singular_name' => 'Testimonial',
		'add_new' => 'Add new',
		'add_new_item' => 'Add new Testimonial',
		'edit_item' => 'Edit Testimonial',
		'new_item' => 'New Testimonial',
		'view_item' => 'View Testimonial',
		'search_items' => 'Search Testimonials',
		'not_found' =>  'No Testimonials found',
		'not_found_in_trash' => 'No Testimonials found in trash',
		'parent_item_colon' => '',
		'menu_name' => 'Testimonials'
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true, 
		'show_in_menu' => true, 
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'rewrite' => array( 'slug' => 'testimonials' ),
		'has_archive' => true, 
		'hierarchical' => false,
		'menu_position' => null,
		'menu_icon' => 'dashicons-format-chat',
		'supports' => array( 'title', 'editor', 'genesis-cpt-archives-settings', 'thumbnail' )
	); 

	register_post_type( 'testimonials', $args );

}
add_action( 'init', 'testimonials_register_post_type' );	