jQuery(function ($) {
    $('.nbtwccs_multiple_simple_panel').show(200);   

    jQuery.fn.life = function (types, data, fn) {
        jQuery(this.context).on(types, this.selector, data, fn);
        return this;
    };

    $('.nbtwccs_price_col .wc_input_price, .chosen_select').life('change', function () {
        nbtwccs_enable_save_variation_changes();
    });

});


function nbtwccs_open_tab(tabName, product_id) {
    var i = 0;
    var x = document.getElementsByClassName("nbtwccs_tab");
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";
    }
    document.getElementById(tabName + '_' + product_id).style.display = "block";

    
    jQuery('.nbtwccs_tab_button').removeClass('active');
    jQuery('#' + tabName + '_btn_' + product_id).addClass('active');
}



function nbtwccs_add_product_price(post_id) {
    var code = jQuery('#nbtwccs_multiple_simple_select_' + post_id).val();
    if (code) {
        var html = jQuery('#nbtwccs_multiple_simple_tpl').html();
        html = html.replace(/__POST_ID__/gi, post_id);
        html = html.replace(/__CURR_CODE__/gi, code);
        jQuery('#nbtwccs_multiple_simple_list_' + post_id).append(html);
        
        jQuery("#nbtwccs_multiple_simple_select_" + post_id + " option[value='" + code + "']").remove();
        jQuery("#nbtwccs_multiple_simple_select_" + post_id + " option").eq(0).prop('selected', 'selected');
    }
    nbtwccs_enable_save_variation_changes();
}

function nbtwccs_add_all_product_price(post_id) {
    jQuery.each(jQuery("#nbtwccs_multiple_simple_select_" + post_id + " option"), function (i, code) {
        jQuery("#nbtwccs_multiple_simple_select_" + post_id + " option[value='" + code + "']").prop('selected', 'selected');
        nbtwccs_add_product_price(post_id);
    });
}
function nbtwccs_add_fixed_field(post_id,selector) {
    var code = jQuery('#nbtwccs_multiple_simple_select_'+selector +'_'+ post_id).val();
    if (code) {
        var html = jQuery('#nbtwccs_multiple_simple_tpl_'+selector).html();
        html = html.replace(/__POST_ID__/gi, post_id);
        html = html.replace(/__CURR_CODE__/gi, code);
        jQuery('#nbtwccs_multiple_simple_list_'+selector +'_' + post_id).append(html);
        
        jQuery("#nbtwccs_multiple_simple_select_"+selector +'_' + post_id + " option[value='" + code + "']").remove();
        jQuery("#nbtwccs_multiple_simple_select_"+selector +'_' + post_id + " option").eq(0).prop('selected', 'selected');
    }
    nbtwccs_enable_save_variation_changes();
}

function nbtwccs_add_all_fixed_field(post_id,selector) {
    jQuery.each(jQuery("#nbtwccs_multiple_simple_select_"+selector +"_" + post_id + " option"), function (i, code) {
        jQuery("#nbtwccs_multiple_simple_select_"+selector +"_" + post_id + " option[value='" + code + "']").prop('selected', 'selected');
        nbtwccs_add_fixed_field(post_id,selector);
    });
}

function nbtwccs_add_select_product_price(post_id, code) {
    jQuery('#nbtwccs_multiple_simple_select_' + post_id).append('<option value="' + code + '">' + code + '</option>');
}
function nbtwccs_add_select_fixed_field(post_id, code,selector) {
    jQuery('#nbtwccs_multiple_simple_select_'+selector +'_' + post_id).append('<option value="' + code + '">' + code + '</option>');
}

function nbtwccs_remove_li_product_price(post_id, code, geo) {

    if (geo) {
        
        jQuery('#nbtwccs_multiple_simple_list_geo_' + post_id + ' #nbtwccs_li_geo_' + post_id + '_' + code).remove();
    } else {
        jQuery('#nbtwccs_multiple_simple_list_' + post_id + ' #nbtwccs_li_' + post_id + '_' + code).remove();
    }

    nbtwccs_add_select_product_price(post_id, code);
    nbtwccs_enable_save_variation_changes();
}
function nbtwccs_remove_li_fixed_field(post_id, code, geo,selector) {

    if (geo) {
        
        jQuery('#nbtwccs_multiple_simple_list_geo_'+selector +'_' + post_id + ' #nbtwccs_li_geo_' + post_id + '_' + code).remove();
    } else {
        jQuery('#nbtwccs_multiple_simple_list_'+selector +'_' + post_id + ' #nbtwccs_li_' + post_id + '_' + code).remove();
    }

    nbtwccs_add_select_fixed_field(post_id, code,selector);
    nbtwccs_enable_save_variation_changes();
}

function nbtwccs_enable_save_variation_changes() {
    
    jQuery('.form-row textarea').trigger('change');
}

/**************************************/

function nbtwccs_add_group_geo(post_id) {
    var html = jQuery('#nbtwccs_multiple_simple_tpl_geo').html();
    html = html.replace(/__POST_ID__/gi, post_id);
    var d = new Date();
    var index = d.getTime();
    html = html.replace(/__INDEX__/gi, index);
    jQuery('#nbtwccs_multiple_simple_list_geo_' + post_id).append(html);    
    jQuery('#nbtwccs_li_geo_' + post_id + '_' + index + ' select').chosen();
    jQuery('#nbtwccs_li_geo_' + post_id + '_' + index + ' select').trigger("liszt:updated");
    
    nbtwccs_enable_save_variation_changes();
}
