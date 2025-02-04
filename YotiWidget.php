<?php

class YotiWidget extends WP_Widget
{
    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        parent::__construct(
            'yoti_connect', // Base ID
            esc_html__('Yoti Connect'), // Name
            array('description' => 'Yoti Connect button')
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance)
    {
        wp_enqueue_style('yoti-connect', plugin_dir_url(__FILE__) . 'assets/styles.css');
        $config = YotiConnectHelper::getConfig();
        if (!empty($config['yoti_sdk_id']) && !empty($config['yoti_pem']['contents'])) {
            echo '<div class="yoti-connect-button">' . YotiConnectButton::render() . '</div>';
        }
        else {
            echo '<div class="yoti-missing-config"><p><strong>Yoti Connect not configured.</strong></p></div>';
        }

//        echo $args['before_widget'];
//        if (!empty($instance['title'])) {
//            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
//        }
//        echo esc_html__('Hello, World!', 'text_domain');
//        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('New title', 'text_domain');
        ?>
      <p>
		<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_attr_e('Title:', 'text_domain'); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
		</p>
        <?php
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
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';

        return $instance;
    }
}