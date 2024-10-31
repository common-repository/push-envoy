<?php

function push_io_script_style() {

    $allowed_pages = array('push_en_io_settings', 'push_en_io_send_msg');
    ///// Don't load styles  outside this plugin///
    if (!isset($_GET['page']) && !@in_array($_GET['page'], $allowed_pages)) {

        return;
    }

    wp_register_style('push_boostrap_css', plugins_url("/passets/css/bootstrap.css", PUSH_EN_IO_URL));
    wp_register_style('push_admin_css', plugins_url("/passets/css/adminclass.css", PUSH_EN_IO_URL));
    wp_register_script('push_boostrap_js', plugins_url('/passets/js/bootstrap.min.js', PUSH_EN_IO_URL), array('jquery'), '1.0', true);

    wp_register_script('push_app_js', plugins_url('/passets/js/app.js', PUSH_EN_IO_URL), array('jquery'), '1.0', true);

    wp_enqueue_style('push_boostrap_css');
    wp_enqueue_style('push_admin_css');
    wp_enqueue_script('push_boostrap_js');
    wp_enqueue_script('push_app_js');
    
    
    wp_enqueue_style( 'jquery-ui-datepicker' );
 

    // jQuery
// This will enqueue the Media Uploader script
    wp_enqueue_media();
}

?>