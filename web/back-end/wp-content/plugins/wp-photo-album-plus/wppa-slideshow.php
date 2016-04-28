<?php
/* wppa-slideshow.php
* Package: wp-photo-album-plus
*
* Contains all the slideshow high level functions
* Version 6.4.07
*
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

function wppa_the_slideshow() {

	wppa_prepare_slideshow_pagination();

	if ( wppa_opt( 'pagelink_pos' ) == 'top' || wppa_opt( 'pagelink_pos' ) == 'both' ) wppa_slide_page_links();

	if ( wppa_switch( 'split_namedesc' ) ) {
		$indexes = explode( ',', wppa_opt( 'slide_order_split' ) );
		$i = '0';
		while ( $i < '12' ) {
			switch ( $indexes[$i] ) {
				case '0':
					wppa_start_stop('optional');				// The 'Slower | start/stop | Faster' bar
					break;
				case '1':
					wppa_slide_frame();						// The photo / slide
					break;
				case '2':
					wppa_slide_name_box('optional');		// Show name in a box.
					break;
				case '3':
					wppa_slide_desc_box('optional');		// Show description in a box.
					break;
				case '4':
					wppa_slide_custom('optional');			// Custom box
					break;
				case '5':
					wppa_slide_rating('optional');			// Rating box
					break;
				case '6':
					wppa_slide_filmstrip('optional');		// Show Filmstrip
					break;
				case '7':
					wppa_browsebar('optional');				// The 'Previous photo | Photo n of m | Next photo' bar
					break;
				case '8':
					wppa_comments('optional');				// The Comments box
					break;
				case '9':
					wppa_iptc('optional');					// The IPTC box
					break;
				case '10':
					wppa_exif('optional');					// The EXIF box
					break;
				case '11':
					wppa_share('optional');					// The Share box
					break;
				default:
					break;
			}
			$i++;
		}
	}
	else {
		$indexes = explode( ',', wppa_opt( 'slide_order' ) );
		$i = '0';
		while ( $i < '11' ) {
			switch ( $indexes[$i] ) {
				case '0':
					wppa_start_stop('optional');				// The 'Slower | start/stop | Faster' bar
					break;
				case '1':
					wppa_slide_frame();						// The photo / slide
					break;
				case '2':
					wppa_slide_name_desc('optional');		// Show name and description in a box.
					break;
				case '3':
					wppa_slide_custom('optional');			// Custom box
					break;
				case '4':
					wppa_slide_rating('optional');			// Rating box
					break;
				case '5':
					wppa_slide_filmstrip('optional');		// Show Filmstrip
					break;
				case '6':
					wppa_browsebar('optional');				// The 'Previous photo | Photo n of m | Next photo' bar
					break;
				case '7':
					wppa_comments('optional');				// The Comments box
					break;
				case '8':
					wppa_iptc('optional');					// The IPTC box
					break;
				case '9':
					wppa_exif('optional');					// The EXIF box
					break;
				case '10':
					wppa_share('optional');					// The Share box
					break;
				default:
					break;
			}
			$i++;
		}
	}
	if ( wppa_opt( 'pagelink_pos' ) == 'bottom' || wppa_opt( 'pagelink_pos' ) == 'both' ) wppa_slide_page_links();
}

function wppa_prepare_slideshow_pagination() {
global $thumbs;
global $thumbs_ids;

	wppa( 'ss_pag', false );

	// Save thumb ids of full selection
	$thumbs_ids = array();
	if ( $thumbs ) foreach ( $thumbs as $t ) {
		$thumbs_ids[] = $t['id'];
	}

	// See if slideonly max is appliccable
	if ( wppa( 'is_slideonly' ) || wppa( 'is_slideonlyf' ) ) {
		if ( wppa_opt( 'slideonly_max' ) ) {
			$pagsiz = wppa_opt( 'slideonly_max' );
		}
		else {
			return;
		}
	}
	
	// Not slideonly
	else {
	
		// Page size defined?
		if ( ! wppa_opt( 'slideshow_pagesize' ) ) return;

		// Not in a widget!
		if ( wppa_in_widget() ) return;

		// Fits in one page?
		$pagsiz = wppa_opt( 'slideshow_pagesize' );
		if ( count( $thumbs ) <= $pagsiz ) return;
	}
	
	// Pagination on and required
	wppa( 'ss_pag', true );
	$nslides = count( $thumbs );
	wppa( 'npages', ceil( $nslides / $pagsiz ) );

	// Assume page = 1
	wppa( 'curpage', '1' );

	// If a page is requested, find it
	$pagreq = wppa_get_get( 'page' );
	if ( is_numeric( $pagreq ) && $pagreq > '0' ) {
		wppa( 'curpage', $pagreq );
	}

	// If a photo requested, find the page where its on
	elseif ( wppa( 'start_photo' ) ) {
		$first = true;
		foreach ( array_keys( $thumbs ) as $key ) {
		if ( $first ) { wppa_dbg_msg('First index = '.$key); $first = false; }
			if ( $thumbs[$key]['id'] == wppa( 'start_photo' ) ) {
				wppa( 'curpage', floor( $key / $pagsiz ) + '1' );
				wppa_dbg_msg('Startphoto is on page #'.wppa( 'curpage' ) );
			}
		}
	}

	// Filmstrip assumes array $thumbs to start at index 0.
	// We shift the req'd part down to the beginning and unset the rest
	$skips = ( wppa( 'curpage' ) - '1' ) * $pagsiz;
	wppa_dbg_msg('Skips = '.$skips);
	foreach ( array_keys( $thumbs ) as $key ) {
		if ( $key < $pagsiz ) {
			if ( isset( $thumbs[$key + $skips]) ) {
				if ( $skips ) $thumbs[$key] = $thumbs[$key + $skips];
			}
			else unset( $thumbs[$key] );	// last page < pagesize
		}
		else unset ( $thumbs[$key] );
	}
	wppa_dbg_msg('Thumbs has '.count($thumbs).' elements.');
}

function wppa_slide_page_links() {
global $thumbs;

	if ( ! wppa( 'ss_pag' ) ) return;	// No pagination
	if ( wppa( 'is_slideonly' ) || wppa( 'is_slideonlyf' ) ) return; // Not on slideonly

	wppa_page_links( wppa( 'npages' ), wppa( 'curpage' ), true );

}

function wppa_start_stop( $opt = '' ) {

	if ( is_feed() ) return;	// Not in a feed

	// A single image slideshow needs no navigation
	if ( wppa( 'is_single' ) ) return;
	if ( wppa( 'is_filmonly' ) ) return;

	// we always need the js part for the functionality (through filmstrip etc).
	// so if not wanted: hide it
	$hide = 'display:none; '; // assume hide
	if ( $opt != 'optional' ) $hide = '';														// not optional: show
	if ( wppa_switch( 'show_startstop_navigation' ) && ! wppa( 'is_slideonly' ) ) $hide = '';	// we want it

	if ( wppa_opt( 'start_slide' ) || wppa_in_widget() ) {
		wppa_add_js_page_data( "\n" . '<script type="text/javascript">' );
		wppa_add_js_page_data( "\n" . 'wppaSlideInitRunning['.wppa( 'mocc' ).'] = true;' );
		wppa_add_js_page_data( "\n" . 'wppaMaxOccur = '.wppa( 'mocc' ).';' );
		wppa_add_js_page_data( "\n" . '</script>' );
	}

	if ( ! $hide ) {
		wppa_out( 	'<div' .
						' id="prevnext1-'.wppa( 'mocc' ).'"' .
						' class="wppa-box wppa-nav wppa-nav-text"' .
						' style="text-align:center;'.__wcs('wppa-box').__wcs('wppa-nav').__wcs('wppa-nav-text').$hide.'"' .
						' >' .
						'<a' .
							' id="speed0-'.wppa( 'mocc' ).'"' .
							' class="wppa-nav-text speed0"' .
							' style="'.__wcs('wppa-nav-text').'"' .
							' onclick="wppaSpeed('.wppa( 'mocc' ).', false); return false;"' .
							' >' .
							__('Slower', 'wp-photo-album-plus') .
						'</a>' .
						' | ' .
						'<a' .
							' id="startstop-'.wppa( 'mocc' ).'"' .
							' class="wppa-nav-text startstop"' .
							' style="'.__wcs('wppa-nav-text').'"' .
							' onclick="wppaStartStop('.wppa( 'mocc' ).', -1); return false;">' .
							__('Start', 'wp-photo-album-plus') .
						'</a>' .
						' | ' .
						'<a' .
							' id="speed1-'.wppa( 'mocc' ).'"' .
							' class="wppa-nav-text speed1"' .
							' style="'.__wcs('wppa-nav-text').'"' .
							' onclick="wppaSpeed('.wppa( 'mocc' ).', true); return false;">' .
							__('Faster', 'wp-photo-album-plus') .
						'</a>' .
					'</div>' );
	}
}

function wppa_slide_frame() {

	if ( is_feed() ) return;
	if ( wppa( 'is_filmonly' ) ) return;

	if ( wppa_switch('wppa_slide_pause') ) {
		$pause = 	' onmouseover="wppaSlidePause['.wppa( 'mocc' ).'] = \''.__('Paused', 'wp-photo-album-plus').'\'"' .
					' onmouseout="wppaSlidePause['.wppa( 'mocc' ).'] = false"';
	}
	else $pause = '';

	// There are still users who turn off javascript...
	wppa_out( 	'<noscript style="text-align:center; " >' .
					'<span style="color:red; ">' .
						__('To see the full size images, you need to enable javascript in your browser.', 'wp-photo-album-plus') .
					'</span>' .
				'</noscript>' );

	wppa_out( 	'<div' .
					' id="slide_frame-'.wppa( 'mocc' ).'"' .
					$pause .
					' class="slide-frame"' .
					' style="overflow:hidden;' . wppa_get_slide_frame_style() . '"' .
					' >' );

	$auto = wppa( 'auto_colwidth' ) || ( wppa_opt( 'colwidth' ) == 'auto' );

	wppa_out( 	'<div' .
					' id="theslide0-' . wppa( 'mocc' ) . '"' .
					' class="theslide theslide-' . wppa( 'mocc' ) . '"' .
					' style="' .
						( $auto ? 'width:100%;' : 'width:' . wppa( 'slideframewidth' )  . 'px;' ) .
						'margin:auto;' .
					'"' .
					' >' .
				'</div>' .
				'<div' .
					' id="theslide1-' . wppa( 'mocc' ) . '"' .
					' class="theslide theslide-' . wppa( 'mocc' ) . '"' .
					' style="' .
						( $auto ? 'width:100%;' : 'width:' . wppa( 'slideframewidth' )  . 'px;' ) .
						'margin:auto;' .
					'"' .
					' >' .
				'</div>'
			);

	wppa_out( 	'<div' .
					' id="spinner-' . wppa( 'mocc' ) . '"' .
					' class="spinner"' .
					' >' .
				'</div>'
			);

	if ( ! wppa_page( 'oneofone' ) ) {

		// Big browsing buttons enabled ?
		if ( ( wppa_switch( 'show_bbb' ) && ! wppa_in_widget() ) ||
			 ( wppa_switch( 'show_bbb_widget' ) && wppa_in_widget() ) ) {

			wppa_out( 	'<img' .
							' id="bbb-' . wppa( 'mocc' ) . '-l"' .
							' class="bbb-l bbb-' . wppa( 'mocc' ) . '"' .
							' src="' . wppa_get_imgdir() . 'bbbl.png"' .
							' alt="bbbl"' .
							' style="' .
								'background-color:transparent;' .
								'border:none;' .
								'z-index:83;' .
								'position:absolute;' .
								'float:left;' .
								'top:0px;' .
								'width:' . ( wppa( 'slideframewidth' ) * 0.5 ) . 'px;' .
								'height:' . wppa( 'slideframeheight' ) . 'px;' .
								'box-shadow:none;' .
								'cursor:default;' .
								'"' .
							' onmouseover="wppaBbb('.wppa( 'mocc' ).',\'l\',\'show\')"' .
							' onmouseout="wppaBbb('.wppa( 'mocc' ).',\'l\',\'hide\')"' .
							' onclick="wppaBbb('.wppa( 'mocc' ).',\'l\',\'click\')"' .
							' />' .
						'<img' .
							' id="bbb-' . wppa( 'mocc' ) . '-r"' .
							' class="bbb-r bbb-' . wppa( 'mocc' ) . '"' .
							' src="' . wppa_get_imgdir() . 'bbbr.png"' .
							' alt="bbbr"' .
							' style="' .
								'background-color:transparent;' .
								'border:none;' .
								'z-index:83;' .
								'position:absolute;' .
								'float:right;' .
								'top:0px;' .
								'width:' . ( wppa( 'slideframewidth' ) * 0.5 ) . 'px;' .
								'height:' . wppa( 'slideframeheight' ) . 'px;' .
								'box-shadow:none;' .
								'cursor:default;' .
								'"' .
							' onmouseover="wppaBbb('.wppa( 'mocc' ).',\'r\',\'show\')"' .
							' onmouseout="wppaBbb('.wppa( 'mocc' ).',\'r\',\'hide\')"' .
							' onclick="wppaBbb('.wppa( 'mocc' ).',\'r\',\'click\')"' .
							' />'
					);
		}

		// Ugly browse buttons ?
		if ( ( wppa_switch( 'show_ubb' ) && ! wppa_in_widget() ) ||
			 ( wppa_switch( 'show_ubb_widget' ) && wppa_in_widget() ) ) {

/*
	wppaOvlLeftSymbolUrl = "'.(wppa_opt('left_symbol_url') ? wppa_opt('left_symbol_url') : wppa_get_imgdir( 'prev-'.wppa_opt('ovl_theme').'.gif', true )).'";
	wppaOvlRightSymbolUrl = "'.(wppa_opt('right_symbol_url') ? wppa_opt('right_symbol_url') : wppa_get_imgdir( 'next-'.wppa_opt('ovl_theme').'.gif', true )).'";
	wppaLeftRightSymbolSize = '.wppa_opt('left_right_symbol_size').';
	wppaLeftRightSymbolBradius = '.wppa_opt('left_right_symbol_bradius').';

						' onclick="wppaOvlShowPrev()"' +
						' ontouchend="wppaOvlShowPrev()"' +
						' onmouseover="jQuery(this).stop().fadeTo(200,1);"' +
						' onmouseout="jQuery(this).stop().fadeTo(200,0);"' +
						' ontouchstart="jQuery(this).stop().fadeTo(200,1);"' +
						' onload="jQuery(this).stop().fadeTo(5000,0);"' +

*/


			wppa_out( 	'<img' .
							' id="ubb-'.wppa( 'mocc' ).'-l"' .
							' class="ubb ubb-l ubb-'.wppa( 'mocc' ).'"' .
							' src="'.(wppa_opt('left_symbol_url') ? wppa_opt('left_symbol_url') : wppa_get_imgdir( 'prev-'.wppa_opt('ovl_theme').'.gif', true )).'"' .
							' alt="ubbl"' .
							' style="' .
								'background-color:transparent;' .
								'border:none;' .
								'z-index:85;' .
								'position:absolute;' .
								'top:50%;' .
								'margin-top:-' . ( wppa_opt('left_right_symbol_size') / 2 ) . 'px;' .
								'left:0;' .
								'box-shadow:none;' .
								'cursor:pointer;' .
								'width:' . wppa_opt('left_right_symbol_size') . 'px;' .
								'border-radius:' . wppa_opt('left_right_symbol_bradius') . 'px;' .
								'"' .
							' onmouseover="wppaUbb('.wppa( 'mocc' ).',\'l\',\'show\')"' .
							' ontouchstart="wppaUbb('.wppa( 'mocc' ).',\'l\',\'show\')"' .
							' onmouseout="wppaUbb('.wppa( 'mocc' ).',\'l\',\'hide\')"' .
							' onclick="wppaUbb('.wppa( 'mocc' ).',\'l\',\'click\')"' .
							' ontouchend="wppaUbb('.wppa( 'mocc' ).',\'l\',\'click\')"' .
							' onload="wppaUbb('.wppa( 'mocc' ).',\'l\',\'hide\')"' .
							' />' .
						'<img' .
							' id="ubb-'.wppa( 'mocc' ).'-r"' .
							' class="ubb ubb-r ubb-'.wppa( 'mocc' ).'"' .
							' src="'.(wppa_opt('right_symbol_url') ? wppa_opt('right_symbol_url') : wppa_get_imgdir( 'next-'.wppa_opt('ovl_theme').'.gif', true )).'"' .
							' alt="ubbr"' .
							' style="' .
								'background-color:transparent;' .
								'border:none;' .
								'z-index:85;' .
								'position:absolute;' .
								'top:50%;' .
								'margin-top:-' . ( wppa_opt('left_right_symbol_size') / 2 ) . 'px;' .
								'right:0;' .
								'box-shadow:none;' .
								'cursor:pointer;' .
								'width:' . wppa_opt('left_right_symbol_size') . 'px;' .
								'border-radius:' . wppa_opt('left_right_symbol_bradius') . 'px;' .
								'"' .
							' onmouseover="wppaUbb('.wppa( 'mocc' ).',\'r\',\'show\')"' .
							' ontouchstart="wppaUbb('.wppa( 'mocc' ).',\'r\',\'show\')"' .
							' onmouseout="wppaUbb('.wppa( 'mocc' ).',\'r\',\'hide\')"' .
							' onclick="wppaUbb('.wppa( 'mocc' ).',\'r\',\'click\')"' .
							' ontouchend="wppaUbb('.wppa( 'mocc' ).',\'r\',\'click\')"' .
							' onload="wppaUbb('.wppa( 'mocc' ).',\'l\',\'hide\')"' .
							' />'
					);
		}
	}

	wppa_startstop_icons();
	wppa_numberbar();

	wppa_out( '</div>' );
}

function wppa_slide_name_desc( $key = 'optional' ) {

	$do_it = false;
	if ( $key != 'optional' ) $do_it = true;
	if ( wppa( 'is_slideonly' ) ) {
		if ( wppa( 'name_on') ) $do_it = true;
		if ( wppa( 'desc_on') ) $do_it = true;
	}
	else {
		if ( wppa_switch('wppa_show_full_desc') ) $do_it = true;
		if ( wppa_switch('wppa_show_full_name') || wppa_switch('wppa_show_full_owner') ) $do_it = true;
	}
	if ( $do_it ) {
		wppa_out( 	'<div' .
						' id="namedesc-' . wppa( 'mocc' ) . '"' .
						' class="wppa-box wppa-name-desc"' .
						' style="' . __wcs('wppa-box') . __wcs('wppa-name-desc') .'"' .
						' >'
				);

			if ( wppa_switch('wppa_swap_namedesc') ) {
				wppa_slide_name($key);			// The name of the photo
				wppa_slide_description($key);		// The description of the photo
			}
			else {
				wppa_slide_description($key);		// The description of the photo
				wppa_slide_name($key);			// The name of the photo
			}

		wppa_out( '</div>' );
	}
}

function wppa_slide_name_box( $key = 'optional' ) {

	$do_it = false;
	if ( $key != 'optional' ) $do_it = true;
	if ( wppa( 'is_slideonly' ) ) {
		if ( wppa( 'name_on' ) ) $do_it = true;
	}
	else {
		if ( wppa_switch( 'show_full_name' ) || wppa_switch( 'show_full_owner' ) ) $do_it = true;
	}
	if ( $do_it ) {
		wppa_out( 	'<div' .
						' id="namebox-' . wppa( 'mocc' ) . '"' .
						' class="wppa-box wppa-name-desc"' .
						' style="' . __wcs( 'wppa-box' ) . __wcs( 'wppa-name-desc' ) . '"' .
						' >'
				);
			wppa_slide_name( $key );			// The name of the photo
		wppa_out(  '</div>' );
	}
}

function wppa_slide_desc_box( $key = 'optional' ) {

	$do_it = false;
	if ( $key != 'optional' ) $do_it = true;
	if ( wppa( 'is_slideonly' ) ) {
		if ( wppa( 'desc_on' ) ) $do_it = true;
	}
	else {
		if ( wppa_switch( 'show_full_desc' ) ) $do_it = true;
	}
	if ( $do_it ) {
		wppa_out( 	'<div' .
						' id="descbox-'.wppa( 'mocc' ).'"' .
						' class="wppa-box wppa-name-desc"' .
						' style="' . __wcs( 'wppa-box' ) . __wcs( 'wppa-name-desc' ) . '"' .
						' >' );
			wppa_slide_description( $key );		// The description of the photo
		wppa_out(  '</div>' );
	}
}

function wppa_slide_name($opt = '') {

	if ( wppa( 'is_slideonly' ) ) {
		if ( wppa( 'name_on' ) ) $doit = true;
		else $doit = false;
	}
	else {
		if ( $opt == 'optional' ) {
			if ( wppa_switch( 'show_full_name' ) || wppa_switch( 'show_full_owner' ) ) $doit = true;
			else $doit = false;
		}
		else $doit = true;
	}
	if ( $opt == 'description' ) $doit = false;

	if ( $doit ) {
		wppa_out( 	'<div' .
						' id="imagetitle-'.wppa( 'mocc' ).'"' .
						' class="wppa-fulltitle imagetitle"' .
						' style="'.__wcs('wppa-fulltitle').'padding:3px; width:100%"' .
						' >' .
					'</div>'
				);
	}
}

function wppa_slide_description( $opt = '' ) {

	if ( wppa( 'is_slideonly' ) ) {
		if ( wppa( 'desc_on' ) ) $doit = true;
		else $doit = false;
	}
	else {
		if ( $opt == 'optional' ) {
			if ( wppa_switch( 'show_full_desc' ) ) $doit = true;
			else $doit = false;
		}
		else $doit = true;
	}
	if ( $opt == 'name' ) $doit = false;

	if ( $doit ) {
		wppa_out( 	'<div' .
						' id="imagedesc-'.wppa( 'mocc' ).'"' .
						' class="wppa-fulldesc imagedesc"' .
						' style="' .
							__wcs( 'wppa-fulldesc' ) .
							'padding:3px;' .
							'width:100%;' .
							'text-align:' . wppa_opt( 'fulldesc_align' ) .
							'"' .
						' >' .
					'</div>'
				);
	}
}

function wppa_slide_custom( $opt = '' ) {

	if ( $opt == 'optional' && ! wppa_switch( 'custom_on' ) ) return;
	if ( wppa( 'is_slideonly' ) ) return;	/* Not when slideonly */
	if ( is_feed() ) return;

	$content = __( stripslashes( wppa_opt( 'custom_content' ) ) );

	// w#albdesc
	if ( wppa_is_int( wppa( 'start_album' ) ) && wppa( 'start_album' ) > '0' ) {
		$content = str_replace( 'w#albdesc', wppa_get_album_desc( wppa( 'start_album' ) ), $content );
	}
	else {
		$content = str_replace( 'w#albdesc', '', $content );
	}

	// w#fotomoto
	if ( wppa_switch( 'fotomoto_on' ) ) {
		$fontsize = wppa_opt( 'fotomoto_fontsize' );
		if ( $fontsize ) {
			$s = '<style>.FotomotoToolbarClass{font-size:' . wppa_opt( 'fotomoto_fontsize' ) . 'px !important;}</style>';
		}
		else $s = '';
		$content = str_replace( 'w#fotomoto',
								$s .
								'<div' .
									' id="wppa-fotomoto-container-'.wppa( 'mocc' ).'"' .
									' class="wppa-fotomoto-container"' .
									' >' .
								'</div>' .
								'<div' .
									' id="wppa-fotomoto-checkout-'.wppa( 'mocc' ).'"' .
									' class="wppa-fotomoto-checkout FotomotoToolbarClass"' .
									' style="float:right; clear:none;"' .
									' >' .
									'<ul' .
										' class="FotomotoBar"' .
										' style="list-style:none outside none;"' .
										' >' .
										'<li>' .
											'<a' .
											' onclick="FOTOMOTO.API.checkout(); return false;"' .
											' >' .
												__('Checkout', 'wp-photo-album-plus') .
											'</a>' .
										'</li>' .
									'</ul>' .
								'</div>' .
								'<div' .
									' style="clear:both;"' .
									' >' .
								'</div>',
								$content );
	}
	else {
		$content = str_replace( 'w#fotomoto', '', $content );
	}

	$content = wppa_html( $content );

	wppa_out( 	'<div' .
					' id="wppa-custom-'.wppa( 'mocc' ).'"' .
					' class="wppa-box wppa-custom"' .
					' style="'.__wcs('wppa-box').__wcs('wppa-custom').'"' .
					' >' .
					$content .
				'</div>'
			);
}

function wppa_slide_rating( $opt = '' ) {

	if ( wppa_opt( 'rating_max' ) == '1' ) {
		wppa_slide_rating_vote_only( $opt );
	}
	else {
		wppa_slide_rating_range( $opt );
	}
}

function wppa_slide_rating_vote_only( $opt ) {

	if ( $opt == 'optional' && ! wppa_switch( 'rating_on' ) ) return;
	if ( wppa( 'is_slideonly' ) ) return;	/* Not when slideonly */
	if ( is_feed() ) return;

	// Open the voting box
	wppa_out(	'<div' .
					' id="wppa-rating-'.wppa( 'mocc' ).'"' .
					' class="wppa-box wppa-nav wppa-nav-text"' .
					' style="' .
						__wcs( 'wppa-box' ) .
						__wcs( 'wppa-nav' ) .
						__wcs( 'wppa-nav-text' ) .
						'text-align:center;' .
						'"' .
					'>'
			);

	// Logged in or don't care ?
	if ( ! wppa_switch( 'rating_login' ) || is_user_logged_in() ) {
		$cnt = '0';
		if ( wppa_switch( 'show_avg_rating' ) ) {
			wppa_out( sprintf(__('Number of votes: <span id="wppa-vote-count-%s" >%s</span>&nbsp;', 'wp-photo-album-plus'), wppa( 'mocc' ), $cnt) );
		}
		wppa_out( 	'<input' .
						' id="wppa-vote-button-'.wppa( 'mocc' ).'"' .
						' class="wppa-vote-button"' .
						' style="margin:0;"' .
						' type="button"' .
						' onclick="wppaRateIt('.wppa( 'mocc' ).', 1)"' .
						' value="'.wppa_opt( 'vote_button_text' ) . '"' .
					' />'
				);
	}

	// Must login to vote
	else {
		if ( wppa_switch( 'login_links' ) ) {
			wppa_out( sprintf( __( 'You must <a href="%s">login</a> to vote' , 'wp-photo-album-plus' ), site_url( 'wp-login.php', 'login' ) ) );
		}
		else {
			wppa_out( __( 'You must login to vote' , 'wp-photo-album-plus' ) );
		}
	}

	// Close the voting box
	wppa_out(  	'</div>' );

}

function wppa_slide_rating_range( $opt ) {

	if ( $opt == 'optional' && ! wppa_switch( 'rating_on' ) ) return;
	if ( wppa( 'is_slideonly' ) ) return;	/* Not when slideonly */
	if ( is_feed() ) return;

	$fs = wppa_opt( 'fontsize_nav' );
	if ( $fs ) $fs += 3; else $fs = '15';	// iconsize = fontsize+3, Default to 15
	$dh = $fs + '6';
	$size = 'font-size:'.$fs.'px;';

	// Open the rating box
	wppa_out( 	'<div' .
					' id="wppa-rating-'.wppa( 'mocc' ).'"' .
					' class="wppa-box wppa-nav wppa-nav-text"' .
					' style="' .
						__wcs( 'wppa-box' ) .
						__wcs( 'wppa-nav' ) .
						__wcs( 'wppa-nav-text' ) .
						$size .
						' text-align:center;"' .
					'> '
			);

	// Graphic display ?
	if ( wppa_opt( 'rating_display_type' ) == 'graphic' ) {
		if ( wppa_opt( 'rating_max' ) == '5' ) {
			$r['1'] = __( 'very low', 'wp-photo-album-plus' );
			$r['2'] = __( 'low', 'wp-photo-album-plus' );
			$r['3'] = __( 'average', 'wp-photo-album-plus' );
			$r['4'] = __( 'high', 'wp-photo-album-plus' );
			$r['5'] = __( 'very high', 'wp-photo-album-plus' );
		}
		else for ( $i = '1'; $i <= '10'; $i++ ) $r[$i] = $i;

		$style = ' style="height:'.$fs.'px; margin:0 0 -3px 0; padding:0; box-shadow:none; display:inline;background-color:transparent;"';
		$icon = 'star.png';

		// Display avg rating
		if ( wppa_switch( 'show_avg_rating' ) ) {
			wppa_out( 	'<span id="wppa-avg-rat-' . wppa( 'mocc' ) . '" >' .
							__( 'Average&nbsp;rating', 'wp-photo-album-plus' ) .
						'</span>&nbsp;'
					);

			$i = '1';
			while ( $i <= wppa_opt( 'rating_max' ) ) {
				wppa_out( 	'<img' .
								' id="wppa-avg-' . wppa( 'mocc' ) . '-' . $i . '"' .
								' class="wppa-avg-'.wppa( 'mocc' ).' no-shadow"' .
								$style .
								' src="' . wppa_get_imgdir() . $icon . '"' .
								' alt=" ' . $i . '"' .
								' title="'.__('Average&nbsp;rating', 'wp-photo-album-plus').': '.$r[$i].'"' .
							' />'
						);
				$i++;
			}
		}

		wppa_out( 	'<img' .
						' id="wppa-filler-'.wppa( 'mocc' ).'"' .
						' src="'.wppa_get_imgdir().'transp.png"' .
						' alt="f"' .
						' style="width:'.wppa_opt('wppa_ratspacing').'px; height:15px; box-shadow:none; padding:0; margin:0; border:none;"' .
					' />'
				);

		// Display my rating
		// Logged in or don't care
		if ( ! wppa_switch( 'rating_login' ) || is_user_logged_in() ) {

			// Show dislike icon?
			$pad = round( ( wppa_opt( 'ratspacing' ) - $fs ) / 2 );
			if ( $pad < 5 ) $pad = '5';
			if ( wppa_opt( 'dislike_mail_every' ) ) {
				wppa_out( 	'<img' .
								' id="wppa-dislike-'.wppa( 'mocc' ).'"' .
								' title="'.__('Click this if you do NOT like this image!', 'wp-photo-album-plus').'"' .
								' src="'.wppa_get_imgdir().'thumbdown.png"' .
								' alt="d"' .
								' style="height:'.$fs.'px; margin:0 0 -3px 0; padding:0 '.$pad.'px; box-shadow:none; display:inline;"' .
								' class="no-shadow"' .
								' onmouseover="jQuery(this).stop().fadeTo(100, 1.0)"' .
								' onmouseout="jQuery(this).stop().fadeTo(100, wppaStarOpacity)"' .
								' onclick="if (confirm(\'' .
													__('Are you sure you want to mark this image as inappropriate?', 'wp-photo-album-plus') .
													'\')) wppaRateIt('.wppa( 'mocc' ).', -1)"' .
							' />'
						);
				if ( wppa_switch( 'dislike_show_count' ) ) {
					wppa_out( 	'<span' .
									' id="wppa-discount-' . wppa( 'mocc' ) . '"' .
									' style="cursor:default"' .
									' title="' . __('Number of people who marked this photo as inappropriate', 'wp-photo-album-plus') . '"' .
									' >' .
								'</span>'
							);
				}
			}

			// Text left if no avg rating
			if ( ! wppa_switch( 'show_avg_rating') ) {
				wppa_out( __('My&nbsp;rating', 'wp-photo-album-plus').':&nbsp;' );
			}

			// Display the my rating stars
			$i = '1';
			while ( $i <= wppa_opt( 'rating_max' ) ) {
				wppa_out( 	'<img' .
								' id="wppa-rate-'.wppa( 'mocc' ).'-'.$i.'"' .
								' class="wppa-rate-'.wppa( 'mocc' ).' no-shadow"' .
								$style .
								' src="'.wppa_get_imgdir().$icon.'"' .
								' alt="'.$i.'"' .
								' title="'.__('My&nbsp;rating', 'wp-photo-album-plus').': '.$r[$i].'"' .
								' onmouseover="wppaFollowMe('.wppa( 'mocc' ).', '.$i.')"' .
								' onmouseout="wppaLeaveMe('.wppa( 'mocc' ).', '.$i.')"' .
								' onclick="wppaRateIt('.wppa( 'mocc' ).', '.$i.')"' .
							' />'
						);
				$i++;
			}

			// Text right if avg rating diaplayed
			if ( wppa_switch( 'show_avg_rating' ) ) {
				wppa_out( '&nbsp;'.'<span id="wppa-my-rat-'.wppa( 'mocc' ).'">'.__('My&nbsp;rating', 'wp-photo-album-plus').'</span>' );
			}
		}
		else {
			if ( wppa_switch( 'login_links' ) ) {
				wppa_out( sprintf(__( 'You must <a href="%s">login</a> to vote' , 'wp-photo-album-plus'), site_url('wp-login.php', 'login')) );
			}
			else {
				wppa_out( __( 'You must login to vote' , 'wp-photo-album-plus') );
			}
		}
	}

	// display_type = numeric?
	elseif ( wppa_opt( 'rating_display_type' ) == 'numeric' ) {

		// Display avg rating
		if ( wppa_switch( 'show_avg_rating' ) ) {
			wppa_out( 	__('Average&nbsp;rating', 'wp-photo-album-plus').':&nbsp;' .
						'<span id="wppa-numrate-avg-'.wppa( 'mocc' ).'"></span>' .
						' &bull;'
					);
		}

		// Display my rating
		// Logged in or don't care
		if ( ! wppa_switch( 'rating_login' ) || is_user_logged_in() ) {

			// Show dislike icon?
			$pad = round( ( wppa_opt( 'ratspacing' ) - $fs ) / 2 );
			if ( $pad < 5 ) $pad = '5';
			if ( wppa_opt( 'dislike_mail_every') ) {
				wppa_out(	'<div' .
								' id="wppa-dislike-imgdiv-'.wppa( 'mocc' ).'"' .
								' style="display:inline"' .
								' >' .
								'<img' .
									' id="wppa-dislike-'.wppa( 'mocc' ).'"' .
									' title="'.__('Click this if you do NOT like this image!', 'wp-photo-album-plus').'"' .
									' src="'.wppa_get_imgdir().'thumbdown.png"' .
									' alt="d"' .
									' style="height:'.$fs.'px; margin:0 0 -3px 0; padding:0 '.$pad.'px; box-shadow:none; display:inline;"' .
									' class="no-shadow"' .
									' onmouseover="jQuery(this).stop().fadeTo(100, 1.0)"' .
									' onmouseout="jQuery(this).stop().fadeTo(100, wppaStarOpacity)"' .
									' onclick="if (confirm(\'' .
													__('Are you sure you want to mark this image as inappropriate?', 'wp-photo-album-plus') .
													'\')) wppaRateIt('.wppa( 'mocc' ).', -1)"' .
								' />' .
							'</div>'
						);
				if ( wppa_switch( 'dislike_show_count') ) {
					wppa_out( 	'<span' .
									' id="wppa-discount-'.wppa( 'mocc' ).'"' .
									' style="cursor:default"' .
									' title="'.__('Number of people who marked this photo as inappropriate', 'wp-photo-album-plus').'"' .
									' >' .
								'</span>'
							);
				}
			}

			wppa_out( ' '.__('My rating:', 'wp-photo-album-plus') );
			wppa_out( '<span id="wppa-numrate-mine-' . wppa( 'mocc' ) . '" ></span>' );
		}
		else {
			if ( wppa_switch( 'login_links' ) ) {
				wppa_out( sprintf(__( 'You must <a href="%s">login</a> to vote', 'wp-photo-album-plus' ), site_url( 'wp-login.php', 'login' ) ) );
			}
			else {
				wppa_out( __( 'You must login to vote', 'wp-photo-album-plus' ) );
			}
		}
	}

	// Close rating box
	wppa_out( '</div>' );
}

function wppa_slide_filmstrip( $opt = '' ) {

	// A single image slideshow needs no navigation
	if ( wppa( 'is_single' ) ) return;

	$do_it = false;												// Init
	if ( is_feed() ) $do_it = true;								// feed -> do it to indicate that there is a slideshow
	else {														// Not a feed
		if ( $opt != 'optional' ) $do_it = true;				// not optional -> do it
		else {													// optional
			if ( wppa_switch('wppa_filmstrip') ) {				// optional and option on
				if ( ! wppa( 'is_slideonly' ) ) $do_it = true;	// always except slideonly
			}
			if ( wppa( 'film_on' ) ) $do_it = true;				// explicitly turned on
		}
	}
	if ( ! $do_it ) return;										// Don't do it

	$t = -microtime(true);

	$alb = wppa_get_get( 'album' );

	$thumbs = wppa_get_thumbs();
	if ( ! $thumbs || count( $thumbs ) < 1 ) return;

	$preambule = wppa_get_preambule();

	$width 		= ( wppa_opt( 'film_thumbsize' ) + wppa_opt( 'tn_margin' ) ) * ( count( $thumbs ) + 2 * $preambule );
	$width 		+= wppa_opt( 'tn_margin' ) + 2;
	$topmarg 	= wppa_opt( 'film_thumbsize' ) / 2 - 16;
	$height 	= wppa_opt( 'film_thumbsize' ) + wppa_opt( 'tn_margin' );
	$height1 	= wppa_opt( 'film_thumbsize' );
	$marg 		= '42';	// 32
	$fs 		= '24';
	$fw 		= '42';

	if ( wppa_in_widget() ) {
		$width 		/= 2;
		$topmarg 	/= 2;
		$height 	/= 2;
		$height1 	/= 2;
		$marg 		= '21';
		$fs 		= '12';
		$fw 		= '21';
	}

	$conw = wppa_get_container_width();
	if ( $conw < 1 ) $conw *= 640;
	$w = $conw - ( 2*6 + 2*42 + 2*wppa_opt( 'bwidth' ) ); /* 2*padding + 2*arrows + 2*border */
	if ( wppa_in_widget() ) $w = $conw - ( 2*6 + 2*21 + 2*wppa_opt( 'bwidth' ) ); /* 2*padding + 2*arrow + 2*border */
	$IE6 = 'width: '.$w.'px;';
	$pagsiz = round( $w / ( wppa_opt( 'film_thumbsize' ) + wppa_opt( 'tn_margin' ) ) );
	if ( wppa_in_widget() ) $pagsiz = round( $w / ( wppa_opt( 'film_thumbsize' ) / 2 + wppa_opt( 'tn_margin' ) / 2 ) );

	wppa_add_js_page_data( '<script type="text/javascript">' );
	wppa_add_js_page_data( 'wppaFilmPageSize['.wppa( 'mocc' ).'] = '.$pagsiz.';' );
	wppa_add_js_page_data( '</script>' );

	if ( is_feed() ) {
		wppa_out( '<div style="'.__wcs('wppa-box').__wcs('wppa-nav').'">' );
	}
	else {

	wppa_out( 	'<div' .
					' class="wppa-box wppa-nav"' .
					' style="text-align:center; '.__wcs('wppa-box').__wcs('wppa-nav').'height:'.$height.'px;"' .
					' >' .
					'<div' .
						' class="wppa-fs-arrow-cont-'.wppa( 'mocc' ).'"' .
						' style="float:left; text-align:left; cursor:pointer; margin-top:'.$topmarg.'px; width: '.$fw.'px; font-size: '.$fs.'px;"' .
						' >' .
						'<a' .
							' class="wppa-prev-'.wppa( 'mocc' ).' wppa-arrow"' .
							' style="'.__wcs('wppa-arrow').'"' .
							' id="prev-film-arrow-'.wppa( 'mocc' ).'"' .
							' onclick="wppaFirst('.wppa( 'mocc' ).');"' .
							' title="'.__('First', 'wp-photo-album-plus').'"' .
							' >' .
							'&laquo;' .
						'</a>' .
						'<a' .
							' class="wppa-prev-'.wppa( 'mocc' ).' wppa-arrow"' .
							' style="'.__wcs('wppa-arrow').'"' .
							' id="prev-film-arrow-1-'.wppa( 'mocc' ).'"' .
							' onclick="wppaPrev('.wppa( 'mocc' ).');"' .
							' title="'.__('Previous', 'wp-photo-album-plus').'"' .
							' >' .
							'&lsaquo;' .
						'</a>' .
					'</div>' .
					'<div' .
						' class="wppa-fs-arrow-cont-'.wppa( 'mocc' ).'"' .
						' style="float:right; text-align:right; cursor:pointer; margin-top:'.$topmarg.'px; width: '.$fw.'px; font-size: '.$fs.'px;"' .
						' >' .
						'<a' .
							' class="wppa-next-'.wppa( 'mocc' ).' wppa-arrow"' .
							' style="'.__wcs('wppa-arrow').'"' .
							' id="next-film-arrow-1-'.wppa( 'mocc' ).'"' .
							' onclick="wppaNext('.wppa( 'mocc' ).');"' .
							' title="'.__('Next', 'wp-photo-album-plus').'"' .
							' >' .
							'&rsaquo;' .
						'</a>' .
						'<a' .
							' class="wppa-next-'.wppa( 'mocc' ).' wppa-arrow"' .
							' style="'.__wcs('wppa-arrow').'"' .
							' id="next-film-arrow-'.wppa( 'mocc' ).'"' .
							' onclick="wppaLast('.wppa( 'mocc' ).');"' .
							' title="'.__('Last', 'wp-photo-album-plus').'"' .
							' >' .
							'&raquo;' .
						'</a>' .
					'</div>' .
					'<div' .
						' id="filmwindow-'.wppa( 'mocc' ).'"' .
						' class="filmwindow"' .
						' style="'.$IE6.' position:absolute; display: block; height:'.$height.'px; margin: 0 0 0 '.$marg.'px; overflow:hidden;"' .
						' >' .
						'<div' .
							' id="wppa-filmstrip-'.wppa( 'mocc' ).'"' .
							' style="height:'.$height1.'px; width:'.$width.'px; max-width:'.$width.'px;margin-left: -100px;"' .
							' >'
			);
	}

	$cnt 	= count( $thumbs );
	$start 	= $cnt - $preambule;
	$end 	= $cnt;
	$idx 	= $start;

	// Preambule
	while ( $idx < $end ) {
		$glue 	= $cnt == ( $idx + 1 ) ? true : false;
		$ix 	= $idx;
		while ( $ix < 0 ) {
			$ix += $cnt;
		}
		$thumb = $thumbs[$ix];
		wppa_do_filmthumb( $thumb['id'], $ix, false, $glue );
		$idx++;
	}

	// Real thumbs
	$idx = 0;
	foreach ( $thumbs as $tt ) : $thumb = $tt;
		$glue = $cnt == ( $idx + 1 ) ? true : false;
		wppa_do_filmthumb( $thumb['id'], $idx, true, $glue );
		$idx++;
	endforeach;

	// Postambule
	$start = '0';
	$end = $preambule;
	$idx = $start;
	while ( $idx < $end ) {
		$ix = $idx;
		while ( $ix >= $cnt ) $ix -= $cnt;
		$thumb = $thumbs[$ix];
		wppa_do_filmthumb( $thumb['id'], $ix, false );
		$idx++;
	}

	if ( is_feed() ) {
		wppa_out( '</div>' );
	}
	else {
			wppa_out( '</div>' );
		wppa_out( '</div>' );
	wppa_out( '</div>' );
	}

	$t += microtime(true);
	wppa_dbg_msg( 'Filmstrip took '.$t.' seconds.' );
}

function wppa_startstop_icons() {

	// Do they need us?
	if ( ! wppa_switch( 'show_start_stop_icons' ) ) {
		return;
	}

	$size = wppa_opt( 'start_pause_symbol_size' );
	$curv = wppa_opt( 'start_pause_symbol_bradius' );

	// Create and output the html
	wppa_out( 	'<img' .
					' id="wppa-startstop-icon-' . wppa( 'mocc' ) . '"' .
					' src="' . ( wppa_opt( 'wppa_start_symbol_url' ) ? wppa_opt( 'wppa_start_symbol_url' ) : wppa_get_imgdir( 'start.png' ) ) . '"' .
					' alt="start stop"' .
					' style="' .
						'position:absolute;' .
						'left:50%;' .
						'margin-left:' . ( - $size / '2' ) . 'px;' .
						'top:50%;' .
						'margin-top:' . ( - $size / '2' ) . 'px;' .
						'z-index:90;' .
						'width:' . $size . 'px;' .
						'border-radius:' . $curv . 'px;' .
						'opacity:0.8;' .
						'cursor:pointer;' .
						'box-shadow:none;' .
						'"' .
					' onclick="wppaStartStop( ' . wppa( 'mocc' ) . ', -1 );"' .
					' onmouseover="jQuery(this).fadeTo(200,0.8);"' .
					' onmouseout="jQuery(this).fadeTo(200,0);"' .
					' ontouchstart="jQuery(this).fadeTo(200,0.8);"' .
					' ontouchend="wppaStartStop( ' . wppa( 'mocc' ) . ', -1 );"' .
					' onload="jQuery(this).fadeTo(1000,0);"' .
					' />' );
}

function wppa_numberbar( $opt = '' ) {

	// A single image slideshow needs no navigation
	if ( wppa( 'is_single' ) ) return;

	if ( is_feed() ) return;

    $do_it = false;
    if ( wppa_switch( 'show_slideshownumbar') && ! wppa( 'is_slideonly' ) ) $do_it = true;
	if ( wppa( 'numbar_on' ) ) $do_it = true;
	if( ! $do_it ) return;

	// get the data
	$thumbs = wppa_get_thumbs();
	if ( empty( $thumbs ) ) return;

	// get the sizes
	$size_given = is_numeric( wppa_opt( 'fontsize_numbar' ) );
	if ( $size_given ) {
		$size = wppa_opt( 'fontsize_numbar' );
		if ( wppa_in_widget() ) $size /= 2;
	}
	else {
		$size = wppa_in_widget() ? '9' : '12';
	}
	if ( $size < '9') $size = '9';
	$size_2  = floor( $size / 2) ;
	$size_4  = floor( $size_2 / 2 );
	$size_32 = floor( $size * 3 / 2 );

	// make the numbar style
	$style = 'position:absolute; bottom:'.$size.'px; right:0; margin-right:'.$size_2.'px; ';

	// start the numbar
	wppa_out( '<div class="wppa-numberbar" style="'.$style.'">' );
		$numid = 0;

		// make the elementstyles
		$style = 	'display:block;' .
					'float:left;' .
					'padding:0 ' .
					$size_4 . 'px;' .
					'margin-right:' . $size_2 . 'px;' .
					'font-weight:' . wppa_opt( 'fontweight_numbar' ) . ';';
		if ( wppa_opt( 'fontfamily_numbar' ) ) {
			$style .= 'font-family:' . wppa_opt( 'fontfamily_numbar' ) .';';
		}
		if ( wppa_opt( 'fontcolor_numbar' ) ) {
			$style .= 'color:' . wppa_opt( 'fontcolor_numbar' ) . ';';
		}
		if ( $size_given ) {
			$style .= 'font-size:' . $size . 'px;line-height:' . $size_32 . 'px;';
		}

		$style_active = $style;

		if ( wppa_opt( 'bgcolor_numbar' ) ) {
			$style .= 'background-color:' . wppa_opt( 'bgcolor_numbar' ) . ';';
		}
		if ( wppa_opt( 'bgcolor_numbar_active' ) ) {
			$style_active .= 'background-color:' . wppa_opt( 'bgcolor_numbar_active' ) . ';';
		}
		if ( wppa_opt( 'bcolor_numbar' ) ) {
			$style .= 'border:1px solid ' . wppa_opt( 'bcolor_numbar' ) . ';';
		}
		if ( wppa_opt( 'bcolor_numbar_active' ) ) {
			$style_active .= 'border:1px solid ' . wppa_opt( 'bcolor_numbar_active' ) . ';';
		}

		// if the number of photos is larger than a certain number, only the active ph displays a number, other are dots
		$count = count( $thumbs );
		$high = wppa_opt( 'numbar_max' );

		// do the numbers
		foreach ( $thumbs as $tt ) {
			$title = sprintf( __( 'Photo %s of %s', 'wp-photo-album-plus' ), $numid + '1', $count );
			wppa_out( 	'<a' .
							' id="wppa-numbar-'.wppa( 'mocc' ).'-'.$numid.'"' .
							' title="'.$title.'"' .
							' ' . ($numid == 0 ? ' class="wppa-numbar-current" ' : '') .
							' style="' . ($numid == 0 ? $style_active : $style) . '"' .
							' onclick="wppaGotoKeepState('.wppa( 'mocc' ).',' . $numid . ');return false;"' .
							' >' .
							( $count > $high ? '.' : $numid + 1 ) .
						'</a>'
					);
			$numid++;
		}
	wppa_out( '</div>' );
}

function wppa_browsebar( $opt = '' ) {

	// A single image slideshow needs no navigation
	if ( wppa( 'is_single' ) ) return;

	if ( is_feed() ) return;

	$do_it = false;
	if ( $opt != 'optional' ) $do_it = true;
	if ( ! wppa( 'is_slideonly' ) && wppa_switch( 'show_browse_navigation' ) ) $do_it = true;
	if ( wppa( 'is_slideonly' ) && wppa( 'browse_on' ) ) $do_it = true;

	if ( $do_it ) {
		wppa_out( 	'<div' .
						' id="prevnext2-'.wppa( 'mocc' ).'"' .
						' class="wppa-box wppa-nav wppa-nav-text"' .
						' style="text-align: center; '.__wcs('wppa-box').__wcs('wppa-nav').__wcs('wppa-nav-text').'"' .
						' >' .
						'<a' .
							' id="prev-arrow-'.wppa( 'mocc' ).'"' .
							' class="wppa-prev-'.wppa( 'mocc' ).' wppa-nav-text arrow-'.wppa( 'mocc' ).'"' .
							' style="float:left; text-align:left; cursor:pointer; '.__wcs('wppa-nav-text').'" onclick="wppaPrev('.wppa( 'mocc' ).')"' .
							' >' .
						'</a>' .
						'<a' .
							' id="next-arrow-'.wppa( 'mocc' ).'"' .
							' class="wppa-next-'.wppa( 'mocc' ).' wppa-nav-text arrow-'.wppa( 'mocc' ).'"' .
							' style="float:right; text-align:right; cursor:pointer; '.__wcs('wppa-nav-text').'"' .
							' onclick="wppaNext('.wppa( 'mocc' ).')"' .
							' >' .
						'</a>' .
						'<span' .
							' id="counter-'.wppa( 'mocc' ).'"' .
							' class="wppa-nav-text wppa-black"' .
							' style="text-align:center; '.__wcs('wppa-nav-text').'; cursor:pointer;"' .
							' onclick="wppaStartStop('.wppa( 'mocc' ).', -1);"' .
							' title="'.__('Click to start/stop', 'wp-photo-album-plus').'"' .
							' >' .
						'</span>' .
					'</div>'
				);
	}
}

function wppa_comments( $opt = '' ) {

	if ( is_feed() ) {
		if ( wppa_switch( 'show_comments' ) ) {
			wppa_dummy_bar( __( '- - - Comments box activated - - -', 'wp-photo-album-plus' ) );
			return;
		}
	}

	$do_it = false;
	if ( $opt != 'optional' ) $do_it = true;
	if ( ! wppa( 'is_slideonly' ) && wppa_switch( 'show_comments' ) && ! wppa_in_widget() ) $do_it = true;

	if ( $do_it ) {
		wppa_out( 	'<div' .
						' id="wppa-comments-'.wppa( 'mocc' ).'"' .
						' class="wppa-box wppa-comments"' .
						' style="text-align: center; '.__wcs('wppa-box').__wcs('wppa-comments').'"' .
						' >' .
					'</div>'
				);
	}
}

function wppa_iptc( $opt = '' ) {

	if ( is_feed() ) {
		if ( wppa_switch( 'show_iptc' ) ) {
			wppa_dummy_bar( __( '- - - IPTC box activated - - -', 'wp-photo-album-plus' ) );
		}
		return;
	}

	$do_it = false;
	if ( $opt != 'optional' ) $do_it = true;
	if ( ! wppa( 'is_slideonly' ) && wppa_switch( 'show_iptc' ) && ! wppa_in_widget() ) $do_it = true;

	if ( $do_it ) {
		wppa_out( 	'<div' .
						' id="iptc-'.wppa( 'mocc' ).'"' .
						' class="wppa-box wppa-box-text wppa-iptc"' .
						' style="text-align: center; '.__wcs('wppa-box').__wcs('wppa-box-text').__wcs('wppa-iptc').'"' .
						' >' .
					'</div>'
				);
	}
}

function wppa_exif( $opt = '' ) {

	if ( is_feed() ) {
		if ( wppa_switch( 'show_exif' ) ) {
			wppa_dummy_bar( __( '- - - EXIF box activated - - -', 'wp-photo-album-plus' ) );
		}
		return;
	}

	$do_it = false;
	if ( $opt != 'optional' ) $do_it = true;
	if ( ! wppa( 'is_slideonly' ) && wppa_switch( 'show_exif' ) && ! wppa_in_widget() ) $do_it = true;

	if ( $do_it ) {
		wppa_out( 	'<div' .
						' id="exif-'.wppa( 'mocc' ).'"' .
						' class="wppa-box wppa-box-text wppa-exif"' .
						' style="text-align: center; '.__wcs('wppa-box').__wcs('wppa-box-text').__wcs('wppa-exif').'"' .
						' >' .
					'</div>'
				);
	}
}

function wppa_share( $opt = '' ) {

	if ( is_feed() ) {
		return;
	}

	$do_it = false;
	if ( $opt != 'optional' ) $do_it = true;
	if ( ! wppa( 'is_slideonly' ) ) {
		if ( wppa_switch( 'share_on') && ! wppa_in_widget() ) $do_it = true;
		if ( wppa_switch( 'share_on_widget') && wppa_in_widget() ) $do_it = true;
	}

	if ( $do_it ) {
		wppa_out( 	'<div' .
						' id="wppa-share-'.wppa( 'mocc' ).'"' .
						' class="wppa-box wppa-box-text wppa-share"' .
						' style="text-align: center; '.__wcs('wppa-box').__wcs('wppa-box-text').__wcs('wppa-share').'"' .
						' >' .
					'</div>'
				);
	}
}

function wppa_errorbox( $text ) {

	wppa_out( 	'<div' .
					' id="error-'.wppa( 'mocc' ).'"' .
					' class="wppa-box wppa-box-text wppa-nav wppa-errorbox"' .
					' style="text-align: center; '.__wcs('wppa-box').__wcs('wppa-box-text').__wcs('wppa-nav').'"' .
					' >' .
					$text .
				'</div>'
			);
}