<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraEITMSof extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\EITMSof');
  }

  public  static  function getBLLClassName()
  {
      return '\ClickBlocks\DB\EITMSof';
  }

  public  function selectData($id)
  {
    return $this->db->couples("SELECT * FROM EITMSof WHERE mapID = $id ");
  }
}

?>