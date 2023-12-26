<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Utils,
    ClickBlocks\MVC,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers;

class PopUp extends Panel
{
   public function __construct($id)
   {
      parent::__construct($id);
      $this->attributes['class'] = 'popup';
      $this->properties['shadow'] = true;
      $this->properties['visible'] = 0;
   }

   public function init()
   {
      $this->js->addTool('controls');
      $this->js->add(new Helpers\Script('popup', null, Core\IO::url('js') . '/popup.min.js'), 'link');
      $this->tpl->uniqueID = $this->attributes['uniqueID'];
      $click = 'popup.isClicked = true;popup.hide(\'' . $this->attributes['uniqueID'] . '\');';
      foreach(array('btnNo', 'btnCancel', 'btnYes') as $btnID)
         if ($btn = $this->get($btnID)) $btn->onclick = $click . $btn->onclick;
      $this->addStyle('z-index', '100');
   }

   public function show($opacity = 0.5, $centre = true, $fadeTime = 0, $closeTime = 0, $time = 0)
   {
      $this->visible=1;
      // $this->addStyle('z-index', '100');
      if ($this->ajax->isSubmit()) $p = 'parent.';
      $script = $p . 'popup.show(\'' . $this->attributes['uniqueID'] . '\', ' . (float)$opacity . ', \'' . (int)$centre . '\', ' . (int)$fadeTime . ', ' . (int)$closeTime . ');';
      if (Web\Ajax::isAction()) $this->ajax->script($script, $time);
      else $this->js->add(new Helpers\Script(null, $script), 'foot');
      $this->update();
   }

   public function hide($time = 0)
   {
      // $this->visible=0;
      if ($this->ajax->isSubmit()) $p = 'parent.';
      $script = $p . 'popup.hide(\'' . $this->attributes['uniqueID'] . '\');';
      if (Web\Ajax::isAction()) $this->ajax->script($script, $time);
      else $this->js->add(new Helpers\Script(null, $script), 'foot');
      // $this->update();
   }

   public function JS()
   {
      parent::JS();
      $script = 'popup.initialize(\'' . $this->attributes['uniqueID'] . '\', ' . (int)$this->properties['shadow'] . ');';
      if (Web\Ajax::isAction())
      {
         if ($this->ajax->isSubmit()) $p = 'parent.';
         $this->ajax->script($script, $this->updated, true);
      }
      else $this->js->add(new Helpers\Script('popupinit_' . $this->attributes['uniqueID'], $script), 'foot');
      return $this;
   }

   protected function invisible()
   {
      $span = new Helpers\StaticText($this->getRepaintID());
      $span->showID = true;
      $span->addStyle('display', 'none');
      $span->addStyle('z-index', '100');
      return $span->render();
   }
}

?>
