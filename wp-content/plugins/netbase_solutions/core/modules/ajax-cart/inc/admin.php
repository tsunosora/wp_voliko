<?php
class NBT_WooCommerce_AjaxCart_Admin{

	
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array($this, 'nbt_ajaxcart_scripts_method') );
        add_filter('nbt_admin_field_icon', array($this, 'admin_icon'), 10, 3 );

	}

    public function ntb_cs_page(){
        include(NB_AJAXCART_PATH .'tpl/admin.php');
    }

    public function admin_icon($html, $field, $value){

        ob_start();
        ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['name'] ); ?></label>
            </th>
            <td class="forminp forminp-<?php echo sanitize_title( $field['type'] ) ?>">
                <ul class="<?php echo esc_attr( $field['id'] ); ?>">
                    <?php foreach ($field['options'] as $k => $icon) {?>
                    <li<?php if($value == $icon){ echo ' class="active"';}?> data-icon="<?php echo $icon;?>">
                        <i class="<?php echo $icon;?>"></i>
                        <input type="radio" name="<?php echo esc_attr( $field['id'] ); ?>" value="<?php echo $icon;?>"<?php if($value == $icon){ echo ' checked';}?> />
                    </li>
                    <?php }?>
                </ul>
            </td>
        </tr>
        <?php $html = ob_get_clean();

        return $html;
    }

	public function nbt_ajaxcart_scripts_method($hooks){
		wp_enqueue_style( 'admin', NB_AJAXCART_URL . 'assets/css/admin.css'  );
        wp_enqueue_script( 'admin', NB_AJAXCART_URL . 'assets/js/admin.js', null, null, true );
	}
}
new NBT_WooCommerce_AjaxCart_Admin();