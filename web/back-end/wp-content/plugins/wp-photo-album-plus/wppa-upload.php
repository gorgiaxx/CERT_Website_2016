<?php
/* wppa-upload.php
* Package: wp-photo-album-plus
*
* Contains all the upload/import pages and functions
* Version 6.4.02
*
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

// upload images admin page
function _wppa_page_upload() {
global $target;
global $wppa_revno;

    // sanitize system
	$user = wppa_get_user();
	wppa_sanitize_files();

	// Update watermark settings for the user ifnew values supplied
	if ( wppa_switch( 'watermark_on' ) && ( wppa_switch( 'watermark_user' ) || current_user_can( 'wppa_settings' ) ) ) {
		if ( isset( $_POST['wppa-watermark-file'] ) ) update_option( 'wppa_watermark_file_'.$user, $_POST['wppa-watermark-file'] );
		if ( isset( $_POST['wppa-watermark-pos'] ) ) update_option( 'wppa_watermark_pos_'.$user, $_POST['wppa-watermark-pos'] );
	}

	// If from album admin set the last album
	if ( isset( $_REQUEST['wppa-set-album'] ) ) wppa_set_last_album( $_REQUEST['wppa-set-album'] );

	// Do the upload if requested
	// From BOX A
	if ( isset( $_POST['wppa-upload-multiple'] ) ) {
		check_admin_referer( '$wppa_nonce', WPPA_NONCE );
		wppa_upload_multiple();
		if ( isset( $_POST['wppa-go-edit-multiple'] ) ) {
			if ( current_user_can( 'wppa_admin' ) ) {
				wppa_ok_message( __( 'Connecting to edit album...' , 'wp-photo-album-plus' ) ); ?>
				<script type="text/javascript">document.location = '<?php echo( wppa_dbg_url( get_admin_url().'admin.php?page=wppa_admin_menu&tab=edit&edit_id='.$_POST['wppa-album'], 'js' ) ) ?>';</script>
			<?php }
			else {
				wppa_ok_message( __( 'Connecting to edit photos...' , 'wp-photo-album-plus' ) ); ?>
				<script type="text/javascript">document.location = '<?php echo( wppa_dbg_url( get_admin_url().'admin.php?page=wppa_edit_photo', 'js' ) ) ?>';</script>
			<?php }
		}
	}
	// From BOX B
	if ( isset( $_POST['wppa-upload'] ) ) {
		check_admin_referer( '$wppa_nonce', WPPA_NONCE );
		wppa_upload_photos();
		if ( isset( $_POST['wppa-go-edit-single'] ) ) {
			if ( current_user_can( 'wppa_admin' ) ) {
				wppa_ok_message( __( 'Connecting to edit album...' , 'wp-photo-album-plus' ) ); ?>
				<script type="text/javascript">document.location = '<?php echo( wppa_dbg_url( get_admin_url().'admin.php?page=wppa_admin_menu&tab=edit&edit_id='.$_POST['wppa-album'], 'js' ) ) ?>';</script>
			<?php }
			else {
				wppa_ok_message( __( 'Connecting to edit photos...' , 'wp-photo-album-plus' ) ); ?>
				<script type="text/javascript">document.location = '<?php echo( wppa_dbg_url( get_admin_url().'admin.php?page=wppa_edit_photo', 'js' ) ) ?>';</script>
			<?php }
		}
	}
	// From BOX C
	if ( isset( $_POST['wppa-upload-zip'] ) ) {
		check_admin_referer( '$wppa_nonce', WPPA_NONCE );
		$err = wppa_upload_zip();
		if ( isset( $_POST['wppa-go-import'] ) && $err == '0' ) {
			wppa_ok_message( __( 'Connecting to your depot...' , 'wp-photo-album-plus' ) );
			update_option( 'wppa_import_source_'.$user, WPPA_DEPOT_PATH ); ?>
			<script type="text/javascript">document.location = '<?php echo( wppa_dbg_url( get_admin_url().'admin.php?page=wppa_import_photos&zip='.$target, 'js' ) ) ?>';</script>
		<?php }
	}

	// sanitize system again
	wppa_sanitize_files();

	?>

	<div class="wrap">
		<?php $iconurl = WPPA_URL.'/images/camera32.png'; ?>
		<div id="icon-camera" class="icon32" style="background: transparent url( <?php echo( $iconurl ); ?> ) no-repeat">
		</div>
		<?php $iconurl = WPPA_URL.'/images/arrow32.png'; ?>
		<div id="icon-arrow" class="icon32" style="background: transparent url( <?php echo( $iconurl ); ?> ) no-repeat">
		</div>
		<?php $iconurl = WPPA_URL.'/images/album32.png'; ?>
		<div id="icon-album" class="icon32" style="background: transparent url( <?php echo( $iconurl ); ?> ) no-repeat">
		<br />
		</div>
		<h2><?php _e( 'Upload Photos' , 'wp-photo-album-plus'); ?></h2>

		<?php
		// Check for trivial requirements
		if ( ! function_exists( 'imagecreatefromjpeg' ) ) {
			wppa_error_message( __( 'There is a serious misconfiguration in your servers PHP config. Function imagecreatefromjpeg() does not exist. You will encounter problems when uploading photos and not be able to generate thumbnail images. Ask your hosting provider to add GD support with a minimal version 1.8.' , 'wp-photo-album-plus' ) );
		}

		$max_files = ini_get( 'max_file_uploads' );
		$max_files_txt = $max_files;
		if ( $max_files < '1' ) {
			$max_files_txt = __( 'unknown' , 'wp-photo-album-plus' );
			$max_files = '15';
		}
		$max_size = ini_get( 'upload_max_filesize' );
		$max_time = ini_get( 'max_input_time' );
		if ( $max_time < '1' ) $max_time = __( 'unknown' , 'wp-photo-album-plus' );

		// chek if albums exist before allowing upload
		if ( wppa_has_albums() ) {
			if ( wppa_switch( 'upload_one_only' ) && ! wppa_user_is( 'administrator' ) ) {
				/* One only */ ?>
				<div style="border:1px solid #ccc; padding:10px; margin-bottom:10px; width: 600px;">
					<h3 style="margin-top:0px;"><?php _e( 'Upload a single photo' , 'wp-photo-album-plus'); ?></h3>
					<form enctype="multipart/form-data" action="<?php echo( wppa_dbg_url( get_admin_url().'admin.php?page=wppa_upload_photos' ) ) ?>" method="post">
					<?php wp_nonce_field( '$wppa_nonce', WPPA_NONCE ); ?>
						<input id="my_files" type="file" name="my_files[]" />
						<p>
							<label for="wppa-album"><?php _e( 'Album:' , 'wp-photo-album-plus'); ?> </label>
							<select name="wppa-album" id="wppa-album-s">
								<?php echo wppa_album_select_a( array( 'path' => wppa_switch( 'hier_albsel' ),'addpleaseselect' => true, 'checkowner' => true, 'checkupload' => true ) ) ?>
							</select>
						</p>
						<?php if ( wppa_switch( 'watermark_on' ) && ( wppa_switch( 'watermark_user' ) || current_user_can( 'wppa_settings' ) ) ) { ?>
							<p>
								<?php _e( 'Apply watermark file:' , 'wp-photo-album-plus') ?>
								<select name="wppa-watermark-file" id="wppa-watermark-file">
									<?php echo( wppa_watermark_file_select() ) ?>
								</select>

								<?php _e( 'Position:' , 'wp-photo-album-plus') ?>
								<select name="wppa-watermark-pos" id="wppa-watermark-pos">
									<?php echo( wppa_watermark_pos_select() ) ?>
								</select>
							</p>
						<?php } ?>
						<input type="submit" class="button-primary" name="wppa-upload-multiple" value="<?php _e( 'Upload Photo' , 'wp-photo-album-plus') ?>" onclick="if ( document.getElementById( 'wppa-album-s' ).value == 0 ) { alert( '<?php _e( 'Please select an album' , 'wp-photo-album-plus') ?>' ); return false; }" />
						<input type="checkbox" id="wppa-go-edit-multiple" name="wppa-go-edit-multiple" style="display:none" checked="checked" />&nbsp;
					</form>
				</div>
<?php		}
			else { ?>
				<div style="border:1px solid #ccc; padding:10px; margin-bottom:10px; width: 600px; background-color:#fffbcc; border-color:#e6db55;">
				<?php echo( sprintf( __( '<b>Notice:</b> your server allows you to upload <b>%s</b> files of maximum total <b>%s</b> bytes and allows <b>%s</b> seconds to complete.' , 'wp-photo-album-plus' ), $max_files_txt, $max_size, $max_time ) ) ?>
				<?php _e( 'If your request exceeds these limitations, it will fail, probably without an errormessage.' , 'wp-photo-album-plus' ) ?>
				<?php _e( 'Additionally your hosting provider may have set other limitations on uploading files.' , 'wp-photo-album-plus' ) ?>
				<?php echo '<br />'.wppa_check_memory_limit() ?>
				</div>
				<?php /* Multple photos */ ?>
				<div style="border:1px solid #ccc; padding:10px; margin-bottom:10px; width: 600px;">
					<h3 style="margin-top:0px;"><?php _e( 'Box A:' , 'wp-photo-album-plus'); echo ' ';_e( 'Multiple Photos in one selection' , 'wp-photo-album-plus'); ?></h3>
					<?php echo sprintf( __( 'You can select up to %s photos in one selection and upload them.' , 'wp-photo-album-plus'), $max_files_txt ); ?>
					<br /><small style="color:blue" ><?php _e( 'You need a modern browser that supports HTML-5 to select multiple files' , 'wp-photo-album-plus') ?></small>
					<form enctype="multipart/form-data" action="<?php echo( wppa_dbg_url( get_admin_url().'admin.php?page=wppa_upload_photos' ) ) ?>" method="post">
					<?php wp_nonce_field( '$wppa_nonce', WPPA_NONCE ); ?>
						<input id="my_files" type="file" multiple="multiple" name="my_files[]" onchange="showit()" />
						<div id="files_list2">
							<h3><?php _e( 'Selected Files:' , 'wp-photo-album-plus'); ?></h3>

						</div>
						<script type="text/javascript">
							function showit() {
								var maxsize = parseInt( '<?php echo $max_size ?>' ) * 1024 * 1024;
								var maxcount = parseInt( '<?php echo $max_files_txt ?>' );
								var totsize = 0;
								var files = document.getElementById( 'my_files' ).files;
								var tekst = '<h3><?php _e( 'Selected Files:' , 'wp-photo-album-plus') ?></h3>';
								tekst += '<table><thead><tr>';
										tekst += '<td><?php _e( 'Name' , 'wp-photo-album-plus') ?></td><td><?php _e( 'Size' , 'wp-photo-album-plus') ?></td><td><?php _e( 'Type' , 'wp-photo-album-plus') ?></td>';
									tekst += '</tr></thead>';
									tekst += '<tbody>';
										tekst += '<tr><td><hr /></td><td><hr /></td><td><hr /></td></tr>';
										for ( var i=0;i<files.length;i++ ) {
											tekst += '<tr>';
												tekst += '<td>' + files[i].name + '</td>';
												tekst += '<td>' + files[i].size + '</td>';
												totsize += files[i].size;
												tekst += '<td>' + files[i].type + '</td>';
											tekst += '</tr>';
										}
										tekst += '<tr><td><hr /></td><td><hr /></td><td><hr /></td></tr>';
									var style1 = '';
									var style2 = '';
									var style3 = '';
									var warn1 = '';
									var warn2 = '';
									var warn3 = '';
									if ( maxcount > 0 && files.length > maxcount ) {
										style1 = 'color:red';
										warn1 = '<?php _e( 'Too many!' , 'wp-photo-album-plus') ?>';
									}
									if ( maxsize > 0 && totsize > maxsize ) {
										style2 = 'color:red';
										warn2 = '<?php _e( 'Too big!' , 'wp-photo-album-plus') ?>';
									}
									if ( warn1 || warn2 ) {
										style3 = 'color:green';
										warn3 = '<?php _e( 'Try again!' , 'wp-photo-album-plus') ?>';
									}
									tekst += '<tr><td style="'+style1+'" ><?php _e( 'Total' , 'wp-photo-album-plus') ?>: '+files.length+' '+warn1+'</td><td style="'+style2+'" >'+totsize+' '+warn2+'</td><td style="'+style3+'" >'+warn3+'</td></tr>';
									tekst += '</tbody>';
								tekst += '</table>';
								jQuery( '#files_list2' ).html( tekst );
							}
						</script>
						<p>
							<label for="wppa-album"><?php _e( 'Album:' , 'wp-photo-album-plus'); ?> </label>
							<select name="wppa-album" id="wppa-album-s">
								<?php echo wppa_album_select_a( array( 'path' => wppa_switch( 'hier_albsel' ), 'addpleaseselect' => true, 'checkowner' => true, 'checkupload' => true ) ) ?>
							</select>
						</p>
						<?php if ( wppa_switch( 'watermark_on' ) && ( wppa_switch( 'watermark_user' ) || current_user_can( 'wppa_settings' ) ) ) { ?>
							<p>
								<?php _e( 'Apply watermark file:' , 'wp-photo-album-plus') ?>
								<select name="wppa-watermark-file" id="wppa-watermark-file">
									<?php echo( wppa_watermark_file_select() ) ?>
								</select>

								<?php _e( 'Position:' , 'wp-photo-album-plus') ?>
								<select name="wppa-watermark-pos" id="wppa-watermark-pos">
									<?php echo( wppa_watermark_pos_select() ) ?>
								</select>
							</p>
						<?php } ?>
						<input  type="submit" class="button-primary" name="wppa-upload-multiple" value="<?php _e( 'Upload Multiple Photos' , 'wp-photo-album-plus') ?>" onclick="if ( document.getElementById( 'wppa-album-s' ).value == 0 ) { alert( '<?php _e( 'Please select an album' , 'wp-photo-album-plus') ?>' ); return false; }" />
						<input type="checkbox" id="wppa-go-edit-multiple" name="wppa-go-edit-multiple" onchange="wppaCookieCheckbox( this, 'wppa-go-edit-multiple' )" />&nbsp;
						<script type="text/javascript" >
							if ( wppa_getCookie( 'wppa-go-edit-multiple' ) == 'on' ) document.getElementById( 'wppa-go-edit-multiple' ).checked = 'checked';
						</script>
						<?php
						if ( current_user_can( 'wppa_admin' ) ) {
							_e( 'After upload: Go to the <b>Edit Album</b> page.' , 'wp-photo-album-plus');
						}
						else {
							_e( 'After upload: Go to the <b>Edit Photos</b> page.' , 'wp-photo-album-plus');
						}
						?>
					</form>
				</div>
				<?php /* End multiple */ ?>

				<?php /* Single photos */ ?>
				<div style="border:1px solid #ccc; padding:10px; margin-bottom:10px; width: 600px;">
					<h3 style="margin-top:0px;"><?php  _e( 'Box B:' , 'wp-photo-album-plus'); echo ' ';_e( 'Single Photos in multiple selections' , 'wp-photo-album-plus'); ?></h3>
					<?php echo sprintf( __( 'You can select up to %s photos one by one and upload them at once.' , 'wp-photo-album-plus'), $max_files_txt ); ?>
					<form enctype="multipart/form-data" action="<?php echo( wppa_dbg_url( get_admin_url().'admin.php?page=wppa_upload_photos' ) ) ?>" method="post">
					<?php wp_nonce_field( '$wppa_nonce', WPPA_NONCE ); ?>
						<input id="my_file_element" type="file" name="file_1" />
						<div id="files_list">
							<h3><?php _e( 'Selected Files:' , 'wp-photo-album-plus'); ?></h3>

						</div>
						<p>
							<label for="wppa-album"><?php _e( 'Album:' , 'wp-photo-album-plus'); ?> </label>
							<select name="wppa-album" id="wppa-album-m">
								<?php echo wppa_album_select_a( array( 'path' => wppa_switch( 'hier_albsel' ), 'addpleaseselect' => true, 'checkowner' => true, 'checkupload' => true ) );//( '', '', false, false, false, false, false, true ) ); ?>
							</select>
						</p>
						<?php if ( wppa_switch( 'watermark_on' ) && ( wppa_switch( 'watermark_user' ) || current_user_can( 'wppa_settings' ) ) ) { ?>
							<p>
								<?php _e( 'Apply watermark file:' , 'wp-photo-album-plus') ?>
								<select name="wppa-watermark-file" id="wppa-watermark-file">
									<?php echo( wppa_watermark_file_select() ) ?>
								</select>

								<?php _e( 'Position:' , 'wp-photo-album-plus') ?>
								<select name="wppa-watermark-pos" id="wppa-watermark-pos">
									<?php echo( wppa_watermark_pos_select() ) ?>
								</select>
							</p>
						<?php } ?>
						<input type="submit" class="button-primary" name="wppa-upload" value="<?php _e( 'Upload Single Photos' , 'wp-photo-album-plus') ?>" onclick="if ( document.getElementById( 'wppa-album-m' ).value == 0 ) { alert( '<?php _e( 'Please select an album' , 'wp-photo-album-plus') ?>' ); return false; }" />
						<input type="checkbox" id="wppa-go-edit-single" name="wppa-go-edit-single" onchange="wppaCookieCheckbox( this, 'wppa-go-edit-single' )" />&nbsp;
						<script type="text/javascript" >
							if ( wppa_getCookie( 'wppa-go-edit-single' ) == 'on' ) document.getElementById( 'wppa-go-edit-single' ).checked = 'checked';
						</script>
						<?php
						if ( current_user_can( 'wppa_admin' ) ) {
							_e( 'After upload: Go to the <b>Edit Album</b> page.' , 'wp-photo-album-plus');
						}
						else {
							_e( 'After upload: Go to the <b>Edit Photos</b> page.' , 'wp-photo-album-plus');
						}
						?>
					</form>
					<script type="text/javascript">
					<!-- Create an instance of the multiSelector class, pass it the output target and the max number of files -->
						var multi_selector = new MultiSelector( document.getElementById( 'files_list' ), <?php echo( $max_files ) ?> );
					<!-- Pass in the file element -->
						multi_selector.addElement( document.getElementById( 'my_file_element' ) );
					</script>
				</div>
				<?php /* End single photos */ ?>

				<?php /* Single zips */ ?>
				<?php if ( current_user_can( 'wppa_import' ) ) { ?>
					<?php if ( PHP_VERSION_ID >= 50207 ) { ?>
						<div style="border:1px solid #ccc; padding:10px; width: 600px;">
							<h3 style="margin-top:0px;"><?php  _e( 'Box C:' , 'wp-photo-album-plus'); echo ' ';_e( 'Zipped Photos in one selection' , 'wp-photo-album-plus'); ?></h3>
							<?php echo sprintf( __( 'You can upload one zipfile. It will be placed in your personal wppa-depot: <b>.../%s</b><br/>Once uploaded, use <b>Import Photos</b> to unzip the file and place the photos in any album.' , 'wp-photo-album-plus'), WPPA_DEPOT ) ?>
							<form enctype="multipart/form-data" action="<?php echo( wppa_dbg_url( get_admin_url().'admin.php?page=wppa_upload_photos' ) ) ?>" method="post">
							<?php wp_nonce_field( '$wppa_nonce', WPPA_NONCE ); ?>
								<input id="my_zipfile_element" type="file" name="file_zip" /><br/><br/>
								<input type="submit" class="button-primary" name="wppa-upload-zip" value="<?php _e( 'Upload Zipped Photos' , 'wp-photo-album-plus') ?>" />
								<input type="checkbox" id="wppa-go-import" name="wppa-go-import" onchange="wppaCookieCheckbox( this, 'wppa-go-import' )" />&nbsp;
								<script type="text/javascript" >
									if ( wppa_getCookie( 'wppa-go-import' ) == 'on' ) document.getElementById( 'wppa-go-import' ).checked = 'checked';
								</script>
								<?php _e( 'After upload: Go to the <b>Import Photos</b> page.' , 'wp-photo-album-plus') ?>
							</form>
						</div>
					<?php }
					else { ?>
						<div style="border:1px solid #ccc; padding:10px; width: 600px;">
						<?php _e( '<small>Ask your administrator to upgrade php to version 5.2.7 or later. This will enable you to upload zipped photos.</small>' , 'wp-photo-album-plus') ?>
						</div>
					<?php }
				}
			}
		}
	else { ?>
			<?php $url = wppa_dbg_url( get_admin_url().'admin.php?page=wppa_admin_menu' ); ?>
			<p><?php _e( 'No albums exist. You must' , 'wp-photo-album-plus'); ?> <a href="<?php echo( $url ) ?>"><?php _e( 'create one' , 'wp-photo-album-plus'); ?></a> <?php _e( 'beofre you can upload your photos.' , 'wp-photo-album-plus'); ?></p>
<?php } ?>
	</div>
<?php
}

// import images admin page
function _wppa_page_import() {
global $wppa_revno;
global $wpdb;
global $wppa_supported_photo_extensions;
global $wppa_supported_video_extensions;
global $wppa_supported_audio_extensions;
global $wppa_session;

	if ( wppa( 'ajax' ) ) ob_start();	// Suppress output if ajax operation

	// Init
	$ngg_opts 	= get_option( 'ngg_options', false );
	$user 		= wppa_get_user();

	// Check database
	wppa_check_database( true );

	// Update watermark settings
	if ( wppa_switch( 'watermark_on' ) && ( wppa_switch( 'watermark_user' ) || current_user_can( 'wppa_settings' ) ) ) {
		if ( isset( $_POST['wppa-watermark-file'] ) ) update_option( 'wppa_watermark_file_'.$user, $_POST['wppa-watermark-file'] );
		if ( isset( $_POST['wppa-watermark-pos'] ) ) update_option( 'wppa_watermark_pos_'.$user, $_POST['wppa-watermark-pos'] );
	}

	// Update last used albums
	if ( isset( $_POST['wppa-photo-album'] ) ) {
		update_option( 'wppa-photo-album-import-'.wppa_get_user(), $_POST['wppa-photo-album'] );	// nog in form verwerken
	}
	if ( isset( $_POST['wppa-video-album'] ) ) {
		update_option( 'wppa-video-album-import-'.wppa_get_user(), $_POST['wppa-video-album'] );
	}
	if ( isset( $_POST['wppa-audio-album'] ) ) {
		update_option( 'wppa-audio-album-import-'.wppa_get_user(), $_POST['wppa-audio-album'] );
	}

	// Verify last albums still exist
	$alb = get_option( 'wppa-photo-album-import-'.wppa_get_user(), '0' );
	if ( $alb ) {
		$exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `" . WPPA_ALBUMS . "` WHERE `id` = %s", $alb ) );
		if ( ! $exists ) update_option( 'wppa-photo-album-import-'.wppa_get_user(), '0' );
	}
	$alb = get_option( 'wppa-video-album-import-'.wppa_get_user(), '0' );
	if ( $alb ) {
		$exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `" . WPPA_ALBUMS . "` WHERE `id` = %s", $alb ) );
		if ( ! $exists ) update_option( 'wppa-video-album-import-'.wppa_get_user(), '0' );
	}
	$alb = get_option( 'wppa-audio-album-import-'.wppa_get_user(), '0' );
	if ( $alb ) {
		$exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `" . WPPA_ALBUMS . "` WHERE `id` = %s", $alb ) );
		if ( ! $exists ) update_option( 'wppa-audio-album-import-'.wppa_get_user(), '0' );
	}

	// Extract zip
	if ( isset( $_GET['zip'] ) ) {
		wppa_extract( $_GET['zip'], true );
	}

	// Set local / remote
	if ( isset( $_POST['wppa-local-remote'] ) ) {
		check_admin_referer( '$wppa_nonce', WPPA_NONCE );
		update_option( 'wppa_import_source_type_'.$user, $_POST['wppa-local-remote'] );
	}

	// Set import source dir ( when local )
	if ( isset( $_POST['wppa-import-set-source-dir'] ) ) {
		check_admin_referer( '$wppa_nonce', WPPA_NONCE );
		if ( isset( $_POST['wppa-source'] ) ) {
			update_option( 'wppa_import_source_'.$user, $_POST['wppa-source'] );
		}
	}

	// Set import source url ( when remote )
	if ( isset( $_POST['wppa-import-set-source-url'] ) ) {
		check_admin_referer( '$wppa_nonce', WPPA_NONCE );
		if ( isset( $_POST['wppa-source-remote'] ) ) {
			update_option( 'wppa_import_source_url_'.$user, $_POST['wppa-source-remote'] );
			update_option( 'wppa_import_source_url_found_'.$user, false );
			update_option( 'wppa_import_remote_max_'.$user, strval( intval( $_POST['wppa-import-remote-max'] ) ) );
		}
	}

	// Hit the submit button
	if ( isset( $_POST['wppa-import-submit'] ) ) {
		if ( wppa( 'ajax' ) ) {
			if ( ! wp_verify_nonce( $_POST['wppa-update-check'], '$wppa_nonce' ) ) {
				echo $_POST['wppa-update-check'].' Security check failure';
				wppa_exit();
			}
		}
		else {
			check_admin_referer( '$wppa_nonce', WPPA_NONCE );
		}
        if ( isset( $_POST['del-after-p'] ) ) $delp = true; else $delp = false;
		if ( isset( $_POST['del-after-f'] ) ) $delf = true; else $delf = false;
		if ( isset( $_POST['del-after-a'] ) ) $dela = true; else $dela = false;
		if ( isset( $_POST['del-after-z'] ) ) $delz = true; else $delz = false;
		if ( isset( $_POST['del-after-v'] ) ) $delv = true; else $delv = false;
		if ( isset( $_POST['del-after-u'] ) ) $delu = true; else $delu = false;
		if ( isset( $_POST['del-after-c'] ) ) $delc = true; else $delc = false;

		wppa_import_photos( $delp, $dela, $delz, $delv, $delu, $delc, $delf );
	}

	// Continue dirimport after timeout
	elseif ( isset( $_GET['continue'] ) ) {
		if ( wp_verify_nonce( $_GET['nonce'], 'dirimport' ) ) wppa_import_photos();
	}

	// If we did this by ajax, setup reporting results for it
	if ( wppa( 'ajax' ) ) {
		ob_end_clean();
		if ( wppa( 'ajax_import_files_done' ) ) {
			echo '<span style="color:green" >' . wppa( 'ajax_import_files' ) . ' ' . __( 'Done!', 'wp-photo-album-plus' ) . '</span>';
		}
		elseif ( wppa( 'ajax_import_files_error' ) ) {
			echo '<span style="color:red" >' . wppa( 'ajax_import_files' ) . ' ' . wppa( 'ajax_import_files_error' ) . '</span>';
		}
		else {
			echo '<span style="color:red" >' . wppa( 'ajax_import_files' ) . ' ' . __( 'Failed!', 'wp-photo-album-plus' ) . '</span>';
		}
		wppa_exit();
	}

?>

	<div class="wrap">
		<?php $iconurl = WPPA_URL.'/images/camera32.png'; ?>
		<div id="icon-camera" class="icon32" style="background: transparent url( <?php echo( $iconurl ); ?> ) no-repeat"></div>
		<?php $iconurl = WPPA_URL.'/images/arrow32.png'; ?>
		<div id="icon-arrow" class="icon32" style="background: transparent url( <?php echo( $iconurl ); ?> ) no-repeat"></div>
		<?php $iconurl = WPPA_URL.'/images/album32.png'; ?>
		<div id="icon-album" class="icon32" style="background: transparent url( <?php echo( $iconurl ); ?> ) no-repeat"><br /></div>

		<h2><?php _e( 'Import Photos' , 'wp-photo-album-plus'); ?></h2><br />
		<?php

		// Get this users current source directory setting
		$can_remote = ini_get( 'allow_url_fopen' ) && function_exists( 'curl_init' );
		// $can_remote = false; // Debug
		if ( ! $can_remote ) {
			update_option( 'wppa_import_source_type_'.$user, 'local' );
		}
		$source_type = get_option( 'wppa_import_source_type_'.$user, 'local' );
		if ( $source_type == 'local' ) {
			$source      = get_option( 'wppa_import_source_'.$user, WPPA_DEPOT_PATH );
			if ( ! $source || ! is_dir( $source ) ) {
				$source = WPPA_DEPOT_PATH;
				update_option( 'wppa_import_source_'.$user, WPPA_DEPOT_PATH );
			}

			// See if the current source is the 'home' directory
			$is_depot 	= ( $source == WPPA_DEPOT_PATH );

			// See if the current source is a subdir of my depot
			$is_sub_depot = ( substr( $source, 0, strlen( WPPA_DEPOT_PATH ) ) == WPPA_DEPOT_PATH );

			// Sanitize system
			if ( $is_sub_depot ) {
				wppa_sanitize_files();
			}

			// See what's in there
			$files 		= wppa_get_import_files();
			$zipcount 	= wppa_get_zipcount( $files );
			$albumcount = wppa_get_albumcount( $files );
			$photocount = wppa_get_photocount( $files );
			$videocount = wppa_get_video_count( $files );
			$audiocount = wppa_get_audio_count( $files );
			$dircount	= $is_depot ? wppa_get_dircount( $files ) : '0';
			$csvcount 	= $is_depot ? wppa_get_csvcount( $files ) : '0';
			// echo 'zips:'.$zipcount,' albs:'.$albumcount.' pho:'.$photocount.' dirs:'.$dircount;
			if ( $ngg_opts ) {
				$is_ngg = strpos( $source, $ngg_opts['gallerypath'] ) !== false;	// this is false for the ngg root !!
			}
			else $is_ngg = false;
		}
		if ( $source_type == 'remote' ) {
			wppa( 'is_remote', true );
			$source     	= get_option( 'wppa_import_source_url_' . $user, 'http://' );
			$source_path 	= $source;
			$source_url 	= $source;
			$is_depot 		= false;
			$is_sub_depot 	= false;
			$files 			= wppa_get_import_files();
			$zipcount 		= '0';
			$albumcount 	= '0';
			$photocount 	= $files ? count( $files ) : '0';
			$videocount 	= '0';
			$audiocount 	= '0';
			$dircount		= '0';
			$csvcount 		= '0';
			$is_ngg 		= false;
			$remote_max 	= get_option( 'wppa_import_remote_max_'.$user, '10' );
		}

?>
	<form action="<?php echo( wppa_dbg_url( get_admin_url().'admin.php?page=wppa_import_photos' ) ) ?>" method="post">
		<?php if ( wppa_user_is( 'administrator' ) || ! wppa_switch( 'chgsrc_is_restricted' ) ) { ?>

		<div style="border:1px solid gray; padding:4px; margin: 3px 0;" >
			<?php
				wp_nonce_field( '$wppa_nonce', WPPA_NONCE );
				_e( 'Select Local or Remote' , 'wp-photo-album-plus');
				$disabled = $can_remote ? '' : 'disabled="disabled"';
			?>
			<select name="wppa-local-remote" >
				<option value="local" <?php if ( $source_type == 'local' ) echo 'selected="selected"' ?>><?php _e( 'Local' , 'wp-photo-album-plus') ?></option>
				<option value="remote" <?php echo $disabled; if ( $source_type == 'remote' ) echo 'selected="selected"' ?>><?php _e( 'Remote' , 'wp-photo-album-plus') ?></option>
			</select>
			<?php if ( $can_remote ) { ?>
				<input type="submit" class="button-secundary" name="wppa-import-set-source" value="<?php _e( 'Set Local/Remote' , 'wp-photo-album-plus'); ?>" />
			<?php } else {
				if ( ! ini_get( 'allow_url_fopen' ) )
					_e( 'The server does not allow you to import from remote locations. ( The php directive allow_url_fopen is not set to 1 )' , 'wp-photo-album-plus');
				if ( ! function_exists( 'curl_init' ) )
					_e( 'The server does not allow you to import from remote locations. ( The curl functions are not set up )' , 'wp-photo-album-plus');
			} ?>
		</div>
		<div style="border:1px solid gray; padding:4px; margin: 3px 0;" >
			<?php wp_nonce_field( '$wppa_nonce', WPPA_NONCE ); ?>
			<?php _e( 'Import photos from:' , 'wp-photo-album-plus'); ?>
			<?php if ( $source_type == 'local' ) { ?>
			<select name="wppa-source">
				<?php wppa_abs_walktree( wppa_opt('import_root'), $source ) ?>
			</select>
			<input type="submit" class="button-secundary" name="wppa-import-set-source-dir" value="<?php _e( 'Set source directory' , 'wp-photo-album-plus'); ?>" />
			<?php } else { ?>
			<input type="text" style="width:50%" name="wppa-source-remote" value="<?php echo $source ?>" />
			<?php _e( 'Max:' , 'wp-photo-album-plus') ?>
			<input type="text" style="width:50px;" name="wppa-import-remote-max" value="<?php echo $remote_max ?>" />
			<input type="submit" onclick="jQuery( '#rem-rem' ).css( 'display','inline' ); return true;" class="button-secundary" name="wppa-import-set-source-url" value="<?php _e( 'Find remote photos' , 'wp-photo-album-plus'); ?>" />
			<span id="rem-rem" style="display:none;"><?php _e( 'Working, please wait...' , 'wp-photo-album-plus') ?></span>
			<?php _e( '<br />You can enter either a web page address like <i>http://mysite.com/mypage/</i> or a full url to an image file like <i>http://mysite.com/wp-content/uploads/wppa/4711.jpg</i>' , 'wp-photo-album-plus'); ?>
			<?php } ?>
		</div>

		<?php } ?>
	</form>

	<?php
		// check if albums exist or will be made before allowing upload
		if ( wppa_has_albums() || $albumcount > '0' || $zipcount >'0' || $dircount > '0' || $videocount > '0' || $audiocount > '0' || $csvcount > '0' ) {

			if ( $photocount > '0' || $albumcount > '0' || $zipcount >'0' || $dircount > '0' || $videocount > '0' || $audiocount > '0' || $csvcount > '0' ) { ?>

				<form action="<?php echo( wppa_dbg_url( get_admin_url().'admin.php?page=wppa_import_photos' ) ) ?>" method="post">
				<?php wp_nonce_field( '$wppa_nonce', WPPA_NONCE );

				// Display the zips
				if ( PHP_VERSION_ID >= 50207 && $zipcount > '0' ) { ?>
					<div style="border:1px solid gray; padding:4px; margin: 3px 0;" >
						<p><b>
							<?php _e( 'There are' , 'wp-photo-album-plus'); echo( ' '.$zipcount.' ' ); _e( 'zipfiles in the depot.' , 'wp-photo-album-plus') ?><br/>
						</b></p>
						<table class="form-table wppa-table widefat" style="margin-bottom:0;" >
							<thead>
								<tr>
									<td>
										<input type="checkbox" id="all-zip" checked="checked" onchange="checkAll( 'all-zip', '.wppa-zip' )" /><b>&nbsp;&nbsp;<?php _e( 'Check/uncheck all' , 'wp-photo-album-plus') ?></b>
									</td>
									<?php if ( $is_sub_depot ) { ?>
										<td>
											<input type="checkbox" id="del-after-z" name="del-after-z" checked="checked" /><b>&nbsp;&nbsp;<?php _e( 'Delete after successful extraction.' , 'wp-photo-album-plus'); ?></b>
										</td>
									<?php } ?>
								</tr>
							</thead>
						</table>
						<table class="form-table wppa-table widefat" style="margin-top:0;" >
							<tr>
								<?php
								$ct = 0;
								$idx = '0';
								foreach ( $files as $file ) {

									$ext = strtolower( substr( strrchr( $file, "." ), 1 ) );
									if ( $ext == 'zip' ) { ?>
										<td>
											<input type="checkbox" id="file-<?php echo( $idx ) ?>" name="file-<?php echo( $idx ) ?>" class="wppa-zip" checked="checked" />&nbsp;&nbsp;<?php echo( wppa_sanitize_file_name( basename( $file ) ) ); ?>
										</td>
										<?php if ( $ct == 3 ) {
											echo( '</tr><tr>' );
											$ct = 0;
										}
										else {
											$ct++;
										}
									}
									$idx++;
								} ?>
							</tr>
						</table>
					</div>
				<?php }

				// Dispay the albums ( .amf files )
				if ( $albumcount > '0' ) { ?>
					<div style="border:1px solid gray; padding:4px; margin: 3px 0;" >
						<p><b>
							<?php _e( 'There are' , 'wp-photo-album-plus'); echo( ' '.$albumcount.' ' ); _e( 'albumdefinitions in the depot.' , 'wp-photo-album-plus') ?><br/>
						</b></p>
						<table class="form-table wppa-table widefat" style="margin-bottom:0;" >
							<thead>
								<tr>
									<td>
										<input type="checkbox" id="all-amf" checked="checked" onchange="checkAll( 'all-amf', '.wppa-amf' )" /><b>&nbsp;&nbsp;<?php _e( 'Check/uncheck all' , 'wp-photo-album-plus') ?></b>
									</td>
									<?php if ( $is_sub_depot ) { ?>
										<td>
											<input type="checkbox" id="del-after-a" name="del-after-a" checked="checked" /><b>&nbsp;&nbsp;<?php _e( 'Remove from depot after successful import, or if the album already exists.' , 'wp-photo-album-plus'); ?></b>
										</td>
									<?php } ?>
								</tr>
							</thead>
						</table>
						<table class="form-table wppa-table widefat"  style="margin-top:0;" >
							<tr>
								<?php
								$ct = 0;
								$idx = '0';
								foreach ( $files as $file ) {
									$ext = strtolower( substr( strrchr( $file, "." ), 1 ) );
									if ( $ext == 'amf' ) { ?>
										<td>
											<input type="checkbox" id="file-<?php echo( $idx ) ?>" name="file-<?php echo( $idx ) ?>" class="wppa-amf" checked="checked" />&nbsp;&nbsp;<?php echo( basename( $file ) ); ?>&nbsp;<?php echo( stripslashes( wppa_get_meta_name( $file, '( ' ) ) ) ?>
										</td>
										<?php if ( $ct == 3 ) {
											echo( '</tr><tr>' );
											$ct = 0;
										}
										else {
											$ct++;
										}
									}
									$idx++;
								} ?>
							</tr>
						</table>
					</div>
				<?php }

				// Display the single photos
				if ( $photocount > '0' ) { ?>
					<div style="border:1px solid gray; padding:4px; margin: 3px 0;" >
						<p><b>
							<?php _e( 'There are' , 'wp-photo-album-plus'); echo( ' '.$photocount.' ' );
								if ( $source_type == 'local' ) {
									if ( $is_ngg ) {
										_e( 'photos in the ngg gallery.' , 'wp-photo-album-plus');
									}
									else _e( 'photos in the depot.' , 'wp-photo-album-plus');
								}
								else _e( 'possible photos found remote.' , 'wp-photo-album-plus');
								if ( wppa_switch( 'resize_on_upload' ) ) { echo( ' ' ); _e( 'Photos will be downsized during import.' , 'wp-photo-album-plus'); } ?><br/>
						</b></p>
						<p class="hideifupdate" >
							<?php _e( 'Default album for import:' , 'wp-photo-album-plus') ?>
							<select name="wppa-photo-album" id="wppa-photo-album">
								<?php echo wppa_album_select_a( array( 	'path' 				=> wppa_switch( 'hier_albsel' ),
																		'selected' 			=> get_option( 'wppa-photo-album-import-'.wppa_get_user(), '0' ),
																		'addpleaseselect' 	=> true,
																		'checkowner' 		=> true,
																		'checkupload' 		=> true
																	) ) ?>
							</select>
							<?php _e( 'Photos that have (<em>name</em>)[<em>album</em>] will be imported by that <em>name</em> in that <em>album</em>.' , 'wp-photo-album-plus') ?>
						</p>
						<?php if ( wppa_switch( 'watermark_on' ) && ( wppa_switch( 'watermark_user' ) || current_user_can( 'wppa_settings' ) ) ) { ?>
							<p>
								<?php _e( 'Apply watermark file:' , 'wp-photo-album-plus') ?>
								<select name="wppa-watermark-file" id="wppa-watermark-file">
									<?php echo( wppa_watermark_file_select() ) ?>
								</select>
								<?php _e( 'Position:' , 'wp-photo-album-plus') ?>
								<select name="wppa-watermark-pos" id="wppa-watermark-pos">
									<?php echo( wppa_watermark_pos_select() ) ?>
								</select>
							</p>
						<?php } ?>
						<table class="form-table wppa-table widefat" style="margin-bottom:0;" >
							<thead>
								<tr>
									<td>
										<input type="checkbox" id="all-pho" <?php if ( $is_sub_depot ) echo( 'checked="checked"' ) ?> onchange="checkAll( 'all-pho', '.wppa-pho' )" /><b>&nbsp;&nbsp;<?php _e( 'Check/uncheck all' , 'wp-photo-album-plus') ?></b>
									</td>
									<?php if ( $is_sub_depot ) { ?>
										<td>
											<input type="checkbox" id="del-after-p" name="del-after-p" checked="checked" /><b>&nbsp;&nbsp;<?php _e( 'Remove from depot after successful import.' , 'wp-photo-album-plus'); ?></b>
										</td>
										<td>
											<input type="checkbox" id="del-after-f" name="del-after-f" /><b>&nbsp;&nbsp;<?php _e( 'Remove from depot after failed import.' , 'wp-photo-album-plus'); ?></b>
										</td>
									<?php } ?>
									<?php if ( $is_ngg ) { ?>
										<td>
											<input type="checkbox" id="cre-album" name="cre-album" checked="checked" value="<?php echo esc_attr( basename( $source ) ) ?>" /><b>&nbsp;&nbsp;<?php echo sprintf( __( 'Import into album <i>%s</i>.' , 'wp-photo-album-plus'), basename( $source ) ); ?></b>
											<br /><small><?php _e( 'The album will be created if it does not exist' , 'wp-photo-album-plus') ?></small>
										</td>
										<td>
											<input type="checkbox" id="use-backup" name="use-backup" checked="checked" /><b>&nbsp;&nbsp;<?php _e( 'Use backup if available' , 'wp-photo-album-plus') ?></b>
										</td>
									<?php } ?>
									<td>
										<input type="checkbox" id="wppa-update" onchange="impUpd( this, '#submit' )" name="wppa-update"><b>&nbsp;&nbsp;<?php _e( 'Update existing photos' , 'wp-photo-album-plus') ?></b>
									</td>
									<td>
									<?php if ( wppa_switch( 'void_dups' ) ) { ?>
										<input type="hidden" id="wppa-nodups" name="wppa-nodups" value="true" />
									<?php } else { ?>
										<input type="checkbox" id="wppa-nodups" name="wppa-nodups" checked="checked" ><b>&nbsp;&nbsp;<?php _e( 'Do not create duplicates' , 'wp-photo-album-plus') ?></b>
									<?php } ?>
									</td>
									<?php
									if ( wppa_switch( 'import_preview' ) ) { ?>
										<td>
											<input type="checkbox" id="wppa-zoom" onclick="wppa_setCookie('zoompreview', this.checked, '365')" ><b>&nbsp;&nbsp;<?php _e( 'Zoom previews' , 'wp-photo-album-plus') ?></b>
											<script type="text/javascript">if ( wppa_getCookie('zoompreview') == true ) { jQuery('#wppa-zoom').attr('checked', 'checked') }</script>
										</td>
									<?php } ?>
								</tr>
							</thead>
						</table>
						<table class="form-table wppa-table widefat" style="margin-top:0;" >
							<tr>
								<?php
								$ct = 0;
								$idx = '0';
								if ( is_array( $files ) ) foreach ( $files as $file ) {
									$ext = wppa_get_ext( $file ); //strtolower( substr( strrchr( $file, "." ), 1 ) );
									$meta =	wppa_strip_ext( $file ).'.PMF';
									if ( ! is_file( $meta ) ) {
										$meta =	wppa_strip_ext( $file ).'.pmf';
									}
									if ( in_array( strtolower($ext), $wppa_supported_photo_extensions ) ) { ?>
										<td id="td-file-<?php echo( $idx ) ?>" >
											<input type="checkbox" id="file-<?php echo( $idx ) ?>" name="file-<?php echo( $idx ) ?>" title="<?php echo $file ?>" class= "wppa-pho" <?php if ( $is_sub_depot ) echo( 'checked="checked"' ) ?> />
											<span id="name-file-<?php echo( $idx ) ?>" >&nbsp;&nbsp;
												<?php
												if ( wppa( 'is_wppa_tree' ) ) {
													$t = explode( '/wppa/', $file );
													echo $t[1];
												}
												else {
													echo( wppa_sanitize_file_name( basename( $file ) ) );
												}
												?>&nbsp;
												<?php echo( stripslashes( wppa_get_meta_name( $meta, '( ' ) ) ) ?>
												<?php echo( stripslashes( wppa_get_meta_album( $meta, '[' ) ) ) ?>
											</span>
											<?php
											if ( wppa_switch( 'import_preview' ) ) {
												if ( wppa( 'is_remote' ) ) {
													if ( strpos( $file, '//res.cloudinary.com/' ) !== false ) {
														$img_url = dirname( $file ) . '/h_144/' . basename( $file );
													}
													else {
														$img_url = $file;
													}
												}
												else {
													$img_url = str_replace( ABSPATH, home_url().'/', $file );
												}
											?>
											<img src="<?php echo $img_url ?>" alt="N.A." style="max-height:48px;" onmouseover="if (jQuery('#wppa-zoom').attr('checked')) jQuery(this).css('max-height', '144px')" onmouseout="if (jQuery('#wppa-zoom').attr('checked')) jQuery(this).css('max-height', '48px')" />
											<?php } ?>
										</td>
										<?php if ( $ct == 3 ) {
											echo( '</tr><tr>' );
											$ct = 0;
										}
										else {
											$ct++;
										}
									}
									$idx++;
								} ?>
							</tr>
						</table>
					</div>
				<?php }

				// Display the videos
				if ( $videocount > '0' && wppa_switch( 'enable_video' ) ) { ?>
					<div style="border:1px solid gray; padding:4px; margin: 3px 0;" >
						<p><b>
							<?php _e( 'There are' , 'wp-photo-album-plus'); echo( ' '.$videocount.' ' ); _e( 'videos in the depot.' , 'wp-photo-album-plus') ?><br/>
						</b></p>
						<p class="hideifupdate" >
							<?php _e( 'Album to import to:' , 'wp-photo-album-plus') ?>
							<select name="wppa-video-album" id="wppa-video-album">
								<?php echo wppa_album_select_a( array( 	'path' 				=> wppa_switch( 'hier_albsel' ),
																		'selected' 			=> get_option( 'wppa-video-album-import-'.wppa_get_user(), '0' ),
																		'addpleaseselect'	=> true,
																		'checkowner' 		=> true,
																		'checkupload' 		=> true
																	) ) ?>
							</select>
						</p>
						<table class="form-table wppa-table widefat" style="margin-bottom:0;" >
							<thead>
								<tr>
									<td>
										<input type="checkbox" id="all-video" checked="checked" onchange="checkAll( 'all-video', '.wppa-video' )" /><b>&nbsp;&nbsp;<?php _e( 'Check/uncheck all' , 'wp-photo-album-plus') ?></b>
									</td>
									<?php if ( $is_sub_depot ) { ?>
										<td>
											<input type="checkbox" id="del-after-v" name="del-after-v" checked="checked" /><b>&nbsp;&nbsp;<?php _e( 'Remove from depot after successful import.' , 'wp-photo-album-plus'); ?></b>
										</td>
									<?php } ?>
								</tr>
							</thead>
						</table>
						<table class="form-table wppa-table widefat" style="margin-top:0;" >
							<tr>
								<?php
								$ct = 0;
								$idx = '0';
								if ( is_array( $files ) ) foreach ( $files as $file ) {
									$ext = strtolower( substr( strrchr( $file, "." ), 1 ) );
									if ( in_array( strtolower($ext), $wppa_supported_video_extensions ) ) { ?>
										<td>
											<input type="checkbox" id="file-<?php echo( $idx ) ?>" name="file-<?php echo( $idx ) ?>" title="<?php echo $file ?>" class="wppa-video" checked="checked" /><span id="name-file-<?php echo( $idx ) ?>" >&nbsp;&nbsp;<?php echo( wppa_sanitize_file_name( basename( $file ) ) ); ?></span>
										</td>
										<?php if ( $ct == 3 ) {
											echo( '</tr><tr>' );
											$ct = 0;
										}
										else {
											$ct++;
										}
									}
									$idx++;
								} ?>
							</tr>
						</table>
					</div>
				<?php }

				// Display the audios
				if ( $audiocount > '0' && wppa_switch( 'enable_audio' ) ) { ?>
					<div style="border:1px solid gray; padding:4px; margin: 3px 0;" >
						<p><b>
							<?php _e( 'There are' , 'wp-photo-album-plus'); echo( ' '.$audiocount.' ' ); _e( 'audios in the depot.' , 'wp-photo-album-plus') ?><br/>
						</b></p>
						<p class="hideifupdate" >
							<?php _e( 'Album to import to:' , 'wp-photo-album-plus') ?>
							<select name="wppa-audio-album" id="wppa-audio-album">
								<?php echo wppa_album_select_a( array( 	'path' 				=> wppa_switch( 'hier_albsel' ),
																		'selected' 			=> get_option( 'wppa-audio-album-import-'.wppa_get_user(), '0' ),
																		'addpleaseselect' 	=> true,
																		'checkowner' 		=> true,
																		'checkupload' 		=> true
																	) ) ?>
							</select>
						</p>
						<table class="form-table wppa-table widefat" style="margin-bottom:0;" >
							<thead>
								<tr>
									<td>
										<input type="checkbox" id="all-audio" checked="checked" onchange="checkAll( 'all-audio', '.wppa-audio' )" /><b>&nbsp;&nbsp;<?php _e( 'Check/uncheck all' , 'wp-photo-album-plus') ?></b>
									</td>
									<?php if ( $is_sub_depot ) { ?>
										<td>
											<input type="checkbox" id="del-after-u" name="del-after-u" checked="checked" /><b>&nbsp;&nbsp;<?php _e( 'Remove from depot after successful import.' , 'wp-photo-album-plus'); ?></b>
										</td>
									<?php } ?>
								</tr>
							</thead>
						</table>
						<table class="form-table wppa-table widefat" style="margin-top:0;" >
							<tr>
								<?php
								$ct = 0;
								$idx = '0';
								if ( is_array( $files ) ) foreach ( $files as $file ) {
									$ext = strtolower( substr( strrchr( $file, "." ), 1 ) );
									if ( in_array( strtolower($ext), $wppa_supported_audio_extensions ) ) { ?>
										<td>
											<input type="checkbox" id="file-<?php echo( $idx ) ?>" name="file-<?php echo( $idx ) ?>" title="<?php echo $file ?>" class="wppa-audio" checked="checked" /><span id="name-file-<?php echo( $idx ) ?>" >&nbsp;&nbsp;<?php echo( wppa_sanitize_file_name( basename( $file ) ) ); ?></span>
										</td>
										<?php if ( $ct == 3 ) {
											echo( '</tr><tr>' );
											$ct = 0;
										}
										else {
											$ct++;
										}
									}
									$idx++;
								} ?>
							</tr>
						</table>
					</div>
				<?php }

				// Display the directories to be imported as albums. Do this in the depot only!!
				if ( $is_depot && $dircount > '0' ) { ?>
					<div style="border:1px solid gray; padding:4px; margin: 3px 0;" >
						<p><b>
							<?php _e( 'There are' , 'wp-photo-album-plus'); echo( ' '.$dircount.' ' ); _e( 'albumdirectories in the depot.' , 'wp-photo-album-plus') ?><br/>
						</b></p>
						<table class="form-table wppa-table widefat" style="margin-bottom:0;" >
							<thead>
								<tr>
									<td>
										<input type="checkbox" id="all-dir" checked="checked" onchange="checkAll( 'all-dir', '.wppa-dir' )" /><b>&nbsp;&nbsp;<?php _e( 'Check/uncheck all' , 'wp-photo-album-plus') ?></b>
									</td>
								</tr>
							</thead>
						</table>
						<table class="form-table wppa-table widefat" style="margin-top:0;" >
							<?php
								$ct = 0;
								$idx = '0';
								foreach( $files as $dir ) {
									if ( basename( $dir ) == '.' ) {}
									elseif ( basename( $dir ) == '..' ) {}
									elseif ( is_dir( $dir ) ) { ?>
										<tr>
											<td>
												<input type="checkbox" id="file-<?php echo( $idx ) ?>" name="file-<?php echo( $idx ) ?>" class= "wppa-dir" checked="checked" />&nbsp;&nbsp;<b><?php echo( wppa_sanitize_file_name( basename( $dir ) ) ) ?></b>
												<?php
													$subfiles = glob( $dir.'/*' );
													$subdircount = '0';
													if ( $subfiles ) foreach ( $subfiles as $subfile ) if ( is_dir( $subfile ) && basename( $subfile ) != '.' && basename( $subfile ) != '..' ) $subdircount++;
													$sfcount = empty( $subfiles ) ? '0' : wppa_get_photocount( $subfiles );
													echo ' Contains '.$sfcount.' files';
													if ( $subdircount ) echo ' and '.$subdircount.' sub directories.';
												?>
											</td>
										</tr>
									<?php
									}
									$idx++;
								} ?>
						</table>
					</div>
				<?php } ?>
				<?php

				// Display the csv files
				if ( $is_depot && $csvcount > '0' ) { ?>
					<div style="border:1px solid gray; padding:4px; margin: 3px 0;" >
						<p><b>
							<?php _e( 'There are' , 'wp-photo-album-plus'); echo( ' '.$csvcount.' ' ); _e( 'csv files in the depot.' , 'wp-photo-album-plus') ?><br/>
						</b></p>
						<table class="form-table wppa-table widefat" style="margin-bottom:0;" >
							<thead>
								<tr>
									<td>
										<input type="checkbox" id="all-csv" checked="checked" onchange="checkAll( 'all-csv', '.wppa-csv' )" /><b>&nbsp;&nbsp;<?php _e( 'Check/uncheck all' , 'wp-photo-album-plus') ?></b>
									</td>
									<td>
										<input type="checkbox" id="del-after-c" name="del-after-c" checked="checked" disabled="disabled" /><b>&nbsp;&nbsp;<?php _e( 'Remove from depot after successful import.' , 'wp-photo-album-plus'); ?></b>
									</td>
								</tr>
							</thead>
						</table>
						<table class="form-table wppa-table widefat" style="margin-top:0;" >
							<?php
								$ct = 0;
								$idx = '0';
								foreach( $files as $csv ) {
									if ( is_file( $csv ) && strtolower( wppa_get_ext( $csv ) ) == 'csv' ) { ?>
										<tr>
											<td>
												<input type="checkbox" id="file-<?php echo( $idx ) ?>" name="file-<?php echo( $idx ) ?>" class= "wppa-csv" checked="checked" />&nbsp;&nbsp;<b><?php echo wppa_sanitize_file_name( basename( $csv ) ) .  ' (' . sprintf( '%5.1f', filesize( $csv ) / 1024 ) . ' kb)' ?></b>
											</td>
										</tr>
									<?php
									}
									$idx++;
								} ?>
						</table>
					</div>

				<?php } ?>
				<?php

				// The submit button
				?>
				<p>
					<script type="text/javascript">
						function wppaVfyAlbum() {
							var csvs = jQuery( '.wppa-csv' );
							if ( jQuery( '#wppa-update' ).attr( 'checked' ) != 'checked' ) {
								if ( 	! parseInt( jQuery( '#wppa-photo-album' ).attr( 'value' ) ) &&
										! parseInt( jQuery( '#wppa-video-album' ).attr( 'value' ) ) &&
										! parseInt( jQuery( '#wppa-audio-album' ).attr( 'value' ) ) &&
										csvs.length == 0
									) {
									alert( 'Please select an album first' );
									return false;
								}
							}
							return true;
						}
						function wppaCheckInputVars() {
							var checks = jQuery( ':checked' );
							var nChecks = checks.length;
							var nMax = <?php echo ini_get( 'max_input_vars' ) ?>;
							if ( nMax == 0 ) nMax = 100;
							if ( nChecks > nMax ) {
								alert ( 'There are '+nChecks+' boxes checked or selected, that is more than the maximum allowed number of '+nMax );
								return false;
							}
							var dirs = jQuery( '.wppa-dir' );
							var nDirsChecked = 0;
							if ( dirs.length > 0 ) {
								var i = 0;
								while ( i < dirs.length ) {
									if ( jQuery( dirs[i] ).attr( 'checked' ) == 'checked' ) {
										nDirsChecked++;
									}
									i++;
								}
							}
							var zips = jQuery( '.wppa-zip' );
							var nZipsChecked = 0;
							if ( zips.length > 0 ) {
								var i = 0;
								while ( i < zips.length ) {
									if ( jQuery( zips[i] ).attr( 'checked' ) == 'checked' ) {
										nZipsChecked++;
									}
									i++;
								}
							}
							// If no dirs to import checked, there must be an album selected
							if ( 0 == nDirsChecked && 0 == nZipsChecked && ! wppaVfyAlbum() ) return false;
							return true;
						}
					</script>
					<input type="submit" onclick="return wppaCheckInputVars()" class="button-primary" id="submit" name="wppa-import-submit" value="<?php _e( 'Import' , 'wp-photo-album-plus'); ?>" />
					<script type="text/javascript" >
						var wppaImportRuns = false;
						function wppaDoAjaxImport() {
							wppaImportRuns = true;
							var data = '';
							data += 'wppa-update-check='+jQuery( '#wppa-update-check' ).attr( 'value' );
							data += '&wppa-photo-album='+jQuery( '#wppa-photo-album' ).attr( 'value' );
							data += '&wppa-video-album='+jQuery( '#wppa-video-album' ).attr( 'value' );
							data += '&wppa-audio-album='+jQuery( '#wppa-audio-album' ).attr( 'value' );
							data += '&wppa-watermark-file='+jQuery( '#wppa-watermark-file' ).attr( 'value' );
							data += '&wppa-watermark-pos='+jQuery( '#wppa-watermark-pos' ).attr( 'value' );
							if ( jQuery( '#cre-album' ).attr( 'checked' ) ) data += '&cre-album='+jQuery( '#cre-album' ).attr( 'value' );
							if ( jQuery( '#use-backup' ).attr( 'checked' ) ) data += '&use-backup=on'; //+jQuery( '#use-backup' ).attr( 'value' );
							if ( jQuery( '#wppa-update' ).attr( 'checked' ) ) data += '&wppa-update=on'; //+jQuery( '#wppa-update' ).attr( 'value' );
							if ( jQuery( '#wppa-nodups' ).attr( 'checked' ) ) data += '&wppa-nodups=on'; //+jQuery( '#wppa-nudups' ).attr( 'value' );
							if ( jQuery( '#del-after-p' ).attr( 'checked' ) ) data += '&del-after-p=on';
							if ( jQuery( '#del-after-f' ).attr( 'checked' ) ) data += '&del-after-f=on';
							if ( jQuery( '#del-after-v' ).attr( 'checked' ) ) data += '&del-after-v=on';
							if ( jQuery( '#del-after-u' ).attr( 'checked' ) ) data += '&del-after-u=on';
							data += '&wppa-import-submit=ajax';

							var files = jQuery( ':checked' );
							var found = false;
							var i=0;
							var elm;
							var fulldata;
							for ( i=0; i<files.length; i++ ) {
								found = false;	// assume done
								elm = files[i];
								// Is it a file checkbox?
								var temp = elm.id.split( '-' );
								if ( temp[0] != 'file' ) continue;	// no
								fulldata = data+'&import-ajax-file='+elm.title;
								found = true;
								break;
							}
							//	alert( data );
							if ( ! found ) {
								wppaStopAjaxImport();
								return;	// nothing left
							}
							// found one, do it
							var oldhtml=jQuery( '#name-'+elm.id ).html();
							var xmlhttp = wppaGetXmlHttp();
							xmlhttp.onreadystatechange = function() {
								if ( xmlhttp.readyState == 4 ) {
									if ( xmlhttp.status!=404 ) {
										var resp = xmlhttp.responseText;
										if ( resp.indexOf( 'Server' ) != -1 && resp.indexOf( 'Error' ) != -1 ) {
											resp = '<span style="color:red" >Server error</span>';
										}
										jQuery( '#name-'+elm.id ).html( '&nbsp;&nbsp;<b>'+resp+'</b>' );
										elm.checked = '';
										if ( jQuery( '#del-after-p' ).attr( 'checked' ) ||
											 jQuery( '#del-after-f' ).attr( 'checked' ) ) {
											elm.disabled = 'disabled';
											elm.title = '';
										}
										if ( wppaImportRuns ) {
											setTimeout( 'wppaDoAjaxImport()', 100 );
										}
									}
									else {
										jQuery( '#name-'+elm.id ).html( '&nbsp;&nbsp;<b>Not found</b>' );
									}
								}
							}
							var url = wppaAjaxUrl+'?action=wppa&wppa-action=import';
							xmlhttp.open( 'POST',url,true );
							xmlhttp.setRequestHeader( "Content-type","application/x-www-form-urlencoded" );
							xmlhttp.send( fulldata );
							jQuery( '#name-'+elm.id ).html( '&nbsp;&nbsp;<b style="color:blue" >' + '<?php _e( 'Working...', 'wp-photo-album-plus' ) ?>' + '</b>' );
							jQuery( '#wppa-start-ajax' ).css( 'display', 'none' );
							jQuery( '#wppa-stop-ajax' ).css( 'display', 'inline' );
						}
						function wppaStopAjaxImport() {
							wppaImportRuns = false;
							jQuery( '#wppa-start-ajax' ).css( 'display', 'inline' );
							jQuery( '#wppa-stop-ajax' ).css( 'display', 'none' );
						}
					</script>
					<?php if ( ( $photocount || $videocount || $audiocount ) && ! $albumcount && ! $dircount && ! $zipcount ) { ?>
					<input id="wppa-start-ajax" type="button" onclick="if ( wppaVfyAlbum() ) { wppaDoAjaxImport() }" class="button-secundary" value="<?php esc_attr( _e( 'Start Ajax Import', 'wp-photo-album-plus' ) ) ?>" />
					<input id="wppa-stop-ajax" style="display:none;" type="button" onclick="wppaStopAjaxImport()" class="button-secundary" value="<?php esc_attr( _e( 'Stop Ajax Import', 'wp-photo-album-plus' ) ) ?>" />
					<?php } ?>
				</p>
				</form>

		<?php }
		else {
			if ( $source_type == 'local' ) {
				wppa_ok_message( __( 'There are no importable files found in directory:' , 'wp-photo-album-plus').' '.$source );
			}
			else {
				wppa_ok_message( __( 'There are no photos found or left to process at url:' , 'wp-photo-album-plus').' '.$source_url );
			}
		}
		echo '<br /><b>';
		_e( 'You can import the following file types:', 'wp-photo-album-plus');
		echo '</b><br />';
		if ( PHP_VERSION_ID >= 50207 ) {
			echo '<br />';
			_e( 'Compressed file types: .zip' , 'wp-photo-album-plus');
		}
		if ( true ) {
			echo '<br />';
			_e( 'Photo file types:' , 'wp-photo-album-plus');
			foreach ( $wppa_supported_photo_extensions as $ext ) {
				echo ' .'.$ext;
			}
		}
		if ( wppa_switch( 'enable_video' ) ) {
			echo '<br />';
			_e( 'Video file types:' , 'wp-photo-album-plus');
			foreach ( $wppa_supported_video_extensions as $ext ) {
				echo ' .'.$ext;
			}
		}
		if ( wppa_switch( 'enable_audio' ) ) {
			echo '<br />';
			_e( 'Audio file types:' , 'wp-photo-album-plus');
			foreach ( $wppa_supported_audio_extensions as $ext ) {
				echo ' .'.$ext;
			}
		}
		echo '<br />';
		_e( 'WPPA+ file types: .amf .pmf' , 'wp-photo-album-plus');
		echo '<br />';
		_e( 'Directories with optional subdirs containig photos' , 'wp-photo-album-plus');
		echo '<br />';
		_e( 'Custom data files of type .csv' , 'wp-photo-album-plus');
		echo '<br /><br />';
		_e( 'Your depot directory is:' , 'wp-photo-album-plus');
		echo '<b> .../' . WPPA_DEPOT . '/</b>';
	}
	else { ?>
		<?php $url = wppa_dbg_url( get_admin_url().'admin.php?page=wppa_admin_menu' ); ?>
		<p><?php _e( 'No albums exist. You must' , 'wp-photo-album-plus'); ?> <a href="<?php echo( $url ) ?>"><?php _e( 'create one' , 'wp-photo-album-plus'); ?></a> <?php _e( 'beofre you can upload your photos.' , 'wp-photo-album-plus'); ?></p><?php
	}
	if ( wppa( 'continue' ) ) {
		wppa_warning_message( __( 'Trying to continue...' , 'wp-photo-album-plus') );
		echo '<script type="text/javascript">document.location=\''.get_admin_url().'admin.php?page=wppa_import_photos&continue&nonce='.wp_create_nonce( 'dirimport' ).'\';</script>';
	} ?>
	</div>
<?php
}

// get array of files to import
function wppa_get_import_files() {

	// Init
	$user 			= wppa_get_user();
	$source_type 	= get_option( 'wppa_import_source_type_'.$user, 'local' );
	$files			= array();

	// Ajax? one file
	if ( isset ( $_POST['import-ajax-file'] ) ) {
		$files = array( $_POST['import-ajax-file'] );
	}

	// Dispatch on source type local/remote
	elseif ( $source_type == 'local' ) {
		$source 		= get_option( 'wppa_import_source_'.$user, WPPA_DEPOT_PATH );
		$source_path 	= $source;	// Filesystem
		$files 			= glob( $source_path . '/*' );
	}
	else { // remote
		$max_tries 		= get_option( 'wppa_import_remote_max_'.$user, '10' );
		$setting 		= get_option( 'wppa_import_source_url_'.$user, 'http://' );
		$pattern		= '/src=".*?"/';

		// Is it a photofile in a wppa tree filestructure?
		$old_setting = $setting;

		// assume not
		if ( is_array( @ getimagesize( $setting ) ) ) {
			wppa( 'is_wppa_tree', false );
		}

		// though?
		else {
			$setting = wppa_expand_tree_path( $old_setting );
			if ( is_array( @ getimagesize( $setting ) ) ) {
				wppa( 'is_wppa_tree', true );
			}
		}

		// Is it a photofile?
		if ( is_array( @ getimagesize( $setting ) ) ) {	// image uri
			$files = array( $setting );
			$pid = wppa_strip_ext( basename( $old_setting ) );
			if ( is_numeric( $pid ) ) {
				$tries = 1;
				$before = substr( $old_setting, 0, strpos( $old_setting, $pid) );
				while ( $tries < $max_tries ) {
					$tries++;
					$pid++;
					if ( wppa( 'is_wppa_tree' ) ) {
						$files[] = $before . wppa_expand_id($pid) . '.jpg';
					}
					else {
						$files[] = $before . $pid . '.jpg';
					}
				}
			}
		}

		// is it a page url
		else {
			$files = get_option( 'wppa_import_source_url_found_'.$user, false );
			if ( $files === false ) {

				// Init
				$files = array();

				// Get page content
				$curl = curl_init();
				curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $curl, CURLOPT_URL, $setting );
				$contents = curl_exec( $curl );
				curl_close( $curl );

				// Process result
				if ( $contents ) {

					// Preprocess
					$contents = str_replace( '\'', '"', $contents );

					// Find matches
					preg_match_all( $pattern, $contents, $matches, PREG_PATTERN_ORDER );
					if ( is_array( $matches[0] ) ) {

						// Sort
						sort( $matches[0] );

						// Copy to $files, skipping dups
						$val = '';
						$count = 0;
						$sfxs = array( 'jpg', 'jpeg', 'gif', 'png', 'JPG', 'JPEG', 'GIF', 'PNG' );
						foreach ( array_keys( $matches[0] ) as $idx ) {
							if ( $matches[0][$idx] != $val ) {
								$val = $matches[0][$idx];
								// Post process found item
								$match 		= substr( $matches[0][$idx], 5 );
								$matchpos 	= strpos( $contents, $match );
								$match 		= trim( $match, '"' );
								if ( strpos( $match, '?' ) ) $match = substr( $match, 0, strpos( $match, '?' ) );
								$match 		= str_replace( '/uploads/wppa/thumbs/', '/uploads/wppa/', $match );
								$sfx = wppa_get_ext( $match );
								if ( in_array( $sfx, $sfxs ) ) {
									// Save it
									$count++;
									if ( $count <= $max_tries ) {
										$files[] = $match;
									}
								}
							}
						}
					}
				}
				update_option( 'wppa_import_source_url_found_'.$user, $files );
			}
		}
	}

	// Remove non originals
	if ( is_array( $files ) ) foreach ( array_keys( $files ) as $key ) {
		if ( ! wppa_is_orig( $files[$key] ) ) {
			unset ( $files[$key] );
		}
	}

	// Sort to keep synchronicity when doing ajax import
	if ( is_array( $files ) ) sort( $files );

	// Done, return result
	return $files;
}

// Upload multiple photos
function wppa_upload_multiple() {
	global $wpdb;
	global $warning_given;

	$warning_given = false;
	$uploaded_a_file = false;

	$count = '0';
	foreach ( $_FILES as $file ) {
		if ( is_array( $file['error'] ) ) {
			for ( $i = '0'; $i < count( $file['error'] ); $i++ ) {
				if ( wppa_is_time_up() ) {
					wppa_warning_message( sprintf( __( 'Time out. %s photos uploaded in album nr %s.' , 'wp-photo-album-plus'), $count, $_POST['wppa-album'] ) );
					wppa_set_last_album( $_POST['wppa-album'] );
					return;
				}
				if ( ! $file['error'][$i] ) {
					$id = wppa_insert_photo( $file['tmp_name'][$i], $_POST['wppa-album'], $file['name'][$i] );
					if ( $id ) {
						$uploaded_a_file = true;
						$count++;
						wppa_backend_upload_mail( $id, $_POST['wppa-album'], $file['name'][$i] );
					}
					else {
						wppa_error_message( __( 'Error inserting photo' , 'wp-photo-album-plus') . ' ' . wppa_sanitize_file_name( basename( $file['name'][$i] ) ) . '.' );
						return;
					}
				}
			}
		}
	}

	if ( $uploaded_a_file ) {
		wppa_update_message( $count.' '.__( 'Photos Uploaded in album nr' , 'wp-photo-album-plus') . ' ' . $_POST['wppa-album'] );
		wppa_set_last_album( $_POST['wppa-album'] );
    }
}

// Upload single photos
function wppa_upload_photos() {
	global $wpdb;
	global $warning_given;

	$warning_given = false;
	$uploaded_a_file = false;

	$count = '0';
	foreach ( $_FILES as $file ) {
		if ( $file['tmp_name'] != '' ) {
//			$file['name'] = wppa_sanitize_file_name( $file['name'] );
			$id = wppa_insert_photo( $file['tmp_name'], $_POST['wppa-album'], $file['name'] );
			if ( $id ) {
				$uploaded_a_file = true;
				$count++;
				wppa_backend_upload_mail( $id, $_POST['wppa-album'], $file['name'] );
			}
			else {
				wppa_error_message( __( 'Error inserting photo' , 'wp-photo-album-plus') . ' ' . wppa_sanitize_file_name( basename( $file['name'] ) ) . '.' );
				return;
			}
		}
	}

	if ( $uploaded_a_file ) {
		wppa_update_message( $count.' '.__( 'Photos Uploaded in album nr' , 'wp-photo-album-plus') . ' ' . $_POST['wppa-album'] );
		wppa_set_last_album( $_POST['wppa-album'] );
    }
}

// Send emails after backend upload
function wppa_backend_upload_mail( $id, $alb, $name ) {

	$owner = wppa_get_user();
	if ( $owner == 'admin' ) return;	// Admin does not send mails to himself

	if ( wppa_switch( 'upload_backend_notify' ) ) {
		$to = get_bloginfo( 'admin_email' );
		$subj = sprintf( __( 'New photo uploaded: %s' , 'wp-photo-album-plus'), wppa_sanitize_file_name( $name ) );
		$cont['0'] = sprintf( __( 'User %1$s uploaded photo %2$s into album %3$s' , 'wp-photo-album-plus'), $owner, $id, wppa_get_album_name( $alb ) );
		if ( wppa_switch( 'upload_moderate' ) && !current_user_can( 'wppa_admin' ) ) {
			$cont['1'] = __( 'This upload requires moderation' , 'wp-photo-album-plus');
			$cont['2'] = '<a href="'.get_admin_url().'admin.php?page=wppa_admin_menu&tab=pmod&photo='.$id.'" >'.__( 'Moderate manage photo' , 'wp-photo-album-plus').'</a>';
		}
		else {
			$cont['1'] = __( 'Details:' , 'wp-photo-album-plus');
			$cont['1'] .= ' <a href="'.get_admin_url().'admin.php?page=wppa_admin_menu&tab=pmod&photo='.$id.'" >'.__( 'Manage photo' , 'wp-photo-album-plus').'</a>';
		}
		wppa_send_mail( $to, $subj, $cont, $id );
	}
}

// Upload a zipfile
function wppa_upload_zip() {
global $target;

	$file 	= $_FILES['file_zip'];
	$name 	= wppa_sanitize_file_name( $file['name'] );
	$type 	= $file['type'];
	$error 	= $file['error'];
	$size 	= $file['size'];
	$temp 	= $file['tmp_name'];
	$target = WPPA_DEPOT_PATH.'/'.$name;

	copy( $temp, $target );

	if ( $error == '0' ) wppa_ok_message( __( 'Zipfile' , 'wp-photo-album-plus').' '.$name.' '.__( 'sucessfully uploaded.' , 'wp-photo-album-plus') );
	else wppa_error_message( __( 'Error' , 'wp-photo-album-plus').' '.$error.' '.__( 'during upload.' , 'wp-photo-album-plus') );

	return $error;
}

// Do the import photos
function wppa_import_photos( $delp = false, $dela = false, $delz = false, $delv = false, $delu = false, $delc = false, $delf = false ) {
global $wpdb;
global $warning_given;
global $wppa_supported_photo_extensions;
global $wppa_supported_video_extensions;
global $wppa_supported_audio_extensions;

	$warning_given = false;

	// Get this users current source directory setting
	$user 			= wppa_get_user();
	$source_type 	= get_option( 'wppa_import_source_type_'.$user, 'local' );
	if ( $source_type == 'remote' ) wppa( 'is_remote', true );
	$source 		= get_option( 'wppa_import_source_'.$user, WPPA_DEPOT_PATH );

	$depot 			= WPPA_ABSPATH . $source;	// Filesystem
	$depoturl 		= get_bloginfo( 'wpurl' ).'/'.$source;	// url

	// See what's in there
	$files = wppa_get_import_files();

	// First extract zips if our php version is ok
	$idx='0';
	$zcount = 0;
	if ( PHP_VERSION_ID >= 50207 ) {
		foreach( $files as $zipfile ) {
			if ( isset( $_POST['file-'.$idx] ) ) {
				$ext = strtolower( substr( strrchr( $zipfile, "." ), 1 ) );

				if ( $ext == 'zip' ) {
					$err = wppa_extract( $zipfile, $delz );
					if ( $err == '0' ) $zcount++;
				} // if ext = zip
			} // if isset
			$idx++;
		} // foreach
	}

	// Now see if albums must be created
	$idx='0';
	$acount = 0;
	foreach( $files as $album ) {
		if ( isset( $_POST['file-'.$idx] ) ) {
			$ext = strtolower( substr( strrchr( $album, "." ), 1 ) );
			if ( $ext == 'amf' ) {
				$name = '';
				$desc = '';
				$aord = '0';
				$parent = '0';
				$porder = '0';
				$owner = '';
				$handle = fopen( $album, "r" );
				if ( $handle ) {
					$buffer = fgets( $handle, 4096 );
					while ( !feof( $handle ) ) {
						$tag = substr( $buffer, 0, 5 );
						$len = strlen( $buffer ) - 6;	// substract 5 for label and one for eol
						$data = substr( $buffer, 5, $len );
						switch( $tag ) {
							case 'name=':
								$name = $data;
								break;
							case 'desc=':
								$desc = wppa_txt_to_nl( $data );
								break;
							case 'aord=':
								if ( is_numeric( $data ) ) $aord = $data;
								break;
							case 'prnt=':
								if ( $data == __( '--- none ---' , 'wp-photo-album-plus') ) $parent = '0';
								elseif ( $data == __( '--- separate ---' , 'wp-photo-album-plus') ) $parent = '-1';
								else {
									$prnt = wppa_get_album_id( $data );
									if ( $prnt != '' ) {
										$parent = $prnt;
									}
									else {
										$parent = '0';
										wppa_warning_message( __( 'Unknown parent album:' , 'wp-photo-album-plus').' '.$data.' '.__( '--- none --- used.' , 'wp-photo-album-plus') );
									}
								}
								break;
							case 'pord=':
								if ( is_numeric( $data ) ) $porder = $data;
								break;
							case 'ownr=':
								$owner = $data;
								break;
						}
						$buffer = fgets( $handle, 4096 );
					} // while !foef
					fclose( $handle );
					if ( wppa_get_album_id( $name ) != '' ) {
						wppa_warning_message( 'Album already exists '.stripslashes( $name ) );
						if ( $dela ) unlink( $album );
					}
					else {
						$id = basename( $album );
						$id = substr( $id, 0, strpos( $id, '.' ) );
						$id = wppa_create_album_entry( array ( 	'id' 			=> $id,
																'name' 			=> stripslashes( $name ),
																'description' 	=> stripslashes( $desc ),
																'a_order' 		=> $aord,
																'a_parent' 		=> $parent,
																'p_order_by' 	=> $porder,
																'owner' 		=> $owner
															 ) );

						if ( $id === false ) {
							wppa_error_message( __( 'Could not create album.' , 'wp-photo-album-plus') );
						}
						else {
							//$id = wppa_get_album_id( $name );
							wppa_set_last_album( $id );
							wppa_index_add( 'album', $id );
							wppa_ok_message( __( 'Album #' , 'wp-photo-album-plus') . ' ' . $id . ': '.stripslashes( $name ).' ' . __( 'Added.' , 'wp-photo-album-plus') );
							if ( $dela ) unlink( $album );
							$acount++;
							wppa_clear_cache();
							wppa_flush_treecounts( $id );
						} // album added
					} // album did not exist
				} // if handle ( file open )
			} // if its an album
		} // if isset
		$idx++;
	} // foreach file

	// Now the photos
	$idx 		= '0';
	$pcount 	= '0';
	$totpcount 	= '0';

	// find album id
	if ( isset( $_POST['cre-album'] ) ) {	// use album ngg gallery name for ngg conversion
		$album 	= wppa_get_album_id( strip_tags( $_POST['cre-album'] ) );
		if ( ! $album ) {				// the album does not exist yet, create it
			$name	= strip_tags( $_POST['cre-album'] );
			$desc 	= sprintf( __( 'This album has been converted from ngg gallery %s' , 'wp-photo-album-plus'), $name );
			$uplim	= '0/0';	// Unlimited not to destroy the conversion process!!
			$album 	= wppa_create_album_entry( array ( 	'name' 			=> $name,
														'description' 	=> $desc,
														'upload_limit' 	=> $uplim
														 ) );
			if ( $album === false ) {
				wppa_error_message( __( 'Could not create album.' , 'wp-photo-album-plus').'<br/>Query = '.$query );
				wp_die( 'Sorry, cannot continue' );
			}
		}
	}
	elseif ( isset( $_POST['wppa-photo-album'] ) ) {
		$album = $_POST['wppa-photo-album'];
	}
	else $album = '0';

	// Report starting process
	wppa_ok_message( __( 'Processing files, please wait...' , 'wp-photo-album-plus').' '.__( 'If the line of dots stops growing or your browser reports Ready, your server has given up. In that case: try again' , 'wp-photo-album-plus').' <a href="'.wppa_dbg_url( get_admin_url().'admin.php?page=wppa_import_photos' ).'">'.__( 'here.' , 'wp-photo-album-plus').'</a>' );

	// Do them all
	foreach ( array_keys( $files ) as $file_idx ) {
		$unsanitized_path_name = $files[$file_idx];
		$file = $files[$file_idx];
		wppa_is_wppa_tree( $file );	// Sets wppa( 'is_wppa_tree' )
		if ( isset( $_POST['use-backup'] ) && is_file( $file.'_backup' ) ) {
			$file = $file.'_backup';
		}
		$file = wppa_sanitize_file_name( $file );
		if ( isset( $_POST['file-'.$idx] ) || wppa( 'ajax' ) ) {
			if ( wppa( 'is_wppa_tree' ) ) {
				if ( wppa( 'ajax' ) ) wppa( 'ajax_import_files', basename( wppa_compress_tree_path( $file ) ) );
			}
			else {
				if ( wppa( 'ajax' ) ) wppa( 'ajax_import_files', basename( $file ) );
			}
			$ext = strtolower( substr( strrchr( $file, "." ), 1 ) );
			$ext = str_replace( '_backup', '', $ext );
			if ( in_array( $ext, $wppa_supported_photo_extensions ) ) {

				// See if a metafile exists
				//$meta = substr( $file, 0, strlen( $file ) - 3 ).'pmf';
				$meta = wppa_strip_ext( $unsanitized_path_name ) . '.PMF';
				if ( ! is_file( $meta ) ) {
					$meta = wppa_strip_ext( $unsanitized_path_name ) . '.pmf';
				}

				// find all data: name, desc, porder form metafile
				if ( is_file( $meta ) ) {
					$alb = wppa_get_album_id( wppa_get_meta_album( $meta ) );
					$name = wppa_get_meta_name( $meta );
					$desc = wppa_txt_to_nl( wppa_get_meta_desc( $meta ) );
					$porder = wppa_get_meta_porder( $meta );
					$linkurl = wppa_get_meta_linkurl( $meta );
					$linktitle = wppa_get_meta_linktitle( $meta );
				}
				else {
					$alb = $album;	// default album
					$name = '';		// default name
					$desc = '';		// default description
					$porder = '0';	// default p_order
					$linkurl = '';
					$linktitle = '';
				}

				// If there is a video or audio with the same name, this is the poster.
				$is_poster = wppa_file_is_in_album( wppa_strip_ext( basename( $file ) ) . '.xxx', $alb );
				if ( $is_poster ) {

					// Delete possible poster sourcefile
					wppa_delete_source( basename( $file ), $alb );

					// Remove possible existing posters, the file-extension may be different as before
					$old_photo = wppa_strip_ext( wppa_get_photo_path( $is_poster ) );
					$old_thumb = wppa_strip_ext( wppa_get_thumb_path( $is_poster ) );
					foreach ( $wppa_supported_photo_extensions as $pext ) {
						@ unlink( $old_photo . '.' . $pext );
						@ unlink( $old_thumb . '.' . $pext );
					}

					// Clear sizes on db
					wppa_update_photo( array( 	'thumbx' => '0',
												'thumby' => '0',
												'photox' => '0',
												'photoy' => '0'
										));

					// Make new files
					$bret = wppa_make_the_photo_files( $file, $is_poster, strtolower( wppa_get_ext( basename( $file ) ) ) );
					if ( $bret ) { 	// Success
						if ( wppa( 'ajax' ) ) wppa( 'ajax_import_files_done', true );
						wppa_save_source( $file, basename( $file ), $alb );
						$pcount++;
						$totpcount += $bret;
						if ( $delp ) {
							unlink( $file );
						}
					}
					else { 			// Failed
						if ( ! wppa( 'ajax' ) ) {
							wppa_error_message('Failed to add poster for item '.$is_poster);
						}
						if ( $delf ) {
							unlink( $file );
						}
					}
				}

				// Update the photo ?
				elseif ( isset( $_POST['wppa-update'] ) ) {

					if ( wppa( 'is_wppa_tree' ) ) {
						$tmp = explode( '/wppa/', $file );
						$name = str_replace( '/', '', $tmp[1] );
					}

					$iret = wppa_update_photo_files( $unsanitized_path_name, $name );
					if ( $iret ) {
						if ( wppa( 'ajax' ) ) wppa( 'ajax_import_files_done', true );
						$pcount++;
						$totpcount += $iret;
						if ( $delp ) {
							unlink( $unsanitized_path_name );
						}
					}
					else {
						if ( $delf ) {
							unlink( $unsanitized_path_name );
						}
					}
				}

				// Insert the photo
				else {
					if ( is_numeric( $alb ) && $alb != '0' ) {
						if ( wppa( 'is_wppa_tree' ) ) {
							$tmp = explode( '/wppa/', $file );
							$id = str_replace( '/', '', $tmp[1] );
							$name = $id;
						}
						else {
							$id = basename( $file );
						}
						if ( wppa_switch( 'void_dups' ) && wppa_file_is_in_album( $id, $alb ) ) {
							wppa_warning_message( sprintf( __( 'Photo %s already exists in album %s. (1)' , 'wp-photo-album-plus'), $id, $alb ) );
							wppa( 'ajax_import_files_error', __( 'Duplicate', 'wp-photo-album-plus') );
							if ( $delf ) {
								unlink( $file );
							}
						}
						else {
							$id = substr( $id, 0, strpos( $id, '.' ) );
							if ( !is_numeric( $id ) || ! wppa_is_id_free( 'photo', $id ) ) $id = 0;
							if ( wppa_insert_photo( $unsanitized_path_name, $alb, stripslashes( $name ), stripslashes( $desc ), $porder, $id, stripslashes( $linkurl ), stripslashes( $linktitle ) ) ) {
								if ( wppa( 'ajax' ) ) {
									wppa( 'ajax_import_files_done', true );
								}
								$pcount++;
								if ( $delp ) {
									unlink( $unsanitized_path_name );
									if ( is_file( $meta ) ) unlink( $meta );
								}
							}
							else {
								wppa_error_message( __( 'Error inserting photo' , 'wp-photo-album-plus') . ' ' . basename( $file ) . '.' );
								if ( $delf ) {
									unlink( $unsanitized_path_name );
								}
							}
						}
					}
					else {
						wppa_error_message( sprintf( __( 'Error inserting photo %s, unknown or non existent album.' , 'wp-photo-album-plus'), basename( $file ) ) );
					}
				} // Insert
			}
		}
		$idx++;
		if ( $source_type == 'remote' ) unset( $files[$file_idx] );
		if ( wppa_is_time_up() ) {
			wppa_warning_message( sprintf( __( 'Time out. %s photos imported. Please restart this operation.' , 'wp-photo-album-plus'), $pcount ) );
			wppa_set_last_album( $album );
			if ( $source_type == 'remote' ) update_option( 'wppa_import_source_url_found_'.$user, $files );
			return;
		}
	} // foreach $files
	if ( $source_type == 'remote' ) update_option( 'wppa_import_source_url_found_'.$user, $files );

	// Now the dirs to album imports

	$idx 		= '0';
	$dircount 	= '0';
	global $photocount;
	$photocount = '0';
	$iret 		= true;

	foreach ( $files as $file ) {
		if ( basename( $file ) != '.' &&  basename( $file ) != '..' && ( isset( $_POST['file-'.$idx] ) || isset( $_GET['continue'] ) ) ) {
			if ( is_dir( $file ) ) {
				$iret = wppa_import_dir_to_album( $file, '0' );
				if ( wppa_is_time_up() && wppa_switch( 'auto_continue' ) ) {
					wppa( 'continue', 'continue' );
				}
				$dircount++;
			}
		}
		$idx++;
		if ( $iret == false ) break;	// Time out
	}

	// Now the video files
	$videocount = '0';
	$alb = isset( $_POST['wppa-video-album'] ) ? $_POST['wppa-video-album'] : '0';
	if ( wppa( 'ajax' ) && ! $alb ) {
		wppa( 'ajax_import_files_error', __( 'Unknown album' , 'wp-photo-album-plus' ) );
	}
	else foreach ( array_keys( $files ) as $idx ) {
		$file = $files[$idx];
		if ( isset( $_POST['file-'.$idx] ) || wppa( 'ajax' ) ) {
			if ( wppa( 'ajax' ) ) wppa( 'ajax_import_files', wppa_sanitize_file_name( basename( $file ) ) );	/* */
			$ext = strtolower( substr( strrchr( $file, "." ), 1 ) );
			if ( in_array( $ext, $wppa_supported_video_extensions ) ) {
				if ( is_numeric( $alb ) && $alb != '0' ) {

					// Do we have this filename with ext xxx in this album?
					$filename = wppa_strip_ext( basename( $file ) ).'.xxx';
					$id = wppa_file_is_in_album( $filename, $alb );

					// Or maybe the poster is already there
					foreach ( $wppa_supported_photo_extensions as $pext ) {
						if ( ! $id ) {
							$id = wppa_file_is_in_album( str_replace( 'xxx', $pext, $filename ), $alb );
						}
					}

					// This filename already exists: is the poster. Fix the filename in the photo info
					if ( $id ) {
						$fname = wppa_get_photo_item( $id, 'filename' );
						$fname = wppa_strip_ext( $fname ) . '.xxx';

						// Fix filename and ext in photo info
						wppa_update_photo( array( 'id' => $id, 'filename' => $fname, 'ext' => 'xxx' ) );
					}

					// Add new entry
					if ( ! $id ) {
						$id = wppa_create_photo_entry( array( 'album' => $alb, 'filename' => $filename, 'ext' => 'xxx', 'name' => wppa_strip_ext( $filename ) ) );
						wppa_flush_treecounts( $alb );
					}

					// Add video filetype
					$newpath = wppa_strip_ext( wppa_get_photo_path( $id ) ).'.'.$ext;
					copy( $file, $newpath );
					if ( $delv ) unlink( $file );
					if ( wppa( 'ajax' ) ) {
						wppa( 'ajax_import_files_done', true );
					}

					// Make sure ext is set to xxx after adding video to an existing poster
					wppa_update_photo( array( 'id' => $id, 'ext' => 'xxx' ) );

					// Book keeping
					$videocount++;
				}
				else {
					wppa_error_message( sprintf( __( 'Error inserting video %s, unknown or non existent album.' , 'wp-photo-album-plus'), basename( $file ) ) );
				}
			}
		}
	}

	// Now the audio files
	$audiocount = '0';
	$alb = isset( $_POST['wppa-audio-album'] ) ? $_POST['wppa-audio-album'] : '0';
	if ( wppa( 'ajax' ) && ! $alb ) {
		wppa( 'ajax_import_files_error', __( 'Unknown album', 'wp-photo-album-plus' ) );
	}
	else foreach ( array_keys( $files ) as $idx ) {
		$file = $files[$idx];
		if ( isset( $_POST['file-'.$idx] ) || wppa( 'ajax' ) ) {
			if ( wppa( 'ajax' ) ) wppa( 'ajax_import_files', wppa_sanitize_file_name( basename( $file ) ) );
			$ext = strtolower( substr( strrchr( $file, "." ), 1 ) );
			if ( in_array( $ext, $wppa_supported_audio_extensions ) ) {
				if ( is_numeric( $alb ) && $alb != '0' ) {

					// Do we have this filename with ext xxx in this album?
					$filename = wppa_strip_ext( basename( $file ) ).'.xxx';
					$id = wppa_file_is_in_album( $filename, $alb );

					// Or maybe the poster is already there
					foreach ( $wppa_supported_photo_extensions as $pext ) {
						if ( ! $id ) {
							$id = wppa_file_is_in_album( str_replace( 'xxx', $pext, $filename ), $alb );
						}
					}

					// This filename already exists: is the poster. Fix the filename in the photo info
					if ( $id ) {
						$fname = wppa_get_photo_item( $id, 'filename' );
						$fname = wppa_strip_ext( $fname ) . '.xxx';

						// Fix filename and ext in photo info
						wppa_update_photo( array( 'id' => $id, 'filename' => $fname, 'ext' => 'xxx' ) );
					}

					// Add new entry
					if ( ! $id ) {
						$id = wppa_create_photo_entry( array( 'album' => $alb, 'filename' => $filename, 'ext' => 'xxx', 'name' => wppa_strip_ext( $filename ) ) );
						wppa_flush_treecounts( $alb );
					}

					// Add audio filetype
					$newpath = wppa_strip_ext( wppa_get_photo_path( $id ) ).'.'.$ext;
					copy( $file, $newpath );
					if ( $delu ) unlink( $file );
					if ( wppa( 'ajax' ) ) {
						wppa( 'ajax_import_files_done', true );
					}

					// Make sure ext is set to xxx after adding audio to an existing poster
					wppa_update_photo( array( 'id' => $id, 'ext' => 'xxx' ) );

					// Book keeping
					$audiocount++;
				}
				else {
					wppa_error_message( sprintf( __( 'Error inserting audio %s, unknown or non existent album.' , 'wp-photo-album-plus'), basename( $file ) ) );
				}
			}
		}
	}

	// The csv files. NOT with ajax
	$csvcount = wppa_get_csvcount( $files );
	if ( $csvcount ) {
		$csvcount = '0';
		if ( ! wppa( 'ajax' ) ) {
			if ( is_array( $files ) ) {

				// Make sure the feature is on
				if ( ! wppa_switch( 'custom_fields' ) ) {
					wppa_update_option( 'wppa_custom_fields', 'yes' );
					echo '<b>' . __( 'Custom datafields enabled' , 'wp-photo-album-plus').'</b><br />';
				}

				// Get the captions we already have
				$cust_labels = array();
				for ( $i = '0'; $i < '10'; $i++ ) {
					$cust_labels[$i] = wppa_opt( 'custom_caption_' . $i );
				}
/*
				// Display the captions we have
				echo __( 'Captions are:' ) . ' ';
				for ( $i = 0; $i < '10'; $i++ ) {
					echo '[' . $i . '] -> ';
					if ( $cust_labels[$i] ) {
						echo $cust_labels[$i] . ' ';
					}
					else {
						echo __( '--- not used ---' ) . ' ';
					}
				}
				echo '<br />';
*/
				// Process the files
				$photos_processed_csv = '0';
				$photos_skipped_csv = '0';
				foreach ( array_keys( $files ) as $idx ) {
					$this_skipped = '0';
					$file = $files[$idx];
					if ( isset( $_POST['file-'.$idx] ) || isset( $_GET['continue'] ) ) {
						$ext = strtolower( wppa_get_ext( $file ) );
						if ( $ext == 'csv' ) {
							echo '<b>' . __( 'Processing', 'wp-photo-album-plus') . ' ' . basename( $file ) . '</b><br />';
							wppa_log( 'dbg', __( 'Processing', 'wp-photo-album-plus') . ' ' . basename( $file ) );

							// Copy the file to a temp file
							$tempfile = dirname( $file ) . '/temp.csv';
							copy ( $file, $tempfile );

							// Open file
							$handle = fopen( $tempfile, "rt" );
							if ( ! $handle ) {
								wppa_error_message( __( 'Can not open file. Can not continue. (1)' , 'wp-photo-album-plus') );
								return;
							}
							$write_handle = fopen( $file, "wt" );
							if ( ! $write_handle ) {
								wppa_error_message( __( 'Can not open file. Can not continue. (2)' , 'wp-photo-album-plus') );
								return;
							}

							// Read header
							$header = fgets( $handle, 4096 );
							if ( ! $header ) {
								wppa_error_message( __( 'Can not read header. Can not continue.' , 'wp-photo-album-plus') );
								fclose( $handle );
								return;
							}
							fputs( $write_handle, $header );
							echo __( 'Read header:' , 'wp-photo-album-plus') . ' ' . $header . '<br />';

							// Interprete header
							$captions = wppa_explode_csv( $header );
							if ( ! is_array( $captions ) || count( $captions ) < '2' ) {
								wppa_error_message( __( 'Invalid header. Can not continue.' , 'wp-photo-album-plus') );
								fclose( $handle );
								return;
							}
							foreach ( array_keys( $captions ) as $key ) {
								if ( $key == '0' ) {
									if ( ! in_array( strtolower( trim( $captions['0'] ) ), array( 'name', 'photoname', 'filename' ) ) ) {
										wppa_error_message( __( 'Invalid header. First item must be \'name\', \'photoname\' or \'filename\'' , 'wp-photo-album-plus') );
										fclose( $handle );
										return;
									}
								}
								elseif ( ! in_array( $captions[$key], $cust_labels ) ) {
									if ( ! in_array( '', $cust_labels ) ) {
										wppa_error_message( __( 'All available custom data fields are in use. There is no space for' , 'wp-photo-album-plus') . ' ' . $captions[$key] );
										fclose( $handle );
										return;
									}

									// Add a new caption
									$i = '0';
									while ( $cust_labels[$i] ) $i++;
									$cust_labels[$i] = $captions[$key];
									wppa_update_option( 'wppa_custom_caption_' . $i, $cust_labels[$i] );
									wppa_update_option( 'wppa_custom_visible_' . $i, 'yes' );
									wppa_log( 'dbg', sprintf( __( 'New caption %s added.' , 'wp-photo-album-plus'), $cust_labels[$i] ) );
								}
							}

							// Find the correlation between caption index and custom data index.
							$pointers = array();
							for ( $i = '1'; $i < count( $captions ); $i++ ) {
								for ( $j = '0'; $j < '10'; $j++ ) {
									if ( $captions[$i] == $cust_labels[$j] ) {
										$pointers[$j] = $i;
									}
								}
							}

							// Now process the lines
							while ( ! feof( $handle ) ) {
								$dataline = fgets( $handle, 4096 );
								if ( $dataline ) {
									wppa_log( 'dbg', __( 'Read data:' , 'wp-photo-album-plus') . ' ' . trim( $dataline ) );
									$data_arr = wppa_explode_csv( $dataline );
									$search = $data_arr[0];
									switch ( strtolower($captions[0]) ) {
										case 'photoname':
											$photos = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `".WPPA_PHOTOS."` WHERE `name` = %s", $data_arr[0] ), ARRAY_A );
											break;
										case 'filename':
											$photos = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `".WPPA_PHOTOS."` WHERE `filename` = %s", $data_arr[0] ), ARRAY_A );
											break;
										case 'name':
											$photos = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `".WPPA_PHOTOS."` WHERE `name` = %s OR `filename` = %s", $data_arr[0], $data_arr[0] ), ARRAY_A );
											break;
									}
									if ( $photos ) {
										foreach( $photos as $photo ) {
											$cust_data = $photo['custom'] ? unserialize( $photo['custom'] ) : array( '', '', '', '', '', '', '', '', '', '' );
											foreach( array_keys( $pointers ) as $p ) {
												$cust_data[$p] = wppa_sanitize_custom_field( $data_arr[$pointers[$p]] );
											}
											wppa_update_photo( array( 'id' => $photo['id'], 'custom' => serialize( $cust_data ) ) );
											$photos_processed_csv ++;
										}
										wppa_log( 'dbg', 'Processed: ' . $data_arr[0] );
									}

									// This line could not be processed
									else {
										wppa_log( 'dbg', 'Could not find: ' . $data_arr[0] );

										// Write back to original file
										fputs( $write_handle, $dataline );
										$photos_skipped_csv++;
										$this_skipped++;
									}
									echo '.';
								}

								// Time up?
								if ( wppa_is_time_up() && wppa_switch( 'auto_continue' ) ) {
									wppa( 'continue', 'continue' );

									// Copy rest of file back to original
									while ( ! feof( $handle ) ) {
										$temp = fgets( $handle, 4096 );
										fputs( $write_handle, $temp );
									}
								}
							}

							fclose( $handle );
							fclose( $write_handle );

							$csvcount++;

							// Remove tempfile
							unlink( $tempfile );

							// Remove orig file
							if ( ! $this_skipped && ! wppa_is_time_up() ) {
								unlink( $file );
							}
						}
					}
				}
			}
		}
	}

	wppa_ok_message( __( 'Done processing files.' , 'wp-photo-album-plus') );

	if ( $pcount == '0' && $acount == '0' && $zcount == '0' && $dircount == '0' && $photocount == '0' && $videocount == '0' && $audiocount == '0' && $csvcount == '0' ) {
		wppa_warning_message( __( 'No files to import.' , 'wp-photo-album-plus') );
	}
	else {
		$msg = '';
		if ( $zcount ) $msg .= $zcount.' '.__( 'Zipfiles extracted.' , 'wp-photo-album-plus').' ';
		if ( $acount ) $msg .= $acount.' '.__( 'Albums created.' , 'wp-photo-album-plus').' ';
		if ( $dircount ) $msg .= $dircount.' '.__( 'Directory to album imports.' , 'wp-photo-album-plus').' ';
		if ( $photocount ) $msg .= ' '.sprintf( __( 'With total %s photos.','wppa' , 'wp-photo-album-plus'), $photocount ).' ';
		if ( $pcount ) {
			if ( isset( $_POST['wppa-update'] ) ) {
				$msg .= $pcount.' '.__( 'Photos updated' , 'wp-photo-album-plus');
				if ( $totpcount != $pcount ) {
					$msg .= ' '.sprintf( __( 'to %s locations' , 'wp-photo-album-plus'), $totpcount );
				}
				$msg .= '.';
			}
			else $msg .= $pcount.' '.__( 'single photos imported.' , 'wp-photo-album-plus').' ';
		}
		if ( $videocount ) {
			$msg .= $videocount.' '.__( 'Videos imported.' , 'wp-photo-album-plus');
		}
		if ( $audiocount ) {
			$msg .= $audiocount.' '.__( 'Audios imported.' , 'wp-photo-album-plus');
		}
		if ( $csvcount ) {
			$msg .= $csvcount . ' ' . __( 'CSVs imported,' , 'wp-photo-album-plus') . ' ' .
					$photos_processed_csv .' '. __( 'photos processed.', 'wp-photo-album-plus') . ' ' .
					$photos_skipped_csv . ' ' . __('photos skipped.', 'wp-photo-album-plus');
		}
		wppa_ok_message( $msg );
		wppa_set_last_album( $album );
	}
}

function wppa_get_zipcount( $files ) {
	$result = 0;
	if ( $files ) {
		foreach ( $files as $file ) {
			$ext = strtolower( substr( strrchr( $file, "." ), 1 ) );
			if ( $ext == 'zip' ) $result++;
		}
	}
	return $result;
}

function wppa_get_albumcount( $files ) {
	$result = 0;
	if ( $files ) {
		foreach ( $files as $file ) {
			$ext = strtolower( substr( strrchr( $file, "." ), 1 ) );
			if ( $ext == 'amf' ) $result++;
		}
	}
	return $result;
}

function wppa_get_photocount( $files ) {
global $wppa_supported_photo_extensions;

	$result = 0;
	if ( $files ) {
		foreach ( $files as $file ) {
			$ext = strtolower( wppa_get_ext( $file ) );
			if ( in_array( $ext, $wppa_supported_photo_extensions ) ) $result++;
		}
	}
	return $result;
}

function wppa_get_video_count( $files ) {
global $wppa_supported_video_extensions;

	$result = 0;
	if ( $files ) {
		foreach ( $files as $file ) {
			$ext = strtolower( wppa_get_ext( $file ) );
			if ( in_array( $ext, $wppa_supported_video_extensions ) ) $result++;
		}
	}
	return $result;
}

function wppa_get_audio_count( $files ) {
global $wppa_supported_audio_extensions;

	$result = 0;
	if ( $files ) {
		foreach ( $files as $file ) {
			$ext = strtolower( wppa_get_ext( $file ) );
			if ( in_array( $ext, $wppa_supported_audio_extensions ) ) $result++;
		}
	}
	return $result;
}


// Find dir is new album candidates
function wppa_get_dircount( $files ) {
	$result = 0;
	if ( $files ) {
		foreach ( $files as $file ) {
			if ( basename( $file ) == '.' ) {}
			elseif ( basename( $file ) == '..' ) {}
			elseif ( is_dir( $file ) ) $result++;
		}
	}
	return $result;
}

// Find .csv file count
function wppa_get_csvcount( $files ) {
	$result = 0;
	if ( $files ) {
		foreach ( $files as $file ) {
			if ( is_file( $file ) ) {
				if ( strtolower( wppa_get_ext( $file ) ) == 'csv' ) $result++;
			}
		}
	}
	return $result;
}

function wppa_get_meta_name( $file, $opt = '' ) {
	return wppa_get_meta_data( $file, 'name', $opt );
}
function wppa_get_meta_album( $file, $opt = '' ) {
	return wppa_get_meta_data( $file, 'albm', $opt );
}
function wppa_get_meta_desc( $file, $opt = '' ) {
	return wppa_get_meta_data( $file, 'desc', $opt );
}
function wppa_get_meta_porder( $file, $opt = '' ) {
	return wppa_get_meta_data( $file, 'pord', $opt );
}
function wppa_get_meta_linkurl( $file, $opt = '' ) {
	return wppa_get_meta_data( $file, 'lnku', $opt );
}
function wppa_get_meta_linktitle( $file, $opt = '' ) {
	return wppa_get_meta_data( $file, 'lnkt', $opt );
}

function wppa_get_meta_data( $file, $item, $opt ) {
	$result = '';
	$opt2 = '';
	if ( $opt == '( ' ) $opt2 = ' )';
	if ( $opt == '{' ) $opt2 = '}';
	if ( $opt == '[' ) $opt2 = ']';
	if ( is_file( $file ) ) {
		$handle = fopen( $file, "r" );
		if ( $handle ) {
			while ( ( $buffer = fgets( $handle, 4096 ) ) !== false ) {
				if ( substr( $buffer, 0, 5 ) == $item.'=' ) {
					if ( $opt == '' ) $result = substr( $buffer, 5, strlen( $buffer )-6 );
					else $result = $opt.__( substr( $buffer, 5, strlen( $buffer )-6 ) ).$opt2;		// Translate for display purposes only
				}
			}
			if ( !feof( $handle ) ) {
				_e( 'Error: unexpected fgets() fail in wppa_get_meta_data().' , 'wp-photo-album-plus');
			}
			fclose( $handle );
		}
	}
	return $result;
}


function wppa_extract( $xpath, $delz ) {
// There are two reasons that we do not allow the directory structure from the zipfile to be restored.
// 1. we may have no create dir access rights.
// 2. we can not reach the pictures as we only glob the users depot and not lower.
// We extract all files to the users depot.
// The illegal files will be deleted there by the wppa_sanitize_files routine,
// so there is no chance a depot/subdir/destroy.php or the like will get a chance to be created.
// dus...

	$err = '0';
	if ( ! class_exists( 'ZipArchive' ) ) {
		$err = '3';
		wppa_error_message( __( 'Class ZipArchive does not exist! Check your php configuration' , 'wp-photo-album-plus') );
	}
	else {

		// Start security fix
		$path = wppa_sanitize_file_name( $xpath );
		if ( ! file_exists( $xpath ) ) {
			wppa_error_message( 'Zipfile '.$path.' does not exist.' );
//			unlink( $xpath );
			$err = '4';
			return $err;
		}
		// End security fix

		$ext = strtolower( wppa_get_ext( $xpath ) );
		if ( $ext == 'zip' ) {
			$zip = new ZipArchive;
			if ( $zip->open( $xpath ) === true ) {

				$supported_file_ext = array( 'jpg', 'png', 'gif', 'JPG', 'PNG', 'GIF', 'amf', 'pmf' );
				$done = '0';
				$skip = '0';
				for( $i = 0; $i < $zip->numFiles; $i++ ){
					$stat = $zip->statIndex( $i );
					$file_ext = @ end( explode( '.', $stat['name'] ) );
					if ( in_array( $file_ext, $supported_file_ext ) ) {
						$zip->extractTo( WPPA_DEPOT_PATH, $stat['name'] );
						$done++;
					}
					else {
						wppa_warning_message( sprintf( __( 'File %s is of an unsupported filetype and has been ignored during extraction.' , 'wp-photo-album-plus'), wppa_sanitize_file_name( $stat['name'] ) ) );
						$skip++;
					}
				}

				$zip->close();
				wppa_ok_message( sprintf( __( 'Zipfile %s processed. %s files extracted, %s files skipped.' , 'wp-photo-album-plus'), basename( $path ), $done, $skip ) );
				if ( $delz ) unlink( $xpath );
			} else {
				wppa_error_message( __( 'Failed to extract' , 'wp-photo-album-plus').' '.$path );
				$err = '1';
			}
		}
		else $err = '2';
	}
	return $err;
}

function wppa_import_dir_to_album( $file, $parent ) {
global $photocount;
global $wpdb;
global $wppa_session;

	// Session should survive the default hour
	wppa_extend_session();

	// see if album exists
	if ( is_dir( $file ) ) {

		// Check parent
		if ( wppa_switch( 'import_parent_check' ) ) {

			$alb = wppa_get_album_id( basename( $file ), $parent );

			// If parent = 0 ( top-level album ) and album not found,
			// try a 'separate' album ( i.e. parent = -1 ) with this name
			if ( ! $alb && $parent == '0' ) {
				$alb = wppa_get_album_id( basename( $file ), '-1' );
			}
		}

		// All albums have unique names, do'nt worry about parent
		else {
			$alb = wppa_get_album_id( basename( $file ), false );
		}

		if ( ! $alb ) {	// Album must be created
			$name	= basename( $file );
			$uplim	= wppa_opt( 'upload_limit_count' ). '/' . wppa_opt( 'upload_limit_time' );
			$alb = wppa_create_album_entry( array ( 'name' 		=> $name,
													'a_parent' 	=> $parent
													 ) );
			if ( $alb === false ) {
				wppa_error_message( __( 'Could not create album.' , 'wp-photo-album-plus').'<br/>Query = '.$query );
				wp_die( 'Sorry, cannot continue' );
			}
			else {
				wppa_set_last_album( $alb );
				wppa_flush_treecounts( $alb );
				wppa_index_add( 'album', $alb );
				wppa_create_pl_htaccess();
				wppa_ok_message( __( 'Album #' , 'wp-photo-album-plus') . ' ' . $alb . ' ( '.$name.' ) ' . __( 'Added.' , 'wp-photo-album-plus') );
				if ( wppa_switch( 'newpag_create' ) && $parent <= '0' ) {

					// Create post object
					$my_post = array(
					  'post_title'    => $name,
					  'post_content'  => str_replace( 'w#album', $alb, wppa_opt( 'newpag_content' ) ),
					  'post_status'   => wppa_opt( 'newpag_status' ),
					  'post_type'	  => wppa_opt( 'newpag_type' )
					 );

					// Insert the post into the database
					$pagid = wp_insert_post( $my_post );
					if ( $pagid ) {
						wppa_ok_message( sprintf( __( 'Page <a href="%s" target="_blank" >%s</a> created.' , 'wp-photo-album-plus'), home_url().'?page_id='.$pagid, $name ) );
						$wpdb->query( $wpdb->prepare( "UPDATE `".WPPA_ALBUMS."` SET `cover_linkpage` = %s WHERE `id` = %s", $pagid, $alb ) );
					}
					else {
						wppa_error_message( __( 'Could not create page.' , 'wp-photo-album-plus') );
					}
				}
			}
		}

		// Now import the files
		$photofiles = glob( $file.'/*' );
		if ( $photofiles ) foreach ( $photofiles as $photofile ) {
			if ( ! is_dir( $photofile ) ) {

				if ( ! isset( $wppa_session[$photofile] ) || ! wppa_switch( 'keep_import_files' ) ) {

					if ( wppa_albumphoto_exists( $alb, basename( $photofile ) ) ) {
						if ( ! wppa_switch( 'keep_import_files' ) ) {
							wppa_warning_message( 'Photo '.basename( $photofile ).' already exists in album '.$alb.'. Removed. (2)' );
						}
					}
					else {
						$bret = wppa_insert_photo( $photofile, $alb, basename( $photofile ) );
						$photocount++;
					}
					if ( ! wppa_switch( 'keep_import_files' ) ) {
						@ unlink( $photofile );
					}
					$wppa_session[$photofile] = true;
				}

				if ( wppa_is_time_up( $photocount ) ) return false;
			}
		}

		// Now go deeper, process the subdirs
		$subdirs = glob( $file.'/*' );
		if ( $subdirs ) foreach ( $subdirs as $subdir ) {
			if ( is_dir( $subdir ) ) {
				if ( basename( $subdir ) != '.' && basename( $subdir ) != '..' ) {
					$bret = wppa_import_dir_to_album( $subdir, $alb );
					if ( ! $bret ) return false;	// Time out
				}
			}
		}
		@ rmdir( $file );	// Try to remove dir, ignore error
	}
	else {
		wppa_dbg_msg( 'Invalid file in wppa_import_dir_to_album(): '.$file );
		return false;
	}
	return true;
}

function wppa_is_wppa_tree( $file ) {

	$temp = explode( '/wppa/', $file );
	if ( count( $temp ) == '2' ) {
		$temp[1] = wppa_expand_id( wppa_strip_ext( $temp[1] ) ) . '.' . wppa_get_ext( $temp[1] );
		$newf = implode( '/wppa/', $temp );
		wppa( 'is_wppa_tree', ( $newf != $file ) );
	}
	else {
		wppa( 'is_wppa_tree', false );
	}
	return wppa( 'is_wppa_tree' );
}

function wppa_compress_tree_path( $path ) {

	$result = $path;
	$temp = explode( '/wppa/', $path );
	if ( count( $temp ) == '2' ) {
		$temp[1] = str_replace( '/', '', $temp[1] );
		$result = implode( '/wppa/', $temp );
	}
	return $result;
}

function wppa_expand_tree_path( $path ) {

	$result = $path;
	$temp = explode( '/wppa/', $path );
	if ( count( $temp ) == '2' ) {
		$temp[1] = wppa_expand_id( wppa_strip_ext( $temp[1] ) ) . '.' . wppa_get_ext( $temp[1] );
		$result = implode( '/wppa/', $temp );
	}
	return $result;
}

function wppa_abs_walktree( $root, $source ) {
static $void_dirs;

	// Init void dirs
	if ( ! $void_dirs ) {
		$void_dirs = array( '.', '..',
							'wp-admin',
							'wp-includes',
							'themes',
							'upgrade',
							'plugins',
							'languages',
							'wppa',
							( wppa_switch('allow_import_source') ? '' : 'wppa-source' ),
							);
	}

	// If currently in selected dir, set selected
	$sel = $root == $source ? ' selected="selected"' : '';

	// Set disabled if there are no files inside
	$n_files = count( glob( $root . '/*' ) );
	$n_dirs  = count( glob( $root . '/*', GLOB_ONLYDIR ) );
	$dis     = $n_files == $n_dirs ? ' disabled="disabled"' : '';

	// Check for (sub)depot
	$my_depot = __( '--- My depot --- ' ,'wp-photo-album-plus' );
	$display  = str_replace( WPPA_DEPOT_PATH, $my_depot, $root );
	if ( strpos( $display, $my_depot ) !== false ) {
		$dis = '';
	}

	// Check for ngg gallery dir
	$ngg_opts = get_option( 'ngg_options', false );
	if ( $ngg_opts ) {
		$ngg_gal =  __( '--- Ngg Galleries --- ', 'wp-photo-album-plus' );
		$display = str_replace( rtrim( $ngg_opts['gallerypath'], '/' ), $ngg_gal, $display );
		$pos = strpos( $display, $ngg_gal );
		if ( $pos ) {
			$display = substr( $display, $pos );
		}
	}

	// Remove ABSPATH from display string
	$display = str_replace( ABSPATH, '', $display );

	// Output the selecion if not in the wp dir
	if ( $root.'/' != ABSPATH ) {
		echo "\n" . '<option' .
						' value="' . $root . '"' .
						$sel .
						$dis .
						' data-nfiles="' . $n_files . '"' .
						' data-ndirs="' . $n_dirs . '"' .
						' >' .
						$display .
					'</option>';
	}

	// See if subdirs exist
	$dirs = glob( $root . '/*', GLOB_ONLYDIR );

	// Go deeper if not in a list of void disnames
	if ( $dirs ) foreach( $dirs as $path ) {
		$dir = basename( $path );
		if ( ! in_array( $dir, $void_dirs ) ) {
			$newroot = $root . '/' . $dir;
			wppa_abs_walktree( $newroot, $source );
		}
	}
}