<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
if (!function_exists('nbtwccs_coupon_options'))
{
    function nbtwccs_coupon_options($post_id, $curr_code, $amount = '')
    {
        ?>
        <li id="nbtwccs_li_<?php echo $post_id ?>_<?php echo $curr_code ?>">
            <div class="nbtwccs_price_col">
                <p class="form-field form-row _regular_price_field">
                    <label for="nbtwccs_amount_<?php echo $post_id ?>_<?php echo $curr_code ?>"><?php _e('Coupon amount', 'netbase_solutions') ?>&nbsp;(<b><?php echo $curr_code ?></b>):</label>
                    <input type="text" class="short wc_input_price" name="nbtwccs_fixed_coupon[<?php echo $post_id ?>][<?php echo $curr_code ?>]" id="nbtwccs_amount_<?php echo $post_id ?>_<?php echo $curr_code ?>" value="<?php echo($amount > 0 ? $amount : '') ?>" placeholder="<?php _e('auto', 'netbase_solutions') ?>">
                </p>
            </div>
            <div class="nbtwccs_price_col">
                <p class="form-row">
                    <a href="javascript:nbtwccs_remove_li_product_price(<?php echo $post_id ?>,'<?php echo $curr_code ?>',false);void(0);" class="button"><?php _e('Remove', 'netbase_solutions') ?></a>
                </p>
            </div>
        </li>
        <?php
    }

}
?>        
<div class="nbtwccs_multiple_simple_panel options_group pricing woocommerce_variation" >
    <ul class="nbtwccs_tab_navbar">
        <?php if ($is_fixed_enabled): ?>
            <li><a href="javascript:nbtwccs_open_tab('nbtwccs_tab_fixed',<?php echo $post_id ?>);void(0)" id="nbtwccs_tab_fixed_btn_<?php echo $post_id ?>" class="nbtwccs_tab_button button"><?php _e('The coupon fixed amount rules', 'netbase_solutions') ?></a></li>
        <?php endif; ?>

    </ul>
    <input type="hidden" name="nbtwccs_fixed_coupon[<?php echo $post_id ?>]" value="" />
    <?php if ($is_fixed_enabled): ?>
        <div id="nbtwccs_tab_fixed_<?php echo $post_id ?>" class="nbtwccs_tab">
            <h4><?php _e('NBTWCCS - the coupon <b>fixed</b> amount', 'netbase_solutions') ?><img class="help_tip" data-tip="<?php _e('Here you can set FIXED amount for the coupon for any currency you want. In the case of empty amount field recounting by rate will work!', 'netbase_solutions') ?>" src="<?php echo NBTWCCS_LINK ?>/assets/img/help.png" height="16" width="16" /></h4>
            <select class="select short" id="nbtwccs_multiple_simple_select_<?php echo $post_id ?>">
                <?php foreach ($currencies as $code => $curr): ?>
                    <?php
                    if ($code === $default_currency OR $this->is_exists($post_id, $code, 'amount'))
                    {
                        continue;
                    }
                    ?>
                    <option value="<?php echo $code ?>"><?php echo $code ?></option>
                <?php endforeach; ?>
            </select>
            &nbsp;<a href="javascript:nbtwccs_add_product_price(<?php echo $post_id ?>);void(0);" class="button"><?php _e('Add', 'netbase_solutions') ?></a>
            &nbsp;<a href="javascript:nbtwccs_add_all_product_price(<?php echo $post_id ?>);void(0);" class="button"><?php _e('Add all', 'netbase_solutions') ?></a>
            <br />
            <br />
            <hr style="clear: both; overflow: hidden;" />
            <ul id="nbtwccs_multiple_simple_list_<?php echo $post_id ?>">
                <?php
                foreach ($currencies as $code => $curr)
                {
                    if ($this->is_exists($post_id, $code, 'amount'))
                    {
                        nbtwccs_coupon_options($post_id, $code, $this->get_value($post_id, $code, 'amount'));
                    }
                }
                ?>
            </ul>
            <div id="nbtwccs_multiple_simple_tpl">
                <?php nbtwccs_coupon_options('__POST_ID__', '__CURR_CODE__') ?>
            </div>
        </div>
    <?php endif; ?>    
</div>