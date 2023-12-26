<?php

namespace ClickBlocks\API;
use \Exception;

require_once 'apibase.php';

/**
 * Extended version of API with additional features:
 * - batch request 
 * 
 * @version 1.0
 * @author Killian
 */
abstract class APIBatch extends APIBase 
{
   protected $batch = null;
   protected $batchIndex = 0;
   protected $results = array();

   protected function init() 
   {
      parent::init();
      $this->batch = $this->getBatchParams();
      if (is_array($this->batch) && count($this->batch) > 0) $this->results = array_fill(0, count($this->batch), null);
   }
   
   protected function processRequest() 
   {
      $count = is_array($this->batch) ? count($this->batch) : 1;
      for ($this->batchIndex = 0; $this->batchIndex < $count; $this->batchIndex++) 
      {
         $this->beforeEachRequest();
         parent::processRequest();
         $this->afterEachRequest();
         if ($count > 1) {
            $data = $this->getCurrentState();
            $this->exception = null;
            $this->errors = $this->return = array();
            $this->results[$this->batchIndex] = $data;
         }
      }
   }
   
   /**
    * Executed before each Request is handled (before getRequestInfo()) Executed always
    */
   protected function beforeEachRequest() {}
   
   /**
    * Executed after each Request is handled (in single or multiple mode) Executed always, except fatal errors and unexpected script stop (like exit)
    */
   protected function afterEachRequest() {}

   /**
    * @return array of requests
    */
   abstract protected function getBatchParams();

   protected final function isBatchMode()
   {
      return is_array($this->batch);
   }

   protected function getCurrentState()
   {
      $data = array('params'=>$this->params, 'statusCode'=>$this->statusCode, 'exception'=>$this->exception, 'errors'=>$this->errors, 'return'=>$this->return, 'echo'=> ob_get_contents(), 'batchIndex'=>$this->batchIndex);
      if (!$this->isFatalError()) ob_clean();
      return $data;
   }
   
   protected final function getResult() 
   {
      if (count($this->results) > 0) {
         $return = array_fill(0, count($this->results), NULL);
         foreach ($this->results as $i=>$result)
         {
            if ($result === NULL && $i > $this->batchIndex) break;
            if ($result === NULL) $result = $this->getCurrentState();
            $return[$i] = $this->getResultForRequest($result);
         }
         return $return;
      } else {
         return $this->getResultForRequest($this->getCurrentState());
      }
   }
   
   public function isError(array $state = null) {
      if (!$state) return parent::isError();
      return (count($state['errors']) > 0);
   }

      /**
    * should return result array for single request
    * @param array $state array(params, statusCode, exception, errors, return, echo)
    */
   abstract protected function getResultForRequest(array $state);
}


?>
