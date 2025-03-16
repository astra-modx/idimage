<?php

use IdImage\Cli;

/* @var modX $modx */

/* @var idImage $idImage */
/* @var Cli $cli */
require_once dirname(__FILE__, 2).'/default.php';

$name = 'Task poll';
$cli->title($name);

$action = 'mgr/actions/api/task/poll';
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

if ($iterations) {
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
        $cli->info('[iteration:'.$i.'] '.$name.': '.$data['total']);
    }
}

$cli->info('Completed');
