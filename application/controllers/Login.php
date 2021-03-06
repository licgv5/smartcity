<?php
/**
 * @name LoginController
 * @author root
 * @desc 登陆控制器
 */
class LoginController extends Yaf_Controller_Abstract {

	public function loginAction() {
		$postData = new PostParams();
		$user = trim($postData->getPost("username"));
		$password = trim($postData->getPost("password"));
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		$userTable = new UserTableModel($config["resources"]["database"]["params"]);
		$queryResult = $userTable->selectUser($user);

		foreach($queryResult as $row) {
			if ($row["account"] === $user && $row["password"] === $password) {
				setcookie("username", $user);
				setcookie("password", $password);
				$_SESSION['user_info'] = $user;
				$result = array('status'=>200, 'data'=>array('msg'=>'success','result'=>true), 'error'=>null);
				echo json_encode($result);
				return;
			}
		}
		$result = array('status'=>200, 'data'=>array('msg'=>'unsuccess','result'=>false), 'error'=>'用户名或者密码错误');
		echo json_encode($result);
	}

}
