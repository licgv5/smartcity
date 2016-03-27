<?php
/**
 * @name LightSettingController
 * @author root
 * @desc 设置路灯亮度
 */
class SetLightManualController extends BaseControllerAbstract {

	public function setLightManualAction() {
		$postData = new PostParams();
		if (!self::checkParams($postData)) {
			return;
		}
		
		$id = $postData->getPost("id");
		$level = $postData->getPost("level");
		$luminance = $postData->getPost("luminance");
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		
		$multipleTable = new MultipleTableModel($config["resources"]["database"]["params"]);
		$lightIds = $multipleTable->getLightIdByForefather($level, $id);
		$lightTable = new LightTableModel($config["resources"]["database"]["params"]);
		$rs = $lightTable->setLuminance($lightIds, $luminance);
		if ($rs === true) {
			$result = array(
				'status'=>200, 
				'data'=>array(
					'msg'=>'success',
					'result'=>true
					), 
				'error'=>null);
		} else {
			$result = array(
				'status'=>200, 
				'data'=>array(
					'msg'=>'unsuccess',
					'result'=>false
					), 
				'error'=>'set luminance error');
		}
		echo json_encode($result);
	}

	private function checkParams($postData) {
		$id = $postData->getPost("id");
		$level = $postData->getPost("level");
		$luminance = $postData->getPost("luminance");
		if (!is_int($id) || !is_int($level) || !is_float($luminance)) {
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
