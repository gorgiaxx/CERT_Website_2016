<?php
/* wppa-photo-admin-autosave.php
* Package: wp-photo-album-plus
*
* edit and delete photos
* Version 6.4.02
*
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

// Edit photo for owners of the photo(s) only
function _wppa_edit_photo() {

	// Check input
	wppa_vfy_arg( 'photo' );

	// Edit Photo
	if ( isset( $_GET['photo'] ) ) {
		$photo = $_GET['photo'];
		$thumb = wppa_cache_thumb( $photo );
		if ( $thumb['owner'] == wppa_get_user() ) { ?>
			<div class="wrap">
				<h2><?php _e( 'Edit photo' , 'wp-photo-album-plus') ?></h2>
				<?php wppa_album_photos( '', $photo ) ?>
			</div>
<?php	}
		else {
			wp_die( 'You do not have the rights to do this' );
		}
	}
	else {	// Edit all photos owned by current user
		?>
			<div class="wrap">
				<h2><?php _e( 'Edit photos' , 'wp-photo-album-plus') ?></h2>
				<?php wppa_album_photos( '', '', wppa_get_user() ) ?>
			</div>
		<?php
	}
}

// Moderate photos
function _wppa_moderate_photos() {

	// Check input
	wppa_vfy_arg( 'photo' );

	if ( isset( $_GET['photo'] ) ) {
		$photo = $_GET['photo'];
	}
	else $photo = '';
	?>
		<div class="wrap">
			<h2><?php _e( 'Moderate photos' , 'wp-photo-album-plus') ?></h2>
			<?php wppa_album_photos( '', $photo, '', true ) ?>
		</div>
	<?php
}

// The photo edit list. Also used in wppa-album-admin-autosave.php
function wppa_album_photos( $album = '', $photo = '', $owner = '', $moderate = false ) {
global $wpdb;

	// Check input
	wppa_vfy_arg( 'wppa-page' );

	$pagesize 	= wppa_opt( 'photo_admin_pagesize' );
	$page 		= isset ( $_GET['wppa-page'] ) ? $_GET['wppa-page'] : '1';
	$skip 		= ( $page - '1' ) * $pagesize;
	$limit 		= ( $pagesize < '1' ) ? '' : ' LIMIT '.$skip.','.$pagesize;

	if ( $album ) {
		if ( $album == 'search' ) {
			$count 	= wppa_get_edit_search_photos( '', 'count_only' );
			$photos = wppa_get_edit_search_photos( $limit );
			$link 	= wppa_dbg_url( get_admin_url().'admin.php?page=wppa_admin_menu&tab=edit&edit_id='.$album.'&wppa-searchstring='.wppa_sanitize_searchstring($_REQUEST['wppa-searchstring']) );
		}
		else {
			$counts = wppa_treecount_a( $album );
			$count = $counts['selfphotos'] + $counts['pendphotos'];
			$photos = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `'.WPPA_PHOTOS.'` WHERE `album` = %s '.wppa_get_photo_order( $album, 'norandom' ).$limit, $album ), ARRAY_A );
			$link = wppa_dbg_url( get_admin_url().'admin.php?page=wppa_admin_menu&tab=edit&edit_id='.$album );
		}
	}
	elseif ( $photo && ! $moderate ) {
		$count = '1';
		$photos = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `'.WPPA_PHOTOS.'` WHERE `id` = %s', $photo ), ARRAY_A );
		$link = '';
	}
	elseif ( $owner ) {
		$count = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM `'.WPPA_PHOTOS.'` WHERE `owner` = %s', $owner ) );
		$photos = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `'.WPPA_PHOTOS.'` WHERE `owner` = %s ORDER BY `timestamp` DESC'.$limit, $owner ), ARRAY_A );
		$link = wppa_dbg_url( get_admin_url().'admin.php?page=wppa_edit_photo' );
	}
	elseif ( $moderate ) {
		if ( ! current_user_can( 'wppa_moderate' ) ) wp_die( __( 'You do not have the rights to do this' , 'wp-photo-album-plus') );
		if ( $photo ) {
			$count = '1';
			$photos = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `'.WPPA_PHOTOS.'` WHERE `id` = %s', $photo ), ARRAY_A );
			$link = '';
		}
		else {
			// Photos with pending comments?
			$cmt = $wpdb->get_results( "SELECT `photo` FROM `".WPPA_COMMENTS."` WHERE `status` = 'pending'", ARRAY_A );

			if ( $cmt ) {
				$orphotois = '';
				foreach ( $cmt as $c ) {
					$orphotois .= "OR `id` = ".$c['photo']." ";
				}
			}
			else $orphotois = '';
			$count = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM `'.WPPA_PHOTOS.'` WHERE `status` = %s '.$orphotois, 'pending' ) );
			$photos = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `'.WPPA_PHOTOS.'` WHERE `status` = %s '.$orphotois.' ORDER BY `timestamp` DESC'.$limit, 'pending' ), ARRAY_A );
			$link = wppa_dbg_url( get_admin_url().'admin.php?page=wppa_moderate_photos' );
		}
		if ( empty( $photos ) ) {
			if ( $photo ) echo '<p>'.__( 'This photo is no longer awaiting moderation.' , 'wp-photo-album-plus').'</p>';
			else echo '<p>'.__( 'There are no photos awaiting moderation at this time.' , 'wp-photo-album-plus').'</p>';
			if ( wppa_user_is( 'administrator' ) ) {
				echo '<h3>'.__( 'Manage all photos by timestamp' , 'wp-photo-album-plus').'</h3>';
				$count = $wpdb->get_var( "SELECT COUNT(*) FROM `".WPPA_PHOTOS."`" );
				$photos = $wpdb->get_results( "SELECT * FROM `".WPPA_PHOTOS."` ORDER BY `timestamp` DESC".$limit, ARRAY_A );
				$link = wppa_dbg_url( get_admin_url().'admin.php?page=wppa_moderate_photos' );
			}
			else return;
		}
	}
	else wppa_dbg_msg( 'Missing required argument in wppa_album_photos() 1', 'red', 'force' );

	if ( $link && isset( $_REQUEST['quick'] ) ) $link .= '&quick';

	wppa_show_search_statistics();

	if ( empty( $photos ) ) {
		if ( $photo ) {
			echo 	'<div id="photoitem-'.$photo.'" class="photoitem" style="width: 99%; background-color: rgb( 255, 255, 224 ); border-color: rgb( 230, 219, 85 );">
						<span style="color:red">'.sprintf( __( 'Photo %s has been removed.' , 'wp-photo-album-plus'), $photo ).'</span>
					</div>';
		}
		else {
			if ( isset( $_REQUEST['wppa-searchstring'] ) ) {
				echo '<h3>'.__( 'No photos matching your search criteria.' , 'wp-photo-album-plus').'</h3>';
			}
			else {
				echo '<h3>'.__( 'No photos yet in this album.' , 'wp-photo-album-plus').'</h3>';
			}
		}
	}
	else {
		$wms 	= array( 'toplft' => __( 'top - left' , 'wp-photo-album-plus'), 'topcen' => __( 'top - center' , 'wp-photo-album-plus'), 'toprht' => __( 'top - right' , 'wp-photo-album-plus'),
						 'cenlft' => __( 'center - left' , 'wp-photo-album-plus'), 'cencen' => __( 'center - center' , 'wp-photo-album-plus'), 'cenrht' => __( 'center - right' , 'wp-photo-album-plus'),
						 'botlft' => __( 'bottom - left' , 'wp-photo-album-plus'), 'botcen' => __( 'bottom - center' , 'wp-photo-album-plus'), 'botrht' => __( 'bottom - right' , 'wp-photo-album-plus'), );
		$temp 	= wppa_get_water_file_and_pos( '0' );
		$wmfile = isset( $temp['select'] ) ? $temp['select'] : '';
		$wmpos 	= isset( $temp['pos'] ) && isset ( $wms[$temp['pos']] ) ? $wms[$temp['pos']] : '';

		wppa_admin_page_links( $page, $pagesize, $count, $link );

		foreach ( $photos as $photo ) {
			$is_multi = wppa_is_multi( $photo['id'] );
			$is_video = wppa_is_video( $photo['id'] );
			$has_audio = wppa_has_audio( $photo['id'] );
			?>
			<a id="photo_<?php echo $photo['id'] ?>" name="photo_<?php echo $photo['id'] ?>"></a>
			<div class="widefat wppa-table-wrap" id="photoitem-<?php echo $photo['id'] ?>" style="width:99%; position: relative;" >

				<!-- Left half starts here -->
				<div style="width:49.5%; float:left; border-right:1px solid #ccc; margin-right:0;">
					<input type="hidden" id="photo-nonce-<?php echo $photo['id'] ?>" value="<?php echo wp_create_nonce( 'wppa_nonce_'.$photo['id'] );  ?>" />
					<table class="wppa-table wppa-photo-table" style="width:98%" >
						<tbody>

							<!-- Preview -->
							<tr>
								<th>
									<label ><?php echo 'ID = '.$photo['id'].'. '.__( 'Preview:' , 'wp-photo-album-plus'); ?></label>
									<br />
									<?php echo __( 'Crypt', 'wp-photo-album-plus' ) . ': ' . $photo['crypt'] . '<br/>' ?>
									<?php echo sprintf( __( 'Album: %d<br />(%s)' , 'wp-photo-album-plus'), $photo['album'], wppa_get_album_name( $photo['album'] ) ) ?>
									<br /><br />
									<?php if ( ! $is_video ) { ?>
										<?php _e( 'Rotate' , 'wp-photo-album-plus') ?>
										<a onclick="if ( confirm( '<?php echo esc_js( __( 'Are you sure you want to rotate this photo left?' , 'wp-photo-album-plus') ) ?>' ) ) wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'rotleft', 0, <?php echo ( wppa( 'front_edit' ) ? 'false' : 'true' ) ?> ); " ><?php _e( 'left' , 'wp-photo-album-plus'); ?></a>

										<a onclick="if ( confirm( '<?php echo esc_js( __( 'Are you sure you want to rotate this photo 180&deg;?' , 'wp-photo-album-plus') ) ?>' ) ) wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'rot180', 0, <?php echo ( wppa( 'front_edit' ) ? 'false' : 'true' ) ?> ); " ><?php _e( '180&deg;' , 'wp-photo-album-plus'); ?></a>

										<a onclick="if ( confirm( '<?php echo esc_js( __( 'Are you sure you want to rotate this photo right?' , 'wp-photo-album-plus') ) ?>' ) ) wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'rotright', 0, <?php echo ( wppa( 'front_edit' ) ? 'false' : 'true' ) ?> ); " ><?php _e( 'right' , 'wp-photo-album-plus'); ?></a>
										<br />

										<span style="font-size: 9px; line-height: 10px; color:#666;">
											<?php if ( wppa( 'front_edit' ) ) {
												_e( 'If it says \'Photo rotated\', the photo is rotated.' , 'wp-photo-album-plus');
											}
											else {
												$refresh = '<a onclick="wppaReload()" >'.__( 'Refresh' , 'wp-photo-album-plus').'</a>';
												echo sprintf( __( 'If it says \'Photo rotated\', the photo is rotated. %s the page.' , 'wp-photo-album-plus'), $refresh );
											}
											?>
										</span>
									<?php } ?>
								</th>
								<td>
									<?php
									$src = wppa_get_thumb_url( $photo['id'] );
									$big = wppa_get_photo_url( $photo['id'] );
									if ( $is_video ) {
										reset( $is_video );
										$big = str_replace( 'xxx', current( $is_video ), $big );
										?>
										<a href="<?php echo $big ?>" target="_blank" title="<?php echo esc_attr( __( 'Preview fullsize video' , 'wp-photo-album-plus') ) ?>" >
											<?php echo wppa_get_video_html( array( 	'id' 		=> $photo['id'],
																					'width' 	=> '160',
																					'height' 	=> '160' * wppa_get_videoy( $photo['id'] ) / wppa_get_videox( $photo['id'] ),
																					'controls' 	=> false,
																					'use_thumb' => true
																				) ) ?>
										</a><?php
									}
									else {
										if ( $has_audio ) {
											$big = wppa_fix_poster_ext( $big, $photo['id'] );
											$src = wppa_fix_poster_ext( $src, $photo['id'] );
										}
										?>
										<a href="<?php echo $big ?>" target="_blank" title="<?php echo esc_attr( __( 'Preview fullsize photo' , 'wp-photo-album-plus') ) ?>" >
											<img src="<?php echo( $src ) ?>" alt="<?php echo( $photo['name'] ) ?>" style="max-width: 160px; vertical-align:middle;" />
										</a><?php
										if ( $has_audio ) {
											$audio = wppa_get_audio_html( array( 	'id' 		=> $photo['id'],
																					'width' 	=> '160',
																					'controls' 	=> true
																				) );
											?>
											<br />
											<?php
											if ( $audio ) {
												echo $audio;
											}
											else {
												echo '<span style="color:red;">' . __( 'Audio disabled' , 'wp-photo-album-plus') . '</span>';
											}
										}
									} ?>
								</td>
							</tr>

							<!-- Upload -->
							<tr>
								<th>
									<label><?php _e( 'Upload:' , 'wp-photo-album-plus'); ?></label>
								</th>
								<td>
									<?php
									$timestamp = $photo['timestamp'];
									if ( $timestamp ) {
										echo wppa_local_date( get_option( 'date_format', "F j, Y," ).' '.get_option( 'time_format', "g:i a" ), $timestamp ).' '.__( 'local time' , 'wp-photo-album-plus').' ';
									}
									if ( $photo['owner'] ) {
										if ( wppa_switch( 'photo_owner_change' ) && wppa_user_is( 'administrator' ) ) {
											echo '</td></tr><tr><th><label>' . __( 'Owned by:' , 'wp-photo-album-plus') . '</label></th><td>';
											echo '<input type="text" onkeyup="wppaAjaxUpdatePhoto( \''.$photo['id'].'\', \'owner\', this )" onchange="wppaAjaxUpdatePhoto( \''.$photo['id'].'\', \'owner\', this )" value="'.$photo['owner'].'" />';
										}
										else {
											echo __( 'By:' , 'wp-photo-album-plus').' '.$photo['owner'];
										}
									}
									?>
								</td>
							</tr>

							<!-- Modified -->
							<tr>
								<th>
									<label><?php _e( 'Modified:' , 'wp-photo-album-plus'); ?></label>
								</th>
								<td>
									<?php $modified = $photo['modified'];
									if ( $modified > $timestamp ) {
										echo wppa_local_date( get_option( 'date_format', "F j, Y," ).' '.get_option( 'time_format', "g:i a" ), $modified ).' '.__( 'local time' , 'wp-photo-album-plus');
									}
									else {
										_e( 'Not modified', 'wp-photo-album-plus' );
									}
									?>
								</td>
							</tr>

							<!-- EXIF Date -->
							<tr>
								<th>
									<label><?php _e( 'EXIF Date' , 'wp-photo-album-plus') ?></label>
								</th>
								<td>
								<?php
								if ( wppa_user_is( 'administrator' ) ) {
									echo '<input type="text" onkeyup="wppaAjaxUpdatePhoto( \''.$photo['id'].'\', \'exifdtm\', this )" onchange="wppaAjaxUpdatePhoto( \''.$photo['id'].'\', \'exifdtm\', this )" value="'.$photo['exifdtm'].'" />';
								}
								else {
									echo $photo['exifdtm'];
								}
								?>
								</td>
							</tr>

							<!-- Rating -->
							<tr  >
								<th  >
									<label><?php _e( 'Rating:' , 'wp-photo-album-plus') ?></label>
								</th>
								<td class="wppa-rating" >
									<?php
									$entries = wppa_get_rating_count_by_id( $photo['id'] );
									if ( $entries ) {
										echo __( 'Entries:' , 'wp-photo-album-plus') . ' ' . $entries . '. ' . __( 'Mean value:' , 'wp-photo-album-plus') . ' ' . wppa_get_rating_by_id( $photo['id'], 'nolabel' ) . '.';
									}
									else {
										_e( 'No ratings for this photo.' , 'wp-photo-album-plus');
									}
									$dislikes = wppa_dislike_get( $photo['id'] );
									if ( $dislikes ) {
										echo ' <span style="color:red" >'.sprintf( __( 'Disliked by %d visitors' , 'wp-photo-album-plus'), $dislikes ).'</span>';
									}
									$pending = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_RATING."` WHERE `photo` = %s AND `status` = 'pending'", $photo['id'] ) );
									if ( $pending ) {
										echo ' <span style="color:orange" >'.sprintf( __( '%d pending votes.' , 'wp-photo-album-plus'), $pending ).'</span>';
									}
									?>

								</td>
							</tr>

							<!-- Views -->
							<tr  >
								<th  >
									<label><?php _e( 'Views' , 'wp-photo-album-plus'); ?></label>
								</th>
								<td >
									<?php echo $photo['views'] ?>
								</td>
							</tr>

							<!-- P_order -->
							<?php if ( ! wppa_switch( 'porder_restricted' ) || wppa_user_is( 'administrator' ) ) { ?>
							<tr  >
								<th  >
									<label><?php _e( 'Photo sort order #:' , 'wp-photo-album-plus'); ?></label>
								</th>
								<td >
									<input type="text" id="porder-<?php echo $photo['id'] ?>" value="<?php echo( $photo['p_order'] ) ?>" style="width: 50px" onkeyup="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'p_order', this )" onchange="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'p_order', this )" />
								</td>
							</tr>
							<?php } ?>

							<?php if ( ! isset( $_REQUEST['quick'] ) ) { ?>
								<?php if ( ! isset( $album_select[$photo['album']] ) ) $album_select[$photo['album']] = wppa_album_select_a( array( 'checkaccess' => true, 'path' => wppa_switch( 'hier_albsel' ), 'exclude' => $photo['album'], 'selected' => '0', 'addpleaseselect' => true ) ) ?>
								<!-- Move -->
								<tr  >
									<th  >
										<input type="button" style="" onclick="if( document.getElementById( 'moveto-<?php echo( $photo['id'] ) ?>' ).value != 0 ) { if ( confirm( '<?php esc_js( _e( 'Are you sure you want to move this photo?' , 'wp-photo-album-plus') ) ?>' ) ) wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'moveto', document.getElementById( 'moveto-<?php echo( $photo['id'] ) ?>' ) ) } else { alert( '<?php echo esc_js( __( 'Please select an album to move the photo to first.' , 'wp-photo-album-plus') ) ?>' ); return false;}" value="<?php echo esc_attr( __( 'Move photo to' , 'wp-photo-album-plus') ) ?>" />
									</th>
									<td >
										<select id="moveto-<?php echo $photo['id'] ?>" style="width:100%;" ><?php echo $album_select[$photo['album']] ?></select>
									</td>
								</tr>
								<!-- Copy -->
								<tr  >
									<th  >
										<input type="button" style="" onclick="if ( document.getElementById( 'copyto-<?php echo( $photo['id'] ) ?>' ).value != 0 ) { if ( confirm( '<?php esc_js( _e( 'Are you sure you want to copy this photo?' , 'wp-photo-album-plus') ) ?>' ) ) wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'copyto', document.getElementById( 'copyto-<?php echo( $photo['id'] ) ?>' ) ) } else { alert( '<?php echo esc_js( __( 'Please select an album to copy the photo to first.' , 'wp-photo-album-plus') ) ?>' ); return false;}" value="<?php echo esc_attr( __( 'Copy photo to' , 'wp-photo-album-plus') ) ?>" />
									</th>
									<td >
										<select id="copyto-<?php echo( $photo['id'] ) ?>" style="width:100%;" ><?php echo $album_select[$photo['album']] ?></select>
									</td>
								</tr>
							<?php } ?>

							<!-- Delete -->
							<?php if ( ! wppa( 'front_edit' ) ) { ?>
							<tr  >
								<th  style="padding-top:0; padding-bottom:4px;">
									<input type="button" style="color:red;" onclick="if ( confirm( '<?php _e( 'Are you sure you want to delete this photo?' , 'wp-photo-album-plus') ?>' ) ) wppaAjaxDeletePhoto( <?php echo $photo['id'] ?> )" value="<?php echo esc_attr( __( 'Delete photo' , 'wp-photo-album-plus') ) ?>" />
								</th>
							</tr>
							<?php } ?>

							<!-- Auto Page -->
							<?php if ( wppa_switch( 'auto_page' ) && ( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) ) { ?>
							<tr style=="vertical-align:bottom;" >
								<th  style="padding-top:0; padding-bottom:4px;">
									<label>
										<?php _e( 'Autopage Permalink:' , 'wp-photo-album-plus'); ?>
									</label>
								</th>
								<td >
									<?php echo get_permalink( wppa_get_the_auto_page( $photo['id'] ) ) ?>
								</td>
							</tr>
							<?php } ?>

							<!-- Link url -->
							<?php if ( ! wppa_switch( 'link_is_restricted' ) || wppa_user_is( 'administrator' ) ) { ?>
								<tr  >
									<th  >
										<label><?php _e( 'Link url:' , 'wp-photo-album-plus') ?></label>
									</th>
									<td >
										<input type="text" style="width:60%;" onkeyup="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'linkurl', this )" onchange="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'linkurl', this )" value="<?php echo( stripslashes( $photo['linkurl'] ) ) ?>" />
										<select style="float:right;" onchange="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'linktarget', this )" >
											<option value="_self" <?php if ( $photo['linktarget'] == '_self' ) echo 'selected="selected"' ?>><?php _e( 'Same tab' , 'wp-photo-album-plus') ?></option>
											<option value="_blank" <?php if ( $photo['linktarget'] == '_blank' ) echo 'selected="selected"' ?>><?php _e( 'New tab' , 'wp-photo-album-plus') ?></option>
										</select>
									</td>
								</tr>

								<!-- Link title -->
								<tr  >
									<th  >
										<label><?php _e( 'Link title:' , 'wp-photo-album-plus') ?></label>
									</th>
									<td >
										<input type="text" style="width:97%;" onkeyup="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'linktitle', this )" onchange="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'linktitle', this )" value="<?php echo( stripslashes( $photo['linktitle'] ) ) ?>" />
									</td>
								</tr>
								<?php if ( current_user_can( 'wppa_settings' ) ) { ?>
								<tr style="padding-left:10px; font-size:9px; line-height:10px; color:#666;" >
									<td colspan="2" style="padding-top:0" >
										<?php _e( 'If you want this link to be used, check \'PS Overrule\' checkbox in table VI.' , 'wp-photo-album-plus') ?>
									</td>
								</tr>
								<?php } ?>
							<?php } ?>

							<!-- Alt custom field -->
							<?php
							if ( wppa_opt( 'alt_type' ) == 'custom' ) { ?>
							<tr  >
								<th  >
									<label><?php _e( 'HTML Alt attribute:' , 'wp-photo-album-plus') ?></label>
								</th>
								<td >
									<input type="text" style="width:100%;" onkeyup="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'alt', this )" onchange="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'alt', this )" value="<?php echo( stripslashes( $photo['alt'] ) ) ?>" />
								</td>
							</tr>
							<?php } ?>

						</tbody>
					</table>
				</div>

				<!-- Right half starts here -->
				<div style="width:50%; float:left; border-left:1px solid #ccc; margin-left:-1px;">
					<table class="wppa-table wppa-photo-table" >
						<tbody>

							<!-- Filename -->
							<tr>
								<th>
									<label><?php _e( 'Filename:' , 'wp-photo-album-plus'); ?></label>
								</th>
								<td>
									<?php
									echo $photo['filename'];
									if ( wppa_user_is( 'administrator' ) || ! wppa_switch( 'reup_is_restricted' ) ) { ?>
										<input type="button" onclick="jQuery( '#re-up-<?php echo $photo['id'] ?>' ).css( 'display', '' );" value="<?php _e('Update file', 'wp-photo-album-plus') ?>" />
									<?php } ?>
								</td>
							</tr>
							<?php if ( wppa_user_is( 'administrator' ) || ! wppa_switch( 'reup_is_restricted' ) ) { ?>
							<tr id="re-up-<?php echo $photo['id'] ?>" style="display:none" >
								<th>
								</th>
								<td>
									<form id="wppa-re-up-form-<?php echo $photo[ 'id'] ?>" onsubmit="wppaReUpload( event,<?php echo $photo['id'] ?>, '<?php echo $photo['filename'] ?>' )" >
										<input type="file" id="wppa-re-up-file-<?php echo $photo['id'] ?>" />
										<input type="submit" id="wppa-re-up-butn-<?php echo $photo['id'] ?>" value="<?php _e( 'Upload', 'wp-photo-album-plus') ?>" />
									</form>
								</td>
							</tr>
							<?php } ?>

							<!--- Video sizes -->
							<?php if ( $is_video ) { ?>
							<tr>
								<th>
									<label><?php _e( 'Video size:' , 'wp-photo-album-plus') ?>
								</th>
								<td>
									<table class="wppa-subtable" >
										<tr>
											<td>
												<?php _e( 'Width:' , 'wp-photo-album-plus') ?>
											</td>
											<td>
												<input style="width:50px;margin:0 4px;" onkeyup="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'videox', this ); " onchange="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'videox', this ); " value="<?php echo $photo['videox'] ?>" /><?php echo sprintf( __( 'pix, (0=default:%s)' , 'wp-photo-album-plus'), wppa_opt( 'video_width' ) ) ?>
											</td>
										</tr>
										<tr>
											<td>
												<?php _e( 'Height:' , 'wp-photo-album-plus') ?>
											</td>
											<td>
												<input style="width:50px;margin:0 4px;" onkeyup="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'videoy', this ); " onchange="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'videoy', this ); " value="<?php echo $photo['videoy'] ?>" /><?php echo sprintf( __( 'pix, (0=default:%s)' , 'wp-photo-album-plus'), wppa_opt( 'video_height' ) ) ?>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<th>
									<label><?php _e( 'Formats:' , 'wp-photo-album-plus') ?>
								</th>
								<td>
									<table class="wppa-subtable" >
										<?php
											foreach ( $is_video as $fmt ) {
												echo 	'<tr>' .
															'<td>' . $fmt . '</td>' .
															'<td>' . __( 'Filesize:' , 'wp-photo-album-plus') . '</td>' .
															'<td>' . wppa_get_filesize( str_replace( 'xxx', $fmt, wppa_get_photo_path( $photo['id'] ) ) ) . '</td>' .
														'</tr>';
											}
									?>
									</table>
								</td>
							</tr>
							<?php } ?>

							<!-- Audio -->
							<?php if ( $has_audio ) { ?>
							<tr>
								<th>
									<label><?php _e( 'Formats:' , 'wp-photo-album-plus') ?>
								</th>
								<td>
									<table class="wppa-subtable" >
										<?php
											foreach ( $has_audio as $fmt ) {
												echo 	'<tr>' .
															'<td>' . $fmt . '</td>' .
															'<td>' . __( 'Filesize:' , 'wp-photo-album-plus') . '</td>' .
															'<td>' . wppa_get_filesize( str_replace( 'xxx', $fmt, wppa_get_photo_path( $photo['id'] ) ) ) . '</td>' .
														'</tr>';
											}
									?>
									</table>
								</td>
							</tr>
							<?php } ?>

							<!-- Filesizes -->
							<tr>
								<th>
									<label><?php ( $is_video || $has_audio ) ? _e( 'Poster:', 'wp-photo-album-plus') : _e( 'Photo sizes:', 'wp-photo-album-plus') ?></label>
								</th>
								<td>
									<table class="wppa-subtable" >
										<tr>
											<td>
												<?php _e( 'Source file:' , 'wp-photo-album-plus') ?>
											</td>
												<?php $sp = wppa_get_source_path( $photo['id'] );
										if ( is_file( $sp ) ) {
											$ima 	= getimagesize( $sp ); ?>

											<td>
												<?php echo $ima['0'].' x '.$ima['1'].' px.' ?>
											</td>
											<td>
												<?php echo wppa_get_filesize( $sp ) ?>
											</td>
											<td>
												<a style="cursor:pointer; font-weight:bold;" title="<?php _e( 'Remake display file and thumbnail file' , 'wp-photo-album-plus') ?>" onclick="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'remake', this )"><?php _e( 'Remake files' , 'wp-photo-album-plus') ?></a>
											</td>
								<?php 	}
										else { ?>
											<td>
												<span style="color:orange;"><?php _e('Unavailable', 'wp-photo-album-plus') ?></span>
											</td>
											<td>
											</td>
											<td>
											</td>
								<?php 	} ?>
										</tr>
										<tr>
											<td>
												<?php _e( 'Display file:', 'wp-photo-album-plus') ?>
											</td>
												<?php $dp = wppa_fix_poster_ext( wppa_get_photo_path( $photo['id'] ), $photo['id'] );
													if ( is_file( $dp ) ) {
												?>
											<td>
												<?php echo floor( wppa_get_photox( $photo['id'] ) ) . ' x ' . floor( wppa_get_photoy( $photo['id'] ) ).' px.' ?>
											</td>
											<td>
												<?php echo wppa_get_filesize( $dp ) ?>
											</td>
											<td>
											</td>
												<?php } else { ?>
											<td>
												<span style="color:red;"><?php _e('Unavailable', 'wp-photo-album-plus') ?></span>
											</td>
											<td>
											</td>
											<td>
											</td>
												<?php } ?>
										</tr>
										<tr>
											<td>
												<?php _e( 'Thumbnail file:', 'wp-photo-album-plus') ?>
											</td>
												<?php $tp = wppa_fix_poster_ext( wppa_get_thumb_path( $photo['id'] ), $photo['id'] );
													if ( is_file( $tp ) ) {
												?>
											<td>
												<?php echo floor( wppa_get_thumbx( $photo['id'] ) ) . ' x ' . floor( wppa_get_thumby( $photo['id'] ) ) . ' px.' ?>
											</td>
											<td>
												<?php echo wppa_get_filesize( $tp ) ?>
											</td>
												<?php } else { ?>
											<td>
												<span style="color:red;"><?php _e('Unavailable', 'wp-photo-album-plus') ?></span>
											</td>
											<td>
											</td>
												<?php } ?>
											<td>
												<a style="cursor:pointer; font-weight:bold;" title="<?php _e( 'Remake thumbnail file' , 'wp-photo-album-plus') ?>" onclick="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'remakethumb', this )"><?php _e( 'Remake' , 'wp-photo-album-plus') ?></a>
											</td>
										</tr>
									</table>
								</td>
							</tr>

							<!-- Stereo -->
							<?php if ( wppa_switch( 'enable_stereo' ) ) { ?>
							<tr>
								<th>
									<label><?php _e( 'Stereophoto:' , 'wp-photo-album-plus'); ?></label>
								</th>
								<td>
									<select id="stereo-<?php echo $photo['id'] ?>" onchange="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'stereo', this )" >
										<option value="0" <?php if ( $photo['stereo'] == '0' ) echo 'selected="selected" ' ?>><?php _e( 'no stereo image or ready anaglyph', 'wp-photo-album-plus') ?></option>
										<option value="1" <?php if ( $photo['stereo'] == '1' ) echo 'selected="selected" ' ?>><?php _e( 'Left - right stereo image', 'wp-photo-album-plus') ?></option>
										<option value="-1" <?php if ( $photo['stereo'] == '-1' ) echo 'selected="selected" ' ?>><?php _e( 'Right - left stereo image', 'wp-photo-album-plus') ?></option>
									<select>
								<td>
							</tr>
							<tr>
								<th>
									<label><?php _e( 'Images:' , 'wp-photo-album-plus'); ?></label>
								</th>
								<td>
									<?php
									$files = glob( WPPA_UPLOAD_PATH . '/stereo/' . $photo['id'] . '-*.*' );
									if ( ! empty( $files ) ) {
										sort( $files );
										$c = 0;
										echo '<table><tbody>';
										foreach ( $files as $file ) {
											if ( ! $c ) echo '<tr>';
											if ( is_file( $file ) ) {
												echo '<td style="padding:0;" ><a href="' . str_replace( WPPA_UPLOAD_PATH, WPPA_UPLOAD_URL, $file ) . '" target="_blank" >' . basename( $file ) . '</a></td>';
											}
											if ( strpos( basename( $file ), '_flat' ) ) $c++;
											$c = ( $c + 1 ) % 2;
											if ( ! $c ) echo '</tr>';
										}
										if ( $c ) echo '<td style="padding:0;" ></td></tr>';
										echo '</tbody></table>';
									}
									?>
								</td>
							</tr>
							<?php } ?>

							<!-- Location -->
							<?php if ( $photo['location'] || wppa_switch( 'geo_edit' ) ) { ?>
							<tr>
								<th>
									<label><?php _e( 'Location:' , 'wp-photo-album-plus'); ?></label>
								</th>
								<td>
									<?php
									$loc = $photo['location'] ? $photo['location'] : '///';
									$geo = explode( '/', $loc );
									echo $geo['0'].' '.$geo['1'].' ';
									if ( wppa_switch( 'geo_edit' ) ) { ?>
										<?php _e( 'Lat:' , 'wp-photo-album-plus') ?><input type="text" style="width:100px;" id="lat-<?php echo $photo['id'] ?>" onkeyup="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'lat', this );" onchange="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'lat', this );" value="<?php echo $geo['2'] ?>" />
										<?php _e( 'Lon:' , 'wp-photo-album-plus') ?><input type="text" style="width:100px;" id="lon-<?php echo $photo['id'] ?>" onkeyup="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'lon', this );" onchange="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'lon', this );" value="<?php echo $geo['3'] ?>" />
										<?php if ( ! wppa( 'front_edit' ) ) { ?>
											<span class="description"><br /><?php _e( 'Refresh the page after changing to see the degrees being updated', 'wp-photo-album-plus') ?></span>
										<?php } ?>
									<?php } ?>
								</td>
							</tr>
							<?php } ?>

							<!-- Name -->
							<tr  >
								<th  >
									<label><?php _e( 'Photoname:' , 'wp-photo-album-plus'); ?></label>
								</th>
								<?php if ( wppa_switch( 'use_wp_editor' ) ) { ?>
								<td>
									<input type="text" style="width:100%;" id="pname-<?php echo $photo['id'] ?>" value="<?php echo esc_attr( stripslashes( $photo['name'] ) ) ?>" />

									<input type="button" class="button-secundary" value="<?php _e( 'Update Photo name' , 'wp-photo-album-plus') ?>" onclick="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'name', document.getElementById( 'pname-<?php echo $photo['id'] ?>' ) );" />
								</td>
								<?php }
								else { ?>
									<td>
										<input type="text" style="width:100%;" id="pname-<?php echo $photo['id'] ?>" onkeyup="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'name', this );" onchange="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'name', this );" value="<?php echo esc_attr( stripslashes( $photo['name'] ) ) ?>" />
									</td>
								<?php } ?>
							</tr>

							<!-- Description -->
							<?php if ( ! wppa_switch( 'desc_is_restricted' ) || wppa_user_is( 'administrator' ) ) { ?>
							<tr>
								<th>
									<label><?php _e( 'Description:' , 'wp-photo-album-plus'); ?></label>
								</th>
								<?php if ( wppa_switch( 'use_wp_editor' ) ) { ?>
								<td>

									<?php
									$alfaid = wppa_alfa_id( $photo['id'] );
							//		$quicktags_settings = array( 'buttons' => 'strong,em,link,block,ins,ul,ol,li,code,close' );
									wp_editor( stripslashes( $photo['description'] ), 'wppaphotodesc'.$alfaid, array( 'wpautop' => true, 'media_buttons' => false, 'textarea_rows' => '6', 'tinymce' => true ));//, 'quicktags' => $quicktags_settings ) );
									?>

									<input
										type="button" class="button-secundary" value="<?php _e( 'Update Photo description' , 'wp-photo-album-plus') ?>" onclick="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'description', document.getElementById( 'wppaphotodesc'+'<?php echo $alfaid ?>' ), false, '<?php echo $alfaid ?>' )" />
									<img id="wppa-photo-spin-<?php echo $photo['id'] ?>" src="<?php echo wppa_get_imgdir().'wpspin.gif' ?>" style="visibility:hidden" />
								</td>
								<?php }
								else { ?>
								<td>
									<textarea style="width: 100%; height:120px;" onkeyup="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'description', this )" onchange="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'description', this )" ><?php echo( stripslashes( $photo['description'] ) ) ?></textarea>
								</td>
								<?php } ?>
							</tr>
							<?php } else { ?>
							<tr>
								<th>
									<label><?php _e( 'Description:' , 'wp-photo-album-plus'); ?></label>
								</th>
								<td>
									<div style="width: 100%; height:120px; overflow:auto;" ><?php echo( stripslashes( $photo['description'] ) ) ?></div>
								</td>
							</tr>
							<?php } ?>

							<!-- Custom -->
							<?php
								if ( wppa_switch( 'custom_fields' ) ) {
									$custom = wppa_get_photo_item( $photo['id'], 'custom' );
									if ( $custom ) {
										$custom_data = unserialize( $custom );
									}
									else {
										$custom_data = array( '', '', '', '', '', '', '', '', '', '' );
									}
									foreach( array_keys( $custom_data ) as $key ) {
										if ( wppa_opt( 'custom_caption_'.$key ) ) {
											?>
												<tr>
													<th>
														<label><?php echo wppa_opt( 'custom_caption_'.$key ) . ':<br /><small>(w#cc'.$key.')</small>' ?></label>
													</th>
													<td>
														<?php echo '<small>(w#cd'.$key.')</small>' ?>
														<input 	type="text"
																style="width:85%; float:right;"
																id="pname-<?php echo $photo['id'] ?>"
																onkeyup="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'custom_<?php echo $key ?>', this );"
																onchange="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'custom_<?php echo $key ?>', this );"
																value="<?php echo esc_attr( stripslashes( $custom_data[$key] ) ) ?>"
																/>

													</td>
												</tr>
											<?php
										}
									}
								}
							?>
							<!-- Tags -->
							<tr style="vertical-align:middle;" >
								<th  >
									<label ><?php _e( 'Tags:' , 'wp-photo-album-plus') ?></label>
									<span class="description" >
										<br />&nbsp;
									</span>
								</th>
								<td >
									<input id="tags-<?php echo $photo['id'] ?>" type="text" style="width:100%;" onchange="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'tags', this )" value="<?php echo( stripslashes( trim( $photo['tags'], ',' ) ) ) ?>" />
									<span class="description" >
										<?php _e( 'Separate tags with commas.' , 'wp-photo-album-plus') ?>&nbsp;
										<?php _e( 'Examples:' , 'wp-photo-album-plus') ?>
										<select onchange="wppaAddTag( this.value, 'tags-<?php echo $photo['id'] ?>' ); wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'tags', document.getElementById( 'tags-<?php echo $photo['id'] ?>' ) )" >
											<?php $taglist = wppa_get_taglist();
											if ( is_array( $taglist ) ) {
												echo '<option value="" >'.__( '- select -' , 'wp-photo-album-plus').'</option>';
												foreach ( $taglist as $tag ) {
													echo '<option value="'.$tag['tag'].'" >'.$tag['tag'].'</option>';
												}
											}
											else {
												echo '<option value="0" >'.__( 'No tags yet' , 'wp-photo-album-plus').'</option>';
											}
											?>
										</select>
										<?php _e( 'Select to add' , 'wp-photo-album-plus') ?>
									</span>
								</td>
							</tr>

							<!-- Status -->
							<tr style="vertical-align:middle;" >
								<th>
									<label ><?php _e( 'Status:' , 'wp-photo-album-plus') ?></label>
								</th>
								<td>
								<?php if ( ( current_user_can( 'wppa_admin' ) || current_user_can( 'wppa_moderate' ) ) && ! isset( $_REQUEST['quick'] ) ) { ?>
									<table>
										<tr>
											<td>
												<select id="status-<?php echo $photo['id'] ?>" onchange="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'status', this ); wppaPhotoStatusChange( <?php echo $photo['id'] ?> ); ">
													<option value="pending" <?php if ( $photo['status']=='pending' ) echo 'selected="selected"'?> ><?php _e( 'Pending' , 'wp-photo-album-plus') ?></option>
													<option value="publish" <?php if ( $photo['status']=='publish' ) echo 'selected="selected"'?> ><?php _e( 'Publish' , 'wp-photo-album-plus') ?></option>
													<?php if ( wppa_switch( 'ext_status_restricted' ) && ! wppa_user_is( 'administrator' ) ) $dis = ' disabled'; else $dis = ''; ?>
													<option value="featured" <?php if ( $photo['status']=='featured' ) echo 'selected="selected"'; echo $dis?> ><?php _e( 'Featured' , 'wp-photo-album-plus') ?></option>
													<option value="gold" <?php if ( $photo['status'] == 'gold' ) echo 'selected="selected"'; echo $dis?> ><?php _e( 'Gold' , 'wp-photo-album-plus') ?></option>
													<option value="silver" <?php if ( $photo['status'] == 'silver' ) echo 'selected="selected"'; echo $dis?> ><?php _e( 'Silver' , 'wp-photo-album-plus') ?></option>
													<option value="bronze" <?php if ( $photo['status'] == 'bronze' ) echo 'selected="selected"'; echo $dis?> ><?php _e( 'Bronze' , 'wp-photo-album-plus') ?></option>
													<option value="scheduled" <?php if ( $photo['status'] == 'scheduled' ) echo 'selected="selected"'; echo $dis?> ><?php _e( 'Scheduled' , 'wp-photo-album-plus') ?></option>
													<option value="private" <?php if ( $photo['status'] == 'private' ) echo 'selected="selected"'; echo $dis ?> ><?php _e( 'Private' , 'wp-photo-album-plus') ?></option>
												</select>
											</td>
											<td class="wppa-datetime-<?php echo $photo['id'] ?>" >
												<?php echo wppa_get_date_time_select_html( 'photo', $photo['id'], true ) ?>
											</td>
										</tr>
									</table>
								<?php }
									else { ?>
										<input type="hidden" id="status-<?php echo $photo['id'] ?>" value="<?php echo $photo['status'] ?>" />
									<table>
										<tr>
											<td>
												<?php
													if ( $photo['status'] == 'pending' ) _e( 'Pending' , 'wp-photo-album-plus');
													elseif ( $photo['status'] == 'publish' ) _e( 'Publish' , 'wp-photo-album-plus');
													elseif ( $photo['status'] == 'featured' ) _e( 'Featured' , 'wp-photo-album-plus');
													elseif ( $photo['status'] == 'gold' ) _e( 'Gold' , 'wp-photo-album-plus');
													elseif ( $photo['status'] == 'silver' ) _e( 'Silver' , 'wp-photo-album-plus');
													elseif ( $photo['status'] == 'bronze' ) _e( 'Bronze' , 'wp-photo-album-plus');
													elseif ( $photo['status'] == 'scheduled' ) _e( 'Scheduled' , 'wp-photo-album-plus');
													elseif ( $photo['status'] == 'private' ) _e( 'Private' , 'wp-photo-album-plus');
												?>
											</td>
											<td class="wppa-datetime-<?php echo $photo['id'] ?>" >
												<?php echo wppa_get_date_time_select_html( 'photo', $photo['id'], false ) ?>
											</td>
										</tr>
									</table>
									<?php } ?>
									<span id="psdesc-<?php echo $photo['id'] ?>" class="description" style="display:none;" ><?php _e( 'Note: Featured photos should have a descriptive name; a name a search engine will look for!' , 'wp-photo-album-plus'); ?></span>

								</td>
							</tr>

							<!-- Watermark -->
							<?php
							if ( ! $is_video || is_file( wppa_fix_poster_ext( wppa_get_photo_path( $photo['id'] ), $photo['id'] ) ) ) { ?>
								<tr style="vertical-align:middle;" >
									<th  >
										<label><?php _e( 'Watermark:' , 'wp-photo-album-plus') ?></label>
									</th>
									<td>
										<?php
										$user = wppa_get_user();
										if ( wppa_switch( 'watermark_on' ) ) {
											if ( wppa_switch( 'watermark_user' ) || current_user_can( 'wppa_settings' ) ) {
												echo __( 'File:','wppa' , 'wp-photo-album-plus').' ' ?>
												<select id="wmfsel_<?php echo $photo['id']?>" onchange="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'wppa_watermark_file_<?php echo $user ?>', this );" >
												<?php echo wppa_watermark_file_select() ?>
												</select>
												<?php
												echo '<br />'.__( 'Pos:' , 'wp-photo-album-plus').' ' ?>
												<select id="wmpsel_<?php echo $photo['id']?>" onchange="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'wppa_watermark_pos_<?php echo $user ?>', this );" >
												<?php echo wppa_watermark_pos_select() ?>
												</select>
												<input type="button" class="button-secundary" value="<?php _e( 'Apply watermark' , 'wp-photo-album-plus') ?>" onclick="if ( confirm( '<?php echo esc_js( __( 'Are you sure? Once applied it can not be removed!' , 'wp-photo-album-plus') ).'\n\n'.esc_js( __( 'And I do not know if there is already a watermark on this photo' , 'wp-photo-album-plus') ) ?>' ) ) wppaAjaxApplyWatermark( <?php echo $photo['id'] ?>, document.getElementById( 'wmfsel_<?php echo $photo['id']?>' ).value, document.getElementById( 'wmpsel_<?php echo $photo['id']?>' ).value )" />
												<?php
											}
											else {
												echo __( 'File:','wppa' , 'wp-photo-album-plus').' '.__( $wmfile , 'wp-photo-album-plus');
												if ( $wmfile != '--- none ---' ) echo ' '.__( 'Pos:' , 'wp-photo-album-plus').' '.$wmpos;
											} ?>
											<img id="wppa-water-spin-<?php echo $photo['id'] ?>" src="<?php echo wppa_get_imgdir().'wpspin.gif' ?>" style="visibility:hidden" /><?php
										}
										else {
											_e( 'Not configured' , 'wp-photo-album-plus');
										}
										?>
									</td>
								</tr>
							<?php } ?>
							<!-- Remark -->
							<tr style="vertical-align: middle;" >
								<th >
									<label style="color:#070"><?php _e( 'Remark:' , 'wp-photo-album-plus') ?></label>
								</th>
								<td id="photostatus-<?php echo $photo['id'] ?>" style="padding-left:10px; width: 400px;">
									<?php
									if ( wppa_is_video( $photo['id'] ) ) {
										echo sprintf( __( 'Video %s is not modified yet' , 'wp-photo-album-plus'), $photo['id'] );
									}
									else {
										echo sprintf( __( 'Photo %s is not modified yet' , 'wp-photo-album-plus'), $photo['id'] );
									}
									?>
								</td>
							</tr>

						</tbody>
					</table>
					<script type="text/javascript">wppaPhotoStatusChange( <?php echo $photo['id'] ?> )</script>
				</div>

				<div style="clear:both;"></div>

				<?php if ( ! isset( $_REQUEST['quick'] ) ) { ?>
				<div class="wppa-links" >
					<table style="width:100%" >
						<tbody>
							<?php if ( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) { ?>
							<tr>
								<td><?php _e( 'Single image shortcode' , 'wp-photo-album-plus') ?>:</td>
								<td><?php echo esc_js( '[wppa type="photo" photo="'.$photo['id'].'" size="'.wppa_opt( 'fullsize' ).'"][/wppa]' ) ?></td>
							</tr>
							<?php } ?>
							<?php if ( is_file( wppa_get_source_path( $photo['id'] ) ) ) { ?>
							<tr>
								<td><?php _e( 'Permalink' , 'wp-photo-album-plus') ?>:</td>
								<td><?php echo wppa_get_source_pl( $photo['id'] ) ?></td>
							</tr>
							<?php } ?>
							<tr>
								<td><?php _e( 'Hi resolution url' , 'wp-photo-album-plus') ?>:</td>
								<td><?php echo wppa_get_hires_url( $photo['id'] ) ?></td>
							</tr>
							<?php if ( is_file( wppa_get_photo_path( $photo['id'] ) ) ) { ?>
							<tr>
								<td><?php _e( 'Display file url' , 'wp-photo-album-plus') ?>:</td>
								<td><?php echo wppa_get_lores_url( $photo['id'] ) ?></td>
							</tr>
							<?php } ?>
							<?php if ( is_file( wppa_get_thumb_path( $photo['id'] ) ) ) { ?>
							<tr>
								<td><?php _e( 'Thumbnail file url' , 'wp-photo-album-plus') ?>:</td>
								<td><?php echo wppa_get_tnres_url( $photo['id'] ) ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
				<?php } ?>

</div>
				<!-- Comments -->
				<?php
				$comments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `".WPPA_COMMENTS."` WHERE `photo` = %s ORDER BY `timestamp` DESC", $photo['id'] ), ARRAY_A );
				if ( $comments ) {
				?>
				<div class="widefat" style="width:99%; font-size:11px;" >
					<table class="wppa-table widefat wppa-setting-table" >
						<thead>
							<tr style="font-weight:bold;" >
								<td style="padding:0 4px;" >#</td>
								<td style="padding:0 4px;" >User</td>
								<td style="padding:0 4px;" >Time since</td>
								<td style="padding:0 4px;" >Status</td>
								<td style="padding:0 4px;" >Comment</td>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $comments as $comment ) {
							echo '
							<tr>
								<td style="padding:0 4px;" >'.$comment['id'].'</td>
								<td style="padding:0 4px;" >'.$comment['user'].'</td>
								<td style="padding:0 4px;" >'.wppa_get_time_since( $comment['timestamp'] ).'</td>';
								if ( current_user_can( 'wppa_comments' ) || current_user_can( 'wppa_moderate' ) || ( wppa_get_user() == $photo['owner'] && wppa_switch( 'owner_moderate_comment' ) ) ) {
									$p = ( $comment['status'] == 'pending' ) ? 'selected="selected" ' : '';
									$a = ( $comment['status'] == 'approved' ) ? 'selected="selected" ' : '';
									$s = ( $comment['status'] == 'spam' ) ? 'selected="selected" ' : '';
									$t = ( $comment['status'] == 'trash' ) ? 'selected="selected" ' : '';
									echo '
										<td style="padding:0 4px;" >
											<select style="height: 20px; font-size: 11px; padding:0;" onchange="wppaAjaxUpdateCommentStatus( '.$photo['id'].', '.$comment['id'].', this.value )" >
												<option value="pending" '.$p.'>'.__( 'Pending' , 'wp-photo-album-plus').'</option>
												<option value="approved" '.$a.'>'.__( 'Approved' , 'wp-photo-album-plus').'</option>
												<option value="spam" '.$s.'>'.__( 'Spam' , 'wp-photo-album-plus').'</option>
												<option value="trash" '.$t.'>'.__( 'Trash' , 'wp-photo-album-plus').'</option>
											</select >
										</td>
									';
								}
								else {
									echo '<td style="padding:0 4px;" >';
										if ( $comment['status'] == 'pending' ) _e( 'Pending' , 'wp-photo-album-plus');
										elseif ( $comment['status'] == 'approved' ) _e( 'Approved' , 'wp-photo-album-plus');
										elseif ( $comment['status'] == 'spam' ) _e( 'Spam' , 'wp-photo-album-plus');
										elseif ( $comment['status'] == 'trash' ) _e( 'Trash' , 'wp-photo-album-plus');
									echo '</td>';
								}
								echo '<td style="padding:0 4px;" >'.$comment['comment'].'</td>
							</tr>
							';
							} ?>
						</tbody>
					</table>
				</div>
			<?php } ?>
		<!--	</div> -->
			<div style="clear:both;margin-top:7px;"></div>
<?php
		} /* foreach photo */
		wppa_admin_page_links( $page, $pagesize, $count, $link );
	} /* photos not empty */
} /* function */

function wppa_album_photos_bulk( $album ) {
	global $wpdb;

	// Check input
	wppa_vfy_arg( 'wppa-page' );

	// Init
	$count = '0';
	$abort = false;

	if ( isset ( $_POST['wppa-bulk-action'] ) ) {
		check_admin_referer( 'wppa-bulk', 'wppa-bulk' );
		if ( isset ( $_POST['wppa-bulk-photo'] ) ) {
			$ids 		= $_POST['wppa-bulk-photo'];
			$newalb 	= isset ( $_POST['wppa-bulk-album'] ) ? $_POST['wppa-bulk-album'] : '0';
			$status 	= isset ( $_POST['wppa-bulk-status'] ) ? $_POST['wppa-bulk-status'] : '';
			$owner 		= isset ( $_POST['wppa-bulk-owner'] ) ? $_POST['wppa-bulk-owner'] : '';
			$totcount 	= count( $ids );
			if ( ! is_numeric( $newalb ) ) wp_die( 'Security check failure 1' );
			if ( is_array( $ids ) ) {
				foreach ( array_keys( $ids ) as $id ) {
					$skip = false;
					switch ( $_POST['wppa-bulk-action'] ) {
						case 'wppa-bulk-delete':
							wppa_delete_photo( $id );
							break;
						case 'wppa-bulk-move-to':
							if ( $newalb ) {
								$photo = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM '.WPPA_PHOTOS.' WHERE `id` = %s', $id ), ARRAY_A );
								if ( wppa_switch( 'void_dups' ) ) {	// Check for already exists
									$exists = $wpdb->get_var ( $wpdb->prepare ( "SELECT COUNT(*) FROM `".WPPA_PHOTOS."` WHERE `filename` = %s AND `album` = %s", $photo['filename'], $newalb ) );
									if ( $exists ) {	// Already exists
										wppa_error_message ( sprintf ( __( 'A photo with filename %s already exists in album %s.' , 'wp-photo-album-plus'), $photo['filename'], $newalb ) );
										$skip = true;
									}
								}
								if ( $skip ) continue;
								wppa_flush_treecounts( $photo['album'] );		// Current album
								wppa_flush_treecounts( $newalb );				// New album
								$wpdb->query( $wpdb->prepare( 'UPDATE `'.WPPA_PHOTOS.'` SET `album` = %s WHERE `id` = %s', $newalb, $id ) );
								wppa_move_source( $photo['filename'], $photo['album'], $newalb );
							}
							else wppa_error_message( 'Unexpected error #4 in wppa_album_photos_bulk().' );
							break;
						case 'wppa-bulk-copy-to':
							if ( $newalb ) {
								$photo = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM '.WPPA_PHOTOS.' WHERE `id` = %s', $id ), ARRAY_A );
								if ( wppa_switch( 'void_dups' ) ) {	// Check for already exists
									$exists = $wpdb->get_var ( $wpdb->prepare ( "SELECT COUNT(*) FROM `".WPPA_PHOTOS."` WHERE `filename` = %s AND `album` = %s", $photo['filename'], $newalb ) );
									if ( $exists ) {	// Already exists
										wppa_error_message ( sprintf ( __( $exists.'A photo with filename %s already exists in album %s.' , 'wp-photo-album-plus'), $photo['filename'], $newalb ) );
										$skip = true;
									}
								}
								if ( $skip ) continue;
								wppa_copy_photo( $id, $newalb );
								wppa_flush_treecounts( $newalb );
							}
							else wppa_error_message( 'Unexpected error #3 in wppa_album_photos_bulk().' );
							break;
						case 'wppa-bulk-status':
							if ( ! in_array( $status, array( 'publish', 'pending', 'featured', 'scheduled', 'gold', 'silver', 'bronze', 'private' ) ) ) {
								wppa_log( 'error', 'Unknown status '.strip_tags( $status ).' found in wppa-photo-admin-autosave.php -> wppa_album_photos_bulk()' );
								$status = 'publish';
							}
							if ( current_user_can( 'wppa_admin' ) || current_user_can( 'wppa_moderate' ) ) {
								if ( $status == 'publish' || $status == 'pending' || wppa_user_is( 'administrator' ) || ! wppa_switch( 'ext_status_restricted' ) ) {
									$wpdb->query( "UPDATE `".WPPA_PHOTOS."` SET `status` = '".$status."' WHERE `id` = ".$id );
									wppa_flush_treecounts( $id, wppa_get_photo_item( $id, 'album' ) );
								}
								else wp_die( 'Security check failure 2' );
							}
							else wp_die( 'Security check failure 3' );
							break;
						case 'wppa-bulk-owner':
							if ( wppa_user_is( 'administrator' ) && wppa_switch( 'photo_owner_change' ) ) {
								if ( $owner ) {
									$owner = sanitize_user( $owner );
									$exists = $wpdb->get_var( "SELECT COUNT(*) FROM `".$wpdb->users."` WHERE `user_login` = '".$owner."'" );
									if ( $exists ) {
										$wpdb->query( "UPDATE `".WPPA_PHOTOS."` SET `owner` = '".$owner."' WHERE `id` = ".$id );
									}
									else {
										wppa_error_message( 'A user with login name '.$owner.' does not exist.' );
										$skip = true;
									}
								}
								else wp_die( 'Missing required arg in bulk change owner' );
							}
							else wp_die( 'Security check failure 4' );
							break;
						default:
							wppa_error_message( 'Unimplemented bulk action requested in wppa_album_photos_bulk().' );
							break;
					}
					if ( ! $skip ) $count++;
					if ( wppa_is_time_up() ) {
						wppa_error_message( sprintf( __( 'Time is out after processing %d out of %d items.' , 'wp-photo-album-plus'), $count, $totcount ) );
						$abort = true;
					}
					if ( $abort ) break;
				}
			}
			else wppa_error_message( 'Unexpected error #2 in wppa_album_photos_bulk().' );
		}
		else wppa_error_message( 'Unexpected error #1 in wppa_album_photos_bulk().' );

		if ( $count && ! $abort ) {
			switch ( $_POST['wppa-bulk-action'] ) {
				case 'wppa-bulk-delete':
					$message = sprintf( __( '%d photos deleted.' , 'wp-photo-album-plus'), $count );
					break;
				case 'wppa-bulk-move-to':
					$message = sprintf( __( '%1$s photos moved to album %2$s.' , 'wp-photo-album-plus'), $count, $newalb.': '.wppa_get_album_name( $newalb ) );
					break;
				case 'wppa-bulk-copy-to':
					$message = sprintf( __( '%1$s photos copied to album %2$s.' , 'wp-photo-album-plus'), $count, $newalb.': '.wppa_get_album_name( $newalb ) );
					break;
				case 'wppa-bulk-status':
					$message = sprintf( __( 'Changed status to %1$s on %2$s photos.' , 'wp-photo-album-plus'), $status, $count );
					break;
				case 'wppa-bulk-owner':
					$message = sprintf( __( 'Changed owner to %1$s on %2$s photos.' , 'wp-photo-album-plus'), $owner, $count );
					break;
				default:
					$message = sprintf( __( '%d photos processed.' , 'wp-photo-album-plus'), $count );
					break;
			}
			wppa_ok_message( $message );
		}
	}

	$pagesize 	= wppa_opt( 'photo_admin_pagesize' );
	$page 		= isset ( $_GET['wppa-page'] ) ? $_GET['wppa-page'] : '1';
	$skip 		= ( $page - '1' ) * $pagesize;
	$limit 		= ( $pagesize < '1' ) ? '' : ' LIMIT '.$skip.','.$pagesize;

	if ( $album ) {
		if ( $album == 'search' ) {
			$count 	= wppa_get_edit_search_photos( '', 'count_only' );
			$photos = wppa_get_edit_search_photos( $limit );
			$link 	= wppa_dbg_url( get_admin_url().'admin.php?page=wppa_admin_menu&tab=edit&edit_id='.$album.'&wppa-searchstring='.wppa_sanitize_searchstring($_REQUEST['wppa-searchstring']).'&bulk' );
			wppa_show_search_statistics();
		}
		else {
			$counts = wppa_treecount_a( $album );
			$count = $counts['selfphotos'] + $counts['pendphotos']; //$wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM `'.WPPA_PHOTOS.'` WHERE `album` = %s', $album ) );
			$photos = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `'.WPPA_PHOTOS.'` WHERE `album` = %s '.wppa_get_photo_order( $album, 'norandom' ).$limit, $album ), ARRAY_A );
			$link = wppa_dbg_url( get_admin_url().'admin.php?page=wppa_admin_menu&tab=edit&edit_id='.$album.'&bulk' );
		}

		if ( $photos ) {
			wppa_admin_page_links( $page, $pagesize, $count, $link, '#manage-photos' );
			?>
			<script type="text/javascript" >
				function wppaBulkActionChange( elm, id ) {
					wppa_setCookie( 'wppa_bulk_action',elm.value,365 );
					if ( elm.value == 'wppa-bulk-move-to' || elm.value == 'wppa-bulk-copy-to' ) jQuery( '#wppa-bulk-album' ).css( 'display', 'inline' );
					else jQuery( '#wppa-bulk-album' ).css( 'display', 'none' );
					if ( elm.value == 'wppa-bulk-status' ) jQuery( '#wppa-bulk-status' ).css( 'display', 'inline' );
					else jQuery( '#wppa-bulk-status' ).css( 'display', 'none' );
					if ( elm.value == 'wppa-bulk-owner' ) jQuery( '#wppa-bulk-owner' ).css( 'display', 'inline' );
					else jQuery( '#wppa-bulk-owner' ).css( 'display', 'none' );
				}
				function wppaBulkDoitOnClick() {
					var photos = jQuery( '.wppa-bulk-photo' );
					var count=0;
					for ( i=0; i< photos.length; i++ ) {
						var photo = photos[i];
						if ( photo.checked ) count++;
					}
					if ( count == 0 ) {
						alert( 'No photos selected' );
						return false;
					}
					var action = document.getElementById( 'wppa-bulk-action' ).value;
					switch ( action ) {
						case '':
							alert( 'No action selected' );
							return false;
							break;
						case 'wppa-bulk-delete':
							break;
						case 'wppa-bulk-move-to':
						case 'wppa-bulk-copy-to':
							var album = document.getElementById( 'wppa-bulk-album' ).value;
							if ( album == 0 ) {
								alert( 'No album selected' );
								return false;
							}
							break;
						case 'wppa-bulk-status':
							var status = document.getElementById( 'wppa-bulk-status' ).value;
							if ( status == 0 ) {
								alert( 'No status selected' );
								return false;
							}
							break;
						case 'wppa-bulk-owner':
							var owner = documnet.getElementById( 'wppa-bulk-owner' ).value;
							if ( owner == 0 ) {
								alert( 'No new owner selected' );
								return false;
							}
							break;
						default:
							alert( 'Unimplemented action requested: '+action );
							return false;
							break;

					}
					return true;
				}
				function wppaSetThumbsize( elm ) {
					var thumbsize = elm.value;
					wppa_setCookie( 'wppa_bulk_thumbsize',thumbsize,365 );
					jQuery( '.wppa-bulk-thumb' ).css( 'max-width', thumbsize+'px' );
					jQuery( '.wppa-bulk-thumb' ).css( 'max-height', ( thumbsize/2 )+'px' );
					jQuery( '.wppa-bulk-dec' ).css( 'height', ( thumbsize/2 )+'px' );
				}
				jQuery( document ).ready( function() {
					var action = wppa_getCookie( 'wppa_bulk_action' );
					document.getElementById( 'wppa-bulk-action' ).value = action;
					if ( action == 'wppa-bulk-move-to' || action == 'wppa-bulk-copy-to' ) {
						jQuery( '#wppa-bulk-album' ).css( 'display','inline' );
						document.getElementById( 'wppa-bulk-album' ).value = wppa_getCookie( 'wppa_bulk_album' );
					}
					if ( action == 'wppa-bulk-status' ) {
						jQuery( '#wppa-bulk-status' ).css( 'display','inline' );
						document.getElementById( 'wppa-bulk-status' ).value = wppa_getCookie( 'wppa_bulk_status' );
					}
					if ( action == 'wppa-bulk-owner' ) {
						jQuery( '#wppa-bulk-owner' ).css( 'display','inline' );
						document.getElementById( 'wppa-bulk-owner' ).value = wppa_getCookie( 'wppa_bulk_owner' );
					}
				} );

			</script>
			<form action="<?php echo $link.'&wppa-page='.$page.'#manage-photos' ?>" method="post" >
				<?php wp_nonce_field( 'wppa-bulk','wppa-bulk' ) ?>
				<h3>
				<span style="font-weight:bold;" ><?php _e( 'Bulk action:' , 'wp-photo-album-plus') ?></span>
				<select id="wppa-bulk-action" name="wppa-bulk-action" onchange="wppaBulkActionChange( this, 'bulk-album' )" >
					<option value="" ></option>
					<option value="wppa-bulk-delete" ><?php _e( 'Delete' , 'wp-photo-album-plus') ?></option>
					<option value="wppa-bulk-move-to" ><?php _e( 'Move to' , 'wp-photo-album-plus') ?></option>
					<option value="wppa-bulk-copy-to" ><?php _e( 'Copy to' , 'wp-photo-album-plus') ?></option>
					<?php if ( current_user_can( 'wppa_admin' ) || current_user_can( 'wppa_moderate' ) ) { ?>
						<option value="wppa-bulk-status" ><?php _e( 'Set status to' , 'wp-photo-album-plus') ?></option>
					<?php } ?>
					<?php if ( wppa_user_is( 'administrator' ) && wppa_switch( 'photo_owner_change' ) ) { ?>
						<option value="wppa-bulk-owner" ><?php _e( 'Set owner to' , 'wp-photo-album-plus') ?></option>
					<?php } ?>
				</select>
				<select name="wppa-bulk-album" id="wppa-bulk-album" style="display:none;" onchange="wppa_setCookie( 'wppa_bulk_album',this.value,365 );" >
					<?php echo wppa_album_select_a( array( 'checkaccess' => true, 'path' => wppa_switch( 'hier_albsel' ), 'exclude' => $album, 'selected' => '0', 'addpleaseselect' => true ) ) ?>
				</select>
				<select name="wppa-bulk-status" id="wppa-bulk-status" style="display:none;" onchange="wppa_setCookie( 'wppa_bulk_status',this.value,365 );" >
					<option value="" ><?php _e( '- select a status -' , 'wp-photo-album-plus') ?></option>
					<option value="pending" ><?php _e( 'Pending' , 'wp-photo-album-plus') ?></option>
					<option value="publish" ><?php _e( 'Publish' , 'wp-photo-album-plus') ?></option>
					<?php if ( wppa_switch( 'ext_status_restricted' ) && ! wppa_user_is( 'administrator' ) ) $dis = ' disabled'; else $dis = ''; ?>
					<option value="featured"<?php echo $dis?> ><?php _e( 'Featured' , 'wp-photo-album-plus') ?></option>
					<option value="gold" <?php echo $dis?> ><?php _e( 'Gold' , 'wp-photo-album-plus') ?></option>
					<option value="silver" <?php echo $dis?> ><?php _e( 'Silver' , 'wp-photo-album-plus') ?></option>
					<option value="bronze" <?php echo $dis?> ><?php _e( 'Bronze' , 'wp-photo-album-plus') ?></option>
					<option value="scheduled" <?php echo $dis?> ><?php _e( 'Scheduled' , 'wp-photo-album-plus') ?></option>
					<option value="private" <?php echo $dis ?> ><?php _e(  'Private' , 'wp-photo-album-plus') ?></option>
				</select>
				<!-- Owner -->
				<?php 	$users = wppa_get_users();
						if ( count( $users ) ) { ?>
				<select name="wppa-bulk-owner" id="wppa-bulk-owner" style="display:none;" onchange="wppa_setCookie( 'wppa_bulk_owner',this.value,365 );">
					<option value="" ><?php _e( '- select an owner -' , 'wp-photo-album-plus') ?></option>
					<?php

						foreach ( $users as $user ) {
							echo '<option value="'.$user['user_login'].'" >'.$user['display_name'].' ('.$user['user_login'].')</option>';
						}
					?>
				</select>
				<?php } else { ?>
				<input name="wppa-bulk-owner" id="wppa-bulk-owner" style="display:none;" onchange="wppa_setCookie( 'wppa_bulk_owner',this.value,365 );" />
				<?php } ?>
				<!-- Submit -->
				<input type="submit" onclick="return wppaBulkDoitOnClick()" class="button-primary" value="<?php _e( 'Doit!' , 'wp-photo-album-plus') ?>" />
				<span style="font-family:sans-serif; font-size:12px; font-style:italic; font-weight:normal;" >
					<?php _e( 'Pressing this button will reload the page after executing the selected action' , 'wp-photo-album-plus') ?>
				</span>
				</h3>
				<table class="widefat" >
					<thead style="font-weight:bold;" >
						<td><input type="checkbox" class="wppa-bulk-photo" onchange="jQuery( '.wppa-bulk-photo' ).attr( 'checked', this.checked );" /></td>
						<td><?php _e( 'ID' , 'wp-photo-album-plus') ?></td>
						<td><?php _e( 'Preview' , 'wp-photo-album-plus') ?></td>
						<td><?php _e( 'Name' , 'wp-photo-album-plus') ?></td>
						<td><?php _e( 'Description' , 'wp-photo-album-plus') ?></td>
						<td><?php _e( 'Status' , 'wp-photo-album-plus') ?></td>
						<td><?php _e( 'Owner' , 'wp-photo-album-plus') ?></td>
						<td><?php _e( 'Remark' , 'wp-photo-album-plus') ?></td>
					</thead>
					<tbody>
						<?php foreach ( $photos as $photo ) { ?>
						<?php $id = $photo['id']; ?>
						<tr id="photoitem-<?php echo $photo['id'] ?>" >
							<!-- Checkbox -->
							<td>
								<input type="hidden" id="photo-nonce-<?php echo $photo['id'] ?>" value="<?php echo wp_create_nonce( 'wppa_nonce_'.$photo['id'] );  ?>" />
								<input type="checkbox" name="wppa-bulk-photo[<?php echo $photo['id'] ?>]" class="wppa-bulk-photo" />
							</td>
							<!-- ID and delete link -->
							<td><?php echo $photo['id'] ?>
								<br /><a onclick="if ( confirm( '<?php _e( 'Are you sure you want to delete this photo?', 'wp-photo-album-plus' ) ?>' ) ) wppaAjaxDeletePhoto( <?php echo $photo['id'] ?>, '<td colspan=3 >', '</td>' )" style="color:red;font-weight:bold;"><?php _e( 'Delete' , 'wp-photo-album-plus') ?></a>
							</td>
							<!-- Preview -->
							<td style="min-width:240px; text-align:center;" >
							<?php if ( wppa_is_video( $photo['id'] ) ) { ?>
								<a href="<?php echo str_replace( 'xxx', 'mp4', wppa_get_photo_url( $photo['id'] ) ) ?>" target="_blank" title="Click to see fullsize" >
									<?php // Animating size changes of a video tag is not a good idea. It will rapidly screw up browser cache and cpu ?>
									<?php echo wppa_get_video_html( array(
													'id'			=> $id,
												//	'width'			=> $imgwidth,
													'height' 		=> '60',
													'controls' 		=> false,
												//	'margin_top' 	=> '0',
												//	'margin_bottom' => '0',
													'tagid' 		=> 'pa-id-'.$id,
												//	'cursor' 		=> 'cursor:pointer;',
													'events' 		=> ' onmouseover="jQuery( this ).css( \'height\', \'160\' )" onmouseout="jQuery( this ).css( \'height\', \'60\' )"',
												//	'title' 		=> $title,
													'preload' 		=> 'metadata',
												//	'onclick' 		=> $onclick,
												//	'lb' 			=> false,
												//	'class' 		=> '',
												//	'style' 		=> $imgstyle,
													'use_thumb' 	=> true
													));


									?>
					<!--				<video preload="metadata" style="height:60px;" onmouseover="jQuery( this ).css( 'height', '160' )" onmouseout="jQuery( this ).css( 'height', '60' )" >
										<?php // echo wppa_get_video_body( $photo['id'] ) ?>
									</video>	-->
								</a>
							<?php }
							else { ?>
								<a href="<?php echo wppa_fix_poster_ext( wppa_get_photo_url( $photo['id'] ), $photo['id'] ) ?>" target="_blank" title="Click to see fullsize" >
									<img class="wppa-bulk-thumb" src="<?php echo wppa_fix_poster_ext( wppa_get_thumb_url( $photo['id'] ), $photo['id'] ) ?>" style="height:60px;" onmouseover="jQuery( this ).stop().animate( {height:120}, 100 )" onmouseout="jQuery( this ).stop().animate( {height:60}, 100 )" />
								</a>
							<?php } ?>
							</td>
							<td style="width:25%;" >
								<input type="text" style="width:100%;" id="pname-<?php echo $photo['id'] ?>" onchange="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'name', this );" value="<?php echo esc_attr( stripslashes( $photo['name'] ) ) ?>" />
								<?php
								if ( wppa_is_video( $photo['id'] ) ) {
									echo '<br />'.wppa_get_videox( $photo['id'] ).' x '.wppa_get_videoy( $photo['id'] ).' px.';
								}
								else {
									$sp = wppa_get_source_path( $photo['id'] );
									if ( is_file( $sp ) ) {
										$ima = getimagesize( $sp );
										if ( is_array( $ima ) ) {
											echo '<br />'.$ima['0'].' x '.$ima['1'].' px.';
										}
									}
								}
								?>
							</td>
							<!-- Description -->
							<td style="width:25%;" >
								<textarea class="wppa-bulk-dec" style="height:50px; width:100%" onchange="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'description', this )" ><?php echo( stripslashes( $photo['description'] ) ) ?></textarea>
							</td>
							<!-- Status -->
							<td>
							<?php if ( current_user_can( 'wppa_admin' ) || current_user_can( 'wppa_moderate' ) )  { ?>
								<select id="status-<?php echo $photo['id'] ?>" onchange="wppaAjaxUpdatePhoto( <?php echo $photo['id'] ?>, 'status', this ); wppaPhotoStatusChange( <?php echo $photo['id'] ?> ); ">
									<option value="pending" <?php if ( $photo['status']=='pending' ) echo 'selected="selected"'?> ><?php _e( 'Pending' , 'wp-photo-album-plus') ?></option>
									<option value="publish" <?php if ( $photo['status']=='publish' ) echo 'selected="selected"'?> ><?php _e( 'Publish' , 'wp-photo-album-plus') ?></option>
									<?php if ( wppa_switch( 'ext_status_restricted' ) && ! wppa_user_is( 'administrator' ) ) $dis = ' disabled'; else $dis = ''; ?>
									<option value="featured" <?php if ( $photo['status']=='featured' ) echo 'selected="selected"'; echo $dis?> ><?php _e( 'Featured' , 'wp-photo-album-plus') ?></option>
									<option value="gold" <?php if ( $photo['status'] == 'gold' ) echo 'selected="selected"'; echo $dis?> ><?php _e( 'Gold' , 'wp-photo-album-plus') ?></option>
									<option value="silver" <?php if ( $photo['status'] == 'silver' ) echo 'selected="selected"'; echo $dis?> ><?php _e( 'Silver' , 'wp-photo-album-plus') ?></option>
									<option value="bronze" <?php if ( $photo['status'] == 'bronze' ) echo 'selected="selected"'; echo $dis?> ><?php _e( 'Bronze' , 'wp-photo-album-plus') ?></option>
									<option value="scheduled" <?php if ( $photo['status'] == 'scheduled' ) echo 'selected="selected"'; echo $dis?> ><?php _e( 'Scheduled' , 'wp-photo-album-plus') ?></option>
									<option value="private" <?php if ( $photo['status'] == 'private' ) echo 'selected="selected"'; echo $dis ?> ><?php _e( 'Private' , 'wp-photo-album-plus') ?></option>
								</select>
							<?php }
								else {
									if ( $photo['status'] == 'pending' ) _e( 'Pending' , 'wp-photo-album-plus');
									elseif ( $photo['status'] == 'publish' ) _e( 'Publish' , 'wp-photo-album-plus');
									elseif ( $photo['status'] == 'featured' ) e( 'Featured' );
									elseif ( $photo['status'] == 'gold' ) _e( 'Gold' , 'wp-photo-album-plus');
									elseif ( $photo['status'] == 'silver' ) _e( 'Silver' , 'wp-photo-album-plus');
									elseif ( $photo['status'] == 'bronze' ) _e( 'Bronze' , 'wp-photo-album-plus');
									elseif ( $photo['status'] == 'scheduled' ) _e( 'Scheduled' , 'wp-photo-album-plus');
									elseif ( $photo['status'] == 'private' ) _e( 'Private' , 'wp-photo-album-plus');
								} ?>
							</td>
							<!-- Owner -->
							<td>
								<?php echo $photo['owner'] ?>
							</td>
							<!-- Remark -->
							<td id="photostatus-<?php echo $photo['id'] ?>" style="width:25%;" >
								<?php _e( 'Not modified' , 'wp-photo-album-plus') ?>
								<script type="text/javascript">wppaPhotoStatusChange( <?php echo $photo['id'] ?> )</script>
							</td>
						</tr>
						<?php } ?>
					</tbody>
					<tfoot style="font-weight:bold;" >
						<td><input type="checkbox" class="wppa-bulk-photo" onchange="jQuery( '.wppa-bulk-photo' ).attr( 'checked', this.checked );" /></td>
						<td><?php _e( 'ID' , 'wp-photo-album-plus') ?></td>
						<td><?php _e( 'Preview' , 'wp-photo-album-plus') ?></td>
						<td><?php _e( 'Name' , 'wp-photo-album-plus') ?></td>
						<td><?php _e( 'Description' , 'wp-photo-album-plus') ?></td>
						<td><?php _e( 'Status' , 'wp-photo-album-plus') ?></td>
						<td><?php _e( 'Owner' , 'wp-photo-album-plus') ?></td>
						<td><?php _e( 'Remark' , 'wp-photo-album-plus') ?></td>
					</tfoot>
				</table>
			</form>
			<?php
			wppa_admin_page_links( $page, $pagesize, $count, $link );
		}
		else {
			if ( $page == '1' ) {
				if ( isset( $_REQUEST['wppa-searchstring'] ) ) {
					echo '<h3>'.__( 'No photos matching your search criteria.' , 'wp-photo-album-plus').'</h3>';
				}
				else {
					echo '<h3>'.__( 'No photos yet in this album.' , 'wp-photo-album-plus').'</h3>';
				}
			}
			else {
				$page_1 = $page - '1';
				echo sprintf( __( 'Page %d is empty, try <a href="%s" >page %d</a>.' , 'wp-photo-album-plus'), $page, $link.'&wppa-page='.$page_1.'#manage-photos', $page_1 );
			}
		}
	}
	else {
		wppa_dbg_msg( 'Missing required argument in wppa_album_photos() 2', 'red', 'force' );
	}
}

function wppa_album_photos_sequence( $album ) {
global $wpdb;

	if ( $album ) {
		$photoorder 	= wppa_get_photo_order( $album, 'norandom' );
		$is_descending 	= strpos( $photoorder, 'DESC' ) !== false;
		$is_p_order 	= strpos( $photoorder, 'p_order' ) !== false;
		$photos 		= $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `'.WPPA_PHOTOS.'` WHERE `album` = %s '.$photoorder, $album ), ARRAY_A );
		$link 			= wppa_dbg_url( get_admin_url().'admin.php?page=wppa_admin_menu&tab=edit&edit_id='.$album.'&bulk' );
		$size 			= '180';

		if ( $photos ) {
			?>
			<style>
				.sortable-placeholder {
					width: <?php echo $size ?>px;
					height: <?php echo $size ?>px;
					margin: 5px;
					border: 1px solid #cccccc;
					border-radius:3px;
					float: left;
				}
				.ui-state-default {
					position: relative;
					width: <?php echo $size ?>px;
					height: <?php echo $size ?>px;
					margin: 5px;
					border-radius:3px;
					float: left;
				}
				.wppa-publish {
					border: 1px solid;
					background-color: rgb( 255, 255, 224 );
					border-color: rgb( 230, 219, 85 );
				}
				.wppa-featured {
					border: 1px solid;
					background-color: rgb( 224, 255, 224 );
					border-color: rgb( 85, 238, 85 );
				}
				.wppa-pending {
					border: 1px solid;
					background-color: rgb( 255, 235, 232 );
					border-color: rgb( 204, 0, 0 );
				}
			</style>
			<script>
				jQuery( function() {
					jQuery( "#sortable" ).sortable( {
						cursor: "move",
						placeholder: "sortable-placeholder",
						stop: function( event, ui ) {
							var ids = jQuery( ".wppa-sort-item" );
							var seq = jQuery( ".wppa-sort-seqn" );
							var idx = 0;
							var descend = <?php if ( $is_descending ) echo 'true'; else echo 'false' ?>;
							while ( idx < ids.length ) {
								var newvalue;
								if ( descend ) newvalue = ids.length - idx;
								else newvalue = idx + 1;
								var oldvalue = seq[idx].value;
								var photo = ids[idx].value;
								if ( newvalue != oldvalue ) {
									wppaDoSeqUpdate( photo, newvalue );
								}
								idx++;
							}
						}
					} );
				} );
				function wppaDoSeqUpdate( photo, seqno ) {
					var data = 'action=wppa&wppa-action=update-photo&photo-id='+photo+'&item=p_order&wppa-nonce='+document.getElementById( 'photo-nonce-'+photo ).value+'&value='+seqno;
					var xmlhttp = new XMLHttpRequest();

					xmlhttp.onreadystatechange = function() {
						if ( xmlhttp.readyState == 4 && xmlhttp.status != 404 ) {
							var ArrValues = xmlhttp.responseText.split( "||" );
							if ( ArrValues[0] != '' ) {
								alert( 'The server returned unexpected output:\n'+ArrValues[0] );
							}
							switch ( ArrValues[1] ) {
								case '0':	// No error
									jQuery( '#wppa-seqno-'+photo ).html( seqno );
									break;
								case '99':	// Photo is gone
									jQuery( '#wppa-seqno-'+photo ).html( '<span style="color"red" >deleted</span>' );
									break;
								default:	// Any error
									jQuery( '#wppa-seqno-'+photo ).html( '<span style="color"red" >Err:'+ArrValues[1]+'</span>' );
									break;
							}
						}
					}
					xmlhttp.open( 'POST',wppaAjaxUrl,true );
					xmlhttp.setRequestHeader( "Content-type","application/x-www-form-urlencoded" );
					xmlhttp.send( data );
					jQuery( "#wppa-sort-seqn-"+photo ).attr( 'value', seqno );	// set hidden value to new value to prevent duplicate action
					var spinnerhtml = '<img src="'+wppaImageDirectory+'wpspin.gif'+'" />';
					jQuery( '#wppa-seqno-'+photo ).html( spinnerhtml );
				}
			</script>
			<?php if ( ! $is_p_order ) wppa_warning_message( __( 'Setting photo sequence order has only effect if the photo order method is set to <b>Order#</b>' , 'wp-photo-album-plus') ) ?>
			<div class="widefat" style="border-color:#cccccc" >
				<div id="sortable">
					<?php foreach ( $photos as $photo ) {
						if ( wppa_is_video( $photo['id'] ) ) {
							$imgs['0'] = wppa_get_videox( $photo['id'] );
							$imgs['1'] = wppa_get_videoy( $photo['id'] );
						}
						else {
//							$imgs = getimagesize( wppa_get_thumb_path( $photo['id'] ) );
							$imgs['0'] = wppa_get_thumbx( $photo['id'] );
							$imgs['1'] = wppa_get_thumby( $photo['id'] );
						}
						if ( ! $imgs['0'] ) {	// missing thuimbnail, prevent division by zero
							$imgs['0'] = 200;
							$imgs['1'] = 150;
						}
						$mw = $size - '20';
						$mh = $mw * '3' / '4';
						if ( $imgs[1]/$imgs[0] > $mh/$mw ) {	// more portrait than 200x150, y is limit
							$mt = '15';
						}
						else {	// x is limit
							$mt = ( $mh - ( $imgs[1]/$imgs[0] * $mw ) ) / '2' + '15';
						}
					?>
					<div id="photoitem-<?php echo $photo['id'] ?>" class="ui-state-default wppa-<?php echo $photo['status'] ?>" style="background-image:none; text-align:center; cursor:move;" >
					<?php if ( wppa_is_video( $photo['id'] ) ) { ?>
					<?php $id = $photo['id'] ?>
					<?php $imgstyle = 'max-width:'.$mw.'px; max-height:'.$mh.'px; margin-top:'.$mt.'px;' ?>
					<?php echo wppa_get_video_html( array(
													'id'			=> $id,
												//	'width'			=> $imgwidth,
												//	'height' 		=> '60',
													'controls' 		=> false,
												//	'margin_top' 	=> '0',
												//	'margin_bottom' => '0',
													'tagid' 		=> 'pa-id-'.$id,
												//	'cursor' 		=> 'cursor:pointer;',
												//	'events' 		=> ' onmouseover="jQuery( this ).css( \'height\', \'160\' )" onmouseout="jQuery( this ).css( \'height\', \'60\' )"',
												//	'title' 		=> $title,
													'preload' 		=> 'metadata',
												//	'onclick' 		=> $onclick,
												//	'lb' 			=> false,
													'class' 		=> 'wppa-bulk-thumb',
													'style' 		=> $imgstyle,
													'use_thumb' 	=> true
													));
						?>
	<!--					<video preload="metadata" class="wppa-bulk-thumb" style="max-width:<?php echo $mw ?>px; max-height:<?php echo $mh ?>px; margin-top: <?php echo $mt ?>px;" >
						 // echo //wppa_get_video_body( $photo['id'] ) ?>
						</video>
	-->
					<?php }
					else { ?>
						<img class="wppa-bulk-thumb" src="<?php echo wppa_fix_poster_ext( wppa_get_thumb_url( $photo['id'] ), $photo['id'] ) ?>" style="max-width:<?php echo $mw ?>px; max-height:<?php echo $mh ?>px; margin-top: <?php echo $mt ?>px;" />
					<?php } ?>
						<div style="font-size:9px; position:absolute; bottom:24px; text-align:center; width:<?php echo $size ?>px;" ><?php echo wppa_get_photo_name( $photo['id'] ) ?></div>
						<div style="text-align: center; width: <?php echo $size ?>px; position:absolute; bottom:8px;" >
							<span style="margin-left:15px;float:left"><?php echo __( 'Id: ' , 'wp-photo-album-plus').$photo['id']?></span>
							<?php if ( wppa_is_video( $photo['id'] ) )_e('Video', 'wp-photo-album-plus'); ?>
							<?php if ( wppa_has_audio( $photo['id'] ) ) _e('Audio', 'wp-photo-album-plus'); ?>
							<span style="float:right; margin-right:15px;"><?php echo __( 'Ord: ' , 'wp-photo-album-plus').'<span id="wppa-seqno-'.$photo['id'].'" >'.$photo['p_order'] ?></span>
						</div>
						<input type="hidden" id="photo-nonce-<?php echo $photo['id'] ?>" value="<?php echo wp_create_nonce( 'wppa_nonce_'.$photo['id'] );  ?>" />
						<input type="hidden" class="wppa-sort-item" value="<?php echo $photo['id'] ?>" />
						<input type="hidden" class="wppa-sort-seqn" id="wppa-sort-seqn-<?php echo $photo['id'] ?>" value="<?php echo $photo['p_order'] ?>" />
					</div>
					<?php } ?>
				</div>
				<div style="clear:both;"></div>
			</div>
			<?php
		}
		else {
			echo '<h3>'.__( 'The album is empty.' , 'wp-photo-album-plus').'</h3>';
		}
	}
	else {
		wppa_dbg_msg( 'Missing required argument in wppa_album_photos() 3', 'red', 'force' );
	}
}

function wppa_get_edit_search_photos( $limit = '', $count_only = false ) {
global $wpdb;
global $wppa_search_stats;

	$doit = false;
//	if ( wppa_user_is( 'administrator' ) ) $doit = true;
	if ( current_user_can( 'wppa_admin' ) && current_user_can( 'wppa_moderate' ) ) $doit = true;
	if ( wppa_switch( 'upload_edit' ) ) $doit = true;
	if ( ! $doit ) {	// Should never get here. Only when url is manipulted manually.
		die('Security check failure #309');
	}

	$words = explode( ',', wppa_sanitize_searchstring( $_REQUEST['wppa-searchstring'] ) );

	$wppa_search_stats = array();

	$first = true;
	foreach( $words as $word ) {

		// Find lines in index db table
		if ( wppa_switch( 'wild_front' ) ) {
			$pidxs = $wpdb->get_results( "SELECT `slug`, `photos` FROM `".WPPA_INDEX."` WHERE `slug` LIKE '%".$word."%'", ARRAY_A );
		}
		else {
			$pidxs = $wpdb->get_results( "SELECT `slug`, `photos` FROM `".WPPA_INDEX."` WHERE `slug` LIKE '".$word."%'", ARRAY_A );
		}

		$photos = '';

		foreach ( $pidxs as $pi ) {
			$photos .= $pi['photos'].',';
		}

		if ( $first ) {
			$photo_array 	= wppa_index_array_remove_dups( wppa_index_string_to_array( trim( $photos, ',' ) ) );
			$count 			= empty( $photo_array ) ? '0' : count( $photo_array );
			$list 			= implode( ',', $photo_array );
			if ( ! $list ) {
				$list = '0';
			}

//			if ( wppa_user_is( 'administrator' ) ) {
			if ( current_user_can( 'wppa_admin' ) && current_user_can( 'wppa_moderate' ) ) {
				$real_count = $wpdb->get_var( "SELECT COUNT(*) FROM `".WPPA_PHOTOS."` WHERE `id` IN (".$list.") " );
				if ( $count != $real_count ) {
					update_option( 'wppa_remake_index_photos_status', __('Required', 'wp-photo-album-plus') );
// 					echo 'realcount mismatch:1';
//					echo ' count='.$count.', realcount='.$real_count.'<br/>';
				}
			}
			else { // Not admin, can edit own photos only
				$real_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_PHOTOS."` WHERE `id` IN (".$list.") AND `owner` = %s", wppa_get_user() ) );
			}

			$wppa_search_stats[] 	= array( 'word' => $word, 'count' => $real_count );
			$first = false;
		}
		else {
			$temp_array 	= wppa_index_array_remove_dups( wppa_index_string_to_array( trim( $photos, ',' ) ) );
			$count 			= empty( $temp_array ) ? '0' : count( $temp_array );
			$list 			= implode( ',', $temp_array );

//			if ( wppa_user_is( 'administrator' ) ) {
			if ( current_user_can( 'wppa_admin' ) && current_user_can( 'wppa_moderate' ) ) {
				$real_count = $wpdb->get_var( "SELECT COUNT(*) FROM `".WPPA_PHOTOS."` WHERE `id` IN (".$list.") " );
				if ( $count != $real_count ) {
					update_option( 'wppa_remake_index_photos_status', __('Required', 'wp-photo-album-plus') );
//					echo 'realcount mismatch:2';
//					echo ' count='.$count.', realcount='.$real_count.'<br/>';
				}
			}
			else { // Not admin, can edit own photos only
				$real_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_PHOTOS."` WHERE `id` IN (".$list.") AND `owner` = %s", wppa_get_user() ) );
			}

			$wppa_search_stats[] 	= array( 'word' => $word, 'count' => $real_count );
			$photo_array 			= array_intersect( $photo_array, $temp_array );
		}
	}

	if ( ! empty( $photo_array ) ) {

		$list = implode( ',', $photo_array );

//		if ( wppa_user_is( 'administrator' ) ) {
		if ( current_user_can( 'wppa_admin' ) && current_user_can( 'wppa_moderate' ) ) {
			$totcount = $wpdb->get_var( "SELECT COUNT(*) FROM `".WPPA_PHOTOS."` WHERE `id` IN (".$list.") " );
		}
		else { // Not admin, can edit own photos only
			$totcount = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".WPPA_PHOTOS."` WHERE `id` IN (".$list.") AND `owner` = %s" , wppa_get_user() ) );
		}

		$wppa_search_stats[] = array( 'word' => __( 'Combined', 'wp-photo-album-plus'), 'count' => $totcount );

//		if ( wppa_user_is( 'administrator' ) ) {
		if ( current_user_can( 'wppa_admin' ) && current_user_can( 'wppa_moderate' ) ) {
			$photos = $wpdb->get_results( "SELECT * FROM `".WPPA_PHOTOS."` WHERE `id` IN (".$list.") " . wppa_get_photo_order( '0', 'norandom' ).$limit, ARRAY_A );
		}
		else { // Not admin, can edit own photos only
			$photos = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `".WPPA_PHOTOS."` WHERE `id` IN (".$list.") AND `owner` = %s" . wppa_get_photo_order( '0', 'norandom' ).$limit, wppa_get_user() ), ARRAY_A );
		}
	}
	else {
		$photos = false;
	}


	if ( $count_only ) {
		if ( is_array( $photos ) ) {
			return count( $photos );
		}
		else {
			return '0';
		}
	}
	else {
		return $photos;
	}
}

function wppa_show_search_statistics() {
global $wppa_search_stats;

	if ( isset( $_REQUEST['wppa-searchstring'] ) ) {
		echo '
		<table>
			<thead>
				<tr>
					<td><b>' .
						__('Word', 'wp-photo-album-plus') . '
					</b></td>
					<td><b>' .
						__('Count', 'wp-photo-album-plus') . '
					</b></td>
				</tr>
				<tr>
					<td><hr /></td>
					<td><hr /></td>
				</tr>
			</thead>
			<tbody>';
			$count = empty( $wppa_search_stats ) ? '0' : count( $wppa_search_stats );
			$c = '0';
			$s = '';
			foreach( $wppa_search_stats as $search_item ) {
				$c++;
				if ( $c == $count ) {
					echo '<tr><td><hr /></td><td><hr /></td></tr>';
					$s = 'style="font-weight:bold;"';
				}
				echo '
				<tr>
					<td '.$s.'>' .
						$search_item['word'] . '
					</td>
					<td '.$s.'>' .
						$search_item['count'] . '
					</td>
				</tr>';
			}
		echo '
		</table>';
	}
}