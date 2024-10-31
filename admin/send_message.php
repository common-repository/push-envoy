<?php

function push_envoy_io_send_msg() {

    if (!current_user_can('edit_theme_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }



    //// Check if token exist // ///
    $value = get_option("push_en_settings");
    ?>

    <div class="wrap">
        <div class="container">

            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-12">

                        <h3> Send an instant push message to your subscribers</h3><hr/>

                        <div id='resp' class='text-center'></div>

                        <?php if (!$value['app_token']) { ?>

                            <div class='alert alert-danger'> You have not setup application token.Kindly setup here <a class='btn btn-info' href='<?php echo admin_url('admin.php?page=push_en_io_settings') ?>'> Setup token</a></div>
                        <?php } else { ?>
                            <form  method="post" action="admin-post.php" id="sendmsgform" >
                                <input type="hidden" name="action" value="push_en_io_msg_submit"/>
                                <!--- for form protection -->
                                <?php wp_nonce_field("push_io_msg_verify") ?>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Message Title <span class='smallired'>(required)</span></label>
                                            <input  onkeyup="tranfer_title(this.value)" required type="text" class="form-control" name="push_title"  value=""/>
                                        </div>
                                    </div>



                                </div>

                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Message<span class='smalli'> For advanced messaging, login to your pushenvoy.com account</span> <span class='smallired'>(required)</span></label>
                                            <textarea onkeyup="tranfer_text(this.value)" name="push_msg" id='push_msg' maxlength="200" onkeyup="javascript:content_count_keyup(this.value.length);" class="form-control"></textarea>
                                        </div>  
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Web Link to open on click <span class='smallired'>(required)</span></label>

        <!---<span class="input-group-addon" id="basic-addon3">http(s)://</span>-->
                                            <input required type="url" class="form-control" id="basic-url" aria-describedby="basic-addon3" name="push_open_url" placeholder="http://example.com"  value='<?php echo get_site_url() ?>'>



                                        </div>  
                                    </div> 

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Icon link for push notification </label>
                                            <input type="text"  onkeyup="checkicon(this.value)" class="form-control" name="push_icon"  id='push_icon' value="<?php echo $value['app_push_icon'] ?>"/>


                                        </div>  
                                    </div> 
                                </div>


                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group" style="background-color:#eee; padding:5px">
                                            <label class="control-label">Select Platform <span class='smallired'>(required. select at least one)</span></label> <br/>

                                            <input type="checkbox" name="push_platform[]" value="firefox" checked="checked">  Firefox <br/>
                                            <input type="checkbox" name="push_platform[]" value="chrome" checked="checked"> Chrome<br/>
                                            <input type="checkbox" name="push_platform[]" value="android">  Android* <br/>
                                            <input type="checkbox" name="push_platform[]" value="ios"> IOS* <br/>
                                            <input  type="checkbox" name="push_platform[]" value="safari"> Safari<br/>


                                        </div>
                                    </div>
                                    <div class="col-md-12 alert alert-info"> Only users who subscribed  with the selected platform will get the notification</div>

                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <button class="btn  btn-info " id='sendbtn' type="button">Send Now</button>
                                    </div>
                                </div>

                                <input type="hidden" value="<?php echo plugins_url("passets/img/loading.gif", PUSH_EN_IO_URL) ?>" id="wp_path"/>
                                <!-- .inside -->
                            </form>

                        <?php } ?>
                    </div> </div>

            </div>


            <div class="col-md-4" >
                <div class="row" style="margin-left:10px; border: #4e5b6c solid thin ">
                    <h4 class='text-center'><strong>Preview your message <span class='smalli'>what your users will see</span></strong></h4><hr/>
                    <div class='col-md-4' style="margin-right:0px;padding-right:0px">
                        <img id='pre_icon' src="<?php echo $value['app_push_icon'] ?>" class='img-responsive'/>  
                    </div>
                    <div class="col-md-8">
                        <p id='showTitle' style="font-weight: bold"></p>
                        <p id="showText" >  <em class="smalli">Preview message will apear here </em></p>
                    </div>
                </div>

                <div class="row" style="margin-left:10px; border: #4e5b6c solid thin ">
                    <h4 class='text-center'>Instructions</h4><hr/>
                    <div class="well">
                        1. You must be registered on <a href="https://pushenvoy.com" target='_blank'>pushenvoy.com</a><hr/>
                        2. Create an application on pushenvoy.com and save the application ID<hr/>
                        3.Ensure you have saved your token in the settings area of this plugin <a href="<?php echo admin_url("admin.php?page=push_en_io_settings") ?>">Here</a><hr/>
                        4. Only select android, IOS or Windows if you have a mobile app on any of these platforms. Else, select only web broswers e.g chrome, firefox e.t.c

                    </div>

                </div>

            </div>
        </div>


    </div> <!-- .wrap -->



<?php } ?>