<?php
class WPCCM_Position {

	private $file_position_tpl = '_position.php';
	private $file_position_handle_tpl = '_position_handle.php';

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
	}

	/**
	 * Add page
	 */
	public function add_plugin_page() {
		// This page will be under Content manage section.
		$parent_slug = WPCCM_MEMBER_PAGE;
		$page_title = __('社团管理', 'WPCCM');
		$menu_title = __('职位管理', 'WPCCM');
		$capability = 'edit_pages';
		$menu_slug = WPCCM_POSITION_PAGE;
		add_submenu_page(
			$parent_slug,
			$page_title,
			$menu_title,
			$capability,
			$menu_slug,
			array($this, 'create_admin_page')
		);
	}
	public function create_admin_page() {
		if (isset($_GET['edit']) || isset($_GET['delete'])) {
			require_once $this->file_position_handle_tpl;
		} else {
			require_once $this->file_position_tpl;
		}
	}

}

?>