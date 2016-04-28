<?php
/* wppa-admins-choice-widget.php
* Package: wp-photo-album-plus
*
* display the admins-choice widget
* Version 6.4.06
*
*/

class AdminsChoice extends WP_Widget {
    /** constructor */
    function __construct() {
		$widget_ops = array('classname' => 'wppa_admins_choice', 'description' => __( 'Admins choice of photos', 'wp-photo-album-plus') );	//
		parent::__construct('wppa_admins_choice', __('Admins Choice', 'wp-photo-album-plus'), $widget_ops);															//
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

		wppa( 'in_widget', 'admins-choice' );
		wppa_bump_mocc();

        extract( $args );

		$instance = wp_parse_args( (array) $instance, array( 'title' => __('Admins Choice', 'wp-photo-album-plus'), 'tags' => array() ) );

 		$widget_title = apply_filters('widget_title', $instance['title']);

		// Display the widget
		echo $before_widget;

		if ( !empty( $widget_title ) ) { echo $before_title . $widget_title . $after_title; }

		echo '<div class="wppa-admins-choice-widget" >'.wppa_get_admins_choice_html().'</div>';

		echo '<div style="clear:both"></div>';
		echo $after_widget;

		wppa( 'in_widget', false );
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {

		// Make sure the feature is enabled
		if ( ! wppa_switch( 'enable_admins_choice' ) ) {
			wppa_update_option( 'wppa_enable_admins_choice', 'yes' );
		}

		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => __('Admins Choice', 'wp-photo-album-plus'), 'tags' => '' ) );
		$title = $instance['title'];

		echo 	'<p>' .
					'<label for="' . $this->get_field_id('title') . '">' .
						__('Title:', 'wp-photo-album-plus') .
					'</label>' .
					'<input' .
						' class="widefat"' .
						' id="' . $this->get_field_id('title') . '"' .
						' name="' . $this->get_field_name('title') . '"' .
						' type="text"' .
						' value="' . $title . '"' .
					' />' .
				'</p>';
    }

} // class AdminsChoice

// register Admins Choice widget
add_action('widgets_init', 'wppa_register_AdminsChoice' );

function wppa_register_AdminsChoice() {
	register_widget("AdminsChoice");
}