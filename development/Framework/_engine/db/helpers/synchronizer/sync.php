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
 * Responsibility of this file: sync.php
 *
 * @category   DB
 * @package    DB
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */

namespace ClickBlocks\DB\Sync;

/**
 * Interface for all classes reading database structure.
 */
interface IReader
{
  /**
   * Sets the regular expression for the names of the tables, containing synchronizing data.
   *
   * @param string $pattern - the regular expression.
   * @access public
   */
  public function setInfoTables($pattern);
  
  /**
   * Returns the regular expression for detecting the tables, containing synchronizing data.
   * 
   * @return string
   * @access public
   */
  public function getInfoTables();

  /**
   * Resets the received data of the database structure. 
   * Repetitive call of the "read" method will allow to receive up-to-date information concerning database structure.
   *
   * @return self
   * @access public
   */
  public function reset();
  
  /**
   * Reads database structure.
   *
   * @return array - you can find the format of returned array in file synchronizer/structure_db.txt
   * @access public
   */
  public function read();
}

/**
 * Interface for all classes that implement changes in database structure.
 */
interface IWriter
{
  /**
   * Makes changes in database structure and information tables data.
   * If changes were made in db structure then method returns the array of executed SQL queries.
   *
   * @param array $info - the array returned by method Synchronizer::compare.
   * @return array | NULL
   * @access public
   */
  public function write(array $info);
}

/**
 * Exception class for the synchronizer.
 */
class SynchronizerException extends \Exception {}

/**
 * Main class for database structure synchronization.
 */
class Synchronizer
{
  /**
   * Error message templates.
   */
  const ERR_SYNC_1 = 'Source data provider is not specified.';
  const ERR_SYNC_2 = 'Destination data provider is not specified.';
  const ERR_SYNC_3 = 'It is impossible to synchronize databases with different DBMS';
  protected $core = null;
  /**
   * The structure of the source database.
   *
   * @var array $out - you can find the format of this array in file synchronizer/structure_db.txt
   * @access protected
   */
  protected $out = null;
  
  /**
   * The recipient database structure.
   *
   * @var array $in - you can find the  format of this array in file synchronizer/structure_db.txt
   * @access protected
   */
  protected $in = null;
  
  /**
   * Regular expression for detecting the tables, containing synchronizing data.
   *
   * @var string $infoTablesPattern
   * @access protected
   */
  protected $infoTablesPattern = null;
  
  /**
   * Parameters of connection to the recipient database. 
   *
   * @var array $params
   * @access protected
   */
  protected $params = array();
  
  /**
   * Sets the regular expression for the names of the tables, containing synchronizing data.
   *
   * @param string $pattern - the regular expression.
   * @access public
   */
  public function setInfoTables($pattern)
  {
    $this->infoTablesPattern = $pattern;
    return $this;
  }
  
  /**
   * Returns the regular expression for detecting the tables, containing synchronizing data.
   * 
   * @return string
   * @access public
   */
  public function getInfoTables()
  {
    return $this->infoTablesPattern;
  }
  
  /**
   * Reads database structure from the source of changes. It can be any database or the vault.
   * If the first argument of this method is file path to the vault then the method reads db structure from the vault.
   * Otherwise the methods reads structure from a database.
   *
   * @param string $vaultordsn - the file path or database connection DSN.
   * @param string $username - the username of establishing database connection.
   * @param string $password - the password of establishing database connection.
   * @param array $options - the options of establishing database connection.
   * @return self
   * @access public
   */
  public function out($vaultordsn, $username = null, $password = null, array $options = null)
  {
    if ($username === null) $reader = new VaultReader($vaultordsn);
    else 
    {
      $this->core = DBCore::getInstance($vaultordsn, $username, $password, $options);
      $reader = $this->core->getReader();
    }
    $reader->setInfoTables($this->infoTablesPattern);
    $this->out = $reader->read();
    return $this;
  }
  
  /**
   * Reads database structure from the recipient of changes. It can be any database or the vault.
   * If the first argument of this method is file path to the vault then the method reads db structure from the vault.
   * Otherwise the methods reads structure from a database.
   *
   * @param string $vaultordsn - the file path or database connection DSN.
   * @param string $username - the username of establishing database connection.
   * @param string $password - the password of establishing database connection.
   * @param array $options - the options of establishing database connection.
   * @return self
   * @access public
   */
  public function in($vaultordsn, $username = null, $password = null, array $options = null)
  {
    if ($username === null) $reader = new VaultReader($vaultordsn);
    else 
    {
      $this->core = DBCore::getInstance($vaultordsn, $username, $password, $options);
      $reader = $this->core->getReader();
    }
    $reader->setInfoTables($this->infoTablesPattern);
    $this->params = array('vaultordsn' => $vaultordsn, 'username' => $username, 'password' => $password, 'options' => $options);
    $this->in = $reader->read();
    return $this;
  }
  
  /**
   * Performs synchronization.
   * If changes were made in db structure then method returns the array of executed SQL queries. 
   *
   * @param boolean $merge - determines whether or not the merge of db structures is needed.
   * @return array | NULL
   * @access public
   */
  public function sync($merge = false, $test = false)
  {
    if ($this->out === null) throw new SynchronizerException(self::ERR_SYNC_1);
    if ($this->in  === null) throw new SynchronizerException(self::ERR_SYNC_2);
    
    if ($this->params['username'] === null) $writer = new VaultWriter($this->params['vaultordsn']);
    else 
    {
      $core   = DBCore::getInstance($this->params['vaultordsn'], $this->params['username'], $this->params['password'], $this->params['options']);      
      $writer = $core->getWriter();
    }
    $writer->write($this->compare($this->out, $this->in, $merge), $test);
    if($this->params['username'] === null) 
    {
      $this->core->getWriter()->write($this->compare($this->out, $this->in, $merge), true);
    }
  }

  /**
   * Compares two database structures.
   *
   * @param array $d1 - the source db structure.
   * @param array $d2 - the recipient db structure.
   * @param boolean $merge - determines whether or not the merge of db structures is needed.
   * @return array - you can find format of returned array in file synchronizer/structure_compare.txt
   * @access protected
   */
  protected function compare(array $d1, array $d2, $merge)
  {
    if (count($d2) == 0) return array('insert' => array(), 'update' => $d1, 'delete' => array(), 'data'=>array());
    if ($d1['meta']['driver'] != $d2['meta']['driver']){print_p(array($d1['meta'],$d2['meta']));return array('insert' => array(), 'update' => $d1, 'delete' => array(), 'data'=>array());};// throw new SynchronizerException(self::ERR_SYNC_3);
    $update = array();
    $insert = array('tables' => array(), 'columns' => array(), 'indexes' => array(), 'constraints' => array(), 'triggers' => array());
    $delete = array('triggers' => array(), 'constraints' => array(), 'indexes' => array(), 'columns' => array(), 'tables' => array());
    if ($d1['meta']['charset'] != $d2['meta']['charset'] || $d1['meta']['collation'] != $d2['meta']['collation']) $update['meta'] = $d1['meta'];
    if ($tmp = array_diff_key($d1['tables'], $d2['tables'])) $insert['tables'] = $tmp;
    if (!$merge && $tmp = array_diff_key($d2['tables'], $d1['tables'])) $delete['tables'] = $tmp;
    if ($tables = array_intersect_key($d1['tables'], $d2['tables']))
    {
      foreach ($tables as $table => $tb1)
      {
        $tb2 = $d2['tables'][$table];
        if ($tb1['meta'] != $tb2['meta']) $update['tables'][$table]['meta'] = $tb1['meta'];
        if ($tmp = array_diff_key($tb1['columns'], $tb2['columns'])) $insert['columns'][$table] = $tmp;
        if (!$merge && $tmp = array_diff_key($tb2['columns'], $tb1['columns'])) $delete['columns'][$table] = $tmp;
        if ($columns = array_intersect_key($tb1['columns'], $tb2['columns']))
        {
          foreach ($columns as $column => $cl1)
          {
            $cl2 = $tb2['columns'][$column];
            if ($cl1 != $cl2) $update['tables'][$table]['columns'][$column] = $cl1;
          }
        }
        foreach (array('indexes', 'constraints', 'triggers') as $entity)
        {
          if ($tmp = array_diff_key($tb1[$entity], $tb2[$entity])) $insert[$entity][$table] = $tmp;
          if (!$merge && $tmp = array_diff_key($tb2[$entity], $tb1[$entity])) $delete[$entity][$table] = $tmp;
          if ($tmp = array_intersect_key($tb1[$entity], $tb2[$entity]))
          {
            foreach ($tmp as $name => $value)
            {
              if ($value != $tb2[$entity][$name]) $update['tables'][$table][$entity][$name] = $value;
            }
          }
        }
      }
    }
    foreach (array('procedures', 'events', 'views') as $entity)
    {
      if ($tmp = array_diff_key($d1[$entity], $d2[$entity])) $insert[$entity] = $tmp;
      if (!$merge && $tmp = array_diff_key($d2[$entity], $d1[$entity])) $delete[$entity] = $tmp;
      if ($tmp = array_intersect_key($d1[$entity], $d2[$entity]))
      {
        foreach ($tmp as $name => $value)
        {
          if ($value != $d2[$entity][$name]) $update[$entity][$name] = $value;
        }
      }
    }
    $data = $d1['data'];
    return array('insert' => $insert, 'update' => $update, 'delete' => $delete, 'data' => $data);
  }


}