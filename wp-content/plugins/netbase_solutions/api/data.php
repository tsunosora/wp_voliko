<?php
//TODO Optimize this class?
class NBT_Solutions_Data {

    public static function get_best_seller($period)
    {
        global $wpdb;
        $query = array();

        if($period === 'week') {
            $start = gmdate('Y-m-d', strtotime('last Monday'));
        } elseif($period === 'month') {
            $start = date( 'Y-m-01', current_time( 'timestamp' ) );
        } elseif($period === 'year') {
            $start = date( 'Y-01-01', current_time( 'timestamp' ) );
        }

        $query['fields']  = "SELECT SUM( order_item_meta.meta_value ) as qty, order_item_meta_2.meta_value as product_id, order_items.order_item_name as product_title
			FROM {$wpdb->posts} as posts";
        $query['join']    = "INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_id ";
        $query['join']   .= "INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id ";
        $query['join']   .= "INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta_2 ON order_items.order_item_id = order_item_meta_2.order_item_id ";
        $query['where']   = "WHERE posts.post_type IN ( '" . implode( "','", wc_get_order_types( 'order-count' ) ) . "' ) ";
        $query['where']  .= "AND posts.post_status IN ( 'wc-" . implode( "','wc-", apply_filters( 'woocommerce_reports_order_statuses', array( 'completed', 'processing', 'on-hold' ) ) ) . "' ) ";
        $query['where']  .= "AND order_item_meta.meta_key = '_qty' ";
        $query['where']  .= "AND order_item_meta_2.meta_key = '_product_id' ";
        $query['where']  .= "AND posts.post_date >= '" . $start . "' ";
        $query['where']  .= "AND posts.post_date <= '" . date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) . "' ";
        $query['groupby'] = "GROUP BY product_id";
        $query['orderby'] = "ORDER BY qty DESC";
        $query['limits']  = "LIMIT 8";

        return $wpdb->get_results( implode( ' ', apply_filters( 'woocommerce_dashboard_status_widget_top_seller_query', $query ) ) );
    }

    public static function get_recent_income($period = 'month')
    {
        global $wpdb;
        // $order_totals = array();
        $result = array();

        $query = array();
        

        $begin = '';
        $end = '';
        $time_line = '';

        $new_query = array();
        for($i=1;$i<=12;$i++) {
            if($period === 'week') {
                $week = date('W', strtotime("-$i week"));
                $dto = new DateTime();
                $dto->setISODate(date('Y'), $week);
                $begin = $dto->format('Y-m-d');
                $dto->modify('+6 days');
                $end = $dto->format('Y-m-d');                
                $time_line = $week;                
            } elseif($period === 'month') {
                $begin = date("Y-m-01", strtotime( date( 'Y-m' )." -$i months"));
                $end = date("Y-m-t", strtotime( date( 'Y-m' )." -$i months"));
                $time_line = date("Y-m", strtotime( date( 'Y-m' )." -$i months"));
            }
            
            $query['fields'] = "SELECT SUM(meta.meta_value) AS total_sales, COUNT(posts.post_status) as order_count FROM {$wpdb->posts} as posts";
            $query['join']   = "LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id";            
            $query['where']  = "WHERE meta.meta_key = '_order_total'";
            $query['where'] .= " AND posts.post_type = 'shop_order'";
            $query['where'] .= " AND posts.post_status = 'wc-completed'";
            $new_query = " AND posts.post_date >= '" . $begin . "' AND posts.post_date <= '" . $end . "' ";
            $query['where'] .= $new_query;

            // $result[] = $i;

            $order_totals = $wpdb->get_row(implode(' ', $query));

            if($period === 'week') {
                $result[$time_line]['label'] = 'Week ' . $time_line;
            } else {
                $result[$time_line]['label'] = $time_line;
            }

            // $result[]['period'] = $time_line;
            if(!empty($order_totals->total_sales)) {                
                // $result[$time_line] = (int)$order_totals->total_sales;                
                $result[$time_line]['total_sale'] = (int)$order_totals->total_sales;
                $result[$time_line]['order_count'] = (int)$order_totals->order_count;
                
            } else {
                $result[$time_line]['total_sale'] = 0;
                $result[$time_line]['order_count'] = 0;
                
            }
            
        }

        sort($result);

        return $result;
    }

    public static function get_total_income($period = false)
    {
        global $wpdb;

        $query = array();
        $query['fields'] = "SELECT SUM(meta.meta_value) AS total_sales FROM {$wpdb->posts} as posts";
        $query['join']   = "LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id";
        $query['where']  = "WHERE meta.meta_key = '_order_total'";
        $query['where'] .= " AND posts.post_type = 'shop_order'";
        $query['where'] .= " AND posts.post_status = 'wc-completed'";

        $order_totals = $wpdb->get_row(implode(' ', $query));    
        
        if(is_null($order_totals->total_sales)) {
            $order_totals->total_sales = 0;
        }

        return $order_totals->total_sales;
    }

    public static function get_orders()
    {
        $pending_order = new WP_Query(
            array(
                'post_type' => 'shop_order',
                'post_status' => array('wc-processing')
            )
        );

        return $pending_order->post_count;
    }

    public static function get_customers()
    {
        global $wpdb;
        $user = array();
        // $best_customers = array();
        // $best_customers_query = array();
        $all_customer_args = array(
            'role' => 'customer',
        );

        $new_customer_args = array(
            'role' => 'customer',
            'date_query' => array(
                'after' => array(
                    'month' => date('m')
                )
            ),
        );

        $all_customers = new WP_User_Query($all_customer_args);
        $new_customers = new WP_User_Query($new_customer_args);

        $user['all_customers'] = $all_customers->get_total();
        if($new_customers->get_total()) {
            $user['new_customers'] = $new_customers->get_total();
        } else {
            $user['new_customers'] = 0;
        }
        

        $result_arr = array();
        $all_customer_args = array(
            'role' => 'customer',
        );

        $all_customers = new WP_User_Query($all_customer_args);
        $customers_arr = $all_customers->get_results();
        foreach($customers_arr as $customer) {
            $result_arr[$customer->ID]['user_id'] = $customer->ID;
            $result_arr[$customer->ID]['user_name'] = $customer->display_name;
            $result_arr[$customer->ID]['user_email'] = $customer->user_email;
            $result_arr[$customer->ID]['order_count'] = wc_get_customer_order_count($customer->ID);
            $result_arr[$customer->ID]['total_spent'] = wc_get_customer_total_spent($customer->ID);
        }
        usort($result_arr, function($a, $b) {
            return $b['total_spent'] - $a['total_spent'];
        });
        $result = array_slice($result_arr, 0 ,8);

        $user['best_customers'] = $result;

        return $user;
    }

    public static function get_recent_comments()
    {
        $result = array();

        $comments_args = array(
            // 'count' => true,
            'status' => 'hold'
        );

        $comments = get_comments( $comments_args );

        $result['comments'] = $comments;

        foreach($comments as $key => $comment) {
            $result['comments'][$key]->author_avatar = get_avatar_url($comment->comment_author_email);
        }

        $result['comments_count'] = count($result['comments']);

        return $result;
    }

    // public static function update_comment_status(int $comment_id, string $status) {
    //     wp_set_comment_status($comment_id, $status);
    // }

    // public static function get_this_month_comments() {
    //     $args = 
    // }

    public static function get_payment_method()
    {
        global $wpdb;
        $result = array();

        $gateways_obj = new WC_Payment_Gateways();
        $gatesway_arr = $gateways_obj->payment_gateways();

        $i = 0;

        foreach($gatesway_arr as $gateway => $value) {
            $query = array();

            $query['fields'] = "SELECT SUM(meta2.meta_value) AS total_sales, COUNT(meta.meta_value) as payment_order_count FROM {$wpdb->posts} AS posts";
            $query['join']   = "INNER JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id";
            $query['join']  .= " INNER JOIN {$wpdb->postmeta} AS meta2 ON posts.ID = meta2.post_id";
            $query['join']  .= " INNER JOIN {$wpdb->postmeta} AS meta3 ON posts.ID = meta3.post_id";
            $query['where']  = "WHERE posts.post_type = 'shop_order'";
            $query['where'] .= " AND posts.post_status = 'wc-completed'";
            $query['where'] .= " AND meta.meta_key = '_payment_method'";
            $query['where'] .= " AND meta.meta_value = '{$value->id}'";
            $query['where'] .= " AND meta2.meta_key = '_order_total'";
            $query['where'] .= " AND meta3.meta_key = '_payment_method_title'";

            $result[$i] = $wpdb->get_row(implode(' ', $query), ARRAY_A);
            $result[$i]['payment_title'] = $value->method_title;
            $result[$i]['payment_slug'] = $value->id;

            if( empty($result[$i]['total_sales']) ) {
                $result[$i]['total_sales'] = 0;
            }

            $i++;
        }

        return $result;
    }  
    
    public static function filter_billing_country() {
        global $wpdb;

        $result = array();
        $sum = 0;
        // $result = array();

        $query = array();

        $query['fields'] = "SELECT SUM(meta.meta_value) as total_country_billing, meta2.meta_value as country_code, COUNT(meta2.meta_value) as order_count FROM {$wpdb->posts} as posts";
        $query['join']   = "INNER JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id ";     
        $query['join']   .= "INNER JOIN {$wpdb->postmeta} AS meta2 ON posts.ID = meta2.post_id";   
        $query['where']  = "WHERE posts.post_type = 'shop_order'";
        $query['where'] .= " AND posts.post_status = 'wc-completed'";
        $query['where'] .= " AND meta.meta_key = '_order_total'";
        $query['where'] .= " AND meta2.meta_key = '_billing_country'";        
        $query['groupby'] = "GROUP BY country_code";
        $query['orderby'] = "ORDER BY total_country_billing DESC";
        $query['limits']  = "LIMIT 5";

        $result = $wpdb->get_results( implode( ' ', $query ) ); 
        
        $country_arr = self::country_array();

        // for($i = 0; $i <= count($result); $i++) {
        //     $result[$i]->country = $country_arr[$result[$i]->country_code];
        // }

        foreach($result as $k => $v) {
            if($v->country_code !== '') {
                $v->country = $country_arr[$v->country_code];                
            } else {
                $v->country = esc_html__('Unknown', 'nbt-solution');
            }
            $sum += $v->total_country_billing;
        }

        $remain_order_arr = array(
            'post_type' => 'shop_order',
            'post_status' => 'wc-completed'
        );

        $remain_order_query = new WP_Query($remain_order_arr);
        
        $total_income = self::get_total_income();

        $other = $total_income - $sum;

        $push = array('total_country_billing' => $other, 'country_code' => 'other', 'country' => esc_html__('Other', 'nbt-solution'), 'order_count' => $remain_order_query->found_posts);

        array_push($result, $push);

        return $result;
    }  

    public static function dashboard_reports()
    {
        $arr = array();

        // $arr['best_seller'] = self::get_best_seller('year', 10);
        $arr['total_income'] = self::get_total_income();
        //TODO Remove this because there is a endpoint for this ?
        $arr['recent_income'] = self::get_recent_income('month');
        $arr['orders'] = self::get_orders();
        $arr['customers'] = self::get_customers();
        $arr['payments'] = self::get_payment_method();
        $arr['comments_on_hold'] = self::get_recent_comments();
        $arr['billing_country'] = self::filter_billing_country();

        return $arr;
    }

    public static function modules_list() {
        $modules_list = array();
        $modules_list['list'] = NBT_Solutions_Modules::register_modules();
        $activated_modules = get_option('solutions_core_settings');
        $modules_list['activated_modules'] = $activated_modules ? $activated_modules : array();        

        return $modules_list;

        // return get_option('solutions_core_settings');
    }

    public static function activated_modules(array $modules_list) {
        return PREFIX_CORE::compile_modules($modules_list);
    }


    public static function update_module_setting($module, $settings) {
        $option_name = $module . '_settings';
        $get_transient = get_transient( $option_name );

        if( $get_transient ) {
            delete_transient( $option_name );
        }

        update_option($option_name, $settings);
    }

    public static function get_module_settings($module) {
        return get_option($module . '_settings');
    }

    public static function get_settings() {
        // $module_settings = array();
        $instance_arr = NBT_Solutions_Modules::$settings;

        foreach($instance_arr as $k => $v) {
            foreach($v['settings'] as $settings => $setting_val) {
                if($setting_val['type'] !== 'repeater' && $setting_val['type'] !== 'border') {
                    if(!array_key_exists('value', $setting_val)) {
                        if(array_key_exists('default', $setting_val)) {
                            $instance_arr[$k]['settings'][$settings]['value'] = $setting_val['default'];
                        }
                    }
                }                
            }
        }

        // return $module_settings;
        return $instance_arr;
    }

    public static function country_array() {
        return $countries = array(
            'AF' => 'Afghanistan',
            'AX' => 'Aland Islands',
            'AL' => 'Albania',
            'DZ' => 'Algeria',
            'AS' => 'American Samoa',
            'AD' => 'Andorra',
            'AO' => 'Angola',
            'AI' => 'Anguilla',
            'AQ' => 'Antarctica',
            'AG' => 'Antigua And Barbuda',
            'AR' => 'Argentina',
            'AM' => 'Armenia',
            'AW' => 'Aruba',
            'AU' => 'Australia',
            'AT' => 'Austria',
            'AZ' => 'Azerbaijan',
            'BS' => 'Bahamas',
            'BH' => 'Bahrain',
            'BD' => 'Bangladesh',
            'BB' => 'Barbados',
            'BY' => 'Belarus',
            'BE' => 'Belgium',
            'BZ' => 'Belize',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BT' => 'Bhutan',
            'BO' => 'Bolivia',
            'BA' => 'Bosnia And Herzegovina',
            'BW' => 'Botswana',
            'BV' => 'Bouvet Island',
            'BR' => 'Brazil',
            'IO' => 'British Indian Ocean Territory',
            'BN' => 'Brunei Darussalam',
            'BG' => 'Bulgaria',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'KH' => 'Cambodia',
            'CM' => 'Cameroon',
            'CA' => 'Canada',
            'CV' => 'Cape Verde',
            'KY' => 'Cayman Islands',
            'CF' => 'Central African Republic',
            'TD' => 'Chad',
            'CL' => 'Chile',
            'CN' => 'China',
            'CX' => 'Christmas Island',
            'CC' => 'Cocos (Keeling) Islands',
            'CO' => 'Colombia',
            'KM' => 'Comoros',
            'CG' => 'Congo',
            'CD' => 'Congo, Democratic Republic',
            'CK' => 'Cook Islands',
            'CR' => 'Costa Rica',
            'CI' => 'Cote D\'Ivoire',
            'HR' => 'Croatia',
            'CU' => 'Cuba',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DK' => 'Denmark',
            'DJ' => 'Djibouti',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'EC' => 'Ecuador',
            'EG' => 'Egypt',
            'SV' => 'El Salvador',
            'GQ' => 'Equatorial Guinea',
            'ER' => 'Eritrea',
            'EE' => 'Estonia',
            'ET' => 'Ethiopia',
            'FK' => 'Falkland Islands (Malvinas)',
            'FO' => 'Faroe Islands',
            'FJ' => 'Fiji',
            'FI' => 'Finland',
            'FR' => 'France',
            'GF' => 'French Guiana',
            'PF' => 'French Polynesia',
            'TF' => 'French Southern Territories',
            'GA' => 'Gabon',
            'GM' => 'Gambia',
            'GE' => 'Georgia',
            'DE' => 'Germany',
            'GH' => 'Ghana',
            'GI' => 'Gibraltar',
            'GR' => 'Greece',
            'GL' => 'Greenland',
            'GD' => 'Grenada',
            'GP' => 'Guadeloupe',
            'GU' => 'Guam',
            'GT' => 'Guatemala',
            'GG' => 'Guernsey',
            'GN' => 'Guinea',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HT' => 'Haiti',
            'HM' => 'Heard Island & Mcdonald Islands',
            'VA' => 'Holy See (Vatican City State)',
            'HN' => 'Honduras',
            'HK' => 'Hong Kong',
            'HU' => 'Hungary',
            'IS' => 'Iceland',
            'IN' => 'India',
            'ID' => 'Indonesia',
            'IR' => 'Iran, Islamic Republic Of',
            'IQ' => 'Iraq',
            'IE' => 'Ireland',
            'IM' => 'Isle Of Man',
            'IL' => 'Israel',
            'IT' => 'Italy',
            'JM' => 'Jamaica',
            'JP' => 'Japan',
            'JE' => 'Jersey',
            'JO' => 'Jordan',
            'KZ' => 'Kazakhstan',
            'KE' => 'Kenya',
            'KI' => 'Kiribati',
            'KR' => 'Korea',
            'KW' => 'Kuwait',
            'KG' => 'Kyrgyzstan',
            'LA' => 'Lao People\'s Democratic Republic',
            'LV' => 'Latvia',
            'LB' => 'Lebanon',
            'LS' => 'Lesotho',
            'LR' => 'Liberia',
            'LY' => 'Libyan Arab Jamahiriya',
            'LI' => 'Liechtenstein',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'MO' => 'Macao',
            'MK' => 'Macedonia',
            'MG' => 'Madagascar',
            'MW' => 'Malawi',
            'MY' => 'Malaysia',
            'MV' => 'Maldives',
            'ML' => 'Mali',
            'MT' => 'Malta',
            'MH' => 'Marshall Islands',
            'MQ' => 'Martinique',
            'MR' => 'Mauritania',
            'MU' => 'Mauritius',
            'YT' => 'Mayotte',
            'MX' => 'Mexico',
            'FM' => 'Micronesia, Federated States Of',
            'MD' => 'Moldova',
            'MC' => 'Monaco',
            'MN' => 'Mongolia',
            'ME' => 'Montenegro',
            'MS' => 'Montserrat',
            'MA' => 'Morocco',
            'MZ' => 'Mozambique',
            'MM' => 'Myanmar',
            'NA' => 'Namibia',
            'NR' => 'Nauru',
            'NP' => 'Nepal',
            'NL' => 'Netherlands',
            'AN' => 'Netherlands Antilles',
            'NC' => 'New Caledonia',
            'NZ' => 'New Zealand',
            'NI' => 'Nicaragua',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'NU' => 'Niue',
            'NF' => 'Norfolk Island',
            'MP' => 'Northern Mariana Islands',
            'NO' => 'Norway',
            'OM' => 'Oman',
            'PK' => 'Pakistan',
            'PW' => 'Palau',
            'PS' => 'Palestinian Territory, Occupied',
            'PA' => 'Panama',
            'PG' => 'Papua New Guinea',
            'PY' => 'Paraguay',
            'PE' => 'Peru',
            'PH' => 'Philippines',
            'PN' => 'Pitcairn',
            'PL' => 'Poland',
            'PT' => 'Portugal',
            'PR' => 'Puerto Rico',
            'QA' => 'Qatar',
            'RE' => 'Reunion',
            'RO' => 'Romania',
            'RU' => 'Russian Federation',
            'RW' => 'Rwanda',
            'BL' => 'Saint Barthelemy',
            'SH' => 'Saint Helena',
            'KN' => 'Saint Kitts And Nevis',
            'LC' => 'Saint Lucia',
            'MF' => 'Saint Martin',
            'PM' => 'Saint Pierre And Miquelon',
            'VC' => 'Saint Vincent And Grenadines',
            'WS' => 'Samoa',
            'SM' => 'San Marino',
            'ST' => 'Sao Tome And Principe',
            'SA' => 'Saudi Arabia',
            'SN' => 'Senegal',
            'RS' => 'Serbia',
            'SC' => 'Seychelles',
            'SL' => 'Sierra Leone',
            'SG' => 'Singapore',
            'SK' => 'Slovakia',
            'SI' => 'Slovenia',
            'SB' => 'Solomon Islands',
            'SO' => 'Somalia',
            'ZA' => 'South Africa',
            'GS' => 'South Georgia And Sandwich Isl.',
            'ES' => 'Spain',
            'LK' => 'Sri Lanka',
            'SD' => 'Sudan',
            'SR' => 'Suriname',
            'SJ' => 'Svalbard And Jan Mayen',
            'SZ' => 'Swaziland',
            'SE' => 'Sweden',
            'CH' => 'Switzerland',
            'SY' => 'Syrian Arab Republic',
            'TW' => 'Taiwan',
            'TJ' => 'Tajikistan',
            'TZ' => 'Tanzania',
            'TH' => 'Thailand',
            'TL' => 'Timor-Leste',
            'TG' => 'Togo',
            'TK' => 'Tokelau',
            'TO' => 'Tonga',
            'TT' => 'Trinidad And Tobago',
            'TN' => 'Tunisia',
            'TR' => 'Turkey',
            'TM' => 'Turkmenistan',
            'TC' => 'Turks And Caicos Islands',
            'TV' => 'Tuvalu',
            'UG' => 'Uganda',
            'UA' => 'Ukraine',
            'AE' => 'United Arab Emirates',
            'GB' => 'United Kingdom',
            'US' => 'United States',
            'UM' => 'United States Outlying Islands',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VU' => 'Vanuatu',
            'VE' => 'Venezuela',
            'VN' => 'Viet Nam',
            'VG' => 'Virgin Islands, British',
            'VI' => 'Virgin Islands, U.S.',
            'WF' => 'Wallis And Futuna',
            'EH' => 'Western Sahara',
            'YE' => 'Yemen',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
        );        
    }

}