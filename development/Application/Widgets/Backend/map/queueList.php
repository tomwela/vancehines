<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\DB,
    ClickBlocks\Utils,
    ClickBlocks\MVC,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers;

class WidgetMapQueue extends WidgetBackend
{
  public function __construct($id, $template = null)
  {
     parent::__construct($id, ($template) ?: 'map/queueList.html');
     $this->properties['source'] = 'ClickBlocks\DB\OrchestraCustomers->getWidgetMapQueueData';
     $this->properties['searchvalue'] = null;
     $this->properties['sortBy'] = 2;
     //$this->properties['superDistributorID'] = NULL;
     //$this->properties['corporateEmployee'] = NULL;
  }

  public function init()
  {
     $this->get('pageSize')-> onchange = $this->method('__set', array('pageSize', 'js::controls.$(\'' . $this->get('pageSize')->uniqueID . '\').value'));
     $this->get('pageSize1')-> onchange = $this->method('__set', array('pageSize', 'js::controls.$(\'' . $this->get('pageSize1')->uniqueID . '\').value'));
     $this->get('search')-> onclick = $this->method('search');
  }

  public function search()
  {
    $this->properties['searchvalue'] = $this->get('searchText')->value;
  }

  protected function getData()
  {
     $html = parent::getData();
     $this->get('pageSize1')->value = $this->get('pageSize')->value = $this->properties['pageSize'];
     $this->tpl->rows = $this->rows;
     $this->tpl->sortBy = $this->properties['sortBy'];
     $html .= '<tr>
                   <th width="145"' . (abs($this->properties['sortBy']) == 1 ? ' class="gray"' : '') . '>' . $this->sortLink(1, 'VIN #') . '</th>
                   <th width="90"'  . (abs($this->properties['sortBy']) == 2 ? ' class="gray"' : '') . '>' . $this->sortLink(2, 'DATE') . '</th>
                   <th width="80"'  . (abs($this->properties['sortBy']) == 3 ? ' class="gray"' : '') . '>' . $this->sortLink(3, 'TIME') . '</th>
                   <th width="100"' . (abs($this->properties['sortBy']) == 4 ? ' class="gray"' : '') . '>' . $this->sortLink(4, 'CUSTOMER') . '</th>
                   <th width="50"'  . (abs($this->properties['sortBy']) == 5 ? ' class="gray"' : '') . '>' . $this->sortLink(5, 'FIRMWARE') . '</th>
                   <th width="50"'  . (abs($this->properties['sortBy']) == 6 ? ' class="gray"' : '') . '>' . $this->sortLink(6, 'MODEL') . '</th>
                   <th width="50"'  . (abs($this->properties['sortBy']) == 7 ? ' class="gray"' : '') . '>' . $this->sortLink(7, 'YEAR') . '</th>
                   <th width="50"'  . (abs($this->properties['sortBy']) == 8 ? ' class="gray"' : '') . '>' . $this->sortLink(8, 'OS') . '</th>
                   <th width="120"></th>
               </tr>';
     return $html;
  }
   
  public function refresh()
  {
    $this->update();
  }
}
