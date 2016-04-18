<?php
/**
 * @name ChangeProvinceController
 * @author root
 * @desc 修改省控制器
 */
class ChangeProvinceController extends BaseControllerAbstract {

	public function changeProvinceAction() {
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		//$GLOBALS['HTTP_RAW_POST_DATA'] = "{\"operation\":0,\"country_id\":0,\"province_name\":\"湖南\"}";
		$postData = new PostParams();
		if (!self::checkParams($postData)) {
			return;
		}
		$operation = $postData->getPost("operation");
		$provinceTable = new ProvinceTableModel($config["resources"]["database"]["params"]);
		switch($operation) {
			case 0:
				$provinceName = $postData->getPost("province_name"); 
				$countryID = $postData->getPost("country_id"); 
				self::addProvince($provinceTable, $provinceName, $countryID);
				break;
			case 1:
				$provinceID = $postData->getPost("province_id"); 
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
		$provinceID = $provinceTable->addProvince($provinceName);
		if ($provinceID !== false) {
			$result = array('status'=>200, 'data'=>array('msg'=>'success','result'=>true,'province_id'=>intval($provinceID)), 'error'=>null);
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
			$provinceName = $postData->getPost("province_name");
			$countryID = $postData->getPost("country_id"); 
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
			$provinceID = $postData->getPost("province_id");
			if (!is_int($provinceID)) {
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

