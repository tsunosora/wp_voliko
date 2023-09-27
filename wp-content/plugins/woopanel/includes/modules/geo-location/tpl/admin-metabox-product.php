<div class="m-portlet">
	<div class="m-portlet__head">
		<div class="m-portlet__head-caption">
			<div class="m-portlet__head-title">
				<h3 class="m-portlet__head-text"><?php esc_html_e('Geo Location', 'woopanel' );?></h3>
			</div>
		</div>
	</div>
	<div class="m-portlet__body">
        <?php
        global $current_user;

        $geo_application_id = get_user_meta( $current_user->ID, 'geo_application_id', true );
        $geo_application_code = get_user_meta( $current_user->ID, 'geo_application_code', true );


        $desc = esc_html__( 'Please fill your product adress to show map.', 'woopanel' );
        if( empty($geo_application_id) && empty($geo_application_code) ) {
            $desc = sprintf( esc_html__( 'You did not set up api key. Please %s to set up it.', 'woopanel' ), sprintf( '<a href="'. woopanel_dashboard_url().'/settings/#geo_location">%s</a>', esc_html__('click here', 'woopanel' ) ) );
        }
        

  

        woopanel_form_field(
            'user_geo_location',
            array(
                'type'		  => 'map',
                'id'          => 'user_geo_location',
                'label'       => esc_html__( 'Product Address', 'woopanel' ),
                'placeholder' => esc_html__('Enter your store address here.', 'woopanel' ),
                'form_inline' => true,
                'description' => $desc
            ),
            get_post_meta($post_id, 'user_geo_location', true)
        );

        woopanel_form_field(
            '_product_map_lat',
            array(
                'type'		  => 'hidden',
                'id'          => 'woopanel_map_lat',
                'label'       => esc_html__( 'GEO Location', 'woopanel' ),
                'placeholder' => esc_html__('Enter your store address here.', 'woopanel' ),
                'form_inline' => true
            ),
            get_post_meta($post_id, '_product_map_lat', true)
        );

        woopanel_form_field(
            '_product_map_lng',
            array(
                'type'		  => 'hidden',
                'id'          => 'woopanel_map_lng',
                'label'       => esc_html__( 'GEO Location', 'woopanel' ),
                'placeholder' => esc_html__('Enter your store address here.', 'woopanel' ),
                'form_inline' => true
            ),
            get_post_meta($post_id, '_product_map_lng', true)
        );
        ?>
    </div>
</div>