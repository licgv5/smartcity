<?php
/**
 * @name GetLightInfoController
 * @author root
 * @desc 获取路灯信息
 */
class GetLightInfoController extends BaseControllerAbstract {

	public function getLightInfoAction() {
		//$GLOBALS['HTTP_RAW_POST_DATA'] = "{\"lightId\":1}";
		$postData = new PostParams();
		if (!self::checkParams($postData)) {
			return;
		}
		
		$id = $postData->getPost("lightId");
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		
		$lightTable = new LightTableModel($config["resources"]["database"]["params"]);
		$lightInfo = $lightTable->getLightInfo($id);
		if (count($lightInfo)===0) {
			$result = array(
						'status'=>200, 
						'data'=>array(
							'msg'=>'unsuccess',
							'result'=>false), 
						'error'=>'id is not exsit');
			echo json_encode($result);
			return;
		}
	
		$redis = new RedisOperationModel($config["resources"]["redis"]["params"]);
		$lightId = sprintf("%06d", $id);

		$redisLightInfo = $redis->getAllHash("LIG".$lightId);
		$redisEnvInfo = $redis->getAllHash("ENV".$lightId);

		$result = array(
			'status'=>200, 
			'data'=>array(
				'msg'=>'success',
				'result'=>true,
				'lightNum'=>$lightInfo['number'],
				'lng'=>$lightInfo['longitude'],
				'lat'=>$lightInfo['latitude'],
				'current'=>$redisLightInfo['ledCurrent'],
				'voltage'=>doubleval($redisLightInfo['ledVoltage']),
				'power'=>doubleval($redisLightInfo['pwm1Power']),
				'temperature'=>doubleval($redisEnvInfo['temperature']),
				'humidity'=>doubleval($redisEnvInfo['humidity']),
				'pm2p5'=>doubleval($redisEnvInfo['pm2.5']),
				'light'=>$lightInfo['status'],
				'voice'=>$lightInfo['voice_status'],
				'environment'=>$lightInfo['environment_status'],
				'vehicle'=>$lightInfo['vehicle_status'],
				'crowd'=>$lightInfo['crowd_status']), 
			'error'=>null);
		echo json_encode($result);
	}

	private function checkParams($postData) {
		$id = $postData->getPost("lightId");
		if (!is_int($id)) {
			$result = array(
						'status'=>200, 
						'data'=>array(
							'msg'=>'unsuccess',
							'result'=>false), 
						'error'=>'id is not int');
			echo json_encode($result);
			return false;
		}
		return true;
	}
}
