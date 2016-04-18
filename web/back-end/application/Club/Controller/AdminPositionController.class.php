<?php
/**
 * 响应组职位管理
 */
namespace Club\Controller;
use Common\Controller\AdminbaseController;

class AdminPositionController extends AdminbaseController {

	function _initialize() {
		parent::_initialize();
		$this->position_model = D("Club/Position");
	}

	/**
	 *  列表
	 */
	public function index() {
		$where = array();
		$count = $this->position_model->where($where)->count();
		$page = $this->page($count, 20);
		$positions = $this->position_model
			->where($where)
			->limit($page->firstRow . ',' . $page->listRows)
			->order("weight DESC")
			->select();
		$this->assign("positions", $positions);
		$this->assign("Page", $page->show('Admin'));
		$this->display();
	}

	/**
	 *  增加成员页面
	 */
	public function add() {
		$this->display();
	}

	function add_post() {
		if (IS_POST) {
			if (empty($_POST['position_name'])) {
				$this->error("请输入职位名！");
			}
			if (empty($_POST['position_name_en'])) {
				$this->error("请输入职位英文名！");
			}
			if (empty($_POST['weight'])) {
				$this->error("请输入权重！");
			}
			$position["position_name"] = htmlspecialchars($_POST['position_name']);
			$position["position_name_en"] = htmlspecialchars($_POST['position_name_en']);
			$position["weight"] = intval($_POST['weight']);
			$position["create_time"] = date('Y-m-d H:i:s', time());

			if (is_null($_POST['id'])) {
				$result = $this->position_model->add($position);
			} else {
				$id = intval($_POST['id']);
				$result = $this->position_model->where('id=' . $id)->save($position);
			}

			if ($result) {
				$this->success("编辑成功！", U("AdminPosition/index"));
			} else {
				$this->error("编辑失败！");
			}
		}
	}

	/**
	 *  编辑职位页面
	 */
	public function edit() {
		$id = intval(I("get.id"));
		$position = $this->position_model->where("id=$id")->find();
		$this->assign("position", $position);
		$this->assign("id", $id);
		$this->display();
	}

	/**
	 *  删除职位
	 */
	public function delete() {
		if (isset($_POST['ids'])) {
			$ids = implode(",", $_POST['ids']);
			if ($this->position_model->where("id in ($ids)")->delete()) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		} else {
			if (isset($_GET['id'])) {
				$id = intval(I("get.id"));
				if ($this->position_model->delete($id)) {
					$this->success("删除成功！");
				} else {
					$this->error("删除失败！");
				}
			}
		}
	}

}
