<?php
/**
 * @name ChangeProvinceController
 * @author root
 * @desc 修改国家控制器
 */
class ChangeProvinceController extends Yaf_Controller_Abstract {

	public function changeProvinceAction() {
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		if (!self::checkParams()) {
			return;
		}
		$operation = $this->getRequest()->getPost("operation");
		$provinceTable = new ProvinceTableModel($config["resources"]["database"]["params"]);
		switch($operation) {
			case 0:
				$provinceName = $this->getRequest()->getPost("province_name"); 
				$countryID = $this->getRequest()->getPost("country_id"); 
				self::addProvince($provinceTable, $provinceName, $countryID);
				break;
			case 1:
				$provinceID = $this->getRequest()->getPost("province_id"); 
				self::delProvince($provinceTable, $provinceID);
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

	private function addProvince($provinceTable, $provinceName, $countryID) {
		if ($provinceTable->addProvince($provinceName, $countryID)) {
			$result = array('status'=>200, 'data'=>array('msg'=>'success','result'=>true), 'error'=>null);
		} else {
			$result = array('status'=>200, 'data'=>array('msg'=>'unsuccess','result'=>false), 'error'=>null);
		}
		echo json_encode($result);
	}

	private function delProvince($provinceTable, $provinceID) {
		if ($provinceTable->delProvince($provinceID)) {
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
			$provinceName = $this->getRequest()->getPost("province_name");
			$countryID = $this->getRequest()->getPost("country_id"); 
			if ($provinceName === false || $provinceName === "" || $provinceName === NULL || !is_int($countryID)) {
				$result = array(
							'status'=>200, 
							'data'=>array(
								'msg'=>'unsuccess',
								'result'=>false), 
							'error'=>'province_name or country_id is error');
				echo json_encode($result);
				return false;
			} 
		}

		if ($operation === 1) {
			$provinceID = $this->getRequest()->getPost("province_id");
			if ($provinceName === NULL || !is_int($provinceID)) {
				$result = array(
							'status'=>200, 
							'data'=>array(
								'msg'=>'unsuccess',
								'result'=>false), 
							'error'=>'province_id is error');
				echo json_encode($result);
				return false;
			} 
		}
		return true;
	}
}

