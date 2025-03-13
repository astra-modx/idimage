<?php

use IdImage\Sender;

class idImageTaskUploadProcessor extends modProcessor
{
    public function process()
    {
        $id = (int)$this->getProperty('id');
        /* @var idImageTask $Task */
        if (!$Task = $this->modx->getObject('idImageTask', $id)) {
            throw new \IdImage\Exceptions\ExceptionJsonModx('Не удалось получить Task для id');
        }

        $idImage = $this->modx->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');
        $sender = $idImage->sender();

        $sender->addTask($Task);

        // Создаем превью для отправки
        $sender->collection()->each(function (\IdImage\Entites\TaskEntity $task) use ($idImage) {
            $uploadPath = $idImage->makeThumbnail($task->getPicturePath());
            // Записываем путь с превью для отправки
            $task->setTmpPath($uploadPath);
        });

        return $sender->upload() ? $this->success('Выполнено') : $this->failure('Ошибка');
    }
}

return 'idImageTaskUploadProcessor';
