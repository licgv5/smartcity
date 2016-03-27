<?php
/**
 * @name GetGeographyInfoController
 * @author root
 * @desc 获取地理信息
 */
class GetGeographyInfoController extends BaseControllerAbstract {

	public function getGeographyInfoAction() {
		//$GLOBALS['HTTP_RAW_POST_DATA'] = "{\"level\":0,\"id\":0,}";
		$postData = new PostParams();
		if (!self::checkParams($postData)) {
			return;
		}
		
		$id = $postData->getPost("id");
		$level = $postData->getPost("level");
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		
		$multipleTable = new MultipleTableModel($config["resources"]["database"]["params"]);
		$lightIds = $multipleTable->getLightIdByForefather($level, $id);
		$lightTable = new LightTableModel($config["resources"]["database"]["params"]);
		$geographyInfo = $lightTable->getGeographyInfo($lightIds);
		$result = array(
			'status'=>200, 
			'data'=>array(
				'location'=>$geographyInfo,
				'msg'=>'success',
				'result'=>true
				), 
			'error'=>null);
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
