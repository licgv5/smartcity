<?php
/**
 * @name GetLightPlanController
 * @author root
 * @desc 获取已设置的计划模式
 */

class GetLightPlanController extends BaseControllerAbstract {

	public function getLightPlanAction() {
		$postData = new PostParams();
		if (!self::checkParams($postData)) {
			return;
		}

		$id = $postData->getPost("id");
		$level = $postData->getPost("level");
		$config = Yaf_Registry::get("config");
		$config = $config->toArray();

		$multipleTable = new MultipleTableModel($config["resources"]["database"]["params"]);
		$rs = $multipleTable->getEffectPlan($level, $id);
		$effectLevel = 0;
		$effectPlan = array();
		if ($rs !== false) {
			$effectLevel = $rs['level'];
			for($i = 1; $i <= 5; $i++) {
				if ($rs["plan_generator".$i] === "1") {
					$plan = array('start'=>intval($rs["plan_point".($i-1)]),
								'end'=>intval($rs["plan_point".$i]),
								'brightness'=>intval($rs["brightness".$i])
								);
					$effectPlan[] = $plan;
				} else {
					break;
				}
			}
		}
		$result = array(
					'status'=>200, 
					'data'=>array(
						'msg'=>'success',
						'result'=>true,
						'plans'=>$effectPlan,
						'level'=>$effectLevel
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
