<?php

use IdImage\Cli;

/* @var modX $modx */

/* @var idImage $idImage */
/* @var Cli $cli */
require_once dirname(__FILE__, 2).'/default.php';

$name = 'Task upload';
$cli->title($name);


if (!$idImage->isSendFile()) {
    $cli->error('Mode send file disabled, setting idimage_send_file is not true');
    exit;
}

$action = 'mgr/actions/api/task/upload';
$response = $idImage->runProcessor($action, [
    'steps' => true,
]);

if ($response->isError()) {
    $cli->info('Error: '.$response->getMessage());
    exit;
}
$data = $response->getObject();

$total = $data['total'];
$iterations = $data['iterations'];

$cli->info('Total products: '.$total);

if ($iterations) {
// Создать пошаговый процесс по согласно лимиту $limit
    foreach ($iterations as $i => $ids) {
        $modx->error->reset();

        $time = microtime(true);


        // Создать товары
        $response = $idImage->runProcessor($action, [
            'ids' => $ids,
        ]);

        if ($response->isError()) {
            echo $response->getMessage();
            continue;
        }

        $data = $response->getObject();



        $time = round((microtime(true) - $time),2) .' s.';
        $cli->info('[iteration:'.$i.']['.$time.'] '.$name.': '.$data['total']);
    }
}

$cli->info('Completed');
