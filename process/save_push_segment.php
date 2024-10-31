<?php


function send_push_envoy_segment() {
    ob_start();
    $admin = new PUSH_ENVOY_ADMIN;
check_admin_referer("push_io_segment_verify");
    /// Check if submitted by a trusted user, the  ///
    if (!current_user_can("edit_theme_options")) {
        wp_die(__("You are not allowed to access this page"));
    }

    $validator = new FormValidator();
    $validator->addValidation("push_segment", "req", "App ID is required");
 
    if (!$validator->ValidateForm()) {

        $error_string = "Correct the following errors";

        $error_hash = $validator->GetErrors();

        foreach ($error_hash as $inpname => $inp_err) {

            $error_string.='<br/><strong>Warning!</strong> <p>' . $inp_err . '</p> ';
        }
        wp_redirect(admin_url('admin.php?page=push_en_io_segment&status=2&msg=' . urlencode($error_string)));
        die();
    } else {
        $saved_opts = get_option("push_en_settings");
        
        $new_segment=$admin->create_segment($saved_opts['app_token'],sanitize_text_field($_POST['push_segment']));
        
        if($new_segment['status'] ==1){
         wp_redirect(admin_url('admin.php?page=push_en_io_segment&status=1&msg='.urlencode($new_segment['message'])));   
        }
        else{
            wp_redirect(admin_url('admin.php?page=push_en_io_segment&status=2&msg='.urlencode($new_segment['message'])));
        }

        
    }
}

?>
