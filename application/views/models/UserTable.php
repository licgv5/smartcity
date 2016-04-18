<?php
/**
 * @name UserTableModel
 * @desc 访问mysql的user表
 * @author root
 */
class UserTableModel extends MysqlOperationModel {
	public function __construct(array $conf) {
		parent::__construct($conf);
	}   

	public function selectUser($userName) {
		$rs = self::$_dbh->query("SELECT * FROM user WHERE account='$userName'");
		$rs->setFetchMode(PDO::FETCH_ASSOC);
		return $rs->fetchAll();
	}

	public function insertUser($arrUserInfo) {
		return self::$_dbh->exec("INSERT INTO user SET account='$arrUserInfo[user]', password='$arrUserInfo[password]'");
	}
}
