<?php
/**
 * @version    1.0
 * @package    Package Name
 * @author     Your Team <support@yourdomain.com>
 * @copyright  Copyright (C) 2014 yourdomain.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

/**
 * Plug additional sidebars into WordPress.
 *
 * @package  Package Name
 * @since    1.0
 */


class NBT_Solutions_Metabox {
	/**
	 * Variable to hold the initialization state.
	 *
	 * @var  boolean
	 */
	protected static $initialized = false;



	/**
	 * Initialize functions.
	 *
	 * @return  void
	 */
	public static function initialize() {
		// Do nothing if pluggable functions already initialized.
		if ( self::$initialized ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array(__CLASS__, 'embed_script') );

		// State that initialization completed.
		self::$initialized = true;
	}

	public static function set_field(){
		return apply_filters('nbt_metabox_fields', array(
			'text', 'color'
		) );
	}

	public static function show_field($field, $value = false){
		if(isset($field['id']) && isset($value[$field['id']])){
			$value = $value[$field['id']];
		}elseif(isset($field['default'])){
			$value = $field['default'];
		}
		if(is_array($field)){
			switch ($field['type']) {
				case 'color':
					?>
					<tr valign="top">
			            <th scope="row" class="titledesc">
			                <label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['name'] ); ?></label>
			            </th>
			            <td class="forminp forminp-<?php echo sanitize_title( $field['type'] ) ?>">
			            	<input type="text" id="term-<?php echo esc_attr( $field['name'] ) ?>" name="<?php echo esc_attr( $field['id'] ) ?>" class="nbt-colorpicker" value="<?php echo esc_attr( $value ) ?>" />
			            </td>
			        </tr>
					<?php
					break;

				case 'text':
					?>
					<tr valign="top">
			            <th scope="row" class="titledesc">
			                <label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['name'] ); ?></label>
			            </th>
			            <td class="forminp forminp-<?php echo sanitize_title( $field['type'] ) ?>">
			            	<input type="text" name="<?php echo esc_attr( $field['id'] ) ?>" value="<?php echo esc_attr( $value ) ?>" style="width: 100%;" />
			            </td>
			        </tr>
					<?php
					break;
				case 'textarea':
					?>
					<tr valign="top">
			            <th scope="row" class="titledesc">
			                <label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['name'] ); ?></label>
			            </th>
			            <td class="forminp forminp-<?php echo sanitize_title( $field['type'] ) ?>">
			            	<textarea style="width: 100%" rows="<?php if(isset($field['rows'])){ echo $field['rows'];}else{ echo '3';}?>" name="<?php echo esc_attr( $field['id'] ) ?>"><?php echo esc_attr( $value ) ?></textarea>
			            	<?php if(isset($field['desc_tip'])){
			            		echo $field['desc_tip'];
			            	}?>
			            </td>
			        </tr>
					<?php
					break;
				case 'repeater':
					?>
					<tr valign="top">
			            <th scope="row" class="titledesc">
			                <label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['name'] ); ?></label>
			            </th>
			            <td class="forminp forminp-<?php echo sanitize_title( $field['type'] ) ?>">
			            	<?php include($field['temp']);?>
			            </td>
			        </tr>
			        <?php
					break;
				case 'border':
					?>
					<tr valign="top">
			            <td colspan="2" class="forminp forminp-<?php echo sanitize_title( $field['type'] ) ?>"><hr /></td>
			        </tr>
					<?php
					break;
				case 'radio_image':?>
					<tr valign="top">
			            <th scope="row" class="titledesc">
			                <label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['name'] ); ?></label>
			            </th>
			            <td class="forminp forminp-<?php echo sanitize_title( $field['type'] ) ?>">

						    <div class="nbt-ri-selector">
						    	<?php if(isset($field['option'])){
						    		foreach ($field['option'] as $k_ri => $ri) {?>
							        <input<?php if($k_ri == $value){ echo ' checked="checked"';}?> id="<?php echo $k_ri;?>" type="radio" name="<?php echo $field['id'];?>" value="<?php echo $k_ri;?>" />
							        <label class="nbt-ri-selector-label" for="<?php echo $k_ri;?>" style="background-image:url('<?php echo esc_url($ri['src']);?>');"></label>
						        <?php }
						        }?>
						    </div>
			            </td>
			        </tr>
			        <?php

					break;
				case 'checkbox':?>
					<tr valign="top">
			            <th scope="row" class="titledesc">
			                <label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['name'] ); ?></label>
			            </th>
			            <td class="forminp forminp-<?php echo sanitize_title( $field['type'] ) ?>">
							<div class="checkbox">
							  <label><input type="checkbox" name="<?php echo $field['id'];?>" value="1"<?php if($value == 1){ echo ' checked';}?>> <?php echo $field['label'];?></label>
							</div>
			            </td>
			        </tr>

					<?php
					break;
				case 'image':
					
					?>
					<tr valign="top">
			            <th scope="row" class="titledesc">
			                <label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['name'] ); ?></label>
			            </th>
			            <td class="forminp forminp-<?php echo sanitize_title( $field['type'] ) ?>">
							<?php
					        $image = $value ? wp_get_attachment_image_src( $value ) : '';
					        $image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';
					        ?>
					        <div class="nbtmcs-wrap-image">
					            <div class="nbtmcs-term-image-thumbnail" style="float:left;margin-right:10px;">
					                <img src="<?php echo esc_url( $image ) ?>" width="60px" height="60px" />
					            </div>
					            <div class="right-button-term" style="line-height:60px;">
					                <input type="hidden" class="nbtmcs-term-image" name="<?php echo $field['id'];?>" value="<?php echo esc_attr( $value ) ?>" />
					                <button type="button" class="nbtmcs-upload-image-button button"><?php esc_html_e( 'Upload/Add image', 'wcvs' ); ?></button>
					                <button type="button" class="nbtmcs-remove-image-button button <?php echo $value ? '' : 'hidden' ?>"><?php esc_html_e( 'Remove image', 'wcvs' ); ?></button>
					            </div>
					        </div>
			            </td>
			        </tr>
			        <?php
					break;
				case $field['type']:
					echo apply_filters('nbt_admin_field_'.$field['type'], $field['type'], $field, $value );
					break;

				default:
					# code...

					break;
			}

		}
	}

	public static function embed_script(){
        if( ! defined('PREFIX_NBT_SOL')){
            wp_enqueue_style( 'spectrum', AJAX_SEARCH_URL . 'assets/css/spectrum.css'  );
            wp_enqueue_script( 'spectrum', AJAX_SEARCH_URL . 'assets/js/spectrum.js', null, null, true );
        }
	}
}