<?php

namespace ClickBlocks\DB\Sync;

abstract class DBWriter implements IWriter
{
  protected $db =  null;
  
  protected $queries = array();
  
  public function __construct(DBCore $db)
  {
    $this->db = $db;
    $this->queries = array();
  }
  
  public function getQueries()
  {
    return $this->queries;
  }
  
  protected function setData(\PDO $pdo, $class, $type, array $params = null, $test = false)
  {
    $sql = $this->db->getSQL($class, $type, $params);
    $this->queries[] = $sql;
    echo "$sql;<br/>";
    if(!$test) $pdo->prepare($sql)->execute();
  }
}