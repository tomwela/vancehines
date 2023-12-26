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
 * Responsibility of this file: register.php 
 * 
 * @category   Core
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0        
 */

namespace ClickBlocks\Core;

/**
 * This class is designed for storing of global objects.
 * 
 * Этот класс предназначен для хранения глобальных объектов.
 *
 * @category  Core
 * @package   Core
 * @copyright 2007-2010 SARITASA LLC <info@saritasa.com>
 * @version   Release: 1.0.0
 * @final
 */
final class Register
{        
   /**
    * The instance of this class.
    * 
    * Экземпляр класса.
    * 
    * @var object $instance
    * @access private
    */           
   private static $instance = null;
   
   /**
    * Constructor of this class.
    * 
    * Конструктор класса.
    * 
    * @access private
    */
   private function __construct(){}
   
   /**
    * Clones an object of this class. The private method '__clone' doesn't allow to clone an instance of the class.
    * 
    * Клонирует объект данного класса. При этом скрытый метод __clone не позволяет клонировать объект.
    * 
    * @access private
    */
   private function __clone(){}
   
   /**
    * Returns an instance of this class.
    * 
    * Возвращает экземпляр этого класса. 
    * 
    * @return object
    * @access public
    * @static
    */
   public static function getInstance()
   {
      if (self::$instance === null) self::$instance = new self();
      return self::$instance;
   }
}

?>
