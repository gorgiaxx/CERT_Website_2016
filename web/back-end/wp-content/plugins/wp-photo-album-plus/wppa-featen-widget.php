<?php
/* wppa-featen-widget.php
* Package: wp-photo-album-plus
*
* display the featured photos
* Version 6.4.00
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

class FeaTenWidget extends WP_Widget {
    /** constructor */
    function __construct() {
		$widget_ops = array('classname' => 'wppa_featen_widget', 'description' => __( 'WPPA+ Featured Photos', 'wp-photo-album-plus') );
		parent::__construct('wppa_featen_widget', __('Featured Photos', 'wp-photo-album-plus'), $widget_ops);
    }

	/** @see WP_Widget::widget */
    function widget($args, $instance) {
		global $wpdb;
		global $wppa_opt;

 		require_once(dirname(__FILE__) . '/wppa-links.php');
		require_once(dirname(__FILE__) . '/wppa-styles.php');
		require_once(dirname(__FILE__) . '/wppa-functions.php');
		require_once(dirname(__FILE__) . '/wppa-thumbnails.php');
		require_once(dirname(__FILE__) . '/wppa-boxes-html.php');
		require_once(dirname(__FILE__) . '/wppa-slideshow.php');
		wppa_initialize_runtime();

		extract( $args );
		wppa( 'in_widget', 'featen' );

		$instance 		= wp_parse_args( (array) $instance, array( 'title' => '', 'album' => '' ) );
 		$widget_title	= apply_filters( 'widget_title', $instance['title'] );
		$page 			= in_array( wppa_opt( 'featen_widget_linktype' ), wppa( 'links_no_page' ) ) ?
							'' :
							wppa_get_the_landing_page('wppa_featen_widget_linkpage', __('Featured photos', 'wp-photo-album-plus'));
		$max 			= wppa_opt( 'featen_count' );
		$album 			= $instance['album'];
		$generic 		= ( $album == '-2' );

//		wppa( 'start_album', $album );

//		if ( $generic ) {
//			$album = '0';
//			$max += '1000';
//		}

		switch( $album ) {
			// owner/public
			case '-3':
				$temp = $wpdb->get_results( "SELECT * FROM `".WPPA_PHOTOS."` WHERE `status` = 'featured' ORDER BY RAND(".wppa_get_randseed().") DESC", ARRAY_A );
				if ( $temp ) {
					$c = '0';
					$thumbs = array();
					while ( $c < $max && $c < count( $temp ) ) {
						$alb = wppa_get_photo_item( $temp[$c]['id'], 'album' );
						$own = wppa_get_album_item( $alb, 'owner' );
						if ( $own == '---public---' || $own == wppa_get_user() ) {
							$thumbs[] = $temp[$c];
						}
						$c++;
					}
				}
				else {
					$thumbs = false;
				}
				break;
			// generic
			case '-2':
				$temp = $wpdb->get_results( "SELECT * FROM `".WPPA_PHOTOS."` WHERE `status` = 'featured' ORDER BY RAND(".wppa_get_randseed().") DESC", ARRAY_A );
				if ( $temp ) {
					$c = '0';
					$thumbs = array();
					while ( $c < $max && $c < count( $temp ) ) {
						$alb = wppa_get_photo_item( $temp[$c]['id'], 'album' );
						if ( ! wppa_is_separate( $alb ) ) {
							$thumbs[] = $temp[$c];
						}
						$c++;
					}
				}
				else {
					$thumbs = false;
				}
				break;
			// all
			case '0':
				$thumbs = $wpdb->get_results( "SELECT * FROM `".WPPA_PHOTOS."` WHERE `status` = 'featured' ORDER BY RAND(".wppa_get_randseed().") DESC LIMIT " . $max, ARRAY_A );
				break;
			// album spec
			default:
				$thumbs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `".WPPA_PHOTOS."` WHERE `status`= 'featured' AND `album` = %s ORDER BY RAND(".wppa_get_randseed().") DESC LIMIT " . $max, $album ), ARRAY_A );
		}

		$widget_content = "\n".'<!-- WPPA+ FeaTen Widget start -->';
		$maxw 			= wppa_opt( 'featen_size' );
		$maxh 			= $maxw;
		$lineheight 	= wppa_opt( 'fontsize_widget_thumb' ) * 1.5;
		$maxh 			+= $lineheight;
		$count 			= '0';

		if ( $thumbs ) foreach ( $thumbs as $image ) {

			$thumb = $image;

			if ( $generic && wppa_is_separate( $thumb['album'] ) ) continue;

			// Make the HTML for current picture
			$widget_content .= "\n".'<div class="wppa-widget" style="width:'.$maxw.'px; height:'.$maxh.'px; margin:4px; display:inline; text-align:center; float:left;">';

			if ($image) {
				$no_album = !$album;
				if ($no_album) $tit = __( 'View the featured photos', 'wp-photo-album-plus' ); else $tit = esc_attr(__(stripslashes($image['description'])));
				$link       = wppa_get_imglnk_a('featen', $image['id'], '', $tit, '', $no_album, $album);
				$file       = wppa_get_thumb_path($image['id']);
				$imgstyle_a = wppa_get_imgstyle_a( $image['id'], $file, $maxw, 'center', 'ttthumb' );
				$imgstyle   = $imgstyle_a['style'];
				$width      = $imgstyle_a['width'];
				$height     = $imgstyle_a['height'];
				$cursor		= $imgstyle_a['cursor'];
				$imgurl 	= wppa_get_thumb_url( $image['id'], '', $width, $height );

				$imgevents = wppa_get_imgevents('thumb', $image['id'], true);

				if ($link) $title = esc_attr(stripslashes($link['title']));
				else $title = '';

//				$album = '0';
				$display = 'thumbs';

				$widget_content .= wppa_get_the_widget_thumb('featen', $image, $album, $display, $link, $title, $imgurl, $imgstyle_a, $imgevents);

			}
			else {	// No image
				$widget_content .= __( 'Photo not found', 'wp-photo-album-plus' );
			}

			$widget_content .= "\n".'</div>';

			$count++;
			if ( $count == wppa_opt( 'featen_count' ) ) break;

		}
		else $widget_content .= __( 'There are no featured photos (yet)', 'wp-photo-album-plus' );

		$widget_content .= '<div style="clear:both"></div>';
		$widget_content .= "\n".'<!-- WPPA+ FeaTen Widget end -->';

		echo "\n" . $before_widget;
		if ( !empty( $widget_title ) ) { echo $before_title . $widget_title . $after_title; }
		echo $widget_content . $after_widget;

		wppa( 'in_widget', false );
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['album'] = $new_instance['album'];

        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
		global $wppa_opt;
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => __('Featured Photos', 'wp-photo-album-plus'), 'album' => '0') );
 		$widget_title = apply_filters('widget_title', $instance['title']);

		$album = $instance['album'];
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp-photo-album-plus'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $widget_title; ?>" />
		</p>
		<p><label for="<?php echo $this->get_field_id('album'); ?>"><?php _e('Album:', 'wp-photo-album-plus'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('album'); ?>" name="<?php echo $this->get_field_name('album'); ?>" >

				<?php echo wppa_album_select_a(array('selected' => $album, 'addall' => true, 'addowner' => true, 'path' => wppa_switch('wppa_hier_albsel'))) ?>

			</select>
		</p>

		<p><?php _e('You can set the sizes in this widget in the <b>Photo Albums -> Settings</b> admin page.', 'wp-photo-album-plus'); ?></p>
<?php
    }

} // class FeaTenWidget

// register FeaTenWidget widget
add_action('widgets_init', 'wppa_register_FeaTenWidget' );

function wppa_register_FeaTenWidget() {
	register_widget("FeaTenWidget");
}
