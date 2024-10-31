<?php

function save_push_envoy_settings() {
ob_start();
$admin = new PUSH_ENVOY_ADMIN;

/// Check if submitted by a trusted user, the  ///
if (!current_user_can("edit_theme_options")) {
wp_die(__("You are not allowed to access this page"));
}

$form_error = array();

if($_POST['push_token'] == ''){
$form_error[] = "App Token is required";
}

if($_POST['sendoption'] == ''){
$form_error[] = "Mode of sending is required";
}



if(count($form_error) > 0){
    $error_string= implode("<br/>", $form_error);
  wp_redirect(admin_url('admin.php?page=push_en_io_settings&status=2&msg=' . urlencode($error_string)));  
  die();  
}
else {


if (count($_POST['push_platform']) == 0) {
$error_string = urlencode('You have to select at least one platform');
wp_redirect(admin_url('admin.php?page=push_en_io_settings&status=2&msg=' . $error_string));

die();
}

if (count($_POST['push_post_type']) == 0) {
$error_string = urlencode('You have to select at least one post  type');
wp_redirect(admin_url('admin.php?page=push_en_io_settings&status=2&msg=' . $error_string));

die();
}
$check_token = array();
$check_token = $admin->check_token($_POST['push_token']);
if ($check_token['status'] == 2) {
//// Kill //
$error_string = urlencode($check_token['message']);


wp_redirect(admin_url('admin.php?page=push_en_io_settings&status=2&msg=' . $error_string));

die();
}

/// check the wp nouce //
check_admin_referer("push_io_options_verify");

///// API CHECK ///
///  get  the options ///
$saved_opts = get_option("push_en_settings");


$saved_opts['app_id'] = sanitize_text_field($check_token['appid']);
$saved_opts['app_token'] = sanitize_text_field($_POST['push_token']);
$saved_opts['app_sendoption'] = sanitize_text_field($_POST['sendoption']);
$saved_opts['app_push_post_type'] = array(); /// reset//
$cleaned_post_types = array(); /// reset//

foreach ($_POST['push_post_type'] as $post_type){
$cleaned_post_types[] = sanitize_text_field($post_type);
}
$saved_opts['app_push_post_type'] = $cleaned_post_types;


if ($_POST['app_push_icon'] == '') {
/// Set default Icon to push envoy Icon ///
$saved_opts['app_push_icon'] = plugins_url("passets/img/pushicon.png", PUSH_EN_IO_URL);
} else {
$saved_opts['app_push_icon'] = sanitize_text_field($_POST['app_push_icon']);
}
$saved_opts['platforms'] = '';

$clean_platform = $admin->prepare_platforms($_POST['push_platform']);


$saved_opts['platforms'] = $clean_platform;
update_option("push_en_settings", $saved_opts);
wp_redirect(admin_url('admin.php?page=push_en_io_settings&status=1'));
}
}
?>
