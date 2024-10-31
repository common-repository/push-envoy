<?php

function push_en_io_sync_cat() {
    $admin = new PUSH_ENVOY_ADMIN;

    check_admin_referer("push_io_sync_verify");

    /// Check if submitted by a trusted user, the  ///
    if (!current_user_can("edit_theme_options")) {
        wp_die(__("You are not allowed to access this page"));
    }


    $saved_opts = get_option("push_en_settings");
    $error_flag = 0;

    $segments = $admin->get_segments($saved_opts['app_token']);

    $total_segments = @count($segments['data']);
    $segment_array = array();
    $to_convert = array();
    for ($x = 0; $x < $total_segments; $x++) {

        $segment_array[] = strtolower(trim($segments['data'][$x]->title));
    }


    $categories = get_categories(array(
        'orderby' => 'name',
        'parent' => 0,
        'hide_empty' => false,
            ));


    foreach ($categories as $category) {

        if (!in_array(strtolower(trim($category->name)), $segment_array)) {
            if ($category->name != "Uncategorized") {
                $to_convert[] = ucwords($category->name);
            }
        }
    }

///// Create sync segements ////
    foreach ($to_convert as $sync_seg) {
        
        $api_result = $admin->create_segment($saved_opts['app_token'], sanitize_text_field($sync_seg));

        if ($api_result['status'] == '2') {
            echo "<div class='alert alert-danger'>" . $api_result['message'] . "</div>";
            $error_flag = 1;
            break;
        }
    }

    
    if ($error_flag == 0) {
        $total_synced = count($to_convert);
        echo "<div class='alert alert-success'>You have successfully converted <strong>" . $total_synced . "</strong> categories to push notification segment</div>";
    }
}

?>