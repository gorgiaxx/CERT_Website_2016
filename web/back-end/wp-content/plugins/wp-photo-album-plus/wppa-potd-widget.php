<?php
/* wppa-potd-widget.php
* Package: wp-photo-album-plus
*
* display the widget
* Version 6.3.17
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

class PhotoOfTheDay extends WP_Widget {
    /** constructor */
    function __construct() {
		$widget_ops = array('classname' => 'wppa_widget', 'description' => __( 'WPPA+ Photo Of The Day', 'wp-photo-album-plus') );	//
		parent::__construct('wppa_widget', __('Photo Of The Day', 'wp-photo-album-plus'), $widget_ops);															//
    }

	/** @see WP_Widget::widget */
    function widget($args, $instance) {
		global $wpdb;

		wppa( 'in_widget', 'potd' );
		wppa_bump_mocc();

		require_once(dirname(__FILE__) . '/wppa-links.php');
		require_once(dirname(__FILE__) . '/wppa-styles.php');
		require_once(dirname(__FILE__) . '/wppa-functions.php');
		require_once(dirname(__FILE__) . '/wppa-thumbnails.php');
		require_once(dirname(__FILE__) . '/wppa-boxes-html.php');
		require_once(dirname(__FILE__) . '/wppa-slideshow.php');
		wppa_initialize_runtime();

        extract( $args );

		$widget_title = apply_filters('widget_title', $instance['title']);

		// get the photo  ($image)
		$image = wppa_get_potd();

		// Make the HTML for current picture
		$widget_content = "\n".'<!-- WPPA+ Photo of the day Widget start -->';

		$ali = wppa_opt( 'potd_align' );
		if ( $ali != 'none' ) {
			$align = 'text-align:'.$ali.';';
		}
		else $align = '';

		$widget_content .= "\n".'<div class="wppa-widget-photo" style="'.$align.' padding-top:2px; ">';

		if ( $image ) {

			$id 		= $image['id'];
			$w 			= wppa_opt( 'potd_widget_width' );
			$ratio 		= wppa_get_photoy( $id ) / wppa_get_photox( $id );
			$h 			= round( $w * $ratio );
			$usethumb	= wppa_use_thumb_file( $id, wppa_opt( 'widget_width' ), '0' );
			$imgurl 	= wppa_fix_poster_ext( $usethumb ? wppa_get_thumb_url( $id, '', $w, $h ) : wppa_get_photo_url( $id, '', $w, $h ), $id );
			$name 		= wppa_get_photo_name( $id );
			$page 		= in_array( wppa_opt( 'widget_linktype' ), wppa( 'links_no_page' ) ) ? '' : wppa_get_the_landing_page( 'wppa_widget_linkpage', __('Photo of the day', 'wp-photo-album-plus') );
			$link 		= wppa_get_imglnk_a( 'potdwidget' , $id );
			$is_video 	= wppa_is_video( $id );
			$has_audio 	= wppa_has_audio( $id );

			if ( $link['is_lightbox'] ) {
				$lightbox = ( $is_video ? ' data-videohtml="' . esc_attr( wppa_get_video_body( $id ) ) . '"' .
							' data-videonatwidth="'.wppa_get_videox( $id ).'"' .
							' data-videonatheight="'.wppa_get_videoy( $id ).'"' : '' ) .
							( $has_audio ? ' data-audiohtml="' . esc_attr( wppa_get_audio_body( $id ) ) . '"' : '' ) .
							' ' . wppa( 'rel' ) . '="' . wppa_opt( 'lightbox_name' ) . '"';
			}
			else {
				$lightbox = '';
			}

			if ( $link ) {
				if ( $link['is_lightbox'] ) {
					$cursor = ' cursor:url('.wppa_get_imgdir().wppa_opt('wppa_magnifier').'),pointer;';
					$title  = wppa_zoom_in( $id );
					$ltitle = wppa_get_lbtitle('potd', $id);
				}
				else {
					$cursor = ' cursor:pointer;';
					$title  = $link['title'];
					$ltitle = $title;
				}
			}
			else {
				$cursor = ' cursor:default;';
				$title = esc_attr(stripslashes(__($image['name'], 'wp-photo-album-plus')));
			}

			// The medal if on top
			$widget_content .= wppa_get_medal_html_a( array( 'id' => $id, 'size' => 'M', 'where' => 'top' ) );

			// The link, if any
			if ($link) $widget_content .= "\n\t".'<a href = "'.$link['url'].'" target="'.$link['target'].'" '.$lightbox.' ' . wppa( 'lbtitle' ) . '="'.$ltitle.'">';

				// The image
				if ( wppa_is_video( $id ) ) {
					$widget_content .= "\n\t\t".wppa_get_video_html( array ( 	'id' 		=> $id,
																				'width' 	=> wppa_opt('wppa_potd_widget_width'),
																				'title' 	=> $title,
																				'controls' 	=> ( wppa_opt( 'widget_linktype' ) == 'none' ),
																				'cursor' 	=> $cursor
																	));
				}
				else {
					$widget_content .= 	'<img' .
											' src="'.$imgurl.'"' .
											' style="width: '.wppa_opt('wppa_potd_widget_width').'px;'.$cursor.'"' .
											' ' . wppa_get_imgalt( $id ) .
											( $title ? 'title="' . $title . '"' : '' ) .
											' />';
				}

			// Close the link
			if ($link) $widget_content .= "\n\t".'</a>';

			// The medal if at the bottom
			$widget_content .= wppa_get_medal_html_a( array( 'id' => $id, 'size' => 'M', 'where' => 'bot' ) );

			// Audio
			if ( wppa_has_audio( $id ) ) {
				$widget_content .= wppa_get_audio_html( array ( 	'id' 		=> $id,
																	'width' 	=> wppa_opt('wppa_potd_widget_width'),
																	'controls' 	=> true
													));
			}

		}
		else {	// No image
			$widget_content .= __( 'Photo not found', 'wp-photo-album-plus' );
		}
		$widget_content .= "\n".'</div>';

		// Add subtitle, if any
		switch ( wppa_opt( 'widget_subtitle' ) ) {
			case 'none':
				break;
			case 'name':
				if ($image && $image['name'] != '') {
					$widget_content .= "\n".'<div class="wppa-widget-text wppa-potd-text" style="'.$align.'">' . wppa_get_photo_name( $id ) . '</div>';
				}
				break;
			case 'desc':
				if ($image && $image['description'] != '') {
					$widget_content .= "\n".'<div class="wppa-widget-text wppa-potd-text" style="'.$align.'">' . wppa_get_photo_desc( $id ) . '</div>';
				}
				break;
			case 'owner':
				if ( $image ) {
					$owner = $image['owner'];
					$user = get_user_by('login', $owner);
					$owner = $user->display_name;
					$widget_content .= "\n".'<div class="wppa-widget-text wppa-potd-text" style="'.$align.'">'.__('By:', 'wp-photo-album-plus').' ' . $owner . '</div>';
				}
		}

		$widget_content .= '<div style="clear:both;" ></div>';

		$widget_content .= "\n".'<!-- WPPA+ Photo of the day Widget end -->';

		echo "\n" . $before_widget;
		if ( !empty( $widget_title ) ) { echo $before_title . $widget_title . $after_title; }
		echo $widget_content . $after_widget;

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

		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => wppa_opt('wppa_widgettitle') ) );
		$widget_title = $instance['title'];
		?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp-photo-album-plus'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $widget_title; ?>" /></p>
			<p><?php _e('You can set the content and the sizes in this widget in the <b>Photo Albums -> Sidebar Widget</b> admin page.', 'wp-photo-album-plus'); ?></p>
		<?php
    }

} // class PhotoOfTheDay

require_once ('wppa-widget-functions.php');

// register PhotoOfTheDay widget
add_action('widgets_init', 'wppa_register_PhotoOfTheDay' );

function wppa_register_PhotoOfTheDay() {
	register_widget("PhotoOfTheDay");
}
