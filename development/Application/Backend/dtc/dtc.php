<?php

namespace ClickBlocks\MVC\Backend;

use ClickBlocks\Core,
    ClickBlocks\DB,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers,
    ClickBlocks\Web\UI\POM,
    ClickBlocks\Utils;

class PageDtc extends Backend
{
  public function __construct()
  {
    parent::__construct('dtc/dtc.html');
   //$dw= new ClickBlocks\DB\OrchestaTestOrc;
   
  }

  public function init()
  {
    parent::init();
   $this->head->name = 'Manage test';

  }
//////////////////////////////////
    public function testUser($id = 0, $flag = 0)
  { 
    // $user = foo(new DB\ServiceUsers)->getByID(99);
    // $popup->tpl->text=var_dump($user);
  }

///show popup





  }

?>
