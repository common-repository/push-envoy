<?php

function send_push_envoy_msg() {
  ob_start(); 
    $admin = new PUSH_ENVOY_ADMIN;
    check_admin_referer("push_io_msg_verify");
    /// Check if submitted by a trusted user, the  ///
    if (!current_user_can("edit_theme_options")) {
        wp_die(__("You are not allowed to access this page"));
    }

    
    $form_error = array();

if($_POST['push_title'] == ''){
$form_error[] = "Message title is required";
}

if($_POST['push_msg'] == ''){
$form_error[] = "Message body is required";
}

if($_POST['push_open_url'] == ''){
$form_error[] = "Provide a link";
}

if(count($form_error) > 0){
    $error_string= implode("<br/>", $form_error);
    echo $error_string.'#2';
}

 else {

        if (!filter_var($_POST['push_open_url'], FILTER_VALIDATE_URL)) {
            echo("Web link is not a is a valid URL#2");
            die();
        }
        
        if (!filter_var($_POST['push_icon'], FILTER_VALIDATE_URL)) {
            echo("Push icon must be a valid url#2");
            die();
        }
        
        if (count(@$_POST['push_platform']) == 0) {
            $error_string = 'You have to select at least one platform#2';
            echo("You have to select at least one platform#2");

            die();
        }
        $clean_platform = $admin->prepare_platforms($_POST['push_platform']);
        global $wpdb;

        $table_name = $wpdb->prefix . 'push_en_user_msg';
        $wpdb->insert($table_name, array(
            'msg_title' => sanitize_text_field($_POST['push_title']),
            'msg_body' => sanitize_text_field($_POST['push_msg']),
            'platform' => sanitize_text_field($clean_platform),
            'open_url' => sanitize_text_field($_POST['push_open_url']),
            'icon_url' => sanitize_text_field($_POST['push_icon']),
            'rdate' => time(),
            'userid' => get_current_user_id(),
        ));

        if ($wpdb->insert_id > 0) {
            /// Fetch the data and send to push server ///  
            $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $table_name . " WHERE msgid =%d",$wpdb->insert_id));

            ///// SEND TO PUSH SERVER ///
            /// fetch APP TOKEN //
            $saved_opts = get_option("push_en_settings");
            $send_msg = $admin->send_push_message($row, $saved_opts['app_token']);
            $status = $send_msg['status'];
            $res_msg = $send_msg['message'];

            /// update status to db //
            $wpdb->update($table_name, array('sendstatus' => $status), array('msgid' => $wpdb->insert_id),array('%s'), array('%d'));


            echo $res_msg."#" . $status;
            die();
        }


        //   wp_redirect(admin_url('admin.php?page=push_message.php&status='.$status.'&msg='.urlencode($res_msg)));
    }
}?>
