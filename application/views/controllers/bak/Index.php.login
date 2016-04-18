<?php
/**
 * @name LoginController
 * @author root
 * @desc 登陆控制器
 */
class IndexController extends Yaf_Controller_Abstract {

    public function indexAction() {
        $user = $this->getRequest()->getPost("user");
        $password = $this->getRequest()->getPost("password");
        $user = "test";
        $password = "testaa";
        
		$config = Yaf_Registry::get("config");
        $config = $config->toArray();
        $userTable = new UserTableModel($config["resources"]["database"]["params"]);
        $queryResult = $userTable->selectUser($user);
        foreach($queryResult as $row) {
            var_dump($row);
            var_dump($row["account"] === $user);
            var_dump($row["password"] === $password);
            if ($row["account"] === $user && $row["password"] === $password) {
                $result = array('stat'=>200, 'msg'=>array('result'=>'true'));
                echo json_encode($result);
		        SeasLog::notice('this is a notice log');
                return;
            }
		}
		$result = array('stat'=>200, 'msg'=>array('result'=>'false'));
		echo json_encode($result);
    }

}
