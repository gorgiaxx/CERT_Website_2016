<?php
/* wppa-super-view-widget.php
* Package: wp-photo-album-plus
*
* ask the album / display you want
* Version 6.3.11
*/


class WppaSuperView extends WP_Widget {
    /** constructor */
    function __construct() {
		$widget_ops = array('classname' => 'wppa_super_view', 'description' => __( 'WPPA+ Selectable display', 'wp-photo-album-plus') );	//
		parent::__construct('wppa_super_view', __('Super View Photos', 'wp-photo-album-plus'), $widget_ops);															//
    }

	/** @see WP_Widget::widget */
    function widget($args, $instance) {
		global $wpdb;
		global $widget_content;

		require_once(dirname(__FILE__) . '/wppa-links.php');
		require_once(dirname(__FILE__) . '/wppa-styles.php');
		require_once(dirname(__FILE__) . '/wppa-functions.php');
		require_once(dirname(__FILE__) . '/wppa-thumbnails.php');
		require_once(dirname(__FILE__) . '/wppa-boxes-html.php');
		require_once(dirname(__FILE__) . '/wppa-slideshow.php');
		wppa_initialize_runtime();

        extract( $args );
		$instance = wp_parse_args( (array) $instance, array(
														'title' => '',
														'root'	=> '0',
														'sort'	=> true,
														) );

 		$widget_title 	= apply_filters('widget_title', $instance['title'] );
		$album_root 	= $instance['root'];
		$sort 			= $instance['sort'];

		wppa( 'in_widget', 'superview' );
		wppa_bump_mocc();

		$widget_content = wppa_get_superview_html( $album_root, $sort );

		wppa( 'in_widget', false );

		echo $before_widget . $before_title . $widget_title . $after_title . $widget_content . $after_widget;
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] 	= strip_tags($new_instance['title']);
		$instance['root'] 	= $new_instance['root'];
		$instance['sort']	= $new_instance['sort'];

        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 	'title' => __( 'Super View Photos' , 'wp-photo-album-plus'),
																'root' 	=> '0',
																'sort'	=> true
															) );
		$title 	= esc_attr( $instance['title'] );
		$root 	= $instance['root'];
		$sort 	= $instance['sort'];
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp-photo-album-plus'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('root'); ?>"><?php _e('Enable (sub)albums of:', 'wp-photo-album-plus'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('root'); ?>" name="<?php echo $this->get_field_name('root'); ?>" >
				<?php echo wppa_album_select_a( array( 'selected' => $root, 'addall' => true, 'addseparate' => true, 'addgeneric' => true, 'path' => true ) ) ?>
			</select>
		</p>
			<label for="<?php echo $this->get_field_id('sort'); ?>"><?php _e('Sort alphabeticly:', 'wp-photo-album-plus'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('sort'); ?>" name="<?php echo $this->get_field_name('sort'); ?>" >
				<option value="0" ><?php _e('no, use album sort method', 'wp-photo-album-plus') ?></option>
				<option value="1" <?php if ( $sort ) echo 'selected="selected"' ?> ><?php _e('yes', 'wp-photo-album-plus') ?></option>
			</select>
		<p>
		</p>
<?php
    }

} // class WppaSuperView

// register WppaSuperView widget
add_action('widgets_init', 'wppa_register_wppaSuperView' );

function wppa_register_wppaSuperView() {
	register_widget("WppaSuperView");
}
