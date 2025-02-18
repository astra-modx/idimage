<?php
if (file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php')) {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
} else {
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
}
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var idimage $idimage */
$idimage = $modx->getService('idimage', 'idimage', MODX_CORE_PATH . 'components/idimage/model/');
$modx->lexicon->load('idimage:default');

// handle request
$corePath = $modx->getOption('idimage_core_path', null, $modx->getOption('core_path') . 'components/idimage/');
$path = $modx->getOption('processorsPath', $idimage->config, $corePath . 'processors/');
$modx->getRequest();

/** @var modConnectorRequest $request */
$request = $modx->request;
$request->handleRequest([
    'processors_path' => $path,
    'location' => '',
]);