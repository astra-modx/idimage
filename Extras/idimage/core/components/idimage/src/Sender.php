<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 10.03.2025
 * Time: 18:52
 */

namespace IdImage;

use IdImage\Abstracts\SenderAbsract;
use IdImage\Entites\TaskEntity;
use IdImage\Exceptions\ExceptionJsonModx;
use idImageTask;

class Sender extends SenderAbsract
{
    public function poll(): bool
    {
        return $this->handle(
            self::ACTION_POLL,
            function (idImageTask $task, TaskEntity $entity) {
                if ($entity->isReceived()) {
                    $task->set('type', $entity->getType());
                    $task->set('hash', $entity->getEtag());


                 /*   // Создаем запись для векторов
                    if ($dataEmbedding = $entity->getEmbedding()) {
                        $embedding = $task->embedding();
                        $embedding->set('embedding', $dataEmbedding);
                        if (!$embedding->save()) {
                            throw new ExceptionJsonModx('Не удалось сохранить вектора для изображения taskId: '.$entity->getTaskId());
                        }
                    }*/


                    if ($dataSimilar = $entity->getSimilar()) {
                        $close = $task->close();

                        $min_scope = (int)$dataSimilar['min_scope'] ?? 0;
                        $total = (int)$dataSimilar['total'] ?? 0;
                        $similar = (is_array($dataSimilar['similar']) && !empty($dataSimilar['similar'])) ? $dataSimilar['similar'] : null;
                        $close->set('total', $total);
                        $close->set('similar', $similar);
                        $close->set('min_scope', $min_scope);

                        if (!$close->save()) {
                            throw new ExceptionJsonModx('Не удалось сохранить вектора для изображения taskId: '.$entity->getTaskId());
                        }
                    }

                }

                return $entity->getStatus();
            }
        );
    }

    public function received(): bool
    {
        return $this->handle(
            self::ACTION_RECEIVED,
            function (idImageTask $task, TaskEntity $entity) {
                $task->set('task_id', $entity->getTaskId());

                return $entity->getStatus();
            }
        );
    }

    public function upload(): bool
    {
        return $this->handle(
            self::ACTION_UPLOAD,
            function (idImageTask $task, TaskEntity $entity) {
                // Получаем только ссылку на изображение
                $picture = $entity->getPicture();


                $status = idImageTask::STATUS_CREATED;
                $image_available = false;
                if (empty($picture)) {
                    $task->setErrors('Не удалось загрузить изображение');
                    $status = idImageTask::STATUS_FAILED;
                } else {
                    $image_available = true;
                    $task->set('picture', $picture);
                }

                $task->set('image_available', $image_available);

                // Если удалось загрузить ставим статус на CREATED для добавления в очередь на обработку
                return $status;
            }
        );
    }


}
