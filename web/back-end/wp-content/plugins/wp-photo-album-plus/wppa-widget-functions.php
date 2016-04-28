<?php
/* wppa-widget-functions.php
/* Package: wp-photo-album-plus
/*
/* Version 6.3.18
/*
*/

// This function returns an array of photos that meet the current photo of the day selection criteria
function wppa_get_widgetphotos( $alb, $option = '' ) {
global $wpdb;

	$photos = false;
	$query = '';

	// Compile status clause
	switch( wppa_opt( 'widget_status_filter' ) ) {
		case 'publish':
			$statusclause = " `status` = 'publish' ";
			break;
		case 'featured':
			$statusclause = " `status` = 'featured' ";
			break;
		case 'gold':
			$statusclause = " `status` = 'gold' ";
			break;
		case 'silver':
			$statusclause = " `status` = 'silver' ";
			break;
		case 'bronze':
			$statusclause = " `status` = 'bronze' ";
			break;
		case 'anymedal':
			$statusclause = " `status` IN ( 'gold', 'silver', 'bronze' ) ";
			break;
		default:
			$statusclause = " `status` <> 'scheduled' ";
			if ( ! is_user_logged_in() ) {
				$statusclause .= " AND `status` <> 'private' ";
			}
	}

	// Is it a single album?
	if ( wppa_is_int( $alb ) ) {
		$query = $wpdb->prepare( "SELECT `id`, `p_order` FROM `" . WPPA_PHOTOS . "` WHERE `album` = %s " . " AND " . $statusclause . $option, $alb );
	}

	// Is it an enumeration of album ids?
	elseif ( strchr( $alb, ',' ) ) {
		$alb = trim( $alb, ',' );

		// Test for numeric only ( security test )
		$t = str_replace( ',', '', $alb);
		if ( is_numeric( $t ) ) {
			$query = 	"SELECT `id`, `p_order` FROM `" . WPPA_PHOTOS . "` " .
							"WHERE `album` IN ( " . $alb . " ) " .
							"AND " . $statusclause . $option;
		}
	}

	// Is it ALL?
	elseif ( $alb == 'all' ) {
		$query = "SELECT `id`, `p_order` FROM `" . WPPA_PHOTOS . "` " . " WHERE " . $statusclause . $option;
	}

	// Is it SEP?
	elseif ( $alb == 'sep' ) {
		$albs = $wpdb->get_results( "SELECT `id`, `a_parent` FROM `" . WPPA_ALBUMS . "`", ARRAY_A );
		$query = "SELECT `id`, `p_order` FROM `" . WPPA_PHOTOS . "` WHERE ( `album` = '0' ";
		$first = true;
		foreach ( $albs as $a ) {
			if ( $a['a_parent'] == '-1' ) {
				$query .= "OR `album` = '" . $a['id'] . "' ";
			}
		}
		$query .= ") AND " . $statusclause . $option;
	}

	// Is it ALL-SEP?
	elseif ( $alb == 'all-sep' ) {
		$albs = $wpdb->get_results( "SELECT `id`, `a_parent` FROM `" . WPPA_ALBUMS . "`", ARRAY_A );
		$query = "SELECT `id`, `p_order` FROM `" . WPPA_PHOTOS . "` WHERE ( `album` IN ('0'";
		foreach ( $albs as $a ) {
			if ( $a['a_parent'] != '-1' ) {
				$query .= ",'" . $a['id'] . "'";
			}
		}
		$query .= ") ) AND " . $statusclause . $option;
	}

	// Is it Topten?
	elseif ( $alb == 'topten' ) {

		// Find the 'top' policy
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

		// It is assumed that status is ok for top rated photos
		$query = "SELECT `id`, `p_order` FROM `" . WPPA_PHOTOS . "` ORDER BY " . $sortby . " LIMIT " . wppa_opt( 'topten_count' );
		$query .= $option;
	}

	// Do the query
	if ( $query ) {
		$photos = $wpdb->get_results( $query, ARRAY_A );
		wppa_dbg_q( 'Q-Potd' );
		wppa_dbg_msg( 'Potd query: '.$query );
	}
	else {
		$photos = array();
	}

	// Ready
	return $photos;
}

// get select form element listing albums
// Special version for widget
function wppa_walbum_select( $sel = '' ) {
	global $wpdb;
	$albums = $wpdb->get_results( "SELECT * FROM `" . WPPA_ALBUMS . "` ORDER BY `name`", ARRAY_A );
	wppa_dbg_q( 'Q-Asel' );
	wppa_cache_album( 'add', $albums );

	if ( is_numeric( $sel ) ) $type = 1;		// Single number
	elseif ( strchr( $sel, ',' ) ) {
		$type = 2;							// Array
		$albs =  explode( ',', $sel );
	}
	elseif ( $sel == 'all' ) $type = 3;		// All
	elseif ( $sel == 'sep' ) $type = 4;		// Separate only
	elseif ( $sel == 'all-sep' ) $type = 5;	// All minus separate
	elseif ( $sel == 'topten' ) $type = 6;	// Topten

	else $type = 0;							// Nothing yet

    $result = '<option value="" >' . __( '- select (another) album or a set -' , 'wp-photo-album-plus' ) . '</option>';

	foreach ( $albums as $album ) {
		switch ( $type ) {
			case 1:
				$dis = ( $album['id'] == $sel );
				break;
			case 2:
				$dis = in_array( $album['id'], $albs );
				break;
			case 3:
				$dis = true;
				break;
			case 4:
				$dis = ( $album['a_parent'] == '-1' );
				break;
			case 5:
				$dis = ( $album['a_parent'] != '-1' );
				break;
			case 6:
				$dis = false;
				break;

			default:
				$dis = false;
		}
		if ( $dis ) $dis = 'disabled="disabled"';
		else $dis = '';
		$result .= '<option ' . $dis . ' value="' . $album['id'] . '">( ' . $album['id'] . ' )';
			if ( $album['id'] < '1000' ) $result .= '&nbsp;';
			if ( $album['id'] < '100' ) $result .= '&nbsp;';
			if ( $album['id'] < '10' ) $result .= '&nbsp;';
			$result .= __( stripslashes( $album['name'] ) ) . '</option>';
	}
	$sel = $type == 3 ? 'selected="selected"' : '';
	$result .= '<option value="all" ' . $sel . ' >' . __( '- all albums -' , 'wp-photo-album-plus' ) . '</option>';
	$sel = $type == 4 ? 'selected="selected"' : '';
	$result .= '<option value="sep" ' . $sel . ' >' . __( '- all -separate- albums -' , 'wp-photo-album-plus' ) . '</option>';
	$sel = $type == 5 ? 'selected="selected"' : '';
	$result .= '<option value="all-sep" ' . $sel . ' >' . __( '- all albums except -separate-' , 'wp-photo-album-plus' ) . '</option>';
	$sel = $type == 6 ? 'selected="selected"' : '';
	$result .= '<option value="topten" ' . $sel . ' >' . __( '- top rated photos -' , 'wp-photo-album-plus' ) . '</option>';
	$result .= '<option value="clr" >' . __( '- start over -' , 'wp-photo-album-plus' ) . '</option>';
	return $result;
}

function wppa_walbum_sanitize( $walbum ) {

	$result = strtolower( $walbum );
	$result = strip_tags( $result );

	if ( strstr( $result, 'all-sep' ) ) $result = 'all-sep';
	elseif ( strstr( $result, 'all' ) ) $result = 'all';
	elseif ( strstr( $result, 'sep' ) ) $result = 'sep';
	elseif ( strstr( $result, 'topten' ) ) $result = 'topten';
	elseif ( strstr( $result, 'clr' ) ) $result = '';
	else {

		// Change multiple commas to one
		while ( substr_count( $result, ',,' ) ) $result = str_replace( ',,', ',', $result );

		// remove leading and trailing commas
		$result = trim( $result, ',' );

		// Check for illegal chars
		$temp = str_replace( ',', '', $result );
		if ( $temp && ! wppa_is_int( $temp ) ) {
			// $result contains other chars than numbers and comma's
			$result = 'clr';
		}
	}
	return $result;
}

// get the photo of the day
function wppa_get_potd() {
global $wpdb;

	$image = '';
	switch ( wppa_opt( 'widget_method' ) ) {
		case '1':	// Fixed photo
			$id = wppa_opt( 'widget_photo' );
			if ( $id != '' ) {
				$image = $wpdb->get_row( $wpdb->prepare(
					"SELECT * FROM `" . WPPA_PHOTOS . "` WHERE `id` = %s LIMIT 0,1", $id
					), ARRAY_A );
				wppa_dbg_q( 'Q-Potd' );
				wppa_cache_photo( 'add', $image );
			}
			break;
		case '2':	// Random
			$album = wppa_opt( 'widget_album' );
			if ( $album == 'topten' ) {
				$images = wppa_get_widgetphotos( $album );
				if ( count( $images ) > 1 ) {	// Select a random first from the current selection
					$idx = rand( 0, count( $images ) - 1 );
					$image = $images[$idx];
				}
			}
			elseif ( $album != '' ) {
				$images = wppa_get_widgetphotos( $album, "ORDER BY RAND() LIMIT 0,1" );
				$image = $images[0];
			}
			break;
		case '3':	// Last upload
			$album = wppa_opt( 'widget_album' );
			if ( $album == 'topten' ) {
				$images = wppa_get_widgetphotos( $album );
				if ( $images ) {
					// fid last uploaded image in the $images pool
					$temp = 0;
					foreach( $images as $img ) {
						if ( $img['timestamp'] > $temp ) {
							$temp = $img['timestamp'];
							$image = $img;
						}
					}
				}
			}
			elseif ( $album != '' ) {
				$images = wppa_get_widgetphotos( $album, "ORDER BY timestamp DESC LIMIT 0,1" );
				$image = $images[0];
			}
			break;
		case '4':	// Change every
			$album = wppa_opt( 'widget_album' );
			if ( $album != '' ) {
				$per = wppa_opt( 'widget_period' );
				$photos = wppa_get_widgetphotos( $album );
				if ( $per == '0' ) {
					if ( $photos ) {
						$image = $photos[rand( 0, count( $photos )-1 )];
					}
					else $image = '';
				}
				elseif ( $per == 'day-of-week' ) {
					$image = '';
					if ( $photos ) {
						$d = date_i18n( "w" );
						$d -= get_option( 'wppa_potd_offset', '0' );
						while ( $d < '1' ) $d += '7';
						foreach ( $photos as $img ) {
							if ( $img['p_order'] == $d ) $image = $img;
						}
					}
				}
				elseif ( $per == 'day-of-month' ) {
					$image = '';
					if ( $photos ) {
						$d = strval(intval(date_i18n( "d" )));
						$d -= get_option( 'wppa_potd_offset', '0' );
						while ( $d < '1' ) $d += '31';
						foreach ( $photos as $img ) {
							if ( $img['p_order'] == $d ) $image = $img;
						}
					}
				}
				elseif ( $per == 'day-of-year' ) {
					$image = '';
					if ( $photos ) {
						$d = strval(intval(date_i18n( "z" )));
						$d -= get_option( 'wppa_potd_offset', '0' );
						while ( $d < '0' ) $d += '366';
						foreach ( $photos as $img ) {
							if ( $img['p_order'] == $d ) $image = $img;
						}
					}
				}
				else {
					$u = date_i18n( "U" ); // Seconds since 1-1-1970, local
					$u /= 3600;		//  hours since
					$u = floor( $u );
					$u /= $per;
					$u = floor( $u );

					// Find the right photo out of the photos found by wppa_get_widgetphotos(),
					// based on the Change every { any timeperiod } algorithm.
					if ( $photos ) {
						$p = count( $photos );
						$idn = fmod( $u, $p );

						// If from topten,...
						if ( $album == 'topten' ) {

							// Do a re-read of the same to order by rand, reproduceable
							// This can not be done by wppa_get_widgetphotos(),
							// it does already ORDER BY for the top selection criterium.
							// So we save the ids, and do a SELECT WHERE id IN ( array of found ids ) ORDER BY RAND( seed )
							$ids = array();
							foreach( $photos as $photo ) {
								$ids[] = $photo['id'];
							}
							$photos = $wpdb->get_results( 	"SELECT `id`, `p_order` " .
															"FROM `".WPPA_PHOTOS."` " .
															"WHERE `id` IN (" . implode( ',', $ids ) . ") " .
															"ORDER BY RAND(".$idn.")",
															ARRAY_A );
						}

						// Not from topten, use wppa_get_widgetphotos() to get a reproduceable random sequence
						else {
							$photos = wppa_get_widgetphotos( $album, " ORDER BY RAND(".$idn.")" );
						}

						// Image found
						$image = $photos[$idn];
					}
					else {
						$image = '';
					}
				}
			} else {
				$image = '';
			}
			break;

		default:
			$image = '';
	}

	if ( $image ) {
		$image = wppa_cache_photo( $image['id'] );
	}
	return $image;
}

