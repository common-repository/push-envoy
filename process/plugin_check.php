<?php

function check_if_setup_push_envoy(){
    
       $saved_opts = get_option("push_en_settings");
        //// Get app ID ////
        $appid=$saved_opts['app_id'];
        
        if(@$_GET['page'] !='push_en_io_settings'){
            
            //// Don't load this notice in admin setup page //
        
        if($appid ==''){
         echo  '<div class="notice notice-warning" style="padding:15px"><img src="'.Push_envoy_logo.'" style="height:40px; " /><br/><h4> Push envoy requires configuration to activate Notifications</h4>'
                 . '<a class="button-primary" href="'.admin_url('admin.php?page=push_en_io_settings').'" title="">Configure</a>'
                 . '</div>';
        }
        
        }
}