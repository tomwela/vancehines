<?php

namespace ClickBlocks\Web;

use ClickBlocks\Core,
    ClickBlocks\Web\UI\Helpers;

class CSS
{
   private static $instance = null;
  
   protected $css = array();

   private function __construct()
   {
      $this->css['link'] = array();
      $this->css['style'] = array();
   }

   public static function getInstance()
   {
      if (self::$instance === null) self::$instance = new CSS();
      return self::$instance;
   }

   protected function isValidType($type)
   {
      if (!isset($this->css[$type])) throw new \Exception(err_msg('ERR_CSS_1', array($type)));
      return true;
   }

   public function add(Helpers\Style $obj, $type = 'link')
   {
      $config = Core\Register::getInstance()->config;
      if ($config['staticFilesExpired'] && $obj->href)
         $obj->href .= '?id=' . $config['staticFilesExpired'];

      if (!isset($this->css[$type])) throw new \Exception(err_msg('ERR_CSS_1', array($type)));
      $this->css[$type][$obj->id] = $obj;
      return $this;
   }

   public function set($id, Helpers\Style $obj, $type = 'link')
   {
      $this->isValidType($type);
      $this->css[$type][$id] = $obj;
      return $this;
   }

   public function get($id, $type = 'link')
   {
      $this->isValidType($type);
      return $this->css[$type][$id];
   }

   public function delete($id, $type = 'link')
   {
      $this->isValidType($type);
      unset($this->css[$type][$id]);
      return $this;
   }

   public function render($type = 'link')
   {
      $this->isValidType($type);
      foreach ($this->css[$type] as $obj) $html .= $obj->render();
      return $html;
   }

   public static function style($style)
   {
      return foo(new Helpers\Style(null, $style))->render();
   }

   public static function link($src, $charset = null)
   {
      if ($src[0] == '/') $src = Core\IO::url('css') . $src;
      $obj = new Helpers\Style();
      $obj->href = $src;
      $obj->charset = $charset;
      return $obj->render();
   }
}

?>
