<?php

// FOR PHP <= 5.2, comment out following 2 lines:
namespace ClickBlocks\API;
use \Exception, \LogicException, \ErrorException;
// AND UNCOMMENT this:
//define('__NAMESPACE__','');
// PHP >= 5.0 compatible code starts here

require_once 'apicontrollerbase.php';

class APIException extends LogicException {}

interface IAPIBase {
  function __construct();
  function __destruct();
  function execute();
}


/**
 * Basic request-response API
 * Features:
 * - assured formalized output
 * - error handling, outputting & logging
 * - execute one or multiple requests using separate controller class
 * - highly extensible
 *
 * @author Killian
 * 
 * @todo rethink ERROR logging
 */
abstract class APIBase implements IAPIBase 
{
   const STATUS_ERROR = 500;
   const STATUS_OK = 0;

   /**
    * @var mixed Logical parameters passed to API request. Usually array.
    */
   protected $params = NULL;
   
   /**
    * @var int Logical status code of request, set after execution
    */
   protected $statusCode = 0;
   
   /**
    * @var array list of error descriptions. Used to check if request was successfull or not.
    */
   protected $errors = array();
   
   /**
    * @var Exception caught during execution
    */
   protected $exception;
   
   /**
    * @var boolean set to true when fatal error occured.
    */
   private $isFatalError = false;
   
   /**
    * @var mixed Logical return value of request, may be any scalar type or array with any structure
    */
   public $return = array();
   
   /**
    * @var int status code for HTTP response. only valid and common statuses allowed.
    */
   protected $HTTPStatusCode = 200;
   
   /**
    * last executed controller
    * @var APIControllerBase
    */
   protected $controller;

   private static $_instance;
  
   /**
    * Constructor takes all error handling
    */
   public final function __construct() 
   {
      if (self::$_instance) {
        throw new Exception("API already instantiated.");
      }
      self::$_instance = $this;
      ini_set('display_errors', 1);
      ini_set('html_errors', 0);
      error_reporting(E_ALL | E_STRICT);
      restore_error_handler();
      //$out = ob_get_contents(); 
      while (ob_get_level()>1) ob_end_clean();
      ob_start(array($this, 'errorFatalFlush'));
      set_exception_handler(array($this, 'exceptionHandler'));
      set_error_handler(array($this, 'errorHandler'), error_reporting());
   }
  
   public final function execute() 
   {
      try
      {
        $this->init();
        $this->processRequest();
        $this->finalize();
      } catch (Exception $e) {
        $this->setStatusFromException($e);
      }
      return $this;
   }

   /**
    * Executes single logical request
    * @throws Exception if getRequestInfo() provided incorrect array
    * @throws APIException if some request parameters were incorrect
    */
   protected function processRequest()
   {
      // this trick is needed to handle exit and die properly as failed request
      $this->errors = array('Something went wrong! (controller was not completely executed)');
      $exc = null;
      try {
         $info = $this->getRequestInfo();
         if (!$info['class'] || !$info['method'] || !is_array($info['params'])) {
            throw new Exception('Invalid Request Info: '); 
         }
         $class = $info['class'];
         $method = strtolower($info['method']);
         $this->params = $info['params'];
         $this->validateMethodName($method);
         if (!class_exists($class) || !is_subclass_of($class, $this->getControllerBaseClassName())) {
           $cls = explode('\\', $class);
           throw new APIException("Controller class '".end($cls)."' not exists.");
         }
         if (!@method_exists($class, $method)) {
           throw new APIException("Method '$method' not exists.");
         }
         $this->controller = $controller = new $class();
         $controller->params = $info['params'];
         $controller->init_($method);
         $this->return = $controller->$method($controller->params);
         if ($this->return !== NULL) {
           $controller->return = $this->return;
         }
         $controller->finalize_($method);
         $this->return = $controller->return;
      } catch (Exception $e) {$exc = $e;}
      $this->errors = array(); // everything went fine
      if ($exc instanceof Exception) $this->setStatusFromException($exc);
   }

   protected final function setStatusFromException(Exception $e) 
   {
     $this->exception = $e;
     try {
       throw $e;
     } catch (LogicException $e) {
       $this->errors[] = $e->getMessage();
       $this->statusCode = $e->getCode();
     } catch (APIException $e) {
       $this->errors[] = 'API Usage error: '.$e->getMessage();
       $this->statusCode = self::STATUS_ERROR;
     } catch (Exception $e) {
       if ($e instanceof ErrorException) {
         $error_types = array(E_NOTICE=>'E_NOTICE', E_WARNING=>'E_WARNING',E_ERROR=>'E_ERROR', E_STRICT=>'E_STRICT', @E_DEPRECATED=>'E_DEPRECATED', 
             E_USER_NOTICE=>'E_USER_NOTICE', E_USER_WARNING=>'E_USER_WARNING', E_USER_ERROR=>'E_USER_ERROR');
         $type = isset($error_types[$e->getSeverity()]) ? $error_types[$e->getSeverity()] : 'UNKNOWN';
       } else 
         $type = 'Exception';
       $this->errors[] = $type.': "'.$e->getMessage().'" in "'.$e->getFile().'" on line #'.$e->getLine();
       $this->statusCode = self::STATUS_ERROR;
     }
   }
  
   protected final function setErrorLevel($lv)
   {
     error_reporting($lv);
     set_error_handler(array($this, 'errorHandler'), $lv);
   }

   /**
    * Custom initialization code
    */
   protected function init()  {  }

   /**
    * Custom finalization code
    */
   protected function finalize()  {  }

   /**
    * This value is used when checking that class returned by getRequestControllerClassName() is compatible with current API class 
    * (i.e. descendant from this class)
    * override this every time in your API class when you add new public methods & properties in your Controller class to be used in your API class
    * @return string full class name
    */
   protected function getControllerBaseClassName() {
     return (@__NAMESPACE__ ? __NAMESPACE__.'\\' : '').'APIControllerBase';
   }

   /**
    * Prepare all information about request in this method (take it from request URI, POST data, etc.)<br/>
    * Return array contain following elements:<br/>
    * <b>class</b> - Class name of Controller to use. Should be subclass of APIControllerBase<br/>
    * <b>method</b> - Method name within Controller to execute. This public method will recieve request parameters array in first argument.<br/>
    * <b>params</b> - Associative array of request parameters.<br/>
    * @return array .
    */
   abstract public function getRequestInfo();

   /**
    * Override this to implement additional validation on methods name
    * NOTE: always call parent method to prevent calls like "__destruct"
    * @param type $method  method name as returned by getRequestInfo()['method']
    * @throws APIException with error message describing what's wrong with method name
    */
   protected function validateMethodName($method) {
     if ($method[0]=='_' || $method[strlen($method)-1]=='_')
       throw new APIException("Method name starting or ending with '_' not allowed.");
   }

   /**
    * @return bool if true, then afterFlush() will be executed & errors will be catched
    */
   protected function doAfterFlush() {
     return false;
   }

   protected function afterFlush() { }

   public function isError() {
     return (bool)count($this->errors);
   }
   
   public final function isFatalError() {
     return $this->isFatalError;
   }

   public function getErrors() {
     return $this->errors;
   }

   protected function isFormalOutput() {
     return true;
   }

   private function flushFormal() 
   {
     $output = $this->formatResult($this->getResult());
     while (ob_get_level()>1) {
       ob_end_clean(); // destroy ALL output buffers
     }
     header('Content-Encoding:');
     header('Vary:');
     ob_end_clean(); // level 0
     ob_start(); // return to level 1 - default
     header (self::getHTTPStatusByCode($this->HTTPStatusCode)); 
     header ('Connection: close'); 
     header ('Content-Length: '.strlen($output)); // to severe connection with client immediately after flush
     header ('Content-Type: '.$this->getContentType());
     //header('Pragma: no-cache');
     header('Cache-Control: no-cache, must-revalidate'); // disable caching
     echo $output;
     ob_end_flush();
   }

   public final function __destruct() 
   {
     $d=debug_backtrace();
     $d=end($d);
     if (isset($d['file'])) {
       throw new Exception("Cannot destruct ".__CLASS__.' manually!');
     }
     if ($this->isFormalOutput()) { 
       $this->flushFormal();  // hard override of all previous output buffering
     }
     flush();
     if (session_id()) session_write_close();
     $this->afterFlush();
   }

   public final function errorHandler($ercode, $erstr, $erfile, $erline) 
   {
     if (error_reporting() & $ercode) {
       throw new ErrorException($erstr, 0, $ercode, $erfile, $erline);
       //$this->errors[] = 'wtf';
     } else
       return FALSE;
   }

   public final function exceptionHandler(Exception $e) 
   {
     $this->errors[] = 'Uncought exception: "'.$e->getMessage().'" in file '.$e->getFile().' on line '.$e->getLine();
   }

   /**
    * ob handler to catch fatal error
    * in case of error flushes formalized response
    * use in ob_start
    * @uses getContentType()
    * @uses getResultString()
    */
   public final function errorFatalFlush($html) 
   {
      if (preg_match('/(Fatal|Parse) error:(.*) in (.*) on line (\d+)/', $html, $res)) {
        $this->isFatalError = true;
        $this->errors[] = $res[1].' error: '.$res[2].' in '.$res[3].' on line '.$res[4];
        header('Content-Encoding:');
        header('Vary:');
        header ('Content-Type: '.$this->getContentType());
        return $this->formatResult($this->getResult());
      } else
        return $html;
   }
  
  /**
   * Returns HTTP Status string by code (eg. "HTTP/1.1 200 OK"). Only common codes supported. If code not found, defaults to 200.
   * @param int $code
   * @return string
   */
   public static function getHTTPStatusByCode($code)
   {
      $http = array(
         100 => 'Continue',
         101 => 'Switching Protocols',
         200 => 'OK',
         201 => 'Created',
         202 => 'Accepted',
         203 => 'Non-Authoritative Information',
         204 => 'No Content',
         205 => 'Reset Content',
         206 => 'Partial Content',
         300 => 'Multiple Choices',
         301 => 'Moved Permanently',
         302 => 'Found',
         303 => 'See Other',
         304 => 'Not Modified',
         305 => 'Use Proxy',
         307 => 'Temporary Redirect',
         400 => 'Bad Request',
         401 => 'Unauthorized',
         402 => 'Payment Required',
         403 => 'Forbidden',
         404 => 'Not Found',
         405 => 'Method Not Allowed',
         406 => 'Not Acceptable',
         407 => 'Proxy Authentication Required',
         408 => 'Request Time-out',
         409 => 'Conflict',
         410 => 'Gone',
         411 => 'Length Required',
         412 => 'Precondition Failed',
         413 => 'Request Entity Too Large',
         414 => 'Request-URI Too Large',
         415 => 'Unsupported Media Type',
         416 => 'Requested Range Not Satisfiable',
         417 => 'Expectation Failed',
         500 => 'Internal Server Error',
         501 => 'Not Implemented',
         502 => 'Bad Gateway',
         503 => 'Service Unavailable',
         504 => 'Gateway Time-out',
         505 => 'HTTP Version Not Supported',
      );
      if (!isset($http[$code])) $code = 200;
      return 'HTTP/1.1 '.$code.' '.$http[$code];
   }

  abstract protected function getResult();

  /**
   * @param string $result  returned by getResult()
   * @return string return, formatted accordingly (json, xml or whatever)
   */
  abstract protected function formatResult($result);
  
  abstract protected function getContentType();
}


?>
