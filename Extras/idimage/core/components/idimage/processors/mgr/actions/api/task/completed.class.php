<?php

use IdImage\Sender;

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../../actions.class.php';
}

class idImageApiTaskCompletedProcessor extends idImageActionsProcessor
{
    public function process()
    {
        $this->canToken();
        $api = $this->idimage()->api();
        $query = $this->idimage()->query()
            ->tasks()
            ->innerJoin('idImageClose', 'Close', 'Close.pid = idImageTask.pid')
            ->where([
                'idImageTask.status' => idImageTask::STATUS_PENDING,
            ]);


        $ids = $query->ids('idImageTask.task_id as id');
        if (!empty($ids)) {
            $Response = $api->task()->completed($ids)->send();

            if (!$Response->isOk()) {
                $Response->exception();
            }
            $items = $Response->json('items');

            $completed = [];
            foreach ($items as $taskId => $status) {
                if ($status == 2) {
                    $completed[] = $taskId;
                }
            }
        }
        dd($completed);
    }

}

return 'idImageApiTaskCompletedProcessor';
