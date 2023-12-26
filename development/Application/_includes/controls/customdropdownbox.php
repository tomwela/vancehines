<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Web,
  ClickBlocks\Web\UI\Helpers;

class CustomDropDownBox extends DropDownBox
{
  public function __construct($id, $value = null)
  {
    parent::__construct($id, $value);
    $this->properties['tag'] = 'div';
    $this->attributes['style'] = 'z-index:10;width:268px;';
    $this->attributes['class'] = 'custom-select short f-l mt10 ml10';
  }

  public function JS()
  {
    if (!$this->properties['disabled'])
    {
      if ($this->ajax->isSubmit()) $p = 'parent.';
      $script = $p .'cddb.initialize(\'' . $this->attributes['uniqueID'] . '\');';
      if (Web\Ajax::isAction()) $this->ajax->script($script, $this->updated, true);
      else
      {
        $this->js->addTool('controls');
        $this->js->add(new Helpers\Script('cddb', null, Core\IO::url('js') . '/customddb.min.js'), 'link');
        $this->js->add(new Helpers\Script('customddb_' . $this->attributes['uniqueID'], $script), 'foot');
      }
    }
    return $this;
  }

  public function render()
  {
    if (!$this->properties['visible']) return $this->invisible();

    $stack = new \SplStack();
    $stack->push($this->attributes['style']);
    $stack->push($this->attributes['class']);
    $stack->push($this->attributes['onchange']);
    $this->attributes['style'] = 'display:none;';
    $this->attributes['class'] = $this->hasClass('error')?'error':'';
    $this->attributes['onchange'] = '';
    $ddb = parent::render();
    $this->attributes['onchange'] = $stack->pop();
    $this->attributes['class']    = $stack->pop();
    $this->attributes['style']    = $stack->pop();

    $ulWidth = (substr_count($this->attributes['class'],'fixlist')) ? (int)$this->getStyle('width') - 1 : (int)$this->getStyle('width');
    $width = (int)$this->getStyle('width') - 38;
    if (substr_count($this->attributes['class'],'fixlist2')) --$width;
    if ($this->properties['defaultValue'] && $this->properties['value'] == '') $value = $this->properties['defaultValue'];
    else
    {
       $value = $this->properties['value'] != '' ? $this->properties['options'][$this->properties['value']] : current((array)$this->properties['options']);
    }

    $html = '<' . $this->properties['tag'] . 
              ' id="container_' . $this->attributes['uniqueID'] . '"'.
              ' class="' . htmlspecialchars($this->attributes['class']) . ($this->properties['disabled'] ? ' disable-element' : '') . '"'.
              ' style="' . htmlspecialchars($this->attributes['style']) . '"'.
              ' onchange="'.$this->attributes['onchange'].';">';
    $html .= '<div id="select_' . $this->attributes['uniqueID'] . '">';
      $html .= '<span class="arrow"></span>';
      $html .= '<div class="splash"></div>';
    $html .= '</div>';
    $html .= '<div id="val_'.$this->attributes['uniqueID'].'" class="field">'.htmlspecialchars($value).'</div>';
    $html .= '<ul id="list_'.$this->attributes['uniqueID'].'" style="display:none; width: 100%">';
    $i = 0;
    $opts = $this->properties['options'];
    if ($this->properties['defaultValue'] != '') $opts = array('' => $this->properties['defaultValue']) + $opts;
    foreach($opts as $k => $v)
    {
      // if(!is_array($v)) continue;
      $html .= '<li '.
                  'id="li_'.$i.'_'.$this->attributes['uniqueID'].'"'.
                  'data-id="'.addslashes(htmlspecialchars($k)).'"'.
                  'data-text="'.addslashes(htmlspecialchars($v)).'"'.
                '>'.
                      htmlspecialchars($v).
                '</li>';
      $i++;
    }
    $html .= '</ul>';
    $html .= $ddb . '</' . $this->properties['tag'] . '>';
    return $html;
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
