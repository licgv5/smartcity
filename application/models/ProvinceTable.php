<?php
/**
 * @name ProvinceTableModel
 * @desc 访问mysql的province表
 * @author root
 */
class ProvinceTableModel extends MysqlOperationModel {
	public function __construct(array $conf) {
		parent::__construct($conf);
	}   

	public function delProvince($provinceID) {
		try {
			$affected =  self::$_dbh->exec("DELETE FROM province WHERE id=$provinceID");
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

	public function addProvince($provinceName, $countryID) {
		try {
			$affected = self::$_dbh->exec("INSERT INTO `province` (`name`) VALUES ('$provinceName')");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
			} else {
				$rs = self::$_dbh->query("SELECT id FROM `province` WHERE name='$provinceName'");
				$rs->setFetchMode(PDO::FETCH_ASSOC);
				$rs = $rs->fetchAll();
				return $rs[0]['id'];
			}
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
		}
		return false;
	}

	public function getProvinceByCountry($countryID) {
		try {
			$rs = self::$_dbh->query("SELECT id,name FROM `province` WHERE country_id=$countryID");
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rs = $rs->fetchAll();
			return $rs;
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
		}
		return false;
	}

	public function getAllProvince() {
		try {
			$rs = self::$_dbh->query("SELECT id,name FROM `province`");
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rs = $rs->fetchAll();
			return $rs;
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
		}
		return false;
	}
}
