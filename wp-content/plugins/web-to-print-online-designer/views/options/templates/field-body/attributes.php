<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="field_body_attributes">'; ?>
    <div class="nbd-field-info" ng-show="check_depend(field.general, field.general.attributes)">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Attributes', 'web-to-print-online-designer'); ?></b> <nbd-tip data-tip="<?php _e('Attributes let you define extra product data, such as size or color.', 'web-to-print-online-designer'); ?>" ></nbd-tip></label></div>
        </div>  
        <div class="nbd-field-info-2">
            <div>
                <div ng-repeat="(opIndex, op) in field.general.attributes.options" class="nbd-attribute-wrap">
                    <div ng-show="op.isExpand" class="nbd-attribute-img-wrap">
                        <div><?php _e('Swatch type', 'web-to-print-online-designer'); ?></div>
                        <div>
                            <select ng-model="op.preview_type" style="width: 110px;" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][preview_type]">
                                <option value="i"><?php _e('Image', 'web-to-print-online-designer'); ?></option>
                                <option value="c"><?php _e('Color', 'web-to-print-online-designer'); ?></option>
                            </select>
                        </div>
                        <div class="nbd-attribute-img-inner" ng-show="op.preview_type == 'i'">
                            <span class="dashicons dashicons-no remove-attribute-img" ng-click="remove_attribute_image(fieldIndex, $index, 'image', 'image_url')"></span>
                            <input ng-hide="true" ng-model="op.image" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][image]"/>
                            <img title="<?php _e('Click to change image', 'web-to-print-online-designer'); ?>" ng-click="set_attribute_image(fieldIndex, $index, 'image', 'image_url')" ng-src="{{op.image != 0 ? op.image_url : '<?php echo NBDESIGNER_ASSETS_URL . 'images/placeholder.png' ?>'}}" />
                        </div>
                        <div class="nbd-attribute-color-inner" ng-show="op.preview_type == 'c'">
                            <input type="text" name="options[fields][{{fieldIndex}}][general][attributes][options][{{$index}}][color]" ng-model="op.color" class="nbd-color-picker" nbd-color-picker="op.color"/>
                            <span class="add-color2" ng-click="add_remove_second_color(fieldIndex, $index)"><span ng-show="!op.color2">+</span><span ng-show="op.color2">-</span></span>
                            <input ng-if="op.color2" type="text" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][color2]" ng-model="op.color2" class="nbd-color-picker" nbd-color-picker="op.color2"/>
                        </div>
                        <div ng-if="field.appearance.change_image_product.value == 'y'">
                            <div><?php _e('Product image', 'web-to-print-online-designer'); ?></div>
                            <div class="nbd-attribute-img-inner">
                                <span class="dashicons dashicons-no remove-attribute-img" ng-click="remove_attribute_image(fieldIndex, $index, 'product_image', 'product_image_url')"></span>
                                <input ng-hide="true" ng-model="op.product_image" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][product_image]"/>
                                <img title="<?php _e('Click to change image', 'web-to-print-online-designer'); ?>" ng-click="set_attribute_image(fieldIndex, $index, 'product_image', 'product_image_url')" ng-src="{{op.product_image_url ? op.product_image_url : '<?php echo NBDESIGNER_ASSETS_URL . 'images/placeholder.png' ?>'}}" />
                            </div>
                        </div>
                    </div>
                    <div ng-show="op.isExpand" class="nbd-attribute-content-wrap">
                        <div><?php _e('Title', 'web-to-print-online-designer'); ?></div>
                        <div class="nbd-attribute-name">
                            <input required type="text" value="{{op.name}}" ng-model="op.name" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][name]"/>
                            <label><input type="checkbox" name="options[fields][{{fieldIndex}}][general][attributes][options][{{$index}}][selected]" ng-checked="op.selected" ng-click="seleted_attribute(fieldIndex, 'attributes', $index)"/> <?php _e('Default', 'web-to-print-online-designer'); ?></label>
                        </div>
                        <div class="nbd-margin-10"></div>
                        <div><?php _e('Description', 'web-to-print-online-designer'); ?></div>
                        <div class="nbd-attribute-name">
                            <textarea placeholder="<?php _e('Description', 'web-to-print-online-designer'); ?>" value="{{op.des}}" ng-model="op.des" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][des]"></textarea>
                        </div> 
                        <div class="nbd-margin-10"></div>
                        <div><?php _e('Price', 'web-to-print-online-designer'); ?></div>
                        <div ng-show="field.general.depend_quantity.value != 'y'">
                            <div><?php _e('Additional Price', 'web-to-print-online-designer'); ?></div>
                            <div>
                                <input autocomplete="off" ng-click="initFormulaPrice(op.price[0], 0, fieldIndex, opIndex)" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][price][0]" class="nbd-short-ip" type="text" ng-model="op.price[0]"/>
                            </div>
                        </div>
                        <div class="nbd-table-wrap" ng-show="field.general.depend_quantity.value == 'y'" >
                            <table class="nbd-table">
                                <tr>
                                    <th><?php _e('Quantity break', 'web-to-print-online-designer'); ?></th>
                                    <th ng-repeat="break in options.quantity_breaks">{{break.val}}</th>
                                </tr>
                                <tr>
                                    <td><?php _e('Additional Price', 'web-to-print-online-designer'); ?></td>
                                    <td ng-repeat="break in options.quantity_breaks">
                                        <input autocomplete="off" ng-click="initFormulaPrice(op.price[$index], $index, fieldIndex, opIndex)" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][price][{{$index}}]" class="nbd-short-ip" type="text" ng-model="op.price[$index]"/>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="nbd-margin-10"></div>
                        <div>
                            <div><?php _e('Implicit Value', 'web-to-print-online-designer'); ?></div>
                            <div>
                                <input class="nbd-short-ip" type="text" ng-model="op.implicit_value" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][implicit_value]"/>
                            </div>
                        </div>
                        <ng-include src="'field_body_attributes_conditional'"></ng-include>
                        <div class="nbd-margin-10"></div><hr />
                        <div class="nbd-enable-subattribute" ng-hide="field.nbd_type != '' && field.nbd_type != null">
                            <label><input ng-click="toggle_enable_subattr(fieldIndex, $index)" type="checkbox" name="options[fields][{{fieldIndex}}][general][attributes][options][{{$index}}][enable_subattr]" ng-true-value="'on'" ng-false-value="'off'" ng-model="op.enable_subattr" ng-checked="op.enable_subattr" /> <?php _e('Enable sub attributes', 'web-to-print-online-designer'); ?></label>
                        </div>
                        <div class="nbd-margin-10"></div>
                        <div class="nbd-subattributes-wrapper" ng-if="op.enable_subattr === true || op.enable_subattr == 'on'">
                            <div class="nbd-field-info">
                                <div class="nbd-field-info-1">
                                    <div><label><b><?php _e('Sub attributes type', 'web-to-print-online-designer'); ?></b></label></div>
                                </div>
                                <div class="nbd-field-info-2">
                                    <div>
                                        <select style="width: 150px;" name="options[fields][{{fieldIndex}}][general][attributes][options][{{$index}}][sattr_display_type]" ng-model="op.sattr_display_type" >
                                            <option value="d"><?php _e('Dropdown', 'web-to-print-online-designer'); ?></option>
                                            <option value="r"><?php _e('Radio button', 'web-to-print-online-designer'); ?></option>
                                            <option value="s"><?php _e('Swatch', 'web-to-print-online-designer'); ?></option>
                                            <option value="l"><?php _e('Label', 'web-to-print-online-designer'); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="nbd-margin-10"></div>
                            <div ng-repeat="(sopIndex, sop) in op.sub_attributes" class="nbd-subattributes-wrap">
                                <div ng-show="sop.isExpand" class="nbd-attribute-img-wrap">
                                    <div><?php _e('Swatch type', 'web-to-print-online-designer'); ?></div>
                                    <div>
                                        <select ng-model="sop.preview_type" style="width: 110px;" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][sub_attributes][{{sopIndex}}][preview_type]">
                                            <option value="i"><?php _e('Image', 'web-to-print-online-designer'); ?></option>
                                            <option value="c"><?php _e('Color', 'web-to-print-online-designer'); ?></option>
                                        </select>
                                    </div>
                                    <div class="nbd-attribute-img-inner" ng-show="sop.preview_type == 'i'">
                                        <span class="dashicons dashicons-no remove-attribute-img" ng-click="remove_sub_attribute_image(fieldIndex, opIndex, sopIndex)"></span>
                                        <input ng-hide="true" ng-model="sop.image" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][sub_attributes][{{sopIndex}}][image]"/>
                                        <img title="<?php _e('Click to change image', 'web-to-print-online-designer'); ?>" ng-click="set_sub_attribute_image(fieldIndex, opIndex, sopIndex)" ng-src="{{sop.image != 0 ? sop.image_url : '<?php echo NBDESIGNER_ASSETS_URL . 'images/placeholder.png' ?>'}}" />
                                    </div>
                                    <div class="nbd-attribute-color-inner" ng-show="sop.preview_type == 'c'">
                                        <input type="text" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][sub_attributes][{{sopIndex}}][color]" ng-model="sop.color" class="nbd-color-picker" nbd-color-picker="sop.color"/>
                                    </div>
                                </div>
                                <div ng-show="sop.isExpand" class="nbd-attribute-content-wrap">
                                    <div><?php _e('Title', 'web-to-print-online-designer'); ?></div>
                                    <div class="nbd-attribute-name">
                                        <input required type="text" value="{{sop.name}}" ng-model="sop.name" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][sub_attributes][{{sopIndex}}][name]"/>
                                        <label><input type="checkbox" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][sub_attributes][{{sopIndex}}][selected]" ng-checked="sop.selected" ng-click="seleted_sub_attribute(fieldIndex, 'attributes', opIndex, sopIndex)"/> <?php _e('Default', 'web-to-print-online-designer'); ?></label>
                                    </div>
                                    <div class="nbd-margin-10"></div>
                                    <div><?php _e('Description', 'web-to-print-online-designer'); ?></div>
                                    <div class="nbd-attribute-name">
                                        <textarea placeholder="<?php _e('Description', 'web-to-print-online-designer'); ?>" value="{{sop.des}}" ng-model="sop.des" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][sub_attributes][{{sopIndex}}][des]"></textarea>
                                    </div>
                                    <div><?php _e('Price', 'web-to-print-online-designer'); ?></div>
                                    <div ng-show="field.general.depend_quantity.value != 'y'">
                                        <div><?php _e('Additional Price', 'web-to-print-online-designer'); ?></div>
                                        <div>
                                            <input autocomplete="off" ng-click="initFormulaPrice(sop.price[0], 0, fieldIndex, opIndex, sopIndex)" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][sub_attributes][{{sopIndex}}][price][0]" class="nbd-short-ip" type="text" ng-model="sop.price[0]"/>
                                        </div>
                                    </div>
                                    <div class="nbd-table-wrap" ng-show="field.general.depend_quantity.value == 'y'" >
                                        <table class="nbd-table">
                                            <tr>
                                                <th><?php _e('Quantity break', 'web-to-print-online-designer'); ?></th>
                                                <th ng-repeat="break in options.quantity_breaks">{{break.val}}</th>
                                            </tr>
                                            <tr>
                                                <td><?php _e('Additional Price', 'web-to-print-online-designer'); ?></td>
                                                <td ng-repeat="break in options.quantity_breaks">
                                                    <input autocomplete="off" ng-click="initFormulaPrice(sop.price[$index], $index, fieldIndex, opIndex, sopIndex)" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][sub_attributes][{{sopIndex}}][price][{{$index}}]" class="nbd-short-ip" type="text" ng-model="sop.price[$index]"/>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="nbd-margin-10"></div>
                                    <div>
                                        <div><?php _e('Implicit Value', 'web-to-print-online-designer'); ?></div>
                                        <div>
                                            <input class="nbd-short-ip" type="text" ng-model="sop.implicit_value" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][sub_attributes][{{sopIndex}}][implicit_value]"/>
                                        </div>
                                    </div>
                                    <ng-include src="'field_body_sub_attributes_conditional'"></ng-include>
                                </div>
                                <div ng-show="!sop.isExpand" class="nbd-attribute-name-preview">{{sop.name}}</div>
                                <div class="nbd-attribute-action">
                                    <span class="nbo-sort-group">
                                        <span ng-click="sort_sub_attribute(fieldIndex, opIndex, sopIndex, 'up')" class="dashicons dashicons-arrow-up nbo-sort-up nbo-sort" title="<?php _e('Up', 'web-to-print-online-designer') ?>"></span>
                                        <span ng-click="sort_sub_attribute(fieldIndex, opIndex, sopIndex, 'down')" class="dashicons dashicons-arrow-down nbo-sort-down nbo-sort" title="<?php _e('Down', 'web-to-print-online-designer') ?>"></span>
                                    </span>
                                    <a class="button nbd-mini-btn"  ng-click="remove_sub_attribute(fieldIndex, opIndex, sopIndex)" title="<?php _e('Delete', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-no-alt"></span></a>
                                    <a class="button nbd-mini-btn"  ng-click="toggle_expand_sub_attribute(fieldIndex, opIndex, sopIndex)" title="<?php _e('Expend', 'web-to-print-online-designer'); ?>">
                                        <span ng-show="sop.isExpand" class="dashicons dashicons-arrow-up"></span>
                                        <span ng-show="!sop.isExpand" class="dashicons dashicons-arrow-down"></span>
                                    </a>
                                </div>
                            </div>
                            <div><a class="button" ng-click="add_sub_attribute(fieldIndex, opIndex)"><span class="dashicons dashicons-plus"></span> <?php _e('Add sub attribute', 'web-to-print-online-designer'); ?></a></div>
                            <div class="nbd-margin-10"></div>
                        </div>
                    </div> 
                    <div ng-show="!op.isExpand" class="nbd-attribute-name-preview">{{op.name}}</div>
                    <div class="nbd-attribute-action">
                        <span class="nbo-sort-group">
                            <span ng-click="sort_attribute(fieldIndex, $index, 'up')" class="dashicons dashicons-arrow-up nbo-sort-up nbo-sort" title="<?php _e('Up', 'web-to-print-online-designer') ?>"></span>
                            <span ng-click="sort_attribute(fieldIndex, $index, 'down')" class="dashicons dashicons-arrow-down nbo-sort-down nbo-sort" title="<?php _e('Down', 'web-to-print-online-designer') ?>"></span>
                        </span>
                        <a class="button nbd-mini-btn"  ng-click="remove_attribute(fieldIndex, 'attributes', $index)" title="<?php _e('Delete', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-no-alt"></span></a>
                        <a class="button nbd-mini-btn"  ng-click="toggle_expand_attribute(fieldIndex, opIndex)" title="<?php _e('Expend', 'web-to-print-online-designer'); ?>">
                            <span ng-show="op.isExpand" class="dashicons dashicons-arrow-up"></span>
                            <span ng-show="!op.isExpand" class="dashicons dashicons-arrow-down"></span>
                        </a>
                    </div>
                    <div class="clear"></div>
                </div>
                <div><a class="button" ng-click="add_attribute(fieldIndex, 'attributes')"><span class="dashicons dashicons-plus"></span> <?php _e('Add attribute', 'web-to-print-online-designer'); ?></a></div>                        
            </div>
        </div>
    </div>
<?php echo '</script>';