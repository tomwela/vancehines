<?php

/**
 * Simple logger
 */
class Logger 
{
  protected $filePath = '';
  protected $logMode  = false;
  
  public function __construct() 
  {
    $this->filePath = __DIR__ . '/import.log';
  }
  
  public function setLogMode( $mode ) 
  {
    $this->logMode = $mode;
  }
  
  public function log( $data ) 
  {
    if ($this->logMode == false) return;
    
    if ( !(is_string($data) || is_numeric($data)) ) {
      $data = var_export( $data, true );
    }
    error_log( $data . PHP_EOL, 3, $this->filePath );
  }
}

?>
