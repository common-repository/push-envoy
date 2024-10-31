<?php

function push_envoy_dash_page() {
    $adminpage = $_GET['page'];
    $saved_opts = get_option("push_en_settings");
    $admin = new DASH;
    if (!current_user_can('edit_theme_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    ///// fetach successful notification ///
    $get_success_count = $admin->get_successful_notificaton();
    $get_failed = $admin->get_failed_notificaton();

    //// API CALL, fetch account parameters //
    $api = new PUSH_ENVOY_ADMIN;


    $get_account_info = $api->get_account_info($saved_opts['app_token']);
    $account_message = $api->get_account_message($saved_opts['app_token']);
    $app_subscribers = $api->get_app_subscribers($saved_opts['app_token']);

    if (isset($_GET['delete']) && $_GET['log'] == 'instant') {
        $admin->delete_msg($_GET['delete']);
    }

    if (isset($_GET['delete']) && $_GET['log'] != 'instant') {
        $admin->delete_post($_GET['delete']);
    }


    if ($_GET['log'] == 'instant') {
        $log = $_GET['log'];
        $num = $admin->msg_push_Count();
    } else {
        $num = $admin->post_push_Count();
    }




    $numrows = $num;

//$numrows=500;
// number of rows to show per page
    $rowsperpage = 20;
// find out total pages
    $totalpages = ceil($numrows / $rowsperpage);

// get the current page or set a default
    if (isset($_GET['currentpage']) && is_numeric($_GET['currentpage'])) {
        // cast var as int
        $currentpage = (int) $_GET['currentpage'];
    } else {
        // default page num
        $currentpage = 1;
    } // end if
// if current page is greater than total pages...
    if ($currentpage > $totalpages) {
        // set current page to last page
        $currentpage = $totalpages;
    } // end if
// if current page is less than first page...
    if ($currentpage < 1) {
        // set current page to first page
        $currentpage = 1;
    } // end if
// the offset of the list, based on current page 
    $offset = ($currentpage - 1) * $rowsperpage;

    if ($_GET['log'] == 'instant') {
        $get_msg_push = $admin->msg_push($offset, $rowsperpage);
    } else {
        $get_post_push = $admin->post_push($offset, $rowsperpage);
    }
    ?>

    <div class="wrap">
        <div class="container">
                <?php
                $saved_opts = get_option("push_en_settings");
                if ($saved_opts['app_token'] == '') {
                    echo '<div style="background-color:#C7611E; padding:5px; opacity: 0.6; color:#FFF ">Application configuration is missing :PushEnvoy.com configuration is required<a style="color: #e4ce11" href="' . admin_url('admin.php?page=push_en_io_settings') . '">  [Configure Settins]</a></div>';
                } else {
                    ?>

                <div class="row text-center" >
                   
                    <div class="col-md-3">
                        <img   id='pre_icon' src="<?php echo Push_envoy_logo ?>" class='img-responsive'/>
                    </div>
                    <div class="col-md-4">
                        <h4> Account Type</h4><hr/>
                        <h3> <label class="label label-info"><?php echo $get_account_info['data']->title ?></label>
                            <?php if ($get_account_info['data']->title != "Enterprise") { ?>
                                <a style="font-size:14px" href="https://pushenvoy.com/app/" target="_blank"> <label class="label label-danger" > Upgrade</label>  </a>
                            <?php } ?>
                        </h3>

                    </div>
                    <div class="col-md-5">
                        <h4>  Message Usage</h4><hr/>
                        <h3><?php echo number_format($account_message['data']->totalmessage) ?> /<?php echo number_format($get_account_info['data']->messages_quota) ?></h3>
                    </div>


                </div>

                <div class="row text-center" style="background-color:#eee;">
                    <div class="col-md-3 text-center dashbox"><h4>Subscribers</h4><hr/><span class="dashtxt"><label class="label label-primary "> <?php echo $app_subscribers['data']->AppSubscribers ?></label></span></div>
                    <div class="col-md-4   text-center  dashbox"><h4>Successful Notifications</h4><hr/> <span class="dashtxt"><label class="label label-success"><?php echo number_format($get_success_count) ?></label></span></div>
                    <div class="col-md-4   text-center  dashbox"><h4>Failed Notifications</h4><hr/><span class="dashtxt"><label class="label label-danger"><?php echo number_format($get_failed) ?></label></span></div>
                </div>
                <hr/>




                <?php if ($_GET['log'] != 'instant') { ?>
                    <div class="wall"><p class="text-left"><a href="<?php echo admin_url("admin.php?page=push_en_io_dash&log=instant") ?>" class="btn btn-danger">Instant Message Log</a></p>
                        <h3> Post Message log</h3>

                        <div class="row"  style="margin-right: 10px">
                            <div class="col-md-12">

                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Post Type</th>
                                            <th>Push Date</th>
                                            <th>Status</th>
                                            <th>View</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($get_post_push as $info) { ?>
                                            <tr>
                                                <td ><?php echo get_the_title($info->ID) ?></td>
                                                <td><label class="label label-info"><?php echo $info->post_type ?></label></td>
                                                <td><?php echo $admin->timepro($info->post_time) ?></td>
                                                <td><?php if ($info->sendstatus == 1) { ?><label class="label label-success">Sent</label><?php } else { ?><label class="label label-success">Danger</label> <?php } ?></td>
                                                <td><a target="_blank" href="<?php
                                                    $url_link = get_the_permalink($info->ID);
                                                    echo $url_link
                                                    ?>" class="btn btn-info btn-sm">View</a> &nbsp;&nbsp; <a href='<?php echo admin_url("admin.php?page=push_en_io_dash&delete=" . $info->eventid) ?>'><img style='width:25px; height:25px'  src="<?php echo plugins_url("passets/img/delete.png", PUSH_EN_IO_URL) ?>" /></a></td>

                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>

                    <?php } else { ?>
                        <div class="wall"><p class="text-left"><a href="<?php echo admin_url("admin.php?page=push_en_io_dash") ?>" class="btn btn-danger"><< Back to Post messages</a></p> 
                            <div class="row"  style="margin-right: 10px">
                                <div class="col-md-12">

                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Message</th>
                                                <th>Platform</th>
                                                <th>Status</th>
                                                <th>Date</th>


                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($get_msg_push as $info) { ?>
                                                <tr>

                                                    <td><label class="label label-info"><?php echo $info->msg_title ?></label></td>
                                                    <td><?php echo $info->msg_body ?></td>
                                                    <td><?php echo $info->platform ?></td>
                                                    <td><?php if ($info->sendstatus == 1) { ?><label class="label label-success">Sent</label><?php } else { ?><label class="label label-success">Danger</label> <?php } ?></td>

                                                    <td><?php echo $admin->timepro($info->rdate) ?> <a href='<?php echo admin_url("admin.php?page=push_en_io_dash&log=instant&delete=" . $info->msgid) ?>'><img style='width:25px; height:25px'  src="<?php echo plugins_url("passets/img/delete.png", PUSH_EN_IO_URL) ?>" /></a></td>



                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        <?php } ?>

                        <div class="row" style="background-color:#eee;">
                            <ul class="pagination">

                                <?php
                                $range = 2;

// if not on page 1, don't show back links
                                if ($currentpage > 1) {
                                    // show << link to go back to page 1
                                    echo "<li><a href='{$_SERVER['PHP_SELF']}?currentpage=1&page=$adminpage&log=$log'  class='met_bgcolor_transition'>|<strong><<</strong></a></li> ";
                                    // get previous page num
                                    $prevpage = $currentpage - 1;
                                    // show < link to go back to 1 page
                                    echo "<li><a href='{$_SERVER['PHP_SELF']}?currentpage=$prevpage&page=$adminpage&log=$log'  class='active'><strong><<</strong></a></li>";
                                } // end if 
// loop to show links to range of pages around current page
                                for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++) {
                                    // if it's a valid page number...
                                    if (($x > 0) && ($x <= $totalpages)) {
                                        // if we're on current page...
                                        if ($x == $currentpage) {
                                            // 'highlight' it but don't make a link
                                            echo "<li> <a href='javascript:;' class='page-numbers' style='background:black; color:white' >$x</a></li>";
                                            // if not current page...
                                        } else {
                                            // make it a link
                                            echo "<li><a href='{$_SERVER['PHP_SELF']}?currentpage=$x&page=$adminpage&log=$log'  class='page-numbers'>$x</a></li> ";
                                        } // end else
                                    } // end if 
                                } // end for
// if not on last page, show forward and last page links	
                                if ($currentpage != $totalpages) {
                                    // get next page
                                    $nextpage = $currentpage + 1;
                                    // echo forward link for next page 
                                    echo "<li><a href='{$_SERVER['PHP_SELF']}?currentpage=$nextpage&page=$adminpage&log=$log' class='next page-numbers'><strong>>></strong></a></li> ";
                                    // echo forward link for lastpage
                                    echo "<li><a href='{$_SERVER['PHP_SELF']}?currentpage=$totalpages&page=$adminpage&log=$log'  class='met_bgcolor_transition'><strong>>></strong>|</a></li> ";
                                } // end if
                                /*                                 * **** end build pagination links ***** */
                                ?>
                            </ul>
                        </div>

                    <?php } ?>
                </div>
            </div>
        <?php } ?>