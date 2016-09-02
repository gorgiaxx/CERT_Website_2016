<?php
/*
 * Settings Page, It's required by WPCCMApplication Class only.
 *
 */
global $wpdb;
function redirect() {
	$redirect = '<script type="text/javascript">';
	$redirect .= 'window.location = "' . menu_page_url(WPCCM_APPLICATION_PAGE, false) . '"';
	$redirect .= '</script>';
	echo $redirect;
}
/*
 * Delete applications
 */
if (isset($_GET['delete'])) { 
	if (is_array($_GET['delete'])) {
		$current_id = implode(",", $_GET['delete']);
	} else {
		$current_id = intval($_GET['delete']);
	}
	if ($current_id != '') {
		$s = $wpdb->query("DELETE FROM wp_application WHERE id in (" . $current_id . ")");
	}
	redirect();
} else {
	if (isset($_GET['pass'])) {
		$current_id = (int) $_GET['pass'];
		$wpdb->update( 
			'wp_application', 
			array('pass' => TRUE), 
			array('ID' => $current_id)
		);
	} elseif (isset($_GET['unpass'])) {
		$current_id = (int) $_GET['unpass'];
		$wpdb->update( 
			'wp_application', 
			array('pass' => FALSE), 
			array('ID' => $current_id)
		);
	} elseif (isset($_GET['check'])) {
		$current_id = (int) $_GET['check'];
		$wpdb->update( 
			'wp_application', 
			array('check' => TRUE),
			array('ID' => $current_id)
		);
	} elseif (isset($_GET['uncheck'])) {
		$current_id = (int) $_GET['uncheck'];
		$wpdb->update( 
			'wp_application', 
			array('check' => FALSE), 
			array('ID' => $current_id)
		);
	}
	redirect();
}