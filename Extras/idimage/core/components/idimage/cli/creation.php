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
$created = 0;
$updated = 0;
$created_thumbnail = 0;
$task_upload = 0;
if ($iterations) {
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

        $stat = $data['stat'];
        if (!empty($stat['created'])) {
            $created += $stat['created'];
        }
        if (!empty($stat['updated'])) {
            $updated += $stat['updated'];
        }
        if (!empty($stat['created_thumbnail'])) {
            $created_thumbnail += $stat['created_thumbnail'];
        }
        if (!empty($stat['task_upload'])) {
            $task_upload += $stat['task_upload'];
        }

        $time = round(microtime(true) - $time, 2).' s';
        $cli->info('[iteration:'.$i.'][time:'.$time.'] products: '.$data['total']);
    }
}
$cli->info('Created: '.$created);
$cli->info('Updated: '.$updated);
$cli->info('Created thumbnail: '.$created_thumbnail);
$cli->info('Task upload: '.$task_upload);
$cli->info('Completed');
