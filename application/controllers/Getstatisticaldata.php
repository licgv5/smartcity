<?php
/**
 * @name GetStatisticalDataController
 * @author root
 * @desc 获取路灯实时视频
 */
class GetStatisticalDataController extends BaseControllerAbstract {

	public function getStatisticalDataAction() {
		//$GLOBALS['HTTP_RAW_POST_DATA'] = "{\"level\":1,\"id\":1,\"calibration\":4,\"endDate\":\"2016-03-26\",\"type\":1}";
		$postData = new PostParams();
		if (!self::checkParams($postData)) {
			return;
		}
		$level = $postData->getPost("level");
		$id = $postData->getPost("id");
		$calibration = $postData->getPost("calibration");
		$endDate = $postData->getPost("endDate");
		$type = $postData->getPost("type");
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		
		$statisticalData = new StatisticalDataModel($config["resources"]["database1"]["params"]);
		switch($calibration) {
			case 1:
				$data = $statisticalData->getDayStatisticalData($level, $id, $type, $endDate);
				break;
			case 2:
				$data = $statisticalData->getWeekStatisticalData($level, $id, $type, $endDate);
				break;
			case 3:
				$data = $statisticalData->getMonthStatisticalData($level, $id, $type, $endDate);
				break;
			case 4:
				$data = $statisticalData->getYearStatisticalData($level, $id, $type, $endDate);
				break;
			default:
				$data = false;
		}

		if ($data === false) {
			$result = array(
						'status'=>200, 
						'data'=>array(
							'msg'=>'unsuccess',
							'result'=>false), 
						'error'=>'has no statistical data');
			echo json_encode($result);
			return;
		}
		
		$result = array(
			'status'=>200, 
			'data'=>array(
				'msg'=>'success',
				'result'=>true,
				'info'=>$data),
			'error'=>null);
		echo json_encode($result);
	}

	private function checkParams($postData) {
		$level = $postData->getPost("level");
		$id = $postData->getPost("id");
		$calibration = $postData->getPost("calibration");
		$endDate = $postData->getPost("endDate");
		$type = $postData->getPost("type");
		if (!is_int($level) || !is_int($id) || !is_int($calibration) || !is_int($type)) {
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
