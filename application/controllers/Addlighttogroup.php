<?php
/**
 * @name AddLightToGroupController
 * @author root
 * @desc 对路灯设置分组
 */
class AddLightToGroupController extends BaseControllerAbstract {

	public function addLightToGroupAction() {
		$postData = new PostParams();
		
		if (!$this->checkParams($postData)) {
			return;
		}
		$groupId = $postData->getPost("id");
		$lightIds = $postData->getPost("lightIds");

		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		$lightTable = new LightTableModel($config["resources"]["database"]["params"]);
		$rs = $lightTable->addLightToGroup($lightIds, $groupId);
		if ($rs === true) {
			$result = array('status'=>200, 'data'=>array('msg'=>'success','result'=>true), 'error'=>null);
		} else {
			$result = array('status'=>200, 'data'=>array('msg'=>'unsuccess','result'=>false), 'error'=>'and light to group failed');
		}
		echo json_encode($result);
	}

	private function checkParams($postData) {
		$groupId = $postData->getPost("id");
		$lightIds = $postData->getPost("lightIds");
		if (!is_int($groupId) || !is_array($lightIds)) {
			$result = array(
						'status'=>200, 
						'data'=>array(
							'msg'=>'unsuccess',
							'result'=>false), 
						'error'=>'params is error');
			echo json_encode($result);
			return false;
		}
		return true;
	}
}
