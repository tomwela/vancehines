<?php
namespace ClickBlocks\MVC\Backend;

use ClickBlocks\Core,
    ClickBlocks\DB,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers,
    ClickBlocks\Web\UI\POM,
    ClickBlocks\Utils;

class PageLogin extends Backend
{
   public function __construct()
   {
      parent::__construct('login/login.html');
   }

   public function access()
   {
      $this->noAccessURL = $this->config->cms . '/';
      return !$this->user->userID;
   }
   
  public function init()
  {
    parent::init();
    $this->head->name = 'Authorization';
//    if (isset($_COOKIE['__VH__']))
//    {
//      $this->get('email')->value = $_COOKIE['__VH__']['email'];
//      $this->get('password')->value = $_COOKIE['__VH__']['pwd'];
//    }
  }

  function check()
  {
    $row = foo(new DB\OrchestraUsers())->login($this->get('email')->value, $this->get('password')->value);
    
    if(!$row){
      $this->get('email')->addClass('error');
      $this->get('password')->addClass('error');
      $this->get('email')->update();
      $this->get('password')->update();
      return false;
    }
    // Store info
    $_SESSION['__VH__']['userID'] = $row['userID'];
    setcookie("__VH__[email]", $this->get('email')->value, time() + 2592000);
    setcookie("__VH__[pwd]", $this->get('password')->value, time() + 2592000);
    
    return true;
  }
}

?>
