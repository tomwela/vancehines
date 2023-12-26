<?php namespace Admin;

use \ClickBlocks\Core,
    \ClickBlocks\Utils,
    \ClickBlocks\DB;

$start=microtime(true);

require_once(__DIR__ . '/../Application/connect.php');

function print_p($a){if(!isset($a))return;if(is_array($a))$k=print_r($a,true);else $k=$a;echo "<pre>$k</pre>";};

class Admin
{
  function __construct()
  {
    MercAuth::getInstance();
  }

}

$admin=new Admin();

if (!$_SESSION['is_admin'])
{
  include 'admin/admin.html';
}
else {
$home=!isset($_REQUEST['page'])&&!isset($_REQUEST['action']);
?>
<html>
  <head>
    <title>Admin Tool</title>
    <style type="text/css">
      .main{margin:0 auto;width:955px;}
      .auth{margin:90px auto 0;position:relative;width:480px;}
      .frm_login{margin:0 auto;position:relative;width:285px;}
      .row{float:left;padding:7px 0 4px 5px;width:100%;}
      .row a {float: left; width:100%;}
      .row label{color:#363636;display:block;font-size:12px;font-weight:bold;padding:0 0 9px;}
      .inp{border:1px solid #A5A5A5;float:left;padding:4px 0 5px 5px;vertical-align:middle;width:240px;}
      h3{ float: left;margin-bottom: 5px;margin-top: 5px}
      h4{ float: left;margin-bottom: 0px;margin-top: 0px}
      .hello{float:left;width:100%; border:solid 2px; border-width: 0 0 2px;margin-bottom: 30px;}
      .footer{border:solid 2px; border-width: 2px 0 0; margin-top: 30px;}
      a.button{ background-color: #dfdfdf; border: 1px solid;clear: both;color: black;float: left;margin: 2px 0;text-align: center;text-decoration: none;width: 300px;}
      .menu{float: left; width: 100%; height: auto;  margin-left: 40px;}
      .sync{border: 1px solid; width: 295px; margin: 0 0 15px;}
      .sync span{float:left;width: 122px;}
      .button.small{width: 100px; margin-left: 10px;}
      .button.medium{width: 250px; margin-left: 18px; margin-top: 10px;}
      .f_r {float:right !important;}
      .f_l {float:left !important;}
    </style>
  </head>
  <body>
    <div>
      <div class="hello" >
        <?if(!$home){?> <a class="button small f_l" href="<?=$_SERVER['PHP_SELF']?>">Home</a><?}?>
        <div style="float:right;margin-bottom: 5px;">
          <a class="button small f_r" href="<?=$_SERVER['PHP_SELF']?>?action=logout">Logout</a>
          Hello, <?=$_SESSION['name']?>.
        </div>
      </div>
<?if($home) {?>
      <div class="menu">
        <h3>Framework features:</h3>
        <div class="row mt11 w410">
          <a class="button" href="<?=$_SERVER['PHP_SELF']?>?page=requirements">Framework Requirements</a>
          <a class="button" href="<?=$_SERVER['PHP_SELF']?>?action=cleancache">Clean Cache</a>
          <a class="button" href="<?=$_SERVER['PHP_SELF']?>?page=logs">Project Logs</a>
        </div>
        <h3>Database features:</h3>
        <div class="row">

          <div class="row sync">
            <h4>Syncronization:</h4>
            <div style="clear: both;" ></div>
            <input id="sync_base" type="radio" name="dir" style="float:left;" value="out" checked="checked" >
            <span>Bin-to-Base</span>
            <input id="sync_bin" type="radio" name="dir" style="float:left;" value="in">
            <span>Base-to-Bin</span>
            <input id="sync_is_test" type="checkbox" style="float:left;" id="isTest" checked="checked" name="test" value="isTest">
            <span>Is Test</span>
            <input id="sync_use_merge" type="checkbox" style="float:left;" id="useMerge" name="merge" value="useMerge">
            <span>Use Merge</span>
            <a class="button medium" href="javascript:void(0);" onclick="sync();" >Syncronize</a>
          </div>
          <div style="clear: both;" ></div>
          <script type="text/javascript">
            function sync()
            {
              var p = '<?=$_SERVER['PHP_SELF']?>?action=generate&mode=sync';
              if (document.getElementById('sync_is_test').checked){
                if (document.getElementById('sync_base').checked) p = p + '&dir=testdb';
                if (document.getElementById('sync_bin').checked) p = p + '&dir=testbin';
              } else {
                if (document.getElementById('sync_bin').checked) p = p + '&dir=in';
                if (document.getElementById('sync_base').checked) p = p + '&dir=out';
              }
              if (document.getElementById('sync_use_merge').checked) p = p + '&merge=true';
              window.location.href = p;
            };
          </script>

          <div class="row sync">
            <h4>Generate:</h4>
            <a class="button medium" href="<?=$_SERVER['PHP_SELF']?>?action=generate&mode=xml">XML File</a>
            <a class="button medium" href="<?=$_SERVER['PHP_SELF']?>?action=generate&mode=classes">ORM Classes</a>
          </div>
          <a class="button" href="<?=$_SERVER['PHP_SELF']?>?page=db">Simple Database Control</a>
        </div>
        <h3>Other:</h3>
        <div class="row mt11 w410">
          <a class="button" href="<?=$_SERVER['PHP_SELF']?>?page=shell">Shell (with apache rights)</a>
          <a class="button" href="<?=$_SERVER['PHP_SELF']?>?page=info">PHP Info</a><br/>
          <a class="button" href="<?=$_SERVER['PHP_SELF']?>?page=wmsimport">Import WMS logs</a><br/>
        </div>

      </div>
<?}?>

<div style="float: left; width: 100%; height: auto;">
<?switch($_REQUEST['page'])
{
  case 'db':
    $db = new DBPage();
    $db->execute();
    break;
  case 'requirements':
   $req = Utils\Requirements::getInstance();
   $req->check();
   $req->printReport();
   break;
  case 'info':
   phpinfo();
   break;
  case 'shell':
    $log='';
    if(@$_REQUEST['cmd'])
    {
      exec($_REQUEST['cmd'],$answer,$return);
      $log.="\"".$_REQUEST['cmd']."\" executed with return code $return\n";
      foreach((array)$answer as $row)$log.="$row\n";
    }
      $url=$_SERVER['PHP_SELF']."?page=shell";
    ?>
    <H1>Shell</H1>
    <textarea name="log" style= "width:70%;height:300px;" ><?=$log?></textarea><br/>
    <form action="<?=$url?>" method='POST' name="cmdform" style="width:70%;"><br/>
    <input name="cmd" style="width:100%" value="<?=@$_REQUEST['cmd']?:''?>" onkeydown="function(e){if ((e || event).keyCode == 13){this.form.submit()} ;};" autofocus/>
    </form><?
    break;
  case 'logs':
    ?>
    <H1>Logs:</H1><?
    $logs = foo(Core\Logger::getInstance())->getLog();
    if(isset($_REQUEST['date']))
    {
      $url = $_SERVER['PHP_SELF']."?page=logs";
      ?>
      <a class='button' href='<?=$url?>'>All</a>
      <a class='button' href='<?=$url?>&date=<?=$_REQUEST['date']?>&mode=h'>HTML</a>
      <a class='button' href='<?=$url?>&date=<?=$_REQUEST['date']?>&mode=p'>print_r</a>
      <br/><br/><br/><br/><hr/>
      <?
      foreach($logs as $log)
        if($log['time']==$_REQUEST['date'])
        {
          switch($_REQUEST['mode'])
          {
            default:
            case 'h':
              echo $log['SESSION']['bug_content'];
              if(isset($log['SESSION']['bug_content']))break;
            case 'p':
              echo "<pre>".nl2br(htmlspecialchars(print_r($log,true)))."</pre>";
              break;
          }
        }
    }
    else
    {
      $times=array();
      foreach($logs as $log) $times[$log['time']]=$log['time'];
      rsort($times, SORT_STRING);
      foreach($times as $t)
      {
        $url = $_SERVER['PHP_SELF']."?page=logs&date={$t}";
        echo "<a class='button' href='$url'>{$t}</a>";
      }

    }
    break;
  case 'wmsimport':
    $dir = Core\IO::dir('files') . '/log_WMS/';
    if (!is_dir($dir)) {echo 'Dir doesnt exists'; return;}
    if (!$od = opendir($dir)) {echo 'Cant open dir'; return;}
    while(($file = readdir($od)) !== false)
    {
      if (substr($file, 0, 6) !== 'Order-') continue;
      $order_id = substr($file, 6, strpos($file, '_')-6);
      if (!count(foo(new DB\OrchestraOrders())->getOrderByID($order_id))) {echo 'order #'.$order_id.' was deleted<br/>'; continue;}
      $wmslog = foo(new DB\ServiceWMSLogs)->getByID();
      $wmslog->orderID = $order_id;
      $d = explode('-', substr($file, strpos($file, '_')+1, 10));
      $wmslog->date = $d[2].'-'.$d[1].'-'.$d[0].' '.'00:00:00';
      $data = file_get_contents($dir.$file);
      $st = str_replace("<", "", substr($data, strpos($data, '<ws_AddPickingOrderResult>')+26, 5));
      $wmslog->statusID = ($st=='true'?1:0);
      $f = fopen($dir.$file, 'r');
      if (!$f) {echo 'cant read file';return;}
      $l = false; $req = ''; $b = false; $res = '';
      while (($s = fgets($f, 4096)) !== false)
      {
        //echo $s.'<br/>';
        if (strpos($s, 'Response:')!==false) $l = false;
        if ($l) $req .= $s;
        if ($b) $res .= $s;
        if (strpos($s, 'Request:')!==false) $l = true;
        if (strpos($s, 'Response:')!==false) $b = true;
      }
      //echo '<br><br>'.$req.'<br>';
      //echo '<br><br>'.$res.'<br>';
      $wmslog->request = $req;
      $wmslog->response = $res;
      //break;
      //echo $file.' #'.$wmslogs->orderID.' d:'.$wmslogs->date.' r:'.$response.'<br/>';
      foo(new DB\ServiceWMSLogs)->save($wmslog);

    }
    break;

  default:
  unset($res);
  switch($_REQUEST['action'])
  {
    case 'cleancache':
      $reg = Core\Register::getInstance();
      $reg->cache->clean();
      $loader = $reg->loader;
      $loader->setClasses(array());
      $loader->fillCache();

      if ($reg->config->isDebug)
      echo 'Total: <b>' . count($loader->getClasses()) . '</b><br />Cached classes:<br/>';
      print_p($loader->getClasses());
      break;
    case 'generate':
      $orm = DB\ORM::getInstance();
      $orm->addAllDB();
      switch($_REQUEST['mode'])
      {
        case 'xml':$orm->generateXML('ClickBlocks\\DB');$res="XML was generated.";break;
        case 'classes':$orm->generateClasses();$res="ORM classes were generated";break;
        case 'sync':
          $config = Core\Register::getInstance()->config;
          foreach ($orm->getDBAliases() as $alias)
          {
            $cfg = $config[$alias];
            $file = Core\IO::dir('engine') . '/' . $alias . '.bin';
            $s = new DB\Sync\Synchronizer();
            $exceptionslookup = implode('|', array( 'categoryattributes$','productcategories$','userroles$', 'producttypes$'));
            $s->setInfoTables("/(^lookup(?!{$exceptionslookup}))|(^CustomCategories$)/i");
              if (!is_file($file))$s->out($cfg['dsn'], $cfg['dbuser'], $cfg['dbpass'])->in($file)->sync();
              switch (strtolower($_GET['dir']))
              {
                case 'in'     : $res = $s->out($cfg['dsn'], $cfg['dbuser'], $cfg['dbpass'])->in($file)->sync($_REQUEST['merge']); break;
                case 'out'    : if (is_file($file))
                                $res = $s->out($file)->in($cfg['dsn'], $cfg['dbuser'], $cfg['dbpass'])->sync($_REQUEST['merge']); break;

                case 'testdb' : if (is_file($file))
                                $res = $s->out($file)->in($cfg['dsn'], $cfg['dbuser'], $cfg['dbpass'])->sync($_REQUEST['merge'], true); break;
                case 'testbin': $res = $s->out($cfg['dsn'], $cfg['dbuser'], $cfg['dbpass'])->in($file)->sync($_REQUEST['merge'], true); break;
              }
          }
          break;
      }
      break;

  }
  // print_p($res);
  break;
}
?>
</div>
<div class="row footer">
  Page generated in <?=number_format((float)(microtime(true)-$start),5,'.',' ')?> s.
</div>
<?if($home) {?>
      </div>
    </body>
</html>
<?}?>
<? } ?>
