<?php
/* wppa-items.php
* Package: wp-photo-album-plus
*
* Contains functions to retrieve album and photo items
* Version 6.4.06
*
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

// Bring album into cache
// Returns album info and puts it also in global $album
function wppa_cache_album( $id, $data = '' ) {
global $wpdb;
static $album;
static $album_cache_2;

	// Action?
	if ( $id == 'invalidate' ) {
		if ( isset( $album_cache_2[$data] ) ) unset( $album_cache_2[$data] );
		$album = false;
		return false;
	}
	if ( $id == 'add' ) {
		if ( ! $data ) {							// Nothing to add
			return false;
		}
		elseif ( isset( $data['id'] ) ) { 			// Add a single album to 2nd level cache
			$album_cache_2[$data['id']] = $data;	// Looks valid
		}
		else foreach( $data as $album ) {			// Add multiple
			if ( isset( $album['id'] ) ) {			// Looks valid
				$album_cache_2[$album['id']] = $album;
			}
		}
		return false;
	}
	if ( $id == 'count' ) {
		if ( is_array( $album_cache_2 ) ) {
			return count( $album_cache_2 );
		}
		else {
			return false;
		}
	}
	if ( wppa_is_enum( $id ) && ! wppa_is_int( $id ) ) {
		return false;	// enums not supporte yet
	}
	if ( $id == '-9' ) {
		return false;
	}
	if ( ! wppa_is_int( $id ) || $id < '1' ) {
		$album = false;
		wppa_dbg_msg( 'Invalid arg wppa_cache_album('.$id.')', 'red' );
		return false;
	}

	// In first level cache?
	if ( isset( $album['id'] ) && $album['id'] == $id ) {
		wppa_dbg_q( 'G-A1' );
		return $album;
	}

	// In  second level cache?
	if ( ! empty( $album_cache_2 ) ) {
		if ( in_array( $id, array_keys( $album_cache_2 ) ) ) {
			$album = $album_cache_2[$id];
			wppa_dbg_q( 'G-A2' );
			return $album;
		}
	}

	// Not in cache, do query
	$album = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `".WPPA_ALBUMS."` WHERE `id` = %s", $id ), ARRAY_A );
	wppa_dbg_q( 'Q-A' );

	// Found one?
	if ( $album ) {
		// Store in second level cache
		$album_cache_2[$id] = $album;
		return $album;
	}
	else {
		wppa_dbg_msg( 'Album '.$id.' does not exist (cache album)', 'red' );
		wppa_log( 'dbg', 'Album '.$id.' does not exist (cache album)', true );
		return false;
	}
}

// Bring photo into cache
// Returns photo info and puts it also in global $thumb
function wppa_cache_photo( $id, $data = '' ) {
	return wppa_cache_thumb( $id, $data );
}
function wppa_cache_thumb( $id, $data = '' ) {
global $wpdb;
static $thumb;
static $thumb_cache_2;

	// Invalidate ?
	if ( $id == 'invalidate' ) {
		if ( isset( $thumb_cache_2[$data] ) ) unset( $thumb_cache_2[$data] );
		$thumb = false;
		return false;
	}

	// Add ?
	if ( $id == 'add' ) {
		if ( ! $data ) {							// Nothing to add
			return false;
		}
		elseif ( isset( $data['id'] ) ) { 			// Add a single thumb to 2nd level cache
			if ( count( $data ) < 31 ) {
				wppa_log( 'Err', 'Attempt to cache add incomplete photo item '.$data['id'].'. Only '.count( $data ).' items supplied.' );
				return false;
			}
			$thumb_cache_2[$data['id']] = $data;	// Looks valid
		}
		elseif ( count( $data ) > 10000 ) {
			return false;							// Too many, may cause out of memory error
		}
		else foreach( $data as $thumb ) {			// Add multiple
			if ( isset( $thumb['id'] ) ) {			// Looks valid
				if ( count( $thumb ) < 31 ) {
					wppa_log( 'Err', 'Attempt to cache add incomplete photo item '.$thumb['id'].'. Only '.count( $thumb ).' items supplied.' );
					return false;
				}
				$thumb_cache_2[$thumb['id']] = $thumb;
			}
		}
		return false;
	}

	// Count ?
	if ( $id == 'count' ) {
		if ( is_array( $thumb_cache_2 ) ) {
			return count( $thumb_cache_2 );
		}
		else {
			return false;
		}
	}

	// Error in arg?
	if ( ! wppa_is_int( $id ) || $id < '1' ) {
		wppa_dbg_msg( 'Invalid arg wppa_cache_thumb('.$id.')', 'red' );
		$thumb = false;
		wppa( 'current_photo', false );
		return false;
	}

	// In first level cache?
	if ( isset( $thumb['id'] ) && $thumb['id'] == $id ) {
		wppa_dbg_q( 'G-T1' );
		wppa( 'current_photo', $thumb );
		return $thumb;
	}

	// In  second level cache?
	if ( ! empty( $thumb_cache_2 ) ) {
		if ( in_array( $id, array_keys( $thumb_cache_2 ) ) ) {
			$thumb = $thumb_cache_2[$id];
			wppa_dbg_q( 'G-T2' );
			wppa( 'current_photo', $thumb );
			return $thumb;
		}
	}

	// Not in cache, do query
	$thumb = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `".WPPA_PHOTOS."` WHERE `id` = %s", $id ), ARRAY_A );
	wppa_dbg_q( 'Q-P' );

	// Found one?
	if ( $thumb ) {
		// Store in second level cache
		$thumb_cache_2[$id] = $thumb;
		wppa( 'current_photo', $thumb );
		return $thumb;
	}
	else {
		wppa_dbg_msg( 'Photo '.$id.' does not exist', 'red' );
		wppa( 'current_photo', false );
		return false;
	}
}

// get the name of a full sized image
function wppa_get_photo_name( $id, $add_owner = false, $add_medal = false, $esc_js = false, $show_name = true ) {

	// Init
	$result = '';

	// Verify args
	if ( ! is_numeric( $id ) || $id < '1' ) {
		wppa_dbg_msg( 'Invalid arg wppa_get_photo_name( '.$id.' )', 'red' );
		return '';
	}

	// Get data
	$thumb = wppa_cache_thumb( $id );
	if ( $show_name ) {
		$result .= __( stripslashes( $thumb['name'] ) , 'wp-photo-album-plus');
	}

	// Add owner?
	if ( $add_owner ) {
		$user = get_user_by( 'login', $thumb['owner'] );
		if ( $user ) {
			if ( $show_name ) {
				if ( wppa_switch( 'owner_on_new_line' ) ) {
					if ( ! $esc_js ) {
						$result .= '<br />';
					}
					else {
						$result .= ' [br /]';
					}
				}
				else {
					$result .= ' ';
				}
				$result .= '('.$user->display_name.')';
			}
			else {
				$result .= ' '.$user->display_name;
			}
		}
	}

	// For js use?
	if ( $esc_js ) $result = esc_js( $result );

	// Medal?
	if ( $add_medal ) {
		$color = wppa_opt( 'medal_color' );
		$wppa_url = is_ssl() ? str_replace( 'http://', 'https://', WPPA_URL ) : WPPA_URL;	// Probably redundant... but it is not clear in to the codex if plugins_url() returns https
		if ( $thumb['status'] == 'gold' ) $result .= '<img src="'.$wppa_url.'/images/medal_gold_'.$color.'.png" title="'.esc_attr(__('Gold medal', 'wp-photo-album-plus')).'" alt="'.__('Gold', 'wp-photo-album-plus').'" style="border:none; margin:0; padding:0; box-shadow:none; height:32px;" />';
		if ( $thumb['status'] == 'silver' ) $result .= '<img src="'.$wppa_url.'/images/medal_silver_'.$color.'.png" title="'.esc_attr(__('Silver medal', 'wp-photo-album-plus')).'" alt="'.__('Silver', 'wp-photo-album-plus').'" style="border:none; margin:0; padding:0; box-shadow:none; height:32px;" />';
		if ( $thumb['status'] == 'bronze' ) $result .= '<img src="'.$wppa_url.'/images/medal_bronze_'.$color.'.png" title="'.esc_attr(__('Bronze medal', 'wp-photo-album-plus')).'" alt="'.__('Bronze', 'wp-photo-album-plus').'" style="border:none; margin:0; padding:0; box-shadow:none; height:32px;" />';
	}

	// To prevent recursive rendering of scripts or shortcodes:
	$result = str_replace( array( '%%wppa%%', '[wppa', '[/wppa]' ), array( '%-wppa-%', '{wppa', '{/wppa}' ), $result );
	if ( wppa_switch( 'allow_foreign_shortcodes_general' ) ) {
		$result = do_shortcode( $result );
	}
	else {
		$result = strip_shortcodes( $result );
	}

	return $result;
}

// get the description of an image
function wppa_get_photo_desc( $id, $do_shortcodes = false, $do_geo = false ) {

	// Verify args
	if ( ! is_numeric( $id ) || $id < '1' ) {
		wppa_dbg_msg( 'Invalid arg wppa_get_photo_desc( '.$id.' )', 'red' );
		return '';
	}

	// Get data
	$thumb = wppa_cache_thumb( $id );
	$desc = $thumb['description'];			// Raw data
	$desc = stripslashes( $desc );			// Unescape
	$desc = __( $desc , 'wp-photo-album-plus');					// qTranslate

	// To prevent recursive rendering of scripts or shortcodes:
	$desc = str_replace( array( '%%wppa%%', '[wppa', '[/wppa]' ), array( '%-wppa-%', '{wppa', '{/wppa}' ), $desc );

	// Geo
	if ( $thumb['location'] && ! wppa_in_widget() && strpos( wppa_opt( 'custom_content' ), 'w#location' ) !== false && $do_geo == 'do_geo' ) {
		wppa_do_geo( $id, $thumb['location'] );
	}

	// Other keywords
	if ( strpos($desc, 'w#') !== false ) {	// Is there any 'w#' ?
		// Keywords
		$desc = str_replace( 'w#albumname', wppa_get_album_name( $thumb['album'] ), $desc );
		$desc = str_replace( 'w#albumid', $thumb['album'], $desc );
		$keywords = array('name', 'filename', 'owner', 'id', 'tags', 'views', 'album');
		foreach ( $keywords as $keyword ) {
			$replacement = __( trim( stripslashes( $thumb[$keyword] ) ) , 'wp-photo-album-plus');
			if ( $keyword == 'tags' ) {
				$replacement = trim( $replacement, ',' );
			}
			if ( $replacement == '' ) $replacement = '&lsaquo;'.__( 'none' , 'wp-photo-album-plus').'&rsaquo;';
			$desc = str_replace( 'w#'.$keyword, $replacement, $desc );
		}
		$desc = str_replace( 'w#url', wppa_get_lores_url( $id ), $desc );
		$desc = str_replace( 'w#hrurl', esc_attr( wppa_get_hires_url( $id ) ), $desc );
		$desc = str_replace( 'w#tnurl', wppa_get_tnres_url( $id ), $desc );
		$desc = str_replace( 'w#pl', wppa_get_source_pl( $id ), $desc );
		$desc = str_replace( 'w#rating', wppa_get_rating_by_id( $id, 'nolabel' ), $desc );

		// Art monkey sizes
		if ( strpos( $desc, 'w#amx' ) !== false || strpos( $desc, 'w#amy' ) !== false || strpos( $desc, 'w#amfs' ) !== false ) {
			$amxy = wppa_get_artmonkey_size_a( $id );
			if ( is_array( $amxy ) ) {
				$desc = str_replace( 'w#amx', $amxy['x'], $desc );
				$desc = str_replace( 'w#amy', $amxy['y'], $desc );
				$desc = str_replace( 'w#amfs', $amxy['s'], $desc );
			}
			else {
				$desc = str_replace( 'w#amx', 'N.a.', $desc );
				$desc = str_replace( 'w#amy', 'N.a.', $desc );
				$desc = str_replace( 'w#amfs', 'N.a.', $desc );
			}
		}

		// Timestamps
		$timestamps = array( 'timestamp', 'modified' );
		foreach ( $timestamps as $timestamp ) {
			if ( $thumb[$timestamp] ) {
				$desc = str_replace( 'w#'.$timestamp, wppa_local_date( get_option( 'date_format', "F j, Y," ).' '.get_option( 'time_format', "g:i a" ), $thumb[$timestamp] ), $desc );
			}
			else {
				$desc = str_replace( 'w#'.$timestamp, '&lsaquo;'.__( 'unknown' , 'wp-photo-album-plus').'&rsaquo;', $desc );
			}
		}

		// Custom data fields
		if ( wppa_switch( 'custom_fields' ) ) {
			$custom = $thumb['custom'];
			$custom_data = $custom ? unserialize( $custom ) : array( '', '', '', '', '', '', '', '', '', '' );
			for ( $i = '0'; $i < '10'; $i++ ) {
				if ( wppa_opt( 'custom_caption_'.$i ) ) {				// Field defined
					if ( wppa_switch( 'custom_visible_'.$i ) ) {		// May be displayed
						$desc = str_replace( 'w#cc'.$i, __( wppa_opt( 'custom_caption_'.$i ) , 'wp-photo-album-plus') . ':', $desc );	// Caption
						$desc = str_replace( 'w#cd'.$i, __( stripslashes( $custom_data[$i] ) , 'wp-photo-album-plus'), $desc );	// Data
					}
					else { 												// May not be displayed
						$desc = str_replace( 'w#cc'.$i, '', $desc ); 	// Remove
						$desc = str_replace( 'w#cd'.$i, '', $desc ); 	// Remove
					}
				}
				else { 													// Field not defined
					$desc = str_replace( 'w#cc'.$i, '', $desc ); 		// Remove
					$desc = str_replace( 'w#cd'.$i, '', $desc ); 		// Remove
				}
			}
		}
	}

	// Shortcodes
	if ( $do_shortcodes ) $desc = do_shortcode( $desc );	// Do shortcodes if wanted
	else $desc = strip_shortcodes( $desc );					// Remove shortcodes if not wanted

	$desc = wppa_html( $desc );				// Enable html
	$desc = balanceTags( $desc, true );		// Balance tags
	$desc = wppa_filter_iptc( $desc, $id );	// Render IPTC tags
	$desc = wppa_filter_exif( $desc, $id );	// Render EXIF tags
	$desc = make_clickable( $desc );		// Auto make a tags for links
	$desc = convert_smilies( $desc );		// Make smilies visible

	// CMTooltipGlossary on board?
	$desc = wppa_filter_glossary( $desc );

	return $desc;
}

// get album name
function wppa_get_album_name( $id, $extended = false ) {

	if ( $id > '0' ) {
		$album = wppa_cache_album( $id );
	}
	else {
		$album = false;
	}

    $name = '';

	if ( $extended ) {
		if ( $id == '0' ) {
			$name = __( '--- none ---', 'wp-photo-album-plus' );
			return $name;
		}
		if ( $id == '-1' ) {
			$name = __( '--- separate ---', 'wp-photo-album-plus' );
			return $name;
		}
		if ( $id == '-2' ) {
			$name = __( '--- all ---', 'wp-photo-album-plus' );
			return $name;
		}
		if ( $id == '-3' ) {
			$name = __( '--- owner/public ---', 'wp-photo-album-plus' );
			return $name;
		}
		if ( $id == '-9' ) {
			$name = __( '--- deleted ---', 'wp-photo-album-plus' );
			return $name;
		}
		if ( $extended == 'raw' ) {
			$name = $album['name'];
			return $name;
		}
	}
	else {
		if ( $id == '-2' ) {
			$name = __( 'All Albums', 'wp-photo-album-plus' );
			return $name;
		}
		if ( $id == '-3' ) {
			$name = __( 'My and public albums', 'wp-photo-album-plus' );
		}
	}

	if ( ! $id ) return '';
	elseif ( $id == '-9' ) {
		return '';
	}
	elseif ( ! is_numeric( $id ) || $id < '1' ) {
		wppa_dbg_msg( 'Invalid arg wppa_get_album_name( '.$id.', '.$extended.' )', 'red' );
		return '';
	}
    else {
		if ( ! $album ) {
			$name = __( '--- deleted ---', 'wp-photo-album-plus');
		}
		else {
			$name = __( stripslashes( $album['name'] ) );
		}
    }

	// To prevent recursive rendering of scripts or shortcodes:
	$name = str_replace( array( '%%wppa%%', '[wppa', '[/wppa]' ), array( '%-wppa-%', '{wppa', '{/wppa}' ), $name );
	if ( wppa_switch( 'allow_foreign_shortcodes_general' ) ) {
		$name = do_shortcode( $name );
	}
	else {
		$name = strip_shortcodes( $name );
	}

	return $name;
}

// get album description
function wppa_get_album_desc( $id ) {

	if ( ! is_numeric( $id ) || $id < '1' ) wppa_dbg_msg( 'Invalid arg wppa_get_album_desc( '.$id.' )', 'red' );
	$album = wppa_cache_album( $id );
	$desc = $album['description'];			// Raw data
	if ( ! $desc ) return '';				// No content, need no filtering
	$desc = stripslashes( $desc );			// Unescape
	$desc = __( $desc , 'wp-photo-album-plus');					// qTranslate
	$desc = wppa_html( $desc );				// Enable html
	$desc = balanceTags( $desc, true );		// Balance tags

	if ( strpos($desc, 'w#') !== false ) {	// Is there any 'w#' ?
		// Keywords
		$keywords = array( 'name', 'owner', 'id', 'views' );
		foreach ( $keywords as $keyword ) {
			$replacement = __( trim( stripslashes( $album[$keyword] ) ) , 'wp-photo-album-plus');
			if ( $replacement == '' ) $replacement = '&lsaquo;'.__( 'none' , 'wp-photo-album-plus').'&rsaquo;';
			$desc = str_replace( 'w#'.$keyword, $replacement, $desc );
		}

		// Timestamps
		$timestamps = array( 'timestamp', 'modified' );
		foreach ( $timestamps as $timestamp ) {
			if ( $album[$timestamp] ) {
				$desc = str_replace( 'w#'.$timestamp, wppa_local_date( get_option( 'date_format', "F j, Y," ).' '.get_option( 'time_format', "g:i a" ), $album['timestamp'] ), $desc );
			}
			else {
				$desc = str_replace( 'w#'.$timestamp, '&lsaquo;'.__('unknown', 'wp-photo-album-plus').'&rsaquo;', $desc );
			}
		}
	}

	// To prevent recursive rendering of scripts or shortcodes:
	$desc = str_replace( array( '%%wppa%%', '[wppa', '[/wppa]' ), array( '%-wppa-%', '{wppa', '{/wppa}' ), $desc );
	if ( wppa_switch( 'allow_foreign_shortcodes_general' ) ) {
		$desc = do_shortcode( $desc );
	}
	else {
		$desc = strip_shortcodes( $desc );
	}

	// Convert links and mailto:
	$desc = make_clickable( $desc );

	// CMTooltipGlossary on board?
	$desc = wppa_filter_glossary( $desc );

	return $desc;
}

// Get any album field of any album, raw data from the db
function wppa_get_album_item( $id, $item ) {

	$album = wppa_cache_album( $id );

	if ( $album ) {
		if ( isset( $album[$item] ) ) {
			return trim( $album[$item] );
		}
		else {
			wppa_dbg_msg( 'Album item ' . $item . ' does not exist. ( get_album_item )', 'red' );
		}
	}
	else {
		wppa_dbg_msg( 'Album ' . $id . ' does not exist. ( get_album_item )', 'red' );
	}
	return false;
}

// Get any photo field of any photo, raw data from the db
function wppa_get_photo_item( $id, $item ) {

	$photo = wppa_cache_photo( $id );

	if ( $photo ) {
		if ( isset( $photo[$item] ) ) {
			return trim( $photo[$item] );
		}
		else {
			wppa_dbg_msg( 'Photo item ' . $item . ' does not exist. ( get_photo_item )', 'red' );
		}
	}
	else {
		wppa_dbg_msg( 'Photo ' . $id . ' does not exist. ( get_photo_item )', 'red' );
	}
	return false;
}

// Get sizes routines
// $id: int photo id
// $force: bool force recalculation, both x and y
function wppa_get_thumbx( $id, $force = false ) {
	if ( wppa_is_video( $id ) ) {
		$x = wppa_get_videox( $id );
		$y = wppa_get_videoy( $id );
		if ( $x > $y ) { 	// Landscape
			$result = wppa_opt( 'thumbsize' );
		}
		else {
			$result = wppa_opt( 'thumbsize' ) * $x / $y;
		}
	}
	else {
		$result = wppa_get_thumbphotoxy( $id, 'thumbx', $force );
	}
	if ( ! $result && wppa_has_audio( $id ) ) {
		$result = wppa_opt( 'thumbsize' );
	}
	return $result;
}
function wppa_get_thumby( $id, $force = false ) {
	if ( wppa_is_video( $id ) ) {
		$x = wppa_get_videox( $id );
		$y = wppa_get_videoy( $id );
		if ( $x > $y ) { 	// Landscape
			$result = wppa_opt( 'thumbsize' ) * $y / $x;
		}
		else {
			$result = wppa_opt( 'thumbsize' );
		}
	}
	else {
		$result = wppa_get_thumbphotoxy( $id, 'thumby', $force );
	}
	if ( ! $result && wppa_has_audio( $id ) ) {
		$result = wppa_opt( 'thumbsize' );// * 1080 / 1920;
		$siz = getimagesize( WPPA_UPLOAD_PATH . '/' . wppa_opt( 'audiostub' ) );
		$result *= $siz['1'] / $siz['0'];
	}
	return $result;
}
function wppa_get_photox( $id, $force = false ) {
	return wppa_get_thumbphotoxy( $id, 'photox', $force );
}
function wppa_get_photoy( $id, $force = false ) {
	return wppa_get_thumbphotoxy( $id, 'photoy', $force );
}
function wppa_get_thumbratioxy( $id ) {
	if ( wppa_is_video( $id ) ) {
		$result = wppa_get_videox( $id ) / wppa_get_videoy( $id );
	}
	else {
		if ( wppa_get_thumby( $id ) ) {
			$result = wppa_get_thumbx( $id ) / wppa_get_thumby( $id );
		}
		else {
			$result = '1';
		}
	}
	return $result;
}
function wppa_get_thumbratioyx( $id ) {
	if ( wppa_is_video( $id ) ) {
		$result = wppa_get_videoy( $id ) / wppa_get_videox( $id );
	}
	else {
		if ( wppa_get_thumbx( $id ) ) {
			$result = wppa_get_thumby( $id ) / wppa_get_thumbx( $id );
		}
		else {
			$result = '1';
		}
	}
	return $result;
}
function wppa_get_thumbphotoxy( $id, $key, $force = false ) {

	$result = wppa_get_photo_item( $id, $key );
	if ( $result && ! $force ) {
		return $result; 			// Value found
	}

	if ( $key == 'thumbx' || $key == 'thumby' ) {
		$file = wppa_get_thumb_path( $id );
	}
	else {
		$file = wppa_get_photo_path( $id );
	}

	if ( wppa_get_ext( $file ) == 'xxx' ) {
//		if ( $key == 'photox' || $key == 'photoy' ) {
			$file = wppa_fix_poster_ext( $file, $id );
//		}
	}

	if ( ! is_file( $file ) && ! $force ) {
		return '0';	// File not found
	}

	if ( is_file( $file ) ) {
		$size = getimagesize( $file );
	}
	else {
		$size = array( '0', '0');
	}
	if ( is_array( $size ) ) {
		if ( $key == 'thumbx' || $key == 'thumby' ) {
			wppa_update_photo( array( 'id' => $id, 'thumbx' => $size[0], 'thumby' => $size[1] ) );
		}
		else {
			wppa_update_photo( array( 'id' => $id, 'photox' => $size[0], 'photoy' => $size[1] ) );
		}
		wppa_cache_photo( 'invalidate', $id );
	}

	if ( $key == 'thumbx' || $key == 'photox' ) {
		return $size[0];
	}
	else {
		return $size[1];
	}
}

function wppa_get_imagexy( $id, $key = 'photo' ) {
	if ( wppa_is_video( $id ) ) {
		$result = array( wppa_get_videox( $id ), wppa_get_videoy( $id ) );
	}
	elseif ( $key == 'thumb' ) {
		$result = array( wppa_get_thumbx( $id ), wppa_get_thumby( $id ) );
	}
	else {
		$result = array( wppa_get_photox( $id ), wppa_get_photoy( $id ) );
	}
	return $result;
}

function wppa_get_imagex( $id, $key = 'photo' ) {
	if ( wppa_is_video( $id ) ) {
		$result = wppa_get_videox( $id );
	}
	elseif ( $key == 'thumb' ) {
		$result = wppa_get_thumbx( $id );
	}
	else {
		$result = wppa_get_photox( $id );
	}
	return $result;
}

function wppa_get_imagey( $id, $key = 'photo' ) {
	if ( wppa_is_video( $id ) ) {
		$result = wppa_get_videoy( $id );
	}
	elseif ( $key == 'thumb' ) {
		$result = wppa_get_thumby( $id );
	}
	else {
		$result = wppa_get_photoy( $id );
	}
	return $result;
}