<?php
/**
 * ClickBlocks.PHP v. 1.0
 *
 * Copyright (C) 2010  SARITASA LLC
 * http://www.saritasa.com
 *
 * This framework is free software. You can redistribute it and/or modify
 * it under the terms of either the current ClickBlocks.PHP License
 * viewable at theclickblocks.com) or the License that was distributed with
 * this file.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY, without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the ClickBlocks.PHP License
 * along with this program.
 *
 * Responsibility of this file: config.php
 *
 * @category   Core
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */

namespace ClickBlocks\Core;

/**
 * This class is designed for storing global configuration parameters of an application and the framework.
 *
 * Этот класс предназначен для хранения глобальных конфигурационных параметров приложения и фрэймворка.
 *
 * @category  Core
 * @package   Core
 * @copyright 2007-2010 SARITASA LLC <info@saritasa.com>
 * @version   Release: 1.0.0
 */
class Config implements \ArrayAccess
{
   /**
    * Constructor of this class.
    *
    * Конструктор класса.
    *
    * @access public
    */
   public function __construct()
   {
      $this->root = $_SERVER['DOCUMENT_ROOT'];
   }

   public function offsetSet($property, $value)
   {
      $this->{$property} = $value;
   }

   public function offsetGet($property)
   {
      if (isset($this->{$property})) return $this->{$property};
      if (is_null($property)) throw new \Exception(err_msg('ERR_GENERAL_8', array(get_class($this))));
      throw new \Exception(err_msg('ERR_GENERAL_3', array($property, get_class($this))));
   }

   public function offsetExists($property)
   {
      return isset($this->{$property});
   }

   public function offsetUnset($property)
   {
      unset($this->{$property});
   }

   /**
    * Loads configuration parameters from a configuration file.
    *
    * Загружает конфигурационные параметры из файла конфигурации.
    *
    * @param string $iniFile - full path to an configuration file in php-ini format.
    * @access public
    * @return boolean returns TRUE if ini file is existed and was parsed correctly and FALSE if the file is not exist.
    */
   public function init($iniFile)
   {
      if (!is_file($iniFile)) return false;
      $data = parse_ini_file($iniFile, true);
      if ($data === false) throw new \Exception(err_msg('ERR_CONFIG_1', array($iniFile)));
      foreach ($data as $section => $properties)
      {
         if ($section != 'general' && is_array($properties)) foreach ($properties as $k => $v) $this->{$section}[$k] = $v;
         elseif ($section == 'general') foreach ($properties as $k => $v) $this->{$k} = $v;
         else $this->{$section} = $properties;
      }
      $root = explode('/',$this->root);
      if($this->hostname) return true;
      elseif($_SERVER['SERVER_NAME'])$this->hostname = $_SERVER['SERVER_NAME'];
      else 
      {
        while(in_array(end($root),array('development','html'))) unset($root[count($root)-1]);
        $this->hostname = end($root);
      }
      return true;
   }
}

?>
