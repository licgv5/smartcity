<?php
/**
 * @name GetChildNodeController
 * @author root
 * @desc 获取子节点
 */
class GetChildNodeController extends BaseControllerAbstract {

	public function getChildNodeAction() {
		$postData = new PostParams();
		if (!self::checkParams($postData)) {
			return;
		}
		
		$id = $postData->getPost("id");
		$level = $postData->getPost("level");
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();

		switch($level) {
			/*case 0:
				//查找所有国家
				$countryTable = new CountryTableModel($config["resources"]["database"]["params"]);
				$rs = $countryTable->getAllCountry();
				break;*/
			case 1:
				//查找国家的子节点
				$provinceTable = new ProvinceTableModel($config["resources"]["database"]["params"]);
				$rs = $provinceTable->getAllProvince();
				break;
			case 2:
				//查找省下的城市
				$cityTable = new CityTableModel($config["resources"]["database"]["params"]);
				$rs = $cityTable->getCityByProvince($id);
				break;
			/*case 3:
				//查找城市下的zone
				$zoneTable = new ZoneTableModel($config["resources"]["database"]["params"]);
				$rs = $zoneTable->getZoneByCity($id);
				break;*/
			case 3:
				//查找区下的路灯集
				$districtTable = new DistrictTableModel($config["resources"]["database"]["params"]);
				$rs = $districtTable->getDistrictByCity($id);
				break;
			case 4:
				//查找路灯集下的分组
				$groupTable = new GroupTableModel($config["resources"]["database"]["params"]);
				$rs = $groupTable->getGroupByDistrict($id);
				break;
			default:
				$result = array(
							'status'=>200, 
							'data'=>array(
								'msg'=>'unsuccess',
								'result'=>false), 
							'error'=>'level must between 1 and 4');
				echo json_encode($result);
				return;
		}
	    if($rs === false) {
			$rs = array();
		}
		$result = array('status'=>200, 'data'=>array('msg'=>'success','result'=>true,'nodes'=>$rs), 'error'=>null);
		echo json_encode($result);
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
