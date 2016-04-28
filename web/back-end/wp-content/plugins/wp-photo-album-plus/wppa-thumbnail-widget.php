<?php
/* wppa-thumbnail-widget.php
* Package: wp-photo-album-plus
*
* display thumbnail photos
* Version 6.3.17
*/

class ThumbnailWidget extends WP_Widget {
    /** constructor */
    function __construct() {
		$widget_ops = array('classname' => 'wppa_thumbnail_widget', 'description' => __( 'WPPA+ Thumbnails', 'wp-photo-album-plus') );
		parent::__construct('wppa_thumbnail_widget', __('Thumbnail Photos', 'wp-photo-album-plus'), $widget_ops);
    }

	/** @see WP_Widget::widget */
    function widget($args, $instance) {

		global $wpdb;

		require_once(dirname(__FILE__) . '/wppa-links.php');
		require_once(dirname(__FILE__) . '/wppa-styles.php');
		require_once(dirname(__FILE__) . '/wppa-functions.php');
		require_once(dirname(__FILE__) . '/wppa-thumbnails.php');
		require_once(dirname(__FILE__) . '/wppa-boxes-html.php');
		require_once(dirname(__FILE__) . '/wppa-slideshow.php');
		wppa_initialize_runtime();

		wppa( 'in_widget', 'tn' );
		wppa_bump_mocc();

        extract( $args );

		$instance 		= wp_parse_args( (array) $instance, array(
														'title' 	=> '',
														'album' 	=> 'no',
														'link' 		=> '',
														'linktitle' => '',
														'name' 		=> 'no',
														'display' 	=> 'thumbs',
														'sortby' 	=> wppa_get_photo_order('0'),
														'limit' 	=> wppa_opt( 'thumbnail_widget_count' )
														) );
//		$widget_title 	= apply_filters('widget_title', $instance['title']);
		$widget_title 	= apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		$widget_link	= $instance['link'];
		$page 			= in_array( wppa_opt( 'thumbnail_widget_linktype' ), wppa( 'links_no_page' ) ) ? '' : wppa_get_the_landing_page('wppa_thumbnail_widget_linkpage', __('Thumbnail photos', 'wp-photo-album-plus'));
		$max  			= $instance['limit'];
		$sortby 		= $instance['sortby'];
		$album 			= $instance['album'];
		$name 			= $instance['name'];
		$display 		= $instance['display'];
		$linktitle 		= $instance['linktitle'];

		$generic = ( $album == '-2' );
		if ( $generic ) {
			$album = '0';
			$max += '1000';
		}
		$separate = ( $album == '-1' );
		if ( $separate ) {
			$album = '0';
			$max += '1000';
		}

		if ( $album ) {
			$thumbs = $wpdb->get_results($wpdb->prepare( "SELECT * FROM `".WPPA_PHOTOS."` WHERE `status` <> 'pending' AND `status` <> 'scheduled' AND `album` = %s ".$sortby." LIMIT %d", $album, $max ), 'ARRAY_A' );
		}
		else {
			$thumbs = $wpdb->get_results($wpdb->prepare( "SELECT * FROM `".WPPA_PHOTOS."` WHERE `status` <> 'pending' AND `status` <> 'scheduled'".$sortby." LIMIT %d", $max ), 'ARRAY_A' );
		}

		global $widget_content;
		$widget_content = "\n".'<!-- WPPA+ thumbnail Widget start -->';
		$maxw = wppa_opt( 'thumbnail_widget_size' );
		$maxh = $maxw;
		$lineheight = wppa_opt( 'fontsize_widget_thumb' ) * 1.5;
		$maxh += $lineheight;
		if ( $name == 'yes' ) $maxh += $lineheight;

		$count = '0';
		if ( $thumbs ) foreach ( $thumbs as $image ) {

			$thumb = $image;

			if ( $generic && wppa_is_separate( $thumb['album'] ) ) continue;
			if ( $separate && ! wppa_is_separate( $thumb['album'] ) ) continue;

			// Make the HTML for current picture
			if ( $display == 'thumbs' ) {
				$widget_content .= "\n".'<div class="wppa-widget" style="width:'.$maxw.'px; height:'.$maxh.'px; margin:4px; display:inline; text-align:center; float:left;">';
			}
			else {
				$widget_content .= "\n".'<div class="wppa-widget" >';
			}
			if ($image) {
				$link       = wppa_get_imglnk_a('tnwidget', $image['id']);
				$file       = wppa_get_thumb_path($image['id']);
				$imgstyle_a = wppa_get_imgstyle_a( $image['id'], $file, $maxw, 'center', 'twthumb');
				$imgurl 	= wppa_get_thumb_url( $image['id'], '', $imgstyle_a['width'], $imgstyle_a['height'] );
				$imgevents 	= wppa_get_imgevents('thumb', $image['id'], true);
				$title 		= $link ? esc_attr(stripslashes($link['title'])) : '';

				wppa_do_the_widget_thumb('thumbnail', $image, $album, $display, $link, $title, $imgurl, $imgstyle_a, $imgevents);

				$widget_content .= "\n\t".'<div style="font-size:'.wppa_opt( 'fontsize_widget_thumb' ).'px; line-height:'.$lineheight.'px;">';
				if ( $name == 'yes' && $display == 'thumbs' ) {
					$widget_content .= "\n\t".'<div>'.__(stripslashes($image['name']), 'wp-photo-album-plus').'</div>';
				}
				$widget_content .= "\n\t".'</div>';
			}
			else {	// No image
				$widget_content .= __( 'Photo not found', 'wp-photo-album-plus' );
			}
			$widget_content .= "\n".'</div>';
			$count++;
			if ( $count == $instance['limit'] ) break;

		}
		else $widget_content .= __( 'There are no photos (yet)', 'wp-photo-album-plus' );

		$widget_content .= '<div style="clear:both"></div>';
		$widget_content .= "\n".'<!-- WPPA+ thumbnail Widget end -->';

		echo "\n" . $before_widget;
		if ( !empty( $widget_title ) ) {

			echo $before_title;

			if (!empty($widget_link)) {
				echo "\n".'<a href="'.$widget_link.'" title="'.$linktitle.'" >'.$widget_title.'</a>';
			}
			else {
				echo $widget_title;
			}

			echo $after_title;
		}

		echo $widget_content . $after_widget;

		wppa( 'in_widget', false );
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] 		= strip_tags($new_instance['title']);
		$instance['link'] 		= strip_tags($new_instance['link']);
		$instance['album'] 		= $new_instance['album'];
		$instance['name'] 		= $new_instance['name'];
		$instance['display'] 	= $new_instance['display'];
		$instance['linktitle']	= $new_instance['linktitle'];
		$instance['sortby'] 	= $new_instance['sortby'];
		$instance['limit']		= strval(intval($new_instance['limit']));

        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {

		//Defaults
		$instance = wp_parse_args( (array) $instance, array(
															'title'		=> __('Thumbnail Photos', 'wp-photo-album-plus'),
															'link'	 	=> '',
															'linktitle' => '',
															'album' 	=> '0',
															'name' 		=> 'no',
															'display' 	=> 'thumbs',
															'sortby' 	=> wppa_get_photo_order('0'),
															'limit' 	=> wppa_opt( 'thumbnail_widget_count' )
															) );
 		$album 			= $instance['album'];
		$name 			= $instance['name'];
		$widget_title 	= $instance['title'];
		$widget_link 	= $instance['link'];
		$link_title 	= $instance['linktitle'];
		$display 		= $instance['display'];
		$sortby 		= $instance['sortby'];
		$limit			= $instance['limit'];
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp-photo-album-plus'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $widget_title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('link'); ?>"><?php _e('Link from the title:', 'wp-photo-album-plus'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="text" value="<?php echo $widget_link; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('link'); ?>"><?php _e('Link Title ( tooltip ):', 'wp-photo-album-plus'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('linktitle'); ?>" name="<?php echo $this->get_field_name('linktitle'); ?>" type="text" value="<?php echo $widget_link; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('album'); ?>"><?php _e('Album:', 'wp-photo-album-plus'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('album'); ?>" name="<?php echo $this->get_field_name('album'); ?>" >

				<?php echo wppa_album_select_a(array('selected' => $album, 'addseparate' => true, 'addall' => true, 'path' => wppa_switch('wppa_hier_albsel'))) //('', $album, true, '', '', true); ?>

			</select>
		</p>

		<p>
			<?php _e('Sort by:', 'wp-photo-album-plus'); ?>
			<select class="widefat" id="<?php echo $this->get_field_id('sortby'); ?>" name="<?php echo $this->get_field_name('sortby'); ?>">
				<option value="" <?php if ( $sortby == '' ) echo 'selected="selected"' ?>><?php _e('--- none ---', 'wp-photo-album-plus') ?></option>
				<option value="ORDER BY `p_order`" <?php if ( $sortby == 'ORDER BY `p_order`' ) echo 'selected="selected"' ?>><?php _e('Order #', 'wp-photo-album-plus') ?></option>
				<option value="ORDER BY `name`" <?php if ( $sortby == 'ORDER BY `name`' ) echo 'selected="selected"' ?>><?php _e('Name', 'wp-photo-album-plus') ?></option>
				<option value="ORDER BY RAND()" <?php if ( $sortby == 'ORDER BY RAND()' ) echo 'selected="selected"' ?>><?php _e('Random', 'wp-photo-album-plus') ?></option>
				<option value="ORDER BY `mean_rating` DESC" <?php if ( $sortby == 'ORDER BY `mean_rating` DESC' ) echo 'selected="selected"' ?>><?php _e('Rating mean value desc', 'wp-photo-album-plus') ?></option>
				<option value="ORDER BY `rating_count` DESC" <?php if ( $sortby == 'ORDER BY `rating_count` DESC' ) echo 'selected="selected"' ?>><?php _e('Number of votes desc', 'wp-photo-album-plus') ?></option>
				<option value="ORDER BY `timestamp` DESC" <?php if ( $sortby == 'ORDER BY `timestamp` DESC' ) echo 'selected="selected"' ?>><?php _e('Timestamp desc', 'wp-photo-album-plus') ?></option>
			</select>
		</p>

		<p>
			<?php _e('Max number:', 'wp-photo-album-plus') ?>
			<input id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" value="<?php echo $limit ?>">
		</p>

		<p>
			<?php _e('Display:', 'wp-photo-album-plus'); ?>
			<select id="<?php echo $this->get_field_id('display'); ?>" name="<?php echo $this->get_field_name('display'); ?>">
				<option value="thumbs" <?php if ($display == 'thumbs') echo 'selected="selected"' ?>><?php _e('thumbnail images', 'wp-photo-album-plus'); ?></option>
				<option value="names" <?php if ($display == 'names') echo 'selected="selected"' ?>><?php _e('photo names', 'wp-photo-album-plus'); ?></option>
			</select>

		</p>

		<p>
			<?php _e('Show photo names <small>under thumbnails only</small>:', 'wp-photo-album-plus'); ?>
			<select id="<?php echo $this->get_field_id('name'); ?>" name="<?php echo $this->get_field_name('name'); ?>">
				<option value="no" <?php if ($name == 'no') echo 'selected="selected"' ?>><?php _e('no.', 'wp-photo-album-plus'); ?></option>
				<option value="yes" <?php if ($name == 'yes') echo 'selected="selected"' ?>><?php _e('yes.', 'wp-photo-album-plus'); ?></option>
			</select>
		</p>

		<p><?php _e('You can set the sizes in this widget in the <b>Photo Albums -> Settings</b> admin page.', 'wp-photo-album-plus'); ?></p>
<?php
    }

} // class thumbnailWidget

// register thumbnailWidget widget
add_action('widgets_init', 'wppa_register_ThumbnailWidget' );

function wppa_register_ThumbnailWidget() {
	register_widget("ThumbnailWidget");
}
