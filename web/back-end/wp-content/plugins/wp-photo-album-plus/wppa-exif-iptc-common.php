<?php
/* wppa-exif-iptc-common.php
* Package: wp-photo-album-plus
*
* exif and iptc common functions
* version 6.3.0
*
* 
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );
	
// Translate iptc tags into  photo dependant data inside a text
function wppa_filter_iptc($desc, $photo) {
global $wpdb;
static $iptclabels;

	if ( strpos($desc, '2#') === false ) return $desc;	// No tags in desc: Nothing to do
	
	// Get te labels if not yet present
	if ( ! $iptclabels ) {
		$iptclabels = $wpdb->get_results( "SELECT * FROM `" . WPPA_IPTC . "` WHERE `photo` = '0' ORDER BY `id`", ARRAY_A );
		wppa_dbg_q('Q-IptcLabel');
	}
	
	// Get the photos iptc data
	$iptcdata 	= $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `" . WPPA_IPTC . "` WHERE `photo` = %s ORDER BY `id`", $photo ), ARRAY_A );
	wppa_dbg_q('Q-IptcData');
	
	// Init
	$temp = $desc;
	$prevtag = '';
	$combined = '';
	
	// Process all iptclines of this photo
	if ( $iptcdata ) {
		foreach ( $iptcdata as $iptcline ) {
			$tag = $iptcline['tag'];
			if ( $prevtag == $tag ) {			// add a next item for this tag
				$combined .= ', '.htmlspecialchars( strip_tags( $iptcline['description'] ) );
			}
			else { 							// first item of this tag
				if ( $combined ) { 			// Process if required
					$temp = str_replace( $prevtag, $combined, $temp );
				}
				$combined = htmlspecialchars( strip_tags( $iptcline['description'] ) );
				$prevtag = $tag;
			}
		}
		
		// Process last
		$temp = str_replace( $tag, $combined, $temp );
	}

	// Process all labels
	if ( $iptclabels ) {
		foreach( $iptclabels as $iptclabel ) {
			$tag = $iptclabel['tag'];
			
			// convert 2#XXX to 2#LXXX to indicate the label
			$t = substr( $tag, 0, 2 ) . 'L' . substr( $tag, 2 );
			$tag = $t;
			$temp = str_replace( $tag, __( $iptclabel['description'] , 'wp-photo-album-plus'), $temp );
		}
	}
	
	// Remove untranslated
	$pos = strpos($temp, '2#');
	while ( $pos !== false ) {
		$tmp = substr($temp, 0, $pos).__('n.a.', 'wp-photo-album-plus').substr($temp, $pos+5);
		$temp = $tmp;
		$pos = strpos($temp, '2#');
	}

	return $temp;
}

// Translate exif tags into  photo dependant data inside a text
function wppa_filter_exif( $desc, $photo ) {
global $wpdb;
static $exiflabels;
static $exifdata_array;

	if ( strpos($desc, 'E#') === false ) return $desc;	// No tags in desc: Nothing to do
	
	// Get tha labels if not yet present
	if ( ! $exiflabels ) {
		$exiflabels = $wpdb->get_results( "SELECT * FROM `" . WPPA_EXIF . "` WHERE `photo` = '0' ORDER BY `id`", ARRAY_A );
		wppa_dbg_q('Q-ExifLabel');
	}
	
	// Get the photos exif data
	if ( ! isset( $exifdata_array[$photo] ) ) {
		$exifdata_array[$photo]	= $wpdb->get_results($wpdb->prepare("SELECT * FROM `".WPPA_EXIF."` WHERE `photo`=%s ORDER BY `id`", $photo), ARRAY_A);
		wppa_dbg_q('Q-ExifData');
	}
	$exifdata = $exifdata_array[$photo];
		
	// Init
	$temp = $desc;
	$prevtag = '';
	$combined = '';
	
	// Process all exiflines of this photo
	if ( $exifdata ) { 
		foreach ( $exifdata as $exifline ) {
			$tag = $exifline['tag'];
			if ( $prevtag == $tag ) {			// add a next item for this tag
				$combined .= ', '.htmlspecialchars( strip_tags( $exifline['description'] ) );
			}
			else { 							// first item of this tag
				if ( $combined ) { 			// Process if required
					$temp = str_replace( $prevtag, $combined, $temp );
				}
				$combined = htmlspecialchars( strip_tags( $exifline['description'] ) );
				$prevtag = $tag;
			}
		}
	
		// Process last
		$temp = str_replace( $tag, $combined, $temp );
	}

	// Process all labels
	if ( $exiflabels ) {
		foreach( $exiflabels as $exiflabel ) {
			$tag = $exiflabel['tag'];
			
			// convert E#XXX to E#LXXX to indicate the label
			$t = substr( $tag, 0, 2 ) . 'L' . substr( $tag, 2 );
			$tag = $t;
			
			$temp = str_replace( $tag, __( $exiflabel['description'] , 'wp-photo-album-plus'), $temp );
		}
	}

	// Remove untranslated
	$pos = strpos($temp, 'E#');
	while ( $pos !== false ) {
		$tmp = substr($temp, 0, $pos).__('n.a.', 'wp-photo-album-plus').substr($temp, $pos+6);
		$temp = $tmp;
		$pos = strpos($temp, 'E#');
	}

	// Return result
	return $temp;
}

function wppa_format_exif($tag, $data) {

	$result = $data;
	switch ($tag) {
/*
E#0132		Date Time					Already formatted correctly
E#013B		Photographer				Already formatted correctly
E#8298		Copyright					Already formatted correctly
			Location					Formatted into one line according to the 3 tags below:  2#092, 2#090, 2#095, 2#101
										2#092		Sub location
										2#090		City
										2#095		State
										2#101		Country

E#0110		Camera						Already formatted correctly  Example: Canon EOS 50D
aux:Lens	Lens						Already formatted correctly - See line 66 in sample photo exifdata.jpg attached  Example aux:Lens="EF300mm f/4L IS USM +1.4x"
*/
//	E#920A		Focal length				Must be formatted:  420/1 = 420 mm
		case 'E#920A':
			$temp = explode('/', $data);
			if (isset($temp[1])) {
				if (is_numeric($temp[1])) {
					if ($temp[1] != 0) $result = round($temp[0]/$temp[1]).' mm.';
				}
			}
			break;

//	E#9206		Subject distance			Must be formatted:  765/100 = 7,65 m.
		case 'E#9206':
			$temp = explode('/', $data);
			if (isset($temp[1])) {
				if (is_numeric($temp[1])) {
					if ($temp[1] != 0) $result = round(100*$temp[0]/$temp[1])/"100".' m.';
				}
			}
			break;

//	E#829A		Shutter Speed				Must be formatted:  1/125 = 1/125 s.
		case 'E#829A':
			if ($result) $result .= ' s.';
			break;
			
//	E#829D		F-Stop						Must be formatted:  56/10 = f/5,6
		case 'E#829D':
			$temp = explode('/', $data);
			if (isset($temp[1])) {
				if (is_numeric($temp[1])) {
					if ($temp[1] != 0) $result = 'f/'.(round(10*$temp[0]/$temp[1])/10);
				}
			}				
			break;
/*
E#8827		ISO	Speed Rating			Already formatted correctly
E#9204		Exposure bias				Already formatted correctly

E#8822		Exposure program			Must be formatted according to table
										0 = Not Defined
										1 = Manual
										2 = Program AE
										3 = Aperture-priority AE
										4 = Shutter speed priority AE
										5 = Creative (Slow speed)
										6 = Action (High speed)
										7 = Portrait
										8 = Landscape
										9 = Bulb
*/
		case 'E#8822':
			switch ($data) {
				case '0': $result = __('Not Defined', 'wp-photo-album-plus'); break;
				case '1': $result = __('Manual', 'wp-photo-album-plus'); break;
				case '2': $result = __('Program AE', 'wp-photo-album-plus'); break;
				case '3': $result = __('Aperture-priority AE', 'wp-photo-album-plus'); break;
				case '4': $result = __('Shutter speed priority AE', 'wp-photo-album-plus'); break;
				case '5': $result = __('Creative (Slow speed)', 'wp-photo-album-plus'); break;
				case '6': $result = __('Action (High speed)', 'wp-photo-album-plus'); break;
				case '7': $result = __('Portrait', 'wp-photo-album-plus'); break;
				case '8': $result = __('Landscape', 'wp-photo-album-plus'); break;
				case '9': $result = __('Bulb', 'wp-photo-album-plus'); break;
			}
			break;
/* 
E#9204 		Exposure bias value 
*/
		case 'E#9204':
			if ( $data) $result = $data.' EV';
			else $result = '';
			break;
/*
E#9207		Metering mode				Must be formatted according to table
										1 = Average
										2 = Center-weighted average
										3 = Spot
										4 = Multi-spot
										5 = Multi-segment
										6 = Partial
										255 = Other
*/
		case 'E#9207':
			switch ($data) {
				case '1': $result = __('Average', 'wp-photo-album-plus'); break;
				case '2': $result = __('Center-weighted average', 'wp-photo-album-plus'); break;
				case '3': $result = __('Spot', 'wp-photo-album-plus'); break;
				case '4': $result = __('Multi-spot', 'wp-photo-album-plus'); break;
				case '5': $result = __('Multi-segment', 'wp-photo-album-plus'); break;
				case '6': $result = __('Partial', 'wp-photo-album-plus'); break;
				case '255': $result = __('Other', 'wp-photo-album-plus'); break;
			}
			break;
/*
E#9209		Flash						Must be formatted according to table
										0x0	= No Flash
										0x1	= Fired
										0x5	= Fired, Return not detected
										0x7	= Fired, Return detected
										0x8	= On, Did not fire
										0x9	= On, Fired
										0xd	= On, Return not detected
										0xf	= On, Return detected
										0x10	= Off, Did not fire
										0x14	= Off, Did not fire, Return not detected
										0x18	= Auto, Did not fire
										0x19	= Auto, Fired
										0x1d	= Auto, Fired, Return not detected
										0x1f	= Auto, Fired, Return detected
										0x20	= No flash function
										0x30	= Off, No flash function
										0x41	= Fired, Red-eye reduction
										0x45	= Fired, Red-eye reduction, Return not detected
										0x47	= Fired, Red-eye reduction, Return detected
										0x49	= On, Red-eye reduction
										0x4d	= On, Red-eye reduction, Return not detected
										0x4f	= On, Red-eye reduction, Return detected
										0x50	= Off, Red-eye reduction
										0x58	= Auto, Did not fire, Red-eye reduction
										0x59	= Auto, Fired, Red-eye reduction
										0x5d	= Auto, Fired, Red-eye reduction, Return not detected
										0x5f	= Auto, Fired, Red-eye reduction, Return detected		
*/
		case 'E#9209':
			switch ($data) {
				case '0x0':
				case '0': $result = __('No Flash', 'wp-photo-album-plus'); break;
				case '0x1':
				case '1': $result = __('Fired', 'wp-photo-album-plus'); break;
				case '0x5':
				case '5': $result = __('Fired, Return not detected', 'wp-photo-album-plus'); break;
				case '0x7':
				case '7': $result = __('Fired, Return detected', 'wp-photo-album-plus'); break;
				case '0x8':
				case '8': $result = __('On, Did not fire', 'wp-photo-album-plus'); break;
				case '0x9':
				case '9': $result = __('On, Fired', 'wp-photo-album-plus'); break;
				case '0xd':
				case '13': $result = __('On, Return not detected', 'wp-photo-album-plus'); break;
				case '0xf':
				case '15': $result = __('On, Return detected', 'wp-photo-album-plus'); break;
				case '0x10':
				case '16': $result = __('Off, Did not fire', 'wp-photo-album-plus'); break;
				case '0x14':
				case '20': $result = __('Off, Did not fire, Return not detected', 'wp-photo-album-plus'); break;
				case '0x18':
				case '24': $result = __('Auto, Did not fire', 'wp-photo-album-plus'); break;
				case '0x19':
				case '25': $result = __('Auto, Fired', 'wp-photo-album-plus'); break;
				case '0x1d':
				case '29': $result = __('Auto, Fired, Return not detected', 'wp-photo-album-plus'); break;
				case '0x1f':
				case '31': $result = __('Auto, Fired, Return detected', 'wp-photo-album-plus'); break;
				case '0x20':
				case '32': $result = __('No flash function', 'wp-photo-album-plus'); break;
				case '0x30':
				case '48': $result = __('Off, No flash function', 'wp-photo-album-plus'); break;
				case '0x41':
				case '65': $result = __('Fired, Red-eye reduction', 'wp-photo-album-plus'); break;
				case '0x45':
				case '69': $result = __('Fired, Red-eye reduction, Return not detected', 'wp-photo-album-plus'); break;
				case '0x47':
				case '71': $result = __('Fired, Red-eye reduction, Return detected', 'wp-photo-album-plus'); break;
				case '0x49':
				case '73': $result = __('On, Red-eye reduction', 'wp-photo-album-plus'); break;
				case '0x4d':
				case '77': $result = __('Red-eye reduction, Return not detected', 'wp-photo-album-plus'); break;
				case '0x4f':
				case '79': $result = __('On, Red-eye reduction, Return detected', 'wp-photo-album-plus'); break;
				case '0x50':
				case '80': $result = __('Off, Red-eye reduction', 'wp-photo-album-plus'); break;
				case '0x58':
				case '88': $result = __('Auto, Did not fire, Red-eye reduction', 'wp-photo-album-plus'); break;
				case '0x59':
				case '89': $result = __('Auto, Fired, Red-eye reduction', 'wp-photo-album-plus'); break;
				case '0x5d':
				case '93': $result = __('Auto, Fired, Red-eye reduction, Return not detected', 'wp-photo-album-plus'); break;
				case '0x5f':
				case '95': $result = __('Auto, Fired, Red-eye reduction, Return detected', 'wp-photo-album-plus'); break;
			}
			break;
			
		default:
			$result = $data;
	}
	
	return $result;
}

function wppa_iptc_clean_garbage( $photo ) {
global $wpdb;

	$items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `" . WPPA_IPTC ."` WHERE `photo` = %s", $photo ), ARRAY_A );
	if ( is_array( $items ) ) {
		foreach( $items as $item ) {
			$txt = sanitize_text_field( $item['description'] );
			$txt = str_replace( array(chr(0),chr(1),chr(2),chr(3),chr(4),chr(5),chr(6),chr(7)), '', $txt );
			
			// Cleaned text empty?
			if ( ! $txt ) { 
			
				// Garbage text, remove from photo
				$wpdb->query( $wpdb->prepare( "DELETE FROM `" . WPPA_IPTC . "` WHERE `id` = %s", $item['id'] ) );
				
				// Current label still used?
				$in_use = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `" . WPPA_IPTC . "` WHERE `photo` <> '0' AND `tag` = %s", $item['tag'] ) );
				if ( ! $in_use ) {
					$wpdb->query( $wpdb->prepare( "DELETE FROM `" . WPPA_IPTC . "` WHERE `photo` = '0' AND `tag` = %s", $item['tag'] ) );
					wppa_log( 'dbg', 'Iptc tag label' . $item['tag'] . ' removed.' );
				}
			}
		}
	}
}

function wppa_exif_clean_garbage( $photo ) {
global $wpdb;

	$items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `" . WPPA_EXIF ."` WHERE `photo` = %s", $photo ), ARRAY_A );
	if ( is_array( $items ) ) {
		foreach( $items as $item ) {
			$txt = sanitize_text_field( $item['description'] );
			$txt = str_replace( array(chr(0),chr(1),chr(2),chr(3),chr(4),chr(5),chr(6),chr(7)), '', $txt );
			
			// Cleaned text empty?
			if ( ! $txt ) { 
			
				// Garbage
				$wpdb->query( $wpdb->prepare( "DELETE FROM `" . WPPA_EXIF . "` WHERE `id` = %s", $item['id'] ) );
				
				// Current label still used?
				$in_use = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `" . WPPA_EXIF . "` WHERE `photo` <> '0' AND `tag` = %s", $item['tag'] ) );
				if ( ! $in_use ) {
					$wpdb->query( $wpdb->prepare( "DELETE FROM `" . WPPA_EXIF . "` WHERE `photo` = '0' AND `tag` = %s", $item['tag'] ) );
					wppa_log( 'dbg', 'Exif tag label ' . $item['tag'] . ' removed.' );
				}
			}
		}
	}
}