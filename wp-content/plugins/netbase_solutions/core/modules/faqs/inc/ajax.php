<?php
class NBT_FAQs_Ajax{

	protected static $initialized = false;
	
    /**
     * Initialize functions.
     *
     * @return  void
     */
    public static function initialize() {
        if ( self::$initialized ) {
            return;
        }

	    self::admin_hooks();
        self::$initialized = true;
    }


    public static function admin_hooks(){
		add_action( 'wp_ajax_nopriv_load_global_faqs', array( __CLASS__, 'load_global_faqs') );
		add_action( 'wp_ajax_load_global_faqs', array( __CLASS__, 'load_global_faqs') );
    }

    public static function load_global_faqs(){
    	global $post;

		$faqs = absint($_REQUEST['faqs'] );
		$heading = $_REQUEST['heading'];
		if(is_numeric($faqs)){
			$datas = get_post_meta($faqs, '_nbt_faq', true);
			if($datas){
				$option = '';

				foreach ($datas as $key => $value) {
					if(!$heading){
						if($value['heading']){
							$option .= '<optgroup label="'.$value['heading'].'">';
						}
						foreach ($value['lists'] as $key2 => $value2) {
							$option .= '<option value="'.$key.'_'.$key2.'">&nbsp;&nbsp;&nbsp;'.$value2['faq_title'].'</option>';
						}
						if($value['heading']){
							$option .= '</optgroup>';
						}
					}else{
						if($value['heading']){
							$option .= '<option value="'.$key.'">'.$value['heading'].'</option>';
						}
					}
					


					
				}
				$json['complete'] = true;
				$json['option'] = $option;

			}else{
				$json['msg'] = '';
			}

			
		}else{
			$json['msg'] = '';
		}



		echo wp_json_encode($json, TRUE);

    	wp_die();
    }


}