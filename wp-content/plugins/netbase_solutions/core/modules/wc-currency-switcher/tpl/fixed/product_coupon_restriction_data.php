<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
if (!function_exists('nbtwccs_restriction_options')) {

    function nbtwccs_restriction_options($post_id, $curr_code, $type, $amouth_min = '', $amouth_max = '') {
        ?>
        <li id="nbtwccs_li_<?php echo $post_id ?>_<?php echo $curr_code ?>">
            <div class="nbtwccs_price_col">
                <p class="form-field form-row restriction_min__field">
                    <label for="nbtwccs_restriction_min_<?php echo $post_id ?>_<?php echo $curr_code ?>"><?php _e('Minimum spend', 'netbase_solutions') ?>&nbsp;(<b><?php echo $curr_code ?></b>):</label>
                    <input type="text" class="short wc_input_price" name="nbtwccs_restriction_min[<?php echo $post_id ?>][<?php echo $curr_code ?>]" id="nbtwccs_restriction_min_<?php echo $post_id ?>_<?php echo $curr_code ?>" value="<?php echo($amouth_min > 0 ? $amouth_min : '') ?>" placeholder="<?php _e('auto', 'netbase_solutions') ?>">
                </p>
                <p class="form-field form-row _restriction_max_field">
                    <label for="nbtwccs_restriction_max_<?php echo $post_id ?>_<?php echo $curr_code ?>"><?php _e('Maximum spend', 'netbase_solutions') ?>&nbsp;(<b><?php echo $curr_code ?></b>):</label>
                    <input type="text" class="short wc_input_price" name="nbtwccs_restriction_max[<?php echo $post_id ?>][<?php echo $curr_code ?>]" id="nbtwccs_restriction_max_<?php echo $post_id ?>_<?php echo $curr_code ?>" value="<?php echo($amouth_max > 0 ? $amouth_max : '') ?>" placeholder="<?php _e('auto', 'netbase_solutions') ?>">
                </p>
            </div>
            <div class="nbtwccs_price_col">
                <p class="form-row">
                    <a href="javascript:nbtwccs_remove_li_fixed_field(<?php echo $post_id ?>,'<?php echo $curr_code ?>',false,'<?php echo $type ?>');void(0);" class="button"><?php _e('Remove', 'netbase_solutions') ?></a>
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
            <li><a href="javascript:nbtwccs_open_tab('nbtwccs_tab_fixed_restriction',<?php echo $post_id ?>);void(0)" id="nbtwccs_tab_fixed_restriction_btn_<?php echo $post_id ?>" class="nbtwccs_tab_button button"><?php _e('The coupon fixed Minimum and Maximum spend rules', 'netbase_solutions') ?></a></li>
        <?php endif; ?>

    </ul>

    <input type="hidden" name="nbtwccs_restriction_min[<?php echo $post_id ?>]" value="" />
    <input type="hidden" name="nbtwccs_restriction_max[<?php echo $post_id ?>]" value="" />   

    <?php if ($is_fixed_enabled): ?>
        <div id="nbtwccs_tab_fixed_restriction_<?php echo $post_id ?>" class="nbtwccs_tab">
            <h4><?php _e('NBTWCCS - the <b>fixed</b> Minimum and Maximum spend ', 'netbase_solutions') ?><img class="help_tip" data-tip="<?php _e('Here you can set FIXED amount for the coupon for any currency you want. In the case of empty amount field recounting by rate will work!', 'netbase_solutions') ?>" src="<?php echo NBTWCCS_LINK ?>/assets/img/help.png" height="16" width="16" /></h4>
            <select class="select short" id="nbtwccs_multiple_simple_select_<?php echo $type ?>_<?php echo $post_id ?>">
                <?php foreach ($currencies as $code => $curr): ?>
                    <?php
                    if ($code === $default_currency OR ( $this->is_exists($post_id, $code, 'min_spend') OR $this->is_exists($post_id, $code, 'max_spend'))) {
                        continue;
                    }
                    ?>
                    <option value="<?php echo $code ?>"><?php echo $code ?></option>
                <?php endforeach; ?>
            </select>
            &nbsp;<a href="javascript:nbtwccs_add_fixed_field(<?php echo $post_id ?>,'<?php echo $type ?>');void(0);" class="button"><?php _e('Add', 'netbase_solutions') ?></a>
            &nbsp;<a href="javascript:nbtwccs_add_all_fixed_field(<?php echo $post_id ?>,'<?php echo $type ?>');void(0);" class="button"><?php _e('Add all', 'netbase_solutions') ?></a>
            <br />
            <br />
            <hr style="clear: both; overflow: hidden;" />
            <ul id="nbtwccs_multiple_simple_list_<?php echo $type ?>_<?php echo $post_id ?>">
                <?php
                foreach ($currencies as $code => $curr) {
                    if ($this->is_exists($post_id, $code, 'min_spend') OR $this->is_exists($post_id, $code, 'max_spend')) {
                        nbtwccs_restriction_options($post_id, $code, $type, $this->get_value($post_id, $code, 'min_spend'), $this->get_value($post_id, $code, 'max_spend'));
                    }
                }
                ?>
            </ul>
            <div id="nbtwccs_multiple_simple_tpl_<?php echo $type ?>" >
                <?php nbtwccs_restriction_options('__POST_ID__', '__CURR_CODE__', $type) ?>
            </div>
        </div>
    <?php endif; ?>
    
</div>

