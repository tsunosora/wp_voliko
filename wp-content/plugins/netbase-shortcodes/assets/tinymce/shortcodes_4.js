(function() {
    tinymce.PluginManager.add('shortcodes', function(editor, url) {
        editor.addButton('netbase_shortcodes_button', {
            type: 'menubutton',
            icon: 'netbase',
            tooltip: 'Netbase Shortcodes',
            menu: [                
                { text: 'Product Sticky', 
                value: 'netbase_product_category', 
                onclick: function() { netbase_shortcode_open(this.text(), this.value()); } 
                },
                { text: 'Post by Category', 
                value: 'netbase-post-loop', 
                onclick: function() { netbase_shortcode_open(this.text(), this.value()); } 
                },
                { text: 'List Products by Category', 
                value: 'netbase_list_products_cat', 
                onclick: function() { netbase_shortcode_open(this.text(), this.value()); } 
                }                
                
            ]
        });
    });

})();