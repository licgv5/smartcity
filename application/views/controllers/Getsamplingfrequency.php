<?php
/**
 * @name GetSamplingFrequencyController
 * @author root
 * @desc 获取已设置的采样频率
 */

class GetSamplingFrequencyController extends BaseControllerAbstract {

	public function getSamplingFrequencyAction() {
		$postData = new PostParams();
		if (!self::checkParams($postData)) {
			return;
		}

		$id = $postData->getPost("id");
		$level = $postData->getPost("level");
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();

		$multipleTable = new MultipleTableModel($config["resources"]["database"]["params"]);
		$rs = $multipleTable->getEffectFrequency($level, $id);
		if ($rs !== false) {
			$set = $rs;	
		}
		$result = array(
					'status'=>200, 
					'data'=>array(
						'msg'=>'success',
						'result'=>true,
						'set'=>$set,
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
						'error'=>'params error');
			echo json_encode($result);
			return false;
		}
		return true;
	}
}
