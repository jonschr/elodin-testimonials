<?php

/**
 * Add a redirect from the single template to the archive
 */
add_action( 'template_redirect', 'rb_redirect_testimonials_single_to_archive' );
 function rb_redirect_testimonials_single_to_archive() {
    if ( ! is_singular( 'testimonials' ) )
        return;

    wp_redirect( '/testimonials/', 301 );
    exit;
}
