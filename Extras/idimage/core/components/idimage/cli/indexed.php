<?php
/**
 * Индексация товаров для получения похожих изображений
 */

use IdImage\Cli;

/* @var modX $modx */

/* @var idImage $idImage */
/* @var Cli $cli */
require_once dirname(__FILE__, 1).'/default.php';

$command = new \IdImage\Command\IndexedCommand($idImage, $cli, 100);
$command->run();
