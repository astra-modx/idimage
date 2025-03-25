<?php
/**
 * Загрузка изображений в сервис IDimage и получения векторов для индексации
 */
use IdImage\Cli;
use IdImage\Command\UploadCommand;

/* @var modX $modx */

/* @var idImage $idImage */
/* @var Cli $cli */
require_once dirname(__FILE__, 1).'/default.php';


$Command = new UploadCommand($idImage, $cli);
$Command->run();

