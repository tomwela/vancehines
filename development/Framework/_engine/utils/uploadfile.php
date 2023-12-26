<?php
/**
 * ClickBlocks.PHP v. 1.0
 *
 * Copyright (C) 2010  SARITASA LLC
 * http://www.saritasa.com
 *
 * This framework is free software. You can redistribute it and/or modify
 * it under the terms of either the current ClickBlocks.PHP License
 * (viewable at theclickblocks.com) or the License that was distributed with
 * this file.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY, without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the ClickBlocks.PHP License
 * along with this program.
 *
 * Responsibility of this file: uploadfile.php
 *
 * @category   Helper
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */

namespace ClickBlocks\Utils;

use ClickBlocks\Core;

/**
 * UploadFile carries the moving of the uploaded file in according to the selected mode and creates thumbnails of the uploaded picture.
 *
 * @category   Helper
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @version    Release: 1.0.0
 */
class UploadFile
{
   /**
    * The default mode of the file uploading. The uploaded file moves on an indicated direcotory.
    */
   const UPLOAD_MODE_DEFAULT = 0;

   /**
    * The mode when the uloaded file moves on a temporary directory.
    */
   const UPLOAD_MODE_TEMP = 1;
   /**
    * The mode when the uloaded file moves on a temporary directory.
    * Don't delete previous loaded files in this session with same id
    */
   const UPLOAD_MODE_TEMP_MULTIPLY  = 3;
   const UPLOAD_MODE_PLACE_MULTIPLY = 4;

   /**
    * The mode when the uploaded file moves from a temporary directory on an indicated directory.
    */
   const UPLOAD_MODE_PLACE = 2;

   /**
    * The object of the class "Config"
    *
    * @var    Config
    * @access private
    */
   private $config = null;

   /**
    * The properties of this class.
    *
    * @var    array
    * @access protected
    */
   protected $properties = array(
      'extensions' => array(),
      'allowedNameChars' => 'a-zA-Z0-9_ \.\-\(\)\[\]',
      'maxsize' => null,
      'minsize' => null,
      'types' => array(),
      'error' => 0,
      'validate' => true,
      'name' => null,
      'id' => null,
      'unique' => false,
      'destination' => null,
      'mode' => UPLOAD_MODE_DEFAULT
      );

   /**
    * The array of thumbnails information.
    *
    * @var    array
    * @access protected
    */
   protected $thumbnails = array();

   public $id;

   /**
    * Constructs a new UploadFile.
    *
    * @param string $id unique identifier of an uploaded file.
    * @access public
    */
   public function __construct($id = null)
   {
      $this->config = Core\Register::getInstance()->config;
      $this->id = $id;
   }

   /**
    * Assigns a value to a property.
    *
    * @param string $param
    * @param mixed $value
    * @throws \Exception
    * @access public
    */
   public function __set($param, $value)
   {
      if (array_key_exists($param, $this->properties)) $this->properties[$param] = $value;
      else throw new \Exception(err_msg('ERR_GENERAL_3', array($param, get_class($this))));
   }

   /**
    * Returns a value of a property of the class.
    *
    * @param string $param
    * @return mixed
    * @throws \Exception
    * @access public
    */
   public function __get($param)
   {
      if (array_key_exists($param, $this->properties)) return $this->properties[$param];
      else throw new \Exception(err_msg('ERR_GENERAL_3', array($param, get_class($this))));
   }

   /**
    * Adds thumbnail's options for an uploaded picture.
    * The format of an option array is:
    * array('prefix' => ...,     - name prefix of a thumbnail file.
    *       'width' => ...,      - wishful width of a picture thumbnail.
    *       'height' => ...,     - wishful height of a picture thumbnail.
    *       'mode' => ...,       - resizing mode of a picture, for more information see Picture::resize method.
    *       'maxWidth' => ...,   - maximum allowable width of a picture.
    *       'maxHieght' => ...)  - maximum allowable height of a picture.
    *
    * @param array $options
    * @access public
    */
   public function addThumbnail(array $options)
   {
      $this->thumbnails[] = $options;
   }

   /**
    * Verifies whether or not satisfy the downloaded file the required conditions.
    *
    * @param array $data
    * @return boolean
    * @access public
    */
   public function isValid(array $data)
   {
      if ($data['error'] > 0)
      {
         $this->error = $data['error'];
         return false;
      }
      if (is_array($this->extensions) && count($this->extensions) > 0)
      {
         $pp = pathinfo($data['name']);
         if (!in_array(strtolower($pp['extension']), $this->extensions))
         {
            $this->error = 10;
            return false;
         }
      }
      if ($this->maxsize)
      {
         if ($data['size'] > $this->maxsize)
         {
            $this->error = 11;
            return false;
         }
      }
      if ($this->minsize)
      {
         if ($data['size'] < $this->minsize)
         {
            $this->error = 12;
            return false;
         }
      }
      if (is_array($this->types) && count($this->types) > 0)
      {
         if (!in_array($data['type'], $this->types))
         {
            $this->error = 13;
            return false;
         }
      }
      $this->error = 0;
      return true;
   }
protected function uploadPlace()
{
  if(isset($_SESSION['__UPLOAD_FILES__'][$this->id]['fullname'])) $_SESSION['__UPLOAD_FILES__'][$this->id] = array($_SESSION['__UPLOAD_FILES__'][$this->id]);
   $res = array();
   foreach ((array)$_SESSION['__UPLOAD_FILES__'][$this->id] as $index => $info) 
   {
     $path = $this->normalizePath($this->destination);
     $name = $this->getName($info, $path);
     if (is_file($info['fullname']))
     {
        $url = $this->normalizeURL(str_replace($this->config->root, '', $path));
        Core\IO::createDirectories($path);

        rename($info['fullname'], $path . $name);
        unset($_SESSION['__UPLOAD_FILES__'][$this->id][$index]);
        $info['fullname'] = $path . $name;
        $info['url'] = $url . $name;
        $info['name'] = $name;
        $info['path'] = $path;
        $thumbs = array();
        foreach ($info['thumbnails'] as $k => $nail)
        {
           if (!is_file($nail['fullname']))
           {
              $this->error = 15;
              return false;
           }
           $thname = (($nail['prefix']) ? $nail['prefix'] : 'th' . $k . '_') . $name;
           rename($nail['fullname'], $path . $thname);
           $thumbs[$k]['fullname'] = $path . $thname;
           $thumbs[$k]['name'] = $thname;
           $thumbs[$k]['url'] = $url . $thname;
           $thumbs[$k]['path'] = $path;
        }
        $info['thumbnails'] = $thumbs;
        $res[] = $info;
     }        
   }
   unset($_SESSION['__UPLOAD_FILES__'][$this->id]);
   if(!$res)
   {
    $this->error = 114;
    return false;
   }
   return $res;
}
   /**
    * Moves an uploaded file according to the indicated mode.
    *
    * @param array $data
    * @return boolean|array   returns FALSE if a error is occured and the array of parameters of a moved file otherwise.
    * @access public
    */
   public function upload(array $data = null)
   {
      switch ($this->mode)
      {
         case self::UPLOAD_MODE_DEFAULT:
         default:
           if ($this->validate && !$this->isValid($data)) return false;
           $path = $this->normalizePath($this->destination);
           $name = $this->getName($data, $path);
           Core\IO::createDirectories($path);
           if (move_uploaded_file($data['tmp_name'], $path . $name))
           {
              $url = $this->normalizeURL(str_replace($this->config->root, '', $path));
              $info = array('thumbnails' => $this->getThumbnails($path, $url, $name), 'fullname' => $path . $name, 'name' => $name, 'url' => $url . $name, 'path' => $path, 'type' => $data['type'], 'size' => $data['size']);
              return $info;
           }
           break;
         case self::UPLOAD_MODE_TEMP:
           if ($this->validate && !$this->isValid($data)) return false;
           $path = $this->normalizePath(($this->destination) ? $this->destination : Core\IO::dir('temp'));
           $name = $this->getName($data, $path);
           Core\IO::createDirectories($path);
           $id = ($this->id) ? $this->id : $name;
           if (is_file($_SESSION['__UPLOAD_FILES__'][$id]['fullname'])) unlink($_SESSION['__UPLOAD_FILES__'][$id]['fullname']);
           if (is_array($_SESSION['__UPLOAD_FILES__'][$id]['thumbnails']))
           {
              foreach ($_SESSION['__UPLOAD_FILES__'][$id]['thumbnails'] as $nail) if (is_file($nail['fullname'])) unlink($nail['fullname']);
           }

           if (move_uploaded_file($data['tmp_name'], $path . $name))
           {
              $url = $this->normalizeURL(str_replace($this->config->root, '', $path));
              $info = array('thumbnails' => $this->getThumbnails($path, $url, $name), 'fullname' => $path . $name, 'name' => $name, 'url' => $url . $name, 'path' => $path, 'type' => $data['type'], 'size' => $data['size']);
              $_SESSION['__UPLOAD_FILES__'][$id] = $info;
              return $info;
           }
           break;

          case self::UPLOAD_MODE_TEMP_MULTIPLY:
           if ($this->validate && !$this->isValid($data)) return false;
           $path = $this->normalizePath(($this->destination) ? $this->destination : Core\IO::dir('temp'));
           $name = $this->getName($data, $path);
           Core\IO::createDirectories($path);
           $id = ($this->id) ? $this->id : $name;

           if (move_uploaded_file($data['tmp_name'], $path . $name))
           {
              $url = $this->normalizeURL(str_replace($this->config->root, '', $path));
              $info = array('thumbnails' => $this->getThumbnails($path, $url, $name), 'fullname' => $path . $name, 'name' => $name, 'url' => $url . $name, 'path' => $path, 'type' => $data['type'], 'size' => $data['size']);
              $_SESSION['__UPLOAD_FILES__'][$id][] = $info;
              return $info;
           }
           break;
         case self::UPLOAD_MODE_PLACE:
          return $this->uploadPlace();
         case self::UPLOAD_MODE_PLACE_MULTIPLY:
          return $this->uploadPlace(true);
      }
      $this->error = 14;
      return false;
   }

   public static function clean($id)
   {
      unset($_SESSION['__UPLOAD_FILES__'][$id]);
   }

   public static function delete($id)
   {
      if (!isset($_SESSION['__UPLOAD_FILES__'][$id])) return;
      if (is_array($_SESSION['__UPLOAD_FILES__'][$id]['thumbnails']))
      {
         foreach ($_SESSION['__UPLOAD_FILES__'][$id]['thumbnails'] as $nail) if (is_file($nail['fullname'])) unlink($nail['fullname']);
      }
      if (is_file($_SESSION['__UPLOAD_FILES__'][$id]['fullname'])) unlink($_SESSION['__UPLOAD_FILES__'][$id]['fullname']);
      unset($_SESSION['__UPLOAD_FILES__'][$id]);
   }

   public static function erase()
   {
      foreach ((array)$_SESSION['__UPLOAD_FILES__'] as $id => $data) self::delete($id);
   }

   public static function getInfo($id)
   {
      return $_SESSION['__UPLOAD_FILES__'][$id];
   }

   public static function setInfo($id, array $params)
   {
      $_SESSION['__UPLOAD_FILES__'][$id] = $params;
   }

   /**
    * Returns new name for a moving file.
    *
    * @param array $data
    * @return string
    * @access protected
    */
   protected function getName(array $data, $path = null)
   {
      $pp = pathinfo($data['name']);
      if ($this->unique) 
      {
        $name = md5(microtime());
        $res = ($pp['extension']) ? $name . '.' . $pp['extension'] : $name;
      }
      else if (!$this->name) $res = $data['name'];
      else 
      {
        $name = $this->name;
        $res = ($pp['extension']) ? $name . '.' . $pp['extension'] : $name;
      }
      $res = preg_replace("/[^".$this->properties['allowedNameChars']."]/", '_', $res);
      if(!is_null($path))
        if(is_file($path . $res)) 
        {
          $i=0;
          $name = explode('.', $res);
          $ext = array_pop($name);
          $name = implode('.', $name);
          while(is_file($path . $name.(++$i).'.'.$ext));
          $res = $name.$i.'.'.$ext;
        }      
      return $res;
   }

   /**
    * Returns an array of information of picture thumbnail.
    *
    * @param string $path
    * @param string $url
    * @param string $name
    * @return array
    * @access protected
    */
   protected function getThumbnails($path, $url, $name)
   {
      $thumbs = array();
      foreach ($this->thumbnails as $k => $nail)
      {
         $thname = (($nail['prefix']) ? $nail['prefix'] : 'th' . $k . '_') . $name;
         $pic = new Picture($path . $name);
         $pic->resize($path . $thname, intval($nail['width']), intval($nail['height']), $nail['mode'], intval($nail['maxWidth']), intval($nail['maxHeight']));
         $thumbs[$k]['fullname'] = $path . $thname;
         $thumbs[$k]['name'] = $thname;
         $thumbs[$k]['url'] = $url . $thname;
         $thumbs[$k]['path'] = $path;
         $thumbs[$k]['prefix'] = $nail['prefix'];
      }
      return $thumbs;
   }

   /**
    * Adds slash to the start of url.
    *
    * @param string $url
    * @return string
    * @access private
    */
   private function normalizeURL($url)
   {
      return $this->normalizePath((($url[0] == '/') ? '' : '/') . $url);
   }

   /**
    * Adds slash to the end of path.
    *
    * @param string $path
    * @return string
    * @access private
    */
   private function normalizePath($path)
   {
      if (!$path) return '';
      return $path . (($path[strlen($path) - 1] == '/') ? '' : '/');
   }

  public function getErrorMessage()
  {
     switch ($this->error)
     {
        case UPLOAD_ERR_INI_SIZE:
          return 'The uploaded file exceeds the upload_max_filesize (' . ini_get('upload_max_filesize') . ') directive in php.ini.';
        case UPLOAD_ERR_FORM_SIZE:
          return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
        case UPLOAD_ERR_PARTIAL:
          return 'The uploaded file was only partially uploaded.';
        case UPLOAD_ERR_NO_FILE:
          return 'No file was uploaded.';
        case UPLOAD_ERR_NO_TMP_DIR:
          return 'Missing a temporary folder.';
        case UPLOAD_ERR_CANT_WRITE:
          return 'Failed to write file to disk.';
        case UPLOAD_ERR_EXTENSION:
          return 'File upload stopped by extension.';
        case 10:
          return 'We only accept files such as ' . implode(', ', $this->extensions) . '.';
        case 11:
          return 'The file size should not exceed ' . number_format((($this->maxsize > 0) ? $this->maxsize : MAX_PICTURE_SIZE) / (1024 * 1024), 2) . 'MB';
        default:
          return 'File uploading is failed (error code is ' . $this->error . ').';
      }
  }   
}

?>
