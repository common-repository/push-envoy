<?php

function push_envoy_io_segment() {
    if (!current_user_can('edit_theme_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    
    if(!isset($_GET['status'])){
        $_GET['status']='';
    }
    if(!isset($_GET['msg'])){
        $_GET['msg']='';
    }
    $admin = new PUSH_ENVOY_ADMIN;
    $segments=array(
        "data"=>'',
        "message"=>''
    );
    //// Check configuration ///
    $saved_opts = get_option("push_en_settings");
    $segments = $admin->get_segments($saved_opts['app_token']);
    $total_segments = @count($segments['data']);
    ?>


    <div class="wrap">
        <?php if($_GET['status']==1){?>
            <div class="alert alert-sucess"> <?php echo $_GET['msg'] ?> </div>    
        <?php } ?>
            <?php if($_GET['status']==2){?>
            <div class="alert alert-danger"> <?php echo $_GET['msg'] ?> </div>    
        <?php } ?>
        <?php if($segments['message'] !=''){?>
             <div class="alert alert-info"> <?php echo $segments['message'] ?> </div>  
            
        <?php } ?>
            
        <div class="container">

            <div class="col-md-8">
                <div class="row">
                    <?php
                    if ($saved_opts['app_token'] == '') {
                        echo '<div style="background-color:#C7611E; padding:5px; opacity: 0.6; color:#FFF ">Application configuration is missing :PushEnvoy.com configuration is required<a style="color: #e4ce11" href="' . admin_url('admin.php?page=push_en_io_settings') . '">  [Configure Settins]</a></div>';
                    } else {
                        ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Segment Name</th>
                                    <th>Subscribers</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($x = 0; $x < $total_segments; $x++) { ?>
                                    <tr>
                                        <td><?php echo $x ?></td>
                                        <td><?php echo $segments['data'][$x]->title ?></td>
                                        <td><?php echo $segments['data'][$x]->count ?></td>

                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                    <?php } ?>
                </div>
                <div class="row text-center">
                    <a class="btn btn-info" href="<?php echo admin_url('admin.php?page=push_en_io_settings#syncform') ?>"> Convert your wordpress category to push notification segments</a>
                    
                </div>
            </div>

            <div class="col-md-4">
                <div class="row" style="margin-left:10px;">
                    <h4 class='text-center'>Create Segment</h4><hr/>
                    <form  method="post" action="admin-post.php"  >
                        <input type="hidden" name="action" value="push_en_io_segment_submit"/>
                        <!--- for form protection -->
                        <?php wp_nonce_field("push_io_segment_verify") ?>

                        
                                <div class="form-group">
                                    <label class="control-label">Segment Name</label>
                                    <input  required type="text" maxlength="20" class="form-control" name="push_segment"  value=""/>
                                </div>
                           
                      
                           
                        <button class="btn  btn-info " type="submit">Add New Segment</button>
                            <a class="btn btn-success" href="<?php echo admin_url('admin.php?page=push_en_io_settings#syncform') ?>">Convert to Segment</a>
                    
                        
                    </form>
                </div>

                <div class="row" style="margin-left:10px;">
                    <h4 class='text-center'>What are Segments?</h4><hr/>
                    <div class="well">
                        A segment is a category your visitors can subscribe to receive push notifications. I.e you might have visitors who are interested in
                         certain topics only. It will be a good idea to send those specific topic to them when you post content related to that topic.
                        This will reduce the frequency of subscribers unsubscribing from your notifications.
                        <hr/> <h4 class='text-center'>Instructions</h4>
                        1. You must be registered on <a href="https://pushenvoy.com" target='_blank'>pushenvoy.com</a><hr/>
                        2. Create an application on pushenvoy.com and save the application Token<hr/>
                        3.Ensure you have saved your token in the settings area of this plugin <a href="<?php echo admin_url("admin.php?page=push_en_io_settings") ?>">Here</a><hr/>
                        4. Only select android, IOS or Windows if you have a mobile app on any of these platforms. Else, select only web broswers e.g chrome, firefox e.t.c

                    </div>

                </div>
            </div>
        </div>


    </div> <!-- .wrap -->

<?php } ?>