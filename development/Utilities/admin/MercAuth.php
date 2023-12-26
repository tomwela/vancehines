<?
namespace Admin;
class MercAuth
{
  private static $instance=null;
  private $url = 'https://hg.saritasa.com:6060/';
  private function __clone(){}
  private function __construct()
  {
    if ($_REQUEST['action'] == 'logout')
    {
      $_SESSION['is_admin'] = false;
      header('Location: '.$_SERVER['PHP_SELF']);
      return;
    }
    if(\ClickBlocks\Core\Register::getInstance()->config->isLocal) $_SESSION['is_admin'] = true;
    if ($_SESSION['is_admin'] !== true)
      if (!$this->check()) 
      {
        //header('Location: '.$_SERVER['PHP_SELF']); 
        return;
      }
  }
  
  static function getInstance()
  {
    return self::$instance?:self::$instance=new self();
  }

  private function merccheck()
  {
    if(\ClickBlocks\Core\Register::getInstance()->config->isLocal) return true;
    $headers = array('Authorization: Basic '.base64_encode($_POST['login'].':'.$_POST['password']));
    try
    {
      $res = file_get_contents($this->url,false, stream_context_create(array('http' => array('method' => "GET", 'header' => implode("\r\n", $headers)))));
    }
    catch(\Exception $e){}
    return (bool)$res;
  }

  function check()
  {
    if($this->merccheck()) 
    {
      $_SESSION['is_admin'] = true;
      $_SESSION['name']     = $_POST['login'];      
    }  
    else $_SESSION['is_admin'] = false;
    return $_SESSION['is_admin'];
  }
}
?>
