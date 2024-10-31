<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              pushenvoy.com
 * @since             1.0.0
 * @package           pushenvoy.com
 *
 * @wordpress-plugin
 * Plugin Name:       Push Envoy
 * Plugin URI:        pushenvoy.com
 * Description:       Get an opt-in rate of up to 25% easily with Push Envoy. Automatically send out push notification to your visitors on posting a content
 * . 
 * Version:           1.0.0
 * Author:            Push Envoy
 * Author URI:        
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pushenvoy.com
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
define("PUSH_EN_IO_URL", __FILE__);

define("Push_envoy_logo",plugins_url("passets/img/pushenvoy.png",PUSH_EN_IO_URL));

include("includes/activate.php");
include("includes/deactivate.php");
include("includes/script_styles.php");
include("process/save_push_settings.php");
include("process/send_push_msg.php");
include("process/adminclass.php");
include("process/dashclass.php");
include("process/post_save_process.php");
include("process/save_push_segment.php");
include("process/sync_cat.php");
include("process/plugin_check.php");
include("process/push_load_footer_inc.php");
include("admin/init.php");
include("admin/menus.php");
include("admin/dashboard.php");
include("admin/send_message.php");
include("admin/setting.php");
include("process/autopost.php");
include("admin/segmentation.php");
include("process/push_wp_dash.php"); /// Add a dashboard snippet to wordpress dashboard

$saved_opts = get_option('push_en_settings');

//// Register hook actions for configured post type ////

if (empty($saved_opts['app_push_post_type'])) {
    //// Default  as default for all post type
    $saved_opts['app_push_post_type'] = array();
}

register_activation_hook(__FILE__, 'on_push_envoy_activation');
register_deactivation_hook(__FILE__, 'on_push_envoy_deactivation');

add_action("admin_init", "push_envoy_initialize_admin");
add_action("admin_menu", "push_envoy_admin_menus");
add_action("admin_menu", "push_io_script_style");
add_action('transition_post_status', 'push_en_auto_send_post', 10, 3); /// On publish Post ///
add_action("wp_dashboard_setup", "push_envoy_wp_dash");
add_action( 'admin_notices', 'check_if_setup_push_envoy' );


if ($saved_opts['app_id'] !='') {
    ////////////////// Application has been configured //////
    add_action("wp_footer", "push_load_footer_inc");
    
    
}
foreach ($saved_opts['app_push_post_type'] as $set_push_post_type) {
    
    add_action("save_post_".$set_push_post_type, "post_envoy_save_process", 10, 3);
}

?>