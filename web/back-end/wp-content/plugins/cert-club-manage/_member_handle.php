<?php
/*
 * Settings Page, It's required by WPCCMMember Class only.
 *
 */
global $wpdb;
function redirect() {
	$redirect = '<script type="text/javascript">';
	$redirect .= 'window.location = "' . menu_page_url(WPCCM_MEMBER_PAGE, false) . '"';
	$redirect .= '</script>';
	echo $redirect;
}
/*
 * Delete members
 */
if (isset($_GET['delete'])) {
	if (is_array($_GET['delete'])) {
		$current_id = implode(",", $_GET['delete']);
	} else {
		$current_id = $_GET['delete'];
	}
	if ($current_id != '') {
		$s = $wpdb->delete("wp_member", array('ID' => $current_id), array('%d'));
	}
	redirect();
} else {
	$department = $wpdb->get_results("SELECT id,department_name FROM wp_department");
	$position = $wpdb->get_results("SELECT id,position_name FROM wp_position");
	if (isset($_GET['edit'])) {
		$current_id = (int) $_GET['edit'];
		$member = $wpdb->get_row("SELECT * FROM wp_member WHERE id = " . $current_id);
	} else {
		$current_id = '';
	}
}

/*
 * Save members
 */

if (isset($_POST['submit-save-exit']) || isset($_POST['submit-save'])) {
	$errors = new WP_Error();

	if (empty($_POST['student_id'])) {
		$errors->add("student_id", "请输入学号！");
	}
	if (empty($_POST['username'])) {
		$errors->add("username", "请输入姓名！");
	}
	if (empty($_POST['classname'])) {
		$errors->add("classname", "请输入班级名！");
	}
	if (empty($_POST['department_id'])) {
		$errors->add("department_id", "请选择部门！");
	}
	if (empty($_POST['position_id'])) {
		$errors->add("position_id", "请选择职位！");
	}
	if (empty($_POST['join_time'])) {
		$errors->add("join_time", "请输入加入日期！");
	}
	$member = array();
	$member["student_id"] = sanitize_text_field($_POST['student_id']);
	$member["username"] = sanitize_text_field($_POST['username']);
	$member["classname"] = sanitize_text_field($_POST['classname']);
	$member["department_id"] = intval($_POST['department_id']);
	$member["position_id"] = intval($_POST['position_id']);
	$member["join_time"] = sanitize_text_field($_POST['join_time']);
	$member["face_url"] = @sanitize_text_field($_POST['face_url']);
	$member["introduction"] = @sanitize_text_field($_POST['introduction']);
	$member["link"] = @sanitize_text_field($_POST['link']);
	$member["show_depart"] = @(bool) $_POST['show_depart'];
	$member["show_famehall"] = @(bool) $_POST['show_famehall'];
	$member["create_time"] = date('Y-m-d H:i:s', time());

	if ($current_id == '') {
		$wpdb->insert(
			'wp_member',
			$member,
			array(
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
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
			'wp_member',
			$member,
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
<h2>添加成员</h2>
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
				<input type="hidden" name="page" value="<?php echo WPCCM_MEMBER_PAGE; ?>
				" />
				<h3>基本信息</h3>

				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label>学号</label>
						</th>
						<td>
							<input type="text" name="student_id" value="<?php echo @$member->student_id; ?>" class="middle-text"/></td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label>姓名</label>
						</th>
						<td>
							<input type="text" name="username" value="<?php echo @$member->username; ?>" class="middle-text"/></td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label>班级</label>
						</th>
						<td>
							<input type="text" name="classname" value="<?php echo @$member->classname; ?>" class="middle-text"/></td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label>加入时间</label>
						</th>
						<td>
							<input type="date" name="join_time" value="<?php echo @$member->join_time; ?>" class="middle-text"/></td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label>部门</label>
						</th>
						<td>
							<select name="department_id" id="msg_type">
								<option value="">请选择部门</option>
								<?php foreach ($department as $val): ?>
								<?php $selected = ($val->id == @$member->department_id) ? 'selected' : '';?>
								<option value="<?php echo $val->id; ?>"<?php echo $selected; ?>>
									<?php echo $val->department_name; ?></option>
								<?php endforeach;?></select>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label>职位</label>
						</th>
						<td>
							<select name="position_id" id="msg_type">
								<option value="">请选择职位</option>
								<?php foreach ($position as $val): ?>
								<?php $selected = ($val->id == @$member->position_id) ? 'selected' : '';?>
								<option value="<?php echo $val->id; ?>"<?php echo $selected; ?>><?php echo $val->position_name; ?></option>
								<?php endforeach;?></select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label>是否在部门展示里显示</label>
						</th>
						<td>
							<label>
								<input type="checkbox" name="show_depart" value="publish" <?php echo @$member->show_depart ? 'checked' : ''; ?>
								/>显示
							</label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label>是否在名人堂里显示</label>
						</th>
						<td>
							<label>
								<input type="checkbox" name="show_famehall" value="publish" <?php echo @$member->show_famehall ? 'checked' : ''; ?>
								/>显示
							</label>
						</td>
					</tr>
				</table>
				<div>
					<hr>
					<h3>扩展信息</h3>
					<div class="msg-box">
						<table class="form-table">
							<tr valign="top">
								<th scope="row">
									<label>头像</label>
								</th>
								<td>
									<img id="upload_face" src="" style="width: 80px;height: 80px;" alt="点击上传">
									<input type="hidden" name="face_url" value="<?php echo @$member->link; ?>" class="middle-text"/>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label>博客链接</label>
								</th>
								<td>
									<input type="text" name="link" value="<?php echo @$member->link; ?>" class="middle-text"/>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label>个人介绍</label>
								</th>
								<td>
									<textarea id="resp_msg_textarea" name="introduction" rows="10" class="large-text"><?php echo @$member->introduction; ?></textarea>
								</td>
							</tr>
						</table>
					</div>
				</div>

				<hr>
				<div class="func-submit">
					<?php submit_button('保存并返回', 'primary', 'submit-save-exit', false);?>
					&nbsp;&nbsp;
					<?php submit_button('保存', 'secondary', 'submit-save', false);?>
					&nbsp;
					<a href="<?php echo menu_page_url(WPCCM_MEMBER_PAGE, false); ?>" class="button secondary">取消</a>
				</div>
				<div class="clear"></div>
				<?php if ($current_id != ''): ?>
				<div class="func-delete">
					<a href="<?php echo menu_page_url(WPCCM_MEMBER_PAGE, false) . '&delete=' . $current_id; ?>">删除</a>
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