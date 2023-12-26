<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Utils,
    ClickBlocks\MVC,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers;


class Table extends Panel
{
   public function __construct($id)
   {
      parent::__construct($id);
      $this->properties['tag'] = 'table';
      $this->attributes['cellspacing'] = 0;
      $this->attributes['cellpadding'] = 0;
      $this->attributes['border']      = 0;
   }
}

?>
