<?php
class NBT_Solutions_Dokan_Setting_Store {
	function __construct() {
		add_filter('woopanel_options', array($this, 'get_settings'), 99, 1);
		add_action( 'woopanel_init', array($this, 'save_settings') );
		add_filter( 'woopanel_form_field_show_more', array($this, 'field_show_more'), 99, 4);
		add_filter( 'woopanel_form_field_address', array($this, 'field_address'), 99, 4);
	}
	
	function get_settings( $fields = array() ) {
		global $current_user;
		
		$dokan_settings = get_user_meta($current_user->ID, 'dokan_profile_settings', true);

		$fields['dokan_store'] = [
			'menu_title' => esc_html__( 'Dokan Store', 'woopanel' ),
			'title'      => esc_html__( 'Dokan Store Settings', 'woopanel' ),
			'desc'       => '',
			'parent'     => '',
			'icon'       => '',
			'type'       => 'user_meta',
			'fields'     => array(
				array(
					'id'       => 'dokan_store[banner]',
					'type'     => 'image',
					'title'    => esc_html__( 'Shop Banner', 'woopanel'  ),
					'size'	   => 'full',
					'dimensions' => array(
						'width' => 625,
						'height' => 300
					),
					'value' => isset($dokan_settings['banner']) ? $dokan_settings['banner'] : '-1'
				),
				array(
					'id'       => 'dokan_store[gravatar]',
					'type'     => 'image',
					'title'    => esc_html__( 'Profile Picture', 'woopanel'  ),
					'size'	   => 'full',
					'dimensions' => array(
						'width' => 150,
						'height' => 150
					),
					'value' => isset($dokan_settings['gravatar']) ? $dokan_settings['gravatar'] : '-1'
				),
				array(
					'id'       => 'dokan_store[store_name]',
					'type'     => 'text',
					'title'    => esc_html__( 'Store Name', 'woopanel'  ),
					'value' => isset($dokan_settings['store_name']) ? $dokan_settings['store_name'] : 0
				),
				array(
					'id'       => 'dokan[store_ppp]',
					'type'     => 'number',
					'title'    => esc_html__( 'Store Product Per Page', 'woopanel'  ),
					'value' => isset($dokan_settings['store_ppp']) ? $dokan_settings['store_ppp'] : 0
				),
				array(
					'id'       => 'dokan_store[address]',
					'type'     => 'address',
					'title'    => esc_html__( 'Address', 'woopanel'  ),
					'fields'   => array(
						'street_1' => array(
							'type' => 'text',
							'label' => esc_html__('Street', 'woopanel' ),
							'placeholder' => esc_html__('Street address', 'woopanel' ),
							'value' => isset($dokan_settings['address']['street_1']) ? $dokan_settings['address']['street_1'] : ''
						),
						'street_2' => array(
							'type' => 'text',
							'label' => esc_html__('Street 2', 'woopanel' ),
							'placeholder' => esc_html__('Apartment, suite, unit etc. (optional)', 'woopanel' ),
							'value' => isset($dokan_settings['address']['street_2']) ? $dokan_settings['address']['street_2'] : ''
						),
						'city' => array(
							'type' => 'text',
							'label' => esc_html__('City', 'woopanel' ),
							'placeholder' => esc_html__('Town / City', 'woopanel' ),
							'value' => isset($dokan_settings['address']['city']) ? $dokan_settings['address']['city'] : ''
						),
						'zip' => array(
							'type' => 'text',
							'label' => esc_html__('Post/ZIP Code', 'woopanel' ),
							'placeholder' => esc_html__('Postcode / Zip', 'woopanel' ),
							'value' => isset($dokan_settings['address']['zip']) ? $dokan_settings['address']['zip'] : ''
						),
						'country' => array(
							'type' => 'select',
							'label' => esc_html__('Country', 'woopanel' ),
							'value' => isset($dokan_settings['address']['country']) ? $dokan_settings['address']['country'] : ''
						),
						'state' => array(
							'type' => 'text',
							'label' => esc_html__('State', 'woopanel' ),
							'value' => isset($dokan_settings['address']['state']) ? $dokan_settings['address']['state'] : ''
						),
					)
				),
				array(
					'id'       => 'dokan_store[phone]',
					'type'     => 'text',
					'title'    => esc_html__( 'Phone No', 'woopanel' ),
					'value' => isset($dokan_settings['phone']) ? $dokan_settings['phone'] : 0
				),
				array(
					'id'       => 'dokan_store[show_email]',
					'type'     => 'checkbox',
					'title'    => esc_html__( 'Email', 'woopanel' ),
					'default'	=> 'yes',
					'description'	=> esc_html__( 'Show email address in store', 'woopanel' ),
					'value' => isset($dokan_settings['show_email']) ? $dokan_settings['show_email'] : 'no'
				),
				array(
					'id'       => 'dokan_store[show_more_ptab]',
					'type'     => 'checkbox',
					'title'    => esc_html__( 'More products', 'woopanel'  ),
					'default'	=> 'yes',
					'description'	=> esc_html__( 'Enable tab on product single page view', 'woopanel' ),
					'value' => isset($dokan_settings['show_more_ptab']) ? $dokan_settings['show_more_ptab'] : 'no'
				),
				array(
					'id'       => 'dokan_store[find_address]',
					'type'     => 'text',
					'title'    => esc_html__( 'Map', 'woopanel'  ),
					'value' => isset($dokan_settings['find_address']) ? $dokan_settings['find_address'] : ''
				),
				array(
					'id'       => 'dokan_store[dokan_store_time]',
					'type'     => 'show_more',
					'title'    => esc_html__( 'Store Opening Closing Time', 'woopanel'  ),
					'description'	=> esc_html__( 'Show store opening closing time widget in store page', 'woopanel' ),
					'fields'   =>  array(
						'sunday' => dokan_get_translated_days('sunday'),
						'monday' => dokan_get_translated_days('monday'),
						'tuesday' => dokan_get_translated_days('tuesday'),
						'wednesday' => dokan_get_translated_days('wednesday'),
						'thursday' => dokan_get_translated_days('thursday'),
						'friday' => dokan_get_translated_days('friday'),
						'saturday' => dokan_get_translated_days('saturday')
					)
				),
				array(
					'id'       => 'dokan_store[dokan_store_open_notice]',
					'type'     => 'text',
					'title'    => esc_html__( 'Store Open Notice', 'woopanel'  ),
					'placeholder' => esc_html__('Store is open', 'woopanel' ),
					'value' => isset($dokan_settings['dokan_store_open_notice']) ? $dokan_settings['dokan_store_open_notice'] : ''
				),
				array(
					'id'       => 'dokan_store[dokan_store_close_notice]',
					'type'     => 'text',
					'title'    => esc_html__( 'Store Close Notice', 'woopanel'  ),
					'placeholder' => esc_html__('Store is closed', 'woopanel' ),
					'value' => isset($dokan_settings['dokan_store_close_notice']) ? $dokan_settings['dokan_store_close_notice'] : ''
				),
			)
		];

		return $fields;
	}
	

	function save_settings() {
		global $current_user;
		
		$dokan_profile_settings = get_user_meta($current_user->ID, 'dokan_profile_settings', true);
		if( empty($dokan_profile_settings) ) {
			$dokan_profile_settings = array();
		}

		if ( is_woopanel_endpoint_url('settings') && ( isset($_POST['save']) || isset($_POST['save1']) ) ) {
				$fields = $this->get_settings();

				foreach( $fields['dokan_store']['fields'] as $f) {
					$name = str_replace( array('dokan_store[', ']'), '', $f['id']);
					if( isset($f['fields']) && ! empty($f['fields']) ) {
						if( isset($_POST['dokan_store'][$name]) ) {
							$dokan_profile_settings[$name] = $_POST['dokan_store'][$name];
						}else {
							$dokan_profile_settings[$name] = '';
						}
					}else {
						if( isset($_POST['dokan_store'][$name]) ) {
							$dokan_profile_settings[$name] = $_POST['dokan_store'][$name];
						}
					}
				}
				
				update_user_meta($current_user->ID, 'dokan_profile_settings', $dokan_profile_settings );
		}
	}
	

	function field_show_more($field = false, $key, $args, $value ) {
		global $current_user;
		$dokan_profile_settings = get_user_meta($current_user->ID, 'dokan_profile_settings', true);
		if( empty($dokan_profile_settings) ) {
			$dokan_profile_settings = array();
		}
		?>
		<div class="form-group m-form__group type-checkbox row" id="setting_show_more_ptab_field" data-priority="">
			<label for="setting_show_more_ptab" class="col-3 col-form-label"><?php echo esc_attr($args['label']);?></label>
			
			<div class="col-9">
				<label class="m-checkbox"><input type="checkbox" class="input-checkbox " value="" name="setting_show_more_ptab" aria-describedby="setting_show_more_ptab-description" id="setting_show_more_ptab" checked="checked"> <?php echo esc_attr($args['description']);?><span></span></label>
				<?php foreach ( $args['fields'] as $day => $label ) : 
				$value_day = array(
					'status' => 'close',
					'opening_time' => '',
					'closing_time' => ''
				);
				if( isset($dokan_profile_settings['dokan_store_time'][$day]) ) {
					$value_day = $dokan_profile_settings['dokan_store_time'][$day];
				}?>
				<div class="show-group-row">
					<div class="shop-group-col label"><label><?php echo esc_html( $label ); ?></label></div>
					<div class="shop-group-col open-close">
						<select name="dokan_store[dokan_store_time][<?php echo esc_attr( $day ) ?>][status]" class="form-control m-input">
							<option value="close" <?php ! empty( $value_day['status'] ) ? selected( $value_day['status'], 'close' ) : '' ?> >
								<?php esc_html_e( 'Close', 'woopanel' ); ?>
							</option>
							<option value="open" <?php ! empty( $value_day['status'] ) ? selected( $value_day['status'], 'open' ) : '' ?> >
								<?php esc_html_e( 'Open', 'woopanel' ); ?>
							</option>
						</select>
					</div>
					
					<div for="opening-time" class="shop-group-col time" style="visibility: <?php echo isset( $value_day['status'] ) && $value_day['status'] == 'open' ? 'visible' : 'hidden' ?>" >
						<input type="text" class="form-control m-input timepicker" name="dokan_store[dokan_store_time][<?php echo esc_attr( $day ) ?>][opening_time]" id="<?php echo esc_attr( $day ) ?>-opening-time" placeholder="<?php echo date_i18n( get_option( 'time_format', 'g:i a' ), current_time( 'timestamp' ) ); ?>" value="<?php echo isset( $value_day['opening_time'] ) ? esc_attr( $value_day['opening_time'] ) : '' ?>" >
					</div>
					<div for="closing-time" class="shop-group-col time" style="visibility: <?php echo isset( $value_day['status'] ) && $value_day['status'] == 'open' ? 'visible' : 'hidden' ?>" >
						<input type="text" class="form-control m-input timepicker" name="dokan_store[dokan_store_time][<?php echo esc_attr( $day ) ?>][closing_time]" id="<?php echo esc_attr( $day ) ?>-closing-time" placeholder="<?php echo date_i18n( get_option( 'time_format', 'g:i a' ), current_time( 'timestamp' ) ); ?>" value="<?php echo isset( $value_day['closing_time'] ) ? esc_attr( $value_day['closing_time'] ) : '' ?>">
					</div>
				</div>
				<?php endforeach;?>
			</div>
		</div>
		<?php
	}

	function field_address($field = false, $key, $args, $value ) {
		?>
		<div class="form-group m-form__group type-checkbox row" id="setting_show_more_ptab_field" data-priority="">
			<label for="setting_show_more_ptab" class="col-3 col-form-label"><?php echo esc_attr($args['label']);?></label>
			
			<div class="col-9">
				<?php
				if( ! empty($args['fields']) ) {
					foreach( $args['fields'] as $name => $field) {
						?>
						<div class="form-field-address">
							<label><?php echo esc_attr($field['label']);?></label>
							<?php
							if( $field['type'] == 'text' ) {
								?>
								<input type="text" class="form-control m-input" name="dokan_store[address][<?php echo esc_attr( $name ) ?>]" value="<?php echo isset($field['value']) ? $field['value'] : '';?>" placeholder="<?php echo isset($field['placeholder']) ? $field['placeholder'] : '';?>" />
								<?php
							}else {
								$country_obj   = new WC_Countries();
								$countries     = $country_obj->countries;
								$states        = $country_obj->states;
								
								$value = empty($field['value']) ? false : $field['value'];
								?>
								<select name="dokan_store[address][<?php echo esc_attr( $name ) ?>]" class="form-control m-input">
									<?php dokan_country_dropdown( $countries, $value, false ); ?>
								</select>
								<?php
							}?>
						</div>
						<?php
					} 
				}
				?>
			</div>
		</div>
		<?php
	}
}

new NBT_Solutions_Dokan_Setting_Store();
