<?php

function push_envoy_initialize_admin(){
    
     wp_register_style('push_envoy_date_css', plugins_url("/passets/css/datepicker.css", PUSH_EN_IO_URL));
    wp_register_script('push_envoy_moment_js', plugins_url('/passets/js/moment.js', PUSH_EN_IO_URL), array('jquery'), '', true);
    wp_register_script('push_envoy_app_js', plugins_url('/passets/js/app.js', PUSH_EN_IO_URL), array('jquery'), '1.0', true);
     wp_register_script('push_envoy_date_js', plugins_url('/passets/js/daterangepicker.js', PUSH_EN_IO_URL), array('jquery'), '', true);
     
     wp_enqueue_style('push_envoy_date_css');
    wp_enqueue_script('push_envoy_moment_js');
    wp_enqueue_script('push_envoy_date_js');
    wp_enqueue_script('push_envoy_app_js');
    
    
    include('create_post_metabox.php');
    include('create_segment_metabox.php');
    
    
    add_action('add_meta_boxes','push_envoy_create_post_meta_box');
    add_action('add_meta_boxes','push_envoy_create_segment_meta_box');
  
    add_action("admin_post_push_en_io_set_submit","save_push_envoy_settings");
    add_action("admin_post_push_en_io_msg_submit","send_push_envoy_msg");
    add_action("admin_post_push_en_io_sync_cat","push_en_io_sync_cat");
    add_action("admin_post_push_en_io_segment_submit","send_push_envoy_segment");

}
?>