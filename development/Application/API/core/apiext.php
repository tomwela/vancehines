<?php

// FOR PHP <= 5.2, comment out following 2 lines:
namespace ClickBlocks\API;
use \Exception, \LogicException, \ErrorException;
// PHP >= 5.0 compatible code starts here

require_once 'apibatch.php';

/**
 * Extended version of API with additional features:
 * - flush (download) file or binary string, overriding formal output
 * - delayed callbacks to be called after client disconnected
 * - error log for delayed callbacks
 * - batch request support
 *
 * @author Killian
 */
abstract class APIExt extends APIBatch {
  /**
   * @var APIControllerExt
   */
  protected $controller;
  private $old_umask;
  
  protected function getControllerBaseClassName() {
    return (@__NAMESPACE__ ? __NAMESPACE__.'\\' : '').'APIControllerExt';
  }
  
  protected function init() {
    parent::init();
    $this->old_umask = umask(0);
  }

  protected function afterFlush() {
    if ($this->controller->delayedCalls && ($this->isError() == false)) {
      try {
        ini_set('html_errors', 0);
        ob_start(array($this, 'errorFatalLog')); // to catch fatal errors in delayed methods
        $this->controller->afterFlush_();
        ob_end_clean();
        umask($this->old_umask);
      } catch (Exception $e) {
        $this->setStatusFromException($e);
      }

      if ($this->isError()) {
        $mt = sprintf('%03d', strstr(microtime(),' ',true)*1000);
        file_put_contents($this->getLogFile(), '<div><b>Script executed</b>: '.date('Y-m-d H:i:s:').$mt.' (server time)<br/><b>Errors</b>: <ul><li>'.
                implode('</li><li>',$this->errors).'</li></ul><b>Status code</b>: '.(int)$this->statusCode.'</div>', FILE_APPEND);
      }
    }
  }
  
  /**
   * ob handler to catch fatal error
   * in case of error dumps error in log file in html format
   * use in ob_start
   * @uses  getLogFile()
   */
  public final function errorFatalLog($html) 
  {
     if (preg_match('/(Fatal|Parse) error:(.*) in (.*) on line (\d+)/', $html, $res)) {
       $error = $res[1].' error: '.$res[2].' in '.$res[3].' on line '.$res[4];
       file_put_contents($this->getLogFile(), '<div><b>Script executed</b>: '.date('Y-m-d H:i:s').
               ' (server time) from '.$_SERVER['REMOTE_ADDR'].'<br/><b>Fatal error</b>: <ul><li>'.$error.'</li></ul></div>'.PHP_EOL, FILE_APPEND);
       return '';
     } else
       return $html;
  }
  
  protected function isFormalOutput() {
    return (!(bool)$this->controller || (bool)$this->controller->formalOutput);
  }
}

class APIControllerExt extends APIControllerBase 
{
  public $delayedCalls = array();
  public $formalOutput = true;
  public $tempFiles = array();
  
  /*
   * Add temp file(s) to delete on finalization
   * @param string/array  $files  name of single file (with path) or list of files
   */
  protected final function addTempFiles($files) 
  {
    if (is_array($files))
      $this->tempFiles = array_merge($this->tempFiles, $files);
    elseif (is_scalar($files))
      $this->tempFiles[] = (string)$files;
  }
    
  /**
   * Add callback for delayed execution.
   * $method in current APIController will be executed after $time seconds delay, with $params
   * @param string  $method Method name in current controller to execute
   * @param mixed $params parameter for method call
   * @param int $time Delay in seconds BEFORE execution of THIS callback; 
   */
  protected function addDelayed($method, $params = array(), $time = 0)
  {
    $exist = true;
    try {
      $refl=new \ReflectionMethod($this,$method);
    } catch (\ReflectionException $e) {
      $exist = false;
    }
    if (!$exist || ($refl->isPrivate()))
      throw new \Exception(__METHOD__."(): Callback method '$method' is invalid!");
    $this->delayedCalls[] = array('method'=>$method, 'params'=>$params, 'time'=>$time);
  }

  public function afterFlush_() 
  {
    // Browser connection should be ended at this point!
    // background logic should go here (delayed methods, etc.)
    if (count($this->delayedCalls)) // run callbacks only if there are no errors
    {
        foreach ($this->delayedCalls as $call) {
          if (@$call['time'])
            sleep((int)$call['time']);
          $this->{$call['method']}($call['params']);
        }
    }
    if (count($this->tempFiles)) {
      foreach ($this->tempFiles as $file) {
        if (file_exists($file))
          unlink($file);
      }
    }
  }

  private function flushPrepare($filesize, $contentType = 'application/octet-stream', $filename = null) 
  {
    $this->formalOutput = false;
    while (ob_get_level()>0)
      ob_end_clean();
    ob_start();
    header_remove('Content-Encoding');
    header_remove('Vary');
    if (ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off');
    header('Pragma: public'); // required
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: private', false); // required for certain browsers
    if ($contentType)
      header('Content-Type: '.$contentType);
    if ($filename)
      header('Content-Disposition: attachment; filename="' . $filename . '";');
    header('Content-Encoding: binary');
    header('Connection: close'); 
    header('Content-Length: ' . (int)$filesize);
  }

  /**
   * Flush (download) a file
   * 
   * @param string $file  filename on server to send
   * @param type $filename   for 'Content-Disposition: filename=*' header, defaults to basename($file) if omitted
   * @param type $contentType for 'Content-Type:*' header
   */
  protected final function flushFile($file, $filename = null, $contentType = null) 
  {
    if (!$filename)
      $filename = basename($file);
    if (!file_exists($file)) {
      throw new \Exception('File "'.$file.'" not exists!');
      //header("HTTP/1.1 500 Internal Server Error");
      //echo 'ERROR: '.__METHOD__.'() - file "'.$file.'" not exists.';
      //ob_end_flush();
    } else {
      if (!$contentType)
        $contentType = function_exists('mime_content_type') ? mime_content_type($file) : 'application/octet-stream';
      $this->flushPrepare(filesize($file), $contentType, $filename);
      readfile($file);
      ob_end_flush();
    }
    exit; // end script (calls API::__destruct())
  }
  
  /**
   * Flush (send to download) a binary string
   * @param type $binary  data string to send
   * @param type $filename for 'Content-Disposition: filename=*' header
   * @param type $contentType for 'Content-Type:*' header
   */
  protected final function flushBinary($binary, $filename = null, $contentType = 'application/octet-stream') 
  {
    //header_remove('Content-Encoding');
    if (!is_string($binary) || !is_scalar($contentType)) {
     // header("HTTP/1.0 500 Internal Server Error");
      throw new \Exception('ERROR: '.__METHOD__.'() - bad input data');
    } else {
      $this->flushPrepare(strlen($binary), $contentType, $filename);
      echo $binary;
      ob_end_flush();
    }
    exit; // end script (calls API::__destruct())
  }
  
  /**
   * Populate $_FILES array with file uploaded directly via POST, 
   * just like it were uploaded via input[type=file]
   * EXCEPT is_uploaded_file & move_uploaded_file will always fail, so use rename instead
   * 
   * @param string $name  key in $_FILES to be populated
   * @param string $filename  original filename (will be passed to 'name')
   * @return bool true if file was added, false if $name 
   */
  protected function handleDirectUpload($name, $filename) 
  {
    if (!isset($_SERVER["CONTENT_LENGTH"])) {
      throw new Exception(__METHOD__.': getting content length is not supported.');
    }
    $declaredSize = (int)$_SERVER["CONTENT_LENGTH"];
    $input = fopen("php://input", "r");
    $tempFile = tempnam('','php');
    $temp = fopen($tempFile, 'w'); //          tmpfile();
    $this->addTempFiles($tempFile);
    $realSize = stream_copy_to_stream($input, $temp);
    fclose($input);
    fclose($temp);
    if ($realSize != $declaredSize){            
      throw new Exception(__METHOD__.': content-length doesn\'t match actual size'); ;
    }
    $_FILES[$name] = array('name'=>$filename, 'type'=>$_SERVER['CONTENT_TYPE'], 'tmp_name'=>$tempFile, 'error'=>0, 'size'=>$realSize);
  }
  
}

?>
