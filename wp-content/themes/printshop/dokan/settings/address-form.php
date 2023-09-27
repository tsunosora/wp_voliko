<?php
/**
 * Dokan Settings Address form Template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>

<?php

$address         = isset( $profile_info['address'] ) ? $profile_info['address'] : '';
$address_street1 = isset( $profile_info['address']['street_1'] ) ? $profile_info['address']['street_1'] : '';
$address_street2 = isset( $profile_info['address']['street_2'] ) ? $profile_info['address']['street_2'] : '';
$address_city    = isset( $profile_info['address']['city'] ) ? $profile_info['address']['city'] : '';
$address_zip     = isset( $profile_info['address']['zip'] ) ? $profile_info['address']['zip'] : '';
$address_country = isset( $profile_info['address']['country'] ) ? $profile_info['address']['country'] : '';
$address_state   = isset( $profile_info['address']['state'] ) ? $profile_info['address']['state'] : '';

?>

<input type="hidden" id="dokan_selected_country" value="<?php echo $address_country?>" />
<input type="hidden" id="dokan_selected_state" value="<?php echo $address_state?>" />
<div class="row dokan-address-fieldz form-group">
    <div class="col-md-3">
        <label><?php _e( 'Address', 'printshop' ); ?></label>
    </div>

    <div class="col-md-9">
        <div class="row">
            <div class="col-md-12">
                <?php if ( $seller_address_fields['street_1'] ) { ?>
                <div class="form-group">
                    <label for="pwd"><?php _e( 'Street ', 'printshop' ); ?>
                        <?php
                        $required_attr = '';
                        if ( $seller_address_fields['street_1']['required'] ) {
                            $required_attr = 'required'; ?>
                            <span class="required"> *</span>
                        <?php } ?>
                    </label>
                    <input <?php echo $required_attr; ?> <?php echo $disabled ?> id="dokan_address[street_1]" value="<?php echo esc_attr( $address_street1 ); ?>" name="dokan_address[street_1]" placeholder="<?php _e( 'Street address' , 'printshop' ) ?>" class="dokan-form-control input-md" type="text">
                </div>
                <?php }
                if ( $seller_address_fields['street_2'] ) { ?>
                <div class="form-group">
                    <label><?php _e( 'Street 2', 'printshop' ); ?>
                        <?php
                        $required_attr = '';
                        if ( $seller_address_fields['street_2']['required'] ) {
                            $required_attr = 'required'; ?>
                            <span class="required"> *</span>
                        <?php } ?>
                    </label>
                    <input <?php echo $required_attr; ?> <?php echo $disabled ?> id="dokan_address[street_2]" value="<?php echo esc_attr( $address_street2 ); ?>" name="dokan_address[street_2]" placeholder="<?php _e( 'Apartment, suite, unit etc. (optional)' , 'printshop' ) ?>" class="dokan-form-control input-md" type="text">
                </div>
                <?php }
                if ( $seller_address_fields['city'] || $seller_address_fields['zip'] ) {?>
                    <div class="row">
                    <?php if ( $seller_address_fields['city'] ) { ?>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php _e( 'City', 'printshop' ); ?>
                                <?php
                                $required_attr = '';
                                if ( $seller_address_fields['city']['required'] ) {
                                    $required_attr = 'required'; ?>
                                    <span class="required"> *</span>
                                <?php } ?>
                                </label>
                                <input <?php echo $required_attr; ?> <?php echo $disabled ?> id="dokan_address[city]" value="<?php echo esc_attr( $address_city ); ?>" name="dokan_address[city]" placeholder="<?php _e( 'Town / City' , 'printshop' ) ?>" class="dokan-form-control input-md" type="text">
                            </div>
                        </div>
                    <?php }
                    if ( $seller_address_fields['zip'] ) { ?>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php _e( 'Post/ZIP Code', 'printshop' ); ?>
                                    <?php
                                    $required_attr = '';
                                    if ( $seller_address_fields['zip']['required'] ) {
                                        $required_attr = 'required'; ?>
                                        <span class="required"> *</span>
                                    <?php } ?>
                                </label>
                                <input <?php echo $required_attr; ?> <?php echo $disabled ?> id="dokan_address[zip]" value="<?php echo esc_attr( $address_zip ); ?>" name="dokan_address[zip]" placeholder="<?php _e( 'Postcode / Zip' , 'printshop' ) ?>" class="dokan-form-control input-md" type="text">
                            </div>
                        </div>
                    <?php }?>
                    </div>
                    <?php
                }?>
            </div>
        </div>

        <div class="row">
            <?php

            if ( $seller_address_fields['country'] ) {
                $country_obj   = new WC_Countries();
                $countries     = $country_obj->countries;
                $states        = $country_obj->states;
                ?>
                <div class="col-md-6">
                    <label><?php _e( 'Country ', 'printshop' ); ?>
                        <?php
                        $required_attr = '';
                        if ( $seller_address_fields['country']['required'] ) {
                            $required_attr = 'required'; ?>
                            <span class="required"> *</span>
                        <?php } ?>
                    </label>
                    <select <?php echo $required_attr; ?> <?php echo $disabled ?> name="dokan_address[country]" class="country_to_state dokan-form-control" id="dokan_address_country">
                        <?php dokan_country_dropdown( $countries, $address_country, false ); ?>
                    </select>
                </div>
            <?php }?>
            <div class="col-md-6">
                <?php
                if ( $seller_address_fields['state'] ) {
                    $address_state_class = '';
                    $is_input            = false;
                    $no_states           = false;
                    if ( isset( $states[$address_country] ) ) {
                        if ( empty( $states[$address_country] ) ) {
                            $address_state_class = 'dokan-hide';
                            $no_states           = true;
                        } else {

                        }
                    } else {
                        $is_input = true;
                    }
                    ?>
                    <div id="dokan-states-boxed" class="form-group">
                        <label><?php _e( 'State ', 'printshop' ); ?>
                            <?php
                            $required_attr = '';
                            if ( $seller_address_fields['state']['required'] ) {
                                $required_attr = 'required'; ?>
                                <span class="required"> *</span>
                            <?php } ?>
                        </label>
                        <?php if ( $is_input ) { ?>
                            <input <?php echo $required_attr; ?> <?php echo $disabled ?> name="dokan_address[state]" type="text" class="dokan-form-control <?php echo $address_state_class ?>" id="dokan_address_state" value="<?php echo $address_state ?>"/>
                        <?php } else { ?>
                            <select <?php echo $required_attr; ?> <?php echo $disabled ?> name="dokan_address[state]" class="dokan-form-control" id="dokan_address_state">
                                <?php dokan_state_dropdown( $states[$address_country], $address_state ) ?>
                            </select>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>

        </div>
    </div>


</div>
