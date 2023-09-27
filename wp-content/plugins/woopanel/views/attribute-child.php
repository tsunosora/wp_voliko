<div class="m-portlet__head">
    <div class="m-portlet__head-caption">
        <div class="m-portlet__head-title">
            <h3 class="m-portlet__head-text"><?php echo esc_attr($label); ?></h3>
        </div>
    </div>
</div>

<div class="m-portlet__body m-attribute-wrapper <?php echo ! empty($this->edit) ? 'm-attribute-edit' : 'm-attribute-add';?>">
    <form class="m-form" action="" method="POST">
        <?php
		echo wp_kses(
			wpautop( __( 'Attribute terms can be assigned to products and variations.<br/><br/><strong>Note</strong>: Deleting a term will remove it from all products and variations to which it has been assigned. Recreating a term will not automatically assign it back to products.', 'woopanel' ) ),
			array( 'p' => array(), 'br' => array(), 'strong' => array() )
        );
        ?>
        <?php wp_nonce_field( 'add_taxonomy', 'tax_' . esc_attr($this->taxonomy) ); ?>
        <?php
        woopanel_form_field(
            'term_name', 
            [
                'type'			=> 'text',
                'label'	=> esc_html__('Name', 'woopanel' ),
                'id'			=> 'term_name',
                'desc_tip'    => 'true',
                'description'   =>  esc_html__( 'The name is how it appears on your site.', 'woopanel' )
            ],
            $term->name
        );

        if ( ! global_terms_enabled() ) {
            woopanel_form_field(
                'term_slug', 
                [
                    'type'			=> 'text',
                    'label'	=> esc_html__('Slug', 'woopanel' ),
                    'id'			=> 'term_slug',
                    'desc_tip'    => 'true',
                    'description'   => esc_html__( 'The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'woopanel' )
                ],
                $term->slug
            );
        }

        woopanel_form_field(
            'term_description', 
            [
                'type'			=> 'textarea',
                'label'	=> esc_html__('Description', 'woopanel' ),
                'id'			=> 'term_description',
                'desc_tip'    => 'true',
                'description'   => esc_html__( 'The description is not prominent by default; however, some themes may show it.', 'woopanel' )
            ],
            $term->description
        );

        if( $this->edit ) {
            do_action($this->taxonomy . '_edit_form_fields', $term, $this->taxonomy);
        }else {
            do_action($this->taxonomy . '_add_form_fields', $this->taxonomy);
        }

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