<?php
/**
 * @name RedisOperationModel
 * @desc 用来操作redis的基类
 * @author root
 */

class RedisOperationModel {

	/**
	 * 初始化类
	 * @param array $conf redis配置
	 */   
	public function __construct(array $conf) {
		$this->_redis = new Redis();
		$this->_host = $conf['hostname'];
		$this->_port = $conf['port'];
		$this->_pass = $conf['password'];
		//连接redis
		if ( is_null(self::$_redis) ) {
			$this->_connect();
		}
	}   

	/**
	 * 连接redis的方法
	 */
	protected function _connect() {
		$this->_redis->connect($this->_host, $this->_port);
		$this->_redis->auth($this->_pass);
	}

	public function getAllHash($key) {
		return $this->_redis->hGetAll($key);
	}

	protected static $_redis = null; //静态属性,所有实例共用,避免重复连接
	protected $_host = 'localhost';
	protected $_port = 6379;
	protected $_pass = '';
}
