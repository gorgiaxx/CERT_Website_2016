<?php
/*
 * Settings Page, It's required by WPCCMDepart Class only.
 *
 */
global $wpdb;
function redirect() {
	$redirect = '<script type="text/javascript">';
	$redirect .= 'window.location = "' . menu_page_url(WPCCM_DEPART_PAGE, false) . '"';
	$redirect .= '</script>';
	echo $redirect;
}
/*
 * Delete departs
 */
if (isset($_GET['delete'])) {
	if (is_array($_GET['delete'])) {
		$current_id = implode(",", $_GET['delete']);
	} else {
		$current_id = $_GET['delete'];
	}
	if ($current_id != '') {
		$s = $wpdb->delete("wp_depart", array('ID' => $current_id), array('%d'));
	}
	redirect();
} else {
	if (isset($_GET['edit'])) {
		$current_id = (int) $_GET['edit'];
		$depart = $wpdb->get_row("SELECT * FROM wp_department WHERE id = " . $current_id);
	} else {
		$current_id = '';
	}
}

/*
 * Save departs
 */

if (isset($_POST['submit-save-exit']) || isset($_POST['submit-save'])) {
	$errors = new WP_Error();

	if (empty($_POST['department_name'])) {
		$errors->add("department_name", "请输入部门名！");
	}
	if (empty($_POST['department_name_en'])) {
		$errors->add("department_name_en", "请输入部门英文标识！");
	}
	if (empty($_POST['brief'])) {
		$errors->add("brief", "请输入部门简介！");
	}
	if (empty($_POST['introduction'])) {
		$errors->add("introduction", "请输入部门详细介绍！");
	}
	if (empty($_POST['background'])) {
		$errors->add("background", "请选择部门！");
	}
	$depart = array();
	$depart["department_name"] = sanitize_text_field($_POST['department_name']);
	$depart["department_name_en"] = sanitize_text_field($_POST['department_name_en']);
	$depart["brief"] = sanitize_text_field($_POST['brief']);
	$depart["introduction"] = sanitize_text_field($_POST['introduction']);
	$depart["background"] = sanitize_text_field($_POST['background']);
	$depart["flag"] = intval($_POST['flag']);
	$depart["orders"] = intval($_POST['orders']);
	$depart["create_time"] = date('Y-m-d H:i:s', time());

	if ($current_id == '') {
		$wpdb->insert(
			'wp_department',
			$depart,
			array(
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%d',
				'%s',
			)
		);
		$current_id = $wpdb->insert_id;
	} else {
		$wpdb->update(
			'wp_department',
			$depart,
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
<h2><?php if (empty($_GET['edit'])) echo "添加部门"; else echo "编辑部门"; ?></h2>
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
				<input type="hidden" name="page" value="<?php echo WPCCM_DEPART_PAGE; ?>
				" />

				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label>部门名</label>
						</th>
						<td>
							<input type="text" name="department_name" value="<?php echo @$depart->department_name; ?>" class="middle-text"/>*</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label>英文标识</label>
						</th>
						<td>
							<input type="text" name="department_name_en" value="<?php echo @$depart->department_name_en; ?>" class="middle-text"/>*</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label>简介</label>
						</th>
						<td>
							<input type="text" name="brief" value="<?php echo @$depart->brief; ?>" class="large-text"/>*</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label>详细介绍</label>
						</th>
						<td>
							<textarea id="resp_msg_textarea" name="introduction" rows="10" class="large-text"><?php echo @$depart->introduction; ?></textarea>*
					</tr>

					<tr valign="top">
						<th scope="row">
							<label>主题背景</label>
						</th>
						<td>
							<img id="upload_background" src="<?php echo $depart->background; ?>" style="width: 320px;height: 240px;" alt="点击上传">*
							<input type="hidden" name="background" value="<?php echo $depart->background; ?>"/>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label>顺序</label>
						</th>
						<td>
							<input type="number" name="orders" value="<?php if(@$depart->orders)echo @$depart->orders; else echo "1"; ?>" class="small-text"/></td>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label>是否启用部门</label>
						</th>
						<td>
							<label>
								<input type="checkbox" name="flag" <?php echo @$depart->flag ? 'checked' : ''; ?>
								/>启用
							</label>
						</td>
					</tr>
				</table>

				<hr>
				<div class="func-submit">
					<?php submit_button('保存并返回', 'primary', 'submit-save-exit', false);?>
					&nbsp;&nbsp;
					<?php submit_button('保存', 'secondary', 'submit-save', false);?>
					&nbsp;
					<a href="<?php echo menu_page_url(WPCCM_DEPART_PAGE, false); ?>" class="button secondary">取消</a>
				</div>
				<div class="clear"></div>
				<?php if ($current_id != ''): ?>
				<div class="func-delete">
					<a href="<?php echo menu_page_url(WPCCM_DEPART_PAGE, false) . '&delete=' . $current_id; ?>">删除</a>
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