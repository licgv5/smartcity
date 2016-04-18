<?php
/**
 * @name GetUngroupedLightController
 * @author root
 * @desc 获取未分组路灯
 */
class GetUngroupedLightController extends BaseControllerAbstract {

	public function getUngroupedLightAction() {
		$postData = new PostParams();
		
		if (!$this->checkParams($postData)) {
			return;
		}
		$district_id = $postData->getPost("id");

		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		$lightTable = new LightTableModel($config["resources"]["database"]["params"]);
		$queryResult = $lightTable->getLightIdForUngrouped($district_id);
		$result = array('status'=>200, 'data'=>array('msg'=>'success','result'=>true,'light'=>$queryResult), 'error'=>null);
		echo json_encode($result);
	}

	private function checkParams($postData) {
		$set_id = $postData->getPost("id");
		if (!is_int($set_id)) {
			$result = array(
						'status'=>200, 
						'data'=>array(
							'msg'=>'unsuccess',
							'result'=>false), 
						'error'=>'id is not int');
			echo json_encode($result);
			return false;
		}
		return true;
	}
}
