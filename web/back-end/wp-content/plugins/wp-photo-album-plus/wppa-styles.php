<?php
/* wppa-styles.php
/* Package: wp-photo-album-plus
/*
/* Various style computation routines
/* Version 6.3.12
/*
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

// Create dynamic css file
// This function creates the dynamic css file with styles that depend on the settings.
// Any updates to this routine must also be implemented in __wcs()
/* If you add something that uses an element from $wppa_opt[], */
/* or a function that uses an element from $wppa_opt[], */
/* add the optionslug to $dynamic_css_critical[] in wppa_update_option in wppa-utils.php!!!!! */
function wppa_create_wppa_dynamic_css() {
global $wppa_dynamic_css_data;

	$header =
'/* -- WPPA+ css
/*
/* Dynamicly Created on ' . date( 'c' ) . '
/*
*/
';

	$content = '
.wppa-box {
	' . ( wppa_opt( 'bwidth' ) > '0' ?
		'border-style: solid; border-width:' . wppa_opt( 'bwidth' ) . 'px; '
		: '' ) . '
	' . ( wppa_opt( 'bradius' ) > '0' ?
		'border-radius:' . wppa_opt( 'bradius' ) . 'px; -moz-border-radius:' .
			wppa_opt( 'bradius' ) . 'px; -khtml-border-radius:' .
			wppa_opt( 'bradius' ) . 'px; -webkit-border-radius:' .
			wppa_opt( 'bradius' ) . 'px; ' :
		'' ) . '
	' . ( wppa_opt( 'box_spacing' ) ?
		'margin-bottom:' . wppa_opt( 'box_spacing' ) . 'px; ' :
		'' ) . '
}';

	$content .= '
.wppa-mini-box {
	' . ( wppa_opt( 'bwidth' ) > '0' ?
		'border-style: solid; border-width:' . floor( ( wppa_opt( 'bwidth' ) + 2 ) / 3 ) . 'px; ' :
		'' ) . '
	' . ( wppa_opt( 'bradius' ) > '0' ?
		'border-radius:' . floor( ( wppa_opt( 'bradius' ) + 2 ) / 3 ) . 'px; -moz-border-radius:' .
			floor( ( wppa_opt( 'bradius' ) + 2 ) / 3 ) . 'px; -khtml-border-radius:' .
			floor( ( wppa_opt( 'bradius' ) + 2 ) / 3 ) . 'px; -webkit-border-radius:' .
			floor( ( wppa_opt( 'bradius' ) + 2 ) / 3 ) . 'px; ' :
		'' ) . '
}';

	$content .= '
.wppa-cover-box {
	' . ( wppa_opt( 'cover_minheight' ) ?
		'min-height:' . wppa_opt( 'cover_minheight' ) . 'px; ' :
		'' ) . '
}';

	$content .= '
.wppa-cover-text-frame {
	' . ( wppa_opt( 'head_and_text_frame_height' ) ?
		'min-height:' . wppa_opt( 'head_and_text_frame_height' ) . 'px; ' :
		'' ) . '
}';

	$content .= '
.wppa-box-text {
	' . ( wppa_opt( 'fontcolor_box' ) ?
		'color:' . wppa_opt( 'fontcolor_box' ) . '; ' :
		'' ) . '
}
.wppa-box-text, .wppa-box-text-nocolor {
	' . ( wppa_opt( 'fontfamily_box' ) ?
		'font-family:' . wppa_opt( 'fontfamily_box' ) . '; ' :
		'' ) . '
	' . ( wppa_opt( 'fontsize_box' ) ?
		'font-size:' . wppa_opt( 'fontsize_box' ) . 'px; ' :
		'' ) . '
	' . ( wppa_opt( 'fontweight_box' ) ?
		'font-weight:' . wppa_opt( 'fontweight_box' ) . '; ' :
		'' ) . '
}';

	$content .= '
.wppa-thumb-text {
	' . ( wppa_opt( 'fontfamily_thumb' ) ?
		'font-family:' . wppa_opt( 'fontfamily_thumb' ) . '; ' :
		'' ) . '
	' . ( wppa_opt( 'fontsize_thumb' ) ?
		'font-size:' . wppa_opt( 'fontsize_thumb' ) . 'px; line-height:' .
			floor( wppa_opt( 'fontsize_thumb' ) * 1.29 ) . 'px; ' :
		'' ) . '
	' . ( wppa_opt( 'fontcolor_thumb' ) ?
		'color:' . wppa_opt( 'fontcolor_thumb' ) . '; ' :
		'' ) . '
	' . ( wppa_opt( 'fontweight_thumb' ) ?
		'font-weight:' . wppa_opt( 'fontweight_thumb' ) . '; ' :
		'' ) . '
}';

	$content .= '
.wppa-comments {
	' . ( wppa_opt( 'bgcolor_com' ) ?
		'background-color:' . wppa_opt( 'bgcolor_com' ) . '; ' :
		'' ) . '
	' . ( wppa_opt( 'bcolor_com' ) ?
		'border-color:' . wppa_opt( 'bcolor_com' ) . '; ' :
		'' ) . '
}';

	$content .= '
.wppa-iptc {
	' . ( wppa_opt( 'bgcolor_iptc' ) ?
		'background-color:' . wppa_opt( 'bgcolor_iptc' ) . '; ' :
		'' ) . '
	' . ( wppa_opt( 'bcolor_iptc' ) ?
		'border-color:' . wppa_opt( 'bcolor_iptc' ) . '; ' :
		'' ) . '
}';

	$content .= '
.wppa-exif {
	' . ( wppa_opt( 'bgcolor_exif' ) ? 'background-color:' . wppa_opt( 'bgcolor_exif' ) . '; ' : '' ) . '
	' . ( wppa_opt( 'bcolor_exif' ) ? 'border-color:' . wppa_opt( 'bcolor_exif' ) . '; ' : '' ) . '
}';

	$content .= '
.wppa-share {
	' . ( wppa_opt( 'bgcolor_share' ) ? 'background-color:' . wppa_opt( 'bgcolor_share' ) . '; ' : '' ) . '
	' . ( wppa_opt( 'bcolor_share' ) ? 'border-color:' . wppa_opt( 'bcolor_share' ) . '; ' : '' ) . '
}';

	$content .= '
.wppa-name-desc {
	' . ( wppa_opt( 'bgcolor_namedesc' ) ? 'background-color:' . wppa_opt( 'bgcolor_namedesc' ) . '; ' : '' ) . '
	' . ( wppa_opt( 'bcolor_namedesc' ) ? 'border-color:' . wppa_opt( 'bcolor_namedesc' ) . '; ' : '' ) . '
}';

	$content .= '
.wppa-nav {
	' . ( wppa_opt( 'bgcolor_nav' ) ? 'background-color:' . wppa_opt( 'bgcolor_nav' ) . '; ' : '' ) . '
	' . ( wppa_opt( 'bcolor_nav' ) ? 'border-color:' . wppa_opt( 'bcolor_nav' ) . '; ' : '' ) . '
}';

	$content .= '
.wppa-nav-text {
	' . ( wppa_opt( 'fontfamily_nav' ) ? 'font-family:' . wppa_opt( 'fontfamily_nav' ) . '; ' : '' ) . '
	' . ( wppa_opt( 'fontsize_nav' ) ? 'font-size:' . wppa_opt( 'fontsize_nav' ) . 'px; ' : '' ) . '
	' . ( wppa_opt( 'fontcolor_nav' ) ? 'color:' . wppa_opt( 'fontcolor_nav' ) . '; ' : '' ) . '
	' . ( wppa_opt( 'fontweight_nav' ) ? 'font-weight:' . wppa_opt( 'fontweight_nav' ) . '; ' : '' ) . '
}';

	$content .= '
.wppa-even {
	' . ( wppa_opt( 'bgcolor_even' ) ? 'background-color:' . wppa_opt( 'bgcolor_even' ) . '; ' : '' ) . '
	' . ( wppa_opt( 'bcolor_even' ) ? 'border-color:' . wppa_opt( 'bcolor_even' ) . '; ' : '' ) . '
}';

	$content .= '
.wppa-alt {
	' . ( wppa_opt( 'bgcolor_alt' ) ? 'background-color:' . wppa_opt( 'bgcolor_alt' ) . '; ' : '' ) . '
	' . ( wppa_opt( 'bcolor_alt' ) ? 'border-color:' . wppa_opt( 'bcolor_alt' ) . '; ' : '' ) . '
}';

	$content .= '
.wppa-img {
	' . ( wppa_opt( 'bgcolor_img' ) ? 'background-color:' . wppa_opt( 'bgcolor_img' ) . '; ' : '' ) . '
}';

	$content .= '
.wppa-title {
	' . ( wppa_opt( 'fontfamily_title' ) ? 'font-family:' . wppa_opt( 'fontfamily_title' ) . '; ' : '' ) . '
	' . ( wppa_opt( 'fontsize_title' ) ? 'font-size:' . wppa_opt( 'fontsize_title' ) . 'px; ' : '' ) . '
	' . ( wppa_opt( 'fontcolor_title' ) ? 'color:' . wppa_opt( 'fontcolor_title' ) . '; ' : '' ) . '
	' . ( wppa_opt( 'fontweight_title' ) ? 'font-weight:' . wppa_opt( 'fontweight_title' ) . '; ' : '' ) . '
}';

	$content .= '
.wppa-fulldesc {
	' . ( wppa_opt( 'fontfamily_fulldesc' ) ? 'font-family:' . wppa_opt( 'fontfamily_fulldesc' ) . '; ' : '' ) . '
	' . ( wppa_opt( 'fontsize_fulldesc' ) ? 'font-size:' . wppa_opt( 'fontsize_fulldesc' ) . 'px; ' : '' ) . '
	' . ( wppa_opt( 'fontcolor_fulldesc' ) ? 'color:' . wppa_opt( 'fontcolor_fulldesc' ) . '; ' : '' ) . '
	' . ( wppa_opt( 'fontweight_fulldesc' ) ? 'font-weight:' . wppa_opt( 'fontweight_fulldesc' ) . '; ' : '' ) . '
}';

	$content .= '
.wppa-fulltitle {
	' . ( wppa_opt( 'fontfamily_fulltitle' ) ? 'font-family:' . wppa_opt( 'fontfamily_fulltitle' ) . '; ' : '' ) . '
	' . ( wppa_opt( 'fontsize_fulltitle' ) ? 'font-size:' . wppa_opt( 'fontsize_fulltitle' ) . 'px; ' : '' ) . '
	' . ( wppa_opt( 'fontcolor_fulltitle' ) ? 'color:' . wppa_opt( 'fontcolor_fulltitle' ) . '; ' : '' ) . '
	' . ( wppa_opt( 'fontweight_fulltitle' ) ? 'font-weight:' . wppa_opt( 'fontweight_fulltitle' ) . '; ' : '' ) . '
}';

	$content .= '
.wppa-custom {
	' . ( wppa_opt( 'bgcolor_cus' ) ? 'background-color:' . wppa_opt( 'bgcolor_cus' ) . '; ' : '' ) . '
	' . ( wppa_opt( 'bcolor_cus' ) ? 'border-color:' . wppa_opt( 'bcolor_cus' ) . '; ' : '' ) . '
}';

	$content .= '
.wppa-upload {
	' . ( wppa_opt( 'bgcolor_upload' ) ? 'background-color:' . wppa_opt( 'bgcolor_upload' ) . '; ' : '' ) . '
	' . ( wppa_opt( 'bcolor_upload' ) ? 'border-color:' . wppa_opt( 'bcolor_upload' ) . '; ' : '' ) . '
}';

	$content .= '
.wppa-multitag {
	' . ( wppa_opt( 'bgcolor_multitag' ) ? 'background-color:' . wppa_opt( 'bgcolor_multitag' ) . '; ' : '' ) . '
	' . ( wppa_opt( 'bcolor_multitag' ) ? 'border-color:' . wppa_opt( 'bcolor_multitag' ) . '; ' : '' ) . '
}';

	$content .= '
.wppa-bestof {
	' . ( wppa_opt( 'bgcolor_bestof' ) ? 'background-color:' . wppa_opt( 'bgcolor_bestof' ) . '; ' : '' ) . '
	' . ( wppa_opt( 'bcolor_bestof' ) ? 'border-color:' . wppa_opt( 'bcolor_bestof' ) . '; ' : '' ) . '
}';

	$content .= '
.wppa-stereo {
	' . ( wppa_opt( 'bgcolor_stereo' ) ? 'background-color:' . wppa_opt( 'bgcolor_stereo' ) . '; ' : '' ) . '
	' . ( wppa_opt( 'bcolor_stereo' ) ? 'border-color:' . wppa_opt( 'bcolor_stereo' ) . '; ' : '' ) . '
}';

	$content .= '
.wppa-tagcloud {
	' . ( wppa_opt( 'bgcolor_tagcloud' ) ? 'background-color:' . wppa_opt( 'bgcolor_tagcloud' ) . '; ' : '' ) . '
	' . ( wppa_opt( 'bcolor_tagcloud' ) ? 'border-color:' . wppa_opt( 'bcolor_tagcloud' ) . '; ' : '' ) . '
}';

	$content .= '
.wppa-superview {
	' . ( wppa_opt( 'bgcolor_superview' ) ? 'background-color:' . wppa_opt( 'bgcolor_superview' ) . '; ' : '' ) . '
	' . ( wppa_opt( 'bcolor_superview' ) ? 'border-color:' . wppa_opt( 'bcolor_superview' ) . '; ' : '' ) . '
}';

	$content .= '
.wppa-search {
	' . ( wppa_opt( 'bgcolor_search' ) ? 'background-color:' . wppa_opt( 'bgcolor_search' ) . '; ' : '' ) . '
	' . ( wppa_opt( 'bcolor_search' ) ? 'border-color:' . wppa_opt( 'bcolor_search' ) . '; ' : '' ) . '
}';

	$content .= '
.wppa-calendar {
	' . ( wppa_opt( 'bgcolor_calendar' ) ? 'background-color:' . wppa_opt( 'bgcolor_calendar' ) . '; ' : '' ) . '
	' . ( wppa_opt( 'bcolor_calendar' ) ? 'border-color:' . wppa_opt( 'bcolor_calendar' ) . '; ' : '' ) . '
}';

	$content .= '
.wppa-arrow {
	' . ( wppa_opt( 'arrow_color' ) ? 'color:' . wppa_opt( 'arrow_color' ) . '; ' : '' ) . '
}';

	// Add miscellaneous styles
	if ( ! wppa_switch( 'ovl_fs_icons' ) ) {
		$content .= '
#wppa-norms-btn, #wppa-fulls-btn { display:none; }';
	}

	// Add custom style
	$content .= wppa_opt( 'custom_style' );

	// Open file
	$file = @ fopen ( WPPA_PATH . '/wppa-dynamic.css', 'wb' );
	if ( $file ) {
		// Minify
		$old_len = strlen( $content );
		$new_len = '0';
		while ( $old_len != $new_len ) {
			$content = str_replace( '  ', ' ', $content );
			$old_len = $new_len;
			$new_len = strlen( $content );
		}
		$content = str_replace( "\t", '', $content );
		$content = str_replace( "\n", '', $content );
		$content = str_replace( ' }', '}', $content );
		$content = str_replace( '} ', '}', $content );
		$content = str_replace( ' {', '{', $content );
		$content = str_replace( '{ ', '{', $content );
		$content = str_replace( ' ;', ';', $content );
		$content = str_replace( '; ', ';', $content );
		$content = str_replace( ' :', ':', $content );
		$content = str_replace( ': ', ':', $content );
		$content = str_replace( '}', "}\n", $content );
		$content = str_replace( '*/', "*/\n", $content );
		$content = str_replace( ",\n", ',', $content );
		// Write file
		fwrite ( $file, $header . $content );
		// Close file
		fclose ( $file );
		$wppa_dynamic_css_data = '';
	}
	else {
		$wppa_dynamic_css_data =
'<style type="text/css">
/* Warning: file wppa-dynamic.css could not be created */
/* The content is therefor output here */

' . $content . '
</style>
';
	}
}

// get full img style
function wppa_get_fullimgstyle( $id ) {

	$temp = wppa_get_fullimgstyle_a( $id );

	if ( is_array( $temp ) ) {
		return $temp['style'];
	}
	else {
		return '';
	}
}

// get full img style - array output
function wppa_get_fullimgstyle_a( $id ) {

	if ( ! is_numeric( wppa( 'fullsize' ) ) || wppa( 'fullsize' ) <= '1' ) {
		wppa( 'fullsize', wppa_opt( 'fullsize' ) );
	}

	wppa( 'enlarge', wppa_switch( 'enlarge' ) );

	return wppa_get_imgstyle_a( $id, wppa_get_photo_path( $id ), wppa( 'fullsize' ), 'optional', 'fullsize' );
}

// Image style array output
function wppa_get_imgstyle_a( $id, $file, $xmax_size, $xvalign = '', $type = '' ) {

	$result = Array(
					'style' 		=> '',
					'width' 		=> '',
					'height' 		=> '',
					'cursor' 		=> '',
					'margin-top' 	=> '',
					'margin-bottom' => ''
					 );	// Init

	wppa_cache_thumb( $id );

	if ( ! $id ) return $result;						// no image: no dimensions
	if ( $file == '' ) return $result;					// no image: no dimensions

	if ( wppa_has_audio( $id ) ) {
		$file = wppa_fix_poster_ext( $file, $id );
	}
	if ( ! wppa_is_video( $id ) && ! is_file( $file ) ) {
		wppa_dbg_msg( 'Please check file ' . $file . ' it is missing while expected. Id=' . $id, 'red' );
		return $result;									// no file: no dimensions ( 2.3.0 )
	}

	if ( strpos( $file, '/wppa/thumbs/' ) ) {
		$image_attr = wppa_get_imagexy( $id, 'thumb' );
	}
	else {
		$image_attr = wppa_get_imagexy( $id, 'photo' );
	}
//	if ( wppa_is_video( $id ) ) {
//		$image_attr = array( '0' => wppa_get_videox( $id ), '1' => wppa_get_videoy( $id ) );
//	}
//	else {
//		$image_attr = array( '0' => wppa_get_photox( $id ), '1' => wppa_get_photoy( $id ) );
//	}
	if (
			! $image_attr ||
			! isset( $image_attr['0'] ) ||
			! $image_attr['0'] ||
			! isset( $image_attr['1'] ) ||
			! $image_attr['1'] ) {

		// File is corrupt
		wppa_dbg_msg( 'Please check file ' . $file . ' it is corrupted. If it is a thumbnail image,' .
			' regenerate them using Table VIII-A4 of the Photo Albums -> Settings admin page.',
			'red' );
		return $result;
	}

	// Adjust for 'border'
	if ( $type == 'fullsize' && ! wppa_in_widget() ) {
		switch ( wppa_opt( 'fullimage_border_width' ) ) {
			case '':
				$max_size = $xmax_size;
				break;
			case '0':
				$max_size = $xmax_size - '2';
				break;
			default:
				$max_size = $xmax_size - '2' - 2 * wppa_opt( 'fullimage_border_width' );
			}
	}
	else $max_size = $xmax_size;

	$ratioref = wppa_opt( 'maxheight' ) / wppa_opt( 'fullsize' );
	$max_height = round( $max_size * $ratioref );

	if ( $type == 'fullsize' ) {
		if ( wppa( 'portrait_only' ) ) {
			$width = $max_size;
			$height = round( $width * $image_attr[1] / $image_attr[0] );
		}
		else {
			if ( wppa_is_wider( $image_attr[0], $image_attr[1] ) ) {
				$width = $max_size;
				$height = round( $width * $image_attr[1] / $image_attr[0] );
			}
			else {
				$height = round( $ratioref * $max_size );
				$width = round( $height * $image_attr[0] / $image_attr[1] );
			}
			if ( $image_attr[0] < $width && $image_attr[1] < $height ) {
				if ( ! wppa( 'enlarge' ) ) {
					$width = $image_attr[0];
					$height = $image_attr[1];
				}
			}
		}
	}
	else {
		if ( $type == 'cover' &&
			wppa_switch( 'coversize_is_height' ) &&
			( wppa_opt('wppa_coverphoto_pos') == 'top' || wppa_opt('wppa_coverphoto_pos') == 'bottom' )
			 ) {
				$height = $max_size;
				$width = round( $max_size * $image_attr[0] / $image_attr[1] );
		}
		else {
			if ( wppa_is_landscape( $image_attr ) ) {
				$width = $max_size;
				$height = round( $max_size * $image_attr[1] / $image_attr[0] );
			}
			else {
				$height = $max_size;
				$width = round( $max_size * $image_attr[0] / $image_attr[1] );
			}
		}
	}

	switch ( $type ) {
		case 'cover':
			if ( wppa_opt( 'bcolor_img' ) != '' ) { 		// There is a border color given
				$result['style'] .= ' border: 1px solid ' . wppa_opt( 'bcolor_img' ) . ';';
			}
			else {											// No border color: no border
				$result['style'] .= ' border-width: 0px;';
			}
			$result['style'] .= ' width:' . $width . 'px; height:' . $height . 'px;';
			if ( wppa_switch( 'use_cover_opacity' ) && ! is_feed() ) {
				$opac = wppa_opt( 'cover_opacity' );
				$result['style'] .= ' opacity:' . $opac/100 .
					'; filter:alpha( opacity=' . $opac . ' );';
			}
			if ( wppa_opt( 'coverimg_linktype' ) == 'lightbox' ) {
				$result['cursor'] =
					' cursor:url( ' .wppa_get_imgdir() . wppa_opt( 'magnifier' ) . ' ),pointer;';
			}
			break;

		case 'thumb':		// Normal
		case 'ttthumb':		// Topten
		case 'comthumb':	// Comment widget
		case 'fthumb':		// Filmthumb
		case 'twthumb':		// Thumbnail widget
		case 'ltthumb':		// Lasten widget
		case 'albthumb':	// Album widget
			if ( $type == 'thumb' && wppa_get_get( 'hilite' ) == $id ) {
				$result['style'] .= ' border:2px solid blue;box-sizing:border-box;';
			}
			else {
				$result['style'] .= ' border-width: 0px;';
			}
			$result['style'] .= ' width:' . $width . 'px; height:' . $height . 'px;';
			if ( $xvalign == 'optional' ) $valign = wppa_opt( 'valign' );
			else $valign = $xvalign;
			if ( $valign != 'default' ) {	// Center horizontally
				$delta = floor( ( $max_size - $width ) / 2 );
				if ( is_numeric( $valign ) ) $delta += $valign;
				if ( $delta < '0' ) {
					$delta = '0';
				}
				if ( $delta > '0' ) {
					$result['style'] .= ' margin-left:' . $delta .
						'px; margin-right:' . $delta . 'px;';
				}
			}

			switch ( $valign ) {
				case 'top':
					$delta = $max_size - $height;
					if ( $delta < '0' ) $delta = '0';
					$result['style'] .= ' margin-bottom: ' . $delta . 'px;';
					$result['margin-bottom'] = $delta;
					break;
				case 'center':
					$delta = round( ( $max_size - $height ) / 2 );
					if ( $delta < '0' ) $delta = '0';
					$result['style'] .= ' margin-top: ' . $delta .
						'px; margin-bottom: ' . $delta . 'px;';
					$result['margin-top'] = $delta;
					$result['margin-bottom'] = $delta;
					break;
				case 'bottom':
					$delta = $max_size - $height;
					if ( $delta < '0' ) $delta = '0';
					$result['style'] .= ' margin-top: ' . $delta . 'px;';
					$result['margin-top'] = $delta;
					break;
				default:
					if ( is_numeric( $valign ) ) {
						$delta = $valign;
						$result['style'] .= ' margin-top: ' . $delta . 'px;';
						$result['style'] .= ' margin-bottom: ' . $delta . 'px;';
						$result['margin-top'] = $delta;
						$result['margin-bottom'] = $delta;
					}
			}
			if ( wppa_switch( 'use_thumb_opacity' ) && ! is_feed() ) {
				$opac = wppa_opt( 'thumb_opacity' );
				$result['style'] .=
					' opacity:' . $opac/100 . '; filter:alpha( opacity=' . $opac . ' );';
			}

			// Cursor
			$linktyp = '';
			switch ( $type ) {
				case 'thumb':		// Normal
					$linktyp = wppa_opt( 'thumb_linktype' );
					break;
				case 'ttthumb':		// Topten	v
					$linktyp = wppa_opt( 'topten_widget_linktype' );
					break;
				case 'comthumb':	// Comment widget	v
					$linktyp = wppa_opt( 'comment_widget_linktype' );
					break;
				case 'fthumb':		// Filmthumb
					$linktyp = wppa_opt( 'film_linktype' );
					break;
				case 'twthumb':		// Thumbnail widget	v
					$linktyp = wppa_opt( 'thumbnail_widget_linktype' );
					break;
				case 'ltthumb':		// Lasten widget	v
					$linktyp = wppa_opt( 'lasten_widget_linktype' );
					break;
				case 'albthumb':	// Album widget
					$linktyp = wppa_opt( 'album_widget_linktype' );
			}
			if ( $linktyp == 'none' ) {
				$result['cursor'] = ' cursor:default;';
			}
			elseif ( $linktyp == 'lightbox' ) {
				$result['cursor'] = ' cursor:url(' . wppa_get_imgdir() .
					wppa_opt( 'magnifier' ) . '),pointer;';
			}
			else {
				$result['cursor'] = ' cursor:pointer;';
			}

			break;
		case 'fullsize':
			if ( wppa( 'auto_colwidth' ) ) {

				// These sizes fit within the rectangle define by Table I-B1,2
				// times 2 for responsive themes,
				// and are supplied for ver 4 browsers as they have undefined natural sizes.
				$result['style'] .= ' max-width:' . ( $width * 2 ) . 'px;';
				$result['style'] .= ' max-height:' . ( $height * 2 ) . 'px;';
			}
			else {

				// These sizes fit within the rectangle define by Table I-B1,2
				// and are supplied for ver 4 browsers as they have undefined natural sizes.
				$result['style'] .= ' max-width:' . $width . 'px;';
				$result['style'] .= ' max-height:' . $height . 'px;';

				$result['style'] .= ' width:' . $width . 'px;';
				$result['style'] .= ' height:' . $height . 'px;';
			}

			if ( wppa( 'is_slideonly' ) == '1' ) {
				if ( wppa( 'ss_widget_valign' ) != '' ) $valign = wppa( 'ss_widget_valign' );
				else $valign = 'fit';
			}
			elseif ( $xvalign == 'optional' ) {
				$valign = wppa_opt( 'fullvalign' );
			}
			else {
				$valign = $xvalign;
			}

			// Margin
			if ( $valign != 'default' ) {
				$m_left 	= '0';
				$m_right 	= '0';
				$m_top 		= '0';
				$m_bottom 	= '0';

				// Center horizontally
				$delta = round( ( $max_size - $width ) / 2 );
				if ( $delta < '0' ) $delta = '0';
				if ( wppa( 'auto_colwidth' ) ) {
					$m_left 	= 'auto';
					$m_right 	= 'auto';
				}
				else {
					$m_left 	= $delta;
					$m_right 	= '0';
				}

				// Position vertically
				if ( wppa_in_widget() == 'ss' && wppa( 'in_widget_frame_height' ) > '0' ) {
					$max_height = wppa( 'in_widget_frame_height' );
				}
				$delta = '0';
				if ( ! wppa( 'auto_colwidth' ) && ! wppa_page( 'oneofone' ) ) {
					switch ( $valign ) {
						case 'top':
						case 'fit':
							$delta = '0';
							break;
						case 'center':
							$delta = round( ( $max_height - $height ) / 2 );
							if ( $delta < '0' ) $delta = '0';
							break;
						case 'bottom':
							$delta = $max_height - $height;
							if ( $delta < '0' ) $delta = '0';
							break;
					}
				}
				$m_top = $delta;

				$result['style'] .= wppa_combine_style( 'margin', $m_top, $m_left, $m_right, $m_bottom );
			}

			// Border and padding
			if ( ! wppa_in_widget() ) switch ( wppa_opt( 'fullimage_border_width' ) ) {
				case '':
					break;
				case '0':
					$result['style'] .= ' border: 1px solid ' . wppa_opt( 'bcolor_fullimg' ) . ';';
					break;
				default:
					$result['style'] .= ' border: 1px solid ' . wppa_opt( 'bcolor_fullimg' ) . ';';
					$result['style'] .= ' background-color:' . wppa_opt( 'bgcolor_fullimg' ) . ';';
					$result['style'] .= ' padding:' . wppa_opt( 'fullimage_border_width' ) . 'px;';

					// If we do round corners...
					if ( wppa_opt( 'bradius' ) > '0' ) {	// then also here
						$result['style'] .= ' border-radius:' .
							wppa_opt( 'fullimage_border_width' ) . 'px;';
					}
			}

			break;
		default:
			wppa_out( 'Error wrong "$type" argument: ' . $type . ' in wppa_get_imgstyle_a' );
	}
	$result['width'] = $width;
	$result['height'] = $height;
	return $result;
}

// This function is used to either provide inline styles or hide them,
// dependant of the wppa_inline_css switch.
// Styles should be passed as is . They must be independatnt of settings,
// and appear in the standard wppa-styles.css file.
function __wis( $style ) {

	if ( ! wppa_switch( 'inline_css' ) ) {
		return '';	// No inline styles
	}
	else {
		return $style;
	}
}

// This function returns styles dependant of settings or hides them,
// dependant of the wppa_inline_css switch.
// The class is passed and the corresponding inline styles are returned.
// Any updates to this routine must also be implemented in wppa_create_wppa_dynamic_css().
function __wcs( $class ) {

	if ( ! wppa_switch( 'inline_css' ) ) {
		return '';	// No inline styles
	}

	$opt = '';
	$result = '';

	switch ( $class ) {
		case 'wppa-box':
			$opt = wppa_opt( 'bwidth' );
			if ( $opt > '0' ) {
				$result .= 'border-style: solid; border-width:' . $opt . 'px; ';
			}
			$opt = wppa_opt( 'bradius' );
			if ( $opt > '0' ) {
				$result .= 'border-radius:' . $opt . 'px; ';
				$result .= '-moz-border-radius:' . $opt . 'px; ';
				$result .= '-khtml-border-radius:' . $opt . 'px; ';
				$result .= '-webkit-border-radius:' . $opt . 'px; ';
			}
			$opt = wppa_opt( 'box_spacing' );
			if ( $opt != '' ) {
				$result .= 'margin-bottom:' . $opt . 'px; ';
			}
			break;
		case 'wppa-mini-box':
			$opt = wppa_opt( 'bwidth' );
			if ( $opt > '0' ) {
				$opt = floor( ( $opt + 2 ) / 3 );
				$result .= 'border-style: solid; border-width:' . $opt . 'px; ';
			}
			$opt = wppa_opt( 'bradius' );
			if ( $opt > '0' ) {
				$opt = floor( ( $opt + 2 ) / 3 );
				$result .= 'border-radius:' . $opt . 'px; ';
				$result .= '-moz-border-radius:' . $opt . 'px; ';
				$result .= '-khtml-border-radius:' . $opt . 'px; ';
				$result .= '-webkit-border-radius:' . $opt . 'px; ';
			}
			break;
		case 'wppa-cover-box':
			$opt = wppa_opt( 'cover_minheight' );
			if ( $opt ) $result .= 'min-height:' . $opt . 'px; ';
			break;
		case 'wppa-cover-text-frame':
			$opt = wppa_opt( 'head_and_text_frame_height' );
			if ( $opt ) $result .= 'min-height:' . $opt . 'px; ';
			break;
		case 'wppa-thumb-text':
			$opt = wppa_opt( 'fontfamily_thumb' );
			if ( $opt ) $result .= 'font-family:' . $opt . '; ';
			$opt = wppa_opt( 'fontsize_thumb' );
			if ( $opt ) {
				$ls = floor( $opt * 1.29 );
				$result .= 'font-size:' . $opt . 'px; line-height:' . $ls . 'px; ';
			}
			$opt = wppa_opt( 'fontcolor_thumb' );
			if ( $opt ) $result .= 'color:' . $opt . '; ';
			$opt = wppa_opt( 'fontweight_thumb' );
			if ( $opt ) $result .= 'font-weight:' . $opt . '; ';
			break;
		case 'wppa-box-text':
			$opt = wppa_opt( 'fontcolor_box' );
			if ( $opt ) $result .= 'color:' . $opt . '; ';
		case 'wppa-box-text-nocolor':
			$opt = wppa_opt( 'fontfamily_box' );
			if ( $opt ) $result .= 'font-family:' . $opt . '; ';
			$opt = wppa_opt( 'fontsize_box' );
			if ( $opt ) $result .= 'font-size:' . $opt . 'px; ';
			$opt = wppa_opt( 'fontweight_box' );
			if ( $opt ) $result .= 'font-weight:' . $opt . '; ';
			break;
		case 'wppa-comments':
			$opt = wppa_opt( 'bgcolor_com' );
			if ( $opt ) $result .= 'background-color:' . $opt . '; ';
			$opt = wppa_opt( 'bcolor_com' );
			if ( $opt ) $result .= 'border-color:' . $opt . '; ';
			break;
		case 'wppa-iptc':
			$opt = wppa_opt( 'bgcolor_iptc' );
			if ( $opt ) $result .= 'background-color:' . $opt . '; ';
			$opt = wppa_opt( 'bcolor_iptc' );
			if ( $opt ) $result .= 'border-color:' . $opt . '; ';
			break;
		case 'wppa-exif':
			$opt = wppa_opt( 'bgcolor_exif' );
			if ( $opt ) $result .= 'background-color:' . $opt . '; ';
			$opt = wppa_opt( 'bcolor_exif' );
			if ( $opt ) $result .= 'border-color:' . $opt . '; ';
			break;
		case 'wppa-share':
			$opt = wppa_opt( 'bgcolor_share' );
			if ( $opt ) $result .= 'background-color:' . $opt . '; ';
			$opt = wppa_opt( 'bcolor_share' );
			if ( $opt ) $result .= 'border-color:' . $opt . '; ';
			break;
		case 'wppa-name-desc':
			$opt = wppa_opt( 'bgcolor_namedesc' );
			if ( $opt ) $result .= 'background-color:' . $opt . '; ';
			$opt = wppa_opt( 'bcolor_namedesc' );
			if ( $opt ) $result .= 'border-color:' . $opt . '; ';
			break;
		case 'wppa-nav':
			$opt = wppa_opt( 'bgcolor_nav' );
			if ( $opt ) $result .= 'background-color:' . $opt . '; ';
			$opt = wppa_opt( 'bcolor_nav' );
			if ( $opt ) $result .= 'border-color:' . $opt . '; ';
			break;
		case 'wppa-nav-text':
			$opt = wppa_opt( 'fontfamily_nav' );
			if ( $opt ) $result .= 'font-family:' . $opt . '; ';
			$opt = wppa_opt( 'fontsize_nav' );
			if ( $opt ) $result .= 'font-size:' . $opt . 'px; ';
			$opt = wppa_opt( 'fontcolor_nav' );
			if ( $opt ) $result .= 'color:' . $opt . '; ';
			$opt = wppa_opt( 'fontweight_nav' );
			if ( $opt ) $result .= 'font-weight:' . $opt . '; ';
			break;
		case 'wppa-even':
			$opt = wppa_opt( 'bgcolor_even' );
			if ( $opt ) $result .= 'background-color:' . $opt . '; ';
			$opt = wppa_opt( 'bcolor_even' );
			if ( $opt ) $result .= 'border-color:' . $opt . '; ';
			break;
		case 'wppa-alt':
			$opt = wppa_opt( 'bgcolor_alt' );
			if ( $opt ) $result .= 'background-color:' . $opt . '; ';
			$opt = wppa_opt( 'bcolor_alt' );
			if ( $opt ) $result .= 'border-color:' . $opt . '; ';
			break;
		case 'wppa-img':
			$opt = wppa_opt( 'bgcolor_img' );
			if ( $opt ) $result .= 'background-color:' . $opt . '; ';
			break;
		case 'wppa-title':
			$opt = wppa_opt( 'fontfamily_title' );
			if ( $opt ) $result .= 'font-family:' . $opt . '; ';
			$opt = wppa_opt( 'fontsize_title' );
			if ( $opt ) $result .= 'font-size:' . $opt . 'px; ';
			$opt = wppa_opt( 'fontcolor_title' );
			if ( $opt ) $result .= 'color:' . $opt . '; ';
			$opt = wppa_opt( 'fontweight_title' );
			if ( $opt ) $result .= 'font-weight:' . $opt . '; ';
			break;
		case 'wppa-fulldesc':
			$opt = wppa_opt( 'fontfamily_fulldesc' );
			if ( $opt ) $result .= 'font-family:' . $opt . '; ';
			$opt = wppa_opt( 'fontsize_fulldesc' );
			if ( $opt ) $result .= 'font-size:' . $opt . 'px; ';
			$opt = wppa_opt( 'fontcolor_fulldesc' );
			if ( $opt ) $result .= 'color:' . $opt . '; ';
			$opt = wppa_opt( 'fontweight_fulldesc' );
			if ( $opt ) $result .= 'font-weight:' . $opt . '; ';
			break;
		case 'wppa-fulltitle':
			$opt = wppa_opt( 'fontfamily_fulltitle' );
			if ( $opt ) $result .= 'font-family:' . $opt . '; ';
			$opt = wppa_opt( 'fontsize_fulltitle' );
			if ( $opt ) $result .= 'font-size:' . $opt . 'px; ';
			$opt = wppa_opt( 'fontcolor_fulltitle' );
			if ( $opt ) $result .= 'color:' . $opt . '; ';
			$opt = wppa_opt( 'fontweight_fulltitle' );
			if ( $opt ) $result .= 'font-weight:' . $opt . '; ';
			break;
		case 'wppa-custom':
			$opt = wppa_opt( 'bgcolor_cus' );
			if ( $opt ) $result .= 'background-color:' . $opt . '; ';
			$opt = wppa_opt( 'bcolor_cus' );
			if ( $opt ) $result .= 'border-color:' . $opt . '; ';
			break;
		case 'wppa-upload':
			$opt = wppa_opt( 'bgcolor_upload' );
			if ( $opt ) $result .= 'background-color:' . $opt . '; ';
			$opt = wppa_opt( 'bcolor_upload' );
			if ( $opt ) $result .= 'border-color:' . $opt . '; ';
			break;
		case 'wppa-multitag':
			$opt = wppa_opt( 'bgcolor_multitag' );
			if ( $opt ) $result .= 'background-color:' . $opt . '; ';
			$opt = wppa_opt( 'bcolor_multitag' );
			if ( $opt ) $result .= 'border-color:' . $opt . '; ';
			break;
		case 'wppa-bestof':
			$opt = wppa_opt( 'bgcolor_bestof' );
			if ( $opt ) $result .= 'background-color:' . $opt . '; ';
			$opt = wppa_opt( 'bcolor_bestof' );
			if ( $opt ) $result .= 'border-color:' . $opt . '; ';
			break;
		case 'wppa-stereo':
			$opt = wppa_opt( 'bgcolor_stereo' );
			if ( $opt ) $result .= 'background-color:' . $opt . '; ';
			$opt = wppa_opt( 'bcolor_stereo' );
			if ( $opt ) $result .= 'border-color:' . $opt . '; ';
			break;
		case 'wppa-tagcloud':
			$opt = wppa_opt( 'bgcolor_tagcloud' );
			if ( $opt ) $result .= 'background-color:' . $opt . '; ';
			$opt = wppa_opt( 'bcolor_tagcloud' );
			if ( $opt ) $result .= 'border-color:' . $opt . '; ';
			break;
		case 'wppa-superview':
			$opt = wppa_opt( 'bgcolor_superview' );
			if ( $opt ) $result .= 'background-color:' . $opt . '; ';
			$opt = wppa_opt( 'bcolor_superview' );
			if ( $opt ) $result .= 'border-color:' . $opt . '; ';
			break;
		case 'wppa-search':
			$opt = wppa_opt( 'bgcolor_search' );
			if ( $opt ) $result .= 'background-color:' . $opt . '; ';
			$opt = wppa_opt( 'bcolor_search' );
			if ( $opt ) $result .= 'border-color:' . $opt . '; ';
			break;
		case 'wppa-calendar':
			$opt = wppa_opt( 'bgcolor_calendar' );
			if ( $opt ) $result .= 'background-color:' . $opt . '; ';
			$opt = wppa_opt( 'bcolor_calendar' );
			if ( $opt ) $result .= 'border-color:' . $opt . '; ';
			break;
		case 'wppa-black':
//			$opt = wppa_opt( 'black' );
//			if ( $opt ) $result .= 'color:' . $opt . '; ';
//			break;
			break;
		case 'wppa-arrow':
			$opt = wppa_opt( 'arrow_color' );
			if ( $opt ) $result .= 'color:' . $opt . '; ';
			break;
		case 'wppa-td';
			$result .= 'padding: 3px 2px 3px 0; border: 0';
			break;
		default:
			wppa_dbg_msg( 'Unexpected error in __wcs, unknown class: ' . $class, 'red' );
			wppa_log( 'Err', 'Unexpected error in __wcs, unknown class: ' . $class );
	}
	return $result;
}

function wppa_get_text_medal_color_style( $type, $bw ='1' ) {

	$darks = array(
					'red' 		=> '#BB0000',
					'orange' 	=> '#BB8400',
					'yellow' 	=> '#BBBB00',
					'green' 	=> '#00BB00',
					'blue' 		=> '#0000BB',
					'purple' 	=> '#800080',
					'black'		=> '#333333',
				);
	$lites = array(
					'red' 		=> '#FF7777',
					'orange' 	=> '#FFCC77',
					'yellow' 	=> '#FFFF00',
					'green' 	=> '#77FF77',
					'blue' 		=> '#7777FF',
					'purple' 	=> '#FF00FF',
					'black' 	=> '#999999',
				);

	$dark = $darks[ wppa_opt( $type.'_label_color' ) ];
	$lite = $lites[ wppa_opt( $type.'_label_color' ) ];

	$result =	'background-color:' . $dark . ';' .
				'background:linear-gradient(' . $dark . ', ' . $lite . ');' .
				'border-color:' . $dark . ';' .
				'box-shadow:'.$bw.'px '.$bw.'px '.$bw.'px ' . $dark . ';' .
				'color:#FFFFFF;';

	return $result;
}