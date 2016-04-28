<?php
/* wppa-slideshow-widget.php
* Package: wp-photo-album-plus
*
* display a slideshow in the sidebar
* Version 6.3.17
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

/**
 * SlideshowWidget Class
 */
class SlideshowWidget extends WP_Widget {
    /** constructor */
    function __construct() {
		$widget_ops = array('classname' => 'slideshow_widget', 'description' => __( 'WPPA+ Sidebar Slideshow', 'wp-photo-album-plus') );	//
		parent::__construct('slideshow_widget', __('Sidebar Slideshow', 'wp-photo-album-plus'), $widget_ops);
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

        extract( $args );

		$instance = wp_parse_args( (array) $instance,
									array( 	'title' 	=> '',
											'album' 	=> '',
											'width' 	=> wppa_opt( 'widget_width' ),
											'height' 	=> round( wppa_opt( 'widget_width' ) * wppa_opt( 'maxheight' ) / wppa_opt( 'fullsize' ) ),
											'ponly' 	=> 'no',
											'linkurl' 	=> '',
											'linktitle' => '',
											'subtext' 	=> '',
											'supertext' => '',
											'valign' 	=> 'center',
											'timeout' 	=> '4',
											'film' 		=> 'no',
											'browse' 	=> 'no',
											'name' 		=> 'no',
											'numbar'	=> 'no',
											'desc' 		=> 'no'
											) );
		$title 		= apply_filters('widget_title', $instance['title']);
		$album 		= $instance['album'];
		$width 		= $instance['width'];
		$height		= $instance['height'];
		if ( $height == '0' ) $height = round( $width * wppa_opt( 'maxheight' ) / wppa_opt( 'fullsize' ) );
		$ponly 		= $instance['ponly'];
		$linkurl 	= $instance['linkurl'];
		$linktitle 	= $instance['linktitle'];
		$supertext 	= __($instance['supertext']);
		$subtext 	= __($instance['subtext']);
		$valign 	= $instance['valign'];
		$timeout	= $instance['timeout'] * 1000;
		$film 		= $instance['film'];
		$browse 	= $instance['browse'];
		$name 		= $instance['name'];
		$numbar		= $instance['numbar'];
		$desc 		= $instance['desc'];

		$page = in_array( wppa_opt( 'slideonly_widget_linktype' ), wppa( 'links_no_page' ) ) ? '' : wppa_get_the_landing_page( 'wppa_slideonly_widget_linkpage', __( 'Widget landing page', 'wp-photo-album-plus' ) );

		if (is_numeric($album)) {
			echo $before_widget;
				if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
				if ( $linkurl != '' && wppa_opt( 'slideonly_widget_linktype' ) == 'widget' ) {
					wppa( 'in_widget_linkurl', $linkurl );
					wppa( 'in_widget_linktitle', __($linktitle) );
				}
				if ($supertext != '') {
					echo '<div style="padding-top:2px; padding-bottom:4px; text-align:center">'.$supertext.'</div>';
				}
				echo '<div style="padding-top:2px; padding-bottom:4px;" >';
			wppa( 'auto_colwidth', false );
					wppa( 'in_widget', 'ss' );
					wppa( 'in_widget_frame_height', $height );
					wppa( 'in_widget_frame_width', $width );
					wppa( 'in_widget_timeout', $timeout );
					wppa( 'portrait_only', ($ponly == 'yes') );
					wppa( 'ss_widget_valign', $valign );
					wppa( 'film_on', ($film == 'yes') );
					wppa( 'browse_on', ($browse == 'yes') );
					wppa( 'name_on', ($name == 'yes') );
					wppa( 'numbar_on', ($numbar == 'yes') );
					wppa( 'desc_on', ($desc == 'yes') );
						echo wppa_albums($album, 'slideonly', $width, 'center');
					wppa( 'desc_on', false );
					wppa( 'numbar_on', false );
					wppa( 'name_on', false );
					wppa( 'browse_on', false );
					wppa( 'film_on', false );
					wppa( 'ss_widget_valign', '' );
					wppa( 'portrait_only', false );
					wppa( 'in_widget_timeout', '0' );
					wppa( 'in_widget_frame_height', '' );
					wppa( 'in_widget_frame_width', '' );
					wppa( 'in_widget', false );

					wppa( 'fullsize', '' );	// Reset to prevent inheritage of wrong size in case widget is rendered before main column

				echo '</div>';
				if ($linkurl != '') {
					wppa( 'in_widget_linkurl', '' );
					wppa( 'in_widget_linktitle', '' );
				}
				if ($subtext != '') {
					echo '<div style="padding-top:2px; padding-bottom:0px; text-align:center">'.$subtext.'</div>';
				}
			echo $after_widget;
		}
		else {
			echo "\n" . $before_widget;
			if ( !empty( $widget_title ) ) { echo $before_title . $widget_title . $after_title; }
			echo __( 'No album defined (yet)', 'wp-photo-album-plus' );
			echo $after_widget;
		}
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['album'] = $new_instance['album'];
		$instance['width'] = $new_instance['width'];
		$instance['height'] = $new_instance['height'];
		$instance['ponly'] = $new_instance['ponly'];
		$instance['linkurl'] = $new_instance['linkurl'];
		$instance['linktitle'] = $new_instance['linktitle'];
		$instance['supertext'] = $new_instance['supertext'];
		$instance['subtext'] = $new_instance['subtext'];
		if ($instance['ponly'] == 'yes') {
			$instance['valign'] = 'fit';
		}
		else {
			$instance['valign'] = $new_instance['valign'];
		}
		$instance['timeout'] = $new_instance['timeout'];
		$instance['film'] = $new_instance['film'];
		$instance['browse'] = $new_instance['browse'];
		$instance['name'] = $new_instance['name'];
		$instance['numbar'] = $new_instance['numbar'];
		$instance['desc'] = $new_instance['desc'];

        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {

		//Defaults
		$instance = wp_parse_args( (array) $instance,
									array( 	'title' 	=> __( 'Sidebar Slideshow' , 'wp-photo-album-plus'),
											'album' 	=> '',
											'width' 	=> wppa_opt( 'widget_width' ),
											'height' 	=> round( wppa_opt( 'widget_width' ) * wppa_opt( 'maxheight' ) / wppa_opt( 'fullsize' ) ),
											'ponly' 	=> 'no',
											'linkurl' 	=> '',
											'linktitle' => '',
											'subtext' 	=> '',
											'supertext' => '',
											'valign' 	=> 'center',
											'timeout' 	=> '4',
											'film' 		=> 'no',
											'browse' 	=> 'no',
											'name' 		=> 'no',
											'numbar'	=> 'no',
											'desc' 		=> 'no'
											) );

		$title = esc_attr( $instance['title'] );
		$album = $instance['album'];
		$width = $instance['width'];
		$height = $instance['height'];
		$ponly = $instance['ponly'];
		$linkurl = $instance['linkurl'];
		$linktitle = $instance['linktitle'];
		$supertext = $instance['supertext'];
		$subtext = $instance['subtext'];
		$valign = $instance['valign'];
		$timeout = $instance['timeout'];
		$film = $instance['film'];
		$browse = $instance['browse'];
		$name = $instance['name'];
		$numbar = $instance['numbar'];
		$desc = $instance['desc'];

	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp-photo-album-plus'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('album'); ?>"><?php _e('Album:', 'wp-photo-album-plus'); ?></label> <select class="widefat" id="<?php echo $this->get_field_id('album'); ?>" name="<?php echo $this->get_field_name('album'); ?>"><?php echo '<option value="-2">' . __('--- all ---', 'wp-photo-album-plus') . '</option>'.wppa_album_select_a(array('selected' => $album, 'path' => wppa_switch('wppa_hier_albsel'))) ?></select></p>
		<p><?php _e('Enter the width and optionally the height of the area wherein the slides will appear. If you specify a 0 for the height, it will be calculated. The value for the height will be ignored if you set the vertical alignment to \'fit\'.', 'wp-photo-album-plus') ?></p>
		<p><label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width:', 'wp-photo-album-plus'); ?></label> <input class="widefat" style="width:15%;" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width; ?>" />&nbsp;<?php _e('pixels.', 'wp-photo-album-plus') ?>
		<label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height:', 'wp-photo-album-plus'); ?></label> <input class="widefat" style="width:15%;" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $height; ?>" />&nbsp;<?php _e('pixels.', 'wp-photo-album-plus') ?></p>
		<p>
			<?php _e('Portrait only:', 'wp-photo-album-plus'); ?>
			<select id="<?php echo $this->get_field_id('ponly'); ?>" name="<?php echo $this->get_field_name('ponly'); ?>">
				<option value="no" <?php if ($ponly == 'no') echo 'selected="selected"' ?>><?php _e('no.', 'wp-photo-album-plus'); ?></option>
				<option value="yes" <?php if ($ponly == 'yes') echo 'selected="selected"' ?>><?php _e('yes.', 'wp-photo-album-plus'); ?></option>
			</select>&nbsp;<?php _e('Set to \'yes\' if there are only portrait images in the album and you want the photos to fill the full width of the widget.<br/>Set to \'no\' otherwise.', 'wp-photo-album-plus') ?>
			&nbsp;<?php _e('If set to \'yes\', Vertical alignment will be forced to \'fit\'.', 'wp-photo-album-plus') ?>
		</p>
		<p>
			<?php _e('Vertical alignment:', 'wp-photo-album-plus'); ?>
			<select id="<?php echo $this->get_field_id('valign'); ?>" name="<?php echo $this->get_field_name('valign'); ?>">
				<option value="top" <?php if ($valign == 'top') echo(' selected '); ?>><?php _e('top', 'wp-photo-album-plus'); ?></option>
				<option value="center" <?php if ($valign == 'center') echo(' selected '); ?>><?php _e('center', 'wp-photo-album-plus'); ?></option>
				<option value="bottom" <?php if ($valign == 'bottom') echo(' selected '); ?>><?php _e('bottom', 'wp-photo-album-plus'); ?></option>
				<option value="fit" <?php if ($valign == 'fit') echo(' selected '); ?>><?php _e('fit', 'wp-photo-album-plus'); ?></option>
			</select><br/><?php _e('Set the desired vertical alignment method.', 'wp-photo-album-plus'); ?>
		</p>
		<p><label for="<?php echo $this->get_field_id('timeout'); ?>"><?php _e('Slideshow timeout:', 'wp-photo-album-plus'); ?></label> <input class="widefat" style="width:15%;" id="<?php echo $this->get_field_id('timeout'); ?>" name="<?php echo $this->get_field_name('timeout'); ?>" type="text" value="<?php echo $timeout; ?>" />&nbsp;<?php _e('sec.', 'wp-photo-album-plus'); ?></p>
		<p><label for="<?php echo $this->get_field_id('linkurl'); ?>"><?php _e('Link to:', 'wp-photo-album-plus'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('linkurl'); ?>" name="<?php echo $this->get_field_name('linkurl'); ?>" type="text" value="<?php echo $linkurl; ?>" /></p>

		<p>
			<?php _e('Show name:', 'wp-photo-album-plus'); ?>
			<select id="<?php echo $this->get_field_id('name'); ?>" name="<?php echo $this->get_field_name('name'); ?>">
				<option value="no" <?php if ($name == 'no') echo 'selected="selected"' ?>><?php _e('no.', 'wp-photo-album-plus'); ?></option>
				<option value="yes" <?php if ($name == 'yes') echo 'selected="selected"' ?>><?php _e('yes.', 'wp-photo-album-plus'); ?></option>
			</select>
		</p>
		<p>
			<?php _e('Show description:', 'wp-photo-album-plus'); ?>
			<select id="<?php echo $this->get_field_id('desc'); ?>" name="<?php echo $this->get_field_name('desc'); ?>">
				<option value="no" <?php if ($desc == 'no') echo 'selected="selected"' ?>><?php _e('no.', 'wp-photo-album-plus'); ?></option>
				<option value="yes" <?php if ($desc == 'yes') echo 'selected="selected"' ?>><?php _e('yes.', 'wp-photo-album-plus'); ?></option>
			</select>
		</p>
		<p>
			<?php _e('Show filmstrip:', 'wp-photo-album-plus'); ?>
			<select id="<?php echo $this->get_field_id('film'); ?>" name="<?php echo $this->get_field_name('film'); ?>">
				<option value="no" <?php if ($film == 'no') echo 'selected="selected"' ?>><?php _e('no.', 'wp-photo-album-plus'); ?></option>
				<option value="yes" <?php if ($film == 'yes') echo 'selected="selected"' ?>><?php _e('yes.', 'wp-photo-album-plus'); ?></option>
			</select>
		</p>
		<p>
			<?php _e('Show browsebar:', 'wp-photo-album-plus'); ?>
			<select id="<?php echo $this->get_field_id('browse'); ?>" name="<?php echo $this->get_field_name('browse'); ?>">
				<option value="no" <?php if ($browse == 'no') echo 'selected="selected"' ?>><?php _e('no.', 'wp-photo-album-plus'); ?></option>
				<option value="yes" <?php if ($browse == 'yes') echo 'selected="selected"' ?>><?php _e('yes.', 'wp-photo-album-plus'); ?></option>
			</select>
		</p>
		<p>
			<?php _e('Show numbar:', 'wp-photo-album-plus'); ?>
			<select id="<?php echo $this->get_field_id('numbar'); ?>" name="<?php echo $this->get_field_name('numbar'); ?>">
				<option value="no" <?php if ($numbar == 'no') echo 'selected="selected"' ?>><?php _e('no.', 'wp-photo-album-plus'); ?></option>
				<option value="yes" <?php if ($numbar == 'yes') echo 'selected="selected"' ?>><?php _e('yes.', 'wp-photo-album-plus'); ?></option>
			</select>
		</p>

		<p><span style="color:blue"><small><?php _e('The following text fields support qTranslate', 'wp-photo-album-plus') ?></small></span></p>
		<p><label for="<?php echo $this->get_field_id('linktitle'); ?>"><?php _e('Tooltip text:', 'wp-photo-album-plus'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('linktitle'); ?>" name="<?php echo $this->get_field_name('linktitle'); ?>" type="text" value="<?php echo $linktitle; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('supertext'); ?>"><?php _e('Text above photos:', 'wp-photo-album-plus'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('supertext'); ?>" name="<?php echo $this->get_field_name('supertext'); ?>" type="text" value="<?php echo $supertext; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('subtext'); ?>"><?php _e('Text below photos:', 'wp-photo-album-plus'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('subtext'); ?>" name="<?php echo $this->get_field_name('subtext'); ?>" type="text" value="<?php echo $subtext; ?>" /></p>

<?php
    }

} // class SlideshowWidget

// register SlideshowWidget widget
add_action('widgets_init', 'wppa_register_SlideshowWidget' );

function wppa_register_SlideshowWidget() {
	register_widget("SlideshowWidget");
}
