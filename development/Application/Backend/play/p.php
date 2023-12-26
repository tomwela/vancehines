<?php 
namespace ClickBlocks\MVC\Backend;

use ClickBlocks\Core,
    ClickBlocks\DB,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers,
    ClickBlocks\Web\UI\POM,
    ClickBlocks\Utils;

class PagePlay extends Backend
{
  public function __construct()
  {
    parent::__construct('play/p.html');
  }
  
  public function init()
  {
    parent::init();
    $this->head->name = 'play!'; //Tab title

  }
  
}
  
 ?>