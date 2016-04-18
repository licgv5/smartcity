<?php
/**
 * @name MultipleTableModel 
 * @desc 访问mysql的多个地域表
 * @author root
 */
class MultipleTableModel extends MysqlOperationModel {
	public function __construct(array $conf) {
		//$this->_provinceTable = new ProvinceTableModel($conf);
		//$this->_cityTable = new CityTableModel($conf);
		//$this->_districtTable = new DistrictTableModel($conf);
		//$this->_groupTable = new GroupTableModel($conf);
		//$this->_lightTable = new LightTableModel($conf);
		parent::__construct($conf);
		$this->_levelConf = array(0=>'country', 1=>'province', 2=>'city', 3=>'district', 4=>'group', 5=>'light');
		$this->_sensorConf = array(1=>'light', 2=>'environment', 3=>'vehicle', 4=>'crowd', 5=>'voice');
		$this->_sensorEEPROM = array(1=>'pwmUploadInterval', 
					2=>'envUploadInterval', 
					3=>'carUploadInterva', 
					4=>'humanUploadInterval',
					5=>'soundUploadInterva');
	}   

	public function getLightIdByForefather($level, $id) {
		switch($level) {
			case 0:
				// 查找中国所有的路灯id
				$LightIds = $this->getAllLightInCountry();
				break;
			case 1:
				// 查找省所有路灯id
				$LightIds = $this->getAllLightInProvince($id);
				break;
			case 2:
				// 查找城市路灯id
				$LightIds = $this->getAllLightInCity($id);
				break;
			case 3:
				// 路灯集下所有路灯id
				$LightIds = $this->getAllLightInDistrict($id);
				break;
			case 4:
				// 查找路灯分组下路灯
				$LightIds = $this->getAllLightInGroup($id);
				break;
			default:
				return false;
		}
		$ids = array();
		foreach ($LightIds as $row) {
			$ids[] = $row['id'];
		}
		return $ids;
	}

	public function setLightPlans($level, $id, $plans, $type) {

		switch($level) {
			case 1:
				// 对省一级设置计划
				$rs = $this->setPlanInProvince($level, $id, $plans, $type);
				break;
			case 2:
				// 对市一级设置计划
				$rs = $this->setPlanInCity($level, $id, $plans, $type);
				break;
			case 3:
				// 对路灯集设置计划
				$rs = $this->setPlanInDistrict($level, $id, $plans, $type);
				break;
			case 4:
				// 对分组设置计划
				$rs = $this->setPlanInGroup($level, $id, $plans, $type);
				break;
			default:
				$rs = false;
		}
		return $rs;
	}

	public function setSamplingFrequency($level, $id, $frequency, $type) {
		switch($level) {
			case 1:
				// 对省一级设置采样频率
				$rs = $this->setSamplingFrequencyInProvince($level, $id, $frequency, $type);
				break;
			case 2:
				// 对市一级设置采样频率
				$rs = $this->setSamplingFrequencyInCity($level, $id, $frequency, $type);
				break;
			case 3:
				// 对路灯集设置采样频率
				$rs = $this->setSamplingFrequencyInDistrict($level, $id, $frequency, $type);
				break;
			case 4:
				// 对分组设置采样频率
				$rs = $this->setSamplingFrequencyInGroup($level, $id, $frequency, $type);
				break;
			default:
				$rs = false;
		}
		return $rs;
	}

	public function getEffectFrequencyLight($level, $id, $type) {
		try {
			if (!array_key_exists($type, $this->_sensorConf)) {
				return false;
			}
			$sensor = $this->_sensorConf[$type];
		    $col = $this->_sensorEEPROM[$type];
			$rs = self::$_dbh->query("SELECT id FROM `light` WHERE `".$sensor."frequency_id`=$id AND `".$sensor."frequency_level`=$level");
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rs = $rs->fetchAll();
			$ids = array();
			foreach ($rs as $row) {
				$ids[] = intval($row['id']);
			}
			return $ids;
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
		}
		return false;
	}

	public function getEffectLight($level, $id) {
		try {
			$rs = self::$_dbh->query("SELECT id FROM `light` WHERE `is_self_plan`=0 AND `plan_id`=$id AND `plan_level`=$level");
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rs = $rs->fetchAll();
			$ids = array();
			foreach ($rs as $row) {
				$ids[] = intval($row['id']);
			}
			return $ids;
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
		}
		return false;
	}

	public function getEffectFrequency($level, $id) {
		if (array_key_exists($level, $this->_levelConf)) {
			$table = $this->_levelConf[$level];
		} else {
			return false;
		}
		try {
			$rs = self::$_dbh->query("SELECT 
						voice_frequency_id, 
						voice_frequency_level,
						crowd_frequency_id,
						crowd_frequency_level,
						vehicle_frequency_id,
						vehicle_frequency_level,
						environment_frequency_id,
						environment_frequency_level,
						light_frequency_id,
						light_frequency_level
						FROM `$table` WHERE `id`=$id");
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rs = $rs->fetchAll();
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
			return false;
		}
		if ($rs === false || count($rs) !== 1) {
			return false;
		}

		return $this->getFrequencyInfo($rs[0]);
	}

	public function getFrequencyInfo($effectFrequency) {
		try{
			if ($effectFrequency['voice_frequency_id'] !== 'NULL' 
						&& $effectFrequency['voice_frequency_level'] !== '0'
						&& array_key_exists(intval($effectFrequency['voice_frequency_level']), $this->_levelConf)) {
				$table = $this->_levelConf[intval($effectFrequency['voice_frequency_level'])];
				$id = intval($effectFrequency['voice_frequency_id']);
				$rs = self::$_dbh->query("SELECT `voice_frequency` as frequency, `voice_frequency_level` as level 
							FROM `$table` WHERE `id`=$id");
				$rs->setFetchMode(PDO::FETCH_ASSOC);
				$rs = $rs->fetchAll();
				if ($rs !== false && count($rs) !== 0) {
					$frequencys['voice'] = $rs[0];
				}
			}

			if ($effectFrequency['crowd_frequency_id'] !== 'NULL' 
						&& $effectFrequency['crowd_frequency_level'] !== '0'
						&& array_key_exists(intval($effectFrequency['crowd_frequency_level']), $this->_levelConf)) {
				$table = $this->_levelConf[intval($effectFrequency['crowd_frequency_level'])];
				$id = intval($effectFrequency['crowd_frequency_id']);
				$rs = self::$_dbh->query("SELECT `crowd_frequency` as frequency, `crowd_frequency_level` as level 
							FROM `$table` WHERE `id`=$id");
				$rs->setFetchMode(PDO::FETCH_ASSOC);
				$rs = $rs->fetchAll();
				if ($rs !== false && count($rs) !== 0) {
					$frequencys['crowd'] = $rs[0];
				}
			}
			if ($effectFrequency['vehicle_frequency_id'] !== 'NULL' 
						&& $effectFrequency['vehicle_frequency_level'] !== '0'
						&& array_key_exists(intval($effectFrequency['vehicle_frequency_level']), $this->_levelConf)) {
				$table = $this->_levelConf[intval($effectFrequency['vehicle_frequency_level'])];
				$id = intval($effectFrequency['vehicle_frequency_id']);
				$rs = self::$_dbh->query("SELECT `vehicle_frequency` as frequency, `vehicle_frequency_level` as level 
							FROM `$table` WHERE `id`=$id");
				$rs->setFetchMode(PDO::FETCH_ASSOC);
				$rs = $rs->fetchAll();
				if ($rs !== false && count($rs) !== 0) {
					$frequencys['vehicle'] = $rs[0];
				}
			}

			if ($effectFrequency['environment_frequency_id'] !== 'NULL' 
						&& $effectFrequency['environment_frequency_level'] !== '0'
						&& array_key_exists(intval($effectFrequency['environment_frequency_level']), $this->_levelConf)) {
				$table = $this->_levelConf[intval($effectFrequency['environment_frequency_level'])];
				$id = intval($effectFrequency['environment_frequency_id']);
				$rs = self::$_dbh->query("SELECT `environment_frequency` as frequency, `environment_frequency_level` as level 
							FROM `$table` WHERE `id`=$id");
				$rs->setFetchMode(PDO::FETCH_ASSOC);
				$rs = $rs->fetchAll();
				if ($rs !== false && count($rs) !== 0) {
					$frequencys['environment'] = $rs[0];
				}
			}

			if ($effectFrequency['light_frequency_id'] !== 'NULL' 
						&& $effectFrequency['light_frequency_level'] !== '0'
						&& array_key_exists(intval($effectFrequency['light_frequency_level']), $this->_levelConf)) {
				$table = $this->_levelConf[intval($effectFrequency['light_frequency_level'])];
				$id = intval($effectFrequency['light_frequency_id']);
				$rs = self::$_dbh->query("SELECT `light_frequency` as frequency, `light_frequency_level` as level 
							FROM `$table` WHERE `id`=$id");
				$rs->setFetchMode(PDO::FETCH_ASSOC);
				$rs = $rs->fetchAll();
				if ($rs !== false && count($rs) !== 0) {
					$frequencys['light'] = $rs[0];
				}
			}
			return $frequencys;
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
			return false;
		}
	}

	public function getEffectPlan($level, $id) {
		if (array_key_exists($level, $this->_levelConf)) {
			$table = $this->_levelConf[$level];
		} else {
			return false;
		}

		try {
			$rs = self::$_dbh->query("SELECT plan_id, plan_level FROM `$table` WHERE `id`=$id");
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rs = $rs->fetchAll();
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
			return false;
		}

		if ($rs === false 
					|| count($rs) !== 1
					|| $rs[0]["plan_id"] === "NULL"
					|| $rs[0]["plan_level"] === "0") {
			return false;
		}

		$effctPlan["plan_id"] = intval($rs[0]["plan_id"]);
		$effctPlan["plan_level"] = intval($rs[0]["plan_level"]);

		$planInfo = $this->getPlanInfo($effctPlan);
		if ($planInfo === false || count($planInfo) === 0) {
			return false;
		}
		$planInfo[0]['level'] = $effctPlan["plan_level"];
		$planInfo[0]['plan_point0'] = 0; //方便后续运算
		$planInfo[0]['plan_point5'] = 24*60; //方便后续运算
		return $planInfo[0];
	}

	public function getPlanInfo($effectPlan) {
		if (array_key_exists($effectPlan['plan_level'], $this->_levelConf)) {
			$table = $this->_levelConf[$effectPlan['plan_level']];
		} else {
			return false;
		}	
		$id = $effectPlan['plan_id'];

		try {
			$rs = self::$_dbh->query("SELECT `plan_point1`,
						`plan_point2`,
						`plan_point3`,
						`plan_point4`,
						`plan_generator1`,
						`plan_generator2`,
						`plan_generator3`,
						`plan_generator4`,
						`plan_generator5`,
						`brightness1`,
						`brightness2`,
						`brightness3`,
						`brightness4`,
						`brightness5`
						FROM `$table` WHERE `id`=$id");
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rs = $rs->fetchAll();
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
			return false;
		}
		return $rs;
	}

	protected function updateEEPROMFrequency($level, $id, $frequency, $type) {
		try {
			if (!array_key_exists($type, $this->_sensorConf)
						|| !array_key_exists($type, $this->_sensorEEPROM)) {
				return false;
			}
			$sensor = $this->_sensorConf[$type];
		    $col = $this->_sensorEEPROM[$type];
			$affected = self::$_dbh->exec("UPDATE `EEPROM` SET 
						`$col`=$frequency,
						`control_type`='manual' WHERE light_id in 
						(SELECT id FROM `light` WHERE `".$sensor."_frequency_id`=$id AND `".$sensor."_frequency_level`=$level)");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
			return false;
		}
		return true;
	}

	protected function updateEEPROMPlan($level, $id, $plans, $type) {
		try {
			if($type === "plan") {
				$plan_points = $plans['plan_point'];
				$plan_generators = $plans['plan_generator'];
				$brightness = $plans['brightness']; 
				$startMin = array();
				$duration = array();
				for ($i = 0; $i < 5; ++$i) {
					if ($plan_generators[$i] === 0 && $plan_points[$i] === 24*60) {
						$startMin[] = 0;
					} else {
						$startMin[] = $plan_points[$i];
					}
					$duration[] = $plan_points[$i+1] - $plan_points[$i];
				}

				$affected = self::$_dbh->exec("UPDATE `EEPROM` SET 
							`pwm1Power1`=$brightness[0],
							`pwm1StartMin1`=$startMin[0],
							`pwm1Duration1`=$duration[0],
							`pwm1Power2`=$brightness[1],
							`pwm1StartMin2`=$startMin[1],
							`pwm1Duration2`=$duration[1],
							`pwm1Power3`=$brightness[2],
							`pwm1StartMin3`=$startMin[2],
							`pwm1Duration3`=$duration[2],
							`pwm1Power4`=$brightness[3],
							`pwm1StartMin4`=$startMin[3],
							`pwm1Duration4`=$duration[3],
							`pwm1Power5`=$brightness[4],
							`pwm1StartMin5`=$startMin[4],
							`pwm1Duration5`=$duration[4],
							`control_type`='plan' WHERE light_id in 
							(SELECT id FROM `light` WHERE `plan_id`=$id AND `plan_level`=$level)");
				if ($affected === false) {
					$err = $conn->errorInfo();
					SeasLog::error($err[2]);
					return false;
				}
			} else if ($type === "manual") {
				$affected = self::$_dbh->exec("UPDATE `EEPROM` SET 
							`manual_brightness`=$plans,
							`control_type`='manual' WHERE light_id in 
							(SELECT id FROM `light` WHERE `is_self_plan`=0 AND `plan_id`=$id AND `plan_level`=$level)");
				if ($affected === false) {
					$err = $conn->errorInfo();
					SeasLog::error($err[2]);
					return false;
				}
			}
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
			return false;
		}
		return true;
	}

	protected function setPlanInProvince($level, $id, $plans, $type) {
		try {
			if ($type === "plan") {
				// 设置省的计划
				$plan_points = $plans['plan_point'];
				$plan_generators = $plans['plan_generator'];
				$brightness = $plans['brightness']; 
				$affected = self::$_dbh->exec("UPDATE `province` SET 
							`plan_point1`=$plan_points[1],
							`plan_point2`=$plan_points[2],
							`plan_point3`=$plan_points[3],
							`plan_point4`=$plan_points[4],
							`plan_generator1`=$plan_generators[0],
							`plan_generator2`=$plan_generators[1],
							`plan_generator3`=$plan_generators[2],
							`plan_generator4`=$plan_generators[3],
							`plan_generator5`=$plan_generators[4],
							`brightness1`=$brightness[0],
							`brightness2`=$brightness[1],
							`brightness3`=$brightness[2],
							`brightness4`=$brightness[3],
							`brightness5`=$brightness[4],
							`control_type`='plan',
							`plan_id`=$id,
							`plan_level`=$level,
							`is_self_plan`=1 WHERE id=$id");
				if ($affected === false) {
					$err = $conn->errorInfo();
					SeasLog::error($err[2]);
					return false;
				}
			} else if ($type === "manual") {
				$affected = self::$_dbh->exec("UPDATE `province` SET 
							`manual_brightness`=$plans,
							`control_type`='manual',
							`plan_id`=$id,
							`plan_level`=$level,
							`is_self_plan`=1 WHERE id=$id");
				if ($affected === false) {
					$err = $conn->errorInfo();
					SeasLog::error($err[2]);
					return false;
				}
			}
			// 对下级各层未设置计划的节点设置有效计划
			$affected = self::$_dbh->exec("UPDATE `city` SET
						`plan_id`=$id,
						`plan_level`=$level,
						`is_self_plan`=0 WHERE `province_id`=$id");

			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}
			$affected = self::$_dbh->exec("UPDATE `district` SET
						`plan_id`=$id,
						`plan_level`=$level,
						`is_self_plan`=0 WHERE `city_id` in (SELECT `id` FROM `city` WHERE `province_id`=$id AND `is_self_plan`=0)");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}

			$affected = self::$_dbh->exec("UPDATE `group` SET
						`plan_id`=$id,
						`plan_level`=$level,
						`is_self_plan`=0 
						WHERE `district_id` in (SELECT `id` FROM `district` WHERE `city_id` in (
								SELECT `id` FROM `city` WHERE `province_id`=$id AND `is_self_plan`=0) 
							AND `is_self_plan`=0)
						AND `is_self_plan`=0");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}

			$affected = self::$_dbh->exec("UPDATE `light` SET
						`plan_id`=$id,
						`plan_level`=$level,
						`is_self_plan`=0 
						WHERE `district_id` in (SELECT `id` FROM `district` WHERE `city_id` in (
								SELECT `id` FROM `city` WHERE `province_id`=$id AND `is_self_plan`=0) 
							AND `is_self_plan`=0)
						AND `is_self_plan`=0 AND `group_id` is NULL");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}

			$affected = self::$_dbh->exec("UPDATE `light` SET
						`plan_id`=$id,
						`plan_level`=$level,
						`is_self_plan`=0 
						WHERE `group_id` in (SELECT `id` FROM `group` WHERE `district_id` in 
							(SELECT `id` FROM `district` WHERE `city_id` in 
							 (SELECT `id` FROM `city` WHERE `province_id`=$id AND `is_self_plan`=0) 
							 AND `is_self_plan`=0)
							AND `is_self_plan`=0)
						AND `is_self_plan`=0");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}
			return $this->updateEEPROMPlan($level, $id, $plans, $type);
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
			return false;
		}
		return true;
	}

	protected function setPlanInCity($level, $id, $plans, $type) {
		try {
			if ($type === "plan") {
				$plan_points = $plans['plan_point'];
				$plan_generators = $plans['plan_generator'];
				$brightness = $plans['brightness']; 
				// 设置城市的计划
				$affected = self::$_dbh->exec("UPDATE `city` SET 
							`plan_point1`=$plan_points[1],
							`plan_point2`=$plan_points[2],
							`plan_point3`=$plan_points[3],
							`plan_point4`=$plan_points[4],
							`plan_generator1`=$plan_generators[0],
							`plan_generator2`=$plan_generators[1],
							`plan_generator3`=$plan_generators[2],
							`plan_generator4`=$plan_generators[3],
							`plan_generator5`=$plan_generators[4],
							`brightness1`=$brightness[0],
							`brightness2`=$brightness[1],
							`brightness3`=$brightness[2],
							`brightness4`=$brightness[3],
							`brightness5`=$brightness[4],
							`control_type`='plan',
							`plan_id`=$id,
							`plan_level`=$level,
							`is_self_plan`=1 WHERE id=$id");
				if ($affected === false) {
					$err = $conn->errorInfo();
					SeasLog::error($err[2]);
					return false;
				}
			} else if ($type === "manual") {
				$affected = self::$_dbh->exec("UPDATE `city` SET 
							`manual_brightness`=$plans,
							`control_type`='manual',
							`plan_id`=$id,
							`plan_level`=$level,
							`is_self_plan`=1 WHERE id=$id");
				if ($affected === false) {
					$err = $conn->errorInfo();
					SeasLog::error($err[2]);
					return false;
				}
			}
			// 对下级各层未设置计划的节点设置有效计划
			$affected = self::$_dbh->exec("UPDATE `district` SET
						`plan_id`=$id,
						`plan_level`=$level,
						`is_self_plan`=0 WHERE `city_id`=$id");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}

			$affected = self::$_dbh->exec("UPDATE `group` SET
						`plan_id`=$id,
						`plan_level`=$level,
						`is_self_plan`=0 WHERE `district_id` in (SELECT `id` FROM `district` WHERE `city_id`=$id AND `is_self_plan`=0)
						AND `is_self_plan`=0");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}

			$affected = self::$_dbh->exec("UPDATE `light` SET
						`plan_id`=$id,
						`plan_level`=$level,
						`is_self_plan`=0 WHERE `district_id` in (SELECT `id` FROM `district` WHERE `city_id`=$id AND `is_self_plan`=0) 
						AND `is_self_plan`=0 AND `group_id` is NULL");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}
			
			$affected = self::$_dbh->exec("UPDATE `light` SET
						`plan_id`=$id,
						`plan_level`=$level,
						`is_self_plan`=0 
						WHERE `group_id` in (SELECT `id` FROM `group` WHERE `district_id` in (
								SELECT `id` FROM `district` WHERE `city_id`=$id AND `is_self_plan`=0) 
							AND `is_self_plan`=0)
						AND `is_self_plan`=0");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}
			return $this->updateEEPROMPlan($level, $id, $plans, $type);
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
			return false;
		}
		return true;
	}

	protected function setPlanInDistrict($level, $id, $plans, $type) {
		try {
			if ($type === "plan") {
				$plan_points = $plans['plan_point'];
				$plan_generators = $plans['plan_generator'];
				$brightness = $plans['brightness']; 
				// 路灯集的计划
				$affected = self::$_dbh->exec("UPDATE `district` SET 
							`plan_point1`=$plan_points[1],
							`plan_point2`=$plan_points[2],
							`plan_point3`=$plan_points[3],
							`plan_point4`=$plan_points[4],
							`plan_generator1`=$plan_generators[0],
							`plan_generator2`=$plan_generators[1],
							`plan_generator3`=$plan_generators[2],
							`plan_generator4`=$plan_generators[3],
							`plan_generator5`=$plan_generators[4],
							`brightness1`=$brightness[0],
							`brightness2`=$brightness[1],
							`brightness3`=$brightness[2],
							`brightness4`=$brightness[3],
							`brightness5`=$brightness[4],
							`control_type`='plan',
							`plan_id`=$id,
							`plan_level`=$level,
							`is_self_plan`=1 WHERE id=$id");
				if ($affected === false) {
					$err = $conn->errorInfo();
					SeasLog::error($err[2]);
					return false;
				}
			} else if ($type === "manual") {
				$affected = self::$_dbh->exec("UPDATE `district` SET 
							`manual_brightness`=$plans,
							`control_type`='manual',
							`plan_id`=$id,
							`plan_level`=$level,
							`is_self_plan`=1 WHERE id=$id");
				if ($affected === false) {
					$err = $conn->errorInfo();
					SeasLog::error($err[2]);
					return false;
				}
			}
			// 对下级各层未设置计划的节点设置有效计划
			$affected = self::$_dbh->exec("UPDATE `group` SET
						`plan_id`=$id,
						`plan_level`=$level,
						`is_self_plan`=0 WHERE `district_id`=$id AND `is_self_plan`=0");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}

			$affected = self::$_dbh->exec("UPDATE `light` SET
						`plan_id`=$id,
						`plan_level`=$level,
						`is_self_plan`=0 WHERE `district_id`=$id AND `is_self_plan`=0 AND `group_id` is NULL");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}
			
			$affected = self::$_dbh->exec("UPDATE `light` SET
						`plan_id`=$id,
						`plan_level`=$level,
						`is_self_plan`=0 WHERE `group_id` in (SELECT `id` FROM `group` WHERE `district_id`=$id AND `is_self_plan`=0)");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}
			return $this->updateEEPROMPlan($level, $id, $plans, $type);
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
			return false;
		}
		return true;
	}

	protected function setPlanInGroup($level, $id, $plans, $type) {
		try {
			if ($type === "plan") {
				$plan_points = $plans['plan_point'];
				$plan_generators = $plans['plan_generator'];
				$brightness = $plans['brightness']; 
				// 设置分组的计划
				$affected = self::$_dbh->exec("UPDATE `group` SET 
							`plan_point1`=$plan_points[1],
							`plan_point2`=$plan_points[2],
							`plan_point3`=$plan_points[3],
							`plan_point4`=$plan_points[4],
							`plan_generator1`=$plan_generators[0],
							`plan_generator2`=$plan_generators[1],
							`plan_generator3`=$plan_generators[2],
							`plan_generator4`=$plan_generators[3],
							`plan_generator5`=$plan_generators[4],
							`brightness1`=$brightness[0],
							`brightness2`=$brightness[1],
							`brightness3`=$brightness[2],
							`brightness4`=$brightness[3],
							`brightness5`=$brightness[4],
							`control_type`='plan',
							`plan_id`=$id,
							`plan_level`=$level,
							`is_self_plan`=1 WHERE id=$id");
				if ($affected === false) {
					$err = $conn->errorInfo();
					SeasLog::error($err[2]);
					return false;
				}
			} else if ($type === "manual") {
				$affected = self::$_dbh->exec("UPDATE `group` SET 
							`manual_brightness`=$plans,
							`control_type`='manual',
							`plan_id`=$id,
							`plan_level`=$level,
							`is_self_plan`=1 WHERE id=$id");
				if ($affected === false) {
					$err = $conn->errorInfo();
					SeasLog::error($err[2]);
					return false;
				}
			}
			// 对下级各层未设置计划的节点设置有效计划
			$affected = self::$_dbh->exec("UPDATE `light` SET
						`plan_id`=$id,
						`plan_level`=$level,
						`is_self_plan`=0 WHERE `group_id`=$id AND `is_self_plan`=0");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}
			return $this->updateEEPROMPlan($level, $id, $plans, $type);
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
			return false;
		}
		return true;
	}

	protected function setSamplingFrequencyInProvince($level, $id, $frequency, $type) {
		try {
			if (!array_key_exists($type, $this->_sensorConf)) {
				return false;
			}
			$sensor = $this->_sensorConf[$type];
			$affected = self::$_dbh->exec("UPDATE `province` SET 
						`".$sensor."_frequency`=$frequency,
						`".$sensor."_frequency_id`=$id,
						`".$sensor."_frequency_level`=$level,
						`is_self_".$sensor."_frequency`=1 WHERE id=$id");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}
			// 对下级各层未设置计划的节点设置有效计划
			$affected = self::$_dbh->exec("UPDATE `city` SET
						`".$sensor."_frequency_id`=$id,
						`".$sensor."_frequency_level`=$level,
						`is_self_".$sensor."_frequency`=0 WHERE `province_id`=$id");

			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}
			$affected = self::$_dbh->exec("UPDATE `district` SET
						`".$sensor."_frequency_id`=$id,
						`".$sensor."_frequency_level`=$level,
						`is_self_".$sensor."_frequency`=0 WHERE `city_id` in (SELECT `id` FROM `city` WHERE `province_id`=$id AND `is_self_".$sensor."_frequency`=0)");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}

			$affected = self::$_dbh->exec("UPDATE `group` SET
						`".$sensor."_frequency_id`=$id,
						`".$sensor."_frequency_level`=$level,
						`is_self_".$sensor."_frequency`=0 
						WHERE `district_id` in (SELECT `id` FROM `district` WHERE `city_id` in (
								SELECT `id` FROM `city` WHERE `province_id`=$id AND `is_self_".$sensor."_frequency`=0) 
							AND `is_self_".$sensor."_frequency`=0)
						AND `is_self_".$sensor."_frequency`=0");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}

			$affected = self::$_dbh->exec("UPDATE `light` SET
						`".$sensor."_frequency_id`=$id,
						`".$sensor."_frequency_level`=$level,
						`is_self_".$sensor."_frequency`=0 
						WHERE `district_id` in (SELECT `id` FROM `district` WHERE `city_id` in (
								SELECT `id` FROM `city` WHERE `province_id`=$id AND `is_self_".$sensor."_frequency`=0) 
							AND `is_self_".$sensor."_frequency`=0)
						AND `is_self_".$sensor."_frequency`=0 AND `group_id` is NULL");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}

			$affected = self::$_dbh->exec("UPDATE `light` SET
						`".$sensor."_frequency_id`=$id,
						`".$sensor."_frequency_level`=$level,
						`is_self_".$sensor."_frequency`=0 
						WHERE `group_id` in (SELECT `id` FROM `group` WHERE `district_id` in 
							(SELECT `id` FROM `district` WHERE `city_id` in 
							 (SELECT `id` FROM `city` WHERE `province_id`=$id AND `is_self_".$sensor."_frequency`=0) 
							 AND `is_self_".$sensor."_frequency`=0)
							AND `is_self_".$sensor."_frequency`=0)
						AND `is_self_".$sensor."_frequency`=0");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}
			return $this->updateEEPROMFrequency($level, $id, $frequency, $type);
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
			return false;
		}
		return true;
	}

	protected function setSamplingFrequencyInCity($level, $id, $frequency, $type) {
		try {
			if (!array_key_exists($type, $this->_sensorConf)) {
				return false;
			}
			$sensor = $this->_sensorConf[$type];
			$affected = self::$_dbh->exec("UPDATE `city` SET 
						`".$sensor."_frequency`=$frequency,
						`control_type`='manual',
						`".$sensor."_frequency_id`=$id,
						`".$sensor."_frequency_level`=$level,
						`is_self_".$sensor."_frequency`=1 WHERE id=$id");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}
			// 对下级各层未设置计划的节点设置有效计划
			$affected = self::$_dbh->exec("UPDATE `district` SET
						`".$sensor."_frequency_id`=$id,
						`".$sensor."_frequency_level`=$level,
						`is_self_".$sensor."_frequency`=0 WHERE `city_id`=$id");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}

			$affected = self::$_dbh->exec("UPDATE `group` SET
						`".$sensor."_frequency_id`=$id,
						`".$sensor."_frequency_level`=$level,
						`is_self_".$sensor."_frequency`=0 WHERE `district_id` in 
							(SELECT `id` FROM `district` WHERE `city_id`=$id AND `is_self_".$sensor."_frequency`=0)
						AND `is_self_".$sensor."_frequency`=0");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}

			$affected = self::$_dbh->exec("UPDATE `group` SET
						`".$sensor."_frequency_id`=$id,
						`".$sensor."_frequency_level`=$level,
						`is_self_".$sensor."_frequency`=0 WHERE `district_id` in 
							(SELECT `id` FROM `district` WHERE `city_id`=$id AND `is_self_".$sensor."_frequency`=0)
						AND `is_self_".$sensor."_frequency`=0 AND `group_id` is NULL");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}
			
			$affected = self::$_dbh->exec("UPDATE `light` SET
						`".$sensor."_frequency_id`=$id,
						`".$sensor."_frequency_level`=$level,
						`is_self_".$sensor."_frequency`=0 
						WHERE `group_id` in (SELECT `id` FROM `group` WHERE `district_id` in (
								SELECT `id` FROM `district` WHERE `city_id`=$id AND `is_self_".$sensor."_frequency`=0) 
							AND `is_self_".$sensor."_frequency`=0)
						AND `is_self_".$sensor."_frequency`=0");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}
			return $this->updateEEPROMFrequency($level, $id, $frequency, $type);
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
			return false;
		}
		return true;
	}

	protected function setSamplingFrequencyInDistrict($level, $id, $frequency, $type) {
		try {
			if (!array_key_exists($type, $this->_sensorConf)) {
				return false;
			}
			$sensor = $this->_sensorConf[$type];
			$affected = self::$_dbh->exec("UPDATE `district` SET 
						`".$sensor."_frequency`=$frequency,
						`control_type`='manual',
						`".$sensor."_frequency_id`=$id,
						`".$sensor."_frequency_level`=$level,
						`is_self_".$sensor."_frequency`=1 WHERE id=$id");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}
			// 对下级各层未设置计划的节点设置有效计划
			$affected = self::$_dbh->exec("UPDATE `group` SET
						`".$sensor."_frequency_id`=$id,
						`".$sensor."_frequency_level`=$level,
						`is_self_".$sensor."_frequency`=0 WHERE `district_id`=$id 
						AND `is_self_".$sensor."_frequency`=0");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}

			$affected = self::$_dbh->exec("UPDATE `light` SET
						`".$sensor."_frequency_id`=$id,
						`".$sensor."_frequency_level`=$level,
						`is_self_".$sensor."_frequency`=0 WHERE `district_id`=$id
						AND `is_self_".$sensor."_frequency`=0 AND `group_id` is NULL");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}
			
			$affected = self::$_dbh->exec("UPDATE `light` SET
						`".$sensor."_frequency_id`=$id,
						`".$sensor."_frequency_level`=$level,
						`is_self_".$sensor."_frequency`=0 WHERE `group_id` in 
							(SELECT `id` FROM `group` WHERE `district_id`=$id AND `is_self_".$sensor."_frequency`=0) 
						AND `is_self_".$sensor."_frequency`=0");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}
			return $this->updateEEPROMFrequency($level, $id, $frequency, $type);
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
			return false;
		}
		return true;
	}

	protected function setSamplingFrequencyInGroup($level, $id, $frequency, $type) {
		try {
			if (!array_key_exists($type, $this->_sensorConf)) {
				return false;
			}
			$sensor = $this->_sensorConf[$type];
			$affected = self::$_dbh->exec("UPDATE `group` SET 
						`".$sensor."_frequency`=$frequency,
						`control_type`='manual',
						`".$sensor."_frequency_id`=$id,
						`".$sensor."_frequency_level`=$level,
						`is_self_".$sensor."_frequency`=1 WHERE id=$id");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}
			// 对下级各层未设置计划的节点设置有效计划
			$affected = self::$_dbh->exec("UPDATE `light` SET
						`".$sensor."_frequency_id`=$id,
						`".$sensor."_frequency_level`=$level,
						`is_self_".$sensor."_frequency`=0 WHERE `group_id`=$id");
			if ($affected === false) {
				$err = $conn->errorInfo();
				SeasLog::error($err[2]);
				return false;
			}

			return $this->updateEEPROMFrequency($level, $id, $frequency, $type);
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
			return false;
		}
		return true;
	}

	protected function getAllLightInCountry() {
		try {
			$rs = self::$_dbh->query("SELECT `id` FROM `light` WHERE district_id in (
				SELECT `id` FROM `district` WHERE `city_id` in (
							SELECT `id` FROM `city` WHERE `province_id` in (
								SELECT `id` FROM `province`)))");
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rs = $rs->fetchAll();
			return $rs;
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
		}
		return false;
	}

	protected function getAllLightInProvince($id) {
		try {
			$rs = self::$_dbh->query("SELECT id FROM `light` WHERE district_id in (
				SELECT id FROM `district` WHERE `city_id` in (
							SELECT id FROM `city` WHERE province_id=$id))");
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rs = $rs->fetchAll();
			return $rs;
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
		}
		return false;
	}

	protected function getAllLightInCity($id) {
		try {
			$rs = self::$_dbh->query("SELECT id FROM `light` WHERE district_id in (
				SELECT id FROM `district` WHERE city_id=$id)");
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rs = $rs->fetchAll();
			return $rs;
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
		}
		return false;
	}

	protected function getAllLightInDistrict($id) {
		try {
			$rs = self::$_dbh->query("SELECT id FROM `light` WHERE district_id=$id");
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rs = $rs->fetchAll();
			return $rs;
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
		}
		return false;
	}

	protected function getAllLightInGroup($id) {
		try {
			$rs = self::$_dbh->query("SELECT id FROM `light` WHERE group_id=$id");
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rs = $rs->fetchAll();
			return $rs;
		} catch(PDOException $e) {
			SeasLog::error($e->getMessage());
		}
		return false;
	}

	//protected $_provinceTable = null;
	//protected $_cityTable = null;
	//protected $_districtTable = null;
	//protected $_groupTable = null;
	//protected $_lightTable = null;
	protected $_levelConf = null;
	protected $_sensorConf = null;
	protected $_sensorEEPROM = null;
}
