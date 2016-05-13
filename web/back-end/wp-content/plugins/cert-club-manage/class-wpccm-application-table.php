<?php
class WPCCM_Application_Table extends WP_List_Table {

	private $rawData = array();
	private $found_data = array();

	public function __construct($data) {
		global $status, $page;
		$this->rawData = $data;
		parent::__construct(array(
			'singular' => 'tpl', //singular name of the listed records
			'plural' => 'tpls', //plural name of the listed records
			'ajax' => false, //does this table support ajax?
		));

	}

	public function no_items() {
		return "没有内容";
	}

	public function column_default($item, $column_name) {
		switch ($column_name) {
		case 'student_id':
		case 'username':
		case 'classname':
		case 'introduction':
		case 'department_name':
		case 'email':
		case 'phone_number':
		case 'pass':
		case 'check':
		case 'create_time':
			return $item[$column_name];
		default:
			return print_r($item, true); //Show the whole array for troubleshooting purposes
		}
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'create_time' => array('create_time', false),
			'student_id' => array('student_id', false),
		);
		return $sortable_columns;
	}

	public function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'student_id' => '学号',
			'username' => '姓名',
			'classname' => '班级',
			'introduction' => '介绍',
			'department_name' => '意向部门',
			'email' => '邮箱',
			'phone_number' => '手机号',
			'pass' => '面试通过',
			'check' => '考核通过',
			'create_time' => '申请时间',
			'action' => '操作',
		);
		return $columns;
	}

	public function usort_reorder($a, $b) {
		// If no sort, default to title
		$orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'ID';
		// If no order, default to asc
		$order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
		// Determine sort order
		$result = strcmp($a[$orderby], $b[$orderby]);
		// Send final sort direction to usort
		return ($order === 'asc') ? $result : -$result;
	}

	public function get_bulk_actions() {
		$actions = array(
			'delete' => "删除",
			// 'enable' => "通过面试",
			// 'disable' => "取消面试",慎重对待，不做批量
		);
		return $actions;
	}

	public function column_cb($item) {
		return sprintf(
			'<input type="checkbox" name="delete[]" value="%s" />', $item['ID']
		);
	}

	public function column_pass($item) {
		if ($item['pass'] == 0) {
			return "未通过";
		} else {
			return "已通过";
		}
	}

	public function column_check($item) {
		if ($item['check'] == 0) {
			return "未通过";
		} else {
			return "已通过";
		}
	}

	public function column_background($item) {
		if (!empty($item['background'])) {
			return sprintf(
			'<img src="%s" style="width:86px;height:auto;"/>',$item['background'] 
		);
		} else {
			return "";
		}
	}

	public function column_action($item) {
		$string = '<a href="' . menu_page_url(WPCCM_APPLICATION_PAGE, false) . '&delete=%s">删除</a><br />';
		$string .= '<a href="' . menu_page_url(WPCCM_APPLICATION_PAGE, false) . '&pass=%s">通过面试</a>&nbsp;&nbsp;';
		$string .= '<a href="' . menu_page_url(WPCCM_APPLICATION_PAGE, false) . '&check=%s">通过考核</a><br />';
		$string .= '<a href="' . menu_page_url(WPCCM_APPLICATION_PAGE, false) . '&unpass=%s">取消面试</a>&nbsp;&nbsp;';
		$string .= '<a href="' . menu_page_url(WPCCM_APPLICATION_PAGE, false) . '&uncheck=%s">取消考核</a>';
		return sprintf($string, $item['ID'],$item['ID'],$item['ID'],$item['ID'],$item['ID']);
	}

	public function extra_tablenav($which) {
		if ($which == "top") {
			//The code that goes before the table is here
			//echo 'top';
		}
		if ($which == "bottom") {
			//The code that goes after the table is there
			//echo 'bottom';
		}
	}

	public function prepare_items() {
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		usort($this->rawData, array(&$this, 'usort_reorder'));
		$per_page = 10;
		$current_page = $this->get_pagenum();

		$total_items = count($this->rawData);

		// only ncessary because we have sample data
		$current_page_idx = ($current_page - 1) * $per_page;
		$this->found_data = array_slice($this->rawData,
			$current_page_idx,
			$per_page);

		$this->set_pagination_args(array(
			'total_items' => $total_items,
			//WE have to calculate the total number of items
			'per_page' => $per_page,
			//WE have to determine how many items to show on a page
		));
		$this->items = $this->found_data;
	}
}

?>