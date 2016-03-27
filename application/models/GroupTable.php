<?php
/**
 * @name GroupTableModel
 * @desc 访问mysql的group表
 * @author root
 */
class GroupTableModel extends MysqlOperationModel {
	public function __construct(array $conf) {
		parent::__construct($conf);
	}   

	public function delGroup($groupID) {
		try {
			$affected =  self::$_dbh->exec("DELETE FROM `group` WHERE id=$groupID");
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

	public function addGroup($groupName, $districtID) {
		try {
			$affected = self::$_dbh->exec("INSERT INTO `group` (`name`, `district_id`) VALUES ('$groupName', $districtID)");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
			} else {
				$rs = self::$_dbh->query("SELECT id FROM `group` WHERE name='$groupName'");
				$rs->setFetchMode(PDO::FETCH_ASSOC);
				$rs = $rs->fetchAll();
				return $rs[0]['id'];
			}
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
		}
		return false;
	}

	public function getGroupByDistrict($districtID) {
		try {
			$rs = self::$_dbh->query("SELECT id,name FROM `group` WHERE district_id=$districtID");
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rs = $rs->fetchAll();
			return $rs;
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
		}
		return false;
	}
}
