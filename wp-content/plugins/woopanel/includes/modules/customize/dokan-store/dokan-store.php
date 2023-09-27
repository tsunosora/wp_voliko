<?php
class WooPanel_Customize_Dokan_Store {
	public $dokan_layout_slug = 'dokan-store';
	public $data = array();
	
	function __construct() {
		add_filter( 'woopanel_customize_settings', array($this, 'settings') );

        /**
         * Fires when scripts and styles are enqueued.
         *
         * @since 2.8.0
         * @hook woopanel_enqueue_scripts
         * @param null
         */
		add_action( 'woopanel_enqueue_scripts', array($this, 'woopanel_enqueue_scripts') );
		add_action( 'woopanel_init', array( $this, 'save') );
		add_filter( 'body_class', array( $this, 'body_classes' ) );

		add_filter( 'woopanel_frontend_inline_style', array($this, 'inline_style') );

		add_action('wp_enqueue_scripts', array($this, 'frontend_enqueue_scripts') );

		include_once NBT_CUSTOMIZE_PATH . 'dokan-store/dokan-store-header.php';
		include_once NBT_CUSTOMIZE_PATH . 'dokan-store/dokan-store-wc.php';

	}

	public function body_classes( $classes ) {
		global $current_user, $wp_query;

		if( isset( $wp_query->query['store'] ) ) {
			$settings = $this->settings();
			$store_user               = get_user_by('login', get_query_var( 'store' ) );
			$this->data = get_user_meta( $store_user->ID, 'customize_' . esc_attr($this->dokan_layout_slug), true );

			$layout = empty($this->data['layout_store']) ? 'left-sidebar' : $this->data['layout_store'];

	        $classes[] = 'woopanel-' . esc_attr($layout);
	    }

        return $classes;
	}


	public function settings() {
		return array(
			$this->dokan_layout_slug => array(
				'label' => esc_html__('Dokan Store', 'woopanel' ),
				'fields' => array(
					array(
						'id' => 'layout_store_panel',
						'type' => 'tab',
						'label' => esc_html__('Layout Store', 'woopanel' )
					),
					array(
						'id' => 'layout_store',
						'type' => 'radio-image',
						'label' => esc_html__('Layout Store', 'woopanel' ),
						'width' => 50,
						'height' => 45,
						'default' => 'left-sidebar',
						'options' => array(
							 array(
							 	'id' => 'left-sidebar',
								'label' => esc_html__('Left sidebar', 'woopanel' ),
								'image' => WOODASHBOARD_URL . 'assets/images/left-sidebar.png'
							),
							 array(
							 	'id' => 'right-sidebar',
								'label' => esc_html__('Right sidebar', 'woopanel' ),
								'image' => WOODASHBOARD_URL . 'assets/images/right-sidebar.png'
							),
							 array(
							 	'id' => 'full-width',
								'label' => esc_html__('Full width', 'woopanel' ),
								'image' => WOODASHBOARD_URL . 'assets/images/full-width.png'
							)
						),
						'form_inline' => true
					),
					array(
						'id' => 'sidebar_width',
						'type' => 'slider',
						'label' => esc_html__('Layout Sidebar Width', 'woopanel' ),
						'min' => 25,
						'max' => 50,
						'default' => 25,
						'form_inline' => true,
						'conditional_logic' => array(
							'layout_store' => array('left-sidebar', 'right-sidebar')
						),
						'units' => '%'
					),
					array(
						'id' => 'header_option_panel',
						'type' => 'tab',
						'label' => esc_html__('Header Store', 'woopanel' )
					),
					array(
						'id' => 'header_style',
						'type' => 'select',
						'label' => esc_html__('Header Style', 'woopanel' ),
						'default' => 'default',
						'form_inline' => true,
						'options' => array(
							'default' => esc_html__('Default', 'woopanel' ),
							'facebook' => esc_html__('Facebook', 'woopanel' ),
						)
					),
					array(
						'id' => 'header_default_position',
						'type' => 'select',
						'label' => esc_html__('Header Info Position', 'woopanel' ),
						'default' => 'default',
						'form_inline' => true,
						'options' => array(
							'left' => esc_html__('Left', 'woopanel' ),
							'right' => esc_html__('Right', 'woopanel' ),
							'top' => esc_html__('Top', 'woopanel' ),
							'bottom' => esc_html__('Bottom', 'woopanel' ),
						),
						'conditional_logic' => array(
							'header_style' => array('default')
						)
					),
					array(
						'id' => 'wc_option_panel',
						'type' => 'tab',
						'label' => esc_html__('WooCommerce Store', 'woopanel' )
					),
					array(
						'id' => 'woocommerce_enable_layout',
						'type' => 'switch',
						'label' => esc_html__('Show Gird/List layout', 'woopanel' ),
						'default' => true,
						'form_inline' => true
					),
					array(
						'id' => 'woocommerce_enable_filter',
						'type' => 'switch',
						'label' => esc_html__('Show Filter Product', 'woopanel' ),
						'default' => true,
						'form_inline' => true
					),
				)
			)
		);
	}


    public function save() {
    	global $current_user;

    	$settings = $this->settings();

    	if( isset($settings[$this->dokan_layout_slug]['fields']) ) {
    		$save = array();
    		foreach ($settings[$this->dokan_layout_slug]['fields'] as $key => $field) {
    			$field_id = $field['id'];
    			if( $field['type'] != 'tab' && isset($_POST[$field_id]) ) {
    				$save[$field_id] = $_POST[$field_id];
    				//enable_theme_store_sidebar
    			}
    		}

    		if( ! empty($save) ) {
    			update_user_meta( $current_user->ID, 'customize_' . esc_attr($this->dokan_layout_slug), $save );
    		}
    	}
    }

    public function inline_style( $css ) {
    	global $current_user;

    	$data = get_user_meta( $current_user->ID, 'customize_' . esc_attr($this->dokan_layout_slug), true );


    	if( isset($data['layout_store']) && $data['layout_store'] != 'full-width' ) {

    		$sidebar_width = $data['sidebar_width'];
    		$content_width = 100 - ($sidebar_width + 3);

    		$margin_sidebar = ($data['layout_store'] == 'right-sidebar') ? 'margin-left: 1.5%;' : 'margin-right: 1.5% !important;';
    		$margin_content = ($data['layout_store'] == 'right-sidebar') ? 'margin-right: 1.5%;' : 'margin-left: 1.5% !important;';

    		$css .= '.woopanel-' . esc_attr($data['layout_store']) .' .dokan-store-sidebar {
    			width: '. esc_attr($sidebar_width) .'%;
    			'.esc_attr($margin_sidebar).';
    		}
    		.woopanel-' . esc_attr($data['layout_store']) .' #dokan-primary {
			    max-width: inherit !important;
			    flex: inherit !important;
    			width: '. esc_attr($content_width) .'%;
    			'. esc_attr($margin_content).'
    		}';

    		/* Fix for multistore */
    		$inline_style_sidebar = $inline_style_content = null;
  
    		if( $data['layout_store'] == 'right-sidebar' ) {
    			$inline_style_content = 'order: 1;';
    			$inline_style_sidebar = 'flex: 0 0 '. esc_attr($sidebar_width) .'% !important; max-width: '. esc_attr($sidebar_width) .'% !important; order: 2; margin-left: 0 !important;';
    		}else {
    			$inline_style_sidebar = 'flex: 0 0 '. esc_attr($sidebar_width) .'% !important; max-width: '. esc_attr($sidebar_width) .'% !important;';
    		}

    		$css .= '.dokan-store.dk-has-sidebar .shop-main #dokan-primary {
    			width: 100%;
    		    margin-left: 0 !important;
    		    '.esc_attr($inline_style_content).'
    		}

    		.dokan-store.dk-has-sidebar #secondary {
    			width: auto;
			    margin-right: 0 !important;
			    '.esc_attr($inline_style_sidebar).'
			}';
    	}

    	return $css;
    }

	public function woopanel_enqueue_scripts() {
		wp_enqueue_script('jquery-ui-slider');
		wp_enqueue_style( 'woopanel-ui-slider', NBT_CUSTOMIZE_URL . 'assets/css/jquery-ui.css', false, '3.0.3', 'all' );
	}

	public function frontend_enqueue_scripts() {
		if( ! get_query_var('store') ) {
			return;
		}

		wp_enqueue_style('wpl-dokan-store', WOODASHBOARD_URL . 'assets/css/dokan-store.css', array(), '0.1.0', 'all');
		wp_enqueue_script( 'wpl-dokan-store', WOODASHBOARD_URL . 'assets/js/dokan-store.js', array(), '1.0.0', true );
	}

}

$GLOBALS['woopanel_dokan_store'] = new WooPanel_Customize_Dokan_Store();