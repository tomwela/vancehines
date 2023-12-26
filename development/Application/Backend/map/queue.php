<?php

namespace ClickBlocks\MVC\Backend;

use ClickBlocks\Core,
    ClickBlocks\DB,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers,
    ClickBlocks\Web\UI\POM,
    ClickBlocks\Utils;

class PageMapQueue extends Backend
{
  public function __construct()
  {
    parent::__construct('map/queue.html');
  }

  public function init()
  {
    parent::init();
    $this->head->name = 'Map Queue';

  }  


  public function updateWidget()
  {
    $this->get('mapQueue')->update();
  }
}

?>
