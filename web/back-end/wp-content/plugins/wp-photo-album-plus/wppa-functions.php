<?php
/* wppa-functions.php
* Package: wp-photo-album-plus
*
* Various funcions
* Version 6.4.09
*
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

// Get the albums by calling the theme module and do some parameter processing
// This is the main entrypoint for the wppa+ invocation, either 'by hand' or through the filter.
// As of version 3.0.0 this routine returns the entire html created by the invocation.
function wppa_albums( $id = '', $type = '', $size = '', $align = '' ) {
global $wppa_lang;
global $wppa_locale;
global $wpdb;
global $thumbs;
global $wppa_session;

	// Diagnostics
	wppa_dbg_msg( 'Entering wppa_albums' );
	wppa_dbg_msg( 'Lang=' . $wppa_lang . ', Locale=' . $wppa_locale . ', Ajax=' . wppa( 'ajax' ) );
	wppa_dbg_msg( '$wppa_session = ' . serialize( $wppa_session ) );

	// List content filters
	// Data struct:	$wp_filter[$tag][$priority][$idx] = array( 'function' => $function_to_add, 'accepted_args' => $accepted_args );
	if ( wppa( 'debug' ) && wppa( 'mocc' ) == '0' ) {
		global $wp_filter;

		wppa_dbg_msg( 'Start content filters', 'green' );
		foreach ( array_keys( $wp_filter['the_content'] ) as $key ) {
			foreach ( array_keys( $wp_filter['the_content'][$key] ) as $key2 ) {

				$func = is_array( $wp_filter['the_content'][$key][$key2]['function'] ) ?
						@	serialize( $wp_filter['the_content'][$key][$key2]['function'] ) :
							$wp_filter['the_content'][$key][$key2]['function'];

				wppa_dbg_msg( 'Pri:'.$key.', func: '.$func.', args: '.$wp_filter['the_content'][$key][$key2]['accepted_args'] );
			}
		}
		wppa_dbg_msg( 'End content filters', 'green' );
	}

	// Process a user upload request, if any. Do it here: it may affect this occurences display
	wppa_user_upload();

	// Test for scheduled publications
	wppa_publish_scheduled();

	// First calculate the occurance
	if ( wppa( 'ajax' ) ) {
		if ( wppa_get_get( 'moccur' ) ) {
			wppa( 'mocc', wppa_get_get( 'moccur' ) );
			if ( ! is_numeric( wppa( 'mocc' ) ) ) wp_die( 'Security check failure 1' );
		}
		else {
			wppa( 'mocc', '1' );
		}

		wppa( 'fullsize', wppa_get_get( 'wppa-size', wppa_get_container_width() ) );

		if ( wppa_get_get( 'occur' ) ) {
			wppa( 'occur', wppa_get_get( 'occur' ) );
			if ( ! is_numeric( wppa( 'occur' ) ) ) wp_die( 'Security check failure 2' );
		}

		if ( wppa_get_get( 'woccur' ) ) {
			wppa( 'widget_occur', wppa_get_get( 'woccur' ) );
			wppa( 'in_widget', true );
			if ( ! is_numeric( wppa( 'widget_occur' ) ) ) wp_die( 'Security check failure 3' );
		}
	}
	else {
		wppa( 'mocc', wppa( 'mocc' ) + '1' );
		if ( wppa_in_widget() ) {
			wppa( 'widget_occur', wppa( 'widget_occur' ) + '1' );
		}
		else {
			wppa( 'occur', wppa( 'occur' ) + '1' );
		}
	}

	// Set wppa( 'src' ) = true and wppa( 'searchstring' ) if this occurrance processes a search request.
	wppa_test_for_search();

	// There are 3 ways to get here:
	// in order of priority:
	// 1. The given query string applies to this invocation ( occurrance )
	//    This invocation requires the ignorance of the filter results and the interpretation of the querystring.
	if ( ( ( wppa_get_get( 'occur' ) || wppa_get_get( 'woccur' ) ) &&								// There IS a query string. For bw compat, occur is required ...
		 ( ( wppa_in_widget() && wppa( 'widget_occur' ) == wppa_get_get( 'woccur' ) ) ||		// and it applies to ...
		 ( ! wppa_in_widget() && wppa( 'occur' ) == wppa_get_get( 'occur' ) ) )				// this occurrance
		 ) && ! wppa( 'is_autopage' ) ) {

		// Process query string
		wppa_out( wppa_dbg_msg( 'Querystring applied', 'brown', false, true ) );

		// Test validity of album arg
		wppa( 'start_album', wppa_get_get( 'album' ) );
//		if ( wppa( 'start_album' ) == '-9' ) exit;
//		echo 'sa='.wppa( 'start_album' ).'<br />';

		wppa( 'is_cover', wppa_get_get( 'cover' ) );
		wppa( 'is_slide', wppa_get_get( 'slide' ) || ( wppa_get_get( 'album' ) !== false && wppa_get_get( 'photo' ) ) );
		if ( wppa( 'is_slide' ) ) {
			wppa( 'start_photo', wppa_get_get( 'photo' ) );		// Start a slideshow here
		}
		else {
			wppa( 'single_photo', wppa_get_get( 'photo' ) ); 	// Photo is the single photoid
		}
		wppa( 'is_single', wppa_get_get( 'single' ) );			// Is a one image slideshow
		wppa( 'topten_count', wppa_force_numeric_else( wppa_get_get( 'topten' ), wppa_opt( 'topten_count' ) ) );
		wppa( 'is_topten', wppa( 'topten_count' ) != '0' );
		wppa( 'lasten_count', wppa_force_numeric_else( wppa_get_get( 'lasten' ), wppa_opt( 'lasten_count' ) ) );
		wppa( 'is_lasten', wppa( 'lasten_count' ) != '0' );
		wppa( 'comten_count', wppa_force_numeric_else( wppa_get_get( 'comten' ), wppa_opt( 'comten_count' ) ) );
		wppa( 'is_comten', wppa( 'comten_count' ) != '0' );
		wppa( 'featen_count', wppa_force_numeric_else( wppa_get_get( 'featen' ), wppa_opt( 'featen_count' ) ) );
		wppa( 'is_featen', wppa( 'featen_count' ) != '0' );
		wppa( 'albums_only', wppa_get_get( 'albums-only' ) );
		wppa( 'photos_only', wppa_get_get( 'photos-only' ) );
		wppa( 'medals_only', wppa_get_get( 'medals-only' ) );
		wppa( 'related_count', wppa_force_numeric_else( wppa_get_get( 'relcount' ), wppa_opt( 'related_count' ) ) );
		wppa( 'is_related', wppa_get_get( 'rel' ) );
		if ( wppa( 'is_related' ) == 'tags' ) {
			wppa( 'is_tag', wppa_get_related_data() );
			if ( wppa( 'related_count' ) == '0' ) {
				wppa( 'related_count', wppa_opt( 'related_count' ) );
			}
		}
		else {
			wppa( 'is_tag', trim( strip_tags( wppa_get_get( 'tag' ) ), ',;' ) );
		}
		if ( wppa( 'is_related' ) == 'desc' ) {
			wppa( 'src', true );
			if ( wppa( 'related_count' ) == '0' ) wppa( 'related_count', wppa_opt( 'related_count' ) );
			wppa( 'searchstring', str_replace( ';', ',', wppa_get_related_data() ) );
			wppa( 'photos_only', true );
		}
		if ( wppa( 'is_tag' ) ) wppa_dbg_msg( 'Is Tag: ' . wppa( 'is_tag' ) );
		else wppa_dbg_msg( 'Is NOT Tag' );
		wppa( 'page', wppa_get_get( 'page' ) );
		if ( wppa_get_get( 'superview' ) ) {
			$wppa_session['superview'] = wppa( 'is_slide' ) ? 'slide': 'thumbs';
			$wppa_session['superalbum'] = wppa( 'start_album' );
			wppa_save_session();
			wppa( 'photos_only', true );
		}
		wppa( 'is_upldr', wppa_get_get( 'upldr' ) );
		if ( wppa( 'is_upldr' ) ) wppa( 'photos_only', true );
		wppa( 'is_owner', wppa_get_get( 'owner' ) );
		if ( wppa( 'is_owner' ) ) {
			$albs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `".WPPA_ALBUMS."` WHERE `owner` = %s ", wppa( 'is_owner' ) ), ARRAY_A );
			wppa_dbg_q( 'Q-Main-1' );
			wppa_cache_album( 'add', $albs );
			$id = '';
			if ( $albs ) foreach ( $albs as $alb ) {
				$id .= $alb['id'].'.';
			}
			$id = rtrim( $id, '.' );
			wppa( 'start_album', $id );
		}
		wppa( 'supersearch', strip_tags( wppa_get_get( 'supersearch' ) ) );
		$wppa_session['supersearch'] = wppa( 'supersearch' );
		wppa_save_session();
		if ( wppa( 'supersearch' ) ) {
			$ss_info = explode( ',', wppa( 'supersearch' ) );
			if ( $ss_info['0'] == 'a' ) {
				wppa( 'albums_only', true );
			}
			else {
				wppa( 'photos_only', true );
			}
		}
		wppa( 'calendar', strip_tags( wppa_get_get( 'calendar' ) ) );
		wppa( 'caldate', strip_tags( wppa_get_get( 'caldate' ) ) );
		wppa( 'is_inverse', wppa_get_get( 'inv' ) );

//		if ( ! isset( $_REQUEST['album'] ) && ! isset( $_REQUEST['wppa-album'] ) ) {
//			wppa_dbg_msg( 'No album spec' . ( wppa_is_virtual() ? ' on virtual album' : ' on real album (0)' ) , 'red', 'force' );
//		}
	}

	// 2. wppa_albums is called directly. Assume any arg. If not, no worry, system defaults are used == generic
	elseif ( $id != '' || $type != '' || $size != '' || $align != '' ) {
		// Do NOT Set internal defaults here, they may be set before the call

		// Interprete function args
		if ( $type == 'album' ) {
		}
		elseif ( $type == 'cover' ) {
			wppa( 'is_cover', true );
		}
		elseif ( $type == 'slide' ) {
			wppa( 'is_slide', true );
		}
		elseif ( $type == 'slideonly' ) {
			wppa( 'is_slideonly', true );
		}

		if ( $type == 'photo' || $type == 'mphoto' || $type == 'slphoto' ) {	// Any type of single photo? id given is photo id
			if ( $id ) wppa( 'single_photo', $id );
		}
		else {																	// Not single photo: id given is album id
			if ( $id ) wppa( 'start_album', $id );
		}
	}

	// 3. The filter supplied the data
	else {
		if ( wppa( 'bestof' ) ) {
			$args = wppa( 'bestof_args' );
			wppa_bestof_box ( $args );
			$out = wppa( 'out' );
			wppa_reset_occurrance();
			return $out;
		}
		elseif ( wppa( 'is_landing' ) && ! wppa( 'src' ) ) {
			wppa_dbg_msg( 'Nothing to do...' );
			wppa_reset_occurrance();
			return '';	// Do nothing on a landing page without a querystring while it is also not a search operation
		}
		elseif ( wppa( 'is_autopage' ) ) {
			$photo = $wpdb->get_var( $wpdb->prepare( "SELECT `id` FROM `".WPPA_PHOTOS."` WHERE `page_id` = %d LIMIT 1", get_the_ID() ) );
			wppa( 'single_photo', $photo );
			wppa_dbg_q( 'Q-main-2' );
			if ( ! wppa( 'single_photo' ) ) {
				wppa_dbg_msg( 'No photo found for page '.get_the_ID(), 'red', 'force' );
				wppa_reset_occurrance();
				return '';	// Give up
			}
			$type = wppa_opt( 'auto_page_type' );
			switch ( $type ) {
				case 'photo':
					break;
				case 'mphoto':
					wppa( 'is_mphoto', true );
					break;
				case 'slphoto':
					wppa( 'is_slide', true );
					wppa( 'start_photo', wppa( 'single_photo' ) );
					wppa( 'is_single', true );
					break;
				default:
					wppa_dbg_msg( 'Unimplemented type autopage display: '.$type, 'red', 'force' );
			}
		}
	}

	// Convert any keywords and / or names to numbers
	// Search for album keyword
	if ( wppa( 'start_album' ) && ! wppa_is_int( wppa( 'start_album' ) ) ) {
		if ( substr( wppa( 'start_album' ), 0, 1 ) == '#' ) {		// Keyword
			$keyword = wppa( 'start_album' );
			if ( strpos( $keyword, ',' ) ) $keyword = substr( $keyword, 0, strpos( $keyword, ',' ) );
			switch ( $keyword ) {		//	( substr( wppa( 'start_album'], 0, 5 ) ) {
				case '#last':				// Last upload
					$id = wppa_get_youngest_album_id();
					if ( wppa( 'is_cover' ) ) {	// To make sure the ordering sequence is ok.
						$temp = explode( ',', wppa( 'start_album' ) );
						if ( isset( $temp['1'] ) ) wppa( 'last_albums_parent', $temp['1'] );
						else wppa( 'last_albums_parent', '0' );
						if ( isset( $temp['2'] ) ) wppa( 'last_albums', $temp['2'] );
						else wppa( 'last_albums', false );
					}
					else {		// Ordering seq is not important, convert to album enum
						$temp = explode( ',', wppa( 'start_album' ) );
						if ( isset( $temp['1'] ) ) $parent = wppa_album_name_to_number( $temp['1'] );
						else $parent = '0';
						if ( $parent === false ) return;
						if ( isset( $temp['2'] ) ) $limit = $temp['2'];
						else $limit = false;
						if ( $limit ) {
							if ( $parent ) {
								if ( $limit ) {
									$q = $wpdb->prepare( "SELECT * FROM `".WPPA_ALBUMS."` WHERE `a_parent` = %s ORDER BY `timestamp` DESC LIMIT %d", $parent, $limit );
								}
								else {
									$q = $wpdb->prepare( "SELECT * FROM `".WPPA_ALBUMS."` WHERE `a_parent` = %s ORDER BY `timestamp` DESC", $parent );
								}
							}
							else {
								if ( $limit ) {
									$q = $wpdb->prepare( "SELECT * FROM `".WPPA_ALBUMS."` ORDER BY `timestamp` DESC LIMIT %d", $limit );
								}
								else {
									$q = "SELECT * FROM `".WPPA_ALBUMS."` ORDER BY `timestamp` DESC";
								}
							}
							$albs = $wpdb->get_results( $q, ARRAY_A );
							wppa_dbg_q( 'Q-main-3' );
							wppa_cache_album( 'add', $albs );
							if ( is_array( $albs ) ) foreach ( array_keys( $albs ) as $key ) $albs[$key] = $albs[$key]['id'];
							$id = implode( '.', $albs );
						}
					}
					break;
				case '#topten':
					$temp = explode( ',', wppa( 'start_album' ) );
					$id = isset( $temp[1] ) ? $temp[1] : '0';
					$cnt = wppa_opt( 'topten_count' );
					if ( isset( $temp[2] ) ) {
						if ( $temp[2] > 0 ) {
							$cnt = $temp[2];
						}
					}
					wppa( 'topten_count', $cnt );
					wppa( 'is_topten', true );
					if ( wppa( 'is_cover' ) ) {
						wppa_dbg_msg( 'A topten album has no cover. ' . wppa( 'start_album' ), 'red', 'force' );
						wppa_reset_occurrance();
						return;	// Give up this occurence
					}
					if ( isset( $temp[3] ) ) {
						if ( $temp[3] == 'medals' ) {
							wppa( 'medals_only', true );
						}
					}
					break;
				case '#lasten':
					$temp = explode( ',', wppa( 'start_album' ) );
					$id = isset( $temp[1] ) ? $temp[1] : '0';
					wppa( 'lasten_count', isset( $temp[2] ) ? $temp[2] : wppa_opt( 'lasten_count' ) );
					wppa( 'is_lasten', true );
					if ( wppa( 'is_cover' ) ) {
						wppa_dbg_msg( 'A lasten album has no cover. ' . wppa( 'start_album' ), 'red', 'force' );
						wppa_reset_occurrance();
						return;	// Give up this occurence
					}
					break;
				case '#comten':
					$temp = explode( ',', wppa( 'start_album' ) );
					$id = isset( $temp[1] ) ? $temp[1] : '0';
					wppa( 'comten_count', isset( $temp[2] ) ? $temp[2] : wppa_opt( 'comten_count' ) );
					wppa( 'is_comten', true );
					if ( wppa( 'is_cover' ) ) {
						wppa_dbg_msg( 'A comten album has no cover. ' . wppa( 'start_album' ), 'red', 'force' );
						wppa_reset_occurrance();
						return;	// Give up this occurence
					}
					break;
				case '#featen':
					$temp = explode( ',', wppa( 'start_album' ) );
					$id = isset( $temp[1] ) ? $temp[1] : '0';
					wppa( 'featen_count', isset( $temp[2] ) ? $temp[2] : wppa_opt( 'featen_count' ) );
					wppa( 'is_featen', true );
					if ( wppa( 'is_cover' ) ) {
						wppa_dbg_msg( 'A featen album has no cover. ' . wppa( 'start_album' ), 'red', 'force' );
						wppa_reset_occurrance();
						return;	// Give up this occurence
					}
					break;
				case '#related':
					$temp = explode( ',', wppa( 'start_album' ) );
					$type = isset( $temp[1] ) ? $temp[1] : 'tags';	// tags is default type
					wppa( 'related_count', isset( $temp[2] ) ? $temp[2] : wppa_opt( 'related_count' ) );
					wppa( 'is_related', $type );

					$data = wppa_get_related_data();

					if ( $type == 'tags' ) {
						wppa( 'is_tag', $data );
					}
					if ( $type == 'desc' ) {
						wppa( 'src', true );
						wppa( 'searchstring', str_replace( ';', ',', $data ) );
						wppa( 'photos_only', true );
					}
					wppa( 'photos_only', true );
					$id = '0';
					break;
				case '#tags':

					// See if they did not use the #cat / #tags combination in wrong sequence order
					$seppos = strpos( wppa( 'start_album' ), '|' );
					if ( $seppos !== false ) {
						wppa_dbg_msg( 'Syntax error in shortcode attribute album=. Expected: album="#cat,...|#tags,...", seen: album="' . wppa( 'start_album' ) . '"', 'red', 'force' );
						wppa_reset_occurrance();
						return;
					}
					wppa( 'is_tag', wppa_sanitize_tags( substr( wppa( 'start_album' ), 6 ), true ) );
					$id = '0';
					wppa( 'photos_only', true );
					break;
				case '#cat':

					// See if the #cat,cat|#tags,tag special case has been used
					$seppos = strpos( wppa( 'start_album' ), '|' );
					if ( $seppos !== false ) {

						// Yes, process the second part, the #tags clause
						if ( substr( wppa( 'start_album' ), $seppos, 7 ) != '|#tags,' ) {
							wppa_dbg_msg( 'Syntax error in shortcode attribute album=. Expected: album="#cat,...|#tags,...", seen: album="' . wppa( 'start_album' ) . '"', 'red', 'force' );
							wppa_reset_occurrance();
							return; // Forget this occurrance
						}
						wppa( 'is_tag', wppa_sanitize_tags( substr( wppa( 'start_album' ), $seppos + 7 ), true ) );
						wppa( 'photos_only', true );
						wppa( 'start_album', substr( wppa( 'start_album' ), 0, $seppos ) );
					}

					$cats = substr( wppa( 'start_album' ), 5 );
					$cats = trim( wppa_sanitize_tags( $cats, true ), ',;' );

					wppa( 'is_cat', $cats );

					if ( ! $cats ) {
						wppa_dbg_msg( 'Missing cat #cat album spec: ' . wppa( 'start_album' ), 'red', 'force' );
						wppa_reset_occurrance();
						return;	// Forget this occurrance
					}

					// Get all albums and cache its data
					$albs = $wpdb->get_results( "SELECT * FROM `".WPPA_ALBUMS."`", ARRAY_A );
					wppa_dbg_q( 'Q-main-4' );
					wppa_cache_album( 'add', $albs );

					// $cats is not empty. If it contains a , all cats must be met ( AND case )
					// It may contain a ; any cat must be met

					// AND case
					if ( strpos( $cats, ',' ) !== false ) {

						// Do not accept a mix of , and ; Convert ; to ,
						$cats = str_replace( ';', ',', $cats );
						$cats = explode( ',', $cats );
						$id = '';

						if ( $albs ) foreach ( $albs as $alb ) {
							$albcats = explode( ',', $alb['cats'] );

							// Assume in
							$in = true;

							// Test all required cats against album cats array
							foreach( $cats as $cat ) {
								if ( ! in_array( $cat, $albcats, true ) ) {
									$in = false;
								}
							}

							if ( $in ) {
								$id .= $alb['id'].'.';
							}
						}
					}

					// OR case
					else {
						$cats = explode( ';', $cats );
						$id = '';

						if ( $albs ) foreach ( $albs as $alb ) {
							$albcats = explode( ',', $alb['cats'] );

							// Assume out
							$in = false;

							// Test all possible cats against album cats array
							foreach( $cats as $cat ) {
								if ( in_array( $cat, $albcats, true ) ) {
									$in = true;
								}
							}

							if ( $in ) {
								$id .= $alb['id'].'.';
							}
						}
					}

					// Remove possible trailing dot
					$id = rtrim( $id, '.' );

					// Nothing found?
					if ( ! $id ) {
						$id = '-9';
					}

					// Add children?
					if ( wppa_switch( 'cats_inherit' ) ) {
						$id = wppa_alb_to_enum_children( $id );
					}
					break;
				case '#owner':
					$temp = explode( ',', wppa( 'start_album' ) );
					$owner = isset( $temp[1] ) ? $temp[1] : '';
					if ( $owner == '#me' ) {
						if ( is_user_logged_in() ) $owner = wppa_get_user();
						else {	// User not logged in, ignore shortcode
							wppa_reset_occurrance();
							return;	// Forget this occurrance
						}
					}
					if ( ! $owner ) {
						wppa_dbg_msg( 'Missing owner in #owner album spec: ' . wppa( 'start_album' ), 'red', 'force' );
						wppa_reset_occurrance();
						return;	// Forget this occurrance
					}
					$parent = isset( $temp[2] ) ? wppa_album_name_to_number( $temp[2] ) : '0';
					if ( $parent === false ) return;
					if ( ! $parent ) $parent = '-1.0';
					if ( $parent ) {	// Valid parent spec
						$parent_arr = explode( '.', wppa_expand_enum( $parent ) );
						$id = wppa_alb_to_enum_children( $parent );

						// Verify all albums are owned by $owner and are directly under a parent album
						$id = wppa_expand_enum( $id );
						$albs = explode( '.', $id );
						if ( $albs ) foreach( array_keys( $albs ) as $idx ) {
							if (
								( wppa_get_album_item( $albs[$idx], 'owner' ) != $owner ) ||
								( ! in_array( wppa_get_album_item( $albs[$idx], 'a_parent' ), $parent_arr ) )
								) {
								unset( $albs[$idx] );
							}
						}
						$id = implode ( '.', $albs );
						if ( ! $id ) {
				$id = '-9';	// Force nothing found
			//				wppa_reset_occurrance();
			//				return;	// No children found
						}
					}
					wppa( 'is_owner', $owner );
					break;
				case '#upldr':
					$temp = explode( ',', wppa( 'start_album' ) );
					$owner = isset( $temp[1] ) ? $temp[1] : '';
					if ( $owner == '#me' ) {
						if ( is_user_logged_in() ) $owner = wppa_get_user();
						else {	// User not logged in, ignore shortcode
							wppa_reset_occurrance();
							return;	// Forget this occurrance
						}
					}
					if ( ! $owner ) {
						wppa_dbg_msg( 'Missing owner in #upldr album spec: ' . wppa( 'start_album' ), 'red', 'force' );
						wppa_reset_occurrance();
						return;	// Forget this occurrance
					}
					$parent = isset( $temp[2] ) ? wppa_album_name_to_number( $temp[2] ) : '0';
					if ( $parent === false ) return;	// parent specified but not a valid value
					if ( $parent ) {	// Valid parent spec
						$id = wppa_alb_to_enum_children( wppa_expand_enum( $parent ) );
						if ( ! $id ) {
							wppa_reset_occurrance();
							return;	// No children found
						}
					}
					else {				// No parent spec
						$id = '0';
					}
					wppa( 'is_upldr', $owner );
					wppa( 'photos_only', true );
					break;
				case '#all':
					$id = '-2';
					break;
				default:
					wppa_dbg_msg( 'Unrecognized album keyword found: ' . wppa( 'start_album' ), 'red', 'force' );
					wppa_reset_occurrance();
					return;	// Forget this occurrance
			}
			wppa( 'start_album', $id );
		}
	}

	// See if the album id is a name and convert it if possible
	wppa( 'start_album', wppa_album_name_to_number( wppa( 'start_album' ) ) );
	if ( wppa( 'start_album' ) === false ) {
		wppa_reset_occurrance();
		return;
	}

	// Also for parents
	wppa( 'last_albums_parent', wppa_album_name_to_number( wppa( 'last_albums_parent' ) ) );
	if ( wppa( 'last_albums_parent' ) === false ) {
		wppa_reset_occurrance();
		return;
	}

	// Check if album is valid
	if ( strpos( wppa( 'start_album' ), '.' ) !== false ) {	// Album may be enum
		if ( ! wppa_series_to_array( wppa( 'start_album' ) ) ) { 	// Syntax error
			wppa_reset_occurrance();
			return;
		}
	}

	// Album must be numeric
	elseif ( wppa( 'start_album' ) && ! is_numeric( wppa( 'start_album' ) ) ) {
		wppa_stx_err( 'Unrecognized Album identification found: ' . wppa( 'start_album' ) );
		wppa_reset_occurrance();
		return;	// Forget this occurrance
	}

	// Album must exist
	elseif ( wppa( 'start_album' ) > '0' ) {	// -2 is #all
		if ( ! wppa_album_exists( wppa( 'start_album' ) ) ) {
			wppa_stx_err( 'Album does not exist: ' . wppa( 'start_album' ) );
			wppa_reset_occurrance();
			return;	// Forget this occurrance
		}
	}

	// See if the photo id is a keyword and convert it if possible
	if ( wppa( 'single_photo' ) && ! is_numeric( wppa( 'single_photo' ) ) ) {
		if ( substr( wppa( 'single_photo' ), 0, 1 ) == '#' ) {		// Keyword
			switch ( wppa( 'single_photo' ) ) {
				case '#potd':				// Photo of the day
					$t = wppa_get_potd();
					if ( is_array( $t ) ) $id = $t['id'];
					else $id = '0';
					break;
				case '#last':				// Last upload
					$id = wppa_get_youngest_photo_id();
					break;
				default:
					wppa_dbg_msg( 'Unrecognized photo keyword found: ' . wppa( 'single_photo' ), 'red', 'force' );
					wppa_reset_occurrance();
					return;	// Forget this occurrance
			}
			wppa( 'single_photo', $id );
		}
	}

	// See if the photo id is a name and convert it if possible
	if ( wppa( 'single_photo' ) && ! is_numeric( wppa( 'single_photo' ) ) ) {
		if ( substr( wppa( 'single_photo' ), 0, 1 ) == '$' ) {		// Name
			$id = wppa_get_photo_id_by_name( substr( wppa( 'single_photo' ), 1 ) );
			if ( $id > '0' ) wppa( 'single_photo', $id );
			else {
				wppa_dbg_msg( 'Photo name not found: ' . wppa( 'single_photo' ), 'red', 'force' );
				wppa_reset_occurrance();
				return;	// Forget this occurrance
			}
		}
	}

	// Size and align
	if ( is_numeric( $size ) ) {
		wppa( 'fullsize', $size );
	}
	elseif ( $size == 'auto' ) {
		wppa( 'auto_colwidth', true );
	}
	if ( $align == 'left' || $align == 'center' || $align == 'right' ) {
		wppa( 'align', $align );
	}

	// Empty related shortcode?
	if ( wppa( 'is_related' ) ) {
		$thumbs = wppa_get_thumbs();
		if ( empty( $thumbs ) ) {
			wppa_errorbox( __( 'No related photos found.', 'wp-photo-album-plus') );
			$result = wppa( 'out' );
			wppa_reset_occurrance();	// Forget this occurrance
			return $result;
		}
	}

	// Subsearch or rootsearch?
	if ( wppa( 'occur' ) == '1' && $wppa_session['has_searchbox'] && ! wppa( 'in_widget' ) ) {

		// Is it a search now?
		if ( wppa( 'src' ) ) {

			// Is the subsearch box checked?
			wppa( 'is_subsearch', wppa_get_get( 'subsearch' ) || wppa_get_post( 'subsearch' ) );

			// Is the rootsearch box checked?
			wppa( 'is_rootsearch', wppa_get_get( 'rootsearch' ) || wppa_get_post( 'rootsearch' ) );

			// Is it even a forced root search?
			if ( isset( $_REQUEST['wppa-forceroot'] ) ) {
				$wppa_session['search_root'] = strval( intval( $_REQUEST['wppa-forceroot'] ) );
				wppa( 'is_rootsearch', true );
				wppa( 'start_album', strval( intval( $_REQUEST['wppa-forceroot'] ) ) );
				wppa_save_session();
			}

			// No rootsearch, forget previous root
			if ( ! wppa( 'is_rootsearch' ) ) {
				$wppa_session['search_root'] = '0';
				wppa_save_session();
			}

		}

		// It is not a search now
		else {

			// Find new potential searchroot
			if ( isset( $_REQUEST['wppa-searchroot'] ) ) {
				wppa( 'start_album', strval( intval( $_REQUEST['wppa-searchroot'] ) ) );
			}

			// Update session with new searchroot
			$wppa_session['search_root'] = wppa( 'start_album' );
			wppa_save_session();

		}

		// Update searchroot in search boxes
		$rt = $wppa_session['search_root'];
		if ( ! $rt ) $rt = '0';	// must be non-empty string
		wppa_add( 'src_script', 'jQuery(document).ready(function(){wppaUpdateSearchRoot( \'' . esc_js( wppa_display_root( $rt ) ) . '\', \'' . $rt . '\' )});' );

		// If not search forget previous results
		if ( ! wppa( 'src' )  ) {
			$wppa_session['use_searchstring'] = '';
			$wppa_session['display_searchstring'] = '';
			wppa_save_session();
			wppa_add( 'src_script', "\n" . 'jQuery(document).ready(function(){wppaClearSubsearch()});' );
		}
		else { // Enable subbox
			wppa_add( 'src_script', 'jQuery(document).ready(function(){wppaEnableSubsearch()});' );
		}
	}

	// Is it url?
	if ( wppa( 'is_url' ) ) {
		if ( wppa_photo_exists( wppa( 'single_photo' ) ) ) {
			wppa_out( wppa_get_hires_url( wppa( 'single_photo' ) ) );
		}
		else {
			wppa_dbg_msg( sprintf( 'Photo %s not found', wppa( 'single_photo' ) ), 'red', 'force' );
		}
	}
	// Is is a stereo settings box?
	elseif ( wppa( 'is_stereobox' ) ) {
		wppa_stereo_box();
	}
	// Is it the search box?
	elseif ( wppa( 'is_searchbox' ) ) {
		wppa_search_box( '', wppa( 'may_sub' ), wppa( 'may_root' ) );
	}
	// Is it the superview box?
	elseif ( wppa( 'is_superviewbox' ) ) {
		wppa_superview_box( wppa( 'start_album' ) );
	}
	// Is it the multitag box?
	elseif ( wppa( 'is_multitagbox' ) ) {
		wppa_multitag_box( wppa( 'tagcols' ), wppa( 'taglist' ) );
	}
	// Is it the tagcloud box?
	elseif ( wppa( 'is_tagcloudbox' ) ) {
		wppa_tagcloud_box( wppa( 'taglist' ), wppa_opt( 'tagcloud_min' ), wppa_opt( 'tagcloud_max' ) );
	}
	// Is it an upload box?
	elseif ( wppa( 'is_upload' ) ) {
		wppa_upload_box();
	}
	// Is it a supersearch box?
	elseif ( wppa( 'is_supersearch' ) ) {
		wppa_supersearch_box();
	}
	// Is it newstyle single photo mediastyle?
	elseif ( wppa( 'is_mphoto' ) == '1' ) {
		if ( wppa( 'is_autopage' ) ) wppa_auto_page_links( 'top' );
		wppa_mphoto();
		if ( wppa( 'is_autopage' ) ) wppa_auto_page_links( 'bottom' );
	}
	// Is it newstyle single photo plain?
	elseif ( wppa_page( 'oneofone' ) ) {
		if ( wppa( 'is_autopage' ) ) wppa_auto_page_links( 'top' );
		wppa_sphoto();
		if ( wppa( 'is_autopage' ) ) wppa_auto_page_links( 'bottom' );
	}
	// Is it the calendar?
	elseif ( wppa( 'is_calendar' ) ) {
		wppa_calendar_box();
	}
	// The normal case
	else {
		if ( function_exists( 'wppa_theme' ) ) {
			if ( wppa( 'is_autopage' ) ) wppa_auto_page_links( 'top' );
			wppa_theme();	// Call the theme module
			if ( wppa( 'is_autopage' ) ) wppa_auto_page_links( 'bottom' );
		}
		else wppa_out( '<span style="color:red">ERROR: Missing function wppa_theme(), check the installation of WPPA+. Remove customized wppa_theme.php</span>' );
		global $wppa_version;
		$expected_version = '6-3-14-000';
		if ( $wppa_version != $expected_version ) {
			wppa_dbg_msg( 'WARNING: customized wppa-theme.php is out of rev. Expected version: ' . $expected_version . ' found: ' . $wppa_version, 'red' );
		}
	}

	// Done
	$out = str_replace( 'w#location', wppa( 'geo' ), wppa( 'out' ) );

	// Reset
	wppa_reset_occurrance();
	return $out;
}

function wppa_album_name_to_number( $xalb ) {
	$xalb = strip_tags( $xalb );
	if ( $xalb && ! wppa_is_int( $xalb ) ) {
		if ( substr( $xalb, 0, 1 ) == '$' ) {		// Name
			$id = wppa_get_album_id_by_name( substr( $xalb, 1 ), 'report_dups' );
			if ( $id > '0' ) return $id;
			elseif ( $id < '0' ) {
				wppa_dbg_msg( 'Duplicate album names found: '.$xalb, 'red', 'force' );
				wppa_reset_occurrance();
				return false;	// Forget this occurrance
			}
			else {
				wppa_dbg_msg( 'Album name not found: '.$xalb, 'red', 'force' );
				wppa_reset_occurrance();
				return false;	// Forget this occurrance
			}
		}
		else return $xalb; // Is album enum
	}
	else return $xalb; // Is non zero integer
}

function wppa_get_related_data() {
global $wpdb;
	$pagid = wppa_get_the_id();
	$data = $wpdb->get_var( "SELECT `post_content` FROM `".$wpdb->posts."` WHERE `ID` = ".$pagid );
	wppa_dbg_q( 'Q-reld' );
	$data = str_replace( array( ' ', ',', '.', "\t", "\r", "0", "x0B", "\n" ), ';', $data );
	$data = strip_tags( $data );
	$data = strip_shortcodes( $data );
	$data = wppa_sanitize_tags( $data, true );
	$data = trim( $data, "; \t\n\r\0\x0B" );
	return $data;
}

// Determine in wich theme page we are, Album covers, Thumbnails or slideshow
function wppa_page( $page ) {

	if ( wppa_in_widget() ) {
		$occur = wppa_get_get( 'woccur' );
	}
	else {
		$occur = wppa_get_get( 'occur' );
	}

	$ref_occur = wppa_in_widget() ? wppa( 'widget_occur' ) : wppa( 'occur' );

	if ( wppa( 'is_slide' ) ) $cur_page = 'slide';				// Do slide or single when explixitly on
	elseif ( wppa( 'is_slideonly' ) ) $cur_page = 'slide';		// Slideonly is a subset of slide
	elseif ( is_numeric( wppa( 'single_photo' ) ) ) $cur_page = 'oneofone';
	else $cur_page = 'albums';

	if ( $cur_page == $page ) return true; else return false;
}

// loop album
function wppa_get_albums() {
global $wpdb;
global $wppa_session;

	wppa_dbg_msg( 'get_albums entered: ' . wppa( 'mocc' ) . ' Start_album=' . wppa('start_album') . ', Cover=' . wppa( 'is_cover' ) );

	if ( wppa( 'is_topten' ) ) 	return false;
	if ( wppa( 'is_lasten' ) ) 	return false;
	if ( wppa( 'is_comten' ) ) 	return false;
	if ( wppa( 'is_featen' ) ) 	return false;
	if ( wppa( 'is_tag' ) ) 		return false;
	if ( wppa( 'photos_only' ) ) return false;

	if ( wppa( 'src' ) && wppa_switch( 'photos_only' ) ) return false;
	if ( wppa( 'is_owner' ) && ! wppa( 'start_album' ) ) return false; 	// No owner album( s )

	if ( wppa( 'calendar' ) == 'exifdtm' ) return false;
	if ( wppa( 'calendar' ) == 'timestamp' ) return false;
	if ( wppa( 'calendar' ) == 'modified' ) return false;

	// Supersearch?
	if ( wppa( 'supersearch' ) ) {
		$ss_data = explode( ',', wppa( 'supersearch' ) );
		$data = $ss_data['3'];
		switch ( $ss_data['1'] ) {

			// Category
			case 'c':
				$catlist 	= wppa_get_catlist();
				if ( strpos( $data, '.' ) ) {
					$temp = explode( '.', $data );
					$ids = $catlist[$temp['0']]['ids'];
					$i = '1';
					while ( $i < count( $temp ) ) {
						$ids = array_intersect( $ids, $catlist[$temp[$i]]['ids'] );
						$i++;
					}
				}
				else {
					$ids 	= $catlist[$data]['ids'];
				}
				if ( empty( $ids ) ) {
					$ids = array( '0' );	// Dummy
				}
				$query 		= "SELECT * FROM `" . WPPA_ALBUMS . "` WHERE `id` IN (" . implode( ',',$ids ) . ")";
				$albums 	= $wpdb->get_results( $query, ARRAY_A );
				wppa_dbg_q( 'Q_SS' );
				break;

			// Name. Name is converted to number or enum
			case 'n':
				$query 		= $wpdb->prepare( "SELECT * FROM `" . WPPA_ALBUMS . "` WHERE `name` = %s", $data );
				$albums 	= $wpdb->get_results( $query, ARRAY_A );
				wppa_dbg_q( 'Q_SS' );
				break;

			// Text
			case 't':
				if ( strpos( $data, '.' ) ) {
					$temp 		= explode( '.', $data );
					$query 		= $wpdb->prepare( "SELECT * FROM `" . WPPA_INDEX . "` WHERE `slug` = %s", $temp['0'] );
					$indexes 	= $wpdb->get_row( $query, ARRAY_A );
					wppa_dbg_q( 'Q_SS' );
					$ids 		= explode( '.', wppa_expand_enum( $indexes['albums'] ) );
					$i = '1';
					while ( $i < count( $temp ) ) {
						$query 		= $wpdb->prepare( "SELECT * FROM `" . WPPA_INDEX . "` WHERE `slug` = %s", $temp[$i] );
						$indexes 	= $wpdb->get_row( $query, ARRAY_A );
						wppa_dbg_q( 'Q_SS' );
						$ids 		= array_intersect( $ids, explode( '.', wppa_expand_enum( $indexes['albums'] ) ) );
						$i++;
					}
				}
				else {
					$query 		= $wpdb->prepare( "SELECT * FROM `" . WPPA_INDEX . "` WHERE `slug` = %s", $data );
					$indexes 	= $wpdb->get_row( $query, ARRAY_A );
					wppa_dbg_q( 'Q_SS' );
					$ids 		= explode( '.', wppa_expand_enum( $indexes['albums'] ) );
				}
				if ( empty( $ids ) ) {
					$ids = array( '0' ); 	// Dummy
				}
				$query 		= "SELECT * FROM `" . WPPA_ALBUMS . "` WHERE `id` IN (" . implode( ',', $ids ) . ")";
				$albums 	= $wpdb->get_results( $query, ARRAY_A );
				wppa_dbg_q( 'Q_SS' );
				break;
		}
	}

	// Search?
	elseif ( wppa( 'src' ) ) {
		$final_array = array();
		$chunks = explode( ',', stripslashes( strtolower( wppa( 'searchstring' ) ) ) );
		// all chunks
		foreach ( $chunks as $chunk ) if ( strlen( trim( $chunk ) ) ) {
			$words = wppa_index_raw_to_words( $chunk );
			$album_array = array();
			// all words in the searchstring
			foreach ( $words as $word ) {
				$word = trim( $word );
				if ( strlen( $word ) > 1 ) {
					if ( strlen( $word ) > 10 ) $word = substr( $word, 0, 10 );
					if ( wppa_switch( 'wild_front' ) ) {
						$aidxs = $wpdb->get_results( "SELECT `slug`, `albums` FROM `".WPPA_INDEX."` WHERE `slug` LIKE '%".$word."%'", ARRAY_A );
					}
					else {
						$aidxs = $wpdb->get_results( "SELECT `slug`, `albums` FROM `".WPPA_INDEX."` WHERE `slug` LIKE '".$word."%'", ARRAY_A );
					}
					$albums = '';
					if ( $aidxs ) {
						foreach ( $aidxs as $ai ) {
							$albums .= $ai['albums'].',';
						}
					}
					$album_array[] = wppa_index_string_to_array( trim( $albums, ',' ) );
				}
			}
			// Must meet all words: intersect photo sets
			foreach ( array_keys( $album_array ) as $idx ) {
				if ( $idx > '0' ) {
					$album_array['0'] = array_intersect( $album_array['0'], $album_array[$idx] );
				}
			}
			// Save partial result
			if ( isset( $album_array['0'] ) ) $final_array = array_merge( $final_array, $album_array['0'] );
		}

		// Compose WHERE clause
		$selection = " `id` = '0' ";
		foreach ( array_keys( $final_array ) as $p ) {
			$selection .= "OR `id` = '".$final_array[$p]."' ";
		}

		// Get them
		$albums = $wpdb->get_results( "SELECT * FROM `" . WPPA_ALBUMS . "` WHERE " . $selection . " " . wppa_get_album_order( '0' ), ARRAY_A );
		wppa_dbg_q( 'Q10' );

		// Exclusive separate albums?
		if ( wppa_switch( 'excl_sep' ) ) {
			foreach ( array_keys( $albums ) as $idx ) {
				if ( wppa_is_separate( $albums[$idx]['id'] ) ) unset ( $albums[$idx] );
			}
		}

		// Rootsearch?
		if ( wppa( 'is_rootsearch' ) ) {
			$root = $wppa_session['search_root'];
			if ( is_array( $albums ) ) {
				$c1=count( $albums );
				foreach ( array_keys ( $albums ) as $idx ) {
					if ( ! wppa_is_ancestor( $root, $albums[$idx]['id'] ) ) unset ( $albums[$idx] );
				}
				$c2=count( $albums );
				wppa_dbg_msg( 'Rootsearch albums:'.$c1.' -> '.$c2 );
			}
		}

		// Check maximum
		if ( is_array( $albums ) && count( $albums ) > wppa_opt( 'max_search_albums' ) && wppa_opt( 'max_search_albums' ) != '0' ) {
			$alert_text = sprintf( 	__( 'There are %s albums found. Only the first %s will be shown. Please refine your search criteria.' , 'wp-photo-album-plus'),
									count( $albums ),
									wppa_opt( 'max_search_albums' )
								);
			wppa_alert( $alert_text );
			foreach ( array_keys( $albums ) as $idx ) {
				if ( $idx >= wppa_opt( 'max_search_albums' ) ) unset ( $albums[$idx] );
			}
		}

		if ( is_array( $albums ) ) wppa( 'any', true );
	}
	else {	// Its not search
		$id = wppa( 'start_album' );
		if ( ! $id ) $id = '0';

		// Do the query
		if ( $id == '-2' ) {	// All albums
			if ( wppa( 'is_cover' ) ) {
				$q = "SELECT * FROM `".WPPA_ALBUMS."` ".wppa_get_album_order();
				wppa_dbg_q( 'Q11d' );
				$albums = $wpdb->get_results( $q, ARRAY_A );
			}
			else $albums = false;
		}
		elseif ( wppa( 'last_albums' ) ) {	// is_cover = true. For the order sequence, see remark in wppa_albums()
			if ( wppa( 'last_albums_parent' ) ) {
				$q = $wpdb->prepare( "SELECT * FROM `".WPPA_ALBUMS."` WHERE `a_parent` = %s ORDER BY `timestamp` DESC LIMIT %d", wppa( 'last_albums_parent' ), wppa( 'last_albums' ) );
			}
			else {
				$q = $wpdb->prepare( "SELECT * FROM `".WPPA_ALBUMS."` ORDER BY `timestamp` DESC LIMIT %d", wppa( 'last_albums' ) );
			}
			wppa_dbg_q( 'Q11a' );
			$albums = $wpdb->get_results( $q, ARRAY_A );
		}
		elseif ( wppa_is_int( $id ) ) {
			if ( wppa( 'is_cover' ) ) {
				$q = $wpdb->prepare( 'SELECT * FROM ' . WPPA_ALBUMS . ' WHERE `id` = %s', $id );
			}
			else {
				$q = $wpdb->prepare( 'SELECT * FROM ' . WPPA_ALBUMS . ' WHERE `a_parent` = %s '. wppa_get_album_order( $id ), $id );
			}
			wppa_dbg_q( 'Q11b' );
			$albums = $wpdb->get_results( $q, ARRAY_A );
			if ( ! wppa( 'is_cover' ) ) {
				wppa_verify_treecounts( $id, 'albums', count( $albums ) );
			}
		}
		elseif ( strpos( $id, '.' ) !== false ) {	// Album enum
			$ids = wppa_series_to_array( $id );
			if ( wppa( 'is_cover' ) ) {
				$q = "SELECT * FROM `".WPPA_ALBUMS."` WHERE `id` = ".implode( " OR `id` = ", $ids )." ".wppa_get_album_order();
			}
			else {
				$q = "SELECT * FROM `".WPPA_ALBUMS."` WHERE `a_parent` = ".implode( " OR `a_parent` = ", $ids )." ".wppa_get_album_order();
			}
			wppa_dbg_q( 'Q11c' );
			wppa_dbg_msg( $q, 'red' );
			$albums = $wpdb->get_results( $q, ARRAY_A );
		}
		else $albums = false;
	}

	// Check for empty albums
	if ( wppa_switch( 'skip_empty_albums' ) ) {
		$user = wppa_get_user();
		if ( is_array( $albums ) ) foreach ( array_keys( $albums ) as $albumkey ) {
			$albumid 	= $albums[$albumkey]['id'];
			$albumowner = $albums[$albumkey]['owner'];
			$treecount 	= wppa_treecount_a( $albums[$albumkey]['id'] );
			$photocount = $treecount['photos'];
			if ( ! $photocount && ! wppa_user_is( 'administrator' ) && $user != $albumowner ) unset( $albums[$albumkey] );
		}
	}

	// Copy data into secondary cache
	if ( $albums ) {
		wppa_cache_album( 'add', $albums );
	}

	wppa( 'album_count', count( $albums ) );
	return $albums;
}

// loop thumbs
function wppa_get_thumbs() {
global $wpdb;
global $thumbs;
global $wppa_session;

	// Log we are in
	wppa_dbg_msg( 	'Get_thumbs entered, mocc = ' . wppa( 'mocc' ) .
					', Start_album=' . wppa( 'start_album' ) . ', Cover=' . wppa( 'is_cover' ) );

	// Done already this occ?
	if ( is_array( $thumbs ) ) {
		wppa_dbg_msg( 'Cached thumbs used' );
		return $thumbs;
	}

	// A cover -> no thumbs
	if ( wppa( 'is_cover' ) ) {
		wppa_dbg_msg( 'Its cover, leave get_thumbs' );
		return false;
	}

	// Albums only -> no thumbs
	if ( wppa( 'albums_only' ) ) {
		wppa_dbg_msg( 'Albums only, leave get_thumbs' );
		return false;
	}

	// Init
	$count_first = true;

	// Start timer
	$time = -microtime( true );

	// Make Album clause
	$fullalb = wppa( 'start_album' );	// See if album is an enumeration or range
	if ( strpos( $fullalb, '.' ) == false ) {
		$album_clause = ' `album` = ' . $fullalb;
	}
	else {
		$ids = wppa_series_to_array( $fullalb );
		$album_clause = ' `album` IN ( ' . implode( ',', $ids ) . ' )';
	}

	// For upload link on thumbarea: if startalbum is a single real album, put it in current album
	if ( wppa_is_int( wppa( 'start_album' ) ) ) {
		wppa( 'current_album', wppa( 'start_album' ) );
	}

	// So far so good
	// Now make the query, dependant of type of selection
	// Init
	$query = '';

	// Single image slideshow?
	if ( wppa( 'start_photo' ) && wppa( 'is_single' ) ) {
		$query = $wpdb->prepare( 'SELECT * FROM `'.WPPA_PHOTOS.'` WHERE `id` = %s', wppa( 'start_photo' ) );
	}

	// Uploader?
	elseif ( wppa( 'is_upldr' ) ) {
		$status = "`status` <> 'pending' AND `status` <> 'scheduled'";
		if ( ! is_user_logged_in() ) $status .= " AND `status` <> 'private'";
		if ( wppa( 'start_album' ) ) {
			$query = $wpdb->prepare( 	"SELECT * FROM `" . WPPA_PHOTOS . "` " .
										"WHERE " . $album_clause . " AND `owner` = %s AND ( ( " . $status . " ) OR `owner` = %s ) " .
										"ORDER BY `timestamp` DESC", wppa( 'is_upldr' ), wppa_get_user() );
			}
		else {
			$query = $wpdb->prepare( 	"SELECT * FROM `".WPPA_PHOTOS."` " .
										"WHERE `owner` = %s AND ( ( " . $status . " ) OR `owner` = %s ) " .
										"ORDER BY `timestamp` DESC", wppa( 'is_upldr' ), wppa_get_user() );
		}
	}

	// Topten?
	elseif ( wppa( 'is_topten' ) ) {
		$max = wppa( 'topten_count' );
		switch ( wppa_opt( 'topten_sortby' ) ) {
			case 'mean_rating':
				$sortby = '`mean_rating` DESC, `rating_count` DESC, `views` DESC';
				break;
			case 'rating_count':
				$sortby = '`rating_count` DESC, `mean_rating` DESC, `views` DESC';
				break;
			case 'views':
				$sortby = '`views` DESC, `mean_rating` DESC, `rating_count` DESC';
				break;
			default:
				wppa_error_message( 'Unimplemented sorting method' );
				$sortby = '';
				break;
		}
		if ( wppa( 'medals_only' ) ) {
			$status = "`status` IN ( 'gold', 'silver', 'bronze' )";
		}
		else {
			$status = "`status` <> 'pending' AND `status` <> 'scheduled'";
		}
		if ( ! is_user_logged_in() ) $status .= " AND `status` <> 'private'";
		if ( wppa( 'start_album' ) ) {
			$query = 	"SELECT * FROM `" . WPPA_PHOTOS . "` " .
						"WHERE " . $album_clause . " AND ( " . $status . " ) " .
						"ORDER BY " . $sortby . " LIMIT " . $max;
		}
		else {
			$query = 	"SELECT * FROM `" . WPPA_PHOTOS . "` " .
						"WHERE ( " . $status . " ) " .
						"ORDER BY " . $sortby . " LIMIT " . $max;
		}
		$count_first = false;
	}

	// Featen?
	elseif ( wppa( 'is_featen' ) ) {
		$max = wppa( 'featen_count' );
		if ( wppa( 'start_album' ) ) {
			$query =  	"SELECT * FROM `".WPPA_PHOTOS."` " .
						"WHERE " . $album_clause . " AND `status` = 'featured' " .
						"ORDER BY RAND( ".wppa_get_randseed()." ) DESC LIMIT ".$max;
		}
		else {
			$query =  	"SELECT * FROM `".WPPA_PHOTOS."` " .
						"WHERE `status` = 'featured' " .
						"ORDER BY RAND( ".wppa_get_randseed()." ) DESC LIMIT ".$max;
		}
		$count_first = false;
	}

	// Lasten?
	elseif ( wppa( 'is_lasten' ) ) {
		$max = wppa( 'lasten_count' );
		$status = "`status` <> 'pending' AND `status` <> 'scheduled'";
		if ( ! is_user_logged_in() ) $status .= " AND `status` <> 'private'";
		$order_by = wppa_switch( 'lasten_use_modified' ) ? 'modified' : 'timestamp';

		// If you want only 'New' photos in the selection, the period must be <> 0;
		if ( wppa_switch ( 'wppa_lasten_limit_new' ) && wppa_opt( 'max_photo_newtime' ) ) {
			$newtime = " `" . $order_by . "` >= ".( time() - wppa_opt( 'max_photo_newtime' ) );
			if ( current_user_can( 'wppa_moderate' ) ) {
				if ( wppa( 'start_album' ) ) {
					$query = 	"SELECT * FROM `" . WPPA_PHOTOS . "` " .
								"WHERE ( " . $album_clause . " ) AND ( " . $newtime ." ) " .
								"ORDER BY `" . $order_by . "` DESC LIMIT " . $max;
				}
				else {
					$query = 	"SELECT * FROM `" . WPPA_PHOTOS . "` " .
								"WHERE " . $newtime . " " .
								"ORDER BY `" . $order_by . "` DESC LIMIT " . $max;
				}
			}
			else {
				if ( wppa( 'start_album' ) ) {
					$query = 	"SELECT * FROM `".WPPA_PHOTOS."` " .
								"WHERE ( " . $album_clause . " ) AND ( " . $status . " ) AND ( " . $newtime . " ) " .
								"ORDER BY `" . $order_by . "` DESC LIMIT " . $max;
				}
				else {
					$query = 	"SELECT * FROM `".WPPA_PHOTOS."` " .
								"WHERE ( " . $status . " ) AND (" . $newtime . ") " .
								"ORDER BY `" . $order_by . "` DESC LIMIT ".$max;
				}
			}
		}

		// No 'New' limitation
		else {
			if ( current_user_can( 'wppa_moderate' ) ) {
				if ( wppa( 'start_album' ) ) {
					$query =  	"SELECT * FROM `" . WPPA_PHOTOS . "` " .
								"WHERE " . $album_clause . " " .
								"ORDER BY `" . $order_by . "` DESC LIMIT " . $max;
				}
				else {
					$query = 	"SELECT * FROM `" . WPPA_PHOTOS . "` " .
								"ORDER BY `" . $order_by . "` DESC LIMIT " . $max;
				}
			}
			else {
				if ( wppa( 'start_album' ) ) {
					$query = 	"SELECT * FROM `" . WPPA_PHOTOS . "` " .
								"WHERE ( " . $album_clause . " ) AND ( " . $status . " ) " .
								"ORDER BY `" . $order_by . "` DESC LIMIT " . $max;
				}
				else {
					$query = 	"SELECT * FROM `".WPPA_PHOTOS."` " .
								"WHERE " . $status . " " .
								"ORDER BY `" . $order_by . "` DESC LIMIT " . $max;
				}
			}
		}
		$count_first = false;
	}

	// Comten?
	elseif ( wppa( 'is_comten' ) ) {
		$alb_ids = wppa( 'start_album' );
		if ( strpos( $alb_ids, '.' ) !== false ) {
			$alb_ids = wppa_series_to_array( $alb_ids );
		}

		// Comments only visible if logged in or not required to log in
		if ( ! wppa_switch( 'comment_view_login' ) || is_user_logged_in() ) {
			$photo_ids = wppa_get_comten_ids( wppa( 'comten_count' ), (array) $alb_ids );
		}
		else {
			$photo_ids = false;
		}

		$status = "`status` <> 'pending' AND `status` <> 'scheduled'";
		if ( ! is_user_logged_in() ) $status .= " AND `status` <> 'private'";

		// To keep the sequence ok ( in sequence of comments desc ), do the queries one by one
		$thumbs = array();
		if ( is_array( $photo_ids ) ) foreach( $photo_ids as $id ) {
			$temp = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `".WPPA_PHOTOS."` WHERE ".$status." AND `id` = %s", $id ), ARRAY_A );
			if ( $temp ) {
				$thumbs[] = $temp;
			}
		}

		wppa( 'any', ! empty ( $thumbs ) );
		wppa( 'thumb_count', empty( $thumbs ) ? '0' : count( $thumbs ) );
		$time += microtime( true );
		wppa_dbg_msg( 	'Get thumbs exit is_comten took ' . $time . ' seconds. ' .
						'Found: ' . wppa( 'thumb_count' ) . ' items. ' .
						'Mem used=' . ceil( memory_get_peak_usage( true ) / ( 1024*1024 ) ) . ' Mb.'
					);
		return $thumbs;
	}

	// Tagcloud or multitag? Tags do not look at album
	elseif ( wppa( 'is_tag' ) ) {

		// Init
		$andor = 'AND';
		if ( strpos( wppa( 'is_tag' ), ';' ) ) $andor = 'OR';

		// Compute status clause for query
		$status = "`status` <> 'pending' AND `status` <> 'scheduled'";
		if ( ! is_user_logged_in() ) $status .= " AND `status` <> 'private'";

		// Define tags clause for query
		$seltags = explode( ',', trim( wppa_sanitize_tags( wppa( 'is_tag' ) ), ',' ) );
		$tags_like = '';
		$first = true;
		foreach ( $seltags as $tag ) {
			if ( ! $first ) {
				$tags_like .= " " . $andor;
			}
			$tags_like .= " `tags` LIKE '%,".$tag.",%'";
			$first = false;
		}

		// Album spec?
		if ( wppa( 'start_album' ) ) {
			$fac = ' AND ' . $album_clause . ' ';
		}
		else {
			$fac = '';
		}

		// Prepare the query
		if ( current_user_can( 'wppa_moderate' ) ) {
			$query = "SELECT * FROM `".WPPA_PHOTOS."` WHERE (".$tags_like." ) ".$fac.wppa_get_photo_order( '0' );
		}
		else {
			$query = "SELECT * FROM `".WPPA_PHOTOS."` WHERE (".$tags_like." ) AND ".$status." ".$fac.wppa_get_photo_order( '0' );
		}
	}

	// Supersearch?
	elseif ( wppa( 'supersearch' ) ) {

		$ss_data = explode( ',', wppa( 'supersearch' ) );
		$data = $ss_data['3'];

		$status = "`status` <> 'pending' AND `status` <> 'scheduled'";
		if ( ! is_user_logged_in() ) $status .= " AND `status` <> 'private'";

		switch ( $ss_data['1'] ) {

			// Name
			case 'n':
				$is = '=';
				if ( substr( $data, -3 ) == '...' ) {
					$data = substr( $data, 0, strlen( $data ) - 3 ) . '%';
					$is = 'LIKE';
				}
				if ( current_user_can( 'wppa_moderate' ) ) {
					$query = $wpdb->prepare( 	"SELECT * FROM `" . WPPA_PHOTOS . "` " .
												"WHERE `name` " . $is . " %s " .
												wppa_get_photo_order( '0' ), $data );
				}
				else {
					$query = $wpdb->prepare( 	"SELECT * FROM `" . WPPA_PHOTOS . "` " .
												"WHERE `name` " . $is . " %s AND " . $status . " " .
												wppa_get_photo_order( '0' ), $data );
				}
				break;

			// Owner
			case 'o':
				if ( current_user_can( 'wppa_moderate' ) ) {
					$query = $wpdb->prepare( 	"SELECT * FROM `" . WPPA_PHOTOS . "` " .
												"WHERE `owner` = %s " .
												wppa_get_photo_order( '0' ), $data );
				}
				else {
					$query = $wpdb->prepare( 	"SELECT * FROM `" . WPPA_PHOTOS . "` " .
												"WHERE `owner` = %s AND " . $status . " " .
												wppa_get_photo_order( '0' ), $data );
				}
				break;

			// Tag
			case 'g':
				$taglist = wppa_get_taglist();
				if ( strpos( $data, '.' ) ) {
					$qtags 	= explode( '.', $data );
					$tagids = $taglist[$qtags['0']]['ids'];
					$i = '0';
					while ( $i < count( $qtags ) ) {
						$tagids = array_intersect( $tagids, $taglist[$qtags[$i]]['ids'] );
						$i++;
					}
				}
				else {
					$tagids 	= $taglist[$data]['ids'];
				}
				if ( count( $tagids ) > '0' ) {
					$query = 	"SELECT * FROM `" . WPPA_PHOTOS . "` " .
								"WHERE ".$status." AND `id` IN (" . implode( ',',$tagids ) . ")";
				}
				break;

			// Text
			case 't':

				// To distinguish items with ellipses, we temporary replace them with ***
				$data = str_replace( '...', '***', $data );
				if ( strpos( $data, '.' ) ) {
					$temp 		= explode( '.', $data );
					$is = '=';
					if ( wppa_opt( 'ss_text_max' ) ) {
						if ( substr( $temp['0'], -3 ) == '***' ) {
							$temp['0'] = substr( $temp['0'], 0, strlen( $temp['0'] ) - 3 ) . '%';
							$is = 'LIKE';
						}
					}
					$query 		= $wpdb->prepare( "SELECT * FROM `" . WPPA_INDEX . "` WHERE `slug` ".$is." %s", $temp['0'] );
					$indexes 	= $wpdb->get_results( $query, ARRAY_A );
					$ids 		= array();
					foreach( $indexes as $item ) {
						$ids 	= array_merge( $ids, explode( '.', wppa_expand_enum( $item['photos'] ) ) );
					}
					$i = '1';
					while ( $i < count( $temp ) ) {
						$is = '=';
						if ( wppa_opt( 'ss_text_max' ) ) {
							if ( substr( $temp[$i], -3 ) == '***' ) {
								$temp[$i] = substr( $temp[$i], 0, strlen( $temp[$i] ) - 3 ) . '%';
								$is = 'LIKE';
							}
						}

						$query 		= $wpdb->prepare( "SELECT * FROM `" . WPPA_INDEX . "` WHERE `slug` ".$is." %s", $temp[$i] );
						$indexes 	= $wpdb->get_results( $query, ARRAY_A );
						$deltaids 	= array();
						foreach( $indexes as $item ) {
							$deltaids 	= array_merge( $deltaids, explode( '.', wppa_expand_enum( $item['photos'] ) ) );
						}

						$ids 		= array_intersect( $ids, $deltaids );
						$i++;
					}
				}
				else {
					$is = '=';
					if ( wppa_opt( 'ss_text_max' ) ) {
						if ( substr( $data, -3 ) == '***' ) {
							$data = substr( $data, 0, strlen( $data ) - 3 ) . '%';
							$is = 'LIKE';
						}
					}
					$query 		= $wpdb->prepare( "SELECT * FROM `" . WPPA_INDEX . "` WHERE `slug` ".$is." %s", $data );
					$indexes 	= $wpdb->get_results( $query, ARRAY_A );
					$ids 		= array();
					foreach( $indexes as $item ) {
						$ids 	= array_merge( $ids, explode( '.', wppa_expand_enum( $item['photos'] ) ) );
					}
				}
				if ( empty( $ids ) ) {
					$ids = array( '0' ); 	// Dummy
				}
				$query = 	"SELECT * FROM `" . WPPA_PHOTOS . "` " .
							"WHERE ".$status." AND `id` IN (" . trim( implode( ',', $ids ), ',' ) . ")";
				break;

			// Iptc
			case 'i':
				$itag 		= str_replace( 'H', '#', $ss_data['2'] );
				$desc 		= $ss_data['3'];
				$query 		= $wpdb->prepare( 	"SELECT * FROM `" . WPPA_IPTC . "` " .
												"WHERE `tag` = %s AND `description` = %s", $itag, $desc );
				$iptclines 	= $wpdb->get_results( $query, ARRAY_A );
				$ids 		= '0';
				if ( is_array( $iptclines ) ) foreach( $iptclines as $item ) {
					$ids .= ','.$item['photo'];
				}
				$query 		= 	"SELECT * FROM `" . WPPA_PHOTOS . "` " .
								"WHERE ".$status." AND `id` IN (" . $ids . ")";
				break;

			// Exif
			case 'e':
				$etag 		= str_replace( 'H', '#', $ss_data['2'] );
				$desc 		= $ss_data['3'];
				$query 		= $wpdb->prepare( 	"SELECT * FROM `" . WPPA_EXIF . "` " .
												"WHERE `tag` = %s AND `description` = %s", $etag, $desc );
				$exiflines 	= $wpdb->get_results( $query, ARRAY_A );
				$ids 		= '0';
				if ( is_array( $exiflines ) ) foreach( $exiflines as $item ) {
					$ids .= ','.$item['photo'];
				}
				$query 		= 	"SELECT * FROM `" . WPPA_PHOTOS . "` " .
								"WHERE ".$status." AND `id` IN (" . $ids . ")";
				break;
		}
	}

	// Search?
	elseif ( wppa( 'src' ) ) {	// Searching

		$status = "`status` <> 'pending' AND `status` <> 'scheduled'";
		if ( ! is_user_logged_in() ) $status .= " AND `status` <> 'private'";

		$searchstring = wppa( 'searchstring' );
		if ( ! empty ( $wppa_session['use_searchstring'] ) ) $searchstring = $wppa_session['use_searchstring'];

		$final_array = array();
		$chunks = explode( ',', stripslashes( strtolower( $searchstring ) ) );

		// all chunks
		foreach ( $chunks as $chunk ) if ( strlen( trim( $chunk ) ) ) {
			$words = wppa_index_raw_to_words( $chunk );
			$photo_array = array();
			// all words in the searchstring
			foreach ( $words as $word ) {
				$word = trim( $word );
				if ( strlen( $word ) > 1 ) {
					if ( strlen( $word ) > 20 ) $word = substr( $word, 0, 20 );
					if ( wppa_switch( 'wild_front' ) ) {
						$pidxs = $wpdb->get_results( "SELECT `slug`, `photos` FROM `".WPPA_INDEX."` WHERE `slug` LIKE '%".$word."%'", ARRAY_A );
					}
					else {
						$pidxs = $wpdb->get_results( "SELECT `slug`, `photos` FROM `".WPPA_INDEX."` WHERE `slug` LIKE '".$word."%'", ARRAY_A );
					}
					wppa_dbg_q( 'Q-IX' );
					$photos = '';
					if ( $pidxs ) {
						foreach ( $pidxs as $pi ) {
							$photos .= $pi['photos'].',';
						}
					}
					$photo_array[] = wppa_index_string_to_array( trim( $photos, ',' ) );
				}
			}
			// Must meet all words: intersect photo sets
			foreach ( array_keys( $photo_array ) as $idx ) {
				if ( $idx > '0' ) {
					$photo_array['0'] = array_intersect( $photo_array['0'], $photo_array[$idx] );
				}
			}
			// Save partial result
			if ( isset( $photo_array['0'] ) ) $final_array = array_merge( $final_array, $photo_array['0'] );

			// Remove dups
			$final_array = array_unique( $final_array );
		}

		// Remove scheduled and pending when not can moderate
		if ( ! current_user_can( 'wppa_moderate' ) ) {
			$needmod = $wpdb->get_results( "SELECT `id` FROM `".WPPA_PHOTOS."` WHERE `status` = 'scheduled' OR `status` = 'pending'", ARRAY_A );
			if ( is_array( $needmod ) ) {
				$delta = array();
				foreach ( $needmod as $item ) {
					$delta[] = $item['id'];
				}
			}
			$final_array = array_diff( $final_array, $delta );
		}

		// Remove private when not logged in
		if ( ! is_user_logged_in() ) {
			$needlogin = $wpdb->get_results( "SELECT `id` FROM `".WPPA_PHOTOS."` WHERE `status` = 'private'", ARRAY_A );
			if ( is_array( $needlogin ) ) {
				$delta = array();
				foreach ( $needlogin as $item ) {
					$delta[] = $item['id'];
				}
			}
			$final_array = array_diff( $final_array, $delta );
		}

		// remove dups from $final_array
		$final_array = array_unique( $final_array );

		// Make album clause
		$alb_clause = '';

		// If rootsearch, the album clause resticts to sub the root
		// else: maybe exclude separates
		// See for rootsearch
		if ( wppa( 'is_rootsearch' ) && isset ( $wppa_session['search_root'] ) ) {

			// Find all albums below root
			$root = $wppa_session['search_root'];
			$root_albs = wppa_expand_enum( wppa_alb_to_enum_children( $root ) );
			$root_albs = str_replace( '.', ',', $root_albs );
			$alb_clause = $root_albs ? ' AND `album` IN ('.$root_albs.') ' : '';
		}

		// exclude separate if required
		elseif ( ! $alb_clause && wppa_switch( 'excl_sep' ) ) {
			$sep_albs = '';
			$temp = $wpdb->get_results( "SELECT * FROM `".WPPA_ALBUMS."` WHERE `a_parent` = '-1'", ARRAY_A );
			if ( ! empty( $temp ) ) {
				$t = array();
				foreach ( $temp as $item ) {
					$t[] = $item['id'];
				}
				$sep_albs = implode( '.', $t );
				$sep_albs = wppa_expand_enum( wppa_alb_to_enum_children( $sep_albs ) );
				$sep_albs = str_replace( '.', ',', $sep_albs );
				$alb_clause = $sep_albs ? ' AND `album` NOT IN ('.$sep_albs.') ' : '';
			}
		}

		// compose photo selection
		if ( ! empty( $final_array ) ) {
			$selection = " `id` IN (";
			$selection .= implode( ',', $final_array );
			$selection .= ") ";
		}
		else {
			$selection = " `id` = '0' ";
		}

		// Construct the query
		$query = "SELECT * FROM `" . WPPA_PHOTOS . "` WHERE " . $selection . $alb_clause . wppa_get_photo_order( '0' );
	}

	// Calendar?
	elseif ( wppa( 'calendar' ) ) {
		if ( wppa( 'start_album' ) ) {
			$alb_clause = " AND `album` IN ( ". str_replace( '.', ',', wppa_expand_enum( wppa( 'start_album' ) ) ) ." ) ";
		}
		else {
			$alb_clause = '';
		}
		switch ( wppa( 'calendar' ) ) {
			case 'exifdtm':
				$selection = "`exifdtm` LIKE '" . strip_tags( wppa( 'caldate' ) ) . "%' AND `status` <> 'pending' AND `status` <> 'scheduled' ";
				$query = 	"SELECT * FROM `" . WPPA_PHOTOS . "` " .
							"WHERE " . $selection . $alb_clause . wppa_get_photo_order( '0' );
				break;

			case 'timestamp':
				$t1 = strval( intval( wppa( 'caldate' ) * 24*60*60 ) );
				$t2 = $t1 + 24*60*60;
				$selection = "`timestamp` >= $t1 AND `timestamp` < $t2 AND `status` <> 'pending' AND `status` <> 'scheduled' ";
				$query = 	"SELECT * FROM `" . WPPA_PHOTOS . "` " .
							"WHERE " . $selection . $alb_clause . wppa_get_photo_order( '0' );
				break;

			case 'modified':
				$t1 = strval( intval( wppa( 'caldate' ) * 24*60*60 ) );
				$t2 = $t1 + 24*60*60;
				$selection = "`modified` >= $t1 AND `modified` < $t2 AND `status` <> 'pending' AND `status` <> 'scheduled' ";
				$query = 	"SELECT * FROM `" . WPPA_PHOTOS . "` " .
							"WHERE " . $selection . $alb_clause . wppa_get_photo_order( '0' );
				break;
		}
	}

	// Normal
	else {

		// Status
		$status = "`status` <> 'pending' AND `status` <> 'scheduled'";
		if ( ! is_user_logged_in() ) $status .= " AND `status` <> 'private'";

		// On which album( s )?
		if ( strpos( wppa( 'start_album' ), '.' ) !== false ) $allalb = wppa_series_to_array( wppa( 'start_album' ) );
		else $allalb = false;

		wppa_dbg_msg( 'Startalbum = ' . wppa( 'start_album' ) );

		// All albums ?
		if ( wppa( 'start_album' ) == -2 ) {
			if ( current_user_can( 'wppa_moderate' ) ) {
				$query = "SELECT * FROM `" . WPPA_PHOTOS . "` " . wppa_get_photo_order( '0' );
			}
			else {
				$query = $wpdb->prepare( 	"SELECT * FROM `".WPPA_PHOTOS."` " .
											"WHERE ( ( " . $status . " ) OR `owner` = %s ) " .
											wppa_get_photo_order( '0' ), wppa_get_user() );
			}
		}

		// Single album ?
		elseif ( wppa_is_int( wppa( 'start_album' ) ) ) {
			if ( current_user_can( 'wppa_moderate' ) ) {
				$query = 	"SELECT * FROM `" . WPPA_PHOTOS . "` " .
							"WHERE `album` = " . wppa( 'start_album' ) . " " .
							wppa_get_photo_order( wppa( 'start_album' ) );
			}
			else {
				$query = $wpdb->prepare( 	"SELECT * FROM `" . WPPA_PHOTOS . "` " .
											"WHERE ( ( " . $status . " ) OR `owner` = %s ) AND `album` = " . wppa( 'start_album' ) . " " .
											wppa_get_photo_order( wppa( 'start_album' ) ), wppa_get_user() );
			}
		}

		// Album enumeration?
		elseif ( is_array( $allalb ) ) {
			$wherealbum = ' `album` IN (' . implode( ',', $allalb ) . ') ';
			if ( current_user_can( 'wppa_moderate' ) ) {
				$query = 	"SELECT * FROM `" . WPPA_PHOTOS . "` " .
							"WHERE " . $wherealbum . " " .
							wppa_get_photo_order( '0' );
			}
			else {
				$query = $wpdb->prepare( 	"SELECT * FROM `" . WPPA_PHOTOS . "` " .
											"WHERE ( ( " . $status . " ) OR `owner` = %s ) AND " . $wherealbum . " " .
											wppa_get_photo_order( '0' ), wppa_get_user() );
			}
		}
	}

	// Anything to look for?
	if ( ! $query ) {

		// Not implemented or impossable shortcode
		return false;
	}

	// Do query and return result after copy result to $thumbs!!
	$thumbs = wppa_do_get_thumbs_query( $query );
	return $thumbs;
}

// Handle the select thumbs query
// @1: The MySql query
// @2: bool. Set to false if the expected count of thumbs is always less than 2500
function wppa_do_get_thumbs_query( $query, $count_first = true ) {
global $wpdb;

	// Anything to do here?
	if ( ! $query ) {
		wppa( 'thumb_count', '0' );
		wppa( 'any', false );
		wppa_dbg_msg( 'Empty query photos', 'red' );
		return false;
	}

	// Init
	$time = -microtime( true );

	// Inverse requested?
	$invers = false;
	if ( wppa( 'is_inverse' ) ) {
		$invers = true;
	}

	// Do we need to get the count first to dicede if we get the full data and probably cache it ?
	if ( $count_first || $invers ) {

		// First get the count of the result
		$count = $wpdb->get_var( str_replace( 'SELECT *', 'SELECT COUNT(*)', $query ) );

		// If less than 2500, get them and cache them
		if ( $count <= 2500 && ! $invers ) {
			$thumbs 	= $wpdb->get_results( $query, ARRAY_A );
			$caching 	= true;
		}

		// If more than 2500, or inverse requested, get the ids only, and do not cache them
		else {
			$query 		= str_replace( 'SELECT *', 'SELECT `id`', $query );
			$thumbs 	= $wpdb->get_results( $query , ARRAY_A );
			$caching 	= false;
		}
	}

	// Need no count first, just do it.
	else {
		$thumbs 	= $wpdb->get_results( $query, ARRAY_A );
		$caching 	= true;
		$count 		= empty( $thumbs ) ? '0' : count( $thumbs );
	}

	// Inverse selection requested?
	if ( $invers ) {
		$all = $wpdb->get_results( "SELECT `id`, `album` FROM `".WPPA_PHOTOS."` ".wppa_get_photo_order( '0' ), ARRAY_A );
		if ( is_array( $thumbs ) ) foreach ( array_keys($thumbs) as $thumbs_key ) {
			foreach ( array_keys($all) as $all_key ) {
				if ( $thumbs[$thumbs_key]['id'] == $all[$all_key]['id'] ) {
					unset( $all[$all_key] );
				}
			}
		}

		// Exclude separate albums?
		if ( wppa_switch( 'excl_sep' ) ) {
			foreach ( array_keys( $all ) as $all_key ) {
				if ( wppa_is_separate( $all[$all_key]['album'] ) ) {
					unset ( $all[$all_key] );
				}
			}
		}

		// Resequence for slideshow pagination
		$thumbs = array();
		if ( ! empty( $all ) ) foreach( $all as $item ) {
			$thumbs[] = $item;
		}
	}

	// Log query
	wppa_dbg_q( 'Q-TG' );
	wppa_dbg_msg( $query );
	wppa( 'thumb_count', $count );
	$time += microtime( true );
	wppa_dbg_msg( 	'Get thumbs query took ' . $time . ' seconds. ' .
					'Found: ' . $count . ' items. ' .
					'Mem used=' . ceil( memory_get_peak_usage( true ) / ( 1024*1024 ) ) . ' Mb.'
				);
	if ( $caching ) {
		wppa_cache_photo( 'add', $thumbs );
	}
	wppa( 'any', ! empty ( $thumbs ) );

	return $thumbs;
}

function wppa_get_all_children( $root ) {
global $wpdb;

	$result = array();
	$albs = $wpdb->get_results( $wpdb->prepare( "SELECT `id` FROM `".WPPA_ALBUMS."` WHERE `a_parent` = %s", $root ), ARRAY_A );
	if ( ! $albs ) return $result;
	foreach ( $albs as $alb ) {
		$result[] = $alb['id'];
		$part = wppa_get_all_children( $alb['id'] );
		if ( $part ) $result = array_merge( $result, $part );
	}
	return $result;
}

// get slide info
function wppa_get_slide_info( $index, $id, $callbackid = '' ) {
global $wpdb;
static $user;

	// Make sure $thumb contains our image data
	$thumb = wppa_cache_thumb( $id );

	if ( ! $user ) $user = wppa_get_user();
	$photo = wppa_get_get( 'photo' );
	$ratingphoto = wppa_get_get( 'rating-id' );

	if ( ! $callbackid ) $callbackid = $id;

	// Process a comment if given for this photo
	$comment_request = ( wppa_get_post( 'commentbtn' ) && ( $id == $photo ) );
	$comment_allowed = ( ! wppa_switch( 'comment_login' ) || is_user_logged_in() );
	if ( wppa_switch( 'show_comments' ) && $comment_request && $comment_allowed ) {
		wppa_do_comment( $id );
	}

	// Find rating
	if ( wppa_switch( 'rating_on' ) && ! wppa( 'is_slideonly' ) && ! wppa( 'is_filmonly' ) ) {

		// Find my ( avg ) rating
		$rats = $wpdb->get_results( $wpdb->prepare( 	"SELECT `value` FROM `".WPPA_RATING."` " .
														"WHERE `photo` = %s AND `user` = %s AND `status` = 'publish'", $id, $user ), ARRAY_A );
		wppa_dbg_q( 'Q33v' );
		if ( $rats ) {
			$n = 0;
			$accu = 0;
			foreach ( $rats as $rat ) {
				$accu += $rat['value'];
				$n++;
			}
			$myrat = $accu / $n;
			$i = wppa_opt( 'rating_prec' );
			$j = $i + '1';
			$myrat = sprintf( '%'.$j.'.'.$i.'f', $myrat );
		}
		else $myrat = '0';

		// Find the avg rating
		$avgrat = wppa_get_rating_by_id( $id, 'nolabel' );
		if ( ! $avgrat ) $avgrat = '0';
		$avgrat .= '|'.wppa_get_rating_count_by_id( $id );

		// Find the dislike count
		$discount = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_RATING."` WHERE `photo` = %s AND `value` = -1 AND `status` = %s", $id, 'publish' ) );

		// Make the discount textual
		$distext = wppa_get_distext( $discount, $myrat );
	}
	else {	// Rating off
		$myrat 		= '0';
		$avgrat 	= '0';
		$discount 	= '0';
		$distext 	= '';
	}


	// Find comments
	$comment = ( wppa_switch( 'show_comments' ) && ! wppa( 'is_filmonly' ) && ! wppa( 'is_slideonly' ) ) ? wppa_comment_html( $id, $comment_allowed ) : '';

	// Get the callback url.
	if ( wppa_switch( 'rating_on' ) ) {
		$url = wppa_get_slide_callback_url( $callbackid );
		$url = str_replace( '&amp;', '&', $url );	// js use
	}
	else {
		$url = '';
	}

	// Find link url, link title and link target
	if ( wppa_in_widget() == 'ss' ) {
		$link = wppa_get_imglnk_a( 'sswidget', $id );
	}
	else {
		$link = wppa_get_imglnk_a( 'slideshow', $id );
	}
	$linkurl = $link['url'];
	$linktitle = $link['title'];
	$linktarget = $link['target'];

	// Find full image style and size
	if ( wppa( 'is_filmonly' ) ) {
		$style_a['style'] = '';
		$style_a['width'] = '';
		$style_a['height'] = '';
	}
	else {
		$style_a = wppa_get_fullimgstyle_a( $id );
	}

	// Find image url
	if ( wppa_switch( 'fotomoto_on' ) && ! wppa_is_stereo( $id ) ) {
		$photourl = wppa_get_hires_url( $id );
	}
	elseif ( wppa_use_thumb_file( $id, $style_a['width'], $style_a['height'] ) && ! wppa_is_stereo( $id ) ) {
		$photourl = wppa_get_thumb_url( $id, '', $style_a['width'], $style_a['height'] );
	}
	else {
		$photourl = wppa_get_photo_url( $id, '', $style_a['width'], $style_a['height'] );
	}

	if ( wppa_has_audio( $id ) ) {
		$photourl = wppa_fix_poster_ext( $photourl, $id );
	}

	// Find iptc data
	$iptc = ( wppa_switch( 'show_iptc' ) && ! wppa( 'is_slideonly' ) && ! wppa( 'is_filmonly' ) ) ? wppa_iptc_html( $id ) : '';

	// Find EXIF data
	$exif = ( wppa_switch( 'show_exif' ) && ! wppa( 'is_slideonly' ) && ! wppa( 'is_filmonly' ) ) ? wppa_exif_html( $id ) : '';

	// Lightbox subtitle
	$doit = false;
	if ( wppa_opt( 'slideshow_linktype' ) == 'lightbox' || wppa_opt( 'slideshow_linktype' ) == 'lightboxsingle' ) $doit = true;	// For fullsize
	if ( wppa_switch( 'filmstrip' ) && wppa_opt( 'film_linktype' ) == 'lightbox' ) {	// For filmstrip?
		if ( ! wppa( 'is_slideonly' ) ) $doit = true;		// Film below fullsize
		if ( wppa( 'film_on' ) ) $doit = true;				// Film explicitly on ( slideonlyf )
	}
	if ( $doit ) {
		$lbtitle = wppa_get_lbtitle( 'slide', $id );
	}
	else $lbtitle = '';

	// Name
	if ( wppa( 'is_filmonly' ) || wppa( 'is_slideonly' ) ) {
		$name = '';
		$fullname = '';
	}
	else {
		$name = esc_js( wppa_get_photo_name( $id ) );
		if ( ! $name ) $name = '&nbsp;';
		$fullname = wppa_get_photo_name( $id, wppa_switch( 'show_full_owner' ), true, true, wppa_switch('wppa_show_full_name') );
		if ( ! $fullname ) $fullname = '&nbsp;';
	}

	// Shareurl
	if ( wppa( 'is_filmonly' ) || wppa( 'is_slideonly' ) ) {
		$shareurl = '';
	}
	else {
		$shareurl = wppa_get_image_page_url_by_id( $id, false, wppa( 'start_album' ) );
		$shareurl = wppa_convert_to_pretty( $shareurl );
		$shareurl = str_replace( '&amp;', '&', $shareurl );
	}

	// Make photo desc, filtered
	$desc = '';
	if ( ( ! wppa( 'is_slideonly' ) || wppa( 'desc_on' ) ) && ! wppa( 'is_filmonly' ) ) {

		$desc .= wppa_get_photo_desc( $id, wppa_switch( 'allow_foreign_shortcodes' ), 'do_geo' );	// Foreign shortcodes is handled here

		// Run wpautop on description?
		if ( wppa_switch( 'run_wpautop_on_desc' ) ) {
			$desc = wpautop( $desc );
		}

		// And format
		$desc = wppa_html( esc_js( stripslashes( $desc ) ) );

		// Remove extra space created by other filters like wpautop
		if ( wppa_switch( 'allow_foreign_shortcodes' ) && wppa_switch( 'clean_pbr' ) ) {
			$desc = str_replace( array( "<p>", "</p>", "<br>", "<br/>", "<br />" ), " ", $desc );
		}

		if ( ! $desc ) $desc = '&nbsp;';
	}

	// Edit photo link
	$editlink = '';
	$dellink = '';
	$choicelink = '';
	if ( ! wppa( 'is_filmonly' ) && ! wppa( 'is_slideonly' ) ) {
		if ( ! wppa_is_user_blacklisted() ) {
			if ( 	( wppa_user_is( 'administrator' ) ) ||
					( current_user_can( 'wppa_moderate' ) ) ||
					( wppa_get_user() == wppa_get_photo_owner( $id ) && wppa_switch( 'upload_edit' ) )
				) {
				$editlink = '
					<div style="float:right; margin-right:6px;" >' .
						'<a' .
							' style="color:green;"' .
							' onclick="_wppaStop( '.wppa( 'mocc' ).' );wppaEditPhoto( '.wppa( 'mocc' ).', '.esc_js('\''.wppa_encrypt_photo($thumb['id']).'\'').' ); return false;"' .
							' >' .
							__( 'Edit' , 'wp-photo-album-plus') .
						'</a>' .
					'</div>';
				$dellink = '
					<div style="float:right; margin-right:6px;" >' .
						'<a' .
							' style="color:red;"' .
							' onclick="' .
								'_wppaStop( ' . wppa( 'mocc' ) . ' );' .
								esc_attr( 'if ( confirm( "' . __( 'Are you sure you want to remove this photo?' , 'wp-photo-album-plus') . '" ) ) ' .
									'wppaAjaxRemovePhoto( '.wppa( 'mocc' ).', '.esc_js('\''.wppa_encrypt_photo($thumb['id']).'\'').', true ); return false;' ).'"' .
							'>' .
							__( 'Delete' , 'wp-photo-album-plus') .
						'</a>' .
					'</div>';
				if ( wppa_user_is( 'administrator' ) && wppa_switch( 'enable_admins_choice' ) ) {
					$choicelink = '
					<div' .
						' id="admin-choice-' . wppa_encrypt_photo($thumb['id']) . '-' . wppa( 'mocc' ) . '"' .
						' style="float:right;margin-right:6px;color:gray;"' .
						' >';
						if ( ! wppa_is_photo_in_zip( $thumb['id'] ) ) {
							$choicelink .=
							'<a' .
								' style="color:blue;"' .
								' onclick="' .
									'_wppaStop( ' . wppa( 'mocc' ) . ' );' .
									esc_attr( 'if ( confirm( "' . __( 'Are you sure you want to add this photo to your zip?' , 'wp-photo-album-plus') . '" ) ) ' .
										'wppaAjaxAddPhotoToZip( '.wppa( 'mocc' ).', '.esc_js('\''.wppa_encrypt_photo($thumb['id']).'\'').', true ); return false;' ).'"' .
								'>' .
								__( 'MyChoice' , 'wp-photo-album-plus') .
							'</a>';
						}
						else {
							$choicelink .= __('Selected', 'wp-photo-album-plus');
						}
					$choicelink .=
					'</div>';
				}
				else {
					$choicelink = '';
				}
			}
		}
	}
	if ( $editlink || $dellink || $choicelink ) $desc = $editlink.$dellink.$choicelink.'<div style="clear:both"></div>'.$desc;

	if ( in_array( $thumb['status'], array( 'pending', 'scheduled' ) ) ) {
		$desc .= wppa_html( esc_js( wppa_moderate_links( 'slide', $id ) ) );
	}

	// Share HTML
	$sharehtml = ( wppa( 'is_filmonly' ) || wppa( 'is_slideonly' ) ) ? '' : wppa_get_share_html( $id );

	// Og Description
	$ogdsc = '';
	if ( wppa_switch( 'facebook_comments' ) && ! wppa_in_widget() && ! wppa( 'is_filmonly' ) && ! wppa( 'is_slideonly' ) ) {
		$ogdsc = strip_shortcodes( wppa_strip_tags( wppa_html( wppa_get_photo_desc( $id ) ), 'all' ) );
		$ogdsc = esc_js( $ogdsc );
	}

	// Hires url
	$hiresurl = esc_js( wppa_fix_poster_ext( wppa_get_hires_url( $id ), $id ) );

	// Video html
	$videohtml = wppa_get_video_body( $id );

	// Audio html
	$audiohtml = wppa_get_audio_body( $id );

	// Produce final result
    $result = "'".wppa( 'mocc' )."','";
	$result .= $index."','";
	$result .= $photourl."','";
	$result .= $style_a['style']."','";
	$result .= ( $videohtml ? wppa_get_videox( $id ) : $style_a['width'] )."','";
	$result .= ( $videohtml ? wppa_get_videoy( $id ) : $style_a['height'] )."','";
	$result .= $fullname."','";
	$result .= $name."','";
	$result .= $desc."','";
	$result .= wppa_encrypt_photo( $id )."','";
//wppa_dbg_msg('id='.$id, 'red');
	$result .= $avgrat."','";
	$result .= $distext."','";
	$result .= $myrat."','";
	$result .= $url."','";
//wppa_dbg_msg('url='.$url, 'red');
	$result .= $linkurl."','".$linktitle."','".$linktarget."','";
//wppa_dbg_msg('linkurl='.$linkurl, 'red');
	$result .= wppa( 'in_widget_timeout' )."','";
	$result .= $comment."','";
	$result .= $iptc."','";
	$result .= $exif."','";
	$result .= $lbtitle."','";
	$result .= $shareurl."','";	// Used for history.pushstate()
//wppa_dbg_msg('shareurl='.$shareurl, 'red');
	$result .= $sharehtml."','";	// The content of the SM ( share ) box
	$result .= $ogdsc."','";
	$result .= $hiresurl."','";
	$result .= $videohtml."','";
	$result .= $audiohtml."'";

	// This is an ingenious line of code that is going to prevent us from very much trouble.
	// Created by OpaJaap on Jan 15 2012, 14:36 local time. Thanx.
	// Make sure there are no linebreaks in the result that would screw up Javascript.
	return str_replace( array( "\r\n", "\n", "\r" ), " ", $result );

//	return $result;
}

function wppa_get_distext( $discount, $myrat ) {

	if ( wppa_switch( 'dislike_show_count' ) ) {
		$distext = $discount ? esc_js( sprintf( _n( '%d dislike', '%d dislikes', $discount, 'wp-photo-album-plus' ), $discount ) ) : '';
		if ( $myrat < '0' ) {
			$distext .= ' ' . esc_js( __( 'including mine', 'wp-photo-album-plus' ) );
		}
	}
	else {
		$distext = '';
	}
	return $distext;
}

// Process a comment request
function wppa_do_comment( $id ) {
global $wpdb;
global $wppa_done;

	if ( $wppa_done ) return; // Prevent multiple
	$wppa_done = true;

	$time = time();
	$photo = isset( $_REQUEST['photo'] ) ? strval( intval( $_REQUEST['photo'] ) ) : '0';	//wppa_get_get( 'photo' );
	if ( ! $photo ) $photo = isset( $_REQUEST['photo-id'] ) ? strval( intval( $_REQUEST['photo-id'] ) ) : '0';	//wppa_get_get( 'photo' );
	if ( ! $photo ) die( 'Photo id missing while processing a comment' );
	$user = sanitize_user( wppa_get_post( 'comname' ) );
	if ( ! $user ) die( 'Illegal attempt to enter a comment 1' );
	$email = sanitize_email( wppa_get_post( 'comemail' ) );

	if ( ! $email ) {
		if ( wppa_opt( 'comment_email_required' ) == 'required' ) die( 'Illegal attempt to enter a comment 2' );
		else $email = wppa_get_user();	// If email not present and not required, use his IP
	}

	// Retrieve and filter comment
	$comment = wppa_get_post( 'comment' );
	$comment = trim( $comment );
	$comment = wppa_decode( $comment );
	$comment = strip_tags( $comment );
	$save_comment = str_replace( "\n", '<br />', $comment );	// Resque newline chars
	$save_comment = stripslashes( $save_comment );

	$policy = wppa_opt( 'comment_moderation' );
	switch ( $policy ) {
		case 'all':
			$status = 'pending';
			break;
		case 'logout':
			$status = is_user_logged_in() ? 'approved' : 'pending';
			break;
		case 'none':
			$status = 'approved';
			break;
	}
	if ( current_user_can( 'wppa_moderate' ) ) $status = 'approved';	// Need not moderate comments issued by moderator

	// Editing a comment?
	$cedit = wppa_get_post( 'comment-edit', '0' );
	if ( ! wppa_is_int( $cedit ) ) wp_die( 'Security check falure 14' );

	// Check captcha
	if ( ( is_user_logged_in() && wppa_opt( 'comment_captcha' ) == 'all' ) ||
		 ( ! is_user_logged_in() && wppa_opt( 'comment_captcha' ) != 'none' ) )	{
		$captkey = $id;
		if ( $cedit ) $captkey = $wpdb->get_var( $wpdb->prepare( 'SELECT `timestamp` FROM `'.WPPA_COMMENTS.'` WHERE `id` = %s', $cedit ) );
		wppa_dbg_q( 'Q43' );
		if ( ! wppa_check_captcha( $captkey ) ) {
				$status = 'spam';
		}
	}

	// Process ( edited ) comment
	if ( $comment ) {
		if ( $cedit ) {
			$query = $wpdb->prepare(
				"UPDATE `" . WPPA_COMMENTS . "`".
				" SET `comment` = %s, `user` = %s, `email` = %s, `status` = %s, `timestamp` = %s " .
				" WHERE `id` = %s LIMIT 1",
					$save_comment,
					$user,
					$email,
					$status,
					time(),
					$cedit
			);
			wppa_dbg_q( 'Q44' );
			$iret = $wpdb->query( $query );
			if ( $iret !== false ) {
				wppa( 'comment_id', $cedit );
			}
		}
		else {
			// See if a refresh happened
			$old_entry = $wpdb->prepare( 'SELECT * FROM `'.WPPA_COMMENTS.'` WHERE `photo` = %s AND `user` = %s AND `comment` = %s LIMIT 1', $photo, $user, $save_comment );
			$iret = $wpdb->query( $old_entry );
			if ( $iret ) {
				if ( wppa( 'debug' ) ) echo( '<script type="text/javascript">alert( "Duplicate comment ignored" )</script>' );
				return;
			}
			$key = wppa_create_comments_entry( array( 'photo' => $photo, 'user' => $user, 'email' => $email, 'comment' => $save_comment, 'status' => $status ) );
			if ( $key ) wppa( 'comment_id', $key );
		}
		if ( $iret !== false ) {
			if ( $status != 'spam' ) {
				if ( $cedit ) {
					if ( wppa_switch( 'comment_notify_added' ) ) {
						echo( '<script id="cme" type="text/javascript">alert( "'.__( 'Comment edited' , 'wp-photo-album-plus').'" );jQuery( "#cme" ).html( "" );</script>' );
					}
				}
				else {
					// SUCCESSFUL COMMENT, ADD POINTS
					wppa_add_credit_points( wppa_opt( 'cp_points_comment' ), __( 'Photo comment' , 'wp-photo-album-plus'), $id );

					// SEND EMAILS
					// Initialize
					$subj = __( 'Comment on photo:' , 'wp-photo-album-plus').' '.wppa_get_photo_name( $id );
					$usr  = $user;
					if ( is_user_logged_in() ) {
						global $current_user;
						$current_user = wp_get_current_user();
						$usr = $current_user->display_name;
					}
					$returnurl 	= wppa_get_post('returnurl');
					$sentto = array();

					// Setup standard content
					$cont['0'] = $usr.' '.__( 'wrote on photo' , 'wp-photo-album-plus').' '.wppa_get_photo_name( $id ).':';
					$cont['1'] = '<blockquote style="color:#000077; background-color: #dddddd; border:1px solid black; padding: 6px; border-radius 4px;" ><em> '.stripslashes($comment).'</em></blockquote>';
					$cont['2'] = $returnurl ? '<a href="'.$returnurl.'" >'.__( 'Reply' , 'wp-photo-album-plus').'</a>' : '';
					$cont2     = 	'<a href="'.get_admin_url().'admin.php?page=wppa_manage_comments&commentid='.$key.'" >' .
										__( 'Moderate comment admin' , 'wp-photo-album-plus') .
									'</a>';
					$cont3     = 	'<a href="'.get_admin_url().'admin.php?page=wppa_admin_menu&tab=cmod&photo='.$id.'" >' .
										__( 'Moderate manage photo' , 'wp-photo-album-plus') .
									'</a>';
					$cont3a	   = 	'<a href="'.get_admin_url().'admin.php?page=wppa_edit_photo&photo='.$id.'" >' .
										__( 'Edit photo' , 'wp-photo-album-plus') .
									'</a>';

					// Process various types of emails
					if ( is_numeric( wppa_opt( 'comment_notify' ) ) ) {

						// Mail specific user
						$moduser 	= get_userdata( wppa_opt( 'comment_notify' ) );
						$to      	= $moduser->user_email;
						if ( user_can( $moduser, 'wppa_comments' ) ) $cont['3'] = $cont2; else $cont['3'] = '';
						if ( user_can( $moduser, 'wppa_admin' ) ) 	 $cont['4'] = $cont3; else $cont['4'] = '';
						$cont['5'] 	= __( 'You receive this email as you are assigned to moderate' , 'wp-photo-album-plus');
						// Send!
						wppa_send_mail( $to, $subj, $cont, $photo, ( wppa_switch( 'mail_upl_email' ) ? $email : 'void' ), $returnurl );
						$sentto[] = $moduser->user_login;
					}
					if ( wppa_opt( 'comment_notify' ) == 'admin' || wppa_opt( 'comment_notify' ) == 'both' || wppa_opt( 'comment_notify' ) == 'upadmin' ) {
						// Mail admin
						$moduser   = get_user_by( 'id', '1' );
						if ( ! in_array( $moduser->user_login, $sentto ) ) {	// Already sent him?
							$to        = get_bloginfo( 'admin_email' );
							$cont['3'] = $cont2;
							$cont['4'] = $cont3;
							$cont['5'] = __( 'You receive this email as administrator of the site' , 'wp-photo-album-plus');
							// Send!
							wppa_send_mail( $to, $subj, $cont, $photo, $email, $returnurl );
							$sentto[] = $moduser->user_login;
						}
					}
					if ( wppa_opt( 'comment_notify' ) == 'upload' || wppa_opt( 'comment_notify' ) == 'upadmin' || wppa_opt( 'comment_notify' ) == 'upowner' ) {
						// Mail uploader
						$uploader = $wpdb->get_var( $wpdb->prepare( "SELECT `owner` FROM `".WPPA_PHOTOS."` WHERE `id` = %d", $id ) );
						$moduser = get_user_by( 'login', $uploader );
						if ( $moduser ) {	// else it's an ip address ( anonymus uploader )
							if ( ! in_array( $moduser->user_login, $sentto ) ) {	// Already sent him?
								$to = $moduser->user_email;
								$cont['3'] = user_can( $moduser, 'wppa_comments' ) ? $cont2 : '';
								if ( user_can( $moduser, 'wppa_admin' ) ) $cont['4'] = $cont3;
								elseif ( wppa_switch( 'upload_edit' ) ) $cont['4'] = $cont3a;
								else $cont['4'] = '';
								$cont['5'] = __( 'You receive this email as uploader of the photo' , 'wp-photo-album-plus');
								// Send!
								wppa_send_mail( $to, $subj, $cont, $photo, ( wppa_switch( 'mail_upl_email' ) ? $email : 'void' ), $returnurl );
								$sentto[] = $moduser->user_login;
							}
						}
					}
					if ( wppa_opt( 'comment_notify' ) == 'owner' || wppa_opt( 'comment_notify' ) == 'both' || wppa_opt( 'comment_notify' ) == 'upowner' ) {
						// Mail album owner
						$alb     = $wpdb->get_var( $wpdb->prepare( "SELECT `album` FROM `".WPPA_PHOTOS."` WHERE `id` = %d", $id ) );
						$owner   = $wpdb->get_var( $wpdb->prepare( "SELECT `owner` FROM `".WPPA_ALBUMS."` WHERE `id` = %d", $alb ) );
						if ( $owner == '--- public ---' ) $owner = 'admin';
						$moduser = get_user_by( 'login', $owner );
						if ( ! in_array( $moduser->user_login, $sentto ) ) {	// Already sent him?
							$to = $moduser->user_email;
							if ( user_can( $moduser, 'wppa_comments' ) ) $cont['3'] = $cont2; else $cont['3'] = '';
							if ( user_can( $moduser, 'wppa_admin' ) ) 	 $cont['4'] = $cont3; else $cont['4'] = '';
							$cont['5'] = __( 'You receive this email as owner of the album' , 'wp-photo-album-plus');
							// Send!
							wppa_send_mail( $to, $subj, $cont, $photo, ( wppa_switch( 'mail_upl_email' ) ? $email : 'void' ), $returnurl );
							$sentto[] = $moduser->user_login;
						}
					}
					if ( wppa_switch( 'com_notify_previous' ) ) {
						// Mail users already commented on this photo
						$cmnts 	= $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `".WPPA_COMMENTS."` WHERE `photo` = %d", $photo ), ARRAY_A );
						if ( $cmnts ) foreach( $cmnts as $cmnt ) {
							$user = $cmnt['user'];
							if ( ! in_array( $user, $sentto ) ) {
								$cmuser = get_user_by( 'login', $user );
								if ( $cmuser ) {	// Not to an ip
									$to = $cmuser->user_email;
									$cont['3'] = '';
									$cont['4'] = '';
									$cont['5'] = __( 'You receive this email because you commented this photo earlier.' , 'wp-photo-album-plus');
									// Send!
									wppa_send_mail( $to, $subj, $cont, $photo, ( wppa_switch( 'mail_upl_email' ) ? $email : 'void' ), $returnurl );
									$sentto[] = $to;
								}
							}
						}
					}
/* to do
					if ( wppa_switch(  'wppa_mail_ats' ) ) {
						// Mail to @dest
						// Find @dest in $comment

						// Mail them

					}
*/

					// Process any pending votes of this user for this photo if rating needs comment, do it anyway, feature may have been on but now off
	//				if ( wppa_switch( 'vote_needs_comment' ) ) {
						$iret = $wpdb->query( $wpdb->prepare( "UPDATE `".WPPA_RATING."` SET `status` = 'publish' WHERE `photo` = %s AND `user` = %s", $id, wppa_get_user() ) );
						if ( $iret ) wppa_rate_photo( $id );	// Recalc ratings for this photo
	//				}

					// Notyfy user
					if ( wppa_switch( 'comment_notify_added' ) ) {
						echo( '<script id="cme" type="text/javascript">alert( "'.__( 'Comment added' , 'wp-photo-album-plus').'" );jQuery( "#cme" ).html( "" );</script>' );
					}
				}
			}
			else {
				echo 	'<script type="text/javascript">' .
							'alert( "'.__( 'Sorry, you gave a wrong answer.\n\nPlease try again to solve the computation.' , 'wp-photo-album-plus').'" )' .
						'</script>';
			}

			wppa( 'comment_photo', $id );
			wppa( 'comment_text', $comment );

			// Clear ( super )cache
			wppa_clear_cache();
		}
		else {
			echo( '<script type="text/javascript">alert( "'.__( 'Could not process comment.\nProbably timed out.' , 'wp-photo-album-plus').'" )</script>' );
		}
	}
	else {	// Empty comment
	}
}

// Create a captcha
function wppa_make_captcha( $id ) {
	$capt = wppa_ll_captcha( $id );
	return $capt['text'];
}

// Check the comment security answer
function wppa_check_captcha( $id ) {
	$answer = wppa_get_post( 'wppa-captcha' );
	$capt = wppa_ll_captcha( $id );
	return $capt['ans'] == $answer;
}

// Low level captcha routine
function wppa_ll_captcha( $id ) {
	$nonce = wp_create_nonce( 'wppa_photo_comment_'.$id );
	$result['val1'] = 1 + intval( substr( $nonce, 0, 4 ), 16 ) % 12;
	$result['val2'] = 1 + intval( substr( $nonce, -4 ), 16 ) % 12;
	if ( $result['val1'] == $result['val1'] ) $result['val2'] = 1 + intval( substr( $nonce, -5, 4 ), 16 ) % 12;
	if ( $result['val1'] != 1 && $result['val2'] != 1 && $result['val1'] * $result['val2'] < 21 ) {
		$result['oper'] = 'x';
		$result['ans'] = $result['val1'] * $result['val2'];
	}
	elseif ( $result['val1'] > ( $result['val2'] + 1 ) ) {
		$result['oper'] = '-';
		$result['ans'] = $result['val1'] - $result['val2'];
	}
	else {
		$result['oper'] = '+';
		$result['ans'] = $result['val1'] + $result['val2'];
	}
	$result['text'] = sprintf( '%d %s %d = ', $result['val1'], $result['oper'], $result['val2'] );
	return $result;
}

function wppa_get_imgevents( $type = '', $id = '', $no_popup = false, $idx = '' ) {
global $wpdb;

	$result = '';
	$perc = '';
	if ( $type == 'thumb' || $type=='film' ) {
		if ( wppa_switch( 'use_thumb_opacity' ) || wppa_switch( 'use_thumb_popup' ) ) {

			if ( wppa_switch( 'use_thumb_opacity' ) ) {
				$perc = wppa_opt( 'thumb_opacity' );
				$result = ' onmouseout="jQuery( this ).fadeTo( 400, ' . $perc/100 . ' )" onmouseover="jQuery( this ).fadeTo( 400, 1.0 );';
			} else {
				$result = ' onmouseover="';
			}

			if ( $type == 'film' && wppa_switch( 'film_hover_goto' ) ) {
				$result .= 'wppaGotoFilmNoMove( '.wppa( 'mocc' ).', '.$idx.' );';
			}

			if ( ! $no_popup && wppa_switch( 'use_thumb_popup' ) ) {
				if ( wppa_opt( 'thumb_linktype' ) != 'lightbox' ) {

					$name = wppa_switch( 'popup_text_name' ) || wppa_switch( 'popup_text_owner' ) ?
								wppa_get_photo_name( $id, wppa_switch( 'popup_text_owner' ), false, false, wppa_switch( 'popup_text_name' ) ) :
								'';
					$name = esc_js( $name );

					$desc = wppa_switch( 'popup_text_desc' ) ? wppa_get_photo_desc( $id ) : '';
					if ( wppa_switch( 'popup_text_desc_strip' ) ) $desc = wppa_strip_tags( $desc );
					$desc = esc_js( $desc );

					$rating = wppa_switch( 'popup_text_rating' ) ? wppa_get_rating_by_id( $id ) : '';
					if ( $rating && wppa_switch( 'show_rating_count' ) ) $rating .= ' ( '.wppa_get_rating_count_by_id( $id ).' )';
					$rating = esc_js( $rating );

					if ( wppa_switch( 'popup_text_ncomments' ) ) {
						$ncom = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_COMMENTS."` WHERE `photo` = %s AND `status` = 'approved'", $id ) );
					}
					else $ncom = '0';
					if ( $ncom ) {
						$ncom = sprintf( _n( '%d comment', '%d comments', $ncom, 'wp-photo-album-plus'), $ncom );
					}
					else $ncom = '';
					$ncom = esc_js( $ncom );

					$x = wppa_get_imagex( $id, 'thumb' );
					$y = wppa_get_imagey( $id, 'thumb' );
					/*
					if ( wppa_is_video( $id ) ) {
						$x = wppa_get_videox( $id );
						$y = wppa_get_videoy( $id );
					}
					else {
						$x = wppa_get_thumbx( $id );
						$y = wppa_get_thumby( $id );
					}
					*/

					if ( $x > $y ) {
						$w = wppa_opt( 'popupsize' );
						$h = round( $w * $y / $x );
					}
					else {
						$h = wppa_opt( 'popupsize' );
						$w = round( $h * $x / $y );
					}

					if ( wppa_is_video( $id ) ) {
						$video_args = array(
												'id'			=> $id,
												'controls' 		=> false,
												'tagid' 		=> 'wppa-img-'.wppa( 'mocc' ),
												'width' 		=> $w,
												'height' 		=> $h
											 );
						if ( wppa_opt( 'thumb_linktype' ) == 'fullpopup' ) {
							$video_args['events'] = 'onclick="alert( \''.esc_attr( __( 'A video can not be printed or downloaded' , 'wp-photo-album-plus') ).'\' );"';
						}
						$videohtml = wppa_get_video_html( $video_args );
					}
					else {
						$videohtml = '';
					}

					$result .= 'wppaPopUp( ' .
						wppa( 'mocc' ) .
						', this, ' .
						'\''.wppa_encrypt_photo($id) .'\''.
						', \'' .
						$name .
						'\', \'' .
						$desc .
						'\', \'' .
						$rating .
						'\', \'' .
						$ncom .
						'\', \'' .
						esc_js( $videohtml ) .
						'\', \'' .
						$w .
						'\', \'' .
						$h .
						'\' );" ';
				}
				else {
					// Popup and lightbox on thumbs are incompatible. skip popup.
					$result .= '" ';
				}
			}
			else $result .= '" ';
		}
	}
	elseif ( $type == 'cover' ) {
		if ( wppa_switch( 'use_cover_opacity' ) ) {
			$perc = wppa_opt( 'cover_opacity' );
			$result = ' onmouseover="jQuery( this ).fadeTo( 400, 1.0 )" onmouseout="jQuery( this ).fadeTo( 400, ' . $perc/100 . ' )" ';
		}
	}
	return $result;
}

function wppa_onpage( $type = '', $counter, $curpage ) {

	$pagesize = wppa_get_pagesize( $type );
	if ( $pagesize == '0' ) {			// Pagination off
		if ( $curpage == '1' ) return true;
		else return false;
	}
	$cnt = $counter - 1;
	$crp = $curpage - 1;
	if ( floor( $cnt / $pagesize ) == $crp ) return true;
	return false;
}

function wppa_get_pagesize( $type = '' ) {

	if ( $type == 'albums' ) return wppa_opt( 'album_page_size' );
	if ( $type == 'thumbs' ) return wppa_opt( 'thumb_page_size' );
	return '0';
}

function wppa_deep_stristr( $string, $tokens ) {
global $wppa_stree;
	$string = stripslashes( $string );
	$tokens = stripslashes( $tokens );
	// Explode tokens into search tree
	if ( !isset( $wppa_stree ) ) {
		// sanitize search token string
		$tokens = trim( $tokens );
		while ( strstr( $tokens, ', ' ) ) $tokens = str_replace( ', ', ',', $tokens );
		while ( strstr( $tokens, ' ,' ) ) $tokens = str_replace( ' ,', ',', $tokens );
		while ( strstr( $tokens, '  ' ) ) $tokens = str_replace( '  ', ' ', $tokens );
		while ( strstr( $tokens, ',,' ) ) $tokens = str_replace( ',,', ',', $tokens );
		// to level explode
		if ( strstr( $tokens, ',' ) ) {
			$wppa_stree = explode( ',', $tokens );
		}
		else {
			$wppa_stree[0] = $tokens;
		}
		// bottom level explode
		for ( $idx = 0; $idx < count( $wppa_stree ); $idx++ ) {
			if ( strstr( $wppa_stree[$idx], ' ' ) ) {
				$wppa_stree[$idx] = explode( ' ', $wppa_stree[$idx] );
			}
		}
	}
	// Check the search criteria
	foreach ( $wppa_stree as $branch ) {
		if ( is_array( $branch ) ) {
			if ( wppa_and_stristr( $string, $branch ) ) return true;
		}
		else {
			if ( stristr( $string, $branch ) ) return true;
		}
	}
	return false;
}

function wppa_and_stristr( $string, $branch ) {
	foreach ( $branch as $leaf ) {
		if ( !stristr( $string, $leaf ) ) return false;
	}
	return true;
}

function wppa_get_slide_frame_style() {

	$fs = wppa_opt( 'fullsize' );
	$cs = wppa_opt( 'colwidth' );
	if ( $cs == 'auto' ) {
		$cs = $fs;
		wppa( 'auto_colwidth', true );
	}
	$result = '';
	$gfs = ( is_numeric( wppa( 'fullsize' ) ) && wppa( 'fullsize' ) > '1' ) ? wppa( 'fullsize' ) : $fs;

	$gfh = floor( $gfs * wppa_opt( 'maxheight' ) / wppa_opt( 'fullsize' ) );

	if ( wppa_in_widget() == 'ss' && wppa( 'in_widget_frame_height' ) > '0' ) $gfh = wppa( 'in_widget_frame_height' );

	// for bbb:
	wppa( 'slideframewidth', $gfs );
	wppa( 'slideframeheight', $gfh );

	if ( wppa( 'portrait_only' ) ) {
		$result = 'width: ' . $gfs . 'px;';	// No height
	}
	else {
		if ( wppa_page( 'oneofone' ) ) {
			$h = floor( $gfs * wppa_get_photoy( wppa( 'single_photo' ) ) / wppa_get_photox( wppa( 'single_photo' ) ) );
			$result .= 'height: ' . $h . 'px;';
		}
		elseif ( wppa( 'auto_colwidth' ) ) {
			$result .= ' height: ' . $gfh . 'px;';
		}
		elseif ( wppa( 'ss_widget_valign' ) != '' && wppa( 'ss_widget_valign' ) != 'fit' ) {
			$result .= ' height: ' . $gfh . 'px;';
		}
		elseif ( wppa_opt( 'fullvalign' ) == 'default' ) {
			$result .= 'min-height: ' . $gfh . 'px;';
		}
		else {
			$result .= 'height: ' . $gfh . 'px;';
		}
		$result .= 'width: ' . $gfs . 'px;';
	}

	$hor = wppa_opt( 'fullhalign' );
	if ( $gfs == $fs ) {
		if ( $fs != $cs ) {
			switch ( $hor ) {
			case 'left':
				$result .= 'margin-left: 0px;';
				break;
			case 'center':
				$result .= 'margin-left: ' . floor( ( $cs - $fs ) / 2 ) . 'px;';
				break;
			case 'right':
				$result .= 'margin-left: ' . ( $cs - $fs ) . 'px;';
				break;
			}
		}
	}
	// Margin bottom
	if ( wppa_opt( 'box_spacing' ) ) {
		$result .= 'margin-bottom: ' . wppa_opt( 'box_spacing' ) . 'px;';
	}

	return $result;
}

function wppa_get_thumb_frame_style( $glue = false, $film = '' ) {
	$temp = wppa_get_thumb_frame_style_a( $glue, $film );
	$result = $temp['style'];
	return $result;
}

function wppa_get_thumb_frame_style_a( $glue = false, $film = '' ) {
static $wppaerrmsgxxx;

	// Init
	$album = wppa( 'current_album' ) ? wppa_cache_album( wppa( 'current_album' ) ) : false;
	$result = array( 'style'=> '', 'width' => '', 'height' => '' );

	// Comten alt display?
	$com_alt = wppa( 'is_comten' ) && wppa_switch( 'comten_alt_display' ) && ! wppa_in_widget() && ! $film;

	// Film, normal or alt?
	if ( $film ) {
		$tfw = wppa_opt( 'film_thumbsize' );
		$tfh = $tfw;
	}
	else {
		$alt = is_array( $album ) && $album['alt_thumbsize'] == 'yes' ? '_alt' : '';
		$tfw = wppa_opt( 'tf_width'.$alt );
		$tfh = wppa_opt( 'tf_height'.$alt );
	}

	// Margin
	$mgl = wppa_opt( 'tn_margin' );

	// Film in widget
	if ( $film && wppa_in_widget() ) {
		$tfw /= 2;
		$tfh /= 2;
		$mgl /= 2;
	}

	// Half margin
	$mgl2 = floor( $mgl / '2' );

	if ( ! $film && wppa_switch( 'thumb_auto' ) ) {
		$area = wppa_get_box_width() + $tfw;	// Area for n+1 thumbs
		$n_1 = floor( $area / ( $tfw + $mgl ) );
		if ( $n_1 == '0' ) {
			if ( ! $wppaerrmsgxxx ) wppa_dbg_msg( 'Misconfig. thumbnail area too small. Areasize = '.wppa_get_box_width().' tfwidth = '.$tfw.' marg= '.$mgl );
			$n_1 = '1';
			$wppaerrmsgxxx = true;	// err msg given
		}
		$mgl = floor( $area / $n_1 ) - $tfw;
	}

	if ( is_numeric( $tfw ) && is_numeric( $tfh ) ) {
		$result['style'] = 'width: '.$tfw.'px; height: '.$tfh.'px; margin-left: '.$mgl.'px; margin-top: '.$mgl2.'px; margin-bottom: '.$mgl2.'px;';
		if ( $glue && wppa_switch( 'film_show_glue' ) && wppa_switch( 'slide_wrap' ) ) {
			$result['style'] .= 'padding-right:'.$mgl.'px; border-right: 2px dotted gray;';
		}
		$result['width'] = $tfw;
		$result['height'] = $tfh;
	}
	else $result['style'] = '';

	// Alt comment?
	if ( $com_alt ) {
		$w = wppa_get_container_width();
		if ( $w <= 1.0 ) {
			$w = $w * wppa_opt( 'initial_colwidth' );
		}
		$result['style'] = 'width: '.$w.'px; margin-left: 4px; margin-top: 2px; margin-bottom: 2px;';
	}

	return $result;
}

function wppa_get_container_width( $netto = false ) {

	if ( is_numeric( wppa( 'fullsize' ) ) && wppa( 'fullsize' ) > '0' ) {
		$result = wppa( 'fullsize' );
	}
	else {
		$result = wppa_opt( 'colwidth' );
		if ( $result == 'auto' ) {
			$result = wppa_opt( 'initial_colwidth' ); //'640';
			wppa( 'auto_colwidth', true );
		}
	}
	if ( $netto ) {
	$result -= 12; // 2*padding
	$result -= 2 * wppa_opt( 'bwidth' );
	}
	return $result;
}

function wppa_get_thumbnail_area_width() {
	$result = wppa_get_container_width();
	$result -= wppa_get_thumbnail_area_delta();
	return $result;
}

function wppa_get_thumbnail_area_delta() {

//	$result = 7 + 2 * wppa_opt( 'bwidth' );	// 7 = .thumbnail_area padding-left
		$result = 12 + 2 * wppa_opt( 'bwidth' );	// experimental
	return $result;
}

function wppa_get_container_style() {

	$result = '';

	// See if there is space for a margin
	$marg = false;
	if ( is_numeric( wppa( 'fullsize' ) ) ) {
		$cw = wppa_opt( 'colwidth' );
		if ( is_numeric( $cw ) ) {
			if ( $cw > ( wppa( 'fullsize' ) + 10 ) ) {
				$marg = '10px;';
			}
		}
	}

	if ( ! wppa_in_widget() ) {
		if ( wppa( 'align' ) == 'left' ) {
			$result .= 'clear: left; ';
		}
		if ( wppa( 'align' ) == 'right' ) {
			$result .= 'clear: right; ';
		}
	}

	$ctw = wppa_get_container_width();
	if ( wppa( 'auto_colwidth' ) ) {
		if ( is_feed() ) {
			$result .= 'width:'.$ctw.'px;';
		}
	}
	else {
		$result .= 'width:'.$ctw.'px;';
	}

//	if ( wppa( 'align' ) == '' ||
	if ( wppa( 'align' ) == 'left' ) {
		$result .= 'float: left;';
		if ( $marg ) $result .= 'margin-right: '.$marg;
	}
	elseif ( wppa( 'align' ) == 'center' ) $result .= 'display: block; margin-left: auto; margin-right: auto;';
	elseif ( wppa( 'align' ) == 'right' ) {
		$result .= 'float: right;';
		if ( $marg ) $result .= 'margin-left: '.$marg;
	}

	$result .= ' padding:0;';	//4.7.5

	return $result;
}

function wppa_get_curpage() {

	if ( wppa_get_get( 'page' ) ) {
		if ( wppa_in_widget() ) {
			$oc = wppa_get_get( 'woccur' );
			if ( ! $oc ) $oc = '1';
			$curpage = wppa( 'widget_occur' ) == $oc ? wppa_get_get( 'page' ) : '1';
		}
		else {
			$oc = wppa_get_get( 'occur' );
			if ( ! $oc ) $oc = '1';
			$curpage = wppa( 'occur' ) == $oc ? wppa_get_get( 'page' ) : '1';
		}
	}
	else $curpage = '1';
	return $curpage;
}

function wppa_container( $action ) {
global $wppa_version;			// The theme version ( wppa_theme.php )
global $wppa_microtime;
global $wppa_microtime_cum;
global $wppa_err_displayed;
global $wppa_loadtime;
global $wppa_initruntimetime;
static $wppa_numqueries;
static $auto;
global $blog_id;

	if ( is_feed() ) return;		// Need no container in RSS feeds

	if ( $action == 'open' ) {
		$wppa_numqueries = get_num_queries();

		// Open the container
		if ( ! wppa( 'ajax' ) ) {
//			wppa( 'out' ) .= '<!-- Start WPPA+ generated code'.( is_multisite() ? ', Multi site, id='.$blog_id : ', Single site' ).' -->';
//			if ( wppa( 'shortcode_content' ) ) {
//				wppa( 'out' ) .= '<!-- ' . wppa( 'shortcode_content' ) . ' -->';
//			}
			wppa_out( 	'<div' .
							' id="wppa-container-'.wppa( 'mocc' ).'"' .
							' style="'.wppa_get_container_style().'"' .
							' class="' .
								'wppa-container' . ' ' .
								'wppa-container-' . wppa( 'mocc' ) . ' ' .
								'wppa-rev-' . wppa( 'revno' ) . ' ' .
								'wppa-prevrev-' . wppa_opt( 'prevrev' ) . ' ' .
								'wppa-theme-' . $wppa_version . ' ' .
								'wppa-api-' . wppa( 'api_version' ) .
								'"' .
							' >'
					);
		}

		// Spinner for Ajax
		if ( wppa_switch( 'allow_ajax' ) ) {
			if ( ! wppa_in_widget() ) {
				wppa_out( 	'<img' .
								' id="wppa-ajax-spin-' . wppa( 'mocc' ) . '"' .
								' src="'.wppa_get_imgdir().'loader.gif"' .
								' alt="spinner"' .
								' style="' .
									'box-shadow:none;' .
									'z-index:1010;' .
									'position:fixed;' .
									'top:50%;' .
									'margin-top:-32px;' .
									'left:50%;' .
									'margin-left:-32px;' .
									'display:none;' .
								'"' .
							' />'
						);
			}
		}

		// Start timer if in debug mode
		if ( wppa( 'debug' ) ) {
			$wppa_microtime = - microtime( true );
			wppa_dbg_q( 'init' );
		}
		if ( wppa( 'mocc' ) == '1' ) {
			wppa_dbg_msg( 'Plugin load time :'.substr( $wppa_loadtime,0,5 ).'s.' );
			wppa_dbg_msg( 'Init runtime time :'.substr( $wppa_initruntimetime,0,5 ).'s.' );
			wppa_dbg_msg( 'Num queries before wppa :'.get_num_queries() );
		}

		/* Check if wppa.js and jQuery are present */
		if ( ! $wppa_err_displayed && ( WPPA_DEBUG || wppa_get_get( 'debug' ) || WP_DEBUG ) && ! wppa_switch( 'defer_javascript' ) ) {
			wppa_out( '<script type="text/javascript">/* <![CDATA[ */' );
				wppa_out( "if ( typeof( _wppaSlides ) == 'undefined' ) " .
									"alert( 'There is a problem with your theme. The file wppa.js is not loaded when it is expected ( Errloc = wppa_container ).' );" );
				wppa_out( "if ( typeof( jQuery ) == 'undefined' ) " .
									"alert( 'There is a problem with your theme. The jQuery library is not loaded when it is expected ( Errloc = wppa_container ).' );" );
			wppa_out( "/* ]]> */</script>" );
			$wppa_err_displayed = true;
		}

		/* Check if init is properly done */
		if ( ! wppa_opt( 'fullsize' ) ) {
			wppa_out( '<script type="text/javascript">/* <![CDATA[ */' );
				wppa_out( "alert( 'The initialisation of wppa+ is not complete yet. " .
										"You will probably see division by zero errors. " .
										"Please run Photo Albums -> Settings admin page Table VIII-A1. ( Errloc = wppa_container ).' );" );
			wppa_out( "/* ]]> */</script>" );
		}

		// Nonce field check for rating security
		if ( wppa( 'mocc' ) == '1' ) {
			if ( wppa_get_get( 'rating' ) ) {
				$nonce = wppa_get_get( 'nonce' );
				$ok = wp_verify_nonce( $nonce, 'wppa-check' );
				if ( $ok ) {
					wppa_dbg_msg( 'Rating nonce ok' );
					if ( ! is_user_logged_in() ) sleep( 2 );
				}
				else die( '<b>' . __( 'ERROR: Illegal attempt to enter a rating.' , 'wp-photo-album-plus') . '</b>' );
			}
		}

		// Nonce field check for comment security
		if ( wppa( 'mocc' ) == '1' ) {
			if ( wppa_get_post( 'comment' ) ) {
				$nonce = wppa_get_post( 'nonce' );
				$ok = wp_verify_nonce( $nonce, 'wppa-check' );
				if ( $ok ) {
					wppa_dbg_msg( 'Comment nonce ok' );
					if ( ! is_user_logged_in() ) sleep( 2 );
				}
				else die( '<b>' . __( 'ERROR: Illegal attempt to enter a comment.' , 'wp-photo-album-plus') . '</b>' );
			}
		}

		wppa_out( wppa_nonce_field( 'wppa-check' , 'wppa-nonce', false, false ) );

		if ( wppa_page( 'oneofone' ) ) wppa( 'portrait_only', true );
		wppa( 'alt', 'alt' );

		// Javascript occurrence dependant stuff
		wppa_add_js_page_data( "\n" . '<script type="text/javascript">' );
			// wppa( 'auto_colwidth' ) is set by the filter or by wppa_albums in case called directly
			// wppa_opt( 'colwidth' ) is the option setting
			// script or call has precedence over option setting
			// so: if set by script or call: auto, else if set by option: auto
			$auto = false;
			$contw = wppa_get_container_width();
			if ( wppa( 'auto_colwidth' ) ) $auto = true;
			elseif ( wppa_opt( 'colwidth' ) == 'auto' ) $auto = true;
			elseif ( $contw > 0 && $contw <= 1.0 ) $auto = true;

			if ( $auto ) {
				wppa_add_js_page_data( "\n" . 'wppaAutoColumnWidth['.wppa( 'mocc' ).'] = true;' );
				if ( $contw > 0 && $contw <= 1.0 ) {
					wppa_add_js_page_data( "\n" . 'wppaAutoColumnFrac['.wppa( 'mocc' ).'] = '.$contw.';' );
				}
				else {
					wppa_add_js_page_data( "\n" . 'wppaAutoColumnFrac['.wppa( 'mocc' ).'] = 1.0;' );
				}
				wppa_add_js_page_data( "\n" . 'wppaColWidth['.wppa( 'mocc' ).'] = 0;' );
			}
			else {
				wppa_add_js_page_data( "\n" . 'wppaAutoColumnWidth['.wppa( 'mocc' ).'] = false;' );
				wppa_add_js_page_data( "\n" . 'wppaColWidth['.wppa( 'mocc' ).'] = '.wppa_get_container_width().';' );
			}
			wppa_add_js_page_data( "\n" . 'wppaTopMoc = '.wppa( 'mocc' ).';' );
			if ( wppa_opt( 'thumbtype' ) == 'masonry-v' ) {
				wppa_add_js_page_data( "\n" . 'wppaMasonryCols['.wppa( 'mocc' ).'] = '.ceil( wppa_get_container_width() / wppa_opt( 'thumbsize' ) ).';' );
			} else {
				wppa_add_js_page_data( "\n" . 'wppaMasonryCols['.wppa( 'mocc' ).'] = 0;' );
			}
			if ( wppa( 'src_script' ) ) {
				wppa_add_js_page_data( "\n" . wppa( 'src_script' ) );
			}

			// Aspect ratio and fullsize
			if ( wppa_in_widget() == 'ss' && is_numeric( wppa( 'in_widget_frame_width' ) ) && wppa( 'in_widget_frame_width' ) > '0' ) {
				$asp = wppa( 'in_widget_frame_height' ) / wppa( 'in_widget_frame_width' );
				$fls = wppa( 'in_widget_frame_width' );
			}
			else {
				$asp = wppa_opt( 'maxheight' ) / wppa_opt( 'fullsize' );
				$fls = wppa_opt( 'fullsize' );
			}
			wppa_add_js_page_data( "\n" . 'wppaAspectRatio['.wppa( 'mocc' ).'] = '.$asp.';' );
			wppa_add_js_page_data( "\n" . 'wppaFullSize['.wppa( 'mocc' ).'] = '.$fls.';' );

			// last minute change: fullvalign with border needs a height correction in slideframe
			if ( wppa_opt( 'fullimage_border_width' ) != '' && ! wppa_in_widget() ) {
				$delta = ( 1 + wppa_opt( 'fullimage_border_width' ) ) * 2;
			} else $delta = 0;
			wppa_add_js_page_data( "\n" . 'wppaFullFrameDelta['.wppa( 'mocc' ).'] = '.$delta.';' );

			// last minute change: script %%size != default colwidth
			$temp = wppa_get_container_width() - ( 2*6 + 2*36 + 2*wppa_opt( 'bwidth' ) );
			if ( wppa_in_widget() ) $temp = wppa_get_container_width() - ( 2*6 + 2*18 + 2*wppa_opt( 'bwidth' ) );
			wppa_add_js_page_data( "\n" . 'wppaFilmStripLength['.wppa( 'mocc' ).'] = '.$temp.';' );

			// last minute change: filmstrip sizes and related stuff. In widget: half size.
			$temp = wppa_opt( 'film_thumbsize' ) + wppa_opt( 'tn_margin' );
			if ( wppa_in_widget() ) $temp /= 2;
			wppa_add_js_page_data( "\n" . 'wppaThumbnailPitch['.wppa( 'mocc' ).'] = '.$temp.';' );
			$temp = wppa_opt( 'tn_margin' ) / 2;
			if ( wppa_in_widget() ) $temp /= 2;
			wppa_add_js_page_data( "\n" . 'wppaFilmStripMargin['.wppa( 'mocc' ).'] = '.$temp.';' );
			$temp = 2*6 + 2*42 + 2*wppa_opt( 'bwidth' );
			if ( wppa_in_widget() ) $temp = 2*6 + 2*21 + 2*wppa_opt( 'bwidth' );
			wppa_add_js_page_data( "\n" . 'wppaFilmStripAreaDelta['.wppa( 'mocc' ).'] = '.$temp.';' );
			if ( wppa_in_widget() ) {
				wppa_add_js_page_data( "\n" . 'wppaIsMini['.wppa( 'mocc' ).'] = true;' );
			}
			else {
				wppa_add_js_page_data( "\n" . 'wppaIsMini['.wppa( 'mocc' ).'] = false;' );
			}

			$target = false;
			if ( wppa_in_widget() == 'ss' && wppa_switch( 'sswidget_blank' ) ) $target = true;
			if ( ! wppa_in_widget() && wppa_switch( 'slideshow_blank' ) ) $target = true;
			if ( $target ) {
				wppa_add_js_page_data( "\n" . 'wppaSlideBlank['.wppa( 'mocc' ).'] = true;' );
			}
			else {
				wppa_add_js_page_data( "\n" . 'wppaSlideBlank['.wppa( 'mocc' ).'] = false;' );
			}

			wppa_add_js_page_data( "\n" . 'wppaLightBox['.wppa( 'mocc' ).'] = "xxx";' );

			// If this occur is a slideshow, determine if its link is to lightbox. This may differ between normal slideshow or ss widget
			$is_slphoto = wppa( 'is_slide' ) && wppa( 'start_photo' ) && wppa( 'is_single' );
			if ( 'ss' == wppa_in_widget() || wppa_page( 'slide' ) || $is_slphoto ) {
				$ss_linktype = ( 'ss' == wppa_in_widget() ) ? wppa_opt( 'slideonly_widget_linktype' ) : wppa_opt( 'slideshow_linktype' );
				switch ( $ss_linktype ) {
					case 'file':
						$lbkey = 'file'; // gives anchor tag with rel="file"
						break;
					case 'lightbox':
					case 'lightboxsingle':
						$lbkey = wppa_opt( 'lightbox_name' ); // gives anchor tag with rel="lightbox" or the like
						break;
					default:
						$lbkey = ''; // results in omitting the anchor tag
						break;
				}
				wppa_add_js_page_data( 	"\n" . 'wppaLightBox[' . wppa( 'mocc' ) . '] = "' . $lbkey . '";' .
										"\n" . 'wppaConsoleLog("mocc:' . wppa( 'mocc' ) . ' lbkey:"+wppaLightBox[' . wppa( 'mocc' ) . '] );' );

				wppa_add_js_page_data( 	"\n" . 'wppaLightboxSingle[' . wppa( 'mocc' ) . '] = ' . ( wppa_opt( 'slideshow_linktype' ) == 'lightboxsingle' ? 'true': 'false' ) . ';' );
			}
		wppa_add_js_page_data( "\n" . '</script>' );

	}
	elseif ( $action == 'close' )	{

		if ( wppa_page( 'oneofone' ) ) wppa( 'portrait_only', false );
		if ( ! wppa_in_widget() ) wppa_out( '<div style="clear:both;"></div>' );

		// Add diagnostic <p> if debug is 1
		if ( wppa( 'debug' ) == '1' && wppa( 'mocc' ) == '1' ) wppa_out( '<p id="wppa-debug-'.wppa( 'mocc' ).'" style="font-size:9px; color:#070; line-size:12px;" ></p>' );

		// Init lightbox intermediate to facillitate premature clicks to lightbox when not yet document.complete
		wppa_out( "\n" . '<script type="text/javascript" >if ( typeof(wppaInitOverlay) != "undefined" ) { wppaInitOverlay(); }</script>' );

		if ( ! wppa( 'ajax' ) ) {
			wppa_out( '<div id="wppa-container-' . wppa( 'mocc' ) . '-end" ></div>' );
			wppa_out( '</div>' );
		}

		if ( wppa( 'debug' ) ) {
			$laptim = $wppa_microtime + microtime( true );
			$wppa_numqueries = get_num_queries() - $wppa_numqueries;
			if ( !is_numeric( $wppa_microtime_cum ) ) $wppa_mcrotime_cum = '0';
			$wppa_microtime_cum += $laptim;
			wppa_dbg_msg( 'Time elapsed occ '.wppa( 'mocc' ).':'.substr( $laptim, 0, 5 ).'s. Tot:'.substr( $wppa_microtime_cum, 0, 5 ).'s.' );
			wppa_dbg_msg( 'Number of queries occ '.wppa( 'mocc' ).':' . $wppa_numqueries, 'green' );
			wppa_dbg_q( 'print' );
		}
	}
	else {
		wppa_out( "\n".'<span style="color:red;">Error, wppa_container() called with wrong argument: '.$action.'. Possible values: \'open\' or \'close\'</span>' );
	}
}

function wppa_album_list( $action ) {
global $cover_count;
global $cover_count_key;

	if ( $action == 'open' ) {
		$cover_count = '0';
		$cover_count_key = 'l';
		wppa_out( '<div id="wppa-albumlist-'.wppa( 'mocc' ).'" class="albumlist">' );
	}
	elseif ( $action == 'close' ) {
		wppa_out( '</div>' );
	}
	else {
		wppa_out( '<span style="color:red;">Error, wppa_albumlist() called with wrong argument: '.$action.'. Possible values: \'open\' or \'close\'</span>' );
	}
}

function wppa_thumb_list( $action ) {
global $cover_count;
global $cover_count_key;

	if ( $action == 'open' ) {
		$cover_count = '0';
		$cover_count_key = 'l';
		wppa_out( '<div id="wppa-thumblist-'.wppa( 'mocc' ).'" class="thumblist">' );
		if ( wppa( 'current_album' ) ) wppa_bump_viewcount( 'album', wppa( 'current_album' ) );
	}
	elseif ( $action == 'close' ) {
		wppa_out( '</div>' );
	}
	else {
		wppa_out( '<span style="color:red;">Error, wppa_thumblist() called with wrong argument: '.$action.'. Possible values: \'open\' or \'close\'</span>' );
	}
}

function wppa_get_npages( $type, $array ) {

	$aps = wppa_get_pagesize( 'albums' );
	$tps = wppa_get_pagesize( 'thumbs' );
	$arraycount = is_array( $array ) ? count( $array ) : '0';
	$result = '0';
	if ( $type == 'albums' ) {
		if ( $aps != '0' ) {
			$result = ceil( $arraycount / $aps );
		}
		elseif ( $tps != '0' ) {
			if ( $arraycount ) $result = '1';
			else $result = '0';
		}
	}
	elseif ( $type == 'thumbs' ) {
		if ( wppa( 'is_cover' ) == '1' ) {		// Cover has no thumbs: 0 pages
			$result = '0';
		}
		elseif ( $arraycount <= wppa_get_mincount() ) {
			$result = '0';
		}
		elseif ( $tps != '0' ) {
			$result = ceil( $arraycount / $tps );	// Pag on: compute
		}
		else {
			$result = '1';								// Pag off: all fits on 1
		}
	}
	return $result;
}


function wppa_popup() {

	wppa_out( 	'<div' .
					' id="wppa-popup-'.wppa( 'mocc' ).'"' .
					' class="wppa-popup-frame wppa-thumb-text"' .
					' style="max-width:2048px;'.__wcs( 'wppa-thumb-text' ).'"' .
					' onmouseout="wppaPopDown( '.wppa( 'mocc' ).' );"' .
					' >' .
				'</div>' .
				'<div style="clear:both;" >' .
				'</div>' );
}

function wppa_run_slidecontainer( $type = '' ) {
global $thumbs;

//	if ( wppa( 'is_filmonly' ) ) return;

	$c = is_array( $thumbs ) ? count( $thumbs ) : '0';
	wppa_dbg_msg( 'Running slidecontainer type '.$type.' with '.$c.' elements in thumbs, is_single=' . wppa( 'is_single' ) );

	if ( wppa( 'is_single' ) && is_feed() ) {	// process feed for single image slideshow here, normal slideshow uses filmthumbs
		$style_a = wppa_get_fullimgstyle_a( wppa( 'start_photo' ) );
		$style   = $style_a['style'];
		$width   = $style_a['width'];
		$height  = $style_a['height'];
		$imgalt	 = wppa_get_imgalt( wppa( 'start_photo' ) );
		wppa_out( '<a href="' . get_permalink() . '">' .
						'<img' .
							' src="' . wppa_get_photo_url( wppa( 'start_photo' ), '', $width, $height ) . '"' .
							' style="' . $style . '"' .
							' width="' . $width . '"' .
							' height="' . $height . '" ' .
							$imgalt .
						' />' .
					'</a>'
				);
		return;
	}
	elseif ( $type == 'slideshow' ) {

		// Find slideshow start method
		switch ( wppa_opt( 'start_slide' ) ) {
			case 'run':
				$startindex = -1;
				break;
			case 'still':
				$startindex = 0;
				break;
			case 'norate':
				$startindex = -2;
				break;
			default:
				echo 'Unexpected error unknown wppa_start_slide in wppa_run_slidecontainer';
		}

		// A requested photo id overrules the method. $startid >0 is requested photo id, -1 means: no id requested
		if ( wppa( 'start_photo' ) ) $startid = wppa( 'start_photo' );
		else $startid = -1;

		// Create next ids
		$ix = 0;
		if ( $thumbs ) while ( $ix < count( $thumbs ) ) {
			if ( $ix == ( count( $thumbs )-1 ) ) $thumbs[$ix]['next_id'] = $thumbs[0]['id'];
			else $thumbs[$ix]['next_id'] = $thumbs[$ix + 1]['id'];
			$ix ++;
		}

		// Produce scripts for slides
		$index = 0;
		if ( $thumbs ) {
			$t = -microtime( true );
			wppa_add_js_page_data( "\n" . '<script type="text/javascript">' );

				foreach ( $thumbs as $thumb ) {
					if ( wppa_switch( 'next_on_callback' ) ) {
						wppa_add_js_page_data( "\n" . 'wppaStoreSlideInfo( ' . wppa_get_slide_info( $index, $thumb['id'], $thumb['next_id'] ) . ' );' );
					}
					else {
						wppa_add_js_page_data( "\n" . 'wppaStoreSlideInfo( ' . wppa_get_slide_info( $index, $thumb['id'] ) . ' );' );
					}
					if ( $startid == $thumb['id'] ) $startindex = $index;	// Found the requested id, put the corresponding index in $startindex
					$index++;
				}

			wppa_add_js_page_data( "\n" . '</script>' );
			$t += microtime( true );
			wppa_dbg_msg( 'SlideInfo took ' . $t . ' seconds.' );
		}

		wppa_add_js_page_data( "\n" . '<script type="text/javascript">' );

			// How to start if slideonly
			if ( wppa( 'is_slideonly' ) ) {
				if ( wppa_switch( 'start_slideonly' ) ) {
					$startindex = -1;	// There are no navigations, so start running, overrule everything
				}
				else {
					$startindex = 0;
				}
			}

			// Vertical align
			if ( wppa( 'is_slideonly' ) ) {
				$ali = wppa( 'ss_widget_valign' ) ? wppa( 'ss_widget_valign' ) : $ali = 'fit';
				wppa_add_js_page_data( "\n" . 'wppaFullValign['.wppa( 'mocc' ).'] = "'.$ali.'";' );
			}
			else {
				wppa_add_js_page_data( "\n" . 'wppaFullValign['.wppa( 'mocc' ).'] = "'.wppa_opt( 'fullvalign' ).'";' );
			}

			// Horizontal align
			wppa_add_js_page_data( "\n" . 'wppaFullHalign['.wppa( 'mocc' ).'] = "'.wppa_opt( 'fullhalign' ).'";' );

			// Portrait only ?
			if ( wppa( 'portrait_only' ) ) {
				wppa_add_js_page_data( "\n" . 'wppaPortraitOnly['.wppa( 'mocc' ).'] = true;' );
			}

			// Start command with appropriate $startindex: -2 = at norate, -1 run from first, >=0 still at index
			// If we use lightbox on slideshow, wait for documen.ready, if we do not use lightbox, go immediately.
			if ( wppa_opt( 'slideshow_linktype' ) == 'lightbox' || wppa_opt( 'slideshow_linktype' ) == 'lightboxsingle' || wppa_opt( 'film_linktype' ) == 'lightbox' ) {
				wppa_add_js_page_data( "\n" . 'jQuery( document ).ready( function() { wppaStartStop( '.wppa( 'mocc' ).', '.$startindex.' ); } );' );
			}
			else {
				wppa_add_js_page_data( "\n" . 'wppaStartStop( '.wppa( 'mocc' ).', '.$startindex.' );' );
			}

		wppa_add_js_page_data( "\n" . '</script>' );

	}
	else {
		wppa_out( '<span style="color:red;">' .
						'Error, wppa_run_slidecontainer() called with wrong argument: ' . $type . '. Possible values: \'single\' or \'slideshow\'' .
					'</span>'
				);
	}
}

function wppa_is_pagination() {

	if ( ( wppa_get_pagesize( 'albums' ) == '0' && wppa_get_pagesize( 'thumbs' ) == '0' ) ) return false;
	else return true;
}


function wppa_get_preambule() {

	if ( ! wppa_switch( 'slide_wrap' ) ) return '0';
	$result = is_numeric( wppa_opt( 'colwidth' ) ) ? wppa_opt( 'colwidth' ) : wppa_opt( 'fullsize' );
	$result = ceil( ceil( $result / wppa_opt( 'thumbsize' ) ) / 2 ) + 1;
	return $result;
}

function wppa_dummy_bar( $msg = '' ) {

	wppa_out( '<div style="margin:4px 0; '.__wcs( 'wppa-box' ).__wcs( 'wppa-nav' ).'text-align:center;">'.$msg.'</div>' );
}

function wppa_rating_count_by_id( $id = '' ) {

	wppa_out( wppa_get_rating_count_by_id( $id ) );
}

function wppa_rating_by_id( $id = '', $opt = '' ) {

	wppa_out( wppa_get_rating_by_id( $id, $opt ) );
}

function wppa_get_cover_width( $type ) {

	$conwidth = wppa_get_container_width();
	$cols = wppa_get_cover_cols( $type );

	$result = floor( ( $conwidth - ( 8 * ( $cols - 1 ) ) ) / $cols );

	$result -= ( 2 * ( 6 + wppa_opt( 'bwidth' ) ) );	// 2 * ( padding + border )
	return $result;
}

function wppa_get_text_frame_style( $photo_left, $type ) {

	if ( wppa_in_widget() ) {
		$result = '';
	}
	else {
		if ( $type == 'thumb' ) {
			$width = wppa_get_cover_width( $type );
			$width -= 13;	// margin
			$width -= 2; 	// border
			$width -= wppa_opt( 'smallsize' );

			if ( $photo_left ) {
				$result = 'style="width:'.$width.'px; float:right;"';
			}
			else {
				$result = 'style="width:'.$width.'px; float:left;"';
			}
		}
		elseif ( $type == 'cover' ) {
			$width = wppa_get_cover_width( $type );
			$photo_pos = $photo_left;
			switch ( $photo_pos ) {
				case 'left':
					$width -= 13;	// margin
					$width -= 2; 	// border
					$width -= wppa_opt( 'smallsize' );
					$result = 'style="width:'.$width.'px; float:right;'.__wcs( 'wppa-cover-text-frame' ).'"';
					break;
				case 'right':
					$width -= 13;	// margin
					$width -= 2; 	// border
					$width -= wppa_opt( 'smallsize' );
					$result = 'style="width:'.$width.'px; float:left;'.__wcs( 'wppa-cover-text-frame' ).'"';
					break;
				case 'top':
//					$width -= 13;
					$result = 'style="'.__wcs( 'wppa-cover-text-frame' ).'"';//'style="width:'.$width.'px;"';
					break;
				case 'bottom':
//					$width -= 13;
					$result = 'style="'.__wcs( 'wppa-cover-text-frame' ).'"';//'style="width:'.$width.'px;"';
					break;
				default:
					wppa_dbg_msg( 'Illegal $photo_pos in wppa_get_text_frame_style', 'red' );
			}
		}
		else wppa_dbg_msg( 'Illegal $type in wppa_get_text_frame_style', 'red' );
	}
	return $result;
}

function wppa_get_textframe_delta() {

	$delta = wppa_opt( 'smallsize' );
	$delta += ( 2 * ( 7 + wppa_opt( 'bwidth' ) + 4 ) + 5 + 2 );	// 2 * ( padding + border + photopadding ) + margin
	return $delta;
}

function wppa_step_covercount( $type ) {
global $cover_count;
global $cover_count_key;

	$key = 'm';
	$cols = wppa_get_cover_cols( $type );
	$cover_count++;
	if ( $cover_count == $cols ) {
		$cover_count = '0'; // Row is full
		$key = 'l';
	}
	if ( $cover_count + '1' == $cols ) {
		$key = 'r';
	}
	$cover_count_key = $key;
}

function wppa_get_cover_cols( $type ) {

	$conwidth = wppa_get_container_width();

	$cols = ceil( $conwidth / wppa_opt( 'max_cover_width' ) );

	// Exceptions
	if ( wppa( 'auto_colwidth' ) ) $cols = '1';
	if ( ( $type == 'cover' ) && ( wppa( 'album_count' ) < '2' ) ) $cols = '1';
	if ( ( $type == 'thumb' ) && ( wppa( 'thumb_count' ) < '2' ) ) $cols = '1';
	return $cols;
}

function wppa_get_box_width() {

	$result = wppa_get_container_width();
	$result -= 12;	// 2 * padding
	$result -= 2 * wppa_opt( 'bwidth' );
	return $result;
}

function wppa_get_box_delta() {
	return wppa_get_container_width() - wppa_get_box_width();
}

function wppa_force_balance_pee( $xtext ) {

	$text = $xtext;	// Make a local copy
	$done = false;
	$temp = strtolower( $text );

	// see if this chunk ends in <p> in which case we remove that in stead of appending a </p>
	$len = strlen( $temp );
	if ( $len > 3 ) {
		if ( substr( $temp, $len - 3 ) == '<p>' ) {
			$text = substr( $text, 0, $len - 3 );
			$temp = strtolower( $text );
		}
	}

	$opens = substr_count( $temp, '<p' );
	$close = substr_count( $temp, '</p' );
	// append a close
	if ( $opens > $close ) {
		$text .= '</p>';
	}
	// prepend an open
	if ( $close > $opens ) {
		$text = '<p>'.$text;
	}
	return $text;
}

function wppa_mphoto() {

	$id 		= wppa( 'single_photo' );
	$width 		= wppa_get_container_width();
	$height 	= floor( $width / wppa_get_ratio( $id ) );
	$usethumb	= wppa_use_thumb_file( $id, $width, $height );
	$src 		= $usethumb ? wppa_get_thumb_url( $id, '', $width, $height ) : wppa_get_photo_url( $id, '', $width, $height );
	if ( wppa_has_audio( $id ) ) {
		$src = wppa_fix_poster_ext( $src, $id );
	}

	if ( ! wppa_in_widget() ) wppa_bump_viewcount( 'photo', $id );

	$autocol = wppa( 'auto_colwidth' ) || ( $width > 0 && $width <= 1.0 );

	// The initial width is $width if not autocol, else it should default to initial column width when auto
	// or initial column width * fraction
	if ( $autocol ) {
		if ( $width == 'auto' ) {
			$contwidth = wppa_opt( 'initial_colwidth' );
		}
		else {
			$contwidth = wppa_opt( 'initial_colwidth' ) * $width;
		}
	}
	else {
		$contwidth = $width;
	}

	// Open the pseudo container
	$captwidth = $contwidth + '10';
	wppa_out( '<div id="wppa-container-'.wppa( 'mocc' ).'" class="wppa-mphoto-'.wppa( 'mocc' ).' wp-caption' );
		if ( wppa( 'align' ) != '' ) wppa_out( ' align' . wppa( 'align' ) );
	wppa_out( '" style="width: '.$captwidth.'px; position:relative;">' );

		// The script for responsive
		wppa_add_js_page_data( "\n" . '<script type="text/javascript">' );
		if ( $autocol ) {
			wppa_add_js_page_data( "\n" . 'wppaAutoColumnWidth['.wppa( 'mocc' ).'] = true;' );
			if ( $width > 0 && $width <= 1.0 ) {
				wppa_add_js_page_data( "\n" . 'wppaAutoColumnFrac['.wppa( 'mocc' ).'] = '.$width.';' );
			}
			else {
				wppa_add_js_page_data( "\n" . 'wppaAutoColumnFrac['.wppa( 'mocc' ).'] = 1.0;' );
			}
			wppa_add_js_page_data( "\n" . 'wppaColWidth['.wppa( 'mocc' ).'] = 0;' );
		}
		wppa_add_js_page_data( "\n" . 'wppaTopMoc = '.wppa( 'mocc' ).';' );
		wppa_add_js_page_data( "\n" . '</script>' );

		// The link
		$link = wppa_get_imglnk_a( 'mphoto', $id );
		if ( $link ) {
			if ( $link['is_lightbox'] ) {
				$lbtitle 	= wppa_get_lbtitle( 'mphoto', $id );
				$videohtml 	= esc_attr( wppa_get_video_body( $id ) );
				$audiohtml 	= esc_attr( wppa_get_audio_body( $id ) );
				$videox 	= wppa_get_videox( $id );
				$videoy 	= wppa_get_videoy( $id );
				wppa_out( 	'<a' .
								' href="' . $link['url'] . '"' .
								( $lbtitle ? ' ' . wppa( 'lbtitle' ) . '="' . $lbtitle . '"' : '' ) .
								( $videohtml ? ' data-videohtml="' . $videohtml . '"' : '' ) .
								( $audiohtml ? ' data-audiohtml="' . $audiohtml . '"' : '' ) .
								( $videox ? ' data-videonatwidth="' . $videox . '"' : '' ) .
								( $videoy ? ' data-videonatheight="' . $videoy . '"' : '' ) .
								' ' . wppa( 'rel' ) . '="' . wppa_opt( 'lightbox_name' ) . '"' .
								( $link['target'] ? ' target="' . $link['target'] . '"' : '' ) .
								' class="thumb-img"' .
								' id="a-' . $id . '-' . wppa( 'mocc' ) . '"' .
								' >'
						);
			}
			else {
				wppa_out( 	'<a' .
								' href="' . $link['url'] . '"' .
								( $link['title'] ? ' title="' . $link['title'] . '"' : '' ) .
								( $link['target'] ? ' target="' . $link['target'] . '"' : '' ) .
								' class="thumb-img"' .
								' id="a-' . $id . '-' . wppa( 'mocc' ) . '"' .
								' >'
						);
			}
		}

		// The image
		$title = $link ? $link['title'] : esc_attr( stripslashes( wppa_get_photo_name( $id ) ) );
		if ( $link['is_lightbox'] ) {
			$style = ' cursor:url( '.wppa_get_imgdir().wppa_opt( 'magnifier' ).' ),pointer;';
			$title = wppa_zoom_in( $id );
		}
		else {
			$style = '';
		}
		$style .= 'box-sizing:content-box;';

		if ( wppa_is_video( $id ) ) {
			if ( $autocol ) {
				wppa_out( wppa_get_video_html( array( 	'id' 			=> $id,
														'controls' 		=> ! $link['is_lightbox'],
														'style' 		=> $style,
														'class' 		=> 'size-medium wppa-mphoto wppa-mimg-'.wppa( 'mocc' )
													)
											)
						);
			}
			else {
				wppa_out( wppa_get_video_html( array( 	'id' 			=> $id,
														'width' 		=> $width,
														'height' 		=> $height,
														'controls' 		=> ! $link['is_lightbox'],
														'style' 		=> $style,
														'class' 		=> 'size-medium wppa-mphoto wppa-mimg-'.wppa( 'mocc' )
													)
											)
						);
			}
		}
		else {
			wppa_out( 	'<img' .
							' src="' . $src . '"' .
							' ' . wppa_get_imgalt( $id ) .
							' style="' . $style . '"' .
							' class="size-medium wppa-mphoto wppa-mimg-' . wppa( 'mocc' ) . '"' .
							( $title ? ' title="' . $title . '"' : '' ) .
							' width="' . $width . '"' .
							' height="' . $height . '"' .
							' />'
					);
		}
		if ( $link ) {
			wppa_out( '</a>' );
		}

		// Add audio?				mphoto
		if ( wppa_has_audio( $id ) ) {
			wppa_out( '<div style="position:relative;z-index:11;" >' );

			wppa_out( wppa_get_audio_html( array(
													'id' 		=> $id,
													'cursor' 	=> 'cursor:pointer;',
													'style' 	=> $style .
																	'position:relative;' .
																	'padding-top:0;' .
																	'padding-bottom:0;' .
																	'background-color:transparent;' .
																	'border:none;' .
																	'margin:0;' .
																	'width:98%;',
													'class' 	=> 'size-medium wp-caption wppa-mphoto_ wppa-mimg-' . wppa( 'mocc' ),
												)
										)
					);

			wppa_out( '</div>' );
		}

		// The subtitle
		wppa_out( '<p class="wp-caption-text">' . wppa_get_photo_desc( $id ) . '</p>' );

		// The share buttons
		if ( wppa_switch( 'share_on_mphoto' ) ) {
			wppa_out( wppa_get_share_html( $id, 'mphoto', false ) );
		}

	wppa_out( '</div>' );
}

// Like mphoto but without the caption and with the fullsize background/border
function wppa_sphoto() {

	$id 		= wppa( 'single_photo' );
	$width 		= wppa_get_container_width();
	$height 	= floor( $width / wppa_get_ratio( $id ) );
	$usethumb	= wppa_use_thumb_file( $id, $width, $height );
	$src 		= $usethumb ? wppa_get_thumb_url( $id, '', $width, $height ) : wppa_get_photo_url( $id, '', $width, $height );
	if ( wppa_has_audio( $id ) ) {
		$src = wppa_fix_poster_ext( $src, $id );
	}

	if ( ! wppa_in_widget() ) wppa_bump_viewcount( 'photo', $id );

	$autocol = wppa( 'auto_colwidth' ) || ( $width > 0 && $width <= 1.0 );

	// The initial width is $width if not autocol, else it should default to initial column width when auto
	// or initial column width * fraction
	if ( $autocol ) {
		if ( $width == 'auto' ) {
			$contwidth = wppa_opt( 'initial_colwidth' );
		}
		else {
			$contwidth = wppa_opt( 'initial_colwidth' ) * $width;
		}
	}
	else {
		$contwidth = $width;
	}

	// Open the pseudo container
	wppa_out( 	'<div' .
					' id="wppa-container-' . wppa( 'mocc' ) . '"' .
					' class="' .
						( wppa( 'align' ) ? 'align' . wppa( 'align' ) : '' ) .
						' wppa-sphoto' .
						' wppa-sphoto-' . wppa( 'mocc' ) .
						'"' .
					' style="width:' . $contwidth . 'px;position:relative;"' .
					' >'
			);

		// The script for responsive
		wppa_add_js_page_data( "\n" . '<script type="text/javascript">' );
		if ( $autocol ) {
			wppa_add_js_page_data( "\n" . 'wppaAutoColumnWidth['.wppa( 'mocc' ).'] = true;' );
			if ( $width > 0 && $width <= 1.0 ) {
				wppa_add_js_page_data( "\n" . 'wppaAutoColumnFrac['.wppa( 'mocc' ).'] = '.$width.';' );
			}
			else {
				wppa_add_js_page_data( "\n" . 'wppaAutoColumnFrac['.wppa( 'mocc' ).'] = 1.0;' );
			}
			wppa_add_js_page_data( "\n" . 'wppaColWidth['.wppa( 'mocc' ).'] = 0;' );
		}
		wppa_add_js_page_data( "\n" . 'wppaTopMoc = '.wppa( 'mocc' ).';' );
		wppa_add_js_page_data( "\n" . '</script>' );

		wppa( 'portrait_only', true );
		$fis 	= wppa_get_fullimgstyle_a( $id );
		$width	= $fis['width'];
		$height	= $fis['height'];
		$style	= $fis['style'];
		$style  .= 'box-sizing:content-box;vertical-align:middle;';

		// The link
		$link = wppa_get_imglnk_a( 'sphoto', $id );
		if ( $link ) {
			if ( $link['is_lightbox'] ) {
				$lbtitle 	= wppa_get_lbtitle( 'sphoto', $id );
				$videobody 	= esc_attr( wppa_get_video_body( $id ) );
				$audiobody 	= esc_attr( wppa_get_audio_body( $id ) );
				$videox 	= wppa_get_videox( $id );
				$videoy 	= wppa_get_videoy( $id );
				wppa_out( 	'<a' .
								' href="' . $link['url'] . '"' .
								( $lbtitle ? ' ' . wppa( 'lbtitle' ) . '="'.$lbtitle.'"' : '' ) .
								( $videobody ? ' data-videohtml="' . $videobody . '"' : '' ) .
								( $audiobody ? ' data-audiohtml="' . $audiobody . '"' : '' ) .
								( $videox ? ' data-videonatwidth="' . $videox . '"' : '' ) .
								( $videoy ? ' data-videonatheight="' . $videoy . '"' : '' ) .
								' ' . wppa( 'rel' ) . '="'.wppa_opt( 'lightbox_name' ).'"' .
								( $link['target'] ? ' target="' . $link['target'] . '"' : '' ) .
								' class="thumb-img"' .
								' id="a-' . $id . '-' . wppa( 'mocc' ) . '"' .
								' >'
						);
			}
			else {
				wppa_out( 	'<a' .
								' href="' . $link['url'] . '"' .
								' title="' . $link['title'] . '"' .
								' target="' . $link['target'] . '"' .
								' class="thumb-img"' .
								' id="a-' . $id . '-' . wppa( 'mocc' ) . '"' .
								' >'
						);
			}
		}

		// The image
		$title = $link ? esc_attr( $link['title'] ) : esc_attr( stripslashes( wppa_get_photo_name( $id ) ) );
		if ( $link['is_lightbox'] ) {
			$style .= ' cursor:url( '.wppa_get_imgdir().wppa_opt( 'magnifier' ).' ),pointer;';
			$title = wppa_zoom_in( $id );
		}

		if ( wppa_is_video( $id ) ) {
			if ( $autocol ) {
				wppa_out( wppa_get_video_html( array( 	'id' 			=> $id,
														'controls' 		=> ! $link['is_lightbox'],
														'style' 		=> $style,
														'class' 		=> 'size-medium wppa-sphoto wppa-simg-'.wppa( 'mocc' ),
													)
											)
						);
			}
			else {
				wppa_out( wppa_get_video_html( array(	'id' 			=> $id,
														'width' 		=> $width,
														'height' 		=> $height,
														'controls' 		=> ! $link['is_lightbox'],
														'style' 		=> $style,
														'class' 		=> 'size-medium wppa-sphoto wppa-simg-'.wppa( 'mocc' ),
													)
												)
						);
			}
		}
		else {
			wppa_out( '<img' .
							' src="' . $src . '"' .
							' ' . wppa_get_imgalt( $id ) .
							' class="size-medium wppa-sphoto wppa-simg-' . wppa( 'mocc' ) . '"' .
							' ' . wppa_get_imgalt( $id ) .
							( $title ? ' title="' . $title . '" ' : '' ) .
							' style="' . $style . '"' .
							( $autocol ? '' : ' width="' . $width . '" height="' . $height . '" ' ) .
						'/ >'
					);
		}

		// Close the link
		if ( $link ) {
			wppa_out( '</a>' );
		}

		// Add audio?			sphoto
		if ( wppa_has_audio( $id ) ) {
			wppa_out( '<div style="position:relative;z-index:11;" >' );
			$margl = ( wppa_opt( 'fullimage_border_width' ) === '' ) ? 0 : wppa_opt( 'fullimage_border_width' ) + 1;
			$margb = $margl;

			wppa_out( wppa_get_audio_html( array(	'id' 		=> $id,
													'cursor' 	=> 'cursor:pointer;',
													'style' 	=> $style .
																	'position:absolute;' .
																	'left:' . $margl . 'px;' .
																	'bottom:' . $margb . 'px;' .
																	'padding:0;' .
																	'margin:0;' .
																	'border:none;' .
																	'height:' . wppa_get_audio_control_height() . 'px;' .
																	'border-radius:0;',
													'class' 	=> 'size-medium wppa-sphoto wppa-simg-'.wppa( 'mocc' ),
												)
										)
					);

			wppa_out( '</div>' );
		}

	// The pseudo container
	wppa_out( '</div>' );
}

// returns aspect ratio ( w/h ), or 1 on error
function wppa_get_ratio( $id ) {
global $wpdb;

	if ( ! wppa_is_int( $id ) ) return '1';	// Not 0 to prevent divide by zero

	$temp = wppa_get_imagexy( $id );

	if ( $temp['1'] ) {
		return $temp['0'] / $temp['1'];
	}
	else {
		return '1';
	}
}

function wppa_is_photo_new( $id ) {

	$thumb = wppa_cache_thumb( $id );

	$birthtime = $thumb['timestamp'];
	$timnow = time();
	$isnew = ( ( $timnow - $birthtime ) < wppa_opt( 'max_photo_newtime' ) );

	return $isnew;
}

function wppa_is_photo_modified( $id ) {

	$thumb = wppa_cache_thumb( $id );

	$modtime = $thumb['modified'];
	$timnow = time();
	$isnew = ( ( $timnow - $modtime ) < wppa_opt( 'max_photo_modtime' ) );

	return $isnew;
}

function wppa_is_album_new( $id ) {
global $wpdb;
global $wppa_children;

	$album = wppa_cache_album( $id );
	$birthtime = $album['timestamp'];
	$timnow = time();
	$isnew = ( ( $timnow - $birthtime ) < wppa_opt( 'max_album_newtime' ) );

	if ( $isnew ) return true;

	// A new ( grand )child?
	if ( isset( $wppa_children[$id] ) ) {
		$children = $wppa_children[$id];
	}
	else {
		$children = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `".WPPA_ALBUMS."` WHERE `a_parent` = %s", $id ), ARRAY_A );
		$wppa_children[$id] = $children;
		wppa_dbg_q( 'Q-ian' );
	}

	if ( $children ) {
		foreach ( $children as $child ) {
			if ( wppa_is_album_new( $child['id'] ) ) return true;	// Found one? Done!
		}
	}
	return false;
}

function wppa_is_album_modified( $id ) {
global $wpdb;
global $wppa_children;

	$album = wppa_cache_album( $id );
	$modtime = $album['modified'];
	$timnow = time();
	$isnew = ( ( $timnow - $modtime ) < wppa_opt( 'max_album_modtime' ) );

	if ( $isnew ) return true;

	// A modified ( grand )child?
	if ( isset( $wppa_children[$id] ) ) {
		$children = $wppa_children[$id];
	}
	else {
		$children = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `".WPPA_ALBUMS."` WHERE `a_parent` = %s", $id ), ARRAY_A );
		$wppa_children[$id] = $children;
		wppa_dbg_q( 'Q-iam' );
	}

	if ( $children ) {
		foreach ( $children as $child ) {
			if ( wppa_is_album_modified( $child['id'] ) ) return true;	// Found one? Done!
		}
	}
	return false;
}

function wppa_get_photo_id_by_name( $xname, $album = '0' ) {
global $wpdb;
global $allphotos;

	if ( wppa_is_int( $xname ) ) {
		return $xname; // Already nemeric
	}

	$name = wppa_decode_uri_component( $xname );
	$name = str_replace( '\'', '%', $name );	// A trick for single quotes
	$name = str_replace( '"', '%', $name );		// A trick for double quotes
	$name = stripslashes( $name );

	if ( wppa_is_int( $album ) ) {
		$alb = $album;
	}
	else {
		$albums = wppa_series_to_array( $album );
		if ( is_array( $albums ) ) {
			$alb = implode( " OR `album` = ", $albums );
		}
		else {
			$alb = wppa_get_album_id_by_name( $album );
		}
	}

	if ( $alb ) {
		$pid = $wpdb->get_var( "SELECT `id` FROM `".WPPA_PHOTOS."` WHERE `name` LIKE '%".$name."%' AND ( `album` = ".$alb." ) LIMIT 1" );
	}
	else {
		$pid = $wpdb->get_var( "SELECT `id` FROM `".WPPA_PHOTOS."` WHERE `name` LIKE '%".$name."%' LIMIT 1" );
	}

	if ( $pid ) {
		wppa_dbg_msg( 'Pid '.$pid.' found for '.$name );
	}
	else {
		wppa_dbg_msg( 'No pid found for '.$name );
	}
	return $pid;
}

function wppa_get_album_id_by_name( $xname, $report_dups = false ) {
global $wpdb;
global $allalbums;

	if ( wppa_is_int( $xname ) ) {
		return $xname;	// Already numeric
	}
	if ( wppa_is_enum( $xname ) ) {
		return $xname; 	// Is enumeration
	}

	$name = wppa_decode_uri_component( $xname );
	$name = str_replace( '\'', '%', $name );	// A trick for single quotes
	$name = str_replace( '"', '%', $name );		// A trick for double quotes
	$name = stripslashes( $name );

	$query = "SELECT * FROM `".WPPA_ALBUMS."` WHERE `name` LIKE '%".$name."%'";
	$albs = $wpdb->get_results( $query, ARRAY_A );

	if ( $albs ) {
		if ( count( $albs ) == 1 ) {
			wppa_dbg_msg( 'Alb '.$albs[0]['id'],' found for '.$xname );
			$aid = $albs[0]['id'];
		}
		else {
			wppa_dbg_msg( 'Dups found for '.$xname );
			if ( $report_dups ) {
				$aid = false;
			}
			else {

				// Find the best match
				foreach ( $albs as $alb ) {
					$aname = __( $alb['name'] );				// Possibly qTranslate translated
					$aname = str_replace( '\'', '%', $aname );	// A trick for single quotes
					$aname = str_replace( '"', '%', $aname );	// A trick for double quotes
					$aname = stripslashes( $aname );
					wppa_dbg_msg( 'Testing '.$aname.' for '.$name.' (get_album_id_by_name)' );
					if ( $aname == $name ) {
						$aid = $alb['id'];
					}
				}

				// No perfect match, take the first 'like' option
				if ( ! $aid ) {
					$aid = $albs[0]['id'];
				}
			}
		}
	}
	else {
		$aid = false;
	}

	if ( $aid ) {
		wppa_dbg_msg( 'Aid '.$aid.' found for '.$name );
	}
	else {
		wppa_dbg_msg( 'No aid found for '.$name );
	}
	return $aid;
}

// Perform the frontend Create album, Upload photo and Edit album
// wppa_user_upload_on must be on for any of these functions to be enabled
function wppa_user_upload() {
global $wpdb;
static $done;

	wppa_dbg_msg( 'Usr_upl entered' );

	if ( $done ) return;					// Already done
	$done = true;							// Mark as done

	// Upload possible?
	$may_upload = wppa_switch( 'user_upload_on' );
	if ( wppa_switch( 'user_upload_login' ) ) {
		if ( ! is_user_logged_in() ) $may_upload = false;					// Must login
	}

	// Create album possible?
	$may_create = wppa_switch( 'user_create_on' );
	if ( wppa_switch( 'user_create_login' ) ) {
		if ( ! is_user_logged_in() ) $may_create = false;					// Must login
	}

	// Edit album possible?
	$may_edit = wppa_switch( 'user_album_edit_on' );

	// Do create
	if ( $may_create ) {
		if ( wppa_get_post( 'wppa-fe-create' ) ) {	// Create album
			$nonce = wppa_get_post( 'nonce' );
			if ( wppa_get_post( 'wppa-album-name' ) ) {
				$albumname = trim( strip_tags( wppa_get_post( 'wppa-album-name' ) ) );
			}
			if ( ! wppa_sanitize_file_name( $albumname ) ) {
				$albumname = __('New Album', 'wp-photo-album-plus');
			}
			$ok = wp_verify_nonce( $nonce, 'wppa-album-check' );
			if ( ! $ok ) die( '<b>' . __( 'ERROR: Illegal attempt to create an album.' , 'wp-photo-album-plus') . '</b>' );
			// Check captcha
			$captkey = wppa_get_randseed( 'session' );
			if ( ! wppa_check_captcha( $captkey ) ) {
				wppa_alert( __( 'Wrong captcha, please try again' , 'wp-photo-album-plus') );
				return;
			}
			$parent = strval( intval( wppa_get_post( 'wppa-album-parent' ) ) );
			if ( ! wppa_user_is( 'administrator' ) && wppa_switch( 'default_parent_always' ) ) {
				$parent = wppa_opt( 'default_parent' );
			}
			$album = wppa_create_album_entry( array( 	'name' 			=> $albumname,
														'description' 	=> strip_tags( wppa_get_post( 'wppa-album-desc' ) ),
														'a_parent' 		=> $parent,
														'owner' 		=> wppa_switch( 'frontend_album_public' ) ? '--- public ---' : wppa_get_user()
														 ) );
			if ( $album ) {
				if ( wppa_switch( 'fe_alert' ) ) {
					wppa_alert( sprintf( __( 'Album #%s created' , 'wp-photo-album-plus'), $album ) );
				}
				wppa_flush_treecounts( $parent );
				wppa_create_pl_htaccess();
			}
			else {
				wppa_alert( __( 'Could not create album' , 'wp-photo-album-plus') );
			}
		}
	}

	// Do Upload
	if ( $may_upload ) {
		if ( wppa_get_post( 'wppa-upload-album' ) ) {	// Upload photo
			$nonce = wppa_get_post( 'nonce' );
			$ok = wp_verify_nonce( $nonce, 'wppa-check' );
			if ( ! $ok ) {
				die( '<b>' . __( 'ERROR: Illegal attempt to upload a file.' , 'wp-photo-album-plus') . '</b>');
			}

//print_r($_POST);

			$alb = wppa_get_post( 'wppa-upload-album' );

			if ( is_array( $_FILES ) ) {
				$bret = true;
				$filecount = '1';
				$done = '0';
				$fail = '0';
				foreach ( $_FILES as $file ) {
					if ( ! is_array( $file['error'] ) ) {
						$bret = wppa_do_frontend_file_upload( $file, $alb );	// this should no longer happen since the name is incl []
						if ( $bret ) $done++;
						else $fail++;
					}
					else {
						$filecount = count( $file['error'] );
						for ( $i = '0'; $i < $filecount; $i++ ) {
							if ( $bret ) {
								$f['error'] = $file['error'][$i];
								$f['tmp_name'] = $file['tmp_name'][$i];
								$f['name'] = $file['name'][$i];
								$f['type'] = $file['type'][$i];
								$f['size'] = $file['size'][$i];
								$bret = wppa_do_frontend_file_upload( $f, $alb );
								if ( $bret ) $done++;
								else $fail++;
							}
						}
					}
				}
				$points = '0';
				$alert = '';
				$reload = wppa_switch( 'home_after_upload' ) && $done ? 'home' : false;

				if ( $done ) {

					//SUCCESSFUL UPLOAD, ADD POINTS
					$points = wppa_opt( 'cp_points_upload' ) * $done;
					$bret = wppa_add_credit_points( $points, __( 'Photo upload' ,'wp-photo-album-plus' ) );
					$alert .= sprintf( _n( '%d photo successfully uploaded', '%d photos successfully uploaded', $done, 'wp-photo-album-plus' ), $done );
					if ( $bret ) $alert .= ' ' . sprintf( __( '%s points added' ,'wp-photo-album-plus'), $points );
					if ( wppa_switch( 'fe_alert' ) ) {
						wppa_alert( $alert, $reload );
					}
					else {
						wppa_alert( '', $reload );
					}
				}
				if ( $fail ) {
					if ( ! $done ) {
						$alert .= __( 'Upload failed' , 'wp-photo-album-plus');
					}
					else {
						$alert .= sprintf( _n( '%d upload failed', '%d uploads failed', $fail, 'wp-photo-album-plus' ), $fail );
					}
					wppa_alert( $alert, $reload );
				}
			}
		}
	}

	// Do Edit
	if ( $may_edit ) {
		if ( wppa_get_post( 'wppa-albumeditsubmit' ) ) {
			$alb 			= wppa_get_post( 'wppa-albumeditid' );
			$name 			= wppa_get_post( 'wppa-albumeditname' );
			$name 			= trim( strip_tags( $name ) );
			if ( ! wppa_sanitize_file_name( $name ) ) {	// Empty album name is not allowed
				$name = 'Album-#'.$alb;
			}
			$description 	= wppa_get_post( 'wppa-albumeditdesc' );
			if ( ! wp_verify_nonce( wppa_get_post( 'wppa-albumeditnonce' ), 'wppa_nonce_'.$alb ) ) die( 'Security check failure' );
			wppa_update_album( array( 'id' => $alb, 'name' => $name, 'description' => $description, 'modified' => time() ) );
			wppa_create_pl_htaccess();
		}
	}
}

// Subroutine to upload one file in the frontend
function wppa_do_frontend_file_upload( $file, $alb ) {
global $wpdb;

	// Log upload attempt
	wppa_log( 'Upl', 'FE Upload attempt of file '.$file['name'].', size='.filesize($file['tmp_name']) );

	$album = wppa_cache_album( $alb );

	if ( ! wppa_allow_uploads( $alb ) || ! wppa_allow_user_uploads() ) {
		wppa_alert( __( 'Max uploads reached' , 'wp-photo-album-plus') );
		return false;
	}
	if ( $file['error'] != '0' ) {
		wppa_alert( __( 'Error during upload' , 'wp-photo-album-plus') );
		return false;
	}
	$imgsize = getimagesize( $file['tmp_name'] );
	if ( !is_array( $imgsize ) ) {
		wppa_alert( __( 'Uploaded file is not an image' , 'wp-photo-album-plus') );
		return false;
	}
	if ( $imgsize[2] < 1 || $imgsize[2] > 3 ) {
		wppa_alert( sprintf( __( 'Only gif, jpg and png image files are supported. Returned filetype = %d.' , 'wp-photo-album-plus'), $imagesize[2] ) );
		return false;
	}
	$ms = wppa_opt( 'upload_fronend_maxsize' );
	if ( $ms ) {	// Max size configured
		if ( $imgsize[0] > $ms || $imgsize[0] > $ms ) {
			wppa_alert( sprintf( __( 'Uploaded file is larger than the allowed maximum of %d x %d pixels.' , 'wp-photo-album-plus'), $ms, $ms ) );
			return false;
		}
	}
	if ( wppa_switch( 'void_dups' ) ) {	// Check for already exists
		if ( wppa_file_is_in_album( wppa_sanitize_file_name( $file['name'] ), $alb ) ) {
			wppa_alert( sprintf( __( 'Uploaded file %s already exists in this album.' , 'wp-photo-album-plus'), wppa_sanitize_file_name( $file['name'] ) ) );
			return false;
		}
	}
	$mayupload = wppa_check_memory_limit( '', $imgsize[0], $imgsize[1] );
	if ( $mayupload === false ) {
		$maxsize = wppa_check_memory_limit( false );
		if ( is_array( $maxsize ) ) {
			wppa_alert( sprintf( __( 'The image is too big. Max photo size: %d x %d (%2.1f MegaPixel)' , 'wp-photo-album-plus'), $maxsize['maxx'], $maxsize['maxy'], $maxsize['maxp']/( 1024*1024 ) ) );
			return false;
		}
	}
	switch( $imgsize[2] ) { 	// mime type
		case 1: $ext = 'gif'; break;
		case 2: $ext = 'jpg'; break;
		case 3: $ext = 'png'; break;
	}
	if ( wppa_get_post( 'user-name' ) ) {
		$name = wppa_get_post( 'user-name' );
	}
	else {
		$name = $file['name'];
	}
	$name 		= wppa_sanitize_photo_name( $name );
	$desc 		= balanceTags( wppa_get_post( 'user-desc' ), true );
	$linktarget = '_self';
	$status 	= ( wppa_switch( 'upload_moderate' ) && ! current_user_can( 'wppa_admin' ) ) ? 'pending' : 'publish';
	$filename 	= wppa_sanitize_file_name( $file['name'] );
	$id 		= wppa_create_photo_entry( array( 'album' => $alb, 'ext' => $ext, 'name' => $name, 'description' => $desc, 'status' => $status, 'filename' => $filename, ) );

	if ( ! $id ) {
		wppa_alert( __( 'Could not insert photo into db.' , 'wp-photo-album-plus') );
		return false;
	}
	else {
		wppa_save_source( $file['tmp_name'], $filename, $alb );
		wppa_update_album( array( 'id' => $alb, 'modified' => time() ) );
		wppa_flush_treecounts( $alb );
		wppa_flush_upldr_cache( 'photoid', $id );
	}
	if ( wppa_make_the_photo_files( $file['tmp_name'], $id, $ext ) ) {

		// Repair photoname if not standard
		if ( ! wppa_get_post( 'user-name' ) ) {
			wppa_set_default_name( $id, $file['name'] );
		}

		// Custom data
		if ( wppa_switch( 'fe_custom_fields' ) ) {
			$custom_data = array( '', '', '', '', '', '', '', '', '', '' );
			for ( $i = '0'; $i < '10' ; $i++ ) {
				if ( isset( $_POST['wppa-user-custom-'.$i] ) ) {
					$custom_data[$i] = strip_tags( $_POST['wppa-user-custom-'.$i] );
				}
			}
			wppa_update_photo( array( 'id' => $id, 'custom' => serialize( $custom_data ) ) );
		}

		// Default tags
		wppa_set_default_tags( $id );

		// Custom tags
		$tags = wppa_get_photo_item( $id, 'tags' );
		$oldt = $tags;
		for ( $i = '1'; $i < '4'; $i++ ) {
			if ( isset( $_POST['wppa-user-tags-'.$i] ) ) {	// Existing tags
				$tags .= ','.implode( ',', $_POST['wppa-user-tags-'.$i] );
			}
		}
		if ( isset( $_POST['wppa-new-tags'] ) ) {	// New tags
			$newt = $_POST['wppa-new-tags'];
			$tags .= ','.$newt;
		}
		else {
			$newt = '';
		}
		$tags = wppa_sanitize_tags( str_replace( array( '\'', '"' ), ',', wppa_filter_iptc( wppa_filter_exif( $tags, $id ), $id ) ) );
		if ( $tags != $oldt ) {					// Added tag(s)
			wppa_update_photo( array( 'id' => $id, 'tags' => $tags ) );
		}

		// Index
		wppa_index_add( 'photo', $id );

		// Tags
		if ( $tags ) {
			wppa_clear_taglist();			// Forces recreation
		}

		// and add watermark ( optionally ) to fullsize image only
		wppa_add_watermark( $id );

		// Also to thumbnail?
		if ( wppa_switch( 'watermark_thumbs' ) ) {
			wppa_create_thumbnail( $id );	// create new thumb
		}

		// Is it a default coverimage?
		wppa_check_coverimage( $id );

		// Mail
		if ( wppa_switch( 'upload_notify' ) ) {
			$to = get_bloginfo( 'admin_email' );
			$subj = sprintf( __( 'New photo uploaded: %s' , 'wp-photo-album-plus'), $name );
			$cont['0'] = sprintf( __( 'User %1$s uploaded photo %2$s into album %3$s' , 'wp-photo-album-plus'), wppa_get_user(), $id, wppa_get_album_name( $alb ) );
			if ( wppa_switch( 'upload_moderate' ) && !current_user_can( 'wppa_admin' ) ) {
				$cont['1'] = __( 'This upload requires moderation' , 'wp-photo-album-plus');
				$cont['2'] = '<a href="'.get_admin_url().'admin.php?page=wppa_admin_menu&tab=pmod&photo='.$id.'" >'.__( 'Moderate manage photo' , 'wp-photo-album-plus').'</a>';
			}
			else {
				$cont['1'] = __( 'Details:' , 'wp-photo-album-plus');
				$cont['1'] .= ' <a href="'.get_admin_url().'admin.php?page=wppa_admin_menu&tab=pmod&photo='.$id.'" >'.__( 'Manage photo' , 'wp-photo-album-plus').'</a>';
			}
			wppa_send_mail( $to, $subj, $cont, $id );
		}
		return true;
	}
	else {
		return false;
	}
}

function wppa_normalize_quotes( $xtext ) {

	$text = html_entity_decode( $xtext );
	$result = '';
	while ( $text ) {
		$char = substr( $text, 0, 1 );
		$text = substr( $text, 1 );
		switch ( $char ) {
			case '`':	// grave
			case '’':	// acute
				$result .= "'";
				break;
			case '“':	// double grave
			case '”':	// double acute
				$result .= '"';
				break;
			case '&':
				if ( substr( $text, 0, 5 ) == '#039;' ) {	// quote
					$result .= "'";
					$text = substr( $text, 5 );
				}
				elseif ( substr( $text, 0, 5 ) == '#034;' ) {	// double quote
					$result .= "'";
					$text = substr( $text, 5 );
				}
				elseif ( substr( $text, 0, 6 ) == '#8216;' || substr( $text, 0, 6 ) == '#8217;' ) {	// grave || acute
					$result .= "'";
					$text = substr( $text, 6 );
				}
				elseif ( substr( $text, 0, 6 ) == '#8220;' || substr( $text, 0, 6 ) == '#8221;' ) {	// double grave || double acute
					$result .= '"';
					$text = substr( $text, 6 );
				}
				break;
			default:
				$result .= $char;
				break;
		}
	}
	return $result;
}

// Find the search results. For use in a page template to show the search results. See ./theme/search.php
function wppa_have_photos( $xwidth = '0' ) {

	if ( !is_search() ) return false;
	$width = $xwidth ? $xwidth : wppa_get_container_width();

	wppa( 'searchresults', wppa_albums( '', '', $width ) );

	return wppa( 'any' );
}

// Display the searchresults. For use in a page template to show the search results. See ./theme/search.php
function wppa_the_photos() {

	if ( wppa( 'any' ) ) echo wppa( 'searchresults' );
}

// Decide if a thumbnail photo file can be used for a requested display
function wppa_use_thumb_file( $id, $width = '0', $height = '0' ) {

	if ( ! wppa_switch( 'use_thumbs_if_fit' ) ) return false;
	if ( $width <= 1.0 && $height <= 1.0 ) return false;	// should give at least one dimension and not when fractional

	$file = wppa_get_thumb_path( $id );
	if ( file_exists( $file ) ) {
		$size = wppa_get_imagexy( $id, 'thumb' );
	}
	else return false;

	if ( ! is_array( $size ) ) return false;
	if ( $width > 0 && $size[0] < $width ) return false;
	if ( $height > 0 && $size[1] < $height ) return false;

	return true;
}

// Compute time to wait for time limited uploads
function wppa_time_to_wait_html( $album, $user = false ) {
global $wpdb;

	if ( ! $album && ! $user ) return '0';

	if ( $user ) {
		$limits = wppa_get_user_upload_limits();
	}
	else {
		$limits = $wpdb->get_var( $wpdb->prepare( "SELECT `upload_limit` FROM `".WPPA_ALBUMS."` WHERE `id` = %s", $album ) );
	}
	wppa_dbg_q( 'Q62' );
	$temp = explode( '/', $limits );
	$limit_max  = isset( $temp[0] ) ? $temp[0] : '0';
	$limit_time = isset( $temp[1] ) ? $temp[1] : '0';

	$result = '';

	if ( ! $limit_max || ! $limit_time ) return $result;

	if ( $user ) {
		$owner = wppa_get_user( 'login' );
		$last_upload_time = $wpdb->get_var( $wpdb->prepare( "SELECT `timestamp` FROM `".WPPA_PHOTOS."` WHERE `owner` = %s ORDER BY `timestamp` DESC LIMIT 1", $owner ) );
	}
	else {
		$last_upload_time = $wpdb->get_var( $wpdb->prepare( "SELECT `timestamp` FROM `".WPPA_PHOTOS."` WHERE `album` = %s ORDER BY `timestamp` DESC LIMIT 1", $album ) );
	}
	wppa_dbg_q( 'Q63' );
	$timnow = time();

	// For simplicity: a year is 364 days = 52 weeks, we skip the months
	$seconds = array( 'min' => '60', 'hour' => '3600', 'day' => '86400', 'week' => '604800', 'month' => '2592000', 'year' => '31449600' );
	$deltatim = $last_upload_time + $limit_time - $timnow;

	$temp    = $deltatim;
	$weeks   = floor( $temp / $seconds['week'] );
	$temp    = $temp % $seconds['week'];
	$days    = floor( $temp / $seconds['day'] );
	$temp    = $temp % $seconds['day'];
	$hours   = floor( $temp / $seconds['hour'] );
	$temp    = $temp % $seconds['hour'];
	$mins    = floor( $temp / $seconds['min'] );
	$secs    = $temp % $seconds['min'];

	$switch = ( $weeks > '0' );

	$string = __( 'You can upload after' , 'wp-photo-album-plus').' ';

	if ( $weeks || $switch ) {
		$string .= sprintf( _n( '%d week', '%d weeks', $weeks, 'wp-photo-album-plus' ), $weeks ).', ';
		$switch = true;
	}
	if ( $days  || $switch ) {
		$string .= sprintf( _n( '%d day', '%d days', $days, 'wp-photo-album-plus'), $days ).', ';
		$switch = true;
	}
	if ( $hours || $switch ) {
		$string .= sprintf( _n( '%d hour', '%d hours', $hours, 'wp-photo-album-plus'), $hours ).', ';
		$switch = true;
	}
	if ( $mins  || $switch ) {
		$string .= sprintf( _n( '%d minute', '%d minutes', $mins, 'wp-photo-album-plus'), $mins ).', ';
		$switch = true;
	}
	if ( $switch ) {
		$string .= sprintf( _n( '%d second', '%d seconds', $secs, 'wp-photo-album-plus'), $secs );
	}
	$string .= '.';
	$result = '<span style="font-size:9px;"> '.$string.'</span>';
	return $result;
}

// Get the title to be used for lightbox links == text under the lightbox image
function wppa_get_lbtitle( $type, $id ) {

	if ( ! is_numeric( $id ) || $id < '1' ) wppa_dbg_msg( 'Invalid arg wppa_get_lbtitle( '.$id.' )', 'red' );

	$thumb = wppa_cache_thumb( $id );

	$do_download 	= wppa_is_video( $id ) ? false : wppa_switch( 'art_monkey_on_lightbox' );
	$do_name 		= wppa_switch( 'ovl_'.$type.'_name' ) || wppa_switch( 'ovl_add_owner' );
	$do_desc 		= wppa_switch( 'ovl_'.$type.'_desc' );
	$do_sm 			= wppa_switch( 'share_on_lightbox' );

	$result = '';
	if ( $do_download ) {
		if ( wppa_opt( 'art_monkey_display' ) == 'button' ) {
			$result .= 	'<input' .
							' type="button"' .
							' title="' . __( 'Download' , 'wp-photo-album-plus') . '"' .
							' style="cursor:pointer; margin-bottom:0px; max-width:500px;"' .
							' class="wppa-download-button"' .
							' onclick="wppaAjaxMakeOrigName( ' . wppa( 'mocc' ) . ', \'' . wppa_encrypt_photo($id) .'\' );"' .
							' value="' . rtrim( __( 'Download' , 'wp-photo-album-plus') . ' ' .
										 wppa_get_photo_name( $id, wppa_switch( 'ovl_add_owner' ), false, false, wppa_switch( 'ovl_'.$type.'_name' ) ) ) .
							'"' .
						' />';
		}
		else {
			$result .= 	'<a' .
							' title="' . __( 'Download' , 'wp-photo-album-plus') . '"' .
							' style="cursor:pointer;"' .
							' onclick="wppaAjaxMakeOrigName( '.wppa( 'mocc' ).', \''.wppa_encrypt_photo($id).'\' );"' .
							' >' .
							rtrim( __( 'Download' , 'wp-photo-album-plus') . ' ' .
							wppa_get_photo_name( $id, wppa_switch( 'ovl_add_owner' ), false, false, wppa_switch( 'ovl_'.$type.'_name' ) ) ) .
						'</a>';
		}
	}
	else {
		if ( $do_name ) $result .= wppa_get_photo_name( $id, wppa_switch( 'ovl_add_owner' ), false, false, wppa_switch( 'ovl_'.$type.'_name' ) );
	}
	if ( $do_name && $do_desc ) $result .= '<br />';
	if ( $do_desc ) $result .= wppa_get_photo_desc( $thumb['id'] );
	if ( ( $do_name || $do_desc ) && $do_sm ) $result .= '<br />';
	if ( $do_sm ) $result .= wppa_get_share_html( $thumb['id'], 'lightbox' );
	if ( ! wppa_is_user_blacklisted() ) {
			if ( 	( wppa_user_is( 'administrator' ) ) ||
					( current_user_can( 'wppa_moderate' ) ) ||
					( wppa_get_user() == wppa_get_photo_owner( $id ) && wppa_switch( 'upload_edit' ) )
				) {
			if ( $type == 'slide' ) {
				$parg = esc_js('\''.wppa_encrypt_photo($id).'\'');
			}
			else {
				$parg = '\''.wppa_encrypt_photo($id).'\'';
			}
		$result .= '
			<div style="float:right; margin-right:6px;" >' .
				'<a' .
					' style="color:green;cursor:pointer;"' .
					' onclick="' . ( $type == 'slide' ? '_wppaStop( '.wppa( 'mocc' ).' );' : '' ) . 'wppaEditPhoto( '.wppa( 'mocc' ).', '.$parg.' ); return false;"' .
					' >' .
					__( 'Edit' , 'wp-photo-album-plus') .
				'</a>' .
			'</div>';
				}
	}

	$result = esc_attr( $result );
	return $result;
}

function wppa_zoom_in( $id ) {

	if ( $id === false ) return '';

	if ( wppa_switch( 'show_zoomin' ) ) {
		if ( wppa_opt( 'magnifier' ) ) {
			return __( 'Zoom in' , 'wp-photo-album-plus');
		}
		else {
			return esc_attr( stripslashes( wppa_get_photo_name( $id ) ) );
		}
	}
	else return '';
}


