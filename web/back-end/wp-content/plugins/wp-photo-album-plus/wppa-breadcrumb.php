<?php
/* wppa-breadcrumb.php
* Package: wp-photo-album-plus
*
* Functions for breadcrumbs
* Version 6.4.07
*
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

// shows the breadcrumb navigation
function wppa_breadcrumb( $opt = '' ) {
global $wpdb;
global $wppa_session;

	// See if they need us
	// Check Table II-A1 a and b
	if ( $opt == 'optional' ) {
		$pid = wppa_get_the_page_id();
		$type = $wpdb->get_var( $wpdb->prepare(
			"SELECT `post_type` FROM `" . $wpdb->posts . "` WHERE `ID` = %s", $pid
			 ) );
		wppa_dbg_q( 'Q-bc1') ;
		if ( $type == 'post' && ! wppa_switch( 'show_bread_posts' ) ) {
			return;	// Nothing to do here
		}
		if ( $type != 'post' && ! wppa_switch( 'show_bread_pages' ) ) {
			return;	// Nothing to do here
		}
	}

	// Check special cases
	if ( wppa( 'is_single' ) ) return;			// A single image slideshow needs no navigation
	if ( wppa_page( 'oneofone' ) ) return; 		// Never at a single image page
	if ( wppa( 'is_slideonly' ) ) return;		// Not when slideonly
	if ( wppa_in_widget() ) return; 			// Not in a widget
	if ( is_feed() ) return;					// Not in a feed

	$thumbhref = '';

	// Any special selection has its own switch
	if ( wppa( 'is_topten' ) && ! wppa_switch( 'bc_on_topten' ) ) return;
	if ( wppa( 'is_lasten' ) && ! wppa_switch( 'bc_on_lasten' ) ) return;
	if ( wppa( 'is_comten' ) && ! wppa_switch( 'bc_on_comten' ) ) return;
	if ( wppa( 'is_featen' ) && ! wppa_switch( 'bc_on_featen' ) ) return;
	if ( wppa( 'is_related' ) && ! wppa_switch( 'bc_on_related' ) ) return;
	if ( wppa( 'is_tag' ) && ! wppa_switch( 'bc_on_tag' ) ) return;
	if ( wppa( 'src' ) && ! wppa_switch( 'bc_on_search' ) ) return;

	// Get the album number
	$alb = wppa_is_int( wppa( 'start_album' ) ) ?
		wppa( 'start_album' ) :
		'0';	// A single album or all ( all = 0 here )

	$is_albenum = strlen( wppa( 'start_album' ) ) > '0' && ! wppa_is_int( wppa( 'start_album' ) );

	wppa_dbg_msg( 'alb=' . $alb . ', albenum=' . $is_albenum, 'green' );

	$virtual = wppa_is_virtual() || wppa( 'last_albums' );

	if ( wppa( 'last_albums' ) ) {
		$alb = wppa( 'last_albums_parent' );
	}

	wppa_dbg_msg(
		'alb='.$alb.', albenum='.$is_albenum.', l_a='.wppa( 'last_albums' ).
		', l_a_p='.wppa( 'last_albums_parent' ), 'green' );

	// See if the album is a 'stand alone' album
	$separate = wppa_is_separate( $alb );

	// See if the album links to slides in stead of thumbnails
	$slide = ( wppa_get_album_title_linktype( $alb ) == 'slide' ) ? '&amp;wppa-slide' : '';

	// See if we link to covers or to contents
	$to_cover = wppa_opt( 'thumbtype' ) == 'none' ? '1' : '0';

	// Photo number?
	$photo = wppa( 'start_photo' );

	wppa_dbg_msg(
		'pid='.$pid.', type='.$type.', alb='.$alb.', sep='.$separate.
		', slide='.$slide.', t_c=0, ph='.$photo, 'green' );

	// Open the breadcrumb box
	wppa_out( 	'<div' .
					' id="wppa-bc-'.wppa( 'mocc' ) . '"' .
					' class="wppa-nav wppa-box wppa-nav-text" ' .
					'style="' .
						__wcs( 'wppa-nav' ) .
						__wcs( 'wppa-box' ) .
						__wcs( 'wppa-nav-text' ) .
					'" >' );

		// Do we need Home?
		if ( wppa_switch( 'show_home' ) ) {
			$value 	= __( wppa_opt( 'home_text' ) );
			$href 	= wppa_dbg_url( get_bloginfo( 'url' ) );
			$title 	= get_bloginfo( 'title' );
			wppa_bcitem( $value, $href, $title, 'b1' );
		}

		// Page ( grand )parents ?
		if ( $type == 'page' && wppa_switch( 'show_page' ) ) {
			wppa_crumb_page_ancestors( $pid );
		}

		// Do the post/page
		if ( wppa_switch( 'show_page' ) ) {
			$value 	= __( stripslashes( $wpdb->get_var( $wpdb->prepare(
				"SELECT `post_title` FROM `".$wpdb->posts.
				"` WHERE `post_status` = 'publish' AND `ID` = %s LIMIT 0,1", $pid
				) ) ) );
			wppa_dbg_q( 'Q-bc2' );
			$href	= ( $alb || $virtual || $is_albenum ) ?
				wppa_get_permalink( $pid, true ) :
				'';
			$title	= $type == 'post' ? __( 'Post:' , 'wp-photo-album-plus').' '.$value : __( 'Page:' , 'wp-photo-album-plus').' '.$value;
			wppa_bcitem( $value, $href, $title, 'b3' );
		}

		// The album ( grand ) parents if not separate
		wppa_crumb_ancestors( $alb, $to_cover );

		// The album and optional placeholder for photo

		// Supersearch ?
		if ( wppa( 'supersearch' ) ) {
			$value  = ' ';
			$ss_data = explode( ',', wppa( 'supersearch' ) );
			switch ( $ss_data['0'] ) {
				case 'a':
					$value .= ' ' . __('Albums', 'wp-photo-album-plus');
					switch ( $ss_data['1'] ) {
						case 'c':
							$value .= ' ' . __('with category:', 'wp-photo-album-plus');
							break;
						case 'n':
							$value .= ' ' . __('with name:', 'wp-photo-album-plus');
							break;
						case 't':
							$value .= ' ' . __('with words:', 'wp-photo-album-plus');
							break;
					}
					$value .= ' <b>' . str_replace( '.', '</b> ' . __('and', 'wp-photo-album-plus') . ' <b>', $ss_data['3'] ) . '</b>';
					break;
				case 'p':
					$value .= ' ' . __('Photos', 'wp-photo-album-plus');
					switch ( $ss_data['1'] ) {
						case 'g':
							$value .= ' ' . __('with tag:', 'wp-photo-album-plus') . ' <b>' . str_replace( '.', '</b> ' . __('and', 'wp-photo-album-plus') . ' <b>', $ss_data['3'] ) . '</b>';
							break;
						case 'n':
							$value .= ' ' . __('with name:', 'wp-photo-album-plus') . ' <b>' . $ss_data['3'] . '</b>';
							break;
						case 't':
							$ss_data['3'] = str_replace( '...', '***', $ss_data['3'] );
							$value .= ' ' . __('with words:', 'wp-photo-album-plus') . ' <b>' . str_replace( '.', '</b> ' . __('and', 'wp-photo-album-plus') . ' <b>', $ss_data['3'] ) . '</b>';
							$value = str_replace( '***', '...', $value );
							break;
						case 'o':
							$value .= ' ' . __('of owner:', 'wp-photo-album-plus') . ' <b>' . $ss_data['3'] . '</b>';
							break;
						case 'i':
							$label = $wpdb->get_var( $wpdb->prepare( "SELECT `description` FROM `" . WPPA_IPTC . "` WHERE `tag` = %s AND `photo` = '0'", str_replace( 'H', '#', $ss_data['2'] ) ) );
							$label = trim( $label, ':' );
							$value .= ' ' . __('with iptc tag:', 'wp-photo-album-plus') . ' <b>' . __($label, 'wp-photo-album-plus') . '</b> ' . __('with content:', 'wp-photo-album-plus') .' <b>' . $ss_data['3'] . '</b>';
							break;
						case 'e':
							$label = $wpdb->get_var( $wpdb->prepare( "SELECT `description` FROM `" . WPPA_EXIF . "` WHERE `tag` = %s AND `photo` = '0'", str_replace( 'H', '#', $ss_data['2'] ) ) );
							$label = trim( $label, ':' );
							$value .= ' ' . __('with exif tag:', 'wp-photo-album-plus') . ' <b>' . __($label, 'wp-photo-album-plus') . '</b> ' . __('with content:', 'wp-photo-album-plus') .' <b>' . $ss_data['3'] . '</b>';
							break;
					}
					break;
			}

			if ( wppa( 'is_slide' ) ) {
				$thumbhref 	= wppa_get_permalink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-supersearch='.stripslashes( wppa( 'supersearch' ) );
				$thumbajax  = wppa_get_ajaxlink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-supersearch='.stripslashes( wppa( 'supersearch' ) );
				$title  = __( 'View the thumbnails' , 'wp-photo-album-plus');
				wppa_bcitem( $value, $thumbhref, $title, 'b8', $thumbajax );
			}

			$href 	= '';
			$title	= '';
			wppa_bcitem( $value, $href, $title, 'b9' );
		}

		// Search ?
		elseif ( wppa( 'src' ) && ! wppa( 'is_related' ) ) {

			$searchroot = $wppa_session['search_root'];
			if ( ! $searchroot ) {
				$searchroot = '-2'; // To get 'All albums'
			}
			$albtxt = wppa( 'is_rootsearch' ) ?
				' <span style="cursor:pointer;" title="'.
					esc_attr( sprintf( __( 'Searchresults from album %s and its subalbums' , 'wp-photo-album-plus'),
					wppa_display_root( $searchroot ) ) ).'">*</span> ' :
				'';
			if ( wppa( 'is_slide' ) ) {
				$value  = __( 'Searchstring:' , 'wp-photo-album-plus') . ' ' . ( isset ( $wppa_session['display_searchstring'] ) ? $wppa_session['display_searchstring'] : stripslashes( wppa( 'searchstring' ) ) ) . $albtxt;
				$thumbhref 	= wppa_get_permalink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-searchstring='.stripslashes( str_replace( ' ', '+', $wppa_session['use_searchstring'] ) );
				$thumbajax 	= wppa_get_ajaxlink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-searchstring='.stripslashes( str_replace( ' ', '+', $wppa_session['use_searchstring'] ) );
				$title  = __( 'View the thumbnails' , 'wp-photo-album-plus');
				wppa_bcitem( $value, $thumbhref, $title, 'b8', $thumbajax );
			}
			$value  = __( 'Searchstring:' , 'wp-photo-album-plus') . ' ' . ( isset ( $wppa_session['display_searchstring'] ) ? $wppa_session['display_searchstring'] : stripslashes( wppa( 'searchstring' ) ) ) . $albtxt;
			$href 	= '';
			$title	= isset ( $wppa_session['display_searchstring'] ) ? wppa_dss_to_title( $wppa_session['display_searchstring'] ) : '';
			wppa_bcitem( $value, $href, $title, 'b9' );
		}
		elseif ( wppa( 'calendar' ) ) {
			if ( wppa( 'is_slide' ) ) {
				switch( wppa( 'calendar' ) ) {
					case 'exifdtm':
						$value 	= __( 'Photos by EXIF date' , 'wp-photo-album-plus') . ': ' . wppa( 'caldate' );
						break;

					case 'timestamp':
						$value 	= __( 'Photos by date of upload' , 'wp-photo-album-plus') . ': ' . date( 'M d D Y', wppa( 'caldate' ) * 24*60*60 );
						break;

					case 'modified':
						$value 	= __( 'Photos by date last modified' , 'wp-photo-album-plus') . ': ' . date( 'M d D Y', wppa( 'caldate' ) * 24*60*60 );
						break;

				}
				$thumbhref = '#';
				$title = 'T8';
				wppa_bcitem( $value, $thumbhref, $title, 'b8' );
			}
			switch( wppa( 'calendar' ) ) {
				case 'exifdtm':
					$value 	= __( 'Photos by EXIF date' , 'wp-photo-album-plus') . ': ' . wppa( 'caldate' );
					break;

				case 'timestamp':
					$value 	= __( 'Photos by date of upload' , 'wp-photo-album-plus') . ': ' . date( 'M d D Y', wppa( 'caldate' ) * 24*60*60 );
					break;

				case 'modified':
					$value 	= __( 'Photos by date last modified' , 'wp-photo-album-plus') . ': ' . date( 'M d D Y', wppa( 'caldate' ) * 24*60*60 );
					break;

			}
			$href 	= '';
			$title 	= '';
			wppa_bcitem( $value, $href, $title, 'b9' );
		}
		elseif ( wppa( 'is_upldr' ) ) {
			$usr = get_user_by( 'login', wppa( 'is_upldr' ) );
			if ( $usr ) $user = $usr->display_name; else $user = wppa( 'is_upldr' );
			if ( wppa( 'is_slide' ) ) {
				$value 	= sprintf( __( 'Photos by %s' , 'wp-photo-album-plus'), $user );
				if ( wppa( 'start_album' ) ) {
					$thumbhref = wppa_get_permalink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-upldr='.wppa( 'is_upldr' ).'&amp;wppa-album='.wppa( 'start_album' );
					$thumbajax = wppa_get_ajaxlink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-upldr='.wppa( 'is_upldr' ).'&amp;wppa-album='.wppa( 'start_album' );
				}
				else {
					$thumbhref = wppa_get_permalink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-upldr='.wppa( 'is_upldr' );
					$thumbajax = wppa_get_ajaxlink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-upldr='.wppa( 'is_upldr' );
				}
				$title	= __( 'View the thumbnails' , 'wp-photo-album-plus');
				wppa_bcitem( $value, $thumbhref, $title, 'b8', $thumbajax );
			}
			$value 	= sprintf( __( 'Photos by %s' , 'wp-photo-album-plus'), $user );
			$href 	= '';
			$title	= '';
			wppa_bcitem( $value, $href, $title, 'b9' );
		}
		elseif ( wppa( 'is_topten' ) ) {							// TopTen
			if ( wppa( 'start_album' ) ) {
				$value 	= $is_albenum ? __( 'Various albums' , 'wp-photo-album-plus') : wppa_get_album_name( $alb );
				$href 	= wppa_get_permalink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-album='.wppa( 'start_album' );
				$ajax 	= wppa_get_ajaxlink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-album='.wppa( 'start_album' );
				$title	= $is_albenum ? __( 'Albums:' , 'wp-photo-album-plus').' '.wppa( 'start_album' ) : __( 'Album:' , 'wp-photo-album-plus').' '.$value;
				wppa_bcitem( $value, $href, $title, 'b7', $ajax );
			}
			if ( wppa( 'is_slide' ) ) {
				$value 	= __( 'Top rated photos' , 'wp-photo-album-plus');
				$thumbhref 	= wppa_get_permalink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-topten='.wppa( 'topten_count' ).'&amp;wppa-album='.wppa( 'start_album' );
				$thumbajax 	= wppa_get_ajaxlink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-topten='.wppa( 'topten_count' ).'&amp;wppa-album='.wppa( 'start_album' );
				if ( wppa( 'medals_only' ) ) {
					$thumbhref .= '&amp;wppa-medals-only=1';
					$thumbajax .= '&amp;wppa-medals-only=1';
				}
				$title	= __( 'View the thumbnails' , 'wp-photo-album-plus');
				wppa_bcitem( $value, $thumbhref, $title, 'b8', $thumbajax );
			}
			$value 	= __( 'Top rated photos' , 'wp-photo-album-plus');
			$href 	= '';
			$title	= '';
			wppa_bcitem( $value, $href, $title, 'b9' );
		}
		elseif ( wppa( 'is_lasten' ) ) {							// Lasten
			if ( wppa( 'start_album' ) ) {
				$value 	= $is_albenum ? __( 'Various albums' , 'wp-photo-album-plus') : wppa_get_album_name( $alb );
				$href 	= wppa_get_permalink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-album='.wppa( 'start_album' );
				$ajax 	= wppa_get_ajaxlink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-album='.wppa( 'start_album' );
				$title	= $is_albenum ? __( 'Albums:' , 'wp-photo-album-plus').' '.wppa( 'start_album' ) : __( 'Album:' , 'wp-photo-album-plus').' '.$value;
				wppa_bcitem( $value, $href, $title, 'b7', $ajax );
			}
			if ( wppa( 'is_slide' ) ) {
				if ( wppa_switch( 'lasten_use_modified' ) ) {
					$value 	= __( 'Recently modified photos' , 'wp-photo-album-plus');
				}
				else {
					$value 	= __( 'Recently uploaded photos' , 'wp-photo-album-plus');
				}
				$thumbhref 	= wppa_get_permalink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-lasten='.wppa( 'lasten_count' ).'&amp;wppa-album='.wppa( 'start_album' );
				$thumbajax 	= wppa_get_ajaxlink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-lasten='.wppa( 'lasten_count' ).'&amp;wppa-album='.wppa( 'start_album' );
				$title	= __( 'View the thumbnails' , 'wp-photo-album-plus');
				wppa_bcitem( $value, $thumbhref, $title, 'b8', $thumbajax );
			}
			if ( wppa_switch( 'lasten_use_modified' ) ) {
				$value 	= __( 'Recently modified photos' , 'wp-photo-album-plus');
			}
			else {
				$value 	= __( 'Recently uploaded photos' , 'wp-photo-album-plus');
			}
			$href 	= '';
			$title	= '';
			wppa_bcitem( $value, $href, $title, 'b9' );
		}
		elseif ( wppa( 'is_comten' ) ) {							// Comten
			if ( wppa( 'start_album' ) ) {
				$value 	= $is_albenum ? __( 'Various albums' , 'wp-photo-album-plus') : wppa_get_album_name( $alb );
				$href 	= wppa_get_permalink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-album='.wppa( 'start_album' );
				$ajax 	= wppa_get_ajaxlink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-album='.wppa( 'start_album' );
				$title	= $is_albenum ? __( 'Albums:' , 'wp-photo-album-plus').' '.wppa( 'start_album' ) : __( 'Album:' , 'wp-photo-album-plus').' '.$value;
				wppa_bcitem( $value, $href, $title, 'b7', $ajax );
			}
			if ( wppa( 'is_slide' ) ) {
				$value 	= __( 'Recently commented photos' , 'wp-photo-album-plus');
				$thumbhref 	= wppa_get_permalink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-comten='.wppa( 'comten_count' ).'&amp;wppa-album='.wppa( 'start_album' );
				$thumbajax 	= wppa_get_ajaxlink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-comten='.wppa( 'comten_count' ).'&amp;wppa-album='.wppa( 'start_album' );
				$title	= __( 'View the thumbnails' , 'wp-photo-album-plus');
				wppa_bcitem( $value, $thumbhref, $title, 'b8', $thumbajax );
			}
			$value 	= __( 'Recently commented photos' , 'wp-photo-album-plus');
			$href 	= '';
			$title	= '';
			wppa_bcitem( $value, $href, $title, 'b9' );
		}
		elseif ( wppa( 'is_featen' ) ) {							// Featen
			if ( wppa( 'start_album' ) ) {
				$value 	= $is_albenum ? __( 'Various albums' , 'wp-photo-album-plus') : wppa_get_album_name( $alb );
				$href 	= wppa_get_permalink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-album='.wppa( 'start_album' );
				$ajax 	= wppa_get_ajaxlink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-album='.wppa( 'start_album' );
				$title	= $is_albenum ? __( 'Albums:' , 'wp-photo-album-plus').' '.wppa( 'start_album' ) : __( 'Album:' , 'wp-photo-album-plus').' '.$value;
				wppa_bcitem( $value, $href, $title, 'b7', $ajax );
			}
			if ( wppa( 'is_slide' ) ) {
				$value 	= __( 'Featured photos' , 'wp-photo-album-plus');
				$thumbhref 	= wppa_get_permalink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-featen='.wppa( 'featen_count' ).'&amp;wppa-album='.wppa( 'start_album' );
				$thumbajax 	= wppa_get_ajaxlink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-featen='.wppa( 'featen_count' ).'&amp;wppa-album='.wppa( 'start_album' );
				$title	= __( 'View the thumbnails' , 'wp-photo-album-plus');
				wppa_bcitem( $value, $thumbhref, $title, 'b8', $thumbajax );
			}
			$value 	= __( 'Featured photos' , 'wp-photo-album-plus');
			$href 	= '';
			$title	= '';
			wppa_bcitem( $value, $href, $title, 'b9' );
		}
		elseif ( wppa( 'is_related' ) ) {						// Related photos
			if ( wppa( 'is_slide' ) ) {
				$value 	= __( 'Related photos' , 'wp-photo-album-plus');
				$href 	= wppa_get_permalink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-tag='.wppa( 'is_tag' ).'&amp;wppa-album='.wppa( 'start_album' );
				$ajax 	= wppa_get_ajaxlink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-tag='.wppa( 'is_tag' ).'&amp;wppa-album='.wppa( 'start_album' );
				$title	= __( 'View the thumbnails' , 'wp-photo-album-plus');
				wppa_bcitem( $value, $href, $title, 'b8', $ajax );
			}
			$value 	= __( 'Related photos' , 'wp-photo-album-plus');
			$href 	= '';
			$title	= '';
			wppa_bcitem( $value, $href, $title, 'b9' );
		}
		elseif ( wppa( 'is_tag' ) && wppa( 'is_cat' ) ) {
			if ( wppa( 'is_slide' ) ) {
				$value 	= __( 'Tagged photos:' , 'wp-photo-album-plus').'&nbsp;'.str_replace( ';', ' '.__( 'or' , 'wp-photo-album-plus').' ', str_replace( ',', ' '.__( 'and' , 'wp-photo-album-plus').' ', trim( wppa( 'is_tag' ), ',;' ) ) );
				$value .= '&nbsp;' . __( 'From albums with', 'wp-photo-album-plus' ) . '&nbsp;';
				$value .= __( 'Category:' , 'wp-photo-album-plus').'&nbsp;'.str_replace( ';', ' '.__( 'or', 'wp-photo-album-plus' ).' ', str_replace( ',', ' '.__( 'and', 'wp-photo-album-plus' ).' ', trim( wppa( 'is_cat' ), ',;' ) ) );
				$thumbhref 	= wppa_get_permalink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-cat='.wppa( 'is_cat' ).'&amp;wppa-album='.wppa( 'start_album' );
				$thumbajax 	= wppa_get_ajaxlink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-cat='.wppa( 'is_cat' ).'&amp;wppa-album='.wppa( 'start_album' );
				$title	= __( 'View the thumbnails' , 'wp-photo-album-plus');
				wppa_bcitem( $value, $thumbhref, $title, 'b8', $thumbajax );
			}
			$value 	= __( 'Tagged photos:' , 'wp-photo-album-plus').'&nbsp;'.str_replace( ';', ' '.__( 'or' , 'wp-photo-album-plus').' ', str_replace( ',', ' '.__( 'and' , 'wp-photo-album-plus').' ', trim( wppa( 'is_tag' ), ',;' ) ) );
			$value .= '&nbsp;' . __( 'From albums with', 'wp-photo-album-plus' ) . '&nbsp;';
			$value .= __( 'Category:' , 'wp-photo-album-plus').'&nbsp;'.str_replace( ';', ' '.__( 'or', 'wp-photo-album-plus' ).' ', str_replace( ',', ' '.__( 'and', 'wp-photo-album-plus' ).' ', trim( wppa( 'is_cat' ), ',;' ) ) );
			$href 	= '';
			$title	= '';
			wppa_bcitem( $value, $href, $title, 'b9' );

		}
		elseif ( wppa( 'is_tag' ) ) {							// Tagged photos
			if ( wppa( 'is_slide' ) ) {
				$value 	= __( 'Tagged photos:' , 'wp-photo-album-plus').'&nbsp;'.str_replace( ';', ' '.__( 'or' , 'wp-photo-album-plus').' ', str_replace( ',', ' '.__( 'and' , 'wp-photo-album-plus').' ', trim( wppa( 'is_tag' ), ',;' ) ) );
				if ( wppa( 'start_album' ) ) {
					$value .= ' ' . __( 'out of various albums' , 'wp-photo-album-plus');
				}
				if ( wppa_get_get( 'inv' ) ) {
					$value .= ' (' . __( 'Inverted', 'wp-photo-album-plus' ) . ')';
				}
				$thumbhref 	= wppa_get_permalink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-tag='.wppa( 'is_tag' ).'&amp;wppa-album='.wppa( 'start_album' );
				$thumbajax 	= wppa_get_ajaxlink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-tag='.wppa( 'is_tag' ).'&amp;wppa-album='.wppa( 'start_album' );
				if ( wppa( 'is_inverse' ) ) {
					$thumbhref .= '&amp;wppa-inv=1';
					$thumbajax .= '&amp;wppa-inv=1';
				}
				$title	= __( 'View the thumbnails' , 'wp-photo-album-plus');
				wppa_bcitem( $value, $thumbhref, $title, 'b8', $thumbajax );
			}
			$value 	= __( 'Tagged photos:' , 'wp-photo-album-plus').'&nbsp;'.str_replace( ';', ' '.__( 'or' , 'wp-photo-album-plus').' ', str_replace( ',', ' '.__( 'and' , 'wp-photo-album-plus').' ', trim( wppa( 'is_tag' ), ',;' ) ) );
			if ( wppa( 'start_album' ) ) {
				$value .= ' ' . __( 'out of various albums' , 'wp-photo-album-plus');
			}
			if ( wppa_get_get( 'inv' ) ) {
				$value .= ' (' . __( 'Inverted', 'wp-photo-album-plus' ) . ')';
			}
			$href 	= '';
			$title	= '';
			wppa_bcitem( $value, $href, $title, 'b9' );
		}
		elseif ( wppa( 'is_cat' ) ) {							// Categorized albums
			if ( wppa( 'is_slide' ) ) {
				$value 	= __( 'Category:' , 'wp-photo-album-plus').'&nbsp;'.str_replace( ';', ' '.__( 'or', 'wp-photo-album-plus' ).' ', str_replace( ',', ' '.__( 'and', 'wp-photo-album-plus' ).' ', trim( wppa( 'is_cat' ), ',;' ) ) );
				$thumbhref 	= wppa_get_permalink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-cat='.wppa( 'is_cat' ).'&amp;wppa-album='.wppa( 'start_album' );
				$thumbajax 	= wppa_get_ajaxlink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-cat='.wppa( 'is_cat' ).'&amp;wppa-album='.wppa( 'start_album' );
				$title	= __( 'View the thumbnails' , 'wp-photo-album-plus');
				wppa_bcitem( $value, $thumbhref, $title, 'b8', $thumbajax );
			}
			$value 	= __( 'Category:' , 'wp-photo-album-plus').'&nbsp;'.str_replace( ';', ' '.__( 'or', 'wp-photo-album-plus' ).' ', str_replace( ',', ' '.__( 'and', 'wp-photo-album-plus' ).' ', trim( wppa( 'is_cat' ), ',;' ) ) );
			$href 	= '';
			$title	= '';
			wppa_bcitem( $value, $href, $title, 'b9' );
		}

		elseif ( wppa( 'last_albums' ) ) {							// Recently modified albums( s )
			if ( wppa( 'last_albums_parent' ) ) {
				$value 	= wppa_get_album_name( $alb );
				$href 	= wppa_get_permalink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-album='.wppa( 'start_album' );
				$ajax 	= wppa_get_ajaxlink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-album='.wppa( 'start_album' );
				$title	= __( 'Album:' , 'wp-photo-album-plus').' '.$value;
				wppa_bcitem( $value, $href, $title, 'b7', $ajax );
			}
			if ( wppa( 'is_slide' ) ) {
				$value 	= __( 'Recently updated albums' , 'wp-photo-album-plus');
				$thumbhref 	= wppa_get_permalink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-album='.wppa( 'start_album' );
				$thumbajax 	= wppa_get_ajaxlink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-album='.wppa( 'start_album' );
				$title	= __( 'View the thumbnails' , 'wp-photo-album-plus');
				wppa_bcitem( $value, $thumbhref, $title, 'b8', $thumbajax );
			}
			$value 	= __( 'Recently updated albums' , 'wp-photo-album-plus');
			$href 	= '';
			$title	= '';
			wppa_bcitem( $value, $href, $title, 'b9' );
		}
		else { 			// Maybe a simple normal standard album???
			if ( wppa( 'is_owner' ) ) {
				$usr = get_user_by( 'login', wppa( 'is_owner' ) );
				if ( $usr ) $dispname = $usr->display_name;
				else $dispname = wppa( 'is_owner' );	// User deleted
				$various = sprintf( __( 'Various albums by %s' , 'wp-photo-album-plus'), $dispname );
			}
			else $various = __( 'Various albums' , 'wp-photo-album-plus');
			if ( wppa( 'is_slide' ) ) {
				$value 	= $is_albenum ? $various : wppa_get_album_name( $alb );
				$href 	= wppa_get_permalink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-album='.wppa( 'start_album' );
				$ajax 	= wppa_get_ajaxlink().'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-album='.wppa( 'start_album' );
				$title	= $is_albenum ? __( 'Albums:' , 'wp-photo-album-plus').' '.wppa( 'start_album' ) : __( 'Album:' , 'wp-photo-album-plus').' '.$value;
				wppa_bcitem( $value, $href, $title, 'b7', $ajax );
			}
			$value 	= $is_albenum ? $various : wppa_get_album_name( $alb );
			$href 	= '';
			$title	= '';
			$class 	= 'b10';
			wppa_bcitem( $value, $href, $title, $class );
		}

		// 'Go to thumbnail display' - icon
		if ( wppa( 'is_slide' ) && ! wppa( 'calendar' ) ) {
			if ( wppa_switch( 'bc_slide_thumblink' ) ) {
//				$pg = ( ( wppa_opt('thumb_page_size' ) == wppa_opt( 'slideshow_pagesize' ) ) && wppa_get_curpage() != '1' ) ? '&wppa-page='.wppa_get_curpage() : '';
//				$thumbhref .= $pg;
				if ( $virtual ) {
					if ( $thumbhref ) {
						$thumbhref = wppa_trim_wppa_( $thumbhref );
						$fs = wppa_opt( 'fontsize_nav' );
						if ( $fs != '' ) $fs += 3; else $fs = '15';	// iconsize = fontsize+3, Default to 15
						$imgs = 'height: '.$fs.'px; margin:0 0 -3px 0; padding:0; box-shadow:none;';
						wppa_out( 	'<a href="'.$thumbhref.'" title="'.__( 'Thumbnail view' , 'wp-photo-album-plus').
										'" class="wppa-nav-text" style="'.__wcs( 'wppa-nav-text' ).'float:right; cursor:pointer;" '.
										'onmouseover="jQuery(\'#wppa-tnv-'.wppa( 'mocc' ).'\').css(\'display\', \'none\'); jQuery(\'#wppa-tnvh-'.wppa( 'mocc' ).'\').css(\'display\', \'\')" '.
										'onmouseout="jQuery(\'#wppa-tnv-'.wppa( 'mocc' ).'\').css(\'display\', \'\'); jQuery(\'#wppa-tnvh-'.wppa( 'mocc' ).'\').css(\'display\', \'none\')" >'.
										'<img id="wppa-tnv-'.wppa( 'mocc' ).'" class="wppa-tnv" src="'.wppa_get_imgdir().'application_view_icons.png" alt="'.__( 'Thumbs', 'wp-photo-album-plus').'" style="'.$imgs.'" />'.
										'<img id="wppa-tnvh-'.wppa( 'mocc' ).'" class="wppa-tnv" src="'.wppa_get_imgdir().'application_view_icons_hover.png" alt="'.__( 'Thumbs', 'wp-photo-album-plus').'" style="display:none;'.$imgs.'" />'.
									'</a>' );
					}
				}
				else {
					$s = wppa( 'src' ) ? '&wppa-searchstring='.urlencode( wppa( 'searchstring' ) ) : '';
					$onclick = "wppaDoAjaxRender( ".wppa( 'mocc' ).", '".wppa_encrypt_url(wppa_get_album_url_ajax( wppa( 'start_album' ), '0' )."&amp;wppa-photos-only=1".$s)."', '".wppa_convert_to_pretty( wppa_encrypt_url( wppa_get_album_url( wppa( 'start_album' ), '0' ).'&wppa-photos-only=1'.$s ) )."' )";
					$fs = wppa_opt( 'fontsize_nav' );
					if ( $fs != '' ) $fs += 3; else $fs = '15';	// iconsize = fontsize+3, Default to 15
					$imgs = 'height: '.$fs.'px; margin:0 0 -3px 0; padding:0; box-shadow:none;';
					wppa_out( 	'<a title="'.__( 'Thumbnail view' , 'wp-photo-album-plus').
									'" class="wppa-nav-text" style="'.__wcs( 'wppa-nav-text' ).'float:right; cursor:pointer;" '.
									'onclick="'.$onclick.'" '.
									'onmouseover="jQuery(\'#wppa-tnv-'.wppa( 'mocc' ).'\').css(\'display\', \'none\'); jQuery(\'#wppa-tnvh-'.wppa( 'mocc' ).'\').css(\'display\', \'\')" '.
									'onmouseout="jQuery(\'#wppa-tnv-'.wppa( 'mocc' ).'\').css(\'display\', \'\'); jQuery(\'#wppa-tnvh-'.wppa( 'mocc' ).'\').css(\'display\', \'none\')" >'.
										'<img id="wppa-tnv-'.wppa( 'mocc' ).'" class="wppa-tnv" src="'.wppa_get_imgdir().'application_view_icons.png" alt="'.__( 'Thumbs', 'wp-photo-album-plus').'" style="'.$imgs.'" />'.
										'<img id="wppa-tnvh-'.wppa( 'mocc' ).'" class="wppa-tnv" src="'.wppa_get_imgdir().'application_view_icons_hover.png" alt="'.__( 'Thumbs', 'wp-photo-album-plus').'" style="display:none;'.$imgs.'" />'.
									'</a>' );
				}
			}
		}

	// Close the breadcrumb box
	wppa_out( '<div style="clear:both;" ></div>' );
	wppa_out( '</div>' );
}

// Display a breadcrumb item with optionally a seperator if it is a link.
// If it's a link, it's not the last item
function wppa_bcitem( $value = '', $href = '', $title = '', $class = '', $ajax = '' ) {
static $sep;

	// Encrypt
	$href = wppa_encrypt_url( $href );
	$ajax = wppa_encrypt_url( $ajax );

	// Convert url to pretty
	$href = wppa_convert_to_pretty( $href );

	// Has content?
	if ( ! $value ) return;	// No content
	if ( $href ) {
		wppa_out( 	'<a' .
						( $ajax && wppa_switch( 'allow_ajax' ) ?
						' onclick="wppaDoAjaxRender(' . wppa( 'mocc' ) . ', \'' . $ajax . '\', \'' . $href . '\' );"' :
						' href="' . $href . '"' ) .
						' class="wppa-nav-text ' . $class . '"' .
						' style="cursor:pointer;' . __wcs( 'wppa-nav-text' ).'"' .
						' title="' . esc_attr( $title ) . '" >' .
						$value .
					'</a>' );
	}
	else {					// No link, its the last item
		wppa_out(	'<span' .
						' id="bc-pname-'.wppa( 'mocc' ).'"' .
						' class="wppa-nav-text '.$class.'"' .
						' style="' .
							( $title ? 'cursor:pointer;' : '' ) .
							__wcs( 'wppa-nav-text' ) .
							'"' .
						' title="' . esc_attr( $title ) . '"' .
						' >' .
						$value .
					'</span>' );
		return;
	}

	// Add seperator
	if ( ! $sep ) {		// Compute the seperator
		$temp = wppa_opt( 'bc_separator' );
		switch ( $temp ) {
			case 'url':
				$size = wppa_opt( 'fontsize_nav' );
				if ( $size == '' ) $size = '12';
				$style = 'height:' . $size . 'px;';
				$sep = 	' ' .
						'<img' .
							' src="' . wppa_opt( 'bc_url' ) . '"' .
							' class="no-shadow"' .
							' style="' . $style . '"' .
						' />' .
						' ';
				break;
			case 'txt':
				$sep = 	' ' .
						html_entity_decode( stripslashes( wppa_opt( 'bc_txt' ) ), ENT_QUOTES ) .
						' ';
				break;
			default:
				$sep = ' &' . $temp . '; ';
		}
	}
	wppa_out(	'<span' .
					' class="wppa-nav-text ' . $class . '"' .
					' style="' . __wcs( 'wppa-nav-text' ).'"' .
					' >' .
					$sep .
				'</span>' );
}

// Recursive process to display the ( grand )parent albums
function wppa_crumb_ancestors( $alb, $to_cover ) {
global $wpdb;

	// Find parent
    $parent = wppa_get_parentalbumid( $alb );
	if ( $parent < '1' ) return;				// No parent -> toplevel -> done.

    wppa_crumb_ancestors( $parent, $to_cover );

	// Find the album specific link type ( content, slide, page or none )
	$slide = ( wppa_get_album_title_linktype( $parent ) == 'slide' ) ? '&amp;wppa-slide' : '';

	// NOT SLIDE when there are no photos
	if ( ! wppa_get_photo_count( $parent, 'use_treecounts' ) ) {
		$slide = '';
	}

	$pagid = $wpdb->get_var( $wpdb->prepare(
		"SELECT `cover_linkpage` FROM `".WPPA_ALBUMS."` WHERE `id` = %s", $parent
		) );
	wppa_dbg_q( 'Q-bc3' );

	$value 	= wppa_get_album_name( $parent );
	$href 	=
		wppa_get_permalink( $pagid ) .
		'wppa-album=' . $parent . '&amp;wppa-cover=' . $to_cover . $slide .
		'&amp;wppa-occur=' . wppa( 'occur' );
	$title 	= __( 'Album:' , 'wp-photo-album-plus') . ' ' . wppa_get_album_name( $parent );
	$class 	= 'b20';
	wppa_bcitem( $value, $href, $title, $class );
    return;
}

// Recursive process to display the ( grand )parent pages
function wppa_crumb_page_ancestors( $page = '0' ) {
global $wpdb;

	$query = "SELECT post_parent FROM " . $wpdb->posts . " WHERE post_type = 'page' AND post_status = 'publish' AND id = %s LIMIT 0,1";
	$parent = $wpdb->get_var( $wpdb->prepare( $query, $page ) );
	wppa_dbg_q( 'Q-bc4' );

	if ( ! is_numeric( $parent ) || $parent == '0' ) return;

	wppa_crumb_page_ancestors( $parent );

	$query = "SELECT post_title FROM " . $wpdb->posts . " WHERE post_type = 'page' AND post_status = 'publish' AND id = %s LIMIT 0,1";
	$title = $wpdb->get_var( $wpdb->prepare( $query, $parent ) );
	wppa_dbg_q( 'Q-bc5' );

	$title = __( stripslashes( $title ) );
	if ( ! $title ) {
		$title = '****';		// Page exists but is not publish
		wppa_bcitem( $title, '#', __( 'Unpublished' , 'wp-photo-album-plus'), $class = 'b2' );
	} else {
		wppa_bcitem( $title, get_page_link( $parent ), __( 'Page:' , 'wp-photo-album-plus').' '.$title, 'b2' );
	}
}

// Get the page id, returns the page id we are working for, even when Ajax
function wppa_get_the_page_id() {
	$page = @ get_the_ID();
	if ( ! $page ) {
		if ( isset( $_REQUEST['page_id'] ) ) $page = $_REQUEST['page_id'];
		elseif ( isset( $_REQUEST['wppa-fromp'] ) ) $page = $_REQUEST['wppa-fromp'];
		else $page = '0';
	}
	return $page;
}

// Convert display searchstring into readable format for use in title tooltip
// Reurns value only if intersection or unioun symbols are in the input text
function wppa_dss_to_title( $txt ) {

	$result = '';

	//                   AND                                    OR
	if ( strpos( $txt, '&#8745' ) === false && strpos( $txt, '&#8746' ) === false ) {
		return '';
	}

	$orarr = explode( '&#8746', $txt );

	$result = __( 'Found photos will meet the search criteria as follows:', 'wp-photo-album-plus' ) . ' ';
	foreach ( array_keys( $orarr ) as $orkey ) {
		if ( strpos( $orarr[ $orkey ], '&#8745' ) !== false ) {
			$oritem = str_replace( '&#8745', __( 'AND', 'wp-photo-album-plus' ), $orarr[ $orkey ] );
			$orarr[ $orkey ] = ' ( ' . $oritem . ' ) ';
		}
	}
	$result .= implode ( __( 'OR', 'wp-photo-album-plus' ) , $orarr );

	return $result;
}