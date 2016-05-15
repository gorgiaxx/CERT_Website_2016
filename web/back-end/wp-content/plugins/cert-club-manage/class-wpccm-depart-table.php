<?php
class WPCCM_Depart_Table extends WP_List_Table {

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
		case 'department_name':
		case 'department_name_en':
		case 'brief':
		case 'introduction':
		case 'background':
		case 'orders':
		case 'flag':
		case 'face_url':
			return $item[$column_name];
		default:
			return print_r($item, true); //Show the whole array for troubleshooting purposes
		}
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'orders' => array('orders', false),
			'department_name' => array('ID', false),
		);
		return $sortable_columns;
	}

	public function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'department_name' => '部门名',
			'department_name_en' => '部门英文标识',
			'brief' => '简介',
			'introduction' => '介绍',
			'background' => '背景图',
			'orders' => '顺序',
			'flag' => '状态',
			'action' => '操作',
		);
		return $columns;
	}

	public function usort_reorder($a, $b) {
		// If no sort, default to title
		$orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'orders';
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
			// 'enable' => "启用",
			// 'disable' => "停用",
		);
		return $actions;
	}

	public function column_cb($item) {
		return sprintf(
			'<input type="checkbox" name="delete[]" value="%s" />', $item['ID']
		);
	}

	public function column_brief($item) {
		return sprintf(
			'<p style="overflow: hidden; white-space: nowrap;  text-overflow: ellipsis;">%s</p>', $item['brief']
		);
	}
	
	public function column_introduction($item) {
		return sprintf(
			'<p style="overflow: hidden; white-space: nowrap;  text-overflow: ellipsis;">%s</p>', $item['introduction']
		);
	}

	public function column_flag($item) {
		if ($item['flag'] == 0) {
			return "停用";
		} else {
			return "启用";
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
		return sprintf(
			'<a href="' . menu_page_url(WPCCM_DEPART_PAGE, false) . '&edit=%s">编辑</a>&nbsp;<a href="' . menu_page_url(WPCCM_DEPART_PAGE, false) . '&delete=%s">删除</a>', $item['ID'], $item['ID']
		);
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