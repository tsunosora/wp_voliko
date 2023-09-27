<div class="m-portlet__head">
    <div class="m-portlet__head-caption">
        <div class="m-portlet__head-title">
            <h3 class="m-portlet__head-text"><?php echo esc_attr($label); ?></h3>
        </div>
    </div>
</div>

<div class="m-portlet__body m-attribute-wrapper">
    <form class="m-form" action="" method="POST">
        <p><?php esc_html_e( 'Attributes let you define extra product data, such as size or color. You can use these attributes in the shop sidebar using the "layered nav" widgets.', 'woopanel' ); ?></p>
        <?php wp_nonce_field( 'add_taxonomy', 'tax_' . esc_attr($this->taxonomy) ); ?>
        <?php
        woopanel_form_field(
            'attribute_name', 
            [
                'type'			=> 'text',
                'label'	=> esc_html__('Name', 'woopanel' ),
                'id'			=> 'attribute_name',
                'desc_tip'    => 'true',
                'description'   => esc_html__( 'Name for the attribute (shown on the front-end).', 'woopanel' )
            ],
            $attribute->attribute_label
        );

        if ( ! global_terms_enabled() ) {
            woopanel_form_field(
                'attribute_slug', 
                [
                    'type'			=> 'text',
                    'label'	=> esc_html__('Slug', 'woopanel' ),
                    'id'			=> 'attribute_slug',
                    'desc_tip'    => 'true',
                    'description'   => esc_html__( 'Unique slug/reference for the attribute; must be no more than 28 characters.', 'woopanel' )
                ],
                $attribute->attribute_name
            );
        }

        if ( wc_has_custom_attribute_types() ) {
            woopanel_form_field(
                'attribute_type',
                array(
                    'type'	  => 'select',
                    'id'      => 'attribute_type',
                    'label'   => esc_html__( 'Type', 'woopanel' ),
                    'options' => wc_get_attribute_types(),
                    'description' => esc_html__( "Determines how this attribute's values are displayed.", 'woopanel' )
                ),
                $attribute->attribute_type
            );
        }


        if( $this->edit ) {
            do_action('woocommerce_after_edit_attribute_fields');
        }else {
            do_action('woocommerce_after_add_attribute_fields');
        }
        
        woopanel_form_field(
            'attribute_orderby',
            array(
                'type'	  => 'select',
                'id'      => 'attribute_orderby',
                'label'   => esc_html__( 'Default sort order', 'woopanel' ),
                'options' => array(
                    'menu_order'=> esc_html__( 'Custom ordering', 'woopanel' ),
                    'name'      => esc_html__( 'Name', 'woopanel' ),
                    'name_num'  => esc_html__( 'Name (numeric)', 'woopanel' ),
                    'id'        => esc_html__( 'Term ID', 'woopanel' )
                ),
                'description' => esc_html__( 'Determines the sort order of the terms on the frontend shop product pages. If using custom ordering, you can drag and drop the terms in this attribute.', 'woopanel' )
            ),
            $attribute->attribute_orderby
        );
        ?>
        <div class="btn-attribute-wrapper">
            <button type="submit" name="submit" class="btn btn-primary m-btn m-loader--light m-loader--right" id="publish" onclick="if(!this.classList.contains('m-loader')) this.className+=' m-loader';"><?php
            if( $this->edit ) {
                esc_html_e('Update', 'woopanel' );
            }else {
                esc_html_e( 'Add attribute', 'woopanel' );
            }?></button>
        </div>

    </form>
</div>