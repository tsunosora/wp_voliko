<?php
/**
 * Singleton class for handling the theme's customizer integration.
 *
 * @since  1.0.0
 * @access public
 */
final class Printing_Press_Customize {

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Sets up initial actions.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', array( $this, 'sections' ) );

		// Register scripts and styles for the controls.
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ), 0 );
	}

	/**
	 * Sets up the customizer sections.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $manager
	 * @return void
	*/
	public function sections( $manager ) {

		// Load custom sections.
		load_template( trailingslashit( get_template_directory() ) . '/includes/go-pro/upgrade-pro.php' );

		// Register custom section types.
		$manager->register_section_type( 'Printing_Press_Customize_Section_Pro' );

		$manager->add_section(
			new Printing_Press_Customize_Section_Pro(
				$manager,
				'printing_press_upgrade_pro',
				array(
					'title'       => esc_html__( 'Printing Press Pro', 'printing-press' ),
					'pro_text'    => esc_html__( 'GO PRO', 'printing-press' ),
					'pro_url'     => 'https://www.wpelemento.com/elementor/printing-press-wordpress-theme/',
					'priority'    => 5,
				)
			)
		);
		$manager->add_section(
			new Printing_Press_Customize_Section_Pro(
				$manager,
				'printing-press-documentation',
				array(
					'title'       => esc_html__( 'Documentation', 'printing-press' ),
					'pro_text'    => esc_html__( 'DOCS', 'printing-press' ),
					'pro_url'     => 'https://www.wpelemento.com/theme-documentation/printing-press/',
					'priority'    => 5,
				)
			)
		);

		$manager->add_section(
			new Printing_Press_Customize_Section_Pro(
				$manager,
				'printing-press-demo',
				array(
					'title'       => esc_html__( 'Demo link', 'printing-press' ),
					'pro_text'    => esc_html__( 'Demo', 'printing-press' ),
					'pro_url'     => 'https://www.wpelemento.com/demo/printing-press/',
					'priority'    => 5,
				)
			)
		);
	}
	/**
	 * Loads theme customizer CSS.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_control_scripts() {

		wp_enqueue_script( 'printing-press-customize-controls', trailingslashit( get_template_directory_uri() ) . '/assets/js/customize-controls.js', array( 'customize-controls' ) );

		wp_enqueue_style( 'printing-press-customize-controls', trailingslashit( get_template_directory_uri() ) . '/assets/css/customize-controls.css' );
	}
}

// Doing this customizer thang!
Printing_Press_Customize::get_instance();