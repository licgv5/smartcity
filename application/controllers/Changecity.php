<?php
/**
 * @name ChangeCityController
 * @author root
 * @desc 修改城市控制器
 */
class ChangeCityController extends BaseControllerAbstract {

	public function changeCityAction() {
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		//$GLOBALS['HTTP_RAW_POST_DATA'] = "{\"operation\":0,\"province_id\":0,\"city_name\":\"长沙\"}";
		$postData = new PostParams();
		if (!self::checkParams($postData)) {
			return;
		}
		$operation = $postData->getPost("operation");
		$cityTable = new CityTableModel($config["resources"]["database"]["params"]);
		switch($operation) {
			case 0:
				$cityName = $postData->getPost("city_name"); 
				$provinceID = $postData->getPost("province_id"); 
				self::addCity($cityTable, $cityName, $provinceID);
				break;
			case 1:
				$cityID = $postData->getPost("city_id"); 
				self::delCity($cityTable, $cityID);
				break;
			default:
				$result = array(
							'status'=>200, 
							'data'=>array(
								'msg'=>'unsuccess',
								'result'=>false), 
							'error'=>'operation between 1 and 0');
				echo json_encode($result);
		}
	}

	private function addCity($cityTable, $cityName, $provinceID) {
		$cityID = $cityTable->addCity($cityName, $provinceID);
		if ($cityID !== false) {
			$result = array('status'=>200, 'data'=>array('msg'=>'success','result'=>true,'city_id'=>intval($cityID)), 'error'=>null);
		} else {
			$result = array('status'=>200, 'data'=>array('msg'=>'unsuccess','result'=>false), 'error'=>null);
		}
		echo json_encode($result);
	}

	private function delCity($cityTable, $cityID) {
		if ($cityTable->delCity($cityID)) {
			$result = array('status'=>200, 'data'=>array('msg'=>'success','result'=>true), 'error'=>null);
		} else {
			$result = array('status'=>200, 'data'=>array('msg'=>'unsuccess','result'=>false), 'error'=>null);
		}
		echo json_encode($result);
	}

	private function checkParams($postData) {
		$operation = $postData->getPost("operation");
		if ($operation !== 0 && $operation !== 1) {
			$result = array(
						'status'=>200, 
						'data'=>array(
							'msg'=>'unsuccess',
							'result'=>false), 
						'error'=>'operation between 0 and 1');
			echo json_encode($result);
			return false;
		}

		if ($operation === 0) {
			$cityName = $postData->getPost("city_name");
			$provinceID = $postData->getPost("province_id"); 
			if ($cityName === false || $cityName === "" || $cityName === NULL || !is_int($provinceID)) {
				$result = array(
							'status'=>200, 
							'data'=>array(
								'msg'=>'unsuccess',
								'result'=>false), 
							'error'=>'city_name or province_id is error');
				echo json_encode($result);
				return false;
			} 
		}

		if ($operation === 1) {
			$cityID = $postData->getPost("city_id");
			if (!is_int($cityID)) {
				$result = array(
							'status'=>200, 
							'data'=>array(
								'msg'=>'unsuccess',
								'result'=>false), 
							'error'=>'city_id is error');
				echo json_encode($result);
				return false;
			} 
		}
		return true;
	}
}

