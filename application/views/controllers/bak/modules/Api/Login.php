<?php
/**
 * @name LoginController
 * @author root
 * @desc 登陆控制器
 */
class LoginController extends Yaf_Controller_Abstract {

	public function loginAction() {
		echo "bbbbbbbbbb\n";
		$user = $this->getRequest()->getPost("user");
		$password = $this->getRequest()->getPost("password");

		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		$userTable = new UserTableModel($config["resources"]["database"]["params"]);
		$queryResult = $userTable->selectUser($user);
		foreach($queryResult as $row) {
			if ($row["account"] === $user && $row["password"] === $password) {
				$result = array('status'=>200, 'data'=>array('msg':'success','result'=>true), 'error'=>'');
				echo json_encode($result);
				return;
			}
		}
		$result = array('status'=>200, 'data'=>array('msg':'unsuccess','result'=>false), 'error'=>'');
		echo json_encode($result);
	}

}
