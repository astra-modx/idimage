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

    public function withProgressIds()
    {
        $query = $this->tasks()
            ->where([
                'idImageTask.status' => idImageTask::STATUS_PENDING,
            ]);

        $ids = $query->ids('idImageTask.id as id');

        return $ids;
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
