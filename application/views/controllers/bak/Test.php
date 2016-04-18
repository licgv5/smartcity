<?php
/**
 * @name LoginController
 * @author root
 * @desc 登陆控制器
 */
class TestController extends Yaf_Controller_Abstract {

	public function indexAction() {
		$user = $this->getRequest()->getPost("user");
		$password = $this->getRequest()->getPost("password");

		$result = array('status'=>200, 'data'=>array('msg'=>'unsuccess','result'=>false), 'error'=>'');
		//echo json_encode($result);
		$this->getView()->assign("content", var_export($result));
	}

}
