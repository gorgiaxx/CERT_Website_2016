<?php
/* wppa-qr-widget.php
* Package: wp-photo-album-plus
*
* display qr code
* Version 6.3.11
*/


class wppaQRWidget extends WP_Widget {
    /** constructor */
    function __construct() {
		$widget_ops = array('classname' => 'qr_widget', 'description' => __( 'WPPA+ QR Widget' , 'wp-photo-album-plus') );	//
		parent::__construct('qr_widget', __('QR Widget', 'wp-photo-album-plus'), $widget_ops);															//
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

 		$title 			= apply_filters('widget_title', empty( $instance['title'] ) ? __( 'QR Widget' , 'wp-photo-album-plus') : $instance['title']);
		$qrsrc 			= 'http://api.qrserver.com/v1/create-qr-code/' .
							'?data=' . site_url() .
							'&amp;size='. wppa_opt( 'qr_size' ).'x'.wppa_opt( 'qr_size' ) .
							'&amp;color='.trim( wppa_opt( 'qr_color' ), '#' ) .
							'&amp;bgcolor='.trim( wppa_opt( 'qr_bgcolor' ) );
		$widget_content = '
		<div style="text-align:center;" ><img id="wppa-qr-img" src="' . $qrsrc . '" title="" alt="' . __('QR code', 'wp-photo-album-plus') . '" /></div>
		<div style="clear:both" ></div>';

		$widget_content .= '
		<script type="text/javascript">
			/*[CDATA[*/
			var wppaQRData = document.location.href;
			var wppaQRDataOld = "";
			var wppaQRSrc = "";
			var workData = "";

			wppaConsoleLog( "doc.loc.href = " + wppaQRData );

			function wppaQRUpdate( arg ) {
				if ( arg ) wppaQRData = arg;
				if ( wppaQRData != wppaQRDataOld ) {
					wppaQRDataOld 	= wppaQRData;
					workData 		= wppaQRData;
					wppaQRSrc 		= "http://api.qrserver.com/v1/create-qr-code/?data=" +
										encodeURIComponent( workData ) +
										"&size=' . wppa_opt( 'qr_size' ) .
											'x' . wppa_opt( 'qr_size' ) .
											'&color=' . trim( wppa_opt( 'qr_color' ), '#' ) .
											'&bgcolor='.trim( wppa_opt( 'qr_bgcolor' ), '#' ) .
										'";
					document.getElementById( "wppa-qr-img" ).src = wppaQRSrc;
					document.getElementById( "wppa-qr-img" ).title = workData;
				}
				return;
			}

			jQuery(document).ready(function(){
				wppaQRUpdate();
			});
			/*]]*/
		</script>';


		echo $before_widget . $before_title . $title . $after_title . $widget_content . $after_widget;
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);

        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'sortby' => 'post_title', 'title' => '') );
		$title = esc_attr( $instance['title'] );
	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'xxx', 'wp-photo-album-plus'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p><?php _e('You can set the sizes and colors in this widget in the <b>Photo Albums -> Settings</b> admin page.', 'wp-photo-album-plus'); ?></p>

<?php
    }

} // class wppaQRWidget

// register wppaQRWidget widget
add_action('widgets_init', create_function('', 'return register_widget("wppaQRWidget");'));
?>
