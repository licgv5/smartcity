<?php
/**
 * @name CityTableModel
 * @desc 访问mysql的city表
 * @author root
 */
class CityTableModel extends MysqlOperationModel {
	public function __construct(array $conf) {
		parent::__construct($conf);
	}   

	public function delCity($cityID) {
		try {
			$affected =  self::$_dbh->exec("DELETE FROM city WHERE id=$cityID");
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

	public function addCity($cityName, $provinceID) {
		try {
			$affected = self::$_dbh->exec("INSERT INTO `city` (`name`, `province_id`) VALUES ('$cityName', $provinceID)");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
			} else {
				$rs = self::$_dbh->query("SELECT id FROM `city` WHERE name='$cityName'");
				$rs->setFetchMode(PDO::FETCH_ASSOC);
				$rs = $rs->fetchAll();
				return $rs[0]['id'];
			}
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
		}
		return false;
	}

	public function getCityByProvince($provinceID) {
		try {
			$rs = self::$_dbh->query("SELECT id,name FROM `city` WHERE province_id=$provinceID");
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rs = $rs->fetchAll();
			return $rs;
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
		}
		return false;
	}

}
