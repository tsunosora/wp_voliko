function netbase_shortcode_open(name, id) {
    var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
    W = W - 80;
    H = H - 120;
    tb_show( 'Netbase ' + name + ' Shortcode', '#TB_inline?width=' + W + '&height=' + H + '&inlineId='+ id +'-form' );
}

function netbase_shortcode_close() {

}

function netbase_shortcode_animation_type() {
    var html = '<option value="">none</option>\
    <optgroup label="Attention Seekers">\
        <option value="bounce">bounce</option>\
        <option value="flash">flash</option>\
        <option value="pulse">pulse</option>\
        <option value="rubberBand">rubberBand</option>\
        <option value="shake">shake</option>\
        <option value="swing">swing</option>\
        <option value="tada">tada</option>\
        <option value="wobble">wobble</option>\
    </optgroup>\
    <optgroup label="Bouncing Entrances">\
        <option value="bounceIn">bounceIn</option>\
        <option value="bounceInDown">bounceInDown</option>\
        <option value="bounceInLeft">bounceInLeft</option>\
        <option value="bounceInRight">bounceInRight</option>\
        <option value="bounceInUp">bounceInUp</option>\
    </optgroup>\
    <optgroup label="Fading Entrances">\
        <option value="fadeIn">fadeIn</option>\
        <option value="fadeInDown">fadeInDown</option>\
        <option value="fadeInDownBig">fadeInDownBig</option>\
        <option value="fadeInLeft">fadeInLeft</option>\
        <option value="fadeInLeftBig">fadeInLeftBig</option>\
        <option value="fadeInRight">fadeInRight</option>\
        <option value="fadeInRightBig">fadeInRightBig</option>\
        <option value="fadeInUp">fadeInUp</option>\
        <option value="fadeInUpBig">fadeInUpBig</option>\
    </optgroup>\
    <optgroup label="Flippers">\
        <option value="flip">flip</option>\
        <option value="flipInX">flipInX</option>\
        <option value="flipInY">flipInY</option>\
    </optgroup>\
    <optgroup label="Lightspeed">\
        <option value="lightSpeedIn">lightSpeedIn</option>\
    </optgroup>\
    <optgroup label="Rotating Entrances">\
        <option value="rotateIn">rotateIn</option>\
        <option value="rotateInDownLeft">rotateInDownLeft</option>\
        <option value="rotateInDownRight">rotateInDownRight</option>\
        <option value="rotateInUpLeft">rotateInUpLeft</option>\
        <option value="rotateInUpRight">rotateInUpRight</option>\
    </optgroup>\
    <optgroup label="Sliders">\
        <option value="slideInDown">slideInDown</option>\
        <option value="slideInLeft">slideInLeft</option>\
        <option value="slideInRight">slideInRight</option>\
    </optgroup>\
    <optgroup label="Specials">\
        <option value="hinge">hinge</option>\
        <option value="rollIn">rollIn</option>\
    </optgroup>';

    return html;
}

function netbase_shortcode_testimonial_view_type() {
    var html = '<option value="default">Default</option>\
        <option value="simple">Simple</option>\
        <option value="transparent">Transparent</option>';

    return html;
}

function netbase_shortcode_testimonial_color_skin() {
    var html = '<option value="">Normal</option>\
        <option value="white">White</option>';

    return html;
}

function netbase_shortcode_align() {
    var html = '<option value="">None</option>\
        <option value="left">Left</option>\
        <option value="right">Right</option>\
        <option value="center">Center</option>\
        <option value="justify">Justify</option>';

    return html;
}

function netbase_shortcode_boolean_true() {
    var html = '<option value="true" selected="selected">True</option>\
        <option value="">False</option>';

    return html;
}

function netbase_shortcode_boolean_false() {
    var html = '<option value="true">True</option>\
        <option value="" selected="selected">False</option>';

    return html;
}

function netbase_shortcode_blog_layout() {
    var html = '<option value="full">Full</option>\
        <option value="large">Large</option>\
        <option value="large-alt">Large Alt</option>\
        <option value="medium">Medium</option>\
        <option value="grid">Grid</option>\
        <option value="timeline" selected="selected">Timeline</option>';

    return html;
}

function netbase_shortcode_blog_grid_columns() {
    var html = '<option value="2">2</option>\
        <option value="3" selected="selected">3</option>\
        <option value="4">4</option>';

    return html;
}

function netbase_shortcode_portfolio_layout() {
    var html = '<option value="grid">Grid</option>\
        <option value="timeline" selected="selected">Timeline</option>\
        <option value="medium">Medium</option>\
        <option value="large">Large</option>\
        <option value="full">Full</option>';

    return html;
}

function netbase_shortcode_portfolio_grid_view() {
    var html = '<option value="">Classic</option>\
        <option value="full">Full</option>';

    return html;
}

function netbase_shortcode_portfolio_grid_columns() {
    var html = '<option value="2">2</option>\
        <option value="3">3</option>\
        <option value="4">4</option>\
        <option value="5">5</option>\
        <option value="6">6</option>';

    return html;
}

function netbase_shortcode_products_view() {
    var html = '<option value="grid">Grid</option>\
        <option value="list">List</option>\
        <option value="products-slider">Slider</option>';

    return html;
}

function netbase_shortcode_products_grid_columns() {
    var html = '<option value="1">1</option>\
        <option value="2">2</option>\
        <option value="3">3</option>\
        <option value="4" selected="selected">4</option>\
        <option value="5">5</option>\
        <option value="6">6</option>\
        <option value="7">7 (without sidebar)</option>\
        <option value="8">8 (without sidebar)</option>';

    return html;
}

function netbase_shortcode_products_grid_column_width() {
    var html = '<option value="">Default</option>\
        <option value="1">1/1 of content width</option>\
        <option value="2">1/2 of content width</option>\
        <option value="3">1/3 of content width</option>\
        <option value="4">1/4 of content width</option>\
        <option value="5">1/5 of content width</option>\
        <option value="6">1/6 of content width</option>\
        <option value="7">1/7 of content width</option>\
        <option value="8">1/8 of content width</option>';

    return html;
}

function netbase_shortcode_products_orderby() {
    var html = '<option value=""></option>\
        <option value="date">Date</option>\
        <option value="ID">ID</option>\
        <option value="author">Author</option>\
        <option value="title">Title</option>\
        <option value="modified">Modified</option>\
        <option value="rand">Random</option>\
        <option value="comment_count">Comment count</option>\
        <option value="menu_order">Menu order</option>';

    return html;
}

function netbase_shortcode_products_order() {
    var html = '<option value=""></option>\
        <option value="DESC">Descending</option>\
        <option value="ASC">Ascending</option>';

    return html;
}

function netbase_shortcode_products_addlinks_pos() {
    var html = '<option value="">Default</option>\
        <option value="outimage">Out of Image</option>\
        <option value="onimage">On Image</option>';

    return html;
}

function netbase_shortcode_product_view() {
    var html = '<option value="grid">Grid</option>\
        <option value="list">List</option>';

    return html;
}

function netbase_shortcode_product_categories_view() {
    var html = '<option value="grid">Grid</option>\
        <option value="products-slider">Slider</option>';

    return html;
}

function netbase_shortcode_widget_products_show() {
    var html = '<option value="">All Products</option>\
        <option value="featured">Featured Products</option>\
        <option value="onsale">On-sale Products</option>';

    return html;
}

function netbase_shortcode_widget_products_orderby() {
    var html = '<option value="date">Date</option>\
        <option value="price">Price</option>\
        <option value="rand">Random</option>\
        <option value="sales">Sales</option>';

    return html;
}

function netbase_shortcode_blockquote_view_type() {
    var html = '<option value="">Default</option>\
        <option value="with-borders">With Borders</option>';

    return html;
}

function netbase_shortcode_blockquote_dir() {
    var html = '<option value="">Default</option>\
        <option value="blockquote-reverse">Reverse</option>';

    return html;
}

function netbase_shortcode_skin_color() {
    var html = '<option value="custom"></option>\
        <option value="primary">Primary</option>\
        <option value="secondary">Secondary</option>\
        <option value="tertiary">Tertiary</option>\
        <option value="quaternary">Quaternary</option>\
        <option value="dark">Dark</option>\
        <option value="light">Light</option>';

    return html;
}

function netbase_shortcode_position() {
    var html = '<option value="top">Top</option>\
        <option value="right">Right</option>\
        <option value="bottom">Bottom</option>\
        <option value="left">Left</option>';

    return html;
}

function netbase_shortcode_display_type() {
    var html = '<option value="">Inline</option>\
        <option value="block">Block</option>';

    return html;
}

function netbase_shortcode_tooltip_type() {
    var html = '<option value="">Link</option>\
        <option value="btn-link">Button Link</option>\
        <option value="btn">Button</option>';

    return html;
}

function netbase_shortcode_popover_type() {
    var html = '<option value="">Link</option>\
        <option value="btn-link">Button Link</option>\
        <option value="btn">Button</option>';

    return html;
}

function netbase_shortcode_size() {
    var html = '<option value="">Normal</option>\
        <option value="lg">Large</option>\
        <option value="sm">Small</option>\
        <option value="xs">Extra Small</option>';

    return html;
}

function netbase_shortcode_colors() {
    var html = '<option value="custom"></option>\
        <option value="primary">Primary</option>\
        <option value="secondary">Secondary</option>\
        <option value="tertiary">Tertiary</option>\
        <option value="quaternary">Quaternary</option>\
        <option value="dark">Dark</option>\
        <option value="light">Light</option>';

    return html;
}

function netbase_shortcode_contextual() {
    var html = '<option value="">None</option>\
        <option value="success">Success</option>\
        <option value="info">Info</option>\
        <option value="warning">Warning</option>\
        <option value="danger">Danger</option>';

    return html;
}
function netbase_shortcode_style() {

    var html = '<option value="default">Default</option>\
        <option value="catchild">Category_Child</option>';

    return html;

    }


jQuery(function($) {

    var form = jQuery('<div id="netbase_product_category-form"><table id="netbase_product_category-table" class="form-table">\
			<tr>\
				<th><label for="netbase_product_category-per_page">Per Page</label></th>\
                <td><input type="text" name="per_page" id="netbase_product_category-per_page" value="12" /></td>\
            </tr>\
			<tr>\
				<th><label for="netbase_product_category-orderby">Order by</label></th>\
                <td><select name="orderby" id="netbase_product_category-orderby">\
                ' + netbase_shortcode_products_orderby() + '\
				</select></td>\
            </tr>\
			<tr>\
				<th><label for="netbase_product_category-order">Order way</label></th>\
                <td><select name="order" id="netbase_product_category-order">\
                ' + netbase_shortcode_products_order() + '\
				</select></td>\
            </tr>\
			<tr>\
				<th><label for="netbase_product_category-category">Category Slugs</label></th>\
                <td><input type="text" name="category" id="netbase_product_category-category" value="" /></td>\
            </tr>\
            <tr>\
				<th><label for="netbase_product_category-el_class">Url Image Product sticky</label></th>\
				<td><input type="text" name="el_class" id="netbase_product_category-el_class" value="" /></td>\
			</tr>\
		</table>\
		<p class="submit">\
			<input type="button" id="netbase_product_category-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
		</p>\
		</div>');

    var table = form.find('table');
    form.appendTo('body').hide();

    form.find('#netbase_product_category-submit').click(function(){

        var options = {
            'title'              : '',
            'view'               : '',
            'per_page'           : '12',
            'columns'            : '4',
            'column_width'       : '',
            'orderby'            : '',
            'order'              : '',
            'category'           : '',
            'addlinks_pos'       : '',
            'navigation'         : 'true',
            'pagination'         : '',
            'animation_type'     : '',
            'animation_duration' : '1000',
            'animation_delay'    : '0',
            'el_class'           : ''
        };

        var shortcode = '[netbase_product_category';

        for( var index in options) {
            var value = table.find('#netbase_product_category-' + index).val();

            if ( value !== options[index] && (typeof value !== 'undefined'))
                shortcode += ' ' + index + '="' + value + '"';
        }

        shortcode += ']';

        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

        tb_remove();
    });
});



/*post loop*/

jQuery(function($) {

    var form = jQuery('<div id="netbase-post-loop-form"><table id="netbase-post-loop-table" class="form-table">\
			<tr>\
				<th><label for="netbase-post-loop-category">Category Slugs</label></th>\
                <td><input type="text" name="category" id="netbase-post-loop-category" value="" /></td>\
            </tr>\
		</table>\
		<p class="submit">\
			<input type="button" id="netbase-post-loop-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
		</p>\
		</div>');

    var table = form.find('table');
    form.appendTo('body').hide();

    form.find('#netbase-post-loop-submit').click(function(){

        var options = {
            'title'              : '',
            'view'               : '',
            'per_page'           : '12',
            'columns'            : '4',
            'column_width'       : '',
            'orderby'            : '',
            'order'              : '',
            'category'           : '',
            'addlinks_pos'       : '',
            'navigation'         : 'true',
            'pagination'         : '',
            'animation_type'     : '',
            'animation_duration' : '1000',
            'animation_delay'    : '0',
            'el_class'           : ''
        };

        var shortcode = '[netbase-post-loop';

        for( var index in options) {
            var value = table.find('#netbase-post-loop-' + index).val();

            if ( value !== options[index] && (typeof value !== 'undefined'))
                shortcode += ' ' + index + '="' + value + '"';
        }

        shortcode += ']';

        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

        tb_remove();
    });
});

/*end post loop*/

/*list products cat*/

jQuery(function($) {

    var form = jQuery('<div id="netbase_list_products_cat-form"><table id="netbase_list_products_cat-table" class="form-table">\
            <tr>\
                <th><label for="netbase_list_products_cat-per_page">Per Page</label></th>\
                <td><input type="text" name="per_page" id="netbase_list_products_cat-per_page" value="8" /></td>\
            </tr>\
            <tr>\
                <th><label for="netbase_list_products_cat-orderby">Order by</label></th>\
                <td><select name="orderby" id="netbase_list_products_cat-orderby">\
                ' + netbase_shortcode_products_orderby() + '\
                </select></td>\
            </tr>\
            <tr>\
                <th><label for="netbase_list_products_cat-order">Order way</label></th>\
                <td><select name="order" id="netbase_list_products_cat-order">\
                ' + netbase_shortcode_products_order() + '\
                </select></td>\
            </tr>\
            <tr>\
                <th><label for="netbase_list_products_cat-category">Category Slugs</label></th>\
                <td><input type="text" name="category" id="netbase_list_products_cat-category" value="" /></td>\
            </tr>\
            <tr>\
                <th><label for="netbase_list_products_cat-el_class">Url Image Product sticky</label></th>\
                <td><input type="text" name="el_class" id="netbase_list_products_cat-el_class" value="" /></td>\
            </tr>\
            <tr>\
                    <th><label for="netbase_list_products_cat-nbcarousel">Show Carousel</label></th>\
                    <td><select name="nbcarousel" id="netbase_list_products_cat-nbcarousel">\
                    ' + netbase_shortcode_boolean_true() + '\
                    </select></td>\
            </tr>\
            <tr>\
                <th><label for="netbase_list_products_cat-style">Style</label></th>\
                <td><select name="style" id="netbase_list_products_cat-style">\
                ' + netbase_shortcode_style() + '\
                </select></td>\
                </tr>\
        </table>\
        <p class="submit">\
            <input type="button" id="netbase_list_products_cat-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
        </p>\
        </div>');

    var table = form.find('table');
    form.appendTo('body').hide();

    form.find('#netbase_list_products_cat-submit').click(function(){

        var options = {
            'title'              : '',
            'view'               : '',
            'per_page'           : '8',
            'columns'            : '4',
            'column_width'       : '',
            'orderby'            : '',
            'order'              : '',
            'category'           : '',
            'addlinks_pos'       : '',
            'navigation'         : 'true',
            'pagination'         : '',
            'animation_type'     : '',
            'animation_duration' : '1000',
            'animation_delay'    : '0',
            'nbcarousel'         : '',
            'style'              :'',  
            'el_class'           : ''
        };

        var shortcode = '[netbase_list_products_cat';

        for( var index in options) {
            var value = table.find('#netbase_list_products_cat-' + index).val();

            if ( value !== options[index] && (typeof value !== 'undefined'))
                shortcode += ' ' + index + '="' + value + '"';
        }

        shortcode += ']';

        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

        tb_remove();
    });
});


/*end list products cat*/

jQuery(function($) {

    var form = jQuery('<div id="netbase_widget_woo_products-form"><table id="netbase_widget_woo_products-table" class="form-table">\
			<tr>\
				<th><label for="netbase_widget_woo_products-title">Title</label></th>\
                <td><input type="text" name="title" id="netbase_widget_woo_products-title" value="" /></td>\
            </tr>\
            <tr>\
				<th><label for="netbase_widget_woo_products-number">Number of products</label></th>\
				<td><input type="text" name="number" id="netbase_widget_woo_products-number" value="5" /></td>\
			</tr>\
			<tr>\
				<th><label for="netbase_widget_woo_products-show">Show</label></th>\
                <td><select name="show" id="netbase_widget_woo_products-show">\
                ' + netbase_shortcode_widget_products_show() + '\
				</select></td>\
            </tr>\
			<tr>\
				<th><label for="netbase_widget_woo_products-orderby">Order by</label></th>\
                <td><select name="orderby" id="netbase_widget_woo_products-orderby">\
                ' + netbase_shortcode_widget_products_orderby() + '\
				</select></td>\
            </tr>\
			<tr>\
				<th><label for="netbase_widget_woo_products-order">Order way</label></th>\
                <td><select name="order" id="netbase_widget_woo_products-order">\
                ' + netbase_shortcode_products_order() + '\
				</select></td>\
            </tr>\
			<tr>\
				<th><label for="netbase_widget_woo_products-hide_free">Hide free products</label></th>\
                <td><select name="hide_free" id="netbase_widget_woo_products-hide_free">\
                ' + netbase_shortcode_boolean_true() + '\
				</select></td>\
            </tr>\
			<tr>\
				<th><label for="netbase_widget_woo_products-show_hidden">Show hidden products</label></th>\
                <td><select name="show_hidden" id="netbase_widget_woo_products-show_hidden">\
                ' + netbase_shortcode_boolean_true() + '\
				</select></td>\
            </tr>\
			<tr>\
				<th><label for="netbase_widget_woo_products-animation_type">Animation Type</label></th>\
                <td><select name="animation_type" id="netbase_widget_woo_products-animation_type">\
                ' + netbase_shortcode_animation_type() + '\
				</select></td>\
            </tr>\
			<tr>\
				<th><label for="netbase_widget_woo_products-animation_duration">Animation Duration</label></th>\
				<td><input type="text" name="animation_duration" id="netbase_widget_woo_products-animation_duration" value="1000" />\
				<br/><small>numerical value (unit: milliseconds)</small></td>\
			</tr>\
			<tr>\
				<th><label for="netbase_widget_woo_products-animation_delay">Animation Delay</label></th>\
				<td><input type="text" name="animation_delay" id="netbase_widget_woo_products-animation_delay" value="0" />\
				<br/><small>numerical value (unit: milliseconds)</small></td>\
			</tr>\
            <tr>\
				<th><label for="netbase_widget_woo_products-el_class">Extra Class Name</label></th>\
				<td><input type="text" name="el_class" id="netbase_widget_woo_products-el_class" value="" /></td>\
			</tr>\
		</table>\
		<p class="submit">\
			<input type="button" id="netbase_widget_woo_products-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
		</p>\
		</div>');

    var table = form.find('table');
    form.appendTo('body').hide();

    form.find('#netbase_widget_woo_products-submit').click(function(){

        var options = {
            'title'              : '',
            'number'             : '5',
            'show'               : '',
            'orderby'            : '',
            'order'              : '',
            'hide_free'          : 'true',
            'show_hidden'        : 'true',
            'animation_type'     : '',
            'animation_duration' : '1000',
            'animation_delay'    : '0',
            'el_class'           : ''
        };

        var shortcode = '[netbase_widget_woo_products';

        for( var index in options) {
            var value = table.find('#netbase_widget_woo_products-' + index).val();

            if ( value !== options[index] && (typeof value !== 'undefined'))
                shortcode += ' ' + index + '="' + value + '"';
        }

        shortcode += ']';

        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

        tb_remove();
    });
});

