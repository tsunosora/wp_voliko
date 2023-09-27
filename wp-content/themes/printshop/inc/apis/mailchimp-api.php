<?php //Add hook script
add_action( 'wp_footer', 'printshop_action_script' );
function printshop_action_script(){?><script>    (function($){        
    // Subcribe mailchimp        
    $('#printshop-subcribe').submit(function() {            
        // update user interface            
        $('#printshop-response, .subcribe-message').html('<?php esc_html_e('Adding email address...','printshop');?>');            
        $.ajax({                url: '<?php echo admin_url( "admin-ajax.php" ); ?>',                
        data: 'ajax=true&email=' + escape($('#email-subcriber').val()),                
        success: function(msg) {                    
        $('#printshop-response, .subcribe-message').html(msg);                }            });            return false;        });    })(jQuery)</script><?php } 
        //Add request data
        add_action('wp_ajax_printshop_ApiSubcribe', 'printshop_storeAddress');
        add_action('wp_ajax_nopriv_printshop_ApiSubcribe', 'printshop_storeAddress');
        function printshop_storeAddress(){    global $printshop_option;   
        //var_dump($printshop_option['mailchimp-api']);    
        if (isset($printshop_option['mailchimp-api'])) {        $api_value  = $printshop_option['mailchimp-api'];    }else{        $api_value  = "";    }    if (isset($printshop_option['mailchimp-groupid'])) {        $list_id    = $printshop_option['mailchimp-groupid'];    }else{        $list_id ="";    }	
        // Validation	
        if(!$_GET['email']){ return "No email address provided"; }	
        if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$/i", $_GET['email'])) {		return "Email address is invalid";        exit();	}	require_once(get_template_directory().'/libs/MailChimp/MCAPI.class.php');	
        // grab an API Key from http://admin.mailchimp.com/account/api/	
        $api = new MCAPI($api_value);	
        // grab your List's Unique Id by going to http://admin.mailchimp.com/lists/	
        // Click the "settings" link for the list - the Unique Id is at the bottom of that page.	
        //$list_id = "my_list_unique_id";	
        if($api->listSubscribe($list_id, $_GET['email'], '') === true) {		
            // It worked!		
            return 'Success! Check your email to confirm sign up.';        exit();	}else{		
                // An error ocurred, return error message		
                return 'Error: ' . $api->errorMessage;        exit();	}    exit();}
                // If being called via ajax, autorun the function
                if(isset($_GET['ajax'])){ echo printshop_storeAddress(); exit();}?>