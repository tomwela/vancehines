<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers;

class CustomAutoFill extends TextBox
{
   public function __construct($id, $value = null)
   {
      parent::__construct($id, $value);
      $this->properties['callBack'] = null;
      $this->properties['x'] = 0;
      $this->properties['y'] = 0;
      $this->properties['searchAction'] = "ajax.doit('->searchProducts');";
      $this->properties['classContainer'] = 'search-container';
      $this->properties['styleContainer'] = '';
      $this->properties['listClass'] = 'search-ul';      
   }

   public function render()
   {
      $searchText = Core\Register::getInstance()->page->get('searchText')->uniqueID;
      $this->properties['clearAction'] = $this->properties['clearAction']?: "ctrl=controls.$('{$searchText}'); ctrl.value=''; autofill.hideList('{$searchText}'); controls.focus(ctrl);";
      if (!$this->properties['visible']) return $this->invisible();
      $html = '
      <div id="container_' . $this->attributes['uniqueID'] . '" 
            style="' . htmlspecialchars($this->properties['styleContainer']) . '" 
            class="' . htmlspecialchars($this->properties['classContainer']) . '">
         <div class="search-block">
            <div class="icon-search" onclick="'.$this->properties['searchAction'].'"></div>
            '.parent::render().'
            <div class="icon-close" onclick="'.$this->properties['clearAction'].'"></div>
         </div>
         <div id="list_' . $this->attributes['uniqueID'] . '" style="display:none;" class="' . htmlspecialchars($this->properties['listClass']) . '"></div>
      </div>
      ';
      return $html;
   }

   public function JS()
   {
      $this->js->addTool('autofill');
      if (Web\Ajax::isAction())
      {
         if ($this->ajax->isSubmit()) $p = 'parent.';
         $this->ajax->script($p . 'autofill.initialize(\'' . $this->attributes['uniqueID'] . '\')', $this->updated + 100, true);
      }
      else $this->js->add(new Helpers\Script('autofillinit_' . $this->attributes['uniqueID'], 'autofill.initialize(\'' . $this->attributes['uniqueID'] . '\');'), 'foot');
      return $this;
   }

   public function search($value, $update = true)
   {
      if (!$this->properties['callBack']) return;
      $method = new Core\Delegate($this->properties['callBack']);
      $res = $method($value, $this->attributes['uniqueID']);
      $this->ajax->insert($res, 'list_' . $this->attributes['uniqueID'], true);
      if ($update)
      {
         if ($res) $this->ajax->script('autofill.showList(\'' . $this->attributes['uniqueID'] . '\', ' . (int)$this->properties['x'] . ', ' . (int)$this->properties['y'] . ')', true);
         else $this->ajax->script('autofill.hideList(\'' . $this->attributes['uniqueID'] . '\')', true);
      }
   }

   protected function repaint()
   {
      parent::repaint();
      if (!$this->properties['visible']) return;
      $this->JS();
   }

   protected function getRepaintID()
   {
      return 'container_' . $this->attributes['uniqueID'];
   }
}

?>
