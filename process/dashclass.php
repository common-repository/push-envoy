<?php

class DASH {

    public function timepro($timestamp) {
        $date = new DateTime();
        @$date->setTimestamp($timestamp);
        $newdate = $date->format('d, M Y  H:i:s');

        return $newdate;
    }

    public function get_successful_notificaton() {

        global $wpdb;
        /// fetch from blog table ///

        $t1 = $wpdb->get_var($wpdb->prepare("select count(*) from " . $wpdb->prefix . "push_auto_post where sendstatus=%d",1));

        //// fetch instant message ///
        $t2 = $wpdb->get_var($wpdb->prepare("select count(*) from " . $wpdb->prefix . "push_en_user_msg where sendstatus=%d",1));

        $total_success = $t1 + $t2;
        return $total_success;
    }

    public function get_failed_notificaton() {

        global $wpdb;
        /// fetch from blog table ///

        $t1 = $wpdb->get_var($wpdb->prepare("select count(*) from " . $wpdb->prefix . "push_auto_post where sendstatus !=%d",1));

        //// fetch instant message ///
        $t2 = $wpdb->get_var($wpdb->prepare("select count(*) from " . $wpdb->prefix . "push_en_user_msg where sendstatus !=%d",1));

        $total_success = $t1 + $t2;
        return $total_success;
    }

    public function post_push_Count() {
        global $wpdb;
        $t1 = $wpdb->get_var("select count(*) from " . $wpdb->prefix . "push_auto_post");
        return $t1;
    }

    public function post_push($offset, $rowsperpage) {
        global $wpdb;
        $offset = sanitize_text_field($offset);
        $rowsperpage = sanitize_text_field($rowsperpage);
        $t1 = $wpdb->get_results($wpdb->prepare("select * from " . $wpdb->prefix . "push_auto_post," . $wpdb->prefix . "posts  where postid=ID order by eventid desc limit %d,%d", $offset,$rowsperpage ));
        return $t1;
    }
    
    public function msg_push_Count() {
        global $wpdb;
        $t1 = $wpdb->get_var("select count(*) from " . $wpdb->prefix . "push_en_user_msg");
        return $t1;
    }

    public function msg_push($offset, $rowsperpage) {
        global $wpdb;
        $offset = sanitize_text_field($offset);
        $rowsperpage = sanitize_text_field($rowsperpage);
        $t1 = $wpdb->get_results($wpdb->prepare("select * from " . $wpdb->prefix . "push_en_user_msg order by msgid desc limit %d,%d", $offset,$rowsperpage ));
        return $t1;
    }
    
    public function delete_msg($id){
    global $wpdb;
    $wpdb->delete( $wpdb->prefix.'push_en_user_msg', array('msgid'=>$id), array('%d'), $where_format = null );
    return;
    }
    
     public function delete_post($id){
    global $wpdb;
    $wpdb->delete( $wpdb->prefix.'push_auto_post', array('eventid'=>$id), array('%d'),  $where_format = null );
    return;
    }

}
