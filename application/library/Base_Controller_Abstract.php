<?php
class Base_Controller_Abstract extends Yaf_Controller_Abstract {
	public function init() {
		parent::init();
		$this->checklogin();
	}

	protected function checklogin() {
		/*if(!empty($_SESSION['user_info'])) { 
			return true;
		}

		if (empty($_COOKIE['username']) || empty($_COOKIE['password'])) {
			header("Location:http://114.215.192.232/smartcity/smarty_light/main.html?#/login");  
		}

		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		$userTable = new UserTableModel($config["resources"]["database"]["params"]);
		$queryResult = $userTable->selectUser($user);

		foreach($queryResult as $row) {
			if ($row["account"] === $user && $row["password"] === $password) {
				setcookie("username", $user);
				setcookie("password", $password);
				$_SESSION['user_info'] = $user;
				return true;
			}
		}
		header("Location:http://114.215.192.232/smartcity/smarty_light/main.html?#/login");
		*/
	}
}



