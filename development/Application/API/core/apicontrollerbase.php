<?php

// FOR PHP <= 5.2, comment out following 2 lines:
namespace ClickBlocks\API;
use \Exception, \LogicException, \ErrorException;
// PHP >= 5.0 compatible code starts here

interface IAPIControllerBase
{
  function init_($method);
  function finalize_($method);
}

/**
 * Class holds methods directly invoked by user via API
 * NOTE: be carefull with public methods, as they are accessible by API user
 * - to limit access on API level use validateMethod()
 * - on controller level use init_($method)
 */
abstract class APIControllerBase implements IAPIControllerBase
{
  /**
   * @var IAPIBase 
   */
  public $return;
  public $params = array();

  public function __construct() 
  {
    //$this->api = APIBase::getInstance();
    //$this->params = $this->api->getParams();
    //$this->return = &$this->api->return;
  }
  
  public final function __destruct() {}
  
  
  /**
   * check if all of the required params is present in request and throw exception if not
   * 
   * @param array $list list of parameter names to check
   * @param bool $notEmpty if true - check that parameter value is not empty (eg. php typecast to bool returns true)
   */
  protected function requireParams(array $list, $notEmpty = false, $exceptionCode = 0) 
  {
    $diff = array();
    foreach ($list as $field) {
      if (!isset($this->params[$field]) || ($notEmpty && !(bool)$this->params[$field]))
        $diff[] = $field;
    }
    if (count($diff))
      throw new LogicException("Required parameter(s) not specified: '".implode("', '",$diff)."'".($notEmpty ? " (cannot be empty)":""), $exceptionCode);
  }
  
  /**
   * this method is executed before actual call to user-specified API method
   * perform global checks here, eg. authorization
   * use LogicException for errors
   * @param string  $method method name
   * @return void
   */
  public function init_($method) { }
  
  /**
   * this method is executed:
   * - after successfull call, OR
   * - after exception is thrown in...
   *   - _init()
   *   - method being called
   * @param string  $method method that was previously executed
   */
  public function finalize_($method) 
  {
    
  }
}


?>
