<?php
/* -------------------------------------------- *
 * Class Definition		*
 * -------------------------------------------- */

//This part is our very own WP_List_Table

class WPCCM_Member_Table extends WP_List_Table {

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
		case 'phone_number':
		case 'classname':
		case 'department':
		case 'position':
		case 'join_time':
		case 'face_url':
		case 'introduction':
			return $item[$column_name];
		default:
			return print_r($item, true); //Show the whole array for troubleshooting purposes
		}
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'department' => array('department', false),
			'position' => array('position', false),
			'join_time' => array('join_time', false),
		);
		return $sortable_columns;
	}

	public function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'student_id' => '学号',
			'username' => '姓名',
			'phone_number' => '手机号',
			'classname' => '班级名',
			'department' => '部门',
			'position' => '职位',
			'join_time' => '加入时间',
			'face_url' => '头像',
			'introduction' => '自我介绍',
			'action' => '操作',
		);
		return $columns;
	}

	public function usort_reorder($a, $b) {
		// If no sort, default to title
		$orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'ID';
		// If no order, default to asc
		$order = (!empty($_GET['order'])) ? $_GET['order'] : 'desc';
		// Determine sort order
		$result = strcmp($a[$orderby], $b[$orderby]);
		// Send final sort direction to usort
		return ($order === 'asc') ? $result : -$result;
	}

	public function get_bulk_actions() {
		$actions = array(
			'delete' => "删除",
			'show_depart' => "在部门里显示",
			'show_famewall' => "在名人堂显示",
		);
		return $actions;
	}

//	public function process_bulk_action() {
	//
	//	    if ( 'delete' === $this->current_action() ) {
	//	    	if(isset($_GET['tpl'])){
	//		        foreach($_GET['tpl'] as $tpl){
	//		        	foreach($this->rawData as $key=>$dt){
	//		        		if($dt['ID']==$tpl){
	//		        			unset($this->rawData[$key]);
	//		        		}
	//		        	}
	//
	//		        }
	//	        }
	//	    }
	//	}

	public function column_cb($item) {
		return sprintf(
			'<input type="checkbox" name="delete[]" value="%s" />', $item['ID']
		);
	}

	public function column_face_url($item) {
		if (!empty($item['face_url'])) {
			return sprintf(
			'<img src="%s" style="width:36px;height:36px;"/>',$item['face_url'] 
		);
		} else {
			return "";
		}
	}

	public function column_action($item) {
		return sprintf(
			'<a href="' . menu_page_url(WPCCM_MEMBER_PAGE, false) . '&edit=%s">编辑</a>&nbsp;<a href="' . menu_page_url(WPCCM_MEMBER_PAGE, false) . '&delete=%s">删除</a>', $item['ID'], $item['ID']
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