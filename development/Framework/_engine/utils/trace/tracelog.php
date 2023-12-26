<?php 
namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Utils,
    ClickBlocks\MVC,
    ClickBlocks\Web,
    ClickBlocks\DB,
    ClickBlocks\Web\UI\Helpers;

class Trace extends Panel 
{  
    const DELIMITER = '@!';
    const FILE      = 'trace.log';

    private $tmpFile = null;

    public function __construct($id)
    {
        parent::__construct($id);
        $this->properties['tag'] = 'div';
        $this->tmpFile = Core\IO::dir('temp') . '/' . self::FILE;
    }
    public function init()
    {
      $this->reg->js->addTool('controls');      
    }
    public static function trace($var, $line = 0)
    {
      if (!self::isActive()) return;
      $file  = Core\IO::dir('temp') . '/' . self::FILE;
 
      $trace = backtrace();
      if($line) array_shift($trace);
      $data = array(
        'method' => $trace[0], 
        'data'   => $var,
        'trace'  => implode("\n", $trace)
        );
      $msg = (is_file($file) ? self::DELIMITER : '') . serialize($data);
      file_put_contents($file, $msg, FILE_APPEND);
    }
    
    public static function isActive()
    {
      return (\ClickBlocks\Core\Register::getInstance()->config->profiler || (int)$_COOKIE["profiler"]);
    }
    
    public function unload() {}

    public function render()
    {
        if (!$this->properties['visible']) return $this->invisible();
        
        $tpl = new Core\Template();
        $tpl->setTemplate('trace', __DIR__ . '/trace.html');
        $tpl->table = $this->getInnerHTML();
        $tpl->info  = $this->getInfo();
        return $tpl->render();
    }
    
    public function redraw(array $parameters = null)
    {
      $parent = $this->ajax->isSubmit() ? 'parent.' : '';
      $this->ajax->script($parent.'logTrace.addRows(\''.addslashes($this->getInnerHTML()).'\');',1);
      $this->ajax->insert($this->getInfo(), 'sys-info');
    }
    
    public function getInnerHTML()
    {
        $tpl = new Core\Template();
        $tpl->setTemplate('trace_table', __DIR__ . '/table.html');

        $params = $this->getLogData();
        foreach ($params as $name => $val) $tpl->{$name} = $val;

        return $tpl->render();
    }
    
    protected function repaint() {}

    private function getInfo()
    {
      $html  = '<ul>';
      $html .= '<li style="color:#C07041">Method: <span style="color:silver">'.(Web\Ajax::isAction() ? Core\Register::getInstance()->fv['ajaxfunc'] : 'Init').'</span></li>';
      $html .= '<li style="color:#C07041">Memory Use: <span style="color:silver">'.number_format( Core\Logger::getMemoryUsage() / 1024 / 1024, 2).' Mb</span></li>';
      $html .= '<li style="color:#C07041">Execution Time: <span style="color:silver">'.Core\Logger::getExecutionTime().'</span></li>';
      $html .= '<li style="color:#C07041">Request Time: <span style="color:silver">'.Core\Logger::getRequestTime().'</span></li>';
      $html .= '</ul>';
      return $html;
    }
    
    private function getLogData()
    {    
        if (file_exists($this->tmpFile))
        {
            $text = file_get_contents($this->tmpFile);
            unlink($this->tmpFile);
            $list = explode(self::DELIMITER, $text);
            foreach ($list as $n => &$value) $value = unserialize($value);
        } else $list = array();

        return array('info' => $this->getInfo(), 'db' => DB\DB::getStatistic(), 'trace' => $list);
    }



}
?>
