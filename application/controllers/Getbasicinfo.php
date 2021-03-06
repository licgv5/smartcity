<?php
/**
 * @name GetBasicInfoController
 * @author root
 * @desc 获取基本信息
 */
class GetBasicInfoController extends BaseControllerAbstract {

	public function getBasicInfoAction() {
		//$GLOBALS['HTTP_RAW_POST_DATA'] = "{\"id\":2,\"level\":1}";
		//var_dump($GLOBALS['HTTP_RAW_POST_DATA']);
		$postData = new PostParams();
		if (!self::checkParams($postData)) {
			return;
		}
		
		$id = $postData->getPost("id");
		$level = $postData->getPost("level");
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		$multipleTable = new MultipleTableModel($config["resources"]["database"]["params"]);
		$lightIds = $multipleTable->getLightIdByForefather($level, $id);
		if (count($lightIds) > 0) {
			$lightTable = new LightTableModel($config["resources"]["database"]["params"]);
			$workLight = $lightTable->getWorkLightNum($lightIds);
			$faultLight = $lightTable->getFaultLightNum($lightIds);
			$device = array(
						'light'=>array('total'=>$workLight + $faultLight,'work'=>$workLight,'fault'=>$faultLight),
						'controller'=>array('total'=>$workLight + $faultLight,'work'=>$workLight,'fault'=>$faultLight),
						'sensor'=>array('total'=>$workLight + $faultLight,'work'=>$workLight,'fault'=>$faultLight),
						'camera'=>array('total'=>$workLight + $faultLight,'work'=>$workLight,'fault'=>$faultLight),
						'wifi'=>array('total'=>$workLight + $faultLight,'work'=>$workLight,'fault'=>$faultLight));
			$redis = new RedisOperationModel($config["resources"]["redis"]["params"]);
			foreach ($lightIds as $lightId) {
				$lightId = sprintf("%06d", $lightId);
				$lightInfo = $redis->getAllHash("LIG".$lightId);

				$envInfo = $redis->getAllHash("ENV".$lightId);
				if (count($envInfo) > 0) {
					$temperatureSum += doubleval($envInfo['temperature']);
					$humiditySum += doubleval($envInfo['humidity']);
					$pm25Sum += doubleval($envInfo['pm2.5']);
					$count++;
				}
			}
			$result = array(
						'status'=>200, 
						'data'=>array(
							'msg'=>'success',
							'result'=>true,
							'power'=>100,
							'voltage'=>220,
							'current'=>12,
							'temperature'=>$temperatureSum / $count,
							'humidity'=>$humiditySum / $count,
							'pm2p5'=>$pm25Sum / $count,
							'device'=>$device), 
						'error'=>null);
			echo json_encode($result);
		} else {
			$result = array(
						'status'=>200, 
						'data'=>array(
							'msg'=>'unsuccess',
							'result'=>false), 
						'error'=>'this id no lights');
			echo json_encode($result);
			return false;

		}
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
						'error'=>'id or level is not int');
			echo json_encode($result);
			return false;
		}
		return true;
	}
}
