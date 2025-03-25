<?php
/**
 * Создание товаров для получения похожих и проверка новых изображений
 */

use IdImage\Cli;

/* @var modX $modx */

/* @var idImage $idImage */
/* @var Cli $cli */
require_once dirname(__FILE__, 1).'/default.php';

$Command = new \IdImage\Command\CreationCommand($idImage, $cli);
$Command->run();
