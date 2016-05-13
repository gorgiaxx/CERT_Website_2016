<?php
/*
 * Settings Page, It's required by WPCCMPosition Class only.
 *
 */
global $wpdb;
function redirect() {
	$redirect = '<script type="text/javascript">';
	$redirect .= 'window.location = "' . menu_page_url(WPCCM_POSITION_PAGE, false) . '"';
	$redirect .= '</script>';
	echo $redirect;
}
/*
 * Delete positions
 */
if (isset($_GET['delete'])) {
	if (is_array($_GET['delete'])) {
		$current_id = implode(",", $_GET['delete']);
	} else {
		$current_id = $_GET['delete'];
	}
	if ($current_id != '') {
		$s = $wpdb->delete("wp_position", array('ID' => $current_id), array('%d'));
	}
	redirect();
} else {
	if (isset($_GET['edit'])) {
		$current_id = (int) $_GET['edit'];
		$position = $wpdb->get_row("SELECT * FROM wp_position WHERE id = " . $current_id);
	} else {
		$current_id = '';
	}
}

/*
 * Save positions
 */

if (isset($_POST['submit-save-exit']) || isset($_POST['submit-save'])) {
	$errors = new WP_Error();

	if (empty($_POST['position_name'])) {
		$errors->add("position_name", "请输入职位名！");
	}
	if (empty($_POST['position_name_en'])) {
		$errors->add("position_name_en", "请输入职位英文标识！");
	}
	if (empty($_POST['weight'])) {
		$errors->add("weight", "请输入权重！");
	}
	$position = array();
	$position["position_name"] = sanitize_text_field($_POST['position_name']);
	$position["position_name_en"] = sanitize_text_field($_POST['position_name_en']);
	$position["weight"] = intval($_POST['weight']);
	$position["create_time"] = date('Y-m-d H:i:s', time());

	if ($current_id == '') {
		$wpdb->insert(
			'wp_position',
			$position,
			array(
				'%s',
				'%s',
				'%d',
				'%s',
			)
		);
		$current_id = $wpdb->insert_id;
	} else {
		$wpdb->update(
			'wp_position',
			$position,
			array('ID' => $current_id)
		);
	}

	if (isset($_POST['submit-save-exit'])) {
		if (!empty($errors)) {
			redirect();
		}
	}
}

//Load content
require_once 'content.php';
?>
<link href="<?php echo WPCCM_PLUGIN_URL; ?>
/css/style.css" rel="stylesheet">
<link href="<?php echo WPCCM_PLUGIN_URL; ?>
/css/modal.css" rel="stylesheet">
<div class="wrap">
<?php echo $content['header']; ?>
<?php echo $content['tips_content']; ?>
<hr>
<h2><?php if (empty($_GET['edit'])) echo "添加职位"; else echo "编辑职位"; ?></h2>
<br>
<?php if (isset($errors) && is_wp_error($errors)): ?>
<div class="error">
	<p>
		<?php echo implode("</p>\n<p>", $errors->get_error_messages()); ?></p>
	</div>
	<?php endif;?>
	<div class="postbox">
		<div class="inside">
			<form action="" method="post" class="edit-template-form">
				<input type="hidden" name="edit" value="<?php echo $current_id; ?>
				" />
				<input type="hidden" name="page" value="<?php echo WPCCM_POSITION_PAGE; ?>
				" />

				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label>职位名</label>
						</th>
						<td>
							<input type="text" name="position_name" value="<?php echo @$position->position_name; ?>" class="middle-text"/>*</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label>英文标识</label>
						</th>
						<td>
							<input type="text" name="position_name_en" value="<?php echo @$position->position_name_en; ?>" class="middle-text"/>*</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label>权重</label>
						</th>
						<td>
							<input type="number" name="weight" value="<?php if(@$position->weight)echo @$position->weight; else echo "1"; ?>" class="small-text"/></td>
						</td>
					</tr>
				</table>

				<hr>
				<div class="func-submit">
					<?php submit_button('保存并返回', 'primary', 'submit-save-exit', false);?>
					&nbsp;&nbsp;
					<?php submit_button('保存', 'secondary', 'submit-save', false);?>
					&nbsp;
					<a href="<?php echo menu_page_url(WPCCM_POSITION_PAGE, false); ?>" class="button secondary">取消</a>
				</div>
				<div class="clear"></div>
				<?php if ($current_id != ''): ?>
				<div class="func-delete">
					<a href="<?php echo menu_page_url(WPCCM_POSITION_PAGE, false) . '&delete=' . $current_id; ?>">删除</a>
				</div>
				<?php endif;?></form>
		</div>
	</div>
</div>
<!--wrap-->
<!-- model -->
<div id="hide-modal" style="display: none; width:800px; position:absolute;" class="hide-modal-content">
	<div class="hide-modal-body"></div>
</div>