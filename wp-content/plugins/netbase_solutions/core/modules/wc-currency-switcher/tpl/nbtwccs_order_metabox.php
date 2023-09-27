<?php if (!defined('ABSPATH')) die('No direct access allowed'); 
$currencies = $this->get_currencies();
$rate = get_post_meta($post->ID, '_nbtwccs_order_rate', TRUE);
$currency = get_post_meta($post->ID, '_order_currency', TRUE);
$base_currency = get_post_meta($post->ID, '_nbtwccs_order_base_currency', TRUE);
$changed_mannualy = get_post_meta($post->ID, '_nbtwccs_order_currency_changed_mannualy', TRUE);
if (empty($base_currency))
{
    $base_currency = $this->default_currency;
}
?>
<div id="nbtwccs_order_metabox">
    <strong><?php _e('Order currency', 'netbase_solutions') ?></strong>: 
    <span class="nbtwccs_order_currency">
        <i><?php echo $currency ?></i>
        <select name="nbtwccs_order_currency2" style="width: 80%; display: none;">
            <?php foreach ($currencies as $key => $curr) : ?>
                <option value="<?php echo $key ?>"><?php echo $curr['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </span>&nbsp;<span class="tips" data-tip="<?php _e('Currency in which the customer paid.', 'netbase_solutions') ?><?php if ($changed_mannualy > 0): ?> <?php printf(__('THIS order currency is changed manually %s!', 'netbase_solutions'), date('d-m-Y', $changed_mannualy)) ?><?php endif; ?>">[?]</span><br />
    <strong><?php _e('Base currency', 'netbase_solutions') ?></strong>: <?php echo $base_currency ?><br />
    <strong><?php _e('Order currency rate', 'netbase_solutions') ?></strong>: <?php echo $rate ?>&nbsp;<span class="tips" data-tip="<?php _e('Currency rate when the customer paid ', 'netbase_solutions') ?>">[?]</span><br />
    <strong><?php _e('Total amount', 'netbase_solutions') ?></strong>: 
    <?php
    $_REQUEST['no_nbtwccs_order_amount_total'] = 1;
    echo trim(number_format($order->get_total(), $this->price_num_decimals) . ' ' . $currency);
    ?><br />
    <hr />
    <a href="javascript:nbtwccs_change_order_data();void(0);" class="button nbtwccs_change_order_curr_button"><?php _e('Change order currency', 'netbase_solutions') ?>&nbsp;<img class="help_tip" data-tip="<?php _e('For new manual order ONLY!!', 'netbase_solutions') ?>" src="<?php echo NBTWCCS_LINK ?>/assets/img/help.png" height="16" width="16" /></a>
    <a href="javascript:nbtwccs_cancel_order_data();void(0);" style="display: none;" class="button nbtwccs_cancel_order_curr_button"><?php _e('cancel', 'netbase_solutions') ?></a><br />


    <?php if ($currency !== $this->default_currency): ?>
        <hr />
        <a href="javascript:nbtwccs_recalculate_order_data();void(0);" class="button nbtwccs_recalculate_order_curr_button"><?php _e("Recalculate order", 'netbase_solutions') ?>&nbsp;<img class="help_tip" data-tip="<?php _e('Recalculate current order with basic currency. Recommended test this option on the clone of your site! Read the documentation of the plugin about it!', 'netbase_solutions') ?>" src="<?php echo NBTWCCS_LINK ?>/assets/img/help.png" height="16" width="16" /></a><br />
        <?php endif; ?>

</div>

<script type="text/javascript">
    var nbtwccs_old_currency = null;
    function nbtwccs_change_order_data() {
        nbtwccs_old_currency = jQuery('#nbtwccs_order_metabox .nbtwccs_order_currency i').html();
        jQuery('#nbtwccs_order_metabox .nbtwccs_order_currency select').show();
        jQuery('#nbtwccs_order_metabox .nbtwccs_order_currency select').attr('name', 'nbtwccs_order_currency');
        jQuery('#nbtwccs_order_metabox .nbtwccs_order_currency select').val(nbtwccs_old_currency);
        jQuery('.nbtwccs_change_order_curr_button').hide();
        jQuery('.nbtwccs_cancel_order_curr_button').show();
    }

    function nbtwccs_cancel_order_data() {
        jQuery('#nbtwccs_order_metabox .nbtwccs_order_currency select').hide();
        jQuery('#nbtwccs_order_metabox .nbtwccs_order_currency select').attr('name', 'nbtwccs_order_currency2');
        jQuery('.nbtwccs_change_order_curr_button').show();
        jQuery('.nbtwccs_cancel_order_curr_button').hide();
    }

    function nbtwccs_recalculate_order_data() {
        if (confirm('Sure? This operation could not be rollback!!')) {
            jQuery('.nbtwccs_recalculate_order_curr_button').prop('href', 'javascript:void(0);');
            var data = {
                action: "nbtwccs_recalculate_order_data",
                order_id: <?php echo $post->ID ?>
            };
            jQuery.post(ajaxurl, data, function (data) {
                window.location.reload();
            });
        }
    }
</script>

