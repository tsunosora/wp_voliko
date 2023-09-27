<tr class="pm-row">
	<td class="pm-row-zero order pm-handle">
		<span><?php echo count($attributes) + 1;?></span>
	</td>

	<td class="pm-field">
		<div class="pm-input">
			<div class="pm-input-wrap">
				<select class="form-control m-input pm-attributes-field" name="pm_attr[]">
					<option value="<?php echo esc_attr($new_attribute['slug']);?>" selected><?php echo esc_attr($new_attribute['label']);?></option>
				</select>
			</div>
		</div>
	</td>
	<td class="pm-field">
		<div class="pm-input">
			<div class="pm-input-wrap">
				<select class="form-control m-input pm-direction-field" name="pm_direction[]">
					<?php foreach( WooPanel_Price_Matrix::pm_direction() as $k => $v ) {?>
					<option value="<?php echo esc_attr($k);?>"<?php if( isset($new_attribute['direction']) && $new_attribute['direction'] == $k ) { echo ' selected';}?>><?php echo esc_attr($v);?></option>
					<?php }?>
				</select>
			</div>
		</div>
	</td>
	<td class="pm-row-zero">
		<a class="pm-icon -plus small" href="#" data-event="add-row" title="<?php esc_html_e('Add row', 'woopanel' );?>"></a>
		<a class="pm-icon -minus small" href="#" data-event="remove-row" title="<?php esc_html_e('Remove row', 'woopanel' );?>"></a>
	</td>
</tr>