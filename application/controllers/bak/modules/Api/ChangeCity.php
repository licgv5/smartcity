<?php
/**
 * @name ChangeCityController
 * @author root
 * @desc 修改国家控制器
 */
class ChangeCityController extends Yaf_Controller_Abstract {

	public function changeCityAction() {
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		if (!self::checkParams()) {
			return;
		}
		$operation = $this->getRequest()->getPost("operation");
		$cityTable = new CityTableModel($config["resources"]["database"]["params"]);
		switch($operation) {
			case 0:
				$cityName = $this->getRequest()->getPost("city_name"); 
				$provinceID = $this->getRequest()->getPost("province_id"); 
				self::addCity($cityTable, $cityName, $provinceID);
				break;
			case 1:
				$cityID = $this->getRequest()->getPost("city_id"); 
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
		if ($cityTable->addCity($cityName, $provinceID)) {
			$result = array('status'=>200, 'data'=>array('msg'=>'success','result'=>true), 'error'=>null);
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

	private function checkParams() {
		$operation = $this->getRequest()->getPost("operation");
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
			$cityName = $this->getRequest()->getPost("city_name");
			$provinceID = $this->getRequest()->getPost("province_id"); 
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
			$cityID = $this->getRequest()->getPost("city_id");
			if ($cityName === NULL || !is_int($cityID)) {
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

