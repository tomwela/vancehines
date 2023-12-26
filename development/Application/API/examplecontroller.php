<?php

namespace ClickBlocks\API\Logic;
use ClickBlocks\DB,
    ClickBlocks\Core,
    ClickBlocks\API,
    ClickBlocks\Utils,
    ClickBlocks\Exceptions,
    ClickBlocks\Utils\DT;

class Example extends API\APIControllerExt 
{
   const SESSION_STORAGE = 'api_auth';
   
   const TYPE_INTEGER = 'int';
   const TYPE_STRING = 'str';
   const TYPE_BOOLEAN = 'bool';
   const TYPE_ARRAY = 'array';
   const TYPE_NUMBER = 'number';
   const TYPE_EMAIL = 'email';
   const TYPE_SCALAR = 'scalar';
   
   /**
    * @var \ClickBlocks\Core\Register 
    */
   protected $reg;
   
   /**
    * @var \ClickBlocks\Core\Config
    */
   protected $config;
   
   /**
    * @var \ClickBlocks\Utils\Timer
    */
   protected $timer;
   
   /**
    * @var \ClickBlocks\DB\Users
    */
   protected $user;
   
   /**
    * This method is executed right before the main method that was called by user
    * @param string $method Name of method that will be called
    * @throws \LogicException
    */
   public function init_($method) 
   {
      parent::init_($method);
      try {
         //DB\ORM::getInstance()->getDB('db0')->catchException = true;
      } catch (\Exception $e) {
         throw new \LogicException('Database is not available', 500);
      }
      $this->reg = Core\Register::getInstance();
      $this->config = $this->reg->config;
      $this->timer = new Utils\Timer();
      // check authorization
      $flag = true;
      $sKey = @$this->params['sKey'];
      if (!$sKey) {
         $flag = false;  // session key not provided
      } else {
         $hashes = $this->reg->cache->get(self::SESSION_STORAGE) ? : array();
         // delete expired sessions
         foreach ($hashes as $hash => $info) if (time() > $info['expire']) unset($hashes[$hash]);
         if (isset($hashes[$sKey])) {
            /*$this->user = foo(new DB\ServiceUsers)->getByID($hashes[$sKey]['id']);
            if (!$this->user->userID) {
               unset($hashes[$sKey]);
               throw new \LogicException('Session for non-existent user #' . $hashes[$sKey]['id'], 0);
            }*/
         } else {
            $flag = false; // session not found
         }
         $this->reg->cache->set(self::SESSION_STORAGE, $hashes, $this->getSessionLifeTime());
      }
      if (!$flag && ($sKey || !in_array(substr(strtolower($method), 4), $this->getNoAuthMethods())))
         $this->except('Authorization failed', 401);
   }
   
   protected function getSessionLifeTime()
   {
      return (int)$this->reg->config->api['sessionLifeTime'] ?: 60;
   }

   public function finalize_($method) 
   {
      parent::finalize_($method);
      $this->return['timestamp'] = time();
      $tz = date('Z');
      $this->return['tzOffset'] = (int)$tz;
   }
   
   protected function createSession($userID) 
   {
    $hash = hash('sha512', microtime().$this->reg->config->secretSalt); 
    $hashes = $this->reg->cache->get(self::SESSION_STORAGE)?: array(); 
    foreach ($hashes as $h=>$info) // delete all previous sessions
      if ($info['id']==$userID)
        unset($hashes[$h]);
    $hashes[$hash] = array('id'=>$userID, 'expire'=>time() + $this->getSessionLifeTime() );
    $this->reg->cache->set(self::SESSION_STORAGE,$hashes, $this->getSessionLifeTime());
    return $hash;
  }

  protected function deleteSession($userID) 
  {
    $hashes = $this->reg->cache->get(self::SESSION_STORAGE)?: array();
    foreach ($hashes as $h=>$info) // delete all previous sessions
      if ($info['id']==$userID) 
      {
        unset($hashes[$h]);
      }
    $this->reg->cache->set(self::SESSION_STORAGE,$hashes, $this->sessionLifetime);
  }
   
   /**
    * Should return array of method names, for which the sKey will not be required (no user context, or it is optional)
    * @return array Array of method names
    * @access protected
    */
   protected function getNoAuthMethods()
   {
      return array('test','test2');
   }


   public function api_test(array $p) {
     
     $this->return = array('test'=> "The API is working! Great!", 'par'=>$p);
   }
   
   public function api_test2(array $p) {
      $this->validateParams(array(
           'email' => array(self::TYPE_EMAIL, 'req'=>true),
           'password' => array(self::TYPE_STRING, 'req'=>true),
           'number' => array(self::TYPE_INTEGER, 'req'=>false),
           'books' => array(self::TYPE_ARRAY, 'req'=>false),
       ));
      if ($p['books']) foreach ($p['books'] as $book) $this->validateObject($book, array(
         'title' => array(self::TYPE_STRING, 'req'=>true),
         'price' => array(self::TYPE_NUMBER, 'req'=>true),
         'description' => array(self::TYPE_STRING, 'req'=>false),
      ), 'Book object');
      $this->return = array('test'=> "Validation OK", 'par'=>$p);
   }

   protected function except($msg, $code = 0) 
   {
      throw new \LogicException($msg, $code);
   }

   protected static function exec($cmd, $error = 'executing') 
   {
      exec($cmd . ' 2>&1', $out, $err);
      if ($err) {
         throw new \Exception('Error ' . $error . ': "' . implode('; ', $out) . '"; Command: "' . $cmd . '"');
      }
      return implode('', $out);
   }
   
   /**
   * 
   * @param array $dbobject object from call to Orchestra or BLL->getValues() with raw string DB data
   * @param array $fields array describing fields and types
   * @param boolean $preserveOtherFields if true, fields the present in $dbobject but omitted in $fields will be preserved
   * @return type
   */
   protected function db2jsonObject(array $dbobject, array $fields, $preserveOtherFields = false)
   {
      $object = array();
      foreach ($fields as $field=>$info) {
         if (!is_array($info)) $info = array('type'=>$info);
         elseif (!$info['type'] && $info[0]) $info['type'] = $info[0];
         $dbfield = $info['bind'] ?: $field;
         $val = $dbobject[$dbfield];
         if ($info['bind']) unset($dbobject[$info['bind']]);
         //if (isset($info['nullValue'])) $this->return['test'] = $info['nullValue'];
         if (isset($info['nullValue']) && $val === (string)$info['nullValue']) $val = NULL;
         if ((bool)$info['type'] && $info['type']!=self::TYPE_STRING && $val === '') $val = NULL;
         if ($val !== NULL) switch ($info['type']) {
            case self::TYPE_INTEGER:
               $val = (int)$val;
               break;
            case self::TYPE_NUMBER: case self::TYPE_FLOAT:
               $val = (float)$val;
               break;
            case self::TYPE_BOOLEAN:
               $val = (bool)$val;
               break;
            case self::TYPE_MONEY:
               $val = (int)((float)$val*100);
               break;
            case self::TYPE_TIMESTAMP:
               if (!is_numeric($val)) $val = Utils\DT::sql2date($val, 'U', strlen($val)<12);
               $val = (int)$val;
               break;
         }
         $object[$field] = $val;
      }
      if ($preserveOtherFields) $object += $dbobject;
      return $object;
   }
   
   /**
    * Check presense of parameters
    * @param array $list  Array of parameters
    * @param type $notEmpty
    * @throws API\ParameterRequiredException If not specified
    */
  protected function requireParams(array $list, $notEmpty = true, $code = 201) 
  {
    try { 
      parent::requireParams((array)$list, $notEmpty);
    } catch (\LogicException $e) {
      throw new API\ParameterRequiredException($e->getMessage(), $code);
    }
  }
  
   public static function validateObject(&$obj, array $fields, $name)
   {
      return JSONObjectValidator::validateObject($obj, $fields, $name);
   }

  /**
   * Validates set of function parameters; should be called at start of every controller method
   * @param array $fields List of maps with keys: 
   *   name - string name of field to validate
   *   type - string type to check against
   *   required - bool whether it must be evaluated as true
   *   expression - regular expression to match against, for regexp type
   * @throws API\ParameterValidationException if value is present but not match validation
   * @throws API\ParameterRequiredException if one or more required parameters are missing
   */
  protected function validateParams(array $fields)
  {
    return JSONObjectValidator::validateObject($this->params, $fields, 'data');
  }
}

/*
Examples: 
Normal request:
http://clickblocks.local/api/example/test?data={"a":"1","b":2}

Batch request:
http://clickblocks.local/api/batch?batch=[{"name":"First","method":"example/test","data":{"a":1,"b":2}},{"method":"example/test2","data":{"email":"ad@sd.aa","password":"STRING:FROM:0:$.test","books":[{"title":"asd"},2,3]}}]&data={"a":"a","c":3}

*/

?>
