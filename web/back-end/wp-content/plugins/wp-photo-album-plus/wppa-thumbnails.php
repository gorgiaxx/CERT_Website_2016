<?php
/* wppa-thumbnails.php
* Package: wp-photo-album-plus
*
* Various funcions to display a thumbnail image
* Contains all possible frontend thumbnail types
*
* Version 6.4.09
*
*/

// Display the standard thumbnail image
function wppa_thumb_default( $id ) {

	wppa_out( wppa_get_thumb_default( $id ) );
}

// Get the standard thumbnail image html
function wppa_get_thumb_default( $id ) {
global $wpdb;

	// Validate args
	if ( ! wppa_is_int( $id ) || $id < '0' ) {
		wppa_dbg_msg( 'Please check file wppa-theme.php or any other php file that calls wppa_get_thumb_default(). Argument 1: photo id is missing or illegal!', 'red', 'force' );
		die( 'Please check your configuration' );
	}

	// Initialize
	$result = '';

	// Encrypted photo id
	$xid = wppa_encrypt_photo( $id );

	// Get the photo info
	$thumb = wppa_cache_thumb( $id );

	// Get the album info
	$album = wppa_cache_album( $thumb['album'] );
	wppa( 'current_album', $album['id'] );

	// Get photo info
	$is_video 		= wppa_is_video( $id );
	$has_audio 		= wppa_has_audio( $id );
	$com_alt 		= wppa( 'is_comten' ) && wppa_switch( 'comten_alt_display' ) && ! wppa_in_widget();
	$frameattr_a 	= wppa_get_thumb_frame_style_a();
	$framestyle 	= $frameattr_a['style'];
	$framewidth 	= $frameattr_a['width'];
	$frameheight 	= $frameattr_a['height'];

	// Get class depending of comment alt display
	if ( $com_alt ) {
		$class = 'thumbnail-frame-comalt thumbnail-frame-comalt-'.wppa( 'mocc' ).' thumbnail-frame-photo-'.$xid;
	}
	else {
		$class = 'thumbnail-frame thumbnail-frame-'.wppa( 'mocc' ).' thumbnail-frame-photo-'.$xid;
	}

	// If no image to display, die gracefully
	$imgsrc = wppa_fix_poster_ext( wppa_get_thumb_path( $id ), $id );
	if ( ! wppa_is_video( $id ) && ! is_file( $imgsrc ) && ! wppa_has_audio( $id ) ) {
		$result .= '<div' .
						' class="' . $class . '"' .
						' style="' . $framestyle . '; color:red;" >' .
							'Missing thumbnail image #' . $id .
					'</div>';
		return $result;
	}

	// Find image attributes
	$alt 				= $album['alt_thumbsize'] == 'yes' ? '_alt' : '';
	$imgattr_a 			= wppa_get_imgstyle_a( $id, $imgsrc, wppa_opt( 'thumbsize'.$alt ), 'optional', 'thumb' );
	$imgstyle  			= $imgattr_a['style'];
	$imgwidth  			= $imgattr_a['width'];
	$imgheight 			= $imgattr_a['height'];
	$imgmargintop 		= $imgattr_a['margin-top'];
	$imgmarginbottom  	= $imgattr_a['margin-bottom'];

	// Special case for comment alt display
	if ( $com_alt ) {
		$imgwidth 	= wppa_opt( 'comten_alt_thumbsize' );
		$imgheight 	= round( $imgwidth * $imgattr_a['height'] / $imgattr_a['width'] );
		$imgstyle   .= 'float:left; margin:0 20px 8px 0;width:'.$imgwidth.'px; height:'.$imgheight.'px;';
	}

	// Cursor depends on link
	$cursor	   		= $imgattr_a['cursor'];

	// Find the required image sizes
	if ( wppa_switch( 'use_thumb_popup' ) ) {

		// Landscape?
		if ( $imgwidth > $imgheight ) {
			$popwidth 	= wppa_opt( 'popupsize' );
			$popheight 	= round( $popwidth * $imgheight / $imgwidth );
		}
		// Portrait
		else {
			$popheight 	= wppa_opt( 'popupsize' );
			$popwidth 	= round( $popheight * $imgwidth / $imgheight );
		}
	}
	else {
		$popwidth 	= $imgwidth;
		$popheight 	= $imgheight;
	}

	// More image attributes
	$imgurl    	= wppa_fix_poster_ext( wppa_get_thumb_url( $id, '', $popwidth, $popheight ), $id );
	$events    	= wppa_get_imgevents( 'thumb', $id );
	$imgalt		= wppa_get_imgalt( $id );	// returns something like ' alt="Any text" '
	$title 		= esc_attr( wppa_get_photo_name( $id ) );

	// Feed ?
	if ( is_feed() ) {
		$imgattr_a 	= wppa_get_imgstyle_a( $id, $imgsrc, '100', '4', 'thumb' );
		$style 		= $imgattr_a['style'];
		$result 	.= 	'<a href="'.get_permalink().'">' .
							'<img src="'.$imgurl.'" '.$imgalt.' title="'.$title.'" style="'.$style.'" />' .
						'</a>';
		return $result;
	}

	// Open Com alt wrapper
	if ( $com_alt ) $result .= '<div>';

	// Open the thumbframe
	$result .= 	'<div' .
					' id="thumbnail_frame_'.$xid.'_'.wppa( 'mocc' ).'"' .
					' class="'.$class.'"' .
					' style="'.$framestyle.'"' .
				' >';

	// Open the image container
	$imgcontheight = $com_alt ? $imgheight : max( $imgwidth,$imgheight );
	if ( ! is_file( $imgsrc ) ) {
		$imgcontheight = 2 * wppa_get_audio_control_height();
	}
	if ( $com_alt ) $framewidth = $imgwidth + '4';
	$result .= '<div' .
					' class="wppa-tn-img-container"' .
					' style="' .
						'height:'.$imgcontheight.'px;' .
						'width:'.$framewidth.'px;' .
						( $com_alt ? 'float:left;' : '' ) .
						'overflow:hidden;"' .
				'>';

	// The medals if at the top
	$medalsize = $com_alt ? 'S' : 'M';
	$result .= wppa_get_medal_html_a( array( 'id' => $id, 'size' => $medalsize, 'where' => 'top' ) );

	// The audio when no popup
	if ( wppa_switch( 'thumb_audio' ) && wppa_has_audio( $id ) && ! $com_alt ) {
		$result 	.= '<div style="position:relative;z-index:11;">';
		$is_safari 	= strpos( $_SERVER["HTTP_USER_AGENT"], 'Safari' );
		$cont_h 	= $is_safari ? 16 : 28;
		$audiotop 	= $imgattr_a['height'] + $imgattr_a['margin-top'] - $cont_h;

		if ( ! is_file( $imgsrc ) ) { // Audio without image
			$audiotop 	= wppa_get_audio_control_height();
			$imgwidth 	= wppa_opt( 'tf_width' );
			$imgheight 	= wppa_get_audio_control_height();
		}
		$result 	.= wppa_get_audio_html( array(
							'id' 		=> $id,
							'width'		=> $imgwidth,
							'height' 	=> $cont_h,
							'style' 	=> 'position:absolute;top:'.$audiotop.'px;left:0;border:none;'
							));

		$result .= '</div>';
	}

	// Get the image link
	if ( wppa( 'is_topten' ) ) {
		$no_album = ! wppa( 'start_album' );
		if ( $no_album ) $tit = __( 'View the top rated photos' , 'wp-photo-album-plus'); else $tit = esc_attr( __( stripslashes( $thumb['description'] ) ) );
		$link = wppa_get_imglnk_a( 'thumb', $id, '', $tit, '', $no_album );
	}
	else $link = wppa_get_imglnk_a( 'thumb', $id ); // voor parent uplr

	// See if ajax possible
	if ( $link ) {

		// Is link an url?
		if ( $link['is_url'] ) {
			if ( wppa_switch( 'allow_ajax' )
				&& wppa_opt( 'thumb_linktype' ) == 'photo' 							// linktype must be to slideshow image
				&& wppa_opt( 'thumb_linkpage' ) == '0'									// same page/post
				&& ! wppa_switch( 'thumb_blank' )										// not on a new tab
				&& ! ( wppa_switch( 'thumb_overrule' ) && $thumb['linkurl'] )			// no ( ps overrule set AND link present )
				&& ! wppa( 'is_topten' )													// no topten selection
				&& ! wppa( 'is_lasten' )													// no lasten selection
				&& ! wppa( 'is_comten' )													// no comten selection
				&& ! wppa( 'is_featen' )
				&& ! wppa( 'is_tag' )														// no tag selection
				&& ! wppa( 'is_upldr' )														// not on uploader deisplay
				&& ! wppa( 'src' )															// no search
				&& ! wppa( 'supersearch' )													// no supersearch
//				&& ! wppa( 'calendar' )
				&& ( wppa_is_int( wppa( 'start_album' ) ) || wppa( 'start_album' ) == '' )	// no set of albums
				 )
			{ 	// Ajax	possible

				// The a img ajax
				$p =  wppa( 'calendar') ? '' : '&amp;wppa-photo=' . $xid;
				$onclick = 'wppaDoAjaxRender( ' .
								wppa( 'mocc' ) . ', \'' .
								wppa_encrypt_url( wppa_get_slideshow_url_ajax( wppa( 'start_album' ), '0' ) ) .
								'&amp;wppa-photo=' . $xid .
								'\', \'' .
								wppa_convert_to_pretty( wppa_encrypt_url( wppa_get_slideshow_url( wppa( 'start_album' ), '0' ) . $p ) ) .
								'\' )';

				$result .= '<a style="position:static;" class="thumb-img" id="x-'.$xid.'-'.wppa( 'mocc' ).'">';

				// Video?
				if ( $is_video ) {

					$result .= wppa_get_video_html( array(
							'id'			=> $id,
							'width'			=> $imgwidth,
							'height' 		=> $imgheight,
							'controls' 		=> wppa_switch( 'thumb_video' ),
							'margin_top' 	=> '0',
							'margin_bottom' => '0',
							'tagid' 		=> 'i-'.$id.'-'.wppa( 'mocc' ),
							'cursor' 		=> 'cursor:pointer;',
							'events' 		=> $events,
							'title' 		=> $title,
							'preload' 		=> 'metadata',
							'onclick' 		=> $onclick,
							'lb' 			=> false,
							'class' 		=> '',
							'style' 		=> $imgstyle
							));
				}

				// No video
				else {
					$result .= 	'<img' .
									' onclick="' . $onclick . '"' .
									' id="i-' . $xid . '-'.wppa( 'mocc' ) . '"' .
									' src="' . $imgurl . '"' .
									' ' . $imgalt .
									( $title ? ' title="' . $title . '"' : '' ) .
									' width="' . $imgwidth . '"' .
									' height="' . $imgheight . '"' .
									' style="' . $imgstyle . ' cursor:pointer;"' .
									' ' . $events .
								' />';
				}

				// Close the a img ajax
				$result .= '</a>';
			}

			// non ajax
			else {

				// The a img non ajax
				$result .= '<a style="position:static;" href="'.$link['url'].'" target="'.$link['target'].'" class="thumb-img" id="x-'.$xid.'-'.wppa( 'mocc' ).'">';
				if ( $is_video ) {
					$result .= wppa_get_video_html( array(
							'id'			=> $id,
							'width'			=> $imgwidth,
							'height' 		=> $imgheight,
							'controls' 		=> wppa_switch( 'thumb_video' ),
							'margin_top' 	=> '0',
							'margin_bottom' => '0',
							'tagid' 		=> 'i-'.$xid.'-'.wppa( 'mocc' ),
							'cursor' 		=> 'cursor:pointer;',
							'events' 		=> $events,
							'title' 		=> $title,
							'preload' 		=> 'metadata',
							'onclick' 		=> '',
							'lb' 			=> false,
							'class' 		=> '',
							'style' 		=> $imgstyle
							));
				}
				else {
					$result .= 	'<img' .
									' id="i-' . $xid . '-' . wppa( 'mocc' ) . '"' .
									' src="' . $imgurl . '" ' . $imgalt .
									( $title ? ' title="' . $title . '"' : '' ) .
									' width="' . $imgwidth . '"' .
									' height="' . $imgheight . '"' .
									' style="' . $imgstyle . ' cursor:pointer;"' .
									' ' . $events .
								' />';
				}

				// Close the img non ajax
				$result .= '</a>';
			}
		}

		// Link is not an url. link is lightbox ?
		elseif ( $link['is_lightbox'] ) {
			$title 		= wppa_get_lbtitle( 'thumb', $id );

			// The a img
			$result .= '<a href="'.$link['url'].'" target="'.$link['target'] . '"' .
						( $is_video ? ' data-videohtml="' . esc_attr( wppa_get_video_body( $id ) ) . '"' .
						' data-videonatwidth="'.wppa_get_videox( $id ) . '"' .
						' data-videonatheight="'.wppa_get_videoy( $id ) . '"' : '' ) .
						( $has_audio ? ' data-audiohtml="' . esc_attr( wppa_get_audio_body( $id ) ) . '"' : '' ) .
						' ' . wppa( 'rel' ) . '="'.wppa_opt( 'lightbox_name' ).'[occ'.wppa( 'mocc' ).']"' .
						' ' . wppa( 'lbtitle' ) . '="'.$title.'" ' .
						' class="thumb-img" id="x-'.$xid.'-'.wppa( 'mocc' ).'">';
			if ( $is_video ) {
				$result .= wppa_get_video_html( array(
						'id'			=> $id,
						'width'			=> $imgwidth,
						'height' 		=> $imgheight,
						'controls' 		=> wppa_switch( 'thumb_video' ),
						'margin_top' 	=> '0',
						'margin_bottom' => '0',
						'tagid' 		=> 'i-'.$xid.'-'.wppa( 'mocc' ),
						'cursor' 		=> $cursor,
						'events' 		=> $events,
						'title' 		=> wppa_zoom_in( $id ),
						'preload' 		=> 'metadata',
						'onclick' 		=> '',
						'lb' 			=> false,
						'class' 		=> '',
						'style' 		=> $imgstyle
						));
			}
			else {
				$title = wppa_zoom_in( $id );
				$result .= 	'<img' .
								' id="i-' . $xid . '-' . wppa( 'mocc' ) . '"' .
								' src="' . $imgurl . '"' .
								' ' . $imgalt .
								( $title ? ' title="' . $title . '"' : '' ) .
								' width="' . $imgwidth . '"' .
								' height="' . $imgheight . '"' .
								' style="' . $imgstyle . $cursor . '"' .
								' ' . $events .
							' />';
			}

			// Close the a img
			$result .= '</a>';
		}
		else {	// is onclick
			// The div img
			$result .= '<div onclick="'.$link['url'].'" class="thumb-img" id="x-'.$xid.'-'.wppa( 'mocc' ).'">';
			if ( $is_video ) {
				$result .= wppa_get_video_html( array(
						'id'			=> $id,
						'width'			=> $imgwidth,
						'height' 		=> $imgheight,
						'controls' 		=> wppa_switch( 'thumb_video' ),
						'margin_top' 	=> '0',
						'margin_bottom' => '0',
						'tagid' 		=> 'i-'.$xid.'-'.wppa( 'mocc' ),
						'cursor' 		=> 'cursor:pointer;',
						'events' 		=> $events,
						'title' 		=> $title,
						'preload' 		=> 'metadata',
						'onclick' 		=> '',
						'lb' 			=> false,
						'class' 		=> '',
						'style' 		=> $imgstyle
						));
			}
			else {
				$result .= 	'<img' .
								' id="i-' . $xid . '-' . wppa( 'mocc' ) . '"' .
								' src="' . $imgurl . '"' .
								' ' . $imgalt .
								( $title ? ' title="' . $title . '"' : '' ) .
								' width="' . $imgwidth . '"' .
								' height="' . $imgheight . '"' .
								' style="' . $imgstyle . ' cursor:pointer;"' .
								' ' . $events .
							' />';
			}
			$result .= '</div>';
			$result .= '<script type="text/javascript">';
			$result .= '/* <![CDATA[ */';
			$result .= 'wppaPopupOnclick['.$id.'] = "'.$link['url'].'";';
			$result .= '/* ]]> */';
			$result .= '</script>';
		}
	}
	else {	// no link
		if ( wppa_switch( 'use_thumb_popup' ) ) {
			$result .= '<div id="x-'.$xid.'-'.wppa( 'mocc' ).'">';
				if ( $is_video ) {
					$result .= wppa_get_video_html( array(
							'id'			=> $id,
							'width'			=> $imgwidth,
							'height' 		=> $imgheight,
							'controls' 		=> false,
							'margin_top' 	=> '0',
							'margin_bottom' => '0',
							'tagid' 		=> 'i-'.$xid.'-'.wppa( 'mocc' ),
							'cursor' 		=> '',
							'events' 		=> $events,
							'title' 		=> $title,
							'preload' 		=> 'metadata',
							'onclick' 		=> '',
							'lb' 			=> false,
							'class' 		=> '',
							'style' 		=> $imgstyle
							));
				}
				else {
					$result .= 	'<img' .
									' src="' . $imgurl . '"' .
									' ' . $imgalt .
									( $title ? ' title="' . $title . '"' : '' ) .
									' width="' . $imgwidth . '"' .
									' height="' . $imgheight . '"' .
									' style="' . $imgstyle . '"' .
									' ' . $events .
								' />';
				}
			$result .= '</div>';
		}
		else {
			if ( $is_video ) {
				$result .= wppa_get_video_html( array(
						'id'			=> $id,
						'width'			=> $imgwidth,
						'height' 		=> $imgheight,
						'controls' 		=> wppa_switch( 'thumb_video' ),
						'margin_top' 	=> '0',
						'margin_bottom' => '0',
						'tagid' 		=> 'i-'.$id.'-'.wppa( 'mocc' ),
						'cursor' 		=> '',
						'events' 		=> $events,
						'title' 		=> $title,
						'preload' 		=> 'metadata',
						'onclick' 		=> '',
						'lb' 			=> false,
						'class' 		=> '',
						'style' 		=> $imgstyle
						));
			}
			else {
				$result .= 	'<img' .
								' src="' . $imgurl . '"' .
								' ' . $imgalt .
								( $title ? ' title="' . $title . '"' : '' ) .
								' width="' . $imgwidth . '"' .
								' height="' . $imgheight . '"' .
								' style="' . $imgstyle . '"' .
								' ' . $events . ' />';
			}
		}
	}

	// The medals if near the bottom
	$result .= wppa_get_medal_html_a( array( 'id' => $id, 'size' => $medalsize, 'where' => 'bot' ) );

	// Close the image container
	$result .= '</div>';
/*
	// The audio when popup
	if ( wppa_switch( 'use_thumb_popup' ) && wppa_switch( 'thumb_audio' ) && wppa_has_audio( $id ) && ! $com_alt ) {
		$result .= wppa_get_audio_html( array(
							'id' 		=> $id,
							'width'		=> $imgwidth
							));
	}
*/
	// Comten alt display?
	if ( $com_alt ) {
		$result .= 	'<div' .
						' class="wppa-com-alt wppa-com-alt-' . wppa( 'mocc' ) . '"' .
						' style="' .
							'height:' . $imgheight . 'px;' .
							'overflow:auto;' .
							'margin: 0 0 8px 10px;' .
							'border:1px solid ' . wppa_opt( 'bcolor_alt' ) . ';' .
							'"' .
					' >';

			$comments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `".WPPA_COMMENTS."` WHERE `photo` = %s AND `status` = 'approved' ORDER BY `timestamp` DESC", $id ), ARRAY_A );
			$first = true;
			if ( $comments ) foreach ( $comments as $com ) {
				$result .= 	'<h6' .
								' style="' .
									'font-size:10px;' .
									'line-height:12px;' .
									'font-weight:bold;' .
									'padding:'.( $first ? '0' : '6px' ).' 0 0 6px;' .
									'margin:0;float:left;'.
									'"'.
								'>' .
									$com['user'] . ' ' . __( 'wrote' , 'wp-photo-album-plus') . ' ' . wppa_get_time_since( $com['timestamp'] ) . ':' .
							'</h6>'.
							'<p' .
								' style="' .
									'font-size:10px;' .
									'line-height:12px;' .
									'padding:0 0 0 6px;' .
									'text-align:left;' .
									'margin:0;' .
									'clear:left;' .
									'"' .
								'>' .
									html_entity_decode( convert_smilies( stripslashes( $com['comment'] ) ) ) .
							'</p>';
							$first = false;
			}
		$result .= '</div>';
	}

	// NOT comalt
	else {

		// Open the subtext container
		$margtop = wppa_switch( 'align_thumbtext' ) ? '' : 'margin-top:'.-$imgmarginbottom.'px;';
		$subtextcontheight = $frameheight - max( $imgwidth,$imgheight );
		if ( ! wppa_switch( 'align_thumbtext' ) ) $subtextcontheight += $imgmarginbottom;
		$result.=	'<div' .
						' style="' .
							'height:'.$subtextcontheight.'px;' .
							'width:'.$framewidth.'px;' .
							'position:absolute;' .
							$margtop .
							'overflow:hidden;' .
						'" >';

		// Single button voting system
		if ( wppa_opt( 'rating_max' ) == '1' && wppa_switch( 'vote_thumb' ) ) {
			$mylast  = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM `'.WPPA_RATING.'` WHERE `photo` = %s AND `user` = %s ORDER BY `id` DESC LIMIT 1', $id, wppa_get_user() ), ARRAY_A );
			$buttext = $mylast ? __( wppa_opt( 'voted_button_text' ) , 'wp-photo-album-plus') : __( wppa_opt( 'vote_button_text' ) , 'wp-photo-album-plus');
			$result .= 	'<input' .
							' id="wppa-vote-button-' . wppa( 'mocc' ) . '-' . $xid . '"' .
							' class="wppa-vote-button-thumb"' .
							' style="margin:0;"' .
							' type="button"' .
							' onclick="wppaVoteThumb( ' . wppa( 'mocc' ) . ', ' . $id . ' )"' .
							' value="'.$buttext.'"' .
						' />';
		}

		// Name
		if ( wppa_switch( 'thumb_text_name' ) || wppa_switch( 'thumb_text_owner' ) ) {
			$result .= 	'<div' .
							' class="wppa-thumb-text"' .
							' style="'.__wcs( 'wppa-thumb-text' ).'"' .
							' >' .
							wppa_get_photo_name( $id, wppa_switch( 'thumb_text_owner' ), false, false, wppa_switch( 'thumb_text_name' ) ) .
						'</div>';
		}

		// searching, link to album
//		if ( wppa( 'src' ) || wppa( 'supersearch' ) || ( ( wppa( 'is_comten') || wppa( 'is_topten' ) || wppa( 'is_lasten' ) || wppa( 'is_featen') ) && wppa( 'start_album' ) != $thumb['album'] ) ) {
		if ( wppa_switch( 'thumb_text_virt_album' ) && wppa_is_virtual() && wppa( 'start_album' ) != $thumb['album'] ) {
			$result .= 	'<div' .
							' class="wppa-thumb-text"' .
							' style="' . __wcs( 'wppa-thumb-text' ) . '"' .
							' >' .
							'<a' .
								' href="' . wppa_get_album_url( $thumb['album'] ) . '"' .
								' >' .
								'<span class="wppa-tnpar" >(</span>' .
									stripslashes( __( wppa_get_album_name( $thumb['album'] ) , 'wp-photo-album-plus') ) .
								'<span class="wppa-tnpar" >)</span>' .
							'</a>' .
						'</div>';
		}

		// Share
		if ( wppa_switch( 'share_on_thumbs' ) ) {
			$result .= 	'<div' .
							' class="wppa-thumb-text"' .
							' style="' . __wcs( 'wppa-thumb-text' ) . '"' .
							' >' .
							wppa_get_share_html( $id, 'thumb' ) .
						'</div>';
		}

		// Delete and Edit links
		if ( wppa_switch( 'edit_thumb' ) && ! wppa_is_user_blacklisted() ) {
			if ( ( wppa_user_is( 'administrator' ) ) || ( current_user_can( 'wppa_moderate' ) ) || ( wppa_get_user() == wppa_get_photo_owner( $id ) && wppa_switch( 'upload_edit' ) ) ) {
				$result .= 	'<div' .
								' class="wppa-thumb-text"' .
								' style="' . __wcs( 'wppa-thumb-text' ) . '"' .
								' >';

								// The admins choice link
								if ( wppa_user_is( 'administrator' ) && wppa_switch( 'enable_admins_choice' ) ) {
									$result .=
									'<span' .
										' id="admin-choice-' . wppa_encrypt_photo($thumb['id']) . '-' . wppa( 'mocc' ) . '"' .
										' style="color:gray;"' .
										' >';
									if ( ! wppa_is_photo_in_zip( $thumb['id'] ) ) {
										$result .=
										'<a' .
											' style="color:blue;cursor:pointer;"' .
											' onclick="' .
												esc_attr( 'if ( confirm( "' . __( 'Are you sure you want to add this photo to your zip?' , 'wp-photo-album-plus') . '" ) ) ' .
												'wppaAjaxAddPhotoToZip( '.wppa( 'mocc' ).', \''.wppa_encrypt_photo($thumb['id']).'\', false ); return false;' ).'"' .
											'>' .
											__( 'MyChoice' , 'wp-photo-album-plus') .
										'</a>';
									}
									else {
										$result .= __('Selected', 'wp-photo-album-plus');
									}
									$result .=
									'</span>&nbsp;';
								}

								$result .=
								'<a' .
									' style="color:red;cursor:pointer;"' .
									' onclick="'.esc_attr( 'if ( confirm( "'.__( 'Are you sure you want to remove this photo?' , 'wp-photo-album-plus').'" ) ) wppaAjaxRemovePhoto( '.wppa( 'mocc' ).', \''.$xid.'\', false ); return false;' ).'"' .
									' >' .
									__( 'Delete' , 'wp-photo-album-plus') .
								'</a>' .
									'&nbsp;' .
								'<a' .
									' style="color:green;cursor:pointer;"' .
									' onclick="wppaEditPhoto( '.wppa( 'mocc' ).', \''.$xid.'\' ); return false;"' .
									' >' .
									__( 'Edit' , 'wp-photo-album-plus') .
								'</a>' .
							'</div>';
			}
		}

		// Description
		if ( wppa_switch( 'thumb_text_desc' ) || $thumb['status'] == 'pending' || $thumb['status'] == 'scheduled' ) {
			$desc = '';
			if ( $thumb['status'] == 'pending' || $thumb['status'] == 'scheduled' ) {
				$desc .= wppa_moderate_links( 'thumb', $id );
			}
			$desc .= wppa_get_photo_desc( $id, wppa_switch( 'allow_foreign_shortcodes_thumbs' ) );
			$result .= 	'<div' .
							' class="wppa-thumb-text"' .
							' style="'.__wcs( 'wppa-thumb-text' ).'"' .
							' >' . $desc .
						'</div>';
		}

		// Rating
		if ( wppa_switch( 'thumb_text_rating' ) ) {
			$rating = wppa_get_rating_by_id( $id );
			if ( $rating && wppa_switch( 'show_rating_count' ) ) $rating .= ' ( '.wppa_get_rating_count_by_id( $id ).' )';
			$result .= 	'<div' .
							' class="wppa-thumb-text"' .
							' style="'.__wcs( 'wppa-thumb-text' ).'"' .
							' >' . $rating .
						'</div>';
		}

		// Comcount
		if ( wppa_switch( 'thumb_text_comcount' ) ) {
			$comcount = $wpdb->get_var( "SELECT COUNT(*) FROM `" . WPPA_COMMENTS ."` WHERE `photo` = " . $id );
			if ( $comcount ) {
				$result .= 	'<div' .
								' class="wppa-thumb-text"' .
								' style="'.__wcs( 'wppa-thumb-text' ).'"' .
								' >' . sprintf( _n( '%d comment', '%d comments', $comcount, 'wp-photo-album-plus'), $comcount ) .
							'</div>';
			}
		}

		// Viewcount
		if ( wppa_switch( 'thumb_text_viewcount' ) ) {
			$result .= 	'<div' .
							' class="wppa-thumb-text"' .
							' style="clear:both;'.__wcs( 'wppa-thumb-text' ).'"' .
							' >' . sprintf( _n( '%d view', '%d views', $thumb['views'], 'wp-photo-album-plus'), $thumb['views'] ) .
						'</div>';
		}

		// Close the subtext container
		$result .= 	'</div>';

	} // if ! $com_alt

	// Close the thumbframe
	$result .= '</div>';

	if ( $com_alt ) $result .= '</div>';

	return $result;
}

// A thumb 'as cover'
function wppa_thumb_ascover( $id ) {
global $cover_count_key;
global $thlinkmsggiven;

	// Init
	$result = '';

	// Encrypted photo id
	$xid = wppa_encrypt_photo( $id );

	// Get the photo info
	$thumb = wppa_cache_thumb( $id );

	// Get the album info
	$album = wppa_cache_album( $thumb['album'] );

	$path 		= wppa_fix_poster_ext( wppa_get_thumb_path( $id ), $id );
	$imgattr_a 	= wppa_get_imgstyle_a( $id, $path, wppa_opt( 'smallsize' ), '', 'cover' );
	$events 	= is_feed() ? '' : wppa_get_imgevents( 'cover' );
	$src 		= wppa_fix_poster_ext( wppa_get_thumb_url( $id, '', $imgattr_a['width'], $imgattr_a['height'] ), $id );
	$link 		= wppa_get_imglnk_a( 'thumb', $id );

	if ( $link ) {
		$href = $link['url'];
		$title = $link['title'];
		$target = $link['target'];
	}
	else {
		$href = '';
		$title = '';
		$target = '';
	}

	if ( ! $link['is_url'] ) {
		if ( ! $thlinkmsggiven ) wppa_dbg_msg( 'Title link may not be an event in thumbs as covers.' );
		$href = '';
		$title = '';
		$thlinkmsggiven = true;
	}

	$mcr = wppa_opt( 'thumbtype' ) == 'ascovers-mcr' ? 'mcr-' : '';

	$photo_left = wppa_switch( 'thumbphoto_left' );
	$class_asym = 'wppa-asym-text-frame-'.$mcr.wppa( 'mocc' );

	$style = __wcs( 'wppa-box' ).__wcs( 'wppa-'.wppa( 'alt' ) );
	if ( is_feed() ) $style .= ' padding:7px;';

	$wid = wppa_get_cover_width( 'thumb' );
	$style .= 'width: '.$wid.'px;';
	if ( $cover_count_key == 'm' ) {
		$style .= 'margin-left: 8px;';
	}
	elseif ( $cover_count_key == 'r' ) {
		$style .= 'float:right;';
	}
	else {
		$style .= 'clear:both;';
	}
	wppa_step_covercount( 'thumb' );

	$result .= 	"\n" .  '<div' .
							' id="thumb-' . $xid . '-' . wppa( 'mocc' ) . '"' .
							' class="thumb wppa-box wppa-cover-box wppa-cover-box-' . $mcr . wppa( 'mocc' ) . ' wppa-' . wppa( 'alt' ) . '"' .
							' style="' . $style . '"' .
							' >';

		if ( $photo_left ) {
			$result .= wppa_the_thumbascoverphoto( $id, $src, $photo_left, $link, $imgattr_a, $events );
		}

		$textframestyle = wppa_get_text_frame_style( $photo_left, 'thumb' );

		$result .=  '<div' .
						' id="thumbtext_frame_' . $id . '_' . wppa( 'mocc' ) . '"' .
						' class="wppa-text-frame-' . wppa( 'mocc' ) . ' wppa-text-frame thumbtext-frame ' . $class_asym . '"' .
						' ' . $textframestyle .
						' >' .
						'<h2' .
							' class="wppa-title"' .
							' style="clear:none;"' .
							' >';
							if ( $link['is_lightbox'] ) {
								$result .= wppa_get_photo_name( $id );
							}
							else {
								$result .= 	'<a' .
												' href="' . $href . '"' .
												' target="' . $target . '"' .
												' title="' . $title . '"' .
												' style="' . __wcs( 'wppa-title' ) . '"' .
												' >' . wppa_get_photo_name( $id ) .
											'</a>';
							}
			$result .= 	'</h2>';

			$desc =  wppa_get_photo_desc( $id );
			if ( in_array( $thumb['status'], array( 'pending', 'scheduled' ) ) ) $desc .= wppa_moderate_links( 'thumb', $id );

			$result .= 	'<p' .
							' class="wppa-box-text wppa-black"' .
							' style="' . __wcs( 'wppa-box-text' ) . __wcs( 'wppa-black' ) .
							'" >' . $desc .
						'</p>';
		$result .= 	'</div>';

		if ( ! $photo_left ) {
			$result .= wppa_the_thumbascoverphoto( $id, $src, $photo_left, $link, $imgattr_a, $events );
		}

	$result .= 	'</div>';

	// Toggle alt/even
	wppa_toggle_alt();

	wppa_out( $result );
}

// The image for the 'thumb as cover'
function wppa_the_thumbascoverphoto( $id, $src, $photo_left, $link, $imgattr_a, $events ) {

	$result 	= '';
	$href 		= $link['url'];
	$title 		= $link['title'];
	$imgattr 	= $imgattr_a['style'];
	$imgwidth 	= $imgattr_a['width'];
	$imgheight 	= $imgattr_a['height'];
	$frmwidth 	= $imgwidth + '10';	// + 2 * 1 border + 2 * 4 padding

	if ( ! $src ) {
		return '';
	}

	// Encrypted photo id
	$xid = wppa_encrypt_photo( $id );

	if ( wppa_in_widget() ) {
		$photoframestyle = 'style="text-align:center;"';
	}
	else {
		$photoframestyle = $photo_left ? 'style="float:left; margin-right:5px;width:'.$frmwidth.'px;"' : 'style="float:right; margin-left:5px;width:'.$frmwidth.'px;"';
	}

	$result .= 	'<div'.
					' id="thumbphoto_frame_' . $xid . '_' . wppa( 'mocc' ) . '"' .
					' class="thumbphoto-frame"' .
					' ' . $photoframestyle .
					'>';

	if ( $link['is_lightbox'] ) {
		$href = wppa_get_hires_url( $id );
		$cursor = ' cursor:url( ' .wppa_get_imgdir() . wppa_opt( 'magnifier' ) . ' ),pointer;';

		$result .= 	'<a' .
						' href="' . $href . '"' .
						' ' . wppa( 'rel' ) . '="' . wppa_opt( 'lightbox_name' ). '[occ' . wppa( 'mocc' ) . ']"' .
						( $title ? ' ' . wppa( 'lbtitle' ) . '="' . $title . '"' : '' ) .
						' >';

			if ( wppa_is_video( $id ) ) {
				$result .= wppa_get_video_html( array (
							'id'			=> $id,
							'width'			=> $imgwidth,
							'height' 		=> $imgheight,
							'controls' 		=> false,
					//		'margin_top' 	=> '0',
					//		'margin_bottom' => '0',
							'tagid' 		=> 'i-'.$xid.'-'.wppa( 'mocc' ),
					//		'cursor' 		=> '',
							'events' 		=> $events,
							'title' 		=> $title,
							'preload' 		=> 'metadata',
					//		'onclick' 		=> '',
							'lb' 			=> false,
							'class' 		=> 'image wppa-img',
							'style' 		=> __wcs( 'wppa-img' ).$imgattr.$cursor		//$imgstyle
						) );
			}
			else {
				$result .= 	'<img' .
								' src="' . $src . '"' .
								' ' . wppa_get_imgalt( $id ) .
								' class="image wppa-img"' .
								' width="' . $imgwidth . '"' .
								' height="' . $imgheight . '"' .
								' style="' . __wcs( 'wppa-img' ) . $imgattr . $cursor . '"' .
								' ' . $events .
							' />';
			}
		$result .= '</a>';
	}
	elseif ( $link['is_url'] ) {

		$result .= 	'<a' .
						' href="' . $href . '"' .
						( $title ? ' title="' . $title . '"' : '' ) .
						' >';

			if ( wppa_is_video( $id ) ) {
				$result .= wppa_get_video_html( array (
							'id'			=> $id,
							'width'			=> $imgwidth,
							'height' 		=> $imgheight,
							'controls' 		=> false,
					//		'margin_top' 	=> '0',
					//		'margin_bottom' => '0',
							'tagid' 		=> 'i-'.$xid.'-'.wppa( 'mocc' ),
					//		'cursor' 		=> '',
							'events' 		=> $events,
							'title' 		=> $title,
							'preload' 		=> 'metadata',
					//		'onclick' 		=> '',
							'lb' 			=> false,
							'class' 		=> 'image wppa-img',
							'style' 		=> __wcs( 'wppa-img' ).$imgattr		//$imgstyle
						) );
			}
			else {
				$result .= 	'<img' .
								' src="' . $src . '"' .
								' ' . wppa_get_imgalt( $id ) .
								' class="image wppa-img"' .
								' width="' . $imgwidth . '"' .
								' height="' . $imgheight . '"' .
								' style="' . __wcs( 'wppa-img' ) . $imgattr . '"' .
								' ' . $events .
								' />';
			}
		$result .= 	'</a>';
	}
	else {
		if ( wppa_is_video( $id ) ) {
				$result .= wppa_get_video_html( array (
							'id'			=> $id,
							'width'			=> $imgwidth,
							'height' 		=> $imgheight,
							'controls' 		=> false,
					//		'margin_top' 	=> '0',
					//		'margin_bottom' => '0',
							'tagid' 		=> 'i-'.$xid.'-'.wppa( 'mocc' ),
					//		'cursor' 		=> '',
							'events' 		=> $events,
							'title' 		=> $title,
							'preload' 		=> 'metadata',
							'onclick' 		=> $href,
							'lb' 			=> false,
							'class' 		=> 'image wppa-img',
							'style' 		=> __wcs( 'wppa-img' ).$imgattr		//$imgstyle
						) );
		}
		else {
			$result .= 	'<img' .
							' src="' . $src . '"' .
							' ' . wppa_get_imgalt( $id ) .
							' class="image wppa-img"' .
							' width="' . $imgwidth . '"' .
							' height="' . $imgheight . '"' .
							' style="' . __wcs( 'wppa-img' ) . $imgattr . '"' .
							' ' . $events .
							' onclick="' . $href . '"' .
							' />';
		}
	}
	$result .= '</div>';

	return $result;
}

// Display the masonry thumbnail image
function wppa_thumb_masonry( $id ) {

	wppa_out( wppa_get_thumb_masonry( $id ) );
}

// Get the masonry thumbnail image html
function wppa_get_thumb_masonry( $id ) {
global $wpdb;

	// Init
	if ( ! $id ) {
		wppa_dbg_msg('Please check file wppa-theme.php or any other php file that calls wppa_thumb_masonry(). Argument 1: photo id is missing!', 'red', 'force' );
		die( 'Please check your configuration' );
	}
	$result = '';

	// Encrypted photo id
	$xid = wppa_encrypt_photo( $id );

	$cont_width = wppa_get_container_width();
	$count_cols = ceil( $cont_width / wppa_opt( 'thumbsize' ) );

	// Get the photo info
	$thumb = wppa_cache_thumb( $id );

	// Get the album info
	$album = wppa_cache_album( $thumb['album'] );

	// Get photo info
	$is_video 		= wppa_is_video( $id );
	$has_audio 		= wppa_has_audio( $id );
	$imgsrc 		= wppa_fix_poster_ext( wppa_get_thumb_path( $id ), $id );

	if ( ! wppa_is_video( $id ) && ! is_file( $imgsrc ) ) {
		$result .= 	'<div' .
						' class=""' .
						' style="' .
							'font-size:10px;' .
							'color:red;' .
							'width:' . wppa_opt( 'thumbsize' ) . 'px;' .
							'position:static;' .
							'float:left;' .
							'"' .
						' >' .
						sprintf( __( 'Missing thumbnail image #%s' , 'wp-photo-album-plus'), $id ) .
					'</div>';
		return $result;
	}

	$alt 				= $album['alt_thumbsize'] == 'yes' ? '_alt' : '';
	$imgattr_a 			= wppa_get_imgstyle_a( $id, $imgsrc, wppa_opt( 'thumbsize'.$alt ), 'optional', 'thumb' );

	// Verical style ?
	if ( wppa_opt( 'thumbtype' ) == 'masonry-v' ) {
		$imgwidth  		= wppa_opt( 'thumbsize' );
		$imgheight 		= $imgwidth * wppa_get_thumbratioyx( $id );
		$imgstyle  		= 'width:100%; height:auto; margin:0; position:relative; box-sizing:border-box;';
		$frame_h 		= '';
	}

	// Horizontal style ?
	else {
		$imgheight 		= wppa_opt( 'thumbsize' );
		$imgwidth 		= $imgheight * wppa_get_thumbratioxy( $id );
		$imgstyle  		= 	'height:100%;' .
							'width:auto;' .
							'margin:0;' .
							'position:relative;' .
							'box-sizing:border-box;' .
							'';
		$frame_h 		= 'height:100%; ';
	}

	// Mouseover effect?
	if ( wppa_switch( 'use_thumb_opacity' ) ) {
		$opac = wppa_opt( 'thumb_opacity' );
		$imgstyle .= ' opacity:' . $opac/100 . '; filter:alpha( opacity=' . $opac . ' );';
	}

	// Padding
	if ( wppa_is_int( wppa_opt( 'tn_margin' ) / 2 ) ) {
		$imgstyle 		.= ' padding:'.( wppa_opt( 'tn_margin' ) / 2 ).'px;';
	}
	else {
		$p1 			= floor( wppa_opt( 'tn_margin' ) / 2 );
		$p2 			= ceil( wppa_opt( 'tn_margin' ) / 2 );
		$imgstyle 		.= ' padding:'.$p1.'px '.$p2.'px '.$p2.'px '.$p1.'px;';
	}

	// Cursor
	$cursor	   			= $imgattr_a['cursor'];

	// Popup ?
	if ( wppa_switch( 'use_thumb_popup' ) ) {

		// Landscape?
		if ( $imgwidth > $imgheight ) {
			$popwidth 	= wppa_opt( 'popupsize' );
			$popheight 	= round( $popwidth * $imgheight / $imgwidth );
		}

		// Portrait
		else {
			$popheight 	= wppa_opt( 'popupsize' );
			$popwidth 	= round( $popheight * $imgwidth / $imgheight );
		}
	}

	// No popup
	else {
		$popwidth 	= $imgwidth;
		$popheight 	= $imgheight;
	}

	$imgurl    	= wppa_fix_poster_ext( wppa_get_thumb_url( $id, '', $popwidth, $popheight ), $id );
	$events    	= wppa_get_imgevents( 'thumb', $id );
	$imgalt		= wppa_get_imgalt( $id );	// returns something like ' alt="Any text" '
	$title 		= esc_attr( wppa_get_masonry_title( $id ) ); // esc_attr( wppa_get_photo_name( $id ) );

	// Feed ?
	if ( is_feed() ) {
		$imgattr_a = wppa_get_imgstyle_a( $id, $imgsrc, '100', '4', 'thumb' );
		$style = $imgattr_a['style'];
		$result .= '<a href="' . get_permalink() . '">' .
						'<img' .
							' src="' . $imgurl . '"' .
							' ' . $imgalt .
							( $title ? ' title="' . $title . '"' : '' ) .
							' style="'.$style.'"' .
						' />' .
					'</a>';
		return;
	}

	// Get the image link
	if ( wppa( 'is_topten' ) ) {
		$no_album = ! wppa( 'start_album' );
		if ( $no_album ) $tit = __( 'View the top rated photos' , 'wp-photo-album-plus'); else $tit = esc_attr( __( stripslashes( $thumb['description'] ) ) );
		$link = wppa_get_imglnk_a( 'thumb', $id, '', $tit, '', $no_album );
	}
	else $link = wppa_get_imglnk_a( 'thumb', $id ); // voor parent uplr

	// Open the thumbframe
	// Add class wppa-mas-h-{mocc} for ie if horizontal
	$is_ie_or_chrome = strpos( $_SERVER["HTTP_USER_AGENT"], 'Trident' ) || strpos( $_SERVER["HTTP_USER_AGENT"], 'Chrome' );
	$result .= '
				<div' .
					' id="thumbnail_frame_masonry_' . $xid . '_' . wppa( 'mocc' ) . '"' .
					( $is_ie_or_chrome && wppa_opt( 'thumbtype' ) == 'masonry-h' ? ' class="wppa-mas-h-' . wppa( 'mocc' ) . '"' : '' ) .
					' style="' .
						$frame_h .
						'position:static;' .
						'float:left;' .
						'font-size:12px;' .
						'line-height:8px;' .
						'overflow:hidden;' .
						'box-sizing:content-box;' .
					'" >';

	// The medals
	$result .= wppa_get_medal_html_a( array( 'id' => $id, 'size' => 'M', 'where' => 'top' ) );

	// See if ajax possible
	if ( $link ) {
		if ( $link['is_url'] ) {	// is url
			if ( wppa_switch( 'allow_ajax' )
				&& wppa_opt( 'thumb_linktype' ) == 'photo' 						// linktype must be to slideshow image
				&& wppa_opt( 'thumb_linkpage' ) == '0'								// same page/post
				&& ! wppa_switch( 'thumb_blank' )									// not on a new tab
				&& ! ( wppa_switch( 'thumb_overrule' ) && $thumb['linkurl'] )		// no ( ps overrule set AND link present )
				&& ! wppa( 'is_topten' )													// no topten selection
				&& ! wppa( 'is_lasten' )													// no lasten selection
				&& ! wppa( 'is_comten' )													// no comten selection
				&& ! wppa( 'is_featen' )
				&& ! wppa( 'is_tag' )													// no tag selection
				&& ! wppa( 'is_upldr' )													// not on uploader deisplay
				&& ! wppa( 'src' )														// no search
				&& ! wppa( 'supersearch' )													// no supersearch
	//			&& ! wppa( 'calendar' )
				&& ( wppa_is_int( wppa( 'start_album' ) ) || wppa( 'start_album' ) == '' )	// no set of albums
				 )
			{ 	// Ajax	possible

				// The a img ajax
				$p =  wppa( 'calendar') ? '' : '&amp;wppa-photo=' . $xid;
				$onclick = 'wppaDoAjaxRender( ' .
								wppa( 'mocc' ) . ', \'' .
								wppa_encrypt_url( wppa_get_slideshow_url_ajax( wppa( 'start_album' ), '0' ) ) .
								'&amp;wppa-photo=' . $xid .
								'\', \'' .
								wppa_convert_to_pretty( wppa_encrypt_url( wppa_get_slideshow_url( wppa( 'start_album' ), '0' ) . $p ) ) .
								'\' )';
// old			$onclick = "wppaDoAjaxRender( ".wppa( 'mocc' ).", '".wppa_get_slideshow_url_ajax( wppa( 'start_album' ), '0' ).'&amp;wppa-photo='.$id."', '".wppa_convert_to_pretty( wppa_get_slideshow_url( wppa( 'start_album' ), '0' )."&amp;wppa-photo=".$id )."' )";
				$result .= '<a style="position:static;" class="thumb-img" id="x-'.$id.'-'.wppa( 'mocc' ).'">';
				if ( $is_video ) {
//					$result .= '<video preload="metadata" onclick="'.$onclick.'" id="i-'.$id.'-'.wppa( 'mocc' ).'" '.$imgalt.' title="'.$title.'" style="'.$imgstyle.' cursor:pointer;" '.$events.' >'.wppa_get_video_body( $id ).'</video>';
					$result .= wppa_get_video_html( array(
							'id'			=> $id,
					//		'width'			=> $imgwidth,
					//		'height' 		=> $imgheight,
							'controls' 		=> wppa_switch( 'thumb_video' ),
							'margin_top' 	=> '0',
							'margin_bottom' => '0',
							'tagid' 		=> 'i-'.$xid.'-'.wppa( 'mocc' ),
							'cursor' 		=> 'cursor:pointer;',
							'events' 		=> $events,
							'title' 		=> $title,
							'preload' 		=> 'metadata',
							'onclick' 		=> $onclick,
							'lb' 			=> false,
							'class' 		=> '',
							'style' 		=> $imgstyle,
							'use_thumb' 	=> true
							));
				}
				else {
					$result .= 	'<img' .
									' onclick="' . $onclick . '"' .
									' id="i-' . $xid . '-' . wppa( 'mocc' ) . '"' .
									' src="' . $imgurl . '"' .
									' ' . $imgalt .
									( $title ? ' title="' . $title . '"' : '' ) .
									' style="' . $imgstyle . ' cursor:pointer;"' .
									' ' . $events .
								' />';
				}
				$result .= '</a>';
			}
			else { 	// non ajax
				// The a img non ajax
				$result .= '<a style="position:static;" href="'.$link['url'].'" target="'.$link['target'].'" class="thumb-img" id="x-'.$xid.'-'.wppa( 'mocc' ).'">';
				if ( $is_video ) {
//					$result .= '<video preload="metadata" id="i-'.$id.'-'.wppa( 'mocc' ).'" '.$imgalt.' title="'.$title.'" width="'.$imgwidth.'" height="'.$imgheight.'" style="'.$imgstyle.' cursor:pointer;" '.$events.' >'.wppa_get_video_body( $id ).'</video>';
					$result .= wppa_get_video_html( array(
							'id'			=> $id,
					//		'width'			=> $imgwidth,
					//		'height' 		=> $imgheight,
							'controls' 		=> wppa_switch( 'thumb_video' ),
							'margin_top' 	=> '0',
							'margin_bottom' => '0',
							'tagid' 		=> 'i-'.$xid.'-'.wppa( 'mocc' ),
							'cursor' 		=> 'cursor:pointer;',
							'events' 		=> $events,
							'title' 		=> $title,
							'preload' 		=> 'metadata',
							'onclick' 		=> '',
							'lb' 			=> false,
							'class' 		=> '',
							'style' 		=> $imgstyle,
							'use_thumb' 	=> true
							));
				}
				else {
					$result .= 	'<img' .
									' id="i-' . $xid . '-' . wppa( 'mocc' ) . '"' .
									' src="' . $imgurl . '"' .
									' ' . $imgalt .
									( $title ? ' title="' . $title . '"' : '' ) .
									' style="' . $imgstyle . 'cursor:pointer;"' .
									' ' . $events .
								' />';
				}
				$result .= '</a>';
			}
		}

		// Link is lightbox
		elseif ( $link['is_lightbox'] ) {

			// The a img
			$title 		= wppa_get_lbtitle( 'thumb', $id );
			$result .= '<a href="'.$link['url'].'"' .
						' target="'.$link['target'].'"' .
						( $is_video ? ' data-videohtml="' . esc_attr( wppa_get_video_body( $id ) ) . '"' .
						' data-videonatwidth="' . wppa_get_videox( $id ) . '"' .
						' data-videonatheight="' . wppa_get_videoy( $id ) . '"' : '' ) .
						( $has_audio ? ' data-audiohtml="' . esc_attr( wppa_get_audio_body( $id ) ) . '"' : '' ) .
						' ' . wppa( 'rel' ) . '="' . wppa_opt( 'lightbox_name' ) . '[occ'.wppa( 'mocc' ) . ']"' .
						( $title ? ' ' . wppa( 'lbtitle' ) . '="' . $title . '"' : '' ) .
						' class="thumb-img"' .
						' id="x-' . $xid . '-' . wppa( 'mocc' ) . '">';

			// The image
			$title = wppa_zoom_in( $id );

			// Video?
			if ( $is_video ) {
				$result .= wppa_get_video_html( array(
						'id'			=> $id,
						'controls' 		=> wppa_switch( 'thumb_video' ),
						'margin_top' 	=> '0',
						'margin_bottom' => '0',
						'tagid' 		=> 'i-'.$xid.'-'.wppa( 'mocc' ),
						'cursor' 		=> $cursor,
						'events' 		=> $events,
						'title' 		=> $title,
						'preload' 		=> 'metadata',
						'onclick' 		=> '',
						'lb' 			=> false,
						'class' 		=> '',
						'style' 		=> $imgstyle,
						'use_thumb' 	=> true
						));
			}

			// Image
			else {
				$result .= 	'<img' .
								' id="i-' . $xid . '-' . wppa( 'mocc' ) . '"' .
								' src="' . $imgurl . '"' .
								' ' . $imgalt .
								( $title ? ' title="' . $title . '"' : '' ) .
								' style="' . $imgstyle . $cursor . '"' .
								' ' . $events .
							' />';
			}
			$result .= '</a>';
		}

		// is onclick
		else {

			// The div img
			$result .= '<div onclick="'.$link['url'].'" class="thumb-img" id="x-'.$id.'-'.wppa( 'mocc' ).'" style="height:100%;" >';

			// Video?
			if ( $is_video ) {
				$result .= wppa_get_video_html( array(
						'id'			=> $id,
				//		'width'			=> $imgwidth,
				//		'height' 		=> $imgheight,
						'controls' 		=> wppa_switch( 'thumb_video' ),
						'margin_top' 	=> '0',
						'margin_bottom' => '0',
						'tagid' 		=> 'i-'.$xid.'-'.wppa( 'mocc' ),
						'cursor' 		=> 'cursor:pointer;',
						'events' 		=> $events,
						'title' 		=> $title,
						'preload' 		=> 'metadata',
						'onclick' 		=> '',
						'lb' 			=> false,
						'class' 		=> '',
						'style' 		=> $imgstyle,
						'use_thumb' 	=> true
						));
			}

			// Image
			else {
				$result .= 	'<img' .
								' id="i-' . $xid . '-' . wppa( 'mocc' ) . '"' .
								' src="' . $imgurl . '"' .
								' ' . $imgalt .
								( $title ? ' title="' . $title . '"' : '' ) .
								' style="' . $imgstyle . 'cursor:pointer;"' .
								' ' . $events .
							' />';
			}

			$result .= '</div>';
			$result .= '<script type="text/javascript">';
			$result .= '/* <![CDATA[ */';
			$result .= 'wppaPopupOnclick['.$id.'] = "'.$link['url'].'";';
			$result .= '/* ]]> */';
			$result .= '</script>';
		}
	}
	else {	// no link
		if ( wppa_switch( 'use_thumb_popup' ) ) {
			$result .= '<div id="x-'.$id.'-'.wppa( 'mocc' ).'" style="height:100%" >';
				if ( $is_video ) {
					$result .= wppa_get_video_html( array(
							'id'			=> $id,
					//		'width'			=> $imgwidth,
					//		'height' 		=> $imgheight,
							'controls' 		=> false,
							'margin_top' 	=> '0',
							'margin_bottom' => '0',
							'tagid' 		=> 'i-'.$xid.'-'.wppa( 'mocc' ),
							'cursor' 		=> '',
							'events' 		=> $events,
							'title' 		=> $title,
							'preload' 		=> 'metadata',
							'onclick' 		=> '',
							'lb' 			=> false,
							'class' 		=> '',
							'style' 		=> $imgstyle,
							'use_thumb' 	=> true
							));
				}
				else {
					$result .= 	'<img' .
									' id="i-'.$xid.'-'.wppa( 'mocc' ).'"' .
									' src="' . $imgurl . '"' .
									' ' . $imgalt .
									( $title ? ' title="' . $title . '"' : '' ) .
									' width="' . $imgwidth . '"' .
									' height="' . $imgheight . '"' .
									' style="' . $imgstyle . '"' .
									' ' . $events .
								' />';
				}
			$result .= '</div>';
		}
		else {
			if ( $is_video ) {
//				$result .= '<video preload="metadata" '.$imgalt.' title="'.$title.'" width="'.$imgwidth.'" height="'.$imgheight.'" style="'.$imgstyle.'" '.$events.' >'.wppa_get_video_body( $id ).'</video>';
				$result .= wppa_get_video_html( array(
						'id'			=> $id,
				//		'width'			=> $imgwidth,
				//		'height' 		=> $imgheight,
						'controls' 		=> false,
						'margin_top' 	=> '0',
						'margin_bottom' => '0',
						'tagid' 		=> 'i-'.$xid.'-'.wppa( 'mocc' ),
						'cursor' 		=> '',
						'events' 		=> $events,
						'title' 		=> $title,
						'preload' 		=> 'metadata',
						'onclick' 		=> '',
						'lb' 			=> false,
						'class' 		=> '',
						'style' 		=> $imgstyle,
						'use_thumb' 	=> true
						));
			}
			else {
				$result .= 	'<img' .
								' id="i-'.$xid.'-'.wppa( 'mocc' ).'"' .
								' src="' . $imgurl . '"' .
								' ' . $imgalt .
								( $title ? ' title="' . $title . '"' : '' ) .
								' width="' . $imgwidth . '"' .
								' height="' . $imgheight . '"' .
								' style="' . $imgstyle . '" ' . $events .
							' />';
			}
		}
	}

				// The audio when no popup
				if ( wppa_switch( 'thumb_audio' ) && wppa_has_audio( $id ) ) {
					$result 	.= '<div style="position:relative;z-index:11;">';
				//	$is_safari 	= strpos( $_SERVER["HTTP_USER_AGENT"], 'Safari' );
				//	$cont_h 	= $is_safari ? 16 : 28;
				//	$audiotop 	= $imgattr_a['height'] + $imgattr_a['margin-top'] - $cont_h;

		//			if ( ! is_file( $imgsrc ) ) { // Audio without image
		//				$audiotop 	= wppa_get_audio_control_height();
		//				$imgwidth 	= wppa_opt( 'tf_width' );
		//				$imgheight 	= wppa_get_audio_control_height();
		//			}
					$result 	.= wppa_get_audio_html( array(
										'id' 		=> $id,
										'tagid' 	=> 'a-'.$xid.'-'.wppa( 'mocc' ),
								//		'width'		=> $imgwidth,
								//		'height' 	=> wppa_get_audio_control_height(),
										'style' 	=> 'width:100%;position:absolute;bottom:0;margin:0;padding:'.(wppa_opt('tn_margin')/2).'px;left:0;border:none;z-index:10;'
										));

					$result .= '</div>';
				}


	// The medals
	$result .= wppa_get_medal_html_a( array( 'id' => $id, 'size' => 'M', 'where' => 'bot' ) );

	// Close the thumbframe
	$result .= '</div>';

	return $result;
}

function wppa_get_masonry_title( $id ) {

	$result = '';
	$thumb = wppa_cache_thumb( $id );

	// Name
	if ( wppa_switch( 'thumb_text_name' ) || wppa_switch( 'thumb_text_owner' ) ) {
		$result .= wppa_get_photo_name( $id, wppa_switch( 'thumb_text_owner' ), false, false, wppa_switch( 'thumb_text_name' ) ) . "\n";
	}

	// Description
	if ( wppa_switch( 'thumb_text_desc' ) || $thumb['status'] == 'pending' || $thumb['status'] == 'scheduled' ) {
		$result .= wppa_get_photo_desc( $id, wppa_switch( 'allow_foreign_shortcodes_thumbs' ) ) . "\n";
	}

	// Rating
	if ( wppa_switch( 'thumb_text_rating' ) ) {
		$rating = wppa_get_rating_by_id( $id );
		if ( $rating && wppa_switch( 'show_rating_count' ) ) {
			$result .= ' ( '.wppa_get_rating_count_by_id( $id ).' )' . "\n";
		}
	}

	// Viewcount
	if ( wppa_switch( 'thumb_text_viewcount' ) ) {
		$result .= sprintf( _n( '%d view', '%d views', $thumb['views'], 'wp-photo-album-plus'), $thumb['views'] );
	}

	$result = strip_tags( rtrim( $result, "\n" ) );
	return $result;
}

// Do the widget thumb
function wppa_do_the_widget_thumb( $type, $image, $album, $display, $link, $title, $imgurl, $imgstyle_a, $imgevents ) {
global $widget_content;

	$result = wppa_get_the_widget_thumb( $type, $image, $album, $display, $link, $title, $imgurl, $imgstyle_a, $imgevents );
	$widget_content .= $result;
}

// Get the widget thumbnail html
function wppa_get_the_widget_thumb( $type, $image, $album, $display, $link, $title, $imgurl, $imgstyle_a, $imgevents ) {

	// Init
	$result = '';

	// Get the id
	$id = $image ? $image['id'] : '0';

	// Encrypted photo id
	$xid = wppa_encrypt_photo( $id );

	// Fix url if audio
	if ( wppa_has_audio( $id ) ) {
		$imgurl = wppa_fix_poster_ext( $imgurl, $id );
	}

	// Is it a video?
	$is_video = $id ? wppa_is_video( $id ) : false;

	// Get the video and audio bodies
	$videobody = $id ? wppa_get_video_body( $id ) : '';
	$audiobody = $id ? wppa_get_audio_body( $id ) : '';

	// Open container if an image must be displayed
	if ( $display == 'thumbs' ) {
		$size = max( $imgstyle_a['width'], $imgstyle_a['height'] );
		$result .= '<div style="width:' . $size . 'px; height:' . $size . 'px; overflow:hidden;" >';
	}

	// The medals if on top
	if ( $display == 'thumbs' ) {
		$result .= $id ? wppa_get_medal_html_a( array( 'id' => $id, 'size' => 'S', 'where' => 'top' ) ) : '';
	}

	// Get the name
	$name = $id ? wppa_get_photo_name( $id ) : '';

	if ( $link ) {
		if ( $link['is_url'] ) {	// Is a href
			$result .= "\n\t" . '<a href="' . $link['url'] . '" title="' . $title . '" target="' . $link['target'] . '" >';
				$result .= "\n\t\t";
				if ( $display == 'thumbs' ) {
					if ( $is_video ) {
						$result .= wppa_get_video_html( array(
							'id'			=> $id,
							'width'			=> $imgstyle_a['width'],
							'height' 		=> $imgstyle_a['height'],
							'controls' 		=> false,
							'margin_top' 	=> $imgstyle_a['margin-top'],
							'margin_bottom' => $imgstyle_a['margin-bottom'],
							'tagid' 		=> 'i-' . $xid . '-' . wppa( 'mocc' ),
							'cursor' 		=> 'cursor:pointer;',
							'events' 		=> $imgevents,
							'title' 		=> $title,
						) );
					}
					else {
						$result .= 	'<img' .
										' id="i-' . $xid . '-' . wppa( 'mocc' ) . '"' .
										( $title ? ' title="' . $title . '"' : '' ) .
										' src="' . $imgurl . '"' .
										' width="' . $imgstyle_a['width'] . '"' .
										' height="' . $imgstyle_a['height'] . '"' .
										' style="' . $imgstyle_a['style'] . ' cursor:pointer;"' .
										' ' . $imgevents .
										' ' . wppa_get_imgalt( $id ) .
										' />';
					}
				}
				else {
					$result .= $name;
				}
			$result .= "\n\t" . '</a>';
		}
		elseif ( $link['is_lightbox'] ) {
			$title 		= wppa_get_lbtitle( 'thumb', $id );
			$videohtml 	= esc_attr( $videobody );
			$audiohtml 	= esc_attr( $audiobody );
			$result .= 	'<a href="' . $link['url'] . '"' .
						( $videohtml ? ' data-videohtml="' . $videohtml . '"' .
							' data-videonatwidth="'.wppa_get_videox( $id ).'"' .
							' data-videonatheight="'.wppa_get_videoy( $id ).'"' : '' ) .
						( $audiohtml ? ' data-audiohtml="' . $audiohtml . '"' : '' ) .
						' ' . wppa( 'rel' ) . '="' . wppa_opt( 'lightbox_name' ) . '[' . $type . '-' . $album . '-' . wppa( 'mocc' ) . ']"' .
						( $title ? ' ' . wppa( 'lbtitle' ) . '="' . $title . '"' : '' ) .
						' target="' . $link['target'] . '" >';
				$result .= "\n\t\t";
				if ( $display == 'thumbs' ) {
					$title = wppa_zoom_in( $id );
					if ( $is_video ) {
						$result .= wppa_get_video_html( array(
							'id'			=> $id,
							'width'			=> $imgstyle_a['width'],
							'height' 		=> $imgstyle_a['height'],
							'controls' 		=> false,
							'margin_top' 	=> $imgstyle_a['margin-top'],
							'margin_bottom' => $imgstyle_a['margin-bottom'],
							'tagid' 		=> 'i-' . $xid . '-' . wppa( 'mocc' ),
							'cursor' 		=> $imgstyle_a['cursor'],
							'events' 		=> $imgevents,
							'title' 		=> $title
						) );
					}
					else {
						$result .= 	'<img' .
										' id="i-' . $xid . '-' . wppa( 'mocc' ) . '"' .
										( $title ? ' title="' . $title . '"' : '' ) .
										' src="' . $imgurl . '"' .
										' width="' . $imgstyle_a['width'] . '"' .
										' height="' . $imgstyle_a['height'] . '"' .
										' style="' . $imgstyle_a['style'] . $imgstyle_a['cursor'] . '"' .
										' ' . $imgevents .
										' ' . wppa_get_imgalt( $id ) .
										' />';
					}
				}
				else {
					$result .= $name;
				}
			$result .= "\n\t" . '</a>';
		}
		else { // Is an onclick unit
			$result .= "\n\t";
			if ( $display == 'thumbs' ) {
				if ( $is_video ) {
					$result .= wppa_get_video_html( array(
							'id'			=> $id,
							'width'			=> $imgstyle_a['width'],
							'height' 		=> $imgstyle_a['height'],
							'controls' 		=> false,
							'margin_top' 	=> $imgstyle_a['margin-top'],
							'margin_bottom' => $imgstyle_a['margin-bottom'],
							'tagid' 		=> 'i-' . $xid . '-' . wppa( 'mocc' ),
							'cursor' 		=> 'cursor:pointer;',
							'events' 		=> $imgevents,
							'title' 		=> $title,
							'onclick' 		=> $link['url']
						) );
				}
				else {
					$result .= 	'<img' .
									' id="i-' . $xid . '-' . wppa( 'mocc' ) . '"' .
									( $title ? ' title="' . $title . '"' : '' ) .
									' src="' . $imgurl . '"' .
									' width="' . $imgstyle_a['width'] . '"' .
									' height="' . $imgstyle_a['height'] . '"' .
									' style="' . $imgstyle_a['style'] . ' cursor:pointer;"' .
									' ' . $imgevents .
									' onclick="' . $link['url'] . '"' .
									' ' . wppa_get_imgalt( $id ) .
									' />';
				}
			}
			else {
				$result .= 	'<a' .
								' style="cursor:pointer;"' .
								' onclick="' . $link['url'] . '"' .
								' >' . $name .
							'</a>';

			}
		}
	}
	else {	// No link
		$result .= "\n\t";
		if ( $display == 'thumbs' ) {
			if ( $is_video ) {
				$result .= wppa_get_video_html( array(
						'id'			=> $id,
						'width'			=> $imgstyle_a['width'],
						'height' 		=> $imgstyle_a['height'],
						'controls' 		=> false,
						'margin_top' 	=> $imgstyle_a['margin-top'],
						'margin_bottom' => $imgstyle_a['margin-bottom'],
						'tagid' 		=> 'i-' . $xid . '-' . wppa( 'mocc' ),
						'cursor' 		=> 'cursor:pointer;',
						'events' 		=> $imgevents,
						'title' 		=> $title
					) );
			}
			else {
				$result .= 	'<img' .
								' id="i-' . $xid . '-' . wppa( 'mocc' ) . '"' .
								( $title ? ' title="' . $title . '"' : '' ) .
								' src="' . $imgurl . '"' .
								' width="' . $imgstyle_a['width'] . '"' .
								' height="' . $imgstyle_a['height'] . '"' .
								' style="' . $imgstyle_a['style'] . '"' .
								' ' . $imgevents .
								' ' . wppa_get_imgalt( $id ) .
								' />';
			}
		}
		else {
			$result .= $name;
		}
	}

	// The medals if at the bottom
	if ( $display == 'thumbs' ) {
		$result .= $id ? wppa_get_medal_html_a( array( 'id' => $id, 'size' => 'S', 'where' => 'bot' ) ) : '';
	}

	// Close container
	if ( $display == 'thumbs' ) {
		$result .= '</div>';
	}

	return $result;
}

// The filmstrip thumbnail image
// $idx = index in filmstrip
// $do_for_feed = bool, true if for feed
// $glue = bool, Set to true only at thumbs where the right border should be the glue line.
function wppa_do_filmthumb( $id, $idx, $do_for_feed = false, $glue = false ) {

	$thumb 		= wppa_cache_thumb( $id );

	$result 	= '';
	$src 		= wppa_fix_poster_ext( wppa_get_thumb_path( $thumb['id'] ), $thumb['id'] );
	$max_size 	= wppa_opt( 'film_thumbsize' );
	if ( wppa_in_widget() ) $max_size /= 2;
	$com_alt 	= wppa( 'is_comten' ) && wppa_switch( 'comten_alt_display' ) && ! wppa_in_widget();

	$imgattr_a 	= wppa_get_imgstyle_a( $thumb['id'], $src, $max_size, 'optional', 'fthumb' );
	$imgstyle  	= $imgattr_a['style'];
	$imgwidth  	= $imgattr_a['width'];
	$imgheight 	= $imgattr_a['height'];
	$cursor    	= $imgattr_a['cursor'];

	$url 		= wppa_fix_poster_ext( wppa_get_thumb_url( $thumb['id'], '', $imgwidth, $imgheight ), $thumb['id'] );
	$furl 		= str_replace( '/thumbs', '', $url );
	$events 	= wppa_get_imgevents( 'film', $thumb['id'], 'nopopup', $idx );
	$thumbname 	= wppa_get_photo_name( $thumb['id'] );
	$target 	= wppa_switch( 'film_blank' ) || ( $thumb['linktarget'] == '_blank' ) ? 'target="_blank" ' : '';
	$psotitle 	= $thumb['linktitle'] ? 'title="'.esc_attr($thumb['linktitle']).'" ' : '';
	$psourl 	= wppa_switch( 'film_overrule' ) && $thumb['linkurl'] ? 'href="'.$thumb['linkurl'].'" '.$target.$psotitle : '';
	$imgalt	 	= wppa_get_imgalt( $thumb['id'] );

	if ( wppa_opt( 'film_linktype' ) == 'lightbox' ) {
//		$title = esc_attr( wppa_zoom_in( $thumb['id'] ) );
	}
	else {
		$events .= ' onclick="wppaGotoKeepState( '.wppa( 'mocc' ).', '.$idx.' )"';
		$events .= ' ondblclick="wppaStartStop( '.wppa( 'mocc' ).', -1 )"';
	}

	if ( is_feed() ) {
		if ( $do_for_feed ) {
			$style_a = wppa_get_imgstyle_a( $thumb['id'], $src, '100', '4', 'thumb' );
			$style = $style_a['style'];
			$result .= 	'<a href="'.get_permalink().'">' .
							'<img' .
								' src="'.$url.'"' .
								' ' . $imgalt .
								' title="'.$thumbname.'"' .
								' style="'.$style.'"' .
							' />' .
						'</a>';
		}
	} else {

		// If ! $do_for_feed: pre-or post-ambule. To avoid dup id change it in that case
		$tmp = $do_for_feed ? 'film' : 'pre';
		$style = $glue ? 'style="'.wppa_get_thumb_frame_style( $glue, 'film' ).'"' : 'style="'.wppa_get_thumb_frame_style( false, 'film' ).'"';
		$result .= 	'<div' .
						' id="'.$tmp.'_wppatnf_'.wppa_encrypt_photo($thumb['id']).'_'.wppa( 'mocc' ).'"' .
						' class="thumbnail-frame"' .
						' ' . $style .
						' >';

		if ( $psourl ) {	// True only when pso activated and data present
			$result .= '<a '. $psourl . '>';	// $psourl contains url, target and title
		}
		elseif ( wppa_opt( 'film_linktype' ) == 'lightbox' && $tmp == 'film' ) {
			$title 		= wppa_get_lbtitle( 'slide', $thumb['id'] );
			$videohtml 	= esc_attr( wppa_get_video_body( $thumb['id'] ) );
			$audiohtml 	= esc_attr( wppa_get_audio_body( $thumb['id'] ) );
			$result .= 	'<a href="' . $furl . '"' .
							( $videohtml ? ' data-videohtml="' . $videohtml . '"' .
							' data-videonatwidth="' . wppa_get_videox( $thumb['id'] ) . '"' .
							' data-videonatheight="' . wppa_get_videoy( $thumb['id'] ) . '"' : '' ) .
							( $audiohtml ? ' data-audiohtml="' . $audiohtml . '"' : '' ) .
							' ' . wppa( 'rel' ) . '="' . wppa_opt( 'lightbox_name' ) . '[occ'.wppa( 'mocc' ) . ']"' .
							( $title ? ' ' . wppa( 'lbtitle' ) . '="' . $title . '"' : '' ) .
							' >';
		}

			if ( $tmp == 'pre' && wppa_opt( 'film_linktype' ) == 'lightbox' ) $cursor = 'cursor:default;';
			if ( $tmp == 'film' && ! $com_alt && ! wppa_cdn( 'front' ) && ! wppa_switch( 'lazy_or_htmlcomp' ) ) $result .= '<!--';
				if ( wppa_is_video( $thumb['id'] ) ) {
					$result .= wppa_get_video_html( array( 	'id' 			=> $thumb['id'],
																	'width' 		=> $imgattr_a['width'],
																	'height' 		=> $imgattr_a['height'],
																	'controls' 		=> false,
																	'margin_top' 	=> $imgattr_a['margin-top'],
																	'margin_bottom' => $imgattr_a['margin-bottom'],
																	'cursor' 		=> $imgattr_a['cursor'],
																	'events' 		=> $events,
																	'tagid' 		=> 'wppa-'.$tmp.'-'.$idx.'-'.wppa( 'mocc' )
																 )
														 );
				}
				else {
					$result .=  '<img' .
									' id="wppa-' . $tmp . '-' . $idx . '-' . wppa( 'mocc' ) . '"' .
									' class="wppa-'.$tmp.'-'.wppa( 'mocc' ).'"' .
									' src="' . $url . '"' .
									' ' . $imgalt .
									' style="' . $imgstyle . $cursor . '"' .
									' ' . $events .
									' data-title="' . ( $psourl ? esc_attr( $thumb['linktitle'] ) : '' ) . '"' .
									' />';
				}
			if ( $tmp == 'film' && ! $com_alt && ! wppa_cdn( 'front' ) && ! wppa_switch( 'lazy_or_htmlcomp' ) ) $result .= '-->';

		if ( $psourl ) {	// True only when pso activated and data present
			$result .= '</a>';	// $psourl contains url, target and title
		}
		elseif ( wppa_opt( 'film_linktype' ) == 'lightbox' && $tmp == 'film' ) {
			$result .= '</a>';
		}

		$result .= '</div>';
	}

	wppa_out( $result );
}

// The medals
function wppa_get_medal_html_a( $args ) {

	// Completize args
	$args = wp_parse_args( (array) $args, array(
											'id' 	=> '0',
											'size' 	=> 'M',
											'where' => ''
											) );

	// Validate args
	if ( $args['id'] == '0' ) return '';													// Missing required id
	if ( ! in_array( $args['size'], array( 'S', 'M', 'L', 'XL' ) ) ) return ''; 			// Missing or not implemented size spec
	if ( ! in_array( $args['where'], array( 'top', 'bot' ) ) ) return ''; 					// Missing or not implemented where

	// Do it here?
	if ( strpos( wppa_opt( 'medal_position' ), $args['where'] ) === false ) return ''; // No

	// Get rquired photo and config data
	$id 	= $args['id'];
	$new 	= wppa_is_photo_new( $id );
	$mod 	= wppa_is_photo_modified( $id );
	$status	= wppa_get_photo_item( $id, 'status' );
	$medal 	= in_array ( $status, array( 'gold', 'silver',  'bronze' ) ) ? $status : '';

	// Have a medal to show?
	if ( ! $new && ! $medal && ! $mod ) {
		return '';																			// No
	}

	// Init local vars
	$result = '';
	$color 	= wppa_opt( 'medal_color' );
	$left 	= strpos( wppa_opt( 'medal_position' ), 'left' ) !== false;
	$ctop 	= strpos( wppa_opt( 'medal_position' ), 'top' ) === false ? '-32' : '0';
	$sizes 	= array(
		'S' 	=> '16',
		'M' 	=> '20',
		'L' 	=> '24',
		'XL' 	=> '32'
		);
	$nsizes 	= array(
		'S' 	=> '14',
		'M' 	=> '16',
		'L' 	=> '20',
		'XL' 	=> '24'
		);
	$fsizes 	= array(
		'S' 	=> '9',
		'M' 	=> '10',
		'L' 	=> '14',
		'XL' 	=> '20'
		);
	$smargs = array(
		'S' 	=> '4',
		'M' 	=> '5',
		'L' 	=> '6',
		'XL' 	=> '8'
		);
	$lmargs = array(
		'S' 	=> '22',
		'M' 	=> '28',
		'L' 	=> '36',
		'XL' 	=> '48'
		);
	$tops = array(
		'S' 	=> '8',
		'M' 	=> '8',
		'L' 	=> '6',
		'XL' 	=> '0'
		);
	$ntops = array(
		'S' 	=> '10',
		'M' 	=> '10',
		'L' 	=> '8',
		'XL' 	=> '0'
		);
	$titles = array(
		'gold' 		=> __('Gold medal', 'wp-photo-album-plus'),
		'silver' 	=> __('Silver medal', 'wp-photo-album-plus'),
		'bronze' 	=> __('Bronze medal', 'wp-photo-album-plus'),
		);
	$size 	= $sizes[$args['size']];
	$nsize 	= $nsizes[$args['size']];
	$fsize 	= $fsizes[$args['size']];
	$smarg  = $smargs[$args['size']];
	$lmarg  = $lmargs[$args['size']];
	$top 	= $tops[$args['size']];
	$ntop 	= $ntops[$args['size']];
	$title 	= $medal ? esc_attr( $titles[$medal] ) : '';
	$sstyle = $left ? 'left:'.$smarg.'px;' : 'right:'.$smarg.'px;';
	$lstyle = $left ? 'left:'.$lmarg.'px;' : 'right:'.$lmarg.'px;';

	// The medal container
	$result .= '<div style="position:relative;top:'.$ctop.'px;z-index:10;">';

	// The medal
	if ( $medal ) $result .= 	'<img' .
									' src="' . WPPA_URL . '/images/medal_' . $medal . '_' . $color .'.png"' .
									' title="' . $title . '"' .
									' alt="' . $title . '"' .
									' style="' . $sstyle .
										'top:4px;' .
										'position:absolute;' .
										'border:none;' .
										'margin:0;' .
										'padding:0;' .
										'box-shadow:none;' .
										'height:'  .$size . 'px;' .
										'top:' . $top . 'px;' .
										'"' .
								' />';

	// Is there a new or modified indicator to display?
	if ( $new ) {
		$type = 'new';
	}
	elseif ( $mod ) {
		$type = 'mod';
	}
	else {
		$type = '';
	}

	// Style adjustment if only a new/modified without a real medal
	if ( ! $medal ) {
		$lstyle = $sstyle;
	}

	$do_image = ! wppa_switch( 'wppa_new_mod_label_is_text' );

	// Yes there is one to display
	if ( $type ) {
		if ( $do_image ) {
			$result .= 	'<img' .
							' src="' . wppa_opt($type.'_label_url') . '"' .
							' title="' . esc_attr( __('New', 'wp-photo-album-plus') ) . '"' .
							' alt="' . esc_attr( __('New', 'wp-photo-album-plus') ) . '"' .
							' class="wppa-thumbnew"' .
							' style="' . $lstyle .
								'top:' . $ntop . 'px;' .
								'position:absolute;' .
								'border:none;' .
								'margin:0;' .
								'padding:0;' .
								'box-shadow:none;' .
								'height:' . $nsize . 'px;' .
								'"'.
						' />';
		}
		else {
			$result .= 	'<div' .
							' style="' . $lstyle .
								'position:absolute;' .
								'top:' . $ntop . 'px;' .
								'box-sizing:border-box;' .
								'float:' . ( $left ? 'left;' : 'right;' ) .
								'font-size:' . $fsize . 'px;' .
								'line-height:' . $fsize . 'px;' .
								'font-family:\'Arial Black\', Gadget, sans-serif;' .
								'border-radius:2px;' .
								'border-width:1px;' .
								'border-style:solid;' .
								'padding:1px;' .
								wppa_get_text_medal_color_style( $type ) .
								'"' .
							' >' .
							'&nbsp;' . __( wppa_opt( $type.'_label_text' ) ) . '&nbsp;' .
						'</div>';
		}
	}

	// Close container
	$result .= '</div>';

	return $result;
}