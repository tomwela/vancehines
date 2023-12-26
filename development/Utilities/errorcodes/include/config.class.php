<?php
/**
 * Config Class
 * @version 0.1
 * @author Vinh, Vo Huynh Vinh <vinh@saritasa.com>
 * @link 
 * @copyright Copyright 2012 Saritasa LLC - http://www.saritasa.com/
 * @license Copyleft
 * @package
 */

interface iConfig {
	public function getValue($name, $group);
}

class ConfigINI implements iConfig {
	private $_config;

	public function __construct($file) {
		$this->_config = parse_ini_file($file, true, INI_SCANNER_RAW);
	}

	public function getValue($name, $group = '') {
		if ($group != '') {
			return $this->_config[$group][$name];
		}
		
		return $this->_config[$name];
	}
}