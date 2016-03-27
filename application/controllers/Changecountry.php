<?php
/**
 * @name ChangeCountryController
 * @author root
 * @desc 修改国家控制器
 */
class ChangeCountryController extends BaseControllerAbstract {

	public function changeCountryAction() {
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		if (!self::checkParams()) {
			return;
		}
		$operation = $this->getRequest()->getPost("operation");
		$countryTable = new CountryTableModel($config["resources"]["database"]["params"]);
		switch($operation) {
			case 0:
				$countryName = $this->getRequest()->getPost("country_name"); 
				self::addCountry($countryTable, $countryName);
				break;
			case 1:
				$countryID = $this->getRequest()->getPost("country_id"); 
				self::delCountry($countryTable, $countryID);
				break;
			default:
				$result = array(
							'status'=>200, 
							'data'=>array(
								'msg'=>'unsuccess',
								'result'=>false), 
							'error'=>'operation between 1 and 2');
				echo json_encode($result);
		}
	}

	private function addCountry($countryTable, $countryName) {
		$countryID = $countryTable->addCountry($countryName);
		if ($countryID !== false) {
			$result = array('status'=>200, 'data'=>array('msg'=>'success','result'=>true,'country_id'=>intval($countryID)), 'error'=>null);
		} else {
			$result = array('status'=>200, 'data'=>array('msg'=>'unsuccess','result'=>false), 'error'=>null);
		}
		echo json_encode($result);
	}

	private function delCountry($countryTable, $countryID) {
		if ($countryTable->delCountry($countryID)) {
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
			$countryName = $this->getRequest()->getPost("country_name");
			if ($countryName === false || $countryName === "" || $countryName === NULL) {
				$result = array(
							'status'=>200, 
							'data'=>array(
								'msg'=>'unsuccess',
								'result'=>false), 
							'error'=>'country_name is error');
				echo json_encode($result);
				return false;
			} 
		}

		if ($operation === 1) {
			$countryID = $this->getRequest()->getPost("country_id");
			if (!is_int($countryID)) {
				$result = array(
							'status'=>200, 
							'data'=>array(
								'msg'=>'unsuccess',
								'result'=>false), 
							'error'=>'country_id is error');
				echo json_encode($result);
				return false;
			} 
		}
		return true;
	}
}

