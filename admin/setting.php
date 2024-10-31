<?php

function push_envoy_io_settings() {
    if (!current_user_can('edit_theme_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    ?>

    <div class="wrap">
        <div class="container">

            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-12">
                        <?php $value = get_option("push_en_settings"); ?>
                        <?php if (@$_GET['status'] == 1) { ?>
                            <div class="alert alert-success"><?php _e("Your Settings has been saved successfully") ?> </div>

                        <?php }
                        if (@$_GET['status'] == 2) {
                            ?>
                            <div class="alert alert-danger"><?php _e($_GET['msg']) ?> </div>
    <?php } ?>
                        <form  method="post" action="admin-post.php">
                            <input type="hidden" name="action" value="push_en_io_set_submit"/>
                            <!--- for form protection -->
    <?php wp_nonce_field("push_io_options_verify") ?>

                            <div class="row">
                                <!--<div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label"> APP ID</label>
                                        <input  required type="text" class="form-control" name="push_appid"  value="<?php echo stripslashes($value['appid']) ?>"/>
                                    </div>
                                </div>-->

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Application Token</label>
                                        <input required type="text" class="form-control" name="push_token" value="<?php echo stripslashes($value['app_token']) ?>" />

                                    </div>  
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-6 ">
                                    <div class="form-group" style="background-color:#eee; padding:5px">
                                        <label class="control-label">Select Platform</label><br/>

    <?php $platforms = array();
    $platforms = json_decode($value['platforms']); ?>

                                        <input <?php if (@in_array("firefox", $platforms)) {
        echo "checked='checked'";
    } ?> type="checkbox" name="push_platform[]" value="firefox">  Firefox <br/>
                                        <input <?php if (@in_array("chrome", $platforms)) {
        echo "checked='checked'";
    } ?>  type="checkbox" name="push_platform[]" value="chrome"> Chrome<br/>
                                        <input <?php if (@in_array("android", $platforms)) {
        echo "checked='checked'";
    } ?> type="checkbox" name="push_platform[]" value="android">  Android<br/>
                                        <input <?php if (@in_array("ios", $platforms)) {
        echo "checked='checked'";
    } ?> type="checkbox" name="push_platform[]" value="ios"> Ios<br/>
                                        <input <?php if (@in_array("safari", $platforms)) {
        echo "checked='checked'";
    } ?> type="checkbox" name="push_platform[]" value="safari"> Safari<br/>

                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">How do you want to send new post?</label>
                                        <select class="form-control " id="sendopt" name="sendoption" onchange="sendtype(this.value)" >

    <?php if ($value['app_sendoption'] == 'auto') { ?>
                                                <option value="auto" selected='selected'>Automatically (On Post Publish) </option>  
                                                <option value="man"> I will send it myself </option>
                                            <?php } ?>

    <?php if ($value['app_sendoption'] == 'man') { ?>
        <!--- <script> $('#msg').show(); </script>--->
                                                <option value="man" selected='selected'> I will send it myself </option> 
                                                <option value="auto">Automatically (On Post Publish) </option>
                                            <?php } ?>

                                                <?php if ($value['app_sendoption'] == '') { ?>
                                                <option value="">Select</option>
                                                <option value="auto" selected="selected">Automatically (On Post Publish)  </option>
                                                <option value="man"> I will send it myself </option>
                                                <?php } ?>



                                        </select>
                                        <div  id='msg' style="background-color:#eee; padding:5px; margin-top:10">
                                                <?php if ($value['app_sendoption'] == 'auto') { ?>
                                           
                                   <div  style='margin-bottom:0px' class='alert alert-success'>Automatic sending Enabled. Your NEW post will be sent out once published</div>

    <?php } ?>
                                            
                                           <?php if ($value['app_sendoption'] == 'man') { ?>
                                                
                                                <div  style='margin-bottom:0px' class='alert alert-warning'>Automatic sending disabled.You will have to set push time on post setup</div>
                                           <?php } ?>
                                        
                                        </div>          

                                    </div>  
                                </div> 
                            </div>
                            
                             <div class="row">
                                <div class="col-md-6 ">
                                    <div class="form-group" style="background-color:#eee; padding:5px">
                                        <label class="control-label">Enable Push Notification for post type</label><br/>

  <?php 
  /// fetch  custom post  type
  $args = array(
   'public'   => true,
  //'_builtin' => false
);

$output = 'names'; // names or objects, note names is the default
$operator = 'and'; // 'and' or 'or'

  $post_types = get_post_types($args, $output, $operator); 
  foreach ($post_types as $posttype){
      if($posttype=='attachment'){
          continue;
      }
  ?>

    <input <?php if(in_array($posttype, $value['app_push_post_type'])){ echo "checked=checked"; } ?> type="checkbox" name="push_post_type[]" value="<?php echo $posttype ?>"> <?php echo $posttype ?><br/>
       
  <?php } ?>
     
        

                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                   
                                        
                                    </div>  
                                </div> 
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <label for="image_url"class="control-label">Push Notification Icon <span class="smalli">Should be your website logo</span></label><br/>
                                   
                                    <input type="text" name="app_push_icon" id="image_url" class="all-options" value="<?php echo $value['app_push_icon'] ?>">
    <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload Icon">
    
                                </div>
                                
                                <div class="col-md-4 " >
                                    <?php if($value['app_push_icon'] ==''){?>
                                    <img style="height:80px" id='pre_icon' src="<?php echo plugins_url("passets/img/noicon.png",PUSH_EN_IO_URL) ?>" class='img-responsive'/>   
                                    <?php }else{?>
                                    <img style="height:80px" id='pre_icon' src="<?php  echo $value['app_push_icon'] ?> " class='img-responsive'/>   
                                    <?php } ?>
                                    <strong>Push  Icon </strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn  btn-success" type="submit">Verify token and save</button>
                                </div>
                            </div>


                            <!-- .inside -->
                        </form>
                    </div> </div>

                    <hr/>
                     <div class="row">
                     <div class="col-md-12">
                      <h4>Convert your wordpress category into push notification segment</h4>
                    <p><em> By converting your wordpress categories into segments, your visitors can choose to subscribe to push notification for specific category </em>
                    <a target="_blank" href="https://pushenvoy.com/docs/our-wordpress-plugin/convert-wordpress-category-to-push-segments/">[see sample]</a>
                    </p>
                    </div> 
                         <?php if($value['app_id'] ==''){?>
                         <div class="col-md-12">
                             <div class="alert alert-warning">To sync your category with your push notification segments, you must first activate this plugin by providing an application token above</div> </div>
<?php } else{?>
                         <form  method="post" action="admin-post.php"  id="syncform"  >
                        <input type="hidden" name="action" value="push_en_io_sync_cat"/>
                        <!--- for form protection -->
                        <?php wp_nonce_field("push_io_sync_verify") ?>

                                <div class="col-md-12">
                                
                                <div id="sync_resp"></div>
                                
                                <input type="hidden" value="<?php echo plugins_url("passets/img/loading.gif", PUSH_EN_IO_URL) ?>" id="wp_path"/>
                                    <button class="btn  btn-success" type="button" id="sync_cat">Convert Now</button>
                                </div>

                                </form>
                         <?php } ?>
                         <div class="col-md-12" style="margin-top: 10px">
                          <p><em> Note: This feature works for top level category only and not sub-category of a parent</em> </p>
                            </div>
                            </div>

            </div>

            <div class="col-md-4"> 
                <div class="row" style="margin-left:10px; border: #4e5b6c solid thin ">
                    
                    <img   id='pre_icon' src="<?php echo Push_envoy_logo ?>" class='img-responsive'/>
                </div>
                <div class="row" style="margin-left:10px; border: #4e5b6c solid thin ">
                    <h4 class='text-center'>Instructions</h4><hr/>
                    <div class="well">
                        1. You must be registered on <a href="https://pushenvoy.com" target='_blank'>pushenvoy.com</a><hr/>
                        2. Create an application on pushenvoy.com and save the application Token <a target="_blank" href="https://pushenvoy.com/docs/user-guide/creating-your-first-application/">[How to generate a token]</a><hr/>
                        3.Ensure you have saved your token in the settings area of this plugin <a href="<?php echo admin_url("admin.php?page=push_en_io_settings") ?>">Here</a><hr/>
                        4. Only select android, IOS or Windows if you have a mobile app on any of these platforms. Else, select only web browsers e.g chrome, firefoxe.t.c

                    </div>

                </div>

                <div class="row" style="margin-left:10px">
                   
                </div>
            </div>
        </div>


    </div> <!-- .wrap -->

<?php } ?>