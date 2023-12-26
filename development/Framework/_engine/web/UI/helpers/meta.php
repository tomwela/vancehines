<?php

namespace ClickBlocks\Web\UI\Helpers;

use ClickBlocks\Core,
    ClickBlocks\Web;

class Meta extends Control
{
   public function __construct($id = null, $name = null, $content = null, $httpequiv = null, $charset = null)
   {
      parent::__construct($id);
      $this->attributes['name'] = $name;
      $this->attributes['content'] = $content;
      $this->attributes['scheme'] = null;
      $this->attributes['http-equiv'] = $httpequiv;      

      $this->attributes['charset'] = $charset;      
   }
   
   public function render()
   {
      return '<meta' . $this->getParams() . ' />';
   }
}

?>
