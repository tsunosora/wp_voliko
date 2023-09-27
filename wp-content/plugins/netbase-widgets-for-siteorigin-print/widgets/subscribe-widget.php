<?php
/**
 * Create class Subscribe Form
 */
class Wpnetbase_Subscribe_Widget extends WP_Widget {

        /**
         * setting: name, base ID
         */
        function __construct() {
          parent::__construct(
          /*Base ID of your widget*/ 
          'wpnetbase_subscribe_widget', 

          /*Widget name will appear in UI*/ 
          __('NBT Subscribe', 'wpnetbase'), 

          /*Widget description*/ 
          array( 'description' => __( 'Subscribe mailchimp widget.', 'wpnetbase' ),  'panels_groups' => array('netbaseteam') ) 
          );
        }

        /**
         * form option widget
         */
        function form( $instance ) {
              $default = array(
             'title_small' => __('Newsletter','wpnetbase'),
           
            );
            $instance = wp_parse_args( (array) $instance, $default );
            $title_small = esc_attr($instance['title_small'] );
              echo '<p>Title small <input type="text" class="widefat" name="'.$this->get_field_name('title_small').'" value="'.$title_small.'"/></p>';
          
        }
        /**
         * save widget form
         */

        function update( $new_instance, $old_instance ) {

            parent::update( $new_instance, $old_instance );
            $instance = $old_instance;
            $instance['title_small'] = strip_tags($new_instance['title_small']);
            return $instance;
        }

        /**
         * Show widget
         */
        function widget( $args, $instance ) {
            extract( $args );
             $widget_width = !empty($instance['widget_width']) ? $instance['widget_width'] : "subscribe-widget";
              /* Add the width from $widget_width to the class from the $before widget */
              // no 'class' attribute - add one with the value of width
              if( strpos($before_widget, 'class') === false ) {
                $before_widget = str_replace('>', 'class="'. $widget_width . '"', $before_widget);
              }
              // there is 'class' attribute - append width value to it
              else {
                $before_widget = str_replace('class="', 'class="'. $widget_width . ' ', $before_widget);
              }
              /* Before widget */
            $title_small = apply_filters( 'widget_title_small', $instance['title_small'] );
           ?>

                <?php echo $before_widget; ?>
            <h3 class="widget-title"><?php echo esc_html($title_small); ?></h3>
            <div class="sidebar-content-widget">
              <div class="subcriber-widget subcriber">                

                <span class="title subcribe-message"></span>
                 <form id="printshop-subcribe" class="form-inline" method="get">
                    <div class="form-group">
                      <input type="text" name="email-subcriber" class="form-control" id="email-subcriber" placeholder="<?php echo esc_html_e('Your e-mail...','wpnetbase'); ?>">
                    </div>
                    <button type="submit" class="btn btn-default"><?php esc_html_e('Subscribe','wpnetbase') ?></button>
                 </form>

              </div>
            </div>

                <?php
            printf('%s',$after_widget);?>
       <?php }
}

add_action( 'widgets_init', 'create_subscribe_widget' );
function create_subscribe_widget() {
        register_widget('Wpnetbase_Subscribe_Widget');
}