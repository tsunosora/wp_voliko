<?php 
if (!defined('ABSPATH')) exit;
$in_quick_view  = false;
$is_wqv         = false;
if( (isset($_REQUEST['wc-api']) && $_REQUEST['wc-api'] == 'WC_Quick_View') || (isset($_REQUEST['action']) && $_REQUEST['action'] == 'yith_load_product_quick_view') ){
    $in_quick_view = true;
    if(isset($_REQUEST['wc-api']) && $_REQUEST['wc-api'] == 'WC_Quick_View') $is_wqv = true;
}
$appid              = "nbo-app-" . time() . rand( 1, 1000 );
$display_type       = nbdesigner_get_option( 'nbdesigner_option_display', '1' );
$nbd_qv_type        = nbdesigner_get_option( 'nbdesigner_display_product_option', '1' );
$sublist_position   = nbdesigner_get_option( 'nbdesigner_ad_sublist_position', 'b' );
$in_design_editor   = false;

$nbd_product_option = get_post_meta( $product_id, '_nbdesigner_option', true );
if( $nbd_product_option ){
    $_nbd_product_option    = unserialize( $nbd_product_option );
    $dimension_unit         = isset( $_nbd_product_option['unit'] ) ? $_nbd_product_option['unit'] : nbdesigner_get_option( 'nbdesigner_dimensions_unit', 'cm' );
} else {
    $dimension_unit = nbdesigner_get_option( 'nbdesigner_dimensions_unit', 'cm' );
}

if( isset($_REQUEST['wc-api']) && $_REQUEST['wc-api'] == 'NBO_Quick_View'){
    if( isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'catalog'){
        $nbd_qv_type == '1';
    }else{
        $in_design_editor = true;
    }
    if( $nbd_qv_type == '2'){
        $display_type       = 1;
        $sublist_position   = 'b';
    }
}
global $in_nbau_mode_2;
$group_mode = false;
if( !function_exists( 'get_field_index_by_id' ) ){
    function get_field_index_by_id( $fid, $fields ){
        foreach( $fields as $f_index => $field ){
            if( $field['id'] == $fid ){
                return $f_index;
            }
        }
    }
}
if( !function_exists( 'get_field_by_id' ) ){
    function get_field_by_id( $fid, $fields ){
        foreach( $fields as $f_index => $field ){
            if( $field['id'] == $fid ){
                return $field;
            }
        }
    }
}
if( $options['display_type'] == 4 && isset( $options["groups"] ) && is_array($options["groups"]) && count($options["groups"]) ){
    foreach( $options["groups"] as $group ){
        if( isset( $group['fields'] ) && count( $group['fields'] ) ){
            $group_mode = true;
            foreach( $group["fields"] as $f ){
                $f_index = get_field_index_by_id( $f, $options["fields"] );
                $options["fields"][$f_index]['show_in_group'] = true;
            }
        }
    }
}

$popup_mode     = false;
if( $options['display_type'] == 6 && isset( $options["popup_fields"] ) && count( $options["popup_fields"] ) ){
    $popup_mode     = true;
    foreach($options["popup_fields"] as $popup_field){
        $popup_field_index = get_field_index_by_id( $popup_field, $options["fields"] );
        $options["fields"][$popup_field_index]['show_in_group'] = true;
    }
}

if( $options['display_type'] == 5 ) {
    $display_type = 1;
}
$prefix             = $display_type == 2 ? '-2' : '';
$style_class        = $display_type == 2 ? 'nbo-style-2' : 'nbo-style-1';
$hide_swatch_label  = nbdesigner_get_option( 'nbdesigner_hide_option_swatch_label', 'yes' );
$enable_gallery_api = false;
if( !function_exists( 'get_gallery_folder' ) ){
    function get_gallery_folder( $product_id ){
        $tem = '';
        if( isset( $_GET['nbo_cart_item_key'] ) ){
            $cart_item_key = $_GET['nbo_cart_item_key'];
            if( isset( WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds'] ) ){
                $tem = WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds']['nbd'];
                return $tem;
            }
        }
        $template = nbd_get_templates( $product_id, 0, '', true );
        if( isset( $template[0] ) ){
            $tem = $template[0]['folder'];
        }
        return $tem;
    }
}
$template_folder    = '';
if( nbdesigner_get_option( 'nbdesigner_enable_gallery_api', 'no' ) == 'yes' ){
    if( is_nbdesigner_product( $product_id ) ){
        $enable_gallery_api = true;
        $template_folder    = get_gallery_folder( $product_id );
    }
}

$currentDir = realpath(dirname(__FILE__));

?>
<div class="nbo-wrapper <?php if($is_wqv) echo 'nbd-option-in-wqv'; ?> <?php echo 'wrapper-type-' . $display_type; ?>">
<style>
    /* tipTip */
    #tiptip_holder {
        display: none;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 9999999999;
    }
    #tiptip_holder.tip_top {
        padding-bottom: 5px;
    }
    #tiptip_holder.tip_bottom {
        padding-top: 5px;
    }
    #tiptip_holder.tip_right {
        padding-left: 5px;
    }
    #tiptip_holder.tip_left {
        padding-right: 5px;
    }
    #tiptip_content {
        font-size: 11px;
        color: #fff;
        text-shadow: 0 0 2px #000;
        padding: 4px 8px;
        border: 1px solid rgba(255,255,255,0.25);
        background-color: rgb(25,25,25);
        background-color: rgba(25,25,25,0.92);
        background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(transparent), to(#000));
        border-radius: 3px;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        box-shadow: 0 0 3px #555;
        -webkit-box-shadow: 0 0 3px #555;
        -moz-box-shadow: 0 0 3px #555;
    }
    #tiptip_arrow, #tiptip_arrow_inner {
        position: absolute;
        border-color: transparent;
        border-style: solid;
        border-width: 6px;
        height: 0;
        width: 0;
    }
    .rtl #tiptip_arrow{
        right:50%;
        margin-right:-6px
    }
    #tiptip_holder.tip_top #tiptip_arrow {
        border-top-color: #fff;
        border-top-color: rgba(255,255,255,0.35);
    }
    #tiptip_holder.tip_bottom #tiptip_arrow {
        border-bottom-color: #fff;
        border-bottom-color: rgba(255,255,255,0.35);
    }
    #tiptip_holder.tip_right #tiptip_arrow {
        border-right-color: #fff;
        border-right-color: rgba(255,255,255,0.35);
    }
    #tiptip_holder.tip_left #tiptip_arrow {
        border-left-color: #fff;
        border-left-color: rgba(255,255,255,0.35);
    }
    #tiptip_holder.tip_top #tiptip_arrow_inner {
        margin-top: -7px;
        margin-left: -6px;
        border-top-color: rgb(25,25,25);
        border-top-color: rgba(25,25,25,0.92);
    }
    .rtl #tiptip_holder.tip_top #tiptip_arrow_inner{
        margin-right:-6px;
    }
    #tiptip_holder.tip_bottom #tiptip_arrow_inner {
        margin-top: -5px;
        margin-left: -6px;
        border-bottom-color: rgb(25,25,25);
        border-bottom-color: rgba(25,25,25,0.92);
    }
    .rtl #tiptip_holder.tip_bottom #tiptip_arrow_inner {
        margin-right:-6px;
    }
    #tiptip_holder.tip_right #tiptip_arrow_inner {
        margin-top: -6px;
        margin-left: -5px;
        border-right-color: rgb(25,25,25);
        border-right-color: rgba(25,25,25,0.92);
    }
    .rtl #tiptip_holder.tip_right #tiptip_arrow_inner {
        margin-right:-5px;
    }
    #tiptip_holder.tip_left #tiptip_arrow_inner {
        margin-top: -6px;
        margin-left: -7px;
        border-left-color: rgb(25,25,25);
        border-left-color: rgba(25,25,25,0.92);
    }
    .rtl #tiptip_holder.tip_left #tiptip_arrow_inner {
        margin-right:-7px;
    }
    @media screen and (-webkit-min-device-pixel-ratio:0) {
        #tiptip_content {
            padding: 4px 8px 5px 8px;
            background-color: rgba(45,45,45,0.88);
        }
        #tiptip_holder.tip_bottom #tiptip_arrow_inner { 
            border-bottom-color: rgba(45,45,45,0.88);
        }
        #tiptip_holder.tip_top #tiptip_arrow_inner { 
            border-top-color: rgba(20,20,20,0.92);
        }
    }
    .nbo-disabled {
        opacity: 0.3;
        pointer-events: none;
    }
    .nbo-prevent-pointer {
        pointer-events: none;
    }
    .nbd-help-tip {
        vertical-align: middle;
        cursor: help;
        margin: -2px -24px 0 5px;
        line-height: 1;
        color: #fff !important;
        background: #333333;
        border-radius: 50%;
        display: inline-block;
        font-size: 10px;
        font-style: normal;
        height: 12px;
        position: relative;
        width: 12px;  
        z-index: 2;
    }
    .nbd-help-tip::after {
        font-family: monospace;
        speak: none;
        font-weight: 400;
        text-transform: none;
        line-height: 1;
        -webkit-font-smoothing: antialiased;
        text-indent: 0px;
        position: absolute;
        top: 1px;
        left: 0px;
        width: 100%;
        height: 100%;
        text-align: center;
        content: "i";
        cursor: help;
        font-variant: normal;
        margin: 0px;
    }
    .rtl .nbd-help-tip::after {
        top:0;
        right:0;
    }
    .rtl .nbd-help-tip {
        margin: 0 9px 0 0;
    }
    /* End tipTip */ 
    /* nbd-radio */
    @keyframes ripple {
        0% {
            box-shadow: 0px 0px 0px 1px rgba(0, 0, 0, 0);
        }
        50% {
            box-shadow: 0px 0px 0px 15px rgba(0, 0, 0, 0.1);
        }
        100% {
            box-shadow: 0px 0px 0px 15px rgba(0, 0, 0, 0);
        }
    }    
    @-webkit-keyframes ripple {
        0% {
            box-shadow: 0px 0px 0px 1px rgba(0, 0, 0, 0);
        }
        50% {
            box-shadow: 0px 0px 0px 15px rgba(0, 0, 0, 0.1);
        }
        100% {
            box-shadow: 0px 0px 0px 15px rgba(0, 0, 0, 0);
        }
    } 
    .nbd-radio input[type="radio"]:checked + label:before,
    .nbo-sub-attr-r input[type="radio"]:checked + label:before {
        border-color: #404762;
        animation: ripple 0.2s linear forwards;
    }
    .nbd-radio input[type="radio"]:checked + label:after,
    .nbo-sub-attr-r input[type="radio"]:checked + label:after {
        transform: scale(1);
    }
    .nbo-sub-attr-r label {
        background: unset !important;
        color: unset !important;
    }
    .nbd-radio label,
    .nbo-sub-attr-r label {
        display: inline-block;
        height: 20px;
        position: relative;
        padding: 0 30px;
        margin-bottom: 0;
        cursor: pointer;
        line-height: 20px;
    }
    .nbd-radio label:before, .nbd-radio label:after,
    .nbo-sub-attr-r label:before, .nbo-sub-attr-r label:after {
        position: absolute;
        content: '';
        border-radius: 50%;
        transition: all .3s ease;
        transition-property: transform, border-color;
        box-sizing: border-box;
    }
    .nbd-radio label:before,
    .nbo-sub-attr-r label:before {
        left: 0;
        top: 0;
        width: 20px;
        height: 20px;
        border: 2px solid rgba(0, 0, 0, 0.54);
    }
    .nbd-radio label:after,
    .nbo-sub-attr-r label:after {
        top: 5px;
        left: 5px;
        width: 10px;
        height: 10px;
        transform: scale(0);
        background: #404762;
    }    
    .nbo-sub-attr-l label:after, .nbo-sub-attr-l label:before {
        display: none;
    }
    /* end. nbd-radio */
    [ng\:cloak], [ng-cloak], [data-ng-cloak], [x-ng-cloak], .ng-cloak, .x-ng-cloak {
      display: none !important;
    }
    .nbd-option-wrapper {
        margin-bottom: 1.1em;
    }
    .nbd-option-field {
        background-color: #fff;
        margin-bottom: 1.1em;
        border: 1px solid #f8f8f8;
    }
    .nbd-option-field select,
    .nbd-option-field input[type="text"]{
        min-width: 150px;
    }
    td .nbo-dimension-label{
        min-width: 50px;
        display: inline-block;
    }
    .nbd-field-header {
        padding: 10px;
        background: #f8f8f8;
        color: #404762;
        font-weight: bold;
    }
    .nbd-field-header label {
        font-weight: bold;
    }
    .nbd-field-content {
        padding: 10px;
    }    
    .nbd-field-content:after,
    .nbd-field-header:after {
        content: '';
        display: block;
        clear: both;
    }
    .nbd-option-wrapper label {
        cursor: pointer;
        margin: 0 !important;
        margin: 0px 4px 2px!important;
    }
    .nbd-swatch {
        width: 36px;
        height: 36px;
        display: inline-block;
        border-radius: 50%; 
        cursor: pointer;
        border: 2px solid #ddd;
        position: relative;
    }
    .nbo-checkbox {
        width: 36px;
        height: 36px;
        display: inline-block;
        cursor: pointer;
        border: 2px solid #ddd;
    }
    .nbd-option-wrapper input[type="radio"], .nbo-checkbox-wrap input[type="checkbox"] {
        display: none;
    }
    .nbd-swatch-wrap input[type="radio"]:checked + label,
    .nbo-checkbox-wrap input[type="checkbox"]:checked + label {
        border: 2px solid #404762;
        position: relative;
        display: inline-block;
    }
    .nbd-swatch-wrap input[type="radio"]:checked + label:before {
        display: block;
        top: 0;
        left: 0;
        border: 2px solid #fff;
        position: absolute;
        z-index: 2;
        width: 100%;
        height: 100%;
        content: '';
        border-radius: 100%;
        box-sizing: border-box;
    }
    .nbd-swatch-wrap input[type="radio"]:checked + label:after, 
    .nbo-checkbox-wrap input[type="checkbox"]:checked + label:after {
        -webkit-transform: rotate(45deg);
        -moz-transform: rotate(45deg);
        transform: rotate(45deg);
        content: "";
        width: 6px;
        height: 10px;
        display: block;
        border: solid #404762;
        border-width: 0 2px 2px 0;
        position: absolute;
        top: 7px;
        left: 12px;
    }
    .nbo-dropdown {
        border: 1px solid #EEE;
        height: 36px;
        padding: 3px 36px 3px 8px;
        background-color: transparent;
        line-height: 100%;
        outline: 0;
        background-image: url(<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/arrow.png'; ?>);
        background-position: right;
        background-repeat: no-repeat;
        position: relative;
        cursor: pointer;
        -webkit-appearance: none;
        -moz-appearance: none;
    }
    .nbd-label, .nbo-sub-attr-l label{
        border-radius: 36px;
        height: 36px;
        line-height: 36px;
        padding: 0 20px;
        background: #ddd;
        text-transform: uppercase;
        font-size: 13px;
        display: inline-block;
        margin: 0 5px 5px 0;
        -webkit-box-shadow: 0 1px 4px 0 rgba(0,0,0,0.14);
        -moz-box-shadow: 0 1px 4px 0 rgba(0,0,0,0.14);
        -ms-box-shadow: 0 1px 4px 0 rgba(0,0,0,0.14);
        box-shadow: 0 1px 4px 0 rgba(0,0,0,0.14);
        color: #757575;
        -webkit-transition: all 0.4s;
        -moz-transition: all 0.4s;
        -ms-transition: all 0.4s;
        transition: all 0.4s;
        background: #eee;
        max-width: 100%;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }
    .nbd-label-wrap input[type="radio"]:checked + label,
    .nbo-sub-attr-l input[type="radio"]:checked + label {
        background: #404762;
        color: #fff;
    }
    .nbd-label:hover, .nbo-sub-attr-l label:hover {
        -webkit-box-shadow: 0 3px 10px 0 rgba(75,79,84,.3);
        -moz-box-shadow: 0 3px 10px 0 rgba(75,79,84,.3);
        -ms-box-shadow: 0 3px 10px 0 rgba(75,79,84,.3);
        box-shadow: 0 3px 10px 0 rgba(75,79,84,.3);
    }    
    .nbd-swatch-wrap .nbd-field-content{
        font-size: 0;
    }
    .nbd-required {
        color: red !important;
    }
    .nbd-field-input-wrap input[type="number"] {
        padding: 0.418047em;
        background-color: #f2f2f2;
        color: #43454b;
        outline: 0;
        border: 0;
        -webkit-appearance: none;
        box-sizing: border-box;
        font-weight: 400;
        box-shadow: inset 0 1px 1px rgba(0,0,0,.125);
        width: 4.235801032em;
        text-align: center;
    }
    .nbd-field-content input[type="range"] {
        margin-left: 0;
        flex: 1;
    }
    .nbd-field-content input[type="range"] {
        padding: 0;
        -webkit-appearance: none;
        background: transparent;
        border: none;
        box-shadow: none;
    }
    .nbd-field-content input[type="range"][name="nbo-quantity"]{
        max-width: 200px;
    }
    .nbd-field-content input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        height: 24px;
        width: 24px;
        border-radius: 50%;
        cursor: pointer;
        background: #404762;
        border: 4px solid transparent;
        background-clip: padding-box;
        box-sizing: border-box;
        margin-top: -10px
    }
    .nbd-field-content input[type="range"]::-moz-range-thumb {
        height: 18px;
        width: 18px;
        border-radius: 50%;
        cursor: pointer;
        background: #404762;
        border: 4px solid transparent;
        background-clip: padding-box;
        box-sizing: border-box
    }
    .nbd-field-content input[type="range"]::-ms-thumb {
        border-radius: 50%;
        cursor: pointer;
        background: #404762;
        border: 4px solid transparent;
        background-clip: padding-box;
        box-sizing: border-box;
        margin-top: 0;
        height: 14px;
        width: 14px;
        border: 2px solid transparent
    }
    .nbd-field-content input[type="range"]:focus {
        outline: none
    }
    .nbd-field-content input[type="range"]:focus::-webkit-slider-thumb {
        background-color: #fff;
        color: #191e23;
        box-shadow: inset 0 0 0 1px #6c7781,inset 0 0 0 2px #fff;
        outline: 2px solid transparent;
        outline-offset: -2px
    }
    .nbd-field-content input[type="range"]:focus::-moz-range-thumb {
        background-color: #fff;
        color: #191e23;
        box-shadow: inset 0 0 0 1px #6c7781,inset 0 0 0 2px #fff;
        outline: 2px solid transparent;
        outline-offset: -2px
    }
    .nbd-field-content input[type="range"]:focus::-ms-thumb {
        background-color: #fff;
        color: #191e23;
        box-shadow: inset 0 0 0 1px #6c7781,inset 0 0 0 2px #fff;
        outline: 2px solid transparent;
        outline-offset: -2px
    }
    .nbd-field-content input[type="range"]::-webkit-slider-runnable-track {
        height: 3px;
        cursor: pointer;
        background: #e2e4e7;
        border-radius: 1.5px;
        margin-top: -4px
    }
    .nbd-field-content input[type="range"]::-moz-range-track {
        height: 3px;
        cursor: pointer;
        background: #e2e4e7;
        border-radius: 1.5px
    }
    .nbd-field-content input[type="range"]::-ms-track {
        margin-top: -4px;
        background: transparent;
        border-color: transparent;
        color: transparent;
        height: 3px;
        cursor: pointer;
        background: #e2e4e7;
        border-radius: 1.5px
    }
    .nbd-field-content .nbd-invalid-notice {
        display: none;
        font-size: 0.75em;
        color: red;
    }
    .nbd-field-content input.ng-invalid-min ~ .nbd-invalid-min {
        display: inline-block;
    }
    .nbd-field-content input.ng-invalid-max ~ .nbd-invalid-max {
        display: inline-block;
    } 
    .nbd-invalid-form {
        color: red;
    }
    .nbo-disabled {
        opacity: .5!important;
        cursor: not-allowed;
    }
    .nbo-hidden {
        display: none;
    }
    .nbo-table-wrap {
        margin: 0 0 1.41575em;
    }
    .nbo-price-matrix, .nbo-table-wrap {
        max-width: 100%;
        overflow-x: scroll;
        overflow: auto;
    }
    .nbo-table-wrap table {
        margin: 0 !important;
    }
    .nbo-table-wrap table input[type="number"]{
        width: 4em;
        padding: 0.418047em;
        background-color: #f2f2f2;
        color: #43454b;
        outline: 0;
        border: 0;
        -webkit-appearance: none;
        box-sizing: border-box;
        font-weight: 400;
        box-shadow: inset 0 1px 1px rgba(0,0,0,.125);
        width: 4.235801032em;
        text-align: center;
    }    
    .nbo-price-matrix table, .nbo-table-wrap table{
        border-collapse: collapse;
    }
    .nbo-price-matrix table, .nbo-price-matrix td, .nbo-price-matrix th,
    .nbo-table-wrap table, .nbo-table-wrap td, .nbo-table-wrap th {
        text-align: center;
        border: 1px solid #ddd;
        vertical-align: middle;
        padding: 0.75em 0.75em;
    }
    .nbd-product-tab .nbo-price-matrix td, .nbd-product-tab .nbo-price-matrix th {
        text-align: center !important;
        white-space: nowrap;
    }
    .nbo-price-matrix td, .nbo-table-wrap td {
        cursor: pointer;
    }
    .nbo-pm-empty, .nbo-price-matrix th {
        pointer-events: none;
    }
    .nbo-price-matrix td.selected {
        background: #404762 !important;
        color: #fff !important;
    }
    .nbo-price-matrix td.disabled {
        background-color: #f8f8f8;
        pointer-events: none;
    }
    .nbo-dimension {
        width: 4em;
        background: #fff !important;
        box-shadow: none !important;
        height: 36px !important;
    }
    input.nbo-dimension::-webkit-outer-spin-button,
    input.nbo-dimension::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input.nbo-dimension {
        -moz-appearance:textfield;
    }
    .nbo-dimension-wrap {
        border: solid 1px #eee;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        display: inline-block;
    }
    .nbo-updown-dimension {
        display: inline-block;
        width: 36px;
        height: 36px;
        vertical-align: top;
        line-height: 36px;
        text-align: center;
        background: #eee;
        font-size: 24px !important;
        cursor: pointer;
    }
    .nbd-input-range {
        margin-left: 5px;
        background: #404762;
        padding: 0 15px;
        line-height: 20px;
        vertical-align: middle;
        color: #fff;
        border-radius: 20px;
        display: inline-block;
    }    
    .nbo-final-price {
        background: #eee;
        color: #404762;
        font-weight: bold;
    }
    .nbd-swatch-label-wrap {
        display: flex;
        align-items: center;
    }
    .nbd-swatch-label-wrap:not(:last-child){
        margin-bottom: 10px;  
    }
    .nbd-swatch-description {
        font-size: 14px;
        margin-left: 10px;
    }
    .nbo-clear-option-wrap {
        text-align: right;
        margin-bottom: 1em;
        overflow: hidden;
    }
    .nbo-clear-option-wrap:after{
        content: '';
        display: block;
        clear: both;
    }
    .nbo-clear-option-wrap .nbd-button {
        float: right !important;
    }
    .nbo-style-1 {
        border: 1px solid #f8f8f8;
        margin-bottom: 1em;
    }
    .nbo-style-1 .nbo-summary-title,
    .nbo-style-1 .nbo-table-pricing-title{
        padding: 10px;
        background: #f8f8f8;
        margin: 0;
        display: flex;
        justify-content: space-between;
    }
    .nbo-style-1 .nbo-summary-title:after {
        clear: both;
    }
    .nbo-style-1 .nbo-summary-table ,
    .nbo-style-1 .nbo-table-pricing {
        margin: 0;
        width: 100%;
    }
    .nbo-toggle {
        text-align: center;
        cursor: pointer;
    }
    .nbo-toggle svg {
        vertical-align: top;
        height: 100%;
    }
    .nbd-swatch-tooltip, 
    .nbo-group-timeline-tooltip {
        background: #404762;
        color: #fff !important;
        border-radius: 4px;
        font-size: 14px;
        font-weight: bold;
        position:  absolute;
        bottom: 50%;
        left: 50%;
        pointer-events: none;
        padding: 5px 7px;
        visibility: hidden;
        opacity: 0;
        -webkit-transform: translate3d(-50%,0%,0);
        -moz-transform: translate3d(-50%,0%,0);
        transform: translate3d(-50%,0%,0);
/*         width: -webkit-max-content;
        width: -moz-max-content;
        width: max-content; */
        display: inline-block;
        white-space: nowrap;
        max-width: 200px;
        z-index: 99;
        -webkit-transition: all .4s;
        -moz-transition: all .4s;
        transition: all .4s; 
    }
    .nbd-swatch-tooltip:before, 
    .nbo-group-timeline-tooltip:before {
        content: '';
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 5px solid #404762;
        position: absolute;
        bottom: -5px;
        margin-left: -3px;
        left: 50%;
    }
    .nbd-swatch:hover .nbd-swatch-tooltip {
        bottom: 40px;
        visibility: visible;
        opacity: 1; 
    }
    .nbo-group-timeline-step:hover .nbo-group-timeline-tooltip {
        bottom: 16px;
        visibility: visible;
        opacity: 1; 
    }
    .nbo-wrapper:afte {
        content: '';
        display: block;
        clear: both;
    }
    .nbo-bold {
        font-weight: bold;
    }
    .nbo-sub-attr-wrap {
        padding: 10px;
        border: 1px solid #f8f8f8;
        margin-top: 5px;
    }
    .nbd-input-u {
        cursor: pointer;
        padding: 30px;
        width: 100%;
        border: 2px dashed #ddd;
        border-radius: 4px;
    }
    .nbd-field-textarea {
        border: 1px solid #ddd;
        width: 100%;
    }
    @media (min-width: 769px){
        .nbd-tb-options td:first-child {
            width: 30%;
        }
    }
    .nbo-group-wrap {
        margin-bottom: 15px;
    }
    .nbo-group-header {
        border-bottom: 3px solid #f8f8f8;
        height: 40px;
        overflow: hidden;
    }
    .nbo-group-header .group-title {
        background: #f8f8f8;
        color: #404762;
        display: inline-block;
        position: relative;
        padding: 0 15px;
        height: 40px;
        line-height: 40px;
        box-sizing: border-box;
        border-top-left-radius: 4px;
        font-weight: bold;
        max-width: calc(100% - 50px);
    }
    .nbo-group-header .group-title:after {
        position: absolute;
        right: -40px;
        top: 0;
        display: block;
        content: '';
        width: 0;
        height: 0;
        border-color: transparent;
        border-width: 0;
        border-top: 40px solid transparent;
        border-left: 40px solid #f8f8f8;
    }
    .nbo-group-header .group-title .nbo-group-icon {
        width: 30px;
        height: 30px;
        vertical-align: middle;
        /* border: 2px solid #fff; */
        box-sizing: border-box;
        border-radius: 50%;
        display: inline-block;
        margin: 0 10px 0 -5px;
    }
    .nbo-group-header .group-title .nbo-group-icon span {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        text-align: center;
    }
    .nbo-group-header .group-title .nbo-group-icon img {
        max-width: 100%;
        max-height: 100%;
        display: inline-block;
    }
    .nbo-group-header .group-title .nbd-help-tip {
        position: absolute;
        top: 18px;
    }
    .nbo-group-footer {
        background: #f8f8f8;
        padding: 10px;
        border-bottom-right-radius: 4px;
        border-bottom-left-radius: 4px;
    }
    .nbo-group-wrap .nbo-group-body {
        display: flex;
         -webkit-align-items: flex-end; 
        -ms-flex-align: end;
         align-items: flex-start; 
         display: -webkit-flex; 
        display: -ms-flexbox;
        -webkit-flex-wrap: wrap;
        -ms-flex-wrap: wrap;
         flex-wrap: wrap; 
        padding: 10px 5px;
        border: 1px solid #f8f8f8;
        position: relative;
        background-color: #fff;
    }
    .nbo-clearfix:after {
        content: '';
        display: block;
        clear: both;
    }
    .nbo-group-wrap .nbo-group-body .nbd-option-field {
        width: calc(100% - 10px);
        margin: 0 5px 10px;
        -webkit-transition: all 0.4s;
        -moz-transition: all 0.4s;
        transition: all 0.4s;
    }
    .nbo-flex-col-2 .nbo-group-body .nbd-option-field {
        flex-basis: calc(50% - 10px);
    }
    .nbo-flex-col-2 .nbo-group-body .nbd-option-field:last-of-type:nth-child(2n+1) {
        flex-basis: calc(100% - 10px);
    }
    .nbo-flex-col-3 .nbo-group-body .nbd-option-field {
        flex-basis: calc(33.3333% - 10px);
    }
    .nbo-flex-col-4 .nbo-group-body .nbd-option-field {
        flex-basis: calc(25% - 10px);
    }
    .nbo-group-toggle {
        position: absolute;
        right: 10px;
        bottom: -14px;
        box-sizing: border-box;
        width: 28px;
        height: 28px;
        border: 2px solid #f8f8f8;
        border-radius: 50%;
        background: #fff;
        cursor: pointer;
        text-align: center;
        line-height: 24px;
    }
    .nbo-group-toggle svg{
        -webkit-transition: all 0.4s;
        -moz-transition: all 0.4s;
        transition: all 0.4s;
        transform: rotate(-180deg);
    }
    .nbo-group-wrap .nbo-group-body.nbo-collapse .nbo-group-toggle svg{
        transform: rotate(0deg);
    }
    .nbo-group-wrap .nbo-group-body.nbo-collapse .nbd-option-field {
        display: none;
    }
    .nbo-group-toggle svg path{
        fill: #ddd;
    }
    .nbd-option-field .nbd-field-header .nbo-toggle {
        float: right;
    }
    .nbd-option-field .nbd-field-header .nbo-plus {
        display: none;
    }
    .nbd-option-field.nbo-collapse .nbd-field-header .nbo-minus {
        display: none;
    }
    .nbd-option-field.nbo-collapse .nbd-field-header .nbo-plus {
        display: unset;
    }
    .nbd-option-field .nbd-field-content {
        -webkit-transition: all 0.4s;
        -moz-transition: all 0.4s;
        transition: all 0.4s;
    }
    .nbd-option-field.nbo-collapse .nbd-field-content {
        height: 0;
        padding-top: 0;
        padding-bottom: 0;
        overflow: hidden;
        opacity: 0;
    }
    .nbd-product-tab .nbo-group-wrap .nbo-group-body .nbd-option-field {
         flex-basis: calc(100% - 10px) !important;
    }
    .nbd-field-ad-dropdown-wrap .nbd-field-content {
        position: relative;
    }
    .nbd-field-ad-dropdown-wrap .nbo-dropdown {
        display: none;
    }
    .nbo-ad-result {
        height: 36px;
        padding: 3px 8px;
        border: 1px solid #EEE;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
    }
    .nbo-ad-result-name{
        flex-basis: calc(100% - 30px);
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
        line-height: 30px;
    }
    .nbd-field-ad-dropdown-wrap .nbo-ad-result svg{
        -webkit-transition: all 0.4s;
        -moz-transition: all 0.4s;
        transition: all 0.4s;
    }
    .nbd-field-ad-dropdown-wrap.active .nbo-ad-result svg{
        transform: rotate(180deg);
    }
    .nbo-ad-pseudo-list {
        position: absolute;
        width: calc(100% - 20px);
        top: 46px;
        -webkit-box-shadow: 0 3px 10px 0 rgba(75,79,84,.3);
        -moz-box-shadow: 0 3px 10px 0 rgba(75,79,84,.3);
        -ms-box-shadow: 0 3px 10px 0 rgba(75,79,84,.3);
        box-shadow: 0 3px 10px 0 rgba(75,79,84,.3);
        background: #fff;
        z-index: 99;
        cursor: pointer;
        display: none;
        opacity: 0;
    }
    .nbd-field-ad-dropdown-wrap.active .nbo-ad-pseudo-list{
        display: block;
        opacity: 1;
    }
    .nbo-ad-pseudo-list .nbo-ad-list-item {
        display: flex;
        position: relative;
        padding: 5px 10px;
        justify-content: space-between;
        align-items: center;
    }
    .nbo-ad-pseudo-list .nbo-ad-list-item:not(:last-child) {
        border-bottom: 1px solid #ddd;
    }
    .nbo-ad-pseudo-list .nbo-ad-list-item:hover {
        background: #f8f8f8;
    }
    .nbo-ad-pseudo-list .nbo-ad-list-item.active {
        background: #0c8ea7;
    }
    .nbo-ad-pseudo-list .nbo-ad-list-item.active > .nbo-ad-item-main{
        color: #fff;
    }
    .nbo-ad-pseudo-list .nbo-ad-list-item .nbo-ad-item-thumb{ 
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #fff;
        margin-right: 10px;
        box-sizing: border-box;
        object-fit: cover;
        flex: 0 0 50px;
    }
    .nbo-ad-pseudo-list .nbo-ad-list-item .nbo-ad-item-main {
        display: flex;
        justify-content: center;
        flex-direction: column;
        width: calc(100% - 60px);
    }
    .nbo-ad-pseudo-list .nbo-ad-list-item .nbo-ad-item-main.nbo-shrink {
        width: calc(100% - 84px);
    }
    .nbo-ad-pseudo-list .nbo-ad-list-item .nbo-ad-item-title {
        font-weight: bold;
    }
    .nbo-ad-pseudo-list .nbo-ad-list-item .nbo-recomand{
        position: absolute;
        top: -3px;
        right: 17px;
    }
    .nbo-ad-pseudo-list .nbo-ad-list-item .nbo-recomand svg path {
        fill: #db133b;
    }
    .nbo-ad-pseudo-list .nbo-ad-list-item.active > .nbo-recomand svg path {
        fill: #fff;
    }
    .nbd-field-shape .nbo-ad-pseudo-list {
        padding: 5px;
    }
    .nbd-field-ad-dropdown-wrap.nbd-field-shape.active .nbo-ad-pseudo-list {
        display: flex !important;
        flex-wrap: wrap;
    }
    .nbd-field-shape .nbo-ad-pseudo-list .nbo-ad-list-item {
        width: 50px;
        height: 50px;
        padding: 0;
        margin: 5px;
        align-items: center;
        text-align: center;
        border-bottom: none;
    }
    .nbd-field-shape .nbo-ad-pseudo-list .nbo-ad-list-item:hover {
        background: #fff;
    }
    .nbd-field-shape .nbo-ad-pseudo-list .nbo-ad-list-item.active {
        background: #f8f8f8;
    }
    .nbd-field-shape .nbo-ad-result .nbo-ad-result-name{
        width: 30px;
        height: 30px;
        align-items: center;
        text-align: center;
        display: flex !important;
    }
    .nbd-field-shape .nbo-ad-result .nbo-ad-result-shape svg{
        height: 30px;
        width: 30px;
    }
    .nbd-field-shape .nbo-ad-result .nbo-ad-result-shape svg path{
        fill: #ddd;
        stroke-width: 1;
        stroke: #404762;
    }
    .nbd-field-shape .nbo-ad-result .nbo-ad-result-name {
        flex-basis: calc(100% - 60px);
    }
    .nbd-field-shape .nbo-ad-pseudo-list .nbo-ad-list-item svg{
        height: 50px;
        width: 50px;
        display: block;
    }
    .nbd-field-shape .nbo-ad-pseudo-list .nbo-ad-list-item svg path{
        fill: #ddd;
        stroke-width: 1;
        stroke: #404762;
    }
    .nbd-field-shape .nbo-ad-pseudo-list .nbo-ad-list-item:hover svg path{
        fill: #404762;
        stroke: #404762;
    }
    .nbd-field-shape .nbo-ad-pseudo-list .nbo-ad-list-item.active svg path{
        fill: #404762 !important;
        stroke: #404762;
    }
    .nbo-rotate-180 {
        transform: rotate(180deg);
    }
    .nbo-ad-pseudo-sublist {
        position: absolute;
        top: calc(100% + 40px);
        left: 5px;
        width: 100%;
        visibility: hidden;
        opacity: 0;
        -webkit-transition: all 0.4s;
        -moz-transition: all 0.4s;
        transition: all 0.4s;
        background: #fff;
        -webkit-box-shadow: 0 0 40px 0 rgba(0,0,0,0.2);
        -moz-box-shadow: 0 0 40px 0 rgba(0,0,0,0.2);
        -ms-box-shadow: 0 0 40px 0 rgba(0,0,0,0.2);
        box-shadow: 0 0 40px 0 rgba(0,0,0,0.2);
        z-index: 100;
    }
    .nbo-ad-pseudo-sublist-toggle {
        height: 24px;
        width: 24px;
        background: #fff;
        border-radius: 50%;
        flex: 0 0 24px;
    }
    .nbo-ad-pseudo-list .nbo-ad-list-item .nbo-ad-pseudo-sublist.active {
        top: calc(100% - 5px);
        visibility: visible;
        opacity: 1;
    }
    .nbo-ad-pseudo-list.nbo-ad-right .nbo-ad-pseudo-sublist {
        left: calc(100%);
        top: 0;
    }
    .nbo-ad-pseudo-list.nbo-ad-right .nbo-ad-list-item:hover .nbo-ad-pseudo-sublist {
        left: calc(100% - 34px);
        visibility: visible;
        opacity: 1;
    }
    .nbo-ad-pseudo-list.nbo-ad-right .nbo-ad-pseudo-sublist-toggle {
        transform: rotate(-90deg);
        background: transparent;
        pointer-events: none;
    }
    .nbo-ad-item-description {
        font-size: 0.75em;
    }
    .nbd-xlabel-wrapper {
        justify-content: flex-start;
        align-items: flex-start;
        -webkit-align-items: flex-start;
        -ms-flex-align: end;
        align-items: flex-start;
        display: flex; 
        display: -webkit-flex;
        display: -ms-flexbox;
        -webkit-flex-wrap: wrap;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
    }
    .nbd-xlabel-wrap {
        width: calc(25% - 10px);
        display: inline-block;
        margin: 0 10px 10px 0;
        box-sizing: border-box;
        vertical-align: top;
        text-align: center;
    }
    .nbd-product-tab .nbd-xlabel-wrap {
        width: calc(50% - 10px);
    }
    .nbd-xlabel-value {
        width: 100%;
        padding-top: 100%;
        position: relative;
    }
    .nbd-xlabel-value-inner {
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        position: absolute;
    }
    .nbd-xlabel {
        display: block;
        width: 100%;
        height: 100%;
        box-sizing: border-box;
        border: 2px solid #fff;
        border-radius: 10px;
        position: relative;
        text-align: left;
        box-shadow: 1px 0 10px rgba(0,0,0,.08);
        -webkit-transition: all 0.4s;
        -moz-transition: all 0.4s;
        transition: all 0.4s;
    }
    .nbd-xlabel:hover {
        -webkit-box-shadow: 0 3px 10px 0 rgba(75,79,84,.3);
        -moz-box-shadow: 0 3px 10px 0 rgba(75,79,84,.3);
        -ms-box-shadow: 0 3px 10px 0 rgba(75,79,84,.3);
        box-shadow: 0 3px 10px 0 rgba(75,79,84,.3);
    }
    .nbd-xlabel .nbo-recomand {
        position: absolute;
        top: -2px;
        right: -2px;
    }
    .nbd-xlabel .nbo-recomand svg path {
        fill: #0c8ea7;
    }
    .nbd-xlabel .nbo-recomand svg {
        width: 20px;
        height: 32px;
    }
    .nbd-xlabel .nbd-help-tip {
        width: 24px;
        height: 24px;
        font-size: 16px;
        background: #404762;
        top: 4px;
    }
    input[type=radio]+label.nbd-xlabel {
        margin: 0 !important;
    }
    .nbd-xlabel-wrap input:checked + label {
    }
     .nbd-xlabel-wrap input:checked + label:after{
        -webkit-transform: rotate(45deg);
        -moz-transform: rotate(45deg);
        transform: rotate(45deg);
        content: "";
        width: 20px;
        height: 40px;
        display: block;
        border: solid #404762;
        border-width: 0 4px 4px 0;
        position: absolute;
        top: calc(50% - 22px);
        left: calc(50% - 12px);
        transform-origin: center center;
        -webkit-filter: drop-shadow(5px 5px 15px #404762);
        filter: drop-shadow(5px 5px 15px #404762);
    }
    .nbd-xlabel-wrap > label {
        width: 100%;
        word-wrap: break-word;
    }
    .nbd-xlabel .nbd-help-tip:after {
        margin-top: 3px;
    }
    .nbo-gallery-loading {
        position: relative;
    }
    .nbo-gallery-loading:after{
        position: absolute;
        display: block;
        content: '';
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 2;
        pointer-events: none;
        text-align: center;
        background: url('<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/preloader-gif.svg'; ?>') 50% 50%;
        background-repeat: no-repeat;
    }
    .nbo-dec {
        color: #04b591;
    }
    .nbo-inc {
        color: #db133b;
    }
    .nbo-group-type2-wrap .group-type2-title {
        width: 100%;
        display: flex;
        justify-content: space-between;
        border-top: 1px solid #ddd;
        padding-top: 5px;
        padding-bottom: 5px;
        color: #04b591;
        font-weight: bold;
    }
    .nbo-group-type2-toggle {
        cursor: pointer;
        transform: rotate(180deg);
        -webkit-transition: all 0.4s;
        -moz-transition: all 0.4s;
        transition: all 0.4s;
        margin-right: 10px;
    }
    .nbo-group-type2-toggle svg {
        vertical-align: top;
    }
    .nbo-group-type2-toggle svg path{
        fill: #04b591;
    }
    .nbo-group-type2-wrap .nbo-group-type2-body .nbd-option-field {
        float: left;
        width: 100%;
        border: none;
    }
    .nbo-group-type2-wrap.nbo-collapse .nbo-group-type2-body {
        display: none;
    }
    .nbo-group-type2-wrap.nbo-collapse .nbo-group-type2-toggle {
        transform: rotate(0deg);
    }
    .nbo-group-type2-wrap .nbo-group-type2-body:after,
    .nbo-group-type2-wrap .nbo-group-type2-body .nbd-option-field:after {
        display: block;
        content: '';
        clear: both;
    }
    .nbo-group-type2-wrap.nbo-float-col-2 .nbo-group-type2-body .nbd-option-field {
        width: 50%;
    }
    .nbo-group-type2-wrap.nbo-float-col-3 .nbo-group-type2-body .nbd-option-field {
        width: 33.3333%;
    }
    .nbo-group-type2-wrap.nbo-float-col-4 .nbo-group-type2-body .nbd-option-field {
        width: 25%;
    }
    .nbo-group-type2-wrap .nbo-group-type2-body .nbd-option-field .nbd-field-header,
    .nbo-group-type2-wrap .nbo-group-type2-body .nbd-option-field .nbd-field-content {
        float: left;
        background: transparent;
    }
    .nbo-group-type2-wrap .nbo-group-type2-body .nbd-option-field .nbd-field-header {
        width: 33.3333%;
        margin-top: 5px;
    }
    .nbo-group-type2-wrap .nbo-group-type2-body .nbd-option-field .nbd-field-content {
        width: 66.6666%;
    }
    .nbo-table-summary-wrap.nbo-float-summary{
        position: fixed;
        bottom: 0;
        left: 0px;
        z-index: 999999998;
        background: #fff;
        width: 300px;
        font-size: 0.8em;
        box-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
        max-height: calc(100vh - 50px);
        overflow: hidden;
        overflow-y: auto;
    }
    .nbo-table-summary-wrap.nbo-float-summary .nbo-summary-title {
        margin: 0;
        padding: 10px 15px;
        background: #0c8ea7;
        color: #fff;
        border-top-right-radius: 4px;
    }
    .nbo-table-summary-wrap.nbo-float-summary .nbo-summary-table {
        margin-bottom: 0;
    }
    .nbo-float-summary .nbo-summary-title .nbo-toggle {
        display: none;
    }
    .nbo-delivery-wrapper table td, .nbo-delivery-wrapper table th {
        padding: 0;
        text-align: center;
        background: transparent;
        vertical-align: middle;
        border-top: 1px solid #ddd;
        border-left: 1px solid #ddd;
    }
    .nbo-delivery-wrapper table td:last-child, .nbo-delivery-wrapper table th:last-child {
        border-right: 1px solid #ddd;
    }
    .nbo-delivery-wrapper table tr:last-child td {
        border-bottom: 1px solid #ddd;
    }
    .nbo-delivery-wrapper table .nbo-delivery-date-wrap-title {
        background: #ddd;
    }
    .nbo-delivery-wrapper table td {
        height: 50px;
    }
    .nbo-delivery-wrapper table .nbo-delivery-icon-wrap {
        width: 120px;
        border-top: 1px solid transparent;
        border-left: none;
    }
    .nbo-delivery-icon {
        display: flex;
        height: 90px;
        flex-direction: column;
        background: #fff;
        justify-content: center;
        align-items: center;
    }
    .nbo-delivery-icon svg {
        width: 40px; 
        height: 40px; 
    }
    .nbo-delivery-icon svg path{
        fill: #0c8ea7;
    }
    .nbo-delivery-qty {
        background: #ddd;
        height: 30px;
        line-height: 30px;
    }
    .nbo-delivery-date-wrap {
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .nbo-delivery-date-title {
        font-size: 0.8em;
        margin-bottom: 1em;
    }
    .nbo-delivery-date {
        font-size: 0.8em;
        font-weight: normal;
    }
    .nbo-delivery-date2 {
        color: #db133b;
        line-height: 1.5em;
    }
    .nbo-delivery-date2 .day {
        font-size: 1.5em;
    }
    .nbo-delivery-date-selector {
        cursor: pointer;
        -webkit-transition: all 0.2s;
        -moz-transition: all 0.2s;
        transition: all 0.2s;
    }
    .nbo-delivery-date-selector.active,
    .nbo-delivery-date-selector:hover {
        background: #404762 !important;
        color: #fff !important;
    }
    .nbo-delivery-date-selector > span {
        display: block;
    }
    .nbo-delivery-date-selector span.nbo-delivery-price-item{
        display: block;
        font-size: 0.7em;
        color: #888;
    }
    .nbo-delivery-date-selector.active span.nbo-delivery-price-item,
    .nbo-delivery-date-selector:hover span.nbo-delivery-price-item{
        color: #fff;
    }
    .nbo-float-summary-toggle {
        height: 16px;
        width: 16px;
        cursor: pointer;
        -webkit-transition: all 0.4s;
        -moz-transition: all 0.4s;
        transition: all 0.4s;
    }
    .nbo-float-summary-toggle svg{ 
        height: 100%;
    }
    .nbo-float-summary-toggle svg path{ 
        fill: #fff;
    }
    .nbo-float-summary .nbo-summary-title {
        display: flex;
        justify-content: space-between;
    }
    .nbo-float-summary.nbo-collapse .nbo-float-summary-toggle {
        transform: rotate(180deg);
    }
    .nbo-float-summary.nbo-collapse .nbo-summary-table {
        display: none;
    }
    .nbdd_disable {
        pointer-events: none;
    }
    .nbo-delivery-custom-quantity input{
        height: 34px;
        line-height: 34px;
        padding: 0 0 0 10px;
        vertical-align: top;
    }
    .nbo-delivery-custom-quantity .update-custom-quantity{
        height: 34px;
        line-height: 34px;
    }
    .nbo-delivery-custom-quantity .update-custom-quantity path{
        fill: #fff;
    }
    input.ng-invalid, select.ng-invalid{
        border: 1px solid red !important;
    }
    .nbo-detail-popup-wrap {
        position: fixed;
        top: 0;
        left: 0;
        height: 9999px; 
        width: 100vw;
        z-index: -1;
        opacity: 0;
        -webkit-transition: all 0.4s;
        transition: all 0.4s;
    }
    .nbo-detail-popup-wrap.nb-show {
        z-index: 999999;
        opacity: 1;
    }
    .nbo-detail-popup-wrap .popup-inner {
        position: absolute;
        width: 100%;
        height: 100vh;
        overflow: auto;
        padding: 60px 0;
        box-sizing: border-box;
        background: rgba(0,0,0,0.7);
        text-align: center;
    }
    .nbo-detail-popup-wrap .main-popup {
        width: 800px;
        padding: 0 20px 20px;
        background-color: #fff;
        border-radius: 2px;
        box-sizing: border-box;
        -webkit-transition: all .4s;
        -moz-transition: all .4s;
        transition: all .4s;
        display: inline-block;
        text-align: left;
    }
    .nbo-detail-popup-wrap .main-popup .nbo-detail-popup-header {
        height: 60px;
        line-height: 60px;
        font-weight: bold;
    }
    .nbo-clearfix:after {
        display: block;
        content: '';
        clear: both;
    }
    .nbo_map_disable {
        pointer-events: none;
        opacity: 0.7;
    }
    .nbo-hidden-checked:before {
        border-color: rgba(0, 0, 0, 0.54) !important;
    }
    .nbo-hidden-checked:after {
        transform: scale(0) !important;
    }
    .nbo-invalid-option {
        color: red;
        font-size: 0.8em;
        font-style: italic;
        opacity: 0;
    }
    .nbo-invalid-option.active {
        opacity: 1;
    }
    .nbo-disabled-wrap {
        opacity: 0.7;
        pointer-events: none;
    }
    .nbo_group_panel_wrap {
        overflow: hidden;
    }
    .nbo_group_panel_wrap_inner {
        -webkit-transition: all 0.4s;
        -moz-transition: all 0.4s;
        transition: all 0.4s;
    }
    .nbo_group_panel_wrap_inner .nbo-group-wrap,
    .nbo_group_panel_wrap_inner .nbo-group-type2-wrap{
        float: left;
        position: relative;
    }
    .nbo_group_panel_prev, .nbo_group_panel_next {
        display: inline-block;
        border: 1px solid #eeeeee;
        box-sizing: border-box;
        height: 30px;
        margin-right: 5px;
        padding: 0 5px;
        cursor: pointer;
        background: #eeeeee;
    }
    .nbo_group_panel_prev svg,
    .nbo_group_panel_prev span,
    .nbo_group_panel_next svg,
    .nbo_group_panel_next span{
        display: inline-block;
        vertical-align: middle;
        font-size: 14px;
        line-height: 27px;
    }
    .nbo_group_panel_prev svg path, .nbo_group_panel_next  svg path{
        fill: #6d6d6d;
    }
    .nbo_group_panel_prev svg {
        transform: rotate(90deg);
    }
    .nbo_group_panel_next svg {
        transform: rotate(-90deg);
    }
    .nbo-group-timeline-container {
        position: relative;
        height: 100px;
    }
    .nbd-product-tab .nbo-group-timeline-container{
        display: none;
    }
    .nbo-group-timeline-wrap {
        position: relative;
        height: 100%;
        overflow: hidden;
    }
    .nbo-group-timeline-container.paged .nbo-group-timeline-wrap {
        margin: 0 40px;
    }
    .nbo-group-timeline-paged {
        position: absolute;
        z-index: 1;
        top: 50%;
        -webkit-transform: translateY(-50%);
        -ms-transform: translateY(-50%);
        transform: translateY(-50%);
        height: 34px;
        width: 34px;
        border-radius: 50%;
        border-width: 2px;
        border-style: solid;
        border-color: #dfdfdf;
        display: none;
        cursor: pointer;
        text-align: center;
        padding-top: 3px;
        -webkit-transition: all 0.4s;
        -moz-transition: all 0.4s;
        transition: all 0.4s;
    }
    .nbo-group-timeline-paged svg path{
        fill: #dfdfdf;
    }
    .nbo-group-timeline-paged:hover {
        border-color: #404762;
    }
    .nbo-group-timeline-paged:hover svg path{
        fill: #404762;
    }
    .nbo-group-timeline-paged-prev {
        left: 0;
        -webkit-transform: translateY(-50%) rotate(90deg);
        -ms-transform: translateY(-50%) rotate(90deg);
        transform: translateY(-50%) rotate(90deg);
    }
    .nbo-group-timeline-paged-next {
        right: 0;
        -webkit-transform: translateY(-50%) rotate(-90deg);
        -ms-transform: translateY(-50%) rotate(-90deg);
        transform: translateY(-50%) rotate(-90deg);
    }
    .nbo-group-timeline-container.paged .nbo-group-timeline-paged{
        display: block;
    }
    .nbo-group-timeline-wrap:before,
    .nbo-group-timeline-wrap:after {
        content: '';
        position: absolute;
        z-index: 2;
        top: 41px;
        height: 16px;
        width: 20px;
    }
    .nbo-group-timeline-wrap:before {
        left: 0;
        background: linear-gradient(to right,#ffffff,rgba(247, 247, 247, 0));
    }
    .nbo-group-timeline-wrap:after {
        right: 0;
        background: linear-gradient(to left,#ffffff,rgba(247, 247, 247, 0));
    }
    .nbo-group-timeline-line {
        position: absolute;
        z-index: 1;
        left: 0;
        top: 49px;
        height: 2px;
        background-color: #dfdfdf;
        -webkit-transition: all 0.4s;
        -moz-transition: all 0.4s;
        transition: all 0.4s;
    }
    .nbo-group-timeline-step {
        position: absolute;
        width: 0;
        height: 0;
        z-index: 3;
    }
    .nbo-group-timeline-step-inner {
        position: relative;
        width: 0;
        height: 0;
    }
    .nbo-group-timeline-step-inner:after {
        content: '';
        position: absolute;
        right: -9px;
        bottom: -8px;
        height: 12px;
        width: 12px;
        border-radius: 50%;
        border-width: 2px;
        border-style: solid;
        border-color: #dfdfdf;
        background-color: #fff;
        cursor: pointer;
        z-index: 1;
    }
    .nbo-group-timeline-step.active .nbo-group-timeline-step-inner:after {
        background-color: #404762;
        border-color: #404762;
    }
    .nbo-group-timeline-step.over .nbo-group-timeline-step-inner:after {
        border-color: #404762;
    }
    .nbo-group-timeline-fill-line {
        position: absolute;
        z-index: 1;
        left: 0;
        top: 0;
        height: 100%;
        width: 100%;
        background-color: #404762;
        transform-origin: left center;
        -webkit-transition: all 0.3s;
        -moz-transition: all 0.3s;
        transition: all 0.3s;
    }
    .drt-up .nbo-ad-pseudo-list {
        bottom: 46px;
        top: unset !important;
    }
    @media (max-width:768px){
        .nbo-detail-popup-wrap .main-popup {
            width: calc(100% - 30px);;
        }
        .nbo-group-type2-wrap .nbo-group-type2-body .nbd-option-field {
            width: 100% !important;
        }
        .nbo-group-wrap .nbo-group-body .nbd-option-field {
            flex-basis: calc(100% - 10px);
        }
        .nbd-swatch-tooltip {
            display: none;
        }
        .nbd-tb-options td {
            display: inline-block !important;
            width: 100%;
            padding: 10px !important;
        }
        .nbo-summary-wrapper table tfoot td:last-child,
        .nbo-summary-wrapper table th {
            white-space: nowrap;
        }
        .nbo-table-pricing-wrap {
            overflow-x: scroll;
        }
        .nbo-table-summary-wrap, .nbo-table-pricing-wrap {
            background: #f8f8f8;
        }
        .nbo-delivery-wrapper table td, .nbo-delivery-wrapper table th{
            white-space: nowrap;
        }
        .nbo-delivery-wrapper table td, .nbo-delivery-wrapper table th:not(.nbo-delivery-icon-wrap){
            padding: 0 10px !important;
        }
        .nbo-delivery-icon {
            width: 100px !important;
        }
        .nbo-delivery-wrapper {
            overflow-x: scroll;
        }
    }
</style>
<div class="nbd-option-wrapper" <?php //if(!$in_quick_view) echo 'ng-app="nboApp"'; ?> id="<?php echo $appid; ?>">
    <div ng-controller="optionCtrl" ng-form="nboForm" id="nbo-ctrl-<?php echo $appid; ?>" ng-cloak>
        <div class="nbo-fields-wrapper">
            <?php do_action( 'nbo_before_fields' ); ?>
    <?php if( $display_type == 2 ): ?>
        <table class="nbd-tb-options">
            <tbody>
<?php endif; 
$html_field         = '';
$has_nbpb           = false;
$has_delivery       = false;
$artwork_action     = '';
$num_visible_field  = 0;
$matrix_type        = 1;
if( $cart_item_key != '' && $options['display_type'] == 3 ) $options['display_type'] = 1;
if( $options['display_type'] == 2 ){
    if( isset( $options['manual_build_pm'] ) && $options['manual_build_pm'] == 'on' ){
        if( isset( $options['manual_pm'] ) && $options['manual_pm'] != '' ){
            $pm_parts = explode( '|', $options['manual_pm'] );
            $options['pm_hoz']      = explode( ',', $pm_parts[0] );
            $options['pm_ver']      = explode( ',', $pm_parts[1] );
            $options['mpm_prices']  = explode( ',', $pm_parts[2] );
            if( count( $options['pm_hoz'] ) > 0 && count( $options['pm_ver'] ) > 0 ){
                $pm_field_indexes = array_merge( $options['pm_hoz'], $options['pm_ver'] );
                $matrix_type    = 2;
            }
        }
    } else {
        $pm_field_indexes = array_merge( $options['pm_hoz'], $options['pm_ver'] );
    }
}
$options['matrix_type'] = $matrix_type;
foreach( $options["fields"] as $key => $field ){
    if( $options['display_type'] == 2 ){
        $class = ( ( $matrix_type == 1 && !in_array( $key, $pm_field_indexes ) ) || ( $matrix_type == 2 && !in_array( $field['id'], $pm_field_indexes ) ) ) ? '' : 'nbo-hidden';
    }else if( $options['display_type'] == 3 ){
        $class = !in_array($key, $options['bulk_fields']) ? '' : 'nbo-hidden';
    }else{
        $class = '';
    }
    if( !$in_quick_view && $nbdpb_enable == '1' && isset($field['nbpb_type']) && ( $field['nbpb_type'] == 'nbpb_com' || $field['nbpb_type'] == 'nbpb_text' || $field['nbpb_type'] == 'nbpb_image' ) ){
        $class      = 'nbo-hidden';
        $has_nbpb   = true;
    }
    if( $options['display_type'] == 5 ){
        $class .= ' nbo-collapse';
    }
    if( isset($field['nbe_type']) && $field['nbe_type'] == 'delivery' && $field['general']['enabled'] == 'y' ){
        if( $options['display_type'] != 3 && isset( $field['general']['attributes'] ) && isset( $field['general']['attributes']["options"] ) && count( $field['general']['attributes']["options"] ) > 0 ){
            $has_delivery       = true;
            $delivery_fields[]  = $field;
            $class             .= ' nbo-hidden';
        }
    }
    if( isset($field['nbe_type']) && ( $field['nbe_type'] == 'frame' || $field['nbe_type'] == 'number_file' ) ){
        $class .= ' nbo-hidden';
    }
    if( isset( $field['general']['published'] ) && $field['general']['published'] == 'n' ){
        $class .= ' nbo-hidden';
    }
    if( isset($field['nbe_type']) && $field['nbe_type'] == 'actions' && $field['general']['enabled'] == 'y' && isset( $field['general']['attributes'] ) && isset( $field['general']['attributes']["options"] ) && count( $field['general']['attributes']["options"] ) > 0 ){
        $artwork_action = apply_filters( 'nbo_artwork_action', '', $field );
    }
    if( isset( $field['appearance']['css_class'] ) ){
        $class .= ' ' . $field['appearance']['css_class'];
    }
    if( $in_design_editor && isset($field['nbe_type']) && $field['nbe_type'] == 'actions' ){
        if( isset( $field['general']['attributes'] ) && isset( $field['general']['attributes']["options"] ) && count( $field['general']['attributes']["options"] ) > 0 ){
            $has_custom_design_action = false;
            foreach ($field['general']['attributes']["options"] as $attr_index => $attr){
                if( isset( $attr['action'] ) && $attr['action'] == 'c' ){
                    $has_custom_design_action = true;
                }
            }
            if( $has_custom_design_action ){
                foreach ($field['general']['attributes']["options"] as $attr_index => $attr){
                    if( isset( $attr['action'] ) && $attr['action'] == 'c' ){
                        $options["fields"][$key]['general']['attributes']["options"][$attr_index]['selected'] = 'on';
                    }else{
                        $options["fields"][$key]['general']['attributes']["options"][$attr_index]['selected'] = 'off';
                    }
                }
                $class .= ' nbo-hidden';
            }
        }
    }
    $class      = apply_filters( 'nbo_field_class', $class, $field );
    $need_show  = true;
    $_prefix    = ( !( isset( $field['show_in_group'] ) || isset( $field['show_in_popup'] ) ) ) ? $prefix : '';
    if( $field['general']['data_type'] == 'i' ){
        if( $field['general']['input_type'] == 'a' ) {
            $tempalte = $currentDir .'/options-builder/textarea'.$_prefix.'.php'; 
        } else {
            $tempalte = $currentDir .'/options-builder/input'.$_prefix.'.php'; 
        }
    }else{
        if( count($field['general']['attributes']["options"]) == 0){
            $need_show = false;
        }
        if( isset($field['nbd_type']) && ( ( $field['nbd_type'] == 'page' && $field['general']['data_type'] == 'm' ) || ( $field['nbd_type'] == 'page2' ) ) ){
            $tempalte = $currentDir .'/options-builder/page'.$_prefix.'.php';
        } else if( isset( $field['nbd_type'] ) && $field['nbd_type'] == 'shape' ){
            $tempalte = $currentDir .'/options-builder/shape'.$_prefix.'.php';
        }else{
            switch( $field['appearance']['display_type'] ){
                case 's':
                    $tempalte = $currentDir .'/options-builder/swatch'.$_prefix.'.php';
                    break;
                case 'l':
                    $tempalte = $currentDir .'/options-builder/label'.$_prefix.'.php';
                    break;
                case 'r':
                    $tempalte = $currentDir .'/options-builder/radio'.$_prefix.'.php';
                    break;
                case 'ad':
                    $tempalte = $currentDir .'/options-builder/advanced-dropdown'.$_prefix.'.php';
                    break;
                case 'xl':
                    $tempalte = $currentDir .'/options-builder/xlabel'.$_prefix.'.php';
                    break;
                default:
                    $tempalte = $currentDir .'/options-builder/dropdown'.$_prefix.'.php';
                    break;
            }
        }
    }
    $options["fields"][$key]['template']    = $tempalte;
    $options["fields"][$key]['need_show']   = $need_show;
    $options["fields"][$key]['class']       = $class;
    if( !( isset( $field['show_in_group'] ) || isset( $field['show_in_popup'] ) ) ){
        if( $field['general']['enabled'] == 'y' && $need_show ) include( $tempalte );
    }
    if( $field['general']['enabled'] == 'y' && $need_show && false === strpos( $class, 'nbo-hidden' ) ){
        $num_visible_field += 1;
    }
}
$disable_quantity_input = false;
$show_quantity_option   = false;
if( $options['quantity_enable'] == 'y' && !$is_sold_individually && !($options['display_type'] == 3 && count($options['bulk_fields'])) ){
    $disable_quantity_input = $options['quantity_type'] != 'r' ? true : false;
    $show_quantity_option = true;
    if( !$has_delivery ) include($currentDir .'/options-builder/quantity'.$prefix.'.php');
}
$bulk_fields = array();
if( $options['display_type'] == 3 && count($options['bulk_fields']) ){
    foreach($options["bulk_fields"] as $key => $bulk_index){
       $bulk_fields[] = $options["fields"][$bulk_index]; 
    }
}

$popup_fields   = array();
if( $options['display_type'] == 6 && isset( $options["popup_fields"] ) && count( $options["popup_fields"] ) ){
    foreach($options["popup_fields"] as $popup_field){
        $popup_field_index  = get_field_index_by_id( $popup_field, $options["fields"] );
        $popup_fields[]     = $options["fields"][$popup_field_index];
    }
}

if( $display_type == 2 ): ?>
            </tbody>
        </table>
<?php endif;
    do_action( 'nbo_after_fields' );
if( $group_mode ){
    include( $currentDir . '/options-builder/groups' . $prefix . '.php' );
}
if( $popup_mode ){
    include( $currentDir . '/options-builder/popup.php' );
}
if( $has_nbpb && !$in_quick_view ) do_action( 'nbo_after_default_options' );
if( $options['display_type'] == 2 && count( $pm_field_indexes ) ){
    if( $matrix_type == 1 ){
        include( $currentDir . '/options-builder/price-matrix.php' );
    }else{
        include( $currentDir . '/options-builder/price-matrix-2.php' );
    }
}else if( $options['display_type'] == 3 && count( $options['bulk_fields'] ) ){
    include( $currentDir . '/options-builder/bulk-options.php' );
}
if( $has_delivery ){
    foreach( $delivery_fields as $delivery_field ){
        include($currentDir .'/options-builder/delivery.php');
    }
}
if( $cart_item_key != ''){ ?>
            <input type="hidden" value="<?php echo $cart_item_key; ?>" name="nbo_cart_item_key"/>
            <?php 
                if( $nbau != '' ){ 
                    $upload_datas   = (array)json_decode( $nbau );
                    $upload_files   = array();
                    if( count( $upload_datas ) > 0 ){
                        $upload_path    = NBDESIGNER_UPLOAD_DIR . '/' . $nbu_item_key;
                        foreach( $upload_datas as $key => $data ){
                            $data                       = (array)$data;
                            $file                       = $upload_path . '/' . $data['name'];
                            $preview_file               = $upload_path . '_preview/' . $data['name'];
                            $final_file                 = $upload_path . '_final/' . $data['name'];
                            $preview_final_file         = $upload_path . '_preview_final/' . $data['name'];
                            list( $width, $height )     = getimagesize( $file );
                            list( $p_width, $p_height ) = getimagesize( $preview_file );
                            $cropped_preview_url        = Nbdesigner_IO::wp_convert_path_to_url( $preview_final_file );
                            $upload_files[] = array(
                                'name'                  => $data['name'],
                                'origin'                => Nbdesigner_IO::wp_convert_path_to_url( $file ),
                                'preview'               => $cropped_preview_url,
                                'src'                   => Nbdesigner_IO::wp_convert_path_to_url( $preview_file ),
                                'width'                 => $width,
                                'height'                => $height,
                                'cropWidth'             => $data['width'],
                                'cropHeight'            => $data['height'],
                                'cropLeft'              => $data['startX'],
                                'cropTop'               => $data['startY'],
                                'cropped_url'           => Nbdesigner_IO::wp_convert_path_to_url( $final_file ),
                                'cropped_preview_url'   => $cropped_preview_url,
                                'zoom'                  => $data['zoom'],
                                'previewOriginWidth'    => $p_width,
                                'previewOriginHeight'   => $p_height,
                                'previewRatio'          => $width / $p_width,
                                'productWidth'          => $data['productWidth'],
                                'productHeight'         => $data['productHeight']
                            );
                        }
                    }
            ?>
                <input type="hidden" value='<?php echo $nbau; ?>' name="nbu_advanced_upload_data" class="nbu-advanced-upload-data" />
                <script type="text/javascript">
                    cartItemNbau = JSON.parse('<?php echo json_encode( $upload_files ); ?>')
                </script>
            <?php }
} ?>
            <div ng-if="fields.length" class="nbo-clear-option-wrap">
                <?php if( $in_design_editor && $nbd_qv_type == '2') : ?>
                <a ng-class="printingOptionsAvailable ? '' : 'nbd-disabled'" class="nbd-button nbo-apply" ng-click="applyOptions()">{{settings.task2 == '' ? "<?php _e('Apply options','web-to-print-online-designer'); ?>" : "<?php _e('Start design','web-to-print-online-designer'); ?>" }}</a>
                <?php endif; ?>
                <?php if( $num_visible_field > 0 ): ?>
                <a class="button nbd-button" ng-click="reset_options()"><?php _e('Clear selection', 'web-to-print-online-designer'); ?></a>
                <?php endif; ?>
            </div>
            <input type="hidden" value="<?php echo $product_id; ?>" name="nbo-add-to-cart"/>
            <p ng-if="!valid_form" class="nbd-invalid-form"><?php _e('Please check invalid fields and quantity input or choose a different combination!', 'web-to-print-online-designer'); ?></p>
            <?php if( $popup_mode ): ?>
            <p>
                <a class="button alt nbd-trigger-nbo-popup nbdesign-button" id="nbd-trigger-nbo-popup">
                    <?php _e('Get started', 'web-to-print-online-designer'); ?>
                </a>
            </p>
            <?php endif; ?>
        </div>
        <div class="nbo-summary-wrapper">
            <?php if( nbdesigner_get_option('nbdesigner_hide_summary_options') != 'yes' && $options['display_type'] != 3 && $num_visible_field > 0): ?>
            <?php $float_summary = nbdesigner_get_option('nbdesigner_float_summary_options', 'no'); ?>
            <div ng-if="valid_form" class="nbo-table-summary-wrap <?php echo $style_class; ?> <?php if( !wp_is_mobile() && !(isset($_REQUEST['wc-api']) && $_REQUEST['wc-api'] == 'NBO_Quick_View') && !$in_quick_view && $float_summary == 'yes' ) echo 'nbo-float-summary'; ?>" <?php if( isset($_REQUEST['wc-api']) && $_REQUEST['wc-api'] == 'NBO_Quick_View' && $nbd_qv_type == '2') echo 'nbd-perfect-scroll'; ?>>
                <p class="nbo-summary-title" ng-init="showNboSummary = true">
                    <b><?php _e('Summary options', 'web-to-print-online-designer'); ?></b>
                    <?php if( $display_type == 1 ): ?>
                    <span class="nbo-minus nbo-toggle" ng-show="showNboSummary" ng-click="showNboSummary = !showNboSummary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19 13H5v-2h14v2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
                    </span>
                    <span class="nbo-plus nbo-toggle" ng-show="!showNboSummary" ng-click="showNboSummary = !showNboSummary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
                    </span>
                    <?php endif; ?>
                    <?php if( $float_summary == 'yes' ): ?>
                    <span class="nbo-float-summary-toggle" ng-click="toggle_float_summary()">
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="24" height="24" viewBox="0 0 24 24">
                            <path d="M16.594 8.578l1.406 1.406-6 6-6-6 1.406-1.406 4.594 4.594z"/>
                        </svg>
                    </span>
                    <?php endif; ?>
                </p>
                <table class="nbo-summary-table" ng-show="showNboSummary">
                    <tbody>
                        <tr ng-repeat="(key, field) in nbd_fields" ng-show="field.enable && field.published">
                            <td>{{field.title}} : <b>{{field.value_name}}</b>
                                <br ng-if="field.ind_qty" /><small ng-if="field.ind_qty && field.price != ''"> <?php _e('( cart fee )', 'web-to-print-online-designer'); ?></small>
                                <br ng-if="field.fixed_amount" /><small ng-if="field.fixed_amount && field.price != ''"> <?php _e('( for all items )', 'web-to-print-online-designer'); ?></small>
                            </td>
                            <td ng-bind-html="field.price | to_trusted"></td>
                        </tr>
                    </tbody>
                    <tfoot style="border-top: 1px solid #404762;">
                        <tr>
                            <td><b><?php _e('Options price', 'web-to-print-online-designer'); ?></b></td>
                            <td><span id="nbd-option-total"><span ng-bind-html="total_price | to_trusted"></span> / <?php _e('1 item', 'web-to-print-online-designer'); ?></span></td>
                        </tr>
                        <tr>
                            <td><b><?php _e('Quantity Discount', 'web-to-print-online-designer'); ?></b></td>
                            <td><span id="nbd-option-total"><span ng-bind-html="discount_by_qty | to_trusted"></span> / <?php _e('1 item', 'web-to-print-online-designer'); ?></span></td>
                        </tr>
                        <tr class="nbo-final-price">
                            <td><b><?php _e('Final price', 'web-to-print-online-designer'); ?></b></td>
                            <td>
                                <span id="nbd-option-total">
                                    <span ng-hide="_qty == 1" ng-bind-html="final_price | to_trusted"></span><span ng-show="_qty == 1" ng-bind-html="total_cart_price | to_trusted"></span> / <?php _e('1 item', 'web-to-print-online-designer'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr class="nbo-final-price" ng-if="cart_item_fee.enable">
                            <td><b><?php _e('Cart item fee', 'web-to-print-online-designer'); ?></b></td>
                            <td><span id="nbd-option-total"><span ng-bind-html="cart_item_fee.value | to_trusted"></span> / <?php _e('all items', 'web-to-print-online-designer'); ?></span></td>
                        </tr>
                        <?php if($options['display_type'] != 3 || count($options['bulk_fields']) == 0): ?>
                        <tr class="nbo-final-price nbo-total-price" ng-if="_qty > 1">
                            <td><b><?php _e('Subtotal price', 'web-to-print-online-designer'); ?></b></td>
                            <td><span id="nbd-option-total"><span ng-bind-html="total_cart_price | to_trusted"></span> / {{_qty}} <?php _e('items', 'web-to-print-online-designer'); ?></span></td>
                        </tr>
                        <?php endif; ?>
                    </tfoot>
                </table>
            </div>
            <?php endif; ?>
            <?php if( nbdesigner_get_option('nbdesigner_hide_table_pricing', 'no') == 'no' && $options['display_type'] != 3 && !$has_delivery && $num_visible_field > 0 ): ?>
            <div ng-if="valid_form && price_table.length > 1" class="nbo-table-pricing-wrap <?php echo $style_class; ?>" <?php if( isset($_REQUEST['wc-api']) && $_REQUEST['wc-api'] == 'NBO_Quick_View' && $nbd_qv_type == '2') echo 'nbd-perfect-scroll'; ?>>
                <p class="nbo-table-pricing-title" ng-init="showNboTablePricing = true">
                    <b><?php _e('Table pricing', 'web-to-print-online-designer'); ?></b>
                    <?php if( $display_type == 1 ): ?>
                    <span class="nbo-minus nbo-toggle" ng-show="showNboTablePricing" ng-click="showNboTablePricing = !showNboTablePricing">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19 13H5v-2h14v2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
                    </span>
                    <span class="nbo-plus nbo-toggle" ng-show="!showNboTablePricing" ng-click="showNboTablePricing = !showNboTablePricing">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
                    </span>
                    <?php endif; ?>
                </p>
                <table class="nbo-table-pricing" ng-show="showNboTablePricing">
                    <?php if( nbdesigner_get_option( 'nbdesigner_table_pricing_type', '1' ) == '1' ): ?>
                    <thead>
                        <tr>
                            <th><?php _e('From', 'web-to-print-online-designer'); ?></th>
                            <th><?php _e('Up to', 'web-to-print-online-designer'); ?></th>
                            <th><?php _e('Price / 1 item', 'web-to-print-online-designer'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="pt in price_table" ng-class="pt.in_range ? 'nbo-bold' : ''">
                            <td>{{pt.from}}</td>
                            <td>{{pt.up != '**' ? pt.up : '<?php _e('or more', 'web-to-print-online-designer'); ?>'}}</td>
                            <td ng-bind-html="pt.final_price | to_trusted"></td>
                        </tr>
                    </tbody>
                    <?php else: ?>
                    <thead>
                        <tr>
                            <th><?php _e('Amount', 'web-to-print-online-designer'); ?></th>
                            <th><?php _e('Unit price', 'web-to-print-online-designer'); ?></th>
                            <th><?php _e('Total price', 'web-to-print-online-designer'); ?></th>
                            <th><?php _e('Saving', 'web-to-print-online-designer'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="pt in price_table">
                            <td>{{pt.qty}}</td>
                            <td ng-bind-html="pt.final_price | to_trusted"></td>
                            <td ng-if="!pt.cart_item_fee.enable" ng-bind-html="pt.total_cart_price | to_trusted"></td>
                            <td ng-if="pt.cart_item_fee.enable"><span ng-bind-html="pt._total_cart_price | to_trusted"></span>(+<span ng-bind-html="pt.cart_item_fee.value | to_trusted"></span>)</td>
                            <td ng-class="pt.klass">{{pt.saving}}</td>
                        </tr>
                    </tbody>
                    <tfoot ng-if="price_table_cart_fee">
                        <tr><td colspan="4"><small><?php _e('(+x) x: Cart item fee', 'web-to-print-online-designer'); ?></small></td></tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function($){
        $.fn.tipTip = function(options) {
                var defaults = {
                        activation: "hover",
                        keepAlive: false,
                        maxWidth: "200px",
                        edgeOffset: 3,
                        defaultPosition: "bottom",
                        delay: 400,
                        fadeIn: 200,
                        fadeOut: 200,
                        attribute: "title",
                        content: false, // HTML or String to fill TipTIp with
                        enter: function(){},
                        exit: function(){}
                };
                var opts = $.extend(defaults, options);

                // Setup tip tip elements and render them to the DOM
                if($("#tiptip_holder").length <= 0){
                        var tiptip_holder = $('<div id="tiptip_holder" style="max-width:'+ opts.maxWidth +';"></div>');
                        var tiptip_content = $('<div id="tiptip_content"></div>');
                        var tiptip_arrow = $('<div id="tiptip_arrow"></div>');
                        $("body").append(tiptip_holder.html(tiptip_content).prepend(tiptip_arrow.html('<div id="tiptip_arrow_inner"></div>')));
                } else {
                        var tiptip_holder = $("#tiptip_holder");
                        var tiptip_content = $("#tiptip_content");
                        var tiptip_arrow = $("#tiptip_arrow");
                }

                return this.each(function(){
                        var org_elem = $(this);
                        if(opts.content){
                                var org_title = opts.content;
                        } else {
                                var org_title = org_elem.attr(opts.attribute);
                        }
                        if(org_title != ""){
                                if(!opts.content){
                                        org_elem.removeAttr(opts.attribute); //remove original Attribute
                                }
                                var timeout = false;

                                if(opts.activation == "hover"){
                                        org_elem.hover(function(){
                                                active_tiptip();
                                        }, function(){
                                                if(!opts.keepAlive){
                                                        deactive_tiptip();
                                                }
                                        });
                                        if(opts.keepAlive){
                                                tiptip_holder.hover(function(){}, function(){
                                                        deactive_tiptip();
                                                });
                                        }
                                } else if(opts.activation == "focus"){
                                        org_elem.focus(function(){
                                                active_tiptip();
                                        }).blur(function(){
                                                deactive_tiptip();
                                        });
                                } else if(opts.activation == "click"){
                                        org_elem.click(function(){
                                                active_tiptip();
                                                return false;
                                        }).hover(function(){},function(){
                                                if(!opts.keepAlive){
                                                        deactive_tiptip();
                                                }
                                        });
                                        if(opts.keepAlive){
                                                tiptip_holder.hover(function(){}, function(){
                                                        deactive_tiptip();
                                                });
                                        }
                                }

                                function active_tiptip(){
                                        opts.enter.call(this);
                                        tiptip_content.html(org_title);
                                        tiptip_holder.hide().removeAttr("class").css("margin","0");
                                        tiptip_arrow.removeAttr("style");

                                        var top = parseInt(org_elem.offset()['top']);
                                        var left = parseInt(org_elem.offset()['left']);
                                        var org_width = parseInt(org_elem.outerWidth());
                                        var org_height = parseInt(org_elem.outerHeight());
                                        var tip_w = tiptip_holder.outerWidth();
                                        var tip_h = tiptip_holder.outerHeight();
                                        var w_compare = Math.round((org_width - tip_w) / 2);
                                        var h_compare = Math.round((org_height - tip_h) / 2);
                                        var marg_left = Math.round(left + w_compare);
                                        var marg_top = Math.round(top + org_height + opts.edgeOffset);
                                        var t_class = "";
                                        var arrow_top = "";
                                        var arrow_left = Math.round(tip_w - 12) / 2;

                    if(opts.defaultPosition == "bottom"){
                        t_class = "_bottom";
                        } else if(opts.defaultPosition == "top"){
                                t_class = "_top";
                        } else if(opts.defaultPosition == "left"){
                                t_class = "_left";
                        } else if(opts.defaultPosition == "right"){
                                t_class = "_right";
                        }

                                        var right_compare = (w_compare + left) < parseInt($(window).scrollLeft());
                                        var left_compare = (tip_w + left) > parseInt($(window).width());

                                        if((right_compare && w_compare < 0) || (t_class == "_right" && !left_compare) || (t_class == "_left" && left < (tip_w + opts.edgeOffset + 5))){
                                                t_class = "_right";
                                                arrow_top = Math.round(tip_h - 13) / 2;
                                                arrow_left = -12;
                                                marg_left = Math.round(left + org_width + opts.edgeOffset);
                                                marg_top = Math.round(top + h_compare);
                                        } else if((left_compare && w_compare < 0) || (t_class == "_left" && !right_compare)){
                                                t_class = "_left";
                                                arrow_top = Math.round(tip_h - 13) / 2;
                                                arrow_left =  Math.round(tip_w);
                                                marg_left = Math.round(left - (tip_w + opts.edgeOffset + 5));
                                                marg_top = Math.round(top + h_compare);
                                        }

                                        var top_compare = (top + org_height + opts.edgeOffset + tip_h + 8) > parseInt($(window).height() + $(window).scrollTop());
                                        var bottom_compare = ((top + org_height) - (opts.edgeOffset + tip_h + 8)) < 0;

                                        if(top_compare || (t_class == "_bottom" && top_compare) || (t_class == "_top" && !bottom_compare)){
                                                if(t_class == "_top" || t_class == "_bottom"){
                                                        t_class = "_top";
                                                } else {
                                                        t_class = t_class+"_top";
                                                }
                                                arrow_top = tip_h;
                                                marg_top = Math.round(top - (tip_h + 5 + opts.edgeOffset));
                                        } else if(bottom_compare | (t_class == "_top" && bottom_compare) || (t_class == "_bottom" && !top_compare)){
                                                if(t_class == "_top" || t_class == "_bottom") {
                                t_class = "_bottom";
                            } else {
                                t_class = t_class + "_bottom";
                            }
                            arrow_top = -12;
                            marg_top = Math.round(top + org_height + opts.edgeOffset);
                        }

                        if (t_class == "_right_top" || t_class == "_left_top") {
                            marg_top = marg_top + 5;
                        } else if (t_class == "_right_bottom" || t_class == "_left_bottom") {
                            marg_top = marg_top - 5;
                        }
                        if (t_class == "_left_top" || t_class == "_left_bottom") {
                            marg_left = marg_left + 5;
                        }
                        tiptip_arrow.css({"margin-left": arrow_left + "px", "margin-top": arrow_top + "px"});
                        tiptip_holder.css({"margin-left": marg_left + "px", "margin-top": marg_top + "px"}).attr("class", "tip" + t_class);

                        if (timeout) {
                            clearTimeout(timeout);
                        }
                        timeout = setTimeout(function () {
                            tiptip_holder.stop(true, true).fadeIn(opts.fadeIn);
                        }, opts.delay);
                    }

                    function deactive_tiptip() {
                        opts.exit.call(this);
                        if (timeout) {
                            clearTimeout(timeout);
                        }
                        tiptip_holder.fadeOut(opts.fadeOut);
                    }
                }
            });
        }
    })(jQuery); 

    !function(a){if("object"==typeof exports&&"undefined"!=typeof module)module.exports=a();else if("function"==typeof define&&define.amd)define([],a);else{var b;b="undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self?self:this,b.mexp=a()}}(function(){return function a(b,c,d){function e(g,h){if(!c[g]){if(!b[g]){var i="function"==typeof require&&require;if(!h&&i)return i(g,!0);if(f)return f(g,!0);var j=new Error("Cannot find module '"+g+"'");throw j.code="MODULE_NOT_FOUND",j}var k=c[g]={exports:{}};b[g][0].call(k.exports,function(a){var c=b[g][1][a];return e(c?c:a)},k,k.exports,a,b,c,d)}return c[g].exports}for(var f="function"==typeof require&&require,g=0;g<d.length;g++)e(d[g]);return e}({1:[function(a,b,c){var d=a("./postfix_evaluator.js");d.prototype.formulaEval=function(){"use strict";for(var a,b,c,d=[],e=this.value,f=0;f<e.length;f++)1===e[f].type||3===e[f].type?d.push({value:3===e[f].type?e[f].show:e[f].value,type:1}):13===e[f].type?d.push({value:e[f].show,type:1}):0===e[f].type?d[d.length-1]={value:e[f].show+("-"!=e[f].show?"(":"")+d[d.length-1].value+("-"!=e[f].show?")":""),type:0}:7===e[f].type?d[d.length-1]={value:(1!=d[d.length-1].type?"(":"")+d[d.length-1].value+(1!=d[d.length-1].type?")":"")+e[f].show,type:7}:10===e[f].type?(a=d.pop(),b=d.pop(),"P"===e[f].show||"C"===e[f].show?d.push({value:"<sup>"+b.value+"</sup>"+e[f].show+"<sub>"+a.value+"</sub>",type:10}):d.push({value:(1!=b.type?"(":"")+b.value+(1!=b.type?")":"")+"<sup>"+a.value+"</sup>",type:1})):2===e[f].type||9===e[f].type?(a=d.pop(),b=d.pop(),d.push({value:(1!=b.type?"(":"")+b.value+(1!=b.type?")":"")+e[f].show+(1!=a.type?"(":"")+a.value+(1!=a.type?")":""),type:e[f].type})):12===e[f].type&&(a=d.pop(),b=d.pop(),c=d.pop(),d.push({value:e[f].show+"("+c.value+","+b.value+","+a.value+")",type:12}));return d[0].value},b.exports=d},{"./postfix_evaluator.js":5}],2:[function(a,b,c){function d(a,b){for(var c=0;c<a.length;c++)a[c]+=b;return a}function e(a,b,c,d){for(var e=0;e<d;e++)if(a[c+e]!==b[e])return!1;return!0}var f=a("./math_function.js"),g=["sin","cos","tan","pi","(",")","P","C","asin","acos","atan","7","8","9","int","cosh","acosh","ln","^","root","4","5","6","/","!","tanh","atanh","Mod","1","2","3","*","sinh","asinh","e","log","0",".","+","-",",","Sigma","n","Pi","pow"],h=["sin","cos","tan","&pi;","(",")","P","C","asin","acos","atan","7","8","9","Int","cosh","acosh"," ln","^","root","4","5","6","&divide;","!","tanh","atanh"," Mod ","1","2","3","&times;","sinh","asinh","e"," log","0",".","+","-",",","&Sigma;","n","&Pi;","pow"],i=[f.math.sin,f.math.cos,f.math.tan,"PI","(",")",f.math.P,f.math.C,f.math.asin,f.math.acos,f.math.atan,"7","8","9",Math.floor,f.math.cosh,f.math.acosh,Math.log,Math.pow,Math.sqrt,"4","5","6",f.math.div,f.math.fact,f.math.tanh,f.math.atanh,f.math.mod,"1","2","3",f.math.mul,f.math.sinh,f.math.asinh,"E",f.math.log,"0",".",f.math.add,f.math.sub,",",f.math.sigma,"n",f.math.Pi,Math.pow],j={0:11,1:0,2:3,3:0,4:0,5:0,6:0,7:11,8:11,9:1,10:10,11:0,12:11,13:0},k=[0,0,0,3,4,5,10,10,0,0,0,1,1,1,0,0,0,0,10,0,1,1,1,2,7,0,0,2,1,1,1,2,0,0,3,0,1,6,9,9,11,12,13,12,8],l={0:!0,1:!0,3:!0,4:!0,6:!0,8:!0,9:!0,12:!0,13:!0},m={0:!0,1:!0,2:!0,3:!0,4:!0,5:!0,6:!0,7:!0,8:!0,9:!0,10:!0,11:!0,12:!0,13:!0},n={0:!0,3:!0,4:!0,8:!0,12:!0,13:!0},o={},p={0:!0,1:!0,3:!0,4:!0,6:!0,8:!0,12:!0,13:!0},q={1:!0},r=[[],["1","2","3","7","8","9","4","5","6","+","-","*","/","(",")","^","!","P","C","e","0",".",",","n"],["pi","ln","Pi"],["sin","cos","tan","Del","int","Mod","log","pow"],["asin","acos","atan","cosh","root","tanh","sinh"],["acosh","atanh","asinh","Sigma"]];f.addToken=function(a){for(var b=0;b<a.length;b++){var c=a[b].token.length,d=-1;if(c<r.length)for(var e=0;e<r[c].length;e++)if(a[b].token===r[c][e]){d=g.indexOf(r[c][e]);break}d===-1?(g.push(a[b].token),k.push(a[b].type),r.length<=a[b].token.length&&(r[a[b].token.length]=[]),r[a[b].token.length].push(a[b].token),i.push(a[b].value),h.push(a[b].show)):(g[d]=a[b].token,k[d]=a[b].type,i[d]=a[b].value,h[d]=a[b].show)}},f.lex=function(a,b){"use strict";var c,s,t,u,v={value:f.math.changeSign,type:0,pre:21,show:"-"},w={value:")",show:")",type:5,pre:0},x={value:"(",type:4,pre:0,show:"("},y=[x],z=[],A=a,B=0,C=l,D=0,E=o,F="";"undefined"!=typeof b&&f.addToken(b);var G={};for(s=0;s<A.length;s++)if(" "!==A[s]){for(c="",t=A.length-s>r.length-2?r.length-1:A.length-s;t>0;t--)for(u=0;u<r[t].length;u++)e(A,r[t][u],s,t)&&(c=r[t][u],u=r[t].length,t=0);if(s+=c.length-1,""===c)throw new f.Exception("Can't understand after "+A.slice(s));var H,I=g.indexOf(c),J=c,K=k[I],L=i[I],M=j[K],N=h[I],O=y[y.length-1];for(H=z.length;H--&&0===z[H];)if([0,2,3,5,9,11,12,13].indexOf(K)!==-1){if(C[K]!==!0)throw new f.Exception(c+" is not allowed after "+F);y.push(w),C=m,E=p,d(z,-1).pop()}if(C[K]!==!0)throw new f.Exception(c+" is not allowed after "+F);if(E[K]===!0&&(K=2,L=f.math.mul,N="&times;",M=3,s-=c.length),G={value:L,type:K,pre:M,show:N},0===K)C=l,E=o,d(z,2).push(2),y.push(G),y.push(x);else if(1===K)1===O.type?(O.value+=L,d(z,1)):y.push(G),C=m,E=n;else if(2===K)C=l,E=o,d(z,2),y.push(G);else if(3===K)y.push(G),C=m,E=p;else if(4===K)B+=z.length,z=[],D++,C=l,E=o,y.push(G);else if(5===K){if(!D)throw new f.Exception("Closing parenthesis are more than opening one, wait What!!!");for(;B--;)y.push(w);B=0,D--,C=m,E=p,y.push(G)}else if(6===K){if(O.hasDec)throw new f.Exception("Two decimals are not allowed in one number");1!==O.type&&(O={value:0,type:1,pre:0},y.push(O),d(z,-1)),C=q,d(z,1),E=o,O.value+=L,O.hasDec=!0}else 7===K&&(C=m,E=p,d(z,1),y.push(G));8===K?(C=l,E=o,d(z,4).push(4),y.push(G),y.push(x)):9===K?(9===O.type?O.value===f.math.add?(O.value=L,O.show=N,d(z,1)):O.value===f.math.sub&&"-"===N&&(O.value=f.math.add,O.show="+",d(z,1)):5!==O.type&&7!==O.type&&1!==O.type&&3!==O.type&&13!==O.type?"-"===J&&(C=l,E=o,d(z,2).push(2),y.push(v),y.push(x)):(y.push(G),d(z,2)),C=l,E=o):10===K?(C=l,E=o,d(z,2),y.push(G)):11===K?(C=l,E=o,y.push(G)):12===K?(C=l,E=o,d(z,6).push(6),y.push(G),y.push(x)):13===K&&(C=m,E=p,y.push(G)),d(z,-1),F=c}for(H=z.length;H--&&0===z[H];)y.push(w),d(z,-1).pop();if(C[5]!==!0)throw new f.Exception("complete the expression");for(;D--;)y.push(w);return y.push(w),new f(y)},b.exports=f},{"./math_function.js":3}],3:[function(a,b,c){var d=function(a){this.value=a};d.math={isDegree:!0,acos:function(a){return d.math.isDegree?180/Math.PI*Math.acos(a):Math.acos(a)},add:function(a,b){return a+b},asin:function(a){return d.math.isDegree?180/Math.PI*Math.asin(a):Math.asin(a)},atan:function(a){return d.math.isDegree?180/Math.PI*Math.atan(a):Math.atan(a)},acosh:function(a){return Math.log(a+Math.sqrt(a*a-1))},asinh:function(a){return Math.log(a+Math.sqrt(a*a+1))},atanh:function(a){return Math.log((1+a)/(1-a))},C:function(a,b){var c=1,e=a-b,f=b;f<e&&(f=e,e=b);for(var g=f+1;g<=a;g++)c*=g;return c/d.math.fact(e)},changeSign:function(a){return-a},cos:function(a){return d.math.isDegree&&(a=d.math.toRadian(a)),Math.cos(a)},cosh:function(a){return(Math.pow(Math.E,a)+Math.pow(Math.E,-1*a))/2},div:function(a,b){return a/b},fact:function(a){if(a%1!==0)return"NaN";for(var b=1,c=2;c<=a;c++)b*=c;return b},inverse:function(a){return 1/a},log:function(a){return Math.log(a)/Math.log(10)},mod:function(a,b){return a%b},mul:function(a,b){return a*b},P:function(a,b){for(var c=1,d=Math.floor(a)-Math.floor(b)+1;d<=Math.floor(a);d++)c*=d;return c},Pi:function(a,b,c){for(var d=1,e=a;e<=b;e++)d*=Number(c.postfixEval({n:e}));return d},pow10x:function(a){for(var b=1;a--;)b*=10;return b},sigma:function(a,b,c){for(var d=0,e=a;e<=b;e++)d+=Number(c.postfixEval({n:e}));return d},sin:function(a){return d.math.isDegree&&(a=d.math.toRadian(a)),Math.sin(a)},sinh:function(a){return(Math.pow(Math.E,a)-Math.pow(Math.E,-1*a))/2},sub:function(a,b){return a-b},tan:function(a){return d.math.isDegree&&(a=d.math.toRadian(a)),Math.tan(a)},tanh:function(a){return d.sinha(a)/d.cosha(a)},toRadian:function(a){return a*Math.PI/180}},d.Exception=function(a){this.message=a},b.exports=d},{}],4:[function(a,b,c){var d=a("./lexer.js");d.prototype.toPostfix=function(){"use strict";for(var a,b,c,e,f,g=[],h=[{value:"(",type:4,pre:0}],i=this.value,j=1;j<i.length;j++)if(1===i[j].type||3===i[j].type||13===i[j].type)1===i[j].type&&(i[j].value=Number(i[j].value)),g.push(i[j]);else if(4===i[j].type)h.push(i[j]);else if(5===i[j].type)for(;4!==(b=h.pop()).type;)g.push(b);else if(11===i[j].type){for(;4!==(b=h.pop()).type;)g.push(b);h.push(b)}else{a=i[j],e=a.pre,f=h[h.length-1],c=f.pre;var k="Math.pow"==f.value&&"Math.pow"==a.value;if(e>c)h.push(a);else{for(;c>=e&&!k||k&&e<c;)b=h.pop(),f=h[h.length-1],g.push(b),c=f.pre,k="Math.pow"==a.value&&"Math.pow"==f.value;h.push(a)}}return new d(g)},b.exports=d},{"./lexer.js":2}],5:[function(a,b,c){var d=a("./postfix.js");d.prototype.postfixEval=function(a){"use strict";a=a||{},a.PI=Math.PI,a.E=Math.E;for(var b,c,e,f=[],g=this.value,h="undefined"!=typeof a.n,i=0;i<g.length;i++)1===g[i].type?f.push({value:g[i].value,type:1}):3===g[i].type?f.push({value:a[g[i].value],type:1}):0===g[i].type?"undefined"==typeof f[f.length-1].type?f[f.length-1].value.push(g[i]):f[f.length-1].value=g[i].value(f[f.length-1].value):7===g[i].type?"undefined"==typeof f[f.length-1].type?f[f.length-1].value.push(g[i]):f[f.length-1].value=g[i].value(f[f.length-1].value):8===g[i].type?(b=f.pop(),c=f.pop(),f.push({type:1,value:g[i].value(c.value,b.value)})):10===g[i].type?(b=f.pop(),c=f.pop(),"undefined"==typeof c.type?(c.value=c.concat(b),c.value.push(g[i]),f.push(c)):"undefined"==typeof b.type?(b.unshift(c),b.push(g[i]),f.push(b)):f.push({type:1,value:g[i].value(c.value,b.value)})):2===g[i].type||9===g[i].type?(b=f.pop(),c=f.pop(),"undefined"==typeof c.type?(console.log(c),c=c.concat(b),c.push(g[i]),f.push(c)):"undefined"==typeof b.type?(b.unshift(c),b.push(g[i]),f.push(b)):f.push({type:1,value:g[i].value(c.value,b.value)})):12===g[i].type?(b=f.pop(),"undefined"!=typeof b.type&&(b=[b]),c=f.pop(),e=f.pop(),f.push({type:1,value:g[i].value(e.value,c.value,new d(b))})):13===g[i].type&&(h?f.push({value:a[g[i].value],type:3}):f.push([g[i]]));if(f.length>1)throw new d.exception("Uncaught Syntax error");return f[0].value>1e15?"Infinity":parseFloat(f[0].value.toFixed(15))},d.eval=function(a,b,c){return"undefined"==typeof b?this.lex(a).toPostfix().postfixEval():"undefined"==typeof c?"undefined"!=typeof b.length?this.lex(a,b).toPostfix().postfixEval():this.lex(a).toPostfix().postfixEval(b):this.lex(a,b).toPostfix().postfixEval(c)},b.exports=d},{"./postfix.js":4}]},{},[1])(1)});

    !function(e){if("function"==typeof define&&define.amd)define(["jquery"],e);else if("object"==typeof exports){var n=require("jquery");module.exports=e(n)}else e(window.jQuery||window.Zepto||window.$)}(function(e){"use strict";e.fn.serializeJSON=function(n){var r,s,t,i,a,u,l,o,p,c,d,f,y;return r=e.serializeJSON,s=this,t=r.setupOpts(n),i=s.serializeArray(),r.readCheckboxUncheckedValues(i,t,s),a={},e.each(i,function(e,n){u=n.name,l=n.value,p=r.extractTypeAndNameWithNoType(u),c=p.nameWithNoType,(d=p.type)||(d=r.attrFromInputWithName(s,u,"data-value-type")),r.validateType(u,d,t),"skip"!==d&&(f=r.splitInputNameIntoKeysArray(c),o=r.parseValue(l,u,d,t),(y=!o&&r.shouldSkipFalsy(s,u,c,d,t))||r.deepSet(a,f,o,t))}),a},e.serializeJSON={defaultOptions:{checkboxUncheckedValue:void 0,parseNumbers:!1,parseBooleans:!1,parseNulls:!1,parseAll:!1,parseWithFunction:null,skipFalsyValuesForTypes:[],skipFalsyValuesForFields:[],customTypes:{},defaultTypes:{string:function(e){return String(e)},number:function(e){return Number(e)},boolean:function(e){return-1===["false","null","undefined","","0"].indexOf(e)},null:function(e){return-1===["false","null","undefined","","0"].indexOf(e)?e:null},array:function(e){return JSON.parse(e)},object:function(e){return JSON.parse(e)},auto:function(n){return e.serializeJSON.parseValue(n,null,null,{parseNumbers:!0,parseBooleans:!0,parseNulls:!0})},skip:null},useIntKeysAsArrayIndex:!1},setupOpts:function(n){var r,s,t,i,a,u;u=e.serializeJSON,null==n&&(n={}),t=u.defaultOptions||{},s=["checkboxUncheckedValue","parseNumbers","parseBooleans","parseNulls","parseAll","parseWithFunction","skipFalsyValuesForTypes","skipFalsyValuesForFields","customTypes","defaultTypes","useIntKeysAsArrayIndex"];for(r in n)if(-1===s.indexOf(r))throw new Error("serializeJSON ERROR: invalid option '"+r+"'. Please use one of "+s.join(", "));return i=function(e){return!1!==n[e]&&""!==n[e]&&(n[e]||t[e])},a=i("parseAll"),{checkboxUncheckedValue:i("checkboxUncheckedValue"),parseNumbers:a||i("parseNumbers"),parseBooleans:a||i("parseBooleans"),parseNulls:a||i("parseNulls"),parseWithFunction:i("parseWithFunction"),skipFalsyValuesForTypes:i("skipFalsyValuesForTypes"),skipFalsyValuesForFields:i("skipFalsyValuesForFields"),typeFunctions:e.extend({},i("defaultTypes"),i("customTypes")),useIntKeysAsArrayIndex:i("useIntKeysAsArrayIndex")}},parseValue:function(n,r,s,t){var i,a;return i=e.serializeJSON,a=n,t.typeFunctions&&s&&t.typeFunctions[s]?a=t.typeFunctions[s](n):t.parseNumbers&&i.isNumeric(n)?a=Number(n):!t.parseBooleans||"true"!==n&&"false"!==n?t.parseNulls&&"null"==n?a=null:t.typeFunctions&&t.typeFunctions.string&&(a=t.typeFunctions.string(n)):a="true"===n,t.parseWithFunction&&!s&&(a=t.parseWithFunction(a,r)),a},isObject:function(e){return e===Object(e)},isUndefined:function(e){return void 0===e},isValidArrayIndex:function(e){return/^[0-9]+$/.test(String(e))},isNumeric:function(e){return e-parseFloat(e)>=0},optionKeys:function(e){if(Object.keys)return Object.keys(e);var n,r=[];for(n in e)r.push(n);return r},readCheckboxUncheckedValues:function(n,r,s){var t,i,a;null==r&&(r={}),e.serializeJSON,t="input[type=checkbox][name]:not(:checked):not([disabled])",s.find(t).add(s.filter(t)).each(function(s,t){if(i=e(t),null==(a=i.attr("data-unchecked-value"))&&(a=r.checkboxUncheckedValue),null!=a){if(t.name&&-1!==t.name.indexOf("[]["))throw new Error("serializeJSON ERROR: checkbox unchecked values are not supported on nested arrays of objects like '"+t.name+"'. See https://github.com/marioizquierdo/jquery.serializeJSON/issues/67");n.push({name:t.name,value:a})}})},extractTypeAndNameWithNoType:function(e){var n;return(n=e.match(/(.*):([^:]+)$/))?{nameWithNoType:n[1],type:n[2]}:{nameWithNoType:e,type:null}},shouldSkipFalsy:function(n,r,s,t,i){var a=e.serializeJSON.attrFromInputWithName(n,r,"data-skip-falsy");if(null!=a)return"false"!==a;var u=i.skipFalsyValuesForFields;if(u&&(-1!==u.indexOf(s)||-1!==u.indexOf(r)))return!0;var l=i.skipFalsyValuesForTypes;return null==t&&(t="string"),!(!l||-1===l.indexOf(t))},attrFromInputWithName:function(e,n,r){var s,t;return s=n.replace(/(:|\.|\[|\]|\s)/g,"\\$1"),t='[name="'+s+'"]',e.find(t).add(e.filter(t)).attr(r)},validateType:function(n,r,s){var t,i;if(i=e.serializeJSON,t=i.optionKeys(s?s.typeFunctions:i.defaultOptions.defaultTypes),r&&-1===t.indexOf(r))throw new Error("serializeJSON ERROR: Invalid type "+r+" found in input name '"+n+"', please use one of "+t.join(", "));return!0},splitInputNameIntoKeysArray:function(n){var r;return e.serializeJSON,r=n.split("["),""===(r=e.map(r,function(e){return e.replace(/\]/g,"")}))[0]&&r.shift(),r},deepSet:function(n,r,s,t){var i,a,u,l,o,p;if(null==t&&(t={}),(p=e.serializeJSON).isUndefined(n))throw new Error("ArgumentError: param 'o' expected to be an object or array, found undefined");if(!r||0===r.length)throw new Error("ArgumentError: param 'keys' expected to be an array with least one element");i=r[0],1===r.length?""===i?n.push(s):n[i]=s:(a=r[1],""===i&&(o=n[l=n.length-1],i=p.isObject(o)&&(p.isUndefined(o[a])||r.length>2)?l:l+1),""===a?!p.isUndefined(n[i])&&e.isArray(n[i])||(n[i]=[]):t.useIntKeysAsArrayIndex&&p.isValidArrayIndex(a)?!p.isUndefined(n[i])&&e.isArray(n[i])||(n[i]=[]):!p.isUndefined(n[i])&&p.isObject(n[i])||(n[i]={}),u=r.slice(1),p.deepSet(n[i],u,s,t))}}});
    
    var in_quick_view = <?php echo $in_quick_view ? 1 : 0; ?>;
    var nbOption = {
        status: false,
        initialed: false,
        options: <?php echo json_encode($options); ?>,
        bulk_fields: <?php echo json_encode($bulk_fields); ?>,
        nbd_fields: {},
        odOption: {},
        extraOdOption: {},
        lastOdOption: {},
        lastExtraOdOption: {},
        crtlId: 'nbo-ctrl-<?php echo $appid; ?>',
        updateVariations: function(){
            var scope = angular.element(document.getElementById(nbOption.crtlId)).scope();
            scope.updateVariations();
        },
        updateBulkPrice: function(){
            var scope = angular.element(document.getElementById(nbOption.crtlId)).scope();
            scope.calculate_bulk_total_price();
        },
        enable_gallery_api: <?php if( $enable_gallery_api ){echo '1';}else{echo '0';}; ?>,
        template_folder: "<?php echo $template_folder; ?>",
        options_str: '',
        prev_options_str: '',
        gallery: {},
        gallery_url: "<?php echo NBDESIGNER_DATA_URL . '/gallery'; ?>",
        design_stored: 0,
        has_delivery: <?php if( $has_delivery ){echo '1';}else{echo '0';}; ?>,
        delivery_field_id: '<?php if( $has_delivery ){echo $delivery_fields[0]['id'];}else{echo '';}; ?>',
        delivery_count: parseInt(<?php if( $has_delivery ){echo count( $delivery_fields );}else{echo 0;}; ?>)
    };
    jQuery('.variations_form').on('woocommerce_variation_has_changed wc_variation_form', function(){
        startApp();
    });
    jQuery('.variations_form').on('found_variation', function(){
        setTimeout(function(){
            startApp();
        }, 100);
    });
    function _debounce(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };
    jQuery(document).ready(function(){
        <?php if($show_quantity_option && !$disable_quantity_input && $options['quantity_type'] == 'r'): ?>
        if( nbOption.options.quantity_min != '' ){
            jQuery('input[name="quantity"]').attr('min', nbOption.options.quantity_min);
        }
        if( nbOption.options.quantity_max != '' ){
            jQuery('input[name="quantity"]').attr('max', nbOption.options.quantity_max);
        }
        var changeQtyFn = _debounce(function(event){
                if( event.namespace == 'nbo' ){
                    startApp();
                }else{
                    startApp( true );
                }
            } , 1000);
        <?php else: ?>
        var changeQtyFn = function(event){
                if( event.namespace == 'nbo' ){
                    startApp();
                }else{
                    startApp( true );
                }
            };
        <?php endif ?>
        jQuery('input[name="quantity"]').on('input change change.nbo', changeQtyFn);
        <?php if($disable_quantity_input): ?>
            jQuery('input[name="quantity"]').on('click', function(){
                if( nbOption.status ){
                    jQuery('html,body').animate({
                        scrollTop: jQuery("#nbo-quantity-option-wrap").offset().top
                    }, 'slow');
                }
            });
        <?php endif; ?>
        jQuery('#nbd-trigger-nbo-popup').on('click', function(){
            jQuery('#nbo-detail-popup-wrap').showNBDPopup();
        });
        jQuery('#nbo-sumit-popup-action').on('click', function(){
            jQuery('.single_add_to_cart_button').trigger('click');
            jQuery('#nbo-detail-popup-wrap .popup-inner').trigger('click');
        });
    });
    function startApp( updateQty ){
        if( nbOption.status ){
            var scope = angular.element(document.getElementById("nbo-ctrl-<?php echo $appid; ?>")).scope();
            scope.mapOptions();
            if( angular.isDefined(updateQty) ){
                if( nbOption.has_delivery ){
                    scope.update_delivery_date();
                }
            }
            scope.check_valid();
            scope.update_app(); 
            <?php if($show_quantity_option && !$disable_quantity_input): ?>
                if( angular.isDefined(updateQty) ){
                    scope.quantity = scope.validate_int( jQuery('input[name="quantity"]').val());
                }
            <?php endif; ?>
        }
    };
    var option_selector = "<?php echo nbdesigner_get_option('nbdesigner_selector_increase_qty_btn'); ?>";
    var quantity_selector = '.quantity:not(.buttons_added) .minus, .quantity:not(.buttons_added) .plus, .quantity-plus, .quantity-minus';
    var qty_selector = option_selector != '' ? quantity_selector + ', ' + option_selector : quantity_selector;
    jQuery(document).off('click.nbo', qty_selector)
            .on('click.nbo', qty_selector, function(){
                jQuery('input[name="quantity"]').trigger( 'change.nbo' );
            });
    <?php if( $in_design_editor ) : ?>
    var nboApp = nbdApp;
    <?php elseif( $in_nbau_mode_2 ): ?>
    var nboApp = nbuApp;
    <?php else: ?>
    var nboApp = angular.module('nboApp', []);
    <?php endif; ?>

    function nbo_variation_calculator(variation_attributes, product_variations, all_set_callback, not_all_set_callback) {
        this.recalc_needed = true;

        this.all_set_callback = all_set_callback;
        this.not_all_set_callback = not_all_set_callback;
        this.variation_attributes = variation_attributes;
        this.variations_available = product_variations;
        this.variations_current = {};
        this.variations_selected = {};

        this.reset_current = function () {
            for (var attribute in this.variation_attributes) {
                this.variations_current[attribute] = {};
                for (var av = 0; av < this.variation_attributes[attribute].length; av++) {
                    this.variations_current[attribute.toString()][this.variation_attributes[attribute][av].toString()] = 0;
                }
            }
        };

        this.update_current = function () {
            this.reset_current();
            for ( var i = 0; i < this.variations_available.length; i++ ) {
                if ( !this.variations_available[i].variation_is_active ) {
                    continue;
                }

                var variation_attributes = this.variations_available[i].attributes;

                for ( var attribute in variation_attributes ) {
                    var maybe_available_attribute_value = variation_attributes[attribute];
                    var selected_value = this.variations_selected[attribute];

                    if ( selected_value && selected_value == maybe_available_attribute_value ) {
                        this.variations_current[attribute][maybe_available_attribute_value] = 1;
                    } else {
                        var result = true;
                        for ( var other_selected_attribute in this.variations_selected ) {
                            if (other_selected_attribute == attribute) {
                                continue;
                            }

                            var other_selected_attribute_value = this.variations_selected[other_selected_attribute];
                            var other_available_attribute_value = variation_attributes[other_selected_attribute];

                            if (other_selected_attribute_value) {
                                if (other_available_attribute_value) {
                                    if (other_selected_attribute_value != other_available_attribute_value) {
                                        result = false;
                                    }
                                }
                            }
                        }
                        if (result) {
                            if (maybe_available_attribute_value === "") {
                                for (var av in this.variations_current[attribute]) {
                                    this.variations_current[attribute][av] = 1;
                                }
                            } else {
                                this.variations_current[attribute][maybe_available_attribute_value] = 1;
                            }
                        }
                    }
                }
            }
            this.recalc_needed = false;
        };

        this.get_current = function () {
            if (this.recalc_needed) {
                this.update_current();
            }
            return this.variations_current;
        };

        this.reset_selected = function () {
            this.recalc_needed = true;
            this.variations_selected = {};
        }

        this.set_selected = function (key, value) {
            this.recalc_needed = true;
            this.variations_selected[key] = value;
        };

        this.get_selected = function () {
            return this.variations_selected;
        }
    };

    nboApp.controller('optionCtrl', ['$scope', '$timeout', function($scope, $timeout){
        $scope.product_id = <?php echo $product_id; ?>;
        $scope.options = nbOption.options;
        $scope.bulk_fields = nbOption.bulk_fields;
        $scope.fields = $scope.options["fields"];
        $scope.price = "<?php echo $price; ?>";
        $scope.type = "<?php echo $type; ?>";
        $scope.width = parseFloat(<?php echo $width; ?>);
        $scope.height = parseFloat(<?php echo $height; ?>);
        $scope.variations = <?php echo $variations; ?>;
        $scope.dimensions = <?php echo $dimensions; ?>;
        $scope.form_values = <?php echo json_encode( $form_values ); ?>;
        $scope.is_sold_individually = "<?php echo $is_sold_individually; ?>";
        $scope.artwork_action = "<?php echo $artwork_action; ?>";
        $scope._quantity = "<?php echo $quantity; ?>";
        $scope.ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
        $scope.valid_form = false;
        $scope.product_image = [];
        $scope.product_img = [];
        $scope.price_table = [];
        $scope.turnaround_matrix = [];
        $scope.has_price_matrix = false;
        $scope.can_start_design = true;
        $scope.custom_quantity = false;
        $scope.current_group_panel = 0;
        $scope.total_cart_item_price_num = 0;
        $scope.check_valid = function( calculate_pm, pro ){
            $timeout(function(){
                $scope.$emit( "nbo_options_changed", $scope.nbd_fields );
                var check = {}, total_check = true, show_popup_trigger = false;
                angular.forEach($scope.nbd_fields, function(field, field_id){
                    $scope.check_depend(field_id);
                    field.valid = true;
                    field.invalidOption = '';
                    check[field_id] = ( field.enable && field.required == 'y' && (field.value === '' || angular.isUndefined(field.value) ) ) ? false : true;
                    var origin_field = $scope.get_field(field_id);
                    if( angular.isUndefined( origin_field.general.published ) ){
                        field.published = true;
                    } else {
                        field.published = origin_field.general.published == 'y' ? true : false;
                    }
                    if( angular.isUndefined( origin_field.show_in_group ) ){
                        field.show_in_group = false;
                    } else {
                        field.show_in_group = origin_field.show_in_group;
                    }
                    if( origin_field.general.data_type == 'i' ){
                        if( origin_field.general.input_type != 't' && origin_field.general.input_type != 'a' ){
                            if( angular.isUndefined(field.value) ) check[field_id] = false;
                            if( origin_field.general.input_type == 'u' && field.required != 'y' ) check[field_id] = true;
                        }else{
                            if( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'dimension' ){
                                if( angular.isUndefined(field.width) || angular.isUndefined(field.height) ){
                                    check[field_id] = false;
                                }
                            }else if( angular.isDefined(field.value) ){
                                if( field.enable && field.required == 'y' ){
                                    if( angular.isDefined(origin_field.general.text_option.min) && origin_field.general.text_option.min != '' ){
                                        var min = $scope.validate_int(origin_field.general.text_option.min);
                                        if( field.value.length < min ) check[field_id] = false;
                                    }
                                    if( angular.isDefined(origin_field.general.text_option.max) && origin_field.general.text_option.max != '' ){
                                        var max = $scope.validate_int(origin_field.general.text_option.max);
                                        if( field.value.length > max ) check[field_id] = false;
                                    }
                                }
                            }
                        }
                        field.value_name = '';
                        if( angular.isDefined(field.value) ){
                            if( origin_field.general.input_type != 'u' ){
                                field.value_name = field.value;
                            }else if( angular.isDefined(field.value.name) ){
                                field.value_name = field.value.name;
                            }
                        }
                    }else{
                        if( angular.isDefined(field.values) ){
                            field.value_name = '';
                            angular.forEach(field.values, function(val, index){
                                field.value_name += (index == 0 ? '' : ', ') + origin_field.general.attributes.options[val].name;
                            });
                            if( origin_field.nbd_type == "page" || origin_field.nbd_type == "page2"  ){
                                $scope.can_start_design = field.values.length == 0 ? false: true;
                            }
                        }else{
                            var selected_option = origin_field.general.attributes.options[field.value];
                            field.value_name = selected_option.name;
                            if( angular.isDefined($scope.nbd_fields[field_id]) ){
                                $scope.nbd_fields[field_id].form_name = '';
                                if( angular.isDefined(selected_option.enable_subattr) && selected_option.enable_subattr == 'on' ){
                                    if( angular.isDefined(selected_option.sub_attributes) && selected_option.sub_attributes.length > 0 ){
                                        $scope.nbd_fields[field_id].form_name = selected_option.form_name;
                                        if( angular.isUndefined( selected_option.sub_attributes[$scope.nbd_fields[field_id].sub_value] ) ){
                                            $scope.nbd_fields[field_id].sub_value = '0';
                                        }
                                        field.value_name += ' - ' + selected_option.sub_attributes[$scope.nbd_fields[field_id].sub_value].name;
                                    }
                                }
                                if( origin_field.appearance.display_type == 'ad' ){
                                    $scope.nbd_fields[field_id].form_name = '[value]';
                                }
                            }
                            if( origin_field.nbe_type == 'actions' ){
                                var action = selected_option.action;
                                if( !( action == 'u' || action == 'c' ) ){
                                    jQuery('#triggerDesign, #startDesign, #startUpload, #useTemplate').hide();
                                    if( jQuery('.nbd-force-ignore-design').length == 0 ){
                                        jQuery('form.cart').append('<input type="hidden" value="1" name="nbd-force-ignore-design" class="nbd-force-ignore-design" />');
                                    }
                                    if( ( jQuery('#triggerDesign').length > 0 || jQuery('#startDesign').length > 0 || jQuery('#startUpload').length > 0 ) && angular.isDefined( nbds_frontend ) && nbds_frontend.hide_cart_button == 'yes'){
                                        jQuery('button[type="submit"].single_add_to_cart_button').show();
                                    };
                                }else{
                                    jQuery('#triggerDesign, #startDesign, #startUpload, #useTemplate').show();
                                    if( action == 'u' ){
                                        jQuery('#startDesign, #useTemplate').hide();
                                    }else{
                                        jQuery('#startUpload').hide();
                                    }
                                    jQuery('form.cart').find('.nbd-force-ignore-design').remove();
                                    if( ( jQuery('#triggerDesign').length > 0 || jQuery('#startDesign').length > 0 || jQuery('#startUpload').length > 0 ) && angular.isDefined( nbds_frontend ) && nbds_frontend.hide_cart_button == 'yes'){
                                        jQuery('button[type="submit"].single_add_to_cart_button').hide();
                                    };
                                }
                            }

                            if( origin_field.general.attributes.options.length ){
                                origin_field.general.attributes.options.forEach(function(op, opIndex){
                                    $scope.checkAttributeStatus( field_id, opIndex );

                                    if( angular.isDefined( op.enable_subattr ) && op.enable_subattr == 'on' && op.sub_attributes.length > 0 ){
                                        op.sub_attributes.forEach(function(sop, sopIndex){
                                            $scope.checkAttributeStatus( field_id, opIndex, sopIndex );
                                        });
                                    }
                                });

                                if( !$scope.status_fields[field_id][field.value].enable ){
                                    check[field_id] = false;
                                    field.valid = false;
                                    field.invalidOption = selected_option.name;
                                }

                                if( angular.isDefined( field.sub_value ) ){
                                    if( angular.isDefined( selected_option.enable_subattr ) && selected_option.enable_subattr == 'on' && selected_option.sub_attributes.length > 0 ){
                                        var selected_sub_option = selected_option.sub_attributes[field.sub_value];
                                        if( !$scope.status_fields[field_id][field.value].sub_attributes[field.sub_value] ){
                                            check[field_id] = false;
                                            field.valid = false;
                                            field.invalidOption = selected_sub_option.name;
                                        }
                                    }
                                }
                            }

                            if( angular.isDefined( origin_field.nbd_type ) && origin_field.nbd_type == 'shape' ){
                                field.shape = selected_option.shape;
                            }
                        }
                    }
                    if( !field.enable ) check[field_id] = true;
                    if( $scope.options.display_type == 6 ){
                        if( field_id == $scope.options.popup_trigger_field && field.value == $scope.options.popup_trigger_value ){
                            show_popup_trigger = true;
                        }
                    }
                });
                if( show_popup_trigger ){
                    jQuery('#nbd-trigger-nbo-popup').css('display', 'inline-block');
                    jQuery('.single_add_to_cart_button').addClass('nbop-hidden');
                }else{
                    jQuery('#nbd-trigger-nbo-popup').css('display', 'none');
                    jQuery('.single_add_to_cart_button').removeClass('nbop-hidden');
                }
                angular.forEach(check, function(c){
                    total_check = total_check && c;
                });
                /*if( $scope.options.display_type == 3 ){
                    var check_bulk_quantity = false;
                    if( jQuery('.nbb-qty-field').length == 0 ) check_bulk_quantity = true;
                    jQuery.each(jQuery('.nbb-qty-field'), function(key, el){
                        if(jQuery(el).val() != '') check_bulk_quantity = true;
                    });
                    total_check = total_check && check_bulk_quantity;
                }*/
                if( total_check ){
                    $scope.postOptionsToEditor();
                    $scope.calculate_price();
                    <?php if( $options['display_type'] == 3 && count( $options['bulk_fields'] ) ): ?>
                    $scope.calculate_bulk_total_price();
                    <?php endif; ?>
                    <?php if( nbdesigner_get_option( 'nbdesigner_table_pricing_type', '1' ) == '1' ): ?>
                    $scope.calculate_price_table();
                    <?php else: ?>
                    $scope.calculate_price_table2();
                    <?php endif; ?>
                    if( nbOption.has_delivery ){
                        $scope.calc_turnaround_matrix();
                    }
                    $scope.valid_form = true;
                    jQuery('.single_add_to_cart_button').removeClass( "nbo-disabled nbo-hidden");
                    jQuery('.variations_form, form.cart').find('[name="nbo-ignore-design"]').remove();
                    if($scope.can_start_design){
                        if( $scope.type == 'variable' ){
                            var variation_id = jQuery('input[name="variation_id"], input.variation_id').val();
                            if( variation_id != '' && variation_id != 0  ){
                                jQuery('#triggerDesign').removeClass('nbdesigner_disable');
                            }
                        }else{
                            jQuery('#triggerDesign').removeClass('nbdesigner_disable');
                        }
                    }else{
                        jQuery('.variations_form, form.cart').append('<input name="nbo-ignore-design" type="hidden" value="1" />');
                        jQuery('#triggerDesign').addClass('nbdesigner_disable');
                    };
                    jQuery(document).triggerHandler( 'nbo_valid_form' );
                }else{
                    jQuery(document).triggerHandler( 'invalid_nbo_options' );
                    jQuery('.single_add_to_cart_button').addClass( "nbo-disabled");
                    if( nbds_frontend.nbdesigner_hide_add_cart_until_form_filled == 'yes' ){
                        jQuery('.single_add_to_cart_button').addClass( "nbo-hidden");
                    }
                    $scope.valid_form = false;
                    jQuery('#triggerDesign').addClass('nbdesigner_disable');
                    jQuery(document).triggerHandler( 'nbo_invalid_form' );
                }
                $scope.may_be_change_product_image();
                if( $scope.has_price_matrix && ( angular.isUndefined( calculate_pm ) || calculate_pm ) ){
                    $scope.calculate_price_matrix();
                }
                angular.copy($scope.nbd_fields, nbOption.nbd_fields);
                if( !nbOption.initialed ){
                    jQuery(document).triggerHandler( 'initialed_nbo_options' );
                    nbOption.initialed = true;
                    function inIframe() {
                        try {
                            return window.self !== window.top;
                        } catch (e) {
                            return true;
                        }
                    }
                    if( inIframe() ){
                        window.parent.postMessage('initialed_nbo_options', window.location.origin);
                    }
                }else{
                    jQuery(document).triggerHandler( 'update_nbo_options', { pro: pro } );
                };

                var preventEnter = function(event){
                    if( event.keyCode == 13 ) {
                        event.preventDefault();
                        return false;
                    }
                };
                jQuery('.variations_form input, form.cart input').off('keydown', preventEnter).on('keydown', preventEnter);

                if( nbOption.delivery_count > 1 && !$scope.nbd_fields[nbOption.delivery_field_id].enable ){
                    var first = true, delivery_field_id;
                    angular.forEach($scope.nbd_fields, function(field, field_id){
                        var origin_field = $scope.get_field(field_id);
                        if( origin_field.general.enabled == 'y' && origin_field.general.published == 'y' && angular.isDefined( origin_field.nbe_type )
                             && origin_field.nbe_type == 'delivery' & angular.isDefined( origin_field.general.attributes ) && angular.isDefined( origin_field.general.attributes.options ) 
                             && origin_field.general.attributes.options.length > 0 && first && $scope.nbd_fields[field_id].enable ){
                            delivery_field_id = origin_field.id;
                            first = false;
                        }
                    });
                    if( delivery_field_id ){
                        nbOption.delivery_field_id = delivery_field_id;
                        $scope.init_turnaround_matrix();
                        $scope.calc_turnaround_matrix();
                    }
                }

                if( angular.isDefined( $scope.no_of_group ) && $scope.no_of_group != 0 ){
                    $scope.changeGroupPanel( null, -1 );
                }
                jQuery(document).triggerHandler( 'trigger_nbo_options_changed', {fields: $scope.nbd_fields, pro: pro} );

                $scope.update_app();
            });
        };
        $scope.postOptionsToEditor = function(){
            angular.copy( nbOption.odOption, nbOption.lastOdOption );
            angular.copy( nbOption.extraOdOption, nbOption.lastExtraOdOption );
            nbOption.odOption = {};
            nbOption.extraOdOption = {};
            var options_str = '';
            angular.forEach($scope.nbd_fields, function(field, field_id){
                if(field.enable){
                    var origin_field = $scope.get_field(field_id);
                    if( angular.isDefined(origin_field.nbd_type) ){
                        switch(origin_field.nbd_type){
                            case 'dpi':
                                nbOption.odOption.dpi = $scope.validate_int( field.value );
                                break;
                            case 'color':
                                var option_color = origin_field.general.attributes.options[field.value];
                                nbOption.odOption.color = {
                                    bg_type: origin_field.general.attributes.bg_type,
                                    bg_color: option_color.bg_color,
                                    bg_image: option_color.bg_image_url
                                };
                                if( origin_field.general.attributes.bg_type == 'i' ){
                                    options_str += ( ( options_str == '' ) ? '' : '|' ) + 'color,' + field_id + ',' + field.value;
                                }
                                if( angular.isDefined( origin_field.general.attributes.show_as_pt ) && origin_field.general.attributes.show_as_pt == 'y' ){
                                    nbOption.extraOdOption.pattern = {
                                        bg_image: option_color.bg_image_url,
                                        color: option_color.bg_color,
                                        type: origin_field.general.attributes.bg_type
                                    };
                                    delete nbOption.odOption.color;
                                }
                                break;
                            case 'page':
                            case 'page1':
                            case 'page2':
                                var number_page = $scope.validate_int( field.value );
                                nbOption.odOption.page = {
                                    number: number_page,
                                    page_display: origin_field.general.page_display,
                                    exclude_page: origin_field.general.exclude_page,
                                    field_id: field_id
                                };
                                if( origin_field.general.data_type == 'm' ){
                                    nbOption.odOption.page.list_page = field.values;
                                }
                                break;
                            case 'page3':
                                var list_page = [0, 1];
                                if( field.value == 0 ){
                                    list_page = [0];
                                } else if ( field.value == 1 ){
                                    list_page = [1];
                                }
                                nbOption.odOption.page = {
                                    list_page: list_page,
                                    field_id: field_id
                                };
                                break;
                            case 'size':
                                /*var currentFieldIndex = $scope.getFieldIndexById(field_id) + '';
                                if( $scope.options.bulk_fields.includes(currentFieldIndex) ){
                                    nbOption.variations = [];
                                    var bulkForm = jQuery('.nbo-bulk-variation input, .nbo-bulk-variation select').serializeJSON();
                                    angular.forEach(bulkForm['nbb-qty-fields'], function(bf_field, bf_index){
                                        var option_size = origin_field.general.attributes.options[bulkForm['nbb-fields'][field_id][bf_index]];
                                        var first = true, name = '';
                                        angular.forEach(bulkForm['nbb-fields'], function(_bff_field, _bff_id){
                                            var _origin_field = $scope.get_field(_bff_id);
                                            var _option = _origin_field.general.attributes.options[bulkForm['nbb-fields'][_bff_id][bf_index]];
                                            var separate = first ? '' : ', ';
                                            name += separate + _option.name;
                                            first = false;
                                        });
                                        var size = {
                                            product_width: $scope.validate_float( option_size.product_width ),
                                            product_height: $scope.validate_float( option_size.product_height ),
                                            real_width: $scope.validate_float( option_size.real_width ),
                                            real_height: $scope.validate_float( option_size.real_height ),
                                            real_top: $scope.validate_float( option_size.real_top ),
                                            real_left: $scope.validate_float( option_size.real_left )
                                        };
                                        nbOption.variations.push({index: bf_index, qty: $scope.validate_int(bf_field), size: size, name: name});
                                    });
                                }else{*/
                                    if(origin_field.general.attributes.same_size == 'n'){
                                        var option_size = origin_field.general.attributes.options[field.value];
                                        nbOption.odOption.size = {
                                            product_width: $scope.validate_float( option_size.product_width ),
                                            product_height: $scope.validate_float( option_size.product_height ),
                                            real_width: $scope.validate_float( option_size.real_width ),
                                            real_height: $scope.validate_float( option_size.real_height ),
                                            real_top: $scope.validate_float( option_size.real_top ),
                                            real_left: $scope.validate_float( option_size.real_left )
                                        };
                                    }
                                /*}*/
                                break;
                            case 'dimension':
                                nbOption.odOption.dimension = {
                                    width: field.width,
                                    height: field.height
                                };
                                break;
                            case 'orientation':
                                nbOption.odOption.orientation = $scope.validate_int( field.value );
                                break;
                            case 'area':
                                nbOption.odOption.area = $scope.validate_int( parseInt(field.value) + 1 );
                                break;
                            case 'padding':
                                var option = origin_field.general.attributes.options[field.value];
                                nbOption.odOption.padding = parseFloat(option.padding);
                                break;
                            case 'rounded_corner':
                                var option = origin_field.general.attributes.options[field.value];
                                nbOption.extraOdOption.rounded_corner = parseFloat(option.radius);
                                break;
                            case 'overlay':
                                var option = origin_field.general.attributes.options[field.value];
                                nbOption.extraOdOption.overlay = option.overlay_image_url;
                                break;
                            case 'fold':
                                var option = origin_field.general.attributes.options[field.value];
                                nbOption.extraOdOption.fold = option.fold;
                                break;
                            case 'shape':
                                var option = origin_field.general.attributes.options[field.value];
                                nbOption.extraOdOption.shape = option.shape;
                                break;
                        }
                    }
                }
            });
            if( nbOption.enable_gallery_api == '1' && options_str != '' ){
                nbOption.prev_options_str = nbOption.options_str;
                nbOption.options_str = options_str;
                var _options_folder = 'product_id,' + $scope.product_id + '|' + 'template,' + nbOption.template_folder + '|' + nbOption.options_str;
                _options_folder = window.btoa( _options_folder );
                $timeout(function(){
                    if( nbOption.prev_options_str != nbOption.options_str ) $scope.get_gallery( _options_folder );
                });
            }
            /* send option to editor */
            if( angular.equals( nbOption.odOption, nbOption.lastOdOption ) ){
                jQuery(document).triggerHandler( 'change_nbo_options_without_od_option' );
            }else{
                jQuery(document).triggerHandler( 'change_nbo_options_with_od_option' );
            };
            if( !angular.equals( nbOption.extraOdOption, nbOption.lastExtraOdOption ) ){
                jQuery(document).triggerHandler( 'change_nbo_extra_od_options' );
            }
            jQuery(document).triggerHandler( 'change_nbo_options' );
        };
        <?php if( $enable_gallery_api ): ?>
        $scope.get_gallery = function( _options_folder ){
            var gallery_path = window.btoa( "<?php echo urlencode(NBDESIGNER_DATA_DIR . '/gallery'); ?>" ),
            check_gallery_url = "<?php echo NBDESIGNER_PLUGIN_URL . 'includes/gallery_image_exists.php'; ?>",
            get_gallery_url = "<?php echo esc_url_raw( rest_url() ) . 'nbd/v1/gallery/generate'; ?>";
            if( jQuery( '#product-'+ $scope.product_id ).find( '.flex-control-nav li').length == 0 ) return;
            /* jQuery( '#product-'+ $scope.product_id ).find( '.flex-control-nav li:not(:first-child)' ).addClass('nbo-gallery-loading'); */
            function get_gallery(){
                jQuery.get( get_gallery_url + '?request=' + _options_folder + '&stored=' + nbOption.design_stored + '&folder=' + nbOption.template_folder ).done(function( res ) { 
                    if( angular.isDefined( res.flag ) && res.flag == 1 ){
                        $scope.change_gallery_image( res.gallery );
                    }
                });
            }
            if( nbOption.design_stored == 0 ){
                jQuery.get( check_gallery_url + '?path=' + gallery_path + '&folder=' + _options_folder ).done(function( res ) {
                    if( angular.isDefined( res.flag ) && res.flag == 1 ){
                        angular.forEach( res.images, function( image ){
                            image.src = nbOption.gallery_url + '/' + _options_folder + '/' + image.src;
                        });
                        $scope.change_gallery_image( res.images );
                    }else{
                        get_gallery();
                    }
                });
            }else{
                get_gallery();
            }
        };
        <?php endif; ?>
        $scope.getFieldIndexById = function(field_id){
            var currentFieldIndex = 0;
            angular.forEach($scope.options.fields, function(__field, __index){
                if(__field.id == field_id) currentFieldIndex = __index;
            });
            return currentFieldIndex;
        };
        $scope.updateVariations = function(){
            nbOption.variations = [];
            var bulkForm = jQuery('.nbo-bulk-variation input, .nbo-bulk-variation select').serializeJSON();
            angular.forEach(bulkForm['nbb-qty-fields'], function(bf_field, bf_index){
                angular.forEach(bulkForm['nbb-fields'], function(bff_field, bff_id){
                    var origin_field = $scope.get_field(bff_id);
                    if( origin_field.nbd_type == 'size' ){
                        var first = true, name = '';
                        angular.forEach(bulkForm['nbb-fields'], function(_bff_field, _bff_id){
                            var _origin_field = $scope.get_field(_bff_id);
                            var _option = _origin_field.general.attributes.options[bulkForm['nbb-fields'][_bff_id][bf_index]];
                            var separate = first ? '' : ', ';
                            name += separate + _option.name;
                            first = false;
                        });
                        var option_size = origin_field.general.attributes.options[bulkForm['nbb-fields'][bff_id][bf_index]];
                        var size = {
                            product_width: $scope.validate_float( option_size.product_width ),
                            product_height: $scope.validate_float( option_size.product_height ),
                            real_width: $scope.validate_float( option_size.real_width ),
                            real_height: $scope.validate_float( option_size.real_height ),
                            real_top: $scope.validate_float( option_size.real_top ),
                            real_left: $scope.validate_float( option_size.real_left )
                        };
                        nbOption.variations.push({index: bf_index, qty: $scope.validate_int(bf_field), size: size, name: name});
                    }
                });
            });
            if(nbOption.variations.length){
                jQuery(document).triggerHandler( 'change_nbo_size_variations' );
            }
        };
        $scope.updateMultiselectValue = function(field_id){
            $scope.nbd_fields[field_id].values = [];
            angular.forEach($scope.nbd_fields[field_id]._values, function(val, index){
                if(val){
                    $scope.nbd_fields[field_id].values.push(index);
                }
            });
            $scope.nbd_fields[field_id].value = $scope.nbd_fields[field_id].values[0];
            $scope.check_valid();
        };
        $scope.update_dimensionvalue = function(field_id, dir){
            var origin_field = $scope.get_field(field_id),
            current_val = $scope.validate_float( $scope.nbd_fields[field_id][dir] ),
            min_val = $scope.validate_float( origin_field.general['min_' + dir] ),
            max_val = $scope.validate_float( origin_field.general['max_' + dir] );
            current_val = ( current_val < min_val ) ? min_val : current_val;
            current_val = ( max_val != 0 && current_val > max_val ) ? max_val : current_val;
            $scope.nbd_fields[field_id][dir] = current_val;
            $scope.current_dimensions[dir] = current_val;
            $scope.nbd_fields[field_id].value = $scope.nbd_fields[field_id].width + 'x' + $scope.nbd_fields[field_id].height;
            $scope.check_valid();
        };
        $scope.update_dimension = function(field_id, dir, operator){
            var origin_field = $scope.get_field(field_id),
            current_val = $scope.validate_float( $scope.nbd_fields[field_id][dir] ),
            min_val = $scope.validate_float( origin_field.general['min_' + dir] ),
            max_val = $scope.validate_float( origin_field.general['max_' + dir] ),
            step_val = $scope.validate_float( origin_field.general['step_' + dir] );
            step_val = ( step_val == 0 ) ? 1 : step_val;
            if( operator == 'minus' ){
                current_val = (current_val - step_val) >= min_val ? $scope.shorten(current_val - step_val) : min_val;
            }else{
                if( max_val != 0 ){
                    current_val = (current_val + step_val) <= max_val ? $scope.shorten(current_val + step_val) : max_val;
                }
            }
            $scope.nbd_fields[field_id][dir] = current_val;
            $scope.update_dimensionvalue( field_id, dir );
        };
        $scope.lastTickDpi = new Date().getTime();
        $scope.update_dpi = function(){
            $scope.lastTickDpi = new Date().getTime();
            $timeout(function() {
                var current = new Date().getTime();
                if( (current - $scope.lastTickDpi) >= 500){
                    $scope.check_valid();
                };
            }, 500);
        };
        $scope.set_product_image_attr = function(ele, attr, value, id){
            if( angular.isUndefined($scope.product_image[id]) || angular.isUndefined($scope.product_image[id][attr]) ){
                if( angular.isUndefined($scope.product_image[id]) ) $scope.product_image[id] = {};
                $scope.product_image[id][attr] = ele.attr( attr );
            }
            if ( false === value ) {
                ele.removeAttr( attr );
            }else{
                ele.attr( attr, value );
            }
        };
        $scope.reset_product_image_attr = function(ele, attr, id){
            ele.attr( attr, $scope.product_image[id][attr] );
            delete $scope.product_image[id][attr];
        };
        $scope.may_be_change_product_image = function(){
            $scope.product_img = [];
            angular.forEach($scope.nbd_fields, function(_field, field_id){
                var field = $scope.get_field(field_id);
                if( field.general.data_type == 'm' && field.appearance.change_image_product == 'y' 
                    && field.general.attributes.options[_field.value].imagep == 'y' && _field.enable ){
                    $scope.product_img.field_id  = field_id;
                    $scope.product_img.option_index  = _field.value;
                }
            });
            if( angular.isDefined($scope.product_img.field_id) && angular.isDefined($scope.product_img.option_index) ){
                $scope.change_product_image($scope.product_img.field_id, $scope.product_img.option_index);
            }
        };
        $scope.change_product_image = function( field_id, option_index ){
            var field = $scope.get_field(field_id);
            if( field.appearance.change_image_product == 'y' && field.general.attributes.options[option_index].imagep == 'y' ){
                var product_element = jQuery( '#product-'+ $scope.product_id );
                var product_image = product_element.find( '.woocommerce-product-gallery__image:not(.clone), .woocommerce-product-gallery__image--placeholder:not(.clone)' ).eq( 0 ).find( '.wp-post-image' ).first();
                if ( product_image.length === 0 ) {
                    product_image = product_element.find( "a.woocommerce-main-image img, img.woocommerce-main-image" ).not( '.thumbnails img,.product_list_widget img' ).first();
                }
                if ( jQuery( product_image ).length > 1 ) {
                    product_image = jQuery( product_image ).first();
                }  
                var gallery_image = product_element.find( '.flex-control-nav li:eq(0) img' ),
                gallery_wrapper = product_element.find( '.woocommerce-product-gallery__wrapper ' ),
                product_image_wrap = gallery_wrapper.find( '.woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder' ).eq( 0 ),
                product_link = product_image.closest( 'a' );
                var option_data = field.general.attributes.options[option_index];
                if( !option_data.full_src ) option_data.full_src = option_data.image_link;
                if (product_image.length){
                    if( !option_data.full_src_w ) option_data.full_src = product_image.attr('data-large_image_width');
                    if( !option_data.full_src_h ) option_data.full_src_h = product_image.attr('data-large_image_height');
                    $scope.set_product_image_attr(product_image, 'src', option_data.image_link, 0);
                    $scope.set_product_image_attr(product_image, 'srcset', option_data.image_srcset, 0);
                    $scope.set_product_image_attr(product_image, 'sizes', option_data.image_sizes, 0);
                    $scope.set_product_image_attr(product_image, 'title', option_data.image_title, 0);
                    $scope.set_product_image_attr(product_image, 'alt', option_data.image_alt, 0);
                    $scope.set_product_image_attr(product_image, 'data-src', option_data.full_src, 0);
                    $scope.set_product_image_attr(product_image, 'data-large_image', option_data.full_src, 0);
                    $scope.set_product_image_attr(product_image, 'data-large_image_width', option_data.full_src_w, 0);
                    $scope.set_product_image_attr(product_image, 'data-large_image_height', option_data.full_src_h, 0);

                    $scope.set_product_image_attr(product_image, 'alt', option_data.alt, 0);
                    $scope.set_product_image_attr(product_image_wrap, 'data-thumb', option_data.image_link, 1);
                }
                if (gallery_image.length){
                    $scope.set_product_image_attr(gallery_image, 'src', option_data.image_link, 2);
                }
                if (product_link.length){
                    $scope.set_product_image_attr(product_link, 'href', option_data.full_src, 3);
                    $scope.set_product_image_attr(product_link, 'title', option_data.image_caption, 3);
                }
                $scope.init_product_gallery_and_zoom();
            }
        };
        $scope.change_gallery_image = function( gallery_images, folder ){
            if( angular.isDefined( folder ) ){
                nbOption.template_folder = folder;
                nbOption.gallery = {};
                nbOption.design_stored = 1;
            }
            var _options_folder = 'product_id,' + $scope.product_id + '|' + 'template,' + nbOption.template_folder + '|' + nbOption.options_str;
            _options_folder = window.btoa( _options_folder );
            nbOption.gallery[_options_folder] = gallery_images;
            var product_element = jQuery( '#product-'+ $scope.product_id ),
            product_images = product_element.find( '.woocommerce-product-gallery__image:not(.clone), .woocommerce-product-gallery__image--placeholder:not(.clone)' ),
            thumbnail_images = product_element.find( '.flex-control-nav li' );
            if(product_images.length > 1 && gallery_images.length > 0 ){
                jQuery.each( product_images, function( index, el ){
                    if( index > 0 && index <= gallery_images.length ){
                        var timestamp = new Date().getTime(),
                        src = gallery_images[index - 1].src + '?t=' + timestamp;
                        jQuery(el).find('a img').attr({
                            'src': src,
                            'srcset': src + ' 320w',
                            'sizes': gallery_images[index - 1].sizes,
                            'title': gallery_images[index - 1].title,
                            'data-src': src,
                            'data-large_image': src,
                            'data-large_image_width': gallery_images[index - 1].width,
                            'data-large_image_height': gallery_images[index - 1].height,
                            'data-thumb': src
                        });
                        jQuery(el).find('a').attr( 'href', src );
                        jQuery(el).addClass('nbo-gallery-loading');
                        thumbnail_images.eq(index).addClass('nbo-gallery-loading');
                        var image = new Image();
                        image.onload = function(){
                            thumbnail_images.eq(index).find('img').attr( { 'src': src, 'alt': gallery_images[index - 1].title } );
                            thumbnail_images.eq(index).removeClass('nbo-gallery-loading');
                            jQuery(el).removeClass('nbo-gallery-loading');
                            jQuery('#nbdesigner_frontend_area .img-con').eq(index - 1).find('img').attr( { 'src': src, 'alt': gallery_images[index - 1].title } );
                        };
                        image.src = src;
                    }
                });
                $scope.init_product_gallery_and_zoom();
            }
        };
        $scope.change_product_image_without_field = function( option ){
            var product_element = jQuery( '#product-'+ $scope.product_id );
            var product_image = product_element.find( '.woocommerce-product-gallery__image:not(.clone), .woocommerce-product-gallery__image--placeholder:not(.clone)' ).eq( 0 ).find( '.wp-post-image' ).first();
            if ( product_image.length === 0 ) {
                product_image = product_element.find( "a.woocommerce-main-image img, img.woocommerce-main-image,a img" ).not( '.thumbnails img,.product_list_widget img' ).first();
            }
            if ( jQuery( product_image ).length > 1 ) {
                product_image = jQuery( product_image ).first();
            }
            var gallery_image = product_element.find( '.flex-control-nav li:eq(0) img' ),
            gallery_wrapper = product_element.find( '.woocommerce-product-gallery__wrapper ' ),
            product_image_wrap = gallery_wrapper.find( '.woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder' ).eq( 0 ),
            product_link = product_image.closest( 'a' );
            if (product_image.length){
                $scope.set_product_image_attr(product_image, 'src', option.image_link, 0);
                $scope.set_product_image_attr(product_image, 'srcset', option.image_srcset, 0);
                $scope.set_product_image_attr(product_image, 'sizes', option.image_sizes, 0);
                $scope.set_product_image_attr(product_image, 'title', option.image_title, 0);
                $scope.set_product_image_attr(product_image, 'alt', option.image_alt, 0);
                $scope.set_product_image_attr(product_image, 'data-src', option.full_src, 0);
                $scope.set_product_image_attr(product_image, 'data-large_image', option.full_src, 0);
                $scope.set_product_image_attr(product_image, 'data-large_image_width', option.full_src_w, 0);
                $scope.set_product_image_attr(product_image, 'data-large_image_height', option.full_src_h, 0);

                $scope.set_product_image_attr(product_image, 'alt', option.alt, 0);
                $scope.set_product_image_attr(product_image_wrap, 'data-thumb', option.image_link, 1);
            }
            if (gallery_image.length){
                $scope.set_product_image_attr(gallery_image, 'src', option.image_link, 2);
            }
            if (product_link.length){
                $scope.set_product_image_attr(product_link, 'href', option.full_src, 3);
                $scope.set_product_image_attr(product_link, 'title', option.image_caption, 3);
            }
            $scope.init_product_gallery_and_zoom();
        };
        $scope.init_product_gallery_and_zoom = function(){
            var product_element = jQuery( '#product-'+ $scope.product_id );
            var gallery_element = product_element.find( '.woocommerce-product-gallery' );
            if( gallery_element.length && gallery_element.data( 'flexslider' ) ){
                $timeout(function(){
                    gallery_element.flexslider( 0 );
                }, 100);
                window.setTimeout( function () {
                    gallery_element.trigger( 'woocommerce_gallery_init_zoom' );
                    jQuery( window ).trigger( 'resize' );
                }, 10 );
            }
            var zoom_images = product_element.find( '.woocommerce-product-gallery__image' ),
                galleryWidth = product_element.find( '.woocommerce-product-gallery--with-images' ).width(),
                zoomEnabled  = false;
            jQuery( zoom_images ).each( function( index, target ) {
                var image = jQuery( target ).find( 'img.wp-post-image' );
                if ( image.attr( 'data-large_image_width' ) > galleryWidth ) {
                    zoomEnabled = true;
                    return false;
                }
            } ); 
            if ( zoomEnabled ){
                var zoom_options = {
                    touch: false
                };
                if ( 'ontouchstart' in window ) {
                    zoom_options.on = 'click';
                }
                zoom_images.trigger( 'zoom.destroy' );
                if( typeof zoom_images.zoom == 'function' ) zoom_images.zoom( zoom_options );
            }else{
                zoom_images.trigger( 'zoom.destroy' );
            }
        };
        $scope.debug = function(){
            jQuery('input[name="quantity"]').val( 100 );
            jQuery('input[name="quantity"]').trigger( 'change.nbo' );
        };
        $scope.get_field = function( field_id ){
            var _field = null;
            angular.forEach( $scope.fields, function( field ){
                if( field.id == field_id ) _field = field;
            });
            return _field;
        };
        $scope.get_field_index = function( field_id ){
            var _index = null;
            angular.forEach( $scope.fields, function( field, index ){
                if( field.id == field_id ) _index = index;
            });
            return _index;
        };
        $scope.check_depend = function( field_id ){
            if( angular.isUndefined($scope.nbd_fields[field_id]) ) return;
            var field = $scope.get_field(field_id),
            check = [];
            $scope.nbd_fields[field_id].enable = true;
            if( field.conditional.enable == 'n' ) return true;
            if( angular.isUndefined(field.conditional.depend) ) return true;
            if( field.conditional.depend.length == 0 ) return true;
            var show = field.conditional.show,
            logic = field.conditional.logic,
            total_check = logic == 'a' ? true : false;
            angular.forEach(field.conditional.depend, function(con, key){
                if( con.id != '' ){
                    if( con.id != 'qty' && ( angular.isUndefined($scope.nbd_fields[con.id]) || !$scope.nbd_fields[con.id].enable ) ){
                        check[key] = false;
                    }else{
                        if( con.id == 'qty' ){
                            var qty = $scope.validate_int( jQuery('input[name="quantity"]').val() );
                            if( $scope.is_sold_individually == 1 ){
                                qty = 1;
                            }
                            con.val = con.val * 1;
                        }
                        switch(con.operator){
                            case 'i':
                                check[key] = $scope.nbd_fields[con.id].value == con.val ? true : false;
                                break;
                            case 'n':
                                check[key] = $scope.nbd_fields[con.id].value != con.val ? true : false;
                                break;  
                            case 'e':
                                check[key] = $scope.nbd_fields[con.id].value == '' ? true : false;
                                break;
                            case 'ne':
                                check[key] = $scope.nbd_fields[con.id].value != '' ? true : false;
                                break; 
                            case 'eq':
                                check[key] = qty == con.val ? true : false;
                                break;
                            case 'gt':
                                check[key] = qty > con.val ? true : false;
                                break;
                            case 'lt':
                                check[key] = qty < con.val ? true : false;
                                break;
                        }
                    }
                }else{
                    check[key] = true;
                }
            });
            angular.forEach(check, function(c){
                total_check = logic == 'a' ? (total_check && c) : (total_check || c);
            });
            $scope.nbd_fields[field_id].enable = show == 'y' ? total_check : !total_check;
            return $scope.nbd_fields[field_id].enable;
        };
        $scope.checkAttributeStatus = function( field_id, attr_index, sub_attr_index ){
            var check = true, checks = [];
            var origin_field = $scope.get_field(field_id),
            currentOption = origin_field.general.attributes.options[attr_index], 
            option;
            $scope.status_fields[field_id][attr_index] = $scope.status_fields[field_id][attr_index] || {
                sub_attributes: [],
                enable: true
            };

            function assignCheck( check ){
                if( typeof sub_attr_index != 'undefined' ){
                    $scope.status_fields[field_id][attr_index].sub_attributes = $scope.status_fields[field_id][attr_index].sub_attributes || [];
                    $scope.status_fields[field_id][attr_index].sub_attributes[sub_attr_index] = check;
                }else{
                    $scope.status_fields[field_id][attr_index].enable = check;
                }
            }

            if( typeof sub_attr_index != 'undefined' ){
                option = currentOption.sub_attributes[sub_attr_index];
            }else{
                option = currentOption;
            }

            if( angular.isDefined( option.enable_con ) && option.enable_con == 'on' && angular.isDefined( option.depend ) ){
                if( option.depend.length > 0 ){
                    var show = option.con_show,
                    logic = option.con_logic,
                    total_check = logic == 'a' ? true : false;
                    angular.forEach(option.depend, function(con, key){
                        if( con.id != '' ){
                            if(  con.id != 'qty' && ( angular.isUndefined($scope.nbd_fields[con.id]) || !$scope.nbd_fields[con.id].enable ) ){
                                checks[key] = false;
                            }else{
                                if( con.id == 'qty' ){
                                    var qty = $scope.validate_int( jQuery('input[name="quantity"]').val() );
                                    if( $scope.is_sold_individually == 1 ){
                                        qty = 1;
                                    }
                                    con.val = con.val * 1;
                                }
                                switch(con.operator){
                                    case 'i':
                                        checks[key] = $scope.nbd_fields[con.id].value == con.val ? true : false;
                                        break;
                                    case 'n':
                                        checks[key] = $scope.nbd_fields[con.id].value != con.val ? true : false;
                                        break;  
                                    case 'e':
                                        checks[key] = $scope.nbd_fields[con.id].value == '' ? true : false;
                                        break;
                                    case 'ne':
                                        checks[key] = $scope.nbd_fields[con.id].value != '' ? true : false;
                                        break;
                                    case 'eq':
                                        checks[key] = qty == con.val ? true : false;
                                        break;
                                    case 'gt':
                                        checks[key] = qty > con.val ? true : false;
                                        break;
                                    case 'lt':
                                        checks[key] = qty < con.val ? true : false;
                                        break;
                                }
                            }
                            if( angular.isDefined( con.subval ) && con.subval != '' ){
                                if( angular.isUndefined( $scope.nbd_fields[con.id] ) || !$scope.nbd_fields[con.id].enable || angular.isUndefined( $scope.nbd_fields[con.id].sub_value ) ){
                                    checks[key] = false;
                                }else{
                                    switch(con.operator){
                                        case 'i':
                                            checks[key] = ( $scope.nbd_fields[con.id].value == con.val && $scope.nbd_fields[con.id].sub_value == con.subval ) ? true : false;
                                            break;
                                        case 'n':
                                            checks[key] = ( $scope.nbd_fields[con.id].value != con.val || $scope.nbd_fields[con.id].sub_value != con.subval ) ? true : false;
                                            break;
                                    }
                                }
                            }
                        }else{
                            checks[key] = true;
                        }
                    });
                    angular.forEach(checks, function(c){
                        total_check = logic == 'a' ? (total_check && c) : (total_check || c);
                    });
                    check = show == 'y' ? total_check : !total_check;
                }
            }
            assignCheck( check );
        };
        $scope.init = function(){
            $scope.current_dimensions = {};
            nbOption.status = true; 
            <?php if($options['display_type'] == 3 && count($options['bulk_fields'])): ?>
            jQuery('input[name="add-to-cart"]').remove();
            jQuery('button[name="add-to-cart"]').attr('name', 'nbo-add-to-cart');
            jQuery('input[name="quantity"], .quantity .screen-reader-text, .nb-quantity').remove();
            <?php endif; ?>
            <?php if($show_quantity_option): ?>
                $scope.quantity = $scope.validate_int("<?php echo $quantity; ?>");
                <?php if($disable_quantity_input): ?>
                    jQuery(qty_selector + ', input[name="quantity"]').addClass('nbo-disabled');
                <?php endif; ?>
                jQuery('input[name="quantity"]').val($scope.quantity);
            <?php endif; ?>
            <?php if($change_base == 'yes'): ?>
                <?php if( $in_design_editor && $nbd_qv_type == '2' ) : ?>
                var wrapEl = '#nbo-options-wrap .price';
                <?php else: ?>
                var wrapEl = '#product-' + $scope.product_id + ' .summary .price';
                <?php endif; ?>
            if(this.type == 'variable'){
                var price_html = jQuery(wrapEl + ' .woocommerce-Price-amount').first().clone(),
                nbo_price_html = jQuery(wrapEl + ' .nbo-base-price-html-var').clone();
                //price_html.removeClass('amount');
                jQuery(wrapEl + ':first').html('').append(nbo_price_html).append(' ').append(price_html);
            }
            jQuery(wrapEl + ' del').remove();
            if( $scope.artwork_action != '' ){
                if(this.type == 'variable'){
                    var price_html = jQuery('#product-' + $scope.product_id + ' .nbd-design-action-info .price .woocommerce-Price-amount').first().clone(),
                    nbo_price_html = jQuery('#product-' + $scope.product_id + ' .nbd-design-action-info .price .nbo-base-price-html-var').clone();
                    //price_html.removeClass('amount');
                    jQuery('#product-' + $scope.product_id + ' .nbd-design-action-info .price:first').html('').append(nbo_price_html).append(' ').append(price_html);
                }
                jQuery('#product-' + $scope.product_id + ' .nbd-design-action-info .price del').remove();
            }
            <?php endif; ?>
            $scope.nbd_fields = {};
            $scope.status_fields = {};
            $scope.basePrice = $scope.convert_wc_price_to_float( $scope.price );
            $scope.total_price = 0;
            angular.forEach($scope.fields, function(field){
                if(field.general.enabled == 'y'){
                    $scope.nbd_fields[field.id] = {
                        title: field.general.title,
                        price: $scope.convert_to_wc_price(0),
                        required: field.general.required
                    };
                    if(field.general.data_type == 'i'){
                        if( field.general.input_type != 't' && field.general.input_type != 'a' ){
                            if( field.general.input_type != 'u' ){
                                if( angular.isDefined( field.general.input_option.default ) ){
                                    $scope.nbd_fields[field.id].value = field.general.input_option.default != '' ? field.general.input_option.default :  0;
                                }else{
                                    $scope.nbd_fields[field.id].value = field.general.input_option.min != '' ? field.general.input_option.min :  0;
                                }
                            }
                        }else{
                            $scope.nbd_fields[field.id].value = '';
                            if( angular.isDefined( field.nbd_type ) && field.nbd_type == 'dimension' ){
                                if( angular.isDefined( field.general.default_width ) && field.general.default_width != '' ){
                                    $scope.nbd_fields[field.id].width = 1 * field.general.default_width;
                                }
                                if( angular.isDefined( field.general.default_height ) && field.general.default_height != '' ){
                                    $scope.nbd_fields[field.id].height = 1 * field.general.default_height;
                                }
                                if( angular.isDefined( field.general.default_width ) && field.general.default_width != '' 
                                        && angular.isDefined( field.general.default_height ) && field.general.default_height != '' ){
                                    $scope.nbd_fields[field.id].value = $scope.nbd_fields[field.id].width + 'x' + $scope.nbd_fields[field.id].height;
                                }
                            }
                        }
                    }else{
                        if( field.general.attributes.options.length == 0 ){
                            $scope.nbd_fields[field.id].value = '0';
                        }else{
                            $scope.nbd_fields[field.id].value = '0';
                            var selectedOp;
                            $scope.status_fields[field.id] = [];
                            angular.forEach(field.general.attributes.options, function(op, k){
                                if( op.selected == 'on' ){
                                    $scope.nbd_fields[field.id].value = '' + k;
                                    selectedOp = op;
                                }
                                op.form_name = '';
                                if( angular.isDefined(op.enable_subattr) && op.enable_subattr == 'on' ){
                                    if(angular.isDefined(op.sub_attributes)){
                                        $scope.nbd_fields[field.id].sub_value = '0';
                                        if( op.sub_attributes.length > 0 ) op.form_name = '[value]';
                                    }
                                }
                                $scope.status_fields[field.id][k] = {enable: true};
                            });
                            if( !selectedOp ){
                                selectedOp = field.general.attributes.options[0];
                            }
                            if( selectedOp ){
                                if( angular.isDefined(selectedOp.enable_subattr) && selectedOp.enable_subattr == 'on' ){
                                    if(angular.isDefined(selectedOp.sub_attributes)){
                                        $scope.nbd_fields[field.id].sub_value = '0';
                                        angular.forEach(selectedOp.sub_attributes, function(sop, sk){
                                            if( sop.selected == 'on' ) $scope.nbd_fields[field.id].sub_value = '' + sk;
                                        });
                                    }
                                } 
                            }
                            if( $scope.isMultipleSelectPage( field ) ){
                                if( angular.isDefined( $scope.form_values[field.id] ) ){
                                    $scope.nbd_fields[field.id].values = [parseInt($scope.nbd_fields[field.id].value)];
                                }else{
                                    $scope.nbd_fields[field.id].values = [];
                                }
                                $scope.nbd_fields[field.id]._values = [];
                                angular.forEach(field.general.attributes.options, function(op, k){
                                    if( angular.isDefined( $scope.form_values[field.id] ) ){
                                        $scope.nbd_fields[field.id]._values[k] = false;
                                    }else{
                                        if( angular.isDefined( field.general.auto_select_page ) && field.general.auto_select_page == 'n' ){
                                            if( op.selected == 'on' ){
                                                $scope.nbd_fields[field.id]._values[k] = true;
                                                $scope.nbd_fields[field.id].values.push(k);
                                            }
                                        }else{
                                            $scope.nbd_fields[field.id]._values[k] = true;
                                            $scope.nbd_fields[field.id].values.push(k);
                                        }
                                    }
                                    //$scope.nbd_fields[field.id]._values[k] = k == 0 ? true : false;
                                });
                                if( $scope.nbd_fields[field.id]._values.length == 0 ){
                                    $scope.nbd_fields[field.id]._values[0] = true;
                                    $scope.nbd_fields[field.id].values.push(0);
                                }
                            }
                            if( $scope.artwork_action != '' ){
                                if( angular.isDefined( field.nbe_type ) && field.nbe_type == 'actions' ){
                                    $scope.nbd_fields[field.id].value = $scope.artwork_action;
                                }
                            }
                        }
                    }
                }
            });
            angular.forEach($scope.form_values, function(value, field_id){
                if(field_id){
                    if( angular.isDefined(value['sub_value']) ){
                        $scope.nbd_fields[field_id].value = value['value'];
                        $scope.nbd_fields[field_id].sub_value = value['sub_value'];
                    } else if( angular.isDefined(value['value']) ){
                        $scope.nbd_fields[field_id].value = value['value'];
                    }else{
                        $scope.nbd_fields[field_id].value = value;
                    }
                }
                var origin_field = $scope.get_field(field_id);
                if( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'dimension' ){
                    var dimension = value.split("x");
                    $scope.nbd_fields[field_id].width = parseFloat(dimension[0]);
                    $scope.nbd_fields[field_id].height = parseFloat(dimension[1]);
                }
                if( $scope.isMultipleSelectPage( origin_field ) ){
                    $scope.nbd_fields[field_id].value = value[0];
                    $scope.nbd_fields[field_id].values = value;
                    angular.forEach(value, function(val){
                        $scope.nbd_fields[origin_field.id]._values[val] = true;
                    });
                }
            });
            angular.forEach($scope.fields, function(field){
                $scope.check_depend(field.id);
            });
            if( $scope.options.display_type == 2 && ( ( $scope.options.pm_hoz.length > 0 || $scope.options.pm_ver.length > 0 ) || ( $scope.options.mpm_hoz.length > 0 && $scope.options.mpm_ver.length > 0 ) ) ){
                $scope.init_price_matrix();
                $scope.has_price_matrix = true;
            }
            if( nbOption.has_delivery ) $scope.init_turnaround_matrix();
            $scope.check_valid();
            $timeout(function(){
                jQuery('.nbd-option-field:first').removeClass('nbo-collapse');

                if( angular.isDefined( $scope.no_of_group ) && $scope.no_of_group != 0 ){
                    $scope.changeGroupPanel( null, 0 );
                    $scope.initGroupTimeline();
                }
            });
            jQuery(document).on( 'change_nbo_variations', function(){
                $scope.upDateVaritionQty(NBDESIGNERPRODUCT.variations);
            });
        };
        $scope.mapOptions = function(){
            if( !$scope.variations_form ){
                $scope.variations_form = jQuery('.variations_form');
                $scope.variations_form_obj = {
                    calculator: null,
                    use_ajax: false,
                    swatches_xhr: null,
                    checked: false,
                    first: true
                };
                if( $scope.variations_form.length && $scope.variations_form.find('select.nbo-mapping-select').length ){
                    var getSelector = function( field_id ){
                        var field = $scope.get_field(field_id),
                        type = field.appearance.display_type,
                        selector = '';
                        if( angular.isDefined( field.nbd_type ) && field.nbd_type == 'shape' ){
                            type = 'ad';
                        }
                        switch( type ){
                            case 's':
                                selector = '> .nbd-swatch-wrap input[type="radio"]';
                                break;
                            case 'r':
                                selector = '> .__nbd-radio-wrap input[type="radio"]';
                                break;
                            case 'xl':
                                selector = '> .nbd-xlabel-wrapper input[type="radio"]';
                                break;
                            case 'ad':
                                selector = '> div > select option';
                                break;
                            case 'l':
                                selector = '> .nbd-label-wrap input[type="radio"]';
                                break;
                            default:
                                selector = '> .__nbd-dropdown-wrap select option';
                                break;
                        }
                        return selector;
                    };

                    var updateFieldStatus = function( current_options ){
                        var mustCheckValid = false;
                        $scope.variations_form.find('.variations select.nbo-mapping-select').each(function () {
                            var classList = jQuery(this).attr('class').split(/\s+/),
                            field_id, optionWrap;
                            jQuery.each(classList, function(index, _class) {
                                if ( _class.indexOf("nbo_field_id-") > -1 ) {
                                    var arr = _class.split("-");
                                    field_id = arr[1];
                                }
                            });
                            optionWrap = jQuery('.nbd-option-field[data-id="' + field_id + '"]');
                            var selector = getSelector( field_id );

                            var attribute_name = jQuery(this).data('attribute_name') || jQuery(this).attr('name'),
                            avaiable_options = current_options[attribute_name];

                            jQuery(this).find('option').each(function(index, el){
                                var val = jQuery(el).val();
                                if( index > 0 ){
                                    var option = optionWrap.find('.nbd-field-content ' + selector).eq( index - 1);
                                    if ( !avaiable_options[val] ) {
                                        option.addClass('nbo_map_disable').attr('disabled', 'disabled');
                                    }else{
                                        option.removeClass('nbo_map_disable').removeAttr('disabled');
                                    }
                                }
                            });
                        });
                        if( mustCheckValid ){
                            //$scope.check_valid();
                        }
                    };

                    var init = function(){
                        $scope.variations_form.find('.variations select.nbo-mapping-select').each(function () {
                            var classList = jQuery(this).attr('class').split(/\s+/),
                            val = jQuery(this).val(),
                            field_id, optionWrap;
                            jQuery.each(classList, function(index, _class) {
                                if ( _class.indexOf("nbo_field_id-") > -1 ) {
                                    var arr = _class.split("-");
                                    field_id = arr[1];
                                }
                            });
                            optionWrap = jQuery('.nbd-option-field[data-id="' + field_id + '"]');
                            var selector = getSelector( field_id );
                            if( optionWrap.length ){
                                jQuery(this).parents('tr').hide();
                                if( val != '' ){
                                    var index = jQuery(this).find("[value='" + val + "']").index();
                                    var option = optionWrap.find('.nbd-field-content ' + selector).eq( index - 1);
                                }else{
                                    option = optionWrap.find('.nbd-field-content ' + selector).eq(0);
                                }
                                if( option.attr('disabled') == 'disabled'){
                                    var enabledOption = optionWrap.find('.nbd-field-content ' + selector +':enabled').eq(0);
                                    if( enabledOption.length ){
                                        enabledIndex = enabledOption.val();
                                        $scope.nbd_fields[field_id].value = enabledIndex;
                                        $scope.updateMapOptions(field_id);
                                    }
                                }
                            }else{
                                jQuery(this).show();
                            }
                        });
                        $scope.check_valid();
                        $scope.variations_form_obj.first = false;
                    };

                    $scope.variations_form.on('bind_calculator', function(){
                        var $product_variations = $scope.variations_form.data('product_variations');
                        $scope.variations_form_obj.use_ajax = $product_variations === false;

                        if ( $scope.variations_form_obj.use_ajax && jQuery.fn.block ) {
                            $scope.variations_form.block({message: null, overlayCSS: {background: '#fff', opacity: 0.6}});
                        }

                        var attribute_keys = {};
                        $scope.variations_form.find('.variations select').each(function (index, el) {
                            var $current_attr_select = jQuery(el);
                            var current_attribute_name = $current_attr_select.data('attribute_name') || $current_attr_select.attr('name');
                            attribute_keys[current_attribute_name] = [];
                            var current_options = '';
                            current_options = $current_attr_select.find('option:gt(0)').get();
                            if (current_options.length) {
                                for (var i = 0; i < current_options.length; i++) {
                                    var option = current_options[i];
                                    attribute_keys[current_attribute_name].push(jQuery(option).val());
                                }
                            }
                        });

                        if ( $scope.variations_form_obj.use_ajax ) {
                            if ( $scope.variations_form_obj.swatches_xhr ) {
                                $scope.variations_form_obj.swatches_xhr.abort();
                            }

                            var data = {
                                product_id: $scope.product_id,
                                action: 'nbo_get_product_variations'
                            };

                            $scope.variations_form_obj.swatches_xhr = jQuery.ajax({
                                url: $scope.ajax_url,
                                type: 'POST',
                                data: data,
                                success: function (response) {
                                    $scope.variations_form_obj.calculator = new nbo_variation_calculator(attribute_keys, response.data, null, null);
                                    if( jQuery.fn.unblock ) {
                                        $scope.variations_form.unblock();
                                    }

                                    $scope.variations_form.trigger('woocommerce_variation_has_changed');
                                    if( $scope.variations_form_obj.first ){
                                        init();
                                    }
                                }
                            });
                        } else {
                            $scope.variations_form_obj.calculator = new nbo_variation_calculator(attribute_keys, $product_variations, null, null);
                        }

                        $scope.variations_form.trigger('woocommerce_variation_has_changed');

                        if ( !$scope.variations_form_obj.use_ajax ) {
                            if( $scope.variations_form_obj.first ){
                                init();
                            }
                        }
                    });

                    $scope.variations_form.on('reset_data', function(){
                        if ( $scope.variations_form_obj.calculator == null ) {
                            return;
                        }

                        var current_options = $scope.variations_form_obj.calculator.get_current();
                        if( !$scope.variations_form_obj.checked ){
                            updateFieldStatus( current_options );
                            $scope.variations_form_obj.checked = true;
                        }
                    })
                    .on('woocommerce_variation_has_changed', function () {
                        if ( $scope.variations_form_obj.calculator == null ) {
                            return;
                        }

                        $scope.variations_form.find('.variations select').each(function () {
                            var attribute_name = jQuery(this).data('attribute_name') || jQuery(this).attr('name');
                            $scope.variations_form_obj.calculator.set_selected(attribute_name, jQuery(this).val());
                        });

                        var current_options = $scope.variations_form_obj.calculator.get_current();
                        updateFieldStatus( current_options );

                        if ( $scope.variations_form_obj.use_ajax ) {
                            $scope.variations_form.find('.nbo-default-select').each(function (index, element) {
                                var $wc_select_box = jQuery(element);

                                var attribute_name = $wc_select_box.data('attribute_name') || $wc_select_box.attr('name');
                                var avaiable_options = current_options[attribute_name];

                                $wc_select_box.find('option:gt(0)').removeClass('attached');
                                $wc_select_box.find('option:gt(0)').removeClass('enabled');
                                $wc_select_box.find('option:gt(0)').removeAttr('disabled');

                                $wc_select_box.find('option:gt(0)').each(function (optindex, option_element) {
                                    if (!avaiable_options[jQuery(option_element).val()]) {
                                        jQuery(option_element).addClass('disabled', 'disabled');
                                    } else {
                                        jQuery(option_element).addClass('attached');
                                        jQuery(option_element).addClass('enabled');
                                    }
                                });

                                $wc_select_box.find('option:gt(0):not(.enabled)').attr('disabled', 'disabled');
                            });
                        }
                    });

                    $scope.variations_form.trigger('bind_calculator');
                    $scope.variations_form.on('reload_product_variations', function () {
                        $scope.variations_form.trigger('woocommerce_variation_has_changed');
                        $scope.variations_form.trigger('bind_calculator');
                        $scope.variations_form.trigger('woocommerce_variation_has_changed');
                    });

                    $scope.variations_form.trigger('check_variations');
                }
            }
        };
        $scope.updateMapOptions = function( field_id ){
            if( !$scope.variations_form ) return;
            $timeout(function(){
                var _class = "nbo_field_id-" + field_id,
                index = parseInt( $scope.nbd_fields[field_id].value );
                if( $scope.variations_form.find( 'select.' + _class ).length ){
                    $scope.variations_form.find( 'select.' + _class ).find('option').eq( index + 1 ).prop("selected", "selected").change();
                }
            });
        };
        $scope.upDateVaritionQty = function( variations ){
            jQuery.each(jQuery('.nbb-qty-field'), function(index, ip){
                jQuery(ip).val(variations[index].qty);
            });
        };
        $scope.reset_options = function(){
            <?php if($change_base == 'yes' && !($options['display_type'] == 3 && count($options['bulk_fields']))): ?>
            $scope.basePrice = $scope.validate_float($scope.price);
            if(this.type == 'variable'){
                var variation_id = jQuery('input[name="variation_id"], input.variation_id').val();
                $scope.basePrice = (variation_id != '' && variation_id != 0 ) ? $scope.validate_float($scope.variations[variation_id]) : $scope.validate_float($scope.basePrice);
            }
            <?php if( $in_design_editor && $nbd_qv_type == '2') : ?>
            var wrapEl = '#nbo-options-wrap';
            <?php else: ?>
            var wrapEl = '#product-' + $scope.product_id + ' .summary';
            <?php endif; ?>
            jQuery(wrapEl + ' .price .amount').html($scope.convert_to_wc_price( $scope.basePrice ));
            jQuery(wrapEl + ' .nbo-base-price-html').html(nbds_frontend.total);
            <?php endif; ?>
            $scope.init();
            <?php if( $options['quantity_enable'] == 'y' && !$is_sold_individually ): ?>
            $scope.quantity = $scope.validate_int("<?php echo $options['quantity_breaks'][0]['val']; ?>");
            <?php endif; ?>
            if( angular.isDefined( $scope.quantity ) ) $scope.change_quantity();
            jQuery(document).triggerHandler( 'reset_nbo_options' );
        };
        $scope.update_turnaround_matrix = function(){
            var need_update_quantity_break = true;
            angular.forEach( $scope.turnaround_quantity_breaks, function(_break, key){
                if( _break.val == $scope.quantity ) need_update_quantity_break = false;
            });
            if( need_update_quantity_break ){
                $scope.turnaround_quantity_breaks = [];
                angular.copy($scope.options.quantity_breaks, $scope.turnaround_quantity_breaks);
                var quantity_break  = $scope.get_quantity_break( $scope.quantity );
                var quantity_break_clone = {};
                angular.copy($scope.options.quantity_breaks[quantity_break.index], quantity_break_clone);
                quantity_break_clone.val = $scope.quantity;
                var position = quantity_break.oparator == 'lt' ? quantity_break.index : quantity_break.index + 1;
                $scope.turnaround_quantity_breaks.splice(position, 0, quantity_break_clone);
                $scope.init_turnaround_matrix( true );
                $scope.calc_turnaround_matrix();
                if( $scope.current_turnaround_position[0] != 0 ){
                    if( $scope.turnaround_matrix[position][ $scope.current_turnaround_position[1] ].show == false ){
                        var delivery_field = $scope.get_field( nbOption.delivery_field_id );
                        for (i = 0; i < delivery_field.general.attributes.options.length; i++) {
                            if( $scope.turnaround_matrix[position][ i ].show == true ){
                                $scope.nbd_fields[nbOption.delivery_field_id].value = '' + i;
                                $scope.current_turnaround_position[1] = i;
                                $scope.current_turnaround_position[0] = position;
                                $scope.check_valid();
                                $scope.turnaround_matrix[position][ i ].active = true;
                                break;
                            }
                        }
                    }else{
                        $scope.turnaround_matrix[position][ $scope.current_turnaround_position[1] ].active = true;
                        $scope.current_turnaround_position[0] = position;
                    }
                }
                $scope.change_quantity();
            }
        };
        $scope.init_turnaround_matrix = function( update_qty_breaks ){
            $scope.turnaround_matrix = [];
            if( angular.isUndefined( update_qty_breaks ) ){
                $scope.current_turnaround_position = [0, 0];
                $scope.turnaround_quantity_breaks = [];
                angular.copy($scope.options.quantity_breaks, $scope.turnaround_quantity_breaks);
            }
            var delivery_field = $scope.get_field( nbOption.delivery_field_id );
            angular.forEach( $scope.turnaround_quantity_breaks, function(_break, key){
                $scope.turnaround_matrix[key] = [];
                angular.forEach( delivery_field.general.attributes.options, function(op, okey){
                    var active = false;
                    if( angular.isUndefined( update_qty_breaks ) && angular.isDefined( $scope.form_values[ nbOption.delivery_field_id ] ) 
                            && $scope.form_values[ nbOption.delivery_field_id ] == okey && $scope._quantity == _break.val ){
                        active = true;
                    }
                    $scope.turnaround_matrix[key][okey] = {
                        qty: $scope.validate_int( _break.val ),
                        show: false,
                        active: active
                    };
                });
            });
        };
        $scope.change_delivery_date = function( qty_break_index, delivery_index ){
            $scope.quantity = $scope.validate_int( $scope.turnaround_quantity_breaks[ qty_break_index ].val );
            $scope.nbd_fields[nbOption.delivery_field_id].value = '' + delivery_index;
            var delivery_field = $scope.get_field( nbOption.delivery_field_id );
            angular.forEach( $scope.turnaround_quantity_breaks, function(_break, key){
                angular.forEach( delivery_field.general.attributes.options, function(op, okey){
                    $scope.turnaround_matrix[key][okey].active = false;
                });
            });
            $scope.turnaround_matrix[qty_break_index][delivery_index].active = true;
            $scope.custom_quantity = false;
            $scope.current_turnaround_position = [qty_break_index, delivery_index];
            $scope.change_quantity();
        };
        $scope.update_delivery_date = function(){
            var qty = $scope.validate_int( jQuery('input[name="quantity"]').val()),
                quantity_break  = $scope.get_quantity_break( qty ),
                position = quantity_break.index;
            if( angular.isDefined( $scope.current_turnaround_position[1] ) ){
                if( $scope.turnaround_matrix[ position ][ $scope.current_turnaround_position[1] ].show == false ){
                    $scope.turnaround_matrix[ $scope.current_turnaround_position[0] ][ $scope.current_turnaround_position[1] ].active = false;
                    var delivery_field = $scope.get_field( nbOption.delivery_field_id );
                    for (i = 0; i < delivery_field.general.attributes.options.length; i++) {
                        if( $scope.turnaround_matrix[position][ i ].show == true ){
                            $scope.nbd_fields[nbOption.delivery_field_id].value = '' + i;
                            $scope.current_turnaround_position[1] = i;
                            $scope.current_turnaround_position[0] = position;
                            $scope.turnaround_matrix[position][ i ].active = true;
                            break;
                        }
                    }
                }
            }
        };
        $scope.custom_qty = {
            enable: false,
            value: !!$scope.quantity ? $scope.quantity : 1
        };
        var debounce_change_quantity = _debounce(function(event){
            $scope.quantity = $scope.custom_qty.value;
            $scope.change_quantity();
        } , 300);
        $scope._change_quantity = function(){
            debounce_change_quantity();
        };
        $scope.disable_custom_qty = function(){
            $timeout(function(){
                $scope.custom_qty = {
                    enable: false,
                    value: $scope.quantity
                };
            });
        };
        $scope.change_quantity = function(){
            $timeout(function(){
                jQuery('input[name="quantity"]').val($scope.quantity).trigger( 'change.nbo' );
            });
        };
        $scope.select_all_variation = function( $event ){
            var el = angular.element($event.target),
            list = el.parents('table.nbo-bulk-variation').find('tbody input.nbo-bulk-checkbox'),
            check = el.prop('checked') ? true : false;
            jQuery.each(list, function(){
                jQuery(this).prop('checked', check);
            });
        };
        $scope.add_variaion = function( $event ){
            var el = angular.element($event.target),
            tb = el.parents('table.nbo-bulk-variation').find('tbody'),
            row = tb.find('tr').last().clone();
            tb.append(row);
            $scope.calculate_bulk_total_price();
        };
        $scope.delete_variaions = function( $event ){
            var el = angular.element($event.target),
            tb = el.parents('table.nbo-bulk-variation').find('tbody');
            jQuery.each(tb.find('input.nbo-bulk-checkbox:checked'), function(){
                if( tb.find('tr').length > 1 ) jQuery(this).parents('tr').remove();
            });
            el.parents('table.nbo-bulk-variation').find('input.nbo-bulk-checkbox').prop('checked', false);
            $scope.calculate_bulk_total_price();
        };
        $scope.init_price_matrix = function(){
            $scope.options.pm_num_col = 1;
            $scope.options.pm_num_row = 1;
            $scope.options.pm_hoz_field = [];
            $scope.options.pm_ver_field = [];
            $scope.options.pm_hoz.forEach(function(field, index){
                var field_index = $scope.options.matrix_type == 1 ? field : $scope.get_field_index( field );
                $scope.options.pm_num_col *= $scope.fields[field_index].general.attributes.options.length;
                var colspan = 1;
                $scope.options.pm_hoz.forEach(function(field, _index){
                    var field_index = $scope.options.matrix_type == 1 ? field : $scope.get_field_index( field );
                    if(_index > index) colspan *= $scope.fields[field_index].general.attributes.options.length;
                });
                $scope.options.pm_hoz_field.push({field_id: $scope.fields[field_index].id, colspan: colspan});
            });
            $scope.options.pm_ver.forEach(function(field, index){
                var field_index = $scope.options.matrix_type == 1 ? field : $scope.get_field_index( field );
                $scope.options.pm_num_row *= $scope.fields[field_index].general.attributes.options.length;
                var rowspan = 1;
                $scope.options.pm_ver.forEach(function(field, _index){
                    var field_index = $scope.options.matrix_type == 1 ? field : $scope.get_field_index( field );
                    if(_index > index) rowspan *= $scope.fields[field_index].general.attributes.options.length;
                });
                $scope.options.pm_ver_field.push({field_id: $scope.fields[field_index].id, rowspan: rowspan});
            });
            var i, j;
            $scope.options.price_matrix = [];
            for( i = 0; i < $scope.options.pm_num_row; i++ ){
                $scope.options.price_matrix[i] = [];
                for( j = 0; j < $scope.options.pm_num_col; j++ ){
                    var h_index = j;
                    $scope.options.price_matrix[i][j] = {
                        fields: {},
                        pm_fields: {},
                        discount_by_qty: 0,
                        total_price: 0,
                        class: '',
                        price: '?',
                        disable: false
                    };
                    $scope.options.pm_hoz_field.forEach(function(field, index){
                        var field_val = Math.floor(h_index / field.colspan);
                        var field_index = $scope.options.matrix_type == 1 ? $scope.options.pm_hoz[index] : $scope.get_field_index( field.field_id );
                        $scope.options.price_matrix[i][j].pm_fields[$scope.fields[field_index].id] = field_val;
                        $scope.options.price_matrix[i][j].fields[$scope.fields[field_index].id] = {};
                        $scope.options.price_matrix[i][j].fields[$scope.fields[field_index].id].value = field_val;
                        h_index = h_index % field.colspan;
                    });
                    var v_index = i;
                    $scope.options.pm_ver_field.forEach(function(field, index){
                        var field_val = Math.floor(v_index / field.rowspan);
                        var field_index = $scope.options.matrix_type == 1 ? $scope.options.pm_ver[index] : $scope.get_field_index( field.field_id );
                        $scope.options.price_matrix[i][j].pm_fields[$scope.fields[field_index].id] = field_val;
                        $scope.options.price_matrix[i][j].fields[$scope.fields[field_index].id] = {};
                        $scope.options.price_matrix[i][j].fields[$scope.fields[field_index].id].value = field_val;
                        v_index = v_index % field.rowspan;
                    });
                    if( $scope.form_values ){
                        var _check_class = true;
                        angular.forEach($scope.options.price_matrix[i][j].pm_fields, function(value, field_id){
                            if( value != $scope.form_values[field_id] ) _check_class = false;
                        });
                        if( _check_class ) $scope.options.price_matrix[i][j].class = 'selected';
                        if( $scope.form_values.length == 0 && i == 0 && j == 0 ){
                            $scope.options.price_matrix[i][j].class = 'selected'; 
                            $scope.options.pm_selected = [i, j];
                        }
                    }
                    if( $scope.options.matrix_type == 2 ){
                        var priceIndex = i * $scope.options.pm_num_col + j;
                        if( angular.isDefined( $scope.options.mpm_prices[priceIndex] ) && $scope.options.mpm_prices[priceIndex] == '' ){
                            $scope.options.price_matrix[i][j].disable = true;
                        }
                    }
                }
            }
        };
        $scope.calc_turnaround_matrix = function(){
            var basePrice = $scope.price;
            if(this.type == 'variable'){
                var variation_id = jQuery('input[name="variation_id"], input.variation_id').val();
                basePrice = (variation_id != '' && variation_id != 0 ) ? $scope.variations[variation_id] : basePrice;
            }
            var delivery_field = $scope.get_field( nbOption.delivery_field_id );
            basePrice = $scope.convert_wc_price_to_float( basePrice );
            if( $scope.has_price_matrix && $scope.options.matrix_type == 2 ){
                var pmi = angular.isDefined( $scope.options.pm_selected ) ? $scope.options.pm_selected[0] : 0,
                pmj = angular.isDefined( $scope.options.pm_selected ) ? $scope.options.pm_selected[1] : 0;
                basePrice = $scope.get_mpm_base_price( pmi, pmj );
            }
            angular.forEach( $scope.turnaround_quantity_breaks, function(_break, key){
                angular.forEach( delivery_field.general.attributes.options, function(op, okey){
                    var nbd_fields  = {},
                    qty             = $scope.validate_int( _break.val ),
                    total_price     = 0,
                    discount_by_qty = 0,
                    xfactor         = 1,
                    quantity_break  = $scope.get_quantity_break( qty ),
                    cart_item_fee   = {enable: false},
                    line_price      = {
                        fixed: 0,
                        percent: 0,
                        xfactor: 1
                    }, 
                    fixed_amount = 0;
                    angular.copy($scope.nbd_fields, nbd_fields);
                    nbd_fields[ nbOption.delivery_field_id ].value = okey;
                    angular.forEach(nbd_fields, function(field, field_id){
                        if( field.enable ){
                            if( $scope.has_price_matrix && $scope.options.matrix_type == 2 ){
                                if( $scope.options.pm_hoz.indexOf( field_id ) > -1 || $scope.options.pm_ver.indexOf( field_id ) > -1 ) return;
                            }

                            var origin_field = $scope.get_field(field_id);
                            var factor = null;
                            if( origin_field.general.data_type == 'i' ){
                                if(origin_field.general.depend_quantity == 'n'){
                                    if( origin_field.general.price_type == 'mf' ){
                                        factor = $scope.eval_price( origin_field.general.price, origin_field, qty, nbd_fields );
                                    }else{
                                        factor = origin_field.general.price;
                                    }
                                }else{
                                    if( origin_field.general.price_type == 'mf' ){
                                        factor = $scope.eval_price( origin_field.general.price_breaks[quantity_break.index], origin_field, qty, nbd_fields );
                                    }else{
                                        factor = origin_field.general.price_breaks[quantity_break.index];
                                    }
                                }
                                if( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'dimension' 
                                        && origin_field.general.mesure == 'y' && angular.isDefined(origin_field.general.mesure_range) && origin_field.general.mesure_range.length > 0 ){
                                    factor = $scope.calculate_price_base_measurement(origin_field, field.width, field.height, qty);
                                    if( (origin_field.general.price_type == 'f' || origin_field.general.price_type == 'c')
                                            && origin_field.general.mesure_base_pages == 'y' ){
                                        if( angular.isDefined(nbOption.odOption.page) ){
                                            var _origin_field = $scope.get_field(nbOption.odOption.page.field_id);
                                            if( _origin_field.general.data_type == 'i' ){
                                                factor *= Math.floor( (nbOption.odOption.page.number + 1) / 2 );
                                            }
                                        }
                                    }
                                }
                                if( origin_field.general.input_type == 'u' && ( angular.isUndefined(field.value) || field.value == "" ) ){
                                    factor = 0;
                                }
                                if( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'page1' 
                                        && angular.isDefined(origin_field.general.price_depend_no) && origin_field.general.price_depend_no == 'y' 
                                        && angular.isDefined(origin_field.general.price_no_range) && origin_field.general.price_no_range.length > 0 ){
                                    if( angular.isUndefined( origin_field.general.input_option.default ) || origin_field.general.input_option.default == '' ){
                                        origin_field.general.input_option.default = 0;
                                    }
                                    var current_value = $scope.validate_int( field.value ),
                                    default_val = $scope.validate_int( origin_field.general.input_option.default );
                                    current_value = current_value - default_val;
                                    if( current_value < 0 ) current_value = 0;
                                    var price_no_range = origin_field.general.price_no_range,
                                    len = price_no_range.length;
                                    angular.forEach(price_no_range, function(range, key){
                                        var qty = parseInt( range[0] );
                                        if( current_value >= qty ){
                                            factor = range[1];
                                        }
                                    });
                                }
                            }else{
                                var option = origin_field.general.attributes.options[field.value];
                                if(option){
                                    var option_price =  option.price;
                                    if(origin_field.general.depend_quantity == 'n'){
                                        if( origin_field.general.price_type == 'mf' ){
                                            factor = $scope.eval_price( option_price[0], origin_field, qty, nbd_fields );
                                        }else{
                                            factor = $scope.validate_float( option_price[0] );
                                        }
                                    }else{
                                        if( origin_field.general.price_type == 'mf' ){
                                            factor = $scope.eval_price( option_price[quantity_break.index], origin_field, qty, nbd_fields );
                                        }else{
                                            factor = $scope.validate_float( option_price[quantity_break.index] );
                                        }
                                    }
                                    if( angular.isDefined(option.enable_subattr) && option.enable_subattr == 'on' ){
                                        if(angular.isDefined(option.sub_attributes) && option.sub_attributes.length > 0){
                                            soption_price = option.sub_attributes[field.sub_value].price;
                                            if(origin_field.general.depend_quantity == 'n'){
                                                if( origin_field.general.price_type == 'mf' ){
                                                    factor += $scope.eval_price( soption_price[0], origin_field, qty, nbd_fields );
                                                }else{
                                                    factor += $scope.validate_float( soption_price[0] );
                                                }
                                            }else{
                                                if( origin_field.general.price_type == 'mf' ){
                                                    factor += $scope.eval_price( soption_price[quantity_break.index], origin_field, qty, nbd_fields );
                                                }else{
                                                    factor += $scope.validate_float( soption_price[quantity_break.index] );
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            if( $scope.isMultipleSelectPage( origin_field ) ){
                                factor = [];
                                angular.forEach(field.values, function(val, v_index){
                                    var option = origin_field.general.attributes.options[val];
                                    if(origin_field.general.depend_quantity == 'n'){
                                        if( origin_field.general.price_type == 'mf' ){
                                            factor[v_index] = $scope.eval_price( option.price[0], origin_field, qty, nbd_fields );
                                        }else{
                                            factor[v_index] = option.price[0];
                                        }
                                    }else{
                                        if( origin_field.general.price_type == 'mf' ){
                                            factor[v_index] = $scope.eval_price( option.price[quantity_break.index], origin_field, qty, nbd_fields );
                                        }else{
                                            factor[v_index] = option.price[quantity_break.index];
                                        }
                                    }
                                });
                                field.price = 0;
                                var xfac = 0, _xfac = 0;
                                angular.forEach(factor, function(fac){
                                    fac = $scope.validate_float(fac);
                                    var _fac = fac;
                                    if( $scope.is_independent_qty( origin_field ) ){
                                        fac = 0;
                                        field.ind_qty = true;
                                    }
                                    if( $scope.is_fixed_amount( origin_field ) ){
                                        fac /= qty;
                                    }
                                    switch(origin_field.general.price_type){
                                        case 'f':
                                        case 'mf':
                                            field.price          += _fac;
                                            total_price += fac;
                                            if( $scope.is_independent_qty( origin_field ) ){
                                                line_price.fixed += _fac;
                                            }
                                            break;
                                        case 'p':
                                            field.price          += basePrice * _fac / 100;
                                            total_price          += basePrice * fac / 100;
                                            if( $scope.is_independent_qty( origin_field ) ){
                                                line_price.percent += _fac;
                                            }
                                            break;
                                        case 'p+':
                                            field.price          += fac / 100;
                                            field._price         += _fac / 100;
                                            xfac                 += fac / 100;
                                            _xfac                += _fac / 100;
                                            field.is_pp          = 1;
                                            break;
                                    }
                                });
                                if( $scope.is_fixed_amount( origin_field ) ){
                                    field.fixed_amount = true;
                                }
                                field.price_val = field.price * 1;
                                field.price = $scope.convert_to_wc_price( field.price );
                                if(origin_field.general.price_type == 'p+'){
                                    xfactor *= (1 + xfac / 100);
                                    if( $scope.is_independent_qty( origin_field ) ){
                                        line_price.xfactor *= (1 + _xfac / 100);
                                    }
                                }
                            }else{
                                factor = $scope.validate_float(factor) ;
                                field.is_pp = 0;
                                if( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'dimension' 
                                        && origin_field.general.price_type == 'c' ){
                                    origin_field.general.price_type = 'f';
                                }
                                var _factor = factor;
                                if( $scope.is_independent_qty( origin_field ) ){
                                    factor = 0;
                                    field.ind_qty = true;
                                }
                                if( $scope.is_fixed_amount( origin_field ) ){
                                    factor /= qty;
                                }
                                switch(origin_field.general.price_type){
                                    case 'f':
                                    case 'mf':
                                        field.price_val = _factor;
                                        field.price = $scope.convert_to_wc_price( _factor );
                                        total_price += factor;
                                        if( $scope.is_independent_qty( origin_field ) ){
                                            line_price.fixed += _factor;
                                        }
                                        break;
                                    case 'p':
                                        field.price_val = basePrice * _factor / 100;
                                        field.price = $scope.convert_to_wc_price( basePrice * _factor / 100 );
                                        total_price += (basePrice * factor / 100);
                                        if( $scope.is_independent_qty( origin_field ) ){
                                            line_price.percent += _factor;
                                        }
                                        break;
                                    case 'p+':
                                        field.price = factor / 100;
                                        field._price = _factor / 100;
                                        xfactor *= (1 + factor / 100);
                                        field.is_pp = 1;
                                        if( $scope.is_independent_qty( origin_field ) ){
                                            line_price.xfactor *= (1 + _factor / 100);
                                        }
                                        break;
                                    case 'c':
                                        var current_value = $scope.validate_int( field.value );
                                        if( ( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'page' && origin_field.general.data_type == 'i' ) ||
                                            ( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'page1' ) || ( angular.isDefined(origin_field.nbe_type) && origin_field.nbe_type == 'number_file' ) ){
                                            if( angular.isUndefined( origin_field.general.input_option.default ) || origin_field.general.input_option.default == '' ){
                                                origin_field.general.input_option.default = 0;
                                            }
                                            var default_val = $scope.validate_int( origin_field.general.input_option.default );
                                            current_value = current_value - default_val;
                                            if( current_value < 0 ) current_value = 0;
                                        }
                                        field.price_val = _factor * current_value;
                                        field.price = $scope.convert_to_wc_price( field.price_val );
                                        total_price += factor * current_value;
                                        if( $scope.is_independent_qty( origin_field ) ){
                                            line_price.fixed += field.price_val;
                                        }
                                        break; 
                                    case 'cp':
                                        field.price_val = _factor * $scope.validate_int( field.value.length );
                                        field.price = $scope.convert_to_wc_price( field.price_val );
                                        total_price += factor * $scope.validate_int( field.value.length );
                                        if( $scope.is_independent_qty( origin_field ) ){
                                            line_price.fixed += field.price_val;
                                        }
                                        break;
                                }
                                if( $scope.is_fixed_amount( origin_field ) ){
                                    field.fixed_amount = true;
                                }
                            }
                        }
                    });
                    total_price += ( ( basePrice + total_price ) * ( xfactor - 1 ) );
                    angular.forEach(nbd_fields, function(field){
                        if( field.is_pp == 1 ){
                            field.price_val = field.price * (basePrice + total_price ) / ( field.price + 1 );
                            field.price = $scope.convert_to_wc_price( field.price_val );
                        }
                    });
                    var qty_factor = $scope.validate_float( _break.dis );
                    discount_by_qty = $scope.options.quantity_discount_type == 'f' ? qty_factor : (basePrice + total_price ) * qty_factor / 100;
                    var final_price = total_price + basePrice - discount_by_qty;
                    final_price = final_price > 0 ? final_price : 0;
                    total_cart_price = final_price * qty;
                    var _total_cart_price = total_cart_price;
                    if( line_price.fixed != 0 || line_price.xfactor != 1 || line_price.percent != 0 ){
                        if( line_price.fixed != 0 ){
                            total_cart_price += line_price.fixed;
                        }
                        if( line_price.percent != 0 ){
                            total_cart_price += (basePrice * line_price.percent / 100);
                        }
                        if( line_price.xfactor != 1 ){
                            total_cart_price += ( total_cart_price * ( line_price.xfactor - 1 ) );
                            angular.forEach(nbd_fields, function(field){
                                if( field.is_pp == 1 && field.ind_qty ){
                                    field.price = $scope.convert_to_wc_price( field._price * total_cart_price / ( field._price + 1 ) );
                                }
                            });
                        }
                        cart_item_fee.value = total_cart_price - _total_cart_price;
                        if( cart_item_fee.value > 0 ){
                            cart_item_fee.enable = true;
                        }
                        cart_item_fee.value = $scope.convert_to_wc_price( cart_item_fee.value );
                    }
                    if( angular.isUndefined( $scope.turnaround_matrix[key] ) ) $scope.turnaround_matrix[key] = [];
                    var max_qty = $scope.validate_int( op['max_qty'] ),
                    show = false;
                    if( op['max_qty'] == '' || max_qty >= qty ) show = true;
                    $scope.turnaround_matrix[key][okey].show = show;
                    $scope.turnaround_matrix[key][okey].total_cart_price = $scope.convert_to_wc_price( total_cart_price );
                    $scope.turnaround_matrix[key][okey].final_price = $scope.convert_to_wc_price( final_price, true );
                });
            });
        };
        $scope.calculate_price_matrix = function(){
            var i, j;
            var basePrice = $scope.price;
            if($scope.type == 'variable'){
                var variation_id = jQuery('input[name="variation_id"], input.variation_id').val();
                basePrice = (variation_id != '' && variation_id != 0 ) ? $scope.variations[variation_id] : basePrice;
            }
            basePrice = $scope.convert_wc_price_to_float( basePrice );

            var calculate_price = function( _fields, basePrice ){
                var total_price = 0,
                discount_by_qty = 0,
                qty = 0,
                cart_item_fee  = 0;
                if( $scope.is_sold_individually == 1 ){
                    qty = 1;
                }else{
                    qty = $scope.validate_int(jQuery('input[name="quantity"]').val());
                }
                var quantity_break = $scope.get_quantity_break(qty);
                var xfactor = 1,
                line_price  = {
                    fixed: 0,
                    percent: 0,
                    xfactor: 1
                },
                fixed_amount = 0;
                angular.forEach(_fields, function(field, field_id){
                    if( $scope.has_price_matrix && $scope.options.matrix_type == 2 ){
                        if( $scope.options.pm_hoz.indexOf( field_id ) > -1 || $scope.options.pm_ver.indexOf( field_id ) > -1 ) return;
                    }

                    if( field.enable ){
                        var origin_field = $scope.get_field( field_id );
                        var factor = null;
                        if( origin_field.general.data_type == 'i' ){
                            if(origin_field.general.depend_quantity == 'n'){
                                if( origin_field.general.price_type == 'mf' ){
                                    factor = $scope.eval_price( origin_field.general.price, origin_field, qty, _fields );
                                }else{
                                    factor = origin_field.general.price;
                                }
                            }else{
                                if( origin_field.general.price_type == 'mf' ){
                                    factor = $scope.eval_price( origin_field.general.price_breaks[quantity_break.index], origin_field, qty, _fields );
                                }else{
                                    factor = origin_field.general.price_breaks[quantity_break.index];
                                }
                            }

                            if( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'dimension' 
                                && origin_field.general.mesure == 'y' && angular.isDefined(origin_field.general.mesure_range) && origin_field.general.mesure_range.length > 0 ){
                                factor = $scope.calculate_price_base_measurement(origin_field, field.width, field.height, qty);
                                if( (origin_field.general.price_type == 'f' || origin_field.general.price_type == 'c')
                                        && origin_field.general.mesure_base_pages == 'y' ){
                                    if( angular.isDefined(nbOption.odOption.page) ){
                                        var _origin_field = $scope.get_field(nbOption.odOption.page.field_id);
                                        if( _origin_field.general.data_type == 'i' ){
                                            factor *= Math.floor( (nbOption.odOption.page.number + 1) / 2 );
                                        }
                                    }
                                }
                            }

                            if( origin_field.general.input_type == 'u' && ( angular.isUndefined(field.value) || field.value == "" ) ){
                                factor = 0;
                            }

                            if( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'page1' 
                                    && angular.isDefined(origin_field.general.price_depend_no) && origin_field.general.price_depend_no == 'y' 
                                    && angular.isDefined(origin_field.general.price_no_range) && origin_field.general.price_no_range.length > 0 ){
                                if( angular.isUndefined( origin_field.general.input_option.default ) || origin_field.general.input_option.default == '' ){
                                    origin_field.general.input_option.default = 0;
                                }
                                var current_value = $scope.validate_int( field.value ),
                                default_val = $scope.validate_int( origin_field.general.input_option.default );
                                current_value = current_value - default_val;
                                if( current_value < 0 ) current_value = 0;
                                var price_no_range = origin_field.general.price_no_range,
                                len = price_no_range.length;
                                angular.forEach(price_no_range, function(range, key){
                                    var qty = parseInt( range[0] );
                                    if( current_value >= qty ){
                                        factor = range[1];
                                    }
                                });
                            }
                        }else{
                            var option = origin_field.general.attributes.options[field.value];
                            if(option){
                                if(origin_field.general.depend_quantity == 'n'){
                                    if( origin_field.general.price_type == 'mf' ){
                                        factor = $scope.eval_price( option.price[0], origin_field, qty, _fields );
                                    }else{
                                        factor = $scope.validate_float( option.price[0] );
                                    }
                                }else{
                                    if( origin_field.general.price_type == 'mf' ){
                                        factor = $scope.eval_price( option.price[quantity_break.index], origin_field, qty, _fields );
                                    }else{
                                        factor = $scope.validate_float( option.price[quantity_break.index] );
                                    }
                                }
                                if( angular.isDefined(option.enable_subattr) && option.enable_subattr == 'on' ){
                                    if(angular.isDefined(option.sub_attributes) && option.sub_attributes.length > 0){
                                        soption_price = option.sub_attributes[field.sub_value].price;
                                        if(origin_field.general.depend_quantity == 'n'){
                                            if( origin_field.general.price_type == 'mf' ){
                                                factor += $scope.eval_price( soption_price[0], origin_field, qty, _fields );
                                            }else{
                                                factor += $scope.validate_float( soption_price[0] );
                                            }
                                        }else{
                                            if( origin_field.general.price_type == 'mf' ){
                                                factor += $scope.eval_price( soption_price[quantity_break.index], origin_field, qty, _fields );
                                            }else{
                                                factor += $scope.validate_float( soption_price[quantity_break.index] );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if( $scope.isMultipleSelectPage( origin_field ) ){
                            factor = [];
                            angular.forEach(field.values, function(val, v_index){
                                var option = origin_field.general.attributes.options[val];
                                if(origin_field.general.depend_quantity == 'n'){
                                    if( origin_field.general.price_type == 'mf' ){
                                        factor[v_index] = $scope.eval_price( option.price[0], origin_field, qty, _fields );
                                    }else{
                                        factor[v_index] = option.price[0];
                                    }
                                }else{
                                    if( origin_field.general.price_type == 'mf' ){
                                        factor[v_index] = $scope.eval_price( option.price[quantity_break.index], origin_field, qty, _fields );
                                    }else{
                                        factor[v_index] = option.price[quantity_break.index];
                                    }
                                }
                            });
                            field.price = 0;
                            var xfac = 0, _xfac = 0;
                            angular.forEach(factor, function(fac){
                                fac = $scope.validate_float(fac);
                                var _fac = fac;
                                if( $scope.is_independent_qty( origin_field ) ){
                                    fac = 0;
                                    field.ind_qty = true;
                                }
                                switch(origin_field.general.price_type){
                                    case 'f':
                                    case 'mf':
                                        field.price += _fac;
                                        if( ! $scope.is_fixed_amount( origin_field ) ) total_price += fac;
                                        if( $scope.is_independent_qty( origin_field ) ){
                                            line_price.fixed += _fac;
                                        }
                                        break;
                                    case 'p':
                                        field.price += $scope.basePrice * _fac / 100;
                                        total_price += $scope.basePrice * fac / 100;
                                        if( $scope.is_independent_qty( origin_field ) ){
                                            line_price.percent += _fac;
                                        }
                                        break;
                                    case 'p+':
                                        field.price += fac / 100;
                                        field._price += _fac / 100;
                                        xfac += fac / 100;
                                        _xfac += _fac / 100;
                                        field.is_pp = 1;
                                        break;
                                }
                            });
                            if( $scope.is_fixed_amount( origin_field ) ){
                                fixed_amount += field.price;
                                field.fixed_amount = true;
                            }
                            field.price_val = field.price * 1;
                            field.price = $scope.convert_to_wc_price( field.price ); 
                            if(origin_field.general.price_type == 'p+'){
                                xfactor *= (1 + xfac / 100);
                                if( $scope.is_independent_qty( origin_field ) ){
                                    line_price.xfactor *= (1 + _xfac / 100);
                                }
                            }
                        }else{
                            factor = $scope.validate_float(factor);
                            field.is_pp = 0;
                            var _factor = factor;
                            if( $scope.is_independent_qty( origin_field ) ){
                                factor = 0;
                            }
                            switch(origin_field.general.price_type){
                                case 'f':
                                case 'mf':
                                    field.price_val = _factor;
                                    field.price = $scope.convert_to_wc_price( _factor );
                                    if( ! $scope.is_fixed_amount( origin_field ) ) total_price += factor;
                                    if( $scope.is_independent_qty( origin_field ) ){
                                        line_price.fixed += _factor;
                                    }
                                    break;
                                case 'p':
                                    field.price_val = basePrice * _factor / 100;
                                    field.price = $scope.convert_to_wc_price( field.price_val );
                                    total_price += ($scope.basePrice * factor / 100);
                                    if( $scope.is_independent_qty( origin_field ) ){
                                        line_price.percent += _factor;
                                    }
                                    break;
                                case 'p+':
                                    field.price = factor / 100;
                                    field._price = _factor / 100;
                                    xfactor *= (1 + factor / 100);
                                    field.is_pp = 1;
                                    if( $scope.is_independent_qty( origin_field ) ){
                                        line_price.xfactor *= (1 + _factor / 100);
                                    }
                                    break;
                                case 'c':
                                    var current_value = $scope.validate_int( field.value );
                                    if( ( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'page' && origin_field.general.data_type == 'i' ) ||
                                        ( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'page1' ) || ( angular.isDefined(origin_field.nbe_type) && origin_field.nbe_type == 'number_file' ) ){
                                        if( angular.isUndefined( origin_field.general.input_option.default ) || origin_field.general.input_option.default == '' ){
                                            origin_field.general.input_option.default = 0;
                                        }
                                        var default_val = $scope.validate_int( origin_field.general.input_option.default );
                                        current_value = current_value - default_val;
                                        if( current_value < 0 ) current_value = 0;
                                    }
                                    field.price_val = _factor * current_value;
                                    field.price = $scope.convert_to_wc_price( field.price_val );
                                    total_price += factor * current_value;
                                    if( $scope.is_independent_qty( origin_field ) ){
                                        line_price.fixed += field.price_val;
                                    }
                                    break;
                                case 'cp':
                                    field.price_val = _factor * $scope.validate_int( field.value.length );
                                    field.price = $scope.convert_to_wc_price( field.price_val );
                                    total_price += factor * $scope.validate_int( field.value.length );
                                    if( $scope.is_independent_qty( origin_field ) ){
                                        line_price.fixed += field.price_val;
                                    }
                                    break;
                            }
                            if( $scope.is_fixed_amount( origin_field ) ){
                                fixed_amount += factor;
                                field.fixed_amount = true;
                            }
                        }
                    }
                });
                total_price += ( (basePrice + total_price ) * (xfactor - 1 ) );
                angular.forEach(_fields, function(field){
                    if( field.is_pp == 1 ) {
                        field.price_val = field.price * (basePrice + total_price ) / ( field.price + 1 );
                        field.price = $scope.convert_to_wc_price( field.price_val );
                    }
                });
                var qty_factor = null;
                if( quantity_break.index == 0 && quantity_break.oparator == 'lt' ){
                    qty_factor = '';
                }else{
                    qty_factor = $scope.options.quantity_breaks[quantity_break.index].dis;
                }
                qty_factor = $scope.validate_float( qty_factor );
                discount_by_qty = $scope.options.quantity_discount_type == 'f' ? qty_factor : (basePrice + total_price ) * qty_factor / 100;
                var final_price = basePrice + total_price - discount_by_qty;
                final_price = final_price > 0 ? final_price : 0;
                var total_cart_price = final_price * qty;
                if( $scope.options.quantity_discount_type == 'f' ){
                    total_cart_price += fixed_amount;
                }else{
                    total_cart_price += fixed_amount * ( 100 - qty_factor ) / 100;
                }
                if( line_price.fixed != 0 || line_price.xfactor != 1 || line_price.percent != 0 ){
                    var _total_cart_price = total_cart_price;
                    if( line_price.fixed != 0 ){
                        total_cart_price += line_price.fixed;
                    }
                    if( line_price.percent != 0 ){
                        total_cart_price += (basePrice * line_price.percent / 100);
                    }
                    if( line_price.xfactor != 1 ){
                        total_cart_price += ( total_cart_price * ( line_price.xfactor - 1 ) );
                    }
                    cart_item_fee = total_cart_price - _total_cart_price;
                    return final_price + cart_item_fee;
                }
                return final_price;
            };  
            var check_depend = function( field_id, pm_fields ){
                var field = $scope.get_field(field_id),
                check = [];
                pm_fields[field_id].enable = true;
                if( field.conditional.enable == 'n' ) return true;
                if( angular.isUndefined(field.conditional.depend) ) return true;
                if( field.conditional.depend.length == 0 ) return true;
                var show = field.conditional.show,
                logic = field.conditional.logic,
                total_check = logic == 'a' ? true : false;
                angular.forEach(field.conditional.depend, function(con, key){
                    if( con.id != '' ){
                        if( con.id == 'qty' ){
                            var qty = $scope.validate_int( jQuery('input[name="quantity"]').val() );
                            if( $scope.is_sold_individually == 1 ){
                                qty = 1;
                            }
                            con.val = con.val * 1;
                        }
                        switch(con.operator){
                            case 'i':
                                check[key] = pm_fields[con.id].value == con.val ? true : false;
                                break;
                            case 'n':
                                check[key] = pm_fields[con.id].value != con.val ? true : false;
                                break;  
                            case 'e':
                                check[key] = pm_fields[con.id].value == '' ? true : false;
                                break;
                            case 'ne':
                                check[key] = pm_fields[con.id].value != '' ? true : false;
                                break;  
                            case 'eq':
                                check[key] = qty == con.val ? true : false;
                                break;
                            case 'gt':
                                check[key] = qty > con.val ? true : false;
                                break;
                            case 'lt':
                                check[key] = qty < con.val ? true : false;
                                break;
                        }
                    }else{
                        check[key] = true;
                    }
                });
                angular.forEach(check, function(c){
                    total_check = logic == 'a' ? (total_check && c) : (total_check || c);
                });
                pm_fields[field_id].enable = show == 'y' ? total_check : !total_check;
            };
            for( i = 0; i < $scope.options.pm_num_row; i++ ){
                for( j = 0; j < $scope.options.pm_num_col; j++ ){
                    angular.forEach($scope.nbd_fields, function(field, field_id){
                        var val = field.value;
                        if( angular.isDefined($scope.options.price_matrix[i][j].pm_fields[field_id]) ){
                            val = $scope.options.price_matrix[i][j].pm_fields[field_id];
                        }else{
                            $scope.options.price_matrix[i][j].fields[field_id] = {};
                        }
                        angular.copy(field, $scope.options.price_matrix[i][j].fields[field_id]);
                        if( val !== null && angular.isDefined(val) ){
                            $scope.options.price_matrix[i][j].fields[field_id].value = '' + val;
                        }else{
                            $scope.options.price_matrix[i][j].fields[field_id].value = val;
                        }
                    });
                    angular.forEach($scope.options.price_matrix[i][j].fields, function(field, field_id){
                        check_depend(field_id, $scope.options.price_matrix[i][j].fields);
                    });
                    basePrice = $scope.options.matrix_type == 1 ? basePrice : $scope.get_mpm_base_price( i, j );
                    var total_price = calculate_price( $scope.options.price_matrix[i][j].fields, basePrice );
                    $scope.options.price_matrix[i][j].price = $scope.convert_to_wc_price( total_price );
                }
            }
        };
        $scope.select_price_matrix = function(_i, _j){
            var i, j;
            for( i = 0; i < $scope.options.pm_num_row; i++ ){
                for( j = 0; j < $scope.options.pm_num_col; j++ ){
                    $scope.options.price_matrix[i][j].class = '';
                }
            }
            $scope.options.price_matrix[_i][_j].class = 'selected';
            angular.copy($scope.options.price_matrix[_i][_j].fields, $scope.nbd_fields);
            $scope.options.pm_selected = [_i, _j];
            $scope.check_valid( false );
        };
        $scope.get_mpm_base_price = function(i, j){
            var index = i * $scope.options.pm_num_col + j;
            if( angular.isDefined( $scope.options.mpm_prices[index] ) ) return $scope.convert_wc_price_to_float( $scope.options.mpm_prices[index] );
            return 0;
        };
        $scope.convert_to_wc_price = function(price, required){
            <?php if( $hide_zero_price == 'yes' ): ?> 
            var precision = parseInt(nbds_frontend.wc_currency_format_num_decimals);
            if( price.toFixed(precision) == 0 && angular.isUndefined(required) ) return '';
            <?php endif; ?>
            return accounting.formatMoney( price, {
                symbol: nbds_frontend.currency_format_symbol,
                decimal: nbds_frontend.currency_format_decimal_sep,
                thousand: nbds_frontend.currency_format_thousand_sep,
                precision: angular.isUndefined( required ) ? nbds_frontend.wc_currency_format_num_decimals : nbds_frontend.currency_format_num_decimals,
                format: nbds_frontend.currency_format
            });
        };
        $scope.convert_wc_price_to_float = function(price){ return $scope.validate_float(price);
            var c = jQuery.trim(nbds_frontend.currency_format_thousand_sep).toString(), 
                d = jQuery.trim(nbds_frontend.currency_format_decimal_sep).toString();
            return price = price.replace(/ /g, ""), price = "." === c ? price.replace(/\./g, "") : price.replace(new RegExp(c,"g"), ""), price = price.replace(d, "."), price = parseFloat(price);            
        };
        $scope.validate_int = function(input){
            var output = parseInt(input);
            if( isNaN(output) ) output = 0;
            if( output < 0 ) output = 0;
            return output;
        };
        $scope.shorten = function(num) {
            num += '';
            num = num.replace(/(\.\d*?)0{5,}\d+$/, '$1');
            if( /(\.\d*?)9{5,}\d+$/.test( num ) ){
                var tem = num.replace(/(\.\d*?)9{5,}\d+$/, '$1');
                var decimals = tem.slice(tem.indexOf('.')+1),
                num_decimal = decimals.length;
                if( num_decimal > 0 ){
                    var new_decimals = decimals * 1;
                    new_decimals    += 1;
                    tem = tem.replace(/(\d+\.)(\d+)/, '$1' + new_decimals);
                } else if( (/\d+\.$/).test( tem ) ) {
                    tem = ( tem.replace("\.", "") * 1 ) + 1;
                }
                return tem.replace(/(\.\d*?)0{5,}\d+$/, '$1') * 1;
            }
            return num * 1;
        };
        $scope.validate_float = function(input){
            var output = parseFloat(input);
            if( isNaN(output) ) output = 0;
            return output;
        };
        $scope.get_quantity_break = function( qty ){
            var quantity_break = {index: 0, oparator: 'gt'};
            var quantity_breaks = [];
            angular.forEach($scope.options.quantity_breaks, function(_break, key){
                quantity_breaks[key] = $scope.validate_int(_break.val);
            });
            angular.forEach(quantity_breaks, function(_break, key){
                if( key == 0 && qty < _break){
                    quantity_break = {index: 0, oparator: 'lt'};
                }
                if( qty >= _break && key < ( quantity_breaks.length - 1 ) ){
                    quantity_break = {index: key, oparator: 'bw'};
                }
                if( key == ( quantity_breaks.length - 1 ) && qty >= _break){
                    quantity_break = {index: key, oparator: 'gt'};
                }
            });
            return quantity_break;
        };
        $scope.calculate_price = function(){
            $scope.basePrice = $scope.price;
            if(this.type == 'variable'){
                var variation_id = jQuery('input[name="variation_id"], input.variation_id').val();
                $scope.basePrice = (variation_id != '' && variation_id != 0 ) ? $scope.variations[variation_id] : $scope.basePrice;
            }
            $scope.basePrice        = $scope.convert_wc_price_to_float( $scope.basePrice );
            if( $scope.has_price_matrix && $scope.options.matrix_type == 2 ){
                var pmi = angular.isDefined( $scope.options.pm_selected ) ? $scope.options.pm_selected[0] : 0,
                pmj = angular.isDefined( $scope.options.pm_selected ) ? $scope.options.pm_selected[1] : 0;
                $scope.basePrice = $scope.get_mpm_base_price( pmi, pmj );
            }
            $scope.total_price      = 0;
            $scope.discount_by_qty  = 0;
            $scope.cart_item_fee  = {
                enable: false,
                value: 0
            };
            var qty = 0; 
            if( $scope.is_sold_individually == 1 ){
                qty = 1;
            }else{
                qty = $scope.validate_int(jQuery('input[name="quantity"]').val());
            }
            $scope._qty = qty;
            var quantity_break  = $scope.get_quantity_break(qty);
            var xfactor         = 1,
                line_price      = {
                    fixed: 0,
                    percent: 0,
                    xfactor: 1
                }, fixed_amount = 0;
            angular.forEach($scope.nbd_fields, function(field, field_id){
                if( field.enable ){
                    if( $scope.has_price_matrix && $scope.options.matrix_type == 2 ){
                        if( $scope.options.pm_hoz.indexOf( field_id ) > -1 || $scope.options.pm_ver.indexOf( field_id ) > -1 ) return;
                    }

                    var origin_field = $scope.get_field(field_id);
                    var factor = null;
                    if( origin_field.general.data_type == 'i' ){
                        if(origin_field.general.depend_quantity == 'n'){
                            if( origin_field.general.price_type == 'mf' ){
                                factor = $scope.eval_price( origin_field.general.price, origin_field, $scope._qty, $scope.nbd_fields );
                            }else{
                                factor = origin_field.general.price;
                            }
                        }else{
                            if( origin_field.general.price_type == 'mf' ){
                                factor = $scope.eval_price( origin_field.general.price_breaks[quantity_break.index], origin_field, $scope._qty, $scope.nbd_fields );
                            }else{
                                factor = origin_field.general.price_breaks[quantity_break.index];
                            }
                        }
                        if( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'dimension' 
                                && origin_field.general.mesure == 'y' && angular.isDefined(origin_field.general.mesure_range) && origin_field.general.mesure_range.length > 0 ){
                            factor = $scope.calculate_price_base_measurement(origin_field, field.width, field.height, $scope._qty);
                            if( (origin_field.general.price_type == 'f' || origin_field.general.price_type == 'c')
                                    && origin_field.general.mesure_base_pages == 'y' ){
                                if( angular.isDefined(nbOption.odOption.page) ){
                                    var _origin_field = $scope.get_field(nbOption.odOption.page.field_id);
                                    if( _origin_field.general.data_type == 'i' ){
                                        factor *= Math.floor( (nbOption.odOption.page.number + 1) / 2 );
                                    }else{
                                        /* factor *= Math.floor( (nbOption.odOption.page.list_page.length + 1) / 2 ); */
                                    }
                                }
                            }
                        }
                        if( origin_field.general.input_type == 'u' && ( angular.isUndefined(field.value) || field.value == "" ) ){
                            factor = 0;
                        }
                        if( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'page1' 
                                && angular.isDefined(origin_field.general.price_depend_no) && origin_field.general.price_depend_no == 'y' 
                                && angular.isDefined(origin_field.general.price_no_range) && origin_field.general.price_no_range.length > 0 ){
                            if( angular.isUndefined( origin_field.general.input_option.default ) || origin_field.general.input_option.default == '' ){
                                origin_field.general.input_option.default = 0;
                            }
                            var current_value = $scope.validate_int( field.value ),
                            default_val = $scope.validate_int( origin_field.general.input_option.default );
                            current_value = current_value - default_val;
                            if( current_value < 0 ) current_value = 0;
                            var price_no_range = origin_field.general.price_no_range,
                            len = price_no_range.length;
                            angular.forEach(price_no_range, function(range, key){
                                var qty = parseInt( range[0] );
                                if( current_value >= qty ){
                                    factor = range[1];
                                }
                            });
                        }
                    }else{
                        var option = origin_field.general.attributes.options[field.value];
                        if( option ){
                            var option_price =  option.price;
                            if(origin_field.general.depend_quantity == 'n'){
                                if( origin_field.general.price_type == 'mf' ){
                                    factor = $scope.eval_price( option_price[0], origin_field, $scope._qty, $scope.nbd_fields );
                                }else{
                                    factor = $scope.validate_float( option_price[0] );
                                }
                            }else{
                                if( origin_field.general.price_type == 'mf' ){
                                    factor = $scope.eval_price( option_price[quantity_break.index], origin_field, $scope._qty, $scope.nbd_fields );
                                }else{
                                    factor = $scope.validate_float( option_price[quantity_break.index] );
                                }
                            }
                            if( angular.isDefined(option.enable_subattr) && option.enable_subattr == 'on' ){
                                if(angular.isDefined(option.sub_attributes) && option.sub_attributes.length > 0){
                                    soption_price = option.sub_attributes[field.sub_value].price;
                                    if(origin_field.general.depend_quantity == 'n'){
                                        if( origin_field.general.price_type == 'mf' ){
                                            factor += $scope.eval_price( soption_price[0], origin_field, $scope._qty, $scope.nbd_fields );
                                        }else{
                                            factor += $scope.validate_float( soption_price[0] );
                                        }
                                    }else{
                                        if( origin_field.general.price_type == 'mf' ){
                                            factor += $scope.eval_price( soption_price[quantity_break.index], origin_field, $scope._qty, $scope.nbd_fields );
                                        }else{
                                            factor += $scope.validate_float( soption_price[quantity_break.index] );
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if( $scope.isMultipleSelectPage( origin_field ) ){
                        factor = [];
                        angular.forEach(field.values, function(val, v_index){
                            var option = origin_field.general.attributes.options[val];
                            if(origin_field.general.depend_quantity == 'n'){
                                if( origin_field.general.price_type == 'mf' ){
                                    factor[v_index] = $scope.eval_price( option.price[0], origin_field, $scope._qty, $scope.nbd_fields );
                                }else{
                                    factor[v_index] = option.price[0];
                                }
                            }else{
                                if( origin_field.general.price_type == 'mf' ){
                                    factor[v_index] = $scope.eval_price( option.price[quantity_break.index], origin_field, $scope._qty, $scope.nbd_fields );
                                }else{
                                    factor[v_index] = option.price[quantity_break.index];
                                }
                            }
                        });
                        field.price = 0;
                        var xfac = 0, _xfac = 0;
                        angular.forEach(factor, function(fac){
                            fac = $scope.validate_float(fac);
                            var _fac = fac;
                            if( $scope.is_independent_qty( origin_field ) ){
                                fac = 0;
                                field.ind_qty = true;
                            }
                            if( $scope.is_fixed_amount( origin_field ) ){
                                fac /= qty;
                            }
                            switch(origin_field.general.price_type){
                                case 'f':
                                    field.price          += _fac;
                                    $scope.total_price   += fac;
                                    if( $scope.is_independent_qty( origin_field ) ){
                                        line_price.fixed += _fac;
                                    }
                                    break;
                                case 'p':
                                    field.price          += $scope.basePrice * _fac / 100;
                                    $scope.total_price   += $scope.basePrice * fac / 100;
                                    if( $scope.is_independent_qty( origin_field ) ){
                                        line_price.percent += _fac;
                                    }
                                    break;
                                case 'p+':
                                    field.price          += fac / 100;
                                    field._price         += _fac / 100;
                                    xfac                 += fac / 100;
                                    _xfac                += _fac / 100;
                                    field.is_pp          = 1;
                                    break;
                                case 'mf':
                                    field.price          += _fac;
                                    $scope.total_price   += fac;
                                    if( $scope.is_independent_qty( origin_field ) ){
                                        line_price.fixed += _fac;
                                    }
                                    break;
                            }
                        });
                        if( $scope.is_fixed_amount( origin_field ) ){
                            field.fixed_amount = true;
                        }
                        field.price_val = field.price * 1;
                        field.price = $scope.convert_to_wc_price( field.price );
                        if(origin_field.general.price_type == 'p+'){
                            xfactor *= (1 + xfac / 100);
                            if( $scope.is_independent_qty( origin_field ) ){
                                line_price.xfactor *= (1 + _xfac / 100);
                            }
                        }
                    }else{
                        factor = $scope.validate_float(factor) ;
                        field.is_pp = 0;
                        if( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'dimension' 
                                && origin_field.general.price_type == 'c' ){
                            origin_field.general.price_type = 'f';
                        }
                        var _factor = factor;
                        if( $scope.is_independent_qty( origin_field ) ){
                            factor = 0;
                            field.ind_qty = true;
                        }
                        if( $scope.is_fixed_amount( origin_field ) ){
                            factor /= qty;
                        }
                        switch(origin_field.general.price_type){
                            case 'f':
                                field.price_val = _factor;
                                field.price = $scope.convert_to_wc_price( _factor );
                                $scope.total_price += factor;
                                if( $scope.is_independent_qty( origin_field ) ){
                                    line_price.fixed += _factor;
                                }
                                break;
                            case 'p':
                                field.price_val = $scope.basePrice * _factor / 100;
                                field.price = $scope.convert_to_wc_price( field.price_val );
                                $scope.total_price += ($scope.basePrice * factor / 100);
                                if( $scope.is_independent_qty( origin_field ) ){
                                    line_price.percent += _factor;
                                }
                                break;
                            case 'p+':
                                field.price = factor / 100;
                                field._price = _factor / 100;
                                xfactor *= (1 + factor / 100);
                                field.is_pp = 1;
                                if( $scope.is_independent_qty( origin_field ) ){
                                    line_price.xfactor *= (1 + _factor / 100);
                                }
                                break;
                            case 'c':
                                var current_value = $scope.validate_int( field.value );
                                if( ( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'page' && origin_field.general.data_type == 'i' ) ||
                                    ( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'page1' ) || ( angular.isDefined(origin_field.nbe_type) && origin_field.nbe_type == 'number_file' ) ){
                                    if( angular.isUndefined( origin_field.general.input_option.default ) || origin_field.general.input_option.default == '' ){
                                        origin_field.general.input_option.default = 0;
                                    }
                                    var default_val = $scope.validate_int( origin_field.general.input_option.default );
                                    current_value = current_value - default_val;
                                    if( current_value < 0 ) current_value = 0;
                                }
                                field.price_val = _factor * current_value;
                                field.price = $scope.convert_to_wc_price( field.price_val );
                                $scope.total_price += factor * current_value;
                                if( $scope.is_independent_qty( origin_field ) ){
                                    line_price.fixed += field.price_val;
                                }
                                break; 
                            case 'cp':
                                field.price_val = _factor * $scope.validate_int( field.value.length );
                                field.price = $scope.convert_to_wc_price( field.price_val );
                                $scope.total_price += factor * $scope.validate_int( field.value.length );
                                if( $scope.is_independent_qty( origin_field ) ){
                                    line_price.fixed += field.price_val;
                                }
                                break;
                            case 'mf':
                                field.price = $scope.convert_to_wc_price( _factor );
                                field.price_val = _factor;
                                $scope.total_price += factor;
                                if( $scope.is_independent_qty( origin_field ) ){
                                    line_price.fixed += _factor;
                                }
                                break;
                        }
                        if( $scope.is_fixed_amount( origin_field ) ){
                            field.fixed_amount = true;
                        }
                    }
                }
            });
            $scope.total_price += ( ($scope.basePrice + $scope.total_price ) * ( xfactor - 1 ) );
            angular.forEach($scope.nbd_fields, function(field){
                if( field.is_pp == 1 ){
                    field.price_val = field.price * ($scope.basePrice + $scope.total_price ) / ( field.price + 1 );
                    field.price = $scope.convert_to_wc_price( field.price_val );
                }
            });
            var qty_factor = null;
            if( quantity_break.index == 0 && quantity_break.oparator == 'lt' ){
                qty_factor = '';
            }else{
                qty_factor = $scope.options.quantity_breaks[quantity_break.index].dis;
            }
            qty_factor = $scope.validate_float(qty_factor);
            $scope.discount_by_qty = $scope.options.quantity_discount_type == 'f' ? qty_factor : ($scope.basePrice + $scope.total_price ) * qty_factor / 100;
            $scope.final_price = $scope.total_price + $scope.basePrice - $scope.discount_by_qty;
            $scope.final_price = $scope.final_price > 0 ? $scope.final_price : 0;
            $scope.total_cart_price = $scope.final_price * qty;
            if( line_price.fixed != 0 || line_price.xfactor != 1 || line_price.percent != 0 ){
                $scope.cart_item_fee.enable = true;
                var _total_cart_price = $scope.total_cart_price;
                if( line_price.fixed != 0 ){
                    $scope.total_cart_price += line_price.fixed;
                }
                if( line_price.percent != 0 ){
                    $scope.total_cart_price += ($scope.basePrice * line_price.percent / 100);
                }
                if( line_price.xfactor != 1 ){
                    $scope.total_cart_price += ( $scope.total_cart_price * ( line_price.xfactor - 1 ) );
                    angular.forEach($scope.nbd_fields, function(field){
                        if( field.is_pp == 1 && field.ind_qty ){
                            field.price = $scope.convert_to_wc_price( field._price * $scope.total_cart_price / ( field._price + 1 ) );
                        }
                    });
                }
                $scope.cart_item_fee.value = $scope.total_cart_price - _total_cart_price;
                $scope.cart_item_fee.value = $scope.convert_to_wc_price( $scope.cart_item_fee.value );
            }
            <?php if( !( $options['display_type'] == 3 && count( $options['bulk_fields'] ) ) ): ?>
            $scope.total_cart_item_price_num = $scope.total_cart_price;
            <?php endif; ?>
            $scope.total_cart_price = $scope.convert_to_wc_price( $scope.total_cart_price );
            <?php if($change_base == 'yes' && !($options['display_type'] == 3 && count($options['bulk_fields']))): ?>
                <?php if( $in_design_editor && $nbd_qv_type == '2') : ?>
                var wrapEl = '#nbo-options-wrap';
                <?php else: ?>
                var wrapEl = '#product-' + $scope.product_id + ' .summary';
                <?php endif; ?> 
            jQuery(wrapEl + ' .price .amount').html($scope.total_cart_price);
            jQuery(wrapEl + ' .nbo-base-price-html').html(nbds_frontend.total);
            jQuery(wrapEl + ' .nbo-base-price-html-var').html(nbds_frontend.total);
            jQuery('#product-' + $scope.product_id + ' .nbd-design-action-info .price .amount').html($scope.total_cart_price);
            jQuery('#product-' + $scope.product_id + ' .nbd-design-action-info .nbo-base-price-html').html(nbds_frontend.total);
            <?php endif; ?>
            $scope.final_price = $scope.convert_to_wc_price( $scope.final_price, true );
            $scope.total_price = $scope.convert_to_wc_price( $scope.total_price, true );
            $scope.discount_by_qty = $scope.convert_to_wc_price( $scope.discount_by_qty, true );
        };
        $scope.calculate_bulk_total_price = function(){
            var nbb_fields_arr = [], bulk_total_price = 0;
            var basePrice = $scope.price;
            if(this.type == 'variable'){
                var variation_id = jQuery('input[name="variation_id"], input.variation_id').val();
                basePrice = (variation_id != '' && variation_id != 0 ) ? $scope.variations[variation_id] : basePrice;
            }
            basePrice = $scope.convert_wc_price_to_float( basePrice ); 
            var bulk_fields = {};
            angular.forEach($scope.bulk_fields, function(field, index){
                var elements = jQuery('[name="nbb-fields[' + field.id + '][]"]');
                bulk_fields[field.id] = [];
                jQuery.each( elements, function(_index, el){
                    var val = jQuery(el).val();
                    bulk_fields[field.id].push({
                        value: val,
                        value_name: field.general.attributes.options[val].name,
                        enable: true
                    });
                });
            });
            jQuery.each( jQuery('[name="nbb-qty-fields[]"]'), function( index, qtyEl ){
                var nbb_fields = {};
                angular.copy($scope.nbd_fields, nbb_fields);
                angular.forEach(bulk_fields, function(field, field_id){
                    nbb_fields[field_id] = field[index];
                });
                nbb_fields_arr.push( nbb_fields );
            });
            jQuery.each( jQuery('[name="nbb-qty-fields[]"]'), function( index, qtyEl ){
                var qty = $scope.validate_int( jQuery(qtyEl).val() );
                if( qty > 0 ){
                    var total_price     = 0,
                    discount_by_qty = 0,
                    xfactor         = 1,
                    quantity_break  = $scope.get_quantity_break( qty ),
                    cart_item_fee   = {enable: false},
                    line_price      = {
                        fixed: 0,
                        percent: 0,
                        xfactor: 1
                    },
                    fixed_amount = 0;
                    angular.forEach(nbb_fields_arr[index], function(field, field_id){
                        if(field.enable){
                            var origin_field = $scope.get_field(field_id);
                            var factor = null;
                            if( origin_field.general.data_type == 'i' ){
                                if(origin_field.general.depend_quantity == 'n'){
                                    if( origin_field.general.price_type == 'mf' ){
                                        factor = $scope.eval_price( origin_field.general.price, origin_field, qty, nbb_fields_arr[index] );
                                    }else{
                                        factor = origin_field.general.price;
                                    }
                                }else{
                                    if( origin_field.general.price_type == 'mf' ){
                                        factor = $scope.eval_price( origin_field.general.price_breaks[quantity_break.index], origin_field, qty, nbb_fields_arr[index] );
                                    }else{
                                        factor = origin_field.general.price_breaks[quantity_break.index];
                                    }
                                }
                                if( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'dimension' 
                                        && origin_field.general.mesure == 'y' && angular.isDefined(origin_field.general.mesure_range) && origin_field.general.mesure_range.length > 0 ){
                                    factor = $scope.calculate_price_base_measurement(origin_field, field.width, field.height, qty);
                                    if( (origin_field.general.price_type == 'f' || origin_field.general.price_type == 'c')
                                            && origin_field.general.mesure_base_pages == 'y' ){
                                        if( angular.isDefined(nbOption.odOption.page) ){
                                            var _origin_field = $scope.get_field(nbOption.odOption.page.field_id);
                                            if( _origin_field.general.data_type == 'i' ){
                                                factor *= Math.floor( (nbOption.odOption.page.number + 1) / 2 );
                                            }
                                        }
                                    }
                                }
                                if( origin_field.general.input_type == 'u' && ( angular.isUndefined(field.value) || field.value == "" ) ){
                                    factor = 0;
                                }
                                if( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'page1' 
                                        && angular.isDefined(origin_field.general.price_depend_no) && origin_field.general.price_depend_no == 'y' 
                                        && angular.isDefined(origin_field.general.price_no_range) && origin_field.general.price_no_range.length > 0 ){
                                    if( angular.isUndefined( origin_field.general.input_option.default ) || origin_field.general.input_option.default == '' ){
                                        origin_field.general.input_option.default = 0;
                                    }
                                    var current_value = $scope.validate_int( field.value ),
                                    default_val = $scope.validate_int( origin_field.general.input_option.default );
                                    current_value = current_value - default_val;
                                    if( current_value < 0 ) current_value = 0;
                                    var price_no_range = origin_field.general.price_no_range,
                                    len = price_no_range.length;
                                    angular.forEach(price_no_range, function(range, key){
                                        var qty = parseInt( range[0] );
                                        if( current_value >= qty ){
                                            factor = range[1];
                                        }
                                    });
                                }
                            }else{
                                var option = origin_field.general.attributes.options[field.value];
                                if(option){
                                    var option_price =  option.price;
                                    if(origin_field.general.depend_quantity == 'n'){
                                        if( origin_field.general.price_type == 'mf' ){
                                            factor = $scope.eval_price( option_price[0], origin_field, qty, nbb_fields_arr[index] );
                                        }else{
                                            factor = $scope.validate_float( option_price[0] );
                                        }
                                    }else{
                                        if( origin_field.general.price_type == 'mf' ){
                                            factor = $scope.eval_price( option_price[quantity_break.index], origin_field, qty, nbb_fields_arr[index] );
                                        }else{
                                            factor = $scope.validate_float( option_price[quantity_break.index] );
                                        }
                                    }
                                    if( angular.isDefined(option.enable_subattr) && option.enable_subattr == 'on' ){
                                        if(angular.isDefined(option.sub_attributes) && option.sub_attributes.length > 0){
                                            soption_price = option.sub_attributes[field.sub_value].price;
                                            if(origin_field.general.depend_quantity == 'n'){
                                                if( origin_field.general.price_type == 'mf' ){
                                                    factor += $scope.eval_price( soption_price[0], origin_field, qty, nbb_fields_arr[index] );
                                                }else{
                                                    factor += $scope.validate_float( soption_price[0] );
                                                }
                                            }else{
                                                if( origin_field.general.price_type == 'mf' ){
                                                    factor += $scope.eval_price( soption_price[quantity_break.index], origin_field, qty, nbb_fields_arr[index] );
                                                }else{
                                                    factor += $scope.validate_float( soption_price[quantity_break.index] );
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            if( $scope.isMultipleSelectPage( origin_field ) ){
                                factor = [];
                                angular.forEach(field.values, function(val, v_index){
                                    var option = origin_field.general.attributes.options[val];
                                    if(origin_field.general.depend_quantity == 'n'){
                                        if( origin_field.general.price_type == 'mf' ){
                                            factor[v_index] = $scope.eval_price( option.price[0], origin_field, qty, nbb_fields_arr[index] );
                                        }else{
                                            factor[v_index] = option.price[0];
                                        }
                                    }else{
                                        if( origin_field.general.price_type == 'mf' ){
                                            factor[v_index] = $scope.eval_price( option.price[quantity_break.index], origin_field, qty, nbb_fields_arr[index] );
                                        }else{
                                            factor[v_index] = option.price[quantity_break.index];
                                        }
                                    }
                                });
                                field.price = 0;
                                var xfac = 0, _xfac = 0;
                                angular.forEach(factor, function(fac){
                                    fac = $scope.validate_float(fac);
                                    var _fac = fac;
                                    if( $scope.is_independent_qty( origin_field ) ){
                                        fac = 0;
                                        field.ind_qty = true;
                                    }
                                    switch(origin_field.general.price_type){
                                        case 'f':
                                        case 'mf':
                                            field.price          += _fac;
                                            if( ! $scope.is_fixed_amount( origin_field ) ) total_price += fac;
                                            if( $scope.is_independent_qty( origin_field ) ){
                                                line_price.fixed += _fac;
                                            }
                                            break;
                                        case 'p':
                                            field.price          += basePrice * _fac / 100;
                                            total_price          += basePrice * fac / 100;
                                            if( $scope.is_independent_qty( origin_field ) ){
                                                line_price.percent += _fac;
                                            }
                                            break;
                                        case 'p+':
                                            field.price          += fac / 100;
                                            field._price         += _fac / 100;
                                            xfac                 += fac / 100;
                                            _xfac                += _fac / 100;
                                            field.is_pp          = 1;
                                            break;
                                    }
                                });
                                if( $scope.is_fixed_amount( origin_field ) ){
                                    fixed_amount += field.price;
                                    field.fixed_amount = true;
                                }
                                field.price_val = field.price * 1;
                                field.price = $scope.convert_to_wc_price( field.price );
                                if(origin_field.general.price_type == 'p+'){
                                    xfactor *= (1 + xfac / 100);
                                    if( $scope.is_independent_qty( origin_field ) ){
                                        line_price.xfactor *= (1 + _xfac / 100);
                                    }
                                }
                            }else{
                                factor = $scope.validate_float(factor) ;
                                field.is_pp = 0;
                                if( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'dimension' 
                                        && origin_field.general.price_type == 'c' ){
                                    origin_field.general.price_type = 'f';
                                }
                                var _factor = factor;
                                if( $scope.is_independent_qty( origin_field ) ){
                                    factor = 0;
                                    field.ind_qty = true;
                                }
                                switch(origin_field.general.price_type){
                                    case 'f':
                                    case 'mf':
                                        field.price_val = _factor;
                                        field.price = $scope.convert_to_wc_price( _factor );
                                        if( ! $scope.is_fixed_amount( origin_field ) ) total_price += factor;
                                        if( $scope.is_independent_qty( origin_field ) ){
                                            line_price.fixed += _factor;
                                        }
                                        break;
                                    case 'p':
                                        field.price_val = basePrice * _factor / 100;
                                        field.price = $scope.convert_to_wc_price( field.price_val );
                                        total_price += (basePrice * factor / 100);
                                        if( $scope.is_independent_qty( origin_field ) ){
                                            line_price.percent += _factor;
                                        }
                                        break;
                                    case 'p+':
                                        field.price = factor / 100;
                                        field._price = _factor / 100;
                                        xfactor *= (1 + factor / 100);
                                        field.is_pp = 1;
                                        if( $scope.is_independent_qty( origin_field ) ){
                                            line_price.xfactor *= (1 + _factor / 100);
                                        }
                                        break;
                                    case 'c':
                                        var current_value = $scope.validate_int( field.value );
                                        if( ( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'page' && origin_field.general.data_type == 'i' ) ||
                                            ( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'page1' ) || ( angular.isDefined(origin_field.nbe_type) && origin_field.nbe_type == 'number_file' ) ){
                                            if( angular.isUndefined( origin_field.general.input_option.default ) || origin_field.general.input_option.default == '' ){
                                                origin_field.general.input_option.default = 0;
                                            }
                                            var default_val = $scope.validate_int( origin_field.general.input_option.default );
                                            current_value = current_value - default_val;
                                            if( current_value < 0 ) current_value = 0;
                                        }
                                        field.price_val = _factor * current_value;
                                        field.price = $scope.convert_to_wc_price( field.price_val );
                                        total_price += factor * current_value;
                                        if( $scope.is_independent_qty( origin_field ) ){
                                            line_price.fixed += _factor * current_value;
                                        }
                                        break; 
                                    case 'cp':
                                        field.price_val = _factor * $scope.validate_int( field.value.length );
                                        field.price = $scope.convert_to_wc_price( field.price_val );
                                        total_price += factor * $scope.validate_int( field.value.length );
                                        if( $scope.is_independent_qty( origin_field ) ){
                                            line_price.fixed += field.price_val;
                                        }
                                        break;
                                }
                                if( $scope.is_fixed_amount( origin_field ) ){
                                    fixed_amount += factor;
                                    field.fixed_amount = true;
                                }
                            }
                        }
                    });
                    total_price += ( ( basePrice + total_price ) * ( xfactor - 1 ) );
                    angular.forEach(nbb_fields_arr[index], function(field){
                        if( field.is_pp == 1 ){
                            field.price_val = field.price * (basePrice + total_price ) / ( field.price + 1 );
                            field.price = $scope.convert_to_wc_price( field.price_val );
                        }
                    });
                    var qty_factor = null;
                    if( quantity_break.index == 0 && quantity_break.oparator == 'lt' ){
                        qty_factor = '';
                    }else{
                        qty_factor = $scope.options.quantity_breaks[quantity_break.index].dis;
                    }
                    var qty_factor = $scope.validate_float( qty_factor );
                    discount_by_qty = $scope.options.quantity_discount_type == 'f' ? qty_factor : (basePrice + total_price ) * qty_factor / 100;
                    var final_price = total_price + basePrice - discount_by_qty;
                    final_price = final_price > 0 ? final_price : 0;
                    total_cart_price = final_price * qty;
                    var _total_cart_price = total_cart_price;
                    if( line_price.fixed != 0 || line_price.xfactor != 1 || line_price.percent != 0 || fixed_amount > 0 ){
                        if( line_price.fixed != 0 ){
                            total_cart_price += line_price.fixed;
                        }
                        if( line_price.percent != 0 ){
                            total_cart_price += (basePrice * line_price.percent / 100);
                        }
                        if( line_price.xfactor != 1 ){
                            total_cart_price += ( total_cart_price * ( line_price.xfactor - 1 ) );
                            angular.forEach(nbb_fields_arr[index], function(field){
                                if( field.is_pp == 1 && field.ind_qty ){
                                    field.price = $scope.convert_to_wc_price( field._price * total_cart_price / ( field._price + 1 ) );
                                }
                            });
                        }
                        cart_item_fee.value = total_cart_price - _total_cart_price;
                        if( cart_item_fee.value > 0 ){
                            cart_item_fee.enable = true;
                        }
                        cart_item_fee.value = $scope.convert_to_wc_price( cart_item_fee.value );
                    }
                    if( $scope.options.quantity_discount_type == 'f' ){
                        total_cart_price += fixed_amount;
                    }else{
                        total_cart_price += fixed_amount * ( 100 - qty_factor ) / 100;
                    }
                    bulk_total_price += total_cart_price;
                }
            });
            bulk_total_price = bulk_total_price > 0 ? bulk_total_price : 0;
            $scope.total_cart_item_price_num = bulk_total_price;
            var bulk_total_price_html = $scope.convert_to_wc_price( bulk_total_price );
            <?php if( $change_base == 'yes' ): ?>
                <?php if( $in_design_editor && $nbd_qv_type == '2') : ?>
                var wrapEl = '#nbo-options-wrap';
                <?php else: ?>
                var wrapEl = '#product-' + $scope.product_id + ' .summary';
                <?php endif; ?>
            jQuery(wrapEl + ' .price .amount').html(bulk_total_price_html);
            jQuery(wrapEl + ' .nbo-base-price-html').html(nbds_frontend.total);
            jQuery('#product-' + $scope.product_id + ' .nbd-design-action-info .price .amount').html(bulk_total_price_html);
            jQuery('#product-' + $scope.product_id + ' .nbd-design-action-info .nbo-base-price-html').html(nbds_frontend.total);
            <?php endif; ?>
        };
        $scope.calculate_price_table2 = function(){
            $scope.price_table = [];
            var basePrice = $scope.price;
            if(this.type == 'variable'){
                var variation_id = jQuery('input[name="variation_id"], input.variation_id').val();
                basePrice = (variation_id != '' && variation_id != 0 ) ? $scope.variations[variation_id] : basePrice;
            }
            basePrice = $scope.convert_wc_price_to_float( basePrice );
            if( $scope.has_price_matrix && $scope.options.matrix_type == 2 ){
                var pmi = angular.isDefined( $scope.options.pm_selected ) ? $scope.options.pm_selected[0] : 0,
                pmj = angular.isDefined( $scope.options.pm_selected ) ? $scope.options.pm_selected[1] : 0;
                basePrice = $scope.get_mpm_base_price( pmi, pmj );
            }
            var missing_one = true;
            angular.forEach($scope.options.quantity_breaks, function(_break, key){
                if( _break.val == '1' ) missing_one = false;
            });
            var quantity_breaks = [];
            angular.copy($scope.options.quantity_breaks, quantity_breaks);
            if( missing_one ){
               quantity_breaks.unshift({val: 1, dis: 0});
            }
            angular.forEach(quantity_breaks, function(_break, key){
                var qty         = $scope.validate_int(_break.val),
                nbd_fields      = {},
                total_price     = 0,
                discount_by_qty = 0,
                xfactor         = 1,
                quantity_break  = $scope.get_quantity_break( qty ),
                cart_item_fee   = {enable: false},
                line_price      = {
                    fixed: 0,
                    percent: 0,
                    xfactor: 1
                }, 
                fixed_amount = 0;
                angular.copy($scope.nbd_fields, nbd_fields);
                angular.forEach(nbd_fields, function(field, field_id){
                    if( field.enable ){
                        if( $scope.has_price_matrix && $scope.options.matrix_type == 2 ){
                            if( $scope.options.pm_hoz.indexOf( field_id ) > -1 || $scope.options.pm_ver.indexOf( field_id ) > -1 ) return;
                        }

                        var origin_field = $scope.get_field(field_id);
                        var factor = null;
                        if( origin_field.general.data_type == 'i' ){
                            if(origin_field.general.depend_quantity == 'n'){
                                if( origin_field.general.price_type == 'mf' ){
                                    factor = $scope.eval_price( origin_field.general.price, origin_field, qty, nbd_fields );
                                }else{
                                    factor = origin_field.general.price;
                                }
                            }else{
                                if( origin_field.general.price_type == 'mf' ){
                                    factor = $scope.eval_price( origin_field.general.price_breaks[quantity_break.index], origin_field, qty, nbd_fields );
                                }else{
                                    factor = origin_field.general.price_breaks[quantity_break.index];
                                }
                            }
                            if( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'dimension' 
                                    && origin_field.general.mesure == 'y' && angular.isDefined(origin_field.general.mesure_range) && origin_field.general.mesure_range.length > 0 ){
                                factor = $scope.calculate_price_base_measurement(origin_field, field.width, field.height, qty);
                                if( (origin_field.general.price_type == 'f' || origin_field.general.price_type == 'c')
                                        && origin_field.general.mesure_base_pages == 'y' ){
                                    if( angular.isDefined(nbOption.odOption.page) ){
                                        var _origin_field = $scope.get_field(nbOption.odOption.page.field_id);
                                        if( _origin_field.general.data_type == 'i' ){
                                            factor *= Math.floor( (nbOption.odOption.page.number + 1) / 2 );
                                        }else{
                                            /* factor *= Math.floor( (nbOption.odOption.page.list_page.length + 1) / 2 ); */
                                        }
                                    }
                                }
                            }
                            if( origin_field.general.input_type == 'u' && ( angular.isUndefined(field.value) || field.value == "" ) ){
                                factor = 0;
                            }
                            if( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'page1' 
                                    && angular.isDefined(origin_field.general.price_depend_no) && origin_field.general.price_depend_no == 'y' 
                                    && angular.isDefined(origin_field.general.price_no_range) && origin_field.general.price_no_range.length > 0 ){
                                if( angular.isUndefined( origin_field.general.input_option.default ) || origin_field.general.input_option.default == '' ){
                                    origin_field.general.input_option.default = 0;
                                }
                                var current_value = $scope.validate_int( field.value ),
                                default_val = $scope.validate_int( origin_field.general.input_option.default );
                                current_value = current_value - default_val;
                                if( current_value < 0 ) current_value = 0;
                                var price_no_range = origin_field.general.price_no_range,
                                len = price_no_range.length;
                                angular.forEach(price_no_range, function(range, key){
                                    var qty = parseInt( range[0] );
                                    if( current_value >= qty ){
                                        factor = range[1];
                                    }
                                });
                            }
                        }else{
                            var option = origin_field.general.attributes.options[field.value];
                            if(option){
                                var option_price =  option.price;
                                if(origin_field.general.depend_quantity == 'n'){
                                    if( origin_field.general.price_type == 'mf' ){
                                        factor = $scope.eval_price( option_price[0], origin_field, qty, nbd_fields );
                                    }else{
                                        factor = $scope.validate_float( option_price[0] );
                                    }
                                }else{
                                    if( origin_field.general.price_type == 'mf' ){
                                        factor = $scope.eval_price( option_price[quantity_break.index], origin_field, qty, nbd_fields );
                                    }else{
                                        factor = $scope.validate_float( option_price[quantity_break.index] );
                                    }
                                }
                                if( angular.isDefined(option.enable_subattr) && option.enable_subattr == 'on' ){
                                    if(angular.isDefined(option.sub_attributes) && option.sub_attributes.length > 0){
                                        soption_price = option.sub_attributes[field.sub_value].price;
                                        if(origin_field.general.depend_quantity == 'n'){
                                            if( origin_field.general.price_type == 'mf' ){
                                                factor += $scope.eval_price( soption_price[0], origin_field, qty, nbd_fields );
                                            }else{
                                                factor += $scope.validate_float( soption_price[0] );
                                            }
                                        }else{
                                            if( origin_field.general.price_type == 'mf' ){
                                                factor += $scope.eval_price( soption_price[quantity_break.index], origin_field, qty, nbd_fields );
                                            }else{
                                                factor += $scope.validate_float( soption_price[quantity_break.index] );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if( $scope.isMultipleSelectPage( origin_field ) ){
                            factor = [];
                            angular.forEach(field.values, function(val, v_index){
                                var option = origin_field.general.attributes.options[val];
                                if(origin_field.general.depend_quantity == 'n'){
                                    if( origin_field.general.price_type == 'mf' ){
                                        factor[v_index] = $scope.eval_price( option.price[0], origin_field, qty, nbd_fields );
                                    }else{
                                        factor[v_index] = option.price[0];
                                    }
                                }else{
                                    if( origin_field.general.price_type == 'mf' ){
                                        factor[v_index] = $scope.eval_price( option.price[quantity_break.index], origin_field, qty, nbd_fields );
                                    }else{
                                        factor[v_index] = option.price[quantity_break.index];
                                    }
                                }
                            });
                            field.price = 0;
                            var xfac = 0, _xfac = 0;
                            angular.forEach(factor, function(fac){
                                fac = $scope.validate_float(fac);
                                var _fac = fac;
                                if( $scope.is_independent_qty( origin_field ) ){
                                    fac = 0;
                                    field.ind_qty = true;
                                }
                                switch(origin_field.general.price_type){
                                    case 'f':
                                    case 'mf':
                                        field.price          += _fac;
                                        if( ! $scope.is_fixed_amount( origin_field ) ) total_price += fac;
                                        if( $scope.is_independent_qty( origin_field ) ){
                                            line_price.fixed += _fac;
                                        }
                                        break;
                                    case 'p':
                                        field.price          += basePrice * _fac / 100;
                                        total_price          += basePrice * fac / 100;
                                        if( $scope.is_independent_qty( origin_field ) ){
                                            line_price.percent += _fac;
                                        }
                                        break;
                                    case 'p+':
                                        field.price          += fac / 100;
                                        field._price         += _fac / 100;
                                        xfac                 += fac / 100;
                                        _xfac                += _fac / 100;
                                        field.is_pp          = 1;
                                        break;
                                }
                            });
                            if( $scope.is_fixed_amount( origin_field ) ){
                                fixed_amount += field.price;
                                field.fixed_amount = true;
                            }
                            field.price_val = field.price * 1;
                            field.price = $scope.convert_to_wc_price( field.price );
                            if(origin_field.general.price_type == 'p+'){
                                xfactor *= (1 + xfac / 100);
                                if( $scope.is_independent_qty( origin_field ) ){
                                    line_price.xfactor *= (1 + _xfac / 100);
                                }
                            }
                        }else{
                            factor = $scope.validate_float(factor) ;
                            field.is_pp = 0;
                            if( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'dimension' 
                                    && origin_field.general.price_type == 'c' ){
                                origin_field.general.price_type = 'f';
                            }
                            var _factor = factor;
                            if( $scope.is_independent_qty( origin_field ) ){
                                factor = 0;
                                field.ind_qty = true;
                            }
                            switch(origin_field.general.price_type){
                                case 'f':
                                case 'mf':
                                    field.price_val = _factor;
                                    field.price = $scope.convert_to_wc_price( _factor );
                                    if( ! $scope.is_fixed_amount( origin_field ) ) total_price += factor;
                                    if( $scope.is_independent_qty( origin_field ) ){
                                        line_price.fixed += _factor;
                                    }
                                    break;
                                case 'p':
                                    field.price_val = basePrice * _factor / 100;
                                    field.price = $scope.convert_to_wc_price( basePrice * _factor / 100 );
                                    total_price += (basePrice * factor / 100);
                                    if( $scope.is_independent_qty( origin_field ) ){
                                        line_price.percent += _factor;
                                    }
                                    break;
                                case 'p+':
                                    field.price = factor / 100;
                                    field._price = _factor / 100;
                                    xfactor *= (1 + factor / 100);
                                    field.is_pp = 1;
                                    if( $scope.is_independent_qty( origin_field ) ){
                                        line_price.xfactor *= (1 + _factor / 100);
                                    }
                                    break;
                                case 'c':
                                    var current_value = $scope.validate_int( field.value );
                                    if( ( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'page' && origin_field.general.data_type == 'i' ) ||
                                        ( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'page1' ) || ( angular.isDefined(origin_field.nbe_type) && origin_field.nbe_type == 'number_file' ) ){
                                        if( angular.isUndefined( origin_field.general.input_option.default ) || origin_field.general.input_option.default == '' ){
                                            origin_field.general.input_option.default = 0;
                                        }
                                        var default_val = $scope.validate_int( origin_field.general.input_option.default );
                                        current_value = current_value - default_val;
                                        if( current_value < 0 ) current_value = 0;
                                    }
                                    field.price_val = _factor * current_value;
                                    field.price = $scope.convert_to_wc_price( field.price_val );
                                    total_price += factor * current_value;
                                    if( $scope.is_independent_qty( origin_field ) ){
                                        line_price.fixed += field.price_val;
                                    }
                                    break; 
                                case 'cp':
                                    field.price_val = _factor * $scope.validate_int( field.value.length );
                                    field.price = $scope.convert_to_wc_price( field.price_val );
                                    total_price += factor * $scope.validate_int( field.value.length );
                                    if( $scope.is_independent_qty( origin_field ) ){
                                        line_price.fixed += field.price_val;
                                    }
                                    break;
                            }
                            if( $scope.is_fixed_amount( origin_field ) ){
                                fixed_amount += factor;
                                field.fixed_amount = true;
                            }
                        }
                    }
                });
                total_price += ( ( basePrice + total_price ) * ( xfactor - 1 ) );
                angular.forEach(nbd_fields, function(field){
                    if( field.is_pp == 1 ){
                        field.price_val = field.price * (basePrice + total_price ) / ( field.price + 1 );
                        field.price = $scope.convert_to_wc_price( field.price_val );
                    }
                });
                var qty_factor = $scope.validate_float( _break.dis );
                discount_by_qty = $scope.options.quantity_discount_type == 'f' ? qty_factor : (basePrice + total_price ) * qty_factor / 100;
                var final_price = total_price + basePrice - discount_by_qty;
                final_price = final_price > 0 ? final_price : 0;
                total_cart_price = final_price * qty;
                var _total_cart_price = total_cart_price;
                if( line_price.fixed != 0 || line_price.xfactor != 1 || line_price.percent != 0 || fixed_amount > 0 ){
                    if( line_price.fixed != 0 ){
                        total_cart_price += line_price.fixed;
                    }
                    if( line_price.percent != 0 ){
                        total_cart_price += (basePrice * line_price.percent / 100);
                    }
                    if( line_price.xfactor != 1 ){
                        total_cart_price += ( total_cart_price * ( line_price.xfactor - 1 ) );
                        angular.forEach(nbd_fields, function(field){
                            if( field.is_pp == 1 && field.ind_qty ){
                                field.price = $scope.convert_to_wc_price( field._price * total_cart_price / ( field._price + 1 ) );
                            }
                        });
                    }
                    cart_item_fee.value = total_cart_price - _total_cart_price + fixed_amount;
                    if( cart_item_fee.value > 0 ){
                        cart_item_fee.enable = true;
                    }
                    cart_item_fee.value = $scope.convert_to_wc_price( cart_item_fee.value );
                }
                if( $scope.options.quantity_discount_type == 'f' ){
                    total_cart_price += fixed_amount;
                }else{
                    total_cart_price += fixed_amount * ( 100 - qty_factor ) / 100;
                }
                $scope.price_table[key] = {
                    qty: qty,
                    cart_item_fee: cart_item_fee,
                    total_cart_price: $scope.convert_to_wc_price( total_cart_price ),
                    _total_cart_price: $scope.convert_to_wc_price( _total_cart_price ),
                    final_price_val: final_price,
                    final_price: $scope.convert_to_wc_price( final_price, true )
                };
            });
            $scope.price_table_cart_fee = false;
            var _first = $scope.price_table[0];
            angular.forEach($scope.price_table, function(pt, key){
                if( angular.isDefined( pt.cart_item_fee.enable ) && pt.cart_item_fee.enable ){
                    $scope.price_table_cart_fee = true;
                }
                if( pt.final_price_val.toFixed != 0 ){
                    pt.klass = ((pt.final_price_val - _first.final_price_val) > 0 ) ? 'nbo-inc' : ( ((pt.final_price_val - _first.final_price_val) < 0 ) ? 'nbo-dec' : '' );
                    pt.saving = ( -(pt.final_price_val - _first.final_price_val) / _first.final_price_val * 100 ).toFixed(2) + '%';
                }else{
                    pt.saving = '';
                }
            });
            if( missing_one ){
                $scope.price_table.splice(0, 1);
            }
        };
        $scope.calculate_price_table = function(){
            $scope.price_table = [];
            $scope.basePrice = $scope.price;
            if(this.type == 'variable'){
                var variation_id = jQuery('input[name="variation_id"], input.variation_id').val();
                $scope.basePrice = (variation_id != '' && variation_id != 0 ) ? $scope.variations[variation_id] : $scope.basePrice;
            }
            $scope.basePrice = $scope.convert_wc_price_to_float($scope.basePrice);
            if( $scope.has_price_matrix && $scope.options.matrix_type == 2 ){
                var pmi = angular.isDefined( $scope.options.pm_selected ) ? $scope.options.pm_selected[0] : 0,
                pmj = angular.isDefined( $scope.options.pm_selected ) ? $scope.options.pm_selected[1] : 0;
                $scope.basePrice = $scope.get_mpm_base_price( pmi, pmj );
            }
            var quantity_breaks = [];
            angular.forEach($scope.options.quantity_breaks, function(_break, key){
                quantity_breaks[key] = $scope.validate_int(_break.val);
            });
            var _qty = 0;
            if( $scope.is_sold_individually == 1 ){
                _qty = 1;
            }else{
                _qty = $scope.validate_int(jQuery('input[name="quantity"]').val());
            }
            angular.forEach(quantity_breaks, function(_break, key){
                var pt;
                if( key == 0 && _break > 1 ) {
                    pt = {};
                    pt.from = 1;
                    pt.up = _break - 1;
                    pt.quantity_break = {index: 0, oparator: 'lt'};
                    $scope.price_table.push(pt);
                }
                if( key > 0 && key < (quantity_breaks.length) ){
                    pt = {};
                    pt.from = quantity_breaks[key - 1];
                    pt.up = _break - 1;
                    pt.quantity_break = {index: key - 1, oparator: 'bw'};
                    $scope.price_table.push(pt);
                }
                if( key == (quantity_breaks.length - 1) ){
                    pt = {};
                    pt.from = _break;
                    pt.up = '**';
                    pt.quantity_break = {index: key, oparator: 'gt'};
                    $scope.price_table.push(pt);
                }
            });
            angular.forEach($scope.price_table, function(pt, pt_index){
                pt.nbd_fields = {};
                pt.in_range = ( _qty >= pt.from && ( _qty <= pt.up || pt.up == '**' ) ) ? true : false;
                angular.copy($scope.nbd_fields, pt.nbd_fields);
                pt.total_price = 0;
                pt.discount_by_qty = 0;
                var xfactor = 1, fixed_amount = 0;
                angular.forEach(pt.nbd_fields, function(field, field_id){
                    if( field.enable ){
                        if( $scope.has_price_matrix && $scope.options.matrix_type == 2 ){
                            if( $scope.options.pm_hoz.indexOf( field_id ) > -1 || $scope.options.pm_ver.indexOf( field_id ) > -1 ) return;
                        }

                        var origin_field = $scope.get_field(field_id);
                        var factor = null;
                        if( origin_field.general.data_type == 'i' ){
                            if(origin_field.general.depend_quantity == 'n'){
                                if( origin_field.general.price_type == 'mf' ){
                                    factor = $scope.eval_price( origin_field.general.price, origin_field, pt.from, pt.nbd_fields );
                                }else{
                                    factor = origin_field.general.price;
                                }
                            }else{
                                if( origin_field.general.price_type == 'mf' ){
                                    factor = $scope.eval_price( origin_field.general.price_breaks[pt.quantity_break.index], origin_field, pt.from, pt.nbd_fields );
                                }else{
                                    factor = origin_field.general.price_breaks[pt.quantity_break.index];
                                }
                            }
                            if( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'dimension' 
                                    && origin_field.general.mesure == 'y' && angular.isDefined(origin_field.general.mesure_range) && origin_field.general.mesure_range.length > 0 ){
                                factor = $scope.calculate_price_base_measurement(origin_field, field.width, field.height, pt.from);
                                if( (origin_field.general.price_type == 'f' || origin_field.general.price_type == 'c')
                                        && origin_field.general.mesure_base_pages == 'y' ){
                                    if( angular.isDefined(nbOption.odOption.page) ){
                                        factor *= Math.floor( (nbOption.odOption.page.number + 1) / 2 );
                                    }
                                }
                            }
                            if( origin_field.general.input_type == 'u' && ( angular.isUndefined(field.value) || field.value == "" ) ){
                                factor = 0;
                            }
                            if( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'page1' 
                                    && angular.isDefined(origin_field.general.price_depend_no) && origin_field.general.price_depend_no == 'y' 
                                    && angular.isDefined(origin_field.general.price_no_range) && origin_field.general.price_no_range.length > 0 ){
                                if( angular.isUndefined( origin_field.general.input_option.default ) || origin_field.general.input_option.default == '' ){
                                    origin_field.general.input_option.default = 0;
                                }
                                var current_value = $scope.validate_int( field.value ),
                                default_val = $scope.validate_int( origin_field.general.input_option.default );
                                current_value = current_value - default_val;
                                if( current_value < 0 ) current_value = 0;
                                var price_no_range = origin_field.general.price_no_range,
                                len = price_no_range.length;
                                angular.forEach(price_no_range, function(range, key){
                                    var qty = parseInt( range[0] );
                                    if( current_value >= qty ){
                                        factor = range[1];
                                    }
                                });
                            }
                        }else{
                            var option = origin_field.general.attributes.options[field.value];
                            if(option){
                                if(origin_field.general.depend_quantity == 'n'){
                                    if( origin_field.general.price_type == 'mf' ){
                                        factor = $scope.eval_price( option.price[0], origin_field, pt.from, pt.nbd_fields );
                                    }else{
                                        factor = $scope.validate_float( option.price[0] );
                                    }
                                }else{
                                    if( origin_field.general.price_type == 'mf' ){
                                        factor = $scope.eval_price( option.price[pt.quantity_break.index], origin_field, pt.from, pt.nbd_fields );
                                    }else{
                                        factor = $scope.validate_float( option.price[pt.quantity_break.index] );
                                    }
                                }
                                if( angular.isDefined(option.enable_subattr) && option.enable_subattr == 'on' ){
                                    if(angular.isDefined(option.sub_attributes) && option.sub_attributes.length > 0){
                                        soption_price = option.sub_attributes[field.sub_value].price;
                                        if(origin_field.general.depend_quantity == 'n'){
                                            if( origin_field.general.price_type == 'mf' ){
                                                factor += $scope.eval_price( soption_price[0], origin_field, pt.from, pt.nbd_fields );
                                            }else{
                                                factor += $scope.validate_float( soption_price[0] );
                                            }
                                        }else{
                                            if( origin_field.general.price_type == 'mf' ){
                                                factor += $scope.eval_price( soption_price[pt.quantity_break.index], origin_field, pt.from, pt.nbd_fields );
                                            }else{
                                                factor += $scope.validate_float( soption_price[pt.quantity_break.index] );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if( $scope.isMultipleSelectPage( origin_field ) ){
                            factor = [];
                            angular.forEach(field.values, function(val, v_index){
                                var option = origin_field.general.attributes.options[val];
                                if(origin_field.general.depend_quantity == 'n'){
                                    if( origin_field.general.price_type == 'mf' ){
                                        factor[v_index] = $scope.eval_price( option.price[0], origin_field, pt.from, pt.nbd_fields );
                                    }else{
                                        factor[v_index] = option.price[0];
                                    }
                                }else{
                                    if( origin_field.general.price_type == 'mf' ){
                                        factor[v_index] = $scope.eval_price( option.price[pt.quantity_break.index], origin_field, pt.from, pt.nbd_fields );
                                    }else{
                                        factor[v_index] = option.price[pt.quantity_break.index];
                                    }
                                }
                            });
                            field.price = 0;
                            var xfac = 0;
                            angular.forEach(factor, function(fac){
                                fac = $scope.validate_float(fac);
                                var _fac = fac;
                                if( $scope.is_independent_qty( origin_field ) ){
                                    fac = 0;
                                }
                                switch(origin_field.general.price_type){
                                    case 'f':
                                    case 'mf':
                                        field.price += _fac;
                                        if( ! $scope.is_fixed_amount( origin_field ) ) pt.total_price += fac;
                                        break;
                                    case 'p':
                                        field.price += $scope.basePrice * _fac / 100;
                                        pt.total_price += $scope.basePrice * fac / 100;
                                        break;
                                    case 'p+':
                                        field.price += fac / 100;
                                        xfac += fac / 100;
                                        field.is_pp = 1;
                                        break;
                                }
                            });
                            if( $scope.is_fixed_amount( origin_field ) ){
                                fixed_amount += field.price;
                                field.fixed_amount = true;
                            }
                            field.price_val = field.price * 1;
                            field.price = $scope.convert_to_wc_price( field.price ); 
                            if(origin_field.general.price_type == 'p+'){
                                xfactor *= (1 + xfac / 100);
                            }
                        }else{
                            factor = $scope.validate_float(factor) ;
                            field.is_pp = 0;
                            if( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'dimension' 
                                && origin_field.general.price_type == 'c' ){
                                origin_field.general.price_type = 'f';
                            }
                            var _factor = factor;
                            if( $scope.is_independent_qty( origin_field ) ){
                                factor = 0;
                            }
                            switch(origin_field.general.price_type){
                                case 'f':
                                case 'mf':
                                    field.price_val = _factor;
                                    field.price = $scope.convert_to_wc_price( _factor );
                                    if( ! $scope.is_fixed_amount( origin_field ) ) pt.total_price += factor;
                                    break;
                                case 'p':
                                    field.price_val = $scope.basePrice * _factor / 100;
                                    field.price = $scope.convert_to_wc_price(field.price_val );
                                    pt.total_price += ($scope.basePrice * factor / 100);
                                    break;
                                case 'p+':
                                    field.price = factor / 100;
                                    xfactor *= (1 + factor / 100);
                                    field.is_pp = 1;
                                    break;
                                case 'c':
                                    var current_value = $scope.validate_int( field.value );
                                    if( ( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'page' && origin_field.general.data_type == 'i' ) ||
                                        ( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'page1' ) || ( angular.isDefined(origin_field.nbe_type) && origin_field.nbe_type == 'number_file' ) ){
                                        if( angular.isUndefined( origin_field.general.input_option.default ) || origin_field.general.input_option.default == '' ){
                                            origin_field.general.input_option.default = 0;
                                        }
                                        var default_val = $scope.validate_int( origin_field.general.input_option.default );
                                        current_value = current_value - default_val;
                                        if( current_value < 0 ) current_value = 0;
                                    }
                                    field.price_val = _factor * current_value;
                                    field.price = $scope.convert_to_wc_price( field.price_val );
                                    pt.total_price += factor * current_value;
                                    break; 
                                case 'cp':
                                    field.price_val = _factor * $scope.validate_int( field.value.length );
                                    field.price = $scope.convert_to_wc_price( field.price_val );
                                    pt.total_price += factor * $scope.validate_int( field.value.length );
                                    break;
                            }
                            if( $scope.is_fixed_amount( origin_field ) ){
                                fixed_amount += factor;
                                field.fixed_amount = true;
                            }
                        }
                    }
                });
                pt.total_price += ( ($scope.basePrice + pt.total_price ) * (xfactor - 1 ) );
                angular.forEach(pt.nbd_fields, function(field){
                    if( field.is_pp == 1 ){
                        field.price_val = field.price * ($scope.basePrice + pt.total_price ) / ( field.price + 1 );
                        field.price = $scope.convert_to_wc_price( field.price_val );
                    }
                });
                var qty_factor = null;
                if( pt.quantity_break.index == 0 && pt.quantity_break.oparator == 'lt' ){
                    qty_factor = '';
                }else{
                    qty_factor = $scope.options.quantity_breaks[pt.quantity_break.index].dis;
                }
                qty_factor = $scope.validate_float(qty_factor);
                pt.discount_by_qty = $scope.options.quantity_discount_type == 'f' ? qty_factor : ($scope.basePrice + pt.total_price ) * qty_factor / 100;
                pt.final_price = pt.total_price + $scope.basePrice - pt.discount_by_qty;
                pt.final_price = pt.final_price > 0 ? pt.final_price : 0;
                pt.final_price = $scope.convert_to_wc_price( pt.final_price, true );
                pt.total_price = $scope.convert_to_wc_price( pt.total_price, true );
                pt.discount_by_qty = $scope.convert_to_wc_price( pt.discount_by_qty, true );
            });
        };
        $scope.is_independent_qty = function( field ){
            if( angular.isDefined( field.general.depend_qty ) && field.general.depend_qty == 'n' ){
                return true;
            }else{
                return false;
            }
        };
        $scope.is_fixed_amount = function( field ){
            if( angular.isDefined( field.general.depend_qty ) && field.general.depend_qty == 'n2' ){
                return true;
            }else{
                return false;
            }
        };
        $scope.isMultipleSelectPage = function(field){
            if( angular.isDefined(field.nbd_type) && ( field.nbd_type == 'page' || field.nbd_type == 'page2' ) && field.general.data_type == 'm' ){
                return true;
            }
            return false;
        };
        $scope.calculate_price_base_measurement = function(origin_field, width, height, qty){
            var mesure_range = origin_field.general.mesure_range;
            var area = $scope.validate_float( width ) * $scope.validate_float( height ),
            _area = area;

            if( angular.isDefined( origin_field.general.mesure_base_qty ) && origin_field.general.mesure_base_qty == 'y' ){
                area *= qty;
            }

            if( angular.isDefined( origin_field.general.mesure_min_area ) ){
                var minArea = $scope.validate_float( origin_field.general.mesure_min_area );
                _area -= minArea;
                area -= minArea;
            }
            _area = _area > 0 ? _area : 0;
            area = area > 0 ? area : 0;

            var price_per_unit = 0, start_range = 0, end_range = 0, price_range = 0;
            if( angular.isDefined( origin_field.general.mesure_type ) && origin_field.general.mesure_type == 'ur' ){
                var measurement_price = 0, infinity_end_range = false, area_in_range, prev_end_range = 0;
                angular.forEach(mesure_range, function(range, key){
                    start_range = $scope.validate_float(range[0]);
                    end_range = $scope.validate_float(range[1]);
                    price_range = $scope.validate_float(range[2]);
                    start_range = start_range > 0 ? start_range : 0;
                    end_range = end_range > 0 ? end_range : 0;
                    price_range = price_range > 0 ? price_range : 0;

                    if( !infinity_end_range && area >= start_range && ( end_range > start_range || end_range == 0 ) ){
                        area_in_range = ( area >= end_range && end_range != 0 ) ? ( end_range - prev_end_range ) : ( area - prev_end_range );
                        measurement_price += area_in_range * price_range;
                        prev_end_range = end_range;
                    }
                    if( end_range == 0 ) infinity_end_range = true;
                });

                if( angular.isDefined( origin_field.general.mesure_base_qty ) && origin_field.general.mesure_base_qty == 'y' ){
                    measurement_price /= ( qty != 0 ? qty : 1 );
                }

                return measurement_price;
            }
            angular.forEach(mesure_range, function(range, key){
                start_range = $scope.validate_float(range[0]);
                end_range = $scope.validate_float(range[1]);
                price_range = $scope.validate_float(range[2]);
                if( start_range <= area && ( area <= end_range || end_range == 0 ) ){
                    price_per_unit = price_range;
                }
                if( start_range <= area && key == ( mesure_range.length - 1 ) && area > end_range  ){
                    price_per_unit = price_range;
                }
            });
            if( angular.isDefined( origin_field.general.mesure_type ) && origin_field.general.mesure_type == 'r' ) return price_per_unit;
            return price_per_unit * _area;
        };
        $scope.calculate_product_area = function(){
            var area = 0, hasDim = false;
            angular.forEach($scope.nbd_fields, function(field, field_id){
                if( field.enable ){
                    var origin_field = $scope.get_field( field_id );
                    if( origin_field.general.data_type == 'i' ){
                        if( angular.isDefined(origin_field.nbd_type) && origin_field.nbd_type == 'dimension' 
                            && origin_field.general.mesure == 'y' && angular.isDefined(origin_field.general.mesure_range) && origin_field.general.mesure_range.length > 0 ){
                            area = $scope.validate_float( field.width ) * $scope.validate_float( field.height );
                            if( angular.isDefined(origin_field.general.mesure_min_area) ){
                                var minArea = $scope.validate_float( origin_field.general.mesure_min_area );
                                area -= minArea;
                            }
                            hasDim = true;
                        }
                    }
                }
            });
            if( !hasDim ){
                if( $scope.type == 'variable' ){
                    var variation_id = jQuery('input[name="variation_id"], input.variation_id').val(),
                    width = $scope.dimensions[variation_id].width,
                    height = $scope.dimensions[variation_id].height;
                }else{
                    width = $scope.width;
                    height = $scope.height;
                }
                area = $scope.validate_float( width ) * $scope.validate_float( height );
            }
            return area > 0 ? area : 0;
        };
        $scope.eval_price = function( formula, origin_field, qty, fields ){
            if( !formula ) return 0;

            var price = 0,
            area = $scope.calculate_product_area();

            formula = formula.replace(/{quantity}/g, qty);
            formula = formula.replace(/{price}/g, $scope.basePrice);
            formula = formula.replace(/{area}/g, area);
            formula = formula.replace(/{this.value}/g, fields[origin_field.id].value);
            formula = formula.replace(/{this.value_length}/g, fields[origin_field.id].value.length);

            if (formula.match(/\{(\s)*?field\.([^}]*)}/)) {
                var matches = formula.match(/\{(\s)*?field\.([^}]*)}/g),
                pos, reg, field_id, type, val;
                matches.forEach(function (field) {
                    match = field.match(/\{(\s)*?field\.([^}]*)}/);
                    if (undefined !== match[2] && "string" == typeof match[2]) {
                        pos = match[2].lastIndexOf(".");
                        val = 0;

                        if ( pos !== -1 ) {
                            field_id = match[2].substr(0, pos);
                            type = match[2].substr(pos + 1);

                            switch (type) {
                                case 'price':
                                    val = angular.isDefined( fields[field_id].price_val ) ? fields[field_id].price_val : 0;
                                    break;
                                case 'value':
                                    val = fields[field_id].value;
                                    break;
                                case 'value_length':
                                    val = fields[field_id].value.length;
                                    break;
                                case 'implicit_value':
                                    var _origin_field = $scope.get_field( field_id );
                                    if( _origin_field.general.data_type == 'm' ){
                                        var field_value = fields[field_id].value;
                                        var option = _origin_field.general.attributes.options[field_value];
                                        if( option && angular.isDefined( option.implicit_value ) ){
                                            val = option.implicit_value;
                                        }
                                    }
                                    break;
                                case 'sub_implicit_value':
                                    var _origin_field = $scope.get_field( field_id );
                                    if( _origin_field.general.data_type == 'm' ){
                                        var field_value = fields[field_id].value;
                                        var option = _origin_field.general.attributes.options[field_value];
                                        if( option ){
                                            if( angular.isDefined( option.enable_subattr ) && option.enable_subattr == 'on' 
                                                && angular.isDefined( option.sub_attributes ) && option.sub_attributes.length > 0 ){
                                                var sub_field_value = fields[field_id].sub_value;
                                                var sub_option = option.sub_attributes[sub_field_value];
                                                if( sub_option && angular.isDefined( sub_option.implicit_value ) ){
                                                    val = sub_option.implicit_value;
                                                }
                                            }
                                        }
                                    }
                                    break;
                            }
                        }

                        reg = new RegExp(match[0]);
                        formula = formula.replace(reg, val + '');
                    }
                });
            }

            try {
                price = mexp.eval(formula);
            } catch (e) {
                price = 0;
            }

            return price;
        };
        $scope.toggle_group = function( $event ){
            jQuery($event.target).parents( '.nbo-group-body' ).toggleClass('nbo-collapse');
            jQuery($event.target).parents( '.nbo-group-type2-wrap' ).toggleClass('nbo-collapse');
        };
        $scope.toggle_float_summary = function(){
            jQuery( '.nbo-float-summary' ).toggleClass('nbo-collapse');
        };
        $scope.toggle_field = function( $event ){
            jQuery($event.target).parents( '.nbd-option-field' ).toggleClass('nbo-collapse');
        };
        $scope.select_adv_attr = function( field_id, attr_index ){
            $scope.nbd_fields[field_id].value = attr_index;
            $scope.check_valid();
        };
        $scope.select_adv_subattr = function( field_id, attr_index, subattr_index ){
            $scope.nbd_fields[field_id].value = attr_index;
            $scope.nbd_fields[field_id].sub_value = subattr_index;
            $scope.check_valid();
        };
        $scope.changeGroupPanel = function( $event, command ){
            $timeout(function(){
                var wrapper = jQuery( '#' + nbOption.crtlId ).find('.nbo-fields-wrapper');
                if( command == 'prev' ){
                    if( $scope.current_group_panel > 0 ) $scope.current_group_panel--;
                }else if( command == 'next' ){
                    if( $scope.current_group_panel < ( $scope.no_of_group - 1 ) ) $scope.current_group_panel++;
                }else{
                    if( command >= 0 && command < $scope.no_of_group ){
                        $scope.current_group_panel = command;
                    }
                }
                var height = wrapper.find('.nbo-group-wrap:nth(' + ( $scope.current_group_panel ) + ')').outerHeight();
                if( wrapper.find('.nbo-group-type2-wrap').length ){
                    height = wrapper.find('.nbo-group-type2-wrap:nth(' + ( $scope.current_group_panel ) + ')').outerHeight();
                }
                wrapper.find('.nbo_group_panel_wrap').css('height', ( height + 15 ) + 'px');
            });
        };
        $scope.groupPageInit = false;
        $scope.currentGroupPage = 0;
        $scope.totalGroupPage = 1;
        $scope.groupTimeLineTranslate = '0%';
        $scope.initGroupTimeline = function(){
            $timeout(function(){
                var wrapper = jQuery( '#' + nbOption.crtlId ).find('.nbo-fields-wrapper'),
                timelineCon = wrapper.find('.nbo-group-timeline-wrap'),
                timelineLine = wrapper.find('.nbo-group-timeline-line'),
                containerWidth = timelineCon.innerWidth(),
                timelineWidth = timelineLine.outerWidth();

                if( timelineWidth > containerWidth ){
                    $scope.totalGroupPage++;
                    $timeout(function(){
                        containerWidth = timelineCon.innerWidth(),
                        timelineWidth = timelineLine.outerWidth();

                        $scope.totalGroupPage = Math.ceil( timelineWidth / containerWidth );
                        $scope.changeGroupPage( null, 0 );
                    });
                }else{
                    $scope.changeGroupPage( null, 0 );
                }
            });
        };
        $scope.changeGroupPage = function( $event, command ){
            if( command == 'prev' ){
                if( $scope.currentGroupPage > 0 ) $scope.currentGroupPage--;
            }else if( command == 'next' ){
                if( $scope.currentGroupPage < ( $scope.totalGroupPage - 1 ) ) $scope.currentGroupPage++;
            }else{
                if( command >= 0 && command < $scope.totalGroupPage ){
                    $scope.currentGroupPage = command;
                }
            }
            if( $scope.currentGroupPage == ( $scope.totalGroupPage - 1 ) ){
                var wrapper = jQuery( '#' + nbOption.crtlId ).find('.nbo-fields-wrapper');
                timelineCon = wrapper.find('.nbo-group-timeline-wrap'),
                timelineLine = wrapper.find('.nbo-group-timeline-line'),
                containerWidth = timelineCon.innerWidth(),
                timelineWidth = timelineLine.outerWidth();
                if( containerWidth < timelineWidth ) $scope.groupTimeLineTranslate = ( containerWidth - timelineWidth ) + 'px';
            }else{
                $scope.groupTimeLineTranslate = -$scope.currentGroupPage * 100 / $scope.totalGroupPage + '%';
            }
        };
        $scope.update_app = function(){
            if ($scope.$root.$$phase !== "$apply" && $scope.$root.$$phase !== "$digest") $scope.$apply(); 
        };
        $scope.init();
    }]).directive('stringToNumber', function() {
        return {
            require: 'ngModel',
            link: function(scope, element, attrs, ngModel) {
                ngModel.$parsers.push(function(value) {
                    if( value === null ) value = '';
                    return '' + value;
                });
                ngModel.$formatters.push(function(value) {
                    return parseFloat(value);
                });
            }
        };
    }).directive('convertToNumber', function() {
        return {
            require: 'ngModel',
            link: function(scope, element, attrs, ngModel) {
                ngModel.$parsers.push(function(val) {
                    return val != null ? parseInt(val, 10) : null;
                });
                ngModel.$formatters.push(function(val) {
                    return val != null ? '' + val : null;
                });
            }
        };
    }).directive('nboClickDebounce', function ($timeout) {
        var delay = 500;
        return {
            restrict: 'A',
            priority: -1,
            link: function (scope, elem) {
                var disabled = false;
                function onClick(evt) {
                    if (disabled) {
                        evt.preventDefault();
                        evt.stopImmediatePropagation();
                    } else {
                        disabled = true;
                        $timeout(function () { disabled = false; }, delay, false);
                    }
                }
                scope.$on('$destroy', function () { elem.off('click', onClick); });
                elem.on('click', onClick);
            }
        };
    }).directive( 'nbdHelpTip', function($timeout) {
        return {
            restrict: 'C',
            scope: {
                position: '@position'
            },
            link: function( scope, element, attrs ) {
                var tiptip_args = {
                    'attribute': 'data-tip',
                    'fadeIn': 50,
                    'fadeOut': 50,
                    'delay': 200,
                    defaultPosition: scope.position ? scope.position : "top"
                };
                $timeout(function() {
                    jQuery(element).tipTip( tiptip_args );
                }, 0);
            }
        };
    }).directive( 'nboAdvDropdown', function($timeout){
        return {
            restrict: 'A',
            link: function( scope, element, attrs ){
                $timeout(function() {
                    jQuery('body').click(function( event ){
                        jQuery.each( jQuery('.nbd-field-ad-dropdown-wrap'), function( ind, el ){
                            var re_el = jQuery( el ).find('.nbo-ad-result');
                            if( !( re_el.is( jQuery(event.target) ) 
                                    || jQuery( event.target ).parents('.nbo-ad-result').is( re_el )
                                    || jQuery(event.target).is( jQuery(element).find('.nbo-ad-pseudo-sublist-toggle') ) ) ){
                                jQuery( el ).removeClass('active');
                                jQuery( el ).find('.nbo-ad-pseudo-sublist-toggle').removeClass('nbo-rotate-180');
                                jQuery( el ).find('.nbo-ad-pseudo-sublist').removeClass('active');
                            }
                        });
                    });
                    jQuery(element).find('.nbo-ad-result').on('click', function(){
                        jQuery(element).toggleClass('active');
                    });
                    jQuery(element).find('.nbo-ad-pseudo-sublist-toggle').on('click', function(e){
                        e.stopPropagation();
                        var sublist_el = jQuery(this).next('.nbo-ad-pseudo-sublist');
                        jQuery.each( jQuery(element).find('.nbo-ad-pseudo-sublist'), function(){
                            if( !jQuery(this).is( sublist_el ) ){
                                jQuery(this).removeClass('active');
                                jQuery(this).prev('.nbo-ad-pseudo-sublist-toggle').removeClass('nbo-rotate-180');
                            }
                        });
                        jQuery(this).toggleClass('nbo-rotate-180');
                        sublist_el.toggleClass('active');
                    });
                });
            }
        }
    }).directive( 'nboInputFile', function($timeout, $window) {
        return {
            restrict: 'A',
            require: 'ngModel',
            scope: {
                fileChange: '&',
                fieldId: '@fieldId',
                types: '@types',
                file: '@',
                filename: '@',
                uploaded: '@',
                minsize: '@',
                maxsize: '@'
            },
            link: function( scope, element, attrs, ctrl ) {
                if( scope.uploaded == 1 ){
                    ClipboardEvent = $window.ClipboardEvent,
                    DataTransfer = $window.DataTransfer;
                    try {
                        var el = element[0];
                        if (ClipboardEvent || DataTransfer ){
                            var dT = new ClipboardEvent('').clipboardData || new DataTransfer();
                            dT.items.add(new File([scope.file], scope.filename));
                            el.files = dT.files;
                            onChange( 'init' );
                        }
                    }catch(err){
                        console.log(err);
                    }
                }
                element.on('change', onChange);
                scope.$on('destroy', function () {
                    element.off('change', onChange);
                });
                function onChange( init ) {
                    if( init != 'init' ){
                        var file = element[0].files[0];
                        if( file ){
                            function resetInput(){
                                ctrl.$setViewValue('');
                                jQuery(element).val('');
                                scope.fileChange();
                                return false;
                            };
                            if( scope.maxsize != '' ){
                                var max_size = parseInt( scope.maxsize ) * 1024 * 1024;
                                if( max_size < file.size ){
                                    alert("<?php _e('Sorry, file is too big, max size: ', 'web-to-print-online-designer'); ?>" + scope.maxsize + 'MB');
                                    resetInput();
                                }
                            }
                            if( scope.minsize != '' ){
                                var minsize = parseInt( scope.minsize ) * 1024 * 1024;
                                if( minsize > file.size ){
                                    alert("<?php _e('Sorry, file is too small, min size: ', 'web-to-print-online-designer'); ?>" + scope.minsize + 'MB');
                                    resetInput();
                                }
                            }
                            if( scope.types != '' ){
                                var types = scope.types.replace(/ /g,'').split(','),
                                filetype = file.type.toLowerCase(),
                                checType = false;
                                filetype = '';
                                filetype = filetype != '' ? filetype : file.name.substring(file.name.lastIndexOf('.')+1).toLowerCase();
                                angular.forEach(types, function(type){
                                    if( filetype.indexOf(type) > -1 ){
                                        checType = true;
                                    }
                                });
                                if( !checType ){
                                    alert("<?php _e('Sorry, this file type is not permitted for security reasons. Only accept: ', 'web-to-print-online-designer'); ?>" + scope.types);
                                    resetInput();
                                }
                            }
                        }
                    }
                    if( element[0].files[0] ){
                        ctrl.$setViewValue(element[0].files[0]);
                    }else{
                        ctrl.$setViewValue( '' );
                    }
                    jQuery(element).parent('.nbd-field-content').find('.nbd-upload-hidden').remove();
                    scope.fileChange();
                }
            }
        };
    }).directive( 'nboDisabled', function($timeout) {
        return {
            restrict: 'A',
            scope: {
                nboDisabled: '=',
                nboDisabledType: '@'
            },
            link: function( scope, element, attrs ) {
                function updateStatus(){
                    if( scope.nboDisabled ){
                        if( scope.nboDisabledType == 'attr' ){
                            jQuery(element).attr( 'disabled', true );
                        }else{
                            jQuery(element).addClass( 'nbo-disabled-wrap' );
                        }
                    }else{
                        if( scope.nboDisabledType == 'attr' ){
                            jQuery(element).removeAttr( 'disabled' );
                        }else{
                            jQuery(element).removeClass( 'nbo-disabled-wrap' );
                        }
                    }
                };

                $timeout(function() {
                    updateStatus();
                });

                scope.$watch('nboDisabled', function(newValue, oldValue){
                    if( newValue != oldValue ){
                        $timeout(function() {
                            updateStatus();
                        });
                    }
                }, true)
            }
        };
    }).filter('to_trusted', ['$sce', function($sce){
        return function(text) {
            var div = document.createElement('div');
            text += '';
            div.innerHTML = text;
            return $sce.trustAsHtml(div.textContent);
        };
    }]).filter('svg_trusted', ['$sce', function($sce){
        return function(text) {
            return $sce.trustAsHtml(text);
        };
    }]).filter('updateDimension', [function(){
        return function(val, args) {
            var validate_float = function(input){
                var output = parseFloat(input);
                if( isNaN(output) ) output = 0;
                return output;
            }
            if( args.curr ){
                current_val = validate_float( args.curr ),
                min_val = validate_float( args.min ),
                max_val = validate_float( args.max );
                current_val = ( current_val < min_val ) ? min_val : current_val;
                current_val = ( max_val != 0 && current_val > max_val ) ? max_val : current_val;
            }else{
                current_val = val;
            }
            var _scope = angular.element(document.getElementById(nbOption.crtlId)).scope();
            setTimeout(function(){
                _scope.update_dimensionvalue(args.fid, 'width');
                _scope.update_dimensionvalue(args.fid, 'height');
                setTimeout(function(){
                    _scope.check_valid();
                } , 300);
            }, 100);
            return current_val;
        };
    }]);
    <?php if( !$in_design_editor && !$in_nbau_mode_2 ) : ?>
        var appEl = document.getElementById('<?php echo $appid; ?>');
        angular.element(function() {
            angular.bootstrap(appEl, ['nboApp']);
        });
    <?php endif; ?>
    jQuery(document).on( 'update_nbo_options_from_builder', function(e, data){
        var $scope = angular.element(document.getElementById(nbOption.crtlId)).scope();
        angular.forEach(data.nbd_fields, function(nbd_field, field_id){
            $scope.nbd_fields[field_id].value = nbd_field.value;
            $scope.nbd_fields[field_id].sub_value = nbd_field.sub_value;
        });
        $scope.check_valid( true, true );
    });
    jQuery(document).on( 'update_product_image_from_builder', function(e, data){
        var $scope = angular.element(document.getElementById(nbOption.crtlId)).scope();
        $scope.change_product_image_without_field( data );
    });
    jQuery(document).on( 'update_nbo_options_from_advenced_upload', function(e, data){
        var $scope = angular.element(document.getElementById(nbOption.crtlId)).scope();
        angular.forEach(data.options, function(option){
            $scope.nbd_fields[option.field_id].value = option.value;
        });
        $scope.check_valid( true, true );
    });
    <?php if( $enable_gallery_api ): ?>
    jQuery(document).on( 'nbd_update_gallery', function(e, data){
        if( angular.isDefined( data.gallery ) ){
            var $scope = angular.element(document.getElementById(nbOption.crtlId)).scope();
            $scope.change_gallery_image( data.gallery, data.folder );
        }
    });
    <?php endif; ?>
</script>
</div>