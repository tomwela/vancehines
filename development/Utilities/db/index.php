<?php

require realpath(__DIR__ .'/logger.php');
require realpath(__DIR__ .'/config.php');
require realpath(__DIR__ .'/tools.php');
require realpath(__DIR__ .'/factory.php');

$config = Factory::getConfig();
$tools  = new MysqlTools($config->db0['dsn'], $config->db0['dbuser'], $config->db0['dbpass']);
$logger = Factory::getLogger();
$logger->setLogMode(false);

$dirPath = dirname(dirname(__DIR__)) . $config->db0['dirPath'];
$message = array();

$create = isset($_GET['create']) ? 1 : 0;
// process create table if have "create" parameter
if ($create) {
  try {
    // recheck version table exists
    if ( !$tools->checkVersionTableExist() ) {
      $tools->createVersionTable();
      $message[] = 'Created version table!';
    }
  }
  catch (MysqlToolsException $ex) {
    $message[] = '<span class="error">' . $ex->getMessage() . '</span>';
  }
}

// process import file if have "import" parameter
$tableExists = $tools->checkVersionTableExist();
if ($tableExists) {
  $import  = isset($_GET['import']) ? $_GET['import'] : '';
  $fileName = base64_decode( $import );
  // check file is sql file and not imported
  if ('sql' == substr($fileName, -3) && !$tools->checkVersionLog($fileName)) {

    $message[] = 'Import file ' . $fileName;
    try {
      if ( !file_exists($dirPath .'/'. $fileName) ) {
        throw new MysqlToolsException('File not exists!');
      }
      $tools->import( $dirPath . '/' . $fileName );
      //$tools->addVersionLog($fileName);
    }
    catch (MysqlToolsException $ex) {
      $message[] = '<span class="error">' . str_replace("\n", '<br />', $ex->getMessage()) . '</span>';
    }
  }
  
  $list = $tools->checkMissImportFile( $dirPath );
}

?>

<!doctype html>
<html>
  
<head>
  <title>Check version tools</title>
  
  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <?php if ( !$tableExists ) : ?>
  <h3> Patches table isn't exists! </h3>
  <p><a href="index.php?create=1">Click here</a> to create table</p>
  <?php else : 
    $k = 0; 
  ?>
  <?php if (count($message) > 0) : ?>
  <div class="msg"><?php echo implode('<br />', $message);?></div>
  <?php endif; ?>
  <table class="list" border="0">
    <tr>
      <th>Script filename</th>
      <th>Status</th>
    </tr>
    <?php foreach ($list as $key => $val) : ?>
      <tr>
        <td ><?php echo $key;?></td>
        <td>
        <?php
          switch ($val ) {
            case MysqlTools::FILE_IN_BOTH:
              echo 'Imported';
              break;
            case MysqlTools::FILE_IS_CHANGED:
              echo 'Imported. However file is changed';
              break;
            case MysqlTools::FILE_IN_DB:
              echo 'Imported but not exists in script folders';
              break;
            case MysqlTools::FILE_IN_DIR:
              echo 'Not import';
              $link = 'index.php?import='.base64_encode( $key );
              echo ' <a href="'.$link.'">Click here to import</a>';
              break;
          }
        ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
  <?php endif; ?>
  
  
</body>

</html>