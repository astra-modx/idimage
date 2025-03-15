<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 10.03.2025
 * Time: 18:52
 */

namespace IdImage;


use Closure;
use Exception;
use IdImage\Entites\TaskEntity;
use IdImage\Exceptions\ExceptionJsonModx;
use idImageTask;

class Sender
{
    private \idImage $idImage;

    const ACTION_POLL = 'poll';
    const ACTION_RECEIVED = 'received';
    const ACTION_UPLOAD = 'upload';
    private TaskCollection $collection;

    public function __construct(\idImage $idImage)
    {
        $this->idImage = $idImage;
        $this->create();
    }

    private function handle(string $action, \Closure $handle, \Closure $collection = null): bool
    {
        $api = $this->idImage->api()->task();

        $collection = $collection ?? $this->collection;
        switch (true) {
            case $action === self::ACTION_UPLOAD;
                $request = $api->upload($collection);
                break;
            case $action === self::ACTION_RECEIVED;
                $request = $api->received($collection);
                break;
            case $action === self::ACTION_POLL;
                $request = $api->poll($collection);
                break;
            default:
                throw new ExceptionJsonModx('Неизвестный тип запроса: '.$action);
        }

        // Отправляем данные в сервис
        $Response = $request->send();

        if ($action === self::ACTION_UPLOAD) {
            $this->unlinkTmpFiles();
        }

        if ($Response->isFail()) {
            // Критическая ошибка автоматчиески вышибает все задания
            $msg = $Response->getMessage();

            $this->collection->each(function (TaskEntity $entity) use ($msg) {
                $task = $this->getTask($entity->getOfferId());
                $task->setErrors($msg, null);
                $task->save();
            });
        } else {
            $items = $Response->json('items');
            // назначить ключем offer_id
            $items = array_combine(array_column($items, 'offer_id'), $items);
            // перебор коллекции задач

            $statuses = idImageTask::$statusMap;


            $this->collection->each(function (TaskEntity $entity) use ($handle, $items, $statuses) {
                // Получаем ответ
                $item = $items[$entity->getOfferId()] ?? null;

                if (!empty($item)) {
                    // Заполняем ответ в задачу
                    $entity->fromArray($item);

                    $task = $this->getTask($entity->getOfferId());

                    $status = $handle($task, $entity);

                    if (!in_array($status, $statuses)) {
                        throw new ExceptionJsonModx('Неизвестный статус: '.$status.' taskId: '.$task->get('id'));
                    }

                    $task->set('status', $status);
                    $task->save();
                } else {
                    throw new ExceptionJsonModx('Не удалось найти задачу с ID: '.$entity->getOfferId());
                }
            });
        }


        $this->collection->reset();

        return true;
    }

    public function unlinkTmpFiles()
    {
        $this->collection->each(function (TaskEntity $entity) {
            if ($path = $entity->getTmpPath()) {
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        });
    }

    public function getTask(int $id)
    {
        return $this->collection()->getTask($id);
    }

    public function collection()
    {
        return $this->collection;
    }

    public function poll(): bool
    {
        return $this->handle(
            self::ACTION_POLL,
            function (idImageTask $task, TaskEntity $entity) {
                if ($entity->isReceived()) {
                    $task->set('type', $entity->getType());
                    $task->set('hash', $entity->getEtag());

                    // Создаем запись для векторов
                    $embedding = $task->embedding();
                    $embedding->set('embedding', $entity->getEmbedding());
                    if (!$embedding->save()) {
                        throw new ExceptionJsonModx('Не удалось сохранить вектора для изображения taskId: '.$entity->getTaskId());
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

    public function create()
    {
        $this->collection = new TaskCollection();

        return $this;
    }

    public function addTask(idImageTask $task)
    {
        $this->collection->add($task);

        return $this;
    }

}
