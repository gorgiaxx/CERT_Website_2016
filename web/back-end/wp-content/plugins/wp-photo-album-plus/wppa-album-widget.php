<?php
/* wppa-album-widget.php
* Package: wp-photo-album-plus
*
* display thumbnail albums
* Version 6.4.07
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

class AlbumWidget extends WP_Widget {

    /** constructor */
    function __construct() {
		$widget_ops = array( 'classname' => 'wppa_album_widget', 'description' => __( 'WPPA+ Albums' , 'wp-photo-album-plus') );
		parent::__construct( 'wppa_album_widget', __( 'Thumbnail Albums' , 'wp-photo-album-plus'), $widget_ops );
    }

	/** @see WP_Widget::widget */
    function widget( $args, $instance ) {
		global $wpdb;

		require_once(dirname(__FILE__) . '/wppa-links.php');
		require_once(dirname(__FILE__) . '/wppa-styles.php');
		require_once(dirname(__FILE__) . '/wppa-functions.php');
		require_once(dirname(__FILE__) . '/wppa-thumbnails.php');
		require_once(dirname(__FILE__) . '/wppa-boxes-html.php');
		require_once(dirname(__FILE__) . '/wppa-slideshow.php');
		wppa_initialize_runtime();

		wppa( 'in_widget', 'alb' );
		wppa_bump_mocc();

        extract( $args );

		$instance = wp_parse_args( (array) $instance, array(
													'title' 	=> '',		// Widget title
													'parent' 	=> 'none',	// Parent album
													'name' 		=> 'no',		// Display album name?
													'skip' 		=> 'yes'		// Skip empty albums
							//						'count' 	=> wppa_opt( 'album_widget_count' ),	// to be added
							//						'size' 		=> wppa_opt( 'album_widget_size' )
													) );

		$widget_title = apply_filters('widget_title', $instance['title']);

		$page = in_array( wppa_opt( 'album_widget_linktype' ), wppa( 'links_no_page' ) ) ? '' : wppa_get_the_landing_page('wppa_album_widget_linkpage', __('Photo Albums', 'wp-photo-album-plus'));

		$max  	= wppa_opt( 'album_widget_count' );
		if ( ! $max ) $max = '10';
		$parent = $instance['parent'];
		$name 	= $instance['name'];
		$skip 	= $instance['skip'];

		if ( is_numeric($parent) ) {
			$albums = $wpdb->get_results($wpdb->prepare( 'SELECT * FROM `'.WPPA_ALBUMS.'` WHERE `a_parent` = %s '.wppa_get_album_order($parent), $parent ), ARRAY_A );
		}
		else {
			switch ($parent) {
				case 'all':
					$albums = $wpdb->get_results( 'SELECT * FROM `'.WPPA_ALBUMS.'` '.wppa_get_album_order(), ARRAY_A );
					break;
				case 'last':
					$albums = $wpdb->get_results( 'SELECT * FROM `'.WPPA_ALBUMS.'` ORDER BY `timestamp` DESC', ARRAY_A );
					break;
				default:
					wppa_dbg_msg('Error, unimplemented album selection: '.$parent.' in Album widget.', 'red', true);
				}
		}

		$widget_content = "\n".'<!-- WPPA+ album Widget start -->';
		$maxw = wppa_opt( 'album_widget_size' );
		$maxh = $maxw;
		if ( $name == 'yes' ) $maxh += 18;

		$count = 0;
		if ( $albums ) foreach ( $albums as $album ) {

			if ( $count < $max ) {

				$imageid 		= wppa_get_coverphoto_id( $album['id'] );
				if ( $imageid ) {
					$image 		= wppa_cache_thumb( $imageid ); //$wpdb->get_row( $wpdb->prepare( 'SELECT * FROM `'.WPPA_PHOTOS.'` WHERE `id` = %s', $imageid ), ARRAY_A );
				}
				else {
					$image = false;
				}
				$imgcount 		= $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM '.WPPA_PHOTOS.' WHERE `album` = %s', $album['id']  ) );
				$subalbumcount 	= wppa_has_children( $album['id'] ); // $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_ALBUMS."` WHERE `a_parent` = %s", $album['id'] ) );
				$thumb 			= $image;

				// Make the HTML for current picture
				if ( $image && ( $imgcount > wppa_opt( 'min_thumbs' ) || $subalbumcount ) ) {
					$link       = wppa_get_imglnk_a('albwidget', $image['id']);
					$file       = wppa_get_thumb_path($image['id']);
					$imgevents  = wppa_get_imgevents('thumb', $image['id'], true);
					$imgstyle_a = wppa_get_imgstyle_a( $image['id'], $file, $maxw, 'center', 'albthumb' );
					$imgstyle   = $imgstyle_a['style'];
					$width      = $imgstyle_a['width'];
					$height     = $imgstyle_a['height'];
					$cursor		= $imgstyle_a['cursor'];
					if ( wppa_switch('wppa_show_albwidget_tooltip') ) $title = esc_attr(strip_tags(wppa_get_album_desc($album['id'])));
					else $title = '';
					$imgurl 	= wppa_get_thumb_url( $image['id'], '', $width, $height );
				}
				else {
					$link       = '';
					$file 		= '';
					$imgevents  = '';
					$imgstyle   = 'width:'.$maxw.';height:'.$maxh.';';
					$width      = $maxw;
					$height     = $maxw; // !!
					$cursor		= 'default';
					$title 		= sprintf(__('Upload at least %d photos to this album!', 'wp-photo-album-plus'), wppa_opt( 'min_thumbs' ) - $imgcount + 1);
					if ( $imageid ) {	// The 'empty album has a cover image
						$file       = wppa_get_thumb_path($image['id']);
						$imgstyle_a = wppa_get_imgstyle_a( $image['id'], $file, $maxw, 'center', 'albthumb' );
						$imgstyle   = $imgstyle_a['style'];
						$width      = $imgstyle_a['width'];
						$height     = $imgstyle_a['height'];
						$imgurl 	= wppa_get_thumb_url( $image['id'], '', $width, $height );
					}
					else {
						$imgurl		= wppa_get_imgdir().'album32.png';
					}
				}

				if ( $imageid ) {
					$imgurl = wppa_fix_poster_ext( $imgurl, $image['id'] );
				}

				if ( $imgcount > wppa_opt( 'min_thumbs' ) || $skip == 'no' ) {

					$widget_content .= "\n".'<div class="wppa-widget" style="width:'.$maxw.'px; height:'.$maxh.'px; margin:4px; display:inline; text-align:center; float:left;">';

					if ($link) {
						if ( $link['is_url'] ) {	// Is a href
							$widget_content .= "\n\t".'<a href="'.$link['url'].'" title="'.$title.'" target="'.$link['target'].'" >';
							if ( $imageid && wppa_is_video( $image['id'] ) ) {
								$widget_content .= wppa_get_video_html( array( 	'id' 			=> $image['id'],
																				'width' 		=> $width,
																				'height' 		=> $height,
																				'controls' 		=> false,
																				'margin_top' 	=> $imgstyle_a['margin-top'],
																				'margin_bottom' => $imgstyle_a['margin-bottom'],
																				'cursor' 		=> 'pointer',
																				'events' 		=> $imgevents,
																				'tagid' 		=> 'i-'.$image['id'].'-'.wppa( 'mocc' ),
																				'title' 		=> $title
																			 )
																	 );
							}
							else {
								$widget_content .= "\n\t\t".'<img id="i-'.$image['id'].'-'.wppa( 'mocc' ).'" title="'.$title.'" src="'.$imgurl.'" width="'.$width.'" height="'.$height.'" style="'.$imgstyle.' cursor:pointer;" '.$imgevents.' '.wppa_get_imgalt($image['id']).' >';
							}
							$widget_content .= "\n\t".'</a>';
						}
						elseif ( $link['is_lightbox'] ) {
							$thumbs = $wpdb->get_results($wpdb->prepare("SELECT * FROM `".WPPA_PHOTOS."` WHERE `album` = %s ".wppa_get_photo_order($album['id']), $album['id']), 'ARRAY_A');
							if ( $thumbs ) foreach ( $thumbs as $thumb ) {
								$title = wppa_get_lbtitle('alw', $thumb['id']);
								if ( wppa_is_video( $thumb['id']  ) ) {
									$siz['0'] = wppa_get_videox( $thumb['id'] );
									$siz['1'] = wppa_get_videoy( $thumb['id'] );
								}
								else {
									$siz['0'] = wppa_get_photox( $thumb['id'] );
									$siz['1'] = wppa_get_photoy( $thumb['id'] );
								}
								$link 		= wppa_fix_poster_ext( wppa_get_photo_url( $thumb['id'], '', $siz['0'], $siz['1'] ), $thumb['id'] );
								$is_video 	= wppa_is_video( $thumb['id'] );
								$has_audio 	= wppa_has_audio( $thumb['id'] );

								$widget_content .= "\n\t" .
										'<a href="'.$link.'"' .
										( $is_video ? ' data-videohtml="' . esc_attr( wppa_get_video_body( $thumb['id'] ) ) . '"' .
										' data-videonatwidth="'.wppa_get_videox( $thumb['id'] ).'"' .
										' data-videonatheight="'.wppa_get_videoy( $thumb['id'] ).'"' : '' ) .
										( $has_audio ? ' data-audiohtml="' . esc_attr( wppa_get_audio_body( $thumb['id'] ) ) . '"' : '' ) .
										' ' . wppa( 'rel' ) . '="'.wppa_opt( 'lightbox_name' ).'[alw-'.wppa( 'mocc' ).'-'.$album['id'].']"' .
										' ' . wppa( 'lbtitle' ) . '="'.$title.'" >';
								if ( $thumb['id'] == $image['id'] ) {		// the cover image
									if ( wppa_is_video( $image['id'] ) ) {
										$widget_content .= wppa_get_video_html( array( 	'id' 			=> $image['id'],
																						'width' 		=> $width,
																						'height' 		=> $height,
																						'controls' 		=> false,
																						'margin_top' 	=> $imgstyle_a['margin-top'],
																						'margin_bottom' => $imgstyle_a['margin-bottom'],
																						'cursor' 		=> $cursor,
																						'events' 		=> $imgevents,
																						'tagid' 		=> 'i-'.$image['id'].'-'.wppa( 'mocc' ),
																						'title' 		=> wppa_zoom_in( $image['id'] ),
																					 )
																			 );
									}
									else {
										$widget_content .= "\n\t\t" .
																'<img' .
																	' id="i-'.$image['id'].'-'.wppa( 'mocc' ).'"' .
																	' title="'.wppa_zoom_in( $image['id'] ).'"' .
																	' src="'.$imgurl.'"' .
																	' width="'.$width.'"' .
																	' height="'.$height.'"' .
																	' style="'.$imgstyle.$cursor.'" ' .
																	$imgevents . ' ' .
																	wppa_get_imgalt( $image['id'] ) .
																	' >';
									}
								}
								$widget_content .= "\n\t".'</a>';
							}
						}
						else { // Is an onclick unit
							if ( $imageid && wppa_is_video( $image['id'] ) ) {
								$widget_content .= wppa_get_video_html( array( 	'id' 			=> $image['id'],
																				'width' 		=> $width,
																				'height' 		=> $height,
																				'controls' 		=> false,
																				'margin_top' 	=> $imgstyle_a['margin-top'],
																				'margin_bottom' => $imgstyle_a['margin-bottom'],
																				'cursor' 		=> 'pointer',
																				'events' 		=> $imgevents.' onclick="'.$link['url'].'"',
																				'tagid' 		=> 'i-'.$image['id'].'-'.wppa( 'mocc' ),
																				'title' 		=> $title,
																			 )
																	 );
							}
							else {
								$widget_content .= "\n\t" .
									'<img' .
										' id="i-'.$image['id'].'-'.wppa( 'mocc' ).'"' .
										' title="'.$title.'"' .
										' src="'.$imgurl.'"' .
										' width="'.$width.'"' .
										' height="'.$height.'"' .
										' style="'.$imgstyle.' cursor:pointer;" ' .
										$imgevents .
										' onclick="' . $link['url'] . '" ' .
										wppa_get_imgalt($image['id']) .
										' >';
							}
						}
					}
					else {
						if ( $imageid && wppa_is_video( $image['id'] ) ) {
							$widget_content .= wppa_get_video_html( array( 	'id' 			=> $image['id'],
																			'width' 		=> $width,
																			'height' 		=> $height,
																			'controls' 		=> false,
																			'margin_top' 	=> $imgstyle_a['margin-top'],
																			'margin_bottom' => $imgstyle_a['margin-bottom'],
																			'cursor' 		=> 'pointer',
																			'events' 		=> $imgevents,
																			'tagid' 		=> 'i-'.$image['id'].'-'.wppa( 'mocc' ),
																			'title' 		=> $title,
																		 )
																 );
						}
						else {
							$widget_content .= "\n\t" .
													'<img' .
														' id="i-'.$image['id'].'-'.wppa( 'mocc' ).'"' .
														' title="'.$title.'"' .
														' src="'.$imgurl.'"' .
														' width="'.$width.'"' .
														' height="'.$height.'"' .
														' style="'.$imgstyle.'" ' .
														$imgevents . ' ' .
														( $imageid ? wppa_get_imgalt( $image['id'] ) : '' ) .
														' >';
						}
					}

					if ($name == 'yes') $widget_content .= "\n\t".'<span style="font-size:'.wppa_opt( 'fontsize_widget_thumb' ).'px; min-height:100%;">'.__(stripslashes($album['name']), 'wp-photo-album-plus').'</span>';

					$widget_content .= "\n".'</div>';

					$count++;
				}
			}
		}
		else $widget_content .= 'There are no albums (yet).';

		$widget_content .= '<div style="clear:both"></div>';

		$widget_content .= "\n".'<!-- WPPA+ thumbnail Widget end -->';

		echo "\n" . $before_widget;
		if ( !empty( $widget_title ) ) { echo $before_title . $widget_title . $after_title; }
		echo $widget_content . $after_widget;

		wppa( 'in_widget', false );
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['parent'] = $new_instance['parent'];
		$instance['name'] = $new_instance['name'];
		$instance['skip'] = $new_instance['skip'];

        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
		global $wpdb;

		//Defaults
		$instance = wp_parse_args( (array) $instance, array(
															'title' => __('Thumbnail Albums', 'wp-photo-album-plus'),
															'parent' => '0',
															'name' => 'no',
															'skip' => 'yes' ) );
 		$parent = $instance['parent'];
		$name = $instance['name'];
		$skip = $instance['skip'];
		$widget_title = $instance['title'];
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp-photo-album-plus'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $widget_title; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('parent'); ?>"><?php _e('Album selection or Parent album:', 'wp-photo-album-plus'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('parent'); ?>" name="<?php echo $this->get_field_name('parent'); ?>" >

				<option value="all" <?php if ($parent == 'all') echo 'selected="selected"' ?>><?php _e('--- all albums ---', 'wp-photo-album-plus') ?></option>
				<option value="0"  <?php if ($parent == '0')  echo 'selected="selected"' ?>><?php _e('--- all generic albums ---', 'wp-photo-album-plus') ?></option>
				<option value="-1" <?php if ($parent == '-1') echo 'selected="selected"' ?>><?php _e('--- all separate albums ---', 'wp-photo-album-plus') ?></option>
				<option value="last" <?php if ($parent == 'last') echo 'selected="selected"' ?>><?php _e('--- most recently added albums ---', 'wp-photo-album-plus') ?></option>
				<?php $albs = $wpdb->get_results( "SELECT * FROM `".WPPA_ALBUMS."` ORDER BY `name`", ARRAY_A);
				if ( $albs ) foreach( $albs as $alb ) {
					echo '<option value="'.$alb['id'].'" ';
					if ( $parent == $alb['id'] ) echo 'selected="selected" ';
					if ( ! wppa_has_children($alb['id']) ) echo 'disabled="disabled" ';
					echo '>'.__(stripslashes($alb['name']), 'wp-photo-album-plus').'</option>';
				} ?>

			</select>
		</p>
		<p>
			<?php _e('Show album names:', 'wp-photo-album-plus'); ?>
			<select id="<?php echo $this->get_field_id('name'); ?>" name="<?php echo $this->get_field_name('name'); ?>">
				<option value="no" <?php if ($name == 'no') echo 'selected="selected"' ?>><?php _e('no.', 'wp-photo-album-plus'); ?></option>
				<option value="yes" <?php if ($name == 'yes') echo 'selected="selected"' ?>><?php _e('yes.', 'wp-photo-album-plus'); ?></option>
			</select>
		</p>
		<p>
			<?php _e('Skip "empty" albums:', 'wp-photo-album-plus'); ?>
			<select id="<?php echo $this->get_field_id('skip'); ?>" name="<?php echo $this->get_field_name('skip'); ?>">
				<option value="no" <?php if ($skip == 'no') echo 'selected="selected"' ?>><?php _e('no.', 'wp-photo-album-plus'); ?></option>
				<option value="yes" <?php if ($skip == 'yes') echo 'selected="selected"' ?>><?php _e('yes.', 'wp-photo-album-plus'); ?></option>
			</select>
		</p>

		<p><?php _e('You can set the sizes in this widget in the <b>Photo Albums -> Settings</b> admin page.', 'wp-photo-album-plus'); ?></p>
<?php
    }

} // class AlbumWidget

// register AlbumWidget widget
add_action('widgets_init', 'wppa_register_AlbumWidget' );

function wppa_register_AlbumWidget() {
	register_widget("AlbumWidget");
}
