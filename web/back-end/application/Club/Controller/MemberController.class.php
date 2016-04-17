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
			$member["student_id"] = htmlspecialchars($_POST['student_id']);
			$member["username"] = htmlspecialchars($_POST['username']);
			$member["classname"] = htmlspecialchars($_POST['classname']);
			$member["department_id"] = intval($_POST['department_id']);
			$member["position_id"] = intval($_POST['position_id']);
			$member["join_time"] = htmlspecialchars($_POST['join_time']);
			$member["face_url"] = htmlspecialchars($_POST['face_url']);
			$member["introduction"] = htmlspecialchars($_POST['introduction']);
			$member["link"] = htmlspecialchars($_POST['link']);
			$member["show_depart"] = (bool) $_POST['show_depart'];
			$member["show_famehall"] = (bool) $_POST['show_famehall'];
			$member["create_time"] = date('Y-m-d H:i:s', time());
			$result = $this->member_model->add($member);
			if ($result) {
				$this->success("添加成功！", U("Member/index"));
			} else {
				$this->error("添加失败！");
			}
		}
	}

	/**
	 *  编辑成员页面
	 */
	public function edit() {
		$id = intval(I("get.id"));
		$member = $this->member_model->where("id=$id")->find();
		$this->assign("member", $member);
		$this->assign("id", $id);

		$where = array();
		$departments = $this->depart_model->where($where)->order("orders ASC")->field('id,department_name')->select();
		$this->assign("departments", $departments);
		$this->display();
	}

	/**
	 *  删除成员
	 */
	public function delete() {
		if (isset($_POST['ids'])) {
			$ids = implode(",", $_POST['ids']);
			if ($this->member_model->where("id in ($ids)")->delete()) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		} else {
			if (isset($_GET['id'])) {
				$id = intval(I("get.id"));
				if ($this->member_model->delete($id)) {
					$this->success("删除成功！");
				} else {
					$this->error("删除失败！");
				}
			}
		}
	}

	/**
	 * 更新成员
	 */
	public function update() {
		if (IS_POST) {
			if (is_null($_POST['id'])) {
				$this->error("err0r!");
			}
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
			$id = intval($_POST['id']);
			$member["student_id"] = htmlspecialchars($_POST['student_id']);
			$member["username"] = htmlspecialchars($_POST['username']);
			$member["classname"] = htmlspecialchars($_POST['classname']);
			$member["department_id"] = intval($_POST['department_id']);
			$member["position_id"] = intval($_POST['position_id']);
			$member["join_time"] = htmlspecialchars($_POST['join_time']);
			$member["face_url"] = htmlspecialchars($_POST['face_url']);
			$member["introduction"] = htmlspecialchars($_POST['introduction']);
			$member["link"] = htmlspecialchars($_POST['link']);
			$member["show_depart"] = (bool) $_POST['show_depart'];
			$member["show_famehall"] = (bool) $_POST['show_famehall'];
			$member["create_time"] = date('Y-m-d H:i:s', time());
			$result = $this->member_model->where('id=' . $id)->save($member);
			if ($result) {
				$this->success("更新成功！", U("Member/index"));
			} else {
				$this->error("更新失败！");
			}
		}
	}
}