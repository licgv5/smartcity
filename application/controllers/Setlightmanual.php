<?php
/**
 * @name SetLightManualController
 * @author root
 * @desc 手动设置路灯模式
 */
class SetLightManualController extends BaseControllerAbstract {

	public function setLightManualAction() {
		//$GLOBALS['HTTP_RAW_POST_DATA'] = "{\"id\":1,\"level\":4,\"brightness\":70}";
		$postData = new PostParams();
		if (!self::checkParams($postData)) {
			return;
		}
		
		$id = $postData->getPost("id");
		$level = $postData->getPost("level");
		$brightness = $postData->getPost("brightness");
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		
		$multipleTable = new MultipleTableModel($config["resources"]["database"]["params"]);
		if ($multipleTable->setLightPlans($level, $id, $brightness, "manual") === false) {
			// 对应层级设置
			$result = array(
						'status'=>200, 
						'data'=>array(
							'msg'=>'unsuccess',
							'result'=>false
							), 
						'error'=>'set manual error');
			echo json_encode($result);
			return;
		}

		// 获取当前level下所有对该计划生效的灯
		$lightsPlan = $multipleTable->getEffectLight($level, $id);
		if ($lightsPlan === false || count($lightsPlan) ===0){
			$result = array(
				'status'=>200, 
				'data'=>array(
					'msg'=>'success',
					'result'=>true
					), 
				'error'=>null);
			echo json_encode($result);
			return;
		}
		$hardwareDeviceServer = new HardwareDeviceServerModel($config["resources"]["server"]["params"]);
		$rs = $hardwareDeviceServer->sendManualInstruction($lightsPlan, $brightness);
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
				'error'=>'set insert manual control error');
		}
		echo json_encode($result);
	}

	private function checkParams($postData) {
		$id = $postData->getPost("id");
		$level = $postData->getPost("level");
		$brightness = $postData->getPost("brightness");
		if (!is_int($id) || !is_int($level) || !is_int($brightness) ||$brightness > 100 || $brightness < 0) {
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
