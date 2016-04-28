<?php
/* wppa-maintenance.php
* Package: wp-photo-album-plus
*
* Contains (not yet, but in the future maybe) all the maintenance routines
* Version 6.4.02
*
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

// Main maintenace module
// Must return a string like: errormesssage||$slug||status||togo
function wppa_do_maintenance_proc( $slug ) {
global $wpdb;
global $wppa_session;
global $wppa_supported_video_extensions;
global $wppa_supported_audio_extensions;

	// Check for multiple maintenance procs
	if ( ! wppa_switch( 'maint_ignore_concurrency_error' ) ) {
		$all_slugs = array( 'wppa_remake_index_albums',
							'wppa_remove_empty_albums',
							'wppa_remake_index_photos',
							'wppa_apply_new_photodesc_all',
							'wppa_append_to_photodesc',
							'wppa_remove_from_photodesc',
							'wppa_remove_file_extensions',
							'wppa_readd_file_extensions',
							'wppa_regen_thumbs',
							'wppa_rerate',
							'wppa_recup',
							'wppa_file_system',
							'wppa_cleanup',
							'wppa_remake',
							'wppa_list_index',
							'wppa_blacklist_user',
							'wppa_un_blacklist_user',
							'wppa_rating_clear',
							'wppa_viewcount_clear',
							'wppa_iptc_clear',
							'wppa_exif_clear',
							'wppa_watermark_all',
							'wppa_create_all_autopages',
							'wppa_delete_all_autopages',
							'wppa_leading_zeros',
							'wppa_add_gpx_tag',
							'wppa_optimize_ewww',
							'wppa_comp_sizes',
							'wppa_edit_tag',
							'wppa_sync_cloud',
							'wppa_sanitize_tags',
							'wppa_sanitize_cats',
							'wppa_test_proc',
							'wppa_crypt_photos',
							'wppa_crypt_albums',


						);
		foreach ( array_keys( $all_slugs ) as $key ) {
			if ( $all_slugs[$key] != $slug ) {
				if ( get_option( $all_slugs[$key].'_togo', '0') ) { 	// Process running
					return __('You can run only one maintenance procedure at a time', 'wp-photo-album-plus').'||'.$slug.'||'.__('Error', 'wp-photo-album-plus').'||'.''.'||'.'';
				}
			}
		}
	}

	// Lock this proc
	update_option( $slug.'_user', wppa_get_user() );

	// Extend session
	wppa_extend_session();

	// Initialize
	$endtime 	= time() + '5';	// Allow for 5 seconds
	$chunksize 	= '1000';
	$lastid 	= strval( intval ( get_option( $slug.'_last', '0' ) ) );
	$errtxt 	= '';
	$id 		= '0';
	$topid 		= '0';
	$reload 	= '';
	$to_delete_from_cloudinary = array();

	if ( ! isset( $wppa_session ) ) $wppa_session = array();
	if ( ! isset( $wppa_session[$slug.'_fixed'] ) )   $wppa_session[$slug.'_fixed'] = '0';
	if ( ! isset( $wppa_session[$slug.'_added'] ) )   $wppa_session[$slug.'_added'] = '0';
	if ( ! isset( $wppa_session[$slug.'_deleted'] ) ) $wppa_session[$slug.'_deleted'] = '0';
	if ( ! isset( $wppa_session[$slug.'_skipped'] ) ) $wppa_session[$slug.'_skipped'] = '0';

	if ( $lastid == '0' ) {
		$wppa_session[$slug.'_fixed'] = '0';
		$wppa_session[$slug.'_deleted'] = '0';
		$wppa_session[$slug.'_skipped'] = '0';
	}

	wppa_save_session();

	// Pre-processing needed?
	if ( $lastid == '0' ) {
		wppa_log( 'Obs', 'Maintenance proc '.$slug.' started.' );
		switch ( $slug ) {
			case 'wppa_remake_index_albums':
				$wpdb->query( "UPDATE `".WPPA_INDEX."` SET `albums` = ''" );
				break;
			case 'wppa_remake_index_photos':
				$wpdb->query( "UPDATE `".WPPA_INDEX."` SET `photos` = ''" );
				wppa_index_compute_skips();
				break;
			case 'wppa_recup':
				$wpdb->query( "DELETE FROM `".WPPA_IPTC."` WHERE `photo` <> '0'" );
				$wpdb->query( "DELETE FROM `".WPPA_EXIF."` WHERE `photo` <> '0'" );
				break;
			case 'wppa_file_system':
				if ( get_option('wppa_file_system') == 'flat' ) update_option( 'wppa_file_system', 'to-tree' );
				if ( get_option('wppa_file_system') == 'tree' ) update_option( 'wppa_file_system', 'to-flat' );
				break;
			case 'wppa_cleanup':
				$orphan_album = get_option( 'wppa_orphan_album', '0' );
				$album_exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM`".WPPA_ALBUMS."` WHERE `id` = %s", $orphan_album ) );
				if ( ! $album_exists ) $orphan_album = false;
				if ( ! $orphan_album ) {
					$orphan_album = wppa_create_album_entry( array( 'name' => __('Orphan photos', 'wp-photo-album-plus'), 'a_parent' => '-1', 'description' => __('This album contains refound lost photos', 'wp-photo-album-plus') ) );
					update_option( 'wppa_orphan_album', $orphan_album );
				}
				break;
			case 'wppa_sync_cloud':
				if ( ! wppa_get_present_at_cloudinary_a() ) {
					// Still Initializing
					$status = 'Initializing';
					if ( ! isset( $wppa_session['fun-count'] ) ) {
						$wppa_session['fun-count'] = 0;
					}
					$wppa_session['fun-count'] = ( $wppa_session['fun-count'] + 1 ) % 3;
					for ( $i=0; $i < $wppa_session['fun-count']; $i++ ) $status .= '.';
					$togo   = 'all';
					$reload = false;
					echo '||'.$slug.'||'.$status.'||'.$togo.'||'.$reload;
					wppa_exit();
				}
				break;
			case 'wppa_crypt_albums':
				update_option( 'wppa_album_crypt_0', wppa_get_unique_photo_crypt() );
				update_option( 'wppa_album_crypt_1', wppa_get_unique_photo_crypt() );
				update_option( 'wppa_album_crypt_2', wppa_get_unique_photo_crypt() );
				update_option( 'wppa_album_crypt_3', wppa_get_unique_photo_crypt() );
				break;


		}
		wppa_save_session();
	}

	// Dispatch on albums / photos / single actions

	switch ( $slug ) {

		case 'wppa_remake_index_albums':
		case 'wppa_remove_empty_albums':
		case 'wppa_sanitize_cats':
		case 'wppa_crypt_albums':

			// Process albums
			$table 		= WPPA_ALBUMS;
			$topid 		= $wpdb->get_var( "SELECT `id` FROM `".WPPA_ALBUMS."` ORDER BY `id` DESC LIMIT 1" );
			$albums 	= $wpdb->get_results( "SELECT * FROM `".WPPA_ALBUMS."` WHERE `id` > ".$lastid." ORDER BY `id` LIMIT 100", ARRAY_A );
			wppa_cache_album( 'add', $albums );

			if ( $albums ) foreach ( $albums as $album ) {

				$id = $album['id'];

				switch ( $slug ) {

					case 'wppa_remake_index_albums':
						wppa_index_add( 'album', $id );
						break;

					case 'wppa_remove_empty_albums':
						$p = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_PHOTOS."` WHERE `album` = %s", $id ) );
						$a = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_ALBUMS."` WHERE `a_parent` = %s", $id ) );
						if ( ! $a && ! $p ) {
							$wpdb->query( $wpdb->prepare( "DELETE FROM `".WPPA_ALBUMS."` WHERE `id` = %s", $id ) );
							wppa_delete_album_source( $id );
							wppa_flush_treecounts( $id );
							wppa_index_remove( 'album', $id );
						}
						break;

					case 'wppa_sanitize_cats':
						$cats = $album['cats'];
						if ( $cats ) {
							wppa_update_album( array( 'id' => $album['id'], 'cats' => wppa_sanitize_tags( $cats ) ) );
						}
						break;

					case 'wppa_crypt_albums':
						wppa_update_album( array( 'id' => $album['id'], 'crypt' => wppa_get_unique_album_crypt() ) );
						break;


				}
				// Test for timeout / ready
				$lastid = $id;
				update_option( $slug.'_last', $lastid );
				if ( time() > $endtime ) break; 	// Time out
			}
			else {	// Nothing to do, Done anyway
				$lastid = $topid;
			}
			break;	// End process albums

		case 'wppa_remake_index_photos':
			$chunksize = '100';
		case 'wppa_apply_new_photodesc_all':
		case 'wppa_append_to_photodesc':
		case 'wppa_remove_from_photodesc':
		case 'wppa_remove_file_extensions':
		case 'wppa_readd_file_extensions':
		case 'wppa_regen_thumbs':
		case 'wppa_rerate':
		case 'wppa_recup':
		case 'wppa_file_system':
		case 'wppa_cleanup':
		case 'wppa_remake':
		case 'wppa_watermark_all':
		case 'wppa_create_all_autopages':
		case 'wppa_delete_all_autopages':
		case 'wppa_leading_zeros':
		case 'wppa_add_gpx_tag':
		case 'wppa_optimize_ewww':
		case 'wppa_comp_sizes':
		case 'wppa_edit_tag':
		case 'wppa_sync_cloud':
		case 'wppa_sanitize_tags':
		case 'wppa_crypt_photos':
		case 'wppa_test_proc':

			// Process photos
			$table 		= WPPA_PHOTOS;

			if ( $slug == 'wppa_cleanup' ) {
				$topid 		= get_option( 'wppa_'.WPPA_PHOTOS.'_lastkey', '1' ) * 10;
				$photos 	= array();
				for ( $i = ( $lastid + '1'); $i <= $topid; $i++ ) {
					$photos[]['id'] = $i;
				}
			}
			else {
				$topid 		= $wpdb->get_var( "SELECT `id` FROM `".WPPA_PHOTOS."` ORDER BY `id` DESC LIMIT 1" );
				$photos 	= $wpdb->get_results( "SELECT * FROM `".WPPA_PHOTOS."` WHERE `id` > ".$lastid." ORDER BY `id` LIMIT ".$chunksize, ARRAY_A );
			}

			if ( $slug == 'wppa_edit_tag' ) {
				$edit_tag 	= get_option( 'wppa_tag_to_edit' );
				$new_tag 	= get_option( 'wppa_new_tag_value' );
			}

			if ( ! $photos && $slug == 'wppa_file_system' ) {
				$fs = get_option( 'wppa_file_system' );
				if ( $fs == 'to-tree' ) {
					$to = 'tree';
				}
				elseif ( $fs == 'to-flat' ) {
					$to = 'flat';
				}
				else {
					$to = $fs;
				}
			}

			if ( $photos ) foreach ( $photos as $photo ) {
				$thumb = $photo;	// Make globally known

				$id = $photo['id'];

				switch ( $slug ) {

					case 'wppa_remake_index_photos':
						wppa_index_add( 'photo', $id );
						break;

					case 'wppa_apply_new_photodesc_all':
						$value = wppa_opt( 'newphoto_description' );
						$description = trim( $value );
						if ( $description != $photo['description'] ) {	// Modified photo description
							$wpdb->query( $wpdb->prepare( "UPDATE `".WPPA_PHOTOS."` SET `description` = %s WHERE `id` = %s", $description, $id ) );
						}
						break;

					case 'wppa_append_to_photodesc':
						$value = trim( wppa_opt( 'append_text' ) );
						if ( ! $value ) return 'Unexpected error: missing text to append||'.$slug.'||Error||0';
						$description = rtrim( $photo['description'] . ' '. $value );
						if ( $description != $photo['description'] ) {	// Modified photo description
							$wpdb->query( $wpdb->prepare( "UPDATE `".WPPA_PHOTOS."` SET `description` = %s WHERE `id` = %s", $description, $id ) );
						}
						break;

					case 'wppa_remove_from_photodesc':
						$value = trim( wppa_opt( 'remove_text' ) );
						if ( ! $value ) return 'Unexpected error: missing text to remove||'.$slug.'||Error||0';
						$description = rtrim( str_replace( $value, '', $photo['description'] ) );
						if ( $description != $photo['description'] ) {	// Modified photo description
							$wpdb->query( $wpdb->prepare( "UPDATE `".WPPA_PHOTOS."` SET `description` = %s WHERE `id` = %s", $description, $id ) );
						}
						break;

					case 'wppa_remove_file_extensions':
						if ( ! wppa_is_video( $id ) ) {
							$name = str_replace( array( '.jpg', '.png', '.gif', '.JPG', '.PNG', '.GIF' ), '', $photo['name'] );
							if ( $name != $photo['name'] ) {	// Modified photo name
								$wpdb->query( $wpdb->prepare( "UPDATE `".WPPA_PHOTOS."` SET `name` = %s WHERE `id` = %s", $name, $id ) );
							}
						}
						break;

					case 'wppa_readd_file_extensions':
						if ( ! wppa_is_video( $id ) ) {
							$name = str_replace( array( '.jpg', '.png', 'gif', '.JPG', '.PNG', '.GIF' ), '', $photo['name'] );
							if ( $name == $photo['name'] ) { 	// Name had no fileextension
								$wpdb->query( $wpdb->prepare( "UPDATE `".WPPA_PHOTOS."` SET `name` = %s WHERE `id` = %s", $name.'.'.$photo['ext'], $id ) );
							}
						}
						break;

					case 'wppa_regen_thumbs':
						if ( ! wppa_is_video( $id ) || file_exists( str_replace( 'xxx', 'jpg', wppa_get_photo_path( $id ) ) ) ) {
							wppa_create_thumbnail( $id );
						}
						break;

					case 'wppa_rerate':
						wppa_rate_photo( $id );
						break;

					case 'wppa_recup':
						$a_ret = wppa_recuperate( $id );
						if ( $a_ret['iptcfix'] ) $wppa_session[$slug.'_fixed']++;
						if ( $a_ret['exiffix'] ) $wppa_session[$slug.'_fixed']++;
						break;

					case 'wppa_file_system':
						$fs = get_option('wppa_file_system');
						if ( $fs == 'to-tree' || $fs == 'to-flat' ) {
							if ( $fs == 'to-tree' ) {
								$from = 'flat';
								$to = 'tree';
							}
							else {
								$from = 'tree';
								$to = 'flat';
							}

							// Media files
							if ( wppa_is_multi( $id ) ) {	// Can NOT use wppa_has_audio() or wppa_is_video(), they use wppa_get_photo_path() without fs switch!!
								$exts 		= array_merge( $wppa_supported_video_extensions, $wppa_supported_audio_extensions );
								$pathfrom 	= wppa_get_photo_path( $id, $from );
								$pathto 	= wppa_get_photo_path( $id, $to );
							//	wppa_log( 'dbg', 'Trying: '.$pathfrom );
								foreach ( $exts as $ext ) {
									if ( is_file( str_replace( '.xxx', '.'.$ext, $pathfrom ) ) ) {
									//	wppa_log( 'dbg',  str_replace( '.xxx', '.'.$ext, $pathfrom ).' -> '.str_replace( '.xxx', '.'.$ext, $pathto ));
										@ rename ( str_replace( '.xxx', '.'.$ext, $pathfrom ), str_replace( '.xxx', '.'.$ext, $pathto ) );
									}
								}
							}

							// Poster / photo
							if ( file_exists( wppa_fix_poster_ext( wppa_get_photo_path( $id, $from ), $id ) ) ) {
								@ rename ( wppa_fix_poster_ext( wppa_get_photo_path( $id, $from ), $id ), wppa_fix_poster_ext( wppa_get_photo_path( $id, $to ), $id ) );
							}

							// Thumbnail
							if ( file_exists( wppa_fix_poster_ext( wppa_get_thumb_path( $id, $from ), $id ) ) ) {
								@ rename ( wppa_fix_poster_ext( wppa_get_thumb_path( $id, $from ), $id ), wppa_fix_poster_ext( wppa_get_thumb_path( $id, $to ), $id ) );
							}

						}
						break;

					case 'wppa_cleanup':
						$photo_files = glob( WPPA_UPLOAD_PATH.'/'.$id.'.*' );
						// Remove dirs
						if ( $photo_files ) {
							foreach( array_keys( $photo_files ) as $key ) {
								if ( is_dir( $photo_files[$key] ) ) {
									unset( $photo_files[$key] );
								}
							}
						}
						// files left? process
						if ( $photo_files ) foreach( $photo_files as $photo_file ) {
							$basename 	= basename( $photo_file );
							$ext 		= substr( $basename, strpos( $basename, '.' ) + '1');
							if ( ! $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_PHOTOS."` WHERE `id` = %s", $id ) ) ) { // no db entry for this photo
								if ( wppa_is_id_free( WPPA_PHOTOS, $id ) ) {
									if ( wppa_create_photo_entry( array( 'id' => $id, 'album' => $orphan_album, 'ext' => $ext, 'filename' => $basename ) ) ) { 	// Can create entry
										$wppa_session[$slug.'_fixed']++;	// Bump counter
										wppa_log( 'Debug', 'Lost photo file '.$photo_file.' recovered' );
									}
									else {
										wppa_log( 'Debug', 'Unable to recover lost photo file '.$photo_file.' Create photo entry failed' );
									}
								}
								else {
									wppa_log( 'Debug', 'Could not recover lost photo file '.$photo_file.' The id is not free' );
								}
							}
						}
						break;

					case 'wppa_remake':
						if ( wppa_switch( 'wppa_remake_orientation_only' ) ) {
							$ori = wppa_get_exif_orientation( wppa_get_source_path( $id ) );
							if ( $ori > '1'&& $ori < '9' ) {
								$doit = true;
							}
							else {
								$doit = false;
							}
						}
						else {
							$doit = true;
						}
						if ( $doit && wppa_remake_files( '', $id ) ) {
							$wppa_session[$slug.'_fixed']++;
						}
						else {
							$wppa_session[$slug.'_skipped']++;
						}
						break;

					case 'wppa_watermark_all':
						if ( ! wppa_is_video( $id ) ) {
							if ( wppa_add_watermark( $id ) ) {
								wppa_create_thumbnail( $id );	// create new thumb
								$wppa_session[$slug.'_fixed']++;
							}
							else {
								$wppa_session[$slug.'_skipped']++;
							}
						}
						else {
							$wppa_session[$slug.'_skipped']++;
						}
						break;

					case 'wppa_create_all_autopages':
						wppa_get_the_auto_page( $id );
						break;

					case 'wppa_delete_all_autopages':
						wppa_remove_the_auto_page( $id );
						break;

					case 'wppa_leading_zeros':
						$name = $photo['name'];
						if ( wppa_is_int( $name ) ) {
							$target_len = wppa_opt( 'zero_numbers' );
							$name = strval( intval( $name ) );
							while ( strlen( $name ) < $target_len ) $name = '0'.$name;
						}
						if ( $name !== $photo['name'] ) {
							$wpdb->query( $wpdb->prepare( "UPDATE `".WPPA_PHOTOS."` SET `name` = %s WHERE `id` = %s", $name, $id ) );
						}
						break;

					case 'wppa_add_gpx_tag':
						$tags 	= $photo['tags'];
						$temp 	= explode( '/', $photo['location'] );
						if ( ! isset( $temp['2'] ) ) $temp['2'] = false;
						if ( ! isset( $temp['3'] ) ) $temp['3'] = false;
						$lat 	= $temp['2'];
						$lon 	= $temp['3'];
						if ( $lat < 0.01 && $lat > -0.01 &&  $lon < 0.01 && $lon > -0.01 ) {
							$lat = false;
							$lon = false;
						}
						if ( $photo['location'] && strpos( $tags, 'Gpx' ) === false && $lat && $lon ) {	// Add it
							$tags = wppa_sanitize_tags( $tags . ',Gpx' );
							wppa_update_photo( array( 'id' => $photo['id'], 'tags' => $tags ) );
							wppa_index_update( 'photo', $photo['id'] );
							wppa_clear_taglist();
						}
						elseif ( strpos( $tags, 'Gpx' ) !== false && ! $lat && ! $lon ) { 	// Remove it
							$tags = wppa_sanitize_tags( str_replace( 'Gpx', '', $tags ) );
							wppa_update_photo( array( 'id' => $photo['id'], 'tags' => $tags ) );
							wppa_index_update( 'photo', $photo['id'] );
							wppa_clear_taglist();
						}
						break;

					case 'wppa_optimize_ewww':
						$file = wppa_get_photo_path( $photo['id'] );
						if ( is_file( $file ) ) {
							ewww_image_optimizer( $file, 4, false, false, false );
						}
						$file = wppa_get_thumb_path( $photo['id'] );
						if ( is_file( $file ) ) {
							ewww_image_optimizer( $file, 4, false, false, false );
						}
						break;

					case 'wppa_comp_sizes':
						$tx = 0; $ty = 0; $px = 0; $py = 0;
						$file = wppa_get_photo_path( $photo['id'] );
						if ( is_file( $file ) ) {
							$temp = getimagesize( $file );
							if ( is_array( $temp ) ) {
								$px = $temp[0];
								$py = $temp[1];
							}
						}
						$file = wppa_get_thumb_path( $photo['id'] );
						if ( is_file( $file ) ) {
							$temp = getimagesize( $file );
							if ( is_array( $temp ) ) {
								$tx = $temp[0];
								$ty = $temp[1];
							}
						}
						wppa_update_photo( array( 'id' => $photo['id'], 'thumbx' => $tx, 'thumby' => $ty, 'photox' => $px, 'photoy' => $py ) );
						break;

					case 'wppa_edit_tag':
						$phototags = explode( ',', wppa_get_photo_item( $photo['id'], 'tags' ) );
						if ( in_array( $edit_tag, $phototags ) ) {
							foreach( array_keys( $phototags ) as $key ) {
								if ( $phototags[$key] == $edit_tag ) {
									$phototags[$key] = $new_tag;
								}
							}
							$tags = wppa_sanitize_tags( implode( ',', $phototags ) );
							wppa_update_photo( array( 'id' => $photo['id'], 'tags' => $tags ) );
							$wppa_session[$slug.'_fixed']++;
						}
						else {
							$wppa_session[$slug.'_skipped']++;
						}
						break;

					case 'wppa_sync_cloud':
						$is_old 	 = ( wppa_opt( 'max_cloud_life' ) ) && ( time() > ( $photo['timestamp'] + wppa_opt( 'max_cloud_life' ) ) );
					//	$is_in_cloud = @ getimagesize( wppa_get_cloudinary_url( $photo['id'], 'test_only' ) );
						$is_in_cloud = isset( $wppa_session['cloudinary_ids'][$photo['id']] );
					//	wppa_log('Obs', 'Id='.$photo['id'].', is old='.$is_old.', in cloud='.$is_in_cloud);
						if ( $is_old && $is_in_cloud ) {
							$to_delete_from_cloudinary[] = strval( $photo['id'] );
							if ( count( $to_delete_from_cloudinary ) == 10 ) {
								wppa_delete_from_cloudinary( $to_delete_from_cloudinary );
								$to_delete_from_cloudinary = array();
							}
							$wppa_session[$slug.'_deleted']++;
						}
						if ( ! $is_old && ! $is_in_cloud ) {
							wppa_upload_to_cloudinary( $photo['id'] );
							$wppa_session[$slug.'_added']++;
						}
						if ( $is_old && ! $is_in_cloud ) {
							$wppa_session[$slug.'_skipped']++;
						}
						if ( ! $is_old && $is_in_cloud ) {
							$wppa_session[$slug.'_skipped']++;
						}
						break;

					case 'wppa_sanitize_tags':
						$tags = $photo['tags'];
						if ( $tags ) {
							wppa_update_photo( array( 'id' => $photo['id'], 'tags' => wppa_sanitize_tags( $tags ) ) );
						}
						break;

					case 'wppa_crypt_photos':
						wppa_update_photo( array( 'id' => $photo['id'], 'crypt' => wppa_get_unique_photo_crypt() ) );
						break;

					case 'wppa_test_proc':
						$tags 	= '';
						$albid 	= $photo['album'];
						$albnam = wppa_get_album_item( $albid, 'name' );
						$tags .= $albnam;
						while ( $albid > '0' ) {
							$albid = wppa_get_album_item( $albid, 'a_parent' );
							if ( $albid > '0' ) {
								$tags .= ',' . wppa_get_album_item( $albid, 'name' );
							}
						}
						wppa_update_photo( array( 'id' => $photo['id'], 'tags' => wppa_sanitize_tags( $tags ) ) );
						break;

				}
				// Test for timeout / ready
				$lastid = $id;
				update_option( $slug.'_last', $lastid );
				if ( time() > $endtime ) break; 	// Time out
			}
			else {	// Nothing to do, Done anyway
				$lastid = $topid;
				wppa_log( 'Debug', 'Maintenance proc '.$slug.': Done!');
			}
			break;	// End process photos

		// Single action maintenance modules

//		case 'wppa_list_index':
//			break;

//		case 'wppa_blacklist_user':
//			break;

//		case 'wppa_un_blacklist_user':
//			break;

//		case 'wppa_rating_clear':
//			break;

//		case 'wppa_viewcount_clear':
//			break;

//		case 'wppa_iptc_clear':
//			break;

//		case 'wppa_exif_clear':
//			break;

		default:
			$errtxt = 'Unimplemented maintenance slug: '.strip_tags( $slug );
	}

	// either $albums / $photos has been exhousted ( for this try ) or time is up

	// Post proc this try:
	switch ( $slug ) {

		case 'wppa_sync_cloud':
			if ( count( $to_delete_from_cloudinary ) > 0 ) {
				wppa_delete_from_cloudinary( $to_delete_from_cloudinary );
			}
			break;
	}

	// Find togo
	if ( $slug == 'wppa_cleanup' ) {
		$togo 	= $topid - $lastid;
	}
	else {
		$togo 	= $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".$table."` WHERE `id` > %s ", $lastid ) );
	}

	// Find status
	if ( ! $errtxt ) {
		$status = $togo ? 'Working' : 'Ready';
	}
	else $status = 'Error';

	// Not done yet?
	if ( $togo ) {
		update_option( $slug.'_togo', $togo );
		update_option( $slug.'_status', 'Pending' );
	}

	// Really done
	else {

		// Report fixed/skipped/deleted
		if ( $wppa_session[$slug.'_fixed'] ) {
			$status .= ' fixed:'.$wppa_session[$slug.'_fixed'];
			unset ( $wppa_session[$slug.'_fixed'] );
		}
		if ( $wppa_session[$slug.'_added'] ) {
			$status .= ' added:'.$wppa_session[$slug.'_added'];
			unset ( $wppa_session[$slug.'_added'] );
		}
		if ( $wppa_session[$slug.'_deleted'] ) {
			$status .= ' deleted:'.$wppa_session[$slug.'_deleted'];
			unset ( $wppa_session[$slug.'_deleted'] );
		}
		if ( $wppa_session[$slug.'_skipped'] ) {
			$status .= ' skipped:'.$wppa_session[$slug.'_skipped'];
			unset ( $wppa_session[$slug.'_skipped'] );
		}

		// Re-Init options
		update_option( $slug.'_togo', '' );
		update_option( $slug.'_status', '' );
		update_option( $slug.'_last', '0' );
		update_option( $slug.'_user', '' );

		// Post-processing needed?
		switch ( $slug ) {
			case 'wppa_remake_index_albums':
			case 'wppa_remake_index_photos':
				$wpdb->query( "DELETE FROM `".WPPA_INDEX."` WHERE `albums` = '' AND `photos` = ''" );	// Remove empty entries
				delete_option( 'wppa_index_need_remake' );
				break;
			case 'wppa_apply_new_photodesc_all':
			case 'wppa_append_to_photodesc':
			case 'wppa_remove_from_photodesc':
				update_option( 'wppa_remake_index_photos_status', __('Required', 'wp-photo-album-plus') );
				break;
			case 'wppa_regen_thumbs':
				wppa_bump_thumb_rev();
				break;
			case 'wppa_file_system':
				wppa_update_option( 'wppa_file_system', $to );
				$reload = 'reload';
				break;
			case 'wppa_remake':
				wppa_bump_photo_rev();
				wppa_bump_thumb_rev();
				break;
			case 'wppa_edit_tag':
				wppa_clear_taglist();
				if ( wppa_switch( 'search_tags' ) ) {
					update_option( 'wppa_remake_index_photos_status', __('Required', 'wp-photo-album-plus') );
				}
				$reload = 'reload';
				break;
			case 'wppa_sanitize_tags':
				wppa_clear_taglist();
				break;
			case 'wppa_sanitize_cats':
				wppa_clear_catlist();
				break;
			case 'wppa_test_proc':
				wppa_clear_taglist();
				break;
			case 'wppa_sync_cloud':
				unset( $wppa_session['cloudinary_ids'] );
				break;
		}

		wppa_log( 'Obs', 'Maintenance proc '.$slug.' completed' );

	}

	wppa_save_session();

	return $errtxt.'||'.$slug.'||'.$status.'||'.$togo.'||'.$reload;
}

function wppa_do_maintenance_popup( $slug ) {
global $wpdb;

	$result = '';

	switch ( $slug ) {
		case 'wppa_list_index':
			$start = get_option( 'wppa_list_index_display_start', '' );
			$total = $wpdb->get_var( "SELECT COUNT(*) FROM `".WPPA_INDEX."`" );
			$indexes = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `".WPPA_INDEX."` WHERE `slug` >= %s ORDER BY `slug` LIMIT 1000", $start ), ARRAY_A );

			$result .= '
			<style>td, th { border-right: 1px solid darkgray; } </style>
			<h2>List of Searcheable words <small>( Max 1000 entries of total '.$total.' )</small></h2>
			<div style="float:left; clear:both; width:100%; overflow:auto; background-color:#f1f1f1; border:1px solid #ddd;" >';
			if ( $indexes ) {
				$result .= '
				<table>
					<thead>
						<tr>
							<th><span style="float:left;" >Word</span></th>
							<th style="max-width:400px;" ><span style="float:left;" >Albums</span></th>
							<th><span style="float:left;" >Photos</span></th>
						</tr>
						<tr><td colspan="3"><hr /></td></tr>
					</thead>
					<tbody>';

				foreach ( $indexes as $index ) {
					$result .= '
						<tr>
							<td>'.$index['slug'].'</td>
							<td style="max-width:400px; word-wrap: break-word;" >'.$index['albums'].'</td>
							<td>'.$index['photos'].'</td>
						</tr>';
				}

				$result .= '
					</tbody>
				</table>';
			}
			else {
				$result .= __('There are no index items.', 'wp-photo-album-plus');
			}
			$result .= '
				</div><div style="clear:both;"></div>';

			break;

		case 'wppa_list_errorlog':
			$filename = WPPA_CONTENT_PATH.'/wppa-depot/admin/error.log';
			$result .= '
				<h2>List of WPPA+ error messages</h2>
				<div style="float:left; clear:both; width:100%; overflow:auto; word-wrap:none; background-color:#f1f1f1; border:1px solid #ddd;" >';

			if ( ! $file = @ fopen( $filename, 'r' ) ) {
				$result .= __('There are no error log messages', 'wp-photo-album-plus');
			}
			else {
				$size 	= filesize( $filename );
				$data 	= fread( $file, $size );
				$data 	= htmlspecialchars( strip_tags( $data ) );
				$data 	= str_replace( array( '{b}', '{/b}', "\n" ), array( '<b>', '</b>', '<br />' ), $data );
				$result .= $data;
				fclose( $file );
			}

			$result .= '
				</div><div style="clear:both;"></div>
				';
			break;

		case 'wppa_list_rating':
			$total = $wpdb->get_var( "SELECT COUNT(*) FROM `".WPPA_RATING."`" );
			$ratings = $wpdb->get_results( "SELECT * FROM `".WPPA_RATING."` ORDER BY `timestamp` DESC LIMIT 1000", ARRAY_A );
			$result .= '
			<style>td, th { border-right: 1px solid darkgray; } </style>
			<h2>List of recent ratings <small>( Max 1000 entries of total '.$total.' )</small></h2>
			<div style="float:left; clear:both; width:100%; overflow:auto; background-color:#f1f1f1; border:1px solid #ddd;" >';
			if ( $ratings ) {
				$result .= '
				<table>
					<thead>
						<tr>
							<th>Id</th>
							<th>Timestamp</th>
							<th>Date/time</th>
							<th>Status</th>
							<th>User</th>
							<th>Value</th>
							<th>Photo id</th>
							<th></th>
							<th># ratings</th>
							<th>Average</th>
						</tr>
						<tr><td colspan="10"><hr /></td></tr>
					</thead>
					<tbody>';

				foreach ( $ratings as $rating ) {
					$thumb = wppa_cache_thumb( $rating['photo'] );
					$result .= '
						<tr>
							<td>'.$rating['id'].'</td>
							<td>'.$rating['timestamp'].'</td>
							<td>'.( $rating['timestamp'] ? wppa_local_date(get_option('date_format', "F j, Y,").' '.get_option('time_format', "g:i a"), $rating['timestamp']) : 'pre-historic' ).'</td>
							<td>'.$rating['status'].'</td>
							<td>'.$rating['user'].'</td>
							<td>'.$rating['value'].'</td>
							<td>'.$rating['photo'].'</td>
							<td style="width:250px; text-align:center;"><img src="'.wppa_get_thumb_url($rating['photo']).'"
								style="height: 40px;"
								onmouseover="jQuery(this).stop().animate({height:this.naturalHeight}, 200);"
								onmouseout="jQuery(this).stop().animate({height:\'40px\'}, 200);" /></td>
							<td>'.$thumb['rating_count'].'</td>
							<td>'.$thumb['mean_rating'].'</td>
						</tr>';
				}

				$result .= '
					</tbody>
				</table>';
			}
			else {
				$result .= __('There are no ratings', 'wp-photo-album-plus');
			}
			$result .= '
				</div><div style="clear:both;"></div>';
			break;

		case 'wppa_list_session':
			$total = $wpdb->get_var( "SELECT COUNT(*) FROM `".WPPA_SESSION."` WHERE `status` = 'valid'" );
			$sessions = $wpdb->get_results( "SELECT * FROM `".WPPA_SESSION."` WHERE `status` = 'valid' ORDER BY `id` DESC LIMIT 1000", ARRAY_A );
			$result .= '
			<style>td, th { border-right: 1px solid darkgray; } </style>
			<h2>List of active sessions <small>( Max 1000 entries of total '.$total.' )</small></h2>
			<div style="float:left; clear:both; width:100%; overflow:auto; background-color:#f1f1f1; border:1px solid #ddd;" >';
			if ( $sessions ) {
				$result .= '
				<table>
					<thead>
						<tr>
							<th>Id</th>

							<th>IP</th>
							<th>Started</th>
							<th>Count</th>
							<th>Data</th>
							<th>Uris</th>
						</tr>
						<tr><td colspan="7"><hr /></td></tr>
					</thead>
					<tbody style="overflow:auto;" >';
					foreach ( $sessions as $session ) {
						$data = unserialize( $session['data'] );
						$result .= '
							<tr>
								<td>'.$session['id'].'</td>

								<td>'.$session['ip'].'</td>
								<td style="width:150px;" >'.wppa_local_date(get_option('date_format', "F j, Y,").' '.get_option('time_format', "g:i a"), $session['timestamp']).'</td>
								<td>'.$session['count'].'</td>' .
								'<td style="border-bottom:1px solid gray;max-width:300px;" >';
									foreach ( array_keys( $data ) as $key ) {
										if ( $key != 'uris' ) {
											if ( is_array( $data[$key] ) ) {
												$result .= '['.$key.'] => Array('.
												implode( ',', array_keys($data[$key]) ) .
												')<br />';
											}
											else {
												$result .= '['.$key.'] => '.$data[$key].'<br />';
											}
										}
									}
						$result .= '
								</td>
								<td style="border-bottom:1px solid gray;" >';
								if ( is_array( $data['uris'] ) ) {
									foreach ( $data['uris'] as $uri ) {
										$result .= $uri.'<br />';
									}
								}
						$result .= '
								</td>
							</tr>';
					}
				$result .= '
					</tbody>
				</table>';
			}
			else {
				$result .= __('There are no active sessions', 'wp-photo-album-plus');
			}
			$result .= '
				</div><div style="clear:both;"></div>';

			break;
			
		case 'wppa_list_comments':
			$total = $wpdb->get_var( "SELECT COUNT(*) FROM `".WPPA_COMMENTS."`" );
			$order = wppa_opt( 'list_comments_by' );
			if ( $order == 'timestamp' ) $order .= ' DESC';
			$query = "SELECT * FROM `".WPPA_COMMENTS."` ORDER BY ".$order." LIMIT 1000";
	$result .= $query.'<br />';
			$comments = $wpdb->get_results( $query, ARRAY_A );
			$result .= '
			<style>td, th { border-right: 1px solid darkgray; } </style>
			<h2>List of comments <small>( Max 1000 entries of total '.$total.' )</small></h2>
			<div style="float:left; clear:both; width:100%; overflow:auto; background-color:#f1f1f1; border:1px solid #ddd;" >';
			if ( $comments ) {
				$result .= '
				<table>
					<thead>
						<tr>
							<th>Id</th>
							<th>Timestamp</th>
							<th>Date/time</th>
							<th>Status</th>
							<th>User</th>
							<th>Email</th>
							<th>Photo id</th>
							<th></th>
							<th>Comment</th>
						</tr>
						<tr><td colspan="10"><hr /></td></tr>
					</thead>
					<tbody>';

				foreach ( $comments as $comment ) {
					$thumb = wppa_cache_thumb( $comment['photo'] );
					$result .= '
						<tr>
							<td>'.$comment['id'].'</td>
							<td>'.$comment['timestamp'].'</td>
							<td>'.( $comment['timestamp'] ? wppa_local_date(get_option('date_format', "F j, Y,").' '.get_option('time_format', "g:i a"), $comment['timestamp']) : 'pre-historic' ).'</td>
							<td>'.$comment['status'].'</td>
							<td>'.$comment['user'].'</td>
							<td>'.$comment['email'].'</td>
							<td>'.$comment['photo'].'</td>
							<td style="width:250px; text-align:center;"><img src="'.wppa_get_thumb_url($comment['photo']).'"
								style="height: 40px;"
								onmouseover="jQuery(this).stop().animate({height:this.naturalHeight}, 200);"
								onmouseout="jQuery(this).stop().animate({height:\'40px\'}, 200);" /></td>
							<td>'.$comment['comment'].'</td>
						</tr>';
				}

				$result .= '
					</tbody>
				</table>';
			}
			else {
				$result .= __('There are no comments', 'wp-photo-album-plus');
				$result .= '<br />Query='.$wpdb->prepare( "SELECT * FROM `".WPPA_COMMENTS."` ORDER BY %s DESC LIMIT 1000", wppa_opt( 'list_comments_by' ) );
			}
			$result .= '
				</div><div style="clear:both;"></div>';
			break;

		default:
			$result = 'Error: Unimplemented slug: '.$slug.' in wppa_do_maintenance_popup()';
	}

	return $result;
}

function wppa_recuperate( $id ) {
global $wpdb;

	$thumb = wppa_cache_thumb( $id );
	$iptcfix = false;
	$exiffix = false;
	$file = wppa_get_source_path( $id );
	if ( ! is_file( $file ) ) $file = wppa_get_photo_path( $id );

	if ( is_file ( $file ) ) {					// Not a dir
		$attr = getimagesize( $file, $info );
		if ( is_array( $attr ) ) {				// Is a picturefile
			if ( $attr[2] == IMAGETYPE_JPEG ) {	// Is a jpg

				if ( wppa_switch( 'save_iptc' ) ) {	// Save iptc
					if ( isset( $info["APP13"] ) ) {		// There is IPTC data
						$is_iptc = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_IPTC."` WHERE `photo` = %s", $id ) );
						if ( ! $is_iptc ) { 				// No IPTC yet and there is: Recuperate
							wppa_import_iptc($id, $info, 'nodelete');
							$iptcfix = true;
						}
					}
				}

				if ( wppa_switch('wppa_save_exif') ) {		// Save exif
					$image_type = exif_imagetype( $file );
					if ( $image_type == IMAGETYPE_JPEG ) {	// EXIF supported by server
						$is_exif = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_EXIF."` WHERE `photo`=%s", $id ) );
						if ( ! $is_exif ) { 				// No EXIF yet
							$exif = @ exif_read_data($file, 'EXIF');//@
							if ( is_array( $exif ) ) { 		// There is exif data present
								wppa_import_exif($id, $file, 'nodelete');
								$exiffix = true;
							}
						}
					}
				}
			}
		}
	}
	return array( 'iptcfix' => $iptcfix, 'exiffix' => $exiffix );
}

// Fix erroneous source path in case of migration to an other host
function wppa_fix_source_path() {

	if ( strpos( wppa_opt( 'source_dir' ), ABSPATH ) === 0 ) return; 					// Nothing to do here

	$wp_content = trim( str_replace( home_url(), '', content_url() ), '/' );

	// The source path should be: ( default ) WPPA_ABSPATH.WPPA_UPLOAD.'/wppa-source',
	// Or at least below WPPA_ABSPATH
	if ( strpos( wppa_opt( 'source_dir' ), WPPA_ABSPATH ) === false ) {
		if ( strpos( wppa_opt( 'source_dir' ), $wp_content ) !== false ) {	// Its below wp-content
			$temp = explode( $wp_content, wppa_opt( 'source_dir' ) );
			$temp['0'] = WPPA_ABSPATH;
			wppa_update_option( 'wppa_source_dir', implode( $wp_content, $temp ) );
			wppa_log( 'Fix', 'Sourcepath set to ' . wppa_opt( 'source_dir' ) );
		}
		else { // Give up, set to default
			wppa_update_option( 'wppa_source_dir', WPPA_ABSPATH.WPPA_UPLOAD.'/wppa-source' );
			wppa_log( 'Fix', 'Sourcepath set to default.' );
		}
	}
}

