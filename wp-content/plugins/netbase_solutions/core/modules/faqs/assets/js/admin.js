jQuery( function( $ ) {


	var nbtfaq_load = {
		/**
		 * Init jQuery.BlockUI
		 */
		block: function($el) {
			$el.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},

		/**
		 * Remove jQuery.BlockUI
		 */
		unblock: function($el) {
			$el.unblock();
		}
	}



	/**
	 * Variations Price Matrix actions
	 */
	var nbtfaq_admin = {

		$el: 22,
		toolbars: {},

		/**
		 * Initialize variations actions
		 */
		init: function() {
			$(document).on('click', '.repeater-heading', this.repeater_arrow);
			$(document).on('click', '.nbt-repeater-btn-add', this.add_row);
			$(document).on('click', '.nbt-heading-btn-add', this.add_heading);
			$(document).on('keyup', '.faq_title', this.live_title);
			$(document).on('click', '.faq-action-row .button-link-delete', this.delete_section);
			$(document).on('click', '.faq-action-row .widget-control-close', this.close_section);
			$(document).on('mouseenter', '.nbt-repeater-order', this.dragdrop);
			$(document).on('click', '.nbt-heading-product-add', this.add_new_heading);
			$(document).on('click', '.nbt-repeater-product-add', this.add_new_faq);
			$(document).on('change', '.select-global-faqs', this.select_global_faqs);

			$( '.js-select2' ).select2({ width: '100%'});
		},
		add_new_heading: function(){
			var $box = $(this).closest('.heading-box');
			var $count = $('.row-heading').length;
			var $template = $('#nbt-heading-template').html();
			$('#nbt-repeater').show();


			var $template = $template.replace("select_global_faqs[]", "select_global_faqs[" + $count + "]");
			$template = $template.replace("global_faqs[]", "global_faqs[" + $count + "]");
			$template = $template.replace("faq_title[]", "faq_title[" + $count + "]");

			$('#nbt-repeater-wrap').append('<div id="heading-' + $count + '" class="heading-box" data-id="' + $count + '">' + $template + '</div>');
			$box.find('.select-data').hide();
			$( '.js-select2' ).select2({ width: '100%'});
		},
		select_global_faqs: function(){
			var heading = 0;
			if($(this).closest('.repeater-row').hasClass('row-heading')){
				var heading = 1;
			}
			var $box = $(this).closest('.repeater-row');
			nbtfaq_load.block($box);
			var $val = $(this).val();
			$box.find('.select-global-data').html('');

			$box.find('.select-data').hide();
			if($val){
				$('.field-single-faq').hide();
			}else{
				$('.field-single-faq').show();
			}
			$.ajax({
				url: nbtfaqs.ajax_url,
				data: {
					action:     'load_global_faqs',
					faqs: $val,
					heading: heading
				},
				type: 'POST',
				datatype: 'json',
				success: function( response ) {
					var rs = JSON.parse(response);
					
					if(rs.complete != undefined){
						$box.find('.select-data').show();
						$box.find('.select-global-data').html(rs.option);
						$( '.js-select2' ).select2({ width: '100%'});
					}
					nbtfaq_load.unblock($box);
				},
				error:function(){
					alert('There was an error when processing data, please try again !');
					nbtfaq_load.unblock($box);
				}
			});
		},
		add_new_faq: function(){
			var count_heading = $('.row-heading').length;
			if( count_heading <= 0) {
				alert('Please add heading first!');
				return;
			}


			$('#nbt-repeater').show();
			var $count = $('.repeater-row').length;
			var $template = $('#nbt-repeater-template').html();
			var $id_heading = $('#nbt-repeater-wrap > .heading-box:last-child').attr('data-id');

	
			$template = nbt_editor.str_replace( 'id_repeater', 'repeater-row-' + $count, $template );
			$template = $template.replace( 'faq_title[]', "faq_title[" + $id_heading + "][]" );
			$template = $template.replace("select_repeater_faq_type[]", "select_repeater_faq_type[" + $id_heading + "][]");
			$template = $template.replace("select_repeater_faq_option[]", "select_repeater_faq_option[" + $id_heading + "][]");
			$template = nbt_editor.str_replace( 'id_repeater', 'repeater-row-' + $count, $template );
			$('#repeater-row-' + $count).attr('data-id' + $count);

			if($('#nbt-repeater-wrap > .heading-box:last-child').length){
				$('#nbt-repeater-wrap > .heading-box:last-child').append($template);
			}else{
				$('#nbt-repeater-wrap').append('<div id="heading-' + $count + '" class="heading-box" data-id="' + $count + '">' + $template + '</div>');
			}
			$('#repeater-row-' + $count).find('.wp-editor-area').attr('name', 'faq_content[' + $id_heading + '][]');
			
			nbt_editor.init('repeater-row-' + $count);

			$( '.js-select2' ).select2({ width: '100%'});

			return false;
		},
		add_row: function() {
			var count_heading = $('.row-heading').length;
			if( count_heading <= 0) {
				alert('Please add heading first!');
				return;
			}

			var $count = $('.repeater-row').length;
			var $id_heading = $('#nbt-repeater-wrap > .heading-box:last-child').attr('data-id');
			var $template = $('#nbt-repeater-template').html();
			$template = $template.replace( 'faq_title[]', "faq_title[" + $id_heading + "][]" );
			$template = nbt_editor.str_replace( 'id_repeater', 'repeater-row-' + $count, $template );
			$('#repeater-row-' + $count).attr('data-id' + $count);
			
			
			$('#nbt-repeater-wrap > .heading-box:last-child').append($template);
			$('#repeater-row-' + $count).find('.wp-editor-area').attr('name', 'faq_content[' + $id_heading + '][]');

			nbt_editor.init('repeater-row-' + $count);
		},
		add_heading: function(){
			var $count = $('.row-heading').length;
			var $template = $('#nbt-heading-template').html();

			var $template = $template.replace("faq_title[]", "faq_title[" + $count + "]");

			$('#nbt-repeater-wrap').append('<div id="heading-' + $count + '" class="heading-box" data-id="' + $count + '">' + $template + '</div>');
		},
		delete_section: function(){
			$(this).closest('.repeater-row').remove();
		},
		close_section: function(){
			var $row = $(this).closest('.repeater-row');
			$row.removeClass('open');
			$row.find('.repeater-content').hide();
		},
		repeater_arrow: function(){

			var $row = $(this).closest('.repeater-row');
			if($row.hasClass('open')){
				$row.removeClass('open');
				$row.find('.repeater-content').hide();
			}else{
				$row.addClass('open');
				$row.find('.repeater-content').show();
			}
		},
		live_title: function(){
			var text = $(this).val();
			$(this).closest('.repeater-row').find('.nbt-repeater-title').text(': ' + text);
		},
		dragdrop: function(){
			// sortable
			$('#nbt-repeater-wrap').sortable({
				items: '> .repeater-row',
				handle: '> div.nbt-repeater-order',
				forceHelperSize: true,
				forcePlaceholderSize: true,
				scroll: true,
				start: function(event, ui) {

					// acf.do_action('sortstart', ui.item, ui.placeholder);

	   			},
	   			stop: function(event, ui) {

					// render
					// self.render();

					// acf.do_action('sortstop', ui.item, ui.placeholder);

	   			},
	   			update: function(event, ui) {

		   			// trigger change
					//self.$input.trigger('change');

		   		}

			});
		}
	}

	nbtfaq_admin.init();

	var nbt_editor = {

		toolbars: {},


		init: function(id){
			// bail early if no tinyMCEPreInit (needed by both tinymce and quicktags)
			if( typeof tinyMCEPreInit === 'undefined' ) return;

			var $wrap = $('#' + id).find('.nbt-editor-wrap');
			var toolbar = $wrap.attr('data-toolbar');

			var old_id = 'nbt-editor-' + $wrap.closest('.nbt-field-wysiwyg').attr('data-id'),
				new_id = nbt_editor.get_uniqid('nbt-editor-'),
				html = $wrap[0].outerHTML;

			// replace
			html = nbt_editor.str_replace( old_id, new_id, html );
			// swap
			$('#' + id).find('.nbt-editor-input').html( html );

			this.initialize_tinymce(new_id);
			this.initialize_quicktags(new_id);
		},
		initialize_tinymce: function(id){
			// bail early if no tinymce
			if( typeof tinymce === 'undefined' ) return;
			
			// bail early if no tinyMCEPreInit.mceInit
			if( typeof tinyMCEPreInit.mceInit === 'undefined' ) return;

			// vars
			var mceInit = this.get_mceInit(id);

			// append
			tinyMCEPreInit.mceInit[ mceInit.id ] = mceInit;

			// bail early if not visual active
			if( !$('#' + id).closest('.nbt-editor-wrap').hasClass('tmce-active') ) return;

			// initialize
			try {
				
				// init
				tinymce.init( mceInit );
				
				
				// vars
				var ed = tinyMCE.get( mceInit.id );
				
		
			} catch(e){}

		},
		initialize_quicktags: function(id){
			
			// bail early if no quicktags
			if( typeof quicktags === 'undefined' ) return;
			
			
			// bail early if no tinyMCEPreInit.qtInit
			if( typeof tinyMCEPreInit.qtInit === 'undefined' ) return;
			
			
			// vars
			var qtInit = this.get_qtInit(id);
			
			
			// append
			tinyMCEPreInit.qtInit[ qtInit.id ] = qtInit;
			
			
			// initialize
			try {
				
				// init
				var qtag = quicktags( qtInit );
				
				
				// buttons
				this._buttonsInit( qtag );

				$('#' + id).prop("disabled", false);;
	
			} catch(e){}
			
		},
		get_mceInit : function(id){
			var itoolbar = $('#wp-' + id + '-wrap').attr('data-toolbar');
	

			
			// reference
			var $field = $('#' + id).closest('.repeater-row');

			// vars
			var toolbar = this.get_toolbar( itoolbar ),
				mceInit = $.extend({}, tinyMCEPreInit.mceInit.content);


			// selector
			mceInit.selector = '#' + id;
			
			
			// id
			mceInit.id = id; // tinymce v4
			mceInit.elements = id; // tinymce v3
			
			
			// toolbar
			if( toolbar ) {
				
				var k = (tinymce.majorVersion < 4) ? 'theme_advanced_buttons' : 'toolbar';
				
				for( var i = 1; i < 5; i++ ) {
					
					mceInit[ k + i ] = this.isset(toolbar, i) ? toolbar[i] : '';
					
				}

				
			}
			
			
			
			// events
			if( tinymce.majorVersion < 4 ) {
				
				mceInit.setup = function( ed ){
					
					ed.onInit.add(function(ed, event) {
						
						// focus
						$(ed.getBody()).on('focus', function(){
					
							acf.validation.remove_error( $field );
							
						});
						
						$(ed.getBody()).on('blur', function(){
							
							// update the hidden textarea
							// - This fixes a bug when adding a taxonomy term as the form is not posted and the hidden textarea is never populated!
			
							// save to textarea	
							ed.save();
							
							
							// trigger change on textarea
							$field.find('textarea').trigger('change');
							
						});
					
					});
					
				};
			
			} else {
			
				mceInit.setup = function( ed ){
					
					ed.on('focus', function(e) {
				
						
						
					});
					
					ed.on('change', function(e) {
						
						// save to textarea	
						ed.save();
						
						
						$field.find('textarea').trigger('change');
						
					});
										
				};
			
			}
			
			
			// disable wp_autoresize_on (no solution yet for fixed toolbar)
			mceInit.wp_autoresize_on = false;
		
			// return
			return mceInit;
			
		},
		get_qtInit : function(id){
				
			// vars
			var qtInit = $.extend({}, tinyMCEPreInit.qtInit.content);
			
			
			// id
			qtInit.id = id;
			
			
			// return
			return qtInit;
			
		},
		_buttonsInit: function( ed ) {
			var defaults = ',strong,em,link,block,del,ins,img,ul,ol,li,code,more,close,';
	
			canvas = ed.canvas;
			name = ed.name;
			settings = ed.settings;
			html = '';
			theButtons = {};
			use = '';

			// set buttons
			if ( settings.buttons ) {
				use = ','+settings.buttons+',';
			}

			for ( i in edButtons ) {
				if ( !edButtons[i] ) {
					continue;
				}

				id = edButtons[i].id;
				if ( use && defaults.indexOf( ',' + id + ',' ) !== -1 && use.indexOf( ',' + id + ',' ) === -1 ) {
					continue;
				}

				if ( !edButtons[i].instance || edButtons[i].instance === inst ) {
					theButtons[id] = edButtons[i];

					if ( edButtons[i].html ) {
						html += edButtons[i].html(name + '_');
					}
				}
			}

			if ( use && use.indexOf(',fullscreen,') !== -1 ) {
				theButtons.fullscreen = new qt.FullscreenButton();
				html += theButtons.fullscreen.html(name + '_');
			}


			if ( 'rtl' === document.getElementsByTagName('html')[0].dir ) {
				theButtons.textdirection = new qt.TextDirectionButton();
				html += theButtons.textdirection.html(name + '_');
			}

			ed.toolbar.innerHTML = html;
			ed.theButtons = theButtons;
			
		},
		get_toolbar : function( name ){
			this.toolbars.basic = {};
			this.toolbars.full = {};
			this.toolbars.basic[1] = 'bold,italic,underline,blockquote,strikethrough,bullist,numlist,alignleft,aligncenter,alignright,undo,redo,link,unlink,fullscreen';
			this.toolbars.full[1] = 'formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,fullscreen,wp_adv';
			this.toolbars.full[2] = 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help';
			
			// bail early if toolbar doesn't exist
			if( typeof this.toolbars[ name ] !== 'undefined' ) {
				return this.toolbars[ name ];
			}
			
			// return
			return false;
			
		},
		isset : function(){
			
			var a = arguments,
		        l = a.length,
		        c = null,
		        undef;
			
		    if (l === 0) {
		        throw new Error('Empty isset');
		    }
			
			c = a[0];
			
		    for (i = 1; i < l; i++) {
		    	
		        if (a[i] === undef || c[ a[i] ] === undef) {
		            return false;
		        }
		        
		        c = c[ a[i] ];
		        
		    }
		    
		    return true;	
			
		},
		str_replace: function( search, replace, subject ) {
			return subject.split(search).join(replace);
		},
		get_uniqid : function( prefix, more_entropy ){
		
			// + original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
			// + revised by: Kankrelune (http://www.webfaktory.info/)
			// % note 1: Uses an internal counter (in php_js global) to avoid collision
			// * example 1: uniqid();
			// * returns 1: 'a30285b160c14'
			// * example 2: uniqid('foo');
			// * returns 2: 'fooa30285b1cd361'
			// * example 3: uniqid('bar', true);
			// * returns 3: 'bara20285b23dfd1.31879087'
			if (typeof prefix === 'undefined') {
				prefix = "";
			}
			
			var retId;
			var formatSeed = function (seed, reqWidth) {
				seed = parseInt(seed, 10).toString(16); // to hex str
				if (reqWidth < seed.length) { // so long we split
					return seed.slice(seed.length - reqWidth);
				}
				if (reqWidth > seed.length) { // so short we pad
					return Array(1 + (reqWidth - seed.length)).join('0') + seed;
				}
				return seed;
			};
			
			// BEGIN REDUNDANT
			if (!this.php_js) {
				this.php_js = {};
			}
			// END REDUNDANT
			if (!this.php_js.uniqidSeed) { // init seed with big random int
				this.php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
			}
			this.php_js.uniqidSeed++;
			
			retId = prefix; // start with prefix, add current milliseconds hex string
			retId += formatSeed(parseInt(new Date().getTime() / 1000, 10), 8);
			retId += formatSeed(this.php_js.uniqidSeed, 5); // add seed hex string
			if (more_entropy) {
				// for more entropy we add a float lower to 10
				retId += (Math.random() * 10).toFixed(8).toString();
			}
			
			return retId;
		}
	}

});