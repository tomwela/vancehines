<?php

namespace ClickBlocks\Utils;

class Requirements
{
   private static $instance = null;
   private $phpBinary = NULL;
   private $report = null;
   private $config = null;

   private function __construct()
   {
      set_time_limit(0);
      ini_set('display_errors', 1);
      error_reporting(E_ALL & ~E_NOTICE);
      set_error_handler(array('\ClickBlocks\Utils\Requirements', 'errorHandler'), E_ALL & ~E_NOTICE);
      set_exception_handler(array('\ClickBlocks\Utils\Requirements', 'exceptionHandler'));
   }

   private function __clone(){}

   public static function getInstance()
   {
      if (self::$instance === null) self::$instance = new self();
      return self::$instance;
   }

   public static function errorHandler($errno, $errstr, $errfile, $errline)
   {
      throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
   }

   public static function exceptionHandler(\Exception $ex)
   {
      $req = Requirements::getInstance();
      $req->addError($ex->getMessage() . '<br />File: ' . $ex->getFile() . '<br />Line: ' . $ex->getLine(), 'refer to the developers by the framework.');
      $req->printReport();
   }

   public function check()
   {
      if (!$this->checkSystem()) return;
      if (!$this->checkBasicDirectories()) return;
      if (!$this->checkCache()) return;
      if (!$this->checkDB()) return;
      $this->checkConnect();
   }

   public function getReport()
   {
      return $this->report;
   }

   public function printReport($show = true)
   {
      if (count($this->report) == 0) $html = 'All requirements are met.';
      else
      {
         $html .= '<ul>';
         foreach ($this->report as $n => $info)
         {
            $html .= '<li><b>' . ($n + 1) . '</b> <span style="color:#BC2E2E;">' . $info['error'] . '</span> <span style="color:#3650C5">(<b>Recomendation:</b> ' . $info['suggestion'] . ')</span></li>';
         }
         $html .= '</ul>';
      }
      if ($show) echo $html;
      else return $html;
   }

   private function checkConnect()
   {
      $path = $this->config->root . $this->config->dirs['framework'] ;
      $files = array($this->config->root . '/Application/connect.php',
                     $path . '/core/errors.php',
                     $path . '/core/register.php',
                     $path . '/core/io.php',
                     $path . '/core/loader.php',
                     $path . '/core/logexception.php',
                     $path . '/core/debugger.php');
      foreach ($files as $file)
      {
         if (!is_file($file))
         {
            $this->addError('File "' . $file . '" is not found.', 'refer to the developers by the framework.');
            return false;
         }
         if (!$this->checkSyntax($file, $output))
         {
            $this->addError($output, 'refer to the developers by the framework.');
            return false;
         }
      }
      session_start();
      if (!isset($_SESSION)) $this->addError('Session is not working.', 'refer to an admin of your server.');
      if (!extension_loaded('zlib')) $this->addError('zlib extension is not loaded.', 'refer to an admin of your server or check appropriate settings in the php.ini');
      else if (ini_get('zlib.output_compression') == 1) $this->addError('You cannot use both ob_gzhandler() and zlib.output_compression.', 'change appropriate settings in the php.ini file.');
   }

   private function checkDB()
   {
      foreach ($this->config as $alias => $data)
      {
         if (substr($alias, 0, 2) != 'db') continue;
         if (!extension_loaded('PDO'))
         {
            $this->addError('PDO extension is not loaded.', 'check extensions\' section in the php.ini');
            return false;
         }
         try
         {
            new \PDO($data['dsn'], $data['dbuser'], $data['dbpass']);
         }
         catch (\Exception $ex)
         {
            $this->addError('Cannot connect to DB "' . $alias . '" (' . $ex->getMessage() . ').', 'refer to an admin of your server or check "dsn" parameter of "' . $alias . '" section in the configuration file.');
         }
      }
      return true;
   }

   private function checkCache()
   {
      $file = $this->config->root . $this->config->dirs['framework'] . '/cache/cache.php';
      if (!is_file($file))
      {
         $this->addError('File "' . $file . '" is not found.', 'refer to the developers by the framework.');
         return false;
      }
      if (!$this->checkSyntax($file, $output))
      {
         $this->addError($output, 'refer to the developers by the framework.');
         return false;
      }
      require_once($file);
      switch ($this->config->cache['type'])
      {
         case 'Memory':
           $file = 'memory.php';
           break;
         case 'File':
           $file = 'file.php';
           break;
         case 'Alternative':
           $file = 'alternative.php';
           break;
         case 'EAccelerator':
           $file = 'eaccelerator.php';
           break;
         case 'X':
           $file = 'xcache.php';
           break;
         case 'Default':
           $file = 'default.php';
           break;
         default:
           $this->addError('Section "cache" is not specified.', 'you should specify the "cache" section in the configuration file.');
           return false;
      }
      $file = $this->config->root . $this->config->dirs['framework'] . '/cache/' . $file;
      if (!is_file($file))
      {
         $this->addError('File "' . $file . '" is not found.', 'refer to the developers by the framework.');
         return false;
      }
      if (!$this->checkSyntax($file, $output))
      {
         $this->addError($output, 'refer to the developers by the framework.');
         return false;
      }
      require_once($file);
      $cache = '\ClickBlocks\Cache\Cache' . $this->config->cache['type'];
      if (class_exists($cache, false))
      {
         if (call_user_func($cache . '::isAvailable'))
         {
            switch ($config->cache['type'])
            {
               case 'Memory':
                 if (!isset($config->cache['params']['host'])) $this->addError('"host" parameter for Memory cache is not defined.', 'set a valid value for the "host" parameter in the configuration file.');
                 else if (!isset($config->cache['params']['port'])) $this->addError('"port" parameter for Memory cache is not defined.', 'set a valid value for the "port" parameter in the configuration file.');
                 else
                 {
                    try
                    {
                       $cache = new $cache($config->cache['params']['host'], $config->cache['params']['port'], $config->cache['params']['compress']);
                    }
                    catch (\Exception $ex)
                    {
                       $this->addError('Memcache for host "' . $config->cache['params']['host'] . '" is not available.', 'refer to an system admin of your server for a consultation.');
                    }
                 }
                 break;
            }
         }
         else $this->addError('Cache "' . $config->cache['type'] . '" is not available.', 'adjust appropriate parameters of this cache in the php.ini');
      }
      else $this->addError('Class "' . $cache . '" not found.', 'refer to the developers by the framework.');
      return true;
   }

   private function checkBasicDirectories()
   {
      $file = __DIR__ . '/../Framework/_engine/core/config.php';
      if (!is_file($file))
      {
         $this->addError('File "' . $file . '" is not found.', 'refer to the developers by the framework.');
         return false;
      }
      if (!$this->checkSyntax($file, $output))
      {
         $this->addError($output, 'refer to the developers by the framework.');
         return false;
      }
      require_once($file);
      $config = new \ClickBlocks\Core\Config();
      $config->init($config->root . '/Application/_config/config.ini');
      $config->init($config->root . '/Application/_config/.local.ini');
      $this->config = $config;
      if (!isset($config->debugPage)) $this->addError('Configuration parameter "debugPage" is not defined.', 'set a value of this parameter in the "/Application/_config/config.ini" or "/Application/_config/.local.ini"');
      else if (!is_file($config->root . $config->debugPage)) $this->addError('Parameter "debugPage" is wrong. No such file.', 'change this parameter so that it refers on a valid debug page template (by default it is "/Framework/_templates/debug/bug.tpl")');
      if (!isset($config->bugPage)) $this->addError('Configuration parameter "bugPage" is not defined.', 'set a value of this parameter in the "/Application/_config/config.ini" or "/Application/_config/.local.ini"');
      else if (!is_file($config->root . $config->bugPage)) $this->addError('Parameter "bugPage" is wrong. No such file.', 'change this parameter so that it refers on a valid bug page template (by default it is "/Framework/_templates/debug/msg.html")');
      $dirs = array('framework', 'application', 'engine', 'plugins', 'cache', 'temp', 'log');
      foreach ($dirs as $dir)
      {
         if ($dir == 'log' && !$config->isLog) continue;
         if (!isset($config->dirs[$dir])) $this->addError('Configuration parameter "' . $dir . '" from "dirs" section is not defined.', 'you should set a value of this parameters in your configuration file.');
         else if (!is_dir($config->root . $config->dirs[$dir])) $this->addError('Parameter "' . $dir . '" from "dirs" section is wrong. No such directory.', 'you should set the correct value of this parameter in your configuration file.');
         else if (in_array($dir, array('log', 'temp', 'cache', 'engine')))
         {
            try
            {
               $file = $config->root . $config->dirs[$dir] . '/test.txt';
               file_put_contents($file, 'test');
               unlink($file);
            }
            catch (\Exception $ex)
            {
               $this->addError('It\'s not possible to write to the "' . $dir . '" directory (' . $ex->getMessage() . ').', 'set permissions to write for this directory.');
            }
         }
      }
      return true;
   }

   private function checksystem()
   {
      if (ini_get('date.timezone') == '') $this->addError('Default time zone is not defined.', 'you should set a value of "date.timezone" parameter in the php.ini file.');
      if (ini_get('short_open_tag') == 0) $this->addError('Short tags are not available.', 'you need to set a value of "short_open_tag" parameter in the php.ini file to "On".');
      if (ini_get('allow_call_time_pass_reference') == 1) $this->addError('Passing values by reference is enabled', 'you need to set "allow_call_time_pass_reference" parameter in the php.ini file to "Off".');
      return true;
   }

   private function checkSyntax($filename, &$output = null)
   {
      $command = $this->getPhpBinary();
      if (DIRECTORY_SEPARATOR == '\\') $command = escapeshellarg($command);
      $command .= ' -l ' . escapeshellarg($filename);
      exec($command, $output);
      $output = $output[1];
      return (strpos($output, 'error') === false);
   }

   private function getPhpBinary()
   {
      if (is_null($this->phpBinary))
      {
         if (is_readable('@php_bin@')) $this->phpBinary = '@php_bin@';
         else if (PHP_SAPI == 'cli' && isset($_SERVER['_']))
         {
            $file = file($_SERVER['_']);
            $tmp = explode(' ', $file[0]);
            $this->phpBinary = trim($tmp[1]);
         }
         if (!is_readable($this->phpBinary)) $this->phpBinary = 'php';
         else
         {
            $this->phpBinary = escapeshellarg($this->phpBinary);
         }
      }
      return $this->phpBinary;
   }

   private function addError($error, $suggestion)
   {
      $this->report[] = array('error' => $error, 'suggestion' => $suggestion);
      return $this;
   }
}

if (strpos($_SERVER['REQUEST_URI'], 'requirements.php') !== false)
{
   $req = Requirements::getInstance();
   $req->check();
   $req->printReport();
}

?>