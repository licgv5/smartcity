<?php
class PostParams {
	public function __construct() {
		$this->_post_data = json_decode($GLOBALS['HTTP_RAW_POST_DATA'], TRUE);
	}

	public function getPost($key) {
		return $this->_post_data[$key];
	}

	protected $_post_data = null;
}
