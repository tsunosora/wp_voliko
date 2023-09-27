<?php

/**
 * This class will load field for setting page
 *
 * @package WooPanel_Modules
 */
class WooPanel_Modules_Metabox {
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

    /**
     * Display field of form settings page
     */
	public static function show_field($field, $value = false, $tr = true){
		if(!empty($value)){
			if(is_array($value) && isset($field['id']) && isset($value[$field['id']])){
				$value = $value[$field['id']];
			}
		}else{
			if(isset($field['default'])){
				$value = $field['default'];
			}
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
			            	<input type="text" id="term-<?php echo esc_attr( $field['id'] ) ?>" name="<?php echo esc_attr( $field['id'] ) ?>" class="nbt-colorpicker" value="<?php echo esc_attr( $value ) ?>" />
			            </td>
			        </tr>
			        <script type="text/javascript">
			        	jQuery(document).ready(function($){
			        		$('#term-<?php echo esc_attr( $field['id'] ) ?>').spectrum({
							    allowEmpty:false,
							    showInput: true,
							    showAlpha: true,
							    maxPaletteSize: 10,
							    preferredFormat: "hex"
							});
			        	});
			        </script>
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
			            	<textarea style="width: 100%" rows="<?php if(isset($field['rows'])){ echo esc_attr($field['rows']);}else{ echo '3';}?>" name="<?php echo esc_attr( $field['id'] ) ?>"><?php echo esc_attr( $value ) ?></textarea>
			            	<?php if(isset($field['desc_tip'])){
			            		echo esc_attr($field['desc_tip']);
			            	}?>
			            </td>
			        </tr>
					<?php
					break;
				case 'repeater':
					$field_id = $field['id'];
					$fields = $field['fields'];?>
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
							        <input<?php if($k_ri == $value){ echo ' checked="checked"';}?> id="<?php echo esc_attr($k_ri);?>" type="radio" name="<?php echo esc_attr($field['id']);?>" value="<?php echo esc_attr($k_ri);?>" />
							        <label class="nbt-ri-selector-label" for="<?php echo esc_attr($k_ri);?>" style="background-image:url('<?php echo esc_url($ri['src']);?>');"></label>
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
			                <label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo isset($field['name']) ? esc_html( $field['name'] ) : ''; ?></label>
			            </th>
			            <td class="forminp forminp-<?php echo sanitize_title( $field['type'] ) ?>">
							<div class="checkbox">
							  <label><input type="checkbox" name="<?php echo esc_attr($field['id']);?>" value="1"<?php if($value == 1){ echo ' checked';}?>> <?php echo isset($field['label']) ? esc_html( $field['label'] ) : ''; ?></label>
							</div>
			            </td>
			        </tr>

					<?php
					break;
				case 'number':
					if($tr){?>
					<tr valign="top">
			            <th scope="row" class="titledesc">
			                <label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['name'] ); ?></label>
			            </th>
			            <td class="forminp forminp-<?php echo sanitize_title( $field['type'] ) ?>"><?php }?>
			            	<input type="number" name="<?php echo esc_attr( $field['id'] ) ?>" value="<?php echo esc_attr( $value ) ?>"<?php if(isset($field['min'])){ echo ' min="'.esc_attr($field['min']).'"';}?><?php if(isset($field['max'])){ echo ' max="'.esc_attr($field['max']).'"';}?> />

			            <?php if($tr){?></td>
			        </tr>
			        <?php
			    	}
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
					                <input type="hidden" class="nbtmcs-term-image" name="<?php echo esc_attr($field['id']);?>" value="<?php echo esc_attr( $value ) ?>" />
					                <button type="button" class="nbtmcs-upload-image-button button"><?php esc_html_e( 'Upload/Add image', 'woopanel' ); ?></button>
					                <button type="button" class="nbtmcs-remove-image-button button <?php echo ($value ? '' : 'hidden') ?>"><?php esc_html_e( 'Remove image', 'woopanel' ); ?></button>
					            </div>
					        </div>
			            </td>
			        </tr>
			        <?php
					break;
				case 'select':
					if($tr){?>
					<tr valign="top">
			            <th scope="row" class="titledesc">
			                <label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['name'] ); ?></label>
			            </th>
			            <td class="forminp forminp-<?php echo sanitize_title( $field['type'] ) ?>"><?php }?>
							<select <?php if(isset($field['class'])){ echo ' class="'.esc_attr($field['class']).'"';}?> name="<?php echo esc_attr($field['id']);?>">
								<?php foreach ($field['options'] as $k_select => $val_select) {
									?>
									<option value="<?php echo esc_attr($k_select);?>"<?php selected( $value, $k_select ); ?>><?php echo esc_attr($val_select);?></option>
									<?php
								}?>				
							</select>
			            <?php if($tr){?></td>
			        </tr>
			        <?php
			    	}
					break;
				case $field['type']:
					echo apply_filters('nbt_admin_field_'.esc_attr($field['type']), $field['type'], $field, $value );
					break;

				default:
					# code...

					break;
			}

		}
	}

    /**
     * Enqueue styles.
     */
	public static function embed_script(){
		// Only working if plugin active alone
        if( ! defined('PREFIX_NBT_SOL') && ! class_exists('NBWooCommerceDashboard') ){
            wp_enqueue_style( 'spectrum', str_replace('inc', '', plugin_dir_url(__FILE__)) . 'assets/css/spectrum.css'  );
            wp_enqueue_script( 'spectrum', str_replace('inc', '', plugin_dir_url(__FILE__)) . 'assets/js/spectrum.js', null, null, true );
        }
	}
}