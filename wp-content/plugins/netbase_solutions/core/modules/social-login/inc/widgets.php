<?php
defined( 'ABSPATH' ) or die( "No script kiddies please!" );

class NBT_Social_Login_Widgets extends WP_Widget {
    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct( 'nbtsl_widget', // Base ID
        __( 'NB - Social Login', 'nbt-solution' ), // Name
        array('description' => __( 'Social Login Widget', 'nbt-solution' )) // Args
         );
    }
    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        if( isset( $instance['title'] ) ) {
            $title = $instance['title'];
        }else {
            $title = '';
        }

        if( isset( $instance['login_text'] ) ) {
            $login_text = $instance['login_text'];
        }else {
            $login_text = '';
        }
?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title: ', 'nbt-solution' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'login_text' ); ?>"><?php _e( 'Login Text: ', 'nbt-solution' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'login_text' ); ?>" name="<?php echo $this->get_field_name( 'login_text' ); ?>" type="text" value="<?php echo esc_attr( $login_text ); ?>">
        </p>
        <?php
    }
    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if( !empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        global $post;
        if( have_posts() ) {
            $widget_flag = get_post_meta( $post->ID, 'nbtsl_widget_flag', true );
        }else {
            $widget_flag = 0;
        }
        if( $widget_flag != '1' ) {
            echo "<div class='nbtsl-widget'>";
            echo do_shortcode( "[nbtsl-login-lite login_text='{$instance['login_text']}']" );
            echo "</div>";
        }
        echo $args['after_widget'];
    }
    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] =( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['login_text'] =( !empty( $new_instance['login_text'] ) ) ? strip_tags( $new_instance['login_text'] ) : '';
        return $instance;
    }
}