<?php
global $wpdb;
ini_set('memory_limit', '48M');
ini_set('max_execution_time', 120);
$extensions = array('xls' => '.xls', 'xlsx' => '.xlsx');

if (empty($_POST['Submit']) || !check_admin_referer('e2e_export_data')) {
	wp_die('你想做什么？大黑阔？');
} elseif (!isset($_POST['ext']) || !array_key_exists($_POST['ext'], $extensions)) {
	wp_die('请选择文件扩展名');
} elseif (!isset($_POST['date_begin']) || !isset($_POST['date_end'])) {
	wp_die('请选择时间范围');
} elseif (!isset($_POST['department_id'])) {
	wp_die('请选择部门');
} else {
	$date_begin = $_POST['date_begin'];
	$date_end = $_POST['date_end'];
	$department_id = $_POST['department_id'];
	$ext = $_POST['ext'];
	$str = '<table border="1"><tr>';
	$str .= '<td colspan=6 align="center">计算机紧急响应组成员表</td>';
	$str .= '</tr><tr><td>学号</td><td>姓名</td><td>班级</td><td>部门</td><td>职位</td><td>手机号</td></tr>';
	$members = $wpdb->get_results("select wp_member.*,wp_department.department_name,wp_position.position_name from wp_member LEFT JOIN wp_department ON wp_member.department_id = wp_department.id LEFT JOIN wp_position ON wp_member.position_id = wp_position.id;");

	foreach ($members as $row) {
		$str .= '<tr><td style="vnd.ms-excel.numberformat:@">' . $row->student_id . "</td><td>" . $row->username . "</td><td>" . $row->classname . "</td><td>" . $row->department_name . "</td><td>" . $row->position_name . "</td><td style='vnd.ms-excel.numberformat:@'>" . $row->phone_number . '</td></tr>';
	}

	$filename = '计算机紧急响应组成员表.' . $ext;
	if ($ext == 'xls') {
		header("Content-type: application/vnd.ms-excel;");
	} elseif ($ext == 'xlsx') {
		header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, charset=utf-8;");
	}
	header("Content-Disposition: attachment; filename=" . $filename);
	print $str; //$str variable is used in loop.php

	exit();
}
?>