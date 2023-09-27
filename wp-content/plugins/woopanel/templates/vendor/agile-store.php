<div class="card-title mt-3 mb-3"><?php echo esc_html__('Store Profile', 'woopanel') ?></div>
<div class="row">
    <div class="col-md-12 form-group mb-12">
        <?php
			echo WooPanel_Admin_Options::get_field_html(array(
                'title'     => esc_html__( 'Banner', 'woopanel' ),
                'desc'      => '',
                'name'      => 'data[banner_id]',
                'id'        => 'banner_id',
                'type'      => 'image',
                'default'   => '',
	            'width'     => 550,
	            'height'    => 200,
                'value'     => empty($store->banner_id) ? '' : $store->banner_id,
	            'rectangle' => true
            ));?>
    </div>

    <div class="col-md-12 form-group mb-12">
        <?php
			echo WooPanel_Admin_Options::get_field_html(array(
                'title'    => esc_html__( 'Avatar', 'woopanel' ),
                'desc'     => '',
                'name'     => 'data[logo_id]',
                'id'       => 'logo_id',
                'type'     => 'image',
                'default'  => '',
                'value'    => empty($store->logo_id) ? '' : $store->logo_id,
	            'width'    => 150,
	            'height'   => 150,
            ));?>
    </div>

    <div class="col-md-6 form-group mb-12">
        <label for="txt_email"><?php echo esc_html__('User', 'woopanel') ?></label><br />
        <select name="data[user_id]" class="form-control form-select2">
            <?php
            $blogusers = get_users( 'orderby=nicename' );
            // Array of WP_User objects.
            foreach ( $blogusers as $user ) {
                ?>
                <option value="<?php echo absint($user->ID);?>" <?php selected( $user->ID, $store->user_id ); ?>>#<?php echo absint( $user->ID ) . ' - ' . esc_html( $user->user_login ) . ' ('. esc_html( $user->user_email ) .')';?></option>
                <?php
            }
            ?>
        </select>
    </div>

</div>