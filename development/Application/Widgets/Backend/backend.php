<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Utils,
    ClickBlocks\MVC,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers;

abstract class WidgetBackend extends Widget
 {
   public function __construct($id, $template = null)
   {
      parent::__construct($id, ($template) ? Core\IO::dir('widgets') . '/Backend/' . $template : '');
      $this->properties['source'] = null;
   }
 
   public function getCount()
   {
      $method = new Core\Delegate($this->properties['source']);
      return $method('count', $this->properties);
   }
   public function getRows()
   {
      $method = new Core\Delegate($this->properties['source']);
      return $method('rows', $this->properties);
   } 
   protected function getData()
   {
      if ($this->count)
       {
         $from = $this->properties['pageSize'] * $this->properties['pos'] + 1;
         $to = $from + $this->properties['pageSize'] - 1;
         if ($to > $this->count) $to = $this->count;
      }
      $this->tpl->from = (int)$from;
      $this->tpl->to = (int)$to;
   }
   protected function sortLink($sortBy, $title, $align='left')
   {
      if (abs($this->properties['sortBy']) == $sortBy)
      {
         $img = new Helpers\Img();
         $img->style = 'margin-right:5px;';
         $up =  ($this->properties['sortBy'] < 0) ? '' : 'up';
         $arr = '<div class="sort-arrow '. $up .' " ></div>';
         $sortBy = $this->properties['sortBy'];
      }
      $text = new Helpers\StaticText(null, $title);      
      $text->addStyle('cursor', 'pointer');
      if($align=='right') $text->addStyle('float', 'right');
      if($align=='left') $text->addStyle('float', 'left');
      $text->onclick = $this->method('sort', array((int)$sortBy));
      $arr = new Helpers\StaticText(null, $arr);      
      $arr->addStyle('cursor', 'pointer');
      if($align=='right') $arr->addStyle('float', 'right');
      $arr->onclick = $this->method('sort', array((int)$sortBy));
      return ($align=='right')? $arr . $text : $text . $arr;
   }
 }

?>
