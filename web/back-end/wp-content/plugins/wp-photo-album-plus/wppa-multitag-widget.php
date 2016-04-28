<?php
/* wppa-multitag-widget.php
* Package: wp-photo-album-plus
*
* display the multitag widget
* Version 6.3.11
*
*/

class MultitagPhotos extends WP_Widget {
    /** constructor */
    function __construct() {
		$widget_ops = array('classname' => 'wppa_multitag_photos', 'description' => __( 'WPPA+ Photo Tags', 'wp-photo-album-plus') );	//
		parent::__construct('wppa_multitag_photos', __('Photo Tags Filter', 'wp-photo-album-plus'), $widget_ops);															//
    }

	/** @see WP_Widget::widget */
    function widget($args, $instance) {
		global $widget_content;

		wppa( 'in_widget', 'multitag' );
		wppa_bump_mocc();

		require_once(dirname(__FILE__) . '/wppa-links.php');
		require_once(dirname(__FILE__) . '/wppa-styles.php');
		require_once(dirname(__FILE__) . '/wppa-functions.php');
		require_once(dirname(__FILE__) . '/wppa-thumbnails.php');
		require_once(dirname(__FILE__) . '/wppa-boxes-html.php');
		require_once(dirname(__FILE__) . '/wppa-slideshow.php');
		wppa_initialize_runtime();

        extract( $args );

		$instance = wp_parse_args( (array) $instance, array( 'title' => __('Photo Tags', 'wp-photo-album-plus'), 'cols' => '2', 'tags' => array() ) );

 		$widget_title = apply_filters('widget_title', $instance['title']);

		// Display the widget
		echo $before_widget;

		if ( !empty( $widget_title ) ) { echo $before_title . $widget_title . $after_title; }

		$tags = is_array( $instance['tags'] ) ? implode( ',', $instance['tags'] ) : '';

		echo '<div class="wppa-multitag-widget" >'.wppa_get_multitag_html( $instance['cols'], $tags ).'</div>';
		echo '<div style="clear:both"></div>';
		echo $after_widget;

		wppa( 'in_widget', false );
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$cols = $new_instance['cols'];
		if ( ! is_numeric($cols) || $cols < '1' ) $cols = '2';
		$instance['cols'] = $cols;
		$instance['tags'] = $new_instance['tags'];
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => __('Photo Tags', 'wp-photo-album-plus'), 'cols' => '2', 'tags' => '' ) );
		$title = $instance['title'];
		$cols = $instance['cols'];
		$stags = $instance['tags'];
		if ( ! $stags ) $stags = array();

		echo '<p><label for="' . $this->get_field_id('title') . '">' . __('Title:', 'wp-photo-album-plus') . '</label><input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></p>';
		echo '<p><label for="' . $this->get_field_id('cols') . '">' . __('No of columns:', 'wp-photo-album-plus') . '</label><input class="widefat" id="' . $this->get_field_id('cols') . '" name="' . $this->get_field_name('cols') . '" type="text" value="' . $cols . '" /></p>';
		echo '<p><label for="' . $this->get_field_id('tags') . '">' . __('Select multiple tags or --- all ---:', 'wp-photo-album-plus') . '</label><br />';
			echo '<select class="widefat" id="' . $this->get_field_id('tags') . '" name="' . $this->get_field_name('tags') . '[]" multiple="multiple" >'.
					'<option value="" >'.__('--- all ---', 'wp-photo-album-plus').'</option>';
						$tags = wppa_get_taglist();
						if ( $tags ) foreach ( array_keys($tags) as $tag ) {
							if ( in_array($tag, $stags) ) $sel = ' selected="selected"'; else $sel = '';
							echo '<option value="'.$tag.'"'.$sel.' >'.$tag.'</option>';
						}
			echo '</select>';
		echo '</p>';
		if ( isset($instance['tags']['0']) && $instance['tags']['0'] ) $s = implode(',', $instance['tags']); else $s = __('--- all ---', 'wp-photo-album-plus');
		echo '<p>Currently selected tags: <br /><b>'.$s.'</b></p>';
    }

} // class MultitagPhotos

// register Photo Tags widget
add_action('widgets_init', 'wppa_register_MultitagPhotos' );

function wppa_register_MultitagPhotos() {
	register_widget("MultitagPhotos");
}
