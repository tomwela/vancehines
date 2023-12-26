<?php

require realpath(__DIR__ .'/../logger.php');
require realpath(__DIR__ .'/../config.php');
require realpath(__DIR__ .'/../tools.php');
require realpath(__DIR__ .'/../factory.php');

class TestTools extends PHPUnit_Framework_TestCase
{
  /**
   *
   * @var MysqlTools
   */
  protected $tools = null;
    
  protected function setUp()
  {
    $config = Factory::getConfig();
    $this->tools = new MysqlTools($config->db0['dsn'], $config->db0['dbuser'], $config->db0['dbpass']);
  }
  
  public function testImport()
  {
    $tools   = $this->tools;
    $pdo     = $tools->getPDO();
    
    $pdo->exec('TRUNCATE ' . $tools->getVerTableName());
    
    $query = 'SELECT * FROM person';
    // import file 1
    $tools->import( __DIR__ . '/sql/001_#11111_filename1.sql' );
    $rs = $pdo->query( $query );
    $this->assertEquals(0, count($rs->fetchAll()));
    
    // import file 2
    $tools->import( __DIR__ . '/sql/002_#22222_filename2.sql' );
    $rs = $pdo->query( $query );
    $this->assertEquals(2, count($rs->fetchAll()));
    
    // test exception
    $this->setExpectedException('MysqlToolsException');
    $tools->import( __DIR__ . '/sql/006_#54566_filename6_err.sql' );
    $rs = $pdo->query("SELECT * FROM person WHERE name LIKE 'test_exception'");
    $this->assetEquals(0, count($rs->fetchAll()));
        
    // test file import not found
    $this->setExpectedException('MysqlToolsException', 'Could not read file script!', 1000);
    $tools->import( __DIR__ . '/sql/006_#54566_filenotfound.sql' );
  }
  
  public function testCreate()
  {
    $this->assertTrue( $this->tools->dropVersionTable() );
    $this->assertFalse($this->tools->checkVersionTableExist() );
    $this->assertTrue( $this->tools->createVersionTable() );
    $this->assertTrue( $this->tools->checkVersionTableExist() );
  }
  
  public function testAddGetVersionLog()
  {
    $tools   = $this->tools;
    $pdo     = $tools->getPDO();
    
    $logNames = array(
      '001_20120713_#23576_licenseState.sql',
      '002_20120717_#23820_gender.sql',
      '003_20120718_#23726_Update_Admin_User_account.sql'
    );
    
    // delete all rows of versions table
    $pdo->exec('TRUNCATE ' . $tools->getVerTableName());
    
    foreach ($logNames as $logName) {
      $this->assertTrue( $tools->addVersionLog($logName) );
      $this->assertTrue( $tools->checkVersionLog($logName) );
    }
    
    $rows = $tools->getVersionLogs();
    $this->assertGreaterThan(0, count($rows));
    
    foreach ($logNames as $i => $logName) {
      $this->assertEquals($logName, $rows[$i]['filename']);
    }
  }
  
  public function testCheckMissImportFile()
  {
    $logNames = array(
      '001_#11111_filename1.sql',
      '002_#22222_filename2.sql',
      '003_#33333_filename3.sql',
      '009_#99999_filename9.sql'
    );
    
    $expectRs = array(
      '001_#11111_filename1.sql' => MysqlTools::FILE_IS_CHANGED,
      '002_#22222_filename2.sql' => MysqlTools::FILE_IN_BOTH,
      '003_#33333_filename3.sql' => MysqlTools::FILE_IN_BOTH,
      '009_#99999_filename9.sql' => MysqlTools::FILE_IN_DB,
      '004_#54566_filename4.sql' => MysqlTools::FILE_IN_DIR,
      '005_#54566_filename5.sql' => MysqlTools::FILE_IN_DIR,
      '006_#54566_filename6_err.sql' => MysqlTools::FILE_IN_DIR
    );
    
    $tools = $this->tools;
    $pdo   = $tools->getPDO();
    
    // delete all rows of versions table
    $pdo->exec('TRUNCATE ' . $tools->getVerTableName());
    
    foreach ($logNames as $logName) {
      $this->assertTrue( $tools->addVersionLog($logName, 834) );
    }
    
    $dirPath = __DIR__ . '/sql';
    $result = $tools->checkMissImportFile($dirPath);
    
    $this->assertEquals($expectRs, $result);
  }
  
}