jQuery(document).ready(function($) {
    let siteUrl = script_object.getSiteUrl ? script_object.getSiteUrl : "";
    window.addEventListener("message", (e) => {
        console.log(e.data);
        if (typeof e.data === "object") return;
        if (e.data == "send successfully") {
            swal({
                title: "Successfully !",
                text: "Wellcome to worksuite !",
                icon: "success",
            });
            window.location.href = siteUrl +'/wp-admin/admin.php?page=dashboard';
        }
    });
    jQuery(document).on('click',".js-register-quickly, .wp-submenu li a",(e)=> {
        jQuery(document).find('#loading').css('display','none');
        jQuery('body').append( '<div style="display:block" id="loading"></div>' );
    });
    jQuery('iframe').load(function() {
        $('#loading').css('display','none');
    })

});
