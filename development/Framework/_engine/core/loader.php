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
 * Responsibility of this file: loader.php
 *
 * @category   Core
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */

namespace ClickBlocks\Core;

/**
 * Connects to the extent necessary files to the appropriate classes.
 *
 * Подключает по мере необходимости файлы с нужными классами.
 *
 * @category  Core
 * @package   Core
 * @copyright 2007-2010 SARITASA LLC <info@saritasa.com>
 * @version   Release: 1.0.0
 */
class Loader
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
    * The unique key for cache containing paths to files with classes.
    *
    * Уникальный ключ для кэша, содержащего пути к файлам с классами.
    *
    * @var string $cacheKeyAutoload
    * @access protected
    */
   protected $cacheKeyAutoload = null;

   /**
    * The array with paths to classes.
    *
    * Массив с путями к классам.
    *
    * @var array $classes
    * @access protected
    */
   protected $classes = null;

   /**
    * The instance of Register class.
    *
    * Экземпляр класса Register.
    *
    * @var object $reg
    * @access protected
    */
   protected $reg = null;

   /**
    * The array of included files.
    *
    * Массив уже подключённых файлов.
    *
    * @var array $includedFiles
    * @access protected
    */
   protected $includedFiles = array();

   /**
    * Time caching of content include files.
    *
    * Время кэширования содержимого подключаемых файлов.
    *
    * @var integer $includeExpire
    * @access public
    */
   public $includeExpire = 0;

   /**
    * Time caching of paths to files with classes.
    *
    * Время кэширования путей к файлам с классами.
    *
    * @var integer $autoloadExpire
    * @access public
    */
   public $autoloadExpire = 2592000;  // a month

   /**
    * Regular expression for php-files searching.
    *
    * Регулярное выражение для поиска php-файлов.
    *
    * @var string $phpFileName
    * @access public
    */
   public $phpFileName = '/\.php$/i';

   /**
    * Clones an object of this class. The private method '__clone' doesn't allow to clone an instance of the class.
    *
    * Клонирует объект данного класса. При этом скрытый метод __clone не позволяет клонировать объект.
    *
    * @access private
    */
   private function __clone(){}

   /**
    * Constructor of this class.
    *
    * Конструктор класса.
    *
    * @access private
    */
   private function __construct()
   {
      $this->reg = Register::getInstance();
      $this->cacheKeyAutoload = 'cache_key_autoload_' . md5($this->reg->config->root);
      $this->classes = $this->reg->cache->get($this->cacheKeyAutoload);
      if (!is_array($this->classes)) $this->classes = array();
      if (count($this->classes) == 0) $this->fillCache();
      spl_autoload_register(array($this, 'autoLoadClass'));
   }

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

   /**
    * Returns the array of file paths with classes.
    *
    * Возвращает массив путей к файлам с классами.
    *
    * @return array
    * @acces public
    */
   public function getClasses()
   {
      return $this->classes;
   }

   /**
    * Replaces the array of file paths with classes with given array.
    *
    * Заменяет массив путей к файлам с классами заданным.
    *
    * @param array $classes
    * @access public
    */
   public function setClasses(array $classes)
   {
      $this->classes = $classes;
      $this->reg->cache->set($this->cacheKeyAutoload, $this->classes, $this->autoloadExpire);
      return $this;
   }

   /**
    * Cleans classes' cache.
    *
    * Чистит кэш классов.
    *
    * @access public
    */
   public function cleanCache()
   {
      $this->reg->cache->set($this->cacheKeyAutoload, array(), $this->autoloadExpire);
      $this->classes = array();
      return $this;
   }

   /**
    * Fills cache with information about file paths to classes.
    *
    * Заполняет кэш информацией о путях к файлам с классами.
    *
    * @param string $path
    * @access public
    */
   public function fillCache($path = null)
   {
      $path = (!strlen($path)) ? $this->reg->config->root : $path;
      $dir = opendir($path);
      while (($item = readdir($dir)) !== false)
      {
         if ($item != '.' && $item != '..' && $item != '.svn')
         {
            $file = $path . '/' . $item;
            if (is_file($file) && preg_match($this->phpFileName, $item))
            {
               $tokens = token_get_all('<?php ' . file_get_contents($file) . ' ?>');
               $namespace = null;
               foreach ($tokens as $n => $token)
               {
                  if ($token[0] == T_NAMESPACE) $namespace = $this->getNamespace($tokens, $n) . '\\';
                  else if ($token[0] == T_CLASS || $token[0] == T_INTERFACE)
                  {
                     $classes = $this->getClasses();
                     $classes[strtolower('\\' . $namespace . $this->getClassName($tokens, $n))] = $file;
                     $this->setClasses($classes);
                  }
               }
            }
            else if (is_dir($file)) $this->fillCache($file, $mask);
         }
      }
      closedir($dir);
      return $this;
   }

   /**
    * Loads new class.
    *
    * Подгружает новый класс.
    *
    * @param string $class - class name (with namespace)
    * @param string $path  - path to a directory which will begin search for a file with the class.
    * @return boolean      - returns TRUE if such a file exists and FALSE otherwise.
    * @access public
    */
   public function loadClass($class, $path = null)
   {
      return $this->autoLoadClass($class, $path, false);
   }

   /**
    * Loads new file.
    *
    * Подключает новый файл.
    *
    * @param string $file - full path to a file.
    * @param array $Vars  - template variables.
    * @access public
    */
   public function load($file, array $vars = null)
   {
      if ($vars !== null) extract($vars);
      if (!is_file($file))
      {
         eval(" ?>$file<?php ");
         return $this;
      }
      if ($this->includeExpire == 0) require_once($file);
      else
      {
         if ($this->reg->cache->isExpired($file)) $content = $this->reg->cache->get($file);
         else
         {
            $content = file_get_contents($file);
            $this->reg->cache->set($file, $content, $this->includeExpire);
         }
         eval(" ?>$content<?php ");
      }
      return $this;
   }

   /**
    * Allows to download a file.
    *
    * Позволяет выдавать файл на скачку.
    *
    * @param string $file
    * @access public
    */
   public function download($file)
   {
      if (!file_exists($file)) return false;
      // required for IE, otherwise Content-disposition is ignored
      if (ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off');
      header('Pragma: public'); // required
      header('Expires: 0');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Cache-Control: private', false); // required for certain browsers
      header('Content-Type: ' . mime_content_type($file));
      header('Content-Disposition: attachment; filename="' . basename($file) . '";');
      header('Content-Transfer-Encoding: binary');
      header('Content-Length: ' . filesize($file));
      readfile($file);
      exit;
   }

   /**
    * Provides posibility of autoloading file with some class.
    *
    * Предоставляет возможность автозагрузки файла с классом.
    *
    * @param string $class
    * @param string $path    - path to a directory which will begin search for a file with the class.
    * @param boolean $isAuto - equals TRUE if given method is invoked as __autoload magick function and FALSE otherwise.
    * @return boolean        - returns TRUE if such a file with given class exists and FALSE otherwise.
    * @access protected
    */
   protected function autoLoadClass($class, $path = null, $isAuto = true)
   {
      $class = strtolower($class);
      if ($class[0] != '\\') $class = '\\' . $class;
      if ($this->autoloadExpire > 0)
      {
         if (isset($this->classes[$class]) && file_exists($this->classes[$class]))
         {
            $this->load($this->classes[$class]);
            $this->includedFiles[$this->classes[$class]] = true;
            if (class_exists($class, false) || interface_exists($class, false)) return true;
         }
      }
      $file = $this->searchFileByClass($class, ($path) ?: $this->reg->config->root);
      if ($file === false)
      {
         if ($isAuto) Debugger::exceptionHandler(new \Exception(err_msg('ERR_GENERAL_1', array($class))));
         else return false;
      }
      if ($this->autoloadExpire > 0)
      {
         $this->classes[$class] = $file;
         $this->setClasses($this->classes);
      }
      return true;
   }

   /**
    * Searches a file with specified class.
    *
    * Ищет файл с определённым классом.
    *
    * @param string $class - class name with namespace.
    * @param string $path  - full path to a directory wich will begin file searching.
    * @return boolean      - returns TRUE if desired class exists and FALSE otherwise.
    * @access private
    */
   private function searchFileByClass($class, $path)
   {
      $dir = opendir($path);
      while (($item = readdir($dir)) !== false)
      {
         if ($item != '.' && $item != '..' && $item != '.svn')
         {
            $file = $path . '/' . $item;
            if (is_file($file) && !isset($this->includedFiles[$file]) && preg_match($this->phpFileName, $item))
            {
               $tokens = token_get_all(file_get_contents($file) );
               $flag = false; $ns = null;
               foreach ($tokens as $n => $token)
               {
                  if ($token[0] == T_NAMESPACE) $ns = $this->getNamespace($tokens, $n) . '\\';
                  else if ($token[0] == T_CLASS || $token[0] == T_INTERFACE)
                  {
                     if (strcasecmp('\\' . $ns . $this->getClassName($tokens, $n), $class) == 0)
                     {
                        $flag = true;
                        break;
                     }
                  }
               }
               if ($flag)
               {
                  $this->load($file);
                  $this->includedFiles[$file] = true;
                  if (class_exists($class, false) || interface_exists($class, false))
                  {
                     $res = $file;
                     break;
                  }
               }
            }
            else if (is_dir($file) && ($res = $this->searchFileByClass($class, $file, $mask)) !== false) break;
         }
      }
      closedir($dir);
      return (isset($res)) ? $res : false;
   }

   /**
    * Returns namesapce from a php-file.
    *
    * Возвращает пространство имён из php-файла.
    *
    * @param array $tokens - array of tokens of a current php-file.
    * @param integer $n    - position which will begin searching a namespace.
    * @return string
    * @access private
    */
   private function getNamespace(array $tokens, $n)
   {
      do
      {
         $token = $tokens[++$n];
         if ($token[0] == T_STRING || $token[0] == T_NS_SEPARATOR) $namespace .= $token[1];
      }
      while ($token != ';');
      return $namespace;
   }

   /**
    * Returns class name from a php-file.
    *
    * Возвращает имя класса из php-файла.
    *
    * @param array $tokens - array of tokens of a current php-file.
    * @param integer $n    - position wich will begin searching a class name.
    * @return string
    * @access private
    */
   private function getClassName(array $tokens, $n)
   {
      do
      {
         $token = $tokens[++$n];
      }
      while ($token[0] != T_STRING);
      return $token[1];
   }
}

?>
