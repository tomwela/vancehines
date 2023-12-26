namespace <?=$namespace?>;

use ClickBlocks\Core,
    ClickBlocks\Cache<?if ($namespace != 'ClickBlocks\DB') {?>,
    ClickBlocks\DB<?}?>;


/**
<?=$properties?>
 */
class <?=$class?> extends <?=$parent?>
{
  public function __construct($pk = null)
  {
    $this->addDAL(new <?=$dalclass?>(), __CLASS__);
    parent::__construct($pk);
  }<?=$methods?>
}