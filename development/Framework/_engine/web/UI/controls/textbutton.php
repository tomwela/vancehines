<?php

namespace ClickBlocks\Web\UI\POM;

class TextButton extends WebControl
{
   public function __construct($id, $value = null, $type = 'button')
   {
      parent::__construct($id);
      $this->attributes['name'] = $id;
      $this->attributes['value'] = $value;
      $this->attributes['type'] = $type;
      $this->attributes['src'] = null;
   }
   
   public function render()
   {
      if (!$this->properties['visible']) return $this->invisible();
      $html = '<input';
      if ($this->properties['disabled']) $html .= ' disabled="disabled"';
      $html .= $this->getParams() . ' />';
      return $html;
   }
}

?>
