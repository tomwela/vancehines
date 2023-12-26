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
 * Responsibility of this file: dbcore.php
 *
 * @category   DB
 * @package    DB
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */
namespace ClickBlocks\DB\Sync;

/**
 * Main class for all database operations.
 *
 * @abstract
 */
abstract class DBCore
{
  /**
   * Error message templates.
   */
  const ERR_DB_1 = 'DSN is wrong.';
  const ERR_DB_2 = 'Unknown DBMS driver.';
  
  /**
   * Database connection parameters.
   *
   * @var array $params
   * @access protected
   */
  protected $params = array();
  
  /**
   * Array of SQL query templates for different database operations.
   *
   * @var array $sql
   * @access protected
   */
  protected $sql = array();
  
  /**
   * Returns a class instance for database interaction based on the type of DBMS.
   *
   * @param string $dsn - the database connection DSN.
   * @param string $username - the username of establishing database connection.
   * @param string $password - the password of establishing database connection.
   * @param array $options - the options of establishing database connection.
   * @return ClickBlocks\DB\Sync\DBCore
   * @access public
   * @static
   */
  public static function getInstance($dsn, $username = null, $password = null, array $options = null)
  {
    $tmp = array();
    do
    {
      $dsn = get_cfg_var('pdo.dsn.' . $dsn) ?: $dsn;
      $tmp['dsn'] = $dsn;
      $dsn = explode(':', $dsn);
      $tmp['driver'] = strtolower($dsn[0]);
      if ($tmp['driver'] == 'uri') 
      {
        unset($dsn[0]);
        unset($dsn[1]);
        $dsn[2] = ltrim($dsn[2], '/');
        $dsn = file_get_contents(implode(':', $dsn));
      }
    }
    while ($tmp['driver'] == 'uri');
    if (empty($dsn[1])) throw new SynchronizerException(self::ERR_DB_1);
    $dsn = explode(';', $dsn[1]);
    foreach ($dsn as $v)
    {
      $v = explode('=', $v);
      $tmp[strtolower(trim($v[0]))] = trim($v[1]);
    }
    $tmp['username'] = $username;
    $tmp['password'] = $password;
    $tmp['options'] = $options;
    switch ($tmp['driver'])
    {
      case 'mysql': return new MySQLCore($tmp);
    }
    throw new SynchronizerException(self::ERR_DB_2);
  }
  
  /**
   * Constructor.
   *
   * @param array $params - database connection parameters.
   * @access private
   */
  private function __construct(array $params)
  {
    $this->params = $params;
  }
  
  /**
   * Returns the database connection parameters.
   *
   * @return array
   * @access public
   */
  public function getParameters()
  {
    return $this->params;
  }
  
  /**
   * Returns PDO object of relevant current database session.
   *
   * @return PDO
   * @access public
   */
  public function getPDO()
  {
    $pdo = new \PDO($this->params['dsn'], $this->params['username'], $this->params['password'], $this->params['options']);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    return $pdo;
  }
  
  /**
   * Returns SQL query.
   *
   * @param string $class - SQL query class.
   * @param string $type - SQL query type.
   * @param array $params - parameters of SQL query.
   * @return string
   * @access public
   */
  public function getSQL($class, $type, array $params = null)
  {
    if (!isset($this->sql[$class][$type])) return false;
    $sql = $this->sql[$class][$type];
    if ($params) $sql = strtr($sql, $params);
    return $sql;
  }
  
  /**
   * Returns DBReader class for the current DBMS.
   *
   * @return DBReader
   * @access public
   */
  public function getReader($isTest = false)
  {
    $class = __namespace__ . '\\' . $this->params['driver'] . ($isTest ? 'TestReader' : 'Reader');
    return new $class($this);
  }
  
  /**
   * Returns DBWriter class for the current DBMS.
   *
   * @return DBWriter
   * @access public
   */
  public function getWriter($isTest = false)
  {
    $class = __namespace__ . '\\'. $this->params['driver'] .($isTest ? 'TestWriter' : 'Writer');
    return new $class($this);
  }
  
  /**
   * Quotes a column name or table name for use in queries.
   *
   * @param string $name - column or table name.
   * @param boolean $isColumnName
   * @return string
   * @access public
   * @abstract
   */
  abstract public function wrap($name, $isColumnName = true);
  
  /**
   * Quotes a string value for use in queries.
   *
   * @param string $value
   * @param boolean $isLike - determines whether the quoting value is used in LIKE clause.
   * @return string
   * @access public
   * @abstract
   */
  abstract public function quote($value, $isLike = false);
}
