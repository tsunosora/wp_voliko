<?php

if ( class_exists("Kirki")){

	Kirki::add_config('theme_config_id', array(
		'capability'   =>  'edit_theme_options',
		'option_type'  =>  'theme_mod',
	));

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'slider',
		'settings'    => 'printing_press_logo_resizer',
		'label'       => esc_html__( 'Adjust Logo Size', 'printing-press' ),
		'section'     => 'title_tagline',
		'default'     => 70,
		'choices'     => [
			'min'  => 10,
			'max'  => 300,
			'step' => 10,
		],
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_enable_logo_text',
		'section'     => 'title_tagline',
		'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Enable / Disable Site Title and Tagline', 'printing-press' ) . '</h3>',
		'priority'    => 10,
	] );

  	Kirki::add_field( 'theme_config_id', [
		'type'        => 'switch',
		'settings'    => 'printing_press_display_header_title',
		'label'       => esc_html__( ' Enable / Disable Site Title', 'printing-press' ),
		'section'     => 'title_tagline',
		'default'     => '1',
		'priority'    => 10,
		'choices'     => [
			'on'  => esc_html__( 'Enable', 'printing-press' ),
			'off' => esc_html__( 'Disable', 'printing-press' ),
		],
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'switch',
		'settings'    => 'printing_press_display_header_text',
		'label'       => esc_html__( ' Enable / Disable Tagline', 'printing-press' ),
		'section'     => 'title_tagline',
		'default'     => '1',
		'priority'    => 10,
		'choices'     => [
			'on'  => esc_html__( 'Enable', 'printing-press' ),
			'off' => esc_html__( 'Disable', 'printing-press' ),
		],
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_site_tittle_font_heading',
		'section'     => 'title_tagline',
		'default'     => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Site Title Font Size', 'printing-press' ) . '</h3>',
	] );

	Kirki::add_field( 'theme_config_id', array(
		'settings'    => 'printing_press_site_tittle_font_size',
		'type'        => 'number',
		'section'     => 'title_tagline',
		'transport' => 'auto',
		'output' => array(
			array(
				'element'  => array('.logo a'),
				'property' => 'font-size',
				'suffix' => 'px'
			),
		),
	) );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_site_tagline_font_heading',
		'section'     => 'title_tagline',
		'default'     => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Site Tagline Font Size', 'printing-press' ) . '</h3>',
	] );

	Kirki::add_field( 'theme_config_id', array(
		'settings'    => 'printing_press_site_tagline_font_size',
		'type'        => 'number',
		'section'     => 'title_tagline',
		'transport' => 'auto',
		'output' => array(
			array(
				'element'  => array('.logo span'),
				'property' => 'font-size',
				'suffix' => 'px'
			),
		),
	) );

	// TYPOGRAPHY SETTINGS
	Kirki::add_panel( 'printing_press_typography_panel', array(
		'priority' => 10,
		'title'    => __( 'Typography', 'printing-press' ),
	) );

	//Heading 1 Section

	Kirki::add_section( 'printing_press_h1_typography_setting', array(
		'title'    => __( 'Heading 1', 'printing-press' ),
		'panel'    => 'printing_press_typography_panel',
		'priority' => 0,
	) );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_h1_typography_heading',
		'section'     => 'printing_press_h1_typography_setting',
		'default'     => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Heading 1 Typography', 'printing-press' ) . '</h3>',
	] );

	Kirki::add_field( 'theme_config_id', array(
		'type'  =>  'typography',
		'settings'  => 'printing_press_h1_typography_font',
		'section'   =>  'printing_press_h1_typography_setting',
		'default'   =>  [
			'font-family'   =>  'Inter',
			'variant'       =>  '700',
			'font-size'       => '',
			'line-height'   =>  '',
			'letter-spacing'    =>  '',
			'text-transform'    =>  '',
		],
		'transport'     =>  'auto',
		'output'        =>  [
			[
				'element'   =>  'h1',
				'suffix' => '!important'
			],
		],
	) );


	//Heading 2 Section

	Kirki::add_section( 'printing_press_h2_typography_setting', array(
		'title'    => __( 'Heading 2', 'printing-press' ),
		'panel'    => 'printing_press_typography_panel',
		'priority' => 0,
	) );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_h2_typography_heading',
		'section'     => 'printing_press_h2_typography_setting',
		'default'     => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Heading 2 Typography', 'printing-press' ) . '</h3>',
	] );

	Kirki::add_field( 'theme_config_id', array(
		'type'  =>  'typography',
		'settings'  => 'printing_press_h2_typography_font',
		'section'   =>  'printing_press_h2_typography_setting',
		'default'   =>  [
			'font-family'   =>  'Inter',
			'font-size'       => '',
			'variant'       =>  '700',
			'line-height'   =>  '',
			'letter-spacing'    =>  '',
			'text-transform'    =>  '',
		],
		'transport'     =>  'auto',
		'output'        =>  [
			[
				'element'   =>  'h2',
				'suffix' => '!important'
			],
		],
	) );

	//Heading 3 Section

	Kirki::add_section( 'printing_press_h3_typography_setting', array(
		'title'    => __( 'Heading 3', 'printing-press' ),
		'panel'    => 'printing_press_typography_panel',
		'priority' => 0,
	) );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_h3_typography_heading',
		'section'     => 'printing_press_h3_typography_setting',
		'default'     => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Heading 3 Typography', 'printing-press' ) . '</h3>',
	] );

	Kirki::add_field( 'theme_config_id', array(
		'type'  =>  'typography',
		'settings'  => 'printing_press_h3_typography_font',
		'section'   =>  'printing_press_h3_typography_setting',
		'default'   =>  [
			'font-family'   =>  'Inter',
			'variant'       =>  '700',
			'font-size'       => '',
			'line-height'   =>  '',
			'letter-spacing'    =>  '',
			'text-transform'    =>  '',
		],
		'transport'     =>  'auto',
		'output'        =>  [
			[
				'element'   =>  'h3',
				'suffix' => '!important'
			],
		],
	) );

	//Heading 4 Section

	Kirki::add_section( 'printing_press_h4_typography_setting', array(
		'title'    => __( 'Heading 4', 'printing-press' ),
		'panel'    => 'printing_press_typography_panel',
		'priority' => 0,
	) );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_h4_typography_heading',
		'section'     => 'printing_press_h4_typography_setting',
		'default'     => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Heading 4 Typography', 'printing-press' ) . '</h3>',
	] );

	Kirki::add_field( 'theme_config_id', array(
		'type'  =>  'typography',
		'settings'  => 'printing_press_h4_typography_font',
		'section'   =>  'printing_press_h4_typography_setting',
		'default'   =>  [
			'font-family'   =>  'Inter',
			'variant'       =>  '700',
			'font-size'       => '',
			'line-height'   =>  '',
			'letter-spacing'    =>  '',
			'text-transform'    =>  '',
		],
		'transport'     =>  'auto',
		'output'        =>  [
			[
				'element'   =>  'h4',
				'suffix' => '!important'
			],
		],
	) );

	//Heading 5 Section

	Kirki::add_section( 'printing_press_h5_typography_setting', array(
		'title'    => __( 'Heading 5', 'printing-press' ),
		'panel'    => 'printing_press_typography_panel',
		'priority' => 0,
	) );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_h5_typography_heading',
		'section'     => 'printing_press_h5_typography_setting',
		'default'     => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Heading 5 Typography', 'printing-press' ) . '</h3>',
	] );

	Kirki::add_field( 'theme_config_id', array(
		'type'  =>  'typography',
		'settings'  => 'printing_press_h5_typography_font',
		'section'   =>  'printing_press_h5_typography_setting',
		'default'   =>  [
			'font-family'   =>  'Inter',
			'variant'       =>  '700',
			'font-size'       => '',
			'line-height'   =>  '',
			'letter-spacing'    =>  '',
			'text-transform'    =>  '',
		],
		'transport'     =>  'auto',
		'output'        =>  [
			[
				'element'   =>  'h5',
				'suffix' => '!important'
			],
		],
	) );

	//Heading 6 Section

	Kirki::add_section( 'printing_press_h6_typography_setting', array(
		'title'    => __( 'Heading 6', 'printing-press' ),
		'panel'    => 'printing_press_typography_panel',
		'priority' => 0,
	) );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_h6_typography_heading',
		'section'     => 'printing_press_h6_typography_setting',
		'default'     => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Heading 6 Typography', 'printing-press' ) . '</h3>',
	] );

	Kirki::add_field( 'theme_config_id', array(
		'type'  =>  'typography',
		'settings'  => 'printing_press_h6_typography_font',
		'section'   =>  'printing_press_h6_typography_setting',
		'default'   =>  [
			'font-family'   =>  'Inter',
			'variant'       =>  '700',
			'font-size'       => '',
			'line-height'   =>  '',
			'letter-spacing'    =>  '',
			'text-transform'    =>  '',
		],
		'transport'     =>  'auto',
		'output'        =>  [
			[
				'element'   =>  'h6',
				'suffix' => '!important'
			],
		],
	) );

	//body Typography

	Kirki::add_section( 'printing_press_body_typography_setting', array(
		'title'    => __( 'Content Typography', 'printing-press' ),
		'panel'    => 'printing_press_typography_panel',
		'priority' => 0,
	) );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_body_typography_heading',
		'section'     => 'printing_press_body_typography_setting',
		'default'     => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Content  Typography', 'printing-press' ) . '</h3>',
	] );

	Kirki::add_field( 'theme_config_id', array(
		'type'  =>  'typography',
		'settings'  => 'printing_press_body_typography_font',
		'section'   =>  'printing_press_body_typography_setting',
		'default'   =>  [
			'font-family'   =>  'Inter',
			'variant'       =>  '',
		],
		'transport'     =>  'auto',
		'output'        =>  [
			[
				'element'   => 'body',
				'suffix' => '!important'
			],
		],
	) );

	// Theme Options Panel
	Kirki::add_panel( 'printing_press_theme_options_panel', array(
		'priority' => 10,
		'title'    => __( 'Theme Options', 'printing-press' ),
	) );

	// HEADER SECTION

	Kirki::add_section( 'printing_press_section_header', array(
	    'title'          => esc_html__( 'Header Settings', 'printing-press' ),
	    'description'    => esc_html__( 'Here you can add header information.', 'printing-press' ),
	    'panel'    => 'printing_press_theme_options_panel',
		'priority'       => 160,
	) );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'switch',
		'settings'    => 'printing_press_sticky_header',
		'label'       => esc_html__( 'Enable/Disable Sticky Header', 'printing-press' ),
		'section'     => 'printing_press_section_header',
		'default'     => 'on',
		'priority'    => 10,
		'choices'     => [
			'on'  => esc_html__( 'Enable', 'printing-press' ),
			'off' => esc_html__( 'Disable', 'printing-press' ),
		],
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_advertisement_text_heading',
		'section'     => 'printing_press_section_header',
		'default'     => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Advertisement Text', 'printing-press' ) . '</h3>',
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'     => 'text',
		'settings' => 'printing_press_header_advertisement_text',
		'section'  => 'printing_press_section_header',
		'default'  => '',
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_enable_button_heading',
		'section'     => 'printing_press_section_header',
		'default'     => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Button', 'printing-press' ) . '</h3>',
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'     => 'text',
		'label'    => esc_html__( 'Button Text', 'printing-press' ),
		'settings' => 'printing_press_header_button_text',
		'section'  => 'printing_press_section_header',
		'default'  => '',
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'     => 'url',
		'label'    =>  esc_html__( 'Button Link', 'printing-press' ),
		'settings' => 'printing_press_header_button_url',
		'section'  => 'printing_press_section_header',
		'default'  => '',
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_header_phone_number_heading',
		'section'     => 'printing_press_section_header',
		'default'     => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Add Phone Number', 'printing-press' ) . '</h3>',
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'     => 'text',
		'settings' => 'printing_press_header_phone_number',
		'section'  => 'printing_press_section_header',
		'default'  => '',
		'sanitize_callback' => 'printing_press_sanitize_phone_number',
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_header_email_heading',
		'section'     => 'printing_press_section_header',
		'default'     => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Add Email', 'printing-press' ) . '</h3>',
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'     => 'text',
		'settings' => 'printing_press_header_email',
		'section'  => 'printing_press_section_header',
		'default'  => '',
		'sanitize_callback' => 'sanitize_email',
	] );


	Kirki::add_field( 'theme_config_id', [
		'type'        => 'switch',
		'settings'    => 'printing_press_cart_box_enable',
		'label'       => esc_html__( 'Enable/Disable Shopping Cart', 'printing-press' ),
		'section'     => 'printing_press_section_header',
		'default'     => 'on',
		'priority'    => 10,
		'choices'     => [
			'on'  => esc_html__( 'Enable', 'printing-press' ),
			'off' => esc_html__( 'Disable', 'printing-press' ),
		],
	] );

	//ADDITIONAL SETTINGS

	Kirki::add_section( 'printing_press_additional_setting', array(
		'title'          => esc_html__( 'Additional Settings', 'printing-press' ),
		'description'    => esc_html__( 'Additional Settings of themes', 'printing-press' ),
		'panel'    => 'printing_press_theme_options_panel',
		'priority'       => 160,
	) );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'toggle',
		'settings'    => 'printing_press_preloader_hide',
		'label'       => esc_html__( 'Here you can enable or disable your preloader.', 'printing-press' ),
		'section'     => 'printing_press_additional_setting',
		'default'     => '0',
		'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'toggle',
		'settings'    => 'printing_press_scroll_enable_setting',
		'label'       => esc_html__( 'Here you can enable or disable scroller.', 'printing-press' ),
		'section'     => 'printing_press_additional_setting',
		'default'     => '0',
		'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_single_page_layout_heading',
		'section'     => 'printing_press_additional_setting',
		'default'     => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Single Page Layout', 'printing-press' ) . '</h3>',
	] );

	Kirki::add_field( 'theme_config_id', array(
		'type'        => 'select',
		'settings'    => 'printing_press_single_page_layout',
		'section'     => 'printing_press_additional_setting',
		'default'     => 'One Column',
		'choices'     => [
			'Left Sidebar' => esc_html__( 'Left Sidebar', 'printing-press' ),
			'Right Sidebar' => esc_html__( 'Right Sidebar', 'printing-press' ),
			'One Column' => esc_html__( 'One Column', 'printing-press' ),
		],
	) );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_header_background_attachment_heading',
		'section'     => 'printing_press_additional_setting',
		'default'     => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Header Image Attachment', 'printing-press' ) . '</h3>',
	] );

	Kirki::add_field( 'theme_config_id', array(
		'type'        => 'select',
		'settings'    => 'printing_press_header_background_attachment',
		'section'     => 'printing_press_additional_setting',
		'default'     => 'scroll',
		'choices'     => [
			'scroll' => esc_html__( 'Scroll', 'printing-press' ),
			'fixed' => esc_html__( 'Fixed', 'printing-press' ),
		],
		'output' => array(
			array(
				'element'  => '.header-image-box',
				'property' => 'background-attachment',
			),
		),
	 ) );

	 Kirki::add_field( 'theme_config_id', [
		'type'        => 'toggle',
		'settings'    => 'printing_press_header_page_title',
		'label'       => esc_html__( 'Enable / Disable Header Image Page Title.', 'printing-press' ),
		'section'     => 'printing_press_additional_setting',
		'default'     => '1',
		'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'toggle',
		'settings'    => 'printing_press_header_breadcrumb',
		'label'       => esc_html__( 'Enable / Disable Header Image Breadcrumb.', 'printing-press' ),
		'section'     => 'printing_press_additional_setting',
		'default'     => '1',
		'priority'    => 10,
	] );

	// POST SECTION

	Kirki::add_section( 'printing_press_blog_post', array(
		'title'          => esc_html__( 'Post Settings', 'printing-press' ),
		'description'    => esc_html__( 'Here you can add post information.', 'printing-press' ),
		'panel'    => 'printing_press_theme_options_panel',
		'priority'       => 160,
	) );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_post_layout_heading',
		'section'     => 'printing_press_blog_post',
		'default'     => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Blog Layout', 'printing-press' ) . '</h3>',
	] );

	Kirki::add_field( 'theme_config_id', array(
		'type'        => 'select',
		'settings'    => 'printing_press_post_layout',
		'section'     => 'printing_press_blog_post',
		'default'     => 'Right Sidebar',
		'choices'     => [
			'Left Sidebar' => esc_html__( 'Left Sidebar', 'printing-press' ),
			'Right Sidebar' => esc_html__( 'Right Sidebar', 'printing-press' ),
			'One Column' => esc_html__( 'One Column', 'printing-press' ),
			'Three Columns' => esc_html__( 'Three Columns', 'printing-press' ),
			'Four Columns' => esc_html__( 'Four Columns', 'printing-press' ),
		],
	) );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'toggle',
		'settings'    => 'printing_press_date_hide',
		'label'       => esc_html__( 'Enable / Disable Post Date', 'printing-press' ),
		'section'     => 'printing_press_blog_post',
		'default'     => '1',
		'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'toggle',
		'settings'    => 'printing_press_author_hide',
		'label'       => esc_html__( 'Enable / Disable Post Author', 'printing-press' ),
		'section'     => 'printing_press_blog_post',
		'default'     => '1',
		'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'toggle',
		'settings'    => 'printing_press_comment_hide',
		'label'       => esc_html__( 'Enable / Disable Post Comment', 'printing-press' ),
		'section'     => 'printing_press_blog_post',
		'default'     => '1',
		'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'toggle',
		'settings'    => 'printing_press_blog_post_featured_image',
		'label'       => esc_html__( 'Enable / Disable Post Image', 'printing-press' ),
		'section'     => 'printing_press_blog_post',
		'default'     => '1',
		'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_length_setting_heading',
		'section'     => 'printing_press_blog_post',
		'default'     => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Blog Post Content Limit', 'printing-press' ) . '</h3>',
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'number',
		'settings'    => 'printing_press_length_setting',
		'section'     => 'printing_press_blog_post',
		'default'     => '15',
		'priority'    => 10,
		'choices'  => [
					'min'  => -10,
					'max'  => 40,
		 			'step' => 1,
				],
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'toggle',
		'label'       => esc_html__( 'Enable / Disable Single Post Tag', 'printing-press' ),
		'settings'    => 'printing_press_single_post_tag',
		'section'     => 'printing_press_blog_post',
		'default'     => '1',
		'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'toggle',
		'label'       => esc_html__( 'Enable / Disable Single Post Category', 'printing-press' ),
		'settings'    => 'printing_press_single_post_category',
		'section'     => 'printing_press_blog_post',
		'default'     => '1',
		'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'toggle',
		'settings'    => 'printing_press_single_post_featured_image',
		'label'       => esc_html__( 'Enable / Disable Single Post Image', 'printing-press' ),
		'section'     => 'printing_press_blog_post',
		'default'     => '1',
		'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_single_post_radius',
		'section'     => 'printing_press_blog_post',
		'default'     => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Single Post Image Border Radius(px)', 'printing-press' ) . '</h3>',
	] );

	Kirki::add_field( 'theme_config_id', array(
		'settings'    => 'printing_press_single_post_border_radius',
		'label'       => __( 'Enter a value in pixels. Example:15px', 'printing-press' ),
		'type'        => 'text',
		'section'     => 'printing_press_blog_post',
		'transport' => 'auto',
		'output' => array(
			array(
				'element'  => array('.post-img img'),
				'property' => 'border-radius',
			),
		),
	) );

	// WOOCOMMERCE SETTINGS

	Kirki::add_section( 'printing_press_woocommerce_settings', array(
		'title'          => esc_html__( 'Woocommerce Settings', 'printing-press' ),
		'description'    => esc_html__( 'Woocommerce Settings of themes', 'printing-press' ),
		'panel'    => 'woocommerce',
		'priority'       => 160,
	) );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'toggle',
		'settings'    => 'printing_press_shop_page_sidebar',
		'label'       => esc_html__( 'Enable/Disable Shop Page Sidebar', 'printing-press' ),
		'section'     => 'printing_press_woocommerce_settings',
		'default'     => 'true',
		'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', array(
		'type'        => 'select',
		'label'       => esc_html__( 'Shop Page Layouts', 'printing-press' ),
		'settings'    => 'printing_press_shop_page_layout',
		'section'     => 'printing_press_woocommerce_settings',
		'default'     => 'Right Sidebar',
		'choices'     => [
			'Right Sidebar' => esc_html__( 'Right Sidebar', 'printing-press' ),
			'Left Sidebar' => esc_html__( 'Left Sidebar', 'printing-press' ),
		],
		'active_callback'  => [
			[
				'setting'  => 'printing_press_shop_page_sidebar',
				'operator' => '===',
				'value'    => true,
			],
		]

	) );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'select',
		'label'       => esc_html__( 'Products Per Row', 'printing-press' ),
		'settings'    => 'printing_press_products_per_row',
		'section'     => 'printing_press_woocommerce_settings',
		'default'     => '3',
		'priority'    => 10,
		'choices'     => [
			'2' => '2',
			'3' => '3',
			'4' => '4',
		],
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'number',
		'label'       => esc_html__( 'Products Per Page', 'printing-press' ),
		'settings'    => 'printing_press_products_per_page',
		'section'     => 'printing_press_woocommerce_settings',
		'default'     => '9',
		'priority'    => 10,
		'choices'  => [
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				],
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'toggle',
		'settings'    => 'printing_press_single_product_sidebar',
		'label'       => esc_html__( 'Enable / Disable Single Product Sidebar', 'printing-press' ),
		'section'     => 'printing_press_woocommerce_settings',
		'default'     => 'true',
		'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', array(
		'type'        => 'select',
		'label'       => esc_html__( 'Single Product Layout', 'printing-press' ),
		'settings'    => 'printing_press_single_product_sidebar_layout',
		'section'     => 'printing_press_woocommerce_settings',
		'default'     => 'Right Sidebar',
		'choices'     => [
			'Right Sidebar' => esc_html__( 'Right Sidebar', 'printing-press' ),
			'Left Sidebar' => esc_html__( 'Left Sidebar', 'printing-press' ),
		],
		'active_callback'  => [
			[
				'setting'  => 'printing_press_single_product_sidebar',
				'operator' => '===',
				'value'    => true,
			],
		]
	) );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_products_button_border_radius_heading',
		'section'     => 'printing_press_woocommerce_settings',
		'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Products Button Border Radius', 'printing-press' ) . '</h3>',
		'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'slider',
		'settings'    => 'printing_press_products_button_border_radius',
		'section'     => 'printing_press_woocommerce_settings',
		'default'     => '1',
		'priority'    => 10,
		'choices'  => [
					'min'  => 1,
					'max'  => 50,
					'step' => 1,
				],
		'output' => array(
			array(
				'element'  => array('.woocommerce ul.products li.product .button',' a.checkout-button.button.alt.wc-forward','.woocommerce #respond input#submit', '.woocommerce a.button', '.woocommerce button.button','.woocommerce input.button','.woocommerce #respond input#submit.alt','.woocommerce button.button.alt','.woocommerce input.button.alt'),
				'property' => 'border-radius',
				'units' => 'px',
			),
		),
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_sale_badge_position_heading',
		'section'     => 'printing_press_woocommerce_settings',
		'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Sale Badge Position', 'printing-press' ) . '</h3>',
		'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', array(
		'type'        => 'select',
		'settings'    => 'printing_press_sale_badge_position',
		'section'     => 'printing_press_woocommerce_settings',
		'default'     => 'right',
		'choices'     => [
			'right' => esc_html__( 'Right', 'printing-press' ),
			'left' => esc_html__( 'Left', 'printing-press' ),
		],
	) );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_products_sale_font_size_heading',
		'section'     => 'printing_press_woocommerce_settings',
		'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Sale Font Size', 'printing-press' ) . '</h3>',
		'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'text',
		'settings'    => 'printing_press_products_sale_font_size',
		'section'     => 'printing_press_woocommerce_settings',
		'priority'    => 10,
		'output' => array(
			array(
				'element'  => array('.woocommerce span.onsale','.woocommerce ul.products li.product .onsale'),
				'property' => 'font-size',
				'units' => 'px',
			),
		),
	] );

	// FOOTER SECTION

	Kirki::add_section( 'printing_press_footer_section', array(
        'title'          => esc_html__( 'Footer Settings', 'printing-press' ),
        'description'    => esc_html__( 'Here you can change copyright text', 'printing-press' ),
		'panel'    => 'printing_press_theme_options_panel',
		'priority'       => 160,
    ) );

    Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_footer_text_heading',
		'section'     => 'printing_press_footer_section',
			'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Footer Copyright Text', 'printing-press' ) . '</h3>',
		'priority'    => 10,
	] );

    Kirki::add_field( 'theme_config_id', [
		'type'     => 'text',
		'settings' => 'printing_press_footer_text',
		'section'  => 'printing_press_footer_section',
		'default'  => '',
		'priority' => 10,
	] );

    Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'printing_press_footer_enable_heading',
		'section'     => 'printing_press_footer_section',
			'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Enable / Disable Footer Link', 'printing-press' ) . '</h3>',
		'priority'    => 10,
	] );

    Kirki::add_field( 'theme_config_id', [
		'type'        => 'switch',
		'settings'    => 'printing_press_copyright_enable',
		'label'       => esc_html__( 'Section Enable / Disable', 'printing-press' ),
		'section'     => 'printing_press_footer_section',
		'default'     => '1',
		'priority'    => 10,
		'choices'     => [
			'on'  => esc_html__( 'Enable', 'printing-press' ),
			'off' => esc_html__( 'Disable', 'printing-press' ),
		],
	] );
}
