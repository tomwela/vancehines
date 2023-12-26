<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core;

class Validators implements \IteratorAggregate
{
   private static $instance = null;

   protected $reg = null;
   protected $results = array();

   private function __clone(){}

   private function __construct()
   {
      $this->reg = Core\Register::getInstance();
   }

   public function &__get($param)
   {
      if ($param == 'results') return $this->results;
      return parent::__get($param);
   }

   public static function getInstance()
   {
      if (self::$instance === null) self::$instance = new Validators();
      return self::$instance;
   }

   public function getIterator()
   {
      return new POMIterator($this->getValidators());
   }

   public function getValidators($uniqueID = null)
   {
     if (!$uniqueID) return $this->reg->page->getValidators();
     $vals = array();
     foreach ($this->reg->page->getValidators() as $uid)
     {
        $validator = $this->reg->page->getByUniqueID($uid);
        if (in_array($uniqueID, $validator->controls)) $vals[] = $uid;
     }
     return $vals;
   }

   public function clean($group = 'default', $update = false)
   {
      $validators = $this->getValidators();
      uasort($validators, array($this, 'sortValidators'));
      $iterator = new POMIterator($validators);
      foreach ($iterator as $validator)
      {
         if (in_array($group, $validator->groups) || $group == '')
         {
            $validator->isValid = true;
            if ($update) $validator->update();
         }
      }
      return $this;
   }

    protected function showResults($invClass = 'error', $valClass = '')
    {
      $res = true;
      foreach ($this->results as $uniqueID => $result)
      {
        $ctrl = $this->reg->page->getByUniqueID($uniqueID);
        if($result) 
        {
          if($invClass) $ctrl->removeClass($invClass);
          if($valClass) $ctrl   ->addClass($valClass);
        }
        else
        {
          $res = false;
          if($invClass) $ctrl   ->addClass($invClass);
          if($valClass) $ctrl->removeClass($valClass);
        }      
      }
      return $res;    
    }

  public function isValid($group = 'default', $isAll = true, $showResults = false, $invClass = 'error', $valClass = '')
  {
    $validators = $this->getValidators();
    $groups = explode(',', $group);
    uasort($validators, array($this, 'sortValidators'));
    $iterator = new POMIterator($validators);
    $flag = true;
    $ctrlres = $valres = array();
    foreach ($groups as $group)
    {
       foreach ($iterator as $validator)
       {
          if ($validator->groups&&(in_array($group, $validator->groups) || $group == ''))
          {
            $res = $validator->validate()->isValid;
            foreach($validator->controls as $uniqueID)
            {
              if(!isset($this->results[$uniqueID])) $this->results[$uniqueID] = true;                 
              $ctrlres[$uniqueID] = $this->results[$uniqueID] &=  ($valres[$validator->uniqueID] = $validator->results[$uniqueID]);
            }
            if(!$res)
            {
              $flag = false;
              if(!$isAll) return $showResults?$this->showResults($invClass, $valClass):false;
            }
          }
       }
    }
    if($this->reg->config->valDebug) \ClickBlocks\Web\Ajax::getInstance()->consolelog($group, $ctrlres, $valres);
    return $showResults?$this->showResults($invClass, $valClass):$flag;
  }

  public function validate($group = 'default', $invClass = 'error', $valClass = '', $isAll = true)
  {
    return $this->isValid($group, $isAll, true, $invClass, $valClass);
  }

   private function sortValidators($uniqueID1, $uniqueID2)
   {
      $vs1 = $this->reg->page->getActualVS($uniqueID1);
      $vs2 = $this->reg->page->getActualVS($uniqueID2);
      $order1 = $vs1['parameters'][1]['order'];
      $order2 = $vs2['parameters'][1]['order'];
      if ($order1 == $order2) return 0;
      return ($order1 < $order2) ? -1 : 1;
   }
}

?>
