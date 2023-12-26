<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraUsers extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\Users');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\Users';
  }

  public  function getWidgetUsersData($type, array $params)
  {
      // filter users 
      if($params['filter'])
        $w = array("u.status <> 'deactivated'");
      
      //if ($params['superDistributorID']) $w[] = 'u.superDistributorID = '.(int)$params['superDistributorID']; else $w[] = 'u.superDistributorID IS NULL';
      
      if ($params['searchvalue'])
      {
        $like = $this->db->quote('%' . $params['searchvalue'] . '%');
        $or = array(
          'u.userID', 
          'u.firstName', 
          'u.lastName',
          'u.email',
          'u.created', 
          'u.role', 
          );
        foreach ($or as $key => &$v) $v = "$v LIKE $like";
        $w[] = implode(' OR ', $or);
      }
      
      //if (strtolower($params['searchvalue']) == 'active') $w[] = 'u.statusID = 3';
      //elseif (strtolower($params['searchvalue']) == 'inactive') $w[] = 'u.statusID = 2';
      
      if($w) $where = ' WHERE ' . implode(' AND ', $w);
      switch ($type)
      {
        case 'count':
           return $this->db->col('SELECT COUNT(*) FROM Users AS u' . $where . $o . $limit);
        case 'rows':
        	$fields = array(
                    'u.userID',
                    'u.firstName',
                    'u.lastName',
                    'u.email',
                    'u.created',
                    'u.role',
        		);
        	$sortBy = $fields[ abs($params['sortBy']) - 1 ] . ($params['sortBy'] > 0 ? " ASC" : " DESC");
           $o = ' ORDER BY ' . $sortBy;
           $fields = implode(', ', $fields);
           $limit = ' LIMIT ' . ($params['pageSize'] * $params['pos']) . ', ' . $params['pageSize'];

           $rows = $this->db->rows("SELECT $fields  FROM Users AS u " . $where . $o . $limit);
           return $rows;
      }
    }
    
  
  /**
   * Hash the password, compared the hash to the hash stored in the db
   * and allow login if there is a match
   * @param type $email
   * @param type $password
   * @return int
   */
  public  function login($email, $password)
  {
    
    $dbPassword = $this->db->row('SELECT password, userID FROM Users WHERE email = ?', array($email));
    
    if ( \ClickBlocks\MVC\Backend\PageUsers::compareHashes($password, $dbPassword['password']) ) {
      return array('userID'=>$dbPassword['userID']);
    } else {
      return 0;
    }
    
  }

  public  function checkEmailExist($email)
  {
    return (int) $this->db->row('SELECT userID FROM Users WHERE email = ?', array($email));
  }

  public  function getUserInfo($userID)
  {
    return $this->db->row('SELECT * FROM Users WHERE userID = ?', array($userID));
  }

  public  function getUserDataByEmail($email)
  {
    return $this->db->row('SELECT * FROM Users WHERE email = ?', array($email));
  }

//  public  function deleteGroup($ids)
//  {
//    $ids = implode(',', array_filter($ids));
//    $this->db->execute("DELETE FROM Users WHERE userID IN ($ids) AND userID <> 1" );
//  }
  
  public  function deactivateUser($id)
  {
    $sql = "UPDATE Users SET email = NULL, password = 'accountdeactivated', status = 'deactivated' WHERE userID = ? ";
    $this->db->execute($sql, array($id));
  }
  
  public  function deactivateGroup($ids)
  {
    $ids = implode(',', array_filter($ids));
    $this->db->execute("UPDATE Users SET email = NULL, password = 'accountdeactivated', status = 'deactivated' WHERE userID IN ($ids) " );
  }


}

?>