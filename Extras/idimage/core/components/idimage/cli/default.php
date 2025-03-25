<?php
error_reporting(E_ALL & ~E_NOTICE);

use IdImage\Cli;

const MODX_API_MODE = true;
require_once dirname(__FILE__, 4).'/config/config.inc.php';
require_once MODX_BASE_PATH.'index.php';

// Load main services
/** @var modX $modx */
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->getService('error', 'error.modError');
$modx->lexicon->load('idimage:default');
$modx->lexicon->load('idimage:manager');

// Time limit
set_time_limit(600);
$tmp = 'Trying to set time limit = 600 sec: ';
$tmp .= ini_get('max_execution_time') == 600 ? 'done' : 'error';


$idImage = $modx->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');

$cli = new Cli();
$cli->info($tmp);
