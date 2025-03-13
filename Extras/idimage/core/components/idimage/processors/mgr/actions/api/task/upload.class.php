<?php

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../../actions.class.php';
}

class idImageApiTaskUploadProcessor extends idImageActionsProcessor implements \IdImage\Interfaces\ActionProgressBar
{
    public function stepChunk()
    {
        return 10;
        #return $this->idimage()->limitUpload();
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
            'idImageTask.status' => idImageTask::STATUS_UPLOAD,
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
                    $sender->addTask($task);
                    $this->pt();

                    return true;
                });

            // Создаем превью для отправки
            $sender->collection()->each(function (\IdImage\Entites\TaskEntity $task) {
                $uploadPath = $this->idImage->makeThumbnail($task->getPicturePath());

                // Записываем путь с превью для отправки
                $task->setTmpPath($uploadPath);
            });


            // Отправляем файлы
            $sender->upload();

            return $this->total();
        });
    }


}

return 'idImageApiTaskUploadProcessor';
