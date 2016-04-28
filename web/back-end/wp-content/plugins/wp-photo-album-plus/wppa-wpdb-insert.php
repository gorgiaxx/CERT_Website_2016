<?php
/* wppa-wpdb-insert.php
* Package: wp-photo-album-plus
*
* Contains low-level wpdb routines that add new records
* Version 6.4.00
*
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

// Session
function wppa_create_session_entry( $args ) {
global $wpdb;

	$args = wp_parse_args( (array) $args, array (
		/*			'id' 				=> '0',	*/ //		session is now auto increment
					'session' 			=> wppa_get_session_id(),
					'timestamp' 		=> time(),
					'user'				=> wppa_get_user(),
					'ip'				=> $_SERVER['REMOTE_ADDR'],
					'status' 			=> 'valid',
					'data'				=> false,
					'count' 			=> '1'
					) );

	$query = $wpdb->prepare("INSERT INTO `" . WPPA_SESSION ."` 	(
																	`session`,
																	`timestamp`,
																	`user`,
																	`ip`,
																	`status`,
																	`data`,
																	`count`
																)
														VALUES ( %s, %s, %s, %s, %s, %s, %s )",

																$args['session'],
																$args['timestamp'],
																$args['user'],
																$args['ip'],
																$args['status'],
																$args['data'],
																$args['count']
														);
	$iret = @ $wpdb->query($query);

	return $iret;
}

// Index
function wppa_create_index_entry( $args ) {
global $wpdb;

	$args = wp_parse_args( (array) $args, array (
					'id'				=> '0',
					'slug' 				=> '',
					'albums' 			=> '',
					'photos' 			=> ''
					) );

	if ( ! wppa_is_id_free( WPPA_INDEX, $args['id'] ) ) $args['id'] = wppa_nextkey( WPPA_INDEX );

	$query = $wpdb->prepare("INSERT INTO `" . WPPA_INDEX . "` 	( 	`id`,
																	`slug`,
																	`albums`,
																	`photos`
																)
														VALUES ( %s, %s, %s, %s )",
																$args['id'],
																$args['slug'],
																$args['albums'],
																$args['photos']
														);
	$iret = $wpdb->query($query);

	if ( $iret ) return $args['id'];
	else return false;
}

// EXIF
function wppa_create_exif_entry( $args ) {
global $wpdb;

	$args = wp_parse_args( (array) $args, array (
					'id' 				=> '0',
					'photo' 			=> '0',
					'tag' 				=> '',
					'description' 		=> '',
					'status' 			=> ''
					) );

	if ( ! wppa_is_id_free( WPPA_EXIF, $args['id'] ) ) $args['id'] = wppa_nextkey( WPPA_EXIF );

	$query = $wpdb->prepare("INSERT INTO `" . WPPA_EXIF . "` 	( 	`id`,
																	`photo`,
																	`tag`,
																	`description`,
																	`status`
																)
														VALUES ( %s, %s, %s, %s, %s )",
																$args['id'],
																$args['photo'],
																$args['tag'],
																$args['description'],
																$args['status']
														);
	$iret = $wpdb->query($query);

	if ( $iret ) return $args['id'];
	else return false;
}

// IPTC
function wppa_create_iptc_entry( $args ) {
global $wpdb;

	$args = wp_parse_args( (array) $args, array (
					'id' 				=> '0',
					'photo' 			=> '0',
					'tag' 				=> '',
					'description' 		=> '',
					'status' 			=> ''
					) );

	if ( ! wppa_is_id_free( WPPA_IPTC, $args['id'] ) ) $args['id'] = wppa_nextkey( WPPA_IPTC );

	$query = $wpdb->prepare("INSERT INTO `" . WPPA_IPTC . "` 	( 	`id`,
																	`photo`,
																	`tag`,
																	`description`,
																	`status`
																)
														VALUES ( %s, %s, %s, %s, %s )",
																$args['id'],
																$args['photo'],
																$args['tag'],
																$args['description'],
																$args['status']
														);
	$iret = $wpdb->query($query);

	if ( $iret ) return $args['id'];
	else return false;
}

// Comments
function wppa_create_comments_entry( $args ) {
global $wpdb;

	$args = wp_parse_args( (array) $args, array (
					'id' 				=> '0',
					'timestamp' 		=> time(),
					'photo' 			=> '0',
					'user' 				=> wppa_get_user(),
					'ip'				=> $_SERVER['REMOTE_ADDR'],
					'email' 			=> '',
					'comment' 			=> '',
					'status' 			=> ''
					) );

	if ( ! wppa_is_id_free( WPPA_COMMENTS, $args['id'] ) ) $args['id'] = wppa_nextkey( WPPA_COMMENTS );

	$query = $wpdb->prepare("INSERT INTO `" . WPPA_COMMENTS . "` 	( 	`id`,
																		`timestamp`,
																		`photo`,
																		`user`,
																		`ip`,
																		`email`,
																		`comment`,
																		`status`
																	)
															VALUES ( %s, %s, %s, %s, %s, %s, %s, %s )",
																$args['id'],
																$args['timestamp'],
																$args['photo'],
																$args['user'],
																$args['ip'],
																$args['email'],
																$args['comment'],
																$args['status']
														);
	$iret = $wpdb->query($query);

	if ( $iret ) return $args['id'];
	else return false;
}

// Rating
function wppa_create_rating_entry( $args ) {
global $wpdb;

	$args = wp_parse_args( (array) $args, array (
					'id' 				=> '0',
					'timestamp' 		=> time(),
					'photo' 			=> '0',
					'value' 			=> '0',
					'user' 				=> '',
					'status' 			=> 'publish'
					) );

	if ( ! wppa_is_id_free( WPPA_RATING, $args['id'] ) ) $args['id'] = wppa_nextkey( WPPA_RATING );

	$query = $wpdb->prepare("INSERT INTO `" . WPPA_RATING . "` ( 	`id`,
																	`timestamp`,
																	`photo`,
																	`value`,
																	`user`,
																	`status`
																)
														VALUES ( %s, %s, %s, %s, %s, %s )",
																$args['id'],
																$args['timestamp'],
																$args['photo'],
																$args['value'],
																$args['user'],
																$args['status']
														);
	$iret = $wpdb->query($query);

	if ( $iret ) return $args['id'];
	else return false;
}

// Photo
function wppa_create_photo_entry( $args ) {
global $wpdb;

	$args = wp_parse_args( (array) $args, array (
					'id'				=> '0',
					'album' 			=> '0',
					'ext' 				=> 'jpg',
					'name'				=> '',
					'description' 		=> '',
					'p_order' 			=> '0',
					'mean_rating'		=> '',
					'linkurl' 			=> '',
					'linktitle' 		=> '',
					'linktarget' 		=> '_self',
					'owner'				=> wppa_get_user(),
					'timestamp'			=> time(),
					'status'			=> 'publish',
					'rating_count'		=> '0',
					'tags' 				=> '',
					'alt' 				=> '',
					'filename' 			=> '',
					'modified' 			=> time(),
					'location' 			=> '',
					'views' 			=> '0',
					'page_id' 			=> '0',
					'exifdtm' 			=> '',
					'videox' 			=> '0',
					'videoy' 			=> '0',
					'scheduledtm' 		=> $args['album'] ? $wpdb->get_var( $wpdb->prepare( "SELECT `scheduledtm` FROM `".WPPA_ALBUMS."` WHERE `id` = %s", $args['album'] ) ) : '',
					'custom'			=> '',
					'crypt' 			=> wppa_get_unique_photo_crypt()
					) );

	if ( $args['scheduledtm'] ) $args['status'] = 'scheduled';

	if ( ! wppa_is_id_free( WPPA_PHOTOS, $args['id'] ) ) $args['id'] = wppa_nextkey( WPPA_PHOTOS );

	$query = $wpdb->prepare("INSERT INTO `" . WPPA_PHOTOS . "` ( 	`id`,
																	`album`,
																	`ext`,
																	`name`,
																	`description`,
																	`p_order`,
																	`mean_rating`,
																	`linkurl`,
																	`linktitle`,
																	`linktarget`,
																	`owner`,
																	`timestamp`,
																	`status`,
																	`rating_count`,
																	`tags`,
																	`alt`,
																	`filename`,
																	`modified`,
																	`location`,
																	`views`,
																	`page_id`,
																	`exifdtm`,
																	`videox`,
																	`videoy`,
																	`scheduledtm`,
																	`custom`,
																	`crypt`
																)
														VALUES ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )",
																$args['id'],
																$args['album'],
																$args['ext'],
																trim( $args['name'] ),
																trim( $args['description'] ),
																$args['p_order'],
																$args['mean_rating'],
																$args['linkurl'],
																$args['linktitle'],
																$args['linktarget'],
																$args['owner'],
																$args['timestamp'],
																$args['status'],
																$args['rating_count'],
																$args['tags'],
																$args['alt'],
																wppa_sanitize_file_name( $args['filename'] ),	// Security fix
																$args['modified'],
																$args['location'],
																$args['views'],
																$args['page_id'],
																$args['exifdtm'],
																$args['videox'],
																$args['videoy'],
																$args['scheduledtm'],
																$args['custom'],
																$args['crypt']
														);
	$iret = $wpdb->query($query);

	if ( $iret ) return $args['id'];
	else return false;
}

// Album
function wppa_create_album_entry( $args ) {
global $wpdb;

	$args = wp_parse_args( (array) $args, array (
					'id' 				=> '0',
					'name' 				=> __( 'New Album', 'wp-photo-album-plus' ),
					'description' 		=> '',
					'a_order' 			=> '0',
					'main_photo' 		=> wppa_opt( 'main_photo' ),
					'a_parent' 			=> wppa_opt( 'default_parent' ),
					'p_order_by' 		=> '0',
					'cover_linktype' 	=> wppa_opt( 'default_album_linktype' ),
					'cover_linkpage' 	=> '0',
					'owner' 			=> wppa_get_user(),
					'timestamp' 		=> time(),
					'modified' 			=> time(),
					'upload_limit' 		=> wppa_opt( 'upload_limit_count' ).'/'.wppa_opt( 'upload_limit_time' ),
					'alt_thumbsize' 	=> '0',
					'default_tags' 		=> '',
					'cover_type' 		=> '',
					'suba_order_by' 	=> '',
					'views' 			=> '0',
					'cats'				=> '',
					'scheduledtm' 		=> '',
					'crypt' 			=> wppa_get_unique_album_crypt()
					) );

	if ( ! wppa_is_id_free( WPPA_ALBUMS, $args['id'] ) ) $args['id'] = wppa_nextkey( WPPA_ALBUMS );

	$query = $wpdb->prepare("INSERT INTO `" . WPPA_ALBUMS . "` ( 	`id`,
																	`name`,
																	`description`,
																	`a_order`,
																	`main_photo`,
																	`a_parent`,
																	`p_order_by`,
																	`cover_linktype`,
																	`cover_linkpage`,
																	`owner`,
																	`timestamp`,
																	`modified`,
																	`upload_limit`,
																	`alt_thumbsize`,
																	`default_tags`,
																	`cover_type`,
																	`suba_order_by`,
																	`views`,
																	`cats`,
																	`scheduledtm`,
																	`crypt`
																	)
														VALUES ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s ,%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )",
																$args['id'],
																trim( $args['name'] ),
																trim( $args['description'] ),
																$args['a_order'],
																$args['main_photo'],
																$args['a_parent'],
																$args['p_order_by'],
																$args['cover_linktype'],
																$args['cover_linkpage'],
																$args['owner'],
																$args['timestamp'],
																$args['modified'],
																$args['upload_limit'],
																$args['alt_thumbsize'],
																$args['default_tags'],
																$args['cover_type'],
																$args['suba_order_by'],
																$args['views'],
																$args['cats'],
																$args['scheduledtm'],
																$args['crypt']
														);
	$iret = $wpdb->query($query);

	if ( $iret ) return $args['id'];
	else return false;
}

// Find the next available id in a table
//
// Creating a keyvalue of an auto increment primary key incidently returns the value of MAXINT,
// and thereby making it impossible to add a next record.
// This happens when a time-out occurs during an insert query.
// This is not theoretical, i have seen it happen two times on different installations.
// This routine will find a free keyvalue larger than any key used, ignoring the fact that the MAXINT key may be used.
function wppa_nextkey( $table ) {
global $wpdb;

	$name = 'wppa_'.$table.'_lastkey';
	$lastkey = get_option( $name, 'nil' );

	if ( $lastkey == 'nil' ) {	// Init option
		$lastkey = $wpdb->get_var( "SELECT `id` FROM `".$table."` WHERE `id` < '9223372036854775806' ORDER BY `id` DESC LIMIT 1" );
		wppa_dbg_q('Q207');
		if ( ! is_numeric( $lastkey ) ) $lastkey = '0';
		add_option( $name, $lastkey, '', 'no');
	}
	wppa_dbg_msg('Lastkey in '.$table.' = '.$lastkey);

	$result = $lastkey + '1';
	while ( ! wppa_is_id_free( $table, $result ) ) {
		$result++;
	}
	wppa_update_option( $name, $result );
	return $result;
}

// Check whether a given id value is not used
function wppa_is_id_free( $type, $id ) {
global $wpdb;

	if ( ! is_numeric($id) ) return false;
	if ( $id == '0' ) return false;

	$table = '';
	if ( $type == 'album' ) $table = WPPA_ALBUMS;
	elseif ( $type == 'photo' ) $table = WPPA_PHOTOS;
	else $table = $type;	// $type may be the tablename itsself

	if ( $table == '' ) {
		echo 'Unexpected error in wppa_is_id_free()';
		return false;
	}

	$exists = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `".$table."` WHERE `id` = %s", $id ), ARRAY_A );
	wppa_dbg_q('Q208');
	if ( $exists ) return false;
	return true;
}