<?php
/**
 * @name GetDistrictOrGroupLightController
 * @author root
 * @desc 获取已设置的计划模式
 */

class GetDistrictOrGroupLightController extends BaseControllerAbstract {

	public function getDistrictOrGroupLightAction() {
		//$GLOBALS['HTTP_RAW_POST_DATA'] = "{\"id\":1,\"level\":4}";
		$postData = new PostParams();
		if (!self::checkParams($postData)) {
			return;
		}

		$id = $postData->getPost("id");
		$level = $postData->getPost("level");
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();

		$lightTable = new LightTableModel($config["resources"]["database"]["params"]);
		$rs = $lightTable->getLightInfoByParent($level, $id);
		if ($rs === false) {
			$rs = array();
			$result = array(
						'status'=>200, 
						'data'=>array(
							'msg'=>'unsuccess',
							'result'=>false,
							), 
						'error'=>'get light error');

		} else {
			$result = array(
						'status'=>200, 
						'data'=>array(
							'msg'=>'success',
							'result'=>true,
							'set'=>$rs,
							), 
						'error'=>null);
		}
		echo json_encode($result);
	}

	private function checkParams($postData) {
		$id = $postData->getPost("id");
		$level = $postData->getPost("level");
		if (!is_int($id) || !is_int($level)) {
			$result = array(
						'status'=>200, 
						'data'=>array(
							'msg'=>'unsuccess',
							'result'=>false), 
						'error'=>'params error');
			echo json_encode($result);
			return false;
		}
		return true;
	}
}
