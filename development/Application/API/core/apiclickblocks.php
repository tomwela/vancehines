<?php

/**
 * API wrapper for Clickblocks. Use only in context of framework 
 */

namespace ClickBlocks\API;
use ClickBlocks\DB,
    ClickBlocks\Core,
    ClickBlocks\Utils,
    ClickBlocks\Exceptions,
    ClickBlocks\Utils\DT;

require_once 'apiext.php';
require_once 'jsonpath.php';

class ParameterRequiredException extends \LogicException {}
class ParameterValidationException extends \LogicException {}

class API extends APIExt
{
   /**
    * @var Core\Register 
    */
   protected $reg;
   protected $isDebug = true;

   protected $batchNames = array();
   protected $entities = array();
   protected $timer = null;

   protected function init() {
      $this->setErrorLevel(E_ALL & ~E_NOTICE);
      parent::init();
      $this->timer = new Utils\Timer();
      $this->timer->start('TOTAL');
      //DB\ORM::getInstance()->getDB('db0')->catchException = true;
      $this->reg = Core\Register::getInstance();
      $this->isDebug = (bool)$this->reg->config->isDebug;
      if ($this->isDebug) {
        $this->logOut('> '.date('Y-m-d H:i:s').' > Request from '.$_SERVER['REMOTE_ADDR'].': '.urldecode($_SERVER['REQUEST_URI']).'; Post: data='.$_POST['data']);
      }
   }
  
   public function setEntities($ents) {
      $this->entities = $ents;
      return $this;
   }
   
   protected function beforeEachRequest() {
      $this->timer->start('Request #'.$this->batchIndex.' work');
   }
   
   protected function afterEachRequest() {
      $this->timer->stop();
   }

   protected function getBatchParams()
   {
      if (substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'?'))=='/api/batch') {
         $batch = json_decode($_GET['batch'] ?: $_POST['batch'], true);
         if (!is_array($batch)) {
            throw new APIException('Invalid batch array');
         }
         $i = 0;
         foreach ($batch as $k=>$info) {
            if ($k!=$i || !is_array($info)) throw new APIException('Invalid batch array element #'.$i);
            if ($info['name']) $this->batchNames[$info['name']] = $k;
            $i++;
         }
         return $batch;
      } else return NULL;
   }
  
  /**
   * Checks provided request name and converts it to batch request index, if needed
   * @param string $bk Request name in batch requests; 0-based index or qualified name
   * @throws APIException on invalid dependancies
   * @return int Request index
   */
   protected function getRequestDependancy($name)
   {
      $bk = $name;
      if (!is_numeric($bk)) $bk = $this->batchNames[$bk];
      if ($bk === NULL || $bk >= count($this->batch)) throw new APIException('Dependancy to invalid request '.$name.' in request #'.$this->batchIndex);
      if ($this->results[$bk] == NULL) throw new APIException('Dependancy to future request is impossible. Request #'.$this->batchIndex.', link: '.$name);
      if ($this->results[$bk]['errors']) throw new APIException('Dependancy request '.$name.' was not successfull');
      return $bk;
   }

   protected function filterRequestParams($data, $isRecursion = false)
   {
      if (!is_array($data)) return array();
      foreach ($data as $k=>$v) {
         if (is_array($v)) {
            if ($isRecursion) $v = $this->filterRequestParams($v, true);
         } else if (is_string($v) && preg_match('/^(ARRAY|STRING|NUMBER|BOOLEAN):FROM\:(\w+)\:(\$[^\}]*)$/', $v, $m)) {
             $bk = $this->getRequestDependancy($m[2]);
             $data[$k] = jsonPath($this->results[$bk]['return'], $m[3]);
             switch ($m[1]) {
                case 'STRING': 
                   $data[$k] = (string)$data[$k][0]; 
                   break;
                case 'NUMBER': 
                   $data[$k] = (float)$data[$k][0]; 
                   break;
                case 'BOOLEAN':
                   $data[$k] = (bool)$data[$k][0]; 
                   break;
                default: case 'ARRAY': 
                   // leave it
                   break;
             }
         }
      }
      return $data;
   }

   public function getRequestParams() 
   {
      $mainData = array_merge((array)json_decode($_GET['data'],true), (array)json_decode($_POST['data'],true));
      if ($this->isBatchMode()) {
         $request = $this->batch[$this->batchIndex];
         if (($request['data'] && !is_array($request['data'])) || !is_string($request['method'])) {
             $this->HTTPStatusCode = 500;
             throw new APIException('Invalid batch request #'.$this->batchIndex);
         }
         if ($request['depends']) {
            $this->getRequestDependancy($request['depends']);
         }
         //if ($data['sKey']) $this->sKey = $data['sKey'];
         //else if ($this->sKey) $data['sKey'] = $this->sKey;
         return array_merge(  $mainData, 
                              $this->filterRequestParams($this->batch[$this->batchIndex]['data'], true), 
                              $_FILES  );
      }
      if (!count($mainData) && ($_GET['data'] || $_POST['data']))
        throw new APIException('Invalid JSON recieved');
      return array_merge($mainData, $_FILES);
   }

   public function getRequestInfo() 
   {
      $info = array('params' => $this->getRequestParams());
      if ($this->isBatchMode()) {
         if (!preg_match('%^(\w+)\/(\w+)$%', $this->batch[$this->batchIndex]['method'], $m))
             throw new APIException('Incorrect method name');
         $entity = $m[1];
         $method = $m[2];
      } else {
         preg_match_all('%\/(\w+)%', @$_SERVER['REQUEST_URI'], $m);
         if (count($m[1]) != 3)
           throw new APIException("Path must contain Entity and Method");
         $entity = $m[1][1];
         $method = $m[1][2];
      }
      if (!in_array(strtolower($entity), $this->entities))
        throw new APIException("Entity '$entity' not exists");
      $info['class'] = '\ClickBlocks\API\Logic\\'.$entity;
      $info['method'] = 'api_'.$method;
      return $info;
   }
  
   protected function getResultForRequest(array $state)
   {
      if ($this->isError($state)) 
      {
         if (!$this->isBatchMode()) $this->HTTPStatusCode = 417;
         $return = array ('success' => false, 'error'=>array('code'=>$state['statusCode']), 'result'=>null);
         if ($this->isDebug) {
           $return['error']['description'] = (string)end($state['errors']);
           if ($state['exception'] && !($state['exception'] instanceof \LogicException)) $return['backtrace'] = $state['exception']->getTrace();
           $return['data'] = $state['params'];
           $return['files'] = $_FILES;
         } else {
           $return['error']['description'] = ($state['exception'] && ($state['exception'] instanceof \LogicException)) ? $state['exception']->getMessage() : 'Internal Error Occurred';
         }
      } else {
         $return = $state['return'] ?: array();
         if (!is_array($return))$return = array('message' => $return);
         $return = array('success' => true, 'result' => $return);
      }
      $return['echo'] = $state['echo'];
      if ($this->timer && $state['batchIndex']==0) $return['times'] = $this->timer->stop()->getTimes();
      $this->return = array();
      return $return;
   }
   
   private function logOut($text) {
      @file_put_contents(Core\IO::dir('temp').'/api_log.txt', $text.PHP_EOL, FILE_APPEND);
   }

   protected function formatResult($result) {
     $res = json_encode($result);
     // Be extremely catious in any code here! NO ERRORS allowed here, as they will never be shown!
     if ($this->isDebug) {
       $this->logOut('Response: '.$res.PHP_EOL.PHP_EOL);
     }
     return $res;
   }

   protected function getContentType() {
     return 'application/json';
   }

   protected function getLogFile() {
     return Core\IO::dir('temp').'/api_log.html';
   }
}

/*
 * tests:
 * http://edepo.local/api/batch?batch=[{"method":"edepo/test","data":{"a":1}},{"method":"user/login","name":"abc","data":{"email":"client@edepo.com","password":1234}},{"method":"cases/getList","data":{"sKey":"STRING:FROM:abc:$.sKey"}}]&data={"a":2}
 * 
 */


?>
