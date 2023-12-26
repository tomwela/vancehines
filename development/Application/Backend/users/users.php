<?php

namespace ClickBlocks\MVC\Backend;

use ClickBlocks\Core,
    ClickBlocks\DB,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers,
    ClickBlocks\Web\UI\POM,
    ClickBlocks\Utils;

class PageUsers extends Backend
{
  public function __construct()
  {
    parent::__construct('users/users.html');
  }
  
  public function init()
  {
    parent::init();
    $this->head->name = 'Manage Users';

  }

   public function userAdd()
  {
    $popup = $this->get('userAdd');
    $popup->tpl->userID = null;
    $popup->get('firstName')->value = NULL;
    $popup->get('lastName')->value = NULL;
    $popup->get('email')->value = NULL;
    $popup->get('password')->value = 
    $popup->get('confirmPassword')->value = 
      NULL;
    $popup->show();
  }  
  
   public function userEdit($userID)
  {
    $popup = $this->get('userAdd');
    $popup->tpl->userID = $userID;
    $user = foo(new DB\ServiceUsers)->getByID($userID);
    $popup->get('firstName')->value = $user->firstName;
    $popup->get('lastName')->value = $user->lastName;
    $popup->get('email')->value = $user->email;
    $popup->get('password')->value = '';
    $popup->get('confirmPassword')->value = '';
    $popup->show();
  }

  public function saveUser(array $fv, $id = null)
  {
    if(!$this->validators->isValid()) return false;

    $user = new DB\Users($id);
    $user->setValues($fv);
    
    if (!$fv['password']) return false;
    
    $user->password = $this->generateHash($fv['password']);
    $user->created = 'NOW()';
    $user->save();
    $this->get('userAdd')->hide();
    $this->get('users')->searchvalue = '';
    $this->get('users')->update();
  }

  public function deleteUser($id = 0, $flag = 0)
  { 
    $user = foo(new DB\ServiceUsers)->getByID($id);
    if($id == 1) return;
    $popup = $this->get('msg');
    if ($flag)
    {
      $popup->tpl->title = "Delete User";
      $popup->tpl->text = 'You are going to delete '. $user->firstName .' '. $user->lastName .'. <br> Continue?';
      $popup->get('btnYes')->onclick = "ajax.doit('->deleteUser'," . (int) $id . ")";
      $popup->show();
    } else
    {
//      foo(new DB\ServiceUsers())->deleteByID((int) $id);
      foo(new DB\OrchestraUsers)->deactivateUser((int) $id);
      $this->get('users')->update();
      $popup->hide();
    }
  }

  public function deleteGroup($ids, $flag = 0)
  {
    if(!array_filter($ids)) return;
    
    $popup = $this->get('msg');
    if ($flag)
    {
      $popup->tpl->title = "Delete User";
      $popup->tpl->text = 'You are going to delete some users <br>      Continue?';
      $popup->get('btnYes')->onclick = "ajax.doit('->deleteGroup'," . json_encode($ids) . ")";
      $popup->show();
    } else
    {
//      foo(new DB\OrchestraUsers)->deleteGroup($ids);
      foo(new DB\OrchestraUsers)->deactivateGroup($ids);
      $this->get('users')->update();
      $popup->hide();
    }
    
  }
  
// for php >= 5.4
//  public  function xxxxxx($email, $password)
//  {
//    //$hash = password_hash($password, PASSWORD_DEFAULT);
//    $dbPassword = $this->db->row('SELECT password FROM Users WHERE email = ?', array($email));
//    
//    if (password_verify($password, $dbPassword['password'])) {
//        return $this->db->row('SELECT userID FROM Users WHERE email = ?', array($email));
//    } else {
//        return 0;
//    }
//  }      
  
  
  
  /**
   * Generates a password HASH with a random salt
   * @param type $password
   * @return type string
   */
  public static function generateHash($password) 
  {
    if (defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH) {
        $salt = '$2y$11$' . substr(md5(uniqid(rand(), true)), 0, 22);
        return crypt($password, $salt);
    }
  }

  /**
   * Generates a random 6 character password
   * @return type string
   */
  public static function generateNewRandomPassword()
  {
    return substr( base64_encode(uniqid('up')),14,6 );
  }

  /**
   * For Login Process:  compares a hash generated from the typed in password
   * to the hash stored in the database.  If they match, allow login.
   * @param string $password
   * @param string $dbHash
   * @return boolean
   */
  public static function compareHashes($password, $dbHash) 
  {
    if( crypt($password, $dbHash) == $dbHash ) {
        return true;
    } else {
        return false;
    }
  }
  
}

?>
