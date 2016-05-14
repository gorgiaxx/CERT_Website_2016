<?php
require_once 'class-wpccm-position-table.php';
global $wpdb;

$raw = $wpdb->get_results("select * FROM `wp_position`");

$data = array();
foreach ($raw as $d) {
	$data[] = array(
		'ID' => $d->id,
		'position_name' => $d->position_name,
		'position_name_en' => $d->position_name_en,
		'weight' => $d->weight,
	);
}

//Prepare Table of elements
$wp_list_table = new WPCCM_Position_Table($data);
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
	职位列表
	<a href="<?php menu_page_url(WPCCM_POSITION_PAGE);?>&edit" class="add-new-h2">添加职位</a>
	</h2>
	<form action="" method="get">
		<input type="hidden" name="page" value="<?php echo WPCCM_POSITION_PAGE; ?>" />
		<?php $wp_list_table->display();?>
	</form>
</div>