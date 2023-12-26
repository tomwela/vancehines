<?php

/**
 * MysqlToolsException
 * 
 * Exceptio code:
 * 1000: Couldn't read file script
 * 1001: Error when importing script
 * 1002: Not read folder path
 */
class MysqlToolsException extends Exception {}

/**
 * MysqlTools
 * Handle something like
 * - Import db script
 * - Check version table exists or not
 * - Create version table
 *
 * @version 1.0
 */
class MysqlTools
{
  const FILE_IN_BOTH = 1;
  const FILE_IN_DB   = 2;
  const FILE_IN_DIR  = 3;
  const FILE_IS_CHANGED = 4;
  
  protected $dsn  = '';
  protected $user = '';
  protected $pass = '';
  
  protected $logger = null;
  
  protected $verTableName = '_patches_';
  
  /**
   *
   * @var PDO
   */
  protected $pdo = null;
  
  protected $safeMode = true;
  
  /**
   * Constructor
   */
  public function __construct($dsn, $user, $pass)
  {
    $this->dsn  = $dsn;
    $this->user = $user;
    $this->pass = $pass;
    
    $this->logger = Factory::getLogger();
    
    // get pdo object
    $this->pdo = new PDO($this->dsn, $this->user, $this->pass);
    // exception if query error
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  
  /**
   * Get PDO object
   * 
   * @return PDO
   */
  public function getPDO()
  {
    return $this->pdo;
  }
  
  /**
   * Get version table name
   *
   * @return string
   */
  public function getVerTableName()
  {
    return $this->verTableName;
  }
  
  /**
   * Set safe mode
   * 
   * @param bool $mode
   */
  public function setSafeMode($mode) 
  {
    $this->safeMode = $mode;
    return $this;
  }
  
  /**
   * Get safe mode
   * 
   * @return bool
   */
  public function getSafeMode()
  {
    return $this->safeMode;
  }
  
  /**
   * Import db from script file
   * 
   * @param string $file  File path
   * 
   * @throw MysqlToolsException 
   */
  public function import($file)
  {
    // set default delimiter
    $delimiter = $delimiter_default = ';';
    $delimiter_keyword = 'DELIMITER ';
    $delimiter_keyword_len = strlen($delimiter_keyword);
    // read content
    $content = file_get_contents( $file );
    if ($content === FALSE) {
      throw new MysqlToolsException('Could not read file script!', 1000);
    }
    $ignores = array('--', '#');
    // convert endline of WINDOW
    $content = str_replace("\r\n", "\n", $content);
    // explode content into many lines
    $lines = explode("\n", trim($content));
    $statement = '';
    // progress & execute statement
    try {
      // begin transaction if safe mode
      if ($this->safeMode) $this->pdo->beginTransaction();
      
      foreach ($lines as $line) {
        // ignore empty line
        if ( '' == ($line = trim($line)) ) continue;
        // ignore comment
        if ( in_array(substr($line, 0, 2), $ignores) ) continue;

        // check there is delimeter statement in query line
        //$listState = explode($delimiter, $line);
        //$tmp = trim(end($listState));
        if ( ($p = stripos($line, $delimiter_keyword)) !== false ) {
          // find next space if have
          $np = strpos($line, ' ', $p + $delimiter_keyword_len);
          if ($np === false) {
            $np = strlen($line);
          }
          // get new delimiter
          $new_delimiter = substr($line, $p + $delimiter_keyword_len, $np - $p - $delimiter_keyword_len);
          $new_delimiter = trim($new_delimiter);
          if (0 < strlen($new_delimiter)) {
            $delimiter = $new_delimiter;
          }
          // new change delimiter statement
          $line = substr($line, 0, $p) . substr($line, $np + 1);
        }
        // replace delimiter because delimiter isn't affect when execute by php
        if ($delimiter !== $delimiter_default) {
          $line = str_replace($delimiter, $delimiter_default, $line);
        }
        $statement .= PHP_EOL . $line;
        // check there is delimiter mark
        if ( $delimiter == $delimiter_default && $delimiter == substr($line, -1, 1) ) {
          // excute statement
          $this->pdo->exec( $statement );
          //
          if ($this->logger) {
            $this->logger->log( $statement );
          }
          // reset statement
          $statement = '';
        }
      }
      // execute statement if not empty
      if (!empty($statement)) {
        $this->pdo->exec( $statement );
        //
        if ($this->logger) {
          $this->logger->log( $statement );
        }
      }
      // if version table is exists, insert infor of path file
      if ($this->checkVersionTableExist()) {
        clearstatcache(); // sure that filesize isn't cached
        $size = filesize($file);
        $file = str_replace('\\', '/', $file);
        $filename = end(explode('/', $file));
        $this->addVersionLog($filename, $size);
      }
      // import success, commit if safe mode
      if ($this->safeMode) $this->pdo->commit();
    } catch( PDOException $ex ) {
      // rollback all privous statement if safe mode
      if ($this->safeMode) $this->pdo->rollBack();
      // log message
      if ($this->logger) {
        $this->logger->log( $statement );
      }
      throw new MysqlToolsException( $statement . "\n" . $ex->getMessage(), 1001);
    }
  }
  
  /**
   * Check table version exists or not
   * 
   * @return bool
   *   TRUE if exists, otherwise FALSE
   */
  public function checkVersionTableExist()
  {
    // build query check table exists
    $query = "SHOW TABLES LIKE '{$this->verTableName}'";
    //
    $rs = $this->pdo->query($query);
    return $rs->rowCount() > 0;
  }
  
  /**
   * Create version table
   */
  public function createVersionTable()
  {
    // build query
    $query = "
      CREATE TABLE IF NOT EXISTS `{$this->verTableName}` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `filename` varchar(300) NOT NULL,
        `filesize` int UNSIGNED NOT NULL,
        `created` datetime NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `filename` (`filename`)
      ) AUTO_INCREMENT=1
    ";
    try {
      $this->pdo->exec($query);
    } catch(PDOException $ex) {
      throw new MysqlToolsException($ex->getMessage(), $ex->getCode());
    }
    return true;
  }
  
  /**
   * Drop version table
   * @throw MysqlToolsException
   */
  public function dropVersionTable()
  {
    $query = "DROP TABLE `{$this->verTableName}`";
    try {
      $this->pdo->exec($query);
    } catch (PDOException $ex) {
      throw new MysqlToolsException($ex->getMessage(), $ex->getCode());
    }
    return true;
  }
  
  /**
   * Add a version log by filename
   * 
   * @param string $filename
   * @return bool
   *   TRUE if success, otherwise FALSE
   */
  public function addVersionLog($filename, $filesize = 0) 
  {
    $filename = $this->pdo->quote($filename);
    $query = "INSERT INTO {$this->verTableName} (filename, filesize, created) VALUE ($filename, $filesize, NOW())";
    $rs    = $this->pdo->exec( $query );
    return $rs > 0;
  }
  
  /**
   * Get version rows
   * 
   * @return array
   *   Array of version row, and each row is array
   */
  public function getVersionLogs()
  {
    $query = "SELECT * FROM {$this->verTableName} ORDER BY filename";
    $rs    = $this->pdo->query($query);
    return $rs->fetchAll();
  }
  
  /**
   * Check a version log exists in version table or not
   * 
   * @param string $name
   * @return bool
   *   TRUE if exists in db, otherwise FASLE
   */
  public function checkVersionLog($name)
  {
    $query = "SELECT * FROM {$this->verTableName} WHERE filename = " . $this->pdo->quote($name);
    $rs    = $this->pdo->query($query);
    return $rs->rowCount() > 0;
  }
  
  /**
   * Check how many files missing import
   * 
   * @return array
   *   List of files with mark as FILE_IN_BOTH (1), FILE_IN_DB (2), FILE_IN_DIR (3)
   * array(
   *   '001_#32332_filename1' => 1,
   *   '002_#34343_filename2' => 2,
   *   '003_#43223_filename3' => 3
   * )
   */
  public function checkMissImportFile($dirPath)
  {
    if ( !is_dir($dirPath) ) {
      throw new MysqlToolsException('Not read folder path', 1002);
    }
    
    // convert to unix path
    $dirPath = str_replace('\\', '/', $dirPath);
    if ( substr($dirPath, -1, 1) != '/' ) {
      $dirPath .= '/';
    }
    // get list sql file in dir
    $listFile = array();
    $handle = @opendir($dirPath);
    if ($handle !== false) {
      while ( ($file = readdir($handle)) !== false ) {
        if ( is_file($dirPath.$file) && 'sql' == substr($file, -3) ) {
          $listFile[] = $file;
        }
      }
      @closedir($handle);
    }
    // get list sql file in db
    $rows = $this->getVersionLogs();
    $listVerLogs = array();
    $filesizes   = array();
    foreach ($rows as $row) {
      $listVerLogs[] = $row['filename'];
      $filesizes[$row['filename']] = $row['filesize'];
    }
    
    // filter each file
    $list = array_fill_keys( array_merge($listFile, $listVerLogs), self::FILE_IN_BOTH );
    foreach ($list as $key => $val) {
      $isInDB  = in_array($key, $listVerLogs);
      $isInDir = in_array($key, $listFile);
      
      if ( $isInDB && !$isInDir ) {
        $list[$key] = self::FILE_IN_DB;
      }
      elseif ( !$isInDB && $isInDir ) {
        $list[$key] = self::FILE_IN_DIR;
      }
      else {
        clearstatcache(); // sure filesize isn't cached
        $fsize = filesize($dirPath . $key);
        if ($fsize != $filesizes[$key]) {
          $list[$key] = self::FILE_IS_CHANGED;
        }
      }
    }
    
    return $list;
  }
  
}