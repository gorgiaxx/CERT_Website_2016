<?php
/**
 * MemberController(响应组成员管理)
 */
namespace Club\Controller;
use Common\Controller\AdminbaseController;

class MemberController extends AdminbaseController {

	protected $member_model;

	function _initialize() {
		parent::_initialize();
		$this->member_model = D("Club/Member");
		$this->depart_model = D("Club/Department");
	}

	/**
	 *  列表
	 */
	public function index() {
		$where = array();
		$count = $this->member_model->where($where)->count();
		$page = $this->page($count, 20);
		$members = $this->member_model
			->where($where)
			->limit($page->firstRow . ',' . $page->listRows)
			->order("id DESC")
			->select();
		$this->assign("members", $members);
		$this->assign("Page", $page->show('Admin'));
		$this->display();
	}

	/**
	 *  增加成员页面
	 */
	public function add() {
		$where = array();
		$departments = $this->depart_model->where($where)->order("orders ASC")->field('id,department_name')->select();
		$this->assign("departments", $departments);
		$this->display();
	}

	function add_post() {
		if (IS_POST) {
			if (empty($_POST['student_id'])) {
				$this->error("请输入学号！");
			}
			if (empty($_POST['username'])) {
				$this->error("请输入姓名！");
			}
			if (empty($_POST['classname'])) {
				$this->error("请输入班级名！");
			}
			if (empty($_POST['department_id'])) {
				$this->error("请选择部门！");
			}
			if (empty($_POST['position_id'])) {
				$this->error("请选择职位！");
			}
			if (empty($_POST['join_time'])) {
				$this->error("请输入加入日期！");
			}
			$department["student_id"] = htmlspecialchars($_POST['student_id']);
			$department["username"] = htmlspecialchars($_POST['username']);
			$department["classname"] = htmlspecialchars($_POST['classname']);
			$department["department_id"] = intval($_POST['department_id']);
			$department["position_id"] = intval($_POST['position_id']);
			$department["join_time"] = htmlspecialchars($_POST['join_time']);
			$department["face_url"] = htmlspecialchars($_POST['face_url']);
			$department["introduction"] = intval($_POST['introduction']);
			$department["link"] = htmlspecialchars($_POST['link']);
			$department["show_depart"] = (bool) $_POST['show_depart'];
			$department["show_famehall"] = (bool) $_POST['show_famehall'];
			$department["create_time"] = date('Y-m-d H:i:s', time());
			$result = $this->depart_model->add($department);
			if ($result) {
				$this->success("添加成功！", U("Depart/index"));
			} else {
				$this->error("添加失败！");
			}
		}
	}

	/**
	 *  编辑成员页面
	 */
	public function edit() {
	}

	/**
	 *  删除成员
	 */
	public function delete() {
	}

	/**
	 * 更新成员
	 */
	public function update() {
	}
}