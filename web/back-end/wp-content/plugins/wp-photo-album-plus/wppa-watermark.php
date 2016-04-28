<?php
/* wppa-watermark.php
*
* Functions used for the application of watermarks
* Version 6.3.11
*
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

function wppa_create_textual_watermark_file( $args ) {

	// See what we have
	$args = wp_parse_args( ( array ) $args, array( 	'content' 		=> '---preview---',
													'pos' 			=> 'cencen',
													'id'			=> '',
													'font' 			=> wppa_opt( 'textual_watermark_font' ),
													'text' 			=> '',
													'style' 		=> wppa_opt( 'textual_watermark_type' ),
													'filebasename' 	=> 'dummy',
													'url' 			=> false,
													'width'			=> '',
													'height' 		=> '',
													'transp' 		=> '0',
													 ) );

	// We may have been called from wppa_get_water_file_and_pos() just to find the settings
	// In this case there is no id given.
	$id = $args['id'];
	if ( ! $id && $args['content'] != '---preview---' ) {
		return false;
	}
	if ( $id && wppa_is_video( $id ) ) {
//		return false;
	}

	// Set special values in case of preview
	if ( $args['content'] == '---preview---' ) {
		$preview = true;
		$fontsize 		= $args['font'] == 'system' ? 5 : 12;
		$padding   		= 6;
		$linespacing 	= ceil( $fontsize * 2 / 3 );
	}
	else {
		$preview = false;
		$fontsize 		= wppa_opt( 'textual_watermark_size' );
		if ( $args['font'] == 'system' ) $fontsize = min( $fontsize, 5 );
		$padding   		= 12;
		$linespacing 	= ceil( $fontsize * 2 / 3 );
	}

	// Set font specific vars
	$fontfile 			= $args['font'] == 'system' ? '' : WPPA_UPLOAD_PATH.'/fonts/'.$args['font'].'.ttf';

	// Output file
	if ( $preview ) $filename 	= WPPA_UPLOAD_PATH.'/fonts/wmf'.$args['filebasename'].'.png';
	else $filename 	= WPPA_UPLOAD_PATH.'/temp/wmf'.$args['filebasename'].'.png';

	// Preprocess the text
	if ( ! $args['text'] ) {
		switch ( $args['content'] ) {
			case '---preview---':
				$text = strtoupper( substr( $args['font'],0,1 ) ).strtolower( substr( $args['font'],1 ) );
				break;
			case '---filename---':
				$text = wppa_get_photo_item( $id, 'filename' );
				break;
			case '---name---':
				$text = wppa_get_photo_name( $id );
				break;
			case '---description---':
				$text = strip_tags( wppa_strip_tags( wppa_get_photo_desc( $id ), 'style&script' ) );
				break;
			case '---predef---':
				$text = wppa_opt( 'textual_watermark_text' );
				if ( $args['font'] != 'system' ) {
					$text = str_replace( '( c )', '&copy;', $text );
					$text = str_replace( '( R )', '&reg;', $text );
				}
				$text = html_entity_decode( $text );
				$text = str_replace( 'w#site', get_bloginfo( 'url' ), $text );
				$text = str_replace( 'w#owner', wppa_get_photo_item( $id, 'owner' ), $text );
				$text = str_replace( 'w#id', $id, $text );
				$text = str_replace( 'w#name', wppa_get_photo_name( $id ), $text );
				$text = str_replace( 'w#filename', wppa_get_photo_item( $id, 'filename' ), $text );
				$text = str_replace( 'w#timestamp', wppa_local_date( get_option( 'date_format', "F j, Y," ).' '.get_option( 'time_format', "g:i a" ), wppa_get_photo_item( $id, 'timestamp' ) ), $text );

				$text = trim( $text );
				break;

			default:
				wppa_log( 'Error', 'Unimplemented arg '.$arg.' in wppa_create_textual_watermark_file()' );
				return false;
		}
	}
	else {
		$text = $args['text'];
	}

	// Any text anyway?
	if ( ! strlen( $text ) ) {
		wppa_log( 'Error', 'No text for textual watermark. photo='.$id );
		return false;		// No text -> no watermark
	}

	// Split text on linebreaks
	$text 	= str_replace( "\n", '\n', $text );
	$lines 	= explode( '\n', $text );

	// Trim and remove empty lines
	$temp = $lines;
	$lines = array();
	foreach ( $temp as $line ) {
		$line = trim( $line );
		if ( $line ) $lines[] = $line;
	}

	// Find image width
	if ( $args['width'] ) $image_width = $args['width']; else $image_width = '';
	if ( $args['height'] ) $image_height = $args['height']; else $image_height = '';

	if ( $preview ) {
		if ( ! $image_width ) $image_width = 2000;
		if ( ! $image_height ) $image_height = 1000;
	}
	else {
//		$temp = getimagesize( wppa_get_photo_path( $id ) );
		$temp = wppa_get_imagexy( $id );
		if ( ! is_array( $temp ) ) {
			wppa_log( 'Error', 'Trying to apply a watermark on a non image file. Id = '.$id );
			return false;	// not an image
		}
		if ( ! $image_width ) $image_width = $temp[0];
		if ( ! $image_height ) $image_height = $temp[1];
	}

	$width_fits = false;

	while ( ! $width_fits ) {
		// Find pixel linelengths
		foreach ( array_keys( $lines ) as $key ) {
			$lines[$key] = trim( $lines[$key] );
			if ( $args['font'] == 'system' ) {
				$lengths[$key] = strlen( $lines[$key] ) * imagefontwidth( $fontsize );
			}
			else {
				$temp = imagettfbbox ( $fontsize , 0.0 , $fontfile , $lines[$key] );
				$lengths[$key] = $temp[2] - $temp[0];
			}
		}
		$maxlen = wppa_array_max( $lengths );

		// Find canvas size
		$nlines 	= count( $lines );
		if ( $args['font'] == 'system' ) {
			$lineheight 	= imagefontheight( $fontsize );
		}
		else {
			$temp = imagettfbbox ( $fontsize , 0.0 , $fontfile , $lines[0] );
			$lineheight = $temp[3] - $temp[7];
		}
		$canvas_width 	= wppa_array_max( $lengths ) + 4 * $padding;
		$canvas_height 	= ( $lineheight + $linespacing ) * count( $lines ) + 2 * $padding;

		// Does it fit?
		if ( $canvas_width > $image_width ) {
			// Break the longest line into two sublines. There should be a space in the right half, if not: fail
			$i = 0;
			$l = 0;
			foreach ( array_keys( $lines ) as $key ) {
				if ( strlen( $lines[$key] ) > $l ) {
					$i = $key;
					$l = strlen( $lines[$key] );
				}
			}
			$temp = $lines;
			$lines = array();
			$j = 0;
			while ( $j < $i ) {
				$lines[$j] = $temp[$j];
				$j++;
			}
			//
			$j = $i;
			$llen = strlen( $temp[$i] );
			$spos = floor( $llen / 2 );
			while ( $spos < $llen && substr( $temp[$i], $spos, 1 ) != ' ' ) $spos++;
			if ( $spos == $llen ) {	// Unable to find a space, give up
				wppa_log( 'Error', 'Trying to apply a watermark that is too wide for the image. Id = '.$id );
				return false;	// too wide
			}
			$lines[$j] = substr( $temp[$i], 0, $spos );
			$lines[$j+1] = trim( str_replace( $lines[$j], '', $temp[$i] ) );
			$i++;
			//
			$j = $i + 1;
			while ( $j <= count( $temp ) ) {
				$lines[$j] = $temp[$i];
				$j++;
			}
		}
		else {
			$width_fits = true;
		}
		if ( $canvas_height > $image_height ) {
			wppa_log( 'Error', 'Trying to apply a watermark that is too high for the image. Id = '.$id );
			return false;	// not an image
		}
	}

	// Create canvas
	$canvas 	= imagecreatetruecolor( $canvas_width, $canvas_height );
	$bgcolor 	= imagecolorallocatealpha( $canvas,   0,   0,   0, 127 );	// Transparent
	$white 		= imagecolorallocatealpha( $canvas, 255, 255, 255, $args['transp'] );
	$black 		= imagecolorallocatealpha( $canvas,   0,   0,   0, $args['transp'] );

	imagefill( $canvas, 0, 0, $bgcolor );
//	imagerectangle( $canvas, 0, 0, $canvas_width-1, $canvas_height-1, $white );	// debug

	// Define the text colors
	switch ( $args['style'] ) {
		case 'tvstyle':
		case 'whiteonblack':
			$fg = $white;
			$bg = $black;
			break;
		case 'utopia':
		case 'blackonwhite':
			$fg = $black;
			$bg = $white;
			break;
		case 'white':
			$fg = $white;
			$bg = $bgcolor;
			break;
		case 'black':
			$fg = $black;
			$bg = $bgcolor;
			break;
	}

	// Plot the text
	foreach ( array_keys( $lines ) as $lineno ) {
		if ( strpos( $args['pos'], 'lft' ) !== false ) $indent = 0;
		elseif ( strpos( $args['pos'], 'rht' ) !== false ) $indent = $maxlen - $lengths[$lineno];
		else $indent = floor( ( $maxlen - $lengths[$lineno] ) / 2 );
		switch ( $args['style'] ) {
			case 'tvstyle':
			case 'utopia':
				for ( $i=-1; $i<=1; $i++ ) {
					for ( $j=-1; $j<=1; $j++ ) {
						if ( $args['font'] == 'system' ) {
							imagestring( $canvas, $fontsize, 2 * $padding + $i + $indent, $padding + $lineno * ( $lineheight + $linespacing ) + $j,  $lines[$lineno], $bg );
						}
						else {
							imagettftext ( $canvas, $fontsize, 0, 2 * $padding + $i + $indent, $padding + ( $lineno + 1 ) * $lineheight + $lineno * $linespacing + $j, $bg, $fontfile, $lines[$lineno] );
						}
					}
				}
				if ( $args['font'] == 'system' ) {
					imagestring( $canvas, $fontsize, 2 * $padding + $indent, $padding + $lineno * ( $lineheight + $linespacing ),  $lines[$lineno], $fg );
				}
				else {
					imagettftext ( $canvas, $fontsize, 0, 2 * $padding + $indent, $padding + ( $lineno + 1 ) * $lineheight + $lineno * $linespacing, $fg, $fontfile, $lines[$lineno] );
				}
				break;
			case 'blackonwhite':
			case 'whiteonblack':
			case 'white':
			case 'black':
				$lft = $padding + $indent;
				$rht = 3 * $padding + $indent + $lengths[$lineno];
				$top = $lineno * ( $lineheight + $linespacing ) + $padding;
				$bot = ( $lineno + 1 ) * ( $lineheight + $linespacing ) + $padding;

				imagefilledrectangle( $canvas, $lft, $top+1, $rht, $bot, $bg );
//				imagerectangle( $canvas, $lft, $top, $rht, $bot, $fg );	// debug

				$top = $padding + $lineno * ( $lineheight + $linespacing ) + floor( $linespacing/2 );
				$lft = 2 * $padding + $indent;
				$bot = $padding + ( $lineno + 1 ) * ( $lineheight + $linespacing ) - ceil( $linespacing/2 );
				if ( $args['font'] == 'system' ) {
					imagestring( $canvas, $fontsize, $lft, $top,  $lines[$lineno], $fg );
				}
				else {
					imagettftext ( $canvas, $fontsize, 0, $lft, $bot-1, $fg, $fontfile, $lines[$lineno] );
				}
				break;
		}
	}
	imagesavealpha( $canvas, true );
	imagepng( $canvas, $filename );
	imagedestroy( $canvas );
	if ( $preview || $args['url'] ) {
		$url = str_replace( WPPA_UPLOAD_PATH, WPPA_UPLOAD_URL, $filename );
		return $url;
	}
	else {
		return $filename;
	}
}

function wppa_array_max( $array ) {
	if ( ! is_array( $array ) ) return $array;
	$result = 0;
	foreach ( $array as $item ) if ( $item > $result ) $result = $item;
	return $result;
}

function wppa_get_water_file_and_pos( $id ) {

	$result['file'] = wppa_opt( 'watermark_file' );	// default
	$result['pos'] = wppa_opt( 'watermark_pos' );	// default

	$user = wppa_get_user();

	if ( wppa_switch( 'watermark_user' ) || current_user_can( 'wppa_settings' ) ) {									// user overrule?
		if ( isset( $_POST['wppa-watermark-file'] ) ) {
			$result['file'] = $_POST['wppa-watermark-file'];
			update_option( 'wppa_watermark_file_' . $user, $_POST['wppa-watermark-file'] );
		}
		elseif ( get_option( 'wppa_watermark_file_' . $user, 'nil' ) != 'nil' ) {
			$result['file'] = get_option( 'wppa_watermark_file_' . $user );
		}
		if ( isset( $_POST['wppa-watermark-pos'] ) ) {
			$result['pos'] = $_POST['wppa-watermark-pos'];
			update_option( 'wppa_watermark_pos_' . $user, $_POST['wppa-watermark-pos'] );
		}
		elseif ( get_option( 'wppa_watermark_pos_' . $user, 'nil' ) != 'nil' ) {
			$result['pos'] = get_option( 'wppa_watermark_pos_' . $user );
		}
	}
	$result['select'] = $result['file'];

	if ( substr( $result['file'], 0, 3 ) == '---' && $result['file'] != '--- none ---' ) {			// Special identifier, not a file
		$result['file'] = wppa_create_textual_watermark_file( array( 'content' => $result['file'], 'pos' => $result['pos'], 'id' => $id ) );
	}
	else {
		$result['file'] = WPPA_UPLOAD_PATH . '/watermarks/' . $result['file'];
	}
	return $result;
}


function wppa_add_watermark( $id ) {

	// Init
	if ( ! wppa_switch( 'watermark_on' ) ) return false;	// Watermarks off
//	if ( wppa_is_video( $id ) ) return false;					// Can not on a video

	// Find the watermark file and location
	$temp = wppa_get_water_file_and_pos( $id );
	$waterfile = $temp['file'];
	if ( ! $waterfile ) return false;					// an error has occurred

	$waterpos = $temp['pos'];										// default

	if ( basename( $waterfile ) == '--- none ---' ) {
		return false;	// No watermark this time
	}
	// Open the watermark file
	$watersize = @ getimagesize( $waterfile );
	if ( !is_array( $watersize ) ) return false;	// Not a valid picture file
	$waterimage = imagecreatefrompng( $waterfile );
	if ( empty( $waterimage ) or ( !$waterimage ) ) {
		wppa_dbg_msg( 'Watermark file '.$waterfile.' not found or corrupt' );
		return false;			// No image
	}
	imagealphablending( $waterimage, false );
	imagesavealpha( $waterimage, true );


	// Open the photo file
	$file = wppa_get_photo_path( $id );
	if ( wppa_is_video( $id ) ) {
		$file = wppa_fix_poster_ext( $file, $id );
	}
	if ( ! is_file( $file ) ) return false;	// File gone
	$photosize = getimagesize( $file );
	if ( ! is_array( $photosize ) ) {
		return false;	// Not a valid photo
	}
	switch ( $photosize[2] ) {
		case 1: $tempimage = imagecreatefromgif( $file );
			$photoimage = imagecreatetruecolor( $photosize[0], $photosize[1] );
			imagecopy( $photoimage, $tempimage, 0, 0, 0, 0, $photosize[0], $photosize[1] );
			break;
		case 2: $photoimage = imagecreatefromjpeg( $file );
			break;
		case 3: $photoimage = imagecreatefrompng( $file );
			break;
	}
	if ( empty( $photoimage ) or ( ! $photoimage ) ) return false; 			// No image

	$ps_x = $photosize[0];
	$ps_y = $photosize[1];
	$ws_x = $watersize[0];
	$ws_y = $watersize[1];
	$src_x = 0;
	$src_y = 0;
	if ( $ws_x > $ps_x ) {
		$src_x = ( $ws_x - $ps_x ) / 2;
		$ws_x = $ps_x;
	}
	if ( $ws_y > $ps_y ) {
		$src_y = ( $ws_y - $ps_y ) / 2;
		$ws_y = $ps_y;
	}

	$loy = substr( $waterpos, 0, 3 );
	switch( $loy ) {
		case 'top': $dest_y = 0;
			break;
		case 'cen': $dest_y = ( $ps_y - $ws_y ) / 2;
			break;
		case 'bot': $dest_y = $ps_y - $ws_y;
			break;
		default: $dest_y = 0; 	// should never get here
	}
	$lox = substr( $waterpos, 3 );
	switch( $lox ) {
		case 'lft': $dest_x = 0;
			break;
		case 'cen': $dest_x = ( $ps_x - $ws_x ) / 2;
			break;
		case 'rht': $dest_x = $ps_x - $ws_x;
			break;
		default: $dest_x = 0; 	// should never get here
	}

	$opacity = strpos( $waterfile, '/temp/' ) === false ? intval( wppa_opt( 'watermark_opacity' ) ) : intval( wppa_opt( 'watermark_opacity_text' ) );
	wppa_imagecopymerge_alpha( $photoimage , $waterimage , $dest_x, $dest_y, $src_x, $src_y, $ws_x, $ws_y, $opacity );

	// Save the result
	switch ( $photosize[2] ) {
		case 1: imagegif( $photoimage, $file );
			break;
		case 2: imagejpeg( $photoimage, $file, wppa_opt( 'jpeg_quality' ) );
			break;
		case 3: imagepng( $photoimage, $file, 7 );
			break;
	}

	// Cleanup
	imagedestroy( $photoimage );
	imagedestroy( $waterimage );

	return true;
}


/**
 * PNG ALPHA CHANNEL SUPPORT for imagecopymerge();
 * This is a function like imagecopymerge but it handle alpha channel well!!!
 **/

// A fix to get a function like imagecopymerge WITH ALPHA SUPPORT
// Main script by aiden dot mail at freemail dot hu
// Transformed to imagecopymerge_alpha() by rodrigo dot polo at gmail dot com
function wppa_imagecopymerge_alpha( $dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct ){
    if( !isset( $pct ) ){
        return false;
    }
    $pct /= 100;
    // Get image width and height
    $w = imagesx( $src_im );
    $h = imagesy( $src_im );
    // Turn alpha blending off
    imagealphablending( $src_im, false );
    // Find the most opaque pixel in the image ( the one with the smallest alpha value )
    $minalpha = 127;
    for( $x = 0; $x < $w; $x++ )
    for( $y = 0; $y < $h; $y++ ){
        $alpha = ( imagecolorat( $src_im, $x, $y ) >> 24 ) & 0xFF;
        if( $alpha < $minalpha ){
            $minalpha = $alpha;
        }
    }
    //loop through image pixels and modify alpha for each
    for( $x = 0; $x < $w; $x++ ){
        for( $y = 0; $y < $h; $y++ ){
            //get current alpha value ( represents the TANSPARENCY! )
            $colorxy = imagecolorat( $src_im, $x, $y );
            $alpha = ( $colorxy >> 24 ) & 0xFF;
            //calculate new alpha
            if( $minalpha !== 127 ){
                $alpha = 127 + 127 * $pct * ( $alpha - 127 ) / ( 127 - $minalpha );
            } else {
                $alpha += 127 * $pct;
            }
            //get the color index with new alpha
            $alphacolorxy = imagecolorallocatealpha( $src_im, ( $colorxy >> 16 ) & 0xFF, ( $colorxy >> 8 ) & 0xFF, $colorxy & 0xFF, $alpha );
            //set pixel with the new color + opacity
            if( !imagesetpixel( $src_im, $x, $y, $alphacolorxy ) ){
                return false;
            }
        }
    }
    // The image copy
    imagecopy( $dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h );
}

function wppa_watermark_file_select( $default = false ) {

	// Init
	$result = '';
	$user = wppa_get_user();

	// See what's in there
	$paths = WPPA_UPLOAD_PATH . '/watermarks/*.png';
	$files = glob( $paths );

	// Find current selection
	$select = wppa_opt( 'watermark_file' );	// default
	if ( ! $default && ( wppa_switch( 'watermark_user' ) || current_user_can( 'wppa_settings' ) ) && get_option( 'wppa_watermark_file_' . $user, 'nil' ) !== 'nil' ) {
		$select = get_option( 'wppa_watermark_file_' . $user );
	}

	// Produce the html
	$result .= '<option value="--- none ---">'.__( '--- none ---' , 'wp-photo-album-plus' ).'</option>';
	if ( $files ) foreach ( $files as $file ) {
		$sel = $select == basename( $file ) ? 'selected="selected"' : '';
		$result .= '<option value="'.basename( $file ).'" '.$sel.'>'.basename( $file ).'</option>';
	}

	// Text based watermarks
	$sel = $select == '---name---' ? 'selected="selected"' : '';
	$result .= '<option value="---name---" '.$sel.'>'.__( '--- text: name ---' , 'wp-photo-album-plus' ).'</option>';
	$sel = $select == '---filename---' ? 'selected="selected"' : '';
	$result .= '<option value="---filename---" '.$sel.'>'.__( '--- text: filename ---' , 'wp-photo-album-plus' ).'</option>';
	$sel = $select == '---description---' ? 'selected="selected"' : '';
	$result .= '<option value="---description---" '.$sel.'>'.__( '--- text: description ---' , 'wp-photo-album-plus' ).'</option>';
	$sel = $select == '---predef---' ? 'selected="selected"' : '';
	$result .= '<option value="---predef---" '.$sel.'>'.__( '--- text: pre-defined ---' , 'wp-photo-album-plus' ).'</option>';

	return $result;
}

function wppa_watermark_pos_select( $default = false ) {

	// Init
	$user = wppa_get_user();
	$result = '';
	$opt = array( 	__( 'top - left' , 'wp-photo-album-plus' ), __( 'top - center' , 'wp-photo-album-plus' ), __( 'top - right' , 'wp-photo-album-plus' ),
					__( 'center - left' , 'wp-photo-album-plus' ), __( 'center - center' , 'wp-photo-album-plus' ), __( 'center - right' , 'wp-photo-album-plus' ),
					__( 'bottom - left' , 'wp-photo-album-plus' ), __( 'bottom - center' , 'wp-photo-album-plus' ), __( 'bottom - right' , 'wp-photo-album-plus' ), );
	$val = array( 	'toplft', 'topcen', 'toprht',
					'cenlft', 'cencen', 'cenrht',
					'botlft', 'botcen', 'botrht', );
	$idx = 0;

	// Find current selection
	$select = wppa_opt( 'watermark_pos' );	// default
	if ( ! $default && ( wppa_switch( 'watermark_user' ) || current_user_can( 'wppa_settings' ) ) && get_option( 'wppa_watermark_pos_' . $user, 'nil' ) !== 'nil' ) {
		$select = get_option( 'wppa_watermark_pos_' . $user );
	}

	// Produce the html
	while ( $idx < 9 ) {
		$sel = $select == $val[$idx] ? 'selected="selected"' : '';
		$result .= '<option value="'.$val[$idx].'" '.$sel.'>'.$opt[$idx].'</option>';
		$idx++;
	}

	return $result;
}
