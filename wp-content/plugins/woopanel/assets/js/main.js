(function($) {
'use strict';
	
	if( jQuery().select2 ) {
		jQuery(".select2-tags-ajax").select2({
			width: '100%',
			allowClear:  $( this ).data( 'allow_clear' ) ? true : false,
			placeholder: $( this ).data( 'placeholder' ),
			minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3'
		});
	}


	jQuery(document).on('click', '.m-aside-left-overlay', function(e) {
		e.preventDefault();

		if( $(window).width() < 1024 && $(e.target).hasClass('m-aside-left-overlay') ) {
			$('body, #m_aside_left').removeClass('m-aside-left--on');
			$('.m-aside-left-overlay').remove();
		}
	});

	jQuery(document).on('click', '.woopanel-fixed-layout .m-woopanel-toggle', function(e) {
		e.preventDefault();

		if( $('body').hasClass('m-aside-left--on') ) {
			$('body, #m_aside_left').removeClass('m-aside-left--on');
			$('.m-aside-left-overlay').remove();
		}else {
			$('body, #m_aside_left').addClass('m-aside-left--on');
			$('#m_aside_left').after('<div class="m-aside-left-overlay"></div>');
		}
	});

	$(document).on('click', '.page-links a', function(e) {
		e.preventDefault();

		$('.page-links a').removeClass('active');
		$(this).addClass('active');

		var $tab = $(this).attr('href');

		$('.page-tab-content .page-tab-panel').removeClass('active');
		$($tab).addClass('active');
	});

	if( $('.m-portlet__body .m-tabs').length > 0 ) {
		var url = window.location.href,
			page = $('.m-form').attr('data-page'),
			res = url.replace(WooPanel.url + page + '/', ''),
			tabLink = $('[href="' + res + '"]');


		
		if( tabLink.length > 0 ) {
			$('.m-nav__link.m-tabs__item').removeClass('m-tabs__item--active');
			tabLink.addClass('m-tabs__item--active');

			$('#m_sections').find('.m-tabs-content__item').removeClass('m-tabs-content__item--active');
			$(res).addClass('m-tabs-content__item--active');

			$('#wallet_type').val(res);

			tabLink.trigger('click');
		}

		$(document).on('click', '#main_portlet .m-nav__link', function() {
			var $id = $(this).attr('href');


			$('#wallet_type').val($id);

			history.pushState({
			    id: 'homepage'
			}, 'pageTitle', WooPanel.url + page + '/' + $id );
		});
	}


	var xhr_view = null;

	$(document).on('click', '.wpl-collapse-item a', function(e) {
		var layout = $(this).attr('data-layout');
		e.preventDefault();

		if( layout == 'collapse' ) {
			var save = 'fixed';
		}

		if( layout == 'expand' ) {
			var save = 'fullwidth';
		}

		Cookies.set('switch_layout', save);

		location.reload();

	});

	
	if( jQuery().slider ) {
		jQuery( ".woopanel-ui-slider" ).each(function( index ) {
			var $val = parseInt( jQuery(this).attr('data-value') );
			var $units = jQuery(this).attr('data-units');
			var $min = parseInt( jQuery(this).attr('data-min') );

			var $max = parseInt( jQuery(this).attr('data-max') );


			jQuery(this).slider({
				min: $min,
				max: $max,
				range: "min",
				step: 1,
				value: $val,
				slide: function( event, ui ) {
					var $wrapper = jQuery(ui.handle).closest('.type-slider');
					$wrapper.find('input').val(ui.value);
					$wrapper.find('.range-slider__value').text(ui.value + $units);
				}
			});
		});
	}
	
	jQuery( ".m-form__group" ).each(function( index ) {
		var $this = jQuery(this),
			logic = jQuery(this).attr('data-conditional_logic');
			
		if( typeof logic != 'undefined') {
			$this.hide();
			
			logic = jQuery.parseJSON(logic);
			jQuery.each(logic, function( index, values ) {
				var $name = jQuery('[name="' + index + '"]');
				set_conditional_logic($name, values, $this);
				
				jQuery('[name="' + index + '"]').on('change', function() {
					set_conditional_logic(jQuery(this), values, $this);
				});
			});
		}
	});
	
	
	function set_conditional_logic($name, values, el) {
		var $type = $name.attr('type'),
			$value = $name.val();
		if( $type == 'radio' || $type == 'checkbox' ) {
			$value = $name.filter(":checked").val();
		}

		if( jQuery.inArray( $value, values ) >= 0 ) {
			el.show();
		}else {
			el.hide();
		}
	}
	
	
	jQuery(document).on('click', '.btn-tax-actions', function(e) {
		e.preventDefault();
		
		var favorite = [],
			$this = jQuery(this),
			this_page = window.location.toString();
		jQuery.each( jQuery('.wpl-datatable__body .m-checkbox input[type="checkbox"]:checked' ), function(){            
			favorite.push(jQuery(this).val());
		});

		this.xhr_view = jQuery.ajax({
			url: WooPanel.ajaxurl,
			data: {
				action: 'woopanel_delete_category',
				term_id: favorite,
				taxonomy: $this.attr('data-taxonomy'),
				security: $this.attr('data-security')
			},
			type: 'POST',
			datatype: 'json',
			success: function( response ) {
				if(response.complete != undefined ) {
					jQuery( '#posts-filter' ).load( this_page + ' .woopanel-list-post-table', function() {
						
					});
				}
			},
			error:function( xhr, status, error ) {

			}
		});
	});
	
	jQuery(document).on('click', '.product-quick-edit', function(e) {
		e.preventDefault();
		
		var $this = jQuery(this),
			productID = $this.attr('data-product_id');
		jQuery('tr.quick-edit').not( jQuery('#quick-edit-' + productID) ).hide();
		
		if( $this.hasClass('active') ) {
			$this.removeClass('active');
			jQuery('#quick-edit-' + productID).slideUp();
		}else {
			$this.addClass('active');
			jQuery('#quick-edit-' + productID).css('display', 'table-row');
		}
	});
		
	jQuery(document).on('click', '.quick-edit-actions .btn-quickedit-submit', function(e) {
		e.preventDefault();
		
		var $this = jQuery(this),
			$wrapper = $this.closest('.quick-edit');

		jQuery.ajax({
			url: WooPanel.ajaxurl,
			data: 'action=woopanel_save_quickedit&' + $wrapper.find(':input').serialize(),
			type: 'POST',
			datatype: 'json',
			success: function( response ) {
				$this.removeClass('m-loader');
				$this.prop('disabled', false);
				
				if( response.complete != undefined ) {
					jQuery('[name="add_category_name"]').val('');
					jQuery('#newcategory_parent').prop('selectedIndex', 0);
					jQuery('#' +  response.element).append(response.html);
				}else {
					alert(response.data['message']);
				}
			},
			error:function( xhr, status, error ) {
				$this.removeClass('m-loader');
				$this.prop('disabled', false);
				
				if( xhr.status == 403) {
					alert( WooPanel.label.i18n_deny);
				}else {
					alert('There was an error when processing data, please try again !');
				}
			}
		});
	});	
	
	jQuery(document).on('click', '#link-category-add-submit', function(e) {
		e.preventDefault();

		var $this = jQuery(this);
		$this.addClass('m-loader');
		$this.prop('disabled', true);

		jQuery.ajax({
			url: WooPanel.ajaxurl,
			data: 'action=woopanel_add_category&name=' + jQuery('.add_category_name').val() + '&parent=' + jQuery('#newcategory_parent').val() + '&taxonomy=' + $('.taxonomy_type').val() + '&security=' + $this.attr('data-security'),
			type: 'POST',
			datatype: 'json',
			success: function( response ) {
				$this.removeClass('m-loader');
				$this.prop('disabled', false);
				
				if( response.complete != undefined ) {
					jQuery('[name="add_category_name"]').val('');
					jQuery('#newcategory_parent').prop('selectedIndex', 0);
					jQuery('#' +  response.element).append(response.html);
				}else {
					alert(response.data['message']);
				}
			},
			error:function( xhr, status, error ) {
				$this.removeClass('m-loader');
				$this.prop('disabled', false);
				
				if( xhr.status == 403) {
					alert( WooPanel.label.i18n_deny);
				}else {
					alert('There was an error when processing data, please try again !');
				}
			}
		});
	});

	jQuery(document).on('click', '#category-add-toggle:not(.disable-link)', function(e) {
		e.preventDefault();

		jQuery('#link-category-add').slideToggle();
	});

	jQuery(document).on('click', '#editable-post-name', function(e) {
		e.preventDefault();
	});

	jQuery(document).on('click', '.edit-slug', function(e) {
		e.preventDefault();
		
		var $field_edit = jQuery('#editable-post-name');
		
		if( jQuery('#new-post-slug').length <= 0 ) {
			var $permalink = jQuery('#editable-post-name').attr('data-title');
			$field_edit.html('<input type="text" id="new-post-slug" name="post_permalink" value="' + $permalink + '" class="form-control m-input" autocomplete="off">');
			jQuery('#edit-slug-buttons').hide();
		}
	});

	hightlight_table();
	function hightlight_table() {
		jQuery( "tr.wpl-datatable_edit" ).each(function( index ) {
			if (index % 2 === 0) {
				jQuery(this).addClass('td-white');
			}
		});
		
		jQuery( "tr.wpl-datatable__row_cmhide" ).each(function( index ) {
			if (index % 2 === 0) {
				jQuery(this).addClass('td-white');
			}
		});
	}

	jQuery(document).on('click', '.cm-destructive', function(e) {
		e.preventDefault();
		
		var $id = jQuery(this).attr('data-id');

		jQuery('#cm-hide-' + $id + ' td .spam-undo-inside, #cm-hide-' + $id + ' td .trash-undo-inside').hide();
		jQuery('#cm-hide-' + $id + ' td').css('background-color', '#dff0d8');
		jQuery('#cm-hide-' + $id + ' td').fadeOut( 400, function() {
			jQuery('#cm-hide-' + $id).hide();
			jQuery('#user-' + $id + ' td').removeAttr('style');
			jQuery('#user-' + $id).show();
		});

		
		jQuery.ajax({
			url: WooPanel.ajaxurl,
			data: 'action=woopanel_comment_link&method=undo&id=' + $id,
			type: 'POST',
			datatype: 'json',
			success: function( response ) {
			},
			error:function(){
				alert('There was an error when processing data, please try again !');
			}
		});
	});
	
	jQuery(document).on('click', '.comment-link', function(e) {
		e.preventDefault();

		var $action = jQuery(this).attr('data-action'),
			$id = jQuery(this).attr('data-id'),
			$row = jQuery(this).closest('.row-actions');

		if( $action == 'spam' || $action == 'trash' ) {
			if( $action == 'spam' ) {
				jQuery('#cm-hide-' + $id + ' td .spam-undo-inside').show();
			}
			
			if( $action == 'trash' ) {
				jQuery('#cm-hide-' + $id + ' td .trash-undo-inside').show();
			}

			jQuery('#user-' + $id + ' td').css('background-color', '#f2dede');
			jQuery('#cm-hide-' + $id + ' td').removeAttr('style');
			jQuery('#user-' + $id ).fadeOut( "slow", function() {
				jQuery('#cm-hide-' + $id).show();
			});
			
			$row.closest('.wpl-datatable__row').css('background-color', '#f2dede');
		}
	
		if( $action === "unapprove" ||
			$action === "approve" ||
			$action === "delete" ||
        	$action === "unspam" ||
            $action === "untrash" ) {
			jQuery('.woopanel-list-post-table .table-responsive').block({
				message: '<div class="loading-message"><div class="block-spinner-bar"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>',
				overlayCSS: {
					background: '#555',
					opacity: 0.1
				}
			});
		}

		
		jQuery.ajax({
			url: WooPanel.ajaxurl,
			data: 'action=woopanel_comment_link&method=' + $action + '&id=' + $id,
			type: 'POST',
			datatype: 'json',
			success: function( response ) {
				jQuery('.woopanel-list-post-table .table-responsive').unblock();
				if( response.complete != undefined ) {
					if( response.status_class == 'approved' ) {
                        $row.find('span.approve').removeClass('hidden');
                        $row.find('span.unapproved').addClass('hidden');
					} else if( response.status_class == 'unapproved' ) {
                        $row.find('span.approve').addClass('hidden');
                        $row.find('span.unapproved').removeClass('hidden');
					}
					if( $action === 'unspam' || $action == 'untrash' || $action == 'delete' ) {
                        $row.closest('tr').remove();
                    }
				}
			},
			error:function(){
				alert('There was an error when processing data, please try again !');
			}
		});
	});
    function display_bulk_actions(){
        var checked = jQuery('tbody .check-column input[type=checkbox]:checked').length;
        if( checked > 0 ) {
            jQuery('.tablenav').show();
            jQuery('.tablenav .selected_number').html( checked );
        } else {
            jQuery('.tablenav').hide();
        }
    }
    display_bulk_actions();
	
	if( jQuery().selectpicker ) {
		jQuery('.m-bootstrap-select').selectpicker();
	}

    jQuery("table").on("click", "thead .check-column :checkbox, tfoot .check-column :checkbox", function(){
        var c = this.checked;
        jQuery(':checkbox').prop('checked', c);
        display_bulk_actions();
    });
    
    jQuery("table").on("click", "tbody .check-column input[type=checkbox]", function(){
        display_bulk_actions();
    });
	
	if( jQuery().tooltip ) {
		jQuery('[data-toggle="tooltip"]').tooltip({ html: true });
	}
	
	if( jQuery().datepicker ) {
		var arrows;
		arrows = {
			leftArrow: '<i class="la la-angle-left"></i>',
			rightArrow: '<i class="la la-angle-right"></i>'
		}
	
        jQuery('.m-datepicker').datepicker({
            todayHighlight: true,
            templates: arrows,
			format: 'yyyy-mm-dd'
        });
		
		jQuery('.m-datepicker').on('changeDate', function(ev){
			jQuery(this).datepicker('hide');
		});
	}
	
	if( jQuery('.woopanel-readmore').length > 0 ) {
		jQuery( '.woopanel-readmore' ).each(function( index ) {
			var $max_height = jQuery(this).attr('data-height');
			var $length = jQuery(this).outerHeight();
			
			if( $length > $max_height ) {
				jQuery(this).css({"height": $max_height, "overflow": "hidden"});
				jQuery(this).after('<a href="javascript:;" class="woopanel-readmore-text">... view more</a>');
			}
		});
		
		jQuery(document).on('click', '.woopanel-readmore-text', function(e) {
			e.preventDefault();
			
			jQuery(this).prev().removeAttr('style');
			jQuery(this).remove();
		});
	}
	
	if( jQuery( '.m-datatable__table' ).length > 0 ) {
		
		jQuery( '.m-datatable__table tbody tr' ).each(function( index ) {
			var $tr = jQuery(this);

			var max = 0;
			$tr.find('td').each(function( index ) {
				if( jQuery(this).children().length > 0 ) {
					var $width = jQuery(this).children().outerWidth();
				}else {
					var $width = jQuery(this).outerWidth();
				}
				
				$width = Math.ceil($width) + 50;
				jQuery(this).addClass('xxx-' + $width);
			});
		});
	}

	if( jQuery('.woopanel-loading-wrapper').length > 0 ) {

		var link_was_clicked = false;
		document.addEventListener("click", function(e) {
			link_was_clicked = true;
		}, true);

		jQuery('.woopanel-loading-wrapper').show();
		
		jQuery(window).load(function() {
			jQuery('.woopanel-loading-wrapper').hide();
		});

		window.onbeforeunload  = function (e) {
			if(link_was_clicked) {
				link_was_clicked = false;
				return;
			}else {
				jQuery('.woopanel-loading-wrapper').show();
			}
			
		}
	}

	jQuery(document).on( 'click', '.woopanel-loading-closed', function(e) {
		e.preventDefault();
		jQuery('.woopanel-loading-wrapper').hide();
	});

	jQuery(document).on( 'click', '.wpl-icon_item', function(e) {
		e.preventDefault();

		jQuery(this).prev().trigger('click');
	});

})(jQuery);