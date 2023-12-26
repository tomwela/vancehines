<?php
namespace Admin;

use ClickBlocks\Core,
    ClickBlocks\DB,
    ClickBlocks\Utils;
?>

<?php
class DBPage
{
  private function table($a,$html=false)
  { ?>
 Count of rows: <?=count($a)?>;<br/>
 <table border="1">
<?  foreach((array)$a as $k=>$row)
    {
      foreach ((array)$row as $th=>$v){?> <th><?=$th?></th><?}
      break;
    }
    foreach((array)$a as $k=>$row)
    { ?>
    <tr>
<?    foreach((array)$row as $td) {?>
        <td style='text-align:center;'><?=($html)?$td:nl2br(htmlspecialchars($td))?></td>
      <?} ?>
    </tr>
<?  } ?>
</table>
<?
  }
  public function execute()
  {
$db = DB\ORM::getInstance()->getDB('db0');
if ($_REQUEST['use']||$_REQUEST['use']=$_SESSION['use'])
{
  $_SESSION['use']=$_REQUEST['use'];
  $db->execute("USE {$_REQUEST['use']};");
}

$sql = $_REQUEST['sql']?:'SHOW TABLES';
$url=$_SERVER["PHP_SELF"].'?page=db';
$rows = $db->rows($sql);
  $html = true;
  switch(true)
  {
    case preg_match('/SHOW[\s]+TABLES/i',$sql):
      foreach($rows as $k => $row)
      {
        foreach($row as $kk=>$v)
        {
          $select  =$url."&sql=SELECT * FROM $v;";     $rows[$k][$kk]="<a href='$select'>$v</a>";
          $describe=$url."&sql=DESCRIBE $v;";          $rows[$k]['descr']="<a href='$describe'>Descr</a>";
          $where   =$url."&sql=SELECT * FROM $v WHERE ";
          $rows[$k]['where']=
          "<input id='where$v'/>
          <a href='javascript:;' onclick=\"v=document.getElementById('where$v').value;document.location.href='$where'+(v?v:'1');\">Go</a>";
#          $fields=$db->rows("DESCRIBE $v;");foreach($fields as $fk=>$fr){$fields[$fk]=$fields[$fk]['Type'].' '.$fields[$fk]['Field'];}$rows[$k]['fields']=implode(', ',$fields);
        }
      }
      break;
    case preg_match('/SHOW[\s]]+DATABASES/i',$sql):
      foreach($rows as $k => $row)
      {
        foreach($row as $kk=>$v)
        {
          $describe=$url."&use=$v&sql=SHOW TABLES;";   $rows[$k][$kk]="<a href='$describe'>$v</a>";
        }
      }
      break;
      default: $html = isset($_GET['html_view']);
  }
?>
<div style="width:100%">
<form action="<?=$url?>" method='POST' style="width:60%;">
  <textarea name="sql" style= "width:100%;height:300px;" ><?= htmlspecialchars($sql) ?></textarea><br/>
  <input type="submit"/>
</form>
<br/><a href="<?=$url?>&sql=<?=$sql?>&<?=!$html?'html_view':''?>" class="button small">HTML</a><br/>
<?$tables=$db->cols("SHOW TABLES;");?>
  <div style="float:right;position:absolute;right:0px;top:60px;width:35%;max-width:40%;overflow:auto;max-height:315px;">
    <?
    $descr=array();
    ?><TABLE border="0"><?
    foreach((array)$tables as $k=>$t)
    {
      $descr[$k]=$db->rows("DESCRIBE $t;");
      ?>
      <tr>
        <td>
          <div style="cursor:pointer;" onclick="if(document.getElementById('helper<?=$k?>').style.display=='none'){hideClass('helper');document.getElementById('helper<?=$k?>').style.display='';}else{document.getElementById('helper<?=$k?>').style.display='none';}">
            <?=$t." ".count($descr[$k])." fields"?>
          </div>
          <div style="display:none;margin-left:10px;" id="helper<?=$k?>" class="helper">
            <?$this->table($descr[$k]);?>
          </div>
        </td>
      </tr>
      <?
    }
    ?>
    </TABLE>
  </div>
</div>
        <br/>
<?if(isset($_SESSION['lastsql'])){?>
        <a href="<?=$url."&sql=".urlencode($_SESSION['lastsql'])?>">Back</a>
  <?}?>
        <a href="<?=$url."&sql=SHOW DATABASES"?>">Bases</a>
        <a href="<?=$url."&sql=SHOW TABLES"?>">Tables</a>
        <br/>
  <?
    echo nl2br(htmlspecialchars($sql))."<br/>\n";
    $this->table($rows,$html);
    $_SESSION['lastsql']=$sql;
  ?>
<script>
function getElementsByClassName(classname, node)  {
  if(!node) node = document.getElementsByTagName("body")[0];
  var a = [];
  var re = new RegExp('\\b' + classname + '\\b');
  var els = node.getElementsByTagName("*");
  for(var i=0,j=els.length; i<j; i++)
      if(re.test(els[i].className))a.push(els[i]);
  return a;
}

function hideClass(classname)
{
  var elements = new Array();
  elements = getElementsByClassName(classname);
  for(i in elements ){
       elements[i].style.display = "none";
  }
}
</script>
<?
  }
}
