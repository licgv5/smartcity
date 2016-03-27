<?php
/**
 * @name LightVideoTableModel
 * @desc 访问mysql的light_video表
 * @author root
 */
class LightVideoTableModel extends MysqlOperationModel {
	public function __construct(array $conf) {
		parent::__construct($conf);
	}   

	public function getRealTimeVideo($id) {
		try {
			$rs = self::$_dbh->query("SELECT a.video_url, b.number FROM light_video a, light b 
						WHERE a.light_id=$id AND a.light_id=b.id AND a.video_type='1'");
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rs = $rs->fetchAll();

			if (count($rs) !== 1) {
				return false;
			} else {
				return $rs[0];
			}
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
			return false;
		}
	}

	public function getHistoryVideo($id, $pageNum, $pageSize) {
		try {
			$start = ($pageNum-1) * $pageSize;
			$end = $pageNum * $pageSize;
			$rs = self::$_dbh->query("SELECT a.video_url, a.video_time, b.number FROM light_video a, light b 
						WHERE a.light_id=$id AND a.light_id=b.id AND a.video_type='0' 
						ORDER BY a.id DESC LIMIT $start, $end");
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rs = $rs->fetchAll();
			if (count($rs) <= 0) {
				return false;
			} else {
				$lightVideoInfo = array();
				foreach($rs as $row) {
					$videoInfo['videoSrc'] = $row['video_url'];
					$videoInfo['videoTime'] = $row['video_time'];
					$lightVideoInfo['videoInfo'][] = $videoInfo;
				}
				$lightVideoInfo['lightNum'] = $rs[0]['number'];
				return $lightVideoInfo;
			}
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
			return false;
		}
	}
}
