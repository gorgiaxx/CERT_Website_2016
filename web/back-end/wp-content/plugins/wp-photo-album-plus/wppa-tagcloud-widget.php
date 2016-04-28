<?php
/* wppa-tagcloud-widget.php
* Package: wp-photo-album-plus
*
* display the tagcloud widget
* Version 6.3.11
*
*/

class TagcloudPhotos extends WP_Widget {
    /** constructor */
    function __construct() {
		$widget_ops = array('classname' => 'wppa_tagcloud_photos', 'description' => __( 'WPPA+ Photo Tags', 'wp-photo-album-plus') );	//
		parent::__construct('wppa_tagcloud_photos', __('Photo Tag Cloud', 'wp-photo-album-plus'), $widget_ops);															//
    }

	/** @see WP_Widget::widget */
    function widget($args, $instance) {
		global $widget_content;

		require_once(dirname(__FILE__) . '/wppa-links.php');
		require_once(dirname(__FILE__) . '/wppa-styles.php');
		require_once(dirname(__FILE__) . '/wppa-functions.php');
		require_once(dirname(__FILE__) . '/wppa-thumbnails.php');
		require_once(dirname(__FILE__) . '/wppa-boxes-html.php');
		require_once(dirname(__FILE__) . '/wppa-slideshow.php');
		wppa_initialize_runtime();

		wppa( 'in_widget', 'tagcloud' );
		wppa_bump_mocc();

        extract( $args );

		$instance = wp_parse_args( (array) $instance, array( 'title' => __('Photo Tags', 'wp-photo-album-plus'), 'tags' => array() ) );
        if ( empty( $instance['tags'] ) ) $instance['tags'] = array();

 		$widget_title = apply_filters('widget_title', $instance['title']);

		// Display the widget
		echo $before_widget;

		if ( !empty( $widget_title ) ) { echo $before_title . $widget_title . $after_title; }

		echo '<div class="wppa-tagcloud-widget" >'.wppa_get_tagcloud_html(implode(',', $instance['tags']), wppa_opt('tagcloud_min'), wppa_opt('tagcloud_max') ).'</div>';

		echo '<div style="clear:both"></div>';
		echo $after_widget;

		wppa( 'in_widget', false );
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['tags'] = $new_instance['tags'];
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => __('Photo Tags', 'wp-photo-album-plus'), 'tags' => '' ) );
		$title = $instance['title'];
		$stags = $instance['tags'];
		if ( ! $stags ) $stags = array();

		echo '<p><label for="' . $this->get_field_id('title') . '">' . __('Title:', 'wp-photo-album-plus') . '</label><input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></p>';
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

} // class TagcloudPhotos

// register Photo Tags widget
add_action('widgets_init', 'wppa_register_TagcloudPhotos' );

function wppa_register_TagcloudPhotos() {
	register_widget("TagcloudPhotos");
}