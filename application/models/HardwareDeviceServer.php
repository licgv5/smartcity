<?php
/**
 * @name HardwareDeviceServerModel
 * @desc 用来向后台接口发指令
 * @author root
 */

class HardwareDeviceServerModel {

	/**
	 * 初始化类
	 * @param array $conf redis配置
	 */   
	public function __construct(array $conf) {
		$this->_host = $conf['hostname'];
		//$this->_host = "nodec";
		$this->_port = $conf['port'];
		$this->_frequencyType = array(1=>0x0020, 2=>0x0021, 3=>0x0022, 4=>0x0023, 5=>0x0024);
	}   

	/**
	 * 发送消息
	 */
	protected function sendMsg($msg) {
		$this->_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($this->_socket !== false) {
			if(socket_connect($this->_socket, $this->_host, $this->_port) === false) {
				return false;
			}
		}
        socket_write($this->_socket, $msg, strlen($msg));
		while ($this->_response = socket_read($this->_socket, 8192)) {
			//var_dump($this->_response);
		}
		socket_close($this->_socket);
	}

	public function sendFrequencyInstruction($lights, $frequency, $type) {
		$ftype = $this->_frequencyType[$tpye];
		if (!$ftype) {
			return;
		}
		$msg = array('UpdateConfig'=>$ftype, 'Lights'=>$lights, 'Frequency'=>$frequency);
		$msgJson = json_encode($msg);
		$this->sendMsg($msgJson);
		//var_dump($msgJson);
		return true;
	}
	
	public function sendManualInstruction($lights, $power) {
		$msg = array('ManualControl'=>$lights, 'power'=>$power);
		$msgJson = json_encode($msg);
		$this->sendMsg($msgJson);
		//var_dump($msgJson);
		return true;
	}

	public function sendPlanInstruction($lights, $plans) {
		$plan_points = $plans['plan_point'];
		$plan_generators = $plans['plan_generator'];
		$brightness = $plans['brightness'];
		$timing = array();
		for ($i = 0; $i < 5; ++$i) {
			$plan = array();
			$plan['power'] = $brightness[$i];
			if ($plan_generators[$i] === 0 && $plan_points[$i] === 24*60) {
				$plan['startMin'] = 0;
			} else {
				$plan['startMin'] = $plan_points[$i];
			}
			$plan['duration'] = $plan_points[$i+1] - $plan_points[$i];
			$timing[] = $plan;
		}
		$planControls = array();
		foreach ($lights as $light) {
			$planControl = array();
			$planControl['light'] = $light;
			$planControl['timing'] = $timing;
			$planControls[] = $planControl;
		}
		$msg = array('PlanPlanControl'=>$planControls);
		$msgJson = json_encode($msg);
		//var_dump($this->_response);
		$this->sendMsg($msgJson);
		return true;
	}
	protected $_socket;
	protected $_host;
	protected $_port;
	protected $_response;
}
