<?php
/**
 * @name GetLightRealTimeVideoController
 * @author root
 * @desc 获取路灯实时视频
 */
class GetLightRealTimeVideoController extends BaseControllerAbstract {

	public function getLightRealTimeVideoAction() {
		//$GLOBALS['HTTP_RAW_POST_DATA'] = "{\"lightId\":1}";
		$postData = new PostParams();
		if (!self::checkParams($postData)) {
			return;
		}
		
		$id = $postData->getPost("lightId");
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();
		
		$lightVideoTable = new LightVideoTableModel($config["resources"]["database"]["params"]);
		$lightVideoInfo = $lightVideoTable->getRealTimeVideo($id);
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
				'videoSrc'=>$lightVideoInfo['video_url'],
				'lightNum'=>$lightVideoInfo['number']), 
			'error'=>null);
		echo json_encode($result);
	}

	private function checkParams($postData) {
		$id = $postData->getPost("lightId");
		if (!is_int($id)) {
			$result = array(
						'status'=>200, 
						'data'=>array(
							'msg'=>'unsuccess',
							'result'=>false), 
						'error'=>'lightId is not int');
			echo json_encode($result);
			return false;
		}
		return true;
	}
}
