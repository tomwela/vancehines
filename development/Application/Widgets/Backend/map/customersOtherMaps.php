<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Web;

class WidgetCustomersOtherMaps extends WidgetBackend
{
    public function __construct($id, $template = NULL)
    {
        parent::__construct($id, ($template) ? : 'map/customersOtherMaps.html');
        $this->properties['source'] = 'ClickBlocks\DB\OrchestraMaps->getCustomersOtherMapsData';
        $this->properties['sortBy'] = 1;
        $this->properties['perem'] = NULL;
        $this->properties['otherMaps'] = NULL;
        $this->properties['isCustomer'] = NULL;
        $this->properties['customerID'] = NULL;
        $this->properties['currentMap'] = NULL;

    }

    public function refresh()
    {
        $this->update();
    }

    protected function getData()
    {
        $html = parent::getData();

        $this->get('searchMapBlock.pageSize1')->value = $this->get('searchMapBlock.pageSize')->value = $this->properties['pageSize'];
        $this->tpl->rows = $this->rows;
        $this->tpl->sortBy = $this->properties['sortBy'];
        $this->tpl->perem = $this->properties['perem'];
        $this->tpl->otherMaps = $this->properties['otherMaps'];
        $html .= '<tr>
                <th width="280"' . (abs($this->properties['sortBy']) == 1 ? ' class="gray"' : '') . '>' . $this->sortLink(1, 'NAME OF MAP') . '</th>
                <th width="90"' . (abs($this->properties['sortBy']) == 2 ? ' class="gray"' : '') . '>' . $this->sortLink(2, 'DATE') . '</th>
                <th width="90"' . (abs($this->properties['sortBy']) == 3 ? ' class="gray"' : '') . '>' . $this->sortLink(3, 'TIME') . '</th>
                <th width="350"' . (abs($this->properties['sortBy']) == 4 ? ' class="gray"' : '') . '>' . $this->sortLink(4, 'DESCRIPTION') . '</th>
                <th width="20"' . (abs($this->properties['sortBy']) == 5 ? ' class="gray"' : '') . '>' . $this->sortLink(5, 'Original Map') . '</th>
                <th width="20"' . (abs($this->properties['sortBy']) == 6 ? ' class="gray"' : '') . '>' . $this->sortLink(6, 'Map ID') . '</th>
                <th width="115"></th>
            </tr>';

        return $html;
    }

}
