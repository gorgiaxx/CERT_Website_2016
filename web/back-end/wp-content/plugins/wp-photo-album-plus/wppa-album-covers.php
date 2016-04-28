<?php
/* wppa-album-covers.php
* Package: wp-photo-album-plus
*
* Functions for album covers
* Version 6.4.07
*
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

// Main entry for an album cover
// decide wich cover type and call the types function
function wppa_album_cover( $id ) {

	// Find the album specific cover type
	$cover_type = wppa_get_album_item( $id, 'cover_type' );

	// No type specified (0), use default
	if ( ! $cover_type ) {
		$cover_type = wppa_opt( 'cover_type' );
	}

	// Find the cover photo position
	wppa( 'coverphoto_pos', wppa_opt( 'coverphoto_pos' ) );

	// Assume multicolumn responsive
	$is_mcr = true;

	// Dispatch on covertype
	switch ( $cover_type ) {
		case 'default':
			$is_mcr = false;
		case 'default-mcr':
			wppa_album_cover_default( $id, $is_mcr );
			break;
		case 'imagefactory':
			$is_mcr = false;
		case 'imagefactory-mcr':
			if ( wppa( 'coverphoto_pos' ) == 'left' ) {
				wppa( 'coverphoto_pos', 'top' );
			}
			if ( wppa( 'coverphoto_pos' ) == 'right' ) {
				wppa( 'coverphoto_pos', 'bottom' );
			}
			wppa_album_cover_imagefactory( $id, $is_mcr );
			break;
		case 'longdesc':
			$is_mcr = false;
		case 'longdesc-mcr':
			if ( wppa( 'coverphoto_pos' ) == 'top' ) {
				wppa( 'coverphoto_pos', 'left' );
			}
			if ( wppa( 'coverphoto_pos' ) == 'bottom' ) {
				wppa( 'coverphoto_pos', 'right' );
			}
			wppa_album_cover_longdesc( $id, $is_mcr );
			break;
		default:
			$err = 'Unimplemented covertype: ' . $cover_type;
			wppa_dbg_msg( $err );
			wppa_log( 'Err', $err );
	}
}

// The default cover type
function wppa_album_cover_default( $albumid, $multicolresp = false ) {
global $cover_count_key;
global $wpdb;

	// Init
	$album 	= wppa_cache_album( $albumid );
	$alt 	= wppa( 'alt' );

	// Multi column responsive?
	if ( $multicolresp ) $mcr = 'mcr-'; else $mcr = '';

	// Find album details
	$coverphoto = wppa_get_coverphoto_id( $albumid );
//	$query 		= $wpdb->prepare( 	"SELECT * " .
//									"FROM `" . WPPA_PHOTOS . "` " .
//									"WHERE `id` = %s",
//									$coverphoto
//								);
	$image 		= wppa_cache_thumb( $coverphoto ); //$wpdb->get_row( $query, ARRAY_A );
	$photocount = wppa_get_photo_count( $albumid, 'use_treecounts' );
	$albumcount = wppa_get_album_count( $albumid, 'use_treecounts' );
	$mincount 	= wppa_get_mincount();

	// Init links
	$title 				= '';
	$linkpage 			= '';
	$href_title 		= '';
	$href_slideshow 	= '';
	$href_content 		= '';
	$onclick_title 		= '';
	$onclick_slideshow 	= '';
	$onclick_content 	= '';

	// See if there is substantial content to the album
	$has_content = ( $albumcount > '0' ) || ( $photocount > $mincount );

	// What is the albums title linktype
	$linktype = $album['cover_linktype'];

	// If not specified, use default
	if ( ! $linktype ) {
		$linktype = 'content';
	}

	// What is the albums title linkpage
	$linkpage = $album['cover_linkpage'];

	// Fix backward compatibility issue
	if ( $linkpage == '-1' ) {
		$linktype = 'none';
	}

	// Find the cover title href, onclick and title
	$title_attr 	= wppa_get_album_title_attr_a( 	$albumid,
													$linktype,
													$linkpage,
													$has_content,
													$coverphoto,
													$photocount
												);
	$href_title 	= $title_attr['href'];
	$onclick_title 	= $title_attr['onclick'];
	$title 			= $title_attr['title'];

	// Find the slideshow link and onclick
	$href_slideshow = wppa_convert_to_pretty( wppa_encrypt_url( wppa_get_slideshow_url( $albumid, $linkpage ) ) );
	if ( wppa_switch( 'allow_ajax' ) && ! $linkpage ) {
		$onclick_slideshow = "wppaDoAjaxRender( " . wppa( 'mocc' ) . ", '" .
			wppa_encrypt_url( wppa_get_slideshow_url_ajax( $albumid, $linkpage ) ) . "', '" .
			wppa_convert_to_pretty( $href_slideshow ) . "' )";
		$href_slideshow = "#";
	}

	// Find the content 'View' link
	$href_content = wppa_convert_to_pretty( wppa_encrypt_url( wppa_get_album_url( $albumid, $linkpage ) ) );
	if ( wppa_switch( 'allow_ajax' ) && ! $linkpage ) {
		$onclick_content = "wppaDoAjaxRender( " . wppa( 'mocc' ) . ", '" .
			wppa_encrypt_url( wppa_get_album_url_ajax( $albumid, $linkpage ) ) . "', '" .
			wppa_convert_to_pretty( $href_content ) . "' )";
		$href_content = "#";
	}

	// Find the coverphoto link
	if ( $coverphoto ) {
		$photolink = wppa_get_imglnk_a( 	'coverimg',
											$coverphoto,
											$href_title,
											$title,
											$onclick_title,
											'',
											$albumid
										);
	}
	else {
		$photolink = false;
	}

	// Find the coverphoto details
	if ( $coverphoto ) {
		$path 		= wppa_get_thumb_path( 	$coverphoto );
		$imgattr_a 	= wppa_get_imgstyle_a( 	$coverphoto,
											$path,
											wppa_opt( 'smallsize' ),
											'',
											'cover'
										);
		$src 		= wppa_get_thumb_url( 	$coverphoto,
											'',
											$imgattr_a['width'],
											$imgattr_a['height']
										);
	}

	// No coverphoto
	else {
		$path 		= '';
		$imgattr_a 	= false;
		$src 		= '';
	}

	// Feed?
	if ( is_feed() ) {
		$events  	= '';
	}
	else {
		$events 	= wppa_get_imgevents( 'cover' );
	}

	// Is cover a-symmetric ?
	$photo_pos = wppa( 'coverphoto_pos' );
	if ( $photo_pos == 'left' || $photo_pos == 'right' ) {
		$class_asym = 'wppa-asym-text-frame-' . $mcr . wppa( 'mocc' );
	}
	else {
		$class_asym = '';
	}

	// Set up album cover style
	$style =  __wcs( 'wppa-box' ) . __wcs( 'wppa-' . wppa( 'alt' ) );
	if ( is_feed() ) {
		$style .= ' padding:7px;';
	}
	$wid = wppa_get_cover_width( 'cover' );
	$style .= 'width: ' . $wid . 'px;';
	if ( $cover_count_key == 'm' ) {
		$style .= 'margin-left: 8px;';
	}
	elseif ( $cover_count_key == 'r' ) {
		$style .= 'float:right;';
	}
	else {
		$style .= 'clear:both;';
	}

	// keep track of position
	wppa_step_covercount( 'cover' );

	// Open the album box
	wppa_out( 	'<div' .
					' id="album-' . $albumid . '-' . wppa( 'mocc' ) . '"' .
					' class="' .
						'wppa-album-cover-standard ' .
						'album ' .
						'wppa-box ' .
						'wppa-cover-box ' .
						'wppa-cover-box-' . $mcr . wppa( 'mocc' ) . ' ' .
						'wppa-' . wppa( 'alt' ) .
						'"' .
					' style="' . $style . __wcs( 'wppa-cover-box' ) . '"' .
					' >'
	);

	// First The Cover photo?
	if ( $photo_pos == 'left' || $photo_pos == 'top' ) {
		wppa_the_coverphoto( 	$albumid,
								$image,
								$src,
								$photo_pos,
								$photolink,
								$title,
								$imgattr_a,
								$events
							);
	}

	// Open the Cover text frame
	$textframestyle = wppa_get_text_frame_style( $photo_pos, 'cover' );
	wppa_out( 	'<div' .
					' id="covertext_frame_' . $albumid . '_' . wppa( 'mocc' ) . '"' .
					' class="' .
						'wppa-text-frame-' . wppa( 'mocc' ) . ' ' .
						'wppa-text-frame ' .
						'wppa-cover-text-frame ' .
						$class_asym . '"' .
						' ' . $textframestyle .
					' >'
	);

	// The Album title
	$target = wppa_switch( 'allow_ajax' ) ? '_self' : $photolink['target'];
	wppa_the_album_title( 	$albumid,
							$href_title,
							$onclick_title,
							$title,
							$target
						);

	// The Album description
	if ( wppa_switch( 'show_cover_text' ) ) {
		if ( wppa_opt( 'text_frame_height' ) > '0' ) {
			$textheight = 'min-height:' . wppa_opt( 'text_frame_height' ) . 'px;';
		}
		else {
			$txtheight = '';
		}
		wppa_out( 	'<p' .
						' class="wppa-box-text wppa-black wppa-box-text-desc"' .
						' style="' .
							$textheight .
							__wcs( 'wppa-box-text' ) .
							__wcs( 'wppa-black' ) .
							'"' .
						' >' .
						wppa_get_album_desc( $albumid ) .
					'</p>'
				);
	}

	// Close the Cover text frame
	if ( $photo_pos == 'left' ) {
		wppa_out( 	'</div>' .
					'<div style="clear:both;" ></div>'
				);
	}

	// The 'Slideshow'/'Browse' link
	wppa_the_slideshow_browse_link( $photocount,
									$href_slideshow,
									$onclick_slideshow,
									$target
								);

	// The 'View' link
	wppa_album_cover_view_link( 	$albumid,
									$has_content,
									$photocount,
									$albumcount,
									$mincount,
									$href_content,
									$target,
									$onclick_content
								);

	// Close the Cover text frame
	if ( $photo_pos != 'left' ) {
		wppa_out( '</div>' );
	}

	// The Cover photo last?
	if ( $photo_pos == 'right' || $photo_pos == 'bottom' ) {
		wppa_the_coverphoto( 	$albumid,
								$image,
								$src,
								$photo_pos,
								$photolink,
								$title,
								$imgattr_a,
								$events
							);
	}

	// The sublinks
	wppa_albumcover_sublinks( 	$albumid,
								wppa_get_cover_width( 'cover' ),
								$multicolresp
							);

	// Prepare for closing
	wppa_out( '<div style="clear:both;"></div>' );

	// Close the album box
	wppa_out( '</div>' );

	// Toggel alt/even
	wppa_toggle_alt();
}

// Type Image Factory
function wppa_album_cover_imagefactory( $albumid, $multicolresp = false ) {
global $cover_count_key;
global $wpdb;

	// Init
	$album = wppa_cache_album( $albumid );

	// Multi column responsive?
	if ( $multicolresp ) $mcr = 'mcr-'; else $mcr = '';

	$photo_pos 		= wppa( 'coverphoto_pos' );
	$cpcount 		= $album['main_photo'] > '0' ? '1' : wppa_opt( 'imgfact_count' );
	$coverphotos 	= wppa_get_coverphoto_ids( $albumid, $cpcount );

	$images 	= array();
	$srcs 		= array();
	$paths 		= array();
	$imgattrs_a = array();
	$photolinks = array();

	if ( is_feed() ) {
		$events = '';
	}
	else {
		$events = wppa_get_imgevents( 'cover' );
	}

	if ( ! empty( $coverphotos ) ) {
		$coverphoto = $coverphotos['0'];
	}
	else {
		$coverphoto = false;
	}

	$photocount = wppa_get_photo_count( $albumid, 'use_treecounts' );
	$albumcount = wppa_get_album_count( $albumid, 'use_treecounts' );
	$mincount 	= wppa_get_mincount();
	$title 		= '';
	$linkpage 	= '';

	$href_title 		= '';
	$href_slideshow 	= '';
	$href_content 		= '';
	$onclick_title 		= '';
	$onclick_slideshow 	= '';
	$onclick_content 	= '';

	// See if there is substantial content to the album
	$has_content = ( $albumcount > '0' ) || ( $photocount > $mincount );

	// If not specified, use default
	$linktype = $album['cover_linktype'];
	if ( ! $linktype ) {
		$linktype = 'content';
	}

	// Fix backward compatibility issue
	$linkpage = $album['cover_linkpage'];
	if ( $linkpage == '-1' ) {
		$linktype = 'none';
	}

	// Find the cover title href, onclick and title
	$title_attr 	= wppa_get_album_title_attr_a( 	$albumid,
													$linktype,
													$linkpage,
													$has_content,
													$coverphoto,
													$photocount
												);
	$href_title 	= $title_attr['href'];
	$onclick_title 	= $title_attr['onclick'];
	$title 			= $title_attr['title'];

	// Find the coverphotos details
	foreach ( $coverphotos as $coverphoto ) {
//		$query 			= $wpdb->prepare( 	"SELECT * " .
//											"FROM `" . WPPA_PHOTOS . "` " .
//											"WHERE `id` = %s",
//											$coverphoto
//										);
		$images[] 		= wppa_cache_thumb( $coverphoto ); //$wpdb->get_row( $query, ARRAY_A );
		$path 			= wppa_get_thumb_path( 	$coverphoto	 );
		$paths[] 		= $path;
		$cpsize 		= count( $coverphotos ) == '1' ?
							wppa_opt( 'smallsize' ) :
							wppa_opt( 'smallsize_multi' );
		$imgattr_a		= wppa_get_imgstyle_a( 	$coverphoto,
												$path,
												$cpsize,
												'',
												'cover'
											);
		$imgattrs_a[] 	= $imgattr_a;
		$srcs[] 		= wppa_get_thumb_url( 	$coverphoto,
												'',
												$imgattr_a['width'],
												$imgattr_a['height']
											);
		$photolinks[] 	= wppa_get_imglnk_a( 	'coverimg',
												$coverphoto,
												$href_title,
												$title,
												$onclick_title,
												'',
												$albumid
											);
	}

	// Find the slideshow link and onclick
	$href_slideshow = wppa_convert_to_pretty( wppa_encrypt_url( wppa_get_slideshow_url( $albumid, $linkpage ) ) );
	if ( wppa_switch( 'allow_ajax' ) && ! $linkpage ) {
		$onclick_slideshow = "wppaDoAjaxRender( " . wppa( 'mocc' ) . ", '" .
			wppa_encrypt_url( wppa_get_slideshow_url_ajax( $albumid, $linkpage ) ) . "', '" .
			wppa_convert_to_pretty( $href_slideshow ) . "' )";
		$href_slideshow = "#";
	}

	// Find the content 'View' link
	$href_content = wppa_convert_to_pretty( wppa_encrypt_url( wppa_get_album_url( $albumid, $linkpage ) ) );
	if ( wppa_switch( 'allow_ajax' ) && ! $linkpage ) {
		$onclick_content = "wppaDoAjaxRender( " . wppa( 'mocc' ) . ", '" .
			wppa_encrypt_url( wppa_get_album_url_ajax( $albumid, $linkpage ) ) . "', '" .
			wppa_convert_to_pretty( $href_content ) . "' )";
		$href_content = "#";
	}

	$style =  __wcs( 'wppa-box' ) . __wcs( 'wppa-' . wppa( 'alt' ) );
	if ( is_feed() ) $style .= ' padding:7px;';

	$wid = wppa_get_cover_width( 'cover' );
	$style .= 'width: ' . $wid . 'px;';
	if ( $cover_count_key == 'm' ) {
		$style .= 'margin-left: 8px;';
	}
	elseif ( $cover_count_key == 'r' ) {
		$style .= 'float:right;';
	}
	else {
		$style .= 'clear:both;';
	}
	wppa_step_covercount( 'cover' );

	$pl = isset( $photolinks['0']['target'] ) ? $photolinks['0']['target'] : '_self';
	$target = wppa_switch( 'allow_ajax' ) ? '_self' : $pl;

	// Open the album box
	wppa_out( 	'<div' .
					' id="album-' . $albumid . '-' . wppa( 'mocc' ) . '"' .
					' class="' .
						'wppa-album-cover-imagefactory ' .
						'album ' .
						'wppa-box ' .
						'wppa-cover-box ' .
						'wppa-cover-box-' . $mcr . wppa( 'mocc' ) . ' ' .
						'wppa-' . wppa( 'alt' ) .
						'"' .
					' style="' . $style . __wcs( 'wppa-cover-box' ) . '"' .
					' >'
			);

	// First The Cover photo?
	if ( $photo_pos == 'left' || $photo_pos == 'top' ) {
		wppa_the_coverphotos(
			$albumid, $images, $srcs, $photo_pos, $photolinks, $title, $imgattrs_a, $events );
	}

	// Open the Cover text frame
	$textframestyle = 'style="text-align:center;'.__wcs( 'wppa-cover-text-frame' ).'"';
	wppa_out( 	'<div' .
					' id="covertext_frame_' . $albumid . '_' . wppa( 'mocc' ) . '"' .
					' class="' .
						'wppa-text-frame-' . wppa( 'mocc' ) . ' ' .
						'wppa-text-frame ' .
						'wppa-cover-text-frame' .
						'"' .
					' ' . $textframestyle .
					' >'
			);

	// The Album title
	wppa_the_album_title( $albumid, $href_title, $onclick_title, $title, $target );

	// The Album description
	if ( wppa_switch( 'show_cover_text' ) ) {
		$textheight = wppa_opt( 'text_frame_height' ) > '0' ?
		'min-height:' . wppa_opt( 'text_frame_height' ) . 'px; ' :
		'';
		wppa_out( 	'<p' .
						' class="wppa-box-text wppa-black wppa-box-text-desc"' .
						' style="' .
							$textheight .
							__wcs( 'wppa-box-text' ) .
							__wcs( 'wppa-black' ) .
							'"' .
						' >' .
						wppa_get_album_desc( $albumid ) .
					'</p>'
				);
	}

	// The 'Slideshow'/'Browse' link
	wppa_the_slideshow_browse_link( $photocount,
									$href_slideshow,
									$onclick_slideshow,
									$target
								);

	// The 'View' link
	wppa_album_cover_view_link( 	$albumid,
									$has_content,
									$photocount,
									$albumcount,
									$mincount,
									$href_content,
									$target,
									$onclick_content
								);

	// Close the Cover text frame
	wppa_out( '</div>' );

	// The Cover photo last?
	if ( $photo_pos == 'right' || $photo_pos == 'bottom' ) {
		wppa_the_coverphotos( 	$albumid,
								$images,
								$srcs,
								$photo_pos,
								$photolinks,
								$title,
								$imgattrs_a,
								$events
							);
	}

	// The sublinks
	wppa_albumcover_sublinks( 	$albumid,
								wppa_get_cover_width( 'cover' ),
								$multicolresp
							);

	// Prepare for closing
	wppa_out( '<div style="clear:both;"></div>' );

	// Close the album box
	wppa_out( '</div>' );

	// Toggle alt/even
	wppa_toggle_alt();
}

// Type Long Description
function wppa_album_cover_longdesc( $albumid, $multicolresp = false ) {
global $cover_count_key;
global $wpdb;

	$album = wppa_cache_album( $albumid );

	if ( $multicolresp ) $mcr = 'mcr-'; else $mcr = '';

	$coverphoto = wppa_get_coverphoto_id( $albumid );
	$image 		= wppa_cache_thumb( $coverphoto ); //$wpdb->get_row( $wpdb->prepare(
//					"SELECT * FROM `" . WPPA_PHOTOS . "` WHERE `id` = %s", $coverphoto
//					), ARRAY_A );
	$photocount = wppa_get_photo_count( $albumid, true );
	$albumcount = wppa_get_album_count( $albumid, true );
	$mincount 	= wppa_get_mincount();
	$title 		= '';
	$linkpage 	= '';

	$href_title 		= '';
	$href_slideshow 	= '';
	$href_content 		= '';
	$onclick_title 		= '';
	$onclick_slideshow 	= '';
	$onclick_content 	= '';

	// See if there is substantial content to the album
	$has_content = ( $albumcount > '0' ) || ( $photocount > $mincount );

	// What is the albums title linktype
	$linktype = $album['cover_linktype'];
	if ( !$linktype ) $linktype = 'content'; // Default

	// What is the albums title linkpage
	$linkpage = $album['cover_linkpage'];
	if ( $linkpage == '-1' ) $linktype = 'none'; // for backward compatibility

	// Find the cover title href, onclick and title
	$title_attr 	= wppa_get_album_title_attr_a(
						$albumid, $linktype, $linkpage, $has_content, $coverphoto, $photocount );
	$href_title 	= $title_attr['href'];
	$onclick_title 	= $title_attr['onclick'];
	$title 			= $title_attr['title'];

	// Find the slideshow link and onclick
	$href_slideshow = wppa_convert_to_pretty( wppa_encrypt_url( wppa_get_slideshow_url( $albumid, $linkpage ) ) );
	if ( wppa_switch( 'allow_ajax' ) && ! $linkpage ) {
		$onclick_slideshow = "wppaDoAjaxRender( " . wppa( 'mocc' ) . ", '" .
			wppa_encrypt_url( wppa_get_slideshow_url_ajax( $albumid, $linkpage ) ) . "', '" .
			wppa_convert_to_pretty( $href_slideshow ) . "' )";
		$href_slideshow = "#";
	}

	// Find the content 'View' link
	$href_content = wppa_convert_to_pretty( wppa_encrypt_url( wppa_get_album_url( $albumid, $linkpage ) ) );
	if ( wppa_switch( 'allow_ajax' ) && ! $linkpage ) {
		$onclick_content = "wppaDoAjaxRender( " . wppa( 'mocc' ) . ", '" .
			wppa_encrypt_url( wppa_get_album_url_ajax( $albumid, $linkpage ) ) . "', '" .
			wppa_convert_to_pretty( $href_content ) . "' )";
		$href_content = "#";
	}

	// Find the coverphoto link
	if ( $coverphoto ) {
		$photolink = wppa_get_imglnk_a(
			'coverimg', $coverphoto, $href_title, $title, $onclick_title, '', $albumid );
	}
	else $photolink = false;

	// Find the coverphoto details
	if ( $coverphoto ) {
		$path 		= wppa_get_thumb_path( $coverphoto );
		$imgattr_a 	= wppa_get_imgstyle_a(
							$coverphoto, $path, wppa_opt( 'smallsize' ), '', 'cover' );
		$src 		= wppa_get_thumb_url(
							$coverphoto, '', $imgattr_a['width'], $imgattr_a['height'] );
	}
	else {
		$path 		= '';
		$imgattr_a 	= false;
		$src 		= '';
	}

	// Feed?
	if ( is_feed() ) {
		$events = '';
	}
	else {
		$events = wppa_get_imgevents( 'cover' );
	}

	$photo_pos = wppa( 'coverphoto_pos' );

	$style =  __wcs( 'wppa-box' ) . __wcs( 'wppa-' . wppa( 'alt' ) );
	if ( is_feed() ) $style .= ' padding:7px;';

	$wid = wppa_get_cover_width( 'cover' );
	$style .= 'width: ' . $wid . 'px;';
	if ( $cover_count_key == 'm' ) {
		$style .= 'margin-left: 8px;';
	}
	elseif ( $cover_count_key == 'r' ) {
		$style .= 'float:right;';
	}
	else {
		$style .= 'clear:both;';
	}
	wppa_step_covercount( 'cover' );

	$target = wppa_switch( 'allow_ajax' ) ? '_self' : $photolink['target'];

	// Open the album box
	wppa_out( 	'<div' .
						' id="album-' . $albumid . '-' . wppa( 'mocc' ) . '"' .
						' class="' .
							'wppa-album-cover-longdesc ' .
							'album ' .
							'wppa-box ' .
							'wppa-cover-box ' .
							'wppa-cover-box-' . $mcr . wppa( 'mocc' ) . ' ' .
							'wppa-' . wppa( 'alt' ) .
							'"' .
						' style="' . $style . __wcs( 'wppa-cover-box' ) . '"' .
						' >'
			);

	// First The Cover photo?
	if ( $photo_pos == 'left' || $photo_pos == 'top' ) {
		wppa_the_coverphoto(
			$albumid, $image, $src, $photo_pos, $photolink, $title, $imgattr_a, $events );
	}

	// Open the Cover text frame
	$textframestyle = wppa_get_text_frame_style( $photo_pos, 'cover' );
	wppa_out( 	'<div' .
						' id="covertext_frame_' . $albumid . '_' . wppa( 'mocc' ) . '"' .
						' class="' .
							'wppa-text-frame-' . wppa( 'mocc' ) . ' ' .
							'wppa-text-frame ' .
							'wppa-cover-text-frame ' .
							'wppa-asym-text-frame-' . $mcr . wppa( 'mocc' ) .
							'"' .
							' ' . $textframestyle .
						'>'
			);

	// The Album title
	wppa_the_album_title( $albumid, $href_title, $onclick_title, $title, $target );

	// The 'Slideshow'/'Browse' link
	wppa_the_slideshow_browse_link( $photocount, $href_slideshow, $onclick_slideshow, $target );

	// The 'View' link
	wppa_album_cover_view_link( $albumid, $has_content, $photocount, $albumcount,
		$mincount, $href_content, $target, $onclick_content );

	// Close the Cover text frame
	wppa_out( '</div>' );

	// The Cover photo last?
	if ( $photo_pos == 'right' || $photo_pos == 'bottom' ) {
		wppa_the_coverphoto(
			$albumid, $image, $src, $photo_pos, $photolink, $title, $imgattr_a, $events );
	}

	// The Album description
	if ( wppa_switch( 'show_cover_text' ) ) {
		$textheight = wppa_opt( 'text_frame_height' ) > '0' ?
			'min-height:' . wppa_opt( 'text_frame_height' ) . 'px; ' :
			'';
		wppa_out( 	'<div' .
							' id="coverdesc_frame_' . $albumid . '_' . wppa( 'mocc' ) . '"' .
							' style="clear:both"' .
							' >' .
							'<p' .
								' class="wppa-box-text wppa-black wppa-box-text-desc"' .
								' style="' .
									$textheight .
									__wcs( 'wppa-box-text' ) .
									__wcs( 'wppa-black' ) .
									'"' .
								' >' .
								wppa_get_album_desc( $albumid ) .
							'</p>' .
						'</div>'
				);
	}

	// The sublinks
	wppa_albumcover_sublinks( $albumid, wppa_get_cover_width( 'cover' ), $multicolresp );

	// Prepare for closing
	wppa_out( '<div style="clear:both;"></div>' );

	// Close the album box
	wppa_out( '</div>' );

	// Toggle alt/even
	wppa_toggle_alt();
}

// A single coverphoto
// Output goes directly to wppa_out()
function wppa_the_coverphoto( $albumid, $image, $src, $photo_pos, $photolink, $title, $imgattr_a, $events ) {
global $wpdb;

	if ( ! $image ) {
		return;
	}

	if ( wppa_has_audio( $image['id'] ) ) {
		$src = wppa_fix_poster_ext( $src, $image['id'] );
	}

	$imgattr   = $imgattr_a['style'];
	$imgwidth  = $imgattr_a['width'];
	$imgheight = $imgattr_a['height'];
	$frmwidth  = $imgwidth + '10';	// + 2 * 1 border + 2 * 4 padding

	// Find the photo frame style
	if ( wppa_in_widget() ) {
		$photoframestyle = 'style="text-align:center; "';
	}
	else {
		switch ( $photo_pos ) {
			case 'left':
				$photoframestyle =
					'style="float:left; margin-right:5px;width:' . $frmwidth . 'px;"';
				break;
			case 'right':
				$photoframestyle =
					'style="float:right; margin-left:5px;width:' . $frmwidth . 'px;"';
				break;
			case 'top':
				$photoframestyle = 'style="text-align:center;"';
				break;
			case 'bottom':
				$photoframestyle = 'style="text-align:center;"';
				break;
			default:
				$photoframestyle = '';
				wppa_dbg_msg( 'Illegal $photo_pos in wppa_the_coverphoto' );
		}
	}

	// Open the coverphoto frame
	wppa_out( '<div' .
						' id="coverphoto_frame_' . $albumid . '_' . wppa( 'mocc' ) . '"' .
						' class="coverphoto-frame" ' .
						$photoframestyle .
						' >' );

	// The link from the coverphoto
	if ( $photolink ) {

		// If lightbox, we need all the album photos to set up a lightbox set
		if ( $photolink['is_lightbox'] ) {
			$thumbs = $wpdb->get_results( $wpdb->prepare(
				"SELECT * FROM `" . WPPA_PHOTOS . "` WHERE `album` = %s " .
				wppa_get_photo_order( $albumid ), $albumid
				), ARRAY_A );
			wppa_dbg_q( 'Q-CovPlB' );							// Report the query
			wppa_cache_thumb( 'add', $thumbs );				// Save rsult in 2nd level cache

			if ( $thumbs ) foreach ( $thumbs as $thumb ) {
				$id = $thumb['id'];
				$title = wppa_get_lbtitle( 'cover', $id );
				if ( wppa_is_video( $id ) ) {
					$siz['0'] = wppa_get_videox( $id );
					$siz['1'] = wppa_get_videoy( $id );
				}
				else {
					$siz['0'] = wppa_get_photox( $id );
					$siz['1'] = wppa_get_photoy( $id );
				}
				$link = wppa_get_photo_url( $id, '', $siz['0'], $siz['1'] );
				if ( wppa_has_audio( $id ) ) {
					$link = wppa_fix_poster_ext( $link, $id );
				}
				$is_video 	= wppa_is_video( $id );
				$has_audio 	= wppa_has_audio( $id );

				// Open the anchor tag for lightbox
				wppa_out( "\n\t" .
					'<a' .
					' href="' . $link . '"' .
					( $is_video ? ' data-videohtml="' . esc_attr( wppa_get_video_body( $id ) ) . '"' .
					' data-videonatwidth="'.wppa_get_videox( $id ).'"' .
					' data-videonatheight="'.wppa_get_videoy( $id ).'"' : '' ) .
					( $has_audio ? ' data-audiohtml="' . esc_attr( wppa_get_audio_body( $id ) ) . '"' : '' ) .
					' ' . wppa( 'rel' ) . '="' . wppa_opt( 'lightbox_name' ) . '[alw-' . wppa( 'mocc' ) . '-' . $albumid . ']"' .
					' ' . wppa( 'lbtitle' ) . '="' . $title . '"' .
					' >' );

				// the cover image
				if ( $id == $image['id'] ) {
					if ( wppa_is_video( $image['id'] ) ) {
						wppa_out(
							'<video preload="metadata" class="image wppa-img" id="i-' . $image['id'] . '-' .
							wppa( 'mocc' ) . '" title="' . wppa_zoom_in( $image['id'] ) .
							'" width="' . $imgwidth . '" height="' . $imgheight . '" style="' .
							__wcs( 'wppa-img' ) . $imgattr . $imgattr_a['cursor'] . '" ' .
							$events . ' >' .
							wppa_get_video_body( $image['id'] ) . '</video>'
						);
					}
					else {
						wppa_out(
							'<img class="image wppa-img" id="i-' . $image['id'] . '-' .
							wppa( 'mocc' ) . '" title="' . wppa_zoom_in( $image['id'] ) .
							'" src="' . $src . '" width="' . $imgwidth . '" height="' . $imgheight . '" style="' .
							__wcs( 'wppa-img' ) . $imgattr . $imgattr_a['cursor'] . '" ' .
							$events . ' ' . wppa_get_imgalt( $image['id'] ) . ' />'
						);
					}
				}

				// Close the lightbox anchor tag
				wppa_out( "\n\t" . '</a>' );
			}
		}

		// Link is NOT lightbox
		else {
			$href = $photolink['url'] == '#' ? '' : 'href="' . wppa_convert_to_pretty( $photolink['url'] ) . '" ';
			wppa_out( 	'<a' .
							' ' . $href .
							' target="' . $photolink['target'] . '"' .
							' title="' . $photolink['title'] . '"' .
							' onclick="' . $photolink['onclick'] . '"' .
							' >'
					);

			// A video?
			if ( wppa_is_video( $image['id'] ) ) {
				wppa_out(
					'<video preload="metadata" ' . $title . '" class="image wppa-img" width="' . $imgwidth . '" height="' .
					$imgheight . '" style="' . __wcs( 'wppa-img' ) . $imgattr . '" ' . $events . ' >' .
					wppa_get_video_body( $image['id'] ) . '</video>'
				);
			}

			// A photo
			else {
				wppa_out(
					'<img src="' . $src . '" ' . wppa_get_imgalt( $image['id'] ) . ' class="image wppa-img" width="' .
					$imgwidth . '" height="' . $imgheight . '" style="' . __wcs( 'wppa-img' ) .
					$imgattr . '" ' . $events . ' />'
				);
			}
			wppa_out( '</a>' );
		}
	}

	// No link on coverphoto
	else {

		// A video?
		if ( wppa_is_video( $image['id'] ) ) {
			wppa_out(
				'<video preload="metadata" class="image wppa-img" width="' . $imgwidth . '" height="' .
				$imgheight . '" style="' . __wcs( 'wppa-img' ) . $imgattr . '" ' . $events . ' >' .
				wppa_get_video_body( $image['id'] ) . '</video>'
			);
		}

		// A photo
		else {
			wppa_out(
				'<img src="' . $src . '" ' . wppa_get_imgalt( $image['id'] ) . ' class="image wppa-img" width="' .
				$imgwidth . '" height="' . $imgheight . '" style="' . __wcs( 'wppa-img' ) .
				$imgattr . '" ' . $events . ' />'
			);
		}
	}

	// Close the coverphoto frame
	wppa_out( '</div>' );
}

// Multiple coverphotos
// Output goes directly to wppa_out()
function wppa_the_coverphotos( $albumid, $images, $srcs, $photo_pos, $photolinks, $title, $imgattrs_a, $events ) {
global $wpdb;

	if ( ! $images ) {
		return;
	}

	// Find the photo frame style
	$photoframestyle = 'style="text-align:center; "';

	// Open the coverphoto frame
	wppa_out(
		'<div id="coverphoto_frame_' . $albumid . '_' . wppa( 'mocc' ) .
		'" class="coverphoto-frame" ' . $photoframestyle . '>'
		);

	// Process the images
	$n = count( $images );
	for ( $idx='0'; $idx < $n; $idx++ ) {
		$image 		= $images[$idx];
		$src 		= $srcs[$idx];

		if ( wppa_has_audio( $image['id'] ) ) {
			$src = wppa_fix_poster_ext( $src, $image['id'] );
		}

		$imgattr   	= $imgattrs_a[$idx]['style'];
		$imgwidth  	= $imgattrs_a[$idx]['width'];
		$imgheight 	= $imgattrs_a[$idx]['height'];
		$frmwidth  	= $imgwidth + '10';	// + 2 * 1 border + 2 * 4 padding
		$imgattr_a	= $imgattrs_a[$idx];
		$photolink 	= $photolinks[$idx];

		if ( $photolink ) {
			if ( $photolink['is_lightbox'] ) {
				$thumb = $image;
				$title = wppa_get_lbtitle( 'cover', $thumb['id'] );
				if ( wppa_is_video( $thumb['id'] ) ) {
					$siz['0'] = wppa_get_videox( $thumb['id'] );
					$siz['1'] = wppa_get_videoy( $thumb['id'] );
				}
				else {
					$siz['0'] = wppa_get_photox( $thumb['id'] );
					$siz['1'] = wppa_get_photoy( $thumb['id'] );
				}

				$link = wppa_get_photo_url( $thumb['id'], '', $siz['0'], $siz['1'] );
				if ( wppa_has_audio( $thumb['id'] ) ) {
					$link = wppa_fix_poster_ext( $link, $thumb['id'] );
				}

				$is_video 	= wppa_is_video( $thumb['id'] );
				$has_audio 	= wppa_has_audio( $thumb['id'] );

				wppa_out(
					'<a' .
						' href="' . $link . '"' .
						( $is_video ? ' data-videohtml="' . esc_attr( wppa_get_video_body( $thumb['id'] ) ) . '"' .
						' data-videonatwidth="' . wppa_get_videox( $thumb['id'] ) . '"' .
						' data-videonatheight="' . wppa_get_videoy( $thumb['id'] ) . '"' : '' ) .
						( $has_audio ? ' data-audiohtml="' . esc_attr( wppa_get_audio_body( $thumb['id'] ) ) . '"' : '' ) .
						' ' . wppa( 'rel' ) . '="' . wppa_opt( 'lightbox_name' ) . '[alw-' . wppa( 'mocc' ) . '-' . $albumid . ']"' .
						( $title ? ' ' . wppa( 'lbtitle' ) . '="' . $title . '"' : '' ) .
						' >'
					);

				// the cover image
				if ( $thumb['id'] == $image['id'] ) {
					if ( wppa_is_video( $image['id'] ) ) {
						wppa_out( "\n\t\t" .
							'<video preload="metadata" class="image wppa-img" id="i-' . $image['id'] . '-' .
							wppa( 'mocc' ) . '" title="' . wppa_zoom_in( $image['id'] ) . '" width="' .
							$imgwidth . '" height="' . $imgheight . '" style="' . __wcs( 'wppa-img' ) .
							$imgattr . $imgattr_a['cursor'] . '" ' . $events . '>' .
							wppa_get_video_body( $image['id'] ) . '</video>'
						);
					}
					else {
						wppa_out( "\n\t\t" .
							'<img class="image wppa-img" id="i-' . $image['id'] . '-' .
							wppa( 'mocc' ) . '" title="' . wppa_zoom_in( $image['id'] ) . '" src="' .
							$src . '" width="' . $imgwidth . '" height="' . $imgheight . '" style="' .
							__wcs( 'wppa-img' ) . $imgattr . $imgattr_a['cursor'] . '" ' . $events .
							' ' . wppa_get_imgalt( $image['id'] ) . '>'
						);
					}
				}
				wppa_out( '</a> ' );
			}

			else {	// Link is NOT lightbox
				$href = $photolink['url'] == '#' ? '' : 'href="' . wppa_convert_to_pretty( $photolink['url'] ) . '" ';
				wppa_out( '<a ' . $href . 'target="' . $photolink['target'] .
					'" title="' . $photolink['title'] . '" onclick="' . $photolink['onclick'] . '" >' );
				if ( wppa_is_video( $image['id'] ) ) {
					wppa_out( '<video preload="metadata" ' .
						' class="image wppa-img" width="' . $imgwidth . '" height="' . $imgheight .
						'" style="' . __wcs( 'wppa-img' ) . $imgattr . '" ' . $events . ' >' .
						wppa_get_video_body( $image['id'] ) . '</video>'
					);
				}
				else {
					wppa_out( '<img src="' . $src . '" ' . wppa_get_imgalt( $image['id'] ) .
						' class="image wppa-img" width="' . $imgwidth . '" height="' . $imgheight .
						'" style="' . __wcs( 'wppa-img' ) . $imgattr . '" ' . $events . ' />'
					);
				}
				wppa_out( '</a> ' );
			}
		}

		/*
		// No link
		else {
			wppa_out(
				'<img src="' . $src . '" ' . wppa_get_imgalt( $image['id'] ) . ' class="image wppa-img" width="' . $imgwidth .
				'" height="' . $imgheight . '" style="' . __wcs( 'wppa-img' ) . $imgattr . '" ' . $events . ' />'
			);
		}
		*/

		// No link on coverphoto
		else {

			// A video?
			if ( wppa_is_video( $image['id'] ) ) {
				wppa_out(
					'<video preload="metadata" class="image wppa-img" width="' . $imgwidth . '" height="' .
					$imgheight . '" style="' . __wcs( 'wppa-img' ) . $imgattr . '" ' . $events . ' >' .
					wppa_get_video_body( $image['id'] ) . '</video>'
				);
			}

			// A photo
			else {
				wppa_out(
					'<img src="' . $src . '" ' . wppa_get_imgalt( $image['id'] ) . ' class="image wppa-img" width="' .
					$imgwidth . '" height="' . $imgheight . '" style="' . __wcs( 'wppa-img' ) .
					$imgattr . '" ' . $events . ' />'
				);
			}
		}
	}

	// Close the coverphoto frame
	wppa_out( '</div>' );
}

// get id of coverphoto. does all testing
function wppa_get_coverphoto_id( $xalb = '' ) {
	$result = wppa_get_coverphoto_ids( $xalb, '1' );

	if ( empty( $result ) ) return false;
	return $result['0'];
}

// Get the cover photo id(s)
// The id in the album may be 0: random, -1: featured random; -2: last upload; > 0: one assigned specific.
// If one assigned but no longer exists or moved to other album: treat as random
function wppa_get_coverphoto_ids( $alb, $count ) {
global $wpdb;
static $cached_cover_photo_ids;

	// no album, no coverphoto
	if ( ! $alb ) return false;					
	
	// Did we do this before?
	if ( isset( $cached_cover_photo_ids[$alb] ) ) {
		return $cached_cover_photo_ids[$alb];
	}

	// Find cover photo id
	$id = wppa_get_album_item( $alb, 'main_photo' );

	// main_photo is a positive integer ( photo id )?
	if ( $id > '0' ) {									// 1 coverphoto explicitly given
		$photo = wppa_cache_photo( $id );
		if ( ! $photo ) {								// Photo gone, set id to 0
			$id = '0';
		}
		elseif ( $photo['album'] != $alb ) {			// Photo moved to other album, set id to 0
			$id = '0';
		}
		else {
			$temp['0'] = $photo;						// Found!
		}
	}

	// main_photo is 0? Random
	if ( '0' == $id ) {
		if ( current_user_can( 'wppa_moderate' ) ) {
			$temp = $wpdb->get_results( $wpdb->prepare(
				"SELECT * FROM `" . WPPA_PHOTOS . "` WHERE `album` = %s ORDER BY RAND( " . wppa_get_randseed( 'page' ) . " ) LIMIT %d",
				$alb, $count ), ARRAY_A );
		}
		else {
			$temp = $wpdb->get_results( $wpdb->prepare(
				"SELECT * FROM `" . WPPA_PHOTOS . "` WHERE `album` = %s AND ( ( `status` <> 'pending' AND `status` <> 'scheduled' ) OR `owner` = %s ) ORDER BY RAND( " . wppa_get_randseed( 'page' ) . " ) LIMIT %d",
				$alb, wppa_get_user(), $count ), ARRAY_A );
		}
	}

	// main_photo is -2? Last upload
	if ( '-2' == $id ) {
		if ( current_user_can( 'wppa_moderate' ) ) {
			$temp = $wpdb->get_results( $wpdb->prepare(
				"SELECT * FROM `" . WPPA_PHOTOS .
				"` WHERE `album` = %s ORDER BY `timestamp` DESC LIMIT %d", $alb, $count
				), ARRAY_A );
		}
		else {
			$temp = $wpdb->get_results( $wpdb->prepare(
				"SELECT * FROM `" . WPPA_PHOTOS .
				"` WHERE `album` = %s AND ( ( `status` <> 'pending' AND `status` <> 'scheduled' ) OR `owner` = %s ) ORDER BY `timestamp` DESC LIMIT %d",
				$alb, wppa_get_user(), $count ), ARRAY_A );
		}
	}

	// main_phtot is -1? Random featured
	if ( '-1' == $id ) {
		$temp = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM `" . WPPA_PHOTOS .
			"` WHERE `album` = %s AND `status` = 'featured' ORDER BY RAND( " . wppa_get_randseed( 'page' ) . " ) LIMIT %d",
			$alb, $count ), ARRAY_A );
	}

	// Random from children
	if ( '-3' == $id ) {
		$allalb = wppa_expand_enum( wppa_alb_to_enum_children( $alb ) );
		$temp = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM `" . WPPA_PHOTOS . "` " .
			"WHERE `album` IN ( " . str_replace( '.', ',', $allalb ) . " ) " .
			"AND ( ( `status` <> 'pending' AND `status` <> 'scheduled' ) OR `owner` = %s ) " .
			"ORDER BY RAND( " . wppa_get_randseed( 'page' ) . " ) LIMIT %d", wppa_get_user(), $count ), ARRAY_A );
	}

	// Most recent from children
	if ( '-4' == $id ) {
		$allalb = wppa_expand_enum( wppa_alb_to_enum_children( $alb ) );
		$temp = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM `" . WPPA_PHOTOS . "` " .
			"WHERE `album` IN ( " . str_replace( '.', ',', $allalb ) . " ) " .
			"AND ( ( `status` <> 'pending' AND `status` <> 'scheduled' ) OR `owner` = %s ) " .
			"ORDER BY `timestamp` DESC LIMIT %d", wppa_get_user(), $count ), ARRAY_A );
	}

	// Report query
	wppa_dbg_q( 'Q-gcovp' );

	// Add to 2nd level cache
	wppa_cache_photo( 'add', $temp );

	// Extract the ids only
	$ids = array();
	if ( is_array( $temp ) ) foreach ( $temp as $item ) {
		$ids[] = $item['id'];
	}
	
	$cached_cover_photo_ids[$alb] = $ids;
	return $ids;
}

// Find the cover Title's href, onclick and title
function wppa_get_album_title_attr_a( $albumid, $linktype, $linkpage, $has_content, $coverphoto, $photocount ) {

	$album = wppa_cache_album( $albumid );

	// Init
	$href_title 	= '';
	$onclick_title 	= '';
	$title_title 	= '';

	// Dispatch on linktype when page is not current
	if ( $linkpage > 0 ) {
		switch ( $linktype ) {
			case 'content':
			case 'thumbs':
			case 'albums':
				if ( $has_content ) {
					$href_title = wppa_get_album_url( $albumid, $linkpage, $linktype );
				}
				else {
					$href_title = get_page_link( $album['cover_linkpage'] );
				}
				break;
			case 'slide':
				if ( $has_content ) {
					$href_title = wppa_get_slideshow_url( $albumid, $linkpage );
				}
				else {
					$href_title = get_page_link( $album['cover_linkpage'] );
				}
				break;
			case 'page':
				$href_title = get_page_link( $album['cover_linkpage'] );
				break;
			case 'none':
				break;
			default:
		}
		$href_title = wppa_convert_to_pretty( $href_title );
		$title_title = __( 'Link to' , 'wp-photo-album-plus');
		$title_title .= ' ' . __( get_the_title( $album['cover_linkpage'] ) );
	}

	// Dispatch on linktype when page is current
	elseif ( $has_content ) {
		switch ( $linktype ) {
			case 'content':
			case 'thumbs':
			case 'albums':
				$href_title = wppa_convert_to_pretty( wppa_encrypt_url( wppa_get_album_url( $albumid, $linkpage, $linktype ) ) );
				if ( wppa_switch( 'allow_ajax' ) ) {
					$onclick_title = "wppaDoAjaxRender( " . wppa( 'mocc' ) . ", '" .
						wppa_encrypt_url( wppa_get_album_url_ajax( $albumid, $linkpage, $linktype ) ) . "', '" . $href_title . "' )";
					$href_title = "#";
				}
				break;
			case 'slide':
				$href_title = wppa_convert_to_pretty( wppa_encrypt_url( wppa_get_slideshow_url( $albumid, $linkpage ) ) );
				if ( wppa_switch( 'allow_ajax' ) ) {
					$onclick_title = "wppaDoAjaxRender( " . wppa( 'mocc' ) . ", '" .
						wppa_encrypt_url( wppa_get_slideshow_url_ajax( $albumid, $linkpage, $linktype ) ) . "', '" . $href_title . "' )";
					$href_title = "#";
				}
				break;
			case 'none':
				break;
			default:
		}
		$title_title =
			__( 'View the album' , 'wp-photo-album-plus') . ' ' . esc_attr( __( stripslashes( $album['name'] ) ) );
	}
	else {	// No content on current page/post
		if ( $photocount > '0' ) {	// coverphotos only
			if ( $coverphoto ) {
				$href_title = wppa_convert_to_pretty( wppa_encrypt_url( wppa_get_image_page_url_by_id( $coverphoto ) ) );
			}
			else {
				$href_title = '#';
			}
			if ( wppa_switch( 'allow_ajax' ) ) {
				if ( $coverphoto ) {
					$onclick_title = "wppaDoAjaxRender( " . wppa( 'mocc' ) . ", '" .
						wppa_encrypt_url( wppa_get_image_url_ajax_by_id( $coverphoto ) ) . "', '" . $href_title . "' )";
				}
				else {
					$onclick_title = '';
				}
				$href_title = "#";
			}
			$title_title = _n( 'View the cover photo', 'View the cover photos' , $photocount, 'wp-photo-album-plus');
		}
	}
	$title_attr['href'] 	= wppa_encrypt_url( $href_title );
	$title_attr['onclick'] 	= wppa_encrypt_url( $onclick_title );
	$title_attr['title'] 	= $title_title;

	return $title_attr;
}

// The 'View' link
function wppa_album_cover_view_link(
	$albumid, $has_content, $photocount, $albumcount, $mincount, $href_content,
	$target, $onclick_content ) {


	$album = wppa_cache_album( $albumid );

	if ( wppa_switch( 'show_viewlink' ) ) {
		wppa_out(
			'<div class="wppa-box-text wppa-black wppa-info wppa-viewlink">' );
		if ( $has_content ) {

			// Fake photocount to prevent link to empty page
			if ( wppa_opt( 'thumbtype' ) == 'none' ) $photocount = '0';

			// Still has content
			if ( $photocount > $mincount || $albumcount ) {

				// Get treecount data
				if ( wppa_switch( 'show_treecount' ) ) {
					$treecount = wppa_treecount_a( $albumid );
				}
				else {
					$treecount = false;
				}

				if ( $href_content == '#' ) {
					wppa_out(
						'<a onclick="' . $onclick_content . '" title="' . __( 'View the album' , 'wp-photo-album-plus') . ' ' .
						esc_attr( stripslashes( __( $album['name'] ) ) ) . '" style="cursor:pointer;' .
						__wcs( 'wppa-box-text-nocolor' ) . '" >'
					);
				}
				else {
					wppa_out(
						'<a href="' . $href_content . '" target="' . $target . '" onclick="' .
						$onclick_content . '" title="' . __( 'View the album' , 'wp-photo-album-plus') . ' ' .
						esc_attr( stripslashes( __( $album['name'] ) ) ) .
						'" style="cursor:pointer;' . __wcs( 'wppa-box-text-nocolor' ) . '" >'
					);
				}

				$na 	= $albumcount;
				$nta 	= $treecount['albums'] > $albumcount ? $treecount['albums'] : '';
				$np 	= $photocount > $mincount ? $photocount : '';
				$ntp 	= $treecount['photos'] > $photocount ? $treecount['photos'] : '';
				$text 	= __( 'View' , 'wp-photo-album-plus') . ' ';
				if ( $na ) {
					$text .= sprintf( _n( '%d album', '%d albums', $na, 'wp-photo-album-plus' ), $na ) . ' ';
				}
				if ( $nta ) {
					$text .= '(' . $nta . ') ';
				}
				if ( ( $na || $nta ) && ( $np || $ntp ) ) {
					$text .= __( 'and' ,'wp-photo-album-plus' ) . ' ';
				}
				if ( $np || $ntp ) {
					$text .= sprintf( _n( '%d photo', '%d photos', $np, 'wp-photo-album-plus' ), $np ) . ' ';
				}
				if ( $ntp ) {
					$text .= '(' . $ntp . ')';
				}

				wppa_out( str_replace( ' ', '&nbsp;', $text ) );

				wppa_out( '</a>' );
			}
		}
		else {
			wppa_out( '&nbsp;' );
		}
		wppa_out( '</div>' );
	}
}

function wppa_the_album_title( $alb, $href_title, $onclick_title, $title, $target ) {

	$album = wppa_cache_album( $alb );

	wppa_out(
		'<h2 class="wppa-title" style="clear:none; ' . __wcs( 'wppa-title' ) . '">'
		);

	if ( $href_title ) {
		if ( $href_title == '#' ) {
			wppa_out(
				'<a onclick="' . $onclick_title . '" title="' . $title .
				'" class="wppa-title" style="cursor:pointer; ' . __wcs( 'wppa-title' ) . '">' .
				wppa_get_album_name( $alb ) . '</a>'
				);
		}
		else {
			wppa_out(
				'<a href="' . $href_title . '" target="' . $target . '" onclick="' . $onclick_title .
				'" title="' . $title . '" class="wppa-title" style="' . __wcs( 'wppa-title' ) . '">' .
				wppa_get_album_name( $alb ) . '</a>'
				);
		}
	}
	else {
		wppa_out( wppa_get_album_name( $alb ) );
	}

	$fsize = '12';
	if ( wppa_is_album_new( $alb ) ) {
		$type = 'new';
	}
	elseif ( wppa_is_album_modified( $alb ) ) {
		$type = 'mod';
	}
	else {
		$type = '';
	}

	$do_image =  ! wppa_switch( 'wppa_new_mod_label_is_text' );

	if ( $type ) {
		if ( $do_image ) {
			wppa_out( 	'<img' .
							' src="' . wppa_opt($type.'_label_url') . '"' .
							' title="' . __( 'New!', 'wp-photo-album-plus' ) . '"' .
							' class="wppa-albumnew"' .
							' style="border:none;margin:0;padding:0;box-shadow:none;"' .
							' alt="' . __( 'New', 'wp-photo-album-plus' ) . '"' .
						' />'
					);

		}
		else {
			wppa_out(	' <div' .
							' style="' .
								'display:inline;' .
								'box-sizing:border-box;' .
								'font-size:' . $fsize . 'px;' .
								'line-height:' . $fsize . 'px;' .
								'font-family:\'Arial Black\', Gadget, sans-serif;' .
								'border-radius:4px;' .
								'border-width:2px;' .
								'border-style:solid;' .
						//		'padding:1px;' .
								wppa_get_text_medal_color_style( $type, '2' ) .
								'"' .
							' >' .
							'&nbsp;' . __( wppa_opt( $type . '_label_text' ) ) . '&nbsp;' .
						'</div>'
					);
		}
	}
	wppa_out( '</h2>' );
}

function wppa_albumcover_sublinks( $id, $width, $rsp ) {

	wppa_subalbumlinks_html( $id );
	wppa_user_destroy_html( $id, $width, 'cover', $rsp );
	wppa_user_create_html( $id, $width, 'cover', $rsp );
	wppa_user_upload_html( $id, $width, 'cover', $rsp );
	wppa_user_albumedit_html( $id, $width, 'cover', $rsp );
	wppa_album_download_link( $id );
	wppa_the_album_cats( $id );
}

function wppa_subalbumlinks_html( $id, $top = true ) {
global $wpdb;

	// Do they need us? Anything to display?
	if ( wppa_opt( 'cover_sublinks_display' ) == 'none' ) {
		return;
	}

	// Display type
	$display_type = wppa_opt( 'cover_sublinks_display' );

	// Link type
	$link_type = wppa_opt( 'cover_sublinks' );

	// Init
	$is_list = ( $display_type == 'list' || $display_type == 'recursivelist' );
	$is_recursive = $display_type == 'recursivelist';
	$first = true;

	// Get the children
	$subs = $wpdb->get_results( "SELECT * FROM `".WPPA_ALBUMS."` WHERE `a_parent` = " . $id . " " . wppa_get_album_order( $id ), ARRAY_A );

	// Only if there are sub-albums
	if ( ! empty( $subs ) ) {

		wppa_out( '<div class="wppa-cover-sublist-container" >' );

		// Start list if required
		if ( $is_list ) {
			wppa_out( 	'<ul' .
							' class="wppa-cover-sublink-list"' .
							' style="' .
								'clear:both;' .
								'margin:0;' .
								'list-style-type:disc;' .
								'list-style-position:inside;' .
								'padding:0 0 0 24px;' .
								'"' .
							' >'
					);
		}
		else {
			wppa_out( '<div style="clear:both;" ></div>' );
		}

		// Process the sub-albums
		foreach( $subs as $album ) {

			// What is the albums title linktype
			$linktype = $album['cover_linktype'];
			if ( ! $linktype ) $linktype = 'content'; // Default

			// What is the albums title linkpage
			$linkpage = $album['cover_linkpage'];
			if ( $linkpage == '-1' ) $linktype = 'none'; // for backward compatibility

			// Find the content 'View' link
			$albumid 			= $album['id'];
			$photocount 		= wppa_get_photo_count( $albumid, 'use_treecounts' );

			// Thumbnails and covers, show sub-album covers
			// in case slideshow is requested on an empty album
			if ( wppa_opt( 'cover_sublinks' ) == 'content' || ! $photocount ) {
				if ( wppa_switch( 'allow_ajax' ) && ! $linkpage ) {
					$href_content 		= '';
					$onclick_content 	= "wppaDoAjaxRender( " . wppa( 'mocc' ) . ", '" .
						wppa_encrypt_url( wppa_get_album_url_ajax( $albumid, $linkpage ) ) . "', '" .
						wppa_convert_to_pretty( wppa_encrypt_url( $href_content ) ) . "' )";
				}
				else {
					$href_content 		= wppa_convert_to_pretty( wppa_encrypt_url( wppa_get_album_url( $albumid, $linkpage ) ) );
					$onclick_content 	= '';
				}
			}

			// Slideshow
			else {
				if ( wppa_switch( 'allow_ajax' ) && ! $linkpage ) {
					$href_content 		= '';
					$onclick_content 	= "wppaDoAjaxRender( " . wppa( 'mocc' ) . ", '" .
						wppa_encrypt_url( wppa_get_slideshow_url_ajax( $albumid, $linkpage ) ) . "', '" .
						wppa_convert_to_pretty( $href_content ) . "' )";
				}
				else {
					$href_content 		= wppa_convert_to_pretty( wppa_encrypt_url( wppa_get_slideshow_url( $albumid, $linkpage ) ) );
					$onclick_content 	= '';
				}
			}

			// Do the output
			$title = esc_attr( __( 'View the album', 'wp-photo-album-plus' ) . ': ' . wppa_get_album_name( $album['id'] ) );
			switch( $display_type ) {
				case 'list':
				case 'recursivelist':
					if ( $link_type == 'none' ) {
						wppa_out( 	'<li style="margin:0;cursor:pointer;" >' .
										wppa_get_album_name( $album['id'] ) .
									'</li>'
								);
					}
					else {
						wppa_out( 	'<li style="margin:0;cursor:pointer;" >' .
										'<a' .
											( $href_content ? ' href="' . $href_content . '"' : '' ) .
											( $onclick_content ? ' onclick="' . $onclick_content . '"' : '' ) .
											' title="' . $title . '"' .
											' >' .
											wppa_get_album_name( $album['id'] ) .
										'</a>' .
									'</li>'
								);
					}
					break;
				case 'enum':
					if ( ! $first ) {
						wppa_out( ', ' );
					}
					if ( $link_type == 'none' ) {
						wppa_out( wppa_get_album_name( $album['id'] ) );
					}
					else {
						wppa_out( 	'<a' .
										( $href_content ? ' href="' . $href_content . '"' : '' ) .
										( $onclick_content ? ' onclick="' . $onclick_content . '"' : '' ) .
										' title="' . $title . '"' .
										' >' .
										wppa_get_album_name( $album['id'] ) .
									'</a>'
								);
					}
					$first = false;
					break;
				case 'microthumbs':
					$coverphoto_id = wppa_get_coverphoto_id( $album['id'] );
					$src = wppa_fix_poster_ext( wppa_get_thumb_url( $coverphoto_id ), $coverphoto_id );
					if ( $link_type == 'none' ) {
						wppa_out( 	'<img' .
										' class="wppa-cover-sublink-img"' .
										' src="' . $src . '"' .
										' alt="' . wppa_get_album_name( $album['id'] ) . '"' .
										' style="' .
											'max-width:100px;' .
											'max-height:50px;' .
											'padding:1px;' .
											'margin:1px;' .
											'background-color:' . wppa_opt( 'bgcolor_img' ) . ';' .
											'float:left;' .
											'"' .
									' />'
								);
					}
					else {
						wppa_out( 	'<a' .
										( $href_content ? ' href="' . $href_content . '"' : '' ) .
										( $onclick_content ? ' onclick="' . $onclick_content . '"' : '' ) .
										' title="' . $title . '"' .
										' >' .
											'<img' .
												' class="wppa-cover-sublink-img"' .
												' src="' . $src . '"' .
												' alt="' . wppa_get_album_name( $album['id'] ) . '"' .
												' style="' .
													'max-width:100px;' .
													'max-height:50px;' .
													'padding:1px;' .
													'margin:1px;' .
													'background-color:' . wppa_opt( 'bgcolor_img' ) . ';' .
													'float:left;' .
													'"' .
											' />' .
									'</a>'
								);
					}
					break;
			}

			// Go deeper for grandchildren
			if ( $is_recursive ) {
				wppa_subalbumlinks_html( $album['id'], false );
			}
		}

		// End list
		if ( $is_list ) {
			wppa_out( '</ul>' );
		}

		wppa_out( '</div>' );
	}
}

function wppa_the_slideshow_browse_link( $photocount, $href_slideshow, $onclick_slideshow, $target ) {

	if ( wppa_switch( 'show_slideshowbrowselink' ) ) {
		wppa_out(
			'<div class="wppa-box-text wppa-black wppa-info wppa-slideshow-browse-link">'
			);
		if ( $photocount > wppa_get_mincount() ) {
			$label = wppa_switch( 'enable_slideshow' ) ?
				__( 'Slideshow', 'wp-photo-album-plus' ) :
				__( 'Browse photos', 'wp-photo-album-plus' );
			if ( $href_slideshow == '#' ) {
				wppa_out(
					'<a onclick="' . $onclick_slideshow . '" title="' . $label . '" style="cursor:pointer;' .
					__wcs( 'wppa-box-text-nocolor' ) . '" >' . $label . '</a>'
					);
			}
			else {
				wppa_out(
					'<a href="' . $href_slideshow . '" target="' . $target . '" onclick="' .
					$onclick_slideshow . '" title="' . $label . '" style="cursor:pointer;' .
					__wcs( 'wppa-box-text-nocolor' ) . '" >' . $label . '</a>'
					);
			}
		}
		else {
			wppa_out( '&nbsp;' );
		}
		wppa_out( '</div>' );
	}
}

function wppa_the_album_cats( $alb ) {

	if ( ! wppa_switch( 'show_cats' ) ) {
		return;
	}

	$cats = wppa_get_album_item( $alb, 'cats' );
	$cats = trim( $cats, ',' );
	$cats = str_replace( ',', ',&nbsp;', $cats );

	if ( $cats ) {
		$temp 	= explode( ',', $cats );
		$ncats 	= count( $temp );
		wppa_out(
			'<div id="wppa-cats-' . $alb . '-' . wppa( 'mocc' ) . '" style="float:right" >' .
				_n( 'Category:', 'Categories:', $ncats, 'wp-photo-album-plus' ) . '&nbsp;<b>' . $cats . '</b>' .
			'</div>'
		);
	}
}