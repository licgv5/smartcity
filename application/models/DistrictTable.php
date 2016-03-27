<?php
/**
 * @name DistrictTableModel
 * @desc 访问mysql的district表
 * @author root
 */
class DistrictTableModel extends MysqlOperationModel {
	public function __construct(array $conf) {
		parent::__construct($conf);
	}   

	public function delDistrict($districtID) {
		try {
			$affected =  self::$_dbh->exec("DELETE FROM district WHERE id=$districtID");
			if ($affected === false) {
				$err = $conn->errorInfo();
				if ($err[0] === '00000') {
					return true;
				} else {
					SeasLog::error($err[2]);
				}
			}
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
		}
		return true;
	}

	public function addDistrict($districtName, $cityID) {
		try {
			$affected = self::$_dbh->exec("INSERT INTO `district` (`name`, `city_id`) VALUES ('$districtName', $cityID)");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
			} else {
				$rs = self::$_dbh->query("SELECT id FROM `district` WHERE name='$districtName'");
				$rs->setFetchMode(PDO::FETCH_ASSOC);
				$rs = $rs->fetchAll();
				return $rs[0]['id'];
			}
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
		}
		return false;
	}

	public function getDistrictByCity($cityID) {
		try {
			$rs = self::$_dbh->query("SELECT id,name FROM `district` WHERE city_id=$cityID");
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rs = $rs->fetchAll();
			return $rs;
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
		}
		return false;
	}
}
