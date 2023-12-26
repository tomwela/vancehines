<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Exceptions;

class ORMSynchronizer
{  
  public function sync($xmlfile = null)
  {
    $xml1 = Core\IO::dir('temp') . '/db.xml';
    $xml2 = Core\IO::dir('engine') . '/db.xml';
    $orm = ORM::getInstance();
    if (!is_file($xml2))
    {
      $orm->generateXML('ClickBlocks\\DB');
      return;
    }
    $orm->generateXML('ClickBlocks\\DB', $xml1);
    $dom1 = new \DOMDocument('1.0', 'utf-8');
    $dom1->preserveWhiteSpace = false;
    $dom1->load($xml1);
    $xpath1 = new \DOMXPath($dom1);
    $dom2 = new \DOMDocument('1.0', 'utf-8');
    $dom2->formatOutput = true;
    $dom2->preserveWhiteSpace = false;
    $dom2->load($xml2);
    $xpath2 = new \DOMXPath($dom2);
    foreach ($xpath2->query('//DataBase') as $db2)
    {
      $dbName = $db2->getAttribute('Name');
      $db1 = $xpath1->query('//DataBase[@Name="' . $dbName . '"]');
      if ($db1->length == 0) $dom2->documentElement->removeChild($db2);
    }
    foreach ($xpath1->query('//DataBase') as $n => $db1)
    {
      $dbName = $db1->getAttribute('Name');
      $db2 = $xpath2->query('//DataBase[@Name="' . $dbName . '"]');
      if ($db2->length == 0)
      {
        $dom2->documentElement->insertBefore($dom2->importNode($db1, true), $dom2->documentElement->childNodes->item($n));
        continue;
      }
      $db2 = $db2->item(0);
      $db2->replaceChild($dom2->importNode($db1->childNodes->item(0), true), $db2->childNodes->item(0));
      foreach ($xpath2->query('//DataBase[@Name="' . $dbName . '"]/ModelLogical/Tables/Table') as $tb2)
      {
        $repo = $tb2->getAttribute('Repository');
        $tb1 = $xpath1->query('//DataBase[@Name="' . $dbName . '"]/ModelLogical/Tables/Table[@Repository="' . $repo . '"]');
        if ($tb1->length == 0) $xpath2->query('//DataBase[@Name="' . $dbName . '"]/ModelLogical/Tables')->item(0)->removeChild($tb2);
      }
      foreach ($xpath1->query('//DataBase[@Name="' . $dbName . '"]/ModelLogical/Tables/Table') as $n => $tb1)
      {
        $repo = $tb1->getAttribute('Repository');
        $tb2 = $xpath2->query('//DataBase[@Name="' . $dbName . '"]/ModelLogical/Tables/Table[@Repository="' . $repo . '"]');
        if ($tb2->length == 0)
        {
          $tables = $xpath2->query('//DataBase[@Name="' . $dbName . '"]/ModelLogical/Tables')->item(0);
          $tables->insertBefore($dom2->importNode($tb1, true), $tables->childNodes->item($n));
          continue;
        }
        $tb2 = $tb2->item(0);
        foreach ($xpath2->query('//DataBase[@Name="' . $dbName . '"]/ModelLogical/Tables/Table[@Repository="' . $repo . '"]/Fields/Field') as $field2)
        {
          $link = $field2->getAttribute('Link');
          $field1 = $xpath1->query('//DataBase[@Name="' . $dbName . '"]/ModelLogical/Tables/Table[@Repository="' . $repo . '"]/Fields/Field[@Link="' . $link . '"]');
          if ($field1->length == 0) $xpath2->query('//DataBase[@Name="' . $dbName . '"]/ModelLogical/Tables/Table[@Repository="' . $repo . '"]/Fields')->item(0)->removeChild($field2);
        }
        foreach ($xpath1->query('//DataBase[@Name="' . $dbName . '"]/ModelLogical/Tables/Table[@Repository="' . $repo . '"]/Fields/Field') as $n => $field1)
        {
          $link = $field1->getAttribute('Link');
          $field2 = $xpath2->query('//DataBase[@Name="' . $dbName . '"]/ModelLogical/Tables/Table[@Repository="' . $repo . '"]/Fields/Field[@Link="' . $link . '"]');
          if ($field2->length == 0)
          {
            $fields = $xpath2->query('//DataBase[@Name="' . $dbName . '"]/ModelLogical/Tables/Table[@Repository="' . $repo . '"]/Fields')->item(0);
            $fields->insertBefore($dom2->importNode($field1, true), $fields->childNodes->item($n));
            continue;
          }
        }
      }
    }
    $dom2->save($xml2);
  }
}

?>