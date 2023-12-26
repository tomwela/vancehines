<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraEITMSon extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\EITMSon');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\EITMSon';
   }

  public  function selectData($id)
  {
    return $this->db->couples("SELECT * FROM EITMSon WHERE mapID = $id ");
  }
}

?>