<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\DB;
use ClickBlocks\Web;

class WidgetMapSearch extends WidgetBackend
{
   public function __construct($id, $template = NULL)
   {
      parent::__construct($id, ($template) ?: 'map/searchList.html');
      $this->properties['source'] = 'ClickBlocks\DB\OrchestraMaps->getWidgetMapSearchData';
      $this->properties['searchvalue'] = NULL;
      $this->properties['sortBy'] = 1;
      $this->properties['superDistributorID'] = NULL;
      $this->properties['ExhastManufacturer'] = NULL;
      $this->properties['ExhastName']= NULL;
      $this->properties['BaffleType']= NULL;
      $this->properties['EngineSize']= NULL;
      $this->properties['perem']=NULL;
      $this->properties['otherMaps']=NULL;
      $this->properties['isCustomer']=NULL;
      $this->properties['customerID']=NULL;
      $this->properties['currentMap']=NULL;

   }

    public function init()
    {
        // don't fill the select menus with options on page load - comment out next 4 lines
        $this->get('searchMapBlock.allMaps.exmanuf')->options = foo(new DB\OrchestraMaps)->getDDBOptions('eManufacturer');
        $this->get('searchMapBlock.allMaps.exname')->options = foo(new DB\OrchestraMaps)->getDDBOptions('eName');
        $this->get('searchMapBlock.allMaps.baffletype')->options = foo(new DB\OrchestraMaps)->getDDBOptions('baffleType');
        $this->get('searchMapBlock.allMaps.enginesize')->options = foo(new DB\OrchestraMaps)->getDDBOptions('eSize');


        $this->get('searchMapBlock.allMaps.exmanuf')->onchange = $this->method('mapFilter', array( 'exmanuf' ));
        $this->get('searchMapBlock.allMaps.exname')->onchange = $this->method('mapFilter', array( 'exname' ));
        $this->get('searchMapBlock.allMaps.baffletype')->onchange = $this->method('mapFilter', array( 'baffletype' ));
        $this->get('searchMapBlock.allMaps.enginesize')->onchange = $this->method('mapFilter', array( 'enginesize' ));

        $this->get('searchMapBlock.pageSize')->onchange = $this->method('__set', array( 'pageSize', 'js::controls.$(\'' . $this->get('searchMapBlock.pageSize')->uniqueID . '\').value' ));
        $this->get('searchMapBlock.pageSize1')->onchange = $this->method('__set', array( 'pageSize', 'js::controls.$(\'' . $this->get('searchMapBlock.pageSize1')->uniqueID . '\').value' ));
        $this->get('searchMapBlock.customerMaps.search')->onclick = $this->method('search', array( "js::$('.search-input').val()" ));

    }

    public function search($value)
    {
        $this->properties['searchvalue'] = ($value == 'Search' ? NULL : trim($value));


        //force the select menu drop-downs to update based on the new search value
        $this->mapFilter();

        //$this->ajax->script("$('.radio-list .custom-radio').removeClass('checked');$('.radio-list .custom-radio:eq(1)').addClass('checked')"  );
    }

   public function mapFilter()
   {
    $e = $this->get('searchMapBlock.allMaps.exmanuf')->value;
    $x = $this->get('searchMapBlock.allMaps.exname')->value;
    $b = $this->get('searchMapBlock.allMaps.baffletype')->value;
    $n = $this->get('searchMapBlock.allMaps.enginesize')->value;
    $this->get('searchMapBlock.allMaps.exmanuf')->options    = foo(new DB\OrchestraMaps)->getDDBOptions('eManufacturer', null, $x, $b, $n, null, null, $this->properties['searchvalue'] );
    $this->get('searchMapBlock.allMaps.exname')->options     = foo(new DB\OrchestraMaps)->getDDBOptions('eName',         $e, null, $b, $n, null, null, $this->properties['searchvalue'] );
    $this->get('searchMapBlock.allMaps.baffletype')->options = foo(new DB\OrchestraMaps)->getDDBOptions('baffleType',    $e, $x, null, $n, null, null, $this->properties['searchvalue'] );
    $this->get('searchMapBlock.allMaps.enginesize')->options = foo(new DB\OrchestraMaps)->getDDBOptions('eSize',         $e, $x, $b, null, null, null, $this->properties['searchvalue'] );
    $this->properties['ExhastManufacturer'] = $e;
    $this->properties['ExhastName'] = $x;
    $this->properties['BaffleType'] = $b;
    $this->properties['EngineSize'] = $n;

    $this->update();
   }

    //public function mapFilterCustomer($value)
    //{
    //    if ( $value == 0 ) {
    //        $this->properties['searchvalue'] = '';
    //    } else {
    //        $this->properties['ExhastManufacturer'] = NULL;
    //        $this->properties['ExhastName'] = NULL;
    //        $this->properties['BaffleType'] = NULL;
    //        $this->properties['EngineSize'] = NULL;
    //
    //        $this->get('searchMapBlock.allMaps.exmanuf')->value = NULL;
    //        $x = $this->get('searchMapBlock.allMaps.exname')->value = NULL;
    //        $this->get('searchMapBlock.allMaps.baffletype')->value = NULL;
    //        $this->get('searchMapBlock.allMaps.enginesize')->value = NULL;
    //        $this->update();
    //    }
    //    $this->get('searchMapBlock.allMaps')->visible = !$value;
    //    $this->get('searchMapBlock.customerMaps')->visible = $value;
    //    $this->properties['isCustomer'] = $value;
    //    $this->ajax->script("$('.radio-list .custom-radio').removeClass('checked');$('.radio-list .custom-radio:eq(" . $value . ")').addClass('checked')");
    //}

    protected function getData()
    {
        $html = parent::getData();

        if ( !$this->properties['searchvalue'] ) {
            $text = 'Search';
        } else {
            $text = $this->properties['searchvalue'];
        }

        $this->get('searchMapBlock')->get('customerMaps')->get('txtSearch')->value = $text;
        $this->get('searchMapBlock.pageSize1')->value = $this->get('searchMapBlock.pageSize')->value = $this->properties['pageSize'];

        $this->tpl->rows = $this->rows;
        $this->tpl->sortBy = $this->properties['sortBy'];
        $this->tpl->perem = $this->properties['perem'];
        $this->tpl->otherMaps = $this->properties['otherMaps'];
        $html .= '<tr>
                    <th width="25"' . (abs($this->properties['sortBy']) == 1 ? ' class="gray"' : '') . '>' . $this->sortLink(1, 'Map ID') . '</th>
                    <th width="50"' . (abs($this->properties['sortBy']) == 2 ? ' class="gray"' : '') . '>' . $this->sortLink(2, 'Manufacturer') . '</th>
                    <th width="20"' . (abs($this->properties['sortBy']) == 3 ? ' class="gray"' : '') . '>' . $this->sortLink(3, 'Fitment') . '</th>
                    <th width="20"' . (abs($this->properties['sortBy']) == 4 ? ' class="gray"' : '') . '>' . $this->sortLink(4, 'Firmware') . '</th>
                    <th width="250"' . (abs($this->properties['sortBy']) == 5 ? ' class="gray"' : '') . '>' . $this->sortLink(5, 'Name') . '</th>
                    <th width="150"' . (abs($this->properties['sortBy']) == 6 ? ' class="gray"' : '') . '>' . $this->sortLink(6, 'Baffle') . '</th>
                    <th width="50"' . (abs($this->properties['sortBy']) == 7 ? ' class="gray"' : '') . '>' . $this->sortLink(7, 'Year') . '</th>
                    <th width="50"' . (abs($this->properties['sortBy']) == 8 ? ' class="gray"' : '') . '>' . $this->sortLink(8, 'Model') . '</th>
                    <th width="125"' . (abs($this->properties['sortBy']) == 9 ? ' class="gray"' : '') . '>' . $this->sortLink(9, 'Size') . '</th>
                    <th width="100"' . (abs($this->properties['sortBy']) == 10 ? ' class="gray"' : '') . '>' . $this->sortLink(10, 'Filename') . '</th>
                </tr>';

        return $html;
    }

    public function refresh()
    {
        $this->update();
    }

}
