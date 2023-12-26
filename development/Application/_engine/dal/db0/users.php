<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

/**
 * @property int $userID
 * @property varchar $firstName
 * @property varchar $lastName
 * @property varchar $email
 * @property varchar $password
 * @property datetime $created
 */
class DALUsers extends DALTable
{
  public  function __construct()
  {
      parent::__construct('db0', 'Users');
   }
}

?>