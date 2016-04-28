<?php
/* wppa-boxes-html.php
* Package: wp-photo-album-plus
*
* Various wppa boxes
* Version 6.4.08
*
*
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

// Open / cose the box containing the thumbnails
function wppa_thumb_area( $action ) {

	// Init
	$result = '';
	$mocc 	= wppa( 'mocc' );
	$alt 	= wppa( 'alt' );

	// Open thumbnail area box
	if ( $action == 'open' ) {
		if ( is_feed() ) {
			$result .= 	'<div'.
							' id="wppa-thumb-area-' . $mocc . '"' .
							' class="wppa-thumb-area"' .
							' style="' . __wcs( 'wppa-box' ) . __wcs( 'wppa-' . $alt ) . '"' .
							' >';
		}
		else {
			$result .= 	"\n";
			$result .= 	'<div' .
							' id="wppa-thumb-area-' . $mocc . '"' .
							' class="' .
								'wppa-thumb-area ' .
								'wppa-thumb-area-' . $mocc . ' ' .
								'wppa-box wppa-' . $alt .
								'"' .
							' style="' .
								__wcs( 'wppa-box' ) .
								__wcs( 'wppa-' . $alt ) .
								'width:' . wppa_get_thumbnail_area_width() . 'px;' .
								'"' .
							' >';

			if ( wppa_is_int( wppa( 'start_album' ) ) ) {
				wppa_bump_viewcount( 'album', wppa( 'start_album') );
			}
		}

		// Toggle alt/even
		wppa_toggle_alt();
	}

	// Cloase thumbnail area box
	elseif ( $action == 'close' ) {

		// Display create subalbum and upload photo links conditionally
		if ( 	! wppa( 'is_upldr' ) &&
				! wppa( 'searchstring' ) &&
				! wppa( 'supersearch' )  &&
				! wppa( 'calendar' ) ) {

			$alb = wppa( 'current_album' );
			wppa_user_create_html( $alb, wppa_get_container_width( 'netto' ), 'thumb' );
			wppa_user_upload_html( $alb, wppa_get_container_width( 'netto' ), 'thumb' );
		}

		// Clear both
		$result .= '<div class="wppa-clear" style="' . __wis( 'clear:both;' ) . '" ></div>';

		// Close the thumbnail box
		$result .= '</div>';
	}

	// Unimplemented action
	else {
		$result .= '<span style="color:red;">' .
						'Error, wppa_thumb_area() called with wrong argument: ' .
						$action .
						'. Possible values: \'open\' or \'close\'' .
					'</span>';
	}

	// Output result
	wppa_out( $result );
}

// Search box
function wppa_search_box() {

	// Init
	$result = '';

	// No search box on feeds
	if ( is_feed() ) return;

	// Open container
	wppa_container( 'open' );

	// Open wrapper
	$result .= "\n";
	$result .= '<div' .
					' id="wppa-search-'.wppa( 'mocc' ) . '"' .
					' class="wppa-box wppa-search"' .
					' style="' . __wcs( 'wppa-box' ) . __wcs( 'wppa-search' ) . '"' .
					' >';

	// The search html
	$result .= wppa_get_search_html( '', wppa( 'may_sub' ), wppa( 'may_root' ), wppa( 'forceroot' ), wppa( 'landingpage' ) );

	// Clear both
	$result .= '<div class="wppa-clear" style="'.__wis( 'clear:both;' ).'" ></div>';

	// Close wrapper
	$result .= '</div>';

	// Output
	wppa_out( $result );

	// Close container
	wppa_container( 'close' );
}

// Get search html
function wppa_get_search_html( $label = '', $sub = false, $rt = false, $force_root = '', $page = '' ) {
global $wppa_session;

	$wppa_session['has_searchbox'] = true;
	wppa_save_session();

	if ( ! $page ) {
		$page 		= wppa_get_the_landing_page( 	'wppa_search_linkpage',
													__( 'Photo search results', 'wp-photo-album-plus' )
												);
	}
	$pagelink 		= wppa_dbg_url( get_page_link( $page ) );
	$cansubsearch  	= $sub && $wppa_session['use_searchstring'];
	$value 			= $cansubsearch ? '' : wppa_test_for_search( true );
	$root 			= $wppa_session['search_root'];
	$rootboxset 	= $root ? '' : 'checked="checked" disabled="disabled"';
	$fontsize 		= wppa_in_widget() ? 'font-size: 9px;' : '';
	$mocc 			= wppa( 'mocc' );

	if ( ! wppa_in_widget() ) $label = wppa_opt( 'search_toptext' );

	wppa_dbg_msg( 'Root='.$root.': '.wppa_get_album_name( $root ) );

	$result =
	'<form' .
		' id="wppa_searchform_' . $mocc . '"' .
		' action="' . $pagelink.'"' .
		' method="post"' .
		' class="widget_search"' .
		' >' .
		'<div>' .
			$label .
			'<input' .
				' type="text"' .
				' class="wppa-search-input"' .
				' style="width:60%;"' .
				' name="wppa-searchstring"' .
				' id="wppa_s-'.$mocc.'"' .
				' value="'.$value.'"' .
				' />' .
			'<input' .
				' id="wppa_searchsubmit-'.$mocc.'"' .
				' type="submit"' .
				' name="wppa-search-submit"' .
				' value="'.__( 'Search', 'wp-photo-album-plus' ).'"' .
				' onclick="if ( document.getElementById( \'wppa_s-'.$mocc.'\' ).value == \'\' ) return false;"' .
				' />';
	if ( $force_root ) {
		$result .=
			'<input' .
				' type="hidden"' .
				' name="wppa-forceroot"' .
				' value="' . $force_root . '"' .
				' />';
	}
	$result .=
			'<input' .
				' type="hidden"' .
				' name="wppa-searchroot"' .
				' class="wppa-search-root-id"' .
				' value="' . $root . '"' .
				' />' .
			( $rt && ! $force_root ?
				'<div style="clear:both" ></div>' .
				'<small class="wppa-search-root" style="margin:0;padding:4px 0 0;" >' .
					wppa_display_root( $root ) .
				'</small>' .
				'<div style="clear:both;' . $fontsize . '" >
					<input type="checkbox" name="wppa-rootsearch" class="wppa-rootbox" ' . $rootboxset . ' /> ' .
						wppa_opt( 'search_in_section' ) .
				'</div>' : '' ) .
			( $sub ?
				'<div style="clear:both" ></div>' .
				'<small class="wppa-display-searchstring" style="margin:0;padding:4px 0 0;" >' .
					$wppa_session['display_searchstring'] .
				'</small>' .
				'<div style="clear:both;' . $fontsize . '" >' .
					'<input' .
						' type="checkbox"' .
						' name="wppa-subsearch"' .
						' class="wppa-search-sub-box"' .
						( empty( $wppa_session['display_searchstring'] ) ? ' disabled="disabled"' : '' ) .
						' onchange="wppaSubboxChange(this)"' .
						' /> '.
						wppa_opt( 'search_in_results' ) .
				'</div>' : '' ) .
		'</div>' .
	'</form>';

	return $result;
}

// The supersearch box
function wppa_supersearch_box() {

	if ( is_feed() ) return;

	wppa_container( 'open' );

	wppa_out( 	'<div' .
					' id="wppa-search-' . wppa( 'mocc' ) . '"' .
					' class="wppa-box wppa-search"' .
					' style="' . __wcs( 'wppa-box' ) . __wcs( 'wppa-search' ) . '"' .
					' >' .
					wppa_get_supersearch_html() .
					'<div class="wppa-clear" style="'.__wis( 'clear:both;' ).'" >' .
					'</div>' .
				'</div>'
			);

	wppa_container( 'close' );
}

// Get supersearch html
function wppa_get_supersearch_html() {
global $wpdb;
global $wppa_session;

	// Init
	$page 		= wppa_get_the_landing_page( 	'wppa_supersearch_linkpage',
												__( 'Photo search results' ,'wp-photo-album-plus' )
											);
	$pagelink 	= wppa_dbg_url( get_page_link( $page ) );
	$fontsize 	= wppa_in_widget() ? 'font-size: 9px;' : '';
	$query 		= 	"SELECT `id`, `name`, `owner` FROM `" . WPPA_ALBUMS . "` ORDER BY `name`";
	$albums 	= $wpdb->get_results( $query, ARRAY_A );
	$query 		= 	"SELECT `name` FROM `" . WPPA_PHOTOS .
						"` WHERE `status` <> 'pending' AND `status` <> 'scheduled' ORDER BY `name`";
	$photonames	= $wpdb->get_results( $query, ARRAY_A );
	$query 		= 	"SELECT `owner` FROM `" .WPPA_PHOTOS .
						"` WHERE `status` <> 'pending' AND `status` <> 'scheduled' ORDER BY `owner`";
	$ownerlist 	= $wpdb->get_results( $query, ARRAY_A );
	$catlist 	= wppa_get_catlist();
	$taglist 	= wppa_get_taglist();
	$ss_data 	= explode( ',', $wppa_session['supersearch'] );
	if ( count( $ss_data ) < '4' ) {
		$ss_data = array( '', '', '', '' );
	}
	$ss_cats 	= ( $ss_data['0'] == 'a' && $ss_data['1'] == 'c' ) ? explode( '.', $ss_data['3'] ) : array();
	$ss_tags 	= ( $ss_data['0'] == 'p' && $ss_data['1'] == 'g' ) ? explode( '.', $ss_data['3'] ) : array();
	$ss_data['3'] = str_replace( '...', '***', $ss_data['3'] );
	$ss_atxt 	= ( $ss_data['0'] == 'a' && $ss_data['1'] == 't' ) ? explode( '.', $ss_data['3'] ) : array();
	foreach( array_keys( $ss_atxt ) as $key ) {
		$ss_atxt[$key] = str_replace( '***', '...', $ss_atxt[$key] );
	}
	$ss_ptxt 	= ( $ss_data['0'] == 'p' && $ss_data['1'] == 't' ) ? explode( '.', $ss_data['3'] ) : array();
	foreach( array_keys( $ss_ptxt ) as $key ) {
		$ss_ptxt[$key] = str_replace( '***', '...', $ss_ptxt[$key] );
	}
	$ss_data['3'] = str_replace( '***', '...', $ss_data['3'] );

	$query 		= "SELECT `slug` FROM `".WPPA_INDEX."` WHERE `albums` <> '' ORDER BY `slug`";
	$albumtxt 	= $wpdb->get_results( $query, ARRAY_A );
	$query 		= "SELECT `slug` FROM `".WPPA_INDEX."` WHERE `photos` <> '' ORDER BY `slug`";
	$phototxt 	= $wpdb->get_results( $query, ARRAY_A );
	$iptclist 	= wppa_switch( 'save_iptc' ) ?
					$wpdb->get_results( "SELECT `tag`, `description` FROM `" . WPPA_IPTC .
							"` WHERE `photo` = '0' AND `status` <> 'hide' ", ARRAY_A ) : array();
	$exiflist 	= wppa_switch( 'save_exif' ) ?
					$wpdb->get_results( "SELECT `tag`, `description` FROM `" . WPPA_EXIF .
							"` WHERE `photo` = '0' AND `status` <> 'hide' ", ARRAY_A ) : array();

	// Check for empty albums
	if ( wppa_switch( 'skip_empty_albums' ) ) {
		$user = wppa_get_user();
		if ( is_array( $albums ) ) foreach ( array_keys( $albums ) as $albumkey ) {
			$albumid 	= $albums[$albumkey]['id'];
			$albumowner = $albums[$albumkey]['owner'];
			$treecount 	= wppa_treecount_a( $albums[$albumkey]['id'] );
			$photocount = $treecount['photos'];
			if ( ! $photocount && ! wppa_user_is( 'administrator' ) && $user != $albumowner ) {
				unset( $albums[$albumkey] );
			}
		}
	}
	if ( empty( $albums ) ) $albums = array();

	// Compress photonames if partial length search
	if ( wppa_opt( 'ss_name_max' ) ) {
		$maxl = wppa_opt( 'ss_name_max' );
		$last = '';
		foreach ( array_keys( $photonames ) as $key ) {
			if ( strlen( $photonames[$key]['name'] ) > $maxl ) {
				$photonames[$key]['name'] = substr( $photonames[$key]['name'], 0, $maxl ) . '...';
			}
			if ( $photonames[$key]['name'] == $last ) {
				unset( $photonames[$key] );
			}
			else {
				$last = $photonames[$key]['name'];
			}
		}
	}

	// Compress phototxt if partial length search
	if ( wppa_opt( 'ss_text_max' ) ) {
		$maxl = wppa_opt( 'ss_text_max' );
		$last = '';
		foreach ( array_keys( $phototxt ) as $key ) {
			if ( strlen( $phototxt[$key]['slug'] ) > $maxl ) {
				$phototxt[$key]['slug'] = substr( $phototxt[$key]['slug'], 0, $maxl ) . '...';
			}
			if ( $phototxt[$key]['slug'] == $last ) {
				unset( $phototxt[$key] );
			}
			else {
				$last = $phototxt[$key]['slug'];
			}
		}
	}

	// Remove dup photo owners
	$last = '';
	foreach( array_keys( $ownerlist ) as $key ) {
		if ( $ownerlist[$key]['owner'] == $last ) {
			unset( $ownerlist[$key] );
		}
		else {
			$last = $ownerlist[$key]['owner'];
		}
	}

	// Make the html
	$id = 'wppa_searchform_' . wppa('mocc');
	$result =
	'<form' .
		' id="' . $id . '"' .
		' action="'.$pagelink.'"' .
		' method="post"' .
		' class="widget_search"' .
		' >' .
		'<input' .
			' type="hidden"' .
			' id="wppa-ss-pageurl-'.wppa('mocc').'"' .
			' name="wppa-ss-pageurl"' .
			' value="'.$pagelink.'"' .
		' />';

		// album or photo
		$id = 'wppa-ss-pa-'.wppa('mocc');
		$result .=
		'<select' .
			' id="' . $id . '"' .
			' name="wppa-ss-pa"' .
			' style="margin:2px;padding:0;vertical-align:top;"' .
			' onchange="wppaSuperSearchSelect( ' . wppa('mocc') . ' );"' .
			' size="2"' .
			' >' .
			'<option' .
				' value="a" ' .
				( $ss_data['0'] == 'a' ? 'selected="selected" ' : '' ) .
				' >' .
					__('Albums', 'wp-photo-album-plus' ) .
			'</option>' .
			'<option' .
				' value="p" ' .
				( $ss_data['0'] == 'p' ? 'selected="selected" ' : '' ) .
				' >' .
					__('Photos', 'wp-photo-album-plus' ) .
			'</option>' .
		'</select>';

		// album
		$id = 'wppa-ss-albumopt-' . wppa('mocc');
		$result .=
		'<select' .
			' id="' . $id . '"' .
			' name="wppa-ss-albumopt"' .
			' style="display:none;margin:2px;padding:0;vertical-align:top;"' .
			' onchange="wppaSuperSearchSelect( '.wppa('mocc').' );"' .
			' size="' . ( ! empty( $catlist ) ? '3' : '2' ) . '"' .
			' >' .
			( ! empty( $catlist ) ?
				'<option' .
					' value="c"' .
					( $ss_data['0'] == 'a' && $ss_data['1'] == 'c' ? 'selected="selected" ' : '' ) .
					' >' .
						__( 'Category', 'wp-photo-album-plus' ) .
				'</option>' : ''
			) .
			'<option' .
				' value="n"' .
				( $ss_data['0'] == 'a' && $ss_data['1'] == 'n' ? 'selected="selected" ' : '' ) .
				' >' .
					__( 'Name', 'wp-photo-album-plus' ) .
			'</option>' .
			'<option' .
				' value="t"' .
				( $ss_data['0'] == 'a' && $ss_data['1'] == 't' ? 'selected="selected" ' : '' ) .
				' >' .
					__( 'Text', 'wp-photo-album-plus' ) .
			'</option>' .
		'</select>';

		// album category
		if ( ! empty( $catlist ) ) {
			$id = 'wppa-ss-albumcat-'.wppa('mocc');
			$result .=
			'<select'.
				' id="' . $id . '"' .
				' name="wppa-ss-albumcat"' .
				' style="display:none;margin:2px;padding:0;vertical-align:top;"' .
				' onchange="wppaSuperSearchSelect( '.wppa('mocc').' );"' .
				' size="' . ( min( count( $catlist ), '6' ) ) . '"' .
				' multiple' .
				' title="' .
				esc_attr( __( 'CTRL+Click to add/remove option.', 'wp-photo-album-plus' ) ) . "\n" .
				esc_attr( __( 'Items must meet all selected options.', 'wp-photo-album-plus' ) ) .
					'"' .
				' >';
				foreach ( array_keys( $catlist ) as $cat ) {
					$sel = in_array ( $cat, $ss_cats );
					$result .=
					'<option' .
						' value="' . $cat . '"' .
						' class="' . $id . '"' .
						( $sel ? ' selected="selected"' : '' ) .
						' >' .
							$cat .
					'</option>';
				}
			$result .=
			'</select>';
		}

		// album name
		$id = 'wppa-ss-albumname-'.wppa('mocc');
		$result .=
		'<select'.
			' id="' . $id . '"' .
			' name="wppa-ss-albumname"' .
			' style="display:none;margin:2px;padding:0;vertical-align:top;"' .
			' onchange="wppaSuperSearchSelect( '.wppa('mocc').' );"' .
			' size="' . ( min( count( $albums ), '6' ) ) . '"' .
			' >';
			foreach ( $albums as $album ) {
				$name = stripslashes( $album['name'] );
				$sel = ( $ss_data['3'] == $name && $ss_data['0'] == 'a' && $ss_data['1'] == 'n' );
				$result .=
				'<option' .
					' value="' . esc_attr( $name ) . '"' .
					( $sel ? ' selected="selected"' : '' ) .
					' >' .
						__( $name ) .
				'</option>';
			}
		$result .=
		'</select>';

		// album text
		$id = 'wppa-ss-albumtext-'.wppa('mocc');
		$result .= '
		<select'.
			' id="' . $id . '"' .
			' name="wppa-ss-albumtext"' .
			' style="display:none;margin:2px;padding:0;vertical-align:top;"' .
			' onchange="wppaSuperSearchSelect( '.wppa('mocc').' );"' .
			' size="' . ( min( count( $albumtxt ), '6' ) ) . '"' .
			' multiple="multiple"' .
			' title="' .
				esc_attr( __( 'CTRL+Click to add/remove option.', 'wp-photo-album-plus' ) ) . "\n" .
				esc_attr( __( 'Items must meet all selected options.', 'wp-photo-album-plus' ) ) .
				'"' .
			' >';
			foreach ( $albumtxt as $txt ) {
				$text = $txt['slug'];
				$sel = in_array ( $text, $ss_atxt );
				$result .=
				'<option' .
					' value="' . $text . '"' .
					' class="' . $id . '"' .
					( $sel ? ' selected="selected"' : '' ) .
					' >' .
						$text .
				'</option>';
			}
		$result .= '
		</select>';

		// photo
		$n = '1' +
			( count( $ownerlist ) > '1' ) +
			( ! empty( $taglist ) ) +
			'1' +
			( wppa_switch( 'save_iptc' ) ) +
			( wppa_switch( 'save_exif' ) );
		$result .= '
		<select'.
			' id="wppa-ss-photoopt-'.wppa('mocc').'"' .
			' name="wppa-ss-photoopt"' .
			' style="display:none;margin:2px;padding:0;vertical-align:top;"' .
			' onchange="wppaSuperSearchSelect( '.wppa('mocc').' );"' .
			' size="' . $n . '"' .
			' >' .
				'<option' .
					' value="n"' .
					( $ss_data['0'] == 'p' && $ss_data['1'] == 'n' ? 'selected="selected" ' : '' ) .
					' >' .
						__( 'Name', 'wp-photo-album-plus' ) .
				'</option>';
				if ( count( $ownerlist ) > '1' ) {
					$result .=
					'<option' .
						' value="o"' .
						( $ss_data['0'] == 'p' && $ss_data['1'] == 'o' ? 'selected="selected" ' : '' ) .
						' >' .
							__( 'Owner', 'wp-photo-album-plus' ) .
					'</option>';
				}
				if ( ! empty( $taglist ) ) {
					$result .=
					'<option' .
						' value="g"' .
						( $ss_data['0'] == 'p' && $ss_data['1'] == 'g' ? 'selected="selected" ' : '' ) .
						' >' .
							__( 'Tag', 'wp-photo-album-plus' ) .
					'</option>';
				}
				$result .=
				'<option' .
					' value="t"' .
					( $ss_data['0'] == 'p' && $ss_data['1'] == 't' ? 'selected="selected" ' : '' ) .
					' >' .
						__( 'Text', 'wp-photo-album-plus' ) .
				'</option>';
				if ( wppa_switch( 'save_iptc' ) ) {
					$result .=
					'<option' .
						' value="i"' .
						( $ss_data['0'] == 'p' && $ss_data['1'] == 'i' ? 'selected="selected" ' : '' ) .
						' >' .
							__( 'Iptc', 'wp-photo-album-plus' ) .
					'</option>';
				}
				if ( wppa_switch( 'save_exif' ) ) {
					$result .=
					'<option' .
						' value="e"' .
						( $ss_data['0'] == 'p' && $ss_data['1'] == 'e' ? 'selected="selected" ' : '' ) .
						' >' .
							__( 'Exif', 'wp-photo-album-plus' ) .
					'</option>';
				}
		$result .=
		'</select>';

		// photo name
		$id = 'wppa-ss-photoname-'.wppa('mocc');
		$result .= '
		<select'.
			' id="' . $id . '"' .
			' name="wppa-ss-photoname"' .
			' style="display:none;margin:2px;padding:0;vertical-align:top;"' .
			' onchange="wppaSuperSearchSelect( '.wppa('mocc').' );"' .
			' size="' . min( count( $photonames ), '6' ) . '"' .
			' >';
			foreach ( $photonames as $photo ) {
				$name = stripslashes( $photo['name'] );
				$sel = ( $ss_data['3'] == $name && $ss_data['0'] == 'p' && $ss_data['1'] == 'n' );
				$result .=
				'<option' .
					' value="' . esc_attr( $name ) . '"' .
					( $sel ? ' selected="selected"' : '' ) .
					' >' .
						__( $name ) .
				'</option>';
			}
		$result .= '
		</select>';

		// photo owner
		$id = 'wppa-ss-photoowner-'.wppa('mocc');
		$result .= '
		<select'.
			' id="' . $id . '"' .
			' name="wppa-ss-photoowner"' .
			' style="display:none;margin:2px;padding:0;vertical-align:top;"' .
			' onchange="wppaSuperSearchSelect( '.wppa('mocc').' );"' .
			' size="' . ( min( count( $ownerlist ), '6' ) ) . '"' .
			' >';
			foreach ( $ownerlist as $photo ) {
				$owner = $photo['owner'];
				$sel = ( $ss_data['3'] == $owner && $ss_data['0'] == 'p' && $ss_data['1'] == 'o' );
				$result .=
				'<option' .
					' value="' . $owner . '"' .
					( $sel ? ' selected="selected"' : '' ) .
					' >' .
						$owner .
				'</option>';
			}
		$result .= '
		</select>';

		// photo tag
		if ( ! empty( $taglist ) ) {
			$id = 'wppa-ss-phototag-'.wppa('mocc');
			$result .= '
			<select'.
				' id="' . $id . '"' .
				' name="wppa-ss-phototag"' .
				' style="display:none;margin:2px;padding:0;vertical-align:top;"' .
				' onchange="wppaSuperSearchSelect( '.wppa('mocc').' );"' .
				' size="' . ( min( count( $taglist ), '6' ) ) . '"' .
				' multiple' .
				' title="' .
					esc_attr( __( 'CTRL+Click to add/remove option.' , 'wp-photo-album-plus' ) ) . "\n" .
					esc_attr( __( 'Items must meet all selected options.' , 'wp-photo-album-plus' ) ) .
					'"' .
				' >';
				foreach ( array_keys( $taglist ) as $tag ) {
					$sel = in_array ( $tag, $ss_tags );
					$result .=
					'<option' .
						' value="'.$tag.'"' .
						' class="' . $id . '"' .
						( $sel ? ' selected="selected"' : '' ) .
						' >' .
							$tag .
					'</option>';
				}
			$result .=
			'</select>';
		}

		// photo text
		$id = 'wppa-ss-phototext-'.wppa('mocc');
		$result .= '
		<select' .
			' id="' . $id . '"' .
			' name="wppa-ss-phototext"' .
			' style="display:none;margin:2px;padding:0;vertical-align:top;"' .
			' onchange="wppaSuperSearchSelect( '.wppa('mocc').' );"' .
			' size="' . ( min( count( $phototxt ), '6' ) ) . '"' .
			' multiple="multiple"' .
			' title="' .
				esc_attr( __( 'CTRL+Click to add/remove option.' , 'wp-photo-album-plus' ) ) . "\n" .
				esc_attr( __( 'Items must meet all selected options.' , 'wp-photo-album-plus' ) ) .
				'"' .
			' >';
			foreach ( $phototxt as $txt ) {
				$text 	= $txt['slug'];
				$sel 	= in_array ( $text, $ss_ptxt );
				$result .=
				'<option' .
					' value="' . $text . '"' .
					' class="' . $id . '"' .
					( $sel ? ' selected="selected"' : '' ) .
					' >' .
						$text .
				'</option>';
			}
		$result .=
		'</select>';

		// photo iptc
		$result .= '
		<select' .
			' id="wppa-ss-photoiptc-'.wppa('mocc').'"' .
			' name="wppa-ss-photoiptc"' .
			' style="display:none;margin:2px;padding:0;vertical-align:top;"' .
			' onchange="wppaSuperSearchSelect( '.wppa('mocc').' );"' .
			' size="' . min( count( $iptclist ), '6' ) . '"' .
			' >';
			$reftag = str_replace( 'H', '#', $ss_data['2'] );
			foreach ( $iptclist as $item ) {
				$tag = $item['tag'];
				$sel = ( $reftag == $tag && $ss_data['0'] = 'p' && $ss_data['1'] == 'i' );
				$result .=
				'<option' .
					' value="' . $tag . '"' .
					( $sel ? ' selected="selected"' : '' ) .
					' >' .
						__( $item['description'], 'wp-photo-album-plus' ) .
				'</option>';
			}
		$result .=
		'</select>';

		// Iptc items
		$result .= '
		<select' .
			' id="wppa-ss-iptcopts-'.wppa('mocc').'"' .
			' name="wppa-ss-iptcopts"' .
			' style="display:none;margin:2px;padding:0;vertical-align:top;"' .
			' size="6"' .
			' onchange="wppaSuperSearchSelect('.wppa('mocc').')"' .
			' >
		</select>';

		// photo exif
		$result .= '
		<select' .
			' id="wppa-ss-photoexif-'.wppa('mocc').'"' .
			' name="wppa-ss-photoexif"' .
			' style="display:none;margin:2px;padding:0;vertical-align:top;"' .
			' onchange="wppaSuperSearchSelect( '.wppa('mocc').' );"' .
			' size="' . min( count( $exiflist ), '6' ) . '"' .
			' >';
			$reftag = str_replace( 'H', '#', $ss_data['2'] );
			foreach ( $exiflist as $item ) {
				$tag = $item['tag'];
				$sel = ( $reftag == $tag && $ss_data['0'] = 'p' && $ss_data['1'] == 'e' );
				$result .=
				'<option' .
					' value="' . $tag . '"' .
					( $sel ? ' selected="selected"' : '' ) .
					' >' .
						__( $item['description'], 'wp-photo-album-plus' ) .
				'</option>';
			}
		$result .=
		'</select>';

		// Exif items
		$result .= '
		<select' .
			' id="wppa-ss-exifopts-'.wppa('mocc').'"' .
			' name="wppa-ss-exifopts"' .
			' style="display:none;margin:2px;padding:0;vertical-align:top;"' .
			' size="6"' .
			' onchange="wppaSuperSearchSelect('.wppa('mocc').')"' .
			' >
		</select>';

		// The spinner
		$result .= '
		<img' .
			' id="wppa-ss-spinner-'.wppa('mocc').'"' .
			' src="' . wppa_get_imgdir() . '/wpspin.gif' . '"' .
			' style="margin:0 4px;display:none;"' .
		' />';

		// The button
		$result .= '
		<input' .
			' type="button"' .
			' id="wppa-ss-button-' . wppa('mocc') . '"' .
			' value="' . __( 'Submit', 'wp-photo-album-plus' ) . '"' .
			' style="vertical-align:top;margin:2px;"' .
			' onclick="wppaSuperSearchSelect(' . wppa('mocc') .' , true)"' .
			' ontouchstart="wppaSuperSearchSelect(' . wppa('mocc') .' , true)"' .
		' />';

	$result .= '
	</form>
	<script type="text/javascript" >
		wppaSuperSearchSelect(' . wppa('mocc') . ');
	</script>';

	return $result;
}

// Superview box
function wppa_superview_box( $album_root = '0', $sort = true ) {

	if ( is_feed() ) return;

	wppa_container( 'open' );

	wppa_out(
		'<div' .
			' id="wppa-superview-' . wppa( 'mocc' ) . '"' .
			' class="wppa-box wppa-superview"' .
			' style="' . __wcs( 'wppa-box' ) . __wcs( 'wppa-superview' ) . '"' .
			' >' .
			wppa_get_superview_html( $album_root, $sort ) .
			'<div class="wppa-clear" style="'.__wis( 'clear:both;' ).'" >' .
			'</div>' .
		'</div>'
	);

	wppa_container( 'close' );
}

// Get superview html
function wppa_get_superview_html( $album_root = '0', $sort = true ) {
global $wppa_session;

	$page = wppa_get_the_landing_page( 	'wppa_super_view_linkpage',
										__( 'Super View Photos' ,'wp-photo-album-plus' )
									);
	$url = get_permalink( $page );

	$checked = 'checked="checked"';

	$result = '
	<div>
		<form action="' . $url . '" method = "get">
			<label>' . __( 'Album:' , 'wp-photo-album-plus') . '</label><br />
			<select name="wppa-album">' .
				wppa_album_select_a( array( 'selected' 			=> $wppa_session['superalbum'],
											'addpleaseselect' 	=> true,
											'root' 				=> $album_root,
											'content' 			=> true,
											'sort'				=> $sort,
											'path' 				=> ( ! wppa_in_widget() ),
											 ) ) .
			'</select><br />
			<input' .
				' type="radio"' .
				' name="wppa-slide"' .
				' value="nil" ' .
				( $wppa_session['superview'] == 'thumbs' ? $checked : '' ) .
				' >' .
				__( 'Thumbnails' , 'wp-photo-album-plus') .
				'<br />
			<input' .
				' type="radio"' .
				' name="wppa-slide"' .
				' value="1" ' .
				( $wppa_session['superview'] == 'slide' ? $checked : '' ) .
				' >' .
				__( 'Slideshow', 'wp-photo-album-plus' ) .
				'<br />
			<input type="hidden" name="wppa-occur" value="1" />
			<input type="hidden" name="wppa-superview" value="1" />
			<input type="submit" value="' . __( 'Submit', 'wp-photo-album-plus' ) . '" />
		</form>
	</div>
	';

	return $result;
}

// The admins choice html
function wppa_get_admins_choice_html() {

	// Find zip dir
	$zipsdir = WPPA_UPLOAD_PATH.'/zips/';

	// Find all zipfiles
	$zipfiles = glob($zipsdir.'*.zip');

	if ( $zipfiles ) {

		$result = 	'<ul>';

		// Compose the current users zip filename
		$myzip = $zipsdir.wppa_get_user().'.zip';

		foreach( $zipfiles as $zipfile ) {

			// Find zipfiles user
			$user = wppa_strip_ext( basename( $zipfile ) );

			// Check file existance
			if ( is_file( $zipfile ) ) {

				// Open zip
				$wppa_zip = new ZipArchive;
				$wppa_zip->open( $zipfile );
				if ( $wppa_zip ) {

					// Look photos up in zip
					$title = '';
					for( $i = 0; $i < $wppa_zip->numFiles; $i++ ) {
						$stat = $wppa_zip->statIndex( $i );
						$title .= esc_attr($stat['name']) . "\n";
					}
					$result .= 	'<li title="'.$title.'" >' .
									'<a href="'. WPPA_UPLOAD_URL.'/zips/'.basename($zipfile).'" >' .
										$user .
									'</a>';
									if ( $zipfile == $myzip ) {
										$result .=
										'<a' .
											' onclick="wppaAjaxDeleteMyZip();"' .
											' style="float:right;cursor:pointer;" >' .
											__('Delete', 'wp-photo-album-plus');
										'</a>';
									}
					$result .=	'</li>';
				}
			}
		}
		$result .= 	'</ul>';
	}
	else {
		$result = __('No zipfiles available', 'wp-photo-album-plus');
	}

	return $result;
}

// The tagcloud box
function wppa_tagcloud_box( $seltags = '', $minsize = '8', $maxsize = '24' ) {

	if ( is_feed() ) return;

	wppa_container( 'open' );

	wppa_out(
		'<div' .
			' id="wppa-tagcloud-' . wppa( 'mocc' ) . '"' .
			' class="wppa-box wppa-tagcloud"' .
			' style="'.__wcs( 'wppa-box' ).__wcs( 'wppa-tagcloud' ).'"' .
			' >' .
			wppa_get_tagcloud_html( $seltags, $minsize, $maxsize ) .
			'<div class="wppa-clear" style="'.__wis( 'clear:both;' ).'" >' .
			'</div>' .
		'</div>'
	);

	wppa_container( 'close' );
}

// Get html for tagcloud
function wppa_get_tagcloud_html( $seltags = '', $minsize = '8', $maxsize = '24' ) {

	$page 	= wppa_get_the_landing_page( 	'wppa_tagcloud_linkpage',
										__( 'Tagged photos' ,'wp-photo-album-plus' )
									);
	$oc 	= wppa_opt( 'tagcloud_linkpage_oc' );
	$result = '';
	if ( $page ) {
		$hr = wppa_get_permalink( $page );
		if ( wppa_opt( 'tagcloud_linktype' ) == 'album' ) {
			$hr .= 'wppa-album=0&amp;wppa-cover=0&amp;wppa-occur='.$oc;
		}
		if ( wppa_opt( 'tagcloud_linktype' ) == 'slide' ) {
			$hr .= 'wppa-album=0&amp;wppa-cover=0&amp;wppa-occur='.$oc.'&amp;slide';
		}
	}
	else {
		return __( 'Please select a tagcloud landing page in Table VI-C3b', 'wp-photo-album-plus');
	}
	$tags = wppa_get_taglist();
	if ( $tags ) {
		$top = '0';
		foreach ( $tags as $tag ) {	// Find largest percentage
			if ( $tag['fraction'] > $top ) $top = $tag['fraction'];
		}
		if ( $top ) $factor = ( $maxsize - $minsize ) / $top;
		else $factor = '1.0';
		$selarr = $seltags ? explode( ',', $seltags ) : array();
		foreach ( $tags as $tag ) {
			if ( ! $seltags || in_array( $tag['tag'], $selarr ) ) {
				$href 		= $hr . '&amp;wppa-tag=' . urlencode( $tag['tag'] ); //str_replace( ' ', '%20', $tag['tag'] );
				$href 		= wppa_encrypt_url( $href );
				$title 		= sprintf( '%d photos - %s%%', $tag['count'], $tag['fraction'] * '100' );
				$name 		= $tag['tag'];
				$size 		= floor( $minsize + $tag['fraction'] * $factor );
				$result    .= 	'<a' .
									' href="' . $href . '"' .
									' title="' . $title . '"' .
									' style="font-size:' . $size . 'px;"' .
									' >' .
									$name .
								'</a> ';
			}
		}
	}

	return $result;
}

// The multitag box
function wppa_multitag_box( $nperline = '2', $seltags = '' ) {

	if ( is_feed() ) return;

	wppa_container( 'open' );

	wppa_out(
		'<div' .
			' id="wppa-multitag-' . wppa( 'mocc' ) . '"' .
			' class="wppa-box wppa-multitag"' .
			' style="' . __wcs( 'wppa-box' ) . __wcs( 'wppa-multitag' ) . '"' .
			' >' .
			wppa_get_multitag_html( $nperline, $seltags ) .
			'<div class="wppa-clear" style="' . __wis( 'clear:both;' ) . '" >' .
			'</div>' .
		'</div>'
	);

	wppa_container( 'close' );
}

// The html for multitag widget
function wppa_get_multitag_html( $nperline = '2', $seltags = '' ) {

	$or_only 	= wppa_switch( 'tags_or_only' );
	$not_on 	= wppa_switch( 'tags_not_on' );
	$page 		= wppa_get_the_landing_page( 	'wppa_multitag_linkpage',
										__( 'Multi Tagged photos' ,'wp-photo-album-plus' )
									);
	$oc 		= wppa_opt( 'multitag_linkpage_oc' );
	$result 	= '';
	if ( $page ) {
		$hr = wppa_get_permalink( $page );
		$hr = str_replace( '&amp;', '&', $hr );
		if ( wppa_opt( 'multitag_linktype' ) == 'album' ) {
			$hr .= 'wppa-album=0&wppa-cover=0&wppa-occur='.$oc;
		}
		if ( wppa_opt( 'multitag_linktype' ) == 'slide' ) {
			$hr .= 'wppa-album=0&wppa-cover=0&wppa-occur='.$oc.'&slide';
		}
	}
	else {
		return __( 'Please select a multitag landing page in Table VI-C4b' , 'wp-photo-album-plus');
	}
	$tags = wppa_get_taglist();

	$result .= '
	<script type="text/javascript">
	function wppaProcessMultiTagRequest() {
	var any = false;
	var url="' . wppa_encrypt_url( $hr ) . '";';

	$result .= '
		if ( jQuery( "#inverse-'.wppa( 'mocc' ).'" ).attr( "checked" ) ) {
			url += "&wppa-inv=1";
		}
		url += "&wppa-tag=";
	';

	if ( $or_only ) {
		$result .= '
		var andor = "or";';
	}
	else {
	$result .= '
		var andor = "and";
			if ( document.getElementById( "andoror-'.wppa( 'mocc' ).'" ).checked ) andor = "or";
		var sep;';
	}

	$result .= '
	if ( andor == "and" ) sep = ","; else sep = ";";
	';

	$selarr = $seltags ? explode( ',', $seltags ) : array();
	if ( $tags ) foreach ( $tags as $tag ) {
		if ( ! $seltags || in_array( $tag['tag'], $selarr ) ) {
			$result .= '
			if ( document.getElementById( "wppa-'.str_replace( ' ', '_', $tag['tag']).'" ).checked ) {' .
				'url+="'.urlencode($tag['tag']).'"+sep;' .
				'any = true;
			}';
		}
	}

	$result .= '
	if ( any ) document.location = url;
	else alert ( "'.__( 'Please check the tag(s) that the photos must have' , 'wp-photo-album-plus').'" );
	}</script>
	';

	$qtag = wppa_get_get( 'tag' );
	$andor = $or_only ? 'or' : 'and'; // default
	if ( strpos( $qtag, ',' ) ) {
		$querystringtags = explode( ',',wppa_get_get( 'tag' ) );
	}
	elseif ( strpos( $qtag, ';' ) ) {
		$querystringtags = explode( ';', wppa_get_get( 'tag' ) );
		$andor = 'or';
	}
	else $querystringtags = wppa_get_get( 'tag' );

	if ( $tags ) {

		if ( ! $or_only || $not_on ) {
			$result .= 	'<table class="wppa-multitag-table">';
							if ( ! $or_only ) {
								$result .=
								'<tr>' .
									'<td>' .
										'<input' .
											' class="radio"' .
											' name="andor-' . wppa( 'mocc' ) . '"' .
											' value="and"' .
											' id="andorand-' . wppa( 'mocc' ) . '"' .
											' type="radio"' .
											( $andor == 'and' ? ' checked="checked"' : '' ) .
										' />' .
										'&nbsp;' . __( 'And', 'wp-photo-album-plus') .
									'</td>' .
									'<td>' .
										'<input' .
											' class="radio"' .
											' name="andor-' . wppa( 'mocc' ) . '"' .
											' value="or"' .
											' id="andoror-' . wppa( 'mocc' ) . '"' .
											' type="radio"' .
											( $andor == 'or' ? ' checked="checked"' : '' ) .
										' />' .
										'&nbsp;' . __( 'Or', 'wp-photo-album-plus' ) .
									'</td>' .
								'</tr>';
							}
							if ( $not_on ) {
								$result .=
								'<tr>' .
									'<td>' .
										'<input' .
											' type="checkbox"' .
											' class="checkbox"' .
											' name="inverse-' . wppa( 'mocc' ) . '"' .
											' id="inverse-' . wppa( 'mocc' ) . '"' .
											( wppa_get_get( 'inv' ) ? ' checked="checked"' : '' ) .
										' />' .
										'&nbsp;' . __( 'Inverse selection', 'wp-photo-album-plus' ) .
									'</td>' .
									'<td>' .
									'</td>' .
								'</tr>';
							}
			$result .= 	'</table><hr style="margin: 3px 0;" />';
		}

		$count 		= '0';
		$checked 	= '';
		$tropen 	= false;

		$result 	.= '<table class="wppa-multitag-table">';

		foreach ( $tags as $tag ) {
			if ( ! $seltags || in_array( $tag['tag'], $selarr ) ) {
				if ( $count % $nperline == '0' ) {
					$result .= '<tr>';
					$tropen = true;
				}
				if ( is_array( $querystringtags ) ) {
					$checked = in_array( $tag['tag'], $querystringtags ) ? 'checked="checked"' : '';
				}
				$result .= 	'<td' .
								' style="'.__wis( 'padding-right:4px;' ).'"' .
								' >' .
								'<input' .
									' type="checkbox"' .
									' id="wppa-'.str_replace( ' ', '_', $tag['tag'] ).'"' .
									' ' . $checked .
								' />' .
								'&nbsp;' . str_replace( ' ', '&nbsp;', $tag['tag'] ) .
							'</td>';
				$count++;
				if ( $count % $nperline == '0' ) {
					$result .= '</tr>';
					$tropen = false;
				}
			}
		}

		if ( $tropen ) {
			while ( $count % $nperline != '0' ) {
				$result .= '<td></td>';
				$count++;
			}
			$result .= '</tr>';
		}
		$result .= '</table>';
		$result .= 	'<input' .
						' type="button"' .
						' onclick="wppaProcessMultiTagRequest()"' .
						' value="' . __( 'Find!', 'wp-photo-album-plus' ) . '"' .
					' />';
	}

	return $result;
}

// Make html for sharebox
function wppa_get_share_html( $id, $key = '', $js = true ) {
global $wppa_locale;

	$do_it = false;
	if ( ! wppa( 'is_slideonly' ) || $key == 'lightbox' ) {
		if ( wppa_switch( 'share_on' ) && ! wppa_in_widget() ) $do_it = true;
		if ( wppa_switch( 'share_on_widget' ) && wppa_in_widget() ) $do_it = true;
		if ( wppa_switch( 'share_on_lightbox' ) ) $do_it = true;
	}
	if ( ! $do_it ) return '';

	// The share url
	if ( wppa_in_widget() ) {
		if ( wppa_opt( 'widget_sm_linktype' ) == 'home' ) {
			$share_url = home_url();
		}
		else {
			$share_url = 	get_permalink(
								wppa_get_the_landing_page( 'wppa_widget_sm_linkpage',
									__( 'Social media landing page' ,'wp-photo-album-plus' )
								)
							);
			$alb = wppa_get_photo_item( $id, 'album' );
			$oc = wppa_opt( 'widget_sm_linkpage_oc' );
			$share_url .= '?wppa-album='.$alb.'&wppa-photo='.$id.'&wppa-cover=0&wppa-occur='.$oc;
			if ( wppa_switch( 'share_single_image' ) ) {
				$share_url .= '&wppa-single=1';
			}
		}
	}
	else {
		$share_url = wppa_get_image_page_url_by_id( $id, wppa_switch( 'share_single_image' ) );
		$share_url = str_replace( '&amp;', '&', $share_url );
	}
	$share_url = wppa_encrypt_url( $share_url );

	// The share title
	$photo_name = wppa_get_photo_name( $id );

	// The share description
	$photo_desc = wppa_html( wppa_get_photo_desc( $id ) );
	$photo_desc = strip_shortcodes( wppa_strip_tags( $photo_desc, 'all' ) );

	// The default description
	$site = str_replace( '&amp;', __( 'and', 'wp-photo-album-plus' ), get_bloginfo( 'name' ) );
	$see_on_site = sprintf( __( 'See this image on %s' ,'wp-photo-album-plus' ), $site );

	// The share image. Must be the fullsize image for facebook.
	// If you take the thumbnail, facebook takes a different image at random.
	$share_img = wppa_get_photo_url( $id );

	// The icon size
	if ( ( wppa_in_widget() && $key != 'lightbox' ) || $key == 'thumb' ) {
		$s = '16';
	}
	else {
		$s = wppa_opt( 'share_size' );
	}

	// qr code
	if ( wppa_switch( 'share_qr' ) && $key != 'thumb' ) {
		$src 	= 	'http://api.qrserver.com/v1/create-qr-code/' .
						'?data=' . urlencode( $share_url ) .
						'&size=80x80' .
						'&color=' . trim( wppa_opt( 'qr_color' ), '#' ) .
						'&bgcolor=' . trim( wppa_opt( 'qr_bgcolor' ), '#' );
		$qr 	= 	'<div style="float:left; padding:2px;" >' .
						'<img' .
							' src="' . $src . '"' .
							' title="' . esc_attr( $share_url ) . '"' .
							' alt="' . __( 'QR code', 'wp-photo-album-plus' ) . '"' .
						' />' .
					'</div>';
	}
	else {
		$qr = '';
	}

	// twitter share button
	if ( wppa_switch( 'share_twitter' ) ) {
		$tweet = urlencode( $see_on_site ) . ': ';
		$tweet_len = strlen( $tweet ) + '1';

		$tweet .= urlencode( $share_url );

		// find first '/' after 'http( s )://' rest doesnt count for twitter chars
		$url_len = strpos( $share_url, '/', 8 ) + 1;
		$tweet_len += ( $url_len > 1 ) ? $url_len : strlen( $share_url );

		$rest_len = 140 - $tweet_len;

		if ( wppa_switch( 'show_full_name' ) ) {
			if ( $rest_len > strlen( $photo_name ) ) {
				$tweet .= ' ' . urlencode( $photo_name );
				$rest_len -= strlen( $photo_name );
				$rest_len -= '2';
			}
			else {
				$tweet .= ' '. urlencode( substr( $photo_name, 0, $rest_len ) ) . '...';
				$rest_len -= strlen( substr( $photo_name, 0, $rest_len ) );
				$rest_len -= '5';
			}
		}

		if ( $photo_desc ) {
			if ( $rest_len > strlen( $photo_desc ) ) {
				$tweet .= ': ' . urlencode( $photo_desc );
			}
			elseif ( $rest_len > 8 ) {
				$tweet .= ': '. urlencode( substr( $photo_desc, 0, $rest_len ) ) . '...';
			}
		}

		$tw = 	'<div style="float:left; padding:0 2px;" >' .
					'<a' .
						' title="' . sprintf( __( 'Tweet %s on Twitter', 'wp-photo-album-plus' ), esc_attr( $photo_name ) ) . '"' .
						' href="https://twitter.com/intent/tweet?text='.$tweet.'"' .
						' target="_blank"' .
						' >' .
						'<img' .
							' src="' . wppa_get_imgdir() . 'twitter.png"' .
							' style="height:' . $s . 'px;vertical-align:top;"' .
							' alt="' . esc_attr( __( 'Share on Twitter', 'wp-photo-album-plus' ) ) . '"' .
						' />' .
					'</a>' .
				'</div>';
	}
	else {
		$tw = '';
	}

	// Google
	if ( wppa_switch( 'share_google' ) ) {
		$go = 	'<div style="float:left; padding:0 2px;" >' .
					'<a' .
						' title="' . sprintf( __( 'Share %s on Google+', 'wp-photo-album-plus' ), esc_attr( $photo_name ) ) . '"' .
						' href="https://plus.google.com/share?url=' . urlencode( $share_url ) . '"' .
						' onclick="javascript:window.open( this.href, \"\", \"menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\" );return false;"' .
						' target="_blank"' .
						' >' .
						'<img' .
							' src="' . wppa_get_imgdir() . 'google.png"' .
							' style="height:' . $s . 'px;vertical-align:top;"' .
							' alt="' . esc_attr( __( 'Share on Google+', 'wp-photo-album-plus' ) ) . '"' .
						' />' .
					'</a>' .
				'</div>';
	}
	else {
		$go = '';
	}

	// Pinterest
	$desc = urlencode( $see_on_site ).': '.urlencode( $photo_desc );
	if ( strlen( $desc ) > 500 ) $desc = substr( $desc, 0, 495 ).'...';
	if ( wppa_switch( 'share_pinterest' ) ) {
		$pi = 	'<div style="float:left; padding:0 2px;" >' .
					'<a' .
						' title="' . sprintf( __( 'Share %s on Pinterest' ,'wp-photo-album-plus' ), esc_attr( $photo_name ) ) . '"' .
						' href="http://pinterest.com/pin/create/button/?url=' . urlencode( $share_url ) .
							'&media=' . urlencode( str_replace( '/thumbs/', '/', $share_img ) ) .
							'&description=' . $desc .
							'"' .
						' target="_blank"' .
						' >' .
						'<img' .
							' src="' . wppa_get_imgdir() . 'pinterest.png" style="height:' . $s . 'px;vertical-align:top;"' .
							' alt="' . esc_attr( __( 'Share on Pinterest', 'wp-photo-album-plus' ) ) . '"' .
						' />' .
					'</a>' .
				'</div>';

	}
	else {
		$pi = '';
	}

	// LinkedIn
	if ( wppa_switch( 'share_linkedin' ) && $key != 'thumb' && $key != 'lightbox' ) {
		$li = 	'<script' .
					' type="text/javascript"' .
					' src="//platform.linkedin.com/in.js"' .
					' >' .
					'lang: ' . $wppa_locale .
				'</script>' .
				'<script' .
					' type="IN/Share"' .
					' data-url="' . urlencode( $share_url ) . '"' .
					' data-counter="top"' .
					' >' .
				'</script>';
		if ( $js ) {
			$li = str_replace( '<', '[', $li );
		}
	}
	else {
		$li = '';
	}

	// Facebook
	$fb = '';
	$need_fb_init = false;
	$small = ( 'thumb' == $key );
	if ( 'lightbox' == $key ) {
		if ( wppa_switch( 'facebook_like' ) && wppa_switch( 'share_facebook' ) ) {
			$lbs = 'max-width:62px; max-height:96px; overflow:show;';
		}
		else {
			$lbs = 'max-width:62px; max-height:64px; overflow:show;';
		}
	}
	else {
		$lbs = '';
	}

	// Share
	if ( wppa_switch( 'share_facebook' ) && ! wppa_switch( 'facebook_like' ) ) {
		if ( $small ) {
			$fb .= 	'<div' .
						' class="fb-share-button"' .
						' style="float:left;"' .
						' data-href="' . $share_url . '"' .
						' data-type="icon"' .
						' >' .
					'</div>';
		}
		else {
			$disp = wppa_opt( 'fb_display' );
			if ( 'standard' == $disp ) {
				$disp = 'button';
			}
			$fb .= 	'<div' .
						' class="fb-share-button"' .
						' style="float:left; '. $lbs . '"' .
						' data-width="200"' .
						' data-href="' . $share_url . '"' .
						' data-type="' . $disp . '"' .
						' >' .
					'</div>';
		}
		$need_fb_init = true;
	}

	// Like
	if ( wppa_switch( 'facebook_like' ) && ! wppa_switch( 'share_facebook' ) ) {
		if ( $small ) {
			$fb .= 	'<div' .
						' class="fb-like"' .
						' style="float:left;"' .
						' data-href="' . $share_url . '"' .
						' data-layout="button"' .
						' >' .
					'</div>';
		}
		else {
			$fb .= 	'<div' .
						' class="fb-like"' .
						' style="float:left; '.$lbs.'"' .
						' data-width="200"' .
						' data-href="' . $share_url . '"' .
						' data-layout="' . wppa_opt( 'fb_display' ) . '"' .
						' >' .
					'</div>';
		}
		$need_fb_init = true;
	}

	// Like and share
	if ( wppa_switch( 'facebook_like' ) && wppa_switch( 'share_facebook' ) ) {
		if ( $small ) {
			$fb .= 	'<div' .
						' class="fb-like"' .
						' style="float:left;"' .
						' data-href="' . $share_url . '"' .
						' data-layout="button"' .
						' data-action="like"' .
						' data-show-faces="false"' .
						' data-share="true"' .
						' >' .
					'</div>';
		}
		else {
			$fb .= 	'<div' .
						' class="fb-like"' .
						' style="float:left; '.$lbs.'"' .
						' data-width="200"' .
						' data-href="' . $share_url . '"' .
						' data-layout="' . wppa_opt( 'fb_display' ) . '"' .
						' data-action="like"' .
						' data-show-faces="false"' .
						' data-share="true"' .
						' >' .
					'</div>';
		}
		$need_fb_init = true;
	}

	// Comments
	if ( wppa_switch( 'facebook_comments' ) && ! wppa_in_widget() && $key != 'thumb' && $key != 'lightbox' ) {
		$width = wppa( 'auto_colwidth' ) ? '100%' : wppa_get_container_width( true );
		if ( wppa_switch( 'facebook_comments' ) ) {
			$fb .= 	'<div style="clear:both;" ></div>' .
					'<div class="wppa-fb-comments-title" style="color:blue;" >' .
						__( 'Comment on Facebook:', 'wp-photo-album-plus' ) .
					'</div>';
			$fb .= 	'<div class="fb-comments" data-href="'.$share_url.'" data-width='.$width.'></div>';
			$need_fb_init = true;
		}
	}

	// Need init?
	if ( $need_fb_init ) {
		if ( $js && $key != 'thumb' ) {
			$fb .= '[script>wppaFbInit();[/script>';
		}
		else {
			$fb .= '<script>wppaFbInit();</script>';
		}
	}

	return $qr.$tw.$go.$pi.$li.$fb.'<div style="clear:both"></div>';

}

// The upload box
function wppa_upload_box() {

	// Init
	$alb = wppa( 'start_album' );

	// Feature enabled?
	if ( ! wppa_switch( 'user_upload_on' ) ) {
		return;
	}

	// Must login ?
	if ( wppa_switch( 'user_upload_login' ) ) {
		if ( ! is_user_logged_in() ) return;
	}

	// Have i access?
	if ( $alb ) {

		// Access to this album ?
		if ( ! wppa_have_access( $alb ) ) return;
	}

	// Do the dirty work
	$create = wppa_get_user_create_html( $alb, wppa_get_container_width( 'netto' ), 'uploadbox' );
	$upload = wppa_get_user_upload_html( $alb, wppa_get_container_width( 'netto' ), 'uploadbox' );

	if ( ! $create && ! $upload ) return; 	// Nothing to do

	// Open container
	wppa_container( 'open' );

	// Open div
	wppa_out( 	'<div' .
					' id="wppa-upload-box-' . wppa( 'mocc' ) . '"' .
					' class="wppa-box wppa-upload"' .
					' style="'.__wcs( 'wppa-box' ).__wcs( 'wppa-upload' ).'"' .
					' >'
			);

	// The html
	wppa_out( $create );
	wppa_out( $upload );

	// Clear and close div
	wppa_out( '<div style="clear:both;"></div></div>' );

	// Close container
	wppa_container( 'close' );
}

// Frontend delete album, for use in the album box
function wppa_user_destroy_html( $alb, $width, $where, $rsp ) {

	// Feature enabled ?
	if ( ! wppa_switch( 'user_destroy_on' ) ) {
		return;
	}

	// Must login ?
	if ( wppa_switch( 'user_create_login' ) ) {
		if ( ! is_user_logged_in() ) return;
	}

	// Album access ?
	if ( ! wppa_have_access( $alb ) ) {
		return;
	}

	// Been naughty ?
	if ( wppa_is_user_blacklisted() ) {
		return;
	}

	// Make the html
	wppa_out(
		'<div style="clear:both">' .
			'<a' .
				' style="float:left; cursor:pointer;"' .
				' onclick="' .
					'jQuery(this).html(\'' . __( 'Working...', 'wp-photo-album-plus' ) . '\');' .
					'wppaAjaxDestroyAlbum(' . $alb . ',\'' . wp_create_nonce( 'wppa_nonce_' . $alb ) . '\');' .
					'jQuery(this).html(\'' . __( 'Delete album', 'wp-photo-album-plus' ) . '\');' .
					'"' .
				' >' .
				__( 'Delete album', 'wp-photo-album-plus' ) .
			'</a>' .
		'</div>'
	);

}

// Frontend create album, for use in the upload box, the widget or in the album and thumbnail box
function wppa_user_create_html( $alb, $width, $where = '', $mcr = false ) {

	wppa_out( wppa_get_user_create_html( $alb, $width, $where, $mcr ) );
}

function wppa_get_user_create_html( $alb, $width, $where = '', $mcr = false ) {

	// Init
	$result = '';
	$mocc 	= wppa( 'mocc' );
	$occur 	= wppa( 'occur' );
	if ( $alb < '0' ) {
		$alb = '0';
	}

	$parent = $alb;
	if ( ! wppa_is_int( $parent ) && wppa_is_enum( $parent ) ) {
		$parent = '0';
	}

	// Feature enabled ?
	if ( ! wppa_switch( 'user_create_on' ) ) {
		return '';
	}

	// Have access?
	if ( $parent && ! wppa_have_access( $parent ) ) {
		return '';
	}

	// Can create album?
	if ( ! $parent && ! wppa_can_create_top_album() ) {
		return '';
	}
	if ( $parent && ! wppa_can_create_album() ) {
		return '';
	}

	if ( ! wppa_user_is( 'administrator' ) && wppa_switch( 'owner_only' ) ) {
		if ( $parent ) {
			$album = wppa_cache_album( $parent );

			// Need to be admin to create public subalbums
			if ( $album['owner'] == '--- public ---' ) return '';
		}
	}

	// In a widget or multi column responsive?
	$small = ( wppa_in_widget() == 'upload' || $mcr );

	// Create the return url
	$returnurl = wppa_get_permalink();
	if ( $where == 'cover' ) {
		$returnurl .= 'wppa-album=' . $parent . '&amp;wppa-cover=0&amp;wppa-occur=' . $occur;
	}
	elseif ( $where == 'thumb' ) {
		$returnurl .= 'wppa-album=' . $parent . '&amp;wppa-cover=0&amp;wppa-occur=' . $occur;
	}
	elseif ( $where == 'widget' || $where == 'uploadbox' ) {
	}
	if ( wppa( 'page' ) ) $returnurl .= '&amp;wppa-page=' . wppa( 'page' );
	$returnurl = trim( $returnurl, '?' );

	$returnurl = wppa_trim_wppa_( $returnurl );

	$t = $mcr ? 'mcr-' : '';

	// The links
	$result .=
		'<div style="clear:both"></div>' .
		'<a' .
			' id="wppa-cr-' . str_replace('.','-',$alb) . '-' . $mocc . '"' .
			' class="wppa-create-' . $where . '"' .
			' onclick="' .
				'jQuery( \'#wppa-create-'.$t.str_replace('.','-',$alb).'-'.$mocc.'\' ).css( \'display\',\'block\' );'.	// Open the Create form
				'jQuery( \'#wppa-cr-'.str_replace('.','-',$alb).'-'.$mocc.'\' ).css( \'display\',\'none\' );'.			// Hide the Create link
				'jQuery( \'#wppa-up-'.str_replace('.','-',$alb).'-'.$mocc.'\' ).css( \'display\',\'none\' );'.			// Hide the Upload link
				'jQuery( \'#wppa-ea-'.str_replace('.','-',$alb).'-'.$mocc.'\' ).css( \'display\',\'none\' );'.			// Hide the Edit link
				'jQuery( \'#wppa-cats-' . str_replace('.','-',$alb) . '-' . $mocc . '\' ).css( \'display\',\'none\' );'.	// Hide catogory
				'jQuery( \'#_wppa-cr-'.str_replace('.','-',$alb).'-'.$mocc.'\' ).css( \'display\',\'block\' );'. 		// Show backlink
				'_wppaDoAutocol( ' . $mocc . ' )'.													// Trigger autocol
				'"' .
			' style="float:left; cursor:pointer;"' .
			'> ' .
			__( 'Create Album', 'wp-photo-album-plus' ) .
		'</a>' .
		'<a' .
			' id="_wppa-cr-' . str_replace('.','-',$alb) . '-' . $mocc . '"' .
			' class="wppa-create-' . $where . '"' .
			' onclick="' .
				'jQuery( \'#wppa-create-'.$t.str_replace('.','-',$alb).'-'.$mocc.'\' ).css( \'display\',\'none\' );'.	// Hide the Create form
				'jQuery( \'#wppa-cr-'.str_replace('.','-',$alb).'-'.$mocc.'\' ).css( \'display\',\'block\' );'.			// Show the Create link
				'jQuery( \'#wppa-up-'.str_replace('.','-',$alb).'-'.$mocc.'\' ).css( \'display\',\'block\' );'.			// Show the Upload link
				'jQuery( \'#wppa-ea-'.str_replace('.','-',$alb).'-'.$mocc.'\' ).css( \'display\',\'block\' );'.			// Show the Edit link
				'jQuery( \'#wppa-cats-' . str_replace('.','-',$alb) . '-' . $mocc . '\' ).css( \'display\',\'block\' );'.	// Show catogory
				'jQuery( \'#_wppa-cr-'.str_replace('.','-',$alb).'-'.$mocc.'\' ).css( \'display\',\'none\' );'. 			// Hide backlink
				'_wppaDoAutocol( ' . $mocc . ' )'.													// Trigger autocol
				'"' .
			' style="float:right; cursor:pointer;display:none;"' .
			' >' .
			__( wppa_opt( 'close_text' ), 'wp-photo-album-plus' ) .
		'</a>';

	// The create form
	$result .=
		'<div' .
			' id="wppa-create-'.$t.str_replace('.','-',$alb).'-'.$mocc.'"' .
			' class="wppa-file-'.$t.$mocc.'"' .
			' style="width:'.$width.'px;text-align:center;display:none;"' .
			' >' .
			'<form' .
				' id="wppa-creform-'.str_replace('.','-',$alb).'-'.$mocc.'"' .
				' action="'.$returnurl.'"' .
				' method="post"' .
				' >' .
				wppa_nonce_field( 'wppa-album-check' , 'wppa-nonce', false, false, $alb ) .
				'<input type="hidden" name="wppa-album-parent" value="'.$parent.'" />' .
				'<input type="hidden" name="wppa-fe-create" value="yes" />' .
				// Name
				'<div'.
					' class="wppa-box-text wppa-td"' .
					' style="' .
						'clear:both;' .
						'float:left;' .
						'text-align:left;' .
						__wcs( 'wppa-box-text' ) .
						__wcs( 'wppa-td' ) .
						'"' .
					' >' .
					__( 'Enter album name.', 'wp-photo-album-plus' ) .
					'&nbsp;<span style="font-size:10px;" >' .
					__( 'Don\'t leave this blank!', 'wp-photo-album-plus' ) . '</span>' .
				'</div>' .
				'<input' .
					' type="text"' .
					' class="wppa-box-text wppa-file-'.$t.$mocc.'"' .
					' style="padding:0; width:'.( $width-6 ).'px; '.__wcs( 'wppa-box-text' ).'"' .
					' name="wppa-album-name"' .
				' />' .
				// Description
				'<div' .
					' class="wppa-box-text wppa-td"' .
					' style="' .
						'clear:both;' .
						'float:left;' .
						'text-align:left;' .
						__wcs( 'wppa-box-text' ) .
						__wcs( 'wppa-td' ) .
						'"' .
					' >' .
					__( 'Enter album description', 'wp-photo-album-plus' ) .
				'</div>' .
				'<textarea' .
					' class="wppa-user-textarea wppa-box-text wppa-file-'.$t.$mocc.'"' .
					' style="padding:0;height:120px; width:'.( $width-6 ).'px; '.__wcs( 'wppa-box-text' ).'"' .
					' name="wppa-album-desc" >' .
				'</textarea>' .
				'<div style="float:left; margin: 6px 0;" >' .
					'<div style="float:left;">' .
						wppa_make_captcha( wppa_get_randseed( 'session' ) ) .
					'</div>' .
					'<input' .
						' type="text"' .
						' id="wppa-captcha-'.$mocc.'"' .
						' name="wppa-captcha"' .
						' style="margin-left: 6px; width:50px; '.__wcs( 'wppa-box-text' ).__wcs( 'wppa-td' ).'"' .
					' />' .
				'</div>' .
				'<input' .
					' type="submit"' .
					' class="wppa-user-submit"' .
					' style="margin: 6px 0; float:right; '.__wcs( 'wppa-box-text' ).'"' .
					' value="' . __( 'Create album', 'wp-photo-album-plus' ) . '"' .
				' />' .
			'</form>' .
		'</div>';

	return $result;
}

// Frontend upload html, for use in the upload box, the widget or in the album and thumbnail box
function wppa_user_upload_html( $alb, $width, $where = '', $mcr = false ) {

	wppa_out( wppa_get_user_upload_html( $alb, $width, $where, $mcr ) );
}

function wppa_get_user_upload_html( $alb, $width, $where = '', $mcr = false ) {
static $seqno;

	// Init
	$result = '';
	$mocc 	= wppa( 'mocc');
	$occur 	= wppa( 'occur' );

	// Using seqno to distinguish from different places within one occurrence because
	// the album no is not known when there is a selection box.
	if ( $seqno ) $seqno++;
	else $seqno = '1';

	// Feature enabled?
	if ( ! wppa_switch( 'user_upload_on' ) ) return '';

	// Login required?
	if ( wppa_switch( 'user_upload_login' ) ) {
		if ( ! is_user_logged_in() ) return '';
	}

	// I should have access to this album ( $alb > 0 ).
	if ( wppa_is_int( $alb ) && $alb > '0' ) {
		$album_owner = wppa_get_album_item( $alb, 'owner' );
		if ( $album_owner != wppa_get_user() && $album_owner != '--- public ---' && ! wppa_have_access( $alb ) ) {
			return '';
		}
	}

	// Any album available for upload? ( $alb = 0 )
	else {
		if ( ! wppa_have_access() ) {
			return '';
		}
	}

	// Find max files for the user
	$allow_me = wppa_allow_user_uploads();
	if ( ! $allow_me ) {
		if ( wppa_switch( 'show_album_full' ) ) {
			$result .= 	'<div style="clear:both"></div>' .
						'<span style="color:red">' .
							__( 'Max uploads reached', 'wp-photo-album-plus' ) .
							wppa_time_to_wait_html( '0', true ) .
						'</span>';
		}
		return $result;
	}

	// Find max files for the album
	$allow_alb = wppa_allow_uploads( $alb );
	if ( ! $allow_alb ) {
		if ( wppa_switch( 'show_album_full' ) ) {
			$result .=	'<div style="clear:both"></div>' .
						'<span style="color:red">' .
							__( 'Max uploads reached', 'wp-photo-album-plus' ) .
							wppa_time_to_wait_html( $alb ) .
						'</span>';
		}
		return $result;
	}

	if ( wppa_is_user_blacklisted() ) return '';

	// Find max files for the system
	$allow_sys = ini_get( 'max_file_uploads' );

	// THE max
	if ( $allow_me == '-1' ) $allow_me = $allow_sys;
	if ( $allow_alb == '-1' ) $allow_alb = $allow_sys;
	$max = min( $allow_me, $allow_alb, $allow_sys );

	// In a widget or multi column responsive?
	$small = ( wppa_in_widget() == 'upload' || $mcr );

	// Ajax upload?
	$ajax_upload = 	wppa_switch( 'ajax_upload' ) &&	wppa_browser_can_html5();

	// Create the return url
	if ( $ajax_upload ) {
		$returnurl = wppa_switch( 'ajax_non_admin' ) ? WPPA_URL.'/wppa-ajax-front.php' : admin_url('admin-ajax.php');
		$returnurl .= '?action=wppa&amp;wppa-action=do-fe-upload';
	}
	else {
		$returnurl = wppa_get_permalink();
		if ( $where == 'cover' ) {
			$returnurl .= 'wppa-album=' . $alb . '&amp;wppa-cover=0&amp;wppa-occur=' . $occur;
		}
		elseif ( $where == 'thumb' ) {
			$returnurl .= 'wppa-album=' . $alb . '&amp;wppa-cover=0&amp;wppa-occur=' . $occur;
		}
		elseif ( $where == 'widget' || $where == 'uploadbox' ) {
		}
		if ( wppa( 'page' ) ) $returnurl .= '&amp;wppa-page=' . wppa( 'page' );
		$returnurl = trim( $returnurl, '?' );

		$returnurl = wppa_trim_wppa_( $returnurl );
	}

	// Make the HTML
	$t = $mcr ? 'mcr-' : '';
	$result .=
		'<div style="clear:both"></div>' .
		'<a' .
			' id="wppa-up-'.str_replace('.','-',$alb).'-'.$mocc.'"' .
			' class="wppa-upload-'.$where.'"' .
			' onclick="' .
				'jQuery( \'#wppa-file-'.$t.str_replace('.','-',$alb).'-'.$mocc.'\' ).css( \'display\',\'block\' );'.		// Open the Upload form
				'jQuery( \'#wppa-up-'.str_replace('.','-',$alb).'-'.$mocc.'\' ).css( \'display\',\'none\' );'.			// Hide the Upload link
				'jQuery( \'#wppa-cr-'.str_replace('.','-',$alb).'-'.$mocc.'\' ).css( \'display\',\'none\' );'.			// Hide the Create link
				'jQuery( \'#wppa-ea-'.str_replace('.','-',$alb).'-'.$mocc.'\' ).css( \'display\',\'none\' );'.			// Hide the Edit link
				'jQuery( \'#wppa-cats-' . str_replace('.','-',$alb) . '-' . $mocc . '\' ).css( \'display\',\'none\' );'.	// Hide catogory
				'jQuery( \'#_wppa-up-'.str_replace('.','-',$alb).'-'.$mocc.'\' ).css( \'display\',\'block\' );'. 		// Show backlink
				'_wppaDoAutocol( ' . $mocc . ' )' .													// Trigger autocol
				'"' .
			' style="float:left; cursor:pointer;' .
			'" >' .
			__( 'Upload Photo', 'wp-photo-album-plus' ) .
		'</a>' .
		'<a' .
			' id="_wppa-up-'.str_replace('.','-',$alb).'-'.$mocc.'"' .
			' class="wppa-upload-'.$where.'"' .
			' onclick="' .
				'jQuery( \'#wppa-file-'.$t.str_replace('.','-',$alb).'-'.$mocc.'\' ).css( \'display\',\'none\' );'.		// Hide the Upload form
				'jQuery( \'#wppa-cr-'.str_replace('.','-',$alb).'-'.$mocc.'\' ).css( \'display\',\'block\' );'.			// Show the Create link
				'jQuery( \'#wppa-up-'.str_replace('.','-',$alb).'-'.$mocc.'\' ).css( \'display\',\'block\' );'.			// Show the Upload link
				'jQuery( \'#wppa-ea-'.str_replace('.','-',$alb).'-'.$mocc.'\' ).css( \'display\',\'block\' );'.			// Show the Edit link
				'jQuery( \'#wppa-cats-' . str_replace('.','-',$alb) . '-' . $mocc . '\' ).css( \'display\',\'block\' );'.	// Show catogory
				'jQuery( \'#_wppa-up-'.str_replace('.','-',$alb).'-'.$mocc.'\' ).css( \'display\',\'none\' );'. 			// Hide backlink
				'_wppaDoAutocol( ' . $mocc . ' )' .													// Trigger autocol
				'"' .
			' style="float:right; cursor:pointer;display:none;' .
			'" >' .
			__( wppa_opt( 'close_text' ), 'wp-photo-album-plus' ) .
		'</a>' .
		'<div' .
			' id="wppa-file-'.$t.str_replace('.','-',$alb).'-'.$mocc.'"' .
			' class="wppa-file-'.$t.$mocc.'"' .
			' style="width:'.$width.'px;text-align:center;display:none; clear:both;"' .
			' >' .
			'<form' .
				' id="wppa-uplform-'.$alb.'-'.$mocc.'"' .
				' action="'.$returnurl.'"' .
				' method="post"' .
				' enctype="multipart/form-data"' .
				' >' .
				wppa_nonce_field( 'wppa-check' , 'wppa-nonce', false, false, $alb );

	// Single Album given
	if ( wppa_is_int( $alb ) && $alb > '0' ) {
		$result .=
			'<input' .
				' type="hidden"' .
				' id="wppa-upload-album-'.$mocc.'-'.$seqno.'"' .
				' name="wppa-upload-album"' .
				' value="'.$alb.'"' .
			' />';
	}

	// If no album given: select one
	else {
		$albarr = explode( '.', wppa_expand_enum( $alb ) );
		$result .=
			'<select' .
				' id="wppa-upload-album-'.$mocc.'-'.$seqno.'"' .
				' name="wppa-upload-album"' .
				' style="float:left; max-width: '.$width.'px;"' .
				' onchange="jQuery( \'#wppa-sel-'.$alb.'-'.$mocc.'\' ).trigger( \'onchange\' )"' .
				' >' .
				wppa_album_select_a( array ( 	'addpleaseselect' 	=> true,
												'checkowner' 		=> true,
												'checkupload' 		=> true,
												'path' 				=> wppa_switch( 'hier_albsel' ),
												'checkarray' 		=> count( $albarr ) > 1,
												'array' 			=> $albarr,
									) ) .
			'</select>' .
			'<br />';
	}

	// One only ?
	if ( wppa_switch( 'upload_one_only' ) && ! wppa_user_is( 'administrator' ) ) {
		$result .=
			'<input' .
				' type="file"' .
				' accept="image/*"' .
				( wppa_switch( 'camera_connect' ) ? ' capture="capture"' : '' ) .
				' class="wppa-user-file"' .
				' style="' .
					'width:auto;' .
					'max-width:' . $width . ';' .
					'margin:6px 0;' .
					'float:left;' .
					__wcs( 'wppa-box-text' ) .
				'"' .
				' id="wppa-user-upload-' . $alb . '-' . $mocc . '"' .
				' name="wppa-user-upload-' . $alb . '-' . $mocc . '[]"' .
				' onchange="jQuery( \'#wppa-user-submit-' . $alb . '-' . $mocc.'\' ).css( \'display\', \'block\' )"' .
			' />';
	}

	// Multiple
	else {
		$result .=
			'<input' .
				' type="file"' .
				' accept="image/*"' .
				( wppa_switch( 'camera_connect' ) ? ' capture="capture"' : '' ) .
				' multiple="multiple"' .
				' class="wppa-user-file"' .
				' style="' .
					'width:auto;' .
					'max-width:' . $width . ';' .
					'margin:6px 0;' .
					'float:left;' .
					__wcs( 'wppa-box-text' ) .
					'"' .
				' id="wppa-user-upload-' . $alb . '-' . $mocc . '"' .
				' name="wppa-user-upload-' . $alb . '-' . $mocc . '[]"' .
				' onchange="jQuery( \'#wppa-user-submit-' . $alb . '-' . $mocc.'\' ).css( \'display\', \'block\' )"' .
			' />';
	}

	// Explanation
	if ( ! wppa_switch( 'upload_one_only' ) && ! wppa_user_is( 'administrator' ) ) {
		if ( $max ) {
			$result .=
				'<span style="font-size:10px;" >' .
					sprintf( _n( 	'You may upload %d photo', 'You may upload up to %d photos at once if your browser supports HTML-5 multiple file upload',
									$max,
									'wp-photo-album-plus' ), $max ) .
				'</span>';
			$maxsize = wppa_check_memory_limit( false );
			if ( is_array( $maxsize ) ) {
				$result .=
					'<br />' .
					'<span style="font-size:10px;" >' .
						sprintf( 	__( 'Max photo size: %d x %d (%2.1f MegaPixel)', 'wp-photo-album-plus' ),
									$maxsize['maxx'], $maxsize['maxy'], $maxsize['maxp']/( 1024*1024 )
								) .
					'</span>';
			}
		}
	}

	// Copyright notice
	if ( wppa_switch( 'copyright_on' ) ) {
		$result .=
			'<div style="clear:both;" >' .
				__( wppa_opt( 'copyright_notice' ), 'wp-photo-album-plus' ) .
			'</div>';
	}

	// Watermark
	if ( wppa_switch( 'watermark_on' ) && ( wppa_switch( 'watermark_user' ) ) ) {
		$result .=
			'<table' .
				' class="wppa-watermark wppa-box-text"' .
				' style="margin:0; border:0; '.__wcs( 'wppa-box-text' ).'"' .
				' >' .
				'<tbody>' .
					'<tr valign="top" style="border: 0 none; " >' .
						'<td' .
							' class="wppa-box-text wppa-td"' .
							' style="'.__wcs( 'wppa-box-text' ).__wcs( 'wppa-td' ).'"' .
							' >' .
							__( 'Apply watermark file:', 'wp-photo-album-plus' ) .
						'</td>' .
					'</tr>' .
					'<tr>' .
						'<td' .
							' class="wppa-box-text wppa-td"' .
							' style="width: '.$width.';'.__wcs( 'wppa-box-text' ).__wcs( 'wppa-td' ).'"' .
							' >' .
							'<select' .
								' style="margin:0; padding:0; text-align:left; width:auto; "' .
								' name="wppa-watermark-file"' .
								' id="wppa-watermark-file"' .
								' >' .
								wppa_watermark_file_select() .
							'</select>' .
						'</td>' .
					'</tr>' .
					'<tr valign="top" style="border: 0 none; " >' .
						'<td' .
							' class="wppa-box-text wppa-td"' .
							' style="width: '.$width.';'.__wcs( 'wppa-box-text' ).__wcs( 'wppa-td' ).'"' .
							' >' .
							__( 'Position:', 'wp-photo-album-plus' ) .
						'</td>' .
						( $small ? '</tr><tr>' : '' ) .
						'<td' .
							' class="wppa-box-text wppa-td"' .
							' style="width: '.$width.';'.__wcs( 'wppa-box-text' ).__wcs( 'wppa-td' ).'"' .
							' >' .
							'<select' .
								' style="margin:0; padding:0; text-align:left; width:auto; "' .
								' name="wppa-watermark-pos"' .
								' id="wppa-watermark-pos"' .
								' >' .
								wppa_watermark_pos_select() .
							'</select>' .
						'</td>' .
					'</tr>' .
				'</tbody>' .
			'</table>';
	}

	// Name
	if ( wppa_switch( 'name_user' ) ) {
		switch ( wppa_opt( 'newphoto_name_method' ) ) {
			case 'none':
				$expl = '';
				break;
			case '2#005':
				$expl =
				__( 'If you leave this blank, iptc tag 005 (Graphic name) will be used as photoname if available, else the original filename will be used as photo name.',
					'wp-photo-album-plus' );
				break;
			case '2#120':
				$expl =
				__( 'If you leave this blank, iptc tag 120 (Caption) will be used as photoname if available, else the original filename will be used as photo name.',
				'wp-photo-album-plus' );
				break;
			default:
				$expl =
				__( 'If you leave this blank, the original filename will be used as photo name.',
				'wp-photo-album-plus' );
		}
		$result .=
			'<div' .
				' class="wppa-box-text wppa-td"' .
				' style="' .
					'clear:both;' .
					'float:left;' .
					'text-align:left;' .
					__wcs( 'wppa-box-text' ) .
					__wcs( 'wppa-td' ) .
					'"' .
				' >' .
				__( 'Enter photo name', 'wp-photo-album-plus' ) . '&nbsp;' .
				'<span style="font-size:10px;" >' .
					$expl .
				'</span>' .
			'</div>' .
			'<input' .
				' type="text"' .
				' class="wppa-box-text wppa-file-'.$t.$mocc.'"' .
				' style="padding:0; width:'.( $width-6 ).'px; '.__wcs( 'wppa-box-text' ).'"' .
				' name="wppa-user-name"' .
			' />';
	}

	// Description user fillable ?
	if ( wppa_switch( 'desc_user' ) ) {
		$desc = wppa_switch( 'apply_newphoto_desc_user' ) ? stripslashes( wppa_opt( 'newphoto_description' ) ) : '';
		$result .=
			'<div' .
				' class="wppa-box-text wppa-td"' .
				' style="clear:both; float:left; text-align:left; '.__wcs( 'wppa-box-text' ).__wcs( 'wppa-td' ).'"' .
				' >' .
				__( 'Enter/modify photo description', 'wp-photo-album-plus' ) .
			'</div>' .
			'<textarea' .
				' class="wppa-user-textarea wppa-box-text wppa-file-'.$t.$mocc.'"' .
				' style="height:120px; width:'.( $width-6 ).'px; '.__wcs( 'wppa-box-text' ).'"' .
				' name="wppa-user-desc"' .
				' >' .
				$desc .
			'</textarea>';
	}

	// Predefined desc ?
	elseif ( wppa_switch( 'apply_newphoto_desc_user' ) ) {
		$result .=
			'<input' .
				' type="hidden"' .
				' value="' . esc_attr( wppa_opt( 'newphoto_description' ) ) . '"' .
				' name="wppa-user-desc"' .
			' />';
	}

	// Custom fields
	if ( wppa_switch( 'fe_custom_fields' ) ) {
		for ( $i = '0'; $i < '10' ; $i++ ) {
			if ( wppa_opt( 'custom_caption_'.$i ) ) {
				$result .=
					'<div' .
						' class="wppa-box-text wppa-td"' .
						' style="clear:both; float:left; text-align:left; '.__wcs( 'wppa-box-text' ).__wcs( 'wppa-td' ).'"' .
						' >'.
							__( wppa_opt( 'custom_caption_'.$i ), 'wp-photo-album-plus' ) . ': ' .
							( wppa_switch( 'custom_visible_'.$i ) ? '' : '&nbsp;<small><i>(&nbsp;'.__( 'hidden', 'wp-photo-album-plus' ).'&nbsp;)</i></small>' ) .
					'</div>' .
					'<input' .
						' type="text"' .
						' class="wppa-box-text wppa-file-'.$t.$mocc.'"' .
						' style="padding:0; width:'.( $width-6 ).'px; '.__wcs( 'wppa-box-text' ).'"' .
						' name="wppa-user-custom-'.$i.'"' .
					' />';
			}
		}
	}

	// Tags
	if ( wppa_switch( 'fe_upload_tags' ) ) {

		// Prepare onclick action
		$onc = 'wppaPrevTags(\'wppa-sel-'.$alb.'-'.$mocc.'\', \'wppa-inp-'.$alb.'-'.$mocc.'\', \'wppa-upload-album-'.$mocc.'-'.$seqno.'\', \'wppa-prev-'.$alb.'-'.$mocc.'\')';

		// Open the tag enter area
		$result .= '<div class="wppa-box-text wppa-td" style="clear:both; float:left; text-align:left; '.__wcs( 'wppa-box-text' ).__wcs( 'wppa-td' ).'" >';

			// Selection boxes 1..3
			for ( $i = '1'; $i < '4'; $i++ ) {
				if ( wppa_switch( 'up_tagselbox_on_'.$i ) ) {
					$result .= 	'<div style="float:left; margin-right:4px;" >' .
								'<small>'.__( wppa_opt( 'up_tagselbox_title_'.$i ) ,'wp-photo-album-plus' ).'</small><br />' .
								'<select' .
									' id="wppa-sel-'.$alb.'-'.$mocc.'-'.$i.'"' .
									' style="float:left; margin-right: 4px;"' .
									' name="wppa-user-tags-'.$i.'[]"' .
									( wppa_switch( 'up_tagselbox_multi_'.$i ) ? ' multiple' : '' ) .
									' onchange="'.$onc.'"' .
									' >';
					if ( wppa_opt( 'up_tagselbox_content_'.$i ) ) {	// List of tags supplied
						$tags = explode( ',', wppa_opt( 'up_tagselbox_content_'.$i ) );
						$result .= '<option value="" >&nbsp;</option>';
						if ( is_array( $tags ) ) foreach ( $tags as $tag ) {
							$result .= '<option class="wppa-sel-'.$alb.'-'.$mocc.'" value="'.urlencode($tag).'">'.$tag.'</option>';
						}
					}
					else {											// All existing tags
						$tags = wppa_get_taglist();
						$result .= '<option value="" >&nbsp;</option>';
						if ( is_array( $tags ) ) foreach ( $tags as $tag ) {
							$result .= '<option class="wppa-sel-'.$alb.'-'.$mocc.'" value="'.urlencode($tag['tag']).'">'.$tag['tag'].'</option>';
						}
					}
					$result .= '</select>' .
						'</div>';
				}
			}

			// New tags
			if ( wppa_switch( 'up_tag_input_on' ) ) {
				$result .= 	'<div style="float:left; margin-right:4px;" >' .
								'<small>'.__( wppa_opt( 'up_tag_input_title' ), 'wp-photo-album-plus' ).'</small><br />' .
								'<input' .
									' id="wppa-inp-'.$alb.'-'.$mocc.'"' .
									' type="text"' .
									' class="wppa-box-text"' .
									' style="padding:0; width:150px; '.__wcs( 'wppa-box-text' ).'"' .
									' name="wppa-new-tags"' .
									' onchange="'.$onc.'"' .
								' />' .
							'</div>';
			}

			// Preview area
			if ( wppa_switch( 'up_tag_preview' ) ) {
				$result .= 	'<div style="margin:0; clear:both;" >' .
								__( 'Preview tags:', 'wp-photo-album-plus' ) .
								' <small id="wppa-prev-'.$alb.'-'.$mocc.'"></small>' .
							'</div>' .
							'<script type="text/javascript" >jQuery( document ).ready(function() {'.$onc.'})</script>';
			}

		// Close tag enter area
		$result .= '</div>';
	}

/* start submit section */

	// Onclick submit verify album is known
	if ( ! $alb ) {
		$onclick = 	' onclick="if ( document.getElementById( \'wppa-upload-album-'.$mocc.'-'.$seqno.'\' ).value == 0 )' .
					' {alert( \''.esc_js( __( 'Please select an album and try again', 'wp-photo-album-plus' ) ).'\' );return false;}"';
	}
	else {
		$onclick = '';
	}

	// The submit button
	$result .=
		'<input' .
			' type="submit"' .
			' id="wppa-user-submit-' . $alb . '-' . $mocc . '"' .
			$onclick .
			' style="display:none; margin: 6px 0; float:right; '.__wcs( 'wppa-box-text' ).'"' .
			' class="wppa-user-submit"' .
			' name="wppa-user-submit-'.$alb.'-'.$mocc.'" value="'.__( 'Upload photo', 'wp-photo-album-plus' ).'"' .
		' />' .
		'<div style="clear:both"></div>';

	// if ajax: progression bar
	if ( $ajax_upload ) {
		$result .=
			'<div' .
				' id="progress-'.$alb.'-'.$mocc.'"' .
				' class="wppa-progress"' .
				' style="border-color:'.wppa_opt( 'bcolor_upload' ).'"' .
				' >' .
				'<div id="bar-'.$alb.'-'.$mocc.'" class="wppa-bar" ></div>' .
				'<div id="percent-'.$alb.'-'.$mocc.'" class="wppa-percent" >0%</div >' .
			'</div>' .
			'<div id="message-'.$alb.'-'.$mocc.'" class="wppa-message" ></div>';
	}

/* End submit section */


	// Done
	$result .= '</form></div>';

	// Ajax upload script
	if ( $ajax_upload ) {
		$result .=
			'<script>' .
				'jQuery(document).ready(function() {

					var options = {
						beforeSend: function() {
							jQuery("#progress-'.$alb.'-'.$mocc.'").show();
							//clear everything
							jQuery("#bar-'.$alb.'-'.$mocc.'").width(\'0%\');
							jQuery("#message-'.$alb.'-'.$mocc.'").html("");
							jQuery("#percent-'.$alb.'-'.$mocc.'").html("");
						},
						uploadProgress: function(event, position, total, percentComplete) {
							jQuery("#bar-'.$alb.'-'.$mocc.'").width(percentComplete+\'%\');
							if ( percentComplete < 95 ) {
								jQuery("#percent-'.$alb.'-'.$mocc.'").html(percentComplete+\'%\');
							}
							else {
								jQuery("#percent-'.$alb.'-'.$mocc.'").html(\'Processing...\');
							}
						},
						success: function() {
							jQuery("#bar-'.$alb.'-'.$mocc.'").width(\'100%\');
							jQuery("#percent-'.$alb.'-'.$mocc.'").html(\'Done!\');
						},
						complete: function(response) {
							jQuery("#message-'.$alb.'-'.$mocc.'").html( \'<span style="font-size: 10px;" >\'+response.responseText+\'</span>\' );'.
							( $where == 'thumb' ? 'document.location.reload(true)' : '' ).'
						},
						error: function() {
							jQuery("#message-'.$alb.'-'.$mocc.'").html( \'<span style="color: red;" >'.__( 'ERROR: unable to upload files.', 'wp-photo-album-plus' ).'</span>\' );
						}
					};

					jQuery("#wppa-uplform-'.$alb.'-'.$mocc.'").ajaxForm(options);
				});
			</script>';
	}

	return $result;
}

// Frontend edit album info
function wppa_user_albumedit_html( $alb, $width, $where = '', $mcr = false ) {

	$album = wppa_cache_album( $alb );

	if ( ! wppa_switch( 'user_album_edit_on' ) ) return; 	// Feature not enabled
	if ( ! $alb ) return;									// No album given
	if ( ! wppa_have_access( $alb ) ) return;				// No rights
	if ( ! is_user_logged_in() ) return;					// Must login
	if ( $album['owner'] == '--- public ---' && ! current_user_can( 'wppa_admin' ) ) return;	// Public albums are not publicly editable

	$t = $mcr ? 'mcr-' : '';

	// Create the return url
	$returnurl = wppa_get_permalink();
	if ( $where == 'cover' ) {
		$returnurl .= 'wppa-album=' . $alb . '&amp;wppa-cover=1&amp;wppa-occur=' . wppa( 'occur' );
	}
	elseif ( $where == 'thumb' ) {
		$returnurl .= 'wppa-album=' . $alb . '&amp;wppa-cover=0&amp;wppa-occur=' . wppa( 'occur' );
	}
	elseif ( $where == 'widget' || $where == 'uploadbox' ) {
	}
	if ( wppa( 'page' ) ) $returnurl .= '&amp;wppa-page=' . wppa( 'page' );
	$returnurl = trim( $returnurl, '?' );

	$returnurl = wppa_encrypt_url( $returnurl );

	$result = '
	<div style="clear:both;"></div>
	<a id="wppa-ea-'.str_replace('.','-',$alb).'-'.wppa( 'mocc' ).'" class="wppa-aedit-'.$where.'" onclick="'.
									'jQuery( \'#wppa-fe-div-'.str_replace('.','-',$alb).'-'.wppa( 'mocc' ).'\' ).css( \'display\',\'block\' );'.		// Open the Edit form
									'jQuery( \'#wppa-ea-'.str_replace('.','-',$alb).'-'.wppa( 'mocc' ).'\' ).css( \'display\',\'none\' );'.			// Hide the Edit link
									'jQuery( \'#wppa-cr-'.str_replace('.','-',$alb).'-'.wppa( 'mocc' ).'\' ).css( \'display\',\'none\' );'.			// Hide the Create libk
									'jQuery( \'#wppa-up-'.str_replace('.','-',$alb).'-'.wppa( 'mocc' ).'\' ).css( \'display\',\'none\' );'.			// Hide the upload link
									'jQuery( \'#wppa-cats-' . str_replace('.','-',$alb) . '-' . wppa( 'mocc' ) . '\' ).css( \'display\',\'none\' );'.	// Hide catogory
									'jQuery( \'#_wppa-ea-'.str_replace('.','-',$alb).'-'.wppa( 'mocc' ).'\' ).css( \'display\',\'block\' );'. 		// Show backlink
									'_wppaDoAutocol( ' . wppa( 'mocc' ) . ' )' .													// Trigger autocol
									'" style="float:left; cursor:pointer;">
		'.__( 'Edit albuminfo', 'wp-photo-album-plus' ).'
	</a>
	<a id="_wppa-ea-'.str_replace('.','-',$alb).'-'.wppa( 'mocc' ).'" class="wppa-aedit-'.$where.'" onclick="'.
									'jQuery( \'#wppa-fe-div-'.str_replace('.','-',$alb).'-'.wppa( 'mocc' ).'\' ).css( \'display\',\'none\' );'.		// Hide the Edit form
									'jQuery( \'#wppa-cr-'.str_replace('.','-',$alb).'-'.wppa( 'mocc' ).'\' ).css( \'display\',\'block\' );'.			// Show the Create link
									'jQuery( \'#wppa-up-'.str_replace('.','-',$alb).'-'.wppa( 'mocc' ).'\' ).css( \'display\',\'block\' );'.			// Show the Upload link
									'jQuery( \'#wppa-ea-'.str_replace('.','-',$alb).'-'.wppa( 'mocc' ).'\' ).css( \'display\',\'block\' );'.			// Show the Edit link
									'jQuery( \'#wppa-cats-' . str_replace('.','-',$alb) . '-' . wppa( 'mocc' ) . '\' ).css( \'display\',\'block\' );'.	// Show catogory
									'jQuery( \'#_wppa-ea-'.str_replace('.','-',$alb).'-'.wppa( 'mocc' ).'\' ).css( \'display\',\'none\' );'. 			// Hide backlink
									'_wppaDoAutocol( ' . wppa( 'mocc' ) . ' )'.													// Trigger autocol
									'" style="float:right; cursor:pointer;display:none;">
		' . __( wppa_opt( 'close_text' ), 'wp-photo-album-plus' ) .
	'</a>';


	// Get name and description, if possible multilanguage editable. ( if qTranslate-x content filter not active )
	$name = stripslashes( $album['name'] );
	$desc = stripslashes( $album['description'] );

	// qTranslate(-x) not active or not properly closed tag?
	if ( substr( $name, -3 ) != '[:]' ) {
		$name = __( $name );
	}

	// qTranslate(-x) not active or not properly closed tag?
	if ( substr( $desc, -3 ) != '[:]' ) {
		$desc = __( $desc );
	}

	// Escape
	$name = esc_attr( $name );
	$desc = esc_textarea( $desc );

	$result .=
	'<div id="wppa-fe-div-'.str_replace('.','-',$alb).'-'.wppa( 'mocc' ).'" style="display:none;" >
		<form action="'.$returnurl.'" method="post">
			<input' .
				' type="hidden"' .
				' name="wppa-albumeditnonce"' .
				' id="album-nonce-'.wppa( 'mocc' ).'-'.$alb.'"' .
				' value="'.wp_create_nonce( 'wppa_nonce_'.$alb ).'"' .
				' />
			<input' .
				' type="hidden"' .
				' name="wppa-albumeditid"' .
				' id="wppaalbum-id-'.wppa( 'mocc' ).'-'.$alb.'"' .
				' value="'.$alb.'"' .
				' />
			<div' .
				' class="wppa-box-text wppa-td"' .
				' style="' .
					'clear:both;' .
					'float:left;' .
					'text-align:left;' .
					__wcs( 'wppa-box-text' ) .
					__wcs( 'wppa-td' ) .
					'"' .
				' >'.
				__( 'Enter album name', 'wp-photo-album-plus' ) . '&nbsp;' .
				'<span style="font-size:10px;" >' .
					__( 'Don\'t leave this blank!', 'wp-photo-album-plus' ) .
				'</span>
			</div>
			<input' .
				' name="wppa-albumeditname"' .
				' id="wppaalbum-name-'.wppa( 'mocc' ).'-'.$alb.'"' .
				' class="wppa-box-text wppa-file-'.$t.wppa( 'mocc' ).'"' .
				' value="' . $name . '"' .
				' style="padding:0; width:'.( $width-6 ).'px; '.__wcs( 'wppa-box-text' ).'"' .
				' />
			<div' .
				' class="wppa-box-text wppa-td"' .
				' style="' .
					'clear:both;' .
					'float:left;' .
					'text-align:left;' .
					__wcs( 'wppa-box-text' ) .
					__wcs( 'wppa-td' ) .
					'"' .
				' >'.
				__( 'Album description:', 'wp-photo-album-plus' ).'
			</div>
			<textarea' .
				' name="wppa-albumeditdesc"' .
				' id="wppaalbum-desc-'.wppa( 'mocc' ).'-'.$alb.'"' .
				' class="wppa-user-textarea wppa-box-text wppa-file-'.$t.wppa( 'mocc' ).'"' .
				' style="' .
					'padding:0;' .
					'height:120px;' .
					'width:' . ( $width-6 ) . 'px;' .
					__wcs( 'wppa-box-text' ) .
					'"' .
				' >' . $desc .
			'</textarea>' .
			'<input' .
				' type="submit"' .
				' name="wppa-albumeditsubmit"' .
				' class="wppa-user-submit"' .
				' style="margin: 6px 0; float:right; '.__wcs( 'wppa-box-text' ).'"' .
				' value="'.__( 'Update album', 'wp-photo-album-plus' ).'"' .
			' />
		</form>
	</div>';
	wppa_out( $result );
}

// Build the html for the comment box
function wppa_comment_html( $id, $comment_allowed ) {
global $wpdb;
global $wppa_first_comment_html;

	$result = '';
	if ( wppa_in_widget() ) return $result;		// NOT in a widget

	// Find out who we are either logged in or not
	$vis = is_user_logged_in() ? 'display:none; ' : '';
	if ( ! $wppa_first_comment_html ) {
		$wppa_first_comment_html = true;

		// Find user
		if ( wppa_get_post( 'comname' ) ) wppa( 'comment_user', wppa_get_post( 'comname' ) );
		if ( wppa_get_post( 'comemail' ) ) wppa( 'comment_email', wppa_get_post( 'comemail' ) );
		elseif ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			wppa( 'comment_user', $current_user->display_name ); //user_login;
			wppa( 'comment_email', $current_user->user_email );
		}
	}

	// Loop the comments already there
	$n_comments = 0;
	if ( wppa_switch( 'comments_desc' ) ) $ord = 'DESC'; else $ord = '';
	$comments = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM '.WPPA_COMMENTS.' WHERE photo = %s ORDER BY id '.$ord, $id ), ARRAY_A );
	wppa_dbg_q( 'Q-Comm' );
	$com_count = count( $comments );
	$color = 'darkgrey';
	if ( wppa_opt( 'fontcolor_box' ) ) $color = wppa_opt( 'fontcolor_box' );
	if ( $comments && ( is_user_logged_in() || ! wppa_switch( 'comment_view_login' ) ) ) {
		$result .= '
			<div' .
			' id="wppa-comtable-wrap-'.wppa( 'mocc' ).'"' .
			' style="display:none;"' .
			'>' .
			'<table' .
				' id="wppacommentstable-' . wppa( 'mocc' ) . '"' .
				' class="wppa-comment-form"' .
				' style="margin:0; "' .
				'>' .
				'<tbody>';

			foreach( $comments as $comment ) {

				// Show a comment either when it is approved, or it is pending and mine or i am a moderator
				if ( $comment['status'] == 'approved' ||
					current_user_can( 'wppa_moderate' ) ||
					current_user_can( 'wppa_comments' ) ||
						( ( $comment['status'] == 'pending' || $comment['status'] == 'spam' ) &&
							$comment['user'] == wppa( 'comment_user' )
						)
					) {
					$n_comments++;
					$result .= 	'
					<tr' .
						' class="wppa-comment-'.$comment['id'].'"' .
						' valign="top"' .
						' style="border-bottom:0 none; border-top:0 none; border-left: 0 none; border-right: 0 none; "' .
						' >' .
						'<td' .
							' valign="top"' .
							' class="wppa-box-text wppa-td"' .
							' style="vertical-align:top; width:30%; border-width: 0 0 0 0; '.__wcs( 'wppa-box-text' ).__wcs( 'wppa-td' ).'"' .
							' >' .
							$comment['user'] . ' ' . __( 'wrote:', 'wp-photo-album-plus' ) .
							'<br />' .
							'<span style="font-size:9px; ">' .
								wppa_get_time_since( $comment['timestamp'] ) .
							'</span>';

							// Avatar ?
							if ( wppa_opt( 'comment_gravatar' ) != 'none' ) {

								// Find the default
								if ( wppa_opt( 'comment_gravatar' ) != 'url' ) {
									$default = wppa_opt( 'comment_gravatar' );
								}
								else {
									$default = wppa_opt( 'comment_gravatar_url' );
								}

								// Find the avatar, init
								$avt = false;
								$usr = false;

								if ( is_user_logged_in() ) {

									// First try to find the user by email address ( works only if email required on comments )
									if ( $comment['email'] ) {
										$usr = get_user_by( 'email', $comment['email'] );
									}

									// If not found, try to find the user by login name ( works only if login name is equal to display name )
									if ( ! $usr ) {
										$usr = get_user_by( 'login', $comment['user'] );
									}

									// Still no user, try to find him by display name
									if ( ! $usr ) {
										$usr = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE `display_name` = %s", $comment['user'] ) );

										// Accept this user if he is the only one with this display name
										if ( count( $usr ) != 1 ) {
											$usr = false;
										}
									}

									// If a user is found, see for local Avatar ?
									if ( $usr ) {
										if ( is_array( $usr ) ) {
											$avt = str_replace( "'", "\"", get_avatar( $usr[0]->ID, wppa_opt( 'gravatar_size' ), $default ) );
										}
										else {
											$avt = str_replace( "'", "\"", get_avatar( $usr->ID, wppa_opt( 'gravatar_size' ), $default ) );
										}
									}
								}

								// Global avatars off ? try myself
								if ( ! $avt ) {
									$avt = 	'
										<img' .
											' class="wppa-box-text wppa-td"' .
											' src="http://www.gravatar.com/avatar/' .
													md5( strtolower( trim( $comment['email'] ) ) ) .
													'.jpg?d='.urlencode( $default ) . '&s=' . wppa_opt( 'gravatar_size' ) . '"' .
											' alt="' . __( 'Avatar', 'wp-photo-album-plus' ) . '"' .
										' />';
								}

								// Compose the html
								$result .= '
									<div class="com_avatar">' .
										$avt .
									'</div>';
							}
						$result .=
						'</td>';

						$txtwidth = floor( wppa_get_container_width() * 0.7 ).'px';
						$result .=
						'<td' .
							' class="wppa-box-text wppa-td"' .
							' style="width:70%; word-wrap:break-word; border-width: 0 0 0 0;'.__wcs( 'wppa-box-text' ).__wcs( 'wppa-td' ).'"' .
							' >'.
							'<p' .
								' class="wppa-comment-textarea wppa-comment-textarea-'.wppa( 'mocc' ).'"' .
								' style="' .
									'margin:0;' .
									'background-color:transparent;' .
									'width:' . $txtwidth . ';' .
									'max-height:90px;' .
									'overflow:auto;' .
									'word-wrap:break-word;' .
									__wcs( 'wppa-box-text' ) .
									__wcs( 'wppa-td' ) .
									'"' .
								' >'.
								html_entity_decode( esc_js( stripslashes( wppa_convert_smilies( $comment['comment'] ) ) ) );

								if ( $comment['status'] != 'approved' && ( current_user_can( 'wppa_moderate' ) || current_user_can( 'wppa_comments' ) ) ) {
									if ( wppa( 'no_esc' ) ) {
										$result .= wppa_moderate_links( 'comment', $id, $comment['id'] );
									}
									else {
										$result .= wppa_html( esc_js( wppa_moderate_links( 'comment', $id, $comment['id'] ) ) );
									}
								}
								elseif ( $comment['status'] == 'pending' && $comment['user'] == wppa( 'comment_user' ) ) {
									$result .= '<br /><span style="color:red; font-size:9px;" >'.__( 'Awaiting moderation', 'wp-photo-album-plus' ).'</span>';
								}
								elseif ( $comment['status'] == 'spam' && $comment['user'] == wppa( 'comment_user' ) ) {
									$result .= '<br /><span style="color:red; font-size:9px;" >'.__( 'Marked as spam', 'wp-photo-album-plus' ).'</span>';
								}

							$result .=
							'</p>' .
						'</td>' .
					'</tr>' .
					'<tr class="wppa-comment-' . $comment['id'] . '">' .
						'<td colspan="2" style="padding:0">' .
							'<hr style="background-color:' . $color . '; margin:0;" />' .
						'</td>' .
					'</tr>';
				}
			}
			$result .=
				'</tbody>' .
			'</table>' .
		'</div>';
	}

	// See if we are currently in the process of adding/editing this comment
	$is_current = ( $id == wppa( 'comment_photo' ) && wppa( 'comment_id' ) );
	if ( $is_current ) {
		$txt = wppa( 'comment_text' );
		$btn = __( 'Edit!', 'wp-photo-album-plus' );
	}
	else {
		$txt = '';
		$btn = __( 'Send!', 'wp-photo-album-plus' );
	}

	// Prepare the callback url
	$returnurl = wppa_get_permalink();

	$album = wppa_get_get( 'album' );
	if ( $album !== false ) $returnurl .= 'wppa-album='.$album.'&';
	$cover = wppa_get_get( 'cover' );
	if ( $cover ) $returnurl .= 'wppa-cover='.$cover.'&';
	$slide = wppa_get_get( 'slide' );
	if ( $slide !== false ) $returnurl .= 'wppa-slide&';
	$occur = wppa_get_get( 'occur' );
	if ( $occur ) $returnurl .= 'wppa-occur='.$occur.'&';
	$lasten = wppa_get_get( 'lasten' );
	if ( $lasten ) $returnurl .= 'wppa-lasten='.$lasten.'&';
	$topten = wppa_get_get( 'topten' );
	if ( $topten ) $returnurl .= 'wppa-topten='.$topten.'&';
	$comten = wppa_get_get( 'comten' );
	if ( $comten ) $returnurl .= 'wppa-comten='.$comten.'&';
	$tag = wppa_get_get( 'tag' );
	if ( $tag ) $returnurl .= 'wppa-tag='.$tag.'&';

	$returnurl .= 'wppa-photo='.$id;

	// The comment form
	if ( $comment_allowed ) {
		$result .=
			'<div' .
				' id="wppa-comform-wrap-' . wppa( 'mocc' ) . '"' .
				' style="display:none;"' .
				' >' .
				'<form' .
					' id="wppa-commentform-' . wppa( 'mocc' ) . '"' .
					' class="wppa-comment-form"' .
					' action="' . $returnurl . '"' .
					' method="post"' .
					' onsubmit="return wppaValidateComment( ' . wppa( 'mocc' ) . ' )"' .
					' >' .
					wp_nonce_field( 'wppa-nonce-' . wppa( 'mocc' ) , 'wppa-nonce-' . wppa( 'mocc' ), false, false ) .
					( $album ? '<input type="hidden" name="wppa-album" value="' . $album . '" />' : '' ) .
					( $cover ? '<input type="hidden" name="wppa-cover" value="' . $cover . '" />' : '' ) .
					( $slide ? '<input type="hidden" name="wppa-slide" value="' . $slide . '" />' : '' ) .
					'<input' .
						' type="hidden"' .
						' name="wppa-returnurl"' .
						' id="wppa-returnurl-' . wppa( 'mocc' ) . '"' .
						' value="' . $returnurl . '"' .
					' />' .
					( $is_current ? '<input' .
										' type="hidden"' .
										' id="wppa-comment-edit-' . wppa( 'mocc' ) . '"' .
										' name="wppa-comment-edit"' .
										' value="' . wppa( 'comment_id' ) . '"' .
									' />' : '' ) .
					'<input type="hidden" name="wppa-occur" value="'.wppa( 'occur' ).'" />' .

					'<table id="wppacommenttable-'.wppa( 'mocc' ).'" style="margin:0;">' .
						'<tbody>' .
							'<tr valign="top" style="' . $vis . '">' .
								'<td class="wppa-box-text wppa-td" style="width:30%;background-color:transparent;'.__wcs( 'wppa-box-text' ).__wcs( 'wppa-td' ).'" >' .
									__( 'Your name:', 'wp-photo-album-plus' ) .
								'</td>' .
								'<td class="wppa-box-text wppa-td" style="width:70%;background-color:transparent;'.__wcs( 'wppa-box-text' ).__wcs( 'wppa-td' ).'" >' .
									'<input' .
										' type="text"' .
										' name="wppa-comname"' .
										' id="wppa-comname-' . wppa( 'mocc' ) . '"' .
										' style="width:100%; " value="' . wppa( 'comment_user' ) . '"' .
									' />' .
								'</td>' .
							'</tr>';

				if ( wppa_opt( 'comment_email_required' ) != 'none' ) {
				$result .= 	'<tr valign="top" style="'.$vis.'">' .
								'<td class="wppa-box-text wppa-td" style="width:30%;background-color:transparent;'.__wcs( 'wppa-box-text' ).__wcs( 'wppa-td' ).'" >' .
									__( 'Your email:', 'wp-photo-album-plus' ) .
								'</td>' .
								'<td class="wppa-box-text wppa-td" style="width:70%;background-color:transparent;'.__wcs( 'wppa-box-text' ).__wcs( 'wppa-td' ).'" >' .
									'<input' .
										' type="text"' .
										' name="wppa-comemail"' .
										' id="wppa-comemail-' . wppa( 'mocc' ) . '"' .
										' style="width:100%;"' .
										' value="' . wppa( 'comment_email' ) . '"' .
									' />' .
								'</td>' .
							'</tr>';
				}

				$result .= 	'<tr valign="top" style="vertical-align:top;">' .
								'<td valign="top" class="wppa-box-text wppa-td" style="vertical-align:top; width:30%;background-color:transparent;'.__wcs( 'wppa-box-text' ).__wcs( 'wppa-td' ).'" >' .
									__( 'Your comment:', 'wp-photo-album-plus' ) . '<br />' . wppa( 'comment_user' ) . '<br />';
					if ( ( is_user_logged_in() && wppa_opt( 'comment_captcha' ) == 'all' ) ||
						 ( ! is_user_logged_in() && wppa_opt( 'comment_captcha' ) != 'none' ) )	{
						$wid = '20%';
						if ( wppa_opt( 'fontsize_box' ) ) $wid = ( wppa_opt( 'fontsize_box' ) * 1.5 ).'px';
						$captkey = $id;
						if ( $is_current ) $captkey = $wpdb->get_var( $wpdb->prepare( 'SELECT `timestamp` FROM `'.WPPA_COMMENTS.'` WHERE `id` = %s', wppa( 'comment_id' ) ) );
						wppa_dbg_q( 'Q-Com-ts' );
						$result .=
									wppa_make_captcha( $captkey ) .
									'<input' .
										' type="text"' .
										' id="wppa-captcha-' . wppa( 'mocc' ) . '"' .
										' name="wppa-captcha"' .
										' style="width:' . $wid . ';' . __wcs( 'wppa-box-text' ) . __wcs( 'wppa-td' ) . '"' .
									' />&nbsp;';
					}

				$result .=
								'<input type="button" name="commentbtn" onclick="wppaAjaxComment( '.wppa( 'mocc' ).', '.$id.' )" value="'.$btn.'" style="margin:0 4px 0 0;" />' .
								'<img id="wppa-comment-spin-'.wppa( 'mocc' ).'" src="'.wppa_get_imgdir().'wpspin.gif" style="display:none;" />' .
							'</td>' .
							'<td valign="top" class="wppa-box-text wppa-td" style="vertical-align:top; width:70%;background-color:transparent;'.__wcs( 'wppa-box-text' ).__wcs( 'wppa-td' ).'" >';

				if ( wppa_switch( 'comment_smiley_picker' ) ) {
					$result .= wppa_get_smiley_picker_html( 'wppa-comment-'.wppa( 'mocc' ) );
				}

					$result .=
									'<textarea' .
										' name="wppa-comment"' .
										' id="wppa-comment-' . wppa( 'mocc' ) . '"' .
										' style="height:60px; width:100%; "' .
										'>' .
										esc_textarea( stripslashes( $txt ) ) .
									'</textarea>' .
								'</td>' .
							'</tr>' .
						'</tbody>' .
					'</table>' .
				'</form>' .
			'</div>';
	}
	else {
		if ( wppa_switch( 'login_links' ) ) {
			$result .= sprintf( __( 'You must <a href="%s">login</a> to enter a comment', 'wp-photo-album-plus' ), site_url( 'wp-login.php', 'login' ) );
		}
		else {
			$result .= __( 'You must login to enter a comment', 'wp-photo-album-plus' );
		}
	}

	$result .=
			'<div id="wppa-comfooter-wrap-'.wppa( 'mocc' ).'" style="display:block;" >' .
				'<table id="wppacommentfooter-'.wppa( 'mocc' ).'" class="wppa-comment-form" style="margin:0;">' .
					'<tbody>' .
						'<tr style="text-align:center;">' .
							'<td style="text-align:center; cursor:pointer;'.__wcs( 'wppa-box-text' ).'" >' .
								'<a onclick="wppaOpenComments( '.wppa( 'mocc' ).', -1 ); return false;" >';
			if ( $n_comments ) {
				$result .= sprintf( _n( '%d comment', '%d comments', $n_comments, 'wp-photo-album-plus' ), $n_comments );
			}
			else {
				if ( $comment_allowed ) {
					$result .= __( 'Leave a comment', 'wp-photo-album-plus' );
				}
			}
		$result .=
								'</a>' .
							'</td>' .
						'</tr>' .
					'</tbody>' .
				'</table>' .
			'</div>' .
			'<div style="clear:both"></div>';

	return $result;
}

// The smiley picker for the comment box
function wppa_get_smiley_picker_html( $elm_id ) {
static $wppa_smilies;
global $wpsmiliestrans;

	// Fill inverted smilies array if needed
	if ( ! is_array( $wppa_smilies ) ) {
		if ( is_array( $wpsmiliestrans ) ) {
			foreach( array_keys( $wpsmiliestrans ) as $idx ) {
				if ( ! isset ( $wppa_smilies[$wpsmiliestrans[$idx]] ) ) {
					$wppa_smilies[$wpsmiliestrans[$idx]] = $idx;
				}
			}
		}
	}

	// Make the html
	$result = '';
	if ( is_array( $wppa_smilies ) ) {
		foreach ( array_keys( $wppa_smilies ) as $key ) {
			$onclick 	= esc_attr( 'wppaInsertAtCursor( document.getElementById( "' . $elm_id . '" ), " ' . $wppa_smilies[$key] . ' " )' );
			$title 		= trim( $wppa_smilies[$key], ':' );
			$result 	.= 	'<a onclick="'.$onclick.'" title="'.$title.'" >';
			$result 	.= 		wppa_convert_smilies( $wppa_smilies[$key] );
			$result 	.= 	'</a>';
		}
	}
	else {
//		$result .= __( 'Smilies are not available', 'wp-photo-album-plus' );
	}

	return $result;
}

// IPTC box
function wppa_iptc_html( $photo ) {
global $wpdb;
global $wppaiptcdefaults;
global $wppaiptclabels;

	// Get the default ( one time only )
	if ( ! wppa( 'iptc' ) ) {
		$tmp = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `".WPPA_IPTC."` WHERE `photo` = %s ORDER BY `tag`", '0' ), "ARRAY_A" );
		wppa_dbg_q( 'Q-iptc0' );
		if ( ! $tmp ) return '';	// Nothing defined
		$wppaiptcdefaults = false;	// Init
		$wppaiptclabels = false;	// Init
		foreach ( $tmp as $t ) {
			$wppaiptcdefaults[$t['tag']] = $t['status'];
			$wppaiptclabels[$t['tag']] = $t['description'];
		}
		wppa( 'iptc', true );
	}

	$count = 0;

	// Get the photo data
	$iptcdata = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `".WPPA_IPTC."` WHERE `photo` = %s ORDER BY `tag`", $photo ), "ARRAY_A" );
	wppa_dbg_q( 'Q-iptc' );
	if ( $iptcdata ) {

		// Open the container content
		$result = '<div id="iptccontent-'.wppa( 'mocc' ).'" >';

		// Open or closed?
		$d1 = wppa_switch( 'show_iptc_open' ) ? 'display:none;' : 'display:inline;';
		$d2 = wppa_switch( 'show_iptc_open' ) ? 'display:inline;' : 'display:none;';

		// Process data
		$onclick = 	'wppaStopShow( ' . wppa( 'mocc' ) . ' );' .
					'jQuery( \'.wppa-iptc-table-' . wppa( 'mocc' ) . '\' ).css( \'display\', \'\' );' .
					'jQuery( \'.-wppa-iptc-table-' . wppa( 'mocc' ). '\' ).css( \'display\', \'none\' );';

		$result .= 	'<a' .
						' class="-wppa-iptc-table-' . wppa( 'mocc' ) . '"' .
						' onclick="' . esc_attr( $onclick ) . '"' .
						' style="cursor:pointer;' . $d1 . '"' .
						' >' .
						__( 'Show IPTC data', 'wp-photo-album-plus' ) .
					'</a>';

		$onclick = 	'jQuery( \'.wppa-iptc-table-' . wppa( 'mocc' ) . '\' ).css( \'display\', \'none\' );' .
					'jQuery( \'.-wppa-iptc-table-' . wppa( 'mocc' ) . '\' ).css( \'display\', \'\' );';

		$result .= 	'<a' .
						' class="wppa-iptc-table-switch wppa-iptc-table-' . wppa( 'mocc' ) . '"' .
						' onclick="'.esc_attr($onclick).'"' .
						' style="cursor:pointer;'.$d2.'"' .
						' >' .
						__( 'Hide IPTC data', 'wp-photo-album-plus' ) .
					'</a>';

		$result .=
				'<div style="clear:both;" ></div>' .
					'<table class="wppa-iptc-table-'.wppa( 'mocc' ).' wppa-detail" style="border:0 none; margin:0;'.$d2.'" >' .
						'<tbody>';
		$oldtag = '';
		foreach ( $iptcdata as $iptcline ) {
			if ( ! isset( $wppaiptcdefaults[$iptcline['tag']] ) ) continue;

			// Photo status is hide ?
			if ( $iptcline['status'] == 'hide' ) continue;

			// P s is default and default is hide?
			if ( $iptcline['status'] == 'default' && $wppaiptcdefaults[$iptcline['tag']] == 'hide' ) continue;

			// P s is default and default is optional and field is empty ?
			if ( $iptcline['status'] == 'default' && $wppaiptcdefaults[$iptcline['tag']] == 'option' && ! trim( $iptcline['description'], "\x00..\x1F " ) ) continue;

			$count++;
			$newtag = $iptcline['tag'];
			if ( $newtag != $oldtag && $oldtag != '' ) $result .= '</td></tr>';	// Close previous line
			if ( $newtag == $oldtag ) {
				$result .= '; ';							// next item with same tag
			}
			else {
				$result .= 	'<tr style="border-bottom:0 none; border-top:0 none; border-left: 0 none; border-right: 0 none; ">' .
								'<td class="wppa-iptc-label wppa-box-text wppa-td" style="'.__wcs( 'wppa-box-text' ).__wcs( 'wppa-td' ).'" >';
				$result .= esc_js( __( $wppaiptclabels[$newtag], 'wp-photo-album-plus' ) );
				$result .= 		'</td>' .
								'<td class="wppa-iptc-value wppa-box-text wppa-td" style="'.__wcs( 'wppa-box-text' ).__wcs( 'wppa-td' ).'" >';
			}
			$result .= esc_js( wppa_sanitize_text( __( $iptcline['description'], 'wp-photo-album-plus' ) ) );
			$oldtag = $newtag;
		}
		if ( $oldtag != '' ) $result .= '</td></tr>';	// Close last line
		$result .= '</tbody></table></div>';
	}
	if ( ! $count ) {
		$result = '<div id="iptccontent-'.wppa( 'mocc' ).'" >'.__( 'No IPTC data', 'wp-photo-album-plus' ).'</div>';
	}

	return ( $result );
}

// EXIF box
function wppa_exif_html( $photo ) {
global $wpdb;
global $wppaexifdefaults;
global $wppaexiflabels;

	// Get the default ( one time only )
	if ( ! wppa( 'exif' ) ) {
		$tmp = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `".WPPA_EXIF."` WHERE `photo` = %s ORDER BY `tag`", '0' ), "ARRAY_A" );
		wppa_dbg_q( 'Q-exif0' );
		if ( ! $tmp ) return '';	// Nothing defined
		$wppaexifdefaults = false;	// Init
		$wppaexiflabels = false;	// Init
		foreach ( $tmp as $t ) {
			$wppaexifdefaults[$t['tag']] = $t['status'];
			$wppaexiflabels[$t['tag']] = $t['description'];
		}
		wppa( 'exif', true );
	}

	$count = 0;

	// Get the photo data
	$exifdata = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `".WPPA_EXIF."` WHERE `photo`=%s ORDER BY `tag`", $photo ), "ARRAY_A" );
	wppa_dbg_q( 'Q-exif' );
	if ( $exifdata ) {
		// Open the container content
		$result = '<div id="exifcontent-'.wppa( 'mocc' ).'" >';
		// Open or closed?
		$d1 = wppa_switch( 'show_exif_open' ) ? 'display:none;' : 'display:inline;';
		$d2 = wppa_switch( 'show_exif_open' ) ? 'display:inline;' : 'display:none;';
		// Process data
		$onclick = 	'wppaStopShow( ' . wppa( 'mocc' ) . ' );' .
					'jQuery( \'.wppa-exif-table-' . wppa( 'mocc' ) . '\' ).css( \'display\', \'\' );' .
					'jQuery( \'.-wppa-exif-table-' . wppa( 'mocc' ) . '\' ).css( \'display\', \'none\' );';

		$result .= 	'<a' .
						' class="-wppa-exif-table-' . wppa( 'mocc' ) . '"' .
						' onclick="' . esc_attr( $onclick ) . '"' .
						' style="cursor:pointer;' . $d1 . '"' .
						' >' .
						__( 'Show EXIF data', 'wp-photo-album-plus' ) .
					'</a>';

		$onclick = 	'jQuery( \'.wppa-exif-table-' . wppa( 'mocc' ) . '\' ).css( \'display\', \'none\' );' .
					'jQuery( \'.-wppa-exif-table-' . wppa( 'mocc' ) . '\' ).css( \'display\', \'\' )';

		$result .= 	'<a' .
						' class="wppa-exif-table-switch wppa-exif-table-' . wppa( 'mocc' ) . '"' .
						' onclick="' . esc_attr( $onclick ) . '"' .
						' style="cursor:pointer;' . $d2 . '"' .
						' >' .
						__( 'Hide EXIF data', 'wp-photo-album-plus' ) .
					'</a>';

		$result .= 	'<div style="clear:both;" ></div>' .
					'<table' .
						' class="wppa-exif-table-'.wppa( 'mocc' ).' wppa-detail"' .
						' style="'.$d2.' border:0 none; margin:0;"' .
						' >' .
						'<tbody>';
		$oldtag = '';
		foreach ( $exifdata as $exifline ) {
			if ( ! isset( $wppaexifdefaults[$exifline['tag']] ) ) continue;
			$exifline['description'] = trim( $exifline['description'], "\x00..\x1F " );

			// Photo status is hide ?
			if ( $exifline['status'] == 'hide' ) continue;

			// P s is default and default is hide
			if ( $exifline['status'] == 'default' && $wppaexifdefaults[$exifline['tag']] == 'hide' ) continue;

			// P s is default and default is optional and field is empty
			if ( $exifline['status'] == 'default' && $wppaexifdefaults[$exifline['tag']] == 'option' && ! $exifline['description'] ) continue;

			$count++;
			$newtag = $exifline['tag'];
			if ( $newtag != $oldtag && $oldtag != '' ) $result .= '</td></tr>';	// Close previous line
			if ( $newtag == $oldtag ) {
				$result .= '; ';							// next item with same tag
			}
			else {
				$result .= 	'<tr style="border-bottom:0 none; border-top:0 none; border-left: 0 none; border-right: 0 none;" >' .
							'<td class="wppa-exif-label wppa-box-text wppa-td" style="'.__wcs( 'wppa-box-text' ).__wcs( 'wppa-td' ).'" >';
				$result .= esc_js( __( $wppaexiflabels[$newtag], 'wp-photo-album-plus' ) );
				$result .= '</td><td class="wppa-exif-value wppa-box-text wppa-td" style="'.__wcs( 'wppa-box-text' ).__wcs( 'wppa-td' ).'" >';
			}
			$result .= esc_js( wppa_sanitize_text( __( wppa_format_exif( $exifline['tag'], $exifline['description'] ), 'wp-photo-album-plus' ) ) );
			$oldtag = $newtag;
		}
		if ( $oldtag != '' ) $result .= '</td></tr>';	// Close last line
		$result .= '</tbody></table></div>';
	}
	if ( ! $count ) {
		$result = '<div id="exifcontent-'.wppa( 'mocc' ).'" >'.__( 'No EXIF data', 'wp-photo-album-plus' ).'</div>';
	}

	return ( $result );
}

// Display the album name ( on a thumbnail display ) either on top or at the bottom of the thumbnail area
function wppa_album_name( $key ) {

	// Virtual albums have no name
	if ( wppa_is_virtual() ) return;

	// Album enumerations have no name
	if ( strlen( wppa( 'start_album' ) ) > '0' && ! wppa_is_int( wppa( 'start_album' ) ) ) return;

	$result = '';
	if ( wppa_opt( 'albname_on_thumbarea' ) == $key && wppa( 'start_album' ) ) {
		$name = wppa_get_album_name( wppa( 'start_album' ) );
		if ( $key == 'top' ) {
			$result .= 	'<h3' .
							' id="wppa-albname-' . wppa( 'mocc' ) . '"' .
							' class="wppa-box-text wppa-black"' .
							' style="padding-right:6px; margin:0; ' . __wcs( 'wppa-box-text' ) . __wcs( 'wppa-black' ) . '"' .
							' >' .
							$name .
						'</h3>' .
						'<div style="clear:both" ></div>';
		}
		if ( $key == 'bottom' ) {
			$result .= 	'<h3' .
							' id="wppa-albname-b-' . wppa( 'mocc' ) . '"' .
							' class="wppa-box-text wppa-black"' .
							' style="clear:both; padding-right:6px; margin:0; ' . __wcs( 'wppa-box-text' ) . __wcs( 'wppa-black' ) . '"' .
							' >' .
							$name .
						'</h3>';
		}
	}

	wppa_out( $result );
}

// Display the album description ( on a thumbnail display ) either on top or at the bottom of the thumbnail area
function wppa_album_desc( $key ) {

	// Virtual albums have no name
	if ( wppa_is_virtual() ) return;

	// Album enumerations have no name
	if ( strlen( wppa( 'start_album' ) ) > '0' && ! wppa_is_int( wppa( 'start_album' ) ) ) return;

	$result = '';
	if ( wppa_opt( 'albdesc_on_thumbarea' ) == $key && wppa( 'start_album' ) ) {
		$desc = wppa_get_album_desc( wppa( 'start_album' ) );
		if ( $key == 'top' ) {
			$result .= 	'<div' .
							' id="wppa-albdesc-'.wppa( 'mocc' ).'"' .
							' class="wppa-box-text wppa-black"' .
							' style="padding-right:6px;'.__wcs( 'wppa-box-text' ).__wcs( 'wppa-black' ).'"' .
							' >' .
							$desc .
						'</div>' .
						'<div style="clear:both" ></div>';
		}
		if ( $key == 'bottom' ) {
			$result .= 	'<div' .
							' id="wppa-albdesc-b-'.wppa( 'mocc' ).'"' .
							' class="wppa-box-text wppa-black"' .
							' style="clear:both; padding-right:6px;'.__wcs( 'wppa-box-text' ).__wcs( 'wppa-black' ).'"' .
							' >' .
							$desc .
						'</div>';
		}
	}

	wppa_out( $result );
}

// The auto page links
function wppa_auto_page_links( $where ) {
global $wpdb;

	$m = $where == 'bottom' ? 'margin-top:8px;' : '';
	$mustwhere = wppa_opt( 'auto_page_links' );
	if ( ( $mustwhere == 'top' || $mustwhere == 'both' ) && ( $where == 'top' ) || ( ( $mustwhere == 'bottom' || $mustwhere == 'both' ) && ( $where == 'bottom' ) ) ) {
		wppa_out( '
			<div' .
				' id="prevnext1-'.wppa( 'mocc' ).'"' .
				' class="wppa-box wppa-nav wppa-nav-text"' .
				' style="text-align: center; '.__wcs( 'wppa-box' ).__wcs( 'wppa-nav' ).__wcs( 'wppa-nav-text' ).$m.'"' .
				' >' );

		$photo = wppa( 'single_photo' );
		$thumb = wppa_cache_thumb( $photo );
		$album = $thumb['album'];
		$photos = $wpdb->get_results( $wpdb->prepare( "SELECT `id`, `page_id` FROM `".WPPA_PHOTOS."` WHERE `album` = %s ".wppa_get_photo_order( $album ), $album ), ARRAY_A );
		wppa_dbg_q( 'Q-Ppag' );
		$prevpag = '0';
		$nextpag = '0';
		$curpag  = get_the_ID();
		$count = count( $photos );
		$count_ = $count - 1;
		$current = '0';
		if ( $photos ) {
			foreach ( array_keys( $photos ) as $idx ) {
				if ( $photos[$idx]['page_id'] == $curpag ) {
					if ( $idx != '0' ) $prevpag = wppa_get_the_auto_page( $photos[$idx-1]['id'] ); // ['page_id'];
					if ( $idx != $count_ ) $nextpag = wppa_get_the_auto_page( $photos[$idx+1]['id'] ); // ['page_id'];
					$current = $idx;
				}
			}
		}

		if ( $prevpag ) {
			wppa_out(	'<a href="'.get_permalink( $prevpag ).'" style="float:left" >' .
							__( '< Previous', 'wp-photo-album-plus' ) .
						'</a>' );
		}
		else {
			wppa_out( 	'<span style="visibility:hidden" >' .
							__( '< Previous', 'wp-photo-album-plus' ) .
						'</span>' );
		}
		wppa_out( ++$current.'/'.$count );
		if ( $nextpag ) {
			wppa_out( 	'<a href="'.get_permalink( $nextpag ).'" style="float:right" >' .
							__( 'Next >', 'wp-photo-album-plus' ) .
						'</a>' );
		}
		else {
			wppa_out( 	'<span style="visibility:hidden" >' .
							__( 'Next >', 'wp-photo-album-plus' ) .
						'</span>' );
		}

		wppa_out( '</div><div style="clear:both"></div>' );
	}
}

// The bestof box
function wppa_bestof_box ( $args ) {

	wppa_container ( 'open' );
	wppa_out( 	'<div' .
					' id="wppa-bestof-' . wppa( 'mocc' ) . '"' .
					' class="wppa-box wppa-bestof"' .
					' style="' . __wcs( 'wppa-box' ) . __wcs( 'wppa-bestof' ) . '"' .
					'>' .
					wppa_bestof_html( $args, false ) .
					'<div style="clear:both; height:4px;">' .
					'</div>' .
				'</div>'
			);
	wppa_container ( 'close' );
}

// The Bestof html
function wppa_bestof_html( $args, $widget = true ) {

	// Copletify args
	$args = wp_parse_args( ( array ) $args, array( 	'page' 			=> '0',
													'count' 		=> '1',
													'sortby' 		=> 'maxratingcount',
													'display' 		=> 'photo',
													'period' 		=> 'thisweek',
													'maxratings'	=> 'yes',
													'meanrat' 		=> 'yes',
													'ratcount' 		=> 'yes',
													'linktype' 		=> 'none',
													'size' 			=> wppa_opt( 'widget_width' ),
													'fontsize' 		=> wppa_opt( 'fontsize_widget_thumb' ),
													'lineheight' 	=> wppa_opt( 'fontsize_widget_thumb' ) * 1.5,
													'height' 		=> '200'
											 ) );

	// Make args into seperate vars
	extract ( $args );

	// Validate args
	if ( ! in_array( $sortby, array ( 'maxratingcount', 'meanrating', 'ratingcount' ) ) ) {
		wppa_dbg_msg ( 'Invalid arg sortby "'.$sortby.'" must be "maxratingcount", "meanrating" or "ratingcount"', 'red', 'force' );
	}
	if ( ! in_array( $display, array ( 'photo', 'owner' ) ) ) {
		wppa_dbg_msg ( 'Invalid arg display "'.$display.'" must be "photo" or "owner"', 'red', 'force' );
	}
	if ( ! in_array( $period, array ( 'lastweek', 'thisweek', 'lastmonth', 'thismonth', 'lastyear', 'thisyear' ) ) ) {
		wppa_dbg_msg ( 'Invalid arg period "'.$period.'" must be "lastweek", "thisweek", "lastmonth", "thismonth", "lastyear" or "thisyear"', 'red', 'force' );
	}
	if ( ! $widget ) $size = $height;

	$result = '';

	$data = wppa_get_the_bestof( $count, $period, $sortby, $display );

	if ( $display == 'photo' ) {
		if ( is_array( $data ) ) {
			foreach ( array_keys( $data ) as $id ) {
				$thumb = wppa_cache_thumb( $id );
				if ( $thumb ) {
					$imgsize		= array( wppa_get_photox( $id ), wppa_get_photoy( $id ) );
					if ( $widget ) {
						$maxw 		= $size;
						$maxh 		= round ( $maxw * $imgsize['1'] / $imgsize['0'] );
					}
					else {
						$maxh 		= $size;
						$maxw 		= round ( $maxh * $imgsize['0'] / $imgsize['1'] );
					}
					$totalh 		= $maxh + $lineheight;
					if ( $maxratings == 'yes' ) $totalh += $lineheight;
					if ( $meanrat == 'yes' ) 	$totalh += $lineheight;
					if ( $ratcount == 'yes' ) 	$totalh += $lineheight;

					if ( $widget ) $clear = 'clear:both; '; else $clear = '';
					$result .= "\n" .
								'<div' .
									' class="wppa-widget"' .
									' style="'.$clear.'width:'.$maxw.'px; height:'.$totalh.'px; margin:4px; display:inline; text-align:center; float:left;"'.
									' >';

						// The medal if at the top
						$result .= wppa_get_medal_html_a( array( 'id' => $id, 'size' => 'M', 'where' => 'top' ) );

						// The link if any
						if ( $linktype != 'none' ) {
							switch ( $linktype ) {
								case 'owneralbums':
									$href = wppa_get_permalink( $page ).'wppa-cover=1&amp;wppa-owner='.$thumb['owner'].'&amp;wppa-occur=1';
									$title = __( 'See the authors albums', 'wp-photo-album-plus' );
									break;
								case 'ownerphotos':
									$href = wppa_get_permalink( $page ).'wppa-cover=0&amp;wppa-owner='.$thumb['owner'].'&photos-only&amp;wppa-occur=1';
									$title = __( 'See the authors photos', 'wp-photo-album-plus' );
									break;
								case 'upldrphotos':
									$href = wppa_get_permalink( $page ).'wppa-cover=0&amp;wppa-upldr='.$thumb['owner'].'&amp;wppa-occur=1';
									$title = __( 'See all the authors photos', 'wp-photo-album-plus' );
									break;
							}
							$result .= '<a href="'.wppa_convert_to_pretty( $href ).'" title="'.$title.'" >';
						}

						// The image
						$result .= 	'<img' .
										' style="height:'.$maxh.'px; width:'.$maxw.'px;"' .
										' src="' . wppa_fix_poster_ext( wppa_get_photo_url( $id, '', $maxw, $maxh ), $id ) . '"' .
										' ' . wppa_get_imgalt( $id ) .
										' />';

						// The /link
						if ( $linktype != 'none' ) {
							$result .= '</a>';
						}

						// The medal if near the bottom
						$result .= wppa_get_medal_html_a( array( 'id' => $id, 'size' => 'M', 'where' => 'bot' ) );

						// The subtitles
						$result .= "\n\t".'<div style="font-size:'.$fontsize.'px; line-height:'.$lineheight.'px; position:absolute; width:'.$maxw.'px; ">';
							$result .= sprintf( __( 'Photo by: %s', 'wp-photo-album-plus' ), $data[$id]['user'] ).'<br />';
							if ( $maxratings 	== 'yes' ) {
								$n = $data[$id]['maxratingcount'];
								$result .= sprintf( _n( '%d max rating', '%d max ratings', $n, 'wp-photo-album-plus' ), $n ).'<br />';
							}
							if ( $ratcount 		== 'yes' ) {
								$n = $data[$id]['ratingcount'];
								$result .= sprintf( _n( '%d vote', '%d votes', 'wp-photo-album-plus'), $n ).'<br />';
							}
							if ( $meanrat  		== 'yes' ) {
								$m = $data[$id]['meanrating'];
								$result .= sprintf( __( 'Rating: %4.2f.', 'wp-photo-album-plus' ), $m ).'<br />';
							}
						$result .= '</div>';
						$result .= '<div style="clear:both" ></div>';

					$result .= "\n".'</div>';
				}
				else {	// No image
					$result .= '<div>'.sprintf( __( 'Photo %s not found.', 'wp-photo-album-plus' ), $id ).'</div>';
				}
			}
		}
		else {
			$result .= $data;	// No array, print message
		}
	}
	else {	// Display = owner
		if ( is_array( $data ) ) {
			$result .= '<ul>';
			foreach ( array_keys( $data ) as $author ) {
				$result .= '<li>';
				// The link if any
				if ( $linktype != 'none' ) {
					switch ( $linktype ) {
						case 'owneralbums':
							$href = wppa_get_permalink( $page ).'wppa-cover=1&amp;wppa-owner='.$data[$author]['owner'].'&amp;wppa-occur=1';
							$title = __( 'See the authors albums' , 'wp-photo-album-plus');
							break;
						case 'ownerphotos':
							$href = wppa_get_permalink( $page ).'wppa-cover=0&amp;wppa-owner='.$data[$author]['owner'].'&amp;photos-only&amp;wppa-occur=1';
							$title = __( 'See the authors photos' , 'wp-photo-album-plus');
							break;
						case 'upldrphotos':
							$href = wppa_get_permalink( $page ).'wppa-cover=0&amp;wppa-upldr='.$data[$author]['owner'].'&amp;wppa-occur=1';
							$title = __( 'See all the authors photos' , 'wp-photo-album-plus');
							break;
					}
					$result .= '<a href="'.$href.'" title="'.$title.'" >';
				}

				// The name
				$result .= $author;

				// The /link
				if ( $linktype != 'none' ) {
					$result .= '</a>';
				}

				$result .= '<br/>';

				// The subtitles
				$result .= "\n" .
							'<div style="font-size:'.wppa_opt( 'fontsize_widget_thumb' ).'px; line-height:'.$lineheight.'px; ">';
							if ( $maxratings 	== 'yes' ) {
								$n = $data[$author]['maxratingcount'];
								$result .= sprintf( _n( '%d max rating', '%d max ratings', $n, 'wp-photo-album-plus' ), $n ).'<br />';
							}
							if ( $ratcount 		== 'yes' ) {
								$n = $data[$author]['ratingcount'];
								$result .= sprintf( _n( '%d vote', '%d votes', 'wp-photo-album-plus'), $n ).'<br />';
							}
							if ( $meanrat  		== 'yes' ) {
								$m = $data[$author]['meanrating'];
								$result .= sprintf( __( 'Mean value: %4.2f.', 'wp-photo-album-plus' ), $m ).'<br />';
							}

				$result .= 	'</div>';
				$result .= 	'</li>';
			}
			$result .= '</ul>';
		}
		else {
			$result .= $data;	// No array, print message
		}
	}

	return $result;
}

// The calendar box
function wppa_calendar_box() {

	if ( is_feed() ) return;

	// The calendar container
	wppa_container( 'open' );
	wppa_out( 	'<div' .
					' id="wppa-calendar-' . wppa( 'mocc' ) . '"' .
					' class="wppa-box wppa-calendar"' .
					' style="' .
						'font-size:10px;' .
						'line-height:12px;' .
						__wcs( 'wppa-box' ) .
						__wcs( 'wppa-calendar' ) .
						'"' .
					' >' .
					'<div style="overflow:auto;" >' .
						wppa_get_calendar_html() .
					'</div>' .
					'<div class="wppa-clear" style="' . __wis( 'clear:both;' ) . '" >' .
					'</div>' .
				'</div>'
			);

	wppa_container( 'close' );

	// Bump occurrances
	wppa( 'occur', wppa( 'occur' ) + '1' );
	wppa( 'mocc', wppa( 'mocc' ) + '1' );

	// The Display container.
	// Note: the display container ocurrance is one higher than the calendar occurrance.
	wppa_container( 'open' );
	wppa_container( 'close' );

}

// The calendar html
function wppa_get_calendar_html() {
global $wpdb;

	// Init
	$result 		= '';
	$secinday 		= 24*60*60;
	$calendar_type 	= wppa( 'calendar' );
	$autoall		= wppa( 'calendarall' );
	$albums 		= wppa( 'start_album' ) ? wppa_expand_enum( wppa_alb_to_enum_children( wppa( 'start_album' ) ) ) : '';
	$alb_clause 	= $albums ? ' AND `album` IN ( ' . str_replace( '.', ',' , $albums ) . ' ) ' : '';
	$alb_arg 		= wppa( 'start_album' ) ? 'wppa-album=' . wppa_alb_to_enum_children( wppa( 'start_album' ) ) . '&' : '';
	$reverse 		= wppa( 'reverse' ) ? ' DESC ' : '';

	// Get todays daynumber and range
	$today 	= floor( time() / $secinday );

	switch ( $calendar_type ) {
		case 'exifdtm':
			$photos = $wpdb->get_results( 	"SELECT `id`, `exifdtm` " .
											"FROM `" . WPPA_PHOTOS . "` " .
											"WHERE `exifdtm` <> '' " .
												"AND `status` <> 'pending' " .
												"AND `status` <> 'scheduled' " .
												$alb_clause .
											"ORDER BY `exifdtm`" . $reverse, ARRAY_A );
			$dates = array();
			foreach ( $photos as $photo ) {
				$date = substr( $photo['exifdtm'], 0, 10 );
				if ( wppa_is_exif_date( $date ) ) {
					if ( isset( $dates[$date] ) ) {
						$dates[$date]++;
					}
					else {
						$dates[$date] = '1';
					}
				}
			}
			$from 	= 0;
			$to 	= count( $dates );
			break;

		case 'timestamp':
		case 'modified':
			$photos = $wpdb->get_results( 	"SELECT `id`, `" . $calendar_type . "` " .
											"FROM `" . WPPA_PHOTOS ."` " .
											"WHERE `" . $calendar_type . "` > 0 " .
												"AND `status` <> 'pending' " .
												"AND `status` <> 'scheduled' " .
												$alb_clause .
											"ORDER BY `" . $calendar_type . "`" . $reverse, ARRAY_A );
			$dates = array();
			foreach ( $photos as $photo ) {
				$date = floor( $photo[$calendar_type] / $secinday );
				if ( isset( $dates[$date] ) ) {
					$dates[$date]++;
				}
				else {
					$dates[$date] = '1';
				}
			}
			$from 	= 0;
			$to 	= count( $dates );
			break;
	}

	// Display minicovers
	$result .= 	'<div' .
					' style="' .
						'width:' . ( 33 * ( $to - $from ) ) . 'px;' .
						'position:relative;' .
						'"' .
					' >';

	$result .= 	'<script type="text/javascript" >' .
					'wppaWaitForCounter = 0;' .
				'</script>';

	switch( $calendar_type ) {
		case 'exifdtm':

			$keys = array_keys( $dates );

			for ( $day = $from; $day < $to; $day++ ) {
				$date 		= date_create_from_format( 'Y:m:d', $keys[$day] );

				if ( is_object( $date ) ) {

					$ajaxurl 	= wppa_encrypt_url(
												wppa_get_ajaxlink('', '1') .
												'wppa-calendar=exifdtm&' .
												'wppa-caldate=' . $keys[$day] . '&' .
												$alb_arg .
												'wppa-occur=1'
												);

					if ( $autoall ) {
						$onclick 	= 	'';
					}
					else {
						$onclick 	= 	'jQuery( \'.wppa-minicover-' . wppa( 'mocc' ) . '\' ).removeClass( \'wppa-minicover-current\' );' .
										'jQuery( this ).addClass( \'wppa-minicover-current\' );' .
										'wppaDoAjaxRender( ' . ( wppa( 'mocc' ) + '1' ) . ', \'' . $ajaxurl . '\', \'\' );';
					}

					$result .= 	'<a' .
									( $autoall ? ' href="#wppa-' . $day . '"' : '' ) .
									' class="wppa-minicover-' . wppa( 'mocc' ) . '"' .
									' onclick="' . $onclick . '"' .
									' >' .
									'<div' .
										' id="wppa-minicover-' . $day . '"' .
										' class="wppa-minicover"' .
										' style="' .
											'border:1px solid gray;' .
											'margin-right:1px;' .
											'float:left;' .
											'text-align:center;' .
											'width:30px;"' .
										' >' .
										$date->format( 'M' ) . '<br />' .
										$date->format( 'd' ) . '<br />' .
										$date->format( 'D' ) . '<br />' .
										$date->format( 'Y' ) . '<br />' .
										'(' . $dates[$keys[$day]] . ')' .
									'</div>' .
								'</a>';

					if ( $autoall ) {
						$addlabel =	'<a id=\"wppa-' . $day . '\" ></a>';

						$result .= 	'<script type="text/javascript" >' .
										'wppaDoAjaxRender( ' .
											( wppa( 'mocc' ) + '1' ) .
											', \'' . str_replace( '&amp;', '&', $ajaxurl ) .
											'\', \'\', \'' . $addlabel . '\', ' . ( $day + '1' ) .' );' .
									'</script>';
					}
				}
			}
			break;

		case 'timestamp':
		case 'modified':
			$keys = array_keys( $dates );

			for ( $day = $from; $day < $to; $day++ ) {

				$date 		= $keys[$day];

				$ajaxurl 	= wppa_encrypt_url(
											wppa_get_ajaxlink('', '1') .
											'wppa-calendar='.$calendar_type.'&' .
											'wppa-caldate=' . $keys[$day] . '&' .
											$alb_arg .
											'wppa-occur=1'
											);

				if ( $autoall ) {
					$onclick 	= 	'';
				}
				else {
					$onclick 	= 	'jQuery( \'.wppa-minicover-' . wppa( 'mocc' ) . '\' ).removeClass( \'wppa-minicover-current\' );' .
									'jQuery( this ).addClass( \'wppa-minicover-current\' );' .
									'wppaDoAjaxRender( ' . ( wppa( 'mocc' ) + '1' ) . ', \'' . $ajaxurl . '\', \'\' );';
				}

				$result .= 	'<a' .
								' class="wppa-minicover-' . wppa( 'mocc' ) . '"' .
								' onclick="' . $onclick . '"' .
								' >' .
								'<div' .
									' id="wppa-minicover-' . $day . '"' .
									' class="wppa-minicover"' .
									' style="' .
										'border:1px solid gray;' .
										'margin-right:1px;' .
										'float:left;' .
										'text-align:center;' .
										'width:30px;"' .
									' >' .
									date( 'M', $date * $secinday ) . '<br />' .
									date( 'd', $date * $secinday ) . '<br />' .
									date( 'D', $date * $secinday ) . '<br />' .
									date( 'Y', $date * $secinday ) . '<br />' .
									'(' . $dates[$keys[$day]] . ')' .
								'</div>' .
							'</a>';

				if ( $autoall ) {
					$addlabel =	'<a id=\"wppa-' . $day . '\" ></a>';

					$result .= 	'<script type="text/javascript" >' .
									'wppaDoAjaxRender( ' .
										( wppa( 'mocc' ) + '1' ) .
										', \'' . str_replace( '&amp;', '&', $ajaxurl ) .
										'\', \'\', \'' . $addlabel . '\', ' . ( $day + '1' ) .' );' .
								'</script>';
				}
			}
			break;
	}

	$result .= 	'<script type="text/javascript" >' .
					'jQuery(document).ready(function(){ wppaWaitForCounter = 1; });' .
				'</script>';

	$result .= 	'</div>';

	return $result;
}

// Stereo settings box
function wppa_stereo_box() {

	// Init
	$result = '';

	// No search box on feeds
	if ( is_feed() ) return;

	// Open container
	wppa_container( 'open' );

	// Open wrapper
	$result .= "\n";
	$result .= '<div' .
					' id="wppa-stereo-' . wppa( 'mocc' ) . '"' .
					' class="wppa-box wppa-stereo"' .
					' style="' . __wcs( 'wppa-box' ) . __wcs( 'wppa-stereo' ) . '"' .
					' >';

	// The search html
	$result .= wppa_get_stereo_html();

	// Clear both
	$result .= '<div class="wppa-clear" style="'.__wis( 'clear:both;' ).'" ></div>';

	// Close wrapper
	$result .= '</div>';

	// Output
	wppa_out( $result );

	// Close container
	wppa_container( 'close' );
}

// Stereo settings html
function wppa_get_stereo_html() {
global $wppa_supported_stereo_types;
global $wppa_supported_stereo_glasses;
global $wppa_supported_stereo_type_names;
global $wppa_supported_stereo_glass_names;

	$result = 	'<form' .
					' id="wppa-stereo-form-' . wppa( 'mocc' ) . '"' .
					' >' .
					'<select' .
						' id="wppa-stereo-type-' . wppa( 'mocc' ) . '"' .
						' name="wppa-stereo-type"' .
						' onchange="wppaStereoTypeChange( this.value );"' .
						' >';
						foreach( array_keys( $wppa_supported_stereo_types ) as $key ) {
							$result .=
							'<option' .
								' value="' . $wppa_supported_stereo_types[$key] . '"' .
								( isset( $_COOKIE["stereotype"] ) && $_COOKIE["stereotype"] == $wppa_supported_stereo_types[$key] ? ' selected="selected"' : '' ) .
								' >' .
								$wppa_supported_stereo_type_names[$key] .
							'</option>';
						}
	$result .=		'</select>';

	$result .=		'<select' .
						' id="wppa-stereo-glass-' . wppa( 'mocc' ) . '"' .
						' name="wppa-stereo-glass"' .
						' onchange="wppaStereoGlassChange( this.value );"' .
						' >';
						foreach( array_keys( $wppa_supported_stereo_glasses ) as $key ) {
							$result .=
							'<option' .
								' value="' . $wppa_supported_stereo_glasses[$key] . '"' .
								( isset( $_COOKIE["stereoglass"] ) && $_COOKIE["stereoglass"] == $wppa_supported_stereo_glasses[$key] ? ' selected="selected"' : '' ) .
								' >' .
								$wppa_supported_stereo_glass_names[$key] .
							'</option>';
						}
	$result .=		'</select>';

	$result .= 		'<input' .
						' type="button"' .
						' onclick="document.location.reload(true)"' .
						' value="' . __( 'Refresh', 'wp-photo-album-plus' ) . '"' .
						' />';

	$result .=	'</form>';

	return $result;
}

function wppa_is_exif_date( $date ) {

	if ( strlen( $date ) != '10' ) return false;

	for ( $i=0; $i<10; $i++ ) {
		$d = substr( $date, $i, '1' );
		switch ( $i ) {
			case 4:
			case 7:
				if ( $d != ':' ) return false;
				break;
			default:
				if ( ! in_array( $d, array( '0','1','2','3','4','5','6','7','8','9' ) ) ) return false;
		}
	}

	$t = explode( ':', $date );
	if ( $t['0'] < '1970' ) return false;
	if ( $t['0'] > date( 'Y' ) ) return false;
	if ( $t['1'] < '1' ) return false;
	if ( $t['1'] > '12' ) return false;
	if ( $t['2'] < '1' ) return false;
	if ( $t['2'] > '31' ) return false;

	return true;
}