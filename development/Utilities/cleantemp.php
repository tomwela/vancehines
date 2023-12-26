<?php

require_once(__DIR__ . '/../Application/connect.php');

use ClickBlocks\Core;


Core\IO::deleteFiles(Core\IO::dir('temp'));

?>