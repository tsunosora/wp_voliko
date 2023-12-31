<script type="text/html" id="tmpl-dokan-add-new-product">
    <div id="dokan-add-new-product-popup" class="white-popup dokan-add-new-product-popup">
        <h2><i class="fa fa-briefcase">&nbsp;</i>&nbsp;<?php _e( 'Add New Product', 'printshop' ); ?></h2>

        <form action="" method="post" id="dokan-add-new-product-form">
            <div class="product-form-container">
                <div class="content-half-part dokan-feat-image-content">
                    <div class="dokan-feat-image-upload">
                        <?php
                        $wrap_class        = ' dokan-hide';
                        $instruction_class = '';
                        $feat_image_id     = 0;
                        ?>
                        <div class="instruction-inside<?php echo $instruction_class; ?>">
                            <input type="hidden" name="feat_image_id" class="dokan-feat-image-id" value="<?php echo $feat_image_id; ?>">

                            <i class="fa fa-cloud-upload"></i>
                            <a href="#" class="dokan-feat-image-btn btn btn-sm"><?php _e( 'Upload a product cover image', 'printshop' ); ?></a>
                        </div>

                        <div class="image-wrap<?php echo $wrap_class; ?>">
                            <a class="close dokan-remove-feat-image">&times;</a>
                            <img height="" width="" src="" alt="">
                        </div>
                    </div>
                </div>
                <div class="content-half-part dokan-product-field-content">
                    <div class="dokan-form-group">
                        <input type="text" class="dokan-form-control" name="post_title", placeholder="<?php _e( 'Product name..', 'printshop' ); ?>">
                    </div>

                    <div class="dokan-clearfix">
                        <div class="dokan-form-group dokan-clearfix dokan-price-container">
                            <div class="content-half-part">
                                <label for="_regular_price" class="form-label"><?php _e( 'Price', 'printshop' ); ?></label>

                                <div class="dokan-input-group">
                                    <span class="dokan-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                    <input type="text" class="dokan-form-control" name="_regular_price" placeholder="0.00">
                                </div>
                            </div>

                            <div class="content-half-part sale-price">
                                <label for="_sale_price" class="form-label">
                                    <?php _e( 'Discounted Price', 'printshop' ); ?>
                                    <a href="#" class="sale_schedule"><?php _e( 'Schedule', 'printshop' ); ?></a>
                                    <a href="#" class="cancel_sale_schedule dokan-hide"><?php _e( 'Cancel', 'printshop' ); ?></a>
                                </label>

                                <div class="dokan-input-group">
                                    <span class="dokan-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                    <input type="text" class="dokan-form-control" name="_sale_price" placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <div class="dokan-hide sale-schedule-container sale_price_dates_fields dokan-clearfix dokan-form-group">
                            <div class="content-half-part from">
                                <div class="dokan-input-group">
                                    <span class="dokan-input-group-addon"><?php _e( 'From', 'printshop' ); ?></span>
                                    <input type="text" name="_sale_price_dates_from" class="dokan-form-control datepicker sale_price_dates_from" value="" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" placeholder="YYYY-MM-DD">
                                </div>
                            </div>

                            <div class="content-half-part to">
                                <div class="dokan-input-group">
                                    <span class="dokan-input-group-addon"><?php _e( 'To', 'printshop' ); ?></span>
                                    <input type="text" name="_sale_price_dates_to" class="dokan-form-control datepicker sale_price_dates_to" value="" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" placeholder="YYYY-MM-DD">
                                </div>
                            </div>
                        </div><!-- .sale-schedule-container -->
                    </div>
                </div>
                <div class="dokan-clearfix"></div>
                <div class="product-full-container">
                    <?php if ( dokan_get_option( 'product_category_style', 'dokan_selling', 'single' ) == 'single' ): ?>
                        <div class="dokan-form-group">
                            <?php
                            $product_cat = -1;
                            $category_args =  array(
                                'show_option_none' => __( '- Select a category -', 'printshop' ),
                                'hierarchical'     => 1,
                                'hide_empty'       => 0,
                                'name'             => 'product_cat',
                                'id'               => 'product_cat',
                                'taxonomy'         => 'product_cat',
                                'title_li'         => '',
                                'class'            => 'product_cat dokan-form-control dokan-select2',
                                'exclude'          => '',
                                'selected'         => $product_cat,
                            );

                            wp_dropdown_categories( apply_filters( 'dokan_product_cat_dropdown_args', $category_args ) );
                        ?>
                        </div>
                    <?php elseif ( dokan_get_option( 'product_category_style', 'dokan_selling', 'single' ) == 'multiple' ): ?>
                        <div class="dokan-form-group">
                            <?php
                            $term = array();
                            include_once DOKAN_LIB_DIR.'/class.taxonomy-walker.php';
                            $drop_down_category = wp_dropdown_categories( array(
                                'show_option_none' => '',
                                'hierarchical'     => 1,
                                'hide_empty'       => 0,
                                'name'             => 'product_cat[]',
                                'id'               => 'product_cat',
                                'taxonomy'         => 'product_cat',
                                'title_li'         => '',
                                'class'            => 'product_cat dokan-form-control dokan-select2',
                                'exclude'          => '',
                                'selected'         => $term,
                                'echo'             => 0,
                                'walker'           => new DokanTaxonomyWalker()
                            ) );

                            echo str_replace( '<select', '<select data-placeholder="'.__( 'Select product category','printshop' ).'" multiple="multiple" ', $drop_down_category );
                            ?>
                        </div>
                    <?php endif; ?>

                    <div class="dokan-form-group">
                        <?php
                        require_once DOKAN_LIB_DIR.'/class.taxonomy-walker.php';
                        $drop_down_tags = wp_dropdown_categories( array(
                            'show_option_none' => '',
                            'hierarchical'     => 1,
                            'hide_empty'       => 0,
                            'name'             => 'product_tag[]',
                            'id'               => 'product_tag',
                            'taxonomy'         => 'product_tag',
                            'title_li'         => '',
                            'class'            => 'product_tags dokan-form-control dokan-select2',
                            'exclude'          => '',
                            'selected'         => array(),
                            'echo'             => 0,
                            'walker'           => new DokanTaxonomyWalker()
                        ) );

                        echo str_replace( '<select', '<select data-placeholder="'.__( 'Select product tags','printshop' ).'" multiple="multiple" ', $drop_down_tags );
                        ?>
                    </div>

                    <div class="dokan-form-group">
                        <textarea name="post_excerpt" id="" class="dokan-form-control" rows="5" placeholder="<?php _e( 'Enter some short description about this product...') ?>"></textarea>
                    </div>
                </div>
            </div>
            <div class="product-container-footer">
                <span class="dokan-show-add-product-error"></span>
                <span class="dokan-spinner dokan-add-new-product-spinner dokan-hide"></span>
                <input type="submit" id="dokan-create-new-product-btn" class="dokan-btn dokan-btn-default" data-btn_id="create_new" value="<?php _e( 'Create product', 'printshop' ) ?>">
                <input type="submit" id="dokan-create-and-add-new-product-btn" class="dokan-btn dokan-btn-theme" data-btn_id="create_and_new" value="<?php _e( 'Create & add new', 'printshop' ) ?>">
            </div>
        </form>
    </div>
</script>
