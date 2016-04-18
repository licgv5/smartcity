<?php
/**
 * @name ZoneTableModel
 * @desc 访问mysql的zone表
 * @author root
 */
class ZoneTableModel extends MysqlOperationModel {
	public function __construct(array $conf) {
		parent::__construct($conf);
	}   

	public function delZone($zoneID) {
		try {
			$affected =  self::$_dbh->exec("DELETE FROM zone WHERE id=$zoneID");
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

	public function addzone($zonename, $cityid) {
		try {
			$affected = self::$_dbh->exec("insert into `zone` (`name`, `city_id`) values ('$zonename', $cityid)");
			if ($affected === false) {
				$err = $conn->errorinfo();
				seaslog::error($err[2]);
			} else {
				$rs = self::$_dbh->query("select id from `zone` where name='$zonename'");
				$rs->setfetchmode(pdo::fetch_assoc);
				$rs = $rs->fetchall();
				return $rs[0]['id'];
			}
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
		}
		return false;
	}

	public function getZoneByCity($cityID) {
		try {
			$rs = self::$_dbh->query("SELECT id,name FROM `zone` WHERE city_id=$cityID");
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rs = $rs->fetchAll();
			return $rs;
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
		}
		return false;
	}
}
