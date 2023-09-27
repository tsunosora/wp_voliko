<?php
class NBT_LCHAT_Frontend {
	protected $currency = array();
	protected $current_currency = '';
	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action('wp_footer', array($this, 'embed_code_live_chat'));

	}

	public function embed_code_live_chat(){
		$data = get_option(NBT_Solutions_Live_Chat::$plugin_id.'_settings');

		if( $this->exclude_page($data) ) {
			return;
		}


		if(!empty($data['nbt_live-chat_embed_code']) && $data['nbt_live-chat_always_show']){
			echo str_replace('\\', '', $data['nbt_live-chat_embed_code']);
		}
	}

	public function exclude_page($data) {
		global $wp_query, $wp_rewrite;

		$excludes = ! empty( $data['nbt_live-chat_exclude_url'] ) ? str_replace('(*)', '(.*)', $data['nbt_live-chat_exclude_url']) : false;

		if( $excludes ) {
			$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
			$excludes = str_replace('/', '\/', $excludes);
			$urls = preg_split( '/\r\n|\r|\n/', $excludes );

			if( ! empty($urls) ) {
				foreach ( $urls as $key => $url ) {
					if( preg_match('/^' . $url . '$/i', $current_url, $output_array) ) {
						return true;
					}
				}
			}
		}

		return false;
	}

}
new NBT_LCHAT_Frontend();