<?php
require_once 'class-wpccm-member-table.php';
global $wpdb;

if (isset($_GET['s'])) {
	$keyword = "where wp_member.username like '%" . sanitize_text_field($_GET['s']) . "%'";
} else {
	$keyword = '';
}
$raw = $wpdb->get_results("select wp_member.*,wp_department.department_name,wp_position.position_name from wp_member LEFT JOIN wp_department ON wp_member.department_id = wp_department.id LEFT JOIN wp_position ON wp_member.position_id = wp_position.id " . $keyword);

$data = array();
$department = $wpdb->get_results("SELECT id,department_name FROM wp_department");
$position = $wpdb->get_results("SELECT id,position_name FROM wp_position");
foreach ($raw as $d) {
	$data[] = array(
		'ID' => $d->id,
		'student_id' => $d->student_id,
		'username' => $d->username,
		'classname' => $d->classname,
		'department' => $d->department_name,
		'position' => $d->position_name,
		'join_time' => $d->join_time,
		'face_url' => $d->face_url,
		'introduction' => $d->introduction,
		'link' => $d->link,
	);
}

//Prepare Table of elements
$wp_list_table = new WPCCM_Member_Table($data);
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
	成员列表
	<a href="<?php menu_page_url(WPCCM_MEMBER_PAGE);?>&edit" class="add-new-h2">添加成员</a>
	</h2>
	<br>
	<form action="" method="get">
		<?php $wp_list_table->search_box("请输入姓名", 'username');?>
		<input type="hidden" name="page" value="<?php echo WPCCM_MEMBER_PAGE; ?>" />
		<?php $wp_list_table->display();?>
	</form>
</div>