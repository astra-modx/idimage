<?php

use IdImage\TaskCollection;

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../../actions.class.php';
}

class idImageApiTaskReceivedProcessor extends idImageActionsProcessor implements \IdImage\Interfaces\ActionProgressBar
{
    public function stepChunk()
    {
        return $this->idImage->limitReceived();
    }

    public function tasks()
    {
        return $this->query()
            ->tasks()
            ->innerJoin('idImageClose', 'Close', 'Close.pid = idImageTask.pid');
    }

    public function withProgressIds()
    {
        $query = $this->tasks()->where([
            'idImageTask.status' => idImageTask::STATUS_CREATED,
        ]);

        $ids = $query->ids('idImageTask.id as id');

        return $ids;
    }


    public function process()
    {
        $this->canToken();

        return $this->withProgressBar(function (array $ids) {
            // создание коллекции задач
            $sender = $this->idImage->sender();
            $this->tasks()
                ->where(['id:IN' => $ids])
                ->each(function (idImageTask $task) use ($sender) {
                    // Увеличиваем счетчик после каждой попытки
                    /* $task->attempts();

                     // Проверяем что количество попыток меньше 5
                     if ($task->attemptsExceeded()) {
                         $task->setErrors('Превышен лимит попыток! Попробуйте отправить в ручную', idImageTask::STATUS_FAIL);
                         $task->save();

                         return false;
                     }*/

                    $sender->addTask($task);
                    $this->pt();

                    return true;
                });

            // Отправка коллекции задач
            $sender->received();

            return $this->total();
        });
    }


}

return 'idImageApiTaskReceivedProcessor';
