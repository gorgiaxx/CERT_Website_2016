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
	}

	/**
	 *  列表
	 */
	public function index() {
		$where = array();
		$count = $this->member_model->where($where)->count();
		$page = $this->page($count, 20);
		$colleges = $this->member_model
			->where($where)
			->limit($page->firstRow . ',' . $page->listRows)
			->order("id DESC")
			->select();
		$this->assign("colleges", $colleges);
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