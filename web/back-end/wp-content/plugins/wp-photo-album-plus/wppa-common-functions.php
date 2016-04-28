<?php
/* wppa-common-functions.php
*
* Functions used in admin and in themes
* Version 6.4.09
*
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

// Initialize globals and option settings
function wppa_initialize_runtime( $force = false ) {
global $wppa;
global $wppa_opt;
global $wppa_revno;
global $wppa_api_version;
global $wpdb;
global $wppa_initruntimetime;
global $wppa_defaults;

	$wppa_initruntimetime = - microtime( true );

	if ( $force ) {
		$wppa = false; 					// destroy existing arrays
		$wppa_opt = false;
		delete_option( 'wppa_cached_options' );
	}

	if ( is_array( $wppa ) && ! $force ) {
		return; 	// Done already
	}

	if ( ! is_array( $wppa ) ) {
		wppa_reset_occurrance();
	}

	// Get the cache version of all settings
	$wppa_opt = get_option( 'wppa_cached_options', false );
	
	// Check for validity
	if ( md5( serialize( $wppa_opt ) ) != get_option( 'wppa_md5_options', 'nil' ) ) {
		$wppa_opt = false;
		
		// Report fix only if not intentional
		if ( ! $force ) {
			wppa_log( 'Fix', 'Option cache' );
		}
	}

	// Rebuild cached options
	if ( ! is_array( $wppa_opt ) ) {
		wppa_set_defaults();
		$wppa_opt = $wppa_defaults;
		foreach ( array_keys( $wppa_opt ) as $option ) {
			$optval = get_option( $option, 'nil' );
			if ( $optval !== 'nil' ) {
				$wppa_opt[$option] = $optval;
			}
		}
		update_option( 'wppa_cached_options', $wppa_opt );
		update_option( 'wppa_md5_options', md5( serialize( $wppa_opt ) ) );
	}

	if ( isset( $_GET['debug'] ) && wppa_switch( 'allow_debug' ) ) {
		$key = $_GET['debug'] ? $_GET['debug'] : E_ALL;
		wppa( 'debug', $key );
	}

	// Delete obsolete spam
	$spammaxage = wppa_opt( 'spam_maxage' );
	if ( $spammaxage != 'none' ) {
		$time = time();
		$obsolete = $time - $spammaxage;
		$iret = $wpdb->query( $wpdb->prepare( "DELETE FROM `".WPPA_COMMENTS."` WHERE `status` = 'spam' AND `timestamp` < %s", $obsolete ) );
		if ( $iret ) wppa_update_option( 'wppa_spam_auto_delcount', get_option( 'wppa_spam_auto_delcount', '0' ) + $iret );
	}

	// Create an album if required
	if ( wppa_switch( 'grant_an_album' )
		&& wppa_switch( 'owner_only' )
		&& is_user_logged_in()
		&& ( current_user_can( 'wppa_upload' ) || wppa_switch( 'user_upload_on' ) ) ) {
			$owner = wppa_get_user( 'login' );
			$user = wppa_get_user( wppa_opt( 'grant_name' ) );
			$albs = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_ALBUMS."` WHERE `owner` = %s", $owner ) );
			if ( ! $albs ) {	// make an album for this user
				$name = $user;
				if ( is_admin() ) {
					$desc = __( 'Default photo album for' , 'wp-photo-album-plus').' '.$user;
				}
				else {
					$desc = __( 'Default photo album for' , 'wp-photo-album-plus').' '.$user;
				}
				$parent = wppa_opt( 'grant_parent' );
				$id = wppa_create_album_entry( array ( 'name' => $name, 'description' => $desc, 'a_parent' => $parent ) );
				wppa_flush_treecounts( $parent );
				wppa_index_add( 'album', $id );
			}
	}

	$wppa_initruntimetime += microtime( true );
}

function wppa_reset_occurrance() {
global $wppa;
global $wppa_revno;
global $wppa_api_version;
global $thumbs;

	$thumbs = false;

	wppa_cache_thumb( 'invalidate' );
	wppa_cache_album( 'invalidate' );

	$mocc = isset( $wppa['mocc'] ) ? $wppa['mocc'] : '0';
	$occ  = isset( $wppa['occur'] ) ? $wppa['occur'] : '0';
	$wocc = isset( $wppa['widget_occur'] ) ? $wppa['widget_occur'] : '0';
	$rend = isset( $wppa['rendering_enabled'] ) ? $wppa['rendering_enabled'] : false;
	$debug = isset( $wppa['debug'] ) ? $wppa['debug'] : false;

	$wppa = array (
		'debug' 					=> $debug,
		'revno' 					=> $wppa_revno,				// set in wppa.php
		'api_version' 				=> $wppa_api_version,		// set in wppa.php
		'fullsize' 					=> '',
		'enlarge' 					=> false,
		'occur' 					=> $occ,
		'mocc' 						=> $mocc,
		'widget_occur' 				=> $wocc,
		'in_widget' 				=> false,
		'is_cover' 					=> '0',
		'is_slide' 					=> '0',
		'is_slideonly' 				=> '0',
		'is_slideonlyf'				=> '0',
		'is_filmonly'				=> '0',
		'film_on' 					=> '0',
		'browse_on' 				=> '0',
		'name_on' 					=> '0',
		'desc_on' 					=> '0',
		'numbar_on' 				=> '0',
		'single_photo' 				=> '',
		'is_mphoto' 				=> '0',
		'start_album' 				=> '',
		'align' 					=> '',
		'src' 						=> false,
		'portrait_only' 			=> false,
		'in_widget_linkurl' 		=> '',
		'in_widget_linktitle' 		=> '',
		'in_widget_timeout' 		=> '0',
		'ss_widget_valign' 			=> '',
		'album_count' 				=> '0',
		'thumb_count' 				=> '0',
		'out' 						=> '',
		'auto_colwidth' 			=> false,
		'permalink' 				=> '',
		'rendering_enabled' 		=> $rend,
		'tabcount' 					=> '0',
		'comment_id' 				=> '',
		'comment_photo' 			=> '0',
		'comment_user' 				=> '',
		'comment_email' 			=> '',
		'comment_text' 				=> '',
		'no_default' 				=> false,
		'in_widget_frame_height' 	=> '',
		'in_widget_frame_width'		=> '',
//		'user_uploaded'				=> false,
		'current_album'				=> '0',
		'searchstring'				=> wppa_test_for_search(),
		'searchresults'				=> '',
		'any'						=> false,
		'ajax'						=> false,
		'error'						=> false,
		'iptc'						=> false,
		'exif'						=> false,
		'is_topten'					=> false,
		'topten_count'				=> '0',
		'is_lasten'					=> false,
		'lasten_count'				=> '0',
		'is_featen'					=> false,
		'featen_count'				=> '0',
		'start_photo'				=> '0',
		'is_single'					=> false,
		'is_landing'				=> '0',
		'is_comten'					=> false,
		'comten_count'				=> '0',
		'is_tag'					=> false,
		'photos_only'				=> false,
		'albums_only'				=> false,
		'medals_only' 				=> false,
		'page'						=> '',
		'geo'						=> '',
		'continue'					=> '',
		'is_upload'					=> false,
		'ajax_import_files'			=> false,
		'ajax_import_files_done'	=> false,
		'ajax_import_files_error' 	=> '',
		'last_albums'				=> false,
		'last_albums_parent'		=> '0',
		'is_multitagbox' 			=> false,
		'is_tagcloudbox' 			=> false,
		'taglist' 					=> '',
		'tagcols'					=> '2',
		'is_related'				=> false,
		'related_count'				=> '0',
		'is_owner'					=> '',
		'is_upldr'					=> '',
		'no_esc'					=> false,
		'front_edit'				=> false,
		'is_autopage'				=> false,
		'is_cat'					=> false,
		'bestof' 					=> false,
		'is_subsearch' 				=> false,
		'is_rootsearch' 			=> false,
		'is_superviewbox' 			=> false,
		'is_searchbox'				=> false,
		'may_sub'					=> false,
		'may_root'					=> false,
		'links_no_page' 			=> array( 'none', 'file', 'lightbox', 'lightboxsingle', 'fullpopup' ),
		'shortcode_content' 		=> '',
		'is_remote' 				=> false,
		'is_supersearch' 			=> false,
		'supersearch' 				=> '',
		'is_mobile' 				=> wppa_is_mobile(),
		'rel' 						=> get_option( 'wppa_lightbox_name' ) == 'wppa' ? 'data-rel' : 'rel',
		'lbtitle' 					=> get_option( 'wppa_lightbox_name' ) == 'wppa' ? 'data-lbtitle' : 'title',
		'alt'						=> 'even',
		'is_wppa_tree' 				=> false,
		'is_calendar' 				=> false,
		'calendar' 					=> '',
		'caldate' 					=> '',
		'calendarall' 				=> false,
		'reverse' 					=> false,
		'current_photo' 			=> false,
		'is_stereobox' 				=> false,
		'npages' 					=> '',
		'curpage'					=> '',
		'ss_pag' 					=> false,
		'slideframewidth' 			=> '',
		'slideframeheight' 			=> '',
		'ajax_import_files_error' 	=> '',
		'src_script' 				=> '',
		'is_url' 					=> false,
		'is_inverse' 				=> false,
		'coverphoto_pos' 			=> '',
		'forceroot' 				=> '',
		'landingpage' 				=> '',
	);
}

function wppa_get_randseed( $type = '' ) {
global $wppa_session;
static $volatile_randseed;
static $randseed_modified;

	// This randseed is for the page only
	if ( $type == 'page' ) {
		if ( $volatile_randseed ) {
			$randseed = $volatile_randseed;
		}
		else {
			$volatile_randseed = time() % 7487;
			$randseed = $volatile_randseed;
		}
	}

	// This randseed survives pageloads up to the duration of the session ( usually 1 hour )
	elseif ( $type == 'session' ) {
		$randseed = $wppa_session['id']; //session_randseed'];
	}

	// If the album spec in the querystring differs from the previous, or there is no album arg in the querystring,
	// the random seed is renewed to improve the random behaviour for non-critical operation
	else {
		if ( isset( $wppa_session['randseed'] ) && ! $randseed_modified ) {
			$old_album = isset( $wppa_session['albumspec'] ) ? $wppa_session['albumspec'] : false;
			$new_album = wppa_get_get( 'album' );
			if ( $new_album === false || ( $old_album && ( $old_album != $new_album ) ) ) {
				unset( $wppa_session['randseed'] );	// Forget randseed
			}
		}

		if ( isset( $wppa_session['randseed'] ) ) {
			$randseed = $wppa_session['randseed'];
		}
		else {
			$randseed = time() % 4721;
			$wppa_session['randseed'] = $randseed;

			// Save old album for later
			$wppa_session['albumspec'] = wppa_get_get( 'album' );

			$randseed_modified = true;
		}
	}

	wppa_save_session();

	return $randseed;
}

function wppa_phpinfo( $key = -1 ) {

	echo '<div id="phpinfo" style="width:600px; margin:auto;" >';

		ob_start();
		if ( wppa_switch( 'allow_debug' ) ) phpinfo( -1 ); else phpinfo( 4 );
		$php = ob_get_clean();
		$php = preg_replace( 	array	( 	'@<!DOCTYPE.*?>@siu',
											'@<html.*?>@siu',
											'@</html.*?>@siu',
											'@<head[^>]*?>.*?</head>@siu',
											'@<body.*?>@siu',
											'@</body.*?>@siu',
											'@cellpadding=".*?"@siu',
											'@border=".*?"@siu',
											'@width=".*?"@siu',
											'@name=".*?"@siu',
											'@<font.*?>@siu',
											'@</font.*?>@siu'
										 ),
										'',
										$php );

		$php = str_replace( 'Features','Features</td><td>', $php );

		echo $php;

	echo '</div>';
}

function wppa_errorlog() {
	$filename = WPPA_CONTENT_PATH.'/wppa-depot/admin/error.log';
	$file = @ fopen( $filename, 'r' );
	if ( ! $file ) return;	// No logfile
	wppa_dbg_msg( 'Start errorlog' );
		$size 		= filesize( $filename );
		$data 		= fread( $file, $size );
		$messages 	= explode( "\n", $data );
		$count 		= count( $messages );
		$idx 		= $count - '2';

		echo str_replace( "\n", '<br />', $data );
//		while ( $idx >= '0' ) {
//			$msg 	= $messages[$idx];
//			$msg 	= htmlspecialchars( strip_tags( $msg ) );	// Security fix
//			echo $msg.'<br />';
//			$idx--;
//		}
	wppa_dbg_msg( 'dbg', 'End errorlog' );
	fclose( $file );
}

// get the url to the plugins image directory
function wppa_get_imgdir( $file = '', $rel = false ) {

	$result = WPPA_URL.'/images/';
	if ( is_ssl() ) $result = str_replace( 'http://', 'https://', $result );
	$result .= $file;

	$result = wppa_make_relative( $result, $rel );
	return $result;
}

function wppa_make_relative( $url, $rel = '' ) {

	// Init
	$result = $url;

	// Can not use wppa_opt(). $wppa_opt is not initialized when called from wppa_set_defaults
	if ( $rel != 'abs' ) {	// Not if absulute is explicitly requested
		if ( get_option( 'wppa_relative_urls' ) == 'yes' || $rel == 'rel' ) {
			if ( isset( $_ENV['HTTP_HOST'] ) ) {
				if ( is_ssl() ) {
					$result = str_replace( 'https://' . $_ENV['HTTP_HOST'], '', $result );
				}
				else {
					$result = str_replace( 'http://' . $_ENV['HTTP_HOST'], '', $result );
				}
			}
		}
	}

	return $result;
}

function wppa_get_wppa_url() {

	$result = WPPA_URL;
	if ( is_ssl() ) $result = str_replace( 'http://', 'https://', $result );
	return $result;
}

// get album order
function wppa_get_album_order( $parent = '0' ) {
global $wppa;

	// Init
    $result = '';

	// Album given ?
	if ( $parent > '0' ) {
		$album = wppa_cache_album( $parent );
		$order = $album['suba_order_by'];
	}
	else {
		$order = '0';
	}
	if ( ! $order ) $order = wppa_opt( 'list_albums_by' );

	switch ( $order ) {
		case '':
		case '0':
			$result = '';
			break;
		case '1':
			$result = 'ORDER BY a_order';
			break;
		case '-1':
			$result = 'ORDER BY a_order DESC';
			break;
		case '2':
			$result = 'ORDER BY name';
			break;
		case '-2':
			$result = 'ORDER BY name DESC';
			break;
		case '3':
			$result = 'ORDER BY RAND( '.wppa_get_randseed().' )';
			break;
		case '5':
			$result = 'ORDER BY timestamp';
			break;
		case '-5':
			$result = 'ORDER BY timestamp DESC';
			break;
		default:
			wppa_dbg_msg( 'Unimplemented album order: '.$order, 'red' );
	}

	return $result;
}

// get photo order
function wppa_get_photo_order( $id = '0', $no_random = false ) {
global $wpdb;
global $wppa;

	if ( $id == '0' ) $order = '0';
	else {
		$order = $wpdb->get_var( $wpdb->prepare( "SELECT `p_order_by` FROM `" . WPPA_ALBUMS . "` WHERE `id` = %s", $id ) );
		wppa_dbg_q( 'Q201' );
	}
    if ( ! $order ) $order = wppa_opt( 'list_photos_by' );

    switch ( $order )
    {
	case '':
	case '0':
		$result = '';
		break;
    case '1':
        $result = 'ORDER BY p_order';
        break;
	case '-1':
		$result = 'ORDER BY p_order DESC';
		break;
    case '2':
        $result = 'ORDER BY name';
        break;
    case '-2':
        $result = 'ORDER BY name DESC';
        break;
    case '3':
		if ( $no_random ) $result = 'ORDER BY name';
        else $result = 'ORDER BY RAND( '.wppa_get_randseed().' )';
        break;
    case '-3':
		if ( $no_random ) $result = 'ORDER BY name DESC';
        else $result = 'ORDER BY RAND( '.wppa_get_randseed().' ) DESC';
        break;
	case '4':
		$result = 'ORDER BY mean_rating';
		break;
	case '-4':
		$result = 'ORDER BY mean_rating DESC';
		break;
	case '5':
		$result = 'ORDER BY timestamp';
		break;
	case '-5':
		$result = 'ORDER BY timestamp DESC';
		break;
	case '6':
		$result = 'ORDER BY rating_count';
		break;
	case '-6':
		$result = 'ORDER BY rating_count DESC';
		break;
	case '7':
		$result = 'ORDER BY exifdtm';
		break;
	case '-7':
		$result = 'ORDER BY exifdtm DESC';
		break;

    default:
        wppa_dbg_msg( 'Unimplemented photo order: '.$order, 'red' );
		$result = '';
    }

    return $result;
}


// See if an album is another albums ancestor
function wppa_is_ancestor( $anc, $xchild ) {

	$child = $xchild;
	if ( is_numeric( $anc ) && is_numeric( $child ) ) {
		$parent = wppa_get_parentalbumid( $child );
		while ( $parent > '0' ) {
			if ( $anc == $parent ) return true;
			$child = $parent;
			$parent = wppa_get_parentalbumid( $child );
		}
	}
	return false;
}



function wppa_get_album_id( $name = '', $parent = false ) {
global $wpdb;

	if ( $name == '' ) return '';
    $name = stripslashes( $name );

    $albs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `" . WPPA_ALBUMS . "` WHERE `name` = %s", $name ), ARRAY_A );
	wppa_dbg_q( 'Q205' );

    if ( empty( $albs ) ) {
		return '';
	}
	else {
		if ( $parent === false ) {
			return $albs['0']['id'];
		}
		else {
			foreach ( $albs as $alb ) {
				if ( $alb['a_parent'] == $parent ) {
					return $alb['id'];
				}
			}
		}
	}
}

// Check if an image is more landscape than the width/height ratio set in Table I item 2 and 3
function wppa_is_wider( $x, $y, $refx = '', $refy = '' ) {

	if ( $refx == '' ) {
		$ratioref = wppa_opt( 'fullsize' ) / wppa_opt( 'maxheight' );
	}
	else {
		$ratioref = $refx/$refy;
	}
	$ratio = $x / $y;
	return ( $ratio > $ratioref );
}

// qtrans hook to see if qtrans is installed
function wppa_qtrans_enabled() {
	return ( function_exists( 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage' ) );
}

// Output debug message
function wppa_dbg_msg( $txt = '', $color = 'blue', $force = false, $return = false ) {

	if ( wppa( 'debug' ) || $force || ( is_admin() && WPPA_DEBUG ) || ( WPPA_DEBUG && $color == 'red' ) ) {

		$result = 	'<span style="color:' . $color . ';" >' .
						'<small>' .
							'[WPPA+ dbg msg: ' . $txt . ']' .
							'<br />' .
						'</small>' .
					'</span>';

		if ( $return ) {
			return $result;
		}
		else {
			echo $result;
		}
	}
}

// Append debug value to link
function wppa_dbg_url( $link, $js = '' ) {

	$result = $link;

	if ( wppa( 'debug' ) ) {
		if ( strpos( $result, '?' ) ) {
			if ( $js == 'js' ) $result .= '&';
			else $result .= '&amp;';
		}
		else $result .= '?';
		$result .= 'debug=' . wppa( 'debug' );
	}

	return $result;
}

function wppa_get_time_since( $oldtime ) {

	$newtime = time();
	$diff = $newtime - $oldtime;
	if ( $diff < 60 ) {
		return sprintf( _n( '%d second', '%d seconds', $diff, 'wp-photo-album-plus' ), $diff );
	}
	$diff = floor( $diff / 60 );
	if ( $diff < 60 ) {
		return sprintf( _n( '%d minute', '%d minutes', $diff, 'wp-photo-album-plus' ), $diff );
	}
	$diff = floor( $diff / 60 );
	if ( $diff < 24 ) {
		return sprintf( _n( '%d hour', '%d hours', $diff, 'wp-photo-album-plus' ), $diff );
	}
	$diff = floor( $diff / 24 );
	if ( $diff < 7 ) {
		return sprintf( _n( '%d day', '%d days', $diff, 'wp-photo-album-plus' ), $diff );
	}
	elseif ( $diff < 31 ) {
		$t = floor( $diff / 7 );
		return sprintf( _n( '%d week', '%d weeks', $t, 'wp-photo-album-plus' ), $t );
	}
	$diff = floor( $diff / 30.4375 );
	if ( $diff < 12 ) {
		return sprintf( _n( '%d month', '%d months', $diff, 'wp-photo-album-plus' ), $diff );
	}
	$diff = floor( $diff / 12 );
	return sprintf( _n( '%d year', '%d years', $diff, 'wp-photo-album-plus' ), $diff );

}

// See if an album or any album is accessable for the current user
function wppa_have_access( $alb = '0' ) {
global $wpdb;
global $current_user;

//	if ( !$alb ) $alb = 'any'; //return false;

	// See if there is any album accessable
	if ( ! $alb ) { // == 'any' ) {

		// Administrator has always access OR If all albums are public
		if ( wppa_user_is( 'administrator' ) || ! wppa_switch( 'owner_only' ) ) {
			$albs = $wpdb->get_results( "SELECT `id` FROM `".WPPA_ALBUMS."`" );
			wppa_dbg_q( 'Q209' );
			if ( $albs ) return true;
			else return false;	// No albums in system
		}

		// Any --- public --- albums?
		$albs = $wpdb->get_results( "SELECT `id` FROM `".WPPA_ALBUMS."` WHERE `owner` = '--- public ---'" );
		wppa_dbg_q( 'Q210' );
		if ( $albs ) return true;

		// Any logged out created albums? ( owner = ip )
		$albs = $wpdb->get_results( "SELECT `owner` FROM `".WPPA_ALBUMS."`", ARRAY_A );
		if ( $albs ) foreach ( $albs as $a ) {
			if ( wppa_is_int( str_replace( '.', '', $a['owner'] ) ) ) return true;
		}

		// Any albums owned by this user?
		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			$user = $current_user->user_login;
			$any_albs = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_ALBUMS."` WHERE `owner` = %s", $user ) );
			wppa_dbg_q( 'Q211' );
			if ( $any_albs ) return true;
			else return false;	// No albums for user accessable
		}
	}

	// See for given album data array or album number
	else {

		// Administrator has always access
		if ( wppa_user_is( 'administrator' ) ) return true;	// Do NOT change this into 'wppa_admin', it will enable access to all albums at backend while owners only

		// If all albums are public
		if ( ! wppa_switch( 'owner_only' ) ) return true;

		// Find the owner
		$owner = '';
		if ( is_array( $alb ) ) {
			$owner = $alb['owner'];
		}
		elseif ( is_numeric( $alb ) ) {
			$owner = $wpdb->get_var( $wpdb->prepare( "SELECT `owner` FROM `".WPPA_ALBUMS."` WHERE `id` = %s", $alb ) );
			wppa_dbg_q( 'Q212' );
		}

		// -- public --- ?
		if ( $owner == '--- public ---' ) return true;
		if ( wppa_is_int( str_replace( '.', '', $owner ) ) ) return true;

		// Find the user
		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			if ( $current_user->user_login == $owner ) return true;
		}
	}
	return false;
}

// Make the display and thumbnails from a given source or upload temp image file.
// The id and extension must be supplied.
function wppa_make_the_photo_files( $file, $id, $ext ) {
global $wpdb;

	$thumb = wppa_cache_thumb( $id );

	$src_size = @getimagesize( $file, $info );

	// If the given file is an image file, process it
	if ( $src_size ) {

		// Find output path photo file
		$newimage = wppa_get_photo_path( $id );
		if ( $ext ) {
			$newimage = wppa_strip_ext( $newimage ) . '.' . strtolower( $ext );
		}

		// If Resize on upload is checked
		if ( wppa_switch( 'resize_on_upload' ) ) {

			// Picture sizes
			$src_width 	= $src_size[0];

			// Temp convert to logical width if stereo
			if ( $thumb['stereo'] ) {
				$src_width /= 2;
			}
			$src_height = $src_size[1];

			// Max sizes
			if ( wppa_opt( 'resize_to' ) == '0' ) {	// from fullsize
				$max_width 	= wppa_opt( 'fullsize' );
				$max_height = wppa_opt( 'maxheight' );
			}
			else {										// from selection
				$screen = explode( 'x', wppa_opt( 'resize_to' ) );
				$max_width 	= $screen[0];
				$max_height = $screen[1];
			}

			// If orientation needs +/- 90 deg rotation, swap max x and max y
			$ori = wppa_get_exif_orientation( $file );
			if ( $ori >= 5 && $ori <= 8 ) {
				$t = $max_width;
				$max_width = $max_height;
				$max_height = $t;
			}

			// Is source more landscape or more portrait than max window
			if ( $src_width/$src_height > $max_width/$max_height ) {	// focus on width
				$focus = 'W';
				$need_downsize = ( $src_width > $max_width );
			}
			else {														// focus on height
				$focus = 'H';
				$need_downsize = ( $src_height > $max_height );
			}

			// Convert back to physical size
			if ( $thumb['stereo'] ) {
				$src_width *= 2;
			}

			// Downsize required ?
			if ( $need_downsize ) {

				// Find mime type
				$mime = $src_size[2];

				// Create the source image
				switch ( $mime ) {	// mime type
					case 1: // gif
						$temp = @ imagecreatefromgif( $file );
						if ( $temp ) {
							$src = imagecreatetruecolor( $src_width, $src_height );
							imagecopy( $src, $temp, 0, 0, 0, 0, $src_width, $src_height );
							imagedestroy( $temp );
						}
						else $src = false;
						break;
					case 2:	// jpeg
						if ( ! function_exists( 'imagecreatefromjpeg' ) ) {
							wppa_log( 'Error', 'Function imagecreatefromjpeg does not exist.' );
						}
						$src = @ imagecreatefromjpeg( $file );
						break;
					case 3:	// png
						$src = @ imagecreatefrompng( $file );
						break;
				}

				if ( ! $src ) {
					wppa_log( 'Error', 'Image file '.$file.' is corrupt while downsizing photo' );
					return false;
				}

				// Create the ( empty ) destination image
				if ( $focus == 'W') {
					if ( $thumb['stereo'] ) $max_width *= 2;
					$dst_width 	= $max_width;
					$dst_height = round( $max_width * $src_height / $src_width );
				}
				else {
					$dst_height = $max_height;
					$dst_width = round( $max_height * $src_width / $src_height );
				}
				$dst = imagecreatetruecolor( $dst_width, $dst_height );

				// If Png, save transparancy
				if ( $mime == 3 ) {
					imagealphablending( $dst, false );
					imagesavealpha( $dst, true );
				}

				// Do the copy
				imagecopyresampled( $dst, $src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height );

				// Remove source image
				imagedestroy( $src );

				// Save the photo
				switch ( $mime ) {	// mime type
					case 1:
						imagegif( $dst, $newimage );
						break;
					case 2:
						imagejpeg( $dst, $newimage, wppa_opt( 'jpeg_quality' ) );
						break;
					case 3:
						imagepng( $dst, $newimage, 6 );
						break;
				}

				// Remove destination image
				imagedestroy( $dst );
			}
			else {	// No downsize needed, picture is small enough
				copy( $file, $newimage );
			}
		}	// No resize on upload checked
		else {
			copy( $file, $newimage );
		}

		// File successfully created ?
		if ( is_file ( $newimage ) ) {

			// Optimize file
			wppa_optimize_image_file( $newimage );

//			// Create thumbnail...
//			wppa_create_thumbnail( $id );
		}
		else {
			if ( is_admin() ) wppa_error_message( __( 'ERROR: Resized or copied image could not be created.' , 'wp-photo-album-plus') );
			else wppa_alert( __( 'ERROR: Resized or copied image could not be created.', 'wp-photo-album-plus') );
			return false;
		}

		// Process the iptc data
		wppa_import_iptc( $id, $info );

		// Process the exif data
		wppa_import_exif( $id, $file );

		// GPS
		wppa_get_coordinates( $file, $id );

		// Set ( update ) exif date-time if available
		$exdt = wppa_get_exif_datetime( $file );
		if ( $exdt ) {
			wppa_update_photo( array( 'id' => $id, 'exifdtm' => $exdt ) );
		}

		// Create thumbnail...
		wppa_create_thumbnail( $id );

		// Check orientation
		wppa_orientate_image( $id, wppa_get_exif_orientation( $file ) );

		// Compute and save sizes
		wppa_get_photox( $id, 'force' );

		// Show progression
		if ( is_admin() && ! wppa( 'ajax' ) ) echo( '.' );

		// Update CDN
		$cdn = wppa_cdn( 'admin' );
		if ( $cdn ) {
			switch ( $cdn ) {
				case 'cloudinary':
					wppa_upload_to_cloudinary( $id );
					break;
				default:
					wppa_dbg_msg( 'Missing upload instructions for '.$cdn, 'red', 'force' );
			}
		}

		// Create stereo images
		wppa_create_stereo_images( $id );

		// Clear (super)cache
		wppa_clear_cache();
		return true;
	}
	else {
		if ( is_admin() ) wppa_error_message( sprintf( __( 'ERROR: File %s is not a valid picture file.' , 'wp-photo-album-plus'), $file ) );
		else wppa_alert( sprintf( __( 'ERROR: File %s is not a valid picture file.', 'wp-photo-album-plus'), $file ) );
		return false;
	}
}

// See if this image is the default cover image
function wppa_check_coverimage( $id ) {
	if ( wppa_opt( 'default_coverimage_name' ) ) { 	// Feature enabled
		$name = wppa_strip_ext( wppa_get_photo_item( $id, 'filename' ) );
		$dflt = wppa_strip_ext( wppa_opt( 'default_coverimage_name' ) );
		if ( ! strcasecmp( $name, $dflt ) ) {	// Match
			wppa_update_album( array( 	'id'=> wppa_get_photo_item( $id, 'album' ),
										'main_photo' => $id ) );
		}
	}
}

// Get the max size, rounded up to a multiple of 25 px, of all the possible small images
// in order to create the thumbnail file big enough but not too big.
function wppa_get_minisize() {

	// Init
	$result = '100';

	// Thumbnail used / sizes found for...
	$things = array( 	'wppa_thumbsize',
						'wppa_thumbsize_alt',
						'wppa_topten_size',
						'wppa_comten_size',
						'wppa_thumbnail_widget_size',
						'wppa_lasten_size',
						'wppa_album_widget_size',
						'wppa_featen_size',
						'wppa_popupsize',
						'wppa_smallsize',
						'wppa_film_thumbsize',
						 );

	// Find the max
	foreach ( $things as $thing ) {
		$tmp = wppa_opt( $thing );
		if ( is_numeric( $tmp ) && $tmp > $result ) $result = $tmp;
	}

	// Optionally correct for 'size=height' for album cover images
	$temp = wppa_opt( 'smallsize' );
	if ( wppa_switch( 'coversize_is_height' ) ) {
		$temp = round( $temp * 4 / 3 );		// assume aspectratio 4:3
	}
	if ( is_numeric( $temp ) && $temp > $result ) $result = $temp;

	// Round up to x * 25, so not a small change results in remake
	$result = ceil( $result / 25 ) * 25;

	// Done
	return $result;
}

// Create thubnail
function wppa_create_thumbnail( $id, $use_source = true ) {

	// Find file to make thumbnail from
	$source_path = wppa_fix_poster_ext( wppa_get_source_path( $id ), $id );

	// Use source if requested and available
	if ( $use_source ) {

		if ( ! wppa_switch( 'watermark_thumbs' ) && is_file( $source_path ) ) {
			$file = $source_path;										// Use sourcefile
		}
		else {
			$file = wppa_fix_poster_ext( wppa_get_photo_path( $id ), $id );	// Use photofile
		}

		// Non standard orientation files: never use source
		$orient = wppa_get_exif_orientation( $file );
		if ( $orient > '1' ) {
			$file = wppa_fix_poster_ext( wppa_get_photo_path( $id ), $id );	// Use photofile
		}
	}
	else {
		$file = wppa_fix_poster_ext( wppa_get_photo_path( $id ), $id );	// Use photofile
	}

	// Max side
	$max_side = wppa_get_minisize();

	// Check file
	if ( ! file_exists( $file ) ) return false;		// No file, fail
	$img_attr = getimagesize( $file );
	if ( ! $img_attr ) return false;				// Not an image, fail

	// Retrieve aspect
	$asp_attr = explode( ':', wppa_opt( 'thumb_aspect' ) );

	// Get output path
	$thumbpath = wppa_get_thumb_path( $id );
	if ( wppa_get_ext( $thumbpath ) == 'xxx' ) { // Video poster
		$thumbpath = wppa_strip_ext( $thumbpath ) . '.jpg';
	}

	// Source size
	$src_size_w = $img_attr[0];
	$src_size_h = $img_attr[1];

	// Temp convert width if stereo
	if ( wppa_get_photo_item( $id, 'stereo' ) ) {
		$src_size_w /= 2;
	}

	// Mime type and thumb type
	$mime = $img_attr[2];
	$type = $asp_attr[2];

	// Source native aspect
	$src_asp = $src_size_h / $src_size_w;

	// Required aspect
	if ( $type == 'none' ) {
		$dst_asp = $src_asp;
	}
	else {
		$dst_asp = $asp_attr[0] / $asp_attr[1];
	}

	// Convert back width if stereo
	if ( wppa_get_photo_item( $id, 'stereo' ) ) {
		$src_size_w *= 2;
	}

	// Create the source image
	switch ( $mime ) {	// mime type
		case 1: // gif
			$temp = @ imagecreatefromgif( $file );
			if ( $temp ) {
				$src = imagecreatetruecolor( $src_size_w, $src_size_h );
				imagecopy( $src, $temp, 0, 0, 0, 0, $src_size_w, $src_size_h );
				imagedestroy( $temp );
			}
			else $src = false;
			break;
		case 2:	// jpeg
			if ( ! function_exists( 'imagecreatefromjpeg' ) ) wppa_log( 'Error', 'Function imagecreatefromjpeg does not exist.' );
			$src = @ imagecreatefromjpeg( $file );
			break;
		case 3:	// png
			$src = @ imagecreatefrompng( $file );
			break;
	}
	if ( ! $src ) {
		wppa_log( 'Error', 'Image file '.$file.' is corrupt while creating thmbnail' );
		return true;
	}

	// Compute the destination image size
	if ( $dst_asp < 1.0 ) {	// Landscape
		$dst_size_w = $max_side;
		$dst_size_h = round( $max_side * $dst_asp );
	}
	else {					// Portrait
		$dst_size_w = round( $max_side / $dst_asp );
		$dst_size_h = $max_side;
	}

	// Create the ( empty ) destination image
	$dst = imagecreatetruecolor( $dst_size_w, $dst_size_h );
	if ( $mime == 3 ) {	// Png, save transparancy
		imagealphablending( $dst, false );
		imagesavealpha( $dst, true );
	}

	// Fill with the required color
	$c = trim( strtolower( wppa_opt( 'bgcolor_thumbnail' ) ) );
	if ( $c != '#000000' ) {
		$r = hexdec( substr( $c, 1, 2 ) );
		$g = hexdec( substr( $c, 3, 2 ) );
		$b = hexdec( substr( $c, 5, 2 ) );
		$color = imagecolorallocate( $dst, $r, $g, $b );
		if ( $color === false ) {
			wppa_log( 'Err', 'Unable to set background color to: '.$r.', '.$g.', '.$b.' in wppa_create_thumbnail' );
		}
		else {
			imagefilledrectangle( $dst, 0, 0, $dst_size_w, $dst_size_h, $color );
		}
	}

	// Switch on what we have to do
	switch ( $type ) {
		case 'none':	// Use aspect from fullsize image
			$src_x = 0;
			$src_y = 0;
			$src_w = $src_size_w;
			$src_h = $src_size_h;
			$dst_x = 0;
			$dst_y = 0;
			$dst_w = $dst_size_w;
			$dst_h = $dst_size_h;
			break;
		case 'clip':	// Clip image to given aspect ratio
			if ( $src_asp < $dst_asp ) {	// Source image more landscape than destination
				$dst_x = 0;
				$dst_y = 0;
				$dst_w = $dst_size_w;
				$dst_h = $dst_size_h;
				$src_x = round( ( $src_size_w - $src_size_h / $dst_asp ) / 2 );
				$src_y = 0;
				$src_w = round( $src_size_h / $dst_asp );
				$src_h = $src_size_h;
			}
			else {
				$dst_x = 0;
				$dst_y = 0;
				$dst_w = $dst_size_w;
				$dst_h = $dst_size_h;
				$src_x = 0;
				$src_y = round( ( $src_size_h - $src_size_w * $dst_asp ) / 2 );
				$src_w = $src_size_w;
				$src_h = round( $src_size_w * $dst_asp );
			}
			break;
		case 'padd':	// Padd image to given aspect ratio
			if ( $src_asp < $dst_asp ) {	// Source image more landscape than destination
				$dst_x = 0;
				$dst_y = round( ( $dst_size_h - $dst_size_w * $src_asp ) / 2 );
				$dst_w = $dst_size_w;
				$dst_h = round( $dst_size_w * $src_asp );
				$src_x = 0;
				$src_y = 0;
				$src_w = $src_size_w;
				$src_h = $src_size_h;
			}
			else {
				$dst_x = round( ( $dst_size_w - $dst_size_h / $src_asp ) / 2 );
				$dst_y = 0;
				$dst_w = round( $dst_size_h / $src_asp );
				$dst_h = $dst_size_h;
				$src_x = 0;
				$src_y = 0;
				$src_w = $src_size_w;
				$src_h = $src_size_h;
			}
			break;
		default:		// Not implemented
			return false;
	}

	// Copy left half if stereo
	if ( wppa_get_photo_item( $id, 'stereo' ) ) {
		$src_w /= 2;
	}

	// Do the copy
	imagecopyresampled( $dst, $src, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h );

	// Save the thumb
	$thumbpath = wppa_strip_ext( $thumbpath );
	switch ( $mime ) {	// mime type
		case 1:
			imagegif( $dst, $thumbpath . '.gif' );
			break;
		case 2:
			imagejpeg( $dst, $thumbpath . '.jpg', wppa_opt( 'jpeg_quality' ) );
			break;
		case 3:
			imagepng( $dst, $thumbpath . '.png', 6 );
			break;
	}

	// Cleanup
	imagedestroy( $src );
	imagedestroy( $dst );

	// Optimize
	wppa_optimize_image_file( $thumbpath );

	// Compute and save sizes
	wppa_get_thumbx( $id, 'force' );	// forces recalc x and y

	return true;
}

function wppa_test_for_search( $at_session_start = false ) {
global $wppa;

	if ( isset( $_REQUEST['wppa-searchstring'] ) ) {	// wppa+ search
		$str = $_REQUEST['wppa-searchstring'];
	}
	elseif ( isset( $_REQUEST['searchstring'] ) ) {	// wppa+ search
		$str = $_REQUEST['searchstring'];
	}
	elseif ( isset( $_REQUEST['s'] ) ) {				// wp search
		$str = $_REQUEST['s'];
	}
	else { // Not search
		$str = '';
	}

	// Sanitize
	$ignore = array( '"', "'", '\\', '>', '<', ':', ';', '!', '?', '=', '_', '[', ']', '(', ')', '{', '}' );
	$str = wppa_decode_uri_component( $str );
	$str = str_replace( $ignore, ' ', $str );
	$str = strip_tags( $str );
	$str = stripslashes( $str );
	$str = trim( $str );
	$inter = chr( 226 ).chr( 136 ).chr( 169 );
	$union = chr( 226 ).chr( 136 ).chr( 170 );
	$str = str_replace ( $inter, ' ', $str );
	$str = str_replace ( $union, ',', $str );
	while ( strpos ( $str, '  ' ) !== false ) $str = str_replace ( '  ', ' ', $str );	// reduce spaces
	while ( strpos ( $str, ',,' ) !== false ) $str = str_replace ( ',,', ',', $str );	// reduce commas
	while ( strpos ( $str, ', ' ) !== false ) $str = str_replace ( ', ', ',', $str );	// trim commas
	while ( strpos ( $str, ' ,' ) !== false ) $str = str_replace ( ' ,', ',', $str );	// trim commas

	// Did we do wppa_initialize_runtime() ?
	if ( is_array( $wppa ) && ! $at_session_start ) {
		$wppa['searchstring'] = $str;
		if ( $wppa['searchstring'] && $wppa['occur'] == '1' && ! wppa_in_widget() ) $wppa['src'] = true;
		else $wppa['src'] = false;
		$result = $str;
	}
	else {
		$result = $str;
	}

	if ( $wppa['src'] ) {
		switch ( wppa_opt( 'search_display_type' ) ) {
			case 'slide':
				$wppa['is_slide'] = '1';
				break;
			case 'slideonly':
				$wppa['is_slide'] = '1';
				$wppa['is_slideonly'] = '1';
				break;
			default:
				break;
		}
	}

	return $result;
}


function wppa_table_exists( $xtable ) {
global $wpdb;
static $tables;

	// Some sqls do not show tables, benefit of the doubt: assume table exists
	if ( $tables === false ) return true;
	
	if ( empty( $tables ) ) {
		$tables = $wpdb->get_results( "SHOW TABLES FROM `".DB_NAME."`", ARRAY_A );
	}

	if ( empty( $tables ) ) {
		$tables = false;
		return true;
	}

	// Normal check
	foreach ( $tables as $table ) {
		if ( is_array( $table ) )	foreach ( $table as $item ) {
			if ( strcasecmp( $item, $xtable ) == 0 ) return true;
		}
	}
	return false;
}

// Process the iptc data
function wppa_import_iptc( $id, $info, $nodelete = false ) {
global $wpdb;
static $labels;

	$doit = false;
	// Do we need this?
	if ( wppa_switch( 'save_iptc' ) ) $doit = true;
	if ( substr( wppa_opt( 'newphoto_name_method' ), 0, 2 ) == '2#' ) $doit = true;
	if ( ! $doit ) return;

	wppa_dbg_msg( 'wppa_import_iptc called for id='.$id );
	wppa_dbg_msg( 'array is'.( is_array( $info ) ? ' ' : ' NOT ' ).'available' );
	wppa_dbg_msg( 'APP13 is '.( isset( $info['APP13'] ) ? 'set' : 'NOT set' ) );

	// Is iptc data present?
	if ( !isset( $info['APP13'] ) ) return false;	// No iptc data avail
//var_dump( $info );
	// Parse
	$iptc = iptcparse( $info['APP13'] );
	if ( ! is_array( $iptc ) ) return false;		// No data avail

	// There is iptc data for this image.
	// First delete any existing ipts data for this image
	if ( ! $nodelete ) {
		$wpdb->query( $wpdb->prepare( "DELETE FROM `".WPPA_IPTC."` WHERE `photo` = %s", $id ) );
		wppa_dbg_q( 'Q214' );
	}

	// Find defined labels
	if ( ! is_array( $labels ) ) {
		$result = $wpdb->get_results( "SELECT `tag` FROM `".WPPA_IPTC."` WHERE `photo` = '0' ORDER BY `tag`", ARRAY_N );
		wppa_dbg_q( 'Q215' );
		if ( ! is_array( $result ) ) $result = array();
		$labels = array();
		foreach ( $result as $res ) {
			$labels[] = $res['0'];
		}
	}

	foreach ( array_keys( $iptc ) as $s ) {

		// Check for valid item
		if ( $s == '2#000' ) continue; 	// Skip this one

		if ( is_array( $iptc[$s] ) ) {
			$c = count ( $iptc[$s] );
			for ( $i=0; $i <$c; $i++ ) {

				// Process item
				wppa_dbg_msg( 'IPTC '.$s.' = '.$iptc[$s][$i] );

				// Check labels first
				if ( ! in_array( $s, $labels ) ) {

					// Add to labels
					$labels[] = $s;

					// Add to db
					$photo 	= '0';
					$tag 	= $s;
					$desc 	= $s.':';
						if ( $s == '2#005' ) $desc = 'Graphic name:';
						if ( $s == '2#010' ) $desc = 'Urgency:';
						if ( $s == '2#015' ) $desc = 'Category:';
						if ( $s == '2#020' ) $desc = 'Supp categories:';
						if ( $s == '2#040' ) $desc = 'Spec instr:';
						if ( $s == '2#055' ) $desc = 'Creation date:';
						if ( $s == '2#080' ) $desc = 'Photographer:';
						if ( $s == '2#085' ) $desc = 'Credit byline title:';
						if ( $s == '2#090' ) $desc = 'City:';
						if ( $s == '2#095' ) $desc = 'State:';
						if ( $s == '2#101' ) $desc = 'Country:';
						if ( $s == '2#103' ) $desc = 'Otr:';
						if ( $s == '2#105' ) $desc = 'Headline:';
						if ( $s == '2#110' ) $desc = 'Source:';
						if ( $s == '2#115' ) $desc = 'Photo source:';
						if ( $s == '2#120' ) $desc = 'Caption:';
					$status = 'display';
						if ( $s == '1#090' ) $status = 'hide';
						if ( $desc == $s.':' ) $status= 'hide';
					//	if ( $s == '2#000' ) $status = 'hide';
					$iret = wppa_create_iptc_entry( array( 'photo' => $photo, 'tag' => $tag, 'description' => $desc, 'status' => $status ) );
					if ( ! $iret ) wppa_log( 'Warning', 'Could not add IPTC tag '.$tag.' for photo '.$photo );
				}

				// Now add poto specific data item
				$photo 	= $id;
				$tag 	= $s;
				$desc 	= $iptc[$s][$i];
				if ( wppa_switch( 'iptc_need_utf8' ) ) {
					$desc 	= utf8_encode( $desc );
				}
				$status = 'default';
				$iret = wppa_create_iptc_entry( array( 'photo' => $photo, 'tag' => $tag, 'description' => $desc, 'status' => $status ) );
				if ( ! $iret ) wppa_log( 'Warning', 'Could not add IPTC tag '.$tag.' for photo '.$photo );
			}
		}
	}

	wppa_iptc_clean_garbage( $id );
}

function wppa_get_exif_datetime( $file ) {

	return wppa_get_exif_item( $file, 'DateTimeOriginal' );
}

function wppa_get_exif_orientation( $file ) {

	return wppa_get_exif_item( $file, 'Orientation' );
}

function wppa_get_exif_item( $file, $item ) {

	// File exists?
	if ( ! is_file( $file ) ) {
		return false;
	}

	// Exif functions present?
	if ( ! function_exists( 'exif_imagetype' ) ) {
		return false;
	}

	// Check filetype
	$image_type = @ exif_imagetype( $file );
	if ( $image_type != IMAGETYPE_JPEG ) {
		return false;
	}

	// Can get exif data?
	if ( ! function_exists( 'exif_read_data' ) ) {
		return false;
	}

	// Get exif data
	$exif = @ exif_read_data( $file, 'EXIF' );
	if ( ! is_array( $exif ) ) {
		return false;
	}

	// Data present
	if ( isset( $exif[$item] ) ) {
		return $exif[$item];
	}

	// Nothing found
	return false;
}

function wppa_orientate_image( $id, $ori ) {

	// If orientation right, do nothing
	if ( ! $ori || $ori == '1' ) {
		return;
	}

	wppa_orientate_image_file( wppa_fix_poster_ext( wppa_get_photo_path( $id ), $id ), $ori );
	wppa_bump_photo_rev();
}

function wppa_orientate_image_file( $file, $ori ) {

	// Validate args
	if ( ! is_file( $file ) ) {
		wppa_log( 'Err', 'File not found (wppa_orientate_image_file())' );
		return false;
	}
	if ( ! wppa_is_int( $ori ) || $ori < '2' || $ori > '8' ) {
		wppa_log( 'Err', 'Bad arg $ori:'.$ori.' (wppa_orientate_image_file())' );
		return false;
	}

	// Load image
	$source = imagecreatefromjpeg( $file );

	// Perform operation
	switch ( $ori ) {
		case '2':
			$orientate = $source;
			imageflip( $orientate, IMG_FLIP_HORIZONTAL );
			break;
		case '3':
			$orientate = imagerotate( $source, 180, 0 );
			break;
		case '4':
			$orientate = $source;
			imageflip( $orientate, IMG_FLIP_VERTICAL );
			break;
		case '5':
			$orientate = imagerotate( $source, 270, 0 );
			imageflip( $orientate, IMG_FLIP_HORIZONTAL );
			break;
		case '6':
			$orientate = imagerotate( $source, 270, 0 );
			break;
		case '7':
			$orientate = imagerotate( $source, 90, 0 );
			imageflip( $orientate, IMG_FLIP_HORIZONTAL );
			break;
		case '8':
			$orientate = imagerotate( $source, 90, 0 );
			break;
	}

	// Output
	imagejpeg( $orientate, $file, wppa_opt( 'jpeg_quality' ) );

	// Free the memory
	imagedestroy( $source );
	@ imagedestroy( $orientate );

	// Done
	return true;
}

function wppa_import_exif( $id, $file, $nodelete = false ) {
global $wpdb;
static $labels;
static $names;
global $wppa;

	// Do we need this?
	if ( ! wppa_switch( 'save_exif' ) ) return;

	// Check filetype
	if ( ! function_exists( 'exif_imagetype' ) ) return false;

	$image_type = @ exif_imagetype( $file );
	if ( $image_type != IMAGETYPE_JPEG ) return false;	// Not supported image type

	// Get exif data
	if ( ! function_exists( 'exif_read_data' ) ) return false;	// Not supported by the server

	$exif = @ exif_read_data( $file, 'EXIF' );
	if ( ! is_array( $exif ) ) return false;			// No data present

	// There is exif data for this image.
	// First delete any existing exif data for this image
	if ( ! $nodelete ) {
		$wpdb->query( $wpdb->prepare( "DELETE FROM `".WPPA_EXIF."` WHERE `photo` = %s", $id ) );
		wppa_dbg_q( 'Q218' );
	}

	// Find defined labels
	if ( ! is_array( $labels ) ) {
		$result = $wpdb->get_results( "SELECT * FROM `".WPPA_EXIF."` WHERE `photo` = '0' ORDER BY `tag`", ARRAY_A );
		wppa_dbg_q( 'Q219' );
		if ( ! is_array( $result ) ) $result = array();
		$labels = array();
		$names  = array();
		foreach ( $result as $res ) {
			$labels[] = $res['tag'];
			$names[]  = $res['description'];
		}
	}

	foreach ( array_keys( $exif ) as $s ) {
		// Process item
		wppa_dbg_msg( 'EXIF '.$s.' = '.serialize($exif[$s]) );

		// Check labels first
		$tag = '';
		if ( in_array( $s, $names ) ) {
			$i = 0;
			while ( $i < count( $labels ) ) {
				if ( $names[$i] == $s ) $tag = $labels[$i];
			}
		}
		if ( $tag == '' ) $tag = wppa_exif_tag( $s );
		if ( $tag == 'E#EA1C' ) $tag = ''; // EA1C is explixitly undefined and will fail to register
		if ( $tag == '' ) continue;

		if ( ! in_array( $tag, $labels ) ) {

			// Add to labels
			$labels[] = $tag;
			$names[]  = $s.':';

			// Add to db
			$photo 	= '0';
			$desc 	= $s.':';
			$status = 'display';
			if ( substr( $s, 0, 12 ) == 'UndefinedTag' ) $status = 'hide';
			$iret = wppa_create_exif_entry( array( 'photo' => $photo, 'tag' => $tag, 'description' => $desc, 'status' => $status ) );
			if ( ! $iret ) wppa_log( 'Warning 1', 'Could not add EXIF tag '.$tag.' for photo '.$photo );
		}

		// Now add poto specific data item
		// If its an array...
		if ( is_array( $exif[$s] ) ) { // continue;
			$c = count ( $exif[$s] );
			$max = wppa_opt( 'exif_max_array_size' );
			if ( $max != '0' && $c > $max ) {
				wppa_dbg_msg( 'Exif tag '.$tag. ': array truncated form '.$c.' to '.$max.' elements for photo nr '.$id.'.', 'red' );
				$c = $max;
			}
			for ( $i=0; $i <$c; $i++ ) {
				$photo 	= $id;
				$desc 	= $exif[$s][$i];
				$status = 'default';
				$iret = wppa_create_exif_entry( array( 'photo' => $photo, 'tag' => $tag, 'description' => $desc, 'status' => $status ) );
				if ( ! $iret ) wppa_log( 'Warning 2', 'Could not add EXIF tag '.$tag.' for photo '.$photo );

			}
		}
		// Its not an array
		else {
			$photo 	= $id;
			$desc 	= $exif[$s];
			$status = 'default';
			$iret = wppa_create_exif_entry( array( 'photo' => $photo, 'tag' => $tag, 'description' => $desc, 'status' => $status ) );
			if ( ! $iret ) {} /* wppa_log( 'Warning 3', 'Could not add EXIF tag '.$tag.' for photo '.$photo.', desc = '.$desc ); */ // Is junk, dont care
		}
	}

	wppa_exif_clean_garbage( $id );
}

// Inverse of exif_tagname();
function wppa_exif_tag( $tagname ) {
global $wppa_inv_exiftags;

	// Setup inverted matrix
	if ( ! is_array( $wppa_inv_exiftags ) ) {
		$key = 0;
		while ( $key < 65536 ) {
			$tag = exif_tagname( $key );
			if ( $tag != '' ) {
				$wppa_inv_exiftags[$tag] = $key;
			}
			$key++;
			if ( ! $key ) break;	// 16 bit server wrap around ( do they still exist??? )
		}
	}
	// Search
	if ( isset( $wppa_inv_exiftags[$tagname] ) ) return sprintf( 'E#%04X',$wppa_inv_exiftags[$tagname] );
	elseif ( strlen( $tagname ) == 19 ) {
		if ( substr( $tagname, 0, 12 ) == 'UndefinedTag' ) return 'E#'.substr( $tagname, -4 );
	}
	else return '';
}

function wppa_clear_cache( $force = false ) {
global $cache_path;

	// If wp-super-cache is on board, clear cache
	if ( function_exists( 'prune_super_cache' ) ) {
		prune_super_cache( $cache_path . 'supercache/', true );
		prune_super_cache( $cache_path, true );
	}

	// W3 Total cache
	if ( function_exists( 'w3tc_pgcache_flush' ) ) {
		w3tc_pgcache_flush();
	}

	// SG_CachePress
	/*
	if ( class_exists( 'SG_CachePress_Supercacher' ) ) {
		$c = new SG_CachePress_Supercacher();
		@ $c->purge_cache();
	}
	*/

	// Quick cache
	if ( isset($GLOBALS['quick_cache']) ) {
		$GLOBALS['quick_cache']->clear_cache();
	}

	// At a setup or update operation
	// Manually remove the content of wp-content/cache/
	if ( $force ) {
		if ( is_dir( WPPA_CONTENT_PATH.'/cache/' ) ) {
			wppa_tree_empty( WPPA_CONTENT_PATH.'/cache' );
		}
	}
}

// Removes the content of $dir, ignore errors
function wppa_tree_empty( $dir ) {
	$files = glob( $dir.'/*' );
	if ( is_array( $files ) ) foreach ( $files as $file ) {
		$name = basename( $file );
		if ( $name == '.' || $name == '..' ) {}
		elseif ( is_dir( $file ) ) {
			wppa_tree_empty( $file );
			@ unlink( $file );
		}
		else @ unlink( $file );
	}
}

function wppa_alert( $msg, $reload = false ) {
global $wppa;

	if ( ! $reload && $msg ) {
		wppa_add_js_page_data( '<script type="text/javascript">alert( \''.esc_js( $msg ).'\' );jQuery( "#wppaer" ).html( "" );</script>' );
	}
	elseif ( $reload == 'home' ) {
		echo 	'<script id="wppaer" type="text/javascript" >' .
					( $msg ? 'alert( \''.esc_js( $msg ).'\' );' : '' ) .
					'jQuery( "#wppaer" ).html( "" );' .
					'document.location.href="'.home_url().'";' .
				'</script>';
	}
	else {
		echo 	'<script id="wppaer" type="text/javascript" >' .
					( $msg ? 'alert( \''.esc_js( $msg ).'\' );' : '' ) .
					'jQuery( "#wppaer" ).html( "" );' .
					'document.location.reload( true );' .
				'</script>';
	}
}

// Return the allowed number to upload in an album. -1 = unlimited
function wppa_allow_uploads( $alb = '0' ) {
global $wpdb;

	if ( ! $alb ) return '-1';//'0';

	$album = wppa_cache_album( $alb );

	$limits = $album['upload_limit']; //$wpdb->get_var( $wpdb->prepare( "SELECT `upload_limit` FROM `".WPPA_ALBUMS."` WHERE `id` = %s", $alb ) );

	$temp = explode( '/', $limits );
	$limit_max  = isset( $temp[0] ) ? $temp[0] : '0';
	$limit_time = isset( $temp[1] ) ? $temp[1] : '0';

	if ( ! $limit_max ) return '-1';		// Unlimited max

	if ( ! $limit_time ) {					// For ever
		$curcount = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_PHOTOS."` WHERE `album` = %s", $alb ) );
		wppa_dbg_q( 'Q226' );
	}
	else {									// Time criterium in place
		$timnow = time();
		$timthen = $timnow - $limit_time;
		$curcount = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_PHOTOS."` WHERE `album` = %s AND `timestamp` > %s", $alb, $timthen ) );
		wppa_dbg_q( 'Q227' );
	}

	if ( $curcount >= $limit_max ) $result = '0';	// No more allowed
	else $result = $limit_max - $curcount;

	return $result;
}

// Return the allowed number of uploads for a certain user. -1 = unlimited
function wppa_allow_user_uploads() {
global $wpdb;

	// Get the limits
	$limits = wppa_get_user_upload_limits();

	$temp = explode( '/', $limits );
	$limit_max  = isset( $temp[0] ) ? $temp[0] : '0';
	$limit_time = isset( $temp[1] ) ? $temp[1] : '0';

	if ( ! $limit_max ) return '-1';		// Unlimited max

	$user = wppa_get_user( 'login' );
	if ( ! $limit_time ) {					// For ever
		$curcount = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_PHOTOS."` WHERE `owner` = %s", $user ) );
		wppa_dbg_q( 'Q326' );
	}
	else {									// Time criterium in place
		$timnow = time();
		$timthen = $timnow - $limit_time;
		$curcount = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_PHOTOS."` WHERE `owner` = %s AND `timestamp` > %s", $user, $timthen ) );
		wppa_dbg_q( 'Q327' );
	}

	if ( $curcount >= $limit_max ) $result = '0';	// No more allowed
	else $result = $limit_max - $curcount;

	return $result;
}
function wppa_get_user_upload_limits() {
global $wp_roles;

	$limits = '';
	if ( is_user_logged_in() ) {
		if ( current_user_can( 'wppa_upload' ) ) $limits = '0/0';		// Unlimited if you have wppa_upload capabilities
		else {
			$roles = $wp_roles->roles;
			$roles['loggedout'] = '';
			unset ( $roles['administrator'] );
			foreach ( array_keys( $roles ) as $role ) if ( ! $limits ) {
				if ( current_user_can( $role ) ) $limits = get_option( 'wppa_'.$role.'_upload_limit_count', '0' ).'/'.get_option( 'wppa_'.$role.'_upload_limit_time', '0' );
			}
		}
	}
	else {
		$limits = wppa_opt( 'loggedout_upload_limit_count' ).'/'.wppa_opt( 'loggedout_upload_limit_time' );
	}
	return $limits;
}


function wppa_alfa_id( $id = '0' ) {
	return str_replace( array( '1', '2', '3', '4', '5', '6', '7', '8', '9', '0' ), array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j' ), $id );
}

// Thanx to the maker of nextgen, but greatly improved
// Usage: wppa_check_memory_limit() return string telling the max upload size
// @1: if false, return array ( 'maxx', 'maxy', 'maxp' )
// @2: width to test an image,
// @3: height to test an image.
// If both present: return true if fit in memory, false if not.
//
//
function wppa_check_memory_limit( $verbose = true, $x = '0', $y = '0' ) {

// ini_set( 'memory_limit', '18M' );	// testing
	if ( ! function_exists( 'memory_get_usage' ) ) return '';
	if ( is_admin() && ! wppa_switch( 'memcheck_admin' ) ) return '';
	if ( ! is_admin() && ! wppa_switch( 'memcheck_frontend' ) ) return '';

	// get memory limit
	$memory_limit = 0;
	$memory_limini = wppa_convert_bytes( ini_get( 'memory_limit' ) );
	$memory_limcfg = wppa_convert_bytes( get_cfg_var( 'memory_limit' ) );

	// find the smallest not being zero
	if ( $memory_limini && $memory_limcfg ) $memory_limit = min( $memory_limini, $memory_limcfg );
	elseif ( $memory_limini ) $memory_limit = $memory_limini;
	else $memory_limit = $memory_limcfg;

	// No data
	if ( ! $memory_limit ) return '';

	// Calculate the free memory
	$free_memory = $memory_limit - memory_get_usage( true );

	// Calculate number of pixels largest target resized image
	if ( wppa_switch( 'resize_on_upload' ) ) {
		$t = wppa_opt( 'resize_to' );
		if ( $t == '0' ) {
			$to['0'] = wppa_opt( 'fullsize' );
			$to['1'] = wppa_opt( 'maxheight' );
		}
		else {
			$to = explode( 'x', $t );
		}
		$resizedpixels = $to['0'] * $to['1'];
	}
	else {
		$resizedpixels = wppa_get_minisize() * wppa_get_minisize() * 3 / 4;
	}

	// Number of bytes per pixel ( found by trial and error )
	//	$factor = '5.60';	//  5.60 for 17M: 386 x 289 ( 0.1 MP ) thumb only
	//	$factor = '5.10';	//  5.10 for 104M: 4900 x 3675 ( 17.2 MP ) thumb only
	$memlimmb = $memory_limit / ( 1024 * 1024 );
	$factor = '6.00' - '0.58' * ( $memlimmb / 104 );	// 6.00 .. 0.58

	// Calculate max size
	$maxpixels = ( $free_memory / $factor ) - $resizedpixels;

	// If obviously faulty: quit silently
	if ( $maxpixels < 0 ) return '';

	// What are we asked for?
	if ( $x && $y ) { 	// Request for check an image
		if ( $x * $y <= $maxpixels ) $result = true;
		else $result = false;
	}
	else {	// Request for tel me what is the limit
		$maxx = sqrt( $maxpixels / 12 ) * 4;
		$maxy = sqrt( $maxpixels / 12 ) * 3;
		if ( $verbose ) {		// Make it a string
			$result = '<br />'.sprintf(  __( 'Based on your server memory limit you should not upload images larger then <strong>%d x %d (%2.1f MP)</strong>' , 'wp-photo-album-plus'), $maxx, $maxy, $maxpixels / ( 1024 * 1024 ) );
		}
		else {					// Or an array
			$result['maxx'] = $maxx;
			$result['maxy'] = $maxy;
			$result['maxp'] = $maxpixels;
		}
	}
	return $result;
}

/**
 * Convert a shorthand byte value from a PHP configuration directive to an integer value. Negative values return 0.
 * @param    string   $value
 * @return   int
 */
function wppa_convert_bytes( $value ) {
    if ( is_numeric( $value ) ) {
        return max( '0', $value );
    } else {
        $value_length = strlen( $value );
        $qty = substr( $value, 0, $value_length - 1 );
        $unit = strtolower( substr( $value, $value_length - 1 ) );
        switch ( $unit ) {
            case 'k':
                $qty *= 1024;
                break;
            case 'm':
                $qty *= 1048576;
                break;
            case 'g':
                $qty *= 1073741824;
                break;
        }
        return max( '0', $qty );
    }
}

function wppa_dbg_q( $id ) {
global $wppa;

	if ( ! $wppa['debug'] ) return;	// Nothing to do here

	switch ( $id ) {
		case 'init':
			break;
		case 'print':
			if ( ! isset( $wppa['queries'] ) ) return;
			if ( ! is_array( $wppa['queries'] ) ) return;	// Did nothing
			$qtot = 0;
			$gtot = 0;
			$keys = array_keys( $wppa['queries'] );
			sort( $keys );
			$line = 'Cumulative query stats: Q=query, G=cache<br />';
			foreach ( $keys as $k ) {
				if ( substr( $k,0,1 ) == 'Q' ) {
					$line .= $k.'=>'.$wppa['queries'][$k].', ';
					$qtot += $wppa['queries'][$k];
				}
			}
			$line .= '<br />';
			foreach ( $keys as $k ) {
				if ( substr( $k,0,1 ) == 'G' ) {
					$line .= $k.'=>'.$wppa['queries'][$k].', ';
					$gtot += $wppa['queries'][$k];
				}
			}
			$line .= '<br />';
			$line .= sprintf( 'Total queries attempted: %d, Cash hits: %d, equals %4.2f%%, misses: %d.', $qtot+$gtot, $gtot, $gtot*100/( $qtot+$gtot ), $qtot );
			$line .= ' 2nd level cache entries: albums: '.wppa_cache_album( 'count' ).', photos: '.wppa_cache_photo( 'count' ).' NQ='.get_num_queries();
			wppa_dbg_msg( $line );
//			ob_start();
//			print_r( $wppa['queries'] );
//			wppa_dbg_msg( ob_get_clean() );
			break;
		default:
			if ( $wppa['debug'] ) if ( !isset( $wppa['queries'][$id] ) ) $wppa['queries'][$id]=1;else $wppa['queries'][$id]++;
			break;
	}
}

// Get gps data from photofile
function wppa_get_coordinates( $picture_path, $photo_id ) {
global $wpdb;

	// Exif on board?
	if ( ! function_exists( 'exif_read_data' ) ) return false;

	// Check filetype
	if ( ! function_exists( 'exif_imagetype' ) ) return false;
	$image_type = @ exif_imagetype( $picture_path );
	if ( $image_type != IMAGETYPE_JPEG ) return false;	// Not supported image type

	// get exif data
	if ( $exif = @ exif_read_data( $picture_path, 0 , false ) ) {

		// any coordinates available?
		if ( !isset ( $exif['GPSLatitude'][0] ) ) return false;	// No GPS data
		if ( !isset ( $exif['GPSLongitude'][0] ) ) return false;	// No GPS data

		// north, east, south, west?
		if ( $exif['GPSLatitudeRef'] == "S" ) {
			$gps['latitude_string'] = -1;
			$gps['latitude_dicrection'] = "S";
		}
		else {
			$gps['latitude_string'] = 1;
			$gps['latitude_dicrection'] = "N";
		}
		if ( $exif['GPSLongitudeRef'] == "W" ) {
			$gps['longitude_string'] = -1;
			$gps['longitude_dicrection'] = "W";
		}
		else {
			$gps['longitude_string'] = 1;
			$gps['longitude_dicrection'] = "E";
		}
		// location
		$gps['latitude_hour'] = $exif["GPSLatitude"][0];
		$gps['latitude_minute'] = $exif["GPSLatitude"][1];
		$gps['latitude_second'] = $exif["GPSLatitude"][2];
		$gps['longitude_hour'] = $exif["GPSLongitude"][0];
		$gps['longitude_minute'] = $exif["GPSLongitude"][1];
		$gps['longitude_second'] = $exif["GPSLongitude"][2];

		// calculating
		foreach( $gps as $key => $value ) {
			$pos = strpos( $value, '/' );
			if ( $pos !== false ) {
				$temp = explode( '/',$value );
				if ( $temp[1] ) $gps[$key] = $temp[0] / $temp[1];
				else $gps[$key] = 0;
			}
		}

		$geo['latitude_format'] = $gps['latitude_dicrection']." ".$gps['latitude_hour']."&deg;".$gps['latitude_minute']."&#x27;".round ( $gps['latitude_second'], 4 ).'&#x22;';
		$geo['longitude_format'] = $gps['longitude_dicrection']." ".$gps['longitude_hour']."&deg;".$gps['longitude_minute']."&#x27;".round ( $gps['longitude_second'], 4 ).'&#x22;';

		$geo['latitude'] = $gps['latitude_string'] * ( $gps['latitude_hour'] + ( $gps['latitude_minute'] / 60 ) + ( $gps['latitude_second'] / 3600 ) );
		$geo['longitude'] = $gps['longitude_string'] * ( $gps['longitude_hour'] + ( $gps['longitude_minute'] / 60 ) + ( $gps['longitude_second'] / 3600 ) );

	}
	else {	// No exif data
		return false;
	}

	// Process result
//	print_r( $geo );	// debug
	$result = implode( '/', $geo );
	$wpdb->query( $wpdb->prepare( "UPDATE `".WPPA_PHOTOS."` SET `location` = %s WHERE `id` = %s", $result, $photo_id ) );
	return $geo;
}


function wppa_format_geo( $lat, $lon ) {

	if ( ! $lat && ! $lon ) return '';	// Both zero: clear

	if ( ! $lat ) $lat = '0.0';
	if ( ! $lon ) $lon = '0.0';

	$geo['latitude_format'] = $lat >= '0.0' ? 'N ' : 'S ';
	$d = floor( $lat );
	$m = floor( ( $lat - $d ) * 60 );
	$s = round( ( ( ( $lat - $d ) * 60 - $m ) * 60 ), 4 );
	$geo['latitude_format'] .= $d.'&deg;'.$m.'&#x27;'.$s.'&#x22;';

	$geo['longitude_format'] = $lon >= '0.0' ? 'E ' : 'W ';
	$d = floor( $lon );
	$m = floor( ( $lon - $d ) * 60 );
	$s = round( ( ( ( $lon - $d ) * 60 - $m ) * 60 ), 4 );
	$geo['longitude_format'] .= $d.'&deg;'.$m.'&#x27;'.$s.'&#x22;';

	$geo['latitude'] = $lat;
	$geo['longitude'] = $lon;

	$result = implode( '/', $geo );
	return $result;
}


function wppa_album_select_a( $args ) {
global $wpdb;

	$args = wp_parse_args( $args, array( 	'exclude' 			=> '',
											'selected' 			=> '',
											'disabled' 			=> '',
											'addpleaseselect' 	=> false,
											'addnone' 			=> false,
											'addall' 			=> false,
											'addgeneric'		=> false,
											'addblank' 			=> false,
											'addselected'		=> false,
											'addseparate' 		=> false,
											'addselbox'			=> false,
											'addowner' 			=> false,
											'disableancestors' 	=> false,
											'checkaccess' 		=> false,
											'checkowner' 		=> false,
											'checkupload' 		=> false,
											'addmultiple' 		=> false,
											'addnumbers' 		=> false,
											'path' 				=> false,
											'root' 				=> false,
											'content'			=> false,
											'sort'				=> true,
											'checkarray' 		=> false,
											'array' 			=> array(),
											 ) );

	// Provide default selection if no selected given
	if ( $args['selected'] === '' ) {
        $args['selected'] = wppa_get_last_album();
    }

	// See if selection is valid
	if ( ( $args['selected'] == $args['exclude'] ) ||
		 ( $args['checkupload'] && ! wppa_allow_uploads( $args['selected'] ) ) ||
		 ( $args['disableancestors'] && wppa_is_ancestor( $args['exclude'], $args['selected'] ) )
	   ) {
		$args['selected'] = '0';
	}

	$albums = $wpdb->get_results(
		"SELECT * FROM `" . WPPA_ALBUMS . "` " . wppa_get_album_order( $args['root'] ), ARRAY_A
		);

	// Add to secondary cache
	if ( $albums ) {
		wppa_cache_album( 'add', $albums );
	}

	if ( $albums ) {
		// Filter for root
		if ( $args['root'] ) {
			$root = $args['root'];
			switch ( $root ) {	// case '0': all, will be skipped as it returns false in 'if ( $args['root'] )'
				case '-2':	// Generic only
				foreach ( array_keys( $albums ) as $albidx ) {
					if ( wppa_is_separate( $albums[$albidx]['id'] ) ) unset ( $albums[$albidx] );
				}
				break;
				case '-1':	// Separate only
				foreach ( array_keys( $albums ) as $albidx ) {
					if ( ! wppa_is_separate( $albums[$albidx]['id'] ) ) unset ( $albums[$albidx] );
				}
				break;
				default:
				foreach ( array_keys( $albums ) as $albidx ) {
					if ( ! wppa_is_ancestor( $root, $albums[$albidx]['id'] ) ) unset ( $albums[$albidx] );
				}
				break;
			}
		}
		// Filter for must have content
		if ( $args['content'] ) {
			foreach ( array_keys( $albums ) as $albidx ) {
				if ( wppa_get_photo_count( $albums[$albidx]['id'] ) <= wppa_get_mincount() ) unset ( $albums[$albidx] );
			}
		}
		// Add paths
		if ( $args['path'] ) {
			$albums = wppa_add_paths( $albums );
		}
		// Or just translate
		else foreach ( array_keys( $albums ) as $index ) {
			$albums[$index]['name'] = __( stripslashes( $albums[$index]['name'] ) , 'wp-photo-album-plus');
		}
		// Sort
		if ( $args['sort'] ) $albums = wppa_array_sort( $albums, 'name' );
	}

	// Output
	$result = '';

	$selected = $args['selected'] == '0' ? ' selected="selected"' : '';
	if ( $args['addpleaseselect'] ) $result .=
		'<option value="0" disabled="disabled" '.$selected.' >' .
			__( '- select an album -' , 'wp-photo-album-plus' ) .
		'</option>';

	$selected = $args['selected'] == '0' ? ' selected="selected"' : '';
	if ( $args['addnone'] ) $result .=
		'<option value="0"'.$selected.' >' .
			__( '--- none ---' , 'wp-photo-album-plus' ) .
		'</option>';

	$selected = $args['selected'] == '0' ? ' selected="selected"' : '';
	if ( $args['addall'] ) $result .=
		'<option value="0"'.$selected.' >' .
			__( '--- all ---' , 'wp-photo-album-plus' ) .
		'</option>';

	$selected = $args['selected'] == '-2' ? ' selected="selected"' : '';
	if ( $args['addall'] ) $result .=
		'<option value="-2"'.$selected.' >' .
			__( '--- generic ---' , 'wp-photo-album-plus' ) .
		'</option>';

	$selected = $args['selected'] == '-3' ? ' selected="selected"' : '';
	if ( $args['addowner'] ) $result .=
		'<option value="-3"'.$selected.' >' .
			__( '--- owner/public ---' , 'wp-photo-album-plus' ) .
		'</option>';

	$selected = $args['selected'] == '0' ? ' selected="selected"' : '';
	if ( $args['addblank'] ) $result .=
		'<option value="0"'.$selected.' >' .
		'</option>';

	$selected = $args['selected'] == '-99' ? ' selected="selected"' : '';
	if ( $args['addmultiple'] ) $result .=
		'<option value="-99"'.$selected.' >' .
			__( '--- multiple see below ---' , 'wp-photo-album-plus' ) .
		'</option>';

	$selected = $args['selected'] == '0' ? ' selected="selected"' : '';
	if ( $args['addselbox'] ) $result .=
		'<option value="0"'.$selected.' >' .
			__( '--- a selection box ---' , 'wp-photo-album-plus' ) .
		'</option>';

	if ( $albums ) foreach ( $albums as $album ) {
		if ( ( $args['disabled'] == $album['id'] ) ||
			 ( $args['exclude'] == $album['id'] ) ||
			 ( $args['checkupload'] && ! wppa_allow_uploads( $album['id'] ) ) ||
			 ( $args['disableancestors'] && wppa_is_ancestor( $args['exclude'], $album['id'] ) )
			 ) $disabled = ' disabled="disabled"'; else $disabled = '';
		if ( $args['selected'] == $album['id'] && ! $disabled ) $selected = ' selected="selected"'; else $selected = '';

		$ok = true; // Assume this will be in the list
		if ( $args['checkaccess'] && ! wppa_have_access( $album['id'] ) ) {
			$ok = false;
		}
		if ( $args['checkowner'] && wppa_switch( 'upload_owner_only' ) ) { 							// Need to check
			if ( $album['owner'] != wppa_get_user() && $album['owner']  != '--- public ---' ) { 	// Not 'mine'
				if ( ! wppa_user_is( 'administrator' ) ) {											// No admin
					$ok = false;
				}
			}
		}
		if ( $args['checkarray'] ) {
			if ( ! in_array( $album['id'], $args['array'] ) ) {
				$ok = false;
			}
		}
		if ( $selected && $args['addselected'] ) {
			$ok = true;
		}
		if ( $ok ) {
			if ( $args['addnumbers'] ) $number = ' ( '.$album['id'].' )'; else $number = '';
			$result .= '<option value="' . $album['id'] . '" ' . $selected . $disabled . '>' . $album['name'] . $number . '</option>';
		}
	}

	$selected = $args['selected'] == '-1' ? ' selected="selected"' : '';
	if ( $args['addseparate'] ) $result .=
		'<option value="-1"' . $selected . '>' .
			__( '--- separate ---' , 'wp-photo-album-plus' ) .
		'</option>';

	return $result;
}

function wppa_delete_obsolete_tempfiles() {
	// To prevent filling up diskspace, divide lifetime by 2 and repeat removing obsolete files until count <= 10
	$filecount = 101;
	$lifetime = 3600;
	while ( $filecount > 100 ) {
		$files = glob( WPPA_UPLOAD_PATH.'/temp/*' );
		$filecount = 0;
		if ( $files ) {
			$timnow = time();
			$expired = $timnow - $lifetime;
			foreach ( $files as $file ) {
				if ( is_file( $file ) ) {
					$modified = filemtime( $file );
					if ( $modified < $expired ) @ unlink( $file );
					else $filecount++;
				}
			}
		}
		$lifetime /= 2;
	}
}

function wppa_publish_scheduled() {
global $wpdb;

	$last_check = get_option( 'wppa_last_schedule_check', '0' );
	if ( $last_check < ( time() - 300 ) ) {	// Longer than 5 mins ago
		$to_publish = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM`".WPPA_PHOTOS."` WHERE `status` = 'scheduled' AND `scheduledtm` < %s", wppa_get_default_scheduledtm() ), ARRAY_A );
		if ( $to_publish ) foreach ( $to_publish as $photo ) {
			wppa_update_photo( array( 'id' => $photo['id'], 'scheduledtm' => '', 'status' => 'publish', 'timestamp' => time() ) );
			wppa_update_album( array( 'id' => $photo['album'], 'modified' => time() ) );	// For New indicator on album
			wppa_flush_treecounts( $photo['album'] );
		}
		$to_publish = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM`".WPPA_ALBUMS."` WHERE `scheduledtm` <> '' AND `scheduledtm` < %s", wppa_get_default_scheduledtm() ), ARRAY_A );
		if ( $to_publish ) foreach ( $to_publish as $album ) {
			wppa_update_album( array( 'id' => $album['id'], 'scheduledtm' => '' ) );
			wppa_flush_treecounts( $album['id'] );
		}
		update_option( 'wppa_last_schedule_check', time() );
	}
}

function wppa_add_js_page_data( $txt ) {
global $wppa_js_page_data_file;
global $wppa;

	if ( is_admin() && ! $wppa['ajax'] ) {
		echo $txt;
		return;
	}

	if ( $wppa_js_page_data_file && ! $wppa['ajax'] ) {
		$handle = fopen( $wppa_js_page_data_file, 'ab' );
	}
	else {
		$handle = false;
	}

	if ( $handle ) {
		$txt = str_replace( '<script type="text/javascript">', '', $txt );
		$txt = str_replace( '</script>', '', $txt );
		$txt = str_replace( "\t", '', $txt );
		$txt = str_replace( "\n", '', $txt );
		$txt = trim( $txt );
		if ( $txt ) fwrite( $handle, "\n".$txt );
		fclose( $handle );
	}
	else {
		$wppa['out'] .= $txt;
	}
}

function wppa_add_credit_points( $amount, $reason = '', $id = '', $value = '' ) {
global $user_ID;

	// Initialize
	if ( ! $user_ID ) {
		$current_user = wp_get_current_user();
		$user_ID = $current_user->ID;
	}
	$bret = false;

	// Must be logged in
	if ( ! is_user_logged_in() ) return $bret;

	// Cube points
	if ( function_exists( 'cp_alterPoints' )  ) {
		cp_alterPoints( cp_currentUser(), $amount );
		$bret = true;
	}

	// myCred
	if ( function_exists( 'mycred_add' ) ) {
		$entry = $reason . ( $id ? ', '.__('Photo id =', 'wp-photo-album-plus').' '.$id : '' ) . ( $value ? ', '.__('Value =', 'wp-photo-album-plus').' '.$value : '' );
		$bret = mycred_add( str_replace( ' ', '_', $reason ), $user_ID, $amount, $entry, '', '', '' ); // $ref_id, $data, $type );
	}

	return $bret;
}
