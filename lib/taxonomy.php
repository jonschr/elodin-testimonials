<?php

add_action( 'init', 'elodin_testimonials_register_tax', 0 );
function elodin_testimonials_register_tax() {

	$labels = array(
		'name'                       => _x( 'Testimonial Categories', 'Testimonial Categories', 'redbluetestimonials' ),
		'singular_name'              => _x( 'Testimonial Category', 'Testimonial Category', 'redbluetestimonials' ),
		'search_items'               => __( 'Search categories', 'redbluetestimonials' ),
		'popular_items'              => __( 'Popular categories', 'redbluetestimonials' ),
		'all_items'                  => __( 'All categories', 'redbluetestimonials' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Category', 'redbluetestimonials' ),
		'update_item'                => __( 'Update Category', 'redbluetestimonials' ),
		'add_new_item'               => __( 'Add New Category', 'redbluetestimonials' ),
		'new_item_name'              => __( 'New Category Name', 'redbluetestimonials' ),
		'separate_items_with_commas' => __( 'Separate testimonial categories with commas', 'redbluetestimonials' ),
		'add_or_remove_items'        => __( 'Add or remove testimonial categories', 'redbluetestimonials' ),
		'choose_from_most_used'      => __( 'Choose from the most used testimonial categories', 'redbluetestimonials' ),
		'not_found'                  => __( 'No testimonial categories found.', 'redbluetestimonials' ),
		'menu_name'                  => __( 'Categories', 'redbluetestimonials' ),
	);

	$args = array(
		'hierarchical'          => true,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'testimonial-category' ),
	);

	register_taxonomy( 'testimonialcategories', 'testimonials', $args );
}
