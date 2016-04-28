<?php
/*
 * Plugin Name: WP Photo Album Plus
 * Description: Easily manage and display your photo albums and slideshows within your WordPress site.
 * Version: 6.4.09
 * Author: J.N. Breetvelt a.k.a. OpaJaap
 * Author URI: http://wppa.opajaap.nl/
 * Plugin URI: http://wordpress.org/extend/plugins/wp-photo-album-plus/
 * Text Domain: wp-photo-album-plus
 * Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

// Debugging
// error_reporting( E_ALL );

// Switch off wptexturize
// add_filter( 'run_wptexturize', '__return_false' );

/* See explanation on activation hook in wppa-setup.php */
register_activation_hook( __FILE__, 'wppa_activate_plugin' );

/* GLOBALS */
global $wpdb;

/* This is the database revision number
/* It is incremented when the table defs are changed,
/* when new options are added and when the wppa_setup() routine
/* must be called right after update for any other reason.
*/
global $wppa_revno; 		$wppa_revno = '6409';

/* This is the api interface version number
/* It is incremented at any code change.
*/
global $wppa_api_version; 	$wppa_api_version = '6-4-09-007';

/* start timers */
global $wppa_starttime; $wppa_starttime = microtime(true);
global $wppa_loadtime; $wppa_loadtime = - microtime(true);

/* CONSTANTS
/*
/* Check for php version
/* PHP_VERSION_ID is available as of PHP 5.2.7, if our
/* version is lower than that, then emulate it
*/
if ( ! defined( 'PHP_VERSION_ID' ) ) {
	$version = explode( '.', PHP_VERSION );
	define( 'PHP_VERSION_ID', ( $version[0] * 10000 + $version[1] * 100 + $version[2] ) );
}

/* To run WPPA+ on a multisite in single site mode,
/* add to wp-config.php: define('WPPA_MULTISITE_GLOBAL', true); */
if ( ! defined('WPPA_MULTISITE_GLOBAL') ) {
	define( 'WPPA_MULTISITE_GLOBAL', false );
}

/* To run WPPA+ in a multisite old style mode,
/* add to wp-config.php: define('WPPA_MULTISITE_BLOGSDIR', true); */
if ( ! defined('WPPA_MULTISITE_BLOGSDIR') ) {
	define( 'WPPA_MULTISITE_BLOGSDIR', false );
}

/* To run WPPA+ in a multisite new style, new implementation mode,
/* add to wp-config.php: define('WPPA_MULTISITE_INDIVIDUAL', true); */
if ( ! defined('WPPA_MULTISITE_INDIVIDUAL') ) {
	define( 'WPPA_MULTISITE_INDIVIDUAL', false );
}

/* Choose the right db prifix */
if ( is_multisite() && WPPA_MULTISITE_GLOBAL ) {
	$wppa_prefix = $wpdb->base_prefix;
}
else {
	$wppa_prefix = $wpdb->prefix;
}

/* DB Tables */
define( 'WPPA_ALBUMS',   $wppa_prefix . 'wppa_albums' );
define( 'WPPA_PHOTOS',   $wppa_prefix . 'wppa_photos' );
define( 'WPPA_RATING',   $wppa_prefix . 'wppa_rating' );
define( 'WPPA_COMMENTS', $wppa_prefix . 'wppa_comments' );
define( 'WPPA_IPTC',	 $wppa_prefix . 'wppa_iptc' );
define( 'WPPA_EXIF', 	 $wppa_prefix . 'wppa_exif' );
define( 'WPPA_INDEX', 	 $wppa_prefix . 'wppa_index' );
define( 'WPPA_SESSION',	 $wppa_prefix . 'wppa_session' );

/* Paths and urls */ 									// Standard examples
define( 'WPPA_FILE', basename( __FILE__ ) );			// wppa.php
define( 'WPPA_PATH', dirname( __FILE__ ) );				// /.../wp-content/plugins/wp-photo-album-plus
define( 'WPPA_NAME', basename( dirname( __FILE__ ) ) );	// wp-photo-album-plus
define( 'WPPA_URL',  plugins_url() . '/' . WPPA_NAME ); // http://.../wp-photo-album-plus

// To fix a problem in Windows local host systems:
function wppa_trims( $txt ) {
	return trim( $txt, "\\/" );
}
function wppa_flips( $txt ) {
	return str_replace( "\\", "/", $txt );
}
function wppa_trimflips( $txt ) {
	return wppa_flips( wppa_trims ( $txt ) );
}
define( 'WPPA_ABSPATH', wppa_flips( ABSPATH ) );

// Although i may not use wp constants directly,
// there is no function that returns the path to wp-content,
// so, if you changed the location of wp-content, i have to use WP_CONTENT_DIR,
// because wp-content needs not to be relative to ABSPATH
if ( defined( 'WP_CONTENT_DIR' ) ) {
	define( 'WPPA_CONTENT_PATH', wppa_flips( WP_CONTENT_DIR ) );
}

// In the normal case i use content_url() with the site_url() part replaced by ABSPATH
else {
	define( 'WPPA_CONTENT_PATH',
		str_replace( wppa_trimflips( site_url() ) . '/',
		WPPA_ABSPATH, wppa_flips( content_url() ) )
		);												// /.../wp-content
}

// Load language
add_action( 'plugins_loaded', 'wppa_load_plugin_textdomain' );

function wppa_load_plugin_textdomain() {
global $wppa_lang;
global $wppa_locale;

	// Do we want text domain loaded? Hand Wahlgren does not.
	if ( get_option( 'wppa_use_language', 'yes' ) == 'yes' ) {
		$bret = load_plugin_textdomain( 'wp-photo-album-plus', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}
	$wppa_locale = get_locale() ? get_locale() : 'en_US';
	$wppa_lang = substr( $wppa_locale, 0, 2 );
}

// Init
add_action( 'init', 'wppa_init', '7' );

function wppa_init() {
global $blog_id;

	// Upload ( .../wp-content/uploads ) is always relative to ABSPATH,
	// see http://codex.wordpress.org/Editing_wp-config.php#Moving_wp-content_folder
	//
	// Assumption: site_url() corresponds with ABSPATH
	// Our version ( WPPA_UPLOAD ) of the relative part of the path/url to the uploads dir
	// is calculated form wp_upload_dir() by substracting ABSPATH from the uploads basedir.
	$wp_uploaddir = wp_upload_dir();

	// Unfortunately $wp_uploaddir['basedir'] does very often not contain the data promised
	// by the docuentation, so it is unreliable.
	$rel_uploads_path = defined( 'WPPA_REL_UPLOADS_PATH') ?
		wppa_trims( WPPA_REL_UPLOADS_PATH ) :
		'wp-content/uploads';

	// The depot dir is also relative to ABSPATH but on the same level as uploads,
	// but without '/wppa-depot'.
	// If you want to change the name of wp-content, you have also to define WPPA_REL_DEPOT_PATH
	// as being the relative path to the parent of wppa-depot.
	$rel_depot_path = defined( 'WPPA_REL_DEPOT_PATH' ) ?
		wppa_trims( WPPA_REL_DEPOT_PATH ) :
		'wp-content';

	// For multisite the uploads are in /wp-content/blogs.dir/<blogid>/,
	// so we hope still below ABSPATH
	$wp_content_multi = wppa_trims( str_replace( WPPA_ABSPATH, '', WPPA_CONTENT_PATH ) );

	// To test the multisite paths and urls, set $debug_multi = true
	$debug_multi = false;

	// Define paths and urls
	if ( $debug_multi || ( is_multisite() && ! WPPA_MULTISITE_GLOBAL ) ) {
		if ( WPPA_MULTISITE_BLOGSDIR ) {	// Old multisite individual
			define( 'WPPA_UPLOAD', wppa_trims( $wp_content_multi . '/blogs.dir/' . $blog_id ) );
			define( 'WPPA_UPLOAD_PATH', WPPA_ABSPATH.WPPA_UPLOAD . '/wppa' );
			define( 'WPPA_UPLOAD_URL', site_url() . '/' . WPPA_UPLOAD . '/wppa' );
			define( 'WPPA_DEPOT',
				wppa_trims( $wp_content_multi . '/blogs.dir/' . $blog_id . '/wppa-depot' ) );
			define( 'WPPA_DEPOT_PATH', WPPA_ABSPATH.WPPA_DEPOT );
			define( 'WPPA_DEPOT_URL', site_url() . '/' . WPPA_DEPOT );
		}
		elseif ( WPPA_MULTISITE_INDIVIDUAL ) {	// New multisite individual
			define( 'WPPA_UPLOAD', $rel_uploads_path . '/sites/'.$blog_id);
			define( 'WPPA_UPLOAD_PATH', ABSPATH.WPPA_UPLOAD.'/wppa');
			define( 'WPPA_UPLOAD_URL', get_bloginfo('wpurl').'/'.WPPA_UPLOAD.'/wppa');
			define( 'WPPA_DEPOT', $rel_uploads_path . '/sites/'.$blog_id.'/wppa-depot' );
			define( 'WPPA_DEPOT_PATH', ABSPATH.WPPA_DEPOT );
			define( 'WPPA_DEPOT_URL', get_bloginfo('wpurl').'/'.WPPA_DEPOT );
		}
		else { 	// Not working default multisite
			$user = is_user_logged_in() ? '/' . wppa_get_user() : '';
			define( 'WPPA_UPLOAD', $rel_uploads_path );
			define( 'WPPA_UPLOAD_PATH', WPPA_ABSPATH . WPPA_UPLOAD . $user . '/wppa' );
			define( 'WPPA_UPLOAD_URL', site_url() . '/' . WPPA_UPLOAD . $user . '/wppa' );
			define( 'WPPA_DEPOT', wppa_trims( $rel_depot_path . '/wppa-depot' . $user ) );
			define( 'WPPA_DEPOT_PATH', WPPA_ABSPATH . WPPA_DEPOT );
			define( 'WPPA_DEPOT_URL', site_url() . '/' . WPPA_DEPOT );
		}
	}
	else {	// Single site or multisite global
		define( 'WPPA_UPLOAD', $rel_uploads_path );
		if ( ! defined( 'WPPA_UPLOAD_PATH' ) ) {
			define( 'WPPA_UPLOAD_PATH', WPPA_ABSPATH . WPPA_UPLOAD . '/wppa' );
		}
		if ( ! defined( 'WPPA_UPLOAD_URL' ) ) {
			define( 'WPPA_UPLOAD_URL', site_url() . '/' . WPPA_UPLOAD . '/wppa' );
		}
		$user = is_user_logged_in() ? '/' . wppa_get_user() : '';
		define( 'WPPA_DEPOT', wppa_trims( $rel_depot_path . '/wppa-depot' . $user ) );
		if ( ! defined( '_WPPA_DEPOT_PATH' ) ) {
			define( 'WPPA_DEPOT_PATH', WPPA_ABSPATH . WPPA_DEPOT );
		}
		else {
			define( 'WPPA_DEPOT_PATH', _WPPA_DEPOT_PATH . WPPA_DEPOT );
		}
		if ( ! defined( '_WPPA_DEPOT_URL' ) ) {
			define( 'WPPA_DEPOT_URL', site_url() . '/' . WPPA_DEPOT );
		}
		else {
			define( 'WPPA_DEPOT_URL', _WPPA_DEPOT_URL . WPPA_DEPOT );
		}
	}

	wppa_mktree( WPPA_UPLOAD_PATH );	// Whatever (faulty) path has been calculated, it will be
	wppa_mktree( WPPA_UPLOAD_PATH . '/thumbs' );	// Just to make sure the chmod is right ( 755 )
	wppa_mktree( WPPA_DEPOT_PATH );		// created and not prevent plugin to activate or function
}

define( 'WPPA_NONCE' , 'wppa-update-check' );

// set WPPA_DEBUG to true to produces success/fale messages during setup and sets debug switch on.
define( 'WPPA_DEBUG', false );

/* LOAD SIDEBAR WIDGETS */
require_once 'wppa-potd-widget.php';
require_once 'wppa-search-widget.php';
require_once 'wppa-topten-widget.php';
require_once 'wppa-featen-widget.php';
require_once 'wppa-slideshow-widget.php';
require_once 'wppa-gp-widget.php';
require_once 'wppa-comment-widget.php';
require_once 'wppa-thumbnail-widget.php';
require_once 'wppa-lasten-widget.php';
require_once 'wppa-album-widget.php';
require_once 'wppa-qr-widget.php';
require_once 'wppa-tagcloud-widget.php';
require_once 'wppa-multitag-widget.php';
require_once 'wppa-upload-widget.php';
require_once 'wppa-super-view-widget.php';
require_once 'wppa-upldr-widget.php';
require_once 'wppa-bestof-widget.php';
require_once 'wppa-album-navigator-widget.php';
require_once 'wppa-stereo-widget.php';
require_once 'wppa-admins-choice-widget.php';

/* COMMON FUNCTIONS */
require_once 'wppa-common-functions.php';
require_once 'wppa-utils.php';
require_once 'wppa-exif-iptc-common.php';
require_once 'wppa-index.php';
require_once 'wppa-statistics.php';
require_once 'wppa-wpdb-insert.php';
require_once 'wppa-wpdb-update.php';
require_once 'wppa-users.php';
require_once 'wppa-watermark.php';
require_once 'wppa-setup.php';
require_once 'wppa-session.php';
require_once 'wppa-source.php';
require_once 'wppa-items.php';
require_once 'wppa-date-time.php';
require_once 'wppa-htaccess.php';
require_once 'wppa-video.php';
require_once 'wppa-audio.php';
require_once 'wppa-mobile.php';
require_once 'wppa-stereo.php';
require_once 'wppa-encrypt.php';

/* SET UP array $wppa, array $wppa_opt, and LANGUAGE */
add_action( 'init', 'wppa_initialize_runtime', '8' );

/* START SESSION */
add_action( 'init', 'wppa_session_start', '1' );

/* END SESSION */
add_action( 'shutdown', 'wppa_session_end' );

/* DO THE ADMIN/NON ADMIN SPECIFIC STUFF */
if ( is_admin() ) require_once 'wppa-admin.php';
else require_once 'wppa-non-admin.php';

/* ADD AJAX */
require_once 'wppa-ajax.php';

$wppa_loadtime += microtime(true);

/* This is for the changelog text when an update is available */
global $pagenow;
if ( 'plugins.php' === $pagenow )
{
    // Changelog update message
    $file   = basename( __FILE__ );
    $folder = basename( dirname( __FILE__ ) );
    $hook = "in_plugin_update_message-{$folder}/{$file}";
    add_action( $hook, 'wppa_update_message_cb', 20, 2 ); // hook for function below
}
function wppa_update_message_cb( $plugin_data, $r )
{
    $output = '<span style="margin-left:10px;color:#FF0000;">Please Read the ' .
		'<a href="http://wppa.opajaap.nl/changelog/" target="_blank" >Changelog</a>' .
		' Details Before Upgrading.</span>';

    return print $output;
}

/* This function will add "donate" link to main plugins page */
function wppa_donate_link($links, $file) {
	if ( $file == plugin_basename(__FILE__) ) {
		$donate_link_usd = '<a target="_blank" title="Paypal" href="https://' .
			'www.paypal.com/cgi-bin/webscr?cmd=_donations&business=OpaJaap@OpaJaap.nl&item_name=' .
			'WP-Photo-Album-Plus&item_number=Support-Open-Source&currency_code=USD&lc=US">' .
			'Donate USD</a>';
		$donate_link_eur = '<a target="_blank" title="Paypal" href="https://' .
			'www.paypal.com/cgi-bin/webscr?cmd=_donations&business=OpaJaap@OpaJaap.nl&item_name=' .
			'WP-Photo-Album-Plus&item_number=Support-Open-Source&currency_code=EUR&lc=US">' .
			'Donate EUR</a>';
		$docs_link = '<a target="_blank" href="http://wppa.opajaap.nl/" title=' .
			'"Docs & Demos" >Documentation and examples</a>';

		$links[] = $donate_link_usd . ' | ' . $donate_link_eur . ' | ' . $docs_link;
	}
	return $links;
}
add_filter('plugin_row_meta', 'wppa_donate_link', 10, 2);

/* Load adminbar menu if required */
add_action('init', 'wppa_admin_bar_init');
function wppa_admin_bar_init() {

	if ( ( is_admin() && wppa_switch('wppa_adminbarmenu_admin') ) ||
		( ! is_admin() && wppa_switch('wppa_adminbarmenu_frontend') ) ) {

		if ( current_user_can('wppa_admin') ||
			 current_user_can('wppa_upload') ||
			 current_user_can('wppa_import') ||
			 current_user_can('wppa_moderate') ||
			 current_user_can('wppa_export') ||
			 current_user_can('wppa_settings') ||
			 current_user_can('wppa_potd') ||
			 current_user_can('wppa_comments') ||
			 current_user_can('wppa_help') ) {
				require_once 'wppa-adminbar.php';
		}
	}
}

/* Load cloudinary if configured and php version >= 5.3 */
if ( PHP_VERSION_ID >= 50300 ) require_once 'wppa-cloudinary.php';

/* Check multisite config */
add_action('admin_notices', 'wppa_verify_multisite_config');
function wppa_verify_multisite_config() {

	if ( ! is_admin() ) return;
	if ( ! is_multisite() ) return;
	if ( wppa( 'ajax' ) ) return;

	if ( WPPA_MULTISITE_GLOBAL ) return;
	if ( WPPA_MULTISITE_BLOGSDIR ) return;
	if ( WPPA_MULTISITE_INDIVIDUAL ) return;

	$errtxt = __('</strong><h3>WP Photo ALbum Plus Error message</h3>This is a multi site installation. One of the following 3 lines must be entered in wp-config.php:', 'wp-photo-album-plus');
	$errtxt .= __('<br /><br /><b>define( \'WPPA_MULTISITE_INDIVIDUAL\', true );</b> <small>// Multisite WP 3.5 or later with every site its own albums and photos</small>', 'wp-photo-album-plus');
	$errtxt .= __('<br /><b>define( \'WPPA_MULTISITE_BLOGSDIR\', true );</b> <small>// Multisite prior to WP 3.5 with every site its own albums and photos</small>', 'wp-photo-album-plus');
	$errtxt .= __('<br /><b>define( \'WPPA_MULTISITE_GLOBAL\', true );</b> <small>// Multisite with one common set of albums and photos</small>', 'wp-photo-album-plus');
	$errtxt .= __('<br /><br />For more information see: <a href="https://wordpress.org/plugins/wp-photo-album-plus/faq/">the faq</a>', 'wp-photo-album-plus');
	$errtxt .= __('<br /><br /><em>If you upload photos, they will be placed in the wrong location and will not be visible for visitors!</em><strong>', 'wp-photo-album-plus');

	wppa_error_message( $errtxt );
}

/* Check for pending maintenance procs */
if ( get_option( 'wppa_remake_index_photos_status', '' ) == __('Required', 'wp-photo-album-plus') ||
	 get_option( 'wppa_rerate_status', '' ) == __('Required', 'wp-photo-album-plus') ||
	 get_option( 'wppa_index_need_remake', 'no') == 'yes' ) {
		add_action('admin_notices', 'wppa_maintenance_messages');
}
function wppa_maintenance_messages() {
	if ( ! current_user_can( 'wppa_settings' ) ) {
		return;
	}

	if ( /* wppa_switch( 'indexed_search' ) && */ get_option( 'wppa_remake_index_photos_status' ) || get_option( 'wppa_index_need_remake', 'no') == 'yes' ) {
		wppa_error_message( __('</strong>The photo index table needs to be rebuilt. Please run <b>Photo Albums -> Settings</b> admin page <b>Table VIII-A9</b><strong>' , 'wp-photo-album-plus') );
		update_option( 'wppa_remake_index_photos_status', 'required' );
	}
	if ( /* wppa_switch( 'indexed_search' ) && */ get_option( 'wppa_remake_index_albums_status' ) || get_option( 'wppa_index_need_remake', 'no') == 'yes' ) {
		wppa_error_message( __('</strong>The album index table needs to be rebuilt. Please run <b>Photo Albums -> Settings</b> admin page <b>Table VIII-A8</b><strong>' , 'wp-photo-album-plus') );
		update_option( 'wppa_remake_index_albums_status', 'required' );
	}
	if ( wppa_switch( 'rating_on' ) && get_option( 'wppa_rerate_status' ) ) {
		wppa_error_message( __('</strong>The avarage ratings need to be recalculated. Please run <b>Photo Albums -> Settings</b> admin page <b>Table VIII-A5</b><strong>' , 'wp-photo-album-plus') );
	}
}

// Check if tags system needs conversion
add_action( 'admin_init', 'wppa_check_tag_system' );
function wppa_check_tag_system() {
global $wpdb;

	if ( current_user_can( 'wppa_settings' ) ) {
		$tag = $wpdb->get_var( "SELECT `tags` FROM `" . WPPA_PHOTOS . "` WHERE `tags` <> '' ORDER BY `id` DESC LIMIT 1" );
		if ( $tag ) {
			if ( substr( $tag, 0, 1 ) != ',' ) {
				add_action('admin_notices', 'wppa_tag_message');
				update_option( 'wppa_sanitize_tags_status', 'required' );
			}
		}
	}
}
function wppa_tag_message() {
	wppa_error_message( __('</strong>The tags system needs to be converted. Please run <b>Photo Albums -> Settings</b> admin page <b>Table VIII-B16</b><strong>' , 'wp-photo-album-plus') );
}
// Check if cats system needs conversion
add_action( 'admin_init', 'wppa_check_cat_system' );
function wppa_check_cat_system() {
global $wpdb;

	if ( current_user_can( 'wppa_settings' ) ) {
		$tag = $wpdb->get_var( "SELECT `cats` FROM `" . WPPA_ALBUMS . "` WHERE `cats` <> '' ORDER BY `id` DESC LIMIT 1" );
		if ( $tag ) {
			if ( substr( $tag, 0, 1 ) != ',' ) {
				add_action('admin_notices', 'wppa_cat_message');
				update_option( 'wppa_sanitize_cats_status', 'required' );
			}
		}
	}
}
function wppa_cat_message() {
	wppa_error_message( __('</strong>The cats system needs to be converted. Please run <b>Photo Albums -> Settings</b> admin page <b>Table VIII-B17</b><strong>' , 'wp-photo-album-plus') );
}
