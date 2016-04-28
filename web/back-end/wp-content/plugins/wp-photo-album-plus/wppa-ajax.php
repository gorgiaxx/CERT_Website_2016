<?php
/* wppa-ajax.php
*
* Functions used in ajax requests
* Version 6.4.08
*
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

add_action( 'wp_ajax_wppa', 'wppa_ajax_callback' );
add_action( 'wp_ajax_nopriv_wppa', 'wppa_ajax_callback' );

function wppa_ajax_callback() {
global $wpdb;
global $wppa_session;

	wppa( 'ajax', true );
	wppa( 'error', '0' );
	wppa( 'out', '' );
	$wppa_session['page']--;
	$wppa_session['ajax']++;
	wppa_save_session();

	// ALTHOUGH IF WE ARE HERE AS FRONT END VISITOR, is_admin() is true.
	// So, $wppa_opt switches are 'yes' or 'no' and not true or false.
	// So, always use the function wppa_switch( $slug ) to test on a bool setting

	// Globally check query args to prevent php injection
	$wppa_args = array( 'album', 'photo', 'slide', 'cover', 'occur', 'woccur', 'searchstring', 'topten',
						'lasten', 'comten', 'featen', 'single', 'photos-only', 'debug',
						'relcount', 'upldr', 'owner', 'rootsearch' );
	foreach ( $_REQUEST as $arg ) {
		if ( in_array( str_replace( 'wppa-', '', $arg ), $wppa_args ) ) {
			if ( strpos( $arg, '<?' ) !== false ) die( 'Security check failure #91' );
			if ( strpos( $arg, '?>' ) !== false ) die( 'Security check failure #92' );
		}
	}

	wppa_vfy_arg( 'wppa-action', true );
	wppa_vfy_arg( 'photo-id' );
	wppa_vfy_arg( 'comment-id' );
	wppa_vfy_arg( 'moccur' );
	wppa_vfy_arg( 'comemail', true );
	wppa_vfy_arg( 'comname', true );
	wppa_vfy_arg( 'tag', true );

	$wppa_action = $_REQUEST['wppa-action'];

	switch ( $wppa_action ) {
		case 'getssiptclist':
			$tag 		= str_replace( 'H', '#', $_REQUEST['tag'] );
			$mocc 		= $_REQUEST['moccur'];
			$oldvalue = '';
			if ( strpos( $wppa_session['supersearch'], ',' ) !== false ) {
				$ss_data = explode( ',', $wppa_session['supersearch'] );
				if ( count( $ss_data ) == '4' ) {
					if ( $ss_data['0'] == 'p' ) {
						if ( $ss_data['1'] == 'i' ) {
							if ( $ss_data['2'] == $_REQUEST['tag'] ) {
								$oldvalue = $ss_data['3'];
							}
						}
					}
				}
			}
			$iptcdata 	= $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `" . WPPA_IPTC ."` WHERE `photo` > '0' AND `tag` = %s ORDER BY `description`", $tag ), ARRAY_A );
			$last 		= '';
			$any 		= false;
			if ( is_array( $iptcdata ) ) foreach( $iptcdata as $item ) {
				$desc = sanitize_text_field( $item['description'] );
				$desc = str_replace( array(chr(0),chr(1),chr(2),chr(3),chr(4),chr(5),chr(6),chr(7)), '', $desc );

				if ( $desc != $last ) {
					$sel = ( $oldvalue && $oldvalue == $desc ) ? 'selected="selected"' : '';
					if ( $sel ) echo 'selected:'.$oldvalue;
					$ddesc = strlen( $desc ) > '32' ? substr( $desc, 0, 30 ) . '...' : $desc;
					echo 	'<option' .
								' value="' . esc_attr( $desc ) . '"' .
								' class="wppa-iptclist-' . $mocc . '"' .
								' ' . $sel .
								' >' .
									$ddesc .
							'</option>';
					$last = $desc;
					$any = true;
				}
			}
			if ( ! $any ) {
				$query = $wpdb->prepare( "DELETE FROM `" . WPPA_IPTC . "` WHERE `photo` = '0' AND `tag` = %s", $tag );
				$wpdb->query( $query );
//				wppa_log( 'dbg', $query );
			}
			wppa_exit();
			break;

		case 'getssexiflist':
			$tag 		= str_replace( 'H', '#', $_REQUEST['tag'] );
			$mocc 		= $_REQUEST['moccur'];
			$oldvalue = '';
			if ( strpos( $wppa_session['supersearch'], ',' ) !== false ) {
				$ss_data = explode( ',', $wppa_session['supersearch'] );
				if ( count( $ss_data ) == '4' ) {
					if ( $ss_data['0'] == 'p' ) {
						if ( $ss_data['1'] == 'e' ) {
							if ( $ss_data['2'] == $_REQUEST['tag'] ) {
								$oldvalue = $ss_data['3'];
							}
						}
					}
				}
			}
			$exifdata 	= $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `" . WPPA_EXIF ."` WHERE `photo` > '0' AND `tag` = %s ORDER BY `description`", $tag ), ARRAY_A );
			$last 		= '';
			$any 		= false;
			if ( is_array( $exifdata ) ) foreach( $exifdata as $item ) {
				$desc = sanitize_text_field( $item['description'] );
				$desc = str_replace( array(chr(0),chr(1),chr(2),chr(3),chr(4),chr(5),chr(6),chr(7)), '', $desc );

				if ( $desc != $last ) {
					$sel = ( $oldvalue && $oldvalue == $desc ) ? 'selected="selected"' : '';
					$ddesc = strlen( $desc ) > '32' ? substr( $desc, 0, 30 ) . '...' : $desc;
					echo 	'<option' .
								' value="' . esc_attr( $desc ) . '"' .
								' class="wppa-exiflist-' . $mocc . '"' .
								' ' . $sel .
								' >' .
									$ddesc .
							'</option>';
					$last = $desc;
					$any = true;
				}
			}
			if ( ! $any ) {
				$query = $wpdb->prepare( "DELETE FROM `" . WPPA_EXIF . "` WHERE `photo` = '0' AND `tag` = %s", $tag );
				$wpdb->query( $query );
//				wppa_log( 'dbg', $query );
			}
			wppa_exit();
			break;

		case 'front-edit':
			if ( ! isset( $_REQUEST['photo-id'] ) ) die( 'Missing required argument' );
			if ( strlen( $_REQUEST['photo-id'] ) == 12 ) {
				$photo = wppa_decrypt_photo( $_REQUEST['photo-id'] );
			}
			else {
				$photo = $_REQUEST['photo-id'];
			}
			$ok = false;
			if ( current_user_can( 'wppa_admin' ) ) $ok = true;
			if ( wppa_get_user() == wppa_get_photo_owner( $photo ) && ( current_user_can( 'wppa_upload' ) || ( is_user_logged_in() && wppa_switch( 'upload_edit' ) ) ) ) $ok = true;
			if ( ! $ok ) die( 'You do not have sufficient rights to do this' );
			require_once 'wppa-photo-admin-autosave.php';
			wppa( 'front_edit', true );
			echo '	<div style="padding-bottom:4px;height:24px;" >
						<span style="color:#777;" >
							<i>'.
								__( 'All modifications are instantly updated on the server. The <b style="color:#070" >Remark</b> field keeps you informed on the actions taken at the background.' , 'wp-photo-album-plus').
							'</i>
						</span>
						<input id="wppa-fe-exit" type="button" style="float:right;color:red;font-weight:bold;" onclick="window.opener.location.reload( true );window.close();" value="'.__( 'Exit & Refresh' , 'wp-photo-album-plus').'" />
						<div id="wppa-fe-count" style="float:right;" ></div>
					</div><div style="clear:both;"></div>';
			wppa_album_photos( '', $photo );
			wppa_exit();
			break;

		case 'do-comment':
			// Security check
			$mocc 	= $_REQUEST['moccur'];
			$nonce 	= $_REQUEST['wppa-nonce'];
			if ( ! wp_verify_nonce( $nonce, 'wppa-nonce-'.$mocc ) ) {
				_e( 'Security check failure' , 'wp-photo-album-plus');
				wppa_exit();
			}

			// Correct the fact that this is a non-admin operation, if it is only
			if ( is_admin() ) {
				require_once 'wppa-non-admin.php';
			}

			wppa( 'mocc', $_REQUEST['moccur'] );
			wppa( 'comment_photo', isset( $_REQUEST['photo-id'] ) ? $_REQUEST['photo-id'] : '0' );
			wppa( 'comment_id', isset( $_REQUEST['comment-edit'] ) ? $_REQUEST['comment-edit'] : '0' );

			$comment_allowed = ( ! wppa_switch( 'comment_login' ) || is_user_logged_in() );
			if ( wppa_switch( 'show_comments' ) && $comment_allowed ) {
//				if ( wppa_switch( 'search_comments' ) ) wppa_index_remove( 'photo', $_REQUEST['photo-id'] );
				wppa_do_comment( $_REQUEST['photo-id'] );		// Process the comment
				if ( wppa_switch( 'search_comments' ) ) wppa_index_update( 'photo', $_REQUEST['photo-id'] );
			}
			wppa( 'no_esc', true );
			echo wppa_comment_html( $_REQUEST['photo-id'], $comment_allowed );	// Retrieve the new commentbox content
			wppa_exit();
			break;

		case 'import':
			require_once 'wppa-upload.php';
			_wppa_page_import();
			wppa_exit();
			break;

		case 'approve':
			$iret = '0';

			if ( ! current_user_can( 'wppa_moderate' ) && ! current_user_can( 'wppa_comments' ) ) {
				_e( 'You do not have the rights to moderate photos this way' , 'wp-photo-album-plus');
				wppa_exit();
			}

			if ( isset( $_REQUEST['photo-id'] ) && current_user_can( 'wppa_moderate' ) ) {
				$iret = $wpdb->query( $wpdb->prepare( "UPDATE `".WPPA_PHOTOS."` SET `status` = 'publish' WHERE `id` = %s", $_REQUEST['photo-id'] ) );
				wppa_flush_upldr_cache( 'photoid', $_REQUEST['photo-id'] );
				$alb = $wpdb->get_var( $wpdb->prepare( "SELECT `album` FROM `".WPPA_PHOTOS."` WHERE `id` = %s", $_REQUEST['photo-id'] ) );
				wppa_clear_taglist();
				wppa_flush_treecounts( $alb );
			}
			if ( isset( $_REQUEST['comment-id'] ) ) {
				$iret = $wpdb->query( $wpdb->prepare( "UPDATE `".WPPA_COMMENTS."` SET `status` = 'approved' WHERE `id` = %s", $_REQUEST['comment-id'] ) );
			}
			if ( $iret ) {
				echo 'OK';
			}
			else {
				if ( isset( $_REQUEST['photo-id'] ) ) {
					if ( current_user_can( 'wppa_moderate' ) ) {
						echo sprintf( __( 'Failed to update stutus of photo %s' , 'wp-photo-album-plus'), $_REQUEST['photo-id'] )."\n".__( 'Please refresh the page' , 'wp-photo-album-plus');
					}
					else {
						_e( 'Security check failure' , 'wp-photo-album-plus');
					}
				}
				if ( isset( $_REQUEST['comment-id'] ) ) {
					echo sprintf( __( 'Failed to update stutus of comment %s' , 'wp-photo-album-plus'), $_REQUEST['comment-id'] )."\n".__( 'Please refresh the page' , 'wp-photo-album-plus');
				}
			}
			wppa_exit();

		case 'remove':
			if ( isset( $_REQUEST['photo-id'] ) ) {	// Remove photo
				if ( strlen( $_REQUEST['photo-id'] ) == 12 ) {
					$photo = wppa_decrypt_photo( $_REQUEST['photo-id'] );
				}
				else {
					$photo = $_REQUEST['photo-id'];
				}
				if ( ( wppa_user_is( 'administrator' ) ) || ( current_user_can( 'wppa_moderate' ) ) || ( wppa_get_user() == wppa_get_photo_owner( $photo ) && wppa_switch( 'upload_edit' ) ) ) { // Frontend delete?
					wppa_delete_photo( $photo );
					echo 'OK||'.__( 'Photo removed' , 'wp-photo-album-plus');
					wppa_exit();
				}
			}
			if ( ! current_user_can( 'wppa_moderate' ) && ! current_user_can( 'wppa_comments' ) ) {
				_e( 'You do not have the rights to moderate photos this way' , 'wp-photo-album-plus');
				wppa_exit();
			}
			if ( isset( $_REQUEST['photo-id'] ) ) {	// Remove photo
				if ( strlen( $_REQUEST['photo-id'] ) == 12 ) {
					$photo = wppa_decrypt_photo( $_REQUEST['photo-id'] );
				}
				else {
					$photo = $_REQUEST['photo-id'];
				}
				if ( ! current_user_can( 'wppa_moderate' ) ) {
					_e( 'Security check failure' , 'wp-photo-album-plus');
					wppa_exit();
				}
				wppa_delete_photo( $photo );
				echo 'OK||'.__( 'Photo removed' , 'wp-photo-album-plus');
				wppa_exit();
			}
			if ( isset( $_REQUEST['comment-id'] ) ) {	// Remove comment
				$iret = $wpdb->query( $wpdb->prepare( "DELETE FROM `".WPPA_COMMENTS."` WHERE `id`= %s", $_REQUEST['comment-id'] ) );
				if ( $iret ) echo 'OK||'.__( 'Comment removed' , 'wp-photo-album-plus');
				else _e( 'Could not remove comment' , 'wp-photo-album-plus');
				wppa_exit();
			}
			_e( 'Unexpected error' , 'wp-photo-album-plus');
			wppa_exit();

		case 'downloadalbum':
			// Feature enabled?
			if ( ! wppa_switch( 'allow_download_album' ) ) {
				echo '||ER||'.__( 'This feature is not enabled on this website' , 'wp-photo-album-plus');
				wppa_exit();
			}

			// Validate args
			$alb = wppa_decrypt_album( $_REQUEST['album-id'] );

			$status = "`status` <> 'pending' AND `status` <> 'scheduled'";
			if ( ! is_user_logged_in() ) $status .= " AND `status` <> 'private'";

			$photos = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `".WPPA_PHOTOS."` WHERE `album` = %s AND ( ( ".$status." ) OR owner = %s ) ".wppa_get_photo_order( $alb ), $alb, wppa_get_user() ), ARRAY_A );
			if ( ! $photos ) {
				echo '||ER||'.__( 'The album is empty' , 'wp-photo-album-plus');
				wppa_exit();
			}

			// Remove obsolete files
			wppa_delete_obsolete_tempfiles();

			// Open zipfile
			if ( ! class_exists( 'ZipArchive' ) ) {
				echo '||ER||'.__( 'Unable to create zip archive' , 'wp-photo-album-plus');
				wppa_exit();
			}
			$zipfilename = wppa_get_album_name( $alb );
			$zipfilename = wppa_sanitize_file_name( $zipfilename.'.zip' ); 				// Remove illegal chars
			$zipfilepath = WPPA_UPLOAD_PATH.'/temp/'.$zipfilename;
			if ( is_file( $zipfilepath ) ) {
		//		unlink( $zipfilepath );	// Debug
			}
			$wppa_zip = new ZipArchive;
			$iret = $wppa_zip->open( $zipfilepath, 1 );
			if ( $iret !== true ) {
				echo '||ER||'.sprintf( __( 'Unable to create zip archive. code = %s' , 'wp-photo-album-plus'), $iret );
				wppa_exit();
			}

			// Add photos to zip
			$stop = false;
			foreach ( $photos as $p ) {
				if ( wppa_is_time_up() ) {
					wppa_log( 'obs', 'Time up during album to zip creation' );
					$stop = true;
				}
				else {
					$id = $p['id'];
					if ( ! wppa_is_multi( $id ) ) {
						$source = ( wppa_switch( 'download_album_source' ) && is_file( wppa_get_source_path( $id ) ) ) ? wppa_get_source_path( $id ) : wppa_get_photo_path( $id );
						if ( is_file( $source ) ) {
							$dest = $p['filename'] ? wppa_sanitize_file_name( $p['filename'] ) : wppa_sanitize_file_name( wppa_strip_ext( $p['name'] ).'.'.$p['ext'] );
							$dest = wppa_fix_poster_ext( $dest, $id );
							$iret = $wppa_zip->addFile( $source, $dest );

							// To prevent too may files open, and to have at least a file when there are too many photos, close and re-open
							$wppa_zip->close();
							$wppa_zip->open( $zipfilepath );
							// wppa_log( 'dbg', 'Added ' . basename($source) . ' to ' . basename($zipfilepath));
						}
					}
				}
				if ( $stop ) break;
			}

			// Close zip and return
			$zipcount = $wppa_zip->numFiles;
			$wppa_zip->close();

			// A zip is created
			$desturl = WPPA_UPLOAD_URL.'/temp/'.$zipfilename;
			echo $desturl.'||OK||';
			if ( $zipcount != count( $photos ) ) echo sprintf( __( 'Only %s out of %s photos could be added to the zipfile' , 'wp-photo-album-plus'), $zipcount, count( $photos ) );
			wppa_exit();
			break;

		case 'getalbumzipurl':
			$alb = $_REQUEST['album-id'];
			$zipfilename = wppa_get_album_name( $alb );
			$zipfilename = wppa_sanitize_file_name( $zipfilename.'.zip' ); 				// Remove illegal chars
			$zipfilepath = WPPA_UPLOAD_PATH.'/temp/'.$zipfilename;
			$zipfileurl  = WPPA_UPLOAD_URL.'/temp/'.$zipfilename;
			if ( is_file( $zipfilepath ) ) {
				echo $zipfileurl;
			}
			else {
				echo 'ER';
			}
			wppa_exit();
			break;

		case 'addtozip':

			// Check if the user is allowed to do this
			$photo = wppa_decrypt_photo( $_REQUEST['photo-id'] );
			if ( ! wppa_user_is( 'administrator' ) ) {
				echo 'ER||Security check failure';
				wppa_exit();
			}

			// Do we have ziparchive on board?
			if ( ! class_exists( 'ZipArchive' ) ) {
				echo 'ER||'.__( 'Unable to create zip archive' , 'wp-photo-album-plus');
				wppa_exit();
			}

			// Verify existance of zips dir
			$zipsdir = WPPA_UPLOAD_PATH.'/zips/';
			if ( ! is_dir( $zipsdir ) ) @ mkdir( $zipsdir );
			if ( ! is_dir( $zipsdir ) ) {
				echo 'ER||'.__( 'Unable to create zipsdir' , 'wp-photo-album-plus');
				wppa_exit();
			}

			// Compose the users zip filename
			$zipfile = $zipsdir.wppa_get_user().'.zip';

			// Find the photo data
			$data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `".WPPA_PHOTOS."` WHERE `id` = %s", $photo ), ARRAY_A );

			// Find the photo file
			if ( is_file ( wppa_get_source_path( $photo ) ) ) {
				$source = wppa_get_source_path( $photo );
			}
			else {
				$source = wppa_get_photo_path( $photo );
			}
			$source = wppa_fix_poster_ext( $source, $photo );

			// Add photo to zip
			$wppa_zip = new ZipArchive;
			$wppa_zip->open( $zipfile, 1 );
			$wppa_zip->addFile( $source, wppa_fix_poster_ext( $data['filename'], $photo ) );
			$wppa_zip->close();

			echo 'OK||'.__('Selected', 'wp-photo-album-plus');
			wppa_exit();
			break;

		case 'delmyzip':
			// Verify existance of zips dir
			$zipsdir = WPPA_UPLOAD_PATH.'/zips/';
			if ( is_dir( $zipsdir ) ) {

				// Compose the users zip filename
				$zipfile = $zipsdir.wppa_get_user().'.zip';

				// Check file existance and remove
				if ( is_file( $zipfile ) ) {
					@ unlink( $zipfile );
				}
			}
			wppa_exit();
			break;

		case 'makeorigname':
			$photo = wppa_decrypt_photo( $_REQUEST['photo-id'] );
			$from = $_REQUEST['from'];
			if ( $from == 'fsname' ) {
				$type = wppa_opt( 'art_monkey_link' );
			}
			elseif ( $from == 'popup' ) {
				$type = wppa_opt( 'art_monkey_popup_link' );
			}
			else {
				echo '||7||'.__( 'Unknown source of request' , 'wp-photo-album-plus');
				wppa_exit();
			}

			$data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `".WPPA_PHOTOS."` WHERE `id` = %s", $photo ), ARRAY_A );

			if ( $data ) {	// The photo is supposed to exist

				// Make the name
				if ( $data['filename'] ) {
					$name = $data['filename'];
				}
				else {
					$name = __( $data['name'] , 'wp-photo-album-plus');
				}
				$name = wppa_sanitize_file_name( $name ); 				// Remove illegal chars
				$name = preg_replace( '/\.[^.]*$/', '', $name );	// Remove file extension
				if ( strlen( $name ) == '0' ) {
					echo '||1||'.__( 'Empty filename' , 'wp-photo-album-plus');
					wppa_exit();
				}

				// Make the file
				if ( wppa_switch( 'artmonkey_use_source' ) ) {
					if ( is_file ( wppa_get_source_path( $photo ) ) ) {
						$source = wppa_get_source_path( $photo );
					}
					else {
						$source = wppa_get_photo_path( $photo );
					}
				}
				else {
					$source = wppa_get_photo_path( $photo );
				}
				$source = wppa_fix_poster_ext( $source, $photo );

				// Fix the extension for mm items.
				if ( $data['ext'] == 'xxx' ) {
					$data['ext'] = wppa_get_ext( $source );
				}
				$dest 		= WPPA_UPLOAD_PATH.'/temp/'.$name.'.'.$data['ext'];
				$zipfile 	= WPPA_UPLOAD_PATH.'/temp/'.$name.'.zip';
				$tempdir 	= WPPA_UPLOAD_PATH.'/temp';
				if ( ! is_dir( $tempdir ) ) @ mkdir( $tempdir );
				if ( ! is_dir( $tempdir ) ) {
					echo '||2||'.__( 'Unable to create tempdir' , 'wp-photo-album-plus');
					wppa_exit();
				}

				// Remove obsolete files
				wppa_delete_obsolete_tempfiles();

				// Make the files
				if ( $type == 'file' ) {
					copy( $source, $dest );
					$ext = $data['ext'];
				}
				elseif ( $type == 'zip' ) {
					if ( ! class_exists( 'ZipArchive' ) ) {
						echo '||8||'.__( 'Unable to create zip archive' , 'wp-photo-album-plus');
						wppa_exit();
					}
					$ext = 'zip';
					$wppa_zip = new ZipArchive;
					$wppa_zip->open( $zipfile, 1 );
					$wppa_zip->addFile( $source, basename( $dest ) );
					$wppa_zip->close();
				}
				else {
					echo '||6||'.__( 'Unknown type' , 'wp-photo-album-plus');
					wppa_exit();
				}

				$desturl = WPPA_UPLOAD_URL.'/temp/'.$name.'.'.$ext;
				echo '||0||'.$desturl;	// No error: return url
				wppa_exit();
			}
			else {
				echo '||9||'.__( 'The photo does no longer exist' , 'wp-photo-album-plus');
				wppa_exit();
			}
			wppa_exit();
			break;

		case 'tinymcedialog':
			$result = wppa_make_tinymce_dialog();
			echo $result;
			wppa_exit();
			break;

		case 'bumpviewcount':
			$nonce  = $_REQUEST['wppa-nonce'];
			if ( wp_verify_nonce( $nonce, 'wppa-check' ) ) {
				wppa_bump_viewcount( 'photo', $_REQUEST['wppa-photo'] );
			}
			else {
				_e( 'Security check failure' , 'wp-photo-album-plus');
			}
			wppa_exit();
			break;

		case 'rate':
			// Get commandline args
			$photo  = wppa_decrypt_photo( $_REQUEST['wppa-rating-id'] );
			$rating = $_REQUEST['wppa-rating'];
			$occur  = $_REQUEST['wppa-occur'];
			$index  = $_REQUEST['wppa-index'];
			$nonce  = $_REQUEST['wppa-nonce'];

			// Make errortext
			$errtxt = __( 'An error occurred while processing you rating request.' , 'wp-photo-album-plus');
			$errtxt .= "\n".__( 'Maybe you opened the page too long ago to recognize you.' , 'wp-photo-album-plus');
			$errtxt .= "\n".__( 'You may refresh the page and try again.' , 'wp-photo-album-plus');
			$wartxt = __( 'Althoug an error occurred while processing your rating, your vote has been registered.' , 'wp-photo-album-plus');
			$wartxt .= "\n".__( 'However, this may not be reflected in the current pageview' , 'wp-photo-album-plus');

			// Check on validity
			if ( ! wp_verify_nonce( $nonce, 'wppa-check' ) ) {
				echo '0||100||'.$errtxt;
				wppa_exit();																// Nonce check failed
			}
			if ( wppa_opt( 'rating_max' ) == '1' && $rating != '1' ) {
				echo '0||106||'.$errtxt.':'.$rating;
				wppa_exit();																// Value out of range
			}
			elseif ( wppa_opt( 'rating_max' ) == '5' && ! in_array( $rating, array( '-1', '1', '2', '3', '4', '5' ) ) ) {
				echo '0||106||'.$errtxt.':'.$rating;
				wppa_exit();																// Value out of range
			}
			elseif ( wppa_opt( 'rating_max' ) == '10' && ! in_array( $rating, array( '-1', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10' ) ) ) {
				echo '0||106||'.$errtxt.':'.$rating;
				wppa_exit();																// Value out of range
			}

			// Get other data
			if ( ! $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_PHOTOS."` WHERE `id` = %s", $photo ) ) ) {
				echo '0||999||'.__( 'Photo has been removed.' , 'wp-photo-album-plus');
				wppa_exit();
			}
			$user     = wppa_get_user();
			$mylast   = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM `'.WPPA_RATING.'` WHERE `photo` = %s AND `user` = %s ORDER BY `id` DESC LIMIT 1', $photo, $user ), ARRAY_A );
			$myavgrat = '0';			// Init

			// Rate own photo?
			if ( wppa_get_photo_item( $photo, 'owner' ) == $user && ! wppa_switch( 'allow_owner_votes' ) ) {
				echo '0||900||'.__( 'Sorry, you can not rate your own photos' , 'wp-photo-album-plus');
				wppa_exit();
			}

			// Already a pending one?
			$pending = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_RATING."` WHERE `photo` = %s AND `user` = %s AND `status` = %s", $photo, $user, 'pending' ) );

			// Has user motivated his vote?
			$hascommented = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_COMMENTS."` WHERE `photo` = %s AND `user` = %s", $photo, wppa_get_user( 'display' ) ) );

			if ( $pending ) {
				if ( ! $hascommented ) {
					echo '0||900||'.__( 'Please enter a comment.' , 'wp-photo-album-plus');
					wppa_exit();
				}
				else {
					$wpdb->query( $wpdb->prepare( "UPDATE `".WPPA_RATING."` SET `status` = 'publish' WHERE `photo` = %s AND `user` = %s", $photo, $user ) );
				}
			}

			if ( wppa_switch( 'vote_needs_comment' ) ) {
				$ratingstatus = $hascommented ? 'publish' : 'pending';
			}
			else {
				$ratingstatus = 'publish';
			}

			// When done, we have to echo $occur.'||'.$photo.'||'.$index.'||'.$myavgrat.'||'.$allavgrat.'||'.$discount.'||'.$hascommented.'||'.$message;
			// So we have to do: process rating and find new $myavgrat, $allavgrat and $discount ( $occur, $photo and $index are known )

			// Case 0: Illegal second vote. Frontend takes care of this, but a hacker could enter an ajaxlink manually
			if ( $mylast && ( 	// I did vote already
								( ! ( wppa_switch( 'rating_change' ) || wppa_switch( 'rating_multi' ) ) ) || 	// No rating change or rating multi
								( $mylast['value'] < '0' ) ||														// I did a dislike, can not modify
								( $mylast['value'] > '0' && $rating == '-1' )										// I did a rating, can not change into dislike
							 )
						 ) {
				echo '0||109||'.__( 'Security check failure.' , 'wp-photo-album-plus');
				wppa_exit();
			}
			// Case 1: value = -1 this is a legal dislike vote
			if ( $rating == '-1' ) {
				// Add my dislike
				$iret = wppa_create_rating_entry( array( 'photo' => $photo, 'value' => $rating, 'user' => $user, 'status' => $ratingstatus ) );
				if ( ! $iret ) {
					echo '0||101||'.$errtxt;
					wppa_exit();															// Fail on storing vote
				}
				// Add points
				wppa_add_credit_points( wppa_opt( 'cp_points_rating' ), __( 'Photo rated' , 'wp-photo-album-plus'), $photo, $rating );
				wppa_dislike_check( $photo );	// Check for email to be sent every .. dislikes
				if ( ! is_file( wppa_get_thumb_path( $photo ) ) ) {	// Photo is removed
					 echo $occur.'||'.$photo.'||'.$index.'||-1||-1|0||'.wppa_opt( 'dislike_delete' );
					 wppa_exit();
				}
			}
			// Case 2: This is my first vote for this photo
			elseif ( ! $mylast ) {
				// Add my vote
				$iret = wppa_create_rating_entry( array( 'photo' => $photo, 'value' => $rating, 'user' => $user, 'status' => $ratingstatus ) );
				if ( ! $iret ) {
					echo '0||102||'.$errtxt;
					wppa_exit();															// Fail on storing vote
				}
				// Add points
				wppa_add_credit_points( wppa_opt( 'cp_points_rating' ), __( 'Photo rated' , 'wp-photo-album-plus'), $photo, $rating );
			}
			// Case 3: I will change my previously given vote
			elseif ( wppa_switch( 'rating_change' ) ) {					// Votechanging is allowed
				$iret = $wpdb->query( $wpdb->prepare( 'UPDATE `'.WPPA_RATING.'` SET `value` = %s WHERE `photo` = %s AND `user` = %s LIMIT 1', $rating, $photo, $user ) );
				if ( $iret === false ) {
					echo '0||103||'.$errtxt;
					wppa_exit();															// Fail on update
				}
			}
			// Case 4: Add another vote from me
			elseif ( wppa_switch( 'rating_multi' ) ) {					// Rating multi is allowed
				$iret = wppa_create_rating_entry( array( 'photo' => $photo, 'value' => $rating, 'user' => $user, 'status' => $ratingstatus ) );
				if ( ! $iret ) {
					echo '0||104||'.$errtxt;
					wppa_exit();															// Fail on storing vote
				}
			}
			else { 																	// Should never get here....
				echo '0||110||'.__( 'Unexpected error' , 'wp-photo-album-plus');
				wppa_exit();
			}

			// Compute my avg rating
			$myrats = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `'.WPPA_RATING.'`  WHERE `photo` = %s AND `user` = %s AND `status` = %s ', $photo, $user, 'publish' ), ARRAY_A );
			if ( $myrats ) {
				$sum = 0;
				$cnt = 0;
				foreach ( $myrats as $rat ) {
					if ( $rat['value'] == '-1' ) {
						$sum += wppa_opt( 'dislike_value' );
					}
					else {
						$sum += $rat['value'];
					}
					$cnt ++;
				}
				$myavgrat = $sum/$cnt;
				$i = wppa_opt( 'rating_prec' );
				$j = $i + '1';
				$myavgrat = sprintf( '%'.$j.'.'.$i.'f', $myavgrat );
			}
			else {
				$myavgrat = '0';
			}

			// Compute new allavgrat
			$ratings = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM '.WPPA_RATING.' WHERE `photo` = %s AND `status` = %s', $photo, 'publish' ), ARRAY_A );
			if ( $ratings ) {
				$sum = 0;
				$cnt = 0;
				foreach ( $ratings as $rat ) {
					if ( $rat['value'] == '-1' ) {
						$sum += wppa_opt( 'dislike_value' );
					}
					else {
						$sum += $rat['value'];
					}
					$cnt++;
				}
				$allavgrat = $sum/$cnt;
				if ( $allavgrat == '10' ) $allavgrat = '9.99999999';	// For sort order reasons text field
			}
			else $allavgrat = '0';

			// Store it in the photo info
			$iret = $wpdb->query( $wpdb->prepare( 'UPDATE `'.WPPA_PHOTOS. '` SET `mean_rating` = %s WHERE `id` = %s', $allavgrat, $photo ) );
			if ( $iret === false ) {
				echo '0||106||'.$wartxt;
				wppa_exit();																// Fail on save
			}

			// Compute rating_count and store in the photo info
			$ratcount = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_RATING."` WHERE `photo` = %s", $photo ) );
			if ( $ratcount !== false ) {
				$iret = $wpdb->query( $wpdb->prepare( "UPDATE `".WPPA_PHOTOS."` SET `rating_count` = %s WHERE `id` = %s", $ratcount, $photo ) );
				if ( $iret === false ) {
					echo '0||107||'.$wartxt;
					wppa_exit();																// Fail on save
				}
			}

			// Format $allavgrat for output
			$allavgratcombi = $allavgrat.'|'.$ratcount;

			// Compute dsilike count
			$discount = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_RATING."` WHERE `photo` = %s AND `value` = -1 AND `status` = %s", $photo, 'publish' ) );
			if ( $discount === false ) {
				echo '0||108||'.$wartxt;
				wppa_exit();																// Fail on save
			}
			$distext = wppa_get_distext( $discount, $rating );
			if ( ! $distext ) {
				$distext = '0';
			}

			// Test for possible medal
			wppa_test_for_medal( $photo );

			// Success!
			wppa_clear_cache();

			if ( wppa_switch( 'vote_needs_comment' ) && ! $hascommented ) {
				$message = __( "Please explain your vote in a comment.\nYour vote will be discarded if you don't.\n\nAfter completing your comment,\nyou can refresh the page to see\nyour vote became effective." , 'wp-photo-album-plus');
			}
			else {
				$message = '';
			}

			echo $occur.'||'.$photo.'||'.$index.'||'.$myavgrat.'||'.$allavgratcombi.'||'.$distext.'||'.$hascommented.'||'.$message;
			break;

		case 'render':
			$tim_1 	= microtime( true );
			$nq_1 	= get_num_queries();

			// Correct the fact that this is a non-admin operation, if it is
			if ( is_admin() ) {
				require_once 'wppa-non-admin.php';
			}
			wppa_load_theme();
			// Register geo shortcode if google-maps-gpx-vieuwer is on board. GPX does it in wp_head(), what is not done in an ajax call
//			if ( function_exists( 'gmapv3' ) ) add_shortcode( 'map', 'gmapv3' );
			// Get the post we are working for
			if ( isset ( $_REQUEST['wppa-fromp'] ) ) {
				$p = $_REQUEST['wppa-fromp'];
				if ( wppa_is_int( $p ) ) {
					$GLOBALS['post'] = get_post( $p );
				}
			}
			// Render
			echo wppa_albums();

			$tim_2 	= microtime( true );
			$nq_2 	= get_num_queries();
			$mem 	= memory_get_peak_usage( true ) / 1024 / 1024;

			$msg 	= sprintf( 'WPPA Ajax render: db queries: WP:%d, WPPA+: %d in %4.2f seconds, using %4.2f MB memory max', $nq_1, $nq_2 - $nq_1, $tim_2 - $tim_1, $mem );
			echo '<script type="text/javascript">wppaConsoleLog( \''.$msg.'\', \'force\' )</script>';
			break;

		case 'delete-photo':
			$photo = $_REQUEST['photo-id'];
			$nonce = $_REQUEST['wppa-nonce'];

			// Check validity
			if ( ! wp_verify_nonce( $nonce, 'wppa_nonce_'.$photo ) ) {
				echo '||0||'.__( 'You do not have the rights to delete a photo' , 'wp-photo-album-plus');
				wppa_exit();																// Nonce check failed
			}
			if ( ! is_numeric( $photo ) ) {
				echo '||0||'.__( 'Security check failure' , 'wp-photo-album-plus');
				wppa_exit();																// Nonce check failed
			}
			$album = $wpdb->get_var( $wpdb->prepare( 'SELECT `album` FROM `'.WPPA_PHOTOS.'` WHERE `id` = %s', $photo ) );
			wppa_delete_photo( $photo );
			wppa_clear_cache();
			echo '||1||<span style="color:red" >'.sprintf( __( 'Photo %s has been deleted' , 'wp-photo-album-plus'), $photo ).'</span>';
			echo '||';
			$a = wppa_allow_uploads( $album );
			if ( ! $a ) echo 'full';
			else echo 'notfull||'.$a;
			break;

		case 'update-album':
			$album = $_REQUEST['album-id'];
			$nonce = $_REQUEST['wppa-nonce'];
			$item  = $_REQUEST['item'];
			$value = $_REQUEST['value'];
			$value  = wppa_decode( $value );

			// Check validity
			if ( ! wp_verify_nonce( $nonce, 'wppa_nonce_'.$album ) ) {
				echo '||0||'.__( 'You do not have the rights to update album information' , 'wp-photo-album-plus').$nonce;
				wppa_exit();																// Nonce check failed
			}

			switch ( $item ) {
				case 'clear_ratings':
					$photos = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `'.WPPA_PHOTOS.'` WHERE `album` = %s', $album ), ARRAY_A );
					if ( $photos ) foreach ( $photos as $photo ) {
						$iret1 = $wpdb->query( $wpdb->prepare( 'DELETE FROM `'.WPPA_RATING.'` WHERE `photo` = %s', $photo['id'] ) );
						$iret2 = $wpdb->query( $wpdb->prepare( 'UPDATE `'.WPPA_PHOTOS.'` SET `mean_rating` = %s WHERE `id` = %s', '', $photo['id'] ) );
					}
					if ( $photos && $iret1 !== false && $iret2 !== false ) {
						echo '||97||'.__( '<b>Ratings cleared</b>' , 'wp-photo-album-plus').'||'.__( 'No ratings for this photo.' , 'wp-photo-album-plus');
					}
					elseif ( $photos ) {
						echo '||1||'.__( 'An error occurred while clearing ratings' , 'wp-photo-album-plus');
					}
					else {
						echo '||97||'.__( '<b>No photos in this album</b>' , 'wp-photo-album-plus').'||'.__( 'No ratings for this photo.' , 'wp-photo-album-plus');
					}
					wppa_exit();
					break;
				case 'set_deftags':	// to be changed for large albums
					$photos = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `'.WPPA_PHOTOS.'` WHERE `album` = %s', $album ), ARRAY_A );
					$deftag = $wpdb->get_var( $wpdb->prepare( 'SELECT `default_tags` FROM `'.WPPA_ALBUMS.'` WHERE `id` = %s', $album ) );
					if ( is_array( $photos ) ) foreach ( $photos as $photo ) {

						$tags = wppa_sanitize_tags( wppa_filter_iptc( wppa_filter_exif( $deftag, $photo['id'] ), $photo['id'] ) );

						$iret = $wpdb->query( $wpdb->prepare( 'UPDATE `'.WPPA_PHOTOS.'` SET `tags` = %s WHERE `id` = %s', $tags, $photo['id'] ) );
						wppa_index_update( 'photo', $photo['id'] );
					}
					if ( $photos && $iret !== false ) {
						echo '||97||'.__( '<b>Tags set to defaults</b> (reload)' , 'wp-photo-album-plus');
					}
					elseif ( $photos ) {
						echo '||1||'.__( 'An error occurred while setting tags' , 'wp-photo-album-plus');
					}
					else {
						echo '||97||'.__( '<b>No photos in this album</b>' , 'wp-photo-album-plus');
					}
					wppa_clear_taglist();
					wppa_exit();
					break;
				case 'add_deftags':
					$photos = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `'.WPPA_PHOTOS.'` WHERE `album` = %s', $album ), ARRAY_A );
					$deftag = $wpdb->get_var( $wpdb->prepare( 'SELECT `default_tags` FROM `'.WPPA_ALBUMS.'` WHERE `id` = %s', $album ) );
					if ( is_array( $photos ) ) foreach ( $photos as $photo ) {

						$tags = wppa_sanitize_tags( wppa_filter_iptc( wppa_filter_exif( $photo['tags'].','.$deftag, $photo['id'] ), $photo['id'] ) );

						$iret = $wpdb->query( $wpdb->prepare( 'UPDATE `'.WPPA_PHOTOS.'` SET `tags` = %s WHERE `id` = %s', $tags, $photo['id'] ) );
						wppa_index_update( 'photo', $photo['id'] );
					}
					if ( $photos && $iret !== false ) {
						echo '||97||'.__( '<b>Tags added width defaults</b> (reload)' , 'wp-photo-album-plus');
					}
					elseif ( $photos ) {
						echo '||1||'.__( 'An error occurred while adding tags' , 'wp-photo-album-plus');
					}
					else {
						echo '||97||'.__( '<b>No photos in this album</b>' , 'wp-photo-album-plus');
					}
					wppa_clear_taglist();
					wppa_exit();
					break;
				case 'inherit_cats';
				case 'inhadd_cats':
					$albids = wppa_expand_enum( wppa_alb_to_enum_children( $album ) );
					$albarr = explode( '.', $albids );
					$cats = wppa_get_album_item( $album, 'cats' );
					if ( $cats || $item == 'inherit_cats' ) {
						if ( count( $albarr ) > 1 ) {
							foreach( $albarr as $alb ) if ( $album != $alb ) {
								if ( $item == 'inherit_cats' ) {
									wppa_update_album( array( 'id' => $alb, 'cats' => $cats ) );
								}
								else { // 'inhadd_cats'
									$mycats = wppa_get_album_item( $alb, 'cats' );
									wppa_update_album( array( 'id' => $alb, 'cats' => $mycats . $cats ) );
								}
							}
						}
						else {
							echo '||0||' . __( 'No subalbums found to process', 'wp-photo-album-plus' );
							wppa_exit();
						}
					}
					else {
						echo '||0||' . __( 'No categories found to process', 'wp-photo-album-plus' );
						wppa_exit();
					}
					$n = count( $albarr ) - 1;
					echo '||0||' . sprintf( _n( '%d album updated', '%d albums updated', $n, 'wp-photo-album-plus' ), $n );
					wppa_exit();
					break;
				case 'name':
					$value = trim( strip_tags( $value ) );
					if ( ! wppa_sanitize_file_name( $value ) ) {	// Empty album name is not allowed
						$value = 'Album-#'.$album;
						echo '||5||' . sprintf( __( 'Album name may not be empty.<br />Reset to <b>%s</b>' , 'wp-photo-album-plus'), $value );
					}
					$itemname = __( 'Name' , 'wp-photo-album-plus');
					break;
				case 'description':
					$itemname = __( 'Description' , 'wp-photo-album-plus');
					if ( wppa_switch( 'check_balance' ) ) {
						$value = str_replace( array( '<br/>','<br>' ), '<br />', $value );
						if ( balanceTags( $value, true ) != $value ) {
							echo '||3||'.__( 'Unbalanced tags in album description!' , 'wp-photo-album-plus');
							wppa_exit();
						}
					}
					$value = trim( $value );
					break;
				case 'a_order':
					$itemname = __( 'Album order #' , 'wp-photo-album-plus');
					break;
				case 'main_photo':
					$itemname = __( 'Cover photo' , 'wp-photo-album-plus');
					break;
				case 'a_parent':
					$itemname = __( 'Parent album' , 'wp-photo-album-plus');
					wppa_flush_treecounts( $album );	// Myself and my parents
					wppa_flush_treecounts( $value );	// My new parent
					break;
				case 'p_order_by':
					$itemname = __( 'Photo order' , 'wp-photo-album-plus');
					break;
				case 'alt_thumbsize':
					$itemname = __( 'Use Alt thumbsize' , 'wp-photo-album-plus');
					break;
				case 'cover_type':
					$itemname = __( 'Cover Type' , 'wp-photo-album-plus');
					break;
				case 'cover_linktype':
					$itemname = __( 'Link type' , 'wp-photo-album-plus');
					break;
				case 'cover_linkpage':
					$itemname = __( 'Link to' , 'wp-photo-album-plus');
					break;
				case 'owner':
					$itemname = __( 'Owner' , 'wp-photo-album-plus');
					if ( $value != '--- public ---' && ! get_user_by( 'login', $value ) ) {
						echo '||4||'.sprintf( __( 'User %s does not exist' , 'wp-photo-album-plus'), $value );
						wppa_exit();
					}
					break;
				case 'upload_limit_count':
					wppa_ajax_check_range( $value, false, '0', false, __( 'Upload limit count' , 'wp-photo-album-plus') );
					if ( wppa( 'error' ) ) wppa_exit();
					$oldval = $wpdb->get_var( $wpdb->prepare( 'SELECT `upload_limit` FROM '.WPPA_ALBUMS.' WHERE `id` = %s', $album ) );
					$temp = explode( '/', $oldval );
					$value = $value.'/'.$temp[1];
					$item = 'upload_limit';
					$itemname = __( 'Upload limit count' , 'wp-photo-album-plus');
					break;
				case 'upload_limit_time':
					$oldval = $wpdb->get_var( $wpdb->prepare( 'SELECT `upload_limit` FROM '.WPPA_ALBUMS.' WHERE `id` = %s', $album ) );
					$temp = explode( '/', $oldval );
					$value = $temp[0].'/'.$value;
					$item = 'upload_limit';
					$itemname = __( 'Upload limit time' , 'wp-photo-album-plus');
					break;
				case 'default_tags':
					$value = wppa_sanitize_tags( $value, false, true );
					$itemname = __( 'Default tags' , 'wp-photo-album-plus');
					break;
				case 'cats':
					$value = wppa_sanitize_cats( $value );
					wppa_clear_catlist();
					$itemname = __( 'Categories' , 'wp-photo-album-plus');
					break;
				case 'suba_order_by':
					$itemname = __( 'Sub albums sort order' , 'wp-photo-album-plus');
					break;

				case 'year':
				case 'month':
				case 'day':
				case 'hour':
				case 'min':
					$itemname = __( 'Schedule date/time' , 'wp-photo-album-plus');
					$scheduledtm = $wpdb->get_var( $wpdb->prepare( "SELECT `scheduledtm` FROM`".WPPA_ALBUMS."` WHERE `id` = %s", $album ) );
					if ( ! $scheduledtm ) {
						$scheduledtm = wppa_get_default_scheduledtm();
					}
					$temp = explode( ',', $scheduledtm );
					if ( $item == 'year' ) 	$temp[0] = $value;
					if ( $item == 'month' ) $temp[1] = $value;
					if ( $item == 'day' ) 	$temp[2] = $value;
					if ( $item == 'hour' ) 	$temp[3] = $value;
					if ( $item == 'min' ) 	$temp[4] = $value;
					$scheduledtm = implode( ',', $temp );
					wppa_update_album( array( 'id' => $album, 'scheduledtm' => $scheduledtm ) );
					echo '||0||'.sprintf( __( '<b>%s</b> of album %s updated' , 'wp-photo-album-plus'), $itemname, $album );
					wppa_exit();
					break;

				case 'setallscheduled':
					$scheduledtm = $wpdb->get_var( $wpdb->prepare( "SELECT `scheduledtm` FROM `".WPPA_ALBUMS."` WHERE `id` = %s", $album ) );
					if ( $scheduledtm ) {
						$iret = $wpdb->query( $wpdb->prepare( "UPDATE `".WPPA_PHOTOS."` SET `status` = 'scheduled', `scheduledtm` = %s WHERE `album` = %s", $scheduledtm, $album ) );
						echo '||0||'.__( 'All photos set to scheduled per date' , 'wp-photo-album-plus').' ( '.$iret.' ) '.wppa_format_scheduledtm( $scheduledtm );
					}
					wppa_exit();
					break;

				default:
					$itemname = $item;
			}

			$query = $wpdb->prepare( 'UPDATE '.WPPA_ALBUMS.' SET `'.$item.'` = %s WHERE `id` = %s', $value, $album );
			$iret = $wpdb->query( $query );
			if ( $iret !== false ) {
				if ( $item == 'name' || $item == 'description' || $item == 'cats' ) {
					wppa_index_update( 'album', $album );
				}
				if ( $item == 'name' ) {
					wppa_create_pl_htaccess();
				}
				echo '||0||'.sprintf( __( '<b>%s</b> of album %s updated' , 'wp-photo-album-plus'), $itemname, $album );
				if ( $item == 'upload_limit' ) {
					echo '||';
					$a = wppa_allow_uploads( $album );
					if ( ! $a ) echo 'full';
					else echo 'notfull||'.$a;
				}
			}
			else {
				echo '||2||'.sprintf( __( 'An error occurred while trying to update <b>%s</b> of album %s' , 'wp-photo-album-plus'), $itemname, $album );
				echo '<br>'.__( 'Press CTRL+F5 and try again.' , 'wp-photo-album-plus');
			}
			wppa_clear_cache();
			wppa_exit();
			break;

		case 'update-comment-status':
			$photo = $_REQUEST['wppa-photo-id'];
			$nonce = $_REQUEST['wppa-nonce'];
			$comid = $_REQUEST['wppa-comment-id'];
			$comstat = $_REQUEST['wppa-comment-status'];

			// Check validity
			if ( ! wp_verify_nonce( $nonce, 'wppa_nonce_'.$photo ) ) {
				echo '||0||'.__( 'You do not have the rights to update comment status' , 'wp-photo-album-plus').$nonce;
				wppa_exit();																// Nonce check failed
			}

//			if ( wppa_switch( 'search_comments' ) ) wppa_index_remove( 'photo', $photo );
			$iret = $wpdb->query( $wpdb->prepare( 'UPDATE `'.WPPA_COMMENTS.'` SET `status` = %s WHERE `id` = %s', $comstat, $comid ) );
			if ( wppa_switch( 'search_comments' ) ) wppa_index_update( 'photo', $photo );

			if ( $iret !== false ) {
				echo '||0||'.sprintf( __( 'Status of comment #%s updated' , 'wp-photo-album-plus'), $comid );
			}
			else {
				echo '||1||'.sprintf( __( 'Error updating status comment #%s' , 'wp-photo-album-plus'), $comid );
			}
			wppa_exit();
			break;

		case 'watermark-photo':
			$photo = $_REQUEST['photo-id'];
			$nonce = $_REQUEST['wppa-nonce'];

			// Check validity
			if ( ! wp_verify_nonce( $nonce, 'wppa_nonce_'.$photo ) ) {
				echo '||1||'.__( 'You do not have the rights to change photos' , 'wp-photo-album-plus');
				wppa_exit();																// Nonce check failed
			}

			wppa_cache_thumb( $photo );
			if ( wppa_add_watermark( $photo ) ) {
				if ( wppa_switch( 'watermark_thumbs' ) ) {
					wppa_create_thumbnail( $photo );	// create new thumb
				}
				echo '||0||'.__( 'Watermark applied' , 'wp-photo-album-plus');
				wppa_exit();
			}
			else {
				echo '||1||'.__( 'An error occured while trying to apply a watermark' , 'wp-photo-album-plus');
				wppa_exit();
			}

		case 'update-photo':
			if ( strlen( $_REQUEST['photo-id'] ) == 12 ) {
				$photo = wppa_decrypt_photo( $_REQUEST['photo-id'] );
			}
			else {
				$photo = $_REQUEST['photo-id'];
			}
//			$photo = $_REQUEST['photo-id'];
			$nonce = $_REQUEST['wppa-nonce'];
			$item  = $_REQUEST['item'];
			$value = isset( $_REQUEST['value'] ) ? $_REQUEST['value'] : '';
			$value = wppa_decode( $value );

			// Check validity
			if ( ! wp_verify_nonce( $nonce, 'wppa_nonce_'.$photo ) ) {
				echo '||0||'.__( 'You do not have the rights to update photo information' , 'wp-photo-album-plus');
				wppa_exit();																// Nonce check failed
			}

			if ( substr( $item, 0, 20 ) == 'wppa_watermark_file_' || substr( $item, 0, 19 ) == 'wppa_watermark_pos_' ) {
				wppa_update_option( $item, $value );
				echo '||0||'.sprintf( __( '%s updated to %s.' , 'wp-photo-album-plus'), $item, $value );
				wppa_exit();
			}

			switch ( $item ) {
				case 'exifdtm':
						$format = '0000:00:00 00:00:00';
						$err = '0';

						// Length ok?
						if ( strlen( $value ) != 19 ) {
							$err = '1';
						}

						// Check on digits, colons and space
						for ( $i = 0; $i < 19; $i++ ) {
							$d = substr( $value, $i, 1 );
							$f = substr( $format, $i, 1 );
							switch ( $f ) {
								case '0':
									if ( ! in_array( $d, array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ) ) ) {
										$err = '2';
									}
									break;
								case ':':
								case ' ':
									if ( $d != $f ) {
										$err = '3';
									}
									break;
							}
						}

						// Check on values if format correct, report first error only
						if ( ! $err ) {
							$temp = explode( ':', str_replace( ' ', ':', $value ) );
							if ( $temp['0'] < '1970' ) 					$err = '11';	// Before UNIX epoch
							if ( ! $err && $temp['0'] > date( 'Y' ) ) 	$err = '12';	// Future
							if ( ! $err && $temp['1'] < '1' )			$err = '13'; 	// Before january
							if ( ! $err && $temp['1'] > '12' )			$err = '14';	// After december
							if ( ! $err && $temp['2'] < '1' ) 			$err = '15'; 	// Before first of month
							if ( ! $err && $temp['2'] > '31' ) 			$err = '17';	// After 31st ( forget about feb and months with 30 days )
							if ( ! $err && $temp['3'] < '1' ) 			$err = '18'; 	// Before first hour
							if ( ! $err && $temp['3'] > '24' )			$err = '19'; 	// Hour > 24
							if ( ! $err && $temp['4'] < '1' ) 			$err = '20';	// Min < 1
							if ( ! $err && $temp['4'] > '59' ) 			$err = '21';	// Min > 59
							if ( ! $err && $temp['5'] < '1' ) 			$err = '22';	// Sec < 1
							if ( ! $err && $temp['5'] > '59' ) 			$err = '23';	// Sec > 59
						}
						if ( $err ) {
							echo '||1||'.sprintf(__( 'Format error %s. Must be yyyy:mm:dd hh:mm:ss' , 'wp-photo-album-plus'), $err );
						}
						else {
							wppa_update_photo( array( 'id' => $photo, 'exifdtm' => $value ) );
							echo '||0||'.__( 'Exif date/time updated' , 'wp-photo-album-plus');
						}
						wppa_exit();
					break;
				case 'lat':
					if ( ! is_numeric( $value ) || $value < '-90.0' || $value > '90.0' ) {
						echo '||1||'.__( 'Enter a value > -90 and < 90' , 'wp-photo-album-plus');
						wppa_exit();
					}
					$photodata = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM '.WPPA_PHOTOS.' WHERE `id` = %s', $photo ), ARRAY_A );
					$geo = $photodata['location'] ? $photodata['location'] : '///';
					$geo = explode( '/', $geo );
					$geo = wppa_format_geo( $value, $geo['3'] );
					$iret = $wpdb->query( $wpdb->prepare( 'UPDATE `'.WPPA_PHOTOS.'` SET `location` = %s WHERE `id` = %s', $geo, $photo ) );
					if ( $iret ) echo '||0||'.__( 'Lattitude updated' , 'wp-photo-album-plus');
					else {
						echo '||1||'.__( 'Could not update lattitude' , 'wp-photo-album-plus');
					}
					wppa_exit();
					break;
				case 'lon':
					if ( ! is_numeric( $value ) || $value < '-180.0' || $value > '180.0' ) {
						echo '||1||'.__( 'Enter a value > -180 and < 180' , 'wp-photo-album-plus');
						wppa_exit();
					}
					$photodata = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM '.WPPA_PHOTOS.' WHERE `id` = %s', $photo ), ARRAY_A );
					$geo = $photodata['location'] ? $photodata['location'] : '///';
					$geo = explode( '/', $geo );
					$geo = wppa_format_geo( $geo['2'], $value );
					$iret = $wpdb->query( $wpdb->prepare( 'UPDATE `'.WPPA_PHOTOS.'` SET `location` = %s WHERE `id` = %s', $geo, $photo ) );
					if ( $iret ) echo '||0||'.__( 'Longitude updated' , 'wp-photo-album-plus');
					else {
						echo '||1||'.__( 'Could not update longitude' , 'wp-photo-album-plus');
					}
					wppa_exit();
					break;
				case 'remake':
					if ( wppa_remake_files( '', $photo ) ) {
						wppa_bump_photo_rev();
						wppa_bump_thumb_rev();
						echo '||0||'.__( 'Photo files remade' , 'wp-photo-album-plus');
					}
					else {
						echo '||2||'.__( 'Could not remake files' , 'wp-photo-album-plus');
					}
					wppa_exit();
					break;
				case 'remakethumb':
					if ( wppa_create_thumbnail( $photo ) ) {
						echo '||0||'.__( 'Thumbnail remade' , 'wp-photo-album-plus');
					}
					else {
						echo '||0||'.__( 'Could not remake thumbnail' , 'wp-photo-album-plus');
					}
					wppa_exit();
					break;
				case 'rotright':
				case 'rot180':
				case 'rotleft':
					switch ( $item ) {
						case 'rotleft':
							$angle = '90';
							$dir = __( 'left' , 'wp-photo-album-plus');
							break;
						case 'rot180':
							$angle = '180';
							$dir = __( '180&deg;' , 'wp-photo-album-plus');
							break;
						case 'rotright':
							$angle = '270';
							$dir = __( 'right' , 'wp-photo-album-plus');
							break;
					}
					wppa( 'error', wppa_rotate( $photo, $angle ) );
					if ( ! wppa( 'error' ) ) {
						wppa_update_modified( $photo );
						wppa_bump_photo_rev();
						wppa_bump_thumb_rev();
						echo '||0||'.sprintf( __( 'Photo %s rotated %s' , 'wp-photo-album-plus'), $photo, $dir );
					}
					else {
						echo '||'.wppa( 'error' ).'||'.sprintf( __( 'An error occurred while trying to rotate photo %s' , 'wp-photo-album-plus'), $photo );
					}
					wppa_exit();
					break;

				case 'moveto':
					$photodata = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM '.WPPA_PHOTOS.' WHERE `id` = %s', $photo ), ARRAY_A );
					if ( wppa_switch( 'void_dups' ) ) {	// Check for already exists
						$exists = wppa_file_is_in_album( $photodata['filename'], $value );
						if ( $exists ) {	// Already exists
							echo '||3||' . sprintf ( __( 'A photo with filename %s already exists in album %s.' , 'wp-photo-album-plus'), $photodata['filename'], $value );
							wppa_exit();
							break;
						}
					}
					wppa_flush_treecounts( $photodata['album'] );	// Current album
					wppa_flush_treecounts( $value );				// New album
					$iret = $wpdb->query( $wpdb->prepare( 'UPDATE '.WPPA_PHOTOS.' SET `album` = %s WHERE `id` = %s', $value, $photo ) );
					if ( $iret !== false ) {
						wppa_move_source( $photodata['filename'], $photodata['album'], $value );
						echo '||99||'.sprintf( __( 'Photo %s has been moved to album %s (%s)' , 'wp-photo-album-plus'), $photo, wppa_get_album_name( $value ), $value );
					}
					else {
						echo '||3||'.sprintf( __( 'An error occurred while trying to move photo %s' , 'wp-photo-album-plus'), $photo );
					}
					wppa_exit();
					break;

				case 'copyto':
					$photodata = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM '.WPPA_PHOTOS.' WHERE `id` = %s', $photo ), ARRAY_A );
					if ( wppa_switch( 'void_dups' ) ) {	// Check for already exists
						$exists = wppa_file_is_in_album( $photodata['filename'], $value );
						if ( $exists ) {	// Already exists
							echo '||4||' . sprintf ( __( 'A photo with filename %s already exists in album %s.' , 'wp-photo-album-plus'), $photodata['filename'], $value );
							wppa_exit();
							break;
						}
					}
					wppa( 'error', wppa_copy_photo( $photo, $value ) );
					wppa_flush_treecounts( $value );				// New album
					if ( ! wppa( 'error' ) ) {
						echo '||0||'.sprintf( __( 'Photo %s copied to album %s (%s)' , 'wp-photo-album-plus'), $photo, wppa_get_album_name( $value ), $value );
					}
					else {
						echo '||4||'.sprintf( __( 'An error occurred while trying to copy photo %s' , 'wp-photo-album-plus'), $photo );
						echo '<br>'.__( 'Press CTRL+F5 and try again.' , 'wp-photo-album-plus');
					}
					wppa_exit();
					break;

				case 'status':
				if ( ! current_user_can( 'wppa_moderate' ) && ! current_user_can( 'wppa_admin' ) ) die( 'Security check failure #78' );
					wppa_flush_treecounts( wppa_get_photo_item( $photo, 'album' ) ); // $wpdb->get_var( $wpdb->prepare( "SELECT `album` FROM `".WPPA_PHOTOS."` WHERE `id` = %s", $photo ) ) );
				case 'owner':
				case 'name':
				case 'description':
				case 'p_order':
				case 'linkurl':
				case 'linktitle':
				case 'linktarget':
				case 'tags':
				case 'alt':
				case 'videox':
				case 'videoy':
					switch ( $item ) {
						case 'name':
							$value = strip_tags( $value );
							$itemname = __( 'Name' , 'wp-photo-album-plus');
							break;
						case 'description':
							$itemname = __( 'Description' , 'wp-photo-album-plus');
							if ( wppa_switch( 'check_balance' ) ) {
								$value = str_replace( array( '<br/>','<br>' ), '<br />', $value );
								if ( balanceTags( $value, true ) != $value ) {
									echo '||3||'.__( 'Unbalanced tags in photo description!' , 'wp-photo-album-plus');
									wppa_exit();
								}
							}
							break;
						case 'p_order':
							$itemname = __( 'Photo order #' , 'wp-photo-album-plus');
							break;
						case 'owner':
							$usr = get_user_by( 'login', $value );
							if ( ! $usr ) {
								echo '||4||' . sprintf( __( 'User %s does not exists' , 'wp-photo-album-plus'), $value );
								wppa_exit();
							}
							$value = $usr->user_login;	// Correct possible case mismatch
							wppa_flush_upldr_cache( 'photoid', $photo ); 		// Current owner
							wppa_flush_upldr_cache( 'username', $value );		// New owner
							$itemname = __( 'Owner' , 'wp-photo-album-plus');
							break;
						case 'linkurl':
							$itemname = __( 'Link url' , 'wp-photo-album-plus');
							break;
						case 'linktitle':
							$itemname = __( 'Link title' , 'wp-photo-album-plus');
							break;
						case 'linktarget':
							$itemname = __( 'Link target' , 'wp-photo-album-plus');
							break;
						case 'tags':
							$value = wppa_sanitize_tags( $value, false, true );
							$value = wppa_sanitize_tags( wppa_filter_iptc( wppa_filter_exif( $value, $photo ), $photo ) );
							wppa_clear_taglist();
							$itemname = __( 'Photo Tags' , 'wp-photo-album-plus');
							break;
						case 'status':
							wppa_clear_taglist();
							wppa_flush_upldr_cache( 'photoid', $photo );
							$itemname = __( 'Status' , 'wp-photo-album-plus');
							break;
						case 'alt':
							$itemname = __( 'HTML Alt' , 'wp-photo-album-plus');
							$value = strip_tags( stripslashes( $value ) );
							break;
						case 'videox':
							$itemname = __( 'Video width' , 'wp-photo-album-plus');
							if ( ! wppa_is_int( $value ) || $value < '0' ) {
								echo '||3||'.__( 'Please enter an integer value >= 0' , 'wp-photo-album-plus');
								wppa_exit();
							}
							break;
						case 'videoy':
							$itemname = __( 'Video height', 'wp-photo-album-plus');
							if ( ! wppa_is_int( $value ) || $value < '0' ) {
								echo '||3||'.__( 'Please enter an integer value >= 0' , 'wp-photo-album-plus');
								wppa_exit();
							}
							break;

						default:
							$itemname = $item;
					}
	//				if ( $item == 'name' || $item == 'description' || $item == 'tags' ) wppa_index_quick_remove( 'photo', $photo );
					$iret = $wpdb->query( $wpdb->prepare( 'UPDATE '.WPPA_PHOTOS.' SET `'.$item.'` = %s WHERE `id` = %s', $value, $photo ) );
					if ( $item == 'name' || $item == 'description' || $item == 'tags' )  wppa_index_update( 'photo', $photo );
					if ( $item == 'status' && $value != 'scheduled' ) wppa_update_photo( array( 'id' => $photo, 'scheduledtm' => '' ) );
					if ( $item == 'status' ) wppa_flush_treecounts( wppa_get_photo_item( $photo, 'album' ) );
					if ( $iret !== false ) {
						wppa_update_modified( $photo );
						if ( wppa_is_video( $photo ) ) {
							echo '||0||'.sprintf( __( '<b>%s</b> of video %s updated' , 'wp-photo-album-plus'), $itemname, $photo );
						}
						else {
							echo '||0||'.sprintf( __( '<b>%s</b> of photo %s updated' , 'wp-photo-album-plus'), $itemname, $photo );
						}
					}
					else {
						echo '||2||'.sprintf( __( 'An error occurred while trying to update <b>%s</b> of photo %s' , 'wp-photo-album-plus'), $itemname, $photo );
						echo '<br>'.__( 'Press CTRL+F5 and try again.' , 'wp-photo-album-plus');
						wppa_exit();
					}
					break;

				case 'year':
				case 'month':
				case 'day':
				case 'hour':
				case 'min':
					$itemname = __( 'Schedule date/time' , 'wp-photo-album-plus');
					$scheduledtm = $wpdb->get_var( $wpdb->prepare( "SELECT `scheduledtm` FROM`".WPPA_PHOTOS."` WHERE `id` = %s", $photo ) );
					if ( ! $scheduledtm ) {
						$scheduledtm = wppa_get_default_scheduledtm();
					}
					$temp = explode( ',', $scheduledtm );
					if ( $item == 'year' ) 	$temp[0] = $value;
					if ( $item == 'month' ) $temp[1] = $value;
					if ( $item == 'day' ) 	$temp[2] = $value;
					if ( $item == 'hour' ) 	$temp[3] = $value;
					if ( $item == 'min' ) 	$temp[4] = $value;
					$scheduledtm = implode( ',', $temp );
					wppa_update_photo( array( 'id' => $photo, 'scheduledtm' => $scheduledtm, 'status' => 'scheduled' ) );
					wppa_flush_treecounts( $wpdb->get_var( $wpdb->prepare( "SELECT `album` FROM `".WPPA_PHOTOS."` WHERE `id` = %s", $photo ) ) );
					wppa_flush_upldr_cache( 'photoid', $photo );
					if ( wppa_is_video( $photo ) ) {
						echo '||0||'.sprintf( __( '<b>%s</b> of video %s updated' , 'wp-photo-album-plus'), $itemname, $photo );
					}
					else {
						echo '||0||'.sprintf( __( '<b>%s</b> of photo %s updated' , 'wp-photo-album-plus'), $itemname, $photo );
					}
					break;

				case 'custom_0':
				case 'custom_1':
				case 'custom_2':
				case 'custom_3':
				case 'custom_4':
				case 'custom_5':
				case 'custom_6':
				case 'custom_7':
				case 'custom_8':
				case 'custom_9':
					$index 		= substr( $item, -1 );
					$custom 	= wppa_get_photo_item( $photo, 'custom' );
					if ( $custom ) {
						$custom_data = unserialize( $custom );
					}
					else {
						$custom_data = array( '', '', '', '', '', '', '', '', '', '' );
					}
					$custom_data[$index] = wppa_sanitize_custom_field( $value );
					$custom = serialize( $custom_data );
					wppa_update_photo( array( 'id' => $photo, 'custom' => $custom, 'modified' => time() ) );
					wppa_index_update( 'photo', $photo );
					echo '||0||'.sprintf( __( '<b>Custom field %s</b> of photo %s updated' , 'wp-photo-album-plus'), wppa_opt( 'custom_caption_'.$index ), $photo );
					break;

				case 'file':

					// Check on upload error
					if ( $_FILES['photo']['error'] ) {
						echo '||'.$_FILES['photo']['error'].'||'.__( '<b>Error during upload.</b>', 'wp-photo-album-plus');
						wppa_exit();
					}

					// Save new source
					wppa_save_source( $_FILES['photo']['tmp_name'], wppa_get_photo_item( $photo, 'filename' ), wppa_get_photo_item( $photo, 'album') );

					// Make the files
					$bret = wppa_make_the_photo_files( $_FILES['photo']['tmp_name'], $photo, strtolower( wppa_get_ext( $_FILES['photo']['name'] ) ) );
					if ( $bret ) {

						// Update timestamps and sizes
						$alb = wppa_get_photo_item( $photo, 'album' );
						wppa_update_album( array( 	'id' => $alb,
													'modified' => time(),
													) );
						wppa_update_photo( array( 	'id' => $photo,
													'modified' => time(),
													'thumbx' => '0',
													'thumby' => '0',
													'photox' => '0',
													'photoy' => '0',
													) );

						// Report success
						echo '||0||'.__( 'Photo files updated.' , 'wp-photo-album-plus');
					}
					else {

						// Report fail
						echo '||1||'.__( 'Could not update files.' , 'wp-photo-album-plus');
					}
					wppa_exit();
					break;

				case 'stereo':
					$t = microtime(true);
					wppa_update_photo( array( 'id' => $photo, 'stereo' => $value ) );
					wppa_create_stereo_images( $photo );
					wppa_create_thumbnail( $photo );
					$t = microtime(true) - $t;
					echo '||0||' . sprintf( __( 'Stereo mode updated in %d milliseconds', 'wp-photo-album-plus'), floor( $t * 1000 ) );
					wppa_exit();
					break;

				default:
					echo '||98||This update action is not implemented yet( '.$item.' )';
					wppa_exit();
			}
			wppa_clear_cache();
			break;

		// The wppa-settings page calls ajax with $wppa_action == 'update-option';
		case 'update-option':
			// Verify that we are legally here
			$nonce  = $_REQUEST['wppa-nonce'];
			if ( ! wp_verify_nonce( $nonce, 'wppa-nonce' ) ) {
				echo '||1||'.__( 'You do not have the rights to update settings' , 'wp-photo-album-plus');
				wppa_exit();																// Nonce check failed
			}

			// Initialize
			$old_minisize = wppa_get_minisize();		// Remember for later, maybe we do something that requires regen
			$option = $_REQUEST['wppa-option'];			// The option to be processed
			$value  = isset( $_REQUEST['value'] ) ? wppa_decode( $_REQUEST['value'] ) : '';	// The new value, may also contain & # and +
			$value  = stripslashes( $value );
			$value 	= trim( $value ); 					// Remaove surrounding spaces
			$alert  = '';			// Init the return string data
			wppa( 'error', '0' );	//
			$title  = '';			//

			// If it is a font family, change all double quotes into single quotes as this destroys much more than you would like
			if ( strpos( $option, 'wppa_fontfamily_' ) !== false ) $value = str_replace( '"', "'", $value );

			$option = wppa_decode( $option );
			// Dispatch on option
			if ( substr( $option, 0, 16 ) == 'wppa_iptc_label_' ) {
				$tag = substr( $option, 16 );
				$q = $wpdb->prepare( "UPDATE `".WPPA_IPTC."` SET `description`=%s WHERE `tag`=%s AND `photo`='0'", $value, $tag );
				$bret = $wpdb->query( $q );
				// Produce the response text
				if ( $bret ) {
					$output = '||0||'.$tag.' updated to '.$value.'||';
				}
				else {
					$output = '||1||Failed to update '.$tag.'||';
				}
				echo $output;
				wppa_exit();
			}
			elseif ( substr( $option, 0, 17 ) == 'wppa_iptc_status_' ) {
				$tag = substr( $option, 17 );
				$q = $wpdb->prepare( "UPDATE `".WPPA_IPTC."` SET `status`=%s WHERE `tag`=%s AND `photo`='0'", $value, $tag );
				$bret = $wpdb->query( $q );
				// Produce the response text
				if ( $bret ) {
					$output = '||0||'.$tag.' updated to '.$value.'||';
				}
				else {
					$output = '||1||Failed to update '.$tag.'||';
				}
				echo $output;
				wppa_exit();
			}
			elseif ( substr( $option, 0, 16 ) == 'wppa_exif_label_' ) {
				$tag = substr( $option, 16 );
				$q = $wpdb->prepare( "UPDATE `".WPPA_EXIF."` SET `description`=%s WHERE `tag`=%s AND `photo`='0'", $value, $tag );
				$bret = $wpdb->query( $q );
				// Produce the response text
				if ( $bret ) {
					$output = '||0||'.$tag.' updated to '.$value.'||';
				}
				else {
					$output = '||1||Failed to update '.$tag.'||';
				}
				echo $output;
				wppa_exit();
			}
			elseif ( substr( $option, 0, 17 ) == 'wppa_exif_status_' ) {
				$tag = substr( $option, 17 );
				$q = $wpdb->prepare( "UPDATE `".WPPA_EXIF."` SET `status`=%s WHERE `tag`=%s AND `photo`='0'", $value, $tag );
				$bret = $wpdb->query( $q );
				// Produce the response text
				if ( $bret ) {
					$output = '||0||'.$tag.' updated to '.$value.'||';
				}
				else {
					$output = '||1||Failed to update '.$tag.'||';
				}
				echo $output;
				wppa_exit();
			}
			elseif ( substr( $option, 0, 5 ) == 'caps-' ) {	// Is capability setting
				global $wp_roles;
				//$R = new WP_Roles;
				$setting = explode( '-', $option );
				if ( $value == 'yes' ) {
					$wp_roles->add_cap( $setting[2], $setting[1] );
					echo '||0||'.__( 'Capability granted' , 'wp-photo-album-plus').'||';
					wppa_exit();
				}
				elseif ( $value == 'no' ) {
					$wp_roles->remove_cap( $setting[2], $setting[1] );
					echo '||0||'.__( 'Capability withdrawn' , 'wp-photo-album-plus').'||';
					wppa_exit();
				}
				else {
					echo '||1||Invalid value: '.$value.'||';
					wppa_exit();
				}
			}
			else switch ( $option ) {

				case 'wppa_colwidth': //	 ??	  fixed   low	high	title
					wppa_ajax_check_range( $value, 'auto', '100', false, __( 'Column width.' , 'wp-photo-album-plus') );
					break;
				case 'wppa_initial_colwidth':
					wppa_ajax_check_range( $value, false, '100', false, __( 'Initial width.' , 'wp-photo-album-plus') );
					break;
				case 'wppa_fullsize':
					wppa_ajax_check_range( $value, false, '100', false, __( 'Full size.' , 'wp-photo-album-plus') );
					break;
				case 'wppa_maxheight':
					wppa_ajax_check_range( $value, false, '100', false, __( 'Max height.' , 'wp-photo-album-plus') );
					break;
				case 'wppa_film_thumbsize':
				case 'wppa_thumbsize':
				case 'wppa_thumbsize_alt':
					wppa_ajax_check_range( $value, false, '50', false, __( 'Thumbnail size.' , 'wp-photo-album-plus') );
					break;
				case 'wppa_tf_width':
				case 'wppa_tf_width_alt':
					wppa_ajax_check_range( $value, false, '50', false, __( 'Thumbnail frame width' , 'wp-photo-album-plus') );
					break;
				case 'wppa_tf_height':
				case 'wppa_tf_height_alt':
					wppa_ajax_check_range( $value, false, '50',false,  __( 'Thumbnail frame height' , 'wp-photo-album-plus') );
					break;
				case 'wppa_tn_margin':
					wppa_ajax_check_range( $value, false, '0', false, __( 'Thumbnail Spacing' , 'wp-photo-album-plus') );
					break;
				case 'wppa_min_thumbs':
					wppa_ajax_check_range( $value, false, '0', false, __( 'Photocount treshold.' , 'wp-photo-album-plus') );
					break;
				case 'wppa_thumb_page_size':
					wppa_ajax_check_range( $value, false, '0', false, __( 'Thumb page size.' , 'wp-photo-album-plus') );
					break;
				case 'wppa_smallsize':
					wppa_ajax_check_range( $value, false, '50', false, __( 'Cover photo size.' , 'wp-photo-album-plus') );
					break;
				case 'wppa_album_page_size':
					wppa_ajax_check_range( $value, false, '0', false, __( 'Album page size.' , 'wp-photo-album-plus') );
					break;
				case 'wppa_topten_count':
					wppa_ajax_check_range( $value, false, '2', false, __( 'Number of TopTen photos' , 'wp-photo-album-plus'), '40' );
					break;
				case 'wppa_topten_size':
					wppa_ajax_check_range( $value, false, '32', false, __( 'Widget image thumbnail size' , 'wp-photo-album-plus'), wppa_get_minisize() );
					break;
				case 'wppa_max_cover_width':
					wppa_ajax_check_range( $value, false, '150', false, __( 'Max Cover width' , 'wp-photo-album-plus') );
					break;
				case 'wppa_text_frame_height':
					wppa_ajax_check_range( $value, false, '0', false, __( 'Minimal description height' , 'wp-photo-album-plus') );
					break;
				case 'wppa_cover_minheight':
					wppa_ajax_check_range( $value, false, '0', false, __( 'Minimal cover height' , 'wp-photo-album-plus') );
					break;
				case 'wppa_head_and_text_frame_height':
					wppa_ajax_check_range( $value, false, '0', false, __( 'Minimal text frame height' , 'wp-photo-album-plus') );
					break;
				case 'wppa_bwidth':
					wppa_ajax_check_range( $value, '', '0', false, __( 'Border width' , 'wp-photo-album-plus') );
					break;
				case 'wppa_bradius':
					wppa_ajax_check_range( $value, '', '0', false, __( 'Border radius' , 'wp-photo-album-plus') );
					break;
				case 'wppa_box_spacing':
					wppa_ajax_check_range( $value, '', '-20', '100', __( 'Box spacing' , 'wp-photo-album-plus') );
					break;
				case 'wppa_popupsize':
					$floor = wppa_opt( 'thumbsize' );
					$temp  = wppa_opt( 'smallsize' );
					if ( $temp > $floor ) $floor = $temp;
					wppa_ajax_check_range( $value, false, $floor, wppa_opt( 'fullsize' ), __( 'Popup size' , 'wp-photo-album-plus') );
					break;
				case 'wppa_fullimage_border_width':
					wppa_ajax_check_range( $value, '', '0', false, __( 'Fullsize border width' , 'wp-photo-album-plus') );
					break;
				case 'wppa_lightbox_bordersize':
					wppa_ajax_check_range( $value, false, '0', false, __( 'Lightbox Bordersize' , 'wp-photo-album-plus') );
					break;
				case 'wppa_ovl_border_width':
					wppa_ajax_check_range( $value, false, '0', '16', __( 'Lightbox Borderwidth' , 'wp-photo-album-plus') );
					break;
				case 'wppa_ovl_border_radius':
					wppa_ajax_check_range( $value, false, '0', '16', __( 'Lightbox Borderradius' , 'wp-photo-album-plus') );
					break;
				case 'wppa_comment_count':
					wppa_ajax_check_range( $value, false, '2', '40', __( 'Number of Comment widget entries' , 'wp-photo-album-plus') );
					break;
				case 'wppa_comment_size':
					wppa_ajax_check_range( $value, false, '32', wppa_get_minisize(), __( 'Comment Widget image thumbnail size' , 'wp-photo-album-plus'), wppa_get_minisize() );
					break;
				case 'wppa_thumb_opacity':
					wppa_ajax_check_range( $value, false, '0', '100', __( 'Opacity.' , 'wp-photo-album-plus') );
					break;
				case 'wppa_cover_opacity':
					wppa_ajax_check_range( $value, false, '0', '100', __( 'Opacity.' , 'wp-photo-album-plus') );
					break;
				case 'wppa_star_opacity':
					wppa_ajax_check_range( $value, false, '0', '50', __( 'Opacity.' , 'wp-photo-album-plus') );
					break;
//				case 'wppa_filter_priority':
//					wppa_ajax_check_range( $value, false, wppa_opt( 'shortcode_priority' ), false, __( 'Filter priority' ,'wp-photo-album-plus' ) );
//					break;
//				case 'wppa_shortcode_priority':
//					wppa_ajax_check_range( $value, false, '0', wppa_opt( 'filter_priority' ) - '1', __( 'Shortcode_priority', 'wp-photo-album-plus' ) );
//					break;
				case 'wppa_gravatar_size':
					wppa_ajax_check_range( $value, false, '10', '256', __( 'Avatar size' , 'wp-photo-album-plus') );
					break;
				case 'wppa_watermark_opacity':
					wppa_ajax_check_range( $value, false, '0', '100', __( 'Watermark opacity' , 'wp-photo-album-plus') );
					break;
				case 'wppa_watermark_opacity_text':
					wppa_ajax_check_range( $value, false, '0', '100', __( 'Watermark opacity' , 'wp-photo-album-plus') );
					break;
				case 'wppa_ovl_txt_lines':
					wppa_ajax_check_range( $value, 'auto', '0', '24', __( 'Number of text lines' , 'wp-photo-album-plus') );
					break;
				case 'wppa_ovl_opacity':
					wppa_ajax_check_range( $value, false, '0', '100', __( 'Overlay opacity' , 'wp-photo-album-plus') );
					break;
				case 'wppa_upload_limit_count':
					wppa_ajax_check_range( $value, false, '0', false, __( 'Upload limit' , 'wp-photo-album-plus') );
					break;
				case 'wppa_dislike_mail_every':
					wppa_ajax_check_range( $value, false, '0', false, __( 'Notify inappropriate' , 'wp-photo-album-plus') );
					break;
				case 'wppa_dislike_set_pending':
					wppa_ajax_check_range( $value, false, '0', false, __( 'Dislike pending' , 'wp-photo-album-plus') );
					break;
				case 'wppa_dislike_delete':
					wppa_ajax_check_range( $value, false, '0', false, __( 'Dislike delete' , 'wp-photo-album-plus') );
					break;
				case 'wppa_max_execution_time':
					wppa_ajax_check_range( $value, false, '0', '900', __( 'Max execution time' , 'wp-photo-album-plus') );
					break;
				case 'wppa_cp_points_comment':
				case 'wppa_cp_points_rating':
				case 'wppa_cp_points_upload':
					wppa_ajax_check_range( $value, false, '0', false, __( 'Cube Points points' , 'wp-photo-album-plus') );
					break;
				case 'wppa_jpeg_quality':
					wppa_ajax_check_range( $value, false, '20', '100', __( 'JPG Image quality' , 'wp-photo-album-plus') );
					if ( wppa_cdn( 'admin' ) == 'cloudinary' && ! wppa( 'out' ) ) {
						wppa_delete_derived_from_cloudinary();
					}
					break;
				case 'wppa_imgfact_count':
					wppa_ajax_check_range( $value, false, '1', '24', __( 'Number of coverphotos' , 'wp-photo-album-plus') );
					break;
				case 'wppa_dislike_value':
					wppa_ajax_check_range( $value, false, '-10', '0', __( 'Dislike value' , 'wp-photo-album-plus') );
					break;
				case 'wppa_slideshow_pagesize':
					wppa_ajax_check_range( $value, false, '0', false, __( 'Slideshow pagesize' , 'wp-photo-album-plus') );
					break;
				case 'wppa_slideonly_max':
					wppa_ajax_check_range( $value, false, '0', false, __( 'Slideonly max', 'wp-photo-album-plus') );
					break;
				case 'wppa_pagelinks_max':
					wppa_ajax_check_range( $value, false, '0', false, __( 'Max Pagelinks' , 'wp-photo-album-plus') );
					break;
				case 'wppa_start_pause_symbol_size':
					wppa_ajax_check_range( $value, false, '0', false, __('Start/pause symbol size', 'wp-photo-album-plus') );
					break;
				case 'wppa_start_pause_symbol_bradius':
					wppa_ajax_check_range( $value, false, '0', false, __('Start/pause symbol border radius', 'wp-photo-album-plus') );
					break;
				case 'wppa_stop_symbol_size':
					wppa_ajax_check_range( $value, false, '0', false, __('Stop symbol size', 'wp-photo-album-plus') );
					break;
				case 'wppa_stop_symbol_bradius':
					wppa_ajax_check_range( $value, false, '0', false, __('Stop symbol border radius', 'wp-photo-album-plus') );
					break;

				case 'wppa_rating_clear':
					$iret1 = $wpdb->query( 'TRUNCATE TABLE '.WPPA_RATING );
					$iret2 = $wpdb->query( 'UPDATE '.WPPA_PHOTOS.' SET mean_rating="0", rating_count="0" WHERE id > -1' );
					if ( $iret1 !== false && $iret2 !== false ) {
						delete_option( 'wppa_'.WPPA_RATING.'_lastkey' );
						$title = __( 'Ratings cleared' , 'wp-photo-album-plus');
					}
					else {
						$title = __( 'Could not clear ratings' , 'wp-photo-album-plus');
						$alert = $title;
						wppa( 'error', '1' );
					}
					break;
				case 'wppa_viewcount_clear':
					$iret = $wpdb->query( "UPDATE `".WPPA_PHOTOS."` SET `views` = '0'" ) &&
							$wpdb->query( "UPDATE `".WPPA_ALBUMS."` SET `views` = '0'" );
					if ( $iret !== false ) {
						$title = __( 'Viewcounts cleared' , 'wp-photo-album-plus');
					}
					else {
						$title = __( 'Could not clear viewcounts' , 'wp-photo-album-plus');
						$alert = $title;
						wppa( 'error', '1' );
					}
					break;

				case 'wppa_iptc_clear':
					$iret = $wpdb->query( 'TRUNCATE TABLE '.WPPA_IPTC );
					if ( $iret !== false ) {
						delete_option( 'wppa_'.WPPA_IPTC.'_lastkey' );
						$title = __( 'IPTC data cleared' , 'wp-photo-album-plus');
						$alert = __( 'Refresh this page to clear table X' , 'wp-photo-album-plus');
						update_option( 'wppa_index_need_remake', 'yes' );
					}
					else {
						$title = __( 'Could not clear IPTC data' , 'wp-photo-album-plus');
						$alert = $title;
						wppa( 'error', '1' );
					}
					break;

				case 'wppa_exif_clear':
					$iret = $wpdb->query( 'TRUNCATE TABLE '.WPPA_EXIF );
					if ( $iret !== false ) {
						delete_option( 'wppa_'.WPPA_EXIF.'_lastkey' );
						$title = __( 'EXIF data cleared' , 'wp-photo-album-plus');
						$alert = __( 'Refresh this page to clear table XI' , 'wp-photo-album-plus');
						update_option( 'wppa_index_need_remake', 'yes' );
					}
					else {
						$title = __( 'Could not clear EXIF data' , 'wp-photo-album-plus');
						$alert = $title;
						wppa( 'error', '1' );
					}
					break;

				case 'wppa_recup':
					$result = wppa_recuperate_iptc_exif();
					echo '||0||'.__( 'Recuperation performed' , 'wp-photo-album-plus').'||'.$result;
					wppa_exit();
					break;

				case 'wppa_bgcolor_thumbnail':
					$value = trim( strtolower( $value ) );
					if ( strlen( $value ) != '7' || substr( $value, 0, 1 ) != '#' ) {
						wppa( 'error', '1' );
					}
					else for ( $i=1; $i<7; $i++ ) {
						if ( ! in_array( substr( $value, $i, 1 ), array( '0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f' ) ) ) {
							wppa( 'error', '1' );
						}
					}
					if ( ! wppa( 'error' ) ) $old_minisize--;	// Trigger regen message
					else $alert = __( 'Illegal format. Please enter a 6 digit hexadecimal color value. Example: #77bbff' , 'wp-photo-album-plus');
					break;

				case 'wppa_thumb_aspect':
					$old_minisize--;	// Trigger regen message
					break;

				case 'wppa_rating_max':
					if ( $value == '5' && wppa_opt( 'rating_max' ) == '10' ) {
						$rats = $wpdb->get_results( 'SELECT `id`, `value` FROM `'.WPPA_RATING.'`', ARRAY_A );
						if ( $rats ) {
							foreach ( $rats as $rat ) {
								$wpdb->query( $wpdb->prepare( 'UPDATE `'.WPPA_RATING.'` SET `value` = %s WHERE `id` = %s', $rat['value']/2, $rat['id'] ) );
							}
						}
					}
					if ( $value == '10' && wppa_opt( 'rating_max' ) == '5' ) {
						$rats = $wpdb->get_results( 'SELECT `id`, `value` FROM `'.WPPA_RATING.'`', ARRAY_A );
						if ( $rats ) {
							foreach ( $rats as $rat ) {
								$wpdb->query( $wpdb->prepare( 'UPDATE `'.WPPA_RATING.'` SET `value` = %s WHERE `id` = %s', $rat['value']*2, $rat['id'] ) );
							}
						}
					}

					update_option ( 'wppa_rerate_status', 'Required' );
					$alert .= __( 'You just changed a setting that requires the recalculation of ratings.' , 'wp-photo-album-plus');
					$alert .= ' '.__( 'Please run the appropriate action in Table VIII.' , 'wp-photo-album-plus');

					wppa_update_option( $option, $value );
					wppa( 'error', '0' );
					break;

				case 'wppa_newphoto_description':
					if ( wppa_switch( 'check_balance' ) && balanceTags( $value, true ) != $value ) {
						$alert = __( 'Unbalanced tags in photo description!' , 'wp-photo-album-plus');
						wppa( 'error', '1' );
					}
					else {
						wppa_update_option( $option, $value );
						wppa( 'error', '0' );
						$alert = '';
						wppa_index_compute_skips();
					}
					break;

				case 'wppa_keep_source':
					$dir = wppa_opt( 'source_dir' );
					if ( ! is_dir( $dir ) ) @ mkdir( $dir );
					if ( ! is_dir( $dir ) || ! is_writable( $dir ) ) {
						wppa( 'error', '1' );
						$alert = sprintf( __( 'Unable to create or write to %s' , 'wp-photo-album-plus'), $dir );
					}
					break;

				case 'wppa_source_dir':
					$olddir = wppa_opt( 'source_dir' );
					$value = rtrim( $value, '/' );
					if ( strpos( $value.'/', WPPA_UPLOAD_PATH.'/' ) !== false ) {
						wppa( 'error', '1' );
						$alert = sprintf( __( 'Source can not be inside the wppa folder.' , 'wp-photo-album-plus') );
					}
					else {
						$dir = $value;
						if ( ! is_dir( $dir ) ) @ mkdir( $dir );
						if ( ! is_dir( $dir ) || ! is_writable( $dir ) ) {
							wppa( 'error', '1' );
							$alert = sprintf( __( 'Unable to create or write to %s' , 'wp-photo-album-plus'), $dir );
						}
						else {
							@ rmdir( $olddir ); 	// try to remove when empty
						}
					}
					break;

				case 'wppa_newpag_content':
					if ( strpos( $value, 'w#album' ) === false ) {
						$alert = __( 'The content must contain w#album' , 'wp-photo-album-plus');
						wppa( 'error', '1' );
					}
					break;

				case 'wppa_gpx_shortcode':
					if ( strpos( $value, 'w#lat' ) === false || strpos( $value, 'w#lon' ) === false ) {
						$alert = __( 'The content must contain w#lat and w#lon' , 'wp-photo-album-plus');
						wppa( 'error', '1' );
					}
					break;

				case 'wppa_i_responsive':
					if ( $value == 'yes' ) { wppa_update_option( 'wppa_colwidth', 'auto' ); }
					if ( $value == 'no' ) { wppa_update_option( 'wppa_colwidth', '640' ); }
					break;

				case 'wppa_i_downsize':
					if ( $value == 'yes' ) {
						wppa_update_option( 'wppa_resize_on_upload', 'yes' );
						if ( wppa_opt( 'resize_to' ) == '0' ) wppa_update_option( 'wppa_resize_to', '1024x768' );
					}
					if ( $value == 'no' ) {
						wppa_update_option( 'wppa_resize_on_upload', 'no' );
					}
					break;

				case 'wppa_i_source':
					if ( $value == 'yes' ) {
						wppa_update_option( 'wppa_keep_source_admin', 'yes' );
						wppa_update_option( 'wppa_keep_source_frontend', 'yes' );
					}
					if ( $value == 'no' ) {
						wppa_update_option( 'wppa_keep_source_admin', 'no' );
						wppa_update_option( 'wppa_keep_source_frontend', 'no' );
					}
					break;

				case 'wppa_i_userupload':
					if ( $value == 'yes' ) {
						wppa_update_option( 'wppa_user_upload_on', 'yes' );
						wppa_update_option( 'wppa_user_upload_login', 'yes' );
						wppa_update_option( 'wppa_owner_only', 'yes' );
						wppa_update_option( 'wppa_upload_moderate', 'yes' );
						wppa_update_option( 'wppa_upload_edit', 'yes' );
						wppa_update_option( 'wppa_upload_notify', 'yes' );
						wppa_update_option( 'wppa_grant_an_album', 'yes' );
						$grantparent = wppa_opt( 'grant_parent' );
						if ( ! wppa_album_exists( $grantparent ) ) {
							$id = wppa_create_album_entry( array( 'name' => __( 'Members' , 'wp-photo-album-plus'), 'description' => __( 'Parent of the member albums' , 'wp-photo-album-plus'), 'a_parent' => '-1', 'upload_limit' => '0/0' ) );
							if ( $id ) {
								wppa_index_add( 'album', $id );
								wppa_update_option( 'wppa_grant_parent', $id );
							}
							$my_post = array(
								'post_title'    => __( 'Members' , 'wp-photo-album-plus'),
								'post_content'  => '[wppa type="content" album="'.$id.'"][/wppa]',
								'post_status'   => 'publish',
								'post_type'	  	=> 'page'
								 );
							$pagid = wp_insert_post( $my_post );
						}
						wppa_update_option( 'wppa_alt_is_restricted', 'yes' );
						wppa_update_option( 'wppa_link_is_restricted', 'yes' );
						wppa_update_option( 'wppa_covertype_is_restricted', 'yes' );
						wppa_update_option( 'wppa_porder_restricted', 'yes' );
					}
					if ( $value == 'no' ) {
						wppa_update_option( 'wppa_user_upload_on', 'no' );
					}
					break;

				case 'wppa_i_rating':
					if ( $value == 'yes' ) {
						wppa_update_option( 'wppa_rating_on', 'yes' );
					}
					if ( $value == 'no' ) {
						wppa_update_option( 'wppa_rating_on', 'no' );
					}
					break;

				case 'wppa_i_comment':
					if ( $value == 'yes' ) {
						wppa_update_option( 'wppa_show_comments', 'yes' );
						wppa_update_option( 'wppa_comment_moderation', 'all' );
						wppa_update_option( 'wppa_comment_notify', 'admin' );
					}
					if ( $value == 'no' ) {
						wppa_update_option( 'wppa_show_comments', 'no' );
					}
					break;

				case 'wppa_i_share':
					if ( $value == 'yes' ) {
						wppa_update_option( 'wppa_share_on', 'yes' );
					}
					if ( $value == 'no' ) {
						wppa_update_option( 'wppa_share_on', 'no' );
					}
					break;

				case 'wppa_i_iptc':
					if ( $value == 'yes' ) {
						wppa_update_option( 'wppa_show_iptc', 'yes' );
						wppa_update_option( 'wppa_save_iptc', 'yes' );
					}
					if ( $value == 'no' ) {
						wppa_update_option( 'wppa_show_iptc', 'no' );
						wppa_update_option( 'wppa_save_iptc', 'no' );
					}
					break;

				case 'wppa_i_exif':
					if ( $value == 'yes' ) {
						wppa_update_option( 'wppa_show_exif', 'yes' );
						wppa_update_option( 'wppa_save_exif', 'yes' );
					}
					if ( $value == 'no' ) {
						wppa_update_option( 'wppa_show_exif', 'no' );
						wppa_update_option( 'wppa_save_exif', 'no' );
					}
					break;

				case 'wppa_i_gpx':
					if ( $value == 'yes' ) {
						$custom_content = wppa_opt( 'custom_content' );
						if ( strpos( $custom_content, 'w#location' ) === false ) {
							$custom_content = $custom_content.' w#location';
							wppa_update_option( 'wppa_custom_content', $custom_content );
						}
						if ( ! wppa_switch( 'custom_on' ) ) {
							wppa_update_option( 'wppa_custom_on', 'yes' );
						}
						if ( wppa_opt( 'gpx_implementation' ) == 'none' ) {
							wppa_update_option( 'wppa_gpx_implementation', 'wppa-plus-embedded' );
						}
					}
					break;

				case 'wppa_i_fotomoto':
					if ( $value == 'yes' ) {
						$custom_content = wppa_opt( 'custom_content' );
						if ( strpos( $custom_content, 'w#fotomoto' ) === false ) {
							$custom_content = 'w#fotomoto '.$custom_content;
							wppa_update_option( 'wppa_custom_content', $custom_content );
						}
						if ( ! wppa_switch( 'custom_on' ) ) {
							wppa_update_option( 'wppa_custom_on', 'yes' );
						}
						wppa_update_option( 'wppa_fotomoto_on', 'yes' );
						wppa_update_option( 'wppa_custom_on', 'yes' );
					}
					break;

				case 'wppa_i_video':
					if ( $value == 'yes' ) {
						wppa_update_option( 'wppa_enable_video', 'yes' );
					}
					else {
						wppa_update_option( 'wppa_enable_video', 'no' );
					}
					break;

				case 'wppa_i_audio':
					if ( $value == 'yes' ) {
						wppa_update_option( 'wppa_enable_audio', 'yes' );
					}
					else {
						wppa_update_option( 'wppa_enable_audio', 'no' );
					}
					break;

				case 'wppa_i_done':
					$value = 'done';
					break;

				case 'wppa_search_tags':
				case 'wppa_search_cats':
				case 'wppa_search_comments':
					update_option( 'wppa_index_need_remake', 'yes' );
					break;

				case 'wppa_blacklist_user':
					// Does user exist?
					$value = trim ( $value );
					$user = get_user_by ( 'login', $value );	// seems to be case insensitive
					if ( $user && $user->user_login === $value ) {
						$wpdb->query( $wpdb->prepare( "UPDATE `".WPPA_PHOTOS."` SET `status` = 'pending' WHERE `owner` = %s", $value ) );
						$black_listed_users = get_option( 'wppa_black_listed_users', array() );
						if ( ! in_array( $value, $black_listed_users ) ) {
							$black_listed_users[] = $value;
							update_option( 'wppa_black_listed_users', $black_listed_users );
						}
						$alert = esc_js( sprintf( __( 'User %s has been blacklisted.' , 'wp-photo-album-plus'), $value ) );
					}
					else {
						$alert = esc_js( sprintf( __( 'User %s does not exist.' , 'wp-photo-album-plus'), $value ) );
					}
					$value = '';
					break;

				case 'wppa_un_blacklist_user':
					$wpdb->query( $wpdb->prepare( "UPDATE `".WPPA_PHOTOS."` SET `status` = 'publish' WHERE `owner` = %s", $value ) );
					$black_listed_users = get_option( 'wppa_black_listed_users', array() );
					if ( in_array( $value, $black_listed_users ) ) {
						foreach ( array_keys( $black_listed_users ) as $usr ) {
							if ( $black_listed_users[$usr] == $value ) unset ( $black_listed_users[$usr] );
						}
						update_option( 'wppa_black_listed_users', $black_listed_users );
					}
					$value = '0';
					break;

				case 'wppa_superuser_user':
					// Does user exist?
					$value = trim ( $value );
					$user = get_user_by ( 'login', $value );	// seems to be case insensitive
					if ( $user && $user->user_login === $value ) {
						$super_users = get_option( 'wppa_super_users', array() );
						if ( ! in_array( $value, $super_users ) ) {
							$super_users[] = $value;
							update_option( 'wppa_super_users', $super_users );
						}
						$alert = esc_js( sprintf( __( 'User %s is now superuser.' , 'wp-photo-album-plus'), $value ) );
					}
					else {
						$alert = esc_js( sprintf( __( 'User %s does not exist.' , 'wp-photo-album-plus'), $value ) );
					}
					$value = '';
					break;

				case 'wppa_un_superuser_user':
					$super_users = get_option( 'wppa_super_users', array() );
					if ( in_array( $value, $super_users ) ) {
						foreach ( array_keys( $super_users ) as $usr ) {
							if ( $super_users[$usr] == $value ) unset ( $super_users[$usr] );
						}
						update_option( 'wppa_super_users', $super_users );
					}
					$value = '0';
					break;

				case 'wppa_fotomoto_on':
					if ( $value == 'yes' ) {
						$custom_content = wppa_opt( 'custom_content' );
						if ( strpos( $custom_content, 'w#fotomoto' ) === false ) {
							$custom_content = 'w#fotomoto '.$custom_content;
							wppa_update_option( 'wppa_custom_content', $custom_content );
							$alert = __( 'The content of the Custom box has been changed to display the Fotomoto toolbar.' , 'wp-photo-album-plus').' ';
						}
						if ( ! wppa_switch( 'custom_on' ) ) {
							wppa_update_option( 'wppa_custom_on', 'yes' );
							$alert .= __( 'The display of the custom box has been enabled' , 'wp-photo-album-plus');
						}
					}
					break;

				case 'wppa_gpx_implementation':
					if ( $value != 'none' ) {
						$custom_content = wppa_opt( 'custom_content' );
						if ( strpos( $custom_content, 'w#location' ) === false ) {
							$custom_content = $custom_content.' w#location';
							wppa_update_option( 'wppa_custom_content', $custom_content );
							$alert = __( 'The content of the Custom box has been changed to display maps.' , 'wp-photo-album-plus').' ';
						}
						if ( ! wppa_switch( 'custom_on' ) ) {
							wppa_update_option( 'wppa_custom_on', 'yes' );
							$alert .= __( 'The display of the custom box has been enabled' , 'wp-photo-album-plus');
						}
					}
					break;

				case 'wppa_regen_thumbs_skip_one':
					$last = get_option( 'wppa_regen_thumbs_last', '0' );
					$skip = $last + '1';
					update_option( 'wppa_regen_thumbs_last',  $skip );
					break;

				case 'wppa_remake_skip_one':
					$last = get_option( 'wppa_remake_last', '0' );
					$skip = $last + '1';
					update_option( 'wppa_remake_last',  $skip );
					break;

				case 'wppa_errorlog_purge':
					@ unlink( WPPA_CONTENT_PATH.'/wppa-depot/admin/error.log' );
					break;

				case 'wppa_pl_dirname':
					$value = wppa_sanitize_file_name( $value );
					$value = trim( $value, ' /' );
					if ( ! $value ) {
						wppa( 'error', '714' );
						wppa_out( __('This value can not be empty', 'wp-photo-album-plus') );
					}
					else {
						wppa_create_pl_htaccess( $value );
					}
					break;

				case 'wppa_new_tag_value':
					$value = wppa_sanitize_tags( $value, false, true );
					break;

				case 'wppa_up_tagselbox_content_1':
				case 'wppa_up_tagselbox_content_2':
				case 'wppa_up_tagselbox_content_3':
					$value = wppa_sanitize_tags( $value );
					break;

				case 'wppa_wppa_set_shortcodes':
					$value = str_replace( ' ', '', $value );
					break;

				case 'wppa_use_encrypted_links':
					if ( $value == 'yes' ) {
						$ca = $wpdb->get_var( "SELECT COUNT(*) FROM `" . WPPA_ALBUMS . "` WHERE `crypt` = ''" );
						$cp = $wpdb->get_var( "SELECT COUNT(*) FROM `" . WPPA_PHOTOS . "` WHERE `crypt` = ''" );
						if ( $ca + $cp ) {
							if ( $ca ) update_option ( 'wppa_crypt_albums_status', 'Required' );
							if ( $cp ) update_option ( 'wppa_crypt_photos_status', 'Required' );
							wppa( 'error', '4711' );
							$alert .= __( 'You must run Table VIII-A13 and VIII-A14 first before you can switch to encrypted urls.', 'wp-photo-album-plus' );
						}
						if ( wppa_switch( 'use_photo_names_in_urls' ) ) {
							$alert .= ' ' . __( 'Table IV-A3 will be switched off.', 'wp-photo-album-plus' );
							wppa_update_option( 'wppa_use_photo_names_in_urls', 'no' );
						}
						if ( wppa_switch( 'use_album_names_in_urls' ) ) {
							$alert .= ' ' . __( 'Table IV-A4 will be switched off.', 'wp-photo-album-plus' );
							wppa_update_option( 'wppa_use_album_names_in_urls', 'no' );
						}
					}
					break;

				case 'wppa_use_photo_names_in_urls':
				case 'wppa_use_album_names_in_urls':
					if ( wppa_switch( 'use_encrypted_links' ) ) {
						wppa( 'error', '4711' );
						$alert .= __( 'Not allowed when cryptic links is active', 'wp-photo-album-plus' );
					}

				case 'wppa_enable_video':
					// if off: set all statusses of videos to pending
					break;

				default:

					wppa( 'error', '0' );
					$alert = '';
			}

			if ( wppa( 'error' ) ) {
				if ( ! $title ) $title = sprintf( __( 'Failed to set %s to %s' , 'wp-photo-album-plus'), $option, $value );
				if ( ! $alert ) $alert .= wppa( 'out' );
			}
			else {
				wppa_update_option( $option, $value );
				if ( ! $title ) $title = sprintf( __( 'Setting %s updated to %s' , 'wp-photo-album-plus'), $option, $value );
			}

			// Save possible error
			$error = wppa( 'error' );

			// Something to do after changing the setting?
			wppa_initialize_runtime( true );	// force reload new values

			// .htaccess
			wppa_create_wppa_htaccess();

			// Thumbsize
			$new_minisize = wppa_get_minisize();
			if ( $old_minisize != $new_minisize ) {
				update_option ( 'wppa_regen_thumbs_status', 'Required' );
				$alert .= __( 'You just changed a setting that requires the regeneration of thumbnails.' , 'wp-photo-album-plus');
				$alert .= ' '.__( 'Please run the appropriate action in Table VIII.' , 'wp-photo-album-plus');
			}

			// Produce the response text
			$output = '||'.$error.'||'.esc_attr( $title ).'||'.esc_js( $alert );

			echo $output;
			wppa_clear_cache();
			wppa_exit();
			break;	// End update-option

		case 'maintenance':
			$slug 	= $_POST['slug'];
			$nonce  = $_REQUEST['wppa-nonce'];
			if ( ! wp_verify_nonce( $nonce, 'wppa-nonce' ) ) {
				echo 'Security check failure||'.$slug.'||Error||0';
				wppa_exit();
			}
			echo wppa_do_maintenance_proc( $slug );
			wppa_exit();
			break;

		case 'maintenancepopup':
			$slug 	= $_POST['slug'];
			$nonce  = $_REQUEST['wppa-nonce'];
			if ( ! wp_verify_nonce( $nonce, 'wppa-nonce' ) ) {
				echo 'Security check failure||'.$slug.'||Error||0';
				wppa_exit();
			}
			echo wppa_do_maintenance_popup( $slug );
			wppa_exit();
			break;

		case 'do-fe-upload':
			if ( is_admin() ) {
				require_once 'wppa-non-admin.php';
			}
			wppa_user_upload();
			echo wppa( 'out' );
			wppa_exit();
			break;

		case 'sanitizetags':
			$tags 		= isset( $_GET['tags'] ) ? $_GET['tags'] : '';
			$album 		= isset( $_GET['album'] ) ? $_GET['album'] : '0';
			$deftags 	= $album ? wppa_get_album_item( $album, 'default_tags' ) : '';
			$tags 		= $deftags ? $tags . ',' . $deftags : $tags;
			echo wppa_sanitize_tags( $tags, false, true );
			wppa_exit();
			break;

		case 'destroyalbum':
			$album = isset( $_GET['album'] ) ? $_GET['album'] : '0';
			if ( ! $album ) {
				_e('Missing album id', 'wp-photo-album-plus');
				wppa_exit();
			}
			$nonce = isset( $_GET['nonce'] ) ? $_GET['nonce'] : '';
			if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wppa_nonce_'.$album ) ) {
				echo 'Security check failure #798';
				wppa_exit();
			}

			// May I?
			$imay = true;
			if ( ! wppa_switch( 'user_destroy_on' ) ) $may = false;
			if ( wppa_switch( 'user_create_login' ) ) {
				if ( ! is_user_logged_in() ) $may = false;					// Must login
			}
			if ( ! wppa_have_access( $album ) ) {
				$may = false;						// No album access
			}
			if ( wppa_is_user_blacklisted() ) $may = false;
			if ( ! $imay ) {
				_e('You do not have the rights to delete this album', 'wp-photo-album-plus');
				wppa_exit();
			}

			// I may
			require_once 'wppa-album-admin-autosave.php';
			wppa_del_album( $album, '' );
			wppa_exit();
			break;

		case 'export-table':
			if ( ! wppa_user_is( 'administrator' ) ) {
				echo '||1||'.__( 'Security check failure' , 'wp-photo-album-plus' );
				wppa_exit();
			}
			$table = $_REQUEST['table'];
			$bret = wppa_export_table( $table );
			if ( $bret ) {
				echo '||0||' . WPPA_UPLOAD_URL . '/temp/' . $table . '.csv';
			}
			else {
				echo '||2||' . __( 'An error has occurred', 'wp-photo-album-plus' );
			}
			wppa_exit();
			break;


		default:	// Unimplemented $wppa-action
		die( '-1' );
	}
	wppa_exit();
}

function wppa_decode( $string ) {
	$arr = explode( '||HASH||', $string );
	$result = implode( '#', $arr );
	$arr = explode( '||AMP||', $result );
	$result = implode( '&', $arr );
	$arr = explode( '||PLUS||', $result );
	$result = implode( '+', $arr );

	return $result;
}

function wppa_ajax_check_range( $value, $fixed, $low, $high, $title ) {

	if ( $fixed !== false && $fixed == $value ) return;						// User enetred special value correctly
	if ( !is_numeric( $value ) ) wppa( 'error', true );						// Must be numeric if not specaial value
	if ( $low !== false && $value < $low ) wppa( 'error', true );			// Must be >= given min value
	if ( $high !== false && $value > $high ) wppa( 'error' , true );		// Must be <= given max value

	if ( ! wppa( 'error' ) ) return;		// Still no error, ok

	// Compose error message
	if ( $low !== false && $high === false ) {	// Only Minimum given
		wppa_out( __( 'Please supply a numeric value greater than or equal to' , 'wp-photo-album-plus') . ' ' . $low . ' ' . __( 'for' , 'wp-photo-album-plus') . ' ' . $title );
		if ( $fixed !== false ) {
			if ( $fixed ) wppa_out( '. ' . __( 'You may also enter:' , 'wp-photo-album-plus') . ' ' . $fixed );
			else wppa_out( '. ' . __( 'You may also leave/set this blank' , 'wp-photo-album-plus') );
		}
	}
	else {	// Also Maximum given
		wppa_out( __( 'Please supply a numeric value greater than or equal to' , 'wp-photo-album-plus') . ' ' . $low . ' ' . __( 'and less than or equal to' , 'wp-photo-album-plus') . ' ' . $high . ' ' . __( 'for' , 'wp-photo-album-plus') . ' ' . $title );
		if ( $fixed !== false ) {
			if ( $fixed ) wppa_out( '. ' . __( 'You may also enter:' , 'wp-photo-album-plus') . ' ' . $fixed );
			else wppa_out( '. ' . __( 'You may also leave/set this blank' , 'wp-photo-album-plus') );
		}
	}
}

