<?php
/**
 * @name EditLightController
 * @author root
 * @desc 添加路灯
 */
class EditLightController extends BaseControllerAbstract {

	public function editLightAction() {
		$postData = new PostParams();
		
		if (!$this->checkParams($postData)) {
			return;
		}
		$id = $postData->getPost("id");
		$lightInfo = array();
        $lightInfo['number'] = $postData->getPost("lightName");
		$lightInfo['longitude'] = $postData->getPost("lightLng");
		$lightInfo['latitude'] = $postData->getPost("lightLat");
			
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		
		$lightTable = new LightTableModel($config["resources"]["database"]["params"]);
		$rs = $lightTable->editLight($id, $lightInfo);
		if ($rs === true) {
			$result = array('status'=>200, 'data'=>array('msg'=>'success','result'=>true), 'error'=>null);
		} else {
			$result = array('status'=>200, 'data'=>array('msg'=>'unsuccess','result'=>false), 'error'=>'edit light failed');
		}
		echo json_encode($result);
	}

	private function checkParams($postData) {
		$id = $postData->getPost("id");
        $lightName = $postData->getPost("lightName");
		$lightLng = $postData->getPost("lightLng");
		$lightLat = $postData->getPost("lightLat");
		if (!is_int($id) 
					|| !is_int($lightName) 
					|| !is_numeric($lightLng) 
					|| !is_numeric($lightLat)) {
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
