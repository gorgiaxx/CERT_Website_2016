<?php
/* wppa-session.php
* Package: wp-photo-album-plus
*
* Contains all session routines
* Version 6.4.09
*
* Firefox modifies data in the superglobal $_SESSION.
* See https://bugzilla.mozilla.org/show_bug.cgi?id=991019
* The use of $_SESSION data is therefor no longer reliable
* This file contains routines to obtain the same functionality, but more secure.
* In the application use the global $wppa_session in stead of $_SESSION['wppa_session']
*
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

// Generate a unique session id
function wppa_get_session_id() {
global $wppa_api_version;
	$id = md5( $_SERVER["REMOTE_ADDR"] . ( isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : '' ) . $wppa_api_version );
	return $id;
}

// Start a session or retrieve the sessions data. To be called at init.
function wppa_session_start() {
global $wpdb;
global $wppa_session;

	// If the session table does not yet exist on activation
	if ( ! wppa_table_exists( WPPA_SESSION ) ) {
		$wppa_session['id'] = '0';
		return false;
	}

	// Cleanup first
	$lifetime = 3600;			// Sessions expire after one hour
	$savetime = 3600;			// Save session data for 1 hour
	$expire = time() - $lifetime;
	$wpdb->query( $wpdb->prepare( "UPDATE `" . WPPA_SESSION . "` SET `status` = 'expired' WHERE `timestamp` < %s", $expire ) );
	$purge = time() - $savetime;
	$wpdb->query( $wpdb->prepare( "DELETE FROM `" . WPPA_SESSION ."` WHERE `timestamp` < %s", $purge ) );

	// Is session already started?
	$session = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `".WPPA_SESSION."` WHERE `session` = %s AND `status` = 'valid' LIMIT 1", wppa_get_session_id() ), ARRAY_A );
	$data = $session ? $session['data'] : false;

	// Not started yet, setup session
	if ( $data === false ) {
		$iret = false;
		$tries = '0';
		while ( ! $iret && $tries < '10' ) {
			$iret = wppa_create_session_entry( array() );
			if ( ! $iret ) {
				sleep(1);
				$tries++;
			}
		}
		if ( $tries > '3' && $iret ) {
			wppa_log( 'Debug', 'It took '.$tries.' retries to start session '.$iret );
		}
		if ( ! $iret ) {
			wppa_log( 'Error', 'Unable to create session.' );
			return false;
		}
		$wppa_session = array();
		$wppa_session['page'] = '0';
		$wppa_session['ajax'] = '0';
		$wppa_session['id'] = $wpdb->get_var( $wpdb->prepare( "SELECT `id` FROM `".WPPA_SESSION."` WHERE `session` = %s LIMIT 1", wppa_get_session_id() ) );
		$wppa_session['user'] = wppa_get_user();
	}

	// Session exists, Update counter
	else {
		$wpdb->query( $wpdb->prepare( "UPDATE `".WPPA_SESSION."` SET `count` = %s WHERE `id` = %s", $session['count'] + '1', $session['id'] ) );
		$data_arr = unserialize( $data );
		if ( is_array( $data_arr ) ) {
			$wppa_session = $data_arr;
		}
		else {
			$wppa_session = array();
		}
	}

	// Get info for root and sub search
	if ( isset( $_REQUEST['wppa-search-submit'] ) ) {
		$wppa_session['rootbox'] = wppa_get_get( 'rootsearch' ) || wppa_get_post( 'rootsearch' );
		$wppa_session['subbox']  = wppa_get_get( 'subsearch' )  || wppa_get_post( 'subsearch' );
		if ( $wppa_session['subbox'] ) {
			if ( isset ( $wppa_session['use_searchstring'] ) ) {
				$t = explode( ',', $wppa_session['use_searchstring'] );
				foreach( array_keys( $t ) as $idx ) {
					$t[$idx] .= ' '.wppa_test_for_search( 'at_session_start' );
					$t[$idx] = trim( $t[$idx] );
					$v = explode( ' ', $t[$idx] );
					$t[$idx] = implode( ' ', array_unique( $v ) );
				}
				$wppa_session['use_searchstring'] = ' '.implode( ',', array_unique( $t ) );
			}
			else {
				$wppa_session['use_searchstring'] = wppa_test_for_search( 'at_session_start' );
			}
		}
		else {
			$wppa_session['use_searchstring'] = wppa_test_for_search( 'at_session_start' );
		}
		if ( isset ( $wppa_session['use_searchstring'] ) ) {
			$wppa_session['use_searchstring'] = trim( $wppa_session['use_searchstring'], ' ,' );
			$wppa_session['display_searchstring'] = str_replace ( ',', ' &#8746 ', str_replace ( ' ', ' &#8745 ', $wppa_session['use_searchstring'] ) );
		}
	}

	// Add missing defaults
	$defaults = array(
						'has_searchbox' 		=> false,
						'rootbox' 				=> false,
						'search_root' 			=> '',
						'subbox' 				=> false,
						'use_searchstring' 		=> '',
						'display_searchstring' 	=> '',
						'supersearch' 			=> '',
						'superview' 			=> 'thumbs',
						'superalbum' 			=> '0',
						'page'					=> '0',
						'ajax'					=> '0',
						'user' 					=> '',
						'id' 					=> $wpdb->get_var( $wpdb->prepare( "SELECT `id` FROM `".WPPA_SESSION."` WHERE `session` = %s LIMIT 1", wppa_get_session_id() ) ),
						'uris' 					=> array(),

						);

	$wppa_session = wp_parse_args( $wppa_session, $defaults );
	ksort( $wppa_session );

	$wppa_session['page']++;
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$wppa_session['uris'][] = date_i18n("g:i") . ' ' . $_SERVER['REQUEST_URI'] . ( isset( $_SERVER['HTTP_REFERER'] ) ? ', Referrer: ' . $_SERVER['HTTP_REFERER'] : '' );
	}

	wppa_save_session();

	return true;
}

// Saves the session data. To be called at shutdown
function wppa_session_end() {
global $wppa_session;

	// May have logged in now
	$wppa_session['user'] = wppa_get_user();
	wppa_save_session();
}

// Save the session data
function wppa_save_session() {
global $wpdb;
global $wppa_session;
static $last_query;

	// If no id can be found, give up
	if ( ! wppa_get_session_id() ) return false;

	// Compose the query
	$query = $wpdb->prepare( "UPDATE `".WPPA_SESSION."` SET `data` = %s WHERE `session` = %s", serialize( $wppa_session ), wppa_get_session_id() );

	// Only update if data differs from previous update
	if ( $query != $last_query ) {

		// Do the query
		$iret = $wpdb->query( $query );

		// Remember last successfull query
		if ( $iret ) {
			$last_query = $query;
			return true;
		}

		// No luck, maybe attemt to save a session that never started. Start new session
		wppa_log('Dbg', 'Attempt to save a session that was not started. ip='.$_SERVER['REMOTE_ADDR'], true);
		$wppa_session = false;
		$last_query = false;
		wppa_session_start();

		return false;
	}

	// No error return
	return true;
}

// Extends session for admin maintenance procedures, to report the right totals
function wppa_extend_session() {
global $wpdb;

	$sessionid = wppa_get_session_id();
	$wpdb->query( $wpdb->prepare( "UPDATE `" . WPPA_SESSION . "` SET `timestamp` = %d WHERE `session` = %s", time(), $sessionid ) );
}