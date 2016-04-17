<?php
/**
 *  社团申请表提交接口
 */
namespace Club\Controller;
use Common\Controller\HomebaseController;

/**
 * 首页
 */
class ApplicationController extends HomebaseController {
	//提交申请
	public function sendApplication() {
		$this->application_model = D("Club/Application");
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
				$this->error("请选择意向部门！");
			}
			if (empty($_POST['introduction'])) {
				$this->error("请填写个人介绍！");
			}
			$applicant["student_id"] = htmlspecialchars($_POST['student_id']);
			$applicant["username"] = htmlspecialchars($_POST['username']);
			$applicant["classname"] = htmlspecialchars($_POST['classname']);
			$applicant["department_id"] = intval($_POST['department_id']);
			$applicant["introduction"] = htmlspecialchars($_POST['introduction']);
			$applicant["create_time"] = date('Y-m-d H:i:s', time());
			if ($this->application_model->add($applicant)) {
				$this->ajaxReturn(1);
			} else {
				$this->ajaxReturn(0);
			}
		}
	}

	//获取部门列表
	public function getDepartList() {
		$this->depart_model = D("Club/Department");
		$where = array("flag" => TRUE);
		$departments = $this->depart_model->where($where)->order("orders ASC")->field('id,department_name')->select();
		$this->ajaxReturn($departments);
	}
}