<?php
class NBT_WC_Currency_Switcher_Widgets extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'nbt_wc_currency_switcher_widgets',
            __( 'NB - WooCommerce Currency Switcher', 'netbase_solutions' ),
            array(
                'classname' => 'nbt-currency-switcher',
                'description' => __( 'WooCommerce Currency Switcher', 'netbase_solutions' )
            )
        );
    }
	
    public function widget($args, $instance) {             
        if (isset($args['before_widget'])){ echo $args['before_widget']; }
        ?>
        <div class="widget widget-woocommerce-currency-switcher">
            <?php
            if (!empty($instance['title']))
            {
                if (isset($args['before_title'])){
                    echo $args['before_title'];
                    echo $instance['title'];
                    echo $args['after_title'];
                } else{
                    ?>
                    <h3 class="widget-title"><?php echo $instance['title'] ?></h3>
                    <?php
                }
            }
            
            $txt_type = 'code';
            if (isset($instance['txt_type'])){
                $txt_type = $instance['txt_type'];
            }
            
            echo do_shortcode("[nbt_currency_switcher txt_type='{$txt_type}'  width='{$instance['width']}']");
            ?>
        </div>

        <?php
        if (isset($args['after_widget'])){ echo $args['after_widget']; }
    }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];        
        $instance['width'] = $new_instance['width'];        
        $instance['txt_type'] = $new_instance['txt_type'];

        return $instance;
    }

    public function form($instance) {
        $defaults = array(
            'title' => '',            
            'width' => '100%',            
            'txt_type' => 'code'
        );
        $instance = wp_parse_args((array) $instance, $defaults);
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'cmsaddons'); ?></label>
            <input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($instance['title']); ?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width', 'netbase_solutions') ?>:</label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" value="<?php echo $instance['width']; ?>" />
            <br /><i><?php _e('Examples: 300px,100%,auto', 'netbase_solutions') ?></i>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('txt_type'); ?>"><?php _e('Drop-down options text type', 'netbase_solutions') ?>:</label>
            <?php
            $sett = array(
                'code' => __('code', 'netbase_solutions'),
                'desc' => __('description', 'netbase_solutions'),
            );
            ?>
            <select class="widefat" id="<?php echo $this->get_field_id('txt_type') ?>" name="<?php echo $this->get_field_name('txt_type') ?>">
                <?php foreach ($sett as $k => $val) : ?>
                    <option <?php selected($instance['txt_type'], $k) ?> value="<?php echo $k ?>" class="level-0"><?php echo $val ?></option>
                <?php endforeach; ?>
            </select>            
        </p>
    <?php
    }
}

class NBT_WC_Currency_Converter_Widgets extends WP_Widget
{
    public function __construct() {
        parent::__construct(
            'nbt_wc_currency_converter_widgets',
            __( 'NB - WooCommerce Currency Converter', 'netbase_solutions' ),
            array(
                'classname' => 'nbt-currency-converter',
                'description' => __( 'WooCommerce Currency Converter', 'netbase_solutions' )
            )
        );
    }   

    public function widget($args, $instance){        
        global $NBTWCCS;
        if (isset($args['before_widget'])){
            echo $args['before_widget'];
        }
        ?>
        <div class="widget widget-woocommerce-currency-converter">
            <?php
            if (!empty($instance['title']))
            {
                if (isset($args['before_title']))
                {
                    echo $args['before_title'];
                    echo $instance['title'];
                    echo $args['after_title'];
                } else
                {
                    ?>
                    <h3 class="widget-title"><?php echo $instance['title'] ?></h3>
                    <?php
                }
            }
            echo do_shortcode('[nbtwccs_converter exclude="' . $instance['exclude'] . '" precision="' . $instance['precision'] . '"]'); 
            ?>
        </div>
        <?php
        if (isset($args['after_widget'])){ echo $args['after_widget']; }
    }

    public function update($new_instance, $old_instance){
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['exclude'] = $new_instance['exclude'];
        $instance['precision'] = $new_instance['precision'];
        return $instance;
    }

    public function form($instance){
        $defaults = array(
            'title' => '',
            'exclude' => '',
            'precision' => 4
        );
        $instance = wp_parse_args((array) $instance, $defaults);
        
        ?>
        <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'netbase_solutions') ?>:</label>
        <input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e('Currencies excluding from view', 'netbase_solutions') ?>:</label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" value="<?php echo $instance['exclude']; ?>" />
            <br /><i><?php _e('Examples: EUR,GBP,UAH', 'netbase_solutions') ?></i>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('precision'); ?>"><?php _e('Precision', 'netbase_solutions') ?>:</label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id('precision'); ?>" name="<?php echo $this->get_field_name('precision'); ?>" value="<?php echo $instance['precision']; ?>" />
            <br /><i><?php _e('Count of digits after point', 'netbase_solutions') ?></i>
        </p>
        <?php
    }

}

class NBT_WC_Currency_Rates_Widgets extends WP_Widget
{
    public function __construct() {
        parent::__construct(
            'nbt_wc_currency_rates_widgets',
            __( 'NB - WooCommerce Currency Rates', 'netbase_solutions' ),
            array(
                'classname' => 'nbt-currency-rates',
                'description' => __( 'WooCommerce Currency Rates', 'netbase_solutions' )
            )
        );
    }     

    public function widget($args, $instance){
        
        global $NBTWCCS;
        if (!empty($instance['title'])){
            if (isset($args['before_title']))
            {
                echo $args['before_title'];
                echo $instance['title'];
                echo $args['after_title'];
            } else{
                ?>
                <h3 class="widget-title"><?php echo $instance['title'] ?></h3>
                <?php
            }
        }
        echo do_shortcode('[nbtwccs_rates exclude="' . $instance['exclude'] . '" precision="' . $instance['precision'] . '"]');
    }

    public function update($new_instance, $old_instance){
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['exclude'] = $new_instance['exclude'];
        $instance['precision'] = $new_instance['precision'];
        return $instance;
    }

    public function form($instance)
    {
        $defaults = array(
            'title' => __('WooCommerce Currency Rates', 'netbase_solutions'),
            'exclude' => '',
            'precision' => 4
        );
        $instance = wp_parse_args((array) $instance, $defaults);
        ?>
        <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'netbase_solutions') ?>:</label>
        <input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e('Currencies excluding from view', 'netbase_solutions') ?>:</label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" value="<?php echo $instance['exclude']; ?>" />
            <br /><i><?php _e('Examples: EUR,GBP,UAH', 'netbase_solutions') ?></i>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('precision'); ?>"><?php _e('Precision', 'netbase_solutions') ?>:</label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id('precision'); ?>" name="<?php echo $this->get_field_name('precision'); ?>" value="<?php echo $instance['precision']; ?>" />
            <br /><i><?php _e('Count of digits after point', 'netbase_solutions') ?></i>
        </p>
        <?php
    }

}
?>