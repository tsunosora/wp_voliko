<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<div class="nbtwccs-admin-preloader"></div>
<div class="subsubsub_section">
    <br class="clear" />
    <?php
    global $NBTWCCS;

    $welcome_curr_options = array();
    if (!empty($currencies) AND is_array($currencies)) {
        foreach ($currencies as $key => $currency) {
            $welcome_curr_options[$currency['name']] = $currency['name'];
        }
    }
    
    $pd = array();
    $countries = array();
    if (class_exists('WC_Geolocation')) {
        $c = new WC_Countries();
        $countries = $c->get_countries();
        $pd = WC_Geolocation::geolocate_ip();
    }
    
    $options = array(
        array(
            'name' => '',
            'type' => 'title',
            'desc' => '',
            'id' => 'nbtwccs_general_settings'
        ),
        array(
            'name' => __('Drop-down view', 'netbase_solutions'),
            'desc' => __('How to display currency switcher drop-down on the front of your site', 'netbase_solutions'),
            'id' => 'nbtwccs_drop_down_view',
            'type' => 'select',
            'class' => 'chosen_select',
            'css' => 'min-width:300px;',
            'options' => array(               
                'no' => __('simple drop-down', 'netbase_solutions'),
                'flags' => __('show as flags', 'netbase_solutions'),
            ),
            'desc_tip' => true
        ),
        /*array(
            'name' => __('Show flags by default', 'netbase_solutions'),
            'desc' => __('Show/hide flags on the front drop-down', 'netbase_solutions'),
            'id' => 'nbtwccs_show_flags',
            'type' => 'select',
            'class' => 'chosen_select',
            'css' => 'min-width:300px;',
            'options' => array(
                0 => __('No', 'netbase_solutions'),
                1 => __('Yes', 'netbase_solutions')
            ),
            'desc_tip' => true
        ),*/
        array(
            'name' => __('Show money signs', 'netbase_solutions'),
            'desc' => __('Show/hide money signs on the front drop-down', 'netbase_solutions'),
            'id' => 'nbtwccs_show_money_signs',
            'type' => 'select',
            'class' => 'chosen_select',
            'css' => 'min-width:300px;',
            'options' => array(
                0 => __('No', 'netbase_solutions'),
                1 => __('Yes', 'netbase_solutions')
            ),
            'desc_tip' => true
        ),
        array(
            'name' => __('Is multiple allowed', 'netbase_solutions'),
            'desc' => __('Customer will pay with selected currency (Yes) or with default currency (No).', 'netbase_solutions'),
            'id' => 'nbtwccs_is_multiple_allowed',
            'type' => 'select',
            'class' => 'chosen_select',
            'css' => 'min-width:300px;',
            'options' => array(
                0 => __('No', 'netbase_solutions'),
                1 => __('Yes', 'netbase_solutions'),
            ),
            'desc_tip' => false
        ),
        /*array(
            'name' => __('Show price info icon', 'netbase_solutions'),
            'desc' => __('Show info icon near the price of the product which while its under hover shows prices of products in all currencies', 'netbase_solutions'),
            'id' => 'nbtwccs_price_info',
            'type' => 'select',
            'class' => 'chosen_select',
            'css' => 'min-width:300px;',
            'options' => array(
                0 => __('No', 'netbase_solutions'),
                1 => __('Yes', 'netbase_solutions')
            ),
            'desc_tip' => true
        ),*/
        array(
            'name' => __('Welcome currency', 'netbase_solutions'),
            'desc' => __('In wich currency show prices for first visit of your customer on your site', 'netbase_solutions'),
            'id' => 'nbtwccs_welcome_currency',
            'type' => 'select',
            'class' => 'chosen_select',
            'css' => 'min-width:300px;',
            'options' => $welcome_curr_options,
            'desc_tip' => false
        ),
        array(
            'name' => __('Currency aggregator', 'netbase_solutions'),
            'desc' => __('Currency aggregators', 'netbase_solutions'),
            'id' => 'nbtwccs_currencies_aggregator',
            'type' => 'select',
            'class' => 'chosen_select',
            'css' => 'min-width:300px;',
            'options' => array(
                //'yahoo' => 'www.finance.yahoo.com',
                'free_converter' => 'The Free Currency Converter',
                'google' => 'www.google.com/finance',
                'ecb' => 'www.ecb.europa.eu',
                'rf' => 'www.cbr.ru - russian centrobank',
                'privatbank' => 'api.privatbank.ua - ukrainian privatbank',
                'bank_polski' => 'Narodowy Bank Polsky',
                
            ),
            'desc_tip' => true
        ),
        
        array(
            'name' => __('Currency storage', 'netbase_solutions'),
            'desc' => __('In some servers there is troubles with sessions, and after currency selecting its reset to welcome currency or geo ip currency. In such case use transient!', 'netbase_solutions'),
            'id' => 'nbtwccs_storage',
            'type' => 'select',
            'class' => 'chosen_select',
            'css' => 'min-width:300px;',
            'options' => array(
                'session' => __('session', 'netbase_solutions'),
                //'cookie' => __('cookie', 'netbase_solutions'),
                'transient' => __('transient', 'netbase_solutions')
            ),
            'desc_tip' => false
        ),
        array(
            'name' => __('Rate auto update', 'netbase_solutions'),
            'desc' => __('Currencies rate auto update by wp cron. Use it for your own risk!', 'netbase_solutions'),
            'id' => 'nbtwccs_currencies_rate_auto_update',
            'type' => 'select',
            'class' => 'chosen_select',
            'css' => 'min-width:300px;',
            'options' => array(
                'no' => __('no auto update', 'netbase_solutions'),
                'hourly' => __('hourly', 'netbase_solutions'),
                'twicedaily' => __('twicedaily', 'netbase_solutions'),
                'daily' => __('daily', 'netbase_solutions'),
                'week' => __('weekly', 'netbase_solutions'),
                'month' => __('monthly', 'netbase_solutions'),
                'min1' => __('special: each minute', 'netbase_solutions'), //for tests
                'min5' => __('special: each 5 minutes', 'netbase_solutions'), //for tests
                'min15' => __('special: each 15 minutes', 'netbase_solutions'), //for tests
                'min30' => __('special: each 30 minutes', 'netbase_solutions'), //for tests
                'min45' => __('special: each 45 minutes', 'netbase_solutions'), //for tests
            ),
            'desc_tip' => false
        ),
        
        array(
            'name' => __('Hide switcher on checkout page', 'netbase_solutions'),
            'desc' => __('Hide switcher on checkout page for any of your reason. Better restrike for users change currency on checkout page in multiple mode.', 'netbase_solutions'),
            'id' => 'nbtwccs_restrike_on_checkout_page',
            'type' => 'select',
            'class' => 'chosen_select',
            'css' => 'min-width:300px;',
            'options' => array(
                0 => __('No', 'netbase_solutions'),
                1 => __('Yes', 'netbase_solutions'),
            ),
            'desc_tip' => true
        ),
        /*array(
            'name' => __('Show approx. amount', 'netbase_solutions'),
            'desc' => __('THIS IS AN EXPERIMENTAL FEATURE! Show approximate amount on the checkout and the cart page with currency of user defined by IP in the GeoIp rules tab. Works only with currencies rates data and NOT with fixed prices rules and geo rules.', 'netbase_solutions'),
            'id' => 'nbtwccs_show_approximate_amount',
            'type' => 'select',
            'class' => 'chosen_select',
            'css' => 'min-width:300px;',
            'options' => array(
                0 => __('No', 'netbase_solutions'),
                1 => __('Yes', 'netbase_solutions'),
            ),
            'desc_tip' => true
        ),*/
        array(
            'name' => __('I am using cache plugin on my site', 'netbase_solutions'),
            'desc' => __('Set Yes here ONLY if you are REALLY use cache plugin for your site, for example like Super cache or Hiper cache (doesn matter). + Set "Custom price format", for example: __PRICE__ (__CODE__). After enabling this feature - clean your cache to make it works. It will allow show prices in selected currency on all pages of site. Fee for this feature - additional AJAX queries for products prices redrawing.', 'netbase_solutions'),
            'id' => 'nbtwccs_shop_is_cached',
            'type' => 'select',
            'class' => 'chosen_select',
            'css' => 'min-width:300px;',
            'options' => array(
                0 => __('No', 'netbase_solutions'),
                1 => __('Yes', 'netbase_solutions'),
            ),
            'desc_tip' => true
        ),
        
        array('type' => 'sectionend', 'id' => 'nbtwccs_general_settings')
    );
    ?>
    <div class="section">
        <h3 style="margin-bottom: 1px;"><?php esc_html_e( 'WooCommerce Currency Switcher', 'netbase_solutions') ?></h3>
        <br />
        <div id="tabs" class="wfc-tabs wfc-tabs-style-shape" >
            <?php if (version_compare(WOOCOMMERCE_VERSION, NBTWCCS_MIN_WOOCOMMERCE, '<')): ?>
                <b style="color: red;"><?php printf(__("Your version of WooCommerce plugin is too obsolete. Update minimum to %s version to avoid malfunctionality!", 'netbase_solutions'), NBTWCCS_MIN_WOOCOMMERCE) ?></b><br />
            <?php endif; ?>

            <input type="hidden" name="nbtwccs_woo_version" value="<?php echo WOOCOMMERCE_VERSION ?>" />
            <nav>
                <ul>
                    <li class="tab-current">
                        <a href="#tabs-1">
                            
                            <span><?php _e("Currencies", 'netbase_solutions') ?></span>
                        </a>
                    </li><li>
                        <a href="#tabs-2">
                            
                            <span><?php _e("Options", 'netbase_solutions') ?></span>
                        </a>
                    </li>
                    
                    <?php //if ($this->is_use_geo_rules()): ?>
                        <!-- <li>
                            <a href="#tabs-4">
                                
                                <span><?php //_e("GeoIP rules", 'netbase_solutions') ?></span>
                            </a>
                        </li> -->
                    <?php //endif; ?>
                    
                </ul>
            </nav>
            <div class="content-wrap">
                <section id="tabs-1" class="content-current">
                    <div class="wcf-control-section">
                        <a href="#" class="button" id="nbtwccs_add_currency"><?php _e("Add Currency", 'netbase_solutions') ?></a><br />

                        <div style="display: none;">
                            <div id="nbtwccs_item_tpl"><?php
                                $empty = array(
                                    'name' => '',
                                    'rate' => 0,
                                    'symbol' => '',
                                    'position' => '',
                                    'is_etalon' => 0,
                                    'description' => '',
                                    'hide_cents' => 0
                                );
                                nbtwccs_print_currency($this, $empty);
                                ?>
                            </div>
                        </div>

                        <ul id="nbtwccs_list">
                            <?php
                            if (!empty($currencies) AND is_array($currencies)) {
                                foreach ($currencies as $key => $currency) {
                                    nbtwccs_print_currency($this, $currency);
                                }
                            }
                            ?>
                        </ul>

                        <div>
                            <a href="http://en.wikipedia.org/wiki/ISO_4217#Active_codes" target="_blank"><?php _e("Read wiki about Currency Active codes", 'netbase_solutions') ?></a>
                        </div>

                        <div style="clear: both;"></div>

                    </div>
                </section>
                <section id="tabs-2">
                    <div class="wfc-control-section-xxx">
                        <?php woocommerce_admin_fields($options); ?>
                    </div>
                </section>

                

                <?php if ($this->is_use_geo_rules()): ?>
                    <section id="tabs-4">
                        <?php if (version_compare(WOOCOMMERCE_VERSION, '2.3', '<')): ?>

                            <b style="color: red;"><?php _e("GeoIP works from v.2.3 of the WooCommerce plugin and no with minor versions of WooCommerce!!", 'netbase_solutions'); ?></b><br />

                        <?php endif; ?>

                        <?php if (empty($pd)): ?>

                            <b style="color: red;"><?php _e("WooCommerce GeoIP functionality doesn't work on your site. Maybe <a href='https://wordpress.org/support/topic/geolocation-not-working-1/?replies=10' target='_blank'>this</a> will help you.", 'netbase_solutions'); ?></b><br />

                        <?php endif; ?>
                        <ul>
                            <?php
                            if (!empty($currencies) AND is_array($currencies)) {
                                foreach ($currencies as $key => $currency) {
                                    $rules = array();
                                    if (isset($geo_rules[$key])) {
                                        $rules = $geo_rules[$key];
                                    }
                                    ?>
                                    <li>
                                        <table style="width: 100%;">
                                            <tr>
                                                <td>
                                                    <div style="width: 70px;<?php if ($currency['is_etalon']): ?>color: red;<?php endif; ?>"><strong><?php echo $key ?></strong>:</div>
                                                </td>
                                                <td style="width: 100%;">
                                                    <select name="nbtwccs_geo_rules[<?php echo $currency['name'] ?>][]" multiple="" size="1" style="max-width: 100%;" class="chosen_select">
                                                        <option value="0"></option>
                                                        <?php foreach ($countries as $key => $value): ?>
                                                            <option <?php echo(in_array($key, $rules) ? 'selected=""' : '') ?> value="<?php echo $key ?>"><?php echo $value ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                    </section>
                <?php else: ?>
                    <input type="hidden" name="nbtwccs_geo_rules" value="" />
                <?php endif; ?>
            </div>
        </div>
    </div>   

    <div class="info_popup" style="display: none;"></div>

</div>
<script type="text/javascript">
    (function ($, window) {

        'use strict';

        $.fn.wfcTabs = function (options) {

            if (!this.length)
                return;

            return this.each(function () {

                var $this = $(this);

                ({
                    init: function () {
                        this.tabsNav = $this.children('nav');
                        this.items = $this.children('.content-wrap').children('section');
                        this._show();
                        this._initEvents();
                    },
                    _initEvents: function () {
                        var self = this;
                        this.tabsNav.on('click', 'a', function (e) {
                            e.preventDefault();
                            self._show($(this));
                        });
                    },
                    _show: function (element) {

                        if (element == undefined) {
                            this.firsTab = this.tabsNav.find('li').first();
                            this.firstSection = this.items.first();

                            if (!this.firsTab.hasClass('tab-current')) {
                                this.firsTab.addClass('tab-current');
                            }

                            if (!this.firstSection.hasClass('content-current')) {
                                this.firstSection.addClass('content-current');
                            }
                        }

                        var $this = $(element),
                                $to = $($this.attr('href'));

                        if ($to.length) {
                            $this.parent('li').siblings().removeClass().end().addClass('tab-current');
                            $to.siblings().removeClass().end().addClass('content-current');
                        }

                    }

                }).init();

            });
        }

    })(jQuery, window);

    jQuery('.wfc-tabs').wfcTabs();
    jQuery(function () {

        jQuery.fn.life = function (types, data, fn) {
            jQuery(this.context).on(types, this.selector, data, fn);
            return this;
        };        

        jQuery('body').append('<div id="nbtwccs_buffer" style="display: none;"></div>');

        jQuery("#nbtwccs_list").sortable();

        jQuery('#nbtwccs_add_currency').life('click', function () {
            jQuery('#nbtwccs_list').append(jQuery('#nbtwccs_item_tpl').html());
            return false;
        });
        jQuery('.nbtwccs_del_currency').life('click', function () {
            jQuery(this).parents('li').hide(220, function () {
                jQuery(this).remove();
            });
            return false;
        });

        jQuery('.nbtwccs_is_etalon').life('click', function () {
            jQuery('.nbtwccs_is_etalon').next('input[type=hidden]').val(0);
            jQuery('.nbtwccs_is_etalon').prop('checked', 0);
            jQuery(this).next('input[type=hidden]').val(1);
            jQuery(this).prop('checked', 1);
            /*instant save*/
            var currency_name = jQuery(this).parent().find('input[name="nbtwccs_name[]"]').val();
            if (currency_name.length) {
                nbtwccs_show_stat_info_popup('Loading ...');
                var data = {
                    action: "nbtwccs_save_etalon",
                    currency_name: currency_name
                };
                jQuery.post(ajaxurl, data, function (request) {
                    try {
                        request = jQuery.parseJSON(request);
                        jQuery.each(request, function (index, value) {
                            var elem = jQuery('input[name="nbtwccs_name[]"]').filter(function () {
                                return this.value.toUpperCase() == index;
                            });

                            if (elem) {
                                jQuery(elem).parent().find('input[name="nbtwccs_rate[]"]').val(value);
                                jQuery(elem).parent().find('input[name="nbtwccs_rate[]"]').text(value);
                            }
                        });

                        nbtwccs_hide_stat_info_popup();
                        nbtwccs_show_info_popup('Save changes please!', 1999);
                    } catch (e) {
                        nbtwccs_hide_stat_info_popup();
                        alert('Request error! Try later or another agregator!');
                    }
                });
            }

            return true;
        });


        jQuery('.nbtwccs_flag_input').life('change', function ()
        {
            jQuery(this).next('a.nbtwccs_flag').find('img').attr('src', jQuery(this).val());
        });

        jQuery('.nbtwccs_flag').life('click', function ()
        {
            var input_object = jQuery(this).prev('input[type=hidden]');
            window.send_to_editor = function (html)
            {
                jQuery('#nbtwccs_buffer').html(html);
                var imgurl = jQuery('#nbtwccs_buffer').find('img').eq(0).attr('src');                
                jQuery(input_object).val(imgurl);
                jQuery(input_object).trigger('change');
                tb_remove();
                nbtwccs_insert_html_in_buffer("");
            };
            tb_show('', 'media-upload.php?post_id=0&type=image&TB_iframe=true');

            return false;
        });

        jQuery('.nbtwccs_finance_yahoo').life('click', function () {
            var currency_name = jQuery(this).parent().find('input[name="nbtwccs_name[]"]').val();
            console.log(currency_name);
            var _this = this;
            jQuery(_this).parent().find('input[name="nbtwccs_rate[]"]').val('loading ...');
            var data = {
                action: "nbtwccs_get_rate",
                currency_name: currency_name
            };
            jQuery.post(ajaxurl, data, function (value) {
                jQuery(_this).parent().find('input[name="nbtwccs_rate[]"]').val(value);
            });

            return false;
        });

        /*loader*/
        jQuery(".nbtwccs-admin-preloader").fadeOut("slow");

    });


    function nbtwccs_insert_html_in_buffer(html) {
        jQuery('#nbtwccs_buffer').html(html);
    }
    function nbtwccs_get_html_from_buffer() {
        return jQuery('#nbtwccs_buffer').html();
    }

    function nbtwccs_show_info_popup(text, delay) {
        jQuery(".info_popup").text(text);
        jQuery(".info_popup").fadeTo(400, 0.9);
        window.setTimeout(function () {
            jQuery(".info_popup").fadeOut(400);
        }, delay);
    }

    function nbtwccs_show_stat_info_popup(text) {
        jQuery(".info_popup").text(text);
        jQuery(".info_popup").fadeTo(400, 0.9);
    }


    function nbtwccs_hide_stat_info_popup() {
        window.setTimeout(function () {
            jQuery(".info_popup").fadeOut(400);
        }, 500);
    }

</script>

<?php
function nbtwccs_print_currency($_this, $currency) {
    global $NBTWCCS;
    ?>
    <li>
        <input class="help_tip nbtwccs_is_etalon" data-tip="<?php _e("Set etalon main currency. This should be the currency in which the price of goods exhibited!", 'netbase_solutions') ?>" type="radio" <?php checked(1, $currency['is_etalon']) ?> />
        <input type="hidden" name="nbtwccs_is_etalon[]" value="<?php echo $currency['is_etalon'] ?>" />
        <input type="text" value="<?php echo $currency['name'] ?>" name="nbtwccs_name[]" class="nbtwccs-text nbtwccs-currency" placeholder="<?php _e("Exmpl.: USD,EUR", 'netbase_solutions') ?>" />
        <select class="nbtwccs-drop-down nbtwccs-symbol" name="nbtwccs_symbol[]">
            <?php foreach ($_this->currency_symbols as $symbol) : ?>
                <option value="<?php echo md5($symbol) ?>" <?php selected(md5($currency['symbol']), md5($symbol)) ?>><?php echo $symbol; ?></option>
            <?php endforeach; ?>
        </select>
        <select class="nbtwccs-drop-down nbtwccs-position" name="nbtwccs_position[]">
            <option value="0"><?php _e("Select symbol position", 'netbase_solutions'); ?></option>
            <?php foreach ($_this->currency_positions as $position) : ?>
                <option value="<?php echo $position ?>" <?php selected($currency['position'], $position) ?>><?php echo str_replace('_', ' ', $position); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="nbtwccs_decimals[]" class="nbtwccs-drop-down nbtwccs-decimals">
            <?php
            $nbtwccs_decimals = array(
                -1 => __("Decimals", 'netbase_solutions'),
                0 => 0,
                1 => 1,
                2 => 2,
                3 => 3,
                4 => 4,
                5 => 5,
                6 => 6,
                7 => 7,
                8 => 8
            );
            if (!isset($currency['decimals'])) {
                $currency['decimals'] = 2;
            }
            ?>
            <?php foreach ($nbtwccs_decimals as $v => $n): ?>
                <option <?php if ($currency['decimals'] == $v): ?>selected=""<?php endif; ?> value="<?php echo $v ?>"><?php echo $n ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" style="width: 100px;" value="<?php echo $currency['rate'] ?>" name="nbtwccs_rate[]" class="nbtwccs-text nbtwccs-rate" placeholder="<?php _e("exchange rate", 'netbase_solutions') ?>" />
        <button class="button nbtwccs_finance_yahoo help_tip" data-tip="<?php _e("Press this button if you want get currency rate from the selected aggregator above!", 'netbase_solutions') ?>"><?php _e("finance", 'netbase_solutions'); ?>.<?php echo get_option('nbtwccs_currencies_aggregator', 'yahoo') ?></button>
        <select name="nbtwccs_hide_cents[]" class="nbtwccs-drop-down" <?php if (in_array($currency['name'], $NBTWCCS->no_cents)): ?>disabled=""<?php endif; ?>>
            <?php
            $nbtwccs_hide_cents = array(
                0 => __("Show cents on front", 'netbase_solutions'),
                1 => __("Hide cents on front", 'netbase_solutions')
            );
            if (in_array($currency['name'], $NBTWCCS->no_cents)) {
                $currency['hide_cents'] = 1;
            }
            $hide_cents = 0;
            if (isset($currency['hide_cents'])) {
                $hide_cents = $currency['hide_cents'];
            }
            ?>
            <?php foreach ($nbtwccs_hide_cents as $v => $n): ?>
                <option <?php if ($hide_cents == $v): ?>selected=""<?php endif; ?> value="<?php echo $v ?>"><?php echo $n ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" value="<?php echo $currency['description'] ?>" name="nbtwccs_description[]" style="width: 250px;" class="nbtwccs-text" placeholder="<?php _e("description", 'netbase_solutions') ?>" />
        <?php
        $flag = NBTWCCS_LINK . 'assets/img/no_flag.png';
        if (isset($currency['flag']) AND ! empty($currency['flag'])) {
            $flag = $currency['flag'];

        }
        ?>
        <input type="hidden" value="<?php echo $flag ?>" class="nbtwccs_flag_input" name="nbtwccs_flag[]" />
        <a href="#" class="nbtwccs_flag help_tip" data-tip="<?php _e("Click to select the flag", 'netbase_solutions'); ?>"><img src="<?php echo $flag ?>" style="vertical-align: middle; max-width: 50px;" alt="<?php _e("Flag", 'netbase_solutions'); ?>" /></a>
        &nbsp;<a href="#" class="button nbtwccs_del_currency help_tip" data-tip="<?php _e("remove", 'netbase_solutions'); ?>" style="vertical-align: middle;">X</a>
        &nbsp;<a href="#" class="help_tip" data-tip="<?php _e("drag and drope", 'netbase_solutions'); ?>"><img style="width: 22px; vertical-align: middle;" src="<?php echo NBTWCCS_LINK ?>assets/img/move.png" alt="<?php _e("move", 'netbase_solutions'); ?>" /></a>
    </li>
    <?php
}
