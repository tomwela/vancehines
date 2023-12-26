<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core;
use ClickBlocks\DB;
use ClickBlocks\MVC;
use ClickBlocks\Utils;
use ClickBlocks\Web;
use ClickBlocks\Web\UI\Helpers;

class WidgetAllMapNotes extends WidgetBackend
{
    public function __construct($id, $template = null)
    {
        parent::__construct($id, ($template) ?: 'map/allNotes.html');
        $this->properties['source'] = 'ClickBlocks\DB\OrchestraNotes->getWidgetAllMapNotesData';
        $this->properties['searchvalue'] = null;
        $this->properties['sortBy'] = 1;
        $this->properties['mapID'] = NULL;
        $this->properties['customerID'] = NULL;
        $this->properties['pageSize'] = 10;
        //$this->properties['superDistributorID'] = NULL;
        //$this->properties['corporateEmployee'] = NULL;
    }

    public function init()
    {
        $this->get('pageSize')->onchange = $this->method('__set', array('pageSize', 'js::controls.$(\'' . $this->get('pageSize')->uniqueID . '\').value'));
        //$this->get('pageSize1')->onchange = $this->method('__set', array('pageSize', 'js::controls.$(\'' . $this->get('pageSize1')->uniqueID . '\').value'));
        $this->get('search')->onclick = $this->method('search');
        $this->get('searchText')->onkeypress = "if(event.keyCode == 13) ajax.doit('" . addslashes(get_class()) . "@{$this->uniqueID}->search');";

        // no refresh function exists in html yet
        //$this->get('refresh')->onclick = "ajax.doit('" . addslashes(get_class()) . "@{$this->uniqueID}->refresh');";
    }

    public function search()
    {
        $this->properties['searchvalue'] = $this->get('searchText')->value;
    }

    public function refresh()
    {
        $this->properties['mapID'] = $this->tpl->noteMapID;
        $this->properties['customerID'] = $this->tpl->noteCustomerID;
        $this->properties['searchvalue'] = $this->get('searchText')->value;
        $this->update();
    }

    protected function getData()
    {
        $html = parent::getData();

        // pageSize1 was removed from html - It was the dropdown selection for showing 10 or 20 results at a time
        // I removed it because it was redundant and to clean up the interface
        //$this->get('pageSize1')->value = $this->get('pageSize')->value = $this->properties['pageSize'];
        $this->tpl->rows = $this->rows;
        $this->tpl->sortBy = $this->properties['sortBy'];
        $html .= '<tr>
                    <th width="150"' . (abs($this->properties['sortBy']) == 1 ? ' class="gray"' : '') . '>' . $this->sortLink(1, 'NOTE') . '</th>
                    <th width="145"' . (abs($this->properties['sortBy']) == 2 ? ' class="gray"' : '') . '>' . $this->sortLink(2, 'DATE') . '</th>
                    <th width="100"' . (abs($this->properties['sortBy']) == 3 ? ' class="gray"' : '') . '>' . $this->sortLink(3, 'USERNAME') . '</th>
                    <th width="50"' . (abs($this->properties['sortBy']) == 4 ? ' class="gray"' : '') . '>' . $this->sortLink(3, 'Map ID') . '</th>
                    <th width="100"></th>
                </tr>';
        return $html;
    }
}