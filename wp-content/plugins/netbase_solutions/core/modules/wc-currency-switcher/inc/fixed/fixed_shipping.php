<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

class NBTWCCS_FIXED_SHIPPING extends NBTWCCS_FIXED_AMOUNT {

    protected $key = "";

    public function __construct() {
        $this->key = "_shipping_";
        add_filter('woocommerce_shipping_instance_form_fields_flat_rate', array($this, 'add_fixed_flat_rate'), 9999, 1);
        add_filter('woocommerce_shipping_flat_rate_instance_settings_values', array($this, 'save_fixed_flate_rate'), 9999, 2);
    }

    public function add_fixed_flat_rate($fields) {

        global $NBTWCCS;
        $currencies = $NBTWCCS->get_currencies();
        $default_currency = $NBTWCCS->default_currency;
        $is_fixed_enabled = $NBTWCCS->is_fixed_shipping;

        foreach ($currencies as $code => $data) {
            if ($code == $default_currency) {
                continue;
            }
            $fields['nbtwccs_fixed' . $this->key . $code] = array(
                'title' => sprintf(__('Fixed cost %s', 'netbase_solutions'), $code),
                'type' => 'text',
                'placeholder' => __("auto", 'netbase_solutions'),
                'description' => $code,
                'default' => '',
                'desc_tip' => true
            );
        }
        return $fields;
    }

    public function save_fixed_flate_rate($options, $method) {
        return $options;
    }

    public function get_value($method_id, $code, $type) {

        $method = explode(":", $method_id, 2);
        if (!isset($method[1])) {
            return -1;
        }
        $option_string = 'woocommerce_' . $method[0] . '_' . $method[1] . '_settings';
        $settings = get_option($option_string, null);
        if ($settings == null OR ! is_array($settings)) {
            return -1;
        }
        $array_key = sprintf('nbtwccs_fixed%s%s%s', $type, $this->key, $code);
        if (!isset($settings[$array_key])) {
            return -1;
        }
        return $settings[$array_key];
    }

}
