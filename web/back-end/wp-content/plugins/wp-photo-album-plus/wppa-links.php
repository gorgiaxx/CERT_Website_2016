<?php
/* wppa-links.php
* Package: wp-photo-album-plus
*
* Frontend links
* Version 6.4.08
*
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

// get permalink plus ? or & and possible debug switch
function wppa_get_permalink( $key = '', $plain = false ) {
global $wppa_lang;

	if ( ! $key && is_search() ) $key = wppa_opt( 'search_linkpage' );

	switch ( $key ) {
		case '0':
		case '':	// normal permalink
			if ( wppa_in_widget() ) {
//				$pl = esc_url( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ); //
				$pl = get_permalink(); // home_url();
				if ( $plain ) return $pl;
				if ( strpos( $pl, '?' ) ) $pl .= '&amp;';
				else $pl .= '?';
				}
			else {
				if ( wppa( 'ajax' ) ) {
					if ( wppa_get_get( 'page_id' ) ) $id = wppa_get_get( 'page_id' );
					elseif ( wppa_get_get( 'p' ) ) $id = wppa_get_get( 'p' );
					elseif ( wppa_get_get( 'fromp' ) ) $id = wppa_get_get( 'fromp' );
					else $id = '';
					$pl = get_permalink( intval( $id ) );
					if ( $plain ) return $pl;
					if ( strpos( $pl, '?' ) ) $pl .= '&amp;';
					else $pl .= '?';
				}
				else {
					$pl = get_permalink();
					if ( $plain ) return $pl;
					if ( strpos( $pl, '?' ) ) $pl .= '&amp;';
					else $pl .= '?';
				}
			}
			break;
		case 'js':	// normal permalink for js use
			if ( wppa_in_widget() ) {
				$pl = home_url();
				if ( $plain ) return $pl;
				if ( strpos($pl, '?' ) ) $pl .= '&';
				else $pl .= '?';
			}
			else {
				if ( wppa( 'ajax' ) ) {
					if ( wppa_get_get( 'page_id' ) ) $id = wppa_get_get( 'page_id' );
					elseif ( wppa_get_get( 'p' ) ) $id = wppa_get_get( 'p' );
					elseif ( wppa_get_get( 'wppa-fromp' ) ) $id = wppa_get_get( 'wppa-fromp' );
					else $id = '';
					$pl = get_permalink( intval( $id ) );
					if ( $plain ) return $pl;
					if ( strpos( $pl, '?' ) ) $pl .= '&';
					else $pl .= '?';
				}
				else {
					$pl = get_permalink();
					if ( $plain ) return $pl;
					if ( strpos( $pl, '?' ) ) $pl .= '&';
					else $pl .= '?';
				}
			}
			break;
		default:	// pagelink
			$pl = get_permalink($key);
			if ( $plain ) return $pl;
			if (strpos($pl, '?')) $pl .= '&amp;';
			else $pl .= '?';
			break;
	}

	if ( wppa_get_get( 'lang' ) ) {	// If lang in querystring: keep it
		if ( strpos( $pl, 'lang=' ) === false ) { 	// Not yet
			if ( $key == 'js' ) $pl .= 'lang=' . $wppa_lang . '&';
			else $pl .= 'lang=' . $wppa_lang . '&amp;';
		}
	}

	if ( wppa( 'is_rootsearch' ) ) {
		if ( $key == 'js' ) $pl .= 'rootsearch=1&';
		else $pl .= 'rootsearch=1&amp;';
	}

	if ( wppa( 'debug' ) ) {
		if ( $key == 'js' ) $pl .= 'debug='.wppa( 'debug' ).'&';
		else $pl .= 'debug='.wppa( 'debug' ).'&amp;';
	}

	return $pl;
}

// Like get_permalink but for ajax use
function wppa_get_ajaxlink( $key = '', $deltamoccur = '0' ) {
global $wppa_lang;

	if ( wppa_switch( 'ajax_non_admin' ) ) {
		$al = WPPA_URL.'/wppa-ajax-front.php?action=wppa&amp;wppa-action=render';
	}
	else {
		$al = admin_url( 'admin-ajax.php' ).'?action=wppa&amp;wppa-action=render';
	}

	// See if this call is from an ajax operation or...
	if ( wppa( 'ajax' ) ) {
		if ( wppa_get_get( 'size' ) ) $al .= '&amp;wppa-size=' . wppa_get_get( 'size' );
		if ( wppa_get_get( 'moccur' ) ) $al .= '&amp;wppa-moccur=' . wppa_get_get( 'moccur' );
		if ( is_numeric( $key ) && $key > '0' ) {
			$al .= '&amp;page_id='.$key;
		}
		else {
			if ( wppa_get_get( 'page_id' ) ) $al .= '&amp;page_id=' . wppa_get_get( 'page_id' );
		}
		if ( wppa_get_get( 'p' ) ) $al .= '&amp;p=' . wppa_get_get( 'p' );
		if ( wppa_get_get( 'fromp' ) ) $al .= '&amp;wppa-fromp=' . wppa_get_get( 'wppa-fromp' );
	}
	else {	// directly from a page or post
		$al .= '&amp;wppa-size='.wppa_get_container_width();
		$al .= '&amp;wppa-moccur=' . ( wppa( 'mocc' ) + $deltamoccur );
		if ( is_numeric($key) && $key > '0' ) {
			$al .= '&amp;page_id=' . $key;
		}
		else {
			if ( wppa_get_get( 'p' ) ) $al .= '&amp;p=' . wppa_get_get( 'p' );
			if ( wppa_get_get( 'page_id' ) ) $al .= '&amp;page_id=' . wppa_get_get( 'page_id' );
		}
		$al .= '&amp;wppa-fromp=' . get_the_ID();
	}

	if ( wppa_get_get( 'lang' ) ) {	// If lang in querystring: keep it
		if ( strpos($al, 'lang=') === false ) { 	// Not yet
			if ( $key == 'js' ) $al .= '&lang=' . $wppa_lang;
			else $al .= '&amp;lang=' . $wppa_lang;
		}
	}

	if ( wppa( 'is_rootsearch' ) ) {
		if ( $key == 'js' ) $al .= '&rootsearch=1';
		else $al .= '&amp;rootsearch=1';
	}

	if ( wppa( 'debug' ) ) {
		$al .= '&amp;debug=' . wppa( 'debug' );
	}

	return $al.'&amp;';
}

// get page url of current album image
function wppa_get_image_page_url_by_id( $id, $single = false, $alb = false ) {

	if ( ! is_numeric( $id ) || $id < '1' ) wppa_dbg_msg( 'Invalid arg wppa_get_image_page_url_by_id(' . $id . ')', 'red' );

	$thumb = wppa_cache_thumb( $id );

	$occur = wppa_in_widget() ? wppa( 'widget_occur' ) : wppa( 'occur' );
	$w = wppa_in_widget() ? 'w' : '';

	if ( ! $alb ) $alb = $thumb['album'];

	if ( ! $occur ) $occur = '1';

	$result = wppa_get_permalink().'wppa-album='.$alb.'&amp;wppa-photo='.$thumb['id'].'&amp;wppa-cover=0&amp;wppa-'.$w.'occur='.$occur;
	if ( $single ) $result .= '&amp;wppa-single=1';

	return wppa_encrypt_url( $result );
}

// get page url of current album image, ajax version
function wppa_get_image_url_ajax_by_id($id) {

	if ( ! is_numeric($id) || $id < '1' ) wppa_dbg_msg('Invalid arg wppa_get_image_url_ajax_by_id('.$id.')', 'red');

	$thumb = wppa_cache_thumb($id);

	$occur = wppa_in_widget() ? wppa( 'widget_occur' ) : wppa( 'occur' );
	$w = wppa_in_widget() ? 'w' : '';

	return wppa_encrypt_url( wppa_get_ajaxlink().'wppa-album='.$thumb['album'].'&amp;wppa-photo='.$thumb['id'].'&amp;wppa-cover=0&amp;wppa-'.$w.'occur='.$occur );
}

// get link to album by id or in loop
function wppa_get_album_url( $id, $pag = '', $type = 'content', $occur = '0' ) {

	// Use forced occur ?
	if ( $occur ) {
		$w = '';
	}
	else {
		$occur = wppa_in_widget() ? wppa( 'widget_occur' ) : wppa( 'occur' );
		$w = wppa_in_widget() ? 'w' : '';
	}

	if ( $id ) {
		$link = wppa_get_permalink($pag).'wppa-album='.$id.'&amp;wppa-cover=0&amp;wppa-'.$w.'occur='.$occur;
		if ( $type == 'thumbs' ) {
			$link .= '&amp;wppa-photos-only=1';
		}
		if ( $type == 'albums' ) {
			$link .= '&amp;wppa-albums-only=1';
		}
	}
	else $link = '';
    return wppa_encrypt_url( $link );
}

// get link to album by id or in loop ajax version
function wppa_get_album_url_ajax( $id, $pag = '', $type = 'content' ) {

	$occur = wppa_in_widget() ? wppa( 'widget_occur' ) : wppa( 'occur' );
	$w = wppa_in_widget() ? 'w' : '';

	if ( $id ) {
		$link = wppa_get_ajaxlink($pag).'wppa-album='.$id.'&amp;wppa-cover=0&amp;wppa-'.$w.'occur='.$occur;
		if ( $type == 'thumbs' ) {
			$link .= '&amp;wppa-photos-only=1';
		}
		if ( $type == 'albums' ) {
			$link .= '&amp;wppa-albums-only=1';
		}
	}
	else $link = '';
    return wppa_encrypt_url( $link );
}

// get link to slideshow (in loop)
function wppa_get_slideshow_url($id, $page = '', $pid = '') {

	if ($id) {
		$occur = wppa_in_widget() ? wppa( 'widget_occur' ) : wppa( 'occur' );
		$w = wppa_in_widget() ? 'w' : '';
		$link = wppa_get_permalink( $page ) . 'wppa-album=' . $id.'&amp;wppa-slide' . '&amp;wppa-' . $w . 'occur=' . $occur;	// slide=true changed in slide
		if ( $pid ) $link .= '&amp;wppa-photo=' . $pid;
		if ( wppa( 'is_upldr' ) ) $link .= '&amp;wppa-upldr=' . wppa( 'is_upldr' );
		// can be extended for other special cases, see wppa_thumb_default() in wppa-functions.php
	}
	elseif (wppa( 'calendar') ) {
		$link = wppa_get_permalink( $page );
	}
	else {
		$link = '';
	}

	return wppa_encrypt_url( $link );
}

// get link to slideshow (in loop) ajax version
function wppa_get_slideshow_url_ajax($id, $page = '') {

	if ($id) {
		$occur = wppa_in_widget() ? wppa( 'widget_occur' ) : wppa( 'occur' );
		$w = wppa_in_widget() ? 'w' : '';
		$link = wppa_get_ajaxlink($page).'wppa-album='.$id.'&amp;wppa-slide'.'&amp;wppa-'.$w.'occur='.$occur;	// slide=true changed in slide
		if ( wppa( 'is_upldr' ) ) $link .= '&amp;wppa-upldr='.wppa( 'is_upldr' );
		// can be extended for other special cases, see wppa_thumb_default() in wppa-functions.php
	}
	elseif ( wppa( 'calendar') ) {
		$link = wppa_get_ajaxlink( $page ) .
					'&amp;wppa-calendar=' . wppa( 'calendar' ) .
					'&amp;wppa-caldate=' . wppa( 'caldate' ) .
					'&amp;wppa-slide' .
					( wppa( 'start_album' ) ? '&amp; wppa-album=' . wppa( 'start_album' ) : '' ) .
					'&amp;wppa-occur=' . wppa( 'mocc' );
	}
	else {
		$link = '';
	}

	return wppa_encrypt_url( $link );
}

// Pretty links decode
function wppa_convert_from_pretty( $uri ) {

	// Test if we should be here anyway
//	if ( ! isset($_ENV["SCRIPT_URI"]) ) return $uri;
	$wppapos = stripos($uri, '/wppaspec/');
	if ( ! $wppapos ) return $uri;	// Is not a pretty link
	if ( ! get_option('permalink_structure') ) return $uri;

	// copy start up to including slash before wppaspec
	$newuri = substr($uri, 0, $wppapos+1);

	// explode part after wppaspec/
	$args = explode('/', substr($uri, $wppapos+10));

	// process 'arguments'
	if ( count($args > 0) ) {
		$first = true;
		$possible_conflicts = array( 'wppa-page=' );

		foreach ( $args as $arg ) {
			if ( $first ) $newuri .= '?'; else $newuri .= '&';
			$first = false;
			$code = substr($arg, 0, 2);
			switch ( $code ) {
				case 'ab':
					$deltauri = 'wppa-album=';
					break;
				case 'pt':
					$deltauri = 'wppa-photo=';
					break;
				case 'sd':
					$deltauri = 'wppa-slide';
					break;
				case 'cv':
					$deltauri = 'wppa-cover=';
					break;
				case 'oc':
					$deltauri = 'wppa-occur=';
					break;
				case 'wo':
					$deltauri = 'wppa-woccur=';
					break;
				case 'pg':
					$deltauri = 'wppa-page=';
					break;
				case 'ss':
					$deltauri = 'wppa-searchstring=';
					break;
				case 'su':
					$deltauri = 'wppa-supersearch=';
					break;
				case 'tt':
					$deltauri = 'wppa-topten=';
					break;
				case 'lt':
					$deltauri = 'wppa-lasten=';
					break;
				case 'ct':
					$deltauri = 'wppa-comten=';
					break;
				case 'ft':
					$deltauri = 'wppa-featen=';
					break;
				case 'ln':
					$deltauri = 'lang=';
					break;
				case 'si':
					$deltauri = 'wppa-single=';
					break;
				case 'tg':
					$deltauri = 'wppa-tag=';
					break;
				case 'po':
					$deltauri = 'wppa-photos-only=';
					break;
				case 'ao':
					$deltauri = 'wppa-albums-only=';
					break;
				case 'mo':
					$deltauri = 'wppa-medals-only=';
					break;
				case 'db':
					$deltauri = 'debug=';
					break;
				case 'rl':
					$deltauri = 'wppa-rel=';
					break;
				case 'rc':
					$deltauri = 'wppa-relcount=';
					break;
				case 'ul':
					$deltauri = 'wppa-upldr=';
					break;
				case 'ow':
					$deltauri = 'wppa-owner=';
					break;
				case 'rt':
					$deltauri = 'wppa-rootsearch=';
					break;
				case 'hl':
					$deltauri = 'wppa-hilite=';
					break;
				case 'ca':
					$deltauri = 'wppa-calendar=';
					break;
				case 'cd':
					$deltauri = 'wppa-caldate=';
					break;
				case 'in':
					$deltauri = 'wppa-inv=';
					break;

				default:
					$deltauri = '';
			}

//			if ( wppa_switch( 'use_short_qargs' ) ) {
//				if ( ! in_array( $deltauri, $possible_conflicts ) ) {
//					$deltauri = str_replace( 'wppa-', '', $deltauri );
//				}

//			}
			if ( $deltauri ) {
				$newuri .= $deltauri;
				$newuri .= substr($arg, 2);
			}
			else {
				$newuri = rtrim( $newuri, '&' );
			}
		}
	}

	$newuri = wppa_trim_wppa_( $newuri );
	return $newuri;
}

// Pretty links Encode
function wppa_convert_to_pretty( $xuri ) {

	// Make local copy
	$uri = $xuri;

	// Only when permalink structure is not default
	if ( ! get_option('permalink_structure') ) return $uri;

	// Not on front page, the redirection will fail...
//	if ( is_front_page() ) {
//		return $uri;
//	}

	// Any querystring?
	if ( strpos( $uri, '?' ) === false ) {
		return $uri;
	}

	// Not during search. Otherwise wppa_test_for_search('at_session_start') returns '';
	// and this will destroy use and display searchstrings in wppa_session_start()
	// Fix in 6.3.9
	if ( strpos( $uri, 'searchstring' ) ) {
		return $uri;
	}

	// Re-order
	if ( strpos( $uri, '&amp;' ) !== false ) {
		$amps = true;
		$uri = str_replace( '&amp;', '&', $uri );
	}
	else {
		$amps = false;
	}
	$parts = explode( '?', $uri );

	// If is_front_page(), redirection will fail
	// During ajax, is_front_page() returns always false
	// So we test on the existence of a page id ( a single / ) instead
	// If no slash found, its http://mysite.com/wppaspec/.... this will fail: return xuri
	$temp = $parts[0];							// Befor the ?
	$temp = rtrim( $temp, '/' ); 				// remove potential trailing slash
	$temp = str_replace( '//', '', $temp ); 	// Remove //
	if ( strpos( $temp, '/' ) === false ) {		// Yep, on frontpage

		// return original, optionally wppa_ trimmed and album names.
		return wppa_album_number_to_name_in_uri( wppa_trim_wppa_( $xuri ) );
	}

	$args = explode( '&', $parts[1] );
	$order = array( 'occur', 'woccur',
					'searchstring',
					'supersearch',
					'topten', 'lasten', 'comten', 'featen',
					'lang',
					'single',
					'tag',
					'photos-only',
					'albums-only',
					'medals-only',
					'rel',
					'relcount',
					'upldr',
					'owner',
					'rootsearch',
					'slide', 'cover', 'page',
					'album', 'photo', 'hilite',
					'calendar', 'caldate',
					'debug',
					'inv',
					);

	$uri = $parts[0] . '?';
	$first = true;
	foreach ( $order as $item ) {
		foreach ( array_keys($args) as $argidx ) {
			if ( strpos( $args[$argidx], $item ) === 0 || strpos( $args[$argidx], 'wppa-' . $item ) === 0 ) {
				if ( ! $first ) {
					$uri .= '&';
				}
				$uri .=  $args[$argidx];
				unset ( $args[$argidx] );
				$first = false;
			}
		}
	}
	foreach ( $args as $arg ) {	// append unprocessed items
		$uri .= '&' . $arg;
	}
	if ( $amps ) {
		$uri = str_replace( '&', '&amp;', $uri );
	}

	// First filter for short query args
	$uri = wppa_trim_wppa_( $uri );

	// Now filter for album names in urls
	$uri = wppa_album_number_to_name_in_uri( $uri );

	// Now filter for photo names in urls
	if ( wppa_switch( 'use_photo_names_in_urls' ) ) {
		$start 	= 0;
		$end 	= 0;
		$ppos 	= strpos( $uri, 'photo=' );
		if ( $ppos !== false ) {
			$start = $ppos + '6';
			$end = strpos( $uri, '&', $start );
		}
		$before = substr( $uri, 0, $start );
		if ( $end ) {
			$id = substr( $uri, $start, $end - $start );
			if ( wppa_is_int( $id ) ) {	// Can convert single integer photo ids only
				$after = substr( $uri, $end );
				$pname = stripslashes( wppa_get_photo_item( $id, 'name' ) );
				$pname = wppa_encode_uri_component( $pname );
				$uri = $before . $pname . $after;
			}
		}
		else {
			$id = substr( $uri, $start );
			if ( wppa_is_int( $id ) ) {	// Can convert single integer photo ids only
				$pname = stripslashes( wppa_get_photo_item( $id, 'name' ) );
				$pname = wppa_encode_uri_component( $pname );
				$uri = $before . $pname;
			}
		}
	}

	// Now urlencode for funny chars
	$uri = str_replace( array( ' ', '[', ']' ), array( '%20', '%5B', '%5D' ), $uri );

	// Now the actual conversion to pretty links
	if ( ! wppa_switch('wppa_use_pretty_links') ) return $uri;
	if ( ! get_option('permalink_structure') ) return $uri;

	// Leaving the next line out gives 404 on pretty links under certain circumstances.
	// Can not reproduce and also do not understand why, and do not remember why i have put it in.
	//
	// nov 5 2014: changed add_action to test on redirection form init to pplugins_loaded.
	// also skipped if ( ! isset($_ENV["SCRIPT_URI"]) ) return; in redirect test. See wpp-non-admin.php. Seems to work now
//	if ( ! isset($_ENV["SCRIPT_URI"]) ) return $uri;

	// Do some preprocessing
	$uri = str_replace('&amp;', '&', $uri);
	$uri = str_replace('?wppa-', '?', $uri);
	$uri = str_replace('&wppa-', '&', $uri);

	// Test if querystring exists
	$qpos = stripos($uri, '?');
	if ( ! $qpos ) return $uri;

	// Make sure we end without '/'
	$newuri = trim(substr($uri, 0, $qpos), '/');
	$newuri .= '/wppaspec';

	// explode querystring
	$args = explode('&', substr($uri, $qpos+1));
	$support = array(	'album',
						'photo',
						'slide',
						'cover',
						'occur',
						'woccur',
						'page',
						'searchstring',
						'supersearch',
						'topten',
						'lasten',
						'comten',
						'featen',
						'lang',
						'single',
						'tag',
						'photos-only',
						'albums-only',
						'medals-only',
						'debug',
						'rel',
						'relcount',
						'upldr',
						'owner',
						'rootsearch',
						'hilite',
						'calendar',
						'caldate',
						'inv',
					);
	if ( count($args) > 0 ) {
		foreach ( $args as $arg ) {
			$t = explode('=', $arg);
			$code = $t['0'];
			if ( isset($t['1']) ) $val = $t['1']; else $val = false;
			if ( in_array( $code, $support ) ) {
				$newuri .= '/';
				switch ( $code ) {
					case 'album':
						$newuri .= 'ab';
						break;
					case 'photo':
						$newuri .= 'pt';
						break;
					case 'slide':
						$newuri .= 'sd';
						break;
					case 'cover':
						$newuri .= 'cv';
						break;
					case 'occur':
						$newuri .= 'oc';
						break;
					case 'woccur':
						$newuri .= 'wo';
						break;
					case 'page':
						$newuri .= 'pg';
						break;
					case 'searchstring':
						$newuri .= 'ss';
						break;
					case 'supersearch':
						$newuri .= 'su';
						break;
					case 'topten':
						$newuri .= 'tt';
						break;
					case 'lasten':
						$newuri .= 'lt';
						break;
					case 'comten':
						$newuri .= 'ct';
						break;
					case 'featen':
						$newuri .= 'ft';
						break;
					case 'lang':
						$newuri .= 'ln';
						break;
					case 'single':
						$newuri .= 'si';
						break;
					case 'tag':
						$newuri .= 'tg';
						break;
					case 'photos-only':
						$newuri .= 'po';
						break;
					case 'albums-only':
						$newuri .= 'ao';
						break;
					case 'medals-only':
						$newuri .= 'mo';
						break;
					case 'debug':
						$newuri .= 'db';
						break;
					case 'rel':
						$newuri .= 'rl';
						break;
					case 'relcount':
						$newuri .= 'rc';
						break;
					case 'upldr':
						$newuri .= 'ul';
						break;
					case 'owner':
						$newuri .= 'ow';
						break;
					case 'rootsearch':
						$newuri .= 'rt';
						break;
					case 'hilite':
						$newuri .= 'hl';
						break;
					case 'calendar':
						$newuri .= 'ca';
						break;
					case 'caldate':
						$newuri .= 'cd';
						break;
					case 'inv':
						$newuri .= 'in';
						break;
				}
				if ( $val !== false ) {
					if ( $code == 'searchstring' ) $newuri .= str_replace(' ', '_', $val);
					else $newuri .= $val;
				}
			}
		}
	}

	return $newuri;
}

// Replace album number by name in url
function wppa_album_number_to_name_in_uri( $uri ) {

	if ( wppa_switch( 'use_album_names_in_urls' ) ) {

		$apos = strpos( $uri, 'album=' );

		if ( $apos === false ) {
			return $uri;
		}
		else {
			$start = $apos + '6';
			$end = strpos( $uri, '&', $start );
		}

		$before = substr( $uri, 0, $start );
		if ( $end ) {
			$albnum = substr( $uri, $start, $end - $start );
			if ( wppa_is_int( $albnum ) && $albnum > '0' ) {	// Can convert single positive integer album ids only
				$after	= substr( $uri, $end );
				$albnam = stripslashes( wppa_get_album_item( $albnum, 'name' ) );
				$albnam = wppa_encode_uri_component( $albnam );
				$uri = $before . $albnam . $after;
			}
		}
		else {
			$albnum = substr( $uri, $start );
			if ( wppa_is_int( $albnum ) && $albnum > '0' ) {	// Can convert single positive integer album ids only
				$albnam = stripslashes( wppa_get_album_item( $albnum, 'name' ) );
				$albnam = wppa_encode_uri_component( $albnam );
				$uri = $before . $albnam;
			}
		}
	}

	return $uri;
}

// Moderate links
function wppa_moderate_links( $type, $id, $comid = '' ) {

	$thumb = wppa_cache_thumb( $id );

	if ( current_user_can('wppa_moderate') || ( current_user_can('wppa_comments') && $type == 'comment' ) ) {
		switch ( $type ) {
			case 'thumb':
				$app = __('App', 'wp-photo-album-plus');
				$mod = __('Mod', 'wp-photo-album-plus');
				$del = __('Del', 'wp-photo-album-plus');

				$result = '
				<div style="clear:both;"></div>
				<a class="wppa-approve-'.$id.'" style="font-weight:bold; color:green; cursor:pointer;" onclick="if ( confirm(\''.__('Are you sure you want to publish this photo?', 'wp-photo-album-plus').'\') ) wppaAjaxApprovePhoto(\''.$id.'\')">
					'.$app.
				'</a>
				<a class="wppa-approve-'.$id.'" style="font-weight:bold; color:blue; cursor:pointer;" onclick="document.location=\''.get_admin_url().'admin.php?page=wppa_moderate_photos&amp;photo='.$id.'\'" >
					'.$mod.
				'</a>
				<a class="wppa-approve-'.$id.'" style="font-weight:bold; color:red; cursor:pointer;" onclick="if ( confirm(\''.__('Are you sure you want to remove this photo?', 'wp-photo-album-plus').'\') ) wppaAjaxRemovePhoto(\''.$id.'\', true)">
					'.$del.
				'</a><br class="wppa-approve-'.$id.'" />';
				break;
			case 'slide':
				$app = __('Approve', 'wp-photo-album-plus');
				$mod = __('Moderate', 'wp-photo-album-plus');
				$del = __('Delete', 'wp-photo-album-plus');

				$result = '
				<div style="clear:both;"></div>
				<a class="wppa-approve-'.$id.'" style="font-weight:bold; color:green; cursor:pointer;" onclick="if ( confirm(\''.__('Are you sure you want to publish this photo?', 'wp-photo-album-plus').'\') ) wppaAjaxApprovePhoto(\''.$id.'\')">
					'.$app.
				'</a>
				<a class="wppa-approve-'.$id.'" style="font-weight:bold; color:blue; cursor:pointer;" onclick="document.location=\''.get_admin_url().'admin.php?page=wppa_moderate_photos&amp;photo='.$id.'\'" >
					'.$mod.
				'</a>
				<a class="wppa-approve-'.$id.'" style="font-weight:bold; color:red; cursor:pointer;" onclick="if ( confirm(\''.__('Are you sure you want to remove this photo?', 'wp-photo-album-plus').'\') ) wppaAjaxRemovePhoto(\''.$id.'\', true)">
					'.$del.
				'</a><br class="wppa-approve-'.$id.'" />';
				break;
			case 'comment':
				$app = __('Approve', 'wp-photo-album-plus');
				$mod1 = __('PhotoAdmin', 'wp-photo-album-plus');
				$mod2 = __('CommentAdmin', 'wp-photo-album-plus');
				$del = __('Delete', 'wp-photo-album-plus');
				$result = '
				<br class="wppa-approve-'.$comid.'" />
				<a class="wppa-approve-'.$comid.'" style="font-weight:bold; color:green; cursor:pointer;" onclick="if ( confirm(\''.__('Are you sure you want to publish this comment?', 'wp-photo-album-plus').'\') ) wppaAjaxApproveComment(\''.$comid.'\')">
					'.$app.
				'</a>';
				if ( current_user_can('wppa_moderate') ) $result .= '
				<a class="wppa-approve-'.$comid.'" style="font-weight:bold; color:blue; cursor:pointer;" onclick="document.location=\''.get_admin_url().'admin.php?page=wppa_moderate_photos&amp;photo='.$id.'\'" >
					'.$mod1.
				'</a>';
				if ( current_user_can('wppa_comments') ) $result .= '
				<a class="wppa-approve-'.$comid.'" style="font-weight:bold; color:blue; cursor:pointer;" onclick="document.location=\''.get_admin_url().'admin.php?page=wppa_manage_comments&amp;commentid='.$comid.'\'" >
					'.$mod2.
				'</a>';
				$result .= '
				<a class="wppa-approve-'.$comid.'" style="font-weight:bold; color:red; cursor:pointer;" onclick="if ( confirm(\''.__('Are you sure you want to remove this comment?', 'wp-photo-album-plus').'\') ) wppaAjaxRemoveComment(\''.$comid.'\', true)">
					'.$del.
				'</a><br class="wppa-approve-'.$comid.'" />';
				break;
			default:
			echo 'error type='.$type;
				break;
		}
	}
	else {
		if ( $type == 'comment' || $thumb['status'] != 'scheduled' ) {
			$result = '<div style="clear:both; color:red">'.__('Awaiting moderation', 'wp-photo-album-plus').'</div>';
		}
		else {
			$result = '<div style="clear:both; color:red">'.sprintf( __( 'Scheduled for %s' , 'wp-photo-album-plus'), wppa_format_scheduledtm( $thumb['scheduledtm'] ) ).'</div>';
		}
	}
	return $result;
}

// Get the type of link for an album title. Used in wppa-breadcrumb.php
function wppa_get_album_title_linktype($alb) {
global $wpdb;

	if ( is_numeric($alb) ) $result = $wpdb->get_var( $wpdb->prepare( "SELECT cover_linktype FROM ".WPPA_ALBUMS." WHERE id = %s LIMIT 1", $alb ) );
	else $result = '';
	wppa_dbg_q('Q59');

	return $result;
}

function wppa_get_slide_callback_url( $id ) {

	$url = wppa_get_permalink();
	if ( wppa( 'start_album' ) ) $url .= 'wppa-album=' . wppa( 'start_album' ) . '&amp;';
	else $url .= 'wppa-album=0&amp;';
	$url .= 'wppa-cover=0&amp;';
	$url .= 'wppa-slide&amp;';
	if ( wppa( 'is_single' ) ) $url .= 'wppa-single=1&amp;';
	if ( wppa_in_widget() ) $url .= 'wppa-woccur=' . wppa( 'widget_occur' ) . '&amp;';
	else $url .= 'wppa-occur=' . wppa( 'occur' ) . '&amp;';
	if ( wppa( 'is_topten' ) ) $url .= 'wppa-topten=' . wppa( 'topten_count' ) . '&amp;';
	if ( wppa( 'is_lasten' ) ) $url .= 'wppa-lasten=' . wppa( 'lasten_count' ) . '&amp;';
	if ( wppa( 'is_comten' ) ) $url .= 'wppa-comten=' . wppa( 'comten_count' ) . '&amp;';
	if ( wppa( 'is_related' ) ) $url .= 'wppa-rel=' . wppa( 'is_related' ) . '&amp;wppa-relcount=' . wppa( 'related_count' ) . '&amp;';
	elseif ( wppa( 'is_tag' ) ) $url .= 'wppa-tag=' . wppa( 'is_tag' ) . '&amp;';
	$url .= 'wppa-photo=' . $id;
//	if ( wppa( 'is_owner' ) ) $url .= 'wppa-owner=' . wppa( 'is_owner' ) . '&amp;';
	if ( wppa( 'is_upldr' ) ) $url .= 'wppa-upldr=' . wppa( 'is_upldr' ) . '&amp;';

	return wppa_encrypt_url( $url );
}

function wppa_get_thumb_callback_url() {

	$url = wppa_get_permalink();
	if ( wppa( 'start_album' ) ) $url .= 'wppa-album=' . wppa( 'start_album' ) . '&amp;';
	else $url .= 'wppa-album=0&amp;';
	$url .= 'wppa-cover=0&amp;';
	if ( wppa( 'is_single' ) ) $url .= 'wppa-single=1&amp;';
	if ( wppa_in_widget() ) $url .= 'wppa-woccur=' . wppa( 'widget_occur' ) . '&amp;';
	else $url .= 'wppa-occur=' . wppa( 'occur' ) . '&amp;';
	if ( wppa( 'is_topten' ) ) $url .= 'wppa-topten=' . wppa( 'topten_count' ) . '&amp;';
	if ( wppa( 'is_lasten' ) ) $url .= 'wppa-lasten=' . wppa( 'lasten_count' ) . '&amp;';
	if ( wppa( 'is_comten' ) ) $url .= 'wppa-comten=' . wppa( 'comten_count' ) . '&amp;';
	if ( wppa( 'is_related' ) ) $url .= 'wppa-rel=' . wppa( 'is_related' ) . '&amp;wppa-relcount=' . wppa( 'related_count' ) . '&amp;';
	elseif ( wppa( 'is_tag' ) ) $url .= 'wppa-tag=' . wppa( 'is_tag' ) . '&amp;';
//	if ( wppa( 'is_owner' ) ) $url .= 'wppa-owner=' . wppa( 'is_owner' ) . '&amp;';
	if ( wppa( 'is_upldr' ) ) $url .= 'wppa-upldr=' . wppa( 'is_upldr' ) . '&amp;';

	$url = substr($url, 0, strlen($url) - 5);	// remove last '&amp;'

	return wppa_encrypt_url( $url );
}

function wppa_get_upldr_link( $user ) {

	$result = wppa_get_permalink( wppa_opt( 'upldr_widget_linkpage' ) );
	$result .= 'wppa-upldr=' . $user . '&amp;';
	$result .= 'wppa-cover=0&amp;';
	$result .= 'wppa-occur=1';
	$result = str_replace( ' ', '%20', wppa_trim_wppa_( $result ) );

	return $result;
}

function wppa_page_links( $npages = '1', $curpage = '1', $slide = false ) {

	if ( $npages < '2' ) return;	// Nothing to display
	if ( is_feed() ) {
		return;
	}

	// Compose the Previous and Next Page urls

	// Get the main link
	$link_url = wppa_get_permalink();
	$ajax_url = wppa_get_ajaxlink();

	// cover
	if ( wppa_get_get( 'cover' ) ) $ic = wppa_get_get( 'cover' );
	else {
		if ( wppa( 'is_cover' ) == '1' ) $ic = '1'; else $ic = '0';
	}
	$extra_url = 'wppa-cover='.$ic;

	// occur
	$occur = wppa_in_widget() ? wppa_get_get('woccur') : wppa_get_get('occur');
	$ref_occur = wppa_in_widget() ? wppa( 'widget_occur' ) : wppa( 'occur' );

	// album
	if ( ( $occur == $ref_occur || wppa( 'ajax' ) ) && wppa_get_get('album') ) {
			$alb = wppa_get_get( 'album' );
	}
	elseif ( wppa( 'start_album' ) ) {
		$alb = wppa( 'start_album' );
	}
	else {
		$alb = '0';
	}
	$extra_url .= '&amp;wppa-album='.$alb;

	// slide or photo
	if ( $slide ) {
		$extra_url .= '&amp;wppa-slide=1';
	}
	elseif ( wppa_get_get( 'photo' ) ) {
		$extra_url .= '&amp;wppa-photo=' . wppa_get_get( 'photo' );
	}

	// occur
	if ( ! wppa( 'ajax' ) ) {
		$occur = wppa_in_widget() ? wppa( 'widget_occur' ) : wppa( 'occur' );
		$w = wppa_in_widget() ? 'w' : '';
		$extra_url .= '&amp;wppa-' . $w . 'occur=' . $occur;
	}
	else {
		if ( wppa_get_get( 'occur' ) ) {
			$occur = wppa_get_get( 'occur' );
			$extra_url .= '&amp;wppa-occur=' . strval( intval( $occur ) );
		}
		elseif ( wppa_get_get( 'woccur' ) ) {
			$occur = wppa_get_get( 'woccur' );
			$extra_url .= '&amp;wppa-woccur=' . strval( intval( $occur ) );
		}
		else {
			$extra_url .= '&amp;wppa-occur=' . wppa( 'occur' );	// Should never get here?
		}
	}

	// Topten?
	if ( wppa( 'is_topten' ) ) $extra_url .= '&amp;wppa-topten='.wppa( 'topten_count' );

	// Lasten?
	if ( wppa( 'is_lasten' ) ) $extra_url .= '&amp;wppa-lasten='.wppa( 'lasten_count' );

	// Comten?
	if ( wppa( 'is_comten' ) ) $extra_url .= '&amp;wppa-comten='.wppa( 'comten_count' );

	// Featen?
	if ( wppa( 'is_featen' ) ) $extra_url .= '&amp;wppa-featen='.wppa( 'featen_count' );

	// Tag?
	if ( wppa( 'is_tag' ) && ! wppa( 'is_related' ) ) $extra_url .= '&amp;wppa-tag='.wppa( 'is_tag' );

	// Search?
	if ( wppa( 'src' ) && ! wppa( 'is_related' ) ) $extra_url .= '&amp;wppa-searchstring='.urlencode( wppa( 'searchstring' ) );

	// Supersearch?
	if ( wppa( 'supersearch' ) ) $extra_url .= '&amp;wppa-supersearch=' . urlencode( wppa( 'supersearch' ) );

	// Related
	if ( wppa( 'is_related' ) ) $extra_url .= '&amp;wppa-rel='.wppa( 'is_related' ).'&amp;wppa-relcount='.wppa( 'related_count' );

	// Uploader?
	if ( wppa( 'is_upldr' ) ) $extra_url .= '&amp;wppa-upldr='.wppa( 'is_upldr' );

	// Calendar ?
	if ( wppa( 'calendar' ) ) $extra_url .= '&amp;wppa-calendar='.wppa( 'calendar' ).'&amp;wppa-caldate='.wppa( 'caldate' );

	// Photos only?
	if ( wppa( 'photos_only' ) ) $extra_url .= '&amp;wppa-photos-only=1';

	// Albums only?
	if ( wppa( 'albums_only' ) ) $extra_url .= '&amp;wppa-albums-only=1';

	// Inverse?
	if ( wppa( 'is_inverse' ) ) $extra_url .= '&amp;wppa-inv=1';

	// Almost ready
	$link_url .= $extra_url;
	$ajax_url .= $extra_url;

	// Compress
	$link_url = wppa_trim_wppa_( $link_url );
	$ajax_url = wppa_trim_wppa_( $ajax_url );

	// Encrypt
	$link_url = wppa_encrypt_url( $link_url );
	$ajax_url = wppa_encrypt_url( $ajax_url );

	// Adjust display range
	$from = 1;
	$to = $npages;
	if ( $npages > wppa_opt( 'pagelinks_max' ) ) {
		$delta = floor( wppa_opt( 'pagelinks_max' ) / 2 );
		$from = $curpage - $delta;
		$to = $curpage + $delta;
		while ($from < '1') {
			$from++;
			$to++;
		}
		while ($to > $npages) {
			$from--;
			$to--;
		}
	}

	// Doit
	wppa_out( "\n".'<div class="wppa-nav-text wppa-box wppa-nav" style="clear:both; text-align:center; '.__wcs('wppa-box').__wcs('wppa-nav').'" >' );

		$vis = $curpage == '1' ? 'visibility: hidden;' : '';
		wppa_out( '<div style="float:left; text-align:left; '.$vis.'">' );
			wppa_out( '<span class="wppa-arrow" style="'.__wcs('wppa-arrow').'cursor: default;">&laquo;&nbsp;</span>' );
			if ( wppa_switch('wppa_allow_ajax') ) {
				wppa_out( '<a style="cursor:pointer;" onclick="wppaDoAjaxRender('.wppa( 'mocc' ).', \''.$ajax_url.'&amp;wppa-page='.($curpage - 1).'\', \''.wppa_convert_to_pretty($link_url.'&amp;wppa-page='.($curpage - 1)).'\')" >'.__('Previous', 'wp-photo-album-plus').'</a>' );
			}
			else {
				wppa_out( '<a style="cursor:pointer;" href="'.$link_url.'&amp;wppa-page='.($curpage - 1).'" >'.__('Previous', 'wp-photo-album-plus').'</a>' );
			}
		wppa_out( '</div><!-- #prev-page -->' );

		$vis = $curpage == $npages ? 'visibility: hidden;' : '';
		wppa_out( '<div style="float:right; text-align:right; '.$vis.'">' );
			if ( wppa_switch('wppa_allow_ajax') ) {
				wppa_out( '<a style="cursor:pointer;" onclick="wppaDoAjaxRender('.wppa( 'mocc' ).', \''.$ajax_url.'&amp;wppa-page='.($curpage + 1).'\', \''.wppa_convert_to_pretty($link_url.'&amp;wppa-page='.($curpage + 1)).'\')" >'.__('Next', 'wp-photo-album-plus').'</a>' );
			}
			else {
				wppa_out( '<a style="cursor:pointer;" href="'.$link_url.'&amp;wppa-page='.($curpage + 1).'" >'.__('Next', 'wp-photo-album-plus').'</a>' );
			}
			wppa_out( '<span class="wppa-arrow" style="'.__wcs('wppa-arrow').'cursor: default;">&nbsp;&raquo;</span>' );
		wppa_out( '</div><!-- #next-page -->' );

		// The numbered pagelinks ?
		if ( wppa_opt( 'pagelinks_max' ) ) {
			if ($from > '1') {
				wppa_out( '.&nbsp;.&nbsp;.&nbsp;' );
			}
			for ($i=$from; $i<=$to; $i++) {
				if ($curpage == $i) {
					wppa_out( '<div class="wppa-mini-box wppa-alt wppa-black" style="display:inline; text-align:center; '.__wcs('wppa-mini-box').__wcs('wppa-alt').__wcs('wppa-black').' text-decoration: none; cursor: default; font-weight:normal; " >' );
						wppa_out( '&nbsp;'.$i.'&nbsp;' );
					wppa_out( '</div>' );
				}
				else {
					wppa_out( '<div class="wppa-mini-box wppa-even" style="display:inline; text-align:center; '.__wcs('wppa-mini-box').__wcs('wppa-even').'" >' );
						if ( ( ! $slide && wppa_switch('wppa_allow_ajax') ) || ( $slide && wppa_switch('wppa_slideshow_page_allow_ajax') ) ) {
							wppa_out( '<a style="cursor:pointer;" onclick="wppaDoAjaxRender('.wppa( 'mocc' ).', \''.$ajax_url.'&amp;wppa-page='.$i.'\', \''.wppa_convert_to_pretty($link_url.'&amp;wppa-page='.$i).'\')">&nbsp;'.$i.'&nbsp;</a>' );
						}
						else {
							wppa_out( '<a style="cursor:pointer;" href="'.$link_url.'&amp;wppa-page='.$i.'">&nbsp;'.$i.'&nbsp;</a>' );
						}
					wppa_out( '</div>' );
				}
			}
			if ( $to < $npages ) {
				wppa_out( '&nbsp;.&nbsp;.&nbsp;.' );
			}
		}

		// The 3/17 indicator
		else {
			wppa_out( $curpage . '/' . $npages );
		}


	wppa_out( '</div><!-- #prevnext-a-'.wppa( 'mocc' ).' -->' );
}

function wppa_album_download_link( $albumid ) {

	if ( ! wppa_switch( 'allow_download_album' ) ) return;	// Not enabled

	wppa_out( '<div style="clear:both;" ></div>' );
	wppa_out( '<a onclick="wppaAjaxDownloadAlbum('.wppa( 'mocc' ).', \''.wppa_encrypt_album($albumid).'\' );" style="cursor:pointer;" title="'.__('Download', 'wp-photo-album-plus').'">'.__('Download album', 'wp-photo-album-plus').'</a>' );
	wppa_out( '<img id="dwnspin-'.wppa( 'mocc' ).'-'.$albumid.'" src="'.wppa_get_imgdir().'wpspin.gif" style="margin-left:6px; display:none;" alt="spin" />' );
}

function wppa_get_imglnk_a( $wich, $id, $lnk = '', $tit = '', $onc = '', $noalb = false, $album = '' ) {
global $wpdb;

	// make sure the photo data ia available
	$thumb = wppa_cache_thumb( $id );
	if ( ! $thumb ) return false;

	// Is it a video?
	$is_video = wppa_is_video( $id, true );

	// Photo Specific Overrule?
	if ( ( $wich == 'sphoto'     && wppa_switch( 'sphoto_overrule' ) ) ||
		 ( $wich == 'mphoto'     && wppa_switch( 'mphoto_overrule' ) ) ||
		 ( $wich == 'thumb'      && wppa_switch( 'thumb_overrule' ) ) ||
		 ( $wich == 'topten'     && wppa_switch( 'topten_overrule' ) ) ||
		 ( $wich == 'featen'	 && wppa_switch( 'featen_overrule' ) ) ||
		 ( $wich == 'lasten'     && wppa_switch( 'lasten_overrule' ) ) ||
		 ( $wich == 'sswidget'   && wppa_switch( 'sswidget_overrule' ) ) ||
		 ( $wich == 'potdwidget' && wppa_switch( 'potdwidget_overrule' ) ) ||
		 ( $wich == 'coverimg'   && wppa_switch( 'coverimg_overrule' ) ) ||
		 ( $wich == 'comten'	 && wppa_switch( 'comment_overrule' ) ) ||
		 ( $wich == 'slideshow'  && wppa_switch( 'slideshow_overrule' ) ) ||
		 ( $wich == 'tnwidget' 	 && wppa_switch( 'thumbnail_widget_overrule' ) ) ) {

		// Look for a photo specific link
		if ( $thumb ) {
			// If it is there...
			if ( $thumb['linkurl'] ) {
				// Use it. It superceeds other settings
				$result['url'] = esc_attr( $thumb['linkurl'] );
				$result['title'] = esc_attr( __( stripslashes( $thumb['linktitle'] ) ) );
				$result['is_url'] = true;
				$result['is_lightbox'] = false;
				$result['onclick'] = '';
				$result['target'] = $thumb['linktarget'];
				return $result;
			}
		}
	}

	$result['target'] = '_self';
	$result['title'] = '';
	$result['onclick'] = '';
	switch ( $wich ) {
		case 'sphoto':
			$type = wppa_opt( 'sphoto_linktype' );
			$page = wppa_opt( 'sphoto_linkpage' );
			if ( $page == '0' ) $page = '-1';
			if ( wppa_switch( 'sphoto_blank' ) ) $result['target'] = '_blank';
			break;
		case 'mphoto':
			$type = wppa_opt( 'mphoto_linktype' );
			$page = wppa_opt( 'mphoto_linkpage' );
			if ( $page == '0' ) $page = '-1';
			if ( wppa_switch( 'mphoto_blank' ) ) $result['target'] = '_blank';
			break;
		case 'thumb':
			$type = wppa_opt( 'thumb_linktype' );
			$page = wppa_opt( 'thumb_linkpage' );
			if ( wppa_switch( 'thumb_blank' ) ) $result['target'] = '_blank';
			break;
		case 'topten':
			$type = wppa_opt( 'topten_widget_linktype' );
			$page = wppa_opt( 'topten_widget_linkpage' );
			if ( $page == '0' ) $page = '-1';
			if ( wppa_switch( 'topten_blank' ) ) $result['target'] = '_blank';
			break;
		case 'featen':
			$type = wppa_opt( 'featen_widget_linktype' );
			$page = wppa_opt( 'featen_widget_linkpage' );
			if ( $page == '0' ) $page = '-1';
			if ( wppa_switch( 'featen_blank' ) ) $result['target'] = '_blank';
			break;
		case 'lasten':
			$type = wppa_opt( 'lasten_widget_linktype' );
			$page = wppa_opt( 'lasten_widget_linkpage' );
			if ( $page == '0' ) $page = '-1';
			if ( wppa_switch( 'lasten_blank' ) ) $result['target'] = '_blank';
			break;
		case 'comten':
			$type = wppa_opt( 'comment_widget_linktype' );
			$page = wppa_opt( 'comment_widget_linkpage' );
			if ( $page == '0' ) $page = '-1';
			if ( wppa_switch( 'comment_blank' ) ) $result['target'] = '_blank';
			break;
		case 'sswidget':
			$type = wppa_opt( 'slideonly_widget_linktype' );
			$page = wppa_opt( 'slideonly_widget_linkpage' );
			if ( $page == '0' ) $page = '-1';
			if ( wppa_switch( 'sswidget_blank' ) ) $result['target'] = '_blank';
			$result['url'] = '';
			if ( $type == 'lightbox' || $type == 'lightboxsingle' || $type == 'file' ) {
				$result['title'] = wppa_zoom_in( $id );
				$result['target'] = '';
				return $result;
			}
			break;
		case 'potdwidget':
			$type = wppa_opt( 'widget_linktype' );
			$page = wppa_opt( 'widget_linkpage' );
			if ( $page == '0' ) $page = '-1';
			if ( wppa_switch( 'potd_blank' ) ) $result['target'] = '_blank';
			break;
		case 'coverimg':
			$type = wppa_opt( 'coverimg_linktype' );
			$page = wppa_opt( 'coverimg_linkpage' );
			if ( $page == '0' ) $page = '-1';
			if ( wppa_switch( 'coverimg_blank' ) ) $result['target'] = '_blank';
			if ( $type == 'slideshowstartatimage' ) {
				$result['url'] = wppa_encrypt_url( wppa_get_slideshow_url( $album, $page, $id) );
				$result['is_url'] = true;
				$result['is_lightbox'] = false;
				return $result;
			}
			break;
		case 'tnwidget':
			$type = wppa_opt( 'thumbnail_widget_linktype' );
			$page = wppa_opt( 'thumbnail_widget_linkpage' );
			if ( $page == '0' ) $page = '-1';
			if ( wppa_switch( 'thumbnail_widget_blank' ) ) $result['target'] = '_blank';
			break;
		case 'slideshow':
			$type = wppa_opt( 'slideshow_linktype' );	//'';
			$page = wppa_opt( 'slideshow_linkpage' );
			$result['url'] = '';
			if ( $type == 'lightbox' || $type == 'lightboxsingle' || $type == 'file' ) {
				$result['title'] = wppa_zoom_in( $id );
				$result['target'] = '';
				return $result;
			}
			if ( $type == 'thumbs' ) {
				$result['url'] = wppa_encrypt_url( wppa_get_ss_to_tn_link( $page, $id ) );
				$result['title'] = __('View thumbnails', 'wp-photo-album-plus');
				$result['is_url'] = true;
				$result['is_lightbox'] = false;
				if ( wppa_switch( 'slideshow_blank' ) ) $result['target'] = '_blank';
				return $result;
			}
			if ( $type == 'slide' ) {	// Extension for Johnnymosaic
				$t = wppa( 'occur' );
				if ( $page != '0' ) {
					wppa( 'occur', '1' );
				}
				$result['url'] = wppa_encrypt_url( wppa_get_slideshow_url( wppa( 'start_album' ), $page, $id ) );
				wppa( 'occur', $t );
				$result['title'] = __('View fullsize slideshow', 'wp-photo-album-plus');
				$result['is_url'] = true;
				$result['is_lightbox'] = false;
				if ( wppa_switch( 'slideshow_blank' ) ) $result['target'] = '_blank';
				return $result;
			}
			if ( $type == 'none' ) return;
			// Continue for 'single'
			break;
		case 'albwidget':
			$type = wppa_opt( 'album_widget_linktype' );
			$page = wppa_opt( 'album_widget_linkpage' );
			if ( $page == '0' ) $page = '-1';
			if ( wppa_switch( 'album_widget_blank' ) ) $result['target'] = '_blank';
			break;
		default:
			return false;
			break;
	}

	if ( ! $album ) {
		$album = wppa( 'start_album' );
	}
	if ( $album == '' && ! wppa( 'is_upldr' ) ) {	/**/
		$album = wppa_get_album_id_by_photo_id( $id );
	}
	if ( is_numeric( $album ) ) {
		$album_name = wppa_get_album_name( $album );
	}
	else $album_name = '';

	if ( ! $album ) $album = '0';
	if ( $wich == 'comten' ) $album = '0';

//	if ( wppa( 'is_tag' ) ) $album = '0'; // Tags can now also be on an album selecion ( made by album="#cat,cat|#tags,tag" )

	if ( wppa( 'supersearch' ) ) $album = '0';

	if ( wppa( 'calendar' ) ) $album = wppa( 'start_album' ) ? wppa( 'start_album' ) : '0';

//	if ( wppa( 'is_upldr' ) ) $album = '0';	// probeersel upldr parent

	// owner/public?
	if ( $album == '-3' ) {
		$temp = $wpdb->get_results( "SELECT `id` FROM `" . WPPA_ALBUMS . "` WHERE `owner` = '" . wppa_get_user() . "' OR `owner` = '--- public ---' ORDER BY `id`", ARRAY_A );
		$album = '';
		if ( $temp ) {
			foreach( $temp as $t ) {
				$album .= '.' . $t['id'];
			}
			$album = ltrim( $album, '.' );
		}
		$album = wppa_compress_enum( $album );
	}

	if ( $id ) {
		$photo_name = wppa_get_photo_name( $id );
	}
	else $photo_name = '';

	$photo_name_js = esc_js( $photo_name );
	$photo_name = esc_attr( $photo_name );

	if ( $id ) {
		$photo_desc = esc_attr( wppa_get_photo_desc( $id ) );
	}
	else $photo_desc = '';

	$title = __( $photo_name , 'wp-photo-album-plus');

	$result['onclick'] = '';	// Init
	switch ( $type ) {
		case 'none':		// No link at all
			return false;
			break;
		case 'file':		// The plain file
			if ( $is_video ) {
				$siz = array( wppa_get_videox( $id ), wppa_get_videoy( $id ) );
				$result['url'] = wppa_get_photo_url( $id, '', $siz['0'], $siz['1'] );
				reset( $is_video );
				$result['url'] = str_replace( 'xxx', current( $is_video ), $result['url'] );
			}
			else {
				$siz = array( wppa_get_photox( $id ), wppa_get_photoy( $id ) );
				$result['url'] = wppa_get_photo_url( $id, '', $siz['0'], $siz['1'] );
			}
			$result['title'] = $title;
			$result['is_url'] = true;
			$result['is_lightbox'] = false;
			return $result;
			break;
		case 'lightbox':
		case 'lightboxsingle':
			if ( $is_video ) {
				$siz = array( wppa_get_videox( $id ), wppa_get_videoy( $id ) );
				$result['url'] = wppa_get_photo_url( $id, '', $siz['0'], $siz['1'] );
				//$result['url'] = str_replace( 'xxx', $is_video['0'], $result['url'] );
			}
			else {
				if ( wppa_switch( 'lb_hres' ) ) {
					$result['url'] = wppa_get_hires_url( $id );
				}
				else {
					$siz = array( wppa_get_photox( $id ), wppa_get_photoy( $id ) );
					$result['url'] = wppa_get_photo_url( $id, '', $siz['0'], $siz['1'] );
				}
			}
			$result['title'] = $title;
			$result['is_url'] = false;
			$result['is_lightbox'] = true;
			$result['url'] = wppa_fix_poster_ext( $result['url'], $id );
			return $result;
		case 'widget':		// Defined at widget activation
			$result['url'] = wppa( 'in_widget_linkurl' );
			$result['title'] = esc_attr( wppa( 'in_widget_linktitle' ) );
			$result['is_url'] = true;
			$result['is_lightbox'] = false;
			return $result;
			break;
		case 'album':		// The albums thumbnails
		case 'content':		// For album widget
			switch ( $page ) {
				case '-1':
					return false;
					break;
				case '0':
					if ( $noalb ) {
						$result['url'] = wppa_encrypt_url( wppa_get_permalink() . 'wppa-album=0&amp;wppa-cover=0' );
						$result['title'] = ''; // $album_name;
						$result['is_url'] = true;
						$result['is_lightbox'] = false;
					}
					else {
						$result['url'] = wppa_encrypt_url( wppa_get_permalink() . 'wppa-album=' . $album . '&amp;wppa-cover=0' );
						$result['title'] = $album_name;
						$result['is_url'] = true;
						$result['is_lightbox'] = false;
					}
					break;
				default:
					if ( $noalb ) {
						$result['url'] = wppa_encrypt_url( wppa_get_permalink( $page ) . 'wppa-album=0&amp;wppa-cover=0' );
						$result['title'] = ''; //$album_name;//'a++';
						$result['is_url'] = true;
						$result['is_lightbox'] = false;
					}
					else {
						$result['url'] = wppa_encrypt_url( wppa_get_permalink( $page ) . 'wppa-album=' . $album . '&amp;wppa-cover=0' );
						$result['title'] = $album_name;//'a++';
						$result['is_url'] = true;
						$result['is_lightbox'] = false;
					}
					break;
			}
			break;
		case 'thumbalbum':
			$album = $thumb['album'];
			$album_name = wppa_get_album_name( $album );
			switch ( $page ) {
				case '-1':
					return false;
					break;
				case '0':
					$result['url'] = wppa_encrypt_url( wppa_get_permalink() . 'wppa-album=' . $album . '&amp;wppa-cover=0' );
					$result['title'] = $album_name;
					$result['is_url'] = true;
					$result['is_lightbox'] = false;
					break;
				default:
					$result['url'] = wppa_encrypt_url( wppa_get_permalink( $page ) . 'wppa-album=' . $album . '&amp;wppa-cover=0' );
					$result['title'] = $album_name;//'a++';
					$result['is_url'] = true;
					$result['is_lightbox'] = false;
					break;
			}
			break;
		case 'photo':		// This means: The fullsize photo in a slideshow
		case 'slphoto':		// This means: The single photo in the style of a slideshow
			if ( $type == 'slphoto' ) {
				$si = '&amp;wppa-single=1';
			}
			else {
				$si = '';
			}
			switch ( $page ) {
				case '-1':
					return false;
					break;
				case '0':
					if ( $noalb ) {
						$result['url'] = wppa_encrypt_url( wppa_get_permalink() . 'wppa-album=0&amp;wppa-photo=' . $id . $si );
						$result['title'] = $title;
						$result['is_url'] = true;
						$result['is_lightbox'] = false;
					}
					else {
						$result['url'] = wppa_encrypt_url( wppa_get_permalink() . 'wppa-album=' . $album . '&amp;wppa-photo=' . $id . $si );
						$result['title'] = $title;
						$result['is_url'] = true;
						$result['is_lightbox'] = false;
					}
					break;
				default:
					if ( $noalb ) {
						$result['url'] = wppa_encrypt_url( wppa_get_permalink( $page ) . 'wppa-album=0&amp;wppa-photo=' . $id . $si );
						$result['title'] = $title;
						$result['is_url'] = true;
						$result['is_lightbox'] = false;
					}
					else {
						$result['url'] = wppa_encrypt_url( wppa_get_permalink( $page ) . 'wppa-album=' . $album . '&amp;wppa-photo=' . $id . $si );
						$result['title'] = $title;
						$result['is_url'] = true;
						$result['is_lightbox'] = false;
					}
					break;
			}
			break;
		case 'single':
			switch ( $page ) {
				case '-1':
					return false;
					break;
				case '0':
					$result['url'] = wppa_encrypt_url( wppa_get_permalink() . 'wppa-photo=' . $id );
					$result['title'] = $title;
					$result['is_url'] = true;
					$result['is_lightbox'] = false;
					break;
				default:
					$result['url'] = wppa_encrypt_url( wppa_get_permalink( $page ) . 'wppa-photo=' . $id );
					$result['title'] = $title;
					$result['is_url'] = true;
					$result['is_lightbox'] = false;
					break;
			}
			break;
		case 'same':
			$result['url'] = $lnk;
			$result['title'] = $tit;
			$result['is_url'] = true;
			$result['is_lightbox'] = false;
			$result['onclick'] = $onc;
			return $result;
			break;
		case 'fullpopup':
			if ( $is_video ) { 		// A video can not be printed or downloaded
				$result['url'] = esc_attr( 'alert( "' . esc_js( __( 'A video can not be printed or downloaded' , 'wp-photo-album-plus') ) . '" )' );
			}
			else {
				$wid = wppa_get_photox( $id );
				$hig = wppa_get_photoy( $id );
				/*
				$imgsize = getimagesize( wppa_get_photo_path( $id ) );
				if ( $imgsize ) {
					$wid = $imgsize['0'];
					$hig = $imgsize['1'];
				}
				else {
					$wid = '0';
					$hig = '0';
				}
				*/
				$url = wppa_fix_poster_ext( wppa_get_photo_url( $id, '', $wid, $hig ), $id );

				$result['url'] = esc_attr( 'wppaFullPopUp( ' . wppa( 'mocc' ) . ', ' . $id . ', "' . $url . '", ' . $wid . ', ' . $hig . ' )' );
			}
			$result['title'] = $title;
			$result['is_url'] = false;
			$result['is_lightbox'] = false;
			return $result;
			break;
		case 'custom':
			if ( $wich == 'potdwidget' ) {
				$result['url'] = wppa_opt( 'widget_linkurl' );
				$result['title'] = wppa_opt( 'widget_linktitle' );
				$result['is_url'] = true;
				$result['is_lightbox'] = false;
				return $result;
			}
			break;
		case 'slide':	// for album widget
			$result['url'] = wppa_encrypt_url( wppa_get_permalink( wppa_opt( 'album_widget_linkpage' ) ) . 'wppa-album=' . $album . '&amp;slide' );
			$result['title'] = '';
			$result['is_url'] = true;
			$result['is_lightbox'] = false;
			break;
		case 'autopage':
			if ( ! wppa_switch( 'auto_page' ) ) {
				wppa_dbg_msg( 'Auto page has been switched off, but there are still links to it (' . $wich . ')', 'red', 'force' );
				$result['url'] = '';
			}
			else {
				$result['url'] = wppa_get_permalink( wppa_get_the_auto_page( $id ) );
			}
			$result['title'] = '';
			$result['is_url'] = true;
			$result['is_lightbox'] = false;
			break;
		case 'plainpage':
			$result['url'] = get_permalink( $page );
			$result['title'] = $wpdb->get_var( $wpdb->prepare( "SELECT `post_title` FROM `" . $wpdb->prefix . "posts` WHERE `ID` = %s", $page ) );
			$result['is_url'] = true;
			$result['is_lightbox'] = false;
			return $result;
			break;
		default:
			wppa_dbg_msg( 'Error, wrong type: ' . $type . ' in wppa_get_imglink_a', 'red' );
			return false;
			break;
	}

	if ( $type != 'thumbalbum' ) {

		if ( wppa( 'calendar' ) ) {
			$result['url'] .= '&amp;wppa-calendar=' . wppa( 'calendar' ) . '&amp;wppa-caldate=' . wppa( 'caldate' );
		}

		if ( wppa( 'supersearch' ) ) {
			$result['url'] .= '&amp;wppa-supersearch=' . urlencode( wppa( 'supersearch' ) );
		}

		if ( wppa( 'src' ) && ! wppa( 'is_related' ) && ! wppa_in_widget() ) {
			$result['url'] .= '&amp;wppa-searchstring=' . urlencode( wppa( 'searchstring' ) );
		}

		if ( $wich == 'topten' ) {
			$result['url'] .= '&amp;wppa-topten=' . wppa_opt( 'topten_count' );
		}
		elseif ( wppa( 'is_topten' ) ) {
			$result['url'] .= '&amp;wppa-topten=' . wppa( 'topten_count' );
		}

		if ( $wich == 'lasten' ) {
			$result['url'] .= '&amp;wppa-lasten=' . wppa_opt( 'lasten_count' );
		}
		elseif ( wppa( 'is_lasten' ) ) {
			$result['url'] .= '&amp;wppa-lasten=' . wppa( 'lasten_count' );
		}

		if ( $wich == 'comten' ) {
			$result['url'] .= '&amp;wppa-comten=' . wppa_opt( 'comten_count' );
		}
		elseif ( wppa( 'is_comten' ) ) {
			$result['url'] .= '&amp;wppa-comten=' . wppa( 'comten_count' );
		}

		if ( $wich == 'featen' ) {
			$result['url'] .= '&amp;wppa-featen=' . wppa_opt( 'featen_count' );
		}
		elseif ( wppa( 'is_featen' ) ) {
			$result['url'] .= '&amp;wppa-featen=' . wppa( 'featen_count' );
		}

		if ( wppa( 'is_related' ) ) {
			$result['url'] .= '&amp;wppa-rel=' . wppa( 'is_related' ) . '&amp;wppa-relcount=' . wppa( 'related_count' );
		}
		elseif ( wppa( 'is_tag' ) ) {
			$result['url']  .= '&amp;wppa-tag=' . wppa( 'is_tag' );
		}

		if ( wppa( 'is_upldr' ) ) {
			$result['url'] .= '&amp;wppa-upldr=' . wppa( 'is_upldr' );
		}

		if ( wppa( 'is_inverse' ) ) {
			$result['url'] .= '&amp;wppa-inv=1';
		}

		if ( wppa( 'medals_only' ) ) {
			$result['url'] .= '&amp;wppa-medals-only=1';
		}
	}

	if ( $page != '0' ) {	// on a different page
		$occur = '1';
		$w = '';
	}
	else {				// on the same page, post or widget
		$occur = wppa_in_widget() ? wppa( 'widget_occur' ) : wppa( 'occur' );
		$w = wppa_in_widget() ? 'w' : '';
	}
	$result['url'] .= '&amp;wppa-' . $w . 'occur=' . $occur;
	$result['url'] = wppa_encrypt_url( $result['url'] );
	$result['url'] = wppa_convert_to_pretty( $result['url'] );

	if ( $result['title'] == '' ) $result['title'] = $tit;	// If still nothing, try arg

	return $result;
}

// Remove wppa- from query string arguments
function wppa_trim_wppa_( $link ) {
static $trimmable;

	if ( empty( $trimmable ) ) {
		$trimmable = array(	'album',
							'photo',
							'slide',
							'cover',
							'occur',
							'woccur',
							'searchstring',
							'topten',
							'lasten',
							'comten',
							'featen',
							'single',
							'photos-only',
							'albums-only',
							'medals-only',
							'debug',
							'relcount',
							'upldr',
							'owner',
							'rootsearch',
							'hilite'
							);
	}
	$result = $link;

	// In wppa_redirect() is $wppa_opt not yet initialized, do not use wppa_switch() to avoid error
	// if ( wppa_switch( 'use_short_qargs' ) ) {
	if ( get_option( 'wppa_use_short_qargs' ) == 'yes' ) {
		foreach ( $trimmable as $item ) {
			$result = str_replace( 'wppa-'.$item, $item, $result );
		}
	}

	return $result;
}

// Get the link from slideshow to thumbnail view
function wppa_get_ss_to_tn_link( $page = '0', $id = '0' ) {
global $thumbs;
global $thumbs_ids;

	// Search ?
	if ( wppa( 'src' ) && wppa( 'mocc' ) == '1' && ! wppa( 'is_related' ) ) {
		$thumbhref = wppa_get_permalink( $page ).'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-searchstring='.stripslashes( wppa( 'searchstring' ) );
	}
	// Uploader ?
	elseif ( wppa( 'is_upldr' ) ) {
		if ( wppa( 'start_album' ) ) {
			$thumbhref = wppa_get_permalink( $page ).'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-upldr='.wppa( 'is_upldr' ).'&amp;wppa-album='.wppa( 'start_album' );
		}
		else {
			$thumbhref = wppa_get_permalink( $page ).'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-upldr='.wppa( 'is_upldr' );
		}
	}
	// Topten ?
	elseif ( wppa( 'is_topten' ) ) {
		$thumbhref = wppa_get_permalink( $page ).'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-topten='.wppa( 'topten_count' ).'&amp;wppa-album='.wppa( 'start_album' );
	}
	// Lasten ?
	elseif ( wppa( 'is_lasten' ) ) {
		$thumbhref = wppa_get_permalink( $page ).'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-lasten='.wppa( 'lasten_count' ).'&amp;wppa-album='.wppa( 'start_album' );
	}
	// Comten ?
	elseif ( wppa( 'is_comten' ) ) {
		$thumbhref = wppa_get_permalink( $page ).'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-comten='.wppa( 'comten_count' ).'&amp;wppa-album='.wppa( 'start_album' );
	}
	// Featen ?
	elseif ( wppa( 'is_featen' ) ) {
		$thumbhref = wppa_get_permalink( $page ).'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-featen='.wppa( 'featen_count' ).'&amp;wppa-album='.wppa( 'start_album' );
	}
	// Related ?
//	elseif ( wppa( 'is_related' ) ) {
//		$thumbhref = '';
//	}
	// Tag ?
	elseif ( wppa( 'is_tag' ) ) {
		$thumbhref = wppa_get_permalink( $page ).'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-tag='.wppa( 'is_tag' ).'&amp;wppa-album='.wppa( 'start_album' );
	}
	// Cat ?
	elseif ( wppa( 'is_cat' ) ) {
		$thumbhref = wppa_get_permalink( $page ).'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-cat='.wppa( 'is_cat' ).'&amp;wppa-album='.wppa( 'start_album' );
	}
	// Last ?
//	elseif ( wppa( 'last_albums' ) ) {
//		$thumbhref = wppa_get_permalink( $page ).'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-album='.wppa( 'start_album' );
//	}
	// Default ?
	else {
		$thumbhref = wppa_get_permalink( $page ).'wppa-cover=0&amp;wppa-occur='.wppa( 'occur' ).'&amp;wppa-album='.wppa( 'start_album' );
	}

	// $id is the id. See to what page we have to go
	$page = '1';
	$p = wppa_opt( 'thumb_page_size' );
	if ( $p ) {
		$i = '0';
		foreach ( $thumbs_ids as $ti ) {	// $thumbs_ids is setup in function wppa_prepare_slideshow_pagination()
			if ( $id == $ti ) {
				$page = floor( $i / $p ) + '1';
			}
			$i++;
		}
		if ( $page > '1' ) {
			$thumbhref .= '&amp;wppa-page='.$page;
		}
	}

	// Make sure the clicked photos thumb is highligted
	$thumbhref .='&amp;wppa-hilite='.$id;

	$thumbhref = wppa_convert_to_pretty( wppa_trim_wppa_( wppa_encrypt_url( $thumbhref ) ) );

	return $thumbhref;
}