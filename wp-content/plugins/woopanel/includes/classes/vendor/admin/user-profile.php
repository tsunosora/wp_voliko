<?php

class WooPanel_Seller_Admin_Profile {

    /**
     * Total vendors found
     *
     * @var integer
     */
    private $total_users;

    function __construct()
    {
        add_action( 'show_user_profile', array( $this, 'add_meta_fields' ), 20 );
        add_action( 'edit_user_profile', array( $this, 'add_meta_fields' ), 20 );

        add_action( 'personal_options_update', array( $this, 'save_meta_fields' ) );
        add_action( 'edit_user_profile_update', array( $this, 'save_meta_fields' ) );

    }

    public function add_meta_fields( $user ) {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }

        $post_data = wp_unslash( $_POST );

        $selling         = get_user_meta( $user->ID, 'woopanel_enable_selling', true );
        ?>
        <h3><?php esc_html_e( 'WooPanel Options', 'woopanel' ); ?></h3>

        <table class="form-table">
            <tbody>
                <tr>
                    <th><?php esc_html_e( 'Selling', 'woopanel' ); ?></th>
                    <td>
                        <label for="woopanel_enable_selling">
                            <input type="hidden" name="woopanel_enable_selling" value="no">
                            <input name="woopanel_enable_selling" type="checkbox" id="woopanel_enable_selling" value="yes" <?php checked( $selling, 'yes' ); ?> />
                            <?php esc_html_e( 'Enable Adding Products', 'woopanel' ); ?>
                        </label>

                        <p class="description"><?php esc_html_e( 'Enable or disable product adding capability', 'woopanel' ); ?></p>
                    </td>
                </tr>

            </tbody>
        </table>
        <?php
    }

    /**
     * Save user data
     *
     * @param int $user_id
     *
     * @return void
     */
    function save_meta_fields( $user_id ) {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }

        $post_data = wp_unslash( $_POST );

        if ( ! isset( $post_data['woopanel_enable_selling'] ) ) {
            return;
        }

        $selling         = sanitize_text_field( $post_data['woopanel_enable_selling'] );

        update_user_meta( $user_id, 'woopanel_enable_selling', $selling );

    }

}

new WooPanel_Seller_Admin_Profile();