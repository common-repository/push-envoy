<?php

function push_en_auto_send_post($new_status, $old_status, $post) {
    
ob_start();
    if ($_POST) {

        /// Once a post is edited or update, this function runs //
        $admin = new PUSH_ENVOY_ADMIN;
        $admin->save_push_envoy_custom_meta($_POST, $post->ID);

        $saved_opts = get_option("push_en_settings");
        if ($saved_opts['app_token'] != '') { //// check if if application has been configured//
            $send_pattern = get_post_meta($post->ID, 'push_send_pattern', true);
            
            

            if (empty($send_pattern)) {
                // this will trigger if auto post is set to true in settings
                $send_pattern = array(
                    "pattern" => '',
                    "push_send_time" => '',
                    "resend_flag" => '',
                );
            }

            if ($saved_opts['app_sendoption'] == "auto") {
                $time_to_send_push = 'now';
            } else {
                $time_to_send_push = $send_pattern['push_send_time']; /// Pick send time from user selection on post creation  
            }
            
            $configured_post_type = $saved_opts['app_push_post_type'];  ///  REPLACE WITH CONFIGURE OPTIONS
           
            if ($time_to_send_push != "nosend" && 'publish' === $new_status && 'publish' !== $old_status && in_array($post->post_type, $configured_post_type)) {
                push_en_fire_push($post, $time_to_send_push);
            }
            elseif ($send_pattern['resend_flag']==1 && 'publish' === $new_status  && in_array($post->post_type, $configured_post_type)) {
                push_en_fire_push($post, $time_to_send_push);
            }
        }
    }
}

function push_en_fire_push($post, $time_to_send_push) {
    $admin = new PUSH_ENVOY_ADMIN;
    $saved_opts = get_option("push_en_settings");
    $push_segments = get_post_meta($post->ID, 'push_post_segment', true);
    
     global $wpdb;
     $auto_post_table = $wpdb->prefix . 'push_auto_post';
    /// Log in message table ///
    $wpdb->insert($auto_post_table, array(
        "postid" => $post->ID,
        "post_time" => time(),
        "send_type" => $time_to_send_push,
    ), array('%d','%s','%s','%s'));

    if ($wpdb->insert_id > 0) {

        if ($post->post_excerpt != '') {
            $post_body = wp_trim_words($post->post_excerpt, 30, '[Read More]');
        } else {
            $post_body = wp_trim_words($post->post_content, 30, '[Read More]');
        }
        $post_link = get_the_permalink($post->ID);
        $post_title = $post->post_title;
        
        if($time_to_send_push =='now'){ // hack it for now
            $send_type='now';
        }
        else{
             $send_type='time';
             $send_time = $time_to_send_push;
        }

        $icon_link = $saved_opts['app_push_icon'];
        $token = $saved_opts['app_token'];
        $platforms = $saved_opts['platforms'];

        $send_msg = $admin->auto_send_push_message($post_title, $post_body, $post_link, $icon_link, $platforms, $token, $send_time,$send_type,$push_segments);
        $status = $send_msg['status'];
        $res_msg = $send_msg['message'];

        /// update status to db //
        $wpdb->update($auto_post_table, array('sendstatus' => $status, 'resonse_msg' => $res_msg), array('eventid' => $wpdb->insert_id),array('%s','%s'), array('%d'));
        // return;
    } else {
        //die("error in insert");
    }
}
