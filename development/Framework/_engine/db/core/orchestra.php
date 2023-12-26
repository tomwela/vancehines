<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core;

class Orchestra
{
   const PROTOCOL_RAW = 0;
   const PROTOCOL_OBJECTS = 1;

   protected $objInfo = null;
   protected $orm = null;
   protected $db = null;
   protected $config = null;
   protected $className = null;
   protected $protocol = self::PROTOCOL_RAW;

   public function __construct($className)
   {
      $this->config = Core\Register::getInstance()->config;
      $this->orm = ORM::getInstance();
      $this->objInfo = $this->orm->getORMInfoObject();
      $this->className = $className;
      $this->db = foo(new SQLGenerator())->getDBByClassName($this->className);
   }

   public function getDB()
   {
      return $this->db;
   }
   
   public function getProtocol()
   {
      return $this->protocol;
   }

   public function setProtocol($protocol)
   {
      $this->protocol = $protocol;
      return $this;
   }

   public function getAll($start = null, $limit = null, $method = 'rows', $fields = array())
   {
      return $this->getByQuery(null, $start, $limit, $method, $fields);
   }
   
   public function getFirst($method = 'row', $fields = array())
   {
      return $this->getByQuery(null, null, null, $method, $fields);
   }
   
   public function getAllCouples($start = null, $limit = null, $fields = array())
   {
      return $this->getAll($start, $limit, 'couples', $fields);
   }   

   public function getByQuery($where = null, $start = null, $limit = null, $method = 'rows', $fields = array(), $group = null)
   {
      $sql = foo(new SQLGenerator())->getByQuery($this->className, ($where), $start, $limit, $fields, $group);
      return $this->getDataByProtocol($sql, array(), $method);
   }

   protected function getDataByProtocol($sql, array $params = array(), $method = 'rows')
   {
      $data = call_user_func_array(array($this->db, $method), array($sql, $params));
      if ($this->protocol == self::PROTOCOL_OBJECTS)
      {
         if ($method == 'rows')
         {
            foreach ($data as $k => $row)
              $rows[$k] = foo(new $this->className())->assign($row);
            $data = $rows;
         }
         elseif ($method == 'row')
         {
            $data = foo(new $this->className())->assign($data);
         }
      }
      return $data;
   }

   public function retrieveEntries(array $data)
   {
      $w = $p = array();
      if (is_array($data) && count($data))
      {
         $keys = array_keys($data);
         foreach($keys as $k)
         {
            $w[] = $k.' = :'.$k;
            $p[$k] = $data[$k];
         }
         if (count($w)) $w = ' WHERE ' . implode(' AND ', $w);
         else $w = '';
         $sql = 'SELECT * FROM '.foo(new $this->className())->getTableAlias();
         $result = $this->db->rows($sql . $w, $p);
         foreach($result as $value)
         {
            $tableRows[] = foo(new $this->className())->assign($value);
         }
         if(count($tableRows))
         {
            return $tableRows;
         }
      }
   }
}

?>
