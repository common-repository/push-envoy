<?php

function push_envoy_wp_dash() {

    wp_add_dashboard_widget("push_io_dash_widget", "Lastest Push Notifications (Push Envoy)", "load_dash_widget");
}

function load_dash_widget() {

    global $wpdb;
    $get_post_push = $wpdb->get_results("select * from " . $wpdb->prefix . "push_auto_post," . $wpdb->prefix . "posts  where postid=ID order by eventid desc limit 5 ");
     $saved_opts = get_option("push_en_settings");
       if ($saved_opts['app_token'] == '') {
                        echo '<div style="background-color:#C7611E; padding:5px; opacity: 0.6; color:#FFF ">Application configuration is missing :PushEnvoy.com configuration is required<a style="color: #e4ce11" href="' . admin_url('admin.php?page=push_en_io_settings') . '">  [Configure Settins]</a></div>';
                    } else {
    ?>
    <ul>

        <?php foreach ($get_post_push as $info) { ?>

            <li ><a target="_blank" href="<?php $url_link = get_the_permalink($info->ID);
        echo $url_link ?>" class="btn btn-info btn-sm"><?php echo wp_trim_words(get_the_title($info->ID),'10','...') ?></a>
            <?php if ($info->sendstatus == 1) { ?><span style="float: right;background-color: #29b636; border-radius: 100px; padding:3px; color:#fff">sent</span><?php } else { ?><span style="float: right;background-color: #CC0000; border-radius: 100px; padding:3px; color:#fff">sent</span> <?php } ?><hr/></li>

    <?php } ?>

    </ul>
<?php }} ?>
