<?php
/**
 * @name ChangeDistrictController
 * @author root
 * @desc 修改路灯集控制器
 */
class ChangeDistrictController extends Yaf_Controller_Abstract {

	public function changeDistrictAction() {
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		if (!self::checkParams()) {
			return;
		}
		$operation = $this->getRequest()->getPost("operation");
		$districtTable = new DistrictTableModel($config["resources"]["database"]["params"]);
		switch($operation) {
			case 0:
				$districtName = $this->getRequest()->getPost("district_name"); 
				$zoneID = $this->getRequest()->getPost("zone_id"); 
				self::addDistrict($districtTable, $districtName, $zoneID);
				break;
			case 1:
				$districtID = $this->getRequest()->getPost("district_id"); 
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

	private function addDistrict($districtTable, $districtName, $zoneID) {
		if ($districtTable->addDistrict($districtName, $zoneID)) {
			$result = array('status'=>200, 'data'=>array('msg'=>'success','result'=>true), 'error'=>null);
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
			$districtName = $this->getRequest()->getPost("district_name");
			$zoneID = $this->getRequest()->getPost("zone_id"); 
			if ($districtName === false || $districtName === "" || $districtName === NULL || !is_int($zoneID)) {
				$result = array(
							'status'=>200, 
							'data'=>array(
								'msg'=>'unsuccess',
								'result'=>false), 
							'error'=>'district_name or zone_id is error');
				echo json_encode($result);
				return false;
			} 
		}

		if ($operation === 1) {
			$districtID = $this->getRequest()->getPost("district_id");
			if ($districtName === NULL || !is_int($districtID)) {
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

