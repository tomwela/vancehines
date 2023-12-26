<?php

class ConfigException extends Exception {}

/**
 * Config class
 *
 */
class Config 
{
  
  /**
   * Set config from string
   * 
   * @param string $string
   */
  public function setFromString($string) 
  {
    $params = parse_ini_string($string, true);
    if ($params === false) throw new ConfigException('Error when read ini string!');
    
    foreach ($params as $section => $props) {
      $this->{$section} = $props;
    }
  }
  
  /**
   * Set ini config from file
   * 
   * @param string $file
   * 
   * @throws ConfigException
   */
  public function setFromFile($file)
  {
    $content = file_get_contents($file);
    if ($content === false) throw new ConfigException('Could not read ini file!');
    
    $this->setFromString($content);
  }
  
}

?>
