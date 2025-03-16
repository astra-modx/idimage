<?php

use IdImage\Cli;

/* @var modX $modx */

/* @var idImage $idImage */
/* @var Cli $cli */
require_once dirname(__FILE__, 1).'/default.php';

$cli->title('Creating products');

$action = 'mgr/actions/product/creation';
$response = $idImage->runProcessor($action, [
    'steps' => true,
]);

if ($response->isError()) {
    $cli->info('Error: '.$response->getMessage());
    exit;
}
$data = $response->getObject();

$limit = $idImage->limitCreation();
$total = $data['total'];
$iterations = $data['iterations'];

$cli->info('Total products: '.$total);

// Создать пошаговый процесс по согласно лимиту $limit

foreach ($iterations as $i => $ids) {
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
    $cli->info('[iteration:'.$i.'] created products: '.$data['total']);
}


$cli->info('Completed');
