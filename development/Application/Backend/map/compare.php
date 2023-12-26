<?php

namespace ClickBlocks\MVC\Backend;

use ClickBlocks\Core,
    ClickBlocks\DB,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers,
    ClickBlocks\Web\UI\POM,
    ClickBlocks\Utils;

class PageMapCompare extends Backend
{
  public function __construct()
  {
    parent::__construct('map/compare.html');
    $this->customer = foo(new DB\ServiceCustomers())->getByID($this->fv['customerID']);
    $this->map = foo(new DB\ServiceMaps())->getByID($this->customer->currentMap);
  }

  public function updateTab($index)
  {
      $this->tpl->activetab=$index;
    return true;
  }

  public function init()
  {
    parent::init();
    $map = foo(new DB\ServiceCustomers)->getByID($this->fv['customerID'])->maps;
    $this->tpl->mapName = $this->map->name;
    $this->get('mapSearch')->perem=true;
    $this->get('mapSearch')->otherMaps=NULL;
    $this->fillCustomerDetailData($this->customer);
    $this->tpl->customerID = $this->customer->customerID;
    $panelCustomerDetail = $this->get('customerDetail');  
    $this->tpl->mapDataTitle = $this->fv['title'];
    $this->valKey = $this->fv['key'];

    $mStructure = getMapStructure( $this->customer->ecmFirmware );
    
    $orchestraName = '\ClickBlocks\DB\Orchestra' . $mStructure[$this->valKey]['DBTableName'];
    $value = foo(new $orchestraName)->getByQuery('mapID = ' . $this->map->mapID, null, null,'row');
    if($mStructure[$this->valKey]['dataType'] == 'flat'){
      $data = json_decode($value['data'], 1);
      $this->tpl->dataType = 'flat';    
      $this->tpl->mapDataValue = $data;          
    }
    elseif($mStructure[$this->valKey]['dataType'] == 'table'){
      $data = json_decode($value['data'], 1);
      $this->tpl->dataType = 'table';
      $this->tpl->rows = $data;
      $this->tpl->Centigrade = $mStructure[$this->valKey]['table']['titles']['title'];
      $this->tpl->Steps = $mStructure[$this->valKey]['table']['values']['title'];
    }
    elseif($mStructure[$this->valKey]['dataType'] == 'matrix'){
      if($this->valKey == "ThrtottleProgrsivity")
      {
        $orchestraName = '\ClickBlocks\DB\Orchestra' . $mStructure['ThrtottleProgrsivity2']['DBTableName'];
        $ThrtottleProgrsivity2 = foo(new $orchestraName)->getByQuery('mapID = ' . $this->map->mapID, null,null,'row');
        $dataThrProg2 = json_decode($ThrtottleProgrsivity2['data'], 1);
        if (!$value){
          $dataThr = '{"0":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"5":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"20":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"25":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"30":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"40":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"55":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"70":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"100":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""}}';
          $dataThrProg2 = json_decode($dataThr, 1);
        }
        $this->tpl->rowsThrProg = $dataThrProg2;
        $this->tpl->arrMatrixColThrProg = array_keys($dataThrProg2);
        $arrMatrixRowThrProg = array_values($dataThrProg2);
        $this->tpl->arrMatrixRowThrProg = array_keys($arrMatrixRowThrProg[0]);
        $this->get('valColsThrProg')->value = implode(',', $this->tpl->arrMatrixColThrProg);
      }
      $data = json_decode($value['data'], 1);
      $this->tpl->dataType = 'matrix';
      if (!$value){
          $dataThr = '{" 0":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"2.5":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""}," 5":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"20":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"25":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"35":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"45":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"60":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"100":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""}}';
          $data = json_decode($dataThr, 1);
      }
      $this->tpl->rows = $data;
      $this->tpl->arrMatrixCol = array_keys($data);
      $arrMatrixRow = array_values($data);
      $this->tpl->arrMatrixRow = array_keys($arrMatrixRow[0]);
      $this->get('valCols')->value = implode(',', $this->tpl->arrMatrixCol);
    }
    $this->head->name = "Compare Map";
  }

  public function compareTo($name, $id, $model)
  { 
    $this->tpl->ReferenceShow = True;
    $this->get('searchMapBlock')->visible=0;
    $this->get('compareMapBlock')->visible=1;
    $this->tpl->compareMapName = $name;
    $this->tpl->compareMapID = $id;
    $this->tpl->setToCurrentMap = $model;
    $this->valKey = $this->fv['key'];
    
    
    $mStructure = getMapStructure( $this->customer->ecmFirmware );
    
    $orchestraName = '\ClickBlocks\DB\Orchestra' . $mStructure[$this->valKey]['DBTableName'];
    $value = foo(new $orchestraName)->getByQuery('mapID = ' . $id, null, null, 'row');
    if($mStructure[$this->valKey]['dataType'] == 'flat'){
      $this->tpl->dataTypeCompare = 'flat';
      $data = json_decode($value['data'], 1);
      $this->tpl->compareMap = $data;
      $this->tpl->referenceMap = round($data - $this->tpl->mapDataValue, 2);
    }
    elseif($mStructure[$this->valKey]['dataType'] == 'table'){
      $data = json_decode($value['data'], 1);
      $this->tpl->dataTypeCompare = 'table';
      $this->tpl->rowsCompare = $data;      
      foreach ((array)$data as $key => $valueReference) {
        $reference[$key] = round($this->tpl->rowsCompare[$key] - $this->tpl->rows[$key], 2);
      }
      $this->tpl->rowReference = $reference;
    }
    elseif($mStructure[$this->valKey]['dataType'] == 'matrix'){
      if($this->valKey == "ThrtottleProgrsivity")
      {
        $orchestraName = '\ClickBlocks\DB\Orchestra' . $mStructure['ThrtottleProgrsivity2']['DBTableName'];
        $ThrtottleProgrsivity2 = foo(new $orchestraName)->getByQuery('mapID = ' . $id, null,null,'row');
        $dataThrProg2 = json_decode($ThrtottleProgrsivity2['data'], 1);
        if (!$dataThrProg2){
          $dataThr = '{"0":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"5":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"20":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"25":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"30":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"40":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"55":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"70":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"100":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""}}';
          $dataThrProg2 = json_decode($dataThr, 1);
        }
        $this->tpl->rowsCompareThrProg = $dataThrProg2;
        $this->tpl->arrMatrixColCompareThrProg = array_keys($dataThrProg2);
        $arrMatrixRowCompareThrProg = array_values($dataThrProg2);
        $this->tpl->arrMatrixRowCompareThrProg = array_keys($arrMatrixRowCompareThrProg[0]);
        $this->get('valColsThrProg')->value = implode(',', $this->tpl->arrMatrixColCompareThrProg);
        foreach($this->tpl->arrMatrixRowThrProg as $row){ 
          foreach($this->tpl->arrMatrixColThrProg as $col){ 
            if ($dataThr) {
              $referenceThrProg[$col][$row] = $this->tpl->rowsCompareThrProg[$col][$row]; 
            }
            else{
              $referenceThrProg[$col][$row] = round($this->tpl->rowsCompareThrProg[$col][$row] - $this->tpl->rowsThrProg[$col][$row], 2); 
            }
          } 
        }
        $this->tpl->rowReferenceThrProg = $referenceThrProg;

      }
      $data = json_decode($value['data'], 1);
      if (!$data){
          $dataThr = '{"0":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"2.5":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"5":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"20":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"25":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"35":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"45":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"60":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""},"100":{"700":"","750":"","1000":"","1250":"","1500":"","1750":"","2000":"","2500":""}}';
          $data = json_decode($dataThr, 1);
        }
      $this->tpl->dataTypeCompare = 'matrix';
      $this->tpl->rowsCompare = $data;
      $this->tpl->arrMatrixColCompare = array_keys($data);
      $arrMatrixRowCompare = array_values($data);
      $this->tpl->arrMatrixRowCompare = array_keys($arrMatrixRowCompare[0]);
      $this->get('valCols')->value = implode(',', $this->tpl->arrMatrixColCompare);
      foreach($this->tpl->arrMatrixRow as $row){ 
        foreach($this->tpl->arrMatrixCol as $col){ 
          if ($dataThr) {
            $reference[$col][$row] = $this->tpl->rowsCompare[$col][$row]; 
          }
          else{
            $reference[$col][$row] = round($this->tpl->rowsCompare[$col][$row] - $this->tpl->rows[$col][$row], 2); 
          }
        } 
      }   
      $this->tpl->rowReference = $reference;

    }
    $this->get('CrossReferense')->update();
  }

  public function setToCurrent()
  { 
    $this->tpl->ReferenceShow = False;
    $this->valKey = $this->fv['key'];



    $mStructure = getMapStructure( $this->customer->ecmFirmware );
    
    
    $orchestraName = '\ClickBlocks\DB\Orchestra' . $mStructure[$this->valKey]['DBTableName'];
    $serviceName = '\ClickBlocks\DB\Service' . $mStructure[$this->valKey]['DBTableName'];   
    if($mStructure[$this->valKey]['dataType'] == 'flat'){
      $this->tpl->mapName = $this->tpl->compareMapName;
      $this->tpl->mapDataValue = $this->tpl->compareMap;

    }
    elseif($mStructure[$this->valKey]['dataType'] == 'table'){
      $this->tpl->mapName = $this->tpl->compareMapName;
      $this->tpl->rows = $this->tpl->rowsCompare;
      
    }
    elseif($mStructure[$this->valKey]['dataType'] == 'matrix'){
      $this->tpl->mapName = $this->tpl->compareMapName;
      $this->tpl->rows = $this->tpl->rowsCompare;
      if($this->valKey == 'ThrtottleProgrsivity')
         $this->tpl->rowsThrProg = $this->tpl->rowsCompareThrProg;
    }
    $currentMapData = foo(new $orchestraName)->getByQuery('mapID = ' . $this->map->mapID, null, null, 'row');
    $compareMapData = foo(new $orchestraName)->getByQuery('mapID = ' . $this->tpl->compareMapID, null, null, 'row');
    $currentMapPart = foo(new $serviceName)->getByID(reset($currentMapData));
    $currentMapPart->updated = 'NOW()';
    $currentMapPart->updatedBy = $this->user->userID;
    $currentMapPart->data = $compareMapData['data'];
    $currentMapPart->save();
    $this->get('searchMapBlock')->visible=1;
    $this->get('compareMapBlock')->visible=0;
    $this->get('CurrentMap')->update();
    $this->get('CompareTo')->update();
    $this->tpl->referenceMap = NULL;
    $this->tpl->rowReference = $reference;
    $this->get('CrossReferense')->update();
    $this->ajax->script('$(".tabBox ul li").removeClass("active");$(".tabBox ul li:eq(0)").addClass("active");');
  }

  public function backToSearch()
  {
  	$this->get('searchMapBlock')->visible=1;
  	$this->get('compareMapBlock')->visible=0;
    $this->tpl->rowReference = NULL;
    $this->tpl->rowReferenceThrProg = NULL;
    $this->tpl->referenceMap = NULL;
    $this->get('CrossReferense')->update();
  }

  private function fillCustomerDetailData($objCustomer, $isEdit = false)
  {
    $panelCustomerDetail = $this->get('customerDetail');
    $panelData = $panelCustomerDetail->get('customerDetailDisplay');
    $name = 'Label';
    $type = 'text';
    if($isEdit){
      $panelData = $panelCustomerDetail->get('customerDetailEdit');
      $name = '';
      $type = 'value';
    }    
    $panelData->get('customerMake' . $name)->{$type} = $objCustomer->make;
    $panelData->get('customerModel' . $name)->{$type} = $objCustomer->model;
    $panelData->get('customerYear' . $name)->{$type} = $objCustomer->year;
  }

}

?>