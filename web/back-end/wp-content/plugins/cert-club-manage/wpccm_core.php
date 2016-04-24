<?php
/**
 * Plugin Name: CERT Club Manage
 * Plugin URI: http://www.ecjtu.org
 * Description: 计算机紧急响应组社团管理插件
 * Version: 1.0.0
 * Author: Gorgiaxx
 * Author URI: http://blog.gorgiaxx.com
 * License: GPLv2 or later
 * Text Domain: WPCCM
 */

if (!defined('ABSPATH')) {
	exit;
}

define('WPCCM_PLUGIN_URL', plugins_url('', __FILE__));
define('WPCCM_MEMBER_PAGE', 'wpccm-member-page');
define('WPCCM_DEPART_PAGE', 'wpccm-depart-page');
define('WPCCM_POSITION_PAGE', 'wpccm-position-page');
define('WPCCM_APPLICATION_PAGE', 'wpccm-application-page');
define('WPCCM_LEADERSHIP_PAGE', 'wpccm-leadership-page');
define('WPCCM_PRODUCT_PAGE', 'wpccm-product-page');
define('WPCCM_SETTINGS_PAGE', 'wpccm-settings-page');
define('WPCCM_SETTINGS_OPTION', 'wpccm_settings_option');
define('SELECT_ROWS_AMOUNT', 100);
define('SYNC_TITLE_LIMIT', 50);
define('SYNC_CONTENT_LIMIT', 300);
define('SYNC_EXCERPT_LIMIT', 100);
define('MAX_SEARCH_LIMIT', 6);

//utils
function trim_words($str, $limit, $suffix = '...', $db_charset = DB_CHARSET, $strip_tags = true) {
	if ($strip_tags) {
		$str = strip_tags($str);
	}
	if (strpos($db_charset, "utf8") !== false
		|| strpos($db_charset, "utf8") !== false) {
		$db_charset = "utf8";
	}
	$new_str = mb_substr($str, 0, $limit, $db_charset);
	$new_str = mb_strlen($str, $db_charset) > $limit ? $new_str . $suffix : $new_str;
	return $new_str;
}

//Interface
$options = get_option(WPCCM_SETTINGS_OPTION);
global $token;
$token = isset($options['token']) ? $options['token'] : '';

//Setup wechat image size
function set_wechat_img_size() {
	add_image_size('sup_wechat_big', 360, 200, true);
	add_image_size('sup_wechat_small', 200, 200, true);
}
add_action('after_setup_theme', 'set_wechat_img_size');

function sup_wechat_custom_sizes($sizes) {
	return array_merge($sizes, array(
		'sup_wechat_big' => __('WeChat big image', 'WPCCM'),
		'sup_wechat_small' => __('WeChat small image', 'WPCCM'),
	));
}
add_filter('image_size_names_choose', 'sup_wechat_custom_sizes');

//Setup Admin
add_action('_admin_menu', 'wpccm_admin_setup');
function wpccm_admin_setup() {
	global $user_level;
	if ($user_level >= 5) {

		$page_title = __('社团管理', 'WPCCM');
		$menu_title = __('社团管理', 'WPCCM');
		$capability = 'edit_pages';
		$menu_slug = WPCCM_MEMBER_PAGE;
		$function = '';
		$icon_url = WPCCM_PLUGIN_URL . '/img/shield_icon_16.png';
		add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url);

		require_once 'class-wpccm-settings.php';
		require_once 'class-wpccm-member.php';
		require_once 'class-wpccm-depart.php';
		require_once 'class-wpccm-position.php';
		require_once 'class-wpccm-application.php';
		require_once 'class-wpccm-leadership.php';
		require_once 'class-wpccm-product.php';
		//Settings
		$settingObject = WPCCM_Settings::get_instance();
		//Member
		$memberObject = WPCCM_Member::get_instance();
		//Depart
		$departObject = WPCCM_Depart::get_instance();
		//Position
		$positionObject = WPCCM_Position::get_instance();
		//Application
		$applicationObject = WPCCM_Application::get_instance();
		//Leadership
		$leadershipObject = WPCCM_Leadership::get_instance();
		//Product
		$productObject = WPCCM_Product::get_instance();
	}
}

//AJAX handle
//Safe Redirect
add_action('admin_init', 'ajax_handle', 999);
function ajax_handle() {
	require_once 'ajax_request_handle.php';
}

//Safe Redirect
add_action('admin_init', 'safe_redirect', 999);
function safe_redirect() {
	if (isset($_GET['_wp_http_referer'])) {
		wp_redirect(remove_query_arg(array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI'])));
	}

}
//Scripts
add_action('admin_print_scripts', 'custom_admin_scripts');
//add custom upload jquery support.
function custom_admin_scripts() {
	wp_enqueue_script('jquery');
	wp_enqueue_media();
	wp_register_script('custom-upload', WPCCM_PLUGIN_URL . '/js/custom_upload.js', array('jquery', 'media-upload', 'thickbox'), "2.0");
	wp_enqueue_script('custom-upload');
	wp_register_script('modal', WPCCM_PLUGIN_URL . '/js/modal.js', array(), "2.0");
	wp_enqueue_script('modal');
}
// Add settings link on plugin page
function wpccm_plugin_settings_link($links) {
	$settings_link = '<a href="' . menu_page_url(WPCCM_SETTINGS_PAGE, false) . '">设置</a>';
	array_unshift($links, $settings_link);
	return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'wpccm_plugin_settings_link');
?>
