<?php
/**
 *  社团申请表提交接口
 */
namespace Portal\Controller;
use Common\Controller\HomebaseController;

/**
 * 首页
 */
class IndexController extends HomebaseController {

	//首页
	public function index() {
		$this->display(":index");
	}

}