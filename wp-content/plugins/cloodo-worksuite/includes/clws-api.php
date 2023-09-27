<?php 
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}
class Clws_API {
    public static function swap_json($json) {
        return json_decode($json, true);
    }
    public static function call_api_get($url) {
        $arrs = [
            'method' => 'GET',
            'timeout' => 10,
            'redirection' => 5,
            'blocking' => true,
            'cookie' => [],
            'headers' => [
                'X-requested-Width'=> 'XMLHttpRequest',
                'Authorization'=> 'Bearer '.sanitize_text_field(get_option('cloodo_token'))
            ],
            'body' => [
            ]
        ];
        $res = wp_remote_request($url, $arrs);
        return $res;
    }
    public static function call_api_post($url, $data) {
        $arrs = [
            'method' => 'POST',
            'timeout' => 10,
            'redirection' => 10,
            'blocking' => true,
            'cookie' => [],
            'headers' => [
                'X-requested-Width'=>'XMLHttpRequest',
                'Authorization'=>'Bearer '.sanitize_text_field(get_option('cloodo_token'))
            ],
            'body' => $data
        ];
        $res = wp_remote_request($url, $arrs);
        return $res;
    }
}