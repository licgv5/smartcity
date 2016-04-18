<?php
//连接本地的 Redis 服务
/*$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
echo "Connection to server sucessfully\n";
$redis->auth("MhxzKhl");
//设置 redis 字符串数据
$redis->set("tutorial-name", "Redis tutorial");
// 获取存储的数据并输出
echo "Stored string in redis:: " . $redis->get("tutorial-name") ."\n";
$a = array();
$a[] = "1";
$a[] = "2";
$a[] = "3";
echo implode(",",$a);*/
$data[] = array('volume' => 67, 'edition' => 2);
$data[] = array('volume' => 86, 'edition' => 1);
$data[] = array('volume' => 85, 'edition' => 6);
$data[] = array('volume' => 98, 'edition' => 2);
$data[] = array('volume' => 86, 'edition' => 6);
$data[] = array('volume' => 67, 'edition' => 7);
foreach ($data as $key => $row) {
	    $volume[$key]  = $row['volume'];
		$edition[$key] = $row['edition'];
}
array_multisort($volume, SORT_ASC, $data);
var_dump($data);

$aa = "aaa";
var_dump("hello ".$aa."nihao");
?>
