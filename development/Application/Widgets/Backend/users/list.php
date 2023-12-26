<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\DB,
    ClickBlocks\Utils,
    ClickBlocks\MVC,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers;

class WidgetUsers extends WidgetBackend
{

   public function __construct($id, $template = null)
   {
      parent::__construct($id, ($template) ?: 'users/list.html');
      $this->properties['source'] = 'ClickBlocks\DB\OrchestraUsers->getWidgetUsersData';
      $this->properties['searchvalue'] = null;
      $this->properties['sortBy'] = 1;
      //$this->properties['superDistributorID'] = NULL;
      //$this->properties['corporateEmployee'] = NULL;
      $this->properties['filter'] = 1;  // prevent deactivated users from appearing in the user list
   }

   public function init()
   {
      $this->get('pageSize')-> onchange = $this->method('__set', array('pageSize', 'js::controls.$(\'' . $this->get('pageSize')->uniqueID . '\').value'));
      $this->get('pageSize1')-> onchange = $this->method('__set', array('pageSize', 'js::controls.$(\'' . $this->get('pageSize1')->uniqueID . '\').value'));
      $this->get('search')-> onclick = $this->method('search', array("js::$('.search-input').val()"));
   }

   public function search($value)
   {
    //$this->tpl->search = 
    $this->properties['searchvalue'] = ($value == 'Search' ? null : $value);
   }

   protected function getData()
   {
      $html = parent::getData();
      
      $this->tpl->currentUser = $this->page->user;
      $this->get('pageSize1')->value = $this->get('pageSize')->value = $this->properties['pageSize'];
      $this->tpl->rows = $this->rows;
      $this->tpl->sortBy = $this->properties['sortBy'];
      
      $html .= '<tr>
                    <th width="145"'. (abs($this->properties['sortBy']) == 1 ? ' class="gray"': '') . '>' .$this->sortLink(1, '<div class="fl"> Name of Customer </div>') .'</th>
                    <th width="90"'.  (abs($this->properties['sortBy']) == 2 ? ' class="gray"': '') . '>' .$this->sortLink(2, '<div class="fl">Date</div>').'</th>
                    <th width="80"'.  (abs($this->properties['sortBy']) == 3 ? ' class="gray"': '') . '>' .$this->sortLink(3, '<div class="fl">Time</div>').'</th>
                    <th width="100"'. (abs($this->properties['sortBy']) == 4 ? ' class="gray"': '') . '>' .$this->sortLink(4, '<div class="fl">Email</div>').'</th>
                    <th width="120"></th>
                </tr>';


      return $html;
   }
}
