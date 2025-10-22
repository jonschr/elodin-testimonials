<?php

add_action( 'admin_menu', 'testimonials_add_bulk_upload_page' );

function testimonials_find_existing_attachment( $image_url ) {
    // First, check if this exact URL is already in the media library
    $attachment_id = attachment_url_to_postid( $image_url );
    if ( $attachment_id ) {
        return $attachment_id;
    }

    // Get the filename from the external URL
    $filename = basename( parse_url( $image_url, PHP_URL_PATH ) );
    if ( empty( $filename ) ) {
        return false;
    }

    // Download the external image temporarily
    $tmp_file = download_url( $image_url );
    if ( is_wp_error( $tmp_file ) ) {
        return false;
    }

    // Get external image properties
    $external_size = filesize( $tmp_file );
    $external_hash = md5_file( $tmp_file );

    // Get image dimensions if possible
    $external_dimensions = @getimagesize( $tmp_file );
    $external_width = $external_dimensions ? $external_dimensions[0] : 0;
    $external_height = $external_dimensions ? $external_dimensions[1] : 0;

    // Query for attachments with similar properties
    $args = array(
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'posts_per_page' => -1,
        'post_mime_type' => 'image',
        'meta_query'     => array(
            'relation' => 'OR',
            array(
                'key'     => '_wp_attached_file',
                'value'   => $filename,
                'compare' => 'LIKE'
            ),
            array(
                'key'     => '_original_file_hash',
                'value'   => $external_hash,
                'compare' => '='
            )
        )
    );

    $attachments = get_posts( $args );

    foreach ( $attachments as $attachment ) {
        $attachment_path = get_attached_file( $attachment->ID );

        // Check stored original hash first (most reliable)
        $stored_hash = get_post_meta( $attachment->ID, '_original_file_hash', true );
        if ( $stored_hash === $external_hash ) {
            @unlink( $tmp_file );
            return $attachment->ID;
        }

        // Check current file hash as fallback
        if ( file_exists( $attachment_path ) ) {
            $local_size = filesize( $attachment_path );
            $local_hash = md5_file( $attachment_path );

            // Compare file sizes and hashes
            if ( $external_size === $local_size && $external_hash === $local_hash ) {
                @unlink( $tmp_file );
                return $attachment->ID;
            }

            // Check dimensions as additional verification
            $local_dimensions = @getimagesize( $attachment_path );
            if ( $local_dimensions &&
                 $external_width === $local_dimensions[0] &&
                 $external_height === $local_dimensions[1] &&
                 $external_size === $local_size ) {
                @unlink( $tmp_file );
                return $attachment->ID;
            }
        }
    }

    @unlink( $tmp_file );
    return false;
}

function testimonials_handle_featured_image( $post_id, $image_url ) {
    // Check if the image URL is from this site
    $site_url = get_site_url();
    if ( strpos( $image_url, $site_url ) === 0 ) {
        // It's already a local image, check if it's an attachment
        $attachment_id = attachment_url_to_postid( $image_url );
        if ( $attachment_id ) {
            set_post_thumbnail( $post_id, $attachment_id );
            return $attachment_id;
        }
        // If it's a local URL but not an attachment, we can't handle it
        return new WP_Error( 'invalid_local_image', 'Local image URL is not a valid attachment.' );
    }

    // Check if this external image already exists in the media library
    $existing_attachment_id = testimonials_find_existing_attachment( $image_url );
    if ( $existing_attachment_id ) {
        set_post_thumbnail( $post_id, $existing_attachment_id );
        return $existing_attachment_id;
    }

    // It's an external image that doesn't exist locally, download it
    $tmp_file = download_url( $image_url );
    if ( is_wp_error( $tmp_file ) ) {
        return $tmp_file;
    }

    // Get the filename from the URL
    $filename = basename( parse_url( $image_url, PHP_URL_PATH ) );
    if ( empty( $filename ) ) {
        $filename = 'testimonial-image-' . time() . '.jpg';
    }

    // Get file type
    $file_type = wp_check_filetype( $filename );
    if ( ! $file_type['type'] ) {
        @unlink( $tmp_file );
        return new WP_Error( 'invalid_file_type', 'Could not determine file type.' );
    }

    // Upload the file to media library
    $upload_dir = wp_upload_dir();
    $new_filename = wp_unique_filename( $upload_dir['path'], $filename );
    $new_file = $upload_dir['path'] . '/' . $new_filename;

    if ( ! copy( $tmp_file, $new_file ) ) {
        @unlink( $tmp_file );
        return new WP_Error( 'upload_failed', 'Could not copy file to upload directory.' );
    }

    @unlink( $tmp_file );

    // Create attachment
    $attachment = array(
        'post_mime_type' => $file_type['type'],
        'post_title'     => sanitize_file_name( $filename ),
        'post_content'   => '',
        'post_status'    => 'inherit'
    );

    $attachment_id = wp_insert_attachment( $attachment, $new_file, $post_id );
    if ( is_wp_error( $attachment_id ) ) {
        return $attachment_id;
    }

    // Store the original file hash for future deduplication
    update_post_meta( $attachment_id, '_original_file_hash', md5_file( $tmp_file ) );

    // Generate metadata
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    $attachment_data = wp_generate_attachment_metadata( $attachment_id, $new_file );
    wp_update_attachment_metadata( $attachment_id, $attachment_data );

    // Set as featured image
    set_post_thumbnail( $post_id, $attachment_id );

    return $attachment_id;
}

function testimonials_export_to_csv() {
    if ( ! isset( $_GET['testimonials_export'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'testimonials_export' ) ) {
        return;
    }

    // Query all testimonials
    $args = array(
        'post_type'      => 'testimonials',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'orderby'        => 'ID',
        'order'          => 'ASC',
    );

    $testimonials = get_posts( $args );

    // Set headers for CSV download
    header( 'Content-Type: text/csv; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename=testimonials-export-' . date( 'Y-m-d' ) . '.csv' );

    // Create output stream
    $output = fopen( 'php://output', 'w' );

    // Write CSV headers
    fputcsv( $output, array( 'id', 'testimonial', 'firstname', 'lastname', 'title', 'url', 'photo', 'category' ) );

    // Write testimonial data
    foreach ( $testimonials as $testimonial ) {
        $testimonial_content = $testimonial->post_content;
        $testimonial_title = $testimonial->post_title;

        // Try to split title into firstname/lastname if it contains a space
        $name_parts = explode( ' ', $testimonial_title, 2 );
        $firstname = isset( $name_parts[0] ) ? $name_parts[0] : '';
        $lastname = isset( $name_parts[1] ) ? $name_parts[1] : '';

        // Get meta fields
        $title_meta = get_post_meta( $testimonial->ID, '_rbt_testimonials_title', true );
        $url_meta = get_post_meta( $testimonial->ID, '_rbt_testimonials_url', true );

        // Get featured image URL
        $photo_meta = '';
        $thumbnail_id = get_post_thumbnail_id( $testimonial->ID );
        if ( $thumbnail_id ) {
            $photo_meta = wp_get_attachment_url( $thumbnail_id );
        }

        // Get categories (testimonialcategories taxonomy)
        $categories = wp_get_post_terms( $testimonial->ID, 'testimonialcategories', array( 'fields' => 'names' ) );
        $category_string = ! empty( $categories ) ? implode( ', ', $categories ) : '';

        // Write row
        fputcsv( $output, array(
            $testimonial->ID,
            $testimonial_content,
            $firstname,
            $lastname,
            $title_meta,
            $url_meta,
            $photo_meta,
            $category_string,
        ) );
    }

    fclose( $output );
    exit;
}
add_action( 'admin_init', 'testimonials_export_to_csv' );

function testimonials_add_bulk_upload_page() {
    add_submenu_page(
        'edit.php?post_type=testimonials', // parent slug
        'Bulk Upload Testimonials', // page title
        'Bulk Upload', // menu title
        'manage_options', // capability
        'testimonials-bulk-upload', // menu slug
        'testimonials_bulk_upload_page_callback' // callback function
    );
}

function testimonials_process_csv_upload() {
    if ( ! isset( $_POST['upload_csv'] ) || ! isset( $_FILES['testimonials_csv'] ) ) {
        return;
    }

    // Check nonce for security
    if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'testimonials_bulk_upload' ) ) {
        wp_die( 'Security check failed.' );
    }

    $file = $_FILES['testimonials_csv'];

    // Validate file
    if ( $file['error'] !== UPLOAD_ERR_OK ) {
        wp_die( 'File upload error: ' . $file['error'] );
    }

    if ( pathinfo( $file['name'], PATHINFO_EXTENSION ) !== 'csv' ) {
        wp_die( 'Please upload a CSV file.' );
    }

    // Process CSV
    $handle = fopen( $file['tmp_name'], 'r' );
    if ( ! $handle ) {
        wp_die( 'Could not open uploaded file.' );
    }

    $header = fgetcsv( $handle );
    $expected_headers = array( 'id', 'testimonial', 'firstname', 'lastname', 'title', 'url', 'photo', 'category' );

    // Map columns based on available headers
    $column_map = array();
    $missing_headers = array();
    $found_headers = array();

    foreach ( $expected_headers as $expected_header ) {
        $index = array_search( $expected_header, $header );
        if ( $index !== false ) {
            $column_map[$expected_header] = $index;
            $found_headers[] = $expected_header;
        } else {
            $missing_headers[] = $expected_header;
        }
    }

    // Check if we have at least the testimonial column
    if ( ! isset( $column_map['testimonial'] ) ) {
        fclose( $handle );
        wp_die( 'CSV must contain at least a "testimonial" column.' );
    }

    // Store header info for results
    $header_info = array(
        'found' => $found_headers,
        'missing' => $missing_headers,
    );

    $imported_count = 0;
    $updated_count = 0;
    $errors = array();

    while ( ( $row = fgetcsv( $handle ) ) !== false ) {
        // Skip rows that don't have enough columns for the testimonial
        if ( ! isset( $row[ $column_map['testimonial'] ] ) || empty( $row[ $column_map['testimonial'] ] ) ) {
            continue;
        }

        // Extract data using column map
        $id = isset( $column_map['id'] ) ? intval( $row[ $column_map['id'] ] ) : 0;
        $testimonial = isset( $column_map['testimonial'] ) ? $row[ $column_map['testimonial'] ] : '';
        $firstname = isset( $column_map['firstname'] ) ? $row[ $column_map['firstname'] ] : '';
        $lastname = isset( $column_map['lastname'] ) ? $row[ $column_map['lastname'] ] : '';
        $title = isset( $column_map['title'] ) ? $row[ $column_map['title'] ] : '';
        $url = isset( $column_map['url'] ) ? $row[ $column_map['url'] ] : '';
        $photo = isset( $column_map['photo'] ) ? $row[ $column_map['photo'] ] : '';
        $category = isset( $column_map['category'] ) ? $row[ $column_map['category'] ] : '';

        // Create post title from firstname and lastname
        $post_title = trim( $firstname . ' ' . $lastname );
        // Leave title blank if no name is provided

        // Check if this is an update (has valid existing ID) or new creation
        $existing_post = $id > 0 ? get_post( $id ) : null;
        if ( $existing_post && $existing_post->post_type === 'testimonials' ) {
            // Update existing testimonial
            $post_data = array(
                'ID'           => $id,
                'post_title'   => $post_title,
                'post_content' => $testimonial,
            );

            $post_id = wp_update_post( $post_data );

            if ( is_wp_error( $post_id ) ) {
                $errors[] = 'Failed to update testimonial ID ' . $id . ': ' . $post_id->get_error_message();
                continue;
            }

            $action = 'updated';
        } else {
            // Create new testimonial (including when ID is invalid/non-existent)
            $post_data = array(
                'post_title'   => $post_title,
                'post_content' => $testimonial,
                'post_status'  => 'publish',
                'post_type'    => 'testimonials',
            );

            $post_id = wp_insert_post( $post_data );

            if ( is_wp_error( $post_id ) ) {
                $errors[] = 'Failed to create testimonial: ' . $post_id->get_error_message();
                continue;
            }

            $action = 'created';
        }

        // Update meta fields
        if ( ! empty( $title ) ) {
            update_post_meta( $post_id, '_rbt_testimonials_title', $title );
        } else {
            delete_post_meta( $post_id, '_rbt_testimonials_title' );
        }

        if ( ! empty( $url ) ) {
            update_post_meta( $post_id, '_rbt_testimonials_url', $url );
        } else {
            delete_post_meta( $post_id, '_rbt_testimonials_url' );
        }

        // Process categories
        if ( ! empty( $category ) ) {
            $category_names = array_map( 'trim', explode( ',', $category ) );
            $term_ids = array();

            foreach ( $category_names as $category_name ) {
                if ( empty( $category_name ) ) {
                    continue;
                }

                // Check if term exists
                $term = term_exists( $category_name, 'testimonialcategories' );

                if ( ! $term ) {
                    // Create the term
                    $term = wp_insert_term( $category_name, 'testimonialcategories' );
                    if ( is_wp_error( $term ) ) {
                        $errors[] = 'Failed to create category "' . $category_name . '": ' . $term->get_error_message();
                        continue;
                    }
                }

                if ( isset( $term['term_id'] ) ) {
                    $term_ids[] = $term['term_id'];
                }
            }

            // Assign terms to the testimonial
            if ( ! empty( $term_ids ) ) {
                $result = wp_set_post_terms( $post_id, $term_ids, 'testimonialcategories' );
                if ( is_wp_error( $result ) ) {
                    $errors[] = 'Failed to assign categories to testimonial: ' . $result->get_error_message();
                }
            }
        } else {
            // Clear categories if none provided
            wp_set_post_terms( $post_id, array(), 'testimonialcategories' );
        }

        // Process photo/featured image
        if ( ! empty( $photo ) ) {
            $attachment_id = testimonials_handle_featured_image( $post_id, $photo );
            if ( is_wp_error( $attachment_id ) ) {
                $errors[] = 'Failed to set featured image: ' . $attachment_id->get_error_message();
            }
        } else {
            // Clear featured image if none provided
            delete_post_thumbnail( $post_id );
        }

        if ( $action === 'updated' ) {
            $updated_count++;
        } else {
            $imported_count++;
        }
    }

    fclose( $handle );

    // Store results in transient for display
    set_transient( 'testimonials_bulk_upload_results', array(
        'imported' => $imported_count,
        'updated'  => $updated_count,
        'errors'   => $errors,
        'headers'  => $header_info,
    ), 300 ); // 5 minutes

    // Redirect back to the page to show results
    wp_redirect( admin_url( 'edit.php?post_type=testimonials&page=testimonials-bulk-upload' ) );
    exit;
}
add_action( 'admin_init', 'testimonials_process_csv_upload' );

function testimonials_bulk_upload_page_callback() {
    // Display results if available
    $results = get_transient( 'testimonials_bulk_upload_results' );
    if ( $results ) {
        delete_transient( 'testimonials_bulk_upload_results' );

        $message_parts = array();
        if ( isset( $results['imported'] ) && $results['imported'] > 0 ) {
            $message_parts[] = $results['imported'] . ' testimonials created';
        }
        if ( isset( $results['updated'] ) && $results['updated'] > 0 ) {
            $message_parts[] = $results['updated'] . ' testimonials updated';
        }

        if ( ! empty( $message_parts ) ) {
            echo '<div class="notice notice-success"><p>Successfully processed: ' . implode( ', ', $message_parts ) . '.</p></div>';
        }

        if ( isset( $results['headers'] ) ) {
            $found = $results['headers']['found'];
            $missing = $results['headers']['missing'];

            if ( ! empty( $found ) ) {
                echo '<div class="notice notice-info"><p><strong>Columns found and processed:</strong> ' . implode( ', ', $found ) . '</p></div>';
            }

            if ( ! empty( $missing ) ) {
                echo '<div class="notice notice-warning"><p><strong>Columns not found (ignored):</strong> ' . implode( ', ', $missing ) . '</p></div>';
            }
        }

        if ( ! empty( $results['errors'] ) ) {
            echo '<div class="notice notice-error"><p>Errors encountered:</p><ul>';
            foreach ( $results['errors'] as $error ) {
                echo '<li>' . esc_html( $error ) . '</li>';
            }
            echo '</ul></div>';
        }
    }

    ?>
    <div class="wrap">
        <h1>Bulk Upload Testimonials</h1>
        <p>Upload a CSV file to bulk import testimonials. Include an 'id' column to update existing testimonials, or omit it to create new ones.</p>
        <p>
            <a href="<?php echo plugin_dir_url( __FILE__ ) . '../samples/sample-testimonials.csv'; ?>" download>Download Sample CSV</a> - Use this template to format your testimonials data.<br>
            <a href="<?php echo admin_url( 'edit.php?post_type=testimonials&page=testimonials-bulk-upload&testimonials_export=1&_wpnonce=' . wp_create_nonce( 'testimonials_export' ) ); ?>">Download Current Testimonials</a> - Export existing testimonials to CSV for editing and re-import.
        </p>
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field( 'testimonials_bulk_upload' ); ?>
            <input type="file" name="testimonials_csv" accept=".csv" required>
            <input type="submit" name="upload_csv" value="Upload CSV" class="button button-primary">
        </form>
    </div>
    <?php
}
