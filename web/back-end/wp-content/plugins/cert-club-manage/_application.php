<?php
require_once 'class-wpccm-application-table.php';
global $wpdb;

if (@$_GET['sort'] == 'pass') {
	$sort = " where a.pass=1 and a.check=0";
	$pass_current = ' class="current"';
} elseif (@$_GET['sort'] == 'check') {
	$sort = " where a.check=1";
	$check_current = ' class="current"';
} elseif (@$_GET['sort'] == 'unpass') {
	$sort = " where a.pass=0";
	$unpass_current = ' class="current"';
} else {
	$sort = "";
	$all_current = ' class="current"';
}
$raw = $wpdb->get_results("select a.*,d.department_name FROM `wp_application` AS a LEFT JOIN `wp_department` AS d ON a.department_id=d.id" . $sort);

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
	<ul class="subsubsub">
		<li class="all"><a href="<?php menu_page_url(WPCCM_APPLICATION_PAGE);?>"<?php echo @$all_current;?>>全部</a> |</li>
		<li class="check"><a href="<?php menu_page_url(WPCCM_APPLICATION_PAGE);?>&sort=check"<?php echo @$check_current;?>>通过考核</a> |</li>
		<li class="pass"><a href="<?php menu_page_url(WPCCM_APPLICATION_PAGE);?>&sort=pass"<?php echo @$pass_current;?>>只通过面试</a> |</li>
		<li class="unpass"><a href="<?php menu_page_url(WPCCM_APPLICATION_PAGE);?>&sort=unpass"<?php echo @$unpass_current;?>>未通过面试</a></li>
	</ul>
	<form action="" method="get">
		<input type="hidden" name="page" value="<?php echo WPCCM_APPLICATION_PAGE; ?>" />
		<?php $wp_list_table->display();?>
	</form>
</div>