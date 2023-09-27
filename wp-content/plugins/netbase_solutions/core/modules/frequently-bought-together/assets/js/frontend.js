;(function($){
	$(document).ready(function(){
		"use strict";
		var items				= $('.nbcs-list-items'),
			item 				= $('.nbcs-list-items li'),
			first_item 			= $('.nbcs-list-items li:first-child'),
			checkbox 			= items.find('input'),
			total_price_wrap	= $('.nbcs-price-box'),
			total_price_class 	= total_price_wrap.find('.nbcs-display-total-price'),
			total_price_html	= total_price_class.find('.amount');

		/**
		 * Number.prototype.format(n, x)
		 * 
		 * @param integer n: length of decimal
		 * @param integer x: length of sections
		 */
		Number.prototype.format = function(n, x) {
		    var re = '(\\d)(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
		    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$1,');
		};


		// return total price with currency
		var get_total = function(total) {
		    var html = '';
		    if ( nb_fbt.currency_pos === 'left' ) {
		        html = nb_fbt.currency_symbol + total.format(2);
		    }
		    else if ( nb_fbt.currency_pos === 'left_space' ) {
		        html = nb_fbt.currency_symbol + ' ' + total.format(2);
		    }
		    else if ( nb_fbt.currency_pos === 'right' ) {
		        html = total.format(2) + nb_fbt.currency_symbol;
		    }
		    else if ( nb_fbt.currency_pos === 'right_space' ) {
		        html = total.format(2) + ' ' + nb_fbt.currency_symbol;
		    }
		    return html;
		};

		checkbox.on('change', function() {
			
			var t 				= $(this),
				id 				= t.attr('id'),
				total_price_attr= total_price_class.attr('data-total-price'),
				product_price 	= t.attr('data-price'),
				to_show  		= [],
				checked			= items.find('input:checked'),
				thumb 			= $('.nbcs-list-images li.nbcs-thumb[data-rel="' + id + '"]'),
				plus_icon		= $('li.nbcs-plus-icon'),				
				total 			= 0;

			t.parents('li').toggleClass('unchecked');

			checked.each(function(i) {
			    to_show[i] 	= this.id;
			    total 		+= parseFloat( $(this).attr('data-price') );
			});
		    total_price_class.attr('data-total-price', total);

			// show thumbnail
			thumb.fadeToggle();

			//manage plus icon
			
			//all combox box unchecked OR only 1 combo box checked
			if(to_show.length == 0 || to_show.length == 1) {
				plus_icon.fadeOut();
			}
			// all combox box combox_checked
			else if(to_show.length == item.length) {
				plus_icon.fadeIn();
			}
	        else if(id == 'offeringID_0') {
	        	if(to_show[0] != id) {
	        		$('li.nbcs-plus-icon[data-rel="' + to_show[0] + '"]').fadeOut();
	        	}
	        	else
	        	{
	        		thumb.next(plus_icon).fadeIn();
	        	}
	        }
			else if(to_show[0] == id || to_show[0] == thumb.next(plus_icon).data('rel')) {
	            thumb.next(plus_icon).fadeToggle();
	        }
	        else {
	            thumb.prev(plus_icon).fadeToggle();	            
	        }

	        //update total price html
	        total_price_html.html(get_total(total));
	         

			//show/hide add to cart button
	        var combox_checked = checked.length;
	        if(combox_checked == 0) {
	        	total_price_wrap.hide();
	        }
	        else {
	        	total_price_wrap.show();	
	        }

	        //show/hide class clear-price-box
	        if(combox_checked <= 3 ) {
	        	total_price_wrap.removeClass('clear-price-box');
	        }
	        else{
	        	total_price_wrap.addClass('clear-price-box');
	        }
		});

		var variations_form = $('form.variations_form');

		//check if has variations form
		if(variations_form.length == 1)
		{
			$(document).on('change', 'table.variations select', function(){
			  var target = document.body;
			   var config = {
			      childList: true,
			      subtree: true,
			      attributes: true,
			      characterData: true
			  };

			  observer.observe(target, config);

			  setTimeout(function(){
			    observer.disconnect();
			  }, 1000);

			});

	        var variations_data = JSON.parse($('form.variations_form').attr('data-product_variations'));

	        //use MutationObserver to get image_id
			var observer = new MutationObserver(function (mutationRecords, observer) {
			    mutationRecords.forEach(function (mutation) {

			    var first_id = $('.variations tr:first-child select').attr('id');
			    if(mutation.attributeName == 'current-image'){

			      	var select_id = mutation.target[0].id,
			      	image_id = $('.variations_form').attr('current-image');

			      	if(select_id == first_id && image_id) {
		                //find data from variations_data
		                var new_variation_data = {};

		                $.each(variations_data, function (i) {                  
		                    $.each(variations_data[i], function (key, val) {
		                        if(key == 'image_id' && val == image_id) {
		                            new_variation_data[i] = variations_data[i];
		                        }
		                    });
		                });
		                
		                var new_thumb   		= '',
		                	new_srcset 			= '',
		                	new_price 			= 0,
		                	new_price_html 		= '',
		                	new_variation_id 	= 0;
		                $.each(new_variation_data, function (k) {
		                    new_thumb       = new_variation_data[k].image.thumb_src;
		                    new_srcset      = new_variation_data[k].image.srcset;
		                    new_price       = new_variation_data[k].display_price;
		                    new_price_html  = new_variation_data[k].price_html;
		                    new_variation_id = new_variation_data[k].variation_id;
		                });             

		                if(new_thumb != '' && new_srcset != '') {
		                    $('.nbcs-list-images li:first-child a img').attr('src', new_thumb).attr('srcset', new_srcset);
		                }

		                var total_price             = total_price_class.attr('data-total-price'),
		                	first_combo_box 		= first_item.find('input[name="cb-item[]"]'),
		                	first_input 			= first_item.find('input[name="offeringID[]"]'),
		                	first_product_price     = first_combo_box.attr('data-price'),
		                	first_product_price_html= first_item.find('.amount'),
							new_total_price = parseFloat(total_price) - parseFloat(first_product_price) + parseFloat(new_price);
						// if first combo box checked -> update price
						if(!first_item.hasClass('unchecked')) {
			                total_price_html.html(get_total(new_total_price));
			                total_price_class.attr('data-total-price', new_total_price);
						}
		                first_combo_box.attr('data-price', new_price).val(new_variation_id);
		                first_product_price_html.html(new_price_html);
		                first_input.val(new_variation_id).removeClass().addClass('offeringID-' + new_variation_id);
			      	}
			    }
			  });
			});
		}

	})
}(jQuery));