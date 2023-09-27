<?php
class NBT_Solutions_Vendor_Profile_Store {
	function __construct() {
		add_filter('woopanel_options', array($this, 'get_settings'), 99, 1);
		add_action( 'woopanel_init', array($this, 'save_settings') );
		add_filter( 'woopanel_form_field_show_more', array($this, 'field_show_more'), 99, 4);
		add_filter( 'woopanel_form_field_address', array($this, 'field_address'), 99, 4);
	}
	
	function get_settings( $fields = array() ) {
		global $current_user;
		
		$wpl_profile_settings = get_user_meta($current_user->ID, 'woopanel_profile_settings', true);


		$fields['profile_store'] = [
			'menu_title' => esc_html__( 'Profile Store', 'woopanel' ),
			'title'      => esc_html__( 'Profile Store Settings', 'woopanel' ),
			'desc'       => '',
			'parent'     => '',
			'icon'       => '',
			'type'       => 'user_meta',
			'fields'     => array(
				array(
					'id'       => 'profile_store[banner]',
					'type'     => 'image',
					'title'    => esc_html__( 'Shop Banner', 'woopanel'  ),
					'size'	   => 'full',
					'dimensions' => array(
						'width' => 625,
						'height' => 300
					),
					'value' => isset($wpl_profile_settings['banner']) ? $wpl_profile_settings['banner'] : '-1'
				),
				array(
					'id'       => 'profile_store[gravatar]',
					'type'     => 'image',
					'title'    => esc_html__( 'Profile Picture', 'woopanel'  ),
					'size'	   => 'full',
					'dimensions' => array(
						'width' => 150,
						'height' => 150
					),
					'value' => isset($wpl_profile_settings['gravatar']) ? $wpl_profile_settings['gravatar'] : '-1'
				),
				array(
					'id'       => 'profile_store[store_name]',
					'type'     => 'text',
					'title'    => esc_html__( 'Store Name', 'woopanel'  ),
					'value' => isset($wpl_profile_settings['store_name']) ? $wpl_profile_settings['store_name'] : 0
				),
				array(
					'id'       => 'profile_store[store_ppp]',
					'type'     => 'number',
					'title'    => esc_html__( 'Store Product Per Page', 'woopanel'  ),
					'value' => isset($wpl_profile_settings['store_ppp']) ? $wpl_profile_settings['store_ppp'] : 0
				),
				array(
					'id'       => 'profile_store[address]',
					'type'     => 'address',
					'title'    => esc_html__( 'Address', 'woopanel'  ),
					'fields'   => array(
						'street_1' => array(
							'type' => 'text',
							'label' => esc_html__('Street', 'woopanel' ),
							'placeholder' => esc_html__('Street address', 'woopanel' ),
							'value' => isset($wpl_profile_settings['address']['street_1']) ? $wpl_profile_settings['address']['street_1'] : ''
						),
						'street_2' => array(
							'type' => 'text',
							'label' => esc_html__('Street 2', 'woopanel' ),
							'placeholder' => esc_html__('Apartment, suite, unit etc. (optional)', 'woopanel' ),
							'value' => isset($wpl_profile_settings['address']['street_2']) ? $wpl_profile_settings['address']['street_2'] : ''
						),
						'city' => array(
							'type' => 'text',
							'label' => esc_html__('City', 'woopanel' ),
							'placeholder' => esc_html__('Town / City', 'woopanel' ),
							'value' => isset($wpl_profile_settings['address']['city']) ? $wpl_profile_settings['address']['city'] : ''
						),
						'zip' => array(
							'type' => 'text',
							'label' => esc_html__('Post/ZIP Code', 'woopanel' ),
							'placeholder' => esc_html__('Postcode / Zip', 'woopanel' ),
							'value' => isset($wpl_profile_settings['address']['zip']) ? $wpl_profile_settings['address']['zip'] : ''
						),
						'country' => array(
							'type' => 'select',
							'label' => esc_html__('Country', 'woopanel' ),
							'value' => isset($wpl_profile_settings['address']['country']) ? $wpl_profile_settings['address']['country'] : ''
						),
						'state' => array(
							'type' => 'text',
							'label' => esc_html__('State', 'woopanel' ),
							'value' => isset($wpl_profile_settings['address']['state']) ? $wpl_profile_settings['address']['state'] : ''
						),
					)
				),
				array(
					'id'       => 'profile_store[phone]',
					'type'     => 'text',
					'title'    => esc_html__( 'Phone No', 'woopanel' ),
					'value' => isset($wpl_profile_settings['phone']) ? $wpl_profile_settings['phone'] : 0
				),
				array(
					'id'       => 'profile_store[show_email]',
					'type'     => 'checkbox',
					'title'    => esc_html__( 'Email', 'woopanel' ),
					'default'	=> 'yes',
					'description'	=> esc_html__( 'Show email address in store', 'woopanel' ),
					'value' => isset($wpl_profile_settings['show_email']) ? $wpl_profile_settings['show_email'] : 'no'
				),
				array(
					'id'       => 'profile_store[show_more_ptab]',
					'type'     => 'checkbox',
					'title'    => esc_html__( 'More products', 'woopanel'  ),
					'default'	=> 'yes',
					'description'	=> esc_html__( 'Enable tab on product single page view', 'woopanel' ),
					'value' => isset($wpl_profile_settings['show_more_ptab']) ? $wpl_profile_settings['show_more_ptab'] : 'no'
				),
				array(
					'id'       => 'profile_store[find_address]',
					'type'     => 'text',
					'title'    => esc_html__( 'Map', 'woopanel'  ),
					'value' => isset($wpl_profile_settings['find_address']) ? $wpl_profile_settings['find_address'] : ''
				),
				array(
					'id'       => 'profile_store[store_open_notice]',
					'type'     => 'text',
					'title'    => esc_html__( 'Store Open Notice', 'woopanel'  ),
					'placeholder' => esc_html__('Store is open', 'woopanel' ),
					'value' => isset($wpl_profile_settings['store_open_notice']) ? $wpl_profile_settings['store_open_notice'] : ''
				),
				array(
					'id'       => 'profile_store[store_close_notice]',
					'type'     => 'text',
					'title'    => esc_html__( 'Store Close Notice', 'woopanel'  ),
					'placeholder' => esc_html__('Store is closed', 'woopanel' ),
					'value' => isset($wpl_profile_settings['store_close_notice']) ? $wpl_profile_settings['store_close_notice'] : ''
				),
				array(
					'id'       => 'profile_store[store_intro]',
					'type'     => 'textarea',
					'title'    => esc_html__( 'Store Introduction', 'woopanel'  ),
					'placeholder' => esc_html__('Please enter store introduction...', 'woopanel' ),
					'value' => isset($wpl_profile_settings['store_intro']) ? $wpl_profile_settings['store_intro'] : ''
				),
				array(
					'id'       => 'profile_store[store_tos]',
					'type'     => 'textarea',
					'title'    => esc_html__( 'Store TOS', 'woopanel'  ),
					'placeholder' => esc_html__('Please enter store TOS...', 'woopanel' ),
					'value' => isset($wpl_profile_settings['store_tos']) ? $wpl_profile_settings['store_tos'] : ''
				),
			)
		];

		return $fields;
	}
	

	function save_settings() {
		global $current_user;
		
		$wpl_profile_settings = get_user_meta($current_user->ID, 'woopanel_profile_settings', true);
		if( empty($wpl_profile_settings) ) {
			$wpl_profile_settings = array();
		}

		if ( is_woopanel_endpoint_url('settings') && ( isset($_POST['save']) || isset($_POST['save1']) ) ) {
				$fields = $this->get_settings();

				foreach( $fields['profile_store']['fields'] as $f) {
					$name = str_replace( array('profile_store[', ']'), '', $f['id']);
					if( isset($f['fields']) && ! empty($f['fields']) ) {
						if( isset($_POST['profile_store'][$name]) ) {
							$wpl_profile_settings[$name] = $_POST['profile_store'][$name];
						}else {
							$wpl_profile_settings[$name] = '';
						}
					}else {
						if( isset($_POST['profile_store'][$name]) ) {
							$wpl_profile_settings[$name] = $_POST['profile_store'][$name];
						}
					}
				}
				
				update_user_meta($current_user->ID, 'woopanel_profile_settings', $wpl_profile_settings );
		}
	}
	

	function field_show_more($field = false, $key, $args, $value ) {
		global $current_user;
		$wpl_profile_settings = get_user_meta($current_user->ID, 'wpl_profile_settings', true);
		if( empty($wpl_profile_settings) ) {
			$wpl_profile_settings = array();
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
				if( isset($wpl_profile_settings['store_time'][$day]) ) {
					$value_day = $wpl_profile_settings['store_time'][$day];
				}?>
				<div class="show-group-row">
					<div class="shop-group-col label"><label><?php echo esc_html( $label ); ?></label></div>
					<div class="shop-group-col open-close">
						<select name="profile_store[store_time][<?php echo esc_attr( $day ) ?>][status]" class="form-control m-input">
							<option value="close" <?php ! empty( $value_day['status'] ) ? selected( $value_day['status'], 'close' ) : '' ?> >
								<?php esc_html_e( 'Close', 'woopanel' ); ?>
							</option>
							<option value="open" <?php ! empty( $value_day['status'] ) ? selected( $value_day['status'], 'open' ) : '' ?> >
								<?php esc_html_e( 'Open', 'woopanel' ); ?>
							</option>
						</select>
					</div>
					
					<div for="opening-time" class="shop-group-col time" style="visibility: <?php echo isset( $value_day['status'] ) && $value_day['status'] == 'open' ? 'visible' : 'hidden' ?>" >
						<input type="text" class="form-control m-input timepicker" name="profile_store[store_time][<?php echo esc_attr( $day ) ?>][opening_time]" id="<?php echo esc_attr( $day ) ?>-opening-time" placeholder="<?php echo date_i18n( get_option( 'time_format', 'g:i a' ), current_time( 'timestamp' ) ); ?>" value="<?php echo isset( $value_day['opening_time'] ) ? esc_attr( $value_day['opening_time'] ) : '' ?>" >
					</div>
					<div for="closing-time" class="shop-group-col time" style="visibility: <?php echo isset( $value_day['status'] ) && $value_day['status'] == 'open' ? 'visible' : 'hidden' ?>" >
						<input type="text" class="form-control m-input timepicker" name="profile_store[store_time][<?php echo esc_attr( $day ) ?>][closing_time]" id="<?php echo esc_attr( $day ) ?>-closing-time" placeholder="<?php echo date_i18n( get_option( 'time_format', 'g:i a' ), current_time( 'timestamp' ) ); ?>" value="<?php echo isset( $value_day['closing_time'] ) ? esc_attr( $value_day['closing_time'] ) : '' ?>">
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
								<input type="text" class="form-control m-input" name="profile_store[address][<?php echo esc_attr( $name ) ?>]" value="<?php echo isset($field['value']) ? $field['value'] : '';?>" placeholder="<?php echo isset($field['placeholder']) ? $field['placeholder'] : '';?>" />
								<?php
							}else {
								$country_obj   = new WC_Countries();
								$countries     = $country_obj->countries;
								$states        = $country_obj->states;
								
								$value = empty($field['value']) ? false : $field['value'];
								?>
								<select name="profile_store[address][<?php echo esc_attr( $name ) ?>]" class="form-control m-input">
									<?php woopanel_country_dropdown( $countries, $value, false ); ?>
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

new NBT_Solutions_Vendor_Profile_Store();
