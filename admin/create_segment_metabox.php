<?php

function push_envoy_create_segment_meta_box() {
    $saved_opts = get_option("push_en_settings");


    add_meta_box('post_segment_id', 'Push Segments (PushEnvoy.com)', 'push_envoy_load_segment', $saved_opts['app_push_post_type'], 'normal', 'high');
}

function push_envoy_load_segment($post) {
    $saved_opts = get_option("push_en_settings");
    
    if ($saved_opts['app_token'] == '') {
        echo '<div style="background-color:#C7611E; padding:5px; opacity: 0.6; color:#FFF ">Application configuration is missing :PushEnvoy.com configuration is required<a style="color: #e4ce11" href="' . admin_url('admin.php?page=push_en_io_settings') . '">  [Configure Settins]</a></div>';
    } else {
        $admin = new PUSH_ENVOY_ADMIN;
        
        $push_segment = get_post_meta($post->ID, 'push_post_segment', true);
        
        if(empty($push_segment)){
            $push_segment=array();
        }
        
        $segments = $admin->get_segments($saved_opts['app_token']);
        $total_segments = count($segments['data']);
        ?>

<div style="background-color: #eee; color:#000; padding:8px; padding-bottom: 10px"> Subscribers in this category will get message</div>
<input <?php if(in_array('all',$push_segment)){ echo "checked=checked"; } ?>  type="checkbox" name="push_send_segments[]" value="all" checked="checked">All Subscribers<br/>

        <?php for ($x = 0; $x < $total_segments; $x++) { ?>

            <input <?php if(in_array($segments['data'][$x]->id,$push_segment)){ echo "checked=checked"; } ?> type="checkbox" name="push_send_segments[]" value="<?php echo $segments['data'][$x]->id ?>"> <?php echo $segments['data'][$x]->title ?><br/>

        <?php } ?>


    <?php }
}
?>