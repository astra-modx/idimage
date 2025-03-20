<?php

use IdImage\TaskCollection;

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../actions.class.php';
}

class idImageActionsTaskSendProcessor extends idImageActionsProcessor implements \IdImage\Interfaces\ActionProgressBar
{
    public function stepChunk()
    {
        return $this->getProperty('step_limit', 500);
    }

    public function withProgressIds()
    {
        $query = $this->query()->tasksQueue();
        $limit = $this->getProperty('limit');
        if (!empty($limit)) {
            // лимит для фоновых заданий
            $query->limit($limit);
        }

        return $query->ids();
    }

    public function process()
    {
        $this->canToken();

        return $this->withProgressBar(function (array $ids) {
            // Создаем коллекцию заданий для отправки
            $collection = new TaskCollection();


            // Получаем данные
            $query = $this->query()->tasks()->where([
                'id:IN' => $ids,
            ]);
            $query->each(function (idImageTask $task) use ($collection) {
                // Создание задания для получения векторов

                if (!$task->isExecute()) {
                    // Отложенное время отправления задания
                    return false;
                }

                // Считаем количество попыток отправки задания
                if ($task->attemptsExceeded()) {
                    $task->setErrors($this->modx->lexicon('idimage_error_maxtries'));

                    return false;
                }

                $task->attempts();
                $add = true;
                if ($task->operation() === 'embedding') {
                    if (!$task->taskId()) {
                        $add = false;
                    }
                }

                if ($add) {
                    $collection->add($task);
                }

                return true;
            });

            if ($collection->isNotEmpty()) {
                // Отправка
                $this->idimage()->sender()->send($collection);
            }

            return $query->totalIteration();
        });
    }

}

return 'idImageActionsTaskSendProcessor';
