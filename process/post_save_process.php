<?php

function post_envoy_save_process($post_id,$post,$update){
    
    if(!$update){ 
      /// New post 
        
        return;
    }
    $admin=new PUSH_ENVOY_ADMIN;
    $admin ->save_push_envoy_custom_meta($_POST,$post_id);

}