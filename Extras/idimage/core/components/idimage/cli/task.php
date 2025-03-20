<?php

use IdImage\Cli;

/* @var modX $modx */

/* @var idImage $idImage */
/* @var Cli $cli */
require_once dirname(__FILE__, 1).'/default.php';

$cli->title('Task');

$totalTask = $idImage->query()->tasksQueue()->count();

$cli->info('Total tasks: '.$totalTask);

$action = 'mgr/actions/task/send';

$step_limit = $idImage->isIndexedService() ? 1000 : 200;
$response = $idImage->runProcessor($action, [
    'steps' => true,
    'step_limit' => $step_limit,
    'limit' => $idImage->limitTask(),
]);

if ($response->isError()) {
    $cli->info('Error: '.$response->getMessage());
    exit;
}
$data = $response->getObject();


$total = $data['total'];
$iterations = $data['iterations'];

$cli->info('Launch tasks: '.$total);

$totalIterations = $iterations ? count($iterations) : 0;
$cli->info('Iterations: '.$totalIterations);

$cli->startTime();


// Создать пошаговый процесс по согласно лимиту $limit
if (!empty($iterations)) {
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
        $cli->info('[iteration:'.$i.'] tasks: '.$data['total']);
    }
}

$time = $cli->endTime();
$cli->info('Time: '.$time);

$queue = $idImage->query()->tasks()->where([
    'status:IN' => [
        idImageTask::STATUS_PENDING,
        idImageTask::STATUS_CREATED,
    ],
])->count();
$queueEx = $idImage->query()->tasksExecuteAt()->where([
    'status:IN' => [
        idImageTask::STATUS_PENDING,
        idImageTask::STATUS_CREATED,
    ],
])->count();
if ($queueEx > 0) {
    $cli->warning('Queue tasks executeAt: '.$queueEx);
}
$cli->info('Queue tasks: '.$queue);
$cli->info('Completed');
