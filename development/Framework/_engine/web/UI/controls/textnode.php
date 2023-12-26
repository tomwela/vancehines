<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Web;

class TextNode extends WebControl
{
   public function __construct($id = null, $text = null)
   {
      parent::__construct($id);      
      $this->properties['text'] = $text;
   }

   public function render()
   {
      return $this->properties['text'];
   }
}

?>
