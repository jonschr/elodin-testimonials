<?php 

add_action('admin_menu', 'testimonials_add_documentation_link');

function testimonials_add_documentation_link() {
    
    global $submenu;
    $menu_slug = "edit.php?post_type=testimonials"; // used as "key" in menus
   
    $submenu[$menu_slug][] = array(
        'Documentation', 
        'manage_options', 
        'https://github.com/jonschr/elodin-testimonials',
    );
}

add_action( 'admin_footer', 'testimonials_admin_menu_open_new_tab' );    
function testimonials_admin_menu_open_new_tab() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#menu-posts-testimonials li a').each(function () {
            if ($(this).text() == 'Documentation') {
                $(this).css('color', 'yellow');
                $(this).attr('target','_blank');
            }
        });
    });
    </script>
    <?php
}

