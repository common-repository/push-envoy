
function explode(delimiter, string, limit) {

    if (arguments.length < 2 || typeof delimiter === 'undefined' || typeof string === 'undefined')
        return null;
    if (delimiter === '' || delimiter === false || delimiter === null)
        return false;
    if (typeof delimiter === 'function' || typeof delimiter === 'object' || typeof string === 'function' || typeof string ===
            'object') {
        return {
            0: ''
        };
    }
    if (delimiter === true)
        delimiter = '1';

    // Here we go...
    delimiter += '';
    string += '';

    var s = string.split(delimiter);

    if (typeof limit === 'undefined')
        return s;

    // Support for limit
    if (limit === 0)
        limit = 1;

    // Positive limit
    if (limit > 0) {
        if (limit >= s.length)
            return s;
        return s.slice(0, limit - 1)
                .concat([s.slice(limit - 1)
                            .join(delimiter)
                ]);
    }

    // Negative limit
    if (-limit >= s.length)
        return [];

    s.splice(s.length + limit);
    return s;
}


function meta_send_type(va) {

    if (va == 'later') {
        jQuery("#show_push_time").show();
   

    } else if (va == 'now') {
        jQuery("#show_push_time").hide();
        
    }
    else if (va == 'nosend') {
        jQuery("#show_push_time").hide();
        
    }
}



function sendtype(va) {


    if (va == 'auto') {
        jQuery('#msg').html("<div  style='margin-bottom:0px' class='alert alert-success'>NEW post will be automatically sent on PUBLISH</div>");
    } else if (va == 'man') {
        jQuery('#msg').html("<div  style='margin-bottom:0px' class='alert alert-warning'>Automatic sending disabled.You will push out new post manually</div>");
    }
}
function tranfer_title(va) {

    jQuery('#showTitle').html(va);

}

function tranfer_text(va) {

    jQuery('#showText').html(va);

}

function checkicon(icon) {
    jQuery("#pre_icon").attr("src", icon);
}

jQuery(function () {
    
    

jQuery('#push_sendtime').daterangepicker({
    "timePicker": true,
        "timePickerIncrement": 1,
         "singleDatePicker": true,
         "showDropdowns": true,
          "autoApply": true,
         locale: {
            format: 'YYYY-MM-DD h:mm a'
        }
});


    jQuery("#push_icon").bind('paste', function (event) {
        var _this = this;
        // Short pause to wait for paste to complete
        setTimeout(function () {
            var newicon = jQuery(_this).val();

            jQuery("#pre_icon").attr("src", newicon);

        }, 100);
    });





    jQuery('#upload-btn').click(function (e) {
        e.preventDefault();
        var image = wp.media({
            title: 'Select Push Icon',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
                .on('select', function (e) {
                    // This will return the selected image from the Media Uploader, the result is an object
                    var uploaded_image = image.state().get('selection').first();
                    // We convert uploaded_image to a JSON object to make accessing it easier
                    // Output to the console uploaded_image
                    console.log(uploaded_image);
                    var image_url = uploaded_image.toJSON().url;
                    // Let's assign the url value to the input field
                    jQuery('#image_url').val(image_url);
                    jQuery("#pre_icon").attr("src", image_url);
                });
    });

//////////////// UPDATE Pass////
    jQuery("#sendbtn").click(function (e) {
 
  e.stopImmediatePropagation();
    e.preventDefault();
        var wp_path = document.getElementById("wp_path").value


        jQuery('#resp').focus()
        jQuery('#sendmsgform').hide(500)
        jQuery('#resp').html("<div class='text-center'><img  style='width:100px' src='" + wp_path + "'/> <br/><br/> <strong>Processing, please wait....</strong></div>")

        jQuery.ajax({
            url: "admin-post.php",
            type: 'POST',
            timeout: 15000,
            data: jQuery("#sendmsgform").serialize(),
            success: function (data, textStatus, xhr) {

                var stat = explode('#', data)

                if (stat[1] == '1') {

                    jQuery('#resp').html('<div class="alert alert-success">' + stat[0] + ' </div>')

                    //window.setTimeout('location.reload()', 5000); //reloads after 3 seconds

                } else if (stat[1] == '2') {
                    jQuery('#sendmsgform').show(500)

                    jQuery('#resp').html('<div class="alert alert-danger">' + stat[0] + '</div>')
                } else {
                    jQuery('#sendmsgform').show(500)
                    jQuery('#resp').html('<div class="alert alert-danger">' + data + '</div>')

                }

            },
            error: function (xhr, textStatus, errorThrown) {
                jQuery('#sendmsgform').show(500)
                jQuery('#resp').html('<div class="alert alert-danger">Error:  check your connection and Please try later</div>');
                return false;
            }
        });

    });


jQuery("#sync_cat").click(function (e) {
 
  e.stopImmediatePropagation();
    e.preventDefault();
        var wp_path = document.getElementById("wp_path").value
  

       
        jQuery('#sync_cat').hide(500)
        jQuery('#sync_resp').html("<div class='text-center'><img  style='width:100px' src='" + wp_path + "'/> <br/><br/> <strong>Processing, please wait....</strong></div>")

        jQuery.ajax({
             url: "admin-post.php",
            type: 'POST',
            timeout: 15000,
            data: jQuery("#syncform").serialize(),
            success: function (data, textStatus, xhr) {

                    jQuery('#sync_cat').show(500)
                    jQuery('#sync_resp').html(data)

            

            },
            error: function (xhr, textStatus, errorThrown) {
                jQuery('#sendmsgform').show(500)
                jQuery('#resp').html('<div class="alert alert-danger">Error:  check your connection and Please try later</div>');
                return false;
            }
        });

    });

});

