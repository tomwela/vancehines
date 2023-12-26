<?php

/**
 * API Execution script
 */

namespace ClickBlocks\API;

require_once(__DIR__ . '/../connect.php');

foo(new API)->setEntities(array(
      'example'
    ))->execute();

?>
