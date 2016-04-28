<?php
/* wppa-tinymce-scripts.php
* Pachkage: wp-photo-album-plus
*
*
* Version 6.3.11
*
*/

if ( ! defined( 'WPPA_ABSPATH' ) )
    die( "Can't load this file directly" );

class wppaGallery
{
    function __construct() {
    add_action( 'admin_init', array( $this, 'action_admin_init' ) );
	}

	function action_admin_init() {
		// only hook up these filters if we're in the admin panel, and the current user has permission
		// to edit posts or pages
		if ( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) {
			add_filter( 'mce_buttons', array( $this, 'filter_mce_button' ) );
			add_filter( 'mce_external_plugins', array( $this, 'filter_mce_plugin' ) );
		}
	}

	function filter_mce_button( $buttons ) {
		// add a separation before our button.
		array_push( $buttons, '|', 'mygallery_button' );
		return $buttons;
	}

	function filter_mce_plugin( $plugins ) {
		// this plugin file will work the magic of our button
//		if ( wppa_switch( 'use_scripts_in_tinymce' ) ) {
			$file = 'js/wppa-tinymce-scripts.js';
//		}
//		else {
//			$file = 'js/wppa-tinymce-shortcodes.js';
//		}
		$plugins['wppagallery'] = plugin_dir_url( __FILE__ ) . $file;
		return $plugins;
	}

}

$wppagallery = new wppaGallery();

add_action('admin_head', 'wppa_inject_js');

function wppa_inject_js() {
global $wppa_api_version;

	// Things that wppa-tinymce.js AND OTHER MODULES!!! need to know
	echo('<script type="text/javascript">'."\n");
	echo('/* <![CDATA[ */'."\n");
		echo("\t".'wppaImageDirectory = "'.wppa_get_imgdir().'";'."\n");
		echo("\t".'wppaAjaxUrl = "'.admin_url('admin-ajax.php').'";'."\n");
		echo("\t".'wppaPhotoDirectory = "'.WPPA_UPLOAD_URL.'/";'."\n");
		echo("\t".'wppaThumbDirectory = "'.WPPA_UPLOAD_URL.'/thumbs/";'."\n");
		echo("\t".'wppaTempDirectory = "'.WPPA_UPLOAD_URL.'/temp/";'."\n");
		echo("\t".'wppaFontDirectory = "'.WPPA_UPLOAD_URL.'/fonts/";'."\n");
		echo("\t".'wppaNoPreview = "'.__('No Preview available', 'wp-photo-album-plus').'";'."\n");
		echo("\t".'wppaVersion = "'.$wppa_api_version.'";'."\n");
		echo("\t".'wppaSiteUrl = "'.site_url().'";'."\n");
		echo("\t".'wppaWppaUrl = "'.WPPA_URL.'";'."\n");
		echo("\t".'wppaIncludeUrl = "'.trim(includes_url(), '/').'";'."\n");
	echo("/* ]]> */\n");
	echo("</script>\n");
}

function wppa_make_tinymce_dialog() {
global $wpdb;

	$result =
	'<div id="wppagallery-form">'.
		'<div style="height:158px; background-color:#eee; overflow:auto; margin-top:10px;" >'.
			'<div id="wppagallery-album-preview" style="text-align:center;font-size:48px; line-height:21px; color:#fff;" class="wppagallery-album" ><br /><br /><br />'.
			__('Album Preview', 'wp-photo-album-plus').'<br /><span style="font-size:12px; color:#777" ><br/>'.__('A maximum of 100 photos can be previewd', 'wp-photo-album-plus').'</span></div>'.
			'<div id="wppagallery-photo-preview" style="text-align:center;font-size:48px; line-height:21px; color:#fff; display:none;" class="wppagallery-photo" ><br /><br /><br />'.
			__('Photo Preview', 'wp-photo-album-plus').'</div>'.
		'</div>'.
		'<table id="wppagallery-table" class="form-table">'.

			'<tr>'.
				'<th><label for="wppagallery-type">'.__('Type of Gallery display:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-type" name="type" onchange="wppaGalleryTypeChange(this.value)">'.
						'<option value="cover">'.__('The cover of an album', 'wp-photo-album-plus').'</option>'.
						'<option value="album">'.__('The sub-albums and/or thumbnails in an album', 'wp-photo-album-plus').'</option>'.
						'<option value="slide">'.__('A slideshow of the photos in an album', 'wp-photo-album-plus').'</option>'.
						'<option value="slideonly">'.__('A slideshow without supporting boxes', 'wp-photo-album-plus').'</option>'.
						'<option value="slideonlyf">'.__('A slideshow with a filmstrip only', 'wp-photo-album-plus').'</option>'.
						'<option value="photo">'.__('A single photo', 'wp-photo-album-plus').'</option>'.
						'<option value="mphoto">'.__('A single photo with caption', 'wp-photo-album-plus').'</option>'.
						'<option value="slphoto">'.__('A single photo in the style of a slideshow', 'wp-photo-album-plus').'</option>'.
						'<option value="generic">'.__('A generic albums display', 'wp-photo-album-plus').'</option>'.
					'</select>'.
					'<br />'.
					'<small>'.__('Specify the type of gallery', 'wp-photo-album-plus').'</small>'.
				'</td>'.
			'</tr>'.

			'<tr class="wppagallery-help" style="display:none;" >'.
				'<th><label for="wppagallery-album" class="wppagallery-help" >'.__('Explanation:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					__('Use this gallerytype to display all the top-level album covers.', 'wp-photo-album-plus').
				'</td>'.
			'</tr>'.

			'<tr class="wppagallery-album" >'.
				'<th><label for="wppagallery-album" class="wppagallery-album" >'.__('The Album to be used:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-album" name="album" style=width:270px;" class="wppagallery-album" onchange="wppaGalleryAlbumChange(this.value); wppaTinyMceAlbumPreview(this.value)">';
						$albums = $wpdb->get_results( "SELECT `id`, `name` FROM `".WPPA_ALBUMS."` ORDER BY `timestamp` DESC", ARRAY_A );
						if ($albums) {
							if ( wppa_switch('wppa_hier_albsel') ) $albums = wppa_add_paths($albums);
							else foreach ( array_keys($albums) as $index ) $albums[$index]['name'] = __(stripslashes($albums[$index]['name']), 'wp-photo-album-plus');
							// Sort
							$albums = wppa_array_sort($albums, 'name');
							$result .=
							// Please select
							'<option value="0" disabled="disabled" selected="selected" >'.__('Please select an album', 'wp-photo-album-plus').'</option>';
							// All standard albums
							foreach ( $albums as $album ) {
								$value = $album['id'];
								$alb = $album['id'];
								$photos = $wpdb->get_results($wpdb->prepare( "SELECT `id`, `name`, `album`, `ext` FROM `".WPPA_PHOTOS."` WHERE `album` = %s ".wppa_get_photo_order($alb)." LIMIT 100", $alb), ARRAY_A );
								if ( $photos ) foreach ( $photos as $photo ) {
									$photo_id = wppa_opt( 'file_system' ) == 'tree' ? wppa_expand_id( $photo['id'] ) : $photo['id'];
									$value .= '|'.$photo_id.'.'.$photo['ext'];
								}
								else $value .= '|';
								$note = ' ('.$album['id'].')';
								if ( count($photos) <= wppa_opt( 'min_thumbs' ) ) $note .= ' *';
								$result .= '<option value="'.$value.'" >'.stripslashes(__($album['name'], 'wp-photo-album-plus')).$note.'</option>';
							}
							// #last
								$value = '#last';
								$alb = $albums[0]['id'];
								$photos = $wpdb->get_results($wpdb->prepare( "SELECT `id`, `name`, `album`, `ext` FROM `".WPPA_PHOTOS."` WHERE `album` = %s ".wppa_get_photo_order($alb)." LIMIT 100", $alb), ARRAY_A );
								if ( $photos ) foreach ( $photos as $photo ) {
									$photo_id = wppa_opt( 'file_system' ) == 'tree' ? wppa_expand_id($photo['id']) : $photo['id'];
									$value .= '|'.$photo_id.'.'.$photo['ext'];
								}
								else $value .= '|';
								$result .= '<option value="'.$value.'" >'.__('- The latest created album -', 'wp-photo-album-plus').'</option>';
							// #topten
								$value = '#topten';
								$photos = $wpdb->get_results( "SELECT `id`, `name`, `album`, `ext` FROM `".WPPA_PHOTOS."` ORDER BY `mean_rating` DESC LIMIT ".wppa_opt( 'topten_count' ), ARRAY_A );
								if ( $photos ) foreach ( $photos as $photo ) {
									$photo_id = wppa_opt( 'file_system' ) == 'tree' ? wppa_expand_id($photo['id']) : $photo['id'];
									$value .= '|'.$photo_id.'.'.$photo['ext'];
								}
								else $value .= '|';
								$result .= '<option value = "'.$value.'" >'.__('--- The top rated photos ---', 'wp-photo-album-plus').'</option>';
							// #lasten
								$value = '#lasten';
								$photos = $wpdb->get_results( "SELECT `id`, `name`, `album`, `ext` FROM `".WPPA_PHOTOS."` ORDER BY `timestamp` DESC LIMIT ".wppa_opt( 'lasten_count' ), ARRAY_A );
								if ( $photos ) foreach ( $photos as $photo ) {
									$photo_id = wppa_opt( 'file_system' ) == 'tree' ? wppa_expand_id($photo['id']) : $photo['id'];
									$value .= '|'.$photo_id.'.'.$photo['ext'];
								}
								else $value .= '|';
								$result .= '<option value = "'.$value.'" >'.__('--- The most recently uploaded photos ---', 'wp-photo-album-plus').'</option>';
							// #featen
								$value = '#featen';
								$photos = $wpdb->get_results( "SELECT `id`, `name`, `album`, `ext` FROM `".WPPA_PHOTOS."` WHERE `status` = 'featured' ORDER BY RAND() DESC LIMIT ".wppa_opt( 'featen_count' ), ARRAY_A );
								if ( $photos ) foreach ( $photos as $photo ) {
									$photo_id = wppa_opt( 'file_system' ) == 'tree' ? wppa_expand_id($photo['id']) : $photo['id'];
									$value .= '|'.$photo_id.'.'.$photo['ext'];
								}
								else $value .= '|';
								$result .= '<option value = "'.$value.'" >'.__('--- A random selection of featured photos ---', 'wp-photo-album-plus').'</option>';
							// #comten
								$value = '#comten';
								$comments = $wpdb->get_results( "SELECT `id`, `photo` FROM `".WPPA_COMMENTS."` ORDER BY `timestamp` DESC", ARRAY_A );
								$photos = false;
								$done = array();
								if ( $comments ) foreach ( $comments as $comment ) {
									if ( count($done) < wppa_opt( 'comten_count' ) && ! in_array($comment['photo'], $done) ) {
										$done[] = $comment['photo'];
										$photos[] = $wpdb->get_row( "SELECT `id`, `name`, `album`, `ext` FROM `".WPPA_PHOTOS."` WHERE `id` = ".$comment['photo'], ARRAY_A );
									}
								}
								if ( $photos ) foreach ( $photos as $photo ) {
									$photo_id = wppa_opt( 'file_system' ) == 'tree' ? wppa_expand_id($photo['id']) : $photo['id'];
									$value .= '|'.$photo_id.'.'.$photo['ext'];
								}
								else $value .= '|';
								$result .= '<option value = "'.$value.'" >'.__('--- The most recently commented photos ---', 'wp-photo-album-plus').'</option>';
							// #tags
								$value = '#tags';
								$result .= '<option value = "'.$value.'" >'.__('--- Photos that have certain tags ---', 'wp-photo-album-plus').'</option>';
							// #all
								$value = '#all';
								$photos = $wpdb->get_results( "SELECT `id`, `name`, `album`, `ext` FROM `".WPPA_PHOTOS."` ".wppa_get_photo_order('0')." LIMIT 100", ARRAY_A );
								if ( $photos ) foreach ( $photos as $photo ) {
									$photo_id = wppa_opt( 'file_system' ) == 'tree' ? wppa_expand_id($photo['id']) : $photo['id'];
									$value .= '|'.$photo_id.'.'.$photo['ext'];
								}
								else $value .= '|';
								$result .= '<option value = "'.$value.'" >'.__('--- All photos in the system ---', 'wp-photo-album-plus').'</option>';
						}
						else {
							$result .= '<option value="0" >'.__('There are no albums yet', 'wp-photo-album-plus').'</option>';
						}
					$result .=
					'</select>'.
					'<input type="text" id="wppagallery-alb" name="alb" value="" style="width:50px; display:none; background-color:#ddd;" class="wppagallery-extra" title="Enter albumnumber if not systemwide" />'.
					'<input type="text" id="wppagallery-cnt" name="cnt" value="" style="width:50px; display:none; background-color:#ddd;" class="wppagallery-extra" title="Enter count if not default" />'.
					'<br />'.
					'<small class="wppagallery-album" >'.
						__('Specify the album to be used or --- A special selection of photos ---', 'wp-photo-album-plus').'<br />'.
						__('In an upload box, the album is optional. When no album is specified: a selection box will be displayed of the albums the user has the right to upload.', 'wp-photo-album-plus').'<br />'.
						__('* Album contains less than the minimun number of photos', 'wp-photo-album-plus').
					'</small>'.
				'</td>'.
			'</tr>'.

			'<tr class="wppagallery-photo" style="display:none;" >'.
				'<th><label for="wppagallery-photo" style="display:none;" class="wppagallery-photo" >'.__('The Photo to be used:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-photo" name="photo" style="display:none;" class="wppagallery-photo" onchange="wppaTinyMcePhotoPreview(this.value)" >';
						$photos = $wpdb->get_results( "SELECT `id`, `name`, `album`, `ext` FROM `".WPPA_PHOTOS."` ORDER BY `timestamp` DESC LIMIT 100", ARRAY_A );
						if ($photos) {
							$result .= '<option value="0" disabled="disabled" selected="selected" >'.__('Please select a photo', 'wp-photo-album-plus').'</option>';
							foreach ( $photos as $photo ) {
								$name = stripslashes(__($photo['name'], 'wp-photo-album-plus'));
								if ( strlen($name) > '50') $name = substr($name, '0', '50').'...';
								if ( get_option( 'wppa_file_system' ) == 'flat' ) {
									$result .= '<option value="'.$photo['id'].'.'.$photo['ext'].'" >'.$name.' ('.wppa_get_album_name($photo['album']).')'.'</option>';
								}
								else {
									$result .= '<option value="'.wppa_expand_id($photo['id']).'.'.$photo['ext'].'" >'.$name.' ('.wppa_get_album_name($photo['album']).')'.'</option>';
								}
							}
							$result .=  '<option value="#last" >'.__('--- The most recently uploaded photo ---', 'wp-photo-album-plus').'</option>'.
										'<option value="#potd" >'.__('--- The photo of the day ---', 'wp-photo-album-plus').'</option>';
						}
						else {
							$result .= '<option value="0" >'.__('There are no photos yet', 'wp-photo-album-plus').'</option>';
						}
					$result .=
					'</select>'.
					'<br />'.
					'<small style="display:none;" class="wppagallery-photo" >'.
						__('Specify the photo to be used', 'wp-photo-album-plus').'<br />'.
						__('You can select from a maximum of 100 most recently added photos', 'wp-photo-album-plus').'<br />'.
					'</small>'.
				'</td>'.
			'</tr>'.

			'<tr class="wppagallery-tags" style="display:none;" >'.
				'<th><label for="wppagallery-tags">'.__('The tags the photos should have:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-tags" multiple="multiple">'.
						'<option value="" >'.__('--- please select tag(s) ---', 'wp-photo-album-plus').'</option>';
						$tags = wppa_get_taglist();
						if ( $tags ) foreach ( array_keys($tags) as $tag ) {
							$result .= '<option value="'.$tag.'" >'.$tag.'</option>';
						}
						$result .=
					'</select>'.

					'<div><input type="checkbox" id="wppagallery-andor" />&nbsp;<small>'.__('If you want that the photos have all the selected tags, check this box. Leave it unchecked if the photo must have atleast only one of the selected tags', 'wp-photo-album-plus').'</small></div>'.
				'</td>'.
			'</tr>'.

			'<tr>'.
				'<th><label for="wppagallery-size">'.__('The size of the display:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<input type="text" id="wppagallery-size" value="" />'.
					'<br />'.
					'<small>'.
						__('Specify the horizontal size in pixels or <span style="color:blue" >auto</span>.', 'wp-photo-album-plus').' '.
						__('A value less than <span style="color:blue" >100</span> will automaticly be interpreted as a <span style="color:blue" >percentage</span> of the available space.', 'wp-photo-album-plus').'<br />'.
						__('Leave this blank for default size', 'wp-photo-album-plus').'</small>'.
				'</td>'.
			'</tr>'.

			'<tr>'.
				'<th><label for="wppagallery-align">'.__('Horizontal alignment:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-align" name="align" >'.
						'<option value="none" >'.__('--- none ---', 'wp-photo-album-plus').'</option>'.
						'<option value="left" >'.__('left', 'wp-photo-album-plus').'</option>'.
						'<option value="center" >'.__('center', 'wp-photo-album-plus').'</option>'.
						'<option value="right" >'.__('right', 'wp-photo-album-plus').'</option>'.
					'</select>'.
					'<br />'.
					'<small>'.__('Specify the alignment to be used or --- none ---', 'wp-photo-album-plus').'</small>'.
				'</td>'.
			'</tr>'.

		'</table>'.
		'<p class="submit">'.
			'<input type="button" id="wppagallery-submit" class="button-primary" value="'.__('Insert Gallery', 'wp-photo-album-plus').'" name="submit" />&nbsp;'.
		'</p>'.
	'</div>';
	return $result;
}
?>