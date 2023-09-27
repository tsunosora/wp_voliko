<?php
    /**
     * Theme Options Config
     */
    if ( ! class_exists( 'Printshop_Options_Config' ) ) {
        class Printshop_Options_Config {

            public $args = array();
            public $sections = array();
            public $theme;
            public $ReduxFramework;

            public function __construct() {

                if ( ! class_exists( 'ReduxFramework' ) ) {
                    return;
                }

                // This is needed. Bah WordPress bugs.  ;)
                if ( true == Redux_Helpers::isTheme( __FILE__ ) ) {
                    $this->initSettings();
                } else {
                    add_action( 'plugins_loaded', array( $this, 'initSettings' ), 10 );
                }

            }

            public function initSettings() {

                // Set the default arguments
                $this->setArguments();

                // Set a few help tabs so you can see how it's done
                $this->setHelpTabs();

                // Create the sections and fields
                $this->setSections();

                if ( ! isset( $this->args['opt_name'] ) ) { // No errors please
                    return;
                }

                $this->ReduxFramework = new ReduxFramework( $this->sections, $this->args );
            }

            public function setHelpTabs() {

                // Custom page help tabs, displayed using the help API. Tabs are shown in order of definition.
                $this->args['help_tabs'][] = array(
                    'id'      => 'redux-help-tab-1',
                    'title'   => esc_html__( 'Theme Information 1', 'printshop' ),
                    'content' => esc_html__( 'This is the tab content, HTML is allowed.', 'printshop' )
                );

                $this->args['help_tabs'][] = array(
                    'id'      => 'redux-help-tab-2',
                    'title'   => esc_html__( 'Theme Information 2', 'printshop' ),
                    'content' => esc_html__( 'This is the tab content, HTML is allowed.', 'printshop' )
                );

                // Set the help sidebar
                $this->args['help_sidebar'] = esc_html__( 'This is the sidebar content, HTML is allowed.', 'printshop' );
            }

            /**
             * All the possible arguments for Redux.
             * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
             * */
            public function setArguments() {

                $theme = wp_get_theme(); // For use with some settings. Not necessary.

                $this->args = array(
                    // TYPICAL -> Change these values as you need/desire
                    'opt_name'           => 'printshop_options',
                    // This is where your data is stored in the database and also becomes your global variable name.
                    'display_name'       => $theme->get( 'Name' ),
                    // Name that appears at the top of your panel
                    'display_version'    => false,
                    // Version that appears at the top of your panel
                    'menu_type'          => 'menu',
                    //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
                    'allow_sub_menu'     => true,
                    // Show the sections below the admin menu item or not
                    'menu_title'         => esc_html__( 'Theme Options', 'printshop' ),
                    'page_title'         => esc_html__( 'Theme Options', 'printshop' ),
                    // You will need to generate a Google API key to use this feature.
                    // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
                    'google_api_key'     => '',
                    // Must be defined to add google fonts to the typography module

                    'async_typography'   => false,
                    // Use a asynchronous font on the front end or font string
                    'admin_bar'          => true,
                    // Show the panel pages on the admin bar
                    'global_variable'    => 'printshop_option',
                    // Set a different name for your global variable other than the opt_name
                    'dev_mode'           => false,
                    // Show the time the page took to load, etc
                    'customizer'         => false,
                    // Enable basic customizer support

                    // OPTIONAL -> Give you extra features
                    'page_priority'      => null,
                    // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
                    'page_parent'        => 'themes.php',
                    // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
                    'page_permissions'   => 'manage_options',
                    // Permissions needed to access the options panel.
                    'menu_icon'          => '',
                    // Specify a custom URL to an icon
                    'last_tab'           => '',
                    // Force your panel to always open to a specific tab (by id)
                    'page_icon'          => 'icon-themes',
                    // Icon displayed in the admin panel next to your menu_title
                    'page_slug'          => 'wpnetbase_options',
                    // Page slug used to denote the panel
                    'save_defaults'      => true,
                    // On load save the defaults to DB before user clicks save or not
                    'default_show'       => false,
                    // If true, shows the default value next to each field that is not the default value.
                    'default_mark'       => '',
                    // What to print by the field's title if the value shown is default. Suggested: *
                    'show_import_export' => true,
                    // Shows the Import/Export panel when not used as a field.

                    // CAREFUL -> These options are for advanced use only
                    'transient_time'     => 60 * MINUTE_IN_SECONDS,
                    'output'             => true,
                    // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
                    'output_tag'         => true,
                    // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                    'footer_credit'     => ' ',                   
                    // Disable the footer credit of Redux. Please leave if you can help it.

                    // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
                    'database'           => '',
                    // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
                    'system_info'        => false,
                    // REMOVE

                    // HINTS
                    'hints'              => array(
                        'icon'          => 'icon-question-sign',
                        'icon_position' => 'right',
                        'icon_color'    => 'lightgray',
                        'icon_size'     => 'normal',
                        'tip_style'     => array(
                            'color'   => 'light',
                            'shadow'  => true,
                            'rounded' => false,
                            'style'   => '',
                        ),
                        'tip_position'  => array(
                            'my' => 'top left',
                            'at' => 'bottom right',
                        ),
                        'tip_effect'    => array(
                            'show' => array(
                                'effect'   => 'slide',
                                'duration' => '500',
                                'event'    => 'mouseover',
                            ),
                            'hide' => array(
                                'effect'   => 'slide',
                                'duration' => '500',
                                'event'    => 'click mouseleave',
                            ),
                        ),
                    )
                );

                // Panel Intro text -> before the form
                if ( ! isset( $this->args['global_variable'] ) || $this->args['global_variable'] !== false ) {
                    if ( ! empty( $this->args['global_variable'] ) ) {
                        $v = $this->args['global_variable'];
                    } else {
                        $v = str_replace( '-', '_', $this->args['opt_name'] );
                    }
                }

            }

            public function setSections() {


                /*--------------------------------------------------------*/
                /* GENERAL SETTINGS
                /*--------------------------------------------------------*/


                $this->sections[] = array(
                    'title'  => esc_html__( 'General', 'printshop' ),
                    'desc'   => '',
                    'icon'   => 'el-icon-cog el-icon-large',
                    'submenu' => true, // Setting submenu to false on a given section will hide it from the WordPress sidebar menu!
                    'fields' => array(

                        array(
                            'id'       =>'site_logo',
                            'url'      => false,
                            'type'     => 'media', 
                            'title'    => esc_html__('Site Logo', 'printshop'),
                            'default'  => array( 'url' => get_stylesheet_directory_uri() .'/images/logo.png' ),
                            'subtitle' => esc_html__('Upload your logo here.', 'printshop'),
                        ),
                        array(
                            'id'       =>'footer_logo',
                            'url'      => false,
                            'type'     => 'media', 
                            'title'    => esc_html__('Footer Logo', 'printshop'),
                            'default'  => array( 'url' => get_stylesheet_directory_uri() .'/images/logo-footer.png' ),
                            'subtitle' => esc_html__('Upload your logo here.', 'printshop'),
                        ),
                        array(
                            'id'       =>'site_logo_retina',
                            'url'      => false,
                            'type'     => 'media', 
                            'title'    => esc_html__('Site Logo Retina', 'printshop'),
                            'default'  => '',
                            'subtitle' => esc_html__('Upload at exactly 2x the size of your standard logo (optional), the name should include @2x at the end, example logo@2x.png', 'printshop'),
                        ),
                        array(
                            'id'       =>'site_transparent_logo',
                            'url'      => false,
                            'type'     => 'media', 
                            'title'    => esc_html__('Transparent Logo', 'printshop'),
                            'default'  => array( 'url' => get_stylesheet_directory_uri() .'/images/logo_transparent.png' ),
                            'subtitle' => esc_html__('Upload your transparent logo here, transparent logo display on a transparent header when applicable.', 'printshop'),
                        ),
                        array(
                            'id'       =>'site_transparent_logo_retina',
                            'url'      => false,
                            'type'     => 'media', 
                            'title'    => esc_html__('Transparent Logo Retina', 'printshop'),
                            'default'  => '',
                            'subtitle' => esc_html__('Upload at exactly 2x the size of your transparent logo (optional).', 'printshop'),
                        ),
                        array(
                            'id'             => 'logo_margin',
                            'type'           => 'spacing',
                            'output'         => array('.site-header .site-branding'),
                            'mode'           => 'margin',
                            'units'          => array('px'),
                            'units_extended' => 'false',
                            'title'          => esc_html__('Logo Margin', 'printshop'),
                            'subtitle'       => '',
                            'desc'           => esc_html__('Set your logo margin in px. ee.g. 20', 'printshop'),
                            'default'        => array(
                                'margin-top'     => '0px', 
                                'margin-right'   => '0px', 
                                'margin-bottom'  => '0px', 
                                'margin-left'    => '0px',
                                'units'          => 'px', 
                            )

                        ),
                        array(
                            'id'       =>'site_favicon',
                            'url'      => false,
                            'type'     => 'media', 
                            'title'    => esc_html__('Site Favicon', 'printshop'),
                            'default'  => '',
                            'subtitle' => esc_html__('Upload a 16px x 16px .png or .gif image that will be your favicon.', 'printshop'),
                        ),
                        array(
                            'id'       =>'site_iphone_icon',
                            'url'      => false,
                            'type'     => 'media', 
                            'title'    => esc_html__('Apple iPhone Icon ', 'printshop'),
                            'default'  => '',
                            'subtitle' => esc_html__('Custom iPhone icon (57px x 57px).', 'printshop'),
                        ),
                        
                        array(
                            'id'       =>'site_iphone_icon_retina',
                            'url'      => false,
                            'type'     => 'media', 
                            'title'    => esc_html__('Apple iPhone Retina Icon ', 'printshop'),
                            'default'  => '',
                            'subtitle' => esc_html__('Custom iPhone retina icon (114px x 114px).', 'printshop'),
                        ),
                        
                        array(
                            'id'       =>'site_ipad_icon',
                            'url'      => false,
                            'type'     => 'media', 
                            'title'    => esc_html__('Apple iPad Icon ', 'printshop'),
                            'default'  => '',
                            'subtitle' => esc_html__('Custom iPad icon (72px x 72px).', 'printshop'),
                        ),
                        
                        array(
                            'id'       =>'site_ipad_icon_retina',
                            'url'      => false,
                            'type'     => 'media', 
                            'title'    => esc_html__('Apple iPad Retina Icon ', 'printshop'),
                            'default'  => '',
                            'subtitle' => esc_html__('Custom iPad retina icon (144px x 144px).', 'printshop'),
                        ),
                        array(
                            'id'       => 'page_comments',
                            'type'     => 'switch',
                            'title'    => esc_html__('Enable Page Comments?', 'printshop'),
                            'subtitle' => esc_html__('Do you want to enable comments on single page?', 'printshop'),
                            'default'  => false,
                        ),
                        array(
                            'id'       => 'page_back_totop',
                            'type'     => 'switch',
                            'title'    => esc_html__('Enable Back To Top Button?', 'printshop'),
                            'subtitle' => esc_html__('Do you want to enable back to top button?', 'printshop'),
                            'default'  => true,
                        ),
                         array(
                            'id'       => 'mailchimp-api',
                            'type'     => 'text',
                            'title'    => __( 'Your mailchimp API','printshop' ),
                            'default'  => '1fd2b95bf348e191a3c69658abbabe87-us14',
                            'subtitle' => __( 'Grab an API Key from <a href="http://admin.mailchimp.com/account/api/" target="_blank">here</a>.','printshop' ),                            
                        ),
                        array(
                            'id'        => 'mailchimp-groupid',
                            'type'      => 'text',
                            'title'     => __( 'Your group id','printshop' ),
                            'default'  => 'b7f4b79202',
                            'subtitle'  => __( 'Grab your List\'s Unique Id by going <a href="http://admin.mailchimp.com/lists/" target="_blank">here</a>.<br> Click the "settings" link for the list - the Unique Id is at the bottom of that page.','printshop' ),
                        ),
                    )
                );
                

                /*--------------------------------------------------------*/
                /* LAYOUTS
                /*--------------------------------------------------------*/
                $this->sections[] = array(
                    'title'  => esc_html__( 'Layout', 'printshop' ),
                    'desc'   => '',
                    'icon'   => 'el-icon-website el-icon-large',
                    'submenu' => true,
                    'fields' => array(
                        array(
                            'id'       => 'site_boxed',
                            'type'     => 'switch',
                            'title'    => esc_html__('Boxed Version?', 'printshop'),
                            'subtitle' => esc_html__('Do you want to enable boxed layout?', 'printshop'),
                            'default'  => false,
                        ),
                        array(
                            'id'       => 'page_layout',
                            'type'     => 'button_set',
                            'title'    => esc_html__( 'Page Layout', 'printshop' ),
                            'subtitle' => esc_html__( 'Default page layout.', 'printshop' ),
                            'options'  => array(
                                'left-sidebar'  => 'Left Sidebar',
                                'no-sidebar'    => 'No Sidebar',
                                'right-sidebar' => 'Right Sidebar'
                            ),
                            'default'  => 'right-sidebar'
                        ),
                        array(
                            'id'       => 'archive_layout',
                            'type'     => 'button_set',
                            'title'    => esc_html__( 'Archive Layout', 'printshop' ),
                            'subtitle' => esc_html__( 'Default archive layout ( front page, category, tag, search, author, archive ).', 'printshop' ),
                            'options'  => array(
                                'left-sidebar'  => 'Left Sidebar',
                                'no-sidebar'    => 'No Sidebar',
                                'right-sidebar' => 'Right Sidebar'
                            ),
                            'default'  => 'right-sidebar'
                        ),
                        array(
                            'id'       => 'blog_layout',
                            'type'     => 'button_set',
                            'title'    => esc_html__( 'Blog Layout', 'printshop' ),
                            'subtitle' => esc_html__( 'Set your blog layout to display, include blog page and single blog post.', 'printshop' ),
                            'options'  => array(
                                'left-sidebar'  => 'Left Sidebar',
                                'no-sidebar'    => 'No Sidebar',
                                'right-sidebar' => 'Right Sidebar'
                            ),
                            'default'  => 'right-sidebar'
                        ),
                        array(
                            'id'       => 'single_shop_layout',
                            'type'     => 'button_set',
                            'title'    => esc_html__( 'Single WooCommerce Product Layout', 'printshop' ),
                            'subtitle' => esc_html__( 'Layout for single product and products archive.', 'printshop' ),
                            'options'  => array(
                                'left-sidebar'  => 'Left Sidebar',
                                'no-sidebar'    => 'No Sidebar',
                                'right-sidebar' => 'Right Sidebar'
                            ),
                            'default'  => 'right-sidebar'
                        ),
                    )
                );

                /*--------------------------------------------------------*/
                /* HEADER
                /*--------------------------------------------------------*/

                $this->sections[] = array(
                    'title'  => esc_html__( 'Header', 'printshop' ),
                    'desc'   => '',
                    'icon'   => 'el-icon-file',
                    'submenu' => true,
                    'fields' => array(

                        array(
                            'id'       => 'header_fixed',
                            'type'     => 'switch',
                            'title'    => esc_html__('Enable fixed header on scroll.', 'printshop'),
                            'default'  => false,
                        ),
                        array(
                            'id'       => 'header_bg_transparent',
                            'type'     => 'switch',
                            'title'    => esc_html__('Enable bacground transparent', 'printshop'),
                            'default'  => false,
                        ),
                        array(
                            'id'       => 'header_style',
                            'type'     => 'button_set',
                            'title'    => esc_html__( 'Header Layout', 'printshop' ),
                            'subtitle' => esc_html__( 'Select your header layout', 'printshop' ),
                            'options'  => array(
                                'header-default'  => 'Header Default',
                                'logoleft'   => 'Header logo left',
                                'centered' => 'Header Centered',
                                'menubottom' => 'Header Menu Bottom',
                                'creativeleft' => 'Left Always Open',
                                'creativeright' => 'Right Always Open',
                                'banner' => 'Banner Center'
                            ),
                            'default'  => 'header-default'
                        ),

                        array(
                            'id'       => 'header_topbar_info',
                            'type'     => 'info',
                            'style'    => 'warning',
                            'title'    => esc_html__('Header Topbar Setup Guide', 'printshop'),
                            'desc'     => esc_html__('You had selected Header Topbar style. In order to display top bar elements please go to Widget page and look for TopBar Left / TopBar Right widget areas.', 'printshop'),
                            'required' => array('header_style','=','topbar', ),
                        ),
                        array(
                            'id'       => 'header_centered_info',
                            'type'     => 'info',
                            'style'    => 'warning',
                            'title'    => esc_html__('Header Centered Setup Guide', 'printshop'),
                            'desc'     => esc_html__('You had selected Header Centered style. In order to display top bar elements please go to Widget page and look for TopBar Left / TopBar Right widget areas.', 'printshop'),
                            'required' => array('header_style','=','centered', ),
                        ),

                       
                        array(
                            'id'       => 'extract_1_value',
                            'type'     => 'text',
                            'title'    => esc_html__('Phone Value', 'printshop'),
                            'subtitle' => '',
                            'desc'     => "",
                            'default'  => "1.800.123.4567",
                            //'required' => array('header_style','=','header-default', ),
                        ),

                        array(
                            'id'       => 'extras_value_color',
                            'type'     => 'color',
                            'compiler' => true,
                            'title'    => esc_html__('Header Extract Value Color', 'printshop'),
                            'desc'     => "Change color for extract value elements above, the color will be inherit from primary color by default.",
                            'default'  => '',
                            'output'   => array(
                                'color'             => '#masthead .header-right-wrap .extract-element .phone-text'
                            ),
                            'required' => array('header_style','=','header-default', ),
                        ),

						array(
                            'id'       => 'hide_header_topbar',
                            'type'     => 'switch',
                            'title'    => esc_html__('Hide Header Topbar ?.', 'printshop'),
                            'default'  => false,
                        ),
                        
                        array(
                            'id'       => 'topbar_custom_style',
                            'type'     => 'switch',
                            'title'    => esc_html__('Custom Topbar Style?.', 'printshop'),
                            'default'  => false,
                        	'required' => array('hide_header_topbar','=',false, ),
                        ),
                        array(
                            'id'       => 'topbar_background',
                            'type'     => 'background',
                            'compiler' => true,
                            'output'   => array('.site-topbar'),
                            'title'    => esc_html__('Topbar Background', 'printshop'),
                            'desc'     => '',
                            'required' => array('topbar_custom_style','=',true, ),
                            'default'  => array(
                            ),
                        ),
                        array(
                            'id'       => 'topbar_color',
                            'type'     => 'color',
                            'compiler' => true,
                            'title'    => esc_html__('Topbar Text Color', 'printshop'),
                            'required' => array('topbar_custom_style','=',true, ),
                            'default'  => '',
                            'output'   => array(
                                'color'             => '.site-topbar, .extract-element a,
                                .header-right-widgets .currency h3.widget-title,
                                .woocommerce-currency-switcher-form a span'
                            )
                        ),
                        array(
                            'id'       => 'topbar_link_color',
                            'type'     => 'color',
                            'compiler' => true,
                            'title'    => esc_html__('Topbar Link Color', 'printshop'),
                            'required' => array('topbar_custom_style','=',true, ),
                            'default'  => '',
                            'output'   => array(
                                'color'             => '.site-topbar .widget a'
                            )
                        ),
                        array(
                            'id'       => 'topbar_link_hover_color',
                            'type'     => 'color',
                            'compiler' => true,
                            'title'    => esc_html__('Topbar Link Hover Color', 'printshop'),
                            'required' => array('topbar_custom_style','=',true, ),
                            'default'  => '',
                            'output'   => array(
                                'color'             => '.site-topbar a:hover,.site-topbar a:hover i'
                            )
                        ),
                        array(
                            'id'       => 'topbar_primary_color',
                            'type'     => 'color',
                            'compiler' => true,
                            'title'    => esc_html__('Topbar Primary Color', 'printshop'),
                            'required' => array('topbar_custom_style','=',true, ),
                            'default'  => '',
                            'output'   => array(
                                'color'             => '.site-topbar .primary-color'
                            )
                        ),
                        array(
                            'id'       => 'topbar_border_color',
                            'type'     => 'color',
                            'compiler' => true,
                            'title'    => esc_html__('Topbar Border Color', 'printshop'),
                            'required' => array('topbar_custom_style','=',true, ),
                            'default'  => '',
                            'output'   => array(
                                'border-color' => '.site-topbar, .site-topbar .topbar-left .topbar-widget,
                                .site-topbar .topbar-left .topbar-widget:first-child,.site-topbar .topbar-right .topbar-widget,
                                .site-topbar .topbar-right .topbar-widget:first-child,.site-topbar .search-form .search-field'
                            )
                        ),


                        array(
                            'id'       =>'divider_2',
                            'desc'     => '',
                            'required' => array('topbar_custom_style','=',true, ),
                            'type'     => 'divide'
                        ),


                        array(
                            'id'       => 'header_custom_style',
                            'type'     => 'switch',
                            'title'    => esc_html__('Custom Header Style?.', 'printshop'),
                            'default'  => false,
                        ),
                        array(
                            'id'       => 'header_background',
                            'type'     => 'background',
                            'compiler' => true,
                            'output'   => array('.site-header.fixed-on,.site-header .header-wrap 
                            '),
                            'title'    => esc_html__('Header Background', 'printshop'),
                            'desc'     => '',
                            'required' => array('header_custom_style','=',true, ),
                            'default'  => array(
                            ),
                        ),
                        array(
                            'id'       => 'header_background_link',
                            'type'     => 'text',
                            'title'    => esc_html__('Header Background Link', 'printshop'),
                            'subtitle' => '',
                            'desc'     => "",
                            'default'  => "",
                            'required' => array('header_custom_style','=',true, ),
                        ),
                        array(
                            'id'       => 'header_color',
                            'type'     => 'color',
                            'compiler' => true,
                            'title'    => esc_html__('Header Color', 'printshop'),
                            'required' => array('header_custom_style','=',true, ),
                            'default'  => '',
                            'output'   => array(
                                'color'             => 'form.search-form::after, #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item > a.mega-menu-link, .header-cart-search .cart-contents, .header-right-wrap, .site-header .header-right-wrap .header-social a i',
                                
                                'border-color'      => '.site-header .header-right-wrap .header-social a i'

                            )
                        ),
                         
                        
                    )
                );

                /*--------------------------------------------------------*/
                /* PRIMARY MENU
                /*--------------------------------------------------------*/
                $this->sections[] = array(
                    'title'  => esc_html__( 'Primary Menu', 'printshop' ),
                    'desc'   => '',
                    'icon'   => 'el-icon-credit-card',
                    'submenu' => true,
                    'fields' => array(
                        array(
                            'id'             =>'primary_menu_typography',
                            'type'           => 'typography', 
                            'title'          => esc_html__('Primary Menu Typography', 'printshop'),
                            'compiler'       =>true,
                            'google'         =>true,
                            'font-backup'    =>false,
                            'text-align'     =>false,
                            'text-transform' =>true,
                            'font-weight'    =>true,
                            'all_styles'     =>false,
                            'font-style'     =>true,
                            'subsets'        =>true,
                            'font-size'      =>true,
                            'line-height'    =>false,
                            'word-spacing'   =>false,
                            'letter-spacing' =>true,
                            'color'          =>true,
                            'preview'        =>true,
                            'output'         => array('.wpc-menu a'),
                            'units'          =>'px',
                            'subtitle'       => esc_html__('Custom typography for primary menu.', 'printshop'),
                            'default'        => array(
                            )
                        ),
                    )
                );

                /*--------------------------------------------------------*/
                /* PAGE
                /*--------------------------------------------------------*/
                $this->sections[] = array(
                    'title'  => esc_html__( 'Page', 'printshop' ),
                    'desc'   => '',
                    'icon'   => 'el-icon-file-new',
                    'submenu' => true,
                    'fields' => array(
                        
                        array(
                            'id'       => 'page_title_bg',
                            'type'     => 'background',
                            'compiler' => true,
                            'output'   => array('.page-title-wrap'),
                            'title'    => esc_html__('Page Title Background', 'printshop'),
                            'desc'     => 'Apply for page title, note that page title is different with Page Header which will get setting from single page.',
                            'default'  => array(
                                'background-color' => '#f8f9f9',
                            ),
                        ),
                        array(
                            'id'       => 'page_title_color',
                            'type'     => 'color',
                            'compiler' => true,
                            'output'   => array('.page-title-wrap h1'),
                            'title'    => esc_html__('Page Title Custom Color', 'printshop'),
                            'desc'     => 'By default page title color will inherit from Heading color settings.',
                            'default'  => ''
                        ),
                        array(
                            'id'       => 'page_title_contact',
                            'type'     => 'switch',
                            'title'    => esc_html__('Page Title Button', 'printshop'),
                            'desc'     => 'Enable contact button at the right of page title area.',
                            'default'  => false,
                        ),
                    )
                );

                
                /*--------------------------------------------------------*/
                /* STYLING
                /*--------------------------------------------------------*/
                $this->sections[] = array(
                    'title'  => esc_html__( 'Styling', 'printshop' ),
                    'desc'   => '',
                    'icon'   => 'el-icon-tint',
                    'submenu' => true,
                    'fields' => array(
                        array(
                            'id'       => 'primary_color',
                            'type'     => 'color',
                            'compiler' => true,
                            'title'    => esc_html__('Primary Color Schema', 'printshop'),
                            'default'  => '#25BCE9',
                            'output'   => array(
                                'color' => '
                                .single-product .product_meta a:hover, #tab-faq_tab .panel-title a:hover,
                                .sr-price span, .nbtsow-products-wrap .product-price,
                                .heading-404, .error-action, .ywar_review_row span, div.bwl_acc_container h2.acc_trigger a:hover, div.bwl_acc_container h2.acc_trigger.active a,
                                .widget_wpnetbase_contact_info_widget .contact-info ul.info li:hover,                               
                                .icon-star-headline .so-widget-wpnetbase-sow-headline .decoration:after,
                                .error-box h3, .error-box #calendar_wrap caption, #calendar_wrap .error-box caption ,
                                .woocommerce ul.products li.product .product-content-top:hover .price span, 
                                .woocommerce ul.products li.product .compare.button.added:hover,
                                .shop_table.cart tbody tr td.product-name a:hover,
                                .woocommerce .woocommerce-info:before,
                                .nbt-accordion-menu.accordion-nav ul li.active > a, 
                                .nbt-accordion-menu.accordion-nav ul li .fa-minus,
                                .header-right-cart-search form.search-form:hover:after,
                                .header-right-cart-search .cart-contents:hover:before,
                                .woocommerce ul.products li.product .price ins .amount:hover,
                                .woocommerce ul.products li.product .price > .amount:hover,
                                a, .primary-color, .wpc-menu a:hover, .wpc-menu > li.current-menu-item > a, .wpc-menu > li.current-menu-ancestor > a,
                                #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item.mega-toggle-on > a.mega-menu-link, #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item > a.mega-menu-link:hover, #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item > a.mega-menu-link:focus, 
                                .entry-footer .post-categories li a:hover, .entry-footer .post-tags li a:hover, h3.widget-title,
                                .heading-404, .grid-item .grid-title a:hover, .widget a:hover, .widget #calendar_wrap a, .widget_recent_comments a,
                                #secondary .widget.widget_nav_menu ul li a:hover, #secondary .widget.widget_nav_menu ul li li a:hover, #secondary .widget.widget_nav_menu ul li li li a:hover,
                                #secondary .widget.widget_nav_menu ul li.current-menu-item a, .woocommerce ul.products li.product .price, .woocommerce .star-rating,
                                .iconbox-wrapper .iconbox-icon .primary, .iconbox-wrapper .iconbox-image .primary, .iconbox-wrapper a:hover,
                                .breadcrumbs a:hover, #comments .comment .comment-wrapper .comment-meta .comment-time:hover, #comments .comment .comment-wrapper .comment-meta .comment-reply-link:hover, #comments .comment .comment-wrapper .comment-meta .comment-edit-link:hover,
                                .nav-toggle-active i, .header-transparent .header-right-wrap .extract-element .phone-text, .site-header .header-right-wrap .extract-element .phone-text,
                                .wpb_wrapper .wpc-projects-light .esg-navigationbutton:hover, .wpb_wrapper .wpc-projects-light .esg-filterbutton:hover,.wpb_wrapper .wpc-projects-light .esg-sortbutton:hover,.wpb_wrapper .wpc-projects-light .esg-sortbutton-order:hover,.wpb_wrapper .wpc-projects-light .esg-cartbutton-order:hover,.wpb_wrapper .wpc-projects-light .esg-filterbutton.selected,
                                .wpb_wrapper .wpc-projects-dark .esg-navigationbutton:hover, 
                                .wpb_wrapper .wpc-projects-dark .esg-filterbutton:hover, 
                                .wpb_wrapper .wpc-projects-dark .esg-sortbutton:hover,
                                .wpb_wrapper .wpc-projects-dark .esg-sortbutton-order:hover,.wpb_wrapper .wpc-projects-dark .esg-cartbutton-order:hover, .wpb_wrapper .wpc-projects-dark .esg-filterbutton.selected,
                                .right-sidebar #primary .entry-header .entry-title a:hover, .left-sidebar #primary .entry-header .entry-title a:hover,
                                .swiper-button-next:after, .swiper-button-prev:after, 
                                .no-sidebar #primary .entry-header .entry-title a:hover',                           
                                'background-color'  => ' .subcriber-widget button.btn, .wpnetbase_perc_rating,
                                .widget-woocommerce-currency-switcher .chosen-container .chosen-results li.highlighted,
                                .woocommerce ul.products li.product .product-content-top .product-content-info > a.product_type_simple:hover,
                                .woocommerce ul.products li.product .product-content-top .product-content-info > a.product_type_variable:hover,
                                .woocommerce ul.products li.product .product-content-top .product-content-info > a.product_type_simple span,
                                .woocommerce ul.products li.product .product-content-top .product-content-info > a.product_type_variable span,
                                	ul.products .product-content-top .product-content-info .gridlist-buttonwrap > a.button:hover,
                                	ul.products .product-content-top .product-content-info .gridlist-buttonwrap > a.button:hover span,                                 
                                	.header-right-widgets .language aside.widget ul li:hover,
                                	ul.products li.product .product-content-top .product-content-info .yith-wcwl-add-to-wishlist:hover,
                                	ul.products li.product .product-content-top .product-content-info .yith-wcwl-add-to-wishlist .yith-wcwl-add-button a span ,
                                    .yith-wcwl-wishlistexistsbrowse span,.yith-wcwl-wishlistaddedbrowse a span,
                                    .header-right-cart-search .header-cart-search .cart-contents span,             
                                    .woocommerce ul.products li.product .product-content-top .product-content-info .yith-wcwl-add-to-wishlist:hover,
                                    .header-wrap-top,#btt,.currency .currency-sel .chosen-container .chosen-drop ul li.highlighted ,
                                    .header-right-widgets .language aside.widget ul li:hover,.footer-columns .widget-title::after,
                                    .woocommerce ul.products li.product .product-content-top .product-content-info .yith-wcwl-add-to-wishlist .yith-wcwl-add-button a span,
                                    .woocommerce ul.products li.product a.add_to_cart_button:hover,
	                                .woocommerce ul.products li.product a.add_to_cart_button span,
	                                .woocommerce ul.products li.product a.yith-wcqv-button span,
	                                .woocommerce ul.products li.product a.yith-wcqv-button:hover,
	                                .woocommerce ul.products li.product a.compare:hover,
	                                .woocommerce ul.products li.product a.compare span,
	                                .woocommerce ul.products li.product .yith-wcwl-add-to-wishlist .yith-wcwl-add-button a span,
	                                 .header-right-cart-search .header-cart-search .widget_shopping_cart_content p.buttons a,	                                
	                                .woocommerce #respond input#submit:hover, .woocommerce button.button:hover, .woocommerce input.button:hover,						
	                                .woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, input[type="reset"], input[type="submit"], input[type="submit"], .wpc-menu ul li a:hover,
                                    .wpc-menu ul li.current-menu-item > a, .loop-pagination a:hover, .loop-pagination span:hover,
                                    .loop-pagination a.current, .loop-pagination span.current, .footer-social, .tagcloud a:hover, woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt,
                                    .woocommerce #respond input#submit.alt:hover, .woocommerce #respond input#submit.alt:focus, .woocommerce #respond input#submit.alt:active, .woocommerce a.button.alt:hover, .woocommerce a.button.alt:focus, .woocommerce a.button.alt:active, .woocommerce button.button.alt:hover, .woocommerce button.button.alt:focus, .woocommerce button.button.alt:active, .woocommerce input.button.alt:hover, .woocommerce input.button.alt:focus, .woocommerce input.button.alt:active,
                                    .woocommerce span.onsale, .entry-content .wpb_content_element .wpb_tour_tabs_wrapper .wpb_tabs_nav li.ui-tabs-active a, .entry-content .wpb_content_element .wpb_accordion_header li.ui-tabs-active a,
                                    .entry-content .wpb_content_element .wpb_accordion_wrapper .wpb_accordion_header.ui-state-active a,
                                    .btn, .btn:hover, .btn-primary, .custom-heading .heading-line, .custom-heading .heading-line.primary,
                                      .wpb_wrapper .eg-wpc_projects-element-1,
                                      .swiper-pagination-bullet-active,
                                      .paging-navigation .loop-pagination a:hover,
                                      .dokan-store-footer:before',                                
                                'border-color'      => ' 
                                	li:hover .nbt-related-thumb,
                                    .woocommerce form.checkout .woocommerce-shipping-fields p .input-text:hover,
                                    ul.products .product-content-info a.add_to_cart_button span::after,                                    
                                    .woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button,textarea:focus, input[type="date"]:focus, input[type="datetime"]:focus, input[type="datetime-local"]:focus, input[type="email"]:focus, input[type="month"]:focus, input[type="number"]:focus, input[type="password"]:focus, input[type="search"]:focus, input[type="tel"]:focus, input[type="text"]:focus, input[type="time"]:focus, input[type="url"]:focus, input[type="week"]:focus,
                                    .entry-content blockquote, .woocommerce ul.products li.product a img:hover, .woocommerce div.product div.images img:hover,
                                    .dokan-store-thumbnail .dokan-btn.dokan-btn-theme',
                        		'border-top-color' =>' .header-search .wpnetbase_asl_container,
                                    #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-megamenu > ul.mega-sub-menu, #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-flyout > ul.mega-sub-menu,
                                    #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-megamenu > ul.mega-sub-menu:after, #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-flyout > ul.mega-sub-menu:after,
                                    .woocommerce .woocommerce-info,
                                    .yith-wcwl-wishlistexistsbrowse a span:after,
                                    .yith-wcwl-wishlistaddedbrowse a span:after,
                                    .woocommerce ul.products li.product .product-content-top .product-content-info a.add_to_cart_button span::after,
                                    .woocommerce ul.products li.product .product-content-top .product-content-info a.product_type_variable span::after,
                                    .woocommerce ul.products li.product .yith-wcwl-add-to-wishlist .yith-wcwl-add-button a span::after,
                        			.woocommerce ul.products li.product a.add_to_cart_button span::after,
                        			.woocommerce ul.products li.product a.compare span::after,
                        			.woocommerce ul.products li.product a.yith-wcqv-button span::after,
                                    .woocommerce ul.products li.product .compare span::after,
                                    .woocommerce ul.products li.product .yith-wcqv-button span::after, 
                                    .woocommerce ul.products li.product .product-content-top .product-content-info .yith-wcwl-add-to-wishlist .yith-wcwl-add-button a span::after,
 								    .header-right-cart-search form.search-form label, .header-right-cart-search .header-cart-search .widget_shopping_cart_content ul',
                        		'border-bottom-color'=>' 
                                    .header-search .wpnetbase_asl_container:before,                       
                                    .header-right-cart-search form.search-form label::before,
                                    .header-right-cart-search .header-cart-search .widget_shopping_cart_content ul:after',
                                'border-left-color' => '#secondary .widget.widget_nav_menu ul li.current-menu-item a:before'
                            )
                        ),

                        array(
                            'id'       => 'secondary_color',
                            'type'     => 'color',
                            'compiler' => true,
                            'title'    => esc_html__('Secondary Color Schema', 'printshop'),
                            'default'  => '#25BCE9',
                            'output'   => array(
                                'color'            => '.secondary-color,
                                    .shop_table.cart tbody tr td.product-subtotal span, 
                                    .single-product #primary .summary .product_title:hover,li.product a h3:hover, 
                                    .single-product #primary .woocommerce-tabs ul.tabs li.active a,
                                    .iconbox-wrapper .iconbox-icon .secondary, .iconbox-wrapper .iconbox-image .secondary',
								'border-bottom-color' =>'
                                    .single-product #primary .woocommerce-tabs ul.tabs li.active:after, ul.wpnb-brand-carousel li .wb-car-item-cnt:before
                                 ',
                                'border-top-color' =>'ul.wpnb-brand-carousel li .wb-car-item-cnt:before', 
                                'border-left-color' =>'ul.wpnb-brand-carousel li .wb-car-item-cnt:after',   
                                'border-right-color' =>'ul.wpnb-brand-carousel li .wb-car-item-cnt:after', 
                                'border-color' =>'.owl-carousel .owl-dots .owl-dot.active',   
                                'background-color' => '
                                    .owl-carousel .owl-dots .owl-dot.active,
                                    ul.products.list li.product .product-list-content-info .gridlist-buttonwrap > a.button,
                                    .woocommerce ul.products.list li.product .product-list-content-info .gridlist-buttonwrap .yith-wcwl-add-to-wishlist, 
                                    .btn-secondary, .custom-heading .heading-line.secondary,
                                    .widget_wpnetbase_social_media_widget a.nbt-social-media-icon:hover'
                            )
                        ),

                        array(
                            'id'       => 'button_color',
                            'type'     => 'color',
                            'compiler' => true,
                            'title'    => esc_html__('Button Background Color', 'printshop'),
                            'default'  => '#515151',
                            'output'   => array(
                                'background-color'            => '
                                .nav-pills > li.active > a, .nav-pills > li > a:hover,
                                .wpt-tabs .tab_title.selected a, .wpt-tabs .tab_title a:hover, .support_bs .btn-success:hover, table.compare-list tbody tr.add-to-cart td a span, table.compare-list tbody tr.add-to-cart td a, .support_bs .btn-success:active, .support_bs .btn-success:focus,.support_bs .btn-success,
                                .woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button', 
                                'border-color' => '.support_bs .btn-success, .support_bs .btn-success:hover, .support_bs .btn-success:active, .support_bs .btn-success:focus, .subcriber-widget button.btn',     
                                'border-color'            => '.nav-pills > li.active > a,.nav-pills > li > a:hover,.wpt-tabs .tab_title.selected a, .wpt-tabs .tab_title a:hover',                          
                            )
                        ),

                        array(
                            'id'       => 'button_txt_color',
                            'type'     => 'color',
                            'compiler' => true,
                            'title'    => esc_html__('Button Text Color', 'printshop'),
                            'default'  => '#ffffff',
                            'output'   => array(
                                'color'            => '.sticky-product a.button span, .wpt-tabs .tab_title.selected a, .wpt-tabs .tab_title a:hover, table.compare-list tbody tr.add-to-cart td a span, table.compare-list tbody tr.add-to-cart td a', 
                                                              
                            )
                        ),


                        array(
                            'id'       => 'meta_color',
                            'type'     => 'color',
                            'compiler' => true,
                            'title'    => esc_html__('Meta Color', 'printshop'),
                            'default'  => '#f8f9f9',
                            'output'   => array(
                                'background-color' => '.hentry.sticky, .entry-content blockquote, .entry-meta .sticky-label,
                                    .entry-author, #comments .comment .comment-wrapper, .page-title-wrap, .widget_wpc_posts ul li,
                                    .inverted-column > .wpb_wrapper, .inverted-row, div.wpcf7-response-output'
                            )
                        ),

                        array(
                            'id'       => 'border_color',
                            'type'     => 'color',
                            'compiler' => true,
                            'title'    => esc_html__('Border Color', 'printshop'),
                            'default'  => '#e9e9e9',
                            'output'   => array(
                                'border-color' => ''                                
                            )
                        ),
                        
                        array(
                            'id'       => 'body_bg',
                            'type'     => 'background',
                            'compiler' => true,
                            'output'   => array('.site'),
                            'title'    => esc_html__('Site Background', 'printshop'),
                            'default'  => array(
                                'background-color' => '#ffffff',
                            )
                            
                        ),
                        
                        
                        array(
                            'id'       => 'boxed_bg',
                            'type'     => 'background',
                            'compiler' => true,
                            'output'   => array('.layout-boxed'),
                            'title'    => esc_html__('Body Background for boxed layout', 'printshop'),
                            'default'  => array(
                                'background-color' => '#333333',
                            )
                        ),
                        
                    )
                );


                /*--------------------------------------------------------*/
                /* TYPOGRAPHY
                /*--------------------------------------------------------*/
                $this->sections[] = array(
                    'title'      => esc_html__('Typography', 'printshop'),
                    'header'     => '',
                    'desc'       => '',
                    'icon_class' => 'el-icon-large',
                    'icon'       => 'el-icon-font',
                    'submenu'    => true,
                    'fields'     => array(
                        array(
                            'id'             =>'font_body',
                            'type'           => 'typography', 
                            'title'          => esc_html__('Body', 'printshop'),
                            'compiler'       =>true,
                            'google'         =>true,
                            'font-backup'    =>false,
                            'font-weight'    =>false,
                            'all_styles'     =>true,
                            'font-style'     =>false,
                            'subsets'        =>true,
                            'font-size'      =>true,
                            'line-height'    =>false,
                            'word-spacing'   =>false,
                            'letter-spacing' =>false,
                            'color'          =>true,
                            'preview'        =>true,
                            'output'         => array('body'),
                            'units'          =>'px',
                            'subtitle'       => esc_html__('Select custom font for your main body text.', 'printshop'),
                            'default'        => array(
                                'color'       =>"#444444",
                                'font-family' =>'Roboto', 
                                'font-size'   =>'14px',
                            )
                        ),
                        array(
                            'id'             =>'font_heading',
                            'type'           => 'typography', 
                            'title'          => esc_html__('Heading', 'printshop'),
                            'compiler'       =>true,
                            'google'         =>true,
                            'font-backup'    =>false,
                            'all_styles'     =>true,
                            'font-weight'    =>true,
                            'font-style'     =>false,
                            'subsets'        =>true,
                            'font-size'      =>false,
                            'line-height'    =>false,
                            'word-spacing'   =>false,
                            'letter-spacing' =>true,
                            'color'          =>true,
                            'preview'        =>true,
                            'output'         => array('h1,h2,h3,h4,h5,h6, .font-heading'),
                            'units'          =>'px',
                            'subtitle'       => esc_html__('Select custom font for heading like h1, h2, h3, ...', 'printshop'),
                            'default'        => array(
                                'color'       =>"#444444",
                                'font-family' =>'Roboto',
                            )
                        ),  
                    ),
                );

                /*--------------------------------------------------------*/
                /* BLOG SETTINGS
                /*--------------------------------------------------------*/
                $this->sections[] = array(
                    'title'  => esc_html__( 'Blog', 'printshop' ),
                    'desc'   => '',
                    'icon'   => 'el-icon-pencil el-icon-pencil',
                    'submenu' => true,
                    'fields' => array(
                        array(
                            'id'       => 'blog_page_title',
                            'type'     => 'switch',
                            'title'    => esc_html__('Enable Blog Page Title', 'printshop'),
                            'subtitle' => esc_html__('Do you want to enable blog page title?', 'printshop'),
                            'default'  => true,
                        ),
                        array(
                            'id'       => 'blog_single_page_title',
                            'type'     => 'switch',
                            'title'    => esc_html__('Enable Blog Page Title For Single Blog Post', 'printshop'),
                            'subtitle' => esc_html__('Do you want to enable blog page title on single blog post?', 'printshop'),
                            'default'  => true,
                        ),

                        array(
                            'id'   =>'divider_1',
                            'desc' => '',
                            'type' => 'divide'
                        ),
                        array(
                            'id'       => 'blog_single_thumb',
                            'type'     => 'switch',
                            'title'    => esc_html__('Show Featured Image', 'printshop'),
                            'desc'     => esc_html__('Show featured image on single blog post?', 'printshop'),
                            'default'  => true,
                        ),
                        array(
                            'id'       => 'blog_single_author',
                            'type'     => 'switch',
                            'title'    => esc_html__('Show Author Box', 'printshop'),
                            'desc'     => esc_html__('Show author bio box on single blog post?', 'printshop'),
                            'default'  => true,
                        ),
                    )
                );

                /*--------------------------------------------------------*/
                /* FOOTER CONNECT
                /*--------------------------------------------------------*/
                $this->sections[] = array(
                    'title'  => esc_html__( 'Footer Connect', 'printshop' ),
                    'desc'   => '',
                    'icon'   => 'el-icon-file-new',
                    'submenu' => true,
                    'fields' => array(
                        array(
                            'id'       => 'footer_social',
                            'type'     => 'switch',
                            'title'    => esc_html__('Enable footer connect social icon', 'printshop'),
                            'default'  => false,
                        ),
                        array(
                            'id'       => 'social_text',
                            'type'     => 'text',
                            'title'    => esc_html__('Social Text', 'printshop'),
                            'subtitle' => '',
                            'desc'     => esc_html__('Enter social text before the social icons.', 'printshop'),
                            'default'  => 'Follow us',
                        ),
                        array(
                            'id'       => 'footer_use_social',
                            'type'     => 'checkbox',
                            'title'    => esc_html__('Enable Social Icon?', 'printshop'),
                            'subtitle' => esc_html__('Which icon should display? the social icon url will be take from Social Media setting tab.', 'printshop'),
                            'options'  => array(
                                'twitter'   => 'Twitter',
                                'facebook'  => 'Facebook',
                                'linkedin'  => 'Linkedin',
                                'pinterest' => 'Pinterest'
                            ),
                        ),

                    )
                );


                /*--------------------------------------------------------*/
                /* FOOTER
                /*--------------------------------------------------------*/
                $this->sections[] = array(
                    'title'  => esc_html__( 'Footer', 'printshop' ),
                    'desc'   => '',
                    'icon'   => 'el-icon-photo',
                    'submenu' => true,
                    'fields' => array(
                        array(
                            'id'       => 'footer_widgets',
                            'type'     => 'switch',
                            'title'    => esc_html__('Enable footer widgets area.', 'printshop'),
                            'default'  => true,
                        ),
                        array(
                            'id'      => 'footer_columns',
                            'type'    => 'button_set',
                            'title'   => esc_html__( 'Footer Columns', 'printshop' ),
                            'desc'    => esc_html__( 'Select the number of columns you would like for your footer widgets area.', 'printshop' ),
                            'type'    => 'button_set',
                            'default' => '4',
                            'required' => array('footer_widgets','=',true, ),
                            'options' => array(
                                '1'   => esc_html__( '1 Columns', 'printshop' ),
                                '2'   => esc_html__( '2 Columns', 'printshop' ),
                                '3'   => esc_html__( '3 Columns', 'printshop' ),
                                '4'   => esc_html__( '4 Columns', 'printshop' ),
                            ),
                        ),
                        array(
                            'id'       =>'footer_copyright',
                            'type'     => 'textarea',
                            'title'    => esc_html__('Footer Copyright', 'printshop'),
                            'subtitle' => esc_html__('Enter the copyright section text.', 'printshop'),
                        ),

                        array(
                            'id'       => 'footer_custom_color',
                            'type'     => 'switch',
                            'title'    => esc_html__('Custom your footer style?.', 'printshop'),
                            'default'  => false,
                        ),
                        array(
                            'id'       => 'footer_bg',
                            'type'     => 'background',
                            'compiler' => true,
                            'output'   => array('.site-footer'),
                            'title'    => esc_html__('Footer Background ', 'printshop'),
                            'required' => array('footer_custom_color','=',true, ),
                            'default'  => array(
                                'background-color' => '#111111',
                            )
                        ),
                        array(
                            'id'       => 'footer_widget_title_color',
                            'type'     => 'color',
                            'compiler' => true,
                            'output'   => array('.site-footer .footer-columns .footer-column .widget .widget-title'),
                            'title'    => esc_html__('Footer Widget Title Color', 'printshop'),
                            'default'  => '#eeeeee',
                            'required' => array('footer_custom_color','=',true, )
                        ),
                        array(
                            'id'       => 'footer_text_color',
                            'type'     => 'color',
                            'compiler' => true,
                            'output'   => array('.site-footer, .site-footer .widget, .site-footer p'),
                            'title'    => esc_html__('Footer Text Color', 'printshop'),
                            'default'  => '#999999',
                            'required' => array('footer_custom_color','=',true, )
                        ),
                        array(
                            'id'       => 'footer_link_color',
                            'type'     => 'color',
                            'compiler' => true,
                            'output'   => array('.site-footer a, .site-footer .widget a'),
                            'title'    => esc_html__('Footer Link Color', 'printshop'),
                            'default'  => '#dddddd',
                            'required' => array('footer_custom_color','=',true, )
                        ),
                        array(
                            'id'       => 'footer_link_color_hover',
                            'type'     => 'color',
                            'compiler' => true,
                            'output'   => array('.site-footer a:hover, .site-footer .widget a:hover'),
                            'title'    => esc_html__('Footer Link Color Hover', 'printshop'),
                            'default'  => '#ffffff',
                            'required' => array('footer_custom_color','=',true, )
                        ),
                        array(
                            'id'       => 'site_info_bg',
                            'type'     => 'background',
                            'compiler' => true,
                            'output'   => array('.site-info-wrapper'),
                            'title'    => esc_html__('Site Info Background', 'printshop'),
                            'required' => array('footer_custom_color','=',true, ),
                            'default'  => array(
                            )
                            
                        ),
                    )
                );

                /*--------------------------------------------------------*/
                /* SOCIAL
                /*--------------------------------------------------------*/
                $this->sections[] = array(
                    'title'  => esc_html__( 'Social Media', 'printshop' ),
                    'desc'   => 'Enter social url here and then active them in footer or header options. Please add full URLs include "http://".',
                    'icon'   => 'el-icon-address-book',
                    'submenu' => true,
                    'fields' => array(
                        array(
                            'id'       =>'twitter',
                            'type'     => 'text',
                            'title'    => esc_html__('Twitter', 'printshop'),
                            'subtitle' => '',
                            'desc'     => esc_html__('Enter your Twitter URL.', 'printshop'),
                        ),
                        array(
                            'id'       =>'facebook',
                            'type'     => 'text',
                            'title'    => esc_html__('Facebook', 'printshop'),
                            'subtitle' => '',
                            'desc'     => esc_html__('Enter your Facebook URL.', 'printshop'),
                        ),
                        array(
                            'id'       =>'linkedin',
                            'type'     => 'text',
                            'title'    => esc_html__('Linkedin', 'printshop'),
                            'subtitle' => '',
                            'desc'     => esc_html__('Enter your Linkedin URL.', 'printshop'),
                        ),
                        array(
                            'id'       =>'pinterest',
                            'type'     => 'text',
                            'title'    => esc_html__('Pinterest', 'printshop'),
                            'subtitle' => '',
                            'desc'     => esc_html__('Enter your Pinterest URL.', 'printshop'),
                        ),
                        
                    )
                );
                
                /*--------------------------------------------------------*/
                /* ONLINE DESIGN
                /*--------------------------------------------------------*/
                $this->sections[] = array(
                    'title'  => esc_html__( 'Online Design', 'printshop' ),
                    'desc'   => 'Enter social url here and then active them in footer or header options. Please add full URLs include "http://".',
                    'icon'   => 'el-icon-picasa',
                    'submenu' => true,
                    'fields' => array(
                        array(
                            'id'      => 'nbcore_template_designer_style',
                            'type'    => 'button_set',
                            'title'   => esc_html__( 'Style designer page', 'printshop' ),
                            'desc'    => esc_html__( 'Select the style you would like for your designer page.', 'printshop' ),
                            'type'    => 'button_set',
                            'default' => 'style1',
                            'options' => array(
                                'style1' => esc_html__('Style 1', 'printshop'),
                                'style2' => esc_html__('Style 2', 'printshop'),
                                'style3' => esc_html__('Style 3', 'printshop')
                            ),
                        ),
                    )
                );
            }

        }

        global $reduxConfig;
        $reduxConfig = new Printshop_Options_Config();

        // Retrieve theme option values
        if ( ! function_exists('printshop_get_option') ) {
            function printshop_get_option($id, $fallback = false, $key = false ) {
                global $printshop_option;
                if ( $fallback == false ) $fallback = '';
                $output = ( isset($printshop_option[$id]) && $printshop_option[$id] !== '' ) ? $printshop_option[$id] : $fallback;
                if ( !empty($printshop_option[$id]) && $key ) {
                    $output = $printshop_option[$id][$key];
                }
                return $output;
            }
        }
    }
