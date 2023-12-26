<?php

/**
 * Factory class
 */
final class Factory 
{
  protected static $logger = null;
  protected static $config = null;
  
  /**
   * Get logger object
   *
   * @return Logger
   */
  public static function getLogger() 
  {
    if (self::$logger == nulL) {
      self::$logger = new Logger();
    }
    return self::$logger;
  }
  
  /**
   * Get config object
   */
  public static function getConfig()
  {
    if (self::$config == null) {
      self::$config = new Config();
      //@todo remove hard code by php config file
      self::$config->setFromFile(__DIR__.'/config.ini');
    }
    return self::$config;
  }
  
}
