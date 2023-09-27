(function() {

    tinymce.create('tinymce.plugins.ShortcodeMce', {
        init : function(ed, url){
            tinymce.plugins.ShortcodeMce.theurl = url;
        },
        createControl : function(btn, e) {
            if ( btn == "netbase_shortcodes_button" ) {
                var a = this;
                var btn = e.createSplitButton('button', {
                    title: "Netbase Shortcodes",
                    image: tinymce.plugins.ShortcodeMce.theurl +"/shortcodes.png",
                    icons: false
                });
                btn.onRenderMenu.add(function (c, b) {
                    a.render( b, "Product Sticky", "netbase_product_category" );
                    a.render( b, "Post by Category", "netbase-post-loop" );
                    a.render( b, "List Products by Category", "netbase_list_products_cat" );                                      
                });

                return btn;
            }
            return null;
        },
        render : function(ed, title, id) {
            ed.add({
                title: title,
                onclick: function () {
                    netbase_shortcode_open(title, id);
                    return false;
                }
            })
        }

    });
    tinymce.PluginManager.add("shortcodes", tinymce.plugins.ShortcodeMce);

})();