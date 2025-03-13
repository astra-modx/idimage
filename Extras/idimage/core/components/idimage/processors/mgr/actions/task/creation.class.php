<?php

use IdImage\Exceptions\ExceptionJsonModx;

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../actions.class.php';
}

class idImageTaskCreationProcessor extends idImageActionsProcessor implements \IdImage\Interfaces\ActionProgressBar
{
    public function stepChunk()
    {
        return 1000;
    }

    public function withProgressIds()
    {
        $ids = $this->query()
            ->closes()
            ->leftJoin('idImageTask', 'Task', 'Task.pid=idImageClose.pid')
            ->where([
                'Task.id:IS' => null,
            ])->ids('idImageClose.id as id');

        return $ids;
    }


    public function process()
    {
        $this->canToken();

        return $this->withProgressBar(function (array $ids) {
            $closes = $this->query()->closes()->where(['id:IN' => $ids]);
            $closes->each(function (idImageClose $close) {
                /* @var idImageTask $task */
                if (!$task = $close->task()) {
                    $task = $this->modx->newObject('idImageTask');
                }


                $status = idImageTask::STATUS_CREATED;
                $task->set('pid', $close->get('pid'));
                $task->set('etag', (string)$close->get('hash'));
                $task->set('offer_id', (string)$close->get('pid'));

                if ($this->idImage->isSendFile()) {
                    $task->set('picture_path', $close->picturePath(false));
                    if ($task->isNew()) {
                        $status = idImageTask::STATUS_UPLOAD;
                        $task->set('image_available', false); // Метка установиться после загрузки файлов
                    } else {
                        // Если изменился etag, то устанавливаем метку на загрузку файла
                        if ($close->get('etag') !== $task->get('etag')) {
                            $status = idImageTask::STATUS_UPLOAD;
                            $task->set('image_available', false);
                        }
                    }
                } else {
                    // Отправка прямой ссылки на изображение
                    $task->set('picture', $close->link($this->idImage->siteUrl()));
                    $task->set('image_available', true);
                }

                $task->set('status', $status);


                if (!$task->save()) {
                    throw new ExceptionJsonModx('Не удалось сохранить задачу: '.$close->get('id'));
                }

                $this->pt();

                return true;
            });

            return $this->total();
        });
    }

}

return 'idImageTaskCreationProcessor';
