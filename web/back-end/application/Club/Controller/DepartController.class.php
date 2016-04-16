<?php
/**
 * DepartController(响应组部门管理)
 */
namespace Club\Controller;
use Common\Controller\AdminbaseController;

class DepartController extends AdminbaseController {

	protected $depart_model;

	function _initialize() {
		parent::_initialize();
		$this->
			depart_model = D("Club/Department");
	}

	/**
	 *  列表
	 */
	public function index() {
		$where = array();
		$count = $this->depart_model->where($where)->count();
		$page = $this->page($count, 20);
		$departments = $this->depart_model
			->where($where)
			->limit($page->firstRow . ',' . $page->listRows)
			->order("id DESC")
			->select();
		$this->assign("departments", $departments);
		$this->assign("Page", $page->show('Admin'));
		$this->display();
	}

	/**
	 *  增加部门页面
	 */
	public function add() {
		$this->display();
	}

	/**
	 *  增加部门
	 */
	function add_post() {
		if (IS_POST) {
			if (empty($_POST['department_name'])) {
				$this->error("请输入部门名！");
			}
			if (empty($_POST['department_name_en'])) {
				$this->error("请输入部门英文标识！");
			}
			if (empty($_POST['brief'])) {
				$this->error("请输入部门简介！");
			}
			if (empty($_POST['introduction'])) {
				$this->error("请输入部门详介！");
			}
			if (empty($_POST['order'])) {
				$this->error("请输入显示顺序！");
			}
			if (empty($_POST['background'])) {
				$this->error("请添加主题背景！");
			}
			$department["department_name"] = htmlspecialchars($_POST['department_name']);
			$department["department_name_en"] = htmlspecialchars($_POST['department_name_en']);
			$department["brief"] = htmlspecialchars($_POST['brief']);
			$department["introduction"] = htmlspecialchars($_POST['introduction']);
			$department["order"] = intval($_POST['order']);
			$department["background"] = htmlspecialchars($_POST['background']);
			$department["flag"] = (bool) $_POST['flag'];
			$result = $this->depart_model->add($department);
			if ($result) {
				$this->success("添加成功！", U("Depart/index"));
			} else {
				$this->error("添加失败！");
			}
		}
	}

	/**
	 *  编辑部门页面
	 */
	public function edit() {
		$id = intval(I("get.id"));
		$department = $this->depart_model->where("id=$id")->find();
		$this->assign("department", $department);
		$this->assign("id", $id);
		$this->display();
	}

	/**
	 *  删除部门
	 */
	public function delete() {
		if (isset($_POST['ids'])) {
			$ids = implode(",", $_POST['ids']);
			if ($this->depart_model->where("id in ($ids)")->delete()) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		} else {
			if (isset($_GET['id'])) {
				$id = intval(I("get.id"));
				if ($this->depart_model->delete($id)) {
					$this->success("删除成功！");
				} else {
					$this->error("删除失败！");
				}
			}
		}
	}

	/**
	 * 更新部门
	 */
	public function update() {
		if (IS_POST) {
			if (is_null($_POST['id'])) {
				$this->error("err0r!");
			}
			if (empty($_POST['department_name'])) {
				$this->error("请输入部门名！");
			}
			if (empty($_POST['department_name_en'])) {
				$this->error("请输入部门英文标识！");
			}
			if (empty($_POST['brief'])) {
				$this->error("请输入部门简介！");
			}
			if (empty($_POST['introduction'])) {
				$this->error("请输入部门详介！");
			}
			if (empty($_POST['order'])) {
				$this->error("请输入显示顺序！");
			}
			if (empty($_POST['background'])) {
				$this->error("请添加主题背景！");
			}
			$id = intval($_POST['id']);
			$department["department_name"] = htmlspecialchars($_POST['department_name']);
			$department["department_name_en"] = htmlspecialchars($_POST['department_name_en']);
			$department["brief"] = htmlspecialchars($_POST['brief']);
			$department["introduction"] = htmlspecialchars($_POST['introduction']);
			$department["order"] = intval($_POST['order']);
			$department["background"] = htmlspecialchars($_POST['background']);
			$department["flag"] = (bool) $_POST['flag'];
			$result = $this->depart_model->where('id=' . $id)->save($department);
			if ($result) {
				$this->success("添加成功！", U("Depart/index"));
			} else {
				$this->error("添加失败！");
			}
		}
	}
}