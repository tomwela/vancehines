<?php

namespace ClickBlocks\MVC\Backend;

use ClickBlocks\Core;
use ClickBlocks\DB;
use ClickBlocks\Utils;
use ClickBlocks\Web;
use ClickBlocks\Web\UI\Helpers;
use ClickBlocks\Web\UI\POM;

class PageMapSearch extends Backend
{
    public function __construct()
    {
        parent::__construct('map/search.html');
    }

    public function init()
    {
        parent::init();
        $this->head->name = 'Map Search';
    }

    public function MapAdd()
    {
        $popup = $this->get('MapAdd');
        $popup->tpl->userID = null;

        $popup->get('nameMap')->value = null;
        $popup->get('description')->value = null;
        $popup->get('make')->value = null;
        $popup->get('model')->value = null;
        $popup->get('year')->value = null;
        $popup->get('eManufacturer')->value = null;
        $popup->get('eName')->value = null;
        $popup->get('baffleType')->value = null;
        $popup->get('eSize')->value = null;
        $popup->show();
    }

    public function showTablesOtherMap($mapID)
    {
        $this->get('searchMapBlock')->visible = 0;
        $this->get('otherMapsTable')->visible = 1;

        $map = foo(new DB\ServiceMaps)->getByID($mapID);
        $this->get('otherMapsTable.editOtherMaps.editInputMap')->text = $map->name;
        $this->get('otherMapsTable.editOtherMaps.mapDescription')->text = $map->description;
        $this->get('otherMapsTable.editOtherMaps.mapMake')->text = $map->make;
        $this->get('otherMapsTable.editOtherMaps.mapModel')->text = $map->model;
        $this->get('otherMapsTable.editOtherMaps.mapYear')->text = $map->year;
        $this->get('otherMapsTable.editOtherMaps.mapeManufacturer')->text = $map->eManufacturer;
        $this->get('otherMapsTable.editOtherMaps.mapeName')->text = $map->eName;
        $this->get('otherMapsTable.editOtherMaps.mapbaffleType')->text = $map->baffleType;
        $this->get('otherMapsTable.editOtherMaps.mapeSize')->text = $map->eSize;
        $this->tpl->otherMapID         = $mapID;
        $this->tpl->otherMapApiVersion = $map->apiVersion;
        $this->tpl->otherMapYear       = $map->year;


        $ecmFirmware = foo(new DB\OrchestraCustomers())->getECMFirmwareByMapID($mapID);
        $mapStructure = getMapStructure( $ecmFirmware );

        $omTableInfo = array();
        $groupList = array();
        foreach ($mapStructure as $k => $v) {
            unset($mPart);
            $tableName = '\ClickBlocks\DB\Orchestra' . $v['DBTableName'];
            $mPart = foo(new $tableName)->getByQuery('mapID = ' . $map->mapID, null, null, 'row');
            //$val = 'om' . $k . 'Updated';
            //$this->tpl->$val = date('m/d/Y', strtotime($mPart['updated']));


            $groupList[ $k ] = $v['tablegroup'];

            //dynamically create an array of mapStructure and db related info and pass to customers.html
            $omTableInfo[ $k ]['name'] = $k;
            $omTableInfo[ $k ]['group'] = $v['tablegroup'];
            $omTableInfo[ $k ]['dataType'] = $v['dataType'];
            $omTableInfo[ $k ]['title'] = $v['title'];
            $omTableInfo[ $k ]['ShowInCSR'] = $v['ShowInCSR'];


            //If there is no date in the db, the date function will return dec 1969 which is not what we want.
            //Only pass records that have actual dates.
            //This field is used to filter tables displayed in the customer detail page.
            if (isset($mPart['updated'])) {
                $omTableInfo[ $k ]['Updated'] = date('m/d/Y', strtotime($mPart['updated']));
            }

        } //foreach

        //pass data to customers.html -> variable name is  $omTableInfo which is an array
        //ksort($omTableInfo);
        $info = 'omTableInfo';
        $this->tpl->$info = $omTableInfo;

        $groupInfo = 'groupList';
        $this->tpl->$groupInfo = array_unique($groupList);

    }


    public function backToSearchMaps()
    {
        $this->get('searchMapBlock')->visible = 1;
        $this->get('otherMapsTable')->visible = 0;
        $this->get('searchMapBlock')->update();
    }

    public function mapDelete($id = 0, $flag = 0)
    {
        $map = foo(new DB\ServiceMaps)->getByID($id);
        if ($map->isOriginalMap == 1) {
            return;
        }
        $popup = $this->get('msgDelete');
        $popup->get('btnYes')->visible = 1;
        $popup->get('btnNo')->visible = 1;
        $popup->get('btnOk')->visible = 0;
        if ($flag) {
            $popup->tpl->title = "Delete Map";
            $popup->tpl->text = 'You are going to delete ' . $map->name . '. <br> Continue?';
            $popup->get('btnYes')->onclick = "ajax.doit('->mapDelete'," . (int)$id . ")";
            $popup->show();
        } else {
            $accID = foo(new DB\OrchestraMaps())->deleteMap((int)$id);
            $this->get('mapSearch')->update();
            $popup->hide();
            $flags = true;
            $this->ShowPopupMessage("Map " . $map->name . " deleted successfully", $flags);
        }
    }

    protected function ShowPopupMessage($msg, $flags = null)
    {
        $popup = $this->get('msg');
        $popup->tpl->text = $msg;
        $popup->get('btnYes')->visible = 0;
        $popup->get('btnNo')->visible = 0;
        $popup->get('btnOk')->visible = 0;
        $popup->tpl->title = '';
        if ($flags) {
            $popup->get('btnOk')->visible = 1;
            $popup->get('btnOk')->onclick = 'popup.isClicked = true;popup.hide(\'' . $popup->uniqueID . '\');';
            $flags = false;
        }
        $popup->show(0.5, true, 200, 2000);
    }

    public function showPopupWithValues($valKey, $otherMapID = null, $editMap = null)
    {
        $mapID = $otherMapID;

        $ecmFirmware = foo(new DB\OrchestraCustomers())->getECMFirmwareByMapID($mapID);
        if (!$ecmFirmware) {
            $this->ShowPopupMessage($ecmFirmware);

            return;
        }

        $m = foo(new DB\ServiceMaps())->getByID($mapID);
        $mStructure = getMapStructure( $ecmFirmware );
        $orchestraName = '\ClickBlocks\DB\Orchestra' . $mStructure[ $valKey ]['DBTableName'];
        $value = foo(new $orchestraName)->getByQuery('mapID = ' . $mapID, null, null, 'row');
        $data = json_decode($value['data'], 1);


        // count all elements of a multidimensional array
        $thisNumbCols = count($data);
        $thisNumbRows = (count($data, 1) - $thisNumbCols) / $thisNumbCols;
        $gridSize = $thisNumbRows . "x" . $thisNumbCols;
        //$this->ShowPopupMessage($gridSize);
        //return;


        //bug fix - force data type
        if (is_float($data) || is_int($data) || is_numeric($data)) {
            settype($data, "string");
        }

        $dot = (is_float($mStructure[ $valKey ]['min']) || is_float($mStructure[ $valKey ]['max'])) ? '.' : '';

        $minus = ($mStructure[ $valKey ]['min'] < 0 || $mStructure[ $valKey ]['max'] < 0) ? '-' : '';
        $expression = "/[^0-9{$dot}{$minus}]/g";


        // FLAT
        if ($mStructure[ $valKey ]['dataType'] == 'flat') {
            $popup = $this->get('mapSingleInput');
            $popup->tpl->enDisID = reset($value);
            $popup->tpl->keyTable = $valKey;
            $popup->tpl->title = $mStructure[ $valKey ]['title'];
            $popup->tpl->base = $mStructure[ $valKey ]['default'];
            $popup->tpl->min = $mStructure[ $valKey ]['min'];
            $popup->tpl->max = $mStructure[ $valKey ]['max'];
            $expression = ($valKey == 'EngineDisplacement') ? '/[^\.0-9]/g' : '/[^0-9-]/g';
            $popup->get('popupContainer')->get('reg_currValue')->onkeyup = "this.value = this.value.replace($expression,'');";
            $popup->get('popupContainer')->get('reg_currValue')->value = $data;
            $popup->get('popupContainer')->get('reg_currValue')->removeClass('error');
//      $popup->get('saveButton')->visible=1;
//      $popup->get('compareLink')->visible=1;


            /*$this->ajax->script('controls.focus(\'' . $popup->get('popupContainer.reg_currValue')->uniqueID . '\');');
            $this->ajax->script($js, 200);*/


//      $popup->get('popupContainer')->get('reg_currValue')->readonly = false;
//      if($otherMapID)
//      {
            $popup->get('saveButton')->visible = 0;
            $popup->get('popupContainer')->get('reg_currValue')->readonly = true;
            $popup->get('compareLink')->visible = 0;
//      }
//      else
//      {
//        $this->ajax->script("$('#{$popup->get('popupContainer')->get('reg_currValue')->uniqueID}').select();",100);
//      }

        } elseif ($mStructure[ $valKey ]['dataType'] == 'table') {
            $popup = $this->get('mapMultiInput');
            $popup->tpl->keyTable = $valKey;
            $popup->tpl->title = $mStructure[ $valKey ]['title'];
            $popup->tpl->min = $mStructure[ $valKey ]['min'];
            $popup->tpl->max = $mStructure[ $valKey ]['max'];
            $popup->tpl->onKeyUpScript = "this.value = this.value.replace($expression,'');";
            $popup->tpl->maxLength = strlen($mStructure[ $valKey ]['max']);
            $popup->tpl->rows = $data;


//      $popup->get('saveButton')->visible=1;
//      $popup->get('compareLink')->visible=1;
//      $this->ajax->script("$('.multiInputData').removeAttr('readonly')");
            $popup->get('saveButton')->visible = 0;
            $this->ajax->script("$('.multiInputData').attr('readonly', 'readonly')");
            $popup->get('compareLink')->visible = 0;
//        $this->ajax->script("$('.multiInputData').first().select();", 100);


        } // MATRIX
        elseif ($mStructure[ $valKey ]['dataType'] == 'matrix') {
            $popup = $this->get('mapSSInput');

            $popup->get('popupContainer.xHeader')->text = $mStructure[ $valKey ]['axis']['X']['title'];
            $popup->get('popupContainer.xHeader')->update();
            $popup->get('popupContainer.yHeader')->text = $mStructure[ $valKey ]['axis']['Y']['title'];
            $popup->get('popupContainer.yHeader')->update();
            $popup->tpl->keyTable = $valKey;
            $popup->tpl->title = $mStructure[ $valKey ]['title'];
            $popup->tpl->min = $mStructure[ $valKey ]['min'];
            $popup->tpl->max = $mStructure[ $valKey ]['max'];

            $popup->tpl->gridSize = $gridSize;

            $popup->tpl->rows = $data;
            $popup->tpl->arrMatrixCol = array_keys($data);
            $arrMatrixRow = array_values($data);
            $popup->tpl->arrMatrixRow = array_keys($arrMatrixRow[0]);
            $popup->tpl->onKeyUpScript = "this.value = this.value.replace($expression,'');";
            ($mStructure[ $valKey ]);

            $popup->tpl->maxLength = strlen($mStructure[ $valKey ]['max']) + 2;

            $popup->get('popupContainer')->get('valCols')->value = implode(',', $popup->tpl->arrMatrixCol);

            // turn the following features off
            $popup->get('saveButton')->visible = 0;
            $popup->get('compareLink')->visible = 0;
            $popup->get('shiftClick')->visible = 0;

            // add color to matrix type maps
            $this->ajax->script("$.getScript('/Application/Backend/common/jetColorMap.min.js');");

            //$this->ajax->script("$('.ssInputData').removeAttr('readonly')");       // make cells editable
            $this->ajax->script("$('.ssInputData').attr('readonly', 'readonly')");   // make cells un-editable
            //$this->ajax->script("$('.ssInputDataTd').attr('onclick','').unbind('click')", 100);  // dont know what this does
            //$$this->ajax->script("$('.ssInputData').first().select();", 100);   // place cursor,focus on 1st cell
        }
        $popup->show();
    }

    public function editMap()
    {
        $map = $this->map;
        $this->get('editInputMap')->value = $map->name;
        $this->get('mapDescription')->text = $map->description;
        $this->get('editMaps1')->update()->visible = 1;
        $this->get('editMaps')->update()->visible = 0;
    }

    public function updateTab($index)
    {
        $this->tpl->activetab = $index;

        return true;
    }

}

?>