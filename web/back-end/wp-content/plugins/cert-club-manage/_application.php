<?php
require_once 'class-wpccm-application-table.php';
global $wpdb;

$raw = $wpdb->get_results("select a.*,d.department_name FROM `wp_application` AS a LEFT JOIN `wp_department` AS d ON a.department_id=d.id");

$data = array();
foreach ($raw as $d) {
	$data[] = array(
		'ID' => $d->id,
		'student_id' => $d->student_id,
		'username' => $d->username,
		'classname' => $d->classname,
		'introduction' => $d->introduction,
		'department_name' => $d->department_name,
		'email' => $d->email,
		'phone_number' => $d->phone_number,
		'pass' => $d->pass,
		'check' => $d->check,
		'create_time' => $d->create_time,
	);
}

//Prepare Table of elements
$wp_list_table = new WPCCM_Application_Table($data);
$wp_list_table->prepare_items();

//Load content
require_once 'content.php';
?>
<link href="<?php echo WPCCM_PLUGIN_URL; ?>/css/style.css" rel="stylesheet">
<div class="wrap">
	<?php echo $content['header']; ?>
	<?php echo $content['tips_content']; ?>
	<p class="header_func">
		<?php if (current_user_can('manage_options')): ?>
		<a href="<?php menu_page_url(WPCCM_SETTINGS_PAGE);?>">设置</a>
		<?php endif;?>
	</p>
	<hr>
	<h2>
	申请列表
	<a href="<?php menu_page_url(WPCCM_APPLICATION_PAGE);?>&edit" class="add-new-h2">一个按钮</a>
	</h2>
	<br>
	<form action="" method="get">
		<input type="hidden" name="page" value="<?php echo WPCCM_APPLICATION_PAGE; ?>" />
		<?php $wp_list_table->display();?>
	</form>
</div>