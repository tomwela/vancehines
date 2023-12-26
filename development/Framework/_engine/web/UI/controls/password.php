<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core;

class Password extends TextBox
{
   public function __construct($id, $value = null)
   {
      parent::__construct($id);
      $this->attributes['type'] = 'password';
   }
}

?>
