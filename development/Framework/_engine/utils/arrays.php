<?php

namespace ClickBlocks\Utils;

class Arrays
{
   public static function swap(array &$array, $key1, $key2)
   {
      $keys = array_flip(array_keys($array));
      $array = array_merge(array_slice($array, 0, $keys[$key1]), array_reverse(array_slice($array, $keys[$key1], $keys[$key2])) ,array_slice($array, $keys[$key2]));
   }

   public static function inject(array &$array, $keySource, $key, $value)
   {
      $keys = array_flip(array_keys($array));
      $array = array_slice($array, 0, $keys[$keySource] + 1, true) + array($key => $value) + array_slice($array, $keys[$keySource] + 1, null, true);
      return array_merge(array_slice($array, 0, $keys[$key1]), array_reverse(array_slice($array, $keys[$key1], $keys[$key2])) ,array_slice($array, $keys[$key2]));
   }
}

?>