<?php
/**
 * @name ChangeGroupController
 * @author root
 * @desc 修改路灯分组控制器
 */
class ChangeGroupController extends BaseControllerAbstract {

	public function changeGroupAction() {
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		$postData = new PostParams();
		if (!self::checkParams($postData)) {
			return;
		}
		$operation = $postData->getPost("operation");
		$groupTable = new GroupTableModel($config["resources"]["database"]["params"]);
		switch($operation) {
			case 0:
				$groupName = $postData->getPost("group_name"); 
				$districtID = $postData->getPost("district_id"); 
				self::addGroup($groupTable, $groupName, $districtID);
				break;
			case 1:
				$groupID = $postData->getPost("group_id"); 
				self::delGroup($groupTable, $groupID);
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

	private function addGroup($groupTable, $groupName, $districtID) {
		$groupID = $groupTable->addGroup($groupName, $districtID);
		if ($groupID !== false) {
			$result = array('status'=>200, 'data'=>array('msg'=>'success','result'=>true,'group_id'=>intval($groupID)), 'error'=>null);
		} else {
			$result = array('status'=>200, 'data'=>array('msg'=>'unsuccess','result'=>false), 'error'=>null);
		}
		echo json_encode($result);
	}

	private function delGroup($groupTable, $groupID) {
		if ($groupTable->delGroup($groupID)) {
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
			$groupName = $postData->getPost("group_name");
			$districtID = $postData->getPost("district_id"); 
			if ($groupName === false || $groupName === "" || $groupName === NULL || !is_int($districtID)) {
				$result = array(
							'status'=>200, 
							'data'=>array(
								'msg'=>'unsuccess',
								'result'=>false), 
							'error'=>'group_name or district_id is error');
				echo json_encode($result);
				return false;
			} 
		}

		if ($operation === 1) {
			$groupID = $postData->getPost("group_id");
			if (!is_int($groupID)) {
				$result = array(
							'status'=>200, 
							'data'=>array(
								'msg'=>'unsuccess',
								'result'=>false), 
							'error'=>'group_id is error');
				echo json_encode($result);
				return false;
			} 
		}
		return true;
	}
}

