<?php 


namespace ClickBlocks\MVC\Backend;

use ClickBlocks\Core,
    ClickBlocks\DB,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers,
    ClickBlocks\Web\UI\POM,
    ClickBlocks\Utils;

class PageTopstories extends Backend
{
  public function __construct()
  {
    parent::__construct('topstories/topstories.html');
  }
  
  public function init()
  {
    parent::init();
    $this->head->name = 'Manage TOP'; //Tab title

  }

  }

   ?>