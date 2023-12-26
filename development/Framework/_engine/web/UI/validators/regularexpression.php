<?php

namespace ClickBlocks\Web\UI\POM;

class ValidatorRegularExpression extends Validator
{
   public function __construct($id, $message = null)
   {
      parent::__construct($id, $message);
      $this->properties['expression'] = null;
      $this->type = 'regularexpression';
   }

   public function validate()
   {
      switch ($this->properties['mode'])
      {
         case 'AND':
           $flag = true;
           foreach ($this->properties['controls'] as $uniqueID) $flag &= $this->validateControl($uniqueID);
           break;
         case 'OR':
           $flag = false;
           foreach ($this->properties['controls'] as $uniqueID) $flag |= $this->validateControl($uniqueID);
           break;
         case 'XOR':
           $n = 0;
           foreach ($this->properties['controls'] as $uniqueID) if ($this->validateControl($uniqueID)) $n++;
           $flag = ($n == 1);
           break;
      }
      $this->properties['isValid'] = $flag;
      $this->doAction();
      return $this;
   }

   protected function getScriptString(array $params = null)
   {
      return parent::getScriptString(array('expression' => "'" . addslashes($this->properties['expression']) . "'"));
   }

   public function check($value)
   {
      if ($this->properties['expression'] != '' && $value != '')
      {
         if ($this->properties['expression'][0] == 'i') return (preg_match(substr($this->properties['expression'], 1), $value) == 0);
         return (preg_match($this->properties['expression'], $value) != 0);
      }
      return true;
   }
}

?>
