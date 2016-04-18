<?php
/**
 * @name DeleteLightController
 * @author root
 * @desc 删除路灯
 */
class DeleteLightController extends BaseControllerAbstract {

	public function deleteLightAction() {
		//$GLOBALS['HTTP_RAW_POST_DATA'] = "{\"id\":8}";
		$postData = new PostParams();
		if (!self::checkParams($postData)) {
			return;
		}
		
		$id = $postData->getPost("id");
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		
		$lightTable = new LightTableModel($config["resources"]["database"]["params"]);
		$rs = $lightTable->deleteLight($id);
		if ($rs === true) {
			$result = array(
				'status'=>200, 
				'data'=>array(
					'msg'=>'success',
					'result'=>true
					), 
				'error'=>null);
		} else {
			$result = array(
				'status'=>200, 
				'data'=>array(
					'msg'=>'unsuccess',
					'result'=>false
					), 
				'error'=>'delete plan error');
		}
		echo json_encode($result);
	}

	private function checkParams($postData) {
		$id = $postData->getPost("id");
		if (!is_int($id)) {
			$result = array(
						'status'=>200, 
						'data'=>array(
							'msg'=>'unsuccess',
							'result'=>false), 
						'error'=>'params error');
			echo json_encode($result);
			return false;
		}
		return true;
	}
}
