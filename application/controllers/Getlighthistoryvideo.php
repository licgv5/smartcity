<?php
/**
 * @name GetLightHistoryVideoController
 * @author root
 * @desc 获取路灯历史视频
 */
class GetLightHistoryVideoController extends BaseControllerAbstract {

	public function getLightHistoryVideoAction() {
		//$GLOBALS['HTTP_RAW_POST_DATA'] = "{\"lightId\":1,\"pageNum\":1,\"pageSize\":10}";
		$postData = new PostParams();
		if (!self::checkParams($postData)) {
			return;
		}
		
		$id = $postData->getPost("lightId");
		$pageNum = $postData->getPost("pageNum");
		$pageSize = $postData->getPost("pageSize");
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		
		$lightVideoTable = new LightVideoTableModel($config["resources"]["database"]["params"]);
		$lightVideoInfo = $lightVideoTable->getHistoryVideo($id, $pageNum, $pageSize);
		if ($lightVideoInfo === false) {
			$result = array(
						'status'=>200, 
						'data'=>array(
							'msg'=>'unsuccess',
							'result'=>false), 
						'error'=>'has no video');
			echo json_encode($result);
			return;
		}
		
		$result = array(
			'status'=>200, 
			'data'=>array(
				'msg'=>'success',
				'result'=>true,
				'videoInfo'=>$lightVideoInfo['videoInfo'],
				'lightNum'=>$lightVideoInfo['lightNum']), 
			'error'=>null);
		echo json_encode($result);
	}

	private function checkParams($postData) {
		$id = $postData->getPost("lightId");
		$pageNum = $postData->getPost("pageNum");
		$pageSize = $postData->getPost("pageSize");
		if (!is_int($id) || !is_int($pageNum) || !is_int($pageSize)) {
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
