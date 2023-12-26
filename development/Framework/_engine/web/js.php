<?php

namespace ClickBlocks\Web;

use ClickBlocks\Core,
    ClickBlocks\Web\UI\Helpers;

abstract class JS
{
   private static $instance = null;

   protected $js = array();

   protected $core = array('ajax' => array('ajax.min.js'),
                           'controls' => array('controls.min.js'),
                           'validators' => array('validators.min.js', 'controls'),
//                           'autofill' => array('ajax', 'controls', 'autofill.min.js'),
//                           'uploadbutton' => array('controls', 'uploadbutton.min.js'),
//                           'ckeditor' => array('ckeditor/ckeditor.js'),
//                           'tinymce' => array('tinymce/jscripts/tiny_mce/tiny_mce.js'),
//                           'colorpicker' => array('controls', 'colorpicker/js_color_picker_v2.js'),
//                           'raphael' => array('raphael.min.js'),
                           'json' => array('json.min.js'),
//                           'imgeditor' => array('ajax', 'controls', 'raphael', 'imgeditor/imgeditor.js'),
//                           'datetimepicker' => array('datetimepicker/src/js/jscal2.js', 'datetimepicker/src/js/lang/en.js'),
                           );
   protected $coreDir = '/Framework/_engine/web/js/';
   protected $sfe = '';

   private function __construct()
   {
      $this->js['domready'] = array();
      $this->js['link'] = array();
      $this->js['head'] = array();
      $this->js['foot'] = array();

      $config = Core\Register::getInstance()->config;
      if ($config['staticFilesExpired']) $this->sfe = 'id=' . $config['staticFilesExpired'];      
   }

   public static function getInstance()
   {
      if (self::$instance === null)
      {
         $provider = Core\Register::getInstance()->config->jsProvider;
         self::$instance = new $provider();
      }
      return self::$instance;
   }

   public function add(Helpers\Script $obj, $type = 'domready')
   {
      if ($this->sfe && $type == 'link') $obj->src .= (strrpos($obj->src, '?') === false ? '?' : '&') . $this->sfe;
      if (!isset($this->js[$type])) throw new \Exception(err_msg('ERR_JS_1', array($type)));
      $this->js[$type][$obj->id] = ($obj->src) ? $obj->render() : $obj->text;
      return $this;
   }

   public function set($id, Helpers\Script $obj, $type = 'domready')
   {
      if (!isset($this->js[$type])) throw new \Exception(err_msg('ERR_JS_1', array($type)));
      $this->js[$type][$id] = ($obj->src) ? $obj->render() : $obj->text;
      return $this;
   }

   public function get($id, $type = 'domready')
   {
      if (!isset($this->js[$type])) throw new \Exception(err_msg('ERR_JS_1', array($type)));
      return $this->js[$type][$id];
   }

   public function delete($id, $type = 'domready')
   {
      if (!isset($this->js[$type])) throw new \Exception(err_msg('ERR_JS_1', array($type)));
      unset($this->js[$type][$id]);
      return $this;
   }

   public function render($type = 'domready')
   {
      if (!isset($this->js[$type])) throw new \Exception(err_msg('ERR_JS_1', array($type)));
      if (count($this->js[$type]) == 0) return;
      if ($type == 'domready')
      {
         return self::script($this->getCode('domready', $this->js[$type]));
      }
      else if ($type == 'foot') $js = implode(endl, array_reverse($this->js[$type]));
      else $js = implode(endl, $this->js[$type]);
      return ($type == 'link') ? $js : self::script($js);
   }

   public function addTool($tool, $dir = null)
   {
      if (isset($this->tools[$tool])) foreach ($this->tools[$tool] as $tool) $this->addTool($tool, $this->toolDir);
      else if (isset($this->core[$tool])) foreach ($this->core[$tool] as $tool) $this->addTool($tool, $this->coreDir);
      else
      {
         $config = Core\Register::getInstance()->config;
         if ($config['staticFilesExpired'] && $type == 'link') 
            $tool .= (strrpos($tool, '?') === false ? '?' : '&') . 'id=' . $config['staticFilesExpired'];      

         if (!Ajax::isAction())
         {
            $obj = new Helpers\Script($tool);
            $obj->src = (($dir) ? $dir : $this->toolDir) . $tool;
            $this->add($obj, 'link');
            if ($tool == 'ajax.js') $this->add(new Helpers\Script('ajax_init_view_states', 'ajax.initViewStates();'), 'foot');
         }
         else if ($tool != 'ajax.js')
         {
            $ajax = Ajax::getInstance();
            $ajax->tool(($dir ?: $this->toolDir) . $tool . ($this->sfe?"?{$this->sfe}":''), 0, true);
         }
      }
      return $this;
   }

   public function addTools(array $files)
   {
      foreach ($files as $file) $this->addTool($file);
      return $this;
   }

   abstract public function getCode($type, array $params = null);

   public static function script($script)
   {
      return foo(new Helpers\Script(null, $script))->render();
   }

   public function runScript($script, $time=0, $place=false)
   {
      if (!Ajax::isAction()) 
      {
        if($time>0) $script = "setTimeout(function(){ $script }, {$time});";
        $this->add(new Helpers\Script(uniqid("script"), $script, null));
        return $this;
      }
      else return Ajax::getInstance()->addAction($script, $time, $place);
    }

   public function consolelog()
   {
      if( ($numargs = func_num_args()) == 0) return $this;
      $data = array();
      foreach(func_get_args() as $param) $data[] = json_encode($param);
      $data = implode(',', $data);
      $this->runScript("console.log({$data});");
   }   

   public static function link($src, $charset = null)
   {
      if ($src[0] == '/') $src = Core\IO::url('js') . $src;
      $obj = new Helpers\Script();
      $obj->src = $src;
      $obj->charset = $charset;
      return $obj->render();
   }

   public static function goURL($url, $isNewWindow = false)
   {
      if (!$url) return;
      if (Ajax::isAjaxRequest())
      {
         echo ($isNewWindow) ? 'window.open("' . $url . '");' : 'window.location.assign("' . $url . '");';
         exit;
      }
      if ($isNewWindow) echo JS::script('window.open("' . $url . '");');
      else
      {
         try
         {
            header('Location: ' . $url);
         }
         catch (Exception $ex)
         {
            echo self::script('window.location.assign("' . $url . '");');
         }
      }
      exit;
   }

   public static function reload()
   {
      if (Ajax::isAjaxRequest()) echo 'window.location.reload(true);';
      else echo self::script('window.location.reload(true);');
      exit;
   }

   public static function wait($what, $script)
   {
      $u .= uniqid();
      $wh = str_replace( '.', '_', $what);
      return "wait{$wh}{$u} = setInterval(function(){ if(typeof {$what} !== 'undefined'){clearInterval(wait{$wh}{$u});{$script};}; }, 200);";
   }   
}

?>
