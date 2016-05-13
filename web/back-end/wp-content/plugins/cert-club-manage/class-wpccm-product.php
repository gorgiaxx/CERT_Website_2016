<?php
class WPCCM_Product {

	private $file_product_tpl = '_product.php';
	private $file_product_handle_tpl = '_product_handle.php';


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
		$menu_title = __('团队作品', 'WPCCM');
		$capability = 'edit_pages';
		$menu_slug = WPCCM_PRODUCT_PAGE;
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
		if (isset($_GET['delete']) || isset($_GET['edit']) ) {
			require_once $this->file_product_handle_tpl;
		} else {
			require_once $this->file_product_tpl;
		}
	}

}

?>