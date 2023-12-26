<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\DB,
    ClickBlocks\Utils,
    ClickBlocks\MVC,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers;

class WidgetCsrHistory extends WidgetBackend
{
   public function __construct($id, $template = null)
   {
      parent::__construct($id, ($template) ?: 'map/history.html');
      $this->properties['source'] = 'ClickBlocks\DB\OrchestraHistory->getWidgetHistory';
      $this->properties['customerID'] = null;
      $this->properties['searchvalue'] = null;
      $this->properties['sortBy'] = 2;
      $this->properties['superDistributorID'] = NULL;
      $this->properties['corporateEmployee'] = NULL;
   }

   public function init()
   {
      $this->customerID = $this->page->customer->customerID;
      $this->get('pageSize')-> onchange = $this->method('__set', array('pageSize', 'js::controls.$(\'' . $this->get('pageSize')->uniqueID . '\').value'));
      $this->get('pageSize1')-> onchange = $this->method('__set', array('pageSize', 'js::controls.$(\'' . $this->get('pageSize1')->uniqueID . '\').value'));
      //$this->get('search')-> onclick = $this->method('search');
   }

   public function search()
   {
     if(!$this->get('searchText')->value)
       return;
      $this->properties['searchvalue'] = $this->get('searchText')->value;
   }

   protected function getData()
   {
      $html = parent::getData();
      $this->get('pageSize1')->value = $this->get('pageSize')->value = $this->properties['pageSize'];
      $this->tpl->rows = $this->rows;
      $this->tpl->sortBy = $this->properties['sortBy'];
      $html .= '<tr>
                    <th> CSR NAME </th>
                    <th width="190" class="gray"> LAST SERVICE DATE </th>
                </tr>';
      return $html;
   }
}
