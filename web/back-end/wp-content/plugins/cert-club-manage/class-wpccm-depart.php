<?php
class WPCCM_Depart {

	private $file_depart_tpl = '_depart.php';
	private $file_depart_handle_tpl = '_depart_handle.php';

	private static $_instance;

	/**
	 * Start up
	 */

	public static function get_instance() {

		if (!isset(self::$_instance)) {
			$c = __CLASS__;
			self::$_instance = new $c;
		}
		return self::$_instance;
	}

	public function __clone() {
		trigger_error('Clone is not allow', E_USER_ERROR);
	}

	private function __construct() {
		add_action('admin_menu', array($this, 'add_plugin_page'));
		add_action('admin_print_scripts', 'custom_admin_scripts');
		wp_enqueue_media();
		wp_register_script('custom-upload', WPCCM_PLUGIN_URL . '/js/custom_upload.js', array('jquery', 'media-upload', 'thickbox'), "2.0");
		wp_enqueue_script('custom-upload');
	}

	/**
	 * Add page
	 */
	public function add_plugin_page() {
		// This page will be under Content manage section.
		$parent_slug = WPCCM_MEMBER_PAGE;
		$page_title = __('社团管理', 'WPCCM');
		$menu_title = __('部门管理', 'WPCCM');
		$capability = 'edit_pages';
		$menu_slug = WPCCM_DEPART_PAGE;
		add_submenu_page(
			$parent_slug,
			$page_title,
			$menu_title,
			$capability,
			$menu_slug,
			array($this, 'create_admin_page')
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page() {
		if (isset($_GET['edit']) || isset($_GET['delete'])) {
			require_once $this->file_depart_handle_tpl;
		} else {
			require_once $this->file_depart_tpl;
		}
	}

}

?>