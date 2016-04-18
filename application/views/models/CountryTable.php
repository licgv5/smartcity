<?php
/**
 * @name CountryTableModel
 * @desc 访问mysql的country表
 * @author root
 */
class CountryTableModel extends MysqlOperationModel {
	public function __construct(array $conf) {
		parent::__construct($conf);
	}   

	public function delCountry($countryID) {
		try {
			$affected =  self::$_dbh->exec("DELETE FROM country WHERE id=$countryID");
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

	public function addCountry($countryName) {
		try {
			$affected = self::$_dbh->exec("INSERT INTO `country` (`name`) VALUES ('$countryName')");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
			} else {
				$rs = self::$_dbh->query("SELECT id FROM `country` WHERE name='$countryName'");
				$rs->setFetchMode(PDO::FETCH_ASSOC);
				$rs = $rs->fetchAll();
				return $rs[0]['id'];
			}
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
		}
		return false;
	}

	public function getAllCountry($countryName) {
		try {
			$rs = self::$_dbh->query("SELECT id,name FROM `country`");
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rs = $rs->fetchAll();
			return $rs;
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
		}
		return false;
	}
}
