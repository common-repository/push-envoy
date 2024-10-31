<?php

function push_envoy_create_post_meta_box() {
    $saved_opts = get_option("push_en_settings");
    add_meta_box('post_push_id', 'Send Push Notification (PushEnvoy.com)', 'push_envoy_load_sendform', $saved_opts['app_push_post_type'], 'normal', 'high');
}

function push_envoy_load_sendform($post) {

    if(!isset($_GET['action'])){
        $_GET['action']='';
    }

    //// Check configuration ///
    $saved_opts = get_option("push_en_settings");

    if ($saved_opts['app_token'] == '') {
        echo '<div style="background-color:#C7611E; padding:5px; opacity: 0.6; color:#FFF ">Application configuration is missing :PushEnvoy.com configuration is required<a style="color: #e4ce11" href="' . admin_url('admin.php?page=push_en_io_settings') . '">  [Configure Settins]</a></div>';
    } else {
        
        $send_pattern = get_post_meta($post->ID, 'push_send_pattern', true);

        if (empty($send_pattern)) {
            $send_pattern = array(
                "pattern" => '',
                "push_send_time" => '',
            );
        }
        ?>
        <?php if ($saved_opts['app_sendoption'] != 'auto') { ?>

             <?php if($_GET['action'] === 'edit'){ ?>
<input  type="checkbox" name="push_resend" id="push_resend" value="resend" >Tick to send push notification on post update?<br/>
           
           <?php } else{?>
           
            <strong >When do you want to push this message?</strong><br/><br/>
            <select name="send_push_type" id="send_type" onchange="meta_send_type(this.value)" class="regular-text" >
                <?php if ($send_pattern['pattern'] == 'now') { ?>
                    <option value="now" selected="selected">Immediately I publish this</option>
                    <option value="later"> I want to schedule it</option>
                    <option value="nosend" >Don't send</option>
                <?php } elseif ($send_pattern['pattern'] == 'later') { ?>

                    <option value="later" selected="selected"> I want to schedule it</option>
                    <option value="now" >Immediately I publish this</option>
                    <option value="nosend" >Don't send push notification</option>

                <?php } elseif ($send_pattern['pattern'] == 'nosend') { ?>
                    <option value="nosend" selected="selected">Don't send push notification</option>
                    <option value="later" > I want to schedule it</option>
                    <option value="now" >Immediately I publish this</option>


                <?php } else { ?>
                    <option value="now" selected="selected">Immediately I publish this</option>
                    <option value="later"> I want to schedule it</option>
                    <option value="nosend" >Don't send push notification</option>
                <?php } ?>
            </select>
            <?php if ($send_pattern['pattern'] == 'later') { ?>
                <span id="show_push_time">
                    <div id="push_meta_msg" ><hr/> <strong>Select Time</strong></div>

                    <input type="text"    value="<?php echo $send_pattern['push_send_time'] ?>" id="push_sendtime" name='push_sendtime' class="regular-text" readonly="readonly" placeholder="click to select time"/>
                </span>
            <?php } else { ?>

                <span id="show_push_time" style="display: none">
                    <div id="push_meta_msg"  ><hr/> <strong>Select Time</strong></div>

                    <input type="text"  value="<?php echo date("Y-m-d") ?>" id="push_sendtime" name='push_sendtime' class="regular-text" readonly="readonly" placeholder="click to select time" />
                </span>
            <?php } ?>

           <?php }} else { ?>
             <?php if($_GET['action'] === 'edit'){ ?>
<input  type="checkbox" name="push_resend" id="push_resend" value="resend" >Send push notification on post update?<br/>
             <?php }else{?>
            <div style="background-color:#C7611E; padding:5px; opacity: 0.6; color:#FFF ">You configured <strong>Automatic sending</strong> of new post in your push settings page. pushenvoy.com will automatically push your post to subscribers once published <a style="color: #e4ce11" href="<?php echo admin_url('admin.php?page=push_en_io_settings') ?>">  [Change settings]</a></div>

           <?php }}
    }/// end of application configuration ?>

<?php } ?>