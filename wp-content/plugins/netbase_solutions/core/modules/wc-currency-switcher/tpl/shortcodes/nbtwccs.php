<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
//*** hide if there is checkout page
global $post;
if (!class_exists('WooCommerce')) {
    echo "<div class='notice'>" . __('Warning: Woocommerce is not activated', 'netbase_solutions') . "</div>";
    return;
}
if (get_option('nbtwccs_restrike_on_checkout_page', 0)) {
    if (is_object($post)) {
        if ($this->get_checkout_page_id() == $post->ID) {
            return "";
        }
    }
}

$all_currencies = apply_filters('nbtwccs_currency_manipulation_before_show', $this->get_currencies());
$drop_down_view = $this->get_drop_down_view();

if ($drop_down_view == 'flags') {
    foreach ($all_currencies as $key => $currency) {
        if (!empty($currency['flag'])) {
            ?>
            <a href="#" class="nbtwccs_flag_view_item <?php if ($this->current_currency == $key): ?>nbtwccs_flag_view_item_current<?php endif; ?>" data-currency="<?php echo $currency['name'] ?>" title="<?php echo $currency['name'] . ', ' . $currency['symbol'] . ' ' . $currency['description'] ?>"><img src="<?php echo $currency['flag'] ?>" alt="<?php echo $currency['name'] . ', ' . $currency['symbol'] ?>" /></a>
            <?php
        }
    }
} else {
    $empty_flag = NBTWCCS_LINK . 'assets/img/no_flag.png';
    $show_money_signs = get_option('nbtwccs_show_money_signs', 1);

    if (!isset($show_flags)) {
        $show_flags = get_option('nbtwccs_show_flags', 1);
    }

    if (!isset($width)) {
        $width = '100%';
    }

    if (!isset($flag_position)) {
        $flag_position = 'right';
    }
    ?>   
    <form method="<?php echo apply_filters('nbtwccs_form_method', 'post') ?>" action="#" class="woocommerce-currency-switcher-form <?php if ($show_flags): ?>nbtwccs_show_flags<?php endif; ?>" data-ver="<?php echo NBTWCCS_VERSION ?>">
        <input type="hidden" name="woocommerce-currency-switcher" value="<?php echo $this->current_currency ?>" />
        <select name="woocommerce-currency-switcher" style="width: <?php echo $width ?>;" data-width="<?php echo $width ?>" data-flag-position="<?php echo $flag_position ?>" class="woocommerce-currency-switcher" onchange="nbtwccs_redirect(this.value);
                    void(0);">
                    <?php foreach ($all_currencies as $key => $currency) : ?>

                <?php
                $option_txt = apply_filters('nbtwccs_currname_in_option', $currency['name']);

                if ($show_money_signs) {
                    if (!empty($option_txt)) {
                        $option_txt .= ', ' . $currency['symbol'];
                    } else {
                        $option_txt = $currency['symbol'];
                    }
                }                
                if (isset($txt_type)) {
                    if ($txt_type == 'desc') {
                        if (!empty($currency['description'])) {
                            $option_txt = $currency['description'];
                        }
                    }
                }
                ?>
                <option <?php if ($show_flags) : ?>style="background-image: url('<?php echo(!empty($currency['flag']) ? $currency['flag'] : $empty_flag); ?>') ; background-size: 30px 20px;"<?php endif; ?> value="<?php echo $key ?>" <?php selected($this->current_currency, $key) ?> data-imagesrc="<?php if ($show_flags) echo(!empty($currency['flag']) ? $currency['flag'] : $empty_flag); ?>" data-icon="<?php if ($show_flags) echo(!empty($currency['flag']) ? $currency['flag'] : $empty_flag); ?>" data-description="<?php echo $currency['description'] ?>"><?php echo $option_txt ?></option>
            <?php endforeach; ?>
        </select>
        <div style="display: none;">NBTWCCS <?php echo NBTWCCS_VERSION ?></div>
    </form>
    <?php
}