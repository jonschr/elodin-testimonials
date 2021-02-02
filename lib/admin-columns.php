<?php

function ac_custom_column_settings_241c5b99() {

	ac_register_columns( 'testimonials', array(
		array(
			'columns' => array(
				'5e0eb1ac55bbf' => array(
					'type' => 'column-featured_image',
					'label' => 'Image',
					'width' => '120',
					'width_unit' => 'px',
					'featured_image_display' => 'image',
					'image_size' => 'cpac-custom',
					'image_size_w' => '100',
					'image_size_h' => '100',
					'edit' => 'on',
					'sort' => 'on',
					'filter' => 'off',
					'filter_label' => '',
					'name' => '5e0eb1ac55bbf',
					'label_type' => '',
					'bulk-editing' => '',
					'export' => '',
					'search' => ''
				),
				'title' => array(
					'type' => 'title',
					'label' => 'Name',
					'width' => '400',
					'width_unit' => 'px',
					'edit' => 'on',
					'sort' => 'on',
					'name' => 'title',
					'label_type' => '',
					'bulk-editing' => '',
					'export' => '',
					'search' => ''
				),
				'5e0eb1ac5ca8d' => array(
					'type' => 'column-meta',
					'label' => 'Job title',
					'width' => '200',
					'width_unit' => 'px',
					'field' => '_rbt_testimonials_title',
					'field_type' => '',
					'before' => '',
					'after' => '',
					'edit' => 'on',
					'editable_type' => 'textarea',
					'sort' => 'on',
					'filter' => 'off',
					'filter_label' => '',
					'name' => '5e0eb1ac5ca8d',
					'label_type' => '',
					'export' => '',
					'search' => ''
				),
				'5e0eb29330426' => array(
					'type' => 'column-content',
					'label' => 'Content',
					'width' => '',
					'width_unit' => '%',
					'string_limit' => 'word_limit',
					'excerpt_length' => '10',
					'before' => '',
					'after' => '',
					'edit' => 'on',
					'sort' => 'on',
					'filter' => 'off',
					'filter_label' => '',
					'name' => '5e0eb29330426',
					'label_type' => '',
					'bulk-editing' => '',
					'export' => '',
					'search' => ''
				),
				'taxonomy-testimonialcategories' => array(
					'type' => 'taxonomy-testimonialcategories',
					'label' => 'Category',
					'width' => '',
					'width_unit' => '%',
					'edit' => 'on',
					'enable_term_creation' => 'on',
					'sort' => 'on',
					'filter' => 'off',
					'filter_label' => '',
					'name' => 'taxonomy-testimonialcategories',
					'label_type' => '',
					'export' => '',
					'search' => ''
				),
				'5e0eb1eb4bfa6' => array(
					'type' => 'column-meta',
					'label' => 'URL (optional)',
					'width' => '',
					'width_unit' => '%',
					'field' => '_edit_lock',
					'field_type' => '',
					'before' => '',
					'after' => '',
					'edit' => 'on',
					'editable_type' => 'textarea',
					'sort' => 'on',
					'filter' => 'off',
					'filter_label' => '',
					'name' => '5e0eb1eb4bfa6',
					'label_type' => '',
					'export' => '',
					'search' => ''
				),
			),
			
		)
	) );
}
add_action( 'ac/ready', 'ac_custom_column_settings_241c5b99' );