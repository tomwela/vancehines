<?php

namespace ClickBlocks\Web;

use ClickBlocks\Core,
    ClickBlocks\Web\UI\POM;

class XHTMLParser
{
   protected $reg = null;
   protected $parser = null;
   protected $tpl = null;
   protected $res = null;
   protected $stack = null;
   protected $parentID = null;
   protected $subjectID = null;
   protected $autoClosedTags = array('BR' => 1, 'HR' => 1, 'IMG' => 1, 'INPUT' => 1);
   protected static $validators = array();
   protected static $isParsing = false;
   protected static $phpEntities = array();
   protected $controlsPrefix = null;

   public static $controls = array(
     'BODY' => 1,           'PANEL' => 1,           'WEBFORM' => 1,       'TEMPLATE' => 1,     'REPEATER' => 1,
     'CHECKBOXGROUP' => 1,  'RADIOBUTTONGROUP' => 1,'CHECKBOX' => 1,      'RADIOBUTTON' => 1,  'SWITCHGROUP' => 1,
     'CKEDITOR' => 1,       'MEMO' => 1,            'TINYMCE' => 1,       'HIDDEN' => 1,       'UPLOAD' => 1,          
     'UPLOADBUTTON' => 1,   'TEXTLABEL' => 1,       'TEXTBOX' => 1,       'AUTOFILL' => 1,     'IMAGE' => 1,
     'TEXTBUTTON' => 1,     'IMAGEBUTTON' => 1,     'HYPERLINK' => 1,     'NAVIGATOR' => 1,    'SQLDROPDOWNBOX' => 1,  
     'DROPDOWNBOX' => 1,    'VALIDATOR' => 1,       'WIDGET' => 1,        'TIMEPICKER' => 1,   'DATEPICKER' => 1,    
     'DATETIMEPICKER' => 1, 'COLORPICKER' => 1,     'IMGEDITOR' => 1,     'LOGIN' => 1,        'VERIFICATION' => 1,
     'PASSWORD'      => 1
     );

   public static function encodePHPTags($str)
   {
    $xhtml="";
    $tokens = token_get_all($str);
    foreach($tokens as $token)
      {
        switch($token[0])
        {
          case T_INLINE_HTML: 
            $xhtml .= $token[1];
            break;

          case T_OPEN_TAG:
          case T_OPEN_TAG_WITH_ECHO:
            $tk = array($token[1]);
            break;

          default:
            $tk[] = is_array($token) ? $token[1] : $token;
            break;

          case T_CLOSE_TAG:
            $tk[] = $token[1];
            $tk = implode('',$tk);
            self::$phpEntities[$k = md5($tk)] = $tk;
            $xhtml.=$k;
            break;
        }
      }
     return str_replace('&','&amp;',$xhtml);
   }

  public static function decodePHPTags($str)
  {
    return strtr(strtr($str, array('&amp;'=>'&', '&#0010;'=>"\n")), self::$phpEntities);
  }

   public static function exe($code)
   {
      $config = Core\Register::getInstance()->config;
      ob_start();
      Core\Debugger::setEvalCode($code);
      if (eval(' ?>' . $code . '<?php ') === false) Core\Debugger::setEvalCode($code, true);
      $res = ob_get_contents();
      ob_end_clean();
      return $res;
   }

   public static function evil($code)
   {
      if ($code == '') return;
      $code = '$tmp = ' . $code . ';';
      Core\Debugger::setEvalCode($code);
      if (eval($code) === false) Core\Debugger::setEvalCode($code, true);
      return $tmp;
   }
   
   public function __construct()
   {
      $this->reg = Core\Register::getInstance();
      $this->parser = xml_parser_create($this->reg->config->charset);
      xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
      xml_set_element_handler($this->parser, array($this, 'start'), array($this, 'end'));
      xml_set_character_data_handler($this->parser, array($this, 'cdata'));
      xml_set_default_handler($this->parser, array($this, 'cdata'));
      $this->controlsPrefix = strtoupper($this->reg->config->templateControlPrefix) ?: null;
   }

   public function __destruct()
   {
      xml_parser_free($this->parser);
   }

   public static function isParsing()
   {
      return self::$isParsing;
   }

   public static function getValidators()
   {
      return self::$validators;
   }

   public function parse($file, &$template, $adjustValidators = true)
   {
      $xhtml = (is_file($file)) ? file_get_contents($file) : $file;
      $oldParsing = self::$isParsing;
      if (!self::$isParsing) self::$validators = array();
      self::$isParsing = true;
      $this->stack = new \SplStack();
      $this->parentID = $this->subjectID = $this->res = null;
      $this->tpl = ($template instanceof Core\Template) ? $template : new Core\Template();
      if (!xml_parse($this->parser, self::encodePHPTags($xhtml))) throw new \Exception(err_msg('ERR_XHTML_1', array(xml_error_string(xml_get_error_code($this->parser)), (is_file($file)) ? $file : htmlspecialchars($xhtml), xml_get_current_line_number($this->parser), xml_get_current_column_number($this->parser))));
      $template = $this->tpl;
      self::$isParsing = $oldParsing;
      if (!self::$isParsing && $adjustValidators)
      {
         if (!$this->reg->page->body) $this->reg->page->xhtml->body = $this->res;
         $this->adjustValidators();
      }
      return $this->res;
   }

   public function adjustValidators()
   {
      foreach (self::$validators as $uniqueID)
      {
         $validator = $this->reg->page->getByUniqueID($uniqueID);
         if (!is_array($validator->controls))
         {
            $validator->controls = $validator->controls;
            $this->reg->page[$uniqueID] = $validator;
         }
         //$validator->JS();
      }
   }
   
   protected function isControl($tag, &$class)
   {
      $tag = strtoupper($tag);
      if ($this->controlsPrefix !== NULL && substr($tag, 0, strlen($this->controlsPrefix)) == $this->controlsPrefix)
      {
         $class = substr($tag, strlen($this->controlsPrefix));
         return true;
      }
      else 
      {
         $class = $tag;
         if (substr($tag, 0, 6) == 'WIDGET') $tag = 'WIDGET';
         else if (substr($tag, 0, 9) == 'VALIDATOR') $tag = 'VALIDATOR';
         return isset(self::$controls[$tag]);
      }
   }

   protected function start($parser, $tag, array $attributes)
   {
      if ($this->isControl($tag, $tagClass))
      {
         if ($tagClass == 'TEMPLATE')
         {
            $file = Core\IO::dir(self::exe(self::decodePHPTags($attributes['path'])));
            if ($attributes['disableException'] && !is_file($file)) return;
            unset($attributes['path']);
            unset($attributes['disableException']);
            $template = '<panel id="' . uniqid('c') . '">' . file_get_contents($file) . '</panel>';
            $obj = foo(new self())->parse($template, $template);
            if (count($this->stack) > 0)
            {
               $ctrl = $this->stack->top();
               foreach ($obj as $child)
               {
                  $params = $child->getParameters();
                  $params['parameters'][1]['parentUniqueID'] = '';
                  $child->setParameters($params);
                  $ctrl->add($child);
                  $this->copyTemplates($ctrl, $child, $template);
               }
               $this->tpl[$ctrl->uniqueID] .= (string)$template[$obj->uniqueID];
               unset($this->reg->page[$obj->uniqueID]);
            }
            else
            {
               $this->tpl = $template;
               $this->stack->push($obj);
            }
         }
         else
         {
            $class = '\ClickBlocks\Web\UI\POM\\' . $tagClass;
            $ctrl = new $class($attributes['id']);
            if ($ctrl instanceof POM\IPanel)
            {
               if ($attributes['masterpage'])
               {
                  if (count($this->stack) > 0) throw new \Exception(err_msg('ERR_XHTML_5', array(get_class($this), $ctrl->id)));
                  $attributes['masterpage'] = self::exe(self::decodePHPTags($attributes['masterpage']));
                  $this->stack->push(foo(new self())->parse(Core\IO::dir($attributes['masterpage']), $this->tpl));
                  $this->parentID = self::exe(self::decodePHPTags($attributes['parentID']));
                  $this->subjectID = $ctrl->uniqueID;
                  unset($attributes['masterpage']);
                  unset($attributes['parentID']);
               }
            }
            $this->stack->push($ctrl);
            $ctrl->parse($attributes, $this->tpl);
            foreach ($attributes as $k => $v) $ctrl->{$k} = $v;
            if ($ctrl instanceof POM\IValidator) self::$validators[] = $ctrl->uniqueID;
         }
      }
      else
      {
         if (!count($this->stack)) throw new \Exception(err_msg('ERR_XHTML_2'));
         $html = '<' . $tag;
         if (count($attributes))
         {
            $tmp = array();
            foreach ($attributes as $k => $v) $tmp[] = $k . '="' . $v . '"';
            $html .= ' ' . implode(' ', $tmp);
         }
         if (isset($this->autoClosedTags[$tagClass])) $html .= ' />';
         else $html .= '>';
         $ctrl = $this->stack->top();
         if ($ctrl instanceof POM\IPanel) $this->tpl[$ctrl->uniqueID] .= self::decodePHPTags($html);
         else if (isset($ctrl->text)) $ctrl->text .= self::exe(self::decodePHPTags($html));
      }
   }

   protected function cdata($parser, $content)
   {
      $ctrl = $this->stack->top();
      if ($ctrl instanceof POM\IPanel) $this->tpl[$ctrl->uniqueID] .= self::decodePHPTags($content);
      else if (isset($ctrl->text)) $ctrl->text .= self::exe(self::decodePHPTags($content));
   }

   protected function end($parser, $tag)
   {
      if ($this->isControl($tag, $tagClass))
      {
         if ($tagClass == 'TEMPLATE') return;
         $ctrl = $this->stack->pop();
         if ($ctrl->uniqueID == $this->subjectID)
         {
            if ($this->parentID)
            {
               $parent = $this->stack->top()->get($this->parentID, true);
               if ($parent === false) throw new \Exception(err_msg('ERR_XHTML_3', array($this->parentID)));
            }
            if (!$parent) $parent = $this->stack->top();
            $this->tpl[$parent->uniqueID] = preg_replace('/<\?=[\s]?\$' . $ctrl->id . '[\s;]?\?>/', '<?=$' . $ctrl->uniqueID . ';?>', $this->tpl[$parent->uniqueID]);
            if (!($parent instanceof POM\IPanel)) throw new \Exception(err_msg('ERR_XHTML_4', array($this->parentID, get_class($ctrl))));
            $parent->add($ctrl);
            $this->reg->page[$parent->uniqueID] = $parent;
            $ctrl = $this->stack->pop();
            $this->subjectID = $this->parentID = null;
         }
         else if (count($this->stack) > 0)
         {
            $parent = $this->stack->top();
            $parent->add($ctrl);
            $this->tpl[$parent->uniqueID] .= '<?=$' . $ctrl->uniqueID . ';?>';
         }
         if (count($this->stack) < 1)
         {
            $this->reg->page[$ctrl->uniqueID] = $ctrl;
            $this->res = $ctrl;
         }
      }
      else if (!isset($this->autoClosedTags[$tagClass]))
      {
         $ctrl = $this->stack->top();
         if ($ctrl instanceof POM\IPanel) $this->tpl[$ctrl->uniqueID] .= '</' . $tag . '>';
         else if (isset($ctrl->text)) $ctrl->text .= '</' . $tag . '>';
      }
   }

   private function copyTemplates($ctrl, $child, Core\ITemplate $template)
   {
      if ($child instanceof POM\IPanel)
      {
         $this->tpl->setTemplate($child->uniqueID, (string)$template[$child->uniqueID], $ctrl->uniqueID);
         foreach ($child as $sib) $this->copyTemplates($child, $sib, $template);
      }
   }
}

?>
