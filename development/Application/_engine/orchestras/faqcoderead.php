<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraFaqCodeRead extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\FaqCodeRead');
  }

public function faq(){
 return $this->db->rows("SELECT Question, Answer FROM FaqCodeRead ORDER BY ID ASC");
}


}

?>