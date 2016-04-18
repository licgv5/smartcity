<?php
/**
 * @name LightControlTableModel
 * @desc 访问mysql的light_control表
 * @author root
 */
class LightControlTableModel extends MysqlOperationModel {
	public function __construct(array $conf) {
		parent::__construct($conf);
	}   

	public function insertManualControl($lightIds, $brightness) {
		$lightNum = count($lightIds);
		if ($lightNum === 0) {
			return true;
		}
		try {
			for ($i = 0; $i < $lightNum; $i++) {
				if ($i === 0){
					$sql = "INSERT INTO `light_control` (`control_type`,`power`,`light_id`) VALUES ('manual', $brightness, $lightIds[$i])";
				} else {
					$sql.=",('manual',$brightness,$lightIds[$i])";
				}
			}
			$affected = self::$_dbh->exec($sql);
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
			return false;
		}
		return true;
	}

	public function insertPlanControl($lightIds, $brightness, $start, $long) {
		$lightNum = count($lightIds);
		if ($lightNum === 0) {
			return true;
		}
		try {
			for ($i = 0; $i < $lightNum; $i++) {
				if ($i === 0){
					$sql = "INSERT INTO `light_control` (`control_type`,`power`,`startTime`,`timespan`,`light_id`) VALUES ('manual', $start, $long,$brightness, $lightIds[$i])";
				} else {
					$sql.=",('manual',$start, $long,$brightness,$lightIds[$i])";
				}
				$affected = self::$_dbh->exec($sql);
				if ($affected === false) {
					$err = $conn->errorInfo();
					SeasLog::error($err[2]);
					return false;
				}
			} catch(PDOException $e) {
				SeasLog::error($e->getMessage());
				return false;
			}
			return true;
		}

		public function deletePlan($id) {
			try {
				$affected = self::$_dbh->exec("DELETE FROM `light_control` WHERE id=$id");
				if ($affected === false) {
					$err = $conn->errorInfo();
					SeasLog::error($err[2]);
					return false;
				}
			} catch(PDOException $e) {
				SeasLog::error($e->getMessage());
				return false;
			}
			return true;
		}

		public function getPlan($id, $level) {
			$idName = $this->getIDName($level);
			try {
				$rs = self::$_dbh->query("SELECT * FROM `light_control` WHERE `$idName`=$id");	
				$rs->setFetchMode(PDO::FETCH_ASSOC);
				$rs = $rs->fetchAll();
				return $rs;
			} catch(PDOException $e) {
				SeasLog::error($e->getMessage());
			}
			return false;
		}

		private function getIDName($level) {
			switch($level) {
				case 0:
					$idName = "";
					break;
				case 1:
					$idName = "province_id";
					break;
				case 2:
					$idName = "city_id";
					break;
				case 3:
					$idName = "district_id";
					break;
				case 4:
					$idName = "group_id";
					break;
				default:
					$idName = false;
			}
			return $idName;
		}
	}
