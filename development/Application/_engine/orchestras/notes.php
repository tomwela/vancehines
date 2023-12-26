<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraNotes extends Orchestra
{
    public  function __construct()
    {
      parent::__construct('\ClickBlocks\DB\Notes');
    }

  public  function getWidgetMapNotesData($type, array $params)
  {
    if ($params['searchvalue'])
      $w[] = ' (u.firstName LIKE ' . $this->db->quote('%' . $params['searchvalue'] . '%') . ' OR u.lastName LIKE ' . $this->db->quote('%' . $params['searchvalue'] . '%') . ' OR n.note LIKE ' . $this->db->quote('%' . $params['searchvalue'] . '%') . ') ';
    if ($params['mapID'])
    {
      $w[] = " n.mapID = {$params['mapID']} ";
    }
    if ($w)
      $where = ' WHERE ' . implode(' AND ', $w);
    switch ($type)
    {
      case 'count':
        return $this->db->col('SELECT COUNT(*) FROM Notes AS n INNER JOIN Users u ON u.userID = n.createdBy ' . $where);

      case 'rows':
        $fields = array(
            'n.note',
            'LOWER(DATE_FORMAT(n.updated,"%m/%d/%Y %h:%i %p")) AS createdDate',
            'CONCAT(u.firstName, " ", u.lastName) as userName',
        );
        // Special case
        $sortBy = (abs($params['sortBy']) == 2) ? 'createdDate' :
            (abs($params['sortBy']) == 3) ? 'userName' :
                $fields[abs($params['sortBy']) - 1];
        $sortBy .= ($params['sortBy'] > 0 ? " ASC" : " DESC");
        $o = ' ORDER BY ' . $sortBy;
        $fields = implode(', ', $fields);
        $limit = ' LIMIT ' . ($params['pageSize'] * $params['pos']) . ', ' . $params['pageSize'];
        $rows = $this->db->rows("SELECT n.noteID, $fields  FROM Notes AS n INNER JOIN Users u ON u.userID = n.createdBy" . $where . $o . $limit);
        return $rows;
    }
  }

  public  function getWidgetAllMapNotesData($type, array $params)
  {

    if($params['customerID'])
    $w[] = " n.mapID IN ( SELECT m.mapID FROM Maps m WHERE m.customerID = {$params['customerID']} ) ";


    if ($params['searchvalue'])
      $w[] = ' (u.firstName LIKE ' . $this->db->quote('%' . $params['searchvalue'] . '%') . ' OR u.lastName LIKE ' . $this->db->quote('%' . $params['searchvalue'] . '%') . ' OR n.note LIKE ' . $this->db->quote('%' . $params['searchvalue'] . '%') . ') ';


    //if ($params['mapID'])
      //$w[] = " n.mapID = {$params['mapID']} ";


    if ($w)
      $where = ' WHERE ' . implode(' AND ', $w);


    switch ($type)
    {
      case 'count':
        return $this->db->col('SELECT COUNT(*) FROM Notes AS n INNER JOIN Users u ON u.userID = n.createdBy ' . $where);

      case 'rows':
        $fields = array(
            'n.note',
            'LOWER(DATE_FORMAT(n.updated,"%m/%d/%Y %h:%i %p")) AS createdDate',
            'CONCAT(u.firstName, " ", u.lastName) as userName',
        );
        // Special case
        $sortBy = (abs($params['sortBy']) == 2) ? 'createdDate' :
            (abs($params['sortBy']) == 3) ? 'userName' :
                $fields[abs($params['sortBy']) - 1];
        $sortBy .= ($params['sortBy'] > 0 ? " ASC" : " DESC");
        $o = ' ORDER BY ' . $sortBy;
        $fields = implode(', ', $fields);
        $limit = ' LIMIT ' . ($params['pageSize'] * $params['pos']) . ', ' . $params['pageSize'];
        $rows = $this->db->rows("SELECT n.mapID, n.noteID, $fields  FROM Notes AS n INNER JOIN Users u ON u.userID = n.createdBy" . $where . $o . $limit);

        //print_r("SELECT n.mapID, n.noteID, $fields  FROM Notes AS n INNER JOIN Users u ON u.userID = n.createdBy" . $where . $o . $limit);
        return $rows;
    }
  }


}

?>