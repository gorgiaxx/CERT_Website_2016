<?php
/* wppa-encrypt.php
* Package: wp-photo-album-plus
*
* Contains all ecryption/decryption logic
* Version 6.4.00
*
*/

// Find a unique photo crypt
function wppa_get_unique_photo_crypt() {

	$cr = wppa_get_unique_crypt( WPPA_PHOTOS );

	return $cr;
}

// Find a unique album crypt
function wppa_get_unique_album_crypt() {

	$cr = wppa_get_unique_crypt( WPPA_ALBUMS );
	while ( $cr == get_option( 'wppa_album_crypt_0', '' ) ||
			$cr == get_option( 'wppa_album_crypt_1', '' ) ||
			$cr == get_option( 'wppa_album_crypt_2', '' ) ||
			$cr == get_option( 'wppa_album_crypt_3', '' )
			) {
				$cr = wppa_get_unique_crypt( WPPA_ALBUMS );
			}

	return $cr;
}

// Find a unique crypt
function wppa_get_unique_crypt( $table ) {
global $wpdb;

	$crypt 	= substr( md5( microtime() ), 0, 12 );
	$dup 	= $wpdb->get_var( $wpdb->prepare( "SELECT `id` FROM `" . $table . "` WHERE `crypt` = %s", $crypt ) );
	while ( $dup ) {
		sleep( 1 );
		$crypt 	= substr( md5( microtime() ), 0, 12 );
		$dup 	= $wpdb->get_var( $wpdb->prepare( "SELECT `id` FROM `" . $table . "` WHERE `crypt` = %s", $crypt ) );
	}
	return $crypt;
}

// Convert photo id to crypt
function wppa_encrypt_photo( $id ) {

	// Feature enabled?
	if ( ! wppa_switch( 'use_encrypted_links' ) ) {
		return $id;
	}

	// Yes
	if ( strlen( $id ) < 12 ) {
		$crypt = wppa_get_photo_item( $id, 'crypt' );
	}
	else {
		$crypt = $id; 	// Already encrypted
	}

	return $crypt;
}

// Convert album id to crypt
function wppa_encrypt_album( $album ) {

	// Feature enabled?
	if ( ! wppa_switch( 'use_encrypted_links' ) ) {
		return $album;
	}

	// Yes. Decompose possible album enumeration
	$album_ids 		= explode( '.', $album );
	$album_crypts 	= array();
	$i 				= 0;

	// Process all tokens
	while ( $i < count( $album_ids ) ) {
		$id = $album_ids[$i];
		switch ( $id ) {
			case '-3':
				$crypt = get_option( 'wppa_album_crypt_3', false );
				break;
			case '-2':
				$crypt = get_option( 'wppa_album_crypt_2', false );
				break;
			case '-1':
				$crypt = get_option( 'wppa_album_crypt_1', false );
				break;
			case '0':
				$crypt = get_option( 'wppa_album_crypt_0', false );
				break;
			case '':
				$crypt = '';
				break;
			default:
				if ( strlen( $id ) < 12 ) {
					$crypt = wppa_get_album_item( $id, 'crypt' );
				}
				else {
					$crypt = $id; 	// Already encrypted
				}
		}
		$album_crypts[$i] = $crypt;
		$i++;
	}

	// Compose result
	$result = implode( '.', $album_crypts );

	return $result;
}

// Convert photo crypt to id
function wppa_decrypt_photo( $photo ) {
global $wpdb;

	// Feature enabled?
	if ( ! wppa_switch( 'use_encrypted_links' ) ) {
		return $photo;
	}

	// Already decrypted?
	if ( strlen( $photo ) < 12 ) {
//		wppa_log( 'War', 'Decrypted photo id found: ' . $photo . ' url: '. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true );
		if ( wppa_switch( 'use_encrypted_links' ) ) {
			wppa_dbg_msg( __( 'Invalid photo identifier:', 'wp-photo-album-plus') . ' ' . $photo, 'red', 'force' );
			return false;
		}
		return $photo;
	}

	// Yes
	$id = $wpdb->get_var( $wpdb->prepare( "SELECT `id` FROM `" . WPPA_PHOTOS . "` WHERE `crypt` = %s", $photo ) );
	if ( ! $id ) {
		wppa_dbg_msg( 'Invalid photo identifier: ' . $photo, 'red', 'force' );
	}

	return $id;
}

// Convert album crypt to id
function wppa_decrypt_album( $album ) {
global $wpdb;

	$mes_given = false;

	// Feature enabled?
	if ( ! wppa_switch( 'use_encrypted_links' ) ) {
		return $album;
	}

	// Yes. Decompose possible album enumeration
	$album_crypts	= explode( '.', $album );
	$album_ids 		= array();
	$i 				= 0;

	// Process all tokens
	while ( $i < count( $album_crypts ) ) {
		$crypt = $album_crypts[$i];
		if ( $crypt === '' ) {
			$id = '';
		}
		elseif ( $crypt == get_option( 'wppa_album_crypt_0', false ) ) {
			$id = '0';
		}
		elseif ( $crypt == get_option( 'wppa_album_crypt_1', false ) ) {
			$id = '-1';
		}
		elseif ( $crypt == get_option( 'wppa_album_crypt_2', false ) ) {
			$id = '-2';
		}
		elseif ( $crypt == get_option( 'wppa_album_crypt_2', false ) ) {
			$id = '-3';
		}
		else {

			// Already decrypted?
			if ( strlen( $crypt ) < 12 ) {
				if ( ! $mes_given ) {
//					wppa_log( 'War', 'Decrypted album id found: ' . $crypt . ' in albumspec: ' . $album . ' url: '. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true );
					$mes_given = true;
				}
				$id = $crypt;
				if ( wppa_switch( 'use_encrypted_links' ) ) {
					wppa_dbg_msg( __('Invalid album identifier:', 'wp-photo-album-plus') . ' ' . $id, 'red', 'force' );
					return '-9';
				}
			}
			else {
				$id = $wpdb->get_var( $wpdb->prepare( "SELECT `id` FROM `" . WPPA_ALBUMS . "` WHERE `crypt` = %s", $crypt ) );
				if ( ! $id ) {
					wppa_log( 'Err', 'Invalid album identifier: ' . $album, true );
					$id = '-9';
				}
			}
		}
		$album_ids[$i] = $id;
		$i++;
	}

	// Compose result
	$result = implode( '.', $album_ids );

	return $result;
}

// Encrypt a full url
function wppa_encrypt_url( $url ) {

	// Feature enabled?
	if ( ! wppa_switch( 'use_encrypted_links' ) ) {
		return $url;
	}

	// Querystring present?
	if ( strpos( $url, '?' ) === false ) {
		return $url;
	}

	// Has it &amp; 's ?
	if ( strpos( $url, '&amp;' ) === false ) {
		$hasamp = false;
	}
	else {
		$hasamp = true;
	}

	// Disassemble url
	$temp = explode( '?', $url );

	// Has it a querystring?
	if ( count( $temp ) == '1' ) {
		return $url;
	}

	// Disassemble querystring
	$qarray = explode( '&', str_replace( '&amp;', '&', $temp['1'] ) );

	// Search and replace album and photo ids by crypts
	$i = 0;
	while ( $i < count( $qarray ) ) {
		$item = $qarray[$i];
		$t = explode( '=', $item );
		if ( isset( $t['1'] ) ) {
			switch ( $t['0'] ) {
				case 'wppa-album':
				case 'album':
					if ( ! $t['1'] ) $t['1'] = '0';
					$t['1'] = wppa_encrypt_album( $t['1'] );
					if ( ! $t['1'] ) {
						wppa_dbg_msg( 'Error: Illegal album specification: ' . $item . ' (wppa_encrypt_url)', 'red', 'force' );
						exit;
					}
					break;
				case 'wppa-photo':
				case 'photo':
					$t['1'] = wppa_encrypt_photo( $t['1'] );
					if ( ! $t['1'] ) {
						wppa_dbg_msg( 'Error: Illegal photo specification: ' . $item . ' (wppa_encrypt_url)', 'red', 'force' );
						exit;
					}
					break;
			}
		}
		$item = implode( '=', $t );
		$qarray[$i] = $item;
		$i++;
	}

	// Re-assemble url
	$temp['1'] = implode( '&', $qarray );
	$newurl = implode( '?', $temp );
	if ( $hasamp ) {
		$newurl = str_replace( '&', '&amp;', $newurl );
	}

	return $newurl;
}

