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
 * Responsibility of this file: writer.php
 *
 * @category   DB
 * @package    DB
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */
 
namespace ClickBlocks\DB\Sync;

/**
 * Class for changing MySQL database structure.
 *
 * @abstract
 */
class MySQLWriter extends DBWriter
{
  /**
   * Makes changes in database structure and information tables data.
   * If changes were made in db structure then method returns the array of executed SQL queries.
   *
   * @param array $info - the array returned by method Synchronizer::compare.
   * @return array | NULL
   * @access public
   */
  public function write(array $info, $test = false)
  {
    $this->queries = array();
    $params = $this->db->getParameters();
    $pdo = $this->db->getPDO();
    $dbName = $this->db->wrap($params['dbname'], false);
    if(!$test) $pdo->beginTransaction();
    try
    {
      if(!$test) $pdo->prepare('SET FOREIGN_KEY_CHECKS=0')->execute();
      foreach (array('insert', 'delete') as $type)
      {
        foreach ($info[$type] as $class => $data)
        {
          if ($class == 'tables' || $class == 'procedures' || $class == 'events' || $class == 'views')
          {
            foreach ($data as $name => $values)
            {
              if ($class == 'tables')
              {
                if ($type == 'insert') 
                {
                  $this->setData($pdo, 'insert', 'table', array('tbl_definition' => $values['definition']), $test);
                  foreach ($values['triggers'] as $trg) $this->setData($pdo, $type, 'trigger', $trg, $test);
                }
                else $this->setData($pdo, 'delete', 'table', $values['meta'], $test);
              }
              else $this->setData($pdo, $type, substr($class, 0, -1), $values, $test);
            }
          }
          else
          {
            foreach ($data as $table => $dta)
            {
              foreach ($dta as $name => $values)
              {
                $this->setData($pdo, $type, substr($class, 0, -1 - (int)($class == 'indexes')), $values, $test);
              }
            }
          }
        }
      }
      foreach ($info['update'] as $class => $data)
      {
        if ($class == 'meta')
        {
          $this->setData($pdo, 'update', 'database', $data, $test);
        }
        else if ($class == 'tables')
        {
          foreach ($data as $table => $tb)
          {
            if (isset($tb['meta']))
            {
              $this->setData($pdo, 'update', 'table', $tb['meta'], $test);
            }
            else
            {
              foreach (array('columns', 'indexes', 'constraints', 'triggers') as $entity)
              {
                if (!isset($tb[$entity])) continue;
                $key = substr($entity, 0, -1 - (int)($entity == 'indexes'));
                if ($this->db->getSQL('update', $key) === false)
                {
                  foreach ($tb[$entity] as $values)
                  {
                    $this->setData($pdo, 'delete', $key, $values, $test);
                    $this->setData($pdo, 'insert', $key, $values, $test);
                  }
                }
                else
                {
                  foreach ($tb[$entity] as $values)
                  {
                    $this->setData($pdo, 'update', $key, $values, $test);
                  }
                }
              }
            }
          }
        }
        else
        {
          foreach (array('procedures', 'events', 'views') as $entity)
          {
            if ($class != $entity) continue;
            $key = substr($entity, 0, -1);
            foreach ($data as $values)
            {
              $this->setData($pdo, 'delete', $key, $values, $test);
              $this->setData($pdo, 'insert', $key, $values, $test);
            }
          }
        }
      }
      if($info['data'])
      {
        ?><div style="width:100%;height:100px;overflow:auto;">Lookups:<br/><?
        foreach ($info['data'] as $values)
        {
          $this->setData($pdo, 'delete', 'data', $values, $test);
          $this->setData($pdo, 'insert', 'data', $values, $test);
        }
        ?></div><?
      }
      if(!$test)
      {
        $pdo->prepare('SET FOREIGN_KEY_CHECKS=1')->execute();
        $pdo->commit();
      }
    }
    catch (\PDOException $e)
    {
      $pdo->rollBack();
      throw new SynchronizerException($e->getMessage());
    }
    return $this->queries;
  }
}