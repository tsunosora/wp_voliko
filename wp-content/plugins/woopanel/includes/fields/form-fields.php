<?php

if ( ! function_exists( 'woopanel_form_field' ) ) {
	/**
	 * Render form fields for WooPanel
	 *
	 * @param string $field_id
	 * @param array $args Arguments field
	 * @param string $value Default is null
	 * @return array
	 */
	function woopanel_form_field( $key, $args, $value = null ) {
	
		$defaults = array(
			'type'              => 'text',
			'label'             => '',
			'description'       => '',
			'placeholder'       => '',
			'maxlength'         => false,
			'required'          => false,
			'autocomplete'      => false,
			'id'                => $key,
			'class'             => array(),
			'label_class'       => array(),
			'input_class'       => array(),
			'wrapper_class'		=> '',
			'return'            => false,
			'options'           => array(),
			'custom_attributes' => array(),
			'validate'          => array(),
			'default'           => '',
			'autofocus'         => '',
			'priority'          => '',
			'settings'          => '',
			'form_inline'       => false,
			'disable'           => false,
			'wrapper_after'		=> '',
			'size'				=> false,
			'value'				=> '',
			'conditional_logic'	=> false,
			'units'				=> false,
			'min'				=> 0,
			'max'				=> 100,
			'kses'				=> array(),
			'desc_tip'			=> null
		);

		$args = wp_parse_args( $args, $defaults );
		
		if( ! empty($args['value']) ) {
			$value = $args['value'];
		}
		
	
		$args = apply_filters( 'woopanel_form_field_args', $args, $key, $value );

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required        = '&nbsp;<abbr class="required" title="' . esc_attr__( 'required', 'woopanel' ) . '">*</abbr>';
		} else {
			$required = '';
		}

        $disable = $args['disable'] ? 'disabled' : '';

		if ( is_string( $args['label_class'] ) ) {
			$args['label_class'] = array( $args['label_class'] );
		}


		// Custom attribute handling.
		$custom_attributes         = array();
		$args['custom_attributes'] = array_filter( (array) $args['custom_attributes'], 'strlen' );

		if ( $args['maxlength'] ) {
			$args['custom_attributes']['maxlength'] = absint( $args['maxlength'] );
		}

		if ( ! empty( $args['autocomplete'] ) ) {
			$args['custom_attributes']['autocomplete'] = $args['autocomplete'];
		}

		if ( true === $args['autofocus'] ) {
			$args['custom_attributes']['autofocus'] = 'autofocus';
		}

		if ( $args['description'] ) {
			$args['custom_attributes']['aria-describedby'] = $args['id'] . '-description';
		}

		if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
			foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}

		if ( ! empty( $args['validate'] ) ) {
			foreach ( $args['validate'] as $validate ) {
				$args['class'][] = sprintf( 'validate-%s', $validate);
			}
		}
		if ( ! empty( $args['type'] ) ) {
			$args['class'][] = 'type-' . esc_attr($args['type']) . ' ' . esc_attr($args['wrapper_class']);
		}

		$field           = '';
		$label_id        = $args['id'];
		$sort            = $args['priority'] ? $args['priority'] : '';
		$field_container = '<div class="form-group m-form__group %1$s" id="%2$s" data-priority="' . esc_attr( $sort ) . '"%3$s>%4$s</div>';
		
		switch ( $args['type'] ) {
			case 'textarea':
			$field .= '<textarea '. esc_attr($disable) .' name="' . esc_attr( $key ) . '" class="form-control m-input ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" ' . ( empty( $args['custom_attributes']['rows'] ) ? ' rows="5"' : '' ) . ( empty( $args['custom_attributes']['cols'] ) ? ' cols="5"' : '' ) . implode( ' ', $custom_attributes ) . '>' . stripslashes($value) . '</textarea>';

			break;
			case 'text':
			case 'password':
			case 'datetime':
			case 'month':
			case 'week':
			case 'time':
			case 'file':
			case 'email':
			case 'url':
			case 'tel':
				$field .= '<input '. esc_attr($disable) .' type="' . esc_attr( $args['type'] ) . '" class="form-control m-input ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '"  value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';
			break;			
			case 'datepicker':
				$input = '<input '. esc_attr($disable) .' type="text" class="form-control m-input m-datepicker date-picker ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '"  value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';
				$field .= $input . '<div class="input-group-append"><span class="input-group-text"><i class="la la-calendar-check-o"></i></span></div>';
				break;
			case 'select':
			$field   = '';
			$options = '';


			if ( isset($args['options']) ) {
				foreach ( $args['options'] as $option_key => $option_text ) {
					if ( '' === $option_key ) {
							// If we have a blank option, select2 needs a placeholder.
						if ( empty( $args['placeholder'] ) ) {
							$args['placeholder'] = $option_text ? $option_text : esc_html__( 'Choose an option', 'woopanel' );
						}
						$custom_attributes[] = 'data-allow_clear="true"';
					}
					if( is_array($value) ) {
						$options .= '<option value="' . esc_attr( $option_key ) . '" ';
						if( isset($value[$option_key]) ) {
							$options .= 'selected';
						}
						$options .= '>' . esc_attr( $option_text ) . '</option>';

					}else {
						$options .= '<option value="' . esc_attr( $option_key ) . '" ' . selected( $value, $option_key, false ) . '>' . esc_attr( $option_text ) . '</option>';
					}


				}
				


					if(empty($options)) {
						$options .= '<option></option>';
					}

				$field .= '<select '. esc_attr($disable) .' name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="form-control m-input ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ) . '">
				' . wp_kses( $options, array(
					    'option' => array(
					        'value' => array(),
					        'selected' => array()
					    ),
					) ) . '
				</select>';
			}

			break;
			case 'checkbox_list':
				$label_id = current( array_keys( $args['options'] ) );
				if ( ! empty( $args['options'] ) ) {
					foreach ( $args['options'] as $option_key => $option_text ) {
						$checked = '';
						if( ! empty($value) && in_array($option_key, $value) ) {
							$checked = ' checked';
						}

						$input = '<input type="checkbox" class="input-checkbox '. esc_attr( implode( ' ', $args['input_class'] ) ) . '" value="'. esc_attr( $option_key ) . '" name="' . esc_attr( $key ) .'[]" '. implode( ' ', $custom_attributes ) . ' id="' . esc_attr( $args['id'] ) .'_'. esc_attr( $option_key ) . '"' . esc_attr($checked) .' />';
						$field .= '<p><label for="' . esc_attr( $args['id'] ) .'_'. esc_attr( $option_key ) .'" class="m-checkbox ' . implode( ' ', $args['label_class'] ) .'" style="margin-bottom: 0;">'. wp_kses( $input, array(
					    'input' => array(
					        'type' => array(),
					        'class' => array(),
					        'value' => array(),
					        'name' => array(),
					        'id' => array(),
					        'checked' => array()
					    ),
					) ) . esc_attr( $option_text ) . '<span></span></label></p>';
					}
				}
				break;
			case 'checkbox':
				$input = '<input '. esc_attr($disable) .' type="checkbox" class="input-checkbox '. esc_attr( implode( ' ', $args['input_class'] ) ) . '" value="'. esc_attr( $args['default'] ) .'" name="' . esc_attr( $key ) .'" '. implode( ' ', $custom_attributes ) . ' id="' . esc_attr( $args['id'] ) . '"' . checked( $value, $args['default'], false ) .' />';
				$field .= '<label class="m-checkbox">'.  $input .' '. wp_kses( $args['description'], $args['kses'] ). '<span></span></label>';
				break;
			case 'callback':
				if( isset($args['function']) && is_callable( $args['function'] ) ) {
					$field .= call_user_func( $args['function'] ); 
				}
				break;
			case 'radio':
			$label_id = current( array_keys( $args['options'] ) );

			if ( ! empty( $args['options'] ) ) {
				foreach ( $args['options'] as $option_key => $option_text ) {
					$input = '<input type="radio" class="input-radio '. esc_attr( implode( ' ', $args['input_class'] ) ) . '" value="'. esc_attr( $option_key ) . '" name="' . esc_attr( $key ) .'" '. implode( ' ', $custom_attributes ) . ' id="' . esc_attr( $args['id'] ) .'_'. esc_attr( $option_key ) . '"' . checked( $value, $option_key, false ) .' />';
					$field .= '<label for="' . esc_attr( $args['id'] ) .'_'. esc_attr( $option_key ) .'" class="radio ' . implode( ' ', $args['label_class'] ) .'">'. esc_attr( $input ). esc_attr( $option_text ) . '</label>';
				}
			}

			break;
			case 'switch':
				$field .= '<span class="m-switch '. esc_attr( implode( ' ', $args['input_class'] ) ) . '">';
				$field .= '<label><input type="checkbox" name="' . esc_attr( $key ) .'" value="true" '. ($value ? 'checked="checked"' : '') .' /><span></span></label>';
				$field .= '</span>';
			break;
			
			
			case 'number':
				$attr = array();
				if( ! empty($args['custom_attributes']) ) {
					foreach( $args['custom_attributes'] as $key_attr => $val_attr) {
						$attr[] = 'min="'. esc_attr($val_attr) .'"';
					}
				}
				$attr = implode( ' ', $attr);
				
				$field .= '<input value="'. esc_attr($value) .'" name="' . esc_attr( $key ) .'" class="form-control m-input '. esc_attr( implode( ' ', $args['input_class'] ) ) . '" id="' . esc_attr( $args['id'] ) .'" type="number"'. esc_attr($attr) .'>';
				break;
				
			case 'icon':
				$field .= '<div class="woopanel-icon-group"><span class="woopanel-input-group-addon"><i class="fa fa-'. esc_attr($args['icon']) .'"></i></span><input value="'. esc_attr($value) .'" name="' . esc_attr( $key ) .'" class="form-control m-input '. esc_attr( implode( ' ', $args['input_class'] ) ) . '" id="' . esc_attr( $args['id'] ) .'" placeholder="'. esc_attr($args['placeholder']).'" type="text"></div>';
				break;

			case 'map':
				$field .= '<input type="' . esc_attr( $args['type'] ) . '" class="form-control m-input ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '"  value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';

				if ( $args['description'] ) {
					$field .= '<span class="m-form__help" id="' . esc_attr( $args['id'] ) . '-description" aria-hidden="true">' . wp_kses_post( $args['description'] ) . '</span>';
				}
				$field .= '<div id="wplMapPanel"></div>
				<div id="wplMapShow" class="map-geo-location" ></div>';
				break;
				
			case 'hidden':
			$field .= '<input type="' . esc_attr( $args['type'] ) . '" class="form-control m-input ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '"  value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';
			case 'radio-image':
				$value = empty($value) ? $args['default'] : $value;
				$field .= '<div class="woopanel-radio-image-selector">';
					foreach ($args['options'] as $key => $option) {
						$checked = ($value == $option['id']) ? 'checked' : '';


						$field .= '<div class="woopanel-radio-image-item">';
						$field .= sprintf('<input id="%s" type="radio" name="%s" value="%s" %s/>', $option['id'], $args['id'], $option['id'], $checked);

						$field .= sprintf('<label class="woopanel-radio-image-label" for="%s" style="%s"></label>', $option['id'], 'background-image: url('. esc_url($option['image']) .'); width: ' . absint($args['width']) . 'px; height: ' . absint($args['height']) . 'px');

						$field .= '</div>';
					;
					}
   				$field .= '</div>';
				break;
			case 'slider':
				$units = !empty($args['units']) ? $args['units'] : '';
				$value = empty($value) ? $args['default'] : $value;
				$field .= '<div class="woopanel-slider-container"><div class="woopanel-ui-slider" data-value="'.esc_attr($value).'" data-units="'.esc_attr($units).'" data-min="'.absint($args['min']).'" data-max="'.absint($args['max']).'"></div></div><input type="hidden" name="' . esc_attr($args['id']) . '" value="'.esc_attr($value).'" /><span class="range-slider__value">'.esc_attr($value).esc_attr( $units).'</span>';
				break;
			case 'icon_list':
				$total = count($args['options']);
				$field .= '<div class="wpl-icon_lists">';
					$index = 0;
					foreach( $args['options'] as $k => $icon_html ) {
						if( $index % 4 == 0) {
							$field .= '<div class="wpl-icon_item_row">';
						}

						$checked = false;
						if( $k == $args['value']) {
							$checked = ' checked';
						}
						$field .= '<input type="radio" name="'. esc_attr( $key ) .'" value="'. esc_attr($k) .'"'. esc_attr($checked) .' /><div class="wpl-icon_item loading-'. esc_attr($k) .'">'. wp_kses( $icon_html, array(
                                'div' => array(
                                    'class' => array()
                                ),
                            ) ) .'</div>';

						if( $index % 4 == 3 || $index == ($total-1) ) {
							$field .= '</div>';
						}

						$index++;
					}
				$field .= '</div>';
				break;
			case 'tab':
				echo '<div class="woopanel-tab">' . esc_attr($args['label']) .'</div>';
				return;
				break;
		}

		if ( ! empty( $args['type'] ) && $args['type']=='image' ) {
			if($args['form_inline']) {
				$args['label_class'][] = 'col-3 col-form-label';
				echo '<div class="form-group m-form__group type-image row" id="'. esc_attr( $label_id ) .'_field" data-priority="">';
			}else {
				echo '<div class="form-group m-form__group type-image" id="'. esc_attr( $label_id ) .'_field" data-priority="">';
			}

			echo '<label for="'. esc_attr( $label_id ) .'" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) . '">' . esc_attr($args['label']) . esc_attr( $required ). '</label>';
			if($args['form_inline']) {
				echo '<div class="col-9">';
			}else {
				echo '<div class="image-wrapper">';
			}

			woopanel_attachment_image( $value, true, false, esc_attr($key), $args['size'] );
			echo '</div></div>';

			//
		} elseif ( ! empty( $args['type'] ) && $args['type']=='wpeditor' ) {	
			echo '<div class="form-group m-form__group" id="'. esc_attr( $label_id ) .'_field">';		
			if ( $args['label'] && 'checkbox' !== $args['type'] ) {
				echo '<label for="' . esc_attr( $label_id ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) . '">' . esc_attr($args['label']) . esc_attr($required) . '</label>';
			}
			echo '<div class="field-wrapper">';

			$defaults = array(
				'wpautop' => true,
				'media_buttons' => true,
				'textarea_name' => esc_attr( $key ),
				'textarea_rows' => get_option('default_post_edit_rows', 10),
				'tabindex' => '',
				'editor_css' => '',
				'editor_class'  => esc_attr( implode( ' ', $args['input_class'] ) ),
				'editor_height' => '',
				'teeny' => false,
				'dfw' => false,
				'tinymce' => true,
				'quicktags' => true,
				'drag_drop_upload' => false
			);
			$settings  = wp_parse_args($args['settings'], $defaults);

			wp_editor( $value, esc_attr( $args['id'] ), $settings);

			if ( $args['description'] ) {
				echo '<span class="description" id="' . esc_attr( $args['id'] ) . '-description" aria-hidden="true">' . wp_kses_post( $args['description'] ) . '</span>';
			}
			echo '</div>';
			echo '</div>';
		} elseif ( ! empty( $args['type'] ) && $args['type']=='heading' ) {
		    echo '<div class="m-form__heading"><h3 class="m-form__heading-title">'. esc_attr($args['label']) .'</h3></div>';
        } else {
			if ( ! empty( $field ) ) {
				$field_html = '';

				if ( $args['label'] ) {
					if($args['form_inline']) $args['label_class'][] = 'col-3 col-form-label';

					$field_html .= '<label for="' . esc_attr( $label_id ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) . '">' . wp_kses($args['label'], $args['kses']) . esc_attr( $required ) . '</label>';
				}

				if($args['form_inline']) {
					if( $args['type'] == 'datepicker' ) {
						$field_html .= '<div class="input-group date col-9">';
					}else {
						if ( $args['label'] ) {
							$field_html .= '<div class="col-9">';
						}else {
							$field_html .= '<div class="col-12">';
						}
					}
				}

				$field_html .= $field;
	
				if ( $args['description'] && $args['type'] !== 'checkbox' && $args['type'] != 'map') {
					$field_html .= '<span class="m-form__help" id="' . esc_attr( $args['id'] ) . '-description" aria-hidden="true">' . wp_kses_post( $args['description'] ) . '</span>';
				}

				if($args['form_inline']) $field_html .= $args['wrapper_after'] . '</div>';

				if($args['form_inline']) $args['class'][] = 'row';
				$container_class = esc_attr( implode( ' ', $args['class'] ) );
				$container_id    = esc_attr( $args['id'] ) . '_field';



				$conditional_logic = empty($args['conditional_logic']) ? '' : 'data-conditional_logic="'. htmlspecialchars(json_encode($args['conditional_logic'])) .'"';

				$field           = sprintf( $field_container, $container_class, $container_id, $conditional_logic, $field_html );
			}

	        /**
	         * Filter by type.
	         *
	         * @since 1.0.0
	         * @hook woopanel_form_field_{$form_type}
	         * @param {array} $field Field
	         * @param {string} $key Key
	         * @param {array} $args Args
	         * @param {string} $valued Value of field
	         * @return void
	         */
			$field = apply_filters( sprintf( 'woopanel_form_field_%s', $args['type'] ), $field, $key, $args, $value );

	        /**
	         * General filter on form fields.
	         *
	         * @since 1.0.0
	         * @hook woopanel_form_field
	         * @param {array} $field Field
	         * @param {string} $key Key
	         * @param {array} $args Args
	         * @param {string} $valued Value of field
	         * @return void
	         */
			$field = apply_filters( 'woopanel_form_field', $field, $key, $args, $value );

			if ( $args['return'] ) {
				return $field;
			} else {
				print($field); // WPCS: XSS ok.
			}
		}
	}
}