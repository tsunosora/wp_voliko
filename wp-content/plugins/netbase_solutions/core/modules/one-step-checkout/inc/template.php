<?php
class NBT_OSC_Frontend_Template
{
    /**
     * A reference to an instance of this class.
     */
    private static $instance;
	
    /**
     * The array of templates that this plugin tracks.
     */
    protected $templates;
	
    /**
     * Returns an instance of this class.
     */
    public static function get_instance()
    {
        if (null == self::$instance) {
            self::$instance = new NBT_OSC_Frontend_Template();
        }
        return self::$instance;
    }
    /**
     * Initializes the plugin by setting filters and administration functions.
     */
    private function __construct()
    {
		$this->templates = array();
        // Add your templates to this array.
        $this->templates = array(
            'nb-checkout.php' => 'NB One Checkout'
        );
        add_filter( 'theme_page_templates', array($this, 'wp_set_page_templates'), 20, 3 );
		
		// Add a post display state for special WC pages.
		add_filter( 'display_post_states', array( $this, 'add_display_post_states' ), 10, 2 );
		
		add_action( 'wp_trash_post', array($this, 'wp_trash_nb_checkout'), 10, 1 );
		add_action( 'delete_post', array($this, 'wp_delete_nb_checkout'), 10, 1 );
		add_action( 'untrashed_post', array($this, 'wp_untrashed_nb_checkout'), 10, 1 );
    }
    /**
     * Adds our template to the pages cache in order to trick WordPress
     * into thinking the template file exists where it doens't really exist.
     *
     */
	public function wp_set_page_templates($page_templates, $t, $post) {
		$page_templates = array_merge($page_templates, $this->templates);
		
		return $page_templates;
	}

	/**
	 * Add a post display state for special WC pages in the page list table.
	 *
	 * @param array   $post_states An array of post display states.
	 * @param WP_Post $post        The current post object.
	 */
	public function add_display_post_states( $post_states, $post ) {

		if ( NBT_Solutions_One_Step_Checkout::nb_get_page_id( 'checkout' ) === $post->ID ) {
			$post_states['wc_page_for_nbcheckout'] = __( 'NB One Checkout Page', 'woocommerce' );
		}
		
		return $post_states;
	}
	
	public function wp_trash_nb_checkout($post_id) {		
		if ( NBT_Solutions_One_Step_Checkout::nb_get_page_id( 'checkout' ) === $post_id ) {
			delete_option( 'netbase_checkout_page_id' );
			update_option( 'netbase_checkout_trash_page_id',$post_id );
		}
	}
	
	public function wp_delete_nb_checkout($post_id) {
		if ( NBT_Solutions_One_Step_Checkout::nb_get_page_id( 'checkout_trash' ) === $post_id ) {
			delete_option( 'netbase_checkout_trash_page_id' );
		}
	}
	
	public function wp_untrashed_nb_checkout($post_id) {
		if ( NBT_Solutions_One_Step_Checkout::nb_get_page_id( 'checkout_trash' ) === $post_id ) {
			delete_option( 'netbase_checkout_trash_page_id' );
			update_option( 'netbase_checkout_page_id',$post_id );
		}
	}
	
}

NBT_OSC_Frontend_Template::get_instance();