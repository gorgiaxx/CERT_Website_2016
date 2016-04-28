<?php
/* wppa-lasten-widget.php
* Package: wp-photo-album-plus
*
* display the last uploaded photos
* Version 6.3.17
*/

class LasTenWidget extends WP_Widget {
    /** constructor */
    function __construct() {
		$widget_ops = array('classname' => 'wppa_lasten_widget', 'description' => __( 'WPPA+ Last Ten Uploaded Photos', 'wp-photo-album-plus') );
		parent::__construct('wppa_lasten_widget', __('Last Ten Photos', 'wp-photo-album-plus'), $widget_ops);
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

		wppa( 'in_widget', 'lasten' );
		wppa_bump_mocc();

        extract( $args );

		$instance 		= wp_parse_args( (array) $instance, array(
														'title' => '',
														'album' => '',
														'albumenum' => '',
														'timesince' => 'yes',
														'display' => 'thumbs'
														) );
		$widget_title 	= apply_filters('widget_title', $instance['title'] );
		$page 			= in_array( wppa_opt( 'lasten_widget_linktype' ), wppa( 'links_no_page' ) ) ?
							'' :
							wppa_get_the_landing_page( 'wppa_lasten_widget_linkpage', __( 'Last Ten Uploaded Photos', 'wp-photo-album-plus' ) );
		$max  			= wppa_opt( 'lasten_count' );
		$album 			= $instance['album'];
		$timesince 		= $instance['timesince'];
		$display 		= $instance['display'];
		$albumenum 		= $instance['albumenum'];

		$generic = ( $album == '-2' );
		if ( $generic ) {
			$album = '0';
			$max += '1000';
		}

		if ( $album == '-99' ) $album = implode("' OR `album` = '", explode(',', $albumenum));

		// Eiter look at timestamp or at date/time modified
		$order_by = wppa_switch( 'lasten_use_modified' ) ? 'modified' : 'timestamp';

		// If you want only 'New' photos in the selection, the period must be <> 0;
		if ( wppa_switch ( 'wppa_lasten_limit_new' ) && wppa_opt( 'max_photo_newtime' ) ) {
			$newtime = " `" . $order_by . "` >= ".( time() - wppa_opt( 'max_photo_newtime' ) );
			if ( $album ) {
				$q = "SELECT * FROM `".WPPA_PHOTOS."` WHERE (".$newtime.") AND ( `album` = '".$album."' ) AND ( `status` <> 'pending' AND `status` <> 'scheduled' ) ORDER BY `" . $order_by . "` DESC LIMIT " . $max;
			}
			else {
				$q = "SELECT * FROM `".WPPA_PHOTOS."` WHERE (".$newtime.") AND `status` <> 'pending' AND `status` <> 'scheduled' ORDER BY `" . $order_by . "` DESC LIMIT " . $max;
			}
		}
		else {
			if ( $album ) {
				$q = "SELECT * FROM `".WPPA_PHOTOS."` WHERE ( `album` = '".$album."' ) AND ( `status` <> 'pending' AND `status` <> 'scheduled' ) ORDER BY `" . $order_by . "` DESC LIMIT " . $max;
			}
			else {
				$q = "SELECT * FROM `".WPPA_PHOTOS."` WHERE `status` <> 'pending' AND `status` <> 'scheduled' ORDER BY `" . $order_by . "` DESC LIMIT " . $max;
			}
		}
		// echo $q;
		$thumbs 		= $wpdb->get_results( $q, ARRAY_A );

		$widget_content = "\n".'<!-- WPPA+ LasTen Widget start -->';
		$maxw 			= wppa_opt( 'lasten_size' );
		$maxh 			= $maxw;
		$lineheight 	= wppa_opt( 'fontsize_widget_thumb' ) * 1.5;
		$maxh 			+= $lineheight;

		if ( $timesince == 'yes' ) $maxh += $lineheight;

		$count = '0';

		if ( $thumbs ) foreach ( $thumbs as $image ) {

			$thumb = $image;

			if ( $generic && wppa_is_separate( $thumb['album'] ) ) continue;

			// Make the HTML for current picture
			if ( $display == 'thumbs' ) {
				$widget_content .= "\n".'<div class="wppa-widget" style="width:'.$maxw.'px; height:'.$maxh.'px; margin:4px; display:inline; text-align:center; float:left;">';
			}
			else {
				$widget_content .= "\n".'<div class="wppa-widget" >';
			}
			if ( $image ) {
				$no_album = !$album;
				if ($no_album) $tit = __( 'View the most recent uploaded photos', 'wp-photo-album-plus' ); else $tit = esc_attr(__(stripslashes($image['description'])));
				$link       = wppa_get_imglnk_a('lasten', $image['id'], '', $tit, '', $no_album, $albumenum);
				$file       = wppa_get_thumb_path($image['id']);
				$imgstyle_a = wppa_get_imgstyle_a( $image['id'], $file, $maxw, 'center', 'ltthumb');
				$imgurl 	= wppa_get_thumb_url( $image['id'], '', $imgstyle_a['width'], $imgstyle_a['height'] );
				$imgevents 	= wppa_get_imgevents('thumb', $image['id'], true);
				$title 		= $link ? esc_attr(stripslashes($link['title'])) : '';

				$widget_content .= wppa_get_the_widget_thumb('lasten', $image, $album, $display, $link, $title, $imgurl, $imgstyle_a, $imgevents);

				$widget_content .= "\n\t".'<div style="font-size:' . wppa_opt( 'fontsize_widget_thumb' ) . 'px; line-height:'.$lineheight.'px;">';
				if ( $timesince == 'yes' ) {
					$widget_content .= "\n\t".'<div>'.wppa_get_time_since( $image[$order_by] ).'</div>';
				}
				$widget_content .= '</div>';
			}
			else {	// No image
				$widget_content .= __( 'Photo not found', 'wp-photo-album-plus' );
			}
			$widget_content .= "\n".'</div>';
			$count++;
			if ( $count == wppa_opt( 'lasten_count' ) ) break;

		}
		else $widget_content .= __( 'There are no uploaded photos (yet)', 'wp-photo-album-plus' );

		$widget_content .= '<div style="clear:both"></div>';
		$widget_content .= "\n".'<!-- WPPA+ LasTen Widget end -->';

		echo "\n" . $before_widget;
		if ( !empty( $widget_title ) ) { echo $before_title . $widget_title . $after_title; }
		echo $widget_content . $after_widget;

		wppa( 'in_widget', false );
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] 			= strip_tags($new_instance['title']);
		$instance['album'] 			= strval( intval( $new_instance['album'] ) );
		$instance['albumenum'] 		= $new_instance['albumenum'];
		if ( $instance['album'] != '-99' ) $instance['albumenum'] = '';
		$instance['timesince'] 		= $new_instance['timesince'];
		$instance['display'] 		= $new_instance['display'];

        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
		global $wppa_opt;
		//Defaults
		$instance 		= wp_parse_args( (array) $instance, array(
															'title' => __('Last Ten Photos', 'wp-photo-album-plus'),
															'album' => '0',
															'albumenum' => '',
															'timesince' => 'yes',
															'display' => 'thumbs'
															) );
 		$widget_title 	= apply_filters('widget_title', $instance['title']);
		$album 			= $instance['album'];
		$album_enum 	= $instance['albumenum'];
		$timesince 		= $instance['timesince'];
		$display 		= $instance['display'];
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp-photo-album-plus'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $widget_title; ?>" />
		</p>
		<p><label for="<?php echo $this->get_field_id('album'); ?>"><?php _e('Album:', 'wp-photo-album-plus'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('album'); ?>" name="<?php echo $this->get_field_name('album'); ?>" >

				<?php echo wppa_album_select_a(array('selected' => $album, 'addall' => true, 'addmultiple' => true, 'addnumbers' => true, 'path' => wppa_switch('wppa_hier_albsel'))) //('', $album, true, '', '', true, '', '', true, true); ?>

			</select>
		</p>

		<p id="wppa-albums-enum" style="display:block;" ><label for="<?php echo $this->get_field_id('albumenum'); ?>"><?php _e('Albums:', 'wp-photo-album-plus'); ?></label>
		<small style="color:blue;" ><br /><?php _e('Select --- multiple see below --- in the Album selection box. Then enter album numbers seperated by commas', 'wp-photo-album-plus') ?></small>
			<input class="widefat" id="<?php echo $this->get_field_id('albumenum'); ?>" name="<?php echo $this->get_field_name('albumenum'); ?>" type="text" value="<?php echo $album_enum ?>" />
		</p>

		<p>
			<?php _e('Display:', 'wp-photo-album-plus'); ?>
			<select id="<?php echo $this->get_field_id('display'); ?>" name="<?php echo $this->get_field_name('display'); ?>">
				<option value="thumbs" <?php if ($display == 'thumbs') echo 'selected="selected"' ?>><?php _e('thumbnail images', 'wp-photo-album-plus'); ?></option>
				<option value="names" <?php if ($display == 'names') echo 'selected="selected"' ?>><?php _e('photo names', 'wp-photo-album-plus'); ?></option>
			</select>

		</p>

		<p>
			<?php _e('Show time since:', 'wp-photo-album-plus'); ?>
			<select id="<?php echo $this->get_field_id('timesince'); ?>" name="<?php echo $this->get_field_name('timesince'); ?>">
				<option value="no" <?php if ($timesince == 'no') echo 'selected="selected"' ?>><?php _e('no.', 'wp-photo-album-plus'); ?></option>
				<option value="yes" <?php if ($timesince == 'yes') echo 'selected="selected"' ?>><?php _e('yes.', 'wp-photo-album-plus'); ?></option>
			</select>
		</p>

		<p><?php _e('You can set the sizes in this widget in the <b>Photo Albums -> Settings</b> admin page.', 'wp-photo-album-plus'); ?></p>
<?php
    }

} // class LasTenWidget

// register LasTenWidget widget
add_action('widgets_init', 'wppa_register_LasTenWidget' );

function wppa_register_LasTenWidget() {
	register_widget("LasTenWidget");
}
