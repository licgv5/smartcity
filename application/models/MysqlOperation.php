<?php
/**
 * @name MysqlOperationModel
 * @desc 用来操作mysql的基类
 * @author root
 */

class MysqlOperationModel {

	/**
	 * 初始化类
	 * @param array $conf 数据库配置
	 */   
	public function __construct(array $conf) {
		class_exists('PDO') or die("PDO: class not exists.");
		$this->_host = $conf['hostname'];
		$this->_port = $conf['port'];
		$this->_user = $conf['username'];
		$this->_pass = $conf['password'];
		$this->_dbName = $conf['database'];
		//连接数据库
		if ( is_null(self::$_dbh) ) {
			$this->_connect();
		}
	}   

	/**
	 * 连接数据库的方法
	 */
	protected function _connect() {
		$dsn ='mysql:host='.$this->_host.';port='.$this->_port.';dbname='.$this->_dbName;
		$options = $this->_pconnect ? array(PDO::ATTR_PERSISTENT=>true) : array();
		try { 
			$dbh = new PDO($dsn, $this->_user, $this->_pass, $options);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  //设置如果sql语句执行错误则抛出异常，事务会自动回滚
			$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); //禁用prepared statements的仿真效果(防SQL注入)
		} catch (PDOException $e) { 
			die('Connection failed: ' . $e->getMessage());
		}
		$dbh->exec('SET NAMES utf8');
		self::$_dbh = $dbh;
	}


	protected static $_dbh = null; //静态属性,所有数据库实例共用,避免重复连接数据库
	protected $_pconnect = true; //是否使用长连接
	protected $_host = 'localhost';
	protected $_port = 3306;
	protected $_user = 'root';
	protected $_pass = 'root';
	protected $_dbName = 'root';
}
