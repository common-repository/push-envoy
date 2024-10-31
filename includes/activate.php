<?php

function on_push_envoy_activation() {
    //// Create the databases ////
    global $wpdb;

    $table_name = $wpdb->prefix . 'push_en_user_msg'; /// User manuel sending table //
    $auto_post_table=$wpdb->prefix.'push_auto_post';
    
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $CreatTable = "CREATE TABLE `" . $wpdb->prefix . "push_en_user_msg` (
  `msgid` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `msg_title` VARCHAR(255) NOT NULL DEFAULT '',
  `msg_body` TEXT NOT NULL DEFAULT '',
  `platform` VARCHAR(255) NOT NULL DEFAULT '',
  `open_url` VARCHAR(255) NOT NULL DEFAULT '',
  `icon_url` VARCHAR(255) NOT NULL DEFAULT '',
  `sendstatus` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `rdate` VARCHAR(45) NOT NULL DEFAULT '',
  `userid` VARCHAR(45) NOT NULL DEFAULT '',
  PRIMARY KEY(`msgid`)
)
ENGINE = InnoDB " . $wpdb->get_charset_collate() . " AUTO_INCREMENT=1;";
        require_once(ABSPATH . "/wp-admin/includes/upgrade.php"); /// Fro database table creation and manipulation of structure
        dbDelta($CreatTable);
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$auto_post_table'") != $auto_post_table) {

        $Creat_auto_Table = "CREATE TABLE `".$auto_post_table."`(
        `eventid` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `postid` BIGINT UNSIGNED NOT NULL DEFAULT 0,
        `post_time` VARCHAR(45) NOT NULL DEFAULT '',
         `send_type` VARCHAR(45) NOT NULL DEFAULT '',
        `sendstatus` TINYINT UNSIGNED NOT NULL DEFAULT 0,
        `resonse_msg` TEXT NOT NULL DEFAULT '',
        PRIMARY KEY(`eventid`)
        )
        ENGINE = InnoDB ". $wpdb->get_charset_collate() . " AUTO_INCREMENT=1;";
        require_once(ABSPATH . "/wp-admin/includes/upgrade.php"); /// Fro database table creation and manipulation of structure
        dbDelta($Creat_auto_Table);
    }


    /// Create the options rows to handle settings//

    $push_opts = get_option("push_en_settings", false);
    if (!$push_opts) {
        /// install the options //
         
         $push_opts=["app_push_post_type"=>array('post'),"app_token"=>'',"app_id"=>'',"app_sendoption"=>'man', "app_push_icon"=>'','platforms'=>'["firefox","chrome","ios"]' ]; /// set post as the default content type to get notifications 
         
         
       // add_option("push_en_settings");
         update_option("push_en_settings", $push_opts);
    }
}

?>