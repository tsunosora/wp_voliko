<?php
/**
 * Class NPC_Route
 *
 * @since 0.1.0
 */
class NB_Router extends NB_Singleton {
	/**
	 * @since 0.1.0
	 *
	 * @var null
	 */
	private static $rewrite_slug = 'nb-checkout';
	/**
	 * @since 0.1.0
	 *
	 * @var null
	 */
	private static $configs = null;		
    /**
     * Flush rewrite url.
     *
     * @since 0.1.0
     */
    public static function flush() {
        $instance = self::instance();
        $instance->add_route();
        flush_rewrite_rules();
    }
    /**
     * Unflush rewrite url.
     *
     * @since 0.1.0
     */
    public static function deflush() {
        global $wp_rewrite;
        if(is_array($wp_rewrite->endpoints)){
            foreach ($wp_rewrite->endpoints as $key => $endpoint) {
                if(in_array(self::$rewrite_slug, $endpoint)){
                    unset($wp_rewrite->endpoints[$key]);
                }
            }
        }
        $wp_rewrite->flush_rules();
    }
    /**
     * ASL_Route constructor.
     *
     * @since 0.1.0
     */
    protected function __construct() {
        $this->hooks();
    }
    /**
     * Add hooks.
     *
     * @since 0.1.0
     */
    private function hooks() {
        add_action( 'init', array( $this, 'add_route' ) );
        //add_action( 'template_redirect', array( $this, 'handle' ) );
    }
    /**
     * Add custom route.
     *
     * @since 0.1.0
     */
    public function add_route() {
        add_rewrite_endpoint( self::$rewrite_slug, EP_ALL );
    }
    /**
     * Handle request.
     *
     * @since 0.1.0
     */
    public function handle() {
        global $wp_query;
        if ( ! isset( $wp_query->query_vars[self::$rewrite_slug] ) ) {
            return;
        }
        $route = $wp_query->query_vars[self::$rewrite_slug];
        $method = isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $method = strtoupper( $method );
        /**
         * Action handle request with endpoint
         *
         * @since 0.1.0
         */
        do_action( 'netbase_checkout_single_handle_request' );
        die();
    }
    public static function rewrite_slug(){
    	return self::$rewrite_slug;
    }
}NB_Router::instance();