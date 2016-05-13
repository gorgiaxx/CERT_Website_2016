<?php
require_once 'class-wpccm-depart-table.php';
global $wpdb;

$raw = $wpdb->get_results("select * FROM `wp_department` order by `orders` ASC");

$data = array();
foreach ($raw as $d) {
	$data[] = array(
		'ID' => $d->id,
		'department_name' => $d->department_name,
		'department_name_en' => $d->department_name_en,
		'brief' => $d->brief,
		'introduction' => $d->introduction,
		'background' => $d->background,
		'orders' => $d->orders,
		'flag' => $d->flag,
	);
}

//Prepare Table of elements
$wp_list_table = new WPCCM_Depart_Table($data);
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
	部门列表
	<a href="<?php menu_page_url(WPCCM_DEPART_PAGE);?>&edit" class="add-new-h2">添加部门</a>
	</h2>
	<br>
	<form action="" method="get">
		<input type="hidden" name="page" value="<?php echo WPCCM_DEPART_PAGE; ?>" />
		<?php $wp_list_table->display();?>
	</form>
</div>