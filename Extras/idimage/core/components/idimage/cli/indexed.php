<?php

use IdImage\Cli;

/* @var modX $modx */

/* @var idImage $idImage */
/* @var Cli $cli */
require_once dirname(__FILE__, 1).'/default.php';

$cli->title('Indexed products');

$action = 'mgr/actions/indexed/products';
$response = $idImage->runProcessor($action, [
    'steps' => true,
]);

if ($response->isError()) {
    $cli->info('Error: '.$response->getMessage());
    exit;
}
$data = $response->getObject();


$cli->info('Total products: '.$data['total']);


foreach ($data['iterations'] as $i => $ids) {
    $modx->error->reset();

    // Создать товары
    $response = $idImage->runProcessor($action, [
        'ids' => $ids,
    ]);

    if ($response->isError()) {
        echo $response->getMessage();
        continue;
    }

    $data = $response->getObject();
    $cli->info('[iteration:'.$i.'] indexed products: '.$data['total']);
}


$cli->info('Completed');
