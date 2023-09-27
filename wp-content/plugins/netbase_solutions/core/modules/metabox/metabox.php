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
class NBT_Solutions_Metabox
{
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
    public static function initialize()
    {
        // Do nothing if pluggable functions already initialized.
        if (self::$initialized) {
            return;
        }

        add_action('admin_enqueue_scripts', array(__CLASS__, 'embed_script'));

        // State that initialization completed.
        self::$initialized = true;
    }

    public static function set_field()
    {
        return apply_filters('nbt_metabox_fields', array(
            'text', 'color'
        ));
    }

    public static function show_field($field, $value = false, $tr = true)
    {
        if (is_array($field)) {
            switch ($field['type']) {
                case 'color':
                    ?>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php echo esc_attr($field['id']); ?>"><?php echo esc_html($field['name']); ?></label>
                        </th>
                        <td class="forminp forminp-<?php echo sanitize_title($field['type']) ?>">
                            <input type="text" id="term-<?php echo esc_attr($field['name']) ?>"
                                   name="<?php echo esc_attr($field['id']) ?>" class="nbt-colorpicker"
                                   value="<?php echo esc_attr($value) ?>"/>
                        </td>
                    </tr>
                    <?php
                    break;

                case 'text':
                    ?>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php echo esc_attr($field['id']); ?>"><?php echo esc_html($field['name']); ?></label>
                        </th>
                        <td class="forminp forminp-<?php echo sanitize_title($field['type']) ?>">
                            <input type="text" name="<?php echo esc_attr($field['id']) ?>"
                                   value="<?php echo esc_attr($value) ?>" style="width: 100%;"/>
                            <?php if (isset($field['desc_tip'])) {
                                echo $field['desc_tip'];
                            } ?>
                        </td>
                    </tr>
                    <?php
                    break;
                case 'label':
                    ?>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label class="field-type-label" style="text-transform: uppercase;font-weight: bold;width: 100%;border-bottom: 1px solid;" for="<?php echo esc_attr($field['id']); ?>"><?php echo esc_html($field['name']); ?></label>
                        </th>
                        
                    </tr>
                    <?php
                    break;
                case 'textarea':
                    ?>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php echo esc_attr($field['id']); ?>"><?php echo esc_html($field['name']); ?></label>
                        </th>
                        <td class="forminp forminp-<?php echo sanitize_title($field['type']) ?>">
                            <textarea style="width: 100%" rows="<?php if (isset($field['rows'])) {
                                echo $field['rows'];
                            } else {
                                echo '3';
                            } ?>"
                                      name="<?php echo esc_attr($field['id']) ?>"><?php echo str_replace('\\', '', esc_attr($value)) ?></textarea>
                            <?php if (isset($field['desc_tip'])) {
                                echo $field['desc_tip'];
                            } ?>
                        </td>
                    </tr>
                    <?php
                    break;
                case 'repeater':
                    $field_id = $field['id'];
                    $fields = $field['fields']; ?>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php echo esc_attr($field['id']); ?>"><?php echo esc_html($field['name']); ?></label>
                        </th>
                        <td class="forminp forminp-<?php echo sanitize_title($field['type']) ?>">
                            <?php include($field['temp']); ?>
                        </td>
                    </tr>
                    <?php
                    break;
                case 'border':
                    ?>
                    <tr valign="top">
                        <td colspan="2" class="forminp forminp-<?php echo sanitize_title($field['type']) ?>">
                            <hr/>
                        </td>
                    </tr>
                    <?php
                    break;
                case 'radio_image':
                    ?>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php echo esc_attr($field['id']); ?>"><?php echo esc_html($field['name']); ?></label>
                        </th>
                        <td class="forminp forminp-<?php echo sanitize_title($field['type']) ?>">
                            <?php if (isset($field['option'])) {?>
                            <div class="cc-selector">
                                <?php foreach ($field['option'] as $k_radio => $ri) { ?>
                                <input<?php if( $k_radio == $value ) { echo ' checked="checked"';}?> id="<?php echo $k_radio; ?>" type="radio" name="<?php echo $field['id']; ?>" value="<?php echo $k_radio; ?>"/>
                                <label class="drinkcard-cc visa" for="<?php echo $k_radio; ?>" style="background-image: url(<?php echo esc_url($ri['src']); ?>);"></label>
                                <?php }?>
                            </div>
                            <?php }?>
                        </td>
                    </tr>
                    <?php

                    break;
                case 'checkbox':
                    ?>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php echo esc_attr($field['id']); ?>"><?php echo esc_html($field['name']); ?></label>
                        </th>
                        <td class="forminp forminp-<?php echo sanitize_title($field['type']) ?>">
                            <div class="checkbox">
                                <label><input type="checkbox" name="<?php echo $field['id']; ?>"
                                              value="1"<?php if ($value == 1 || $value == true) {
                                        echo ' checked';
                                    } ?>> <?php echo isset($field['label']) ? $field['label'] : ''; ?></label>
                            </div>
                        </td>
                    </tr>

                    <?php
                    break;
                case 'number':
                    if ($tr) {
                        ?>
                        <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php echo esc_attr($field['id']); ?>"><?php echo esc_html($field['name']); ?></label>
                        </th>
                        <td class="forminp forminp-<?php echo sanitize_title($field['type']) ?>"><?php } ?>
                    <input type="number" name="<?php echo esc_attr($field['id']) ?>"
                           value="<?php echo esc_attr($value) ?>"<?php if (isset($field['min'])) {
                        echo ' min="' . $field['min'] . '"';
                    } ?><?php if (isset($field['max'])) {
                        echo ' max="' . $field['max'] . '"';
                    } ?> />

                    <?php if ($tr) { ?></td>
                    </tr>
                    <?php
                }
                    break;
                case 'image':

                    ?>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php echo esc_attr($field['id']); ?>"><?php echo esc_html($field['name']); ?></label>
                        </th>
                        <td class="forminp forminp-<?php echo sanitize_title($field['type']) ?>">
                            <?php
                            $image = $value ? wp_get_attachment_image_src($value) : '';
                            $image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';
                            ?>
                            <div class="nb-metabox-image-wrapper">
                                <div class="nb-metabox-term-image-thumbnail" style="float:left;margin-right:10px;">
                                    <img src="<?php echo esc_url($image) ?>" width="60px" height="60px"/>
                                </div>
                                <div class="right-button-term" style="line-height:60px;">
                                    <input type="hidden" class="nb-metabox-term-image" name="<?php echo $field['id']; ?>"
                                           value="<?php echo esc_attr($value) ?>"/>
                                    <button type="button" class="nb-metabox-image-upload button"><?php esc_html_e('Upload/Add image', 'wcvs'); ?></button>
                                    <button type="button" class="nb-metabox-remove-image button <?php echo $value ? '' : 'hidden' ?>"><?php esc_html_e('Remove image', 'wcvs'); ?></button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php
                    break;
                case 'select':
                    if ($tr) {
                        ?>
                        <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php echo esc_attr($field['id']); ?>"><?php echo esc_html($field['name']); ?></label>
                        </th>
                        <td class="forminp forminp-<?php echo sanitize_title($field['type']) ?>"><?php } ?>
                    <select <?php if (isset($field['class'])) {
                        echo ' class="' . $field['class'] . '"';
                    } ?> name="<?php echo $field['id']; ?>">
                        <?php foreach ($field['options'] as $k_select => $val_select) {
                            ?>
                            <option value="<?php echo $k_select; ?>"<?php selected($value, $k_select); ?>><?php echo $val_select; ?></option>
                            <?php
                        } ?>
                    </select>
                    <?php if ($tr) { ?></td>
                    </tr>
                    <?php
                }
                    break;
                case $field['type']:
                    echo apply_filters('nbt_admin_field_' . $field['type'], $field['type'], $field, $value);
                    break;

                default:
                    # code...

                    break;
            }

        }
    }

    public static function embed_script()
    {
        if (!defined('PREFIX_NBT_SOL')) {
            wp_enqueue_style('spectrum', NB_AJAXCART_URL . 'assets/css/spectrum.css');
            wp_enqueue_script('spectrum', NB_AJAXCART_URL . 'assets/js/spectrum.js', null, null, true);
        }
    }
}