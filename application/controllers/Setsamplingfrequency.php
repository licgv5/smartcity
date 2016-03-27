<?php
/**
 * @name SetSamplingFrequencyController
 * @author root
 * @desc 设置传感器频率
 */
class SetSamplingFrequencyController extends BaseControllerAbstract {

	public function setSamplingFrequencyAction() {
		//$GLOBALS['HTTP_RAW_POST_DATA'] = "{\"id\":1,\"level\":4,\"frequency\":70}";
		$postData = new PostParams();
		if (!self::checkParams($postData)) {
			return;
		}
		
		$id = $postData->getPost("id");
		$level = $postData->getPost("level");
		$frequency = $postData->getPost("frequency");
		$type = $postData->getPost("type");
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		
		$multipleTable = new MultipleTableModel($config["resources"]["database"]["params"]);
		if ($multipleTable->setSamplingFrequency($level, $id, $frequency, $type) === false) {
			$result = array(
						'status'=>200, 
						'data'=>array(
							'msg'=>'unsuccess',
							'result'=>false
							), 
						'error'=>'set frequency error');
			echo json_encode($result);
			return;
		}

		
		// 获取当前level下所有对该计划生效的灯
		$lights = $multipleTable->getEffectFrequencyLight($level, $id, $type);
		if ($lights === false || count($lights) ===0){
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
		$rs = $hardwareDeviceServer->sendFrequencyInstruction($lightsPlan, $frequency, $type);
		
		//$rs =true;
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
		$frequency = $postData->getPost("frequency");
		$type = $postData->getPost("type");
		if (!is_int($id) || !is_int($level) || !is_int($frequency) || !is_int($type)) {
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
