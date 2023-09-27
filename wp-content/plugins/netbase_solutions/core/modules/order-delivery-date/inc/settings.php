<?php
class NBT_Order_Delivery_Date_Settings{
	static $id = 'order-delivery-date';

	protected static $initialized = false;

	public static function initialize() {
		// Do nothing if pluggable functions already initialized.
		if ( self::$initialized ) {
			return;
		}

		// State that initialization completed.
		self::$initialized = true;
	}

    public static function get_settings() {
        global $nbtodd_languages,$nbtodd_date_formats,$nbtodd_days,$nbtodd_calendar_themes;
        $settings = array(
            'enable' => array(
                'name' => __( 'Enable Delivery Date?', 'nbt-solutions' ),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_enable',
                'default' => false,
                'label' => 'Enable'
            ),
            
            'weekday_0' => array(
                'name' => __( 'Delivery Days: Sunday', 'nbt-solutions' ),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_weekday_0',
                'default' => '',   
                'desc_tip' => 'Select weekdays for delivery.'             
            ),
            'weekday_1' => array(
                'name' => __( 'Delivery Days: Monday', 'nbt-solutions' ),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_weekday_1',
                'default' => '',              
                'desc_tip' => 'Select weekdays for delivery.'       
            ),
            'weekday_2' => array(
                'name' => __( 'Delivery Days: Tuesday', 'nbt-solutions' ),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_weekday_2',
                'default' => '',              
                'desc_tip' => 'Select weekdays for delivery.'       
            ),
            'weekday_3' => array(
                'name' => __( 'Delivery Days: Wednesday', 'nbt-solutions' ),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_weekday_3',
                'default' => '',           
                'desc_tip' => 'Select weekdays for delivery.'          
            ),
            'weekday_4' => array(
                'name' => __( 'Delivery Days: Thursday', 'nbt-solutions' ),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_weekday_4',
                'default' => '', 
                'desc_tip' => 'Select weekdays for delivery.'                    
            ),
            'weekday_5' => array(
                'name' => __( 'Delivery Days: Friday', 'nbt-solutions' ),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_weekday_5',
                'default' => '',    
                'desc_tip' => 'Select weekdays for delivery.'                 
            ),
            'weekday_6' => array(
                'name' => __( 'Delivery Days: Saturday', 'nbt-solutions' ),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_weekday_6',
                'default' => '',   
                'desc_tip' => 'Select weekdays for delivery.'                  
            ),

            /*'minimum_order_days' => array(
                'name' => __( 'Minimum Delivery time (in hours):', 'nbt-solutions' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_minimum_time',
                'default' => '',
                'label' => '',
                'desc_tip' => 'Minimum number of hours required to prepare for delivery.'
            ),*/
            'numb_of_dates' => array(
                'name' => __( 'Number of dates to choose:', 'nbt-solutions' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_numb_of_dates',
                'default' => '',
                'label' => '',
                'desc_tip' => 'Number of dates available for delivery.'
            ),

            'field_mandatory' => array(
                'name' => __( 'Mandatory field?', 'nbt-solutions' ),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_field_mandatory',
                'default' => false,
                'label' => '',
                'desc_tip' => 'Selection of delivery date on the checkout page will become mandatory.'
            ),

            'lockout_date_after_orders' => array(
                'name' => __( 'Lockout date after X orders', 'nbt-solutions' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_lockout_date_after_orders',
                'default' => '',
                'label' => '',
                'desc_tip' => 'Maximum deliveries/orders per day.'
            ),
            'sorting_of_column' => array(
                'name' => __( 'Sort on WooCommerce Orders Page?', 'nbt-solutions' ),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_sorting_of_column',
                'default' => false,
                'label' => '',
                'desc_tip' => 'Enable default sorting of orders (in descending order) by Delivery Date on WooCommerce -> Orders page'
            ),
            'auto_first_available_date' => array(
                'name' => __( 'Auto-populate first available Delivery date?', 'nbt-solutions' ),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_auto_first_available_date',
                'default' => false,
                'label' => '',
                'desc_tip' => 'Auto-populate first available Delivery date when the checkout page loads.'
            ),
            'calculate_min_time_disabled_days' => array(
                'name' => __( 'Apply Minimum Delivery Time for non working weekdays?', 'nbt-solutions' ),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_calculate_min_time_disabled_days',
                'default' => false,
                'label' => '',
                'desc_tip' => 'If selected, then the Minimum Delivery Time (in hours) will be applied on the non working weekdays which are unchecked in Delivery Weekdays. If unchecked, then it will not be applied. For example, if Minimum Delivery Time (in hours) is set to 48 hours and Saturday is disabled for delivery. Now if a customer visits the website on Firday, then the first available date will be Monday and not Sunday.'
            ),

            /*'lang_selected' => array(
                'name' => __( 'Calendar Language', 'nbt-solutions' ),
                'type' => 'select',
                'id'   => 'nbt_'.self::$id.'_lang_selected',
                'default' => '',
                'label' => '',
                'options' => $nbtodd_languages,
            ),*/

            'date_format' => array(
                'name' => __( 'Date Format', 'nbt-solutions' ),
                'type' => 'select',
                'id'   => 'nbt_'.self::$id.'_date_format',
                'default' => 'd M, y',
                'label' => '',
                'options' => $nbtodd_date_formats,
            ),
            'start_of_week' => array(
                'name' => __( 'First Day of Week', 'nbt-solutions' ),
                'type' => 'select',
                'id'   => 'nbt_'.self::$id.'_start_of_week',
                'default' => '',
                'label' => '',
                'options' => $nbtodd_days,
            ),

            'field_label' => array(
                'name' => __( 'Field Label', 'nbt-solutions' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_field_label',
                'default' => 'Delivery Date',
                'label' => '',
                'desc_tip' => 'Choose the label that is to be displayed for the field on checkout page.'
            ),
            'field_placeholder' => array(
                'name' => __( 'Field Placeholder Text', 'nbt-solutions' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_field_placeholder',
                'default' => 'Choose a Date',
                'label' => '',
                'desc_tip' => 'Choose the placeholder text that is to be displayed for the field on checkout page.'
            ),

            'field_note' => array(
                'name' => __( 'Field Note Text', 'nbt-solutions' ),
                'type' => 'textarea',
                'id'   => 'nbt_'.self::$id.'_field_note',
                'default' => 'We will try our best to deliver your order on the specified date.',                
                
            ),
            'number_of_months' => array(
                'name' => __( 'Number of Month', 'nbt-solutions' ),
                'type' => 'select',
                'id'   => 'nbt_'.self::$id.'_number_of_months',
                'default' => '1',
                'label' => '',
                'options' => array(
                    '1' => __('1', 'nbt-solutions'),
                    '2' => __('2', 'nbt-solutions'),
                    
                ),
            ),
            'fields_on_checkout_page' => array(
                'name' => __( 'Field placement on the Checkout page', 'nbt-solutions' ),
                'type' => 'select',
                'id'   => 'nbt_'.self::$id.'_fields_on_checkout_page',
                'default' => 'billing_section',
                'label' => '',
                'options' => array(
                    'billing_section' => __('In Billing Section', 'nbt-solutions'),
                    'shipping_section' => __('In Shipping Section', 'nbt-solutions'),
                    'before_order_notes' => __('Before Order Notes', 'nbt-solutions'),
                    'after_order_notes' => __('After Order Notes ', 'nbt-solutions'),
                    
                ),
            ),
            'cart_page' => array(
                'name' => __( 'Delivery Date field on Cart page', 'nbt-solutions' ),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_cart_page',
                'default' => true,
                'label' => '',
                'desc_tip' => 'Add the Delivery Date field on the cart page along with the Checkout page.'
            ),
            'calendar_theme' => array(
                'name' => __( 'Calendar theme', 'nbt-solutions' ),
                'type' => 'select',
                'id'   => 'nbt_'.self::$id.'_calendar_theme',
                'default' => 'smoothness',
                'label' => '',
                'options' => $nbtodd_calendar_themes,
            ),
            'holiday' => array(
                'name' => __( 'Holiday', 'nbt-solutions' ),
                'type' => 'repeater',
                'id'   => 'nbt_'.self::$id.'_holiday',
                'temp' => NBTODD_PATH . 'tpl/admin/repeater-settings.php',
                'fields' => array(
                    'holiday_date' => array(
                        'name' => __( 'Date', 'nbt-solutions' ),
                        'type' => 'text',
                        'desc_tip'=> 'fomat date: m-d-yyyy',
                        'id'   => 'nbt_'.self::$id.'_holiday_date',
                        'default' => '',
                        
                    ), 
                    'holiday_name' => array(
                        'name' => __( 'Name', 'nbt-solutions' ),
                        'type' => 'text',
                        'id'   => 'nbt_'.self::$id.'_holiday_name',
                        'default' => '',
                        
                    ), 
                    
                )
            ),
            
            
            
        );
        return apply_filters( 'nbt_'.self::$id.'_settings', $settings );
    }
}