<?php
/* wppa-upload-widget.php
* Package: wp-photo-album-plus
*
* upload wppa+ widget
*
* A wppa widget to upload photos
*
* Version 6.3.11
*/

class WppaUploadWidget extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'wppa_upload_widget', 'description' => __('WPPA+ Upload photos widget', 'wp-photo-album-plus'));
		parent::__construct('wppa_upload_widget', __('WPPA+ Upload', 'wp-photo-album-plus'), $widget_ops);
	}

	function widget( $args, $instance ) {
		global $wpdb;

		require_once(dirname(__FILE__) . '/wppa-links.php');
		require_once(dirname(__FILE__) . '/wppa-styles.php');
		require_once(dirname(__FILE__) . '/wppa-functions.php');
		require_once(dirname(__FILE__) . '/wppa-thumbnails.php');
		require_once(dirname(__FILE__) . '/wppa-boxes-html.php');
		require_once(dirname(__FILE__) . '/wppa-slideshow.php');
		wppa_initialize_runtime();

		extract($args);
		$instance = wp_parse_args( (array) $instance,
									array( 	'title' 	=> '',
											'album' 	=> '0'
										));
 		$title = apply_filters('widget_title', $instance['title']);
		$album = $instance['album'];

		if ( ! $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_ALBUMS."` WHERE `id` = %d", $album ) ) ) {
			$album = '0';	// Album vanished
		}

		wppa_user_upload();	// Do the upload if required

		wppa( 'in_widget', 'upload' );
		wppa_bump_mocc();

		$create = wppa_get_user_create_html( $album, wppa_opt( 'widget_width' ), 'widget' );
		$upload = wppa_get_user_upload_html( $album, wppa_opt( 'widget_width' ), 'widget' );

		if ( ! $create && ! $upload ) return;	// Nothing to do

		$text = '<div class="wppa-upload-widget" style="margin-top:2px; margin-left:2px;" >'.$create.$upload.'</div>';

		echo $before_widget;
		if ( ! empty( $title ) ) { echo $before_title . $title . $after_title; }
		echo $text;
		echo '<div style="clear:both"></div>';
		echo $after_widget;

		wppa( 'in_widget', false );
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['album'] = strval( intval( $new_instance['album'] ) );
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => __('Upload Photos', 'wp-photo-album-plus'), 'album' => '0' ) );
		$title = $instance['title'];
		$album = $instance['album'];
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp-photo-album-plus'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('album'); ?>"><?php _e('Album:', 'wp-photo-album-plus'); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id('album'); ?>" name="<?php echo $this->get_field_name('album'); ?>" >
			<?php echo wppa_album_select_a(array('path' => wppa_switch('wppa_hier_albsel'), 'selected' => $album, 'addselbox' => true)) ?>
		</select>
<?php
	}
}
// register WppaUploadWidget
add_action('widgets_init', 'wppa_register_WppaUploadWidget' );

function wppa_register_WppaUploadWidget() {
	register_widget("WppaUploadWidget");
}