<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraHistory extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\History');
  }
   
   public function saveCSRHistory($mapID, $customerID, $userID)
   {
   	if($mapID) 		$w[] = 'h.mapID = ' 	 . $this->db->quote($mapID);
   	if($customerID) $w[] = 'h.customerID = ' . $this->db->quote($customerID);
   	if($userID) 	$w[] = 'h.userID = ' 	 . $this->db->quote($userID);
   	if ($w) 
      $w = 'WHERE ' . implode(' AND ', $w);
   	$this->db->execute('UPDATE History as h SET h.date = NOW()' . $w);
   }

  public function getWidgetHistory($type, array $params)
  {   
    $w[] = "h.customerID = " . $params['customerID'];
    $where = " WHERE " . implode(',', $w);
    //$inner = " INNER JOIN History AS h ON c.customerID = h.customerID";
    $inner .= " INNER JOIN Users AS u ON u.userID = h.userID"; 
   	switch ($type)
    {
      case 'count':
        return $this->db->col('SELECT COUNT(customerID) FROM History AS h ' . $inner . $where);
      case 'rows':
        $fields = array(
            'concat(u.firstName, " ", u.lastName) as fullName',
            'h.date'
        );
   		$sortBy = (abs($params['sortBy']) == 2) ? 'h.date' : $fields[abs($params['sortBy'])];
   		$sortBy .= ($params['sortBy'] > 0 ? " DESC" : " ASC");
    	$o = ' ORDER BY ' . $sortBy;
    	$fields = implode(', ', $fields);
    	$limit = ' LIMIT ' . ($params['pageSize'] * $params['pos']) . ', ' . $params['pageSize'];    	

    	$rows = $this->db->rows("SELECT $fields FROM History AS h " . $inner . $where . $o . $limit);
    	return $rows;
	  }
  }
}

?>