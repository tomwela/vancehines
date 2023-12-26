<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\DB,
    ClickBlocks\Utils,
    ClickBlocks\MVC,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers;

class WidgetMapNotes extends WidgetBackend
{
   public function __construct($id, $template = null)
   {
      parent::__construct($id, ($template) ?: 'map/noteList.html');
      $this->properties['source'] = 'ClickBlocks\DB\OrchestraNotes->getWidgetMapNotesData';
      $this->properties['searchvalue'] = null;
      $this->properties['sortBy'] = 1;
      $this->properties['superDistributorID'] = NULL;
      $this->properties['corporateEmployee'] = NULL;
      $this->properties['mapID'] = NULL;
      $this->properties['pageSize'] = 5;
   }

   public function init()
   {
//      $this->get('pageSize')-> onchange = $this->method('__set', array('pageSize', 'js::controls.$(\'' . $this->get('pageSize')->uniqueID . '\').value'));
//      $this->get('pageSize1')-> onchange = $this->method('__set', array('pageSize', 'js::controls.$(\'' . $this->get('pageSize1')->uniqueID . '\').value'));
      $this->get('search')-> onclick = $this->method('search');
      $this->get('searchText')->onkeypress = "if(event.keyCode == 13) ajax.doit('".addslashes(get_class())."@{$this->uniqueID}->search');";
      $this->get('refresh')->onclick = "ajax.doit('".addslashes(get_class())."@{$this->uniqueID}->refresh');";
   }

  public function search()
  {
    $this->properties['searchvalue'] = $this->get('searchText')->value;
  }

   protected function getData()
   {
      $html = parent::getData();
//      $this->get('pageSize1')->value = $this->get('pageSize')->value = $this->properties['pageSize'];
      $this->tpl->rows = $this->rows;
      $this->tpl->sortBy = $this->properties['sortBy'];
      $html .= '<tr>
                    <th width="150"' . (abs($this->properties['sortBy']) == 1 ? ' class="gray"' : '') . '>' . $this->sortLink(1, 'NOTE') . '</th>
                    <th width="145"' . (abs($this->properties['sortBy']) == 2 ? ' class="gray"' : '') . '>' . $this->sortLink(2, 'DATE') . '</th>
                    <th width="100"' . (abs($this->properties['sortBy']) == 3 ? ' class="gray"' : '') . '>' . $this->sortLink(3, 'USERNAME') . '</th>
                    <th width="100"></th>
                </tr>';
      return $html;
   }
   
   public function refresh()
   {
     $this->properties['mapID'] = $this->tpl->noteMapID;
     $this->properties['searchvalue'] = $this->get('searchText')->value;
     $this->update();
   }
}
