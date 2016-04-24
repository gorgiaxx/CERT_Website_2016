<?php
class WPCCM_Member {

	private $file_member_tpl = '_member.php';
	private $file_member_handle_tpl = '_member_handle.php';

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
	}

	/**
	 * Add page
	 */
	public function add_plugin_page() {
		// This page will be under Content manage section.
		$parent_slug = WPCCM_MEMBER_PAGE;
		$page_title = __('社团管理', 'WPCCM');
		$menu_title = __('成员管理', 'WPCCM');
		$capability = 'edit_pages';
		$menu_slug = WPCCM_MEMBER_PAGE;
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
			require_once $this->file_member_handle_tpl;
		} else {
			require_once $this->file_member_tpl;
		}
	}

}

?>