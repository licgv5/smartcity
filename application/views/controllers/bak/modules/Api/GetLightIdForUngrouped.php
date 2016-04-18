<?php
/**
 * @name GetLightIdForUngroupedController
 * @author root
 * @desc 获取未分组路灯
 */
class LoginController extends Yaf_Controller_Abstract {

	public function getLightIdForUngroupedAction() {
		if (!self::checkParams()) {
			return;
		}
		$set_id = $this->getRequest()->getPost("set_id");

		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		$lightTable = new LightTableModel($config["resources"]["database"]["params"]);
		$queryResult = $lightTable->getLightIdForUngrouped($set_id);
		$ids = array();
		foreach($queryResult as $row) {
			if (array_key_exists("id", $row)) {
				$ids[] = intval($row["id"]);
			}
		}
		$result = array('status'=>200, 'data'=>array('msg'=>'unsuccess','result'=>false,'light_id'=>$ids), 'error'=>null);
		echo json_encode($result);
	}

	private function checkParams() {
		$set_id = $this->getRequest()->getPost("set_id");
		if (!is_int($set_id)) {
			$result = array(
						'status'=>200, 
						'data'=>array(
							'msg'=>'unsuccess',
							'result'=>false), 
						'error'=>'set_id is not int');
			echo json_encode($result);
			return false;
		}
	}
}
