<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core;

class ValidatorCustom extends Validator
{
   public function __construct($id, $message = null)
   {
      parent::__construct($id, $message);
      $this->properties['serverFunction'] = null;
      $this->properties['clientFunction'] = null;
      $this->type = 'custom';
   }

   public function validate()
   {
      if (!$this->properties['serverFunction']) $flag = true;
      else
      {
         $delegate = new Core\Delegate($this->properties['serverFunction']);
         $flag = $delegate($this->properties['controls'], $this->properties['mode']);
      }
      foreach ($this->properties['controls'] as $uid)
        $this->results[$uid] = $flag;   
      $this->properties['isValid'] = $flag;
      $this->doAction();
      return $this;
   }

   protected function getScriptString(array $params = NULL)
   {
      if ($this->ajax->isSubmit()) $p = 'parent.';
      return parent::getScriptString(array(
         'hiding'=>(int)$this->properties['hiding'],
         'serverFunction' => $this->properties['serverFunction'] ? "'".addslashes($this->properties['serverFunction'])."'" : 'false',
         'clientFunction' => ($this->properties['clientFunction'] ?: 'false')
         ));
   }
}

?>
