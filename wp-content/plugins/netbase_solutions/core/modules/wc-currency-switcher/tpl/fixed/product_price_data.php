<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
if (!function_exists('nbtwccs_price_options'))
{

    function nbtwccs_price_options($post_id, $curr_code, $value_regular = '', $value_sale = '')
    {
        ?>
        <li id="nbtwccs_li_<?php echo $post_id ?>_<?php echo $curr_code ?>">
            <div class="nbtwccs_price_col">
                <p class="form-field form-row _regular_price_field">
                    <label for="nbtwccs_regular_<?php echo $post_id ?>_<?php echo $curr_code ?>"><?php _e('Regular price', 'netbase_solutions') ?>&nbsp;(<b><?php echo $curr_code ?></b>):</label>
                    <input type="text" class="short wc_input_price" name="nbtwccs_regular_price[<?php echo $post_id ?>][<?php echo $curr_code ?>]" id="nbtwccs_regular_<?php echo $post_id ?>_<?php echo $curr_code ?>" value="<?php echo($value_regular > 0 ? $value_regular : '') ?>" placeholder="<?php _e('auto', 'netbase_solutions') ?>">
                </p>
            </div>
            <div class="nbtwccs_price_col">
                <p class="form-field form-row _sale_price_field">
                    <label for="nbtwccs_sale_<?php echo $post_id ?>_<?php echo $curr_code ?>"><?php _e('Sale price', 'netbase_solutions') ?>&nbsp;(<b><?php echo $curr_code ?></b>):</label>
                    <input type="text" class="short wc_input_price" name="nbtwccs_sale_price[<?php echo $post_id ?>][<?php echo $curr_code ?>]" id="nbtwccs_sale_<?php echo $post_id ?>_<?php echo $curr_code ?>" value="<?php echo($value_sale > 0 ? $value_sale : '') ?>" placeholder="<?php _e('auto', 'netbase_solutions') ?>">
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

if (!function_exists('nbtwccs_price_options_geo'))
{

    function nbtwccs_price_options_geo($post_id, $index, $countries_selected, $value_regular = '', $value_sale = '')
    {
        ?>
        <li id="nbtwccs_li_geo_<?php echo $post_id ?>_<?php echo $index ?>">
            <div class="nbtwccs_price_col">
                <p class="form-field form-row _regular_price_field">
                    <label for="nbtwccs_regular_geo_<?php echo $post_id ?>_<?php echo $index ?>"><?php _e('Regular price', 'netbase_solutions') ?>&nbsp;(<b><?php echo get_woocommerce_currency_symbol(); ?></b>):</label>
                    <input type="text" class="short wc_input_price" name="nbtwccs_regular_price_geo[<?php echo $post_id ?>][<?php echo $index ?>]" id="nbtwccs_regular_geo_<?php echo $post_id ?>_<?php echo $index ?>" value="<?php echo($value_regular > 0 ? $value_regular : '') ?>" placeholder="<?php _e('auto', 'netbase_solutions') ?>">
                </p>
            </div>
            <div class="nbtwccs_price_col">
                <p class="form-field form-row _sale_price_field">
                    <label for="nbtwccs_sale_geo_<?php echo $post_id ?>_<?php echo $index ?>"><?php _e('Sale price', 'netbase_solutions') ?>&nbsp;(<b><?php echo get_woocommerce_currency_symbol(); ?></b>):</label>
                    <input type="text" class="short wc_input_price" name="nbtwccs_sale_price_geo[<?php echo $post_id ?>][<?php echo $index ?>]" id="nbtwccs_sale_geo_<?php echo $post_id ?>_<?php echo $index ?>" value="<?php echo($value_sale > 0 ? $value_sale : '') ?>" placeholder="<?php _e('auto', 'netbase_solutions') ?>">
                </p>
            </div>            
            <div class="nbtwccs_price_col">
                <p class="form-row">
                    <a href="javascript:nbtwccs_remove_li_product_price(<?php echo $post_id ?>,'<?php echo $index ?>', true);void(0);" class="button"><?php _e('Remove', 'netbase_solutions') ?></a>
                </p>
            </div>
            <div style="clear: both;">
                <p class="form-row">
                    <?php $c = new WC_Countries(); ?>
                    <select name="nbtwccs_price_geo_countries[<?php echo $post_id ?>][<?php echo $index ?>][]" multiple="" size="1" style="width: 80%;" <?php if ($index !== '__INDEX__'): ?>class="chosen_select"<?php endif; ?> data-placeholder="<?php _e('select some countries', 'netbase_solutions') ?>">
                        <option value="0"></option>
                        <?php foreach ($c->get_countries() as $key => $value): ?>
                            <option <?php echo(in_array($key, $countries_selected) ? 'selected=""' : '') ?> value="<?php echo $key ?>"><?php echo $value ?></option>
                        <?php endforeach; ?>
                    </select>
                </p>
            </div>
        </li>
        <?php
    }

}
?>

<div class="nbtwccs_multiple_simple_panel options_group pricing woocommerce_variation" style="<?php if ($type == 'simple'): ?>display: none;<?php endif; ?>">

    <ul class="nbtwccs_tab_navbar">
        <?php if ($is_fixed_enabled): ?>
            <li><a href="javascript:nbtwccs_open_tab('nbtwccs_tab_fixed',<?php echo $post_id ?>);void(0)" id="nbtwccs_tab_fixed_btn_<?php echo $post_id ?>" class="nbtwccs_tab_button button"><?php _e('The product fixed prices rules', 'netbase_solutions') ?></a></li>
        <?php endif; ?>

        <?php if ($is_geoip_manipulation): ?>
            <li><a href="javascript:nbtwccs_open_tab('nbtwccs_tab_geo',<?php echo $post_id ?>);void(0)" id="nbtwccs_tab_geo_btn_<?php echo $post_id ?>" class="nbtwccs_tab_button button"><?php _e('The product custom GeoIP rules', 'netbase_solutions') ?></a></li>
        <?php endif; ?>
    </ul>

    <input type="hidden" name="nbtwccs_regular_price[<?php echo $post_id ?>]" value="" />
    <input type="hidden" name="nbtwccs_sale_price[<?php echo $post_id ?>]" value="" />
    <input type="hidden" name="nbtwccs_regular_price_geo[<?php echo $post_id ?>]" value="" />
    <input type="hidden" name="nbtwccs_sale_price_geo[<?php echo $post_id ?>]" value="" />
    <input type="hidden" name="nbtwccs_price_geo_countries[<?php echo $post_id ?>]" value="" />

    <!---------------------------------------------------------------->

    <?php if ($is_fixed_enabled): ?>
        <div id="nbtwccs_tab_fixed_<?php echo $post_id ?>" class="nbtwccs_tab">
            <h4><?php _e('NBTWCCS - the product <b>fixed</b> prices', 'netbase_solutions') ?><img class="help_tip" data-tip="<?php _e('Here you can set FIXED price for the product for any currency you want. In the case of empty price field recounting by rate will work!', 'netbase_solutions') ?>" src="<?php echo NBTWCCS_LINK ?>/assets/img/help.png" height="16" width="16" /></h4>
            <select class="select short" id="nbtwccs_multiple_simple_select_<?php echo $post_id ?>">
                <?php foreach ($currencies as $code => $curr): ?>
                    <?php
                    if ($code === $default_currency OR $this->is_exists($post_id, $code, 'regular'))
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
                    if ($this->is_exists($post_id, $code, 'regular'))
                    {
                        nbtwccs_price_options($post_id, $code, $this->get_value($post_id, $code, 'regular'), $this->get_value($post_id, $code, 'sale'));
                    }
                }
                ?>
            </ul>
            <div id="nbtwccs_multiple_simple_tpl">
                <?php nbtwccs_price_options('__POST_ID__', '__CURR_CODE__') ?>
            </div>
        </div>
    <?php endif; ?>

    <!---------------------------------------------------------------->

    <?php if ($is_geoip_manipulation): ?>
        <div id="nbtwccs_tab_geo_<?php echo $post_id ?>" class="nbtwccs_tab">
            <h4><?php _e('NBTWCCS - the product custom GeoIP rules', 'netbase_solutions') ?><img class="help_tip" data-tip="<?php _e('Here you can set prices in the basic currency for different countries, and recount will be done relatively of this values. ATTENTION: fixed price for currencies has higher priority!', 'netbase_solutions') ?>" src="<?php echo NBTWCCS_LINK ?>/assets/img/help.png" height="16" width="16" /></h4>

            <a href="javascript: nbtwccs_add_group_geo(<?php echo $post_id ?>);void(0);" class="button"><?php _e('Add group', 'netbase_solutions') ?></a>
            <ul id="nbtwccs_multiple_simple_list_geo_<?php echo $post_id ?>">
                <?php
                if (!empty($product_geo_data) AND ! empty($product_geo_data['price_geo_countries']))
                {
                    foreach ($product_geo_data['price_geo_countries'] as $index => $countries_selected)
                    {
                        if ($index == 0)
                        {
                            continue;
                        }

                        nbtwccs_price_options_geo($post_id, $index, (array) $countries_selected, $product_geo_data['regular_price_geo'][$index], $product_geo_data['sale_price_geo'][$index]);
                    }
                }
                ?>
            </ul>

            <div id="nbtwccs_multiple_simple_tpl_geo">
                <?php nbtwccs_price_options_geo('__POST_ID__', '__INDEX__', array()) ?>
            </div>
        </div>
    <?php endif; ?>
</div>