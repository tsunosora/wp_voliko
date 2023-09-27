<?php if (!defined('ABSPATH')) die('No direct access allowed'); 
global $NBTWCCS;

if (!class_exists('WooCommerce')) {
    echo "<div class='notice'>" . __('Warning: Woocommerce is not activated', 'netbase_solutions') . "</div>";
    return;
}

$currencies = $NBTWCCS->get_currencies();
if (isset($exclude)) {
    $exclude = explode(',', $exclude);
} else {
    $exclude = array();
}

if (!isset($precision)) {
    $precision = 2;
}

$current_currency = $NBTWCCS->current_currency;
?>

<div class="nbtwccs_converter_shortcode">
    <input type="text"  placeholder="<?php _e('enter amount', 'netbase_solutions') ?>" class="nbtwccs_converter_shortcode_amount" value="1" /><br />
    <input type="hidden" value="<?php echo $precision ?>" class="nbtwccs_converter_shortcode_precision" />
    <select class="nbtwccs_converter_shortcode_from">
<?php
if (!empty($currencies)) {
    foreach ($currencies as $key => $c) {
        if (in_array($key, $exclude)) {
            continue;
        }
        ?>
                <option <?php selected($current_currency, $key) ?> value="<?php echo $key ?>"><?php echo $c['name'] ?></option>
                <?php
            }
        }
        ?>
    </select>&nbsp;<?php _e('to', 'netbase_solutions') ?>&nbsp;<select class="nbtwccs_converter_shortcode_to">
        <?php
        if (!empty($currencies)) {
            foreach ($currencies as $key => $c) {
                if (in_array($key, $exclude)) {
                    continue;
                }
                ?>
                <option value="<?php echo $key ?>"><?php echo $c['name'] ?></option>
                <?php
            }
        }
        ?>
    </select><br />
    <input type="text" readonly="" placeholder="<?php _e('results', 'netbase_solutions') ?>" class="nbtwccs_converter_shortcode_results" value="" /><br />
    <button class="button nbtwccs_converter_shortcode_button" type="button"><?php _e('Convert', 'netbase_solutions') ?></button>
</div>


