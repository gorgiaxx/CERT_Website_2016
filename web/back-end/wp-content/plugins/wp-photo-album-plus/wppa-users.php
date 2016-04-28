<?php
/* wppa-users.php
* Package: wp-photo-album-plus
*
* Contains user and capabilities related routines
* Version 6.4.08
*
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

// Get number of users
function wppa_get_user_count() {
global $wpdb;
static $usercount;

	if ( empty( $usercount ) ) {
		$usercount = $wpdb->get_var( "SELECT COUNT(*) FROM `" . $wpdb->users . "`" );
	}

	return $usercount;
}

// Get all users
function wppa_get_users() {
global $wpdb;
static $users;

	if ( empty($users) ) {
		if ( wppa_get_user_count() > wppa_opt( 'max_users' ) ) {
			$users = array();
		}
		else {
			$users = $wpdb->get_results( 	"SELECT * FROM `".$wpdb->users."` " .
											"ORDER BY `display_name`", ARRAY_A );
		}
	}
	return $users;
}

// Get user
// If logged in, return userdata as specified in $type
// If logged out, return IP
function wppa_get_user( $type = 'login' ) {

	if ( is_user_logged_in() ) {
		$current_user = wp_get_current_user();
		switch ( $type ) {
			case 'login':
				return $current_user->user_login;
				break;
			case 'display':
				return $current_user->display_name;
				break;
			case 'id':
				return $current_user->ID;
				break;
			case 'firstlast':
				return $current_user->user_firstname.' '.$current_user->user_lastname;
				break;
			default:
				wppa_dbg_msg( 'Un-implemented type: '.$type.' in wppa_get_user()', 'red', 'force' );
				return '';
		}
	}
	else {
		return $_SERVER['REMOTE_ADDR'];
	}
}

// Test if a given user has a given role.
// @1: str role
// @2: int user id, default current user
// returns bool
function wppa_user_is( $role, $user_id = null ) {

 	if ( ! is_user_logged_in() ) return false;

	if ( $role == 'administrator' && wppa_is_user_superuser() ) {
		return true;
	}

	// WP itsself mixes roles and capabilities ( on multisites administrator is a cap of the superadmin )
	if ( $user_id ) {
		return user_can( $user_id, $role );
	}
	else {
		return current_user_can( $role );
	}
}

// Test if current user has extended access
// returns bool
function wppa_extended_access() {

	if ( wppa_user_is( 'administrator' ) ) {
		return true;
	}
	if ( ! wppa_switch( 'owner_only' ) ) {
		return true;
	}
	return false;
}

// Test if current user is allowed to craete albums
// returns bool
function wppa_can_create_album() {
global $wpdb;
global $wp_roles;

	// Test for logged out users
	if ( ! is_user_logged_in() ) {

		// Login required ?
		if ( wppa_switch( 'user_create_login' ) ) {
			return false;
		}

		// Login not required and logged out
		else {
			$rmax = get_option( 'wppa_loggedout_album_limit_count', '0' );

			// If logged out max set, check if limit reached
			if ( $rmax ) {
				$albs = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_ALBUMS."` WHERE `owner` = %s", wppa_get_user() ) );
				if ( $albs >= $rmax ) {
					return false;	// Limit reached
				}
				else {
					return true; 	// Limit not yet reached
				}
			}

			// No logged out limit set
			else {
				return true;
			}
		}
	}

	// Admin can do everything
	if ( wppa_user_is( 'administrator' ) ) {
		return true;
	}

	// A blacklisted user can not create albums
	if ( wppa_is_user_blacklisted() ) {
		return false;
	}

	// Check for global max albums per user setting
	$albs = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_ALBUMS."` WHERE `owner` = %s", wppa_get_user() ) );
	$gmax = wppa_opt( 'max_albums' );
	if ( $gmax && $albs >= $gmax ) {
		return false;
	}

	// Check for role dependant max albums per user setting
	$user 	= wp_get_current_user();
	$roles 	= $wp_roles->roles;
	foreach ( array_keys( $roles ) as $role ) {

		// Find firste role the user has
		if ( wppa_user_is( $role ) ) {
			$rmax = get_option( 'wppa_'.$role.'_album_limit_count', '0' );
			if ( ! $rmax || $albs < $rmax ) {
				return true;
			}
			else {
				return false;
			}
		}
	}

	// If a user has no role, deny creation
	return false;
}

// Test if current user is allowed to craete top level albums
// returns bool
function wppa_can_create_top_album() {

	if ( wppa_user_is( 'administrator' ) ) {
		return true;
	}
	if ( ! wppa_can_create_album() ) {
		return false;
	}
	if ( wppa_switch( 'grant_an_album' ) &&
		'0' != wppa_opt( 'grant_parent' ) ) {
			return false;
		}

	return true;
}

// Test if a user is on the blacklist
// @1: user id, default current user
// returns bool
function wppa_is_user_blacklisted( $user = -1 ) {
global $wpdb;
static $result = -1;

	$cur = ( -1 == $user );

	if ( $cur && -1 != $result ) {	// Already found out for current user
		return $result;
	}

	if ( $cur && ! is_user_logged_in() ) {	// An logged out user can not be on the blacklist
		$result = false;
		return false;
	}

	$blacklist = get_option( 'wppa_black_listed_users', array() );
	if ( empty( $blacklist ) ) {	// Anybody on the blacklist?
		$result = false;
		return false;
	}

	if ( $cur ) {
		$user = get_current_user_id();
	}

	if ( is_numeric( $user ) ) {
		$user = $wpdb->get_var( $wpdb->prepare(
			"SELECT `user_login` FROM `".$wpdb->users."` WHERE `ID` = %d", $user
		) );
		wppa_dbg_q( 'Q-iub' );
	}
	else {
		return false;
	}

	if ( $cur ) {
		$result = in_array( $user, $blacklist );	// Save current users result.
	}

	return in_array( $user, $blacklist );
}

function wppa_is_user_superuser() {

	$login = wppa_get_user();

	$superlist = get_option( 'wppa_super_users', array() );

	if ( in_array( $login, $superlist ) ) {
		return true;
	}
	return false;
}