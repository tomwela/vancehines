<?php
/**
 * ClickBlocks.PHP v. 1.0
 *
 * Copyright (C) 2010  SARITASA LLC
 * http://www.saritasa.com
 *
 * This framework is free software. You can redistribute it and/or modify
 * it under the terms of either the current ClickBlocks.PHP License
 * viewable at theclickblocks.com) or the License that was distributed with
 * this file.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY, without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the ClickBlocks.PHP License
 * along with this program.
 *
 * Responsibility of this file: sqlbuilder.php
 *
 * @category   DB
 * @package    DB
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */
namespace ClickBlocks\DB;

use ClickBlocks\Core;

/**
 * The class is a helper-builder sql queries
 * Класс-помошник является построителем sql запросов 
 */
class SQLBuilder
{
   /**
    * array of options to connect to the database
    * массив с параметрами подключения к базе данных
    *
    * @var array 
    */
   private $dsn = array();

   /**
    * The class constructor
    * Sets the DSN
    * Конструктор класса
    * Устанавливает DSN
    * 
    * @param array $dsn
    * @access public
    */
   public function __construct(array $dsn)
   {
      $this->setDSN($dsn);
   }

   /**
    * records in the $ this->dsn database connection parameters
    * записывает в $this->dsn параметры подключения к базе
    * 
    * @param array $dsn
    * @access public
    */
   public function setDSN(array $dsn)
   {
      $this->dsn = $dsn;
   }

   /**
    * concludes the line in the spec. symbols corresponding to the syntax used by the DBMS
    * заключает строку в спец. символы, соответствующие синтаксису используемой СУБД
    * 
    * @param string $str
    * @return string
    * @access public
    */
   public function wrap($str)
   {
      switch ($this->dsn['engine'])
      {
         case 'mysql':
           return '`' . str_replace('`', '``', $str) . '`';
         case 'mssql':
           return '[' . strtr($str, array('[' => '[[', ']' => ']]')) . ']';
      }
   }

   /**
    * Generates an INSERT query in the chosen table $table with the selected data
    * Генерирует запрос INSERT в выбраную таблицу $table с выбранными данными
    * 
    * @param string $table
    * @param array $data
    * @return string
    * @access public
    */
   public function insert($table, array &$data)
   {
      if ($this->dsn['engine'] == 'mssql')
      {
         $keys = $values = $tmp = array();
         foreach ($data as $k => $v)
         {
            if (!$this->isSQL($v))
            {
               $values[] = (is_numeric($v) || $v == '') ? '?' : 'N?';
               $tmp[] = $v;
            }
            else
            {
               $values[] = $v;
               unset($data[$k]);
            }
            $keys[] = $this->wrap($k);
         }
         $data = $tmp;
      }
      else
      {
         $keys = $values = array();
         foreach ($data as $k => $v)
         {
            if (!$this->isSQL($v)) $values[] = ':' . $k;
            else
            {
               $values[] = $v;
               unset($data[$k]);
            }
            $keys[] = $this->wrap($k);
         }
      }
      $table = $this->wrap($table);
      $ikeys  = implode(', ', $keys);
      $ivalues= implode(', ', $values);
      $list = implode(', ',array_map(function($a,$b){return $a.'='.$b;}, $keys,$values));
      return "INSERT INTO $table ($ikeys) VALUES ($ivalues) ON DUPLICATE KEY UPDATE $list;";
   }

   /**
    * Generates an UPDATE query
    * Генерирует запрос UPDATE
    * 
    * @param string $table
    * @param array $data
    * @param string $where
    * @return string
    * @access public
    */
   public function update($table, array &$data, &$where = null)
   {
      if ($w = $this->joinWhere($where, 'AND')) $w = ' WHERE ' . $w;
      return 'UPDATE ' . $this->wrap($table) . ' SET ' . $this->joinSet($data) . $w;
   }

   /**
    * Generates a request to REPLACE
    * Генерирует запрос REPLACE
    * 
    * @param string $table
    * @param array $data
    * @param string $where
    * @return string
    * @access public
    */
   public function replace($table, array &$data, &$where = null)
   {
      if ($w = $this->joinWhere($where, 'AND')) $w = ' WHERE ' . $w;
      return 'REPLACE ' . $this->wrap($table) . ' SET ' . $this->joinSet($data) . $w;
   }

   /**
    * Generates DELETE request
    * Генерирует запрос DELETE 
    * 
    * @param string $table
    * @param string $where
    * @return string
    * @access public
    */
   public function delete($table, &$where = null)
   {
      if ($w = $this->joinWhere($where, 'AND')) $w = ' WHERE ' . $w;
      return 'DELETE FROM ' . $this->wrap($table) . $w;
   }

   /**
    * generates the proper query, depending on the DBMS.
    * Queries of type $type using, where necessary, the parameters $param
    * генерирует правильные запросы в зависимости от СУБД.
    * Запросы типа $type используя, где надо, параметры $param
    *
    * @param string $type
    * @param string|array $param
    * @return string
    * @access public 
    */
   public function getSQL($type, $param = null)
   {
      switch ($type)
      {
         case 'ShowDataBases':
           switch ($this->dsn['engine'])
           {
              case 'mysql':
                return 'SHOW DATABASES';
              case 'mssql':
                return 'EXEC sp_databases';
           }
           break;
         case 'ShowTables':
           switch ($this->dsn['engine'])
           {
              case 'mysql':
                return 'SHOW TABLES FROM ' . $this->wrap($this->dsn['dbname']);
              case 'mssql':
                return 'USE ' . $this->wrap($this->dsn['dbname']) . '; SELECT TABLE_NAME FROM INFORMATION_SCHEMA.Tables;';
           }
           break;
        case 'ShowFields':
           switch ($this->dsn['engine'])
           {
              case 'mysql':
                return 'SHOW FULL COLUMNS FROM ' . $this->wrap($param);
              case 'mssql':
                return "SELECT C.COLUMN_NAME AS Field, C.COLUMN_DEFAULT AS DefaultValue, C.DATA_TYPE AS Type, C.IS_NULLABLE AS isNullable, C.CHARACTER_MAXIMUM_LENGTH AS MaxLength, columnproperty(object_id(T.TABLE_NAME), C.COLUMN_NAME, 'IsIdentity') AS PK FROM INFORMATION_SCHEMA.Tables AS T
                        INNER JOIN INFORMATION_SCHEMA.Columns AS C ON T.TABLE_NAME = C.TABLE_NAME
                        WHERE T.TABLE_NAME = '" . str_replace("'", "''", $param) . "' ORDER BY C.ORDINAL_POSITION";
           }
           break;
         case 'ShowCreateOperator':
           switch ($this->dsn['engine'])
           {
              case 'mysql':
                return 'SHOW CREATE TABLE ' . $this->wrap($param);
              case 'mssql':
                return '';
           }
         case 'CreateTable':
           switch ($this->dsn['engine'])
           {
              case 'mysql':
                $ff = $pk = array();
                foreach ($param[1] as $k => $field)
                {
                   $ff[] = $this->getFieldDefinition($field);
                   if (isset($param[2][$k])) $pk[] = $this->wrap($field['name']);
                }
                if (count($pk)) $ff[] = ' PRIMARY KEY (' . implode(', ', $pk) . ')';
                return 'CREATE TABLE ' . $this->wrap($param[0]) . '(' . implode(', ', $ff) . ')ENGINE=' . (($param[3]) ? $param[3] : 'InnoDB') . ' CHARACTER SET \'' . (($param[4]) ? $param[4] : 'utf8') . '\'';
              case 'mssql':
                return '';
           }
           break;
         case 'AddField':
           switch ($this->dsn['engine'])
           {
              case 'mysql':
                return 'ALTER TABLE ' . $this->wrap($param[0]) . ' ADD COLUMN ' . $this->getFieldDefinition($param[1]);
              case 'mssql':
                return '';
           }
           break;
         case 'DeleteField':
           switch ($this->dsn['engine'])
           {
              case 'mysql':
                return 'ALTER TABLE ' . $this->wrap($param[0]) . ' DROP COLUMN ' . $this->wrap($param[1]);
              case 'mssql':
                return '';
           }
           break;
         case 'ChangeField':
           switch ($this->dsn['engine'])
           {
              case 'mysql':
                return 'ALTER TABLE ' . $this->wrap($param[0]) . ' CHANGE COLUMN ' . $this->wrap($param[1]) . ' ' . $this->getFieldDefinition($param[2]);
              case 'mssql':
                return '';
           }
           break;
         case 'Routins':
           switch ($this->dsn['engine'])
           {
             case 'mysql':
               return 'SELECT * FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = ?';
             case 'mssql':
                return '';
           }
           break;
         case 'ShowCreateProcedure':
           switch ($this->dsn['engine'])
           {
             case 'mysql':
               return 'SHOW CREATE PROCEDURE ' . $this->wrap($param);
             case 'mssql':
                return '';
           }
           break;
         case 'ShowCreateFunction':
           switch ($this->dsn['engine'])
           {
             case 'mysql':
               return 'SHOW CREATE FUNCTION ' . $this->wrap($param);
             case 'mssql':
                return '';
           }
           break;
      }
   }

   /**
    * generates the correct row in the query with the parameter SET, combining the conditions of the $ data array
    * генерирует корректную строку в запросе с параметром SET, объединяя условия из массива $data
    *
    * @param array $data
    * @return string 
    * @access public
    */
   public function joinSet(array &$data)
   {
      if ($this->dsn['engine'] == 'mssql')
      {
         $p = $tmp = array(); 
         $rows = $data;
         foreach ($rows as $k => $v)
         {
            if (!$this->isSQL($v))
            {
               $p[] = $this->wrap($k) . ((is_numeric($v) || $v == '') ? ' = ?' : ' = N?');
               $tmp[] = $v;
            }
            else
            {
               $p[] = $this->wrap($k) . ' = ' . $v;
               unset($data[$k]);
            }
         }
         $data = $tmp;
      }
      else
      {
         $p = array(); 
         $rows = $data;
         foreach ($rows as $k => $v)
         {
            if (!$this->isSQL($v)) $p[] = $this->wrap($k) . ' = :' . $k;
            else
            {
               $p[] = $this->wrap($k) . ' = ' . $v;
               unset($data[$k]);
            }
         }
      }
      return join(', ', $p);
   }

   /**
    * Generates a valid request WHERE depending on the selected database.
    * If $where - array combines using as a separator $ del
    * If $where - string, it returns
    * Генерирует корректный запрос WHERE в зависимости от выбранной СУБД.
    * Если $where - массив, объединяет используя в качестве разделителя $del
    * Если $where - строка, возвращает ее
    * 
    * @param array|string $where
    * @param string $del
    * @return string 
    * @access public
    */
   public function joinWhere(&$where, $del)
   {
      if ($this->dsn['engine'] == 'mssql')
      {
         if (is_array($where))
         {
            $p = $tmp = array(); $rows = $where;
            foreach ($rows as $k => $v)
            {
               if (is_array($v))
               {
                  $p[] = '(' . $this->joinWhere($v, ($del == 'AND') ? 'OR' : 'AND') . ')';
                  $tmp[] = $v;
               }
               else if (is_numeric($k))
               {
                  $p[] = $v;
                  $tmp[] = $v;
               }
               else if (!$this->isSQL($v))
               {
                  $tmp[] = $v;
                  $p[] = $this->wrap($k) . ((is_numeric($v) || $v == '') ? ' = ?' : ' = N?');
               }
               else
               {
                  $p[] = $this->wrap($k) . ' = ' . $v;
                  unset($where[$k]);
               }
            }
            $w = join(' ' . $del . ' ', $p);
            $where = $tmp;
         }
         else if (is_string($where)) $w = $where;
      }
      else
      {
         if (is_array($where))
         {
            $p = array(); $rows = $where;
            foreach ($rows as $k => $v)
            {
               if (is_array($v)) $p[] = '(' . $this->joinWhere($v, ($del == 'AND') ? 'OR' : 'AND') . ')';
               else if (is_numeric($k)) $p[] = $v;
               else if (!$this->isSQL($v)) $p[] = $this->wrap($k) . ' = :' . $k;
               else
               {
                  $p[] = $this->wrap($k) . ' = ' . $v;
                  unset($where[$k]);
               }
            }
            $w = join(' ' . $del . ' ', $p);
         }
         else if (is_string($where)) $w = $where;
      }
      return $w;
   }

   /**
    * Checks if the $value is SQL expression
    * Проверяет, является ли $value SQL выражением
    * 
    * @param string $str
    * @return bool 
    * @access public
    */
   public function isSQL($str)
   {
    return in_array(strtoupper($str), array('NULL', 'NOW()'));
      // if (is_array($value)) {
      //    $value = $value[0];
      //    return true;
      // }
      // return false;
   }

   /**
    * Depending on the $ type returns one value: string, int, float, dt
    * В зависимости от $type возвращает одно из значений: string, int, float, dt
    *
    * @param string $type
    * @return string 
    * @access public
    * @static
    */
   public static function getPHPDataType($type)
   {
      switch ($type)
      {
         case 'varchar':
         case 'char':
         case 'text':
         case 'tinytext':
         case 'mediumtext':
         case 'longtext':
         case 'tinyblob':
         case 'blob':
         case 'mediumblob':
         case 'longblob':
         case 'enum':
         case 'set':
         case 'binary':
         case 'varbinary':
           return 'string';
         case 'int':
         case 'tinyint':
         case 'smallint':
         case 'mediumint':
         case 'bigint':
         case 'year':
         case 'bit':
           return 'int';
         case 'decimal':
         case 'float':
         case 'double':
           return 'float';
         case 'time':
         case 'datetime':
         case 'date':
         case 'timestamp':
           return 'dt';
      }
   }

   /**
    * The function converts the array, depending on the key to the correct sql query
    * Функция преобразует элементы массива, в зависимости от ключа, в корректный sql запрос
    *
    * @param array $field
    * @return string 
    * @access private
    */
   private function getFieldDefinition($field)
   {
      $f = $this->wrap($field['name']);
      $f .= ' ' . strtoupper($field['type']);
      if (strlen($field['length'])) $f .= '(' . $field['length'] . (($field['precision']) ? ', ' . $field['precision'] : '') . ')';
      if ($field['unsigned']) $f .= ' UNSIGNED';
      if (!$field['null']) $f .= ' NOT NULL';
      if ($field['autoincrement']) $f .= ' AUTO_INCREMENT';
      if (strlen($field['default'])) $f .= ' DEFAULT \'' . addslashes($field['default']) . '\'';
      else if ($field['null']) $f .= ' DEFAULT NULL';
      return $f;
   }
}

?>