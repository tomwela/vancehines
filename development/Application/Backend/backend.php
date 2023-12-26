<?php

namespace ClickBlocks\MVC\Backend;

use ClickBlocks\Core,
    ClickBlocks\DB,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers,
    ClickBlocks\Web\UI\POM;

class Backend extends \ClickBlocks\MVC\VanceAndHines
{
  public $user = null;
  public $ie   = null;

  public function __construct($template = null)
  {
      if ($template != '')
      {
         $temp = Core\IO::dir('backend') . '/' . $template;
         if (is_file($temp)) $template = $temp;
      }
      parent::__construct($template);
    preg_match('/MSIE ([0-9]?)/i', $_SERVER['HTTP_USER_AGENT'], $arr);
    $this->ie = (int)$arr[1];
    
    $this->user = new DB\Users($_SESSION['__VH__']['userID']);
  }

  public function init()
  {
    parent::init();
    $this->tpl->_cmsCall = $this->config->cms;

    // params to Helpers\Meta (id, name, content, http-equiv, charset)
    // the first 3 are required by bootstrap
    $this->head->addMeta(new Helpers\Meta(null, null, null, null, $this->config->charset));    
    $this->head->addMeta(new Helpers\Meta(null, null, 'IE=edge', 'X-UA-Compatible', null));
    $this->head->addMeta(new Helpers\Meta(null, 'viewport', 'width=device-width, initial-scale=1', null, null));  
    
    
    $this->head->addMeta(new Helpers\Meta(null, 'description', 'Customer Support Site for FP3', null, null));    

 
    //$this->css->add(new Helpers\Style('bootstrap', null, Core\IO::url('css') . '/dist/css/bootstrap.min.css'), 'link'); //Bootstrap v3.3.1
    $this->css->add(new Helpers\Style('bootstrap', null, Core\IO::url('css') . '/bootstrap-3.3.6-dist/css/bootstrap.min.css'), 'link'); //Bootstrap v3.3.6
    $this->css->add(new Helpers\Style('generalstylesmain', null, Core\IO::url('css') . '/vhfp3.min.css'), 'link');


    $this->js->addTool('ajax')->addTool('json')->addTool('jquery');
    $this->js->add(new Helpers\Script('bsjs', null, Core\IO::url('css') . '/bootstrap-3.3.6-dist/js/bootstrap.min.js'), 'link');
    $this->js->add(new Helpers\Script('main', null, Core\IO::url('js') . '/main.min.js'), 'link');
    
    $this->initActiveTab();
    $this->initGeneralInfo();
  }
  
  private function initActiveTab()
  {
    $menu = array('' => 1, 'map/queue' => 1, 'map/compare' => 1, 'customers' => 1, 'map/search' => 2, 'users' => 3);
    $uri = $this->uri->path[1];
    if($uri == 'map')
    {
      $uri .= !empty($this->uri->path[2]) ? "/{$this->uri->path[2]}" : '';
    }
    //print_r($uri);
    $this->body->tpl->{'class' . $menu[$uri]} = 'active';
  }
  
  private function initGeneralInfo()
  {
    $this->body->tpl->userName = $this->user->firstName . ' '. $this->user->lastName;
    $this->body->tpl->userRole = $this->user->role;
  }

   public function access()
   { 
     $this->noAccessURL = $this->config->cms . '/login?redirect=' . urldecode($this->uri->getURI());
      return $this->user->userID;
   }

  public function logout()
  {
    unset($_SESSION['__VH__']['userID']);

    Web\JS::goURL($this->config->cms);
  }

  public static function getUSAStates()
  {
    return array(
      'AL' => 'Alabama',
      'AK' => 'Alaska',
      'AZ' => 'Arizona',
      'AR' => 'Arkansas',
      'CA' => 'California',
      'CO' => 'Colorado',
      'CT' => 'Connecticut',
      'DC' => 'D.C.',
      'DE' => 'Delaware',
      'FL' => 'Florida',
      'GA' => 'Georgia',
      'HI' => 'Hawaii',
      'ID' => 'Idaho',
      'IL' => 'Illinois',
      'IN' => 'Indiana',
      'IA' => 'Iowa',
      'KS' => 'Kansas',
      'KY' => 'Kentucky',
      'LA' => 'Louisiana',
      'ME' => 'Maine',
      'MD' => 'Maryland',
      'MA' => 'Massachusetts',
      'MI' => 'Michigan',
      'MN' => 'Minnesota',
      'MS' => 'Mississippi',
      'MO' => 'Missouri',
      'MT' => 'Montana',
      'NE' => 'Nebraska',
      'NV' => 'Nevada',
      'NH' => 'New Hampshire',
      'NJ' => 'New Jersey',
      'NM' => 'New Mexico',
      'NY' => 'New York',
      'NC' => 'North Carolina',
      'ND' => 'North Dakota',
      'OH' => 'Ohio',
      'OK' => 'Oklahoma',
      'OR' => 'Oregon',
      'PA' => 'Pennsylvania',
      'RI' => 'Rhode Island',
      'SC' => 'South Carolina',
      'SD' => 'South Dakota',
      'TN' => 'Tennessee',
      'TX' => 'Texas',
      'UT' => 'Utah',
      'VT' => 'Vermont',
      'VA' => 'Virginia',
      'WA' => 'Washington',
      'WV' => 'West Virginia',
      'WI' => 'Wisconsin',
      'WY' => 'Wyoming'
    );
  }

  protected function getFileError($errno)
  {
    switch ($errno)
    {
      case UPLOAD_ERR_INI_SIZE:
        $message = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
        break;
      case UPLOAD_ERR_FORM_SIZE:
        $message = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
        break;
      case UPLOAD_ERR_PARTIAL:
        $message = 'The uploaded file was only partially uploaded.';
        break;
      case UPLOAD_ERR_NO_FILE:
        $message = 'No file was uploaded.';
        break;
      case UPLOAD_ERR_NO_TMP_DIR:
        $message = 'Missing a temporary folder.';
        break;
      case UPLOAD_ERR_CANT_WRITE:
        $message = 'Failed to write file to disk.';
        break;
      case UPLOAD_ERR_EXTENSION:
        $message = 'File upload stopped by extension.';
        break;
      case 10:
        $message = 'Incorrect file type';
        break;
      case 11:
        $message = 'File size too big';
      default:
        $message = 'File uploading is failed (errcode: ' . intval($errno)  . ')';
        break;
    }
    return $message;
  }



}

