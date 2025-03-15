<?php

use IdImage\Sender;

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../../actions.class.php';
}

class idImageApiTaskSendProcessor extends idImageActionsProcessor implements \IdImage\Interfaces\ActionProgressBar
{
    public function stepChunk()
    {
        return $this->idImage->limitPoll();
    }

    public function tasks()
    {
        return $this->query()
            ->tasks()
            ->innerJoin('idImageClose', 'Close', 'Close.pid = idImageTask.pid');
    }

    public function apiCompletedTask()
    {
        $query = $this->tasks()->where(['idImageTask.status' => idImageTask::STATUS_PENDING]);
        $ids = $query->ids('idImageTask.task_id as task_id');
        $completed = null;
        if (!empty($ids)) {
            $Response = $this->idimage()->api()->task()->completed($ids)->send();
            if (!$Response->isOk()) {
                $Response->exception();
            }
            $items = $Response->json('items');
            foreach ($items as $taskId => $status) {
                if ($status == 2) {
                    $completed[] = $taskId;
                }
            }
        }

        return $completed;
    }


    public function withProgressIds()
    {
        if (!$completedIds = $this->apiCompletedTask()) {
            return null;
        }
        $query = $this->tasks()->where(['idImageTask.task_id:IN' => $completedIds]);

        return $query->ids('idImageTask.id as id');
    }


    public function process()
    {
        $this->canToken();

        return $this->withProgressBar(function (array $ids) {
            $sender = $this->idimage()->sender();
            $this->tasks()
                ->where(['id:IN' => $ids])
                ->each(function (idImageTask $task) use ($sender) {
                    // Увеличиваем счетчик после каждой попытки
                    $sender->addTask($task);
                    $this->pt();

                    return true;
                });


            $sender->poll();

            return $this->total();
        });
    }


}

return 'idImageApiTaskSendProcessor';
