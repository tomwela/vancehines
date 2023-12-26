<?php

namespace ClickBlocks\API\Logic;

use ClickBlocks\API, 
    ClickBlocks\API\Logic\Example,
    ClickBlocks\Core,
    ClickBlocks\Utils;

class JSONObjectValidator
{
   protected static function normalizeInfo($k, $info) 
   {
      if (is_scalar($info))
         $info = array('type'=>$info);
      if (!$info['name'] && is_string($k))
         $info['name'] = $k;
      if (!$info['type'] && ($info[0]))
        $info['type'] = $info[0];
      if (empty($info['required'])) $info['required'] = (bool)$info['req'] ?: false;
      return $info;
   }
   
   public static function validateEmail($email) {
      $re = '/^[-a-z0-9!#$%&\'*+\/=?^_`{|}~]+(\.[-a-z0-9!#$%&\'*+\/=?^_`{|}~]+)*@([a-z0-9]([-a-z0-9]{0,61}[a-z0-9])?\.)+(aero|arpa|asia|biz|cat|com|coop|edu|gov|info|int|jobs|mil|mobi|museum|name|net|org|pro|tel|travel|[a-z][a-z])$/i';
      return preg_match($re,$email);
   }

   public static function validateField(&$object, $name, &$info) 
   {
      $info = self::normalizeInfo($name, $info);
      $value = $object[$name];
      $checkMinMax = function() use ($value, &$info) {
         if (is_numeric($info['min']) && $value<$info['min']) {
           $info['text'] = "'{$info['name']}' is less than {$info['min']}";
           return false;
         }
         if (is_numeric($info['max']) && $value>$info['max']) {
           $info['text'] = "'{$info['name']}' is greater than {$info['max']}";
           return false;
         }
         return true;
       };
      if (!is_array($info) || !$info['name'])
      {
         throw new \Exception(__METHOD__.': Incorrect $info array');
      }
      switch ($info['type'])
      {
        case Example::TYPE_NUMBER:
          $info['typestr'] = 'number';
          if (!is_numeric($value))
            return false;
          return $checkMinMax();
        case Example::TYPE_INTEGER: 
          $info['typestr'] = 'integer';
          if (!is_numeric($value) || ((int)$value != $value))
            return false;
          return $checkMinMax();
        /*case Example::TYPE_FLOAT: 
          $info['typestr'] = 'float';
          if (!is_numeric($value) || ((float)$value != $value))
            return false;
          return $checkMinMax();*/
        case Example::TYPE_STRING: 
          $info['typestr'] = 'string';
          return is_string($value) || is_numeric($value);
        /*case Example::TYPE_ENUM: 
          $info['text'] = "'{$info['name']}' is not in ('".implode("', '", $info['options'])."')";
          return in_array($value, (array)$info['options']);*/
        /*case Example::TYPE_FILE: 
          $info['typestr'] = 'uploaded file';
          return (isset($value['tmp_name']) && isset($value['error']));*/
        case Example::TYPE_ARRAY:
          $info['typestr'] = 'array or object';
          return is_array($value);
        case Example::TYPE_BOOLEAN:
          $info['typestr'] = 'boolean';
          return (is_bool($value) || ($value=='1') || ($value=='0'));
        case Example::TYPE_EMAIL:
          $flag = self::validateEmail($value);
          if (!$flag && !$info['text'])
            $info['text'] = "'{$info['name']}' is not a valid email address";
          return $flag;
        /*case Example::TYPE_REGEXP:
          $regexp = @$info['expression'] ?: @$info['regexp'];
          if (!$regexp)
            throw new \Exception("'expression' is required for regexp validation!");
          $flag = preg_match($regexp, $value);
          if (!$flag && !$info['text'])
            $info['text'] = "'{$info['name']}' doesn't match regular expression {$regexp}";
          return $flag;*/
          //  throw new API\ParameterValidationException();
        /*case Example::TYPE_DATESTRING:
          $flag = preg_match('/^(\d{4})\-(\d{2})\-(\d{2})$/', $value, $m);
          if ($flag)
            $flag = checkdate($m[2], $m[3], $m[1]);*/
          /*if (!$info['format'])
            throw new \Exception("'format' is required for date string validation!");
          $dt = date_create_from_format($info['format'], $value);
          if (!$dt)
            $flag = false;
          else 
            $flag = ($dt->format($info['format']) == $value);*/
          if (!$flag && !$info['text'])
            $info['text'] = "'{$info['name']}' is not a valid date in format 'YYYY-MM-DD'";
          return $flag;
        case Example::TYPE_SCALAR: 
        default:
          $info['typestr'] = 'scalar';
          return is_scalar($value);
      }
    }
    
   public static function validateObject(&$object, array $fields, $objName = '')
   {
      $missing = array();
      foreach ($fields as $k=>&$info) {
         $info = self::normalizeInfo($k, $info);
         if ($info['required'] && !(bool)$object[$info['name']]) $missing[] = $info['name'];
      }
      $inobjstr = $objName ? ' in object "'.$objName.'"' : '';
      if (count($missing))
        throw new API\ParameterRequiredException("Required field(s) are missing: '".implode("', '",$missing)."' {$inobjstr} (must not be 0, 0.0, null, false or empty string)", 201);
      foreach ($fields as $field=>&$info) {
         if (!$object[$field]) continue;
         $flag = self::validateField($object, $field, $info);
         if (!$flag) {
            $text = $info['text'] ?: "'{$field}' doesn't match required type: {$info['typestr']}";
            $text .= ' '.$inobjstr;
            throw new API\ParameterValidationException($text, (int)$info['code'] ?: 202);
         }
      }
   }
}

?>
