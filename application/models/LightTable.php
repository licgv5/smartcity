<?php
/**
 * @name LightTableModel
 * @desc 访问mysql的light表
 * @author root
 */
class LightTableModel extends MysqlOperationModel {
	public function __construct(array $conf) {
		parent::__construct($conf);
	}   

	public function getLightIdForUngrouped($districtId) {
		$rs = self::$_dbh->query("SELECT a.id, b.hardwareNum as name  FROM light as a, EEPROM as b WHERE a.district_id=$districtId and a.group_id is NULL and a.id=b.light_id");
		$rs->setFetchMode(PDO::FETCH_ASSOC);
		$rs =  $rs->fetchAll();
		$ids = array();
		foreach ($rs as $row) {
			$light = array('lightId'=>intval($row['id']), 'lightName' => $row['name']);
			$ids[] = $light;
		}
		return $ids;
	}

	public function getWorkLightNum($ids) {
		$idsStr = implode(",", $ids);
		$rs = self::$_dbh->query("SELECT count(*) as a FROM light WHERE id in ($idsStr) AND (status='1')");
		$rs->setFetchMode(PDO::FETCH_ASSOC);
		return intval($rs->fetchAll()[0]['a']);
	}

	public function getFaultLightNum($ids) {
		$idsStr = implode(",", $ids);
		$rs = self::$_dbh->query("SELECT count(*) as a FROM light WHERE id in ($idsStr) AND status='0'");
		$rs->setFetchMode(PDO::FETCH_ASSOC);
		return intval($rs->fetchAll()[0]['a']);
	}

	public function getGeographyInfo($ids) {
		$idsStr = implode(",", $ids);
		$rs = self::$_dbh->query("SELECT id as lightId, longitude as lng, latitude as lat, status as isWorked
					FROM light WHERE id in ($idsStr)");
		$rs->setFetchMode(PDO::FETCH_ASSOC);
		return $rs->fetchAll();
	}

	public function getLightInfo($id) {
		$rs = self::$_dbh->query("SELECT longitude, latitude, number, status, voice_status, 
					vehicle_status, environment_status, crowd_status FROM light WHERE id=$id");
		$rs->setFetchMode(PDO::FETCH_ASSOC);
		$rs = $rs->fetchAll();
		if (count($rs) === 0) {
			return false;
		} else {
			return $rs[0];
		}
	}

	public function getLightInfoByParent($level, $id) {
		if ($level === 3) {
			$parenName = "district_id";
		} else if ($level === 4) {
			$parenName = "group_id";
		} else {
			return false;
		}
		$rs = self::$_dbh->query("SELECT id as lightId, longitude as lightLng, latitude as lightLat, number as lightName 
					FROM light WHERE `$parenName`=$id");
		$rs->setFetchMode(PDO::FETCH_ASSOC);
		return $rs->fetchAll();
	}
	
	public function addLightToGroup($lightIds, $groupId) {
		$idsStr = implode(",", $lightIds);
		try {
			$affected = self::$_dbh->exec("UPDATE `light` SET group_id=$groupId WHERE id in ($idsStr)");
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

	public function deleteLight($id) {
		try {
			$affected = self::$_dbh->exec("DELETE FROM `light` WHERE `id`=$id");
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

	public function addLight($level, $id, $lightInfo) {
		try {
			$longitude = $lightInfo['longitude'];
			$latitude = $lightInfo['latitude'];
			$number = $lightInfo['number'];
			if ($level === 3) {
				$parent = "district_id";
				$parentTable = "district";
			} else if ($level === 4) {
				$parent = "group_id";
				$parentTable = "group";
			} else {
				return false;
			}
			//var_dump($parent);
			//var_dump($parentTable);
			$affected = self::$_dbh->exec("INSERT INTO `light` (`longitude`, `latitude`, `number`, `$parent`) 
						VALUES ($longitude, $latitude, $number, $id)");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}

			/*echo "aaaaaaa\n";
			$affected = self::$_dbh->exec("INSERT INTO `EEPROM` (`hardwareNum`) 
						VALUES ($number)");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}*/
			 //echo "bbbbbbbbbb\n";
			// 同步计划
			$affected = self::$_dbh->exec("UPDATE `light` a, `$parentTable` b SET 
						a.plan_id=b.plan_id, 
						a.plan_level=b.plan_level, 
						a.is_self_plan=0,
						a.voice_frequency_id=b.voice_frequency_id,
						a.voice_frequency_level=b.voice_frequency_level,
						a.crowd_frequency_id=b.crowd_frequency_id,
						a.crowd_frequency_level=b.crowd_frequency_level,
						a.vehicle_frequency_id=b.vehicle_frequency_id,
						a.vehicle_frequency_level=b.vehicle_frequency_level,
						a.environment_frequency_id=b.environment_frequency_id,
						a.environment_frequency_level=b.environment_frequency_level,
						a.light_frequency_id=b.light_frequency_id,
						a.light_frequency_level=b.light_frequency_level
						WHERE a.id=$id AND a.$parent=b.id;");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}
			//echo "ccccccccccccccc\n";
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
			return false;
		}
		return true;
	}

	public function editLight($id, $lightInfo) {
		try {
			$longitude = $lightInfo['longitude'];
			$latitude = $lightInfo['latitude'];
			$number = $lightInfo['number'];

			$affected = self::$_dbh->exec("UPDATE `light` a, EEPROM b SET 
					a.longitude=$longitude,
					a.latitude=$latitude,
					a.number=$number,
					b.hardwareNum=$number WHERE a.id=$id AND b.light_id=a.id"); 
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
}
