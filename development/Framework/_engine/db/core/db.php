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
 * Responsibility of this file: db.php
 *
 * @category   DB
 * @package    DB
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */
namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

interface IDB
{
   public function execute($sql, array $data = array(), $type = DB::DB_EXEC, $style = \PDO::FETCH_BOTH);
   public function insert($table, array $data);
   public function replace($table, array $data, $where = null);
   public function update($table, array $data, $where = null);
   public function delete($table, $where = null);
   public function row($sql, array $data = array(), $style = \PDO::FETCH_ASSOC);
   public function rows($sql, array $data = array(), $style = \PDO::FETCH_ASSOC);
   public function col($sql, array $data = array());
   public function cols($sql, array $data = array());
   public function couples($sql, array $data = array());

}

/**
 * Class to work with the database
 * Класс для работы с базой данных
 * 
 */
class DB implements IDB
{
   const DB_EXEC    = 0;
   const DB_COLUMN  = 1;
   const DB_COLUMNS = 2;
   const DB_ROW     = 3;
   const DB_ROWS    = 4;
   const DB_COUPLE  = 5;
   const DB_COUPLES = 6;
   const DB_DELETE  = 7;

   private $pdo = null;
   private $sql = null;
   private $dsn = array();
   private $error = array();

   protected $reg = null;
   protected $cachedSQL = array();
   protected static $statistic = array();

   public $expire = 900;
   public $cached = false;
   public $catchException = false;
   public $affectedRows = null;
   
   /**
    * The class constructor.
    * конструктор класса.
    * 
    * @access public
    */
   public function __construct()
   {
      $this->reg = Core\Register::getInstance();
   }

   /**
    * returns the current instance of the database connection or throws an error
    * возвращает текущий экземпляр класса соединения с БД или выдает ошибку
    * 
    * @param string $param
    * @return object
    * @throws \Exception 
    * @access public
    */
   public function __get($param)
   {
      if ($param == 'sql') return $this->sql;
      if ($param == 'pdo') return $this->pdo;
      throw new \Exception(err_msg('ERR_GENERAL_3', array($param, get_class($this))));
   }

   /**
    * Sets the database connection
    * Устанавливает параметры подключения к БД
    * 
    * @param string $dsn
    * @param string $username
    * @param string $password
    * @param array $options
    * @access public
    */
   public function connect($dsn, $username, $password, $options = null)
   {
      $this->parseDSN($dsn);
      $this->pdo = new \PDO($dsn, $username, $password, $options);
      $this->sql = new SQLBuilder($this->dsn);
   }
   
   /**
    * Removes all connection settings
    * Удаляет все параметры подключения
    * 
    * @access public
    */
   public function disconnect()
   {
      $this->pdo = $this->sql = null;
      $this->dsn = array();
   }

   /**
    * get name of the database
    * получаем имя БД
    * 
    * @return string
    * @access public
    */
   public function getDataBaseName()
   {
      return $this->dsn['dbname'];
   }

   /**
    * get host
    * 
    * @return string
    * @access public
    */
   public function getHost()
   {
      return $this->dsn['host'];
   }

   /**
    * get port
    * 
    * @return string
    * @access public
    */
   public function getPort()
   {
      return $this->dsn['port'];
   }

   /**
    * get engine
    * 
    * @return string
    * @access public
    */
   public function getEngine()
   {
      return $this->dsn['engine'];
   }

   /**
    * Get an array dsn
    * Получить массив dsn
    * 
    * @return string
    * @access public
    */
   public function getDSN()
   {
      return $this->dsn;
   }

   /**
    * get the last error message
    * поkeчить последнее уведомление об ошибке
    * 
    * @return array
    * @access public
    */
   public function getLastError()
   {
      return $this->error;
   }

   /**
    * Get statistics
    * 
    * @return array
    * @access public
    * @static
    */
   public static function getStatistic()
   {
      return self::$statistic;
   }

   /**
    * Adds special characters to the string $str
    * Добавляет специальные символы к строке $str
    * 
    * @param string $str
    * @return string
    * @access public
    */
   public function wrap($str)
   {
      return $this->sql->wrap($str);
   }

   /**
    * Escaping special characters
    * Экранирование спец символов
    * 
    * @param string $str
    * @return string
    * @access public
    */
   public function quote($str)
   {
      return $this->pdo->quote($str);
   }

   /**
    * Executes the query $sql into the database. Additional parameters specify how to display
    * Выполняет запрос $sql в базу данных. Дополнительные параметры задают способ вывода
    * 
    * @param string $sql
    * @param array $data
    * @param int $type 
    * @param int $style
    * @return mixed
    * @access public
    */
   public function execute($sql, array $data = array(), $type = self::DB_EXEC, $style = \PDO::FETCH_BOTH)
   {
      if ($this->cached && $this->reg->cache instanceof Cache\ICache && $type != self::DB_EXEC)
      {
         $key = strtr($sql, $data);
         $flag = $this->parseCachedSQL($sql);
         if ($flag && $this->reg->cache->isExists($key)) return $this->reg->cache->get($key);
      }
      $logger = Core\Logger::getInstance();
      $logger->pStart('db_sql_log');
      $st = $this->pdo->prepare($sql);
      if (!$st->execute($data))
      {
         $this->error = $st->errorInfo();
         if ($this->catchException) throw new \Exception($this->error[2]);
         else
         {
            self::$statistic[$this->dsn['dsn']][] = array('sql' => $sql, 'data' => $data, 'type' => $type, 'style' => $style, 'time' => $time, 'datetime' => date('Y-m-d H:i:s'));
            Core\Debugger::exceptionHandler(new \Exception($this->error[2]."<br/><pre>SQL: $sql</pre><br/>".print_r($data, true)."<br/>"), Core\Logger::LOG_CATEGORY_SQL_EXCEPTION);
            exit;
         }
      }
      $this->affectedRows = $st->rowCount();
      try {throw new \Exception();}
      catch (\Exception $e) {$stack = $e->getTraceAsString();}
      switch ($type)
      {
         case self::DB_EXEC:
           $res = $this->affectedRows;
           break;
         case self::DB_COLUMN:
           $res = $st->fetchColumn();
           if (is_array($res)) $res = end($res);
           break;
         case self::DB_COLUMNS:
           $res = array(); while ($row = $st->fetch($style)) $res[] = array_shift($row);
           break;
         case self::DB_ROW:
           $res = $st->fetch($style);
           if ($res === false) $res = array();
           break;
         case self::DB_ROWS:
           $res = $st->fetchAll($style);
           break;
         case self::DB_COUPLE:
           $rows = $st->fetchAll(\PDO::FETCH_NUM);
           $res = array();
           if ($c=count($rows[0]))
            foreach ($rows as $v)
            {
              if($c>2) $res[$v[0]]=$v;
              else     $res[$v[0]]=$v[1];
            }
           break;
         case self::DB_COUPLES:
           $rows = $st->fetchAll($style);
           $res = array();
           if (is_array($rows[0]))
            foreach ($rows as $v)
            {
              $res[reset($v)][]=$v;
            }
           break;           
      }
      $time = $logger->pStop('db_sql_log');
      if($this->reg->config->debug['db']) \ClickBlocks\Web\Ajax::getInstance()->consolelog($sql, $time, $data);
      self::$statistic[$this->dsn['dsn']][] = array('sql' => $sql, 'data' => $data, 'type' => $type, 'style' => $style, 'time' => $time, 'datetime' => date('Y-m-d H:i:s'), 'affectedRows' => $this->affectedRows, 'stack' => $stack,'res'=> $res);
      if ($this->cached && $this->reg->cache instanceof Cache\ICache && $type != self::DB_EXEC && $flag) $this->reg->cache->set($key, $res, $this->expire);
      return $res;
   }

   public function couplerows($sql, array $data = array())
   {
      return $this->execute($sql, $data, self::DB_COUPLES, $style = \PDO::FETCH_ASSOC);
   }
   /**
    * Returns the ID of the last inserted row
    * Возвращает идентификатор последней вставленной строки
    * 
    * @param string $table
    * @param array $data
    * @return integer
    * @access public
    */
   public function insert($table, array $data)
   {
      $this->execute($this->sql->insert($table, $data), $data, self::DB_EXEC);
      if ($this->getEngine() == 'mssql') return $this->col('SELECT @@IDENTITY');
      return $this->pdo->lastInsertId();
   }

   /**
    * update the data in the table $table using the condition $where
    * обновить данные в таблице $table используя условие $where
    * 
    * @param string $table
    * @param array $data
    * @param array $where
    * @return integer
    * @access public
    */
   public function update($table, array $data, $where = null)
   {
      return $this->execute($this->sql->update($table, $data, $where), array_merge($data, (array)$where), self::DB_EXEC);
   }

   /**
    * replace data in a table using the condition $ where
    * Returns the ID of the last inserted row
    * заменить данные в таблице используя условие $where
    * Возвращает идентификатор последней вставленной строки
    * 
    * @param string $table
    * @param array $data
    * @param array $where
    * @return integer
    * @access public
    */
   public function replace($table, array $data, $where = null)
   {
      $this->execute($this->sql->replace($table, $data, $where), array_merge($data, (array)$where), self::DB_EXEC);
      if ($this->getEngine() == 'mssql') return $this->col('SELECT @@IDENTITY');
      return $this->pdo->lastInsertId();
   }

   /**
    * delete data from tables using the condition
    * удалить данные из таблицы используя условие
    * 
    * @param string $table
    * @param array $where
    * @return integer
    * @access public
    * 
    */
   public function delete($table, $where = null)
   {
      return $this->execute($this->sql->delete($table, $where), (array)$where, self::DB_EXEC);
   }

   /**
    * This will return the first value of the first column of results
    * Вернет первое значение первого столбца результата
    * 
    * @param string $sql
    * @param array $data
    * @return string
    * @access public
    */
   public function col($sql, array $data = array())
   {
      return $this->execute($sql, $data, self::DB_COLUMN);
   }

   /**
    * Returns an array whose keys - numeric, and the values ​​are the values ​​of the first column of results.
    * Вернет массив, ключи которого - числовые, а значения являются значениями первого столбца результата.
    * 
    * @param string $sql
    * @param array $data
    * @return array
    * @access public
    */
   public function cols($sql, array $data = array())
   {
      return $this->execute($sql, $data, self::DB_COLUMNS);
   }

   /**
    * returns the first row of the result of the received request
    * возвращает первую строку результата из полученного запроса 
    * 
    * @param string $sql
    * @param array $data
    * @param int $style
    * @return array
    * @access public
    * 
    */
   public function row($sql, array $data = array(), $style = \PDO::FETCH_ASSOC)
   {
      return $this->execute($sql, $data, self::DB_ROW, $style);
   }

   /**
    * Returns an array with the keys used in the names of the columns
    * Вернет массив с ключами, используемыми в названиях столбцов
    * 
    * @param string $sql
    * @param array $data
    * @param int $style
    * @return array
    * @access public
    */
   public function rows($sql, array $data = array(), $style = \PDO::FETCH_ASSOC)
   {
      return $this->execute($sql, $data, self::DB_ROWS, $style);
   }

   /**
    * Returns an array with numeric keys
    * Вернет массив с числовыми ключами
    * 
    * @param string $sql
    * @param array $data
    * @return array
    * @access public
    */
   public function couples($sql, array $data = array())
   {
      return $this->execute($sql, $data, self::DB_COUPLE);
   }

   /**
    * Get an array with all the data in Table
    * Получить массив со всеми данными по таблице
    * 
    * @param string $table
    * @return array
    * @access public
    */
   public function getFields($table)
   {
      $rows = $this->rows($this->sql->getSQL('ShowFields', $table));
      switch ($this->getEngine())
      {
         case 'mssql':
           return $rows;
         case 'mysql':
           $fields = array();
           foreach ($rows as $row)
           {
              preg_match('/.+\(([\d\w,\']+)\)/U', $row['Type'], $arr);
              $field = $row['Field'];
              $fields[$field]['Field'] = $row['Field'];
              $fields[$field]['PK'] = intval(($row['Key'] == 'PRI'));
              $fields[$field]['isNullable'] = (int)($row['Null'] != 'NO');
              $fields[$field]['isAutoIncrement'] = ($row['Extra'] == 'auto_increment') ? 1 : null;
              $fields[$field]['Type'] = $type = preg_replace('/\([\d\w,\']+\)/U', '', $row['Type']);
              $fields[$field]['DefaultValue'] = ($type == 'bit') ? substr($row['Default'], 2, 1) : $row['Default'];
              $fields[$field]['MaxLength'] = 0;
              $fields[$field]['Precision'] = 0;
              if (substr($type, -8) == 'unsigned')
              {
                 $fields[$field]['Type'] = trim(substr($type, 0, -8));
                 $fields[$field]['isUnsigned'] = 1;
              }
              else $fields[$field]['isUnsigned'] = 0;
              $arr = explode(',', $arr[1]);
              if ($type == 'enum' || $type == 'set') $fields[$field]['Set'] = $arr;
              else
              {
                 $fields[$field]['Set'] = null;
                 if (count($arr) == 1) $fields[$field]['MaxLength'] = $arr[0];
                 else
                 {
                    $fields[$field]['MaxLength'] = $arr[0];
                    $fields[$field]['Precision'] = $arr[1];
                 }
              }
           }
           return $fields;
      }
   }

   /**
    * get an array with the names of all tables
    * получить массив с именами всех таблиц
    * 
    * @return array 
    * @access public
    */
   public function getTables()
   {
      return $this->cols($this->sql->getSQL('ShowTables'));
   }

   /**
    * Returns the request to create the table $table
    * Возвращает запрос на создание таблицы $table
    * 
    * @param string $table
    * @return string
    * @access public
    */
   public function getCreateOperator($table)
   {
      $row = $this->row($this->sql->getSQL('ShowCreateOperator', $table), array(), \PDO::FETCH_NUM);
      return $row[1];
   }
   
   public function getRoutine($routine, $isFunction = false)
   {
     $row = $this->row($this->sql->getSQL($isFunction ? 'ShowCreateFunction' : 'ShowCreateProcedure', $routine));
     $def = $row[$isFunction ? 'Create Function' : 'Create Procedure'];
     $tmp = array();
     if ($isFunction) preg_match('/FUNCTION `' . $routine . '`\((.*)\) RETURNS/', $def, $match);
     else preg_match('/PROCEDURE `' . $routine . '`\((.*)\)/', $def, $match);
     $match = trim($match[1]);
     if (strlen($match)) foreach (explode(',', $match) as $param)
     {
       $param = explode('`', $param);
       $tmp[$param[1]] = strtolower(trim($param[2]));
     }
     return array('definition' => $def, 'args' => $tmp);
   }
   
   public function getRoutines($dbName = null)
   {
     $rows = $this->rows($this->sql->getSQL('Routins'), array($dbName !== null ? $dbName : $this->dsn['dbname']));
     foreach ($rows as &$row) $row['ROUTINE_DEFINITION'] = $this->getRoutine($row['ROUTINE_NAME'], $row['ROUTINE_TYPE'] == 'FUNCTION');
     switch ($this->getEngine())
     {
       case 'mysql':
         $tmp = array();
         foreach ($rows as $k => $rw)
         {
           $tmp[$k]['name'] = $rw['ROUTINE_NAME'];
           $tmp[$k]['type'] = $rw['ROUTINE_TYPE'];
           $tmp[$k]['definition'] = $rw['ROUTINE_DEFINITION']['definition'];
           $tmp[$k]['args'] = $rw['ROUTINE_DEFINITION']['args'];
           $tmp[$k]['comment'] = $rw['ROUTINE_COMMENT'];
         }
         return $tmp;
     }
     return $rows;
   }

   /**
    * The query returns an array with the names of all databases
    * Запрос вернет массив с именами всех баз данных
    * 
    * @return array
    * @access public
    */
   public function getDataBases()
   {
      return $this->cols($this->sql->getSQL('ShowDataBases'));
   }

   /**
    * create a table with the given settings
    * создает таблицу с полученными установками
    * 
    * @param string $name
    * @param array $fields
    * @param string $pk
    * @param string $engine
    * @param string $charset
    * @return 
    * @access public
    */
   public function createTable($name, array $fields, array $pk = null, $engine = null, $charset = 'utf8')
   {
      return $this->execute($this->sql->getSQL('CreateTable', array($name, $fields, $pk, $engine, $charset)));
   }

   /**
    * add a field to the table. request "ADD COLUMN"
    * Добавить поле в таблицу. Запрос "ADD COLUMN"
    * 
    * @param string $table
    * @param array $params
    * @return mixed
    * @access public
    */
   public function addField($table, array $params)
   {
      return $this->execute($this->sql->getSQL('AddField', array($table, $params)));
   }

   /**
    * remove the field from the table.. request "DROP COLUMN"
    * Удалить поле из таблицы. Запрос "DROP COLUMN"
    * 
    * @param string $table
    * @param string $field
    * @return mixed
    * @access public
    */
   public function deleteField($table, $field)
   {
      return $this->execute($this->sql->getSQL('DeleteField', array($table, $field)));
   }

   /**
    * change the field in the table. request "CHANGE COLUMN"
    * изменить поле в таблице. запрос "CHANGE COLUMN"
    * 
    * @param string $table
    * @param string $field
    * @param array $params
    * @return mixed
    * @access public
    */
   public function changeField($table, $field, array $params)
   {
      return $this->execute($this->sql->getSQL('ChangeField', array($table, $field, $params)));
   }

   /**
    * add the sql query and specify whether you want to cache
    * добавляем sql запрос и указываем, надо ли его кэшировать
    * 
    * @param string $sql
    * @param bool $isCached
    * @access public
    */
   public function addSQL($sql, $isCached = true)
   {
      $this->cachedSQL[$sql] = $isCached;
   }

   /**
    * remove the SQL query
    * удаляем SQL запрос
    * 
    * @param string $sql
    * @access public
    */
   public function deleteSQL($sql)
   {
      unset($this->cachedSQL[$sql]);
   }

   /**
    * check whether the query cached
    * проверяем, кэширован ли запрос
    * 
    * @param string $sql
    * @return bool
    * @access protected
    */
   protected function parseCachedSQL(&$sql)
   {
      foreach ($this->cachedSQL as $s => $isCached)
      {
         $flag = preg_match('@' . $s . '@isU', $sql);
         if ($isCached) if ($flag) {$sql = $s; return true;}
         else if ($flag) return false;
      }
      return true;
   }

   /**
    * extract the data connection from dsn
    * извлекаем данные подключения из dsn
    * 
    * @param string $dsn
    * @access public
    */
   private function parseDSN($dsn)
   {
      $this->dsn = array('dsn' => $dsn);
      $dsn = explode(':', $dsn);
      $this->dsn['engine'] = ($dsn[0] == 'dblib') ? 'mssql' : $dsn[0];
      $dsn = explode(';', $dsn[1]);
      foreach ($dsn as $v)
      {
         $v = explode('=', $v);
         $this->dsn[strtolower(trim($v[0]))] = trim($v[1]);
      }
   }
}

?>