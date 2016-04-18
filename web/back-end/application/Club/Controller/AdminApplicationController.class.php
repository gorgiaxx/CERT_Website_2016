<?php
/**
 * 响应组社团申请管理
 */
namespace Club\Controller;
use Common\Controller\AdminbaseController;

class AdminApplicationController extends AdminbaseController {

	function _initialize() {
		parent::_initialize();
		$this->application_model = D("Club/Application");
		$this->depart_model = D("Club/Department");
		$this->member_model = D("Club/Member");
	}

	/**
	 *  列表
	 */
	public function index() {
		$this->_lists();
		$this->display();
	}
	/**
	 *  通过面试
	 */
	public function pass() {
		$data['pass'] = TRUE;
		if (isset($_GET['id'])) {
			$id = intval(I("get.id"));
			if ($this->application_model->data($data)->where("id=$id")->save()) {
				$this->success("通过面试成功！");
			} else {
				$this->error("通过面试失败！");
			}
		}
	}
	/**
	 *  取消通过
	 */
	public function unpass() {
		$data['pass'] = FALSE;
		if (isset($_GET['id'])) {
			$id = intval(I("get.id"));
			if ($this->application_model->data($data)->where("id=$id")->save()) {
				$this->success("取消面试成功！");
			} else {
				$this->error("取消面试失败！");
			}
		}
	}
	/**
	 *  通过考核
	 */
	public function check() {
		$data['check'] = TRUE;
		if (isset($_GET['id'])) {
			$id = intval(I("get.id"));
			if ($this->application_model->data($data)->where("id=$id")->save()) {
				$applicant = $this->application_model->where("id=$id")->find();
				$member["student_id"] = $applicant['student_id'];
				$member["username"] = $applicant['username'];
				$member["classname"] = $applicant["classname"];
				$member["department_id"] = $applicant["department_id"];
				$member["introduction"] = $applicant["introduction"];
				$member["join_time"] = date('Y-m-d', time());
				$member["create_time"] = date('Y-m-d H:i:s', time());
				$this->member_model->add($member);
				$this->success("通过考核成功！");
			} else {
				$this->error("通过考核失败！");
			}
		}
	}
	/**
	 *  取消通过
	 */
	public function uncheck() {
		$data['check'] = FALSE;
		if (isset($_GET['id'])) {
			$id = intval(I("get.id"));
			if ($this->application_model->data($data)->where("id=$id")->save()) {
				$applicant = $this->application_model->where("id=$id")->find();
				$this->member_model->where("student_id=" . $applicant['student_id'])->delete();
				$this->success("取消考核成功！");
			} else {
				$this->error("取消考核失败！");
			}
		}
	}

	/**
	 *  删除部门
	 */
	public function delete() {
		if (isset($_POST['ids'])) {
			$ids = implode(",", $_POST['ids']);
			if ($this->application_model->where("id in ($ids)")->delete()) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		} else {
			if (isset($_GET['id'])) {
				$id = intval(I("get.id"));
				if ($this->application_model->delete($id)) {
					$this->success("删除成功！");
				} else {
					$this->error("删除失败！");
				}
			}
		}
	}

	private function _lists() {
		$where_ands = array();
		$fields = array(
			'keyword' => array("field" => "cmf_application.username", "operator" => "like"),
		);
		if (IS_POST) {
			foreach ($fields as $param => $val) {
				if (isset($_POST[$param]) && !empty($_POST[$param])) {
					$operator = $val['operator'];
					$field = $val['field'];
					$get = $_POST[$param];
					$_GET[$param] = $get;
					if ($operator == "like") {
						$get = "%$get%";
					}
					array_push($where_ands, "$field $operator '$get'");
				}
			}
		} else {
			foreach ($fields as $param => $val) {
				if (isset($_GET[$param]) && !empty($_GET[$param])) {
					$operator = $val['operator'];
					$field = $val['field'];
					$get = $_GET[$param];
					if ($operator == "like") {
						$get = "%$get%";
					}
					array_push($where_ands, "$field $operator '$get'");
				}
			}
		}

		unset($_GET[C('VAR_URL_PARAMS')]);

		$where = join(" and ", $where_ands);
		$count = $this->application_model->where($where)->count();
		$page = $this->page($count, 20);
		$this->assign("current_page", $page->GetCurrentPage());
		$applications = $this->application_model
			->join('LEFT JOIN cmf_department ON cmf_application.department_id = cmf_department.id')
			->field('cmf_department.department_name,cmf_application.*')
			->where($where)
			->limit($page->firstRow . ',' . $page->listRows)
			->order("cmf_application.id DESC")
			->select();
		$this->assign("formget", $_GET);
		$this->assign("applications", $applications);
		$this->assign("Page", $page->show('AdminApplication'));
	}
}