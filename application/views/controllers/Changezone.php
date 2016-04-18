<?php
/**
 * @name ChangeZoneController
 * @author root
 * @desc 修改区控制器
 */
class ChangeZoneController extends BaseControllerAbstract {

	public function changeZoneAction() {
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		if (!self::checkParams()) {
			return;
		}
		$operation = $this->getRequest()->getPost("operation");
		$zoneTable = new ZoneTableModel($config["resources"]["database"]["params"]);
		switch($operation) {
			case 0:
				$zoneName = $this->getRequest()->getPost("zone_name"); 
				$cityID = $this->getRequest()->getPost("city_id"); 
				self::addZone($zoneTable, $zoneName, $cityID);
				break;
			case 1:
				$zoneID = $this->getRequest()->getPost("zone_id"); 
				self::delZone($zoneTable, $zoneID);
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

	private function addZone($zoneTable, $zoneName, $cityID) {
		$zoneID = $zoneTable->addCountry($zoneName);
		if ($zoneID !== false) {
			$result = array('status'=>200, 'data'=>array('msg'=>'success','result'=>true,'zone_id'=>intval($zoneID)), 'error'=>null);
		} else {
			$result = array('status'=>200, 'data'=>array('msg'=>'unsuccess','result'=>false), 'error'=>null);
		}
		echo json_encode($result);
	}

	private function delZone($zoneTable, $zoneID) {
		if ($zoneTable->delZone($zoneID)) {
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
			$zoneName = $this->getRequest()->getPost("zone_name");
			$cityID = $this->getRequest()->getPost("city_id"); 
			if ($zoneName === false || $zoneName === "" || $zoneName === NULL || !is_int($cityID)) {
				$result = array(
							'status'=>200, 
							'data'=>array(
								'msg'=>'unsuccess',
								'result'=>false), 
							'error'=>'zone_name or city_id is error');
				echo json_encode($result);
				return false;
			} 
		}

		if ($operation === 1) {
			$zoneID = $this->getRequest()->getPost("zone_id");
			if ($zoneName === NULL || !is_int($zoneID)) {
				$result = array(
							'status'=>200, 
							'data'=>array(
								'msg'=>'unsuccess',
								'result'=>false), 
							'error'=>'zone_id is error');
				echo json_encode($result);
				return false;
			} 
		}
		return true;
	}
}

