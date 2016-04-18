<?php
/**
 * @name StatisticalDataModel
 * @desc 访问mysql的统计信息相关表
 * @author root
 */
class StatisticalDataModel extends MysqlOperationModel {
	public function __construct(array $conf) {
		parent::__construct($conf);
		$this->_levelConf = array(1=>'province', 2=>'city', 3=>'district', 4=>'group', 5=>'device');
		$this->_typeSensorConf = array(1=>'current', 2=>'ledVoltage', 3=>'ledCurrent', 
					4=>'pwm1Power', 5=>'pwm1Time', 6=>'temperature', 
					7=>'humidity', 8=>'pm2p5', 9=>'noise');
	}   

	public function getYearStatisticalData($level, $id, $type, $endDay) {
		if (!array_key_exists($level, $this->_levelConf) 
				|| !array_key_exists($type, $this->_typeSensorConf)) {
				return false;
		}
		$levelStr = $this->_levelConf[$level];
		$typeStr = $this->_typeSensorConf[$type];
		$data = array();
		for($i = 1; $i <= 12; $i++) {
			$data[] = "a.data".$i;
		}
		$columns = implode(",", $data);
		$sql = "SELECT $columns FROM year a, data_type b WHERE a.level='$levelStr' AND a.level_id=$id AND a.date='$endDay' AND b.type='$typeStr' AND a.data_type_id=b.id";
		try {
			$rs = self::$_dbh->query($sql);
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rs =  $rs->fetchAll();
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
			return false;
		}
		return $rs;
	}

	public function getMonthStatisticalData($level, $id, $type, $endDay) {
		if (!array_key_exists($level, $this->_levelConf) 
				|| !array_key_exists($type, $this->_typeSensorConf)) {
				return false;
		}
		$levelStr = $this->_levelConf[$level];
		$typeStr = $this->_typeSensorConf[$type];
		$data = array();
		for($i = 1; $i <= 30; $i++) {
			$data[] = "a.data".$i;
		}
		$columns = implode(",", $data);
		$sql = "SELECT $columns FROM month a, data_type b WHERE a.level='$levelStr' AND a.level_id=$id AND a.date='$endDay' AND b.type='$typeStr' AND a.data_type_id=b.id";
		try {
			$rs = self::$_dbh->query($sql);
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rs =  $rs->fetchAll();
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
			return false;
		}
		return $rs;

	}

	public function getWeekStatisticalData($level, $id, $type, $endDay) {
		if (!array_key_exists($level, $this->_levelConf) 
				|| !array_key_exists($type, $this->_typeSensorConf)) {
				return false;
		}
		$levelStr = $this->_levelConf[$level];
		$typeStr = $this->_typeSensorConf[$type];
		$data = array();
		for($i = 1; $i <= 7; $i++) {
			$data[] = "a.data".$i;
		}
		$columns = implode(",", $data);
		$sql = "SELECT $columns FROM week a, data_type b WHERE a.level='$levelStr' AND a.level_id=$id AND a.date='$endDay' AND b.type='$typeStr' AND a.data_type_id=b.id";
		try {
			$rs = self::$_dbh->query($sql);
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rs =  $rs->fetchAll();
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
			return false;
		}
		return $rs;

	}

	public function getDayStatisticalData($level, $id, $type, $endDay) {
		if (!array_key_exists($level, $this->_levelConf) 
					|| !array_key_exists($type, $this->_typeSensorConf)) {
			return false;
		}
		$levelStr = $this->_levelConf[$level];
		$typeStr = $this->_typeSensorConf[$type];
		$data = array();
		for($i = 1; $i <= 24; $i++) {
			$data[] = "a.data".$i;
		}
		$columns = implode(",", $data);
		$sql = "SELECT $columns FROM day a, data_type b WHERE a.level='$levelStr' AND a.level_id=$id AND a.date='$endDay' AND b.type='$typeStr' AND a.data_type_id=b.id";
		try {
			$rs = self::$_dbh->query($sql);
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rs =  $rs->fetchAll();
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
			return false;
		}
		return $rs;

	}

	protected $_levelConf = null;
	protected $_typeSensorConf = null;
}
