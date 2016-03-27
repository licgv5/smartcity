<?php
/**
 * @name ChangeDistrictController
 * @author root
 * @desc 修改路灯集控制器
 */
class ChangeDistrictController extends BaseControllerAbstract {

	public function changeDistrictAction() {
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		$postData = new PostParams();
		if (!self::checkParams($postData)) {
			return;
		}
		$operation = $postData->getPost("operation");
		$districtTable = new DistrictTableModel($config["resources"]["database"]["params"]);
		switch($operation) {
			case 0:
				$districtName = $postData->getPost("district_name"); 
				$cityID = $postData->getPost("city_id"); 
				self::addDistrict($districtTable, $districtName, $cityID);
				break;
			case 1:
				$districtID = $postData->getPost("district_id"); 
				self::delDistrict($districtTable, $districtID);
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

	private function addDistrict($districtTable, $districtName, $cityID) {
		$districtID = $districtTable->addDistrict($districtName, $cityID);
		if ($districtID !== false) {
			$result = array('status'=>200, 'data'=>array('msg'=>'success','result'=>true,'district_id'=>intval($districtID)), 'error'=>null);
		} else {
			$result = array('status'=>200, 'data'=>array('msg'=>'unsuccess','result'=>false), 'error'=>null);
		}
		echo json_encode($result);
	}

	private function delDistrict($districtTable, $districtID) {
		if ($districtTable->delDistrict($districtID)) {
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
			$districtName = $postData->getPost("district_name");
			$cityID = $postData->getPost("city_id"); 
			if ($districtName === false || $districtName === "" || $districtName === NULL || !is_int($cityID)) {
				$result = array(
							'status'=>200, 
							'data'=>array(
								'msg'=>'unsuccess',
								'result'=>false), 
							'error'=>'district_name or city_id is error');
				echo json_encode($result);
				return false;
			} 
		}

		if ($operation === 1) {
			$districtID = $postData->getPost("district_id");
			if (!is_int($districtID)) {
				$result = array(
							'status'=>200, 
							'data'=>array(
								'msg'=>'unsuccess',
								'result'=>false), 
							'error'=>'district_id is error');
				echo json_encode($result);
				return false;
			} 
		}
		return true;
	}
}

