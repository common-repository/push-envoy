<?php

function push_load_footer_inc(){
    
        $saved_opts = get_option("push_en_settings");
        //// Get app ID ////
        $appid=$saved_opts['app_id'];
        
        wp_register_script('push_not_ext_js', "https://pushenvoy.com/app/integration/build/".$appid, array(), '', true);
        wp_enqueue_script('push_not_ext_js');
    
}