<?php
namespace ClickBlocks\MVC\Backend;

use ClickBlocks\Core,
    ClickBlocks\DB,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers,
    ClickBlocks\Web\UI\POM,
    ClickBlocks\Utils;

class PageForgot extends Backend
{
   public function __construct()
   {
      parent::__construct('login/forgot.html');
   }

   public function access()
   {
      $this->noAccessURL = $this->config->cms . '/groups';
      return ((int)$this->user->userID <= 0);
   }

  public function init()
  {
    parent::init();
    $this->head->name = 'Authorization';
  }

  public function checkEmailExist()
  {
    $pnlForgot = $this->get('forgot');
    $row = foo(new DB\OrchestraUsers())->checkEmailExist($pnlForgot->get('email')->value);
    if(!$row){
      $this->ajax->script('controls.focus(\'' . $pnlForgot->get('email')->uniqueID . '\')', 100);
      $pnlForgot->get('email')->addClass('error');
      $pnlForgot->get('email')->update();
      $this->get('valCustom')->style = 'display:block;';
      return false;
    }
    return true;
  }

  public function retrievalPassword($vs)
  {
    if (!$this->validators->isValid('forgot'))
      return;

    $user = foo(new DB\OrchestraUsers())->getUserDataByEmail($vs['email']);

    // generate a random 6 char password
    $pass = \ClickBlocks\MVC\Backend\PageUsers::generateNewRandomPassword();
    // hash the password
    $hash = \ClickBlocks\MVC\Backend\PageUsers::generateHash($pass);

    // Update Users: store new password in the DB
    $svc = new DB\ServiceUsers();
    $user = $svc->getByID($user['userID']);
    $user->password = $hash;
    $svc->update($user);


    $mailer = new \PHPMailer();
    $host = "localhost";

    $mailer->Subject  = 'Password Generation';
    $mailer->Body     = 'Your password is: ' . $pass . endl . 'Please store this in a secure place.' . endl . endl . 'Thank you!' . endl;
    $mailer->FromName = $this->config->email['fromName'];
    $mailer->From     = $this->config->email['fromEmail'];
    $mail->Host       = $host;
    $mailer->AddAddress($user->email);

    if ($mailer->Send())
    {
      $this->get('forgot')->visible = false;
      $this->get('success')->visible = true;
    }
  }

}

?>
