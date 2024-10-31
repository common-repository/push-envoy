<?php

function  push_envoy_admin_menus(){
    //add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position)
    add_menu_page("Push Envoy Dashboard","PushEnvoy" ,"edit_theme_options","push_en_io_dash","push_envoy_dash_page");
       add_submenu_page( 'push_en_io_dash', 'Send New Message', "Instant Message", "edit_theme_options", "push_en_io_send_msg", "push_envoy_io_send_msg");
       add_submenu_page( 'push_en_io_dash', 'Push Segmentation', "Segmentation", "edit_theme_options", "push_en_io_segment", "push_envoy_io_segment");
    add_submenu_page( 'push_en_io_dash', 'Push Settings', "Settings", "edit_theme_options", "push_en_io_settings", "push_envoy_io_settings");
  
}?>