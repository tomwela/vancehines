<?php 
namespace ClickBlocks\MVC\Backend;

use ClickBlocks\Core,
    ClickBlocks\DB,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers,
    ClickBlocks\Web\UI\POM,
    ClickBlocks\Utils;

class PageNotes extends Backend
{
  public function __construct()
  {
    parent::__construct('notes/notes.html');
  }
  
  public function init()
  {
    parent::init();
    $this->head->name = 'notes!'; //Tab title

  }
  
}
  
 ?>