<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraEngineDisplacements extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\EngineDisplacements');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\EngineDisplacements';
   }

  public  function getWidgetEngineData($type, array $params)
  {
      if($params['corporateEmployee'])
        $w = array('u.roleID <> '.USER_ROLE_ADMIN);
      //if ($params['superDistributorID']) $w[] = 'u.superDistributorID = '.(int)$params['superDistributorID']; else $w[] = 'u.superDistributorID IS NULL';
      if ($params['searchvalue'])
      {
        $like = $this->db->quote('%' . $params['searchvalue'] . '%');
        $or = array(
          'ed.EngineDisplacementsID', 
          'ed.mapID', 
          'ed.data',
          'ed.updated',
          'ed.updatedBy', 
          );
        foreach ($or as $key => &$v) $v = "$v LIKE $like";
        $w[] = implode(' OR ', $or);
      }
      if (strtolower($params['searchvalue']) == 'active') $w[] = 'u.statusID = 3';
      elseif (strtolower($params['searchvalue']) == 'inactive') $w[] = 'u.statusID = 2';
      if($w) $where = ' WHERE ' . implode(' AND ', $w);
      switch ($type)
      {
        case 'count':
           return $this->db->col('SELECT COUNT(*) FROM EngineDisplacements AS ed ');
        case 'rows':
        	$fields = array(
        	  'ed.EngineDisplacementsID', 
         	  'ed.mapID', 
          	  'ed.data',
          	  'ed.updated',
          	  'ed.updatedBy',
        	);
        	$sortBy = $fields[ abs($params['sortBy']) - 1 ] . ($params['sortBy'] > 0 ? " ASC" : " DESC");
           $o = ' ORDER BY ' . $sortBy;
           $fields = implode(', ', $fields);
           $limit = ' LIMIT ' . ($params['pageSize'] * $params['pos']) . ', ' . $params['pageSize'];

           $rows = $this->db->rows("SELECT $fields  FROM EngineDisplacements AS ed " . $where . $o . $limit);
           return $rows;
      }
    }

  public  function selectData($id)
  {
    $row = $this->db->row("SELECT * FROM EngineDisplacements WHERE mapID = $id ");
    return $row;
  }
}


?>