<?php

if (!class_exists('idImageProductTaskProcessor')) {
    include_once __DIR__.'/../task.class.php';
}

class idImageProductEmbeddingProcessor extends idImageProductTaskProcessor implements \IdImage\Interfaces\ActionProgressBar
{

    public function stepChunk()
    {
        return $this->idimage()->limitPoll();
    }

    public function operation(): string
    {
        return 'embedding';
    }

    public function apiCompletedTask()
    {
        $query = $this->query()->closes()
            ->where([
                'upload' => true,
                'task_id:!=' => null,
            ]);

        $ids = $query->ids('idImageClose.task_id as task_id');

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


    public function criteria()
    {
        // Только завершеные задания
        #  $taskIds = $this->apiCompletedTask();

        return [
            # 'task_id:IN' => $taskIds,
            'embedding' => false,
            'task_id:!=' => null,
        ];
    }
}

return 'idImageProductEmbeddingProcessor';
