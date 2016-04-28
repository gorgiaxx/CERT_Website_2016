<?php
/* wppa-non-admin.php
* Package: wp-photo-album-plus
*
* Contains all the non admin stuff
* Version 6.4.09
*
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

/* API FILTER and FUNCTIONS */
require_once 'wppa-filter.php';
require_once 'wppa-slideshow.php';
require_once 'wppa-functions.php';
require_once 'wppa-breadcrumb.php';
require_once 'wppa-album-covers.php';
require_once 'wppa-links.php';
require_once 'wppa-boxes-html.php';
require_once 'wppa-styles.php';
require_once 'wppa-cart.php';
require_once 'wppa-thumbnails.php';

/* LOAD STYLESHEET */
add_action('wp_print_styles', 'wppa_add_style');

function wppa_add_style() {
global $wppa_api_version;

	// Are we allowed to look in theme?
	if ( wppa_switch( 'use_custom_style_file' ) ) {

		// In child theme?
		$userstyle = get_theme_root() . '/' . get_option('stylesheet') . '/wppa-style.css';
		if ( is_file($userstyle) ) {
			wp_register_style('wppa_style', get_theme_root_uri() . '/' . get_option('stylesheet')  . '/wppa-style.css', array(), $wppa_api_version);
			wp_enqueue_style('wppa_style');
			return;
		}

		// In theme?
		$userstyle = get_theme_root() . '/' . get_option('template') . '/wppa-style.css';
		if ( is_file($userstyle) ) {
			wp_register_style('wppa_style', get_theme_root_uri() . '/' . get_option('template')  . '/wppa-style.css', array(), $wppa_api_version);
			wp_enqueue_style('wppa_style');
			return;
		}
	}

	// Use standard
	wp_register_style('wppa_style', WPPA_URL.'/theme/wppa-style.css', array(), $wppa_api_version);
	wp_enqueue_style('wppa_style');

	// Dynamic css
	if ( ! wppa_switch( 'inline_css' ) ) {
		if ( ! file_exists( WPPA_PATH.'/wppa-dynamic.css' ) ) {
			wppa_create_wppa_dynamic_css();
			update_option( 'wppa_dynamic_css_version', get_option( 'wppa_dynamic_css_version', '0' ) + '1' );
		}
		if ( file_exists( WPPA_PATH.'/wppa-dynamic.css' ) ) {
			wp_enqueue_style( 'wppa-dynamic', WPPA_URL.'/wppa-dynamic.css', array('wppa_style'), get_option( 'wppa_dynamic_css_version' ) );
		}
	}
}

/* SEO META TAGS AND SM SHARE DATA */
add_action('wp_head', 'wppa_add_metatags', 5);

function wppa_add_metatags() {
global $wpdb;

	// Share info for sm that uses og
	$id = wppa_get_get( 'photo' );
	if ( $id ) {
		$imgurl = wppa_get_photo_url( $id );
		if ( wppa_is_video( $id ) ) {
			$imgurl = wppa_fix_poster_ext( $imgurl, $id );
		}
	}
	else {
		$imgurl = '';
	}
	if ( $id ) {
		if ( wppa_switch( 'og_tags_on' ) ) {
			$thumb = wppa_cache_thumb( $id );
			if ( $thumb ) {
				$title  = wppa_get_photo_name( $id );
				$desc 	= wppa_get_og_desc( $id );
				$url 	= ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				$site   = get_bloginfo('name');

				echo '
<!-- WPPA+ Share data -->
<meta property="og:site_name" content="' . esc_attr( sanitize_text_field( $site ) ) . '" />
<meta property="og:type" content="article" />
<meta property="og:url" content="' . esc_url( sanitize_text_field( $url ) ) . '" />
<meta property="og:title" content="' . esc_attr( sanitize_text_field( $title ) ) . '" />
<meta property="og:image" content="' . esc_url( sanitize_text_field( $imgurl ) ) . '" />
<meta property="og:description" content="' . esc_attr( sanitize_text_field( $desc ) ) . '" />
<!-- WPPA+ End Share data -->
';
			}
		}
	}

	// To make sure we are on a page that contains at least %%wppa%% we check for Get var 'wppa-album'.
	// This also narrows the selection of featured photos to those that exist in the current album.
	$done = array();
	$album = '';
	if ( isset( $_REQUEST['album'] ) ) $album = $_REQUEST['album'];
	elseif ( isset( $_REQUEST['wppa-album'] ) ) $album = $_REQUEST['wppa-album'];
	$album = strip_tags( $album );
	if ( strlen( $album == 12 ) ) $album = wppa_get_get( 'album' );

	if ( $album ) {
		if ( wppa_switch( 'meta_page' ) ) {
			$photos = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `".WPPA_PHOTOS."` WHERE `album` = %s AND `status` = 'featured'", $album ), ARRAY_A );
			wppa_dbg_q( 'Q-meta1' );
			wppa_cache_photo( 'add', $photos );
			if ( $photos ) {
				echo("\n<!-- WPPA+ BEGIN Featured photos on this page -->");
				foreach ( $photos as $photo ) {
					$id 		= $photo['id'];
					$content 	= esc_attr( sanitize_text_field( wppa_get_keywords( $id ) ) );
					if ( $content && ! in_array( $content, $done ) ) {
						echo'
<meta name="keywords" content="'.$content.'" >';
						$done[] = $content;
					}
				}
				echo("\n<!-- WPPA+ END Featured photos on this page -->\n");
			}
		}
	}

	// No photo and no album, give the plain photo links of all featured photos
	elseif ( wppa_switch( 'meta_all' ) ) {
		$photos = $wpdb->get_results( "SELECT * FROM `".WPPA_PHOTOS."` WHERE `status` = 'featured'", ARRAY_A);
		wppa_dbg_q( 'Q-meta2' );
		wppa_cache_photo( 'add', $photos );
		if ( $photos ) {
			echo("\n<!-- WPPA+ BEGIN Featured photos on this site -->");
			foreach ( $photos as $photo ) {
				$thumb 		= $photo;	// Set to global to reduce queries when getting the name
				$id 		= $photo['id'];
				$content 	= esc_attr( sanitize_text_field( wppa_get_keywords( $id ) ) );
				if ( $content && ! in_array( $content, $done ) ) {
					echo '
<meta name="keywords" content="'.$content.'" >';
					$done[] = $content;
				}
			}
			echo("\n<!-- WPPA+ END Featured photos on this site -->\n");
		}
	}

	// Facebook Admin and App
	if ( ( wppa_switch( 'share_on' ) ||  wppa_switch( 'share_on_widget' ) ) &&
		( wppa_switch( 'facebook_comments' ) || wppa_switch( 'facebook_like' ) || wppa_switch( 'share_facebook' ) ) ) {
		echo("\n<!-- WPPA+ BEGIN Facebook meta tags -->");
		if ( wppa_opt( 'facebook_admin_id' ) ) {
			echo ("\n\t<meta property=\"fb:admins\" content=\"".wppa_opt( 'facebook_admin_id' )."\" />");
		}
		if ( wppa_opt( 'facebook_app_id' ) ) {
			echo ("\n\t<meta property=\"fb:app_id\" content=\"".wppa_opt( 'facebook_app_id' )."\" />");
		}
		if ( $imgurl ) {
			echo '
<link rel="image_src" href="'.esc_url( $imgurl ).'" />';
		}
		echo '
<!-- WPPA+ END Facebook meta tags -->
';
	}
}

/* LOAD SLIDESHOW, THEME, AJAX and LIGHTBOX js, all in one file nowadays */
add_action('init', 'wppa_add_javascripts', '101');

function wppa_add_javascripts() {
global $wppa_api_version;
global $wppa_lang;
global $wppa_js_page_data_file;
global $wppa_opt;

	$footer = ( wppa_switch( 'defer_javascript' ) );

	// If the user wants the js in the footer, try to open a tempfile to collect the js data during processing the page
	// If opening a tempfile fails, revert to js in the header.
	if ( $footer ) {
		$tempdir 	= WPPA_UPLOAD_PATH.'/temp';
		if ( ! is_dir( $tempdir ) ) @ wppa_mktree( $tempdir );
		wppa_delete_obsolete_tempfiles();

		$wppa_js_page_data_file = WPPA_UPLOAD_PATH.'/temp/wppa.'.$_SERVER['REMOTE_ADDR'].'.js';
		$handle = fopen ( $wppa_js_page_data_file, 'wb' );

		if ( $handle ) {
			fwrite( $handle, '/* WPPA+ Generated Page dependant javascript */'."\n" );
		}
		else {
			$wppa_js_page_data_file = '';
			$footer = false;
		}
		fclose ( $handle );
	}

	// WPPA+ Javascript files.
	// All wppa+ js files come in 2 flavours: the normal version and a minified version.
	// If the minified version is available, it will be loaded, else the normal version.
	// If you want to debug js, just delete the minified version; this will cause the normal
	// - readable - version to be loaded.

	$any_lightbox = ( wppa_opt( 'lightbox_name' ) == 'wppa' ) &&
					( wppa_switch( 'lightbox_global' ) ||
						in_array( 'lightbox', $wppa_opt ) ||
						in_array( 'lightboxsingle', $wppa_opt )
					);

	$js_files = array ( 'wppa',
						'wppa-slideshow',
						'wppa-ajax-front',
						'wppa-lightbox',
						'wppa-popup',
						'wppa-touch',
						'wppa-utils',
					);

	$js_depts = array ( array( 'jquery', 'jquery-form', 'wppa-utils' ),
						array( 'jquery' ),
						array( 'jquery' ),
						array( 'jquery' ),
						array( 'jquery' ),
						array( 'jquery' ),
						array( 'jquery' ),
					);

	$js_doits = array ( true,
						true,
						true,
						$any_lightbox,
						true,
						wppa_switch('wppa_slide_swipe') || $any_lightbox,
						true,
					);

	$js_footer = array ( $footer,
						 $footer,
						 $footer,
						 $footer,
						 $footer,
						 $footer,
						 $footer,
					);

	foreach ( array_keys( $js_files ) as $idx ) {
		if ( $js_doits[$idx] ) {
			if ( is_file( dirname( __FILE__ ) . '/js/' . $js_files[$idx] . '.min.js' ) ) {
				wp_enqueue_script( $js_files[$idx], WPPA_URL . '/js/' . $js_files[$idx] . '.min.js', $js_depts[$idx], $wppa_api_version, $js_footer[$idx] );
			}
			else {
				wp_enqueue_script( $js_files[$idx], WPPA_URL . '/js/' . $js_files[$idx] . '.js', $js_depts[$idx], $wppa_api_version, $js_footer[$idx] );
			}
		}
	}

	// google maps
	if ( wppa_opt( 'gpx_implementation' ) == 'wppa-plus-embedded' && strpos( wppa_opt( 'custom_content' ), 'w#location' ) !== false ) {
		if ( wppa_opt( 'map_apikey' ) ) {
			wp_enqueue_script( 'wppa-geo', 'https://maps.googleapis.com/maps/api/js?key='.wppa_opt( 'map_apikey' ).'&sensor=false', '', $wppa_api_version, $footer );
		}
		else {
			wp_enqueue_script( 'wppa-geo', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false', '', $wppa_api_version, $footer );
		}
	}
	// wppa-init
	if ( ! file_exists( WPPA_PATH.'/wppa-init.'.$wppa_lang.'.js' ) ) {
		wppa_create_wppa_init_js();
		update_option( 'wppa_ini_js_version_'.$wppa_lang, get_option( 'wppa_ini_js_version_'.$wppa_lang, '0' ) + '1' );
	}
	if ( file_exists( WPPA_PATH.'/wppa-init.'.$wppa_lang.'.js' ) ) {
		wp_enqueue_script( 'wppa-init', WPPA_URL.'/wppa-init.'.$wppa_lang.'.js', array('wppa'), get_option( 'wppa_ini_js_version_'.$wppa_lang, $footer ) );
	}
	// wppa.pagedata
	if ( $footer ) {
		wp_enqueue_script( 'wppa-pagedata', WPPA_UPLOAD_URL.'/temp/wppa.'.$_SERVER['REMOTE_ADDR'].'.js', array('wppa-init'), rand(0,4711), $footer );
	}
}

/* LOAD WPPA+ THEME */
add_action('init', 'wppa_load_theme');

function wppa_load_theme() {

	// Are we allowed to look in theme?
	if ( wppa_switch( 'use_custom_theme_file' ) ) {

		$usertheme = get_theme_root() . '/' . get_option('template') . '/wppa-theme.php';
		if ( is_file( $usertheme ) ) {
			require_once $usertheme;
			return;
		}
	}
	require_once 'theme/wppa-theme.php';
}

/* LOAD FOOTER REQD DATA */
add_action('wp_footer', 'wppa_load_footer');

function wppa_load_footer() {
global $wpdb;
global $wppa_session;

	echo '
		<!-- start WPPA+ Footer data -->
		';

	// Do they use our lightbox?
	if ( wppa_opt( 'lightbox_name' ) == 'wppa' ) {
		$fontsize_lightbox = wppa_opt( 'fontsize_lightbox' ) ? wppa_opt( 'fontsize_lightbox' ) : '10';
		$d = wppa_switch('wppa_ovl_show_counter') ? 1 : 0;
		$ovlh = wppa_opt( 'ovl_txt_lines' ) == 'auto' ? 'auto' : ((wppa_opt( 'ovl_txt_lines' ) + $d) * ($fontsize_lightbox + 2));
		$txtcol = wppa_opt( 'ovl_theme' ) == 'black' ? '#a7a7a7' : '#272727';

		// The lightbox overlay background
		echo
		'<div' .
			' id="wppa-overlay-bg"' .
			' style="' .
				'text-align:center;' .
				'display:none;' .
				'position:fixed;' .
				'top:0;' .
				'left:0;' .
				'z-index:100090;' .
				'width:100%;' .
				'height:2048px;' .
				'background-color:'.wppa_opt( 'ovl_bgcolor' ).';' .
				'"' .
			' onclick="wppaOvlOnclick(event)"' .
			' >';

			// Display legenda
			if ( wppa_switch( 'ovl_show_legenda' ) && ! wppa( 'is_mobile' ) ) {
				echo
				'<div' .
					' id="wppa-ovl-legenda-1"' .
					' onmouseover="jQuery(this).css(\'visibility\',\'visible\');"' .
					' onmouseout="jQuery(this).css(\'visibility\',\'hidden\');"' .
					' style="' .
						'position:absolute;' .
						'left:0;' .
						'top:0;' .
						'background-color:'.wppa_opt( 'ovl_theme' ).';' .
						'color:'.$txtcol.';' .
						'visibility:visible;' .
						'"' .
					' >
					'.__( 'Press f for fullscreen.' , 'wp-photo-album-plus').'
				</div>';
			}

			// The fullscreen button
			echo
				'<img' .
					' id="wppa-fulls-btn"' .
					' src="'.wppa_get_imgdir('fulls.png').'"' .
					' style="height:32px;z-index:100091;position:fixed;top:0;right:0;"' .
					' alt="' . __( 'Toggle fullscreen', 'wp-photo-album-plus'  ) . '"' .
					' onclick="wppaOvlFull()"' .
					' ontouchstart="wppaOvlFull()"' .
					' onmouseover="jQuery(this).fadeTo(600,1);"' .
					' onmouseout="jQuery(this).fadeTo(600,0);"' .
				' />';

		// Close lightbox overlay background
		echo
		'</div>';

		// The Lightbox Image container
		echo
		'<div'.
			' id="wppa-overlay-ic"'.
			' style="' .
				'position:fixed;' .
				'top:50%;' .
				'left:50%;' .
				'z-index:100095;' .
				'opacity:1;' .
				'box-shadow:none;' .
				'box-sizing:content-box;' .
				'"' .
			' >' .
		'</div>';

		// The Spinner image
		echo '
			<img' .
				' id="wppa-overlay-sp"' .
				' alt="spinner"' .
				' style="' .
					'position:fixed;' .
					'top:50%;' .
					'margin-top:-16px;' .
					'left:50%;' .
					'margin-left:-16px;' .
					'z-index:100100;' .
					'opacity:1;' .
					'visibility:hidden;' .
					'box-shadow:none;' .
					'"' .
				' src="'.wppa_get_imgdir().'loading.gif"' .
			' />';

		// The init vars
		echo '
		<script type="text/javascript">
			jQuery("#wppa-overlay-bg").css({height:window.innerHeight});
			wppaOvlModeInitial = "'.wppa_opt( 'ovl_mode_initial' ).'";
			wppaOvlTxtHeight = "'.$ovlh.'";
			wppaOvlOpacity = '.(wppa_opt( 'ovl_opacity' )/100).';
			wppaOvlOnclickType = "'.wppa_opt( 'ovl_onclick' ).'";
			wppaOvlTheme = "'.wppa_opt( 'ovl_theme' ).'";
			wppaOvlAnimSpeed = '.wppa_opt( 'ovl_anim' ).';
			wppaOvlSlideSpeed = '.wppa_opt( 'ovl_slide' ).';
			wppaVer4WindowWidth = 800;
			wppaVer4WindowHeight = 600;
			wppaOvlShowCounter = '.( wppa_switch('wppa_ovl_show_counter') ? 'true' : 'false' ).';
			'.( wppa_opt( 'fontfamily_lightbox' ) ? 'wppaOvlFontFamily = "'.wppa_opt( 'fontfamily_lightbox' ).'"' : '').'
			wppaOvlFontSize = "'.$fontsize_lightbox.'";
			'.( wppa_opt( 'fontcolor_lightbox' ) ? 'wppaOvlFontColor = "'.wppa_opt( 'fontcolor_lightbox' ).'"' : '').'
			'.( wppa_opt( 'fontweight_lightbox' ) ? 'wppaOvlFontWeight = "'.wppa_opt( 'fontweight_lightbox' ).'"' : '').'
			'.( wppa_opt( 'fontsize_lightbox' ) ? 'wppaOvlLineHeight = "'.(wppa_opt( 'fontsize_lightbox' ) + '2').'"' : '').'
			wppaOvlFullLegenda = "'.__('Keys: f = next mode; q,x = exit; p = previous, n = next, s = start/stop, d = dismiss this notice.', 'wp-photo-album-plus').'";
			wppaOvlFullLegendaSingle = "'.__('Keys: f = next mode; q,x = exit; d = dismiss this notice.', 'wp-photo-album-plus').'";
			wppaOvlVideoStart = '.( wppa_switch( 'ovl_video_start' ) ? 'true' : 'false' ).';
			wppaOvlAudioStart = '.( wppa_switch( 'ovl_audio_start' ) ? 'true' : 'false' ).';
			wppaOvlShowLegenda = '.( wppa_switch( 'ovl_show_legenda' ) && ! wppa( 'is_mobile' ) ? 'true' : 'false' ).';
			wppaOvlShowStartStop = '.( wppa_switch( 'ovl_show_startstop' ) ? 'true' : 'false' ).';
			wppaToggleFullScreen = "'. __( 'Toggle fullscreen', 'wp-photo-album-plus' ) . '";
		</script>
		';
	}

	// The photo views cache
	echo '
	<script type="text/javascript">';
		if ( isset( $wppa_session['photo'] ) ) {
			foreach ( array_keys( $wppa_session['photo'] ) as $p ) {
				echo '
				wppaPhotoView['.$p.'] = true;';
			}
		}
	echo '
	</script>
<!-- end WPPA+ Footer data -->
';

	// Debugging, show queries
	wppa_dbg_q('print');

	// Debugging, show active plugins
	if ( wppa( 'debug' ) ) {
		$plugins = get_option('active_plugins');
		wppa_dbg_msg('Active Plugins');
		foreach ( $plugins as $plugin ) {
			wppa_dbg_msg($plugin);
		}
		wppa_dbg_msg('End Active Plugins');
	}

	echo '
<!-- Nonce for various wppa actions -->';
	// Nonce field for Ajax bump view counter from lightbox, and rating
	wp_nonce_field('wppa-check' , 'wppa-nonce', false, true);

	echo '
<!-- Do user upload -->';
	// Do the upload if required and not yet done
	wppa_user_upload();

	// Done
	echo '
<!-- Done user upload -->';
}

/* FACEBOOK COMMENTS */
add_action('wp_footer', 'wppa_fbc_setup', 100);

function wppa_fbc_setup() {
global $wppa_locale;

	if ( wppa_switch( 'load_facebook_sdk' ) &&  			// Facebook sdk requested
		( 	wppa_switch( 'share_on' ) ||
			wppa_switch( 'share_on_widget' ) ||
			wppa_switch( 'share_on_thumbs' ) ||
			wppa_switch( 'share_on_lightbox' ) ||
			wppa_switch( 'share_on_mphoto' ) ) &&
		(	wppa_switch( 'share_facebook' ) ||
			wppa_switch( 'facebook_like' ) ||
			wppa_switch( 'facebook_comments' ) )			// But is it used by wppa?
	) {
		?>
		<!-- Facebook Comments for WPPA+ -->
		<div id="fb-root"></div>
		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/<?php echo $wppa_locale; ?>/all.js#xfbml=1";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
		</script>
	<?php
	}
}

/* CHECK REDIRECTION */
add_action( 'plugins_loaded', 'wppa_redirect', '1' );

function wppa_redirect() {

	if ( ! isset( $_SERVER["REQUEST_URI"] ) ) return;

	$uri = $_SERVER["REQUEST_URI"];
	$wppapos = stripos( $uri, '/wppaspec/' );

	if ( $wppapos && get_option('permalink_structure') ) {

		// old style solution, still required when qTranslate is active
		$plugins = implode( ',', get_option( 'active_plugins' ) );
		if ( stripos( $plugins, 'qtranslate' ) !== false ) {

			$newuri = wppa_convert_from_pretty( $uri );
			if ( $newuri == $uri ) return;

			// Although the url is urlencoded it is damaged by wp_redirect when it contains chars like ë, so we do a header() call
			header( 'Location: '.$newuri, true, 302 );
			exit;
		}

		// New style solution
		$newuri = wppa_convert_from_pretty($uri);
		if ( $newuri == $uri ) return;
		$_SERVER["REQUEST_URI"] = $newuri;
		wppa_convert_uri_to_get( $newuri );
	}
}

/* ADD PAGE SPECIFIC ( http or https ) URLS */
add_action( 'wp_head', 'wppa_add_page_specific_urls', '99' );

function wppa_add_page_specific_urls() {

	$result = '
<!-- WPPA+ BEGIN Page specific urls -->
<script type="text/javascript">
	wppaImageDirectory = "'.wppa_get_imgdir().'";
	wppaWppaUrl = "'.wppa_get_wppa_url().'";
	wppaIncludeUrl = "'.trim( includes_url(), '/' ).'";
	wppaAjaxUrl = "'.( wppa_switch( 'ajax_non_admin' ) ? wppa_url( 'wppa-ajax-front.php' ) : admin_url( 'admin-ajax.php' ) ).'";
	wppaUploadUrl = "'.WPPA_UPLOAD_URL.'";
</script>
<!-- WPPA+ END Page specific urls -->';

	// Relative urls?
	$result = wppa_make_relative( $result );

	echo $result;

}

/* ENABLE RENDERING */
add_action( 'wp_head', 'wppa_kickoff', '100' );

function wppa_kickoff() {
global $wppa_lang;
global $wppa_api_version;
global $wppa_init_js_data;
global $wppa_dynamic_css_data;

	// init.css failed?
	if ( $wppa_dynamic_css_data ) echo $wppa_dynamic_css_data;

	// init.js failed?
	if ( $wppa_init_js_data ) echo $wppa_init_js_data;

	// Patch for chrome?
	if ( isset($_SERVER["HTTP_USER_AGENT"]) && isset($_SERVER["HTTP_USER_AGENT"]) ) {
		echo '

<!-- WPPA+ Kickoff -->
<!-- Browser detected = '.wppa_decode_uri_component(strip_tags($_SERVER["HTTP_USER_AGENT"])).' -->';
		if ( strstr($_SERVER["HTTP_USER_AGENT"], 'Chrome') && wppa_switch('wppa_ovl_chrome_at_top') ) echo '
<style type="text/css">
	#wppa-overlay-ic { padding-top: 5px !important; }
	#wppa-overlay-qt-img { top: 5px !important; }
</style>';
	}

	// Inline styles?
	if ( wppa_switch('wppa_inline_css') ) {
		echo '
<!-- WPPA+ Custom styles -->
<style type="text/css" >';
		if ( ! wppa_switch( 'ovl_fs_icons' ) ) {
			echo '#wppa-norms-btn, #wppa-fulls-btn { display:none; }';
		}
		echo wppa_opt( 'custom_style' ).'
</style>';
	}

	// Pinterest js
	if ( ( wppa_switch('wppa_share_on') || wppa_switch('wppa_share_on_widget') ) && wppa_switch('wppa_share_pinterest') ) {
		echo '
<!-- Pinterest share -->
<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>';
	}

	if ( wppa( 'debug' ) ) {
		error_reporting( wppa( 'debug' ) );
		add_action( 'wp_footer', 'wppa_phpinfo' );
		add_action( 'wp_footer', 'wppa_errorlog' );
		echo '
<script type="text/javascript" >
	wppaDebug = true;
</script>';
	}

	wppa( 'rendering_enabled', true );
	echo '
<!-- Rendering enabled -->
<!-- /WPPA Kickoff -->

	';

}

/* SKIP JETPACK FOTON ON WPPA+ IMAGES */
add_filter('jetpack_photon_skip_image', 'wppa_skip_photon', 10, 3);
function wppa_skip_photon($val, $src, $tag) {
	$result = $val;
	if ( strpos($src, WPPA_UPLOAD_URL) !== false ) $result = true;
	return $result;
}

/* Create dynamic js init file */
function wppa_create_wppa_init_js() {
global $wppa_api_version;
global $wppa_lang;
global $wppa_init_js_data;

	// Init
	if ( is_numeric(wppa_opt( 'fullimage_border_width' )) ) $fbw = wppa_opt( 'fullimage_border_width' ) + '1'; else $fbw = '0';

	// Make content
	$content =
'/* -- WPPA+ Runtime parameters
/*
/* Dynamicly Created on '.date('c').'
/*
*/
';
	if ( ( WPPA_DEBUG || wppa_get_get( 'debug' ) || WP_DEBUG ) && ! wppa_switch( 'defer_javascript' ) ) {
	$content .= '
	/* Check if wppa.js and jQuery are present */
	if (typeof(_wppaSlides) == \'undefined\') alert(\'There is a problem with your theme. The file wppa.js is not loaded when it is expected (Errloc = wppa_kickoff).\');
	if (typeof(jQuery) == \'undefined\') alert(\'There is a problem with your theme. The jQuery library is not loaded when it is expected (Errloc = wppa_kickoff).\');
';	}
	/* This goes into wppa.js */
	/* If you add something that uses an element from $wppa_opt[], */
	/* or a function that uses an element from $wppa_opt[], */
	/* add the optionslug to $init_js_critical[] in wppa_update_option in wppa-utils.php !!!!! */
	$content .= '
	wppaVersion = "'.$wppa_api_version.'";
	wppaDebug = '.( wppa_switch( 'allow_debug' ) ? 'true' : 'false' ).';
	wppaBackgroundColorImage = "'.wppa_opt( 'bgcolor_img' ).'";
	wppaPopupLinkType = "'.wppa_opt( 'thumb_linktype' ).'";
	wppaAnimationType = "'.wppa_opt( 'animation_type' ).'";
	wppaAnimationSpeed = '.wppa_opt( 'animation_speed' ).';
	wppaThumbnailAreaDelta = '.wppa_get_thumbnail_area_delta().';
	wppaTextFrameDelta = '.wppa_get_textframe_delta().';
	wppaBoxDelta = '.wppa_get_box_delta().';
	wppaSlideShowTimeOut = '.wppa_opt( 'slideshow_timeout' ).';
	wppaPreambule = '.wppa_get_preambule().';
	wppaFilmShowGlue = '.( wppa_switch('wppa_film_show_glue') ? 'true' : 'false' ).';
	wppaSlideShow = "'.__('Slideshow', 'wp-photo-album-plus').'";
	wppaStart = "'.__('Start', 'wp-photo-album-plus').'";
	wppaStop = "'.__('Stop', 'wp-photo-album-plus').'";
	wppaSlower = "'.__('Slower', 'wp-photo-album-plus').'";
	wppaFaster = "'.__('Faster', 'wp-photo-album-plus').'";
	wppaPhoto = "'.__('Photo', 'wp-photo-album-plus').'";
	wppaOf = "'.__('of', 'wp-photo-album-plus').'";
	wppaPreviousPhoto = "'.__('Previous photo', 'wp-photo-album-plus').'";
	wppaNextPhoto = "'.__('Next photo', 'wp-photo-album-plus').'";
	wppaPrevP = "'.__('Prev.', 'wp-photo-album-plus').'";
	wppaNextP = "'.__('Next', 'wp-photo-album-plus').'";
	wppaAvgRating = "'.__('Average&nbsp;rating', 'wp-photo-album-plus').'";
	wppaMyRating = "'.__('My&nbsp;rating', 'wp-photo-album-plus').'";
	wppaAvgRat = "'.__('Avg.', 'wp-photo-album-plus').'";
	wppaMyRat = "'.__('Mine', 'wp-photo-album-plus').'";
	wppaDislikeMsg = "'.__('You marked this image as inappropriate.', 'wp-photo-album-plus').'";
	wppaMiniTreshold = '.( wppa_opt( 'mini_treshold' ) ? wppa_opt( 'mini_treshold' ) : '0' ).';
	wppaRatingOnce = '.( wppa_switch('wppa_rating_change') || wppa_switch('wppa_rating_multi') ? 'false' : 'true' ).';
	wppaPleaseName = "'.__('Please enter your name', 'wp-photo-album-plus').'";
	wppaPleaseEmail = "'.__('Please enter a valid email address', 'wp-photo-album-plus').'";
	wppaPleaseComment = "'.__('Please enter a comment', 'wp-photo-album-plus').'";
	wppaHideWhenEmpty = '.( wppa_switch('wppa_hide_when_empty') ? 'true' : 'false' ).';
	wppaBGcolorNumbar = "'.wppa_opt( 'bgcolor_numbar' ).'";
	wppaBcolorNumbar = "'.wppa_opt( 'bcolor_numbar' ).'";
	wppaBGcolorNumbarActive = "'.wppa_opt( 'bgcolor_numbar_active' ).'";
	wppaBcolorNumbarActive = "'.wppa_opt( 'bcolor_numbar_active' ).'";
	wppaFontFamilyNumbar = "'.wppa_opt( 'fontfamily_numbar' ).'";
	wppaFontSizeNumbar = "'.wppa_opt( 'fontsize_numbar' ).'px";
	wppaFontColorNumbar = "'.wppa_opt( 'fontcolor_numbar' ).'";
	wppaFontWeightNumbar = "'.wppa_opt( 'fontweight_numbar' ).'";
	wppaFontFamilyNumbarActive = "'.wppa_opt( 'fontfamily_numbar_active' ).'";
	wppaFontSizeNumbarActive = "'.wppa_opt( 'fontsize_numbar_active' ).'px";
	wppaFontColorNumbarActive = "'.wppa_opt( 'fontcolor_numbar_active' ).'";
	wppaFontWeightNumbarActive = "'.wppa_opt( 'fontweight_numbar_active' ).'";
	wppaNumbarMax = "'.wppa_opt( 'numbar_max' ).'";
	wppaLang = "'.$wppa_lang.'";
	wppaNextOnCallback = '.( wppa_switch('wppa_next_on_callback') ? 'true' : 'false' ).';
	wppaStarOpacity = '.( wppa_opt( 'star_opacity' )/'100' ).';
	wppaSlideWrap = '.( wppa_switch('wppa_slide_wrap') ? 'true' : 'false' ).';
	wppaEmailRequired = "'.wppa_opt('wppa_comment_email_required').'";
	wppaSlideBorderWidth = '.$fbw.';
	wppaAllowAjax = '.( wppa_switch('wppa_allow_ajax') ? 'true' : 'false' ).';
	wppaUsePhotoNamesInUrls = '.( wppa_switch('wppa_use_photo_names_in_urls') ? 'true' : 'false' ).';
	wppaThumbTargetBlank = '.( wppa_switch('wppa_thumb_blank') ? 'true' : 'false' ).';
	wppaRatingMax = '.wppa_opt( 'rating_max' ).';
	wppaRatingDisplayType = "'.wppa_opt( 'rating_display_type' ).'";
	wppaRatingPrec = '.wppa_opt( 'rating_prec' ).';
	wppaStretch = '.( wppa_switch('wppa_enlarge') ? 'true' : 'false' ).';
	wppaMinThumbSpace = '.wppa_opt( 'tn_margin' ).';
	wppaThumbSpaceAuto = '.( wppa_switch('wppa_thumb_auto') ? 'true' : 'false' ).';
	wppaMagnifierCursor = "'.wppa_opt( 'magnifier' ).'";
	wppaArtMonkyLink = "'.wppa_opt( 'art_monkey_link' ).'";
	wppaAutoOpenComments = '.( wppa_switch('wppa_auto_open_comments') ? 'true' : 'false' ).';
	wppaUpdateAddressLine = '.( wppa_switch('wppa_update_addressline') ? 'true' : 'false' ).';
	wppaFilmThumbTitle = "'.( wppa_opt( 'film_linktype' ) == 'lightbox' ? wppa_zoom_in( false ) : __('Double click to start/stop slideshow running', 'wp-photo-album-plus') ).'";
	wppaVoteForMe = "'.__(wppa_opt( 'vote_button_text' ), 'wp-photo-album-plus').'";
	wppaVotedForMe = "'.__(wppa_opt( 'voted_button_text' ), 'wp-photo-album-plus').'";
	wppaSlideSwipe = '.( wppa_switch('wppa_slide_swipe') ? 'true' : 'false' ).';
	wppaMaxCoverWidth = '.wppa_opt( 'max_cover_width' ).';
	wppaDownLoad = "'.__('Download', 'wp-photo-album-plus').'";
	wppaSlideToFullpopup = '.( wppa_opt( 'slideshow_linktype' ) == 'fullpopup' ? 'true' : 'false' ).';
	wppaComAltSize = '.wppa_opt( 'comten_alt_thumbsize' ).';
	wppaBumpViewCount = '.( wppa_switch('wppa_track_viewcounts') ? 'true' : 'false' ).';
	wppaShareHideWhenRunning = '.( wppa_switch('wppa_share_hide_when_running') ? 'true' : 'false' ).';
	wppaFotomoto = '.( wppa_switch('wppa_fotomoto_on') ? 'true' : 'false' ).';
	wppaArtMonkeyButton = '.( wppa_opt( 'art_monkey_display' ) == 'button' ? 'true' : 'false' ).';
	wppaFotomotoHideWhenRunning = '.( wppa_switch('wppa_fotomoto_hide_when_running') ? 'true' : 'false' ).';
	wppaCommentRequiredAfterVote = '.( wppa_switch('wppa_vote_needs_comment') ? 'true' : 'false' ).';
	wppaFotomotoMinWidth = '.wppa_opt( 'fotomoto_min_width' ).';
	wppaShortQargs = '.( wppa_switch('wppa_use_short_qargs') ? 'true' : 'false' ).';
	wppaOvlHires = '.( wppa_switch( 'lb_hres' ) ? 'true' : 'false' ).';
	wppaSlideVideoStart = '.( wppa_switch( 'start_slide_video' ) ? 'true' : 'false' ).';
	wppaSlideAudioStart = '.( wppa_switch( 'start_slide_audio' ) ? 'true' : 'false' ).';
	wppaAudioHeight = '.wppa_get_audio_control_height().';
	wppaRel = "'.( wppa_opt( 'lightbox_name' ) == 'wppa' ? 'data-rel' : 'rel' ).'";
	wppaStartSymbolUrl = "' . wppa_make_relative( wppa_opt('start_symbol_url') ) . '";
	wppaPauseSymbolUrl = "' . wppa_make_relative( wppa_opt('pause_symbol_url') ) . '";
	wppaStopSymbolUrl = "' . wppa_make_relative( wppa_opt('stop_symbol_url') ) . '";
	wppaStartPauseSymbolSize = "' . wppa_opt('start_pause_symbol_size') . '";
	wppaStartPauseSymbolBradius = "' . wppa_opt('start_pause_symbol_bradius') . '";
	wppaStopSymbolSize = "' . wppa_opt('stop_symbol_size') . '";
	wppaStopSumbolBradius = "' . wppa_opt('stop_symbol_bradius') . '";
	wppaOvlRadius = '.wppa_opt( 'ovl_border_radius' ).';
	wppaOvlBorderWidth = '.wppa_opt( 'ovl_border_width' ).';
	wppaOvlLeftSymbolUrl = "'.(wppa_opt('left_symbol_url') ? wppa_opt('left_symbol_url') : wppa_get_imgdir( 'prev-'.wppa_opt('ovl_theme').'.gif' )).'";
	wppaOvlRightSymbolUrl = "'.(wppa_opt('right_symbol_url') ? wppa_opt('right_symbol_url') : wppa_get_imgdir( 'next-'.wppa_opt('ovl_theme').'.gif' )).'";
	wppaLeftRightSymbolSize = '.wppa_opt('left_right_symbol_size').';
	wppaLeftRightSymbolBradius = '.wppa_opt('left_right_symbol_bradius').';
	';

	// Open file
	$file = @ fopen ( WPPA_PATH.'/wppa-init.'.$wppa_lang.'.js', 'wb' );
	if ( $file ) {
		// Write file
		fwrite ( $file, $content );
		// Close file
		fclose ( $file );
		$wppa_init_js_data = '';
	}
	else {
		$wppa_init_js_data =
'<script type="text/javascript">
/* Warning: file wppa-init.'.$wppa_lang.'.js could not be created */
/* The content is therefor output here */

'.$content.'
</script>
';
	}
}

add_action( 'init', 'wppa_set_shortcode_priority', 100 );

function wppa_set_shortcode_priority() {

	$newpri = wppa_opt( 'shortcode_priority' );
	if ( $newpri == '11' ) return;	// Default, do not change

	$oldpri = has_filter( 'the_content', 'do_shortcode' );
	if ( $oldpri ) {
		remove_filter( 'the_content', 'do_shortcode', $oldpri );
		add_filter( 'the_content', 'do_shortcode', $newpri );
	}
}

// This function contains strings for i18n from files not included
// in the search for frontend required translatable strings
// Mainly from widgets
function wppa_dummy() {

	// Commet widget
	__( 'wrote' , 'wp-photo-album-plus' );
	__( 'Photo not found', 'wp-photo-album-plus' );
	__( 'There are no commented photos (yet)', 'wp-photo-album-plus' );

	// Featen widget
	__( 'View the featured photos', 'wp-photo-album-plus' );
	__( 'Photo not found', 'wp-photo-album-plus' );
	__( 'There are no featured photos (yet)', 'wp-photo-album-plus' );

	// Lasten widget
	__( 'View the most recent uploaded photos', 'wp-photo-album-plus' );
	__( 'Photo not found', 'wp-photo-album-plus' );
	__( 'There are no uploaded photos (yet)', 'wp-photo-album-plus' );

	// Potd widget
	__( 'Photo not found', 'wp-photo-album-plus' );
	__( 'By:', 'wp-photo-album-plus' );

	// Slideshow widget
	__( 'No album defined (yet)', 'wp-photo-album-plus' );

	// Thumbnail widget
	__( 'Photo not found', 'wp-photo-album-plus' );
	__( 'There are no photos (yet)', 'wp-photo-album-plus' );

	// Upldr widget
	__( 'There are too many registered users in the system for this widget' , 'wp-photo-album-plus' );
	__( 'Photos uploaded by', 'wp-photo-album-plus' );

	// Topten widget
	_n( '%d vote', '%d votes', $n, 'wp-photo-album-plus' );
	_n( '%d view', '%d views', $n, 'wp-photo-album-plus' );
	__( 'Photo not found', 'wp-photo-album-plus' );
	__( 'There are no rated photos (yet)', 'wp-photo-album-plus' );

}