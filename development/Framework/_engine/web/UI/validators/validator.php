<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers;

interface IValidator
{

}

abstract class Validator extends WebControl implements IValidator
{
   protected $type = null;
   protected $results = array();

   public function __construct($id, $message = null)
   {
      parent::__construct($id);
      $this->properties['controls'] = array();
      $this->properties['groups'] = array('default');
      $this->properties['text'] = $message;
      $this->properties['mode'] = 'AND';
      $this->properties['client'] = false;
      $this->properties['action'] = null;
      $this->properties['unaction'] = null;
      $this->properties['isValid'] = true;
      $this->properties['order'] = 0;
      $this->properties['tag'] = 'span';
      $this->properties['hiding'] = false;
   }

   abstract public function validate();

   public function check($value)
   {
      return true;
   }

   public function &__get($param)
   {
      if ($param == 'groups') return $this->properties['groups'];
      if ($param == 'controls') return $this->properties['controls'];
      if ($param == 'results') return $this->results;
      return parent::__get($param);
   }

   public function __set($param, $value)
   {
      if ($param == 'controls' && !Web\XHTMLParser::isParsing())
      {
         if (is_array($value)) $this->properties['controls'] = $value;
         else
         {
            $this->properties['controls'] = array();
            foreach (explode(',', $value) as $id)
            {
               $id = trim($id);
               if ($id == '') continue;
               $ctrl = $this->page->get($id);
               if ($ctrl === false) throw new \Exception(err_msg('ERR_VAL_1', array($id)));
               $this->properties['controls'][] = $ctrl->uniqueID;
            }
         }
         return;
      }
      else if ($param == 'groups')
      {
         if (is_array($value)) $this->properties['groups'] = $value;
         else
         {
            $this->properties['groups'] = array();
            foreach (explode(',', $value) as $group)
            {
               if (($group = trim($group)) != '') $this->properties['groups'][] = $group;
            }
         }
         return;
      }
      parent::__set($param, $value);
   }

   public function JS()
   {
      if (!$this->properties['client'] || !$this->properties['visible']) return $this;
      $this->js->addTool('validators');
      $script = new Helpers\Script(null, $this->getScriptString());
      if (!Web\Ajax::isAction()) 
	  {
	     if (!$this->page->validatorIsLocked($this->attributes['uniqueID'])) $this->js->add($script, 'foot');
	  }
      else
	  {
	     if ($this->page->validatorIsLocked($this->attributes['uniqueID'])) $this->ajax->script('validators.remove(\'' . $this->attributes['uniqueID'] . '\')', 0, true);
		 else $this->ajax->script($script->text, 0, true);
	  }
      return $this;
   }

   public function render()
   {
      if (!$this->properties['visible']) return $this->invisible();
      if (!$this->properties['hiding'])
      {
         return '<' . $this->properties['tag'] . $this->getParams() . '>' . ((!$this->properties['isValid']) ? $this->properties['text'] : '') . '</' . $this->properties['tag'] . '>';
      }
      else
      {
         if ($this->properties['isValid']) $this->addStyle('display', 'none');
         else $this->addStyle('display', '');
         return '<' . $this->properties['tag'] . $this->getParams() . '>' . $this->properties['text'] . '</' . $this->properties['tag'] . '>';
      }
   }

   protected function doAction()
   {
      if ($this->properties['action'] && !$this->properties['isValid']) $this->ajax->script('if (document.getElementById(\'' . $this->attributes['uniqueID'] . '\')) {' . $this->properties['action'] . ';}', 0, true);
      if ($this->properties['unaction'] && $this->properties['isValid']) $this->ajax->script('if (document.getElementById(\'' . $this->attributes['uniqueID'] . '\')) {' . $this->properties['unaction'] . ';}', 0, true);
   }

   protected function validateControl($uniqueID)
   {
      $ctrl = $this->page->getByUniqueID($uniqueID);
      if ($ctrl === false) throw new \Exception(err_msg('ERR_VAL_1', array($uniqueID)));
      return $this->results[$ctrl->uniqueID] = $ctrl->validate($this->type, new Core\Delegate(get_class($this) . '@' . $this->getFullID() . '->check'));
   }

   protected function getScriptString(array $params = null)
   {
      if ($this->ajax->isSubmit()) $p = 'parent.';
	  $params['hiding'] = (int)$this->properties['hiding'];
	  foreach ($params as $key => &$param) $param = "'" . $key . "': " . $param;
      return $p . 'validators.add(\'' . $this->attributes['uniqueID'] . 
            '\', {\'groups\': \'' . addslashes(implode(',', $this->groups)) . 
            '\', \'cids\': \'' . (is_array($this->properties['controls'])? implode(',', $this->properties['controls']) : $this->properties['controls']) . 
            '\', \'type\': \'' . $this->type . 
            '\', \'order\': \'' . (int)$this->properties['order'] . 
            '\', \'mode\': \'' . addslashes($this->properties['mode']) . 
            '\', \'message\': \'' . addslashes($this->properties['text']) . 
            '\', \'action\': \'' . addslashes($this->properties['action']) . 
            '\', \'unaction\': \'' . addslashes($this->properties['unaction']) . 
            '\', \'exparam\': {' . implode(', ', $params) . '}});';
   }

   protected function repaint($time = 0)
   {
      parent::repaint($time);
      $this->JS();
   }

   public function remove($time = 0)
   {
      parent::remove($time);
      if ($this->properties['client']) $this->ajax->script('validators.remove(\'' . $this->attributes['uniqueID'] . '\')', $time, true);
   }

   protected function getXHTMLParams()
   {
      $controls = $this->properties['controls'];
      $tmp = array();
      foreach ($controls as $uniqueID)
      {
         $tmp[] = $this->page->getByUniqueID($uniqueID)->getFullID();
      }
      $this->properties['controls'] = implode(', ', array_filter($tmp));
      $params = parent::getXHTMLParams();
      $this->properties['controls'] = $controls;
      return $params;
   }
}

?>