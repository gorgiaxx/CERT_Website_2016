<?php
/* wppa-utils.php
* Package: wp-photo-album-plus
*
* Contains low-level utility routines
* Version 6.4.08
*
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

global $wppa_supported_photo_extensions;
$wppa_supported_photo_extensions = array( 'jpg', 'jpeg', 'png', 'gif' );

// Get url in wppa dir
function wppa_url( $arg ) {
	return WPPA_URL . '/' . $arg;
}

// get url of thumb
function wppa_get_thumb_url( $id, $system = 'flat', $x = '0', $y = '0' ) {
global $blog_id;

	$thumb = wppa_cache_thumb( $id );

	// If in the cloud...
	$is_old = wppa_too_old_for_cloud( $id );
	if ( wppa_cdn( 'front' ) && ! wppa_is_multi( $id ) && ! $is_old && ! wppa_is_stereo( $id ) ) {
		if ( $x && $y ) {		// Only when size is given !! To prevent download of the fullsize image
			switch ( wppa_cdn( 'front' ) ) {
				case 'cloudinary':
					$transform	= explode( ':', wppa_opt( 'thumb_aspect' ) );
					$t 			= 'limit';
					if ( $transform['2'] == 'clip' ) $t = 'fill';
					if ( $transform['2'] == 'padd' ) $t = 'pad,b_black';
					$q 			= wppa_opt( 'jpeg_quality' );
					$sizespec 	= ( $x && $y ) ? 'w_'.$x.',h_'.$y.',c_'.$t.',q_'.$q.'/' : '';
					$prefix 	= ( is_multisite() && ! WPPA_MULTISITE_GLOBAL ) ? $blog_id.'-' : '';
					$s = is_ssl() ? 's' : '';
					$url = 'http'.$s.'://res.cloudinary.com/'.get_option('wppa_cdn_cloud_name').'/image/upload/'.$sizespec.$prefix.$thumb['id'].'.'.$thumb['ext'];
					return $url;
					break;

			}
		}
	}

	if ( get_option('wppa_file_system') == 'flat' ) $system = 'flat';	// Have been converted, ignore argument
	if ( get_option('wppa_file_system') == 'tree' ) $system = 'tree';	// Have been converted, ignore argument
	if ( ! is_numeric($id) || $id < '1' ) wppa_dbg_msg('Invalid arg wppa_get_thumb_url('.$id.')', 'red');
	if ( $system == 'tree' ) {
		$url = WPPA_UPLOAD_URL.'/thumbs/'.wppa_expand_id($thumb['id']).'.'.$thumb['ext'].'?ver='.get_option('wppa_thumb_version', '1');
	}
	else {
		$url = WPPA_UPLOAD_URL.'/thumbs/'.$thumb['id'].'.'.$thumb['ext'].'?ver='.get_option('wppa_thumb_version', '1');
	}
	return $url;
}

// Bump thumbnail version number
function wppa_bump_thumb_rev() {
	wppa_update_option('wppa_thumb_version', get_option('wppa_thumb_version', '1') + '1');
}

// get path of thumb
function wppa_get_thumb_path( $id, $system = 'flat' ) {

	$thumb = wppa_cache_thumb( $id );

	if ( get_option('wppa_file_system') == 'flat' ) $system = 'flat';	// Have been converted, ignore argument
	if ( get_option('wppa_file_system') == 'tree' ) $system = 'tree';	// Have been converted, ignore argument
	if ( ! is_numeric($id) || $id < '1' ) wppa_dbg_msg('Invalid arg wppa_get_thumb_path('.$id.')', 'red');
	if ( $system == 'tree' ) return WPPA_UPLOAD_PATH.'/thumbs/'.wppa_expand_id($thumb['id'], true).'.'.$thumb['ext'];
	else return WPPA_UPLOAD_PATH.'/thumbs/'.$thumb['id'].'.'.$thumb['ext'];
}

// get url of a full sized image
function wppa_get_photo_url( $id, $system = 'flat', $x = '0', $y = '0' ) {
global $blog_id;
global $wppa_supported_stereo_types;

	$thumb = wppa_cache_thumb( $id );

	if ( is_feed() && wppa_switch('wppa_feed_use_thumb') ) return wppa_get_thumb_url($id, $system);

	// If in the cloud...
	$is_old = wppa_too_old_for_cloud( $id );
	if ( wppa_cdn( 'front' ) && ! wppa_is_multi( $id ) && ! $is_old && ! wppa_is_stereo( $id ) ) {
		switch ( wppa_cdn( 'front' ) ) {
			case 'cloudinary':
				$x = round($x);
				$y = round($y);
				$prefix 	= ( is_multisite() && ! WPPA_MULTISITE_GLOBAL ) ? $blog_id.'-' : '';
				$t 			= wppa_switch('wppa_enlarge') ? 'fit' : 'limit';
				$q 			= wppa_opt( 'jpeg_quality' );
				$sizespec 	= ( $x && $y ) ? 'w_'.$x.',h_'.$y.',c_'.$t.',q_'.$q.'/' : '';
				$s = is_ssl() ? 's' : '';
				$url = 'http'.$s.'://res.cloudinary.com/'.get_option('wppa_cdn_cloud_name').'/image/upload/'.$sizespec.$prefix.$thumb['id'].'.'.$thumb['ext'];
				return $url;
				break;

		}
	}

	// Stereo?
	if ( wppa_is_stereo( $id ) ) {

		// Get type from cookie
		$st = isset( $_COOKIE["stereotype"] ) ? $_COOKIE["stereotype"] : 'color';
		if ( ! in_array( $st, $wppa_supported_stereo_types ) ) {
			$st = '_flat';
		}

		// Get glass from cookie
		$sg = 'rc';
		if ( isset( $_COOKIE["stereoglass"] ) && $_COOKIE["stereoglass"] == 'greenmagenta' ) {
			$sg = 'gm';
		}

		// Create the file if not present
		if ( ! is_file( wppa_get_stereo_path( $id, $st, $sg ) ) ) {
			wppa_create_stereo_image( $id, $st, $sg );
		}

		// Build the url
		if ( $st == '_flat' ) {
			$url = WPPA_UPLOAD_URL.'/stereo/'.$id.'-'.$st.'.jpg'.'?ver='.get_option('wppa_photo_version', '1');
		}
		else {
			$url = WPPA_UPLOAD_URL.'/stereo/'.$id.'-'.$st.'-'.$sg.'.jpg'.'?ver='.get_option('wppa_photo_version', '1');
		}

		// Done
		return $url;
	}

	if ( get_option('wppa_file_system') == 'flat' ) $system = 'flat';	// Have been converted, ignore argument
	if ( get_option('wppa_file_system') == 'tree' ) $system = 'tree';	// Have been converted, ignore argument
	if ( ! is_numeric($id) || $id < '1' ) wppa_dbg_msg('Invalid arg wppa_get_photo_url('.$id.')', 'red');

	if ( $system == 'tree' ) return WPPA_UPLOAD_URL.'/'.wppa_expand_id($thumb['id']).'.'.$thumb['ext'].'?ver='.get_option('wppa_photo_version', '1');
	else return WPPA_UPLOAD_URL.'/'.$thumb['id'].'.'.$thumb['ext'].'?ver='.get_option('wppa_photo_version', '1');
}

// Bump Fullsize photo version number
function wppa_bump_photo_rev() {
	wppa_update_option('wppa_photo_version', get_option('wppa_photo_version', '1') + '1');
}

// get path of a full sized image
function wppa_get_photo_path( $id, $system = 'flat' ) {

	$thumb = wppa_cache_thumb( $id );

	if ( get_option( 'wppa_file_system' ) == 'flat' ) $system = 'flat';	// Have been converted, ignore argument
	if ( get_option( 'wppa_file_system' ) == 'tree' ) $system = 'tree';	// Have been converted, ignore argument
	if ( ! is_numeric( $id ) || $id < '1' ) wppa_dbg_msg( 'Invalid arg wppa_get_photo_path(' . $id . ')', 'red' );

	if ( $system == 'tree' ) return WPPA_UPLOAD_PATH . '/' . wppa_expand_id( $thumb['id'], true ) . '.' . $thumb['ext'];
	else return WPPA_UPLOAD_PATH . '/' . $thumb['id'] . '.' . $thumb['ext'];
}

// Expand id to subdir chain for new file structure
function wppa_expand_id( $xid, $makepath = false ) {

	$result = '';
	$id = $xid;
	$len = strlen( $id );
	while ( $len > '2' ) {
		$result .= substr( $id, '0', '2' ) . '/';
		$id = substr( $id, '2' );
		$len = strlen( $id );
		if ( $makepath ) {
			$path = WPPA_UPLOAD_PATH . '/' . $result;
			if ( ! is_dir( $path ) ) wppa_mktree( $path );
			$path = WPPA_UPLOAD_PATH . '/thumbs/' . $result;
			if ( ! is_dir( $path ) ) wppa_mktree( $path );
		}
	}
	$result .= $id;
	return $result;
}

// Makes the html for the geo support for current theme and adds it to $wppa['geo']
function wppa_do_geo( $id, $location ) {
global $wppa;

	$temp 	= explode( '/', $location );
	$lat 	= $temp['2'];
	$lon 	= $temp['3'];

	$type 	= wppa_opt( 'gpx_implementation' );

	// Switch on implementation type
	switch ( $type ) {
		case 'google-maps-gpx-viewer':
			$geo = str_replace( 'w#lon', $lon, str_replace( 'w#lat', $lat, wppa_opt( 'gpx_shortcode' ) ) );
			$geo = str_replace( 'w#ip', $_SERVER['REMOTE_ADDR'], $geo );
			$geo = do_shortcode( $geo );
			$wppa['geo'] .= '<div id="geodiv-' . wppa( 'mocc' ) . '-' . $id . '" style="display:none;">' . $geo . '</div>';
			break;
		case 'wppa-plus-embedded':
			if ( $wppa['geo'] == '' ) { 	// First
				$wppa['geo'] = '
<div id="map-canvas-' . wppa( 'mocc' ).'" style="height:' . wppa_opt( 'map_height' ) . 'px; width:100%; padding:0; margin:0; font-size: 10px;" ></div>
<script type="text/javascript" >
	if ( typeof ( _wppaLat ) == "undefined" ) { var _wppaLat = new Array();	var _wppaLon = new Array(); }
	_wppaLat[' . wppa( 'mocc' ) . '] = new Array(); _wppaLon[' . wppa( 'mocc' ) . '] = new Array();
</script>';
			}	// End first
			$wppa['geo'] .= '
<script type="text/javascript">_wppaLat[' . wppa( 'mocc' ) . '][' . $id . '] = ' . $lat.'; _wppaLon[' . wppa( 'mocc' ) . '][' . $id.'] = ' . $lon . ';</script>';
			break;	// End native
	}
}

// See if an album is in a separate tree
function wppa_is_separate( $id ) {

	if ( $id == '' ) return false;
	if ( ! wppa_is_int( $id ) ) return false;
	if ( $id == '-1' ) return true;
	if ( $id < '1' ) return false;
	$alb = wppa_get_parentalbumid( $id );

	return wppa_is_separate( $alb );
}

// Get the albums parent
function wppa_get_parentalbumid($id) {
static $prev_album_id;

	if ( ! wppa_is_int($id) || $id < '1' ) return '0';

	$album = wppa_cache_album($id);
	if ( $album === false ) {
		wppa_log( 'error', 'Album '.$id.' no longer exists, but is still set as a parent of '.$prev_album_id.'. Please correct this.' );
		return '-9';	// Album does not exist
	}
	$prev_album_id = $id;
	return $album['a_parent'];
}

function wppa_html($str) {
// It is assumed that the raw data contains html.
// If html not allowed, filter specialchars
// To prevent duplicate filtering, first entity_decode
	$result = html_entity_decode($str);
	if ( ! wppa_switch('wppa_html') && ! current_user_can('wppa_moderate') ) {
		$result = htmlspecialchars($str);
	}
	return $result;
}


// get a photos album id
function wppa_get_album_id_by_photo_id( $id ) {

	if ( ! is_numeric($id) || $id < '1' ) wppa_dbg_msg('Invalid arg wppa_get_album_id_by_photo_id('.$id.')', 'red');
	$thumb = wppa_cache_thumb($id);
	return $thumb['album'];
}

function wppa_get_rating_count_by_id($id) {

	if ( ! is_numeric($id) || $id < '1' ) wppa_dbg_msg('Invalid arg wppa_get_rating_count_by_id('.$id.')', 'red');
	$thumb = wppa_cache_thumb($id);
	return $thumb['rating_count'];
}

function wppa_get_rating_by_id($id, $opt = '') {
global $wpdb;

	if ( ! is_numeric($id) || $id < '1' ) wppa_dbg_msg('Invalid arg wppa_get_rating_by_id('.$id.', '.$opt.')', 'red');
	$thumb = wppa_cache_thumb( $id );
	$rating = $thumb['mean_rating'];
	if ( $rating ) {
		$i = wppa_opt( 'rating_prec' );
		$j = $i + '1';
		$val = sprintf('%'.$j.'.'.$i.'f', $rating);
		if ($opt == 'nolabel') $result = $val;
		else $result = sprintf(__('Rating: %s', 'wp-photo-album-plus'), $val);
	}
	else $result = '';
	return $result;
}

function wppa_switch( $xkey ) {
global $wppa_opt;

	if ( substr( $xkey, 0, 5 ) == 'wppa_' ) {
		$key = $xkey;
	}
	else {
		$key = 'wppa_' . $xkey;
	}

	if ( is_array( $wppa_opt ) ) {
		if ( isset( $wppa_opt[$key] ) ) {
			if ( $wppa_opt[$key] == 'yes' ) return true;
			elseif ( $wppa_opt[$key] == 'no' ) return false;
			else wppa_log( 'Error', '$wppa_opt['.$key.'] is not a yes/no setting', true );
		}
		else wppa_log( 'Error', '$wppa_opt['.$key.'] is not a setting', true );
	}
	else wppa_log( 'Error', '$wppa_opt[] is not initialized while testing '.$key, true );

	return false;
}

function wppa_opt( $xkey ) {
global $wppa_opt;

	if ( substr( $xkey, 0, 5 ) == 'wppa_' ) {
		$key = $xkey;
	}
	else {
		$key = 'wppa_' . $xkey;
	}

	if ( empty( $wppa_opt ) ) {
		return trim( get_option( $key ) );
	}

	if ( is_array( $wppa_opt ) ) {
		if ( isset( $wppa_opt[$key] ) ) {
			if ( $wppa_opt[$key] == 'yes' || $wppa_opt[$key] == 'no' ) {
				wppa_log( 'Error', '$wppa_opt['.$key.'] is a yes/no setting, not a value', true );
				return false;
			}
		}
		else {
			wppa_log( 'Error', '$wppa_opt['.$key.'] is not a setting', true );
			return false;
		}
	}
	else {
		wppa_log( 'Error', '$wppa_opt[] is not initialized while testing '.$key, true );
		return false;
	}

	return trim( $wppa_opt[$key] );
}

// Getter / setter of runtime parameter
function wppa( $key, $newval = 'nil' ) {
global $wppa;

	// Array defined?
	if ( is_array( $wppa ) ) {

		// Valid key?
		if ( isset( $wppa[$key] ) ) {

			// Get old value
			$oldval = $wppa[$key];

			// New value supplied?
			if ( $newval !== 'nil' ) {
				$wppa[$key] = $newval;
			}
		}

		// Invalid key
		else {
			wppa_log( 'Obs', '$wppa[\''.$key.'\'] is not defined in reset_occurrance' );
			if ( $newval != 'nil' ) {
				$wppa[$key] = $newval;
			}
			return false;
		}
	}

	// Array not defined
	else {
		wppa_log( 'Error', '$wppa[] is not initialized while testing \''.$key.'\'' );
		return false;
	}

	return $oldval;
}

// Add value to runtime parameter
function wppa_add( $key, $newval ) {
global $wppa;

	// Array defined?
	if ( is_array( $wppa ) ) {

		// Valid key?
		if ( isset( $wppa[$key] ) ) {

			// Add new value
			$wppa[$key] .= $newval;
		}

		// Invalid key
		else {
			wppa_log( 'Error', '$wppa[\''.$key.'\'] is not defined' );
			return false;
		}
	}

	// Array not defined
	else {
		wppa_log( 'Error', '$wppa[] is not initialized while testing \''.$key.'\'' );
		return false;
	}
}

function wppa_display_root( $id ) {
	$all = __('All albums', 'wp-photo-album-plus' );
	if ( ! $id ) return $all;
	$album = wppa_cache_album( $id );
	if ( ! $album ) return '';
	$albums = array();
	$albums[] = $album;
	$albums = wppa_add_paths( $albums );
	return $albums[0]['name'];
}

function wppa_add_paths( $albums ) {

	if ( is_array( $albums ) ) foreach ( array_keys( $albums ) as $index ) {
		$tempid = $albums[$index]['id'];
		$albums[$index]['name'] = __(stripslashes($albums[$index]['name']), 'wp-photo-album-plus');	// Translate name
		while ( $tempid > '0' ) {
			$tempid = wppa_get_parentalbumid($tempid);
			if ( $tempid > '0' ) {
				$albums[$index]['name'] = wppa_get_album_name($tempid).' > '.$albums[$index]['name'];
			}
			elseif ( $tempid == '-1' ) $albums[$index]['name'] = '-s- '.$albums[$index]['name'];
		}
	}
	return $albums;
}

function wppa_add_parents($pages) {
global $wpdb;
static $parents;
static $titles;

	// Pre-fill $parents
	if ( empty( $parents ) ) {
		$temp = $wpdb->get_results( "SELECT `ID`, `post_parent` FROM `" . $wpdb->posts . "`", ARRAY_A );
		if ( ! empty( $temp ) ) {
			foreach( $temp as $item ) {
				$parents[$item['ID']] = $item['post_parent'];
			}
		}
	}

	if ( is_array($pages) ) foreach ( array_keys($pages) as $index ) {
		$tempid = $pages[$index]['ID'];
		$pages[$index]['post_title'] = __(stripslashes($pages[$index]['post_title']));
		while ( $tempid > '0') {
			if ( isset( $parents[$tempid] ) ) {
				$tempid = $parents[$tempid];
			}
			else {
				$t = $wpdb->get_var($wpdb->prepare("SELECT `post_parent` FROM `" . $wpdb->posts . "` WHERE `ID` = %s", $tempid));
				$parents[$tempid] = $t;
				$tempid = $t;
			}
			if ( $tempid > '0' ) {
				if ( ! isset( $titles[$tempid] ) ) {
					$titles[$tempid] = __(stripslashes($wpdb->get_var($wpdb->prepare("SELECT `post_title` FROM `" . $wpdb->posts . "` WHERE `ID` = %s", $tempid))));
				}
				$pages[$index]['post_title'] = $titles[$tempid].' > '.$pages[$index]['post_title'];
			}
			else $tempid = '0';
		}
	}
	return $pages;
}

// Sort an array on a column, keeping the indexes
function wppa_array_sort($array, $on, $order=SORT_ASC) {

    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

function wppa_get_taglist() {

	$result = WPPA_MULTISITE_GLOBAL ? get_site_option( 'wppa_taglist', 'nil' ) : get_option( 'wppa_taglist', 'nil' );
	if ( $result == 'nil' ) {
		$result = wppa_create_taglist();
	}
	else {
		if ( is_array($result) ) foreach ( array_keys($result) as $tag ) {
			$result[$tag]['ids'] = wppa_index_string_to_array($result[$tag]['ids']);
		}
	}
	return $result;
}

function wppa_clear_taglist() {

	$result = WPPA_MULTISITE_GLOBAL ? update_site_option( 'wppa_taglist', 'nil' ) : update_option( 'wppa_taglist', 'nil' );
	$result = WPPA_MULTISITE_GLOBAL ? get_site_option( 'wppa_taglist', 'nil' ) : get_option( 'wppa_taglist', 'nil' );
	if ( $result != 'nil' ) {
		wppa_log( 'Warning', 'Could not clear taglist' ) ;
	}
}

function wppa_create_taglist() {
global $wpdb;

	// Initialize
	$result 	= false;
	$total 		= '0';
	$done 		= false;
	$skip 		= '0';
	$pagsize 	= '10000';

	// To avoid out of memory, we do all the photos in chunks of $pagsize
	while ( ! $done ) {

		// Get the chunk
		$photos = $wpdb->get_results( 	"SELECT `id`, `tags` " .
										"FROM `" . WPPA_PHOTOS . "` " .
										"WHERE `status` <> 'pending' " .
											"AND `status` <> 'scheduled' " .
											"AND `tags` <> '' " .
											"LIMIT " . $skip . "," . $pagsize,
										ARRAY_A );

		// If photos found, process the tags, if any
		if ( $photos ) foreach ( $photos as $photo ) {
			$tags = explode( ',', $photo['tags'] );

			// Tags found?
			if ( $tags ) foreach ( $tags as $tag ) {
				if ( $tag ) {
					if ( ! isset( $result[$tag] ) ) {	// A new tag
						$result[$tag]['tag'] = $tag;
						$result[$tag]['count'] = '1';
						$result[$tag]['ids'][] = $photo['id'];
					}
					else {								// An existing tag
						$result[$tag]['count']++;
						$result[$tag]['ids'][] = $photo['id'];
					}
				}
				$total++;
			}
		}

		// If no more photos, we are done
		else {
			$done = true;
		}
		$skip += $pagsize;
	}

	// If any tags found, calculate fractions
	$tosave = array();
	if ( is_array( $result ) ) {
		foreach ( array_keys( $result ) as $key ) {
			$result[$key]['fraction'] = sprintf( '%4.2f', $result[$key]['count'] / $total );
		}
		$result = wppa_array_sort( $result, 'tag' );
		$tosave = $result;

		// Convert the arrays to compressed enumerations
		foreach ( array_keys( $tosave ) as $key ) {
			$tosave[$key]['ids'] = wppa_index_array_to_string( $tosave[$key]['ids'] );
		}
	}

	// Save the new taglist
	$bret = WPPA_MULTISITE_GLOBAL ? update_site_option( 'wppa_taglist', $tosave ) : update_option( 'wppa_taglist', $tosave );
	if ( ! $bret ) {
		wppa_log( 'Err', 'Unable to save taglist' );
	}

	// And return the result
	return $result;
}

function wppa_get_catlist() {

	$result = WPPA_MULTISITE_GLOBAL ? get_site_option( 'wppa_catlist', 'nil' ) : get_option( 'wppa_catlist', 'nil' );
	if ( $result == 'nil' ) {
		$result = wppa_create_catlist();
	}
	else {
		foreach ( array_keys($result) as $cat ) {
			$result[$cat]['ids'] = wppa_index_string_to_array($result[$cat]['ids']);
		}
	}
	return $result;
}

function wppa_clear_catlist() {

	$result = WPPA_MULTISITE_GLOBAL ? update_site_option( 'wppa_catlist', 'nil' ) : update_option( 'wppa_catlist', 'nil' );
	$result = WPPA_MULTISITE_GLOBAL ? get_site_option( 'wppa_catlist', 'nil' ) : get_option( 'wppa_catlist', 'nil' );
	if ( $result != 'nil' ) {
		wppa_log( 'Warning', 'Could not clear catlist' ) ;
	}
}

function wppa_create_catlist() {
global $wpdb;

	$result = false;
	$total = '0';
	$albums = $wpdb->get_results("SELECT `id`, `cats` FROM `".WPPA_ALBUMS."` WHERE `cats` <> ''", ARRAY_A);
	if ( $albums ) foreach ( $albums as $album ) {
		$cats = explode(',', $album['cats']);
		if ( $cats ) foreach ( $cats as $cat ) {
			if ( $cat ) {
				if ( ! isset($result[$cat]) ) {	// A new cat
					$result[$cat]['cat'] = $cat;
					$result[$cat]['count'] = '1';
					$result[$cat]['ids'][] = $album['id'];
				}
				else {							// An existing cat
					$result[$cat]['count']++;
					$result[$cat]['ids'][] = $album['id'];
				}
			}
			$total++;
		}
	}
	$tosave = array();
	if ( is_array($result) ) {
		foreach ( array_keys($result) as $key ) {
			$result[$key]['fraction'] = sprintf('%4.2f', $result[$key]['count'] / $total);
		}
		$result = wppa_array_sort($result, 'cat');
		$tosave = $result;
		foreach ( array_keys($tosave) as $key ) {
			$tosave[$key]['ids'] = wppa_index_array_to_string($tosave[$key]['ids']);
		}
	}
	$bret = WPPA_MULTISITE_GLOBAL ? update_site_option( 'wppa_catlist', $tosave ) : update_option( 'wppa_catlist', $tosave );
	if ( ! $bret ) {
		wppa_log( 'Err', 'Unable to save catlist' );
	}
	return $result;
}

function wppa_update_option( $option, $value ) {
global $wppa_opt;

	// Update the option
	update_option($option, $value);

	// Update the local cache
	$wppa_opt[$option] = $value;

	// Delete the cached options
	delete_option('wppa_cached_options');

	// See if wppa-init.[lang].js needs remake
	$init_js_critical = array( 	'wppa_slideshow_linktype',
								'wppa_fullimage_border_width',
								'wppa_bgcolor_img',
								'wppa_thumb_linktype',
								'wppa_animation_type',
								'wppa_animation_speed',
								'wppa_bwidth',
								'wppa_smallsize',
								'wppa_colwidth',
								'wppa_slideshow_timeout',
								'wppa_slide_wrap',
								'wppa_thumbsize',
								'wppa_fullsize',
								'wppa_film_show_glue',
								'wppa_dislike_show_count',
								'wppa_mini_treshold',
								'wppa_rating_change',
								'wppa_rating_multi',
								'wppa_hide_when_empty',
								'wppa_bgcolor_numbar',
								'wppa_bcolor_numbar',
								'wppa_bgcolor_numbar_active',
								'wppa_bcolor_numbar_active',
								'wppa_fontfamily_numbar',
								'wppa_fontsize_numbar',
								'wppa_fontcolor_numbar',
								'wppa_fontweight_numbar',
								'wppa_fontfamily_numbar_active',
								'wppa_fontsize_numbar_active',
								'wppa_fontcolor_numbar_active',
								'wppa_fontweight_numbar_active',
								'wppa_numbar_max',
								'wppa_ajax_non_admin',
								'wppa_next_on_callback',
								'wppa_star_opacity',
								'wppa_comment_email_required',
								'wppa_allow_ajax',
								'wppa_use_photo_names_in_urls',
								'wppa_thumb_blank',
								'wppa_rating_max',
								'wppa_rating_display_type',
								'wppa_rating_prec',
								'wppa_enlarge',
								'wppa_tn_margin',
								'wppa_thumb_auto',
								'wppa_magnifier',
								'wppa_art_monkey_link',
								'wppa_auto_open_comments',
								'wppa_update_addressline',
								'wppa_film_linktype',
								'wppa_vote_button_text',
								'wppa_voted_button_text',
								'wppa_slide_swipe',
								'wppa_max_cover_width',
								'wppa_slideshow_linktype',
								'wppa_slideshow_linktype',
								'wppa_comten_alt_thumbsize',
								'wppa_track_viewcounts',
								'wppa_share_hide_when_running',
								'wppa_fotomoto_on',
								'wppa_art_monkey_display',
								'wppa_fotomoto_hide_when_running',
								'wppa_vote_needs_comment',
								'wppa_fotomoto_min_width',
								'wppa_use_short_qargs',
								'wppa_lb_hres',
								'wppa_start_slide_video',
								'wppa_start_slide_audio',
								'wppa_ovl_show_startstop',
								'wppa_ovl_show_legenda',
								'wppa_lightbox_name',
								'wppa_start_symbol_url',
								'wppa_pause_symbol_url',
								'wppa_stop_symbol_url',
								'wppa_start_pause_symbol_size',
								'wppa_start_pause_symbol_bradius',
								'wppa_stop_symbol_size',
								'wppa_stop_symbol_bradius',
								'wppa_left_symbol_url',
								'wppa_right_symbol_url',
								'wppa_left_right_symbol_size',
								'wppa_left_right_symbol_bradius',
								'wppa_ovl_border_width',
								'wppa_ovl_border_radius',
								'wppa_ovl_anim',
								'wppa_ovl_slide',
								'wppa_relative_urls',


		);
	if ( in_array( $option, $init_js_critical ) ) {
		$files = glob( WPPA_PATH.'/wppa-init.*.js' );
		if ( $files ) {
			foreach ( $files as $file ) {
				@ unlink ( $file );		// Will be auto re-created
			}
		}
	}

	// See if wppa-dynamic.css needs remake
	$dynamic_css_critical = array(	'wppa_inline_css',
									'wppa_custom_style',
									'wppa_bwidth',
									'wppa_bradius',
									'wppa_box_spacing',
									'wppa_fontcolor_box',
									'wppa_fontfamily_box',
									'wppa_fontsize_box',
									'wppa_fontweight_box',
									'wppa_fontfamily_thumb',
									'wppa_fontsize_thumb',
									'wppa_fontcolor_thumb',
									'wppa_fontweight_thumb',
									'wppa_bgcolor_com',
									'wppa_bcolor_com',
									'wppa_bgcolor_iptc',
									'wppa_bcolor_iptc',
									'wppa_bgcolor_exif',
									'wppa_bcolor_exif',
									'wppa_bgcolor_share',
									'wppa_bcolor_share',
									'wppa_bgcolor_namedesc',
									'wppa_bcolor_namedesc',
									'wppa_bgcolor_nav',
									'wppa_bcolor_nav',
									'wppa_fontfamily_nav',
									'wppa_fontsize_nav',
									'wppa_fontcolor_nav',
									'wppa_fontweight_nav',
									'wppa_bgcolor_even',
									'wppa_bcolor_even',
									'wppa_bgcolor_alt',
									'wppa_bcolor_alt',
									'wppa_bgcolor_img',
									'wppa_fontfamily_title',
									'wppa_fontsize_title',
									'wppa_fontcolor_title',
									'wppa_fontweight_title',
									'wppa_fontfamily_fulldesc',
									'wppa_fontsize_fulldesc',
									'wppa_fontcolor_fulldesc',
									'wppa_fontweight_fulldesc',
									'wppa_fontfamily_fulltitle',
									'wppa_fontsize_fulltitle',
									'wppa_fontcolor_fulltitle',
									'wppa_fontweight_fulltitle',
									'wppa_bgcolor_cus',
									'wppa_bcolor_cus',
									'wppa_bgcolor_upload',
									'wppa_bcolor_upload',
									'wppa_bgcolor_multitag',
									'wppa_bcolor_multitag',
									'wppa_bgcolor_bestof',
									'wppa_bcolor_bestof',
									'wppa_bgcolor_stereo',
									'wppa_bcolor_stereo',
									'wppa_bgcolor_tagcloud',
									'wppa_bcolor_tagcloud',
									'wppa_bgcolor_superview',
									'wppa_bcolor_superview',
									'wppa_bgcolor_search',
									'wppa_bcolor_search',
									'wppa_arrow_color',
									'wppa_cover_minheight',
									'wppa_head_and_text_frame_height',
									'wppa_ovl_fs_icons',
		);
	if ( in_array( $option, $dynamic_css_critical ) ) {
		@ unlink ( WPPA_PATH.'/wppa-dynamic.css' );		// Will be auto re-created
	}
}

function wppa_album_exists($id) {
global $wpdb;
	return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `".WPPA_ALBUMS."` WHERE `id` = %s", $id));
}

function wppa_photo_exists($id) {
global $wpdb;
	return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `".WPPA_PHOTOS."` WHERE `id` = %s", $id));
}

function wppa_albumphoto_exists($alb, $photo) {
global $wpdb;
	return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `".WPPA_PHOTOS."` WHERE `album` = %s AND `filename` = %s", $alb, $photo));
}

function wppa_dislike_check($photo) {
global $wpdb;

	$count = $wpdb->get_var($wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_RATING."` WHERE `photo` = %s AND `value` = -1", $photo ));

	if ( wppa_opt( 'dislike_mail_every' ) > '0') {		// Feature enabled?
		if ( $count % wppa_opt( 'dislike_mail_every' ) == '0' ) {	// Mail the admin
			$to        = get_bloginfo('admin_email');
			$subj 	   = __('Notification of inappropriate image', 'wp-photo-album-plus');
			$cont['0'] = sprintf(__('Photo %s has been marked as inappropriate by %s different visitors.', 'wp-photo-album-plus'), $photo, $count);
			$cont['1'] = '<a href="'.get_admin_url().'admin.php?page=wppa_admin_menu&tab=pmod&photo='.$photo.'" >'.__('Manage photo', 'wp-photo-album-plus').'</a>';
			wppa_send_mail($to, $subj, $cont, $photo);
		}
	}

	if ( wppa_opt( 'dislike_set_pending' ) > '0') {		// Feature enabled?
		if ( $count == wppa_opt( 'dislike_set_pending' ) ) {
			$wpdb->query($wpdb->prepare( "UPDATE `".WPPA_PHOTOS."` SET `status` = 'pending' WHERE `id` = %s", $photo ));
			$to        = get_bloginfo('admin_email');
			$subj 	   = __('Notification of inappropriate image', 'wp-photo-album-plus');
			$cont['0'] = sprintf(__('Photo %s has been marked as inappropriate by %s different visitors.', 'wp-photo-album-plus'), $photo, $count);
			$cont['0'] .= "\n".__('The status has been changed to \'pending\'.', 'wp-photo-album-plus');
			$cont['1'] = '<a href="'.get_admin_url().'admin.php?page=wppa_admin_menu&tab=pmod&photo='.$photo.'" >'.__('Manage photo', 'wp-photo-album-plus').'</a>';
			wppa_send_mail($to, $subj, $cont, $photo);
		}
	}

	if ( wppa_opt( 'dislike_delete' ) > '0') {			// Feature enabled?
		if ( $count == wppa_opt( 'dislike_delete' ) ) {
			$to        = get_bloginfo('admin_email');
			$subj 	   = __('Notification of inappropriate image', 'wp-photo-album-plus');
			$cont['0'] = sprintf(__('Photo %s has been marked as inappropriate by %s different visitors.', 'wp-photo-album-plus'), $photo, $count);
			$cont['0'] .= "\n".__('It has been deleted.', 'wp-photo-album-plus');
			$cont['1'] = '';//<a href="'.get_admin_url().'admin.php?page=wppa_admin_menu&tab=pmod&photo='.$photo.'" >'.__('Manage photo').'</a>';
			wppa_send_mail($to, $subj, $cont, $photo);
			wppa_delete_photo($photo);
		}
	}
}



function wppa_dislike_get($photo) {
global $wpdb;

	$count = $wpdb->get_var($wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_RATING."` WHERE `photo` = %s AND `value` = -1", $photo ));
	return $count;
}

function wppa_send_mail( $to, $subj, $cont, $photo, $email = '' ) {

	$message_part_1 = '';
	$message_part_2 = '';
	$message_part_3 = '';

	$site = home_url();
	$site = str_replace( 'https://www.', '', $site );
	$site = str_replace( 'http://www.', '', $site );
	$site = str_replace( 'https://', '', $site );
	$site = str_replace( 'http://', '', $site );
	$spos = strpos( $site, '/' );
	if ( $spos  > '2' ) {
		$site = substr( $site, 0, $spos );
	}

	$headers 	= array( 	'From: noreply@' . $site,
							'Content-Type: text/html'
						);

	$message_part_1	.=
'<html>' .
	'<head>' .
		'<title>'.$subj.'</title>' .
	'</head>' .
	'<body>' .
		'<h3>'.$subj.'</h3>' .
		'<p><img src="'.wppa_get_thumb_url($photo).'" '.wppa_get_imgalt($photo).'/></p>';
		if ( is_array($cont) ) {
			foreach ( $cont as $c ) if ( $c ) {
				$message_part_1 .= '<p>'.$c.'</p>';
			}
		}
		else {
			$message_part_1 .= '<p>'.$cont.'</p>';
		}

		if ( $email != 'void' ) {
			if ( is_user_logged_in() ) {
				global $current_user;
				$current_user = wp_get_current_user();
				$e = $current_user->user_email;
				$eml = sprintf(__('The visitors email address is: <a href="mailto:%s">%s</a>', 'wp-photo-album-plus'), $e, $e);
				$message_part_2 .= '<p>'.$eml.'</p>';
			}
			elseif ( $email ) {
				$e = $email;
				$eml = sprintf(__('The visitor says his email address is: <a href="mailto:%s">%s</a>', 'wp-photo-album-plus'), $e, $e);
				$message_part_2 .= '<p>'.$eml.'</p>';
			}
		}

		$message_part_3 .=
		'<p>' .
			'<small>' .
				sprintf(__('This message is automaticly generated at %s. It is useless to respond to it.', 'wp-photo-album-plus'), '<a href="'.home_url().'" >'.home_url().'</a>') .
			'</small>' .
		'</p>' .
	'</body>' .
'</html>';

	$subject = '['.str_replace('&#039;', '', get_bloginfo('name') ).'] '.$subj;

	// Try to send it with extra headers and with html
	$iret = wp_mail( 	$to,
						$subject,
						$message_part_1 . $message_part_2 . $message_part_3,
						$headers,
						'' );
	if ( $iret ) return;

	// Failed
	echo 'Mail sending Failed';

}

function wppa_get_imgalt( $id ) {

	$thumb = wppa_cache_thumb($id);
	switch ( wppa_opt( 'alt_type' ) ) {
		case 'fullname':
			$result = esc_attr( wppa_get_photo_name( $id ) );
			break;
		case 'namenoext':
			$temp = wppa_get_photo_name( $id );
			$temp = preg_replace( '/\.[^.]*$/', '', $temp );	// Remove file extension
			$result = esc_attr( $temp );
			break;
		case 'custom':
			$result = esc_attr( $thumb['alt'] );
			break;
		default:
			$result = $id;
			break;
	}
	if ( ! $result ) $result = '0';
	return ' alt="'.$result.'" ';
}

// Flush treecounts of album $alb, default: clear all
function wppa_flush_treecounts( $alb = '' ) {
global $wppa;

	if ( $alb ) {
		$wppa['counts'] 	= WPPA_MULTISITE_GLOBAL ? get_site_option( 'wppa_counts', array() ) : get_option( 'wppa_counts', array() );
		$wppa['treecounts'] = WPPA_MULTISITE_GLOBAL ? get_site_option( 'wppa_counts_tree', array() ) : get_option( 'wppa_counts_tree', array() );
		if ( isset($wppa['counts'][$alb]) ) {
			unset($wppa['counts'][$alb]);
			$iret = WPPA_MULTISITE_GLOBAL ? update_site_option('wppa_counts', $wppa['counts']) : update_option('wppa_counts', $wppa['counts']);
		}
		if ( isset($wppa['treecounts'][$alb]) ) {
			unset($wppa['treecounts'][$alb]);
			$uret = WPPA_MULTISITE_GLOBAL ? update_site_option('wppa_counts_tree', $wppa['treecounts']) : update_option('wppa_counts_tree', $wppa['treecounts']);
		}
		$parent = wppa_get_parentalbumid($alb);
		if ( $parent > '0' ) wppa_flush_treecounts($parent);
	}
	else {
		$bret = WPPA_MULTISITE_GLOBAL ? delete_site_option( 'wppa_counts' ) : delete_option( 'wppa_counts' );
		$bret = WPPA_MULTISITE_GLOBAL ? delete_site_option( 'wppa_counts_tree' ) : delete_option( 'wppa_counts_tree' );
	}
}

// Verify correctness of treecount value
function wppa_verify_treecounts( $alb, $key, $count ) {

	$treecounts = wppa_treecount_a( $alb );
	$need_a = false;
	$need_p = false;

	// Number of albums ( $count ) equal to subalbums ( 'selfalbums' ) ?
	if ( 'albums' == $key ) {
		if ( $treecounts['selfalbums'] != $count ) {	// Faulty data
			$need_a = true;
		}
	}

	// Number of photos ( $count ) equal to photos in this album ( 'selfphotos' ( + opts ) )?
	if ( 'photos' == $key ) {
		if ( current_user_can( 'wppa_moderate' ) ) {
			if ( ( $treecounts['selfphotos'] + $treecounts['pendphotos'] + $treecounts['scheduledphotos'] ) != $count ) {	// Faulty data
				$need_p = true;
			}
		}
		else {
			if ( $treecounts['selfphotos'] != $count ) {	// Faulty data
				$need_p = true;
			}
		}
	}

	// If no sub-albums, total number of photos should be equal to photos in this album ( 'selfphotos' )
	if ( ! $treecounts['selfalbums'] && $treecounts['photos'] != $treecounts['selfphotos'] ) {
		$need_p = true;
	}

	// Need recalc for reason albums fault?
	if ( $need_a ) {
		wppa_flush_treecounts( $alb );
		wppa_log( 'Fix', 'Treecounts albums for album #'.$alb.' ('.wppa_get_album_name( $alb ).')' );
	}

	// Need recalc for reason photos fault?
	if ( $need_p ) {
		wppa_flush_treecounts( $albumid );
		wppa_log( 'Fix', 'Treecounts photos for album #'.$alb.' ('.wppa_get_album_name( $alb ).')' );
	}
}

// Get the treecounts for album $alb
function wppa_treecount_a( $alb ) {
global $wpdb;
global $wppa;

	$albums = '0';
	$photos = '1';
	$selfalbums = '3';
	$selfphotos = '4';
	$pendphotos = '5';
	$scheduledphotos = '6';

	// Initial fetch
	if ( ! isset($wppa['counts']) ) {
		$wppa['counts'] = WPPA_MULTISITE_GLOBAL ? get_site_option( 'wppa_counts', array() ) : get_option( 'wppa_counts', array() );
	}
	if ( ! isset($wppa['treecounts']) ) {
		$wppa['treecounts'] = WPPA_MULTISITE_GLOBAL ? get_site_option( 'wppa_counts_tree', array() ) : get_option( 'wppa_counts_tree', array() );
	}

	// See if we have this in cache
	if ( isset( $wppa['counts'][$alb] ) && isset( $wppa['treecounts'][$alb] ) ) {	// Album found
		$result['albums'] = $wppa['treecounts'][$alb][$albums];			// Use data
		$result['photos'] = $wppa['treecounts'][$alb][$photos];
		$result['selfalbums'] = $wppa['counts'][$alb][$selfalbums];
		$result['selfphotos'] = $wppa['counts'][$alb][$selfphotos];
		$result['pendphotos'] = $wppa['counts'][$alb][$pendphotos];
		$result['scheduledphotos'] = $wppa['counts'][$alb][$scheduledphotos];

		return $result;													// And return
	}

	// Not in cache
	else {
		$albs	 	 = $wpdb->get_results( $wpdb->prepare( "SELECT `id` FROM `".WPPA_ALBUMS."` WHERE `a_parent` = %s", $alb ), ARRAY_A );
		$album_count = empty($albs) ? '0' : count($albs);
		$photo_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_PHOTOS."` WHERE `album` = %s AND `status` <> 'pending' AND `status` <> 'scheduled'", $alb ) );
		$pend_count  = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_PHOTOS."` WHERE `album` = %s AND `status` = 'pending'", $alb ) );
		$sched_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_PHOTOS."` WHERE `album` = %s AND `status` = 'scheduled'", $alb ) );

		// Result this level
		$result = array(	'albums' => $album_count,
							'photos' => $photo_count,
							'selfalbums' => $album_count,
							'selfphotos' => $photo_count,
							'pendphotos' => $pend_count,
							'scheduledphotos' => $sched_count,
							);

		// Subalbums to process?
		if ( empty($albs) ) {}
		else {
			foreach ( $albs as $albm ) {
				$subcount = wppa_treecount_a($albm['id']);
				$result['albums'] += $subcount['albums'];
				$result['photos'] += $subcount['photos'];
			}
		}

		// Save to cache
		$wppa['treecounts'][$alb][$albums] = $result['albums'];
		$wppa['treecounts'][$alb][$photos] = $result['photos'];
		$wppa['counts'][$alb][$selfalbums] = $result['selfalbums'];
		$wppa['counts'][$alb][$selfphotos] = $result['selfphotos'];
		$wppa['counts'][$alb][$pendphotos] = $result['pendphotos'];
		$wppa['counts'][$alb][$scheduledphotos] = $result['scheduledphotos'];
		$bret = WPPA_MULTISITE_GLOBAL ? update_site_option( 'wppa_counts', $wppa['counts'] ) : update_option( 'wppa_counts', $wppa['counts'] );
		$bret = WPPA_MULTISITE_GLOBAL ? update_site_option( 'wppa_counts_tree', $wppa['treecounts'] ) : update_option( 'wppa_counts_tree', $wppa['treecounts'] );

		return $result;
	}
}

function wppa_is_time_up($count = '') {
global $wppa_starttime;

	$timnow = microtime(true);
	$laptim = $timnow - $wppa_starttime;

	$maxwppatim = wppa_opt( 'max_execution_time' );
	$maxinitim = ini_get('max_execution_time');

	if ( $maxwppatim && $maxinitim ) $maxtim = min($maxwppatim, $maxinitim);
	elseif ( $maxwppatim ) $maxtim = $maxwppatim;
	elseif ( $maxinitim ) $maxtim = $maxinitim;
	else return false;

	wppa_dbg_msg('Maxtim = '.$maxtim.', elapsed = '.$laptim, 'red');
	if ( ! $maxtim ) return false;	// No limit or no value
	if ( ( $maxtim - $laptim ) > '5' ) return false;
	if ( $count ) {
		if ( is_admin() ) {
			if ( wppa_switch('wppa_auto_continue') ) {
				wppa_warning_message(sprintf(__('Time out after processing %s items.', 'wp-photo-album-plus'), $count));
			}
			else {
				wppa_error_message(sprintf(__('Time out after processing %s items. Please restart this operation', 'wp-photo-album-plus'), $count));
			}
		}
		else {
			wppa_alert(sprintf(__('Time out after processing %s items. Please restart this operation', 'wp-photo-album-plus'), $count));
		}
	}
	return true;
}


// Update photo modified timestamp
function wppa_update_modified($photo) {
global $wpdb;
	$wpdb->query($wpdb->prepare("UPDATE `".WPPA_PHOTOS."` SET `modified` = %s WHERE `id` = %s", time(), $photo));
}

function wppa_nl_to_txt($text) {
	return str_replace("\n", "\\n", $text);
}
function wppa_txt_to_nl($text) {
	return str_replace('\n', "\n", $text);
}

// Check query arg on tags
function wppa_vfy_arg($arg, $txt = false) {
	if ( isset($_REQUEST[$arg]) ) {
		if ( $txt ) {	// Text is allowed, but without tags
			$reason = ( defined('WP_DEBUG') && WP_DEBUG ) ? ': '.$arg.' contains tags.' : '';
			if ( $_REQUEST[$arg] != strip_tags($_REQUEST[$arg]) ) wp_die('Security check failue'.$reason);
		}
		else {
			$reason = ( defined('WP_DEBUG') && WP_DEBUG ) ? ': '.$arg.' is not numeric.' : '';
			$value = $_REQUEST[$arg];
			if ( $arg == 'photo-id' && strlen($value) == 12 ) {
				$value = wppa_decrypt_photo( $value );
			}
			if ( ! is_numeric($value) ) wp_die('Security check failue'.$reason);
		}
	}
}

// Strip tags with content
function wppa_strip_tags($text, $key = '') {

	if ($key == 'all') {
		$text = preg_replace(	array	(	'@<a[^>]*?>.*?</a>@siu',				// unescaped <a> tag
											'@&lt;a[^>]*?&gt;.*?&lt;/a&gt;@siu',	// escaped <a> tag
											'@<table[^>]*?>.*?</table>@siu',
											'@<style[^>]*?>.*?</style>@siu',
											'@<div[^>]*?>.*?</div>@siu'
										),
								array	( ' ', ' ', ' ', ' ', ' '
										),
								$text );
		$text = str_replace(array('<br/>', '<br />'), ' ', $text);
		$text = strip_tags($text);
	}
	elseif ( $key == 'script' ) {
		$text = preg_replace('@<script[^>]*?>.*?</script>@siu', ' ', $text );
	}
	elseif ( $key == 'div' ) {
		$text = preg_replace('@<div[^>]*?>.*?</div>@siu', ' ', $text );
	}
	elseif ( $key == 'script&style' || $key == 'style&script' ) {
		$text = preg_replace(	array	(	'@<script[^>]*?>.*?</script>@siu',
											'@<style[^>]*?>.*?</style>@siu'
										),
								array	( ' ', ' '
										),
								$text );
	}
	else {
		$text = preg_replace(	array	(	'@<a[^>]*?>.*?</a>@siu',				// unescaped <a> tag
											'@&lt;a[^>]*?&gt;.*?&lt;/a&gt;@siu'		// escaped <a> tag
										),
								array	( ' ', ' '
										),
								$text );
	}
	return trim($text);
}

// set last album
function wppa_set_last_album($id = '') {
    global $albumid;

	$opt = 'wppa_last_album_used-'.wppa_get_user('login');

	if ( is_numeric($id) && wppa_have_access($id) ) $albumid = $id; else $albumid = '';

    wppa_update_option($opt, $albumid);
}

// get last album
function wppa_get_last_album() {
global $albumid;

    if ( is_numeric( $albumid ) ) $result = $albumid;
    else {
		$opt = 'wppa_last_album_used-'.wppa_get_user('login');
		$result = get_option($opt, get_option('wppa_last_album_used', ''));
	}
    if ( !is_numeric( $result ) ) $result = '';
    else $albumid = $result;

	return $result;
}

// Combine margin or padding style
function wppa_combine_style($type, $top = '0', $left = '0', $right = '0', $bottom = '0') {
// echo $top.' '.$left.' '.$right.' '.$bottom.'<br />';
	$result = $type.':';			// Either 'margin:' or 'padding:'
	if ( $left == $right ) {
		if ( $top == $bottom ) {
			if ( $top == $left ) {	// All the same: one size fits all
				$result .= $top;
				if ( is_numeric($top) && $top > '0' ) $result .= 'px';
			}
			else {					// Top=Bot and Lft=Rht: two sizes
				$result .= $top;
				if ( is_numeric($top) && $top > '0' ) $result .= 'px '; else $result .= ' ';
				$result .= $left;
				if ( is_numeric($left) && $left > '0' ) $result .= 'px';
			}
		}
		else {						// Top, Lft=Rht, Bot: 3 sizes
			$result .= $top;
			if ( is_numeric($top) && $top > '0' ) $result .= 'px '; else $result .= ' ';
			$result .= $left;
			if ( is_numeric($left) && $left > '0' ) $result .= 'px '; else $result .= ' ';
			$result .= $bottom;
			if ( is_numeric($bottom) && $bottom > '0' ) $result .= 'px';
		}
	}
	else {							// Top, Rht, Bot, Lft: 4 sizes
		$result .= $top;
		if ( is_numeric($top) && $top > '0' ) $result .= 'px '; else $result .= ' ';
		$result .= $right;
		if ( is_numeric($right) && $right > '0' ) $result .= 'px '; else $result .= ' ';
		$result .= $bottom;
		if ( is_numeric($bottom) && $bottom > '0' ) $result .= 'px '; else $result .= ' ';
		$result .= $left;
		if ( is_numeric($left) && $left > '0' ) $result .= 'px';
	}
	$result .= ';';
	return $result;
}

// A temp routine to fix an old bug
function wppa_fix_source_extensions() {
global $wpdb;

	$start_time = time();
	$end = $start_time + '15';
	$count = '0';
	$start = get_option('wppa_sourcefile_fix_start', '0');
	if ( $start == '-1' ) return; // Done!

	$photos = $wpdb->get_results( 	"SELECT `id`, `album`, `name`, `filename`" .
										" FROM `".WPPA_PHOTOS."`" .
										" WHERE `filename` <> ''  AND `filename` <> `name` AND `id` > " . $start .
										" ORDER BY `id`", ARRAY_A
								);
	if ( $photos ) {
		foreach ( $photos as $data ) {
			$faulty_sourcefile_name = wppa_opt( 'source_dir' ).'/album-'.$data['album'].'/'.preg_replace('/\.[^.]*$/', '', $data['filename']);
			if ( is_file($faulty_sourcefile_name) ) {
				$proper_sourcefile_name = wppa_opt( 'source_dir' ).'/album-'.$data['album'].'/'.$data['filename'];
				if ( is_file($proper_sourcefile_name) ) {
					unlink($faulty_sourcefile_name);
				}
				else {
					rename($faulty_sourcefile_name, $proper_sourcefile_name);
				}
				$count++;
			}
			if ( time() > $end ) {
				wppa_ok_message( 'Fixed ' . $count . ' faulty sourcefile names.' .
									' Last was ' . $data['id'] . '.' .
									' Not finished yet. I will continue fixing next time you enter this page. Sorry for the inconvenience.'
								);

				update_option('wppa_sourcefile_fix_start', $data['id']);
				return;
			}
		}
	}
	echo $count.' source file extensions repaired';
	update_option('wppa_sourcefile_fix_start', '-1');
}

// Delete a photo and all its attrs by id
function wppa_delete_photo( $photo ) {
global $wppa_supported_audio_extensions;
global $wppa_supported_video_extensions;
global $wpdb;

	$photoinfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM `'.WPPA_PHOTOS.'` WHERE `id` = %s', $photo), ARRAY_A);

	// Get album
	$album = $photoinfo['album'];

	// Get filename
	$filename = $photoinfo['filename'];

	// Delete multimedia files
	if ( wppa_is_multi( $photo ) ) {
		$mmfile = wppa_strip_ext( wppa_get_photo_path( $photo ) );
		$allsup = array_merge( $wppa_supported_audio_extensions, $wppa_supported_video_extensions );
		foreach( $allsup as $mmext ) {
			if ( is_file( $mmfile.'.'.$mmext ) ) {
				@ unlink( $mmfile.'.'.$mmext );
			}
		}
	}

	// Delete fullsize image
	$file = wppa_get_photo_path( $photo );
	$file = wppa_fix_poster_ext( $file, $photo );
	if ( is_file( $file ) ) unlink( $file );

	// Delete thumbnail image
	$file = wppa_get_thumb_path( $photo );
	$file = wppa_fix_poster_ext( $file, $photo );
	if ( is_file( $file ) ) unlink( $file );

	// Delete sourcefile
	wppa_delete_source($filename, $album);

	// Delete index
	wppa_index_remove('photo', $photo);

	// Delete db entries
	$wpdb->query($wpdb->prepare('DELETE FROM `'.WPPA_PHOTOS.'` WHERE `id` = %s LIMIT 1', $photo));
	$wpdb->query($wpdb->prepare('DELETE FROM `'.WPPA_RATING.'` WHERE `photo` = %s', $photo));
	$wpdb->query($wpdb->prepare('DELETE FROM `'.WPPA_COMMENTS.'` WHERE `photo` = %s', $photo));
	$wpdb->query($wpdb->prepare('DELETE FROM `'.WPPA_IPTC.'` WHERE `photo` = %s', $photo));
	$wpdb->query($wpdb->prepare('DELETE FROM `'.WPPA_EXIF.'` WHERE `photo` = %s', $photo));
	wppa_flush_treecounts($album);
	wppa_flush_upldr_cache('photoid', $photo);

	// Delete from cloud
	if ( wppa_cdn( 'admin' ) == 'cloudinary' ) {
		wppa_delete_from_cloudinary( $photo );
	}
}

function wppa_microtime($txt = '') {
static $old;

	$new = microtime(true);
	if ( $old ) {
		$delta = $new - $old;
		$old = $new;
		$msg = sprintf('%s took %7.3f s.', $txt, $delta);
		wppa_dbg_msg($msg, 'green', true);
	}
	else $old = $new;
}

function wppa_sanitize_cats($value) {
	return wppa_sanitize_tags($value);
}
function wppa_sanitize_tags($value, $keepsemi = false, $keephash = false ) {

	// Sanitize
	$value = strip_tags( $value );					// Security

	$value = str_replace( 	array( 					// Remove funny chars
									'"',
									'\'',
									'\\',
									'@',
									'?',
									'|',
								 ),
							'',
							$value
						);
	if ( ! $keephash ) {
		$value = str_replace( '#', '', $value );
	}

	$value = stripslashes($value);					// ...

	// Find separator
	$sep = ',';										// Default seperator
	if ( $keepsemi ) {								// ';' allowed
		if ( strpos($value, ';') !== false ) {		// and found at least one ';'
			$value = str_replace(',', ';', $value);	// convert all separators to ';'
			$sep = ';';
		}											// ... a mix is not permitted
	}
	else {
		$value = str_replace(';', ',', $value);		// Convert all seps to default separator ','
	}

	$temp = explode( $sep, $value );
	if ( is_array($temp) ) {

		// Trim
		foreach ( array_keys( $temp ) as $idx ) {
			$temp[$idx] = trim( $temp[$idx] );
		}

		// Capitalize single words within tags
		if ( wppa_switch( 'capitalize_tags' ) ) {
			foreach ( array_keys($temp) as $idx ) {
				if ( strlen( $temp[$idx] ) > '1' ) {
					$words = explode( ' ', $temp[$idx] );
					foreach( array_keys($words) as $i ) {
						$words[$i] = strtoupper(substr($words[$i], 0, 1)).strtolower(substr($words[$i], 1));
					}
					$temp[$idx] = implode(' ', $words);
				}
			}
		}

		// Capitalize exif tags
		foreach ( array_keys( $temp ) as $idx ) {
			if ( substr( $temp[$idx], 0, 2 ) == 'E#' ) {
				$temp[$idx] = strtoupper( $temp[$idx] );
			}
		}

		// Sort
		asort($temp);

		// Remove dups and recombine
		$value = '';
		$first = true;
		$previdx = '';
		foreach ( array_keys($temp) as $idx ) {
			if ( strlen( $temp[$idx] ) > '1' ) {

				// Remove duplicates
				if ( $temp[$idx] ) {
					if ( $first ) {
						$first = false;
						$value .= $temp[$idx];
						$previdx = $idx;
					}
					elseif ( $temp[$idx] !=  $temp[$previdx] ) {
						$value .= $sep.$temp[$idx];
						$previdx = $idx;
					}
				}
			}
		}
	}

	if ( $sep == ',' && $value != '' ) {
		$value = $sep . $value . $sep;
	}
	return $value;
}

// Does the same as wppa_index_string_to_array() but with format validation and error reporting
function wppa_series_to_array($xtxt) {
	if ( is_array( $xtxt ) ) return false;
	$txt = str_replace(' ', '', $xtxt);					// Remove spaces
	if ( strpos($txt, '.') === false ) return false;	// Not an enum/series, the only legal way to return false
	if ( strpos($txt, '...') !== false ) {
		wppa_stx_err('Max 2 successive dots allowed. '.$txt);
		return false;
	}
	if ( substr($txt, 0, 1) == '.' ) {
		wppa_stx_err('Missing starting number. '.$txt);
		return false;
	}
	if ( substr($txt, -1) == '.' ) {
		wppa_stx_err('Missing ending number. '.$txt);
		return false;
	}
	$t = str_replace(array('.','0','1','2','3','4','5','6','7','8','9'), '',$txt);
	if ( $t ) {
		wppa_stx_err('Illegal character(s): "'.$t.'" found. '.$txt);
		return false;
	}

	// Trim leading '0.'
	if ( substr( $txt, 0, 2 ) == '0.' ) {
		$txt = substr( $txt, 2 );
	}

	$temp = explode('.', $txt);
	$tempcopy = $temp;

	foreach ( array_keys($temp) as $i ) {
		if ( ! $temp[$i] ) { 							// found a '..'
			if ( $temp[$i-'1'] >= $temp[$i+'1'] ) {
				wppa_stx_err('Start > end. '.$txt);
				return false;
			}
			for ( $j=$temp[$i-'1']+'1'; $j<$temp[$i+'1']; $j++ ) {
				$tempcopy[] = $j;
			}
		}
		else {
			if ( ! is_numeric($temp[$i] ) ) {
				wppa_stx_err('A enum or range token must be a number. '.$txt);
				return false;
			}
		}
	}
	$result = $tempcopy;
	foreach ( array_keys($result) as $i ) {
		if ( ! $result[$i] ) unset($result[$i]);
	}
	return $result;
}
function wppa_stx_err($msg) {
	echo 'Syntax error in album specification. '.$msg;
}


function wppa_get_og_desc( $id ) {

	$result = 	sprintf( __('See this image on %s', 'wp-photo-album-plus'), str_replace( '&amp;', __( 'and' , 'wp-photo-album-plus'), get_bloginfo( 'name' ) ) ) .
				': ' .
				strip_shortcodes( wppa_strip_tags( wppa_html( wppa_get_photo_desc( $id ) ), 'all' ) );

	$result = 	apply_filters( 'wppa_get_og_desc', $result );

	return $result;
}

// There is no php routine to test if a string var is an integer, like '3': yes, and '3.7' and '3..7': no.
// is_numeric('3.7') returns true
// intval('3..7') == '3..7' returns true
// is_int('3') returns false
// so we make it ourselves
function wppa_is_int($var) {
	return ( strval(intval($var)) == strval($var) );
}

// return true if $var only contains digits and points
function wppa_is_enum( $var ) {
	return '' === str_replace( array( '0','1','2','3','4','5','6','7','8','9','.' ), '', $var );
}

function wppa_log( $xtype, $msg, $trace = false ) {
global $wppa_session;

	// Sanitize type
	$t = strtolower( substr( $xtype, 0, 1 ) );
	switch ( $t ) {
		case 'd':
			$type = 'Dbg';
			break;
		case 'e':
			$type = 'Err';
			break;
		case 'f':
			$type = 'Fix';
			break;
		case 'o':
			$type = 'Obs';
			break;
		case 'u':
			$type = 'Upl';
			break;
		case 'w':
			$type = 'War';
			break;
		default:
			$type = 'Misc';
	}

	// Log debug messages only if WP_DEBUG is defined as true
	if ( $type == 'Dbg' ) {
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return;
		}
	}

	// Make path to logfile
	@ wppa_mktree( WPPA_CONTENT_PATH.'/wppa-depot/admin' ); // Just in case...
	$filename = WPPA_CONTENT_PATH.'/wppa-depot/admin/error.log';

	// See if max size exceeded
	if ( is_file( $filename ) ) {
		$filesize = filesize( $filename );
		if ( $filesize > 1024000 ) {

			// File > 1000kB, shorten it
			$file = fopen( $filename, 'rb' );
			if ( $file ) {
				$buffer = @ fread( $file, $filesize );
				$buffer = substr( $buffer, $filesize - 900*1024 );	// Take ending 900 kB
				fclose( $file );
				$file = fopen( $filename, 'wb' );
				@ fwrite( $file, $buffer );
				@ fclose( $file );
			}
		}
	}

	// Open for append
	if ( ! $file = fopen( $filename, 'ab' ) ) return;	// Unable to open log file

	// Write log message
	@ fwrite( $file, '{b}'.$type.'{/b}: on:'.wppa_local_date(get_option('date_format', "F j, Y,").' '.get_option('time_format', "g:i a"), time()).': '.wppa_get_user().': '.strip_tags($msg)."\n" );

	// Trace 6 levels if trace requested
	if ( $trace ) {
		ob_start();
		debug_print_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 12 );
		$trace = ob_get_contents();
		ob_end_clean();
		@ fwrite( $file, $trace."\n" );

		// Add uri history
		@ fwrite( $file, 'Uri history:'."\n" );
		if ( is_array( $wppa_session ) ) {
			foreach ( $wppa_session['uris'] as $uri ) {
				@ fwrite( $file, $uri . "\n" );
			}
			@ fwrite( $file, "\n\n" );
		}
	}
	@ fclose( $file );
}

function wppa_is_landscape($img_attr) {
	return ($img_attr[0] > $img_attr[1]);
}

function wppa_get_the_id() {

	$id = '0';
	if ( wppa( 'ajax' ) ) {
		if ( wppa_get_get( 'page_id' ) ) $id = wppa_get_get( 'page_id' );
		elseif ( wppa_get_get( 'p' ) ) $id = wppa_get_get( 'p' );
		elseif ( wppa_get_get( 'wppa-fromp' ) ) $id = wppa_get_get( 'wppa-fromp' );
	}
	else {
		$id = get_the_ID();
	}
	return $id;
}


function wppa_get_artmonkey_size_a($photo) {
global $wpdb;

	$data = $wpdb->get_row($wpdb->prepare("SELECT * FROM `".WPPA_PHOTOS."` WHERE `id` = %s", $photo), ARRAY_A);
	if ( $data ) {
		if ( wppa_switch('wppa_artmonkey_use_source') ) {
			if ( is_file( wppa_get_source_path($photo) ) ) {
				$source = wppa_get_source_path($photo);
			}
			else {
				$source = wppa_get_photo_path($photo);
			}
		}
		else {
			$source = wppa_get_photo_path($photo);
		}
		$imgattr = @ getimagesize( $source );
		if ( is_array( $imgattr ) ) {
			$fs = wppa_get_filesize( $source );
			$result = array( 'x' => $imgattr['0'], 'y' => $imgattr['1'], 's' => $fs );
			return $result;
		}
	}
	return false;
}

function wppa_get_filesize( $file ) {

	if ( is_file( $file ) ) {
		$fs = filesize( $file );

		if ( $fs > 1024*1024 ) {
			$fs = sprintf('%4.2f Mb', $fs/(1024*1024));
		}
		else {
			$fs = sprintf('%4.2f Kb', $fs/1024);
		}
		return $fs;
	}

	return false;
}


function wppa_get_the_landing_page($slug, $title) {

	$page = wppa_opt( $slug );
	if ( ! $page || ! wppa_page_exists( $page ) ) {
	$page = wppa_create_page( $title );
		wppa_update_option( $slug, $page );
		wppa_opt( $slug, $page );
	}
	return $page;
}

function wppa_get_the_auto_page( $photo ) {
global $wpdb;

	if ( ! $photo ) return '0';					// No photo id, no page
	if ( ! wppa_is_int( $photo ) ) return '0';	// $photo not numeric

	$thumb = wppa_cache_thumb( $photo );		// Get photo info

	// Page exists ?
	if ( wppa_page_exists( $thumb['page_id'] ) ) {
		return $thumb['page_id'];
	}

	// Create new page
	$page = wppa_create_page( $thumb['name'], '[wppa type="autopage"][/wppa]' );

	// Store with photo data
	$wpdb->query( $wpdb->prepare( "UPDATE `".WPPA_PHOTOS."` SET `page_id` = ".$page." WHERE `id` = %d", $photo ) );

	// Update cache
	$thumb['page_id'] = $page;

	return $page;
}

function wppa_remove_the_auto_page( $photo ) {

	if ( ! $photo ) return '0';					// No photo id, no page
	if ( ! wppa_is_int( $photo ) ) return '0';	// $photo not numeric

	$thumb = wppa_cache_thumb( $photo );		// Get photo info

	// Page exists ?
	if ( wppa_page_exists( $thumb['page_id'] ) ) {
		wp_delete_post( $thumb['page_id'], true );
		wppa_update_photo( array( 'id' => $photo, 'page_id' => '0' ) );
	}
}

function wppa_create_page( $title, $shortcode = '[wppa type="landing"][/wppa]' ) {

	$my_page = array(
				'post_title'    => $title,
				'post_content'  => $shortcode,
				'post_status'   => 'publish',
				'post_type'	  	=> 'page'
			);

	$page = wp_insert_post( $my_page );
	return $page;
}

// Check if a published page exists
function wppa_page_exists( $id ) {
global $wpdb;
static $pages_exist;

	// Check on valid input
	if ( ! $id ) return false;

	// Already found existing or non existing?
	if ( isset( $pages_exist[$id] ) ) {
		return $pages_exist[$id];
	}

	// Do a query
	$iret = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `" .
											$wpdb->posts . "` " .
											"WHERE `post_type` = 'page' " .
											"AND `post_status` = 'publish' " .
											"AND `ID` = %s", $id ) );

	// Save result
	$pages_exist[$id] = ( $iret > 0 );

	return $pages_exist[$id];
}

function wppa_get_photo_owner( $id ) {

	$thumb = wppa_cache_thumb( $id );
	return $thumb['owner'];
}

function wppa_cdn( $side ) {

	// What did we specify in the settings page?
	$cdn = wppa_opt( 'cdn_service' );

	// Check for fully configured and active
	switch ( $cdn ) {
		case 'cloudinary':
		case 'cloudinarymaintenance':
			if ( wppa_opt( 'cdn_cloud_name' ) && wppa_opt( 'cdn_api_key' ) && wppa_opt( 'cdn_api_secret' ) ) {
				if ( $side == 'admin' ) {		// Admin: always return cloudinary
					$cdn = 'cloudinary';
				}
				elseif ( $side == 'front' ) {	// Front: NOT if in maintenance
					if ( $cdn == 'cloudinarymaintenance' ) {
						$cdn = false;
					}
				}
				else {
					wppa_dbg_msg( 'dbg', 'Wrong arg:'.$side.' in wppa_cdn()', 'red', 'force' );
					$cdn = false;
				}
			}
			else {
				wppa_dbg_msg( 'dbg', 'Incomplete configuration of Cloudinary', 'red', 'force' );
				$cdn = false;	// Incomplete configuration
			}
			break;

		default:
			$cdn = false;

	}

	return $cdn;
}

function wppa_get_source_path( $id ) {
global $blog_id;
global $wppa_supported_photo_extensions;

	// Source files can have uppercase extensions.
	$temp = array();
	foreach( $wppa_supported_photo_extensions as $ext ) {
		$temp[] = strtoupper( $ext );
	}
	$supext = array_merge( $wppa_supported_photo_extensions, $temp );

	$thumb = wppa_cache_thumb( $id );

	$multi = is_multisite();
	if ( $multi && ! WPPA_MULTISITE_GLOBAL ) {
		$blog = '/blog-'.$blog_id;
	}
	else {
		$blog = '';
	}
	$source_path = wppa_opt( 'source_dir' ).$blog.'/album-'.$thumb['album'].'/'.$thumb['filename'];
	if ( wppa_is_multi( $id ) ) {
		$path = wppa_strip_ext( $source_path );
		foreach ( $supext as $ext ) {
			$source = $path . '.' . $ext;
			if ( is_file( $source ) ) {
				return $source;
			}
		}
	}

	return $source_path;
}

// Get url of photo with highest available resolution.
// Not for display ( need not to download fast ) but for external services like Fotomoto
function wppa_get_hires_url( $id ) {

	// video? return the poster url
	if ( wppa_is_video( $id ) || wppa_has_audio( $id ) ) {
		$url = wppa_get_photo_url( $id );
		$url = wppa_fix_poster_ext( $url, $id );
		$temp = explode( '?', $url );
		$url = $temp['0'];
		return $url;
	}

	// Try CDN
	if ( wppa_cdn( 'front' ) && ! wppa_too_old_for_cloud( $id ) ) {
		switch ( wppa_cdn( 'front' ) ) {
			case 'cloudinary':
				$url = wppa_get_cloudinary_url( $id );
				break;
			default:
				$url = '';
		}
		if ( $url ) return $url;
	}

	// Try the source url
	$source_path = wppa_get_source_path( $id );
	$wp_content = trim( str_replace( site_url(), '', content_url() ), '/' );
	if ( is_file( $source_path ) ) {
		$temp = explode( $wp_content, $source_path );
		$hires_url = site_url().'/'.$wp_content.$temp['1'];
	}

	// Try the medium res url
	else {
		$hires_url = wppa_get_photo_url( $id );
	}
	$temp = explode( '?', $hires_url );
	$hires_url = $temp['0'];
	return $hires_url;
}
function wppa_get_lores_url( $id ) {
	$lores_url = wppa_fix_poster_ext( wppa_get_photo_url( $id ), $id );
	$temp = explode( '?', $lores_url );
	$lores_url = $temp['0'];
	return $lores_url;
}
function wppa_get_tnres_url( $id ) {
	$tnres_url = wppa_fix_poster_ext( wppa_get_thumb_url( $id ), $id );
	$temp = explode( '?', $tnres_url );
	$tnres_url = $temp['0'];
	return $tnres_url;
}
function wppa_get_source_pl( $id ) {
	$result = '';
	$source_path = wppa_fix_poster_ext( wppa_get_source_path( $id ), $id );
	if ( is_file( $source_path ) ) {
		$result = 	content_url() . '/' . 						// http://www.mysite.com/wp-content/
					wppa_opt( 'pl_dirname' ) . '/' .		// wppa-pl/
					wppa_sanitize_file_name( wppa_get_album_item( wppa_get_photo_item( $id, 'album' ), 'name' ), false ) . '/' .	// My-Album
					basename( $source_path );					// My-Photo.jpg
	}
	return $result;
}

function wppa_get_source_dir() {
global $blog_id;

	$multi = is_multisite();
//	$multi = true;	// debug
	if ( $multi && ! WPPA_MULTISITE_GLOBAL ) {
		$blog = '/blog-'.$blog_id;
	}
	else {
		$blog = '';
	}
	$source_dir = wppa_opt( 'source_dir' ).$blog;

	return $source_dir;
}

function wppa_get_source_album_dir( $alb ) {
global $blog_id;

	$multi = is_multisite();
//	$multi = true;	// debug
	if ( $multi && ! WPPA_MULTISITE_GLOBAL ) {
		$blog = '/blog-'.$blog_id;
	}
	else {
		$blog = '';
	}
	$source_album_dir = wppa_opt( 'source_dir' ).$blog.'/album-'.$alb;

	return $source_album_dir;
}


function wppa_set_default_name( $id, $filename_raw = '' ) {
global $wpdb;

	if ( ! wppa_is_int( $id ) ) return;
	$thumb = wppa_cache_thumb( $id );

	$method 	= wppa_opt( 'newphoto_name_method' );
	$name 		= $thumb['filename']; 	// The default default
	$filename 	= $thumb['filename'];

	switch ( $method ) {
		case 'none':
			$name = '';
			break;
		case 'filename':
			if ( $filename_raw ) {
				$name = wppa_sanitize_photo_name( $filename_raw );
			}
			break;
		case 'noext':
			if ( $filename_raw ) {
				$name = wppa_sanitize_photo_name( $filename_raw );
			}
			$name = preg_replace('/\.[^.]*$/', '', $name);
			break;
		case '2#005':
			$tag = '2#005';
			$name = $wpdb->get_var( $wpdb->prepare( "SELECT `description` FROM `".WPPA_IPTC."` WHERE `photo` = %s AND `tag` = %s", $id, $tag ) );
			break;
		case '2#120':
			$tag = '2#120';
			$name = $wpdb->get_var( $wpdb->prepare( "SELECT `description` FROM `".WPPA_IPTC."` WHERE `photo` = %s AND `tag` = %s", $id, $tag ) );
			break;
	}
	if ( ( $name && $name != $filename ) || $method == 'none' ) {	// Update name
		$wpdb->query( $wpdb->prepare( "UPDATE `".WPPA_PHOTOS."` SET `name` = %s WHERE `id` = %s", $name, $id ) );
		$thumb['name'] = $name;	// Update cache
	}
	if ( ! wppa_switch('wppa_save_iptc') ) { 	// He doesn't want to keep the iptc data, so...
		$wpdb->query($wpdb->prepare( "DELETE FROM `".WPPA_IPTC."` WHERE `photo` = %s", $id ) );
	}
}

function wppa_set_default_tags( $id ) {
global $wpdb;

	$thumb 	= wppa_cache_thumb( $id );
	$album 	= wppa_cache_album( $thumb['album'] );
	$tags 	= wppa_sanitize_tags( str_replace( array( '\'', '"'), ',', wppa_filter_iptc( wppa_filter_exif( $album['default_tags'], $id ), $id ) ) );

	if ( $tags ) {
		wppa_update_photo( array( 'id' => $id, 'tags' => $tags ) );
		wppa_clear_taglist();
		wppa_cache_thumb( 'invalidate', $id );
	}
}

function wppa_test_for_medal( $id ) {
global $wpdb;

	$thumb = wppa_cache_thumb( $id );
	$status = $thumb['status'];

	if ( wppa_opt( 'medal_bronze_when' ) || wppa_opt( 'medal_silver_when' ) || wppa_opt( 'medal_gold_when' ) ) {
		$max_score = wppa_opt( 'rating_max' );

		$max_ratings = $wpdb->get_var( $wpdb->prepare( 	"SELECT COUNT(*) FROM `".WPPA_RATING."` " .
														"WHERE `photo` = %s AND `value` = %s AND `status` = %s", $id, $max_score, 'publish'
													)
									);

		if ( $max_ratings >= wppa_opt( 'medal_gold_when' ) ) $status = 'gold';
		elseif ( $max_ratings >= wppa_opt( 'medal_silver_when' ) ) $status = 'silver';
		elseif ( $max_ratings >= wppa_opt( 'medal_bronze_when' ) ) $status = 'bronze';
	}

	if ( $status != $thumb['status'] ) {
		$thumb['status'] = $status;	// Update cache
		$wpdb->query( $wpdb->prepare( "UPDATE `".WPPA_PHOTOS."` SET `status` = %s WHERE `id` = %s", $status, $id ) );
	}
}

function wppa_get_the_bestof( $count, $period, $sortby, $what ) {
global $wpdb;

	// Phase 1, find the period we are talking about
	// find $start and $end
	switch ( $period ) {
		case 'lastweek':
			$start 	= wppa_get_timestamp( 'lastweekstart' );
			$end   	= wppa_get_timestamp( 'lastweekend' );
			break;
		case 'thisweek':
			$start 	= wppa_get_timestamp( 'thisweekstart' );
			$end   	= wppa_get_timestamp( 'thisweekend' );
			break;
		case 'lastmonth':
			$start 	= wppa_get_timestamp( 'lastmonthstart' );
			$end 	= wppa_get_timestamp( 'lastmonthend' );
			break;
		case 'thismonth':
			$start 	= wppa_get_timestamp( 'thismonthstart' );
			$end 	= wppa_get_timestamp( 'thismonthend' );
			break;
		case 'lastyear':
			$start 	= wppa_get_timestamp( 'lastyearstart' );
			$end 	= wppa_get_timestamp( 'lastyearend' );
			break;
		case 'thisyear':
			$start 	= wppa_get_timestamp( 'thisyearstart' );
			$end 	= wppa_get_timestamp( 'thisyearend' );
			break;
		default:
			return 'Unimplemented period: '.$period;
	}

	// Phase 2, get the ratings of the period
	// find $ratings, ordered by photo id
	$ratings 	= $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `".WPPA_RATING."` WHERE `timestamp` >= %s AND `timestamp` < %s ORDER BY `photo`", $start, $end ), ARRAY_A );

	// Phase 3, set up an array with data we need
	// There are two methods: photo oriented and owner oriented, depending on

	// Each element reflects a photo ( key = photo id ) and is an array with items: maxratings, meanrating, ratings, totvalue.
	$ratmax	= wppa_opt( 'rating_max' );
	$data 	= array();
	foreach ( $ratings as $rating ) {
		$key = $rating['photo'];
		if ( ! isset( $data[$key] ) ) {
			$data[$key] = array();
			$data[$key]['ratingcount'] 		= '1';
			$data[$key]['maxratingcount'] 	= $rating['value'] == $ratmax ? '1' : '0';
			$data[$key]['totvalue'] 		= $rating['value'];
		}
		else {
			$data[$key]['ratingcount'] 		+= '1';
			$data[$key]['maxratingcount'] 	+= $rating['value'] == $ratmax ? '1' : '0';
			$data[$key]['totvalue'] 		+= $rating['value'];
		}
	}
	foreach ( array_keys( $data ) as $key ) {
		$thumb = wppa_cache_thumb( $key );
		$data[$key]['meanrating'] = $data[$key]['totvalue'] / $data[$key]['ratingcount'];
		$user = get_user_by( 'login', $thumb['owner'] );
		if ( $user ) {
			$data[$key]['user'] = $user->display_name;
		}
		else { // user deleted
			$data[$key]['user'] = $thumb['owner'];
		}
		$data[$key]['owner'] = $thumb['owner'];
	}

	// Now we split into search for photos and search for owners

	if ( $what == 'photo' ) {

		// Pase 4, sort to the required sequence
		$data = wppa_array_sort( $data, $sortby, SORT_DESC );

	}
	else { 	// $what == 'owner'

		// Phase 4, combine all photos of the same owner
		wppa_array_sort( $data, 'user' );
		$temp = $data;
		$data = array();
		foreach ( array_keys( $temp ) as $key ) {
			if ( ! isset( $data[$temp[$key]['user']] ) ) {
				$data[$temp[$key]['user']]['photos'] 			= '1';
				$data[$temp[$key]['user']]['ratingcount'] 		= $temp[$key]['ratingcount'];
				$data[$temp[$key]['user']]['maxratingcount'] 	= $temp[$key]['maxratingcount'];
				$data[$temp[$key]['user']]['totvalue'] 			= $temp[$key]['totvalue'];
				$data[$temp[$key]['user']]['owner'] 			= $temp[$key]['owner'];
			}
			else {
				$data[$temp[$key]['user']]['photos'] 			+= '1';
				$data[$temp[$key]['user']]['ratingcount'] 		+= $temp[$key]['ratingcount'];
				$data[$temp[$key]['user']]['maxratingcount'] 	+= $temp[$key]['maxratingcount'];
				$data[$temp[$key]['user']]['totvalue'] 			+= $temp[$key]['totvalue'];
			}
		}
		foreach ( array_keys( $data ) as $key ) {
			$data[$key]['meanrating'] = $data[$key]['totvalue'] / $data[$key]['ratingcount'];
		}
		$data = wppa_array_sort( $data, $sortby, SORT_DESC );
	}

	// Phase 5, truncate to the desired length
	$c = '0';
	foreach ( array_keys( $data ) as $key ) {
		$c += '1';
		if ( $c > $count ) unset ( $data[$key] );
	}

	// Phase 6, return the result
	if ( count( $data ) ) {
		return $data;
	}
	else {
		return 	__('There are no ratings between', 'wp-photo-album-plus') .
				'<br />' .
				wppa_local_date( 'F j, Y, H:i s', $start ) .
				__('and', 'wp-photo-album-plus') .
				'<br />' .
				wppa_local_date( 'F j, Y, H:i s', $end ) .
				'.';
	}
}

// To check on possible duplicate
function wppa_file_is_in_album( $filename, $alb ) {
global $wpdb;

	if ( ! $filename ) return false;	// Copy/move very old photo, before filnametracking
	$photo_id = $wpdb->get_var ( $wpdb->prepare ( 	"SELECT `id` FROM `".WPPA_PHOTOS."` " .
													"WHERE ( `filename` = %s OR `filename` = %s ) AND `album` = %s LIMIT 1",
														wppa_sanitize_file_name( $filename ), $filename, $alb
												)
								);
	return $photo_id;
}

// Retrieve the number of child albums ( if any )
function wppa_has_children( $alb ) {
global $wpdb;
static $childcounts;

	// See if done this alb earlier
	if ( isset( $childcounts[$alb] ) ) {
		$result = $childcounts[$alb];
	}
	else {
		$result = $wpdb->get_var( $wpdb->prepare( 	"SELECT COUNT(*) " .
													"FROM `" . WPPA_ALBUMS . "` " .
													"WHERE `a_parent` = %s", $alb) );

		// Save result
		$childcounts[$alb] = $result;
	}

	return $result;
}

// Get an enumeration of all the (grand)children of some album spec.
// Album spec may be a number or an enumeration
function wppa_alb_to_enum_children( $xalb ) {
	if ( strpos( $xalb, '.' ) !== false ) {
		$albums = explode( '.', $xalb );
	}
	else {
		$albums = array( $xalb );
	}
	$result = '';
	foreach( $albums as $alb ) {
		$result .= _wppa_alb_to_enum_children( $alb );
		$result = trim( $result, '.' ).'.';
	}
	$result = trim( $result, '.' );
	$result = wppa_compress_enum( $result );
	return $result;
}

function _wppa_alb_to_enum_children( $alb ) {
global $wpdb;
static $child_cache;

	// Done this one before?
	if ( isset( $child_cache[$alb] ) ) {
		return $child_cache[$alb];
	}

	// Get the data
	$result = $alb;
	$children = $wpdb->get_results( $wpdb->prepare( "SELECT `id` FROM `".WPPA_ALBUMS."` WHERE `a_parent` = %s", $alb ), ARRAY_A );
	if ( $children ) foreach ( $children as $child ) {
		$result .= '.' . _wppa_alb_to_enum_children( $child['id'] );
		$result = trim( $result, '.' );
	}

	// Store in cache
	$child_cache[$alb] = $result;

	// Return requested data
	return $child_cache[$alb];
}

function wppa_compress_enum( $enum ) {
	$result = $enum;
	if ( strpos( $enum, '.' ) !== false ) {
		$result = explode( '.', $enum );
		sort( $result, SORT_NUMERIC );
		$old = '-99';
		foreach ( array_keys( $result ) as $key ) { 	// Remove dups
			if ( $result[$key] == $old ) unset ( $result[$key] );
			else $old = $result[$key];
		}
		$result = wppa_index_array_to_string( $result );
		$result = str_replace( ',', '.', $result );
	}
	$result = trim( $result, '.' );
	return $result;
}

function wppa_expand_enum( $enum ) {
	$result = $enum;
	$result = str_replace( '.', ',', $result );
	$result = str_replace( ',,', '..', $result );
	$result = wppa_index_string_to_array( $result );
	$result = implode( '.', $result );
	return $result;
}

function wppa_mktree( $path ) {
	if ( is_dir( $path ) ) {
		@ chmod( $path, 0755 );
		return true;
	}
	$bret = wppa_mktree( dirname( $path ) );
	@ mkdir( $path );
	@ chmod( $path, 0755 );
	return ( is_dir( $path ) );
}

function wppa_rate_photo( $id ) {
global $wpdb;

	$ratings = $wpdb->get_results( $wpdb->prepare( "SELECT `value` FROM `".WPPA_RATING."` WHERE `photo` = %s AND `status` = %s", $id, 'publish' ), ARRAY_A );
	$the_value = '0';
	$the_count = '0';
	if ( $ratings ) foreach ($ratings as $rating) {
		if ( $rating['value'] == '-1' ) $the_value += wppa_opt( 'dislike_value' );
		else $the_value += $rating['value'];
		$the_count++;
	}
	if ( $the_count ) $the_value /= $the_count;
	if ( $the_value == '10' ) $the_value = '9.9999999';	// mean_rating is a text field. for sort order reasons we make 10 into 9.99999
	$wpdb->query( $wpdb->prepare( "UPDATE `".WPPA_PHOTOS."` SET `mean_rating` = %s WHERE `id` = %s", $the_value, $id ) );
	$ratcount = count($ratings);
	$wpdb->query( $wpdb->prepare( "UPDATE `".WPPA_PHOTOS."` SET `rating_count` = %s WHERE `id` = %s", $ratcount, $id ) );
	wppa_test_for_medal( $id );
}

function wppa_strip_ext( $file ) {
	return preg_replace('/\.[^.]*$/', '', $file);
}

function wppa_get_ext( $file ) {
	return str_replace( wppa_strip_ext( $file ).'.', '', $file );
}

function wppa_encode_uri_component( $xstr ) {
	$str = $xstr;
	$illegal = array( '?', '&', '#', '/', '"', "'", ' ' );
	foreach ( $illegal as $char ) {
		$str = str_replace( $char, sprintf( '%%%X', ord($char) ), $str );
	}
	return $str;
}

function wppa_decode_uri_component( $xstr ) {
	$str = $xstr;
	$illegal = array( '?', '&', '#', '/', '"', "'", ' ' );
	foreach ( $illegal as $char ) {
		$str = str_replace( sprintf( '%%%X', ord($char) ), $char, $str );
		$str = str_replace( sprintf( '%%%x', ord($char) ), $char, $str );
	}
	return $str;
}

function wppa_force_numeric_else( $value, $default ) {
	if ( ! $value ) return $value;
	if ( ! wppa_is_int( $value ) ) return $default;
	return $value;
}

// Same as wp sanitize_file_name, except that it can be used for a pathname also.
// If a pathname: only the basename of the path is sanitized.
function wppa_sanitize_file_name( $file, $check_length = true ) {
	$temp 	= explode( '/', $file );
	$cnt 	= count( $temp );
	$temp[$cnt - 1] = sanitize_file_name( $temp[$cnt - 1] );
	$maxlen = wppa_opt( 'max_filename_length' );
	if ( $maxlen && $check_length ) {
		if ( strpos( $temp[$cnt - 1], '.' ) !== false ) {
			$name = wppa_strip_ext( $temp[$cnt - 1] );
			$ext = str_replace( $name.'.', '', $temp[$cnt - 1] );
			if ( strlen( $name ) > $maxlen ) {
				$name = substr( $name, 0, $maxlen );
				$temp[$cnt - 1] = $name.'.'.$ext;
			}
		}
		else {
			if ( strlen( $temp[$cnt - 1] ) > $maxlen ) {
				$temp[$cnt - 1] = substr( $temp[$cnt - 1], 0, $maxlen );
			}
		}
	}
	$file 	= implode( '/', $temp );
	$file 	= trim ( $file );
	return $file;
}

// Create a html safe photo name from a filename. May be a pathname
function wppa_sanitize_photo_name( $file ) {
	$result = htmlspecialchars( strip_tags( stripslashes( basename( $file ) ) ) );
	$maxlen = wppa_opt( 'max_photoname_length' );
	if ( $maxlen && strlen( $result ) > $maxlen ) {
		$result = wppa_strip_ext( $result ); // First remove any possible file-extension
		if ( strlen( $result ) > $maxlen ) {
			$result = substr( $result, 0, $maxlen );	// Truncate
		}
	}
	return $result;
}

// Get meta keywords of a photo
function wppa_get_keywords( $id ) {
static $wppa_void_keywords;

	if ( ! $id ) return '';

	if ( empty ( $wppa_void_keywords ) ) {
		$wppa_void_keywords	= array( 	__('Not Defined', 'wp-photo-album-plus'),
										__('Manual', 'wp-photo-album-plus'),
										__('Program AE', 'wp-photo-album-plus'),
										__('Aperture-priority AE', 'wp-photo-album-plus'),
										__('Shutter speed priority AE', 'wp-photo-album-plus'),
										__('Creative (Slow speed)', 'wp-photo-album-plus'),
										__('Action (High speed)', 'wp-photo-album-plus'),
										__('Portrait', 'wp-photo-album-plus'),
										__('Landscape', 'wp-photo-album-plus'),
										__('Bulb', 'wp-photo-album-plus'),
										__('Average', 'wp-photo-album-plus'),
										__('Center-weighted average', 'wp-photo-album-plus'),
										__('Spot', 'wp-photo-album-plus'),
										__('Multi-spot', 'wp-photo-album-plus'),
										__('Multi-segment', 'wp-photo-album-plus'),
										__('Partial', 'wp-photo-album-plus'),
										__('Other', 'wp-photo-album-plus'),
										__('No Flash', 'wp-photo-album-plus'),
										__('Fired', 'wp-photo-album-plus'),
										__('Fired, Return not detected', 'wp-photo-album-plus'),
										__('Fired, Return detected', 'wp-photo-album-plus'),
										__('On, Did not fire', 'wp-photo-album-plus'),
										__('On, Fired', 'wp-photo-album-plus'),
										__('On, Return not detected', 'wp-photo-album-plus'),
										__('On, Return detected', 'wp-photo-album-plus'),
										__('Off, Did not fire', 'wp-photo-album-plus'),
										__('Off, Did not fire, Return not detected', 'wp-photo-album-plus'),
										__('Auto, Did not fire', 'wp-photo-album-plus'),
										__('Auto, Fired', 'wp-photo-album-plus'),
										__('Auto, Fired, Return not detected', 'wp-photo-album-plus'),
										__('Auto, Fired, Return detected', 'wp-photo-album-plus'),
										__('No flash function', 'wp-photo-album-plus'),
										__('Off, No flash function', 'wp-photo-album-plus'),
										__('Fired, Red-eye reduction', 'wp-photo-album-plus'),
										__('Fired, Red-eye reduction, Return not detected', 'wp-photo-album-plus'),
										__('Fired, Red-eye reduction, Return detected', 'wp-photo-album-plus'),
										__('On, Red-eye reduction', 'wp-photo-album-plus'),
										__('Red-eye reduction, Return not detected', 'wp-photo-album-plus'),
										__('On, Red-eye reduction, Return detected', 'wp-photo-album-plus'),
										__('Off, Red-eye reduction', 'wp-photo-album-plus'),
										__('Auto, Did not fire, Red-eye reduction', 'wp-photo-album-plus'),
										__('Auto, Fired, Red-eye reduction', 'wp-photo-album-plus'),
										__('Auto, Fired, Red-eye reduction, Return not detected', 'wp-photo-album-plus'),
										__('Auto, Fired, Red-eye reduction, Return detected', 'wp-photo-album-plus'),
										'album', 'albums', 'content', 'http',
										'source', 'wp', 'uploads', 'thumbs',
										'wp-content', 'wppa-source',
										'border', 'important', 'label', 'padding',
										'segment', 'shutter', 'style', 'table',
										'times', 'value', 'views', 'wppa-label',
										'wppa-value', 'weighted', 'wppa-pl',
										str_replace( '/', '', site_url() )
									);

		// make a string
		$temp = implode( ',', $wppa_void_keywords );

		// Downcase
		$temp = strtolower( $temp );

		// Remove spaces and funny chars
		$temp = str_replace( array( ' ', '-', '"', "'", '\\', '>', '<', ',', ':', ';', '!', '?', '=', '_', '[', ']', '(', ')', '{', '}' ), ',', $temp );
		$temp = str_replace( ',,', ',', $temp );
//wppa_log('dbg', $temp);

		// Make array
		$wppa_void_keywords = explode( ',', $temp );

		// Sort array
		sort( $wppa_void_keywords );

		// Remove dups
		$start = 0;
		foreach ( array_keys( $wppa_void_keywords ) as $key ) {
			if ( $key > 0 ) {
				if ( $wppa_void_keywords[$key] == $wppa_void_keywords[$start] ) {
					unset ( $wppa_void_keywords[$key] );
				}
				else {
					$start = $key;
				}
			}
		}
	}

	$text 	= wppa_get_photo_name( $id )  .' ' . wppa_get_photo_desc( $id );
	$text 	= str_replace( array( '/', '-' ), ' ', $text );
	$words 	= wppa_index_raw_to_words( $text );
	foreach ( array_keys( $words ) as $key ) {
		if ( 	wppa_is_int( $words[$key] ) ||
				in_array( $words[$key], $wppa_void_keywords ) ||
				strlen( $words[$key] ) < 5 ) {
			unset ( $words[$key] );
		}
	}
	$result = implode( ', ', $words );
	return $result;
}

function wppa_optimize_image_file( $file ) {
	if ( ! wppa_switch( 'optimize_new' ) ) return;
	if ( function_exists( 'ewww_image_optimizer' ) ) {
		ewww_image_optimizer( $file, 4, false, false, false );
	}
}

function wppa_is_orig ( $path ) {
	$file = basename( $path );
	$file = wppa_strip_ext( $file );
	$temp = explode( '-', $file );
	if ( ! is_array( $temp ) ) return true;
	$temp = $temp[ count( $temp ) -1 ];
	$temp = explode( 'x', $temp );
	if ( ! is_array( $temp ) ) return true;
	if ( count( $temp ) != 2 ) return true;
	if ( ! wppa_is_int( $temp[0] ) ) return true;
	if ( ! wppa_is_int( $temp[1] ) ) return true;
	return false;
}

function wppa_browser_can_html5() {

	if ( ! isset( $_SERVER["HTTP_USER_AGENT"] ) ) return false;

	$is_opera 	= strpos( $_SERVER["HTTP_USER_AGENT"], 'OPR' );
	$is_ie 		= strpos( $_SERVER["HTTP_USER_AGENT"], 'Trident' );
	$is_safari 	= strpos( $_SERVER["HTTP_USER_AGENT"], 'Safari' );
	$is_firefox = strpos( $_SERVER["HTTP_USER_AGENT"], 'Firefox' );

	if ( $is_opera ) 	return true;
	if ( $is_safari ) 	return true;
	if ( $is_firefox ) 	return true;

	if ( $is_ie ) {
		$tri_pos = strpos( $_SERVER["HTTP_USER_AGENT"], 'Trident/' );
		$tri_ver = substr( $_SERVER["HTTP_USER_AGENT"], $tri_pos+8, 3 );
		if ( $tri_pos >= 6.0 ) return true; // IE 10 or later
// echo $tri_ver;
	}

	return false;
}

function wppa_get_comten_ids( $max_count = 0, $albums = array() ) {
global $wpdb;

	if ( ! $max_count ) {
		$max_count = wppa_opt( 'comten_count' );
	}

	$photo_ids = $wpdb->get_results( $wpdb->prepare( 	"SELECT `photo` FROM `".WPPA_COMMENTS."` " .
														"WHERE `status` = 'approved' " .
														"ORDER BY `timestamp` DESC LIMIT %d", 100 * $max_count ), ARRAY_A );
	$result = array();

	if ( is_array( $photo_ids ) ) {
		foreach( $photo_ids as $ph ) {
			if ( empty( $albums ) || in_array( wppa_get_photo_item( $ph['photo'], 'album' ), $albums ) || ( count( $albums ) == 1 && $albums[0] == '0' ) ) {
				if ( count( $result ) < $max_count ) {
					if ( ! in_array( $ph['photo'], $result ) ) {
						$result[] = $ph['photo'];
					}
				}
			}
		}
	}

	return $result;
}

// Retrieve a get-vareiable, sanitized and post-processed
// Return '1' if set without value, return false when value is 'nil'
function wppa_get_get( $index ) {
static $wppa_get_get_cache;

	// Found this already?
	if ( isset( $wppa_get_get_cache[$index] ) ) return $wppa_get_get_cache[$index];

	// See if set
	if ( isset( $_GET['wppa-'.$index] ) ) {			// New syntax first
		$result = $_GET['wppa-'.$index];
	}
	elseif ( isset( $_GET[$index] ) ) {				// Old syntax
		$result = $_GET[$index];
	}
	else return false;								// Not set

	if ( $result == 'nil' ) return false;			// Nil simulates not set

	if ( ! strlen( $result ) ) $result = '1';		// Set but no value

	// Sanitize
	$result = strip_tags( $result );
	if ( strpos( $result, '<?' ) !== false ) die( 'Security check failure #191' );
	if ( strpos( $result, '?>' ) !== false ) die( 'Security check failure #192' );

	// Post processing needed?
	if ( $index == 'photo' && ( ! wppa_is_int( $result ) ) ) {

		// Encrypted?
		$result = wppa_decrypt_photo( $result );

		// By name?
		$result = wppa_get_photo_id_by_name( $result, wppa_get_album_id_by_name( wppa_get_get( 'album' ) ) );

		if ( ! $result ) return false;				// Non existing photo, treat as not set
	}
	if ( $index == 'album' ) {

		// Encrypted?
		$result = wppa_decrypt_album( $result );

		if ( ! wppa_is_int( $result ) ) {
			$temp = wppa_get_album_id_by_name( $result );
			if ( wppa_is_int( $temp ) && $temp > '0' ) {
				$result = $temp;
			}
			elseif ( ! wppa_series_to_array( $result ) ) {
				$result = false;
			}
		}
	}

	// Save in cache
	$wppa_get_get_cache[$index] = $result;
	return $result;
}

function wppa_get_post( $index, $default = false ) {

	if ( isset( $_POST['wppa-'.$index] ) ) {		// New syntax first
		$result = $_POST['wppa-'.$index];
		if ( strpos( $result, '<?' ) !== false ) die( 'Security check failure #291' );
		if ( strpos( $result, '?>' ) !== false ) die( 'Security check failure #292' );
		if ( $index == 'album' ) $result = wppa_decrypt_album( $result );
		if ( $index == 'photo' ) $result = wppa_decrypt_photo( $result );
		return $result;
	}
	if ( isset( $_POST[$index] ) ) {				// Old syntax
		$result = $_POST[$index];
		if ( strpos( $result, '<?' ) !== false ) die( 'Security check failure #391' );
		if ( strpos( $result, '?>' ) !== false ) die( 'Security check failure #392' );
		if ( $index == 'album' ) $result = wppa_decrypt_album( $result );
		if ( $index == 'photo' ) $result = wppa_decrypt_photo( $result );
		return $result;
	}
	return $default;
}

function wppa_sanitize_searchstring( $str ) {

	$result = $str;
	$result = strip_tags( $result );
	$result = stripslashes( $result );
	$result = str_replace( array( "'", '"', ':', ), '', $result );
	$temp 	= explode( ',', $result );
	foreach ( array_keys( $temp ) as $key ) {
		$temp[$key] = trim( $temp[$key] );
	}
	$result = implode( ',', $temp );

	return $result;
}

// Filter for Plugin CM Tooltip Glossary
function wppa_filter_glossary( $desc ) {
static $wppa_cmt;

	// Do we need this?
	if ( wppa_switch( 'use_CMTooltipGlossary' ) && class_exists( 'CMTooltipGlossaryFrontend' ) ) {

		// Class initialized?
		if ( empty( $wppa_cmt ) ) {
			$wppa_cmt = new CMTooltipGlossaryFrontend;
		}

		// Do we already start with a <p> ?
		$start_p = ( strpos( $desc, '<p' ) === 0 );

		// remove newlines, glossary converts them to <br />
		$desc = str_replace( array( "\n", "\r", "\t" ), '', $desc );
		$desc = $wppa_cmt->cmtt_glossary_parse( $desc, true );

		// Remove <p> and </p> that CMTG added around
		if ( ! $start_p ) {
			if ( substr( $desc, 0, 3 ) == '<p>' ) {
				$desc = substr( $desc, 3 );
			}
			if ( substr( $desc, -4 ) == '</p>' ) {
				$desc = substr( $desc, 0, strlen( $desc ) - 4 );
			}
		}
	}

	return $desc;
}

// Convert file extension to lowercase
function wppa_down_ext( $file ) {
	if ( strpos( $file, '.' ) === false ) return $file;	// no . found
	$dotpos = strrpos( $file, '.' );
	$file = substr( $file, 0, $dotpos ) . strtolower( substr( $file, $dotpos ) );
	return $file;
}

// See of a photo db entry is a multimedia entry
function wppa_is_multi( $id ) {

	if ( ! $id ) return false;			// No id

	$ext = wppa_get_photo_item( $id, 'ext' );
	return ( $ext == 'xxx' );
}

function wppa_fix_poster_ext( $fileorurl, $id ) {

	$poster_ext = wppa_get_poster_ext( $id );

	// If found, replace extension to ext of existing file
	if ( $poster_ext ) {
		return str_replace( '.xxx', '.'.$poster_ext, $fileorurl );
	}

	// Not found. If audio, return audiostub file or url
	if ( wppa_has_audio( $id ) ) {

		$audiostub = wppa_opt( 'audiostub' );

		// Url ?
		if ( strpos( $fileorurl, 'http://' ) !== false || strpos( $fileorurl, 'https://' ) !== false ) {
			return WPPA_UPLOAD_URL . '/'. $audiostub;
		}

		// File
		else {
			return WPPA_UPLOAD_PATH . '/' . $audiostub;
		}
	}

	// Not found. Is Video, return as jpg
	return str_replace( '.xxx', '.jpg', $fileorurl );
}

function wppa_get_poster_ext( $id ) {
global $wppa_supported_photo_extensions;

	// Init
	$path 		= wppa_get_photo_path( $id );
	$raw_path 	= wppa_strip_ext( $path );

	// Find existing photofiles
	foreach ( $wppa_supported_photo_extensions as $ext ) {
		if ( is_file( $raw_path.'.'.$ext ) ) {
			return $ext;	// Found !
		}
	}

	// Not found.
	return false;
}

// Convert a line from a csv file to an array, taking account of embedded commas and double quotes.
// Sanitizes output so the elements will have no surrounding spaces and / or trailing linefeeds.
function wppa_explode_csv( $txt ) {

	// Make local copy
	$temp = $txt;

	// Convert latin/iso to utf-8
	$temp = utf8_encode( $temp );

	// Replace double double quotes
	$temp = str_replace( '""', '&ddquote;', $temp );

	// See if double quotes in the text
	if ( strpos( $temp, '"' ) ) {
		$temp = explode( '"', $temp );
		$i = '1';
		while ( isset( $temp[$i] ) ) {

			// Replace embedded comma
			$temp[$i] = str_replace ( ',', '&comma;', $temp[$i] );
			$i += '2';
		}

		// Convert to string again
		$temp = implode( '', $temp );
	}

	// Make to array
	$result = explode( ',', $temp );

	// Repair embedded comma's and double quotes
	$i = '0';
	while ( isset( $result[$i] ) ) {
		$result[$i] = str_replace( '&comma;', ',', $result[$i] );
		$result[$i] = str_replace( '&ddquote;', '"', $result[$i] );
		$i++;
	}

	// Sanitize array elements
	foreach( array_keys( $result ) as $key ) {
		$result[$key] = wppa_sanitize_text( $result[$key] );
	}

	// Done!
	return $result;
}

// Like wp sanitize_text_field, but also removes chars 0x00..0x07
function wppa_sanitize_text( $txt ) {
	$result = sanitize_text_field( $txt );
	$result = str_replace( array(chr(0), chr(1), chr(2), chr(3),chr(4), chr(5), chr(6), chr(7) ), '', $result );
	return $result;
}

function wppa_is_mobile() {
	$result = false;
	$detect = new wppa_mobile_detect();
	if ( $detect->isMobile() ) {
		$result = true;
	}
	return $result;
}

// Like wp_nonce_field
// To prevent duplicate id's, we externally add an id number ( e.g. album ) and internally the mocc number.
function wppa_nonce_field( $action = -1, $name = "_wpnonce", $referer = true , $echo = true, $wppa_id = '0' ) {

	$name = esc_attr( $name );
	$nonce_field = 	'<input' .
						' type="hidden"' .
						' id="' . $name . '-' . $wppa_id . '-' . wppa( 'mocc' ) . '"' .
						' name="' . $name . '"' .
						' value="' . wp_create_nonce( $action ) . '"' .
						' />';

	if ( $referer ) {
		$nonce_field .= wp_referer_field( false );
	}

	if ( $echo ) {
		echo $nonce_field;
	}

	return $nonce_field;
}

// Like convert_smilies, but directe rendered to <img> tag to avoid performance bottleneck for emoji's when ajax on firefox
function wppa_convert_smilies( $text ) {
static $smilies;

	// Initialize
	if ( ! is_array( $smilies ) ) {
		$smilies = array(	";-)" 		=> '<img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f609.png" />',
							":|" 		=> '<img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f610.png" />',
							":x" 		=> '<img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f621.png" />',
							":twisted:" => '<img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f608.png" />',
							":shock:" 	=> '<img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f62f.png" />',
							":razz:" 	=> '<img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f61b.png" />',
							":oops:" 	=> '<img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f633.png" />',
							":o" 		=> '<img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f62e.png" />',
							":lol:" 	=> '<img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f606.png" />',
							":idea:" 	=> '<img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f4a1.png" />',
							":grin:" 	=> '<img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f600.png" />',
							":evil:" 	=> '<img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f47f.png" />',
							":cry:" 	=> '<img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f625.png" />',
							":cool:" 	=> '<img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f60e.png" />',
							":arrow:" 	=> '<img class="emoji" draggable="false" alt="?" src="http://s.w.org/images/core/emoji/72x72/27a1.png" />',
							":???:" 	=> '<img class="emoji" draggable="false" alt="??" src="http://s.w.org/images/core/emoji/72x72/1f615.png" />',
							":?:" 		=> '<img class="emoji" draggable="false" alt="?" src="http://s.w.org/images/core/emoji/72x72/2753.png" />',
							":!:" 		=> '<img class="emoji" draggable="false" alt="?" src="http://s.w.org/images/core/emoji/72x72/2757.png" />'
		);
	}

	// Perform
	$result = $text;
	foreach ( array_keys( $smilies ) as $key ) {
		$result = str_replace( $key, $smilies[$key], $result );
	}

	// Convert non-emoji's
	$result = convert_smilies( $result );

	// SSL?
	if ( is_ssl() ) {
		$result = str_replace( 'http://', 'https://', $result );
	}

	// Done
	return $result;
}

function wppa_toggle_alt() {
	if ( wppa( 'alt' ) == 'alt' ) {
		wppa( 'alt', 'even' );
	}
	else {
		wppa( 'alt', 'alt' );
	}
}

function wppa_is_virtual() {

	if ( wppa( 'is_topten' ) ) return true;
	if ( wppa( 'is_lasten' ) ) return true;
	if ( wppa( 'is_featen' ) ) return true;
	if ( wppa( 'is_comten' ) ) return true;
	if ( wppa( 'is_tag' ) ) return true;
	if ( wppa( 'is_related' ) ) return true;
	if ( wppa( 'is_upldr' ) ) return true;
	if ( wppa( 'is_cat' ) ) return true;
	if ( wppa( 'is_supersearch' ) ) return true;
	if ( wppa( 'src' ) ) return true;
	if ( wppa( 'supersearch' ) ) return true;
	if ( wppa( 'searchstring' ) ) return true;
	if ( wppa( 'calendar' ) ) return true;

	return false;
}

function wppa_too_old_for_cloud( $id ) {

	$thumb = wppa_cache_thumb( $id );

	$is_old = wppa_cdn( 'admin' ) && wppa_opt( 'max_cloud_life' ) && ( time() > ( $thumb['timestamp'] + wppa_opt( 'max_cloud_life' ) ) );

	return $is_old;
}

// Test if we are in a widget
// Returns wppa widget type if in a wppa widget
// Else: return true if in a widget, false if not in a widget
function wppa_in_widget() {

	if ( wppa( 'in_widget' ) ) {
		return wppa( 'in_widget' );
	}
	$stack = serialize( debug_backtrace() );
	return ( strpos( $stack, '"WP_Widget"' ) !== false );
}

function wppa_bump_mocc() {
	wppa( 'mocc', wppa( 'mocc' ) + 1 );
}

// This is a nice simple function
function wppa_out( $txt ) {
global $wppa;

	$wppa['out'] .= $txt;
	return;
}

function wppa_exit() {
	wppa_session_end();
	exit;
}

function wppa_sanitize_custom_field( $txt ) {

	if ( wppa_switch( 'allow_html_custom' ) ) {
		$result = wppa_strip_tags( $txt, 'script&style' );
	}
	else {
		$result = strip_tags( $txt );
	}
	return $result;
}

// Get the minimum number of photos to display ( photocount treshold if not virtuel )
function wppa_get_mincount() {

	if ( wppa_is_virtual() ) return '0';

	return wppa_opt( 'min_thumbs' );
}

// See if a photo is in our admins choice zip
function  wppa_is_photo_in_zip( $id ) {
global $wpdb;

	// Verify existance of zips dir
	$zipsdir = WPPA_UPLOAD_PATH.'/zips/';
	if ( ! is_dir( $zipsdir ) ) return false;

	// Compose the users zip filename
	$zipfile = $zipsdir.wppa_get_user().'.zip';

	// Check file existance
	if ( ! is_file( $zipfile ) ) {
		return false;
	}

	// Find the photo data
	$data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `".WPPA_PHOTOS."` WHERE `id` = %s", $id ), ARRAY_A );
	$photo_file = wppa_fix_poster_ext( $data['filename'], $id );

	// Open zip
	$wppa_zip = new ZipArchive;
	$wppa_zip->open( $zipfile );
	if ( ! $wppa_zip ) {

		// Failed to open zip
		return false;
	}

	// Look photo up in zip
	for( $i = 0; $i < $wppa_zip->numFiles; $i++ ) {
		$stat = $wppa_zip->statIndex( $i );
		$file_name = $stat['name'];
		if ( $file_name == $photo_file ) {

			// Found
			$wppa_zip->close();
			return true;
		}
	}

	// Not found
	$wppa_zip->close();
	return false;
}

// Convert querystring to get and request vars
function wppa_convert_uri_to_get( $uri ) {

	// Make local copy of argument
	$temp = $uri;

	// See if a ? is in the string
	if ( strpos( $uri, '?' ) !== false ) {

		// Trim up to and including ?
		$temp = substr( $uri, strpos( $uri, '?' ) + 1 );
	}

	// explode uri
	$arr = explode( '&', $temp );

	// If args exist, process them
	if ( !empty( $arr ) ) {
		foreach( $arr as $item ) {
			$arg = explode( '=', $item );
			if ( ! isset( $arg[1] ) ) {
				$arg[1] = null;
			}
			else {
				$arg[1] = urldecode( $arg[1] );
			}
			$_GET[$arg[0]] = $arg[1];
			$_REQUEST[$arg[0]] = $arg[1];
//			wppa_log('dbg',$item);
		}
	}
}